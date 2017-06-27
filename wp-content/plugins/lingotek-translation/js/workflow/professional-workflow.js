jQuery(document).ready(function() {
    var yes = '#yes-' + workflow_vars.id;
    var no = '#no-' + workflow_vars.id;
    var yes_request = yes + '-request';
    var no_request = no + '-request';

    jQuery(".dashicons-upload").on('click', function(event){
        event.preventDefault();
        jQuery(this).addClass('thickbox');
        var url = Workflow.modals.replace_href(this);
        jQuery(yes).attr('href', url);
        jQuery(this).attr('href', '#TB_inline?width=500&height=300&inlineId=modal-window-id-' + workflow_vars.id);
    });

    jQuery(".dashicons-plus").on('click', function(event){
        event.preventDefault();
        jQuery(this).addClass('thickbox');
        var url = Workflow.modals.replace_href(this);
        jQuery(yes_request).attr('href', url);
        jQuery(this).attr('href', '#TB_inline?width=500&height=300&inlineId=modal-window-id-' + workflow_vars.id + '-request');
    });

    jQuery(yes + ',' + yes_request).on('click', function() {
        Workflow.modals.close_modal('TB_closeWindowButton');
    });
    jQuery(no + ',' + no_request).on('click', function() {
        Workflow.modals.close_modal('TB_closeWindowButton');
    });
})
