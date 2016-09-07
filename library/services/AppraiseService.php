<?php
/**
 * 评论业务封装。
 * @author winerQin
 * @date 2016-06-13
 */

namespace services;

use models\MallOrder;
use common\YCore;
use models\MallOrderItem;
use winer\Validator;
use models\MallAppraiseDetail;
use models\MallComment;
use models\DbBase;
use models\MallAppraise;
class AppraiseService extends BaseService {
    
    /**
     * 买家评价列表。
     * 
     * @param number $user_id 买家用户ID。
     * @param number $page 当前页码。
     * @param number $count 每页显示条数。
     * @return boolean
     */
    public static function getBuyerAppraiseList($user_id, $page = 1, $count = 20) {
    
    }
    
    /**
     * 买家评价商品。
     * -- Example start --
     * $goods_comment = [
     * [
     * 'sub_order_id' => '子订单ID',
     * 'comment' => '评论内容',
     * ],
     * ......
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
        $order_info = $order_model->fetchOne([], [
                'order_id' => $order_id,'user_id' => $user_id,'status' => 1 
        ]);
        if (empty($order_id)) {
            YCore::exception(- 1, '订单不存在或已经删除');
        }
        $order_item_model = new MallOrderItem();
        $order_items = $order_item_model->fetchAll([], [
                'order_id' => $order_id 
        ]);
        if (count($order_items) != count($goods_comment)) {
            YCore::exception(- 1, '必须评价整张订单所有商品');
        }
        $arr_sub_order_id = [];
        foreach ($order_items as $item) {
            $arr_sub_order_id[] = $item['sub_order_id'];
        }
        // [1] 验证子订单号是归属是否正确 & 评论内容长度。
        foreach ($goods_comment as $key => $comment) {
            if (strlen($comment['comment']) === 0) {
                YCore::exception(- 1, '评价内容不能为空');
            }
            if (! Validator::is_len($comment['comment'], 1, 200, 1)) {
                YCore::exception(- 1, '评论内容必须1~200个字符之间');
            }
            $is_exists = false;
            foreach ($order_items as $item) {
                if ($item['sub_order_id'] == $comment['sub_order_id']) {
                    $comment[$key]['goods_id'] = $item['goods_id'];
                    $comment[$key]['product_id'] = $item['goods_id'];
                    $is_exists = true;
                }
            }
            if ($is_exists == false) {
                YCore::exception(- 1, '非法评价');
            }
        }
        // [2] 评分验证。
        $data = [
                'score1' => $score1,'score2' => $score2,'score3' => $score3 
        ];
        $rules = [
                'score1' => '宝贝描述相符评分|require:1000000|integer:1000000|number_between:1000000:1:5',
                'score2' => '卖家服务态度评分|require:1000000|integer:1000000|number_between:1000000:1:5',
                'score3' => '物流服务质量评分|require:1000000|integer:1000000|number_between:1000000:1:5' 
        ];
        Validator::valido($data, $rules);
        $default_db = new DbBase();
        $appraise_detail_model = new MallAppraiseDetail();
        $comment_model = new MallComment();
        $default_db->beginTransaction();
        foreach ($goods_comment as $comment) {
            $data = [
                    'shop_id' => $order_info['order_id'],'order_id' => $order_id,
                    'sub_order_id' => $comment['sub_order_id'],'goods_id' => $comment['goods_id'],
                    'product_id' => $comment['product_id'],'user_id' => $user_id,'content1' => $comment['comment'],
                    'content1_time' => $_SERVER['REQUEST_TIME'],'client_ip' => YCore::ip() 
            ];
            $cid = $comment_model->insert($data);
            if ($cid == 0) {
                $default_db->rollBack();
                YCore::exception(- 1, '服务器繁忙，请稍候重试');
            }
            $data = [
                    'shop_id' => $order_info['order_id'],'order_id' => $order_id,
                    'sub_order_id' => $comment['sub_order_id'],'goods_id' => $comment['goods_id'],
                    'product_id' => $comment['product_id'],'user_id' => $user_id,'score1' => $score1,
                    'score2' => $score2,'score3' => $score3,'client_ip' => YCore::ip(),
                    'created_time' => $_SERVER['REQUEST_TIME'] 
            ];
            $aid = $appraise_detail_model->insert($data);
            if ($aid == 0) {
                $default_db->rollBack();
                YCore::exception(- 1, '服务器繁忙,请稍候重试');
            }
            $updata = [
                    'comment_status' => 1,'modified_time' => $_SERVER['REQUEST_TIME'],'modified_by' => $user_id 
            ];
            $ok = $order_item_model->update($updata, [
                    'sub_order_id' => $comment['sub_order_id'] 
            ]);
            if (! $ok) {
                $default_db->rollBack();
                YCore::exception(- 1, '服务器繁忙,请稍候重试');
            }
        }
        try {
            self::statsShopAppraise($order_info['shop_id']);
        } catch ( \Exception $e ) {
            $default_db->rollBack();
            YCore::exception($e->getCode(), $e->getMessage());
        }
        $updata = [
                'comment_status' => 1,'modified_by' => $user_id,'modified_time' => $_SERVER['REQUEST_TIME'] 
        ];
        $ok = $order_model->update($updata, [
                'order_id' => $order_id 
        ]);
        if (! $ok) {
            $default_db->rollBack();
            YCore::exception(- 1, '服务器繁忙,请稍候重试');
        }
        $default_db->commit();
        return true;
    }
    
    /**
     * 统计商家评价。
     * 
     * @param number $shop_id 商家ID。
     * @return boolean
     */
    public static function statsShopAppraise($shop_id) {
        $default_db = new DbBase();
        $sql = 'SELECT COUNT(1) AS count, SUM(score1) AS score1, SUM(score2) AS score2, SUM(score3) AS score3 ' . 'FROM mall_appraise_detail WHERE shop_id = :shop_id';
    $params = [
            ':shop_id' => $shop_id 
    ];
    $result = $default_db->rawQuery($sql, $params)->rawFetchOne();
    if (empty($result)) {
        return true;
    }
    $appraise_model = new MallAppraise();
    $appraise_info = $appraise_model->fetchOne([], [
            'shop_id' => $shop_id 
    ]);
    $data = [
            't1' => $result['count'],'s1' => $result['score1'],'p1' => round(($result['score1'] / $result['count']), 1),
            't2' => $result['count'],'s2' => $result['score2'],'p2' => round(($result['score2'] / $result['count']), 1),
            't3' => $result['count'],'s3' => $result['score3'],'p3' => round(($result['score3'] / $result['count']), 1) 
    ];
    if (empty($appraise_info)) {
        $data['created_time'] = $_SERVER['REQUEST_TIME'];
        $last_insert_id = $appraise_model->insert($data);
        if ($last_insert_id == 0) {
            YCore::exception(- 1, '服务器繁忙,请稍候重试');
        }
    } else {
        $data['modified_time'] = $_SERVER['REQUEST_TIME'];
        $ok = $appraise_model->update($data, [
                'aid' => $appraise_info['aid'] 
        ]);
        if (! $ok) {
            YCore::exception(- 1, '服务器繁忙,请稍候重试');
        }
    }
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
            'order_id' => $order_id,'user_id' => $user_id,'status' => 1 
    ]);
    if (empty($order_id)) {
        YCore::exception(- 1, '订单不存在或已经删除');
    }
    $order_item_model = new MallOrderItem();
    $order_item_info = $order_item_model->fetchOne([], [
            'order_id' => $order_id,'sub_order_id' => $sub_order_id 
    ]);
    if (empty($order_item_info)) {
        YCore::exception(- 1, '服务器繁忙,请稍候刷新重试');
    }
    $comment_model = new MallComment();
    $where = [
            'order_id' => $order_id,'sub_order_id' => $sub_order_id,'user_id' => $user_id 
    ];
    $comment_info = $comment_model->fetchOne([], $where);
    if (empty($comment_info)) {
        YCore::exception(- 1, '评价异常,请稍候刷新重试');
    }
    $comment_model->beginTransaction();
    $updata = [
            'content2' => $comment,'content2_time' => $_SERVER['REQUEST_TIME'] 
    ];
    $ok = $comment_model->update($updata, $where);
    if (! $ok) {
        $comment_model->rollBack();
        YCore::exception(- 1, '追加评价失败');
    }
    $updata = [
            'comment_status' => 2,'modified_by' => $user_id,'modified_time' => $_SERVER['REQUEST_TIME'] 
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
 * @param number $shop_id 商家ID。
 * @param number $sub_order_id 子订单ID。
 * @param number $reply_type 回复内容。1初评回复、2追评回复。
 * @param number $comment 评论内容。
 * @return boolean
 */
public static function sellerAppraiseReply($user_id, $shop_id, $sub_order_id, $reply_type, $comment) {
    if (strlen($comment) === 0) {
        YCore::exception(- 1, '评论内容必须填写');
    }
    if (! Validator::is_len($comment, 1, 200, true)) {
        YCore::exception(- 1, '评论内容必须1~200个字符之间');
    }
    $order_item_model = new MallOrderItem();
    $order_item_info = $order_item_model->fetchOne([], [
            'sub_order_id' => $sub_order_id 
    ]);
    if (empty($order_item_info)) {
        YCore::exception(- 1, '服务器繁忙,请稍候刷新重试');
    }
    $order_model = new MallOrder();
    $order_info = $order_model->fetchOne([], [
            'order_id' => $order_item_info['order_id'],'shop_id' => $shop_id 
    ]);
    if (empty($order_info)) {
        YCore::exception(- 1, '评价异常,请稍候刷新重试');
    }
    $comment_model = new MallComment();
    $where = [
            'sub_order_id' => $sub_order_id,'shop_id' => $shop_id 
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
                'reply1' => $comment,'reply1_time' => $_SERVER['REQUEST_TIME'] 
        ];
    } else {
        if (strlen($comment_info['reply2']) !== 0) {
            YCore::exception(- 1, '您已经追加回复');
        }
        $updata = [
                'reply2' => $comment,'reply2_time' => $_SERVER['REQUEST_TIME'] 
        ];
    }
    $ok = $comment_model->update($updata, $where);
    if (! $ok) {
        YCore::exception(- 1, '回复失败');
    }
    return true;
}
}