<?php
use common\YUrl;
require_once(APP_VIEW_PATH . DIRECTORY_SEPARATOR . 'common/header.php');
?>

<style type="text/css">
	html{_overflow-y:scroll}
</style>

<div class="pad_10">
<form action="<?php echo YUrl::createAdminUrl('Index', 'Admin', 'editPwd'); ?>" method="post" name="myform" id="myform" autocomplete="off">
<table cellpadding="2" cellspacing="1" class="table_form" width="100%">
	<tr>
		<th width="80">用户名：</th>
		<td><?php echo $admin_info['username']; ?></td>
	</tr>
	<tr>
		<th width="80">真实姓名：</th>
		<td><?php echo $admin_info['realname']; ?></td>
	</tr>
	<tr>
		<th width="80">手机号码：</th>
		<td><?php echo $admin_info['mobilephone']; ?></td>
	</tr>
	<tr>
		<th width="80">旧密码：</th>
		<td><input type="password" name="old_pwd" id="old_pwd" size="20" class="input-text" value=""></td>
	</tr>
	<tr>
		<th width="80">新密码：</th>
		<td><input type="password" name="new_pwd" id="new_pwd" size="20" class="input-text" value=""></td>
	</tr>
	<tr>
		<th width="80">确认密码：</th>
		<td><input type="password" name="confirm_pwd" id="confirm_pwd" size="20" class="input-text" value=""></td>
	</tr>
    <tr>
	    <td width="100%" align="center" colspan="2">
	       <input id="form_submit" type="button" name="dosubmit" value=" 提交 " />
	    </td>
	</tr>
</table>

</form>
</div>

<script type="text/javascript">
<!--

$(document).ready(function(){
	$('#form_submit').click(function(){
		var new_pwd = $('#new_pwd').val();
		var confirm_pwd = $('#confirm_pwd').val();
		if (new_pwd != confirm_pwd) {
			dialogTips('新密码与确认密码不相同', 3);
			return false;
		}
	    $.ajax({
	    	type: 'post',
            url: $('#myform').attr('action'),
            dataType: 'json',
            data: $('#myform').serialize(),
            success: function(data) {
                if (data.errcode == 0) {
                	window.location.reload();
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