<?php
/**
 * 系统广告接口。
 * @author winerQin
 * @date 2016-05-27
 * @version 1.0
 */

namespace apis\v1;

use apis\BaseApi;
use services\AdService;

class SystemAdListApi extends BaseApi {

	/**
	 * 逻辑处理。
	 * @see Api::runService()
	 * @return bool
	 */
	protected function runService() {
		$position = $this->getString('position');
		$count    = $this->getInt('count');
		$list = AdService::getPositionAdList($position, $count);
		$this->render(0, 'ok', $list);
	}
}