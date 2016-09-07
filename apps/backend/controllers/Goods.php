<?php
/**
 * 商品管理。
 * @author winerQin
 * @date 2016-06-11
 */

use common\YCore;
use winer\Paginator;
use services\GoodsService;

class GoodsController extends \common\controllers\Admin {
    
    /**
     * 商品列表。
     */
    public function listAction() {
        $shop_id = $this->getInt('shop_id', - 1);
        $cat_id = $this->getString('cat_id', - 1);
        $page = $this->getInt(YCore::appconfig('pager'), 1);
        $updown = $this->getInt('updown', - 1);
        $goods_name = $this->getString('goods_name', '');
        $start_price = $this->getString('start_price', '');
        $end_price = $this->getString('end_price', '');
        $delted_show = $this->getInt('is_delete_show', 0);
        $list = GoodsService::getBackendGoodsList($shop_id, $updown, $goods_name, $cat_id, $start_price, $end_price, $delted_show, $page, 10);
        $paginator = new Paginator($list['total'], 10);
        $page_html = $paginator->backendPageShow();
        $this->_view->assign('page_html', $page_html);
        $this->_view->assign('shop_id', $shop_id);
        $this->_view->assign('updown', $updown);
        $this->_view->assign('goods_name', $goods_name);
        $this->_view->assign('start_price', $start_price);
        $this->_view->assign('end_price', $end_price);
        $this->_view->assign('cat_id', $cat_id);
        $this->_view->assign('is_delete_show', $delted_show);
        $this->_view->assign('list', $list['list']);
    }
    
    /**
     * 商品删除。
     */
    public function deleteAction() {
        $goods_id = $this->getInt('goods_id');
        $status = GoodsService::deleteGoods($this->admin_id, $goods_id);
        if ($status) {
            $this->json($status, '删除成功');
        } else {
            $this->json($status, '删除失败');
        }
    }
}
