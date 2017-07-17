<div class="breadcrumb">
  <?= CRUMB_DASHBOARD ?>    
  <?= CRUMB_SEPARATOR ?>     
  <span class="current">Questionnaire Dashboard</span>     
</div>
<h1 class="article_page_title">Questionnaire Dashboard</h1>
<?php
	// check user's role for permission to access this display
	if (!current_user_can('is_sales_rep') && !current_user_can('is_sales_manager'))
	{
		wp_die('You do not have access to this display.');
	}

	$admin_ajax_url = admin_url('admin-ajax.php');
	//library ID
	$library_id = filter_var($_REQUEST['library_id'],FILTER_SANITIZE_NUMBER_INT);

	//getQuestions() will get all the questions and answers
	$questions = getQuestions($library_id);

	echo "<a class = 'add' href='" . $admin_ajax_url . "?action=getCourseForm&org_id=0&form_name=add_question_questionnaire&library_id=" . $library_id . "' rel='facebox'>add new question</a>";

	//counter for question number
	$question_number = 1;

	//this loops on all the questions to print them to the screen
	foreach($questions as $question)
	{

		//prints the question with the needed data
		echo
		"<div data-number = " .
		$question_number .
		">Q" .
		$question_number .
		": <span class = 'question'>" .
		$question -> question .
		"</span><input type = 'text' class = 'question-editable' value = '" .
		$question -> question .
		"' hidden><a class = 'edit' href='#'>edit</a>/<a class = 'delete' href='" . $admin_ajax_url . "?action=getCourseForm&org_id=0&form_name=delete_question_questionnaire&question_number=" . $question_number . "&library_id=" . $library_id . "' rel='facebox'>delete question</a><br></div>";

		//decode the answers to an array
		$answers = json_decode($question -> answer, true);

		//question number to be grabbed from jQuery
		echo '<div data-number = ' . $question_number . '>';

		//this loop prints the answers one by one
		for($i = 1; $i <= count($answers); $i++)
		{

			//prints the answer with the needed data
			echo '<div><span class = "answer-number">' .
			$i .
			') </span><span class = "answer">' .
			$answers[$i] .
			"</span><input type = 'text' class = 'answer-editable' value = '" .
			$answers[$i] .
			"' hidden></input><a class = 'edit' href='#'>edit</a>/<a class = 'conditions' href='" . $admin_ajax_url . "?action=getCourseForm&org_id=0&form_name=edit_questionnaire_conditions&question_number=" . $question_number . "&answer_number=" . $i . "&library_id=" . $library_id . "' rel='facebox'>conditions</a><br></div>";

		}

		echo '</div><br>';

		//counter for question number
		$question_number++;
	}
?>

<script language="javascript" type="text/javascript" src="<?= get_template_directory_uri() . '/js/jquery.min.js'?>"></script>
<script language="javascript" type="text/javascript" src="<?= get_template_directory_uri() . '/js/jquery-ui.min.js'?>"></script>
<link type="text/css" href="<?= get_template_directory_uri() . "/css/jqueryui/jquery-ui-1.8.22.custom.css"?>" rel="stylesheet" />
<link type="text/css" href="<?= get_template_directory_uri() . "/css/jqueryui/style.css"?>" rel="stylesheet" />
<script language="javascript" type="text/javascript" src="<?= get_template_directory_uri() . '/js/jquery.dataTables.js'?>"></script>
<script language="javascript" type="text/javascript" src="<?= get_template_directory_uri() . '/js/jquery.eotprogressbar.js'?>"></script>
<script language="javascript" type="text/javascript" src="<?= get_template_directory_uri() . '/js/jquery.eotdatatables.js'?>"></script>
<script language="javascript" type="text/javascript" src="<?= get_template_directory_uri() . '/js/target2.js'?>"></script>
<link href="<?= get_template_directory_uri() . "/css/facebox.css"?>" media="screen" rel="stylesheet" type="text/css"/>
<script src="<?= get_template_directory_uri() . '/js/facebox.js'?>" type="text/javascript"></script>
<script src="<?= get_template_directory_uri() . '/js/flowplayer.min.js'?>"></script>

<script>
	//initializing facebox
	$('a[rel*=facebox]').facebox();

	$ = jQuery;

	//event handler for the edit button (both question and answer)
	$('a.edit').click(function(){
		//hide the edit button
		$(this).hide();
		//hide the text and show the input field
		$(this).parent().find('.answer, .answer-editable, .question, .question-editable').toggle();
		//focus on the input field
		$(this).parent().find('.answer-editable, .question-editable').focus();
	});

	//event handler for when input goes out of focus (an answer has been edited)
	$('.answer-editable').focusout(function(){

		//new answer
		var new_answer = $(this).val();
		//text field + input + edit button (for toggling later)
		var text_field = $(this).parent().find('.answer, .answer-editable, .edit');

		//put the new answer in text field
		$(this).parent().find('.answer').text(new_answer);

		//gather all answers for the current question
		var all_answers_array = $(this).parent().parent().find('.answer').map(function() { return $(this).text(); }).get();
		var all_answers_object = {};

		//This loop will make an object so that all answers can be converted to JSON
		for(var i = 1; i<=all_answers_array.length;i++)
		{
			all_answers_object[i.toString()] = all_answers_array[i-1];
		}

		//this will convert the object to JSON string
		var all_answers = JSON.stringify(all_answers_object);
		//grab question number
		var question_number = $(this).parent().parent().attr('data-number');

		//set up ajax call parameters
		var data = { action: 'updateAnswers', new_answer: all_answers, question_number: question_number};
		var url =  ajax_object.ajax_url;

		//ajax call to update the answer
		$.ajax({
			type: "POST",
			url: url,
			dataType: 'json',
			data: data,
			success:
			function(data)
			{
				//toggle the fields back once the update has been done
				text_field.toggle();
			}
		});

	});

	//even handler for when the input goes out of focus (a question has been edited)
	$('.question-editable').focusout(function(){

		//new question
		var new_question = $(this).val();
		//text field + input + edit button (for toggling later)
		var text_field = $(this).parent().find('.question, .question-editable, .edit');

		//put the question in the text field
		$(this).parent().find('.question').text(new_question);

		//grab the question number
		var question_number = $(this).parent().attr('data-number');

		//set up the ajax call parameters
		var data = { action: 'updateQuestion', new_question: new_question, question_number: question_number};
		var url =  ajax_object.ajax_url;

		//ajax call to update the question
		$.ajax({
			type: "POST",
			url: url,
			dataType: 'json',
			data: data,
			success:
			function(data)
			{
				//toggle the fields back once the update has been done
				text_field.toggle();
			}
		});

	});
</script>