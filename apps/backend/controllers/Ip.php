<?php
/**
 * IP禁止。
 * @author winerQin
 * @date 2016-01-14
 */

use services\IpService;
use winer\Paginator;
use common\YCore;

class IpController extends \common\controllers\Admin {

    /**
     * 被禁IP列表。
     */
    public function indexAction() {
        $keywords  = $this->getString('keywords', '');
        $page      = $this->getInt(YCore::config('pager'), 1);
        $list      = IpService::getIpBanList($keywords, $page, 20);
        $paginator = new Paginator($list['total'], 20);
        $page_html = $paginator->show();
        $this->_view->assign('page_html', $page_html);
        $this->_view->assign('keywords', $keywords);
        $this->_view->assign('list', $list['list']);
    }

    /**
     * 添加黑名单IP。
     */
    public function addAction() {
        if ($this->_request->isXmlHttpRequest()) {
            $ip     = $this->getString('ip');
            $remark = $this->getString('remark');
            $status = IpService::addIpBan($this->admin_id, $ip, $remark);
            if ($status) {
                $this->json($status, '添加成功');
            } else {
                $this->json($status, '添加失败');
            }
        }
    }

    /**
     * 编辑IP黑名单。
     */
    public function editAction() {
        if ($this->_request->isXmlHttpRequest()) {
            $id     = $this->getInt('id');
            $ip     = $this->getString('ip');
            $remark = $this->getString('remark');
            $status = IpService::editIpBan($id, $this->admin_id, $ip, $remark);
            if ($status) {
                $this->json($status, '修改成功');
            } else {
                $this->json($status, '修改失败');
            }
        }
        $id = $this->getInt('id');
        $detail = IpService::getIpBanDetail($id);
        $this->_view->assign('detail', $detail);
    }

    /**
     * 删除IP黑名单。
     */
    public function deleteAction() {
        $id = $this->getInt('id');
        $status = IpService::deleteIpBan($id, $this->admin_id);
        if ($status) {
            $this->json($status, '删除成功');
        } else {
            $this->json($status, '删除失败');
        }
    }
}