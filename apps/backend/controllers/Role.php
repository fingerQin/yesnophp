<?php
use services\AdminPermissionService;
/**
 * 角色管理。
 * 
 * @author winerQin
 *         @date 2015-11-26
 */

class RoleController extends \common\controllers\Admin {
    
    /**
     * 角色列表。
     */
    public function indexAction() {
        $role_list = AdminPermissionService::getRoleList();
        $this->_view->assign('list', $role_list);
    }
    
    /**
     * Ajax方式获取角色列表。
     */
    public function ajaxRoleListAction() {
        if ($this->_request->isXmlHttpRequest()) {
            $role_list = AdminPermissionService::getRoleList();
            $this->json(true, 'ok', $role_list);
        }
        $this->end();
    }
    
    /**
     * 添加角色。
     */
    public function addAction() {
        if ($this->_request->isXmlHttpRequest()) {
            $rolename = $this->getString('rolename');
            $listorder = $this->getInt('listorder');
            $description = $this->getString('description');
            $status = AdminPermissionService::addRole($rolename, $listorder, $description);
            if ($status) {
                $this->json($status, '添加成功');
            } else {
                $this->json($status, '添加失败');
            }
        }
    }
    
    /**
     * 编辑角色。
     */
    public function editAction() {
        if ($this->_request->isXmlHttpRequest()) {
            $roleid = $this->getInt('roleid');
            $rolename = $this->getString('rolename');
            $listorder = $this->getInt('listorder');
            $description = $this->getString('description');
            $status = AdminPermissionService::editRole($roleid, $rolename, $listorder, $description);
            if ($status) {
                $this->json($status, '修改成功');
            } else {
                $this->json($status, '修改失败');
            }
        }
        $roleid = $this->getInt('roleid');
        $role = AdminPermissionService::getRoleDetail($roleid);
        $this->_view->assign('role', $role);
    }
    
    /**
     * 删除角色。
     */
    public function deleteAction() {
        $roleid = $this->getInt('roleid');
        $status = AdminPermissionService::deleteRole($roleid);
        if ($status) {
            $this->json($status, '删除成功');
        } else {
            $this->json($status, '删除失败');
        }
        $this->end();
    }
    
    /**
     * 设置角色权限。
     */
    public function setPermissionAction() {
        if ($this->_request->isXmlHttpRequest()) {
            $roleid = $this->getInt('roleid');
            $arr_menu_id = $this->getArray('menuid');
            $status = AdminPermissionService::setRolePermission($roleid, $arr_menu_id);
            if ($status) {
                $this->json($status, '设置成功');
            } else {
                $this->json($status, '设置失败');
            }
        }
    }
    
    /**
     * 获取角色权限的菜单ID。
     */
    public function getRolePermissionMenuAction() {
        $roleid = $this->getInt('roleid');
        $priv_menu_list = AdminPermissionService::getRolePermissionMenu($roleid);
        $list = AdminPermissionService::getMenuList(0);
        $this->_view->assign('list', $list);
        $this->_view->assign('roleid', $roleid);
        $this->_view->assign('priv_menu_list', $priv_menu_list);
    }
}