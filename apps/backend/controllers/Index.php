<?php
/**
 * 默认controller。
 * @author winerQin
 * @date 2015-11-13
 */

use services\AdminPermissionService;
use services\UploadService;

class IndexController extends \common\controllers\Admin {

	/**
	 * 首页。
	 */
	public function indexAction() {
	    $top_menu = AdminPermissionService::getRoleSubMenu($this->roleid, 0);
	    $this->_view->assign('realname', $this->realname);
	    $this->_view->assign('username', $this->username);
	    $this->_view->assign('mobilephone', $this->mobilephone);
	    $this->_view->assign('top_menu', $top_menu);
	}

	/**
	 * 取左侧菜单。
	 */
	public function leftMenuAction() {
		$menu_id = $this->getInt('menu_id');
		$left_menu = AdminPermissionService::getAdminLeftMenu($this->roleid, $menu_id);
		$this->_view->assign('left_menu', $left_menu);
	}

	/**
	 * 位置（当前页面所处菜单位置）。
	 */
	public function arrowAction() {
	    $menu_id = $this->getInt('menu_id');
	    echo AdminPermissionService::getMenuCrumbs($menu_id);
	    $this->end();
	}

	/**
	 * 默认内容页。
	 */
	public function rightAction() {
	    
	}

	/**
	 * 文件上传。
	 */
	public function uploadAction() {
	    header("Access-Control-Allow-Origin: *");
	    $result = UploadService::uploadImage(1, $this->admin_id, 'voucher', 2);
	    $this->json(true, '上传成功', $result);
	    $this->end();
	}
}