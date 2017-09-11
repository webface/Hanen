<?php
/*
 * Used with facebox.js to load pop up forms
 */
switch ($_REQUEST['form_name']) 
{
    case 'update_title':
        $title = preg_replace("/[^a-zA-Z0-9'\"?_\. !&-]+/","",sanitize_text_field($_REQUEST['title']));
        ob_start();
?>
        <div class="title" style="width:320px">
            <div class="title_h2">Type in or update your question</div>
        </div>
        <div class="middle">
            <span class="errorbox"></span>
            <form id= "add_title" frm_name="update_title" frm_action="update_title" rel="submit_form" hasError=0> 
               
                        <div class=" bs form_group">
                            <input class="bs form-control" type="text" name="title" value="<?=$title;?>" /> 
                            <?php wp_nonce_field('update-title'); ?>
                        </div> 
                </table> 
            </form>
        </div>      
        <div class="popup_footer">
            <div class="buttons">
                <a onclick="jQuery(document).trigger('close.facebox');" class="negative">
                    <img src="<?php bloginfo('stylesheet_directory'); ?>/images/cross.png" alt="Close"/>
                    Cancel
                </a>
                <a active = '0' acton = "update_title" rel = "submit_button" class="positive">
                    <img src="<?php bloginfo('stylesheet_directory'); ?>/images/tick.png" alt="Save"/> 
                    Submit
                </a>
            </div>
        </div>
<?php
        $html = ob_get_clean();
        echo $html;
        exit();
        break;
    case 'add_title':
        $type = filter_var($_REQUEST['type'],FILTER_SANITIZE_STRING);
        ob_start();
?>
        <div class="title" style="width:320px">
            <div class="title_h2">Type in or update your question</div>
        </div>
        <div class="middle">
            <span class="errorbox"></span>
            <form id= "add_title" frm_name="add_title" frm_action="add_title" rel="submit_form" hasError=0> 
               
                        <div class=" bs form_group">
                            <input class="bs form-control" type="text" name="title" value="" /> 
                             <input type="hidden" name="type" value="<?= $type ?>" />
                            <?php wp_nonce_field('add-title_' . $type); ?>
                        </div> 
                </table> 
            </form>
        </div>      
        <div class="popup_footer">
            <div class="buttons">
                <a onclick="jQuery(document).trigger('close.facebox');" class="negative">
                    <img src="<?php bloginfo('stylesheet_directory'); ?>/images/cross.png" alt="Close"/>
                    Cancel
                </a>
                <a active = '0' acton = "add_title" rel = "submit_button" class="positive">
                    <img src="<?php bloginfo('stylesheet_directory'); ?>/images/tick.png" alt="Save"/> 
                    Submit
                </a>
            </div>
        </div>
<?php
        $html = ob_get_clean();
        echo $html;
        exit();
        break;
    case 'delete_quiz':
        $quiz_id = filter_var($_REQUEST['quiz_id'],FILTER_SANITIZE_NUMBER_INT);
        $subscription_id = filter_var($_REQUEST['subscription_id'],FILTER_SANITIZE_NUMBER_INT);
        ob_start();
        ?>
        <div class="title">
            <div class="title_h2">Delete This Quiz?</div>
        </div>
        <div class="middle">
            <form id= "delete_quiz" frm_name="delete_quiz" frm_action="delete_quiz" rel="submit_form" hasError=0> 
                <table padding=0 class="form"> 
                    <tr>
                        <td class="value">
                            <p>If there are users assigned to this quiz, all records of the results will be lost</p>
                            <input type="hidden" name="subscription_id" value="<?= $subscription_id ?>" /> 
                            <input type="hidden" name="quiz_id" value="<?= $quiz_id ?>" /> 
                            <?php wp_nonce_field('delete-quiz_' . $quiz_id); ?>
                        </td> 
                    </tr> 
                </table> 
            </form>
        </div>      
        <div class="popup_footer">
            <div class="buttons">
                <a onclick="jQuery(document).trigger('close.facebox');" class="negative">
                    <img src="<?php bloginfo('stylesheet_directory'); ?>/images/cross.png" alt="Close"/>
                    Cancel
                </a>
                <a active = '0' acton = "delete_quiz" rel = "submit_button" class="positive">
                    <img src="<?php bloginfo('stylesheet_directory'); ?>/images/tick.png" alt="Save"/> 
                    Yes
                </a>
            </div>
        </div>
<?php
        $html = ob_get_clean();
        echo $html;
        exit();
        break;
    case 'delete_question':
        $question_id = filter_var($_REQUEST['question_id'],FILTER_SANITIZE_NUMBER_INT);
        $subscription_id = filter_var($_REQUEST['subscription_id'],FILTER_SANITIZE_NUMBER_INT);
        ob_start();
?>
        <div class="title">
            <div class="title_h2">Delete This Question and its answers?</div>
        </div>
        <div class="middle">
            <form id= "delete_quiz" frm_name="delete_question" frm_action="delete_question" rel="submit_form" hasError=0> 
                <table padding=0 class="form"> 
                    <tr>
                        <td class="value">
                            <p>If there are users assigned to this quiz, all records of the results will be lost</p>
                            <input type="hidden" name="subscription_id" value="<?= $subscription_id ?>" /> 
                            <input type="hidden" name="question_id" value="<?= $question_id ?>" /> 

                            <?php wp_nonce_field('delete-question_' . $question_id); ?>
                        </td> 
                    </tr> 
                </table> 
            </form>
        </div>      
        <div class="popup_footer">
            <div class="buttons">
                <a onclick="jQuery(document).trigger('close.facebox');" class="negative">
                    <img src="<?php bloginfo('stylesheet_directory'); ?>/images/cross.png" alt="Close"/>
                    Cancel
                </a>
                <a active = '0' acton = "delete_question" rel = "submit_button" class="positive">
                    <img src="<?php bloginfo('stylesheet_directory'); ?>/images/tick.png" alt="Save"/> 
                    Yes
                </a>
            </div>
        </div>
<?php
        $html = ob_get_clean();
        echo $html;
        exit();
        break;
    default:
        break;
}
?>