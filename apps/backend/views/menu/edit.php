<?php
use common\YUrl;
require_once(APP_VIEW_PATH . DIRECTORY_SEPARATOR . 'common/header.php');
?>

<style type="text/css">
	html{_overflow-y:scroll}
</style>

<div class="common-form">
<form name="myform" id="myform" action="<?php echo YUrl::createAdminUrl('Index', 'Menu', 'add'); ?>" method="post">
<table width="100%" class="table_form contentWrap">
      <tr>
        <th width="120">上级菜单：</th>
        <td>
        <select name="parentid">
        <option value="0">作为一级菜单</option>
        <?php foreach ($list as $menu): ?>
        <option <?php echo ($menu['menu_id']==$detail['parentid']) ? 'selected="selected"' : ''; ?> value="<?php echo $menu['menu_id']; ?>"><?php echo $menu['name']; ?></option>
			<?php if (isset($menu['sub'])): ?>
			<?php foreach ($menu['sub'] as $sub_m): ?>
			<option <?php echo ($sub_m['menu_id']==$detail['parentid']) ? 'selected="selected"' : ''; ?> value="<?php echo $sub_m['menu_id']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;├─<?php echo $sub_m['name']; ?></option>
			<?php if (isset($sub_m['sub'])): ?>
			<?php foreach ($sub_m['sub'] as $ss_m): ?>
			<option <?php echo ($ss_m['menu_id']==$detail['parentid']) ? 'selected="selected"' : ''; ?> value="<?php echo $ss_m['menu_id']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;│&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;├─<?php echo $ss_m['name']; ?></option>
			<?php endforeach; ?>
			<?php endif; ?>
			<?php endforeach; ?>
			<?php endif; ?>
		<?php endforeach; ?>
		</select>
		</td>
    </tr>
    <tr>
        <th> 菜单名称：</th>
        <td><input type="text" name="name" id="name" value="<?php echo htmlspecialchars($detail['name']); ?>" class="input-text" ></td>
    </tr>
	<tr>
        <th>模块名：</th>
        <td><input type="text" name="m" id="m" value="<?php echo htmlspecialchars($detail['m']); ?>" class="input-text" /></td>
    </tr>
	<tr>
        <th>文件名：</th>
        <td><input type="text" name="c" id="c" value="<?php echo htmlspecialchars($detail['c']); ?>" class="input-text" /></td>
    </tr>
	<tr>
        <th>方法名：</th>
        <td><input type="text" name="a" id="a" value="<?php echo htmlspecialchars($detail['a']); ?>" class="input-text" /> <span id="a_tip"></span></td>
    </tr>
	<tr>
        <th>附加参数：</th>
        <td><input type="text" name="data" value="<?php echo htmlspecialchars($detail['data']); ?>" class="input-text" /></td>
    </tr>
	<tr>
        <th>是否显示菜单：</th>
        <td>
            <input <?php echo $detail['display'] ? 'checked="checked"' : ''; ?> type="radio" name="display" value="1" checked> 是
            <input <?php echo !$detail['display'] ? 'checked="checked"' : ''; ?> type="radio" name="display" value="0"> 否
        </td>
    </tr>
    <tr>
	    <td width="100%" align="center" colspan="2">
	       <input type="hidden" name="menu_id" value="<?php echo $detail['menu_id']; ?>" />
	       <input id="form_submit" type="button" name="dosubmit" value=" 提交 " />
	    </td>
	</tr>
</table>

</form>

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
                	top.dialog.getCurrent().close({"refresh" : 1});
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