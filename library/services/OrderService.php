<?php
/**
 * 订单业务封装。
 * @author winerQin
 * @date 2016-04-08
 */
namespace services;

use common\YCore;
use winer\Validator;
use models\DbBase;
use models\MallOrder;
use models\MallOrderItem;
use models\MallGoods;
use models\MallProduct;
use models\MallOrderLog;
use common\YUrl;
use models\MallLogistics;
use models\District;
use models\MallCoupon;

class OrderService extends BaseService {

    /**
     * 是否需要发票的状态值。
     *
     * @var array
     */
    protected static $arr_need_invoice = [
        0 => '不需要',
        1 => '需要'
    ];

    /**
     * 发票类型的状态值。
     *
     * @var array
     */
    protected static $arr_invoice_type = [
        1 => '个人',
        2 => '单位'
    ];

    const ORDER_STATUS_WAIT_PAY = 0;
    // 待付款。
    const ORDER_STATUS_PAY_OK = 1;
    // 已付款。
    const ORDER_STATUS_DELIVER = 2;
    // 已发货。
    const ORDER_STATUS_SUCCESS = 3;
    // 交易成功。
    const ORDER_STATUS_CLOSED = 4;
    // 交易关闭。
    const ORDER_STATUS_CANCELED = 5;
    // 已取消。
    /**
     * 管理后台获取订单列表。
     *
     * @param string $goods_id 商品ID。
     * @param string $receiver_name 收货人姓名。
     * @param string $receiver_mobile 收货人手机。
     * @param string $order_sn 订单号。
     * @param number $order_status 订单状态。
     * @param string $start_time 成交时间开始。
     * @param string $end_time 成交时间结束。
     * @param number $page 当前页码。
     * @param number $count 每页显示条数。
     * @return array
     */
    public static function getBackendOrderList($goods_id = -1, $receiver_name = '', $receiver_mobile = '', $order_sn = '', $order_status = -1, $start_time = '', $end_time = '', $page = 1, $count = 20) {
        $offset = self::getPaginationOffset($page, $count);
        $from_table = ' FROM mall_order ';
        $columns = ' * ';
        $where = ' WHERE status = :status ';
        $params = [
            ':status' => 1
        ];
        if (strlen($receiver_mobile) > 0) {
            $where .= ' AND receiver_mobile = :receiver_mobile ';
            $params[':receiver_mobile'] = $receiver_mobile;
        }
        if (strlen($receiver_name) > 0) {
            $where .= ' AND receiver_name = :receiver_name ';
            $params[':receiver_name'] = $receiver_name;
        }
        if (strlen($order_sn) > 0) {
            $where .= ' AND order_sn = :order_sn ';
            $params[':order_sn'] = $order_sn;
        }
        if ($order_status != -1) {
            $where .= ' AND order_status = :order_status ';
            $params[':order_status'] = $order_status;
        }
        if (strlen($start_time) > 0) {
            if (!Validator::is_date($start_time)) {
                YCore::exception(-1, '成交时间格式不正确');
            }
            $where .= ' AND created_time >= :start_time ';
            $params[':start_time'] = strtotime($start_time);
        }
        if (strlen($end_time) > 0) {
            if (!Validator::is_date($end_time)) {
                YCore::exception(-1, '成交时间格式不正确');
            }
            $where .= ' AND created_time <= :end_time ';
            $params[':end_time'] = strtotime($end_time);
        }
        $order_by = ' ORDER BY order_id DESC ';
        $sql = "SELECT COUNT(1) AS count {$from_table} {$where}";
        $default_db = new DbBase();
        $count_data = $default_db->rawQuery($sql, $params)->rawFetchOne();
        $total = $count_data ? $count_data['count'] : 0;
        $sql = "SELECT {$columns} {$from_table} {$where} {$order_by} LIMIT {$offset},{$count}";
        $list = $default_db->rawQuery($sql, $params)->rawFetchAll();
        foreach ($list as $key => $item) {
            $item['goods_list']    = self::getOrderItems($item['order_id']);
            $item['modified_time'] = YCore::format_timestamp($item['modified_time']);
            $item['created_time']  = YCore::format_timestamp($item['created_time']);
            $item['pay_time']      = YCore::format_timestamp($item['pay_time']);
            $item['shipping_time'] = YCore::format_timestamp($item['shipping_time']);
            $item['done_time']     = YCore::format_timestamp($item['done_time']);
            $item['closed_time']   = YCore::format_timestamp($item['closed_time']);
            $item['cancel_time']   = YCore::format_timestamp($item['cancel_time']);
            $list[$key] = $item;
        }
        $result = [
            'list'   => $list,
            'total'  => $total,
            'page'   => $page,
            'count'  => $count,
            'isnext' => self::IsHasNextPage($total, $page, $count)
        ];
        return $result;
    }

