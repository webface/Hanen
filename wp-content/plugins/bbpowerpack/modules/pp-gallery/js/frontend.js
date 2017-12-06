(function($) {
	PPGallery = function(settings)
	{
		this.settings       = settings;
		this.nodeClass      = '.fl-node-' + settings.id;
		this.wrapperClass   = this.nodeClass + ' .pp-photo-gallery';
		this.itemClass      = this.wrapperClass + ' .pp-photo-gallery-item';

		if(this._hasItem()) {
			this._initLayout();
		}
	};

	PPGallery.prototype = {

		settings        : {},
		nodeClass       : '',
		wrapperClass    : '',
		itemClass       : '',
		gallery         : null,

		_hasItem: function()
		{
			return $(this.itemClass).length > 0;
		},

		_initLayout: function()
		{
			if ( this.settings.layout === 'masonry' ) {
				this._masonryLayout();
			}

			if ( this.settings.layout === 'justified' ) {
				this._justifiedLayout();
			}

			$(this.itemClass).css('visibility', 'visible');
		},

		_masonryLayout: function()
		{
			var wrap = $(this.wrapperClass);

			var isotopeData = {
				itemSelector: '.pp-gallery-masonry-item',
				percentPosition: true,
				transitionDuration: '0.6s',
				masonry: {
					columnWidth: '.pp-gallery-masonry-item',
					gutter: '.pp-photo-space'
				},
			};

			wrap.imagesLoaded( $.proxy( function() {
				$(this.nodeClass).find('.pp-photo-gallery').isotope(isotopeData);
			}, this ) );
		},

		_justifiedLayout: function()
		{
			var self = this;

			$(this.wrapperClass).justifiedGallery({
				margins: self.settings.spacing,
				rowHeight: self.settings.rowheight,
				maxRowHeight: self.settings.maxrowheight,
				lastRow: self.settings.lastrow,
			});
		},


	};

})(jQuery);
