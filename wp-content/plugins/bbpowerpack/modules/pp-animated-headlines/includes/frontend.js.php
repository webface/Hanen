<?php
$strings = $typeSpeed = $startDelay = $backSpeed = $backDelay = $loop = $loopCount = $showCursor = $cursorChar = '';

$str = str_replace( array("\r\n", "\n", "\r", "<br/>", "<br>"), '|', $settings->animated_text );
$lines = explode( '|', $str );
$count_lines = count( $lines );

$strings = '[';
foreach($lines as $key => $line) {
    $strings .= '"' . trim( htmlspecialchars_decode( strip_tags( $line ) ) ) . '"';
	if ( $key != ( $count_lines - 1 ) ) {
	    $strings .= ',';
	}
}
$strings .= ']';
?>
<?php
if ( $settings->effect_type == 'type' ) {
    $typeSpeed  = ( !empty($settings->typing_speed) ) ? $settings->typing_speed : 35;
    $startDelay = ( !empty($settings->start_delay) ) ? $settings->start_delay : 200;
    $backSpeed  = ( !empty($settings->back_speed) ) ? $settings->back_speed : 0;
    $backDelay  = ( !empty($settings->back_delay) ) ? $settings->back_delay : 1500;
    $loop       = ( $settings->enable_loop == 'no' ) ? 'false' : 'true';

    if ( $settings->show_cursor == 'yes' ) {
        $showCursor = 'true';
        $cursorChar = ( !empty($settings->cursor_text) ) ? $settings->cursor_text : "|";
    } else {
        $showCursor = 'false';
    }
  ?>

    jQuery( document ).ready(function($) {
        new PPAnimatedText({
            id: '<?php echo $id ?>',
            viewport_position:  90,
            animation:    '<?php echo $settings->effect_type; ?>',
            strings:      <?php echo $strings; ?>,
            typeSpeed:    <?php echo $typeSpeed; ?>,
            startDelay:   <?php echo $startDelay; ?>,
            backSpeed:    <?php echo $backSpeed; ?>,
            backDelay:    <?php echo $backDelay; ?>,
            loop:         <?php echo $loop; ?>,
            showCursor:   <?php echo $showCursor; ?>,
            cursorChar:   '<?php echo $cursorChar; ?>'
        });
    });

<?php
} elseif ( $settings->effect_type == 'slide_up' ) {
    $speed = $pause = $mousePause = '';
    $speed = ( !empty($settings->animation_speed) ) ? $settings->animation_speed : 200;
    $pause = ( !empty($settings->pause_time) ) ? $settings->pause_time : 3000;
    $mousePause = ( $settings->pause_hover == 'yes') ? true : false;
?>
    jQuery( document ).ready(function($) {
        var wrapper = $('.fl-node-<?php echo $id; ?>'),
            slide_block = wrapper.find('.pp-slide-main'),
            slide_block_height = slide_block.find('.pp-slide_text').height();
            slide_block.height( slide_block_height );

        var PPAnimated_<?php echo $id; ?> = new PPAnimatedText({
            id: '<?php echo $id ?>',
            viewport_position:  90,
            animation:    '<?php echo $settings->effect_type; ?>',
            speed:      <?php echo $speed; ?>,
            pause:    <?php echo $pause; ?>,
            mousePause:   Boolean( '<?php echo $mousePause; ?>' )
        });

        $( window ).resize(function() {
            PPAnimated_<?php echo $id ?>._initAnimatedText();
        });
    });
<?php
}
?>

<?php if( $settings->effect_type == 'fade' ) {
	$speed = ( !empty($settings->animation_speed) ) ? $settings->animation_speed : 200; ?>
	jQuery( document ).ready(function($) {
	  	new PPAnimatedText({
	  	    id: '<?php echo $id ?>',
		    animation:    '<?php echo $settings->effect_type; ?>',
	  	    strings: <?php echo $strings; ?>,
		    speed: <?php echo $speed; ?>,
	  	});
	});
<?php } ?>
