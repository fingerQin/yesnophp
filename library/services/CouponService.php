<?php
/**
 * 优惠券管理。
 * @author winerQin
 * @date 2016-07-20
 */
namespace services;

use winer\Validator;
use models\MallCoupon;
use common\YCore;
use models\DbBase;
use models\MallUserCoupon;
use models\User;

class CouponService extends BaseService {
    
    /**
     * 管理后台获取优惠券发送记录。
     *
     * @param number $coupon_id 优惠券ID。
     * @param string $username 用户名。
     * @param string $mobilephone 用户手机号。
     * @param number $page 当前分页页码。
     * @param number $count 每页显示条数。
     * @return array
     */
    public static function getBackendSendHistory($coupon_id = -1, $username, $mobilephone, $page, $count) {
        $offset  = self::getPaginationOffset($page, $count);
        $columns = ' * ';
        $where   = ' WHERE 1 ';
        $params  = [];
        $order_by = ' ORDER BY id DESC ';
        if ($coupon_id != -1) {
            $where .= ' AND coupon_id = :coupon_id ';
            $params[':coupon_id'] = $coupon_id;
        }
        if (strlen($username) > 0) {
            $user_model = new User();
            $userinfo = $user_model->fetchOne([], ['username' => $username]);
            $user_id  = $userinfo ? $userinfo['user_id'] : -1;
            $where   .= ' AND user_id = :user_id ';
            $params[':user_id'] = $user_id;
        } else if (strlen($mobilephone) > 0) {
            $user_model = new User();
            $userinfo = $user_model->fetchOne([], [
                'mobilephone' => $mobilephone 
            ]);
            $user_id = $userinfo ? $userinfo['user_id'] : -1;
            $where .= ' AND user_id = :user_id ';
            $params[':user_id'] = $user_id;
        }
        $sql = "SELECT COUNT(1) AS count FROM mall_user_coupon {$where}";
        $default_db = new DbBase();
        $count_data = $default_db->rawQuery($sql, $params)->rawFetchOne();
        $total = $count_data ? $count_data['count'] : 0;
        $sql = "SELECT {$columns} FROM mall_user_coupon {$where} {$order_by} LIMIT {$offset},{$count}";
        $list = $default_db->rawQuery($sql, $params)->rawFetchAll();
        $coupon_list = []; // 保存循环中出现的优惠券数据。避免重复从数据库读取。
        foreach ($list as $k => $v) {
            $v['modified_time'] = $v['modified_time'] ? date('Y-m-d H:i:s', $v['modified_time']) : '';
            $v['created_time'] = $v['created_time'] ? date('Y-m-d H:i:s', $v['created_time']) : '';
            $v['use_time'] = $v['use_time'] ? date('Y-m-d H:i:s', $v['use_time']) : '';
            $v['is_use'] = $v['is_use'] == 1 ? '已使用' : '未使用';
            if (array_key_exists($v['coupon_id'], $coupon_list)) {
                $coupon_detail       = $coupon_list[$v['coupon_id']];
                $v['coupon_name']    = $coupon_detail['coupon_name'];
                $v['money']          = $coupon_detail['money'];
                $v['order_money']    = $coupon_detail['order_money'];
                $v['get_start_time'] = $coupon_detail['get_start_time'];
                $v['get_end_time']   = $coupon_detail['get_end_time'];
                $v['limit_quantity'] = $coupon_detail['limit_quantity'];
                $v['expiry_date']    = $coupon_detail['expiry_date'];
            } else {
                $coupon_detail = self::getCouponDetail($v['coupon_id'], false);
                $coupon_list[$v['coupon_id']] = $coupon_detail;
                $v['coupon_name']    = $coupon_detail['coupon_name'];
                $v['money']          = $coupon_detail['money'];
                $v['order_money']    = $coupon_detail['order_money'];
                $v['get_start_time'] = $coupon_detail['get_start_time'];
                $v['get_end_time']   = $coupon_detail['get_end_time'];
                $v['limit_quantity'] = $coupon_detail['limit_quantity'];
                $v['expiry_date']    = $coupon_detail['expiry_date'];
                
            }
            $list[$k] = $v;
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
     * 管理后台获取优惠券列表。
     *
     * @param string $coupon_name 优惠券名称。
     * @param number $page 当前页码。
     * @param number $count 每页显示条数。
     * @return array
     */
    public static function getBackendCouponList($coupon_name = '', $page = 1, $count = 20) {
        $offset  = self::getPaginationOffset($page, $count);
        $columns = ' * ';
        $where   = ' WHERE 1 ';
        $params  = [];
        $order_by = ' ORDER BY coupon_id DESC ';
        if (strlen($coupon_name) > 0) {
            $where .= ' AND coupon_name LIKE :coupon_name ';
            $params[':coupon_name'] = "%{$coupon_name}%";
        }
        $sql = "SELECT COUNT(1) AS count FROM mall_coupon {$where}";
        $default_db = new DbBase();
        $count_data = $default_db->rawQuery($sql, $params)->rawFetchOne();
        $total = $count_data ? $count_data['count'] : 0;
        $sql   = "SELECT {$columns} FROM mall_coupon {$where} {$order_by} LIMIT {$offset},{$count}";
        $list  = $default_db->rawQuery($sql, $params)->rawFetchAll();
        foreach ($list as $k => $v) {
            $v['get_start_time'] = YCore::format_timestamp($v['get_start_time']);
            $v['get_end_time']   = YCore::format_timestamp($v['get_end_time']);
            $v['expiry_date']    = YCore::format_timestamp($v['expiry_date']);
            $v['get_count']      = self::getCouponDoGetCount($v['coupon_id']);
            $v['use_count']      = self::getCouponUseCount($v['coupon_id']);
            $v['created_time']   = YCore::format_timestamp($v['created_time']);
            $v['modified_time']  = YCore::format_timestamp($v['modified_time']);
            $list[$k] = $v;
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
     * 用户获取优惠券列表。
     *
     * @param number $user_id 用户ID。
     * @param number $is_use 是否使用。-1全部。1是、0否。
     * @param number $page 当前页码。
     * @param number $count 每页显示条数。
     */
    public static function getUserCouponList($user_id, $is_use = -1, $page = -1, $count = 20) {
        $offset  = self::getPaginationOffset($page, $count);
        $columns = ' coupon_id,is_use,use_time,created_time ';
        $where   = ' WHERE user_id = :user_id ';
        $params  = [
            ':user_id' => $user_id 
        ];
        if ($is_use != -1) {
            $where .= ' AND is_use = :is_use ';
            $params[':is_use'] = $is_use;
        }
        $order_by = ' ORDER BY id DESC ';
        $sql = "SELECT COUNT(1) AS count FROM mall_user_coupon {$where}";
        $default_db = new DbBase();
        $count_data = $default_db->rawQuery($sql, $params)->rawFetchOne();
        $total = $count_data ? $count_data['count'] : 0;
        $sql   = "SELECT {$columns} FROM mall_user_coupon {$where} {$order_by} LIMIT {$offset},{$count}";
        $list  = $default_db->rawQuery($sql, $params)->rawFetchAll();
        $coupon_model = new MallCoupon();
        foreach ($list as $k => $v) {
            $coupon_info = $coupon_model->fetchOne([], ['coupon_id' => $v['coupon_id']]);
            $v['coupon_name']  = $coupon_info['coupon_name'];
            $v['money']        = $coupon_info['money'];
            $v['order_money']  = $coupon_info['order_money'];
            $v['expiry_date']  = $coupon_info['expiry_date'];
            $v['coupon_name']  = $coupon_info['coupon_name'];
            $v['created_time'] = YCore::format_timestamp($v['created_time']);
            $list[$k] = $v;
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
     * 获取用户领取优惠券时指定商家的优惠券列表。
     *
     * @param number $page 当前页码。
     * @param number $count 每页显示条数。
     */
    public static function getUserToShopCouponList($page = -1, $count = 20) {
        $offset  = self::getPaginationOffset($page, $count);
        $columns = ' coupon_id,coupon_name,money,order_money,get_start_time,get_end_time,limit_quantity,expiry_date ';
        $where   = ' WHERE status = :status AND get_start_time < :get_start_time ' 
                 . ' AND get_end_time > :get_end_time AND expiry_date > :expiry_date ';
        $params  = [
            ':status'         => 1,
            ':get_start_time' => $_SERVER['REQUEST_TIME'],
            ':get_end_time'   => $_SERVER['REQUEST_TIME'],
            ':expiry_date'    => $_SERVER['REQUEST_TIME'] 
        ];
        $order_by = ' ORDER BY coupon_id DESC ';
        $sql = "SELECT COUNT(1) AS count FROM mall_coupon {$where}";
        $default_db = new DbBase();
        $count_data = $default_db->rawQuery($sql, $params)->rawFetchOne();
        $total = $count_data ? $count_data['count'] : 0;
        $sql   = "SELECT {$columns} FROM mall_coupon {$where} {$order_by} LIMIT {$offset},{$count}";
        $list  = $default_db->rawQuery($sql, $params)->rawFetchAll();
        foreach ($list as $k => $item) {
            $item['get_start_time'] = YCore::format_timestamp($item['get_start_time']);
            $item['get_end_time']   = YCore::format_timestamp($item['get_end_time']);
            $item['expiry_date']    = YCore::format_timestamp($item['expiry_date']);
            $list[$k] = $item;
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
     * 用户领取优惠券。
     *
     * @param number $user_id 用户ID。
     * @param number $coupon_id 优惠券ID。
     * @return boolean
     */
    public static function doGetCoupon($user_id, $coupon_id) {
        $coupon_model = new MallCoupon();
        $coupon_info = $coupon_model->fetchOne([], ['coupon_id' => $coupon_id, 'status' => 1]);
        if (empty($coupon_info)) {
            YCore::exception(-1, '优惠券不存在');
        }
        if ($coupon_info['get_start_time'] > $_SERVER['REQUEST_TIME']) {
            YCore::exception(-1, '优惠券不存在');
        }
        if ($coupon_info['get_end_time'] < $_SERVER['REQUEST_TIME']) {
            YCore::exception(-1, '优惠券已经过了领取时间');
        }
        if ($coupon_info['expiry_date'] < $_SERVER['REQUEST_TIME']) {
            YCore::exception(-1, '优惠券已经过期');
        }
        $where = [
            'user_id'   => $user_id,
            'coupon_id' => $coupon_id 
        ];
        $user_coupon_model = new MallUserCoupon();
        $user_coupon_info = $user_coupon_model->fetchOne([], $where, 'id DESC');
        if (!empty($user_coupon_info) && $user_coupon_info['is_use'] == 0) {
            YCore::exception(-1, '您已经领过该优惠券,请使用之后再来领取吧');
        }
        if ($coupon_info['limit_quantity'] == 1) {
            if (!empty($user_coupon_info)) {
                YCore::exception(-1, '该优惠券每人只能领取一张');
            }
        } else {
            $user_coupon_count = $user_coupon_model->count($where);
            if ($user_coupon_count >= $coupon_info['limit_quantity']) {
                YCore::exception(-1, "该优惠券每人只能领取{$coupon_info['limit_quantity']}张");
            }
        }
        $data = [
            'coupon_id'    => $coupon_id,
            'user_id'      => $user_id,
            'shop_id'      => $coupon_info['shop_id'],
            'is_use'       => 0,
            'use_time'     => 0,
            'created_time' => $_SERVER['REQUEST_TIME'] 
        ];
        $id = $user_coupon_model->insert($data);
        if ($id == 0) {
            YCore::exception(-1, '领取失败');
        }
        return true;
    }
    
    /**
     * 添加优惠券。
     *
     * @param number $user_id 用户ID。
     * @param string $get_start_time 优惠券领取开始时间。
     * @param string $get_end_time 优惠券领取截止时间。
     * @param number $limit_quantity 优惠券限领数量。
     * @param string $coupon_name 优惠券名称。
     * @param number $money 优惠券金额。
     * @param number $order_money 订单满多少可用。
     * @param string $expiry_date 有效期截止时间。
     * @return boolean
     */
    public static function addCoupon($user_id, $get_start_time, $get_end_time, $limit_quantity, $coupon_name, $money, $order_money, $expiry_date) {
        $data = [
            'user_id'        => $user_id,
            'coupon_name'    => $coupon_name,
            'money'          => $money,
            'order_money'    => $order_money,
            'expiry_date'    => $expiry_date,
            'get_start_time' => $get_start_time,
            'get_end_time'   => $get_end_time,
            'limit_quantity' => $limit_quantity 
        ];
        $rules = [
            'user_id'        => '用户ID|require:1000000|integer:1000000',
            'coupon_name'    => '优惠券名称|require:1000000|len:1000000:1:20:1',
            'money'          => '优惠券金额|require:1000000|integer:1000000|number_between:1000000:1:10000',
            'order_money'    => '订单金额|require:1000000|integer:1000000|number_between:1000000:1:1000000',
            'expiry_date'    => '有效期截止时间|require:1000000|date:1000000:1',
            'get_start_time' => '优惠券领取开始时间|require:1000000|date:1000000:1',
            'get_end_time'   => '优惠券领取截止时间|require:1000000|date:1000000:1',
            'limit_quantity' => '优惠券限每人领数量|require:1000000|integer:1000000|number_between:1000000:1:1000' 
        ];
        Validator::valido($data, $rules);
        if ($order_money < $money) {
            YCore::exception(-1, '订单金额必须大于优惠券面额');
        }
        $data['get_start_time'] = strtotime($data['get_start_time']);
        $data['get_end_time']   = strtotime($data['get_end_time']);
        $data['expiry_date']    = strtotime($data['expiry_date']);
        $data['created_time']   = $_SERVER['REQUEST_TIME'];
        $data['created_by']     = $user_id;
        $data['status']         = 1;
        unset($data['user_id']);
        $coupon_model = new MallCoupon();
        $coupon_id = $coupon_model->insert($data);
        if ($coupon_id == 0) {
            YCore::exception(-1, '添加优惠券失败');
        }
        return true;
    }
    
    /**
     * 编辑优惠券。
     *
     * @param number $user_id 用户ID。
     * @param number $coupon_id 优惠券ID。
     * @param string $get_start_time 优惠券领取生效时间。
     * @param string $get_end_time 优惠券领取截止时间。
     * @param number $limit_quantity 优惠券限领数量。
     * @param string $coupon_name 优惠券名称。
     * @param number $money 优惠券金额。
     * @param number $order_money 订单满多少可用。
     * @param string $expiry_date 有效期截止时间。
     * @return boolean
     */
    public static function editCoupon($user_id, $coupon_id, $get_start_time, $get_end_time, $limit_quantity = 1, $coupon_name, $money, $order_money, $expiry_date) {
        $data = [
            'coupon_id'      => $coupon_id,
            'user_id'        => $user_id,
            'coupon_name'    => $coupon_name,
            'money'          => $money,
            'order_money'    => $order_money,
            'expiry_date'    => $expiry_date,
            'get_start_time' => $get_start_time,
            'get_end_time'   => $get_end_time,
            'limit_quantity' => $limit_quantity 
        ];
        $rules = [
            'coupon_id'      => '优惠券ID|require:1000000|integer:1000000',
            'user_id'        => '用户ID|require:1000000|integer:1000000',
            'coupon_name'    => '优惠券名称|require:1000000|len:1000000:1:20:1',
            'money'          => '优惠券金额|require:1000000|integer:1000000|number_between:1000000:1:10000',
            'order_money'    => '订单金额|require:1000000|integer:1000000|number_between:1000000:1:1000000',
            'expiry_date'    => '有效期截止时间|require:1000000|date:1000000:1',
            'get_start_time' => '优惠券领取开始时间|require:1000000|date:1000000:1',
            'get_end_time'   => '优惠券领取截止时间|require:1000000|date:1000000:1',
            'limit_quantity' => '优惠券限每人领数量|require:1000000|integer:1000000|number_between:1000000:1:1000' 
        ];
        Validator::valido($data, $rules);
        if ($order_money < $money) {
            YCore::exception(-1, '订单金额必须大于优惠券面额');
        }
        $where = [
            'coupon_id' => $coupon_id,
            'status' => 1 
        ];
        $coupon_model = new MallCoupon();
        $coupon_info = $coupon_model->fetchOne([], $where);
        if (empty($coupon_info)) {
            YCore::exception(-1, '优惠券不存在');
        }
        if ($coupon_info['expiry_date'] < $_SERVER['REQUEST_TIME']) {
            YCore::exception(-1, '优惠券已经过期不能修改');
        }
        $user_coupon_model = new MallUserCoupon();
        $user_coupon_info = $user_coupon_model->fetchOne([], ['coupon_id' => $coupon_id], 'id DESC');
        if (!empty($user_coupon_info)) {
            YCore::exception(-1, '优惠券已经有人领取不能修改');
        }
        $data['get_start_time'] = strtotime($data['get_start_time']);
        $data['get_end_time']   = strtotime($data['get_end_time']);
        $data['expiry_date']    = strtotime($data['expiry_date']);
        $data['modified_time']  = $_SERVER['REQUEST_TIME'];
        $data['modified_by']    = $user_id;
        unset($data['user_id'], $data['coupon_id']);
        $coupon_id = $coupon_model->update($data, ['coupon_id' => $coupon_id]);
        if ($coupon_id == 0) {
            YCore::exception(-1, '修改优惠券失败');
        }
        return true;
    }
    
    /**
     * 删除优惠券。
     *
     * @param number $user_id 用户ID。
     * @param unknown $coupon_id 优惠券ID。
     * @return boolean
     */
    public static function deleteCoupon($user_id, $coupon_id) {
        $where = [
            'coupon_id' => $coupon_id,
            'status'    => 1 
        ];
        $coupon_model = new MallCoupon();
        $coupon_info = $coupon_model->fetchOne([], $where);
        if (empty($coupon_info)) {
            YCore::exception(-1, '优惠券不存在或已经删除');
        }
        if ($coupon_info['expiry_date'] < $_SERVER['REQUEST_TIME']) {
            YCore::exception(-1, '优惠券已经过期不能修改');
        }
        $user_coupon_model = new MallUserCoupon();
        $user_coupon_info = $user_coupon_model->fetchOne([], ['coupon_id' => $coupon_id], 'id DESC');
        if (!empty($user_coupon_info)) {
            YCore::exception(-1, '优惠券已经有人领取不能删除');
        }
        $data = [
            'modified_time' => $_SERVER['REQUEST_TIME'],
            'status'        => 2 
        ];
        $coupon_id = $coupon_model->update($data, ['coupon_id' => $coupon_id]);
        if ($coupon_id == 0) {
            YCore::exception(-1, '删除优惠券失败');
        }
        return true;
    }
    
    /**
     * 获取优惠券详情。
     *
     * @param number $coupon_id 优惠券ID。
     * @param boolean $dont_status 是否需要状态过滤。true是、false否。
     * @return array
     */
    public static function getCouponDetail($coupon_id, $dont_status = true) {
        $where = [
            'coupon_id' => $coupon_id 
        ];
        if ($dont_status) {
            $where['status'] = 1;
        }
        $coupon_model = new MallCoupon();
        $coupon_info = $coupon_model->fetchOne([], $where);
        if (empty($coupon_info)) {
            YCore::exception(-1, '优惠券不存在或已经删除');
        }
        $coupon_info['get_start_time'] = YCore::format_timestamp($coupon_info['get_start_time']);
        $coupon_info['get_end_time']   = YCore::format_timestamp($coupon_info['get_end_time']);
        $coupon_info['expiry_date']    = YCore::format_timestamp($coupon_info['expiry_date']);
        return $coupon_info;
    }

    /**
     * 获取优惠券领取数量。
     *
     * @param number $coupon_id 优惠券。
     * @return number
     */
    public static function getCouponDoGetCount($coupon_id) {
        $where = [
            'coupon_id' => $coupon_id 
        ];
        $user_coupon_model = new MallUserCoupon();
        return $user_coupon_model->count($where);
    }
    
    /**
     * 获取优惠券使用数量。
     *
     * @param number $coupon_id 优惠券。
     * @return number
     */
    public static function getCouponUseCount($coupon_id) {
        $where = [
            'coupon_id' => $coupon_id,
            'is_use'    => 1 
        ];
        $user_coupon_model = new MallUserCoupon();
        return $user_coupon_model->count($where);
    }
}