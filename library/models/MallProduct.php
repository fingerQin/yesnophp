<?php
/**
 * 货品表。
 * @author winerQin
 * @date 2016-05-03
 */

namespace models;

class MallProduct extends DbBase {

    /**
     * 表名。
     *
     * @var string
     */
    protected $_table_name = 'mall_product';

    /**
     * 删除指定商品的货品。
     *
     * @param number $user_id 操作用户ID。
     * @param number $goods_id 商品ID。
     * @return boolean
     */
    public function deleteGoodsProduct($user_id, $goods_id) {
        $where = [
            'goods_id' => $goods_id,
            'status'   => 1
        ];
        $data = [
            'status'        => 2,
            'modified_by'   => $user_id,
            'modified_time' => $_SERVER['REQUEST_TIME']
        ];
        return $this->update($data, $where);
    }
}