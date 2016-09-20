<?php
/**
 * 评论业务封装。
 * @author winerQin
 * @date 2016-07-21
 */
namespace services;

use models\MallOrder;
use common\YCore;
use models\MallOrderItem;
use winer\Validator;
use models\MallComment;
use models\DbBase;
use models\MallAppraise;
use models\User;

class AppraiseService extends BaseService {

    /**
     * 评价等级字典。
     *
     * @var array
     */
    public static $evaluate_level_dict = [
        1 => '好评',
        2 => '中评',
        3 => '差评'
    ];

    /**
     * 管理后台获取用户评价列表。
     *
     * @param number $goods_id 商品ID。
     * @param string $order_sn 订单号。
     * @param number $evaluate_level 好评等级。
     * @param string $username 评价人账号。
     * @param string $mobilephone 评价人手机号。
     * @param number $page 当前分页页码。
     * @param number $count 每页显示条数。
     * @return array
     */
    public static function getBackendAppraiseList($goods_id = -1, $order_sn = '', $evaluate_level = -1, $username = '', $mobilephone = '', $page = 1, $count = 20) {
        $offset  = self::getPaginationOffset($page, $count);
        $columns = ' cid,sub_order_id,evaluate_level,content1,content1_time,reply1,reply1_time,content2,content2_time,reply2,reply2_time ';
        $where   = ' WHERE 1 ';
        $params  = [];
        if ($goods_id != - 1) {
            $where .= ' AND goods_id = :goods_id ';
            $params[':goods_id'] = $goods_id;
        }
        if (strlen($order_sn) > 0) {
            $order_model  = new MallOrder();
            $order_detail = $order_model->fetchOne([], ['order_sn' => $order_sn]);
            if (empty($order_detail)) {
                $order_id = - 1;
            } else {
                $order_id = $order_detail['order_id'];
            }
            $where .= ' AND order_id = :order_id ';
            $params[':order_id'] = $order_id;
        }
        if ($evaluate_level != - 1) {
            $where .= ' AND evaluate_level = :evaluate_level ';
            $params[':evaluate_level'] = $evaluate_level;
        }
        $user_model = new User();
        if (strlen($username) === 0) {
            $userinfo = $user_model->fetchOne([], ['username' => $username]);
            if (empty($userinfo)) {
                $user_id = - 1;
            } else {
                $user_id = $userinfo['user_id'];
            }
            $where .= ' AND user_id = :user_id ';
            $params[':user_id'] = $user_id;
        } else if (strlen($mobilephone) === 0) {
            $userinfo = $user_model->fetchOne([], ['mobilephone' => $mobilephone]);
            if (empty($userinfo)) {
                $user_id = - 1;
            } else {
                $user_id = $userinfo['user_id'];
            }
            $where .= ' AND user_id = :user_id ';
            $params[':user_id'] = $user_id;
        }
        $order_by = ' ORDER BY cid DESC ';
        $sql = "SELECT COUNT(1) AS count FROM mall_comment {$where}";
        $default_db = new DbBase();
        $count_data = $default_db->rawQuery($sql, $params)->rawFetchOne();
        $total = $count_data ? $count_data['count'] : 0;
        $sql   = "SELECT {$columns} FROM mall_comment {$where} {$order_by} LIMIT {$offset},{$count}";
        $list  = $default_db->rawQuery($sql, $params)->rawFetchAll();
        $users = []; // 循环过程取得的用户信息。
        foreach ($list as $k => $v) {
            $item_detail = OrderService::getOrderItem($v['sub_order_id']);
            $v['goods_name']           = $item_detail['goods_name'];
            $v['goods_image']          = $item_detail['goods_image'];
            $v['goods_id']             = $item_detail['goods_id'];
            $v['product_id']           = $item_detail['product_id'];
            $v['spec_val']             = $item_detail['spec_val'];
            $v['market_price']         = $item_detail['market_price'];
            $v['sales_price']          = $item_detail['sales_price'];
            $v['quantity']             = $item_detail['quantity'];
            $v['comment_status']       = $item_detail['comment_status'];
            $v['reply_status']         = $item_detail['reply_status'];
            $v['content1_time']        = YCore::format_timestamp($v['content1_time']);
            $v['reply1_time']          = YCore::format_timestamp($v['reply1_time']);
            $v['content2_time']        = YCore::format_timestamp($v['content2_time']);
            $v['reply2_time']          = YCore::format_timestamp($v['reply2_time']);
            $v['evaluate_level_label'] = self::$evaluate_level_dict[$v['evaluate_level']];
            // 用户信息。
            if (array_key_exists($v['user_id'], $users)) {
                $userinfo = $users[$v['user_id']];
            } else {
                $userinfo = $user_model->fetchOne([], $where);
                $users[$v['user_id']] = $userinfo;
            }
            $v['username']    = $userinfo['username'];
            $v['mobilephone'] = $userinfo['mobilephone'];
            $list[$k] = $v;
        }
        $result = [
            'list'   => $list,
            'total'  => $total,
            'page'   => $page,
            'count'  => $count,
            'isnext' => self::IsHasNextPage($total, $page, $count)
        ];
        return $result;
    }

