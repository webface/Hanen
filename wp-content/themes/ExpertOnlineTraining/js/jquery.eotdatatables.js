/**
 * jQuery plugin for dataTables sort actions
 * requires jQuery dataTables
 */
 
jQuery.fn.dataTableExt.oSort['numeric-a-tag-asc']  = function(a,b) {
	var x = parseInt(jQuery(a).text());
	var y = parseInt(jQuery(b).text());
	return ((x < y) ? -1 : ((x > y) ?  1 : 0));
};
 
jQuery.fn.dataTableExt.oSort['numeric-a-tag-desc'] = function(a,b) {
	var x = parseInt(jQuery(a).text());
	var y = parseInt(jQuery(b).text());
	return ((x < y) ?  1 : ((x > y) ? -1 : 0));
};
 
jQuery.fn.dataTableExt.oSort['frac-a-tag-asc'] = function(a,b) {
	var xa = jQuery(a).text().match(/[0-9]{1,10}/g);
	var ya = jQuery(b).text().match(/[0-9]{1,10}/g);
	var x = parseFloat(xa[1]) ? parseFloat(xa[0]) / parseFloat(xa[1]) : 0;
	var y = parseFloat(ya[1]) ? parseFloat(ya[0]) / parseFloat(ya[1]) : 0;
	return ((x < y) ?  -1 : ((x > y) ? 1 : 0));
};
 
jQuery.fn.dataTableExt.oSort['frac-a-tag-desc'] = function(a,b) {
	var xa = jQuery(a).text().match(/[0-9]{1,10}/g);
	var ya = jQuery(b).text().match(/[0-9]{1,10}/g);
	var x = parseFloat(xa[1]) ? parseFloat(xa[0]) / parseFloat(xa[1]) : 0;
	var y = parseFloat(ya[1]) ? parseFloat(ya[0]) / parseFloat(ya[1]) : 0;
	return ((x < y) ?  1 : ((x > y) ? -1 : 0));
};
 
jQuery.fn.dataTableExt.oSort['progressbar-asc'] = function(a,b) {
	var x = parseInt(jQuery('.ui-progressbar',a).attr('percent'));
	var y = parseInt(jQuery('.ui-progressbar',b).attr('percent'));
	return ((x < y) ?  -1 : ((x > y) ? 1 : 0));
};
 
jQuery.fn.dataTableExt.oSort['progressbar-desc'] = function(a,b) {
	var x = parseInt(jQuery('.ui-progressbar',a).attr('percent'));
	var y = parseInt(jQuery('.ui-progressbar',b).attr('percent'));
	return ((x < y) ?  1 : ((x > y) ? -1 : 0));
};
			
jQuery.fn.dataTableExt.aTypes.unshift(
	function ( sData )
	{	
		if ( jQuery( sData ).text().match(/[0-9]{1,10} \/ [0-9]{1,10}/) ) {
			return 'frac-a-tag';
		} else if ( jQuery( sData ).is('a') ) {
			if ( jQuery( sData ).text() == parseInt( jQuery( sData ).text() ) ) {
				return 'numeric-a-tag';
			}
		} else if ( jQuery( '.ui-progressbar', sData ).length > 0 ) {
			return 'progressbar';
		}
		
		return null;
	}
);