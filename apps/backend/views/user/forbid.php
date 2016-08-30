<?php
use common\YUrl;
require_once(APP_VIEW_PATH . DIRECTORY_SEPARATOR . 'common/header.php');
?>

<style type="text/css">
	html{_overflow-y:scroll}
</style>

<div class="pad_10">
<form action="<?php echo YUrl::createBackendUrl('', 'User', 'forbid'); ?>" method="post" name="myform" id="myform">
<table cellpadding="2" cellspacing="1" class="table_form" width="100%">
	<tr>
		<th width="100">封禁类型：</th>
		<td>
		<select name="ban_type" id="ban_type">
		  <option value="1">永久封禁</option>
		  <option value="2">临时封禁</option>
		</select>
		</td>
	</tr>
	<tr class="ban_time" style="display:none;">
		<th width="100">封禁开始时间：</th>
		<td>
    		<input type="text" name="ban_start_time" id="ban_start_time" value="" size="20" class="date input-text" />
		</td>
	</tr>
	<tr class="ban_time" style="display:none;">
	   <th>封禁结束时间：</th>
	   <td><input type="text" name="ban_end_time" id="ban_end_time" value="" size="20" class="date input-text" /></td>
	</tr>
	<tr>
		<th width="100">封禁原因：</th>
		<td><textarea rows="5" cols="30" name="ban_reason"></textarea></td>
	</tr>
    <tr>
	    <td width="100%" align="center" colspan="2">
	       <input type="hidden" name="user_id" value="<?php echo $user_id; ?>" />
	       <input id="form_submit"  type="button" name="dosubmit" class="btn_submit"  value=" 提交 " />
	    </td>
	</tr>
</table>

</form>
</div>

<script type="text/javascript">
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

	$('#ban_type').change(function(){
		if (this.value == 1) {
			 $('.ban_time').hide();
		} else {
			$('.ban_time').show();
		}
	});
});

Calendar.setup({
	weekNumbers: false,
    inputField : "ban_start_time",
    trigger    : "ban_start_time",
    dateFormat: "%Y-%m-%d %H:%I:%S",
    showTime: true,
    minuteStep: 1,
    onSelect   : function() {this.hide();}
});

Calendar.setup({
	weekNumbers: false,
    inputField : "ban_end_time",
    trigger    : "ban_end_time",
    dateFormat: "%Y-%m-%d %H:%I:%S",
    showTime: true,
    minuteStep: 1,
    onSelect   : function() {this.hide();}
});

</script>

</body>
</html>