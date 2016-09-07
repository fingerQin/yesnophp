<?php
/**
 * 微信相关操作封装。
 * @author winerQin
 * @date 2016-05-23
 */

namespace services;

use models\WxAccount;
use common\YCore;
use winer\Validator;
use models\WxMenu;
use models\WxNews;
use models\WxNewsItem;
use models\DbBase;
use winer\WeChat\Message\News;
class WeChatService extends BaseService {
    
    /**
     * 获取公众号列表。
     * 
     * @param string $wx_account 公众号名称。
     * @param string $wx_appid 公众号APPID。
     * @param number $page
     * @param number $count
     */
    public static function getWxAccountList($wx_account = '', $sn = '', $wx_appid = '', $page = 1, $count = 20) {
        $wx_account_model = new WxAccount();
        return $wx_account_model->getList($wx_account, $sn, $wx_appid, $page, $count);
    }
    
    /**
     * 添加公众号。
     * -- Example start --
     * $data = [
     * 'admin_id' => '管理员ID',
     * 'wx_sn' => '公众号编码',
     * 'wx_account' => '公众号微信号',
     * 'wx_type' => '公众号类型',
     * 'wx_auth' => '公众号是否认证。1是、0否。',
     * 'wx_appid' => '微信公众号appid',
     * 'wx_appsecret' => '微信公众号密钥',
     * 'wx_token' => '公众号Token',
     * 'wx_aeskey' => '公众号EncodingAESKey',
     * 'wx_cert_path' => '公众号支付证书地址',
     * 'wx_cert_key' => '公众号支付密钥地址',
     * 'wx_report_level' => '微信支付上报等级',
     * 'wx_proxy_host' => '支付代理HOST',
     * 'wx_proxy_port' => '支付代理端口',
     * ];
     * -- Example end --
     * 
     * @param array $data 公众号数据。
     * @return boolean
     */
    public static function addWxAccount($data) {
        $rules = [
                'admin_id' => '管理员ID|require:1000000',
                'wx_sn' => '公众号编码|require:1000000|alpha_dash:1000000|len:1000000:1:32:1',
                'wx_account' => '公众号微信号|require:1000000|alpha_dash:1000000|len:1000000:1:80:1',
                'wx_type' => '公众号类型|require:1000000|number_between:1000000:1:3',
                'wx_auth' => '公众号是否认证|require:1000000|number_between:1000000:0:1',
                'wx_appid' => '微信公众号appid|require:1000000|len:1000000:1:50:1',
                'wx_appsecret' => '微信公众号密钥|require:1000000|len:1000000:1:50:1',
                'wx_token' => '公众号Token|require:1000000|len:1000000:1:32:1',
                'wx_aeskey' => '公众号EncodingAESKey|require:1000000|len:1000000:1:43:1',
                'wx_cert_path' => '公众号支付证书地址|len:1000000:1:100:1','wx_cert_key' => '公众号支付密钥地址|len:1000000:1:100:1',
                'wx_report_level' => '微信支付上报等级|require:1000000|number_between:1000000:0:5',
                'wx_proxy_host' => '支付代理HOST|require:1000000|len:1000000:1:20:1',
                'wx_proxy_port' => '支付代理端口|require:1000000|number_between:1000000:0:100000000' 
        ];
        Validator::valido($data, $rules);
        $account_model = new WxAccount();
        $where = [
                'wx_sn' => $data['wx_sn'],'status' => 1 
        ];
        $result = $account_model->fetchOne([], $where);
        if (! empty($result)) {
            YCore::exception(- 1, '公众号编码已经被占用');
        }
        $data['created_time'] = $_SERVER['REQUEST_TIME'];
        $data['created_by'] = $data['admin_id'];
        $data['status'] = 1;
        unset($data['admin_id']);
        $account_id = $account_model->insert($data);
        if ($account_id == 0) {
            YCore::exception(- 1, '服务器繁忙,请稍候重试');
        }
        return true;
    }
    
