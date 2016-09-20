<?php
/**
 * IP相关业务封装。
 * @author winerQin
 * @date 2016-03-24
 */
namespace services;

use models\IpBan;
use common\YCore;
use winer\Validator;

class IpService extends BaseService {

    /**
     * 获取IP黑名单列表。
     *
     * @param string $keywords
     * @param number $page
     * @param number $count
     */
    public static function getIpBanList($keywords = '', $page = 1, $count = 20) {
        $ip_ban_model = new IpBan();
        return $ip_ban_model->geList($keywords, $page, $count);
    }

    /**
     * 添加IP黑名单。
     *
     * @param number $admin_id 管理员ID。
     * @param string $ip IP地址。
     * @param string $remark 备注。
     * @return boolean
     */
    public static function addIpBan($admin_id, $ip, $remark) {
        $data = self::getByIpToIpBanDetail($ip);
        if ($data) {
            YCore::exception(-1, '该IP地址已经存在');
        }
        if (!Validator::is_ip($ip)) {
            YCore::exception(-1, 'IP地址不正确');
        }
        if (!Validator::is_len($remark, 0, 200, true)) {
            YCore::exception(-1, '备注内容必须200字以内');
        }
        $ip_ban_model = new IpBan();
        return $ip_ban_model->addIp($admin_id, $ip, $remark);
    }

    /**
     * 编辑IP黑名单。
     *
     * @param number $id 配置ID。
     * @param number $admin_id 管理员ID。
     * @param string $ip IP地址。
     * @param string $remark 备注。
     * @return boolean
     */
    public static function editIpBan($id, $admin_id, $ip, $remark) {
        self::getIpBanDetail($id);
        $data = self::getByIpToIpBanDetail($ip);
        if ($data && $data['id'] != $id) {
            YCore::exception(-1, '该IP地址已经存在');
        }
        if (!Validator::is_ip($ip)) {
            YCore::exception(-1, 'IP地址不正确');
        }
        if (!Validator::is_len($remark, 0, 200, true)) {
            YCore::exception(-1, '备注内容必须200字以内');
        }
        $ip_ban_model = new IpBan();
        return $ip_ban_model->editIp($id, $admin_id, $ip, $remark);
    }

    /**
     * 删除IP黑名单。
     *
     * @param number $id ID。
     * @param number $admin_id 管理员ID。
     * @return boolean
     */
    public static function deleteIpBan($id, $admin_id) {
        self::getIpBanDetail($id);
        $ip_ban_model = new IpBan();
        return $ip_ban_model->deleteIp($id, $admin_id);
    }

    /**
     * 按IP获取黑名单记录。
     *
     * @param string $ip IP地址。
     * @return array
     */
    public static function getByIpToIpBanDetail($ip) {
        $ip_ban_model = new IpBan();
        $data = $ip_ban_model->fetchOne([], ['ip' => $ip]);
        return $data ? $data : [];
    }

    /**
     * 获取IP黑名单详情。
     *
     * @param number $id 黑名单记录ID.
     * @return array
     */
    public static function getIpBanDetail($id) {
        $ip_ban_model = new IpBan();
        $data = $ip_ban_model->fetchOne([], ['id' => $id]);
        if (empty($data)) {
            YCore::exception(-1, '记录不存在或已经删除');
        }
        return $data;
    }
}