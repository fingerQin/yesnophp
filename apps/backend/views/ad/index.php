<?php
use common\YUrl;
require_once (APP_VIEW_PATH . DIRECTORY_SEPARATOR . 'common/header.php');
?>

<div class="subnav">
	<div class="content-menu ib-a blue line-x">
		<a class="add fb"
			href="javascript:postDialog('addAd', '<?php echo YUrl::createBackendUrl('', 'Ad', 'add', ['pos_id' => $pos_id]); ?>', '添加广告', 420, 400)"><em>添加广告</em></a>
		<a href='javascript:;' class="on"><em>广告列表</em></a>
	</div>
</div>
<style type="text/css">
html {
	_overflow-y: scroll
}
</style>
<div class="pad-lr-10">


	<form name="searchform" action="" method="get">
		<table width="100%" cellspacing="0" class="search-form">
			<tbody>
				<tr>
					<td>
						<div class="explain-col">
							<p style="margin-top: 10px;">
								<input type="text" value="<?php echo $ad_name; ?>"
									class="input-text" name="ad_name" placeholder="广告名称" /> <input
									type="submit" name="search" class="button" value="搜索" />
							</p>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</form>


	<form name="myform" id="myform"
		action="<?php echo YUrl::createBackendUrl('', 'Ad', 'sortAd'); ?>"
		method="post">
		<div class="table-list">
			<table width="100%" cellspacing="0">
				<thead>
					<tr>
						<th width="50">排序</th>
						<th align="center">ID</th>
						<th width="15%" align="center">广告名称</th>
						<th align="center">广告图片</th>
						<th align="center">生效时间</th>
						<th align="center">失效时间</th>
						<th align="center">显示状态</th>
						<th width="10%" align="center">广告备注</th>
						<th width="90" align="center">创建时间</th>
						<th width="90" align="center">管理操作</th>
					</tr>
				</thead>
				<tbody>
    <?php foreach ($list as $item): ?>
    	<tr>
						<td align='center'><input
							name='listorders[<?php echo $item['ad_id']; ?>]' type='text'
							size='3' value='<?php echo $item['listorder']; ?>'
							class='input-text-c'></td>
						<td align="center"><?php echo $item['ad_id']; ?></td>
						<td align="center"><?php echo $item['ad_name']; ?></td>
						<td align="center"><a target="_blank"
							href="<?php echo $item['ad_url']; ?>"><img width="60"
								src="<?php echo YUrl::filePath($item['ad_image_url']); ?>" /></a></td>
						<td align="center"><?php echo date('Y-m-d H:i:s', $item['start_time']); ?></td>
						<td align="center"><?php echo date('Y-m-d H:i:s', $item['end_time']); ?></td>
						<td align="center"><?php echo $item['display'] ? '显示' : '隐藏'; ?></td>
						<td align="center"><a
							id="view_remark_<?php echo $item['ad_id']; ?>"
							title="<?php echo $item['ad_name']; ?>" href="###"
							onClick="viewRemark('view_remark_<?php echo $item['ad_id']; ?>')">查看</a></td>
						<td align="center"><?php echo date('Y-m-d H:i:s', $item['created_time']); ?></td>
						<td align="center"><a href="###"
							onclick="edit(<?php echo $item['ad_id'] ?>, '<?php echo $item['ad_name'] ?>')"
							title="修改">修改</a> | <a href="###"
							onclick="deleteDialog('adDelete', '<?php echo YUrl::createBackendUrl('', 'Ad', 'delete', ['ad_id' => $item['ad_id']]); ?>', '<?php echo $item['ad_name'] ?>')"
							title="删除">删除</a></td>
					</tr>
    <?php endforeach; ?>
    </tbody>
			</table>

			<div class="btn">
				<input type="button" id="form_submit" class="button" name="dosubmit"
					value="排序" />
			</div>

			<div id="pages">
<?php echo $page_html; ?>
</div>

		</div>

	</form>
</div>
<script type="text/javascript">

$(document).ready(function(){
	$('#form_submit').click(function(){
	    $.ajax({
	    	type: 'post',
            url: $('form').eq(1).attr('action'),
            dataType: 'json',
            data: $('form').eq(1).serialize(),
            success: function(data) {
                if (data.errcode == 0) {
                	window.location.reload();
                } else {
                	dialogTips(data.errmsg, 3);
                }
            }
	    });
	});
});

function edit(id, name) {
	var title = '修改『' + name + '』';
	var page_url = "<?php echo YUrl::createBackendUrl('', 'Ad', 'edit'); ?>?ad_id="+id;
	postDialog('adEdit', page_url, title, 420, 400);
}

function viewRemark(id) {
	var d = dialog({
		id : 'view_remark' + '_' + Math.random(),
	    title: '查看备注',
	    content: $('#'+id).attr('title')
	});
	d.show();
}

</script>
</body>
</html>