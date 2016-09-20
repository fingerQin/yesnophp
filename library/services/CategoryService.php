<?php
/**
 * 分类管理。
 * @author winerQin
 * @date 2016-03-25
 */
namespace services;

use models\Category;
use common\YCore;
use models\News;
use models\Link;

class CategoryService extends BaseService {

    const CAT_NEWS = 1;
    // 文章分类。
    const CAT_LINK = 2;
    // 友情链接分类。
    const CAT_GOODS = 3;
    // 商品分类。

    /**
     * 获取分类列表。
     *
     * @param number $parentid 父ID。默认值0。
     * @param number $cat_type 分类类型。
     * @param boolean $is_filter 是否过滤无用字段。
     * @return array
     */
    public static function getCategoryList($parentid = 0, $cat_type = self::CAT_NEWS, $is_filter = false) {
        $category_model = new Category();
        $category_list = $category_model->getByParentToCategory($parentid, $cat_type, true, $is_filter);
        if (empty($category_list)) {
            return $category_list;
        } else {
            foreach ($category_list as $key => $menu) {
                $category_list[$key]['sub'] = self::getCategoryList($menu['cat_id'], $cat_type, $is_filter);
            }
            return $category_list;
        }
    }

    /**
     * 添加分类。
     *
     * @param number $admin_id 管理员ID。
     * @param number $cat_type 分类类型。
     * @param string $cat_name 分类名称。
     * @param number $parentid 父分类ID。
     * @param number $is_out_url 是否外部链接。1是、0否。
     * @param string $out_url 外部链接。
     * @param number $display 显示状态：1显示、0否。
     * @return boolean
     */
    public static function addCategory($admin_id, $cat_type, $cat_name, $parentid = 0, $is_out_url = 0, $out_url = '', $display = 1) {
        $category_model = new Category();
        $lv = 1;
        if ($parentid != 0) {
            $parent_cat_info = $category_model->fetchOne([], ['cat_id' => $parentid, 'status' => 1]);
            if (empty($parent_cat_info)) {
                YCore::exception(- 1, '父分类不存在或已经删除');
            }
            $lv = $parent_cat_info['lv'] + 1;
            // 当是添加子分类的时候。子分类的分类类型继续父分类的类型。
            $cat_type = $parent_cat_info['cat_type'];
        }
        $cat_code = self::getNewCategoryCode($parentid);
        $data = [
            'cat_name'     => $cat_name,
            'cat_type'     => $cat_type,
            'parentid'     => $parentid,
            'lv'           => $lv,
            'is_out_url'   => $is_out_url,
            'out_url'      => $out_url,
            'display'      => $display,
            'cat_code'     => $cat_code,
            'status'       => 1,
            'created_time' => $_SERVER['REQUEST_TIME'],
            'created_by'   => $admin_id
        ];
        return $category_model->insert($data);
    }

    /**
     * 编辑分类。
     *
     * @param number $admin_id 管理员ID。
     * @param number $cat_id 分类ID。
     * @param string $cat_name 分类名称。
     * @param number $is_out_url 是否外部链接。1是、0否。
     * @param string $out_url 外部链接。
     * @param number $display 显示状态：1显示、0否。
     * @return boolean
     */
    public static function editCategory($admin_id, $cat_id, $cat_name, $is_out_url = 0, $out_url = '', $display = 1) {
        $category_model = new Category();
        $cat_info = $category_model->fetchOne([], ['cat_id' => $cat_id, 'status' => 1]);
        if (empty($cat_info)) {
            YCore::exception(- 1, '分类不存在或已经删除');
        }
        $data = [
            'cat_name'      => $cat_name,
            'is_out_url'    => $is_out_url,
            'out_url'       => $out_url,
            'display'       => $display,
            'modified_time' => $_SERVER['REQUEST_TIME'],
            'modified_by'   => $admin_id
        ];
        $where = [
            'cat_id' => $cat_id
        ];
        return $category_model->update($data, $where);
    }

