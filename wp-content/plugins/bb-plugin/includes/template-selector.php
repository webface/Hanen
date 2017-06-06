<form class="fl-builder-settings fl-template-selector">
	<div class="fl-lightbox-header">
		<h1><?php _e('Layout Templates', 'fl-builder'); ?></h1>
	</div>
	<div class="fl-builder-settings-tabs">

		<?php if($enabled_templates == 'enabled' || $enabled_templates == 'core') : ?>
		<a href="#fl-builder-settings-tab-landing" class="<?php if(count($user_templates) == 0) echo 'fl-active'; ?>"><?php _e('Home Pages', 'fl-builder'); ?></a>
		<a href="#fl-builder-settings-tab-company"><?php _e('Content Pages', 'fl-builder'); ?></a>
		<?php endif; ?>

		<?php if($enabled_templates == 'enabled' || $enabled_templates == 'user') : ?>
		<a href="#fl-builder-settings-tab-yours" class="<?php if(count($user_templates) > 0 || $enabled_templates == 'user') echo 'fl-active'; ?>"><?php _e('Your Templates', 'fl-builder'); ?></a>
		<?php endif; ?>
	</div>
	<div class="fl-builder-settings-fields fl-nanoscroller">
		<div class="fl-nanoscroller-content">

			<?php if($enabled_templates == 'enabled' || $enabled_templates == 'core') : ?>

			<div id="fl-builder-settings-tab-landing" class="fl-builder-settings-tab <?php if(count($user_templates) == 0) echo 'fl-active'; ?>">

				<div class="fl-builder-settings-section">

					<?php $i = 0; foreach($templates as $key => $template) : if($template->category != 'landing') continue; ?>
					<div class="fl-template-preview<?php if(($i + 1) % 3 === 0) echo ' fl-last'; ?>" data-index="<?php echo $key; ?>">
						<div class="fl-template-image">
							<img src="<?php echo FL_BUILDER_URL . 'img/templates/' . $template->image; ?>" />
						</div>
						<span><?php echo $template->name; ?></span>
					</div>
					<?php $i++; endforeach; ?>

					<div class="fl-clear"></div>

				</div>
			</div>

			<div id="fl-builder-settings-tab-company" class="fl-builder-settings-tab">

				<div class="fl-builder-settings-section">

					<div class="fl-template-preview" data-index="0">
						<div class="fl-template-image">
							<img src="<?php echo FL_BUILDER_URL; ?>img/templates/blank.jpg" />
						</div>
						<span><?php _ex( 'Blank', 'Template name.', 'fl-builder' ); ?></span>
					</div>

					<?php $i = 1; foreach($templates as $key => $template) : if($template->category != 'company') continue; ?>
					<div class="fl-template-preview<?php if(($i + 1) % 3 === 0) echo ' fl-last'; ?>" data-index="<?php echo $key; ?>">
						<div class="fl-template-image">
							<img src="<?php echo FL_BUILDER_URL . 'img/templates/' . $template->image; ?>" />
						</div>
						<span><?php echo $template->name; ?></span>
					</div>
					<?php $i++; endforeach; ?>

					<div class="fl-clear"></div>

				</div>
			</div>

			<?php endif; ?>

			<?php if($enabled_templates == 'enabled' || $enabled_templates == 'user') : ?>

			<div id="fl-builder-settings-tab-yours" class="fl-builder-settings-tab <?php if(count($user_templates) > 0 || $enabled_templates == 'user') echo 'fl-active'; ?>">

				<div class="fl-builder-settings-section">

					<p class="fl-builder-settings-message fl-user-templates-message"><?php _e('You haven\'t saved any templates yet! To do so, create a layout and save it as a template under <strong>Tools &rarr; Save Template</strong>.', 'fl-builder'); ?></p>

					<?php if(count($user_templates) > 0) : ?>
					<div class="fl-user-templates">
						<div class="fl-user-template" data-id="blank">
							<span class="fl-user-template-title"><?php _ex( 'Blank', 'Template name.', 'fl-builder' ); ?></span>
							<div class="fl-clear"></div>
						</div>
						<?php foreach($user_templates as $template) : ?>
						<div class="fl-user-template" data-id="<?php echo $template->ID; ?>">
							<div class="fl-user-template-actions">
								<a class="fl-user-template-edit" href="<?php echo add_query_arg('fl_builder', '', get_permalink($template->ID)); ?>"><?php _e('Edit', 'fl-builder'); ?></a>
								<a class="fl-user-template-delete" href="javascript:void(0);" onclick="return false;"><?php _e('Delete', 'fl-builder'); ?></a>
							</div>
							<span class="fl-user-template-title"><?php echo $template->post_title; ?></span>
							<div class="fl-clear"></div>
						</div>
						<?php endforeach; ?>
						<div class="fl-clear"></div>
					</div>
					<?php endif; ?>

				</div>
			</div>

			<?php endif; ?>

		</div>
	</div>
	<div class="fl-lightbox-footer">
		<span class="fl-builder-settings-cancel fl-builder-button fl-builder-button-large" href="javascript:void(0);" onclick="return false;"><?php _e('Cancel', 'fl-builder'); ?></span>
	</div>
</form>