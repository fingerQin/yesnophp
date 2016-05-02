<?php
/**
 * 微信公众号接口操作业务封装。
 * @author winerQin
 * @date 2016-04-13
 */

namespace services\WeChat;

use services\BaseService;
use common\YCore;
use models\WxAccount;

class InterfaceService extends BaseService {

    /**
     * 使用微信公众号菜单生效。
     * @param string $wx_sn 系统分配给微信公众号的唯一编号。
     * @param array $data 菜单数据。
     * @return boolean
     */
    public static function setWeChatMenu($wx_sn, array $data) {
        $access_token = self::getAccessToken($wx_sn);
        $api_url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token={$access_token}";
        $ret_json = self::sendPostRequest($api_url, json_encode($data, JSON_UNESCAPED_UNICODE));
        $result = json_decode($ret_json, true);
        if ($result['errcode'] != 0) {
            YCore::throw_exception(-1, $result['errmsg']);
        }
        return true;
    }

    /**
     * 检查接口签名是否正确。
     * @param string $wx_sn 系统分配给微信公众号的唯一编码。
     * @param string $signature 微信加密签名，signature结合了开发者填写的token参数和请求中的timestamp参数、nonce参数。
     * @param string $timestamp 时间戳
     * @param string $nonce 随机数
     * @return boolean
     */
    public static function checkSignature($wx_sn, $signature, $timestamp, $nonce) {
        $wechat_setting = self::getWechatSetting($wx_sn);
        $token = $wechat_setting['token'];
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
     * 获取微信配置。
     * @param string $wx_sn 系统分配给微信公众号的唯一编号。
     * @return array
     */
    protected static function getWechatSetting($wx_sn) {
        $wx_account_model = new WxAccount();
        $wx_account_info = $wx_account_model->fetchOne([], ['wx_sn' => $wx_sn, 'status' => 1]);
        if (empty($wx_account_info)) {
            YCore::throw_exception(-1, '微信账号已经停止服务');
        }
        return [
            'appid'     => $wx_account_info['wx_appid'],
            'appsecret' => $wx_account_info['wx_appsecret'],
            'aeskey'    => $wx_account_info['wx_aeskey'],
            'token'     => $wx_account_info['wx_token'],
            'type'      => $wx_account_info['wx_type'],
            'auth'      => $wx_account_info['wx_auth']
        ];
    }

    /**
     * 获取微信access_token。
     * @param string $wx_sn 系统分配给微信公众号的唯一编号。
     * @return string
     */
    protected static function getAccessToken($wx_sn) {
        $yaf_environ = APP_ENVIRON;
        $cache = YCore::getCache();
        $cache_key = "wechat_{$yaf_environ}_access_token";
        $access_token = $cache->get($cache_key);
        if ($access_token) {
            return $access_token;
        }
        $wechat_setting = self::getWechatSetting($wx_sn);
        $appid     = $wechat_setting['appid'];
        $appsecret = $wechat_setting['appsecret'];
        $api_url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$appsecret}";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        $response = curl_exec($ch);
        $errno = curl_errno($ch);
        curl_close($ch);
        if ($errno != 0) {
            YCore::throw_exception(-1, 'CURL请求失败');
        }
        $result = json_decode($response, true);
        if (array_key_exists('errcode', $result)) {
            YCore::throw_exception(-1, $result['errmsg']);
        }
        $access_token = $result['access_token'];
        $cache_time = $result['expires_in'] - 60; // 减1分钟是担心临界值。
        $cache->set($cache_key, $access_token, $cache_time);
        return $access_token;
    }

    /**
     * 发送POST请求且返回接口返回来的数据。
     * @param string $api_url 微信接口URL地址。
     * @param string $json_data 微信接口需要的json数据。
     * @return string
     */
    protected static function sendPostRequest($api_url, $json_data) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
        $response = curl_exec($ch);
        $errno = curl_errno($ch);
        curl_close($ch);
        if ($errno != 0) {
            YCore::throw_exception(-1, 'CURL请求失败');
        }
        return $response;
    }
}