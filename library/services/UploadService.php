<?php
/**
 * 上传管理。
 * --1、上传管理模块错误码段位：6002xxx
 * @author winerQin
 * @date 2015-11-13
 */
namespace services;

use common\YCore;
use winer\Validator;
use models\Files;
use common\YDir;

class UploadService extends BaseService {

    const FILE_TYPE_IMAGE = 1; // 图片。
    const FILE_TYPE_OTHER = 2; // 其它格式文件。

    /**
     * 图片上传。
     *
     * @param string $file_content 文件内容。通过file_get_contents得到的内容。
     * @param int $user_type 用户类型。1管理员、2普通用户。决定user_id的值。
     * @param number $user_id 用户ID。如果是私有图片必须设置此值。如果是公开图片此值有就必传没有就不传。
     * @param string $dirname 目录名称。当保存图片的时候，会把相应的图片保存在此目录下。
     * @return array
     */
    public static function saveImage($file_content, $user_type = 2, $user_id = 0, $dirname = '') {
        if (!Validator::is_alpha_dash($dirname)) {
            YCore::exception(7000001, 'The dirname parameter must be wrong');
        }
        $root_dir  = YCore::appconfig('upload.root_dir');
        $root_dir  = realpath($root_dir) . DIRECTORY_SEPARATOR; // 去除结尾处的目录分隔钱并重新拼接上当前运行系统的目录分隔线。
        $ymd_data  = date('Ymd', $_SERVER['REQUEST_TIME']);
        $root_path = $root_dir . 'images/' . $dirname . '/' . $ymd_data;
        YDir::dir_create($root_path);
        $statics_domain_name = YCore::config('files_domain_name');
        $filename = uniqid() . '.jpg';
        file_put_contents("{$root_path}/{$filename}", $file_content);
        $files_model = new Files();
        $file_name   = 'images' . '/' . $dirname . '/' . $ymd_data . '/' . $filename;
        $file_size   = strlen($file_content);
        $file_md5    = md5($file_content);
        $file_id     = $files_model->addFiles($file_name, self::FILE_TYPE_IMAGE, $file_size, $file_md5, $user_type, $user_id);
        if ($file_id == 0) {
            YCore::exception(7000003, '文件上传失败');
        }
        $fileinfo   = [];
        $fileinfo[] = [
            'file_id'            => $file_id,
            'image_url'          => $statics_domain_name . $file_name,
            'relative_image_url' => $file_name
        ];
        return $fileinfo;
    }

    /**
     * 上传其它类型文件。
     * -- 1、其它文件包含：zip、rar、word、ppt、excel、
     *
     * @param int $user_type 用户类型。1管理员、2普通用户。决定user_id的值。
     * @param number $user_id 用户ID。如果是私有图片必须设置此值。如果是公开图片此值有就必传没有就不传。
     * @param string $dirname 目录名称。当保存图片的时候，会把相应的图片保存在此目录下。
     * @param number $file_size 文件最大限制。单位（M）
     * @return array
     */
    public static function uploadOtherFile($user_type = 2, $user_id = 0, $dirname = '', $file_size = 0) {
        if ($file_size <= 0) {
            YCore::exception(6002001, 'The file_size parameter must be greater than zero');
        }
        if (!Validator::is_alpha_dash($dirname)) {
            YCore::exception(6002002, 'The dirname parameter must be wrong');
        }
        $allow_exts = [
            'zip',
            'rar',
            'doc',
            'docx',
            'xls',
            'xlsx',
            'pptx',
            'ppt',
            'gz',
            'bz2',
            'mp3',
            'ogg',
            'mp4',
            'avi',
            'flv',
            'mpeg',
            'wmv',
            'mkv',
            '3gp',
            'mpg',
            'mpeg',
            'rm',
            'rmvb',
            'vob',
            'mov',
            'amr',
            'wav',
            'txt',
            'pdf',
            'dmg'
        ];
        $max_size  = $file_size * 1024 * 1024;
        $root_dir  = YCore::appconfig('upload.root_dir');
        $root_dir  = realpath($root_dir) . DIRECTORY_SEPARATOR; // 去除结尾处的目录分隔钱并重新拼接上当前运行系统的目录分隔线。
        $root_path = $root_dir . 'files/';
        $upload = new \winer\Upload(); // 实例化上传类
        $upload->maxSize  = $max_size; // 设置附件上传大小
        $upload->exts     = $allow_exts; // 设置附件上传类型
        $upload->rootPath = $root_path; // 设置附件上传根目录
        $upload->savePath = $dirname . '/'; // 设置附件上传（子）目录
        $info = $upload->upload();
        $fileinfo = [];
        $statics_domain_name = YCore::config('files_domain_name');
        foreach ($info as $item) {
            $files_model = new Files();
            $file_name = 'files' . '/' . $item['savepath'] . $item['savename'];
            $file_id = $files_model->addFiles($file_name, self::FILE_TYPE_IMAGE, $item['size'], $item['md5'], $user_type, $user_id);
            if ($file_id == 0) {
                YCore::exception(6002003, '文件上传失败');
            }
            $fileinfo[] = [
                'file_id'            => $file_id,
                'image_url'          => $statics_domain_name . $file_name,
                'relative_image_url' => $file_name
            ];
        }
        return $fileinfo;
    }

