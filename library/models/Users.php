<?php
/**
 * 用户表模型。
 * @author winerQin
 * @date 2015-11-05
 */

namespace models;

class Users extends DbBase {

	/**
	 * 表名。
	 * @var string
	 */
	protected $_table_name = 'ms_users';

	/**
	 * 获取用户列表。
	 * @param string $username 用户账号。
	 * @param string $mobilephone 手机号码。
	 * @param unknown $is_verify_mobilephone 手机号是否验证。-1全部、1通过、0否。
	 * @param string $starttime 开始注册时间。
	 * @param string $endtime 截止注册时间。
	 * @param number $page 当前页码。
	 * @param number $count 每页显示条数。
	 * @return array
	 */
	public function getList($username = '', $mobilephone = '', $is_verify_mobilephone = -1, $starttime = '', $endtime = '', $page = 1, $count = 20) {
	    $offset  = $this->getPaginationOffset($page, $count);
	    $columns = ' * ';
	    $where   = ' WHERE 1 ';
	    $params  = [];
	    if (strlen($username) > 0) {
	        $where .= ' AND username LIKE :username ';
	        $params[':username']  = "{$username}%"; // 为了性能，以及常规查询并不会查后缀。
	    }
	    if (strlen($mobilephone) > 0) {
	        $where .= ' AND mobilephone = :mobilephone ';
	        $params[':mobilephone'] = $mobilephone;
	    }
	    if ($is_verify_mobilephone != -1) {
	        $where .= ' AND is_verify_mobilephone = :is_verify_mobilephone ';
	        $params[':is_verify_mobilephone'] = $is_verify_mobilephone;
	    }
	    if (strlen($starttime) > 0) {
	        $where .= ' AND reg_time > :starttime ';
	        $params[':starttime'] = strtotime($starttime);
	    }
	    if (strlen($endtime) > 0) {
	        $where .= ' AND reg_time < :endtime ';
	        $params[':endtime'] = strtotime($endtime);
	    }
	    $order_by = ' ORDER BY user_id ASC ';
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

	/**
	 * 添加用户。
	 * @param string $username 账号。
	 * @param string $password 密码。经过特定规则加密后的密文。
	 * @param string $salt 密码盐。
	 * @return number 添加成功返回用户ID
	 */
	public function addUser($username, $password, $salt) {
		$data = [
				'username' => $username,
				'password' => $password,
				'salt'     => $salt,
				'reg_time' => $_SERVER['REQUEST_TIME']
		];
		return $this->insert($data);
	}

	/**
	 * 根据账号读取用户表数据。
	 * @param string $username 账号。
	 * @return array
	 */
	public function getUserOfByUsername($username) {
		return $this->fetchOne([], ['username' => $username]);
	}

	/**
	 * 根据手机号读取用户表数据。
	 * @param string $mobilephone 手机号码。
	 * @return array
	 */
	public function getUserOfByMobilephone($mobilephone) {
		return $this->fetchOne([], ['mobilephone' => $mobilephone]);
	}

	/**
	 * 根据用户ID读取用户表数据。
	 * @param number $user_id 用户ID。
	 * @return array
	 */
	public function getUserOfByUserId($user_id) {
		return $this->fetchOne([], ['user_id' => $user_id]);
	}
}