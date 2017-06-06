<div class="<?php echo $module->get_classname(); ?>">
	<a href="<?php echo $settings->link; ?>" target="<?php echo $settings->link_target; ?>" class="fl-button" role="button">
		<?php if(!empty($settings->icon)) : ?>
		<i class="fl-button-icon fa <?php echo $settings->icon; ?>"></i>
		<?php endif; ?>
		<span class="fl-button-text"><?php echo $settings->text; ?></span>
	</a>
</div>