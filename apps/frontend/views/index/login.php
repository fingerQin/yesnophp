<?php 
use common\YUrl;
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>用户登录</title>
<meta name="viewport" content="initial-scale=1, width=device-width, maximum-scale=1, user-scalable=no">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-touch-fullscreen" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta name="format-detection" content="telephone=no">
<meta name="format-detection" content="address=no">


</head>
<body id="account" data-com="pagecommon">


<div id="login">
    <form id="normal-login-form" action="" autocomplete="off" method="post">
        <dl class="list list-in"><dd><dl>
            <dd class="dd-padding"><input id="username" class="input-weak" type="text" placeholder="账户名/手机号/Email" name="username" value="" required></dd>
            <dd class="dd-padding"><input id="password" class="input-weak" type="password" placeholder="请输入您的密码" name="password" required/></dd>
        </dl></dd></dl>
        <div class="btn-wrapper">
			<button type="submit" class="btn btn-larger btn-block" >登录</button>
        </div>
    </form>
</div>


</body>
</html>