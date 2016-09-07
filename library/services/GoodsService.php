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
use models\MallShopCategory;
use common\YUrl;
use models\MallFreightTpl;
class GoodsService extends BaseService {
    
    /**
     * 获取商品列表[管理后台版]。
     * 
     * @param number $shop_id 商家ID。
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
    public static function getBackendGoodsList($shop_id = -1, $updown = -1, $goods_name = '', $cat_id = -1, $start_price = '', $end_price = '', $is_delete_show = 0, $page = 1, $count = 20) {
        $offset = self::getPaginationOffset($page, $count);
        $from_table = ' FROM mall_goods ';
        $columns = ' * ';
        $where = ' WHERE 1 ';
        $params = [];
        if (! $is_delete_show) {
            $where .= ' AND status = :status ';
            $params[':status'] = 1;
        }
        if ($shop_id != - 1) {
            $where .= ' AND shop_id LIKE :shop_id ';
            $params[':shop_id'] = $shop_id;
        }
        if (strlen($goods_name) > 0) {
            $where .= ' AND goods_name LIKE :goods_name ';
            $params[':goods_name'] = "%{$goods_name}%";
        }
        if ($cat_id != - 1) {
            $category_model = new Category();
            $cat_info = $category_model->fetchOne([], [
                    'cat_id' => $cat_id,'status' => 1 
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
            if (! Validator::is_integer($start_price)) {
                YCore::exception(- 1, '查询价格必须是整数');
            }
            $where .= ' AND min_price <= :start_price ';
            $params[':start_price'] = $start_price;
        }
        if (strlen($end_price) > 0) {
            if (! Validator::is_integer($end_price)) {
                YCore::exception(- 1, '查询价格必须是整数');
            }
            $where .= ' AND max_price >= :end_price ';
            $params[':end_price'] = strtotime($end_price);
        }
        $order_by = ' ORDER BY goods_id DESC ';
        $sql = "SELECT COUNT(1) AS count {$from_table} {$where}";
        $default_db = new DbBase();
        $count_data = $default_db->rawQuery($sql, $params)->rawFetchOne();
        $total = $count_data ? $count_data['count'] : 0;
        $sql = "SELECT {$columns} {$from_table} {$where} {$order_by} LIMIT {$offset},{$count}";
        $list = $default_db->rawQuery($sql, $params)->rawFetchAll();
        foreach ($list as $k => $v) {
            $v['goods_img'] = YUrl::filePath($v['goods_img']);
            $shop_info = ShopService::getShopDetail($v['shop_id']);
            $v['shop_name'] = $shop_info['shop_name'];
            $list[$k] = $v;
        }
        $result = array(
                'list' => $list,'total' => $total,'page' => $page,'count' => $count,
                'isnext' => self::IsHasNextPage($total, $page, $count) 
        );
        return $result;
    }
    
    /**
     * 获取商品列表[商家中心版]。
     * 
     * @param number $shop_id 商家ID。
     * @param number $updown 上下架状态。-1不限、1上架、0下架。
     * @param string $goods_name 商品名称。
     * @param number $cat_id 分类ID。
     * @param number $custom_cat_id 自定义分类ID。
     * @param number $start_price 价格最小值。
     * @param number $end_price 价格最大值。
     * @param number $page 当前页码。
     * @param number $count 每页显示条数。
     * @return array
     */
    public static function getShopGoodsList($shop_id, $updown = -1, $goods_name = '', $cat_id = -1, $custom_cat_id = -1, $start_price = '', $end_price = '', $page = 1, $count = 20) {
        $offset = self::getPaginationOffset($page, $count);
        $from_table = ' FROM mall_goods ';
        $columns = ' goods_id,goods_name,slogan,min_market_price,max_market_price,min_price,' . ' max_price,goods_img,buy_count,month_buy_count,marketable,marketable_time,limit_count ';
    $where = ' WHERE status = :status AND shop_id = :shop_id ';
    $params = [
            ':status' => 1,':shop_id' => $shop_id 
    ];
    if ($updown != - 1) {
        $where .= ' AND marketable = :marketable ';
        $params[':marketable'] = $updown;
    }
    if (strlen($goods_name) > 0) {
        $where .= ' AND goods_name LIKE :goods_name ';
        $params[':goods_name'] = "%{$goods_name}%";
    }
    if ($cat_id != - 1) {
        $category_model = new Category();
        $cat_info = $category_model->fetchOne([], [
                'cat_id' => $cat_id,'status' => 1 
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
    if ($custom_cat_id != - 1) {
        $where .= ' AND custom_cat_id = :custom_cat_id ';
        $params[':custom_cat_id'] = $custom_cat_id;
    }
    if (strlen($start_price) > 0) {
        if (! Validator::is_integer($start_price)) {
            YCore::exception(- 1, '查询价格必须是整数');
        }
        $where .= ' AND min_price <= :start_price ';
        $params[':start_price'] = $start_price;
    }
    if (strlen($end_price) > 0) {
        if (! Validator::is_integer($end_price)) {
            YCore::exception(- 1, '查询价格必须是整数');
        }
        $where .= ' AND max_price >= :end_price ';
        $params[':end_price'] = strtotime($end_price);
    }
    $order_by = ' ORDER BY goods_id DESC ';
    $sql = "SELECT COUNT(1) AS count {$from_table} {$where}";
    $default_db = new DbBase();
    $count_data = $default_db->rawQuery($sql, $params)->rawFetchOne();
    $total = $count_data ? $count_data['count'] : 0;
    $sql = "SELECT {$columns} {$from_table} {$where} {$order_by} LIMIT {$offset},{$count}";
    $list = $default_db->rawQuery($sql, $params)->rawFetchAll();
    foreach ($list as $k => $v) {
        $v['stock'] = self::getGoodsStock($v['goods_id']);
        $v['goods_img'] = YUrl::filePath($v['goods_img']);
        $v['marketable_time'] = $v['marketable_time'] ? date('Y-m-d H:i:s', $v['marketable_time']) : '-';
        $list[$k] = $v;
    }
    $result = array(
            'list' => $list,'total' => $total,'page' => $page,'count' => $count,
            'isnext' => self::IsHasNextPage($total, $page, $count) 
    );
    return $result;
}

/**
 * 获取商品列表[普通用户版]。
 * 
 * @param string $keyword 搜索关键词。模糊搜索商品名称。
 * @param number $shop_id 商家ID。-1全部。
 * @param number $cat_id 分类ID。-1全部。
 * @param number $custom_cat_id 自定义商品分类ID。-1全部。当$shop_id不等于-1时才有效。
 * @param number $start_price 价格最小值。
 * @param number $end_price 价格最大值。
 * @param number $page 当前页码。
 * @param number $count 每页显示条数。
 * @return array
 */
public static function getGoodsList($keyword = '', $shop_id = -1, $cat_id = -1, $custom_cat_id = -1, $start_price = '', $end_price = '', $page = 1, $count = 20) {
    $offset = self::getPaginationOffset($page, $count);
    $from_table = ' FROM mall_goods ';
    $columns = ' * ';
    $where = ' WHERE status = :status ';
    $params = [
            ':status' => 1 
    ];
    if ($shop_id != - 1) {
        $where .= ' AND shop_id >= :shop_id ';
        $params[':shop_id'] = $shop_id;
    }
    if (strlen($keyword) > 0) {
        $where .= ' AND goods_name LIKE :goods_name ';
        $params[':goods_name'] = "%{$keyword}%";
    }
    if ($cat_id != - 1) {
        $category_model = new Category();
        $cat_info = $category_model->fetchOne([], [
                'cat_id' => $cat_id,'status' => 1 
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
        if (! Validator::is_integer($start_price)) {
            YCore::exception(- 1, '查询价格必须是整数');
        }
        $where .= ' AND min_price <= :start_price ';
        $params[':start_price'] = $start_price;
    }
    if (strlen($end_price) > 0) {
        if (! Validator::is_integer($end_price)) {
            YCore::exception(- 1, '查询价格必须是整数');
        }
        $where .= ' AND max_price >= :end_price ';
        $params[':end_price'] = strtotime($end_price);
    }
    if ($shop_id != - 1 && $custom_cat_id != - 1) {
        $where .= ' AND custom_cat_id >= :custom_cat_id ';
        $params[':custom_cat_id'] = $custom_cat_id;
    }
    $order_by = ' ORDER BY goods_id DESC ';
    $sql = "SELECT COUNT(1) AS count {$from_table} {$where}";
    $default_db = new DbBase();
    $count_data = $default_db->rawQuery($sql, $params)->rawFetchOne();
    $total = $count_data ? $count_data['count'] : 0;
    $sql = "SELECT {$columns} {$from_table} {$where} {$order_by} LIMIT {$offset},{$count}";
    $list = $default_db->rawQuery($sql, $params)->rawFetchAll();
    foreach ($list as $k => $v) {
        $v['goods_img'] = YUrl::filePath($v['goods_img']);
        $list[$k] = $v;
    }
    $result = array(
            'list' => $list,'total' => $total,'page' => $page,'count' => $count,
            'isnext' => self::IsHasNextPage($total, $page, $count) 
    );
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
    $goods_detail = $goods_model->fetchOne([], [
            'goods_id' => $goods_id,'status' => 1 
    ]);
    if (empty($goods_detail)) {
        YCore::exception(- 1, '商品不存在');
    }
    $goods_image_model = new MallGoodsImage();
    $goods_image = $goods_image_model->fetchAll([
            'image_url' 
    ], [
            'goods_id' => $goods_id,'status' => 1 
    ], 0, 'image_id ASC');
    if (empty($goods_image)) {
        YCore::exception(- 1, '商品相册异常');
    }
    $goods_detail['goods_image'] = $goods_image;
    return $goods_detail;
}

/**
 * 添加商品。
 * -- Example start --
 * $data = [
 * 'user_id' => '添加商品的用户ID',
 * 'shop_id' => '商家ID',
 * 'goods_name' => '商品名称',
 * 'cat_id' => '系统分类ID',
 * 'custom_cat_id' => '商家自定义分类ID',
 * 'slogan' => '广告语、运营性质标题',
 * 'weight' => '重量。单位（g）',
 * 'listorder' => '排序值。小到大排列。',
 * 'description' => '商品详情。',
 * 'spec_val' => '商品规格',
 * 'products' => '库存与价格',
 * 'goods_album' => '商品相册',
 * 'market_price' => '市场价。当没有规格的时候，此值是必须的。',
 * 'sales_price' => '销售价。当没有规格的时候，此值是必须的。',
 * 'stock' => '库存。当没有规格的时候，此值是必须的。',
 * 'freight_tpl_id' => '运费模板ID',
 * ];
 *
 * $spec_val = [
 * '颜色' => ['银色', '黑色'],
 * '尺寸' => ['35', '38']
 * ];
 *
 * $products = [
 * '颜色:银色|尺寸:35' => ['market_price' => 129, 'sales_price' => 99, 'stock' => '999'],
 * '颜色:黑色|尺寸:35' => ['market_price' => 129, 'sales_price' => 99, 'stock' => '999'],
 * '颜色:银色|尺寸:38' => ['market_price' => 129, 'sales_price' => 99, 'stock' => '999'],
 * '颜色:黑色|尺寸:38' => ['market_price' => 129, 'sales_price' => 99, 'stock' => '999'],
 * ];
 *
 * # 最多五张图片。第一张图片会更新到商品主图。
 * $goods_album = [
 * 'images/voucher/20160401/56fe70362ef7e.jpg',
 * 'images/voucher/20160401/56fe705fd37a2.jpg',
 * 'images/voucher/20160401/56fe710513c9e.jpg',
 * 'images/voucher/20160402/56fea2043dc01.jpg',
 * 'images/voucher/20160402/56fea3f18677d.jpg'
 * ];
 *
 * -- Example end --
 * 
 * @param array $data 商品数据。
 * @return boolean
 */
public static function addGoods($data) {
    if (empty($data)) {
        YCore::exception(- 1, '请认真添加商品');
    }
    if (! isset($data['goods_name']) || ! Validator::is_len($data['goods_name'], 1, 100, true)) {
        YCore::exception(- 1, '商品名称必须1~100个字符');
    }
    if (! isset($data['slogan']) || ! Validator::is_len($data['slogan'], 1, 50, true)) {
        YCore::exception(- 1, '商品广告语必须1~50个字符');
    }
    if (! isset($data['weight']) || ! Validator::is_number_between($data['weight'], 0, 1000000)) {
        YCore::exception(- 1, '商品重量必须0~1000000个字符'); // 1000kg
    }
    if (! isset($data['description']) || ! Validator::is_len($data['description'], 1, 10000, true)) {
        YCore::exception(- 1, '商品详情长度必须1~10000个字符');
    }
    if (! isset($data['spec_val']) || ! is_array($data['spec_val'])) {
        YCore::exception(- 1, '商品规格有误');
    }
    if (! isset($data['products']) || ! is_array($data['products'])) {
        YCore::exception(- 1, '货品数据有误');
    }
    if (! isset($data['goods_album']) || ! is_array($data['goods_album'])) {
        YCore::exception(- 1, '商品图片必须上传');
    }
    $album_count = count($data['goods_album']);
    if ($album_count < 1 || $album_count > 5) {
        YCore::exception(- 1, '商品相册数量必须1~5张');
    }
    // 初始化市场价与销售价的最大最小值。
    $min_market_price = 0;
    $max_market_price = 0;
    $min_price = 0;
    $max_price = 0;
    if (! isset($data['products']) || empty($data['products']) || count($data['products']) === 0) { // 说明这是一个没有规格的商品。
        if (! Validator::is_integer($data['stock']) || ! Validator::is_number_between($data['stock'], 0, 10000)) {
            YCore::exception(- 1, '库存必须0~10000之间');
        }
        if (! isset($data['market_price']) || ! Validator::is_number_between($data['market_price'], 0.01, 1000000)) {
            YCore::exception(- 1, '市场价必须0.01~1000000之间');
        }
        if (! isset($data['sales_price']) || ! Validator::is_number_between($data['sales_price'], 0.01, 1000000)) {
            YCore::exception(- 1, '销售价必须0.01~1000000之间');
        }
        $data['products'][''] = [
                'stock' => $data['stock'],'market_price' => $data['market_price'],'sales_price' => $data['sales_price'] 
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
    $cat_info = $cat_model->fetchOne([], [
            'cat_id' => $data['cat_id'],'status' => 1 
    ]);
    if (empty($cat_info)) {
        YCore::exception(- 1, '分类不存在或已经删除');
    }
    if ($data['custom_cat_id'] != - 1) {
        $shop_category_model = new MallShopCategory();
        $shop_cat_info = $shop_category_model->fetchOne([], [
                'cat_id' => $data['custom_cat_id'] 
        ]);
        if (empty($shop_cat_info)) {
            YCore::exception(- 1, '自定义商品分类不存在');
        }
    }
    $freight_tpl_model = new MallFreightTpl();
    $where = [
            'tpl_id' => $data['freight_tpl_id'],'shop_id' => $data['shop_id'],'status' => 1 
    ];
    $freight_tpl_info = $freight_tpl_model->fetchOne([], $where);
    if (empty($freight_tpl_info)) {
        YCore::exception(- 1, '运费模板不存在或已经删除');
    }
    $insert_data = [
            'shop_id' => $data['shop_id'],'goods_name' => $data['goods_name'],'slogan' => $data['slogan'],
            'cat_code' => $cat_info['cat_code'],'custom_cat_id' => $data['custom_cat_id'],
            'goods_img' => $data['goods_album'][0],'weight' => $data['weight'],'marketable' => 0,'status' => 1,
            'min_market_price' => $min_market_price,'max_market_price' => $max_market_price,
            'min_price' => $min_price,'max_price' => $max_price,'spec_val_json' => json_encode($data['spec_val']),
            'description' => $data['description'],'listorder' => $data['listorder'],
            'created_time' => $_SERVER['REQUEST_TIME'],'created_by' => $data['user_id'],
            'freight_tpl_id' => $data['freight_tpl_id'] 
    ];
    $goods_model = new MallGoods();
    $base_model = new DbBase();
    $base_model->beginTransaction();
    $goods_id = $goods_model->insert($insert_data);
    if ($goods_id <= 0) {
        $base_model->rollBack();
        YCore::exception(- 1, '商品添加失败');
    }
    try {
        self::setGoodsProduct($data['user_id'], $goods_id, $data['products']);
        self::setGoodsImage($data['user_id'], $goods_id, $data['goods_album']);
    } catch ( \Exception $e ) {
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
 * 'user_id' => '添加商品的用户ID',
 * 'shop_id' => '商家ID',
 * 'goods_id' => '被编辑商品的ID',
 * 'goods_name' => '商品名称',
 * 'cat_id' => '系统分类ID',
 * 'custom_cat_id' => '商家自定义商品分类ID',
 * 'slogan' => '广告语、运营性质标题',
 * 'weight' => '重量。单位（g）',
 * 'listorder' => '排序值。小到大排列。',
 * 'description' => '商品详情。',
 * 'spec_val' => '商品规格',
 * 'products' => '库存与价格',
 * 'goods_album' => '商品相册',
 * 'market_price' => '市场价。当没有规格的时候，此值是必须的。',
 * 'sales_price' => '销售价。当没有规格的时候，此值是必须的。',
 * 'stock' => '库存。当没有规格的时候，此值是必须的。',
 * 'freight_tpl_id' => '运费模板ID',
 * ];
 *
 * $spec_val = [
 * '颜色' => ['银色', '黑色'],
 * '尺寸' => ['35', '38']
 * ];
 *
 * $products = [
 * '颜色:银色|尺寸:35' => ['market_price' => 129, 'sales_price' => 99, 'stock' => '999'],
 * '颜色:黑色|尺寸:35' => ['market_price' => 129, 'sales_price' => 99, 'stock' => '999'],
 * '颜色:银色|尺寸:38' => ['market_price' => 129, 'sales_price' => 99, 'stock' => '999'],
 * '颜色:黑色|尺寸:38' => ['market_price' => 129, 'sales_price' => 99, 'stock' => '999'],
 * ];
 *
 * # 最多五张图片。第一张图片会更新到商品主图。
 * $goods_album = [
 * 'images/voucher/20160401/56fe70362ef7e.jpg',
 * 'images/voucher/20160401/56fe705fd37a2.jpg',
 * 'images/voucher/20160401/56fe710513c9e.jpg',
 * 'images/voucher/20160402/56fea2043dc01.jpg',
 * 'images/voucher/20160402/56fea3f18677d.jpg'
 * ];
 *
 * -- Example end --
 * 
 * @param array $data 商品数据。
 * @return boolean
 */
public static function editGoods($data) {
    if (empty($data)) {
        YCore::exception(- 1, '请认真添加商品');
    }
    if (! isset($data['goods_name']) || ! Validator::is_len($data['goods_name'], 1, 100, true)) {
        YCore::exception(- 1, '商品名称必须1~100个字符');
    }
    if (! isset($data['slogan']) || ! Validator::is_len($data['slogan'], 1, 50, true)) {
        YCore::exception(- 1, '商品广告语必须1~50个字符');
    }
    if (! isset($data['weight']) || ! Validator::is_number_between($data['weight'], 0, 1000000)) {
        YCore::exception(- 1, '商品重量必须0~1000000个字符'); // 1000kg
    }
    if (! isset($data['description']) || ! Validator::is_len($data['description'], 1, 10000, true)) {
        YCore::exception(- 1, '商品详情长度必须1~10000个字符');
    }
    if (! isset($data['spec_val']) || ! is_array($data['spec_val'])) {
        YCore::exception(- 1, '商品规格有误');
    }
    if (! isset($data['products']) || ! is_array($data['products'])) {
        YCore::exception(- 1, '货品数据有误');
    }
    if (! isset($data['goods_album']) || ! is_array($data['goods_album'])) {
        YCore::exception(- 1, '商品图片必须上传');
    }
    $album_count = count($data['goods_album']);
    if ($album_count < 1 || $album_count > 5) {
        YCore::exception(- 1, '商品相册数量必须1~5张');
    }
    // 初始化市场价与销售价的最大最小值。
    $min_market_price = 0;
    $max_market_price = 0;
    $min_price = 0;
    $max_price = 0;
    if (! isset($data['products']) || empty($data['products']) || count($data['products']) === 0) { // 说明这是一个没有规格的商品。
        if (! Validator::is_integer($data['stock']) || ! Validator::is_number_between($data['stock'], 0, 10000)) {
            YCore::exception(- 1, '库存必须0~10000之间');
        }
        if (! isset($data['market_price']) || ! Validator::is_number_between($data['market_price'], 0.01, 1000000)) {
            YCore::exception(- 1, '市场价必须0.01~1000000之间');
        }
        if (! isset($data['sales_price']) || ! Validator::is_number_between($data['sales_price'], 0.01, 1000000)) {
            YCore::exception(- 1, '销售价必须0.01~1000000之间');
        }
        $data['products'][''] = [
                'stock' => $data['stock'],'market_price' => $data['market_price'],'sales_price' => $data['sales_price'] 
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
    $cat_info = $cat_model->fetchOne([], [
            'cat_id' => $data['cat_id'],'status' => 1 
    ]);
    if (empty($cat_info)) {
        YCore::exception(- 1, '分类不存在或已经删除');
    }
    $goods_model = new MallGoods();
    $goods_info = $goods_model->fetchOne([], [
            'goods_id' => $data['goods_id'],'status' => 1 
    ]);
    if (empty($goods_info)) {
        YCore::exception(- 1, '商品不存在或已经删除');
    }
    if ($data['custom_cat_id'] != - 1) {
        $shop_category_model = new MallShopCategory();
        $shop_cat_info = $shop_category_model->fetchOne([], [
                'cat_id' => $data['custom_cat_id'] 
        ]);
        if (empty($shop_cat_info)) {
            YCore::exception(- 1, '自定义商品分类不存在');
        }
    }
    // 判断商品编辑前后，商品规格是否有变化。
    $old_spec = json_decode($goods_info['spec_val_json'], true);
    $diff_result = self::diffOldAndNewSpec($old_spec, $data['spec_val']);
    $freight_tpl_model = new MallFreightTpl();
    $where = [
            'tpl_id' => $data['freight_tpl_id'],'shop_id' => $data['shop_id'],'status' => 1 
    ];
    $freight_tpl_info = $freight_tpl_model->fetchOne([], $where);
    if (empty($freight_tpl_info)) {
        YCore::exception(- 1, '运费模板不存在或已经删除');
    }
    $update_data = [
            'shop_id' => $data['shop_id'],'goods_name' => $data['goods_name'],'slogan' => $data['slogan'],
            'custom_cat_id' => $data['custom_cat_id'],'cat_code' => $cat_info['cat_code'],
            'goods_img' => $data['goods_album'][0],'weight' => $data['weight'],'min_market_price' => $min_market_price,
            'max_market_price' => $max_market_price,'min_price' => $min_price,'max_price' => $max_price,
            'spec_val_json' => json_encode($data['spec_val']),'description' => $data['description'],
            'listorder' => $data['listorder'],'modified_time' => $_SERVER['REQUEST_TIME'],
            'modified_by' => $data['user_id'],'freight_tpl_id' => $data['freight_tpl_id'] 
    ];
    $where = [
            'goods_id' => $data['goods_id'],'status' => 1 
    ];
    $base_model = new DbBase();
    $base_model->beginTransaction();
    $ok = $goods_model->update($update_data, $where);
    if (! $ok) {
        $base_model->rollBack();
        YCore::exception(- 1, '商品保存失败');
    }
    try {
        self::setGoodsProduct($data['user_id'], $data['goods_id'], $data['products'], $diff_result);
        self::setGoodsImage($data['user_id'], $data['goods_id'], $data['goods_album']);
    } catch ( \Exception $e ) {
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
            'goods_id' => $goods_id,'status' => 1 
    ];
    $goods_info = $goods_model->fetchOne([], $where);
    if (empty($goods_info)) {
        YCore::exception(- 1, '商品不存在或已经删除');
    }
    $data = [
            'status' => 2,'modified_by' => $user_id,'modified_time' => $_SERVER['REQUEST_TIME'] 
    ];
    return $goods_model->update($data, $where);
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
            'goods_id' => $goods_id,'status' => 1 
    ];
    $goods_detail = $goods_model->fetchOne([], $where);
    if (empty($goods_detail)) {
        YCore::exception(- 1, '商品不存在');
    }
    $data = [
            'modified_by' => $user_id,'modified_time' => $_SERVER['REQUEST_TIME'],'marketable' => $updown ? 1 : 0,
            'marketable_time' => $_SERVER['REQUEST_TIME'] 
    ];
    $ok = $goods_model->update($data, $where);
    if (! $ok) {
        YCore::exception(- 1, '请稍候刷新重试');
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
            ':goods_id' => $goods_id,':status' => 1 
    ];
    $data = $default_db->rawQuery($sql, $params)->rawFetchOne();
    return $data ? $data['stock'] : 0;
}

/**
 * 设置商品的货品数据。
 * -- Example start --
 * $products = [
 * '颜色:银色|尺寸:35' => ['market_price' => 129, 'sales_price' => 99, 'stock' => '999'],
 * '颜色:黑色|尺寸:35' => ['market_price' => 129, 'sales_price' => 99, 'stock' => '999'],
 * '颜色:银色|尺寸:38' => ['market_price' => 129, 'sales_price' => 99, 'stock' => '999'],
 * '颜色:黑色|尺寸:38' => ['market_price' => 129, 'sales_price' => 99, 'stock' => '999'],
 * ];
 * -- Example end --
 * 
 * @param number $user_id 用户ID。如果此程序放在管理后台就是管理员ID。如果是有商家中心就是商家账号ID。
 * @param number $goods_id 商品ID。
 * @param array $products 货品数据。
 * @param array $diff_result 新旧规格对比结果。
 * @return boolean
 */
protected static function setGoodsProduct($user_id, $goods_id, array $products = [], array $diff_result = []) {
    // 如果对比结果为空，只有添加商品的时候才会出现。此时需要重新创建所有的规格商品。
    if (empty($diff_result)) {
        $diff_result = [
                'add_spec_val' => [],'cut_spec_val' => [],'diff_type' => 1 
        ];
    }
    switch ($diff_result['diff_type']) {
        case '0' : // 没有任何变化。只更新市场价、销售价、库存。
            $product_model = new MallProduct();
            foreach ($products as $spec_val => $product) {
                if (! Validator::is_integer($product['stock']) || ! Validator::is_number_between($product['stock'], 0, 10000)) {
                YCore::exception(- 1, '库存必须0~10000之间');
            }
            if (! Validator::is_number_between($product['sales_price'], 0.01, 1000000)) {
                YCore::exception(- 1, '销售价必须0.01~1000000之间');
            }
            if (! Validator::is_number_between($product['market_price'], 0.01, 1000000)) {
                YCore::exception(- 1, '市场价必须0.01~1000000之间');
            }
            $where = [
                    'goods_id' => $goods_id,'status' => 1,'spec_val' => $spec_val 
            ];
            $update_data = [
                    'market_price' => $product['market_price'],'sales_price' => $product['sales_price'],
                    'stock' => $product['stock'],'modified_by' => $user_id,'modified_time' => $_SERVER['REQUEST_TIME'] 
            ];
            $ok = $product_model->update($update_data, $where);
            if (! $ok) {
                YCore::exception(- 1, '规格保存失败');
            }
        }
        break;
    case 1 : // 规格名发生增加或减少，需要重新创建所有货品。
        $product_model = new MallProduct();
        $product_model->deleteGoodsProduct($user_id, $goods_id);
        foreach ($products as $spec_val => $product) {
            if (! Validator::is_integer($product['stock']) || ! Validator::is_number_between($product['stock'], 0, 10000)) {
                YCore::exception(- 1, '库存必须0~10000之间');
            }
            if (! Validator::is_number_between($product['sales_price'], 0.01, 1000000)) {
                YCore::exception(- 1, '销售价必须0.01~1000000之间');
            }
            if (! Validator::is_number_between($product['market_price'], 0.01, 1000000)) {
                YCore::exception(- 1, '市场价必须0.01~1000000之间');
            }
            $insert_data = [
                    'market_price' => $product['market_price'],'sales_price' => $product['sales_price'],
                    'spec_val' => $spec_val,'goods_id' => $goods_id,'stock' => $product['stock'],'status' => 1,
                    'created_by' => $user_id,'created_time' => $_SERVER['REQUEST_TIME'] 
            ];
            $ok = $product_model->insert($insert_data);
            if (! $ok) {
                YCore::exception(- 1, '规格保存失败');
            }
        }
        break;
    case 2 : // 只对差异化部分进行操作。
        $default_db = new DbBase();
        $product_model = new MallProduct();
        if (! empty($diff_result['cut_spec_val'])) { // 处理规格值减少的货品。将这部分货品删除。
            foreach ($diff_result['cut_spec_val'] as $cut_spec) {
                $spec_val = "{$cut_spec['spec_name']}:{$cut_spec['spec_val']}";
                $sql = 'UPDATE mall_product SET status = :status, modified_by = :modified_by, modified_time = :modified_time ' . 'WHERE goods_id = :goods_id AND spec_val LIKE :spec_val AND status = :status2';
            $params = [
                    ':status' => 2,':status2' => 1,':modified_by' => $user_id,
                    ':modified_time' => $_SERVER['REQUEST_TIME'],':goods_id' => $goods_id,
                    ':spec_val' => "%{$spec_val}%" 
            ];
            $default_db->rawExec($sql, $params);
        }
    }
    $already_insert_product = []; // 保存已经添加过的规格商品。
    $add_spec_products = []; // 保存倾听中新增规格的货品数据。
    if (! empty($diff_result['add_spec_val'])) { // 处理规格值增加的货品。将这部分添加到货品中。
                                                // 每增加一个属性值，会存在一个或多个规格货品。所以，要去循环读取处理。
        foreach ($diff_result['add_spec_val'] as $add_spec) {
            $add_spec_name = $add_spec['spec_name'];
            $add_spec_val = $add_spec['spec_val'];
            $spec = "{$add_spec_name}:{$add_spec_val}";
            // 查找新增规格值对应的记录。
            foreach ($products as $spec_val => $product) {
                if (stripos($spec_val, $spec) !== FALSE) {
                    $already_insert_product[] = $spec_val;
                    $add_spec_products[] = $product;
                }
            }
        }
        foreach ($add_spec_products as $product) {
            if (! Validator::is_integer($product['stock']) || ! Validator::is_number_between($product['stock'], 0, 10000)) {
                YCore::exception(- 1, '库存必须0~10000之间');
            }
            if (! Validator::is_number_between($product['sales_price'], 0.01, 1000000)) {
                YCore::exception(- 1, '销售价必须0.01~1000000之间');
            }
            if (! Validator::is_number_between($product['market_price'], 0.01, 1000000)) {
                YCore::exception(- 1, '市场价必须0.01~1000000之间');
            }
            $insert_data = [
                    'market_price' => $product['market_price'],'sales_price' => $product['sales_price'],
                    'spec_val' => $spec_val,'goods_id' => $goods_id,'stock' => $product['stock'],'status' => 1,
                    'created_by' => $user_id,'created_time' => $_SERVER['REQUEST_TIME'] 
            ];
            $ok = $product_model->insert($insert_data);
            if (! $ok) {
                YCore::exception(- 1, '规格保存失败');
            }
        }
    }
    // 其他值只做市场价、销售价、库存的更新。
    foreach ($products as $spec_val => $product) {
        if (in_array($spec_val, $already_insert_product)) { // 已经做了添加的时候则排除。
            continue;
        }
        if (! Validator::is_integer($product['stock']) || ! Validator::is_number_between($product['stock'], 0, 10000)) {
            YCore::exception(- 1, '库存必须0~10000之间');
        }
        if (! Validator::is_number_between($product['sales_price'], 0.01, 1000000)) {
            YCore::exception(- 1, '销售价必须0.01~1000000之间');
        }
        if (! Validator::is_number_between($product['market_price'], 0.01, 1000000)) {
            YCore::exception(- 1, '市场价必须0.01~1000000之间');
        }
        $where = [
                'goods_id' => $goods_id,'status' => 1,'spec_val' => $spec_val 
        ];
        $update_data = [
                'market_price' => $product['market_price'],'sales_price' => $product['sales_price'],
                'stock' => $product['stock'],'modified_by' => $user_id,'modified_time' => $_SERVER['REQUEST_TIME'] 
        ];
        $ok = $product_model->update($update_data, $where);
        if (! $ok) {
            YCore::exception(- 1, '规格保存失败');
        }
    }
    break;
default :
    YCore::exception(- 1, '新旧规格对比结果类型有误');
    break;
}
return true;
}

/**
 * 获取新旧商品规格不同。
 * $spec_val = [
 * '颜色' => ['银色', '黑色'],
 * '尺寸' => ['35', '38']
 * ];
 * -- 1、情况一：新规格与旧规格在规格名与规格值之间没有任何变化。
 * -- 2、情况二：新规格值在旧规格基础上只增加。如：颜色增加了紫色或尺寸增加了39或两者有之。
 * -- 3、情况三：新规格值在旧规格基础上只减少。如：颜色减少了黑色或尺寸减少了38或两者有之。
 * -- 4、情况四：新规格值在旧规格基础上有增有减。如颜色增加紫色、尺寸减少38。
 * -- 5、情况五：新规格名在旧规格基础上减少或增加。视为一种。即完全打乱了原有的规格。
 * 
 * @param array $old_spec
 * @param array $new_spec
 * @return array
 */
protected static function diffOldAndNewSpec($old_spec, $new_spec) {
$add_spec_val = []; // 保存增加的规格值。
$cut_spec_val = []; // 保存减少的规格值。
$diff_type = 0; // 0代表没有变化、1代表重新创建所有规格及对应的货品。2代表差异化处理即可。
                   // [1] 拆出新旧规格名进行差异判断。
$old_spec_name = array_keys($old_spec);
$new_spec_name = array_keys($new_spec);
$diff_spec_name = YCore::array_remove_equal($old_spec_name, $new_spec_name);

// [2] 判断变化。
if (empty($diff_spec_name)) { // 如果为空，代表规格名没有任何变化。
                              // 如果规格名没有变化，但是，顺序发生变化，也认为是规格名发生了变化。
foreach ($old_spec_name as $key => $val) {
    if ($new_spec_name[$key] != $val) {
        $diff_type = 1;
        break;
    }
}
// 如果规格名顺序没发生变化，则判断规格值是否发生了变化。
if ($diff_type == 0) {
    foreach ($old_spec as $spec_name => $spec) {
        $diff_spec_val = YCore::array_remove_equal($spec, $new_spec[$spec_name]);
        if (! empty($diff_spec_val)) {
            foreach ($diff_spec_val as $spec_val) {
                if (in_array($spec_val, $spec)) { // 如果在旧的规格名对应的规格值中找到，说明是减少。
                    $cut_spec_val[] = [
                            'spec_name' => $spec_name,'spec_val' => $spec_val 
                    ];
                } else { // 如果在新的规格名对应的规格值中找到，说明是增加。
                    $add_spec_val[] = [
                            'spec_name' => $spec_name,'spec_val' => $spec_val 
                    ];
                }
            }
            $diff_type = 2;
        }
    }
}
} else {
$diff_type = 1;
}
return [
    'add_spec_val' => $add_spec_val,'cut_spec_val' => $cut_spec_val,'diff_type' => $diff_type 
];
}

/**
 * 验证商品规格与货品规格是否匹配。
 * -- Example start --
 * $spec_val = [
 * '颜色' => ['银色', '黑色'],
 * '尺寸' => ['35', '38']
 * ];
 *
 * $products = [
 * '颜色:银色|尺寸:35' => ['market_price' => 129, 'sales_price' => 99, 'stock' => '999'],
 * '颜色:黑色|尺寸:35' => ['market_price' => 129, 'sales_price' => 99, 'stock' => '999'],
 * '颜色:银色|尺寸:38' => ['market_price' => 129, 'sales_price' => 99, 'stock' => '999'],
 * '颜色:黑色|尺寸:38' => ['market_price' => 129, 'sales_price' => 99, 'stock' => '999'],
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
    YCore::exception(- 1, '商品规格设置有误');
}
if (empty($key_val)) {
    YCore::exception(- 1, '商品规格设置有误');
}
foreach ($key_val as $key => $val) {
    $s_v = explode(':', $val);
    if (count($s_v) != 2) {
        YCore::exception(- 1, '商品规格设置有误');
    }
    if (! array_key_exists($s_v[0], $spec)) { // $s_v[0] 是规格名称。 $s_v[1] 是规格值。
        YCore::exception(- 1, '商品规格设置有误');
    }
    if (! in_array($s_v[1], $spec[$s_v[0]])) {
        YCore::exception(- 1, '商品规格设置有误');
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
    'goods_id' => $goods_id,'status' => 1 
];
$old_image = $image_model->fetchOne([], $where);
$_old_image = [];
foreach ($old_image as $item) {
$_old_image[$item['image_id']] = $item['image_url'];
}
$old_image = $_old_image;

// [2] 判断新入库的图片是否已经存在，已经存在则不做任何修改。
// 如果旧图片在新图片中不存在，则要进行删除。
$exists_old_image_id = [];
foreach ($album as $image_url) {
if (! empty($old_image) && in_array($image_url, $old_image)) { // 存在。
    $exists_old_image_id[] = array_search($image_url, $old_image);
} else { // 不存在。
    $insert_data = [
            'goods_id' => $goods_id,'image_url' => $image_url,'status' => 1,'created_time' => $_SERVER['REQUEST_TIME'],
            'created_by' => $user_id 
    ];
    $id = $image_model->insert($insert_data);
    if ($id == 0) {
        YCore::exception(- 1, '相册图片保存失败');
    }
}
}
// [3] 得到不在新图片中的旧图片ID。
$not_exist_image_id = [];
foreach ($old_image as $item) {
if (! in_array($item['image_id'], $exists_old_image_id)) {
    $not_exist_image_id[] = $item['image_id'];
}
}
// [4] 删除不在新图片中的旧图片ID对应的图片。
if (! empty($not_exist_image_id)) {
$default_db = new DbBase();
$result = $default_db->createWhereIn($not_exist_image_id);
$sql = "UPDATE mall_goods_image SET status = :status, modified_by = :modified_by, modified_time = :modified_time " . "WHERE image_id IN ({$result['question']})";
$params = $result['values'];
$params[':status'] = 2;
$params[':modified_by'] = $user_id;
$params[':modified_time'] = $_SERVER['REQUEST_TIME'];
$ok = $default_db->rawExec($sql, $params);
if (! $ok) {
YCore::exception(- 1, '相册图片保存失败');
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
':stock' => $stock,':product_id' => $product_id 
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
':stock' => $stock,':product_id' => $product_id 
];
return $default_db->rawExec($sql, $params);
}

/**
 * 获取自定义分类对应的商品数量。
 * 
 * @param number $cat_id 自定义分类ID。
 * @return number
 */
public static function getCustomGoodsCount($cat_id) {
$where = [
'custom_cat_id' => $cat_id,'status' => 1,'marketable' => 1 
];
$goods_model = new MallGoods();
return $goods_model->count($where);
}
}