<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>用户登录</title>
<meta name="description" content="每天团购一次，精品消费指南">
<meta name="viewport" content="initial-scale=1, width=device-width, maximum-scale=1, user-scalable=no">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-touch-fullscreen" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta name="format-detection" content="telephone=no">
<meta name="format-detection" content="address=no">
<link rel="icon" href="http://ms0.meituan.net/touch/img/icon/favicon.ico" type="image/x-icon">
<link href="http://frontend.yesnophp.com/css/eve.css" rel="stylesheet">

<style>
    .nav {
        text-align: center;
    }
    .subline {
        margin: .28rem .2rem;
    }
    .subline li {
        display: inline-block;
    }
    .captcha img {
        margin-left: .2rem;
    }
    .captcha .btn {
        margin-top: -.15rem;
        margin-bottom: -.15rem;
        margin-left: .2rem;
    }
</style>

</head>
<body id="account" data-com="pagecommon">


<header class="navbar">
	<div class="nav-wrap-left">
		<a class="react back" href="javascript:history.back()"><i class="text-icon icon-back"></i></a>
	</div>
    <h1 class="nav-header">美团网</h1>
        <div class="nav-wrap-right">
            <a class="react" href="/index.html">
                <span class="nav-btn"><i class="text-icon">⟰</i>首页</span>
            </a>
        </div>
        <div class="nav-wrap-right">
            <a class="react" href="/search.html">
                <span class="nav-btn"><i class="text-icon">⌕</i>搜索</span>
            </a>
        </div>
</header>


<div id="login">
	<dl class="list">
	    <dd class="nav">
	        <ul class="taba" data-com="tab">
	            <li class="active" tab-target="normal-login-form"><a class="react">美团账号登录</a>
	            <!-- <li tab-target="quick-login-form"><a class="react">手机验证登录</a> -->
	        </ul>
	    </dd>
	</dl>
    <form id="normal-login-form" action="" autocomplete="off" method="post">
        <dl class="list list-in"><dd><dl>
            <dd class="dd-padding"><input id="username" class="input-weak" type="text" placeholder="账户名/手机号/Email" name="email" value="" required></dd>
            <dd class="dd-padding"><input id="password" class="input-weak" type="password" placeholder="请输入您的密码" name="password" required/></dd>
        </dl></dd></dl>
        <div class="btn-wrapper">
			<button type="submit" class="btn btn-larger btn-block" >登录</button>
        </div>
    </form>
    <form id="quick-login-form" action="" autocomplete="off" method="post" style="display:none;">
        <dl class="list list-in">
        	<dd>
	        	<dl>
		            <dd class="kv-line-r dd-padding" data-com="smsBtn" data-requrl="/account/mobilelogincode">
		                <input type="tel" name="mobile" id="login-mobile" class="input-weak kv-k" placeholder="请输入手机号">
		                <button id="smsCode" type="button" class="btn btn-weak kv-v" disabled="disabled">发送验证码</button>
		            </dd>
		            <dd class="dd-padding">
		                <input class="input-weak" name="code" type="tel" pattern="[0-9]+" placeholder="请输入手机短信中的验证码">
		            </dd>
	        	</dl>
        	</dd>
        </dl>
        <div class="btn-wrapper">
			<button type="submit" class="btn btn-larger btn-block" >登录</button>
        </div>
    </form>
</div>
<ul class="subline">
    <li><a href="">立即注册</a>
    <li class="pull-right"><a href="">找回密码</a>
</ul>


<footer>
	<div class="footer-bar">
		<div class="pull-right">
			<span>城市:</span>
			<space></space><a href="" class="btn btn-weak footer-citybtn" >惠州</a>
		</div>
		<a href="" class="btn btn-weak"  rel="nofollow">登录</a>
		<space></space><a href="" class="btn btn-weak"  rel="nofollow">注册</a>
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
	<div class="footer-links">友情链接：
	    <a target="_blank" href="">猫眼电影</a>
	</div>
	<div class="footer-copyright">
	    <div class="hr"></div>
	    <span class="footer-copyright-text">©2014 每天必看网 <a href="http://www.miibeian.gov.cn/" target="_blank">京ICP证070791号</a></span>
	</div>
</footer>

</body>
</html>