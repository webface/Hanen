/**
 * $module An instance of your module class.
 * $id The module's ID.
 * $settings The module's settings.
 */

;(function($) {
    var modal_<?php echo $id; ?> = {
        id: '<?php echo $id; ?>',
        type: '<?php echo $settings->modal_type; ?>',
        <?php echo ( 'auto' == $settings->modal_load ) ? 'auto_load: true' : 'auto_load: false'; ?>,
        <?php echo ( 'exit_intent' == $settings->modal_load ) ? 'exit_intent: true' : 'exit_intent: false'; ?>,
        <?php if ( 'exit_intent' == $settings->modal_load ) { ?>
        display_after: <?php echo intval($settings->display_after); ?>,
        <?php } ?>
        <?php if ( 'auto' == $settings->modal_load ) { ?>
        display_after: <?php echo intval($settings->display_after_auto); ?>,
        <?php } ?>
        delay: <?php echo $settings->modal_delay; ?>,
        animation_load: '<?php echo $settings->animation_load; ?>',
        animation_exit: '<?php echo $settings->animation_exit; ?>',
        <?php echo 'enabled' == $settings->modal_esc ? 'esc_exit: true' : 'esc_exit: false'; ?>,
        <?php echo 'yes' == $settings->modal_click_exit ? 'click_exit: true' : 'click_exit: false'; ?>,
        layout: '<?php echo $settings->modal_layout; ?>',
        <?php echo 'yes' == $settings->modal_height_auto ? 'auto_height: true' : 'auto_height: false'; ?>,
        <?php echo 'no' == $settings->modal_height_auto ? 'height:' . $settings->modal_height . ',' : ''; ?>
        width: <?php echo $settings->modal_width; ?>,
        breakpoint: <?php echo intval($settings->media_breakpoint); ?>,
        <?php echo ( class_exists( 'FLBuilderModel' ) && FLBuilderModel::is_builder_active() ) ? 'previewing: true' : 'previewing: false'; ?>
    };

    <?php if ( ! FLBuilderModel::is_builder_active() ) { ?>
    $(document).ready(function() {
        $('#modal-<?php echo $id; ?>').appendTo(document.body);
    });
    <?php } ?>

    <?php if ( 'onclick' == $settings->modal_load || 'other' == $settings->modal_load ) { ?>
    $(document).on('click', '.modal-<?php echo $id; ?>', function(e) {
        e.preventDefault();
        PPModal.init(modal_<?php echo $id; ?>);
    });
    <?php } ?>

    <?php if ( 'exit_intent' == $settings->modal_load ) { ?>
        document.addEventListener('mouseout', function(e) {
            e = e ? e : window.event;
            var pos = e.relatedTarget || e.toElement;
            if((!pos || null === pos) && !PPModal.isActive) {
                PPModal.init(modal_<?php echo $id; ?>);
            }
        });
    <?php } ?>

    <?php if ( FLBuilderModel::is_builder_active() && 'enabled' == $settings->modal_preview ) { ?>
    setTimeout(function() {
        $( '.fl-node-<?php echo $id; ?>' ).on( 'click', function() {
            if(!PPModal.isActive) {
                PPModal.init(modal_<?php echo $id; ?>);
            }
        } );
        if(!PPModal.isActive && $('form[data-type="pp-modal-box"]').length > 0) {
            if('<?php echo $id; ?>' === $('form[data-type="pp-modal-box"]').data('node')) {
                PPModal.init(modal_<?php echo $id; ?>);
            }
        }
    }, 600);
    <?php } ?>
    <?php if ( ! FLBuilderModel::is_builder_active() && 'auto' == $settings->modal_load ) { ?>
    PPModal.init(modal_<?php echo $id; ?>);
    <?php } ?>

    <?php if ( FLBuilderModel::is_builder_active() ) { ?>
        FLBuilder.addHook('settings-form-init', function() {
            $('.fl-builder-settings[data-node="<?php echo $id; ?>"] .modal-trigger-class').val('modal-<?php echo $id; ?>').attr('readonly', 'readonly').removeAttr('name').removeClass('text-full').off('change').off('keydown').off('keyup').off('keypress');
            $(document).on('click', '.fl-builder-settings[data-node="<?php echo $id; ?>"] .modal-trigger-class', function () {
                this.select();
            });
        });
    <?php } ?>

})(jQuery);
