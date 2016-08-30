<?php
/**
 * 运费管理。
 * @author winerQin
 * @date 2016-06-24
 */

use services\FreightService;

class FreightController extends \common\controllers\Shop {

	/**
	 * 运费模板列表。
	 */
	public function listAction() {
		$list = FreightService::getShopFreightList($this->shop_id);
		$this->_view->assign('list', $list);
	}

	/**
	 * 添加运费模板。
	 */
	public function addAction() {
		if ($this->_request->isXmlHttpRequest()) {
			$data = [
				'user_id'      => $this->user_id,
				'shop_id'      => $this->shop_id,
				'freight_name' => $this->getString('freight_name'),
				'send_time'    => $this->getInt('send_time'),
				'bear_freight' => $this->getInt('bear_freight', 0),
				'rate_step'    => $this->getInt('rate_step', 0),
				'step_freight' => $this->getInt('step_freight', 0),
				'fright_type'  => $this->getInt('fright_type'),
				'base_step'    => $this->getInt('base_step', 0),
				'base_freight' => $this->getInt('base_freight', 0),
				'no_area'      => $this->getString('no_area', ''),
				'baoyou_fee'   => $this->getInt('baoyou_fee', 0)
			];
			FreightService::addFreightTpl($data);
			$this->json(true, '添加成功');
		}
	}

	/**
	 * 编辑运费模板。
	 */
	public function editAction() {
		if ($this->_request->isXmlHttpRequest()) {
			$data = [
				'tpl_id'       => $this->getInt('tpl_id'),
				'user_id'      => $this->user_id,
				'send_time'    => $this->getInt('send_time'),
				'bear_freight' => $this->getInt('bear_freight', 0),
				'rate_step'    => $this->getInt('rate_step', 0),
				'step_freight' => $this->getInt('step_freight', 0),
				'shop_id'      => $this->shop_id,
				'freight_name' => $this->getString('freight_name'),
				'fright_type'  => $this->getInt('fright_type'),
				'base_step'    => $this->getInt('base_step', 0),
				'base_freight' => $this->getInt('base_freight', 0),
				'no_area'      => $this->getString('no_area', ''),
				'baoyou_fee'   => $this->getInt('baoyou_fee', 0)
			];
			FreightService::editFreightTpl($data);
			$this->json(true, '保存成功');
		}
		$tpl_id = $this->getInt('tpl_id');
		$detail = FreightService::getFreightTplDetail($tpl_id, $this->shop_id);
		$this->_view->assign('detail', $detail);
	}

	/**
	 * 删除运费模板。
	 */
	public function deleteAction() {
		$tpl_id = $this->getInt('tpl_id');
		$ok = FreightService::deleteFreightTpl($this->user_id, $this->shop_id, $tpl_id);
		$this->json($ok, '删除成功');
	}
}