    /**
     * 编辑公众号。
     * -- Example start --
     * $data = [
     * 'account_id' => '公众号ID',
     * 'admin_id' => '管理员ID',
     * 'wx_sn' => '公众号编码',
     * 'wx_account' => '公众号微信号',
     * 'wx_type' => '公众号类型',
     * 'wx_auth' => '公众号是否认证。1是、0否。',
     * 'wx_appid' => '微信公众号appid',
     * 'wx_appsecret' => '微信公众号密钥',
     * 'wx_token' => '公众号Token',
     * 'wx_aeskey' => '公众号EncodingAESKey',
     * 'wx_cert_path' => '公众号支付证书地址',
     * 'wx_cert_key' => '公众号支付密钥地址',
     * 'wx_report_level' => '微信支付上报等级',
     * 'wx_proxy_host' => '支付代理HOST',
     * 'wx_proxy_port' => '支付代理端口',
     * ];
     * -- Example end --
     * 
     * @param array $data 公众号数据。
     * @return boolean
     */
    public static function editWxAccount($data) {
        $rules = [
                'account_id' => '公众号ID|require:1000000','admin_id' => '管理员ID|require:1000000',
                'wx_sn' => '公众号编码|require:1000000|alpha_dash:1000000|len:1000000:1:32:1',
                'wx_account' => '公众号微信号|require:1000000|alpha_dash:1000000|len:1000000:1:80:1',
                'wx_type' => '公众号类型|require:1000000|number_between:1000000:1:3',
                'wx_auth' => '公众号是否认证|require:1000000|number_between:1000000:0:1',
                'wx_appid' => '微信公众号appid|require:1000000|len:1000000:1:50:1',
                'wx_appsecret' => '微信公众号密钥|require:1000000|len:1000000:1:50:1',
                'wx_token' => '公众号Token|require:1000000|len:1000000:1:32:1',
                'wx_aeskey' => '公众号EncodingAESKey|require:1000000|len:1000000:1:43:1',
                'wx_cert_path' => '公众号支付证书地址|len:1000000:1:100:1','wx_cert_key' => '公众号支付密钥地址|len:1000000:1:100:1',
                'wx_report_level' => '微信支付上报等级|require:1000000|number_between:1000000:0:5',
                'wx_proxy_host' => '支付代理HOST|require:1000000|len:1000000:1:20:1',
                'wx_proxy_port' => '支付代理端口|require:1000000|number_between:1000000:0:100000000' 
        ];
        Validator::valido($data, $rules);
        $account_model = new WxAccount();
        $where = [
                'account_id' => $data['account_id'],'status' => 1 
        ];
        $account_detail = $account_model->fetchOne([], $where);
        if (empty($account_detail)) {
            YCore::exception(- 1, '公众号不存在或已经删除');
        }
        $where = [
                'wx_sn' => $data['wx_sn'],'status' => 1 
        ];
        $result = $account_model->fetchOne([], $where);
        if (! empty($result) && $data['wx_sn'] != $account_detail['wx_sn']) {
            YCore::exception(- 1, '公众号编码已经被占用');
        }
        $data['modified_time'] = $_SERVER['REQUEST_TIME'];
        $data['modified_by'] = $data['admin_id'];
        unset($data['admin_id']);
        $ok = $account_model->update($data, [
                'account_id' => $data['account_id'] 
        ]);
        if (! $ok) {
            YCore::exception(- 1, '服务器繁忙,请稍候重试');
        }
        return true;
    }
    
    /**
     * 获取公众号详情。
     * 
     * @param number $account_id 公众号ID。
     * @return array
     */
    public static function getWxAccountDetail($account_id) {
        $account_model = new WxAccount();
        $where = [
                'account_id' => $account_id,'status' => 1 
        ];
        $detail = $account_model->fetchOne([], $where);
        if (empty($detail)) {
            YCore::exception(- 1, '公众号不存在或已经删除');
        }
        return $detail;
    }
    
    /**
     * 删除微信公众号。
     * 
     * @param number $admin_id 管理员ID。
     * @param number $account_id 公众号ID。
     * @return boolean
     */
    public static function deleteWxAccount($admin_id, $account_id) {
        $account_model = new WxAccount();
        $data = [
                'status' => 2,'modified_by' => $admin_id,'modified_time' => $_SERVER['REQUEST_TIME'] 
        ];
        $where = [
                'account_id' => $account_id 
        ];
        $ok = $account_model->update($data, $where);
        if (! $ok) {
            YCore::exception(- 1, '删除失败');
        }
        return true;
    }
    
