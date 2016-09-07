<?php
/**
 * IP禁止表。
 * @author winerQin
 * @date 2016-03-24
 */

namespace models;

class IpBan extends DbBase {
    
    /**
     * 表名。
     * 
     * @var string
     */
    protected $_table_name = 'ms_ip_ban';
    
    /**
     * 获取列表。
     * 
     * @param string $ip 查询关键词。查询IP段。即为后缀模糊查询。
     * @param number $page 页码。
     * @param number $count 每页显示条数。
     * @return array
     */
    public function geList($keyword = '', $page = 1, $count = 10) {
        $offset = $this->getPaginationOffset($page, $count);
        $columns = ' * ';
        $where = ' WHERE 1 ';
        $params = [];
        if (strlen($keyword) > 0) {
            $where .= ' AND ip LIKE :ip ';
            $params[':ip'] = "{$keyword}%"; // 通过后缀模糊查询可以实现IP查询。
        }
        $order_by = ' ORDER BY id DESC ';
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
     * 添加。
     * 
     * @param number $admin_id 管理员ID。
     * @param string $ip IP地址。
     * @param string $remark 备注。
     * @return boolean
     */
    public function addIp($admin_id, $ip, $remark) {
        $data = [
                'ip' => $ip,'remark' => $remark,'created_by' => $admin_id,'created_time' => $_SERVER['REQUEST_TIME'] 
        ];
        $config_id = $this->insert($data);
        return $config_id ? true : false;
    }
    
    /**
     * 编辑。
     * 
     * @param number $id 配置ID。
     * @param number $admin_id 管理员ID。
     * @param string $ip IP地址。
     * @param string $remark 备注。
     * @return boolean
     */
    public function editIp($id, $admin_id, $ip, $remark) {
        $data = [
                'ip' => $ip,'remark' => $remark,'modified_by' => $admin_id,'modified_time' => $_SERVER['REQUEST_TIME'] 
        ];
        $where = [
                'id' => $id 
        ];
        return $this->update($data, $where);
    }
    
    /**
     * 删除。
     * 
     * @param number $id ID。
     * @param number $admin_id 管理员ID。
     * @return boolean
     */
    public function deleteIp($id, $admin_id) {
        $where = [
                'id' => $id 
        ];
        return $this->delete($where);
    }
}