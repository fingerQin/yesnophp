<?php
/**
 * Model基类。
 * -- 1、所有数据库操作必须只能在Model里面进行操作。
 * @author winerQin
 * @date 2015-11-03
 */

namespace models;

use common\YCore;
class DbBase {

	/**
	 * 数据库连接资源句柄。
	 * @var \PDO
	 */
	protected $link = null;

	/**
	 * 表名。
	 * @var string
	 */
	protected $_table_name = '';

	/**
	 * 在使用预处理语句时使用。
	 * -- 即建立一个只能向后的指针。
	 * @var array
	 */
	protected $prepare_attr = [\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY];
	
	/**
	 * @var 保存最后操作的PDOStatement对象。
	 */
	protected $stmt = null;

	/**
	 * 构造方法。
	 * @param string $db_options 数据库配置项。
	 * @return void
	 */
	public function __construct($db_options = 'default') {
	    $registry_name = "mysql_{$db_options}";
	    if (\Yaf\Registry::has($registry_name) === false) {
	        $this->connection($db_options);
	    }
	    $this->link = \Yaf\Registry::get($registry_name);
	}

	/**
	 * 切换数据库连接。
	 * @param string $db_options 数据库配置项。
	 * @return void
	 */
	public function changeDb($db_options) {
	    $registry_name = "mysql_{$db_options}";
	    if (\Yaf\Registry::has($registry_name) === false) {
	        $this->connection($db_options);
	    }
	    $this->link = \Yaf\Registry::get($registry_name);
	}

	/**
	 * 连接数据库。
	 * @param string $db_options 数据库配置项。
	 * @return void
	 */
	protected function connection($db_options = 'default') {
	    $registry_name = "mysql_{$db_options}";
	    // [1] 传统初始化MySQL方式。
	    $config = \Yaf\Registry::get("config");
	    $mysql_host     = $config->database->mysql->$db_options->host;
	    $mysql_port     = $config->database->mysql->$db_options->port;
	    $mysql_username = $config->database->mysql->$db_options->username;
	    $mysql_password = $config->database->mysql->$db_options->password;
	    $mysql_charset  = $config->database->mysql->$db_options->charset;
	    $mysql_dbname   = $config->database->mysql->$db_options->dbname;
	    $dsn = "mysql:dbname={$mysql_dbname};host={$mysql_host};port={$mysql_port}";
	    $dbh = new \PDO($dsn, $mysql_username, $mysql_password);

	    // MySQL操作出错，抛出异常。
	    $dbh->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
	    //$dbh->setAttribute(\PDO::ATTR_CASE, \PDO::CASE_LOWER);
	    $dbh->setAttribute(\PDO::ATTR_ORACLE_NULLS, \PDO::NULL_NATURAL);
	    $dbh->setAttribute(\PDO::ATTR_STRINGIFY_FETCHES, FALSE);
	    $dbh->setAttribute(\PDO::ATTR_EMULATE_PREPARES, FALSE);
	    // 以关联数组返回查询结果。
	    $dbh->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
	    $dbh->query("SET NAMES {$mysql_charset}");
	    \Yaf\Registry::set($registry_name, $dbh);
	}

	/**
	 * 获取表名。
	 */
	public function getTableName() {
	    return $this->_table_name;
	}

	/**
	 * 开启数据库事务。
	 */
	final public function beginTransaction() {
	    $is_active = $this->link->inTransaction();
	    if (!$is_active) {
	        $bool = $this->link->beginTransaction();
	        if (!$bool) {
	            YCore::throw_exception(-1, '开启事务失败');
	        }
	    }
	}

	/**
	 * 提交数据库事务。
	 */
	final public function commit() {
	    $is_active = $this->link->inTransaction();
	    if ($is_active) {
	        $bool = $this->link->commit();
	        if (!$bool) {
	            YCore::throw_exception(-1, '提交事务失败');
	        }
	    }
	}

	/**
	 * 回滚数据库事务。
	 */
	final public function rollBack() {
	    $is_active = $this->link->inTransaction();
	    if ($is_active) {
	        $bool = $this->link->rollBack();
	        if (!$bool) {
	            YCore::throw_exception(-1, '回滚事务失败');
	        }
	    }
	}

