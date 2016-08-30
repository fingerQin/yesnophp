<?php
use common\YUrl;
require_once(APP_VIEW_PATH . DIRECTORY_SEPARATOR . 'common/header.php');
?>

<style type="text/css">
	html{_overflow-y:scroll}
</style>

<div class="pad_10">
<form action="<?php echo YUrl::createBackendUrl('', 'Dict', 'editType'); ?>" method="post" name="myform" id="dialog_form">
<table cellpadding="2" cellspacing="1" class="table_form" width="100%">
	<tr>
		<th width="100">字典名称：</th>
		<td><input type="text" name="type_name" id="dict_type_name" size="40" class="input-text" value="<?php echo htmlspecialchars($dict['type_name']); ?>"></td>
	</tr>
	<tr>
		<th width="100">字典编码：</th>
		<td><input type="text" name="type_code" id="dict_type_code" size="40" class="input-text" value="<?php echo $dict['type_code']; ?>"></td>
	</tr>
	<tr>
		<th width="100">字典介绍：</th>
		<td><textarea name="description" id="dict_description" style="width:292px;" rows="5" cols="50"><?php echo htmlspecialchars($dict['description']); ?></textarea></td>
	</tr>

</table>

<input type="hidden" name="dict_type_id" value="<?php echo $dict['dict_type_id']; ?>" />
<input  type="button" name="dosubmit" class="btn_submit"  id="form_dosubmit" value=" 提交 " />

</form>
</div>

<script type="text/javascript">
$(document).ready(function(){
    $("#form_dosubmit").click(function() {
        $.ajax({
            type: 'post',
            url: $('form').eq(0).attr('action'), 
            dataType: 'json',
            data: $('form').eq(0).serialize(), 
            success: function(data){
                if (data.errcode == 0) {
                	top.right.location.reload();
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