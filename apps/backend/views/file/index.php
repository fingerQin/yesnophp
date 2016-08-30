<?php
use common\YUrl;
require_once(APP_VIEW_PATH . DIRECTORY_SEPARATOR . 'common/header.php');
?>

<div class="subnav">
    <div class="content-menu ib-a blue line-x">
    	<a class="add fb" href="javascript:postDialog('addConfig', '<?php echo YUrl::createBackendUrl('', 'File', 'add'); ?>', '添加文件', 450, 240)"><em>添加文件</em></a>
    	<a href='javascript:;' class="on"><em>文件列表</em></a>    
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
     		上传时间：<input type="text" name="start_time" id="start_time" value="<?php echo $start_time; ?>" size="20" class="date input-text" readonly="" /> ～ <input type="text" name="end_time" id="end_time" value="<?php echo $end_time; ?>" size="20" class="date input-text" readonly="" />
    		<script type="text/javascript">
Calendar.setup({
	weekNumbers: false,
    inputField : "start_time",
    trigger    : "start_time",
    dateFormat: "%Y-%m-%d %H:%I:%S",
    showTime: true,
    minuteStep: 1,
    onSelect   : function() {this.hide();}
});

Calendar.setup({
	weekNumbers: false,
    inputField : "end_time",
    trigger    : "end_time",
    dateFormat: "%Y-%m-%d %H:%I:%S",
    showTime: true,
    minuteStep: 1,
    onSelect   : function() {this.hide();}
});
</script>
    		MD5：<input type="text" value="<?php echo $file_md5; ?>" class="input-text" name="file_md5" placeholder="请输入文件MD5值" />
    		<select name="user_type">
     		<option value="-1">全部</option>
     		<option <?php echo $user_type==1 ? 'selected="selected"' : ''; ?> value="1">管理员</option>
     		<option <?php echo $user_type==2 ? 'selected="selected"' : ''; ?> value="2">普通用户</option>
     		</select>
     		<input type="text" value="<?php echo $user_name; ?>" class="input-text" name="user_name" placeholder="请输入要查询的用户账号" />
     		<select name="file_type">
     		<option value="-1">全部</option>
     		<option <?php echo $file_type==1 ? 'selected="selected"' : ''; ?> value="1">图片</option>
     		<option <?php echo $file_type==2 ? 'selected="selected"' : ''; ?> value="2">其他</option>
     		</select>
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
			<th align="center">文件ID</th>
			<th align="center">图片</th>
			<th align="center">类型</th>
			<th align="center">大小</th>
			<th align="left">MD5值</th>
			<th align="left">用户类型</th>
			<th align="left">用户名称</th>
			<th width="120" align="left">上传时间</th>
			<th width="100" align="center">管理操作</th>
		</tr>
	</thead>
    <tbody>
    <?php foreach ($list as $item): ?>
    	<tr>
    		<td align="left"><?php echo $item['file_id']; ?></td>
    		<td align="left"><a target="_blank" href="<?php echo YUrl::filePath($item['file_name']); ?>"><img width="160" src="<?php echo YUrl::filePath($item['file_name']); ?>" /></a></td>
    		<td align="left"><?php echo $item['file_type_label']; ?></td>
    		<td align="left"><?php echo $item['file_size']; ?></td>
    		<td align="left"><?php echo $item['file_md5']; ?></td>
    		<td align="left"><?php echo $item['user_type_label']; ?></td>
    		<td align="left"><?php echo $item['user_name']; ?></td>
    		<td align="left"><?php echo date('Y-m-d H:i:s', $item['created_time']); ?></td>
    		<td align="center">
    		<a href="###" onclick="deleteDialog('deleteFile', '<?php echo YUrl::createBackendUrl('', 'File', 'delete', ['file_id' => $item['file_id']]); ?>', '图片')" title="删除">删除</a>  
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

</script>
</body>
</html>