    /**
     * 买家评价列表。
     *
     * @param number $user_id 买家用户ID。
     * @param number $page 当前页码。
     * @param number $count 每页显示条数。
     * @return boolean
     */
    public static function getBuyerAppraiseList($user_id, $page = 1, $count = 20) {
        $offset = self::getPaginationOffset($page, $count);
        $columns = ' cid,sub_order_id,evaluate_level,content1,content1_time,reply1,reply1_time,content2,content2_time,reply2,reply2_time ';
        $where = ' WHERE 1 ';
        $params = [];
        $order_by = ' ORDER BY cid DESC ';
        $sql = "SELECT COUNT(1) AS count FROM mall_comment {$where}";
        $default_db = new DbBase();
        $count_data = $default_db->rawQuery($sql, $params)->rawFetchOne();
        $total = $count_data ? $count_data['count'] : 0;
        $sql   = "SELECT {$columns} FROM mall_comment {$where} {$order_by} LIMIT {$offset},{$count}";
        $list  = $default_db->rawQuery($sql, $params)->rawFetchAll();
        foreach ($list as $k => $v) {
            $item_detail = OrderService::getOrderItem($v['sub_order_id']);
            $v['goods_name']           = $item_detail['goods_name'];
            $v['goods_image']          = $item_detail['goods_image'];
            $v['goods_id']             = $item_detail['goods_id'];
            $v['product_id']           = $item_detail['product_id'];
            $v['spec_val']             = $item_detail['spec_val'];
            $v['market_price']         = $item_detail['market_price'];
            $v['sales_price']          = $item_detail['sales_price'];
            $v['quantity']             = $item_detail['quantity'];
            $v['comment_status']       = $item_detail['comment_status'];
            $v['reply_status']         = $item_detail['reply_status'];
            $v['content1_time']        = YCore::format_timestamp($v['content1_time']);
            $v['reply1_time']          = YCore::format_timestamp($v['reply1_time']);
            $v['content2_time']        = YCore::format_timestamp($v['content2_time']);
            $v['reply2_time']          = YCore::format_timestamp($v['reply2_time']);
            $v['evaluate_level_label'] = self::$evaluate_level_dict[$v['evaluate_level']];
            $list[$k] = $v;
        }
        $result = [
            'list'   => $list,
            'total'  => $total,
            'page'   => $page,
            'count'  => $count,
            'isnext' => self::IsHasNextPage($total, $page, $count)
        ];
        return $result;
    }

    /**
     * 卖家隐藏商品评论。
     *
     * @param number $user_id 用户ID。
     * @param number $comment_id 评论ID。
     * @return boolean
     */
    public static function sellerHideComment($user_id, $comment_id) {
        $comment_model = new MallComment();
        $where = [
            'cid' => $comment_id
        ];
        $comment_info = $comment_model->fetchOne([], $where);
        if (empty($comment_info)) {
            YCore::exception(- 1, '评论不存在');
        }
        $data = [
            'is_display' => 0
        ];
        $ok = $comment_model->update($data, $where);
        if (! $ok) {
            YCore::exception(- 1, '操作失败');
        }
        return true;
    }

