<?php
/**
 * 后台菜单表。
 * @author winerQin
 * @date 2015-11-17
 */

namespace models;

class Menu extends DbBase {
    
    /**
     * 表名。
     * 
     * @var string
     */
    protected $_table_name = 'ms_menu';
    
    /**
     * 获取菜单信息。
     * 
     * @param number $menu_id 菜单ID。
     * @return array
     */
    public function getMenu($menu_id) {
        $where = [
                'menu_id' => $menu_id 
        ];
        return $this->fetchOne([], $where);
    }
    
    /**
     * 获取所有的菜单。
     * 
     * @return array
     */
    public function getAllMenu() {
        return $this->fetchAll([], [], 0, 'menu_id ASC,listorder ASC');
    }
    
    /**
     * 通过父分类ID读取子菜单。
     * 
     * @param number $parent_id 父分类ID。
     * @param number $is_get_hide 是否获取隐藏的菜单。
     * @return array
     */
    public function getByParentToMenu($parent_id, $is_get_hide = true) {
        $where = [
                'parentid' => $parent_id 
        ];
        if ($is_get_hide == false) {
            $where['display'] = 1;
        }
        $order = 'listorder ASC,menu_id ASC';
        return $this->fetchAll([], $where, 0, $order);
    }
    
    /**
     * 添加菜单。
     * 
     * @param array $data 菜单信息。
     * @return boolean
     */
    public function addMenu($data) {
        return $this->insert($data);
    }
    
    /**
     * 编辑菜单。
     * 
     * @param number $menu_id 菜单ID。
     * @param array $data 菜单信息。
     * @return boolean
     */
    public function editMenu($menu_id, $data) {
        $where = [
                'menu_id' => $menu_id 
        ];
        return $this->update($data, $where);
    }
    
    /**
     * 删除菜单。
     * 
     * @param number $menu_id 菜单ID。
     * @return boolean
     */
    public function deleteMenu($menu_id) {
        $where = [
                'menu_id' => $menu_id 
        ];
        return $this->delete($where);
    }
    
    /**
     * 设置菜单排序值。
     * 
     * @param number $menu_id 菜单ID。
     * @param array $sort_val 排序值。
     * @return boolean
     */
    public function sortMenu($menu_id, $sort_val) {
        $data = [
                'listorder' => $sort_val 
        ];
        $where = [
                'menu_id' => $menu_id 
        ];
        return $this->update($data, $where);
    }
}