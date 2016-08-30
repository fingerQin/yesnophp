<?php
use common\YUrl;
require_once(APP_VIEW_PATH . DIRECTORY_SEPARATOR . 'common/header.php');
?>

<div class="subnav">
    <div class="content-menu ib-a blue line-x">
    	<a class="add fb" href="javascript:postDialog('addConfig', '<?php echo YUrl::createBackendUrl('', 'Config', 'add'); ?>', '添加配置', 480, 240)"><em>添加配置</em></a>
    	<a href='javascript:;' class="on"><em>配置列表</em></a>    
    	<a style="float:right;" class="add fb" href="###" onclick="normalDialog('clearCache', '<?php echo YUrl::createBackendUrl('', 'Config', 'clearCache'); ?>', '您确定要清除配置缓存吗？')" title="清除配置缓存"><em>清除配置缓存</em></a>
    </div>
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
     		<input type="text" value="<?php echo $keywords; ?>" class="input-text" name="keywords" placeholder="配置名称或配置编码" />
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
			<th width="180" align="left">配置标题</th>
			<th align="left">配置编码</th>
			<th align="left">配置值</th>
			<th align="left">描述</th>
			<th width="120" align="left">修改时间</th>
			<th width="120" align="left">创建时间</th>
			<th width="100" align="center">管理操作</th>
		</tr>
	</thead>
    <tbody>
    <?php foreach ($list as $item): ?>
    	<tr>
    		<td align="left"><?php echo $item['config_id']; ?></td>
    		<td align="left"><?php echo $item['ctitle']; ?></td>
    		<td align="left"><?php echo $item['cname']; ?></td>
    		<td align="left"><?php echo $item['cvalue']; ?></td>
    		<td align="left"><?php echo $item['description']; ?></td>
    		<td align="left"><?php echo date('Y-m-d H:i:s', $item['modified_time']); ?></td>
    		<td align="left"><?php echo date('Y-m-d H:i:s', $item['created_time']); ?></td>
    		<td align="center">
    		<a href="###" onclick="edit(<?php echo $item['config_id'] ?>, '<?php echo $item['ctitle'] ?>')" title="修改">修改</a> |  
    		<a href="###" onclick="deleteDialog('deleteType', '<?php echo YUrl::createBackendUrl('', 'Config', 'delete', ['config_id' => $item['config_id']]); ?>', '<?php echo $item['ctitle'] ?>')" title="删除">删除</a>  
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
	var page_url = "<?php echo YUrl::createBackendUrl('', 'Config', 'edit'); ?>?config_id="+id;
	postDialog('editType', page_url, title, 480, 240);
}
</script>
</body>
</html>