    /**
     * 用户订单列表。
     *
     * @param number $user_id 用户ID.
     * @param string $order_sn 订单号。
     * @param number $order_status 订单状态。
     * @param string $start_time 成交时间开始。
     * @param string $end_time 成效时间结束。
     * @param number $page 当前页码。
     * @param number $count 每页显示条数。
     * @return array
     */
    public static function getUserOrderList($user_id, $order_sn = '', $order_status = -1, $start_time = '', $end_time = '', $page = 1, $count = 20) {
        $offset = self::getPaginationOffset($page, $count);
        $from_table = ' FROM ms_order ';
        $columns = ' * ';
        $where   = ' WHERE user_id = :user_id AND status = :status ';
        $params  = [
            ':user_id' => $user_id,
            ':status'  => 1
        ];
        if (strlen($order_sn) > 0) {
            $where .= ' AND order_sn = :order_sn ';
            $params[':order_sn'] = $order_sn;
        }
        if ($order_status != -1) {
            $where .= ' AND order_status = :order_status ';
            $params[':order_status'] = $order_status;
        }
        if (strlen($start_time) > 0) {
            if (!Validator::is_date($start_time)) {
                YCore::exception(-1, '成交时间格式不正确');
            }
            $where .= ' AND created_time >= :start_time ';
            $params[':start_time'] = strtotime($start_time);
        }
        if (strlen($end_time) > 0) {
            if (!Validator::is_date($end_time)) {
                YCore::exception(-1, '成交时间格式不正确');
            }
            $where .= ' AND created_time <= :end_time ';
            $params[':end_time'] = strtotime($end_time);
        }
        $order_by = ' ORDER BY order_id DESC ';
        $sql = "SELECT COUNT(1) AS count {$from_table} {$where}";
        $default_db = new DbBase();
        $count_data = $default_db->rawQuery($sql, $params)->rawFetchOne();
        $total = $count_data ? $count_data['count'] : 0;
        $sql   = "SELECT {$columns} {$from_table} {$where} {$order_by} LIMIT {$offset},{$count}";
        $list  = $default_db->rawQuery($sql, $params)->rawFetchAll();
        foreach ($list as $key => $item) {
            $item['goods_list'] = self::getOrderItems($item['order_id']);
            $list[$key] = $item;
        }
        $result = [
            'list'   => $list,
            'total'  => $total,
            'page'   => $page,
            'count'  => $count,
            'isnext' => self::IsHasNextPage($total, $page, $count)
        ];
        return $result;
    }

    /**
     * 获取订单购买的商品明细。
     *
     * @param number $order_id 订单ID。
     * @return array
     */
    protected static function getOrderItems($order_id) {
        $default_db = new DbBase();
        $columns = 'goods_id,goods_name,goods_image,product_id,spec_val,market_price,sales_price,'
                 . 'quantity,payment_price,total_price,refund_status,reply_status,refund_status';
        $sql = "SELECT {$columns} FROM mall_order_item WHERE order_id = :order_id ORDER BY sub_order_id ASC";
        $params = [
            ':order_id' => $order_id
        ];
        $items = $default_db->rawQuery($sql, $params)->rawFetchAll();
        foreach ($items as $k => $item) {
            $item['goods_image'] = YUrl::filePath($item['goods_image']);
            $items[$k] = $item;
        }
        return $items;
    }

    /**
     * 获取单个子订单详情。
     *
     * @param number $sub_order_id 子订单ID。
     * @return array
     */
    public static function getOrderItem($sub_order_id) {
        $default_db = new DbBase();
        $columns = 'goods_id,goods_name,goods_image,product_id,spec_val,market_price,sales_price,'
                 . 'quantity,payment_price,total_price,refund_status,reply_status,refund_status';
        $sql = "SELECT {$columns} FROM mall_order_item WHERE sub_order_id = :sub_order_id";
        $params = [
            ':sub_order_id' => $sub_order_id
        ];
        $data = $default_db->rawQuery($sql, $params)->rawFetchOne();
        $data['goods_image'] = YUrl::filePath($data['goods_image']);
        return $data;
    }

    /**
     * 获取卖家用户订单详情。
     *
     * @param number $order_id 订单ID。
     * @return array
     */
    public static function getShopOrderDetail($order_id) {
        $where = [
            'order_id' => $order_id,
            'status'   => 1
        ];
        $order_model = new MallOrder();
        $order_detail = $order_model->fetchOne([], $where);
        if (empty($order_detail)) {
            YCore::exception(-1, '订单不存在');
        }
        $order_detail['goods_list'] = self::getOrderItems($order_id);
        $logistics_info = self::getOrderExpressInfo($order_id);
        $order_detail = array_merge($order_detail, $logistics_info);
        return $order_detail;
    }

    /**
     * 获取买家用户订单详情。
     *
     * @param number $user_id 用户ID。
     * @param number $order_id 订单ID。
     * @return array
     */
    public static function getUserOrderDetail($user_id, $order_id) {
        $where = [
            'order_id' => $order_id,
            'user_id'  => $user_id,
            'status'   => 1
        ];
        $order_model = new MallOrder();
        $order_detail = $order_model->fetchOne([], $where);
        if (empty($order_detail)) {
            YCore::exception(-1, '订单不存在');
        }
        $order_detail['goods_list'] = self::getOrderItems($order_id);
        $logistics_info = self::getOrderExpressInfo($order_id);
        $order_detail = array_merge($order_detail, $logistics_info);
        return $order_detail;
    }

