<?php
/**
 * 店铺管理。
 * @author winerQin
 * @date 2016-06-07
 */
 
use services\ShopService;

class ShopController extends \common\controllers\Shop {

	/**
	 * 店铺基本设置。
	 */
	public function baseInfoAction() {
		if ($this->_request->isXmlHttpRequest()) {
			$shop_name   = $this->getString('shop_name');
			$shop_notice = $this->getString('shop_notice');
			$shop_logo   = $this->getString('shop_logo');
			$link_man    = $this->getString('link_man');
			$mobilephone = $this->getString('mobilephone');
			$telephone   = $this->getString('telephone');
			$qq          = $this->getString('qq');
			ShopService::setBaseInfo($this->user_id, $this->shop_id, $shop_name, $shop_logo, $shop_notice, $link_man, $mobilephone, $telephone, $qq);
			$this->json(true, '保存成功');
		}
		$detail = ShopService::getShopDetail($this->shop_id);
		$this->_view->assign('detail', $detail);
	}
}