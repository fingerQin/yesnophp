<?php
/**
 * 微信公众号用户分组表。
 * @author winerQin
 * @date 2016-04-05
 */

namespace models;

class WxGroup extends DbBase {

	/**
	 * 表名。
	 * @var string
	 */
	protected $_table_name = 'ms_wx_group';

	/**
	 * 获取公众号用户分组列表。
	 * @param number $account_id 公众号ID。
	 * @param string $group_name 分组名称。
	 * @param number $page 页码。
	 * @param number $count 每页显示条数。
	 * @return array
	 */
	public function getList($account_id, $group_name = '', $page = 1, $count = 10) {
	    $offset  = $this->getPaginationOffset($page, $count);
	    $columns = ' * ';
	    $where   = ' WHERE status = :status AND account_id = :account_id ';
	    $params  = [
	        ':status'     => 1,
	        ':account_id' => $account_id
	    ];
	    if (strlen($group_name) > 0) {
	        $where .= ' AND group_name = :group_name ';
	        $params[':group_name'] = $group_name;
	    }
	    $order_by = ' ORDER BY account_id DESC ';
	    $sql = "SELECT COUNT(1) AS count FROM {$this->_table_name} {$where}";
	    $sth = $this->link->prepare($sql);
	    $sth->execute($params);
	    $count_data = $sth->fetch();
	    $total  = $count_data ? $count_data['count'] : 0;
	    $sql = "SELECT {$columns} FROM {$this->_table_name} {$where} {$order_by} LIMIT {$offset},{$count}";
	    $sth = $this->link->prepare($sql);
	    $sth->execute($params);
	    $list = $sth->fetchAll();
	    $result = array(
	        'list'   => $list,
	        'total'  => $total,
	        'page'   => $page,
	        'count'  => $count,
	        'isnext' => $this->IsHasNextPage($total, $page, $count),
	    );
	    return $result;
	}
}