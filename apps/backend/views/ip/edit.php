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
	<form action="<?php echo YUrl::createBackendUrl('', 'Ip', 'edit'); ?>"
		method="post" name="myform" id="myform">
		<table cellpadding="2" cellspacing="1" class="table_form" width="100%">

			<tr>
				<th width="80">IP地址：</th>
				<td><input type="text" name="ip" id="ip" size="20"
					class="input-text"
					value="<?php echo htmlspecialchars($detail['ip']); ?>"></td>
			</tr>
			<tr>
				<th width="80">备注：</th>
				<td><textarea name="remark" id="remark"
						style="width: 300px; height: 50px;" rows="3" cols="50"><?php echo htmlspecialchars($detail['remark']); ?></textarea></td>
			</tr>
			<tr>
				<td width="100%" align="center" colspan="2"><input name="id"
					type="hidden" value="<?php echo $detail['id']; ?>" /> <input
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