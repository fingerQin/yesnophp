<?php
/**
 * 系统分类获取。
 * @author winerQin
 * @date 2016-06-06
 */

namespace apis\v1;

use apis\BaseApi;
use services\CategoryService;
class SystemCategoryListApi extends BaseApi {

	/**
	 * 逻辑处理。
	 * @see Api::runService()
	 * @return bool
	 */
	protected function runService() {
		$position = $this->getString('cat_type');
		$count    = $this->getInt('cat_level', -1);
		$list = CategoryService::getCategoryList(0, CategoryService::CAT_NEWS);
		$this->render(0, 'ok', $list);
	}
}