    /**
     * 图片上传。
     *
     * @param int $user_type 用户类型。1管理员、2普通用户。决定user_id的值。
     * @param number $user_id 用户ID。如果是私有图片必须设置此值。如果是公开图片此值有就必传没有就不传。
     * @param string $dirname 目录名称。当保存图片的时候，会把相应的图片保存在此目录下。
     * @param number $file_size 文件最大限制。单位（M）
     * @param string $is_limit_size 是否限制文件尺寸大小。
     * @param number $image_width 如果限制文件尺寸大小。则最大宽度多少。单位（px）
     * @param number $image_height 如果限制文件尺寸大小。则最大高度多少。单位（px）
     * @return array
     */
    public static function uploadImage($user_type = 2, $user_id = 0, $dirname = '', $file_size = 0, $is_limit_size = false, $image_width = 0, $image_height = 0) {
        if ($file_size <= 0) {
            YCore::exception(6002001, 'The file_size parameter must be greater than zero');
        }
        if (!Validator::is_alpha_dash($dirname)) {
            YCore::exception(6002002, 'The dirname parameter must be wrong');
        }
        $max_size = $file_size * 1024 * 1024;
        $root_dir = YCore::appconfig('upload.root_dir');
        $root_dir = realpath($root_dir) . DIRECTORY_SEPARATOR; // 去除结尾处的目录分隔钱并重新拼接上当前运行系统的目录分隔线。
        $root_path = $root_dir . 'images/';
        $upload = new \winer\Upload(); // 实例化上传类
        $upload->maxSize = $max_size; // 设置附件上传大小
        // 设置附件上传类型
        $upload->exts = [
            'jpg',
            'gif',
            'png',
            'jpeg'
        ];
        $upload->rootPath = $root_path; // 设置附件上传根目录
        $upload->savePath = $dirname . '/'; // 设置附件上传（子）目录
        $info = $upload->upload();
        $fileinfo = [];
        $statics_domain_name = YCore::config('files_domain_name');
        foreach ($info as $item) {
            $files_model = new Files();
            $file_name = 'images' . '/' . $item['savepath'] . $item['savename'];
            $file_id = $files_model->addFiles($file_name, self::FILE_TYPE_IMAGE, $item['size'], $item['md5'], $user_type, $user_id);
            if ($file_id == 0) {
                YCore::exception(6002003, '文件上传失败');
            }
            $fileinfo[] = [
                'file_id'            => $file_id,
                'image_url'          => $statics_domain_name . $file_name,
                'relative_image_url' => $file_name
            ];
        }
        return $fileinfo;
    }
}