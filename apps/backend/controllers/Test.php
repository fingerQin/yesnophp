<?php
use services\GoodsService;
use services\OrderService;
use winer\RedisMutexLock;
use common\YCore;
use services\GameService;
/**
 * 测试。
 * 
 * @author winerQin
 *         @date 2016-03-10
 */

class TestController extends \common\controllers\Admin {
    
    public function mdAction() {
    
    }
    
    public function indexAction() {
        $lock_key = 'user_key';
        $redis_lock = RedisMutexLock::lock($lock_key, 3);
        if ($redis_lock) {
            echo uniqid('T:');
            echo "<br />";
        } else {
            echo 'failed';
        }
        RedisMutexLock::release($lock_key);
        $this->end();
    }
    
    /**
     * 用户投注测试。
     */
    public function userBetAction() {
        $bet_number_or_money = [
                [
                        'bet_number' => '01,02,08,09,22,23:12','bet_ledou' => 10 
                ] 
        ];
        GameService::userBet($this->admin_id, 1, 10, $bet_number_or_money);
        echo 'ok';
        $this->end();
    }
    
    public function waitAction() {
        $cache = YCore::getCache();
        $ok = $cache->set('xxx', uniqid());
        echo $ok ? 'successed' : 'failed';
        $this->end();
    }
    
    public function goodsDetailAction() {
        $detail = GoodsService::getGoodsDetail(1);
        print_r($detail);
        $this->end();
    }
    
    public function goodsListAction() {
        $list = GoodsService::getGoodsList('', - 1, '', '', 1, 20);
        print_r($list);
        $this->end();
    }
    