    /**
     * 买家评价商品。
     * -- Example start --
     * $goods_comment = [
     *  [
     *      'sub_order_id' => '子订单ID',
     *      'comment' => '评论内容',
     *      'evaluate_level' => '好评等级：1好评、2中评、3差评',
     *  ],
     *  ......
     * ];
     * -- Example end --
     *
     * @param number $user_id 买家用户ID。
     * @param number $order_id 订单ID。
     * @param number $score1 宝贝描述相符评分。
     * @param number $score2 卖家服务态度评分。
     * @param number $score3 物流服务质量评分。
     * @param array $goods_comment 商品评论。
     * @return boolean
     */
    public static function buyerAppraise($user_id, $order_id, $score1, $score2, $score3, $goods_comment) {
        $order_model = new MallOrder();
        $order_info  = $order_model->fetchOne([], ['order_id' => $order_id, 'user_id' => $user_id, 'status' => 1]);
        if (empty($order_id)) {
            YCore::exception(- 1, '订单不存在或已经删除');
        }
        $order_item_model = new MallOrderItem();
        $order_items = $order_item_model->fetchAll([], ['order_id' => $order_id]);
        if (count($order_items) != count($goods_comment)) {
            YCore::exception(- 1, '必须评价整张订单所有商品');
        }
        // [1] 验证子订单号是归属是否正确 & 评论内容长度。
        foreach ($goods_comment as $key => $comment) {
            if (strlen($comment['comment']) === 0) {
                YCore::exception(- 1, '评价内容不能为空');
            }
            if (! Validator::is_len($comment['comment'], 1, 200, 1)) {
                YCore::exception(- 1, '评论内容必须1~200个字符之间');
            }
            if (! Validator::is_integer($comment['evaluate_level']) || ! Validator::is_number_between($comment['evaluate_level'], 1, 3)) {
                YCore::exception(- 1, '商品好评度异常');
            }
            $is_exists = false;
            foreach ($order_items as $item) {
                if ($item['sub_order_id'] == $comment['sub_order_id']) {
                    $comment[$key]['goods_id']   = $item['goods_id'];
                    $comment[$key]['product_id'] = $item['product_id'];
                    $is_exists = true;
                }
            }
            if ($is_exists == false) {
                YCore::exception(- 1, '非法评价');
            }
        }
        // [2] 评分验证。
        $data = [
            'score1' => $score1,
            'score2' => $score2,
            'score3' => $score3
        ];
        $rules = [
            'score1' => '宝贝描述相符评分|require:1000000|integer:1000000|number_between:1000000:1:5',
            'score2' => '卖家服务态度评分|require:1000000|integer:1000000|number_between:1000000:1:5',
            'score3' => '物流服务质量评分|require:1000000|integer:1000000|number_between:1000000:1:5'
        ];
        Validator::valido($data, $rules);
        $default_db     = new DbBase();
        $appraise_model = new MallAppraise();
        $comment_model  = new MallComment();
        $default_db->beginTransaction();
        $ip2long = ip2long(YCore::ip());
        foreach ($goods_comment as $comment) {
            $data = [
                'order_id'       => $order_id,
                'sub_order_id'   => $comment['sub_order_id'],
                'goods_id'       => $comment['goods_id'],
                'product_id'     => $comment['product_id'],
                'evaluate_level' => $comment['evaluate_level'],
                'user_id'        => $user_id,
                'content1'       => $comment['comment'],
                'content1_time'  => $_SERVER['REQUEST_TIME'],
                'client_ip'      => $ip2long
            ];
            $cid = $comment_model->insert($data);
            if ($cid == 0) {
                $default_db->rollBack();
                YCore::exception(- 1, '服务器繁忙，请稍候重试');
            }
            $data = [
                'order_id'     => $order_id,
                'user_id'      => $user_id,
                'score1'       => $score1,
                'score2'       => $score2,
                'score3'       => $score3,
                'client_ip'    => $ip2long,
                'created_time' => $_SERVER['REQUEST_TIME']
            ];
            $aid = $appraise_model->insert($data);
            if ($aid == 0) {
                $default_db->rollBack();
                YCore::exception(- 1, '服务器繁忙,请稍候重试');
            }
            $updata = [
                'comment_status' => 1,
                'modified_time'  => $_SERVER['REQUEST_TIME'],
                'modified_by'    => $user_id
            ];
            $ok = $order_item_model->update($updata, ['sub_order_id' => $comment['sub_order_id']]);
            if (! $ok) {
                $default_db->rollBack();
                YCore::exception(- 1, '服务器繁忙,请稍候重试');
            }
        }
        $updata = [
            'comment_status' => 1,
            'modified_by'    => $user_id,
            'modified_time'  => $_SERVER['REQUEST_TIME']
        ];
        $ok = $order_model->update($updata, ['order_id' => $order_id]);
        if (! $ok) {
            $default_db->rollBack();
            YCore::exception(- 1, '服务器繁忙,请稍候重试');
        }
        $default_db->commit();
        return true;
    }