    /**
     * 获取公众号菜单。
     * 
     * @param number $account_id 公众号ID。
     * @return array
     */
    public static function getAccountMenu($account_id) {
    
    }
    
    /**
     * 添加公众号菜单。
     * 
     * @param number $admin_id 管理员ID。
     * @param number $account_id 公众号ID。
     * @param number $parent_id 父菜单ID。
     * @param string $menu_name 菜单名称。
     * @param number $menu_type 菜单类型。click、view。
     * @param string $menu_key 菜单KEY。当菜单类型为click的时候。菜单KEY必须设置。
     * @param number $is_outside 是否站外链接。0否、1是。
     * @param string $outside_url 站外链接URL。
     * @param string $module_name 站内链接模块部分。
     * @param string $ctrl_name 站内链接控制器部分。
     * @param string $action_name 站内链接操作部分。
     * @param string $url_query 站内链接URL参数部分。
     * @param number $display 是否显示。如果为1则微信菜单会显示出来。0不显示出来。
     * @return boolean
     */
    public static function addAccountMenu($admin_id, $account_id, $parent_id, $menu_name, $menu_type = '', $menu_key = '', $is_outside = 0, $outside_url = '', $module_name = '', $ctrl_name = '', $action_name = '', $url_query = '', $display = 1) {
        $data = [
                'admin_id' => $admin_id,'parent_id' => $parent_id,'menu_name' => $menu_name,'menu_type' => $menu_key,
                'menu_key' => $menu_key,'is_outside' => $is_outside,'outside_url' => $outside_url,
                'module_name' => $module_name,'ctrl_name' => $ctrl_name,'action_name' => $action_name,
                'url_query' => $url_query,'dispay' => $display 
        ];
        $rules = [
                'admin_id' => '管理员ID|require:1000000','parent_id' => '父菜单ID|require:1000000',
                'menu_name' => '菜单名称|require:1000000|len:1000000:1:4:1','menu_type' => '菜单类型|require:1000000',
                'menu_key' => '菜单key|alpha_dash:1000000',
                'is_outside' => '是否站外链接|require:1000000|number_between:1000000:0:1','outside_url' => '站外链接|url:1000000',
                'module_name' => '模块名称|alpha_dash:1000000','ctrl_name' => '控制器名称|alpha_dash:1000000',
                'action_name' => '操作名称|alpha_dash:1000000','url_query' => '模块名称|len:1000000:0:1000000:1',
                'dispay' => '模块名称|number_between:1000000:0:1' 
        ];
        Validator::valido($data, $rules);
        $data['created_time'] = $_SERVER['REQUEST_TIME'];
        $data['created_by'] = $data['admin_id'];
        unset($data['admin_id'], $rules);
        $menu_model = new WxMenu();
        if ($parent_id > 0) {
            $where = [
                    'account_id' => $account_id,'menu_id' => $parent_id,'status' => 1 
            ];
            $detail = $menu_model->fetchOne([], $where);
            if (empty($detail)) {
                YCore::exception(- 1, '父菜单不存在或已经删除');
            }
            if ($detail['menu_level'] == 2) {
                YCore::exception(- 1, '微信只允许两级菜单');
            }
        }
        $account_detail = $this->getWxAccountDetail($account_id);
        $menu_id = $menu_model->insert($data);
        if ($menu_id == 0) {
            YCore::exception(- 1, '服务器繁忙,请稍候重试');
        }
        return true;
    }
    
