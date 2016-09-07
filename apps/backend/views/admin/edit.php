<?php
use common\YUrl;
require_once (APP_VIEW_PATH . DIRECTORY_SEPARATOR . 'common/header.php');
?>

<style type="text/css">
html {
	_overflow-y: scroll
}
</style>

<div class="pad_10">
	<form
		action="<?php echo YUrl::createBackendUrl('', 'Admin', 'edit'); ?>"
		method="post" name="myform" id="myform" autocomplete="off">
		<table cellpadding="2" cellspacing="1" class="table_form" width="100%">

			<tr>
				<th width="80">账号：</th>
				<td><input type="text" name="username" id="username" size="20"
					class="input-text" value="<?php echo $detail['username']; ?>">（字母、数字、下划线组成）</td>
			</tr>
			<tr>
				<th width="80">密码：</th>
				<td><input type="password" name="password" id="password" size="20"
					class="input-text" value="">（不填表示不修改）</td>
			</tr>
			<tr>
				<th width="80">真实姓名：</th>
				<td><input type="text" name="realname" id="realname" size="20"
					class="input-text"
					value="<?php echo htmlspecialchars($detail['realname']); ?>"></td>
			</tr>
			<tr>
				<th width="80">手机号码：</th>
				<td><input type="text" name="mobilephone" id="mobilephone" size="20"
					class="input-text" value="<?php echo $detail['mobilephone']; ?>"></td>
			</tr>
			<tr>
				<th width="80">角色：</th>
				<td><select name="roleid">
						<option value="-1">选择角色</option>
		      <?php foreach ($role_list as $role): ?>
		      <option value="<?php echo $role['roleid']; ?>"
							<?php echo ($detail['roleid'] == $role['roleid']) ? 'selected="selected"' : ''; ?>><?php echo $role['rolename']; ?></option>
		      <?php endforeach; ?>
		  </select></td>
			</tr>
			<tr>
				<td width="100%" align="center" colspan="2"><input name="admin_id"
					type="hidden" value="<?php echo $detail['admin_id']; ?>" /> <input
					id="form_submit" type="button" name="dosubmit" class="btn_submit"
					value=" 提交 " /></td>
			</tr>
		</table>

	</form>
</div>

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
});

//-->
</script>

</body>
</html>