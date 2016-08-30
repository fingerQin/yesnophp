<?php
/**
 * 全局数据验证类。
 * @author winerQin
 */

namespace winer;

use common\YCore;
class Validator {

	/**
	 * 规则验证失败提示语。
	 * @var array
	 */
	public static $rule_txt = [
			'qq'             => '%label%格式不正确',
			'mobilephone'    => '%label%格式不正确',
			'telephone'      => '%label%格式不正确',
			'zipcode'        => '%label%格式不正确',
			'number_between' => '%label%必须在%min%到%max%之间',
			'chinese'        => '%label%必须为中文',
			'idcard'         => '%label%格式不正确',
			'integer'        => '%label%必须为整型',
			'ip'             => '%label%格式不正确',
			'url'            => '%label%格式不正确',
			'utf8'           => '%label%必须为UTF-8字符',
			'email'          => '%label%格式不正确',
			'require'        => '%label%不能为空',
			'len'            => '%label%长度必须在%min%~%max%之间',
			'date'           => '%label%格式不正确',
			'alpha'          => '%label%必须为字母',
			'alpha_number'   => '%label%只能是字母或数字',
			'alpha_between'  => '%label%字母必须在%start%~%end%之间',
			'alpha_dash'     => '%label%必须由字母、数字、下划线(_)、破拆号(-)组成',
			'mac'            => '%label%格式不正确',
			'float'          => '%label%格式不正确',
			'boolean'        => '%label%格式不正确',
	];

