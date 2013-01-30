jQuery(function() {
	
	$('#datepicker').datepicker({
		showOtherMonths : true,
		selectOtherMonths : true,
		dateFormat : 'yy-mm-dd'
	});
	
	$('#timepicker').timepicker({
		timeFormat : 'h:mm TT',
		ampm : true,
		hourGrid : 4,
		minuteGrid : 30
	});
	
	$('#file_upload').uploadify({
		'uploader' : '../wp-content/plugins/alhughes-revised/uploadify/uploadify.swf',
		'script' : '../wp-content/plugins/alhughes-revised/uploadify/uploadify.php',
		'cancelImg' : '../wp-content/plugins/alhughes-revised/uploadify/cancel.png',
		'folder' : 'pdfs',
		'auto' : true,
		'onComplete' : function(event, ID, fileObj, response, data) {

			jQuery('#file_url').val( response );
			jQuery('#file_link').html('<a href="' + response + '">Current File</a>');
		}
	});
	var attachmentCount = -1;
	$('#attachments').uploadify({
		'uploader' : '../wp-content/plugins/alhughes-revised/uploadify/uploadify.swf',
		'script' : '../wp-content/plugins/alhughes-revised/uploadify/uploadify.php',
		'cancelImg' : '../wp-content/plugins/alhughes-revised/uploadify/cancel.png',
		'folder' : 'attachments',
		'auto' : true,
		'multi' : true,
		'onComplete' : function( event, ID, fileObj, response, data ) {
			var html = "<div id='attachment-" + attachmentCount + "'><input type='hidden' name='attachments[" + attachmentCount + "]' value='" + response + "' /><a href='" + response + "' target='_blank'>View " + fileObj.name + "</a> | <a style='cursor:pointer' onclick='jQuery(\"#attachment-" + attachmentCount + "\").remove()'>Remove Attachment</a></div>";
			jQuery('#attachment_div').html( jQuery('#attachment_div').html() + html );
			attachmentCount--;
		}
	});
	var photoCount = -1;
	$('#photo_upload').uploadify({
		'uploader' : '../wp-content/plugins/alhughes-revised/uploadify/uploadify.swf',
		'script' : '../wp-content/plugins/alhughes-revised/uploadify/uploadify.php',
		'cancelImg' : '../wp-content/plugins/alhughes-revised/uploadify/cancel.png',
		'folder' : 'imgs',
		'auto' : true,
		'multi' : true,
		'onComplete' : function(event, ID, fileObj, response, data) {
			//jQuery('#photo_urls').val( jQuery('#photo_urls').val() + ';' + '{$date}' + '/' + fileObj.name );
			response = response.split(':::'); // response[0] will be the main url, response[1] the thumbnail url
			var someHTML = '<li class="ui-state-default" id="' + photoCount + '"><img src="' + response[1] + '" class="edit_form_img" /><br /><input type=\"hidden\" name=\"photoURLs[' + photoCount + ']\" value="' + response[0] + '" /><input type=\"text\" name=\"captions[' + photoCount + ']\" value=\"\" /><br /><a style="cursor:pointer" onclick="jQuery(\'#' + photoCount + '\').remove()">Remove Picture</a></li>';
			photoCount--;
			jQuery('#sortable').html(jQuery('#sortable').html() + someHTML);
		}
	});

	$('#sortable').sortable();
		/*{
		'stop' : function(event, ui) {
			jQuery('#photo_urls').val('');
			var imgsArray = $('#sortable').sortable('toArray');
			$.each(imgsArray, function(aIndex, aValue) {
				var aVal = ';' + aValue
				jQuery('#photo_urls').val(jQuery('#photo_urls').val() + aVal);
			});
		}
	});*/

});


function removePicture( pictureID ) {
	
	if( confirm("Are you sure you want to delete this picture?") ) {
		jQuery("#" + pictureID ).remove();
	}
	
}
