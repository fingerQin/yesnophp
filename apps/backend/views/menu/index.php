<?php
use common\YUrl;
require_once(APP_VIEW_PATH . DIRECTORY_SEPARATOR . 'common/header.php');
?>

<div class="subnav">
    <div class="content-menu ib-a blue line-x">
    	<a class="add fb" href="javascript:postDialog('addMenu', '<?php echo YUrl::createAdminUrl('Index', 'Menu', 'add'); ?>', '添加菜单', 400, 300)"><em>添加菜单</em></a>
    	<a href='javascript:;' class="on"><em>菜单列表</em></a>    
    </div>
</div>

<style type="text/css">
html {
	_overflow-y: scroll
}
</style>
	<form name="myform" action="<?php echo YUrl::createAdminUrl('Index', 'Menu', 'sort'); ?>" method="post">
		<div class="pad-lr-10">
			<div class="table-list">
				<table width="100%" cellspacing="0">
					<thead>
						<tr>
							<th width="80">排序</th>
							<th width="100">id</th>
							<th>菜单英文名称</th>
							<th>管理操作</th>
						</tr>
					</thead>
					<tbody>
					<?php foreach ($list as $menu): ?>
						<tr>
							<td align='center'><input name='listorders[<?php echo $menu['menu_id']; ?>]' type='text' size='3' value='<?php echo $menu['listorder']; ?>' class='input-text-c'></td>
							<td align='center'><?php echo $menu['menu_id']; ?></td>
							<td><?php echo $menu['name']; ?></td>
							<td align='center'>
								<a href="javascript:postDialog('addMenu', '<?php echo YUrl::createAdminUrl('Index', 'Menu', 'add', ['parentid' => $menu['menu_id']]); ?>', '添加子菜单', 450, 280);">添加子菜单</a> |
								<a href="javascript:postDialog('editMenu', '<?php echo YUrl::createAdminUrl('Index', 'Menu', 'edit', ['menu_id' => $menu['menu_id']]); ?>', '添加子菜单', 450, 280);">修改</a> | 
								<a href="javascript:deleteDialog('deleteMenu', '<?php echo YUrl::createAdminUrl('Index', 'Menu', 'delete', ['menu_id' => $menu['menu_id']]); ?>', '<?php echo $menu['name']; ?>');">删除</a>
							</td>
						</tr>
						<?php if (isset($menu['sub'])): ?>
						<?php foreach ($menu['sub'] as $sub_m): ?>
						<tr>
							<td align='center'><input name='listorders[<?php echo $sub_m['menu_id']; ?>]' type='text' size='3' value='<?php echo $sub_m['listorder']; ?>' class='input-text-c'></td>
							<td align='center'><?php echo $sub_m['menu_id']; ?></td>
							<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;├─ <?php echo $sub_m['name']; ?></td>
							<td align='center'>
								<a href="javascript:postDialog('addMenu', '<?php echo YUrl::createAdminUrl('Index', 'Menu', 'add', ['parentid' => $sub_m['menu_id']]); ?>', '添加子菜单', 450, 280);">添加子菜单</a> |
								<a href="javascript:postDialog('editMenu', '<?php echo YUrl::createAdminUrl('Index', 'Menu', 'edit', ['menu_id' => $sub_m['menu_id']]); ?>', '编辑子菜单', 450, 280);">修改</a> | 
								<a href="javascript:deleteDialog('deleteMenu', '<?php echo YUrl::createAdminUrl('Index', 'Menu', 'delete', ['menu_id' => $sub_m['menu_id']]); ?>', '<?php echo $sub_m['name']; ?>');">删除</a>
							</td>
						</tr>
						<?php if (isset($sub_m['sub'])): ?>
						<?php foreach ($sub_m['sub'] as $ss_m): ?>
						<tr>
							<td align='center'><input name='listorders[<?php echo $ss_m['menu_id']; ?>]' type='text' size='3' value='<?php echo $ss_m['listorder']; ?>' class='input-text-c'></td>
							<td align='center'><?php echo $ss_m['menu_id']; ?></td>
							<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;├─ <?php echo $ss_m['name']; ?></td>
							<td align='center'>
								<a href="javascript:postDialog('addMenu', '<?php echo YUrl::createAdminUrl('Index', 'Menu', 'add', ['parentid' => $ss_m['menu_id']]); ?>', '添加子菜单', 450, 280);">添加子菜单</a> |
								<a href="javascript:postDialog('editMenu', '<?php echo YUrl::createAdminUrl('Index', 'Menu', 'edit', ['menu_id' => $ss_m['menu_id']]); ?>', '编辑子菜单', 450, 280);">修改</a> | 
								<a href="javascript:deleteDialog('deleteMenu', '<?php echo YUrl::createAdminUrl('Index', 'Menu', 'delete', ['menu_id' => $ss_m['menu_id']]); ?>', '<?php echo $ss_m['name']; ?>');">删除</a>
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
					<input type="button" id="form_submit" class="button" name="dosubmit" value="排序" />
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
            url: $('form').eq(0).attr('action'),
            dataType: 'json',
            data: $('form').eq(0).serialize(),
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

//-->
</script>

</body>
</html>