<?php
class ACFRcwdUpdaterManager{

	protected $url 			= 'http://marketplace.envato.com/api/edge/';
	protected $pluginurl	= 'http://codecanyon.net/item/rcwd-upload-for-advanced-custom-fields/5078456';
	protected $author		= 'Roberto Cantarano';
	protected $requires		= '3.5.0';
	protected $tested		= '4.1';
	protected $item_id		= '5078456';

	function __construct( $current_version, $plugin_slug ){
		
		$this->current_version 	= $current_version;
		$this->plugin_slug 		= $plugin_slug;
		$t 						= explode( '/', $plugin_slug );
		$this->slug 			= str_replace( '.php', '', $t[1] );

		add_action( 'admin_init', array( &$this, 'admin_init' ), 11 );
		add_action( 'after_plugin_row_acf-rcwdupload/acf-rcwdupload.php', array( &$this, 'after_plugin_row' ), 10, 3 );
		add_filter( 'pre_set_site_transient_update_plugins', array( &$this, 'pre_set_site_transient_update_plugins' ) );
		add_filter( 'plugins_api', array( &$this, 'plugins_api' ), 10, 3 );
		
	}
	
	function admin_init(){
		
		remove_action( 'after_plugin_row_acf-rcwdupload/acf-rcwdupload.php', 'wp_plugin_update_row', 10 );
		
	}
	
	function after_plugin_row( $file, $plugin_data, $status ){

		$current = get_site_transient('update_plugins');
		
		if(!isset($current->response[$file]))
			return false;
	
		$r = $current->response[$file];

		$plugins_allowedtags 	= array('a' => array('href' => array(),'title' => array()),'abbr' => array('title' => array()),'acronym' => array('title' => array()),'code' => array(),'em' => array(),'strong' => array());
		$plugin_name 			= wp_kses( $plugin_data['Name'], $plugins_allowedtags );
		$details_url 			= self_admin_url('plugin-install.php?tab=plugin-information&plugin='.$r->slug.'&section=changelog&TB_iframe=true&width=600&height=800');
		$wp_list_table 			= _get_list_table('WP_Plugins_List_Table');
	
		if( is_network_admin() or !is_multisite() ){
			
			echo '<tr class="plugin-update-tr"><td colspan="'.$wp_list_table->get_column_count().'" class="plugin-update colspanchange"><div class="update-message">';
	
			if(!current_user_can('update_plugins'))
				printf( __( 'There is a new version of %1$s available: %2$s', 'acf-rcwdupload'), $plugin_name, $r->new_version );
			else{
			
				if(empty($r->package))
					printf( __( 'There is a new version of %1$s available: %2$s <a href="%3$s" target="_blank">Download new version from CodeCanyon.</a>', 'acf-rcwdupload' ), $plugin_name, $r->new_version, $this->pluginurl );
				else{
	
					$check 			= get_option('acf_rcwdupload_verifypurchase');
					$username		= isset($check['username']) 		? trim($check['username'] )				: '';
					$api_key		= isset($check['api_key'])			? trim($check['api_key']) 				: '';
					$purchase_code	= isset($check['purchase_code'])	? (bool)trim($check['purchase_code']) 	: false;
					
					if(empty($username) or empty($api_key) or  !$purchase_code ){
					
						echo ' <a href="'.$this->pluginurl.'" target="_blank">'.__( 'Download new version from CodeCanyon.', 'acf-rcwdupload' ).'</a>';
					
					}else{
						
						printf( __( 'There is a new version of %1$s available: %2$s <a href="%3$s">update now</a>.', 'acf-rcwdupload' ), $plugin_name, $r->new_version, wp_nonce_url( self_admin_url('update.php?action=upgrade-plugin&plugin=').$file, 'upgrade-plugin_'.$file ) );
					
					}
					
				}
	
			}
			
			echo '</div></td></tr>';

		}
		
	}
	
	function pre_set_site_transient_update_plugins($transient){

		if(empty($transient->checked))
			return $transient;

		$remote_version = $this->getRemote_version();

		if( version_compare( $this->current_version, $remote_version, '<' ) ){

			$check 			= get_option( 'acf_rcwdupload_verifypurchase' );
			$username		= isset($check['username']) 		? trim($check['username'] )				: '';
			$api_key		= isset($check['api_key'])			? trim($check['api_key']) 				: '';
			$purchase_code	= isset($check['purchase_code'])	? (bool)trim($check['purchase_code']) 	: false;

			$obj 										= new stdClass();
			$obj->slug 									= $this->slug;
			$obj->new_version 							= $remote_version;
			$obj->name 									= ACF_RCWDUPLOAD_TITLE;
			$obj->url 									= '';
			$obj->package 								= '';
																			
			if( !empty($username) and !empty($api_key) and $purchase_code ){

				$url 	= $this->url.$username.'/'.$api_key.'/download-purchase:'.$purchase_code.'.json';
				$json 	= wp_remote_get($url);
				$result = json_decode( $json['body'], true );

				if( isset($result['download-purchase']['download_url']) and !empty($result['download-purchase']['download_url']) ){
					
					$url 										= $result['download-purchase']['download_url'];
					$obj->url 									= $url;
					$obj->package 								= $url;
					$transient->response[$this->plugin_slug] 	= $obj;

				}
							
			}
			
		}

		return $transient;
				
	}

	function plugins_api( $false, $action, $arg ){

		if( isset($arg->slug) and $arg->slug === $this->slug ){
		
			$api                = new stdClass();
			$api->slug          = $this->slug;
			$api->name          = ACF_RCWDUPLOAD_TITLE;
			$api->version       = $this->getRemote_version();
			$api->author       	= $this->author;
			$api->homepage 		= $this->pluginurl;
			$api->requires 		= $this->requires;
			$api->tested 		= $this->tested;
			$api->sections 		= array(
				
				'description' => sprintf( __( 'All info about the plugin here, <a href="%s" target="_blank">click me :)</a>', 'acf-rcwdupload' ), $this->pluginurl ) 
		
			);
					
			return $api;
			
		}
		
		return $false;
				
	}

	public function getRemote_version(){

		$item_id	= $this->item_id;
		$API 		= new RcwdAcfUploadEnvatoAPI();

		$API->set_api_set('item:'.$item_id);
			
		$data 			= $API->request();
		$item			= $data['item'];
		$last_update	= strtotime($item['last_update']);
		$tags			= str_replace( ' ', '', $item['tags'] );
		$version		= 0;
		
		if(!empty($tags)){
			
			$tags = explode( ',', $tags );
			
			foreach($tags as $tag){
				
				if(substr( $tag, 0, 7 ) == 'version' ){
					
					$version = str_replace( 'version', '', $tag );
					
					break;	
					
				}
				
			}
		
			return $version;
			
		}

		return false;
		
	}
			
}
?>