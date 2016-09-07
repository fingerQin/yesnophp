<?php
use common\YUrl;
require_once (APP_VIEW_PATH . DIRECTORY_SEPARATOR . 'common/dialog_header.php');
?>

<div class="site-form">
	<form
		action="<?php echo YUrl::createShopUrl('', 'Order', 'adjustAddress'); ?>"
		method="post" name="myform" id="myform">

		<div class="row">
			<div class="col-1" align="right">
				<div class="label">省市区：</div>
			</div>
			<div class="cc">
				<div class="input">
					<fieldset id="custom_data">
						<select class="first slct"
							<?php echo $province_id ? "data-value='{$province_id}'" : ''; ?>></select>
						<select class="second slct"
							<?php echo $city_id ? "data-value='{$city_id}'" : ''; ?>></select>
						<select name="district_id" class="third slct"
							<?php echo $district_id ? "data-value='{$district_id}'" : ''; ?>></select>
					</fieldset>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-1" align="right">
				<div class="label">收货人姓名：</div>
			</div>
			<div class="cc">
				<div class="input">
					<input type="text" style="width: 200px;" class="form-input"
						name="receiver_name"
						value="<?php echo htmlspecialchars($receiver_name); ?>">
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-1" align="right">
				<div class="label">收货人手机</div>
			</div>
			<div class="cc">
				<div class="input">
					<input type="text" style="width: 200px;" class="form-input"
						name="receiver_mobile"
						value="<?php echo htmlspecialchars($receiver_mobile); ?>">
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-1" align="right">
				<div class="label">邮编</div>
			</div>
			<div class="cc">
				<div class="input">
					<input type="text" style="width: 100px;" class="form-input"
						name="receiver_zip"
						value="<?php echo htmlspecialchars($receiver_zip); ?>">
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-1" align="right">
				<div class="label">详细地址</div>
			</div>
			<div class="cc">
				<div class="input">
					<input type="text" style="width: 350px;" class="form-input"
						name="receiver_address"
						value="<?php echo htmlspecialchars($receiver_address); ?>">
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-1" align="right">
				<div class="label">&nbsp;</div>
			</div>
			<div class="col-8">
				<div class="ctrl">
					<input type="hidden" name="order_id"
						value="<?php echo $order_id; ?>" /> <input id="form_submit"
						type="button" value="保存" class="form-submit">
				</div>
			</div>
		</div>

	</form>
</div>

<script type="text/javascript"
	src="<?php echo YUrl::assets('js', '/jquery.cxselect.min.js'); ?>"></script>
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

function district_all(response){
	$('#custom_data').cxSelect({
		  selects: ['first', 'second', 'third'],
		  required: true,
		  jsonValue: 'id',
		  jsonName: 'name',
		  jsonSub: 'sub',
		  data : response
	});
}
$.getScript("<?php echo YUrl::assets('js', 'district.js'); ?>");

//-->
</script>

<?php
require_once (APP_VIEW_PATH . DIRECTORY_SEPARATOR . 'common/dialog_footer.php');
?>