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
				<a href="/">首页</a> <span class="arrow">></span> <a href="">商品管理</a>
				<span class="arrow"> > </span> <a href="">自定义商品分类</a>
				<button style="float:right;" onClick="postDialog('addFreight', '<?php echo YUrl::createShopUrl('', 'Category', 'add'); ?>', '添加分类', 350, 200);" class="normal_btn">添加分类</button>
			</div>

			<div class="site-filter-bar m-t-20">
				<div class="bar">
					<dl>
						<dt class="col-1 txt-c">排序</dt>
						<dd class="col-8" style="text-align: left;">分类名称</dd>
						<dd class="col-1">操作</dd>
					</dl>
				</div>
			</div>
			<div class="site-list comment-list" id="goods-list">

				<?php foreach ($list as $cat): ?>
				<div class="list-item">
					<div class="detail cc">
						<div class="col-1 txt-c">
							<input type="text" name="listsorts[<?php echo $cat['cat_id']; ?>]" value="<?php echo $cat['listorder']; ?>" style="width:30px;text-align:center;" />
						</div>
						<div class="col-8"><span class="label"><?php echo htmlspecialchars($cat['cat_name']); ?></span></div>
						<div class="col-1 txt-c">
							<p class="ctrl">
								<a href="###" onClick="edit(<?php echo $cat['cat_id'] ?>, '<?php echo htmlspecialchars($cat['cat_name']); ?>')">[编辑]</a>
								<a href="###" onclick="deleteDialog('deleteCategory', '<?php echo YUrl::createShopUrl('', 'Category', 'delete', ['cat_id' => $cat['cat_id']]); ?>', '<?php echo htmlspecialchars($cat['cat_name']); ?>')" title="删除">[删除]</a>
							</p>
						</div>
					</div>
				</div>
				<?php endforeach; ?>

			</div>

			<div class="m-t-50"></div>
		</div>
	</div>
</div>

<script type="text/javascript">
function edit(id, name) {
	var title = '修改『' + name + '』';
	var page_url = "<?php echo YUrl::createShopUrl('', 'Category', 'edit'); ?>?cat_id="+id;
	postDialog('editCategory', page_url, title, 350, 200);
}
</script>

<?php
require_once (APP_VIEW_PATH . DIRECTORY_SEPARATOR . 'common/footer.php');
?>