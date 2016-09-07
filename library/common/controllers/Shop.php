<?php
/**
 * 商家中心公共controller。
 * --1、Yaf框架会根据特有的类名后缀(Model、Controller、Plugin)进行自动加载。为避免这种情况请不要以这样的名称结尾。
 * @author winerQin
 * @date 2016-06-07
 */

namespace common\controllers;

use services\ShopService;
use common\YCore;

class Shop extends User {
    
    /**
     * 商家ID。
     * 
     * @var number
     */
    protected $shop_id = 0;
    
    /**
     * 商家管理员类型。
     * 
     * @var string
     */
    protected $shop_admin_type = '';
    
    /**
     * 商家名称。
     * 
     * @var string
     */
    protected $shop_mobilephone = '';
    
    /**
     * 商家名称。
     * 
     * @var string
     */
    protected $shop_name = '';
    
    /**
     * 商家LOGO
     * 
     * @var string
     */
    protected $shop_logo = '';
    
    /**
     * 商家是否被系统锁定。
     * 
     * @var number
     */
    protected $is_lock = 0;
    
    /**
     * 前置方法
     * -- 1、登录权限判断。
     * 
     * @see \common\controllers\Common::init()
     */
    public function init() {
        parent::init();
        $module_name = $this->_request->getModuleName();
        $action_name = $this->_request->getActionName();
        $ctrl_name = $this->_request->getControllerName();
        try {
            $result = ShopService::checkShopAuth($this->user_id, $module_name, $ctrl_name, $action_name);
            $this->shop_id = $result['shop_id'];
            $this->shop_name = $result['shop_name'];
            $this->shop_logo = $result['shop_logo'];
            $this->shop_mobilephone = $result['mobilephone'];
            $this->shop_admin_type = $result['admin_type'];
            // 全局性数据assign。避免冲突加g_前缀。
            if (! $this->_request->isXmlHttpRequest()) {
                $this->_view->assign('g_shop_name', $this->shop_name);
                $this->_view->assign('g_shop_logo', $this->shop_logo);
                $this->_view->assign('g_shop_id', $this->shop_id);
                $this->_view->assign('g_is_lock', $this->is_lock);
                $this->_view->assign('g_mobilephone', $this->mobilephone);
            }
        } catch ( \Exception $e ) {
            YCore::exception($e->getCode(), $e->getMessage());
        }
    }
}