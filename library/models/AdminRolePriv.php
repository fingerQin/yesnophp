<?php
/**
 * 角色权限表。
 * @author winerQin
 * @date 2015-11-17
 */

namespace models;

class AdminRolePriv extends DbBase {

	/**
	 * 表名。
	 * @var string
	 */
	protected $_table_name = 'ms_admin_role_priv';

	/**
	 * 清空角色所有权限数据。
	 * @param number $roleid 角色ID。
	 * @return boolean
	 */
	public function clearRolePriv($roleid) {
		$where = ['roleid' => $roleid];
		return $this->delete($where);
	}
	
	/**
	 * 获取角色全部的权限。
	 * @param number $roleid
	 * @reutrn void
	 */
	public function getRolePriv($roleid) {
		$where = ['roleid' => $roleid];
		return $this->fetchAll([], $where, 0);
	}

	/**
	 * 添加角色权限。
	 * @param number $roleid 角色ID。
	 * @param string $menu_id 菜单ID。
	 * @return boolean
	 */
	public function addRolePriv($roleid, $menu_id) {
		$sdata = [
				'roleid'  => $roleid,
				'menu_id' => $menu_id
		];
		$id = $this->insert($sdata);
		return $id ? true : false;
	}
}