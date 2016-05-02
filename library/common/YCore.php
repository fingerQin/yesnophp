<?php
/**
 * 公用类库。
 * @author winerQin
 * @date 2015-11-13
 */
namespace common;

class YCore {

    /**
     * 获取系统缓存对象。
     * @return \Redis
     */
    public static function getCache() {
        $ok = \Yaf\Registry::has('redis');
        if ($ok) {
            return \Yaf\Registry::get('redis');
        } else {
            $config = \Yaf\Registry::get('config');
            $redis_host  = $config->database->redis->host;
            $redis_port  = $config->database->redis->port;
            $redis_pwd   = $config->database->redis->pwd;
            $redis_index = $config->database->redis->index;
            $redis = new \Redis();
            $redis->connect($redis_host, $redis_port);
            $redis->auth($redis_pwd);
            $redis->select($redis_index);
            \Yaf\Registry::set('redis', $redis);
            return $redis;
        }
    }

    /**
     * Cookie操作。
     * @param string $name cookie名称。
     * @param unknown $value cookie值。如果为null，代表yfb
     * @param number $expire 有效时间。
     * @param string $path 生效的路径。
     * @param string $domain 生效的域名。
     * @param string $secure 
     * @param string $httponly
     * @return boolean|string
     */
    public static function setcookie($name, $value, $expire = 0, $path = null, $domain = null, $secure = false, $httponly = false) {
        
    }

	/**
	 * 打印变量。
	 * @param mixed $data
	 */
	public static function print_r($data) {
		echo '<pre>';
		print_r($data);
		echo '</pre>';
	}

	/**
	 * 获取系统字典数据。
	 * @param string $dict_name 字典名称。
	 * @return array
	 */
	public static function dict($dict_name) {
	    $dict_type_model = new \models\DictType();
	    $dict_type = $dict_type_model->fetchOne([], ['type_code' => $dict_name, 'status' => 1]);
	    if (empty($dict_type)) {
	        self::throw_exception(-1, "系统字典[{$dict_name}]未设置");
	    }
		$dict_model = new \models\Dict();
		$columns = [
		    'dict_code',
		    'dict_name'
		];
		$where = [
		    'dict_type_id' => $dict_type['dict_type_id'], 
		    'status'       => 1
		];
		$dict_list = $dict_model->fetchAll($columns, $where, 0, 'listorder ASC');
		if (is_null($dict_list)) {
			self::throw_exception(3001201, "系统字典[{$dict_name}]的字典值未设置");
		} else {
		    $data = [];
		    foreach ($dict_list as $dict) {
		        $data[$dict['dict_code']] = $dict['dict_name'];
		    }
		    unset($dict_list);
			return $data;
		}
	}

	/**
	 * 获取系统配置表里面的配置。
	 * @param string $cname 配置名称。
	 * @param string $default_value 如果取不到时的默认值。注意数据类型。
	 * @return string
	 */
	public static function sys_config($cname, $default_value = null) {
		$config_model = new \models\Config();
		$config_value = $config_model->getValue($cname);
		if (is_null($config_value)) {
			if (is_null($default_value)) {
				self::throw_exception(3001200, "系统配置（{$cname}）未设置");
			} else {
				return $default_value;
			}
		} else {
			return $config_value;
		}
	}

	/**
	 * Yaf框架内所有位置产生的日志都由此方法写入。
	 * @param int $log_type 日志类型。
	 * @param string $log_content 日志类型。
	 * @param int $user_id 日志操作人。
	 * @param int $log_time 日志产生时间。
	 * @param int $errcode 错误编号。
	 * @return void
	 */
	public static function yaf_log($log_type, $log_content, $user_id = 0, $log_time = 0, $errcode = 0) {
		$model = new \models\Log();
		$log_time = $log_time > 0 ? $log_time : $_SERVER['REQUEST_TIME'];
		$model->addLog($log_type, $log_content, $user_id, $log_time, $errcode);
	}

	/**
	 * 将PHP变量到处为文件内容
	 * @param mixed $var
	 * @return string
	 */
	public static function  export($var) {
		return "<?php\nreturn " . var_export($var, true) . ";";
	}

