<?php
/**
 * Beaver Tunnels Output
 *
 * @package Beaver_Tunnels
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Beaver Tunnels Output class
 */
class Beaver_Tunnels_Output {

	/**
	 * The post object
	 *
	 * @var object
	 */
	public $post;

	/**
	 * Class constructor
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		if ( is_admin() ) {
			return;
		}

		add_action( 'wp', array( $this, 'set_post' ) );
		add_action( 'wp', array( $this, 'find_templates' ) );
		add_action( 'bt_before_render_content', array( $this, 'before_render_content' ) );
		add_action( 'bt_after_render_content', array( $this, 'after_render_content' ) );

	}

	/**
	 * Set the post object
	 *
	 * @since 1.0
	 */
	public function set_post() {

		global $post;
		$this->post = $post;

	}

	/**
	 * Before render content
	 *
	 * @since 2.0
	 *
	 * @return void
	 */
	public function before_render_content() {

		/**
		 * A fix for WooCommerce's reset loop function
		 */
		if ( function_exists( 'woocommerce_reset_loop' ) ) {
			remove_filter( 'loop_end', 'woocommerce_reset_loop' );
		}

	}

	/**
	 * After render content
	 *
	 * @since 2.0
	 *
	 * @return void
	 */
	public function after_render_content() {

		/**
		 * A fix for WooCommerce's reset loop function
		 */
		if ( function_exists( 'woocommerce_reset_loop' ) ) {
			add_filter( 'loop_end', 'woocommerce_reset_loop' );
		}

	}

	/**
	 * Loop through the saved templates to find action hooks
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function find_templates() {

		if ( is_singular( 'fl-builder-template' ) || is_admin() || defined( 'DOING_AUTOSAVE' ) ) {
			return;
		}

		do_action( 'bt_before_find_templates' );

		$query = $this->get_tunneled_templates();

		if ( is_object( $query ) && $query->have_posts() ) {

			$templates = $query->get_posts();

			foreach ( $templates as $template ) {

				$rules = $this->get_rules( $template->ID );

				// Continue if the template shouldn't be displayed on this page.
				if ( ! $this->show_on_page( $rules ) ) {
					continue;
				}

				$action = get_post_meta( $template->ID, '_beavertunnels_action', true );
				$self     = $this;

				add_action( $action['action'], function() use ( $self, $rules, $template ) {

					$content = $self->render_content( $template );

					if ( FLBuilderModel::is_builder_active() ) {
						$self->render_builder_active_content( $rules, $content );
					} else {
						echo $content;
					}
				}, $action['priority'] );

			} // foreach

		} // if

		do_action( 'bt_after_find_templates' );

	}

	/**
	 * Get tunneled templates
	 *
	 * @since 1.0
	 *
	 * @return object Post objects
	 */
	private function get_tunneled_templates() {

		// Check the cache for the query.
		$templates = wp_cache_get( 'beaver_tunnels_templates' );

		// If the cache is empty, then run the query.
		if ( false === $templates ) {

			$templates = new WP_Query( array(
				'posts_per_page' => -1,
				'post_type'      => 'fl-builder-template',
				'post_status'    => 'publish',
				'meta_query' => array(
					array(
						'key'     => '_beavertunnels_action',
						'value'   => '',
						'compare' => '!=',
					),
				),
			) );

			// Save the query to the cache.
			wp_cache_set( 'beaver_tunnels_templates', $templates );

		}

		return $templates;

	}

	/**
	 * Render the content
	 *
	 * @since 1.0
	 *
	 * @param  object $template Post object.
	 *
	 * @return string           HTML output
	 */
	public function render_content( $template ) {

		do_action( 'bt_before_render_content' );

		$method = 'render_query';

		switch ( $method ) {
			case 'render_query':
				ob_start();
				FLBuilder::render_query( array(
					'post_type' => 'fl-builder-template',
					'p'         => $template->ID,
				) );
				$content = ob_get_clean();
				break;

			case 'shortcode':
				$content = do_shortcode( '[fl_builder_insert_layout id="' . $template->ID . '"]' );
				break;

		}

		do_action( 'bt_after_render_content' );

		return $content;

	}