    /**
     * 删除分类。
     *
     * @param number $admin_id 管理员ID。
     * @param number $cat_id 分类ID。
     * @return boolean
     */
    public static function deleteCategory($admin_id, $cat_id) {
        // [1]
        $category_model = new Category();
        $data = $category_model->fetchOne([], ['cat_id' => $cat_id, 'status' => 1]);
        if (empty($data)) {
            YCore::exception(- 1, '分类不存在或已经删除');
        }
        // [2] 目前只检查文章与友情链接，后续如果关联了其他功能，这里要做适当调整。
        $news_model = new News();
        $news_count = $news_model->count(['cat_id' => $cat_id, 'status' => 1]);
        if ($news_count > 0) {
            YCore::exception(- 1, '请先清空该分类下的文章');
        }
        $link_model = new Link();
        $link_count = $link_model->count(['cat_id' => $cat_id, 'status' => 1]);
        if ($link_count > 0) {
            YCore::exception(- 1, '请先清空该分类下的友情链接');
        }
        $where = [
            'cat_id' => $cat_id
        ];
        $data = [
            'status'        => 2,
            'modified_by'   => $admin_id,
            'modified_time' => $_SERVER['REQUEST_TIME']
        ];
        return $category_model->update($data, $where);
    }

    /**
     * 获取分类详情。
     *
     * @param number $cat_id 分类ID。
     * @return array
     */
    public static function getCategoryDetail($cat_id) {
        $category_model = new Category();
        $data = $category_model->fetchOne([], ['cat_id' => $cat_id, 'status' => 1]);
        if (empty($data)) {
            YCore::exception(- 1, '分类不存在或已经删除');
        }
        return $data;
    }

    /**
     * 分类排序。
     *
     * @param array $listorders 分类排序数据。[ ['分类ID' => '排序值'], ...... ]
     * @return boolean
     */
    public static function sortCategory($listorders) {
        if (empty($listorders)) {
            return true;
        }
        foreach ($listorders as $cat_id => $sort_val) {
            $category_model = new Category();
            $ok = $category_model->sort($cat_id, $sort_val);
            if (! $ok) {
                return false;
            }
        }
        return true;
    }

    /**
     * 获取分类编码的有效前期。
     *
     * @param string $cat_code 分类编码。
     * @param number $lv 分类层级。
     * @return string
     */
    public static function getCatCodePrefix($cat_code, $lv) {
        return substr($cat_code, 0, $lv * 3);
    }

    /**
     * 获取父分类下子分类最新code编码。
     * -- 1、cat_code编码最大允许的层级是10级。
     * -- 2、每级用3个数字点位表示。10级就是30位。
     * -- 3、第一级点位是100，补齐30位，则就变成了29个0.
     *
     * @param number $parentid 父分类ID。
     * @return string
     */
    public static function getNewCategoryCode($parentid) {
        $category_model = new Category();
        if ($parentid == 0) {
            $sql = 'SELECT * FROM ms_category WHERE parentid = :parentid ORDER BY cat_code DESC LIMIT 1';
            $params = [
                ':parentid' => $parentid
            ];
            $data = $category_model->rawQuery($sql, $params)->rawFetchOne();
            if ($data) {
                $sub_code = substr($data['cat_code'], 0, 3);
                $sub_code = $sub_code + 1;
                $sub_cat_code = sprintf("%-030s", $sub_code);
            } else {
                $sub_cat_code = sprintf("%-030s", 100);
            }
        } else {
            $cat_info = $category_model->fetchOne([], ['cat_id' => $parentid, 'status' => 1]);
            if (empty($cat_info)) {
                YCore::exception(- 1, '父分类不存在或已经删除');
            }
            $code_prefix = substr($cat_info['cat_code'], 0, $cat_info['lv'] * 3);
            $sql = 'SELECT * FROM ms_category WHERE cat_code LIKE :cat_code ORDER BY cat_code DESC LIMIT 1';
            $params = [
                ':cat_code' => "{$code_prefix}%"
            ];
            $data     = $category_model->rawQuery($sql, $params)->rawFetchOne();
            $sub_code = substr($data['cat_code'], $cat_info['lv'] * 3, 3);
            $sub_code = $sub_code + 1;
            $sub_cat_code = sprintf("%-030s", "{$code_prefix}{$sub_code}");
        }
        return $sub_cat_code;
    }

}