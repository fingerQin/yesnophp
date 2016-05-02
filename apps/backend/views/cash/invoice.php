<?php
use common\YUrl;
require_once(APP_VIEW_PATH . DIRECTORY_SEPARATOR . 'common/header.php');
?>

<div class="subnav">
</div>

<style type="text/css">
	html{_overflow-y:scroll}
</style>

<div class="pad-lr-10">

<form name="searchform" action="" method="get">
<table width="100%" cellspacing="0" class="search-form">
    <tbody>
		<tr>
		<td>
		<div class="explain-col">
			<p>
			厂商名称：<select name="shop_id">
			<?php foreach($shop_list as $shop): ?>
            <option <?php echo $shop['shop_id']==$shop_id ? 'selected="selected"' : ''; ?> value="<?php echo $shop['shop_id']; ?>"><?php echo $shop['shop_name']; ?></option>			
			<?php endforeach; ?>
			</select>
			开票状态：<select name="is_ok">
			<option <?php echo $is_ok ==-1 ? 'selected="selected"' : ''; ?> value="-1">全部</option>
			<option <?php echo $is_ok == 1 ? 'selected="selected"' : ''; ?> value="1">已开票</option>
			<option <?php echo $is_ok == 0 ? 'selected="selected"' : ''; ?> value="0">未开票</option>
			</select>
    		<input style="margin-left:20px;" type="submit" name="search" class="button" value="搜索" />
    		</p>
		</div>
		</td>
		</tr>
    </tbody>
</table>
</form>


<div id="table-list-id" class="table-list">

<table width="100%" cellspacing="0">
	<thead>
		<tr>
			<th align="center">ID</th>
			<th align="center">厂商名称</th>
			<th align="center">发票金额</th>
			<th align="center">发票状态</th>
			<th align="center">创建时间</th>
			<th align="center">操作</th>
		</tr>
	</thead>
    <tbody>
        <?php foreach ($list as $item): ?>
    	<tr>
    		<td align="center"><?php echo $item['id'] ?></td>
    		<td align="center"><?php echo $item['shop_name']; ?></td>
    		<td align="center">￥<?php echo $item['money']; ?></td>
    		<td align="center"><?php echo $item['is_ok'] ? '已开票' : '未开票'; ?></td>
    		<td align="center"><?php echo date('Y-m-d H:i:s', $item['created_time']); ?></td>
    		<td align="center">
    		  <a href="javascript:normalDialog(<?php echo $item['id']; ?>, '<?php echo YUrl::createAdminUrl('Index', 'Cash', 'invoiceOk', ['id' => $item['id']]); ?>', '<?php echo "{$item['shop_name']} ￥：{$item['money']}"; ?>');">【确认开票】</a>
    		</td>
    	</tr>
    	<?php endforeach; ?>
    </tbody>
</table>


<div id="pages">
<?php echo $page_html; ?>
</div>

</div>

</div>

<script type="text/javascript">

/**
 * 支付操作。
 * @param withdraw_id 提现ID。
 * @param realname 真实姓名。
 * @param amount 付款金额。
 */
function cashToPerson(withdraw_id, realname, amount) {
	var d = dialog({
	    title: "操作提示",
	    content: "您确定要向【" + realname + "】付款" + amount + "元吗？",
	    okValue: '确认付款',
	    ok: function () {
	        this.title('提交中…');
	        $.ajax({
		        url: "<?php echo YUrl::createAdminUrl('Index', 'Pay', 'wxCashToPerson'); ?>", 
		        data: {'id' : withdraw_id},
		        dataType: 'json', 
		        success: function(data) {
			        console.log(data.errcode);
		            if (data.errcode == 0) {
		            	dialogTips(data.errmsg, 2);
		            } else {
		            	dialogTips(data.errmsg, 3);
			        }
	            }
	        });
	        return true;
	    },
	    cancelValue: '取消',
	    cancel: function () {}
	});
	d.show();
}


</script>
</body>
</html>