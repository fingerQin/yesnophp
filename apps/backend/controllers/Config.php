<?php
/**
 * 配置管理。
 * @author winerQin
 * @date 2016-01-14
 */

use services\ConfigService;
use common\YCore;
use winer\Paginator;

class ConfigController extends \common\controllers\Admin {

    /**
     * 配置列表。
     */
    public function indexAction() {
        $keywords  = $this->getString('keywords', '');
        $page      = $this->getInt(YCore::config('pager'), 1);
        $list      = ConfigService::getConfigList($keywords, $page, 20);
        $paginator = new Paginator($list['total'], 20);
        $page_html = $paginator->show();
        $this->_view->assign('page_html', $page_html);
        $this->_view->assign('keywords', $keywords);
        $this->_view->assign('list', $list['list']);
    }

    /**
     * 添加配置。
     */
    public function addAction() {
        if ($this->_request->isXmlHttpRequest()) {
            $ctitle = $this->getString('ctitle');
            $cname  = $this->getString('cname');
            $cvalue = $this->getString('cvalue');
            $description = $this->getString('description');
            $status = ConfigService::addConfig($this->admin_id, $ctitle, $cname, $cvalue, $description);
            if ($status) {
                $this->json($status, '添加成功');
            } else {
                $this->json($status, '添加失败');
            }
        }
    }

    /**
     * 配置编辑。
     */
    public function editAction() {
        if ($this->_request->isXmlHttpRequest()) {
            $config_id = $this->getInt('config_id');
            $ctitle = $this->getString('ctitle');
            $cname  = $this->getString('cname');
            $cvalue = $this->getString('cvalue');
            $description = $this->getString('description');
            $status = ConfigService::editConfig($this->admin_id, $config_id, $ctitle, $cname, $cvalue, $description);
            if ($status) {
                $this->json($status, '修改成功');
            } else {
                $this->json($status, '修改失败');
            }
        }
        $config_id = $this->getInt('config_id');
        $detail = ConfigService::getConfigDetail($config_id);
        $this->_view->assign('detail', $detail);
    }

    /**
     * 配置删除。
     */
    public function deleteAction() {
        $config_id = $this->getInt('config_id');
        $status = ConfigService::deleteConfig($this->admin_id, $config_id);
        if ($status) {
            $this->json($status, '删除成功');
        } else {
            $this->json($status, '删除失败');
        }
    }
}