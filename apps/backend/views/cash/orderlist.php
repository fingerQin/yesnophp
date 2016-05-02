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
     		订单编号：<input type="text" value="<?php echo $search['order_sn']; ?>" class="input-text" name="order_sn" />
     		订单状态：<select name="order_status">
     		<option <?php echo (-1 == $search['order_status']) ? 'selected="selected"' : ''; ?> value="-1">全部</option>
     		<option <?php echo (0 == $search['order_status']) ? 'selected="selected"' : ''; ?> value="0">待付款</option>
     		<option <?php echo (2 == $search['order_status']) ? 'selected="selected"' : ''; ?> value="2">已支付（待发货）</option>
     		<option <?php echo (3 == $search['order_status']) ? 'selected="selected"' : ''; ?> value="3">已发货（待收货）</option>
     		<option <?php echo (4 == $search['order_status']) ? 'selected="selected"' : ''; ?> value="4">交易成功</option>
     		<option <?php echo (5 == $search['order_status']) ? 'selected="selected"' : ''; ?> value="5">交易关闭</option>
     		<option <?php echo (6 == $search['order_status']) ? 'selected="selected"' : ''; ?> value="6">交易取消</option>
     		<option <?php echo (7 == $search['order_status']) ? 'selected="selected"' : ''; ?> value="7">已支付（未退款）</option>
     		<option <?php echo (8 == $search['order_status']) ? 'selected="selected"' : ''; ?> value="8">已支付（含退款）</option>
     		</select>
     		厂商：<select name="shop_id">
     		<option value="-1">不限</option>
     		<?php foreach ($shop_list as $shop): ?>
     		<option <?php echo ($shop['shop_id'] == $search['shop_id']) ? 'selected="selected"' : ''; ?> value="<?php echo $shop['shop_id']; ?>"><?php echo $shop['shop_name']; ?></option>
     		<?php endforeach; ?>
     		</select>
     		收货人姓名：<input type="text" value="<?php echo $search['receiver_name']; ?>" class="input-text" name="receiver_name" />
     		收货人手机：<input type="text" value="<?php echo $search['receiver_mobile']; ?>" class="input-text" name="receiver_mobile" />
     		<br />
    		</p>
    		<p style="margin-top:10px;">
    		下单时间：<input type="text" name="start_time" id="start_time" value="<?php echo $search['start_time']; ?>" size="20" class="date input-text" readonly="" /> ～ <input type="text" name="end_time" id="end_time" value="<?php echo $search['end_time']; ?>" size="20" class="date input-text" readonly="" />
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
            支付时间：<input type="text" name="start_pay_time" id="start_pay_time" value="<?php echo $search['start_pay_time']; ?>" size="20" class="date input-text" readonly="" /> ～ <input type="text" name="end_pay_time" id="end_pay_time" value="<?php echo $search['end_pay_time']; ?>" size="20" class="date input-text" readonly="" />
                		<script type="text/javascript">
            Calendar.setup({
            	weekNumbers: false,
                inputField : "start_pay_time",
                trigger    : "start_pay_time",
                dateFormat: "%Y-%m-%d %H:%I:%S",
                showTime: true,
                minuteStep: 1,
                onSelect   : function() {this.hide();}
            });
            
            Calendar.setup({
            	weekNumbers: false,
                inputField : "end_pay_time",
                trigger    : "end_pay_time",
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
			<th align="center">订单编码</th>
			<th align="center">退款状态</th>
			<th align="center">支付渠道</th>
			<th align="center">订单状态</th>
			<th align="center">厂商名称</th>
			<th align="center">支付状态</th>
			<th align="center">支付金额</th>
			<th align="center">支付时间</th>
			<th align="center">是否已加入账单</th>
			<th align="center">下单时间</th>
		</tr>
	</thead>
    <tbody>
        <?php foreach ($list as $item): ?>
    	<tr>
    		<td align="center" width="35">
    			<input type="checkbox" <?php echo $item['is_account_check'] ? 'disabled' : ''; ?> name="order_ids[]" value="<?php echo $item['order_id']; ?>" />
    		</td>
    		<td align="center"><?php echo $item['order_sn'] ?></td>
    		<td align="center"><?php echo $item['refund_status'] > 0 ? '退款中' : '未退款'; ?></td>
    		<td align="center"><?php echo $item['payment_type_txt'] ?></td>
    		<td align="center"><?php echo $item['order_status_txt'] ?></td>
    		<td align="center"><?php echo $item['shop_name'] ?></td>
    		<td align="center"><?php echo $item['pay_status'] ? '<span style="color:#F60;font-weight:bold;">已支付</span>' : '未支付'; ?></td>
    		<td align="center">￥<?php echo $item['payment']; ?></td>
    		<td align="center"><?php echo $item['pay_time'] ?: '—'; ?></td>
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
	var order_ids = $('#myform [name="order_ids[]"]:checked').serialize();
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