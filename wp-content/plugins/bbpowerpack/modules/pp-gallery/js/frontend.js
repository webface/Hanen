(function($) {
	PPGallery = function(settings)
	{
		this.settings       = settings;
		this.nodeClass      = '.fl-node-' + settings.id;
		this.wrapperClass   = this.nodeClass + ' .pp-gallery-' + this.settings.layout;
		this.postClass      = this.wrapperClass + ' .pp-gallery-' + this.settings.layout + '-item';

		if(this._hasPosts()) {
			this._initLayout();
		}
	};

	PPGallery.prototype = {

		settings        : {},
		nodeClass       : '',
		wrapperClass    : '',
		postClass       : '',
		gallery         : null,

		_hasPosts: function()
		{
			return $(this.postClass).length > 0;
		},

		_initLayout: function()
		{
			if ( this.settings.layout === 'masonry' ) {
				this._masonryLayout();
			}

			$(this.postClass).css('visibility', 'visible');
		},

		_masonryLayout: function()
		{
			var wrap = $(this.wrapperClass);

			var masonryData = {
				itemSelector: '.pp-gallery-masonry-item',
				percentPosition: true,
				transitionDuration: '0.6s',
			};

			masonryData = $.extend( {}, masonryData, {
				masonry: {
					columnWidth: '.pp-gallery-masonry-item',
					gutter: '.pp-photo-space'
				},
			} );

			wrap.imagesLoaded( $.proxy( function() {
                $(this.nodeClass).find('.pp-masonry-content').isotope(masonryData);
			}, this ) );
		},

	};

})(jQuery);
