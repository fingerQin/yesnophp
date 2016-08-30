<?php
/**
 * 一元购相关业务封装。
 * @author winerQin
 * @date 2016-06-28
 */

namespace services;
use common\YCore;
use models\GmYygImage;
use models\DbBase;
use winer\Validator;
use models\GmYyg;
use common\YUrl;
use models\GmYygQh;
use models\GmYygHistory;
class YygService extends BaseService {

	/**
	 * 添加一元购活动。
	 * -- Example start --
	 * $data = [
	 * 		'yyg_name'     => '一元购活动名称',
	 * 		'yyg_desc'     => '一元购活动介绍',
	 *		'yyg_price'    => '一元购价格',
	 *		'yyg_richtext' => '一元购图文详情',
	 *		'yyg_image'    => '一元购活动相册',
	 *		'listorder'    => '排序值',
	 *		'admin_id'     => '管理员ID',
	 * ];
	 * 
	 * $yyg_image = [
	 * 		'images/voucher/20160401/56fe70362ef7e.jpg',
     *      'images/voucher/20160401/56fe705fd37a2.jpg',
     *      'images/voucher/20160401/56fe710513c9e.jpg',
     *      'images/voucher/20160402/56fea2043dc01.jpg',
     *      'images/voucher/20160402/56fea3f18677d.jpg'
	 * ];
	 * -- Example end --
	 * @param array $data 活动数据。
	 * @return boolean
	 */
	public static function addYyg($data) {
		if (empty($data['yyg_image'])) {
			YCore::exception(-1, '相册图片不能为空');
		}
		$admin_id  = $data['admin_id'];
		$yyg_image = $data['yyg_image'];
		unset($data['yyg_image'], $data['admin_id']);
		$rules = [
			'yyg_name'     => '活动名称|require:1000000|len:1000000:1:50:1',
			'yyg_desc'     => '活动介绍|require:1000000|len:1000000:1:250:1',
			'yyg_price'    => '价格|require:1000000|integer:1000000|number_between:1000000:1:10000',
			'yyg_richtext' => '图文详情|require:1000000|len:1000000:1:1000000:1',
			'listorder'    => '排序|require:1000000|integer:1000000|number_between:1000000:0:10000'
		];
		Validator::valido($data, $rules);
		$default_db = new DbBase();
		$yyg_model = new GmYyg();
		$data['yyg_image_url'] = $data['yyg_image'][0];
		$data['created_time']  = $_SERVER['REQUEST_TIME'];
		$data['created_by']    = $admin_id;
		$data['status']        = 1;
		$data['yyg_start']     = 0;
		$default_db->beginTransaction();
		$yyg_id = $yyg_model->insert($data);
		try {
			self::setYygImage($admin_id, $yyg_id, $yyg_image);
		} catch (\Exception $e) {
			$default_db->rollBack();
			YCore::exception($e->getCode(), $e->getMessage());
		}
		$default_db->commit();
		return true;
	}

