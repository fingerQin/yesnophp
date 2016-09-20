<?php
/**
 * 后台管理员。
 * -- 1、本业务模块误码：6002xxx
 * @author winerQin
 * @date 2015-11-19
 */
namespace services;

use common\YCore;
use models\Admin;
use winer\Validator;
use models\AdminRole;
use models\Menu;
use models\AdminRolePriv;
use models\AdminLoginHistory;
use models\DbBase;
use winer\MobileDetect;
use common\YUrl;

class AdminService extends BaseService {

    /**
     * 获取管理员列表。
     *
     * @param string $keyword 查询关键词(账号、手机、姓名)。
     * @param int $page 当前页码。
     * @param int $count 每页显示条数。
     * @return array
     */
    public static function getAdminList($keyword = '', $page, $count) {
        $admin_model = new Admin();
        $result = $admin_model->getAdminList($keyword, $page, $count);
        $admin_role_model = new AdminRole();
        foreach ($result['list'] as $key => $item) {
            $role = $admin_role_model->getRole($item['roleid']);
            $item['rolename']      = $role['rolename'];
            $item['created_time']  = YCore::format_timestamp($item['created_time']);
            $item['lastlogintime'] = YCore::format_timestamp($item['lastlogintime']);
            $result['list'][$key]  = $item;
        }
        return $result;
    }

    /**
     * 添加管理员。
     *
     * @param string $realname 真实姓名。
     * @param string $username 账号。
     * @param string $password 密码。
     * @param string $mobilephone 手机号码。
     * @param number $roleid 角色ID。
     * @return boolean
     */
    public static function addAdmin($realname, $username, $password, $mobilephone, $roleid) {
        // [1]
        $data = [
            'realname'    => $realname,
            'username'    => $username,
            'password'    => $password,
            'mobilephone' => $mobilephone
        ];
        $rules = [
            'realname'    => '真实姓名|require:5000001|len:5000001:2:20:1',
            'username'    => '账号|require:5000001|alpha_dash:5000002|len:5000003:6:20:0',
            'password'    => '密码|require:5000004|alpha_dash:5000005|len:5000006:6:20:0',
            'mobilephone' => '手机号码|require:5000001|mobilephone:5000001'
        ];
        Validator::valido($data, $rules); // 验证不通过会抛异常。
                                          // [2]
        $admin_model  = new Admin();
        $admin_detail = $admin_model->fetchOne([], ['username' => $username,'status' => 1]);
        if (! empty($admin_detail)) {
            YCore::exception('-1', '管理员账号已经存在');
        }
        // [3]
        $admin_role_model = new AdminRole();
        $role_detail = $admin_role_model->getRole($roleid);
        if (empty($role_detail) || $role_detail['status'] != 1) {
            YCore::exception('-1', '角色不存在或已经删除');
        }
        $salt = YCore::create_randomstr(6);
        $md5_password = self::encryptPassword($password, $salt);
        $data = [
            'realname'     => $realname,
            'username'     => $username,
            'password'     => $md5_password,
            'mobilephone'  => $mobilephone,
            'salt'         => $salt,
            'roleid'       => $roleid,
            'status'       => 1,
            'created_time' => $_SERVER['REQUEST_TIME']
        ];
        $status = $admin_model->insert($data);
        return $status ? true : false;
    }

