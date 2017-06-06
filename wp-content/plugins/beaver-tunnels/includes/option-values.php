<?php
/**
 * Beaver Tunnels Option Values
 *
 * @package Beaver_Tunnels
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Beaver Tunnels Option Values class
 */
class BTOptionValues {

	/**
	 * Get options
	 *
	 * @since 2.0
	 *
	 * @param  string $type    Type of options.
	 * @param  string $current Current option.
	 *
	 * @return [type]          [description]
	 */
	public static function get_options( $type, $current = '' ) {

		$options = BTOptionValues::get_values( $type );
		ob_start();

		foreach ( $options as $key => $label ) {
			echo sprintf(
				'<option%s value="%s">%s</option>',
				selected( $current, $key, false ),
				esc_html( $key ),
				esc_html( $label )
			);
		}

		return ob_get_clean();

	}

	/**
	 * Get values
	 *
	 * @since 2.0
	 *
	 * @param  string $type Type of values.
	 *
	 * @return array       Array of values
	 */
	public static function get_values( $type ) {

		$values = array();

		switch ( $type ) {

			case 'none':
				$values = BTOptionValues::get_none_values();
				break;

			case 'post':
				$values = BTOptionValues::get_post_values();
				break;

			case 'page':
			case 'page_parent':
				$values = BTOptionValues::get_page_values();
				break;

			case 'page_template':
				$values = BTOptionValues::get_page_template_values();
				break;

			case 'page_type':
				$values = BTOptionValues::get_page_type_values();
				break;

			case 'post_archive':
				$values = BTOptionValues::get_post_type_values();
				break;

			case 'post_singular':
				$values = BTOptionValues::get_post_type_values();
				break;

			case 'taxonomy_archive':
				$values = BTOptionValues::get_taxonomy_values();
				break;

			case 'term_archive':
				$values = BTOptionValues::get_term_values();
				break;

			case 'term_singular':
				$values = BTOptionValues::get_term_values();
				break;

			case 'user_status':
				$values = BTOptionValues::get_user_status();
				break;

			case 'user':
				$values = BTOptionValues::get_user_values();
				break;

			case 'user_role':
				$values = BTOptionValues::get_user_role_values();
				break;

			case 'author':
				$values = BTOptionValues::get_user_values();
				break;

			case 'day_of_week':
				$values = BTOptionValues::get_day_of_week_values();
				break;

			default:
				if ( strpos( $type, 'cpt_' ) !== false ) {
					$values = BTOptionValues::get_cpt_values( str_replace( 'cpt_', '', $type ) );
				} else {
					$values = BTOptionValues::get_none_values();
				}
				break;

		}

		return $values;

	}

	/**
	 * Get condition options
	 *
	 * @since 2.0
	 *
	 * @param  string $selected Current option.
	 *
	 * @return string           HTML select options
	 */
	public static function get_condition_options( $selected = 'none' ) {
		ob_start();
		?>
		<option value="none"<?php selected( $selected, 'none' ); ?>></option>
		<optgroup label="<?php esc_attr_e( 'Posts', 'beaver-tunnels' ); ?>">
			<option value="post"<?php selected( $selected, 'post' ); ?>><?php esc_html_e( 'Post', 'beaver-tunnels' ); ?></option>
			<?php echo BTOptionValues::get_cpt_options( $selected ); ?>
		</optgroup>
		<optgroup label="<?php esc_attr_e( 'Pages', 'beaver-tunnels' ); ?>">
			<option value="page"<?php selected( $selected, 'page' ); ?>><?php esc_html_e( 'Page', 'beaver-tunnels' ); ?></option>
			<option value="page_parent"<?php selected( $selected, 'page_parent' ); ?>><?php esc_html_e( 'Parent Page', 'beaver-tunnels' ); ?></option>
			<option value="page_template"<?php selected( $selected, 'page_template' ); ?>><?php esc_html_e( 'Page Template', 'beaver-tunnels' ); ?></option>
			<option value="page_type"<?php selected( $selected, 'page_type' ); ?>><?php esc_html_e( 'Page Type', 'beaver-tunnels' ); ?></option>
		</optgroup>
		<optgroup label="<?php esc_attr_e( 'Singular', 'beaver-tunnels' ); ?>">
			<option value="post_singular"<?php selected( $selected, 'post_singular' ); ?>><?php esc_html_e( 'Post Singular', 'beaver-tunnels' ); ?></option>
			<option value="term_singular"<?php selected( $selected, 'term_singular' ); ?>><?php esc_html_e( 'Term Singular', 'beaver-tunnels' ); ?></option>
		</optgroup>
		<optgroup label="<?php esc_attr_e( 'Archives', 'beaver-tunnels' ); ?>">
			<option value="post_archive"<?php selected( $selected, 'post_archive' ); ?>><?php esc_html_e( 'Post Archive', 'beaver-tunnels' ); ?></option>
			<option value="taxonomy_archive"<?php selected( $selected, 'taxonomy_archive' ); ?>><?php esc_html_e( 'Tax Archive', 'beaver-tunnels' ); ?></option>
			<option value="term_archive"<?php selected( $selected, 'term_archive' ); ?>><?php esc_html_e( 'Term Archive', 'beaver-tunnels' ); ?></option>
		</optgroup>
		<optgroup label="<?php esc_attr_e( 'User / Author', 'beaver-tunnels' ); ?>">
			<option value="user_status"<?php selected( $selected, 'user_status' ); ?>><?php esc_html_e( 'User Status', 'beaver-tunnels' ); ?></option>
			<option value="user"<?php selected( $selected, 'user' ); ?>><?php esc_html_e( 'User', 'beaver-tunnels' ); ?></option>
			<option value="user_role"<?php selected( $selected, 'user_role' ); ?>><?php esc_html_e( 'User Role', 'beaver-tunnels' ); ?></option>
			<option value="author"<?php selected( $selected, 'author' ); ?>><?php esc_html_e( 'Author', 'beaver-tunnels' ); ?></option>
		</optgroup>
		<optgroup label="<?php esc_attr_e( 'Date and Time', 'beaver-tunnels' ); ?>">
			<option value="before_date"<?php selected( $selected, 'before_date' ); ?>><?php esc_html_e( 'Before Date/Time', 'beaver-tunnels' ); ?></option>
			<option value="after_date"<?php selected( $selected, 'after_date' ); ?>><?php esc_html_e( 'After Date/Time', 'beaver-tunnels' ); ?></option>
			<option value="before_time"<?php selected( $selected, 'before_time' ); ?>><?php esc_html_e( 'Before Time', 'beaver-tunnels' ); ?></option>
			<option value="after_time"<?php selected( $selected, 'after_time' ); ?>><?php esc_html_e( 'After Time', 'beaver-tunnels' ); ?></option>
			<option value="day_of_week"<?php selected( $selected, 'day_of_week' ); ?>><?php esc_html_e( 'Day of Week', 'beaver-tunnels' ); ?></option>
		</optgroup>
		<?php
		return ob_get_clean();
	}

