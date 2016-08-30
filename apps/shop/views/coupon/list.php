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
				<a href="/">首页</a> <span class="arrow">></span> <a href="">促销管理</a>
				<span class="arrow"> > </span> <a href="">优惠券列表</a>
				<button style="float:right;" onClick="postDialog('addCoupon', '<?php echo YUrl::createShopUrl('', 'Coupon', 'add'); ?>', '添加优惠券', 450, 420);" class="normal_btn">添加优惠券</button>
			</div>

			<div class="site-filter-bar m-t-20">
				<div class="tags">
					<ul class="cc">
						<li <?php echo ($status==-1) ? 'class="active"' : ''; ?> onClick="chanageStatus(-1);">全部</li>
						<li <?php echo ($status==1) ? 'class="active"' : ''; ?> onClick="chanageStatus(1);">未开始</li>
						<li <?php echo ($status==2) ? 'class="active"' : ''; ?> onClick="chanageStatus(2);">领取中</li>
						<li <?php echo ($status==3) ? 'class="active"' : ''; ?> onClick="chanageStatus(3);">已结束</li>
						<li <?php echo ($status==4) ? 'class="active"' : ''; ?> onClick="chanageStatus(4);">已过期</li>
					</ul>
				</div>
				<div class="bar">
					<dl>
						<dt class="col-2" style="text-align: center;">
							<span>优惠券名称</span>
						</dt>
						<dd class="col-1">金额</dd>
						<dd class="col-1">订单金额</dd>
						<dd class="col-1">领取/使用数量</dd>
						<dd class="col-2">过期时间</dd>
						<dd class="col-2">领取时间</dd>
						<dd class="col-1">操作</dd>
					</dl>
				</div>
			</div>
			<div class="site-list comment-list" id="goods-list">

				<?php foreach ($list as $coupon): ?>
				<div class="list-item">
					<div class="detail cc">
						<div class="col-2">
							<div class="label">
								<strong><?php echo htmlspecialchars($coupon['coupon_name']); ?></strong>
							</div>
						</div>
						<div class="col-1">
							<p class="label">
								<strong class="red"><?php echo $coupon['money']; ?></strong>
							</p>
						</div>
						<div class="col-1">
							<p class="label">
								<strong class="red"><?php echo $coupon['order_money']; ?></strong>
							</p>
						</div>
						<div class="col-1">
							<p class="label"><?php echo "{$coupon['get_count']}/{$coupon['use_count']}"; ?></p>
						</div>
						<div class="col-2">
							<p class="label"><?php echo $coupon['expiry_date']; ?></p>
						</div>
						<div class="col-2">
							<p class="label m-t-20"><?php echo $coupon['get_start_time']; ?><br /><?php echo $coupon['get_end_time']; ?></p>
						</div>
						<div class="col-1">
							<p class="ctrl m-t-20">
								<a href="###" onClick="edit(<?php echo $coupon['coupon_id'] ?>, '<?php echo $coupon['coupon_name']; ?>')">[编辑]</a>
								<a href="###" onclick="deleteDialog('deleteCoupon', '<?php echo YUrl::createShopUrl('', 'Coupon', 'delete', ['coupon_id' => $coupon['coupon_id']]); ?>', '<?php echo $coupon['coupon_name'] ?>')" title="删除">[删除]</a>
							</p>
						</div>
					</div>
				</div>
				<?php endforeach; ?>

			</div>
			<div class="m-t-30">
				<div class="site-page">
					<?php echo $page_html; ?>
				</div>
			</div>
			<div class="m-t-50"></div>
		</div>
	</div>
</div>

<script type="text/javascript">
function edit(id, name) {
	var title = '修改『' + name + '』';
	var page_url = "<?php echo YUrl::createShopUrl('', 'Coupon', 'edit'); ?>?coupon_id="+id;
	postDialog('editCoupon', page_url, title, 450, 420);
}

/**
 * 切换状态。
 * @param number status 1未开始、2领取中、3已结束、4已过期。
 */
function chanageStatus(status) {
	var redirect_url = "<?php echo YUrl::createShopUrl('', 'Coupon', 'List'); ?>?status="+status;
	window.location.href=redirect_url
}
</script>

<?php
require_once (APP_VIEW_PATH . DIRECTORY_SEPARATOR . 'common/footer.php');
?>