    /**
     * 用户提交订单。
     * -- Example start --
     * $data = [
     *      'user_id'          => '用户ID',
     *      'goods_list'       => '商品列表',
     *      'address_id'       => '收货地址ID。如果是临时购买。则此值填写-1',
     *      'need_invoice'     => '是否需要发票：0不需要、1需要',
     *      'invoice_type'     => '发票类型：1个人、2单位',
     *      'invoice_name'     => '发票抬头',
     *      'buyer_message'    => '买家留言。100这个字符。',
     *      'new_address_info' => '新的收货地址。如果address_id不等于-1,则此值有没有设置都无效。',
     *      'is_exchange'      => '是否积分兑换。1是、0否。使用积分兑换的时候不能享受优惠券优惠。',
     *      'user_coupon_id'   => '优惠券ID。使用积分兑换的时候不能享受优惠券优惠。',
     * ];
     *
     * $new_address_info = [
     *      'realname'    => '收货人真实姓名',
     *      'district_id' => '区县或街道ID',
     *      'zipcode'     => '邮政编码',
     *      'mobilephone' => '手机号码',
     *      'address'     => '收货详细地址。除省市区街道外的部分地址信息。',
     * ];
     *
     * $goods_list = [
     *  [
     *      'goods_id' => '商品ID',
     *      'product_id' => '货品ID',
     *      'quantity' => '购买数量',
     *  ],
     *  [
     *      'goods_id' => '商品ID',
     *      'product_id' => '货品ID',
     *      'quantity' => '购买数量',
     *  ],
     *  ......
     * ];
     * -- Example end --
     *
     * @param array $data 订单数据。
     * @return array 订单ID组成的数组。用户购买的商品属于多家店的时候，才会出现多个订单ID。
     */
    public static function submitOrder($data) {
        if (empty($data)) {
            YCore::exception(-1, '购买信息有误');
        }
        $data['is_exchange'] = isset($data['is_exchange']) ?: 0;
        $data['user_coupon_id'] = isset($data['user_coupon_id']) ?: 0;
        if (!isset($data['goods_list']) || empty($data['goods_list'])) {
            YCore::exception(-1, '没有购买任何宝贝');
        }
        if (!isset($data['buyer_message']) || mb_strlen($data['buyer_message'], 'UTF-8') > 50) {
            YCore::exception(-1, '买家留言长度最大50个字符');
        }
        if (!isset($data['need_invoice']) || !in_array($data['need_invoice'], self::$arr_need_invoice)) {
            YCore::exception(-1, '是否需要发票选择有误');
        }
        if ($data['need_invoice'] == 1) {
            if (!isset($data['invoice_type']) || !in_array($data['invoice_type'], self::$arr_invoice_type)) {
                YCore::exception(-1, '发票类型选择有误');
            }
            if ($data['invoice_type'] == 2 && !Validator::is_len($data['invoice_name'], 1, 50, true)) {
                YCore::exception(-1, '公司发票抬头必须1~30个字符');
            }
        } else { // 不需要发票清理这两个值。
            $data['invoice_type'] = 1;
            $data['invoice_name'] = '';
        }
        if (!isset($data['address_id'])) {
            YCore::exception(-1, '收货地址有误');
        }
        if ($data['address_id'] == -1 && (!isset($data['new_address_info']) || !is_array($data['new_address_info']))) {
            YCore::exception(-1, '新添加的收货地址有误');
        }
        if (count($data['goods_list']) > 50) {
            YCore::exception(-1, '一次最多只允许购买50个宝贝');
        }
        if ($data['is_exchange'] == 1 && $data['user_coupon_id'] == 1) {
            YCore::exception(-1, '积分兑换不能与优惠券同时使用');
        }
        $address = [
            'user_id'    => $data['user_id'],
            'address_id' => $data['address_id']
        ];
        $address = array_merge($address, $data['new_address_info']);
        $address_info = UserAddressService::getSubmitUserAddressDetail($address);
        // 准备订单需要的信息。
        $order_data = [
            'user_id'       => $data['user_id'],
            'need_invoice'  => $data['need_invoice'],
            'invoice_type'  => $data['invoice_type'],
            'invoice_name'  => $data['invoice_name'],
            'buyer_message' => $data['buyer_message']
        ];
        // 合并地址信息。
        $order_data = array_merge($order_data, $address_info);
        // 准备开启事务。
        $default_db = new DbBase();
        $default_db->beginTransaction();
        // 以商家级别循环提交订单。
        $order_data['goods_list']     = $data['goods_list'];
        $order_data['user_coupon_id'] = $data['user_coupon_id'];
        $order_data['is_exchange']    = $data['is_exchange'];
        try {
            $order_id = self::submitShopOrder($order_data);
        } catch (\Exception $e) {
            $default_db->rollBack();
            YCore::exception($e->getCode(), $e->getMessage());
        }
        $default_db->commit();
        return $order_id;
    }

