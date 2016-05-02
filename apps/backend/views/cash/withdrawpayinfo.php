<?php
require_once(APP_VIEW_PATH . DIRECTORY_SEPARATOR . 'common/header.php');
?>

<style type="text/css">
	html{_overflow-y:scroll}
</style>

<div class="pad_10">
<table cellpadding="2" cellspacing="1" class="table_form" width="100%">

	<tr>
		<th width="100">日志ID：</th>
		<td><?php echo $detail['log_id']; ?></td>
	</tr>

	<tr>
		<th width="100">支付流水号：</th>
		<td><?php echo $detail['payment_no']; ?></td>
	</tr>
	<tr>
		<th width="100">收款人真实姓名：</th>
		<td><?php echo $detail['realname']; ?></td>
	</tr>
	<tr>
		<th width="100">收款账号类型：</th>
		<td><?php echo $detail['withdraw_type_label']; ?></td>
	</tr>
	<tr>
		<th width="100">收款人账号：</th>
		<td><?php echo $detail['account_num']; ?></td>
	</tr>
	<tr>
		<th width="100">付款金额：</th>
		<td><?php echo $detail['amount']; ?>元</td>
	</tr>
    <tr>
		<th width="100">操作时间：</th>
		<td><?php echo $detail['created_time']; ?></td>
	</tr>
</table>

</div>

<script type="text/javascript">

</script>
</body>
</html>