    public function phpexcelAction() {
        error_reporting(E_ALL);
        ini_set('display_errors', TRUE);
        ini_set('display_startup_errors', TRUE);
        date_default_timezone_set('PRC');
        
        define('EOL', (PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
        
        // Create new PHPExcel object
        echo date('H:i:s'), " Create new PHPExcel object", EOL;
        $objPHPExcel = new \PHPExcel\Spreadsheet();
        
        // Set document properties
        echo date('H:i:s'), " Set document properties", EOL;
        $objPHPExcel->getProperties()->setCreator("Maarten Balliauw")->setLastModifiedBy("Maarten Balliauw")->setTitle("PHPExcel Test Document")->setSubject("PHPExcel Test Document")->setDescription("Test document for PHPExcel, generated using PHP classes.")->setKeywords("office PHPExcel php")->setCategory("Test result file");
        
        // Add some data
        echo date('H:i:s'), " Add some data", EOL;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', 'Hello')->setCellValue('B2', 'world!')->setCellValue('C1', 'Hello')->setCellValue('D2', 'world!');
        
        // Miscellaneous glyphs, UTF-8
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A4', 'Miscellaneous glyphs')->setCellValue('A5', '我是帅气的覃礼钧');
        
        $objPHPExcel->getActiveSheet()->setCellValue('A8', "Hello\nWorld");
        $objPHPExcel->getActiveSheet()->getRowDimension(8)->setRowHeight(- 1);
        $objPHPExcel->getActiveSheet()->getStyle('A8')->getAlignment()->setWrapText(true);
        
        $value = "-ValueA\n-Value B\n-Value C";
        $objPHPExcel->getActiveSheet()->setCellValue('A10', $value);
        $objPHPExcel->getActiveSheet()->getRowDimension(10)->setRowHeight(- 1);
        $objPHPExcel->getActiveSheet()->getStyle('A10')->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->getStyle('A10')->setQuotePrefix(true);
        
        // Rename worksheet
        echo date('H:i:s'), " Rename worksheet", EOL;
        $objPHPExcel->getActiveSheet()->setTitle('Simple');
        
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);
        
        // Save Excel 2007 file
        echo date('H:i:s'), " Write to Excel2007 format", EOL;
        $callStartTime = microtime(true);
        
        $objWriter = \PHPExcel\IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save(str_replace('.php', '.xlsx', __FILE__));
        $callEndTime = microtime(true);
        $callTime = $callEndTime - $callStartTime;
        
        echo date('H:i:s'), " File written to ", str_replace('.php', '.xlsx', pathinfo(__FILE__, PATHINFO_BASENAME)), EOL;
        echo 'Call time to write Workbook was ', sprintf('%.4f', $callTime), " seconds", EOL;
        // Echo memory usage
        echo date('H:i:s'), ' Current memory usage: ', (memory_get_usage(true) / 1024 / 1024), " MB", EOL;
        
        // Save Excel 95 file
        echo date('H:i:s'), " Write to Excel5 format", EOL;
        $callStartTime = microtime(true);
        
        $objWriter = \PHPExcel\IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save(str_replace('.php', '.xls', __FILE__));
        $callEndTime = microtime(true);
        $callTime = $callEndTime - $callStartTime;
        
        echo date('H:i:s'), " File written to ", str_replace('.php', '.xls', pathinfo(__FILE__, PATHINFO_BASENAME)), EOL;
        echo 'Call time to write Workbook was ', sprintf('%.4f', $callTime), " seconds", EOL;
        // Echo memory usage
        echo date('H:i:s'), ' Current memory usage: ', (memory_get_usage(true) / 1024 / 1024), " MB", EOL;
        
        // Echo memory peak usage
        echo date('H:i:s'), " Peak memory usage: ", (memory_get_peak_usage(true) / 1024 / 1024), " MB", EOL;
        
        // Echo done
        echo date('H:i:s'), " Done writing files", EOL;
        echo 'Files have been created in ', getcwd(), EOL;
        $this->end();
    }
    
    /**
     * 用户订单列表。
     */
    public function userOrderListAction() {
        $list = OrderService::getUserOrderList(1, '', - 1, '', '', 1, 20);
        print_r($list);
        $this->end();
    }
    
    public function deleteOrderAction() {
        OrderService::deleteOrder(1, 1);
        $this->end();
    }
    
    /**
     * 提交订单测试 。
     */
    public function submitOrderAction() {
        $goods_list = [
                [
                        'goods_id' => 1,'product_id' => 1,'quantity' => 1 
                ],
                [
                        'goods_id' => 1,'product_id' => 2,'quantity' => 1 
                ] 
        ];
        $new_address_info = [
                'realname' => '覃礼钧','district_code' => '441302007','zipcode' => '516006',
                'mobilephone' => '18575202691','receiver_address' => '仲恺大道666号科融创业大厦15楼1513' 
        ];
        $data = [
                'user_id' => 1,'goods_list' => $goods_list,'address_id' => - 1,'need_invoice' => 0,
                'invoice_type' => 1,'invoice_name' => '','buyer_message' => '买家留言。100这个字符。',
                'new_address_info' => $new_address_info 
        ];
        OrderService::submitOrder($data);
        $this->end();
    }
    
    /**
     * 编辑商品功能测试。
     */
    public function editGoodsAction() {
        $spec_val = [
                '颜色' => [
                        '银色','黑色' 
                ],'尺寸' => [
                        '35','38' 
                ] 
        ];
        $products = [
                '颜色:银色|尺寸:35' => [
                        'market_price' => 129,'sales_price' => 96,'stock' => '996' 
                ],'颜色:黑色|尺寸:35' => [
                        'market_price' => 129,'sales_price' => 97,'stock' => '997' 
                ],'颜色:银色|尺寸:38' => [
                        'market_price' => 129,'sales_price' => 98,'stock' => '998' 
                ],'颜色:黑色|尺寸:38' => [
                        'market_price' => 129,'sales_price' => 99,'stock' => '999' 
                ] 
        ];
        // 最多五张图片。第一张图片会更新到商品主图。
        $goods_album = [
                'images/voucher/20160401/56fe70362ef7e.jpg','images/voucher/20160401/56fe705fd37a2.jpg',
                'images/voucher/20160401/56fe710513c9e.jpg','images/voucher/20160402/56fea2043dc01.jpg',
                'images/voucher/20160402/56fea3f18677d.jpg' 
        ];
        $data = [
                'goods_id' => 1,'user_id' => $this->admin_id,
                'goods_name' => '亚瑟士ASICS跑步鞋缓冲网面运动鞋女鞋透气跑鞋MAVERICKT25XQ-9090','cat_id' => 9,
                'slogan' => '全店支持货到付款，折后满600再减50，满1200再减100','weight' => 500,'listorder' => 0,'description' => '商品详情。',
                'spec_val' => $spec_val,'products' => $products,'goods_album' => $goods_album,'market_price' => '0',
                'sales_price' => '0','stock' => '0' 
        ];
        GoodsService::editGoods($data);
        $this->end();
    }
    
    /**
     * 添加商品功能测试。
     */
    public function addGoodsAction() {
        $spec_val = [
                '颜色' => [
                        '银色','黑色' 
                ],'尺寸' => [
                        '35','38' 
                ] 
        ];
        $products = [
                '颜色:银色|尺寸:35' => [
                        'market_price' => 129,'sales_price' => 99,'stock' => '999' 
                ],'颜色:黑色|尺寸:35' => [
                        'market_price' => 129,'sales_price' => 99,'stock' => '999' 
                ],'颜色:银色|尺寸:38' => [
                        'market_price' => 129,'sales_price' => 99,'stock' => '999' 
                ],'颜色:黑色|尺寸:38' => [
                        'market_price' => 129,'sales_price' => 99,'stock' => '999' 
                ] 
        ];
        // 最多五张图片。第一张图片会更新到商品主图。
        $goods_album = [
                'images/voucher/20160401/56fe70362ef7e.jpg','images/voucher/20160401/56fe705fd37a2.jpg',
                'images/voucher/20160401/56fe710513c9e.jpg','images/voucher/20160402/56fea2043dc01.jpg',
                'images/voucher/20160402/56fea3f18677d.jpg' 
        ];
        $data = [
                'user_id' => $this->admin_id,'goods_name' => '亚瑟士ASICS跑步鞋缓冲网面运动鞋女鞋透气跑鞋MAVERICKT25XQ-9090',
                'cat_id' => 10,'slogan' => '全店支持货到付款，折后满600再减50，满1200再减100','weight' => 500,'listorder' => 0,
                'description' => '商品详情。','spec_val' => $spec_val,'products' => $products,'goods_album' => $goods_album,
                'market_price' => '0','sales_price' => '0','stock' => '0' 
        ];
        GoodsService::addGoods($data);
        $this->end();
    }
}