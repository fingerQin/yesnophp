<?php
/**
 * 测试功能用的controller。
 * @author qinlijun
 * @date 2014-11-12
 */

use common\YCore;
use services\UserService;
use services\UploadService;
use services\CaptchaService;
use models\Files;
use common\YOffice;
use services\SmsService;

class TestController extends \common\controllers\Common {

	/**
	 * 默认首页。
	 */
	public function indexAction() {
		$title = [
		    '姓名', '姓名', '姓名', '姓名', '姓名', '姓名', '姓名', '姓名', '姓名', '姓名', '姓名', '姓名', '姓名', '姓名', '姓名', '姓名',
		    '姓名', '姓名', '姓名', '姓名', '姓名', '姓名', '姓名', '姓名', '姓名', '姓名', '姓名', '姓名', '姓名', '姓名', '姓名', '姓名',
		    '年龄', '年龄', '年龄', '年龄', '年龄', '年龄', '年龄', '年龄', '年龄', '年龄', '年龄', '年龄', '年龄', '年龄', '年龄', '年龄',
		    '年龄', '年龄', '年龄', '年龄', '年龄', '年龄', '年龄', '年龄', '年龄', '年龄', '年龄', '年龄', '年龄', '年龄', '年龄', '年龄',
		];
		$data[] = [ '覃礼钧', '28', '覃礼钧', '28', '覃礼钧', '28', '覃礼钧', '28' , '覃礼钧', '28', '覃礼钧', '28', '覃礼钧', '28', '覃礼钧', '28', '覃礼钧', '28', '覃礼钧', '28', '覃礼钧', '28', '覃礼钧', '28', '覃礼钧', '28', '覃礼钧', '28', '覃礼钧', '28', '覃礼钧', '28', '覃礼钧', '28', '覃礼钧', '28', '覃礼钧', '28', '覃礼钧', '28', '覃礼钧', '28', '覃礼钧', '28', '覃礼钧', '28', '覃礼钧', '28'];
		$data[] = [ '覃礼钧', '28', '覃礼钧', '28', '覃礼钧', '28', '覃礼钧', '28' , '覃礼钧', '28', '覃礼钧', '28', '覃礼钧', '28', '覃礼钧', '28', '覃礼钧', '28', '覃礼钧', '28', '覃礼钧', '28', '覃礼钧', '28', '覃礼钧', '28', '覃礼钧', '28', '覃礼钧', '28', '覃礼钧', '28', '覃礼钧', '28', '覃礼钧', '28', '覃礼钧', '28', '覃礼钧', '28', '覃礼钧', '28', '覃礼钧', '28', '覃礼钧', '28', '覃礼钧', '28'];
		$save_path = APP_PATH . DIRECTORY_SEPARATOR . '/upload/files';
		YOffice::excelExport($title, $data, 'age');
		$this->end();
	}
	
	public function smsAction() {
	    SmsService::sendSmsCode('register', '18575202691');
	    $this->end();
	}

	public function detailAction() {
	    $code = $this->getString('code');
	    var_dump($code);
	    $this->end();
	}

	public function codeAction() {
		CaptchaService::getCode(1, 1);
		$this->end();
	}

	public function checkcodeAction() {
		$code = $this->_request->getQuery('code');
		$bool = CaptchaService::checkCode(1, 1, $code);
		var_dump($bool);
		$this->end();
	}

	/**
	 * 用户登录。
	 */
	public function userLoginAction() {
		$username = 'winerQin';
		$password = '123456';
		$result = UserService::login($username, $password, 2);
		var_dump($result);
		$this->end();
	}

	/**
	 * 用户注册。
	 */
	public function userRegisterAction() {
		$username = 'winerQin';
		$password = '123456';
		$result = UserService::register($username, $password);
		var_dump($result);
		$this->end();
	}

	public function testAction() {
		$files_model = new Files();
		$file_ids = [1,2,3,4,5];
		$result = $files_model->getFile($file_ids);
		print_r($result);
		$this->end();
	}

	public function logAction() {
		$model = new \models\Log();
		$log_type      = 1;
		$log_user_id   = 1;
		$log_user_name = 'winerQin';
		$log_time      = $_SERVER['REQUEST_TIME'];
		$content       = '测试';
		$ok = $model->addLog($log_type, $log_user_id, $log_user_name, $log_time, $content);
		var_dump($ok);
		exit;
	}

