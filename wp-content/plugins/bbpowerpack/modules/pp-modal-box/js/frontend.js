/**
 * This file should contain frontend logic for
 * all module instances.
 */
var PPModal;
;(function($) {
    PPModal = {
        settings: {},
        isActive: false,
        init: function(data) {
            PPModal.settings = data;
            if(parseInt(PPModal.settings.display_after) === 0 || PPModal.settings.display_after < 0 || PPModal.settings.display_after === '') {
                PPModal.cookie.remove();
            }
            if((PPModal.settings.exit_intent || PPModal.settings.auto_load) && PPModal.cookie.get() && !PPModal.settings.previewing){
                return;
            }
            if(PPModal.isActive) {
                return;
            }
            PPModal.responsive();
            PPModal.show();
            PPModal.adjust();
            PPModal.events();
        },
        modal: function() {
            return $('#modal-'+PPModal.settings.id+' .pp-modal');
        },
        show: function() {
            if('fullscreen' !== PPModal.settings.layout) {
                if ( typeof PPModal.settings.height === 'undefined' ) {
                    var $clone = PPModal.modal().clone().css({
                        display: 'block',
                        position: 'absolute',
                        top: '-99999px',
                        width: PPModal.settings.width + 'px',
                        visibility: 'hidden'
                    }).addClass('pp-modal-clone');
                    if('photo' === PPModal.settings.type) {
                        $clone.find('.pp-modal-content-inner img').css('max-width', '100%');
                    }
                    $('body').append($clone);
                    var topPos = ($(window).height() - $clone.find('.pp-modal-body').outerHeight())/2;
                    if ( topPos < 0 ) {
                        topPos = 0;
                    }
                    PPModal.modal().css('top', topPos + 'px');
                    //console.log($clone.find('.pp-modal-body').outerHeight());
                    $clone.remove();
                } else {
                    PPModal.modal().css('top', ($(window).height() - PPModal.settings.height)/2 + 'px');
                }
            }
            setTimeout(function(){
                $('#modal-'+PPModal.settings.id).fadeIn(400);
                PPModal.modal()
                    .removeClass(PPModal.settings.animation_load+' animated')
                    .addClass('modal-visible')
                    .addClass(PPModal.settings.animation_load+' animated')
                    .one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function() {
                        $(this).removeClass(PPModal.settings.animation_load+' animated');
                        if('url' == PPModal.settings.type) {
                            var src = PPModal.modal().find('.pp-modal-iframe').data('src');
                            if(PPModal.modal().find('.pp-modal-iframe').attr('src') === undefined) {
                                PPModal.modal().find('.pp-modal-iframe').attr('src', src);
                            }
                        }
                        if('video' == PPModal.settings.type) {
                            var src = '';
                            if(PPModal.modal().find('iframe, source').attr('src') === undefined) {
                                src = PPModal.modal().find('iframe, source').data('src');
                            } else {
                                src = PPModal.modal().find('iframe, source').attr('src');
                            }
                            if((src.search('youtube') !== -1 || src.search('vimeo') !== -1) && src.search('autoplay=1') == -1) {
                                if(typeof src.split('?')[1] === 'string') {
                                    src = src + '&autoplay=1&rel=0';
                                } else {
                                    src = src + '?autoplay=1&rel=0';
                                }
                            }
                            PPModal.modal().find('iframe, source').attr('src', src);
                            if(PPModal.modal().find('video').length) {
                                PPModal.modal().find('video')[0].play();
                            }
                        }
                    });

                PPModal.isActive = true;
                if(PPModal.settings.exit_intent || PPModal.settings.auto_load){
                    PPModal.cookie.set();
                }
            }, PPModal.settings.auto_load ? parseFloat(PPModal.settings.delay) * 1000 : 0);
        },
        hide: function() {
            PPModal.modal()
                .removeClass(PPModal.settings.animation_exit+' animated')
                .addClass(PPModal.settings.animation_exit+' animated')
                .one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function() {
                    $(this).removeClass(PPModal.settings.animation_exit+' animated').removeClass('modal-visible');
                    $(this).find('.pp-modal-content').removeAttr('style');
                    $('#modal-'+PPModal.settings.id).fadeOut(100);
                    PPModal.isActive = false;
                    PPModal.reset();
                });
        },
        adjust: function() {
            var mH = 0, hH = 0, cH = 0, eq = 0;
            setTimeout(function(){
                if(PPModal.isActive) {
                    if('fullscreen' === PPModal.settings.layout){
                        var marginTop = parseInt(PPModal.modal().css('margin-top'));
                        var marginBottom = parseInt(PPModal.modal().css('margin-bottom'));
                        var modalHeight = $(window).height() - (marginTop + marginBottom);
                        PPModal.modal().css('height', modalHeight + 'px');
                    }
                    eq = 6;
                    mH = PPModal.modal().outerHeight(); // Modal height.
                    hH = PPModal.modal().find('.pp-modal-header').outerHeight(); // Header height.

                    if(PPModal.settings.auto_height && 'fullscreen' !== PPModal.settings.layout) {
                        return;
                    }
                    cP = parseInt(PPModal.modal().find('.pp-modal-content').css('padding')); // Content padding.
                    PPModal.modal().find('.pp-modal-content').css('height', mH - (hH + eq) + 'px');
                    if (!PPModal.settings.auto_height && PPModal.modal().find('.pp-modal-header').length === 0) {
                        PPModal.modal().find('.pp-modal-content').css('height', mH + 'px');
                    }
                    // Adjust iframe height.
                    if('url' === PPModal.settings.type) {
                        PPModal.modal().find('.pp-modal-iframe').css('height', PPModal.modal().find('.pp-modal-content-inner').outerHeight() + 'px');
                    }
                    if('video' === PPModal.settings.type) {
                        PPModal.modal().find('iframe').css({'height':'100%', 'width':'100%'});
                    }
                }
            }, PPModal.settings.auto_load ? parseFloat(PPModal.settings.delay) * 1000 : 0);
        },
        responsive: function() {
            if($(window).width() <= PPModal.settings.breakpoint){
                PPModal.modal().removeClass('layout-standard').addClass('layout-fullscreen');
            }
        },
        events: function() {
            $(document).keyup(function(e) {
                if(PPModal.settings.esc_exit && 27 == e.which && PPModal.isActive && $('form[data-type="pp-modal-box"]').length === 0) {
                    PPModal.hide();
                }
            });
            $('.pp-modal-close').on('click', function() {
                PPModal.hide();
            });
            $(document).on('click', function(e) {
                if (PPModal.settings.click_exit && PPModal.isActive && !PPModal.settings.previewing && !PPModal.modal().is(e.target) && PPModal.modal().has(e.target).length === 0) {
                    PPModal.hide();
                }
            });
        },
        cookie: {
            set: function() {
                if(parseInt(PPModal.settings.display_after) > 0) {
                    return $.cookie('pp_modal_'+PPModal.settings.id, PPModal.settings.display_after, {expires: PPModal.settings.display_after, path: '/'});
                } else {
                    PPModal.cookie.remove();
                }
            },
            get: function() {
                return $.cookie('pp_modal_'+PPModal.settings.id);
            },
            remove: function() {
                return $.cookie('pp_modal_'+PPModal.settings.id, 0, {expires: 0, path: '/'});
            }
        },
        reset: function() {
            if('url' == PPModal.settings.type || 'video' == PPModal.settings.type) {
                var src = PPModal.modal().find('iframe, source').attr('src');
                PPModal.modal().find('iframe, source').attr('data-src', src).removeAttr('src');
                if(PPModal.modal().find('video').length) {
                    PPModal.modal().find('video')[0].pause();
                }
            }
            PPModal.settings = {};
        }
    };
})(jQuery);
