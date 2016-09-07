<?php
use common\YUrl;
require_once (APP_VIEW_PATH . DIRECTORY_SEPARATOR . 'common/header.php');
?>
<div class="main" id="main">
	<div class="w cc">
			<?php
require_once (APP_VIEW_PATH . DIRECTORY_SEPARATOR . 'common/left_menu.php');
?>
		<div class="container">
			<div class="site-crumb">
				<a href="/">首页</a> <span class="arrow">></span> <a href="">店铺管理</a><span
					class="arrow"> > </span> <a href="">店铺设置</a>
			</div>

			<div class="m-t-20">
				<div class="site-form">
					<form
						action="<?php echo YUrl::createShopUrl('', 'Shop', 'baseinfo'); ?>"
						method="post" autocomplete="off" id="form">
						<div class="row">
							<div class="col-2" align="right">
								<div class="label">商家LOGO：</div>
							</div>
							<div class="col-8 cc">
								<div class="input">
									<input type="hidden" name="shop_logo"
										value="<?php echo $detail['shop_logo']; ?>" />
								</div>
								<div class="tips"></div>
							</div>
						</div>

						<div class="row">
							<div class="col-2" align="right">
								<div class="label">商家名称：</div>
							</div>
							<div class="col-8 cc">
								<div class="input">
									<input type="text" class="form-input" style="width: 200px;"
										name="shop_name"
										value="<?php echo htmlspecialchars($detail['shop_name']); ?>">
								</div>
								<div class="tips"></div>
							</div>
						</div>

						<div class="row">
							<div class="col-2" align="right">
								<div class="label">联系人：</div>
							</div>
							<div class="col-8 cc">
								<div class="input">
									<input type="text" class="form-input" style="width: 200px;"
										name="link_man"
										value="<?php echo htmlspecialchars($detail['link_man']); ?>">
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-2" align="right">
								<div class="label">联系手机：</div>
							</div>
							<div class="col-8 cc">
								<div class="input">
									<input type="text" class="form-input" style="width: 200px;"
										name="mobilephone"
										value="<?php echo $detail['mobilephone']; ?>">
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-2" align="right">
								<div class="label">联系座机：</div>
							</div>
							<div class="col-8 cc">
								<div class="input">
									<input type="text" class="form-input" style="width: 200px;"
										name="telephone" value="<?php echo $detail['telephone']; ?>">
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-2" align="right">
								<div class="label">联系QQ：</div>
							</div>
							<div class="col-8 cc">
								<div class="input">
									<input type="text" class="form-input" style="width: 200px;"
										name="qq" value="<?php echo $detail['qq']; ?>">
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-2" align="right">
								<div class="label">商家公告：</div>
							</div>
							<div class="col-8 cc">
								<div class="input">
									<textarea rows="3" cols="50" name="shop_notice"
										class="form-input" style="width: 300px; height: 80px;"><?php echo htmlspecialchars($detail['shop_notice']); ?></textarea>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-2" align="right">
								<div class="label">允许发布商品数量：</div>
							</div>
							<div class="col-8 cc">
								<div class="input"><?php echo $detail['max_goods_count']; ?></div>
							</div>
						</div>

						<div class="row">
							<div class="col-2" align="right">
								<div class="label">商家是否冻洁：</div>
							</div>
							<div class="col-8 cc">
								<div class="input"><?php echo ($detail['is_lock']==1) ? '是' : '否'; ?></div>
							</div>
						</div>

						<div class="row">
							<div class="col-2" align="right">
								<div class="label">开放删评：</div>
							</div>
							<div class="col-8 cc">
								<div class="input"><?php echo ($detail['is_allow_delete_comment']==1) ? '是' : '否'; ?></div>
							</div>
						</div>

						<div class="row">
							<div class="col-2" align="right">
								<div class="label">最后修改时间：</div>
							</div>
							<div class="col-8 cc">
								<div class="input"><?php echo $detail['modified_time']; ?></div>
							</div>
						</div>

						<div class="row">
							<div class="col-2" align="right">
								<div class="label">创建时间：</div>
							</div>
							<div class="col-8 cc">
								<div class="input"><?php echo $detail['created_time']; ?></div>
							</div>
						</div>

						<div class="row">
							<div class="col-2" align="right">
								<div class="label">&nbsp;</div>
							</div>
							<div class="col-8">
								<div class="ctrl">
									<input type="button" id="form_submit" value="保存"
										class="form-submit">
								</div>
							</div>
						</div>

					</form>
				</div>
			</div>

			<div class="m-t-50"></div>
		</div>
	</div>
</div>

<script type="text/javascript">
<!--

$(document).ready(function(){
	$('#form_submit').click(function(){
	    $.ajax({
	    	type: 'post',
            url: $('form').eq(0).attr('action'),
            dataType: 'json',
            data: $('form').eq(0).serialize(),
            success: function(data) {
                if (data.errcode == 0) {
                	dialogTips(data.errmsg, 3);
                } else {
                	dialogTips(data.errmsg, 3);
                }
            }
	    });
	});
});

//-->
</script>

<?php
require_once (APP_VIEW_PATH . DIRECTORY_SEPARATOR . 'common/footer.php');
?>