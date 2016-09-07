<?php
use common\YUrl;
require_once (APP_VIEW_PATH . DIRECTORY_SEPARATOR . 'common/dialog_header.php');
?>

<div class="site-form">
	<form
		action="<?php echo YUrl::createShopUrl('', 'Order', 'deliver'); ?>"
		method="post" name="myform" id="myform">

		<div class="row">
			<div class="col-1" align="right">
				<div class="label">快递厂商：</div>
			</div>
			<div class="cc">
				<div class="input">
					<fieldset id="custom_data">
						<select name="logistics_code" class="first slct">
    		            	<?php foreach ($logistics_list_dict as $code => $name): ?>
    		            	<option
								<?php echo ($logistics_code==$code) ? 'selected="selected"' : ''; ?>
								value="<?php echo $code; ?>"><?php echo $name; ?></option>
    		            	<?php endforeach; ?>
    		            </select>
					</fieldset>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-1" align="right">
				<div class="label">快递单号：</div>
			</div>
			<div class="cc">
				<div class="input">
					<input type="text" style="width: 200px;" class="form-input"
						name="logistics_number" value="<?php echo $logistics_number; ?>">
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-1" align="right">
				<div class="label">&nbsp;</div>
			</div>
			<div class="col-8">
				<div class="ctrl">
					<input name="order_id" type="hidden"
						value="<?php echo $order_id; ?>" /> <input id="form_submit"
						type="button" value="保存" class="form-submit">
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