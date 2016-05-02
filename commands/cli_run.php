<?php
/**
 * 命令行运行。
 * -- 1、通过在此文件可以加载所有的类库以及model。
 * -- 2、使用：直接在当前文件目录下执行命令：php cli_run.php
 * @author winerQin
 * @date 2015-01-29
 */


// $SERVER_NAME = $_SERVER['SERVER_NAME'];
$environ = 'dev';
// if ($SERVER_NAME == 'www.siwen.ren') {
//     $environ = 'product';
// }

// 微秒。
define('MICROTIME', microtime());

// -- 取当前目录名称  --
$pwd      = trim(__DIR__, DIRECTORY_SEPARATOR);
$arr_pwd  = explode(DIRECTORY_SEPARATOR, $pwd);
$app_name = array_pop($arr_pwd);
define('APP_NAME', $app_name);

define("APP_PATH",  realpath(dirname(__FILE__) . '/../')); /* 指向sites的上一级 */
$app  = new \Yaf\Application(APP_PATH . "/common/configs/application.ini", $environ);

// 必须放在 Application 与 execute 之间。否则，加载不到文件以及使用不了一些 YAF 的方法。
// 加载资源初始化文件。以此实现 web 模式下欠缺的一些功能。以后所有的注册操作可放此文件中。
\Yaf\loader::import(APP_PATH . "/commands/init.php");

$app->execute('cli_run');


/**
 * 业务区。
 */
function cli_run() {
    echo "test\n";
}