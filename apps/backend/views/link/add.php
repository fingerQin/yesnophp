<?php
use common\YUrl;
require_once(APP_VIEW_PATH . DIRECTORY_SEPARATOR . 'common/header.php');
?>

<style type="text/css">
	html{_overflow-y:scroll}
</style>

<div class="common-form">
<form name="myform" id="myform" action="<?php echo YUrl::createAdminUrl('Index', 'Link', 'add'); ?>" method="post">
<table width="100%" class="table_form contentWrap">
      <tr>
        <th width="120">上级菜单：</th>
        <td>
        <select name="cat_id">
        <?php foreach ($cat_list as $menu): ?>
        <option value="<?php echo $menu['cat_id']; ?>"><?php echo $menu['cat_name']; ?></option>
			<?php if (isset($menu['sub'])): ?>
			<?php foreach ($menu['sub'] as $sub_m): ?>
			<option value="<?php echo $sub_m['cat_id']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;├─<?php echo $sub_m['cat_name']; ?></option>
			<?php if (isset($sub_m['sub'])): ?>
			<?php foreach ($sub_m['sub'] as $ss_m): ?>
			<option value="<?php echo $ss_m['cat_id']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;│&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;├─<?php echo $ss_m['cat_name']; ?></option>
			<?php endforeach; ?>
			<?php endif; ?>
			<?php endforeach; ?>
			<?php endif; ?>
		<?php endforeach; ?>
		</select>
		</td>
    </tr>
    <tr>
        <th> 友情链接名称：</th>
        <td><input type="text" name="link_name" id="link_name" style="width:120px;" class="input-text" ></td>
    </tr>
	<tr>
        <th>友情链接URL：</th>
        <td><input type="text" name="link_url" id="link_url" style="width:250px;" class="input-text" /></td>
    </tr>
	<tr>
        <th>是否显示：</th>
        <td>
            <select name="display">
            <option value="1">是</option>
            <option value="0">否</option>
            </select>
        </td>
    </tr>
    <tr>
		<th width="100">友情链接图片：</th>
		<td>
		    <input type="hidden" name="image_url" id="input_voucher" value="" />
            <div id="previewImage"></div>
		</td>
	</tr>
    <tr>
	    <td width="100%" align="center" colspan="2">
	       <input id="form_submit" type="button" name="dosubmit" value=" 提交 " />
	    </td>
	</tr>
</table>

</form>

<script type="text/javascript">
<!--

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

//-->
</script>

</body>
</html>