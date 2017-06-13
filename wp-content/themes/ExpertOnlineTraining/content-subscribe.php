
<h1 class="article_page_title">Subscribe</h1>
<?php if (!is_user_logged_in ()) { ?>
<div class="call_to_action">You must have a <a href="<?php bloginfo ('url'); ?>/register/">Director Account</a> to subcribe.</div>
    <hr>
<?php } ?>
<h1 id="prices_le">Prices - Leadership Essentials <?= SUBSCRIPTION_YEAR ?></h1>
<p>
    Subscription prices are based on the number of staff you will train and the number of videos you wish to have access to. A Complete subscription gives you <b>unlimited access</b> to all <a href="/camp-staff-training-videos.html" title=""><?= NUM_VIDEOS ?></a> modules and related resources through <?= SUBSCRIPTION_YEAR ?> while the Limited subscription only gives you access to <b><?= NUM_VIDEOS_LEL ?> out of <?= NUM_VIDEOS_MAX_LEL ?> core modules</b>. 
</p>
<table>
    <tbody>
        <tr>
            <td>
                <table class="libraryprices">
                    <tbody>
                        <tr>
                            <td class="range heading">
                                <b>No. of Staff</b>
                            </td>
                            <td class="price heading">
                                <b>Price (USD)</b>
                            </td>
                        </tr>
                        <tr>
                        <td class="range">
                            <b>Limited Membership</b><br><i>Access to <?= NUM_VIDEOS_LEL ?> out of <?= NUM_VIDEOS_MAX_LEL ?> core modules</i>
                        </td>
                        <td class="price">
                            $ 199.00 per year
                          </td>
                        </tr>
                        <tr>
                        <td class="range">
                            <b>Complete Membership</b><br><i>Unlimited access to <?= NUM_VIDEOS ?> modules</i>
                        </td>
                        <td class="price">
                            $ <?= number_format((float)LE_PRICE, 2, '.', '') ?> per year
                          </td>
                        </tr>
                            <tr>
                                <td class="">
                                    <i>Plus Staff accounts:</i>
                                </td>
                                <td>
                                    <center>+</center>
                                </td>
                            </tr>
                            <tr>
                                <td class="range">
                                    &nbsp;&nbsp;&nbsp;&nbsp;Total Staff: 1 - 99
                                </td>
                                <td class="price">
                                    $ 14.00 per Staff
                                </td>
                            </tr>
                            <tr>
                                <td class="range">
                                    &nbsp;&nbsp;&nbsp;&nbsp;Total Staff: 100 - 249
                                </td>
                                <td class="price">
                                    $ 13.00 per Staff
                                </td>
                            </tr>
                        <tr>
                        <td class="range">
                            &nbsp;&nbsp;&nbsp;&nbsp;Total Staff: 250+
                        </td>
                        <td class="price">
                            $ 12.00 per Staff
                          </td>
                        </tr>
                        <tr>
                        <td class="range">
                            &nbsp;&nbsp;&nbsp;&nbsp;USB Data Drive
                        </td>
                        <td class="price">
                            $ 49.00
                          </td>
                        </tr>
                    </tbody>
                </table>
                <br>
            </td>
            <td>
                <img src="<?php bloginfo ('stylesheet_directory'); ?>/images/le-logo.jpg" style="margin-left:20px;" alt="Leadership Essentials">
            </td>
        </tr>
    </tbody>
</table>
<hr>
<h1 id="prices_se">Prices - Safety Essentials <?= SUBSCRIPTION_YEAR ?></h1>
<table>
    <tbody>
        <tr>
            <td>
                <table class="libraryprices">
                    <tbody>
                        <tr>
                            <td class="range heading">
                                <b>No. of Staff</b>
                            </td>
                            <td class="price heading">
                                <b>Price (USD)</b>
                            </td>
                        </tr>
                        <tr>
                            <td class="range">
                                <b>Base Membership</b>
                            </td>
                            <td class="price">
                                $ <?= number_format((float)SE_PRICE, 2, '.', '') ?> per year
                            </td>
                        </tr>
                        <tr>
                            <td class="">
                                <i>Plus Staff accounts:</i>
                            </td>
                            <td>
                                <center>+</center>
                            </td>
                        </tr>
                        <tr>
                            <td class="range">
                                &nbsp;&nbsp;&nbsp;&nbsp;Accounts 1 - 20
                            </td>
                            <td class="price">
                                <center><i>(Included)</i></center>
                            </td>
                        </tr>
                        <tr>
                            <td class="range">
                                &nbsp;&nbsp;&nbsp;&nbsp;Accounts 21+
                            </td>
                            <td class="price">
                                $ 12.00 per Staff
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
            <td>
                <img src="<?php bloginfo ('stylesheet_directory'); ?>/images/se-logo.jpg" style="margin-left:20px;" alt="Safety Essentials">
            </td>
        </tr>
    </tbody>
</table>
<br>    
<hr>
<h1 id="prices_ce">Prices - Clinical Essentials <?= SUBSCRIPTION_YEAR ?></h1>
<table>
    <tbody>
        <tr>
            <td>
                <table class="libraryprices">
                    <tbody>
                        <tr>
                            <td class="range heading">
                                <b>No. of Staff</b>
                            </td>
                            <td class="price heading">
                                <b>Price (USD)</b>
                            </td>
                        </tr>
                        <tr>
                            <td class="range">
                                <b>Base Membership</b>
                            </td>
                            <td class="price">
                                $ <?= number_format((float)CE_PRICE, 2, '.', '') ?> per year
                            </td>
                        </tr>
                        <tr>
                            <td class="">
                                <i>Plus Staff accounts:</i>
                            </td>
                            <td>
                                <center>+</center>
                            </td>
                        </tr>
                        <tr>
                            <td class="range">
                                &nbsp;&nbsp;&nbsp;&nbsp;Accounts 0-12
                            </td>
                            <td class="price">
                                <center><i>(Included)</i></center>
                            </td>
                        </tr>
                        <tr>
                            <td class="range">
                                &nbsp;&nbsp;&nbsp;&nbsp;Accounts 13+
                            </td>
                            <td class="price">
                                $ 10.00 per Staff
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
            <td>
                <img src="<?php bloginfo ('stylesheet_directory'); ?>/images/ce-logo.jpg" style="margin-left:20px;" alt="Clinical Essentials">
            </td>
        </tr>
    </tbody>
</table>
<br>
<br>
