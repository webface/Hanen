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
          <strong><?= $library_name ?></strong> is our most popular content library for youth leaders and supervisors. Click on one of the six category bars below to get started.
          <br><br>
          <a href="<?=get_home_url()?>/premade-courses/" onclick="load('load')"><button type="button" class="btn btn-primary">Pre-Made Courses</button></a>
          <a href="<?=get_home_url()?>/choosing-a-topic/" onclick="load('load')"><button type="button" class="btn btn-primary">Module Choices</button></a>
          <a href="<?=get_home_url()?>/dashboard/?part=administration&subscription_id=<?=$subscription_id?>" onclick="load('load')"><button type="button" class="btn btn-primary">Custom Content</button></a>
          <br>
        </span>
      <?php
        $modules = array(); // Array of Module objects
        $categoriesInLibrary = getLibraryCategory(0, $library_id); // All categories in this library
        if (isset($library_modules))
        {  
          foreach($library_modules as $key => $module)
          {
            /* 
             * This populates the modules array.
             */
            // if the module does not belong to any of our categories. Skip...
            if( !isset( $categoriesInLibrary[$module['category_id']] ) )
            {
              $new_module = new module( $module['ID'], $module['title'], 'Custom'); // Make a new module in the 'Custom' category
              array_push($modules, $new_module); // Add the new module to the modules array.
            }
            else
            {
              $category_name = $categoriesInLibrary[$module['category_id']]->name; // The category name of this module.
              $new_module = new module( $module['ID'], $module['title'], $category_name); // Make a new module object.
              array_push($modules, $new_module); // Add the new module to the modules array.
            }
          }
        }
        /*  
         * Display the category and display its modules
         */
        //Grabs the video times and wordpress id of presenter in an array with video titles as keys
        $video_times = videoTimes();
        $handouts = array();
        $quizzes = array();
        $module_ids = array_map(function($e) 
          {
            return is_object($e) ? $e->id : $e['id'];
          }, $modules);//same as array_column but for objects.
        $module_ids_string = implode(',',$module_ids);
        $exams = getQuizResourcesInModules($module_ids_string);
        $resources = getHandouts($module_ids_string);

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

        foreach($categoriesInLibrary as $category)
        {
          $category_name = $category->name; 
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
                if ( $module->category == $category_name )
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
/*
                            //get presentor's name based on wordpress id 
                            $presenter = get_user_by('id', $video_times[$title]->presenter_id);
                            if ($presenter)
                            {
                              echo "by " . $presenter->data->display_name;
                            }
*/
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
                              echo ' | <a  href=\'/dashboard?part=view_core_quiz&quiz_id=' . $quizzes[$module->id][0]['id'] . '&subscription_id=' . $_REQUEST["subscription_id"] . '\'>View quiz</a';
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