<?php
/**
 * 商家中心。
 * @author winerQin
 * @date 2016-06-07
 */

use services\UploadService;
use services\GoodsService;
use services\ShopService;

class IndexController extends \common\controllers\Shop {
    
    public function indexAction() {
    
    }
    
    /**
     * 添加无规格商品。
     */
    public function addSingleGoodsAction() {
        $goods_album = [
                'images/voucher/20160401/56fe70362ef7e.jpg','images/voucher/20160401/56fe705fd37a2.jpg',
                'images/voucher/20160401/56fe710513c9e.jpg','images/voucher/20160402/56fea2043dc01.jpg',
                'images/voucher/20160402/56fea3f18677d.jpg' 
        ];
        $description = '<p><img style="max-width: 750.0px;" src="https://img.alicdn.com/imgextra/i4/274607153/TB27f6AqpXXXXazXXXXXXXXXXXX_!!274607153.jpg" align="absmiddle"><img style="max-width: 750.0px;" src="https://img.alicdn.com/imgextra/i3/274607153/TB2SoG5qpXXXXaMXpXXXXXXXXXX_!!274607153.gif" align="absmiddle"><img style="max-width: 750.0px;" src="https://img.alicdn.com/imgextra/i2/274607153/TB2BkbyqpXXXXbGXXXXXXXXXXXX_!!274607153.jpg" align="absmiddle"><img style="max-width: 750.0px;" src="https://img.alicdn.com/imgextra/i2/274607153/TB2wVLeqpXXXXXsXpXXXXXXXXXX_!!274607153.gif" align="absmiddle"><img style="max-width: 750.0px;" src="https://img.alicdn.com/imgextra/i1/274607153/TB2MLvXqpXXXXX.XpXXXXXXXXXX_!!274607153.jpg" class="" align="absmiddle" width="750" height="736"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i4/274607153/TB2FOYomFXXXXawXXXXXXXXXXXX_!!274607153.jpg" width="750" height="752"> <img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i3/274607153/TB2jzMHlFXXXXaTXXXXXXXXXXXX_!!274607153.jpg" width="750" height="550"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i2/274607153/TB2FdQxlFXXXXbVXXXXXXXXXXXX_!!274607153.jpg" width="750" height="754"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i2/274607153/TB2hWItlFXXXXcAXXXXXXXXXXXX_!!274607153.jpg" width="750" height="895"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i2/274607153/TB2HmwclFXXXXamXpXXXXXXXXXX_!!274607153.jpg" width="750" height="824"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i2/274607153/TB2RocqlFXXXXcAXXXXXXXXXXXX_!!274607153.jpg" width="750" height="543"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i4/274607153/TB2QHZwlFXXXXbNXXXXXXXXXXXX_!!274607153.jpg" width="750" height="688"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i1/274607153/TB2D4MelFXXXXagXpXXXXXXXXXX_!!274607153.jpg" width="750" height="935"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i3/274607153/TB2GbIclFXXXXXNXpXXXXXXXXXX_!!274607153.jpg" width="750" height="741"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i1/274607153/TB2FkshlFXXXXXvXpXXXXXXXXXX_!!274607153.jpg" width="750" height="576"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i3/274607153/TB259gClFXXXXbuXXXXXXXXXXXX_!!274607153.jpg" width="750" height="633"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i2/274607153/TB2pXwdlFXXXXaoXpXXXXXXXXXX_!!274607153.jpg" width="750" height="700"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i1/274607153/TB29ucXlFXXXXaHXpXXXXXXXXXX_!!274607153.jpg" width="750" height="595"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i2/274607153/TB2JKz3lFXXXXbbXpXXXXXXXXXX_!!274607153.jpg" width="750" height="705"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i1/274607153/TB21wUKlFXXXXavXXXXXXXXXXXX_!!274607153.jpg" width="750" height="934"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i1/274607153/TB2MdT4lFXXXXb_XpXXXXXXXXXX_!!274607153.jpg" width="750" height="1256"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i1/274607153/TB2PCZnlFXXXXcCXXXXXXXXXXXX_!!274607153.jpg" width="750" height="961"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i1/274607153/TB28NgklFXXXXXsXpXXXXXXXXXX_!!274607153.jpg" width="750" height="1268"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i1/274607153/TB2rU69lFXXXXaHXpXXXXXXXXXX_!!274607153.jpg" width="750" height="899"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i3/274607153/TB2NBLhlVXXXXaXXXXXXXXXXXXX_!!274607153.jpg" width="790" height="732"> <img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i4/274607153/TB2DscolFXXXXc.XXXXXXXXXXXX_!!274607153.jpg" width="750" height="461"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i1/274607153/TB26q.KlFXXXXawXXXXXXXXXXXX_!!274607153.jpg" width="750" height="1156"></p>';
        $data = [
                'user_id' => $this->user_id,'shop_id' => $this->shop_id,
                'goods_name' => 'jkjk夏季男士凉鞋真皮新款沙滩鞋男透气休闲男鞋罗马鞋男露趾凉拖','cat_id' => 3,'custom_cat_id' => 2,
                'slogan' => '全国包邮！ 头层牛皮！ 今日特惠！ 49元！','weight' => 500,'listorder' => 30,'description' => $description,
                'spec_val' => [],'products' => [],'goods_album' => $goods_album,'market_price' => 99,
                'sales_price' => 70,'stock' => 999,'freight_tpl_id' => 1 
        ];
        GoodsService::addGoods($data);
        $this->end();
    }
    
