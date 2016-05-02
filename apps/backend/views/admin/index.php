<?php
use common\YUrl;
require_once(APP_VIEW_PATH . DIRECTORY_SEPARATOR . 'common/header.php');
?>

<div class="subnav">
    <div class="content-menu ib-a blue line-x">
    	<a class="add fb" href="javascript:postDialog('addAdmin', '<?php echo YUrl::createAdminUrl('Index', 'Admin', 'add'); ?>', '添加管理员', 450, 240)"><em>添加管理员</em></a>
    	<a href='javascript:;' class="on"><em>管理员列表</em></a>    
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
     		<input type="text" value="<?php echo $keywords; ?>" class="input-text" name="keywords" placeholder="管理员名称或账号" />
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
			<th align="center">管理员ID</th>
			<th align="center">真实姓名</th>
			<th align="center">账号</th>
			<th align="center">手机号码</th>
			<th align="center">角色名称</th>
			<th width="120" align="left">最后登录时间</th>
			<th width="120" align="left">创建时间</th>
			<th width="100" align="center">管理操作</th>
		</tr>
	</thead>
    <tbody>
    <?php foreach ($list as $item): ?>
    	<tr>
    		<td align="center"><?php echo $item['admin_id']; ?></td>
    		<td align="center"><?php echo $item['realname']; ?></td>
    		<td align="center"><?php echo $item['username']; ?></td>
    		<td align="center"><?php echo $item['mobilephone']; ?></td>
    		<td align="center"><?php echo $item['rolename']; ?></td>
    		<td align="center"><?php echo date('Y-m-d H:i:s', $item['lastlogintime']); ?></td>
    		<td align="center"><?php echo date('Y-m-d H:i:s', $item['created_time']); ?></td>
    		<td align="center">
    		<a href="###" onclick="edit(<?php echo $item['admin_id'] ?>, '<?php echo $item['realname'] ?>')" title="修改">修改</a> |  
    		<a href="###" onclick="deleteDialog('delteAdmin', '<?php echo YUrl::createAdminUrl('Index', 'Admin', 'delete', ['admin_id' => $item['admin_id']]); ?>', '<?php echo $item['realname'] ?>')" title="删除">删除</a>  
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
	var page_url = "<?php echo YUrl::createAdminUrl('Index', 'Admin', 'edit'); ?>?admin_id="+id;
	postDialog('editAdmin', page_url, title, 460, 240);
}
</script>
</body>
</html>