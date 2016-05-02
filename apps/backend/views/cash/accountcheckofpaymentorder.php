<?php
use common\YUrl;
require_once(APP_VIEW_PATH . DIRECTORY_SEPARATOR . 'common/header.php');
?>

<div class="subnav">
    <div class="content-menu ib-a blue line-x">
    	<a class="add fb" href="javascript:postDialog('createAccountCheck', '<?php echo YUrl::createAdminUrl('Index', 'Cash', 'createAccountCheck'); ?>','创建对账单','450', '120')"><em>创建对账单</em></a>　    
    </div>
</div>
<style type="text/css">
	html{_overflow-y:scroll}
</style><div class="pad-lr-10">


<form name="myform" id="myform" action="?m=goods&c=shop&a=listorder" method="post" >
<div id="table-list-id" class="table-list">

<table width="100%" cellspacing="0">
	<thead>
		<tr>
			<th align="center">账单ID</th>
			<th align="center">[准备状态]账单名称</th>
			<th align="center">账单总额</th>
			<th align="center">直接佣金总和</th>
			<th align="center">间接佣金总和</th>
			<th align="center">厂商确认时间</th>
			<th align="center">厂商确认人</th>
			<th align="center">操作</th>
		</tr>
	</thead>
    <tbody>
        <?php foreach ($list as $item): ?>
    	<tr>
    		<td align="center"><?php echo $item['account_id'] ?></td>
    		<td align="center"><span style="color:#F60;">[<?php echo $item['is_ready'] ? 'ok' : 'ing'; ?>]</span><?php echo $item['title']; ?></td>
    		<td align="center">￥<?php echo $item['total_payment'] ?></td>
    		<td align="center">￥<?php echo $item['direct_commission']; ?></td>
    		<td align="center">￥<?php echo $item['indirect_commission']; ?></td>
    		<td align="center"><?php echo $item['confirm_time_txt']; ?></td>
    		<td align="center"><?php echo $item['confirm_by_txt']; ?></td>
    		<td align="center"><a href="###" onclick="deleteDialog('deleteAccountCheckOfPaymentOrder', '<?php echo YUrl::createAdminUrl('Index', 'Cash', 'deleteAccountCheckOfPaymentOrder', ['account_id' => $item['account_id'], 'payment_id' => $item['payment_id']]); ?>', '<?php echo $item['title'] ?>')" title="删除">[删除]</a></td>
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


</body>
</html>