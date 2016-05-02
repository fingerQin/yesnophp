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
			收款人姓名：<input type="text" value="<?php echo $realname; ?>" class="input-text" name="realname" />
			用户平台账号：<input type="text" value="<?php echo $username; ?>" class="input-text" name="username" />
			审核状态：<select name="audit_status">
			<option <?php echo $audit_status ==-1 ? 'selected="selected"' : ''; ?> value="-1">不限</option>
			<option <?php echo $audit_status == 0 ? 'selected="selected"' : ''; ?> value="0">待审核</option>
			<option <?php echo $audit_status == 1 ? 'selected="selected"' : ''; ?> value="1">通过审核</option>
			<option <?php echo $audit_status == 2 ? 'selected="selected"' : ''; ?> value="2">审核不通过</option>
			<option <?php echo $audit_status == 3 ? 'selected="selected"' : ''; ?> value="3">已付款</option>
			</select>
			付款类型：<select name="acct_type">
			<option <?php echo $acct_type ==-1 ? 'selected="selected"' : ''; ?> value="-1">不限</option>
			<option <?php echo $acct_type == 1 ? 'selected="selected"' : ''; ?> value="1">支付宝</option>
			<option <?php echo $acct_type == 2 ? 'selected="selected"' : ''; ?> value="2">微信零钱</option>
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
			<th align="center">提现ID</th>
			<th align="center">真实姓名</th>
			<th align="center">付款类型</th>
			<th align="center">提现金额</th>
			<th align="center">审核状态</th>
			<th align="center">备注</th>
			<th align="center">付款时间</th>
			<th align="center">申请时间</th>
			<th align="center">操作</th>
		</tr>
	</thead>
    <tbody>
        <?php foreach ($list as $item): ?>
    	<tr>
    		<td align="center"><?php echo $item['id'] ?></td>
    		<td align="center"><?php echo $item['realname']; ?></td>
    		<td align="center"><?php echo $item['acct_type'] == 1 ? '支付宝' : '微信零钱'; ?></td>
    		<td align="center">￥<?php echo $item['amount']; ?></td>
    		<td align="center"><?php echo $item['audit_status_label']; ?></td>
    		<td align="center"><?php echo $item['reason']; ?></td>
    		<td align="center"><?php echo $item['pay_time']; ?></td>
    		<td align="center"><?php echo $item['created_time'] ?></td>
    		<td align="center">
    		  <?php if ($item['is_pay']): ?>
    		  <a href="javascript:openWinDialog(<?php echo $item['id']; ?>);">查看付款详情</a>
    		  <?php else: ?>
    		  <a href="javascript:cashToPerson(<?php echo $item['id']; ?>, '<?php echo $item['realname']; ?>', '<?php echo $item['amount']; ?>');">付款</a>
    		  <?php endif; ?>
    		</td>
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

function openWinDialog(id) {
	top.dialog({
		id : 'withdrawPayInfo_' + id,
	    title: "付款详情",
	    url: '<?php echo YUrl::createAdminUrl('Index', 'Cash', 'withdrawPayInfo'); ?>?id=' + id,
	    width: 500,
	    height: 300,
	    scrolling: 'no'
	}).showModal();
}

</script>
</body>
</html>