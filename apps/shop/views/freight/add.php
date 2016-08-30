<?php
use common\YUrl;
require_once (APP_VIEW_PATH . DIRECTORY_SEPARATOR . 'common/dialog_header.php');
?>

<div class="site-form">
	<form action="<?php echo YUrl::createShopUrl('', 'Freight', 'add'); ?>" method="post" name="myform" id="myform">

		<div class="row">
			<div class="col-1" align="right">
				<div class="label">运费模板名称：</div>
			</div>
			<div class="cc">
				<div class="input">
					<input type="text" style="width:200px;" class="form-input" name="freight_name" value="">
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-1" align="right">
				<div class="label">运费承担：</div>
			</div>
			<div class="cc">
				<div class="input">
					<select name="bear_freight" id="bear_freight" class="slct">
						<option value="1">卖家承担运费</option>
						<option value="2">自定义运费</option>
					</select>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-1" align="right">
				<div class="label">发货时间：</div>
			</div>
			<div class="cc">
				<div class="input">
					<select name="send_time" id="send_time" class="slct">
						<option value="4">4小时内</option>
						<option value="12">12小时内</option>
						<option value="24">24小时内</option>
						<option value="48">2天内</option>
						<option value="72">3天内</option>
						<option value="168">一周内</option>
					</select>
				</div>
			</div>
		</div>
		
		<div class="row">
			<div class="col-1" align="right">
				<div class="label">包邮金额：</div>
			</div>
			<div class="cc">
				<div class="input">
					<input type="text" style="width:50px;" class="form-input" name="baoyou_fee" value="0">（0代表不包邮，100代表满100包邮）
				</div>
			</div>
		</div>
		
		<div class="row">
			<div class="col-1" align="right">
				<div class="label">计费类型：</div>
			</div>
			<div class="cc">
				<div class="input">
					<select name="fright_type" id="fright_type" class="slct">
						<option value="1">按件数</option>
						<option value="2">按重量</option>
					</select>
				</div>
			</div>
		</div>
		
		<div class="row">
			<div class="col-1" align="right">
				<div class="label">计费规则：</div>
			</div>
			<div class="cc">
				<div class="input">
					<input type="text" style="width:30px;" class="form-input" name="base_step" value="1" /> 件内，
					<input type="text" style="width:30px;" class="form-input" name="base_freight" value="" /> 元，
					每增加 <input type="text" style="width:30px;" class="form-input" name="rate_step" value="1" /> 件，
					增加运费 <input type="text" style="width:30px;" class="form-input" name="step_freight" value="" /> 元
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-1" align="right">
				<div class="label">计费规则：</div>
			</div>
			<div class="cc">
				<div class="input">
					<input type="text" style="width:50px;" class="form-input" name="base_step" value="1000" /> g内，
					<input type="text" style="width:30px;" class="form-input" name="base_freight" value="" /> 元，
					每增加 <input type="text" style="width:50px;" class="form-input" name="rate_step" value="1000" /> g，
					增加运费 <input type="text" style="width:30px;" class="form-input" name="step_freight" value="" /> 元
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-1" align="right">
				<div class="label">不包邮地区：</div>
			</div>
			<div class="cc">
				<div class="input">
					<input type="text" style="width:50px;" class="form-input" name="no_area" value="">
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-2" align="right">
				<div class="label">&nbsp;</div>
			</div>
			<div class="col-8">
				<div class="ctrl">
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