	/**
	 * Get operator options
	 *
	 * @since 2.0
	 *
	 * @param  string $selected Current option.
	 *
	 * @return string           HTML select options
	 */
	public static function get_operator_options( $selected = '==' ) {
		ob_start();
		?>
		<option value="=="<?php selected( $selected, '==' ); ?>>is equal to</option>
		<option value="!="<?php selected( $selected, '!=' ); ?>>is not equal to</option>
		<?php
		return ob_get_clean();
	}

	/**
	 * Get CPT options
	 *
	 * @since 2.0
	 *
	 * @param  string $selected Current option.
	 *
	 * @return string           HTML select options
	 */
	public static function get_cpt_options( $selected = '' ) {

		$selected = str_replace( 'cpt_', '', $selected );

		$args = array(
			'public'   => true,
			'_builtin' => false,
		);

		$post_types = get_post_types( $args, 'objects' );

		ob_start();
		foreach ( $post_types as $type ) {
			?>
			<option value="cpt_<?php echo esc_attr( $type->name ); ?>"<?php selected( $selected, $type->name ); ?>><?php echo esc_html( $type->labels->singular_name ); ?></option>
			<?php
		}
		return ob_get_clean();

	}

	/**
	 * Get none values
	 *
	 * @since 2.0
	 *
	 * @return array Empty array
	 */
	public static function get_none_values() {
		return array();
	}

	/**
	 * Get CTP Values
	 *
	 * @since 2.0
	 *
	 * @param  string $post_type The post type.
	 *
	 * @return array            An array of posts
	 */
	public static function get_cpt_values( $post_type ) {

		$post_options = array();

		$posts = new WP_Query( array(
			'posts_per_page' => 1000,
			'post_type'      => $post_type,
			'post_status'    => 'publish',
		) );

		if ( $posts->have_posts() ) {
			while ( $posts->have_posts() ) {
				$posts->the_post();
				$post_options[ (string) get_the_ID() ] = get_the_title();
			}
			wp_reset_postdata();
		}

		unset( $posts );

		return $post_options;

	}

	/**
	 * Get Post values
	 *
	 * @since 2.0
	 *
	 * @return array An array of values
	 */
	public static function get_post_values() {

		$posts = get_posts( array(
			'post_status' => 'publish',
			'posts_per_page' => 1000,
			'orderby' => 'date',
			'order' => 'DESC',
		) );

		$post_options = array();
		if ( ! empty( $posts ) ) {
			foreach ( $posts as $post ) {
				$post_options[ $post->ID ] = $post->post_title;
			}
		}

		return $post_options;

	}

	/**
	 * Retrive an array of published pages
	 *
	 * @since 1.0.0
	 *
	 * @return array Pages
	 */
	public static function get_page_values() {

		$pages = get_pages( array(
			'post_type' => 'page',
			'post_status' => 'publish',
		) );

		$page_options = array();
		if ( ! empty( $pages ) ) {
			foreach ( $pages as $page ) {
				$page_options[ $page->ID ] = $page->post_title;
			}
		}

		return $page_options;

	}

