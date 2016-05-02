<?php
/**
 * 用户业务封装。
 * --1、用户业务模块错误码段位：6001xxx
 * @author winerQin
 * @date 2015-10-30
 */
namespace services;

use winer\Validator;
use models\UsersData;
use common\YCore;
use models\UsersLogin;
use models\Users;
use models\UsersBlacklist;
use models\DbBase;

class UserService extends BaseService {

	/**
	 * 用户注册。
	 * @param string $username 账号。
	 * @param string $password 密码。
	 * @return array
	 */
	public static function register($username, $password) {
		// [1] 验证
		$data = [
				'username' => $username,
				'password' => $password,
		];
		$rules = [
				'username' => '账号|require:5000001|alpha_dash:5000002|len:5000003:6:20:0',
				'password' => '密码|require:5000004|alpha_dash:5000005|len:5000006:6:20:0',
		];
		Validator::valido($data, $rules); // 验证不通过会抛异常。
		// [2] 是否已经注册
		$users_model = new \models\Users();
		$userinfo = $users_model->getUserOfByUsername($username);
		if (!empty($userinfo)) {
			YCore::throw_exception(6001001, '该账号已经被人注册了');
		}
		$salt = YCore::create_randomstr(6);
		$password = self::encryptPassword($password, $salt);
		$user_id = $users_model->addUser($username, $password, $salt);
		if ($user_id == 0) {
			YCore::throw_exception(6001002, '注册失败,稍候重试');
		}
		$users_data_model = new UsersData();
		$id = $users_data_model->initUserData($user_id);
		if ($id == 0) {
			YCore::throw_exception(6001003, '注册失败,稍候重试');
		}
		$return_data = [
				'user_id'  => $user_id,
				'username' => $username
		];
		return $return_data;
	}

