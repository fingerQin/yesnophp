<?php
/**
 * 收藏夹模块封装。
 * @author winerQin
 * @date 2016-08-24
 */
namespace services;

use models\DbBase;
use common\YCore;
use common\YUrl;
use models\Favorites;

class FavoritesService extends BaseService {
    /**
     * 获取收藏列表。
     *
     * @param number $user_id 用户ID。
     * @param number $obj_type 收藏类型：1商品收藏、2文章收藏、3问答收藏、4IT题目收藏。
     * @param number $page 当前页码。
     * @param number $count 每页显示条数。
     * @return array
     */
    public static function getList($user_id, $obj_type, $page, $count) {
        $offset  = self::getPaginationOffset($page, $count);
        $columns = ' * ';
        $where   = ' WHERE user_id = :user_id AND obj_type = :obj_type AND status = :status';
        $params  = [
            ':user_id'  => $user_id,
            ':obj_type' => $obj_type,
            ':status'   => 1
        ];
        $order_by = ' ORDER BY coupon_id DESC ';
        $sql = "SELECT COUNT(1) AS count FROM ms_favorites {$where}";
        $default_db = new DbBase();
        $count_data = $default_db->rawQuery($sql, $params)->rawFetchOne();
        $total = $count_data ? $count_data['count'] : 0;
        $sql   = "SELECT {$columns} FROM ms_favorites {$where} {$order_by} LIMIT {$offset},{$count}";
        $list  = $default_db->rawQuery($sql, $params)->rawFetchAll();
        foreach ($list as $k => $v) {
            $v['created_time'] = YCore::format_timestamp($v['created_time']);
            switch ($v['obj_type']) {
                case 1: // 商品。
                    $sql = 'SELECT goods_name,goods_name,status FROM mall_goods WHERE goods_id = :goods_id LIMIT 1';
                    $goods_detail   = $default_db->rawQuery($sql, [':goods_id' => $v['obj_id']])->rawFetchOne();
                    $v['title']     = $goods_detail['goods_name'];
                    $v['image_url'] = YUrl::filePath($goods_detail['goods_img']);
                    $v['is_delete'] = ($goods_detail['status'] == 1) ? false : true;
                    break;
                case 2: // 文章。
                    $sql = 'SELECT title,image_url,status FROM ms_news WHERE news_id = :news_id LIMIT 1';
                    $news_detail    = $default_db->rawQuery($sql, [':news_id' => $v['obj_id']])->rawFetchOne();
                    $v['title']     = $news_detail['title'];
                    $v['image_url'] = YUrl::filePath($news_detail['image_url']);
                    $v['is_delete'] = ($news_detail['status'] == 1) ? false : true;
                    break;
                case 3: // 问答。
                    $sql = 'SELECT ques_title,status FROM qa_question WHERE ques_id = :ques_id LIMIT 1';
                    $ques_detail    = $default_db->rawQuery($sql, [':ques_id' => $v['obj_id']])->rawFetchOne();
                    $v['title']     = $ques_detail['ques_title'];
                    $v['image_url'] = '';
                    $v['is_delete'] = ($ques_detail['status'] == 1) ? false : true;
                    break;
                case 4: // IT题目。
                    $sql = 'SELECT ques_title,status FROM it_question WHERE ques_id = :ques_id LIMIT 1';
                    $ques_detail    = $default_db->rawQuery($sql, [':ques_id' => $v['obj_id']])->rawFetchOne();
                    $v['title']     = $ques_detail['ques_title'];
                    $v['image_url'] = '';
                    $v['is_delete'] = ($goods_detail['status'] == 1) ? false : true;
                    break;
            }
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
     * 添加收藏。
     *
     * @param number $user_id 用户ID。
     * @param number $obj_type 收藏类型：1商品收藏、2文章收藏、3问答收藏、4IT题目收藏。
     * @param number $obj_id 商品ID/文章ID/问答ID/IT题目ID。
     * @return boolean
     */
    public static function add($user_id, $obj_type, $obj_id) {
        $default_db = new DbBase();
        switch ($obj_type) {
            case 1: // 商品。
                $sql = 'SELECT * FROM mall_goods WHERE goods_id = :goods_id AND status = :status LIMIT 1';
                $goods_detail = $default_db->rawQuery($sql, [':goods_id' => $obj_id,':status' => 1])->rawFetchOne();
                if (empty($goods_detail)) {
                    YCore::exception(1, '该商品已经删除');
                }
                break;
            case 2: // 文章。
                $sql = 'SELECT * FROM ms_news WHERE news_id = :news_id AND status = :status LIMIT 1';
                $news_detail = $default_db->rawQuery($sql, [':news_id' => $obj_id, ':status' => 1])->rawFetchOne();
                if (empty($news_detail)) {
                    YCore::exception(2, '该文章已经删除');
                }
                break;
            case 3: // 问答。
                $sql = 'SELECT * FROM qa_question WHERE ques_id = :ques_id AND status = :status LIMIT 1';
                $ques_detail = $default_db->rawQuery($sql, [':ques_id' => $obj_id, ':status' => 1])->rawFetchOne();
                if (empty($ques_detail)) {
                    YCore::exception(3, '该商品已经删除');
                }
                break;
            case 4: // IT题目。
                $sql = 'SELECT * FROM it_question WHERE ques_id = :ques_id AND status = :status LIMIT 1';
                $ques_detail = $default_db->rawQuery($sql, [':ques_id' => $obj_id, ':status' => 1])->rawFetchOne();
                if (empty($ques_detail)) {
                    YCore::exception(4, '该商品已经删除');
                }
                break;
        }
        $where = [
            'user_id'  => $user_id,
            'obj_type' => $obj_type,
            'obj_id'   => $obj_id,
            'status'   => 1
        ];
        $favorites_model = new Favorites();
        $favorites_detail = $favorites_model->fetchOne([], $where);
        if (!empty($favorites_detail)) {
            return true;
        }
        $data = [
            'user_id'  => $user_id,
            'obj_type' => $obj_type,
            'ojb_id'   => $obj_id,
            'status'   => 1
        ];
        $ok = $favorites_model->insert($data);
        if (!$ok) {
            YCore::exception(5, '服务器繁忙,请稍候重试');
        }
        return true;
    }
    /**
     * 删除收藏。
     *
     * @param number $user_id 用户ID。
     * @param number $obj_type 收藏类型：1商品收藏、2文章收藏、3问答收藏、4IT题目收藏
     * @param number $obj_id 商品ID/文章ID/问答ID/IT题目ID
     * @return boolean
     */
    public static function delete($user_id, $obj_type, $obj_id) {
        $favorites_model = new Favorites();
        $where = [
            'user_id'  => $user_id,
            'obj_type' => $obj_type,
            'ojb_id'   => $obj_id,
            'status'   => 1
        ];
        $ok = $favorites_model->delete($where);
        if (!$ok) {
            YCore::exception(1, '删除失败');
        }
        return true;
    }
}