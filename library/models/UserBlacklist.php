<?php
/**
 * 用户黑名单表模型。
 * @author winerQin
 * @date 2015-11-05
 */

namespace models;

use common\YCore;
class UserBlacklist extends DbBase {

    /**
     * 表名。
     *
     * @var string
     */
    protected $_table_name = 'ms_user_blacklist';

    /**
     * 判断用户是否被封禁。
     *
     * @param number $user_id 用户ID。
     * @return array
     * [
     *      'status'  => 0或1,1被封禁。0未封禁。
     *      'message' => '封禁数据。如：您已经被永久封禁。',
     * ]
     */
    public function isForbidden($user_id) {
        $ret_data = [
            'status'  => 0,
            'message' => '正常使用'
        ];
        $result = $this->fetchOne([], ['user_id' => $user_id,'status' => 1]);
        if (empty($result)) {
            return $ret_data; // 没有封禁记录。
        }

        if ($result['ban_type'] == 1) {
            $ret_data['status']  = 1;
            $ret_data['message'] = '您的账号已经被永久封禁';
            return $ret_data;
        }

        $current_timestamp = $_SERVER['REQUEST_TIME'];
        if ($result['ban_type'] == 2 && ($result['ban_end_time'] < $current_timestamp || $result['ban_start_time'] > $current_timestamp)) {
            return $ret_data; // 过了封禁时间限制。
        } else {
            $ban_date            = date('Y-m-d H:i:s', $result['ban_end_time']);
            $ret_data['status']  = 1;
            $ret_data['message'] = "您当前被禁止登录。解禁日期：{$ban_date}";
            return $ret_data;
        }
    }

    /**
     * 封禁账号。
     *
     * @param number $admin_id 管理员ID。
     * @param number $user_id 用户ID。
     * @param string $username 用户账号。
     * @param number $ban_type 封禁类型。1永久封禁、2临时封禁。
     * @param number $ban_start_time 封禁开始时间。
     * @param number $ban_end_time 封禁截止时间。
     * @param string $ban_reason 账号封禁原因。
     * @return bool
     */
    public function forbiddenUser($admin_id, $user_id, $username, $ban_type, $ban_start_time = 0, $ban_end_time = 0, $ban_reason = '') {
        if ($ban_type == 1) { // 永久封禁不需要设置时间。
            $ban_start_time = 0;
            $ban_end_time = 0;
        } else {
            if ($ban_start_time == 0 || strlen($ban_start_time) != 10) {
                YCore::exception(6001000, 'The ban_start_time parameters is wrong');
            }
            if ($ban_end_time == 0 || strlen($ban_end_time) != 10) {
                YCore::exception(6001001, 'The ban_end_time parameters is wrong');
            }
        }
        $data = [
            'user_id'        => $user_id,
            'username'       => $username,
            'ban_type'       => $ban_type,
            'ban_start_time' => $ban_start_time,
            'ban_end_time'   => $ban_end_time,
            'ban_reason'     => $ban_reason,
            'created_by'     => $admin_id,
            'created_time'   => $_SERVER['REQUEST_TIME'],
            'status'         => 1
        ];
        $insert_id = $this->insert($data);
        if ($insert_id > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 解禁账号。
     *
     * @param number $user_id 用户ID。
     * @param number $admin_id 管理员ID。
     * @return bool
     */
    public function unforbiddenUser($user_id, $admin_id) {
        $data = $this->fetchOne([], ['user_id' => $user_id,'status' => 1]);
        if ($data) {
            $update_data = [
                'status'        => 0,
                'modified_by'   => $admin_id,
                'modified_time' => $_SERVER['REQUEST_TIME']
            ];
            return $this->update($update_data, ['id' => $data['id']]);
        } else {
            return false;
        }
    }
}