<?php
/**
 * 回调基础类
 * @author winerQin
 * @date 2016-05-11
 */

namespace winer\WeChat\Pay;

class WxPayNotifyReply extends WxPayDataBase {
    
    /**
     * 设置错误码 FAIL 或者 SUCCESS
     * 
     * @param string
     * @return void
     */
    public function SetReturn_code($return_code) {
        $this->values['return_code'] = $return_code;
    }
    
    /**
     * 获取错误码 FAIL 或者 SUCCESS
     * 
     * @return string $return_code
     * @return void
     */
    public function GetReturn_code() {
        return $this->values['return_code'];
    }
    
    /**
     * 设置错误信息
     * 
     * @param string $return_code
     */
    public function SetReturn_msg($return_msg) {
        $this->values['return_msg'] = $return_msg;
    }
    
    /**
     * 获取错误信息
     * 
     * @return string
     */
    public function GetReturn_msg() {
        return $this->values['return_msg'];
    }
    
    /**
     * 设置返回参数
     * 
     * @param string $key
     * @param string $value
     */
    public function SetData($key, $value) {
        $this->values[$key] = $value;
    }
}