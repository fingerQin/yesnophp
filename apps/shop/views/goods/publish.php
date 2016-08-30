<?php
use common\YUrl;
use common\YCore;
require_once (APP_VIEW_PATH . DIRECTORY_SEPARATOR . 'common/header.php');
?>
<div class="main" id="main">
	<div class="w cc">
<?php
require_once (APP_VIEW_PATH . DIRECTORY_SEPARATOR . 'common/left_menu.php');
?>

			<div class="container">
			<div class="site-crumb">
				<a href="/">首页</a> <span class="arrow">></span> <a href="">商品管理</a>
				<span class="arrow"> > </span> <a href="">发布商品</a>
			</div>

			<div class="goods-publish-form site-form">
				<form method="post"
					action="<?php echo YUrl::createShopUrl('', 'Goods', 'publish'); ?>"
					autocomplete="off">
					<div class="row">
						<div class="col-1" align="right">
							<div class="label">系统分类：</div>
						</div>
						<div class="col-9">
							<fieldset id="custom_data">
					            <select class="first slct"></select>
					            <select class="second slct"></select>
					            <select name="cat_id" class="third slct"></select>
						     </fieldset>
						</div>
					</div>
					<div class="row">
						<div class="col-1" align="right">
							<div class="label">自定义类目：</div>
						</div>
						<div class="col-9 cc">
							<div class="input">
								<select class="slct" name="custom_cat_id">
									<option value="-1">请选择</option>
    								<?php foreach ($custom_goods_cat_list as $cat): ?>
    								<option value="<?php echo $cat['cat_id']; ?>"><?php echo $cat['cat_name']; ?></option>
    								<?php endforeach; ?>
    							</select>
							</div>
							<div class="tips">
								点击新增商品自定义分类[<a href="###" onClick="alert('暂时未实现');">创建</a>]
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-1" align="right">
							<div class="label">
								<span class="red required">*</span> 商品标题：
							</div>
						</div>
						<div class="col-9 cc">
							<div class="input">
								<input type="text" placeholder="商品商品名称,长度控制在20位"
									class="form-input" size="60" name="goods_name" value="">
							</div>
							<div class="tips"></div>
						</div>
					</div>
					<div class="row">
						<div class="col-1" align="right">
							<div class="label">商品推广语：</div>
						</div>
						<div class="col-9">
							<div class="input">
								<textarea name="slogan" cols="80" rows="3" class="form-textarea"></textarea>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-1" align="right">
							<div class="label">
								<span class="red required">*</span> 商品排序：
							</div>
						</div>
						<div class="col-2">
							<div class="input">
								<input type="text" name="listorder" class="form-input"
									placeholder="值越大越靠前" size="18" value="0">
							</div>
						</div>
						<div class="col-1" align="right">
							<div class="label">单位重量：</div>
						</div>
						<div class="col-2 cc">
							<div class="input">
								<input type="text" name="weight" class="form-input"
									placeholder="如：10" style="width: 120px;" value="0">
							</div>
							<div class="tips">单位（g）</div>
						</div>
					</div>
					<div class="row">
						<div class="col-1" align="right">
							<div class="label">商品图片：</div>
						</div>
						<div class="col-9">
							<div class="upload-pics">
								<dl>
									<dt>
										<div class="box">
											800px*800px <br>以上的图片,可以在商品详情面提供图片放大功能
										</div>
										<div class="preview">
											<ul>
												<li class="active"></li>
												<li></li>
												<li></li>
												<li></li>
												<li></li>
											</ul>
										</div>
									</dt>
									<dd>
										<input type="hidden" name="goods_image_1" id="goods_image_1"
											value="" />
										<div id="goods_view_1"></div>
									</dd>
									<dd>
										<input type="hidden" name="goods_image_2" id="goods_image_2"
											value="" />
										<div id="goods_view_2"></div>
									</dd>
									<dd>
										<input type="hidden" name="goods_image_3" id="goods_image_3"
											value="" />
										<div id="goods_view_3"></div>
									</dd>
									<dd>
										<input type="hidden" name="goods_image_4" id="goods_image_4"
											value="" />
										<div id="goods_view_4"></div>
									</dd>
									<dd>
										<input type="hidden" name="goods_image_5" id="goods_image_5"
											value="" />
										<div id="goods_view_5"></div>
									</dd>
								</dl>
								<p class="upload-tips">图片至少上传1张,图片大小不能超过500K,图片格式支持jpg,png</p>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-1" align="right">
							<div class="label">商品属性：</div>
						</div>
						<div class="col-9">
							<div class="inline-form" id="attr-form">
								<p class="inline-form-tips">请先选择商品分类</p>
								<input type="hidden" name="goods_attr">
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-1" align="right">
							<div class="label">商品详情：</div>
						</div>
						<div class="col-9">
							<div class="wap-content">
								<textarea cols="120" rows="10" name="description_wap"></textarea>
								<p class="red">注：商品描述区——推荐宽度为720px</p>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-1" align="right">
							<div class="label">&nbsp;</div>
						</div>
						<div class="col-9">
							<div class="ctrl">
								<input type="button" id="form_submit" value="保存商品"
									class="form-submit">
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript" src="<?php echo YUrl::assets('js', '/jquery.cxselect.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo YUrl::assets('js', '/AjaxUploader/SimpleAjaxUploader.min.js'); ?>"></script>
<script src="<?php echo YUrl::assets('js', '/AjaxUploader/ShopCenterUploadImage.js'); ?>"></script>
<script type="text/javascript">

var uploadUrl = '<?php echo YUrl::createShopUrl('', 'Index', 'upload'); ?>';
var baseJsUrl = '<?php echo YUrl::assets('js', ''); ?>';
var filUrl = '<?php echo YCore::config('files_domain_name'); ?>';
uploadImage(filUrl, baseJsUrl, 'goods_view_1', 'goods_image_1', 120, 120, uploadUrl);
uploadImage(filUrl, baseJsUrl, 'goods_view_2', 'goods_image_2', 120, 120, uploadUrl);
uploadImage(filUrl, baseJsUrl, 'goods_view_3', 'goods_image_3', 120, 120, uploadUrl);
uploadImage(filUrl, baseJsUrl, 'goods_view_4', 'goods_image_4', 120, 120, uploadUrl);
uploadImage(filUrl, baseJsUrl, 'goods_view_5', 'goods_image_5', 120, 120, uploadUrl);

$(function(){
	$('#form_submit').click(function(){
	    $.ajax({
	    	type: 'post',
            url: $('form').eq(0).attr('action'),
            dataType: 'json',
            data: $('form').eq(0).serialize(),
            success: function(data) {
                if (data.errcode == 0) {
                	alert("添加成功");
                } else {
                	alert('添加失败');
                }
            }
	    });
	});
});

$('#custom_data').cxSelect({
  selects: ['first', 'second', 'third'],
  required: true,
  jsonValue: 'cat_id',
  jsonName: 'cat_name',
  jsonSub: 'sub',
  data: <?php echo json_encode($system_goods_cat_list); ?>
});
</script>

<?php
require_once (APP_VIEW_PATH . DIRECTORY_SEPARATOR . 'common/footer.php');
?>