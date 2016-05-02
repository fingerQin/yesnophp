<?php
use common\YUrl;
require_once(APP_VIEW_PATH . DIRECTORY_SEPARATOR . 'common/header.php');
?>

<div class="subnav">
</div>
<style type="text/css">
	html{_overflow-y:scroll}
</style><div class="pad-lr-10">


<form name="searchform" action="" method="get">
<table width="100%" cellspacing="0" class="search-form">
    <tbody>
		<tr>
		<td>
		<div class="explain-col">
     		<p style="margin-top:10px;">
     		订单编号：<input type="text" value="<?php echo $order_sn; ?>" class="input-text" name="order_sn" />
     		厂商：<select name="shop_id">
     		<option value="-1">不限</option>
     		<?php foreach ($shop_list as $shop): ?>
     		<option <?php echo ($shop['shop_id'] == $shop_id) ? 'selected="selected"' : ''; ?> value="<?php echo $shop['shop_id']; ?>"><?php echo $shop['shop_name']; ?></option>
     		<?php endforeach; ?>
     		</select>
    		下单时间：<input type="text" name="start_time" id="start_time" value="<?php echo $start_time; ?>" size="20" class="date input-text" readonly="" /> ～ <input type="text" name="end_time" id="end_time" value="<?php echo $end_time; ?>" size="20" class="date input-text" readonly="" />
    		<script type="text/javascript">
Calendar.setup({
	weekNumbers: false,
    inputField : "start_time",
    trigger    : "start_time",
    dateFormat: "%Y-%m-%d %H:%I:%S",
    showTime: true,
    minuteStep: 1,
    onSelect   : function() {this.hide();}
});

Calendar.setup({
	weekNumbers: false,
    inputField : "end_time",
    trigger    : "end_time",
    dateFormat: "%Y-%m-%d %H:%I:%S",
    showTime: true,
    minuteStep: 1,
    onSelect   : function() {this.hide();}
});
</script>
    		<input type="submit" name="search" class="button" value="搜索" />
    		</p>
		</div>
		</td>
		</tr>
    </tbody>
</table>
</form>


<form name="myform" id="myform" action="" method="post" >
<div id="table-list-id" class="table-list">

<table width="100%" cellspacing="0">
	<thead>
		<tr>
			<th align="center">
				<input type="checkbox" value="" id="check_box" onclick="selectall('order_ids[]');" />
			</th>
			<th align="center">订单号</th>
			<th align="center">厂商名称</th>
			<th align="center">支付金额</th>
			<th align="center">直接佣金</th>
			<th align="center">间接佣金</th>
			<th align="center">是否已加入账单</th>
			<th align="center">下单时间</th>
		</tr>
	</thead>
    <tbody>
        <?php foreach ($list as $item): ?>
    	<tr>
    		<td align="center" width="35">
    		    <!-- 因为共用一个方法，所以，名称不变。只是加一个is_refund_order来标识。 -->
    			<input type="checkbox" <?php echo $item['is_account_check'] ? 'disabled' : ''; ?> name="order_ids[]" value="<?php echo $item['sub_order_id']; ?>" />
    		</td>
    		<td align="center"><?php echo $item['order_sn'] ?></td>
    		<td align="center"><?php echo $item['shop_name'] ?></td>
    		<td align="center">￥<?php echo $item['payment'] ?></td>
    		<td align="center">￥<?php echo $item['direct_commission'] ?></td>
    		<td align="center">￥<?php echo $item['indirect_commission'] ?></td>
    		<td align="center"><?php echo $item['account_check_name'] ?: '-'; ?></td>
    		<td align="center"><?php echo $item['created_time'] ?></td>
    	</tr>
    	<?php endforeach; ?>
    </tbody>
</table>

<div class="btn"> 
	<input type="button" class="button" name="dosubmit" onclick="javascript:addOrderToAccountCheck()" value="添加到账单" />&nbsp;&nbsp;
</div>
<div id="pages">
<?php echo $page_html; ?>
</div>

</div>

</form>

</div>

<script type="text/javascript">
<!--
function addOrderToAccountCheck() {
	var order_ids = $('#myform [name="order_ids[]"]:checked').serialize() + "&is_refund_order=1";
	if (order_ids.length == 0) {
		dialogTips('请勾选订单', 3);
		return false;
	}
	postDialog('miniAccountCheckList', '<?php echo YUrl::createAdminUrl('Index', 'Cash', 'miniAccountCheckList'); ?>','添加到对账单', '800', '500', order_ids);
}
//-->
</script>

</body>
</html>