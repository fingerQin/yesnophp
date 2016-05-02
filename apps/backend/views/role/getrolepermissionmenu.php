<?php
use common\YUrl;
require_once(APP_VIEW_PATH . DIRECTORY_SEPARATOR . 'common/header.php');
?>

<style type="text/css">
html {
	_overflow-y: scroll
}
</style>
	<form name="myform" action="<?php echo YUrl::createAdminUrl('Index', 'Role', 'setPermission'); ?>" method="post" id="setPermissionForm">
		<div class="pad-lr-10">
			<div class="table-list">
				<table width="100%" cellspacing="0">
					<thead>
						<tr>
							<th align="center" width="100">id</th>
							<th align="left">菜单名称</th>
						</tr>
					</thead>
					<tbody>
					<?php foreach ($list as $menu): ?>
						<tr>
							<td align='center'><?php echo $menu['menu_id']; ?></td>
							<td align='left'><label><input <?php echo in_array($menu['menu_id'], $priv_menu_list) ? 'checked="checked"' : ''; ?> id="<?php echo $menu['menu_id']; ?>" parentid="<?php echo $menu['parentid']; ?>" name='menuid[]' type='checkbox' size='3' value='<?php echo $menu['menu_id']; ?>'><?php echo $menu['name']; ?></label></td>
						</tr>
						<?php if (isset($menu['sub'])): ?>
						<?php foreach ($menu['sub'] as $sub_m): ?>
						<tr>
							<td align='center'><?php echo $sub_m['menu_id']; ?></td>
							<td align='left'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label><input <?php echo in_array($sub_m['menu_id'], $priv_menu_list) ? 'checked="checked"' : ''; ?> id="<?php echo $sub_m['menu_id']; ?>" parentid="<?php echo $sub_m['parentid']; ?>" name='menuid[]' type='checkbox' size='3' value='<?php echo $sub_m['menu_id']; ?>'>├─ <?php echo $sub_m['name']; ?></label></td>
						</tr>
						<?php if (isset($sub_m['sub'])): ?>
						<?php foreach ($sub_m['sub'] as $ss_m): ?>
						<tr>
							<td align='center'><?php echo $ss_m['menu_id']; ?></td>
							<td align='left'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label><input <?php echo in_array($ss_m['menu_id'], $priv_menu_list) ? 'checked="checked"' : ''; ?> id="<?php echo $ss_m['menu_id']; ?>" parentid="<?php echo $ss_m['parentid']; ?>" name='menuid[]' type='checkbox' size='3' value='<?php echo $ss_m['menu_id']; ?>'>├─ <?php echo $ss_m['name']; ?></label></td>
						</tr>
						<?php endforeach; ?>
						<?php endif; ?>
						<?php endforeach; ?>
						<?php endif; ?>
					<?php endforeach; ?>
					</tbody>
				</table>
				<div class="btn">
					<input type="hidden" name="roleid" value="<?php echo $roleid; ?>" />
					<input type="button" id="form_submit" class="button" name="dosubmit" value="保存" />
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
            url: $('#setPermissionForm').attr('action'),
            dataType: 'json',
            data: $('#setPermissionForm').serialize(),
            success: function(data) {
                if (data.errcode == 0) {
                	dialogTips(data.errmsg, 3);
                } else {
                	dialogTips(data.errmsg, 3);
                }
            }
	    });
	});

	$(":checkbox").click(function(){
		if (this.checked) {
			var menu_id = this.value; // 获取被点击的菜单ID。
			var obj = $('#' + menu_id); // 获取菜单checkbox对象。
			var node_parentid = obj.attr('parentid'); // 获取菜单的父ID。
			$('#' + node_parentid).prop('checked', true); // 将菜单父ID选中。
			$('input[parentid="' + menu_id + '"]').prop('checked', true); // 将当前被选中的菜单的子菜单选中。
		    $('input[parentid="' + menu_id + '"]').each(function(index, data){
		    	$('input[parentid="' + data.value + '"]').prop('checked', true); // 将当前被选中的菜单的子菜单的子菜单选中。
			});
		} else {
			var menu_id = this.value; // 获取被点击的菜单ID。
			var obj = $('#' + menu_id); // 获取菜单checkbox对象。
			var node_parentid = obj.attr('parentid'); // 获取菜单的父ID。
			var checked_count = $('input[parentid="' + node_parentid + '"]:checked').length; // 获取当前被取消的菜单的同一个父ID的菜单还留存多少个选中的。
			if (checked_count == 0) {
				$('#' + node_parentid).prop('checked', false); // 如果同父ID的菜单已经没有了。则将父菜单取消。
			}
			$('input[parentid="' + menu_id + '"]').prop('checked', false); // 将当前被取消的菜单的子菜单取消。
			$('input[parentid="' + menu_id + '"]').each(function(index, data){
		    	$('input[parentid="' + data.value + '"]').prop('checked', false); // 将当前被取消的菜单的子菜单的子菜单取消。
			});
		}
	});
});

//-->
</script>

</body>
</html>