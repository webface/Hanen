jQuery( document ).ready(function($) {

	bt_open_pointer(0);
    function bt_open_pointer(i) {
        pointer = bt_pointer.pointers[i];
        options = $.extend( pointer.options, {
            close: function() {
                $.post( ajaxurl, {
                    pointer: pointer.pointer_id,
                    action: 'dismiss-wp-pointer'
                });
            }
        });

        $(pointer.target).pointer( options ).pointer('open');
    }

});
