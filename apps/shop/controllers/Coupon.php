<?php
/**
 * 优惠券管理。
 * @author winerQin
 * @date 2016-06-07
 */

use services\CouponService;
use common\YCore;
use winer\Paginator;

class CouponController extends \common\controllers\Shop {
    
    /**
     * 优惠券列表。
     */
    public function listAction() {
        $status = $this->getInt('status', - 1);
        $page = $this->getInt(YCore::appconfig('pager'), 1);
        $list = CouponService::getShopCouponList($this->shop_id, $status, $page, 20);
        $paginator = new Paginator($list['total'], 20);
        $page_html = $paginator->shopPageShow();
        $this->_view->assign('page_html', $page_html);
        $this->_view->assign('list', $list['list']);
        $this->_view->assign('status', $status);
    }
    
    /**
     * 添加优惠券。
     */
    public function addAction() {
        if ($this->_request->isXmlHttpRequest()) {
            $get_start_time = $this->getString('get_start_time');
            $get_end_time = $this->getString('get_end_time');
            $coupon_name = $this->getString('coupon_name');
            $money = $this->getString('money');
            $order_money = $this->getString('order_money');
            $expiry_date = $this->getString('expiry_date');
            $limit_quantity = $this->getInt('limit_quantity', 1);
            CouponService::addCoupon($this->user_id, $this->shop_id, $get_start_time, $get_end_time, $limit_quantity, $coupon_name, $money, $order_money, $expiry_date);
            $this->json(true, '商品成功');
        }
    }
    
    /**
     * 编辑优惠券。
     */
    public function editAction() {
        if ($this->_request->isXmlHttpRequest()) {
            $coupon_id = $this->getInt('coupon_id');
            $get_start_time = $this->getString('get_start_time');
            $get_end_time = $this->getString('get_end_time');
            $coupon_name = $this->getString('coupon_name');
            $money = $this->getInt('money');
            $order_money = $this->getInt('order_money');
            $expiry_date = $this->getString('expiry_date');
            $limit_quantity = $this->getInt('limit_quantity');
            CouponService::editCoupon($this->user_id, $this->shop_id, $coupon_id, $get_start_time, $get_end_time, $limit_quantity, $coupon_name, $money, $order_money, $expiry_date);
            $this->json(true, '商品成功');
        }
        $coupon_id = $this->getInt('coupon_id');
        $detail = CouponService::getCouponDetail($this->shop_id, $coupon_id);
        $this->_view->assign('detail', $detail);
    }
    
    /**
     * 删除优惠券。
     */
    public function deleteAction() {
        $coupon_id = $this->getInt('coupon_id');
        $ok = CouponService::deleteCoupon($this->user_id, $this->shop_id, $coupon_id);
        $this->json($ok, '删除成功');
    }
}