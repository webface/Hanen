<div class="breadcrumb">
  <?= CRUMB_DASHBOARD ?>    
  <?= CRUMB_SEPARATOR ?>    
    <span class="current"><?= __("Add Staff Upgrade", "EOT_LMS") ?></span>     
</div>
<?php
	if(isset($_REQUEST['subscription_id']) && $_REQUEST['subscription_id'] != "")
	{
		if(isset($_REQUEST['library_id']) && $_REQUEST['library_id'] != "")
		{
			if(current_user_can("is_sales_rep") || current_user_can("is_sales_manager"))
		    {
		        global $current_user;
		    	$subscription_id = filter_var($_REQUEST['subscription_id'],FILTER_SANITIZE_NUMBER_INT); // The subscription id
		        $library_id = filter_var($_REQUEST['library_id'],FILTER_SANITIZE_NUMBER_INT); // The library id
         		$subscription = getSubscriptions($subscription_id);
	         	if(!$subscription)
	         	{
	         		echo __("ERROR: I could not retrieve the subscription you are trying to upgrade.", "EOT_LMS");
	         		return;
	         	}
		        $staff_credits = $subscription->staff_credits; // The staff Credits for the subscription
		        $camp_director = get_userdata( $subscription->manager_id ); // The camp directer user information
		        if(!$camp_director)
		        {
		        	echo __("ERROR: I could not get the camp director's information.", "EOT_LMS");
		        	return;
		        }

		        // Variable declaration
		        $rep_id = $current_user->ID; // The rep's wordpress user ID
		        $org_id = $subscription->org_id; // Organization ID
                $org_name = get_the_title($org_id);
	            $full_name = ucwords ($camp_director->user_firstname . " " . $camp_director->user_lastname);
	            $address = get_post_meta ($org_id, 'org_address', true);
	            $city = get_post_meta ($org_id, 'org_city', true);
	            $state = get_post_meta ($org_id, 'org_state', true);
	            $country = get_post_meta ($org_id, 'org_country', true);
	            $zip = get_post_meta ($org_id, 'org_zip', true);
	            $phone = get_post_meta ($org_id, 'org_phone', true);

            	$args = array(
            		'role__in' => array(
            			'salesrep', 
            			'sales_manager'
            		)
            	); // Arguments to get only sales rep
				$salesreps = get_users($args); // All sales rep.
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
			<h1 class="article_page_title"><?= __("Add Staff Upgrade", "EOT_LMS") ?></h1>
			<div id="bill_form">
				<form id="addUpgrade">
		        	<h2><?= __("Add Staff Information", "EOT_LMS") ?></h2>
		            <div class="form-row">
		                <label style="width:200px;"><?= __("# of Additional Staff Accounts:", "EOT_LMS") ?></label>
		                <input type="text" name="accounts" value="" required/>
		            </div>
		            <div class="form-row">
		                <label><?= __("Price:", "EOT_LMS") ?></label>
		                <input type="text" name="price" value="" required/>
		            </div>
		            <div class="form-row">
		                <label><?= __("Discount Notes:", "EOT_LMS") ?></label>
		                <input type="text" name="discount_note" value="" required/>
		            </div>
		            <div class="form-row">
		                <label><?= __("Other Notes:", "EOT_LMS") ?></label>
		                <input type="text" name="other_note" value="" required/>
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
		            <div class="form-row">
		                <label><?= __("Sales Rep", "EOT_LMS") ?></label>
						<select name="rep_id">
							<option value="0" ><?= __("None", "EOT_LMS") ?></option>
	<?php
							// Goes to each sales rep, prints their name and select the current rep for this subscription 
							foreach ($salesreps as $rep) 
							{
	?>
								<option value="<?= $rep->ID ?>"<?php echo ($rep->ID == $rep_id) ? 'selected' : '' ?>><?= $rep->display_name ?></option>
	<?php
							}
	?>
						</select>
		            </div>
                   	<div class="form-row">
                        <label><?= __("Payment Method:", "EOT_LMS") ?></label>
                        <select name="method" id ="method">
                            <option value="Stripe"><?= __("Credit Card", "EOT_LMS") ?></option>
                            <option value="check"><?= __("Check", "EOT_LMS") ?></option>
                            <option value="free"><?= __("Free", "EOT_LMS") ?></option>
                        </select><br />
                    </div>
                    <div id="creditcard_opts">
			            <h2><?= __("Credit Card", "EOT_LMS") ?></h2>
<?php 
			                $cus_id = get_post_meta($org_id, 'stripe_id', true);
			                $cards = get_customer_cards ($cus_id);
?>

<?php if (!empty($cards)) { ?>
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
<?php } ?>
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
		            </div>
		            <?php if ($cus_id) { ?><input type="hidden" name="customer_id" value="<?php echo $cus_id; ?>" /><?php } ?>
		            <input type="hidden" name="email" value="<?php echo $camp_director->user_email; ?>" />
		            <input type="hidden" name="user_id" value="<?php echo $camp_director->ID; ?>" />
					<input type="hidden" name="subscription_id" value="<?= $subscription_id ?>">

		            <p>
		                <i class="fa fa-lock"></i> <?= __("This site uses 256-bit encryption to safeguard your credit card information.", "EOT_LMS") ?>
		            </p>
		            <input value="<?= __("Make Payment", "EOT_LMS") ?>" id="submit_bill" type="button">
		        </form>
			</div>	
		    <div class="processing_payment round_msgbox">
		        <?= __("Attempting to charge Credit card and create the subscription...", "EOT_LMS") ?> <br />
		        <i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>                          <br />
		        <?= __("If you see this message for more than 15 seconds, please call 877-237-3931 for assistance.", "EOT_LMS") ?>  
		    </div>

			<script>
				$('#submit_bill').click( function() {
	                var button_ref = $(this);
	                $(button_ref).attr('disabled','disabled');
	                $('.processing_payment').slideDown();
	                $('#addUpgrade').show();
	                var data = $('#addUpgrade').serialize () + "&action=upgradeSubscription";
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
	                            //show_error ("SUCCESS: added staff accounts to subscription!");
	                            window.location.href = "?part=admin_view_subscriptions&library_id=" + response.library_id + "&status=upgradeSubscription";
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
			</script>

<?php
			}
			else
			{
				wp_die(__("You do not have privilege to view this page.", "EOT_LMS"));
			}
		}
		else
		{
			wp_die(__("Invalid library id.", "EOT_LMS"));
		}
	}
	else
	{
		wp_die(__("Invalid subscription id", "EOT_LMS"));
	}
?>
