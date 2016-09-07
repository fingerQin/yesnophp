<?php
use common\YCore;
use common\YUrl;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7">
	<title>Yesno - 后台管理中心</title> <!-- 管理后台主题样式 start -->
	<link href="<?php echo YUrl::assets('css', '/backend/reset.css'); ?>"
		rel="stylesheet" type="text/css">
		<link
			href="<?php echo YUrl::assets('css', '/backend/zh-cn-system.css'); ?>"
			rel="stylesheet" type="text/css">
			<link
				href="<?php echo YUrl::assets('css', '/backend/table_form.css'); ?>"
				rel="stylesheet" type="text/css">
				<link rel="stylesheet" type="text/css"
					href="<?php echo YUrl::assets('css', '/backend/zh-cn-styles1.css'); ?>"
					title="styles1" media="screen">
					<link rel="alternate stylesheet" type="text/css"
						href="<?php echo YUrl::assets('css', '/backend/zh-cn-styles2.css'); ?>"
						title="styles2" media="screen">
						<link rel="alternate stylesheet" type="text/css"
							href="<?php echo YUrl::assets('css', '/backend/zh-cn-styles3.css'); ?>"
							title="styles3" media="screen">
							<link rel="alternate stylesheet" type="text/css"
								href="<?php echo YUrl::assets('css', '/backend/zh-cn-styles4.css'); ?>"
								title="styles4" media="screen">
								<!-- 管理后台主题样式 end -->

								<!-- JQuery start -->
								<script
									src="<?php echo YUrl::assets('js', '/jquery-1.10.2.js'); ?>"></script>
								<!-- JQuery end -->

								<!-- ArtDialog 对话框 start -->
								<link rel="stylesheet"
									href="<?php echo YUrl::assets('js', '/artDialog/css/ui-dialog.css'); ?>">
									<script
										src="<?php echo YUrl::assets('js', '/artDialog/dist/dialog-min.js'); ?>"></script>
									<script
										src="<?php echo YUrl::assets('js', '/artDialog/dist/dialog-plus-min.js'); ?>"></script>
									<!-- ArtDialog 对话框 end -->

									<!-- SimpleAjaxUpload 插件 start -->
									<script
										src="<?php echo YUrl::assets('js', '/AjaxUploader/SimpleAjaxUploader.min.js'); ?>"></script>
									<!-- SimpleAjaxUpload 插件 end -->

									<!-- 时间插件 start -->
									<link rel="stylesheet" type="text/css"
										href="<?php echo YUrl::assets('js', '/backend/calendar/jscal2.css'); ?>" />
									<link rel="stylesheet" type="text/css"
										href="<?php echo YUrl::assets('js', '/backend/calendar/border-radius.css'); ?>" />
									<link rel="stylesheet" type="text/css"
										href="<?php echo YUrl::assets('js', '/backend/calendar/win2k.css'); ?>" />
									<script type="text/javascript"
										src="<?php echo YUrl::assets('js', '/backend/calendar/calendar.js'); ?>"></script>
									<script type="text/javascript"
										src="<?php echo YUrl::assets('js', '/backend/calendar/lang/en.js'); ?>"></script>
									<!-- 时间插件 end -->

									<script language="javascript" type="text/javascript"
										src="<?php echo YUrl::assets('js', '/backend/backend_common.js'); ?>"></script>
									<script language="javascript" type="text/javascript"
										src="<?php echo YUrl::assets('js', '/backend/styleswitch.js'); ?>"></script>
									<script language="javascript" type="text/javascript"
										src="<?php echo YUrl::assets('js', '/backend/hotkeys.js'); ?>"></script>
									<script language="javascript" type="text/javascript"
										src="<?php echo YUrl::assets('js', '/backend/jquery.sgallery.js'); ?>"></script>

</head>
<body>