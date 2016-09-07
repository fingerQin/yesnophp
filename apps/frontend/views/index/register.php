<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>注册</title>
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

</head>
<body id="signupverify" data-com="pagecommon">

	<header class="navbar">
		<div class="nav-wrap-left">
			<a class="react back" href="/"><i class="text-icon icon-back"></i></a>
		</div>
		<h1 class="nav-header">注册账号</h1>
		<div class="nav-wrap-right">
			<a class="react nav-dropdown-btn" data-com="dropdown"
				data-target="nav-dropdown"> <span class="nav-btn"><i
					class="text-icon">≋</i>导航</span>
			</a>
		</div>
	</header>

	<form id="login-form" action="" method="post">
		<dl class="list">
			<dd class="dd-padding">
				<ol class="crumbs">
					<li class="active">输入手机号</li>
					<li>输入验证码</li>
					<li>设置密码</li>
				</ol>
			</dd>
		</dl>
		<dl class="list">
			<dd class="dd-padding">
				<input id="mobile-number" class="input-weak" type="tel"
					placeholder="请输入您的手机号" name="mobile" value="" autocomplete="off">
			</dd>
		</dl>
		<p class="btn-wrapper">
			<label onclick=""> <input id="term" type="checkbox" name="terms"
				checked="checked" class="mt" />我已阅读并同意<a href="./terms.html">《美团网用户协议》</a>
			</label>
		</p>
		<div class="btn-wrapper">
			<button type="submit" class="btn btn-block btn-larger"
				disabled="disabled">获取验证码</button>
		</div>
	</form>


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


	<script>
require(["zepto.js"], function ($) {
    var $submitBtn = $('.btn-larger'),
        CHECKED = 'checked';

    $('#mobile-number').on('input', function() {
        var val = this.value;
        if (/^\d{11}$/.test(val)) {
            if ($('#term').prop(CHECKED)) {
                $submitBtn.removeAttr("disabled");
            }
        } else {
            $submitBtn.prop("disabled", "disabled");
        }
    });

    $('#term').on('change', function() {
        if (!$(this).prop(CHECKED)) {
            $submitBtn.prop("disabled", "disabled");
        } else if (/^\d{11}$/.test($('#mobile-number').val())) {
            $submitBtn.prop("disabled", '');
        }
    });
});
</script>

</body>
</html>