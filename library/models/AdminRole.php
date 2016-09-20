<?php
/**
 * 后台菜单表。
 * @author winerQin
 * @date 2015-11-18
 */

namespace models;

class AdminRole extends DbBase {

    /**
     * 表名。
     *
     * @var string
     */
    protected $_table_name = 'ms_admin_role';

    /**
     * 获取全部角色。
     *
     * @param string $is_convert 是否转换为一维的键值数组。['1' => '管理员', '2' => '普通管理员']
     * @return array
     */
    public function getAllRole($is_convert = false) {
        $column = [
            'roleid','rolename','listorder','description','created_time'
        ];
        $where = [
            'status' => 1
        ];
        $role_list = $this->fetchAll($column, $where);
        $data = [];
        if ($is_convert) {
            foreach ($role_list as $role) {
                $data[$role['roleid']] = $role['rolename'];
            }
        } else {
            $data = $role_list;
        }
        return $data;
    }

    /**
     * 获取角色信息。
     *
     * @param number $roleid 角色ID。
     * @return array
     */
    public function getRole($roleid) {
        $where = [
            'roleid' => $roleid
        ];
        return $this->fetchOne([], $where);
    }

    /**
     * 添加角色。
     *
     * @param array $data 角色信息。
     * @return boolean
     */
    public function addRole($data) {
        return $this->insert($data);
    }

    /**
     * 编辑角色。
     *
     * @param number $roleid 角色ID。
     * @param array $data 角色信息。
     * @return boolean
     */
    public function editRole($roleid, $data) {
        $where = [
            'roleid' => $roleid
        ];
        return $this->update($data, $where);
    }

    /**
     * 删除角色。
     *
     * @param number $roleid 角色ID。
     * @return boolean
     */
    public function deleteRole($roleid) {
        $data = [
            'status' => 2
        ];
        $where = [
            'roleid' => $roleid
        ];
        return $this->update($data, $where);
    }
}