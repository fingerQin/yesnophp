<?php
/**
 * 消息抽象类。
 * @author winerQin
 * @date 2016-05-12
 */

namespace winer\WeChat\Message;

class AbstractMessage {

	/**
	 * 接收方帐号（收到的OpenID）。
	 * @var string
	 */
	public $ToUserName   = '';

	/**
	 * 开发者微信号。
	 * @var string
	 */
	public $FromUserName = '';

	/**
	 * 消息创建时间 （整型）。
	 * @var string
	 */
	public $CreateTime   = '';

	/**
	 * 消息类型。
	 * @var string
	 */
	protected $MsgType      = '';

	/**
	 * 非公用参数部分。
	 * @var string
	 */
	protected $propertys = [];

	/**
	 * 创建当前消息对象的XML。
	 * @return string
	 */
	public function makeXML() {
		$xml = "<xml>"
			 . "<ToUserName><![CDATA[{$this->ToUserName}]]></ToUserName>"
			 . "<FromUserName><![CDATA[$this->FromUserName]]></FromUserName>"
			 . "<CreateTime>{$this->CreateTime}</CreateTime>"
			 . "<MsgType><![CDATA[{$this->MsgType}]]></MsgType>";
		foreach ($this->propertys as $prop) {
			$prop_val = $this->$prop;
			$xml .= "<{$prop}><![CDATA[{$prop_val}]]></{$prop}>";
		}
		$xml .= "</xml>";
		return $xml;
	}
}