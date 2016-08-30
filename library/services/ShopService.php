<?php
/**
 * 商家中心业务封装。
 * @author winerQin
 * @date 2016-06-07
 */

namespace services;


use models\MallShop;
use models\MallShopAdmin;
use common\YCore;
use models\MallShopAuth;
use winer\Validator;
use models\DbBase;
use common\YUrl;
use models\User;
use models\UserBlacklist;
use models\MallShopCategory;
class ShopService extends BaseService {

    /**
     * 商家管理员类型。
     * @var string
     */
    const SHOP_ROLE_ADMIN   = 'admin';   // 店铺拥有人。 
    const SHOP_ROLE_SERVICE = 'service'; // 店铺客服。

    /**
     * 检验当前商家用户是否有访问权限。
     * @param number $user_id 用户ID。
     * @param string $module_name 模块名称。
     * @param string $ctrl_name 控制器名称。
     * @param string $action_name 操作名称。
     * @return array
     */
    public static function checkShopAuth($user_id, $module_name, $ctrl_name, $action_name) {
        $shop_model = new MallShop();
        $where = [
        	'user_id' => $user_id,
        	'status'  => 1
        ];
        $shop_info = $shop_model->fetchOne([], $where);
        if (!empty($shop_info)) { // 店铺拥有人拥有全部权限。
        	return [
	            'shop_name'   => $shop_info['shop_name'],
	            'shop_id'     => $shop_info['shop_id'],
	            'mobilephone' => $shop_info['mobilephone'],
	            'shop_logo'   => $shop_info['shop_logo'],
	            'admin_type'  => ShopService::SHOP_ROLE_ADMIN,
        		'is_lock'     => $shop_info['is_lock']
	        ];
        }
        $shop_admin_model = new MallShopAdmin();
        $where = [
            'user_id' => $user_id,
            'status'  => 1
        ];
        $shop_admin_info = $shop_admin_model->fetchOne([], $where);
        if (empty($shop_admin_info)) {
            YCore::exception(-1, '您没有权限访问');
        }
        $where = [
        	'shop_id' => $shop_admin_info['shop_id'],
        	'status'  => 1
        ];
        $shop_info = $shop_model->fetchOne([], $where);
        if (empty($shop_info)) {
        	YCore::exception(-1, '商家不存在或已经删除');
        }
        $shop_auth_list = self::getShopAuthMenu($shop_admin_info['shop_id'], $shop_admin_info['admin_type']);
        if (empty($shop_auth_list)) {
            YCore::exception(-1, '您没有权限访问');
        }
        if ($shop_info['is_lock'] == 1) {
        	YCore::exception(-1, '商家已经被系统锁定,只允许商家拥有人管理');
        }
        $module_name = strtolower($module_name);
        $ctrl_name   = strtolower($ctrl_name);
        $action_name = strtolower($action_name);
        $is_allow = false;
        foreach ($shop_auth_list as $item) {
            if ($module_name == $item['m'] && $ctrl_name == $item['c'] && $action_name == $item['a']) {
                $is_allow = true;
                break;
            }
        }
        if (!$is_allow) {
            YCore::exception(-1, '您没有权限访问');
        }
        return [
            'shop_name'   => $shop_info['shop_name'],
            'shop_id'     => $shop_info['shop_id'],
            'mobilephone' => $shop_info['mobilephone'],
            'shop_logo'   => $shop_info['shop_logo'],
            'admin_type'  => $shop_admin_info['admin_type'],
        	'is_lock'     => 0
        ];
    }

    /**
     * 获取商家详情。
     * @param number $shop_id 商家ID。
     */
    public static function getShopDetail($shop_id) {
        $shop_model = new MallShop();
        $where = [
            'shop_id' => $shop_id,
            'status'  => 1
        ];
        $columns = [
        	'shop_name', 'shop_logo', 'shop_notice', 'link_man',
        	'mobilephone', 'telephone', 'qq', 'max_goods_count',
        	'user_id', 'max_goods_count', 'is_allow_delete_comment',
        	'is_lock', 'modified_time', 'created_time'
        ];
        $detail = $shop_model->fetchOne([], $where);
        if (empty($detail)) {
            YCore::exception(-1, '店铺不存在');
        }
        if ($detail['user_id'] > 0) {
        	$user_model = new User();
        	$userinfo = $user_model->fetchOne([], ['user_id' => $detail['user_id']]);
        	$detail['account'] = $userinfo['mobilephone'];
        } else {
        	$detail['account'] = '';
        }
        $detail['modified_time'] = $detail['modified_time'] ? date('Y-m-d H:i:s', $detail['modified_time']) : '-';
        $detail['created_time']  = date('Y-m-d H:i:s', $detail['created_time']);
        return $detail;
    }