	/**
	 * 获取最后插入的ID。
	 * @return number
	 */
	public function lastInsertId() {
	    return $this->link->lastInsertId();
	}

	/**
	 * 执行sql查询
	 * @param array $data 需要查询的字段值[例`name`,`gender`,`birthday`]
	 * @param array $where 查询条件[例`name`='$name']
	 * @param int $limit 返回的结果条数。
	 * @param string $order 排序方式	[默认按数据库默认方式排序]
	 * @param string $group 分组方式	[默认为空]
	 * @return array 查询结果集数组
	 */
	public function fetchAll(array $data = [], array $where = [], $limit = 0, $order = '', $group = '') {
		// [1] 参数判断。
	    if (strlen($this->_table_name) === 0 || !is_string($this->_table_name)) {
			YCore::throw_exception(3002001, 'The table parameters is wrong');
		}
		if (!is_string($order)) {
			YCore::throw_exception(3001002, 'The order parameters is wrong');
		}
		if (!is_string($group)) {
			YCore::throw_exception(3001003, 'The group parameters is wrong');
		}
		if (!is_numeric($limit)) {
			YCore::throw_exception(3001004, 'The limit parameter is wrong');
		}
		// [2] where 条件生成。
		$where_condition = ' 1 AND 1 ';
		$params = [];
		foreach ($where as $column_name => $column_val) {
			$where_condition .= " AND `{$column_name}` = ? ";
			$params[] = $column_val;
		}
		$where_condition = trim($where_condition, ',');
		// [3] 要查询的列名。
		$column_condition = '';
		if (empty($data)) {
			$column_condition = ' * ';
		} else {
			foreach ($data as $column_name) {
				$column_condition .= "`{$column_name}`,";
			}
		}
		$column_condition = trim($column_condition, ',');
		// [4] GROUP BY 处理。
		$group_by = '';
		if (strlen($group) > 0) {
			$group_by = "GROUP BY {$group}";
		}
		// [5] ORDER BY 处理。
		$order_by = '';
		if (strlen($order) > 0) {
			$order_by = "ORDER BY {$order}";
		}
		if ($limit == 0) {
			$sql = "SELECT {$column_condition} FROM `{$this->_table_name}` WHERE {$where_condition} {$group_by} {$order_by}";
		} else {
			$sql = "SELECT {$column_condition} FROM `{$this->_table_name}` WHERE {$where_condition} {$group_by} {$order_by} LIMIT {$limit}";
		}
		$sth = $this->link->prepare($sql);
		$sth->execute($params);
		$data = $sth->fetchAll(\PDO::FETCH_ASSOC);
		return $data ? $data : [];
	}

	/**
	 * 获取单条记录查询
	 * @param array $data 需要查询的字段值。['username', 'sex', 'mobilephone']
	 * @param array $where 查询条件
	 * @param string $order 排序方式	[默认按数据库默认方式排序]
	 * @param string $group 分组方式	[默认为空]
	 * @return array 数据查询结果集,如果不存在，则返回空数组。
	 */
	public function fetchOne(array $data, array $where, $order = '', $group = '') {
		// [1] 参数判断。
	    if (strlen($this->_table_name) === 0 || !is_string($this->_table_name)) {
			YCore::throw_exception(3002001, 'The table parameters is wrong');
		}
		if (empty($where)) {
			YCore::throw_exception(3001002, 'The where parameters is wrong');
		}
		if (!is_string($order)) {
			YCore::throw_exception(3001003, 'The order parameters is wrong');
		}
		if (!is_string($group)) {
			YCore::throw_exception(3001004, 'The group parameters is wrong');
		}
		// [2] where 条件生成。
		$where_condition = ' 1 = 1 ';
		$params = [];
		foreach ($where as $column_name => $column_val) {
			$where_condition .= " AND `{$column_name}` = ? ";
			$params[] = $column_val;
		}
		$where_condition = trim($where_condition, ',');
		// [3] 要查询的列名。
		$column_condition = '';
		if (empty($data)) {
			$column_condition = ' * ';
		} else {
			foreach ($data as $column_name) {
				$column_condition .= "`{$column_name}`,";
			}
		}
		$column_condition = trim($column_condition, ',');
		// [4] GROUP BY 处理。
		$group_by = '';
		if (strlen($group) > 0) {
			$group_by = "GROUP BY {$group}";
		}
		// [5] ORDER BY 处理。
		$order_by = '';
		if (strlen($order) > 0) {
			$order_by = "ORDER BY {$order}";
		}
		$sql = "SELECT {$column_condition} FROM `{$this->_table_name}` WHERE {$where_condition} {$group_by} {$order_by} LIMIT 1";
		$sth = $this->link->prepare($sql);
		$sth->execute($params);
		$data = $sth->fetch(\PDO::FETCH_ASSOC);
		return $data ? $data : [];
	}

