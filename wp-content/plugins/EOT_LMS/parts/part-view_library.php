<?php
  if(isset($_REQUEST['subscription_id']) && $_REQUEST['subscription_id'] > 0)
  {
    $subscription_id = filter_var($_REQUEST['subscription_id'],FILTER_SANITIZE_NUMBER_INT); // The subscription ID
    $subscription = getSubscriptions($subscription_id,0,1); // get the current subscription

    if($subscription)
    {
      $library_modules = getModulesByLibrary($subscription->library_id); // Get all the modules from the library of the current subscription.
      $library = getLibrary($subscription->library_id); // The library information base on the user current subscription
      if($library)
      {
        $library_name = $library->name; // The name of the library
        $description = $library->desc; // The library description
        $library_id = $library->ID; // the library ID
        ?>
        <div class="breadcrumb">
          <?= CRUMB_DASHBOARD ?>    
          <?= CRUMB_SEPARATOR ?>    
            <span class="current"><?= $library_name ?> (Library)</span> 
        </div>
        <h1 class="article_page_title"><?= $library_name ?></h1>
        <span>
          <strong>Welcome to <?= $library_name ?>!</strong> This is our most popular content library for camp counselors, parks & rec staff, and other youth leaders, including supervisors. 
          To watch videos, read video synopses, or download some handouts for your staff training manual, just click on one of the six category bars below.
          <br><br>
          <strong>Pre-Made Courses.</strong> Your annual subscription comes with four pre-made courses, called "New Staff," "Returning Staff," "Program Staff," and "Supervisory Staff." 
          <a href="/choosing-a-topic/">Click here</a> to see a listing of which topics are included in these pre-made courses.
          <br><br>
          <strong>Choosing Topics.</strong> To help you choose topics for a custom course or on-site workshop, we have categorized our videos by theme. Simply click on a theme below to view 
          the videos, quizzes, and handouts created by our amazing faculty. To view a table of recommended videos for different groups of staff, <a href="/choosing-a-topic/">click here</a>.
          <br><br>
          <strong>Custom Content.</strong> Looking for the custom content (videos, quizzes, or handouts) that you have uploaded? You can view it by clicking Manage Your Custom Content 
          in the <a href="/dashboard/?part=administration&subscription_id=<?= $subscription_id?>/" onclick="load('load_dashboard')">Administration page</a> of your Director Dashboard.<br><br>
        </span>
      <?php
        $modules = array(); // Array of Module objects
        $categories = array(); // Array of the name of the categories
        $categoryLibrary = getCategory2(); // All categories name
        if (isset($library_modules))
        {  
          foreach($library_modules as $key => $module)
          {
            /* 
             * This populates the modules array.
             */
            // if the module does not belong to any of our categories. Skip...
            if( !isset( $categoryLibrary[$module['category_id']] ) )
            {
              continue;
            }
            $category_name = $categoryLibrary[$module['category_id']]->name; // The category name of this module.
           /*
            * Include only the modules that are in the same library of the subscription.
            */
            if ($module['library_id'] == $library_id)
            {
                $new_module = new module( $module['ID'], $module['title'], $category_name); // Make a new module.
                array_push($modules, $new_module); // Add the new module to the modules array.
                /*
                 * Populate the category name array if the category name is not yet in the array.
                 */
                if(!in_array($category_name, $categories))
                {
                    array_push($categories, $category_name);
                }
                
            }
          }
        }
        usort($categories, "category_sort"); // Sort the categories based on the function below.
        /*  
         * Display the category and display its modules
         */
        //Grabs the video times and wordpress id of presenter in an array with video titles as keys
        $video_times = videoTimes();
        $handouts=array();
        $quizzes=array();
        $module_ids=  array_map(function($e) {
              return is_object($e) ? $e->id : $e['id'];
        }, $modules);//same as array_column but for objects.
        $module_ids_string = implode(',',$module_ids);
        $exams =  getQuizResourcesInModules($module_ids_string);
        $resources = getHandouts();
        // Process the exams.
        foreach($exams as $quiz)
        {
          if( isset( $quizzes[$quiz['module_id']] ) )
          {
              array_push($quizzes[$quiz['module_id']], array('id'=>$quiz['ID'],'name'=>$quiz['name']));
          }
          else
          {
            $quizzes[$quiz['module_id']]=array();
            array_push($quizzes[$quiz['module_id']], array('id'=>$quiz['ID'],'name'=>$quiz['name']));
          }
        }
        // Process the resources
        foreach($resources as $handout)
        {
          if(isset($handouts[$handout['module_id']]))
          {
            array_push($handouts[$handout['module_id']], array('id'=>$handout['ID'],'name'=>$handout['name'],'url'=>$handout['url']));
          }
          else
          {
            $handouts[$handout['module_id']]=array();
            array_push($handouts[$handout['module_id']], array('id'=>$handout['ID'],'name'=>$handout['name'],'url'=>$handout['url']));
          }
        }

        foreach($categories as $category)
        {
          $category_name = str_replace("_", " ", $category);// The category name. Replace Coma with spaces.
?>
          <div class="item_list" category_name="<?= $category_name ?>">
            <i style="float:right;" class="fa fa-arrow-down" aria-hidden="true"></i>
            <h3>
              <?= $category_name ?>
            </h3>
          </div>
          <div style="clear:both;"> 
          <div id="videolist" category_name="<?= $category_name ?>" style="display:none; width:100%">
            <div class="items_container">
<?php
              foreach( $modules as $key => $module )
              {
                if ( $module->category == $category )
                {
                  $title = $module->title; // The title of the module
?>
                  <div class="items">
                    <p><?= $title ?></p>
                    <ul>
                      <li>
                        <a href="./?part=view_video&module_id=<?= $module->id ?>&subscription_id=<?= $subscription_id ?>" onclick="load('load_video')">Watch Video</a> 
                        <?php
                          // make sure the video exists in the video table
                          if (isset($video_times[$title]))
                          {
                            //turn seconds into minutes.seconds
                            $duration_in_seconds = $video_times[$title]->secs;
                            $minutes = floor($duration_in_seconds / 60);
                            $seconds = $duration_in_seconds - (60 * $minutes);
                            echo " (" . $minutes . ":" . str_pad($seconds, 2, "0", STR_PAD_LEFT) . ") ";
                            //get presentor's name based on wordpress id 
                            $presenter = get_user_by('id', $video_times[$title]->presenter_id);
                            if ($presenter)
                            {
                              echo "by " . $presenter->data->display_name;
                            }

                            //Get the resources for the video
                            //$resources = getResources($video_times[$title]->id);
                            $description = ''; // Initialize variable for subscription.
                            $description = $video_times[$title]->desc;

                            if (!empty($description))
                            {
                              echo ' | <span ' . hover_text_attr(str_replace("'", "&lsquo;", $description),true) .'>Description</span>' ;
                            }

                            // check if we have resources to download. If so display them.
                            if(isset($handouts[$module->id]) && count($handouts[$module->id]) == 1)
                            {
                              echo " | <a href='" . $handouts[$module->id][0]['url'] . "' target='_blank'>Download Resource</a>";
                            }
                            else if (isset($handouts[$module->id]) && count($handouts[$module->id]) > 1)
                            {
                              echo " | <a href='./?part=view_video&module_id=" . $module->id . "&subscription_id=" . $subscription_id . "#resources'>View Resources</a>";
                            }
                            // Check if we have quiz or resources for the module. If so display them
                            if(isset($quizzes[$module->id]) && count($quizzes[$module->id]) == 1)
                            {
                              echo ' | <a  href=\'/dashboard?part=view_quiz&quiz_id=' . $quizzes[$module->id][0]['id'] . '&subscription_id=' . $_REQUEST["subscription_id"] . '\'>View quiz</a';
                            }
                            else if (isset($quizzes[$module->id]) && count($quizzes[$module->id]) > 1)
                            {
                              echo " | <a href='./?part=view_video&module_id=" . $module->id . "&subscription_id=" . $subscription_id . "#resources'>View Resources</a>";
                            }
                          }
                        ?>
                      </li>
                    </ul>
                  </div>
<?php
                }
              }
            // End of <div class="items_container">
            echo '</div>';
          // End of <div class="videolist">
          echo '</div>';
        }
?>
        <script type="text/javascript">
          $ = jQuery;
          $(".item_list").click(function()
          {
            // Change ARROW direction when a category is highlighted.
            if($(this).children('i').attr("class") == "fa fa-arrow-up")
            {
              $(this).children('i').attr("class", "fa fa-arrow-down");
            }
            else
            {
              $(this).children('i').attr("class", "fa fa-arrow-up");
            }
            // Toogle down the video list for this category.
            var category_name = $(this).attr('category_name');
             $('#videolist[category_name="'+category_name+'"]').slideToggle();
          });
         </script>          
<?php
      }
      else  
      {
        echo "Sorry but we coulnd't find your library. Please contact the site administrator.";
      }
    }
    else
    {
      echo "Sorry but you have an invalid subscription. Please contact the site administrator.";
    }
  }
  else
  {
    echo "Sorry but you are missing the subscription ID.";
  }

?>