    /**
     * 添加商家。
     * @param number $admin_id 管理员ID。
     * @param string $shop_name 商家名称。
     * @param string $shop_logo 商家LOGO。
     * @param string $shop_notice 商家公告。
     * @param string $link_man 联系人。
     * @param string $mobilephone 联系手机。
     * @param string $telephone 联系座机。
     * @param string $qq 联系QQ。
     * @param number $max_goods_count 最大能添加的商品数量。
     * @param number $is_allow_delete_comment 是否允许删除评论。
     * @param number $is_lock 是否锁定。锁定之后商家管理员只能只读方式进入。
     * @param string $account 商家拥有人手机账号。
     * @return boolean
     */
    public static function addShop($admin_id, $shop_name, $shop_logo, $shop_notice, $link_man, $mobilephone, $telephone, $qq, $max_goods_count, $is_allow_delete_comment, $is_lock, $account = '') {
        $data = [
            'shop_name'               => $shop_name,
            'shop_logo'               => $shop_logo,
            'shop_notice'             => $shop_notice,
            'link_man'                => $link_man,
            'mobilephone'             => $mobilephone,
            'telephone'               => $telephone,
            'qq'                      => $qq,
            'max_goods_count'         => $max_goods_count,
            'is_allow_delete_comment' => $is_allow_delete_comment,
            'is_lock'                 => $is_lock
        ];
        $rules = [
            'shop_name'               => '店铺名称|require:1000000|len:1000000:1:20:1',
            'shop_logo'               => '店铺LOGO|require:1000000|len:1000000:1:80:1',
            'shop_notice'             => '店铺公告|require:1000000|len:1000000:0:250:1',
            'link_man'                => '店铺联系人|require:1000000|len:1000000:1:20:1',
            'mobilephone'             => '店铺联系电话|require:1000000|mobilephone:1000000',
            'telephone'               => '店铺联系座机|require:1000000|telephone:1000000',
            'qq'                      => '店铺QQ|require:1000000|qq:1000000',
            'max_goods_count'         => '最大商品数量|require:1000000|integer:1000000|number_between:1000000:1:10000000',
            'is_allow_delete_comment' => '是否允许删除评论|require:1000000|integer:1000000|number_between:1000000:0:1',
            'is_lock'                 => '是否锁定|require:1000000|integer:1000000|number_between:1000000:0:1'
        ];
        Validator::valido($data, $rules);
        $userid = 0;
        if (strlen($account) > 0) {
        	$user_model = new User();
        	$userinfo = $user_model->fetchOne([], ['mobilephone' => $account]);
        	if (empty($userinfo)) {
        		YCore::exception(-1, '商家拥有人账号不存在');
        	}
        	$userid = $userinfo['user_id'];
        }
        $shop_model = new MallShop();
        $data['user_id']      = $userid;
        $data['status']       = 1;
        $data['created_by']   = $admin_id;
        $data['created_time'] = $_SERVER['REQUEST_TIME'];
        $shop_id = $shop_model->insert($data);
        if ($shop_id == 0) {
            YCore::exception(-1, '添加失败');
        }
        return true;
    }

