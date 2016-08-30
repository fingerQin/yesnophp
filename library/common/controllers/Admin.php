<?php
/**
 * 管理后台公共controller。
 * --1、Yaf框架会根据特有的类名后缀(Model、Controller、Plugin)进行自动加载。为避免这种情况请不要以这样的名称结尾。
 * @author winerQin
 * @date 2015-11-13
 */

namespace common\controllers; 

use services\AdminService;
use common\YCore;
use common\YUrl;
class Admin extends Common {

	/**
	 * 管理员ID。
	 * @var number
	 */
	protected $admin_id = 0;

	/**
	 * 管理员真实姓名。
	 * @var string
	 */
	protected $realname = '';
	
	/**
	 * 管理员账号。
	 * @var string
	 */
	protected $username = '';

	/**
	 * 管理员手机号码。
	 * @var string
	 */
	protected $mobilephone = '';
	
	/**
	 * 管理员角色ID。
	 * @var number
	 */
	protected $roleid = 0;

	/**
	 * 前置方法
	 * -- 1、登录权限判断。
	 * @see \common\controllers\Common::init()
	 */
	public function init() {
		parent::init();
		try {
		    $module_name = $this->_request->getModuleName();
		    $action_name = $this->_request->getActionName();
		    $ctrl_name   = $this->_request->getControllerName();
		    $admin_info  = AdminService::checkAuth($module_name, $ctrl_name, $action_name);
		    $this->admin_id    = $admin_info['admin_id'];
		    $this->username    = $admin_info['username'];
		    $this->realname    = $admin_info['realname'];
		    $this->mobilephone = $admin_info['mobilephone'];
		    $this->roleid      = $admin_info['roleid'];
		} catch (\Exception $e) {
		    if ($e->getCode() == '6002104' || $e->getCode() == '6004003' || $e->getCode() == '6002103') {
		        if ($this->_request->isXmlHttpRequest()) {
		            YCore::exception($e->getCode(), $e->getMessage());
		        } else {
		            $this->redirect(YUrl::createBackendUrl('', 'Public', 'Login'));
		        }
		    } else {
		        YCore::exception($e->getCode(), $e->getMessage());
		    }
		}
	}
}