<?php
/**
 * 事务管理插件。
 * --1、Yaf框架会根据特有的类名后缀(Model、Controller、Plugin)进行自动加载。为避免这种情况请不要以这样的名称结尾。
 * @author winerQin
 * @date 2014-11-13
 */

namespace common\plugins;

class Transaction extends \Yaf\Plugin_Abstract {
    
    /**
     * 在路由之前触发。
     * -- 1、开启事务。
     * 
     * @param Yaf_Request_Abstract $request
     * @param Yaf_Response_Abstract $response
     */
    public function routerStartup(\Yaf\Request_Abstract $request, \Yaf\Response_Abstract $response) {
        $mysql = \Yaf\Registry::get('mysql');
        $mysql->beginTransaction();
    }
    
    /**
     * 分发循环结束之后触发。
     * -- 1、提交事务。因为能执行到这里说明没有任何异常。
     * 
     * @param Yaf_Request_Abstract $request
     * @param Yaf_Response_Abstract $response
     */
    public function dispatchLoopShutdown(\Yaf\Request_Abstract $request, \Yaf\Response_Abstract $response) {
        $mysql = \Yaf\Registry::get('mysql');
        $mysql->commit();
    }
}