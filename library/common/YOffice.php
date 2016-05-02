<?php
/**
 * Office 之 Excel、Word、Power Point操作。
 * @author winerQin
 * @date 2016-04-12
 */

namespace common;

class YOffice {

    /**
     * Excel导出[直接向浏览器输出一个Excel文件]。
     * @param array $header_title Excel第一行标题。
     * @param array $data Excel每行的数据。
     * @param string $filename 导出的文件名称。
     * @return void
     */
    public static function excelExport($header_title, $data, $filename = '') {
        
    }

    /**
     * 导入Excel文件[获取Excel内容]。
     * @param string $filename 文件名称。
     * @return array
     */
    public static function excelImport($filename) {
        
    }
}