    /**
     * 编辑个规格商品。
     */
    public function editSingleGoodsAction() {
        $goods_album = [
                'images/voucher/20160401/56fe70362ef7e.jpg','images/voucher/20160401/56fe705fd37a2.jpg',
                'images/voucher/20160401/56fe710513c9e.jpg','images/voucher/20160402/56fea2043dc01.jpg',
                'images/voucher/20160402/56fea3f18677d.jpg' 
        ];
        $description = '<p><img style="max-width: 750.0px;" src="https://img.alicdn.com/imgextra/i4/274607153/TB27f6AqpXXXXazXXXXXXXXXXXX_!!274607153.jpg" align="absmiddle"><img style="max-width: 750.0px;" src="https://img.alicdn.com/imgextra/i3/274607153/TB2SoG5qpXXXXaMXpXXXXXXXXXX_!!274607153.gif" align="absmiddle"><img style="max-width: 750.0px;" src="https://img.alicdn.com/imgextra/i2/274607153/TB2BkbyqpXXXXbGXXXXXXXXXXXX_!!274607153.jpg" align="absmiddle"><img style="max-width: 750.0px;" src="https://img.alicdn.com/imgextra/i2/274607153/TB2wVLeqpXXXXXsXpXXXXXXXXXX_!!274607153.gif" align="absmiddle"><img style="max-width: 750.0px;" src="https://img.alicdn.com/imgextra/i1/274607153/TB2MLvXqpXXXXX.XpXXXXXXXXXX_!!274607153.jpg" class="" align="absmiddle" width="750" height="736"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i4/274607153/TB2FOYomFXXXXawXXXXXXXXXXXX_!!274607153.jpg" width="750" height="752"> <img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i3/274607153/TB2jzMHlFXXXXaTXXXXXXXXXXXX_!!274607153.jpg" width="750" height="550"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i2/274607153/TB2FdQxlFXXXXbVXXXXXXXXXXXX_!!274607153.jpg" width="750" height="754"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i2/274607153/TB2hWItlFXXXXcAXXXXXXXXXXXX_!!274607153.jpg" width="750" height="895"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i2/274607153/TB2HmwclFXXXXamXpXXXXXXXXXX_!!274607153.jpg" width="750" height="824"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i2/274607153/TB2RocqlFXXXXcAXXXXXXXXXXXX_!!274607153.jpg" width="750" height="543"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i4/274607153/TB2QHZwlFXXXXbNXXXXXXXXXXXX_!!274607153.jpg" width="750" height="688"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i1/274607153/TB2D4MelFXXXXagXpXXXXXXXXXX_!!274607153.jpg" width="750" height="935"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i3/274607153/TB2GbIclFXXXXXNXpXXXXXXXXXX_!!274607153.jpg" width="750" height="741"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i1/274607153/TB2FkshlFXXXXXvXpXXXXXXXXXX_!!274607153.jpg" width="750" height="576"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i3/274607153/TB259gClFXXXXbuXXXXXXXXXXXX_!!274607153.jpg" width="750" height="633"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i2/274607153/TB2pXwdlFXXXXaoXpXXXXXXXXXX_!!274607153.jpg" width="750" height="700"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i1/274607153/TB29ucXlFXXXXaHXpXXXXXXXXXX_!!274607153.jpg" width="750" height="595"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i2/274607153/TB2JKz3lFXXXXbbXpXXXXXXXXXX_!!274607153.jpg" width="750" height="705"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i1/274607153/TB21wUKlFXXXXavXXXXXXXXXXXX_!!274607153.jpg" width="750" height="934"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i1/274607153/TB2MdT4lFXXXXb_XpXXXXXXXXXX_!!274607153.jpg" width="750" height="1256"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i1/274607153/TB2PCZnlFXXXXcCXXXXXXXXXXXX_!!274607153.jpg" width="750" height="961"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i1/274607153/TB28NgklFXXXXXsXpXXXXXXXXXX_!!274607153.jpg" width="750" height="1268"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i1/274607153/TB2rU69lFXXXXaHXpXXXXXXXXXX_!!274607153.jpg" width="750" height="899"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i3/274607153/TB2NBLhlVXXXXaXXXXXXXXXXXXX_!!274607153.jpg" width="790" height="732"> <img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i4/274607153/TB2DscolFXXXXc.XXXXXXXXXXXX_!!274607153.jpg" width="750" height="461"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i1/274607153/TB26q.KlFXXXXawXXXXXXXXXXXX_!!274607153.jpg" width="750" height="1156"></p>';
        $data = [
                'goods_id' => 6,'user_id' => $this->user_id,'shop_id' => $this->shop_id,
                'goods_name' => 'jkjk夏季男士凉鞋真皮新款沙滩鞋男透气休闲男鞋罗马鞋男露趾凉拖','cat_id' => 3,'custom_cat_id' => 2,
                'slogan' => '全国包邮！ 头层牛皮！ 今日特惠！ 49元！','weight' => 500,'listorder' => 30,'description' => $description,
                'spec_val' => [],'products' => [],'goods_album' => $goods_album,'market_price' => 99,
                'sales_price' => 70,'stock' => 999,'freight_tpl_id' => 1 
        ];
        GoodsService::editGoods($data);
        $this->end();
    }
    
