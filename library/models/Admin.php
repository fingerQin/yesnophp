<?php
/**
 * 管理员表。
 * @author winerQin
 * @date 2015-11-17
 */

namespace models;

class Admin extends DbBase {

	/**
	 * 表名。
	 * @var string
	 */
	protected $_table_name = 'ms_admin';

	/**
	 * 根据账号读取管理员数据。
	 * @param string $username 账号。
	 * @return array
	 */
	public function getUserOfByUsername($username) {
		return $this->fetchOne([], ['username' => $username]);
	}

	/**
	 * 根据管理员ID读取用户表数据。
	 * @param number $admin_id
	 * @return array
	 */
	public function getUserOfByAdminId($admin_id) {
		return $this->fetchOne([], ['admin_id' => $admin_id]);
	}

	/**
	 * 修改管理员基本信息。
	 * @param number $admin_id 管理员ID。
	 * @param string $realname 真实姓名。
	 * @param string $mobilephone 手机号码。
	 * @return boolean
	 */
	public function editInfo($admin_id, $realname, $mobilephone) {
		$where = ['admin_id' => $admin_id];
		$data = [
				'realname'    => $realname,
				'mobilephone' => $mobilephone
		];
		return $this->update($data, $where);
	}
	
	/**
	 * 更改管理员密码。
	 * @param number $admin_id 管理员ID。
	 * @param string $password 加密后的密码。
	 * @return boolean
	 */
	public function editPwd($admin_id, $password) {
		$data = [
				'password' => $password,
		];
		$where = [
				'admin_id' => $admin_id
		];
		return $this->update($data, $where);
	}

	/**
	 * 获取管理员列表。
	 * @param string $keyword 查询关键词。（账号、手机、姓名）。
	 * @param number $page 页码。
	 * @param number $count 每页显示条数。
	 * @return array
	 */
	public function getAdminList($keyword = '', $page = 1, $count = 10) {
	    $offset  = $this->getPaginationOffset($page, $count);
	    $columns = ' a.admin_id,a.realname,a.username,a.password,a.mobilephone,a.roleid,a.lastlogintime,a.created_time,b.rolename ';
	    $where   = ' WHERE a.status = :status ';
	    $params = [
	        ':status' => 1,
	    ];
	    if (strlen($keyword) > 0) {
	        $where .= ' AND ( a.realname LIKE :realname OR a.username LIKE :username OR a.mobilephone = :mobilephone )';
	        $params[':realname']    = "%{$keyword}%";
	        $params[':username']    = "%{$keyword}%";
	        $params[':mobilephone'] = "%{$keyword}%";
	    }
	    $order_by = ' ORDER BY a.admin_id DESC ';
	    $admin_role_model = new AdminRole();
	    $admin_role_table_name = $admin_role_model->getTableName();
	    $sql = "SELECT COUNT(1) AS count FROM {$this->_table_name} AS a LEFT JOIN {$admin_role_table_name} AS b ON(a.roleid=b.roleid) {$where}";
	    $sth = $this->link->prepare($sql);
	    $sth->execute($params);
	    $count_data = $sth->fetch();
	    $total  = $count_data ? $count_data['count'] : 0;
	    $sql = "SELECT {$columns} FROM {$this->_table_name} AS a LEFT JOIN {$admin_role_table_name} AS b ON(a.roleid=b.roleid) {$where} {$order_by} LIMIT {$offset},{$count}";
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