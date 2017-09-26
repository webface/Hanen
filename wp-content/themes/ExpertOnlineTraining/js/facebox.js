/*
 * Facebox (for jQuery)
 * version: 1.2 (05/05/2008)
 * @requires jQuery v1.2 or later
 *
 * Examples at http://famspam.com/facebox/
 *
 * Licensed under the MIT:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 * Copyright 2007, 2008 Chris Wanstrath [ chris@ozmm.org ]
 *
 * Usage:
 *  
 *  jQuery(document).ready(function() {
 *    jQuery('a[rel*=facebox]').facebox() 
 *  })
 *
 *  <a href="#terms" rel="facebox">Terms</a>
 *    Loads the #terms div in the box
 *
 *  <a href="terms.html" rel="facebox">Terms</a>
 *    Loads the terms.html page in the box
 *
 *  <a href="terms.png" rel="facebox">Terms</a>
 *    Loads the terms.png image in the box
 *
 *
 *  You can also use it programmatically:
 * 
 *    jQuery.facebox('some html')
 *
 *  The above will open a facebox with "some html" as the content.
 *    
 *    jQuery.facebox(function($) { 
 *      $.get('blah.html', function(data) { $.facebox(data) })
 *    })
 *
 *  The above will show a loading screen before the passed function is called,
 *  allowing for a better ajaxy experience.
 *
 *  The facebox function can also display an ajax page or image:
 *  
 *    jQuery.facebox({ ajax: 'remote.html' })
 *    jQuery.facebox({ image: 'dude.jpg' })
 *
 *  Want to close the facebox?  Trigger the 'close.facebox' document event:
 *
 *    jQuery(document).trigger('close.facebox')
 *
 *  Facebox also has a bunch of other hooks:
 *
 *    loading.facebox
 *    beforeReveal.facebox
 *    reveal.facebox (aliased as 'afterReveal.facebox')
 *    init.facebox
 *
 *  Simply bind a function to any of these hooks:
 *
 *   $(document).bind('reveal.facebox', function() { ...stuff to do after the facebox and contents are revealed... })
 *
 */