    /**
     * 修改单规格商品为多规格商品。
     */
    public function editSingleToMoreSpecAction() {
        $spec_val = [
                '颜色' => [
                        '银色','黑色','紫色' 
                ],'尺寸' => [
                        '35','38','39' 
                ] 
        ];
        $products = [
                '颜色:银色|尺寸:35' => [
                        'market_price' => 101,'sales_price' => 99,'stock' => '999' 
                ],'颜色:黑色|尺寸:35' => [
                        'market_price' => 129,'sales_price' => 99,'stock' => '999' 
                ],'颜色:紫色|尺寸:35' => [
                        'market_price' => 129,'sales_price' => 99,'stock' => '999' 
                ],'颜色:银色|尺寸:38' => [
                        'market_price' => 129,'sales_price' => 99,'stock' => '999' 
                ],'颜色:黑色|尺寸:38' => [
                        'market_price' => 129,'sales_price' => 99,'stock' => '999' 
                ],'颜色:紫色|尺寸:38' => [
                        'market_price' => 129,'sales_price' => 99,'stock' => '999' 
                ],'颜色:银色|尺寸:39' => [
                        'market_price' => 129,'sales_price' => 99,'stock' => '999' 
                ],'颜色:黑色|尺寸:39' => [
                        'market_price' => 129,'sales_price' => 99,'stock' => '999' 
                ],'颜色:紫色|尺寸:39' => [
                        'market_price' => 129,'sales_price' => 99,'stock' => '999' 
                ] 
        ];
        $goods_album = [
                'images/voucher/20160401/56fe70362ef7e.jpg','images/voucher/20160401/56fe705fd37a2.jpg',
                'images/voucher/20160401/56fe710513c9e.jpg','images/voucher/20160402/56fea2043dc01.jpg',
                'images/voucher/20160402/56fea3f18677d.jpg' 
        ];
        $description = '<p><img style="max-width: 750.0px;" src="https://img.alicdn.com/imgextra/i4/274607153/TB27f6AqpXXXXazXXXXXXXXXXXX_!!274607153.jpg" align="absmiddle"><img style="max-width: 750.0px;" src="https://img.alicdn.com/imgextra/i3/274607153/TB2SoG5qpXXXXaMXpXXXXXXXXXX_!!274607153.gif" align="absmiddle"><img style="max-width: 750.0px;" src="https://img.alicdn.com/imgextra/i2/274607153/TB2BkbyqpXXXXbGXXXXXXXXXXXX_!!274607153.jpg" align="absmiddle"><img style="max-width: 750.0px;" src="https://img.alicdn.com/imgextra/i2/274607153/TB2wVLeqpXXXXXsXpXXXXXXXXXX_!!274607153.gif" align="absmiddle"><img style="max-width: 750.0px;" src="https://img.alicdn.com/imgextra/i1/274607153/TB2MLvXqpXXXXX.XpXXXXXXXXXX_!!274607153.jpg" class="" align="absmiddle" width="750" height="736"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i4/274607153/TB2FOYomFXXXXawXXXXXXXXXXXX_!!274607153.jpg" width="750" height="752"> <img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i3/274607153/TB2jzMHlFXXXXaTXXXXXXXXXXXX_!!274607153.jpg" width="750" height="550"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i2/274607153/TB2FdQxlFXXXXbVXXXXXXXXXXXX_!!274607153.jpg" width="750" height="754"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i2/274607153/TB2hWItlFXXXXcAXXXXXXXXXXXX_!!274607153.jpg" width="750" height="895"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i2/274607153/TB2HmwclFXXXXamXpXXXXXXXXXX_!!274607153.jpg" width="750" height="824"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i2/274607153/TB2RocqlFXXXXcAXXXXXXXXXXXX_!!274607153.jpg" width="750" height="543"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i4/274607153/TB2QHZwlFXXXXbNXXXXXXXXXXXX_!!274607153.jpg" width="750" height="688"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i1/274607153/TB2D4MelFXXXXagXpXXXXXXXXXX_!!274607153.jpg" width="750" height="935"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i3/274607153/TB2GbIclFXXXXXNXpXXXXXXXXXX_!!274607153.jpg" width="750" height="741"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i1/274607153/TB2FkshlFXXXXXvXpXXXXXXXXXX_!!274607153.jpg" width="750" height="576"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i3/274607153/TB259gClFXXXXbuXXXXXXXXXXXX_!!274607153.jpg" width="750" height="633"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i2/274607153/TB2pXwdlFXXXXaoXpXXXXXXXXXX_!!274607153.jpg" width="750" height="700"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i1/274607153/TB29ucXlFXXXXaHXpXXXXXXXXXX_!!274607153.jpg" width="750" height="595"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i2/274607153/TB2JKz3lFXXXXbbXpXXXXXXXXXX_!!274607153.jpg" width="750" height="705"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i1/274607153/TB21wUKlFXXXXavXXXXXXXXXXXX_!!274607153.jpg" width="750" height="934"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i1/274607153/TB2MdT4lFXXXXb_XpXXXXXXXXXX_!!274607153.jpg" width="750" height="1256"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i1/274607153/TB2PCZnlFXXXXcCXXXXXXXXXXXX_!!274607153.jpg" width="750" height="961"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i1/274607153/TB28NgklFXXXXXsXpXXXXXXXXXX_!!274607153.jpg" width="750" height="1268"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i1/274607153/TB2rU69lFXXXXaHXpXXXXXXXXXX_!!274607153.jpg" width="750" height="899"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i3/274607153/TB2NBLhlVXXXXaXXXXXXXXXXXXX_!!274607153.jpg" width="790" height="732"> <img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i4/274607153/TB2DscolFXXXXc.XXXXXXXXXXXX_!!274607153.jpg" width="750" height="461"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i1/274607153/TB26q.KlFXXXXawXXXXXXXXXXXX_!!274607153.jpg" width="750" height="1156"></p>';
        $data = [
                'goods_id' => 6,'user_id' => $this->user_id,'shop_id' => $this->shop_id,
                'goods_name' => 'jkjk夏季男士凉鞋真皮新款沙滩鞋男透气休闲男鞋罗马鞋男露趾凉拖','cat_id' => 3,'custom_cat_id' => 2,
                'slogan' => '全国包邮！ 头层牛皮！ 今日特惠！ 49元！','weight' => 500,'listorder' => 30,'description' => $description,
                'spec_val' => $spec_val,'products' => $products,'goods_album' => $goods_album,'market_price' => '',
                'sales_price' => '','stock' => '','freight_tpl_id' => 1 
        ];
        GoodsService::editGoods($data);
        $this->end();
    }
    
