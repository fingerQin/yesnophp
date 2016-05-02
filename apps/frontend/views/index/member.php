<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>会员中心</title>
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
    .my-account {
        color: #333;
        position: relative;
        background: -webkit-linear-gradient(top,#e1dace,#dfc8b4);
        border-bottom: 1px solid #C0BBB2;
        display: block;
        height: 1.6rem;
        position: relative;
        padding-right: .2rem;
    }
    .my-account>img {
        height: 100%;
        position: absolute;
        right: 0;
        top:0;
        z-index: 0;
    }
    .my-account .user-info {
        z-index: 1;
        position: relative;
        height: 100%;
        padding: .28rem .2rem;
        margin-right: .2rem;
        box-sizing: border-box;
        padding-left: 2rem;
        font-size: .24rem;
        color: #666;
    }
    .my-account .uname {
        font-size: .3rem;
        color: #333;
        margin-top: .1rem;
        margin-bottom: .25rem;
    }
    .my-account strong {
        color: #FF9712;
        font-weight: normal;
    }
    .my-account .avater {
        position: absolute;
        top: .2rem;
        left: .4rem;
        width: 1.2rem;
        height: 1.2rem;
        border-radius: 50%;
    }
    .my-account .more.more-weak:after {
        border-color: #666;
        -webkit-transform: translateY(-50%) scaleY(1.2) rotateZ(-135deg);
    }
    .orderindex li {
        display: inline-block;
        width: 25%;
        text-align:center;
        position: relative;
    }
    .orderindex li .react {
        padding: .28rem 0;
    }
    .orderindex .text-icon {
        display: block;
        font-size: .6rem;
        margin-bottom: .18rem;
    }
    .orderindex .amount-icon {
        position: absolute;
        left: 50%;
        top: .16rem;
        color: white;
        background: #EC5330;
        border-radius: 50%;
        padding: .08rem .06rem;
        min-width: .28rem;
        font-size: .24rem;
        margin-left: .1rem;
        display: none;
    }
    .order-icon {
        display: inline-block;
        width: .5rem;
        height: .5rem;
        text-align: center;
        line-height: .5rem;
        border-radius: .06rem;
        color: white;
        margin-right: .25rem;
        margin-top: -.06rem;
        margin-bottom: -.06rem;
        background-color: #F5716E;
        vertical-align: initial;
        font-size: .3rem;
    }
    .order-all {
        background-color: #2bb2a3;
    }
    .order-zuo,.order-jiudian {
        background-color: #F5716E;
    }
    .order-fav {
        background-color: #0092DE;
    }
    .order-card {
        background-color: #EB2C00;
    }
    .order-lottery {
        background-color: #F5B345;
    }
    .level-icon{
        vertical-align: middle;
        margin-left: .2rem;
    }
</style>

</head>
<body id="account" data-com="pagecommon">

	<header class="navbar">
	        <div class="nav-wrap-left">
	            <a class="react back" href=""><i class="text-icon icon-back"></i></a>
	        </div>
	    <h1 class="nav-header">我的美团</h1>
	        <div class="nav-wrap-right">
	            <a class="react" href="">
	                <span class="nav-btn">
	                    <i class="text-icon">⟰</i>首页
	                </span>
	            </a>
	        </div>
	        <div class="nav-wrap-right">
	            <a class="react" href="">
	                <span class="nav-btn">
	                    <i class="text-icon">⌕</i>搜索
	                </span>
	            </a>
	        </div>
	</header>


<div><a class="my-account" href="">
        <img src="http://ms0.meituan.net/touch/img/my-photo.png"/>
        <img class="avater" src="http://ms0.meituan.net/touch/img/pic-default.png">
        <div class="user-info more more-weak">
            <p class="uname">eyeoftheworld<i class="level-icon level0"></i></p>
            <p>账户余额：<strong>0</strong>元</p>
        </div>
</a></div>


<dl class="list">
    <dd>
        <a class="react" href="">
            <div class="more more-weak">
                <i class="text-icon order-all order-icon">⎕</i>全部团购订单<span data-key="all" class="more-after"></span>
            </div>
        </a>
    </dd>
    <dd><ul class="orderindex">
        <li><a href="" class="react">
            <i class="text-icon">⌻</i>
            <span>未消费</span>
            <span data-key="unused" class="amount-icon"></span>
        </a>
        <li><a href="" class="react">
            <i class="text-icon">⌸</i>
            <span>待付款</span>
            <span data-key="unpaid" class="amount-icon"></span>
        </a>

        <li><a href="" class="react">
            <i class="text-icon">⌹</i>
            <span>待评价</span>
            <span data-key="needfeedback" class="amount-icon"></span>
        </a>
        <li><a href="" class="react">
            <i class="text-icon">⌺</i>
            <span>退款单</span>
            <span data-key="haverefund" class="amount-icon"></span>
        </a>
    </ul></dd>
</dl>

<dl class="list">
    <dd><a class="react" href=""><div class="more more-weak">
        <i class="text-icon order-zuo order-icon">座</i>电影选座订单<span data-key="xuanzuo" class="more-after"></span>
    </div></a></dd>
    <dd><a class="react" href=""><div class="more more-weak">
        <i class="text-icon order-jiudian order-icon">订</i>酒店快订订单<span data-key="hotel" class="more-after"></span>
    </div></a></dd>
</dl>

<dl class="list">
    <dd><a class="react" href=""><div class="more more-weak">
        <i class="text-icon order-fav order-icon">☆</i>我的收藏<span data-key="collection" class="more-after"></span>
    </div></a></dd>
    <dd><a class="react" href=""><div class="more more-weak">
        <i class="text-icon order-card order-icon">□</i>我的抵用券<span data-key="magiccard" class="more-after"></span>
    </div></a></dd>
    <dd><a class="react" href=""><div class="more more-weak">
        <i class="text-icon order-lottery order-icon">⛣</i>我的抽奖单<span data-key="lotterys" class="more-after"></span>
    </div></a></dd>
</dl>
    
    
<footer>
	<div class="footer-bar">
		<div class="pull-right">
			<span>城市:</span>
			<space></space><a href="" class="btn btn-weak footer-citybtn" >惠州</a>
		</div>
		<a href="" class="btn btn-weak" rel="nofollow">登录</a>
		<space></space><a href="" class="btn btn-weak" rel="nofollow">注册</a>
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