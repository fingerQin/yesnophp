<?php
/**
 * 支付管理。
 * @author winerQin
 * @date 2016-03-08
 */

use services\CashService;

class PayController extends \common\controllers\Admin {

    /**
     * 付款给个人。
     */
    public function wxCashToPersonAction() {
        if ($this->_request->isXmlHttpRequest()) {
            $id = $this->getInt('id');
            $status = CashService::cashToPerson($this->admin_id, $id);
            if ($status) {
                $this->json($status, '支付成功');
            } else {
                $this->json($status, '支付失败');
            }
        }
        $this->end();
    }
}