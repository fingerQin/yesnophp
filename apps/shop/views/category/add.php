<?php
use common\YUrl;
require_once (APP_VIEW_PATH . DIRECTORY_SEPARATOR . 'common/dialog_header.php');
?>

<div class="site-form">
	<form action="<?php echo YUrl::createShopUrl('', 'Category', 'add'); ?>" method="post" name="myform" id="myform">

		<div class="row">
			<div class="col-1" align="right">
				<div class="label">分类名称：</div>
			</div>
			<div class="cc">
				<div class="input">
					<input type="text" style="width:200px;" class="form-input" name="cat_name" value="">
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-1" align="right">
				<div class="label">排序值：</div>
			</div>
			<div class="cc">
				<div class="input">
					<input type="text" style="width:50px;" class="form-input" name="listorder" value="0"> (从小到大排序)
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-1" align="right">
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