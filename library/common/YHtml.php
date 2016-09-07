<?php
/**
 * HTML表单快捷生成工具。
 * @author winerQin
 * @date 2016-04-04
 */

namespace common;

class YHtml {
    
    /**
     * 生成表单input标签。
     * 
     * @param string $name input的name的名称。
     * @param string $value 值。
     * @param string $class_name class名称。
     * @param string $id_name id名称
     * @param string $is_output 是否输出。
     * @return string
     */
    public static function input($name, $value = '', $class_name = '', $id_name = '', $is_output = true) {
        $class_name = (strlen($class_name) > 0) ? " class=\"{$class_name}\"" : '';
        $id_name = (strlen($id_name) > 0) ? " id=\"{$id_name}\"" : '';
        $input_text = "<input name=\"{$name}\"{$class_name}{$id_name} value=\"{$value}\" />";
        if ($is_output) {
            echo $input_text;
        } else {
            return $input_text;
        }
    }
    
    /**
     * 生成表单textarea标签。
     * 
     * @param string $name textarea的name名称。
     * @param string $content 值。
     * @param string $class_name css class 名称。
     * @param string $id_name css id 名称。
     * @return void
     */
    public static function textarea($name, $content = '', $class_name = '', $id_name = '', $is_output = true) {
        $class_name = (strlen($class_name) > 0) ? " class=\"{$class_name}\"" : '';
        $id_name = (strlen($id_name) > 0) ? " id=\"{$id_name}\"" : '';
        $select_open = "<textarea name=\"{$name}\"{$class_name}{$id_name}>";
        $select_close = '</textarea>';
        $str = "{$select_open}{$content}{$select_close}";
        if ($is_output) {
            echo $str;
        } else {
            return $str;
        }
    }
    
    /**
     * 生成表单的select标签。
     * 
     * @param string $name select的name名称。
     * @param array $data 下拉数据。
     * @param unknown $selected_value 被选中的值。
     * @param string $class_name css class 名称。
     * @param string $id_name css id 名称。
     * @param boolean $is_output 是否输出。true:是、false: 否。
     * @return void
     */
    public static function select($name, array $data, $selected_value = null, $class_name = '', $id_name = '', $is_output = true) {
        if (empty($data)) {
            YCore::exception(- 1, '下拉数据不能为空');
        }
        $class_name = (strlen($class_name) > 0) ? " class=\"{$class_name}\"" : '';
        $id_name = (strlen($id_name) > 0) ? " id=\"{$id_name}\"" : '';
        $select_open = "<select name=\"{$name}\"{$class_name}{$id_name}>";
        $select_close = '</select>';
        $select_option = '';
        foreach ($data as $key => $item) {
            $key = htmlspecialchars($key);
            $item = htmlspecialchars($item);
            if (strlen($selected_value) > 0) {
                if ($selected_value == $key) {
                    $select_option .= "<option selected=\"selected\" value=\"{$key}\">{$item}</option>";
                } else {
                    $select_option .= "<option value=\"{$key}\">{$item}</option>";
                }
            } else {
                $select_option .= "<option value=\"{$key}\">{$item}</option>";
            }
        }
        $str = "{$select_open}{$select_option}{$select_close}";
        if ($is_output) {
            echo $str;
        } else {
            return $str;
        }
    }
}