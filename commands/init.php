<?php
/**
 * 命令执行执行时资源加载。
 * @author winerQin
 * @date 2015-11-05
 */

init(); // 启动。

function init() {
    // 配置文件注册。
    $config = \Yaf\Application::app()->getConfig();
    \Yaf\Registry::set("config", $config);

    $application_library = $config->application->library . 'functions.php';
    require($application_library);

    // \Yaf\loader::import("vendor/autoload.php");

    _initMySql();
    _initSSDB();
}


/**
 * 初始化MySQL并注册到全局环境。
 */
function _initMySql() {
	// [1] 传统初始化MySQL方式。
	$config = \Yaf\Registry::get("config");
	$mysql_host     = $config->database->mysql->host;
	$mysql_port     = $config->database->mysql->port;
	$mysql_username = $config->database->mysql->username;
	$mysql_password = $config->database->mysql->password;
	$mysql_charset  = $config->database->mysql->charset;
	$mysql_dbname   = $config->database->mysql->dbname;

	$dsn = "mysql:dbname={$mysql_dbname};host={$mysql_host};port={$mysql_port}";
	$dbh = new PDO($dsn, $mysql_username, $mysql_password);
	// MySQL操作出错，抛出异常。
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$dbh->setAttribute(PDO::ATTR_CASE, PDO::CASE_LOWER);
	$dbh->setAttribute(PDO::ATTR_ORACLE_NULLS, PDO::NULL_NATURAL);
	$dbh->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, FALSE);
	$dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, FALSE);
	// 以关联数组返回查询结果。
	$dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
	$dbh->query("SET NAMES {$mysql_charset}");
	\Yaf\Registry::set('mysql', $dbh);
}

/**
 * 初始化SSDB并注册到全局环境。
 */
function _initSSDB() {
	$config = \Yaf\Registry::get("config");
	$ssdb_host = $config->database->ssdb->host;
	$ssdb_port = $config->database->ssdb->port;
	try {
		$ssdb = new \ssdb\SimpleClient($ssdb_host, $ssdb_port);
	} catch (Exception $e){
		die(__LINE__ . ' ' . $e->getMessage());
	}
	\Yaf\Registry::set('ssdb', $ssdb);
}