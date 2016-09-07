<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>分类大全</title>
<meta name="description" content="每天团购一次，精品消费指南">
<meta name="viewport"
	content="initial-scale=1, width=device-width, maximum-scale=1, user-scalable=no">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-touch-fullscreen" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta name="format-detection" content="telephone=no">
<meta name="format-detection" content="address=no">
<link rel="icon"
	href="http://ms0.meituan.net/touch/img/icon/favicon.ico"
	type="image/x-icon">
<link href="http://frontend.yesnophp.com/css/eve.css" rel="stylesheet">

<style>
.box {
	margin-top: .2rem;
	margin-bottom: .4rem;
}

.box:last-child {
	margin-bottom: 0;
}

.cateStrong {
	color: #DD633C
}

@charset "UTF-8";
/**
 * 格子
 * @name .table/.table-tall
 * @tag ul>li
 */
.table {
	min-height: .8rem;
	position: relative;
	overflow: hidden;
	z-index: 0;
}

.table:before {
	content: '';
	position: absolute;
	width: 25%;
	left: 25%;
	height: 100%;
	border-left: 1px solid #ddd8ce;
	border-right: 1px solid #ddd8ce;
}

.table:after {
	content: '';
	position: absolute;
	width: 10%;
	left: 75%;
	height: 100%;
	border-left: 1px solid #ddd8ce;
	border-right: none;
}

.table.table-t3:before {
	width: 33.33%;
	left: 33.33%;
}

.table.table-t3:after {
	border: none;
}

.table li, .table h4 {
	display: inline-block;
	width: 25%;
	height: .8rem;
	line-height: .8rem;
	font-size: .28rem;
	text-align: center;
	border-bottom: 1px solid #ddd8ce;
	margin-bottom: -1px;
	float: left;
	position: relative;
	z-index: 10;
}

.table.table-t3 li, .table.table-t3 h4 {
	width: 33.33%;
}

/*/////多栏变换  */
@media ( min-width : 480px) {
	.table li, .table h4 {
		width: 20%;
	}
	.table:before {
		width: 20%;
		left: 20%;
	}
	.table:after {
		width: 20%;
		left: 60%;
		border-right: 1px solid #ddd8ce;
	}
	.table.table-t3 li, .table.table-t3 h4 {
		width: 25%;
	}
	.table.table-t3:before {
		width: 25%;
		left: 25%;
	}
	.table.table-t3:after {
		width: 10%;
		left: 75%;
		border-left: 1px solid #ddd8ce;
	}
}

.table h4 {
	margin: 0;
	margin-bottom: -1px;
	height: 1.6rem;
	line-height: 1.6rem;
	color: #B7B7B7;
	font-size: .8rem;
}

/** another solution with muti background-image: http://jsbin.com/bemexuza/5/watch **/
</style>

</head>
<body id="category" data-com="pagecommon">

	<header class="navbar">
		<div class="nav-wrap-left">
			<a class="react back" href="javascript:history.back()"><i
				class="text-icon icon-back"></i></a>
		</div>
		<h1 class="nav-header">选择分类</h1>
		<div class="nav-wrap-right">
			<a class="react nav-dropdown-btn" data-com="dropdown"
				data-target="nav-dropdown"> <span class="nav-btn"> <i
					class="text-icon">≋</i>导航
			</span>
			</a>
		</div>
	</header>

	<div id="tips"></div>


	<div id="category" class="wrapper">
		<div class="box box-btn">
			<a class="react" href="">全部分类</a>
		</div>
		<h4>文学</h4>
		<ul class="box nopadding table table-t4">
			<li><a class="react" href="">全部</a></li>
			<li><a class="react" href="">成语</a></li>
			<li><a class="react" href="">寓言</a></li>
			<li><a class="react" href="">歇后语</a></li>
			<li><a class="react" href="">励志</a></li>
			<li class="cateStrong"><a class="react" href="">笑话</a></li>
			<li><a class="react" href="">课文</a></li>
			<li><a class="react" href="">试题</a></li>
		</ul>
		<h4>历史</h4>
		<ul class="box nopadding table table-t4">
			<li><a class="react" href="">全部</a></li>
			<li><a class="react" href="">唐史</a></li>
			<li><a class="react" href="">宋史</a></li>
			<li><a class="react" href="">元史</a></li>
			<li><a class="react" href="">明史</a></li>
			<li><a class="react" href="">清史</a></li>
			<li><a class="react" href="">近代史</a></li>
			<li><a class="react" href="">世界史</a></li>
			<li><a class="react" href="">野史</a></li>
			<li><a class="react" href="">怪谈</a></li>
		</ul>
		<h4>美食</h4>
		<ul class="box nopadding table table-t4">
			<li><a class="react" href="">全部</a></li>
			<li><a class="react" href="">川味</a></li>
			<li><a class="react" href="">湘味</a></li>
			<li><a class="react" href="">粤菜</a></li>
			<li><a class="react" href="">赣菜</a></li>
			<li><a class="react" href="">豫菜</a></li>
			<li><a class="react" href="">鲁菜</a></li>
			<li><a class="react" href="">客家菜</a></li>
			<li><a class="react" href="">东北菜</a></li>
			<li><a class="react" href="">各地特产</a></li>
		</ul>
		<h4>旅游</h4>
		<ul class="box nopadding table table-t4">
			<li><a class="react" href="">全部</a></li>
			<li><a class="react" href="">名山</a></li>
			<li><a class="react" href="">名湖</a></li>
			<li><a class="react" href="">江河</a></li>
			<li><a class="react" href="">古寨</a></li>
			<li><a class="react" href="">古城</a></li>
			<li><a class="react" href="">岛屿</a></li>
			<li><a class="react" href="">奇观</a></li>
		</ul>
	</div>

	<footer>
		<div class="footer-bar">
			<div class="pull-right">
				<span>城市:</span>
				<space></space>
				<a href="" class="btn btn-weak footer-citybtn">惠州</a>
			</div>
			<a href="" class="btn btn-weak" rel="nofollow">登录</a>
			<space></space>
			<a href="" class="btn btn-weak" rel="nofollow">注册</a>
		</div>
		<div class="footer-nav">
			<ul>
				<li><a class="react" href="">首页</a>
				
				<li><a class="react" rel="nofollow" href="">订单</a>
				
				<li><a data-pos="footer" class="react" data-com="i2app" href="">客户端</a>
				
				<li><a class="react" href="">电脑版</a>
				
				<li><a class="react" href="">帮助</a>
			
			</ul>
		</div>
		<div class="footer-links">
			友情链接： <a target="_blank" href="">猫眼电影</a>
		</div>
		<div class="footer-copyright">
			<div class="hr"></div>
			<span class="footer-copyright-text">©2014 每天必看网 <a
				href="http://www.miibeian.gov.cn/" target="_blank">京ICP证070791号</a></span>
		</div>
	</footer>

</body>
</html>