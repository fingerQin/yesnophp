<?php
use services\GoodsService;
use services\OrderService;
use services\FreightService;
use services\CouponService;
/**
 * 测试商家相关功能。
 * @author winerQin
 * @date 2016-06-21
 */

class ShopController extends \common\controllers\Common {

	/**
	 * 添加运费模板。
	 */
	public function addFreightTplAction() {
		$data = [
				'user_id'      => 1,
				'send_time'    => 12,
				'bear_freight' => 2,
				'rate_step'    => 1,
				'step_freight' => 5,
				'shop_id'      => 1,
				'freight_name' => '专家承担运费-生鲜产品类模板',
				'fright_type'  => 1,
				'base_step'    => 1,
				'base_freight' => 10,
				'no_area'      => '',
				'baoyou_fee'   => '0'
		];
		FreightService::addFreightTpl($data);
		echo 'ok';
		$this->end();
	}

	/**
	 * 添加无规格商品。
	 */
	public function addGoodsAction() {
		$goods_album = [
				'images/voucher/20160612/001.jpg',
				'images/voucher/20160612/002.jpg',
				'images/voucher/20160612/003.jpg',
				'images/voucher/20160612/004.jpg',
				'images/voucher/20160612/001.jpg'
		];
		$description = '<p><img style="max-width: 750.0px;" src="https://img.alicdn.com/imgextra/i4/274607153/TB27f6AqpXXXXazXXXXXXXXXXXX_!!274607153.jpg" align="absmiddle"><img style="max-width: 750.0px;" src="https://img.alicdn.com/imgextra/i3/274607153/TB2SoG5qpXXXXaMXpXXXXXXXXXX_!!274607153.gif" align="absmiddle"><img style="max-width: 750.0px;" src="https://img.alicdn.com/imgextra/i2/274607153/TB2BkbyqpXXXXbGXXXXXXXXXXXX_!!274607153.jpg" align="absmiddle"><img style="max-width: 750.0px;" src="https://img.alicdn.com/imgextra/i2/274607153/TB2wVLeqpXXXXXsXpXXXXXXXXXX_!!274607153.gif" align="absmiddle"><img style="max-width: 750.0px;" src="https://img.alicdn.com/imgextra/i1/274607153/TB2MLvXqpXXXXX.XpXXXXXXXXXX_!!274607153.jpg" class="" align="absmiddle" width="750" height="736"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i4/274607153/TB2FOYomFXXXXawXXXXXXXXXXXX_!!274607153.jpg" width="750" height="752"> <img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i3/274607153/TB2jzMHlFXXXXaTXXXXXXXXXXXX_!!274607153.jpg" width="750" height="550"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i2/274607153/TB2FdQxlFXXXXbVXXXXXXXXXXXX_!!274607153.jpg" width="750" height="754"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i2/274607153/TB2hWItlFXXXXcAXXXXXXXXXXXX_!!274607153.jpg" width="750" height="895"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i2/274607153/TB2HmwclFXXXXamXpXXXXXXXXXX_!!274607153.jpg" width="750" height="824"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i2/274607153/TB2RocqlFXXXXcAXXXXXXXXXXXX_!!274607153.jpg" width="750" height="543"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i4/274607153/TB2QHZwlFXXXXbNXXXXXXXXXXXX_!!274607153.jpg" width="750" height="688"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i1/274607153/TB2D4MelFXXXXagXpXXXXXXXXXX_!!274607153.jpg" width="750" height="935"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i3/274607153/TB2GbIclFXXXXXNXpXXXXXXXXXX_!!274607153.jpg" width="750" height="741"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i1/274607153/TB2FkshlFXXXXXvXpXXXXXXXXXX_!!274607153.jpg" width="750" height="576"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i3/274607153/TB259gClFXXXXbuXXXXXXXXXXXX_!!274607153.jpg" width="750" height="633"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i2/274607153/TB2pXwdlFXXXXaoXpXXXXXXXXXX_!!274607153.jpg" width="750" height="700"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i1/274607153/TB29ucXlFXXXXaHXpXXXXXXXXXX_!!274607153.jpg" width="750" height="595"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i2/274607153/TB2JKz3lFXXXXbbXpXXXXXXXXXX_!!274607153.jpg" width="750" height="705"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i1/274607153/TB21wUKlFXXXXavXXXXXXXXXXXX_!!274607153.jpg" width="750" height="934"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i1/274607153/TB2MdT4lFXXXXb_XpXXXXXXXXXX_!!274607153.jpg" width="750" height="1256"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i1/274607153/TB2PCZnlFXXXXcCXXXXXXXXXXXX_!!274607153.jpg" width="750" height="961"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i1/274607153/TB28NgklFXXXXXsXpXXXXXXXXXX_!!274607153.jpg" width="750" height="1268"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i1/274607153/TB2rU69lFXXXXaHXpXXXXXXXXXX_!!274607153.jpg" width="750" height="899"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i3/274607153/TB2NBLhlVXXXXaXXXXXXXXXXXXX_!!274607153.jpg" width="790" height="732"> <img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i4/274607153/TB2DscolFXXXXc.XXXXXXXXXXXX_!!274607153.jpg" width="750" height="461"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i1/274607153/TB26q.KlFXXXXawXXXXXXXXXXXX_!!274607153.jpg" width="750" height="1156"></p>';
		$data = [
				'user_id'        => 1,
				'shop_id'        => 1,
				'goods_name'     => 'jkjk夏季男士凉鞋真皮新款沙滩鞋男透气休闲男鞋罗马鞋男露趾凉拖',
				'cat_id'         => 3,
				'custom_cat_id'  => 2,
				'slogan'         => '全国包邮！ 头层牛皮！ 今日特惠！ 49元！',
				'weight'         => 500,
				'listorder'      => 30,
				'description'    => $description,
				'spec_val'       => [],
				'products'       => [],
				'goods_album'    => $goods_album,
				'market_price'   => 99,
				'sales_price'    => 70,
				'stock'          => 999,
				'freight_tpl_id' => 1,
		];
		GoodsService::addGoods($data);
		$this->end();
	}

