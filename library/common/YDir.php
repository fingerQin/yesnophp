<?php
/**
 * 目录或文件操作操作。
 * @author winerQin
 * @date 2016-05-24
 */

namespace common;

class YDir {
    
    /**
     * 转换目录下面的所有文件编码格式
     * 
     * @param string $in_charset 原字符集
     * @param string $out_charset 目标字符集
     * @param string $dir 目录地址
     * @param string $fileexts 转换的文件格式
     * @return string 如果原字符集和目标字符集相同则返回false，否则为true
     */
    public static function dir_iconv($in_charset, $out_charset, $dir, $fileexts = 'php|html|htm|shtml|shtm|js|txt|xml') {
        if ($in_charset == $out_charset) {
            return false;
        }
        $list = self::dir_list($dir);
        foreach ($list as $v) {
            if (pathinfo($v, PATHINFO_EXTENSION) == $fileexts && is_file($v)) {
                file_put_contents($v, iconv($in_charset, $out_charset, file_get_contents($v)));
            }
        }
        return true;
    }
    
    /**
     * 列出目录下所有文件
     * 
     * @param string $path 路径
     * @param string $exts 扩展名
     * @param array $list 增加的文件列表
     * @return array 所有满足条件的文件
     */
    public static function dir_list($path, $exts = '', $list = []) {
        $path = self::dir_path($path);
        $files = glob($path . '*');
        foreach ($files as $v) {
            if (! $exts || pathinfo($v, PATHINFO_EXTENSION) == $exts) {
                $list[] = $v;
                if (is_dir($v)) {
                    $list = self::dir_list($v, $exts, $list);
                }
            }
        }
        return $list;
    }
    
    /**
     * 删除目录及目录下面的所有文件
     * 
     * @param string $dir 路径。
     * @return bool 如果成功则返回 TRUE,失败则返回 FALSE。
     */
    public static function dir_delete($dir) {
        $dir = self::dir_path($dir);
        if (! is_dir($dir)) {
            return FALSE;
        }
        $list = glob($dir . '*');
        foreach ($list as $v) {
            is_dir($v) ? self::dir_delete($v) : @unlink($v);
        }
        return @rmdir($dir);
    }
    
    /**
     * 创建目录
     * 
     * @param string $path 路径。
     * @param string $mode 属性。
     * @return string 如果已经存在则返回true,否则为flase。
     */
    public static function dir_create($path, $mode = 0777) {
        if (is_dir($path))
            return TRUE;
        $ftp_enable = 0;
        $path = self::dir_path($path);
        $temp = explode('/', $path);
        $cur_dir = '';
        $max = count($temp) - 1;
        for($i = 0; $i < $max; $i ++) {
            $cur_dir .= $temp[$i] . '/';
            if (@is_dir($cur_dir))
                continue;
            @mkdir($cur_dir, 0777, true);
            @chmod($cur_dir, 0777);
        }
        return is_dir($path);
    }
    
    /**
     * 转化 \ 为 /
     * 
     * @param string $path 路径。
     * @return string 路径
     */
    public static function dir_path($path) {
        $path = str_replace('\\', '/', $path);
        if (substr($path, - 1) != '/')
            $path = $path . '/';
        return $path;
    }
    
    /**
     * 拷贝目录及下面所有文件
     * 
     * @param string $fromdir 原路径。
     * @param string $todir 目标路径。
     * @return string 如果目标路径不存在则返回false,否则为true。
     */
    public static function dir_copy($fromdir, $todir) {
        $fromdir = self::dir_path($fromdir);
        $todir = self::dir_path($todir);
        if (! is_dir($fromdir)) {
            return false;
        }
        if (! is_dir($todir)) {
            self::dir_create($todir);
        }
        $list = glob($fromdir . '*');
        if (! empty($list)) {
            foreach ($list as $v) {
                $path = $todir . basename($v);
                if (is_dir($v)) {
                    self::dir_copy($v, $path);
                } else {
                    copy($v, $path);
                    @chmod($path, 0777);
                }
            }
        }
        return true;
    }
    
    /**
     * 设置目录下面的所有文件的访问和修改时间
     * 
     * @param string $path 路径。
     * @param int $mtime 修改时间。
     * @param int $atime 访问时间。
     * @return array 不是目录时返回false，否则返回 true。
     */
    public static function dir_touch($path, $mtime = 0, $atime = 0) {
        $mtime = $mtime ? $mtime : $_SERVER['REQUEST_TIME'];
        $atime = $atime ? $atime : $_SERVER['REQUEST_TIME'];
        if (! is_dir($path)) {
            return false;
        }
        $path = self::dir_path($path);
        if (! is_dir($path)) {
            touch($path, $mtime, $atime);
        }
        $files = glob($path . '*');
        foreach ($files as $v) {
            is_dir($v) ? self::dir_touch($v, $mtime, $atime) : touch($v, $mtime, $atime);
        }
        return true;
    }
    
    /**
     * 目录列表
     * 
     * @param string $dir 路径。
     * @param int $parentid
     * @param array $dirs
     * @return array 返回目录列表。
     */
    public static function dir_tree($dir, $parentid = 0, $dirs = []) {
        global $id;
        if ($parentid == 0) {
            $id = 0;
        }
        $list = glob($dir . '*');
        foreach ($list as $v) {
            if (is_dir($v)) {
                $id ++;
                $dirs[$id] = [
                        'id' => $id,'parentid' => $parentid,'name' => basename($v),'dir' => $v . '/' 
                ];
                $dirs = self::dir_tree($v . '/', $id, $dirs);
            }
        }
        return $dirs;
    }
}