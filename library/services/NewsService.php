<?php
/**
 * 文章管理。
 * @author winerQin
 * @date 2016-03-28
 */
namespace services;

use models\News;
use common\YCore;
use winer\Validator;
use models\NewsData;
use models\Admin;
use models\Category;

class NewsService extends BaseService {

    /**
     * 文章列表。
     *
     * @param string $title 文章标题。
     * @param string $admin_name 管理员账号。
     * @param string $starttime 开始时间。
     * @param string $endtime 截止时间。
     * @param number $page 分页页码。
     * @param number $count 每页显示记录条数。
     * @return array
     */
    public static function getNewsList($title = '', $admin_name = '', $starttime = '', $endtime = '', $page = 1, $count = 20) {
        if (strlen($starttime) > 0 && !Validator::is_date($starttime, 'Y-m-d H:i:s')) {
            YCore::exception(-1, '开始时间格式不对');
        }
        if (strlen($endtime) > 0 && !Validator::is_date($endtime, 'Y-m-d H:i:s')) {
            YCore::exception(-1, '结束时间格式不对');
        }
        if (mb_strlen($title) > 100) {
            YCore::exception(-1, '标题查询条件长度不能大于100个字符');
        }
        $admin_id = -1;
        if (strlen($admin_name) > 0) {
            $admin_model = new Admin();
            $admin = $admin_model->fetchOne([], ['username' => $admin_name]);
            $admin_id = $admin ? $admin['admin_id'] : 0;
        }
        $news_model = new News();
        return $news_model->getList($title, $admin_id, $starttime, $endtime, $page, $count);
    }

    /**
     * 按文章code获取文章详情。
     *
     * @param string $code 文章code。
     * @param boolean $is_get_content 是否获取文章内容。false：否、true：是。
     * @return array
     */
    public static function getByCodeNewsDetail($code, $is_get_content = false) {
        $news_model = new News();
        $data = $news_model->fetchOne([], ['code' => $code, 'status' => 1]);
        if (empty($data)) {
            YCore::exception(-1, '文章不存在或已经删除');
        }
        if ($is_get_content) {
            $news_data_model = new NewsData();
            $news_data = $news_data_model->fetchOne([], ['news_id' => $data['news_id']]);
            if ($news_data) {
                return array_merge($data, $news_data);
            } else {
                YCore::exception(-1, '文章数据异常');
            }
        } else {
            return $data;
        }
    }

    /**
     * 按文章ID获取文章详情。
     *
     * @param number $news_id 文章ID。
     * @param boolean $is_get_content 是否获取文章内容。false：否、true：是。
     * @return array
     */
    public static function getNewsDetail($news_id, $is_get_content = false) {
        $news_model = new News();
        $data = $news_model->fetchOne([], ['news_id' => $news_id, 'status' => 1]);
        if (empty($data)) {
            YCore::exception(-1, '文章不存在或已经删除');
        }
        if ($is_get_content) {
            $news_data_model = new NewsData();
            $news_data = $news_data_model->fetchOne([], ['news_id' => $news_id]);
            if ($news_data) {
                return array_merge($data, $news_data);
            } else {
                YCore::exception(-1, '文章数据异常');
            }
        } else {
            return $data;
        }
    }

    /**
     * 按文章编码获取文章详情。
     *
     * @param string $code 文章编码。
     * @return array
     */
    public static function getByCodeDetail($code) {
        $news_model = new News();
        $data = $news_model->fetchOne([], ['code' => $code, 'status' => 1]);
        if (empty($data)) {
            YCore::exception(-1, '文章不存在或已经删除');
        }
        $news_data_model = new NewsData();
        $news_data = $news_data_model->fetchOne([], ['news_id' => $data['news_id']]);
        if ($news_data) {
            return array_merge($data, $news_data);
        } else {
            YCore::exception(-1, '文章数据异常');
        }
    }

    /**
     * 添加文章。
     *
     * @param number $admin_id 管理员ID。
     * @param string $code 文章编码。
     * @param number $cat_id 分类ID。
     * @param string $title 文章标题。
     * @param string $intro 文章简介。
     * @param string $keywords 文章关键词。
     * @param string $source 文章来源。
     * @param string $image_url 文章图片。
     * @param string $content 文章内容。
     * @param number $display 显示状态：1显示、0隐藏。
     * @return boolean
     */
    public static function addNews($admin_id, $code, $cat_id, $title, $intro, $keywords, $source, $image_url, $content, $display = 1) {
        if (is_numeric($code)) {
            YCore::exception(-1, '文章编码不能为纯数字');
        }
        $category_model = new Category();
        $cat_info = $category_model->fetchOne([], ['cat_id' => $cat_id, 'status' => 1]);
        if (empty($cat_info)) {
            YCore::exception(-1, '分类不存在或已经删除');
        }
        $news_model = new News();
        $news_detail = $news_model->fetchOne([], ['code' => $code, 'status' => 1]);
        if (!empty($news_detail)) {
            YCore::exception(-1, '文章编码已经存在请更换');
        }
        $data = [
            'title'     => $title,
            'code'      => $code,
            'intro'     => $intro,
            'keywords'  => $keywords,
            'source'    => $source,
            'image_url' => $image_url,
            'content'   => $content,
            'display'   => $display
        ];
        $rules = [
            'title'     => '标题|require:1000000|len:1000000:1:80:1',
            'code'      => '文章编码|require:1000000|len:1000000:1:20:1|alpha_dash:1000000',
            'intro'     => '文章简介|require:1000000|len:1000000:20:500:1',
            'keywords'  => '文章关键词|require:1000000|len:1000000:1:100:1',
            'source'    => '文章来源|require:1000000|len:1000000:1:50:1',
            'image_url' => '文章图片|len:1000000:1:100:1',
            'content'   => '文章内容|require:1000000|len:1000000:10:50000:1',
            'display'   => '显示状态|require:1000000|integer:1000000'
        ];
        Validator::valido($data, $rules);
        $data['created_by']   = $admin_id;
        $data['created_time'] = $_SERVER['REQUEST_TIME'];
        $data['cat_id']       = $cat_id;
        $data['status']       = 1;
        unset($data['content']);
        $news_id = $news_model->insert($data);
        if ($news_id > 0) {
            $news_data_model = new NewsData();
            $data = [
                'content' => $content,
                'news_id' => $news_id
            ];
            $news_data_model->insert($data);
            return true;
        } else {
            return false;
        }
    }

