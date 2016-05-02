<?php
/**
 * 广告表。
 * @author winerQin
 * @date 2016-03-30
 */

namespace models;

class Ad extends DbBase {

	/**
	 * 表名。
	 * @var string
	 */
	protected $_table_name = 'ms_ad';

	/**
	 * 获取指定广告位置的广告列表。
	 * @param number $pos_id 广告位置ID。
	 * @param string $ad_name 广告名称。模糊搜索广告名称。
	 * @param string $display 显示状态：-1全部、1显示、0隐藏。
	 * @param number $page 页码。
	 * @param number $count 每页显示条数。
	 * @return array
	 */
	public function getList($pos_id, $ad_name = '', $display = -1, $page = 1, $count = 20) {
	    $offset  = $this->getPaginationOffset($page, $count);
	    $columns = ' * ';
	    $where   = ' WHERE status = :status AND pos_id = :pos_id ';
	    $params  = [
	        ':status' => 1,
	        ':pos_id' => $pos_id
	    ];
	    if (strlen($ad_name) > 0) {
	        $where .= ' AND ad_name LIKE :ad_name ';
	        $params[':ad_name'] = "%{$ad_name}%";
	    }
	    if ($display != -1) {
	        $where .= ' AND display LIKE :display ';
	        $params[':display'] = $display;
	    }
	    $order_by = ' ORDER BY listorder ASC, ad_id DESC ';
	    $sql = "SELECT COUNT(1) AS count FROM {$this->_table_name} {$where}";
	    $count_data = $this->rawQuery($sql, $params)->rawFetchOne();
	    $total  = $count_data ? $count_data['count'] : 0;
	    $sql = "SELECT {$columns} FROM {$this->_table_name} {$where} {$order_by} LIMIT {$offset},{$count}";
	    $list = $this->rawQuery($sql, $params)->rawFetchAll();
	    $result = array(
	        'list'   => $list,
	        'total'  => $total,
	        'page'   => $page,
	        'count'  => $count,
	        'isnext' => $this->IsHasNextPage($total, $page, $count),
	    );
	    return $result;
	}
	
	/**
	 * 设置广告排序值。
	 * @param number $ad_id 广告ID。
	 * @param array $sort_val 排序值。
	 * @return boolean
	 */
	public function sortAd($ad_id, $sort_val) {
	    $data  = ['listorder' => $sort_val];
	    $where = ['ad_id' => $ad_id];
	    return $this->update($data, $where);
	}
}