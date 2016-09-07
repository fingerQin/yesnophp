<?php
/**
 * 页面缓存插件。
 * --1、Yaf框架会根据特有的类名后缀(Model、Controller、Plugin)进行自动加载。为避免这种情况请不要以这样的名称结尾。
 * @author winerQin
 * @date 2014-11-13
 */

namespace common\plugins;

class PageCache extends \Yaf\Plugin_Abstract {
    
    /**
     * 在路由之前触发。
     * 
     * @param Yaf_Request_Abstract $request
     * @param Yaf_Response_Abstract $response
     */
    public function routerStartup(\Yaf\Request_Abstract $request, \Yaf\Response_Abstract $response) {
    }
    
    /**
     * 路由结束之后触发。
     * 
     * @param Yaf_Request_Abstract $request
     * @param Yaf_Response_Abstract $response
     */
    public function routerShutdown(\Yaf\Request_Abstract $request, \Yaf\Response_Abstract $response) {
        $model_name = $request->getModuleName();
        $action_name = $request->getActionName();
        $controller_name = $request->getControllerName();
        if ($model_name == 'Index' && $action_name == 'test' && $action_name == 'test') {
            // $html_body = $response->getBody();
            // $ssdb = Yaf_Registry::get('ssdb');
            // $ssdb->set('test', $html_body);
        }
    }
    
    /**
     * 分发循环结束之后触发。
     * 
     * @param Yaf_Request_Abstract $request
     * @param Yaf_Response_Abstract $response
     */
    public function dispatchLoopShutdown(\Yaf\Request_Abstract $request, \Yaf\Response_Abstract $response) {
        // $html_body = $response->getBody();
        // $ssdb = Yaf_Registry::get('ssdb');
        // $ssdb->set('test', $html_body);
    }
}