	/**
	 * 添加多规格商品。
	 */
	public function addMoreSpecGoodsAction() {
		$spec_val = [
				'颜色' => ['银色', '黑色'],
				'尺寸' => ['35', '38']
		];
		$products = [
				'颜色:银色|尺寸:35' => ['market_price' => 129, 'sales_price' => 99, 'stock' => '999'],
				'颜色:黑色|尺寸:35' => ['market_price' => 129, 'sales_price' => 99, 'stock' => '999'],
				'颜色:银色|尺寸:38' => ['market_price' => 129, 'sales_price' => 99, 'stock' => '999'],
				'颜色:黑色|尺寸:38' => ['market_price' => 129, 'sales_price' => 99, 'stock' => '999'],
		];
		$goods_album = [
				'images/voucher/20160612/001.jpg',
				'images/voucher/20160612/002.jpg',
				'images/voucher/20160612/003.jpg',
				'images/voucher/20160612/004.jpg',
				'images/voucher/20160612/001.jpg'
		];
		$description = '<p><img style="max-width: 750.0px;" src="https://img.alicdn.com/imgextra/i4/274607153/TB27f6AqpXXXXazXXXXXXXXXXXX_!!274607153.jpg" align="absmiddle"><img style="max-width: 750.0px;" src="https://img.alicdn.com/imgextra/i3/274607153/TB2SoG5qpXXXXaMXpXXXXXXXXXX_!!274607153.gif" align="absmiddle"><img style="max-width: 750.0px;" src="https://img.alicdn.com/imgextra/i2/274607153/TB2BkbyqpXXXXbGXXXXXXXXXXXX_!!274607153.jpg" align="absmiddle"><img style="max-width: 750.0px;" src="https://img.alicdn.com/imgextra/i2/274607153/TB2wVLeqpXXXXXsXpXXXXXXXXXX_!!274607153.gif" align="absmiddle"><img style="max-width: 750.0px;" src="https://img.alicdn.com/imgextra/i1/274607153/TB2MLvXqpXXXXX.XpXXXXXXXXXX_!!274607153.jpg" class="" align="absmiddle" width="750" height="736"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i4/274607153/TB2FOYomFXXXXawXXXXXXXXXXXX_!!274607153.jpg" width="750" height="752"> <img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i3/274607153/TB2jzMHlFXXXXaTXXXXXXXXXXXX_!!274607153.jpg" width="750" height="550"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i2/274607153/TB2FdQxlFXXXXbVXXXXXXXXXXXX_!!274607153.jpg" width="750" height="754"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i2/274607153/TB2hWItlFXXXXcAXXXXXXXXXXXX_!!274607153.jpg" width="750" height="895"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i2/274607153/TB2HmwclFXXXXamXpXXXXXXXXXX_!!274607153.jpg" width="750" height="824"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i2/274607153/TB2RocqlFXXXXcAXXXXXXXXXXXX_!!274607153.jpg" width="750" height="543"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i4/274607153/TB2QHZwlFXXXXbNXXXXXXXXXXXX_!!274607153.jpg" width="750" height="688"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i1/274607153/TB2D4MelFXXXXagXpXXXXXXXXXX_!!274607153.jpg" width="750" height="935"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i3/274607153/TB2GbIclFXXXXXNXpXXXXXXXXXX_!!274607153.jpg" width="750" height="741"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i1/274607153/TB2FkshlFXXXXXvXpXXXXXXXXXX_!!274607153.jpg" width="750" height="576"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i3/274607153/TB259gClFXXXXbuXXXXXXXXXXXX_!!274607153.jpg" width="750" height="633"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i2/274607153/TB2pXwdlFXXXXaoXpXXXXXXXXXX_!!274607153.jpg" width="750" height="700"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i1/274607153/TB29ucXlFXXXXaHXpXXXXXXXXXX_!!274607153.jpg" width="750" height="595"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i2/274607153/TB2JKz3lFXXXXbbXpXXXXXXXXXX_!!274607153.jpg" width="750" height="705"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i1/274607153/TB21wUKlFXXXXavXXXXXXXXXXXX_!!274607153.jpg" width="750" height="934"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i1/274607153/TB2MdT4lFXXXXb_XpXXXXXXXXXX_!!274607153.jpg" width="750" height="1256"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i1/274607153/TB2PCZnlFXXXXcCXXXXXXXXXXXX_!!274607153.jpg" width="750" height="961"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i1/274607153/TB28NgklFXXXXXsXpXXXXXXXXXX_!!274607153.jpg" width="750" height="1268"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i1/274607153/TB2rU69lFXXXXaHXpXXXXXXXXXX_!!274607153.jpg" width="750" height="899"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i3/274607153/TB2NBLhlVXXXXaXXXXXXXXXXXXX_!!274607153.jpg" width="790" height="732"> <img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i4/274607153/TB2DscolFXXXXc.XXXXXXXXXXXX_!!274607153.jpg" width="750" height="461"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i1/274607153/TB26q.KlFXXXXawXXXXXXXXXXXX_!!274607153.jpg" width="750" height="1156"></p>';
		$data = [
				'user_id'        => 1,
				'shop_id'        => 1,
				'goods_name'     => 'jkjk夏季男士凉鞋真皮新款沙滩鞋男透气休闲男鞋罗马鞋男露趾凉拖',
				'cat_id'         => 3,
				'custom_cat_id'  => 2,
				'slogan'         => '全国包邮！ 头层牛皮！ 今日特惠！ 49元！',
				'weight'         => 500,
				'listorder'      => 30,
				'description'    => $description,
				'spec_val'       => $spec_val,
				'products'       => $products,
				'goods_album'    => $goods_album,
				'market_price'   => '',
				'sales_price'    => '',
				'stock'          => '',
				'freight_tpl_id' => 1,
		];
		GoodsService::addGoods($data);
		$this->end();
	}

