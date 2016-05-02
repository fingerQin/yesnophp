<?php
use common\YUrl;
require_once(APP_VIEW_PATH . DIRECTORY_SEPARATOR . 'common/header.php');
?>

<div class="subnav">
    <div class="content-menu ib-a blue line-x">
    	<a class="add fb" href="javascript:postDialog('createAccountCheck', '<?php echo YUrl::createAdminUrl('Index', 'Cash', 'createAccountCheck'); ?>','创建对账单','450','120')"><em>创建对账单</em></a>　    
    </div>
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
			<p>
			账单名称：<input type="text" value="<?php echo $title; ?>" class="input-text" name="title" />
			对账单准备状态：<select name="ready_status">
			<option <?php echo $ready_status==-1 ? 'selected="selected"' : ''; ?> value="-1">全部</option>
			<option <?php echo $ready_status==1 ? 'selected="selected"' : ''; ?> value="1">已准备完成</option>
			<option <?php echo $ready_status==0 ? 'selected="selected"' : ''; ?> value="0">正在完成中</option>
			</select>
			厂商确认状态：<select name="shop_confirm_status">
			<option <?php echo $shop_confirm_status==-1 ? 'selected="selected"' : ''; ?> value="-1">全部</option>
			<option <?php echo $shop_confirm_status==1 ? 'selected="selected"' : ''; ?> value="1">已确认</option>
			<option <?php echo $shop_confirm_status==0 ? 'selected="selected"' : ''; ?> value="0">未确认</option>
			</select>
    		<input style="margin-left:20px;" type="submit" name="search" class="button" value="搜索" />
    		</p>
		</div>
		</td>
		</tr>
    </tbody>
</table>
</form>


<form name="myform" id="myform" action="?m=goods&c=shop&a=listorder" method="post" >
<div id="table-list-id" class="table-list">

<table width="100%" cellspacing="0">
	<thead>
		<tr>
			<th align="center">
				<input type="checkbox" value="" id="check_box" onclick="selectall('account_ids[]');" />
			</th>
			<th align="center">账单ID</th>
			<th align="center">[准备状态]账单名称</th>
			<th align="center">账单总额<br />(已扣除退款金额)</th>
			<th align="center">直接佣金总和</th>
			<th align="center">间接佣金总和</th>
			<th align="center">退款金额</th>
			<th align="center">剩余</th>
			<th align="center">订单数量</th>
			<th align="center">厂商确认时间</th>
			<th align="center">厂商确认人</th>
			<th align="center">已加入的付款单</th>
			<th align="center">创建时间</th>
			<th align="center">创建人</th>
			<th align="center">发送给厂商确认</th>
		</tr>
	</thead>
    <tbody>
        <?php foreach ($list as $item): ?>
    	<tr>
    		<td align="center" width="35">
    			<input <?php echo $item['is_payemnt'] ? 'disabled' : ''; ?> type="checkbox" name="account_ids[]" value="<?php echo $item['account_id']; ?>" />
    		</td>
    		<td align="center"><?php echo $item['account_id'] ?></td>
    		<td align="center"><a href="javascript:postDialog('orderListOfAccountCheck', '<?php echo YUrl::createAdminUrl('Index', 'Cash', 'orderListOfAccountCheck', ['account_id' => $item['account_id']]); ?>', '<?php echo $item['title']; ?>','900','450')"><span style="color:#F60;">[<?php echo $item['is_ready'] ? 'ok' : 'ing'; ?>]</span><?php echo $item['title']; ?></a></td>
    		<td align="center">￥<?php echo $item['total_payment'] ?></td>
    		<td align="center">￥<?php echo $item['direct_commission']; ?></td>
    		<td align="center">￥<?php echo $item['indirect_commission']; ?></td>
    		<td align="center">￥<?php echo $item['refund_money']; ?></td>
    		<td align="center">￥<?php echo $item['residue_payment']; ?></td>
    		<td align="center"><?php echo $item['order_count']; ?></td>
    		<td align="center"><?php echo $item['confirm_time_txt']; ?></td>
    		<td align="center"><?php echo $item['confirm_by_txt']; ?></td>
    		<td align="center"><?php echo $item['payemnt_title']; ?></td>
    		<td align="center"><?php echo $item['created_time_txt']; ?></td>
    		<td align="center"><?php echo $item['created_by_txt'] ?></td>
    		<td align="center">
    		<?php if ($item['is_ready'] && !$item['confirm_by']): ?>
    		<span>等待厂商确认</span>
    		<?php elseif(!$item['is_ready']): ?>
    		<a href="###" onclick="normalDialog('changeReadyStatus', '<?php echo YUrl::createAdminUrl('Index', 'Cash', 'readyDo', ['account_id' => $item['account_id']]); ?>', '<?php echo "您确认要装对账单【{$item['title']}】提交给厂商确认吗？" ?>')" title="删除">提交厂商确认</a>
    		<?php else: ?>
    		<span>厂商已确认</span>
    		<?php endif; ?>
    		</td>
    	</tr>
    	<?php endforeach; ?>
    </tbody>
</table>

<div class="btn"> 
	<input type="button" class="button" name="dosubmit" onclick="javascript:addAccountCheckToPaymentOrder()" value="添加到付款单" />&nbsp;&nbsp;
</div>
<div id="pages">
<?php echo $page_html; ?>
</div>

</div>

</form>

</div>

<script type="text/javascript">
<!--
function addAccountCheckToPaymentOrder() {
	var account_ids = $('#myform [name="account_ids[]"]:checked').serialize();
	if (account_ids.length == 0) {
		dialogTips('请勾选对账单', 3);
		return false;
	}
	postDialog('miniPaymentOrderList', '<?php echo YUrl::createAdminUrl('Index', 'Cash', 'miniPaymentOrderList'); ?>','添加到付款单', '800', '450', account_ids);
}
//-->
</script>

</body>
</html>