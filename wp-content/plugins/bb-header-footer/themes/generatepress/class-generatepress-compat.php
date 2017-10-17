<?php
/**
 * GeneratepressCompatibility.
 *
 * @package  bb-header-footer
 */

/**
 * GeneratePress_Compat setup
 *
 * @since 1.0
 */
class GeneratePress_Compat {

	/**
	 * Instance of GeneratePress_Compat
	 *
	 * @var GeneratePress_Compat
	 */
	private static $instance;

	/**
	 *  Initiator
	 */
	public static function instance() {

		if ( ! isset( self::$instance ) ) {
			self::$instance = new GeneratePress_Compat();

			add_action( 'wp', array( self::instance(), 'hooks' ) );
		}

		return self::$instance;
	}

	/**
	 * Run all the Actions / Filters.
	 */
	public function hooks() {

		$header_id = BB_Header_Footer::get_settings( 'bb_header_id', '' );
		$footer_id = BB_Header_Footer::get_settings( 'bb_footer_id', '' );

		if ( '' !== $header_id ) {
			add_action( 'template_redirect', array( $this, 'generatepress_setup_header' ), 10 );
			add_action( 'generate_header', array( $this, 'get_header_content' ) );
		}

		if ( '' !== $footer_id ) {
			add_action( 'template_redirect', array( $this, 'generatepress_setup_footer' ), 10 );
			add_action( 'generate_footer', array( $this, 'get_footer_content' ) );
		}

	}

	/**
	 * Disable header from the theme.
	 */
	public function generatepress_setup_header() {
		remove_action( 'generate_header', 'generate_construct_header' );
	}

	/**
	 * Disable footer from the theme.
	 */
	public function generatepress_setup_footer() {
		remove_action( 'generate_footer', 'generate_construct_footer_widgets', 5 );
		remove_action( 'generate_footer','generate_construct_footer' );
	}

	/**
	 * Display header markup for beaver builder theme.
	 */
	public function get_header_content() {

		?>
			<header id="masthead" itemscope="itemscope" itemtype="http://schema.org/WPHeader">
				<p class="main-title bhf-hidden" itemprop="headline"><a href="<?php echo bloginfo( 'url' ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></p>
				<?php BB_Header_Footer::get_header_content(); ?>
			</header>
		<?php
	}

	/**
	 * Display footer markup for beaver builder theme.
	 */
	public function get_footer_content() {
		?>
		
		<footer class="ehf-site-info" itemtype="http://schema.org/WPFooter" itemscope="itemscope">
			<?php BB_Header_Footer::get_footer_content(); ?>
		</footer>

		<?php
	}

}

GeneratePress_Compat::instance();
