<?php
/**
 * 商家商品自定义分类。
 * @author winerQin
 * @date 2016-06-15
 */

use services\ShopService;

class CategoryController extends \common\controllers\Shop {

	/**
	 * 自定义分类列表。
	 */
	public function listAction() {
		$list = ShopService::getGoodsCategoryList($this->shop_id);
		$this->_view->assign('list', $list);
	}

	/**
	 * 添加自定义分类。
	 */
	public function addAction() {
		if ($this->_request->isXmlHttpRequest()) {
			$cat_name  = $this->getString('cat_name');
			$listorder = $this->getInt('listorder', 30);
			$status = ShopService::addGoodsCategory($this->user_id, $this->shop_id, $cat_name, $listorder);
			$this->json($status, '操作成功');
		}
	}

	/**
	 * 编辑自定义分类。
	 */
	public function editAction() {
		if ($this->_request->isXmlHttpRequest()) {
			$cat_id    = $this->getInt('cat_id');
			$cat_name  = $this->getString('cat_name');
			$listorder = $this->getInt('listorder', 30);
			$status = ShopService::editGoodsCategory($this->user_id, $cat_id, $this->shop_id, $cat_name, $listorder);
			$this->json($status, '操作成功');
		}
		$cat_id = $this->getInt('cat_id');
		$detail = ShopService::getGoodsCategoryDetail($this->shop_id, $cat_id);
		$this->_view->assign('detail', $detail);
	}

	/**
	 * 删除自定义分类。
	 */
	public function deleteAction() {
		$cat_id = $this->getInt('cat_id');
		$status = ShopService::deleteGoodsCategory($this->user_id, $this->shop_id, $cat_id);
		$this->json($status, '删除成功');
	}

	/**
	 * 商家商品自定义分类排序。
	 */
	public function sortAction() {
	    if ($this->_request->isPost()) {
	        $listorders = $this->getGP('listorders');
	        $ok = ShopService::sort($this->user_id, $this->shop_id, $listorders);
	        if ($ok) {
	            $this->json($ok, '排序成功');
	        } else {
	            $this->json($ok, '排序失败');
	        }
	    }
	    $this->end();
	}
}