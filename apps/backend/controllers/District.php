<?php
/**
 * 省市区管理。
 * @author winerQin
 * @date 2016-01-14
 */

use services\DistrictService;
use common\YCore;
use services\ConfigService;

class DistrictController extends \common\controllers\Admin {
    
    /**
     * 省市区列表。
     */
    public function indexAction() {
    }
    
    /**
     * 添加省市区。
     */
    public function addAction() {
        $this->end();
    }
    
    /**
     * 编辑省市区。
     */
    public function editAction() {
        $this->end();
    }
    
    /**
     * 删除省市区。
     */
    public function deleteAction() {
        $this->end();
    }
    
    /**
     * 省市区排序。
     */
    public function sortAction() {
        $this->end();
    }
    
    /**
     * 创建省市区JSON文件。
     */
    public function createJsonFileAction() {
        $list = DistrictService::treeToDistrict();
        $json_path = APP_PATH . DIRECTORY_SEPARATOR . 'statics/js/district.js';
        file_put_contents($json_path, 'district_all(' . json_encode($list) . ')');
        YCore::setconfig('district_json_version', date('YmdHis', $_SERVER['REQUEST_TIME']));
        ConfigService::clearConfigCache();
        $this->json(true, '生成JSON文件成功');
    }
}