<div class="breadcrumb">
  <?= CRUMB_DASHBOARD ?>    
  <?= CRUMB_SEPARATOR ?>    
    <span class="current"><?= __("Upgrade Subscription", "EOT_LMS") ?></span>     
</div>
<h1 class="article_page_title"><?= __("Upgrade your Subscription", "EOT_LMS") ?></h1>
<?php

// verify this user has access to this portal/subscription/page/view
$true_subscription = verifyUserAccess(); 
if(!isset($true_subscription['status']) || !$true_subscription['status'])
{
    echo __("ERROR: You do not have permissions to modify this subscription. Please contact the administrator.", "EOT_LMS");
    return;
}

if(isset($_REQUEST['subscription_id']) && $_REQUEST['subscription_id'] != "")
{
	global $current_user;
	$subscription_id = filter_var($_REQUEST['subscription_id'],FILTER_SANITIZE_NUMBER_INT); // The subscription id
	$subscription = getSubscriptions($subscription_id); // The subscription information
 	if(!$subscription)
 	{
 		echo __("ERROR: Could not fetch your subscription details. Please contact the administrator.", "EOT_LMS");
 		return;
 	}
    $num_staff = $subscription->staff_credits; // # of staff in the subscription

    $org_id = $subscription->org_id; // Organization ID
    $org_name = get_the_title($org_id);    
 
    // Customer INFO
    $camp_director = get_userdata( $subscription->manager_id ); // The camp directer user information
    if(!$camp_director)
    {
    	echo __("ERROR: I could not get the camp director's information.", "EOT_LMS");
    	return;
    }
    $full_name = ucwords ($camp_director->user_firstname . " " . $camp_director->user_lastname);
    $address = get_post_meta ($org_id, 'org_address', true);
    $city = get_post_meta ($org_id, 'org_city', true);
    $state = get_post_meta ($org_id, 'org_state', true);
    $country = get_post_meta ($org_id, 'org_country', true);
    $zip = get_post_meta ($org_id, 'org_zip', true);
    $phone = get_post_meta ($org_id, 'org_phone', true);

    // Add upgrade number of staff
    $upgrades = getUpgrades ($subscription_id);
    if($upgrades)
    {
        foreach($upgrades as $upgrade)
        {
            $num_staff += $upgrade->accounts;
        }
    }

    // get the library title
  	$library = getLibrary ($subscription->library_id); // The library information base on the user current subscription
    if($library)
    {
    	$library_title = $library->name; // The name of the library
    }
    else
    {
		echo __("Sorry but we coulnd't find your library. Please contact the site administrator.", "EOT_LMS");
    	return;
    }
?>
   	<style type="text/css">
        .table_header {
            height: 30px;
            margin-top: 25px;
            width: 665px;
        }
        .table_header_list {
            background-color: #C8E0EE;
            border-top: 1px solid #888;
            border-bottom: 1px solid #888;
            font-weight: bold;
            float: left;
            display: block;
            padding: 5px;
            text-align: center;
            margin: 0;
        }
        .table_header_list.library {
            width: 215px;
            border-left: 1px solid #888;
        }
        .table_header_list.amount {
            width: 180px;
            border-left: 1px solid #888;
        }
        .table_header_list.discount {
            width: 125px;
            border-left: 1px solid #888;
        }
        .table_header_list.subtotal {
            width: 100px;
            border-left: 1px solid #888;
            border-right: 1px solid #888;
        }
        .library {
            width: 215px;
            list-style: none outside none;
        }
        .amount {
            width: 180px;
            border-left: 1px solid #FFF;
            list-style: none outside none;
        }
        .discount {
            width: 125px;
            border-left: 1px solid #FFF;
            list-style: none outside none;
        }
        .subtotal {
            width: 100px;
            border-left: 1px solid #FFF;
            list-style: none outside none;
        }
        .library img {
            margin: 2px 5px 2px 2px;
            float: left;
        }
        .calc_topics {
            height: 30px;
            width: 663px;
            border-right: 1px solid #888;
            border-left: 1px solid #888;
        }
        .calc_le {
            float: left;
            display: block;
            padding: 5px;
            margin: 0;
            height: 20px;
        }
        .calc_lel, .calc_le_sp_dc, .calc_le_sp_oc, .calc_le_sp_prp {
            float: left;
            display: block;
            padding: 5px;
            margin: 0;
            height: 20px;
        }
        .calc_ss {
            float: left;
            display: block;
            padding: 5px;
            margin: 0;
            height: 20px;
        }
    
        .calc_me, .calc_bc {

            float: left;
            display: block;
            padding: 5px;
            margin: 0;
            height: 20px;
        }
        .calc_bc img {
            margin: 2px 5px 2px 2px;
            float: left;
        }
        .calc_se {
            float: left;
            display: block;
            padding: 5px;
            margin: 0;
            height: 20px;
        }
        .calc_topics.le {
            border-bottom: 1px solid #888;
            height: 30px;
        }
        .calc_topics.lel, .calc_topics.le_sp_dc, .calc_topics.le_sp_oc, .calc_topics.le_sp_prp  {
            border-bottom: 1px solid #888;
            height: 30px;
        }
        .calc_topics.ss {
            border-bottom: 1px solid #888;
            height: 30px;
        }
        .calc_topics.se {
            border-bottom: 1px solid #888;
            height: 30px;
        }
        .calc_topics.me {
            border-bottom: 1px solid #888;
            height: 30px;
        }
        
        .calc_ce 
        {
            float: left;
            display: block;
            padding: 5px;
            margin: 0;
            height: 20px;
        }
        
        .calc_topics.ce
        {
            border-bottom: 1px solid #888;
            height: 30px;
        }

        .calc_dd 
        {
            float: left;
            display: block;
            padding: 5px;
            margin: 0;
            height: 20px;
        }
        
        .calc_topics.dd
        {
            border-top: 1px solid #888;
            height: 30px;
        }

        .datadisk {
            width: 542px;
            list-style: none outside none;
        }

        .datadisk img {
            margin: 2px 5px 2px 2px;
            float: left;
        }

        input.small_box {
            text-align: center;
            width: 30px;
        }
        input.medium_box {
            text-align: center;
            width: 40px;
        }
        input.large_box {
            text-align: center;
            width: 60px;
        }
        .expanded_top {
            height: 30px;
            text-align: center;
        }
        .expanded_bot {
            text-align: center;
        }
        .amount_x {
            float: left;
            display: block;
            width: 15px;
            text-align: center;
        }
        .amount_left {
            float: left;
            width: 120px;
            text-align: left;
        }
        .amount_right {
            float: left;
            width: 45px;
            text-align: center;
        }
        #calc_body .amount {
            display: none;
        }
        #calc_body .discount {
            display: none;
        }
        #calc_body .subtotal {
            display: none;
        }
        #calc_total {
            height: 35px;
            width: 663px;
            border: 1px solid #888;
            text-align: right;
            padding-top: 15px;
        }
        #text_total {
            float: left;
            width: 550px;
            text-align: right;
        }
        #sum_total {
            float: left;
            width: 113px;
            text-align: center;
        }
        #submit_calc {
            width: 150px;
            height: 30px;
            float: right;
            margin-top: 20px;
            font-weight: bold;
        }
        #calc_footer {
            width: 663px;
            height: 40px;
        }
        #billing {
            display: none;
        }
        .billing_item {
            height: 50px;
            width: 663px;
            border-right: 1px solid #888;
            border-left: 1px solid #888;
            border-bottom: 1px solid #888;
        }
        .bill_item {
            float: left;
            display: block;
            padding: 5px;
            margin: 0;
            height: 40px;
        }
        #bill_total {
            height: 26px;
            width: 663px;
            border: 1px solid #888;
            text-align: right;
            padding-top: 8px;
        }
        .border_left {
            border-left: 1px solid #888;
        }
        #bill_sum_total {
            float: left;
            width: 113px;
            text-align: center;
        }
        #bill_form label {
            display: block;
            float: left;
            font-weight: bold;
            width: 130px;
        }
        #creditcard_opts img {
            margin-left: 130px;
        }
        #submit_bill {
            width: 150px;
            height: 30px;
            margin: 10px 0;
            font-weight: bold;
        }
        #bill_response {
            display: none;
        }
        .processing_payment {
            display: none;
        }
        #referred_other {
            display: none;
        }
    </style>
	<h2><?= $library_title ?></h2>
	<form id="calculateCostForm" method="post">
		<input type="hidden" name="view" value="page">
	    <input type="hidden" name="id" value="payment_upgrade">
	    <input type="hidden" name="subscription_id" value="<?= $subscription->ID ?>">
		<table class="data">
			<tbody>
				<tr>
				  	<td class="label">
				    	<?= __("Current Max. Staff", "EOT_LMS") ?>&nbsp;&nbsp;&nbsp;
				  	</td>
				  	<td class="value right">
				    	<?= $num_staff ?>              
				    </td>
				</tr>
		        <tr>
				    <td class="label">
			      		<b><?= __("Number of staff accounts to add:", "EOT_LMS") ?>&nbsp;&nbsp;&nbsp;</b>
				    </td>
				    <td>
			      		<input class="right" type="text" name="num_staff_upgrade" id="num_staff_upgrade" size="4" autocomplete="off" required>
				    </td>
			  	</tr>
			</tbody>
		</table>
		<div style="text-align: right; width: 200px; margin-left: 7px;">
			<input type="submit" id="btn_calculate" value="Next"><span id="loading" style="display:none;">&nbsp;&nbsp;<i class="fa fa-spinner fa-pulse fa-2x"></i></span><br>
			<span class="sm"><?= __("(goes to Payment Details Page)", "EOT_LMS") ?></span>
		</div>
		<input type="hidden" name="library_id" value="<?= $subscription->library_id ?>">
		<input type="hidden" name="num_staff_current" value="<?= $num_staff ?>">
	</form>
	<form method="post" id="directorUpgradeSubscription" style="display:none">
		<div id="bill_form">
	            <div class="form-row">
	                <label><?= __("Accounts to Add:", "EOT_LMS") ?></label>
	                <span id="numAccountsToAdd"></span>
	            </div>
	            <div class="form-row">
	                <label><?= __("New Max Account:", "EOT_LMS") ?></label>
	                <span id="newAccountMax"></span>
	            </div>
	            <div class="form-row">
	                <label><?= __("Payment Amount:", "EOT_LMS") ?></label>
	                <?= __("$", "EOT_LMS") ?><span id="paymentAmount"></span> <?= __("USD", "EOT_LMS") ?>
	            </div>
	            <h2><?= __("Billing Address", "EOT_LMS") ?></h2>
	            <div class="form-row">
	                <label><?= __("Organization Name", "EOT_LMS") ?></label>
	                <input type="text" name="org_name" value="<?php echo $org_name; ?>" required/>
	            </div>
	            <div class="form-row">
	                <label><?= __("Cardholder Name", "EOT_LMS") ?></label>
	                <input type="text" name="full_name" value="<?php echo $full_name; ?>" required/>
	            </div>
	            <div class="form-row">
	                <label><?= __("Address", "EOT_LMS") ?></label>
	                <input type="text" name="address" value="<?php echo $address; ?>" required/>
	            </div>
	            <div class="form-row">
	                <label><?= __("City", "EOT_LMS") ?></label>
	                <input type="text" name="city" value="<?php echo $city; ?>" required/>
	            </div>
	            <div class="form-row">
	                <label><?= __("State/Province", "EOT_LMS") ?></label>
	                <input type="text" name="state" value="<?php echo $state; ?>" required/>
	            </div>
	            <div class="form-row">
	                <label><?= __("Country", "EOT_LMS") ?></label>
	                <input type="text" name="country" value="<?php echo $country; ?>" required/>
	            </div>
	            <div class="form-row">
	                <label><?= __("Zip/Postal Code", "EOT_LMS") ?></label>
	                <input type="text" name="zip" value="<?php echo $zip; ?>" required/>
	            </div>
	            <div class="form-row">
	                <label><?= __("Phone Number", "EOT_LMS") ?></label>
	                <input type="text" name="phone" value="<?php echo $phone; ?>" required/>
	            </div>
	            <h2><?= __("Credit Card", "EOT_LMS") ?></h2>