	/**
	 * Render builder active content
	 *
	 * @since 1.0
	 *
	 * @param  array  $rules   The array of rules.
	 * @param  string $content HTML.
	 *
	 * @return void
	 */
	public function render_builder_active_content( $rules, $content ) {

		wp_enqueue_style( 'beaver-tunnels-fl-builder', BEAVER_TUNNELS_PLUGIN_URL . 'assets/css/fl-builder.css', array(), '1.0.0' );
		wp_enqueue_script( 'beaver-tunnels-fl-builder', BEAVER_TUNNELS_PLUGIN_URL . 'assets/js/fl-builder.js', array( 'jquery' ),'1.0.0', false );
		?>
		<div class="beaver-tunnels-content">
			<a href="<?php echo esc_attr( add_query_arg( array( 'fl_builder' => '', 'bt_return' => get_permalink() ), get_permalink( $rules['template_id'] ) ) ); ?>"></a>
			<?php echo $content; ?>
			<div class="bt-template-overlay">
				<div class="fl-block-overlay-header">
					<div class="bt-template-overlay-actions">
						<div class="bt-template-overlay-title"><?php echo get_the_title( $rules['template_id'] ); ?></div>
						<i class="fl-block-settings fa fa-sign-out"></i>
					</div>
					<div class="fl-clear"></div>
				</div>
			</div>
		</div>
		<?php

	}

	/**
	 * Build the rules array
	 *
	 * @since since 0.9.2
	 *
	 * @param  string $template_id The id of the Beaver Builder Template.
	 *
	 * @return array        The array of display rules
	 */
	private function get_rules( $template_id ) {

		$rules = array(
			'template_id'	=> $template_id,
			'all_pages'		=> get_post_meta( $template_id, '_beavertunnels_all_pages', true ),
			'conditions'	=> get_post_meta( $template_id, '_beavertunnels_conditions', true ),
		);

		return $rules;

	}

	/**
	 * Check if the rule group conditions match
	 *
	 * @param  array $rules The array of rules.
	 *
	 * @return boolean        The result. TRUE / FALSE
	 */
	private function show_on_page( $rules ) {

		$conditions = $rules['conditions'];

		if ( ! is_array( $conditions ) ) {

			if ( '1' === $rules['all_pages'] ) {
				return true;
			}

			return false;
		}

		$show = false;

		foreach ( $conditions as $group ) {

			// Setup the arrays to store our results.
			$group_results = array();
			$operators = array();

			// If Global is on, default the rule group to TRUE.
			if ( '1' === $rules['all_pages'] ) {
				$group_results[] = true;
				$operators[] = '==';
			}

			foreach ( $group as $rule ) {

				if ( ! isset( $rule['condition'] ) || 'none' === $rule['condition'] ) {
					continue;
				}

				switch ( $rule['condition'] ) {

					case 'post':
						$result = $this->is_on_post( $rule['value'] );
						break;

					case 'page':
						$result = $this->is_on_page( $rule['value'] );
						break;

					case 'page_parent':
						$result = $this->parent_page_is( $rule['value'] );
						break;

					case 'page_template':
						$result = $this->is_on_page_template( $rule['value'] );
						break;

					case 'page_type':
						$result = $this->is_on_page_type( $rule['value'] );
						break;

					case 'post_archive':
						$result = $this->is_on_archive( $rule['value'] );
						break;

					case 'post_singular':
						$result = $this->is_on_singles( $rule['value'] );
						break;

					case 'taxonomy_archive':
						$result = $this->is_on_tax( $rule['value'] );
						break;

					case 'term_archive':
						$result = $this->is_on_tax_archive( $rule['value'] );
						break;

					case 'term_singular':
						$result = $this->is_on_term( $rule['value'] );
						break;

					case 'tag_singular':
						$result = $this->is_on_tag( $rule['value'] );
						break;

					case 'user_status':
						$result = is_user_logged_in();
						break;

					case 'user':
						$result = false;
						if ( get_current_user_id() === (int) $rule['value'] ) {
							$result = true;
						}
						break;

					case 'user_role':
						$result = false;
						$user = wp_get_current_user();
						if ( in_array( $rule['value'], (array) $user->roles, true ) ) {
							$result = true;
						}
						break;

					case 'author':
						$result = $this->is_post_author( $rule['value'] );
						break;

					case 'before_date':
						$result = $this->is_before_date( $rule['value'] );
						break;

					case 'after_date':
						$result = $this->is_after_date( $rule['value'] );
						break;

					case 'before_time':
						$result = $this->is_before_time( $rule['value'] );
						break;

					case 'after_time':
						$result = $this->is_after_time( $rule['value'] );
						break;

					case 'day_of_week':
						$result = $this->is_day_of_week( $rule['value'] );
						break;

					default:
						if ( strpos( $rule['condition'], 'cpt_' ) !== false ) {
							$result = $this->is_on_post( $rule['value'] );
						}
						break;

				}

				// Reverse the result if using the != operator.
				if ( '!=' === $rule['operator'] ) {
					$result = ! $result;
				}

				// Store the results to compare later.
				$group_results[] = $result;
				$operators[] = $rule['operator'];

			}

			// Check that all of the conditions were TRUE and that there was at least one == operator.
			if ( ! in_array( false, $group_results, true ) && in_array( '==', $operators, true ) ) {

				$show = true;
				break;

			}
		}

		return $show;

	}


