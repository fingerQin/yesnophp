<?php
/**
 * 商家管理。
 * @author winerQin
 * @date 2016-06-10
 */

use common\YCore;
use winer\Paginator;
use services\ShopService;

class ShopController extends \common\controllers\Admin {

    /**
     * 商家列表。
     */
    public function listAction() {
        $shop_name = $this->getString('shop_name', '');
        $cat_id    = $this->getString('cat_id', -1);
        $page      = $this->getInt(YCore::appconfig('pager'), 1);
        $list      = ShopService::getShopList($shop_name, $page, 10);
        $paginator = new Paginator($list['total'], 10);
        $page_html = $paginator->backendPageShow();
        $this->_view->assign('page_html', $page_html);
        $this->_view->assign('shop_name', $shop_name);
        $this->_view->assign('list', $list['list']);
    }

    /**
     * 商家添加。
     */
    public function addAction() {
        if ($this->_request->isXmlHttpRequest()) {
        	$account     = $this->getString('account', '');
            $shop_name   = $this->getString('shop_name');
            $shop_logo   = $this->getString('shop_logo');
            $shop_desc   = $this->getString('shop_desc');
            $shop_notice = $this->getString('shop_notice');
            $link_man    = $this->getString('link_man');
            $mobilephone = $this->getString('mobilephone');
            $telephone   = $this->getString('telephone');
            $qq          = $this->getString('qq');
            $max_goods_count = $this->getInt('max_goods_count');
            $is_allow_delete_comment = $this->getInt('is_allow_delete_comment');
            $is_lock = $this->getInt('is_lock');
            $status = ShopService::addShop($this->admin_id, $shop_name, $shop_logo, $shop_desc, $shop_notice, $link_man, $mobilephone, $telephone, $qq, $max_goods_count, $is_allow_delete_comment, $is_lock, $account);
            if ($status) {
                $this->json($status, '添加成功');
            } else {
                $this->json($status, '添加失败');
            }
        }
    }

    /**
     * 商家编辑。
     */
    public function editAction() {
        if ($this->_request->isXmlHttpRequest()) {
        	$account     = $this->getString('account', '');
            $shop_id     = $this->getString('shop_id');
            $shop_name   = $this->getString('shop_name');
            $shop_logo   = $this->getString('shop_logo');
            $shop_desc   = $this->getString('shop_desc');
            $shop_notice = $this->getString('shop_notice');
            $link_man    = $this->getString('link_man');
            $mobilephone = $this->getString('mobilephone');
            $telephone   = $this->getString('telephone');
            $qq          = $this->getString('qq');
            $max_goods_count = $this->getString('max_goods_count');
            $is_allow_delete_comment = $this->getString('is_allow_delete_comment');
            $is_lock = $this->getString('is_lock');
            $status = ShopService::editShop($this->admin_id, $shop_id, $shop_name, $shop_logo, $shop_desc, $shop_notice, $link_man, $mobilephone, $telephone, $qq, $max_goods_count, $is_allow_delete_comment, $is_lock, $account);
            if ($status) {
                $this->json($status, '修改成功');
            } else {
                $this->json($status, '修改失败');
            }
        }
        $shop_id = $this->getInt('shop_id');
        $detail = ShopService::getShopDetail($shop_id);
        $this->_view->assign('detail', $detail);
    }

    /**
     * 商家删除。
     */
    public function deleteAction() {
        $shop_id = $this->getInt('shop_id');
        $status = ShopService::deleteShop($this->admin_id, $shop_id);
        if ($status) {
            $this->json($status, '删除成功');
        } else {
            $this->json($status, '删除失败');
        }
    }
}