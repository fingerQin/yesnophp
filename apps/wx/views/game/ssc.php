<?php
use common\YUrl;
?>
<!DOCTYPE html>
<html lang="zh-cmn-Hans">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
<title>不单卖</title>
<link rel="stylesheet" href="<?php echo YUrl::assets('css', '/wx/common.css'); ?>" />
<link rel="stylesheet" href="<?php echo YUrl::assets('css', '/wx/lottery.css'); ?>" />
</head>
<body>

	<article class="docBody clearfix newBetPage">
		<header id="header">
			<h1>时时彩</h1>
			<a href="javascript:;" cpurl="" class="rightBox" rel="nofollow">往期开奖</a> <a class="goBack" href="javascript:;" cpurl="" target="_self" rel="nofollow">返回</a>
		</header>
		<section id="wraper" style="height: 760px;">
			<div>
				<div class="gameBanner">
					<span class="period">第2016053期</span>
				</div>
				<div class="gameTip clearfix">
					<span class="l_box rockTip" style="">机选</span> <span
						class="r_box">至少选择1个球，猜中1陪25</span>
				</div>
				<div class="betBox">
					<div class="ballCon redBalls">
						<ul class="clearfix">
							<li><span class="js-ball" data-value="1">01</span></li>
							<li><span class="js-ball" data-value="2">02</span></li>
							<li><span class="js-ball" data-value="3">03</span></li>
							<li><span class="js-ball" data-value="4">04</span></li>
							<li><span class="js-ball" data-value="5">05</span></li>
							<li><span class="js-ball" data-value="6">06</span></li>
							<li><span class="js-ball" data-value="7">07</span></li>
							<li><span class="js-ball" data-value="8">08</span></li>
							<li><span class="js-ball" data-value="9">09</span></li>
							<li><span class="js-ball" data-value="10">10</span></li>
							<li><span class="js-ball" data-value="11">11</span></li>
							<li><span class="js-ball" data-value="12">12</span></li>
							<li><span class="js-ball" data-value="13">13</span></li>
							<li><span class="js-ball" data-value="14">14</span></li>
							<li><span class="js-ball" data-value="15">15</span></li>
							<li><span class="js-ball" data-value="16">16</span></li>
							<li><span class="js-ball" data-value="17">17</span></li>
							<li><span class="js-ball" data-value="18">18</span></li>
							<li><span class="js-ball" data-value="19">19</span></li>
							<li><span class="js-ball" data-value="20">20</span></li>
							<li><span class="js-ball" data-value="21">21</span></li>
							<li><span class="js-ball" data-value="22">22</span></li>
							<li><span class="js-ball" data-value="23">23</span></li>
							<li><span class="js-ball" data-value="24">24</span></li>
							<li><span class="js-ball" data-value="25">25</span></li>
							<li><span class="js-ball" data-value="26">26</span></li>
							<li><span class="js-ball" data-value="27">27</span></li>
							<li><span class="js-ball" data-value="28">28</span></li>
							<li><span class="js-ball" data-value="29">29</span></li>
							<li><span class="js-ball" data-value="30">30</span></li>
						</ul>
					</div>
				</div>
			</div>
		</section>
		<section class="betResult" style="display: block;">
			<div class="selectedInfo"></div>
			<div class="cartLink hide" id="cartLink">
				<span>查看购彩车<i></i></span>
			</div>
			<em class="bottomBtn clearNum" id="clearNum">清空</em> <em
				data-bet-type="3" class="bottomBtn randomNum" id="randomNum">机选</em>
			<em data-bet-type="1" class="bottomBtn confirm" id="confirm">立即投注</em>
			<em data-bet-type="5" class="bottomBtn cartBtn" id="cartBtn"><span>加入购彩车</span><i
				class="orgTip hide"></i></em>
			<div id="randomTip" class="randomNumTip hide">
				<i><i></i></i> <a href="javascript:;" data-count="1"><em
					class="tips">1</em>1注</a> <a href="javascript:;" data-count="5"><em
					class="tips">5</em>5注</a> <a href="javascript:;" data-count="10"><em
					class="tips">10</em>10注</a>
			</div>
		</section>
	</article>

</body>
</html>