	/**
	 * 获取当前时间戳。
	 * @param bool $real_time 是否实时的日期。如果取入口的微秒，则会出现业务处理超长时间，依然是业务处理之前的时间。
	 * @return int
	 */
	public static function getTimestamp($real_time = false) {
		if ($real_time === false) {
			return time();
		} else {
			list($usec, $sec) = explode(' ', MICROTIME);
			return $sec;
		}
	}

	/**
	 *  根据两点间的经纬度计算距离
	 *  -- 1、纬度最大值为90度，经度最大值为180度。
	 *  -- 2、只要其中一个值为-1则返回0.这是特殊约定的业务逻辑。
	 *  @param float $lat 纬度值。
	 *  @param float $lng 经度值。
	 *  @param float $lat2 纬度值2。
	 *  @param float $lng2 经度值2。
	 *  @return int
	 */
	public static function distance($lat1, $lng1, $lat2, $lng2) {
		if ($lat1 == -1 || $lng1 == -1 || $lat2 == -1 || $lng2 == -1) {
			return 0;
		}
		$earthRadius = 6371000; // approximate radius of earth in meters
		$lat1 = ($lat1 * pi()) / 180;
		$lng1 = ($lng1 * pi()) / 180;
		$lat2 = ($lat2 * pi()) / 180;
		$lng2 = ($lng2 * pi()) / 180;
		$calcLongitude = $lng2 - $lng1;
		$calcLatitude = $lat2 - $lat1;
		$stepOne = pow(sin($calcLatitude / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($calcLongitude / 2), 2);
		$stepTwo = 2 * asin(min(1, sqrt($stepOne)));
		$calculatedDistance = $earthRadius * $stepTwo;
		return round($calculatedDistance);
	}

	/**
	 * MongoCursor对象转数组。
	 * @param MongoCursor $cursor
	 * @return array
	 */
	public static function mongoToArray($cursor) {
		$data = [];
		foreach ($cursor as $document) {
			$data[] = $document;
		}
		return $data;
	}

	/**
	 * 数组转换为树。
	 * @param array $sourceArr 源数组。
	 * @param string $key 数组主键名称。
	 * @param string $parentKey 数组父id键名称。
	 * @param string $childrenKey 生成的子树键名称。
	 * @return array
	 */
	public static function arrtyToTree($sourceArr, $key, $parentKey, $childrenKey) {
		$tempSrcArr = [];
		foreach ($sourceArr as $v) {
			$tempSrcArr[$v[$key]] = $v;
		}
		$i = 0;
		$count = count($sourceArr);
		for ($i = ($count - 1); $i >= 0; $i--) {
			if (isset($tempSrcArr[$sourceArr[$i][$parentKey]])) {
				$tArr = array_pop($tempSrcArr);
				$tempSrcArr[$tArr[$parentKey]][$childrenKey] = (isset($tempSrcArr[$tArr[$parentKey]][$childrenKey]) && is_array($tempSrcArr[$tArr[$parentKey]][$childrenKey])) ? $tempSrcArr[$tArr[$parentKey]][$childrenKey] : [];
				array_push($tempSrcArr[$tArr[$parentKey]][$childrenKey], $tArr);
			}
		}
		// 最外层关联索引转换为数字索引，这样在json转换的时候是list,而非对象。
		$_items = [];
		foreach ($tempSrcArr as $_temp_item) {
			$_items[] = $_temp_item;
		}
		return $_items;
	}

	/**
	 * 读取配置文件。
	 * @param string $key 配置名。
	 * @param string $val 当值不存在返回此值。
	 * @return mixed
	 */
	public static function config($key, $val = null) {
		$config = \Yaf\Registry::get("config");
		$cval = $config->get($key);
		if (is_string($cval)) {
			return $cval;
		} else if (is_null($cval) === false) {
			return $cval->toArray();
		} else {
			return $val;
		}
	}

	/**
	 * 抛出异常。
	 * @param int $err_code 错误编号。
	 * @param string $err_msg 错误信息。
	 * @param string $log_data 发生异常时，涉及到的日志数据（用于分析异常）。
	 * @throws Exception
	 */
	public static function throw_exception($err_code, $err_msg, $log_data = '') {
		throw new \Exception($err_msg, $err_code);
	}

	/**
	 * 返回经addslashes处理过的字符串或数组
	 * @param $string 需要处理的字符串或数组
	 * @return mixed
	 */
	public static function new_addslashes($string) {
		if (!is_array($string)) {
			return addslashes($string);
		}
		foreach ($string as $key => $val) {
			$string[$key] = self::new_addslashes($val);
		}
		return $string;
	}

	/**
	 * 返回经stripslashes处理过的字符串或数组
	 * @param $string 需要处理的字符串或数组
	 * @return mixed
	 */
	public static function new_stripslashes($string) {
		if (!is_array($string)) {
			return stripslashes($string);
		}
		foreach ($string as $key => $val) {
			$string[$key] = self::new_stripslashes($val);
		}
		return $string;
	}

	/**
	 * 返回经htmlspecialchars处理过的字符串或数组
	 * @param $obj 需要处理的字符串或数组
	 * @return mixed
	 */
	public static function new_html_special_chars($string) {
		$encoding = 'utf-8';
		if (!is_array($string)) {
			return htmlspecialchars($string, ENT_QUOTES, $encoding);
		}
		foreach ($string as $key => $val) {
			$string[$key] = self::new_html_special_chars($val);
		}
		return $string;
	}

	public static function new_html_entity_decode($string) {
		$encoding = 'utf-8';
		return html_entity_decode($string, ENT_QUOTES, $encoding);
	}

	public static function new_htmlentities($string) {
		$encoding = 'utf-8';
		return htmlentities($string, ENT_QUOTES, $encoding);
	}

	/**
	 * 过滤ASCII码从0-28的控制字符
	 * @return String
	 */
	public static function trim_unsafe_control_chars($str) {
		$rule = '/[' . chr(1) . '-' . chr(8) . chr(11) . '-' . chr(12) . chr(14) . '-' . chr(31) . ']*/';
		return str_replace(chr(0), '', preg_replace($rule, '', $str));
	}

	/**
	 * 格式化文本域内容
	 * @param $string 文本域内容
	 * @return string
	 */
	public static function trim_textarea($string) {
		$string = nl2br(str_replace(' ', '&nbsp;', $string));
		return $string;
	}

	/**
	 * 字符截取支持UTF8。
	 * @param $string
	 * @param $length
	 * @param $dot
	 * @return string
	 */
	public static function str_cut($string, $length, $dot = '...') {
		$strlen = strlen($string);
		if ($strlen <= $length) {
			return $string;
		}
		$string = str_replace([
				' ', 
				'&nbsp;', 
				'&amp;', 
				'&quot;', 
				'&#039;', 
				'&ldquo;', 
				'&rdquo;', 
				'&mdash;', 
				'&lt;', 
				'&gt;', 
				'&middot;', 
				'&hellip;' 
		], [
				'∵', 
				' ', 
				'&', 
				'"', 
				"'", 
				'“', 
				'”', 
				'—', 
				'<', 
				'>', 
				'·', 
				'…' 
		], $string);
		$strcut = '';
		$length = intval($length - strlen($dot) - $length / 3);
		$n = $tn = $noc = 0;
		while ($n < strlen($string)) {
			$t = ord($string[$n]);
			if ($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
				$tn = 1;
				$n++;
				$noc++;
			} elseif (194 <= $t && $t <= 223) {
				$tn = 2;
				$n += 2;
				$noc += 2;
			} elseif (224 <= $t && $t <= 239) {
				$tn = 3;
				$n += 3;
				$noc += 2;
			} elseif (240 <= $t && $t <= 247) {
				$tn = 4;
				$n += 4;
				$noc += 2;
			} elseif (248 <= $t && $t <= 251) {
				$tn = 5;
				$n += 5;
				$noc += 2;
			} elseif ($t == 252 || $t == 253) {
				$tn = 6;
				$n += 6;
				$noc += 2;
			} else {
				$n++;
			}
			if ($noc >= $length) {
				break;
			}
		}
		if ($noc > $length) {
			$n -= $tn;
		}
		$strcut = substr($string, 0, $n);
		$strcut = str_replace([
				'∵', 
				'&', 
				'"', 
				"'", 
				'“', 
				'”', 
				'—', 
				'<', 
				'>', 
				'·', 
				'…' 
		], [
				' ', 
				'&amp;', 
				'&quot;', 
				'&#039;', 
				'&ldquo;', 
				'&rdquo;', 
				'&mdash;', 
				'&lt;', 
				'&gt;', 
				'&middot;', 
				'&hellip;' 
		], $strcut);            
		return $strcut . $dot;
	}

	public static function  get_cost_time() {
		$microtime = microtime(TRUE);
		return $microtime - LARAVEL_START;
	}

	/**
	 * 产生随机字符串
	 * @param int $length  输出长度
	 * @param string $chars 可选的，默认为 0123456789
	 * @return string 字符串
	 */
	public static function random($length, $chars = '0123456789') {
		$hash = '';
		$max = strlen($chars) - 1;
		for ($i = 0; $i < $length; $i++) {
			$hash .= $chars[mt_rand(0, $max)];
		}
		return $hash;
	}

	/**
	 * 生成随机字符串
	 * @param string $lenth 长度
	 * @return string 字符串
	 */
	public static function create_randomstr($lenth = 6) {
		return self::random($lenth, '123456789abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ');
	}

	/**
	 * 转换字节数为其他单位
	 *
	 * @param   string  $filesize   字节大小
	 * @return  string  返回大小
	 */
	public static function  sizecount($filesize) {
		if ($filesize >= 1073741824) {
			$filesize = round($filesize / 1073741824 * 100) / 100 . ' GB';
		} elseif ($filesize >= 1048576) {
			$filesize = round($filesize / 1048576 * 100) / 100 . ' MB';
		} elseif ($filesize >= 1024) {
			$filesize = round($filesize / 1024 * 100) / 100 . ' KB';
		} else {
			$filesize = $filesize . ' Bytes';
		}
		return $filesize;
	}

	/**
	 * 字符串加密、解密函数
	 * @param string $txt 字符串
	 * @param string $operation ENCODE为加密，DECODE为解密，可选参数，默认为ENCODE，
	 * @param string $key 密钥：数字、字母、下划线
	 * @param string $expiry 过期时间
	 * @return string
	 */
	public static function sys_auth($string, $operation = 'ENCODE', $key = '', $expiry = 0) {
		$key_length = 4;
		$key = md5($key != '' ? $key : self::config('authkey'));
		$fixedkey = md5($key);
		$egiskeys = md5(substr($fixedkey, 16, 16));
		$runtokey = $key_length ? ($operation == 'ENCODE' ? substr(md5(microtime(true)), -$key_length) : substr($string, 0, $key_length)) : '';
		$keys = md5(substr($runtokey, 0, 16) . substr($fixedkey, 0, 16) . substr($runtokey, 16) . substr($fixedkey, 16));
		$string = $operation == 'ENCODE' ? sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $egiskeys), 0, 16) . $string : base64_decode(substr($string, $key_length));
		$i = 0;
		$result = '';
		$string_length = strlen($string);
		for ($i = 0; $i < $string_length; $i++) {
			$result .= chr(ord($string{$i}) ^ ord($keys{$i % 32}));
		}
		if ($operation == 'ENCODE') {
			return $runtokey . str_replace('=', '', base64_encode($result));
		} else {
			if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26) . $egiskeys), 0, 16)) {
				return substr($result, 26);
			} else {
				return '';
			}
		}
	}

	/**
	 * 取得文件扩展
	 * @param $filename 文件名
	 * @return 扩展名
	 */
	public static function fileext($filename) {
		return strtolower(trim(substr(strrchr($filename, '.'), 1, 10)));
	}

	/**
	 * 文件下载
	 * @param $filepath 文件路径
	 * @param $filename 文件名称
	 */
	public static function file_down($filepath, $filename = '') {
		if (!$filename) {
			$filename = basename($filepath);
		}
		if (self::is_ie()) {
			$filename = rawurlencode($filename);
		}
		$filetype = self::fileext($filename);
		$filesize = sprintf("%u", filesize($filepath));
		if (ob_get_length() !== false) {
			@ob_end_clean();
		}
		header('Pragma: public');
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		header('Cache-Control: no-store, no-cache, must-revalidate');
		header('Cache-Control: pre-check=0, post-check=0, max-age=0');
		header('Content-Transfer-Encoding: binary');
		header('Content-Encoding: none');
		header('Content-type: ' . $filetype);
		header('Content-Disposition: attachment; filename="' . $filename . '"');
		header('Content-length: ' . $filesize);
		readfile($filepath);
		exit();
	}

	/**
	 * 对数据进行编码转换
	 * @param array/string $data 数组
	 * @param string $input 需要转换的编码
	 * @param string $output 转换后的编码
	 */
	public static function array_iconv($data, $input = 'gbk', $output = 'utf-8') {
		if (!is_array($data)) {
			return iconv($input, $output, $data);
		} else {
			foreach ($data as $key => $val) {
				if (is_array($val)) {
					$data[$key] = self::array_iconv($val, $input, $output);
				} else {
					$data[$key] = iconv($input, $output, $val);
				}
			}
			return $data;
		}
	}

	/**
	 *
	 * 获取远程内容
	 * @param $url 接口url地址
	 * @param $timeout 超时时间
	 */
	public static function pc_file_get_contents($url, $timeout = 30) {
		$stream = stream_context_create([
				'http' => [
						'timeout' => $timeout 
				] 
		]);
		return @file_get_contents($url, 0, $stream);
	}

	/**
	 * 转换目录下面的所有文件编码格式
	 * @param string $in_charset 原字符集
	 * @param string $out_charset 目标字符集
	 * @param string $dir 目录地址
	 * @param string $fileexts 转换的文件格式
	 * @return string 如果原字符集和目标字符集相同则返回false，否则为true
	 */
	public static function dir_iconv($in_charset, $out_charset, $dir, $fileexts = 'php|html|htm|shtml|shtm|js|txt|xml') {
		if ($in_charset == $out_charset) {
			return false;
		}
		$list = self::dir_list($dir);
		foreach ($list as $v) {
			if (pathinfo($v, PATHINFO_EXTENSION) == $fileexts && is_file($v)) {
				file_put_contents($v, iconv($in_charset, $out_charset, file_get_contents($v)));
			}
		}
		return true;
	}

	/**
	 * 列出目录下所有文件
	 *
	 * @param string $path 路径
	 * @param string $exts 扩展名
	 * @param array $list 增加的文件列表
	 * @return array 所有满足条件的文件
	 */
	public static function dir_list($path, $exts = '', $list = []) {
		$path = self::dir_path($path);
		$files = glob($path . '*');
		foreach ($files as $v) {
			if (!$exts || pathinfo($v, PATHINFO_EXTENSION) == $exts) {
				$list[] = $v;
				if (is_dir($v)) {
					$list = self::dir_list($v, $exts, $list);
				}
			}
		}
		return $list;
	}

	/**
	 * 删除目录及目录下面的所有文件
	 *
	 * @param   string  $dir        路径
	 * @return  bool    如果成功则返回 TRUE，失败则返回 FALSE
	 */
	public static function dir_delete($dir) {
		$dir = self::dir_path($dir);
		if (!is_dir($dir)) {
			return FALSE;
		}
		$list = glob($dir . '*');
		foreach ($list as $v) {
			is_dir($v) ? self::dir_delete($v) : @unlink($v);
		}
		return @rmdir($dir);
	}

	/**
	 * 转化 \ 为 /
	 *
	 * @param   string  $path   路径
	 * @return  string  路径
	 */
	public static function dir_path($path) {
		$path = str_replace('\\', '/', $path);
		if (substr($path, -1) != '/')
			$path = $path . '/';
		return $path;
	}

	/**
	 * 获取请求ip
	 * @return string ip地址
	 */
	public static function ip() {
		if (getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
			$ip = getenv('HTTP_CLIENT_IP');
		} elseif (getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
			$ip = getenv('HTTP_X_FORWARDED_FOR');
		} elseif (getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
			$ip = getenv('REMOTE_ADDR');
		} elseif (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		return preg_match('/[\d\.]{7,15}/', $ip, $matches) ? $matches[0] : '';
	}

	/**
	 * IE浏览器判断
	 */
	public static function is_ie() {
		$useragent = strtolower($_SERVER['HTTP_USER_AGENT']);
		if ((strpos($useragent, 'opera') !== false) || (strpos($useragent, 'konqueror') !== false)) {
			return false;
		}
		if (strpos($useragent, 'msie ') !== false) {
			return true;
		}
		return false;
	}

	/**
	 * 多久之前
	 * @param $datetime
	 * @return string
	 */
	public static function howLongAgo($datetime) {
		$timestamp = strtotime($datetime);
		$seconds = time();
		$time = date('Y', $seconds) - date('Y', $timestamp);
		if ($time > 0) {
			if ($time == 1) {
				return '去年';
			} else {
				return $time . '年前';
			}
		}
		$time = date('m', $seconds) - date('m', $timestamp);
		if ($time > 0) {
			if ($time == 1) {
				return '上月';
			} else {
				return $time . '个月前';
			}
		}
		$time = date('d', $seconds) - date('d', $timestamp);
		if ($time > 0) {
			if ($time == 1) {
				return '昨天';
			} elseif ($time == 2) {
				return '前天';
			} else {
				return $time . '天前';
			}
		}
		$time = date('H', $seconds) - date('H', $timestamp);
		if ($time >= 1) {
			return $time . '小时前';
		}
		$time = date('i', $seconds) - date('i', $timestamp);
		if ($time >= 1) {
			return $time . '分钟前';
		}
		$time = date('s', $seconds) - date('s', $timestamp);
		return $time . '秒前';
	}

	/**
	 * 根据生日中的月份和日期来计算所属星座*
	 * @param int $birth_month
	 * @param int $birth_date
	 * @return string
	 */
	public static function get_constellation($birth_month, $birth_date) {
		// 判断的时候，为避免出现1和true的疑惑，或是判断语句始终为真的问题，这里统一处理成字符串形式
		$birth_month = strval($birth_month);
		$constellation_name = array(
				'水瓶座', 
				'双鱼座', 
				'白羊座', 
				'金牛座', 
				'双子座', 
				'巨蟹座', 
				'狮子座', 
				'处女座', 
				'天秤座', 
				'天蝎座', 
				'射手座', 
				'摩羯座' 
		);
		if ($birth_date <= 22) {
			if ('1' !== $birth_month) {
				$constellation = $constellation_name[$birth_month - 2];
			} else {
				$constellation = $constellation_name[11];
			}
		} else {
			$constellation = $constellation_name[$birth_month - 1];
		}
		return $constellation;
	}

	/**
	 * 根据生日中的年份来计算所属生肖
	 * @param int $birth_year
	 * @return string
	 */
	public static function get_animal($birth_year, $format = '1') {
		// 1900年是子鼠年
		if ($format == '2') {
			$animal = array(
					'子鼠', 
					'丑牛', 
					'寅虎', 
					'卯兔', 
					'辰龙', 
					'巳蛇', 
					'午马', 
					'未羊', 
					'申猴', 
					'酉鸡', 
					'戌狗', 
					'亥猪' 
			);
		} elseif ($format == '1') {
			$animal = array(
					'鼠', 
					'牛', 
					'虎', 
					'兔', 
					'龙', 
					'蛇', 
					'马', 
					'羊', 
					'猴', 
					'鸡', 
					'狗', 
					'猪' 
			);
		}
		$my_animal = ($birth_year - 1900) % 12;
		return $animal[$my_animal];
	}
	
	public static function factorial($a) {
	    if($a > 1) {
	        $r = $a * self::factorial($a - 1);
	    } else {
	        $r = $a;
	    }
	    return $r;
	}
}