    /**
     * 编辑管理员。
     *
     * @param int $admin_id 管理员ID。
     * @param int $realname 真实姓名。
     * @param string $password 密码。不填则保持原密码。
     * @param string $mobilephone 手机号码。
     * @param int $roleid 角色ID。
     * @return boolean
     */
    public static function editAdmin($admin_id, $realname, $mobilephone, $roleid, $password = '') {
        // [1]
        $data = [
            'realname'    => $realname,
            'mobilephone' => $mobilephone
        ];
        $rules = [
            'realname'    => '真实姓名|require:5000001|len:5000001:2:20:1',
            'mobilephone' => '手机号码|require:5000001|mobilephone:5000001'
        ];
        if (strlen($password) > 0) {
            $data['password']  = $password;
            $rules['password'] = '密码|require:5000004|alpha_dash:5000005|len:5000006:6:20:0';
        }
        Validator::valido($data, $rules); // 验证不通过会抛异常。
                                          // [2]
        $admin_model = new Admin();
        $admin_detail = $admin_model->getUserOfByAdminId($admin_id);
        if (empty($admin_detail)) {
            YCore::exception('-1', '该账号不存在或已经删除');
        }
        // [3]
        $admin_role_model = new AdminRole();
        $role_detail = $admin_role_model->getRole($roleid);
        if (empty($role_detail) || $role_detail['status'] != 1) {
            YCore::exception('-1', '角色不存在或已经删除');
        }
        $data = [
            'realname'    => $realname,
            'mobilephone' => $mobilephone,
            'roleid'      => $roleid
        ];
        if (strlen($password) > 0) {
            $salt = YCore::create_randomstr(6);
            $md5_password = self::encryptPassword($password, $salt);
            $data['password'] = $md5_password;
            $data['salt']     = $salt;
        }
        $where = [
            'admin_id' => $admin_id
        ];
        $status = $admin_model->update($data, $where);
        return $status ? true : false;
    }

    /**
     * 删除管理员账号。
     * -- 1、超级管理员账号是不允许删除的。
     *
     * @param int $op_admin_id 操作管理员ID。
     * @param int $admin_id 管理员账号ID。
     * @return boolean
     */
    public static function deleteAdmin($op_admin_id, $admin_id) {
        $admin_model  = new Admin();
        $admin_detail = $admin_model->getUserOfByAdminId($admin_id);
        if (empty($admin_detail)) {
            YCore::exception(- 1, '管理员不存在或已经删除');
        }
        if ($admin_detail['status'] != 1) {
            YCore::exception(- 1, '管理员不存在或已经删除');
        }
        if ($admin_id == 1) {
            YCore::exception(- 1, '超级管理员账号不能删除');
        }
        $data = [
            'status' => 2
        ];
        $where = [
            'admin_id' => $admin_id
        ];
        return $admin_model->update($data, $where);
    }

    /**
     * 获取管理员账号详情。
     *
     * @param int $admin_id 管理员账号ID。
     * @return array
     */
    public static function getAdminDetail($admin_id) {
        $admin_model  = new Admin();
        $admin_detail = $admin_model->fetchOne([], [
            'admin_id' => $admin_id,
            'status' => 1
        ]);
        if (empty($admin_detail)) {
            YCore::exception(- 1, '管理员不存在或已经删除');
        }
        return $admin_detail;
    }

    /**
     * 管理员登录。
     * -- 1、后续增加IP限制与登录错误次数限制。
     *
     * @param string $username 账号。
     * @param string $password 密码。
     * @return boolean
     */
    public static function login($username, $password) {
        if (strlen($username) === 0) {
            YCore::exception(6002001, '账号不能为空');
        }
        if (strlen($password) === 0) {
            YCore::exception(6002002, '密码不能为空');
        }
        $admin_model = new Admin();
        $admin_info = $admin_model->fetchOne([], [
            'username' => $username,
            'status' => 1
        ]);
        if (empty($admin_info)) {
            YCore::exception(6002003, '账号不存在');
        }
        $encrypt_password = self::encryptPassword($password, $admin_info['salt']);
        if ($encrypt_password != $admin_info['password']) {
            YCore::exception(6002004, '密码不正确');
        }
        self::addAdminLoginHistory($admin_info['admin_id']);
        $update_data = [
            'lastlogintime' => $_SERVER['REQUEST_TIME']
        ];
        $update_where = [
            'admin_id' => $admin_info['admin_id']
        ];
        $admin_model->update($update_data, $update_where);
        $auth_token = self::createToken($admin_info['admin_id'], $encrypt_password);
        self::setAuthTokenLastAccessTime($admin_info['admin_id'], $auth_token, $_SERVER['REQUEST_TIME']);
        $admin_cookie_domain = YUrl::getDomainName(false);
        setcookie('admin_token', $auth_token, 0, '/', $admin_cookie_domain);
        return true;
    }