    /**
     * 用户下单。
     * -- Example start --
     * $data = [
     *      'user_id'        => '用户ID',
     *      'goods_list'     => '商品列表',
     *      'need_invoice'   => '是否需要发票：0不需要、1需要',
     *      'invoice_type'   => '发票类型：1个人、2单位',
     *      'invoice_name'   => '发票抬头',
     *      'buyer_message'  => '买家留言。100这个字符。',
     *      'realname'       => '收货人真实姓名',
     *      'zipcode'        => '邮政编码',
     *      'mobilephone'    => '收货人手机号码',
     *      'address'        => '收货人详细地址',
     *      'province_name'  => '省名称',
     *      'city_name'      => '市名称',
     *      'district_name'  => '区县名称',
     *      'is_exchange'    => '是否积分兑换。1是、0否。使用积分兑换的时候不能享受优惠券优惠。',
     *      'user_coupon_id' => '优惠券ID。使用积分兑换的时候不能享受优惠券优惠。',
     * ];
     *
     * $goods_list = [
     *  [
     *      'goods_id' => '商品ID',
     *      'product_id' => '货品ID',
     *      'quantity' => '购买数量',
     *  ],
     *  [
     *      'goods_id' => '商品ID',
     *      'product_id' => '货品ID',
     *      'quantity' => '购买数量',
     *  ],
     *  ......
     * ];
     * -- Example end --
     *
     * @param array $data 订单信息。
     * @return boolean
     */
    protected static function submitShopOrder($data) {
        $user_coupon_model = new MallCoupon();
        $coupon_money       = 0; // 优惠券金额。
        $coupon_order_money = 0; // 使用优惠券的订单金额。
        if ($data['user_coupon_id'] > 0) {
            $where = [
                'user_id' => $data['user_id'],
                'id'      => $data['user_coupon_id']
            ];
            $user_coupon_info = $user_coupon_model->fetchOne([], $where);
            if (empty($user_coupon_info)) {
                YCore::exception(-1, '优惠券数据异常');
            }
            if ($user_coupon_info['is_use'] == 1) {
                YCore::exception(-1, '优惠券已经使用');
            }
        }
        $insert_data = [
            'user_id'           => $data['user_id'],
            'order_sn'          => self::getOrderSn($data['user_id'], 'SN'),
            'total_price'       => 0,
            'payment_price'     => 0,
            'pay_status'        => 0,
            'order_status'      => 0,
            'need_invoice'      => $data['need_invoice'],
            'invoice_type'      => $data['invoice_type'],
            'invoice_name'      => $data['invoice_name'],
            'receiver_name'     => $data['realname'],
            'receiver_province' => $data['province_name'],
            'receiver_city'     => $data['city_name'],
            'receiver_district' => $data['district_name'],
            'receiver_street'   => '', // 暂时不支持四级地址。
            'receiver_address'  => $data['address'],
            'receiver_zip'      => $data['zipcode'],
            'receiver_mobile'   => $data['mobilephone'],
            'buyer_message'     => $data['buyer_message'],
            'created_time'      => $_SERVER['REQUEST_TIME'],
            'created_by'        => $data['user_id'],
            'user_coupon_id'    => $data['user_coupon_id'],
            'user_coupon_money' => $coupon_money,
            'jifen_pay'         => 0,
            'status'            => 1
        ];
        $order_model = new MallOrder();
        $order_id = $order_model->insert($insert_data);
        if ($order_id) {
            try {
                $price_info = self::addOrderItem($data['user_id'], $order_id, $data['goods_list']);
            } catch (\Exception $e) {
                YCore::exception($e->getCode(), $e->getMessage());
            }
        }
        $update_data = [];
        // 判断积分是否足够扣取。
        if ($data['is_exchange'] == 1) {
            $jifen_to_cash = YCore::appconfig('jifen_to_cash', 1000);
            $jifen_pay = $jifen_to_cash * $price_info['payment_price'];
            GoldService::goldConsume($data['user_id'], $jifen_pay, GoldService::CONSUME_TYPE_CUT, 'order_pay');
            $update_data['payment_type'] = 2;
            $update_data['jifen_pay']    = $jifen_pay;
            $update_data['pay_status']   = 1;
            $update_data['pay_time']     = $_SERVER['REQUEST_TIME'];
            $update_data['order_status'] = OrderService::ORDER_STATUS_PAY_OK;
        }
        if ($data['user_coupon_id'] > 0) {
            if ($price_info['payment_price'] < $coupon_order_money) {
                YCore::exception(-1, "订单满{$coupon_order_money}元才能使用该优惠券");
            }
            $where = [
                'id'     => $data['user_coupon_id'],
                'is_use' => 0
            ];
            $user_coupon_updata = [
                'is_use'        => 1,
                'use_time'      => $_SERVER['REQUEST_TIME'],
                'modified_time' => $_SERVER['REQUEST_TIME']
            ];
            $ok = $user_coupon_model->update($user_coupon_updata, $where);
            if (!$ok) {
                YCore::exception(-1, '优惠券使用失败');
            }
            // 实付金额减去优惠券的优惠金额。
            $price_info['payment_price'] = $price_info['payment_price'];
            -$coupon_money;
        }
        $update_data['payment_price'] = $price_info['payment_price'];
        $update_data['total_price']   = $price_info['total_price'];
        $update_data['freight_price'] = $price_info['freight_price'];
        $ok = $order_model->update($update_data, ['order_id' => $order_id]);
        if (!$ok) {
            YCore::exception(-1, '服务器繁忙,请稍候重试'); // 此处应该给出日志输出。
        }
        return $order_id;
    }