	/**
	 * 用户登录。
	 * -- 1、登录模式决定了通过哪一种方式管理权限token。
	 * -- 2、接口模式是通过返回token进行会话。WEB模式是通过cookie管理。
	 * @param string $username 账号。
	 * @param string $password 密码。
	 * @param bool $login_entry 登录入口。1:pc、2:app、3:wap
	 * @return array
	 */
	public static function login($username, $password, $login_entry = 1) {
		// [1] 验证
		$data = [
				'username' => $username,
				'password' => $password,
		];
		$rules = [
				'username' => '账号|require:5000001|alpha_dash:5000002|len:5000003:6:20:0',
				'password' => '密码|require:5000004|alpha_dash:5000005|len:5000006:6:20:0',
		];
		Validator::valido($data, $rules); // 验证不通过会抛异常。
		// [2] 是否已经注册
		$users_model = new \models\Users();
		$userinfo = $users_model->getUserOfByUsername($username);
		if (empty($userinfo)) {
			YCore::throw_exception(6002001, '账号不存在,请重新输入');
		}
		$encrypt_pwd = self::encryptPassword($password, $userinfo['salt']);
		if ($encrypt_pwd != $userinfo['password']) {
			YCore::throw_exception(6002002, '账号与密码不匹配,请重新输入');
		}
		$login_time = $_SERVER['REQUEST_TIME'];
		$login_ip = YCore::ip();
		// [3] 记录登录历史。
		$users_login_model = new UsersLogin();
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
			default:
				YCore::throw_exception(6002003, "Parameter login_entry is wrong");
				break;
		}
		$auth_token = self::createToken($userinfo['user_id'], $userinfo['password'], $login_time, $login_model);
		$return_data = [];
		if ($login_model == 1) { // web模式。
			$user_auth_cookie_domain_name = YCore::sys_config('user_auth_cookie_domain_name');
			setcookie('token', $auth_token, 0, '/', $user_auth_cookie_domain_name);
		} else if ($login_model == 2) { // 接口模式。
			$return_data['token'] = $auth_token;
		} else {
			YCore::throw_exception(6002003, "Parameter login_mode is wrong");
		}
		// [5] 设置token最后被访问的时间。通过这个可以知道用户是否超时。
		self::setAuthTokenLastAccessTime($userinfo['user_id'], $auth_token, $login_time, $login_model, $login_time);
		return $return_data;
	}

	/**
	 * 获取用户列表。
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
	        YCore::throw_exception(-1, '开始注册时间格式不对');
	    }
	    if (strlen($endtime) > 0 && !Validator::is_date($endtime)) {
	        YCore::throw_exception(-1, '截止注册时间格式不对');
	    }
	    if (strlen($mobilephone) > 0 && !Validator::is_mobilephone($mobilephone)) {
	        YCore::throw_exception(-1, '手机号码格式不正确');
	    }
	    $users_model = new Users();
	    return $users_model->getList($username, $mobilephone, $is_verify_mobilephone, $starttime, $endtime, $page, $count);
	}

	/**
	 * 添加用户。
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
	    $users_model = new Users();
	    $userinfo = $users_model->fetchOne([], ['username' => $username]);
	    if ($userinfo) {
	        YCore::throw_exception(-1, '该用户名已经存在请更换一个');
	    }
	    if (strlen($mobilephone) > 0) {
	        if (!Validator::is_mobilephone($mobilephone)) {
	            YCore::throw_exception(-1, '手机号码不正确');
	        }
	        $userinfo = $users_model->fetchOne([], ['mobilephone' => $mobilephone]);
	        if ($userinfo) {
	            YCore::throw_exception(-1, '该手机号已经存在');
	        }
	    }
	    if (strlen($email) > 0) {
	        if (!Validator::is_email($email)) {
	            YCore::throw_exception(-1, '邮箱格式不正确');
	        }
	        $userinfo = $users_model->fetchOne([], ['email' => $email]);
	        if ($userinfo) {
	            YCore::throw_exception(-1, '邮箱已经存在');
	        }
	    }
	    $data = [
	        'username'  => $username,
	        'password'  => $password,
	        'realname'  => $$realname,
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
	    $base_model = new DbBase();
	    $base_model->beginTransaction();
	    $user_id = $users_model->insert($data);
	    if ($user_id > 0) {
	        $data = [
	            'user_id'   => $user_id,
	            'realname'  => $realname,
	            'avatar'    => $avatar,
	            'signature' => $signature
	        ];
	        $users_data_model = new UsersData();
	        $ok = $users_data_model->insert($data);
	        if ($ok) {
	            $base_model->commit();
	            return true;
	        } else {
	            $base_model->rollBack();
	            return false;
	        }
	    } else {
	        $base_model->rollBack();
	        return false;
	    }
	}

	/**
	 * 编辑用户。
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
	    $users_model = new Users();
	    $userinfo = $users_model->fetchOne([], ['user_id' => $user_id]);
	    if (empty($userinfo)) {
	        YCore::throw_exception(-1, '用户不存在或已经删除');
	    }
	    $userinfo = $users_model->fetchOne([], ['username' => $username]);
	    if ($userinfo && $userinfo['user_id'] != $user_id) {
	        YCore::throw_exception(-1, '该用户名已经存在请更换一个');
	    }
	    if (strlen($mobilephone) > 0) {
	        if (!Validator::is_mobilephone($mobilephone)) {
	            YCore::throw_exception(-1, '手机号码不正确');
	        }
	        $userinfo = $users_model->fetchOne([], ['mobilephone' => $mobilephone]);
	        if ($userinfo && $userinfo['user_id'] != $user_id) {
	            YCore::throw_exception(-1, '该手机号已经存在');
	        }
	    }
	    if (strlen($email) > 0) {
	        if (!Validator::is_email($email)) {
	            YCore::throw_exception(-1, '邮箱格式不正确');
	        }
	        $userinfo = $users_model->fetchOne([], ['email' => $email]);
	        if ($userinfo && $userinfo['user_id'] != $user_id) {
	            YCore::throw_exception(-1, '邮箱已经存在');
	        }
	    }
	    $data = [
	        'username'  => $username,
	        'realname'  => $$realname,
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
	        'email'       => $email,
	    ];
	    if (strlen($password) > 0) {
	        if (!Validator::is_alpha_dash($password)) {
	            YCore::throw_exception(-1, '密码格式不正确');
	        }
	        if (!Validator::is_len($password, 6, 20, true)) {
	            YCore::throw_exception(-1, '密码长度必须6-20之间');
	        }
	        $salt = YCore::create_randomstr(6);
	        $password = self::encryptPassword($password, $salt);
	        $data['salt']     = $salt;
	        $data['password'] = $password;
	    }
	    $base_model = new DbBase();
	    $base_model->beginTransaction();
	    $user_id = $users_model->update($data, ['user_id' => $user_id]);
	    if ($user_id > 0) {
	        $data = [
	            'user_id'   => $user_id,
	            'realname'  => $realname,
	            'avatar'    => $avatar,
	            'signature' => $signature
	        ];
	        $users_data_model = new UsersData();
	        $ok = $users_data_model->update($data, ['user_id' => $user_id]);
	        if ($ok) {
	            $base_model->commit();
	            return true;
	        } else {
	            $base_model->rollBack();
	            return false;
	        }
	    } else {
	        $base_model->rollBack();
	        return false;
	    }
	}
	
	/**
	 * 账号封禁。
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
	    $users_blacklist = new UsersBlacklist();
	    $blacklist = $users_blacklist->fetchOne([], ['status' => 1, 'user_id' => $user_id]);
	    if ($blacklist) {
	        if ($blacklist['ban_type'] == 1) {
	            YCore::throw_exception(-1, '该用户已经被永久封禁');
	        } else {
	            if ($blacklist['ban_end_time'] < $timestamp) { // 如果临时封禁情况下且已经失效。则将封禁设置为无效。
	                $data = [
	                    'status'        => 0,
	                    'modified_by'   => $admin_id,
	                    'modified_time' => $timestamp
	                ];
	                $users_blacklist->update($data, ['id' => $blacklist['id']]);
	            } else {
	                YCore::throw_exception(-1, '该用户已经被临时封禁还未到期');
	            }
	        }
	    }
	    $users_model = new Users();
	    $userinfo = $users_model->fetchOne([], ['user_id' => $user_id]);
	    if (empty($userinfo)) {
	        YCore::throw_exception(-1, '用户不存在或已经删除');
	    }
	    if ($ban_type == 2) {
	        if (strlen($ban_start_time) === 0) {
	            YCore::throw_exception(-1, '临时封禁时封禁开始时间必须填写');
	        }
	        if (strlen($ban_end_time) === 0) {
	            YCore::throw_exception(-1, '临时封禁时封禁失效时间必须填写');
	        }
	        if (!Validator::is_date($ban_start_time)) {
	            YCore::throw_exception(-1, '封禁开始时间格式不正确');
	        }
	    } else if ($ban_type == 1) {
	        $end_time = $timestamp + 500 * 86400 * 365; // 封禁500年代表永久封禁的意思。
	        $ban_start_time = date('Y-m-d H:i:s', $timestamp);
	        $ban_end_time   = date('Y-m-d H:i:s', $end_time);
	    }
	    if (strlen($ban_reason) > 0 && !Validator::is_len($ban_reason, 1, 200, true)) {
	        YCore::throw_exception(-1, '封禁原因长度最大只允许200个字符');
	    }
	    return $users_blacklist->forbiddenUser($admin_id, $user_id, $userinfo['username'], $ban_type, $ban_start_time, $ban_end_time, $ban_reason);
	}

	/**
	 * 用户详情信息。
	 * @param int $user_id 用户ID。
	 * @return array
	 */
	public static function getUserDetail($user_id) {
		$base_model = new DbBase();
		$sql = 'SELECT a.user_id,a.username,a.mobilephone,a.is_verify_mobilephone,a.verify_mobilephone_time,'
		     . 'a.email,a.is_verify_email,a.verify_email_time,a.reg_time,b.realname,b.avatar,b.signature '
		     . 'FROM ms_users AS a LEFT JOIN ms_users_data AS b ON(a.user_id=b.user_id) '
		     . 'WHERE a.user_id = :user_id';
		$params = [
		    ':user_id' => $user_id
		];
		$userinfo = $base_model->rawQuery($sql, $params)->rawFetchOne();
		if (empty($userinfo)) {
		    YCore::throw_exception(-1, '用户不存在或已经删除');
		}
		return $userinfo;
	}

	/**
	 * 发送找回密码的验证码。
	 */
	public static function sendFindPwdCode($username, $type) {
		
	}

	/**
	 * 找回密码。
	 * @param string $username 账号。
	 * @param string $code 验证码。
	 * @return array
	 */
	public static function findPwd($username, $code) {
		
	}

	/**
	 * 检查用户权限。
	 * -- 1、在每次用户访问程序的时候调用。
	 * @param int $login_mode 登录模式。1web模式、2接口模式。
	 * @param string $token Token。如果是接口模式。必须设置此值。
	 * @return int 返回用户ID。
	 */
	public static function checkAuth($login_mode, $token = '') {
		// [1] 参数判断。
		if ($login_mode == 2 && strlen($token) === 0) {
			YCore::throw_exception(6004001, 'Token parameters must not be empty');
		}
		if ($login_mode == 1) {
			$token = $_COOKIE['token'];
			if (strlen($token) === 0) {
				YCore::throw_exception(6004002, '您还没有登录');
			}
		}
		// [2] token解析
		$token_params = self::parseToken($token);
		$user_id      = $token_params['user_id'];
		$login_time   = $token_params['login_time'];
		$login_mode   = $token_params['mode'];
		$access_time  = $_SERVER['REQUEST_TIME'];
		// [3] 用户存在与否判断
		$user_model   = new Users();
		$userinfo     = $user_model->getUserOfByUserId($user_id);
		if (empty($userinfo)) {
			YCore::throw_exception(6004004, '非法用户请求');
		}
		if ($token['password'] != $userinfo['password']) {
			YCore::throw_exception(6004005, '您的密码被修改,请重新登录');
		}
		// [4] 黑名单判断
		$users_blacklist_model = new UsersBlacklist();
		$result = $users_blacklist_model->isForbidden($user_id);
		if ($result['status'] == 1) {
			YCore::throw_exception(6004006, $result['message']);
		}
		// [5] token是否赶出了超时时限
		$ssdb = \Yaf\Registry::get('ssdb');
		$is_unique_login = YCore::sys_config('is_unique_login');
		if ($is_unique_login == 1) { // 排它性登录。
			$cache_key_token = "user_token_key_{$user_id}";	
			$cache_key_time  = "user_access_time_key_{$user_id}";
			$cache_token     = $ssdb->get($cache_key_token);
			if ($cache_token === false) {
				YCore::throw_exception(6004007, '系统繁忙,稍候重试');
			}
			if ($cache_token === null) {
				YCore::throw_exception(6004008, '登录超时,请重新登录');
			}
			if ($token != $cache_token) {
				YCore::throw_exception(6004009, '您的账号在其它地方登录');
			}
		} else if ($login_mode == 2) { // 非排它性登录。
			$cache_key_token = "user_token_key_{$login_time}_{$user_id}";
			$cache_key_time  = "user_access_time_key_{$login_time}_{$user_id}";
			$cache_token     = $ssdb->get($cache_key_token);
			if ($cache_token === false) {
				YCore::throw_exception(6004010, '系统繁忙,请稍候重试');
			}
			if ($cache_token === null) {
				YCore::throw_exception(6004011, '登录超时,请重新登录');
			}
		} else {
			YCore::throw_exception(6004007, '非法操作');
		}
		self::setAuthTokenLastAccessTime($user_id, $token, $access_time, $login_mode, $login_time);
		return $user_id;
	}

	/**
	 * 将用户踢下线（退出登录）。
	 * @param int $user_id
	 * @return boolean
	 */
	public static function kick($user_id) {
		$ssdb = \Yaf\Registry::get('ssdb');
		$is_unique_login = YCore::sys_config('is_unique_login');
		if ($is_unique_login == 1) {
			$cache_key_token = "user_token_key_{$user_id}";
			$cache_key_time  = "user_access_time_key_{$user_id}";
			$ssdb->del($cache_key_token);
			$ssdb->del($cache_key_time);
		} else if ($is_unique_login == 2) { // 非排他性登录的情况下，可能出现一个账号多次登录的情况。
			$users_login_model = new UsersLogin();
			$pc_logout_time = YCore::sys_config('pc_logout_time') * 60 + 60; // 多加60，是避免边界值误差。
			$start_time = $_SERVER['REQUEST_TIME'] - $pc_logout_time;
			$end_time   = $_SERVER['REQUEST_TIME'];
			$login_record_list = $users_login_model->getUserLoginRecord($user_id, $start_time, $end_time);
			foreach ($login_record_list as $record) {
				$login_time = $record['login_time'];
				$cache_key_token = "user_token_key_{$login_time}_{$user_id}";
				$cache_key_time  = "user_access_time_key_{$login_time}_{$user_id}";
				$ssdb->del($cache_key_token);
				$ssdb->del($cache_key_time);
			}
		}
		return false;
	}

	/**
	 * 设置auth_token最后的访问时间。
	 * @param int $user_id 用户ID。
	 * @param string $auth_token auth_token。
	 * @param int $access_time 最后访问时间戳。
	 * @param int $login_mode 登录模式。1:web模式、2:接口模式。
	 * @param int $login_time 登录时间。
	 * @return void
	 */
	private static function setAuthTokenLastAccessTime($user_id, $auth_token, $access_time, $login_mode, $login_time) {
		$ssdb = \Yaf\Registry::get('ssdb');
		// [1] 不同的登录模式。缓存的时间各不相同。
		if ($login_mode == 1) {
			$cache_time = YCore::sys_config('pc_logout_time') * 60;
		} else if ($login_mode == 2) {
			$cache_time = YCore::sys_config('app_logout_time') * 86400;
		}
		// [2] 排它性登录实现的原理各有差异。
		$is_unique_login = YCore::sys_config('is_unique_login'); 				// 是否排它性登录。
		// 排它性是指同一账号只允许同一时间只能允许一个人登录在线。
		if ($is_unique_login == 1) {
			$cache_key_token = "user_token_key_{$user_id}";						// 用户保存auth_token的缓存键。
			$cache_key_time  = "user_access_time_key_{$user_id}";				// 用户保存最后访问时间的缓存键。
			$ssdb->setx($cache_key_token, $auth_token, $cache_time);
			$ssdb->setx($cache_key_time, $access_time, $cache_time);
		} else {
			$cache_key_token = "user_token_key_{$login_time}_{$user_id}";		// 用户保存auth_token的缓存键。
			$cache_key_time  = "user_access_time_key_{$login_time}_{$user_id}";	// 用户保存最后访问时间的缓存键。
			$ssdb->setx($cache_key_token, $auth_token, $cache_time);
			$ssdb->setx($cache_key_time, $access_time, $cache_time);
		}
	}
	
	/**
	 * 加密密码。
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
	 * @param string $token token会话。
	 * @return array
	 */
	private static function parseToken($token) {
		$data = YCore::sys_auth($token, 'DECODE');
		$data = explode("\t", $data);
		if (count($data) != 4) {
			YCore::throw_exception(6004003, '登录超时,请重新登录');
		}
		$result = [
				'user_id'    => $data[0],	// 用户ID。
				'password'   => $data[1],	// 加密的ID。
				'login_time' => $data[2],	// 登录时间。
				'mode'       => $data[3],	// token模式。1接口模式、0非接口模式。
		];
		return $result;
	}
}