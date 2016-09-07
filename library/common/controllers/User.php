<?php
/**
 * 用户公共controller。
 * --1、Yaf框架会根据特有的类名后缀(Model、Controller、Plugin)进行自动加载。为避免这种情况请不要以这样的名称结尾。
 * @author winerQin
 * @date 2015-11-13
 */

namespace common\controllers;

use services\UserService;
use common\YCore;
use common\YUrl;
class User extends Common {
    
    /**
     * 用户ID。
     * 
     * @var number
     */
    protected $user_id = 0;
    
    /**
     * 手机号码。
     * 
     * @var string
     */
    protected $mobilephone = '';
    
    /**
     * 用户名。
     * 
     * @var string
     */
    protected $username = '';
    
    /**
     * 用户类型。
     * 
     * @var string
     */
    protected $user_type = '';
    
    /**
     * 前置方法
     * -- 1、登录权限判断。
     * 
     * @see \common\controllers\Common::init()
     */
    public function init() {
        parent::init();
        try {
            $result = UserService::checkAuth(UserService::LOGIN_MODE_WEB);
            $this->user_id = $result['user_id'];
            $this->mobilephone = $result['mobilephone'];
            $this->username = $result['username'];
            $this->user_type = $result['user_type'];
        } catch ( \Exception $e ) {
            if ($this->_request->isXmlHttpRequest()) {
                YCore::exception($e->getCode(), $e->getMessage());
            } else {
                $default_redirect_url = YUrl::get_url();
                $redirect_url = $this->getString('redirect_url', $default_redirect_url);
                $this->redirect(YUrl::createAccountUrl('', 'Public', 'Login', [
                        'redirect_url' => $redirect_url 
                ]));
            }
        }
    }
}