<?php
  if(isset($_REQUEST['id']) && $_REQUEST['id'] != "" && current_user_can("is_sales_manager") || current_user_can("is_sales_rep"))
  {
      // Variable declaration
      $user_id = filter_var($_REQUEST['id'],FILTER_SANITIZE_NUMBER_INT); // Wordpress User ID
      $org_id = get_org_from_user ($user_id); // Organization ID
      $stripe_id = get_post_meta ($org_id, 'stripe_id', true); // stripe id of the user
      $first_name = get_user_meta ($user_id, 'first_name', true);
      $last_name = get_user_meta ($user_id, 'last_name', true);   
      $name = $first_name . " " . $last_name; // User's First and Last name in wordpress
      $org_name = get_the_title ($org_id); // The org name
  ?>
  	<div class="breadcrumb">
  		<?= CRUMB_DASHBOARD ?>    
  		<?= CRUMB_SEPARATOR ?>
          <?= CRUMB_CUSTOMFIELDS ?>  
          <?= CRUMB_SEPARATOR ?>
      	<span class="current">Edit Custom Fields</span>     
  	</div>
    <img style="margin-right:60em;" class="loader" src="<?= get_template_directory_uri() . "/images/loading.gif"?>" hidden>
    <div class="main-content" data-org="<?= $org_id ?>" data-user="<?= $user_id ?>">
    	<h1 class="article_page_title">Organization Settings for <b><?= $name ?></b></h1>
          <table class="data small">
            <tbody>
              <tr>
                <td class="label left">post_title</td>
                <td>
                 <input type="text" name="portal-name" id="portal-name" data-subdomain="<?= $org_name ?>" data-type="post" class="portal-fields" size="35" value="<?= $org_name ?>">
              </td>
              <tr>
                <td class="label left">stripe_id</td>
                <td>
                 <input type="text" name="stripe-id" id="stripe-id" data-type="post_meta" class="portal-fields" size="35" value="<?= $stripe_id ?>">
                </td>
              </tr>
            </tbody>
          </table>
<?php
      if(current_user_can("is_sales_manager"))
      {
        if(!$org_id)
        {
          echo '<b>User is not in any organization.<b>';
        }
        $subscriptions = getSubscriptions(0,0,0,$org_id);
        
        if(!is_array($subscriptions) && !empty($subscriptions) )
        {
          $subscriptions_info[0] = $subscriptions;
        }
        else 
        {
          $subscriptions_info = $subscriptions;
        }

        if (!empty($subscriptions_info))
        {

          // loop through the subscriptions and display info
          foreach ($subscriptions_info as $subscription_info)
          {
?>

      <h1 class="article_page_title">Subscription Settings for <b><?= $name ?></b></h1>
          <table class="data small" data-id="<?= $subscription_info->ID ?>">
            <tbody>
                <tr>
                  <td class="label left">library_id</td>
                  <td>
                   <input type="text" name="library-id" id="library-id" class="sub-fields" size="35" value="<?= $subscription_info->library_id ?>">
                  </td>
                </tr>
                <tr>
                  <td class="label left">start_date</td>
                  <td>
                   <input type="text" name="start-date" id="start-date" class="sub-fields date-picker" size="35" value="<?= $subscription_info->start_date ?>">
                  </td>
                </tr>
                <tr>
                  <td class="label left">end_date</td>
                  <td>
                   <input type="text" name="end-date" id="end-date" class="sub-fields date-picker" size="35" value="<?= $subscription_info->end_date ?>">
                  </td>
                </tr>
                <tr>
                  <td class="label left">setup</td>
                  <td>
                   <input type="text" name="setup" id="setup" class="sub-fields" size="35" value="<?= $subscription_info->setup ?>">
                  </td>
                </tr>
            </tbody>
          </table>

<?php
          }
        }
      }
?>
    </div>
    <script>
      $ = jQuery;

      $('.date-picker').datepicker({ dateFormat: 'yy-mm-dd' });

      $(".portal-fields").change(function(){

        var meta_value = $(this).val();                           //meta_value
        var type = $(this).attr("data-type");

        if(meta_value)
        {
         
          var meta_key = $(this).parent().prev().text();          //meta_key
          var user_id = $(".main-content").attr("data-user");     //user_id
          var org_id = $(".main-content").attr("data-org");       //org_id

          //set up the ajax call parameters
          var data = { action: 'updateCustomSettings', meta_key: meta_key, meta_value: meta_value, user_id: user_id, org_id: org_id, type: type};
          var url =  ajax_object.ajax_url;

          //save scroll position
          var tempScrollTop = $(window).scrollTop();

          //show loader gif and hide the content
          $('.main-content, .loader').toggle();

          //ajax call to update the fields
          $.ajax({
            type: "POST",
            url: url,
            dataType: 'json',
            data: data,
            success:
            function(data)
            {
              $('.main-content, .loader').toggle(); //toggle back the content
              $(window).scrollTop(tempScrollTop);   //put screen back into original scroll position
            }
          });

        }

      });

      $(".user-fields").change(function(){

        var meta_value = $(this).val();                           //meta_value

        if(meta_value)
        {
         
          var meta_key = $(this).parent().prev().text();          //meta_key
          var user_id = $(".main-content").attr("data-user");     //user_id
          var org_id = $(".main-content").attr("data-org");       //org_id

          //set up the ajax call parameters
          var data = { action: 'updateCustomSettings', meta_key: meta_key, meta_value: meta_value, user_id: user_id, org_id: org_id, type: "user"};
          var url =  ajax_object.ajax_url;

          //save scroll position
          var tempScrollTop = $(window).scrollTop();

          //show loader gif and hide the content
          $('.main-content, .loader').toggle();

          //ajax call to update the fields
          $.ajax({
            type: "POST",
            url: url,
            dataType: 'json',
            data: data,
            success:
            function(data)
            {
              $('.main-content, .loader').toggle(); //toggle back the content
              $(window).scrollTop(tempScrollTop);   //put screen back into original scroll position
            }
          });

        }

      });

      $(".sub-fields").change(function()
      {
        var value = $(this).val();                                                   //value

        if(!value)
        {
          alert("cannot be empty!");
        }
        else
        {
          if($(this).attr('id') == "setup" && value != 0 && value != 1)
          {
            alert("Setup has to be 0 or 1");
          }
          else
          {

            var field = $(this).parent().prev().text();                             //field
            var id = $(this).parent().parent().parent().parent().attr('data-id');   //row id

            //set up the ajax call parameters
            var data = { action: 'updateSubscriptionSettings', id: id, field: field, value: value};
            var url =  ajax_object.ajax_url;

            //save scroll position
            var tempScrollTop = $(window).scrollTop();

            //show loader gif and hide the content
            $('.main-content, .loader').toggle();

            //ajax call to update the fields
            $.ajax({
              type: "POST",
              url: url,
              dataType: 'json',
              data: data,
              success:
              function(data)
              {
                $('.main-content, .loader').toggle(); //toggle back the content
                $(window).scrollTop(tempScrollTop);   //put screen back into original scroll position
              }
            });
          }
        }

      });
    </script>
<?php
  }
?>