    /**
     * 买家追加评论。
     *
     * @param number $user_id 买家用户ID。
     * @param number $order_id 订单ID。
     * @param number $sub_order_id 子订单ID。
     * @param string $comment 评论内容。
     * @return boolean
     */
    public static function buyerAppendAppraise($user_id, $order_id, $sub_order_id, $comment) {
        if (strlen($comment) === 0) {
            YCore::exception(- 1, '评论内容必须填写');
        }
        if (! Validator::is_len($comment, 1, 200, true)) {
            YCore::exception(- 1, '评论内容必须1~200个字符之间');
        }
        $order_model = new MallOrder();
        $order_info = $order_model->fetchOne([], [
            'order_id' => $order_id,
            'user_id'  => $user_id,
            'status'   => 1
        ]);
        if (empty($order_id)) {
            YCore::exception(- 1, '订单不存在或已经删除');
        }
        $order_item_model = new MallOrderItem();
        $order_item_info = $order_item_model->fetchOne([], ['order_id' => $order_id, 'sub_order_id' => $sub_order_id]);
        if (empty($order_item_info)) {
            YCore::exception(- 1, '服务器繁忙,请稍候刷新重试');
        }
        $comment_model = new MallComment();
        $where = [
            'order_id'     => $order_id,
            'sub_order_id' => $sub_order_id,
            'user_id'      => $user_id
        ];
        $comment_info = $comment_model->fetchOne([], $where);
        if (empty($comment_info)) {
            YCore::exception(- 1, '评价异常,请稍候刷新重试');
        }
        $comment_model->beginTransaction();
        $updata = [
            'content2'      => $comment,
            'content2_time' => $_SERVER['REQUEST_TIME']
        ];
        $ok = $comment_model->update($updata, $where);
        if (! $ok) {
            $comment_model->rollBack();
            YCore::exception(- 1, '追加评价失败');
        }
        $updata = [
            'comment_status' => 2,
            'modified_by'    => $user_id,
            'modified_time'  => $_SERVER['REQUEST_TIME']
        ];
        $where = [
            'sub_order_id' => $sub_order_id
        ];
        $ok = $order_item_model->update($updata, $where);
        if (! $ok) {
            $comment_model->rollBack();
            YCore::exception(- 1, '追加评价失败');
        }
        $comment_model->commit();
        return true;
    }

    /**
     * 卖家评价回复。
     *
     * @param number $user_id 卖家ID。
     * @param number $sub_order_id 子订单ID。
     * @param number $reply_type 回复内容。1初评回复、2追评回复。
     * @param number $comment 评论内容。
     * @return boolean
     */
    public static function sellerAppraiseReply($user_id, $sub_order_id, $reply_type, $comment) {
        if (strlen($comment) === 0) {
            YCore::exception(- 1, '评论内容必须填写');
        }
        if (! Validator::is_len($comment, 1, 200, true)) {
            YCore::exception(- 1, '评论内容必须1~200个字符之间');
        }
        $order_item_model = new MallOrderItem();
        $order_item_info  = $order_item_model->fetchOne([], ['sub_order_id' => $sub_order_id]);
        if (empty($order_item_info)) {
            YCore::exception(- 1, '服务器繁忙,请稍候刷新重试');
        }
        $order_model = new MallOrder();
        $order_info  = $order_model->fetchOne([], ['order_id' => $order_item_info['order_id']]);
        if (empty($order_info)) {
            YCore::exception(- 1, '评价异常,请稍候刷新重试');
        }
        $comment_model = new MallComment();
        $where = [
            'sub_order_id' => $sub_order_id
        ];
        $comment_info = $comment_model->fetchOne([], $where);
        if (empty($comment_info)) {
            YCore::exception(- 1, '评价异常,请稍候刷新重试');
        }
        if ($reply_type == 1) { // 初评回复。
            if (strlen($comment_info['reply1']) !== 0) {
                YCore::exception(- 1, '您已经回复');
            }
            $updata = [
                'reply1'      => $comment,
                'reply1_time' => $_SERVER['REQUEST_TIME']
            ];
        } else {
            if (strlen($comment_info['reply2']) !== 0) {
                YCore::exception(- 1, '您已经追加回复');
            }
            $updata = [
                'reply2'      => $comment,
                'reply2_time' => $_SERVER['REQUEST_TIME']
            ];
        }
        $ok = $comment_model->update($updata, $where);
        if (! $ok) {
            YCore::exception(- 1, '回复失败');
        }
        return true;
    }

}