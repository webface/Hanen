jQuery( document ).ready(function($) {

	jQuery( 'table.bt-form-table .dashicons-editor-help' ).on('click', function(e) {
		jQuery('button#contextual-help-link').trigger('click');
		e.preventDefault();
	});

});


(function($){

	if ( typeof BTCondition != 'undefined' ) {
		return;
	}

	BTCondition = {

		init: function() {
			BTCondition._bind();
            BTCondition._initChosenJS();
		},

		_bind: function() {
            $('.bt-condition select').off();
            $('.bt-condition-button-and').off();
            $('.bt-condition-button-or').off();
            $('.bt-condition-button-remove').off();

			$('.bt-condition select').on('change', BTCondition._change );
            $('.bt-condition-button-and').on('click', BTCondition._andClick );
            $('.bt-condition-button-or').on('click', BTCondition._orClick );
            $('.bt-condition-button-remove').on('click', BTCondition._removeClick );
		},

        _initChosenJS: function() {
            jQuery('table.bt-form-table .bt-condition select').chosen({width: "100%", disable_search: true, allow_single_deselect: true});
            jQuery('table.bt-form-table .bt-operator select').chosen({width: "100%", disable_search: true});
            jQuery('table.bt-form-table .bt-value select').chosen({width: "100%", disable_search_threshold: 10});
        },

		_change: function() {

            $('.bt-loading').show();

            condition = $(this);

            condition.attr('disabled', true);

			data = {
				action: 'bt_row_condition_changed',
				bt_nonce: bt_vars.bt_nonce,
				bt_condition: condition.val(),
				bt_group: condition.parent().parent().data('bt-group'),
				bt_field: condition.parent().parent().parent().parent().data('bt-field-id')
			};

    		jQuery.post( ajaxurl, data,  function( response ) {
    			var element = condition.parent().parent().replaceWith(response);
                BTCondition.init();
                $('.bt-loading').hide();
    		});
		},

        _andClick: function(e) {

            $('.bt-loading').show();

            button = $(this);
            button.attr('disabled', true);
            current_tr = button.parent().parent();
            group = current_tr.data('bt-group');
            field = current_tr.parent().parent().data('bt-field-id');

            data = {
    			action: 'bt_get_and_condition_row',
    			bt_nonce: bt_vars.bt_nonce,
                bt_field: field,
                bt_group: group
    		};

    		jQuery.post( ajaxurl, data,  function( response ) {
                current_tr.after(response);
                BTCondition.init();
                $('.bt-loading').hide();
                button.attr('disabled', false);
    		});

            e.preventDefault();

        },

        _orClick: function(e) {

            $('.bt-loading').show();

            button = $(this);
            current_tr = button.parent().parent();
            or_tr = current_tr.prev();
            group = current_tr.data('bt-group');
			next_group = group + 1;
            field = current_tr.parent().parent().data('bt-field-id');

            data = {
    			action: 'bt_get_and_condition_row',
    			bt_nonce: bt_vars.bt_nonce,
                bt_field: field,
                bt_group: next_group
    		};

    		jQuery.post( ajaxurl, data,  function( response ) {
                current_tr.before(response);
                current_tr.before(or_tr.clone());
				current_tr.data('bt-group', next_group);
                BTCondition.init();
                $('.bt-loading').hide();
    		});

            e.preventDefault();

        },

        _removeClick: function(e) {

            button = $(this);
            current_tr = button.parent().parent();
            prev_tr = current_tr.prev();
            next_tr = current_tr.next();

            if ( prev_tr.data('bt-group') == 'or' && next_tr.data('bt-group') == 'or' ) {
                prev_tr.remove();
            }

            current_tr.remove();

            e.preventDefault();
        }

	};

	$(function(){
		BTCondition.init();
	});

})(jQuery);
