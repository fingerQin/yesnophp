<?php
/**
 * 字典数据表。
 * @author winerQin
 * @date 2015-11-10
 */

namespace models;

class Dict extends DbBase {

    /**
     * 表名。
     *
     * @var string
     */
    protected $_table_name = 'ms_dict';

    /**
     * 字典类型的字典值是否为空。
     *
     * @param number $dict_type_id 字典类型ID。
     * @return boolean true:空、false:非空。
     */
    public function isNotEmpty($dict_type_id) {
        $where = [
            'dict_type_id' => $dict_type_id,
            'status'       => 1
        ];
        $count = $this->count($where);
        return $count > 0 ? false : true;
    }

    /**
     * 获取字典列表。
     *
     * @param number $dict_type_id 字典类型ID。
     * @param string $keywords 查询关键词。查询值编码或名称。
     * @param number $page 当前页码。
     * @param number $count 每页显示条数。
     * @return array
     */
    public function getDictList($dict_type_id, $keywords = '', $page = 1, $count = 10) {
        $offset  = $this->getPaginationOffset($page, $count);
        $columns = " dict_id,dict_type_id,dict_code,dict_value,description,listorder ";
        $where   = ' WHERE status = :status AND dict_type_id = :dict_type_id ';
        $params  = [
                ':status' => 1,':dict_type_id' => $dict_type_id
        ];
        if (strlen($keywords) > 0) {
            $where .= ' AND (dict_code LIKE :dict_code OR dict_value LIKE :dict_value )';
            $params[':dict_code']  = "%{$keywords}%";
            $params[':dict_value'] = "%{$keywords}%";
        }
        $order_by = ' ORDER BY listorder ASC,dict_id ASC ';
        $sql = "SELECT COUNT(1) AS count FROM {$this->_table_name} {$where}";
        $count_data = $this->rawQuery($sql, $params)->rawFetchOne();
        $total = $count_data ? $count_data['count'] : 0;
        $sql   = "SELECT {$columns} FROM {$this->_table_name} {$where} {$order_by} LIMIT {$offset},{$count}";
        $list  = $this->rawQuery($sql, $params)->rawFetchAll();
        $result = [
            'list'   => $list,
            'total'  => $total,
            'page'   => $page,
            'count'  => $count,
            'isnext' => $this->IsHasNextPage($total, $page, $count)
        ];
        return $result;
    }

    /**
     * 获取字典数据。
     *
     * @param number $dict_id 字典ID。
     * @return array
     */
    public function getDict($dict_id) {
        $columnds = [
                'dict_id','dict_value','dict_type_id','dict_code','description','listorder','status'
        ];
        $data = $this->fetchOne($columnds, ['dict_id' => $dict_id]);
        return empty($data) ? [] : $data;
    }

    /**
     * 获取字典值。
     *
     * @param number $dict_type_id 字典类型ID。
     * @return array|null
     */
    public function getValues($dict_type_id) {
        $where = [
            'status'       => 1,
            'dict_type_id' => $dict_type_id
        ];
        $column = [
            'dict_value',
            'dict_code'
        ];
        $order = 'listorder ASC, dict_id ASC';
        $result = $this->fetchAll($column, $where, 0, $order);
        if ($result) {
            $data = [];
            foreach ($result as $val) {
                $data[$val['dict_code']] = $val['dict_value'];
            }
            return $data;
        } else {
            return null;
        }
    }

    /**
     * 添加字典值。
     *
     * @param number $admin_id 管理员ID。
     * @param number $dict_type_id 字典类型ID。
     * @param string $dict_code 字典编码。
     * @param string $dict_name 字典名称。
     * @param string $description 字典描述。
     * @param int $listorder 排序。
     * @return boolean
     */
    public function addDict($admin_id, $dict_type_id, $dict_code, $dict_value, $description = '', $listorder = 0) {
        $data = [
            'dict_type_id' => $dict_type_id,
            'dict_code'    => $dict_code,
            'dict_value'   => $dict_value,
            'description'  => $description,
            'listorder'    => $listorder,
            'status'       => 1,
            'created_by'   => $admin_id,
            'created_time' => $_SERVER['REQUEST_TIME']
        ];
        $dict_id = $this->insert($data);
        return $dict_id ? true : false;
    }

    /**
     * 编辑字典值。
     *
     * @param number $dict_id 字典值ID。
     * @param number $admin_id 管理员ID。
     * @param number $dict_type_id 字典类型ID。
     * @param string $dict_code 字典编码。
     * @param string $dict_value 字典值。
     * @param string $description 字典描述。
     * @param number $listorder 排序。
     * @return boolean
     */
    public function editDict($dict_id, $admin_id, $dict_code, $dict_value, $description = '', $listorder = 0) {
        $data = [
            'dict_code'     => $dict_code,
            'dict_value'    => $dict_value,
            'description'   => $description,
            'listorder'     => $listorder,
            'status'        => 1,
            'modified_by'   => $admin_id,
            'modified_time' => $_SERVER['REQUEST_TIME']
        ];
        $where = [
            'dict_id' => $dict_id
        ];
        $dict_id = $this->update($data, $where);
        return $dict_id ? true : false;
    }

    /**
     * 删除字典值。
     *
     * @param number $dict_id 字典值ID。
     * @param number $admin_id 管理员ID。
     * @return boolean
     */
    public function deleteDict($dict_id, $admin_id) {
        $data = [
            'status'        => 2,
            'modified_by'   => $admin_id,
            'modified_time' => $_SERVER['REQUEST_TIME']
        ];
        $where = [
            'dict_id' => $dict_id
        ];
        $dict_id = $this->update($data, $where);
        return $dict_id ? true : false;
    }

    /**
     * 设置字段值排序。
     *
     * @param number $admin_id 管理员ID。
     * @param number $dict_id 字段ID。
     * @param number $sort 排序值。
     * @return boolean
     */
    public function sort($admin_id, $dict_id, $sort) {
        $data = [
            'modified_by'   => $admin_id,
            'listorder'     => $sort,
            'modified_time' => $_SERVER['REQUEST_TIME']
        ];
        $where = [
            'dict_id' => $dict_id
        ];
        $dict_id = $this->update($data, $where);
        return $dict_id ? true : false;
    }
}