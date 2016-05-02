<?php
/**
 * 日志管理。
 * @author winerQin
 * @date 2016-01-14
 */

use common\YCore;
use services\LogService;
use winer\Paginator;

class LogController extends \common\controllers\Admin {

    /**
     * 日志查看 。
     */
    public function indexAction() {
        $options = [
            'log_type'  => $this->getInt('log_type', -1),
            'log_user'  => $this->getString('log_user', ''),
            'user_type' => $this->getInt('user_type', 1),
            'starttime' => $this->getString('starttime', ''),
            'endtime'   => $this->getString('endtime', ''),
            'errcode'   => $this->getString('errcode', ''),
            'content'   => $this->getString('content', ''),
            'page'      => $this->getInt(YCore::config('pager'), 1),
            'count'     => $this->getInt('count', 50),
        ];
        $result = LogService::getLogList($options);
        $paginator = new Paginator($result['total'], 50);
        $page_html = $paginator->show();
        $this->_view->assign('search', $options);
        $this->_view->assign('page_html', $page_html);
        $this->_view->assign('list', $result['list']);
        $this->_view->assign('search', $options);
    }

    /**
     * 日志详情。
     */
    public function detailAction() {
        $log_id = $this->getInt('log_id');
        $detail = LogService::getLogDetail($log_id);
        $this->_view->assign('detail', $detail);
    }
}