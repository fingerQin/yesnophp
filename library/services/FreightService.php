<?php
/**
 * 运费管理。
 * @author winerQin
 * @date 2016-06-12
 */
namespace services;

use winer\Validator;
use models\MallFreightTpl;
use common\YCore;
use models\MallGoods;
use models\MallProduct;
use models\District;

class FreightService extends BaseService {

    /**
     * 运费承担字典。
     *
     * @var array
     */
    public static $bear_freight_dict = [
        1 => '卖家包邮',
        2 => '买家承担运费'
    ];

    /**
     * 计费类型字典。
     *
     * @var array
     */
    public static $fright_type_dict = [
        1 => '按件数',
        2 => '按重量'
    ];

    /**
     * 计算商品运费[单商家]。
     * -- 1、如果想计算多商家的商品运费请多调几次。
     * -- Example start --
     * $goods_list = [
     *  [
     *      'goods_id'   => '商品ID',
     *      'product_id' => '货品ID',
     *      'quantity'   => '购买数量',
     *  ],
     *  ......
     * ];
     * -- Example end --
     *
     * @param array $goods_list 商品列表。
     * @param number $user_district_id 区县ID。根据这个值判断是否能够配送。
     * @return array
     */
    public static function calculateGoodsFreight($goods_list, $user_district_id = -1) {
        if (empty($goods_list)) {
            YCore::exception(-1, '请求数据有误');
        }
        $province_id = 0;
        $city_id     = 0;
        $district_id = 0;
        if ($district_id != -1) {
            $district_model = new District();
            $district_info = $district_model->fetchOne([], ['district_id' => $user_district_id, 'status' => 1]);
            if (empty($district_info)) {
                YCore::exception(-1, '区县ID有误');
            }
            $city_info = $district_model->fetchOne([], ['city_code' => $district_info['city_code'], 'region_type' => 2, 'status' => 1]);
            if (empty($city_info)) {
                YCore::exception(-1, '配送城市数据有误');
            }
            $province_info = $district_model->fetchOne([], ['province_code' => $district_info['province_code'], 'region_type' => 1, 'status' => 1]);
            if (empty($province_info)) {
                YCore::exception(-1, '配送省份数据有误');
            }
            $district_id = $district_info['district_id'];
            $province_id = $province_info['district_id'];
            $city_id     = $city_info['district_id'];
        }
        $total_freight      = 0;  // 商品总运费。
        $goods_list_freight = []; // 商品运费细则。
        foreach ($goods_list as $goods) {
            $result = self::calculateSingleGoodsFreight($goods['goods_id'], $goods['product_id'], $goods['quantity'], $province_id, $city_id, $district_id);
            if ($result['is_delivery'] == 1) {
                $total_freight += $result['fregith'];
            }
            $key = "{$goods['goods_id']}_{$goods['product_id']}";
            $goods_list_freight[$key] = $result;
        }
        return [
            'total_freight'      => $total_freight,
            'goods_list_freight' => $goods_list_freight
        ];
    }