	/**
	 * 验证数据。
	 * -- Example start --
	 * $data = [
	 * 'username'    => 'yesnophp',
	 * 'password'    => '123456',
	 * 'mobilephone' => '18575202691',
	 * 'code'        => '1234',
	 * 'invite_code' => '',
	 * ];
	 * 
	 * $rules = [
	 * 'username'    => '账号|require|alpha_dash|len:6:20:0',
	 * 'password'    => '密码|require|alpha_dash|len:6:20:0',
	 * 'mobilephone' => '手机号码|require|mobilephone',
	 * 'code'        => '短信验证码|require|len:4:6:0',
	 * 'invite_code' => '邀请码|alpha_number',
	 * ];
	 * -- Example end --
	 * @param array $data 待验证的数据。
	 * @param array $rules 数据验证规则。
	 * @return bool
	 */
	public static function valido(array $data, array $rules) {
		if (empty($data)) {
			YCore::exception(5001001, 'The $data parameter can\'t be empty');
		}
		if (empty($rules)) {
			YCore::exception(5001002, 'The $rules parameter can\'t be empty');
		}
		foreach ($rules as $name => $rule) {
			if (!array_key_exists($name, $data)) {
				YCore::exception(5001003, "The {$name} value does not exist");
			}
			$arr_rule = explode('|', $rule);
			if (count($arr_rule) === 1) {
				continue; // 如果没有设置任何规则，则验证下一个。
			}
			$vali_value = $data[$name];	// 当前被验证的值。
			$label_name = ''; 			// 当前被验证字段的别名。
			foreach ($arr_rule as $index => $rule_item) {
				if ($index == 0) { // 等于0是当前名称的名称。
					$label_name = $rule_item;
					continue;
				}
				$arr_rule_item = explode(':', $rule_item);
				$rule_name = $arr_rule_item[0]; 			// 验证器名称。
				$rule_exception_code = $arr_rule_item[1];	// 验证器验证失败之后抛出的异常code。
				switch ($rule_name) {
					case 'require':
					case 'mobilephone':
					case 'telephone':
					case 'qq':
					case 'zipcode':
					case 'idcard':
					case 'ip':
					case 'url':
					case 'email':
					case 'utf8':
					case 'alpha':
					case 'alpha_dash':
					case 'alpha_number':
					case 'integer':
					case 'chinese':
					case 'mac':
					case 'float':
					case 'boolean':
						if ($rule_name != 'require' && strlen($vali_value) === 0) {
							continue;
						}
						$class_func_name = "is_{$rule_name}"; // 当前调用的验证器名称。
						if (!self::$class_func_name($vali_value)) {
							$errmsg = str_replace('%label%', $label_name, self::$rule_txt[$rule_name]);
							YCore::exception($rule_exception_code, $errmsg);
						}
						break;
					case 'alpha_between':
						if (strlen($vali_value) === 0) {
							continue;
						}
						if (!isset($arr_rule_item[2])) {
							YCore::exception(5001004, 'Alpha_between validator must set the starting value');
						}
						if (!isset($arr_rule_item[3])) {
							YCore::exception(5001005, 'Alpha_between validator must set the cut-off value');
						}
						if (!self::is_alpha_between($vali_value, $arr_rule_item[2], $arr_rule_item[3])) {
							$errmsg = str_replace('%label%', $label_name, self::$rule_txt[$rule_name]);
							YCore::exception($rule_exception_code, $errmsg);
						}
						break;
					case 'number_between':
						if (strlen($vali_value) === 0) {
							continue;
						}
						if (!isset($arr_rule_item[2])) {
							YCore::exception(5001006, 'Number_between validator must set the minimum value');
						}
						if (!isset($arr_rule_item[3])) {
							YCore::exception(5001007, 'Number_between validator must set the maximum value');
						}
						if (!self::is_number_between($vali_value, $arr_rule_item[2], $arr_rule_item[3])) {
							$errmsg = str_replace('%label%', $label_name, self::$rule_txt[$rule_name]);
							$errmsg = str_replace('%min%', $arr_rule_item[2], $errmsg);
							$errmsg = str_replace('%max%', $arr_rule_item[3], $errmsg);
							YCore::exception($rule_exception_code, $errmsg);
						}
						break;
					case 'len':
						if (strlen($vali_value) === 0) {
							continue;
						}
						if (!isset($arr_rule_item[2])) {
							YCore::exception(5001008, 'Len validator first parameter must be set');
						}
						if (!isset($arr_rule_item[3])) {
							YCore::exception(5001009, 'Len validator second parameter must be set');
						}
						if (!isset($arr_rule_item[4])) {
							YCore::exception(5001011, 'Len validator third parameter must be set');
						}
						if (!self::is_len($vali_value, $arr_rule_item[2], $arr_rule_item[3], $arr_rule_item[4])) {
							$errmsg = str_replace('%label%', $label_name, self::$rule_txt[$rule_name]);
							$errmsg = str_replace('%min%', $arr_rule_item[2], $errmsg);
							$errmsg = str_replace('%max%', $arr_rule_item[3], $errmsg);
							YCore::exception($rule_exception_code, $errmsg);
						}
						break;
					case 'date':
						if (strlen($vali_value) === 0) {
							continue;
						}
						if (!isset($arr_rule_item[1])) {
							YCore::exception(5001012, 'Date validator first parameter must be set');
						}
						$format = $arr_rule_item[2]==1 ? 'Y-m-d H:i:s' : 'Y-m-d';
						if (!self::is_date($vali_value, $format)) {
							$errmsg = str_replace('%label%', $label_name, self::$rule_txt[$rule_name]);
							YCore::exception($rule_exception_code, $errmsg);
						}
						break;
					default:
						YCore::exception(5003000, 'In the name of the validator illegally');
						break;
				}
			}
		}
		return true;
	}

	/**
	 * 判断是否为QQ号码。
	 * @param string $qq
	 * @return boolean
	 */
	public static function is_qq($qq) {
		return preg_match('/^[1-9]\d{4,12}$/', $qq) ? true : false;
	}

	/**
	 * 判断是否为手机号码。
	 * @param string $mobilephone
	 * @return boolean
	 */
	public static function is_mobilephone($mobilephone) {
		return preg_match('/^13[\d]{9}$|14^[0-9]\d{8}|^15[0-9]\d{8}$|^18[0-9]\d{8}$/', $mobilephone) ? true : false;
	}

	/**
	 * 判断是否为座机号码。
	 * @param string $telphone
	 * @return boolean
	 */
	public static function is_telephone($telphone) {
		return preg_match('/^((\(\d{2,3}\))|(\d{3}\-))?(\(0\d{2,3}\)|0\d{2,3}-)?[1-9]\d{6,7}(\-\d{1,4})?$/', $telphone) ? true : false;
	}

