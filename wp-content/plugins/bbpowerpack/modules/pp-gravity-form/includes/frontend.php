<?php
$ajax = $settings->form_ajax == 'yes' ? 'true' : 'false';
?>
<div class="pp-gf-content">
	<h3 class="form-title">
	<?php if ( $settings->custom_title ) {
	 	echo $settings->custom_title;
	} ?>
	</h3>
	<p class="form-description">
	<?php if ( $settings->custom_description ) {
		echo $settings->custom_description;
	} ?>
	</p>
    <?php
    if ( $settings->select_form_field ) {
        echo do_shortcode( '[gravityform id='.absint( $settings->select_form_field ).' title='.$settings->title_field.' description='.$settings->description_field.' ajax=' . $ajax . ' tabindex='. $settings->form_tab_index .']' );
    }
    ?>
</div>
