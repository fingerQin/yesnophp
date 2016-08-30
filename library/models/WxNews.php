<?php
/**
 * 微信公众号图文消息表。
 * @author winerQin
 * @date 2016-05-26
 */

namespace models;

class WxNews extends DbBase {

	/**
	 * 表名。
	 * @var string
	 */
	protected $_table_name = 'wx_news';

	/**
     * 获取图文消息列表。
     * @param number $account_id 公众号ID。
     * @param string $title 图文消息标题。只作不同图文消息之间区别之用。
     * @param string $start_push_time 推送时间开始。
     * @param string $end_push_time 推送时间结束。
     * @param number $is_push 是事推送。1是、0否、-1全部。
     * @param string $starttime 创建时间开始。
     * @param string $endtime 创建时间结束。
     * @param number $page 当前页码。
     * @param number $count 每页显示条数。
     * @return array
     */
	public function getList($account_id, $title = '', $start_push_time = '', $end_push_time = '', $starttime = '', $is_push = -1, $endtime = '', $page = 1, $count = 20) {
		$offset  = $this->getPaginationOffset($page, $count);
		$columns = ' * ';
		$where   = ' WHERE status = :status AND account_id = :account_id ';
		$params  = [
				':status'     => 1,
				':account_id' => $account_id
		];
		if (strlen($title) > 0) {
			$where .= ' AND title = :title ';
			$params[':title'] = $title;
		}
		if (strlen($start_push_time) > 0) {
			$where .= ' AND push_time >= :start_push_time ';
			$params[':start_push_time'] = strtotime($start_push_time);
		}
		if (strlen($end_push_time) > 0) {
			$where .= ' AND push_time <= :end_push_time ';
			$params[':end_push_time'] = strtotime($end_push_time);
		}
		if (strlen($starttime) > 0) {
			$where .= ' AND created_time >= :starttime ';
			$params[':starttime'] = strtotime($starttime);
		}
		if (strlen($endtime) > 0) {
			$where .= ' AND created_time <= :endtime ';
			$params[':endtime'] = strtotime($endtime);
		}
		if ($is_push != -1) {
			$where .= ' AND is_push = :is_push ';
			$params[':is_push'] = $is_push;
		}
		$order_by = ' ORDER BY news_id DESC ';
		$sql = "SELECT COUNT(1) AS count FROM {$this->_table_name} {$where}";
		$count_data = $this->rawQuery($sql, $params)->rawFetchOne();
		$total  = $count_data ? $count_data['count'] : 0;
		$sql = "SELECT {$columns} FROM {$this->_table_name} {$where} {$order_by} LIMIT {$offset},{$count}";
		$list = $this->rawQuery($sql, $params)->rawFetchAll();
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