	/**
	 * 添加一元购活动。
	 * -- Example start --
	 * $data = [
	 * 		'yyg_id'       => '一元购活动ID',
	 * 		'yyg_name'     => '一元购活动名称',
	 * 		'yyg_desc'     => '一元购活动介绍',
	 *		'yyg_price'    => '一元购价格',
	 *		'yyg_richtext' => '一元购图文详情',
	 *		'yyg_image'    => '一元购活动相册',
	 *		'listorder'    => '排序值',
	 *		'admin_id'     => '管理员ID',
	 * ];
	 *
	 * $yyg_image = [
	 * 		'images/voucher/20160401/56fe70362ef7e.jpg',
	 *      'images/voucher/20160401/56fe705fd37a2.jpg',
	 *      'images/voucher/20160401/56fe710513c9e.jpg',
	 *      'images/voucher/20160402/56fea2043dc01.jpg',
	 *      'images/voucher/20160402/56fea3f18677d.jpg'
	 * ];
	 * -- Example end --
	 * @param array $data 活动数据。
	 * @return boolean
	 */
	public static function editYyg($data) {
		if (empty($data['yyg_image'])) {
			YCore::exception(-1, '相册图片不能为空');
		}
		$admin_id  = $data['admin_id'];
		$yyg_image = $data['yyg_image'];
		unset($data['yyg_image'], $data['admin_id']);
		$rules = [
			'yyg_id'       => '活动ID|require:1000000|integer:1000000',
			'yyg_name'     => '活动名称|require:1000000|len:1000000:1:50:1',
			'yyg_desc'     => '活动介绍|require:1000000|len:1000000:1:250:1',
			'yyg_price'    => '价格|require:1000000|integer:1000000|number_between:1000000:1:10000',
			'yyg_richtext' => '图文详情|require:1000000|len:1000000:1:1000000:1',
			'listorder'    => '排序|require:1000000|integer:1000000|number_between:1000000:0:10000'
		];
		Validator::valido($data, $rules);
		$default_db = new DbBase();
		$yyg_model  = new GmYyg();
		$where = [
			'yyg_id' => $data['yyg_id'],
			'status' => 1
		];
		$yyg_detail = $yyg_model->fetchOne([], $where);
		if (empty($yyg_detail)) {
			YCore::exception(-1, '活动不存在或已经删除');
		}
		$data['yyg_image_url'] = $data['yyg_image'][0];
		$data['created_time']  = $_SERVER['REQUEST_TIME'];
		$data['created_by']    = $admin_id;
		$default_db->beginTransaction();
		$ok = $yyg_model->update($data, $where);
		if (!$ok) {
			$default_db->rollBack();
			YCore::exception(-1, '保存失败');
		}
		try {
			self::setYygImage($admin_id, $data['yyg_id'], $yyg_image);
		} catch (\Exception $e) {
			$default_db->rollBack();
			YCore::exception($e->getCode(), $e->getMessage());
		}
		$default_db->commit();
		return true;
	}

	/**
	 * 删除一元购活动。
	 * @param number $admin_id 管理员ID。
	 * @param number $yyg_id 活动ID。
	 * @return boolean
	 */
	public static function deleteYyg($admin_id, $yyg_id) {
		$where = [
			'yyg_id' => $yyg_id,
			'status' => 1
		];
		$yyg_model  = new GmYyg();
		$yyg_detail = $yyg_model->fetchOne([], $where);
		if (empty($yyg_detail)) {
			YCore::exception(-1, '活动不存在或已经删除');
		}
		if ($yyg_detail['yyg_start'] == 1) {
			YCore::exception(-1, '只能删除未开始的活动');
		}
		$updata = [
			'status'        => 2,
			'modified_by'   => $admin_id,
			'modified_time' => $_SERVER['REQUEST_TIME']
		];
		$ok = $yyg_model->update($updata, $where);
		if (!$ok) {
			YCore::exception(-1, '删除失败');
		}
		return true;
	}

	/**
	 * 获取正在进行中的活动列表。
	 * @param number $page 当前页码。
	 * @param number $count 每页显示条数。
	 * @return array
	 */
	public static function getStartingYygList($page, $count) {
		$db_model = new DbBase();
		$offset   = self::getPaginationOffset($page, $count);
		$table    = ' FROM gm_yyg ';
		$columns  = ' yyg_id,yyg_name,yyg_image_url,yyg_price ';
		$where    = ' WHERE status = :status AND yyg_start = :yyg_start ';
		$order_by = ' ORDER BY listorder ASC ';
		$params   = [
			':status'    => 1,
			':yyg_start' => 1
		];
		$sql = "SELECT COUNT(1) AS count {$table} {$where}";
		$count_data = $db_model->rawQuery($sql, $params)->rawFetchOne();
		$total  = $count_data ? $count_data['count'] : 0;
		$sql    = "SELECT {$columns} {$table} {$where} {$order_by} LIMIT {$offset},{$count}";
		$list   = $db_model->rawQuery($sql, $params)->rawFetchAll();
		foreach ($list as $k => $item) {
			$item['yyg_image_url'] = YUrl::filePath($item['yyg_image_url']);
			$list[$k] = $item;
		}
		$result = array(
			'list'   => $list,
			'total'  => $total,
			'page'   => $page,
			'count'  => $count,
			'isnext' => self::IsHasNextPage($total, $page, $count),
		);
		return $result;
	}

