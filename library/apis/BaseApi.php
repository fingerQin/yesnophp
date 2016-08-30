<?php
/**
 * 所有API接口基类。
 * @author winerQin
 * @date 2016-03-30
 */

namespace apis;

use common\YCore;
abstract class BaseApi {

	/**
	 * 请求参数。
	 * @var array
	 */
	protected $params = [];

	/**
	 * 结果。
	 * @var array
	 */
	protected $result = [];

	/**
	 * 构造方法。
	 * @param array $data 所有请求过来的参数。
	 * -- 1、合并提交的参数。
	 * -- 2、调用权限判断。
	 * -- 3、签名验证。
	 * -- 4、参数格式判断。
	 * -- 5、运行接口逻辑。
	 */
	public function __construct(&$data) {
		$this->timestamp = $_SERVER['REQUEST_TIME'];
		$this->params    = $data;
		$this->runService();
	}

	/**
	 * 业务逻辑。
	 * -- 关于接口的逻辑写在此方法中。
	 * @return void
	 */
	abstract protected function runService();

	/**
	 * 数据返回格式统一组装方法。
	 * @param int $code 错误码，必须是int类型。
	 * @param string $msg 提示信息。
	 * @param array $data 数据。
	 * @return void
	 */
	public function render($code, $msg, array $data = null) {
	    if (!is_int($code)) {
	        throw new \Exception ('code must is int');
	    }
	    $this->result = [
	        'errcode' => $code,
	        'errmsg'  => $msg
	    ];
	    if ($code == 0 && !is_null($data)) {
	        $this->result['data'] = $data;
	    }
	}
	
	/**
	 * 响应结果。
	 * @return string
	 */
	public function renderJson() {
	    return json_encode($this->result);
	}

	/**
	 * 从接口参数中获取一个整形数值。
	 * @param string $name 名称。
	 * @param int $default_value 默认值。
	 * @return int
	 */
	public function getInt($name, $default_value = null) {
		return YCore::getInt($this->params, $name, $default_value);
	}

	/**
	 * 从接口参数中获取一个字符串值。
	 * @param string $name 名称。
	 * @param string $default_value 默认值。
	 * @return string
	 */
	public function getString($name, $default_value = null) {
		return YCore::getString($this->params, $name, $default_value);
	}

	/**
	 * 从接口参数中获取一个浮点值。
	 * @param string $name 名称。
	 * @param float $default_value 默认值。
	 * @return float
	 */
	public function getFloat($name, $default_value = null) {
		return YCore::getFloat($this->params, $name, $default_value);
	}

	/**
	 * 从接口参数中获取一个浮点值。
	 * @param string $name 名称。
	 * @param array $default_value 默认值。
	 * @return array
	 */
	public function getArray($name, $default_value = null) {
		return YCore::getArray($this->params, $name, $default_value);
	}
}