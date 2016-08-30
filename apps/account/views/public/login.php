<?php
use common\YCore;
use common\YUrl;
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=Edge" />
    <title>不单卖-欢迎登录</title>
    <link type="text/css" rel="stylesheet"  href="<?php echo YUrl::assets('css', '/account/login.css'); ?>" source="widget"/>
    <script type="text/javascript" src="<?php echo YUrl::assets('js', 'jquery-1.10.2.js'); ?>"></script>
</head>
<body>


    <div class="w">
        <div id="logo"><a href="<?php echo YCore::config('frontend_domain_name'); ?>"><img src="<?php echo YUrl::assets('image', '/account/login/logo-20160623-b.png'); ?>" alt="不单卖" width="170" height="60"></a><b></b></div>
    </div>
    <div id="content">
        <div class="login-wrap">
            <div class="w">
                <div class="login-form">
                    <div class="login-box">
                        <div class="mt">
                            <h1>不单卖会员</h1>
                            <div class="extra-r">
                                <div class="regist-link"><a href="" target="_blank"><b></b>立即注册</a></div>
                            </div>
                        </div>
                        <div class="msg-wrap">
                            <div class="msg-warn"><b></b>公共场所不建议自动登录，以防账号丢失</div>
                            <div class="msg-error hide"><b></b></div>
                        </div>
                        <div class="mc">
                            <div class="form">
                                <form id="formlogin" method="post">
                                	<input type="hidden" name="redirect_url" value="<?php echo $redirect_url; ?>"/>
                                    <input type="hidden" id="uuid" name="redirect_url" value=""/>
                                    <div class="item item-fore1">
	                                    <label for="loginname" class="login-label name-label"></label>
	                                    <input id="loginname" type="text" class="itxt" name="username" tabindex="1" autocomplete="off" placeholder="邮箱/用户名/已验证手机" />
	                                    <span class="clear-btn"></span>
                                    </div>
                                    <div id="entry" class="item item-fore2">
                                        <label class="login-label pwd-label" for="nloginpwd"></label>
                                        <input type="password" id="nloginpwd" name="password" class="itxt itxt-error" tabindex="2" autocomplete="off" placeholder="密码"/>
                                        <span class="clear-btn"></span>
                                    </div>
                                    <div class="item item-fore3">
                                        <div class="safe">
                                        <span>
                                            <input id="autoLogin" name="chkRememberMe" type="checkbox" class="jdcheckbox" tabindex="3">
                                            <label for="">自动登录</label>
                                        </span>
										<span class="forget-pw-safe">
                                            <a href="" class="" target="_blank">忘记密码?</a>
                                        </span>
                                        </div>
                                    </div>
                                    
                                    <div class="item item-fore5">
                                        <div class="login-btn">
                                            <a href="javascript:;" class="btn-img btn-entry" id="loginsubmit" onClick="$('#formlogin').submit();" tabindex="6">登&nbsp;&nbsp;&nbsp;&nbsp;录</a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="coagent">
                                <h5>使用合作网站账号登录不单卖：</h5>
                                <ul>
									<li><a href="javascript:void(0)" onclick="">微博</a><span class="line">|</span></li>
									<li><a href="javascript:void(0)" onclick="">QQ</a><span class="line">|</span></li>
                                    <li><a href="javascript:void(0)" onclick="">微信</a></li>
								</ul>
                            </div>
                        </div>
                    </div>
            </div>
            </div>
            <div class="login-banner">
                <div class="w">
                    <div id="banner-bg" class="i-inner"></div>
                </div>
            </div>
        </div>
    </div>
 
    <div class="w">
        <div id="footer-2013">
            <div class="links">
                <a rel="nofollow" target="_blank" href="">关于我们</a>
                |
                <a rel="nofollow" target="_blank" href="">联系我们</a>
                |
                <a rel="nofollow" target="_blank" href="">人才招聘</a>
                |
                <a rel="nofollow" target="_blank" href="">商家入驻</a>
                |
                <a target="_blank" href="">友情链接</a>
            </div>
            <div class="copyright">
                Copyright&copy;2004-2016&nbsp;&nbsp;不单卖budanmai.com&nbsp;版权所有
            </div>
        </div>
    </div>

	<script type="text/javascript">	
	~function () {		
		var data = [{			  
			src:"<?php echo YUrl::assets('image', '/account/login/background-20160623.png'); ?>",			  
			bgColor:"#e93854",                          
			weight: "4"			
		}];						         
		var getRandom = function (arr) {			
			var _temp = 0, _random = 0, _weight, _newArr = [];						
			for (var i = 0; i < arr.length; i++) {				
				_weight = arr[i].weight ? parseInt(arr[i].weight) : 1;				
				_newArr[i] = [];				
				_newArr[i].push(_temp);				
				_temp += _weight;				
				_newArr[i].push(_temp);			
			}						
			_random = Math.ceil(_temp * Math.random());						
			for (var i = 0; i< _newArr.length; i++) {				
				if (_random > _newArr[i][0] && _random <= _newArr[i][1]) {					
					return arr[i];				
				}			
			}			
		};			
		var tpl = '<div class="login-banner" style="background-color: {bgColor}">\		              <div class="w">\			         <div id="banner-bg"  clstag="pageclick|keycount|20150112ABD|46" class="i-inner" style="background: url({imgURI}) 0px 0px no-repeat;background-color: {bgColor}"></div>\		              </div>\		           </div>';							
		var bgData = getRandom(data);		
		var bannerHtml = tpl.replace(/{bgColor}/g, bgData.bgColor).replace(/{imgURI}/g, bgData.src);				
		$('.login-banner').replaceWith(bannerHtml);
	}();
	</script>
    </body>
</html>