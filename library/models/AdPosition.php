<?php
/**
 * 广告位置表。
 * @author winerQin
 * @date 2016-03-30
 */

namespace models;

class AdPosition extends DbBase {
    
    /**
     * 表名。
     * 
     * @var string
     */
    protected $_table_name = 'ms_ad_position';
    
    /**
     * 获取广告位置列表。
     * 
     * @param string $keyword 查询关键词。
     * @param number $page 页码。
     * @param number $count 每页显示条数。
     * @return array
     */
    public function getList($keyword = '', $page = 1, $count = 10) {
        $offset = $this->getPaginationOffset($page, $count);
        $columns = ' * ';
        $where = ' WHERE status = :status ';
        $params = [
                ':status' => 1 
        ];
        if (strlen($keyword) > 0) {
            $where .= ' AND ( ctitle LIKE :ctitle OR cname LIKE :cname )';
            $params[':ctitle'] = "%{$keyword}%";
            $params[':cname'] = "%{$keyword}%";
        }
        $order_by = ' ORDER BY pos_id DESC ';
        $sql = "SELECT COUNT(1) AS count FROM {$this->_table_name} {$where}";
        $count_data = $this->rawQuery($sql, $params)->rawFetchOne();
        $total = $count_data ? $count_data['count'] : 0;
        $sql = "SELECT {$columns} FROM {$this->_table_name} {$where} {$order_by} LIMIT {$offset},{$count}";
        $list = $this->rawQuery($sql, $params)->rawFetchAll();
        $result = array(
                'list' => $list,'total' => $total,'page' => $page,'count' => $count,
                'isnext' => $this->IsHasNextPage($total, $page, $count) 
        );
        return $result;
    }
}