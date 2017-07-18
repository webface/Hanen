<?php
class ACFRcwdUpdater{
	
	public function init(){
		
		add_filter( 'upgrader_pre_download', array( $this, 'upgrader_pre_download'), 10, 4 );
		add_action( 'upgrader_process_complete', array( $this, 'removeTemporaryDir' ) );

	}
	
	function upgrader_pre_download( $reply, $package, $updater ){

		global $wp_filesystem;

		$error = __( 'Error! Envato username, api key and your purchase code are required for downloading updates from Envato marketplace.', 'acf-rcwdupload' );

		if(empty($package))
			return new WP_Error( 'no_credentials', $error );
						
		if( ( isset($updater->skin->plugin) and $updater->skin->plugin === ACF_RCWDUPLOAD_NAME ) or ( isset($updater->skin->plugin_info) and $updater->skin->plugin_info['Name'] === ACF_RCWDUPLOAD_TITLE ) ){
			
			$updater->strings['download_envato'] = __( 'Downloading package from envato market...', 'acf-rcwdupload' );

			$updater->skin->feedback('download_envato');
			
			$package_filename 	= 'acf-rcwdupload.zip';
			$res 				= $updater->fs_connect(array(WP_CONTENT_DIR));
			
			if(!$res)
				return new WP_Error( 'no_credentials', __( "Error! Can't connect to filesystem", 'acf-rcwdupload' ) );

			$check			= get_option('acf_rcwdupload_verifypurchase');
			$username		= isset($check['username']) 		? trim($check['username'] )				: '';
			$api_key		= isset($check['api_key'])			? trim($check['api_key']) 				: '';
			$purchase_code	= isset($check['purchase_code'])	? (bool)trim($check['purchase_code']) 	: false;
			
			if( empty($username) or empty($api_key) or empty($purchase_code) )
				return new WP_Error( 'no_credentials', $error );

			if(empty($package))
				return new WP_Error( 'no_credentials', __( 'Error! Envato API error' . ( isset( $result['error'] ) ? ': ' . $result['error'] : '.' ), 'acf-rcwdupload' ) );
			$download_file = download_url($package);
			
			if(is_wp_error($download_file))
				return $download_file;

			$plugin_directory_name = 'acf-rcwdupload';
	
			if(basename( $download_file, '.zip' ) !== $plugin_directory_name){
				
				$new_archive_name = dirname($download_file).'/'.$plugin_directory_name.'.zip';

				rename( $download_file, $new_archive_name );
				
				$download_file = $new_archive_name;
				
				return $download_file;
				
			}

			return new WP_Error( 'no_credentials', __( 'Error on unzipping package', 'acf-rcwdupload' ) );
			
		}
		
		return $reply;
				
	}

	public function removeTemporaryDir(){
		
		global $wp_filesystem;
		
		if(is_dir($wp_filesystem->wp_content_dir().'uploads/acfrcwdupload_envato_package'))
			$wp_filesystem->delete( $wp_filesystem->wp_content_dir().'uploads/acfrcwdupload_envato_package', true );

	}
			
}
?>