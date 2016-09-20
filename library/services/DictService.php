<?php
/**
 * 系统字典管理。
 * @author winerQin
 * @date 2015-11-10
 */
namespace services;

use models\DictType;
use winer\Validator;
use common\YCore;
use models\Dict;

class DictService extends BaseService {

    /**
     * 获取系统所有的字典类型数据。
     *
     * @return array
     */
    private static function getSystemAllDictType() {
        $cache_key = 'dict_service_system_dict_type';
        $all_dict_type = \Yaf\Registry::get($cache_key);
        if ($all_dict_type !== null && $all_dict_type !== false) {
            return $all_dict_type;
        }
        $cache = YCore::getCache();
        $result = $cache->get($cache_key);
        if ($result === false) {
            $dict_type_model = new DictType();
            $columns = ['dict_type_id', 'type_code'];
            $result = $dict_type_model->fetchAll();
            $all_dict_type = [];
            foreach ($result as $dict_type) {
                $all_dict_type[$dict_type['type_code']] = $dict_type['dict_type_id'];
            }
            $cache->set($cache_key, json_encode($all_dict_type));
            \Yaf\Registry::set($cache_key, $all_dict_type);
            return $all_dict_type;
        } else {
            $all_dict_type = json_decode($result, true);
            \Yaf\Registry::set($cache_key, $all_dict_type);
            return $all_dict_type;
        }
    }

    /**
     * 获取系统所有字典值。
     * -- 1、上万条字典值内存占用也才20KB左右。
     *
     * @return string
     */
    private static function getSystemDictTypeValue() {
        $cache_key = 'dict_service_system_dict_type_value';
        $all_dict_type_value = \Yaf\Registry::get($cache_key);
        if ($all_dict_type_value !== null && $all_dict_type_value !== false) {
            return $all_dict_type_value;
        }
        $cache = YCore::getCache();
        $result = $cache->get($cache_key);
        if ($result === false) {
            $dict_model = new Dict();
            $columns = [
                'dict_type_id',
                'dict_code',
                'dict_value'
            ];
            $where = [
                'status' => 1
            ];
            $order_by = 'listorder ASC, dict_id ASC';
            $result = $dict_model->fetchAll($columns, $where, 0, $order_by);
            $all_dict_type_value = [];
            foreach ($result as $dict) {
                $all_dict_type_value[$dict['dict_type_id']][$dict['dict_code']] = $dict['dict_value'];
            }
            $cache->set($cache_key, json_encode($all_dict_type_value));
            \Yaf\Registry::set($cache_key, $all_dict_type_value);
            return $all_dict_type_value;
        } else {
            $all_dict_type_value = json_decode($result, true);
            \Yaf\Registry::set($cache_key, $all_dict_type_value);
            return $all_dict_type_value;
        }
    }

    /**
     * 清理所有字典相关的缓存数据。
     *
     * @return void
     */
    public static function clearDictCache() {
        // [1] 清理字典类型数据缓存。
        $config_cache_key = 'dict_service_system_dict_type';
        $cache = YCore::getCache();
        $cache->delete($config_cache_key);
        \Yaf\Registry::del($config_cache_key);
        // [2] 清理字典值数据缓存。
        $config_cache_key = 'dict_service_system_dict_type_value';
        $cache->delete($config_cache_key);
        \Yaf\Registry::del($config_cache_key);
    }

    /**
     * 获取系统字典数据。
     *
     * @param string $dict_type_code 字典类型编码。
     * @param string $dict_code 字典编码。
     * @return array
     */
    public static function getSystemDict($dict_type_code, $dict_code = '') {
        // [1] 获取所有字典类型值。
        $all_dict_type = self::getSystemAllDictType();
        if (!isset($all_dict_type[$dict_type_code])) {
            YCore::exception(-1, "系统字典[{$dict_type_code}]未设置");
        }
        $dict_type_id = $all_dict_type[$dict_type_code];
        $dict_type_values = self::getSystemDictTypeValue();
        $values = $dict_type_values[$dict_type_id];
        if (strlen($dict_code) > 0) {
            foreach ($values as $_dict_code => $_dict_value) {
                if ($_dict_code == $dict_code) {
                    return $_dict_value;
                }
            }
            YCore::exception(-1, "字典值编码[{$dict_code}]不存在");
        } else {
            return $values;
        }
    }

    /**
     * 字典排序。
     *
     * @param int $admin_id 管理员ID。
     * @param array $listorders 排序。字典值ID=>排序位置。
     * @return boolean
     */
    public static function sortDict($admin_id, $listorders) {
        if (empty($listorders)) {
            YCore::exception(80001000, '没有任何排序数据');
        }
        foreach ($listorders as $dict_id => $sort) {
            if (!Validator::is_integer($dict_id) || $dict_id < 0) {
                YCore::exception(80001001, '非法参数');
            }
            if (!Validator::is_integer($sort) || $sort < 0) {
                YCore::exception(80001002, '非法参数');
            }
            $dict_model = new Dict();
            $dict_model->sort($admin_id, $dict_id, $sort);
        }
        self::clearDictCache();
        return true;
    }

