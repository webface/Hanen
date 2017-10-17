(function($) {

  /**
   * animated Text Prototype
   *
   */
	PPAnimatedText = function( settings ){

		this.settings           = settings;
		this.viewport_position  =  90;
		this.animation          = settings.animation;
		this.nodeClass          = '.fl-node-' + settings.id;

		/* Type Var */
		if ( settings.animation == 'type' ) {
			this.strings     = settings.strings;
			this.typeSpeed   = settings.typeSpeed;
			this.startDelay  = settings.startDelay;
			this.backSpeed   = settings.backSpeed;
			this.backDelay   = settings.backDelay;
			this.loop        = settings.loop;
			// this.loopCount   = settings.loopCount;
			this.showCursor  = settings.showCursor;
			this.cursorChar  = settings.cursorChar;
		} else if ( settings.animation == 'slide' ) {
			this.speed       = settings.speed;
			this.pause       = settings.pause;
			this.mousePause  = settings.mousePause;
		} else if ( settings.animation == 'fade' ) {
			this.strings     = settings.strings;
			this.speed     	= settings.speed;
		}

	    /* Initialize Animation */
	    this._initAnimatedText();

	};

  	PPAnimatedText.prototype = {
	    settings        : {},
	    nodeClass       : '',
	    viewport_position : 90,
	    animation       : 'type',

	    /* Type Var */
	    strings     : '',
	    typeSpeed   : '',
	    startDelay  : '',
	    backSpeed   : '',
	    backDelay   : '',
	    loop        : '',
	    loopCount   : '',
	    showCursor  : '',
	    cursorChar  : '',

	    /* SLide Up var */
	    speed       : '',
	    pause       : '',
	    mousePause  : '',

    	_initAnimatedText: function(){

	    	if( typeof jQuery.fn.waypoint !== 'undefined' ) {
				$(this.nodeClass).waypoint({
	          		offset: this.viewport_position + '%',
	          		handler: $.proxy( this._triggerAnimation, this )
	        	});
	      	}
	    },

	    _triggerAnimation: function() {
			if ( this.animation == 'type' ) {
	       		$( this.nodeClass + " .pp-typed-main" ).typed({
					strings: this.strings,
					typeSpeed: this.typeSpeed,
					startDelay: this.startDelay,
					backSpeed: this.backSpeed,
					backDelay: this.backDelay,
					loop: this.loop,
					showCursor: this.showCursor,
					cursorChar: this.cursorChar,
				});
	      	} else if ( this.animation == 'slide_up' ) {
				var options = {
				  	speed       : this.speed,
				  	pause       : this.pause,
				  	mousePause  : this.mousePause,
			  	};
	       		$( this.nodeClass + " .pp-slide-main")
	              .vTicker('init', options);
			} else if ( this.animation == 'fade' ) {
				var i = 0;
				this._triggerFadeAnimation(i);
			}
	    },

		_triggerFadeAnimation: function(i)
		{
			var self = this;
			var strings = this.strings;
			i++;
			$( this.nodeClass + " .pp-fade-main" )
				.fadeOut(self.speed, function() {
					$(this).html( strings[i % strings.length] );
					$(this).fadeIn(self.speed, function() {
						self._triggerFadeAnimation(i);
					});
				});
		}
  	};

})(jQuery);