    /**
     * 计算单个商品运费。
     *
     * @param number $goods_id 商品ID。
     * @param number $product_id 货品ID。
     * @param number $quantity 购买数量。
     * @param number $province_id 省ID。根据这个值判断是否能够配送。
     * @param number $city_id 市ID。根据这个值判断是否能够配送。
     * @param number $district_id 区县ID。根据这个值判断是否能够配送。
     * @return array
     */
    protected static function calculateSingleGoodsFreight($goods_id, $product_id, $quantity, $province_id = 0, $city_id = 0, $district_id = 0) {
        $goods_model = new MallGoods();
        $where = [
            'goods_id' => $goods_id,
            'status'   => 1
        ];
        $goods_info = $goods_model->fetchOne([], $where);
        if (empty($goods_info)) {
            YCore::exception(-1, '商品不存在或已经删除');
        }
        $product_model = new MallProduct();
        $product_info = $product_model->fetchOne([], [
            'goods_id'   => $goods_id,
            'product_id' => $product_id,
            'status'     => 1
        ]);
        if (empty($product_info)) {
            YCore::exception(-1, '货品数据有误');
        }
        $freight_tpl_model = new MallFreightTpl();
        $freight_tpl_info = $freight_tpl_model->fetchOne([], [
            'tpl_id' => $goods_info['freight_tpl_id'],
            'status' => 1
        ]);
        if (empty($freight_tpl_info)) {
            YCore::exception(-1, '商品运费模板异常');
        }
        // [1] 判断用户所在区域是否配送。
        $no_area = explode(',', $freight_tpl_info['no_area']);
        $is_delivery = true;
        if (!empty($no_area)) {
            if ($product_id > 0 && in_array($province_id, $no_area)) {
                $is_delivery = false;
            }
            if ($city_id > 0 && in_array($city_id, $no_area)) {
                $is_delivery = false;
            }
            if ($district_id > 0 && in_array($district_id, $no_area)) {
                $is_delivery = false;
            }
        }
        if (!$is_delivery) {
            return [
                'is_delivery' => 0,
                'fregith'     => 0
            ];
        }
        // [2] 判断是否买家包邮。
        if ($freight_tpl_info['bear_freight'] == 1) {
            return [
                'is_delivery' => 1,
                'fregith'     => 0
            ];
        }
        // [3] 计件 & 计重 运费计算。
        if ($freight_tpl_info['fright_type'] == 1) { // 计件。
            $beyond_freight = 0; // 超出基础计费步长的运费。
            if ($quantity > $freight_tpl_info['base_step']) {
                $beyond_quantity = $quantity - $freight_tpl_info['base_step']; // 超出基础计费步长的商品件数。
                if ($freight_tpl_info['rate_step'] == 0 || $freight_tpl_info['step_freight'] == 0) {
                    $beyond_freight = 0; // 超出步长计费步长与每步长费用为0说明不需要此条件。
                } else {
                    $beyond_step    = ceil($beyond_quantity / $freight_tpl_info['rate_step']);
                    $beyond_freight = $beyond_step * $freight_tpl_info['step_freight'];
                }
            }
            $fregith = $freight_tpl_info['base_freight'] + $beyond_freight;
            if ($freight_tpl_info['baoyou_fee'] > 0 && $fregith >= $freight_tpl_info['baoyou_fee']) {
                $fregith = 0; // 商品金额满多少元包邮条件。
            }
        } else if ($freight_tpl_info['fright_type'] == 2) { // 计重。
            $beyond_freight = 0; // 超出基础计费步长的运费。
            if ($quantity > $freight_tpl_info['base_step']) {
                $beyond_quantity = $quantity * $goods_info['weight'] - $freight_tpl_info['base_step']; // 超出基础计费步长的商品件数。
                if ($freight_tpl_info['rate_step'] == 0 || $freight_tpl_info['step_freight'] == 0) {
                    $beyond_freight = 0; // 超出步长计费步长与每步长费用为0说明不需要此条件。
                } else {
                    $beyond_step    = ceil($beyond_quantity / $freight_tpl_info['rate_step']);
                    $beyond_freight = $beyond_step * $freight_tpl_info['step_freight'];
                }
            }
            $fregith = $freight_tpl_info['base_freight'] + $beyond_freight;
            if ($freight_tpl_info['baoyou_fee'] > 0 && $fregith >= $freight_tpl_info['baoyou_fee']) {
                $fregith = 0; // 商品金额满多少元包邮条件。
            }
        }
        return [
            'is_delivery' => 1,
            'fregith' => $fregith
        ];
    }

