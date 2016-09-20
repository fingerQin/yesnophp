<?php
/**
 * 用户副表。
 * @author winerQin
 * @date 2016-05-23
 */

namespace models;

class UserData extends DbBase {

    /**
     * 表名。
     *
     * @var string
     */
    protected $_table_name = 'ms_user_data';

    /**
     * 初始化表数据。
     *
     * @param number $user_id 用户ID。
     * @param string $mobilephone 手机号码。
     * @param string $realname 真实姓名。
     * @param string $nickname 用户昵称。
     * @param string $email 邮箱地址。
     * @param string $avatar 头像。
     * @param string $signature 签名。
     * @return boolean
     */
    public function initTableData($user_id, $mobilephone = '', $realname = '', $nickname = '', $email = '', $avatar = '', $signature = '') {
        $data = [
            'user_id'      => $user_id,
            'nickname'     => $nickname,
            'realname'     => $realname,
            'mobilephone'  => $mobilephone,
            'email'        => $email,
            'avatar'       => $avatar,
            'signature'    => $signature,
            'created_time' => $_SERVER['REQUEST_TIME']
        ];
        $id = $this->insert($data);
        return $id > 0 ? true : false;
    }

    /**
     * 修改表数据。
     *
     * @param number $user_id 用户ID。
     * @param string $mobilephone 手机号码。
     * @param string $realname 真实姓名。
     * @param string $nickname 用户昵称。
     * @param string $email 邮箱地址。
     * @param string $avatar 头像。
     * @param string $signature 签名。
     * @return boolean
     */
    public function editInfo($user_id, $mobilephone = '', $realname = '', $nickname = '', $email = '', $avatar = '', $signature = '') {
        $data = [
            'user_id'       => $user_id,
            'nickname'      => $nickname,
            'realname'      => $realname,
            'mobilephone'   => $mobilephone,
            'email'         => $email,
            'avatar'        => $avatar,
            'signature'     => $signature,
            'modified_time' => $_SERVER['REQUEST_TIME']
        ];
        $ok = $this->update($data, ['user_id' => $user_id]);
        return $ok ? true : false;
    }
}