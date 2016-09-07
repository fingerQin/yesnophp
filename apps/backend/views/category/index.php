<?php
use common\YUrl;
require_once (APP_VIEW_PATH . DIRECTORY_SEPARATOR . 'common/header.php');
?>

<div class="subnav">
	<div class="content-menu ib-a blue line-x">
		<a class="add fb"
			href="javascript:postDialog('addCategory', '<?php echo YUrl::createBackendUrl('', 'Category', 'add'); ?>', '添加分类', 400, 250)"><em>添加分类</em></a>
		<a href='javascript:;' class="on"><em>分类列表</em></a>
	</div>
</div>

<style type="text/css">
html {
	_overflow-y: scroll
}
</style>
<div class="pad-lr-10">

	<form name="searchform" action="" method="get" id="searchform">
		<table width="100%" cellspacing="0" class="search-form">
			<tbody>
				<tr>
					<td>
						<div class="explain-col">
							<p style="margin-top: 10px;">
								<select id="cat_type" name="cat_type">
     		<?php foreach ($cat_type_list as $type_id => $type_name): ?>
            	<option
										<?php echo $type_id==$cat_type ? 'selected="selected"' : ''; ?>
										value="<?php echo $type_id; ?>"><?php echo $type_name; ?></option>
            <?php endforeach; ?>
        	</select>
							</p>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</form>

	<form name="myform"
		action="<?php echo YUrl::createBackendUrl('', 'Category', 'sort'); ?>"
		method="post" id="sort_form">
		<div class="table-list">
			<table width="100%" cellspacing="0">
				<thead>
					<tr>
						<th width="80">排序</th>
						<th width="100">分类id</th>
						<th align="left">分类名称</th>
						<th>是否外链</th>
						<th>是否显示</th>
						<th>分类类型</th>
						<th>code 编码</th>
						<th>管理操作</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($list as $cat): ?>
						<tr>
						<td align='center'><input
							name='listorders[<?php echo $cat['cat_id']; ?>]' type='text'
							size='3' value='<?php echo $cat['listorder']; ?>'
							class='input-text-c'></td>
						<td align='center'><?php echo $cat['cat_id']; ?></td>
						<td align='left'><?php echo $cat['cat_name']; ?></td>
						<td align='center'><?php echo $cat['is_out_url'] ? '是' : '否'; ?></td>
						<td align='center'><?php echo $cat['display'] ? '是' : '否'; ?></td>
						<td align='center'>
							<?php
        if ($cat['cat_type'] == 1) {
            echo '文章分类';
        } else if ($cat['cat_type'] == 2) {
            echo '友情链接分类';
        } else if ($cat['cat_type'] == 3) {
            echo '商品分类';
        }
        ?>
							</td>
						<td align='center'><?php echo $cat['cat_code']; ?></td>
						<td align='center'><a
							href="javascript:postDialog('addCategory', '<?php echo YUrl::createBackendUrl('', 'Category', 'add', ['parentid' => $cat['cat_id']]); ?>', '添加子分类', 450, 280);">添加子分类</a>
							| <a
							href="javascript:postDialog('editCategory', '<?php echo YUrl::createBackendUrl('', 'Category', 'edit', ['cat_id' => $cat['cat_id']]); ?>', '添加子分类', 450, 280);">修改</a>
							| <a
							href="javascript:deleteDialog('deleteCategory', '<?php echo YUrl::createBackendUrl('', 'Category', 'delete', ['cat_id' => $cat['cat_id']]); ?>', '<?php echo $cat['cat_name']; ?>');">删除</a>
						</td>
					</tr>
						<?php if (isset($cat['sub'])): ?>
						<?php foreach ($cat['sub'] as $sub_m): ?>
						<tr>
						<td align='center'><input
							name='listorders[<?php echo $sub_m['cat_id']; ?>]' type='text'
							size='3' value='<?php echo $sub_m['listorder']; ?>'
							class='input-text-c'></td>
						<td align='center'><?php echo $sub_m['cat_id']; ?></td>
						<td align='left'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;├─ <?php echo $sub_m['cat_name']; ?></td>
						<td align='center'><?php echo $sub_m['is_out_url'] ? '是' : '否'; ?></td>
						<td align='center'><?php echo $sub_m['display'] ? '是' : '否'; ?></td>
						<td align='center'>
							<?php
                if ($sub_m['cat_type'] == 1) {
                    echo '文章分类';
                } else if ($sub_m['cat_type'] == 2) {
                    echo '友情链接分类';
                } else if ($sub_m['cat_type'] == 3) {
                    echo '商品分类';
                }
                ?>
							</td>
						<td align='center'><?php echo $sub_m['cat_code']; ?></td>
						<td align='center'><a
							href="javascript:postDialog('addCategory', '<?php echo YUrl::createBackendUrl('', 'Category', 'add', ['parentid' => $sub_m['cat_id']]); ?>', '添加子分类', 450, 280);">添加子分类</a>
							| <a
							href="javascript:postDialog('editCategory', '<?php echo YUrl::createBackendUrl('', 'Category', 'edit', ['cat_id' => $sub_m['cat_id']]); ?>', '添加子分类', 450, 280);">修改</a>
							| <a
							href="javascript:deleteDialog('deleteCategory', '<?php echo YUrl::createBackendUrl('', 'Category', 'delete', ['cat_id' => $sub_m['cat_id']]); ?>', '<?php echo $sub_m['cat_name']; ?>');">删除</a>
						</td>
					</tr>
						<?php if (isset($sub_m['sub'])): ?>
						<?php foreach ($sub_m['sub'] as $ss_m): ?>
						<tr>
						<td align='center'><input
							name='listorders[<?php echo $ss_m['cat_id']; ?>]' type='text'
							size='3' value='<?php echo $ss_m['listorder']; ?>'
							class='input-text-c'></td>
						<td align='center'><?php echo $ss_m['cat_id']; ?></td>
						<td align='left'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;├─ <?php echo $ss_m['cat_name']; ?></td>
						<td align='center'><?php echo $ss_m['is_out_url'] ? '是' : '否'; ?></td>
						<td align='center'><?php echo $ss_m['display'] ? '是' : '否'; ?></td>
						<td align='center'>
							<?php
                        if ($ss_m['cat_type'] == 1) {
                            echo '文章分类';
                        } else if ($ss_m['cat_type'] == 2) {
                            echo '友情链接分类';
                        } else if ($ss_m['cat_type'] == 3) {
                            echo '商品分类';
                        }
                        ?>
							</td>
						<td align='center'><?php echo $ss_m['cat_code']; ?></td>
						<td align='center'><a
							href="javascript:postDialog('addCategory', '<?php echo YUrl::createBackendUrl('', 'Category', 'add', ['parentid' => $ss_m['cat_id']]); ?>', '添加子分类', 450, 280);">添加子分类</a>
							| <a
							href="javascript:postDialog('editCategory', '<?php echo YUrl::createBackendUrl('', 'Category', 'edit', ['cat_id' => $ss_m['cat_id']]); ?>', '添加子分类', 450, 280);">修改</a>
							| <a
							href="javascript:deleteDialog('deleteCategory', '<?php echo YUrl::createBackendUrl('', 'Category', 'delete', ['cat_id' => $ss_m['cat_id']]); ?>', '<?php echo $ss_m['cat_name']; ?>');">删除</a>
						</td>
					</tr>
						<?php endforeach; ?>
						<?php endif; ?>
						<?php endforeach; ?>
						<?php endif; ?>
					<?php endforeach; ?>
					</tbody>
			</table>
			<div class="btn">
				<input type="button" id="form_submit" class="button" name="dosubmit"
					value="排序" />
			</div>
		</div>

</div>
</div>
</form>
</body>
</html>

<script type="text/javascript">
<!--

$(document).ready(function(){
	$('#form_submit').click(function(){
	    $.ajax({
	    	type: 'post',
            url: $('#sort_form').attr('action'),
            dataType: 'json',
            data: $('#sort_form').serialize(),
            success: function(data) {
                if (data.errcode == 0) {
                	window.location.reload();
                } else {
                	dialogTips(data.errmsg, 3);
                }
            }
	    });
	});
	$('#cat_type').change(function(){
		$('#searchform').submit();
	});
});

//-->
</script>

</body>
</html>