<?php 
	                $cus_id = get_post_meta($org_id, 'stripe_id', true);
                    if (empty($cus_id))
                    {
                        // no stripe customer ID for this org. Will need to create one.
                        $cards = '';
                    }
                    else
                    {
                        $cards = get_customer_cards ($cus_id); // get the credit cards for this customer
                    }
	                
                    // if we have CC cards for this customer, display them.
                    if (!empty($cards)) 
                    { 
?>
    	                <table cellpadding="5" cellspacing="0" width="90%" class="cc_cards_list">
    	                    <tr>
    	                        <td>&nbsp;</td>
    	                        <td><?= __("Type", "EOT_LMS") ?></td>
    	                        <td><?= __("Number", "EOT_LMS") ?></td>
    	                        <td><?= __("Expiration", "EOT_LMS") ?></td>
    	                        <td><?= __("CVC", "EOT_LMS") ?></td>
    	                    </tr>
    	                    <?php foreach ($cards as $card) { ?>
    	                        <tr>
    	                            <td><input type="radio" name="cc_card" value="<?php echo $card->id; ?>" /></td>
    	                            <td><?php echo $card->brand; ?></td>
    	                            <td>**** **** **** <?php echo $card->last4; ?></td>
    	                            <td><?php echo $card->exp_month; ?> / <?php echo $card->exp_year; ?></td>
    	                            <td>***</td>
    	                        </tr>
    	                    <?php } ?>
    	                </table>
    	                <a href="#" id="new_card"><?= __("Add new Card", "EOT_LMS") ?></a>
<?php 
                    } 
