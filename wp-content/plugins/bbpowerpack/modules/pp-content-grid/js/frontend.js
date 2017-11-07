(function($) {

	PPContentGrid = function(settings)
	{
		this.settings       = settings;
		this.nodeClass      = '.fl-node-' + settings.id;
		this.wrapperClass   = this.nodeClass + ' .pp-content-post-' + this.settings.layout;
		this.postClass      = this.wrapperClass + ' .pp-content-' + this.settings.layout + '-post';
		this.matchHeight	= settings.matchHeight == 'yes' ? true : false;
		this.masonry		= settings.masonry == 'yes' ? true : false;

		if(this._hasPosts()) {
			this._initLayout();
			this._initInfiniteScroll();
		}
	};

	PPContentGrid.prototype = {

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

				case 'carousel':
				this._carouselLayout();
				break;

			}

			$(this.postClass).css('visibility', 'visible');
		},

		_gridLayout: function()
		{
			var wrap = $(this.wrapperClass);

			var postFilterData = {
				itemSelector: '.pp-content-post',
				percentPosition: true,
				transitionDuration: '0.4s',
			};

			if ( !this.masonry ) {
				postFilterData = $.extend( {}, postFilterData, {
					layoutMode: 'fitRows',
					fitRows: {
						gutter: '.pp-grid-space'
				  	},
				} );
			}

			if ( this.masonry ) {

				postFilterData = $.extend( {}, postFilterData, {
					masonry: {
						columnWidth: '.pp-content-post',
						gutter: '.pp-grid-space'
					},
				} );
			}

			wrap.imagesLoaded( $.proxy( function() {

				var node = $(this.nodeClass);
                var base = this;


                if ( this.settings.filters || this.masonry ) {

                    var postFilters = $(this.nodeClass).find('.pp-content-post-grid').isotope(postFilterData);

                    $(this.nodeClass).find('.pp-post-filters').on('click', '.pp-post-filter', function() {
                        var filterVal = $(this).attr('data-filter');
                        postFilters.isotope({ filter: filterVal });

                        node.find('.pp-post-filters .pp-post-filter').removeClass('pp-filter-active');
                        $(this).addClass('pp-filter-active');
                    });
                }

                if( !this.masonry ) {
                    setTimeout( function() {
                        base._gridLayoutMatchHeight();
                    }, 1000 );
                }

                if ( this.settings.filters || this.masonry ) {
                    setTimeout( function() {

                            node.find('.pp-filter-active').trigger('click');
							if ( !base.masonry ) {
                            	base._gridLayoutMatchHeight();
							}
                            node.find('.pp-content-post-grid').isotope('layout');

                    }, 1000 );
                }

			}, this ) );
		},

		_carouselLayout: function()
		{
			var wrap = $(this.nodeClass + ' .pp-content-post-carousel .pp-content-posts-inner');
			wrap.imagesLoaded( $.proxy( function() {
				var owlOptions = {
					afterUpdate: $.proxy(this._gridLayoutMatchHeight, this),
					afterInit: $.proxy(this._gridLayoutMatchHeight, this),
					afterLazyLoad: $.proxy(this._gridLayoutMatchHeight, this),
				};
				//console.log($.extend({}, this.settings.carousel, owlOptions));
				wrap.owlCarousel( $.extend({}, this.settings.carousel, owlOptions) );
			}, this));

		},

		_gridLayoutMatchHeight: function()
		{
			var highestBox = 0;
			var contentHeight = 0;
			var postElements = $(this.postClass);

			if (0 === this.matchHeight) {
				return;
			}

			if ( this.settings.layout === 'grid' ) {
				var columns = this.settings.postColumns.desktop;

				if (window.innerWidth <= 768) {
					columns = this.settings.postColumns.tablet;
				}
				if (window.innerWidth <= 600) {
					columns = this.settings.postColumns.mobile;
				}

				var rows = Math.round(postElements.length / columns);

				if ( postElements.length % columns > 0 ) {
					rows = rows + 1;
				}

				// range.
				var j = 1,
					k = columns;

				for( var i = 0; i < rows; i++ ) {
					// select number of posts in the current row.
					var postsInRow = $(this.postClass + ':nth-child(n+' + j + '):nth-child(-n+' + k + ')');

					// get height of the larger post element within the current row.
					postsInRow.css('height', '').each(function () {
						if ($(this).height() > highestBox) {
							highestBox = $(this).height();
							contentHeight = $(this).find('.pp-content-post-data').outerHeight();
						}
					});
					// apply the height to all posts in the current row.
					postsInRow.height(highestBox);

					// increment range.
					j = k + 1;
					k = k + columns;
					if ( k > postElements.length ) {
						k = postElements.length;
					}
				}
			} else {
				// carousel layout.
				postElements.css('height', '').each(function(){

					if($(this).height() > highestBox) {
						highestBox = $(this).height();
						contentHeight = $(this).find('.pp-content-post-data').outerHeight();
					}
				});

				postElements.height(highestBox);
			}
            //$(this.postClass).find('.pp-content-post-data').css('min-height', contentHeight + 'px').addClass('pp-content-relative');
		},

		_initInfiniteScroll: function()
		{
			if(this.settings.pagination == 'scroll' && typeof FLBuilder === 'undefined') {
				this._infiniteScroll();
			}
		},

		_infiniteScroll: function(settings)
		{
			var path 		= $(this.nodeClass + ' .pp-content-grid-pagination a.next').attr('href'),
				pagePattern = /(.*?(\/|\&|\?)paged-[0-9]{1,}(\/|=))([0-9]{1,})+(.*)/,
				pageMatched = null,
				scrollData	= {
					navSelector     : this.nodeClass + ' .pp-content-grid-pagination',
					nextSelector    : this.nodeClass + ' .pp-content-grid-pagination a.next',
					itemSelector    : this.postClass,
					prefill         : true,
					bufferPx        : 200,
					loading         : {
						msgText         : 'Loading',
						finishedMsg     : '',
						img             : FLBuilderLayoutConfig.paths.pluginUrl + 'img/ajax-loader-grey.gif',
						speed           : 1
					}
				};

			// Define path since Infinitescroll incremented our custom pagination '/paged-2/2/' to '/paged-3/2/'.
			if ( pagePattern.test( path ) ) {
				scrollData.path = function( currPage ){
					pageMatched = path.match( pagePattern );
					path = pageMatched[1] + currPage + pageMatched[5];
					return path;
				}
			}

			$(this.wrapperClass).infinitescroll( scrollData, $.proxy(this._infiniteScrollComplete, this) );

			setTimeout(function(){
				$(window).trigger('resize');
			}, 100);
		},

		_infiniteScrollComplete: function(elements)
		{
			var wrap = $(this.wrapperClass);

			elements = $(elements);

			if(this.settings.layout == 'grid') {
				wrap.imagesLoaded( $.proxy( function() {
					if( !this.masonry ) {
						this._gridLayoutMatchHeight();
						if( this.settings.filters ) {
							wrap.isotope('insert', elements, $.proxy(this._gridLayoutMatchHeight, this));
						}
					} else {
						wrap.isotope('insert', elements);
					}

					elements.css('visibility', 'visible');
					wrap.find('.pp-grid-space').remove();
					wrap.append('<div class="pp-grid-space"></div>');
				}, this ) );
			}
		}
	};

})(jQuery);