	/**
	 * 判断是否为邮政编码。
	 * @param string $zipcode
	 * @return boolean
	 */
	public static function is_zipcode($zipcode) {
		return preg_match('/^[1-9]\d{5}$/', $zipcode) ? true : false;
	}

	/**
	 * 判断字母是否在某个区域内。用于判断某个字符只能介于[a-h](包含)之间的类似情况。
	 * @param string $alpha 原值。
	 * @param string $start_alpha 起始值。
	 * @param string $end_alpha 截止值。
	 * @return boolean
	 */
	public static function is_alpha_between($alpha, $start_alpha, $end_alpha) {
		if (Validator::is_alpha($alpha) === false) {
			return false;
		}
		if (Validator::is_alpha($start_alpha) === false) {
			return false;
		}
		if (Validator::is_alpha($end_alpha) === false) {
			return false;
		}
		if ($start_alpha >= $end_alpha) {
			return false;
		}
		if ($alpha < $start_alpha) {
			return false;
		}
		if ($alpha > $end_alpha) {
			return false;
		}
		return true;
	}

	/**
	 * 判断数字是否在某个区域之间。[2, 10],包含边界值。
	 * @param int $value 原值。
	 * @param int $start_value 起始值。
	 * @param int $end_value 截止值。
	 * @return boolean
	 */
	public static function is_number_between($value, $start_value, $end_value) {
		if (is_numeric($value) === false || is_numeric($start_value) === false || is_numeric($end_value) === false) {
			return false;
		}
		if ($start_value > $end_value) {
			return false;
		}
		if ($value < $start_value) {
			return false;
		}
		if ($value > $end_value) {
			return false;
		}
		return true;
	}

	/**
	 * 验证是否为中文。
	 * @param string $char
	 * @return bool
	 */
	public static function is_chinese($char) {
		if (strlen($char) === 0) {
			return false;
		}
		return (preg_match("/^[\x7f-\xff]+$/", $char)) ? true : false;
	}

	/**
	 * 判断是否为字母、数字、下划线（_）、破折号（-）。
	 * @param string $str
	 * @return boolean
	 */
	public static function is_alpha_dash($str) {
		return preg_match('/^([a-z0-9_-])+$/i', $str) ? true : false;
	}

	/**
	 * 验证身份证号码是否合法。
	 * @param string $vStr
	 * @return bool
	 */
	public static function is_idcard($vStr) {
		$vCity = array(
	        '11','12','13','14','15','21','22',
	        '23','31','32','33','34','35','36',
	        '37','41','42','43','44','45','46',
	        '50','51','52','53','54','61','62',
	        '63','64','65','71','81','82','91'
	    );
	    if (!preg_match('/^([\d]{17}[xX\d]|[\d]{15})$/', $vStr)) {
	    	return false;
	    }
	    if (!in_array(substr($vStr, 0, 2), $vCity)) {
	    	return false;
	    }
	    $vStr = preg_replace('/[xX]$/i', 'a', $vStr);
	    $vLength = strlen($vStr);
	 
	    if ($vLength == 18) {
	        $vBirthday = substr($vStr, 6, 4) . '-' . substr($vStr, 10, 2) . '-' . substr($vStr, 12, 2);
	    } else {
	        $vBirthday = '19' . substr($vStr, 6, 2) . '-' . substr($vStr, 8, 2) . '-' . substr($vStr, 10, 2);
	    }
	    if (date('Y-m-d', strtotime($vBirthday)) != $vBirthday) {
	    	return false;
	    }
	    if ($vLength == 18) {
	        $vSum = 0;
	        for ($i = 17 ; $i >= 0 ; $i--) {
	            $vSubStr = substr($vStr, 17 - $i, 1);
	            $vSum += (pow(2, $i) % 11) * (($vSubStr == 'a') ? 10 : intval($vSubStr, 11));
	        }
	        if($vSum % 11 != 1) {
	        	return false;
	        }
	    }
	    return true;
	}

