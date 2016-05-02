<?php
/**
 * 微信默认controller。
 * @author winerQin
 * @date 2016-03-02
 */

use services\WeChat\InterfaceService;
use services\WeChat\MsgService;

class IndexController extends \common\controllers\Guest {

    public function indexAction() {
        $data = [ 
              'button' => [
                  [
                      'type' => 'click',
                      'name' => '绿色商城',
                      'key'  => 'V1001_TODAY_MUSIC',
                  ],
                  [
                      'name' => '个人中心',
                      'sub_button' => [
                          [
                              'type' => 'view',
                              'name' => '搜索',
                              'url' => 'http://www.soso.com/',
                          ],
                          [
                              'type' => 'view',
                              'name' => '视频',
                              'url' => 'http://v.qq.com/',
                          ],
                          [
                              'type' => 'click',
                              'name' => '赞一下我们',
                              'key' => 'V1001_GOOD',
                          ],
                      ],
                 ],
             ],
        ];
        $ok = InterfaceService::setWeChatMenu('xxx', $data);
        $this->end();
    }

    /**
     * 接收微信公众号消息。
     */
    public function responseMsgAction() {
        $wx_sn     = $this->getString('wx_sn');
        $echostr   = $this->getString('echostr', '');
        $signature = $this->getString('signature');
        $timestamp = $this->getString('timestamp');
        $nonce     = $this->getString('nonce');
        $ok = InterfaceService::checkSignature($wx_sn, $signature, $timestamp, $nonce);
        if (strlen($echostr) > 0) {
            if ($ok) {
                echo $echostr;
            } else {
                echo '';
            }
        } else {
            if (!$ok) { // 签名不合法。
                echo '';
            }
            $event_post_str = file_get_contents('php://input');
            echo MsgService::doEventHandler($wx_sn, $event_post_str);
        }
        $this->end();
    }
}