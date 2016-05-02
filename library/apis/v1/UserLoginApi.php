<?php
/**
 * 用户登录API接口。
 * @author winer
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
		if ($this->errcode != 0) {
			return false;
		}
		$username = $this->params['username'];
		$password = $this->params['password'];
		$type     = $this->params['type'];
		$return = UserService::directLogin($username, $password, $type);
		$this->errcode  = $return['errcode'];
		$this->errmsg   = $return['errmsg'];
		$this->ret_data = $return;
		return true;
	}

	/**
	 * 响应结果。
	 * @return string
	 */
	public function render() {
		$this->initRetData();
		return json_encode($this->ret_data);
	}
}