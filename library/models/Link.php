<?php
/**
 * 分类表。
 * @author winerQin
 * @date 2016-03-29
 */

namespace models;

class Link extends DbBase {

    /**
     * 表名。
     * @var string
     */
    protected $_table_name = 'ms_link';

    /**
     * 设置广告排序值。
     * @param number $link_id 友情链接ID。
     * @param array $sort_val 排序值。
     * @return boolean
     */
    public function sortLink($link_id, $sort_val) {
        $data  = ['listorder' => $sort_val];
        $where = ['link_id' => $link_id];
        return $this->update($data, $where);
    }
}