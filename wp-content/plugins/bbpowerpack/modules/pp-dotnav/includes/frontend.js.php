/**
 * This file should contain frontend JavaScript that
 * will be applied to individual module instances.
 *
 * $module An instance of your module class.
 * $id The module's ID.
 * $settings The module's settings.
 */

;(function($) {
    $( '#pp-dotnav-<?php echo $id; ?> .pp-dot a' ).on( 'click', function(e) {
        e.preventDefault();
        if( 0 === $( '#'+$(this).data('row-id') ).length ) {
            return;
        }
        $( 'html, body' ).animate({
            scrollTop: $( '#'+$(this).data('row-id') ).offset().top - <?php echo absint($settings->top_offset); ?>
        }, <?php echo absint($settings->scroll_speed); ?>);
        $( '#pp-dotnav-<?php echo $id; ?> .pp-dot' ).removeClass( 'active' );
        $(this).parent().addClass( 'active' );
        return false;
    } );
    updateDot();
    $(window).on('scroll', function() {
        updateDot();
    });
    function updateDot() {
        $('.fl-row').each(function(){
            var $this = $(this);
            if ( ( $this.offset().top - $(window).height()/2 < $(window).scrollTop() ) && ( $this.offset().top >= $(window).scrollTop() || $this.offset().top + $this.height() - $(window).height()/2 > $(window).scrollTop() ) ) {
                $( '#pp-dotnav-<?php echo $id; ?> .pp-dot a[data-row-id="'+$this.attr('id')+'"]' ).parent().addClass('active');
            } else {
                $( '#pp-dotnav-<?php echo $id; ?> .pp-dot a[data-row-id="'+$this.attr('id')+'"]' ).parent().removeClass('active');
            }
        });
    }
    <?php if ( isset($settings->scroll_wheel) && 'enable' == $settings->scroll_wheel ) { ?>
        /*
        var lastAnimation = new Date().getTime(), animationDuration = <?php echo absint($settings->scroll_speed); ?>;
        $(document).bind('mousewheel DOMMouseScroll MozMousePixelScroll', function(e) {
            e.preventDefault();
            var curTime = new Date().getTime();
            var delta = Math.max(-1, Math.min(1, (e.wheelDelta || -e.detail)));
            if (curTime - lastAnimation < animationDuration) {
                e.preventDefault();
                return;
            }
            if (delta < 0) {
                if ($( '#pp-dotnav-<?php echo $id; ?> .pp-dot.active' ).next().length > 0)
                $( '#pp-dotnav-<?php echo $id; ?> .pp-dot.active' ).next().find('a').trigger('click');
            } else {
                if ($( '#pp-dotnav-<?php echo $id; ?> .pp-dot.active' ).prev().length > 0)
                $( '#pp-dotnav-<?php echo $id; ?> .pp-dot.active' ).prev().find('a').trigger('click');
            }
            lastAnimation = curTime;
            console.log(delta);
            return false;
        });
        */
    <?php } ?>
    <?php if ( 'enable' == $settings->scroll_keys ) { ?>
        $(document).keydown(function(e) {
            var tag = e.target.tagName.toLowerCase();
            if (tag === 'input' && tag === 'textarea') {
                return;
            }
            switch(e.which) {
                case 38: // up arrow key.
                    $( '#pp-dotnav-<?php echo $id; ?> .pp-dot.active' ).prev().find('a').trigger('click');
                break;
                case 40: // down arrow key.
                    $( '#pp-dotnav-<?php echo $id; ?> .pp-dot.active' ).next().find('a').trigger('click');
                break;
                case 33: // pageup key.
                    $( '#pp-dotnav-<?php echo $id; ?> .pp-dot.active' ).prev().find('a').trigger('click');
                break;
                case 36: // pagedown key.
                    $( '#pp-dotnav-<?php echo $id; ?> .pp-dot.active' ).next().find('a').trigger('click');
                break;
                default: return;
            }
        });
    <?php } ?>
})(jQuery);
