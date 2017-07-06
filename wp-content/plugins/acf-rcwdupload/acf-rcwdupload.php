<?php
/*
Plugin Name: Rcwd Upload for Advanced Custom Fields
Plugin URI: 
Description: the Rcwd Upload field let you upload with no use of wp media upload.
Version: 1.2.0.8
Author: Roberto Cantarano
Author URI: http://www.cantarano.com
License: You should have purchased a license from http://codecanyon.net/ plugin download page
Copyright: 2013 Roberto Cantarano (email : roberto@cantarano.com) 
*/

// UNCOMMENT THIS BLOCK ONLY FOR DEBUG

/* 
ini_set('display_errors', '1');
ini_set('error_reporting', E_ALL);
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('Nice try :)'); }

require_once('updater.php');
require_once('updater-manager.php');
require_once('commons.php');

if(is_admin())
	include_once('envato/envato.api.class.php');
	
class acf_rcwdupload_plugin{

	public $version = '1.2.0.8';
	
	function __construct(){

		if(!defined('ACF_RCWDUPLOAD_NAME'))
			define( 'ACF_RCWDUPLOAD_NAME', 'acf-rcwdupload/acf-rcwdupload.php' );
		
		$settings = array(
		
			'version' 	=> $this->version,
			'basename' 	=> plugin_basename(__FILE__)
			
		);
		
		register_activation_hook( $settings['basename'], array( $this, 'activate' ) );
		register_deactivation_hook( $settings['basename'], array( $this, 'deactivate' ) );

		$v = get_site_option('acf_rcwdupload_version');
		
		if(empty($v)){

			update_site_option( 'acf_rcwdupload_version', $this->version );
		
		}
		
		add_action( 'generate_rewrite_rules', array( 'ACFRcwdCommon', 'generate_rewrite_rules' ) );

		// version 5+ 
		add_action('acf/include_field_types', array($this, 'include_field_types' ));
		
		// version 4+ 
		add_action('acf/register_fields', array($this, 'register_fields'));

		// version 3-
		add_action( 'init', array( $this, 'init' )); 

		add_action( 'ACFRcwdUploadCheckit', array( 'ACFRcwdCommon', 'checkit' )); 

		if(is_admin()){
			
			$updater = new ACFRcwdUpdater();
			
			$updater->init();
			
			new ACFRcwdUpdaterManager( $settings['version'], ACF_RCWDUPLOAD_NAME );	
		
		}
										
	}
		
	function activate(){ 

		update_site_option( 'acf_rcwdupload_version', $this->version );

		wp_schedule_event( time(), 'daily', 'ACFRcwdUploadCheckit' );
		
		ACFRcwdCommon::flush_rules();	
		
	}

	function deactivate(){
		
		wp_clear_scheduled_hook('ACFRcwdUploadCheckit');
		
	}
	
	function init(){
		
		if(function_exists('register_field')){ 
		
			register_field('acf_field_location', dirname(__File__) . '/rcwdupload-v3.php');
			
		}	
			
	}
	
	function register_fields(){
		
		include_once('rcwdupload-v4.php');
		
	}

	function include_field_types(){
		
		include_once('rcwdupload-v5.php');
		
	}
	
}

new acf_rcwdupload_plugin();
?>