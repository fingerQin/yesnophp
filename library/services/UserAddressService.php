<?php
/**
 * 用户收货地址管理。
 * @author winerQin
 * @date 2016-04-08
 */
namespace services;

use winer\Validator;
use models\District;
use common\YCore;
use models\MallUserAddress;

class UserAddressService extends BaseService {

    /**
     * 获取用户收货地址详情。
     *
     * @param int $user_id 用户ID。
     * @param int $address_id 收货地址ID。
     * @return array
     */
    public static function getAddressDetail($user_id, $address_id) {
        $where = [
            'user_id'    => $user_id,
            'address_id' => $address_id
        ];
        $columns = [
            'address_id',
            'realname',
            'zipcode',
            'mobilephone',
            'address',
            'district_id',
            'is_default'
        ];
        $address_model = new MallUserAddress();
        $detail = $address_model->fetchOne([], $where);
        if (empty($detail)) {
            YCore::exception(1, '收货地址不存在');
        }
        return $detail;
    }

    /**
     * 根据用户提交的地址返回指定格式的地址信息。
     * -- Example start --
     * $data = [
     *      'user_id'     => '用户ID',
     *      'address_id'  => '用户地址ID',
     *      'realname'    => '收货人真实姓名',
     *      'district_id' => '区县或街道ID',
     *      'zipcode'     => '邮政编码',
     *      'mobilephone' => '手机号码',
     *      'address'     => '收货详细地址。除省市区街道外的部分地址信息。',
     * ];
     * -- Example end --
     *
     * @param array $data 地址信息。
     * @return array
     */
    public static function getSubmitUserAddressDetail($data) {
        if ($data['address_id'] == -1) { // 如果收货地址是新填写的，则验证有效性。
            if (!isset($data['realname']) || strlen($data['realname']) == 0) {
                YCore::exception(-1, '收货人姓名必须填写');
            }
            if (!Validator::is_len($data['realname'], 1, 10, true)) {
                YCore::exception(-1, '收货人姓名长度必须1~10个字符之间');
            }
            if (!isset($data['district_id']) || strlen($data['district_id']) === 0) {
                YCore::exception(-1, '请选择区县');
            }
            if (!isset($data['zipcode']) || strlen($data['zipcode']) === 0) {
                YCore::exception(-1, '邮政编码必须填写');
            }
            if (!isset($data['mobilephone']) || strlen($data['mobilephone']) === 0) {
                YCore::exception(-1, '收货人手机号必须填写');
            }
            if (!isset($data['address']) || strlen($data['address']) === 0) {
                YCore::exception(-1, '收货人详细地址必须填写');
            }
            if (!Validator::is_zipcode($data['zipcode'])) {
                YCore::exception(-1, '邮政编码不正确');
            }
            if (!Validator::is_mobilephone($data['mobilephone'])) {
                YCore::exception(-1, '收货人手机号不正确');
            }
            if (!Validator::is_len($data['address'], 1, 50, true)) {
                YCore::exception(-1, '收货详细地址长度必须1~50个字符之间');
            }
            $district_model = new District();
            $district_info = $district_model->fetchOne([], [
                'district_id' => $data['district_id'],
                'status'      => 1
            ]);
            if (empty($district_info)) {
                YCore::exception(-1, '区县ID有误');
            }
            $province_name = $district_info['province_name'];
            $city_name     = $district_info['city_name'];
            $district_name = $district_info['district_name'];
            $realname      = $data['realname'];
            $address       = $data['address'];
            $mobilephone   = $data['mobilephone'];
            $zipcode = $data['zipcode'];
        } else {
            $where = [
                'user_id'    => $data['user_id'],
                'address_id' => $data['address_id'],
                'status'     => 1
            ];
            $address_model = new MallUserAddress();
            $address_info  = $address_model->fetchOne([], $where);
            if (empty($address_info)) {
                YCore::exception(-1, '您选择的收货地址已经失效');
            }
            $district_model = new District();
            $district_info = $district_model->fetchOne([], ['district_id' => $address_info['district_id'], 'status' => 1]);
            if (empty($district_info)) {
                YCore::exception(-1, '您的收货地址的区县已经失效');
            }
            $province_name = $district_info['province_name'];
            $city_name     = $district_info['city_name'];
            $district_name = $district_info['district_name'];
            $realname      = $address_info['realname'];
            $address       = $address_info['address'];
            $mobilephone   = $address_info['mobilephone'];
            $zipcode       = $address_info['zipcode'];
        }
        $data = [
            'realname'      => $realname,
            'province_name' => $province_name,
            'city_name'     => $city_name,
            'district_name' => $district_name,
            'address'       => $address,
            'zipcode'       => $zipcode,
            'mobilephone'   => $mobilephone
        ];
        return $data;
    }