    /**
     * 获取字典类型列表。
     *
     * @param string $keyword 查询关键词。
     * @param int $page 当前页码。
     * @param int $count 每页显示条数。
     * @return array
     */
    public static function getDictTypeList($keyword = '', $page, $count) {
        $dict_type_model = new DictType();
        $result = $dict_type_model->getDictTypeList($keyword, $page, $count);
        return $result;
    }

    /**
     * 获取字典列表。
     *
     * @param int $dict_type_id 字典类型ID。
     * @param string $keywords 查询关键词。查询值编码或值名称。
     * @param int $page 当前页码。
     * @param int $count 每页显示条数。
     * @return array
     */
    public static function getDictList($dict_type_id, $keywords, $page, $count) {
        $dict_model = new Dict();
        $result = $dict_model->getDictList($dict_type_id, $keywords, $page, $count);
        return $result;
    }

    /**
     * 获取字典详情。
     *
     * @param int $dict_id 字典ID。
     * @return array
     */
    public static function getDict($dict_id) {
        $dict_model = new Dict();
        $dict = $dict_model->getDict($dict_id);
        if (empty($dict) || $dict['status'] != 1) {
            YCore::exception(-1, '字典不存在或已经删除');
        }
        return $dict;
    }

    /**
     * 获取字典类型详情。
     *
     * @param int $dict_type_id 字典类型ID。
     * @return array
     */
    public static function getDictType($dict_type_id) {
        $dict_type_model = new DictType();
        $dict_type_detail = $dict_type_model->getDictTypeDetail($dict_type_id);
        if (empty($dict_type_detail) || $dict_type_detail['status'] != 1) {
            YCore::exception(-1, '字典类型不存在或已经删除');
        }
        return $dict_type_detail;
    }

    /**
     * 添加字典类型。
     *
     * @param int $admin_id 修改人ID（管理员ID）。
     * @param string $type_code 字典类型code编码。
     * @param string $type_name 字典类型名称。
     * @param string $description 字典类型描述。
     * @return boolean
     */
    public static function addDictType($admin_id, $type_code, $type_name, $description) {
        // [1] 验证
        $data = [
            'type_code' => $type_code,
            'type_name' => $type_name
        ];
        $rules = [
            'type_code' => '字典类型编码|require:5000001|alpha_dash:5000002|len:5000003:1:50:0',
            'type_name' => '字典类型名称|require:5000004|len:5000006:1:50:0'
        ];
        Validator::valido($data, $rules); // 验证不通过会抛异常。
        $dict_type_model = new DictType();
        $dict_type_id = $dict_type_model->addDictType($admin_id, $type_code, $type_name, $description);
        if ($dict_type_id == 0) {
            YCore::exception(-1, '服务器繁忙,请稍候重试');
        }
        self::clearDictCache();
        return true;
    }

    /**
     * 编辑字典类型。
     *
     * @param int $admin_id 修改人ID（管理员ID）。
     * @param int $dict_type_id 字典类型ID。
     * @param string $type_code 字典类型code编码。
     * @param string $type_name 字典类型名称。
     * @param string $description 字典类型描述。
     * @return boolean
     */
    public static function editDictType($admin_id, $dict_type_id, $type_code, $type_name, $description) {
        // [1] 验证
        $data = [
            'type_code' => $type_code,
            'type_name' => $type_name
        ];
        $rules = [
            'type_code' => '字典类型编码|require:5000001|alpha_dash:5000002|len:5000003:1:50:0',
            'type_name' => '字典类型名称|require:5000004|len:5000006:1:50:0'
        ];
        Validator::valido($data, $rules); // 验证不通过会抛异常。
        $dict_type_model = new DictType();
        $dict_type_detail = $dict_type_model->getDictTypeDetail($dict_type_id);
        if (empty($dict_type_detail)) {
            YCore::exception(7001001, '字典类型不存在或已经删除');
        }
        $ok = $dict_type_model->editDictType($admin_id, $dict_type_id, $type_code, $type_name, $description);
        if (!$ok) {
            YCore::exception(-1, '服务器繁忙,请稍候重试');
        }
        self::clearDictCache();
        return true;
    }

