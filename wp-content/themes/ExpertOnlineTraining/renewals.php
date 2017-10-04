<?php
/**
 * Template Name: Renewals
 *
 * @package WordPress
 * @subpackage Twenty_Fourteen
 * @since Twenty Fourteen 1.0
 */
get_header();
global $wpdb;
if(isset($_REQUEST['renewed']))
{
?>
    <br/>
    <div class="bs">
        <div class="container">
         <div class="well">Your Subscription has been renewed!</div>
         <br/>
         <a class="btn btn-primary" href="<?php echo wp_login_url(); ?>" title="Login to your dashboard">Login</a>
        </div>
    </div>

    <div style="clear:both"></div>
<?php
}
else
{
    $id =isset($_REQUEST['id'])? filter_var($_REQUEST['id'], FILTER_SANITIZE_NUMBER_INT) : 0;
    $key = isset($_REQUEST['key'])?filter_var($_REQUEST['key'], FILTER_SANITIZE_STRING) : '';
    if($id == 0 || $key == "")
    {
        die("Sorry but the link you used is incorrect. Please contact our customer support team toll-free M-F 9-5 ET at 1-877-390-2267");
    }
    $user_data = get_user_by('ID', $id);
    if (!$user_data)
    {
        die("Sorry but the link you used is incorrect. Please contact our customer support team toll-free M-F 9-5 ET at 1-877-390-2267");
    }

    // make sure its not after oct 20thy
    $today = date('Y-m-d');
    $deadline = '2017-10-20';
    $deadline_passed = 0; // boolean weather the deadline passed or not.
    if (strtotime($today) > strtotime($deadline))
    {
        $deadline_passed = 1;
    }

    $email = $user_data->user_email;
    //d($user_data);
    $subscriptions = $wpdb->get_results("SELECT * FROM ". TABLE_SUBSCRIPTIONS . " WHERE manager_id = $id", OBJECT_K);
    //d($subscriptions);

    // go through all of their subscriptions till we find the right one that matches the hash.
    foreach($subscriptions as $subscription)
    {
        //d(($email.$subscription->ID));
        if(wp_hash($email.$subscription->ID) == $key)
        {
            $the_subscription = $subscription;
        }
    }
   
    if(isset($the_subscription))
    {
        $org_id = $the_subscription->org_id; // Organization ID
        $org_name = get_the_title($org_id);
        $full_name = ucwords ($user_data->user_firstname . " " . $user_data->user_lastname);
        $address = get_post_meta ($org_id, 'org_address', true);
        $city = get_post_meta ($org_id, 'org_city', true);
        $state = get_post_meta ($org_id, 'org_state', true);
        $country = get_post_meta ($org_id, 'org_country', true);
        $zip = get_post_meta ($org_id, 'org_zip', true);
        $phone = get_post_meta ($org_id, 'org_phone', true);
        $camp_name = $org_id > 0 ? get_the_title($org_id) : "N/A"; // The name of the camp
        $data = compact ("org_id");
        $price = $the_subscription->price; // Subscription price q
        $library = getLibraries($the_subscription->library_id); // The library of this subscription
        $library_id = $library->ID; // the library id.
        $first_name = $user_data->first_name; // Director's First Name
        $last_name = $user_data->last_name;; // Director's Last Name
        $rep_id = $the_subscription->rep_id; // Representative's ID
        $discounted_price = $library->price;

        if($library_id == 1 && $deadline_passed == 0)// LE so apply the discount
        {
            $discounted_price = $discounted_price - 100;
        }

        if($library)
        {
            // Add upgrade number of staff
            $upgrades = getUpgrades ($the_subscription->ID);
            $num_staff = $the_subscription->staff_credits; // Number of accounts.
            // Sum all of the upgrades staff accounts and add them to the current subscription staff credits.
            if($upgrades)
            {
                foreach($upgrades as $upgrade)
                {
                    $num_staff += $upgrade->accounts;
                }
            }
?>
    <div class="col-md-9 content-area" id="main-column">
        <main id="main" class="site-main" role="main">
            <h1 class="article-title">Renew Subscription</h1>
            <h3>2017 Subscription</h3>
            <p>Donec rutrum congue leo eget malesuada. Praesent sapien massa, convallis a pellentesque nec, egestas non nisi. Donec rutrum congue leo eget malesuada. Cras ultricies ligula sed magna dictum porta. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla quis lorem ut libero malesuada feugiat. Vivamus magna justo, lacinia eget consectetur sed, convallis at tellus. Donec rutrum congue leo eget malesuada. Quisque velit nisi, pretium ut lacinia in, elementum id enim. Nulla quis lorem ut libero malesuada feugiat.</p>
                            <table class="data sm">
                                    <tbody>
                                            <tr>
                                                    <td class="label">
                                                            Library
                                                    </td>
                                                    <td class="value">
                                                            <?= $library->name?>         
                                                    </td>
                                            </tr>
                                            <tr>
                                                    <td class="label">
                                                            Subscribe Date
                                                    </td>
                                                    <td class="value">
                                                            <?= dateTimeFormat($the_subscription->trans_date) ?>         
                                                    </td>
                                            </tr>
                                            <tr>
                                                    <td class="label">
                                                            Start Date
                                                    </td>
                                                    <td class="value">
                                                            <?= dateTimeFormat($the_subscription->start_date) ?>         
                                                    </td>
                                            </tr>
                                            <tr>
                                                    <td class="label">
                                                            Expire Date
                                                    </td>
                                                    <td class="value">
                                                            <?= dateTimeFormat($the_subscription->end_date) ?>              
                                                    </td>
                                            </tr>
                                            <tr>
                                                    <td class="label">
                                                            Director
                                                    </td>
                                                    <td class="value">

                                                            <?php
                                                            /* 
                                                             * Display the director info if they exsist, otherwise display N/a 
                                                             */
                                                            if($user_data)
                                                            {
                                                                    echo $first_name . " " . $last_name . '/ <a href="mailto: ' . $user_data->user_email .'"><' . $user_data->user_email . '</a>';
                                                            }
                                                            else
                                                            {
                                                                    echo "N/a.";
                                                            }
                                                            ?>
                                                    </td>
                                            </tr>
                                            <tr>
                                                    <td class="label">
                                                            Camp Name
                                                    </td>
                                                    <td class="value">
                                                            <?= $camp_name ?>        
                                                    </td>
                                            </tr>
                                            <tr>
                                                    <td class="label">
                                                            Staff
                                                    </td>
                                                    <td class="value">
                                                         <?= $num_staff ?>          
                                                    </td>
                                            </tr>

                                            <tr>
                                                    <td class="label">
                                                            Dash Price
                                                    </td>
                                                    <td class="value">
                                                            $ <?= number_format($the_subscription->dash_price, 2, ".", "") ?>         
                                                    </td>
                                            </tr>
                                            <?php  if($the_subscription->dash_discount > 0){?>
                                            <tr>
                                                    <td class="label">
                                                            Dash Discount
                                                    </td>
                                                    <td class="value">
                                                            $ <?= number_format($the_subscription->dash_discount, 2, ".", "") ?>         
                                                    </td>
                                            </tr>
                                            <?php } ?>
                                            <tr>
                                                    <td class="label">
                                                            Staff Price
                                                    </td>
                                                    <td class="value">
                                                            $ <?= number_format($the_subscription->staff_price, 2, ".", "") ?>         
                                                    </td>
                                            </tr>
                                            <?php  if($the_subscription->staff_discount > 0){?>
                                            <tr>
                                                    <td class="label">
                                                            Staff Discount
                                                    </td>
                                                    <td class="value">
                                                            $ <?= number_format($the_subscription->staff_discount, 2, ".", "") ?>         
                                                    </td>
                                            </tr>
                                            <?php } ?>
                                            <tr>
                                                    <td class="label">
                                                            Payment Method
                                                    </td>
                                                    <td class="value">
                                                            <?= $the_subscription->method ?>        
                                                    </td>
                                            </tr>
                                            <tr>
                                                    <td class="label">
                                                            Total
                                                    </td>
                                                    <td class="value">
                                                            $ <?= number_format($the_subscription->price, 2, ".", "") ?>         
                                                    </td>
                                            </tr>
                                    </tbody>
                            </table>
<?php
                            // Display upgrade history if the director had an upgrade.
                        if($upgrades)
                        {
                            echo '<h1 class="article_page_title">Upgrades History</h1>';
                                    // Tables that will be displayed in the front end.
                                    $upgradeTableObj = new stdClass();
                                    $upgradeTableObj->rows = array();
                                    $upgradeTableObj->headers = array(
                                            'Date' => 'center',
                                            'Total Amount' => 'center',
                                            'Accounts' => 'center',
                                            'Sales Rep' => 'center',
                                            'Discount Note' => 'center',
                                            'Other Note' => 'center',

                                    );


                                    foreach($upgrades as $upgrade)
                                    {
                                            $upgrade_rep_info = get_userdata($upgrade->rep_id); // Rep Info from WP
                                            $upgrade_rep_name = ($upgrade_rep_info) ? $upgrade_rep_info->first_name . " " . $upgrade_rep_info->last_name : 'Self Upgrade'; // REP first and last name
                                            // Create table
                                            $upgradeTableObj->rows[] = array(
                                                    $upgrade->date, // Transaction Date
                                                    '$' . number_format($upgrade->price, 2, '.', ''), // Transaction price.
                                                    $upgrade->accounts,
                                                    $upgrade_rep_name, // REP first and last name
                                                    $upgrade->discount_note,
                                                    $upgrade->other_note

                                            );
                                    }
                              CreateDataTable($upgradeTableObj); // Print the table in the page
                    }
                    }
    ?>

    <h3>2018 Subscription</h3>

            <p>Donec rutrum congue leo eget malesuada. Praesent sapien massa, convallis a pellentesque nec, egestas non nisi. Donec rutrum congue leo eget malesuada. Cras ultricies ligula sed magna dictum porta. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla quis lorem ut libero malesuada feugiat. Vivamus magna justo, lacinia eget consectetur sed, convallis at tellus. Donec rutrum congue leo eget malesuada. Quisque velit nisi, pretium ut lacinia in, elementum id enim. Nulla quis lorem ut libero malesuada feugiat.</p>
            <div class="row bs">
                <div class="col-md-6">
            <div id="bill_form">
                                    <form id="renewCamp">

    <?php if($library_id == 1){ ?>
                                <div class="form-group">
                                    <label>Regular Price:<strike style="color:red">$399</strike> (-$100 discount for early renewal) <span style="color:green">$299.00</span></label>
                                </div>
                                        <?php }else{?>
                                <div class="form-group">
                                    <label>Regular Price:<span style="color:green">$<?= $the_subscription->dash_price?></span></label>
                                </div>        
                                         <?php } ?>

                                <div class="form-group">
                                    <label for="accounts" style=""># of Additional Staff Accounts:</label>
                                    <input id="accounts" style="width:50%" class="form-control" type="number" name="accounts" value="<?= $num_staff?>" min="0"/>
                                </div>

                                <div class="form-group">
                                    <label for="total" style="width:200px;">Total $:<span style="color:green" class="total">00.00</span>&nbsp;&nbsp;<span style="font-size:10px" class="per_staff"></span></label>
                                    <input style="width:50%" class="form-control" type="hidden" name="total" id="total" value=""/>
                                </div>  


                                <h2>Billing Address</h2>
                                <div class="form-group">
                                    <label>Organization Name</label>
                                    <input class="form-control" type="text" name="org_name" value="<?php echo $org_name; ?>" required/>
                                </div>
                                <div class="form-group">
                                    <label>Cardholder Name</label>
                                    <input class="form-control" type="text" name="full_name" value="<?php echo $full_name; ?>" required/>
                                </div>
                                <div class="form-group">
                                    <label>Address</label>
                                    <input class="form-control" type="text" name="address" value="<?php echo $address; ?>" required/>
                                </div>
                                <div class="form-group">
                                    <label>City</label>
                                    <input class="form-control" type="text" name="city" value="<?php echo $city; ?>" required/>
                                </div>
                                <div class="form-group">
                                    <label>State/Province</label>
                                    <input class="form-control" type="text" name="state" value="<?php echo $state; ?>" required/>
                                </div>
                                <div class="form-group">
                                    <label>Country</label>
                                    <input class="form-control" type="text" name="country" value="<?php echo $country; ?>" required/>
                                </div>
                                <div class="form-group">
                                    <label>Zip/Postal Code</label>
                                    <input class="form-control" type="text" name="zip" value="<?php echo $zip; ?>" required/>
                                </div>
                                <div class="form-group">
                                    <label>Phone Number</label>
                                    <input class="form-control" type="text" name="phone" value="<?php echo $phone; ?>" required/>
                                </div>
                            <div class="form-group">
                            <label>Payment Method:</label>
                            <select name="method" id ="method" class="form-control">
                                <option value="Stripe">Credit Card</option>
                                <option value="check">Check</option>
                                <option value="free">Free</option>
                            </select><br />
                        </div>
                        <div id="creditcard_opts">
                                        <h2>Credit Card</h2>
    <?php 
                                            $cus_id = get_post_meta($org_id, 'stripe_id', true);
                                            $cards = get_customer_cards ($cus_id);
    ?>

    <?php if (!empty($cards)) { ?>
                                    <table cellpadding="5" cellspacing="0" width="90%" class="cc_cards_list">
                                        <tr>
                                            <td>&nbsp;</td>
                                            <td>Type</td>
                                            <td>Number</td>
                                            <td>Expiration</td>
                                            <td>CVC</td>
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
                                    <a href="#" id="new_card">Add new Card</a>
    <?php } ?>
                                    <div id="new_cc_form" <?php if (!empty($cards)) { ?> style="display:none;" <?php } else { ?> style="display:block;" <?php } ?> >
                                        <div class="form-group">
                                            <label>Card Number</label>
                                            <input class="form-control" type="text" size="20" autocomplete="off" name="cc_num" value="" required/>
                                        </div>
                                        <div class="form-group">
                                            <label>CVC</label>
                                            <input class="form-control" type="text" size="4" autocomplete="off" name="cc_cvc" value="" required/>
                                        </div>
                                        <div class="form-group">
                                            <label>Expiration</label>
                                            <select name="cc_mon" required class="form-control">
                                                <option value="" selected="selected">MM</option>
                                                <?php for ($i = 1 ; $i <= 12 ; $i++) { ?>
                                                    <option value="<?php if ($i < 10) {echo "0";} echo $i; ?>"><?php if ($i < 10) {echo "0";} echo $i; ?></option>
                                                <?php } ?>
                                            </select>
                                            <span> / </span>
                                            <select name="cc_yr" required class="form-control">
                                                <option value="" selected="selected">YYYY</option>
                                                <?php for ($i = date('Y') ; $i <= (date('Y') + 10) ; $i++) { ?>
                                                    <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <?php if ($cus_id) { ?><input type="hidden" name="customer_id" value="<?php echo $cus_id; ?>" /><?php } ?>
                                <input type="hidden" name="email" value="<?php echo $user_data->user_email; ?>" />
                                <input type="hidden" name="user_id" value="<?php echo $user_data->ID; ?>" />
                                <input type="hidden" name="subscription_id" value="<?= $the_subscription->ID ?>">
                                <input type="hidden" name="library_id" value="<?= $library_id ?>">
                                <input type="hidden"  id="staff_price" name="staff_price" value="">

                                <p>
                                    <i class="fa fa-lock"></i> This site uses 256-bit encryption to safeguard your credit card information.
                                </p>
                                <input value="Make Payment" id="submit_bill" type="button">
                            </form>
                            </div>	
                        <div class="processing_payment round_msgbox">
                            Attempting to charge Credit card and create the subscription... <br />
                            <i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>                          <br />
                            If you see this message for more than 15 seconds, please call 877-237-3931 for assistance.  
                        </div>

                            <script>

                                    $('#submit_bill').click( function() {
                            var button_ref = $(this);
                            $(button_ref).attr('disabled','disabled');
                            $('.processing_payment').slideDown();
                            $('#renewCamp').show();
                            var data = $('#renewCamp').serialize () + "&action=renewCamp";
                            //alert($("#total").val());
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
                                        //show_error ("SUCCESS: renewed the subscription!");
                                        window.location.href = "/renew?renewed=1";
                                    }

                                    return eot_status;
                                },
                                                    error: function(XMLHttpRequest, textStatus, errorThrown) 
                                                    {
                                    $('.processing_payment').slideUp();
                                    $(button_ref).removeAttr('disabled');
                                                            show_error( "ERROR:Unable to renew your subscription: " + textStatus + " " + errorThrown + "<br>Please contact the site administrator!");
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
                        $("#accounts").change(function(){
                            var staff_price = calculate_total($(this).val());
                            var the_total = <?= $discounted_price?>+staff_price;
                            $("#total").val(the_total);
                            $(".total").html(parseFloat(the_total).toFixed(2));

                        });
                        function calculate_price(num_staff){
                            var staff_price = calculate_total(num_staff);
                            var the_total = <?= $discounted_price ?>+staff_price;
                            $("#total").val(the_total);
                            $(".total").html(parseFloat(the_total).toFixed(2));
                        };
                        function calculate_total(num_staff)
                        {
                            console.log("calculate total: "+num_staff);
                            if (num_staff <= 99) {
                                $(".per_staff").html("$14 per staff");
                                $("#staff_price").val(num_staff * 14);
                                return num_staff * 14;
                            } else if (num_staff > 99 && num_staff <= 249) {
                                $(".per_staff").html("$13 per staff");
                                $("#staff_price").val(num_staff * 13);
                                return num_staff * 13;
                            } else {
                                $(".per_staff").html("$12 per staff");
                                $("#staff_price").val(num_staff * 12);
                                return num_staff * 12;
                            }
                        }
                        $(document).ready(function(){
                            calculate_price(<?= $num_staff;?>);
                        });
                            </script>
                </div>

            <div class="col-md-6">
                <h1>Contact Form</h1>
            </div>
                </div>
        </main>
    </div>
    <div style="clear:both"></div>

    <?php

    }
     else
    {
    ?>
    <div class="bs">
        <div class="well well-sm">Sorry we couldn't find your subscription. Please contact us M-F 9-5 ET at 1-877-390-2267.</div>
    </div>
    <?php
    }
}
?>
<?php get_sidebar('left'); ?>

<?php get_sidebar('right'); ?> 
<?php get_footer(); ?> 