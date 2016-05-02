<?php
/**
 * 公用异常处理。
 * @author winerQin
 * @date 2015-11-13
 */

namespace common\controllers;

use common\YCore;

class Error extends \common\controllers\Common {

	/**
	 * 也可通过$request->getException()获取到发生的异常
	 */
	public function errorAction($exception) {
		$errcode = $exception->getCode();
		$errmsg  = $exception->getMessage();

		// 默认异常类别。
		$errcode_type = -1;

		// 框架级别的异常code。
		$arr_framework_exception_code = [
				\Yaf\ERR\STARTUP_FAILED 	 => '2000001',
				\Yaf\ERR\ROUTE_FAILED		 => '2000002',
				\Yaf\ERR\DISPATCH_FAILED	 => '2000003',
				\Yaf\ERR\NOTFOUND\MODULE	 => '2000004',
				\Yaf\ERR\NOTFOUND\CONTROLLER => '2000005',
				\Yaf\ERR\NOTFOUND\ACTION  	 => '2000006',
				\Yaf\ERR\NOTFOUND\VIEW  	 => '2000007',
				\Yaf\ERR\CALL_FAILED  		 => '2000008',
				\Yaf\ERR\AUTOLOAD_FAILED  	 => '2000009',
				\Yaf\ERR\TYPE_ERROR  		 => '2000010',
		];
		// 框架级别的异常信息说明（用户友好）。
		$arr_framework_exception_msg = [
				\Yaf\ERR\STARTUP_FAILED 	 => '服务器繁忙,请稍候重试',
				\Yaf\ERR\ROUTE_FAILED		 => '服务器繁忙,请稍候重试',
				\Yaf\ERR\DISPATCH_FAILED	 => '服务器繁忙,请稍候重试',
				\Yaf\ERR\NOTFOUND\MODULE	 => 'URL地址有误',
				\Yaf\ERR\NOTFOUND\CONTROLLER => 'URL地址有误',
				\Yaf\ERR\NOTFOUND\ACTION  	 => 'URL地址有误',
				\Yaf\ERR\NOTFOUND\VIEW  	 => '服务器繁忙,请稍候重试',
				\Yaf\ERR\CALL_FAILED  		 => '服务器繁忙,请稍候重试',
				\Yaf\ERR\AUTOLOAD_FAILED  	 => '服务器繁忙,请稍候重试',
				\Yaf\ERR\TYPE_ERROR  		 => '服务器繁忙,请稍候重试',
		];

		if (strlen($errcode) == 7) { // 可识别的异常都是7位编号。
			$errcode_type = intval(substr($errcode, 0, 3));
		} else if (array_key_exists($errcode, $arr_framework_exception_code)) {
			$errcode_type = 200;
			$errmsg  = $arr_framework_exception_msg[$errcode];
			$errcode = $arr_framework_exception_code[$errcode];
		}

		// 根据不同的异常类型，写入不同的异常日志。
		switch ($errcode_type) {
			case 100:
				YCore::yaf_log(\models\Log::LOG_TYPE_SYSTEM, $errmsg, 0, 0, $errcode);
				break;
			case 200:
				YCore::yaf_log(\models\Log::LOG_TYPE_FRAMEWORK, $errmsg, 0, 0, $errcode);
				break;
			case 300:
				YCore::yaf_log(\models\Log::LOG_TYPE_PACKAGE, $errmsg, 0, 0, $errcode);
				break;
			case 400:
				YCore::yaf_log(\models\Log::LOG_TYPE_LANG, $errmsg, 0, 0, $errcode);
				break;
			case 500:
				YCore::yaf_log(\models\Log::LOG_TYPE_VALIDATOR, $errmsg, 0, 0, $errcode);
				break;
			case 600:
				YCore::yaf_log(\models\Log::LOG_TYPE_SERVICES, $errmsg, 0, 0, $errcode);
				break;
			case -1:
			default:
			    $errmsg  = "Error Code:{$errcode}\n<br \>Error Message:<br \>{$errmsg}";
			    $errcode = -1;
				YCore::yaf_log(\models\Log::LOG_TYPE_BUSY, $errmsg, 0, 0, -1);
				break;
		}
		if ($this->_request->isXmlHttpRequest()) {
			$data = [
					'errcode' => $errcode,
					'errmsg'  => $errmsg
			];
			echo json_encode($data);
			$this->end();
		} else {
			$this->error("{$errcode}::{$errmsg}", '', 0);
		}
	}
}