	/**
	 * Check if we are on a specific post
	 *
	 * @since 1.0.0
	 *
	 * @param string $value The value to compare.
	 *
	 * @return boolean        True / false
	 */
	private function is_on_post( $value ) {

		if ( is_single( $value ) ) {
		 	return true;
		}

		return false;

	}

	/**
	 * Check if we are on a specific page
	 *
	 * @since 1.0.0
	 *
	 * @param  string $value The value to compare.
	 *
	 * @return boolean        True / false
	 */
	private function is_on_page( $value ) {

		if ( is_page( $value ) ) {
			return true;
		}

		return false;

	}

	/**
	 * Check if this page is a child of a specific parent page
	 *
	 * @since 2.0.2
	 *
	 * @param  string $value The value to compare.
	 *
	 * @return boolean            True / false
	 */
	private function parent_page_is( $value ) {

		if ( empty( $value ) ) {
			return false;
		}

		global $post;
		if ( is_page() && (int) $value === $post->post_parent ) {
		    return true;
		}

		return false;

	}

	/**
	 * Check if we are on a specific page template
	 *
	 * @since 1.0.0
	 *
	 * @param  string $value The value to compare.
	 *
	 * @return boolean            True / false
	 */
	private function is_on_page_template( $value ) {

		if ( empty( $value ) ) {
			return false;
		}

		if ( is_page_template( $value ) ) {
			return true;
		}

		if ( 'default_template' === $value && is_page() && ! is_page_template() ) {
			return true;
		}

		return false;

	}

	/**
	 * Check if we are on a specific page type
	 *
	 * @since 1.0.0
	 *
	 * @param  string $value The value to compare.
	 *
	 * @return boolean            True / false
	 */
	private function is_on_page_type( $value ) {

		if ( empty( $value ) ) {
			return false;
		}

		switch ( $value ) {

			case '404':
				return is_404();
				break;

			case 'search_results':
				return is_search();
				break;

			case 'blog_page':
				return is_home();
				break;

			case 'front_page':
				return is_front_page();
				break;

			case 'rtl':
				return is_rtl();
				break;

			case 'first_page':
				return ! is_paged();
				break;

		}

		return false;

	}

	/**
	 * Check if we are on a specific archive page
	 *
	 * @since 1.0.0
	 *
	 * @param  string $value The value to compare.
	 *
	 * @return boolean           True / false
	 */
	private function is_on_archive( $value ) {

		if ( empty( $value ) ) {
			return false;
		}

		if ( is_post_type_archive( $value ) ) {
			return true;
		}

		if ( 'post' === $value && is_home() ) {
			return true;
		}

		return false;

	}

	/**
	 * Check if we are on a single post type page
	 *
	 * @since 1.0.0
	 *
	 * @param  string $value The value to compare.
	 *
	 * @return boolean          True / false
	 */
	private function is_on_singles( $value ) {

		if ( empty( $value ) ) {
			return false;
		}

		if ( is_singular( $value ) ) {
			return true;
		}

		return false;

	}

