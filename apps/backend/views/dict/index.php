<?php
use common\YUrl;
require_once(APP_VIEW_PATH . DIRECTORY_SEPARATOR . 'common/header.php');
?>
<div class="subnav">
    <div class="content-menu ib-a blue line-x">
    	<a class="add fb" href="javascript:postDialog('addType', '<?php echo YUrl::createAdminUrl('Index', 'Dict', 'addType'); ?>', '添加字典', 450, 200)"><em>添加字典</em></a>
    	<a href='javascript:;' class="on"><em>字典列表</em></a>    
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
     		<input type="text" value="<?php echo $keywords; ?>" class="input-text" name="keywords" placeholder="字典编码或字典名称" />
    		<input type="submit" name="search" class="button" value="搜索" />
    		</p>
		</div>
		</td>
		</tr>
    </tbody>
</table>
</form>


<form name="myform" id="myform" action="?m=goods&c=shop&a=listorder" method="post" >
<div class="table-list">
<table width="100%" cellspacing="0">
	<thead>
		<tr>
			<th width="10%" align="left">ID</th>
			<th width="20%" align="left">字典类型编码</th>
			<th width="20%" align="left">字典类型名称</th>
			<th width="30%" align="left">描述</th>
			<th width="20%" align="center">管理操作</th>
		</tr>
	</thead>
    <tbody>
    <?php foreach ($dict_list['list'] as $dict): ?>
    	<tr>
    		<td align="left"><?php echo $dict['dict_type_id'] ?></td>
    		<td align="left"><?php echo $dict['type_code'] ?></td>
    		<td align="left"><?php echo $dict['type_name'] ?></td>
    		<td align="left"><?php echo $dict['description'] ?></td>
    		<td align="center">
    		<a href="###" onclick="edit(<?php echo $dict['dict_type_id'] ?>, '<?php echo $dict['type_name'] ?>')" title="修改">修改</a> |  
    		<a href="###" onclick="deleteDialog('deleteType', '<?php echo YUrl::createAdminUrl('Index', 'Dict', 'deleteType', ['dict_type_id' => $dict['dict_type_id']]); ?>', '<?php echo $dict['type_name'] ?>')" title="删除">删除</a> |  
    		<a href="###" onclick="setDictValue(<?php echo $dict['dict_type_id'] ?>, '<?php echo $dict['type_name'] ?>')" title="字典值管理">字典值管理</a>
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
	var page_url = "<?php echo YUrl::createAdminUrl('Index', 'Dict', 'editType'); ?>?dict_type_id="+id;
	postDialog('editType', page_url, title, 500, 200);
}

function setDictValue(id, name) {
	page_url = '<?php echo YUrl::createAdminUrl('Index', 'Dict', 'dict'); ?>?dict_type_id='+id;
	title = '管理 『 '+name+' 』字典值';
	postDialog(id, page_url, title, 900, 530, '', 'yes');
}
</script>
</body>
</html>