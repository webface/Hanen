<div class="fl-accordion fl-accordion-<?php echo $settings->label_size; if($settings->collapse) echo ' fl-accordion-collapse'; ?>">
	<?php foreach($settings->items as $item) : if(empty($item)) continue; ?>
	<div class="fl-accordion-item">
		<div class="fl-accordion-button">
			<span class="fl-accordion-button-label"><?php echo $item->label; ?></span>
			<i class="fl-accordion-button-icon fa fa-plus"></i>
		</div>
		<div class="fl-accordion-content fl-clearfix"><?php echo $item->content; ?></div>
	</div>
	<?php endforeach; ?>
</div>