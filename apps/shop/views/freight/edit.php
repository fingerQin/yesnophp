<?php
use common\YUrl;
require_once (APP_VIEW_PATH . DIRECTORY_SEPARATOR . 'common/dialog_header.php');
?>

<div class="site-form">
	<form
		action="<?php echo YUrl::createShopUrl('', 'Freight', 'edit'); ?>"
		method="post" name="myform" id="myform">

		<div class="row">
			<div class="col-1" align="right">
				<div class="label">运费模板名称：</div>
			</div>
			<div class="cc">
				<div class="input">
					<input type="text" style="width: 200px;" class="form-input"
						name="freight_name"
						value="<?php echo htmlspecialchars($detail['freight_name']); ?>">
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
						<option
							<?php echo ($detail['bear_freight']==1) ? 'selected="selected"' : ''; ?>
							value="1">卖家承担运费</option>
						<option
							<?php echo ($detail['bear_freight']==2) ? 'selected="selected"' : ''; ?>
							value="2">自定义运费</option>
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
						<option
							<?php echo ($detail['send_time']==4) ? 'selected="selected"' : ''; ?>
							value="4">4小时内</option>
						<option
							<?php echo ($detail['send_time']==12) ? 'selected="selected"' : ''; ?>
							value="12">12小时内</option>
						<option
							<?php echo ($detail['send_time']==24) ? 'selected="selected"' : ''; ?>
							value="24">24小时内</option>
						<option
							<?php echo ($detail['send_time']==48) ? 'selected="selected"' : ''; ?>
							value="48">2天内</option>
						<option
							<?php echo ($detail['send_time']==72) ? 'selected="selected"' : ''; ?>
							value="72">3天内</option>
						<option
							<?php echo ($detail['send_time']==168) ? 'selected="selected"' : ''; ?>
							value="168">一周内</option>
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
					<input type="text" style="width: 50px;" class="form-input"
						name="baoyou_fee" value="<?php echo $detail['baoyou_fee']; ?>">（0代表不包邮，100代表满100包邮）
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
						<option
							<?php echo ($detail['fright_type']==1) ? 'selected="selected"' : ''; ?>
							value="1">按件数</option>
						<option
							<?php echo ($detail['fright_type']==2) ? 'selected="selected"' : ''; ?>
							value="2">按重量</option>
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
					<input type="text" style="width: 30px;" class="form-input"
						name="base_step" value="<?php echo $detail['base_step']; ?>" />
					件内， <input type="text" style="width: 30px;" class="form-input"
						name="base_freight" value="<?php echo $detail['base_freight']; ?>" />
					元， 每增加 <input type="text" style="width: 30px;" class="form-input"
						name="rate_step" value="<?php echo $detail['rate_step']; ?>" /> 件，
					增加运费 <input type="text" style="width: 30px;" class="form-input"
						name="step_freight" value="<?php echo $detail['step_freight']; ?>" />
					元
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-1" align="right">
				<div class="label">计费规则：</div>
			</div>
			<div class="cc">
				<div class="input">
					<input type="text" style="width: 50px;" class="form-input"
						name="base_step" value="<?php echo $detail['base_step']; ?>" />
					g内， <input type="text" style="width: 30px;" class="form-input"
						name="base_freight" value="<?php echo $detail['base_freight']; ?>" />
					元， 每增加 <input type="text" style="width: 50px;" class="form-input"
						name="rate_step" value="<?php echo $detail['rate_step']; ?>" /> g，
					增加运费 <input type="text" style="width: 30px;" class="form-input"
						name="step_freight" value="<?php echo $detail['step_freight']; ?>" />
					元
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-1" align="right">
				<div class="label">不包邮地区：</div>
			</div>
			<div class="cc">
				<div class="input">
					<input type="text" style="width: 50px;" class="form-input"
						name="no_area" value="">
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-2" align="right">
				<div class="label">&nbsp;</div>
			</div>
			<div class="col-8">
				<div class="ctrl">
					<input name="tpl_id" type="hidden"
						value="<?php echo $detail['tpl_id']; ?>" /> <input
						id="form_submit" type="button" value="保存" class="form-submit">
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