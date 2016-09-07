<?php
use common\YUrl;
require_once (APP_VIEW_PATH . DIRECTORY_SEPARATOR . 'common/header.php');
?>
<div class="main" id="main">
	<div class="w cc">
			<?php
require_once (APP_VIEW_PATH . DIRECTORY_SEPARATOR . 'common/left_menu.php');
?>
			<div class="container">
			<div class="site-crumb">
				<a href="/">首页</a> <span class="arrow">></span> <a href="">商品管理</a>
				<span class="arrow"> > </span> <a href="">运费模板</a>
				<button style="float: right;"
					onClick="postDialog('addFreight', '<?php echo YUrl::createShopUrl('', 'Freight', 'add'); ?>', '添加运费模板', 520, 450);"
					class="normal_btn">添加运费模板</button>
			</div>

			<div class="site-filter-bar m-t-20">
				<div class="bar">
					<dl>
						<dt class="col-3" style="text-align: center;">
							<span>模板名称</span>
						</dt>
						<dd class="col-1">运费承担</dd>
						<dd class="col-1">发货时间</dd>
						<dd class="col-1">计费类型</dd>
						<dd class="col-1">包邮金额</dd>
						<dd class="col-2">创建时间</dd>
						<dd class="col-1">操作</dd>
					</dl>
				</div>
			</div>
			<div class="site-list comment-list" id="goods-list">

				<?php foreach ($list as $tpl): ?>
				<div class="list-item">
					<div class="detail cc">
						<div class="col-3 txt-c">
							<?php echo htmlspecialchars($tpl['freight_name']); ?>
						</div>
						<div class="col-1 txt-c">
							<?php echo $tpl['bear_freight']; ?>
						</div>
						<div class="col-1 txt-c">
							<?php echo $tpl['send_time']; ?>(小时)
						</div>
						<div class="col-1 txt-c">
							<?php echo $tpl['fright_type']; ?>
						</div>
						<div class="col-1 txt-c">
							<?php echo $tpl['baoyou_fee']; ?>
						</div>
						<div class="col-2 txt-c">
							<?php echo $tpl['created_time']; ?>
						</div>
						<div class="col-1 txt-c">
							<p class="ctrl">
								<a href="###"
									onClick="edit(<?php echo $tpl['tpl_id'] ?>, '<?php echo htmlspecialchars($tpl['freight_name']); ?>')">[编辑]</a>
								<a href="###"
									onclick="deleteDialog('deleteFreight', '<?php echo YUrl::createShopUrl('', 'Freight', 'delete', ['tpl_id' => $tpl['tpl_id']]); ?>', '<?php echo htmlspecialchars($tpl['freight_name']); ?>')"
									title="删除">[删除]</a>
							</p>
						</div>
					</div>
				</div>
				<?php endforeach; ?>

			</div>

			<div class="m-t-50"></div>
		</div>
	</div>
</div>

<script type="text/javascript">
function edit(id, name) {
	var title = '修改『' + name + '』';
	var page_url = "<?php echo YUrl::createShopUrl('', 'Freight', 'edit'); ?>?tpl_id="+id;
	postDialog('editFreight', page_url, title, 520, 450);
}
</script>

<?php
require_once (APP_VIEW_PATH . DIRECTORY_SEPARATOR . 'common/footer.php');
?>