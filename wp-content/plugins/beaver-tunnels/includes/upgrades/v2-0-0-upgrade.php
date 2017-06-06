<?php
/**
 * Beaver Tunnels 2.0.0 Upgrade
 *
 * @package Beaver_Tunnels
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Beaver Tunnels 2.0.0 Upgrade class
 */
class Beaver_Tunnels_v2_0_0_Upgrade {

	/**
	 * Conditions
	 *
	 * @var array
	 */
	private $conditions = array();

	/**
	 * Group ID
	 *
	 * @var integer
	 */
	private $group_id = 0;

	/**
	 * Rule ID
	 *
	 * @var integer
	 */
	private $rule_id = 0;

	/**
	 * User Status
	 *
	 * @var array
	 */
	private $user_status = null;

	/**
	 * Users
	 *
	 * @var array
	 */
	private $users = null;

	/**
	 * User Roles
	 *
	 * @var array
	 */
	private $user_roles = null;

	/**
	 * Construct
	 *
	 * @since 2.0
	 */
	function __construct() {
		$this->begin();
	}

	/**
	 * Begin the upgrade
	 *
	 * @since 2.0
	 *
	 * @return void
	 */
	private function begin() {

		$templates = new WP_Query( array(
			'posts_per_page' => -1,
			'post_type'      => 'fl-builder-template',
			'post_status'    => 'publish',
		) );

		if ( $templates->have_posts() ) :

			while ( $templates->have_posts() ) :

				$templates->the_post();

				$bt_action			= get_post_meta( get_the_ID(), '_beavertunnels_action', true );
				$bt_priority		= get_post_meta( get_the_ID(), '_beavertunnels_priority', true );
				$bt_all_pages		= get_post_meta( get_the_ID(), '_beavertunnels_all_pages', true );
				$bt_any_all			= get_post_meta( get_the_ID(), '_beavertunnels_conditions', true );
				$bt_posts			= get_post_meta( get_the_ID(), '_beavertunnels_posts', true );
				$bt_pages			= get_post_meta( get_the_ID(), '_beavertunnels_pages', true );
				$bt_templates		= get_post_meta( get_the_ID(), '_beavertunnels_templates', true );
				$bt_archives		= get_post_meta( get_the_ID(), '_beavertunnels_archives', true );
				$bt_singles			= get_post_meta( get_the_ID(), '_beavertunnels_singles', true );
				$bt_taxonomies		= get_post_meta( get_the_ID(), '_beavertunnels_taxonomies', true );
				$bt_term_archives	= get_post_meta( get_the_ID(), '_beavertunnels_term_archives', true );
				$bt_terms			= get_post_meta( get_the_ID(), '_beavertunnels_terms', true );
				$this->user_status	= get_post_meta( get_the_ID(), '_beavertunnels_user_status', true );
				$this->users		= get_post_meta( get_the_ID(), '_beavertunnels_users', true );
				$this->user_roles	= get_post_meta( get_the_ID(), '_beavertunnels_user_roles', true );

				if ( ( is_string( $bt_action ) && strlen( $bt_action ) > 0 ) && ( is_string( $bt_priority ) && strlen( $bt_priority ) > 0 ) ) {
					$new_action = array(
						'action'	=> $bt_action,
						'priority'	=> $bt_priority,
					);
					update_post_meta( get_the_ID(), '_beavertunnels_action', $new_action );
					delete_post_meta( get_the_ID(), '_beavertunnels_priority' );
				}

				// Build the conditions array.
				if ( 'any' === $bt_any_all ) {

					if ( is_array( $bt_posts ) ) {
						$this->any( 'post', $bt_posts );
					}
					if ( is_array( $bt_pages ) ) {
						$this->any( 'page', $bt_pages );
					}
					if ( is_array( $bt_templates ) ) {
						$this->any( 'page_template', $bt_templates );
					}
					if ( is_array( $bt_archives ) ) {
						$this->any( 'post_archive', $bt_archives );
					}
					if ( is_array( $bt_singles ) ) {
						$this->any( 'post_singular', $bt_singles );
					}
					if ( is_array( $bt_taxonomies ) ) {
						$this->any( 'taxonomy_archive', $bt_taxonomies );
					}
					if ( is_array( $bt_term_archives ) ) {
						$this->any( 'term_archive', $bt_term_archives );
					}
					if ( is_array( $bt_terms ) ) {
						$this->any( 'term_singular', $bt_terms );
					}
					if ( '1' === $bt_all_pages ) {
						if ( is_array( $this->user_status ) ) {
							$this->any( 'user_status', $this->user_status );
						}
						if ( is_array( $this->users ) ) {
							$this->any( 'user', $this->users );
						}
						if ( is_array( $this->user_roles ) ) {
							$this->any( 'user_role', $this->user_roles );
						}
					}
				} elseif ( 'all' === $bt_any_all ) {

					if ( is_array( $bt_posts ) ) {
						$this->all( 'post', $bt_posts );
					}
					if ( is_array( $bt_pages ) ) {
						$this->all( 'page', $bt_pages );
					}
					if ( is_array( $bt_templates ) ) {
						$this->all( 'page_template', $bt_templates );
					}
					if ( is_array( $bt_archives ) ) {
						$this->all( 'post_archive', $bt_archives );
					}
					if ( is_array( $bt_singles ) ) {
						$this->all( 'post_singular', $bt_singles );
					}
					if ( is_array( $bt_taxonomies ) ) {
						$this->all( 'taxonomy_archive', $bt_taxonomies );
					}
					if ( is_array( $bt_term_archives ) ) {
						$this->all( 'term_archive', $bt_term_archives );
					}
					if ( is_array( $bt_terms ) ) {
						$this->all( 'term_singular', $bt_terms );
					}
					if ( is_array( $this->user_status ) ) {
						$this->all( 'user_status', $this->user_status );
					}
					if ( is_array( $this->users ) ) {
						$this->all( 'user', $this->users );
					}
					if ( is_array( $this->user_roles ) ) {
						$this->all( 'user_role', $this->user_roles );
					}

				}

				$this->save( get_the_ID() );
				$this->delete( get_the_ID() );

				// Reset everything for the next template.
				$this->reset();

			endwhile;

		endif;

	} // function

