;(function ($) {

    PPImageCarousel = function (settings) {
        this.id = settings.id;
        this.nodeClass = '.fl-node-' + settings.id;
        this.wrapperClass = this.nodeClass + ' .pp-image-carousel';
        this.slidesPerView = settings.slidesPerView;
        this.settings = settings;

        if (this._isSlideshow()) {
            this.slidesPerView = settings.slideshow_slidesPerView;
        }

        this._init();
    };

    PPImageCarousel.prototype = {
        id: '',
        nodeClass: '',
        wrapperClass: '',
        elements: '',
        slidesPerView: {},
        settings: {},
        swipers: {},

        _init: function () {
            this.elements = {
                mainSwiper: this.nodeClass + ' .pp-image-carousel'
            };

            this.elements.swiperSlide = $(this.elements.mainSwiper).find('.swiper-slide');
            this.elements.thumbSwiper = this.nodeClass + ' .pp-thumbnails-swiper';

            if (1 >= this._getSlidesCount()) {
                return;
            }

            var swiperOptions = this._getSwiperOptions();

            this.swipers.main = new Swiper(this.elements.mainSwiper, swiperOptions.main);

            if (this._isSlideshow()) {
                this.swipers.main.params.control = this.swipers.thumbs = new Swiper(this.elements.thumbSwiper, swiperOptions.thumbs);
                this.swipers.thumbs.params.control = this.swipers.main;
            }
        },

        _getSlidesCount: function () {
            return this.elements.swiperSlide.length;
        },

        _getInitialSlide: function () {
            return this.settings.initialSlide;
        },

        _getSpaceBetween: function () {
            var space = this.settings.spaceBetween.desktop,
                space = parseInt(space);

            if ( isNaN( space ) ) {
                space = 20;
            }

            return space;
        },

        _getSpaceBetweenTablet: function () {
            var space = this.settings.spaceBetween.tablet,
                space = parseInt(space);

            if ( isNaN(space) ) {
                space = this._getSpaceBetween();
            }

            return space;
        },

        _getSpaceBetweenMobile: function () {
            var space = this.settings.spaceBetween.mobile,
                space = parseInt(space);

            if ( isNaN(space) ) {
                space = this._getSpaceBetweenTablet();
            }

            return space;
        },

        _getSlidesPerView: function () {
            var slidesPerView = this.slidesPerView.desktop;

            return Math.min(this._getSlidesCount(), +slidesPerView);
        },

        _getSlidesPerViewTablet: function () {
            var slidesPerView = this.slidesPerView.tablet;

            if (slidesPerView === '' || slidesPerView === 0) {
                slidesPerView = this.slidesPerView.desktop
            }

            if (!slidesPerView && 'coverflow' === this.settings.type) {
                return Math.min(this._getSlidesCount(), 3);
            }

            return Math.min(this._getSlidesCount(), +slidesPerView);
        },

        _getSlidesPerViewMobile: function () {
            var slidesPerView = this.slidesPerView.mobile;

            if (slidesPerView === '' || slidesPerView === 0) {
                slidesPerView = this._getSlidesPerViewTablet();
            }

            if (!slidesPerView && 'coverflow' === this.settings.type) {
                return Math.min(this._getSlidesCount(), 3);
            }

            return Math.min(this._getSlidesCount(), +slidesPerView);
        },

        _getSwiperOptions: function () {
            var medium_breakpoint = this.settings.breakpoint.medium,
                responsive_breakpoint = this.settings.breakpoint.responsive;

            var options = {
                effect: this.settings.effect,
                pagination: '.swiper-pagination',
                nextButton: this.nodeClass + ' .pp-swiper-button-next',
                prevButton: this.nodeClass + ' .pp-swiper-button-prev',
                paginationClickable: true,
                grabCursor: true,
                initialSlide: this._getInitialSlide(),
                slidesPerView: this._getSlidesPerView(),
                spaceBetween: this._getSpaceBetween(),
                paginationType: this.settings.pagination,
                autoplay: this.settings.isBuilderActive ? 0 : this.settings.autoplay_speed,
                autoplayDisableOnInteraction: this.settings.pause_on_interaction,
                loop: true,
                loopedSlides: this._getSlidesCount(),
                speed: this.settings.speed,
                breakpoints: {}
            };

            options.breakpoints[medium_breakpoint] = {
                slidesPerView: this._getSlidesPerViewTablet(),
                spaceBetween: this._getSpaceBetweenTablet()
            };
            options.breakpoints[responsive_breakpoint] = {
                slidesPerView: this._getSlidesPerViewMobile(),
                spaceBetween: this._getSpaceBetweenMobile()
            };

            var thumbsSliderOptions = {
                slidesPerView: this._getSlidesPerView(),
                initialSlide: this._getInitialSlide(),
                centeredSlides: true,
                slideToClickedSlide: true,
                spaceBetween: this._getSpaceBetween(),
                loop: true,
                loopedSlides: this._getSlidesCount(),
                speed: this.settings.speed,
                onSlideChangeEnd: function (swiper) {
                    swiper.fixLoop();
                },
                breakpoints: {}
            };

            thumbsSliderOptions.breakpoints[medium_breakpoint] = {
                slidesPerView: this._getSlidesPerViewTablet(),
                spaceBetween: this._getSpaceBetweenTablet()
            };
            thumbsSliderOptions.breakpoints[responsive_breakpoint] = {
                slidesPerView: this._getSlidesPerViewMobile(),
                spaceBetween: this._getSpaceBetweenMobile()
            };

            if ('coverflow' === this.settings.type) {
                options.effect = 'coverflow';
            }

            if (this._isSlideshow()) {
                options.slidesPerView = 1;

                delete options.pagination;
                delete options.breakpoints;
            }

            return {
                main: options,
                thumbs: thumbsSliderOptions
            };
        },

        _isSlideshow: function () {
            return 'slideshow' === this.settings.type;
        },

        _onElementChange: function (property) {
            if (0 === property.indexOf('width')) {
                this.swipers.main.onResize();
            }

            if (0 === property.indexOf('spaceBetween')) {
                this._updateSpaceBetween(this.swipers.main, property);
            }
        },

        _updateSpaceBetween: function (swiper, property) {
            var newSpaceBw = this._getSpaceBetween(),
                deviceMatch = property.match('space_between_(.*)');

            if (deviceMatch) {
                var breakpoints = {
                    tablet: this.settings.breakpoint.medium,
                    mobile: this.settings.breakpoint.responsive
                };

                swiper.params.breakpoints[breakpoints[deviceMatch[1]]].spaceBetween = newSpaceBw;
            } else {
                swiper.originalParams.spaceBetween = newSpaceBw;
            }

            swiper.params.spaceBetween = newSpaceBw;

            swiper.onResize();
        },
    };

})(jQuery);