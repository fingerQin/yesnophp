<?php
/**
 * 微信应用管理器。
 * @author winerQin
 * @date 2016-05-11
 */

namespace winer\WeChat;

use common\YCore;
use winer\WeChat\Pay\WxPayConfig;
use common\YUrl;
class WeChatApp {

    /**
     * 微信配置。
     * @var array
     */
    protected $config = [
        'appid'        => '', 		// 微信公众号APPID。
        'appsecret'    => '',		// 微信公众号appsercret。
        'token'        => '',		// 微信公众号token。
        'aeskey'       => '',		// 微信公众号aeskey。
        'debug'        => false,	// 调试模式。
    	'mch_id'       => '',		// 商户ID。商户号（必须配置，开户邮件中可查看）。
    	'mch_key'      => '',		// 商户支付密钥。参考开户邮件设置（必须配置，登录商户平台自行设置）。
    	'cert_path'    => '',		// 证书地址。（仅退款、撤销订单时需要，可登录商户平台下载）。
    	'cert_key'     => '',		// 证书密钥。
    	'report_level' => '',		// 接口调用上报等级。上报等级，0.关闭上报; 1.仅错误出错上报; 2.全量上报。
    	'proxy_host'   => '',		// 支付代理IP地址。
    	'proxy_port'   => '',		// 支付代理端口。
    ];

    /**
     * 开发者微信号。
     * --1、接收消息会保存到这个属性中。
     * @var String
     */
    public $ToUserName = null;

    /**
     * 发送方帐号（一个OpenID）。
     * --1、接收消息时会保存到这个属性中。
     * @var string
     */
    public $FromUserName = null;

    /**
     * 构造方法。
     * @param array $config 微信相关配置。
     */
    public function __construct($config) {
        $this->config = array_merge($this->config, $config);
        new WxPayConfig($this->config);
    }

    
    
    /**
     * 设置消息或事件。
     * @param string $message
     * @return Object
     */
    public function setMessage($message) {
    	libxml_disable_entity_loader(true);
    	$msgObj = simplexml_load_string($message, 'SimpleXMLElement', LIBXML_NOCDATA);
    	$this->ToUserName   = $msgObj->ToUserName;
    	$this->FromUserName = $msgObj->FromUserName;
    	return $msgObj;
    }

    /**
     * 创建用户分组。
     * @param array $name 分组名称。
     * @return number
     */
    public function createUserGroup($name) {
    	if (strlen($name) === 0) {
    		YCore::exception(-1, '分组名称不能为空');
    	}
    	$access_token = $this->getAccessToken();
    	$url = "https://api.weixin.qq.com/cgi-bin/groups/create?access_token={$access_token}";
    	$group_names = [
    			'group' => ['name' => $name]
    	];
    	$result = $this->requestCurl($url, 5, json_encode($group_names));
    	return $result['group']['id'];
    }

    /**
     * 微信菜单重命名。
     * @param number $group_id 分组ID。
     * @param string $name 分组名称。
     * @return boolean
     */
    public function editUserGroup($group_id, $name) {
    	$access_token = $this->getAccessToken();
    	$url = "https://api.weixin.qq.com/cgi-bin/groups/update?access_token={$access_token}";
    	$data = [
    			'group' => ['id' => $group_id, 'name' => $name]
    	];
    	$this->requestCurl($url, 5, json_encode($data));
    	return true;
    }

    /**
     * 删除用户分组。
     * @param number $group_id 分组ID。
     * @return boolean
     */
    public function deleteUserGroup($group_id) {
    	$access_token = $this->getAccessToken();
    	$url = "https://api.weixin.qq.com/cgi-bin/groups/get?access_token={$access_token}";
    	$data = [
    			'group' => ['id' => $group_id]
    	];
    	$this->requestCurl($url, 5, json_encode($data));
    	return true;
    }

    /**
     * 删除所有用户分组。
     * @return boolean
     */
    public function deleteAllUserGroup() {
    	$access_token = $this->getAccessToken();
    	$url = "https://api.weixin.qq.com/cgi-bin/groups/get?access_token={$access_token}";
    	$result = $this->requestCurl($url);
    	foreach ($result['groups'] as $group) {
    		$this->deleteUserGroup($group['id']);
    	}
    	return true;
    }

