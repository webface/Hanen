<style type="text/css">
    table.data {
        font-size: 14px;
    }

    #main p {
        font-size: 15px;
    }
</style>

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

    // make sure its not after oct 20thy
    $today = date('Y-m-d');
    $deadline = '2017-10-20';
    $deadline_passed = 0; // boolean weather the deadline passed or not.
    if (strtotime($today) > strtotime($deadline))
    {
        $deadline_passed = 1;
    }

    if (isset($_REQUEST['renewed']))
    {
?>
    <br/>
    <div class="bs">
        <div class="container">
         <div class="well">Success! Your Subscription has been renewed.</div>
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
<?php
            if ($deadline_passed)
            {
                echo '<h1 class="article-title">Renew your Leadership Essentials Subscription</h1>';
            }
            else
            {
                echo '<h1 class="article-title">Instantly save $100 off your 2018 Leadership Essentials Dashboard Price</h1>';
            }
?>
            
            <h3>2017 <?= $library->name; ?> Subscription Details:</h3>
            <table class="data sm">
                <tbody>
                    <tr>
                        <td class="label">
                            Director Dashboard Price
                        </td>
                        <td class="value">
                            $<?= number_format($the_subscription->dash_price, 2, ".", ""); ?>          
                        </td>
                    </tr>
<?php  
                if($the_subscription->dash_discount > 0)
                {
?>
                    <tr>
                        <td class="label">
                            Dashboard Discount
                        </td>
                        <td class="value">
                            $<?= number_format($the_subscription->dash_discount, 2, ".", "") ?>         
                        </td>
                    </tr>
<?php 
                } 
?>
                    <tr>
                        <td class="label">
                                # of Staff
                        </td>
                        <td class="value">
                             <?= $the_subscription->staff_credits; ?>          
                        </td>
                    </tr>
<?php  
                if($the_subscription->staff_discount > 0)
                {
?>
                    <tr>
                        <td class="label">
                            Staff Discount
                        </td>
                        <td class="value">
                            $<?= number_format($the_subscription->staff_discount, 2, ".", "") ?>         
                        </td>
                    </tr>
<?php 
                } 
?>
                    <tr>
                        <td class="label">
                            Total Staff Price
                        </td>
                        <td class="value">
                            $<?= number_format($the_subscription->staff_price, 2, ".", "") ?>         
                        </td>
                    </tr>

                    <tr>
                        <td class="label">
<?php
                if ($upgrades)
                {
                    echo "Subtotal 2017";
                }
                else
                {
                    echo "Total 2017 Price";
                }
?>
                        </td>
                        <td class="value">
                            $<?= number_format($the_subscription->price, 2, ".", "") ?>         
                        </td>
                    </tr>
<?php
                // Display upgrade history if the director had an upgrade.
                if($upgrades)
                {
                    $total = $the_subscription->price;
?>
                    <tr>
                        <td class="label">
                            Account Upgrades
                        </td>
                        <td class="value">
                            <table class="data sm">  
                                <tbody>
                                    <tr>
                                        <th>Date</th>
                                        <th># Accounts</th>
                                        <th>Price</th>
                                    </tr>
<?php
                                foreach($upgrades as $upgrade)
                                {
                                    $total += $upgrade->price;
?>
                                    <tr>
                                        <td><?= $upgrade->date; ?></td>
                                        <td align="center"><?= $upgrade->accounts; ?></td>
                                        <td>$<?= number_format($upgrade->price, 2, '.', ''); ?></td>
                                    </tr>
<?php
                                }
?>
                                </tbody>
                            </table>       
                        </td>
                    </tr>
                    <tr>
                        <td class="label">
                            Total 2017 Price
                        </td>
                        <td class="value">
                            $<?= number_format($total, 2, ".", "") ?>         
                        </td>
                    </tr>
<?php
                }
?>
                </tbody>
            </table>
<?php
            } // end if library
?>

            <h3>2018 Subscription Renewal Options</h3>

            <p>The regular price of a full-featured Directors’ Dashboard has been $399 per year since we opened shop in 2008. Since then, we have grown the Leadership Essentials library from 12 videos to 120 videos, added greater functionality to our site, and made it mobile-friendly. <strong>And we still charge only $399 for the Directors’ Dashboard.</strong></p>
<?php
            if (!$deadline_passed)
            {
                echo '<p>However, if you renew your subscription prior to ' . $deadline . ' you will receive $100 off of our Directors\' Dashboard price.</p>';
            }
?>
            

            <div class="row bs">
                <div class="col-md-6">
                    <div id="bill_form">
                        <form id="renewCamp">
<?php 
                        if($library_id == 1 && $deadline_passed == 0)
                        { 
?>
                            <div class="form-group">
                                <label>Regular Price:<strike style="color:red">$<?= $library->price ?></strike> <span style="color:green">$<?= $discounted_price ?></span> (-$100 discount for early renewal) </label>
                            </div>
<?php 
                        }
                        else
                        {
?>
                            <div class="form-group">
                                <label>Regular Price:<span style="color:green">$<?= $library->price ?></span></label>
                            </div>        
<?php 
                        } 
