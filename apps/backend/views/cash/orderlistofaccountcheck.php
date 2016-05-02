<?php
use common\YUrl;
require_once(APP_VIEW_PATH . DIRECTORY_SEPARATOR . 'common/header.php');
?>

<div class="subnav">
</div>
<style type="text/css">
	html{_overflow-y:scroll}
</style><div class="pad-lr-10">


<form name="myform" id="myform" action="" method="post" >
<div id="table-list-id" class="table-list">

<table width="100%" cellspacing="0">
	<thead>
		<tr>
			<th align="center">订单编码</th>
			<th align="center">订单类型</th>
			<th align="center">订单状态</th>
			<th align="center">厂商名称</th>
			<th align="center">支付状态</th>
			<th align="center">支付金额</th>
			<th align="center">支付时间</th>
			<th align="center">退款金额</th>
			<th align="center">添加到对账单时间</th>
			<th align="center">操作</th>
		</tr>
	</thead>
    <tbody>
        <?php foreach ($list as $item): ?>
    	<tr>
    		<td align="center"><?php echo $item['order_sn'] ?></td>
    		<td align="center"><?php echo $item['sub_order_id'] > 0 ? '退款单' : '购买单'; ?></td>
    		<td align="center"><?php echo $item['order_status_txt'] ?></td>
    		<td align="center"><?php echo $item['shop_name'] ?></td>
    		<td align="center"><?php echo $item['pay_status'] ? '<span style="color:#F60;font-weight:bold;">已支付</span>' : '未支付'; ?></td>
    		<td align="center">￥<?php echo $item['payment']; ?></td>
    		<td align="center"><?php echo $item['pay_time'] ?: '—'; ?></td>
    		<td align="center">￥<?php echo $item['refund_money']; ?></td>
    		<td align="center"><?php echo date('Y-m-d H:i:s', $item['created_time']); ?></td>
    		<td align="center"><a href="###" onclick="deleteDialog('deleteType', '<?php echo YUrl::createAdminUrl('Index', 'Cash', 'deleteOrderOfAccountCheck', ['id' => $item['id']]); ?>', '<?php echo $item['order_sn'] ?>')" title="删除">[删除]</a></td>
    	</tr>
    	<?php endforeach; ?>
    </tbody>
</table>

<div id="pages">
<?php echo $page_html; ?>
</div>

</div>

</form>

</div>

<script type="text/javascript">


</script>
</body>
</html>