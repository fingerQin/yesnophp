<?php
/**
 * 广告管理。
 * @author winerQin
 * @date 2016-03-30
 */
namespace services;

use models\Ad;
use common\YCore;
use models\AdPosition;
use winer\Validator;
use models\DbBase;
use common\YUrl;

class AdService extends BaseService {

    /**
     * 获取指定位置的广告。
     *
     * @param string $pos_code 广告位置编码。
     * @return array
     */
    public static function getPositionAdList($pos_code) {
        $where = [
            'pos_code' => $pos_code,
            'status'   => 1
        ];
        $ad_position = new AdPosition();
        $position_detail = $ad_position->fetchOne([], $where);
        if (empty($position_detail)) {
            YCore::exception(- 1, '无效的广告位置编码');
        }
        $sql = "SELECT ad_name,ad_image_url,ad_url FROM ms_ad WHERE pos_id = :pos_id "
             . "AND start_time <= :start_time AND end_time >= :end_time "
             . "AND status = :status AND display = :display ORDER BY listorder ASC, "
             . "ad_id ASC LIMIT {$position_detail['pos_ad_count']}";
        $params = [
            ':pos_id'     => $position_detail['pos_id'],
            ':display'    => 1,
            ':status'     => 1,
            ':start_time' => $_SERVER['REQUEST_TIME'],
            ':end_time'   => $_SERVER['REQUEST_TIME']
        ];
        $default_db = new DbBase();
        $list = $default_db->rawQuery($sql, $params)->rawFetchAll();
        foreach ($list as $k => $v) {
            $v['ad_image_url'] = YUrl::filePath($v['ad_image_url']);
            $list[$k] = $v;
        }
        return $list;
    }

    /**
     * 获取广告位置列表。
     *
     * @param string $keywords 查询关键词。模糊搜索广告名称和广告编码。
     * @param number $page 当前页码。
     * @param number $count 每页显示条数。
     * @return array
     */
    public static function getAdPostionList($keywords = '', $page = 1, $count = 20) {
        $ad_postion_model = new AdPosition();
        return $ad_postion_model->getList($keywords, $page, $count);
    }

    /**
     * 获取广告位置详情。
     *
     * @param number $pos_id 广告位置ID。
     * @return array
     */
    public static function getAdPostionDetail($pos_id) {
        $ad_postion_model = new AdPosition();
        $data = $ad_postion_model->fetchOne([], ['pos_id' => $pos_id, 'status' => 1]);
        if (empty($data)) {
            YCore::exception(- 1, '广告位置不存在或已经删除');
        }
        return $data;
    }

    /**
     * 添加广告位置。
     *
     * @param number $admin_id 管理员ID。
     * @param string $pos_name 广告位置名称。
     * @param string $pos_code 广告位置编码。
     * @param number $pos_ad_count 广告位允许展示的广告数量。
     * @return boolean
     */
    public static function addAdPostion($admin_id, $pos_name, $pos_code, $pos_ad_count) {
        $ad_pos_model  = new AdPosition();
        $ad_pos_detail = $ad_pos_model->fetchOne([], ['pos_code' => $pos_code, 'status' => 1]);
        if ($ad_pos_detail) {
            YCore::exception(- 1, '广告编码已经存在请更换');
        }
        $data = [
            'pos_name'     => $pos_name,
            'pos_code'     => $pos_code,
            'pos_ad_count' => $pos_ad_count
        ];
        $rules = [
            'pos_name'     => '广告位置名称|require:1000000|len:1000000:1:50:1',
            'pos_code'     => '广告编码|require:1000000|len:1000000:1:50:1|alpha_dash:1000000',
            'pos_ad_count' => '广告位广告展示数量|require:1000000|integer:1000000'
        ];
        Validator::valido($data, $rules);
        $data['status']       = 1;
        $data['created_by']   = $admin_id;
        $data['created_time'] = $_SERVER['REQUEST_TIME'];
        return $ad_pos_model->insert($data);
    }

