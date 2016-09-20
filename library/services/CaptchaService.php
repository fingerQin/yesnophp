<?php
/**
 * 验证码管理。
 * -- 1、验证码生成类\winer\Captcha采用的是缓存机制。不是SESSION会话机制。
 * -- 2、不使用SESSION会话机制，是因为后续的验证码使用要兼容APP这种无会话的模式。
 * -- 3、所有的验证码必须登录之后才可使用。
 * -- 4、如果想在登录时使用验证码，最好放弃。采用登录多次失败就暂时禁止登录。
 * @author winerQin
 * @date 2015-11-19
 */
namespace services;

class CaptchaService extends BaseService {

    /**
     * 获取一个验证码。
     * -- 1、验证码时效只有5分钟。
     *
     * @param int $user_id 用户ID。
     * @param int $position 验证码位置。
     * @return void
     */
    public static function getCode($user_id, $position) {
        $code_sn = $user_id . $position;
        $Verify  = new \winer\Captcha();
        $Verify->fontSize = 14;
        $Verify->length   = 4;
        $Verify->useNoise = false;
        $Verify->entry($code_sn);
    }

    /**
     * 验证码验证。
     *
     * @param int $user_id 用户ID。
     * @param int $position 验证码位置。
     * @param string $code 验证码。
     * @return boolean
     */
    public static function checkCode($user_id, $position, $code) {
        $code_sn = $user_id . $position;
        $verify = new \winer\Captcha();
        return $verify->check($code, $code_sn);
    }

}