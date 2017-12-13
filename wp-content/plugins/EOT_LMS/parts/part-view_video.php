<div class="breadcrumb">
    <?= CRUMB_DASHBOARD ?>    
    <?= CRUMB_SEPARATOR ?>   
    <?= CRUMB_VIEW_LIBRARY ?>   
</div>
<?php
    // verify this user has access to this portal/subscription/page/view
    $true_subscription = verifyUserAccess(); 

    if(isset($_REQUEST['subscription_id']) && $_REQUEST['subscription_id'] > 0 && isset($_REQUEST['module_id']))
    {
        global $current_user;
        $user_id = $current_user->ID; // Wordpress user ID
        $org_id = get_org_from_user ($user_id); // Organization ID
        $subscription_id = filter_var($_REQUEST['subscription_id'],FILTER_SANITIZE_NUMBER_INT); // The subscription ID
        if(isset($true_subscription['status']) && $true_subscription['status'])
        {
            $module_id = filter_var($_REQUEST['module_id'],FILTER_SANITIZE_NUMBER_INT); // The chosen Module ID
            $subLanguage = isset($_REQUEST['subLang']) ? filter_var($_REQUEST['subLang'],FILTER_SANITIZE_STRING) : null; // Video Language
            $resolution = isset($_REQUEST['res']) ? filter_var($_REQUEST['res'],FILTER_SANITIZE_STRING) : null; // Video resolution
            $module_data = getModule($module_id);
            $subscription = getSubscriptions($subscription_id);

            // make sure we got a module AND that user has access to this module.
            if( isset($module_data) && $module_data['library_id'] == $subscription->library_id)
            {
                $module_resources = getResourcesInModule($module_id); // All resources in the module.
                $resources = array();// Video resources. Populated in the next foreach.
                foreach ($module_resources as $module) 
                {
                    if($module['type'] == "video")
                    {
?>
                        <h1 class="article_page_title" class="video_title"><?= $module['name'] ?></h1>
                        <h3><?= __('Description', 'EOT_LMS')?></h3>
                        <p>
                            <?= isset($module['desc']) ? $module['desc'] : "There is no description yet for this video." ?>
                        </p>
                        <b><?= __('Language:', 'EOT_LMS')?></b>  <?= $subLanguage ? '<a href="?part=view_video&module_id=' . $module_id . '&subscription_id=' .$subscription_id.'">English</a>' : 'English' ?> 

<?php 
                        echo ($subLanguage) ? '/ Español' : '/ <a href="?part=view_video&module_id=' . $module_id . '&subscription_id=' .$subscription_id.'&subLang=es"> Español</a>';
                        $upload_dir = wp_upload_dir()["baseurl"]; // URL to the upload directory.
?> 
                        <br />
                        <br />
                        <div id='player_<?= $module['ID']; ?>' style='width:665px;height:388px'>
                            <video id="my-video" user-id="<?=$user_id?>" class="video-js vjs-default-skin" controls preload="auto" width="665" height="388" poster="<?php echo bloginfo('template_directory'); ?>/images/eot_logo.png" data-setup='{"controls": true}'>
                                <track kind="captions" src="<?= $upload_dir ?>/subtitles/<?= $module['video_name'] ?>_en.vtt" srclang="en" label="English" default>
                                <track kind="captions" src="<?= $upload_dir ?>/subtitles/<?= $module['video_name'] ?>_es.vtt" srclang="es" label="Spanish">
                                <track kind="captions" src="<?= $upload_dir ?>/subtitles/<?= $module['video_name'] ?>_ma.vtt" srclang="man" label="Mandarin">

<?php 
                                // Check if we are showing by language or resolution.
                                if($subLanguage)
                                {
?>
                                    <source src="https://eot-output.s3.amazonaws.com/<?= $subLanguage ? $module['spanish'] : $module['shortname_medium'] ?>.mp4" type='video/mp4'>4
                                    <p class="vjs-no-js">
                                    <?= __('To view this video please enable JavaScript, and consider upgrading to a web browser that ', 'EOT_LMS')?>
                                    <a href="http://videojs.com/html5-video-support/" target="_blank"><?= __('supports HTML5 video', 'EOT_LMS')?></a>
                                    </p>
<?php
                                }
                                else
                                {
                                    // Manage changing video resolution
                                    if($resolution == null || $resolution == "high")
                                    {
                                        $video_name = $module['shortname'];
                                    }
                                    else if($resolution == "medium")
                                    {
                                        $video_name = $module['shortname_medium'];
                                    }
                                    else if($resolution == "low")
                                    {
                                        $video_name = $module['shortname_low'];
                                    } 
?>

                                    <source src="https://eot-output.s3.amazonaws.com/<?= $video_name ?>.mp4" type='video/mp4'>
                                    <p class="vjs-no-js">
                                    <?= __('To view this video please enable JavaScript, and consider upgrading to a web browser that ', 'EOT_LMS')?>
                                    <a href="http://videojs.com/html5-video-support/" target="_blank"><?= __('supports HTML5 video', 'EOT_LMS')?></a>
                                    </p>        
<?php
                                }
?>
                                
                            </video>
                        </div>
<?php
                    }
                    // Add docs into the resources table.
                    else if($module['type'] == "doc")
                    {
                        array_push($resources, $module);
                    }
                } // end foreach
?>
                <br />
                <div id="msg">       
                    <h3><?= __('Loading Slowly? Click here.', 'EOT_LMS')?></h3>
                </div>
                <div id="loading_message" style="margin-top: 10px;">
                    <div class="msgboxcontainer " >
                        <div class="msg-tl">
                            <div class="msg-tr"> 
                                <div class="msg-bl">
                                    <div class="msg-br">
                                        <div class='msgbox'>
                                            <h3><?= __('Change Visual Quality', 'EOT_LMS')?><img src="<?php echo bloginfo('template_directory'); ?>/images/target/info-sm.gif" title="If the video is loading slowly (the video will stop-and-go frequently) you can view a lower-resolution version that will take less time to download and should run smoother." class="tooltip" style="margin-bottom: -2px"<?=hover_text_attr("If the video is loading slowly (the video will stop-and-go frequently) you can view a lower-resolution version that will take less time to download and should run smoother.", true) ?>></h3>
                                            <ul class="notop">
<?php 
                                                if( $resolution != "high" && $resolution != null)
                                                {
?>
                                                    <li>
                                                        <?= __('View ', 'EOT_LMS')?><a href="?part=view_video&module_id=<?= $module_id ?>&subscription_id=<?= $subscription_id?>&res=high">
                                                        <!--High-Resolution Version-->
                                                        <?= __('Full HD Version', 'EOT_LMS')?></a><?= __(' for high-speed connections and large screen viewing', 'EOT_LMS')?>
                                                    </li>
<?php
                                                }
                                                if($resolution != "medium")
                                                {
?>
                                                    <li>
                                                        <?= __('View ', 'EOT_LMS')?> <a href="?part=view_video&module_id=<?= $module_id ?>&subscription_id=<?= $subscription_id?>&res=medium">
                                                      <!--Medium-Resolution Version-->
                                                        <?= __('Medium-Resolution Version', 'EOT_LMS')?></a>
                                                    </li>
<?php
                                                }
                                                if($resolution != "low")
                                                {
?>
                                                    <li>
                                                        <?= __('View ', 'EOT_LMS')?><a href="?part=view_video&module_id=<?= $module_id ?>&subscription_id=<?= $subscription_id?>&res=low">
                                                      <!--Low-Resolution Version-->
                                                        <?= __('Low-Resolution Version', 'EOT_LMS')?></a><?= __(' for slow Internet connections', 'EOT_LMS')?>
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
<?php
                //Video Resources
                if(!empty($resources))
                {
?>
                    <h3><?= __('Resources', 'EOT_LMS')?></h3>
                    <ul>
<?php
                        foreach ($resources as $resource) 
                        {
                            echo "<li><a href='" . $resource['url'] . "' target='_blank'>" . $resource['name'] . "</a></li>";
                        }
?>
                    </ul>
<?php
                }
                else
                {
                    echo __("No resources exist for this module yet.", 'EOT_LMS');
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
            }
            else
            {
                echo __("Could not find the module.", 'EOT_LMS');
            }
        }
        else
        {
            echo __("subscription ID does not belong to you", 'EOT_LMS');
        }
    }
    else
    {
        echo __("Sorry but you have an invalid request. Please contact the site administrator.", 'EOT_LMS');
    }
?>