	/**
	 * Retrive an array of registered page templates
	 *
	 * @since 1.0.0
	 *
	 * @return array Page templates
	 */
	public static function get_page_template_values() {

		require_once ABSPATH . 'wp-admin/includes/admin.php';

		$templates = get_page_templates();

		$page_template_options = array();
		$page_template_options['default_template'] = __( 'Default Template', 'beaver-tunnels' );
		if ( ! empty( $templates ) ) {
			foreach ( $templates as $template_name => $template_filename ) {
				$page_template_options[ $template_filename ] = $template_name;
			}
		}

		return $page_template_options;

	}

	/**
	 * Get Page Type values
	 *
	 * @since 2.0
	 *
	 * @return array An array of values
	 */
	public static function get_page_type_values() {

		return array(
			'404'				=> __( '404 Page', 'beaver-tunnels' ),
			'search_results'	=> __( 'Search Results', 'beaver-tunnels' ),
			'front_page'		=> __( 'Front Page', 'beaver-tunnels' ),
			'blog_page'			=> __( 'Blog Page', 'beaver-tunnels' ),
			'rtl'				=> __( 'Right-To-Left', 'beaver-tunnels' ),
			'first_page'		=> __( 'First Page', 'beaver-tunnels' ),
		);

	}

	/**
	 * Get Day of Week values
	 *
	 * @since 2.0
	 *
	 * @return array An array of values
	 */
	public static function get_day_of_week_values() {

		return array(
			'0'	=> __( 'Sunday', 'beaver-tunnels' ),
			'1'	=> __( 'Monday', 'beaver-tunnels' ),
			'2'	=> __( 'Tuesday', 'beaver-tunnels' ),
			'3'	=> __( 'Wednesday', 'beaver-tunnels' ),
			'4'	=> __( 'Thursday', 'beaver-tunnels' ),
			'5'	=> __( 'Friday', 'beaver-tunnels' ),
			'6'	=> __( 'Saturday', 'beaver-tunnels' ),
		);

	}

	/**
	 * Retrieve an array of post types
	 *
	 * @since 1.0.0
	 *
	 * @return array Post types
	 */
	public static function get_post_type_values() {

		$builtin_post_types = get_post_types( array(
			'public' => true,
			'_builtin' => true,
		) );

		if ( ! is_array( $builtin_post_types ) ) {
			$builtin_post_types = array();
		}

		$post_types = get_post_types( array(
			'public' => true,
			'_builtin' => false,
		) );

		if ( ! is_array( $post_types ) ) {
			$post_types = array();
		}

		return array_merge( $builtin_post_types, $post_types );

	}

	/**
	 * Retrieve an array of public taxonomies
	 *
	 * @since 1.0.0
	 *
	 * @return array Taxonomies
	 */
	public static function get_taxonomy_values() {

		$taxonomies = get_taxonomies( array(
			'public' => true,
		) );

		return $taxonomies;

		$taxonomy_options = array();

		return $taxonomy_options;

	}

	/**
	 * Get Tag values
	 *
	 * @since 2.0
	 *
	 * @return array An array of values
	 */
	public static function get_tag_values() {

		$tags = get_tags( array( 'hide_empty' => false ) );
		$tags_array = array();

		foreach ( $tags as $tag ) {
			$tags_array[ $tag->term_id ] = $tag->name;
		}

		return $tags_array;

	}

	/**
	 * Retrieve an array of public taxonomy terms
	 *
	 * @since 1.0.0
	 *
	 * @return array An array of values
	 */
	public static function get_term_values() {

		$taxonomies = get_taxonomies( array(
			'public' => true,
		) );

		$taxonomy_options = array();

		foreach ( $taxonomies as $tax ) {
			$tax_terms = get_terms( $tax, array( 'hide_empty' => false ) );
			if ( is_array( $tax_terms ) ) {
				foreach ( $tax_terms as $term ) {
					$taxonomy_options[ $tax . '|' . $term->slug ] = $term->name . ' (' . $term->taxonomy . ')';
				}
			}
		}

		return $taxonomy_options;

	}

	/**
	 * Get User values
	 *
	 * @since 2.0
	 *
	 * @return array An array of values
	 */
	public static function get_user_values() {

		$users = get_users( array(
			'orderby' => 'login',
			'order'   => 'ASC',
			'fields'  => array(
				'ID',
				'display_name',
				'user_nicename',
			),
		) );

		$users_options = array();

		foreach ( $users as $user ) {
			$users_options[ $user->ID ] = $user->display_name . ' (' . $user->user_nicename . ')';
		}

		return $users_options;

	}

	/**
	 * Get User Role values
	 *
	 * @since 2.0
	 *
	 * @return array An array of values
	 */
	public static function get_user_role_values() {

		global $wp_roles;
	    $user_roles = $wp_roles->roles;

		$user_roles_options = array();

		foreach ( $user_roles as $key => $value ) {
			$user_roles_options[ $key ] = $value['name'];
		}

	    return $user_roles_options;

	}

	/**
	 * Get User Status values
	 *
	 * @since 2.0
	 *
	 * @return array An array of values
	 */
	public static function get_user_status() {

		return array(
			'logged_in' => __( 'Logged In', 'beaver-tunnels' ),
		);

	}

}
