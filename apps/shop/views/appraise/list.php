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
				<a href="/">首页</a> <span class="arrow">></span> <a href="">交易管理</a><span
					class="arrow"> > </span> <a href="">评价管理</a>
			</div>

			<div class="site-filter-bar m-t-20">
				<div class="tags">
					<ul class="cc">
						<li class="active">全部评价</li>
						<li>在售的商品</li>
						<li>下架的商品</li>
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

				<div class="list-item">
					<div class="detail cc">
						<div class="col-5">
							<div class="thumb">
								<img
									src="<?php echo YUrl::assets('image', '/shop/_goods_image.png'); ?>"
									alt="东北优质大米">
							</div>
							<div class="info">
								<strong>【天猫超市】恒大兴安贡米一号5kg/盒 绿色健康非转基因 </strong>
								<p class="gray-9 m-t-10">一粥一饭 五常稻花香大米 纯正东北黑龙江贡米新米5kg寿司米10斤</p>
							</div>
						</div>
						<div class="col-1">
							<p class="label">
								<strong class="red">129 ~ 199.00</strong>
							</p>
						</div>
						<div class="col-1">
							<p class="label">
								905 <i class="edit" action-do="editLocalStock" data-goods-id="1"></i>
							</p>
						</div>
						<div class="col-1">
							<p class="label">
								<span>15</span> <i class="edit" action-do="editBuyCount"
									data-goods-id="1"></i>
							</p>
						</div>
						<div class="col-1">
							<p class="label m-t-20 gray-9">2016-01-04 13:55:43</p>
						</div>
						<div class="col-1">
							<p class="ctrl m-t-20">
								<a target="_blank"
									href="<?php echo YUrl::createShopUrl('', 'Goods', 'detail', ['goods_id' => 1]); ?>">[编辑商品]</a>
							</p>
						</div>
					</div>
				</div>

			</div>

			<div class="m-t-30">
				<div class="site-page">
					<form>
						<span class="page">上一页</span><span class="page">1</span><span
							class="page">下一页</span> <span class="page-label">共1页，到第</span><input
							type="text" name="page" class="i-txt" value="1"><span
							class="page-unit">页</span><input type="submit" class="i-sbmt"
							value="确定">
					</form>
				</div>
			</div>
			<div class="m-t-50"></div>
		</div>
	</div>
</div>
<?php
require_once (APP_VIEW_PATH . DIRECTORY_SEPARATOR . 'common/footer.php');
?>