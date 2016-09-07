<?php
/**
 * 文章分类管理。
 * @author winerQin
 * @date 2015-11-26
 */

use services\CategoryService;
use common\YCore;

class CategoryController extends \common\controllers\Admin {
    
    /**
     * 分类列表。
     */
    public function indexAction() {
        $cat_type = $this->getInt('cat_type', 1);
        $list = CategoryService::getCategoryList(0, $cat_type);
        $cat_type_list = YCore::dict('category_type_list');
        $this->_view->assign('list', $list);
        $this->_view->assign('cat_type', $cat_type);
        $this->_view->assign('cat_type_list', $cat_type_list);
    }
    
    /**
     * 添加分类。
     */
    public function addAction() {
        if ($this->_request->isXmlHttpRequest()) {
            $cat_type = $this->getInt('cat_type', - 1);
            $cat_name = $this->getString('cat_name');
            $parentid = $this->getInt('parentid');
            $is_out_url = $this->getInt('is_out_url');
            $out_url = $this->getString('out_url');
            $display = $this->getInt('display');
            $status = CategoryService::addCategory($this->admin_id, $cat_type, $cat_name, $parentid, $is_out_url, $out_url, $display);
            if ($status) {
                $this->json($status, '操作成功');
            } else {
                $this->json($status, '操作失败');
            }
        }
        $parentid = $this->getInt('parentid', 0);
        $parent_cat_info = [];
        if ($parentid > 0) {
            $parent_cat_info = CategoryService::getCategoryDetail($parentid);
        }
        $cat_type_list = YCore::dict('category_type_list');
        $list = CategoryService::getCategoryList(0);
        $this->_view->assign('parentid', $parentid);
        $this->_view->assign('list', $list);
        $this->_view->assign('parent_cat_info', $parent_cat_info);
        $this->_view->assign('cat_type_list', $cat_type_list);
    }
    
    /**
     * 编辑分类。
     */
    public function editAction() {
        if ($this->_request->isXmlHttpRequest()) {
            $cat_id = $this->getInt('cat_id');
            $cat_name = $this->getString('cat_name');
            $is_out_url = $this->getInt('is_out_url');
            $out_url = $this->getString('out_url');
            $display = $this->getInt('display');
            $status = CategoryService::editCategory($this->admin_id, $cat_id, $cat_name, $is_out_url, $out_url, $display);
            if ($status) {
                $this->json($status, '操作成功');
            } else {
                $this->json($status, '操作失败');
            }
        }
        $parentid = $this->getInt('parentid', 0);
        $cat_id = $this->getInt('cat_id');
        $cat_type_list = YCore::dict('category_type_list');
        $detail = CategoryService::getCategoryDetail($cat_id);
        $list = CategoryService::getCategoryList(0);
        $this->_view->assign('parentid', $parentid);
        $this->_view->assign('detail', $detail);
        ;
        $this->_view->assign('list', $list);
        $this->_view->assign('cat_type_list', $cat_type_list);
    }
    
    /**
     * 删除分类。
     */
    public function deleteAction() {
        $cat_id = $this->getInt('cat_id');
        $status = CategoryService::deleteCategory($this->admin_id, $cat_id);
        if ($status) {
            $this->json($status, '删除成功');
        } else {
            $this->json($status, '删除失败');
        }
    }
    
    /**
     * 分类排序。
     */
    public function sortAction() {
        if ($this->_request->isPost()) {
            $listorders = $this->getArray('listorders');
            $ok = CategoryService::sortCategory($listorders);
            if ($ok) {
                $this->json($ok, '排序成功');
            } else {
                $this->json($ok, '排序失败');
            }
        }
    }
}