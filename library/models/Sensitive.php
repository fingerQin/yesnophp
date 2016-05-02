<?php
/**
 * 敏感词表。
 * @author winerQin
 * @date 2016-03-23
 */

namespace models;

use common\YCore;
class Sensitive extends DbBase {

	/**
	 * 表名。
	 * @var string
	 */
	protected $_table_name = 'ms_sensitive';

	/**
	 * 获取敏感词列表。
	 * @param string $keyword 查询关键词。模糊搜索敏感词。
	 * @param number $lv 敏感词等级。-1全部、其他示为等级。
	 * @param number $page 页码。
	 * @param number $count 每页显示条数。
	 * @return array
	 */
	public function getList($keyword = '', $lv = -1, $page = 1, $count = 10) {
	    $offset  = $this->getPaginationOffset($page, $count);
	    $columns = ' * ';
	    $where   = ' WHERE 1 ';
	    $params  = [];
	    if (strlen($keyword) > 0) {
	        $where .= ' AND val LIKE :val ';
	        $params[':val'] = "%{$keyword}%";
	    }
	    if ($lv != -1) {
	        $where .= ' AND lv = :lv ';
	        $params[':lv'] = $lv;
	    }
	    $order_by = ' ORDER BY id DESC ';
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

	/**
	 * 删除敏感词。
	 * @param number $admin_id 操作管理员ID。
	 * @param number $id 敏感词ID。
	 * @return boolean
	 */
	public function deleteSensitive($admin_id, $id) {
		$where = ['id' => $id];
		return $this->delete($where);
	}

	/**
	 * 添加敏感词。
	 * @param number $admin_id 操作管理员ID。
	 * @param string $lv 字典类型code编码。
	 * @param string $val 字典类型名称。
	 * @return boolean
	 */
	public function addSensitive($admin_id, $lv, $val) {
	    $where = [
	        'val' => $val
	    ];
	    $data = $this->fetchOne([], $where);
	    if (!empty($data)) {
	        YCore::throw_exception(8500001, '敏感词已经存在,请勿重复添加');
	    }
		$data = [
				'lv'           => $lv,
				'val'          => $val,
				'created_by'   => $admin_id,
				'created_time' => $_SERVER['REQUEST_TIME']
		];
		$id = $this->insert($data);
		return $id ? true : false;
	}

	/**
	 * 编辑敏感词。
	 * @param number $id 敏感词ID。
	 * @param number $admin_id 操作管理员ID。
	 * @param string $lv 字典类型code编码。
	 * @param string $val 字典类型名称。
	 * @return boolean
	 */
	public function editSensitive($id, $admin_id, $lv, $val) {
		$data = [
				'lv'            => $lv,
				'val'           => $val,
				'modified_by'   => $admin_id,
				'modified_time' => $_SERVER['REQUEST_TIME']
		];
		$where = ['id' => $id];
		return $this->update($data, $where);
	}
}