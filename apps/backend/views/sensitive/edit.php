<?php
use common\YUrl;
require_once(APP_VIEW_PATH . DIRECTORY_SEPARATOR . 'common/header.php');
?>

<style type="text/css">
	html{_overflow-y:scroll}
</style>

<div class="pad_10">
<form action="<?php echo YUrl::createAdminUrl('Index', 'Sensitive', 'edit'); ?>" method="post" name="myform" id="myform">
<table cellpadding="2" cellspacing="1" class="table_form" width="100%">

	<tr>
		<th width="80">敏感等级：</th>
		<td>
		  <select name="lv">
    		  <option <?php echo $detail['lv']==1 ? 'selected="selected"' : ''; ?> value="1">普通</option>
    		  <option <?php echo $detail['lv']==2 ? 'selected="selected"' : ''; ?> value="2">中</option>
    		  <option <?php echo $detail['lv']==3 ? 'selected="selected"' : ''; ?> value="3">高</option>
		  </select>
		</td>
	</tr>
	<tr>
		<th width="80">敏感词：</th>
		<td><input type="text" name="val" id="form_val" size="20" class="input-text" value="<?php echo htmlspecialchars($detail['val']); ?>"></td>
	</tr>
    <tr>
	    <td width="100%" align="center" colspan="2">
	       <input type="hidden" name="id" value="<?php echo $detail['id']; ?>" />
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