	/**
	 * 数据验证。
	 * 1、验证不通过会抛出异常。
	 * 2、验证通过会返回true。
	 * 3、如果被验证的数据没有对应的验证规则。将不会做验证。
	 */
	public function valiAction() {
		$data = [
			'username'    => 'yesnophp',
			'password'    => '123456',
			'mobilephone' => '18575202691',
			'code'        => '1234',
			'invite_code' => '',
			'date'        => '2015-11-03 09',
		];
		$rules = [
			'username'    => '账号|require:5000001|alpha_dash:5000002|len:5000003:6:20:0',
			'password'    => '密码|require:5000004|alpha_dash:5000005|len:5000006:6:20:0',
			'mobilephone' => '手机号码|require:5000004|mobilephone:5000004',
			'code'        => '短信验证码|require:5000004|len:5000004:4:6:0',
			'invite_code' => '邀请码|alpha_number:5000004',
			'date'        => '开始时间|date:5000004:"Y-m-d H"',
		];
		$result = \winer\Validator::valido($data, $rules);
		var_dump($result);
		exit;
	}

	/**
	 * mongo测试。
	 */
	public function mongoAction() {
		// 选择数据库。
		$m_db = $this->_mongodb->selectDB('qinlijun');
		// 选择users集合。
		$collection = $m_db->users;
		// 计算集合数量。
		$count = $collection->count();
		// var_dump($count);
		// 读取一条记录。
		$row = $collection->findOne();
		// var_dump($row);
		$row = $collection->findOne(array('num' => 1000));
		print_r($row);
		exit;
	}
	
	
	/**
	 * session操作。
	 */
	public function sessionAction() {
		$this->_session->set('test', '123');
		var_dump($this->_session->get('test'));
		exit;
	}

	/**
	 * 当前环境相关信息。
	 */
	public function infoAction() {
		echo 'ModuleName:' . $this->_request->getModuleName() . '<br />';
		echo 'ControllerName:' . $this->_request->getControllerName() . '<br />';
		echo 'ActionName:' . $this->_request->getActionName() . '<br />';
		exit;
	}

	/**
	 * 分页。
	 * -- 生成url的函数U()未实现。
	 */
	public function pageAction() {
		$Page = new \winer\Paginator(100, 25);
		$show = $Page->show();// 分页显示输出
		echo $show;
		exit;
	}

	/**
	 * 上传。
	 */
	public function uploadAction() {
		if ($this->_request->isPost()) {
			$fileinfo = UploadService::uploadOtherFile(2, 1, 'shop', 2);
			YCore::print_r($fileinfo);
			$this->end();
			/*
			$upload = new \winer\Upload();							// 实例化上传类
			$upload->maxSize   = 3145728 ;							// 设置附件上传大小
			$upload->exts      = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
			$upload->rootPath  = APP_PATH . '/upload/tmp/'; 		// 设置附件上传根目录
			$upload->savePath  = 'shop/'; 								// 设置附件上传（子）目录
			// 上传文件
			$info = $upload->upload();
			if (!$info) {// 上传错误提示错误信息
				$this->error($upload->getError());
			} else { // 上传成功
				\common\YCore::print_r($info);
				$this->success('上传成功！');
			}
			*/
		}
	}

	/**
	 * 数据验证。
	 */
	public function checkAction() {
		var_dump(\winer\Validator::is_mobilephone('18665027895'));
		exit;
	}

	/**
	 * 发送邮件。
	 */
	public function sendMailAction() {
		$host     = $this->_config->mail->host;
		$port     = $this->_config->mail->port;
		$username = $this->_config->mail->username;
		$password = $this->_config->mail->password;
		
		$mail = new winer\mail\PHPMailer(); 		// new一个PHPMailer对象出来
		$mail->CharSet = "UTF-8";					// 设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
		$mail->IsSMTP(); 							// 设定使用SMTP服务
		$mail->SMTPDebug  = 1;                    	// 启用SMTP调试功能
		// 1 = errors and messages
		// 2 = messages only
		$mail->SMTPAuth   = true;                 	// 启用 SMTP 验证功能
		// $mail->SMTPSecure = "ssl";               // 安全协议，可以注释掉
		$mail->Host       = $host;        		   	// SMTP 服务器
		$mail->Port       = $port;                 	// SMTP服务器的端口号
		$mail->Username   = $username;      		// SMTP服务器用户名，PS：我乱打的
		$mail->Password   = $password;            	// SMTP服务器密码
		$mail->From       = $username;				// 发件人邮箱 
		$mail->FromName   = 'winer_master';
		$mail->Subject    = '您好！我是你的好朋友';
		$mail->Body       = '<strong>强壮，威武</strong>';
		$mail->IsHTML(true);  // send as HTML   
		$mail->AddAddress('753814253@qq.com', 'winer');
		$attachment_path = APP_PATH . '/data/fruit.jpg';	 // 附件。
		$mail->AddAttachment($attachment_path);      		 // attachment
		//$mail->AddAttachment("images/phpmailer_mini.gif"); // attachment
		if(!$mail->Send()) {
			echo 'Mailer Error: ' . $mail->ErrorInfo;
		} else {
			echo "Message sent!恭喜，邮件发送成功！";
		}
		exit;
	}
	
