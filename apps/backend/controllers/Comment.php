<?php
/**
 * 订论列表。
 * @author winerQin
 * @date 2016-06-10
 */

use common\YCore;
use winer\Paginator;
use services\ShopService;

class CommentController extends \common\controllers\Admin {

    /**
     * 评论列表。
     */
    public function listAction() {
        $shop_name = $this->getString('shop_name', '');
        $cat_id    = $this->getString('cat_id', -1);
        $page      = $this->getInt(YCore::appconfig('pager'), 1);
        $list      = ShopService::getShopList($shop_name, $page, 10);
        $paginator = new Paginator($list['total'], 10);
        $page_html = $paginator->backendPageShow();
        $this->_view->assign('page_html', $page_html);
        $this->_view->assign('shop_name', $shop_name);
        $this->_view->assign('list', $list['list']);
    }

    /**
     * 评论删除。
     */
    public function deleteAction() {
    	$comment_id = $this->getInt('comment_id');
    	$status = ShopService::deleteShop($this->admin_id, $comment_id);
    	if ($status) {
    		$this->json($status, '删除成功');
    	} else {
    		$this->json($status, '删除失败');
    	}
    }
}