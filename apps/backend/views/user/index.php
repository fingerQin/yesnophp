<?php
use common\YUrl;
require_once(APP_VIEW_PATH . DIRECTORY_SEPARATOR . 'common/header.php');
?>

<div class="subnav">
    <div class="content-menu ib-a blue line-x">
    	<a class="add fb" href="javascript:postDialog('addUser', '<?php echo YUrl::createBackendUrl('', 'User', 'add'); ?>', '添加用户', 500, 400)"><em>添加用户</em></a>
    	<a href='javascript:;' class="on"><em>用户列表</em></a>    
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
     		账号：<input type="text" value="<?php echo $username; ?>" class="input-text" name="username" placeholder="请输入要查询的账号" />
     		手机号码：<input type="text" value="<?php echo $mobilephone; ?>" class="input-text" name="mobilephone" placeholder="请输入要查询的手机号码" />
     		手机验证:<select name="is_verify">
     		<option <?php echo $is_verify==-1 ? 'selected="selected"' : ''; ?> value="-1">全部</option>
     		<option <?php echo $is_verify==1 ? 'selected="selected"' : ''; ?> value="1">是</option>
     		<option <?php echo $is_verify==0 ? 'selected="selected"' : ''; ?> value="0">否</option>
     		</select>
     		注册时间：<input type="text" name="starttime" id="starttime" value="<?php echo $starttime; ?>" size="20" class="date input-text" /> ～ <input type="text" name="endtime" id="endtime" value="<?php echo $endtime; ?>" size="20" class="date input-text" />
    		<script type="text/javascript">
Calendar.setup({
	weekNumbers: false,
    inputField : "starttime",
    trigger    : "starttime",
    dateFormat: "%Y-%m-%d %H:%I:%S",
    showTime: true,
    minuteStep: 1,
    onSelect   : function() {this.hide();}
});

Calendar.setup({
	weekNumbers: false,
    inputField : "endtime",
    trigger    : "endtime",
    dateFormat: "%Y-%m-%d %H:%I:%S",
    showTime: true,
    minuteStep: 1,
    onSelect   : function() {this.hide();}
});
</script>
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
			<th width="5%" align="center">用户ID</th>
			<th width="10%" align="center">账号名</th>
			<th width="10%" align="center">账号状态</th>
			<th width="10%" align="left">手机号码</th>
			<th width="5%" align="left">手机验证</th>
			<th width="10%" align="left">邮箱</th>
			<th width="5%" align="center">邮箱验证</th>
			<th width="10%" align="left">注册时间</th>
			<th width="15%" align="center">管理操作</th>
		</tr>
	</thead>
    <tbody>
    <?php foreach ($list as $item): ?>
    	<tr>
    		<td align="center"><?php echo $item['user_id']; ?></td>
    		<td align="center"><?php echo $item['username']; ?></td>
    		<td align="center"><?php echo $item['forbin_label']; ?></td>
    		<td align="left"><?php echo $item['mobilephone']; ?></td>
    		<td align="left"><?php echo $item['mobilephone_ok'] ? '是' : '否'; ?></td>
    		<td align="left"><?php echo $item['email']; ?></td>
    		<td align="center"><?php echo $item['email_ok'] ? '是' : '否'; ?></td>
    		<td align="left"><?php echo date('Y-m-d H:i:s', $item['reg_time']); ?></td>
    		<td align="center">
    		  [<a href="###" onclick="edit(<?php echo $item['user_id'] ?>, '<?php echo $item['username'] ?>')" title="修改">修改</a>]&nbsp;
    		  <?php if ($item['forbin_status']): ?>
    		  [<a href="###" onclick="normalDialog('deleteType', '<?php echo YUrl::createBackendUrl('', 'User', 'unforbid', ['user_id' => $item['user_id']]); ?>', '<?php echo "您确定要解禁[{$item['username']}]用户吗?" ?>')" title="解禁">解禁</a>]
    		  <?php else: ?>
    		  [<a href="###" onclick="forbid(<?php echo $item['user_id'] ?>, '<?php echo $item['username'] ?>')" title="封禁">封禁</a>]
    		  <?php endif; ?>
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
	var page_url = "<?php echo YUrl::createBackendUrl('', 'User', 'edit'); ?>?user_id="+id;
	postDialog('editUser', page_url, title, 500, 400);
}

function forbid(id, name) {
	var title = '封禁『' + name + '』';
	var page_url = "<?php echo YUrl::createBackendUrl('', 'User', 'forbid'); ?>?user_id="+id;
	postDialog('forbidUser', page_url, title, 400, 300);
}
</script>
</body>
</html>