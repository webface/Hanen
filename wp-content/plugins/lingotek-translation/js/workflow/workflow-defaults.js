jQuery(document).ready(function() {
    var lastSel = jQuery('#workflow_id option:selected');
    // try new rollback here.
    jQuery('select').change(function(e) {

        if (Workflow.workflows.hasOwnProperty(this.value))
        {
            var workflow = this.value;
            jQuery('#yes-' + workflow).attr('href', Workflow.workflows[this.value]);

            jQuery('#no-' + workflow).on('click', function() {
                lastSel.attr('selected', true);
                Workflow.modals.close_modal('TB_closeWindowButton');
            });

            tb_show('Default Settings', '#TB_inline?width=500&height=300&inlineId=modal-window-id-' + workflow);
        }
    });
});