	/**
	 * 改变活动开启状态。
	 * @param number $admin_id 管理员ID。
	 * @param number $yyg_id 一元购活动ID。
	 * @param number $start_status 开启状态：1开启、0关闭。
	 * @return boolea
	 */
	public static function changeYygStartStatus($admin_id, $yyg_id, $start_status) {
		$where = [
			'yyg_id' => $yyg_id,
			'status' => 1
		];
		$default_db = new DbBase();
		$yyg_model  = new GmYyg();
		$yyg_detail = $yyg_model->fetchOne([], $where);
		if (empty($yyg_detail)) {
			YCore::exception(-1, '活动不存在或已经删除');
		}
		if ($start_status == 1) {
			if ($yyg_detail['yyg_start'] == 1) {
				YCore::exception(-1, '活动已经处于开启状态');
			}
			$where = [
				'qh_number' => $yyg_detail['qh_number'],
				'yyg_id'    => $yyg_id
			];
			$yyg_qh_model = new GmYygQh();
			$qh_detail = $yyg_qh_model->fetchOne([], $where);
			if (empty($qh_detail)) {
				YCore::exception(-1, '数据异常,请联系开发人员');
			}
			$default_db->beginTransaction();
			if ($qh_detail['winner_id'] != 0) { // 说明已经结束了。
				$qh_number = $yyg_detail['qh_number'] + 1;
				$data = [
					'yyg_id'       => $yyg_id,
					'created_time' => $_SERVER['REQUEST_TIME'],
					'qh_number'    => $qh_number
				];
				$qh_id = $yyg_qh_model->insert($data);
				if ($qh_id == 0) {
					$default_db->rollBack();
					YCore::exception(-1, '开启失败');
				}
			}
			$updata = [
				'modified_time' => $_SERVER['REQUEST_TIME'],
				'modified_by'   => $admin_id,
				'yyg_start'     => 1
			];
			$ok = $yyg_model->update($updata, $where);
			if (!$ok) {
				$default_db->rollBack();
				YCore::exception(-1, '开启失败');
			}
			$default_db->commit();
		} else {
			if ($yyg_detail['yyg_start'] == 0) {
				YCore::exception(-1, '活动已经处于关闭状态');
			}
			$updata = [
				'modified_time' => $_SERVER['REQUEST_TIME'],
				'modified_by'   => $admin_id,
				'yyg_start'     => 0
			];
			$ok = $yyg_model->update($updata, $where);
			if (!$ok) {
				$default_db->rollBack();
				YCore::exception(-1, '关闭失败');
			}
			$default_db->commit();
		}
		return true;
	}

	/**
	 * 用户参与一元购。
	 * @param number $user_id 用户ID。
	 * @param number $yyg_id 一元购ID。
	 * @param number $qh_number 期号。
	 * @param number $do_times 参与次数。
	 * @return boolean
	 */
	public static function joinYyg($user_id, $yyg_id, $qh_number, $do_times) {
		$where = [
			'yyg_id' => $yyg_id,
			'status' => 1
		];
		$yyg_model  = new GmYyg();
		$yyg_detail = $yyg_model->fetchOne([], $where);
		if (empty($yyg_detail)) {
			YCore::exception(-1, '活动不存在或已经删除');
		}
		if ($yyg_detail['yyg_start'] != 1){
			YCore::exception(-1, '活动已经关闭');
		}
		if ($yyg_detail['qh_number'] != $qh_number) {
			YCore::exception(-1, '活动已经结束');
		}
		$data = [
			'yyg_id'       => $yyg_id,
			'qh_number'    => $qh_number,
			'user_id'      => $user_id,
			'do_times'     => $do_times,
			'created_time' => $_SERVER['REQUEST_TIME']
		];
		$yyg_history_model = new GmYygHistory();
		$id = $yyg_history_model->insert($data);
		if ($id == 0) {
			YCore::exception(-1, '服务器异常');
		}
		return true;
	}