    /**
     * 添加订单购买的商品明细。
     * -- Example start --
     * $goods_list = [
     *  [
     *      'goods_id' => '商品ID',
     *      'product_id' => '货品ID',
     *      'quantity' => '购买数量',
     *  ],
     *  [
     *      'goods_id' => '商品ID',
     *      'product_id' => '货品ID',
     *      'quantity' => '购买数量',
     *  ],
     *  ......
     * ];
     * -- Example end --
     *
     * @param number $user_id 购买人用户的ID。
     * @param number $order_id 订单ID。
     * @param array $goods_list 购买的商品列表。
     * @return array 返回订单总价与实付总价。
     */
    protected static function addOrderItem($user_id, $order_id, $goods_list) {
        $total_price      = 0.00;
        $payment_price    = 0.00;
        $order_item_model = new MallOrderItem();
        $goods_model = new MallGoods();
        $product_model = new MallProduct();
        foreach ($goods_list as $goods) {
            if (!isset($goods['goods_id'])) {
                YCore::exception(-1, '购买的商品数据异常');
            }
            if (!isset($goods['product_id'])) {
                YCore::exception(-1, '货品数据异常');
            }
            if (!isset($goods['quantity']) || !Validator::is_integer($goods['quantity']) || $goods['quantity'] <= 0) {
                YCore::exception(-1, '商品购买数量有误');
            }
            $goods_info = $goods_model->fetchOne([], ['goods_id' => $goods['goods_id']]);
            if (empty($goods_info)) {
                YCore::exception(-1, '商品不存在或已经删除');
            }
            if ($goods_info['status'] != 1) {
                YCore::exception(-1, "[{$goods_info['goods_name']}]已经删除");
            }
            if ($goods_info['marketable'] != 1) {
                YCore::exception(-1, "[{$goods_info['goods_name']}]已经下架");
            }
            $product_model = new MallProduct();
            $product_info = $product_model->fetchOne([], ['product_id' => $goods['product_id'], 'status' => 1]);
            if (empty($product_info)) {
                YCore::exception(-1, "[{$goods_info['goods_name']}]已经下线");
            }
            if ($product_info['stock'] < $goods['quantity']) {
                YCore::exception(-1, "[{$goods_info['goods_name']}]库存不足");
            }
            $_total_price   = $product_info['market_price'] * $goods['quantity'];
            $_payment_price = $product_info['sales_price'] * $goods['quantity'];
            $total_price   += $_total_price;
            $payment_price += $_payment_price;
            $data = [
                'order_id'      => $order_id,
                'goods_id'      => $goods_info['goods_id'],
                'goods_name'    => $goods_info['goods_name'],
                'goods_image'   => $goods_info['goods_img'],
                'product_id'    => $product_info['product_id'],
                'spec_val'      => $product_info['spec_val'],
                'market_price'  => $product_info['market_price'],
                'sales_price'   => $product_info['sales_price'],
                'quantity'      => $goods['quantity'],
                'created_time'  => $_SERVER['REQUEST_TIME'],
                'created_by'    => $user_id,
                'payment_price' => $_total_price,
                'total_price'   => $_total_price
            ];
            $ok = $order_item_model->insert($data);
            if (!$ok) {
                YCore::exception(-1, '服务器繁忙,请稍候重试');
            }
            $ok = GoodsService::deductionProductStock($product_info['product_id'], $goods['quantity']);
            if (!$ok) {
                YCore::exception(-1, "《{$goods_info['goods_name']}》库存不足");
            }
        }
        return [
            'total_price'   => $total_price,
            'payment_price' => $payment_price,
            'freight_price' => 0 // 运费。
        ];
    }

    /**
     * 修改订单商品价格。
     *
     * @param number $user_id 用户ID。
     * @param number $order_id 订单ID。
     * @param number $product_id 货品ID。
     * @param float $price 价格。
     * @return boolean
     */
    public static function editOrderGoodsPrice($user_id, $order_id, $product_id, $price) {
        if (!Validator::is_float($price) || $price <= 0) {
            YCore::exception(-1, '调整后的价格必须大于0');
        }
        $price = round($price, 2);
        $where = [
            'order_id' => $order_id,
            'status'   => 1
        ];
        $order_model = new MallOrder();
        $order_info = $order_model->fetchOne([], $where);
        if (empty($order_info)) {
            YCore::exception(-1, '订单不存在');
        }
        if ($order_info['order_status'] != self::ORDER_STATUS_WAIT_PAY) {
            YCore::exception(-1, '未支付的订单才允许调价');
        }
        $where = [
            'product_id' => $product_id,
            'order_id'   => $order_id
        ];
        $order_item_model = new MallOrderItem();
        $order_item_info = $order_item_model->fetchOne([], $where);
        if (empty($order_item_info)) {
            YCore::exception(-1, '非法修改数据');
        }
        if ($order_item_info['sales_price'] < $price) {
            YCore::exception(-1, '调价必须小于原价');
        }
        $default_db = new DbBase();
        $default_db->beginTransaction();
        $updata = [
            'is_edit_price' => 1,
            'sales_price'   => $price,
            'old_price'     => $order_item_info['sales_price'],
            'modified_by'   => $user_id,
            'payment_price' => $price * $order_item_info['quantity'],
            'modified_time' => $_SERVER['REQUEST_TIME']
        ];
        $where = [
            'sub_order_id' => $order_item_info['sub_order_id']
        ];
        $ok = $order_item_model->update($updata, $where);
        if (!$ok) {
            $default_db->rollBack();
            YCore::exception(-1, '改价失败');
        }
        // 商品差额总价。
        $diff_money = ($order_item_info['sales_price'] - $price) * $order_item_info['quantity'];
        $order_payment_price = $order_info['payment_price'] - $diff_money;
        $updata = [
            'payment_price' => $order_payment_price,
            'modified_by'   => $user_id,
            'modified_time' => $_SERVER['REQUEST_TIME']
        ];
        $ok = $order_model->update($updata, ['order_id' => $order_id]);
        if (!$ok) {
            $default_db->rollBack();
            YCore::exception(-1, '改价失败');
        }
        $default_db->commit();
        return true;
    }

