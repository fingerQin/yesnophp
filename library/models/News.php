<?php
/**
 * 文章表。
 * @author winerQin
 * @date 2016-03-27
 */

namespace models;

class News extends DbBase {

    /**
     * 表名。
     * @var string
     */
    protected $_table_name = 'ms_news';

    /**
     * 文章列表。
     * @param string $title 文章标题。
     * @param number $admin_id 管理员ID。
     * @param string $starttime 开始时间。
     * @param string $endtime 截止时间。
     * @param number $page 分页页码。
     * @param number $count 每页显示记录条数。
     * @return array
     */
    public function getList($title = '', $admin_id = -1, $starttime = '', $endtime = '', $page, $count) {
        $offset  = $this->getPaginationOffset($page, $count);
        $columns = ' * ';
        $where   = ' WHERE status = :status ';
        $params = [
            ':status' => 1,
        ];
        if (strlen($title) > 0) {
            $where .= ' AND title LIKE :title ';
            $params[':title'] = "%{$title}%";
        }
        if (strlen($starttime) > 0) {
            $where .= ' AND created_time > :starttime ';
            $params[':starttime'] = strtotime($starttime);
        }
        if (strlen($endtime) > 0) {
            $where .= ' AND created_time < :endtime ';
            $params[':endtime'] = strtotime($endtime);
        }
        if ($admin_id != -1) {
            $where .= ' AND created_by = :admin_id ';
            $params[':admin_id'] = $admin_id;
        }
        $order_by = ' ORDER BY news_id DESC ';
        $sql = "SELECT COUNT(1) AS count FROM {$this->_table_name} {$where}";
        $sth = $this->link->prepare($sql);
        $sth->execute($params);
        $count_data = $sth->fetch();
        $total  = $count_data ? $count_data['count'] : 0;
        $sql = "SELECT {$columns} FROM {$this->_table_name} {$where} {$order_by} LIMIT {$offset},{$count}";
        $sth = $this->link->prepare($sql);
        $sth->execute($params);
        $list = $sth->fetchAll();
        $result = array(
            'list'   => $list,
            'total'  => $total,
            'page'   => $page,
            'count'  => $count,
            'isnext' => $this->IsHasNextPage($total, $page, $count),
        );
        return $result;
    }
}