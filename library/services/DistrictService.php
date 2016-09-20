<?php
/**
 * 省市区操作封装。
 * @author winerQin
 * @date 2016-04-08
 */
namespace services;

use models\DbBase;
use models\District;

class DistrictService extends BaseService {

    /**
     * 树结构格式化省市区。
     *
     * @param number $code 省市区编码。
     * @param number $region_type 区域类型。
     * @param array $list 省市区数据。
     * @return array
     */
    public static function treeToDistrict($code = '', $region_type = 1, $list = []) {
        if (empty($list)) {
            $default_db = new DbBase();
            $sql = 'SELECT * FROM ms_district WHERE region_type IN(1, 2, 3) AND status = :status';
            $list = $default_db->rawQuery($sql, [':status' => 1])->rawFetchAll();
        }
        switch ($region_type) {
            case 1:
                $result = [];
                foreach ($list as $item) {
                    if ($item['region_type'] == $region_type) {
                        $result[] = [
                            'id'   => $item['district_id'],
                            'name' => $item['province_name'],
                            'sub'  => self::treeToDistrict($item['province_code'], 2, $list)
                        ];
                    }
                }
                return $result;
                break;
            case 2:
                $return = [];
                foreach ($list as $item) {
                    if ($item['region_type'] == $region_type && $code == $item['province_code']) {
                        $result[] = [
                            'id'   => $item['district_id'],
                            'name' => $item['city_name'],
                            'sub'  => self::treeToDistrict($item['city_code'], 3, $list)
                        ];
                    }
                }
                return $result;
                break;
            case 3:
                $return = [];
                foreach ($list as $item) {
                    if ($item['region_type'] == $region_type && $code == $item['city_code']) {
                        $result[] = [
                            'id'   => $item['district_id'],
                            'name' => $item['district_name'],
                            'sub'  => []
                        ];
                    }
                }
                return $result;
                break;
        }
    }

    /**
     * 根据省/市/区县/街道名称获取district_id。
     *
     * @param number $region_type 类型。1:省 2:市 3:区县 4:街道。
     * @param number $name 区县名称。
     * @return number
     */
    public static function getByDistrictOfName($region_type, $name) {
        $district_model = new District();
        switch ($region_type) {
            case 1:
                $where = [
                    'province_name' => $name,
                    'region_type'   => 1
                ];
                $district_info = $district_model->fetchOne([], $where);
                return $district_info ? $district_info['district_id'] : 0;
                break;
            case 2:
                $where = [
                    'city_name'   => $name,
                    'region_type' => 2
                ];
                $district_info = $district_model->fetchOne([], $where);
                return $district_info ? $district_info['district_id'] : 0;
                break;
            case 3:
                $where = [
                    'district_name' => $name,
                    'region_type'   => 3
                ];
                $district_info = $district_model->fetchOne([], $where);
                return $district_info ? $district_info['district_id'] : 0;
                break;
            case 4:
                $where = [
                    'street_name' => $name,
                    'region_type' => 4
                ];
                $district_info = $district_model->fetchOne([], $where);
                return $district_info ? $district_info['district_id'] : 0;
                break;
        }
        return 0;
    }
}