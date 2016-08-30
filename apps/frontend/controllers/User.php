<?php
/**
 * 用户相关操作。
 * @author winerQin
 * @date 2016-06-10
 */

class UserController extends \common\controllers\User {

    public function indexAction() {
        echo '登录成功';
        $this->end();
    }

}