    /**
     * 编辑商家。
     * @param number $admin_id 管理员ID。
     * @param string $shop_name 商家名称。
     * @param string $shop_logo 商家LOGO。
     * @param string $shop_notice 商家公告。
     * @param string $link_man 联系人。
     * @param string $mobilephone 联系手机。
     * @param string $telephone 联系座机。
     * @param string $qq 联系QQ。
     * @param number $max_goods_count 最大能添加的商品数量。
     * @param number $is_allow_delete_comment 是否允许删除评论。
     * @param number $is_lock 是否锁定。锁定之后商家管理员只能只读方式进入。
     * @param string $account 商家拥有人手机账号。
     * @return boolean
     */
    public static function editShop($admin_id, $shop_id, $shop_name, $shop_logo, $shop_notice, $link_man, $mobilephone, $telephone, $qq, $max_goods_count, $is_allow_delete_comment, $is_lock, $account= '') {
        $data = [
            'shop_name'               => $shop_name,
            'shop_logo'               => $shop_logo,
            'shop_notice'             => $shop_notice,
            'link_man'                => $link_man,
            'mobilephone'             => $mobilephone,
            'telephone'               => $telephone,
            'qq'                      => $qq,
            'max_goods_count'         => $max_goods_count,
            'is_allow_delete_comment' => $is_allow_delete_comment,
            'is_lock'                 => $is_lock
        ];
        $rules = [
            'shop_name'               => '店铺名称|require:1000000|len:1000000:1:20:1',
            'shop_logo'               => '店铺LOGO|require:1000000|len:1000000:1:80:1',
            'shop_notice'             => '店铺公告|len:1000000:0:250:1',
            'link_man'                => '店铺联系人|require:1000000:1:20:1',
            'mobilephone'             => '店铺联系电话|mobilephone:1000000',
            'telephone'               => '店铺联系座机|require:1000000|telephone:1000000',
            'qq'                      => '店铺QQ|require:1000000|qq:1000000',
            'max_goods_count'         => '最大商品数量|require:1000000|integer:1000000|number_between:1000000:1:10000000',
            'is_allow_delete_comment' => '是否允许删除评论|require:1000000|integer:1000000|number_between:1000000:0:1',
            'is_lock'                 => '是否锁定|require:1000000|integer:1000000|number_between:1000000:0:1'
        ];
        Validator::valido($data, $rules);
        $userid = 0;
        if (strlen($account) > 0) {
        	$user_model = new User();
        	$userinfo = $user_model->fetchOne([], ['mobilephone' => $account]);
        	if (empty($userinfo)) {
        		YCore::exception(-1, '商家拥有人账号不存在');
        	}
        	$userid = $userinfo['user_id'];
        }
        $default_db = new DbBase();
        $shop_model = new MallShop();
        $where = [
            'shop_id' => $shop_id,
            'status'  => 1
        ];
        $shop_info = $shop_model->fetchOne([], $where);
        if (empty($shop_info)) {
            YCore::exception(-1, '商家不存在');
        }
        $default_db->beginTransaction();
        if ($userid > 0) {
            $old_user_id = $shop_info['user_id']; // 原先的用户ID。
            $ok = UserService::changeUserType($old_user_id, UserService::USER_TYPE_NORMAL);
            if (!$ok) {
                $default_db->rollBack();
                YCore::exception(-1, '商家归属变更失败');
            }
        } else {
            $userid = $shop_info['user_id'];
        }
        $data['user_id']       = $userid;
        $data['modified_by']   = $admin_id;
        $data['modified_time'] = $_SERVER['REQUEST_TIME'];
        $ok = $shop_model->update($data, $where);
        if (!$ok) {
            $default_db->rollBack();
            YCore::exception(-1, '保存失败');
        }
        $default_db->commit();
        return true;
    }

    /**
     * 删除店铺。
     * @param number $admin_id 管理员ID。
     * @param number $shop_id 店铺ID。
     * @return boolea
     */
    public static function deleteShop($admin_id, $shop_id) {
        $detail = self::getShopDetail($shop_id);
        $data = [
            'status'        => 2,
            'modified_time' => $_SERVER['REQUEST_TIME'],
            'modified_by'   => $admin_id
        ];
        $where = [
            'shop_id' => $shop_id,
            'status'  => 1
        ];
        $default_db = new DbBase();
        $shop_model = new MallShop();
        $default_db->beginTransaction();
        $ok = $shop_model->update($data, $where);
        if (!$ok) {
            $default_db->rollBack();
            YCore::exception(-1, '删除失败');
        }
        $ok = UserService::changeUserType($detail['user_id'], UserService::USER_TYPE_NORMAL);
        if (!$ok) {
            $default_db->rollBack();
            YCore::exception(-1, '删除失败');
        }
        $default_db->commit();
        return true;
    }

