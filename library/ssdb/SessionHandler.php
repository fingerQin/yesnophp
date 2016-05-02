<?php
namespace ssdb;

class SessionHandler implements \SessionHandlerInterface {

	/**
	 * ssdb对象。
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
	 * 
	 * @param Client $ssdb
	 * @param int $ttl
	 */
	public function __construct($save_path, $ttl = null, $prefix = 'sess_') {
		$this->_ttl = $ttl ?: ini_get('session.gc_maxlifetime');
		$this->_prefix = $prefix;

		$components = parse_url($save_path);
		
		if ($components === false || 
				!isset($components['scheme'], $components['host'], $components['port']) 
				|| strtolower($components['scheme']) !== 'tcp') {
			throw new \Exception('Invalid session.save_path: ' . $save_path);
		}

		$this->_client = new Client($components['host'], $components['port']);

		if (isset($components['query'])) {
			parse_str($components['query'], $query);
			if (isset($query['auth'])) {
				$this->_client->auth($query['auth']);
			}
		}
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
		$session_data = $this->_client->get($this->_prefix . $session_id)->data;
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
			$this->_client->setx($this->_prefix . $session_id, $session_data, $this->_ttl);
		}
		return true;
	}
}
