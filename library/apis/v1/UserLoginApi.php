<?php
/**
 * 用户登录接口。
 * @author winerQin
 * @date 2016-04-30
 * @version 1.0
 */

namespace apis\v1;

use apis\BaseApi;
use services\UserService;

class UserLoginApi extends BaseApi {

	/**
	 * 逻辑处理。
	 * @see Api::runService()
	 * @return bool
	 */
	protected function runService() {
		$username = $this->getString('username');
		$password = $this->getString('password');
		$return = UserService::login($username, $password, 2);
		$this->render(0, '登录成功', $return);
	}
}