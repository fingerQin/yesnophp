<?php
use common\YUrl;
require_once(APP_VIEW_PATH . DIRECTORY_SEPARATOR . 'common/header.php');
?>

<style type="text/css">
	html{_overflow-y:scroll}
</style>

<div class="common-form">
<form name="myform" id="myform" action="<?php echo YUrl::createBackendUrl('', 'Category', 'add'); ?>" method="post">
<table width="100%" class="table_form contentWrap">
      <tr>
        <th width="120">上级分类：</th>
        <td>
        <select name="parentid" <?php echo $parent_cat_info ? 'disabled="disabled"' : ''; ?>>
        <option value="0">作为一级分类</option>
        <?php foreach ($list as $menu): ?>
        <option <?php echo ($menu['cat_id']==$parentid) ? 'selected="selected"' : ''; ?> value="<?php echo $menu['cat_id']; ?>"><?php echo $menu['cat_name']; ?></option>
			<?php if (isset($menu['sub'])): ?>
			<?php foreach ($menu['sub'] as $sub_m): ?>
			<option <?php echo ($sub_m['cat_id']==$parentid) ? 'selected="selected"' : ''; ?> value="<?php echo $sub_m['cat_id']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;├─<?php echo $sub_m['cat_name']; ?></option>
			<?php if (isset($sub_m['sub'])): ?>
			<?php foreach ($sub_m['sub'] as $ss_m): ?>
			<option <?php echo ($ss_m['cat_id']==$parentid) ? 'selected="selected"' : ''; ?> value="<?php echo $ss_m['cat_id']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;│&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;├─<?php echo $ss_m['cat_name']; ?></option>
			<?php endforeach; ?>
			<?php endif; ?>
			<?php endforeach; ?>
			<?php endif; ?>
		<?php endforeach; ?>
		<?php if ($parentid > 0): ?>
		<input name="parentid" value="<?php echo $parentid; ?>" type="hidden" />
		<?php endif; ?>
		</select>
		</td>
    </tr>
    <tr>
        <th> 分类名称：</th>
        <td><input type="text" name="cat_name" id="cat_name" class="input-text" ></td>
    </tr>
	<tr>
        <th>分类类型：</th>
        <td>
            <select <?php echo $parent_cat_info ? 'disabled="disabled"' : ''; ?> id="cat_type" name="cat_type">
     		<?php foreach ($cat_type_list as $type_id => $type_name): ?>
            	<option <?php echo ($parent_cat_info && $type_id==$parent_cat_info['cat_type']) ? 'selected="selected"' : ''; ?> value="<?php echo $type_id; ?>"><?php echo $type_name; ?></option>
            <?php endforeach; ?>
        	</select>
        </td>
    </tr>
	<tr>
        <th>是否外部链接：</th>
        <td>
            <select name="is_out_url">
                <option value="0">否</option>
                <option value="1">是</option>
            </select>
        </td>
    </tr>
	<tr>
        <th>外部链接地址：</th>
        <td><input type="text" name="out_url" style="width:250px;" id="out_url" class="input-text" /></td>
    </tr>
	<tr>
        <th>是否显示分类：</th>
        <td>
            <input type="radio" name="display" value="1" checked> 是
            <input type="radio" name="display" value="0"> 否
        </td>
    </tr>
    <tr>
	    <td width="100%" align="center" colspan="2">
	       <input id="form_submit"  type="button" name="dosubmit" class="btn_submit"  value=" 提交 " />
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