	/**
	 * 验证日期时间格式。
	 * -- 1、验证$value是否为$format格式。
	 * -- 2、只能验证格式，不能验证时间是否正确。比如：2014-22-22
	 * @param string $value 日期。
	 * @param string $format 格式。格式如：Y-m-d 或H:i:s
	 * @return boolean
	 */
	public static function is_date($value, $format = 'Y-m-d H:i:s') {
		return date_create_from_format($format, $value) !== false;
	}

	/**
	 * 判断是否为整数。
	 * @param string $str
	 * @return boolean
	 */
	public static function is_integer($str) {
		return filter_var($str, FILTER_VALIDATE_INT) !== false;
	}

	/**
	 * 判断是否为浮点型。
	 * @param string $str
	 * @return boolean
	 */
	public static function is_float($str) {
		return filter_var($str, FILTER_VALIDATE_FLOAT) !== false;
	}

	/**
	 * 判断是否为字母数字。
	 * @param string $str
	 * @return boolean
	 */
	public static function is_alpha_number($str) {
		return preg_match('/^([a-z0-9])+$/i', $str) ? true : false;
	}

	/**
	 * 判断是否为字母。
	 * @param string $str
	 * @return boolean
	 */
	public static function is_alpha($str) {
		return preg_match('/^([a-z])+$/i', $str) ? true : false;
	}

	/**
	 * 验证IP是否合法。
	 * @param string $ip
	 * @return bool
	 */
	public static function is_ip($ip) {
		return filter_var($ip, FILTER_VALIDATE_IP) !== false;
	}

	/**
	 * 验证URL是否合法。
	 * -- 合法的URL：http://www.baidu.com
	 * @param string $url
	 * @return bool
	 */
	public static function is_url($url) {
		return filter_var($url, FILTER_VALIDATE_URL) !== false;
	}

	/**
	 * 判断email格式是否正确。
	 * @param string $email
	 * @return bool
	 */
	public static function is_email($email) {
		return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
	}

	/**
	 * 是否必需。
	 * @param string $str
	 * @return bool
	 */
	public static function is_require($str) {
		return strlen($str) ? true : false;
	}

	/**
	 * 判断字符串是否为utf8编码，英文和半角字符返回ture。
	 * @param string $string
	 * @return bool
	 */
	public static function is_utf8($string) {
		return preg_match('%^(?:
					[\x09\x0A\x0D\x20-\x7E] # ASCII
					| [\xC2-\xDF][\x80-\xBF] # non-overlong 2-byte
					| \xE0[\xA0-\xBF][\x80-\xBF] # excluding overlongs
					| [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2} # straight 3-byte
					| \xED[\x80-\x9F][\x80-\xBF] # excluding surrogates
					| \xF0[\x90-\xBF][\x80-\xBF]{2} # planes 1-3
					| [\xF1-\xF3][\x80-\xBF]{3} # planes 4-15
					| \xF4[\x80-\x8F][\x80-\xBF]{2} # plane 16
					)*$%xs', $string) ? true : false;
	}

	/**
	 * 检查字符串长度[包含]。
	 * @param string $str 字符串。
	 * @param int $min 最小长度。
	 * @param int $max 最大长度。
	 * @param bool $is_utf8 是否UTF-8字符。
	 * @return boolean
	 */
	public static function is_len($str, $min = 0, $max = 255, $is_utf8 = false) {
		$len = $is_utf8 ? mb_strlen($str, 'UTF-8') : strlen($str);
		if (($len >= $min) && ($len <= $max)) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * 判断是否MAC地址。
	 * @param string $str
	 */
	public static function is_mac($str) {
		return filter_var($str, FILTER_VALIDATE_MAC) !== false;
	}

	/**
	 * 判断是否为真。
	 * -- 1、Returns TRUE for "1", "true", "on" and "yes". Returns FALSE otherwise.
	 * @param string $str
	 */
	public static function is_boolean($str) {
		return filter_var($str, FILTER_VALIDATE_BOOLEAN) !== false;
	}
}