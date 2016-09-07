<?php
/**
 * 商品列表接口。
 * @author winerQin
 * @date 2016-06-06
 */

namespace apis\v1;

use apis\BaseApi;
class GoodsListApi extends BaseApi {
    
    /**
     * 逻辑处理。
     * 
     * @see Api::runService()
     * @return bool
     */
    protected function runService() {
        $keywords = $this->getString('keywords', '');
        $cat_id = $this->getInt('cat_id', - 1);
        $order_by = $this->getString('order_by', 'price');
        $page = $this->getInt('page', 1);
        $count = 10;
        $this->render(0, 'ok');
    }
}