	/**
	 * Save the new post meta
	 *
	 * @since 2.0
	 *
	 * @param  int $id Post ID.
	 *
	 * @return void
	 */
	private function save( $id ) {

		if ( count( $this->conditions ) > 0 ) {
			update_post_meta( $id, '_beavertunnels_conditions', $this->conditions );
		}

	}

	/**
	 * Delete the old post meta
	 *
	 * @since 2.0
	 *
	 * @param  int $id Post ID.
	 *
	 * @return void
	 */
	private function delete( $id ) {

		delete_post_meta( $id, '_beavertunnels_posts' );
		delete_post_meta( $id, '_beavertunnels_pages' );
		delete_post_meta( $id, '_beavertunnels_templates' );
		delete_post_meta( $id, '_beavertunnels_archives' );
		delete_post_meta( $id, '_beavertunnels_singles' );
		delete_post_meta( $id, '_beavertunnels_taxonomies' );
		delete_post_meta( $id, '_beavertunnels_term_archives' );
		delete_post_meta( $id, '_beavertunnels_terms' );
		delete_post_meta( $id, '_beavertunnels_user_status' );
		delete_post_meta( $id, '_beavertunnels_logged_in' );
		delete_post_meta( $id, '_beavertunnels_users' );
		delete_post_meta( $id, '_beavertunnels_user_roles' );

	}