    /**
     * 获取店铺列表。
     * @param string $shop_name 店铺名称。
     * @param number $page 当前页码。 
     * @param number $count 每页显示条数。
     * @return array
     */
    public static function getShopList($shop_name, $page = 1, $count = 20) {
        $offset  = self::getPaginationOffset($page, $count);
        $columns = ' shop_id,shop_name,shop_logo,shop_notice,link_man,mobilephone,telephone,qq,max_goods_count,is_allow_delete_comment,is_lock,modified_time,created_time ';
        $where   = ' WHERE status = :status ';
        $params = [
            ':status' => 1
        ];
        if (strlen($shop_name) > 0) {
            $where .= ' AND sohp_name LIKE :sohp_name ';
            $params[':ip'] = "{$shop_name}%";
        }
        $order_by = ' ORDER BY shop_id DESC ';
        $sql = "SELECT COUNT(1) AS count FROM mall_shop {$where}";
        $default_db = new DbBase();
        $count_data = $default_db->rawQuery($sql, $params)->rawFetchOne();
        $total = $count_data ? $count_data['count'] : 0;
        $sql = "SELECT {$columns} FROM mall_shop {$where} {$order_by} LIMIT {$offset},{$count}";
        $list = $default_db->rawQuery($sql, $params)->rawFetchAll();
        foreach ($list as $k => $v) {
        	$v['shop_logo'] = YUrl::filePath($v['shop_logo']);
        	$list[$k] = $v;
        }
        $result = array(
            'list'   => $list,
            'total'  => $total,
            'page'   => $page,
            'count'  => $count,
            'isnext' => self::IsHasNextPage($total, $page, $count),
        );
        return $result;
    }

    /**
     * 设置店铺基本信息。
     * @param number $user_id 用户ID。
     * @param number $shop_id 店铺ID.
     * @param string $shop_name 店铺名称。
     * @param string $shop_logo 店铺LOGO。
     * @param string $shop_notice 店铺公告。
     * @param string $link_man 联系人，可以对外显示出来的。
     * @param string $mobilephone  店铺联系电话。可以对外显示出来的。
     * @param string $telephone 店铺人机。可以对外显示出来的。
     * @param string $qq 店铺QQ。可以对外显示出来的。
     * @return boolean
     */
    public static function setBaseInfo($user_id, $shop_id, $shop_name, $shop_logo, $shop_notice, $link_man, $mobilephone, $telephone, $qq) {
        $data = [
            'shop_name'   => $shop_name,
            'shop_logo'   => $shop_logo,
            'shop_notice' => $shop_notice,
            'link_man'    => $link_man,
            'mobilephone' => $mobilephone,
            'telephone'   => $telephone,
            'qq'          => $qq
        ];
        $rules = [
            'shop_name'   => '店铺名称|require:1000000|len:1000000:1:20:1',
            'shop_logo'   => '店铺LOGO|require:1000000|len:1000000:1:80:1',
            'shop_notice' => '店铺公告|require:1000000|len:1000000:0:250:1',
            'link_man'    => '店铺联系人|require:1000000|len:1000000:1:20:1',
            'mobilephone' => '店铺联系电话|require:1000000|mobilephone:1000000',
            'telephone'   => '店铺联系座机|telephone:1000000',
            'qq'          => '店铺QQ|require:1000000|qq:1000000'
        ];
        Validator::valido($data, $rules);
        $shop_model = new MallShop();
        $where = [
            'shop_id' => $shop_id
        ];
        $data['modified_time'] = $_SERVER['REQUEST_TIME'];
        $data['modified_by']   = $user_id;
        $ok = $shop_model->update($data, $where);
        if (!$ok) {
            YCore::exception(-1, '保存失败');
        }
        return true;
    }

