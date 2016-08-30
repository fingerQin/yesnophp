<?php
use common\YUrl;
require_once(APP_VIEW_PATH . DIRECTORY_SEPARATOR . 'common/header.php');
?>

<div class="subnav">
    <div class="content-menu ib-a blue line-x">
    	<a class="add fb" href="javascript:postDialog('addShop', '<?php echo YUrl::createBackendUrl('', 'Shop', 'add'); ?>', '添加商家', 600, 530)"><em>添加商家</em></a>
    	<a href='javascript:;' class="on"><em>商家列表</em></a>    
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
     		<p style="margin-top:10px;">
     		<input type="text" value="<?php echo $shop_name; ?>" class="input-text" name="shop_name" placeholder="商家名称" />
    		<input type="submit" name="search" class="button" value="搜索" />
    		</p>
		</div>
		</td>
		</tr>
    </tbody>
</table>
</form>


<form name="myform" id="myform" action="" method="post" >
<div class="table-list">
<table width="100%" cellspacing="0">
	<thead>
		<tr>
			<th align="center">商家LOGO</th>
			<th align="center">商家名称</th>
			<th align="center">联系人</th>
			<th align="center">联系手机</th>
			<th align="center">联系座机</th>
			<th align="center">QQ</th>
			<th align="center">最大商品数</th>
			<th align="center">删评</th>
			<th align="center">锁定</th>
			<th width="120" align="center">修改时间</th>
			<th width="120" align="center">创建时间</th>
			<th width="100" align="center">管理操作</th>
		</tr>
	</thead>
    <tbody>
    <?php foreach ($list as $item): ?>
    	<tr>
    		<td align="center"><img width="120" src="<?php echo $item['shop_logo']; ?>" /></td>
    		<td align="center"><?php echo $item['shop_name']; ?></td>
    		<td align="center"><?php echo $item['link_man']; ?></td>
    		<td align="center"><?php echo $item['mobilephone']; ?></td>
    		<td align="center"><?php echo $item['telephone']; ?></td>
    		<td align="center"><?php echo $item['qq']; ?></td>
    		<td align="center"><?php echo $item['max_goods_count']; ?></td>
    		<td align="center"><?php echo $item['is_allow_delete_comment'] ? '是' : '否'; ?></td>
    		<td align="center"><?php echo $item['is_lock'] ? '是' : '否'; ?></td>
    		<td align="center"><?php echo date('Y-m-d H:i:s', $item['modified_time']); ?></td>
    		<td align="center"><?php echo date('Y-m-d H:i:s', $item['created_time']); ?></td>
    		<td align="center">
    		<a href="###" onclick="edit(<?php echo $item['shop_id'] ?>, '<?php echo $item['shop_name'] ?>')" title="修改">修改</a> |  
    		<a href="###" onclick="deleteDialog('deleteShop', '<?php echo YUrl::createBackendUrl('', 'Shop', 'delete', ['shop_id' => $item['shop_id']]); ?>', '<?php echo $item['shop_name'] ?>')" title="删除">删除</a>  
    		</td>
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
function edit(id, name) {
	var title = '修改『' + name + '』';
	var page_url = "<?php echo YUrl::createBackendUrl('', 'Shop', 'edit'); ?>?shop_id="+id;
	postDialog('editShop', page_url, title, 600, 530);
}
</script>
</body>
</html>