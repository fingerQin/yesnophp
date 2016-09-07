<?php
/**
 * 公共/公开的页面。
 * -- 1、不需要权限就能访问的页面。
 * -- 2、登录、注册、注册协议。
 * @author winerQin
 * @date 2016-06-23
 */

use services\UserService;
use common\YUrl;

class PublicController extends \common\controllers\Guest {
    
    /**
     * 用户登录。
     */
    public function loginAction() {
        if ($this->_request->isPost()) {
            $username = $this->getString('username');
            $password = $this->getString('password');
            $redirect_url = $this->getString('redirect_url', '');
            if (strlen($redirect_url) === 0) {
                $redirect_url = YUrl::createAccountUrl('', 'Index', 'Index');
            }
            UserService::login($username, $password, 1);
            $this->redirect($redirect_url);
        }
        $redirect_url = $this->getString('redirect_url', '');
        $this->_view->assign('redirect_url', $redirect_url);
    }
    
    /**
     * 用户注册。
     */
    public function registerAction() {
        if ($this->_request->isPost()) {
            $username = $this->getString('username');
            $password = $this->getString('password');
            $redirect_url = $this->getString('redirect_url', '');
            if (strlen($redirect_url) === 0) {
                $redirect_url = YUrl::createFrontendUrl('Index', 'User', 'Index');
            }
            UserService::register(UserService::USER_TYPE_NORMAL, $username, $password);
            $this->redirect($redirect_url);
        }
    }
    
    /**
     * 退出登录。
     */
    public function logoutAction() {
        UserService::logout();
        $this->redirect(YUrl::createAccountUrl('', 'Public', 'Login'));
    }
    
    /**
     * 用户协议。
     */
    public function protocolAction() {
    
    }
}