<?php
require_once(APP_VIEW_PATH . DIRECTORY_SEPARATOR . 'common/header.php');
?>
	<div class="main" id="main">
		<div class="w cc">
			<?php
			require_once(APP_VIEW_PATH . DIRECTORY_SEPARATOR . 'common/left_menu.php');
			?>
			<div class="container">
				<div class="site-crumb">
					<a href="/">首页</a> <span class="arrow">></span> <a href="">账户管理</a> <span class="arrow"> > </span> <a href="">账户安全</a>
				</div>
				<div class="site-filter-form cc">
					<form action="">
						<dl>
							<dt>商品名称：</dt>
							<dd>
								<input type="text" name="goods_name" class="i-txt" size="40"
									placeholder="商品名称" />
							</dd>
						</dl>
						<dl class="ctrl">
							<dt>
								<input type="submit" value="搜 索" class="i-sbt" />
							</dt>
						</dl>
					</form>
				</div>
				<div class="site-filter-bar m-t-20">
					<div class="tags">
						<ul class="cc">
							<li class="active" data-map="{comment_status:0,page:1}">全部评价</li>
							<li data-map="{comment_status:1,page:1}">未回复</li>
						</ul>
					</div>
					<div class="bar">
						<dl>
							<dt class="col-1">
								<label><input type="checkbox" class="select-all"
									style="vertical-align: middle" /> 全选</label>
							</dt>
							<dd class="col-3 txt-l">
								<a action-do="show" class="btn">前台显示</a> <a action-do="hide"
									class="btn">前台屏蔽</a>
							</dd>
							<dd class="col-2">购买商品</dd>
							<dd class="col-1">时间</dd>
							<dd class="col-1">状态</dd>
							<dd class="col-1">操作</dd>
						</dl>
					</div>
				</div>
				<div class="site-list comment-list" id="comment-list"></div>
				<div class="m-t-30">
				</div>
				<div class="m-t-50"></div>
			</div>
		</div>
	</div>
<?php
require_once(APP_VIEW_PATH . DIRECTORY_SEPARATOR . 'common/footer.php');
?>