    /**
     * 获取商家管理员列表。
     * @param unknown $shop_id 商家ID。
     * @param string $mobilephone 管理员手机号码。
     * @param number $page 当前页码。
     * @param number $count 每页显示条数。
     * @return array
     */
    public static function getShopAdminList($shop_id = -1, $mobilephone = '', $page = 1, $count = 10) {
    	$offset  = self::getPaginationOffset($page, $count);
    	$columns = ' * ';
    	$where   = ' WHERE status = :status ';
    	$params = [
    		':status' => 1
    	];
    	if ($shop_id != -1) {
    		$where .= ' AND shop_id = :shop_id ';
    		$params[':shop_id'] = $shop_id;
    	}
    	if (strlen($mobilephone) > 0) {
    		$user_model = new User();
    		$userinfo = $user_model->fetchOne([], ['mobilephone' => $mobilephone]);
    		$userid = $userinfo ? $userinfo['user_id'] : 0;
    		$where .= ' AND user_id = :user_id ';
    		$params[':user_id'] = $userid;
    	}
    	$order_by = ' ORDER BY admin_id DESC ';
    	$sql = "SELECT COUNT(1) AS count FROM mall_shop_admin {$where}";
    	$default_db = new DbBase();
    	$count_data = $default_db->rawQuery($sql, $params)->rawFetchOne();
    	$total = $count_data ? $count_data['count'] : 0;
    	$sql = "SELECT {$columns} FROM mall_shop_admin {$where} {$order_by} LIMIT {$offset},{$count}";
    	$list = $default_db->rawQuery($sql, $params)->rawFetchAll();
    	foreach ($list as $k => $v) {
    		$userinfo = UserService::getUserDetail($v['user_id']);
    		$v['mobilephone'] = $userinfo['mobilephone'];
    		$v['nickname']    = $userinfo['nickname'];
    		$v['realname']    = $userinfo['realname'];
    		$v['avatar']      = $userinfo['avatar'];
    		$list[$k] = $v;
    	}
    	$result = array(
    		'list'   => $list,
    		'total'  => $total,
    		'page'   => $page,
    		'count'  => $count,
    		'isnext' => self::IsHasNextPage($total, $page, $count),
    	);
    	return $result;
    }

    /**
     * 获取商品自定义分类。
     * @param number $cat_id 分类ID。
     * @return array
     */
    public static function getGoodsCategoryDetail($shop_id, $cat_id) {
    	$shop_category_model = new MallShopCategory();
    	$where = [
    		'cat_id'  => $cat_id,
    		'shop_id' => $shop_id
    	];
    	$columns = [
    		'cat_id', 'cat_name', 'listorder'
    	];
    	return $shop_category_model->fetchOne($columns, $where);
    }

    /**
     * 获取商家自定义分类。
     * @param number $shop_id 商家ID。
     * @return array
     */
    public static function getGoodsCategoryList($shop_id) {
    	$default_db = new DbBase();
    	$sql = 'SELECT cat_id,cat_name,listorder FROM mall_shop_category WHERE shop_id = :shop_id '
    		 . 'AND status = :status ORDER BY listorder ASC, cat_id ASC';
    	$params = [
    		':status'  => 1,
    		':shop_id' => $shop_id
    	];
    	$cat_list = $default_db->rawQuery($sql, $params)->rawFetchAll();
    	foreach ($cat_list as $k => $v) {
    		//$v['goods_count'] = GoodsService::getCustomGoodsCount($v['cat_id']);
    		$cat_list[$k] = $v;
    	}
    	return $cat_list;
    }

	/**
	 * 添加商品自定义分类。
	 * @param number $user_id 用户ID。
	 * @param number $shop_id 商家ID。
	 * @param string $cat_name 分类名称。
	 * @param number $listorder 排序值。
	 * @return boolean
	 */
    public static function addGoodsCategory($user_id, $shop_id, $cat_name, $listorder = 0) {
    	$data = [
    		'cat_name'  => $cat_name,
    		'listorder' => $listorder
    	];
    	$rules = [
    		'cat_name'  => '分类名称|require:1000000|len:1000000:1:20:1',
    		'listorder' => '排序值|require:1000000|integer:1000000|number_between:1000000:0:10000'
    	];
    	Validator::valido($data, $rules);
    	$where = [
    		'shop_id'  => $shop_id,
    		'cat_name' => $cat_name,
    		'status'   => 1
    	];
    	$shop_category_model = new MallShopCategory();
    	$category_info = $shop_category_model->fetchOne([], $where);
    	if ($category_info) {
    		YCore::exception(-1, '该分类名称已经存在');
    	}
    	$data = [
    		'cat_name'     => $cat_name,
    		'shop_id'      => $shop_id,
    		'status'       => 1,
    		'listorder'    => $listorder,
    		'created_time' => $_SERVER['REQUEST_TIME'],
    		'created_by'   => $user_id
    	];
    	$cat_id = $shop_category_model->insert($data);
    	if ($cat_id == 0) {
    		YCore::exception(-1, '添加成功');
    	}
    	return true;
    }

