<?php
use common\YUrl;
require_once(APP_VIEW_PATH . DIRECTORY_SEPARATOR . 'common/header.php');
?>

<style type="text/css">
	html{_overflow-y:scroll}
</style>

<div class="common-form">
<form name="myform" id="myform" action="<?php echo YUrl::createAdminUrl('Index', 'Category', 'edit'); ?>" method="post">
<table width="100%" class="table_form contentWrap">
      <tr>
        <th width="120">上级分类：</th>
        <td>
        <select name="parentid" disabled="disabled">
        <option value="0">作为一级分类</option>
        <?php foreach ($list as $cat): ?>
        <option <?php echo ($cat['cat_id']==$detail['parentid']) ? 'selected="selected"' : ''; ?> value="<?php echo $cat['cat_id']; ?>"><?php echo $cat['cat_name']; ?></option>
			<?php if (isset($cat['sub'])): ?>
			<?php foreach ($cat['sub'] as $sub_m): ?>
			<option <?php echo ($sub_m['cat_id']==$detail['parentid']) ? 'selected="selected"' : ''; ?> value="<?php echo $sub_m['cat_id']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;├─<?php echo $sub_m['cat_name']; ?></option>
			<?php if (isset($sub_m['sub'])): ?>
			<?php foreach ($sub_m['sub'] as $ss_m): ?>
			<option <?php echo ($ss_m['cat_id']==$detail['parentid']) ? 'selected="selected"' : ''; ?> value="<?php echo $ss_m['cat_id']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;│&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;├─<?php echo $ss_m['cat_name']; ?></option>
			<?php endforeach; ?>
			<?php endif; ?>
			<?php endforeach; ?>
			<?php endif; ?>
		<?php endforeach; ?>
		</select>
		(不可修改)
		</td>
    </tr>
    <tr>
        <th> 分类名称：</th>
        <td><input type="text" name="cat_name" id="cat_name" class="input-text" value="<?php echo $detail['cat_name']; ?>"></td>
    </tr>
	<tr>
        <th>分类类型：</th>
        <td>
            <select name="cat_type" disabled="disabled">
     		<?php foreach ($cat_type_list as $type_id => $type_name): ?>
            	<option <?php echo ($type_id==$detail['cat_type']) ? 'selected="selected"' : ''; ?> value="<?php echo $type_id; ?>"><?php echo $type_name; ?></option>
            <?php endforeach; ?>
        	</select>
            (不可修改)
        </td>
    </tr>
	<tr>
        <th>是否外部链接：</th>
        <td>
            <select name="is_out_url">
                <option <?php echo $detail['cat_type']==0 ? 'selected="selected"' : ''; ?> value="0">否</option>
                <option <?php echo $detail['cat_type']==1 ? 'selected="selected"' : ''; ?> value="1">是</option>
            </select>
        </td>
    </tr>
	<tr>
        <th>外部链接地址：</th>
        <td><input type="text" name="out_url" style="width:250px;" id="out_url" class="input-text" value="<?php echo $detail['out_url']; ?>"/></td>
    </tr>
	<tr>
        <th>是否显示分类：</th>
        <td>
            <input <?php echo $detail['display'] ? 'checked="checked"' : ''; ?> type="radio" name="display" value="1" checked> 是
            <input <?php echo !$detail['display'] ? 'checked="checked"' : '';; ?> type="radio" name="display" value="0"> 否
        </td>
    </tr>
    <tr>
	    <td width="100%" align="center" colspan="2">
	       <input type="hidden" name="cat_id" value="<?php echo $detail['cat_id']; ?>" />
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