    /**
     * 文章编辑。
     *
     * @param number $admin_id 管理员ID。
     * @param number $news_id 文章ID。
     * @param string $code 文章编码。
     * @param number $cat_id 分类ID。
     * @param string $title 文章标题。
     * @param string $intro 文章简介。
     * @param string $keywords 文章关键词。
     * @param string $source 文章来源。
     * @param string $image_url 文章图片。
     * @param string $content 文章内容。
     * @param number $display 显示状态：1显示、0隐藏。
     * @return boolean
     */
    public static function editNews($admin_id, $news_id, $code, $cat_id, $title, $intro, $keywords, $source, $image_url, $content, $display = 1) {
        $news_model = new News();
        $news_detail = $news_model->fetchOne([], ['news_id' => $news_id, 'status' => 1]);
        if (empty($news_detail)) {
            YCore::exception(-1, '文章不存在或已经删除');
        }
        $news_detail_code = $news_model->fetchOne([], ['code' => $code, 'status' => 1]);
        if ($news_detail_code && $news_detail['code'] != $code) {
            YCore::exception(-1, '文章编码已经被占用请更换');
        }
        $category_model = new Category();
        $cat_info = $category_model->fetchOne([], ['cat_id' => $cat_id, 'status' => 1]);
        if (empty($cat_info)) {
            YCore::exception(-1, '分类不存在或已经删除');
        }
        $data = [
            'title'     => $title,
            'code'      => $code,
            'intro'     => $intro,
            'keywords'  => $keywords,
            'source'    => $source,
            'image_url' => $image_url,
            'content'   => $content,
            'display'   => $display
        ];
        $rules = [
            'title'     => '标题|require:1000000|len:1000000:1:80:1',
            'code'      => '文章编码|require:1000000|len:1000000:1:20:0|alpha_dash:1000000',
            'intro'     => '文章简介|require:1000000|len:1000000:20:500:1',
            'keywords'  => '文章关键词|require:1000000|len:1000000:1:100:1',
            'source'    => '文章来源|require:1000000|len:1000000:1:50:1',
            'image_url' => '文章图片|len:1000000:1:100:1',
            'content'   => '文章内容|require:1000000|len:1000000:10:50000:1',
            'display'   => '显示状态|require:1000000|integer:1000000'
        ];
        Validator::valido($data, $rules); // 验证不通过会抛异常。
        $data['modified_by']   = $admin_id;
        $data['modified_time'] = $_SERVER['REQUEST_TIME'];
        $data['cat_id']        = $cat_id;
        unset($data['content']);
        $ok = $news_model->update($data, ['news_id' => $news_id, 'status' => 1]);
        if ($ok) {
            $news_data_model = new NewsData();
            $data = [
                'content' => $content
            ];
            $where = [
                'news_id' => $news_id
            ];
            $news_data_model->update($data, $where);
            return true;
        } else {
            return false;
        }
    }

    /**
     * 删除文章。
     *
     * @param number $admin_id 管理员ID。
     * @param number $news_id 文章ID。
     * @return boolean
     */
    public static function deleteNews($admin_id, $news_id) {
        $news_model = new News();
        $news_detail = $news_model->fetchOne([], ['news_id' => $news_id, 'status' => 1]);
        if (empty($news_detail)) {
            YCore::exception(-1, '文章不存在或已经删除');
        }
        $where = [
            'news_id' => $news_id
        ];
        $data = [
            'status'        => 2,
            'modified_by'   => $admin_id,
            'modified_time' => $_SERVER['REQUEST_TIME']
        ];
        return $news_model->update($data, $where);
    }

    /**
     * 文章排序。
     *
     * @param number $admin_id 管理员ID。
     * @param array $listorders 分类排序数据。[ ['文章ID' => '排序值'], ...... ]
     * @return boolean
     */
    public static function sortNews($admin_id, $listorders) {
        if (empty($listorders)) {
            YCore::exception(-1, '请选择要排序的文章');
        }
        $news_model = new News();
        foreach ($listorders as $news_id => $sort_value) {
            $data = [
                'listorder'     => $sort_value,
                'modified_by'   => $admin_id,
                'modified_time' => $_SERVER['REQUEST_TIME']
            ];
            $where = [
                'news_id' => $news_id,
                'status'  => 1
            ];
            $news_model->update($data, $where);
        }
        return true;
    }
}