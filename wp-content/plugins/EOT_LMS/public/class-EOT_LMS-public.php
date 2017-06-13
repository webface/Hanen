<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.expertonlinetraining.com
 * @since      1.0.0
 *
 * @package    EOT_LMS
 * @subpackage EOT_LMS/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    EOT_LMS
 * @subpackage EOT_LMS/public
 * @author     Your Name <email@example.com>
 */
class EOT_LMS_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $EOT_LMS    The ID of this plugin.
	 */
	private $EOT_LMS;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $EOT_LMS       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $EOT_LMS, $version ) {

		$this->EOT_LMS = $EOT_LMS;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in EOT_LMS_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The EOT_LMS_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->EOT_LMS, plugin_dir_url( __FILE__ ) . 'css/EOT_LMS-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in EOT_LMS_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The EOT_LMS_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->EOT_LMS, plugin_dir_url( __FILE__ ) . 'js/EOT_LMS-public.js', array( 'jquery' ), $this->version, false );

		// in our JavaScript, object properties are accessed as ajax_object.ajax_url
		$data = array( 
						'ajax_url' => admin_url( 'admin-ajax.php' ), // Link to the ajax calls
						'template_url' => get_template_directory_uri() // Link to the template directory. Accessible via PHP and Jquery

		);

		wp_localize_script( $this->EOT_LMS, 'ajax_object', $data );
	}

	/**
	 * This function is will load the required template part wherever the shortcode is positioned.
	 * https://github.com/DevinVinson/WordPress-Plugin-Boilerplate/issues/131#issuecomment-75332338
	 * Usage in theme files: echo do_shortcode( '[load_template_part]' );
	 */
	
	 public function register_shortcodes(){

	 	 function load_template_part_shortcode($atts) {

	 		extract( shortcode_atts( array(
	 	         //your options
	 	 	), $atts ) );

	 	 	$templates = new Gamajo_Template_Loader;

	 	 	ob_start();  // output buffer is required for short codes

	 	   if ( !empty( get_query_var('part') ) ) {
	 	   	$getpart = sanitize_text_field ( get_query_var('part') ) ;
	 	   } else {
	 	   		$getpart = 'default';
	 	   }

	 	   // Since the load parameters is set to false, this will return the path to the
	 	   // template part if a file has been found, otherwise it will return false.
	 	   if (FALSE === $templates->get_template_part( 'part', $getpart, $load = false ) ) {
	 	 		$getpart = 'default';
	 	 	}

	 	 	$templates->get_template_part( 'part', $getpart, true );

	 	 	return ob_get_clean();
	 	 }

      add_shortcode( 'load_template_part', 'load_template_part_shortcode' );

    }

	/**
	 * [custom_query_vars] and [custom_rewrite_endpoint]
	 * Register custom query params with pretty urls
	 * http://infoheap.com/wordpress-custom-query-params-and-pretty-url/
	 * NOTE: Will not work on Front Page: https://core.trac.wordpress.org/ticket/31438
	 */
    function custom_query_vars($vars) {
      $vars[] = "part";
      return $vars;
    }
    
    function custom_rewrite_endpoint(){
      // See Available Places:
      // https://codex.wordpress.org/Rewrite_API/add_rewrite_endpoint#Available_Places
      add_rewrite_endpoint( 'part', EP_ALL );
    }


}
