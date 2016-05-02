<?php
use common\YUrl;
require_once(APP_VIEW_PATH . DIRECTORY_SEPARATOR . 'common/header.php');
?>

<style type="text/css">
	html{_overflow-y:scroll}
</style><div class="pad-lr-10">

<form name="searchform" action="" method="get">
<table width="100%" cellspacing="0" class="search-form">
    <tbody>
		<tr>
		<td>
		<div class="explain-col">
			<p>
			账单名称：<input type="text" value="<?php echo $title; ?>" class="input-text" name="title" />
			厂商确认状态：<select name="shop_confirm_status">
			<option <?php echo $shop_confirm_status==-1 ? 'selected="selected"' : ''; ?> value="-1">全部</option>
			<option <?php echo $shop_confirm_status==1 ? 'selected="selected"' : ''; ?> value="1">已确认</option>
			<option <?php echo $shop_confirm_status==0 ? 'selected="selected"' : ''; ?> value="0">未确认</option>
			</select>
    		<input style="margin-left:20px;" type="submit" name="search" class="button" value="搜索" />
    		</p>
		</div>
		</td>
		</tr>
    </tbody>
</table>
</form>


<form name="dialog_form" id="myform" action="<?php echo YUrl::createAdminUrl('Index', 'Cash', 'addOrderToAccountCheck'); ?>" method="post" >
<div id="table-list-id" class="table-list">

<table width="100%" cellspacing="0">
	<thead>
		<tr>
			<th align="center">
				单选
			</th>
			<th align="center">账单ID</th>
			<th align="center">账单名称</th>
			<th align="center">账单总额</th>
			<th align="center">直接佣金总和</th>
			<th align="center">间接佣金总和</th>
			<th align="center">厂商确认时间</th>
			<th align="center">厂商确认人</th>
		</tr>
	</thead>
    <tbody>
        <?php foreach ($list as $item): ?>
    	<tr>
    		<td align="center" width="35">
    			<input <?php echo $item['is_payemnt'] ? 'disabled' : ''; ?> type="radio" name="account_id" value="<?php echo $item['account_id']; ?>" />
    		</td>
    		<td align="center"><?php echo $item['account_id'] ?></td>
    		<td align="center"><?php echo $item['title'] ?></td>
    		<td align="center">￥<?php echo $item['total_payment'] ?></td>
    		<td align="center">￥<?php echo $item['direct_commission']; ?></td>
    		<td align="center">￥<?php echo $item['indirect_commission']; ?></td>
    		<td align="center"><?php echo $item['confirm_time_txt']; ?></td>
    		<td align="center"><?php echo $item['confirm_by_txt']; ?></td>
    	</tr>
    	<?php endforeach; ?>
    </tbody>
</table>

<div class="btn"> 
</div>
<div id="pages">
<?php echo $page_html; ?>
</div>
</div>
</form>

<button id="dosubmit" type="button">提交</button>

</div>

<script type="text/javascript">
$(function(){
	var dialog = top.dialog.get(window);
	var data = dialog.data; // 获取对话框传递过来的数据
	$('#dosubmit').click(function(){
		account_id = $('input[name="account_id"]:checked').val();
		if (account_id == undefined) {
			dialogTips('请选择对账单', 3);
			return false;
		}
		$.ajax({
	    	type: 'post',
            url: '<?php echo YUrl::createAdminUrl('Index', 'Cash', 'addOrderToAccountCheck'); ?>',
            dataType: 'json',
            data: data + "&account_id="+account_id,
            success: function(data) {
                if (data.errcode == 0) {
                	top.dialog.getCurrent().close({"refresh" : 1});
                } else {
                	dialogTips(data.errmsg, 5);
                }
            }
	    });
	});
});
</script>

</body>
</html>