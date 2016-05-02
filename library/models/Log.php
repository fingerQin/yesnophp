<?php
/**
 * 日志模型。
 * @author winerQin
 * @date 2015-11-03
 */

namespace models;

class Log extends DbBase {

    /**
     * 表名。
     * @var string
     */
    protected $_table_name = 'ms_log';

	const LOG_TYPE_BUSY      = -1; // 服务器繁忙(所有未知的错误或异常)。
	const LOG_TYPE_SYSTEM    = 1;  // 系统底层级别日志。
	const LOG_TYPE_FRAMEWORK = 2;  // WEB框架级别日志。
	const LOG_TYPE_PACKAGE   = 3;  // 扩展包组件级别日志。
	const LOG_TYPE_LANG      = 4;  // 语言级别的日志。
	const LOG_TYPE_VALIDATOR = 5;  // 验证器级别的日志。
	const LOG_TYPE_SERVICES  = 6;  // 验证级别。
	const LOG_TYPE_USER      = 7;  // 用户级别。
	CONST LOG_TYPE_DEBUG     = 8;  // 调试级别。

	/**
	 * 添加日志。
	 * @param number $log_type 日志类型。
	 * @param string $content 日志内容。
	 * @param number $log_user_id 操作用户ID。
	 * @param string $log_time 日志产生时间。
	 * @param number $errcode 错误编号。
	 * @return bool
	 */
	public function addLog($log_type, $content, $log_user_id, $log_time, $errcode = 0) {
		$data = [
				'log_type'      => $log_type,
				'log_user_id'   => $log_user_id,
				'log_time'      => $log_time,
				'content'       => $content,
				'created_time'  => $_SERVER['REQUEST_TIME'],
				'errcode'       => $errcode
		];
		$insert_id = $this->insert($data);
		return $insert_id > 0 ? true : false;
	}

	/**
	 * 获取日志列表。
	 * @param number $log_type 查询关键词。
	 * @param number $user_id 产生日志的用户。 
	 * @param number $errcode 错误编码。
	 * @param number $starttime 日志产生查询开始时间。
	 * @param number $endtime 日志产生查询结束时间。
	 * @param number $page 页码。
	 * @param number $count 每页显示条数。
	 * @return array
	 */
	public function getDictTypeList($log_type = 0, $user_id = 0, $errcode = 0, $starttime = 0, $endtime = 0, $page = 1, $count = 10) {
	    $offset  = $this->getPaginationOffset($page, $count);
	    $columns = ' * ';
	    $where   = ' WHERE 1 = 1 ';
	    $params = [];
	    if ($log_type > 0) {
	        $where .= ' AND log_type = :log_type ';
	        $params[':log_type'] = $log_type;
	    }
	    if ($user_id > 0) {
	        $where .= ' AND log_user_id = :log_user_id ';
	        $params[':log_user_id'] = $user_id;
	    }
	    if ($errcode > 0) {
	        $where .= ' AND errcode = :errcode ';
	        $params[':errcode'] = $errcode;
	    }
	    if ($starttime > 0 && $endtime > 0) {
	        $where .= ' AND log_time BETWEEN :start_log_time AND :end_log_time ';
	        $params[':start_log_time'] = $starttime;
	        $params[':end_log_time']   = $endtime;
	    }
	    $order_by = ' ORDER BY log_id DESC ';
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