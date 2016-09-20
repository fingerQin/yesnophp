<?php
/**
 * 用户业务封装。
 * --1、用户业务模块错误码段位：6001xxx
 * @author winerQin
 * @date 2015-10-30
 */
namespace services;

use winer\Validator;
use common\YCore;
use models\DbBase;
use models\UserBlacklist;
use models\UserData;
use models\User;
use models\UserLogin;
use models\UserBind;
use models\FindPwd;
use common\YUrl;

class UserService extends BaseService {

    /**
     * 登录模式。
     * -- 根据登录模式的不同验证机制与会话机制不同。
     */
    const LOGIN_MODE_WEB = 'web'; // WEB模式。
    const LOGIN_MODE_API = 'api'; // 接口模式。

    /**
     * 账号类型。
     */
    const ACCOUNT_TYPE_USERNAME = 'username'; // 用户名类型。
    const ACCOUNT_TYPE_PHONE = 'mobilephone'; // 手机号码类型。
    const ACCOUNT_TYPE_EMAIL = 'email'; // 邮箱类型。

    /**
     * 手机注册。
     *
     * @param string $mobilephone 手机号码。
     * @param string $password 密码。
     * @param string $code 验证码。
     * @return number 返回用户ID。
     */
    public static function mobilephoneRegister($mobilephone, $password, $code) {
        // [1] 验证
        $data = [
            'mobilephone' => $mobilephone,
            'password'    => $password,
            'code'        => $code
        ];
        $rules = [
            'mobilephone' => '手机号码|require:6000001|alpha_dash:5000002|len:5000003:6:20:0',
            'password'    => '密码|require:6000002|alpha_dash:5000005|len:5000006:6:20:0',
            'code'        => '验证码|require:6000003|alpha_number:1000000|len:1000000:4:8:0'
        ];
        Validator::valido($data, $rules); // 验证不通过会抛异常。
        $user_model = new User();
        $where = [
            'mobilephone' => $mobilephone
        ];
        $result = $user_model->fetchOne([], $where);
        if (!empty($result)) {
            YCore::exception(-1, '手机号码已经被人注册');
        }
        SmsService::valiCode(SmsService::SMS_TYPE_REGISTER, $mobilephone, $code);
        $salt = YCore::create_randomstr(6);
        $default_db = new DbBase();
        $default_db->beginTransaction();
        $password = self::encryptPassword($password, $salt);
        $insert_data = [
            'username'         => uniqid('mp'),
            'password'         => $password,
            'salt'             => $salt,
            'mobilephone'      => $mobilephone,
            'mobilephone_ok'   => 1,
            'mobilephone_time' => $_SERVER['REQUEST_TIME'],
            'reg_time'         => $_SERVER['REQUEST_TIME']
        ];
        $user_id = $user_model->insert($insert_data);
        if ($user_id == 0) {
            $default_db->rollBack();
            YCore::exception(-1, '服务器繁忙,请稍候重试');
        }
        $user_data_model = new UserData();
        $ok = $user_data_model->initTableData($user_id, $mobilephone);
        if (!$ok) {
            $default_db->rollBack();
            YCore::exception(-1, '服务器繁忙,请稍候重试');
        }
        $default_db->commit();
        return $user_id;
    }

    /**
     * 退出登录。
     * -- 1、只有触屏版、PC版才需要调用这个方法来退出登录。
     *
     * @return boolean
     */
    public static function logout() {
        $token = isset($_COOKIE['auth_token']) ? $_COOKIE['auth_token'] : '';
        if (strlen($token) === 0) {
            return true;
        }
        try {
            $userinfo = self::parseToken($token);
        } catch (\Exception $e) {
            return true;
        }
        $user_id = $userinfo['user_id'];
        $ok = self::kick($user_id);
        if (!$ok) {
            YCore::exception(-1, '退出登录失败');
        }
        return true;
    }

