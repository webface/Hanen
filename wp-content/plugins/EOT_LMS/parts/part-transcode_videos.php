<?php

if (current_user_can('sales_manager')) 
{
    $path = WP_PLUGIN_DIR . '/EOT_LMS/';
    require $path . 'includes/aws/aws-autoloader.php';
    require $path . 'includes/ElasticTranscoder.php';
    $admin_ajax_url = admin_url('admin-ajax.php');
?>
<div class="breadcrumb">
	<?= CRUMB_DASHBOARD ?>    
	<?= CRUMB_SEPARATOR ?>   
	  <span class="current">Admin Convert Uploaded Videos</span> 
</div>
<h1 class="article_page_title">Admin Convert Uploaded Videos</h1>
<script type="text/javascript">
var delay = 100;

function addCommas(nStr) {
	nStr += '';
	x = nStr.split('.');
	x1 = x[0];
	x2 = x.length>1?'.'+x[1]:'';
	var rgx = /(\d+)(\d{3})/;
	while (rgx.test(x1)) x1 = x1.replace(rgx,'$1,$2');
	return x1 + x2;
}

function refreshFileList() {
	$("#video_selection_form #table").html("Loading...");
	$.getJSON("<?=$admin_ajax_url?>?action=retrieve_s3_file_list&format=ajax", function(bucketContents) {
            console.log(bucketContents);
		$("#video_selection_form #table").html($("<table>"));
		var tableObj = $("#video_selection_form #table table");
		if (bucketContents["data"]) {
			firstRow = $("<tr>")
				.append($("<td>").append($("<input type='checkbox' id='file_select_all'>").click(function() {
					$("#video_selection_form .files_check").attr('checked',this.checked);
				})))
				.append($("<td>").html("Filename"))
				.append($("<td>").html("Size (bytes)"));
			tableObj.append(firstRow);
			var i = 0;
			for (name in bucketContents["data"]) {
				var fileName = bucketContents["data"][name]["name"];
				var fileSize = bucketContents["data"][name]["size"];
				var rgx = /^[a-z0-9]+_[0-9]{4}\.(0?[1-9]|1[012])\.([012]?[0-9]|3[01])\.[a-z0-9]+$/i;
				var match = rgx.test(fileName);
				var checkbox = match ? $("<input type='checkbox' class='files_check' name='files[]'>").attr("value",fileName).attr("id","file"+i) : "";
				//* Debugging: */ var checkbox = $("<input type='checkbox' class='files_check' name='files[]'>").attr("value",fileName).attr("id","file"+i) ;
				var row = $("<tr>").append($("<td>").append(checkbox))
					.append($("<td>").append($("<label>").attr("for","file"+i).html(fileName)))
					.append($("<td>").append($("<label>").attr("for","file"+i).html(addCommas(fileSize)+" bytes")));
				if (!match) row.addClass("nomatch").find("td label").first().append(" <strong>(Incorrect naming convention)</strong>");
				tableObj.append(row);
				i++;
			}
		} else reloadStatus("Unable to load S3 bucket files!");
	});
}

function reloadStatus(str) {
	$("#form_results_status").html(str).fadeIn(delay);
}

function appendToLog(str) {
	$("#form_results_results").append(str).fadeIn(delay);
}

function clearLog() {
	$("#form_results_results").html("");
}

$(function() {
	refreshFileList();
	$("#video_selection_form").submit(function(e) {
		e.preventDefault();
		e.stopPropagation();

		var files = [];
		$('.files_check:checked').each(function(i){
			files[i] = $(this).val();
		});
		console.log(files);
		clearLog();
		var file_count = files.length;
		if (file_count > 0) {
			reloadStatus("0% Preparing...");
			var convert_file;
			(convert_file = function(file_num) {
				var file_name = files[file_num];
				var percentage = Math.round(100*file_num/file_count);
				reloadStatus(percentage+"% Sending Job "+file_num+": <em>"+file_name+"</em> to server");
				$.post("<?=$admin_ajax_url?>?action=process_s3_video&format=ajax", $.param({name:file_name}), function(data, textStatus, jqXHR) {
					console.log(data);
					var log = data["log"];
					appendToLog(log);
					if (file_num+1<file_count) convert_file(file_num+1);
					else reloadStatus("100% Done!");
				}, "json");
			})(0);
		} else reloadStatus("Nothing selected!");
	});
	$("#video_selection_reload").click(function(e) {
		e.preventDefault();
		e.stopPropagation();
		refreshFileList();	
	});
});
</script>
<style type="text/css">
	#video_selection_form table { width: 100%; margin-bottom: 10px; }
	#video_selection_form tr:nth-child(1) { background-color: #AAA; font-weight: bold; }
	#video_selection_form tr:nth-child(even) { background-color: #DDD; }
	#video_selection_form td { padding: 2px 5px; }
	#video_selection_form tr td:nth-child(1) { width: 5%; }
	#video_selection_form tr td:nth-child(3) { text-align: right; }
	#video_selection_form tr.nomatch { background-color: #FF9E9E; }
	#form_results_status { display:none; width: 90%; padding: 10px 5%; background: #FFCFCF; font-weight: bold; margin-top: 10px; }
	#form_results_results { display:none; width: 90%; padding: 10px 5%; background: #FFE3E3; }

</style>
<p>Below are a list of videos uploaded to the "eot-input" bucket. Check the ones you want 
	to convert and add to the EOT database and server. Files must be in the proper naming 
	convention in order to select them. <a id="video_selection_reload" href="javascript:void(0);">Reload list</a>
</p>
<form id="video_selection_form">
	<div id="table"></div>
	<input type="submit" value="Convert!" />
</form>
<div id="form_results_status"></div>
<div id="form_results_results"></div>

</script>
    <?php

} 
else 
{
    echo "Youre not authorized to view this page";
}
?>

<?php
// Instantiate the S3 client with your AWS credentials
//    $s3Client = new Aws\S3\S3Client(array(
//        'version' => 'latest',
//        'region' => AWS_REGION,
//        'credentials' => array(
//            'key' => AWS_ACCESS_KEY_ID,
//            'secret' => AWS_SECRET_ACCESS_KEY,
//        )
//    ));
//
//    try {
//        $objects = $s3Client->getIterator('ListObjects', array(
//            'Bucket' => 'eot-video-input',
//            'region' => AWS_REGION
//        ));
//
//        foreach ($objects as $object) {
//            //echo $object['Key'] . "<br>";
//        }
//    } catch (S3Exception $e) {
//        echo $e->getMessage() . "<br>";
//    }
    ?>