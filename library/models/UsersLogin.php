<?php
/**
 * 用户登录历史表。
 * @author winerQin
 * @date 2015-11-14
 */

namespace models;

class UsersLogin extends DbBase {

	/**
	 * 表名。
	 * @var string
	 */
	protected $_table_name = 'ms_users_login';

	/**
	 * 添加登录记录。
	 * @param number $user_id 用户ID。
	 * @param number $login_time 登录时间。时间戳。
	 * @param string $login_ip 登录IP。
	 * @param string $login_entry 登录入口。
	 * @return bool
	 */
	public function addLoginRecord($user_id, $login_time, $login_ip, $login_entry) {
		$data = [
				'user_id'     => $user_id,
				'login_time'  => $login_time,
				'login_ip'    => $login_ip,
				'login_entry' => $login_entry
		];
		$insert_id = $this->insert($data);
		return $insert_id > 0 ? true : false;
	}

	/**
	 * 获取用户登录记录。
	 * @param number $user_id 用户ID。
	 * @param number $start_time 开始时间。时间戳。
	 * @param number $end_time 结束时间。时间戳。
	 * @return array
	 */
	public function getUserLoginRecord($user_id, $start_time, $end_time) {
		$mysql = $this->link;
		$sql = "SELECT * FROM {$this->_table_name} WHERE user_id = :user_id AND login_time BETWEEN :start_time AND :end_time";
		$params = [
				':user_id'    => $user_id,
				':start_time' => $start_time,
				':end_time'   => $end_time
		];
		$sth = $this->link->prepare($sql, $this->prepare_attr);
		$sth->execute($params);
		$list = $sth->fetchAll();
		return $list ? $list : [];
	}
}