    /**
     * 编辑广告位置。
     *
     * @param number $admin_id 管理员ID。
     * @param number $pos_id 广告位ID。
     * @param string $pos_name 广告位置名称。
     * @param string $pos_code 广告位置编码。
     * @param number $pos_ad_count 广告位允许展示的广告数量。
     * @return boolean
     */
    public static function editAdPostion($admin_id, $pos_id, $pos_name, $pos_code, $pos_ad_count) {
        $ad_pos_model = new AdPosition();
        $where = [
            'pos_id' => $pos_id,
            'status' => 1
        ];
        $ad_pos_detail = $ad_pos_model->fetchOne([], $where);
        if (empty($ad_pos_detail)) {
            YCore::exception(- 1, '广告位置不存在或已经删除');
        }
        $ad_pos_detail = $ad_pos_model->fetchOne([], [
            'pos_code' => $pos_code,
            'status'   => 1
        ]);
        if ($ad_pos_detail && $ad_pos_detail['pos_id'] != $pos_id) {
            YCore::exception(- 1, '广告编码已经被占用请更换');
        }
        $data = [
            'pos_name'     => $pos_name,
            'pos_code'     => $pos_code,
            'pos_ad_count' => $pos_ad_count
        ];
        $rules = [
            'pos_name'     => '广告位置名称|require:1000000|len:1000000:1:50:1',
            'pos_code'     => '广告编码|require:1000000|len:1000000:1:50:1|alpha_dash:1000000',
            'pos_ad_count' => '广告位广告展示数量|require:1000000|integer:1000000'
        ];
        Validator::valido($data, $rules);
        $data['modified_by']   = $admin_id;
        $data['modified_time'] = $_SERVER['REQUEST_TIME'];
        return $ad_pos_model->update($data, $where);
    }

    /**
     * 删除广告位置。
     *
     * @param number $admin_id 管理员ID。
     * @param number $pos_id 广告位置ID。
     * @return boolean
     */
    public static function deleteAdPostion($admin_id, $pos_id) {
        $ad_position_model = new AdPosition();
        $ad_detail = $ad_position_model->fetchOne([], ['pos_id' => $pos_id, 'status' => 1]);
        if (empty($ad_detail)) {
            YCore::exception(- 1, '广告位置不存在或已经删除');
        }
        $ad_model = new Ad();
        $ad_count = $ad_model->count(['pos_id' => $pos_id, 'status' => 1]);
        if ($ad_count > 0) {
            YCore::exception(- 1, '请先清空该广告位置下的广告');
        }
        $data = [
            'status'        => 2,
            'modified_by'   => $admin_id,
            'modified_time' => $_SERVER['REQUEST_TIME']
        ];
        $where = [
            'pos_id' => $pos_id,
            'status' => 1
        ];
        return $ad_position_model->update($data, $where);
    }

    /**
     * 获取指定位置的广告列表。
     *
     * @param number $pos_id 广告位置ID。
     * @param string $ad_name 广告名称。模糊搜索广告名称。
     * @param number $display 显示状态：1是、0否。
     * @param number $page 当前页码。
     * @param number $count 每页显示记录条数。
     * @return array
     */
    public static function getAdList($pos_id, $ad_name = '', $display = -1, $page = 1, $count = 20) {
        $ad_model = new Ad();
        return $ad_model->getList($pos_id, $ad_name, $display, $page, $count);
    }

    /**
     * 获取广告详情。
     *
     * @param number $ad_id 广告ID。
     * @return array
     */
    public static function getAdDetail($ad_id) {
        $ad_model = new Ad();
        $data = $ad_model->fetchOne([], ['ad_id' => $ad_id, 'status' => 1]);
        if (empty($data)) {
            YCore::exception(- 1, '广告不存在或已经删除');
        }
        return $data;
    }

    /**
     * 添加广告。
     *
     * @param number $admin_id 管理员ID。
     * @param number $pos_id 广告位置ID。
     * @param string $ad_name 广告名称。
     * @param string $start_time 广告生效时间。
     * @param string $end_time 广告失效时间。
     * @param number $display 显示状态：1显示、0隐藏。
     * @param string $remark 广告备注。
     * @param string $ad_image_url 广告图片。
     * @param string $ad_url 广告URL。
     * @return boolean
     */
    public static function addAd($admin_id, $pos_id, $ad_name, $start_time, $end_time, $display, $remark, $ad_image_url, $ad_url) {
        $ad_position_model = new AdPosition();
        $ad_pos_detail = $ad_position_model->fetchOne([], ['pos_id' => $pos_id, 'status' => 1]);
        if (empty($ad_pos_detail)) {
            YCore::exception(- 1, '广告位置不存在或已经删除');
        }
        $data = [
            'ad_name'      => $ad_name,
            'start_time'   => $start_time,
            'end_time'     => $end_time,
            'display'      => $display,
            'remark'       => $remark,
            'ad_image_url' => $ad_image_url,
            'ad_url'       => $ad_url
        ];
        $rules = [
            'ad_name'      => '广告名称|require:1000000|len:1000000:1:50:1',
            'start_time'   => '生效时间|require:1000000|date:1000000:1',
            'end_time'     => '失效时间|require:1000000|date:1000000:1',
            'display'      => '显示状态|require:1000000|integer:1000000',
            'remark'       => '广告备注|require:1000000|len:1000000:1:200:1',
            'ad_image_url' => '广告图片|require:1000000|len:1000000:1:100:1',
            'ad_url'       => '广告URL|require:1000000|len:1000000:1:100:1|url:1000000'
        ];
        Validator::valido($data, $rules);
        if ($end_time <= $start_time) {
            YCore::exception(- 1, '生效时间必须小于失效时间');
        }
        $data['pos_id']       = $pos_id;
        $data['status']       = 1;
        $data['start_time']   = strtotime($start_time);
        $data['end_time']     = strtotime($end_time);
        $data['created_by']   = $admin_id;
        $data['created_time'] = $_SERVER['REQUEST_TIME'];
        $ad_model = new Ad();
        return $ad_model->insert($data);
    }

