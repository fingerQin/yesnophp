<?php
/**
 * 将SESSION封装到redis中保存。
 * @author winer
 * @date 2016-01-07
 */

namespace myredis;

class SessionHandler implements \SessionHandlerInterface {

	/**
	 * Redis对象。
	 * @var Client
	 */
	protected $_client;

	/**
	 * session前缀。
	 * @var string
	 */
	protected $_prefix = 'sess_';

	/**
	 * session有效期。
	 * @var int
	 */
	protected $_ttl;

	/**
	 * @var array
	 */
	protected $_cache = array();

	/**
	 * 构造方法。
	 * @param Redis $redis Redis连接对象。
	 * @param number $ttl
	 * @param string $prefix
	 * @throws \Exception
	 */
	public function __construct(&$redis, $ttl = null, $prefix = 'sess_') {
		$this->_ttl = $ttl ?: ini_get('session.gc_maxlifetime');
		$this->_client = $redis;
	}

	/**
	 * 关闭当前session。
	 * @return boolean
	 */
	public function close() {
		$this->_client->close();
		return true;
	}

	/**
	 * 
	 * @param string $session_id
	 * @return boolean
	 */
	public function destroy($session_id) {
		$this->_client->del($this->_prefix . $session_id);
		return true;
	}

	/**
	 * ssdb不需要gc清理过期的session。ssdb会自己清掉。
	 * @param int $maxlifetime
	 * @return boolean
	 */
	public function gc($maxlifetime) {
		return true;
	}

	/**
	 * @param string $save_path
	 * @param string $name
	 * @return boolean
	 */
	public function open($save_path, $name) {
		return true;
	}

	/**
	 * 读取session。
	 * @param string $session_id
	 * @return string
	 */
	public function read($session_id) {
		if (isset($this->_cache[$session_id])) {
			return $this->_cache[$session_id];
		}
		$session_data = $this->_client->get($this->_prefix . $session_id);
		return $this->_cache[$session_id] = ($session_data === null ? '' : $session_data);
	}

	/**
	 * 写session。
	 * @param string $session_id
	 * @param string $session_data
	 * @return boolean
	 */
	public function write($session_id, $session_data) {
		if (isset($this->_cache[$session_id]) && $this->_cache[$session_id] === $session_data) {
			$this->_client->expire($this->_prefix . $session_id, $this->_ttl);
		} else {
			$this->_cache[$session_id] = $session_data;
			$this->_client->setEx($this->_prefix . $session_id, $this->_ttl, $session_data);
		}
		return true;
	}
}