    /**
     * 修改公众号菜单。
     * 
     * @param number $admin_id 管理员ID。
     * @param number $parent_id 父菜单ID。
     * @param string $menu_name 菜单名称。
     * @param number $menu_type 菜单类型。click、view。
     * @param string $menu_key 菜单KEY。当菜单类型为click的时候。菜单KEY必须设置。
     * @param number $is_outside 是否站外链接。0否、1是。
     * @param string $outside_url 站外链接URL。
     * @param string $module_name 站内链接模块部分。
     * @param string $ctrl_name 站内链接控制器部分。
     * @param string $action_name 站内链接操作部分。
     * @param string $url_query 站内链接URL参数部分。
     * @param number $display 是否显示。如果为1则微信菜单会显示出来。0不显示出来。
     * @return boolean
     */
    public static function editAccountMenu($menu_id, $admin_id, $parent_id, $menu_name, $menu_type = '', $menu_key = '', $is_outside = 0, $outside_url = '', $module_name = '', $ctrl_name = '', $action_name = '', $url_query = '', $display = 1) {
        $data = [
                'admin_id' => $admin_id,'parent_id' => $parent_id,'menu_name' => $menu_name,'menu_type' => $menu_key,
                'menu_key' => $menu_key,'is_outside' => $is_outside,'outside_url' => $outside_url,
                'module_name' => $module_name,'ctrl_name' => $ctrl_name,'action_name' => $action_name,
                'url_query' => $url_query,'dispay' => $display 
        ];
        $rules = [
                'admin_id' => '管理员ID|require:1000000','parent_id' => '父菜单ID|require:1000000',
                'menu_name' => '菜单名称|require:1000000|len:1000000:1:4:1','menu_type' => '菜单类型|require:1000000',
                'menu_key' => '菜单key|alpha_dash:1000000',
                'is_outside' => '是否站外链接|require:1000000|number_between:1000000:0:1','outside_url' => '站外链接|url:1000000',
                'module_name' => '模块名称|alpha_dash:1000000','ctrl_name' => '控制器名称|alpha_dash:1000000',
                'action_name' => '操作名称|alpha_dash:1000000','url_query' => '模块名称|len:1000000:0:1000000:1',
                'dispay' => '模块名称|number_between:1000000:0:1' 
        ];
        Validator::valido($data, $rules);
        $data['modified_time'] = $_SERVER['REQUEST_TIME'];
        $data['modified_by'] = $data['admin_id'];
        unset($data['admin_id'], $rules);
        $menu_model = new WxMenu();
        if ($parent_id > 0) {
            $where = [
                    'menu_id' => $parent_id,'status' => 1 
            ];
            $detail = $menu_model->fetchOne([], $where);
            if (empty($detail)) {
                YCore::exception(- 1, '父菜单不存在或已经删除');
            }
            if ($detail['menu_level'] == 2) {
                YCore::exception(- 1, '微信只允许两级菜单');
            }
        }
        $menu_id = $menu_model->update($data, [
                'menu_id' => $menu_id 
        ]);
        if ($menu_id == 0) {
            YCore::exception(- 1, '服务器繁忙,请稍候重试');
        }
        return true;
    }
    
    /**
     * 删除公众号菜单。
     * 
     * @param number $admin_id 管理员ID。
     * @param number $menu_id 菜单ID。
     * @return boolean
     */
    public static function deleteAccountMenu($admin_id, $menu_id) {
        $menu_model = new WxMenu();
        $where = [
                'menu_id' => $menu_id,'status' => 1 
        ];
        $menu_detail = $menu_model->fetchOne([], $where);
        if (empty($menu_detail)) {
            YCore::exception(- 1, '公众号菜单不存在或已经删除');
        }
        $data = [
                'status' => 2,'modified_by' => $admin_id,'modified_time' => $_SERVER['REQUEST_TIME'] 
        ];
        $ok = $menu_model->update($data, $where);
        if (! $ok) {
            YCore::exception(- 1, '服务器繁忙,请稍候重试');
        }
        return true;
    }
    
    /**
     * 同步公众号菜单到微信。
     * 
     * @param number $account_id 公众号ID。
     * @return boolean
     */
    public static function syncAccountMenu($account_id) {
    
    }
    
    /**
     * 获取图文消息列表。
     * 
     * @param number $account_id 公众号ID。
     * @param string $title 图文消息标题。只作不同图文消息之间区别之用。
     * @param string $start_push_time 推送时间开始。
     * @param string $end_push_time 推送时间结束。
     * @param number $is_push 是事推送。1是、0否、-1全部。
     * @param string $starttime 创建时间开始。
     * @param string $endtime 创建时间结束。
     * @param number $page 当前页码。
     * @param number $count 每页显示条数。
     * @return array
     */
    public static function getNewsList($account_id, $title = '', $start_push_time = '', $end_push_time = '', $starttime = '', $is_push = -1, $endtime = '', $page = 1, $count = 20) {
        $news_model = new WxNews();
        return $news_model->getList($account_id, $title, $start_push_time, $end_push_time, $starttime, $is_push, $endtime, $page, $count);
    }
    
