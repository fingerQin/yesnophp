<?php
use common\YUrl;
require_once(APP_VIEW_PATH . DIRECTORY_SEPARATOR . 'common/header.php');
?>

<div class="subnav">
</div>

<style type="text/css">
	html{_overflow-y:scroll}
</style>

<div class="pad-lr-10">

<form name="searchform" action="" method="get">
<table width="100%" cellspacing="0" class="search-form">
    <tbody>
		<tr>
		<td>
		<div class="explain-col">
			<p>
			厂商名称：<input type="text" value="<?php echo $shop_name; ?>" class="input-text" name="shop_name" />
    		<input style="margin-left:20px;" type="submit" name="search" class="button" value="搜索" />
    		</p>
		</div>
		</td>
		</tr>
    </tbody>
</table>
</form>

<div id="table-list-id" class="table-list">

<table width="100%" cellspacing="0">
	<thead>
		<tr>
			<th align="center">厂商ID</th>
			<th align="center">厂商名称</th>
			<th align="center">联系人</th>
			<th align="center">支付总额</th>
			<th align="center">已支付总额</th>
			<th align="center">未支付总额</th>
		</tr>
	</thead>
    <tbody>
        <?php foreach ($list as $item): ?>
    	<tr>
    		<td align="center"><?php echo $item['shop_id'] ?></td>
    		<td align="center"><?php echo $item['shop_name']; ?></td>
    		<td align="center"><?php echo $item['contacter']; ?></td>
    		<td align="center">￥<?php echo $item['total_money']; ?>元</td>
    		<td align="center">￥<?php echo $item['paid_money']; ?>元</td>
    		<td align="center">￥<?php echo $item['residue_money']; ?>元</td>
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
</div>

</body>
</html>