?>
                            <div class="form-group">
                                <label for="accounts" style=""># of Additional Staff Accounts:</label>
                                <input id="accounts" style="width:50%" class="form-control" type="number" name="accounts" value="<?= $num_staff?>" min="0"/><br>* This is the number of staff you had last year. If you would prefer to pay for your staff account at a later date, change this number to 0.
                            </div>

                            <table class="data sm">  
                                <tbody>
                                    <tr>
                                        <td><label for="dashboard">Dashboard </label></td>
                                        <td>$<span style="color:green" class="dashboard"><?= $discounted_price ?></span></td>
                                    </tr>
                                    <tr>
                                        <td><label for="staff_price">Staff </label></td>
                                        <td>$<span style="color:green" class="staff_price"></span>&nbsp;&nbsp;<span style="font-size:10px" class="per_staff"></span></td>
                                    </tr>
                                    <tr>
                                        <td><label for="total">Total 2018 </label></td>
                                        <td>$<span style="color:green" class="total">00.00</span></td>
                                    </tr>
                                </tbody>
                                <input style="width:50%" class="form-control" type="hidden" name="total" id="total" value=""/>
                            </table>
        

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
                                <label>Auto-Renew</label><br>
                                <input type="checkbox" name="auto-renew" value="1" checked> Yes, please auto-renew my subscription next year.
                            </div>

                        <div class="form-group">
                        <label>Payment Method:</label>
                        <select name="method" id ="method" class="form-control">
                            <option value="Stripe">Credit Card</option>
                        </select><br />
                        <sub>* if you would like to pay by check please contact us at 1-877-390-2267 M-F 9-5 ET.</sub>
                    </div>
                    <p>&nbsp;</p>
                    <div id="creditcard_opts">
                        <h2>Credit Card</h2>
<?php 
                        $cus_id = get_post_meta($org_id, 'stripe_id', true);
                        $cards = get_customer_cards ($cus_id);
                        
                        if (!empty($cards)) 
                        { 
?>
                        <table cellpadding="5" cellspacing="0" width="90%" class="cc_cards_list">
                            <tr>
                                <td>&nbsp;</td>
                                <td>Type</td>
                                <td>Number</td>
                                <td>Expiration</td>
                                <td>CVC</td>
                            </tr>
<?php 
                            foreach ($cards as $card) 
                            { 
?>
                                <tr>
                                    <td><input type="radio" name="cc_card" value="<?php echo $card->id; ?>" /></td>
                                    <td><?php echo $card->brand; ?></td>
                                    <td>**** **** **** <?php echo $card->last4; ?></td>
                                    <td><?php echo $card->exp_month; ?> / <?php echo $card->exp_year; ?></td>
                                    <td>***</td>
                                </tr>
<?php 
                            } 
?>
                        </table>
                        <a href="#" id="new_card">Add new Card</a>
<?php 
                        } 
?>
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
                If you see this message for more than 15 seconds, please call 1-877-390-2267 for assistance.  
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
                                window.location.href = "/renewal/?renewed=1";
                            }

                            return eot_status;
                        },
                        error: function(XMLHttpRequest, textStatus, errorThrown) 
                        {
                            $('.processing_payment').slideUp();
                            $(button_ref).removeAttr('disabled');
                            show_error( "ERROR:Unable to renew your subscription: " + textStatus + " " + errorThrown + "<br>Please contact us at 1-877-390-2267 M-F 9-5 ET");
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
                    $(".staff_price").html(parseFloat(staff_price).toFixed(2));
                    $("#total").val(the_total);
                    $(".total").html(parseFloat(the_total).toFixed(2));

                });
                
                function calculate_price(num_staff){
                    var staff_price = calculate_total(num_staff);
                    var the_total = <?= $discounted_price ?>+staff_price;
                    $(".staff_price").html(parseFloat(staff_price).toFixed(2));
                    $("#total").val(the_total);
                    $(".total").html(parseFloat(the_total).toFixed(2));
                };
                function calculate_total(num_staff)
                {
//console.log("calculate total: "+num_staff);
                    if (num_staff <= 99) {
                        $(".per_staff").html(num_staff + " @ $14 per staff");
                        $("#staff_price").val(num_staff * 14);
                        return num_staff * 14;
                    } else if (num_staff > 99 && num_staff <= 249) {
                        $(".per_staff").html(num_staff + " @ $13 per staff");
                        $("#staff_price").val(num_staff * 13);
                        return num_staff * 13;
                    } else {
                        $(".per_staff").html(num_staff + " @ $12 per staff");
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
            <h2>Contact Us</h2>
            <?= do_shortcode( '[gravityform id="7" title="false" description="true"]' ); ?>            
        </div>
            
            </div>
        </main>
    </div>
<div style="clear:both"></div>



<?php
        } // end if subscription
        else
        {
?>
            <div class="bs">
                <div class="well well-sm">Sorry we couldn't find your subscription. Please contact us M-F 9-5 ET at 1-877-390-2267.</div>
            </div>            
<?php
        }
    } // end else if not renewed

    get_sidebar('left'); 
    get_sidebar('right');  
    get_footer();  
?>