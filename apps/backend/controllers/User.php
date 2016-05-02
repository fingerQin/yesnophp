<?php
/**
 * 用户管理。
 * @author winerQin
 * @date 2015-11-26
 */

use common\YCore;
use services\UserService;
use winer\Paginator;

class UserController extends \common\controllers\Admin {

	/**
	 * 用户列表。
	 */
	public function indexAction() {
	    $username    = $this->getString('username', '');
	    $mobilephone = $this->getString('mobilephone', '');
	    $starttime   = $this->getString('starttime', '');
	    $endtime     = $this->getString('endtime', '');
	    $is_verify   = $this->getString('is_verify', -1);
	    $page        = $this->getInt(YCore::config('pager'), 1);
	    $list        = UserService::getUserList($username, $mobilephone, $is_verify, $starttime, $endtime, $page, 20);
	    $paginator = new Paginator($list['total'], 20);
	    $page_html = $paginator->show();
	    $this->_view->assign('page_html', $page_html);
	    $this->_view->assign('list', $list['list']);
	    $this->_view->assign('mobilephone', $mobilephone);
	    $this->_view->assign('username', $username);
	    $this->_view->assign('starttime', $starttime);
	    $this->_view->assign('endtime', $endtime);
	    $this->_view->assign('is_verify', $is_verify);
	}

	/**
	 * 用户添加。
	 */
	public function addAction() {
	    if ($this->_request->isXmlHttpRequest()) {
	        $username    = $this->getString('username');
	        $password    = $this->getString('password');
	        $mobilephone = $this->getString('mobilephone', '');
	        $email       = $this->getString('email', '');
	        $realname    = $this->getString('realname', '');
	        $avatar      = $this->getString('avatar', '');
	        $signature   = $this->getString('signature', '');
	        $status = UserService::addUser($username, $password, $mobilephone, $email, $realname, $avatar, $signature);
	        if ($status) {
	            $this->json($status, '封禁成功');
	        } else {
	            $this->json($status, '封禁失败');
	        }
	    }
	}

	/**
	 * 用户编辑。
	 */
	public function editAction() {
	    if ($this->_request->isXmlHttpRequest()) {
	        $user_id     = $this->getInt('user_id');
	        $username    = $this->getString('username');
	        $password    = $this->getString('password', ''); // 传空字符串代表保持原密码。
	        $mobilephone = $this->getString('mobilephone', '');
	        $email       = $this->getString('email', '');
	        $realname    = $this->getString('realname', '');
	        $avatar      = $this->getString('avatar', '');
	        $signature   = $this->getString('signature', '');
	        $status = UserService::editUser($user_id, $username, $password, $mobilephone, $email, $realname, $avatar, $signature);
	        if ($status) {
	            $this->json($status, '操作成功');
	        } else {
	            $this->json($status, '操作失败');
	        }
	    }
	    $user_id = $this->getInt('user_id');
	    $userinfo = UserService::getUserDetail($user_id);
	    $this->_view->assign('userinfo', $userinfo);
	}

	/**
	 * 封禁用户。
	 */
	public function forbidAction() {
	    if ($this->_request->isXmlHttpRequest()) {
	        $user_id        = $this->getInt('user_id');
	        $ban_type       = $this->getInt('ban_type');
	        $ban_start_time = $this->getString('ban_start_time');
	        $ban_end_time   = $this->getString('ban_end_time');
	        $ban_reason     = $this->getString('ban_reason');
	        $status = UserService::addBlacklist($this->_admin_id, $user_id, $ban_type, $ban_start_time, $ban_end_time, $ban_reason);
	        if ($status) {
	            $this->json($status, '封禁成功');
	        } else {
	            $this->json($status, '封禁失败');
	        }
	    }
	    $user_id = $this->getInt('user_id');
	    $this->_view->assign('user_id', $user_id);
	}

	/**
	 * 查看用户详情。
	 */
	public function viewAction() {
	    $user_id = $this->getInt('user_id');
	    $userinfo = UserService::getUserDetail($user_id);
	    $this->_view->assign('userinfo', $userinfo);
	}
}