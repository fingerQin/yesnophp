<?php
/**
 * 管理员权限管理。
 * -- 1、本业务模块误码：6003xxx
 * @author winerQin
 * @date 2015-11-19
 */
namespace services;

use models\AdminRole;
use common\YCore;
use winer\Validator;
use models\Menu;
use models\AdminRolePriv;
use models\Admin;
use models\DbBase;

class AdminPermissionService extends BaseService {

    /**
     * 获取角色列表。
     *
     * @return array
     */
    public static function getRoleList() {
        $admin_role_model = new AdminRole();
        return $admin_role_model->getAllRole(false);
    }

    /**
     * 添加角色。
     *
     * @param string $rolename 角色名称。
     * @param number $listorder 排序。小在前。
     * @param string $description 角色介绍。
     * @return bool
     */
    public static function addRole($rolename, $listorder = 0, $description = '') {
        // [1] 验证
        $data = [
            'rolename'    => $rolename,
            'listorder'   => $listorder,
            'description' => $description
        ];
        $rules = [
            'rolename'    => '角色|require:6003001|len:6003002:2:10:1',
            'listorder'   => '排序|require:6003003|integer:6003004',
            'description' => '角色介绍|require:6003005|len:6003006:1:100:1'
        ];
        Validator::valido($data, $rules); // 验证不通过会抛异常。
        $admin_role_model     = new AdminRole();
        $data['created_time'] = $_SERVER['REQUEST_TIME'];
        $data['is_default']   = 0;
        $data['status']       = 1;
        $roleid = $admin_role_model->addRole($data);
        return $roleid ? true : false;
    }

    /**
     * 编辑角色。
     *
     * @param number $roleid 角色ID。
     * @param string $rolename 角色名称。
     * @param number $listorder 排序。
     * @param string $description 角色介绍。
     * @return boolean
     */
    public static function editRole($roleid, $rolename, $listorder = 0, $description = '') {
        $data = [
            'rolename'    => $rolename,
            'listorder'   => $listorder,
            'description' => $description
        ];
        $rules = [
            'rolename'    => '角色|require:6003007|len:6003008:2:10:1',
            'listorder'   => '排序|require:6003009|integer:6003010',
            'description' => '角色介绍|require:6003011|len:6003012:1:100:1'
        ];
        Validator::valido($data, $rules); // 验证不通过会抛异常。
        $admin_role_model = new AdminRole();
        $role_info = $admin_role_model->getRole($roleid);
        if (empty($role_info) || $role_info['status'] != 1) {
            YCore::exception(6003013, '角色不存在或已经删除');
        }
        return $admin_role_model->editRole($roleid, $data);
    }

    /**
     * 角色删除。
     *
     * @param number $roleid 角色ID。
     * @return boolean
     */
    public static function deleteRole($roleid) {
        $admin_role_model = new AdminRole();
        $role_info = $admin_role_model->getRole($roleid);
        if (empty($role_info) || $role_info['status'] != 1) {
            YCore::exception(6003014, '角色不存在或已经删除');
        }
        if ($role_info['is_default'] == 1) {
            YCore::exception(- 1, '默认角色不能删除');
        }
        $admin_model = new Admin();
        $admin_count = $admin_model->count([
            'roleid' => $roleid,
            'status' => 1
        ]);
        if ($admin_count == 0) {
            return $admin_role_model->deleteRole($roleid);
        } else {
            YCore::exception(- 1, '请将该角色下的管理员移动到其它角色下');
        }
    }

    /**
     * 角色详情。
     *
     * @param number $roleid 角色ID。
     * @return boolean
     */
    public static function getRoleDetail($roleid) {
        $admin_role_model = new AdminRole();
        $role_info = $admin_role_model->getRole($roleid);
        if (empty($role_info) || $role_info['status'] != 1) {
            YCore::exception(6003014, '角色不存在或已经删除');
        }
        return $role_info;
    }

    /**
     * 获取指定角色且指定父菜单的子菜单。
     *
     * @param number $roleid 角色ID。
     * @param number $parentid 父菜单ID。
     * @return array
     */
    public static function getRoleSubMenu($roleid, $parentid) {
        if ($roleid == 1) { // 超级管理员验证角色权限。
            return self::getSubMenu($parentid);
        } else {
            $default_db = new DbBase();
            $sql = 'SELECT b.* FROM ms_admin_role_priv AS a INNER JOIN ms_menu AS b '
                 . 'ON(a.menu_id=b.menu_id AND a.roleid = :roleid AND b.parentid = :parentid) '
                 . 'WHERE b.display = :display ORDER BY b.listorder ASC,b.menu_id ASC';
            $params = [
                ':parentid' => $parentid,
                ':roleid'   => $roleid,
                ':display'  => 1
            ];
            $list = $default_db->rawQuery($sql, $params)->rawFetchAll();
            return $list ? $list : [];
        }
    }

