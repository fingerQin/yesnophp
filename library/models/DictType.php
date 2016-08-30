<?php
/**
 * 字典类型表。
 * @author winerQin
 * @date 2015-11-10
 */

namespace models;

use common\YCore;
class DictType extends DbBase {

	/**
	 * 表名。
	 * @var string
	 */
	protected $_table_name = 'ms_dict_type';

	/**
	 * 获取字典类型列表。
	 * @param string $keyword 查询关键词。模糊搜索字典类型编码、字典类型名称。
	 * @param number $page 页码。
	 * @param number $count 每页显示条数。
	 * @return array
	 */
	public function getDictTypeList($keyword = '', $page = 1, $count = 10) {
	    $offset  = $this->getPaginationOffset($page, $count);
	    $columns = ' dict_type_id,type_code,type_name,description ';
	    $where   = ' WHERE status = :status ';
	    $params = [
	        ':status' => 1,
	    ];
	    if (strlen($keyword) > 0) {
	        $where .= ' AND ( type_code LIKE :type_code OR type_name LIKE :type_name )';
	        $params[':type_code'] = "%{$keyword}%";
	        $params[':type_name'] = "%{$keyword}%";
	    }
	    $order_by = ' ORDER BY dict_type_id ASC ';
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
	 * 获取字典类型信息。
	 * @param number $dict_type_id 字典类型ID。
	 * @return array
	 */
	public function getDictTypeDetail($dict_type_id) {
		$data = $this->fetchOne([], ['dict_type_id' => $dict_type_id]);
		return empty($data) ? [] : $data;
	}

	/**
	 * 删除字典类型数据。
	 * @param number $admin_id 修改人ID。管理员ID。
	 * @param number $dict_type_id 字典类型ID。
	 * @return boolean
	 */
	public function deleteDictType($admin_id, $dict_type_id) {
		$where = ['dict_type_id' => $dict_type_id];
		$data = [
		    'status'        => 2,
		    'modified_by'   => $admin_id,
		    'modified_time' => $_SERVER['REQUEST_TIME']
		];
		return $this->update($data, $where);
	}

	/**
	 * 添加字典类型。
	 * @param number $admin_id 修改人ID（管理员ID）。
	 * @param string $type_code 字典类型code编码。
	 * @param string $type_name 字典类型名称。
	 * @param string $description 字典类型描述。
	 * @return boolean
	 */
	public function addDictType($admin_id, $type_code, $type_name, $description) {
	    $where = [
	        'type_code' => $type_code,
	        'status'    => 1
	    ];
	    $dict_type_detail = $this->fetchOne([], $where);
	    if (!empty($dict_type_detail)) {
	        YCore::exception(8500001, '字典编码已经存在,请不要重复添加');
	    }
		$data = [
				'type_code'    => $type_code,
				'type_name'    => $type_name,
				'description'  => $description,
				'created_by'   => $admin_id,
				'status'       => 1,
				'created_time' => $_SERVER['REQUEST_TIME']
		];
		$id = $this->insert($data);
		return $id ? true : false;
	}

	/**
	 * 编辑字典类型。
	 * @param number $admin_id 修改人ID（管理员ID）。
	 * @param number $dict_type_id 字典类型ID。
	 * @param string $type_code 字典类型code编码。
	 * @param string $type_name 字典类型名称。
	 * @param string $description 字典类型描述。
	 * @return boolean
	 */
	public function editDictType($admin_id, $dict_type_id, $type_code, $type_name, $description) {
		$data = [
				'type_code'     => $type_code,
				'type_name'     => $type_name,
				'description'   => $description,
				'modified_by'   => $admin_id,
				'modified_time' => $_SERVER['REQUEST_TIME']
		];
		$where = ['dict_type_id' => $dict_type_id];
		return $this->update($data, $where);
	}
}