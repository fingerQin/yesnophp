<?php
use common\YUrl;
require_once(APP_VIEW_PATH . DIRECTORY_SEPARATOR . 'common/header.php');
?>

<style type="text/css">
	html{_overflow-y:scroll}
</style>

<div class="pad_10">
<form action="<?php echo YUrl::createBackendUrl('', 'Ad', 'add'); ?>" method="post" name="myform" id="myform">
<table cellpadding="2" cellspacing="1" class="table_form" width="100%">
	<tr>
		<th width="80">广告名称：</th>
		<td><input type="text" name="ad_name" id="ad_name" size="20" class="input-text" value=""></td>
	</tr>
	<tr>
		<th width="80">生效时间：</th>
		<td><input type="text" name="start_time" id="start_time" size="20" class="date input-text" value=""></td>
	</tr>
	<tr>
		<th width="80">失效时间：</th>
		<td><input type="text" name="end_time" id="end_time" size="20" class="date input-text" value=""></td>
	</tr>
	<tr>
		<th width="80">显示状态：</th>
		<td>
		  <select name="display">
		      <option value="1">是</option>
		      <option value="0">否</option>
		  </select>
		</td>
	</tr>
	<tr>
		<th width="80">广告URL：</th>
		<td><input type="text" name="ad_url" id="ad_url" size="30" class="input-text" value=""></td>
	</tr>
	<tr>
		<th width="100">友情链接图片：</th>
		<td>
		    <input type="hidden" name="ad_image_url" id="input_voucher" value="" />
            <div id="previewImage"></div>
		</td>
	</tr>
	<tr>
		<th width="80">备注：</th>
		<td><textarea name="remark" id="remark" style="width:250px;height:50px;" rows="3" cols="50"></textarea></td>
	</tr>
    <tr>
	    <td width="100%" align="center" colspan="2">
	    	<input type="hidden" name="pos_id" value="<?php echo $pos_id; ?>" />
	       <input id="form_submit"  type="button" name="dosubmit" class="btn_submit"  value=" 提交 " />
	    </td>
	</tr>
</table>

</form>
</div>

<script type="text/javascript">
<!--

Calendar.setup({
	weekNumbers: false,
    inputField : "start_time",
    trigger    : "start_time",
    dateFormat: "%Y-%m-%d %H:%I:%S",
    showTime: true,
    minuteStep: 1,
    onSelect   : function() {this.hide();}
});

Calendar.setup({
	weekNumbers: false,
    inputField : "end_time",
    trigger    : "end_time",
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
      url: '<?php echo YUrl::createBackendUrl('', 'Index', 'upload'); ?>',
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

//-->
</script>

</body>
</html>