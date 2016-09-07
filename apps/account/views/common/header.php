<?php
use common\YUrl;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<title>账户中心</title>
<link rel="stylesheet"
	href="<?php echo YUrl::assets('css', '/shop/common.css'); ?>" />

<!-- JQuery start -->
<script src="<?php echo YUrl::assets('js', '/jquery-1.10.2.js'); ?>"></script>
<!-- JQuery end -->

<!-- ArtDialog 对话框 start -->
<link rel="stylesheet"
	href="<?php echo YUrl::assets('js', '/artDialog/css/ui-dialog.css'); ?>">
<script
	src="<?php echo YUrl::assets('js', '/artDialog/dist/dialog-min.js'); ?>"></script>
<script
	src="<?php echo YUrl::assets('js', '/artDialog/dist/dialog-plus-min.js'); ?>"></script>
<!-- ArtDialog 对话框 end -->

<!-- laydate时间控件 start -->
<script src="<?php echo YUrl::assets('js', '/laydate/laydate.js'); ?>"></script>
<!-- laydate时间控件 end -->

<script src="<?php echo YUrl::assets('js', '/shop/common.js'); ?>"></script>


</head>
<body>
	<style type="text/css">
.site-header {
	height: 70px;
	background: #ECEDED;
}

.site-header .logo {
	margin-top: 15px;
}

.title-left {
	float: left;
	width: 400px;
}

.title-left .h1 {
	display: inline-block;
}

.title-left .nav-title-line {
	float: left;
	width: 1px;
	height: 40px;
	margin: 15px 10px;
	background: #808080;
	display: inline-table;
}

.title-left  .nav-title {
	color: #1a1a1a;
	height: 40px;
	line-height: 40px;
	font-size: 16px;
	font-weight: normal;
	float: left;
	margin-top: 15px;
}

.site-top {
	height: 25px;
	background: #242735;
}

.site-header .w .user-info {
	margin-top: 25px;
	font-size: 12px;
}

.site-link a {
	margin: 15px;
}

.site-footer {
	padding: 10px 0 20px;
}
</style>

	<div class="site-top"></div>
	<div class="site-header">
		<div class="w">
			<div class="title-left">
				<h1 class="logo">
					<a href="/index/shop"><img height="40px"
						src="<?php echo YUrl::assets('image', '/shop/logo3.png'); ?>" /></a>
				</h1>
				<em class="nav-title-line"></em> <span class="nav-title">账户中心</span>
			</div>
			<div class="user-info" id="user-info">
				你好，xxxx（18575202691），<a href="/site/edit-pw">[修改密码]</a><a
					href="javascript:;" class="sign app-logout">［退出］</a>
			</div>
		</div>
	</div>
	<div style="clear: both;"></div>