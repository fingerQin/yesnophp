<?php
use common\YUrl;
require_once(APP_VIEW_PATH . DIRECTORY_SEPARATOR . 'common/header.php');
?>

<div class="subnav">
    <div class="content-menu ib-a blue line-x">
    	<a class="add fb" href="javascript:postDialog('addLink', '<?php echo YUrl::createAdminUrl('Index', 'Link', 'add'); ?>', '添加友情链接', 400, 300)"><em>添加友情链接</em></a>
    	<a href='javascript:;' class="on"><em>友情链接列表</em></a>    
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
     		<input type="text" value="<?php echo $keyword; ?>" class="input-text" name="keyword" placeholder="友情链接名称" />
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
			<th align="center">ID</th>
			<th align="center">友情链接名称</th>
			<th align="center">分类名称</th>
			<th align="center">友情链接URL</th>
			<th align="center">友情链接图片</th>
			<th align="center">是否显示</th>
			<th width="120" align="center">修改时间</th>
			<th width="120" align="center">创建时间</th>
			<th width="100" align="center">管理操作</th>
		</tr>
	</thead>
    <tbody>
    <?php foreach ($list as $item): ?>
    	<tr>
    		<td align="center"><?php echo $item['link_id']; ?></td>
    		<td align="center"><?php echo $item['link_name']; ?></td>
    		<td align="center"><?php echo $item['cat_name']; ?></td>
    		<td align="center"><img width="60" src="<?php echo $item['image_url'] ? YUrl::filePath($item['image_url']) : ''; ?>" /></td>
    		<td align="center"><?php echo $item['cat_name']; ?></td>
    		<td align="center"><?php echo $item['display'] ? '显示' : '隐藏'; ?></td>
    		<td align="center"><?php echo date('Y-m-d H:i:s', $item['modified_time']); ?></td>
    		<td align="center"><?php echo date('Y-m-d H:i:s', $item['created_time']); ?></td>
    		<td align="center">
    		<a href="###" onclick="edit(<?php echo $item['link_id'] ?>, '<?php echo $item['link_name'] ?>')" title="修改">修改</a> |  
    		<a href="###" onclick="deleteDialog('deleteLink', '<?php echo YUrl::createAdminUrl('Index', 'Link', 'delete', ['link_id' => $item['link_id']]); ?>', '<?php echo $item['link_name'] ?>')" title="删除">删除</a>  
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
	var page_url = "<?php echo YUrl::createAdminUrl('Index', 'Link', 'edit'); ?>?link_id="+id;
	postDialog('editLink', page_url, title, 400, 300);
}
</script>
</body>
</html>