    /**
     * 添加收货地址。
     *
     * @param number $user_id 用户ID。
     * @param string $realname 真实姓名。
     * @param string $zipcode 邮政编码。
     * @param string $mobilephone 手机号码。
     * @param number $district_id 地区ID。
     * @param string $address 街道详细地址。
     * @return number
     */
    public static function addAddress($user_id, $realname, $zipcode, $mobilephone, $district_id, $address) {
        $data = [
            'realname'    => $realname,
            'zipcode'     => $zipcode,
            'mobilephone' => $mobilephone,
            'address'     => $address
        ];
        $rules = [
            'realname'    => '收货人姓名|require:1000000|len:1000000:2:10:1',
            'zipcode'     => '邮政编码|require:1000000|zipcode:1000000',
            'mobilephone' => '手机号码|require:1000000|mobilephone:1000000',
            'address'     => '收货地址|require:1000000|len:1000000:1:50:1'
        ];
        Validator::valido($data, $rules);
        $district_model = new District();
        $district_info = $district_model->fetchOne([], ['district_id' => $district_id, 'status' => 1]);
        if (empty($district_info) || $district_info['region_type'] < 3) {
            YCore::exception(-1, '地址有误');
        }
        $address_count = self::getUserAddressCount($user_id);
        $max_address_count = YCore::config('max_user_address_count');
        if ($address_count >= $max_address_count) {
            YCore::exception(-1, "最多允许创建{$max_address_count}个收货地址");
        }
        $data['district_id']  = $district_id;
        $data['status']       = 1;
        $data['created_time'] = $_SERVER['REQUEST_TIME'];
        $user_address_model = new MallUserAddress();
        $address_id = $user_address_model->insert($data);
        if ($address_id == 0) {
            YCore::exception(-1, '服务器繁忙,请稍候重试');
        }
        return $address_id;
    }

    /**
     * 获取用户收货地址数量。
     *
     * @param number $user_id 用户ID。
     * @return number
     */
    public static function getUserAddressCount($user_id) {
        $address_model = new MallUserAddress();
        $where = [
            'user_id' => $user_id,
            'status'  => 1
        ];
        return $address_model->count($where);
    }

