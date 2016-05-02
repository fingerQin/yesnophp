<?php
/**
 * 系统配置管理。
* @author winerQin
* @date 2016-01-29
*/

namespace services;

use models\Config;
use winer\Validator;
use common\YCore;
class ConfigService extends BaseService {

    /**
     * 获取配置列表。
     * @param string $keyword 查询关键词。
     * @param int $page 当前页码。
     * @param int $count 每页显示条数。
     * @return array
     */
    public static function getConfigList($keyword = '', $page, $count) {
        $config_model = new Config();
        return $config_model->getConfigList($keyword, $page, $count);
    }

    /**
     * 添加配置。
     * @param int $admin_id 管理员ID。
     * @param string $ctitle 配置标题。
     * @param string $cname 配置名称。
     * @param string $cvalue 配置值。
     * @param string $description 配置描述。
     * @return boolean
     */
    public static function addConfig($admin_id, $ctitle, $cname, $cvalue, $description) {
        // [1] 验证
        $data = [
            'ctitle' => $ctitle,
            'cname'  => $cname,
            'cvalue' => $cvalue,
            'desc'   => $description
        ];
        $rules = [
            'ctitle' => '配置标题|require:5000001|len:5000003:1:50:1',
            'cname'  => '配置名称|require:5000001|alpha_dash:5000002|len:5000003:1:30:0',
            'cvalue' => '配置值|require:5000001|len:5000003:1:1000:1',
            'desc'   => '配置描述|require:5000001|len:5000003:1:255:0'
        ];
        Validator::valido($data, $rules); // 验证不通过会抛异常。
        $config_model = new Config();
        return $config_model->addConfig($admin_id, $ctitle, $cname, $cvalue, $description);
    }

    /**
     * 修改配置。
     * @param int $admin_id 管理员ID。
     * @param int $config_id 配置ID。
     * @param string $ctitle 配置标题。
     * @param string $cname 配置名称。
     * @param string $cvalue 配置值。
     * @param string $description 配置描述。
     * @return boolean
     */
    public static function editConfig($admin_id, $config_id, $ctitle, $cname, $cvalue, $description) {
        // [1] 验证
        $data = [
            'ctitle' => $ctitle,
            'cname'  => $cname,
            'cvalue' => $cvalue,
            'desc'   => $description
        ];
        $rules = [
            'ctitle' => '配置标题|require:5000001|len:5000003:1:50:1',
            'cname'  => '配置名称|require:5000001|alpha_dash:5000002|len:5000003:1:30:0',
            'cvalue' => '配置值|require:5000001|len:5000003:1:1000:1',
            'desc'   => '配置描述|require:5000001|len:5000003:1:255:0'
        ];
        Validator::valido($data, $rules); // 验证不通过会抛异常。
        $config_model = new Config();
        $where = [
            'config_id' => $config_id,
            'status'    => 1
        ];
        $config_detail = $config_model->fetchOne([], $where);
        if (empty($config_detail)) {
            YCore::throw_exception(-1, '该配置不存在');
        }
        return $config_model->editConfig($config_id, $admin_id, $ctitle, $cname, $cvalue, $description);
    }

    /**
     * 删除配置。
     * @param int $admin_id 管理员ID。
     * @param int $config_id 配置ID。
     * @return boolean
     */
    public static function deleteConfig($admin_id, $config_id) {
        $config_model = new Config();
        $where = [
            'config_id' => $config_id,
            'status'    => 1
        ];
        $config_detail = $config_model->fetchOne([], $where);
        if (empty($config_detail) || $config_detail['status'] != 1) {
            YCore::throw_exception(5000004, '配置不存在或已经删除');
        }
        $data = [
            'status'        => 2,
            'modified_by'   => $admin_id,
            'modified_time' => $_SERVER['REQUEST_TIME'],
        ];
        return $config_model->update($data, $where);
    }

    /**
     * 获取配置详情。
     * @param int $config_id 配置ID。
     * @return array
     */
    public static function getConfigDetail($config_id) {
        $config_model = new Config();
        $detail = $config_model->fetchOne([], ['config_id' => $config_id]);
        if (empty($detail) || $detail['status'] != 1) {
            YCore::throw_exception(-1, '配置不存在或已经删除');
        }
        return $detail;
    }
}