	/**
	 * 获取记录条数。
	 * @param array $where 查询条件
	 * @return number
	 */
	public function count(array $where) {
	    // [1] 参数判断。
	    if (strlen($this->_table_name) === 0 || !is_string($this->_table_name)) {
			YCore::throw_exception(3002001, 'The table parameters is wrong');
		}
	    if (empty($where)) {
	        YCore::throw_exception(3001002, 'The where parameters is wrong');
	    }
	    // [2] where 条件生成。
	    $where_condition = ' 1 = 1 ';
	    $params = [];
	    foreach ($where as $column_name => $column_val) {
	        $where_condition .= " AND `{$column_name}` = ? ";
	        $params[] = $column_val;
	    }
	    $where_condition = trim($where_condition, ',');
	    // [3] 要查询的列名。
	    $column_condition = 'COUNT(1) AS count';
	    $sql = "SELECT {$column_condition} FROM `{$this->_table_name}` WHERE {$where_condition} LIMIT 1";
	    $sth = $this->link->prepare($sql);
	    $sth->execute($params);
	    $data = $sth->fetch(\PDO::FETCH_ASSOC);
	    return $data ? intval($data['count']) : 0;
	}

	/**
	 * 执行添加记录操作
	 * @param array $data 要增加的数据，参数为数组。数组key为字段值，数组值为数据取值
	 * @return number 大于0为主键id，等于0为添加失败。
	 */
	public function insert(array $data) {
		if (strlen($this->_table_name) === 0 || !is_string($this->_table_name)) {
			YCore::throw_exception(3002001, 'The table parameters is wrong');
		}
		if (empty($data)) {
			YCore::throw_exception(3002002, 'The data parameter can\'t be empty');
		}
		$column_condition = '';
		$column_question  = '';
		$params = [];
		foreach ($data as $column_name => $column_val) {
			$column_condition .= "`{$column_name}`,";
			$column_question  .= "?,";
			$params[] = $column_val;
		}
		$column_condition = trim($column_condition, ',');
		$column_question  = trim($column_question, ',');
		$sql = "INSERT INTO `{$this->_table_name}` ($column_condition) VALUES($column_question) ";
		$sth = $this->link->prepare($sql);
		$ok = $sth->execute($params);
		unset($column_condition, $column_question, $params);
		return $ok ? $this->link->lastInsertId() : 0;
	}

	/**
	 * 执行更新记录操作。
	 * @param array $data 要更新的数据内容。
	 * @param array $where 更新数据时的条件。必须有条件。避免整表更新。
	 * @return boolean
	 */
	public function update(array $data, array $where) {
		// [1] 参数判断。
	   if (strlen($this->_table_name) === 0 || !is_string($this->_table_name)) {
			YCore::throw_exception(3002001, 'The table parameters is wrong');
		}
		if (empty($where)) {
			YCore::throw_exception(3001001, 'The where parameters is wrong');
		}
		if (empty($data)) {
			YCore::throw_exception(3001004, 'The data parameter cannot be empty');
		}
		// [2] SET 条件生成。
		$set_condition = '';
		$params = [];
		foreach ($data as $column_name => $column_val) {
			$set_condition .= "`{$column_name}` = ?,";
			$params[] = $column_val;
		}
		$set_condition = trim($set_condition, ',');
		// [3] where 条件生成。
		$where_condition = '';
		foreach ($where as $column_name => $column_val) {
			$where_condition .= "`{$column_name}` = ? AND";
			$params[] = $column_val;
		}
		$where_condition = trim($where_condition, 'AND');
		$sql = "UPDATE `{$this->_table_name}` SET {$set_condition} WHERE {$where_condition} ";
		$sth = $this->link->prepare($sql);
		$ok = $sth->execute($params);
		unset($params, $set_condition, $where_condition);
		return $ok ? true : false;
	}

