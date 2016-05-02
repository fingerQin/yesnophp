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
        			名称：<input type="text" value="<?php echo $title; ?>" class="input-text" name="title" />
        			支付状态：<select name="pay_status">
        			<option <?php echo $pay_status==-1 ? 'selected="selected"' : ''; ?> value="-1">全部</option>
        			<option <?php echo $pay_status==1 ? 'selected="selected"' : ''; ?> value="1">已支付</option>
        			<option <?php echo $pay_status==0 ? 'selected="selected"' : ''; ?> value="0">未支付</option>
        			</select>
            		<input style="margin-left:20px;" type="submit" name="search" class="button" value="搜索" />
            		</p>
        		</div>
    		</td>
		</tr>
    </tbody>
</table>
</form>


<form name="dialog_form" id="myform" action="<?php echo YUrl::createAdminUrl('Index', 'Cash', 'addAccountCheckToPaymentOrder'); ?>" method="post" >
<div id="table-list-id" class="table-list">

<table width="100%" cellspacing="0">
	<thead>
		<tr>
			<th align="center">
			</th>
			<th align="center">付款单ID</th>
			<th align="center">付款单名称</th>
			<th align="center">支付金额</th>
			<th align="center">支付时间</th>
			<th align="center">创建时间</th>
			<th align="center">创建人</th>
		</tr>
	</thead>
    <tbody>
        <?php foreach ($list as $item): ?>
    	<tr>
    		<td align="center" width="35">
    			<input type="radio" name="payment_id" value="<?php echo $item['payment_id']; ?>" />
    		</td>
    		<td align="center"><?php echo $item['payment_id'] ?></td>
    		<td align="center"><?php echo $item['title'] ?></td>
    		<td align="center">￥<?php echo $item['payment'] ?></td>
    		<td align="center"><?php echo $item['pay_time_txt']; ?></td>
    		<td align="center"><?php echo $item['created_time_txt']; ?></td>
    		<td align="center"><?php echo $item['created_by_txt'] ?></td>
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
		var payment_id = $('input[name="payment_id"]:checked').val()
		if (payment_id == undefined) {
			dialogTips('请勾选付款单', 3);
			return false;
		}
		$.ajax({
	    	type: 'post',
            url: '<?php echo YUrl::createAdminUrl('Index', 'Cash', 'addAccountCheckToPaymentOrder'); ?>',
            dataType: 'json',
            data: data + "&payment_id="+payment_id,
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