    /**
     * 用户登录[用户名、手机、邮箱]。
     * -- 1、登录模式决定了通过哪一种方式管理权限token。
     * -- 2、接口模式是通过返回token进行会话。WEB模式是通过cookie管理。
     *
     * @param string $username 账号。支持用户名、手机、邮箱混登。
     * @param string $password 密码。
     * @param bool $login_entry 登录入口。1:pc、2:app、3:wap
     * @return array
     */
    public static function login($username, $password, $login_entry = 1) {
        // [1] 验证
        if (strlen($username) === 0) {
            YCore::exception(-1, '账号必须输入');
        }
        if (strlen($password) === 0) {
            YCore::exception(-1, '密码必须输入');
        }
        if (!Validator::is_alpha_dash($password)) {
            YCore::exception(-1, '密码格式有误');
        }
        if (Validator::is_mobilephone($username)) {
            $account_type = self::ACCOUNT_TYPE_PHONE;
            $where = [
                'mobilephone' => $username
            ];
        } else if (Validator::is_email($username)) {
            $account_type = self::ACCOUNT_TYPE_EMAIL;
            $where = [
                'email' => $username
            ];
        } else {
            $account_type = self::ACCOUNT_TYPE_USERNAME;
            $where = [
                'username' => $username
            ];
        }
        // [2] 检测账号是否存在。
        $user_model = new User();
        $userinfo = $user_model->fetchOne([], $where);
        if (empty($userinfo)) {
            YCore::exception(6002001, '账号与密码不匹配');
        }
        $encrypt_pwd = self::encryptPassword($password, $userinfo['salt']);
        if ($encrypt_pwd != $userinfo['password']) {
            YCore::exception(6002001, '账号与密码不匹配');
        }
        $user_black_model = new UserBlacklist();
        $forbid_info = $user_black_model->isForbidden($userinfo['user_id']);
        if ($forbid_info['status']) {
            YCore::exception(1, $forbid_info['message']);
        }
        $login_time = $_SERVER['REQUEST_TIME'];
        $login_ip = YCore::ip();
        // [3] 记录登录历史。
        $users_login_model = new UserLogin();
        $users_login_model->addLoginRecord($userinfo['user_id'], $login_time, $login_ip, $login_entry);
        // [4] 根据登录入口不同设置不同的token模式。
        $login_model = 0;
        switch ($login_entry) {
            case 1: // pc
            case 3: // wap
                $login_model = 1;
                break;
            case 2: // app
                $login_model = 2;
                break;
            default :
                YCore::exception(6002003, "Parameter login_entry is wrong");
                break;
        }
        $auth_token = self::createToken($userinfo['user_id'], $userinfo['password'], $login_time, $login_model);
        $return_data = [];
        if ($login_model == 1) { // web模式。
            $user_auth_cookie_domain_name = YUrl::getDomainName(false);
            setcookie('auth_token', $auth_token, 0, '/', $user_auth_cookie_domain_name);
        } else if ($login_model == 2) { // 接口模式。
            $return_data['token'] = $auth_token;
        } else {
            YCore::exception(6002003, "Parameter login_mode is wrong");
        }
        // [5] 设置token最后被访问的时间。通过这个可以知道用户是否超时。
        self::setAuthTokenLastAccessTime($userinfo['user_id'], $auth_token, $login_time, $login_model, $login_time);
        return $return_data;
    }