    /**
     * 字典类型删除。
     *
     * @param int $admin_id 管理员ID。
     * @param int $dict_type_id 字典类型ID。
     * @return boolean
     */
    public static function deleteDictType($admin_id, $dict_type_id) {
        $dict_type_model = new DictType();
        $dict_type_detail = $dict_type_model->getDictTypeDetail($dict_type_id);
        if (empty($dict_type_detail)) {
            YCore::exception(7001001, '字典类型不存在或已经删除');
        }
        $dict_model = new Dict();
        $is_empty = $dict_model->isNotEmpty($dict_type_id);
        if (!$is_empty) {
            YCore::exception(7001001, '该字典的值不为空,请先清空再删除该字典');
        }
        $ok = $dict_type_model->deleteDictType($admin_id, $dict_type_id);
        if (!$ok) {
            YCore::exception(-1, '服务器繁忙,请稍候重试');
        }
        self::clearDictCache();
        return true;
    }

    /**
     * 添加字典。
     *
     * @param int $dict_type_id 字典类型ID。
     * @param string $dict_code 字典编码。
     * @param string $dict_value 字典值。
     * @param string $description 描述。
     * @param int $listorder 排序。
     * @param int $admin_id 管理ID。
     * @return boolean
     */
    public static function addDict($dict_type_id, $dict_code, $dict_value, $description, $listorder, $admin_id) {
        // [1] 验证
        $data = [
            'dict_type_id' => $dict_type_id,
            'dict_code'    => $dict_code,
            'dict_value'   => $dict_value,
            'description'  => $description,
            'listorder'    => $listorder
        ];
        $rules = [
            'dict_type_id' => '字典类型ID|require:5000001|integer:5000002',
            'dict_code'    => '字典编码|require:5000004|alpha_dash:5000005|len:5000006:1:50:0',
            'dict_value'   => '字典值|require:5000001|len:5000003:1:50:1',
            'description'  => '字典描述|require:5000004|len:5000006:1:200:1',
            'listorder'    => '排序|require:5000004|integer:5000005'
        ];
        Validator::valido($data, $rules); // 验证不通过会抛异常。
        $dict_model = new Dict();
        $dict_detail = $dict_model->fetchOne([], ['dict_code' => $dict_code, 'dict_type_id' => $dict_type_id, 'status' => 1]);
        if ($dict_detail) {
            YCore::exception(-1, '不要重复添加');
        }
        $dict_id = $dict_model->addDict($admin_id, $dict_type_id, $dict_code, $dict_value, $description, $listorder);
        if ($dict_id == 0) {
            YCore::exception(-1, '服务器繁忙,请稍候重试');
        }
        self::clearDictCache();
        return true;
    }

    /**
     * 编辑字典。
     *
     * @param int $dict_id 字典ID。
     * @param string $dict_code 字典编码。
     * @param string $dict_value 字典值。
     * @param string $description 描述。
     * @param int $listorder 排序。
     * @param int $admin_id 管理员ID。
     * @return boolean
     */
    public static function editDict($dict_id, $dict_code, $dict_value, $description, $listorder, $admin_id) {
        // [1] 验证
        $data = [
            'dict_id'     => $dict_id,
            'dict_code'   => $dict_code,
            'dict_value'  => $dict_value,
            'description' => $description,
            'listorder'   => $listorder
        ];
        $rules = [
            'dict_id'     => '字典ID|require:5000001|integer:5000002',
            'dict_code'   => '字典编码|require:5000004|alpha_dash:5000005|len:5000006:1:50:0',
            'dict_value'  => '字典值|require:5000001|len:5000003:1:50:1',
            'description' => '字典描述|require:5000004|len:5000006:1:200:1',
            'listorder'   => '排序|require:5000004|integer:5000005'
        ];
        Validator::valido($data, $rules); // 验证不通过会抛异常。
        $dict_model = new Dict();
        $dict_detail = $dict_model->getDict($dict_id);
        if (empty($dict_detail) || $dict_detail['status'] == 2) {
            YCore::exception(5000004, '字典不存在');
        }
        $ok = $dict_model->editDict($dict_id, $admin_id, $dict_code, $dict_value, $description, $listorder);
        if (!$ok) {
            YCore::exception(-1, '服务器繁忙,请稍候重试');
        }
        self::clearDictCache();
        return true;
    }

    /**
     * 删除字典。
     *
     * @param int $dict_id 字典ID。
     * @param int $admin_id 管理员ID。
     * @return boolean
     */
    public static function deleteDict($dict_id, $admin_id) {
        $dict_model = new Dict();
        $dict_detail = $dict_model->getDict($dict_id);
        if (empty($dict_detail) || $dict_detail['status'] == 2) {
            YCore::exception(5000004, '字典不存在');
        }
        $ok = $dict_model->deleteDict($dict_id, $admin_id);
        if (!$ok) {
            YCore::exception(-1, '服务器繁忙,请稍候重试');
        }
        self::clearDictCache();
        return true;
    }
}