<?php
/**
 * 订单管理。
 * @author winerQin
 * @date 2016-06-07
 */

use services\OrderService;
use common\YCore;
use winer\Paginator;
use services\DistrictService;

class OrderController extends \common\controllers\Shop {
    
    /**
     * 订单列表。
     */
    public function listAction() {
        $receiver_mobile = $this->getString('receiver_mobile', '');
        $goods_name = $this->getString('goods_name', '');
        $receiver_name = $this->getString('receiver_name', '');
        $order_sn = $this->getString('order_sn', '');
        $order_status = $this->getInt('order_status', - 1);
        $start_time = $this->getString('start_time', '');
        $end_time = $this->getString('end_time', '');
        $page = $this->getInt(YCore::appconfig('pager'), 1);
        $list = OrderService::getShopOrderList($this->shop_id, $goods_name, $receiver_name, $receiver_mobile, $order_sn, $order_status, $start_time, $end_time, $page, 10);
        $paginator = new Paginator($list['total'], 10);
        $page_html = $paginator->shopPageShow();
        $this->_view->assign('page_html', $page_html);
        $this->_view->assign('list', $list['list']);
        $this->_view->assign('order_status', $order_status);
        $this->_view->assign('start_time', $start_time);
        $this->_view->assign('end_time', $end_time);
        $this->_view->assign('order_sn', $order_sn);
        $this->_view->assign('receiver_name', $receiver_name);
        $this->_view->assign('goods_name', $goods_name);
        $this->_view->assign('receiver_mobile', $receiver_mobile);
    }
    
    /**
     * 关闭订单。
     */
    public function closeAction() {
        if ($this->_request->isXmlHttpRequest()) {
            $order_id = $this->getInt('order_id');
            $status = OrderService::closeOrder($this->user_id, $this->shop_id, $order_id);
            $this->json($status, '操作成功');
        }
    }
    
    /**
     * 发货。
     */
    public function deliverAction() {
        if ($this->_request->isXmlHttpRequest()) {
            $order_id = $this->getInt('order_id');
            $logistics_code = $this->getString('logistics_code');
            $logistics_number = $this->getString('logistics_number');
            OrderService::deliverGoods($this->user_id, $this->shop_id, $order_id, $logistics_code, $logistics_number);
            $this->json(true, '发货成功');
        }
        $order_id = $this->getInt('order_id');
        $order_detail = OrderService::getShopOrderDetail($this->shop_id, $order_id);
        $logistics_list_dict = YCore::dict('logistics_list');
        $this->_view->assign('logistics_list_dict', $logistics_list_dict);
        $this->_view->assign('order_id', $order_id);
        $this->_view->assign('logistics_code', $order_detail['logistics_code']);
        $this->_view->assign('logistics_number', $order_detail['logistics_number']);
    }
    
    /**
     * 修正收货地址。
     * -- 1、用户选错收货地址。未发货之前都可以修改（只需商家确认即可）。
     */
    public function adjustAddressAction() {
        if ($this->_request->isXmlHttpRequest()) {
            $order_id = $this->getInt('order_id');
            $district_id = $this->getInt('district_id');
            $receiver_name = $this->getString('receiver_name');
            $receiver_address = $this->getString('receiver_address');
            $receiver_mobile = $this->getString('receiver_mobile');
            $receiver_zip = $this->getString('receiver_zip');
            OrderService::adjustAddress($this->user_id, $this->shop_id, $order_id, $district_id, $receiver_name, $receiver_address, $receiver_mobile, $receiver_zip);
            $this->json(true, '修改成功');
        }
        $order_id = $this->getInt('order_id');
        $order_detail = OrderService::getShopOrderDetail($this->shop_id, $order_id);
        $province_id = DistrictService::getByDistrictOfName(1, $order_detail['receiver_province']);
        $city_id = DistrictService::getByDistrictOfName(2, $order_detail['receiver_city']);
        $district_id = DistrictService::getByDistrictOfName(3, $order_detail['receiver_district']);
        $this->_view->assign('province_id', $province_id);
        $this->_view->assign('city_id', $city_id);
        $this->_view->assign('district_id', $district_id);
        $this->_view->assign('order_id', $order_id);
        $this->_view->assign('receiver_name', $order_detail['receiver_name']);
        $this->_view->assign('receiver_address', $order_detail['receiver_address']);
        $this->_view->assign('receiver_mobile', $order_detail['receiver_mobile']);
        $this->_view->assign('receiver_zip', $order_detail['receiver_zip']);
    }
    
    /**
     * 订单调价。
     * -- 1、拍下减价。有时候商家做的优惠手段。
     * -- 2、订单价格只能往下调。不能往上调。
     */
    public function adjustPriceAction() {
        if ($this->_request->isXmlHttpRequest()) {
            $order_id = $this->getInt('order_id');
            $product_id = $this->getInt('product_id');
            $price = $this->getFloat('price');
            OrderService::editOrderGoodsPrice($this->user_id, $this->shop_id, $order_id, $product_id, $price);
            $this->json(true, '改价成功');
        }
        $order_id = $this->getInt('order_id');
        $product_id = $this->getInt('product_id');
        $old_price = $this->getFloat('old_price');
        $this->_view->assign('order_id', $order_id);
        $this->_view->assign('product_id', $product_id);
        $this->_view->assign('old_price', $old_price);
    }
}