	/**
	 * Check if we are on a specific taxonomy page
	 *
	 * @since 1.0.0
	 *
	 * @param  string $value The value to compare.
	 *
	 * @return boolean             True / false
	 */
	private function is_on_tax( $value ) {

		if ( empty( $value ) ) {
			return false;
		}

		if ( is_tax( $value ) ) {
			return true;
		}

		if ( 'category' === $value && is_category() ) {
			return true;
		}

		if ( 'post_tag' === $value && is_tag() ) {
			return true;
		}

		return false;

	}

	/**
	 * Check if we are on a specific taxonomy archive
	 *
	 * @since 1.0.0
	 *
	 * @param  string $value The value to compare.
	 *
	 * @return boolean             True / false
	 */
	private function is_on_tax_archive( $value ) {

		if ( empty( $value ) ) {
			return false;
		}

		$tax_array = explode( '|', $value );
		if ( is_tax( $tax_array[0], $tax_array[1] ) || ( ( 'category' === $tax_array[0] ) && is_category( $tax_array[1] ) ) ) {
			return true;
		}

		return false;

	}

	/**
	 * Check if we are on a specific tag archive
	 *
	 * @param  string $value The value to compare.
	 *
	 * @return boolean        True / False
	 */
	private function is_on_tag_archive( $value ) {

		if ( empty( $value ) ) {
			return false;
		}

		if ( is_tag( $value ) ) {
			return true;
		}

		return false;

	}

	/**
	 * Check if we are on a specific taxonomy post
	 *
	 * @since 1.0.0
	 *
	 * @param  string $value The value to compare.
	 *
	 * @return boolean        True / false
	 */
	private function is_on_term( $value ) {

		if ( empty( $value ) ) {
			return false;
		}

		if ( ! is_singular() ) {
			return false;
		}

		$term_array = explode( '|', $value );
		if ( has_term( $term_array[1], $term_array[0], get_the_ID() ) || ( ( 'category' === $term_array[0] ) && in_category( $term_array[1] ) ) ) {
			return true;
		}

		return false;

	}

	/**
	 * Check the post author of the current post
	 *
	 * @since 1.0.0
	 *
	 * @param  string $value The value to compare.
	 *
	 * @return boolean          True / false
	 */
	private function is_post_author( $value ) {

		if ( empty( $value ) ) {
			return false;
		}

		if ( ! is_singular() ) {
			return false;
		}

		global $post;
		if ( isset( $post->post_author ) && $value === $post->post_author ) {
			return true;
		}

		return false;

	}

	/**
	 * Check if it is before a certain date
	 *
	 * @since 2.0
	 *
	 * @param  string $value Date.
	 *
	 * @return boolean
	 */
	private function is_before_date( $value ) {

		$right_now = current_time( 'timestamp' );
		$date = strtotime( $value );

		if ( $right_now < $date ) {
			return true;
		}

		return false;

	}

	/**
	 * Check if it is after a certain date
	 *
	 * @since 2.0
	 *
	 * @param  string $value Date.
	 *
	 * @return boolean
	 */
	private function is_after_date( $value ) {

		$right_now = current_time( 'timestamp' );
		$date = strtotime( $value );

		if ( $right_now > $date ) {
			return true;
		}

		return false;

	}

	/**
	 * Check if it is before a certain time
	 *
	 * @since 2.0
	 *
	 * @param  string $value Time.
	 *
	 * @return boolean
	 */
	private function is_before_time( $value ) {

		$value = str_replace( ' ', '', $value );
		$right_now = current_time( 'timestamp' );
		$time = strtotime( $value, $right_now );

		if ( $right_now < $time ) {
			return true;
		}

		return false;

	}

	/**
	 * Check if it is after a certain time
	 *
	 * @since 2.0
	 *
	 * @param  string $value Time.
	 *
	 * @return boolean
	 */
	private function is_after_time( $value ) {

		$value = str_replace( ' ', '', $value );
		$right_now = current_time( 'timestamp' );
		$time = strtotime( $value, $right_now );

		if ( $right_now > $time ) {
			return true;
		}

		return false;

	}

	/**
	 * Check if it is a certain day of the week
	 *
	 * @since 2.0
	 *
	 * @param  string $value Day number.
	 *
	 * @return boolean
	 */
	private function is_day_of_week( $value ) {

		$day = current_time( 'w' );

		if ( $day === $value ) {
			return true;
		}

		return false;

	}

}
new Beaver_Tunnels_Output();
