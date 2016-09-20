<?php

/**
 * 日志管理。
 * @author winerQin
 * @date 2016-3-15
 */
namespace services;

use models\Log;
use models\Admin;
use common\YCore;
use models\User;

class LogService extends BaseService {

    /**
     * 获取日志列表数据。
     * -- Example start --
     * $options = [
     *      'log_type'  => '日志类型。必传。',
     *      'user_type' => '用户类型：1管理员、2普通用户',
     *      'log_user'  => '触发日志的用户',
     *      'starttime' => '开始时间。必传。',
     *      'endtime'   => '结束时间。必传。',
     *      'errcode'   => '错误码。必传。',
     *      'content'   => '日志内容。必传。',
     *      'page'      => '当前页码。必传。',
     *      'count'     => '每页显示条数。必传。',
     * ];
     * -- Example end --
     *
     * @param array $options 参数。
     * @return array
     */
    public static function getLogList($options) {
        $log_type = $options['log_type'];
        $errcode  = $options['errcode'];
        $page  = $options['page'];
        $count = $options['count'];
        $user_id = 0;
        if ($options['log_user'] > 0) {
            if ($options['user_type'] == 1) {
                $admin_model  = new Admin();
                $admin_detail = $admin_model->getUserOfByUsername($options['log_user']);
                $user_id      = $admin_detail ? $admin_detail['admin_id'] : - 1;
            } else {
                $user_model  = new User();
                $user_detail = $user_model->getUserOfByUsername($options['log_user']);
                $user_id     = $user_detail ? $user_detail['user_id'] : - 1;
            }
        }
        $starttime = 0;
        $endtime = 0;
        if (strlen($options['starttime']) > 0 && strlen($options['endtime']) > 0) {
            if ($options['starttime'] > $options['endtime']) {
                YCore::exception(- 1, '开始时间必须小于等于结束时间');
            }
            $starttime = strtotime($options['starttime']);
            $endtime   = strtotime($options['endtime']);
        }

        $log_model = new Log();
        return $log_model->getDictTypeList($log_type, $user_id, $errcode, $starttime, $endtime, $page, $count);
    }

    /**
     * 获取日志详情。
     *
     * @param int $log_id 日志ID。
     * @return array
     */
    public static function getLogDetail($log_id) {
        $log_model = new Log();
        $detail = $log_model->fetchOne([], [
            'log_id' => $log_id
        ]);
        if (empty($detail)) {
            YCore::exception(- 1, '日志不存在或已经删除');
        }
        return $detail;
    }

}