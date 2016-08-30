<?php
use common\YUrl; 
?>
<div class="site-sidebar">
	<dl>
		<dt>店铺首页</dt>
	</dl>
	<dl>
		<dt>交易管理</dt>
		<dd>
			<a href="<?php echo YUrl::createShopUrl('', 'Order', 'list'); ?>" ><span>·</span>订单管理</a>
		</dd>
		<dd>
			<a href="<?php echo YUrl::createShopUrl('', 'Appraise', 'list'); ?>" ><span>·</span>评价管理</a>
		</dd>
	</dl>
	<dl>
		<dt>商品管理</dt>
		<dd>
			<a href="<?php echo YUrl::createShopUrl('', 'Goods', 'list'); ?>" ><span>·</span>商品列表</a>
		</dd>
		<dd>
			<a href="<?php echo YUrl::createShopUrl('', 'Goods', 'publish'); ?>" ><span>·</span>发布商品</a>
		</dd>
		<dd>
			<a href="<?php echo YUrl::createShopUrl('', 'Freight', 'list'); ?>" ><span>·</span>运费模板</a>
		</dd>
		<dd>
			<a href="<?php echo YUrl::createShopUrl('', 'Category', 'list'); ?>" ><span>·</span>自定义商品分类</a>
		</dd>
	</dl>
	<dl>
		<dt>促销管理</dt>
		<dd>
			<a href="<?php echo YUrl::createShopUrl('', 'Coupon', 'list'); ?>" ><span>·</span>优惠券列表</a>
		</dd>
	</dl>
	<dl>
		<dt>设置</dt>
		<dd>
			<a href="<?php echo YUrl::createShopUrl('', 'Shop', 'baseInfo'); ?>" ><span>·</span>商家设置</a>
		</dd>
	</dl>
	
</div>