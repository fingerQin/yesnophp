<?php
/**
 * 系统配置表。
 * @author winerQin
 * @date 2015-11-13
 */

namespace models;

class Config extends DbBase {

    /**
     * 表名。
     *
     * @var string
     */
    protected $_table_name = 'ms_config';

    /**
     * 获取配置值。
     *
     * @param string $cname 配置名称。
     * @return string|null
     */
    public function getValue($cname) {
        $data = $this->fetchOne(['cvalue'], ['cname' => $cname]);
        return $data ? $data['cvalue'] : null;
    }

    /**
     * 获取配置列表。
     *
     * @param string $keyword 查询关键词。模糊搜索字典类型编码、字典类型名称。
     * @param number $page 页码。
     * @param number $count 每页显示条数。
     * @return array
     */
    public function getConfigList($keyword = '', $page = 1, $count = 10) {
        $offset  = $this->getPaginationOffset($page, $count);
        $columns = " config_id,ctitle,cname,cvalue,description,created_time,modified_time ";
        $where   = ' WHERE status = :status ';
        $params  = [
            ':status' => 1
        ];
        if (strlen($keyword) > 0) {
            $where .= ' AND ( ctitle LIKE :ctitle OR cname LIKE :cname )';
            $params[':ctitle'] = "%{$keyword}%";
            $params[':cname']  = "%{$keyword}%";
        }
        $order_by = ' ORDER BY config_id ASC ';
        $sql = "SELECT COUNT(1) AS count FROM {$this->_table_name} {$where}";
        $count_data = $this->rawQuery($sql, $params)->rawFetchOne();
        $total  = $count_data ? $count_data['count'] : 0;
        $sql    = "SELECT {$columns} FROM {$this->_table_name} {$where} {$order_by} LIMIT {$offset},{$count}";
        $list   = $this->rawQuery($sql, $params)->rawFetchAll();
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
     * 添加配置。
     *
     * @param number $admin_id 管理员ID。
     * @param string $ctitle 配置标题。
     * @param string $cname 配置名称。
     * @param string $cvalue 配置值。
     * @param string $description 配置描述。
     * @return boolean
     */
    public function addConfig($admin_id, $ctitle, $cname, $cvalue, $description) {
        $data = [
            'ctitle'       => $ctitle,
            'cname'        => $cname,
            'cvalue'       => $cvalue,
            'description'  => $description,
            'status'       => 1,
            'created_by'   => $admin_id,
            'created_time' => $_SERVER['REQUEST_TIME']
        ];
        $config_id = $this->insert($data);
        return $config_id ? true : false;
    }

    /**
     * 编辑配置。
     *
     * @param number $config_id 配置ID。
     * @param number $admin_id 管理员ID。
     * @param string $ctitle 配置标题。
     * @param string $cname 配置名称。
     * @param string $cvalue 配置值。
     * @param string $description 配置描述。
     * @return boolean
     */
    public function editConfig($config_id, $admin_id, $ctitle, $cname, $cvalue, $description) {
        $data = [
            'ctitle'        => $ctitle,
            'cname'         => $cname,
            'cvalue'        => $cvalue,
            'description'   => $description,
            'modified_by'   => $admin_id,
            'modified_time' => $_SERVER['REQUEST_TIME']
        ];
        $where = [
            'config_id' => $config_id
        ];
        return $this->update($data, $where);
    }

    /**
     * 删除配置。
     *
     * @param number $config_id 配置ID。
     * @param number $admin_id 管理员ID。
     * @return boolean
     */
    public function deleteConfig($config_id, $admin_id) {
        $data = [
            'status'        => 2,
            'modified_by'   => $admin_id,
            'modified_time' => $_SERVER['REQUEST_TIME']
        ];
        $where = [
            'config_id' => $config_id
        ];
        return $this->update($data, $where);
    }
}