    /**
     * 编辑广告。
     *
     * @param number $admin_id 管理员ID。
     * @param number $ad_id 广告ID。
     * @param string $ad_name 广告名称。
     * @param string $start_time 广告生效时间。
     * @param string $end_time 广告失效时间。
     * @param number $display 显示状态：1显示、0隐藏。
     * @param string $remark 广告备注。
     * @param string $ad_image_url 广告图片。
     * @param string $ad_url 广告URL。
     * @return boolean
     */
    public static function editAd($admin_id, $ad_id, $ad_name, $start_time, $end_time, $display, $remark, $ad_image_url, $ad_url) {
        $ad_model = new Ad();
        $ad_detail = $ad_model->fetchOne([], [
            'ad_id' => $ad_id,
            'status' => 1
        ]);
        if (empty($ad_detail)) {
            YCore::exception(- 1, '广告不存在或已经删除');
        }
        $data = [
            'ad_name'      => $ad_name,
            'start_time'   => $start_time,
            'end_time'     => $end_time,
            'display'      => $display,
            'remark'       => $remark,
            'ad_image_url' => $ad_image_url,
            'ad_url'       => $ad_url
        ];
        $rules = [
            'ad_name'      => '广告名称|require:1000000|len:1000000:1:50:1',
            'start_time'   => '生效时间|require:1000000|date:1000000:1',
            'end_time'     => '失效时间|require:1000000|date:1000000:1',
            'display'      => '显示状态|require:1000000|integer:1000000',
            'remark'       => '广告备注|require:1000000|len:1000000:1:200:1',
            'ad_image_url' => '广告图片|require:1000000|len:1000000:1:100:1',
            'ad_url'       => '广告URL|require:1000000|len:1000000:1:100:1|url:1000000'
        ];
        Validator::valido($data, $rules);
        if ($end_time <= $start_time) {
            YCore::exception(- 1, '生效时间必须小于失效时间');
        }
        $data['start_time']    = strtotime($start_time);
        $data['end_time']      = strtotime($end_time);
        $data['modified_by']   = $admin_id;
        $data['modified_time'] = $_SERVER['REQUEST_TIME'];
        $where = [
            'ad_id' => $ad_id,
            'status' => 1
        ];
        return $ad_model->update($data, $where);
    }

    /**
     * 删除广告。
     *
     * @param number $admin_id 管理员ID。
     * @param number $ad_id 广告ID。
     * @return boolean
     */
    public static function deleteAd($admin_id, $ad_id) {
        $ad_model  = new Ad();
        $ad_detail = $ad_model->fetchOne([], ['ad_id' => $ad_id, 'status' => 1]);
        if (empty($ad_detail)) {
            YCore::exception(- 1, '广告不存在或已经删除');
        }
        $data = [
            'status'        => 2,
            'modified_by'   => $admin_id,
            'modified_time' => $_SERVER['REQUEST_TIME']
        ];
        $where = [
            'ad_id'  => $ad_id,
            'status' => 1
        ];
        return $ad_model->update($data, $where);
    }

    /**
     * 广告排序。
     *
     * @param array $listorders 分类排序数据。[ ['广告ID' => '排序值'], ...... ]
     * @return boolean
     */
    public static function sortAd($listorders) {
        if (empty($listorders)) {
            return true;
        }
        foreach ($listorders as $ad_id => $sort_val) {
            $ad_model = new Ad();
            $ok = $ad_model->sortAd($ad_id, $sort_val);
            if (! $ok) {
                return false;
            }
        }
        return true;
    }

}