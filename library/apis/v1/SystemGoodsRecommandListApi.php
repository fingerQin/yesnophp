<?php
/**
 * 系统推荐商品列表接口。
 * @author winerQin
 * @date 2016-06-06
 */

namespace apis\v1;

use apis\BaseApi;
class SystemCategoryListApi extends BaseApi {

	/**
	 * 逻辑处理。
	 * @see Api::runService()
	 * @return bool
	 */
	protected function runService() {
		$this->render(0, 'ok');
	}
}