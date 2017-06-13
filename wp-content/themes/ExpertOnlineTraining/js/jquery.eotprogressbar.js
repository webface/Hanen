/**
 * jQuery plugin for progress bars w/ animation, colour, and percentage text
 * requires jQuery ui
 */
 
jQuery.fn.eotprogressbar = function(animate) {

	animate = typeof animate !== 'undefined' ? animate : false;
	
	if (!animate) {
		var percent = 0;
		var width = 0;
		this.each(function(){
			percent = Math.round(jQuery(this).text());
			jQuery(this).text('');
			jQuery(this).progressbar({value: percent});
			width   = jQuery(this).css('width');
			jQuery(this).addClass(percent < 50 ? "red" : percent < 80 ? "yellow" : "green")
						.children('.ui-progressbar-value')
						.html('<div style="text-align:center;width:'+width+';position:absolute;text-align:center;">'+percent+' %</div>');
		});
	} else {	
		var percent = 0;
		this.each(function(){
			percent = Math.round(jQuery(this).text());
			jQuery(this).attr('percent',percent).text('');
		});
		this.progressbar({
			change: function (event,ui) {
				var p       = Math.round(jQuery(this).progressbar('value'));
				var width   = jQuery(this).css('width');
				jQuery(this)
					.removeClass('red yellow green')
					.addClass(p < 50 ? "red" : p < 80 ? "yellow" : "green")
					.children('.ui-progressbar-value')
					.html('<div style="text-align:center;width:'+width+';position:absolute;text-align:center;">'+p+' %</div>');
			}
		});
	}
}

function animateProgressBars() {
	$progressBars = jQuery('.eotprogressbar');
	$progressBars.each(function(i,v){
		jQuery(this)
			.progressbar({value:0})
			.show();
	});
	setTimeout(function(){
		$progressBars.each(function(){
			jQuery(this).animate({
				value: jQuery(this).attr('percents')
			}, {
				duration: 2000,
				step: function(now, fx) {
					jQuery(this).progressbar({value:now});
				}
			});
		});
	},500);
}
function reanimateProgressBars() {
	$progressBars = jQuery('.eotprogressbar');

	setTimeout(function(){
		$progressBars.each(function(){
			jQuery(this).animate({
				value: jQuery(this).attr('percents')
			}, {
				duration: 500,
				step: function(now, fx) {
					jQuery(this).progressbar({value:now});
				}
			});
		});
	},500);
}

jQuery(document).ready(function($){

	$progressbars = $('.eotprogressbar');
	if ($progressbars.length > 10 && ($.browser.msie && parseInt($.browser.version) < 9 )) { 
		/** for incapable browsers, exit after making progress bars */
		$progressbars.eotprogressbar();
	} else if ($progressbars.length > 0) {
		/** for capable browsers, animate */
		$progressbars.eotprogressbar(true); 
		setTimeout(function(){animateProgressBars()},1);	
	} 
});

(function($, undefined) {
	$.extend( {
		animateProgressBars : function(progressBars) {
			progressBars.each(function() {
				$(this).children('.ui-progressbar-value')
					.css({width:0})
					.delay(1000)
					.animate({width: $(this).attr('percents')+'%'},2000);
			});
		}
	} );

})(jQuery);