    /**
     * 编辑商品自定义分类。
     * @param number $user_id 用户ID。
     * @param number $cat_id 自定义分类ID。
     * @param number $shop_id 商家ID。
     * @param string $cat_name 分类名称。
     * @param number $listorder 排序值。
     * @return boolean
     */
    public static function editGoodsCategory($user_id, $cat_id, $shop_id, $cat_name, $listorder = 0) {
    	$data = [
    		'cat_name'  => $cat_name,
    		'listorder' => $listorder
    	];
    	$rules = [
    		'cat_name'  => '分类名称|require:1000000|len:1000000:1:20:1',
    		'listorder' => '排序值|require:1000000|integer:1000000|number_between:1000000:0:10000'
    	];
    	$where = [
    		'shop_id' => $shop_id,
    		'cat_id'  => $cat_id,
    		'status'  => 1
    	];
    	$shop_category_model = new MallShopCategory();
    	$category_info = $shop_category_model->fetchOne([], $where);
    	if (empty($category_info)) {
    		YCore::exception(-1, '该分类名称不存在或已经删除');
    	}
    	$data = [
    		'cat_name'      => $cat_name,
    		'modified_time' => $_SERVER['REQUEST_TIME'],
    		'modified_by'   => $user_id,
    		'listorder'     => $listorder
    	];
    	$cat_id = $shop_category_model->update($data, $where);
    	if ($cat_id == 0) {
    		YCore::exception(-1, '修改成功');
    	}
    	return true;
    }

    /**
     * 删除商品自定义分类。
     * @param number $user_id 用户ID。
     * @param number $shop_id 商家ID。
     * @param number $cat_id 自定义分类ID。
     * @return boolean
     */
    public static function deleteGoodsCategory($user_id, $shop_id, $cat_id) {
    	$where = [
    		'shop_id' => $shop_id,
    		'cat_id'  => $cat_id,
    		'status'  => 1
    	];
    	$shop_category_model = new MallShopCategory();
    	$category_info = $shop_category_model->fetchOne([], $where);
    	if (empty($category_info)) {
    		YCore::exception(-1, '该分类名称不存在或已经删除');
    	}
    	$data = [
    		'modified_time' => $_SERVER['REQUEST_TIME'],
    		'modified_by'   => $user_id,
    		'status'        => 2
    	];
    	$cat_id = $shop_category_model->update($data, $where);
    	if ($cat_id == 0) {
    		YCore::exception(-1, '修改成功');
    	}
    	return true;
    }

    /**
     * 添加商家管理员(邀请)。
     * @param number $user_id 用户ID。
     * @param number $shop_id 商家ID。
     * @param string $mobilephone 被邀请成为商家管理员的用户手机号。
     * @param string $admin_type 商家管理员类型。
     * @return boolean
     */
    public static function addShopAdmin($user_id, $shop_id, $mobilephone, $admin_type) {
    	$shop_model = new MallShop();
    	$shop_info  = $shop_model->fetchOne([], ['shop_id' => $shop_id, 'status' => 1]);
    	if (empty($shop_info)) {
    		YCore::exception(-1, '商家不存在');
    	}
    	if ($shop_info['user_id'] != $user_id) {
    		YCore::exception(-1, '您不是该商家拥有者，不能执行此操作');
    	}
    	$user_model = new User();
    	$userinfo = $user_model->fetchOne([], ['mobilephone' => $mobilephone]);
    	if (empty($userinfo)) {
    		YCore::exception(-1, '被邀请人的手机号未注册或未绑定用户账号');
    	}
    	$user_blacklist_model = new UserBlacklist();
    	$result = $user_blacklist_model->isForbidden($userinfo['user_id']);
    	if ($result['status']) {
    		YCore::exception(-1, '被邀请人已经被封号');
    	}
    	$default_db = new DbBase();
    	$sql = 'SELECT * FROM mall_shop_admin WHERE user_id = :user_id AND status = :status AND invite_status != :invite_status';
    	$params = [
    		':user_id'       => $userinfo['user_id'],
    		':status'        => 1,
    		':invite_status' => 1
    	];
    	$shop_admin_info = $default_db->rawQuery($sql, $sql)->rawFetchOne();
    	if (empty($shop_admin_info)) {
    		YCore::exception(-1, '被邀请人已经是别的商家管理员或有未处理的商家邀请');
    	}
    	$shop_admin_model = new MallShopAdmin();
    	$data = [
    		'shop_id'       => $shop_id,
    		'admin_type'    => $admin_type,
    		'created_by'    => $user_id,
    		'created_time'  => $_SERVER['REQUEST_TIME'],
    		'status'        => 1,
    		'invite_status' => 0,
    		'user_id'       => $userinfo['user_id']
    	];
    	$ok = $shop_admin_model->insert($data);
    	if (!$ok) {
    		YCore::exception(-1, '操作失败');
    	}
    	return true;
    }
    
