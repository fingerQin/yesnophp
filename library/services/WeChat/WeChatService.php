<?php
/**
 * 公众号管理。
 * @author winerQin
 * @date 2016-04-25
 */

namespace services\WeChat;

use services\BaseService;
use common\YCore;
use winer\Validator;
use models\WxAccount;
use models\WxMenu;
use models\WxGroup;
class WeChatService extends BaseService {

    /**
     * 获取微信公众号列表。
     * @param string $account 公众号。
     * @param string $sn 公众号编号。
     * @param string $appid APPID。
     * @param number $page 当前页码。
     * @param number $count 每页显示条数。
     * @return array
     */
    public static function getWeChatAccountList($account = '', $sn = '', $appid = '', $page = 1, $count = 10) {
        $wx_account_model = new WxAccount();
        $account_list = $wx_account_model->getList($account, $sn, $appid, $page, $count);
        return $account_list;
    }

    /**
     * 添加公众号。
     * @param number $admin_id 管理员ID。
     * @param string $account 公众号账号。
     * @param number $type 公众号类型。1订阅号、2服务号、3企业号。
     * @param number $auth 是否通过认证。
     * @param string $appid 公众号appid。
     * @param string $appsecret 公众号密钥。
     * @param string $token 接口token。
     * @param string $aeskey 消息加密AES key值。
     * @return boolean
     */
    public static function addWeChatAccount($admin_id, $account, $type, $auth, $appid, $appsecret, $token, $aeskey) {
        $data = [
            'wx_account'   => $account,
            'wx_type'      => $type,
            'wx_auth'      => $auth,
            'wx_appid'     => $appid,
            'wx_appsecret' => $appsecret,
            'wx_token'     => $token,
            'wx_aeskey'    => $aeskey
        ];
        $rules = [
            'wx_account'   => '公众号账号|require:1000000|len:1:80:1:1000000',
            'wx_type'      => '公众号类型|require:1000000|integer:1000000|number_between:1:3:1000000',
            'wx_auth'      => '认证状态|require:1000000|integer:1000000|number_between:0:1:1000000',
            'wx_appid'     => 'AppID|require:1000000|len:1:50:0:1000000|alpha_number:1000000',
            'wx_appsecret' => 'AppSecret|require:1000000|alpha_number:1000000|len:1:50:1',
            'wx_token'     => 'TOKEN|require:1000000|alpha_number:1000000|len:2:32:1:1000000',
            'wx_aeskey'    => 'EncodingAESKey|require:1000000|alpha_number:1000000|len:43:43:1:1000000'
        ];
        Validator::valido($data, $rules);
        $wx_account_model = new WxAccount();
        $wx_account_info = $wx_account_model->fetchOne([], ['wx_account' => $account, 'status' => 1]);
        if (!empty($wx_account_info)) {
            YCore::throw_exception(-1, '公众号已经存在，请勿重复添加');
        }
        $data['wx_sn']        = uniqid();
        $data['created_time'] = $_SERVER['REQUEST_TIME'];
        $data['created_by']   = $admin_id;
        return $wx_account_model->insert($data);
    }

    /**
     * 编辑公众号。
     * @param number $admin_id 管理员ID。
     * @param number $account_id 公众账号ID。
     * @param string $account 公众号账号。
     * @param number $type 公众号类型。1订阅号、2服务号、3企业号。
     * @param number $auth 是否通过认证。
     * @param string $appid 公众号appid。
     * @param string $appsecret 公众号密钥。
     * @param string $token 接口token。
     * @param string $aeskey 消息加密AES key值。
     * @return boolean
     */
    public static function editWeChatAccount($admin_id, $account_id, $account, $type, $auth, $appid, $appsecret, $token, $aeskey) {
        $data = [
            'wx_account'   => $account,
            'wx_type'      => $type,
            'wx_auth'      => $auth,
            'wx_appid'     => $appid,
            'wx_appsecret' => $appsecret,
            'wx_token'     => $token,
            'wx_aeskey'    => $aeskey
        ];
        $rules = [
            'wx_account'   => '公众号账号|require:1000000|len:1:80:1:1000000',
            'wx_type'      => '公众号类型|require:1000000|integer:1000000|number_between:1:3:1000000',
            'wx_auth'      => '认证状态|require:1000000|integer:1000000|number_between:0:1:1000000',
            'wx_appid'     => 'AppID|require:1000000|len:1:50:0:1000000|alpha_number:1000000',
            'wx_appsecret' => 'AppSecret|require:1000000|alpha_number:1000000|len:1:50:1',
            'wx_token'     => 'TOKEN|require:1000000|alpha_number:1000000|len:2:32:1:1000000',
            'wx_aeskey'    => 'EncodingAESKey|require:1000000|alpha_number:1000000|len:43:43:1:1000000'
        ];
        Validator::valido($data, $rules);
        $wx_account_model = new WxAccount();
        $wx_account_info = $wx_account_model->fetchOne([], ['account_id' => $account_id, 'status' => 1]);
        if (empty($wx_account_info)) {
            YCore::throw_exception(-1, '记录不存在或已经删除');
        }
        $wx_account_info = $wx_account_model->fetchOne([], ['wx_account' => $account, 'status' => 1]);
        if (!empty($wx_account_info) && $wx_account_info['account_id'] != $account_id) {
            YCore::throw_exception(-1, '公众号已经存在，请检查！');
        }
        $data['modified_time'] = $_SERVER['REQUEST_TIME'];
        $data['modified_by']   = $admin_id;
        return $wx_account_model->update($data, ['account_id' => $account_id, 'status' => 1]);
    }

