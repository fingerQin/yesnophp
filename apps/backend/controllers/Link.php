<?php
/**
 * 友情链接管理。
 * @author winerQin
 * @date 2016-01-14
 */

use common\YCore;
use services\LinkService;
use winer\Paginator;
use services\CategoryService;

class LinkController extends \common\controllers\Admin {

    /**
     * 友情链接列表。
     */
    public function indexAction() {
        $keyword   = $this->getString('keyword', '');
        $cat_id    = $this->getString('cat_id', -1);
        $page      = $this->getInt(YCore::appconfig('pager'), 1);
        $list      = LinkService::getLinkList($keyword, $cat_id, $page, 20);
        $paginator = new Paginator($list['total'], 20);
        $page_html = $paginator->backendPageShow();
        $this->_view->assign('page_html', $page_html);
        $this->_view->assign('keyword', $keyword);
        $this->_view->assign('cat_id', $cat_id);
        $this->_view->assign('list', $list['list']);
    }

    /**
     * 友情链接添加。
     */
    public function addAction() {
        if ($this->_request->isXmlHttpRequest()) {
            $link_name = $this->getString('link_name');
            $link_url  = $this->getString('link_url');
            $cat_id    = $this->getInt('cat_id');
            $image_url = $this->getString('image_url');
            $display   = $this->getInt('display');
            $status = LinkService::addLink($this->admin_id, $link_name, $link_url, $cat_id, $image_url, $display);
            if ($status) {
                $this->json($status, '添加成功');
            } else {
                $this->json($status, '添加失败');
            }
        }
        $list = CategoryService::getCategoryList(0, 2);
        if (empty($list)) {
            YCore::exception(-1, '请立即创建友情链接分类');
        }
        $this->_view->assign('cat_list', $list);
    }

    /**
     * 友情链接编辑。
     */
    public function editAction() {
        if ($this->_request->isXmlHttpRequest()) {
            $link_id   = $this->getInt('link_id');
            $link_name = $this->getString('link_name');
            $link_url  = $this->getString('link_url');
            $cat_id    = $this->getInt('cat_id');
            $image_url = $this->getString('image_url');
            $display   = $this->getInt('display');
            $status = LinkService::editLink($this->admin_id, $link_id, $link_name, $link_url, $cat_id, $image_url, $display);
            if ($status) {
                $this->json($status, '修改成功');
            } else {
                $this->json($status, '修改失败');
            }
        }
        $link_id = $this->getInt('link_id');
        $detail = LinkService::getLinkDetail($link_id);
        $this->_view->assign('detail', $detail);
        $list = CategoryService::getCategoryList(0, 2);
        if (empty($list)) {
            YCore::exception(-1, '请立即创建友情链接分类');
        }
        $this->_view->assign('cat_list', $list);
    }

    /**
     * 友情链接删除。
     */
    public function deleteAction() {
        $link_id = $this->getInt('link_id');
        $status = LinkService::deleteLink($this->admin_id, $link_id);
        if ($status) {
            $this->json($status, '删除成功');
        } else {
            $this->json($status, '删除失败');
        }
    }

    /**
     * 友情链接排序。
     */
    public function sortAction() {
        if ($this->_request->isPost()) {
	        $listorders = $this->getArray('listorders');
	        $ok = LinkService::sortCategory($listorders);
	        if ($ok) {
	            $this->json($ok, '排序成功');
	        } else {
	            $this->json($ok, '排序失败');
	        }
	    }
    }
}