<?php
use common\YUrl;
require_once(APP_VIEW_PATH . DIRECTORY_SEPARATOR . 'common/header.php');
?>

<div class="subnav">
    <div class="content-menu ib-a blue line-x">
    	<a class="add fb" href="javascript:postDialog('addRole', '<?php echo YUrl::createAdminUrl('Index', 'Role', 'add'); ?>', '添加角色', 450, 200)"><em>添加角色</em></a>
    	<a href='javascript:;' class="on"><em>角色列表</em></a>    
    </div>
</div>
<style type="text/css">
	html{_overflow-y:scroll}
</style><div class="pad-lr-10">


<form name="myform" id="myform" action="" method="post" >
<div class="table-list">
<table width="100%" cellspacing="0">
	<thead>
		<tr>
			<th align="center">角色ID</th>
			<th align="center">角色名称</th>
			<th align="center">角色说明</th>
			<th align="center">创建时间</th>
			<th align="center">管理操作</th>
		</tr>
	</thead>
    <tbody>
    <?php foreach ($list as $item): ?>
    	<tr>
    		<td align="center"><?php echo $item['roleid']; ?></td>
    		<td align="center"><?php echo $item['rolename']; ?></td>
    		<td align="center"><?php echo $item['description']; ?></td>
    		<td align="center"><?php echo date('Y-m-d H:i:s', $item['created_time']); ?></td>
    		<td align="center">
    		<a href="###" onclick="edit(<?php echo $item['roleid'] ?>, '<?php echo $item['rolename'] ?>')" title="修改">修改</a> |  
    		<a href="###" onclick="deleteDialog('deleteRole', '<?php echo YUrl::createAdminUrl('Index', 'Role', 'delete', ['roleid' => $item['roleid']]); ?>', '<?php echo $item['rolename'] ?>')" title="删除">删除</a> |   
    		<a href="###" onclick="setRoleValue(<?php echo $item['roleid'] ?>, '<?php echo $item['rolename'] ?>')" title="角色权限管理">角色权限管理</a>
    		</td>
    	</tr>
    <?php endforeach; ?>
    </tbody>
</table>


</div>

</form>
</div>
<script type="text/javascript">
function edit(id, name) {
	var title = '修改『' + name + '』';
	var page_url = "<?php echo YUrl::createAdminUrl('Index', 'Role', 'edit'); ?>?roleid="+id;
	postDialog('editRole_' + id, page_url, title, 460, 240);
}

function setRoleValue(id, name) {
	page_url = '<?php echo YUrl::createAdminUrl('Index', 'Role', 'getRolePermissionMenu'); ?>?roleid='+id;
	title = '管理 『 '+name+' 』权限';
	postDialog(id, page_url, title, 450, 400, '', 'yes');
}
</script>
</body>
</html>