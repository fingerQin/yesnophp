<?php
/**
 * 敏感词管理。
 * @author winerQin
 * @date 2016-01-14
 */

use services\SensitiveService;
use common\YCore;
use winer\Paginator;

class SensitiveController extends \common\controllers\Admin {
    
    /**
     * 敏感词列表。
     */
    public function indexAction() {
        $keywords = $this->getString('keywords', '');
        $lv = $this->getString('lv', - 1);
        $page = $this->getInt(YCore::appconfig('pager'), 1);
        $list = SensitiveService::getSensitiveList($keywords, $lv, $page, 20);
        $paginator = new Paginator($list['total'], 20);
        $page_html = $paginator->backendPageShow();
        $this->_view->assign('page_html', $page_html);
        $this->_view->assign('keywords', $keywords);
        $this->_view->assign('list', $list['list']);
    }
    
    /**
     * 添加敏感词。
     */
    public function addAction() {
        if ($this->_request->isXmlHttpRequest()) {
            $lv = $this->getString('lv');
            $val = $this->getString('val');
            $status = SensitiveService::addSensitive($this->admin_id, $lv, $val);
            if ($status) {
                $this->json($status, '添加成功');
            } else {
                $this->json($status, '添加失败');
            }
        }
    }
    
    /**
     * 编辑敏感词。
     */
    public function editAction() {
        if ($this->_request->isXmlHttpRequest()) {
            $id = $this->getInt('id');
            $lv = $this->getString('lv');
            $val = $this->getString('val');
            $status = SensitiveService::editSensitive($id, $this->admin_id, $lv, $val);
            if ($status) {
                $this->json($status, '修改成功');
            } else {
                $this->json($status, '修改失败');
            }
        }
        $id = $this->getInt('id');
        $detail = SensitiveService::getSensitiveDetail($id);
        $this->_view->assign('detail', $detail);
    }
    
    /**
     * 删除敏感词。
     */
    public function deleteAction() {
        $id = $this->getInt('id');
        $status = SensitiveService::deleteSensitive($this->admin_id, $id);
        if ($status) {
            $this->json($status, '删除成功');
        } else {
            $this->json($status, '删除失败');
        }
    }
}