    /**
     * 编辑收货地址。
     *
     * @param number $user_id 用户ID。
     * @param number $address_id 收货地址ID。
     * @param string $realname 真实姓名。
     * @param string $zipcode 邮政编码。
     * @param string $mobilephone 手机号码。
     * @param number $district_id 地区ID。
     * @param string $address 街道详细地址。
     * @return boolean
     */
    public static function editAddress($user_id, $address_id, $realname, $zipcode, $mobilephone, $district_id, $address) {
        $data = [
            'realname'    => $realname,
            'zipcode'     => $zipcode,
            'mobilephone' => $mobilephone,
            'address'     => $address
        ];
        $rules = [
            'realname'    => '收货人姓名|require:1000000|len:1000000:2:10:1',
            'zipcode'     => '邮政编码|require:1000000|zipcode:1000000',
            'mobilephone' => '手机号码|require:1000000|mobilephone:1000000',
            'address'     => '收货地址|require:1000000|len:1000000:1:50:1'
        ];
        Validator::valido($data, $rules);
        $district_model = new District();
        $district_info = $district_model->fetchOne([], ['district_id' => $district_id, 'status' => 1]);
        if (empty($district_info) || $district_info['region_type'] < 3) {
            YCore::exception(-1, '地址有误');
        }
        $user_address_model = new MallUserAddress();
        $address_info = $user_address_model->fetchOne([], ['address_id' => $address_id, 'status' => 1, 'user_id' => $user_id]);
        if (empty($address_info)) {
            YCore::exception(-1, '收货地址不存在');
        }
        $data['district_id']   = $district_id;
        $data['status']        = 1;
        $data['modified_time'] = $_SERVER['REQUEST_TIME'];
        $ok = $user_address_model->update($data, ['address_id' => $address_id]);
        if (!$ok) {
            YCore::exception(-1, '服务器繁忙,请稍候重试');
        }
        return true;
    }

    /**
     * 删除收货地址。
     *
     * @param number $user_id 用户ID。
     * @param number $address_id 收货地址ID。
     * @return boolean
     */
    public static function deleteAddress($user_id, $address_id) {
        $user_address_model = new MallUserAddress();
        $address_info = $user_address_model->fetchOne([], ['address_id' => $address_id, 'status' => 1, 'user_id' => $user_id]);
        if (empty($address_info)) {
            YCore::exception(-1, '收货地址不存在');
        }
        $data = [
            'status'        => 2,
            'modified_time' => $_SERVER['REQUEST_TIME']
        ];
        $where = [
            'address_id' => $address_id,
            'user_id'    => $user_id
        ];
        $ok = $user_address_model->update($data, $where);
        if (!$ok) {
            YCore::exception(-1, '服务器繁忙,请稍候重试');
        }
        return true;
    }

    /**
     * 获取用户所有收货地址。
     *
     * @param number $user_id 用户ID。
     * @return array
     */
    public static function getAllAddress($user_id) {
        $where = [
            'user_id' => $user_id,
            'status'  => 1
        ];
        $columns = [
            'address_id',
            'realname',
            'zipcode',
            'mobilephone',
            'district_id',
            'address'
        ];
        $user_address_model = new MallUserAddress();
        $address_list = $user_address_model->fetchAll($columns, $where, 0, 'is_default DESC,address_id ASC');
        foreach ($address_list as $key => $address) {
            $district_model = new District();
            $dis = $district_model->fetchOne([], ['district_id' => $address['district_id']]);
            $address['province_name'] = $dis['province_name'];
            $address['city_name']     = $dis['city_name'];
            $address['district_name'] = $dis['district_name'];
            $address['street_name']   = $dis['street_name'];
            $address_list[$key]       = $address;
        }
        return $address_list;
    }

    /**
     * 设置默认收货地址。
     *
     * @param number $user_id 用户ID。
     * @param number $address_id 收货地址ID。
     * @return boolean
     */
    public static function setDefaultAddress($user_id, $address_id) {
        $user_address_model = new MallUserAddress();
        $address_info = $user_address_model->fetchOne([], ['address_id' => $address_id, 'status' => 1, 'user_id' => $user_id]);
        if (empty($address_info)) {
            YCore::exception(-1, '收货地址不存在');
        }
        $user_address_model->update(['is_default' => 0], ['user_id' => $user_id, 'status' => 1]);
        $data = [
            'is_default'    => 1,
            'modified_time' => $_SERVER['REQUEST_TIME']
        ];
        $where = [
            'address_id' => $address_id,
            'user_id'   => $user_id
        ];
        $ok = $user_address_model->update($data, $where);
        if (!$ok) {
            YCore::exception(-1, '服务器繁忙,请稍候重试');
        }
        return true;
    }
}