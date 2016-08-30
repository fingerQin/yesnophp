<?php
/**
 * 公共controller。
 * --1、Yaf框架会根据特有的类名后缀(Model、Controller、Plugin)进行自动加载。为避免这种情况请不要以这样的名称结尾。
 * @author winerQin
 * @date 2015-11-13
 */

namespace common\controllers;

use common\YCore;
use winer\Validator;
class Common extends \Yaf\Controller_Abstract {

	/**
	 * 配置文件对象。
	 * @var \Yaf\Config_Abstract
	 */
	protected $_config = null;

	/**
	 * 请求对象。
	 * @var \Yaf\Request_Http
	 */
	protected $_request = null;

	/**
	 * 视图对象。
	 * @var \Yaf\View_Simple
	 */
	protected $_view = null;

	/**
	 * session对象。
	 * @var Yaf_Session
	 */
	protected $_session = null;

	/**
	 * MYSQL连接。
	 * @var \PDO
	 */
	protected $_mysql = null;

	/**
	 * 该方法在所有Action执行之前执行。主要做一些初始化工作。
	 */
	public function init() {
		$this->_view    = $this->getView();
		$this->_request = $this->getRequest();
		$this->_session = \Yaf\Registry::get('session');
		$this->_config  = \Yaf\Registry::get('config');
		$this->_mysql   = \Yaf\Registry::get('mysql');
	}

	/**
	 * 从请求中读取一个整型数值。
	 * -- 1、如果该数据本身不是一个整型，将会抛异常。
	 * -- 2、如果该数值不存在将会返回默认值。
	 * -- 3、默认值也必须是整型。
	 * -- 4、读取的值将从GPC(GET、POST)中读取。
	 * @param string $name
	 * @param int $default_value
	 */
	final protected function getInt($name, $default_value = null) {
		$gp_value = $this->getGP($name);
		if (is_null($gp_value)) {
			if (is_null($default_value)) {
				YCore::exception(5009001, "{$name}值异常");
			} else if (!Validator::is_integer($default_value)) {
				YCore::exception(5009002, "{$name}默认值不是整型");
			} else {
				return $default_value;
			}
		} else {
			if (!Validator::is_integer($gp_value)) {
				YCore::exception(5009003, "{$name}值不是整型");
			} else {
				return $gp_value;
			}
		}
	}

	/**
	 * 从请求中读取一个数组。
	 * -- 1、如果该数据本身不是一个数组类型，将会抛异常。
	 * -- 2、如果该数值不存在将会返回默认值。
	 * -- 3、默认值也必须是数组类型。
	 * -- 4、读取的值将从GPC(GET、POST)中读取。
	 * @param string $name
	 * @param array $default_value
	 */
	final protected function getArray($name, $default_value = null) {
	    $gp_value = $this->getGP($name);
	    if (is_null($gp_value)) {
	        if (is_null($default_value)) {
	            YCore::exception(5009001, "{$name}值异常");
	        } else if (!is_array($default_value)) {
	            YCore::exception(5009002, "default_valuec参数不是数组");
	        } else {
	            return $default_value;
	        }
	    } else {
	        if (!is_array($gp_value)) {
	            YCore::exception(5009003, "{$name}值不是数组");
	        } else {
	            return $gp_value;
	        }
	    }
	}

	/**
	 * 从请求中读取一个浮点型数值。
	 * -- 1、如果该数据本身不是一个浮点型，将会抛异常。
	 * -- 2、如果该数值不存在将会返回默认值。
	 * -- 3、默认值也必须是浮点型。
	 * -- 4、读取的值将从GPC(GET、POST)中读取。
	 * @param string $name
	 * @param int $default_value
	 */
	final protected function getFloat($name, $default_value = null) {
		$gp_value = $this->getGP($name);
		if (is_null($gp_value)) {
			if (is_null($default_value)) {
				YCore::exception(5009004, "{$name}值异常");
			} else if (!Validator::is_float($default_value)) {
				YCore::exception(5009005, "default_valuec参数不是浮点型");
			} else {
				return $default_value;
			}
		} else {
			if (!Validator::is_float($gp_value)) {
				YCore::exception(5009006, "{$name}值不是浮点型");
			} else {
				return $gp_value;
			}
		}
	}

	/**
	 * 从请求中读取一个字符串数值。
	 * -- 1、如果该数值不存在将会返回默认值。
	 * -- 2、读取的值将从GPC(GET、POST)中读取。
	 * -- 3、数据会进行防注入处理。
	 * @param string $name
	 * @param int $default_value
	 */
	final protected function getString($name, $default_value = null) {
		$gp_value = $this->getGP($name);
		if (is_null($gp_value)) {
			if (is_null($default_value)) {
				YCore::exception(5009007, "{$name}值异常");
			} else {
				return $default_value;
			}
		} else {
			return $gp_value;
		}
	}

	/**
	 * 获取GET、POST、路由里面的值。
	 * -- 1、先读路由分解出来的参数、再读GET、其次读POST。
	 * @param string $name
	 * @return mixed
	 */
	final protected function getGP($name) {
	    $value = $this->_request->getParam($name);
	    if (strlen($value) > 0) {
	        return $value;
	    }
		if (isset($_GET[$name])) {
			return $this->_request->getQuery($name);
		} else if (isset($_POST[$name])) {
			return $this->_request->getPost($name);
		} else {
			return null;
		}
	}

	public function beginTransaction() {
		
	}
	
	public function commit() {
		
	}
	
	public function rollback(){
		
	}

	/**
	 * 关闭模板渲染。
	 */
	public function end() {
		\Yaf\Dispatcher::getInstance()->autoRender(FALSE);
	}

	/**
	 * 输出JSON到浏览器。
	 * @param boolean $status 操作成功与否。true:成功、false：失败。
	 * @param string $message 提示信息。
	 * @param array $data 返回的数据。如果不存在则连data键不会返回。
	 * @return void
	 */
	public function json($status, $message, array $data = null) {
	    $result = [
	        'errmsg' => $message
	    ];
	    if ($status) {
	        $result['errcode'] = 0;
	    } else {
	        $result['errcode'] = -1;
	    }
	    if (!is_null($data)) {
	        $result['data'] = $data;
	    }
	    echo json_encode($result);
	    $this->end();
	    exit;
	}

	/**
	 * 错误信息。
	 * @param string $message 错误信息。
	 * @param string $url 跳转地址。
	 * @param number $second 跳转时间。
	 */
	public function error($message = '', $url = '', $second = 3) {
		$this->_view->assign('message', $message);
		$this->_view->assign('url', $url);
		$this->_view->assign('second', $second);
		$script_path = $this->getViewPath();
		$this->_view->display($script_path . "/common/error.php");
		$this->end();
	}

	/**
	 * 成功信息。
	 * @param string $message 错误信息。
	 * @param string $url 跳转地址。
	 * @param number $second 跳转时间。
	 * @return void
	 */
	public function success($message = '', $url = '', $second = 3) {
		$this->_view->assign('message', $message);
		$this->_view->assign('url', $url);
		$this->_view->assign('second', $second);
		$script_path = $this->getViewPath();
		$this->_view->display($script_path . "/common/error.php");
		$this->end();
	}
}