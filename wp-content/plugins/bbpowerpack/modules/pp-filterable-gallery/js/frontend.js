(function($) {
	PPFilterableGallery = function(settings)
	{
		this.settings       = settings;
		this.nodeClass      = '.fl-node-' + settings.id;
		this.wrapperClass   = this.nodeClass + ' .pp-gallery-' + this.settings.layout;
		this.postClass      = this.wrapperClass + ' .pp-gallery-' + this.settings.layout + '-item';
		this.matchHeight	= settings.matchHeight == 'yes' ? true : false;
		this.masonry		= settings.masonry == 'yes' ? true : false;

		if(this._hasPosts()) {
			this._initLayout();
		}
	};

	PPFilterableGallery.prototype = {

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
			switch(this.settings.layout) {

				case 'grid':
				this._gridLayout();
				break;

				case 'masonry':
				this._masonryLayout();
				break;

			}

			$(this.postClass).css('visibility', 'visible');
		},

		_gridLayout: function()
		{
			var wrap = $(this.wrapperClass);

			var postFilterData = {
				itemSelector: '.pp-gallery-item',
				percentPosition: true,
				transitionDuration: '0.6s',
			};

			postFilterData = $.extend( {}, postFilterData, {
				layoutMode: 'fitRows',
				fitRows: {
					gutter: '.pp-photo-space'
			  	},
			} );

			wrap.imagesLoaded( $.proxy( function() {

				var node = $(this.nodeClass);
                var base = this;


				var postFilters = $(this.nodeClass).find('.pp-photo-gallery').isotope(postFilterData);
				var filtersWrap = node.find('.pp-gallery-filters');
				var filterToggle = node.find('.pp-gallery-filters-toggle');

				filterToggle.on('click', function () {
					filtersWrap.slideToggle(function () {
						if ($(this).is(':visible')) {
							$(this).addClass('pp-gallery-filters-open');
						}
						if (!$(this).is(':visible')) {
							$(this).removeClass('pp-gallery-filters-open');
						}
					});
				});

				filtersWrap.on('click', '.pp-gallery-filter-label', function() {
                    var filterVal = $(this).attr('data-filter');
                    postFilters.isotope({ filter: filterVal });

					filtersWrap.find('.pp-gallery-filter-label').removeClass('pp-filter-active');
					$(this).addClass('pp-filter-active');
					
					filterToggle.find('span.toggle-text').html($(this).text());
					if (filtersWrap.hasClass('pp-gallery-filters-open')) {
						filtersWrap.slideUp();
					}
                });

                setTimeout( function() {

                        node.find('.pp-filter-active').trigger('click');

                }, 1000 );


			}, this ) );
		},


		_masonryLayout: function()
		{
			var wrap = $(this.wrapperClass);

			var postFilterData = {
				itemSelector: '.pp-gallery-item',
				percentPosition: true,
				transitionDuration: '0.6s',
			};

			postFilterData = $.extend( {}, postFilterData, {
				masonry: {
					columnWidth: '.pp-gallery-item',
					gutter: '.pp-photo-space'
				},
			} );

			wrap.imagesLoaded( $.proxy( function() {

				var node = $(this.nodeClass);
                var base = this;

				var postFilters = node.find('.pp-masonry-content').isotope(postFilterData);
				var filtersWrap = node.find('.pp-gallery-filters');
				var filterToggle = node.find('.pp-gallery-filters-toggle');

				filterToggle.on('click', function () {
					filtersWrap.slideToggle(function () {
						if ($(this).is(':visible')) {
							$(this).addClass('pp-gallery-filters-open');
						}
						if (!$(this).is(':visible')) {
							$(this).removeClass('pp-gallery-filters-open');
						}
					});
				});

				filtersWrap.on('click', '.pp-gallery-filter-label', function() {
                    var filterVal = $(this).attr('data-filter');
                    postFilters.isotope({ filter: filterVal });

					filtersWrap.find('.pp-gallery-filter-label').removeClass('pp-filter-active');
					$(this).addClass('pp-filter-active');
					
					filterToggle.find('span.toggle-text').html($(this).text());
					if (filterWrap.hasClass('pp-gallery-filters-open')) {
						filterWrap.slideUp();
					}
                });

                setTimeout( function() {

                        node.find('.pp-filter-active').trigger('click');

                        node.find('.pp-masonry-content').isotope('layout');

                }, 1000 );

			}, this ) );

		},


		_gridLayoutMatchHeight: function()
		{
			var highestBox = 0;
			var contentHeight = 0;

			if ( 0 === this.matchHeight ) {
				return;
			}

            $(this.postClass).css('height', '').each(function(){

                if($(this).height() > highestBox) {
                	highestBox = $(this).height();
                	contentHeight = $(this).find('.pp-photo-gallery').outerHeight();
                }
            });

            $(this.postClass).height(highestBox);
            //$(this.postClass).find('.pp-content-post-data').css('min-height', contentHeight + 'px').addClass('pp-content-relative');
		},

	};

})(jQuery);
