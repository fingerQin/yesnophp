<?php
/**
 * 敏感词业务封装。
 * @author winerQin
 * @date 2016-03-23
 */
namespace services;

use models\Sensitive;

class SensitiveService extends BaseService {

    /**
     * 添加敏感词。
     *
     * @param int $admin_id 操作管理员ID。
     * @param string $lv 字典类型code编码。
     * @param string $val 字典类型名称。
     * @return boolean
     */
    public static function addSensitive($admin_id, $lv, $val) {
        $sensitive_model = new Sensitive();
        return $sensitive_model->addSensitive($admin_id, $lv, $val);
    }

    /**
     * 编辑敏感词。
     *
     * @param number $id 敏感词ID。
     * @param number $admin_id 操作管理员ID。
     * @param string $lv 字典类型code编码。
     * @param string $val 字典类型名称。
     * @return boolean
     */
    public static function editSensitive($id, $admin_id, $lv, $val) {
        $sensitive_model = new Sensitive();
        return $sensitive_model->editSensitive($id, $admin_id, $lv, $val);
    }

    /**
     * 删除敏感词。
     *
     * @param number $admin_id 操作管理员ID。
     * @param number $id 敏感词ID。
     * @return boolean
     */
    public static function deleteSensitive($admin_id, $id) {
        $sensitive_model = new Sensitive();
        return $sensitive_model->deleteSensitive($admin_id, $id);
    }

    /**
     * 获取敏感词列表。
     *
     * @param string $keyword 查询关键词。模糊搜索敏感词。
     * @param number $lv 敏感词等级。-1全部、其他示为等级。
     * @param number $page 页码。
     * @param number $count 每页显示条数。
     * @return array
     */
    public static function getSensitiveList($keyword = '', $lv = -1, $page = 1, $count = 10) {
        $sensitive_model = new Sensitive();
        return $sensitive_model->getList($keyword, $lv, $page, $count);
    }

    /**
     * 获取敏感词详情。
     *
     * @param number $id 敏感词ID。
     * @return array
     */
    public static function getSensitiveDetail($id) {
        $sensitive_model = new Sensitive();
        $data = $sensitive_model->fetchOne([], ['id' => $id]);
        return $data ? $data : [];
    }
}