<div class="breadcrumb">
  <?= CRUMB_DASHBOARD ?>    
  <?= CRUMB_SEPARATOR ?>  
    <span class="current"><?= __("Customer Success", "EOT_LMS"); ?></span>     
</div>
<?php
  // verify this user has access to this portal/subscription/page/view
  $true_subscription = verifyUserAccess(); 

  // Variable declaration
  global $current_user;
  $page_title = __("Customer Success Videos", "EOT_LMS");

  // Check if the subscription ID is valid.
  if(isset($_REQUEST['subscription_id']) && $_REQUEST['subscription_id'] > 0)
  { 

    echo '<h1 class="article_page_title">'.$page_title.'</h1>';       
?>
        <p><?= __("Play these quick videos to hear insider tips on harnessing the power of online learning. Ten transformative ideas for staff success!", "EOT_LMS"); ?></p>
        <p>&nbsp;</p>
<style type="text/css">
/* Fullscreen
--------------------------------------------------------------------------------
*/
.vjs-default-skin .vjs-fullscreen-control {
  display: block;
}
.vjs-default-skin .vjs-fullscreen-control:before {
  display: block;
}
/* Switch to the exit icon when the player is in fullscreen */
.vjs-default-skin.vjs-fullscreen .vjs-fullscreen-control:before {
  display: block;
}
/* Fullscreen Styles */
body.vjs-full-window {
  display: block;
}
.video-js.vjs-fullscreen {
  display: block;
}
.video-js:-webkit-full-screen {
  display: block;
}

div#accountAreaBottom ul li {
  margin-left: 20px;
}