    /**
     * 添加管理员登录历史。
     *
     * @param number $admin_id 管理员ID。
     * @return void
     */
    private static function addAdminLoginHistory($admin_id) {
        $browser_type = 'computer';
        $detect = new MobileDetect();
        if ($detect->isMobile() && ! $detect->isTablet()) {
            $browser_type = 'phone';
        } else if ($detect->isMobile() && $detect->isTablet()) {
            $browser_type = 'tablet';
        }
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $ip = YCore::ip();
        $address = '';

        $ch = curl_init();
        $url = "http://apis.baidu.com/apistore/iplookupservice/iplookup?ip={$ip}";
        $header = [
            'apikey: 9728c130acfa61d31ec2d814e0d438aa'
        ];
        // 添加apikey到header
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // 执行HTTP请求
        curl_setopt($ch, CURLOPT_URL, $url);
        $res = curl_exec($ch);
        $ip_info = json_decode($res, true);
        if (empty($ip_info) || $ip_info['errNum'] != 0) {
            // YCore::exception(-1, 'IP定位失败');
            $address = '';
        } else {
            $address = "{$ip_info['retData']['country']}{$ip_info['retData']['province']}{$ip_info['retData']['city']}{$ip_info['retData']['district']}{$ip_info['retData']['carrier']}";
        }
        $admin_login_history_model = new AdminLoginHistory();
        $data = [
            'admin_id'     => $admin_id,
            'user_agent'   => $user_agent,
            'ip'           => $ip,
            'browser_type' => $browser_type,
            'address'      => $address,
            'created_time' => $_SERVER['REQUEST_TIME']
        ];
        return $admin_login_history_model->insert($data);
    }

    /**
     * 获取管理员登录记录。
     *
     * @param number $admin_id 管理员ID。
     * @param number $page 当前页码。
     * @param number $count 每页显示条数。
     * @return array
     */
    public static function getAdminLoginHistoryList($admin_id = -1, $page = 1, $count = 20) {
        $offset = self::getPaginationOffset($page, $count);
        $from_table = ' FROM ms_admin_login_history AS a LEFT JOIN ms_admin AS b ON(a.admin_id = b.admin_id) ';
        $columns = ' b.admin_id,b.realname,b.username,b.mobilephone,a.created_time,a.browser_type,a.ip,a.address ';
        $where = ' WHERE 1 = 1 ';
        $params = [];
        if ($admin_id != - 1) {
            $where .= ' AND a.admin_id = :admin_id ';
            $params[':admin_id'] = $admin_id;
        }
        $default_db = new DbBase();
        $order_by = ' ORDER BY a.id DESC ';
        $sql = "SELECT COUNT(1) AS count {$from_table} {$where}";
        $count_data = $default_db->rawQuery($sql, $params)->rawFetchOne();
        $total = $count_data ? $count_data['count'] : 0;
        $sql   = "SELECT {$columns} {$from_table} {$where} {$order_by} LIMIT {$offset},{$count}";
        $list  = $default_db->rawQuery($sql, $params)->rawFetchAll();
        $result = [
            'list'   => $list,
            'total'  => $total,
            'page'   => $page,
            'count'  => $count,
            'isnext' => self::IsHasNextPage($total, $page, $count)
        ];
        return $result;
    }

