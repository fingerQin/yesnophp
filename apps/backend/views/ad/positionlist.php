<?php
use common\YUrl;
require_once(APP_VIEW_PATH . DIRECTORY_SEPARATOR . 'common/header.php');
?>

<div class="subnav">
    <div class="content-menu ib-a blue line-x">
    	<a class="add fb" href="javascript:postDialog('addAdPostion', '<?php echo YUrl::createAdminUrl('Index', 'Ad', 'positionAdd'); ?>', '添加广告位置', 400, 150)"><em>添加广告位置</em></a>
    	<a href='javascript:;' class="on"><em>广告位置列表</em></a>    
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
     		<input type="text" value="<?php echo $keywords; ?>" class="input-text" name="keywords" placeholder="广告位置名称" />
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
			<th align="center">广告位置名称</th>
			<th align="center">广告编码</th>
			<th align="center">允许展示的广告数量</th>
			<th width="120" align="center">修改时间</th>
			<th width="120" align="center">创建时间</th>
			<th width="150" align="center">管理操作</th>
		</tr>
	</thead>
    <tbody>
    <?php foreach ($list as $item): ?>
    	<tr>
    		<td align="center"><?php echo $item['pos_id']; ?></td>
    		<td align="center"><?php echo $item['pos_name']; ?></td>
    		<td align="center"><?php echo $item['pos_code']; ?></td>
    		<td align="center"><?php echo $item['pos_ad_count']; ?></td>
    		<td align="center"><?php echo date('Y-m-d H:i:s', $item['modified_time']); ?></td>
    		<td align="center"><?php echo date('Y-m-d H:i:s', $item['created_time']); ?></td>
    		<td align="center">
    		<a href="###" onclick="edit(<?php echo $item['pos_id'] ?>, '<?php echo $item['pos_name'] ?>')" title="修改">修改</a> |  
    		<a href="###" onclick="deleteDialog('positionDelete', '<?php echo YUrl::createAdminUrl('Index', 'Ad', 'positionDelete', ['pos_id' => $item['pos_id']]); ?>', '<?php echo $item['pos_name'] ?>')" title="删除">删除</a> | 
    		<a href="###" onclick="setAdPosValue(<?php echo $item['pos_id'] ?>, '<?php echo $item['pos_name'] ?>')" title="广告管理">广告管理</a>  
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
	var page_url = "<?php echo YUrl::createAdminUrl('Index', 'Ad', 'positionEdit'); ?>?pos_id="+id;
	postDialog('positionEdit', page_url, title, 400, 140);
}

function setAdPosValue(id, name) {
	page_url = '<?php echo YUrl::createAdminUrl('Index', 'Ad', 'index'); ?>?pos_id='+id;
	title = '管理 『 '+name+' 』位置广告';
	postDialog(id, page_url, title, 1000, 500, '', 'yes');
}
</script>
</body>
</html>