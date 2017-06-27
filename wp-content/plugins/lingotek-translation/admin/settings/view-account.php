<?php
$client = new Lingotek_API();
$api_communities = $client->get_communities();
if ( ! isset( $api_communities->entities ) ) {
	add_settings_error( 'lingotek_community_resources', 'error', __( 'The Lingotek TMS is currently unavailable. Please try again later. If the problem persists, contact Lingotek Support.', 'lingotek-translation' ), 'error' );
	settings_errors();
}
if ( ! $community_id ) {
	$ltk_client = new Lingotek_API();
	$ltk_communities = $ltk_client->get_communities();
	$ltk_num_communities = $ltk_communities->properties->total;
	if ( 1 === $ltk_num_communities ) {
		$ltk_community_id = $ltk_communities->entities[0]->properties->id;
		$this->set_community_resources( $ltk_community_id );
		echo '<script type="text/javascript">document.body.innerHTML = ""; window.location = "admin.php?page=lingotek-translation_tutorial";</script>';
	}
}
?>

<h3><?php esc_html_e( 'Account', 'lingotek-translation' ); ?></h3>
<p class="description"><?php esc_html_e( 'Lingotek account connection and community selection.', 'lingotek-translation' ); ?></p>

<table class="form-table">
	<tr>
	<th scope="row">
		<?php esc_html_e( 'Connected', 'lingotek-translation' ) ?>
	  <a id="cd-show-link" class="dashicons dashicons-arrow-right" onclick="document.getElementById('connection-details').style.display = ''; document.getElementById('cd-hide-link').style.display = ''; this.style.display = 'none'; return false;"></a>
	  <a id="cd-hide-link" class="dashicons dashicons-arrow-down" onclick="document.getElementById('connection-details').style.display = 'none'; document.getElementById('cd-show-link').style.display = ''; this.style.display = 'none'; return false;" style="display: none;"></a>
	</th>
	<td>
		<?php esc_html_e( 'Yes', 'lingotek-translation' ) ?><span title="<?php esc_html_e( 'Connected', 'lingotek-translation' ) ?>" class="dashicons dashicons-yes" style="color: green;"></span>
	</td>
	</tr>
	<tbody id="connection-details" style="display: none;">
	<tr>
	<th scope="row"><?php echo esc_html( __( 'Login ID', 'lingotek-translation' ) ) ?></th>
	<td>
	  <label>
		<?php
		printf(
			'<input name="%s" class="regular-text" type="text" value="%s" disabled="disabled" />', 'login_id', esc_html( $token_details['login_id'] )
		);
		?>
	  </label>
	</td>
	</tr>
	<tr>
	<th scope="row"><?php echo esc_html( __( 'Access Token', 'lingotek-translation' ) ) ?></th>
	<td>
	  <label>
		<?php
		printf(
			'<input name="%s" class="regular-text" type="password" value="%s" disabled="disabled" style="display: none;" />', 'access_token', esc_html( $token_details['access_token'] )
		);
		printf(
			'<input name="%s" class="regular-text" type="text" value="%s" disabled="disabled" />', 'access_token', esc_html( $token_details['access_token'] )
		);
		?>
	  </label>
	</td>
	</tr>
	<tr>
	<th scope="row"><?php echo  esc_html( __( 'API Endpoint', 'lingotek-translation' ) ) ?></th>
	<td>
	  <label>
		<?php
		printf(
			'<input name="%s" class="regular-text" type="text" value="%s" disabled="disabled" />', 'base_url', esc_html( $base_url )
		);
		?>
	  </label>
	</td>
	</tr>
	<tr>
	<th></th>
	<td>
		<?php
		$confirm_message = __( 'Are you sure you would like to disconnect your Lingotek account? \n\nAfter disconnecting, you will need to re-connect an account to continue using Lingotek.', 'lingotek-translation' );
		echo '<a class="button" href="' . esc_html( $redirect_url ) . '&delete_access_token=true" onclick="return confirm(\'' . esc_html( $confirm_message ) . '\')">' . esc_html( __( 'Disconnect', 'lingotek-translation' ) ) . '</a>';
		?>
	</td>
	</tr>
	</tbody>
</table>

<hr/>

<form method="post" action="admin.php?page=<?php echo esc_html( $page_key ); ?>" class="validate">
	<?php wp_nonce_field( $page_key, '_wpnonce_' . $page_key ); ?>

	<table class="form-table">
	<tr>
	  <th scope="row"><label for="lingotek_community"><?php esc_html_e( 'Community', 'lingotek-translation' ) ?></label></th>
	  <td>
		<select name="lingotek_community" id="lingotek_community">
			<?php
			$default_community_id = $community_id;

			// Community.
			$communities = array();
			if ( isset( $api_communities->entities ) ) {
				foreach ( $api_communities->entities as $community ) {
					$communities[ $community->properties->id ] = $community->properties->title;
				}

				$num_communities = count( $communities );
				if ( 1 === $num_communities && ! $community_id ) {
					update_option( 'lingotek_community', current( array_keys( $communities ) ) );
				}
				if ( ! $community_id && $num_communities > 1 ) {
					echo "\n\t" . '<option value="">' . esc_html( __( 'Select', 'lingotek-translation' ) ) . '...</option>';
				}
				foreach ( $communities as $community_id_option => $community_title ) {
					$selected = ($default_community_id === $community_id_option) ? 'selected="selected"' : '';
					echo "\n\t" . '<option value="' . esc_attr( $community_id_option ) . '" ' . esc_html( $selected ) . '>' . esc_html( $community_title ) . '</option>';
				}
			}
			?>
		</select>
	  </td>
	</tr>
	</table>

	<?php submit_button( __( 'Save Changes', 'lingotek-translation' ), 'primary', 'submit', false ); ?>
</form>
