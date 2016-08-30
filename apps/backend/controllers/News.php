<?php
/**
 * 文章管理。
 * @author winerQin
 * @date 2015-11-26
 */

use common\YCore;
use services\NewsService;
use winer\Paginator;
use services\CategoryService;

class NewsController extends \common\controllers\Admin {

	/**
	 * 文章列表。
	 */
	public function indexAction() {
	    $title      = $this->getString('title', '');
	    $admin_name = $this->getString('admin_name', '');
	    $starttime  = $this->getString('starttime', '');
	    $endtime    = $this->getString('endtime', '');
	    $page       = $this->getInt(YCore::appconfig('pager'), 1);
	    $list       = NewsService::getNewsList($title, $admin_name, $starttime, $endtime, $page, 20);
	    $paginator  = new Paginator($list['total'], 20);
	    $page_html  = $paginator->backendPageShow();
	    $this->_view->assign('page_html', $page_html);
	    $this->_view->assign('list', $list['list']);
	    $this->_view->assign('admin_name', $admin_name);
	    $this->_view->assign('title', $title);
	    $this->_view->assign('starttime', $starttime);
	    $this->_view->assign('endtime', $endtime);
	}

	/**
	 * 添加文章。
	 */
	public function addAction() {
	    if ($this->_request->isPost()) {
	        $title     = $this->getString('title');
	        $cat_id    = $this->getString('cat_id');
	        $code      = $this->getString('code');
	        $intro     = $this->getString('intro');
	        $keywords  = $this->getString('keywords');
	        $source    = $this->getString('source');
	        $image_url = $this->getString('image_url');
	        $content   = $this->getString('content');
	        $display   = $this->getInt('display');
	        $status = NewsService::addNews($this->admin_id, $code, $cat_id, $title, $intro, $keywords, $source, $image_url, $content, $display);
	        if ($status) {
	            $this->json($status, '操作成功');
	        } else {
	            $this->json($status, '操作失败');
	        }
	    }
	    $news_cat_list = CategoryService::getCategoryList(0, 1);
        if (empty($news_cat_list)) {
            YCore::exception(-1, '请立即创建文章分类');
        }
        $frontend_url = YCore::config('frontend_domain_name');
        $this->_view->assign('news_cat_list', $news_cat_list);
        $this->_view->assign('frontend_url', $frontend_url);
	}

	/**
	 * 编辑文章。
	 */
	public function editAction() {
	    if ($this->_request->isPost()) {
	        $news_id   = $this->getInt('news_id');
	        $cat_id    = $this->getString('cat_id');
	        $code      = $this->getString('code');
	        $title     = $this->getString('title');
	        $intro     = $this->getString('intro');
	        $keywords  = $this->getString('keywords');
	        $source    = $this->getString('source');
	        $image_url = $this->getString('image_url');
	        $content   = $this->getString('content');
	        $display   = $this->getInt('display');
	        $status = NewsService::editNews($this->admin_id, $news_id, $code, $cat_id, $title, $intro, $keywords, $source, $image_url, $content, $display);
	        if ($status) {
	            $this->json($status, '操作成功');
	        } else {
	            $this->json($status, '操作失败');
	        }
	    }
	    $news_id = $this->getInt('news_id');
	    $detail  = NewsService::getNewsDetail($news_id, true);
	    $news_cat_list = CategoryService::getCategoryList(0, 1);
	    if (empty($news_cat_list)) {
	        YCore::exception(-1, '请立即创建文章分类');
	    }
	    $this->_view->assign('news_cat_list', $news_cat_list);
	    $this->_view->assign('detail', $detail);
	}

	/**
	 * 文章删除。
	 */
	public function deleteAction() {
	    $news_id = $this->getInt('news_id');
	    $status  = NewsService::deleteNews($this->admin_id, $news_id);
	    if ($status) {
	        $this->json($status, '操作成功');
	    } else {
	        $this->json($status, '操作失败');
	    }
	}

	/**
	 * 文章排序。
	 */
	public function sortAction() {
	    if ($this->_request->isPost()) {
	        $listorders = $this->getArray('listorders');
	        $ok = NewsService::sortNews($this->admin_id, $listorders);
	        if ($ok) {
	            $this->json($ok, '排序成功');
	        } else {
	            $this->json($ok, '排序失败');
	        }
	    }
	}
}