    /**
     * 修改密码。
     *
     * @param int $admin_id 用户ID。
     * @param string $old_pwd 旧密码。
     * @param string $new_pwd 新密码。
     * @return boolean
     */
    public static function editPwd($admin_id, $old_pwd, $new_pwd) {
        if (strlen($old_pwd) === 0) {
            YCore::exception(6002301, '旧密码必须填写');
        }
        if (strlen($new_pwd) === 0) {
            YCore::exception(6002302, '新密码必须填写');
        }
        $admin_model = new Admin();
        $admin_info = $admin_model->getUserOfByAdminId($admin_id);
        if (empty($admin_info) || $admin_info['status'] != 1) {
            YCore::exception(6002303, '管理员不存在或已经删除');
        }
        if (! Validator::is_len($new_pwd, 6, 20, true)) {
            YCore::exception(6002304, '新密码长度必须6~20之间');
        }
        if (! Validator::is_alpha_dash($new_pwd)) {
            YCore::exception(6002305, '新密码格式不正确');
        }
        $encrypt_password = self::encryptPassword($new_pwd, $admin_info['salt']);
        $ok = $admin_model->editPwd($admin_id, $encrypt_password);
        if (! $ok) {
            YCore::exception(6002306, '密码修改失败');
        }
        return true;
    }

    /**
     * 获取管理员详情。
     *
     * @param number $admin_id 管理员ID。
     * @return array
     */
    public static function getAdminInfo($admin_id) {
        $admin_model = new Admin();
        $data = $admin_model->fetchOne([], [
            'admin_id' => $admin_id,
            'status' => 1
        ]);
        if (empty($data)) {
            YCore::exception(- 1, '管理员不存在或已经删除');
        }
        return $data;
    }

    /**
     * 管理员修改自己的资料。
     *
     * @param int $admin_id 管理员ID。
     * @param string $realname 真实姓名。
     * @param string $mobilephone 手机号码。
     * @return boolean
     */
    public static function editInfo($admin_id, $realname, $mobilephone) {
        $admin_model = new Admin();
        $data = [
            'realname'    => $realname,
            'mobilephone' => $mobilephone
        ];
        $rules = [
            'realname'    => '真实姓名|require:6002401|len:6002402:2:20:1',
            'mobilephone' => '手机号码|require:6002403|mobilephone:6002404'
        ];
        Validator::valido($data, $rules);
        unset($data, $rules);
        $ok = $admin_model->editInfo($admin_id, $realname, $mobilephone);
        if (! $ok) {
            YCore::exception(6002307, '修改失败');
        }
        return true;
    }

    /**
     * 退出登录。
     */
    public static function logout() {
        $admin_cookie_domain = YUrl::getDomainName(false);
        $valid_time = $_SERVER['REQUEST_TIME'] - 3600;
        setcookie('admin_token', '', $valid_time, '/', $admin_cookie_domain);
    }

    /**
     * 检查用户权限。
     * -- 1、在每次用户访问程序的时候调用。
     *
     * @param string $module_name 模块名称。
     * @param string $ctrl_name 控制器名称。
     * @param string $action_name 操作名称。
     * @return array 基本信息。
     */
    public static function checkAuth($module_name, $ctrl_name, $action_name) {
        // [1] token解析
        $token = isset($_COOKIE['admin_token']) ? $_COOKIE['admin_token'] : '';
        $token_params = self::parseToken($token);
        $admin_id = $token_params['admin_id'];
        $password = $token_params['password'];
        $access_time = $_SERVER['REQUEST_TIME'];
        // [2] 用户存在与否判断
        $admin_model = new Admin();
        $admin_info = $admin_model->fetchOne([], [
            'admin_id' => $admin_id,
            'status' => 1
        ]);
        if (empty($admin_info)) {
            self::logout();
            YCore::exception(6002101, '账号不存在或已经被禁用');
        }
        if ($password != $admin_info['password']) {
            self::logout();
            YCore::exception(6002102, '您的密码被修改,请重新登录');
        }
        // [3] token是否赶出了超时时限
        $cache = YCore::getCache();
        $cache_key_token = "admin_token_key_{$admin_id}";
        $cache_key_time  = "admin_access_time_key_{$admin_id}";
        $cache_token     = $cache->get($cache_key_token);
        if ($cache_token === false) {
            self::logout();
            YCore::exception(6002103, '您还没有登录');
        }
        if ($cache_token === null) {
            self::logout();
            YCore::exception(6002104, '登录超时,请重新登录');
        }
        if ($cache_token != $token) {
            self::logout();
            YCore::exception(6002105, '您的账号已在其他地方登录');
        }
        $ok = self::checkMenuPower($admin_info['roleid'], $module_name, $ctrl_name, $action_name);
        if (! $ok) {
            YCore::exception(6002106, '您没有权限执行此操作');
        }
        self::setAuthTokenLastAccessTime($admin_id, $token, $access_time);
        $data = [
            'admin_id'    => $admin_info['admin_id'],
            'realname'    => $admin_info['realname'],
            'username'    => $admin_info['username'],
            'mobilephone' => $admin_info['mobilephone'],
            'roleid'      => $admin_info['roleid']
        ];
        return $data;
    }

