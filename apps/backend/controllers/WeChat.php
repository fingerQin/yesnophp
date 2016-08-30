<?php
/**
 * 微信公众号管理。
 * @author winerQin
 * @date 2016-04-15
 */

use common\YCore;
use services\WeChatService;
use winer\Paginator;

class WechatController extends \common\controllers\Admin {

    /**
     * 公众号列表。
     */
    public function accountListAction() {
        $page      = $this->getInt(YCore::appconfig('pager'), 1);
        $account   = $this->getString('account', '');
        $sn        = $this->getString('sn', '');
        $appid     = $this->getString('appid', '');
        $list      = WeChatService::getWxAccountList($account, $sn, $appid, $page, 20);
        $paginator = new Paginator($list['total'], 20);
        $page_html = $paginator->backendPageShow();
        $this->_view->assign('page_html', $page_html);
        $this->_view->assign('account', $account);
        $this->_view->assign('sn', $sn);
        $this->_view->assign('appid', $appid);
        $this->_view->assign('list', $list['list']);
        $wechat_type_dict = YCore::dict('wechat_type');
        $this->_view->assign('wechat_type_dict', $wechat_type_dict);
    }

    /**
     * 添加公众号。
     */
    public function addAccountAction() {
        if ($this->_request->isXmlHttpRequest()) {
            $data = [
                'wx_sn'           => $this->getString('wx_sn'),
                'wx_account'      => $this->getString('wx_account'),
                'wx_type'         => $this->getInt('wx_type'),
                'wx_auth'         => $this->getInt('wx_auth'),
                'wx_appid'        => $this->getString('wx_appid'),
                'wx_appsecret'    => $this->getString('wx_appsecret'),
                'wx_token'        => $this->getString('wx_token'),
                'wx_aeskey'       => $this->getString('wx_aeskey'),
                'wx_cert_path'    => $this->getString('wx_cert_path'),
                'wx_cert_key'     => $this->getString('wx_cert_key'),
                'wx_report_level' => $this->getString('wx_report_level'),
                'wx_proxy_host'   => $this->getString('wx_proxy_host'),
                'wx_proxy_port'   => $this->getInt('wx_proxy_port'),
                'admin_id'        => $this->admin_id
            ];
            $status = WeChatService::addWxAccount($data);
            if ($status) {
                $this->json($status, '添加成功');
            } else {
                $this->json($status, '添加失败');
            }
        }
        $wechat_type_dict = YCore::dict('wechat_type');
        $this->_view->assign('wechat_type_dict', $wechat_type_dict);
    }

    /**
     * 编辑公众号。
     */
    public function editAccountAction() {
        if ($this->_request->isXmlHttpRequest()) {
            $data = [
                'account_id'      => $this->getInt('account_id'),
                'wx_sn'           => $this->getString('wx_sn'),
                'wx_account'      => $this->getString('wx_account'),
                'wx_type'         => $this->getInt('wx_type'),
                'wx_auth'         => $this->getInt('wx_auth'),
                'wx_appid'        => $this->getString('wx_appid'),
                'wx_appsecret'    => $this->getString('wx_appsecret'),
                'wx_token'        => $this->getString('wx_token'),
                'wx_aeskey'       => $this->getString('wx_aeskey'),
                'wx_cert_path'    => $this->getString('wx_cert_path'),
                'wx_cert_key'     => $this->getString('wx_cert_key'),
                'wx_report_level' => $this->getString('wx_report_level'),
                'wx_proxy_host'   => $this->getString('wx_proxy_host'),
                'wx_proxy_port'   => $this->getInt('wx_proxy_port'),
                'admin_id'        => $this->admin_id
            ];
            $status = WeChatService::editWxAccount($data);
            if ($status) {
                $this->json($status, '编辑成功');
            } else {
                $this->json($status, '编辑失败');
            }
        }
        $account_id = $this->getInt('account_id');
        $detail = WeChatService::getWxAccountDetail($account_id);
        $this->_view->assign('detail', $detail);
        $wechat_type_dict = YCore::dict('wechat_type');
        $this->_view->assign('wechat_type_dict', $wechat_type_dict);
    }

