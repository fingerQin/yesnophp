<?php
use common\YUrl;
require_once(APP_VIEW_PATH . DIRECTORY_SEPARATOR . 'common/header.php');
?>

<style type="text/css">
	html{_overflow-y:scroll}
</style>

<div class="pad_10">
<form enctype="multipart/form-data" action="<?php echo YUrl::createAdminUrl('Index', 'Cash', 'uploadPayVoucher'); ?>" method="post" name="dialog_form" id="myform">
<table cellpadding="2" cellspacing="1" class="table_form" width="100%">
	<tr>
		<th width="100">付款时间：</th>
		<td><input type="text" name="pay_time" id="pay_time" size="25" class="input-text" value=""></td>
	</tr>
	<tr>
		<th width="100">付款凭证：</th>
		<td>
		    <input type="hidden" name="voucher" id="input_voucher" value="" />
            <div id="previewImage"></div>
		</td>
	</tr>
	<tr>
	    <td width="100%" align="center" colspan="2">
	       <input type="hidden" name="payment_id" value="<?php echo $detail['payment_id']; ?>" />
	       <input id="form_submit" type="button" name="dosubmit" value=" 提交 " />
	    </td>
	</tr>
</table>

</form>
</div>

<script type="text/javascript">
Calendar.setup({
	weekNumbers: false,
    inputField : "pay_time",
    trigger    : "pay_time",
    dateFormat: "%Y-%m-%d %H:%I:%S",
    showTime: true,
    minuteStep: 1,
    onSelect   : function() {this.hide();}
});

$(document).ready(function(){
	$('#form_submit').click(function(){
	    $.ajax({
	    	type: 'post',
            url: $('form').eq(0).attr('action'),
            dataType: 'json',
            data: $('form').eq(0).serialize(),
            success: function(data) {
                if (data.errcode == 0) {
                	top.dialog.getCurrent().close({"refresh" : 1});
                } else {
                	dialogTips(data.errmsg, 3);
                }
            }
	    });
	});

	/* 图片上传 */
	var previewImage = $('#previewImage');
	previewImage.css({"width": "120px", "height": "120px", "border": "2px solid #CCD"});
	previewImage.empty();
	previewImage.append('<img src="<?php echo YUrl::assets('js', '/AjaxUploader/upload_default.png') ?>">');
    var uploader = new ss.SimpleUpload({
      button: previewImage,
      url: '<?php echo YUrl::createAdminUrl('Index', 'Index', 'upload'); ?>',
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
    	  previewImage.append('<img src="<?php echo YUrl::assets('js', '/AjaxUploader/upload_loading.png') ?>">');
        },
      onComplete: function(filename, response) {
          // 上传完成。
          if (!response) {
        	  previewImage.empty();
        	  previewImage.append('<img src="<?php echo YUrl::assets('js', '/AjaxUploader/upload_error.png') ?>">');
              return;
          }
          if (response.errcode == 0) {
              $('#previewImage').empty();
              $('#previewImage').append('<img style="max-width:119px;max-height:119px;" src="' + response.data[0]['image_url'] + '"/>');
              $('#input_voucher').val(response.data[0]['relative_image_url']);
          } else {
        	  previewImage.empty();
        	  previewImage.append('<img src="<?php echo YUrl::assets('js', '/AjaxUploader/upload_error.png') ?>">');
              dialogTips(response.errmsg, 5);
          }
        },
      onError: function() {
    	  previewImage.empty();
    	  previewImage.append('<img src="<?php echo YUrl::assets('js', '/AjaxUploader/upload_error.png') ?>">');
        }
	});
});

</script>

</body>
</html>