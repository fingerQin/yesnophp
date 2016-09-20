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
     * 以键值对形式返回所有的配置数据。
     *
     * @return array
     */
    public static function getAllConfig() {
        $config_cache_key = 'config_service_system_configs';
        $configs = \Yaf\Registry::get($config_cache_key);
        if ($configs !== null && $configs !== false) { // 保证每个请求只会调用一次Redis读取缓存的操作，节省Redis资源。
            return $configs;
        }
        $cache = YCore::getCache();
        $configs_cache = $cache->get($config_cache_key);
        if ($configs_cache === false) {
            $config_model = new Config();
            $columns = ['cname', 'cvalue'];
            $where = [
                'status' => 1
            ];
            $order_by = ' config_id ASC ';
            $result = $config_model->fetchAll($columns, $where, 0, $order_by);
            $configs = [];
            foreach ($result as $val) {
                $configs[$val['cname']] = $val['cvalue'];
            }
            $ok = $cache->set($config_cache_key, json_encode($configs));
            \Yaf\Registry::set($config_cache_key, $configs);
            return $configs;
        } else {
            $configs = json_decode($configs_cache, true);
            \Yaf\Registry::set($config_cache_key, $configs);
            return $configs;
        }
    }

    /**
     * 清除配置文件缓存。
     *
     * @return void
     */
    public static function clearConfigCache() {
        $config_cache_key = 'config_service_system_configs';
        $cache = YCore::getCache();
        $cache->delete($config_cache_key);
        \Yaf\Registry::del($config_cache_key);
    }

    /**
     * 获取配置列表。
     *
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
     *
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
            'cvalue' => '配置值|len:5000003:1:1000:1',
            'desc'   => '配置描述|require:5000001|len:5000003:1:255:1'
        ];
        Validator::valido($data, $rules); // 验证不通过会抛异常。
        $config_model = new Config();
        $config_id = $config_model->addConfig($admin_id, $ctitle, $cname, $cvalue, $description);
        if ($config_id == 0) {
            YCore::exception(- 1, '服务器繁忙,请稍候重试');
        }
        self::clearConfigCache();
        unset($data, $rules, $config_id, $config_model);
        return true;
    }

    /**
     * 修改配置。
     *
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
            'cvalue' => '配置值|len:5000003:1:255:1',
            'desc'   => '配置描述|require:5000001|len:5000003:1:255:1'
        ];
        Validator::valido($data, $rules); // 验证不通过会抛异常。
        $config_model = new Config();
        $where = [
            'config_id' => $config_id,
            'status'    => 1
        ];
        $config_detail = $config_model->fetchOne([], $where);
        if (empty($config_detail)) {
            YCore::exception(- 1, '该配置不存在');
        }
        unset($data, $rules);
        self::clearConfigCache();
        return $config_model->editConfig($config_id, $admin_id, $ctitle, $cname, $cvalue, $description);
    }

    /**
     * 按配置编码更新配置值。
     *
     * @param string $cname 配置编码。
     * @param string $cvalue 配置值。
     * @return boolean
     */
    public static function updateConfigValue($cname, $cvalue) {
        $config_model = new Config();
        if (! Validator::is_len($cvalue, 1, 255, true)) {
            YCore::exception(- 1, '配置值必须小于255个字符');
        }
        $update = [
            'cvlaue'        => $cvalue,
            'modified_by'   => 0,
            'modified_time' => $_SERVER['REQUEST_TIME']
        ];
        $where = [
            'cname'  => $cname,
            'status' => 1
        ];
        $ok = $config_model->update($update, $where);
        if (! $ok) {
            YCore::exception(- 1, '配置更新失败');
        }
        self::clearConfigCache();
        return true;
    }

    /**
     * 删除配置。
     *
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
            YCore::exception(5000004, '配置不存在或已经删除');
        }
        $data = [
            'status'        => 2,
            'modified_by'   => $admin_id,
            'modified_time' => $_SERVER['REQUEST_TIME']
        ];
        self::clearConfigCache();
        return $config_model->update($data, $where);
    }

    /**
     * 获取配置详情。
     *
     * @param int $config_id 配置ID。
     * @return array
     */
    public static function getConfigDetail($config_id) {
        $config_model = new Config();
        $detail = $config_model->fetchOne([], ['config_id' => $config_id]);
        if (empty($detail) || $detail['status'] != 1) {
            YCore::exception(- 1, '配置不存在或已经删除');
        }
        return $detail;
    }

}