    /**
     * 变更店铺管理员类型。
     * -- 1、只有店铺拥有人才能执行此操作。
     * @param number $user_id 用户ID。
     * @param number $shop_id 店铺ID。
     * @param number $change_user_id 被变更管理员ID。
     * @param string $admin_type 管理员类型。
     * @return boolean
     */
    public static function changeShopAdminType($user_id, $shop_id, $change_user_id, $admin_type) {
    	$shop_model = new MallShop();
    	$shop_info  = $shop_model->fetchOne([], ['shop_id' => $shop_id, 'status' => 1]);
    	if (empty($shop_info)) {
    		YCore::exception(-1, '商家不存在');
    	}
    	if ($shop_info['user_id'] != $user_id) {
    		YCore::exception(-1, '您不是该商家拥有者，不能执行此操作');
    	}
    	$data = [
    			'modified_time' => $_SERVER['REQUEST_TIME'],
    			'modified_by'   => $user_id,
    			'admin_type'    => $admin_type
    	];
    	$where = [
    			'user_id' => $change_user_id,
    			'shop_id' => $shop_id
    	];
    	$shop_admin_model = new MallShopAdmin();
    	$ok = $shop_admin_model->update($data, $where);
    	if (!$ok) {
    		YCore::exception(-1, '操作失败');
    	}
    	return true;
    }

    /**
     * 删除店铺管理员。
     * -- 1、只有店铺拥有人才能执行此操作。
     * @param number $user_id 用户ID。
     * @param number $shop_id 店铺ID。
     * @param number $delete_user_id 被删除管理员ID。
     * @return boolean
     */
    public static function deleteShopAdmin($user_id, $shop_id, $delete_user_id) {
    	$shop_model = new MallShop();
    	$shop_info  = $shop_model->fetchOne([], ['shop_id' => $shop_id, 'status' => 1]);
    	if (empty($shop_info)) {
    		YCore::exception(-1, '商家不存在');
    	}
    	if ($shop_info['user_id'] != $user_id) {
    		YCore::exception(-1, '您不是该商家拥有者，不能执行此操作');
    	}
    	$data = [
    		'modified_time' => $_SERVER['REQUEST_TIME'],
    		'modified_by'   => $user_id,
    		'status'        => 2
    	];
    	$where = [
    		'user_id' => $delete_user_id,
    		'shop_id' => $shop_id
    	];
    	$shop_admin_model = new MallShopAdmin();
    	$ok = $shop_admin_model->update($data, $where);
    	if (!$ok) {
    		YCore::exception(-1, '删除失败');
    	}
    	return true;
    }

    /**
     * 获取指定商家管理员的访问菜单。
     * @param number $shop_id 商家ID。
     * @param string $shop_admin_type 商家用户类型。
     * @return array
     */
    protected static function getShopAuthMenu($shop_id, $shop_admin_type) {
        $shop_auth_model = new MallShopAuth();
        $where = [
            'shop_id'        => $shop_id,
            'shop_auth_type' => $shop_admin_type
        ];
        $shop_auth_list = $shop_auth_model->fetchOne([], $where);
        return $shop_auth_list;
    }

    /**
     * 商品自定义分类排序。
     * -- Example start --
     * $listsort = [
     *      [
     *          'cat_id'   => '分类ID',
     *          'listsort' => '排序值',
     *      ],
     *      ......
     * ];
     * -- Example end --
     * @param number $user_id 用户ID。
     * @param number $shop_id 商家ID。
     * @param array $listsort 排序数据。
     * @return boolean
     */
    public static function sort($user_id, $shop_id, $listsort) {
        $default_db = new DbBase();
        $default_db->beginTransaction();
        $shop_category_model = new MallShopCategory();
        foreach ($listsort as $sort) {
            $where = [
                'cat_id'  => $sort['cat_id'],
                'shop_id' => $shop_id,
                'status'  => 1
            ];
            $data = [
                'modified_by'   => $user_id,
                'modified_time' => $_SERVER['REQUEST_TIME'],
                'listorder'     => $sort['listorder']
            ];
            $ok = $shop_category_model->update($data, $where);
            if (!$ok) {
                $default_db->rollBack();
            }
        }
        $default_db->commit();
        return true;
    }
}