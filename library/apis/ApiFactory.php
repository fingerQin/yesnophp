<?php
/**
 * API请求调用全部在此工厂类中。
 * @author winerQin
 * @date 2016-03-30
 */

namespace apis;

class ApiFactory {

	/**
	 * 根据接口名称返回接口对象。
	 * -- 1、接口名称转类名称规则：user.login = UserLoginApi
	 * -- 2、当method参数为空的时候，要抛出异常给调用的人捕获处理。
	 * @param array $api_data 请求来的所有参数。
	 * @throws Exception
	 * @return Api
	 */
	public static function factory($api_data) {
		if (!isset($api_data['method']) || strlen($api_data['method']) === 0) {
			throw new \Exception ('method does not exist', 1100001);
		}
		if (!isset($api_data['v']) || strlen($api_data['v']) === 0 || is_numeric($api_data['v']) === false) {
			throw new \Exception ('version number is wrong', 1100002);
		}
		// 将method参数转换为实际的接口类名称。
		$api_name = $api_data['method'];
		$params = explode('.', $api_name);
		$classname = '';
		foreach($params as $param) {
			$classname .= ucfirst($param);
		}
		$version = $api_data['v'];
		$version = str_replace('.', '_', $version);
		// 只能通过此种方式才能通过变量形式new对象。
		$classname = "apis\\v{$version}\\{$classname}Api";
		if (strlen($api_name) && class_exists($classname)) {
			return new $classname($api_data);
		} else {
			throw new \Exception('Interface does not exist', 1100003);
		}
	}
}