    /**
     * 获取商家所有运费模板。
     *
     * @return array
     */
    public static function getShopFreightList() {
        $where = [
            'status' => 1
        ];
        $order_by = 'tpl_id ASC';
        $freight_tpl_model = new MallFreightTpl();
        $list = $freight_tpl_model->fetchAll([], $where, 0, $order_by);
        foreach ($list as $k => $v) {
            $v['bear_freight_label'] = self::$bear_freight_dict[$v['bear_freight']];
            $v['freight_type_label'] = self::$fright_type_dict[$v['freight_type']];
            $v['modified_time']      = YCore::format_timestamp($v['modified_time']);
            $v['created_time']       = YCore::format_timestamp($v['created_time']);
            $list[$k] = $v;
        }
        return $list;
    }

    /**
     * 添加商家运费模板。
     * -- Example start --
     * $data = [
     *      'user_id'       => '用户ID',
     *      'send_time'     => '发货时间',
     *      'bear_freight'  => '承担运费',
     *      'rate_step'     => '计费步长',
     *      'step_freight'  => '每计费步长费用',
     *      'freight_name'  => '运费模板名称',
     *      'fright_type'   => '计费类型',
     *      'base_step'     => '基础计费步长',
     *      'base_freight'  => '基础运费',
     *      'no_area'       => '不配送区域',
     *      'baoyou_fee'    => '包邮金额'
     * ];
     * -- Example end --
     *
     * @param array $data 运费模板数据。
     * @return boolean
     */
    public static function addFreightTpl($data) {
        $data['no_area'] = trim($data['no_area'], ',');
        if (strlen($data['no_area']) > 0) {
            $no_area = explode(',', $data['no_area']);
            array_unique($no_area);
            $data['no_area'] = implode(',', $no_area);
        }
        $rules = [
            'user_id'      => '用户ID|require:1000000|integer:1000000',
            'bear_freight' => '运费承担|require:1000000|integer:1000000|number_between:1000000:1:2',
            'send_time'    => '发货时间|require:1000000|integer:1000000|number_between:1000000:0:1000',
            'rate_step'    => '记费步长|require:1000000|integer:1000000|number_between:1000000:0:100000',
            'step_freight' => '每记费步长费用|require:1000000|integer:1000000|number_between:1000000:0:100000',
            'freight_name' => '运费模板名称|require:1000000|len:1000000:1:20:1',
            'fright_type'  => '计费类型|require:1000000|integer:1000000|number_between:1000000:1:2',
            'base_step'    => '基础计费步长|require:1000000|integer:1000000|number_between:1000000:0:100000',
            'base_freight' => '基础运费|require:1000000|integer:1000000|number_between:1000000:0:1000000',
            'no_area'      => '不配送区域|len:1000000:0:1000:1',
            'baoyou_fee'   => '包邮金额|require:1000000|integer:1000000|number_between:1000000:0:1000000'
        ];
        Validator::valido($data, $rules);
        $data['status']       = 1;
        $data['created_time'] = $_SERVER['REQUEST_TIME'];
        $data['created_by']   = $data['user_id'];
        unset($data['user_id']);
        $freight_tpl_model = new MallFreightTpl();
        $tpl_id = $freight_tpl_model->insert($data);
        if ($tpl_id == 0) {
            YCore::exception(-1, '添加失败');
        }
        return true;
    }

