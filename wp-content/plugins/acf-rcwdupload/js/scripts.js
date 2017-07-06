
(function($){

	rcwd_acf_get_atts = function( $el ){
		
		var atts = {};
		
		$.each( $el[0].attributes, function( index, attr ) {
        	
        	if( attr.name.substr(0, 5) == 'data-' )
        	{
	        	atts[ attr.name.replace('data-', '') ] = attr.value;
        	}
        });
        
        return atts;
			
	};
	
	acf.fields.rcwdupload 	= {};
	var _rcwdupload 		= acf.fields.rcwdupload;
	$.fn.rcwdPluploader 	= function(e){

		var el, el_, c, b, fid, fk, j, d, maxf, maxfsize, msel, overw, nf_txt, resize, csresize, fl, upf, cpv, cpv_, cpv_mw, cpv_mh, cpv_cr, nativeFiles, fsz, rmvcf, rmvtf, rmv, fht, fhtf, fhtfhref, fhts, acffv, acffv_, acfddb, b_txt, upflrs, fdspeed, is_in_r, is_in_fc, rptid, rptd, fctid, fcid, aupld, rndfldr, cllctn, acfff, field_key, pv_, pv_mw, pv_mh, pv_cr, rnm, chnk;

		el			= $(this);
		el_			= this;
		c 			= el.attr("id");
		ffiles		= el.find('.rcwdacflupload-folder-files-wrapper').attr("id");
		b			= el.find('.rcwdplupload-pickfiles').attr("id");
		fl			= el.find('.rcwdplupload-filelist').attr("id");
		upf			= el.find('.rcwdplupload-uploadfiles').attr("id");
		cpv			= el.find('.rcwdplupload-clientpreview').attr("id");
		//rndfldr		= $('#rcwdupload_rndfldr').val();
		nativeFiles	= {};
		fsz			= el.find('.rcwdplupload-filesize').attr("id");
		rmvcf		= el.find('.rcwdplupload-removecf').attr("id");
		rmvtf		= el.find('.rcwdplupload-removetf').attr("id");
		rmv			= el.find('.rcwdplupload-remove').attr("id");
		fht			= el.find('.rcwdplupload-temp').attr("id");
		fhtf		= el.find('.rcwdplupload-temp-file').attr("id");
		fhtfhref	= $('#'+fhtf+' a').attr('href');
		fhts		= el.find('.rcwdplupload-temp-size').attr("id");
		acffv		= el.find('.acf-file-value').attr("id");
		acffv_		= $('#'+acffv).val();
		acfff		= el.find('.acf-file-folder').attr("id");
		acfddb		= el.find('.rcwdplupload-ddbox').attr("id");
		d 			= typeof acf.helpers !== 'undefined' ? acf.helpers.get_atts(el) : rcwd_acf_get_atts(el);
		fid 		= d.fid;
		cpv_		= d.clientpreview;
		cpv_mw		= parseInt(d.clientpreview_max_width);
		cpv_mh		= parseInt(d.clientpreview_max_height);
		cpv_cr		= d.clientpreview_crop == 'Y' ? true : false;	
		pv_			= d.preview;
		pv_mw		= parseInt(d.preview_max_width);
		pv_mh		= parseInt(d.preview_max_height);			
		pv_cr		= d.preview_crop;
		fk 			= d.fkey;
		maxf		= d.maxf;
		maxfsize	= d.maxfsize;
		msel		= d.msel;
		overw		= d.overw;
		nf_txt		= d.nftxt;
		resize		= d.resize != '' 	? '&'+$.param(JSON.parse(d.resize))	: '';
		csresize	= d.csresize != '' 	? JSON.parse(d.csresize) 			: '';
		upflrs		= d.upflrs != '' 	? JSON.parse(d.upflrs) 				: '';
		aupld		= d.autoupload;
		rnm			= d.rename;
		chnk		= d.chunks;		
		cllctn		= d.collection;
		fldr		= d.folder;
		info		= d.info;
		b_txt		= $('#'+b).html();
		fdspeed		= 300;
		is_in_r 	= (el.closest('.repeater').length > 0) 				? true : false;
		is_in_fc 	= (el.closest('.acf_flexible_content').length > 0) 	? true : false;

		$('<input/>').attr({ type:'hidden', name:fk+'-cf[]', class:'rcwdplupload-cf', value:acffv_ }).appendTo(el);
		
		if(is_in_r){
			
			rptid 		= el.closest('.repeater').closest('.field_type-repeater').attr('id');
			field_key 	= rptid;
			//rptd  = acf.helpers.get_atts($('#'+rptid));
			msel	= true;
			
			$('<input/>').attr({ type:'hidden', id:fid+'_rptfieldkey', name:fk+'_rptfieldkey', value:field_key }).appendTo(el);
			
		}else if(is_in_fc){
			
			fctid	 	= el.closest('.acf_flexible_content').closest('.field_type-flexible_content').attr('id');
			field_key	= rptid;
			//fcid = acf.helpers.get_atts($('#'+fctid));
			
			$('<input/>').attr({ type:'hidden', id:fid+'_rptfieldkey', name:fk+'_rptfieldkey', value:field_key }).appendTo(el);
			
		}

		
		if( !is_in_r && !is_in_fc ){
			
			maxf 	= 1;
			overw	= true;
			msel	= false;
			
		}


		maxf 	= 1;
		overw	= true;
		msel	= false;
		args 	= {

			runtimes 			: 'html5,flash,silverlight,html4',
			container 			: c,
			browse_button 		: b,
			max_file_size 		: maxfsize,
			//url 				: acf_rcwdupload_url+'&fkey='+fk+'&overw='+overw+resize,
			url 				: acf_rcwdupload_url + '&info='+info,
			flash_swf_url 		: acf_rcwdupload_flash_swf_url,
			silverlight_xap_url : acf_rcwdupload_silverlight_xap_url,
			multi_selection		: msel,
			dragdrop			: true,
			drop_element		: c,
			chunk_size			: chnk,
			unique_names		: false,
			max_retries			: 3,
			filters				: ''
										
		}

		if(csresize != '')
			args.resize = csresize;
				
		if(upflrs != '')
			args.filters = upflrs;

		fltr = $(document).triggerHandler( 'acfrcwdupload_filters', [ args.filters, fid ] );
		
		if(typeof fltr != 'undefined')
			args.filters = fltr;
						
		j = new plupload.Uploader(args);

		$(document).trigger( 'acfrcwdupload_object', [ fid ] );

		if( typeof bhkbhnjopacf === 'undefined')
			bhkbhnjopacf = new Object;

		bhkbhnjopacf[c] = j;

		$('#'+c).on("dragover", function(event){
			
			 $(this).addClass('dragover');
			 
		});

		$('#'+c).on("dragleave", function(event){
			
			 $(this).removeClass('dragover');
			 
		});

		$('#'+c).on("drop", function(event){
			
			 $(this).removeClass('dragover');
			 
		});

		$('#'+c).on("end", function(event){
			
			 $(this).removeClass('dragover');
			 
		});							 

		j.bind('PostInit', function( up, params ){
			
			if(j.runtime == "html5"){
				
				var inputFile 		= $('#' + up.settings.container).find('.moxie-shim-html5 input')[0];
				var oldFunction 	= inputFile.onchange;
				
				inputFile.onchange 	= function(){
					
					nativeFiles = this.files;
					
					oldFunction.call(inputFile);
					
				}	
					
			}	
			
			if(typeof rcwdfiletoadd !== 'undefined'){

				j.addFile(rcwdfiletoadd);
				
				rcwdfiletoadd = '';
				
				delete rcwdfiletoadd;
				
			}
					
		});
			
		j.init();
		
		j.bind('FilesAdded', function(up, files){
			
			var fileExt, flnm;

			if(aupld != 'Y')
				$('#'+upf).fadeTo(fdspeed, 1);
								
			if(up.files.length > maxf){
				
				up.files.reverse();
				
				j.splice(maxf, up.files.length - maxf);
				
			}
			
			var currfile = 1;
			
			$.each(files, function(i, file){

				fileExt = 'acfrcwdupload-filext-' + file.name.split('.').pop().toLowerCase(); 	
				flnm 	= file.name;
				
				if(currfile == 1){

					if(file.name.length > 30)
						flnm = 	flnm.substr( 0, 30 )+'...';

					if( j.runtime == "html5" && cpv_ == 'Y' /*&& aupld != 'Y'*/ ){
						
						$('#' + cpv).html('');

						var image 			= $( new Image() ).appendTo('#' + cpv);
						var preloader 		= new mOxie.Image();
						preloader.onload 	= function(){

							if(typeof $(el_).data('width') != 'undefined')
								var width = parseInt($(el_).data('width'));

							if(typeof $(el_).data('height') != 'undefined')
								var height = parseInt($(el_).data('height'));									

							if(typeof $(el_).data('height') != 'undefined')
								var crop = Boolean($(el_).data('crop'));
							
							if( cpv_mw > 0 )	
								var width = cpv_mw;

							if( cpv_mh > 0 )	
								var height = cpv_mh;
																	
							if( cpv_mw > 0 &&  cpv_mh > 0 )
								var crop = cpv_cr;
																 
							preloader.downsize( width, height, crop ); 
							
							image.prop( "src", preloader.getAsDataURL() );
							image.prop( "width", width );
							image.prop( "height", height );
							
							 $('#' + cpv).css( 'display', 'block' ).fadeTo(fdspeed, 1);
							 
						}
						 
						preloader.load(file.getSource());

					}

					if(aupld == 'Y'){
						
						j.start();	

						$('#'+b).addClass('button-disabled');	
						
						$('#'+upf).fadeTo( fdspeed, 0, function(){
							
							$(this).css( 'display', 'none' );
							
						});
							
					}
											
					$('#'+fl+' .rcwdplupload-filename').closest('.rcwdplupload-filewrapper').attr( 'id', file.id );
					$('#'+fl+' .rcwdplupload-filename').html('<span class="acfrcwdupload-filext-generic '+fileExt+'" title="'+file.name+'">'+flnm+'</span>');
					
					$('#'+fsz+' span').html(' ('+plupload.formatSize(file.size)+')');
					$('#'+fsz).fadeTo(fdspeed, 1);
					$('#'+rmv).fadeTo(fdspeed, 1);
					
				}else{
					
					//$('#'+fl).append('<div id="' + file.id + '">'+file.name + ' (' + plupload.formatSize(file.size) + ') <b></b>' +'</div>');
					rcwdfiletoadd 	= file;
					var key 		= el.closest('td').attr('data-field_key');
					var selector	= 'td .acf_rcwdupload:first';

					if(key)
						selector = 'td[data-field_key="' + key + '"] .acf_rcwdupload';
													
					el.closest('td').closest('.row').closest('.repeater').find('.add-row-end').trigger('click');
					
					//el.closest('td').closest('.row').next('.row').find(selector);
					
				}
				
				currfile++;
				
				g();
				
			});
	
			up.refresh();
		});

		$('#'+upf).click(function(ev){
			
			if(j.files.length > 0){
				
				j.start();
				
				$('#'+b).addClass('button-disabled');	
				
				$('#'+upf).fadeTo(fdspeed, 0, function(){
					
					$(this).css( 'display', 'none' );
					
				});
				
			}
			ev.preventDefault();
			
		});	

		j.bind('BeforeUpload', function(up, file){
			
			$('#'+b).html("0%");
			
		});
						
		j.bind('UploadProgress', function(up, file){
			
			if(file.percent == 100)
				$('#' + b).html(window['acfrcwdi18n'].processing);
			else
				$('#' + b).html(file.percent + "%");
			
		});
						
		j.bind('FileUploaded', function( up, file, info ){

			var obj, flnm;
			
			obj = JSON.parse(info.response);
			
			if( typeof obj.error != 'undefined' )
				error(obj.error);
			else{
				
				flnm = obj.filename;
						
				$('#' + file.id + " b").html("99%");
				$('#'+b).addClass('button-disabled');	
				$('#'+acffv).val(obj.filename).change();		
				$('#'+fhtf+' span.rcwdplupload-temp-file-txt').html(nf_txt);
				$('#'+fhtf+' a').html(obj.filename);
				$('#'+fhtf+' a').attr( 'href', fhtfhref + fldr + '/' + obj.filename);
				$('#'+fhts+' span').html(plupload.formatSize(obj.size));
				$('#' + file.id + " b").html("100%");				
				$('#'+fht).fadeTo( fdspeed, 1 );	

				$(document).trigger( 'acfrcwdupload_fileuploaded', [ fhtfhref + obj.filename, fid ] );

				var ext = flnm.split('.').pop().toLowerCase();

				if( pv_ == 'Y' && ( ext == 'jpg' || ext == 'jpeg' || ext == 'png' || ext == 'gif' || ext == 'tif' || ext == 'tiff' ) ){
					
					var e = $.Event( "acfrcwdupload_thumb_force_http" );
					
					$(document).trigger( e, [ fid ] );
					
					var frc = Boolean(e.result);
					
					if(frc)
						var frc = 1;
					else
						var frc = 0;

					var thumburl = window['acfrcwdi18n'].thumburl+'?f=' + encodeURIComponent(obj.base64) + '&w=' + pv_mw + '&h=' + pv_mh + '&c=' + pv_cr + '&t=1&frc=' + frc;
					
					if($('#' + fht + ' .acfrcwdplupload-temp-preview').length > 0)
						$('#' + fht + ' .acfrcwdplupload-temp-preview').html('<img src="' + thumburl + '" alt="" />');
					else
						$('#' + fht).append('<div class="acfrcwdplupload-temp-preview"><img src="' + thumburl + '" alt="" /></div>');
				
				}else{
					
					if($('#' + fht + ' .acfrcwdplupload-temp-preview').length > 0)
						$('#' + fht + ' .acfrcwdplupload-temp-preview').remove();						
					
				}
					
			}
			
			$('#' + fl + ' .rcwdplupload-filename').fadeTo( fdspeed, 0, function(){
				
				$(this).html('...').fadeTo( fdspeed, 1 );
				
			});
			
			$('#' + fsz).fadeTo(fdspeed, 0, function(){
				
				$(this).css( 'display', 'none' );
				
			});	
				
			$('#' + rmv).fadeTo( fdspeed, 0, function(){
				
				$(this).css( 'display', 'none' );
				
			});	

			$('#' + cpv).fadeTo(fdspeed, 0, function(){
				
				$(this).css( 'display', 'none' );
				$(this).html('');
				
			});
										
		});

		j.bind('UploadComplete', function(up, files){
			
			if(maxf == 1){
				
				setTimeout(function(){
					
					$('#'+b).removeClass('button-disabled');	
					$('#'+b).html(b_txt);
					up.refresh();
					
				}, 1000 );	
							
			}
			
		});
		
		j.bind('Error', function( up, err ) {
			error(err);
			up.refresh();
		});

		function error(err){
			var msg;
			switch(err.code){
				case -600: 
					msg = acfrcwdi18n.err600;
					break;				
				case -601: 
					msg = acfrcwdi18n.err601;
					break;
				default:
					msg = err.message;
			}
			
			$.prompt(msg);
						
		}
		
		function g(){
			
			$.each(j.files, function (p, o) {
				
				$('#'+o.id+' .rcwdplupload-remove').click(function(){
					
					j.removeFile(o);
					
					$(this).fadeTo(fdspeed, 0, function(){
						
						$(this).css( 'display', 'none' );
						
					});
					
					$('#'+upf).fadeTo(fdspeed, 0, function(){
						
						$(this).css( 'display', 'none' );
						
					});
					
					$('#'+fl+' .rcwdplupload-filename').fadeTo(fdspeed, 0, function(){
						
						$(this).html('...').fadeTo(fdspeed, 1);
						
					});

					$('#'+cpv).fadeTo(fdspeed, 0, function(){
						
						$(this).css( 'display', 'none' );
						$(this).html('');
						
					});
											
					$('#'+fsz).fadeTo(fdspeed, 0);
					
				});		

				$('#'+rmvtf).click(function(ev){
					
					el.find('.rcwdplupload-temp').fadeTo(fdspeed, 0, function(){
						
						$(this).css( 'display', 'none' );
						
						bhkbhnjopacf[c].refresh();
						
					});
					
					$('#'+acffv).val(acffv_);
					
					ev.preventDefault();
					
				});	
						
			});
			
		}
		
		$('#' + rmvcf).click(function(ev){
			
			el.find('.rcwdplupload-current').fadeTo(fdspeed, 0, function(){
				
				$(this).css( 'display', 'none' );
				
				bhkbhnjopacf[c].refresh();
				
			});
			
			$('#'+acffv).val('');	

			if(cllctn == 'Y'){
				
				$('#'+acfff).val('');	
				$('#'+ffiles).find('.rcwdacflupload-folder-files').removeClass('rcwdacflupload-files-selected');
				
			}
				
			ev.preventDefault();
			
		});		

		$('#' + ffiles).find('.rcwdacflupload-folder-files').hover(
		
			function(){
				
				$(this).addClass('hover');
				
			}, function(){
				
				$(this).removeClass('hover');
				
			}
			
		);
		
		$('#' + ffiles).find('.rcwdacflupload-folder-files').click(function(ev){
			
			if($(this).hasClass('rcwdacflupload-files-selected')){

				el.find('.rcwdplupload-current').fadeTo(fdspeed, 0, function(){
					
					$(this).css( 'display', 'none' );
					
				});
			
				$('#'+acffv).val('');
				$('#'+acfff).val('');	
				$(this).removeClass('rcwdacflupload-files-selected');
				
			}else{
				
				$('#'+ffiles).find('.rcwdacflupload-folder-files').removeClass('rcwdacflupload-files-selected');
				$(this).addClass('rcwdacflupload-files-selected');
				
				var container	= $(this).closest('.acf_rcwdupload');
				var cfile		= container.find('.rcwdplupload-current-file a');
				var cfsize		= container.find('.rcwdplupload-current-size span');
				
				cfile.text($(this).attr('data-filename'));
				cfile.attr( 'href', fhtfhref.replace( '_temp', '' ) + $(this).attr('data-filefolder') + '/' + $(this).attr('data-filename'));
	
				cfsize.text($(this).attr('data-size'));
				
				$('#' + acffv).val($(this).attr('data-filename'));
				$('#' + acfff).val($(this).attr('data-filefolder'));
	
				el.find('.rcwdplupload-current').fadeTo(fdspeed, 1 );
				
			}
			
		});
		
	}

	if(typeof acf.add_action !== 'undefined') {

		acf.add_action('ready append', function($el){
			
			acf.get_fields({ type : 'rcwdupload'}, $el).each(function(){

				var input = $(this).children().children('input[type="hidden"]');

				if( input.attr('name') && input.attr('name').indexOf('[acfcloneindex]') != -1 )
					return;
	
				$(this).find(".rcwdplupload-container").rcwdPluploader({});
				
		
			});

		});
		
	}else{
		
		$(document).bind('acf/setup_fields', function( e, postbox ){

			$(postbox).find('.acf_rcwdupload').each(function(){
	
				var $input = $(this).children().children('input[type="hidden"]');
	
				if(acf.helpers.is_clone_field($input))
					return;
	
				$(this).find(".rcwdplupload-container").rcwdPluploader({});
				
			});
		});
	
	}

})(jQuery);