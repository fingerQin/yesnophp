<?php
/**
 * 短信业务封装。
 * @author winerQin
 * @date 2016-05-05
 */
namespace services;

use models\SmsLog;
use winer\Validator;
use common\YCore;

class SmsService extends BaseService {

    /**
     * 发送。
     * @var number
     */
    const OP_TYPE_SEND = 1;

    /**
     * 验证。
     * @var number
     */
    const OP_TYPE_CHECK = 2;

    /**
     * 短信验证码类型。
     *
     * @var string
     */
    const SMS_TYPE_REGISTER = 'register'; // 注册。

    const SMS_TYPE_FINDPWD = 'findpwd'; // 找回密码。

    const SMS_TYPE_EDITPHONE = 'editphone'; // 修改手机。

    /**
     * 各种初始化操作放这里。
     *
     * @return void
     */
    private static function __init() {
        YCore::serviceDegradation('sms');
    }

    /**
     * 发送短信验证码。
     * -- 1、验证码时效为10分钟。
     * -- 2、每种类型的验证码必须60秒后才能发送第二次。
     *
     * @param string $sms_type 短信类型。register:注册、findpwd:找回密码。
     * @param string $mobilephone 手机号码。
     * @param number $code_len 验证码长度。
     * @return boolean
     */
    public static function sendSmsCode($sms_type, $mobilephone, $code_len = 4) {
        self::__init();
        if (Validator::is_mobilephone($mobilephone) === false) {
            YCore::exception(-1, '手机号码格式不正确');
        }
        if (Validator::is_number_between($code_len, 4, 10) === false) {
            YCore::exception(-1, '手机验证码长度必须4~10之间');
        }
        $cache = YCore::getCache();
        $cache_lock_key = "sms_send_code_{$sms_type}_lock_{$mobilephone}";
        if ($cache->get($cache_lock_key)) {
            YCore::exception(-1, '获取验证码时间间隔过短');
        }
        $code = YCore::random($code_len, '0123456789');
        $sms_tpl = YCore::dict('sms_tpl', $sms_type);
        if (strlen($sms_tpl) === 0) {
            YCore::exception(-1, '短信模板内容为空');
        }
        $sms_txt = str_replace('#code#', $code, $sms_tpl);
        $ok = self::sendCode($mobilephone, $sms_txt, $code);
        if (!$ok) {
            YCore::exception(-1, '短信发送失败,请稍候重试');
        }
        $cache_code_key = "sms_send_code_{$sms_type}_code_{$mobilephone}";
        $cache->set($cache_code_key, $code, 600); // 验证码有效期10分钟。
        $cache->set($cache_lock_key, 1, 60); // 验证码必须60秒才能发送第二次。
        return true;
    }

    /**
     * 验证码短信验证码是否正确。
     * -- 1、验证码超过10次将自动失效。
     *
     * @param string $sms_type 验证码类型。register:注册、findpwd:找回密码。
     * @param string $mobilephone 手机号。
     * @param string $code 验证码。
     * @param boolean $is_destroy 验证成功是否立即销毁。
     * @return boolean
     */
    public static function valiCode($sms_type, $mobilephone, $code, $is_destroy = true) {
        self::__init();
        if (Validator::is_mobilephone($mobilephone) === false) {
            YCore::exception(-1, '手机号码格式不正确');
        }
        $cache = YCore::getCache();
        $cache_code_key = "sms_send_code_{$sms_type}_code_{$mobilephone}";
        $cache_code_vali_times = "sms_send_code_{$sms_type}_code_times_{$mobilephone}";
        $cache_code = $cache->get($cache_code_key);
        if (strlen($cache_code) === 0) {
            YCore::exception(-1, '短信验证码已经过期');
        }
        $times = $cache->incr($cache_code_vali_times);
        if ($times > 10) {
            $cache->delete($cache_code_key);
            $cache->delete($cache_code_vali_times);
            YCore::exception(-1, '验证次数超过10次,验证码自动失效');
        }
        if ($cache_code != $code) {
            YCore::exception(-1, '短信验证码不正确');
        } else {
            if ($is_destroy) {
                $cache->delete($cache_code_key);
            }
            $cache->delete($cache_code_vali_times); // 如果验证码已经正确，则要将此验证码的验证次数key清理掉。
            self::_writeLog('', self::OP_TYPE_CHECK, $mobilephone, $code, $is_destroy);
            return true;
        }
    }

    /**
     * 发送短信验证码。
     *
     * @param string $mobilephone 手机号。
     * @param string $sms_txt 短信内容。
     * @param string $code 验证码。如果是普通的短信则为空字符串。
     * @return boolean
     */
    protected static function sendCode($mobilephone, $sms_txt, $code = '') {
        self::__init();
        $sms_key = YCore::config('luosimao_sms_key');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://sms-api.luosimao.com/v1/send.json");
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, "api:key-{$sms_key}");
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, [
            'mobile' => $mobilephone,
            'message' => $sms_txt
        ]);
        $res = curl_exec($ch);
        $errno = curl_errno($ch);
        curl_close($ch);
        if ($errno) {
            return false;
        }
        $data = json_decode($res, true);
        if ($data['error'] != 0) {
            YCore::log(-1, "短信接口调用失败:{$data['msg']}");
            return false;
        } else {
            return self::_writeLog($sms_txt, self::OP_TYPE_SEND, $mobilephone, $code, 0);
            return true;
        }
    }

    /**
     * 写短信发送与验证日志。
     *
     * @param string $sms_txt 短信发送内容。
     * @param number $op_type 操作类型：1发送。2验证。
     * @param string $mobilephone 手机号码。
     * @param string $code 验证码。
     * @param boolean $is_destroy 是否验证码成功立即销毁。操作类型为2时有效。
     * @return void
     */
    protected static function _writeLog($sms_txt, $op_type, $mobilephone, $code, $is_destroy) {
        $sms_log_model = new SmsLog();
        $data = [
            'op_type'      => $op_type,
            'mobilephone'  => $mobilephone,
            'sms_txt'      => $sms_txt,
            'sms_code'     => $code,
            'is_destroy'   => $is_destroy ? 1 : 0,
            'created_time' => $_SERVER['REQUEST_TIME']
        ];
        return $sms_log_model->insert($data);
    }
}