    /**
     * 删除公众号。
     */
    public function deleteAccountAction() {
        $account_id = $this->getInt('account_id');
        $status = WeChatService::deleteWxAccount($this->admin_id, $account_id);
        if ($status) {
            $this->json($status, '删除成功');
        } else {
            $this->json($status, '删除失败');
        }
    }

    /**
     * 公众号菜单列表。
     */
    public function accountMenuListAction() {
        
    }

    /**
     * 添加公众号菜单。
     */
    public function addAccountMenuAction() {
        if ($this->_request->isXmlHttpRequest()) {
        	$admin_id    = $this->admin_id;
        	$account_id  = $this->getInt('account_id');
        	$parent_id   = $this->getInt('parent_id');
        	$menu_name   = $this->getString('menu_name');
        	$menu_type   = $this->getInt('menu_type');
        	$menu_key    = $this->getString('menu_key', '');
        	$is_outside  = $this->getInt('is_outside', 0);
        	$outside_url = $this->getString('outside_url', '');
        	$module_name = $this->getString('module_name', '');
        	$ctrl_name   = $this->getString('ctrl_name', '');
        	$action_name = $this->getString('action_name', '');
        	$url_query   = $this->getString('url_query', '');
        	$display     = $this->getString('display', 1);
        	$status = WeChatService::addAccountMenu($admin_id, $account_id, $parent_id, $menu_name, $menu_type, $menu_key, $is_outside, $outside_url, $module_name, $ctrl_name, $action_name, $url_query, $display);
        	if ($status) {
        		$this->json($status, '添加成功');
        	} else {
        		$this->json($status, '添加失败');
        	}
        }
    }

    /**
     * 修改公众号菜单。
     */
    public function editAccountMenuAction() {
    	if ($this->_request->isXmlHttpRequest()) {
    		$admin_id    = $this->admin_id;
    		$menu_id     = $this->getInt('menu_id');
    		$parent_id   = $this->getInt('parent_id');
    		$menu_name   = $this->getString('menu_name');
    		$menu_type   = $this->getInt('menu_type');
    		$menu_key    = $this->getString('menu_key', '');
    		$is_outside  = $this->getInt('is_outside', 0);
    		$outside_url = $this->getString('outside_url', '');
    		$module_name = $this->getString('module_name', '');
    		$ctrl_name   = $this->getString('ctrl_name', '');
    		$action_name = $this->getString('action_name', '');
    		$url_query   = $this->getString('url_query', '');
    		$display     = $this->getString('display', 1);
    		$status = WeChatService::editAccountMenu($menu_id, $admin_id, $parent_id, $menu_name, $menu_type, $menu_key, $is_outside, $outside_url, $module_name, $ctrl_name, $action_name, $url_query, $display);
    		if ($status) {
    			$this->json($status, '修改成功');
    		} else {
    			$this->json($status, '修改失败');
    		}
    	}
    }

    /**
     * 删除公众号菜单。
     */
    public function deleteAccountMenuAction() {
    	$menu_id = $this->getInt('menu_id');
    	$status = WeChatService::deleteAccountMenu($this->admin_id, $menu_id);
    	if ($status) {
    		$this->json($status, '删除成功');
    	} else {
    		$this->json($status, '删除失败');
    	}
    }

    /**
     * 推送菜单到微信公众号。
     */
    public function pushAccountMenuToWeChatAction() {
        $this->end();
    }

    /**
     * 图文列表。
     */
    public function imageTextListAction() {
        
    }

    /**
     * 添加图文。
     */
    public function addImageTextAction() {
        if ($this->_request->isXmlHttpRequest()) {
        	$data = [];
	        $status = WeChatService::addNews($data);
	    	if ($status) {
	    		$this->json($status, '删除成功');
	    	} else {
	    		$this->json($status, '删除失败');
	    	}
        }
        
    }

    /**
     * 删除图文。
     */
    public function deleteImageTextAction() {
    	$news_id = $this->getInt('news_id');
    	$status = WeChatService::deleteNews($news_id, $this->admin_id);
    	if ($status) {
    		$this->json($status, '删除成功');
    	} else {
    		$this->json($status, '删除失败');
    	}
    }
}