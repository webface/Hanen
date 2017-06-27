Workflow = {};

Workflow.modals = {};

Workflow.modals.close_modal = function(id) {
    jQuery('#' + id).trigger('click');
}

Workflow.modals.replace_href = function(element) {
    if (!jQuery(element).attr('url'))
    {
        jQuery(element).attr('url', jQuery(element).attr('href'));
    }
    return jQuery(element).attr('url');
}

Workflow.workflows = {
    'professional-translation' : 'https://www.lingotek.com/'
}