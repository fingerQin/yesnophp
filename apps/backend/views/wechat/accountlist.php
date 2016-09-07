<?php
use common\YUrl;
require_once (APP_VIEW_PATH . DIRECTORY_SEPARATOR . 'common/header.php');
?>

<div class="subnav">
	<div class="content-menu ib-a blue line-x">
		<a class="add fb"
			href="javascript:postDialog('addAccount', '<?php echo YUrl::createBackendUrl('', 'WeChat', 'addAccount'); ?>', '添加公众号', 550, 460)"><em>添加公众号</em></a>
		<a href='javascript:;' class="on"><em>公众号列表</em></a>
	</div>
</div>
<style type="text/css">
html {
	_overflow-y: scroll
}
</style>

<div class="pad-lr-10">

	<form name="searchform" action="" method="get">
		<table width="100%" cellspacing="0" class="search-form">
			<tbody>
				<tr>
					<td>
						<div class="explain-col">
							<p>
								公众号微信号：<input type="text" value="<?php echo $account; ?>"
									class="input-text" name="account" placeholder="请输入要查询的公众号微信号" />
								APPID：<input type="text" value="<?php echo $appid; ?>"
									class="input-text" name="appid" placeholder="请输入要查询的公众号APPID" />
								公众号编码：<input type="text" value="<?php echo $sn; ?>"
									class="input-text" name="sn" placeholder="请输入要查询的公众号编码" /> <input
									type="submit" name="search" class="button" value="搜索" />
							</p>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</form>


	<form name="myform" id="myform" action="" method="post">
		<div class="table-list">
			<table width="100%" cellspacing="0">
				<thead>
					<tr>
						<th width="5%" align="center">ID</th>
						<th width="10%" align="center">公众号微信号</th>
						<th width="10%" align="center">编码</th>
						<th width="10%" align="center">APPID</th>
						<th width="5%" align="center">公众号类型</th>
						<th width="10%" align="center">认证状态</th>
						<th width="10%" align="center">注册时间</th>
						<th width="15%" align="center">管理操作</th>
					</tr>
				</thead>
				<tbody>
    <?php foreach ($list as $item): ?>
    	<tr>
						<td align="center"><?php echo $item['account_id']; ?></td>
						<td align="center"><?php echo $item['wx_account']; ?></td>
						<td align="center"><?php echo $item['wx_sn']; ?></td>
						<td align="center"><?php echo $item['wx_appid']; ?></td>
						<td align="center"><?php echo $wechat_type_dict[$item['wx_type']]; ?></td>
						<td align="center"><?php echo $item['wx_auth'] ? '是' : '否'; ?></td>
						<td align="center"><?php echo date('Y-m-d H:i:s', $item['created_time']); ?></td>
						<td align="center">[<a href="###"
							onclick="edit(<?php echo $item['account_id'] ?>, '<?php echo $item['wx_account'] ?>')"
							title="修改">修改</a>]&nbsp; [<a href="###"
							onclick="deleteDialog('deleteAccount', '<?php echo YUrl::createBackendUrl('', 'WeChat', 'deleteAccount', ['account_id' => $item['account_id']]); ?>', '<?php echo $item['wx_account'] ?>')"
							title="删除">删除</a>]<br /> [<a href="###"
							onclick="newsManage(<?php echo $item['account_id'] ?>, '<?php echo $item['wx_account'] ?>')"
							title="图文管理">图文管理</a>]&nbsp; [<a href="###"
							onclick="menuManage(<?php echo $item['account_id'] ?>, '<?php echo $item['wx_account'] ?>')"
							title="菜单管理">菜单管理</a>]
						</td>
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
function edit(id, name) {
	var title = '修改『' + name + '』';
	var page_url = "<?php echo YUrl::createBackendUrl('', 'WeChat', 'editAccount'); ?>?account_id="+id;
	postDialog('editAccount', page_url, title, 550, 460);
}

function newsManage(id, name) {
	var title = '管理『' + name + '』图文管理';
	var page_url = "<?php echo YUrl::createBackendUrl('', 'WeChat', 'imageTextList'); ?>?account_id="+id;
	postDialog('imageTextList', page_url, title, 550, 460);
}

function menuManage(id, name) {
	var title = '管理『' + name + '』公众号菜单';
	var page_url = "<?php echo YUrl::createBackendUrl('', 'WeChat', 'accountMenuList'); ?>?account_id="+id;
	postDialog('accountMenuList', page_url, title, 550, 460);
}
</script>
</body>
</html>