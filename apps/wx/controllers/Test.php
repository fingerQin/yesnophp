<?php
/**
 * 测试controller。
 * @author winerQin
 * @date 2016-05-07
 */

class TestController extends \common\controllers\Guest {
    
    public function indexAction() {
        $this->end();
    }
}