    /**
     * 添加商品。
     */
    public function addGoodsAction() {
        $spec_val = [
                '颜色' => [
                        '银色','黑色' 
                ],'尺寸' => [
                        '35','38' 
                ] 
        ];
        $products = [
                '颜色:银色|尺寸:35' => [
                        'market_price' => 129,'sales_price' => 99,'stock' => '999' 
                ],'颜色:黑色|尺寸:35' => [
                        'market_price' => 129,'sales_price' => 99,'stock' => '999' 
                ],'颜色:银色|尺寸:38' => [
                        'market_price' => 129,'sales_price' => 99,'stock' => '999' 
                ],'颜色:黑色|尺寸:38' => [
                        'market_price' => 129,'sales_price' => 99,'stock' => '999' 
                ] 
        ];
        $goods_album = [
                'images/voucher/20160401/56fe70362ef7e.jpg','images/voucher/20160401/56fe705fd37a2.jpg',
                'images/voucher/20160401/56fe710513c9e.jpg','images/voucher/20160402/56fea2043dc01.jpg',
                'images/voucher/20160402/56fea3f18677d.jpg' 
        ];
        $description = '<p><img style="max-width: 750.0px;" src="https://img.alicdn.com/imgextra/i4/274607153/TB27f6AqpXXXXazXXXXXXXXXXXX_!!274607153.jpg" align="absmiddle"><img style="max-width: 750.0px;" src="https://img.alicdn.com/imgextra/i3/274607153/TB2SoG5qpXXXXaMXpXXXXXXXXXX_!!274607153.gif" align="absmiddle"><img style="max-width: 750.0px;" src="https://img.alicdn.com/imgextra/i2/274607153/TB2BkbyqpXXXXbGXXXXXXXXXXXX_!!274607153.jpg" align="absmiddle"><img style="max-width: 750.0px;" src="https://img.alicdn.com/imgextra/i2/274607153/TB2wVLeqpXXXXXsXpXXXXXXXXXX_!!274607153.gif" align="absmiddle"><img style="max-width: 750.0px;" src="https://img.alicdn.com/imgextra/i1/274607153/TB2MLvXqpXXXXX.XpXXXXXXXXXX_!!274607153.jpg" class="" align="absmiddle" width="750" height="736"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i4/274607153/TB2FOYomFXXXXawXXXXXXXXXXXX_!!274607153.jpg" width="750" height="752"> <img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i3/274607153/TB2jzMHlFXXXXaTXXXXXXXXXXXX_!!274607153.jpg" width="750" height="550"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i2/274607153/TB2FdQxlFXXXXbVXXXXXXXXXXXX_!!274607153.jpg" width="750" height="754"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i2/274607153/TB2hWItlFXXXXcAXXXXXXXXXXXX_!!274607153.jpg" width="750" height="895"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i2/274607153/TB2HmwclFXXXXamXpXXXXXXXXXX_!!274607153.jpg" width="750" height="824"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i2/274607153/TB2RocqlFXXXXcAXXXXXXXXXXXX_!!274607153.jpg" width="750" height="543"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i4/274607153/TB2QHZwlFXXXXbNXXXXXXXXXXXX_!!274607153.jpg" width="750" height="688"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i1/274607153/TB2D4MelFXXXXagXpXXXXXXXXXX_!!274607153.jpg" width="750" height="935"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i3/274607153/TB2GbIclFXXXXXNXpXXXXXXXXXX_!!274607153.jpg" width="750" height="741"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i1/274607153/TB2FkshlFXXXXXvXpXXXXXXXXXX_!!274607153.jpg" width="750" height="576"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i3/274607153/TB259gClFXXXXbuXXXXXXXXXXXX_!!274607153.jpg" width="750" height="633"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i2/274607153/TB2pXwdlFXXXXaoXpXXXXXXXXXX_!!274607153.jpg" width="750" height="700"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i1/274607153/TB29ucXlFXXXXaHXpXXXXXXXXXX_!!274607153.jpg" width="750" height="595"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i2/274607153/TB2JKz3lFXXXXbbXpXXXXXXXXXX_!!274607153.jpg" width="750" height="705"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i1/274607153/TB21wUKlFXXXXavXXXXXXXXXXXX_!!274607153.jpg" width="750" height="934"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i1/274607153/TB2MdT4lFXXXXb_XpXXXXXXXXXX_!!274607153.jpg" width="750" height="1256"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i1/274607153/TB2PCZnlFXXXXcCXXXXXXXXXXXX_!!274607153.jpg" width="750" height="961"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i1/274607153/TB28NgklFXXXXXsXpXXXXXXXXXX_!!274607153.jpg" width="750" height="1268"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i1/274607153/TB2rU69lFXXXXaHXpXXXXXXXXXX_!!274607153.jpg" width="750" height="899"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i3/274607153/TB2NBLhlVXXXXaXXXXXXXXXXXXX_!!274607153.jpg" width="790" height="732"> <img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i4/274607153/TB2DscolFXXXXc.XXXXXXXXXXXX_!!274607153.jpg" width="750" height="461"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i1/274607153/TB26q.KlFXXXXawXXXXXXXXXXXX_!!274607153.jpg" width="750" height="1156"></p>';
        $data = [
                'user_id' => $this->user_id,'shop_id' => $this->shop_id,
                'goods_name' => 'jkjk夏季男士凉鞋真皮新款沙滩鞋男透气休闲男鞋罗马鞋男露趾凉拖','cat_id' => 3,'custom_cat_id' => 2,
                'slogan' => '全国包邮！ 头层牛皮！ 今日特惠！ 49元！','weight' => 500,'listorder' => 30,'description' => $description,
                'spec_val' => $spec_val,'products' => $products,'goods_album' => $goods_album,'market_price' => '',
                'sales_price' => '','stock' => '','freight_tpl_id' => 1 
        ];
        GoodsService::addGoods($data);
        $this->end();
    }
    
