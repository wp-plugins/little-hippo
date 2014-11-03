(function ( $ ) {
	"use strict";

	$(function () {
		// Fix for BootStrapp .hidden class declaration for Help Screens
		$("#contextual-help-wrap").removeClass("hidden");

		// Activation for Tabs on Admin pages
		$('#issues a').click(function(e) {
			e.preventDefault();
			$(this).tab('show');
		});
		$('#default_meta a').click(function(e) {
			e.preventDefault();
			$(this).tab('show');
		});

		var custom_uploader;
 
		$('#upload_image_button').click(function(e) {

			e.preventDefault();

			//If the uploader object has already been created, reopen the dialog
			if (custom_uploader) {
				custom_uploader.open();
				return;
			}

			//Extend the wp.media object
			var custom_uploader = wp.media.frames.file_frame = wp.media({
				title: 'Choose Image',
				button: {
					text: 'Choose Image'
				},
				multiple: false
			});

			//When a file is selected, grab the URL and set it as the text field's value
			custom_uploader.on('select', function() {
				var attachment = custom_uploader.state().get('selection').first().toJSON();
				$('#upload_image').val(attachment.url);
			});

			//Open the uploader dialog
			custom_uploader.open();
		});

		// Place your administration-specific JavaScript here
		$(".to_file").click(function(){
			// _do_ajax(this);
			
			var element	= $(this);
			var url = wpAjax.unserialize(element.attr('href'));
			var data = {
				'action': url.action,
				'_tipp_nonce': url._wpnonce,
				'postid': url.pi,
			};
			$.post(TippSettings.AjaxUrl, data, function(r){
				var res = wpAjax.parseAjaxResponse(r,'ajax-response');
				$.each( res.responses, function() { 
					var media = $.parseJSON(this.data);

					$.each(media, function( index, value ){
						var fileName = $("#image-file-" + index).val();
						var fileExt = '.' + fileName.split('.').pop();
						$("#image-file-" + index).val(value + fileExt);
					});
				});//end each
			});

			return false;
		});

		$(".to_title").click(function(){
			// _do_ajax(this);
			
			var element	= $(this);
			var url = wpAjax.unserialize(element.attr('href'));
			var data = {
				'action': url.action,
				'_tipp_nonce': url._wpnonce,
				'postid': url.pi,
			};
			$.post(TippSettings.AjaxUrl, data, function(r){
				var res = wpAjax.parseAjaxResponse(r,'ajax-response');
				$.each( res.responses, function() { 
					var media = $.parseJSON(this.data);

					$.each(media, function( index, value ){
						$("#image-title-" + index).val(value).addClass("alert-warning");
						var newlen = 65 - value.length;
						$(".chars-title-" + index).html(newlen).addClass("alert-warning");
						/*
						$("#image-title-" + index).characterCounter({
							counterCssClass: "chars-title-"+index,
							limit: 65
						});
*/
					});
				});//end each
			});

			return false;
		});

		$(".to_alt").click(function(){
			// _do_ajax(this);
			
			var element	= $(this);
			var url = wpAjax.unserialize(element.attr('href'));
			var data = {
				'action': url.action,
				'_tipp_nonce': url._wpnonce,
				'postid': url.pi,
			};
			$.post(TippSettings.AjaxUrl, data, function(r){
				var res = wpAjax.parseAjaxResponse(r,'ajax-response');
				$.each( res.responses, function() { 
					var media = $.parseJSON(this.data);

					$.each(media, function( index, value ){
						$("#image-alt-" + index).val(value).addClass("alert-warning");
						var newlen = 255 - value.length;
						$(".chars-alt-" + index).html(newlen).addClass("alert-warning");

						/*
						$("#image-alt-" + index).characterCounter({
							counterCssClass: "chars-alt-"+index,
							limit: 255
						});
*/
					});
				});//end each
			});

			return false;
		});

		$(".lh-empty-trash").click(function(){
			// _do_ajax(this);

			$(".trash_status").html('');
			$(".trash-loader").removeClass("hide");

			var element	= $(this);
			var count = 0;
			var url = wpAjax.unserialize(element.attr('href'));
			var data = {
				'action': url.action,
				'_tipp_nonce': url._wpnonce,
			};

			$.post(TippSettings.AjaxUrl, data, function(r){
				var res = wpAjax.parseAjaxResponse(r,'ajax-response');

				$.each( res.responses, function() { 
					var items = $.parseJSON(this.data);

					if(items){
						$.each(items, function( index, value ){
							count = count + 1;
						});
					}
					if(count == 0){
						$(".trash_status").html('Trash is already empty.');
					} else {
						$(".trash_status").html(count + ' items removed from the trash.');
					}
				});//end each

				$(".trash-loader").addClass("hide");
				$(".trash_status").removeClass("hide");
			});

			return false;
		});

		$(".update_tags").click(function(event){
			var element		= $(this);
			var idname 		= $(this).attr("id");
			var mediaID 	= idname.substring(12);

			$("#loader-"+mediaID).removeClass("hide");
			
			var fileVal 	= $("#image-file-"+mediaID).val();
			var titleVal	= $("#image-title-"+mediaID).val();
			var altVal 		= $("#image-alt-"+mediaID).val();

			if( fileVal == '' || titleVal == '' || altVal == ''){
				$("#loader-"+mediaID).addClass("hide");
				$("#tipp_item_"+mediaID).css('background-color','#f2dede');
			} else {

				var url = wpAjax.unserialize(element.attr('href'));
				var data = {
					'action': url.action,
					'_tipp_nonce': url._wpnonce,
					'media': mediaID,
					'newfile': fileVal,
					'newtitle': titleVal,
					'newalt': altVal
				};
				$.post(TippSettings.AjaxUrl, data, function(response){
					$("#loader-"+mediaID).addClass("hide");
					// alert(response);
					if(response == mediaID){
						$("#tipp_item_"+mediaID).css('background-color','#dff0d8');
						$("#image-title-" + mediaID).removeClass('alert-warning');
						$(".chars-title-" + mediaID).removeClass('alert-warning');
						$("#image-alt-" + mediaID).removeClass('alert-warning');
						$(".chars-alt-" + mediaID).removeClass('alert-warning');
					} else {
						$("#tipp_item_"+mediaID).css('background-color','#f2dede');	
					}
				});
			}

			return false;
		});

		$(".save_meta").click(function(event){
			var element		= $(this);
			var idname 		= $(this).attr("id");
			var objID 		= idname.substring(10);

			var titleVal 	= $("#seo-title-"+objID).val();
			var descrVal	= $("#seo-descr-"+objID).val();
			var kwordVal 	= $("#seo-kword-"+objID).val();

			var url = wpAjax.unserialize(element.attr('href'));
			var data = {
				'action': url.action,
				'_tipp_nonce': url._wpnonce,
				'objid': objID,
				'meta_title_value': titleVal,
				'meta_descr_value': descrVal,
				'meta_kword_value': kwordVal
			};
			$.post(TippSettings.AjaxUrl, data, function(response){
				// alert("Response: " + response + ", and objID: " + objID);

				if(response == objID){
					$(".post_data_" + objID).css('background-color','#dff0d8');
				} else {
					$(".post_data_" + objID).css('background-color','#f2dede');	
				}
			});

			return false;
		});

		$(".save_all").click(function(event){
			var element		= $(this);
			var idname 		= $(this).attr("id");
			var objID 		= idname.substring(10);

			var titleVal 	= $("#seo-title-"+objID).val();
			var descrVal	= $("#seo-descr-"+objID).val();
			var kwordVal 	= $("#seo-kword-"+objID).val();

			var url = wpAjax.unserialize(element.attr('href'));
			var data = {
				'action': url.action,
				'_tipp_nonce': url._wpnonce,
				'objid': objID,
				'meta_title_value': titleVal,
				'meta_descr_value': descrVal,
				'meta_kword_value': kwordVal
			};
			$.post(TippSettings.AjaxUrl, data, function(response){
				// alert("Response: " + response);

				if(response){
					$(".post_data_" + objID).css('background-color','#dff0d8');

					var mediaList = $.parseJSON(response);
					$.each(mediaList, function(i, elem) {
						// alert("elem=" + elem);
						var mediaID = elem.substring(10);
						_do_media_update(mediaID);
						// $("#" + elem).css('background-color','#dff0d8');
					});
				} else {
					$(".post_data_" + objID).css('background-color','#f2dede');	
				}
			});

			return false;
		});

		function _do_ajax(obj) {
			var element = $(obj);
			var url = wpAjax.unserialize(element.attr('href'));
			var s = {}; 

			s.response = 'ajax-response'; 
			s.type = "POST"; 
			s.url = TippSettings.AjaxUrl; 
			s.data = $.extend(s.data, { action: url.action, _tipp_nonce: url._wpnonce, postid: url.pi });
			s.success = function(r) {
				var res = wpAjax.parseAjaxResponse(r,'ajax-response');
				alert(res);
				$.each( res.responses, function() { 
					switch(this.what) {
						case "tipptitletofile":
							var mediaids = this.data;
							alert(mediaids);
							break;
						case "something else":
							break;
						default:
							break;
					}//end switch
				});//end each
			}
			s.error = function(r) {
				alert("Epic Fail!");
			}
			$.ajax(s);
		}

		function _do_media_update(objID) {

			$("#loader-" + objID).removeClass("hide");
			
			var fileVal 	= $("#image-file-" + objID).val();
			var titleVal	= $("#image-title-"+ objID).val();
			var altVal 		= $("#image-alt-" + objID).val();

			if( fileVal == '' || titleVal == '' || altVal == ''){
				$("#loader-" + objID).addClass("hide");
				$("#tipp_item_" + objID).css('background-color','#f2dede');
			} else {

				var element = $("#update_tags_" + objID);
				var url = wpAjax.unserialize(element.attr('href'));
				var data = {
					'action': url.action,
					'_tipp_nonce': url._wpnonce,
					'media': objID,
					'newfile': fileVal,
					'newtitle': titleVal,
					'newalt': altVal
				};
				$.post(TippSettings.AjaxUrl, data, function(response){
					$("#loader-" + objID).addClass("hide");
					// alert(response);
					if(response == objID){
						$("#tipp_item_"+objID).css('background-color','#dff0d8');
						$("#image-title-" + objID).removeClass('alert-warning');
						$(".chars-title-" + objID).removeClass('alert-warning');
						$("#image-alt-" + objID).removeClass('alert-warning');
						$(".chars-alt-" + objID).removeClass('alert-warning');
					} else {
						$("#tipp_item_"+objID).css('background-color','#f2dede');	
					}
				});
			}

		}
	});
}(jQuery));