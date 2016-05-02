<?php
/**
 * 用户数据表模型。
 * @author winerQin
 * @date 2015-11-09
 */

namespace models;

class UsersData extends DbBase {

	/**
	 * 表名。
	 * @var string
	 */
	protected $_table_name = 'ms_users_data';

	/**
	 * 初始化用户数据。
	 * @param number $user_id 用户ID。
	 * @return void
	 */
	public function initUserData($user_id) {
		$data = [
				'user_id'   => $user_id,
				'realname'  => '',
				'avatar'    => '',
				'signature' => ''
		];
		return $this->insert($data);
	}
}