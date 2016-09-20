<?php
/**
 * 文件管理。
 * @author winerQin
 * @date 2016-02-24
 */
namespace services;

use models\Files;
use models\Admin;
use winer\Validator;
use common\YCore;
use models\User;

class FileService extends BaseService {
    /**
     * 获取文件列表。
     *
     * @param number $user_type 用户类型：－1全部、1管理员、2普通用户 。
     * @param string $user_name 用户名。如果user_type为1的时候为管理员用户名，为2的时候为普通用户用户名。
     * @param string $file_md5 文件md5值。
     * @param unknown $file_type 文件类型：1-图片、2-其他文件。
     * @param string $start_time 文件上传时间开始。
     * @param string $end_time 文件上传时间截止。
     * @param unknown $page 当前页码。
     * @param number $count 每页显示条数。
     * @return array
     */
    public static function getFileList($user_type = -1, $user_name = '', $file_md5 = '', $file_type = -1, $start_time = '', $end_time = '', $page = -1, $count = 20) {
        $user_id = -1;
        switch ($user_type) {
            case 1:
                $admin_model = new Admin();
                $admin = $admin_model->fetchOne([], ['username' => $user_name]);
                $user_id = $admin ? $admin['admin_id'] : -1;
                break;
            case 2:
                $users_model = new User();
                $user = $users_model->fetchOne([], ['username' => $user_name]);
                $user_id = $user ? $user['user_id'] : -1;
                break;
        }
        if (strlen($start_time) > 0 && !Validator::is_date($start_time, 'Y-m-d H:i:s')) {
            YCore::exception(-1, '开始时间查询有误');
        }
        if (strlen($end_time) > 0 && !Validator::is_date($end_time, 'Y-m-d H:i:s')) {
            YCore::exception(-1, '结束时间查询有误');
        }
        $files_model = new Files();
        $result = $files_model->getList($user_type, $user_id, $file_md5, $file_type, $start_time, $end_time, $page, $count);
        foreach ($result['list'] as $key => $item) {
            $item['file_type_label'] = $item['file_type'] == 1 ? '图片' : '其他文件';
            $item['user_name']       = '-';
            $item['user_type_label'] = '-';
            if ($item['user_type'] == 1) {
                $admin_model = new Admin();
                $admin = $admin_model->fetchOne([], ['admin_id' => $item['user_id']]);
                $item['user_name'] = $admin ? "{$admin['realname']}[{$admin['username']}]" : '';
                $item['user_type_label'] = '管理员';
            } else if ($item['user_type'] == 2) {
                $users_model = new User();
                $user = $users_model->fetchOne([], ['user_id' => $item['user_id']]);
                $item['user_name'] = $user ? "{$user['username']}" : '';
                $item['user_type_label'] = '普通用户';
            }
            $item['created_time'] = YCore::format_timestamp($item['created_time']);
            $result['list'][$key] = $item;
        }
        return $result;
    }

    /**
     * 文件删除。
     *
     * @param number $file_id 文件ID。
     * @param number $admin_id 管理员ID。
     * @return boolean
     */
    public static function deleteFile($file_id, $admin_id) {
        $files_model = new Files();
        $file = $files_model->fetchOne([], ['file_id' => $file_id, 'status' => 1]);
        if (empty($file)) {
            YCore::exception(-1, '文件不存在或已经删除');
        }
        return $files_model->deleteFile($file_id);
    }
}