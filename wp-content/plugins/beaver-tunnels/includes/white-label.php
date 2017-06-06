<?php
/**
 * Beaver Tunnels White Label
 *
 * @package Beaver_Tunnels
 * @since 1.2.0
 */

/**
 * Beaver Tunnels White Label class
 */
class Beaver_Tunnels_White_Label {

	/**
	 * Construct
	 *
	 * @since 1.2.0
	 */
	public function __construct() {
		add_filter( 'all_plugins', array( $this, 'plugins_page' ) );
	}

	/**
	 * Get Beaver Tunnels branding
	 *
	 * @since 1.2.0
	 *
	 * @return string
	 */
	static public function get_branding() {

		if ( ! class_exists( 'FLBuilderWhiteLabel' ) ) {
			return __( 'Beaver Tunnels', 'beaver-tunnels' );
		}

		$fl_builder_branding = FLBuilderWhiteLabel::get_branding();
		$fl_builder_default  = __( 'Page Builder', 'fl-builder' );

		if ( $fl_builder_branding !== $fl_builder_default ) {
			return $fl_builder_branding . ' ' . __( 'Hooks', 'beaver-tunnels' );
		}

		return __( 'Beaver Tunnels', 'beaver-tunnels' );

	}

	/**
	 * Get Beaver Builder branding
	 *
	 * @since
	 *
	 * @return [type] [description]
	 */
	static public function get_bb_branding() {

		if ( ! class_exists( 'FLBuilderWhiteLabel' ) ) {
			return __( 'Page Builder', 'fl-builder' );
		}

		return FLBuilderWhiteLabel::get_branding();

	}

	/**
	 * Get Beaver Builder Theme branding
	 *
	 * @since 2.0
	 *
	 * @return string
	 */
	static public function get_bb_theme_branding() {

		if ( ! class_exists( 'FLBuilderWhiteLabel' ) ) {
			return __( 'Beaver Builder Theme', 'beaver-tunnels' );
		}

		$fl_builder_theme_branding = FLBuilderWhiteLabel::get_theme_branding();
		if ( is_array( $fl_builder_theme_branding ) && isset( $fl_builder_theme_branding['name'] ) && strlen( $fl_builder_theme_branding['name'] ) > 0 ) {
			return $fl_builder_theme_branding['name'];
		}

		return __( 'Beaver Builder Theme', 'beaver-tunnels' );

	}

	/**
	 * White Label the plugins page
	 *
	 * @since 1.2.0
	 *
	 * @param  array $plugins The installed plugins.
	 *
	 * @return array
	 */
	public function plugins_page( $plugins ) {

		if ( ! class_exists( 'FLBuilderWhiteLabel' ) ) {
			return $plugins;
		}

		$default  = __( 'Page Builder', 'fl-builder' );
		$branding = FLBuilderWhiteLabel::get_branding();
		$key	  = plugin_basename( BEAVER_TUNNELS_PLUGIN_FILE );

		if ( isset( $plugins[ $key ] ) && $branding !== $default ) {
			$plugins[ $key ]['Name']	   = $this->get_branding();
			$plugins[ $key ]['Title']	   = $this->get_branding();
			$plugins[ $key ]['Description'] = str_replace( 'Beaver Builder', $branding, $plugins[ $key ]['Description'] );
			$plugins[ $key ]['Author']	   = '';
			$plugins[ $key ]['AuthorName'] = '';
			$plugins[ $key ]['PluginURI']  = '';
		}

		return $plugins;

	}

}
new Beaver_Tunnels_White_Label();
