<?php
/**
 * Office 之 Excel、Word、Power Point操作。
 * @author winerQin
 * @date 2016-04-12
 */

namespace common;

use PHPExcel\Spreadsheet;
class YOffice {
    
    /**
     * 26个大写字母。
     * 
     * @var array
     */
    private static $alpha = [
            'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z' 
    ];
    
    /**
     * 创建一个Excel。
     * 
     * @param array $header_title Excel第一行标题。
     * @param array $data Excel每行的数据。
     * @param array $save_path 保存路径。
     * @param string $filename 导出的文件名称。
     * @return void
     */
    public static function createExcel($header_title, $data, $save_path, $filename) {
        $objPHPExcel = new Spreadsheet();
        $objPHPExcel->getProperties()->setCreator("winerQin")->setLastModifiedBy("winerQin")->setTitle($filename)->setSubject($filename)->setDescription($filename)->setKeywords()->setCategory();
        $heaer_offset = 0; // 标题对应的字母。
        $alpha_repeat = 0; // 字母重复次数。0代表未重复。
        $y_position = 1; // Y轴数字。标题只能位于第一列。
        $ojbSheet = $objPHPExcel->setActiveSheetIndex(0);
        foreach ($header_title as $key => $title) {
            if ($heaer_offset == 26) {
                $heaer_offset = 0;
                $alpha_repeat += 1;
            }
            $second_alpha = self::$alpha[$heaer_offset]; // 每列组成如：A-Z,AA-ZZ。此值是第二位。
            $first_alpha = ($alpha_repeat > 0) ? self::$alpha[$alpha_repeat - 1] : ''; // 此值是第一位。
            $ojbSheet->setCellValue("{$first_alpha}{$second_alpha}{$y_position}", $title);
            $heaer_offset += 1;
        }
        $y_position = 2; // Y轴数字。数据从第二列开始。
        foreach ($data as $line) {
            $heaer_offset = 0; // 每行对应的字母。
            $alpha_repeat = 0; // 字母重复次数。0代表未重复。
            foreach ($line as $cell) {
                if ($heaer_offset == 26) {
                    $heaer_offset = 0;
                    $alpha_repeat += 1;
                }
                $second_alpha = self::$alpha[$heaer_offset]; // 每列组成如：A-Z,AA-ZZ。此值是第二位。
                $first_alpha = ($alpha_repeat > 0) ? self::$alpha[$alpha_repeat - 1] : ''; // 此值是第一位。
                $postion = $heaer_offset + 1;
                $ojbSheet->setCellValue("{$first_alpha}{$second_alpha}{$y_position}", $cell);
                $heaer_offset += 1;
            }
            $y_position += 1;
        }
        $objPHPExcel->setActiveSheetIndex(0);
        $objWriter = \PHPExcel\IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $save_name = rtrim($save_path, '/\\') . DIRECTORY_SEPARATOR . $filename . '.xlsx';
        $objWriter->save($save_name);
    }
    
    /**
     * Excel导出[直接向浏览器输出一个Excel文件]。
     * 
     * @param array $header_title Excel第一行标题。
     * @param array $data Excel每行的数据。
     * @param string $filename 导出的文件名称。
     * @return void
     */
    public static function excelExport($header_title, $data, $filename = '') {
        $objPHPExcel = new Spreadsheet();
        $objPHPExcel->getProperties()->setCreator("winerQin")->setLastModifiedBy("winerQin")->setTitle($filename)->setSubject($filename)->setDescription($filename)->setKeywords()->setCategory();
        $heaer_offset = 0; // 标题对应的字母。
        $alpha_repeat = 0; // 字母重复次数。0代表未重复。
        $y_position = 1; // Y轴数字。标题只能位于第一列。
        $ojbSheet = $objPHPExcel->setActiveSheetIndex(0);
        foreach ($header_title as $key => $title) {
            if ($heaer_offset == 26) {
                $heaer_offset = 0;
                $alpha_repeat += 1;
            }
            $second_alpha = self::$alpha[$heaer_offset]; // 每列组成如：A-Z,AA-ZZ。此值是第二位。
            $first_alpha = ($alpha_repeat > 0) ? self::$alpha[$alpha_repeat - 1] : ''; // 此值是第一位。
            $ojbSheet->setCellValue("{$first_alpha}{$second_alpha}{$y_position}", $title);
            $heaer_offset += 1;
        }
        $y_position = 2; // Y轴数字。数据从第二列开始。
        foreach ($data as $line) {
            $heaer_offset = 0; // 每行对应的字母。
            $alpha_repeat = 0; // 字母重复次数。0代表未重复。
            foreach ($line as $cell) {
                if ($heaer_offset == 26) {
                    $heaer_offset = 0;
                    $alpha_repeat += 1;
                }
                $second_alpha = self::$alpha[$heaer_offset]; // 每列组成如：A-Z,AA-ZZ。此值是第二位。
                $first_alpha = ($alpha_repeat > 0) ? self::$alpha[$alpha_repeat - 1] : ''; // 此值是第一位。
                $postion = $heaer_offset + 1;
                $ojbSheet->setCellValue("{$first_alpha}{$second_alpha}{$y_position}", $cell);
                $heaer_offset += 1;
            }
            $y_position += 1;
        }
        $objPHPExcel->setActiveSheetIndex(0);
        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0
        $objWriter = \PHPExcel\IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
    }
    
    /**
     * 导入Excel文件[获取Excel内容]。
     * 
     * @param string $filename 文件名称。
     * @return array
     */
    public static function excelImport($filename) {
    
    }
}