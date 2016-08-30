<?php
use common\YUrl;
require_once(APP_VIEW_PATH . DIRECTORY_SEPARATOR . 'common/header.php');
?>

<style type="text/css">
	html{_overflow-y:scroll}
</style>

<div class="pad_10">
<form action="<?php echo YUrl::createBackendUrl('', 'WeChat', 'addAccount'); ?>" method="post" name="myform" id="myform">
<table cellpadding="2" cellspacing="1" class="table_form" width="100%">
	<tr>
		<th width="80">公众号微信号：</th>
		<td><input type="text" name="wx_account" id="wx_account" size="20" class="input-text" value=""></td>
	</tr>
	<tr>
		<th width="80">公众号编码：</th>
		<td><input type="text" name="wx_sn" id="wx_sn" size="10" class="input-text" value=""></td>
	</tr>
	<tr>
		<th width="80">公众号类型：</th>
		<td>
			<select name="wx_type">
			<?php foreach ($wechat_type_dict as $type_id => $type_name): ?>
				<option value="<?php echo $type_id; ?>"><?php echo $type_name; ?></option>
			<?php endforeach; ?>
			</select>
		</td>
	</tr>
	<tr>
		<th width="80">公众号是否认证：</th>
		<td>
			<select name="wx_auth">
				<option value="1">是</option>
				<option value="0">否</option>
			</select>
		</td>
	</tr>
	<tr>
		<th width="80">公众号APPID：</th>
		<td><input type="text" name="wx_appid" id="wx_appid" size="30" class="input-text" value=""></td>
	</tr>
	<tr>
		<th width="80">公众号secret：</th>
		<td><input type="text" name="wx_appsecret" id="wx_appsecret" size="30" class="input-text" value=""></td>
	</tr>
	<tr>
		<th width="80">公众号TOKEN：</th>
		<td><input type="text" name="wx_token" id="wx_token" size="30" class="input-text" value=""></td>
	</tr>
	<tr>
		<th width="80">公众号AES_KEY：</th>
		<td><input type="text" name="wx_aeskey" id="wx_aeskey" size="30" class="input-text" value=""></td>
	</tr>
	<tr>
		<th width="80">公众号支付证书地址：</th>
		<td><input type="text" name="wx_cert_path" id="wx_cert_path" size="30" class="input-text" value=""></td>
	</tr>
	<tr>
		<th width="80">公众号支付密钥地址：</th>
		<td><input type="text" name="wx_cert_key" id="wx_cert_key" size="30" class="input-text" value=""></td>
	</tr>
	<tr>
		<th width="80">微信支付上报等级：</th>
		<td><select name="wx_report_level">
			<option value="0">关闭上报</option>
			<option value="1">仅错误出错上报</option>
			<option value="2">全量上报</option>
		</select></td>
	</tr>
	<tr>
		<th width="80">支付代理HOST：</th>
		<td><input type="text" name="wx_proxy_host" id="wx_proxy_host" size="15" class="input-text" value="0.0.0.0"></td>
	</tr>
	<tr>
		<th width="80">支付代理端口：</th>
		<td><input type="text" name="wx_proxy_port" id="wx_proxy_port" size="6" class="input-text" value="0"></td>
	</tr>
    <tr>
	    <td width="100%" align="center" colspan="2">
	       <input id="form_submit"  type="button" name="dosubmit" class="btn_submit"  value=" 提交 " />
	    </td>
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