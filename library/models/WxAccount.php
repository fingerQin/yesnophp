<?php
/**
 * 微信公众号表。
 * @author winerQin
 * @date 2016-05-03
 */

namespace models;

class WxAccount extends DbBase {

	/**
	 * 表名。
	 * @var string
	 */
	protected $_table_name = 'wx_account';

	/**
	 * 获取公众号列表。
	 * @param string $account 公众号。
     * @param string $sn 公众号编号。
     * @param string $appid APPID。
	 * @param number $page 页码。
	 * @param number $count 每页显示条数。
	 * @return array
	 */
	public function getList($account = '', $sn = '', $appid = '', $page = 1, $count = 10) {
	    $offset  = $this->getPaginationOffset($page, $count);
	    $columns = ' * ';
	    $where   = ' WHERE status = :status ';
	    $params  = [
	        ':status' => 1
	    ];
	    if (strlen($account) > 0) {
	        $where .= ' AND wx_account = :wx_account ';
	        $params[':wx_account'] = $account;
	    }
	    if (strlen($sn) > 0) {
	        $where .= ' AND wx_sn = :wx_sn ';
	        $params[':wx_sn'] = $sn;
	    }
	    if (strlen($appid) > 0) {
	        $where .= ' AND wx_appid = :wx_appid ';
	        $params[':wx_appid'] = $appid;
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