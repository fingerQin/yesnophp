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
     * 字典排序。
     * @param int $admin_id 管理员ID。
     * @param array $listorders 排序。字典值ID=>排序位置。
     * @return boolean
     */
    public static function sortDict($admin_id, $listorders) {
        if (empty($listorders)) {
            YCore::throw_exception(80001000, '没有任何排序数据');
        }
        foreach ($listorders as $dict_id => $sort) {
            if (!Validator::is_integer($dict_id) || $dict_id < 0) {
                YCore::throw_exception(80001001, '非法参数');
            }
            if (!Validator::is_integer($sort) || $sort < 0) {
                YCore::throw_exception(80001002, '非法参数');
            }
            $dict_model = new Dict();
            $dict_model->sort($admin_id, $dict_id, $sort);
        }
        return true;
    }

    /**
     * 获取字典类型列表。
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
     * @param int $dict_id 字典ID。
     * @return array
     */
    public static function getDict($dict_id) {
        $dict_model = new Dict();
        $dict = $dict_model->getDict($dict_id);
        if (empty($dict) || $dict['status'] != 1) {
            YCore::throw_exception(-1, '字典不存在或已经删除');
        }
        return $dict;
    }

    /**
     * 获取字典类型详情。
     * @param int $dict_type_id 字典类型ID。
     * @return array
     */
    public static function getDictType($dict_type_id) {
        $dict_type_model = new DictType();
        $dict_type_detail = $dict_type_model->getDictTypeDetail($dict_type_id);
        if (empty($dict_type_detail) || $dict_type_detail['status'] != 1) {
            YCore::throw_exception(-1, '字典类型不存在或已经删除');
        }
        return $dict_type_detail;
    }

    /**
     * 添加字典类型。
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
            'type_name' => $type_name,
        ];
        $rules = [
            'type_code' => '字典类型编码|require:5000001|alpha_dash:5000002|len:5000003:1:50:0',
            'type_name' => '字典类型名称|require:5000004|len:5000006:1:50:0',
        ];
        Validator::valido($data, $rules); // 验证不通过会抛异常。
        $dict_type_model = new DictType();
        return $dict_type_model->addDictType($admin_id, $type_code, $type_name, $description);
    }

    /**
     * 编辑字典类型。
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
            'type_name' => $type_name,
        ];
        $rules = [
            'type_code' => '字典类型编码|require:5000001|alpha_dash:5000002|len:5000003:1:50:0',
            'type_name' => '字典类型名称|require:5000004|len:5000006:1:50:0',
        ];
        Validator::valido($data, $rules); // 验证不通过会抛异常。
        $dict_type_model = new DictType();
        $dict_type_detail = $dict_type_model->getDictTypeDetail($dict_type_id);
        if (empty($dict_type_detail)) {
            YCore::throw_exception(7001001, '字典类型不存在或已经删除');
        }
        return $dict_type_model->editDictType($admin_id, $dict_type_id, $type_code, $type_name, $description);
    }

    /**
     * 字典类型删除。
     * @param int $admin_id 管理员ID。
     * @param int $dict_type_id 字典类型ID。
     * @return boolean
     */
    public static function deleteDictType($admin_id, $dict_type_id) {
        $dict_type_model = new DictType();
        $dict_type_detail = $dict_type_model->getDictTypeDetail($dict_type_id);
        if (empty($dict_type_detail)) {
            YCore::throw_exception(7001001, '字典类型不存在或已经删除');
        }
        $dict_model = new Dict();
        $is_empty = $dict_model->isNotEmpty($dict_type_id);
        if (!$is_empty) {
            YCore::throw_exception(7001001, '该字典的值不为空,请先清空再删除该字典');
        }
        return $dict_type_model->deleteDictType($admin_id, $dict_type_id);
    }

    /**
     * 添加字典。
     * @param int $dict_type_id 字典类型ID。
     * @param string $dict_code 字典编码。
     * @param string $dict_name 字典值。
     * @param string $description 描述。
     * @param int $listorder 排序。
     * @param int $admin_id 管理ID。
     * @return boolean
     */
    public static function addDict($dict_type_id, $dict_code, $dict_name, $description, $listorder, $admin_id) {
        // [1] 验证
        $data = [
            'dict_type_id' => $dict_type_id,
            'dict_code'    => $dict_code,
            'dict_name'    => $dict_name,
            'description'  => $description,
            'listorder'    => $listorder
        ];
        $rules = [
            'dict_type_id' => '字典类型ID|require:5000001|integer:5000002',
            'dict_code'    => '字典编码|require:5000004|alpha_dash:5000005|len:5000006:1:50:0',
            'dict_name'    => '字典值|require:5000001|len:5000003:1:50:1',
            'description'  => '字典描述|require:5000004|len:5000006:1:200:1',
            'listorder'    => '排序|require:5000004|integer:5000005',
        ];
        Validator::valido($data, $rules); // 验证不通过会抛异常。
        $dict_model = new Dict();
        $dict_detail = $dict_model->fetchOne([], ['dict_code' => $dict_code, 'dict_type_id' => $dict_type_id, 'status' => 1]);
        if ($dict_detail) {
            YCore::throw_exception(-1, '不要重复添加');
        }
        return $dict_model->addDict($admin_id, $dict_type_id, $dict_code, $dict_name, $description, $listorder);
    }

    /**
     * 编辑字典。
     * @param int $dict_id 字典ID。
     * @param string $dict_code 字典编码。
     * @param string $dict_name 字典值。
     * @param string $description 描述。
     * @param int $listorder 排序。
     * @param int $admin_id 管理员ID。
     * @return boolean
     */
    public static function editDict($dict_id, $dict_code, $dict_name, $description, $listorder, $admin_id) {
        // [1] 验证
        $data = [
            'dict_id'     => $dict_id,
            'dict_code'   => $dict_code,
            'dict_name'   => $dict_name,
            'description' => $description,
            'listorder'   => $listorder
        ];
        $rules = [
            'dict_id'     => '字典ID|require:5000001|integer:5000002',
            'dict_code'   => '字典编码|require:5000004|alpha_dash:5000005|len:5000006:1:50:0',
            'dict_name'   => '字典值|require:5000001|len:5000003:1:50:1',
            'description' => '字典描述|require:5000004|len:5000006:1:200:1',
            'listorder'   => '排序|require:5000004|integer:5000005',
        ];
        Validator::valido($data, $rules); // 验证不通过会抛异常。
        $dict_model = new Dict();
        $dict_detail = $dict_model->getDict($dict_id);
        if (empty($dict_detail) || $dict_detail['status'] == 2) {
            YCore::throw_exception(5000004, '字典不存在');
        }
        return $dict_model->editDict($dict_id, $admin_id, $dict_code, $dict_name, $description, $listorder);
    }
    
    /**
     * 删除字典。
     * @param int $dict_id 字典ID。
     * @param int $admin_id 管理员ID。
     * @return boolean
     */
    public static function deleteDict($dict_id, $admin_id) {
        $dict_model = new Dict();
        $dict_detail = $dict_model->getDict($dict_id);
        if (empty($dict_detail) || $dict_detail['status'] == 2) {
            YCore::throw_exception(5000004, '字典不存在');
        }
        return $dict_model->deleteDict($dict_id, $admin_id);
    }
}