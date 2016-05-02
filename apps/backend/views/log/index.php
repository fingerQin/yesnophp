<?php
use common\YCore;
use common\YUrl;
require_once(APP_VIEW_PATH . DIRECTORY_SEPARATOR . 'common/header.php');
?>
<div class="subnav">

</div>
<style type="text/css">
	html{_overflow-y:scroll}
</style><div class="pad-lr-10">


<form name="searchform" action="" method="get">
<table width="100%" cellspacing="0" class="search-form">
    <tbody>
		<tr>
		<td>
		<div class="explain-col">
     		<p style="margin-top:10px;">
     		错误码：
     		<input type="text" value="<?php echo $search['errcode']; ?>" class="input-text" name="errcode" placeholder="" />
     		日志类型：
     		<select name="log_type">
         		 <option value="-1">不限</option>
     		</select>
     		用户类型：
     		<select name="log_type">
         		 <option value="-1">不限</option>
     		</select>
     		<input type="text" value="" class="input-text" name="log_user" placeholder="账号名称" />
     		下单时间：<input type="text" name="starttime" id="starttime" value="<?php echo $search['starttime']; ?>" size="20" class="date input-text" /> ～ <input type="text" name="endtime" id="endtime" value="<?php echo $search['endtime']; ?>" size="20" class="date input-text" />
    		<script type="text/javascript">
Calendar.setup({
	weekNumbers: false,
    inputField : "starttime",
    trigger    : "starttime",
    dateFormat: "%Y-%m-%d %H:%I:%S",
    showTime: true,
    minuteStep: 1,
    onSelect   : function() {this.hide();}
});

Calendar.setup({
	weekNumbers: false,
    inputField : "endtime",
    trigger    : "endtime",
    dateFormat: "%Y-%m-%d %H:%I:%S",
    showTime: true,
    minuteStep: 1,
    onSelect   : function() {this.hide();}
});
</script>
    		<input type="submit" name="search" class="button" value="搜索" />
    		</p>
		</div>
		</td>
		</tr>
    </tbody>
</table>
</form>


<form name="myform" id="myform" action="?m=goods&c=shop&a=listorder" method="post" >
<div class="table-list">
<table width="100%" cellspacing="0">
	<thead>
		<tr>
			<th width="60" align="center">日志ID</th>
			<th width="60" align="center">日志类型</th>
			<th width="120" align="center">用户名称</th>
			<th width="120" align="center">日志时间</th>
			<th width="80" align="center">错误码</th>
			<th align="center">日志内容</th>
			<th width="120" align="center">创建时间</th>
			<th width="60" align="center">操作</th>
		</tr>
	</thead>
    <tbody>
    <?php foreach ($list as $item): ?>
    	<tr>
    		<td align="center"><?php echo $item['log_id']; ?></td>
    		<td align="center"><?php echo $item['log_type']; ?></td>
    		<td align="center"><?php echo $item['log_user_id']; ?></td>
    		<td align="center"><?php echo date('Y-m-d H:i:s', $item['log_time']); ?></td>
    		<td align="center"><?php echo $item['errcode']; ?></td>
    		<td align="left"><?php echo YCore::str_cut($item['content'], 80); ?></td>
    		<td align="center"><?php echo date('Y-m-d H:i:s', $item['created_time']); ?></td>
    		<td align="center"><a href="javascript:postDialog('viewdetail', '<?php echo YUrl::createAdminUrl('Index', 'Log', 'detail', ['log_id' => $item['log_id']]); ?>', '查看日志详情', 500, 380);">[详情]</a></td>
    	</tr>
    <?php endforeach; ?>
    </tbody>
</table>

<div id="pages">
<?php echo $page_html; ?>
</div>

</div>

</form>
</div>
<script type="text/javascript">

</script>
</body>
</html>