    /**
     * 发货。
     * -- 1、可重复设置发货信息。
     * -- 2、发货后24小时内可修改发货信息。
     *
     * @param number $user_id 用户ID。
     * @param number $order_id 订单ID。
     * @param string $logistics_code 快递编码。
     * @param string $logistics_number 快递单号。
     * @return boolean
     */
    public static function deliverGoods($user_id, $order_id, $logistics_code, $logistics_number) {
        if (strlen($logistics_code) === 0) {
            YCore::exception(-1, '快递编码不能为空');
        }
        if (!Validator::is_len($logistics_code, 1, 20, 1)) {
            YCore::exception(-1, '快递编码不正确');
        }
        if (strlen($logistics_number) === 0) {
            YCore::exception(-1, '快递单号不能为空');
        }
        if (!Validator::is_len($logistics_number, 1, 50, 1)) {
            YCore::exception(-1, '快递单号长度必须在1~50个字间');
        }
        $order_model = new MallOrder();
        $where = [
            'order_id' => $order_id
        ];
        $order_info = $order_model->fetchOne([], $where);
        if (empty($order_info)) {
            YCore::exception(-1, '订单不存在');
        }
        if ($order_info['order_status'] != self::ORDER_STATUS_PAY_OK && $order_info['order_status'] != self::ORDER_STATUS_DELIVER) {
            YCore::exception(-1, '已支付或已发货24小时内的才允许操作');
        }
        if ($order_info['order_status'] == self::ORDER_STATUS_DELIVER) {
            $diff_timestamp = $_SERVER['REQUEST_TIME'] - $order_info['shipping_time'];
            if ($diff_timestamp > 86400) {
                YCore::exception(-1, '发货超过24小时不能修改');
            }
        }
        $logistics_list_dict = YCore::dict('logistics_list');
        if (!array_key_exists($logistics_code, $logistics_list_dict)) {
            YCore::exception(-1, '快递编号不正确');
        }
        $logistics_model = new MallLogistics();
        $where = [
            'order_id' => $order_id
        ];
        $logistics_info = $logistics_model->fetchOne([], $where);
        if (empty($logistics_info)) {
            $data = [
                'order_id'         => $order_id,
                'logistics_code'   => $logistics_code,
                'logistics_number' => $logistics_number,
                'created_time'     => $_SERVER['REQUEST_TIME'],
                'created_by'       => $user_id
            ];
            $id = $logistics_model->insert($data);
            $ok = $id > 0 ? true : false;
        } else {
            $where = [
                'order_id' => $order_id
            ];
            $updata = [
                'logistics_code'   => $logistics_code,
                'logistics_number' => $logistics_number,
                'modified_time'    => $_SERVER['REQUEST_TIME'],
                'modified_by'      => $user_id
            ];
            $ok = $logistics_model->update($updata, $where);
        }
        if (!$ok) {
            YCore::exception(-1, '操作失败');
        }
        // 更新订单发货状态。
        if ($order_info['order_status'] == self::ORDER_STATUS_PAY_OK) {
            $data = [
                'order_status'  => self::ORDER_STATUS_DELIVER,
                'shipping_time' => $_SERVER['REQUEST_TIME'],
                'modified_by'   => $user_id,
                'modified_time' => $_SERVER['REQUEST_TIME']
            ];
            $ok = $order_model->update($data, ['order_id' => $order_id]);
            if (!$ok) {
                YCore::exception(-1, '发货失败');
            }
        }
        return true;
    }

    /**
     * 修改订单收货地址。
     * -- 1、发货前都可以修改地址。
     *
     * @param number $user_id 用户ID。
     * @param number $order_id 订单ID。
     * @param number $district_id 区县ID。
     * @param string $receiver_name 收货人姓名。
     * @param string $receiver_address 收货人详细地址。
     * @param string $receiver_mobile 收货人手机号。
     * @param string $receiver_zip 收货人地址邮编。
     * @return boolean
     */
    public static function adjustAddress($user_id, $order_id, $district_id, $receiver_name, $receiver_address, $receiver_mobile, $receiver_zip) {
        if (strlen($receiver_name) === 0) {
            YCore::exception(-1, '收货人姓名必须填写');
        }
        if (!Validator::is_len($receiver_name, 1, 10, true)) {
            YCore::exception(-1, '收货人姓名长度必须1~10个字符之间');
        }
        if (strlen($district_id) === 0) {
            YCore::exception(-1, '请选择区县');
        }
        if (strlen($receiver_zip) === 0) {
            YCore::exception(-1, '邮政编码必须填写');
        }
        if (strlen($receiver_mobile) === 0) {
            YCore::exception(-1, '收货人手机号必须填写');
        }
        if (strlen($receiver_address) === 0) {
            YCore::exception(-1, '收货人详细地址必须填写');
        }
        if (!Validator::is_zipcode($receiver_zip)) {
            YCore::exception(-1, '邮政编码不正确');
        }
        if (!Validator::is_mobilephone($receiver_mobile)) {
            YCore::exception(-1, '收货人手机号不正确');
        }
        if (!Validator::is_len($receiver_address, 1, 50, true)) {
            YCore::exception(-1, '收货详细地址长度必须1~50个字符之间');
        }
        $district_model = new District();
        $district_info = $district_model->fetchOne([], ['district_id' => $district_id, 'status' => 1]);
        if (empty($district_info)) {
            YCore::exception(-1, '区县ID有误');
        }
        $province_name = $district_info['province_name'];
        $city_name     = $district_info['city_name'];
        $district_name = $district_info['district_name'];
        $order_model = new MallOrder();
        $where = [
            'order_id' => $order_id,
            'status'   => 1
        ];
        $order_info = $order_model->fetchOne([], $where);
        if (empty($order_info)) {
            YCore::exception(-1, '订单不存在');
        }
        if ($order_info['order_status'] != self::ORDER_STATUS_PAY_OK) {
            YCore::exception(-1, '已付款的订单才允许修改收货信息');
        }
        $updata = [
            'receiver_province' => $province_name,
            'receiver_city'     => $city_name,
            'receiver_district' => $district_name,
            'receiver_street'   => '',
            'receiver_address'  => $receiver_address,
            'receiver_zip'      => $receiver_zip,
            'receiver_mobile'   => $receiver_mobile,
            'modified_by'       => $user_id,
            'modified_time'     => $_SERVER['REQUEST_TIME']
        ];
        $ok = $order_model->update($updata, $where);
        if (!$ok) {
            YCore::exception(-1, '操作失败');
        }
        return true;
    }