	/**
	 * 下单。
	 */
	public function submitOrderAction() {
		$goods_list = [
				[
					'goods_id'   => '2',
					'product_id' => '5',
					'quantity'   => '10'
				],
				[
					'goods_id'   => '2',
					'product_id' => '3',
					'quantity'   => '3'
				],
		];
		$new_address_info = [
				'realname'    => '覃礼钧',
				'district_id' => 242,
				'zipcode'     => '560001',
				'mobilephone' => '18575202691',
				'address'     => '科融创业大厦1513',
		];
		$data = [
				'user_id'          => 1,
				'goods_list'       => $goods_list,
				'address_id'       => -1,
				'need_invoice'     => 0,
				'invoice_type'     => 1,
				'invoice_name'     => '',
				'buyer_message'    => '我要买东西',
				'new_address_info' => $new_address_info,
		];
		$order_ids = OrderService::submitOrder($data);
		$this->end();
	}

	/**
	 * 添加优惠券。
	 */
	public function addCouponAction() {
		$get_start_time = '2016-06-01 00:00:00';
		$get_end_time   = '2016-09-01 00:00:00';
		$coupon_name    = '国庆狂欢20元优惠券';
		$money          = 20;
		$order_money    = 100;
		$expiry_date    = '2016-10-01 00:00:00';
		$limit_quantity = 1;
		CouponService::addCoupon(1, 1, $get_start_time, $get_end_time, $limit_quantity, $coupon_name, $money, $order_money, $expiry_date);
		$this->end();
	}
}