    /**
     * 获取指定ID的子菜单。
     *
     * @param number $parent_id 父ID。
     * @param number $is_get_hide 是否获取隐藏的菜单。
     * @return array
     */
    public static function getSubMenu($parent_id, $is_get_hide = false) {
        $menu_model = new Menu();
        $menu_list = $menu_model->getByParentToMenu($parent_id, $is_get_hide);
        return $menu_list;
    }

    /**
     * 获取管理后台左侧菜单。
     *
     * @param number $roleid 角色ID。
     * @param number $menu_id 菜单ID。
     * @return array
     */
    public static function getAdminLeftMenu($roleid, $menu_id) {
        $menu_list = self::getRoleSubMenu($roleid, $menu_id);
        if (empty($menu_list)) {
            return [];
        }
        foreach ($menu_list as $key => $menu) {
            $menu_list[$key]['sub_menu'] = self::getRoleSubMenu($roleid, $menu['menu_id']);
        }
        return $menu_list;
    }

    /**
     * 获取菜单列表[tree]。
     *
     * @param number $parentid 父ID。默认值0。
     * @param string $children_name 子节点键名。
     * @return array
     */
    public static function getMenuList($parentid = 0, $children_name = 'sub') {
        $menu_model = new Menu();
        $menu_list  = $menu_model->getByParentToMenu($parentid);
        if (empty($menu_list)) {
            return $menu_list;
        } else {
            foreach ($menu_list as $key => $menu) {
                $menu_list[$key][$children_name] = self::getMenuList($menu['menu_id']);
            }
            return $menu_list;
        }
    }

    /**
     * 获取菜单详情。
     *
     * @param number $menu_id 菜单ID。
     * @return array
     */
    public static function getMenuDetail($menu_id) {
        $menu_model = new Menu();
        return $menu_model->getMenu($menu_id);
    }

    /**
     * 获取菜单面包屑。
     *
     * @param number $menu_id 菜单ID。
     * @param string $crumbs 面包屑。
     * @return string
     */
    public static function getMenuCrumbs($menu_id, $crumbs = '') {
        $menu = self::getMenuDetail($menu_id);
        if ($menu && $menu['parentid'] > 0) {
            $crumbs = " {$menu['name']} > {$crumbs}";
            return self::getMenuCrumbs($menu['parentid'], $crumbs);
        } else {
            return "{$menu['name']} > {$crumbs}";
        }
    }

    /**
     * 添加菜单。
     *
     * @param number $parentid 父ID。
     * @param string $name 菜单名称。
     * @param string $controller_name 控制器名称。
     * @param string $action_name 操作名称。
     * @param string $data 附加参数。
     * @param number $listorder 排序。
     * @param number $display 是否显示。
     * @return boolean
     */
    public static function addMenu($parentid, $name, $controller_name, $action_name, $data, $listorder, $display = 0) {
        $data = [
            'name'      => $name,
            'parentid'  => $parentid,
            'c'         => $controller_name,
            'a'         => $action_name,
            'data'      => $data,
            'listorder' => $listorder,
            'display'   => $display
        ];
        $rules = [
            'name'      => '角色名称|require:6003015|len:6003016:2:10:1',
            'parentid'  => '上级菜单|require:6003017|integer:6003018',
            'c'         => '控制器名称|require:6003020',
            'a'         => '操作名称|require:6003021',
            'data'      => '附加参数|len:6003022:0:100:1',
            'listorder' => '排序|require:6003023|integer:6003024',
            'display'   => '是否显示菜单|require:6003025|integer:6003026'
        ];
        Validator::valido($data, $rules); // 验证不通过会抛异常。
        $menu_model = new Menu();
        return $menu_model->addMenu($data);
    }

