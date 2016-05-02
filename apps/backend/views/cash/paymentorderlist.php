<?php
use common\YUrl;
require_once(APP_VIEW_PATH . DIRECTORY_SEPARATOR . 'common/header.php');
?>

<div class="subnav">
    <div class="content-menu ib-a blue line-x">
    	<a class="add fb" href="javascript:postDialog('createPaymentOrder', '<?php echo YUrl::createAdminUrl('Index', 'Cash', 'createPaymentOrder'); ?>','创建付款单','300', '120')"><em>创建付款单</em></a>　    
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
			名称：<input type="text" value="<?php echo $title; ?>" class="input-text" name="title" />
			支付状态：<select name="pay_status">
			<option <?php echo $pay_status==-1 ? 'selected="selected"' : ''; ?> value="-1">全部</option>
			<option <?php echo $pay_status==1 ? 'selected="selected"' : ''; ?> value="1">已支付</option>
			<option <?php echo $pay_status==0 ? 'selected="selected"' : ''; ?> value="0">未支付</option>
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
			<th align="center">付款单ID</th>
			<th align="center">付款单名称</th>
			<th align="center">支付金额</th>
			<th align="center">支付状态</th>
			<th align="center">支付时间</th>
			<th align="center">付款凭证</th>
			<th align="center">创建时间</th>
			<th align="center">创建人</th>
			<th align="center">操作</th>
		</tr>
	</thead>
    <tbody>
        <?php foreach ($list as $item): ?>
    	<tr>
    		<td align="center"><?php echo $item['payment_id'] ?></td>
    		<td align="center"><a href="javascript:postDialog('accountCheckOfPaymentOrder', '<?php echo YUrl::createAdminUrl('Index', 'Cash', 'accountCheckOfPaymentOrder', ['payment_id' => $item['payment_id']]); ?>', '<?php echo $item['title']; ?>的对账单列表','900','450')"><?php echo $item['title'] ?></a></td>
    		<td align="center">￥<?php echo $item['payment'] ?></td>
    		<td align="center"><?php echo $item['pay_status'] ? '已支付' : '未支付'; ?></td>
    		<td align="center"><?php echo $item['pay_time_txt']; ?></td>
    		<?php $voucher = YUrl::filePath($item['voucher']); ?>
    		<td align="center"><?php echo $voucher ? "<a target='_blank' href='{$voucher}'><img width='100' src='{$voucher}' /></a>" : ''; ?></td>
    		<td align="center"><?php echo $item['created_time_txt']; ?></td>
    		<td align="center"><?php echo $item['created_by_txt'] ?></td>
    		<td align="center"><a href="javascript:postDialog('uploadPayVoucher', '<?php echo YUrl::createAdminUrl('Index', 'Cash', 'uploadPayVoucher', ['payment_id' => $item['payment_id']]); ?>', '<?php echo $item['title']; ?>','500','250')">[上传付款凭证]</a></td>
    	</tr>
    	<?php endforeach; ?>
    </tbody>
</table>

<div class="btn"> 
</div>
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