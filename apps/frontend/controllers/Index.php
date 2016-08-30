<?php
use common\YCore;
/**
 * 商城首页。
 * @author winerQin
 * @date 2015-01-28
 */

class IndexController extends \common\controllers\Common {

	/**
	 * 商城首页。
	 */
	public function indexAction() {
		$this->end();
		$code = $this->getString('code', '');
		$create_home_page_code = YCore::appconfig('create.home.page.code');
		if ($code != $create_home_page_code) {
			header('HTTP/1.1 301 Moved Permanently');
			header('Location:./index.html');
		} else {
			$tpl_path = APP_VIEW_PATH . '/index/index.php';
			$html = $this->_view->render($tpl_path);
			$index_html = APP_SITE_PATH . DIRECTORY_SEPARATOR . 'index.html';
			file_put_contents($index_html, $html);
			echo 'ok';
		}
	}
}