    /**
     * 设置用户分组。
     * @param string $openid 微信用户OPENID。
     * @param number $group_id 分组ID。
     * @return boolean
     */
    public function setUserGroup($openid, $group_id) {
    	$access_token = $this->getAccessToken();
    	$url = "https://api.weixin.qq.com/cgi-bin/groups/members/update?access_token={$access_token}";
    	$data = [
    		'openid'     => $openid,
    		'to_groupid' => $group_id
    	];
    	$result = $this->requestCurl($url, 5, json_encode($data));
    	return true;
    }

    /**
     * 创建默认微信公众号菜单。
     * @param array $menus 菜单数组。
     * @return boolean
     */
    public function createDefaultMenu($menus) {
    	$access_token = $this->getAccessToken();
    	$url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token={$access_token}";
    	$data = [
    			'button' => $menus,
    	];
    	$this->requestCurl($url, 5, json_encode($data, JSON_UNESCAPED_UNICODE));
    	return true;
    }

    /**
     * 创建微信公众号个性化菜单。
     * @param array $menus 菜单数组。
     * @param number $group_id 分组ID。
     * @return boolean
     */
    public function createIndividuationMenu($menus, $group_id) {
    	$access_token = $this->getAccessToken();
    	$url = "https://api.weixin.qq.com/cgi-bin/menu/addconditional?access_token={$access_token}";
    	$data = [
    			'button'    => $menus,
    			'matchrule' => [
    				'group_id' => $group_id
    			]
    	];
    	$result = $this->requestCurl($url, 5, json_encode($data, JSON_UNESCAPED_UNICODE));
    	return true;
    }

    /**
     * 删除微信公众号自定义菜单。
     */
    public function deleteMenu() {
    	$access_token = $this->getAccessToken();
    	$url = "https://api.weixin.qq.com/cgi-bin/menu/delete?access_token={$access_token}";
    	$this->requestCurl($url, 5);
    	return true;
    }

    /**
     * 获取微信公众号菜单。
     * @return array
     */
    public function getWeChatMenu() {
    	$access_token = $this->getAccessToken();
    	$url = "https://api.weixin.qq.com/cgi-bin/get_current_selfmenu_info?access_token={$access_token}";
    	$result = $this->requestCurl($url, 5);
    	return $result;
    }

    /**
     * 设置用户微信分组。
     * @param number $openid 微信用户openid。
     * @param number $group_id 微信分组ID。
     * @return boolean
     */
    public function setUserWeChatGroup($openid, $group_id) {
    	$access_token = $this->getAccessToken();
    	$url = "https://api.weixin.qq.com/cgi-bin/groups/members/update?access_token={$access_token}";
    	$data = [
    			'openid'     => $openid,
    			'to_groupid' => $group_id
    	];
    	$this->requestCurl($url, 5, json_encode($data, JSON_UNESCAPED_UNICODE));
    	return true;
    }

    /**
     * 创建一个网页授权URL链接。
     * @param string $redirect_url 回调URL。
     * @param string $scope 应用授权作用域，snsapi_base、snsapi_userinfo。
     * @param string $state 重定向后会带上state参数，开发者可以填写a-zA-Z0-9的参数值，最多128字节
     * @return string
     */
    public function createAuthorizeUrl($redirect_url, $scope, $state) {
    	$redirect_url = urlencode($redirect_url);
    	$appid = $this->config['appid'];
    	return "https://open.weixin.qq.com/connect/oauth2/authorize?appid={$appid}&redirect_uri={$redirect_url}&response_type=code&scope={$scope}&state={$state}#wechat_redirect";
    }

    /**
     * 向微信服务器发送消息。
     * @param Object $message 具体的消息对象。
     * @return void
     */
    public function sendMessage($message) {
    	$message->ToUserName   = $this->FromUserName;
    	$message->FromUserName = $this->ToUserName;
    	$message->CreateTime   = $_SERVER['REQUEST_TIME'];
    	$xml = $message->makeXML();
    	echo $xml;
    }

