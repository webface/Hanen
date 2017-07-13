<?php
$check 			= get_option('acf_rcwdupload_verifypurchase');
$username		= isset($check['username']) 		? trim($check['username'] )		: '';
$purchase_code	= isset($check['purchase_code'])	? trim($check['purchase_code']) : '';
$api_key		= isset($check['api_key'])			? trim($check['api_key']) 		: '';
$msgis			= '';
$msg			= '';

if(isset($_POST['submit'])){
	
	$username 		= trim($_POST['username']);
	$purchase_code	= trim($_POST['purchase_code']);
	$api_key		= trim($_POST['api_key']);

	if( !empty($username) and !empty($purchase_code) ){

		update_option( 'acf_rcwdupload_verifypurchase', array(
		
			'username' 		=> $username,
			'purchase_code' => $purchase_code,
			'api_key' 		=> $api_key
			
		) );
				
		if(ACFRcwdCommon::serial_is_valid(true))
			$msgis = 'welldone';
		else
			$msgis = 'error';
			
	}else
		$msgis = 'error';
	
}else{
	
	if(is_array($check)){
		 
		if(ACFRcwdCommon::serial_is_valid(true)){
	
			$msgis = 'welldone';
			
		}else{
			
			$msgis = 'error';
			
		}
	
	}

}

if($msgis == 'welldone')
	$msg = '<p class="welldone">'.__( 'Well Done, all data is correct! The item is properly registered.', 'acf-rcwdupload' ).'</p>';	
elseif($msgis == 'error')
	$msg = '<p class="error">'.__( 'Sorry, your data is invalid. Please insert valid data.', 'acf-rcwdupload' ).'</p>';
?>
<style>
#acf-rcwdupload-checkpage p.welldone{		color:#090;
										font-size:14px;
										font-weight:bold;
}
#acf-rcwdupload-checkpage p.error{		color:#ff0000;
										font-size:14px;
										font-weight:bold;
}
#acf-rcwdupload-checkform table input{	font-size:14px;
										letter-spacing:1px;
}
</style>

<div id="acf-rcwdupload-checkpage" class="wrap">

	<h2><?php _e( 'Rcwd Upload: an add-on for Advanced Custom Fields plugin', 'acf-rcwdupload' ) ?></h2>
	<h3><?php _e( '&rarr; Purchase verification', 'acf-rcwdupload' ) ?></h3>

<?php
	if($msg != '')
		echo $msg;
?>
    <form id="acf-rcwdupload-checkform" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
        <table class="form-table">
            <tbody>
                <tr valign="top">
                    <th scope="row"><label><?php _e( 'Marketplace Username:', 'acf-rcwdupload' ) ?></label></th>
                    <td><input type="text" name="username" class="regular-text" value="<?php echo $username ?>" />
                    <br />
                    <?php _e( 'Insert your Envato account username.', 'acf-rcwdupload' ) ?>
                    </td>
                </tr>			
                <tr valign="top">
                    <th scope="row"><label><?php _e( 'Purchase code:', 'acf-rcwdupload' ) ?></label></th>
                    <td><input type="text" name="purchase_code" class="regular-text" value="<?php echo $purchase_code ?>" />
                    <br />
                    <?php printf( __( 'Insert the Purchase code for this item <a href="%1s" target="_blank">(click here for help)</a>.', 'acf-rcwdupload' ), ACFRcwdCommon::get_dir(__FILE__).'img/getitempc.jpg' ) ?></td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label><?php _e( 'Secret API Key:', 'acf-rcwdupload' ) ?></label></th>
                    <td><input type="password" name="api_key" class="regular-text" value="<?php echo $api_key ?>" />
                    <br />
                    <?php printf( __( 'You can find API key by visiting your Envato Account page, then clicking the My Settings tab.<br />At the bottom of the page you\'ll find your account\'s API key.<br />By filling this field, you will enable <strong>automatic updates</strong>.', 'acf-rcwdupload' ), ACFRcwdCommon::get_dir(__FILE__).'img/getitempc.jpg' ) ?></td>
                </tr>                		
            </tbody>
        </table>
        <p class="submit"><input class="button button-primary" type="submit" name="submit" value="<?php _e( 'Verify', 'acf-rcwdupload' ) ?>" /></p>
    </form>

</div>

<div>
    <h3><?php _e( 'Permalink structure bug', 'acf-rcwdupload' ) ?></h3>
    <p style="background:#fff;padding:20px;">
<?php 
        _e( 'You click the linked file in notification mail or in entry detail, and it opens the 404 page?<br />Please open the readme file and check the f.a.q. section in order to fix it ;)<br /><br />If this will not fix it, please send an email to <a href="mailto:roberto@cantarano.com">roberto@cantarano.com</a> with your htaccess file attached and all info about your server type (windows or linux), your license code and plugin name.', 'acf-rcwdupload' );
		echo '<br />';
        _e( 'Anyway you can manually edit the htaccess file and add those 2 lines after the line "RewriteRule ^index\.php$ - [L]":', 'acf-rcwdupload' );
?> 
<br /><br />
        RewriteRule ^acfrcwdup/([^/\.]*)/[^/\.]+/([^/\.]+)/([^/]+)/([^/]+)/([^/]+)(\.[^.]*$|$)?$ //?mode=$1&acfrcwduplddwnlod=$2&type=$3&value=$4&file=$5 [QSA,L]
    </p>
</div>