    /**
     * 编辑菜单。
     *
     * @param number $menu_id 菜单ID。
     * @param number $parentid 父ID。
     * @param string $name 菜单名称。
     * @param string $controller_name 控制器名称。
     * @param string $action_name 操作名称。
     * @param string $data 附加参数。
     * @param number $listorder 排序。
     * @param number $display 是否显示。
     * @return boolean
     */
    public static function editMenu($menu_id, $parentid, $name, $controller_name, $action_name, $data, $listorder, $display = 0) {
        $data = [
            'name'      => $name,
            'parentid'  => $parentid,
            'c'         => $controller_name,
            'a'         => $action_name,
            'data'      => $data,
            'listorder' => $listorder,
            'display'   => $display
        ];
        $rules = [
            'name'      => '角色名称|require:6003015|len:6003016:2:10:1',
            'parentid'  => '上级菜单|require:6003017|integer:6003018',
            'c'         => '控制器名称|require:6003020',
            'a'         => '操作名称|require:6003021',
            'data'      => '附加参数|len:6003022:0:100:1',
            'listorder' => '排序|require:6003023|integer:6003024',
            'display'   => '是否显示菜单|require:6003025|integer:6003026'
        ];
        Validator::valido($data, $rules); // 验证不通过会抛异常。
        $menu_model = new Menu();
        $menu_info  = $menu_model->getMenu($menu_id);
        if (empty($menu_info)) {
            YCore::exception(6003027, '菜单不存在或已经删除');
        }
        return $menu_model->editMenu($menu_id, $data);
    }

    /**
     * 删除菜单。
     *
     * @param number $menu_id 菜单ID。
     * @return boolean
     */
    public static function deleteMenu($menu_id) {
        $menu_model = new Menu();
        $menu_info  = $menu_model->getMenu($menu_id);
        if (empty($menu_info)) {
            YCore::exception(6003028, '菜单不存在或已经删除');
        }
        $sub_menu = $menu_model->fetchAll([], [
            'parentid' => $menu_id
        ]);
        if ($sub_menu) {
            YCore::exception(- 1, '请先移除该菜单下的子菜单再删除');
        }
        return $menu_model->deleteMenu($menu_id);
    }

    /**
     * 菜单排序。
     *
     * @param array $listorders 菜单排序数据。[ ['菜单ID' => '排序值'], ...... ]
     * @return boolean
     */
    public static function sortMenu($listorders) {
        if (empty($listorders)) {
            return true;
        }
        foreach ($listorders as $menu_id => $sort_val) {
            $menu_model = new Menu();
            $ok = $menu_model->sortMenu($menu_id, $sort_val);
            if (! $ok) {
                return false;
            }
        }
        return true;
    }

    /**
     * 设置角色权限。
     *
     * @param number $roleid 角色ID。
     * @param array $arr_menu_id 菜单ID数组。
     * @return boolean
     */
    public static function setRolePermission($roleid, $arr_menu_id) {
        // [1] 角色判断。
        $admin_role_model = new AdminRole();
        $role_info = $admin_role_model->getRole($roleid);
        if (empty($role_info) || $role_info['status'] != 1) {
            YCore::exception(6003029, '角色不存在或已经删除');
        }
        // [2] 清空角色之前的数据。
        $admin_role_priv_model = new AdminRolePriv();
        $admin_role_priv_model->beginTransaction();
        $admin_role_priv_model->clearRolePriv($roleid);
        // [3] 添加权限到角色。
        $menu_model = new Menu();
        foreach ($arr_menu_id as $menu_id) {
            $menu_info = $menu_model->getMenu($menu_id);
            if (empty($menu_info)) {
                $admin_role_priv_model->rollBack();
                YCore::exception(6003030, '菜单不存在或已经删除');
            }
            $ok = $admin_role_priv_model->addRolePriv($roleid, $menu_id);
            if (! $ok) {
                $admin_role_priv_model->rollBack();
                YCore::exception(6003031, '权限添加失败，请重试');
            }
        }
        $admin_role_priv_model->commit();
        return true;
    }

    /**
     * 获取角色对应的权限菜单(树形结构)。
     *
     * @param number $roleid 角色ID。
     * @return array
     */
    public static function getRolePermissionMenu($roleid) {
        $admin_role_model = new AdminRole();
        $role_info = $admin_role_model->getRole($roleid);
        if (empty($role_info) || $role_info['status'] != 1) {
            YCore::exception(6003032, '角色不存在或已经删除');
        }
        $admin_role_priv_model = new AdminRolePriv();
        $list = $admin_role_priv_model->fetchAll([], [
            'roleid' => $roleid
        ]);
        $priv_menu_list = []; // 只存在菜单ID。
        foreach ($list as $menu) {
            $priv_menu_list[] = $menu['menu_id'];
        }
        return $priv_menu_list;
    }

    /**
     * 检查角色是否拥有当前链接权限。
     *
     * @param number $roleid 角色ID。
     * @param string $m 模块名称。
     * @param string $c 控制器名称。
     * @param string $a 操作名称。
     * @return boolean
     */
    public static function checkRoleMenuPriv($roleid, $c, $a) {
        $role_permission_list = self::getRolePermission($roleid);
        $is_ok = false;
        foreach ($role_permission_list as $per) {
            if ($per['c'] == $c && $per['a'] == $a) {
                $is_ok = true;
                break;
            }
        }
        return $is_ok;
    }

}