    /**
     * 确认收货。
     * -- 只允许买家调用该方法。
     *
     * @param number $user_id 用户ID。
     * @param number $order_id 订单ID。
     * @return boolean
     */
    public static function confirmReceiptGoods($user_id, $order_id) {
        $order_model = new MallOrder();
        $order_info = $order_model->fetchOne([], [
            'order_id' => $order_id,
            'status' => 1
        ]);
        if (empty($order_info) || $order_info['user_id'] != $user_id) {
            YCore::exception(-1, '订单不存在或已经删除');
        }
        if ($order_info['order_status'] != self::ORDER_STATUS_DELIVER) {
            YCore::exception(-1, '只允许已发货的订单');
        }
        $update_data = [
            'order_status'  => self::ORDER_STATUS_SUCCESS,
            'modified_by'   => $user_id,
            'modified_time' => $_SERVER['REQUEST_TIME'],
            'done_time'     => $_SERVER['REQUEST_TIME']
        ];
        $where = [
            'order_id' => $order_id,
            'status'    => 1
        ];
        $ok = $order_model->update($update_data, $where);
        if ($ok) {
            self::writeLog($user_id, $order_id, 'canceled');
            return true;
        } else {
            return false;
        }
    }

    /**
     * 取消订单。
     * -- 只允许买家调用该方法。
     *
     * @param number $user_id 用户ID。
     * @param number $order_id 订单ID。
     * @return boolean
     */
    public static function cencelOrder($user_id, $order_id) {
        $order_model = new MallOrder();
        $order_info = $order_model->fetchOne([], ['order_id' => $order_id, 'status' => 1]);
        if (empty($order_info) || $order_info['user_id'] != $user_id) {
            YCore::exception(-1, '订单不存在或已经删除');
        }
        if ($order_info['order_status'] != self::ORDER_STATUS_WAIT_PAY) {
            YCore::exception(-1, '只允许取消未付款的订单');
        }
        $update_data = [
            'order_status'  => self::ORDER_STATUS_CANCELED,
            'modified_by'   => $user_id,
            'modified_time' => $_SERVER['REQUEST_TIME'],
            'cancel_time'   => $_SERVER['REQUEST_TIME']
        ];
        $where = [
            'order_id' => $order_id,
            'status'   => 1
        ];
        $order_model->beginTransaction();
        $ok = $order_model->update($update_data, $where);
        if ($ok) {
            $ok = self::releaseOrderStock($order_id);
            if (!$ok) {
                $order_model->rollBack();
                YCore::exception(-1, '订单取消失败');
            }
            self::writeLog($user_id, $order_id, 'canceled');
            $order_model->commit();
            return true;
        } else {
            $order_model->rollBack();
            return false;
        }
    }

    /**
     * 关闭订单。
     *
     * @param number $admin_id 用户ID。
     * @param number $order_id 订单ID。
     * @return boolean
     */
    public static function closeOrder($user_id, $order_id) {
        $order_model = new MallOrder();
        $order_info = $order_model->fetchOne([], ['order_id' => $order_id, 'status' => 1]);
        if (empty($order_info)) {
            YCore::exception(-1, '订单不存在或已经删除');
        }
        if ($order_info['order_status'] != self::ORDER_STATUS_WAIT_PAY) {
            YCore::exception(-1, '只允许关闭未付款的订单');
        }
        $update_data = [
            'order_status'  => self::ORDER_STATUS_CLOSED,
            'modified_by'   => $user_id,
            'modified_time' => $_SERVER['REQUEST_TIME'],
            'closed_time'   => $_SERVER['REQUEST_TIME']
        ];
        $where = [
            'order_id' => $order_id,
            'status'   => 1
        ];
        $order_model->beginTransaction();
        $ok = $order_model->update($update_data, $where);
        if ($ok) {
            $ok = self::releaseOrderStock($order_id);
            if (!$ok) {
                $order_model->rollBack();
                YCore::exception(-1, '订单取消失败');
            }
            $log_content = '商家用户执行该操作';
            self::writeLog($user_id, $order_id, 'closed', $log_content);
            $order_model->commit();
            return true;
        } else {
            $order_model->rollBack();
            return false;
        }
    }

