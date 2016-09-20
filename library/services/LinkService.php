<?php
/**
 * 友情链接管理。
 * @author winerQin
 * @date 2016-03-29
 */
namespace services;

use models\Link;
use common\YCore;
use winer\Validator;
use models\DbBase;

class LinkService extends BaseService {

    /**
     * 友情链接列表。
     *
     * @param string $keyword 友情链接名称查询关键词。
     * @param number $cat_id 分类ID。
     * @param number $page 当前页码。
     * @param number $count 每页显示记录条数。
     * @return array
     */
    public static function getLinkList($keyword = '', $cat_id = -1, $page = 1, $count = 20) {
        $db_model = new DbBase();
        $offset   = self::getPaginationOffset($page, $count);
        $table    = ' FROM ms_link AS a LEFT JOIN ms_category AS b ON(a.cat_id = b.cat_id) ';
        $columns  = ' a.*,b.cat_name ';
        $where    = ' WHERE a.status = :status ';
        $params   = [
            ':status' => 1
        ];
        if (strlen($keyword) > 0) {
            $where .= ' AND a.link_name LIKE :link_name ';
            $params[':link_name'] = "%{$keyword}%";
        }
        if ($cat_id != -1) {
            $where .= ' AND b.cat_id LIKE :cat_id ';
            $params[':cat_id'] = $cat_id;
        }
        $order_by = ' ORDER BY a.link_id DESC ';
        $sql = "SELECT COUNT(1) AS count {$table} {$where}";
        $count_data = $db_model->rawQuery($sql, $params)->rawFetchOne();
        $total = $count_data ? $count_data['count'] : 0;
        $sql   = "SELECT {$columns} {$table} {$where} {$order_by} LIMIT {$offset},{$count}";
        $list  = $db_model->rawQuery($sql, $params)->rawFetchAll();
        foreach ($list as $k => $item) {
            $item['created_time']  = YCore::format_timestamp($item['created_time']);
            $item['modified_time'] = YCore::format_timestamp($item['modified_time']);
            $list[$k] = $item;
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
     * 获取友情链接详情。
     *
     * @param number $link_id 友情链接ID。
     * @return array
     */
    public static function getLinkDetail($link_id) {
        $link_model = new Link();
        $data = $link_model->fetchOne([], ['link_id' => $link_id, 'status' => 1]);
        if (empty($data)) {
            YCore::exception(-1, '友情链接不存在或已经删除');
        }
        return $data;
    }

    /**
     * 编辑友情链接。
     *
     * @param number $admin_id 管理员ID。
     * @param string $link_name 友情链接名称。
     * @param string $link_url 友情链接URL。
     * @param number $cat_id 分类ID。
     * @param string $image_url 友情链接图片。
     * @param number $display 显示状态：1显示、0隐藏。
     * @return boolean
     */
    public static function addLink($admin_id, $link_name, $link_url, $cat_id, $image_url, $display = 1) {
        $link_model = new Link();
        $data = [
            'link_name' => $link_name,
            'link_url'  => $link_url,
            'cat_id'    => $cat_id,
            'image_url' => $image_url,
            'display'   => $display
        ];
        $rules = [
            'link_name' => '友情链接名称|require:1000000|len:1000000:1:20:1',
            'link_url'  => '友情链接URL|require:1000000|len:1000000:1:100:1|url:1000000',
            'cat_id'    => '友情链接分类|require:1000000|integer:1000000',
            'image_url' => '友情链接图片|len:1000000:1:100:1',
            'display'   => '显示状态|require:1000000|integer:1000000'
        ];
        Validator::valido($data, $rules);
        $data['status']       = 1;
        $data['created_by']   = $admin_id;
        $data['created_time'] = $_SERVER['REQUEST_TIME'];
        return $link_model->insert($data);
    }

    /**
     * 编辑友情链接。
     *
     * @param number $admin_id 管理员ID。
     * @param number $link_id 友情链接ID。
     * @param string $link_name 友情链接名称。
     * @param string $link_url 友情链接URL。
     * @param number $cat_id 分类ID。
     * @param string $image_url 友情链接图片。
     * @param number $display 显示状态：1显示、0隐藏。
     * @return boolean
     */
    public static function editLink($admin_id, $link_id, $link_name, $link_url, $cat_id, $image_url, $display = 1) {
        $link_model = new Link();
        $where = [
            'link_id' => $link_id,
            'status'  => 1
        ];
        $link_detail = $link_model->fetchOne([], $where);
        if (empty($link_detail)) {
            YCore::exception(-1, '友情链接不存在或已经删除');
        }
        $data = [
            'link_name' => $link_name,
            'link_url'  => $link_url,
            'cat_id'    => $cat_id,
            'image_url' => $image_url,
            'display'   => $display
        ];
        $rules = [
            'link_name' => '友情链接名称|require:1000000|len:1000000:1:20:1',
            'link_url'  => '友情链接URL|require:1000000|len:1000000:1:100:1|url:1000000',
            'cat_id'    => '友情链接分类|require:1000000|integer:1000000',
            'image_url' => '友情链接图片|len:1000000:1:100:1',
            'display'   => '显示状态|require:1000000|integer:1000000'
        ];
        Validator::valido($data, $rules);
        $data['modified_by']   = $admin_id;
        $data['modified_time'] = $_SERVER['REQUEST_TIME'];
        return $link_model->update($data, $where);
    }

    /**
     * 删除友情链接。
     *
     * @param number $admin_id 管理员ID。
     * @param number $link_id 友情链接ID。
     * @return boolean
     */
    public static function deleteLink($admin_id, $link_id) {
        $link_model = new Link();
        $where = [
            'link_id' => $link_id,
            'status'  => 1
        ];
        $link_detail = $link_model->fetchOne([], $where);
        if (empty($link_detail)) {
            YCore::exception(-1, '友情链接不存在或已经删除');
        }
        $data = [
            'status'        => 2,
            'modified_by'   => $admin_id,
            'modified_time' => $_SERVER['REQUEST_TIME']
        ];
        return $link_model->update($data, $where);
    }

    /**
     * 友情链接排序。
     *
     * @param array $listorders 分类排序数据。[ ['友情链接ID' => '排序值'], ...... ]
     * @return boolean
     */
    public static function sortLink($listorders) {
        if (empty($listorders)) {
            return true;
        }
        foreach ($listorders as $link_id => $sort_val) {
            $link_model = new Link();
            $ok = $link_model->sortLink($link_id, $sort_val);
            if (!$ok) {
                return false;
            }
        }
        return true;
    }
}