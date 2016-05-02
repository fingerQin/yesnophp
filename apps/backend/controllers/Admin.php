<?php
/**
 * 管理员管理。
 * @author winerQin
 * @date 2015-11-26
 */

use services\AdminService;
use winer\Paginator;
use services\AdminPermissionService;
use common\YCore;

class AdminController extends \common\controllers\Admin {

	/**
	 * 管理员列表。
	 */
	public function indexAction() {
	    $keywords  = $this->getString('keywords', '');
	    $page      = $this->getString('page', 1);
	    $result    = AdminService::getAdminList($keywords, $page, 10);
	    $paginator = new Paginator($result['total'], 20);
	    $page_html = $paginator->show();
	    $this->_view->assign('page_html', $page_html);
	    $this->_view->assign('keywords', $keywords);
	    $this->_view->assign('list', $result['list']);
	}

	/**
	 * 添加管理员。
	 */
	public function addAction() {
	    if ($this->_request->isXmlHttpRequest()) {
	        $realname    = $this->getString('realname');
	        $username    = $this->getString('username');
	        $password    = $this->getString('password');
	        $mobilephone = $this->getString('mobilephone');
	        $roleid      = $this->getInt('roleid');
	        $status = AdminService::addAdmin($realname, $username, $password, $mobilephone, $roleid);
	        if ($status) {
	            $this->json($status, '添加成功');
	        } else {
	            $this->json($status, '添加失败');
	        }
	    }
	    $role_list = AdminPermissionService::getRoleList();
	    $this->_view->assign('role_list', $role_list);
	}

	/**
	 * 编辑管理员。
	 */
	public function editAction() {
	   if ($this->_request->isXmlHttpRequest()) {
	       $admin_id    = $this->getInt('admin_id');
	       $realname    = $this->getString('realname');
	       $password    = $this->getString('password', '');
	       $mobilephone = $this->getString('mobilephone');
	       $roleid      = $this->getInt('roleid');
	       $status = AdminService::editAdmin($admin_id, $realname, $mobilephone, $roleid, $password);
	       if ($status) {
	           $this->json($status, '修改成功');
	       } else {
	           $this->json($status, '修改失败');
	       }
        }
        $admin_id = $this->getInt('admin_id');
        $admin_detail = AdminService::getAdminDetail($admin_id);
        $this->_view->assign('detail', $admin_detail);
        $role_list = AdminPermissionService::getRoleList();
        $this->_view->assign('role_list', $role_list);
	}

	/**
	 * 删除管理员。
	 */
	public function deleteAction() {
	    $admin_id = $this->getInt('admin_id');
	    $status = AdminService::deleteAdmin($this->admin_id, $admin_id);
	    if ($status) {
	        $this->json($status, '删除成功');
	    } else {
	        $this->json($status, '删除失败');
	    }
	    $this->end();
	}

	/**
	 * 管理员修改个人密码。
	 */
	public function editPwdAction() {
	    if ($this->_request->isXmlHttpRequest()) {
	        $old_pwd = $this->getString('old_pwd');
	        $new_pwd = $this->getString('new_pwd');
	        $status  = AdminService::editPwd($this->admin_id, $old_pwd, $new_pwd);
	        if ($status) {
	            $this->json($status, '修改成功');
	        } else {
	            $this->json($status, '修改失败');
	        }
	    }
	    $admin_info = AdminService::getAdminInfo($this->admin_id);
	    $this->_view->assign('admin_info', $admin_info);
	}

	/**
	 * 登录历史。
	 */
	public function loginHistoryAction() {
	    $page   = $this->getString(YCore::config('pager'), 1);
	    $result = AdminService::getAdminLoginHistoryList($this->admin_id, $page, 20);
	    $paginator = new Paginator($result['total'], 20);
	    $page_html = $paginator->show();
	    $this->_view->assign('page_html', $page_html);
	    $this->_view->assign('list', $result['list']);
	}
}