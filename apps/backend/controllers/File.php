<?php
/**
 * 菜单管理。
 * @author winerQin
 * @date 2015-11-26
 */

use common\YCore;
use services\FileService;
use winer\Paginator;

class FileController extends \common\controllers\Admin {

    /**
     * 文件列表。
     */
    public function indexAction() {
        $user_type  = $this->getInt('user_type', -1);
        $user_name  = $this->getString('user_name', '');
        $file_md5   = $this->getString('file_md5', '');
        $file_type  = $this->getInt('file_type', -1);
        $start_time = $this->getString('start_time', '');
        $end_time   = $this->getString('end_time', '');
        $page       = $this->getInt(YCore::config('pager'), 1);
        $list       = FileService::getFileList($user_type, $user_name, $file_md5, $file_type, $start_time, $end_time, $page, 20);
        $paginator  = new Paginator($list['total'], 20);
        $page_html  = $paginator->show();
        $this->_view->assign('page_html', $page_html);
        $this->_view->assign('list', $list['list']);
        $this->_view->assign('user_type', $user_type);
        $this->_view->assign('user_name', $user_name);
        $this->_view->assign('file_md5', $file_md5);
        $this->_view->assign('file_type', $file_type);
        $this->_view->assign('start_time', $start_time);
        $this->_view->assign('end_time', $end_time);
    }

    /**
     * 删除文件。
     */
    public function deleteAction() {
        $file_id = $this->getInt('file_id');
        $status = FileService::deleteFile($file_id, $this->admin_id);
        if ($status) {
            $this->json($status, '删除成功');
        } else {
            $this->json($status, '删除失败');
        }
    }
}