	/**
	 * 图像处理。
	 * -- 目前有一个BUG：当多个操作一起执行的时候，会影响接下来的图片。
	 * -- 可以将下面的注释的代码全数放开可重现。
	 */
	public function imageAction() {
		$image_path = APP_PATH . '/data/fruit.jpg';
		$image = new \winer\Image();
		// 获取图片相关信息。
		$image->open($image_path);
		$width  = $image->width(); 		// 返回图片的宽度
		$height = $image->height(); 	// 返回图片的宽度
		$type   = $image->type(); 		// 返回图片的类型
		$mime   = $image->mime(); 		// 返回图片的mime类型
		$size   = $image->size(); 		// 返回图片的尺寸数组 0 图片宽度 1 图片高度
		// var_dump($width, $height, $type, $mime, $size);

		// 将图片裁剪为400x400并保存为copy_fruit.jpg
		$copy_image_path = APP_PATH . '/data/copy_fruit.jpg';
		$image->crop(400, 400)->save($copy_image_path);

		// 支持从某个坐标开始裁剪，例如下面从（100，30）开始裁剪：
		// 将图片裁剪为400x400并保存为copy_fruit.jpg
		// $copy_image_path = APP_PATH . '/data/copy_fruit2.jpg';
		// $image->crop(400, 400, 100, 30)->save($copy_image_path);

		// 生成缩略图
		// 按照原图的比例生成一个最大为150*150的缩略图并保存为thumb.jpg
		// $thumb_image_path = APP_PATH . '/data/thumb.jpg';
		// $image->thumb(150, 150)->save($thumb_image_path);

		// 居中裁剪
		// 生成一个居中裁剪为150*150的缩略图并保存为thumb2.jpg
		// $thumb_image_path = APP_PATH . '/data/thumb2.jpg';
		// $image->thumb(150, 150, winer\Image::IMAGE_THUMB_CENTER)->save($thumb_image_path);

		// 添加图片水印
		// 将图片裁剪为440x440并保存为corp.jpg
		// $crop_image_path = APP_PATH . '/data/corp.jpg';
		// $watermark_image_path = APP_PATH . '/data/watermark.gif';
		// $image->crop(440, 440)->save($crop_image_path);
		// 给裁剪后的图片添加图片水印，位置为右下角，保存为corp.jpg
		// $image->water($watermark_image_path)->save($crop_image_path);
		
		// 文字水印
		// $save_image_path = APP_PATH . '/data/new.jpg';
		// $fft_path = APP_PATH . '/library/winer/Verify/zhttfs/yahei.ttf';
		// $image->text('winer', $fft_path, 20, '#000000', winer\Image::IMAGE_WATER_SOUTHEAST)->save($save_image_path);
		exit('end');
	}

	/**
	 * 验证码。
	 */
	public function verifyAction() {
		// [1]
// 		$Verify = new \winer\Captcha();
// 		$Verify->entry();

		// [2] 定义字符数和字体大小
		$Verify = new \winer\Captcha();
		$Verify->fontSize = 14;
		$Verify->length   = 4;
		$Verify->useNoise = false;
		$Verify->entry();
		
		// [3] 自定义背景图片
// 		$Verify = new \winer\Captcha();
		// 开启验证码背景图片功能 随机使用 ThinkPHP/Library/Think/Verify/bgs 目录下面的图片
// 		$Verify->useImgBg = true;
// 		$Verify->useNoise = false;
// 		$Verify->entry();
		
		// [4] 中文验证码
// 		$Verify = new \winer\Captcha();
		// 验证码字体使用 ThinkPHP/Library/Think/Verify/ttfs/5.ttf
// 		$Verify->useZh = true;
// 		$Verify->entry();
		$this->end();
	}

	/**
	 * 验证验证码是否正确。
	 * -- 检测输入的验证码是否正确，$code为用户输入的验证码字符串
	 */
	public function checkVerifyAction() {
		$code = $this->_request->getQuery('code', '');
		$verify = new \winer\Captcha();
		$bool = $verify->check($code);
		var_dump($bool);
		$this->end();
	}
}