    /**
     * 添加图文消息。
     * -- Example start --
     * $data = [
     * 'account_id' => '公众号ID',
     * 'admin_id' => '管理员ID',
     * 'news_title' => '图文消息标题',
     * 'news_item' => [
     * [
     * 'title' => '文章标题',
     * 'desc' => '文章描述',
     * 'image_url' => '文章图片URL',
     * 'news_url' => '文章URL'
     * ],
     * [
     * 'title' => '文章标题',
     * 'desc' => '文章描述',
     * 'image_url' => '文章图片URL',
     * 'news_url' => '文章URL'
     * ]
     * ],
     * ];
     * -- Example end --
     * 
     * @param array $data 图文消息内容。
     * @return boolean
     */
    public static function addNews($data) {
        if (! Validator::is_len($data['news_title'], 1, 100, 1)) {
            YCore::exception(- 1, '图文消息主标题必须填写且长度1到100个字之间');
        }
        if (count($data['news_item']) === 0) {
            YCore::exception(- 1, '图文文章不能为空');
        }
        $rules = [
                'title' => '文章标题|require:1000000|len:1000000:1:30:1',
                'desc' => '文章描述|require:1000000|len:1000000:1:100:1',
                'image_url' => '文章图片URL|require:1000000|url:1000000|len:1000000:1:100:1',
                'news_url' => '文章URLURL|require:1000000|url:1000000|len:1000000:1:100:1' 
        ];
        foreach ($data['news_item'] as $news_item) {
            Validator::valido($news_item, $rules);
        }
        $account_model = new WxAccount();
        $account_detail = $account_model->fetchOne([], [
                'account_id' => $data['account_id'],'status' => 1 
        ]);
        if (empty($account_detail)) {
            YCore::exception(- 1, '公众号不存在或已经删除');
        }
        $default_db = new DbBase();
        $news_model = new WxNews();
        $news_data = [
                'account_id' => $data['account_id'],'title' => $data['news_title'],'status' => 1,
                'created_time' => $_SERVER['REQUEST_TIME'],'created_by' => $data['admin_id'] 
        ];
        $default_db->beginTransaction();
        $news_id = $news_model->insert($news_data);
        if ($news_id == 0) {
            $default_db->rollBack();
            YCore::exception(- 1, '服务器繁忙,请稍候重试');
        }
        $news_item_model = new WxNewsItem();
        foreach ($data['news_item'] as $news_item) {
            $insert_data = [
                    'news_id' => $data['news_id'],'title' => $data['title'],'description' => $data['desc'],
                    'image_url' => $data['image_url'],'news_url' => $data['news_url'],'status' => 1,
                    'created_time' => $_SERVER['REQUEST_TIME'],'created_by' => $data['admin_id'] 
            ];
            $item_id = $news_item_model->insert($insert_data);
            if ($item_id == 0) {
                $default_db->rollBack();
                YCore::exception(- 1, '图文消息文章明细设置失败');
            }
        }
        $default_db->commit();
        return true;
    }
    
