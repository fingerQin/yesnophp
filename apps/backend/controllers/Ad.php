<?php
/**
 * 广告管理。
 * @author winerQin
 * @date 2016-01-14
 */

use services\AdService;
use common\YCore;
use winer\Paginator;

class AdController extends \common\controllers\Admin {

    /**
     * 广告位置列表。
     */
    public function positionListAction() {
        $keywords  = $this->getString('keywords', '');
        $page      = $this->getInt(YCore::config('pager'), 1);
        $list      = AdService::getAdPostionList($keywords, $page, 20);
        $paginator = new Paginator($list['total'], 20);
        $page_html = $paginator->show();
        $this->_view->assign('page_html', $page_html);
        $this->_view->assign('keywords', $keywords);
        $this->_view->assign('list', $list['list']);
    }

    /**
     * 广告位置添加。
     */
    public function positionAddAction() {
        if ($this->_request->isXmlHttpRequest()) {
            $pos_name     = $this->getString('pos_name');
            $pos_code     = $this->getString('pos_code');
            $pos_ad_count = $this->getInt('pos_ad_count');
            $status = AdService::addAdPostion($this->admin_id, $pos_name, $pos_code, $pos_ad_count);
            if ($status) {
                $this->json($status, '添加成功');
            } else {
                $this->json($status, '添加失败');
            }
        }
    }

    /**
     * 广告位置编辑。
     */
    public function positionEditAction() {
        if ($this->_request->isXmlHttpRequest()) {
            $pos_id       = $this->getInt('pos_id');
            $pos_name     = $this->getString('pos_name');
            $pos_code     = $this->getString('pos_code');
            $pos_ad_count = $this->getInt('pos_ad_count');
            $status = AdService::editAdPostion($this->admin_id, $pos_id, $pos_name, $pos_code, $pos_ad_count);
            if ($status) {
                $this->json($status, '操作成功');
            } else {
                $this->json($status, '操作失败');
            }
        }
        $pos_id = $this->getInt('pos_id');
        $detail = AdService::getAdPostionDetail($pos_id);
        $this->_view->assign('detail', $detail);
    }

    /**
     * 广告位置删除。
     */
    public function positionDeleteAction() {
        $pos_id = $this->getInt('pos_id');
        $status = AdService::deleteAdPostion($this->admin_id, $pos_id);
        if ($status) {
            $this->json($status, '删除成功');
        } else {
            $this->json($status, '删除失败');
        }
    }

    /**
     * 广告列表。
     */
    public function indexAction() {
        $pos_id    = $this->getInt('pos_id');
        $ad_name   = $this->getString('ad_name', '');
        $display    = $this->getInt('display', -1);
        $page      = $this->getInt(YCore::config('pager'), 1);
        $list      = AdService::getAdList($pos_id, $ad_name, $display, $page, 10);
        $paginator = new Paginator($list['total'], 10);
        $page_html = $paginator->show();
        $this->_view->assign('page_html', $page_html);
        $this->_view->assign('ad_name', $ad_name);
        $this->_view->assign('display', $display);
        $this->_view->assign('list', $list['list']);
        $this->_view->assign('pos_id', $pos_id);
    }

    /**
     * 广告添加。
     */
    public function addAction() {
        if ($this->_request->isXmlHttpRequest()) {
            $pos_id       = $this->getInt('pos_id');
            $ad_name      = $this->getString('ad_name');
            $start_time   = $this->getString('start_time');
            $end_time     = $this->getString('end_time');
            $display      = $this->getInt('display');
            $remark       = $this->getString('remark');
            $ad_image_url = $this->getString('ad_image_url');
            $ad_url       = $this->getString('ad_url');
            $status       = AdService::addAd($this->admin_id, $pos_id, $ad_name, $start_time, $end_time, $display, $remark, $ad_image_url, $ad_url);
            if ($status) {
                $this->json($status, '添加成功');
            } else {
                $this->json($status, '添加失败');
            }
        }
        $pos_id = $this->getInt('pos_id');
        $this->_view->assign('pos_id', $pos_id);
    }

    /**
     * 广告编辑。
     */
    public function editAction() {
        if ($this->_request->isXmlHttpRequest()) {
            $ad_id        = $this->getInt('ad_id');
            $ad_name      = $this->getString('ad_name');
            $start_time   = $this->getString('start_time');
            $end_time     = $this->getString('end_time');
            $display      = $this->getInt('display');
            $remark       = $this->getString('remark');
            $ad_image_url = $this->getString('ad_image_url');
            $ad_url       = $this->getString('ad_url');
            $status       = AdService::editAd($this->admin_id, $ad_id, $ad_name, $start_time, $end_time, $display, $remark, $ad_image_url, $ad_url);
            if ($status) {
                $this->json($status, '修改成功');
            } else {
                $this->json($status, '修改失败');
            }
        }
        $ad_id = $this->getInt('ad_id');
        $detail = AdService::getAdDetail($ad_id);
        $this->_view->assign('detail', $detail);
    }

    /**
     * 广告删除。
     */
    public function deleteAction() {
        $ad_id = $this->getInt('ad_id');
        $status = AdService::deleteAd($this->admin_id, $ad_id);
        if ($status) {
            $this->json($status, '删除成功');
        } else {
            $this->json($status, '删除失败');
        }
    }

    /**
     * 广告排序。
     */
    public function sortAdAction() {
        if ($this->_request->isPost()) {
            $listorders = $this->getArray('listorders');
            $ok = AdService::sortAd($listorders);
            if ($ok) {
                $this->json($ok, '排序成功');
            } else {
                $this->json($ok, '排序失败');
            }
        }
    }
}