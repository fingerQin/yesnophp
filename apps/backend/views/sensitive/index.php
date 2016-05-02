<?php
use common\YUrl;
require_once(APP_VIEW_PATH . DIRECTORY_SEPARATOR . 'common/header.php');
?>

<div class="subnav">
    <div class="content-menu ib-a blue line-x">
    	<a class="add fb" href="javascript:postDialog('addSensitive', '<?php echo YUrl::createAdminUrl('Index', 'Sensitive', 'add'); ?>', '添加敏感词', 350, 120)"><em>添加敏感词</em></a>
    	<a href='javascript:;' class="on"><em>敏感词列表</em></a>    
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
     		<input type="text" value="<?php echo $keywords; ?>" class="input-text" name="keywords" placeholder="请输入敏感词" />
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
			<th align="center">敏感等级</th>
			<th align="center">敏感词</th>
			<th width="120" align="center">修改时间</th>
			<th width="120" align="center">创建时间</th>
			<th width="100" align="center">管理操作</th>
		</tr>
	</thead>
    <tbody>
    <?php foreach ($list as $item): ?>
    	<tr>
    		<td align="center"><?php echo $item['id']; ?></td>
    		<td align="center"><?php echo $item['lv']; ?></td>
    		<td align="center"><?php echo $item['val']; ?></td>
    		<td align="center"><?php echo $item['modified_time'] ? date('Y-m-d H:i:s', $item['modified_time']) : '-'; ?></td>
    		<td align="center"><?php echo date('Y-m-d H:i:s', $item['created_time']); ?></td>
    		<td align="center">
    		<a href="###" onclick="edit(<?php echo $item['id'] ?>, '<?php echo $item['val'] ?>')" title="修改">修改</a> |  
    		<a href="###" onclick="deleteDialog('deleteSensitive', '<?php echo YUrl::createAdminUrl('Index', 'Sensitive', 'delete', ['id' => $item['id']]); ?>', '<?php echo $item['val'] ?>')" title="删除">删除</a>  
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
	var page_url = "<?php echo YUrl::createAdminUrl('Index', 'Sensitive', 'edit'); ?>?id="+id;
	postDialog('editSensitive', page_url, title, 350, 120);
}
</script>
</body>
</html>