<?php
/**
 * 分类表。
 * @author winerQin
 * @date 2016-03-25
 */

namespace models;

class Category extends DbBase {

    /**
     * 表名。
     * @var string
     */
    protected $_table_name = 'ms_category';

    /**
     * 设置分类排序值。
     * @param number $cat_id 分类ID。
     * @param array $sort_val 排序值。
     * @return boolean
     */
    public function sort($cat_id, $sort_val) {
        $data  = ['listorder' => $sort_val];
        $where = ['cat_id' => $cat_id];
        return $this->update($data, $where);
    }

    /**
     * 通过父分类ID读取子分类。
     * @param number $parent_id 父分类ID。
     * @param number $cat_type 分类类型。
     * @param number $is_get_hide 是否获取隐藏的分类。
     * @return array
     */
    public function getByParentToCategory($parent_id, $cat_type = -1, $is_get_hide = true) {
        $where = [
            'parentid' => $parent_id,
            'cat_type' => $cat_type,
            'status'   => 1
        ];
        if ($is_get_hide == false) {
            $where['display'] = 1;
        }
        $order = 'listorder ASC,cat_id ASC';
        return $this->fetchAll([], $where, 0, $order);
    }
}