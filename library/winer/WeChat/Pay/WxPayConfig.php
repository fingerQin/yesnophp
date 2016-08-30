<?php
/**
 * 配置账号信息。
 * @author winerQin
 * @date 2016-05-11
 */

namespace winer\WeChat\Pay;

class WxPayConfig {

    /**
     * 绑定支付的APPID（必须配置，开户邮件中可查看）
     * @var string
     */
	public static $appid = '';

	/**
	 * 商户号（必须配置，开户邮件中可查看）
	 * @var string
	 */
	public static $mch_id = '';

	/**
	 * 商户支付密钥，参考开户邮件设置（必须配置，登录商户平台自行设置）
	 * -- 设置地址：https://pay.weixin.qq.com/index.php/account/api_cert
	 * @var string
	 */
	public static $mch_key = '';

	/**
	 * 公众帐号secert（仅JSAPI支付的时候需要配置， 登录公众平台，进入开发者中心可设置）
	 * -- 获取地址：https://mp.weixin.qq.com/advanced/advanced?action=dev&t=advanced/dev&token=2005451881&lang=zh_CN
	 * @var string
	 */
	public static $appsecret = '';

	/**
	 * 证书地址。
	 * --1、证书路径,注意应该填写绝对路径（仅退款、撤销订单时需要，可登录商户平台下载
	 * --2、API证书下载地址：https://pay.weixin.qq.com/index.php/account/api_cert，下载之前需要安装商户操作证书）
	 * @var string
	 */
	public static $cert_path = '';

	/**
	 * 证书密钥地址。
	 * @var string
	 */
	public static $cert_key = '';

	/**
	 * TODO：这里设置代理机器，只有需要代理的时候才设置，不需要代理，请设置为0.0.0.0和0
	 * 本程序通过curl使用HTTP POST方法，此处可修改代理服务器，
	 * 默认CURL_PROXY_HOST=0.0.0.0和CURL_PROXY_PORT=0，此时不开启代理（如有需要才设置）
	 * @var string
	 */
	public static $curl_proxy_host = '';
	public static $curl_proxy_port = '';

	/**
	 * TODO：接口调用上报等级，默认仅错误上报（注意：上报超时间为【1s】，上报无论成败【永不抛出异常】，
	 * 不会影响接口调用流程），开启上报之后，方便微信监控请求调用的质量，建议至少开启错误上报。
	 * 上报等级，0.关闭上报; 1.仅错误出错上报; 2.全量上报
	 * @var int
	 */
	public static $report_level = 1;

	/**
	 * 构造方法。
	 * -- 初始化配置。
	 * @param array $config
	 * @return void
	 */
	public function __construct($config) {
		self::$appid           = $config['appid'];
		self::$appsecret       = $config['appsecret'];
		self::$mch_id          = $config['mch_id'];
		self::$mch_key         = $config['mch_key'];
		self::$cert_key        = $config['cert_key'];
		self::$cert_path       = $config['cert_path'];
		self::$curl_proxy_host = $config['proxy_host'];
		self::$curl_proxy_port = $config['proxy_port'];
		self::$report_level    = $config['report_level'];
	}
}