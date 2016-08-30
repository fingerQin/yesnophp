/**
 * 上传图片。
 * @param String filesUrl 图片文件域名。
 * @param String baseJsUrl JS静态资源所在位置。
 * @param String ImgId 展示图片的ID。
 * @param String saveImgId 图片上传成功时，图片URL保存的地方。
 * @param Integer width 图片展示时的宽度。并非裁剪宽度。
 * @param Integer height 图片展示时的高度。并非裁剪的高度。
 */
function uploadImage(filesUrl, baseJsUrl, ImgId, saveImgId, width, height, uploadUrl) {
	var previewImage = $('#' + ImgId);
	var default_img = $('#'+saveImgId).val();
	imgurl = filesUrl + '' + default_img;
	var imageUrl = default_img.length > 0 ? imgurl : baseJsUrl + 'AjaxUploader/upload_default.png';
	previewImage.empty();
	previewImage.append('<img width="' + width + '" height=" ' + height + '" src="' + imageUrl + '">');
	previewImage.css({"width": width + "px", "height": height + "px", "border": "2px solid #CCD"});
    var uploader = new ss.SimpleUpload({
      button: previewImage,
      url: uploadUrl,
      name: 'uploadfile',
      multipart: true,
      hoverClass: 'hover',
      focusClass: 'focus',
      responseType: 'json',
      startXHR: function() {
          // 开始上传。可以做一些初始化的工作。
      },
      onSubmit: function() {
    	  previewImage.empty();
    	  previewImage.append('<img src="' + baseJsUrl + 'AjaxUploader/upload_loading.png">');
        },
      onComplete: function(filename, response) {
          // 上传完成。
          if (!response) {
        	  previewImage.empty();
        	  previewImage.append('<img src="' + baseJsUrl + 'AjaxUploader/upload_error.png">');
              return;
          }
          if (response.errcode == 0) {
        	  previewImage.empty();
        	  previewImage.append('<img style="width:' + width + 'px;height:' + height + 'px;" src="' + response.data[0]['image_url'] + '"/>');
              $('#' + saveImgId).val(response.data[0]['relative_image_url']);
          } else {
        	  previewImage.empty();
        	  previewImage.append('<img src="' + baseJsUrl + 'AjaxUploader/upload_error.png">');
              dialogTips(response.errmsg, 5);
          }
        },
      onError: function() {
    	  previewImage.empty();
    	  previewImage.append('<img src="' + baseJsUrl + 'AjaxUploader/upload_error.png">');
        }
	});
}