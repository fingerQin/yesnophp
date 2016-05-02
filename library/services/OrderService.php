<?php
/**
 * 订单业务封装。
 * @author winerQin
 * @date 2016-04-08
 */

namespace services;

use common\YCore;
use winer\Validator;
use models\Order;
use models\Goods;
use models\OrderItem;
use models\Product;
use models\UserAddress;
use models\District;
use models\OrderLog;
use models\DbBase;
class OrderService extends BaseService {

    /**
     * 是否需要发票的状态值。
     * @var array
     */
    protected static $arr_need_invoice = [
        0 => '不需要',
        1 => '需要',
    ];

    /**
     * 发票类型的状态值。
     * @var array
     */
    protected static $arr_invoice_type = [
        1 => '个人',
        2 => '单位',
    ];

    const ORDER_STATUS_WAIT_PAY = 0; // 待付款。
    const ORDER_STATUS_PAY_OK   = 1; // 已付款。
    const ORDER_STATUS_DELIVER  = 2; // 已发货。
    const ORDER_STATUS_SUCCESS  = 3; // 交易成功。
    const ORDER_STATUS_CLOSED   = 4; // 交易关闭。
    const ORDER_STATUS_CANCELED = 5; // 已取消。

    /**
     * 管理后台获取订单列表。
     * @param string $order_sn 订单号。
     * @param number $order_status 订单状态。
     * @param string $start_time 成交时间开始。
     * @param string $end_time 成交时间结束。
     * @param number $page 当前页码。
     * @param number $count 每页显示条数。
     * @return array
     */
    public static function getShopOrderList($order_sn = '', $order_status = -1, $start_time = '', $end_time = '', $page = 1, $count = 20) {
        $offset     = self::getPaginationOffset($page, $count);
        $from_table = ' FROM ms_order ';
        $columns    = ' * ';
        $where      = ' WHERE AND status = :status ';
        $params     = [
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
                YCore::throw_exception(-1, '成交时间格式不正确');
            }
            $where .= ' AND created_time >= :start_time ';
            $params[':start_time'] = strtotime($start_time);
        }
        if (strlen($end_time) > 0) {
            if (!Validator::is_date($end_time)) {
                YCore::throw_exception(-1, '成交时间格式不正确');
            }
            $where .= ' AND created_time <= :end_time ';
            $params[':end_time'] = strtotime($end_time);
        }
        $order_by = ' ORDER BY order_id DESC ';
        $sql = "SELECT COUNT(1) AS count {$from_table} {$where}";
        $default_db = new DbBase();
        $count_data = $default_db->rawQuery($sql, $params)->rawFetchOne();
        $total  = $count_data ? $count_data['count'] : 0;
        $sql = "SELECT {$columns} {$from_table} {$where} {$order_by} LIMIT {$offset},{$count}";
        $list = $default_db->rawQuery($sql, $params)->rawFetchAll();
        foreach ($list as $key => $item) {
            $item['goods_list'] = self::getOrderItems($item['order_id']);
            $list[$key] = $item;
        }
        $result = array(
            'list'   => $list,
            'total'  => $total,
            'page'   => $page,
            'count'  => $count,
            'isnext' => self::IsHasNextPage($total, $page, $count),
        );
        return $result;
    }

    /**
     * 用户订单列表。
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
        $offset     = self::getPaginationOffset($page, $count);
        $from_table = ' FROM ms_order ';
	    $columns    = ' * ';
	    $where      = ' WHERE user_id = :user_id AND status = :status ';
	    $params     = [
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
	            YCore::throw_exception(-1, '成交时间格式不正确');
	        }
	        $where .= ' AND created_time >= :start_time ';
	        $params[':start_time'] = strtotime($start_time);
	    }
	    if (strlen($end_time) > 0) {
	        if (!Validator::is_date($end_time)) {
	            YCore::throw_exception(-1, '成交时间格式不正确');
	        }
	        $where .= ' AND created_time <= :end_time ';
	        $params[':end_time'] = strtotime($end_time);
	    }
	    $order_by = ' ORDER BY order_id DESC ';
	    $sql = "SELECT COUNT(1) AS count {$from_table} {$where}";
	    $default_db = new DbBase();
	    $count_data = $default_db->rawQuery($sql, $params)->rawFetchOne();
	    $total  = $count_data ? $count_data['count'] : 0;
	    $sql = "SELECT {$columns} {$from_table} {$where} {$order_by} LIMIT {$offset},{$count}";
	    $list = $default_db->rawQuery($sql, $params)->rawFetchAll();
	    foreach ($list as $key => $item) {
	        $item['goods_list'] = self::getOrderItems($item['order_id']);
	        $list[$key] = $item;
	    }
	    $result = array(
	        'list'   => $list,
	        'total'  => $total,
	        'page'   => $page,
	        'count'  => $count,
	        'isnext' => self::IsHasNextPage($total, $page, $count),
	    );
	    return $result;
    }
    
    /**
     * 获取订单购买的商品明细。
     * @param number $order_id 订单ID。
     * @return array
     */
    protected static function getOrderItems($order_id) {
        $default_db = new DbBase();
        $columns = 'goods_id,goods_name,product_id,spec_val,market_price,sales_price,quantity,payment_price,total_price,refund_status';
        $sql = "SELECT {$columns} FROM ms_order_item WHERE order_id = :order_id ORDER BY sub_order_id ASC";
        $params = [
            ':order_id' => $order_id
        ];
        return $default_db->rawQuery($sql, $params)->rawFetchAll();
    }

    /**
     * 获取订单详情。
     * @param number $user_id 用户ID。
     * @param number $order_id 订单ID。
     * @return array
     */
    public static function getOrderDetail($user_id, $order_id) {
        $where = [
            'order_id' => $order_id,
            'user_id'  => $user_id,
            'status'   => 1
        ];
        $order_model = new Order();
        $order_detail = $order_model->fetchOne([], $where);
        if (empty($order_detail)) {
            YCore::throw_exception(-1, '订单不存在');
        }
        $order_detail['goods_list'] = self::getOrderItems($order_id);
        return $order_detail;
    }

    /**
     * 用户下单。
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
     * ];
     * 
     * $new_address_info = [
     *      'realname'         => '收货人真实姓名',
     *      'district_code'    => '区县code或街道code',
     *      'zipcode'          => '邮政编码',
     *      'mobilephone'      => '手机号码',
     *      'receiver_address' => '收货详细地址。除省市区街道外的部分地址信息。',
     * ];
     * 
     * $goods_list = [
     *      [
     *          'goods_id'   => '商品ID',
     *          'product_id' => '货品ID',
     *          'quantity'   => '购买数量',
     *      ],
     *      [
     *          'goods_id'   => '商品ID',
     *          'product_id' => '货品ID',
     *          'quantity'   => '购买数量',
     *      ],
     *      ......
     * ];
     * -- Example end --
     * @param array $data 订单信息。
     * @return boolean
     */
    public static function submitOrder($data) {
        if (empty($data)) {
            YCore::throw_exception(-1, '购买信息有误');
        }
        if (!isset($data['goods_list']) || empty($data['goods_list'])) {
            YCore::throw_exception(-1, '没有购买任何商品');
        }
        if (!isset($data['buyer_message']) || mb_strlen($data['buyer_message'], 'UTF-8') > 50) {
            YCore::throw_exception(-1, '买家留言长度最大50个字符');
        }
        if (!isset($data['need_invoice']) || !in_array($data['need_invoice'], self::$arr_need_invoice)) {
            YCore::throw_exception(-1, '是否需要发票选择有误');
        }
        if ($data['need_invoice'] == 1) {
            if (!isset($data['invoice_type']) || !in_array($data['invoice_type'], self::$arr_invoice_type)) {
                YCore::throw_exception(-1, '发票类型选择有误');
            }
            if ($data['invoice_type'] == 2 && !Validator::is_len($data['invoice_name'], 1, 50, true)) {
                YCore::throw_exception(-1, '公司发票抬头必须1~30字符');
            }
        } else { // 不需要发票清理这两个值。
            $data['invoice_type'] = 1;
            $data['invoice_name'] = '';
        }
        if (!isset($data['address_id'])) {
            YCore::throw_exception(-1, '收货地址有误');
        }
        if ($data['address_id'] == -1 && (!isset($data['new_address_info']) || !is_array($data['new_address_info']))) {
            YCore::throw_exception(-1, '新添加的收货地址有误');
        }
        $user_address = self::getOrderUserReceiverAddress($data['user_id'], $data['address_id'], $data['new_address_info']);
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
            'receiver_name'     => $user_address['receiver_name'],
            'receiver_province' => $user_address['receiver_province'],
            'receiver_city'     => $user_address['receiver_city'],
            'receiver_district' => $user_address['receiver_district'],
            'receiver_street'   => $user_address['receiver_street'],
            'receiver_address'  => $user_address['receiver_address'],
            'receiver_zip'      => $user_address['receiver_zip'],
            'receiver_mobile'   => $user_address['receiver_mobile'],
            'buyer_message'     => $data['buyer_message'],
            'created_time'      => $_SERVER['REQUEST_TIME'],
            'created_by'        => $data['user_id'],
            'status'            => 1
        ];
        $order_model = new Order();
        $order_model->beginTransaction();
        $order_id = $order_model->insert($insert_data);
        if ($order_id) {
            try {
                $price_info = self::addOrderItem($data['user_id'], $order_id, $data['goods_list']);                
            } catch (\Exception $e) {
                $order_model->rollBack();
                YCore::throw_exception($e->getCode(), $e->getMessage());
            }
        }
        $update_data = [
            'payment_price' => $price_info['payment_price'],
            'total_price'   => $price_info['total_price'],
            'freight_price' => $price_info['freight_price']
        ];
        $ok = $order_model->update($update_data, ['order_id' => $order_id]);
        if (!$ok) {
            YCore::throw_exception(-1, '服务器繁忙,请稍候重试'); // 此处应该给出日志输出。
        }
        $order_model->commit();
        return $order_id;
    }

    /**
     * 添加订单购买的商品明细。
     * -- Example start --
     * $goods_list = [
     *      [
     *          'goods_id'   => '商品ID',
     *          'product_id' => '货品ID',
     *          'quantity'   => '购买数量',
     *      ],
     *      [
     *          'goods_id'   => '商品ID',
     *          'product_id' => '货品ID',
     *          'quantity'   => '购买数量',
     *      ],
     *      ......
     * ];
     * -- Example end --
     * @param number $user_id 购买人用户的ID。
     * @param number $order_id 订单ID。
     * @param array $goods_list 购买的商品列表。
     * @return array 返回订单总价与实付总价。
     */
    protected static function addOrderItem($user_id, $order_id, $goods_list) {
        $total_price   = 0.00;
        $payment_price = 0.00;
        $order_item_model = new OrderItem();
        $goods_model = new Goods();
        $product_model = new Product();
        foreach ($goods_list as $goods) {
            if (!isset($goods['goods_id'])) {
                YCore::throw_exception(-1, '购买的商品数据异常');
            }
            if (!isset($goods['product_id'])) {
                YCore::throw_exception(-1, '货品数据异常');
            }
            if (!isset($goods['quantity']) || !Validator::is_integer($goods['quantity']) || $goods['quantity'] <= 0) {
                YCore::throw_exception(-1, '商品购买数量有误');
            }
            $goods_info = $goods_model->fetchOne([], ['goods_id' => $goods['goods_id']]);
            if (empty($goods_info)) {
                YCore::throw_exception(-1, '商品不存在或已经删除');
            }
            if ($goods_info['status'] != 1) {
                YCore::throw_exception(-1, "[{$goods_info['goods_name']}]已经删除");
            }
            if ($goods_info['marketable'] != 1) {
                YCore::throw_exception(-1, "[{$goods_info['goods_name']}]已经下架");
            }
            $product_model = new Product();
            $product_info = $product_model->fetchOne([], ['product_id' => $goods['product_id'], 'status' => 1]);
            if (empty($product_info)) {
                YCore::throw_exception(-1, "[{$goods_info['goods_name']}]已经下线");
            }
            if ($product_info['stock'] < $goods['quantity']) {
                YCore::throw_exception(-1, "[{$goods_info['goods_name']}]库存不足");
            }
            $_total_price   = $product_info['market_price'] * $goods['quantity'];
            $_payment_price = $product_info['sales_price'] * $goods['quantity'];
            $total_price   += $_total_price;
            $payment_price += $_payment_price;
            $data = [
                'order_id'      => $order_id,
                'goods_id'      => $goods_info['goods_id'],
                'goods_name'    => $goods_info['goods_name'],
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
                YCore::throw_exception(-1, '服务器繁忙,请稍候重试');
            }
            $ok = GoodsService::deductionProductStock($product_info['product_id'], $goods['quantity']);
            if (!$ok) {
                YCore::throw_exception(-1, "《{$goods_info['goods_name']}》库存不足");
            }
        }
        return [
            'total_price'   => $total_price,
            'payment_price' => $payment_price,
            'freight_price' => 0, // 运费。
        ];
    }

    /**
     * 获取订单用户收货地址[返回订单需要的部分数据]。
     * -- Example start --
     * $new_address_info = [
     *      'realname'         => '收货人真实姓名',
     *      'district_code'    => '区县code或街道code',
     *      'zipcode'          => '邮政编码',
     *      'mobilephone'      => '手机号码',
     *      'receiver_address' => '收货详细地址。除省市区街道外的部分地址信息。',
     * ];
     * -- Example end --
     * @param number $user_id 用户ID。
     * @param number $address_id 用户地址ID。-1代表用户是新添加的地址。
     * @param array $new_address_info 新添加的收货地址。
     * @return array
     */
    protected static function getOrderUserReceiverAddress($user_id, $address_id = -1, $new_address_info = []) {
        $ret_data = [];
        $user_address_model = new UserAddress();
        $district_model = new District();
        if ($address_id != -1) {
            $address_info = $user_address_model->fetchOne([], ['address_id' => $address_id, 'user_id' => $user_id, 'status' => 1]);
            if (empty($address_info)) {
                YCore::throw_exception(-1, '收货地址不正确或已经删除');
            }
            // 因为只保存最后一级。且街道ID又是可以不选的情况。所以。这里必须这样操作。
            $where = [];
            if ($address_info['region_type'] == 4) {
                $where['street_code'] = $address_info['district_code'];
            } else {
                $where['district_code'] = $address_info['district_code'];
            }
            $district_info = $district_model->fetchOne([], $where);
            if (empty($district_info)) {
                YCore::throw_exception(-1, '收货所在地有误,请修改或更换');
            }
            $ret_data = [
                'receiver_name'     => $address_info['realname'],
                'receiver_province' => $district_info['province_name'],
                'receiver_city'     => $district_info['city_name'],
                'receiver_district' => $district_info['district_name'],
                'receiver_street'   => $district_info['street_name'],
                'receiver_address'  => $address_info['address'],
                'receiver_zip'      => $address_info['zipcode'],
                'receiver_mobile'   => $address_info['mobilephone'],
            ];
        } else {
            if (empty($new_address_info) || count($new_address_info) != 5) {
                YCore::throw_exception(-1, '');
            }
            if (!isset($new_address_info['realname']) || !Validator::is_len($new_address_info['realname'], 1, 20, true)) {
                YCore::throw_exception(-1, '收货人姓名必须1~20个字符');
            }
            if (!isset($new_address_info['mobilephone']) || !Validator::is_mobilephone($new_address_info['mobilephone'])) {
                YCore::throw_exception(-1, '收货人手机号码格式不正确');
            }
            if (!isset($new_address_info['zipcode']) || !Validator::is_zipcode($new_address_info['zipcode'])) {
                YCore::throw_exception(-1, '收货地址邮政编码格式不正确');
            }
            if (!isset($new_address_info['receiver_address']) || !Validator::is_len($new_address_info['receiver_address'], 1, 100, true)) {
                YCore::throw_exception(-1, '收货详细地址必须1~100个字符');
            }
            if (!isset($new_address_info['district_code']) || !Validator::is_integer($new_address_info['district_code'])) {
                YCore::throw_exception(-1, '请选择收货所在地');
            }
            $district_info = $district_model->fetchOne([], ['district_code' => $new_address_info['district_code'], 'status' => 1]);
            if (empty($district_info)) {
                $district_info = $district_model->fetchOne([], ['street_code' => $new_address_info['district_code'], 'status' => 1]);
            }
            // 用户收货地址数量是否超过了指定数量。只有用户地址数量还在20个内的时候，才会自动保存。
            $user_where = [
                'user_id' => $user_id,
                'status'  => 1
            ];
            $user_address_count = $user_address_model->count($user_where);
            $max_user_address_count = YCore::sys_config('max_user_address_count');
            if ($user_address_count < $max_user_address_count) {
                $insert_data = [
                    'user_id'       => $user_id,
                    'realname'      => $new_address_info['realname'],
                    'zipcode'       => $new_address_info['zipcode'],
                    'mobilephone'   => $new_address_info['mobilephone'],
                    'district_code' => $new_address_info['district_code'],
                    'region_type'   => $district_info['region_type'],
                    'address'       => $new_address_info['receiver_address'],
                    'status'        => 1,
                    'created_time'  => $_SERVER['REQUEST_TIME']
                ];
                $ok = $user_address_model->insert($insert_data);
                if (!$ok) {
                    YCore::throw_exception(-1, '创建收货地址失败');
                }
            }
            $ret_data = [
                'receiver_name'     => $new_address_info['realname'],
                'receiver_province' => $district_info['province_name'],
                'receiver_city'     => $district_info['city_name'],
                'receiver_district' => $district_info['district_name'],
                'receiver_street'   => $district_info['street_name'],
                'receiver_address'  => $new_address_info['receiver_address'],
                'receiver_zip'      => $new_address_info['zipcode'],
                'receiver_mobile'   => $new_address_info['mobilephone'],
            ];
        }
        return $ret_data;
    }

    /**
     * 确认收货。
     * -- 只允许买家调用该方法。
     * @param number $user_id 用户ID。
     * @param number $order_id 订单ID。
     * @return boolean
     */
    public static function confirmReceiptGoods($user_id, $order_id) {
        $order_model = new Order();
        $order_info = $order_model->fetchOne([], ['order_id' => $order_id, 'status' => 1]);
        if (empty($order_info) || $order_info['user_id'] != $user_id) {
            YCore::throw_exception(-1, '订单不存在或已经删除');
        }
        if ($order_info['order_status'] != self::ORDER_STATUS_DELIVER) {
            YCore::throw_exception(-1, '只允许已发货的订单');
        }
        $update_data = [
            'order_status'  => self::ORDER_STATUS_SUCCESS,
            'modified_by'   => $user_id,
            'modified_time' => $_SERVER['REQUEST_TIME'],
            'done_time'     => $_SERVER['REQUEST_TIME']
        ];
        $where = [
            'order_id' => $order_id,
            'status'   => 1,
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
     * @param number $user_id 用户ID。
     * @param number $order_id 订单ID。
     * @return boolean
     */
    public static function cencelOrder($user_id, $order_id) {
        $order_model = new Order();
        $order_info = $order_model->fetchOne([], ['order_id' => $order_id, 'status' => 1]);
        if (empty($order_info) || $order_info['user_id'] != $user_id) {
            YCore::throw_exception(-1, '订单不存在或已经删除');
        }
        if ($order_info['order_status'] != self::ORDER_STATUS_WAIT_PAY) {
            YCore::throw_exception(-1, '只允许取消未付款的订单');
        }
        $update_data = [
            'order_status'  => self::ORDER_STATUS_CANCELED,
            'modified_by'   => $user_id,
            'modified_time' => $_SERVER['REQUEST_TIME'],
            'cancel_time'   => $_SERVER['REQUEST_TIME']
        ];
        $where = [
            'order_id' => $order_id,
            'status'   => 1,
        ];
        $order_model->beginTransaction();
        $ok = $order_model->update($update_data, $where);
        if ($ok) {
            $ok = self::releaseOrderStock($order_id);
            if (!$ok) {
                $order_model->rollBack();
                YCore::throw_exception(-1, '订单取消失败');
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
     * @param number $admin_id 管理员ID。
     * @param number $order_id 订单ID。
     * @return boolean
     */
    public static function closeOrder($admin_id, $order_id) {
        $order_model = new Order();
        $order_info = $order_model->fetchOne([], ['order_id' => $order_id, 'status' => 1]);
        if (empty($order_info)) {
            YCore::throw_exception(-1, '订单不存在或已经删除');
        }
        if ($order_info['order_status'] != self::ORDER_STATUS_WAIT_PAY) {
            YCore::throw_exception(-1, '只允许关闭未付款的订单');
        }
        $update_data = [
            'order_status'  => self::ORDER_STATUS_CLOSED,
            'modified_by'   => $admin_id,
            'modified_time' => $_SERVER['REQUEST_TIME'],
            'closed_time'   => $_SERVER['REQUEST_TIME']
        ];
        $where = [
            'order_id' => $order_id,
            'status'   => 1,
        ];
        $order_model->beginTransaction();
        $ok = $order_model->update($update_data, $where);
        if ($ok) {
            $ok = self::releaseOrderStock($order_id);
            if (!$ok) {
                $order_model->rollBack();
                YCore::throw_exception(-1, '订单取消失败');
            }
            $log_content = '管理员用户执行该操作';
            self::writeLog($admin_id, $order_id, 'closed', $log_content);
            $order_model->commit();
            return true;
        } else {
            $order_model->rollBack();
            return false;
        }
    }

    /**
     * 删除订单。
     * @param number $user_id 用户ID。
     * @param number $order_id 订单ID。
     * @return boolean
     */
    public static function deleteOrder($user_id, $order_id) {
        $order_model = new Order();
        $order_info = $order_model->fetchOne([], ['order_id' => $order_id, 'status' => 1]);
        if (empty($order_info) || $order_info['user_id'] != $user_id) {
            YCore::throw_exception(-1, '订单不存在或已经删除');
        }
        // 允许删除的订单状态。
        $allow_order_status = [
            self::ORDER_STATUS_CANCELED,
            self::ORDER_STATUS_CLOSED
        ];
        if (!in_array($order_info['order_status'], $allow_order_status)) {
            YCore::throw_exception(-1, '只允许关闭或取消的订单才能删除');
        }
        $update_data = [
            'status'        => 2,
            'modified_by'   => $user_id,
            'modified_time' => $_SERVER['REQUEST_TIME']
        ];
        $where = [
            'order_id' => $order_id,
            'user_id'  => $user_id,
            'status'   => 1,
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
     * 释放订单占用的库存。
     * @param number $order_id 订单ID。
     * @return boolean
     */
    protected static function releaseOrderStock($order_id) {
        $order_item_where = [
            'order_id' => $order_id
        ];
        $order_item_model = new OrderItem();
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
     * @param number $user_id 用户ID。
     * @param number $order_id 订单ID。
     * @param string $action_type 操作类型。
     * @param string $log_content 日志内容。
     * @return boolean
     */
    protected static function writeLog($user_id, $order_id, $action_type, $log_content = '') {
        $order_operation_code = YCore::dict('order_operation_code');
        if (!array_key_exists($action_type, $order_operation_code)) {
            YCore::throw_exception(-1, '操作类型不正确');
        }
        $data = [
            'order_id'     => $order_id,
            'action_type'  => $action_type,
            'log_content'  => $log_content,
            'user_id'      => $user_id,
            'created_time' => $_SERVER['REQUEST_TIME']
        ];
        $order_log_model = new OrderLog();
        $ok = $order_log_model->insert($data);
        return $ok > 0 ? true : false;
    }

    /**
     * 获取订单号。
     * -- 1、同网段的服务器产生的订单号不会重复。如：192.168.1.1 ~ 192.168.255.255
     * -- 2、多网段的服务器可能会产生重复的订单号。如果并发量不大的情况下，可以勉强使用。如果并发量太大，不要使用。
     * -- 3、订单号组成：前缀 + 时间戳(10位) + 微秒(6位) + 服务器IP编号(6位) + 用户ID(10位) = 订单号。
     * @param number $user_id 用户ID。订单号组成部分。用户来避免订单号重复。也可以通过订单号反解得到时间与用户ID等信息。
     * @param string $prefix 订单号前缀。不允许超过5个字符。
     * @return string
     */
    public static function getOrderSn($user_id, $prefix = '') {
        if (strlen($prefix) > 5) {
            YCore::throw_exception(-1, '订单号前缀不允许超过5个字符');
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