<?php
/**
 * 所有API接口基类。
 * @author winerQin
 * @date 2016-03-30
 */

namespace apis;

abstract class BaseApi {

	/**
	 * 接口响应状态码。
	 * @var number
	 */
	protected $errcode = '';

	/**
	 * 接口应用的数据。
	 * @var array
	 */
	protected $ret_data = [];

	/**
	 * 接口结果描述。
	 * @var string
	 */
	protected $errmsg = '';

	/**
	 * 请求参数。
	 * @var array
	 */
	protected $params = [];

	/**
	 * 构造方法。
	 * @param array $data 所有请求过来的参数。
	 * -- 1、合并提交的参数。
	 * -- 2、调用权限判断。
	 * -- 3、签名验证。
	 * -- 4、参数格式判断。
	 * -- 5、运行接口逻辑。
	 */
	public function __construct($data) {
		$this->timestamp = $_SERVER['REQUEST_TIME'];
		$this->errcode   = 0;
		$this->params    = $data;
		$this->runService();
	}

	/**
	 * 初始化成员属性ret_data。
	 * -- 只有当有错误码的时候才会进行初始化。否则不会进行任何操作。
	 */
	final protected function initRetData() {
		if ($this->errcode != 0) {
			$this->ret_data = [
					'errcode' => $this->errcode,
					'errmsg'  => $this->errmsg 
			];
		}
	}

	/**
	 * 业务逻辑。
	 * -- 关于接口的逻辑写在此方法中。
	 * @return void
	 */
	abstract protected function runService();

	/**
	 * 响应结果。
	 * -- 1、调用该方法生成接口文档规定格式的数据。
	 * -- 2、判断是否写日志，是则写日志到数据库表中。
	 * @return string
	 */
	abstract public function render();
}