    /**
     * 以授权回调得到的code获取微信用户基本信息。
     * @param string $code
     * @return array
     */
    public function getByCodeToWeChatInfo($code) {
        $appid     = $this->config['appid'];
        $appsecret = $this->config['appsecret'];
        $api_url   = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$appid}&secret={$appsecret}&code={$code}&grant_type=authorization_code";
        $result    = $this->requestCurl($api_url);
        return $result;
    }

    /**
     * 检查接口签名是否正确。
     * @param string $wx_sn 系统分配给微信公众号的唯一编码。
     * @param string $signature 微信加密签名，signature结合了开发者填写的token参数和请求中的timestamp参数、nonce参数。
     * @param string $timestamp 时间戳
     * @param string $nonce 随机数
     * @return boolean
     */
    public function checkSignature($wx_sn, $signature, $timestamp, $nonce) {
    	$token = $this->config['token'];
    	$tmpArr = [$token, $timestamp, $nonce];
    	sort($tmpArr, SORT_STRING);
    	$tmpStr = implode($tmpArr);
    	$tmpStr = sha1($tmpStr);
    	if ($tmpStr == $signature) {
    		return true;
    	} else {
    		return false;
    	}
    }
    
    /**
     * 获取AccessToken。
     * @return string
     */
    public function getAccessToken() {
    	$appid     = $this->config['appid'];
    	$appsecret = $this->config['appsecret'];
        $yaf_environ = APP_ENVIRON;
        $cache = YCore::getCache();
        $cache_key = "wechat_{$yaf_environ}_{$appid}_access_token";
        $access_token = $cache->get($cache_key);
        if ($access_token) {
        	return $access_token;
        }
        $api_url   = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$appsecret}";
        $result    = $this->requestCurl($api_url);
        $access_token = $result['access_token'];
        $cache_time   = $result['expires_in'] - 60; // 减1分钟是担心临界值。
        $cache->set($cache_key, $access_token, $cache_time);
        return $access_token;
    }

    /**
     * 获取微信公众号JSSDK的。
     * @param string $url 发起支付所在的页面URL地址。
     * @return array
     */
    public function getSignPackage($url = '') {
    	$url = strlen($url) > 0 ? $url : YUrl::get_url();
    	$jsapiTicket = $this->getJsApiTicket();
    	$timestamp = $_SERVER['REQUEST_TIME'];
    	$nonceStr = YCore::create_randomstr(10);
    	$string = "jsapi_ticket={$jsapiTicket}&noncestr={$nonceStr}&timestamp={$timestamp}&url={$url}";
    	$signature = sha1($string);
    	$signPackage = [
    			"appId"     => $this->config['appid'],
    			"nonceStr"  => $nonceStr,
    			"timestamp" => $timestamp,
    			"url"       => $url,
    			"signature" => $signature,
    			//"rawString" => $string
    	];
    	return $signPackage;
    }

    /**
     * 获取微信公众号JSSDK的API ticket。
     * @return string
     */
    protected function getJsApiTicket() {
    	$cache = YCore::getCache();
    	$wx_jsapi_ticket = $cache->get('wx_jsapi_ticket');
    	if ($wx_jsapi_ticket) {
    		return $wx_jsapi_ticket;
    	} else {
    		$access_token = $this->getAccessToken();
    		$url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token={$access_token}";
    		$result = $this->requestCurl($url);
    		$wx_jsapi_ticket = $result['ticket'];
    		$cache->set('wx_jsapi_ticket', $wx_jsapi_ticket, $result['expires_in'] - 60);
    		return $wx_jsapi_ticket;
    	}
    }

    /**
     * 发送CURL请求[微信专用]。
     * @param string $url
     * @param number $timeout 超时时间。
     * @param array|string $post_data 当前此参数有值的时候代表POST请求。
     * @return string 接口返回的JSON。
     */
    protected function requestCurl($url, $timeout = 5, $post_data = null) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        if (!is_null($post_data)) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        }
        $response = curl_exec($ch);
        $errno = curl_errno($ch);
        curl_close($ch);
        if ($errno != 0) {
        	YCore::exception(-1, 'CURL请求失败');
        }
        if ($response === FALSE) {
        	YCore::exception(-1, 'CURL请求失败');
        }
        $result = json_decode($response, true);
        if (isset($result['errcode']) && $result['errcode'] > 0) {
        	YCore::exception(-1, $result['errmsg']);
        }
        return $result;
    }
}