<?php
use common\YUrl;
require_once (APP_VIEW_PATH . DIRECTORY_SEPARATOR . 'common/header.php');
?>

<div class="subnav">
	<div class="content-menu ib-a blue line-x">
		<a href='javascript:;' class="on"><em>商品列表</em></a>
	</div>
</div>
<style type="text/css">
html {
	_overflow-y: scroll
}
</style>
<div class="pad-lr-10">


	<form name="searchform" action="" method="get">
		<table width="100%" cellspacing="0" class="search-form">
			<tbody>
				<tr>
					<td>
						<div class="explain-col">
							<p style="margin-top: 10px;">
								商家：<select name="shop_id">
									<option value="-1">全部</option>
								</select> 商品分类：<select name="cat_id">
									<option value="-1">全部</option>
								</select> 商品名称：<input type="text"
									value="<?php echo $goods_name; ?>" class="input-text"
									name="shop_name" placeholder="商品名称" /> 商品价格：<input type="text"
									value="<?php echo $start_price; ?>" class="input-text"
									name="start_price" style="width: 50px;" placeholder="" /> ~ <input
									type="text" value="<?php echo $end_price; ?>"
									class="input-text" style="width: 50px;" name="end_price"
									placeholder="" /> 显示已删商品：<select name="is_delete_show">
									<option
										<?php echo $is_delete_show==0 ? 'selected="selected"' : ''; ?>
										value="0">否</option>
									<option
										<?php echo $is_delete_show==1 ? 'selected="selected"' : ''; ?>
										value="1">是</option>
								</select> 上下架：<select name="updown">
									<option <?php echo $updown==-1 ? 'selected="selected"' : ''; ?>
										value="-1">全部</option>
									<option <?php echo $updown==1 ? 'selected="selected"' : ''; ?>
										value="1">上架</option>
									<option <?php echo $updown==0 ? 'selected="selected"' : ''; ?>
										value="0">下架</option>
								</select> <input type="submit" name="search" class="button"
									value="搜索" />
							</p>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</form>


	<form name="myform" id="myform" action="" method="post">
		<div class="table-list">
			<table width="100%" cellspacing="0">
				<thead>
					<tr>
						<th align="center">商品图片</th>
						<th align="center">商品名称</th>
						<th align="center">商家名称</th>
						<th align="center">商品价格</th>
						<th align="center">购买次数</th>
						<th align="center">30天购买次数</th>
						<th align="center">上下架</th>
						<th align="center">状态</th>
						<th width="120" align="center">修改时间</th>
						<th width="120" align="center">创建时间</th>
						<th width="100" align="center">管理操作</th>
					</tr>
				</thead>
				<tbody>
    <?php foreach ($list as $item): ?>
    	<tr>
						<td align="center"><img width="120"
							src="<?php echo $item['goods_img']; ?>" /></td>
						<td align="center"><?php echo $item['goods_name']; ?></td>
						<td align="center"><?php echo $item['shop_name']; ?></td>
						<td align="center"><?php echo "{$item['min_price']}~{$item['max_price']}"; ?></td>
						<td align="center"><?php echo $item['buy_count']; ?></td>
						<td align="center"><?php echo $item['month_buy_count']; ?></td>
						<td align="center"><?php echo $item['marketable'] ? '上架' : '下架'; ?></td>
						<td align="center"><?php
        if ($item['status'] == 1) {
            echo '正常';
        } else if ($item['status'] == 2) {
            echo '已删';
        } else {
            echo '无效';
        }
        ?></td>
						<td align="center"><?php echo date('Y-m-d H:i:s', $item['modified_time']); ?></td>
						<td align="center"><?php echo date('Y-m-d H:i:s', $item['created_time']); ?></td>
						<td align="center"><a href="###"
							onclick="deleteDialog('deleteGoods', '<?php echo YUrl::createBackendUrl('', 'Goods', 'delete', ['goods_id' => $item['goods_id']]); ?>', '<?php echo $item['goods_name'] ?>')"
							title="删除">删除</a></td>
					</tr>
    <?php endforeach; ?>
    </tbody>
			</table>

			<div id="pages">
<?php echo $page_html; ?>
</div>

		</div>

	</form>
</div>

</body>
</html>