div#main-content {
  width: 1400px;
}
div.entry-content {
  width:905px;
}
div#col1 {
  width:1000px !important;
}
.video-dimensions{
  width:60%;
  height:320px;
}
.playlist-components {
  height:100%;
}
  .video-holder, .video-holder * {box-sizing: border-box !important}
  .video-holder {background: #1b1b1b; padding: 10px}
  .centered {width: 100%}
  #video {border-radius: 8px}
  .video-holder .vjs-big-play-button { top:30px; left:50px; float:left;}

  .playlist-components {
      width: 40%;
      padding: 0 0 0 10px
  }
  .video-js, .playlist-components {
      display: inline-block;
      margin-right: -4px;
      vertical-align: top;
  }
  .button-holder {
      padding: 10px;
      height: 36px;
  }

  .playlist {
      height: 320px;
      width: 100%;
      overflow-y: auto;
      color: #c0c0c0;
      border-radius: 8px;
      display: block;
      margin: -2px 0 0 0;
      padding: 0;
      position: relative;
      background: -moz-linear-gradient(top,#000 0,#212121 19%,#212121 100%);
      background: -webkit-gradient(linear,left top,left bottom,color-stop(0%,#000),color-stop(19%,#212121),color-stop(100%,#212121));
      background: -o-linear-gradient(top,#000 0,#212121 19%,#212121 100%);
      background: -ms-linear-gradient(top,#000 0,#212121 19%,#212121 100%);
      background: linear-gradient(to bottom,#000 0,#212121 19%,#212121 100%);
      box-shadow: 0 1px 1px #1a1a1a inset,0px 1px 1px #454545;
      border: 1px solid #1a1a18;
  }
  #next {float: right}
  #prev {float: left}
  #prev, #next {
      cursor: pointer;
      color: white;
      text-transform: uppercase;
      font-size: 12px;
  }

  .playlist ul, li {
      padding: 0;
      margin: 0;
      list-style: none;
      list-style-type:none;
  }
  .playlist ul li {
      padding: 10px;
      border-bottom: 1px solid #191919;
      cursor: pointer
  }
  .playlist ul li.active {
      background-color: #4f4f4f;
      border-color: #4f4f4f;
      color: white;
  }
  .playlist ul li:hover {
      border-color: #353535;
      background: #353535;
  }
  .playlist .poster, .playlist .title  {
      display: inline-block;
      vertical-align: top
  }
  .playlist .number {padding-right: 10px; display: none}
  .playlist .poster {display: none}
  .playlist .title {padding-left: 0}  
    /* Ipad */
  @media only screen and (max-width: 1024px) {
      div#main-content {
        width: 700px;
      }
      div.entry-content {
        width:700px;
      }
      div#col1 {
        width:700px;
      }
      .playlist-components {
        display:block;
      }
      .video-dimensions{
      width:100%;
      height:320px;
      }
      .playlist-components {
      width: 100%;
      padding: 0 0 0 10px
      }
      #header_icons,
      #mynav,
      #mynav #login-button,
      #mynav #logout-button,
      #feedjit-left-box,
      .sidebar,
      .sidebar_bg {
        display:none;
      }
      #header .menu,
      #header .login,
      #header .logout {
        display:block;
      }
      #wrapper, #mynav {
        width:100%;
      }
      #wrapper {
        margin-top:55px;
      }
      #header {
        background:#fff;
        position:fixed;
        height:55px;
        z-index: 1000;
        width: 100%;
        top: 0;
        left: 0;
      }
      #header .menu {
        width:40px;
        height:35px;
        position:absolute;
        top:10px;
        left:5px;
        background:url(images/menu.png) no-repeat center center;
        text-indent:-9999px;
      }
      #header .login,
      #header .logout {
        width:35px;
        height:35px;
        position:absolute;
        top:10px;
        right:5px;
        background:url(images/login.png) no-repeat center center;
        text-indent:-9999px;
      }
      #header .logout {
        background:url(images/logout.png) no-repeat center center;
      }
      #logo {
        display:block;
        margin:10px auto 10px auto;
        background-image:url(images/eot_logo_tablet.png);
        width:205px;
        height:35px;
      }
      #mynav {
        height:auto;
        border-radius:0px;
        background-color:#fff;
        position: fixed;
        top: 55px;
        left: 0px;
        z-index: 1000;
      }
      .main_menu {
        float:none;
        margin:0;
        border-top:2px solid #1a81c6;
      }
      .main_menu li {
        display:block;
        border-bottom:2px solid #1a81c6;
        background:transparent;
      }
      .main_menu li:hover {
        background:transparent;
      }
      .main_menu li a {
        color:#1a81c6;
        width:100%;
        text-indent:5px;
        padding-left:0px;
        padding-right:0px;
        text-shadow:none;
        font-weight:bold;
      }
      #bannershowcase {
        padding-left:10px;
        padding-right:10px;
        margin-top:0px;
      }
      .learn-more, .sample-videos {
        margin-left:0px;
      }

      /* staff slider */
      .staff_slider_container {
        margin: 0 auto 0 auto;
        width: 700px;
        min-height: 72px;
        float:none;
        padding-bottom:15px;
      }
      .slider_wrapper p {
        float: none;
        padding: 10px 0px 10px 10px;
        line-height:24px;
        height:auto;
      }
      p.captionname {
        padding:0;
      }

      /* home content */
      #homecontent.plus-feedjit {
        width: 96%;
        padding-left: 2%;
        padding-right: 2%;
        margin: 0 0 15px 0;
        float:none;
      }
      #homecontent.plus-feedjit h1 {
        width: 100% !important;
      }
      #homecontent img {
        max-width:100%;
        height:auto;
      }
      #testimonialbox {
        margin:0;
        float: none;
        width:96%;
        padding-left:2%;
        padding-right:2%;
      }

      .s-c-x #col1, #colright {
        width:100%;
      }
      .component-pad {
        padding-left:0px;
        padding-right:0px;
      }
      #col1wrap {
        width:96%;
        padding-left:2%;
        padding-right:2%;
        float:none;
      }

  }
  /* High end windows phone */
  @media only screen and (max-width: 768px) {
      div#main-content {
        width: 600px;
      }
      div.entry-content {
        width:600px;
      }
      div#col1 {
        width:600px;
      }
      .playlist-components {
        display:block;
      }
      .video-dimensions{
      width:100%;
      height:320px;
      }
      .playlist-components {
      width: 100%;
      padding: 0 0 0 10px
      }
  }
    /* High end windows phone */
  @media only screen and (max-width: 575px) {
      div#main-content {
        width: 350px;
      }
      div.entry-content {
        width:350px;
      }
      div#col1 {
        width:300px;
      }
      .playlist-components {
        display:block;
      }
      .video-dimensions{
      width:100%;
      height:320px;
      }
      .playlist-components {
      width: 100%;
      padding: 0 0 0 10px
      }
  }
      /* High end windows phone */
  @media only screen and (max-width: 320px) {
      div#main-content {
        width: 300px;
      }
      div.entry-content {
        width:300px;
      }
      div#col1 {
        width:300px;
      }
      .playlist-components {
        display:block;
      }
      .video-dimensions{
      width:100%;
      height:320px;
      }
      .playlist-components {
      width: 100%;
      padding: 0 0 0 10px
      }
  }
</style>

<div class="video-holder centered">
  <video id="video" class="video-js vjs-default-skin vjs-big-play-centered"
               controls preload="auto" width="60%" height="264"
               data-setup=""
               poster="">
  </video>
  <div class="playlist-components">
    <div class="playlist">
      <ul></ul>
    </div>
    <!--
    <div class="button-holder">
      <span id="prev">Prev</span>
      <span id="next">Next</span>
    </div>
    -->
  </div>
</div>