    /**
     * 订单运费调整。
     *
     * @param number $user_id
     * @param number $order_id
     * @param number $freight
     */
    public static function adjustFreight($user_id, $order_id, $freight) {
        if (!Validator::is_float($freight) || $freight < 0) {
            YCore::exception(-1, '调整后的运费价必须大于等于0');
        }
        $freight = round($freight, 2);
        $order_model = new MallOrder();
        $order_info = $order_model->fetchOne([], [
            'order_id' => $order_id,
            'status' => 1
        ]);
        if (empty($order_info) || $order_info['user_id'] != $user_id) {
            YCore::exception(-1, '订单不存在或已经删除');
        }
        if ($order_info['order_status'] == self::ORDER_STATUS_WAIT_PAY) {
            YCore::exception(-1, '只允许未支持的订单调运费');
        }
        $update_data = [
            'freight_price' => $freight,
            'payment_price' => $order_info['payment_price'] - $freight,
            'modified_by'   => $user_id,
            'modified_time' => $_SERVER['REQUEST_TIME']
        ];
        $where = [
            'order_id' => $order_id,
            'status'   => 1
        ];
        $ok = $order_model->update($update_data, $where);
        if (!$ok) {
            YCore::exception(-1, '服务器繁忙,请稍候重试');
        }
        return true;
    }

    /**
     * 删除订单。
     *
     * @param number $user_id 用户ID。
     * @param number $order_id 订单ID。
     * @return boolean
     */
    public static function deleteOrder($user_id, $order_id) {
        $order_model = new MallOrder();
        $order_info = $order_model->fetchOne([], ['order_id' => $order_id, 'status' => 1]);
        if (empty($order_info) || $order_info['user_id'] != $user_id) {
            YCore::exception(-1, '订单不存在或已经删除');
        }
        // 允许删除的订单状态。
        $allow_order_status = [
            self::ORDER_STATUS_CANCELED,
            self::ORDER_STATUS_CLOSED
        ];
        if (!in_array($order_info['order_status'], $allow_order_status)) {
            YCore::exception(-1, '只允许关闭或取消的订单才能删除');
        }
        $update_data = [
            'status'        => 2,
            'modified_by'   => $user_id,
            'modified_time' => $_SERVER['REQUEST_TIME']
        ];
        $where = [
            'order_id' => $order_id,
            'user_id'  => $user_id,
            'status'   => 1
        ];
        $ok = $order_model->update($update_data, $where);
        if ($ok) {
            self::writeLog($user_id, $order_id, 'deleted_order');
            return true;
        } else {
            return false;
        }
    }

    /**
     * 获取订单快递信息。
     *
     * @param number $order_id 订单ID。
     * @return array
     */
    public static function getOrderExpressInfo($order_id) {
        $where = [
            'order_id' => $order_id
        ];
        $columns = [
            'logistics_code',
            'logistics_number'
        ];
        $logistics_model = new MallLogistics();
        $logistics_info = $logistics_model->fetchOne($columns, $where);
        if (empty($logistics_info)) {
            return [
                'logistics_code'   => '',
                'logistics_number' => ''
            ];
        } else {
            return $logistics_info;
        }
    }

    /**
     * 释放订单占用的库存。
     *
     * @param number $order_id 订单ID。
     * @return boolean
     */
    protected static function releaseOrderStock($order_id) {
        $order_item_where = [
            'order_id' => $order_id
        ];
        $order_item_model = new MallOrderItem();
        $order_item_list = $order_item_model->fetchAll([], $order_item_where);
        foreach ($order_item_list as $item) {
            $ok = GoodsService::restoreProductStock($item['product_id'], $item['quantity']);
            if (!$ok) {
                return false;
            }
        }
        return true;
    }

    /**
     * 订单操作日志。
     *
     * @param number $user_id 用户ID。
     * @param number $order_id 订单ID。
     * @param string $action_type 操作类型。
     * @param string $log_content 日志内容。
     * @return boolean
     */
    protected static function writeLog($user_id, $order_id, $action_type, $log_content = '') {
        $order_operation_code = YCore::dict('order_operation_code');
        if (!array_key_exists($action_type, $order_operation_code)) {
            YCore::exception(-1, '操作类型不正确');
        }
        $data = [
            'order_id'     => $order_id,
            'action_type'  => $action_type,
            'log_content'  => $log_content,
            'user_id'      => $user_id,
            'created_time' => $_SERVER['REQUEST_TIME']
        ];
        $order_log_model = new MallOrderLog();
        $ok = $order_log_model->insert($data);
        return $ok > 0 ? true : false;
    }

    /**
     * 获取订单号。
     * -- 1、同网段的服务器产生的订单号不会重复。如：192.168.1.1 ~ 192.168.255.255
     * -- 2、多网段的服务器可能会产生重复的订单号。如果并发量不大的情况下，可以勉强使用。如果并发量太大，不要使用。
     * -- 3、订单号组成：前缀 + 时间戳(10位) + 微秒(6位) + 服务器IP编号(6位) + 用户ID(10位) = 订单号。
     *
     * @param number $user_id 用户ID。订单号组成部分。用户来避免订单号重复。也可以通过订单号反解得到时间与用户ID等信息。
     * @param string $prefix 订单号前缀。不允许超过5个字符。
     * @return string
     */
    public static function getOrderSn($user_id, $prefix = '') {
        if (strlen($prefix) > 5) {
            YCore::exception(-1, '订单号前缀不允许超过5个字符');
        }
        // [1]
        $microtime = microtime();
        list($usec, $sec) = explode(' ', $microtime);
        $usec = intval($usec * 1000000);
        $usec = sprintf('%06d', $usec);
        // [2]
        $server_ip     = $_SERVER['SERVER_ADDR'];
        $server_ip_int = ip2long($server_ip);
        $server_number = $server_ip_int % 1000000;
        // [3]
        $user_id = sprintf('%010d', $user_id);
        $order_sn = "{$sec}{$server_number}{$user_id}{$usec}";
        return "{$prefix}{$order_sn}";
    }
}