<?php
use common\YUrl;
require_once(APP_VIEW_PATH . DIRECTORY_SEPARATOR . 'common/header.php');
?>

<style type="text/css">
	html{_overflow-y:scroll}
</style>

<div class="pad_10">
<form action="<?php echo YUrl::createBackendUrl('', 'Config', 'edit'); ?>" method="post" name="myform" id="myform">
<table cellpadding="2" cellspacing="1" class="table_form" width="100%">

	<tr>
		<th width="80">配置标题：</th>
		<td><input type="text" name="ctitle" id="ctitle" size="20" class="input-text" value="<?php echo htmlspecialchars($detail['ctitle']); ?>">（简要说明）</td>
	</tr>
	<tr>
		<th width="80">配置编码：</th>
		<td><input type="text" name="cname" id="cname" size="20" class="input-text" value="<?php echo htmlspecialchars($detail['cname']); ?>">（字母、数字、下划线组成）</td>
	</tr>
	<tr>
		<th width="80">配置值：</th>
		<td><textarea name="cvalue" id="cvalue" style="width:300px;height:50px;" rows="3" cols="50"><?php echo htmlspecialchars($detail['cvalue']); ?></textarea></td>
	</tr>
	<tr>
		<th width="80">描述：</th>
		<td><textarea name="description" id="description" style="width:300px;height:50px;" rows="3" cols="50"><?php echo htmlspecialchars($detail['description']); ?></textarea></td>
	</tr>
    <tr>
	    <td width="100%" align="center" colspan="2">
	       <input name="config_id" type="hidden" value="<?php echo $detail['config_id']; ?>" />
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