<?php
/**
 * 商品管理。
 * @author winerQin
 * @date 2016-06-07
 */

use services\GoodsService;
use winer\Paginator;
use common\YCore;
use services\ShopService;
use services\CategoryService;

class GoodsController extends \common\controllers\Shop {
    
    /**
     * 出售中的商品。
     */
    public function listAction() {
        $goods_name = $this->getString('goods_name', '');
        $cat_id = $this->getInt('cat_id', - 1);
        $updown = $this->getInt('updown', - 1);
        $custom_cat_id = $this->getInt('custom_cat_id', - 1);
        $start_price = $this->getString('start_price', '');
        $end_price = $this->getString('end_price', '');
        $page = $this->getInt(YCore::appconfig('pager'), 1);
        $list = GoodsService::getShopGoodsList($this->shop_id, $updown, $goods_name, $cat_id, $custom_cat_id, $start_price, $end_price, $page, 20);
        $paginator = new Paginator($list['total'], 20);
        $page_html = $paginator->shopPageShow();
        $this->_view->assign('page_html', $page_html);
        $this->_view->assign('list', $list['list']);
        $this->_view->assign('goods_name', $goods_name);
        $this->_view->assign('cat_id', $cat_id);
        $this->_view->assign('updown', $updown);
        $this->_view->assign('custom_cat_id', $custom_cat_id);
        $this->_view->assign('start_price', $start_price);
        $this->_view->assign('end_price', $end_price);
    }
    
    /**
     * 发布商品。
     */
    public function publishAction() {
        if ($this->_request->isXmlHttpRequest()) {
            $data = [
                    'user_id' => $this->user_id,'shop_id' => $this->shop_id,
                    'goods_name' => $this->getString('goods_name'),'cat_id' => $this->getInt('cat_id'),
                    'custom_cat_id' => $this->getInt('custom_cat_id', - 1),'slogan' => $this->getString('slogan'),
                    'weight' => $this->getInt('weight'),'listorder' => $this->getInt('listorder'),
                    'description' => $this->getString('description'),'spec_val' => $this->getArray('spec_val', []),
                    'products' => $this->getArray('products', []),'goods_album' => $this->getArray('goods_album', []),
                    'market_price' => $this->getFloat('market_price', '0.00'),
                    'sales_price' => $this->getFloat('sales_price', '0.00'),'stock' => $this->getInt('stock', 0) 
            ];
            GoodsService::addGoods($data);
            $this->json(true, '商品添加成功');
        }
        $custom_goods_cat_list = ShopService::getGoodsCategoryList($this->shop_id);
        $this->_view->assign('custom_goods_cat_list', $custom_goods_cat_list);
        $system_goods_cat_list = CategoryService::getCategoryList(0, CategoryService::CAT_GOODS, true);
        $this->_view->assign('system_goods_cat_list', $system_goods_cat_list);
    }
    
    /**
     * 商品编辑。
     */
    public function editAction() {
        if ($this->_request->isXmlHttpRequest()) {
            $data = [
                    'goods_id' => $this->getInt('goods_id'),'user_id' => $this->user_id,
                    'shop_id' => $this->shop_id,'goods_name' => $this->getString('goods_name'),
                    'cat_id' => $this->getInt('cat_id'),'custom_cat_id' => $this->getInt('custom_cat_id'),
                    'slogan' => $this->getString('slogan'),'weight' => $this->getInt('weight'),
                    'listorder' => $this->getInt('listorder'),'description' => $this->getString('description'),
                    'spec_val' => $this->getArray('spec_val', []),'products' => $this->getArray('products', []),
                    'goods_album' => $this->getArray('goods_album', []),
                    'market_price' => $this->getFloat('market_price', '0.00'),
                    'sales_price' => $this->getFloat('sales_price', '0.00'),'stock' => $this->getInt('stock', 0) 
            ];
            GoodsService::editGoods($data);
            $this->json(true, '商品编辑成功');
        }
    }
    
    /**
     * 商品删除。
     */
    public function deleteAction() {
        $goods_id = $this->getInt('goods_id');
        $ok = GoodsService::deleteGoods($this->user_id, $goods_id);
        $this->json(true, '删除成功');
    }
    
    /**
     * 商品上下架。
     */
    public function updownAction() {
        $goods_id = $this->getInt('goods_id');
        $updown = $this->getInt('updown');
        $ok = GoodsService::updownGoods($this->user_id, $goods_id, $updown);
        $this->json(true, '操作成功');
    }
}