<?php
/**
 * 提醒表。
 * @author winerQin
 * @date 2015-11-10
 */

namespace models;

class Remind extends DbBase {
    
    /**
     * 表名。
     * 
     * @var string
     */
    protected $_table_name = 'ms_remind';
    
    /**
     * 获取提醒列表。
     * 
     * @param number $remind_type 提醒类型。
     * @param number $user_id 用户ID。
     * @param number $page 页码。
     * @param number $count 每页显示条数。
     * @return array
     */
    public function getRemindList($remind_type = -1, $user_id = -1, $page = 1, $count = 10) {
        $offset = $this->getPaginationOffset($page, $count);
        $columns = ' * ';
        $where = ' WHERE status = :status ';
        $params = [
                ':status' => 1 
        ];
        if ($remind_type != - 1) {
            $where .= ' AND remind_type = :remind_type ';
            $params[':remind_type'] = $remind_type;
        }
        if ($user_id != - 1) {
            $where .= ' AND user_id = :user_id ';
            $params[':user_id'] = $user_id;
        }
        $order_by = ' ORDER BY remind_id DESC ';
        $sql = "SELECT COUNT(1) AS count FROM {$this->_table_name} {$where}";
        $sth = $this->link->prepare($sql);
        $sth->execute($params);
        $count_data = $sth->fetch();
        $total = $count_data ? $count_data['count'] : 0;
        $sql = "SELECT {$columns} FROM {$this->_table_name} {$where} {$order_by} LIMIT {$offset},{$count}";
        $sth = $this->link->prepare($sql);
        $sth->execute($params);
        $list = $sth->fetchAll();
        $result = array(
                'list' => $list,'total' => $total,'page' => $page,'count' => $count,
                'isnext' => $this->IsHasNextPage($total, $page, $count) 
        );
        return $result;
    }
    
    /**
     * 添加提醒。
     * 
     * @param number $user_id 用户ID。
     * @param number $remind_type 提醒类型。
     * @param string $title 提醒标题。
     * @param string $content 提醒内容。
     * @param number $year 年。
     * @param number $month 月。
     * @param number $day 日。
     * @param number $hour 时。
     * @param number $minute 分。
     * @param number $second 秒。
     * @return boolean
     */
    public function addRemind($user_id, $remind_type, $title, $content, $year, $month, $day, $hour, $minute, $second) {
        $data = [
                'user_id' => $user_id,'remind_type' => $remind_type,'title' => $title,'content' => $content,
                'remind_year' => $year,'remind_month' => $month,'remind_day' => $day,'remind_hour' => $hour,
                'remind_minute' => $minute,'remind_second' => $second,'status' => 1,'is_deleted' => 0,
                'created_time' => $_SERVER['REQUEST_TIME'] 
        ];
        $config_id = $this->insert($data);
        return $config_id ? true : false;
    }
}