    /**
     * 添加商家运费模板。
     * -- Example start --
     * $data = [
     *      'tpl_id'       => '运费模板ID',
     *      'user_id'      => '用户ID',
     *      'send_time'    => '发货时间',
     *      'bear_freight' => '承担运费',
     *      'rate_step'    => '记费步长',
     *      'step_freight' => '每记费步长费用',
     *      'freight_name' => '运费模板名称',
     *      'fright_type'  => '计费类型',
     *      'base_step'    => '基础计费步长',
     *      'base_freight' => '基础运费',
     *      'no_area'      => '不配送区域',
     *      'baoyou_fee'   => '包邮金额'
     * ];
     * -- Example end --
     *
     * @param array $data 运费模板数据。
     * @return boolean
     */
    public static function editFreightTpl($data) {
        $data['no_area'] = trim($data['no_area'], ',');
        if (strlen($data['no_area']) > 0) {
            $no_area = explode(',', $data['no_area']);
            array_unique($no_area);
            $data['no_area'] = implode(',', $no_area);
        }
        $rules = [
            'tpl_id'       => '运费模板ID|require:1000000|integer:1000000',
            'user_id'      => '用户ID|require:1000000|integer:1000000',
            'bear_freight' => '运费承担|require:1000000|integer:1000000|number_between:1000000:1:2',
            'send_time'    => '发货时间|require:1000000|integer:1000000|number_between:1000000:0:1000',
            'rate_step'    => '记费步长|require:1000000|integer:1000000|number_between:1000000:0:100000',
            'step_freight' => '每记费步长费用|require:1000000|integer:1000000|number_between:1000000:0:100000',
            'freight_name' => '运费模板名称|require:1000000|len:1000000:1:20:1',
            'fright_type'  => '计费类型|require:1000000|integer:1000000|number_between:1000000:1:2',
            'base_step'    => '基础计费步长|require:1000000|integer:1000000|number_between:1000000:0:100000',
            'base_freight' => '基础运费|require:1000000|integer:1000000|number_between:1000000:0:1000000',
            'no_area'      => '不配送区域|len:1000000:0:1000:1',
            'baoyou_fee'   => '包邮金额|require:1000000|integer:1000000|number_between:1000000:0:1000000'
        ];
        Validator::valido($data, $rules);
        $freight_tpl_model = new MallFreightTpl();
        $where = [
            'tpl_id' => $data['tpl_id'],
            'status' => 1
        ];
        $freight_tpl_info = $freight_tpl_model->fetchOne([], $where);
        if (empty($freight_tpl_info)) {
            YCore::exception(-1, '运费模板不存在或已经删除');
        }
        $data['modified_time'] = $_SERVER['REQUEST_TIME'];
        $data['modified_by']   = $data['user_id'];
        unset($data['user_id'], $data['tpl_id']);
        $ok = $freight_tpl_model->update($data, $where);
        if (!$ok) {
            YCore::exception(-1, '修改失败');
        }
        return true;
    }

    /**
     * 删除运费模板。
     *
     * @param number $user_id 用户ID。
     * @param number $tpl_id 运费模板ID。
     * @return boolean
     */
    public static function deleteFreightTpl($user_id, $tpl_id) {
        $freight_tpl_model = new MallFreightTpl();
        $where = [
            'tpl_id' => $tpl_id,
            'status' => 1
        ];
        $freight_tpl_info = $freight_tpl_model->fetchOne([], $where);
        if (empty($freight_tpl_info)) {
            YCore::exception(-1, '运费模板不存在或已经删除');
        }
        $data = [
            'modified_time' => $_SERVER['REQUEST_TIME'],
            'modified_by'   => $user_id,
            'status'        => 2
        ];
        $ok = $freight_tpl_model->update($data, $where);
        if (!$ok) {
            YCore::exception(-1, '删除失败');
        }
        return true;
    }

    /**
     * 获取运费模板详情。
     *
     * @param number $tpl_id 商家模板ID。
     * @return array
     */
    public static function getFreightTplDetail($tpl_id) {
        $freight_tpl_model = new MallFreightTpl();
        $where = [
            'tpl_id' => $tpl_id,
            'status' => 1
        ];
        $freight_tpl_info = $freight_tpl_model->fetchOne([], $where);
        if (empty($freight_tpl_info)) {
            YCore::exception(-1, '运费模板不存在或已经删除');
        }
        $freight_tpl_info['modified_time'] = YCore::format_timestamp($freight_tpl_info['modified_time']);
        $freight_tpl_info['created_time']  = YCore::format_timestamp($freight_tpl_info['created_time']);
        return $freight_tpl_info;
    }
}