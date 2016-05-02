<?php
/**
 * 菜单管理。
 * -- 1、菜单最多允许三层。超过三层已经没有多大意义。并不便于管理。
 * @author winerQin
 * @date 2015-11-26
 */

use services\AdminPermissionService;

class MenuController extends \common\controllers\Admin {

	/**
	 * 菜单列表。
	 */
	public function indexAction() {
	    $list = AdminPermissionService::getMenuList(0);
	    $this->_view->assign('list', $list);
	}

	/**
	 * 添加菜单。
	 */
	public function addAction() {
	    if ($this->_request->isPost()) {
	        $parentid        = $this->getInt('parentid', 0);
	        $name            = $this->getString('name');
	        $module_name     = $this->getString('m');
	        $controller_name = $this->getString('c');
	        $action_name     = $this->getString('a');
	        $data            = $this->getString('data', '');
	        $listorder       = $this->getInt('listorder', 0);
	        $display         = $this->getInt('display', 0);
	        $ok = AdminPermissionService::addMenu($parentid, $name, $module_name, $controller_name, $action_name, $data, $listorder, $display);
	        if ($ok) {
	            $this->json($ok, '添加成功');
	        } else {
	            $this->json($ok, '添加失败');
	        }
	    }
	    $parentid = $this->getInt('parentid', 0);
	    $list = AdminPermissionService::getMenuList(0);
	    $this->_view->assign('list', $list);
	    $this->_view->assign('parentid', $parentid);
	}

	/**
	 * 编辑菜单。
	 */
	public function editAction() {
	    if ($this->_request->isPost()) {
	        $menu_id         = $this->getInt('menu_id');
	        $parentid        = $this->getInt('parentid');
	        $name            = $this->getInt('name');
	        $module_name     = $this->getInt('module_name');
	        $controller_name = $this->getInt('controller_name');
	        $action_name     = $this->getInt('action_name');
	        $data            = $this->getInt('data');
	        $listorder       = $this->getInt('listorder');
	        $display         = $this->getInt('display', 0);
	        $ok = AdminPermissionService::editMenu($menu_id, $parentid, $name, $module_name, $controller_name, $action_name, $data, $listorder, $display);
	        if ($ok) {
	            $this->json($ok, '编辑成功');
	        } else {
	            $this->json($ok, '编辑失败');
	        }
	    }
	    $menu_id = $this->getInt('menu_id');
	    $detail = AdminPermissionService::getMenuDetail($menu_id);
	    $parentid = $this->getInt('parentid', 0);
	    $list = AdminPermissionService::getMenuList(0);
	    $this->_view->assign('detail', $detail);
	    $this->_view->assign('list', $list);
	}

	/**
	 * 删除菜单。
	 */
	public function deleteAction() {
	    $menu_id = $this->getInt('menu_id');
	    $ok = AdminPermissionService::deleteMenu($menu_id);
	    if ($ok) {
	        $this->json($ok, '删除成功');
	    } else {
	        $this->json($ok, '删除失败');
	    }
	}

	/**
	 * 菜单排序。
	 */
	public function sortAction() {
	    if ($this->_request->isPost()) {
	        $listorders = $this->getArray('listorders');
	        $ok = AdminPermissionService::sortMenu($listorders);
	        if ($ok) {
	            $this->json($ok, '排序成功');
	        } else {
	            $this->json($ok, '排序失败');
	        }
	    }
	}
}