?>
	                <div id="new_cc_form" <?php if (!empty($cards)) { ?> style="display:none;" <?php } else { ?> style="display:block;" <?php } ?> >
	                    <div class="form-row">
	                        <label><?= __("Card Number", "EOT_LMS") ?></label>
	                        <input type="text" size="20" autocomplete="off" name="cc_num" value="" required/>
	                    </div>
	                    <div class="form-row">
	                        <label><?= __("CVC", "EOT_LMS") ?></label>
	                        <input type="text" size="4" autocomplete="off" name="cc_cvc" value="" required/>
	                    </div>
	                    <div class="form-row">
	                        <label><?= __("Expiration", "EOT_LMS") ?></label>
	                        <select name="cc_mon" required>
	                            <option value="" selected="selected">MM</option>
	                            <?php for ($i = 1 ; $i <= 12 ; $i++) { ?>
	                                <option value="<?php if ($i < 10) {echo "0";} echo $i; ?>"><?php if ($i < 10) {echo "0";} echo $i; ?></option>
	                            <?php } ?>
	                        </select>
	                        <span> / </span>
	                        <select name="cc_yr" required>
	                            <option value="" selected="selected">YYYY</option>
	                            <?php for ($i = date('Y') ; $i <= (date('Y') + 10) ; $i++) { ?>
	                                <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
	                            <?php } ?>
	                        </select>
	                    </div>
	            </div>
	            <?php if ($cus_id) { ?><input type="hidden" name="customer_id" value="<?php echo $cus_id; ?>" /><?php } ?>
	            <input type="hidden" name="email" value="<?php echo $camp_director->user_email; ?>" />
	            <input type="hidden" name="user_id" value="<?php echo $camp_director->ID; ?>" />
				<input type="hidden" name="subscription_id" value="<?= $subscription_id ?>">
	            <input type="hidden" name="method" value="Stripe" />

	            <p>
	                <i class="fa fa-lock"></i> <?= __("This site uses 256-bit encryption to safeguard your credit card information.", "EOT_LMS") ?>
	            </p>

				<span id="back" class="btn" style="display:none"><?= __("Previous", "EOT_LMS") ?></span> &nbsp;&nbsp;&nbsp;
	            <input value="<?= __("Make Payment", "EOT_LMS") ?>" id="submit_bill" type="button" class="btn">
		</div>	
	    <div class="processing_payment round_msgbox">
	        <?= __("Attempting to charge Credit card and create the subscription...", "EOT_LMS") ?> <br />
	        <i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>                          <br />
	        <?= __("If you see this message for more than 15 seconds, please call 877-237-3931 for assistance.", "EOT_LMS") ?>  
	    </div>
    </form>


	<script>
	$( document ).ready(function() {
		$('#submit_bill').click( function() {
            var button_ref = $(this);
            $(button_ref).attr('disabled','disabled');
            $('.processing_payment').slideDown();
            $('#directorUpgradeSubscription').show();
            var data = $('#directorUpgradeSubscription').serialize () + "&action=upgradeSubscription&accounts=" + $('#num_staff_upgrade').val();
            $.ajax({
                type: "POST",
                url: eot.ajax_url,
                data: data,
                dataType: "json",
                success: function(response) {
                    eot_status = response.status;
                    my_response = response;
                    $('.processing_payment').slideUp();
                    if (!eot_status) {
                        $(button_ref).removeAttr('disabled');
                        show_error (my_response.message);
                    }
                    else
                    {
                        // show completed message
                        show_error ("<?= __("SUCCESS: We've increased your max staff accounts!", "EOT_LMS") ?>");
                        window.location.href = eot.dashboard;
                    }

                    return eot_status;
                },
				error: function(XMLHttpRequest, textStatus, errorThrown) 
				{
                    $('.processing_payment').slideUp();
                    $(button_ref).removeAttr('disabled');
					show_error( "<?= __("ERROR: Unable to upgrade staff accounts:", "EOT_LMS") ?> " + textStatus + " " + errorThrown + "<br><?= __("Please contact the site administrator!", "EOT_LMS") ?>");
				}
            });
        }); 
        $('#expmonth').focus(function() {
            if ($(this).val() == 'mm') {
                $(this).val('');
            }
        });
        
        $('#expyear').focus(function() {
            if ($(this).val() == 'yy') {
                $(this).val('');
            }
        });
        
        $('#expmonth').blur(function() {
            if ($(this).val() == '') {
                $(this).val('mm');
            }
        });
        
        $('#expyear').blur(function() {
            if ($(this).val() == '') {
                $(this).val('yy');
            }
        });
        
        $("#method").change(function() {
            if($("#method :selected").val() != "Stripe") {
                $("#creditcard_opts").fadeOut(500);
            } else {
                $("#creditcard_opts").fadeIn(500);
            }
        });
                
		// Process calculation by number of staff.
		$( "#btn_calculate" ).click(function( event ) {
			event.preventDefault();
			var num_accounts = $('#num_staff_upgrade').val();
			// Validate Input.
			if( num_accounts <= 0 || num_accounts == '' || isNaN(num_accounts) )
			{
				alert('<?= __("Invalid input.", "EOT_LMS") ?>');
				return;
			}
			else
			{
				$("#loading").show();
                // Calculate Total Cost.
				var url = ajax_object.ajax_url + "?action=calculateCost";
		      	$.ajax( {
		        type: "POST",
		        dataType: "json",
		        url: url,
		        data: $( "form#calculateCostForm" ).serialize(),

		        // If we are successful
		        success: function(data)
		        {
		          if (data.success) 
		          {
					$('#paymentAmount').text(data.staff_price);
					/* 
					 * Toogle forms.
					 */
				 	$( "form#calculateCostForm" ).slideUp( "slow", function() {
			  		});
				 	$( "form#directorUpgradeSubscription" ).slideDown( "slow", function() {
			  		});
			  		$('#numAccountsToAdd').text(num_accounts);
			  		$('#newAccountMax').text( <?= $num_staff ?> + parseInt(num_accounts) ); // New Account Max.
		          	$( "#back" ).show();
		          } 
		          else 
		          {
		            if (data.display_errors) 
		            {
		             show_error (data.errors);
	             	}
		          }
		        },
		        // If it fails on the other hand.
		        error: function(XMLHttpRequest, textStatus, errorThrown) 
		        {
					show_error( "<?= __("ERROR: Unable to calculate the upgade cost:", "EOT_LMS") ?> " + textStatus + " " + errorThrown + "<br><?= __("Please contact the site administrator!", "EOT_LMS") ?>");
		        }
		      });
			}
		});
		// Back button. Hides the payment form and slides down the order form for camp director.
		$( "#back" ).click(function( event ) {
            $("#loading").hide();
		 	$( "form#directorUpgradeSubscription" ).slideUp( "slow", function() {
	  		});
		 	$( "form#calculateCostForm" ).slideDown( "slow", function() {
	  		});
	  		$( "#back" ).hide();
		});
	});
	</script>
<?php
}
else
{
	wp_die(__("Invalid subscription id.", "EOT_LMS"));
}
?>