    /**
     * 编辑图文消息。
     * -- Example start --
     * $data = [
     * 'news_id' => '图文消息ID',
     * 'admin_id' => '管理员ID',
     * 'news_title' => '图文消息标题',
     * 'news_item' => [
     * [
     * 'title' => '文章标题',
     * 'desc' => '文章描述',
     * 'image_url' => '文章图片URL',
     * 'news_url' => '文章URL'
     * ],
     * [
     * 'title' => '文章标题',
     * 'desc' => '文章描述',
     * 'image_url' => '文章图片URL',
     * 'news_url' => '文章URL'
     * ]
     * ],
     * ];
     * -- Example end --
     * 
     * @param array $data 图文消息内容。
     * @return boolean
     */
    public static function editNews($data) {
        if (! Validator::is_len($data['news_title'], 1, 100, 1)) {
            YCore::exception(- 1, '图文消息主标题必须填写且长度1到100个字之间');
        }
        if (count($data['news_item']) === 0) {
            YCore::exception(- 1, '图文文章不能为空');
        }
        $rules = [
                'title' => '文章标题|require:1000000|len:1000000:1:30:1',
                'desc' => '文章描述|require:1000000|len:1000000:1:100:1',
                'image_url' => '文章图片URL|require:1000000|url:1000000|len:1000000:1:100:1',
                'news_url' => '文章URLURL|require:1000000|url:1000000|len:1000000:1:100:1' 
        ];
        foreach ($data['news_item'] as $news_item) {
            Validator::valido($news_item, $rules);
        }
        $default_db = new DbBase();
        $news_model = new WxNews();
        $where = [
                'news_id' => $data['news_id'],'status' => 1 
        ];
        $news_detail = $news_model->fetchOne([], $where);
        if (empty($news_detail)) {
            YCore::exception(- 1, '该图文消息不存在');
        }
        $news_data = [
                'title' => $data['news_title'],'modified_time' => $_SERVER['REQUEST_TIME'],
                'modified_by' => $data['admin_id'] 
        ];
        $default_db->beginTransaction();
        $ok = $news_model->update($news_data, $where);
        if (! $ok) {
            $default_db->rollBack();
            YCore::exception(- 1, '服务器繁忙,请稍候重试');
        }
        $news_item_model = new WxNewsItem();
        $update_data = [
                'status' => 2,'modified_time' => $_SERVER['REQUEST_TIME'],'modified_by' => $data['admin_id'] 
        ];
        $where = [
                'news_id' => $data['news_id'] 
        ];
        $news_item_model->update($update_data, $where);
        foreach ($data['news_item'] as $news_item) {
            $insert_data = [
                    'news_id' => $data['news_id'],'title' => $data['title'],'description' => $data['desc'],
                    'image_url' => $data['image_url'],'news_url' => $data['news_url'],'status' => 1,
                    'created_time' => $_SERVER['REQUEST_TIME'],'created_by' => $data['admin_id'] 
            ];
            $item_id = $news_item_model->insert($insert_data);
            if ($item_id == 0) {
                $default_db->rollBack();
                YCore::exception(- 1, '图文消息文章明细设置失败');
            }
        }
        $default_db->commit();
        return true;
    }
    
    /**
     * 删除图文消息。
     * 
     * @param number $news_id 图文消息ID。
     * @param number $admin_id 管理员ID。
     * @return boolean
     */
    public static function deleteNews($news_id, $admin_id) {
        $news_model = new WxNews();
        $data = [
                'status' => 2,'modified_by' => $admin_id,'modified_time' => $_SERVER['REQUEST_TIME'] 
        ];
        $where = [
                'news_id' => $news_id 
        ];
        $ok = $news_model->update($data, $where);
        if (! $ok) {
            YCore::exception(- 1, '服务器繁忙,请稍候重试');
        }
        return true;
    }
    
    /**
     * 推送图文消息到微信公众号。
     * 
     * @param number $news_id 图文消息ID。
     * @param number $admin_id 管理员ID。
     * @return boolean
     */
    public static function pushNewsToWeChat($news_id, $admin_id) {
        $news_model = new WxNews();
        $where = [
                'news_id' => $news_id,'status' => 1 
        ];
        $news_detail = $news_model->fetchOne([], $where);
        if (empty($news_detail)) {
            YCore::exception(- 1, '图文消息不存在或已经删除');
        }
        $news_item_model = new WxNewsItem();
        $columns = [
                'title','description','image_url','news_url' 
        ];
        $news_item_list = $news_item_model->fetchAll($columns, $where);
        if (empty($news_item_list)) {
            YCore::exception(- 1, '图文消息明细必须设置');
        }
        if (count($news_item_list) > 10) {
            YCore::exception(- 1, '图文消息明细不能超过10条');
        }
        $news_detail = [];
        foreach ($news_item_list as $item) {
            $article = [
                    'Title' => $item['title'],'Description' => $item['description'],'PicUrl' => $item['image_url'],
                    'Url' => $item['news_url'] 
            ];
            $news_detail[] = $article;
        }
        $wechat_app = YCore::getWeChatApp();
        $newsObj = new News($news_detail);
        // 群发图文。
        // $wechat_app->sendMessage($newsObj);
        return true;
    }
}