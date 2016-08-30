<?php
use common\YUrl;
require_once (APP_VIEW_PATH . DIRECTORY_SEPARATOR . 'common/dialog_header.php');
?>

<div class="site-form">
	<form action="<?php echo YUrl::createShopUrl('', 'Order', 'adjustPrice'); ?>" method="post" name="myform" id="myform">

		<div class="row">
			<div class="col-1" align="right">
				<div class="label">原价：</div>
			</div>
			<div class="cc">
				<div class="input"><?php echo $old_price; ?> 元</div>
			</div>
		</div>

		<div class="row">
			<div class="col-1" align="right">
				<div class="label">调整价格：</div>
			</div>
			<div class="cc">
				<div class="input">
					<input type="text" style="width:100px;" class="form-input" name="price" value="">
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-1" align="right">
				<div class="label">&nbsp;</div>
			</div>
			<div class="col-8">
				<div class="ctrl">
					<input name="order_id" type="hidden" value="<?php echo $order_id; ?>" />
					<input name="product_id" type="hidden" value="<?php echo $product_id; ?>" />
					<input id="form_submit" type="button" value="保存" class="form-submit">
				</div>
			</div>
		</div>

	</form>
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
                	top.dialog.getCurrent().close({"refresh" : 1});
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
require_once (APP_VIEW_PATH . DIRECTORY_SEPARATOR . 'common/dialog_footer.php');
?>