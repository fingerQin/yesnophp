<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>评论列表</title>
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

ul.ul {
    list-style-type: initial;
    padding-left: .4rem;
}

ul.ul li {
    font-size: .3rem;
    margin: .1rem 0;
    line-height: 1.5;
}

/* 通用星星样式 */
.stars {
    font-style: normal;
    font-size: .36rem;
    line-height: .36rem;
}

</style>

</head>
<body id="deal-detail" data-com="pagecommon">

<header class="navbar">
    <div class="nav-wrap-left">
        <a class="react back" href="javascript:history.back()"><i class="text-icon icon-back"></i></a>
    </div>
    <h1 class="nav-header"></h1>
    <div class="nav-wrap-right">
        <a class="react nav-dropdown-btn" data-com="dropdown" data-target="nav-dropdown">
            <span class="nav-btn">
                <i class="text-icon">≋</i>导航
            </span>
        </a>
    </div>
</header>

<div id="deal" class="deal">

<dl class="list">
    <dd>
        <dl>
            <dt>怎样预防登革热</dt>
            <dd class="dd-padding">
				<div class="merchant">
				    <div class="biz-detail">
				        <a class="react">
				            <div>惠城区惠州大道11号佳兆业广场4楼惠城区惠州大道11号佳兆业广场4楼惠城区惠州大道11号佳兆业广场4楼</div>
				        </a>
				    </div>
				</div>
            </dd>
        </dl>
    </dd>
</dl>

<dl class="list" id="deal-feedback">
    <dd>
        <dl>

			<!-- 评论列表 start -->
			<dd class="dd-padding">
				<div class="feedbackCard">
					<div class="userInfo"><weak class="username">sally2373</weak></div>
					<div class="score">
				    	<span class="stars"><i class="text-icon icon-star"></i><i class="text-icon icon-star"></i><i class="text-icon icon-star"></i><i class="text-icon icon-star"></i><i class="text-icon icon-star"></i></span>
						<weak class="time">2014-11-15</weak>
					</div>
				    <div class="comment">
						<p>我这周第二次团购这里看电影了，带着儿子来佳兆业，玩，吃，看电影特别好。影院播放的视听效果我很喜欢，看着马达加斯加的企鹅，回忆了很多童年的欢乐，不错的一部亲子电影。</p>
				    </div>
					<div><weak>凯狮国际IMAX影城</weak></div>
				</div>
			</dd>

			<dd class="dd-padding">
				<div class="feedbackCard">
					<div class="userInfo"><weak class="username">sally2373</weak></div>
					<div class="score">
						<span class="stars"><i class="text-icon icon-star"></i><i class="text-icon icon-star"></i><i class="text-icon icon-star"></i><i class="text-icon icon-star"></i><i class="text-icon icon-star"></i></span>
						<weak class="time">2014-11-13</weak>
					</div>
					<div class="comment">
					        <div class="toggleContent" data-com="toggleClass">
					        <p>
					        今晚我们团购了两张星际穿越，超喜欢来影厅观赏科幻片，不仅看到电影的特技效果强，同时也可以给小朋友越多些和接触到新的知识。电影院的环境和座位安排得很舒适，在这影院<i class="text-icon icon-ell"></i><span
					                class="feedbackmore">空间播放出来的音响效果很好，简直还有些3D效果。</span><i
					                class="text-icon icon-toggle-arrow"></i>
					        </p>
					        </div>
					</div>
					<div><weak>凯狮国际IMAX影城</weak></div>
				</div>
			</dd>

			<dd class="dd-padding">
				<div class="feedbackCard">
					<div class="userInfo"><weak class="username">小鳯鳯</weak></div>
					<div class="score">
				    	<span class="stars"><i class="text-icon icon-star"></i><i class="text-icon icon-star"></i><i class="text-icon icon-star"></i><i class="text-icon icon-star"></i><i class="text-icon icon-star"></i></span>
						<weak class="time">2014-11-11</weak>
					</div>
					<div class="comment">
						<p>看得是22:20分的忍者神龟3D版，还不错的说一如既往的支持凯狮。团购价还要加10/票才可以。不过我感觉真的没必要什么片子都要个3D版的，真正镜头没有几个感受到。不过整体还是蛮不错的。</p>
					</div>
					<div><weak>凯狮国际IMAX影城</weak></div>
				</div>
			</dd>
			<!-- 评论列表 end -->

			<!-- 分页 start -->
			<dd>
				<div class="pager">
					<a class="btn btn-weak btn-disabled">上一页</a>
			    	<span class="pager-current">1</span>
					<a class="btn btn-weak" data-page-num="2">下一页</a>
				</div>
			</dd>
			<!-- 分页 end -->

		</dl>
	</dd>

</dl>

</div>

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