    public function editGoodsAction() {
        $spec_val = [
                '颜色' => [
                        '银色','黑色','紫色' 
                ],'尺寸' => [
                        '35','38','39' 
                ] 
        ];
        $products = [
                '颜色:银色|尺寸:35' => [
                        'market_price' => 101,'sales_price' => 99,'stock' => '999' 
                ],'颜色:黑色|尺寸:35' => [
                        'market_price' => 129,'sales_price' => 99,'stock' => '999' 
                ],'颜色:紫色|尺寸:35' => [
                        'market_price' => 129,'sales_price' => 99,'stock' => '999' 
                ],'颜色:银色|尺寸:38' => [
                        'market_price' => 129,'sales_price' => 99,'stock' => '999' 
                ],'颜色:黑色|尺寸:38' => [
                        'market_price' => 129,'sales_price' => 99,'stock' => '999' 
                ],'颜色:紫色|尺寸:38' => [
                        'market_price' => 129,'sales_price' => 99,'stock' => '999' 
                ],'颜色:银色|尺寸:39' => [
                        'market_price' => 129,'sales_price' => 99,'stock' => '999' 
                ],'颜色:黑色|尺寸:39' => [
                        'market_price' => 129,'sales_price' => 99,'stock' => '999' 
                ],'颜色:紫色|尺寸:39' => [
                        'market_price' => 129,'sales_price' => 99,'stock' => '999' 
                ] 
        ];
        $goods_album = [
                'images/voucher/20160401/56fe70362ef7e.jpg','images/voucher/20160401/56fe705fd37a2.jpg',
                'images/voucher/20160401/56fe710513c9e.jpg','images/voucher/20160402/56fea2043dc01.jpg',
                'images/voucher/20160402/56fea3f18677d.jpg' 
        ];
        $description = '<p><img style="max-width: 750.0px;" src="https://img.alicdn.com/imgextra/i4/274607153/TB27f6AqpXXXXazXXXXXXXXXXXX_!!274607153.jpg" align="absmiddle"><img style="max-width: 750.0px;" src="https://img.alicdn.com/imgextra/i3/274607153/TB2SoG5qpXXXXaMXpXXXXXXXXXX_!!274607153.gif" align="absmiddle"><img style="max-width: 750.0px;" src="https://img.alicdn.com/imgextra/i2/274607153/TB2BkbyqpXXXXbGXXXXXXXXXXXX_!!274607153.jpg" align="absmiddle"><img style="max-width: 750.0px;" src="https://img.alicdn.com/imgextra/i2/274607153/TB2wVLeqpXXXXXsXpXXXXXXXXXX_!!274607153.gif" align="absmiddle"><img style="max-width: 750.0px;" src="https://img.alicdn.com/imgextra/i1/274607153/TB2MLvXqpXXXXX.XpXXXXXXXXXX_!!274607153.jpg" class="" align="absmiddle" width="750" height="736"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i4/274607153/TB2FOYomFXXXXawXXXXXXXXXXXX_!!274607153.jpg" width="750" height="752"> <img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i3/274607153/TB2jzMHlFXXXXaTXXXXXXXXXXXX_!!274607153.jpg" width="750" height="550"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i2/274607153/TB2FdQxlFXXXXbVXXXXXXXXXXXX_!!274607153.jpg" width="750" height="754"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i2/274607153/TB2hWItlFXXXXcAXXXXXXXXXXXX_!!274607153.jpg" width="750" height="895"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i2/274607153/TB2HmwclFXXXXamXpXXXXXXXXXX_!!274607153.jpg" width="750" height="824"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i2/274607153/TB2RocqlFXXXXcAXXXXXXXXXXXX_!!274607153.jpg" width="750" height="543"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i4/274607153/TB2QHZwlFXXXXbNXXXXXXXXXXXX_!!274607153.jpg" width="750" height="688"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i1/274607153/TB2D4MelFXXXXagXpXXXXXXXXXX_!!274607153.jpg" width="750" height="935"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i3/274607153/TB2GbIclFXXXXXNXpXXXXXXXXXX_!!274607153.jpg" width="750" height="741"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i1/274607153/TB2FkshlFXXXXXvXpXXXXXXXXXX_!!274607153.jpg" width="750" height="576"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i3/274607153/TB259gClFXXXXbuXXXXXXXXXXXX_!!274607153.jpg" width="750" height="633"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i2/274607153/TB2pXwdlFXXXXaoXpXXXXXXXXXX_!!274607153.jpg" width="750" height="700"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i1/274607153/TB29ucXlFXXXXaHXpXXXXXXXXXX_!!274607153.jpg" width="750" height="595"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i2/274607153/TB2JKz3lFXXXXbbXpXXXXXXXXXX_!!274607153.jpg" width="750" height="705"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i1/274607153/TB21wUKlFXXXXavXXXXXXXXXXXX_!!274607153.jpg" width="750" height="934"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i1/274607153/TB2MdT4lFXXXXb_XpXXXXXXXXXX_!!274607153.jpg" width="750" height="1256"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i1/274607153/TB2PCZnlFXXXXcCXXXXXXXXXXXX_!!274607153.jpg" width="750" height="961"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i1/274607153/TB28NgklFXXXXXsXpXXXXXXXXXX_!!274607153.jpg" width="750" height="1268"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i1/274607153/TB2rU69lFXXXXaHXpXXXXXXXXXX_!!274607153.jpg" width="750" height="899"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i3/274607153/TB2NBLhlVXXXXaXXXXXXXXXXXXX_!!274607153.jpg" width="790" height="732"> <img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i4/274607153/TB2DscolFXXXXc.XXXXXXXXXXXX_!!274607153.jpg" width="750" height="461"><img align="absmiddle" src="//img.alicdn.com/tps/i4/T10B2IXb4cXXcHmcPq-85-85.gif" class="" data-ks-lazyload="https://img.alicdn.com/imgextra/i1/274607153/TB26q.KlFXXXXawXXXXXXXXXXXX_!!274607153.jpg" width="750" height="1156"></p>';
        $data = [
                'goods_id' => 3,'user_id' => $this->user_id,'shop_id' => $this->shop_id,
                'goods_name' => 'jkjk夏季男士凉鞋真皮新款沙滩鞋男透气休闲男鞋罗马鞋男露趾凉拖','cat_id' => 3,'custom_cat_id' => 2,
                'slogan' => '全国包邮！ 头层牛皮！ 今日特惠！ 49元！','weight' => 500,'listorder' => 30,'description' => $description,
                'spec_val' => $spec_val,'products' => $products,'goods_album' => $goods_album,'market_price' => '',
                'sales_price' => '','stock' => '','freight_tpl_id' => 1 
        ];
        GoodsService::editGoods($data);
        $this->end();
    }
    
    /**
     * 获取商家自定义商品分类列表。
     */
    public function getCustomGoodsCatListAction() {
        $list = ShopService::getGoodsCategoryList($this->shop_id);
        print_r($list);
        $this->end();
    }
    
    /**
     * 添加自定义商品分类。
     */
    public function addCustomGoodsCatAction() {
        ShopService::addGoodsCategory($this->user_id, $this->shop_id, '五谷系列');
        $this->end();
    }
    
    /**
     * 修改自定义商品分类。
     */
    public function editCustomGoodsCatAction() {
        ShopService::editGoodsCategory($this->user_id, 3, $this->shop_id, '五谷系列1');
        $this->end();
    }
    
    /**
     * 删除自定义商品分类。
     */
    public function deleteCustomGoodsCatAction() {
        ShopService::deleteGoodsCategory($this->user_id, $this->shop_id, 3);
        $this->end();
    }
    
    /**
     * 文件上传。
     */
    public function uploadAction() {
        header("Access-Control-Allow-Origin: *");
        $result = UploadService::uploadImage(2, $this->user_id, 'goods', 2);
        $this->json(true, '上传成功', $result);
        $this->end();
    }
}