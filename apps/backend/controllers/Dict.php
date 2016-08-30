<?php
/**
 * 字典管理。
 * @author winerQin
 * @date 2015-11-26
 */

use services\DictService;
use common\YCore;
use winer\Paginator;

class DictController extends \common\controllers\Admin {

	/**
	 * 字典类型列表。
	 */
	public function indexAction() {
	    $keywords = $this->getString('keywords', '');
	    $page = $this->getInt(YCore::appconfig('pager'), 1);
        $list = DictService::getDictTypeList($keywords, $page, 10);
        $paginator = new Paginator($list['total'], 10);
        $page_html = $paginator->backendPageShow();
        $this->_view->assign('page_html', $page_html);
        $this->_view->assign('keywords', $keywords);
        $this->_view->assign('dict_list', $list);
	}

	/**
	 * 字典类型下所属的字典数据。
	 */
	public function dictAction() {
	    $dict_type_id = $this->getInt('dict_type_id');
	    $keywords = $this->getString('keywords', '');
	    $page = $this->getInt(YCore::appconfig('pager'), 1);
	    $list = DictService::getDictList($dict_type_id, $keywords, $page, 10);
	    $paginator = new Paginator($list['total'], 10);
	    $page_html = $paginator->backendPageShow();
	    $this->_view->assign('page_html', $page_html);
	    $this->_view->assign('keywords', $keywords);
	    $this->_view->assign('dict_type_id', $dict_type_id);
	    $this->_view->assign('list', $list);
	}

	/**
	 * 添加字典。
	 */
	public function addAction() {
	    if ($this->_request->isPost()) {
	        $dict_value   = $this->getString('dict_value');
	        $dict_code    = $this->getString('dict_code');
	        $description  = $this->getString('description');
	        $dict_type_id = $this->getInt('dict_type_id');
	        $ok = DictService::addDict($dict_type_id, $dict_code, $dict_value, $description, 0, $this->admin_id);
	        if ($ok) {
	            $this->json($ok, '添加成功');
	        } else {
	            $this->json($ok, '添加失败');
	        }
	    }
	    $dict_type_id = $this->getInt('dict_type_id');
	    $this->_view->assign('dict_type_id', $dict_type_id);
	}

	/**
	 * 编辑字典。
	 */
	public function editAction() {
	    if ($this->_request->isPost()) {
	        $dict_id      = $this->getInt('dict_id');
	        $dict_value   = $this->getString('dict_value');
	        $dict_code    = $this->getString('dict_code');
	        $description  = $this->getString('description');
	        $dict_type_id = $this->getInt('dict_type_id');
	        $ok = DictService::editDict($dict_id, $dict_code, $dict_value, $description, 0, $this->admin_id);
	        if ($ok) {
	            $this->json($ok, '修改成功');
	        } else {
	            $this->json($ok, '修改失败');
	        }
	    }
	    $dict_id = $this->getInt('dict_id');
	    $dict = DictService::getDict($dict_id);
	    $dict_type_id = $this->getInt('dict_type_id');
	    $this->_view->assign('dict', $dict);
	    $this->_view->assign('dict_type_id', $dict_type_id);
	}

	/**
	 * 字典删除。
	 */
	public function deleteAction() {
	    $dict_id = $this->getInt('dict_id');
	    $ok = DictService::deleteDict($dict_id, $this->admin_id);
	    if ($ok) {
	        $this->json($ok, '删除成功');
	    } else {
	        $this->json($ok, '删除失败');
	    }
	}

	/**
	 * 字典值排序。
	 */
	public function sortDictAction() {
	    if ($this->_request->isPost()) {
	        $dict_type_id = $this->getInt('dict_type_id');
	        $listorders = $this->getGP('listorders');
	        $ok = DictService::sortDict($this->admin_id, $listorders);
	        if ($ok) {
	            $this->json($ok, '排序成功');
	        } else {
	            $this->json($ok, '排序失败');
	        }
	    }
	    $this->end();
	}

	/**
	 * 添加字典类型。
	 */
	public function addTypeAction() {
	   if ($this->_request->isPost()) {
	        $type_name   = $this->getString('type_name');
	        $type_code   = $this->getString('type_code');
	        $description = $this->getString('description');
	        $ok = DictService::addDictType($this->admin_id, $type_code, $type_name, $description);
	        if ($ok) {
	            $this->json($ok, '字典添加成功');
	        } else {
	            $this->json($ok, '字典添加失败');
	        }
	    }
	}

	/**
	 * 编辑字典类型。
	 */
	public function editTypeAction() {
	    if ($this->_request->isPost()) {
	        $dict_type_id = $this->getInt('dict_type_id');
	        $type_name    = $this->getString('type_name');
	        $type_code    = $this->getString('type_code');
	        $description  = $this->getString('description');
	        $ok = DictService::editDictType($this->admin_id, $dict_type_id, $type_code, $type_name, $description);
	        if ($ok) {
	            $this->json($ok, '修改成功');
	        } else {
	            $this->json($ok, '修改失败');
	        }
	    }

	    $dict_type_id = $this->getInt('dict_type_id');
	    $dict = DictService::getDictType($dict_type_id);
	    $this->_view->assign('dict', $dict);
	}

	/**
	 * 删除字典类型。
	 */
	public function deleteTypeAction() {
	    $dict_type_id = $this->getInt('dict_type_id');
	    $ok = DictService::deleteDictType($this->admin_id, $dict_type_id);
	    $message = $ok ? '操作成功' : '操作失败';
	    $this->json($ok, $message);
	}

	/**
	 * 清除字典缓存。
	 */
	public function clearCacheAction() {
		if ($this->_request->isXmlHttpRequest()) {
			DictService::clearDictCache();
			$this->json(true, '字典缓存清除成功');
		}
	}
}