	/**
	 * 获取活动详情。
	 * @param number $yyg_id 活动ID。
	 * @return array
	 */
	public static function getYygDetail($yyg_id) {
		$where = [
			'yyg_id' => $yyg_id,
			'status' => 1
		];
		$columns = [
			'yyg_id', 'qh_id', 'yyg_name', 'yyg_image_url', 'yyg_desc', 
			'yyg_price', 'yyg_richtext', 'yyg_start'
		];
		$yyg_model  = new GmYyg();
		$yyg_detail = $yyg_model->fetchOne($columns, $where);
		if (empty($yyg_detail)) {
			YCore::exception(-1, '活动不存在或已经删除');
		}
		$yyg_detail['yyg_image_url'] = YUrl::filePath($yyg_detail['yyg_image_url']);
		return $yyg_detail;
	}

	/**
	 * 设置一元购相册。
	 * @param number $admin_id 添加相册的管理员ID。
	 * @param number $yyg_id 一元购活动ID。
	 * @param array $album 相册。
	 * @return boolean
	 */
	protected static function setYygImage($admin_id, $yyg_id, array $album) {
		$image_model = new GmYygImage();
		// [1] 查找该一元购活动原相册图片。
		$where = [
			'yyg_id' => $yyg_id,
			'status' => 1
		];
		$old_image  = $image_model->fetchOne([], $where);
		$_old_image = [];
		foreach ($old_image as $item) {
			$_old_image[$item['image_id']] = $item['image_url'];
		}
		$old_image = $_old_image;

		// [2] 判断新入库的图片是否已经存在，已经存在则不做任何修改。
		// 如果旧图片在新图片中不存在，则要进行删除。
		$exists_old_image_id = [];
		foreach ($album as $image_url) {
			if (!empty($old_image) && in_array($image_url, $old_image)) { // 存在。
				$exists_old_image_id[] = array_search($image_url, $old_image);
			} else { // 不存在。
				$insert_data = [
					'yyg_id'       => $yyg_id,
					'image_url'    => $image_url,
					'status'       => 1,
					'created_time' => $_SERVER['REQUEST_TIME'],
					'created_by'   => $admin_id
				];
				$id = $image_model->insert($insert_data);
				if ($id == 0) {
					YCore::exception(-1, '相册图片保存失败');
				}
			}
		}
		// [3] 得到不在新图片中的旧图片ID。
		$not_exist_image_id = [];
		foreach ($old_image as $item) {
			if (!in_array($item['image_id'], $exists_old_image_id)) {
				$not_exist_image_id[] = $item['image_id'];
			}
		}
		// [4] 删除不在新图片中的旧图片ID对应的图片。
		if (!empty($not_exist_image_id)) {
			$default_db = new DbBase();
			$result = $default_db->createWhereIn($not_exist_image_id);
			$sql = "UPDATE gm_yyg_image SET status = :status, modified_by = :modified_by, modified_time = :modified_time "
				 . "WHERE image_id IN ({$result['question']})";
			$params = $result['values'];
			$params[':status']        = 2;
			$params[':modified_by']   = $admin_id;
			$params[':modified_time'] = $_SERVER['REQUEST_TIME'];
			$ok = $default_db->rawExec($sql, $params);
			if (!$ok) {
				YCore::exception(-1, '相册图片保存失败');
			}
		}
		return true;
	}
}