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
				<span class="arrow"> > </span> <a href="">商品列表</a>
			</div>

			<!-- 搜索条件 start -->
			<div class="site-filter-form cc">
				<form>
					<dl>
						<dt>商品名称：</dt>
						<dd>
							<input type="text" class="i-txt" size="40" name="keywords"
								placeholder="商品名称">
						</dd>
					</dl>
					<dl>
						<dt>商家类目：</dt>
						<dd>
							<select name="cat_id" class="slct">
								<option value="-1">全部类目</option>
								<option value="1">五谷杂粮</option>
								<option value="2">食用油</option>
							</select>
						</dd>
					</dl>
					<dl>
						<dt>价格：</dt>
						<dd>
							<input type="text" class="i-txt" name="start_price" size="16"
								placeholder="￥">
						</dd>
						<dd class="split">至</dd>
						<dd>
							<input type="text" class="i-txt" name="end_price" size="16"
								placeholder="￥">
						</dd>
					</dl>
					<dl>
						<dt>系统类目：</dt>
						<dd>
							<select name="cat_id" class="slct">
								<option value="-1">全部类目</option>
								<option value="1">五谷杂粮</option>
								<option value="2">食用油</option>
							</select>
						</dd>
					</dl>
					<dl class="ctrl clear">
						<dt>&nbsp;</dt>
						<dd>
							<input type="submit" value="搜 索" class="i-sbt">
						</dd>
					</dl>
				</form>
			</div>
			<!-- 搜索条件 end -->

			<div class="site-filter-bar m-t-20">
				<div class="tags">
					<ul class="cc">
						<li <?php echo ($updown==-1) ? 'class="active"' : ''; ?> onClick="chanageStatus(-1);">全部商品</li>
						<li <?php echo ($updown==1) ? 'class="active"' : ''; ?> onClick="chanageStatus(1);">在售的商品</li>
						<li <?php echo ($updown==0) ? 'class="active"' : ''; ?> onClick="chanageStatus(0);">下架的商品</li>
					</ul>
				</div>
				<div class="bar">
					<dl>
						<dt class="col-5" style="text-align: center;">
							<span>商品图片/名称</span>
						</dt>
						<dd class="col-1">价格</dd>
						<dd class="col-1">库存</dd>
						<dd class="col-1">总销量</dd>
						<dd class="col-1">上下架时间</dd>
						<dd class="col-1">操作</dd>
					</dl>
				</div>
			</div>
			<div class="site-list comment-list" id="goods-list">

				<?php foreach ($list as $goods): ?>
				<div class="list-item">
					<div class="detail cc">
						<div class="col-5">
							<div class="thumb">
								<img src="<?php echo $goods['goods_img']; ?>" alt="<?php echo htmlspecialchars($goods['goods_name']); ?>">
							</div>
							<div class="info">
								<strong><?php echo htmlspecialchars($goods['goods_name']); ?></strong>
								<p class="gray-9 m-t-10"><?php echo htmlspecialchars($goods['slogan']); ?></p>
							</div>
						</div>
						<div class="col-1">
							<p class="label">
								<strong class="red"><?php echo $goods['min_price'] . '~' . $goods['max_price']; ?></strong>
							</p>
						</div>
						<div class="col-1">
							<p class="label"> <?php echo $goods['stock']; ?> </p>
						</div>
						<div class="col-1">
							<p class="label">
								<span><?php echo $goods['buy_count']; ?></span>
							</p>
						</div>
						<div class="col-1">
							<p class="label m-t-20 gray-9"><?php echo $goods['marketable_time']; ?></p>
						</div>
						<div class="col-1">
							<p class="ctrl m-t-20">
								<a target="_blank" href="<?php echo YUrl::createShopUrl('', 'Goods', 'detail', ['goods_id' => $goods['goods_id']]); ?>">[编辑商品]</a>
								<br /> <a href="###" onclick="deleteDialog('deleteCoupon', '<?php echo YUrl::createShopUrl('', 'Goods', 'delete', ['goods_id' => $goods['goods_id']]); ?>', '<?php echo htmlspecialchars($goods['goods_name']); ?>')">[删除商品]</a>
								<?php if ($goods['marketable']): ?>
								<br /> <a href="###" onclick="normalDialog('updownCoupon', '<?php echo YUrl::createShopUrl('', 'Goods', 'updown', ['goods_id' => $goods['goods_id'], 'updown' => 0]); ?>', '您确定要下架该商品吗？')">[下架]</a>
								<?php else: ?>
								<br /> <a href="###" onclick="normalDialog('updownCoupon', '<?php echo YUrl::createShopUrl('', 'Goods', 'updown', ['goods_id' => $goods['goods_id'], 'updown' => 1]); ?>', '您确定要上架该商品吗？')">[上架]</a>
								<?php endif; ?>
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
/**
 * 切换状态。
 * @param number updown 上下架状态。-1不限、1上架、0下架。
 */
function chanageStatus(updown) {
	var redirect_url = "<?php echo YUrl::createShopUrl('', 'Goods', 'List'); ?>?updown="+updown;
	window.location.href=redirect_url
}
</script>

<?php
require_once (APP_VIEW_PATH . DIRECTORY_SEPARATOR . 'common/footer.php');
?>