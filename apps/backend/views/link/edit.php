<?php
use common\YUrl;
use common\YCore;
require_once (APP_VIEW_PATH . DIRECTORY_SEPARATOR . 'common/header.php');
?>

<style type="text/css">
html {
	_overflow-y: scroll
}
</style>

<div class="common-form">
	<form name="myform" id="myform"
		action="<?php echo YUrl::createBackendUrl('', 'Link', 'edit'); ?>"
		method="post">
		<table width="100%" class="table_form contentWrap">
			<tr>
				<th width="120">上级菜单：</th>
				<td><select name="cat_id">
        <?php foreach ($cat_list as $menu): ?>
        <option
							<?php echo $menu['cat_id'] == $detail['cat_id'] ? 'selected="selected"' : ''; ?>
							value="<?php echo $menu['cat_id']; ?>"><?php echo $menu['cat_name']; ?></option>
			<?php if (isset($menu['sub'])): ?>
			<?php foreach ($menu['sub'] as $sub_m): ?>
			<option
							<?php echo $menu['cat_id'] == $detail['cat_id'] ? 'selected="selected"' : ''; ?>
							value="<?php echo $sub_m['cat_id']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;├─<?php echo $sub_m['cat_name']; ?></option>
			<?php if (isset($sub_m['sub'])): ?>
			<?php foreach ($sub_m['sub'] as $ss_m): ?>
			<option
							<?php echo $menu['cat_id'] == $detail['cat_id'] ? 'selected="selected"' : ''; ?>
							value="<?php echo $ss_m['cat_id']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;│&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;├─<?php echo $ss_m['cat_name']; ?></option>
			<?php endforeach; ?>
			<?php endif; ?>
			<?php endforeach; ?>
			<?php endif; ?>
		<?php endforeach; ?>
		</select></td>
			</tr>
			<tr>
				<th>友情链接名称：</th>
				<td><input type="text" name="link_name" id="link_name"
					style="width: 120px;"
					value="<?php echo htmlspecialchars($detail['link_name']); ?>"
					class="input-text"></td>
			</tr>
			<tr>
				<th>友情链接URL：</th>
				<td><input type="text" name="link_url" id="link_url"
					style="width: 250px;" class="input-text"
					value="<?php echo htmlspecialchars($detail['link_url']); ?>" /></td>
			</tr>
			<tr>
				<th>是否显示：</th>
				<td><select name="display">
						<option
							<?php echo $menu['display'] ? 'selected="selected"' : ''; ?>
							value="1">是</option>
						<option
							<?php echo !$menu['display'] ? 'selected="selected"' : ''; ?>
							value="0">否</option>
				</select></td>
			</tr>
			<tr>
				<th width="100">友情链接图片：</th>
				<td><input type="hidden" name="image_url" id="input_voucher"
					value="<?php echo $detail['image_url']; ?>" />
					<div id="previewImage">
						<img style="max-width: 119px; max-height: 119px;"
							src="<?php echo $detail['image_url'] ? YUrl::filePath($detail['image_url']) : ''; ?>" />
					</div></td>
			</tr>
			<tr>
				<td width="100%" align="center" colspan="2"><input type="hidden"
					name="link_id" value="<?php echo $detail['link_id']; ?>" /> <input
					id="form_submit" type="button" name="dosubmit" class="btn_submit"
					value=" 提交 " /></td>
			</tr>
		</table>

	</form>

	<script
		src="<?php echo YUrl::assets('js', '/AjaxUploader/uploadImage.js'); ?>"></script>
	<script type="text/javascript">

var uploadUrl = '<?php echo YUrl::createBackendUrl('', 'Index', 'upload'); ?>';
var baseJsUrl = '<?php echo YUrl::assets('js', ''); ?>';
var filUrl = '<?php echo YCore::config('files_domain_name'); ?>';
uploadImage(filUrl, baseJsUrl, 'previewImage', 'input_voucher', 120, 120, uploadUrl);

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
});

</script>

	</body>
	</html>