    /**
     * 删除公众号。
     * @param number $account_id 公众号ID。
     * @param number $admin_id 管理员ID。
     * @return boolean
     */
    public static function deleteWeChatAccount($admin_id, $account_id) {
        $wx_account_model = new WxAccount();
        $where = [
            'account_id' => $account_id,
            'status'     => 1
        ];
        $wx_account_info = $wx_account_model->fetchOne([], $where);
        if (empty($wx_account_info)) {
            YCore::throw_exception(-1, '记录不存在或已经删除');
        }
        $data = [
            'status'        => 2,
            'modified_time' => $_SERVER['REQUEST_TIME'],
            'modified_by'   => $admin_id
        ];
        return $wx_account_model->update($data, $where);
    }

    /**
     * 设置公众号菜单[只是在数据库表中增加菜单记录并不会立即更新到公众号]。
     * -- Example start --
     * $data = [
     *      'menu_id'     => '菜单ID。此值如果传递则认为是修改。',
     *      'menu_name'   => '菜单名称',
     *      'menu_type'   => '菜单事件类型',
     *      'parentid'    => '父菜单ID。如果没有父菜单则为0',
     *      'menu_key'    => '菜单key。如果对应的菜单类型没有key请直接忽略。',
     *      'media_id'    => '媒体素材ID。如果对应的菜单类型没有请直接忽略。',
     *      'is_outside'  => '是否为站外链接:0否、1是',
     *      'outside_url' => '站外链接',
     *      'module_name' => '模块名称。组成站内链接的模块名称。',
     *      'ctrl_name'   => '控制器名称。组成站内链接的控制器名称。',
     *      'action_name' => '操作名称。组成站内链接的操作名称。',
     *      'url_query'   => 'URL附带参数。组成站内链接时的附加参数。格式：username=winer&sex=1',
     *      'display'     => '菜单是否显示。临时性的关闭。',
     * ];
     * -- Example end --
     * @param string $wx_sn 系统分配给微信公众号的唯一编号。
     * @param number $admin_id 管理员ID。
     * @param array $data 菜单数据。
     * @return boolean
     */
    public static function setMenu($wx_sn, $admin_id, array $data) {
        if ($data['parentid'] == 0) {
            if (!Validator::is_len($data['menu_name'], 1, 4, true)) {
                YCore::throw_exception(-1, '一级菜单名称长度必须1~4个字符之间');
            }
        } else {
            if (!Validator::is_len($data['menu_name'], 1, 7, true)) {
                YCore::throw_exception(-1, '二菜单名称长度必须1~7个字符之间');
            }
        }
        switch ($data['menu_type']) {
            case 'click':
                if (!Validator::is_len($data['menu_key'], 1, 30, true)) {
                    YCore::throw_exception(-1, '点击事件的key值长度必须1~30个字符之间');
                }
                $data['is_outside']  = 0;
                $data['outside_url'] = '';
                $data['module_name'] = '';
                $data['ctrl_name']   = '';
                $data['action_name'] = '';
                $data['url_query']   = '';
                break;
            case 'view':
                $data['menu_key']    = '';
                $data['is_outside']  = 0;
                if ($data['is_outside'] == 0) {
                    $data['outside_url'] = '';
                    if (!Validator::is_len($data['module_name'], 1, 50)) {
                        YCore::throw_exception(-1, '模块名称长度必须1~50字符之间');
                    }
                    if (!Validator::is_len($data['ctrl_name'], 1, 50)) {
                        YCore::throw_exception(-1, '控制器名称长度必须1~50字符之间');
                    }
                    if (!Validator::is_len($data['module_name'], 1, 50)) {
                        YCore::throw_exception(-1, '操作名称长度必须1~50字符之间');
                    }
                    if (!Validator::is_len($data['url_query'], 1, 100, true)) {
                        YCore::throw_exception(-1, 'URL附加参数长度必须1~100字符之间');
                    }
                } else {
                    if (!Validator::is_url($data['action_name'])) {
                        YCore::throw_exception(-1, '外部链接必须填写');
                    }
                    $data['module_name'] = '';
                    $data['ctrl_name']   = '';
                    $data['action_name'] = '';
                    $data['url_query']   = '';
                }
                break;
            default :
                YCore::throw_exception(-1, '不允许未知的事件类型');
                break;
        }
        $wx_menu_model = new WxMenu();
        if (isset($data['menu_id'])) { // 修改。
            $where = [
                'menu_id' => $data['menu_id'],
                'status'  => 1
            ];
            $wx_menu_info = $wx_menu_model->fetchOne([], $where);
            if (empty($wx_menu_info)) {
                YCore::throw_exception(-1, '菜单不存在或已经删除');
            }
            $data['modified_time'] = $_SERVER['REQUEST_TIME'];
            $data['modified_by']   = $admin_id;
            return $wx_menu_model->upate($data, $where);
        } else {
            $data['status']       = 1;
            $data['created_time'] = $_SERVER['REQUEST_TIME'];
            $data['created_by']   = $admin_id;
            return $wx_menu_model->insert($data);
        }
    }