	/**
	 * 执行删除记录操作。
	 * @param array $where 删除数据条件,不充许为空。
	 * @return boolean
	 */
	public function delete(array $where) {
	   if (strlen($this->_table_name) === 0 || !is_string($this->_table_name)) {
			YCore::throw_exception(3002001, 'The table parameters is wrong');
		}
		if (empty($where)) {
			YCore::throw_exception(3001001, 'The where parameters is wrong');
		}
		$sql = "DELETE FROM `{$this->_table_name}` WHERE 1 = 1 ";
		$params = [];
		foreach ($where as $column_name => $column_val) {
			$sql .= " AND {$column_name} = ? ";
			$params[] = $column_val;
		}
		$sth = $this->link->prepare($sql);
		$sth->execute($params);
		$affected_row = $sth->rowCount();
		return $affected_row > 0 ? true : false;
	}

	/**
	 * 生成SQL语句中where IN条件部分的预处理参数。
	 * @param array $params 值。示例：[1, 2, 3, 4, 5]
	 * @return array
	 */
	public function createWhereIn($params) {
		if (empty($params)) {
			YCore::throw_exception(3001101, 'where in params 有误');
		}
		$question = '';
		$values   = [];
		$index    = 0;
		foreach ($params as $param) {
			$in_column = ":in_column_{$index}";
			$values[$in_column] = $param;
			$question .= "{$in_column},";
			$index++;
		}
		return [
				'question' => trim($question, ','),
				'values'   => $values
		];
	}

	/**
	 * 计算并返回每页的offset.
	 * @param number $page 页码。
	 * @param number $count 每页显示记录条数。
	 * @return number
	 */
	public function getPaginationOffset($page, $count) {
	    $count = ($count <= 0) ? 10 : $count;
	    $page  = ($page <= 0) ? 1 : $page;
	    return ($page == 1) ? 0 : (($page - 1) * $count);
	}
	
	/**
	 * 计算是否有下一页。
	 * @param number $total 总条数。
	 * @param number $page 当前页。
	 * @param number $count 每页显示多少条。
	 * @return bool
	 */
	public function isHasNextPage($total, $page, $count) {
	    if (!$total || !$count) {
	        return false;
	    }
	    $total_page = ceil($total / $count);
	    if (!$total_page) {
	        return false;
	    }
	    if ($total_page <= $page) {
	        return false;
	    }
	    return true;
	}

	/**
	 * 原生SQL查询。
	 * @param string $sql 查询SQL。
	 * @param array $params 绑定参数。
	 * @return \models\Base
	 */
	public function rawQuery($sql, $params = []) {
	    $this->stmt = $this->link->prepare($sql);
	    $this->stmt->execute($params);
	    return $this;
	}

	/**
	 * 更新、删除、添加。
	 * @param string $sql 查询SQL。
	 * @param array $params 绑定参数。
	 * @param boolean $get_insert_id 获取插入时的ID。只有INSERT语句才会有。
	 * @return boolean|int
	 */
	public function rawExec($sql, $params = [], $get_insert_id = false) {
	    $sth = $this->link->prepare($sql);
	    $sth->execute($params);
	    if ($get_insert_id) {
	        return $this->lastInsertId();
	    } else {
	        $affected_row = $sth->rowCount();
	        return $affected_row > 0 ? true : false;
	    }
	}

	/**
	 * 获取单行结果。
	 * @return array
	 */
	public function rawFetchOne() {
	    if (empty($this->stmt)) {
	        YCore::throw_exception(-1, '请正确使用rawFetchOne()方法');
	    }
	    $result = $this->stmt->fetch(\PDO::FETCH_ASSOC);
	    return $result ? $result : [];
	}

	/**
	 * 获取全部结果。
	 * @return array
	 */
	public function rawFetchAll() {
	    if (empty($this->stmt)) {
	        YCore::throw_exception(-1, '请正确使用rawFetchAll()方法');
	    }
	    $result = $this->stmt->fetchAll(\PDO::FETCH_ASSOC);
	    return $result ? $result : [];
	}
}