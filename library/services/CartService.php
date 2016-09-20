<?php
/**
 * 购物车业务封装。
 * @author winerQin
 * @date 2016-07-22
 */
namespace services;

use models\MallGoods;
use common\YCore;
use models\MallProduct;
use models\MallCart;

class CartService extends BaseService {
    
    /**
     * 获取用户购物车数据。
     *
     * @param number $user_id 用户ID。
     * @return array
     */
    public static function getUserCartList($user_id) {
        $where = [
            'user_id' => $user_id,
            'status'  => 1 
        ];
        $order_by = ' id ASC ';
        $columns = ['goods_id', 'product_id', 'quantity'];
        $cart_model = new MallCart();
        $goods_list = $cart_model->fetchAll($columns, $where, 0, $order_by);
        if (empty($goods_list)) {
            return [];
        }
        return self::formatGoods($goods_list);
    }
    
    /**
     * 格式化指定的商品信息。
     * -- Example start --
     * $goods_list = [
     *  [
     *      'goods_id' => '商品ID',
     *      'product_id' => '货品ID',
     *      'quantity' => '数量',
     *  ],
     *  ......
     * ];
     * -- Example end --
     *
     * @param array $goods_list 商品列表数据。
     * @return array
     */
    protected static function formatGoods($goods_list) {
        $goods_model = new MallGoods();
        $product_model = new MallProduct();
        foreach ($goods_list as $key => $goods) {
            // [1] 取商品信息。
            $goods_info = $goods_model->fetchOne([], ['goods_id' => $goods['goods_id']]);
            $goods['goods_valid'] = ($goods_info['status'] == 1) ? 1 : 0; // 商品是否有效：1有效、0失效。
            $goods['goods_name']  = $goods_info['goods_name'];
            $goods['goods_img']   = $goods_info['goods_img'];
            $goods['limit_count'] = $goods_info['limit_count'];
            // [2] 取货品信息。
            $product_info = $product_model->fetchOne([], ['product_id' => $goods['product_id']]);
            $goods['product_valid'] = ($product_info['status'] == 1) ? 1 : 0;
            $goods['market_price']  = $product_info['market_price'];
            $goods['sales_price']   = $product_info['sales_price'];
            $goods['stock_valid']   = ($product_info['stock'] >= $goods['quantity']) ? 1 : 0; // 库存是否满足当前购物车购买数量。
            $goods['spec_val']      = $product_info['spec_val'];
            $goods_list[$key]       = $goods;
        }
        unset($goods_info, $product_info, $goods_model, $product_model);
        return $goods_list;
    }
    
    /**
     * 设置用户购物车商品数量(增减)。
     *
     * @param number $user_id 用户ID。
     * @param number $goods_id 商品ID。
     * @param number $product_id 货品ID。
     * @param number $quantity 数量。修改后的数量。
     * @return boolean
     */
    public static function setUserCartGoodsQuantity($user_id, $goods_id, $product_id, $quantity) {
        $goods_model = new MallGoods();
        $goods_info = $goods_model->fetchOne([], ['goods_id' => $goods_id, 'status' => 1]);
        if (empty($goods_info)) {
            YCore::exception(-1, '该商品不存在');
        }
        $product_model = new MallProduct();
        $product_info = $product_model->fetchOne([], ['product_id' => $product_id, 'status' => 1]);
        if (empty($product_info)) {
            YCore::exception(-1, '商品已经下架或删除');
        }
        $where = [
            'user_id'    => $user_id,
            'product_id' => $product_id,
            'status'     => 1 
        ];
        $cart_model = new MallCart();
        $cart_info = $cart_model->fetchOne([], $where);
        if (empty($cart_info)) {
            $data = [
                'user_id'      => $user_id,
                'goods_id'     => $goods_id,
                'product_id'   => $product_id,
                'quantity'     => $quantity,
                'status'       => 1,
                'created_time' => $_SERVER['REQUEST_TIME'] 
            ];
            $last_insert_id = $cart_model->insert($data);
            if ($last_insert_id == 0) {
                YCore::exception(-1, '操作失败');
            }
        } else {
            $data = [
                'quantity'      => $quantity,
                'modified_time' => $_SERVER['REQUEST_TIME'] 
            ];
            $where = [
                'user_id'    => $user_id,
                'product_id' => $product_id,
                'status'     => 1 
            ];
            $ok = $cart_model->update($data, $where);
            if (!$ok) {
                YCore::exception(-1, '操作失败');
            }
        }
        return true;
    }
    
    /**
     * 删除购物车商品。
     *
     * @param number $user_id 用户ID。
     * @param number $goods_id 商品ID。
     * @param number $product_id 货品ID。
     * @return boolean
     */
    public static function deleteUserCartGoods($user_id, $goods_id, $product_id) {
        $where = [
            'user_id'    => $user_id,
            'product_id' => $product_id,
            'goods_id'   => $goods_id,
            'status'     => 1 
        ];
        $updata = [
            'modified_time' => $_SERVER['REQUEST_TIME'],
            'status'        => 2 
        ];
        $cart_model = new MallCart();
        $ok = $cart_model->update($updata, $where);
        if (!$ok) {
            YCore::exception(-1, '删除失败');
        }
        return true;
    }
    
    /**
     * 清空用户购物车。
     *
     * @param number $user_id 用户ID。
     * @return boolean
     */
    public static function clearUserCart($user_id) {
        $where = [
            'user_id' => $user_id 
        ];
        $updata = [
            'modified_time' => $_SERVER['REQUEST_TIME'],
            'status'        => 2 
        ];
        $cart_model = new MallCart();
        $ok = $cart_model->update($updata, $where);
        if (!$ok) {
            YCore::exception(-1, '清空失败');
        }
        return true;
    }
    
    /**
     * 同步本地未登录时用户购物车数据。
     * -- Example start --
     * $goods_list = [
     *  [
     *      'goods_id'   => '商品ID',
     *      'product_id' => '货品ID',
     *      'quantity'   => '数量',
     *  ],
     *  ......
     * ];
     * -- Example end --
     *
     * @param number $user_id 用户ID。
     * @param array $goods_list 购物车数据。
     * @return boolean
     */
    public static function syncLocalUserCartGoods($user_id, $goods_list) {
        if (empty($goods_list)) {
            return true; // 没有任何数据也认为成功。
        }
        foreach ($goods_list as $goods) {
            self::setUserCartGoodsQuantity($user_id, $goods['goods_id'], $goods['product_id'], $goods['quantity']);
        }
        return true;
    }
    
    /**
     * 格式化本地用户购物车商品。
     * -- 1、主要用于用户未登录的情况下返回格式化后的购物车数据。
     * -- 2、本地保存关键的购物车数据。
     * -- 3、当用户登录之后，要同步本地购物车数据并清空本地购物车数据。
     * -- Example start --
     * $goods_list = [
     *  [
     *      'goods_id'   => '商品ID',
     *      'product_id' => '货品ID',
     *      'quantity'   => '数量',
     *  ],
     *  ......
     * ];
     * -- Example end --
     *
     * @param array $goods_list 本地购物车商品数据。
     * @return array
     */
    public static function formatLocalUserCartGoods($goods_list) {
        if (empty($goods_list)) {
            return [];
        }
        return self::formatGoods($goods_list);
    }
}