    /**
     * 获取用户列表。
     *
     * @param string $username 账号。
     * @param string $mobilephone 手机号。
     * @param unknown $is_verify_mobilephone 手机号是否验证。-1全部、1通过、0未验证。
     * @param string $starttime 开始注册时间。
     * @param string $endtime 截止注册时间。
     * @param number $page 当前页码。
     * @param number $count 每页显示条数。
     * @return array
     */
    public static function getUserList($username = '', $mobilephone = '', $is_verify_mobilephone = -1, $starttime = '', $endtime = '', $page = 1, $count = 20) {
        if (strlen($starttime) > 0 && !Validator::is_date($starttime)) {
            YCore::exception(-1, '开始注册时间格式不对');
        }
        if (strlen($endtime) > 0 && !Validator::is_date($endtime)) {
            YCore::exception(-1, '截止注册时间格式不对');
        }
        if (strlen($mobilephone) > 0 && !Validator::is_mobilephone($mobilephone)) {
            YCore::exception(-1, '手机号码格式不正确');
        }
        $from = 'FROM ms_user';
        $offset = self::getPaginationOffset($page, $count);
        $columns = ' * ';
        $where = ' WHERE 1 ';
        $params = [];
        if (strlen($username) > 0) {
            $where .= ' AND username LIKE :username ';
            $params[':username'] = "{$username}%"; // 为了性能，以及常规查询并不会查后缀。
        }
        if (strlen($mobilephone) > 0) {
            $where .= ' AND mobilephone = :mobilephone ';
            $params[':mobilephone'] = $mobilephone;
        }
        if ($is_verify_mobilephone != -1) {
            $where .= ' AND is_verify_mobilephone = :is_verify_mobilephone ';
            $params[':is_verify_mobilephone'] = $is_verify_mobilephone;
        }
        if (strlen($starttime) > 0) {
            $where .= ' AND reg_time > :starttime ';
            $params[':starttime'] = strtotime($starttime);
        }
        if (strlen($endtime) > 0) {
            $where .= ' AND reg_time < :endtime ';
            $params[':endtime'] = strtotime($endtime);
        }
        $default_db = new DbBase();
        $order_by = ' ORDER BY user_id ASC ';
        $sql = "SELECT COUNT(1) AS count {$from} {$where}";
        $count_data = $default_db->rawQuery($sql, $params)->rawFetchOne();
        $total = $count_data ? $count_data['count'] : 0;
        $sql = "SELECT {$columns} {$from} {$where} {$order_by} LIMIT {$offset},{$count}";
        $list = $default_db->rawQuery($sql, $params)->rawFetchAll();
        $users_blacklist_model = new UserBlacklist();
        foreach ($list as $k => $val) {
            // 是否封禁。
            $forbid_info = $users_blacklist_model->isForbidden($val['user_id']);
            $val['forbin_status']    = $forbid_info['status'];
            $val['forbin_label']     = $forbid_info['message'];
            $val['reg_time']         = YCore::format_timestamp($val['reg_time']);
            $val['last_login_time']  = YCore::format_timestamp($val['last_login_time']);
            $val['email_time']       = YCore::format_timestamp($val['email_time']);
            $val['mobilephone_time'] = YCore::format_timestamp($val['mobilephone_time']);
            $list[$k] = $val;
        }
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
     * 添加用户。
     *
     * @param string $username 用户名。
     * @param string $password 密码。
     * @param string $mobilephone 手机号。
     * @param string $email 邮箱地址。
     * @param string $realname 真实姓名。
     * @param string $avatar 头像。
     * @param string $signature 签名。
     * @return boolean
     */
    public static function addUser($username, $password, $mobilephone = '', $email = '', $realname = '', $avatar = '', $signature = '') {
        $users_model = new User();
        $userinfo = $users_model->fetchOne([], ['username' => $username]);
        if ($userinfo) {
            YCore::exception(-1, '该用户名已经存在请更换一个');
        }
        if (strlen($mobilephone) > 0) {
            if (!Validator::is_mobilephone($mobilephone)) {
                YCore::exception(-1, '手机号码不正确');
            }
            $userinfo = $users_model->fetchOne([], ['mobilephone' => $mobilephone]);
            if ($userinfo) {
                YCore::exception(-1, '该手机号已经存在');
            }
        }
        if (strlen($email) > 0) {
            if (!Validator::is_email($email)) {
                YCore::exception(-1, '邮箱格式不正确');
            }
            $userinfo = $users_model->fetchOne([], ['email' => $email]);
            if ($userinfo) {
                YCore::exception(-1, '邮箱已经存在');
            }
        }
        $data = [
            'username'  => $username,
            'password'  => $password,
            'realname'  => $realname,
            'signature' => $signature,
            'avatar'    => $avatar
        ];
        $rules = [
            'username'  => '账号|require:5000001|alpha_dash:5000002|len:5000003:6:20:0',
            'password'  => '密码|require:5000004|alpha_dash:5000005|len:5000006:6:20:0',
            'realname'  => '真实姓名|len:1000000:2:20:1',
            'signature' => '签名|len:1000000:1:50:1',
            'avatar'    => '头像|len:1000000:1:50:1'
        ];
        Validator::valido($data, $rules);
        $salt = YCore::create_randomstr(6);
        $password = self::encryptPassword($password, $salt);
        $data = [
            'username'    => $username,
            'salt'        => $salt,
            'password'    => $password,
            'mobilephone' => $mobilephone,
            'email'       => $email,
            'reg_time'    => $_SERVER['REQUEST_TIME']
        ];
        $default_db = new DbBase();
        $default_db->beginTransaction();
        $user_id = $users_model->insert($data);
        if ($user_id == 0) {
            $default_db->rollBack();
            YCore::exception(-1, '服务器繁忙,请稍候重试');
        }
        $user_data_model = new UserData();
        $ok = $user_data_model->initTableData($user_id, $mobilephone, $realname, $realname, $email, $avatar, $signature);
        if ($ok) {
            $default_db->commit();
            return true;
        } else {
            $default_db->rollBack();
            return false;
        }
    }

    /**
     * 编辑用户。
     *
     * @param number $user_id 用户ID。
     * @param string $username 用户名。
     * @param string $password 密码。
     * @param string $mobilephone 手机号。
     * @param string $email 邮箱地址。
     * @param string $realname 真实姓名。
     * @param string $avatar 头像。
     * @param string $signature 签名。
     * @return boolean
     */
    public static function editUser($user_id, $username, $password = '', $mobilephone = '', $email = '', $realname = '', $avatar = '', $signature = '') {
        $users_model = new User();
        $userinfo = $users_model->fetchOne([], ['user_id' => $user_id]);
        if (empty($userinfo)) {
            YCore::exception(-1, '用户不存在或已经删除');
        }
        $userinfo = $users_model->fetchOne([], ['username' => $username]);
        if ($userinfo && $userinfo['user_id'] != $user_id) {
            YCore::exception(-1, '该用户名已经存在请更换一个');
        }
        if (strlen($mobilephone) > 0) {
            if (!Validator::is_mobilephone($mobilephone)) {
                YCore::exception(-1, '手机号码不正确');
            }
            $userinfo = $users_model->fetchOne([], ['mobilephone' => $mobilephone]);
            if ($userinfo && $userinfo['user_id'] != $user_id) {
                YCore::exception(-1, '该手机号已经存在');
            }
        }
        if (strlen($email) > 0) {
            if (!Validator::is_email($email)) {
                YCore::exception(-1, '邮箱格式不正确');
            }
            $userinfo = $users_model->fetchOne([], ['email' => $email]);
            if ($userinfo && $userinfo['user_id'] != $user_id) {
                YCore::exception(-1, '邮箱已经存在');
            }
        }
        $data = [
            'username'  => $username,
            'realname'  => $realname,
            'signature' => $signature,
            'avatar'    => $avatar
        ];
        $rules = [
            'username'  => '账号|require:5000001|alpha_dash:5000002|len:5000003:6:20:0',
            'realname'  => '真实姓名|len:1000000:2:20:1',
            'signature' => '签名|len:1000000:1:50:1',
            'avatar'    => '头像|len:1000000:1:50:1'
        ];
        Validator::valido($data, $rules);
        $data = [
            'username'    => $username,
            'mobilephone' => $mobilephone,
            'email'       => $email
        ];
        if (strlen($password) > 0) {
            if (!Validator::is_alpha_dash($password)) {
                YCore::exception(-1, '密码格式不正确');
            }
            if (!Validator::is_len($password, 6, 20, true)) {
                YCore::exception(-1, '密码长度必须6-20之间');
            }
            $salt = YCore::create_randomstr(6);
            $password = self::encryptPassword($password, $salt);
            $data['salt']     = $salt;
            $data['password'] = $password;
        }
        $base_model = new DbBase();
        $base_model->beginTransaction();
        $ok = $users_model->update($data, ['user_id' => $user_id]);
        if (!$ok) {
            $base_model->rollBack();
            YCore::exception(-1, '服务器繁忙,请稍候重试');
        }
        $user_data_model = new UserData();
        $ok = $user_data_model->editInfo($user_id, $mobilephone, $realname, $realname, $email, $avatar, $signature);
        if ($ok) {
            $base_model->commit();
            return true;
        } else {
            $base_model->rollBack();
            return false;
        }
    }

    /**
     * 解禁用户。
     *
     * @param number $user_id 用户ID。
     * @param number $admin_id 管理员ID。
     * @return bool
     */
    public static function unforbid($user_id, $admin_id) {
        $users_blacklist_model = new UserBlacklist();
        return $users_blacklist_model->unforbiddenUser($user_id, $admin_id);
    }

    /**
     * 绑定第三方用户。
     *
     * @param number $user_id 用户ID。
     * @param string $openid 第三方用户标识。
     * @param string $third_type 第三方类型：qq、weibo、weixin。
     * @return boolean
     */
    public static function thirdUserBind($user_id, $openid, $third_type) {
        if (strlen($openid) === 0) {
            YCore::exception(-1, 'openid error');
        }
        $where = [
            'user_id'   => $user_id,
            'bind_type' => $third_type,
            'status'    => 1
        ];
        $user_bind_model = new UserBind();
        $user_bind_info = $user_bind_model->fetchOne([], $where);
        if (!empty($user_bind_info)) {
            YCore::exception(-1, '请不要重复绑定');
        }
        $data = [
            'user_id'      => $user_id,
            'bind_type'    => $third_type,
            'openid'       => $openid,
            'created_time' => $_SERVER['REQUEST_TIME'],
            'status'       => 1
        ];
        $id = $user_bind_model->insert($data);
        if (!$id) {
            YCore::exception(-1, '绑定失败');
        }
        return true;
    }

    /**
     * 账号封禁。
     *
     * @param number $admin_id 管理员ID。
     * @param number $user_id 用户ID。
     * @param number $ban_type 封禁类型。1永封禁、2临时封禁。
     * @param string $ban_start_time 封禁开始时间。
     * @param string $ban_end_time 封禁失效时间。
     * @param string $ban_reason 封禁原因。
     * @return boolean
     */
    public static function addBlacklist($admin_id, $user_id, $ban_type, $ban_start_time = '', $ban_end_time = '', $ban_reason = '') {
        $timestamp = $_SERVER['REQUEST_TIME'];
        $users_blacklist = new UserBlacklist();
        $blacklist = $users_blacklist->fetchOne([], ['status' => 1, 'user_id' => $user_id]);
        if ($blacklist) {
            if ($blacklist['ban_type'] == 1) {
                YCore::exception(-1, '该用户已经被永久封禁');
            } else {
                if ($blacklist['ban_end_time'] < $timestamp) { // 如果临时封禁情况下且已经失效。则将封禁设置为无效。
                    $data = [
                        'status'        => 0,
                        'modified_by'   => $admin_id,
                        'modified_time' => $timestamp
                    ];
                    $users_blacklist->update($data, ['id' => $blacklist['id']]);
                } else {
                    YCore::exception(-1, '该用户已经被临时封禁还未到期');
                }
            }
        }
        $users_model = new User();
        $userinfo = $users_model->fetchOne([], ['user_id' => $user_id]);
        if (empty($userinfo)) {
            YCore::exception(-1, '用户不存在或已经删除');
        }
        if ($ban_type == 2) {
            if (strlen($ban_start_time) === 0) {
                YCore::exception(-1, '临时封禁时封禁开始时间必须填写');
            }
            if (strlen($ban_end_time) === 0) {
                YCore::exception(-1, '临时封禁时封禁失效时间必须填写');
            }
            if (!Validator::is_date($ban_start_time)) {
                YCore::exception(-1, '封禁开始时间格式不正确');
            }
        } else if ($ban_type == 1) {
            $end_time       = $timestamp + 500 * 86400 * 365; // 封禁500年代表永久封禁的意思。
            $ban_start_time = date('Y-m-d H:i:s', $timestamp);
            $ban_end_time   = date('Y-m-d H:i:s', $end_time);
        }
        if (strlen($ban_reason) > 0 && !Validator::is_len($ban_reason, 1, 200, true)) {
            YCore::exception(-1, '封禁原因长度最大只允许200个字符');
        }
        return $users_blacklist->forbiddenUser($admin_id, $user_id, $userinfo['username'], $ban_type, $ban_start_time, $ban_end_time, $ban_reason);
    }

    /**
     * 用户详情信息。
     *
     * @param int $user_id 用户ID。
     * @return array
     */
    public static function getUserDetail($user_id) {
        $base_model = new DbBase();
        $sql = 'SELECT a.user_id,a.username,a.mobilephone,a.mobilephone_ok,a.mobilephone_time,' . 'a.email,a.email_ok,a.email_time,a.reg_time,b.realname,b.avatar,b.signature ' . 'FROM ms_user AS a LEFT JOIN ms_user_data AS b ON(a.user_id=b.user_id) ' . 'WHERE a.user_id = :user_id';
        $params = [
            ':user_id' => $user_id
        ];
        $userinfo = $base_model->rawQuery($sql, $params)->rawFetchOne();
        if (empty($userinfo)) {
            YCore::exception(-1, '用户不存在或已经删除');
        }
        return $userinfo;
    }

    /**
     * 发送找回密码验证码。
     *
     * @param number $find_type 找回密码类型：1手机号找回、2邮箱找回。
     * @param string $to_account 接收验证码的手机或邮箱账号。
     * @return boolean
     */
    public static function sendFindPwdCode($find_type, $to_account) {
        // [1] 格式验证。
        switch ($find_type) {
            case 1:
                if (!Validator::is_mobilephone($to_account)) {
                    YCore::exception(-1, '手机号格式不正确');
                }
                break;
            case 2:
                if (!Validator::is_email($to_account)) {
                    YCore::exception(-1, '邮箱格式不正确');
                }
                break;
            default :
                YCore::exception(-1, '服务器异常');
                break;
        }
        // [2] 每天每账号的不同类型找回方式只能3次。
        $date = date('Y-m-d', $_SERVER['REQUEST_TIME']);
        $day_start_timestamp = strtotime("{$date} 00:00:00");
        $default_db = new DbBase();
        $sql = 'SELECT COUNT(1) AS count FROM ms_find_pwd WHERE find_type = :find_type '
             . 'AND to_account = :to_account AND created_time > :created_time';
        $params = [
            ':find_type'    => $find_type,
            ':to_account'   => $to_account,
            ':created_time' => $day_start_timestamp
        ];
        $result = $default_db->rawQuery($sql, $params)->rawFetchOne();
        if (!empty($result) && $result['count'] >= 3) {
            YCore::exception(-1, '已经超过3次请明天再试');
        }
        switch ($find_type) {
            case 1:
                $where = [
                    'mobilephone' => $to_account
                ];
                break;
            case 2:
                $where = [
                    'email' => $to_account
                ];
                break;
        }
        $user_model = new User();
        $userinfo = $user_model->fetchOne([], $where);
        if (empty($userinfo)) {
            YCore::exception(-1, '账号不存在');
        }
        $code = YCore::create_randomstr(6);
        // [3] 发送验证码。
        // ......
        switch ($find_type) {
            case 1:
                SmsService::sendSmsCode(SmsService::SMS_TYPE_FINDPWD, $to_account);
                break;
            case 2:
                // 发送邮件。
                break;
        }
        // [4] 记录发送的验证码。
        $data = [
            'user_id'      => $userinfo['user_id'],
            'find_type'    => $find_type,
            'to_account'   => $to_account,
            'code'         => $code,
            'ip'           => YCore::ip(),
            'created_time' => $_SERVER['REQUEST_TIME']
        ];
        $find_pwd_model = new FindPwd();
        $id = $find_pwd_model->insert($data);
        if ($id == 0) {
            YCore::exception(-1, '服务器繁忙,请稍候重试');
        }
        return true;
    }

    /**
     * 找回密码。
     *
     * @param number $find_type 找回密码类型：1手机号码找回、2邮箱找回。
     * @param string $to_account 手机或邮箱账号。
     * @param string $code 验证码。
     * @param string $new_pwd 新密码。
     * @return array
     */
    public static function findPwd($find_type, $to_account, $code, $new_pwd) {
        // [1] 格式验证。
        switch ($find_type) {
            case 1:
                if (!Validator::is_mobilephone($to_account)) {
                    YCore::exception(-1, '手机号格式不正确');
                }
                break;
            case 2:
                if (!Validator::is_email($to_account)) {
                    YCore::exception(-1, '邮箱格式不正确');
                }
                break;
            default :
                YCore::exception(-1, '服务器异常');
                break;
        }
        if (strlen($code) === 0) {
            YCore::exception(-1, '验证必须填写');
        }
        if (strlen($new_pwd) === 0) {
            YCore::exception(-1, '新密码必须填写');
        }
        if (!Validator::is_alpha_dash($new_pwd)) {
            YCore::exception(-1, '新密码格式不正确');
        }
        if (!Validator::is_len($new_pwd, 6, 20, true)) {
            YCore::exception(-1, '密码长度6~20个字符');
        }
        $default_db = new DbBase();
        $sql = 'SELECT * FROM ms_find_pwd WHERE find_type = :find_type ' . 'AND to_account = :to_account AND check_times < :check_times AND is_ok != :is_ok';
        $params = [
            ':find_type'   => $find_type,
            ':to_account'  => $to_account,
            ':check_times' => 3,
            ':is_ok'       => 1
        ];
        $result = $default_db->rawQuery($sql, $params)->rawFetchOne();
        if (empty($result)) {
            YCore::exception(-1, '找回密码操作已过期');
        }
        $where = [
            'id'          => $result['id'],
            'check_times' => $result['check_times']
        ];
        $find_pwd_model = new FindPwd();
        if ($result['code'] == $code) {
            $data = [
                'check_times'   => $result['check_times'] + 1,
                'is_ok'         => 1,
                'modified_time' => $_SERVER['REQUEST_TIME']
            ];
            $ok = $find_pwd_model->update($data, $where);
            if (!$ok) {
                YCore::exception(-1, '服务器繁忙,请重试');
            }
            $salt = YCore::create_randomstr(6);
            $password = self::encryptPassword($new_pwd, $salt);
            $data = [
                'password' => $password,
                'salt'     => $salt
            ];
            $where = [
                'user_id' => $result['user_id']
            ];
            $user_model = new User();
            $ok = $user_model->update($data, $where);
            if (!$ok) {
                YCore::exception(-1, '服务器繁忙,请稍候重试');
            }
            return true;
        } else {
            $data = [
                'check_times'   => $result['check_times'] + 1,
                'is_ok'         => 2,
                'modified_time' => $_SERVER['REQUEST_TIME']
            ];
            $ok = $find_pwd_model->update($data, $where);
            if (!$ok) {
                YCore::exception(-1, '服务器繁忙,请重试');
            } else {
                YCore::exception(-1, '验证码不正确');
            }
        }
    }

    /**
     * 修改密码。
     *
     * @param number $user_id 用户ID。
     * @param string $old_pwd 旧密码。
     * @param string $new_pwd 新密码。
     * @return boolean
     */
    public static function editPwd($user_id, $old_pwd, $new_pwd) {
        if (strlen($old_pwd) === 0) {
            YCore::exception(-1, '原密码必须填写');
        }
        if (strlen($new_pwd) === 0) {
            YCore::exception(-1, '新密码必须填写');
        }
        if ($old_pwd == $new_pwd) {
            YCore::exception(-1, '新密码不能与原密码相同');
        }
        if (!Validator::is_alpha_dash($new_pwd)) {
            YCore::exception(-1, '新密码格式不正确');
        }
        if (!Validator::is_len($new_pwd, 6, 20)) {
            YCore::exception(-1, '新密码长度必须6~20位之间');
        }
        $user_model = new User();
        $userinfo = $user_model->fetchOne([], [
            'user_id' => $user_id
        ]);
        $encrypt_password = self::encryptPassword($old_pwd, $userinfo['salt']);
        if ($encrypt_password != $userinfo['password']) {
            YCore::exception(-1, '原密码不正确');
        }
        $salt = YCore::create_randomstr(6);
        $password = self::encryptPassword($new_pwd, $salt);
        $updata = [
            'salt'     => $salt,
            'password' => $password
        ];
        $ok = $user_model->update($updata, ['user_id' => $user_id]);
        if (!$ok) {
            YCore::exception(-1, '密码修改失败');
        }
        return true;
    }

    /**
     * 检查用户权限。
     * -- 1、在每次用户访问程序的时候调用。
     *
     * @param int $login_mode 登录模式。web模式、api模式。
     * @param string $token Token。如果是接口模式。必须设置此值。
     * @return int 返回用户ID。
     */
    public static function checkAuth($login_mode, $token = '') {
        // [1] 参数判断。
        if ($login_mode == self::LOGIN_MODE_API && strlen($token) === 0) {
            YCore::exception(6004001, 'Token parameters must not be empty');
        }
        if ($login_mode == self::LOGIN_MODE_WEB) {
            $token = isset($_COOKIE['auth_token']) ? $_COOKIE['auth_token'] : '';
            if (strlen($token) === 0) {
                YCore::exception(6004002, '您还没有登录');
            }
        }
        // [2] token解析
        $token_params = self::parseToken($token);
        $user_id      = $token_params['user_id'];
        $login_time   = $token_params['login_time'];
        $login_mode   = $token_params['mode'];
        $access_time  = $_SERVER['REQUEST_TIME'];
        // [3] 用户存在与否判断
        $user_model = new User();
        $userinfo = $user_model->fetchOne([], ['user_id' => $user_id]);
        if (empty($userinfo)) {
            YCore::exception(6004004, '系统异常');
        }
        if ($token_params['password'] != $userinfo['password']) {
            YCore::exception(6004005, '您的密码被修改,请重新登录');
        }
        // [4] 黑名单判断
        $users_blacklist_model = new UserBlacklist();
        $result = $users_blacklist_model->isForbidden($user_id);
        if ($result['status'] == 1) {
            YCore::exception(6004006, $result['message']);
        }
        // [5] token是否赶出了超时时限
        $cache = YCore::getCache();
        $is_unique_login = YCore::config('is_unique_login');
        if ($is_unique_login == 1) { // 排它性登录。
            $cache_key_token = "user_token_key_{$user_id}";
            $cache_key_time  = "user_access_time_key_{$user_id}";
            $cache_token = $cache->get($cache_key_token);
            if ($cache_token === false) {
                YCore::exception(6004007, '系统繁忙,稍候重试');
            }
            if ($cache_token === null) {
                YCore::exception(6004008, '登录超时,请重新登录');
            }
            if ($token != $cache_token) {
                YCore::exception(6004009, '您的账号在其它地方登录');
            }
        } else if ($login_mode == 2) { // 非排它性登录。
            $cache_key_token = "user_token_key_{$login_time}_{$user_id}";
            $cache_key_time  = "user_access_time_key_{$login_time}_{$user_id}";
            $cache_token     = $cache->get($cache_key_token);
            if ($cache_token === false) {
                YCore::exception(6004010, '系统繁忙,请稍候重试');
            }
            if ($cache_token === null) {
                YCore::exception(6004011, '登录超时,请重新登录');
            }
        } else {
            YCore::exception(6004007, '非法操作');
        }
        self::setAuthTokenLastAccessTime($user_id, $token, $access_time, $login_mode, $login_time);
        return [
            'user_id'     => $user_id,
            'username'    => $userinfo['username'],
            'mobilephone' => $userinfo['mobilephone'],
            'user_type'   => $userinfo['user_type']
        ];
    }

    /**
     * 将用户踢下线（退出登录）。
     *
     * @param int $user_id
     * @return boolean
     */
    public static function kick($user_id) {
        $cache = YCore::getCache();
        $is_unique_login = YCore::config('is_unique_login');
        if ($is_unique_login == 1) {
            $cache_key_token = "user_token_key_{$user_id}";
            $cache_key_time  = "user_access_time_key_{$user_id}";
            $cache->del($cache_key_token);
            $cache->del($cache_key_time);
        } else if ($is_unique_login == 2) { // 非排他性登录的情况下，可能出现一个账号多次登录的情况。
            $users_login_model = new UserLogin();
            $pc_logout_time = YCore::config('pc_logout_time') * 60 + 60; // 多加60，是避免边界值误差。
            $start_time = $_SERVER['REQUEST_TIME'] - $pc_logout_time;
            $end_time = $_SERVER['REQUEST_TIME'];
            $login_record_list = $users_login_model->getUserLoginRecord($user_id, $start_time, $end_time);
            foreach ($login_record_list as $record) {
                $login_time = $record['login_time'];
                $cache_key_token = "user_token_key_{$login_time}_{$user_id}";
                $cache_key_time  = "user_access_time_key_{$login_time}_{$user_id}";
                $cache->del($cache_key_token);
                $cache->del($cache_key_time);
            }
        }
        return true;
    }

    /**
     * 设置auth_token最后的访问时间。
     *
     * @param int $user_id 用户ID。
     * @param string $auth_token auth_token。
     * @param int $access_time 最后访问时间戳。
     * @param int $login_mode 登录模式。1:web模式、2:接口模式。
     * @param int $login_time 登录时间。
     * @return void
     */
    private static function setAuthTokenLastAccessTime($user_id, $auth_token, $access_time, $login_mode, $login_time) {
        $cache = YCore::getCache();
        // [1] 不同的登录模式。缓存的时间各不相同。
        if ($login_mode == 1) {
            $cache_time = YCore::config('pc_logout_time') * 60;
        } else if ($login_mode == 2) {
            $cache_time = YCore::config('app_logout_time') * 86400;
        }
        // [2] 排它性登录实现的原理各有差异。
        $is_unique_login = YCore::config('is_unique_login'); // 是否排它性登录。
                                                             // 排它性是指同一账号只允许同一时间只能允许一个人登录在线。
        if ($is_unique_login == 1) {
            $cache_key_token = "user_token_key_{$user_id}";         // 用户保存auth_token的缓存键。
            $cache_key_time  = "user_access_time_key_{$user_id}";   // 用户保存最后访问时间的缓存键。
            $cache->set($cache_key_token, $auth_token, $cache_time);
            $cache->set($cache_key_time, $access_time, $cache_time);
        } else {
            $cache_key_token = "user_token_key_{$login_time}_{$user_id}";       // 用户保存auth_token的缓存键。
            $cache_key_time  = "user_access_time_key_{$login_time}_{$user_id}"; // 用户保存最后访问时间的缓存键。
            $cache->set($cache_key_token, $auth_token, $cache_time);
            $cache->set($cache_key_time, $access_time, $cache_time);
        }
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
     * @param int $user_id 用户ID。
     * @param string $password 用户表password字段。
     * @param int $login_time 登录时间(时间戳)。
     * @param boolean $login_model 登录模式。1web模式、2接口模式。
     * @return string
     */
    private static function createToken($user_id, $password, $login_time, $login_model = 1) {
        $str = "{$user_id}\t{$password}\t{$login_time}\t{$login_model}";
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
        if (count($data) != 4) {
            YCore::exception(6004003, '登录超时,请重新登录');
        }
        $result = [
            'user_id'    => $data[0], // 用户ID。
            'password'   => $data[1], // 加密的ID。
            'login_time' => $data[2], // 登录时间。
            'mode'       => $data[3]
        ]; // token模式。1接口模式、0非接口模式。
        return $result;
    }
}