<script type="text/javascript">

  //videojs-playlists.js
  function playList(options,arg){
    var player = this;
    player.pl = player.pl || {};
    var index = parseInt(options,10);

    player.pl._guessVideoType = function(video){
      var videoTypes = {
        'mp4' : 'video/mp4',
        'webm' : 'video/webm',
        'ogv' : 'video/ogg'
      };
      var extension = video.split('.').pop();

      return videoTypes[extension] || '';
    };

    player.pl.init = function(videos, options) {
      options = options || {};
      player.pl.videos = [];
      player.pl.current = 0;
      player.on('ended', player.pl._videoEnd);

      if (options.getVideoSource) {
        player.pl.getVideoSource = options.getVideoSource;
      }

      player.pl._addVideos(videos);
    };

    player.pl._updatePoster = function(posterURL) {
      player.poster(posterURL);
      player.removeChild(player.posterImage);
      player.posterImage = player.addChild("posterImage");
    };

    player.pl._addVideos = function(videos){
      for (var i = 0, length = videos.length; i < length; i++){
        var aux = [];
        for (var j = 0, len = videos[i].src.length; j < len; j++){
          aux.push({
            type : player.pl._guessVideoType(videos[i].src[j]),
            src : videos[i].src[j]
          });
        }
        videos[i].src = aux;
        player.pl.videos.push(videos[i]);
      }
    };

    player.pl._nextPrev = function(func){
      var comparison, addendum;

      if (func === 'next'){
        comparison = player.pl.videos.length -1;
        addendum = 1;
      }
      else {
        comparison = 0;
        addendum = -1;
      }

      if (player.pl.current !== comparison){
        var newIndex = player.pl.current + addendum;
        player.pl._setVideo(newIndex);
        player.trigger(func, [player.pl.videos[newIndex]]);
      }
    };

    player.pl._setVideo = function(index){
      if (index < player.pl.videos.length){
        player.pl.current = index;
        player.pl.currentVideo = player.pl.videos[index];

        if (!player.paused()){
          player.pl._resumeVideo();
        }

        if (player.pl.getVideoSource) {
          player.pl.getVideoSource(player.pl.videos[index], function(src, poster) {
            player.pl._setVideoSource(src, poster);
          });
        } else {
          player.pl._setVideoSource(player.pl.videos[index].src, player.pl.videos[index].poster);
        }
      }
    };

    player.pl._setVideoSource = function(src, poster) {
      player.src(src);
      player.pl._updatePoster(poster);
    };

    player.pl._resumeVideo = function(){
      player.one('loadstart',function(){
        player.play();
      });
    };

    player.pl._videoEnd = function(){
      if (player.pl.current === player.pl.videos.length -1){
        player.trigger('lastVideoEnded');
      }
      else {
        player.pl._resumeVideo();
        player.next();
      }
    };

    if (options instanceof Array){
      player.pl.init(options, arg);
      player.pl._setVideo(0);
      return player;
    }
    else if (index === index){ // NaN
      player.pl._setVideo(index);
      return player;
    }
    else if (typeof options === 'string' && typeof player.pl[options] !== 'undefined'){
      player.pl[options].apply(player);
      return player;
    }
  }
