<?php
use common\YUrl;
require_once(APP_VIEW_PATH . DIRECTORY_SEPARATOR . 'common/header.php');
?>

<div class="subnav">
    <div class="content-menu ib-a blue line-x">
    	<a class="add fb" href="javascript:postDialog('addIpBan', '<?php echo YUrl::createBackendUrl('', 'Ip', 'add'); ?>', '添加IP黑名单', 400, 140)"><em>添加IP黑名单</em></a>
    	<a href='javascript:;' class="on"><em>IP黑名单列表</em></a>    
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
     		<input type="text" value="<?php echo $keywords; ?>" class="input-text" name="keywords" placeholder="IP地址" />
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
			<th align="center">IP地址</th>
			<th align="center">备注</th>
			<th width="120" align="center">修改时间</th>
			<th width="120" align="center">创建时间</th>
			<th width="100" align="center">管理操作</th>
		</tr>
	</thead>
    <tbody>
    <?php foreach ($list as $item): ?>
    	<tr>
    		<td align="center"><?php echo $item['id']; ?></td>
    		<td align="center"><?php echo $item['ip']; ?></td>
    		<td align="center"><?php echo $item['remark']; ?></td>
    		<td align="center"><?php echo date('Y-m-d H:i:s', $item['modified_time']); ?></td>
    		<td align="center"><?php echo date('Y-m-d H:i:s', $item['created_time']); ?></td>
    		<td align="center">
    		<a href="###" onclick="edit(<?php echo $item['id'] ?>, '<?php echo $item['ip'] ?>')" title="修改">修改</a> |  
    		<a href="###" onclick="deleteDialog('deleteIp', '<?php echo YUrl::createBackendUrl('', 'Ip', 'delete', ['id' => $item['id']]); ?>', '<?php echo $item['ip'] ?>')" title="删除">删除</a>  
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
	var page_url = "<?php echo YUrl::createBackendUrl('', 'Ip', 'edit'); ?>?id="+id;
	postDialog('editIp', page_url, title, 400, 140);
}
</script>
</body>
</html>