<?php
/**
 * 商品相关业务封装。
 * @author winerQin
 * @date 2016-04-06
 */
namespace services;

use winer\Validator;
use common\YCore;
use models\Category;
use models\DbBase;
use models\MallGoods;
use models\MallGoodsImage;
use models\MallProduct;
use common\YUrl;
use models\MallFreightTpl;

class GoodsService extends BaseService {

    /**
     * 获取商品列表[管理后台版]。
     *
     * @param number $updown 上下架状态。-1不限、1上架、0下架。
     * @param string $goods_name 商品名称。
     * @param number $cat_id 分类ID。
     * @param number $start_price 价格最小值。
     * @param number $end_price 价格最大值。
     * @param number $is_delete_show 是否显示已删除的商品。0否、1是。
     * @param number $page 当前页码。
     * @param number $count 每页显示条数。
     * @return array
     */
    public static function getBackendGoodsList($updown = -1, $goods_name = '', $cat_id = -1, $start_price = '', $end_price = '', $is_delete_show = 0, $page = 1, $count = 20) {
        $offset = self::getPaginationOffset($page, $count);
        $from_table = ' FROM mall_goods ';
        $columns = ' * ';
        $where   = ' WHERE 1 ';
        $params  = [];
        if (!$is_delete_show) {
            $where .= ' AND status = :status ';
            $params[':status'] = 1;
        }
        if (strlen($goods_name) > 0) {
            $where .= ' AND goods_name LIKE :goods_name ';
            $params[':goods_name'] = "%{$goods_name}%";
        }
        if ($cat_id != -1) {
            $category_model = new Category();
            $cat_info = $category_model->fetchOne([], ['cat_id' => $cat_id, 'status' => 1]);
            if (empty($cat_info)) {
                $where .= ' AND cat_code = :cat_code ';
                $params[':cat_code'] = '';
            } else {
                $where .= ' AND cat_code LIKE :cat_code ';
                $cat_code_prefix = CategoryService::getCatCodePrefix($cat_info['cat_code'], $cat_info['lv']);
                $params[':cat_code'] = "{$cat_code_prefix}%";
            }
        }
        if (strlen($start_price) > 0) {
            if (!Validator::is_integer($start_price)) {
                YCore::exception(-1, '查询价格必须是整数');
            }
            $where .= ' AND min_price <= :start_price ';
            $params[':start_price'] = $start_price;
        }
        if (strlen($end_price) > 0) {
            if (!Validator::is_integer($end_price)) {
                YCore::exception(-1, '查询价格必须是整数');
            }
            $where .= ' AND max_price >= :end_price ';
            $params[':end_price'] = strtotime($end_price);
        }
        $order_by = ' ORDER BY goods_id DESC ';
        $sql = "SELECT COUNT(1) AS count {$from_table} {$where}";
        $default_db = new DbBase();
        $count_data = $default_db->rawQuery($sql, $params)->rawFetchOne();
        $total = $count_data ? $count_data['count'] : 0;
        $sql   = "SELECT {$columns} {$from_table} {$where} {$order_by} LIMIT {$offset},{$count}";
        $list  = $default_db->rawQuery($sql, $params)->rawFetchAll();
        foreach ($list as $k => $v) {
            $v['goods_img']       = YUrl::filePath($v['goods_img']);
            $v['created_time']    = YCore::format_timestamp($v['created_time']);
            $v['modified_time']   = YCore::format_timestamp($v['modified_time']);
            $v['marketable_time'] = YCore::format_timestamp($v['marketable_time']);
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
     * 获取商品列表[普通用户版]。
     *
     * @param string $keyword 搜索关键词。模糊搜索商品名称。
     * @param number $cat_id 分类ID。-1全部。
     * @param number $start_price 价格最小值。
     * @param number $end_price 价格最大值。
     * @param number $page 当前页码。
     * @param number $count 每页显示条数。
     * @return array
     */
    public static function getGoodsList($keyword = '', $cat_id = -1, $start_price = '', $end_price = '', $page = 1, $count = 20) {
        $offset = self::getPaginationOffset($page, $count);
        $from_table = ' FROM mall_goods ';
        $columns = ' * ';
        $where   = ' WHERE status = :status ';
        $params  = [
            ':status' => 1
        ];
        if (strlen($keyword) > 0) {
            $where .= ' AND goods_name LIKE :goods_name ';
            $params[':goods_name'] = "%{$keyword}%";
        }
        if ($cat_id != -1) {
            $category_model = new Category();
            $cat_info = $category_model->fetchOne([], [
                'cat_id' => $cat_id,
                'status' => 1
            ]);
            if (empty($cat_info)) {
                $where .= ' AND cat_code = :cat_code ';
                $params[':cat_code'] = '';
            } else {
                $where .= ' AND cat_code LIKE :cat_code ';
                $cat_code_prefix = CategoryService::getCatCodePrefix($cat_info['cat_code'], $cat_info['lv']);
                $params[':cat_code'] = "{$cat_code_prefix}%";
            }
        }
        if (strlen($start_price) > 0) {
            if (!Validator::is_integer($start_price)) {
                YCore::exception(-1, '查询价格必须是整数');
            }
            $where .= ' AND min_price <= :start_price ';
            $params[':start_price'] = $start_price;
        }
        if (strlen($end_price) > 0) {
            if (!Validator::is_integer($end_price)) {
                YCore::exception(-1, '查询价格必须是整数');
            }
            $where .= ' AND max_price >= :end_price ';
            $params[':end_price'] = strtotime($end_price);
        }
        $order_by = ' ORDER BY goods_id DESC ';
        $sql = "SELECT COUNT(1) AS count {$from_table} {$where}";
        $default_db = new DbBase();
        $count_data = $default_db->rawQuery($sql, $params)->rawFetchOne();
        $total = $count_data ? $count_data['count'] : 0;
        $sql   = "SELECT {$columns} {$from_table} {$where} {$order_by} LIMIT {$offset},{$count}";
        $list  = $default_db->rawQuery($sql, $params)->rawFetchAll();
        foreach ($list as $k => $v) {
            $v['goods_img']       = YUrl::filePath($v['goods_img']);
            $v['created_time']    = YCore::format_timestamp($v['created_time']);
            $v['modified_time']   = YCore::format_timestamp($v['modified_time']);
            $v['marketable_time'] = YCore::format_timestamp($v['marketable_time']);
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
     * 获取商品详情。
     *
     * @param number $goods_id 商品ID。
     * @return array
     */
    public static function getGoodsDetail($goods_id) {
        $goods_model = new MallGoods();
        $columns = [
            'goods_id',
            'goods_name',
            'slogan',
            'min_price',
            'max_price',
            'is_exchange',
            'goods_img',
            'weight',
            'buy_count',
            'month_buy_count',
            'spec_val_json',
            'spec_val_json',
            'description'
        ];
        $goods_detail = $goods_model->fetchOne($columns, ['goods_id' => $goods_id, 'status' => 1]);
        if (empty($goods_detail)) {
            YCore::exception(-1, '商品不存在');
        }
        $goods_image_model = new MallGoodsImage();
        $goods_image = $goods_image_model->fetchAll(['image_url'], ['goods_id' => $goods_id, 'status' => 1], 5, 'image_id ASC');
        if (empty($goods_image)) {
            YCore::exception(-1, '商品相册异常');
        }
        $goods_image_list = [];
        foreach ($goods_image as $image) {
            $goods_image_list[] = YUrl::filePath($image['image_url']);
        }
        $goods_detail['spec_val_json'] = json_decode($goods_detail['spec_val_json'], true);
        $goods_detail['goods_image']   = $goods_image_list;
        unset($goods_detail['spec_val_json'], $goods_image, $goods_image_model, $goods_model);
        return $goods_detail;
    }

    /**
     * 添加商品。
     * -- Example start --
     * $data = [
     *      'user_id'        => '添加商品的用户ID',
     *      'goods_name'     => '商品名称',
     *      'cat_id'         => '系统分类ID',
     *      'slogan'         => '广告语、运营性质标题',
     *      'weight'         => '重量。单位（g）',
     *      'listorder'      => '排序值。小到大排列。',
     *      'is_exchange'    => '是否允许金币兑换',
     *      'description'    => '商品详情。',
     *      'spec_val'       => '商品规格',
     *      'products'       => '库存与价格',
     *      'goods_album'    => '商品相册',
     *      'market_price'   => '市场价。当没有规格的时候，此值是必须的。',
     *      'sales_price'    => '销售价。当没有规格的时候，此值是必须的。',
     *      'stock'          => '库存。当没有规格的时候，此值是必须的。',
     *      'freight_tpl_id' => '运费模板ID',
     * ];
     *
     * $spec_val = [
     *      '颜色' => ['银色', '黑色'],
     *      '尺寸' => ['35', '38']
     * ];
     *
     * $products = [
     *      '颜色:银色|尺寸:35' => ['market_price' => 129, 'sales_price' => 99, 'stock' => '999'],
     *      '颜色:黑色|尺寸:35' => ['market_price' => 129, 'sales_price' => 99, 'stock' => '999'],
     *      '颜色:银色|尺寸:38' => ['market_price' => 129, 'sales_price' => 99, 'stock' => '999'],
     *      '颜色:黑色|尺寸:38' => ['market_price' => 129, 'sales_price' => 99, 'stock' => '999'],
     * ];
     *
     * # 最多五张图片。第一张图片会更新到商品主图。
     * $goods_album = [
     *      'images/voucher/20160401/56fe70362ef7e.jpg',
     *      'images/voucher/20160401/56fe705fd37a2.jpg',
     *      'images/voucher/20160401/56fe710513c9e.jpg',
     *      'images/voucher/20160402/56fea2043dc01.jpg',
     *      'images/voucher/20160402/56fea3f18677d.jpg'
     * ];
     *
     * -- Example end --
     *
     * @param array $data 商品数据。
     * @return boolean
     */
    public static function addGoods($data) {
        if (empty($data)) {
            YCore::exception(-1, '请认真添加商品');
        }
        if (!isset($data['goods_name']) || !Validator::is_len($data['goods_name'], 1, 100, true)) {
            YCore::exception(-1, '商品名称必须1~100个字符');
        }
        if (!isset($data['slogan']) || !Validator::is_len($data['slogan'], 1, 50, true)) {
            YCore::exception(-1, '商品广告语必须1~50个字符');
        }
        if (!isset($data['weight']) || !Validator::is_number_between($data['weight'], 0, 1000000)) {
            YCore::exception(-1, '商品重量必须0~1000000个字符'); // 1000kg
        }
        if (!isset($data['description']) || !Validator::is_len($data['description'], 1, 10000, true)) {
            YCore::exception(-1, '商品详情长度必须1~10000个字符');
        }
        if (!isset($data['spec_val']) || !is_array($data['spec_val'])) {
            YCore::exception(-1, '商品规格有误');
        }
        if (!isset($data['products']) || !is_array($data['products'])) {
            YCore::exception(-1, '货品数据有误');
        }
        if (!isset($data['goods_album']) || !is_array($data['goods_album'])) {
            YCore::exception(-1, '商品图片必须上传');
        }
        if (!isset($data['is_exchange']) || !Validator::is_number_between($data['is_exchange'], 0, 1)) {
            YCore::exception(-1, '允许金币兑换值异常');
        }
        $album_count = count($data['goods_album']);
        if ($album_count < 1 || $album_count > 5) {
            YCore::exception(-1, '商品相册数量必须1~5张');
        }
        // 初始化市场价与销售价的最大最小值。
        $min_market_price = 0;
        $max_market_price = 0;
        $min_price = 0;
        $max_price = 0;
        if (!isset($data['products']) || empty($data['products']) || count($data['products']) === 0) { // 说明这是一个没有规格的商品。
            if (!Validator::is_integer($data['stock']) || !Validator::is_number_between($data['stock'], 0, 10000)) {
                YCore::exception(-1, '库存必须0~10000之间');
            }
            if (!isset($data['market_price']) || !Validator::is_number_between($data['market_price'], 0.01, 1000000)) {
                YCore::exception(-1, '市场价必须0.01~1000000之间');
            }
            if (!isset($data['sales_price']) || !Validator::is_number_between($data['sales_price'], 0.01, 1000000)) {
                YCore::exception(-1, '销售价必须0.01~1000000之间');
            }
            $data['products'][''] = [
                'stock' => $data['stock'],
                'market_price' => $data['market_price'],
                'sales_price' => $data['sales_price']
            ];
            $data['spec_val'] = []; // 如果货品一个都没得。必须把规格清掉。
                                    // 初始化市场价与销售价的最大最小值。
            $min_market_price = $data['market_price'];
            $max_market_price = $data['market_price'];
            $min_price = $data['sales_price'];
            $max_price = $data['sales_price'];
        } else {
            foreach ($data['products'] as $pro) {
                if ($min_market_price == 0 || $min_market_price > $pro['market_price']) {
                    $min_market_price = $pro['market_price'];
                }
                if ($max_market_price == 0 || $max_market_price < $pro['market_price']) {
                    $max_market_price = $pro['market_price'];
                }
                if ($min_price == 0 || $min_price > $pro['sales_price']) {
                    $min_price = $pro['sales_price'];
                }
                if ($max_price == 0 || $max_price < $pro['sales_price']) {
                    $max_price = $pro['sales_price'];
                }
            }
        }
        self::checkGoodsSpecAndProduct($data['spec_val'], $data['products']);
        $cat_model = new Category();
        $cat_info = $cat_model->fetchOne([], ['cat_id' => $data['cat_id'], 'status' => 1]);
        if (empty($cat_info)) {
            YCore::exception(-1, '分类不存在或已经删除');
        }
        if (!isset($data['freight_tpl_id']) || !$data['freight_tpl_id']) {
            $data['freight_tpl_id'] = 0;
        } else {
            $freight_tpl_model = new MallFreightTpl();
            $where = [
                'tpl_id' => $data['freight_tpl_id'],
                'status' => 1
            ];
            $freight_tpl_info = $freight_tpl_model->fetchOne([], $where);
            if (empty($freight_tpl_info)) {
                YCore::exception(-1, '运费模板不存在或已经删除');
            }
        }
        $insert_data = [
            'goods_name'       => $data['goods_name'],
            'slogan'           => $data['slogan'],
            'cat_code'         => $cat_info['cat_code'],
            'goods_img'        => $data['goods_album'][0],
            'weight'           => $data['weight'],
            'marketable'       => 0,
            'is_exchange'      => $data['is_exchange'],
            'status'           => 1,
            'min_market_price' => $min_market_price,
            'max_market_price' => $max_market_price,
            'min_price'        => $min_price,
            'max_price'        => $max_price,
            'spec_val_json'    => json_encode($data['spec_val']),
            'description'      => $data['description'],
            'listorder'        => $data['listorder'],
            'created_time'     => $_SERVER['REQUEST_TIME'],
            'created_by'       => $data['user_id'],
            'freight_tpl_id'   => $data['freight_tpl_id']
        ];
        $goods_model = new MallGoods();
        $base_model = new DbBase();
        $base_model->beginTransaction();
        $goods_id = $goods_model->insert($insert_data);
        if ($goods_id <= 0) {
            $base_model->rollBack();
            YCore::exception(-1, '商品添加失败');
        }
        try {
            self::setGoodsProduct($data['user_id'], $goods_id, $data['products']);
            self::setGoodsImage($data['user_id'], $goods_id, $data['goods_album']);
        } catch (\Exception $e) {
            $base_model->rollBack();
            YCore::exception($e->getCode(), $e->getMessage());
        }
        $base_model->commit();
        return true;
    }

    /**
     * 编辑商品。
     * -- Example start --
     * $data = [
     *      'user_id'        => '添加商品的用户ID',
     *      'goods_id'       => '被编辑商品的ID',
     *      'goods_name'     => '商品名称',
     *      'is_exchange'    => '是否允许金币兑换',
     *      'cat_id'         => '系统分类ID',
     *      'slogan'         => '广告语、运营性质标题',
     *      'weight'         => '重量。单位（g）',
     *      'listorder'      => '排序值。小到大排列。',
     *      'is_exchange'    => '是否允许金币兑换',
     *      'description'    => '商品详情。',
     *      'spec_val'       => '商品规格',
     *      'products'       => '库存与价格',
     *      'goods_album'    => '商品相册',
     *      'market_price'   => '市场价。当没有规格的时候，此值是必须的。',
     *      'sales_price'    => '销售价。当没有规格的时候，此值是必须的。',
     *      'stock'          => '库存。当没有规格的时候，此值是必须的。',
     *      'freight_tpl_id' => '运费模板ID',
     * ];
     *
     * $spec_val = [
     *      '颜色' => ['银色', '黑色'],
     *      '尺寸' => ['35', '38']
     * ];
     *
     * $products = [
     *      '颜色:银色|尺寸:35' => ['market_price' => 129, 'sales_price' => 99, 'stock' => '999'],
     *      '颜色:黑色|尺寸:35' => ['market_price' => 129, 'sales_price' => 99, 'stock' => '999'],
     *      '颜色:银色|尺寸:38' => ['market_price' => 129, 'sales_price' => 99, 'stock' => '999'],
     *      '颜色:黑色|尺寸:38' => ['market_price' => 129, 'sales_price' => 99, 'stock' => '999'],
     * ];
     *
     * # 最多五张图片。第一张图片会更新到商品主图。
     * $goods_album = [
     *      'images/voucher/20160401/56fe70362ef7e.jpg',
     *      'images/voucher/20160401/56fe705fd37a2.jpg',
     *      'images/voucher/20160401/56fe710513c9e.jpg',
     *      'images/voucher/20160402/56fea2043dc01.jpg',
     *      'images/voucher/20160402/56fea3f18677d.jpg'
     * ];
     *
     * -- Example end --
     *
     * @param array $data 商品数据。
     * @return boolean
     */
    public static function editGoods($data) {
        if (empty($data)) {
            YCore::exception(-1, '请认真添加商品');
        }
        if (!isset($data['goods_name']) || !Validator::is_len($data['goods_name'], 1, 100, true)) {
            YCore::exception(-1, '商品名称必须1~100个字符');
        }
        if (!isset($data['slogan']) || !Validator::is_len($data['slogan'], 1, 50, true)) {
            YCore::exception(-1, '商品广告语必须1~50个字符');
        }
        if (!isset($data['weight']) || !Validator::is_number_between($data['weight'], 0, 1000000)) {
            YCore::exception(-1, '商品重量必须0~1000000个字符'); // 1000kg
        }
        if (!isset($data['description']) || !Validator::is_len($data['description'], 1, 10000, true)) {
            YCore::exception(-1, '商品详情长度必须1~10000个字符');
        }
        if (!isset($data['spec_val']) || !is_array($data['spec_val'])) {
            YCore::exception(-1, '商品规格有误');
        }
        if (!isset($data['products']) || !is_array($data['products'])) {
            YCore::exception(-1, '货品数据有误');
        }
        if (!isset($data['is_exchange']) || !Validator::is_number_between($data['is_exchange'], 0, 1)) {
            YCore::exception(-1, '允许金币兑换值异常');
        }
        if (!isset($data['goods_album']) || !is_array($data['goods_album'])) {
            YCore::exception(-1, '商品图片必须上传');
        }
        $album_count = count($data['goods_album']);
        if ($album_count < 1 || $album_count > 5) {
            YCore::exception(-1, '商品相册数量必须1~5张');
        }
        // 初始化市场价与销售价的最大最小值。
        $min_market_price = 0;
        $max_market_price = 0;
        $min_price = 0;
        $max_price = 0;
        if (!isset($data['products']) || empty($data['products']) || count($data['products']) === 0) { // 说明这是一个没有规格的商品。
            if (!Validator::is_integer($data['stock']) || !Validator::is_number_between($data['stock'], 0, 10000)) {
                YCore::exception(-1, '库存必须0~10000之间');
            }
            if (!isset($data['market_price']) || !Validator::is_number_between($data['market_price'], 0.01, 1000000)) {
                YCore::exception(-1, '市场价必须0.01~1000000之间');
            }
            if (!isset($data['sales_price']) || !Validator::is_number_between($data['sales_price'], 0.01, 1000000)) {
                YCore::exception(-1, '销售价必须0.01~1000000之间');
            }
            $data['products'][''] = [
                'stock'        => $data['stock'],
                'market_price' => $data['market_price'],
                'sales_price'  => $data['sales_price']
            ];
            $data['spec_val'] = []; // 如果货品一个都没得。必须把规格清掉。
            // 初始化市场价与销售价的最大最小值。
            $min_market_price = $data['market_price'];
            $max_market_price = $data['market_price'];
            $min_price = $data['sales_price'];
            $max_price = $data['sales_price'];
        } else {
            foreach ($data['products'] as $pro) {
                if ($min_market_price == 0 || $min_market_price > $pro['market_price']) {
                    $min_market_price = $pro['market_price'];
                }
                if ($max_market_price == 0 || $max_market_price < $pro['market_price']) {
                    $max_market_price = $pro['market_price'];
                }
                if ($min_price == 0 || $min_price > $pro['sales_price']) {
                    $min_price = $pro['sales_price'];
                }
                if ($max_price == 0 || $max_price < $pro['sales_price']) {
                    $max_price = $pro['sales_price'];
                }
            }
        }
        self::checkGoodsSpecAndProduct($data['spec_val'], $data['products']);
        $cat_model = new Category();
        $cat_info  = $cat_model->fetchOne([], ['cat_id' => $data['cat_id'], 'status' => 1]);
        if (empty($cat_info)) {
            YCore::exception(-1, '分类不存在或已经删除');
        }
        $goods_model = new MallGoods();
        $goods_info = $goods_model->fetchOne([], ['goods_id' => $data['goods_id'], 'status' => 1]);
        if (empty($goods_info)) {
            YCore::exception(-1, '商品不存在或已经删除');
        }
        // 运费处理。
        if (!isset($data['freight_tpl_id']) || !$data['freight_tpl_id']) {
            $data['freight_tpl_id'] = 0;
        } else {
            $freight_tpl_model = new MallFreightTpl();
            $where = [
                'tpl_id' => $data['freight_tpl_id'],
                'status' => 1
            ];
            $freight_tpl_info = $freight_tpl_model->fetchOne([], $where);
            if (empty($freight_tpl_info)) {
                YCore::exception(-1, '运费模板不存在或已经删除');
            }
        }
        $update_data = [
            'goods_name'       => $data['goods_name'],
            'slogan'           => $data['slogan'],
            'cat_code'         => $cat_info['cat_code'],
            'goods_img'        => $data['goods_album'][0],
            'weight'           => $data['weight'],
            'min_market_price' => $min_market_price,
            'max_market_price' => $max_market_price,
            'min_price'        => $min_price,
            'max_price'        => $max_price,
            'spec_val_json'    => json_encode($data['spec_val']),
            'description'      => $data['description'],
            'listorder'        => $data['listorder'],
            'modified_time'    => $_SERVER['REQUEST_TIME'],
            'modified_by'      => $data['user_id'],
            'freight_tpl_id'   => $data['freight_tpl_id']
        ];
        $where = [
            'goods_id' => $data['goods_id'],
            'status'   => 1
        ];
        $base_model = new DbBase();
        $base_model->beginTransaction();
        $ok = $goods_model->update($update_data, $where);
        if (!$ok) {
            $base_model->rollBack();
            YCore::exception(-1, '商品保存失败');
        }
        try {
            self::setGoodsProduct($data['user_id'], $data['goods_id'], $data['products']);
            self::setGoodsImage($data['user_id'], $data['goods_id'], $data['goods_album']);
        } catch (\Exception $e) {
            $base_model->rollBack();
            YCore::exception($e->getCode(), $e->getMessage());
        }
        $base_model->commit();
        return true;
    }

    /**
     * 删除商品。
     *
     * @param number $user_id 用户ID。
     * @param number $goods_id 商品ID。
     * @return boolean
     */
    public static function deleteGoods($user_id, $goods_id) {
        $goods_model = new MallGoods();
        $where = [
            'goods_id' => $goods_id,
            'status' => 1
        ];
        $goods_info = $goods_model->fetchOne([], $where);
        if (empty($goods_info)) {
            YCore::exception(-1, '商品不存在或已经删除');
        }
        $data = [
            'status'        => 2,
            'modified_by'   => $user_id,
            'modified_time' => $_SERVER['REQUEST_TIME']
        ];
        $ok = $goods_model->update($data, $where);
        if (!$ok) {
            YCore::exception(-1, '保存失败');
        }
    }

    /**
     * 商品上下架。
     *
     * @param number $user_id 用户ID。
     * @param number $goods_id 商品ID。
     * @param number $updown 上下架状态。1上架、0下架。
     * @return boolean
     */
    public static function updownGoods($user_id, $goods_id, $updown) {
        $goods_model = new MallGoods();
        $where = [
            'goods_id' => $goods_id,
            'status'   => 1
        ];
        $goods_detail = $goods_model->fetchOne([], $where);
        if (empty($goods_detail)) {
            YCore::exception(-1, '商品不存在');
        }
        $data = [
            'modified_by'     => $user_id,
            'modified_time'   => $_SERVER['REQUEST_TIME'],
            'marketable'      => $updown ? 1 : 0,
            'marketable_time' => $_SERVER['REQUEST_TIME']
        ];
        $ok = $goods_model->update($data, $where);
        if (!$ok) {
            YCore::exception(-1, '请稍候刷新重试');
        }
        return true;
    }

    /**
     * 获取商品库存总数。
     *
     * @param number $goods_id 商品ID。
     * @return number
     */
    protected static function getGoodsStock($goods_id) {
        $default_db = new DbBase();
        $sql = 'SELECT SUM(stock) AS stock FROM mall_product WHERE goods_id = :goods_id AND status = :status';
        $params = [
            ':goods_id' => $goods_id,
            ':status'   => 1
        ];
        $data = $default_db->rawQuery($sql, $params)->rawFetchOne();
        return $data ? $data['stock'] : 0;
    }

    /**
     * 设置商品的货品数据。
     * -- Example start --
     * $products = [
     *      '颜色:银色|尺寸:35' => ['market_price' => 129, 'sales_price' => 99, 'stock' => '999'],
     *      '颜色:黑色|尺寸:35' => ['market_price' => 129, 'sales_price' => 99, 'stock' => '999'],
     *      '颜色:银色|尺寸:38' => ['market_price' => 129, 'sales_price' => 99, 'stock' => '999'],
     *      '颜色:黑色|尺寸:38' => ['market_price' => 129, 'sales_price' => 99, 'stock' => '999'],
     * ];
     * -- Example end --
     *
     * @param number $user_id 用户ID。如果此程序放在管理后台就是管理员ID。如果是有商家中心就是商家账号ID。
     * @param number $goods_id 商品ID。
     * @param array $products 货品数据。
     * @return boolean
     */
    protected static function setGoodsProduct($user_id, $goods_id, array $products = []) {
        // [1] 得到修改前的货品数据。
        $default_db = new DbBase();
        $sql = 'SELECT product_id,spec_val FROM mall_product WHERE goods_id = :goods_id AND status = :status';
        $params = [
            ':goods_id' => $goods_id,
            ':status'   => 1
        ];
        $old_product_result = $default_db->rawQuery($sql, $params)->rawFetchAll();
        $old_product_list = [];
        foreach ($old_product_result as $op) {
            $old_product_list[$op['spec_val']] = $op['product_id'];
        }
        $exists_old_product_ids = [];
        // [2] 循环处理货品。
        foreach ($products as $spec_val => $pro) {
            // [2.1] 参数判断。
            if (!Validator::is_integer($pro['stock']) || !Validator::is_number_between($pro['stock'], 0, 10000)) {
                YCore::exception(-1, '库存必须0~10000之间');
            }
            if (!Validator::is_number_between($pro['sales_price'], 0.01, 1000000)) {
                YCore::exception(-1, '销售价必须0.01~1000000之间');
            }
            if (!Validator::is_number_between($pro['market_price'], 0.01, 1000000)) {
                YCore::exception(-1, '市场价必须0.01~1000000之间');
            }
            // [2.2] 货品存在与否判断。
            $product_model = new MallProduct();
            if (array_key_exists($spec_val, $old_product_list)) { // 存在，则只作更新。
                $product_id = $old_product_list[$spec_val];
                $where = [
                    'product_id' => $product_id,
                    'status'     => 1
                ];
                $data = [
                    'market_price'  => $pro['market_price'],
                    'sales_price'   => $pro['sales_price'],
                    'stock'         => $pro['stock'],
                    'modified_by'   => $user_id,
                    'modified_time' => $_SERVER['REQUEST_TIME']
                ];
                $ok = $product_model->update($data, $where);
                if (!$ok) {
                    YCore::exception(-1, '服务器繁忙,请稍候重试');
                }
                $exists_old_product_ids[] = $product_id;
            } else { // 添加。
                $data = [
                    'spec_val'     => $spec_val,
                    'status'       => 1,
                    'market_price' => $pro['market_price'],
                    'sales_price'  => $pro['sales_price'],
                    'stock'        => $pro['stock'],
                    'created_by'   => $user_id,
                    'created_time' => $_SERVER['REQUEST_TIME'],
                    'goods_id'     => $goods_id
                ];
                $ok = $product_model->insert($data);
                if (!$ok) {
                    YCore::exception(-1, '服务器繁忙,请稍候重试');
                }
            }
        }
        // [3] 将不在新货品中的旧货品删掉。
        foreach ($old_product_list as $product_id) {
            if (in_array($product_id, $exists_old_product_ids)) {
                continue;
            }
            $where = [
                'product_id' => $product_id
            ];
            $update_data = [
                'status'        => 2,
                'modified_by'   => $user_id,
                'modified_time' => $_SERVER['REQUEST_TIME']
            ];
            $ok = $product_model->update($update_data, $where);
            if (!$ok) {
                YCore::exception(-1, '服务器繁忙,请稍候重试');
            }
        }
        return true;
    }

    /**
     * 验证商品规格与货品规格是否匹配。
     * -- Example start --
     * $spec_val = [
     *      '颜色' => ['银色', '黑色'],
     *      '尺寸' => ['35', '38']
     * ];
     *
     * $products = [
     *      '颜色:银色|尺寸:35' => ['market_price' => 129, 'sales_price' => 99, 'stock' => '999'],
     *      '颜色:黑色|尺寸:35' => ['market_price' => 129, 'sales_price' => 99, 'stock' => '999'],
     *      '颜色:银色|尺寸:38' => ['market_price' => 129, 'sales_price' => 99, 'stock' => '999'],
     *      '颜色:黑色|尺寸:38' => ['market_price' => 129, 'sales_price' => 99, 'stock' => '999'],
     * ];
     * -- Example end --
     *
     * @param array $spec 商品规格数据。
     * @param array $products 货品数据。
     * @return void
     */
    protected static function checkGoodsSpecAndProduct(array $spec = [], array $products = []) {
        if (empty($spec) || empty($products)) { // 此种情况认为是没有规格的商品。
            return;
        }
        $goods_spec_count = count($spec);
        foreach ($products as $spec_val => $pro) {
            $key_val = explode('|', $spec_val);
            $spec_count = count($key_val); // 得到货品规格中的规格对数量。如果这个数据与实际的商品规格数量不对应。说明有误。
            if ($goods_spec_count != $spec_count) {
                YCore::exception(-1, '商品规格设置有误');
            }
            if (empty($key_val)) {
                YCore::exception(-1, '商品规格设置有误');
            }
            foreach ($key_val as $key => $val) {
                $s_v = explode(':', $val);
                if (count($s_v) != 2) {
                    YCore::exception(-1, '商品规格设置有误');
                }
                if (!array_key_exists($s_v[0], $spec)) { // $s_v[0] 是规格名称。 $s_v[1] 是规格值。
                    YCore::exception(-1, '商品规格设置有误');
                }
                if (!in_array($s_v[1], $spec[$s_v[0]])) {
                    YCore::exception(-1, '商品规格设置有误');
                }
            }
        }
    }

    /**
     * 设置商品相册。
     *
     * @param number $user_id 添加相册的用户ID。
     * @param number $goods_id 商品ID。
     * @param array $album 相册。
     * @return boolean
     */
    protected static function setGoodsImage($user_id, $goods_id, $album) {
        $image_model = new MallGoodsImage();
        // [1] 查找该商品原相册图片。
        $where = [
            'goods_id' => $goods_id,
            'status'   => 1
        ];
        $columns = [
            'image_id',
            'image_url'
        ];
        $old_image = $image_model->fetchAll($columns, $where);
        $_old_image = [];
        foreach ($old_image as $item) {
            $_old_image[$item['image_url']] = $item['image_id'];
        }
        $old_image = $_old_image;
        // [2] 判断新入库的图片是否已经存在，已经存在则不做任何修改。
        // 如果旧图片在新图片中不存在，则要进行删除。
        $exists_old_image_id = [];
        foreach ($album as $image_url) {
            if (!empty($old_image) && array_key_exists($image_url, $old_image)) { // 存在。
                $exists_old_image_id[] = $old_image[$image_url];
            } else { // 不存在。
                $insert_data = [
                    'goods_id'     => $goods_id,
                    'image_url'    => $image_url,
                    'status'       => 1,
                    'created_time' => $_SERVER['REQUEST_TIME'],
                    'created_by'   => $user_id
                ];
                $id = $image_model->insert($insert_data);
                if (!$id) {
                    YCore::exception(-1, '相册图片保存失败');
                }
            }
        }
        // [3] 得到不在新图片中的旧图片ID。
        $not_exist_image_id = [];
        foreach ($old_image as $image_id) {
            if (!in_array($image_id, $exists_old_image_id)) {
                $not_exist_image_id[] = $image_id;
            }
        }
        // [4] 删除不在新图片中的旧图片ID对应的图片。
        if (!empty($not_exist_image_id)) {
            $default_db = new DbBase();
            $where = [
                'image_id' => ['IN', $not_exist_image_id]
            ];
            $where_info = $default_db->parseWhereCondition($where);
            $sql = "UPDATE mall_goods_image SET status = :status, modified_by = :modified_by, modified_time = :modified_time " . "WHERE {$where_info['where']}";
            $params = $where_info['params'];
            $params[':status']        = 2;
            $params[':modified_by']   = $user_id;
            $params[':modified_time'] = $_SERVER['REQUEST_TIME'];
            $ok = $default_db->rawExec($sql, $params);
            if (!$ok) {
                YCore::exception(-1, '相册图片保存失败');
            }
        }
        return true;
    }

    /**
     * 扣减货品库存。
     * -- 1、购买商品成功通过此方法扣减库存。
     *
     * @param number $product_id 货品ID。
     * @param number $stock 扣减的库存。
     * @return boolean
     */
    public static function deductionProductStock($product_id, $stock) {
        $stock = intval($stock); // 由于 stock = stock - :stock 使用PDO不支持。所以，直接写在SQL里面要进行强制类型转换避免注入。
        $default_db = new DbBase();
        $sql = "UPDATE mall_product SET stock = stock - {$stock} WHERE product_id = :product_id AND stock >= :stock";
        $params = [
            ':stock'      => $stock,
            ':product_id' => $product_id
        ];
        return $default_db->rawExec($sql, $params);
    }

    /**
     * 还原货品库存。
     * -- 1、订单关闭、取消需要把库存还原回来。
     *
     * @param number $product_id 货品ID。
     * @param number $stock 扣减的库存。
     * @return boolean
     */
    public static function restoreProductStock($product_id, $stock) {
        $default_db = new DbBase();
        $sql = 'UPDATE mall_product SET stock = stock + :stock WHERE product_id = :product_id';
        $params = [
            ':stock'      => $stock,
            ':product_id' => $product_id
        ];
        return $default_db->rawExec($sql, $params);
    }
}