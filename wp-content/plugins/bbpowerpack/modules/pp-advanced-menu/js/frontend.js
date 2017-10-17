(function($) {

	/**
	 * Class for Menu Module
	 *
	 * @since 1.6.1
	 */
	PPAdvancedMenu = function( settings ) {

		// set params
		this.settingsId 		 = settings.id;
		this.nodeClass           = '.fl-node-' + settings.id;
		this.wrapperClass        = this.nodeClass + ' .pp-advanced-menu';
		this.type				 = settings.type;
		this.mobileToggle		 = settings.mobile;
		this.breakPoints         = settings.breakPoints;
		this.mobileBreakpoint	 = settings.mobileBreakpoint;
		this.mediaBreakpoint	 = settings.mediaBreakpoint;
		this.mobileMenuType	 	 = settings.mobileMenuType;
		this.offCanvasDirection	 = settings.offCanvasDirection;
		this.isBuilderActive	 = settings.isBuilderActive;
		this.currentBrowserWidth = $( window ).width();

		this._bindSettingsFormEvents();
		// initialize the menu
		this._initMenu();

		// check if viewport is resizing
		$( window ).on( 'resize', $.proxy( function( e ) {

			var width = $( window ).width();

			// if screen width is resized, reload the menu
		    if( width != this.currentBrowserWidth ) {

				this._initMenu();
 				this._clickOrHover();
		    	this.currentBrowserWidth = width;
			}

		}, this ) );

	};

	PPAdvancedMenu.prototype = {
		nodeClass               : '',
		wrapperClass            : '',
		type 	                : '',
		breakPoints 			: {},
		$submenus				: null,

		/**
		 * Check if the screen size fits a mobile viewport.
		 *
		 * @since  1.6.1
		 * @return bool
		 */
		_isMobile: function() {
			return $( window ).width() <= this.breakPoints.small ? true : false;
		},

		/**
		 * Check if the screen size fits a medium viewport.
		 *
		 * @since  1.10.5
		 * @return bool
		 */
		_isMedium: function() {
			return $( window ).width() <= this.breakPoints.medium ? true : false;
		},

		/**
		 * Check if the screen size fits a custom viewport.
		 *
		 * @since  1.10.5
		 * @return bool
		 */
		_isCustom: function() {
			return $( window ).width() <= this.breakPoints.custom ? true : false;
		},

		/**
		 * Check if the menu should toggle for the current viewport base on the selected breakpoint
		 *
		 * @see 	this._isMobile()
		 * @see 	this._isMedium()
		 * @since  	1.10.5
		 * @return bool
		 */
		_isMenuToggle: function() {
			if ( 'always' == this.mobileBreakpoint
				|| ( this._isMobile() && 'mobile' == this.mobileBreakpoint )
				|| ( this._isMedium() && 'medium-mobile' == this.mobileBreakpoint )
				|| ( this._isCustom() && 'custom' == this.mobileBreakpoint )
				) {
				return true;
			}

			return false;
		},

		_bindSettingsFormEvents: function()
		{
			// $('body').delegate( '.fl-builder-settings select[name="offcanvas_direction"]', 'change', function() {
			// 	$('html').removeClass('pp-off-canvas-menu-open');
			// } );
		},

		/**
		 * Initialize the toggle logic for the menu.
		 *
		 * @see    this._isMobile()
		 * @see    this._menuOnCLick()
		 * @see    this._clickOrHover()
		 * @see    this._submenuOnRight()
		 * @see    this._toggleForMobile()
		 * @since  1.6.1
		 * @return void
		 */
		_initMenu: function() {
			this._menuOnFocus();
			this._submenuOnClick();
			if ( $( this.nodeClass ).length && this.type == 'horizontal' ) {
				this._initMegaMenus();
			}

			if( this._isMenuToggle() || this.type == 'accordion' ) {

				$( this.wrapperClass ).off( 'mouseenter mouseleave' );
				this._menuOnClick();
				this._clickOrHover();

			} else {
				$( this.wrapperClass ).off( 'click' );
				this._submenuOnRight();
			}

			if( this.mobileToggle != 'expanded' ) {
				this._toggleForMobile();

				if( this.mobileMenuType === 'off-canvas' ) {
					this._initOffCanvas();
				}

				if( this.mobileMenuType === 'full-screen' ) {
					this._initFullScreen();
				}
			}
		},

		/**
		 * Adds a focus class to menu elements similar to be used similar to CSS :hover psuedo event
		 *
		 * @since  1.9.0
		 * @return void
		 */
		_menuOnFocus: function() {
			$( this.nodeClass ).off('focus').on( 'focus', 'a', $.proxy( function( e ) {
				var $menuItem	= $( e.target ).parents( '.menu-item' ).first(),
					$parents	= $( e.target ).parentsUntil( this.wrapperClass );

				$('.pp-advanced-menu .focus').removeClass('focus');

				$menuItem.addClass('focus');
				$parents.addClass('focus');

			}, this ) ).on( 'focusout', 'a', $.proxy( function( e ) {
				$( e.target ).parentsUntil( this.wrapperClass ).removeClass( 'focus' );
			}, this ) );
		},

		/**
		 * Logic for submenu toggling on accordions or mobile menus (vertical, horizontal)
		 *
		 * @since  1.6.1
		 * @return void
		 */
		_menuOnClick: function() {
			$( this.wrapperClass ).off().on( 'click', '.pp-has-submenu-container', $.proxy( function( e ) {

				var $link			= $( e.target ).parents( '.pp-has-submenu' ).first(),
					$subMenu 		= $link.children( '.sub-menu' ).first(),
					$href	 		= $link.children('.pp-has-submenu-container').first().find('> a').attr('href'),
					$subMenuParents = $( e.target ).parents( '.sub-menu' ),
					$activeParent 	= $( e.target ).closest( '.pp-has-submenu.pp-active' );

				if( !$subMenu.is(':visible') || $(e.target).hasClass('pp-menu-toggle')
					|| ($subMenu.is(':visible') && (typeof $href === 'undefined' || $href == '#')) ) {
					e.preventDefault();
				}
				else {
					window.location.href = $href;
					return;
				}

				if ($(this.wrapperClass).hasClass('pp-advanced-menu-accordion-collapse')) {

					if ( !$link.parents('.menu-item').hasClass('pp-active') ) {
						$('.pp-active', this.wrapperClass).not($link).removeClass('pp-active');
					}
					else if ($link.parents('.menu-item').hasClass('pp-active') && $link.parent('.sub-menu').length) {
						$('.pp-active', this.wrapperClass).not($link).not($activeParent).removeClass('pp-active');
					}

					$('.sub-menu', this.wrapperClass).not($subMenu).not($subMenuParents).slideUp('normal');
				}

				$subMenu.slideToggle();
				$link.toggleClass( 'pp-active' );

			}, this ) );

		},

		/**
		 * Logic for submenu items click event
		 *
		 * @since  1.3.1
		 * @return void
		 */
		_submenuOnClick: function(){
			$( this.wrapperClass + ' .sub-menu' ).off().on( 'click', 'a', $.proxy( function( e ){
				if ( $( e.target ).parent().hasClass('focus') ) {
					$( e.target ).parentsUntil( this.wrapperClass ).removeClass('focus');
				}
			}, this ) );
		},

		/**
		 * Changes general styling and behavior of menus based on mobile / desktop viewport.
		 *
		 * @see    this._isMobile()
		 * @since  1.6.1
		 * @return void
		 */
		_clickOrHover: function() {
			this.$submenus = this.$submenus || $( this.wrapperClass ).find( '.sub-menu' );
			var $wrapper   = $( this.wrapperClass ),
				$menu      = $wrapper.find( '.menu' );
				$li        = $wrapper.find( '.pp-has-submenu' );

			if( this._isMenuToggle() ) {
				$li.each( function( el ) {
					if( !$(this).hasClass('pp-active') ) {
						$(this).find( '.sub-menu' ).fadeOut();
					}
				} );
			} else {
				$li.each( function( el ) {
					if( !$(this).hasClass('pp-active') ) {
						$(this).find( '.sub-menu' ).css( {
							'display' : '',
							'opacity' : ''
						} );
					}
				} );
			}
		},

		/**
		 * Logic to prevent submenus to go outside viewport boundaries.
		 *
		 * @since  1.6.1
		 * @return void
		 */
		_submenuOnRight: function() {

			$( this.wrapperClass )
				.on( 'mouseenter', '.pp-has-submenu', $.proxy( function( e ) {

					if( $ ( e.currentTarget ).find('.sub-menu').length === 0 ) {
						return;
					}

					var $link           = $( e.currentTarget ),
						$parent         = $link.parent(),
						$subMenu        = $link.find( '.sub-menu' ),
						subMenuWidth    = $subMenu.width(),
						subMenuPos      = 0,
						winWidth        = $( window ).width();

					if( $link.closest( '.pp-menu-submenu-right' ).length !== 0) {

						$link.addClass( 'pp-menu-submenu-right' );

					} else if( $( 'body' ).hasClass( 'rtl' ) ) {

						subMenuPos = $parent.is( '.sub-menu' ) ?
									 $parent.offset().left - subMenuWidth:
									 $link.offset().left - subMenuWidth;

						if( subMenuPos <= 0 ) {
							$link.addClass( 'pp-menu-submenu-right' );
						}

					} else {

						subMenuPos = $parent.is( '.sub-menu' ) ?
									 $parent.offset().left + $parent.width() + subMenuWidth :
									 $link.offset().left + subMenuWidth;

						if( subMenuPos > winWidth ) {
							$link.addClass('pp-menu-submenu-right');
						}
					}
				}, this ) )
				.on( 'mouseleave', '.pp-has-submenu', $.proxy( function( e ) {
					$( e.currentTarget ).removeClass( 'pp-menu-submenu-right' );
				}, this ) );

		},

		/**
		 * Logic for the mobile menu button.
		 *
		 * @since  1.6.1
		 * @return void
		 */
		_toggleForMobile: function() {

			var $wrapper = null,
				$menu    = null;

			if( this._isMenuToggle() ) {

				$wrapper = $( this.wrapperClass );
				$menu    = $wrapper.children( '.menu' );

				if( !$wrapper.find( '.pp-advanced-menu-mobile-toggle' ).hasClass( 'pp-active' ) ) {
					$menu.css({ display: 'none' });
				}

				$wrapper.on( 'click', '.pp-advanced-menu-mobile-toggle', function( e ) {
					$( this ).toggleClass( 'pp-active' );
					$menu.slideToggle();
				} );

				$menu.on( 'click', '.menu-item > a[href*="#"]', function(e) {
					var $href = $(this).attr('href'),
						$targetID = '';

					if ( $href !== '#' ) {
						$targetID = $href.split('#')[1];

						if ( $('body').find('#'+  $targetID).length > 0 ) {
							e.preventDefault();
							$( this ).toggleClass( 'pp-active' );
							$menu.slideToggle('fast', function() {
								setTimeout(function() {
									$('html, body').animate({
								        scrollTop: $('#'+ $targetID).offset().top
								    }, 1000, function() {
								        window.location.hash = $targetID;
								    });
								}, 500);
							});
						}
					}
				});
			}
			else {

				$wrapper = $( this.wrapperClass ),
				$menu    = $wrapper.children( '.menu' );
				$wrapper.find( '.pp-advanced-menu-mobile-toggle' ).removeClass( 'pp-active' );
				$menu.css({ display: '' });
			}
		},

		/**
		 * Init any mega menus that exist.
		 *
		 * @see 	this._isMenuToggle()
		 * @since  	1.10.4
		 * @return void
		 */
		_initMegaMenus: function() {

			var module     = $( this.nodeClass ),
				rowContent = module.closest( '.fl-row-content' ),
				rowWidth   = rowContent.width(),
				rowOffset  = rowContent.offset().left,
				megas      = module.find( '.mega-menu' ),
				disabled   = module.find( '.mega-menu-disabled' ),
				isToggle   = this._isMenuToggle();

			if ( isToggle ) {
				megas.removeClass( 'mega-menu' ).addClass( 'mega-menu-disabled' );
				module.find( 'li.mega-menu-disabled > ul.sub-menu' ).css( 'width', '' );
				rowContent.css( 'position', '' );
			} else {
				disabled.removeClass( 'mega-menu-disabled' ).addClass( 'mega-menu' );
				module.find( 'li.mega-menu > ul.sub-menu' ).css( 'width', rowWidth + 'px' );
				rowContent.css( 'position', 'relative' );
			}
		},

		/**
		 * Init off-canvas menu.
		 *
		 * @since  	1.2.8
		 * @return void
		 */
		_initOffCanvas: function() {
			$('html').addClass('pp-off-canvas-menu-module');
			$('html').addClass('pp-off-canvas-menu-' + this.offCanvasDirection);

			if ( this.isBuilderActive ) {
				this._toggleMenu();
				return;
			}
			if ( 'always' === this.mediaBreakpoint || this.mediaBreakpoint >= this.currentBrowserWidth ) {
				if ($('pp-advanced-menu-off-canvas-'+this.settingsId).length > 0) {
					$('pp-advanced-menu-off-canvas-'+this.settingsId).remove();
				}
				$(this.nodeClass).find('.pp-advanced-menu.off-canvas').appendTo('body').wrap('<div id="pp-advanced-menu-off-canvas-'+this.settingsId+'" class="fl-node-'+this.settingsId+'">');
			}
			this._toggleMenu();
		},

		/**
		 * Init full-screen overlay menu.
		 *
		 * @since  	1.2.8
		 * @return void
		 */
		_initFullScreen: function() {
			$('html').addClass('pp-full-screen-menu-module');
			if ( this.isBuilderActive ) {
				this._toggleMenu();
				return;
			}
			if ( 'always' === this.mediaBreakpoint || this.mediaBreakpoint >= this.currentBrowserWidth ) {
				if ($('pp-advanced-menu-full-screen-'+this.settingsId).length > 0) {
					$('pp-advanced-menu-full-screen-'+this.settingsId).remove();
				}
				$(this.nodeClass).find('.pp-advanced-menu.full-screen').appendTo('body').wrap('<div id="pp-advanced-menu-full-screen-'+this.settingsId+'" class="fl-node-'+this.settingsId+'">');
			}
			this._toggleMenu();
		},

		/**
		 * Trigger the toggle event for off-canvas
		 * and full-screen overlay menus.
		 *
		 * @since  	1.2.8
		 * @return void
		 */
		_toggleMenu: function() {
			var self = this;
			if( self.mobileMenuType === 'full-screen' ) {
				var winHeight = $(window).height();
				$(self.nodeClass).find('.pp-menu-overlay').css('height', winHeight + 'px');
				$(window).resize(function() {
					$(self.nodeClass).find('.pp-menu-overlay').css('height', winHeight + 'px');
				});
			}
			// Toggle Click
			$(self.nodeClass).find('.pp-advanced-menu-mobile-toggle' ).off('click').on( 'click', function() {
				if( $(self.nodeClass).find('.pp-advanced-menu').hasClass('menu-open') ) {
					$(self.nodeClass).find('.pp-advanced-menu').removeClass('menu-open');
					$(self.nodeClass).find('.pp-advanced-menu').addClass('menu-close');
					$('html').removeClass('pp-off-canvas-menu-open');
					$('html').removeClass('pp-full-screen-menu-open');
				} else {
					$(self.nodeClass).find('.pp-advanced-menu').addClass('menu-open');
					if( self.mobileMenuType === 'off-canvas' ) {
						$('html').addClass('pp-off-canvas-menu-open');
					}
					if( self.mobileMenuType === 'full-screen' ) {
						$('html').addClass('pp-full-screen-menu-open');
					}
				}
			} );
			// Close button click
			$(self.nodeClass).find('.pp-advanced-menu .pp-menu-close-btn, .pp-clear').on( 'click', function() {
				$(self.nodeClass).find('.pp-advanced-menu').removeClass('menu-open');
				$(self.nodeClass).find('.pp-advanced-menu').addClass('menu-close');
				$('html').removeClass('pp-off-canvas-menu-open');
				$('html').removeClass('pp-full-screen-menu-open');
			} );

			if ( this.isBuilderActive ) {
				setTimeout(function() {
					if ( $('.fl-builder-settings[data-node="'+self.settingsId+'"]').length > 0 ) {
						$('.pp-advanced-menu').removeClass('menu-open');
						$(self.nodeClass).find('.pp-advanced-menu-mobile-toggle').trigger('click');
					}
				}, 600);

				FLBuilder.addHook('settings-form-init', function() {
					if ( ! $('.fl-builder-settings[data-node="'+self.settingsId+'"]').length > 0 ) {
						return;
					}
					if ( ! $(self.nodeClass).find('.pp-advanced-menu').hasClass('menu-open') ) {
						$('.pp-advanced-menu').removeClass('menu-open');
						$(self.nodeClass).find('.pp-advanced-menu-mobile-toggle').trigger('click');
					}
				});

				if ( $('html').hasClass('pp-full-screen-menu-open') && ! $(self.nodeClass).find('.pp-advanced-menu').hasClass('menu-open') ) {
					$('html').removeClass('pp-full-screen-menu-open');
				}
				if ( $('html').hasClass('pp-off-canvas-menu-open') && ! $(self.nodeClass).find('.pp-advanced-menu').hasClass('menu-open') ) {
					$('html').removeClass('pp-off-canvas-menu-open');
				}
			}
		}

	};

})(jQuery);
