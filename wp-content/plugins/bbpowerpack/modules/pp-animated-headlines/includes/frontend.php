<div class="pp-module-content pp-animated-text-node">
<?php if( !empty($settings->effect_type) ) { ?>
<?php echo '<'.$settings->text_tag_selection;?> class="pp-animated-text-wrap pp-animated-text-<?php echo $settings->effect_type; ?>">
	<span class="pp-animated-text-prefix"><?php echo $settings->prefix; ?></span>
	<?php
		$output = '';

		if($settings->effect_type == 'type') {
			$output = '';
			$output .= '<span class="pp-animated-text-main pp-typed-main-wrap">';
	            $output .= '<span class="pp-typed-main">';
				$output .= '</span>';
			$output .= '</span>';
			echo $output;
		}

		if($settings->effect_type == 'fade') {
			$output = '';
			$output .= '<span class="pp-animated-text-main pp-fade-main-wrap">';
	            $output .= '<span class="pp-fade-main">';
				$output .= '</span>';
			$output .= '</span>';
			echo $output;
		}

		if( $settings->effect_type == 'slide_up') {
			$adjust_class = '';

            $order   = array("\r\n", "\n", "\r", "<br/>", "<br>");
			$replace = '|';

            $str = str_replace($order, $replace, $settings->animated_text);
            $lines = explode("|", $str);
            $count_lines = count($lines);
			$output = '';

			/*if( $settings->prefix != '' && $settings->suffix != '' ){
				$adjust_class = " pp-adjust-width";
			}*/

            $output .= '<span class="pp-animated-text-main  pp-slide-main'.$adjust_class.'">';
				$output .= '<span class="pp-slide-main_ul">';
						foreach($lines as $key => $line)
						{
							$output .= '<span class="pp-slide-block">';
							$output .= '<span class="pp-slide_text">'.strip_tags($line).'</span>';
							$output .= '</span>';
							if ( $count_lines == 1 ) {
								$output .= '<span class="pp-slide-block">';
								$output .= '<span class="pp-slide_text">'.strip_tags($line).'</span>';
								$output .= '</span>';
							}
						}
				$output .= '</span>';
			$output .= '</span>';
			echo $output;
		}
	?>

	<span class="pp-animated-text-suffix"><?php echo $settings->suffix; ?></span>
<?php echo '</'.$settings->text_tag_selection.'>';?>
<?php } ?>
</div>
