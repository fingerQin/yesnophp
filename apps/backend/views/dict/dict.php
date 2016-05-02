<?php
use common\YUrl;
require_once(APP_VIEW_PATH . DIRECTORY_SEPARATOR . 'common/header.php');
?>

<div class="subnav">
    <div class="content-menu ib-a blue line-x">
    	<a class="add fb" href="javascript:postDialog('add', '<?php echo YUrl::createAdminUrl('Index', 'Dict', 'add', ['dict_type_id' => $dict_type_id]); ?>', '添加', 500, 250)"><em>添加</em></a>　    
    </div>
</div>
<style type="text/css">
	html{_overflow-y:scroll}
</style><div class="pad-lr-10">

<form name="searchform" action="" method="get" >
<input type="hidden" value="<?php echo $dict_type_id; ?>" name="dict_type_id" />
<table width="100%" cellspacing="0" class="search-form">
    <tbody>
		<tr>
		<td>
		<div class="explain-col">
 		<p style="margin-top:10px;">
 		<input type="text" value="<?php echo $keywords; ?>" class="input-text" name="keywords" placeholder="字典值编码或字典值名称" />
		<input type="submit" name="search" class="button" value="搜索" />
		</p>
	</div>
		</td>
		</tr>
    </tbody>
</table>
</form>


<form name="myform" id="myform" action="<?php echo YUrl::createAdminUrl('Index', 'Dict', 'dict', ['dict_type_id' => $dict_type_id]); ?>" method="post" >
<input type="hidden" value="<?php echo $dict_type_id; ?>" name="dict_type_id" />
<div class="table-list">
<table width="100%" cellspacing="0">
	<thead>
		<tr>
			<th width="5%" align="center">
				<input type="checkbox" value="" id="check_box" onclick="selectall('dict_id[]');" />
			</th>
			<th width="8%" align="center">排序</th>
			<th width="20%" align="left">字典编码</th>
			<th width="20%" align="left">字典值</th>
			<th width="30%" align="left">字典值描述</th>
			<th width="25%" align="center">管理操作</th>
		</tr>
	</thead>
<tbody>
<?php foreach ($list['list'] as $dict): ?>
	<tr>
		<td align="center" width="35">
			<input type="checkbox" name="dict_id[]" value="<?php echo $dict['dict_id']; ?>" />
		</td>
		<td align="center" width="35">
			<input name="listorders[<?php echo $dict['dict_id']; ?>]" autocomplete="off" type="text" size="3" value="<?php echo $dict['listorder']; ?>" class="input-text-c input-text" />
		</td>
		<td align="left"><?php echo $dict['dict_code'] ?></td>
		<td align="left"><?php echo $dict['dict_name'] ?></td>
		<td align="left"><?php echo $dict['description'] ?></td>
		<td align="center">
		  <a href="###" onclick="edit(<?php echo $dict['dict_id'] ?>, '<?php echo $dict['dict_name'] ?>')" title="修改">修改</a> |  
		  <a href="javascript:deleteDialog('deleteDictValue', '<?php echo YUrl::createAdminUrl('Index', 'Dict', 'delete', ['dict_id' => $dict['dict_id']]); ?>', '『 <?php echo $dict['dict_name'] ?> 』');">删除</a>
		</td>
	</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
<div class="btn"> 
	<input type="submit" class="button" name="dosubmit" onclick="document.myform.action='<?php echo YUrl::createAdminUrl('Index', 'Dict', 'delete'); ?>'" value="删除" />&nbsp;&nbsp;
	<input type="submit" class="button" name="dosubmit" onclick="document.myform.action='<?php echo YUrl::createAdminUrl('Index', 'Dict', 'sortDict', ['dict_type_id' => $dict_type_id]); ?>'" value="排序" />
</div>
<div id="pages">
<?php echo $page_html; ?>
</div>
</form>
</div>

<script type="text/javascript">
function edit(id, name) {
	var page_url = '<?php echo YUrl::createAdminUrl('Index', 'Dict', 'edit'); ?>?dict_id='+id+'&dict_type_id=<?php echo $dict_type_id; ?>';
	var title = "修改【" + name + "】";
    postDialog(id, page_url, title, 500, 250);
}
</script>

</body>
</html>