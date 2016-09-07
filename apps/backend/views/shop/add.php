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
		action="<?php echo YUrl::createBackendUrl('', 'Shop', 'add'); ?>"
		method="post">
		<table width="100%" class="table_form contentWrap">
			<tr>
				<th>商家名称：</th>
				<td><input type="text" name="shop_name" id="shop_name"
					style="width: 300px;" class="input-text"></td>
			</tr>
			<tr>
				<th>商家拥有人账号：</th>
				<td><input type="text" name="account" id="account"
					style="width: 120px;" class="input-text">(可以为空)</td>
			</tr>
			<tr>
				<th>联系人：</th>
				<td><input type="text" name="link_man" id="link_man"
					style="width: 120px;" class="input-text"></td>
			</tr>
			<tr>
				<th>联系手机：</th>
				<td><input type="text" name="mobilephone" id="mobilephone"
					style="width: 120px;" class="input-text" /> <span
					style="margin-left: 20px;">联系座机：</span> <input type="text"
					name="telephone" id="telephone" style="width: 120px;"
					class="input-text" /></td>
			</tr>
			<tr>
				<th>联系QQ：</th>
				<td><input type="text" name="qq" id="qq" style="width: 120px;"
					class="input-text" /></td>
			</tr>
			<tr>
				<th>最大商品数：</th>
				<td><input type="text" name="max_goods_count" id="max_goods_count"
					style="width: 50px;" class="input-text" />(即商家最大能添加多少商品)</td>
			</tr>
			<tr>
				<th>是否允许删评：</th>
				<td><select name="is_allow_delete_comment">
						<option value="1">是</option>
						<option value="0">否</option>
				</select> <span style="margin-left: 50px;">是否锁定:</span> <select
					name="is_lock">
						<option value="1">是</option>
						<option value="0">否</option>
				</select> (锁定之后商家以只读模式进入商家中心)</td>
			</tr>
			<tr>
				<th width="100">商家LOGO：</th>
				<td><input type="hidden" name="shop_logo" id="shop_logo" value="" />
					<div id="shop_logo_view"></div></td>
			</tr>
			<tr>
				<th>商家公告：</th>
				<td><textarea rows="3" cols="50" name="shop_notice"></textarea></td>
			</tr>
			<tr>
				<td width="100%" align="center" colspan="2"><input id="form_submit"
					type="button" name="dosubmit" class="btn_submit" value=" 提交 " /></td>
			</tr>
		</table>

	</form>

	<script
		src="<?php echo YUrl::assets('js', '/AjaxUploader/uploadImage.js'); ?>"></script>
	<script type="text/javascript">

var uploadUrl = '<?php echo YUrl::createBackendUrl('', 'Index', 'upload'); ?>';
var baseJsUrl = '<?php echo YUrl::assets('js', ''); ?>';
var filUrl = '<?php echo YCore::config('files_domain_name'); ?>';
uploadImage(filUrl, baseJsUrl, 'shop_logo_view', 'shop_logo', 120, 120, uploadUrl);

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