(function($) {
  $.facebox = function(data, klass,title,popup_footer, message) {
    $.facebox.loading(title, message)

    if (data.ajax) fillFaceboxFromAjax(data.ajax)
    else if (data.image) fillFaceboxFromImage(data.image)
    else if (data.div) fillFaceboxFromHref(data.div)
    else if ($.isFunction(data)) data.call($)
    else $.facebox.reveal(data, klass,title,popup_footer)
  }
  
  /*
   * Public, $.facebox methods
   */

  $.extend($.facebox, {
    settings: {
      opacity      : 0.4,
      overlay      : true,
      loadingImage : '',
      closeImage   : ajax_object.template_url + '/images/closelabel.png',
      imageTypes   : [ 'png', 'jpg', 'jpeg', 'gif' ],
      faceboxHtml  : '\
    <div id="facebox" style="display:none;"> \
      <div class="popup"> \
        <table> \
          <tbody> \
            <tr> \
              <td class="tl"/><td class="b"/><td class="tr"/> \
            </tr> \
            <tr> \
              <td class="b"/> \
              <td class="body"> \
                <div class="content"> \
                </div> \
              </td> \
              <td class="b"/> \
            </tr> \
            <tr> \
              <td class="bl"/><td class="b"/><td class="br"/> \
            </tr> \
          </tbody> \
        </table> \
      </div> \
    </div>'
    },

    loading: function(title, message) {
      init()
      if ($('#facebox .loading').length == 1) return true
      showOverlay()

      $('#facebox .content').empty()
      // Show the message if the title and message is set, otherwise just display a loading image.
      if(typeof title !== 'undefined' && typeof message !== 'undefined')
      {
        $('#facebox .body').children().hide().end().
        append('\
          <div class="loading"> \
            <div class="title"> \
              <div class="title_h2">'+title+'</div> \
            </div> \
            <div id="faceboxMiddle"> \
             <p>'+message+'</p> \
            </div> \
            <center> \
              <i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i> \
            </center> \
            <br /> \
          </div> ')
      }
      else
      {
        $('#facebox .body').children().hide().end().
        append('<div class="loading"><i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i> \
            <br /> \
          </div> ')
      }
      $('#facebox').css({
        top:	getPageScroll()[1] + (getPageHeight() / 10),
        left:	600.5
      }).show()

      $(document).bind('keydown.facebox', function(e) {
        if (e.keyCode == 27) $.facebox.close()
        return true
      })
      $(document).trigger('loading.facebox')
    },

    reveal: function(data, klass, title,popup_footer) {
      // Session died assuming the user is logged out and no privilege to see ajax calls.
      if(data == 0)
      { 
        alert("You have been logged out. You didn't have any activity for a certain period of time. Please log in again.");
        window.location.href = ajax_object.login_url; // Redirect to login page.
        return false;
      }
      $(document).trigger('beforeReveal.facebox')
      if (klass) $('#facebox .content').addClass(klass)
      $('#facebox .content').empty()//prevent multiple content from appearing due to datatable paging.
      $('#facebox .content').append(data)
      $('#facebox .loading').remove()
      $('#facebox .body').children().slideDown(0)
      $('#facebox').css('left', $(window).width() / 2 - ($('#facebox table').width() / 2))
      $(document).trigger('reveal.facebox').trigger('afterReveal.facebox')
    },

    close: function() {
      $(document).trigger('close.facebox')
      return false
    }
  })

  /*
   * Public, $.fn methods
   */

  $.fn.facebox = function(settings) {
    init(settings)

    function clickHandler() {
      $.facebox.loading(true)

      // support for rel="facebox.inline_popup" syntax, to add a class
      // also supports deprecated "facebox[.inline_popup]" syntax
      var klass = this.rel.match(/facebox\[?\.(\w+)\]?/)
      if (klass) klass = klass[1]

      fillFaceboxFromHref(this.href, klass)
      return false
    }

    return this.click(clickHandler)
  }

  /*
   * Private methods
   */

  // called one time to setup facebox on this page
  function init(settings) {
    if ($.facebox.settings.inited) return true
    else $.facebox.settings.inited = true

    $(document).trigger('init.facebox')
    makeCompatible()

    var imageTypes = $.facebox.settings.imageTypes.join('|')
    $.facebox.settings.imageTypesRegexp = new RegExp('\.' + imageTypes + '$', 'i')

    if (settings) $.extend($.facebox.settings, settings)
    $('body').append($.facebox.settings.faceboxHtml)

    var preload = [ new Image(), new Image() ]
    preload[0].src = $.facebox.settings.closeImage
    preload[1].src = $.facebox.settings.loadingImage

    $('#facebox').find('.b:first, .bl, .br, .tl, .tr').each(function() {
      preload.push(new Image())
      preload.slice(-1).src = $(this).css('background-image').replace(/url\((.+)\)/, '$1')
    })
	
    $('#facebox .close').click($.facebox.close)
    $('#facebox .close_image').attr('src', $.facebox.settings.closeImage)
  }
  
  // getPageScroll() by quirksmode.com
  function getPageScroll() {
    var xScroll, yScroll;
    if (self.pageYOffset) {
      yScroll = self.pageYOffset;
      xScroll = self.pageXOffset;
    } else if (document.documentElement && document.documentElement.scrollTop) {	 // Explorer 6 Strict
      yScroll = document.documentElement.scrollTop;
      xScroll = document.documentElement.scrollLeft;
    } else if (document.body) {// all other Explorers
      yScroll = document.body.scrollTop;
      xScroll = document.body.scrollLeft;	
    }
    return new Array(xScroll,yScroll) 
  }

  // Adapted from getPageSize() by quirksmode.com
  function getPageHeight() {
    var windowHeight
    if (self.innerHeight) {	// all except Explorer
      windowHeight = self.innerHeight;
    } else if (document.documentElement && document.documentElement.clientHeight) { // Explorer 6 Strict Mode
      windowHeight = document.documentElement.clientHeight;
    } else if (document.body) { // other Explorers
      windowHeight = document.body.clientHeight;
    }	
    return windowHeight
  }

  // Backwards compatibility
  function makeCompatible() {
    var $s = $.facebox.settings

    $s.loadingImage = $s.loading_image || $s.loadingImage
    $s.closeImage = $s.close_image || $s.closeImage
    $s.imageTypes = $s.image_types || $s.imageTypes
    $s.faceboxHtml = $s.facebox_html || $s.faceboxHtml
  }

  // Figures out what you want to display and displays it
  // formats are:
  //     div: #id
  //   image: blah.extension
  //    ajax: anything else
  function fillFaceboxFromHref(href, klass) {
    // div
    if (href.match(/#/)) {
      var url    = window.location.href.split('#')[0]
      var target = href.replace(url,'')
      $.facebox.reveal($(target).clone().show(), klass)

    // image
    } else if (href.match($.facebox.settings.imageTypesRegexp)) {
      fillFaceboxFromImage(href, klass)
    // ajax
    } else {
      fillFaceboxFromAjax(href, klass)
    }
  }

  function fillFaceboxFromImage(href, klass) {
    var image = new Image()
    image.onload = function() {
      $.facebox.reveal('<div class="image"><img src="' + image.src + '" /></div>', klass)
    }
    image.src = href
  }

  function fillFaceboxFromAjax(href, klass) {
    $.get(href, function(data) {  $.facebox.reveal(data, klass) })
  }

  function skipOverlay() {
    return $.facebox.settings.overlay == false || $.facebox.settings.opacity === null 
  }

  function showOverlay() {
    if (skipOverlay()) return

    if ($('facebox_overlay').length == 0) 
      $("body").append('<div id="facebox_overlay" class="facebox_hide"></div>')

    $('#facebox_overlay').hide().addClass("facebox_overlayBG")
      .css('opacity', $.facebox.settings.opacity)
      .fadeIn(200)
    return false
  }

  function hideOverlay() {
    if (skipOverlay()) return

    $('#facebox_overlay').fadeOut(200, function(){
      $("#facebox_overlay").removeClass("facebox_overlayBG")
      $("#facebox_overlay").addClass("facebox_hide") 
      $("#facebox_overlay").remove()
    })
    
    return false
  }
  
	//Capture onclick for all save/upload buttons
    //Also disables the button so users don't submit more than once
    //$('a[rel*=submit_button]').live('click', function () {
        $(document).on('click','a[rel*=submit_button]',function(){
		if($(this).attr('active')==1)
		{
			return false;
		}
		else
		{
			$('form[frm_name='+$(this).attr('acton')+']').submit();
			$(this).attr('active',1);
		}
	});
	  
    //$('form[rel*=submit_form]').live('submit',function()
    //{
     $(document).on('submit','form[rel*=submit_form]',function(){
  		var form = $(this);
  		var form_name = form.attr('frm_name');
      var form_action = form.attr('frm_action');
  		var query_string = form.serialize();
      var url = ajax_object.ajax_url + "?action=" + form_action;

      // The form.serialize() is not updating the latest messsage changed because it is sent using Ajax.
      // This gets the value of the latest changes in tinyMCE.
      // This only checks if it is for sending message.
      if( query_string.indexOf('&message=') >= 0)
      {
        var new_content = tinymce.get('composed_message').getContent(); // Content message created by the user using tinymce
        query_string = query_string + "&composed_message=" + new_content;
      }
      // Send POST to ajax admin_url 
      $.ajax( {
        type: "POST",
        dataType: "json",
        url: url,
        data: query_string,

        // If we are successful
        success: function(data)
        {
          console.log('testing message');
          if (!$.trim(data))
          {   
              alert("The function is not available, or you your session has ended.");
          }
          if (data.success) 
          {
            jQuery(document).trigger('success.'+form_name,data);
          } 
          else 
          {
            if (data.display_errors) 
            {
              if (form.attr('hasError')==1) 
              {
                form.parent().parent().find(".errorbox").empty();
                form.parent().parent().find(".errorbox").append(data.errors);
              } 
              else 
              {
                $('<div class="errorboxcontainer_no_width"><div class="error-tl"><div class="error-tr"><div class="error-bl"><div class="error-br"><div class="errorbox">'+data.errors+'</div></div></div></div></div></div>').insertBefore(form);
                form.attr('hasError',1);
              }

              form.find("input").removeClass("error");
              $.each(data.error_elements,function(i) 
              {
                form.find('input[name='+data.error_elements[i]+']').addClass("error");
              });
              form.parent().parent().parent().find('a[rel*=submit_button]').attr('active',0);
              form.parent().parent().parent().find('a[rel*=submit_button]').find('img').attr('src','/wp-content/themes/ExpertOnlineTraining/images/tick.png');
            }
          }
        },
        // If it fails on the other hand.
        error: function(XMLHttpRequest, textStatus, errorThrown) 
        {
           alert( "POST Sent fails: " + " Query String : " + query_string +  XMLHttpRequest );
        }
      });
		return false;
    }); 
	
//	$('a[rel=done_button]').live('click',
//      function()
//      {
$(document).on('click','a[rel=done_button]',function(){
        $(document).trigger('close.facebox');
      }
    );
//    $('a[rel=loading]').live('click',
//      function()
//      {
$(document).on('click','a[rel=loading]',function(){
        $.facebox.loading();
      }
    );
//    $('a[rel=refresh]').live('click',
//      function()
//      {
$(document).on('click','a[rel=refresh]',function(){
        $('#staff_listing_pane').jScrollPane({showArrows:true, scrollbarWidth: 15, arrowSize: 16,animateTo:true,animateInterval:50, animateStep:5});
      }
    );
 /*
  * "Remove All" button in manage courses
  * This removes enrollments of all the staff members in a course.
  */
//	$('a[rel=unselect_all_button]').live('click',
//      function()
//      {
$(document).on('click','a[rel=unselect_all_button]',function(){
        var form = $('form#'+$(this).attr("acton"));
        form
          .find('[collection='+$(this).attr('collection')+'][status="remove"]')
          .each(
            function(index,Element)
            {
              $(Element).click();
            }
          )
          .end()
          .find('[collection='+$(this).attr('collection')+']:checked')
          .each(
            function(index,Element)
            {
              $(Element).click();
            }
          )
      }
    );
   /*
    * "Add All" button in manage courses
    * This enroll all the staff members in a course.
    */
//    $('a[rel=select_all_button]').live('click',
//      function()
//      {
$(document).on('click','a[rel=select_all_button]',function(){
    var form = $('#'+$(this).attr("acton"));
        form
          .find('[collection='+$(this).attr('collection')+'][status="add"]')
          .each(
            function(index,Element)
            {
              $(Element).click();
            }
          )
        .end()
        .find('[collection='+$(this).attr('collection')+']:not(:checked):checkbox')
        .each(
          function(index,Element)
          {
            $(Element).click();
          }
        )    
      }
    );  

  //Bindings when facebox opens or closes
  $(document).bind('close.facebox', function() {
    $(document).unbind('keydown.facebox');
    $('#facebox').fadeOut(function() {
      $('#facebox .content').removeClass().addClass('content');
      hideOverlay();
      
      $('#facebox .loading').remove();
          if($('#staff_and_assignment_list').attr("refresh")==1)
    {
      // When triggered, Return the group_id or course id 
      $(document).trigger('load.staff_video_list',[{group_id: $("#staff_and_assignment_list").attr("group_id")}]);
    }
    });
    
  });
  
  $(document).bind('reveal.facebox', function() {
    $('#facebox').draggable({handle: "div.title"});
    
    if ($('textarea.tinymce').length > 0)
    {
      tinymce.init({
       selector: 'textarea',  // change this value according to your HTML
        script_url : '/includes/js/tiny_mce.js',
        theme : "advanced",
        plugins : "pagebreak",
        entity_encoding: "named", entities: "&nbsp;",
        theme_advanced_buttons1 : "bold,italic,underline,|,fontselect,fontsizeselect,bullist,numlist,|,undo,redo,|,link,unlink,image,|,forecolor",
        theme_advanced_buttons2 : "code",
        theme_advanced_buttons3 : "",
        theme_advanced_buttons4 : "",
        theme_advanced_toolbar_location : "top",
        theme_advanced_toolbar_align : "left",
        theme_advanced_resizing : true    
      });
    } 
  });
  
})(jQuery);
