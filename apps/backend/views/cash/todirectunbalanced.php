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
			 当前未付款的提现总额：<span style="color: #F00;"><?php echo $money; ?>元</span>
			 &nbsp;&nbsp;&nbsp;未提现佣金总额：<span style="color: #F00;"><?php echo $dis_money['incomes']; ?>元</span>
			 &nbsp;&nbsp;&nbsp;预收益总额：<span style="color: #F00;"><?php echo $dis_money['pre_incomes']; ?>元</span>
			 &nbsp;&nbsp;&nbsp;累计收益总额：<span style="color: #F00;"><?php echo $dis_money['total_incomes']; ?>元</span>
			</p>
			<p style="margin-top: 20px;">
			<input type="text" value="<?php echo $keywords; ?>" class="input-text" name="keywords" placeholder="真实姓名或手机号" />
			排序[大-小]：<select name="orderby">
			<option <?php echo $orderby==-1 ? 'selected="selected"' : ''; ?> value="-1">注册时间</option>
			<option <?php echo $orderby==1 ? 'selected="selected"' : ''; ?> value="1">累计收益</option>
			<option <?php echo $orderby==2 ? 'selected="selected"' : ''; ?> value="2">剩余收益</option>
			<option <?php echo $orderby==3 ? 'selected="selected"' : ''; ?> value="3">预收益</option>
			</select>
    		<input type="submit" name="search" class="button" value="搜索" />
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
			<th align="center">直销员ID</th>
			<th align="center">真实姓名</th>
			<th align="center">手机号码</th>
			<th align="center">累计收益</th>
			<th align="center">剩余收益</th>
			<th align="center">预收益</th>
		</tr>
	</thead>
    <tbody>
        <?php foreach ($list as $item): ?>
    	<tr>
    		<td align="center"><?php echo $item['user_id'] ?></td>
    		<td align="center"><?php echo $item['real_name']; ?></td>
    		<td align="center"><?php echo $item['mobilephone']; ?></td>
    		<td align="center"><?php echo $item['total_incomes']; ?></td>
    		<td align="center"><?php echo $item['incomes']; ?></td>
    		<td align="center"><?php echo $item['pre_incomes']; ?></td>
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

</body>
</html>