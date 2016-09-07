<?php
/**
 * 普通用户注册接口。
 * @author winerQin
 * @date 2016-05-27
 * @version 1.0
 */

namespace apis\v1;

use apis\BaseApi;
use services\UserService;

class UserRegisterApi extends BaseApi {
    
    /**
     * 逻辑处理。
     * 
     * @see Api::runService()
     * @return bool
     */
    protected function runService() {
        $mobilephone = $this->getString('mobilephone');
        $password = $this->getString('password');
        $code = $this->getString('code');
        $user_type = UserService::USER_TYPE_NORMAL;
        UserService::mobilephoneRegister($user_type, $mobilephone, $password, $code);
        $this->render(0, '注册成功');
    }
}