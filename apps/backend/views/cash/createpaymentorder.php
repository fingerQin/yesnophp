<?php
use common\YUrl;
require_once(APP_VIEW_PATH . DIRECTORY_SEPARATOR . 'common/header.php');
?>

<style type="text/css">
	html{_overflow-y:scroll}
</style>

<div class="pad_10">
<form action="<?php echo YUrl::createAdminUrl('Index', 'Cash', 'createPaymentOrder'); ?>" method="post" name="dialog_form" id="myform">
<table cellpadding="2" cellspacing="1" class="table_form" width="100%">

	<tr>
		<th width="100">对账单标题：</th>
		<td><input type="text" name="title" id="cash_title" size="20" class="input-text" value=""></td>
	</tr>
	
	<tr>
		<th>厂商</th>
		<td>
			<select name="shop_id">
				<option value="-1">请选择厂商</option>
				<?php foreach ($shop_list as $shop): ?>
				<option value="<?php echo $shop['shop_id']; ?>"><?php echo $shop['shop_name']; ?></option>
				<?php endforeach; ?>
			</select>
		</td>
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