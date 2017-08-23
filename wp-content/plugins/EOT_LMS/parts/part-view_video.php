<?php
    if(isset($_REQUEST['subscription_id']) && $_REQUEST['subscription_id'] > 0 && isset($_REQUEST['module_id']))
    {
        global $current_user;
        $user_id = $current_user->ID; // Wordpress user ID
        $org_id = get_org_from_user ($user_id); // Organization ID
        $portal_subdomain = get_post_meta ($org_id, 'org_subdomain', true); // Subdomain of the user
        $subscription_id = filter_var($_REQUEST['subscription_id'],FILTER_SANITIZE_NUMBER_INT); // The subscription ID
        $module_id = filter_var($_REQUEST['module_id'],FILTER_SANITIZE_NUMBER_INT); // The chosen Module ID
        $data = compact ("org_id");
        $modules_in_portal = getModules('0',$portal_subdomain,$data); // all the modules in this portal
        $found = false;
        $subLanguage = isset($_REQUEST['subLang']) ? filter_var($_REQUEST['subLang'],FILTER_SANITIZE_STRING) : null; // Video Language
        $resolution = isset($_REQUEST['res']) ? filter_var($_REQUEST['res'],FILTER_SANITIZE_STRING) : null; // Video resolution

        foreach( $modules_in_portal as $module )
        { 
            if( $module['ID'] == $module_id )
            {
                $video_title = $module['title'];
                $video = getVideo($video_title);
                // Check if there is video found
                if($video)
                {
                    ?>
                    <div class="breadcrumb">
                        <?= CRUMB_DASHBOARD ?>    
                        <?= CRUMB_SEPARATOR ?>   
                        <?= CRUMB_VIEW_LIBRARY ?>   
                        <?= CRUMB_SEPARATOR ?>   
                        <span class="current"><?= $video_title ?></span> 
                    </div>
                    <h1 class="article_page_title" class="video_title"><?= $video_title ?></h1>
                    <h3>Description</h3>
                    <p>
                        <?= isset($video->desc) ? $video->desc : "There is no description yet for this video." ?>
                    </p>
                    <b>Language:</b>  <?= $subLanguage ? '<a href="?part=view_video&module_id=' . $module_id . '&subscription_id=' .$subscription_id.'">English</a>' : 'English' ?> <?php echo $video->spanish ? ($subLanguage ? '/ Español' : '/ <a href="?part=view_video&module_id=' . $module_id . '&subscription_id=' .$subscription_id.'&subLang=es"> Español</a>') : ('')?> <br />
                    <br />
                    <div id='player123' style='width:665px;height:388px'>
                        <video id="my-video" class="video-js vjs-default-skin" controls preload="auto" width="665" height="388" poster="<?php echo bloginfo('template_directory'); ?>/images/eot_logo.png" data-setup='{"controls": true}'>
                            <?php 
                            // Check if we are showing by language or resolution.
                            if($subLanguage)
                            {
                            ?>
                                <source src="https://eot-output.s3.amazonaws.com/<?= $subLanguage ? $video->spanish : $video->shortname_medium ?>.mp4" type='video/mp4'>4
                                <p class="vjs-no-js">
                                To view this video please enable JavaScript, and consider upgrading to a web browser that
                                <a href="http://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a>
                                </p>
                            <?php

                            }
                            else
                            {
                                // Manage changing video resolution
                            ?>
                                <?php
                                if($resolution == null || $resolution == "high")
                                {
                                    $video_name = $video->shortname;
                                }
                                else if($resolution == "medium")
                                {
                                    $video_name = $video->shortname_medium;
                                }
                                else if($resolution == "low")
                                {
                                    $video_name = $video->shortname_low;
                                } 
                                ?>

                                <source src="rtmp://awscdn.expertonlinetraining.com/cfx/st/&mp4:<?= $video_name ?>.mp4" type='rtmp/mp4'>
                                <source src="http://awscdn2.expertonlinetraining.com/<?= $video_name ?>.mp4" type='video/mp4'>
<!--
                                <source src="https://eot-output.s3.amazonaws.com/<?= $video_name ?>.mp4" type='video/mp4'>4
-->
                                <p class="vjs-no-js">
                                To view this video please enable JavaScript, and consider upgrading to a web browser that
                                <a href="http://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a>
                                </p>        
                            <?php
                            }?>
                        </video>
                    </div>
                <br />
                <div id="msg">       
                    <h3>Loading Slowly? Click here.</h3>
                </div>
                <?php
                }
                else
                {
                    // Display an error page. There is no video found in this module.
                    echo '<h1 class="article_page_title" class="video_title">Video Not Found</h1>';
                    echo "We could not find the video for this module. Please contact the site administrator.";
                }
                ?>
                <div id="loading_message" style="margin-top: 10px;">
                    <div class="msgboxcontainer " >
                        <div class="msg-tl">
                            <div class="msg-tr"> 
                                <div class="msg-bl">
                                    <div class="msg-br">
                                        <div class='msgbox'>
                                            <h3>Change Visual Quality <img src="<?php echo bloginfo('template_directory'); ?>/images/target/info-sm.gif" title="If the video is loading slowly (the video will stop-and-go frequently) you can view a lower-resolution version that will take less time to download and should run smoother." class="tooltip" style="margin-bottom: -2px"<?=hover_text_attr("If the video is loading slowly (the video will stop-and-go frequently) you can view a lower-resolution version that will take less time to download and should run smoother.", true) ?>></h3>
                                            <ul class="notop">
                                            <?php 
                                                if( $resolution != "high" && $resolution != null)
                                                {
                                            ?>
                                                    <li>
                                                        View <a href="?part=view_video&module_id=<?= $module_id ?>&subscription_id=<?= $subscription_id?>&res=high">
                                                        <!--High-Resolution Version-->
                                                        Full HD Version</a> for high-speed connections and large screen viewing
                                                    </li>
                                            <?php
                                                }
                                                if($resolution != "medium")
                                                {
                                            ?>
                                                    <li>
                                                        View <a href="?part=view_video&module_id=<?= $module_id ?>&subscription_id=<?= $subscription_id?>&res=medium">
                                                      <!--Medium-Resolution Version-->
                                                        Medium-Resolution Version</a>
                                                    </li>
                                            <?php
                                                }
                                                if($resolution != "low")
                                                {
                                            ?>
                                                    <li>
                                                        View <a href="?part=view_video&module_id=<?= $module_id ?>&subscription_id=<?= $subscription_id?>&res=low">
                                                      <!--Low-Resolution Version-->
                                                        Low-Resolution Version</a> for slow Internet connections
                                                    </li>
                                            <?php
                                                }
                                            ?>
                                        </div>             
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </ul>
                <!-- Resources of the video (handouts, etc.) -->
                <div id="resources">
                <h3>Resources</h3>
                <?php

                    //Video Resources
                    $video_id = getVideoId($video_title);
                    if(!$video_id)
                    {
                        echo "Resources could not be loaded (video id not found)";
                    }
                    else
                    {
                        $resources = getResources($video_id);
                        if(empty($resources))
                        {
                            echo "No resources exist for this Video yet.";
                        }
                        else
                        {
                            echo "<ul>";
                            foreach ($resources as $resource) 
                            {
                                echo "<li><a href='" . $resource->url . "' target='_blank'>" . $resource->name . "</a></li>";
                            }
                            echo "</ul>";
                        }
                    }
                ?>
                </div>
                <script type="text/javascript">
                    jQuery(function($) 
                    {
                        $(document).ready(function()
                        {
                            $('#loading_message').hide();
                            $('#msg').click(function() 
                            {
                              $('#loading_message').slideDown(700);
                              $('#msg').hide();
                            });
                        });
                    })
                </script>
                <?php
                $found = true; // Module found in all modules in the portal.
                continue;
            }
        }
        if($found == false)
        {
            echo "Could not find this module in your portal.";
        }
        ?>

        <?php
    }
    else
    {
        echo "Sorry but you have an invalid subscription. Please contact the site administrator.";
    }
?>
