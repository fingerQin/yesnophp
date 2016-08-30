<?php
/**
 * 应用自身的引导程序。
 * -- 1、当与公共引导程序不一样则可以在这里自行配置。
 * @author winerQin
 * @date 2015-11-13
 */

class Bootstrap extends \common\Bootstrap {

    /**
     * 路由协议注册。
     * -- 1、之所以单独放在frontend,是希望不要影响其他应用。
     * @param \Yaf\Dispatcher $dispatcher
     */
    public function _initRoute(\Yaf\Dispatcher $dispatcher) {
        $config = \Yaf\Application::app()->getConfig();
        $router = \Yaf\Dispatcher::getInstance()->getRouter();
        $router->addConfig($config->routes);
    }
}