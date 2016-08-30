<?php
/**
 * 微信支付API异常类
 * @author winerQin
 */

namespace winer\WeChat\Pay;

class WxPayException extends \Exception {
	public function errorMessage() {
		return $this->getMessage();
	}
}