    /**
     * 检查指定角色的菜单权限。
     *
     * @param number $roleid 角色ID。
     * @param string $module_name 模块名称。
     * @param string $ctrl_name 控制器名称。
     * @param string $action_name 操作名称。
     * @return boolean
     */
    private static function checkMenuPower($roleid, $module_name, $ctrl_name, $action_name) {
        if ($roleid == 1) {
            return true; // 超级管理员组拥有绝对的权限。
        }
        $menu_model = new Menu();
        $where = [
            'c' => $ctrl_name,
            'a' => $action_name
        ];
        $menu_info = $menu_model->fetchOne([], $where);
        if (empty($menu_info)) {
            return false;
        }
        $where = [
            'roleid'  => $roleid,
            'menu_id' => $menu_info['menu_id']
        ];
        $admin_role_priv = new AdminRolePriv();
        $priv = $admin_role_priv->fetchOne([], $where);
        if (empty($priv)) {
            return false;
        }
        return true;
    }

    /**
     * 设置auth_token最后的访问时间。
     *
     * @param int $admin_id 管理员ID。
     * @param string $auth_token auth_token。
     * @param int $access_time 最后访问时间戳。
     * @return void
     */
    private static function setAuthTokenLastAccessTime($admin_id, $auth_token, $access_time) {
        $cache = YCore::getCache();
        $cache_time = YCore::config('admin_logout_time') * 60;
        $cache_key_token = "admin_token_key_{$admin_id}"; // 用户保存auth_token的缓存键。
        $cache_key_time  = "admin_access_time_key_{$admin_id}"; // 用户保存最后访问时间的缓存键。
        $cache->set($cache_key_token, $auth_token, $cache_time);
        $cache->set($cache_key_time, $access_time, $cache_time);
    }

    /**
     * 加密密码。
     *
     * @param string $password 密码明文。
     * @param string $salt 密码加密盐。
     * @return string
     */
    private static function encryptPassword($password, $salt) {
        return md5(md5($password) . $salt);
    }

    /**
     * 生成Token。
     * -- 1、token只分接口与非接口两种模式。
     *
     * @param int $admin_id 管理员ID。
     * @param string $password 用户表password字段。
     * @return string
     */
    private static function createToken($admin_id, $password) {
        $str = "{$admin_id}\t{$password}";
        return YCore::sys_auth($str, 'ENCODE', '', 0);
    }

    /**
     * 解析Token。
     *
     * @param string $token token会话。
     * @return array
     */
    private static function parseToken($token) {
        $data = YCore::sys_auth($token, 'DECODE');
        $data = explode("\t", $data);
        if (count($data) != 2) {
            YCore::exception(6004003, '登录超时,请重新登录');
        }
        $result = [
            'admin_id' => $data[0],  // 用户ID。
            'password' => $data[1]
        ]; // 加密的密码。

        return $result;
    }

}