    /**
     * 删除菜单。
     * @param number $menu_id 菜单ID。
     * @param number $admin_id 管理员ID。
     * @return boolean
     */
    public static function deleteMenu($menu_id, $admin_id) {
        $wx_menu_model = new WxMenu();
        $where = [
            'menu_id' => $menu_id,
            'status'  => 1
        ];
        $wx_menu_info = $wx_menu_model->fetchOne([], $where);
        if (empty($wx_menu_info)) {
            YCore::throw_exception(-1, '菜单不存在或已经删除');
        }
        $data = [
            'status' => 2
        ];
        return $wx_menu_model->update($data, $where);
    }

    /**
     * 获取公众号用户分组。
     * @param number $account_id 公众号ID。
     * @param string $group_name 分组名称。
     * @param number $page 当前页码。
     * @param number $count 每页显示条数。
     * @return array
     */
    public static function getUserGroupList($account_id, $group_name = '', $page = 1, $count = 20) {
        $wx_group_model = new WxGroup();
        return $wx_group_model->getList($account_id, $group_name, $page, $count);
    }

    /**
     * 添加公众号用户分组。
     * @param number $admin_id 管理员ID。
     * @param number $account_id 公众号ID。
     * @param string $group_name 分组名称。
     * @return boolean
     */
    public static function addUserGroup($admin_id, $account_id, $group_name) {
        
    }

    /**
     * 编辑公众号用户分组。
     * @param number $admin_id 管理员ID。
     * @param number $id 分组记录ID。
     * @param string $group_name 分组名称。
     */
    public static function editUserGroup($admin_id, $id, $group_name) {
        
    }

    /**
     * 删除公众号用户分组。
     * @param number $admin_id 管理员ID。
     * @param number $id 分组记录ID。
     * @return boolean
     */
    public static function deleteUserGroup($admin_id, $id) {
        $wx_group_model = new WxGroup();
        $where = [
            'id'     => $id, 
            'status' => 1
        ];
        $detail = $wx_group_model->fetchOne([], $where);
        if (empty($detail)) {
            YCore::throw_exception(-1, '分组不存在或已经删除');
        }
        $data = [
            'modified_time' => $_SERVER['REQUEST_TIME'],
            'status'        => 2,
            'modified_by'   => $admin_id
        ];
        return $wx_group_model->update($data, $where);
    }

    /**
     * 用户分组详情。
     * @param number $id 分组记录ID。
     * @return array
     */
    public static function getUserGroupDetail($id) {
        $wx_group_model = new WxGroup();
        $detail = $wx_group_model->fetchOne([], ['id' => $id, 'status' => 1]);
        if (empty($detail)) {
            YCore::throw_exception(-1, '分组不存在或已经删除');
        }
        return $detail;
    }

    /**
     * 初始化公众号用户分组。
     * -- 从公众号取回已有的分组并插入数据库中。
     * @param number $admin_id 管理员ID。
     * @param number $account_id 公众号ID。
     * @return boolean
     */
    public static function initWeChatUserGroup($admin_id, $account_id) {
        
    }
}