	/**
	 * Build the display conditions
	 *
	 * @since 2.0
	 *
	 * @param  string $condition The display condition.
	 * @param  array  $values    The values.
	 *
	 * @return void
	 */
	private function any( $condition, $values ) {

		switch ( $condition ) :

			case 'user_status':

				$rule_id = 0;

				if ( 'all' === $values ) {
					break;
				}

				$this->conditions[ 'group_' . (string) $this->group_id ][ 'rule_' . (string) $rule_id ] = array(
					'condition'	=> $condition,
					'operator'	=> 'logged_in' === $values ? '==' : '!=',
					'value'		=> 'logged_in',
				);
				$rule_id++;
				$this->group_id++;
				break;

			case 'user':
			case 'user_role':

				if ( ! is_array( $values ) ) {
					return;
				}

				foreach ( $values as $value ) {

					$rule_id = 0;

					$this->conditions[ 'group_' . (string) $this->group_id ][ 'rule_' . (string) $rule_id ] = array(
						'condition'	=> $condition,
						'operator'	=> '==',
						'value'		=> $value,
					);
					$rule_id++;
					$this->group_id++;

				}
				break;

			default:

				if ( ! is_array( $values ) ) {
					return;
				}

				// If Users are selected.
				if ( is_array( $this->users ) ) {

					foreach ( $this->users as $user ) {

						foreach ( $values as $value ) {

							$rule_id = 0;

							$this->conditions[ 'group_' . (string) $this->group_id ][ 'rule_' . (string) $rule_id ] = array(
								'condition'	=> $condition,
								'operator'	=> '==',
								'value'		=> $value,
							);
							$rule_id++;

							$this->conditions[ 'group_' . (string) $this->group_id ][ 'rule_' . (string) $rule_id ] = array(
								'condition'	=> 'user',
								'operator'	=> '==',
								'value'		=> $user,
							);
							$rule_id++;

							if ( 'all' !== $this->user_status ) {

								$this->conditions[ 'group_' . (string) $this->group_id ][ 'rule_' . (string) $rule_id ] = array(
									'condition'	=> 'user_status',
									'operator'	=> 'logged_in' === $this->user_status ? '==' : '!=',
									'value'		=> 'logged_in',
								);
								$rule_id++;

							}

							$this->group_id++;

						}
					}
				}

				// If User Roles are selected.
				if ( is_array( $this->user_roles ) ) :

					foreach ( $this->user_roles as $user_role ) :

						foreach ( $values as $value ) :

							$rule_id = 0;

							$this->conditions[ 'group_' . (string) $this->group_id ][ 'rule_' . (string) $rule_id ] = array(
								'condition'	=> $condition,
								'operator'	=> '==',
								'value'		=> $value,
							);
							$rule_id++;

							$this->conditions[ 'group_' . (string) $this->group_id ][ 'rule_' . (string) $rule_id ] = array(
								'condition'	=> 'user_role',
								'operator'	=> '==',
								'value'		=> $user_role,
							);
							$rule_id++;

							if ( 'all' !== $this->user_status ) {

								$this->conditions[ 'group_' . (string) $this->group_id ][ 'rule_' . (string) $rule_id ] = array(
									'condition'	=> 'user_status',
									'operator'	=> 'logged_in' === $this->user_status ? '==' : '!=',
									'value'		=> 'logged_in',
								);
								$rule_id++;

							}

							$this->group_id++;

						endforeach;

					endforeach;

				endif;

				if ( ! is_array( $this->users ) && ! is_array( $this->user_roles ) ) {

					foreach ( $values as $value ) :

						$rule_id = 0;

						$this->conditions[ 'group_' . (string) $this->group_id ][ 'rule_' . (string) $rule_id ] = array(
							'condition'	=> $condition,
							'operator'	=> '==',
							'value'		=> $value,
						);
						$rule_id++;

						if ( 'all' !== $this->user_status ) {

							$this->conditions[ 'group_' . (string) $this->group_id ][ 'rule_' . (string) $rule_id ] = array(
								'condition'	=> 'user_status',
								'operator'	=> 'logged_in' === $this->user_status ? '==' : '!=',
								'value'		=> 'logged_in',
							);
							$rule_id++;

						}

						$this->group_id++;

					endforeach;

				}
				break;

		endswitch;

	}

	/**
	 * Build the display conditions if ALL was selected
	 *
	 * @since 2.0
	 *
	 * @param  string $condition The display condition.
	 * @param  array  $values    The values.
	 *
	 * @return void
	 */
	private function all( $condition, $values ) {

		switch ( $condition ) :

			case 'user_status':

				if ( 'all' === $values ) {
					break;
				}

				$this->conditions[ 'group_' . (string) $this->group_id ][ 'rule_' . (string) $this->rule_id ] = array(
					'condition'	=> $condition,
					'operator'	=> 'logged_in' === $values ? '==' : '!=',
					'value'		=> 'logged_in',
				);
				$this->rule_id++;
				break;

			default:

				if ( ! is_array( $values ) ) {
					return;
				}

				foreach ( $values as $value ) :

					$this->conditions[ 'group_' . (string) $this->group_id ][ 'rule_' . (string) $this->rule_id ] = array(
						'condition'	=> $condition,
						'operator'	=> '==',
						'value'		=> $value,
					);
					$this->rule_id++;

				endforeach;
				break;

		endswitch;

	}

	/**
	 * Reset the class variables
	 *
	 * @since 2.0
	 */
	private function reset() {

		$this->conditions		= array();
		$this->group_id			= 0;
		$this->rule_id			= 0;
		$this->user_status		= null;
		$this->users			= null;
		$this->user_roles		= null;

	}

}
new Beaver_Tunnels_v2_0_0_Upgrade();