/*
  videojs.Player.prototype.next = function(){
    this.pl._nextPrev('next');
    return this;
  };
  videojs.Player.prototype.prev = function(){
    this.pl._nextPrev('prev');
    return this;
  };
*/
  videojs.plugin('playList', playList);


  (function(){
    var videos = [
      {
        src : [
          'https://eot-output.s3.amazonaws.com/CS_When_to_Give.mp4'
        ],
        poster : 'https://www.expertonlinetraining.com/wp-content/uploads/2016/09/EOT-Clear.png',
        title : '<?= __("When should I give EOT courses to my staff?", "EOT_LMS"); ?>'
      },
      {
        src : [
          'https://eot-output.s3.amazonaws.com/CS_Accreditation_Help.mp4'
        ],
        poster : 'https://www.expertonlinetraining.com/wp-content/uploads/2016/09/EOT-Clear.png',
        title : '<?= __("How does EOT help with accreditation?", "EOT_LMS"); ?>'
      },
      {
        src : [
          'https://eot-output.s3.amazonaws.com/CS_How_Many_Modules.mp4'
        ],
        poster : 'https://www.expertonlinetraining.com/wp-content/uploads/2016/09/EOT-Clear.png',
        title : '<?= __("How many modules should I assign?", "EOT_LMS"); ?>'
      },
      {
        src : [
          'https://eot-output.s3.amazonaws.com/CS_International_Staff.mp4'
        ],
        poster : 'https://www.expertonlinetraining.com/wp-content/uploads/2016/09/EOT-Clear.png',
        title : '<?= __("Should I give EOT to my international staff?", "EOT_LMS"); ?>'
      },
      {
        src : [
          'https://eot-output.s3.amazonaws.com/CS_Module_Selection.mp4'
        ],
        poster : 'https://www.expertonlinetraining.com/wp-content/uploads/2016/09/EOT-Clear.png',
        title : '<?= __("How do I choose which modules to assign?", "EOT_LMS"); ?>'
      },
      {
        src : [
          'https://eot-output.s3.amazonaws.com/CS_OnSite_Use_of_EOT.mp4'
        ],
        poster : 'https://www.expertonlinetraining.com/wp-content/uploads/2016/09/EOT-Clear.png',
        title : '<?= __("What are the best ways to use EOT for on-site training?", "EOT_LMS"); ?>'
      },
      {
        src : [
          'https://eot-output.s3.amazonaws.com/CS_Press_Play.mp4'
        ],
        poster : 'https://www.expertonlinetraining.com/wp-content/uploads/2016/09/EOT-Clear.png',
        title : '<?= __("What are the Press Play workshops?", "EOT_LMS"); ?>'
      },
      {
        src : [
          'https://eot-output.s3.amazonaws.com/CS_Staff_Complete_Course.mp4'
        ],
        poster : 'https://www.expertonlinetraining.com/wp-content/uploads/2016/09/EOT-Clear.png',
        title : '<?= __("How do I get my staff to complete their EOT assignment?", "EOT_LMS"); ?>'
      },
      {
        src : [
          'https://eot-output.s3.amazonaws.com/CS_Statistics_Benefits.mp4'
        ],
        poster : 'https://www.expertonlinetraining.com/wp-content/uploads/2016/09/EOT-Clear.png',
        title : '<?= __("How do the statistics functions help?", "EOT_LMS"); ?>'
      },
      {
        src : [
          'https://eot-output.s3.amazonaws.com/CS_What_Custom_Content.mp4'
        ],
        poster : 'https://www.expertonlinetraining.com/wp-content/uploads/2016/09/EOT-Clear.png',
        title : '<?= __("What custom content should I upload?", "EOT_LMS"); ?>'
      }
    ];


    var demoModule = {
      init : function(){
        this.els = {};
        this.cacheElements();
        this.initVideo();
        this.createListOfVideos();
        this.bindEvents();
        this.overwriteConsole();
      },
      overwriteConsole : function(){
        console._log = console.log;
        console.log = this.log;
      },
      log : function(string){
        demoModule.els.log.append('<p>' + string + '</p>');
        console._log(string);
      },
      cacheElements : function(){
        this.els.$playlist = $('div.playlist > ul');
        this.els.$next = $('#next');
        this.els.$prev = $('#prev');
        this.els.log = $('div.panels > pre');
      },
      initVideo : function(){
        this.player = videojs('video');
        this.player.playList(videos);
      },
      createListOfVideos : function(){
        var html = '';
        for (var i = 0, len = this.player.pl.videos.length; i < len; i++){
          html += '<li data-videoplaylist="'+ i +'">'+
                    '<span class="number">' + (i + 1) + '</span>'+
                    '<span class="poster"><img src="'+ videos[i].poster +'"></span>' +
                    '<span class="title">'+ videos[i].title +'</span>' +
                  '</li>';
        }
        this.els.$playlist.empty().html(html);
        this.updateActiveVideo();
      },
      updateActiveVideo : function(){
        var activeIndex = this.player.pl.current;

        this.els.$playlist.find('li').removeClass('active');
        this.els.$playlist.find('li[data-videoplaylist="' + activeIndex +'"]').addClass('active');
      },
      bindEvents : function(){
        var self = this;
        this.els.$playlist.find('li').on('click', $.proxy(this.selectVideo,this));
        this.els.$next.on('click', $.proxy(this.nextOrPrev,this));
        this.els.$prev.on('click', $.proxy(this.nextOrPrev,this));
        this.player.on('next', function(e){
          console.log('Next video');
          self.updateActiveVideo.apply(self);
        });
        this.player.on('prev', function(e){
          console.log('Previous video');
          self.updateActiveVideo.apply(self);
        });
        this.player.on('lastVideoEnded', function(e){
          console.log('Last video has finished');
        });
      },
      nextOrPrev : function(e){
        var clicked = $(e.target);
        this.player[clicked.attr('id')]();
      },
      selectVideo : function(e){
        var clicked = e.target.nodeName === 'LI' ? $(e.target) : $(e.target).closest('li');

        if (!clicked.hasClass('active')){
          console.log('Selecting video');
          var videoIndex = clicked.data('videoplaylist');
          this.player.playList(videoIndex);
          this.updateActiveVideo();
        }
      }
    };

    demoModule.init();
  })(jQuery);  
</script>

<?php
  }
  else
  {
    echo __("Invalid subscription ID.", "EOT_LMS");
  }
?>
