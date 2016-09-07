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
				<a href="/">首页</a> <span class="arrow">></span> <a href="">交易管理</a><span
					class="arrow"> > </span> <a href="">订单管理</a>
			</div>

			<!-- 搜索条件 start -->
			<div class="site-filter-form cc">
				<form>
					<dl>
						<dt>商品名称：</dt>
						<dd>
							<input type="text" class="i-txt" size="40" name="goods_name"
								value="<?php echo htmlspecialchars($goods_name); ?>"
								placeholder="商品名称">
						</dd>
					</dl>
					<dl>
						<dt>收货人姓名：</dt>
						<dd>
							<input type="text" class="i-txt" size="40" name="receiver_name"
								value="<?php echo htmlspecialchars($receiver_name); ?>"
								placeholder="收货人姓名">
						</dd>
					</dl>
					<dl>
						<dt>收货人手机号：</dt>
						<dd>
							<input type="text" class="i-txt" size="40" name="receiver_mobile"
								value="<?php echo htmlspecialchars($receiver_mobile); ?>"
								placeholder="收货人手机号">
						</dd>
					</dl>
					<dl>
						<dt>订单号：</dt>
						<dd>
							<input type="text" class="i-txt" size="40" name="order_sn"
								value="<?php echo htmlspecialchars($order_sn); ?>"
								placeholder="订单号">
						</dd>
					</dl>
					<dl class="ctrl clear">
						<dt>&nbsp;</dt>
						<dd>
							<input type="submit" value="搜 索" class="i-sbt">
						</dd>
					</dl>
				</form>
			</div>
			<!-- 搜索条件 end -->

			<div class="site-filter-bar m-t-20">
				<div class="tags">
					<ul class="cc">
						<li <?php echo ($order_status==-1) ? 'class="active"' : ''; ?>
							onClick="chanageStatus(-1);">全部订单</li>
						<li <?php echo ($order_status==0) ? 'class="active"' : ''; ?>
							onClick="chanageStatus(0);">待付款订单</li>
						<li <?php echo ($order_status==1) ? 'class="active"' : ''; ?>
							onClick="chanageStatus(1);">待发货订单</li>
						<li <?php echo ($order_status==2) ? 'class="active"' : ''; ?>
							onClick="chanageStatus(2);">待收货订单</li>
						<li <?php echo ($order_status==3) ? 'class="active"' : ''; ?>
							onClick="chanageStatus(3);">交易成功</li>
						<li <?php echo ($order_status==4) ? 'class="active"' : ''; ?>
							onClick="chanageStatus(4);">交易关闭</li>
						<li <?php echo ($order_status==5) ? 'class="active"' : ''; ?>
							onClick="chanageStatus(5);">交易取消</li>
						<!-- <li <?php echo ($order_status==6) ? 'class="active"' : ''; ?> onClick="chanageStatus(6);">退款/售后</li> -->
					</ul>
				</div>
				<div class="bar">
					<dl>
						<dt class="col-6" style="text-align: center;">
							<span>商品图片/名称</span>
						</dt>
						<dd class="col-1"></dd>
						<dd class="col-1">数量</dd>
						<dd class="col-1">单价</dd>
						<dd class="col-1">总额</dd>
						<dd class="col-1">交易状态</dd>
					</dl>
				</div>
			</div>
			<div class="site-list comment-list" id="goods-list">

				<?php foreach ($list as $order): ?>
				<div class="list-item">
					<div class="base">
						<div class="cc">
							<p class="info"> 
			                	<?php
        switch ($order['order_status']) {
            case 0 :
                echo '[待支付]';
                break;
            case 1 :
                echo '[已支付]';
                break;
            case 2 :
                echo '[已发货]';
                break;
            case 3 :
                echo '[已收货]';
                break;
            case 4 :
                echo '[已关闭]';
                break;
            case 5 :
                echo '[已取消]';
                break;
        }
        ?>
			                	<span class="arrow">|</span> 
			                	订单号：<?php echo $order['order_sn']; ?> <span
									class="arrow">|</span> 
			                	下单时间：<?php echo date('Y-m-d H:i:s', $order['created_time']); ?> <span
									class="arrow">|</span> <a href="###"
									onclick="viewBuyerMessage('<?php echo htmlspecialchars($order['buyer_message']); ?>')">[查看买家留言]</a>
							</p>
							<div style="float: right; padding-right: 10px;">
			                	<?php
        switch ($order['order_status']) {
            case 0 : // 未支付。
                ?>
			                			<button type="button" class="normal_btn"
									style="width: 80px;"
									onclick="normalDialog('closeOrder', '<?php echo YUrl::createShopUrl('', 'Order', 'close', ['order_id' => $order['order_id']]); ?>', '您确定要关闭该订单吗？')">关闭订单</button>
								<button type="button" class="normal_btn" style="width: 80px;"
									onclick="adjustAddress(<?php echo $order['order_id']; ?>);">修正地址</button>
			                			<?php
                break;
            case 1 : // 已支付。
                ?>
			                			<button type="button" class="normal_btn"
									style="width: 80px;"
									onclick="adjustAddress(<?php echo $order['order_id']; ?>);">修正地址</button>
								<button type="button" class="normal_btn" style="width: 50px;"
									onclick="deliver(<?php echo $order['order_id']; ?>);">发货</button>
			                			<?php
                break;
            case 2 : // 已发货。
                ?>
                						<button type="button" class="normal_btn"
									style="width: 50px;"
									onclick="deliver(<?php echo $order['order_id']; ?>);">发货</button>
                						<?php
                break;
        }
        ?>
			                </div>
						</div>
					</div>
					<div class="base">
						<div class="cc">
							<p class="info"> 
			                	实付金额：<?php echo $order['payment_price']; ?> <span
									class="arrow">|</span> 
			                	收货人：<?php echo $order['receiver_name']; ?>  <span
									class="arrow">|</span> 
			                	收货人手机：<?php echo $order['receiver_mobile']; ?> <span
									class="arrow">|</span> 
			                	邮编：<?php echo $order['receiver_zip']; ?> <span
									class="arrow">|</span> 
			                	收货地址：<?php echo "{$order['receiver_province']}{$order['receiver_city']}{$order['receiver_district']}{$order['receiver_street']}{{$order['receiver_address']}"; ?>
			                </p>
						</div>
					</div>
					<?php foreach ($order['goods_list'] as $goods): ?>
					<div class="detail cc">
						<div class="col-5">
							<div class="thumb">
								<img src="<?php echo $goods['goods_image']; ?>"
									alt="<?php echo htmlspecialchars($goods['goods_name']); ?>">
							</div>
							<div class="info">
								<strong><?php echo htmlspecialchars($goods['goods_name']); ?></strong>
								<p class="gray-9 m-t-10"><?php echo htmlspecialchars($goods['spec_val']); ?></p>
							</div>
						</div>
						<div class="col-1">
							<p class="label"></p>
						</div>
						<div class="col-1">
							<p class="label">
								<?php echo $goods['quantity']; ?> <i class="edit"
									action-do="editLocalStock" data-goods-id="1"></i>
							</p>
						</div>
						<div class="col-1">
							<p class="label">
								<strong class="red"><?php echo $goods['sales_price']; ?></strong>
							</p>
						</div>
						<div class="col-1">
							<p class="label">
								<strong class="red"><?php echo $goods['payment_price']; ?></strong>
							</p>
						</div>
						<div class="col-1">
							<p class="label">
							<?php if ($order['order_status']==0): ?>
								<a href="###"
									onclick="adjustPrice(<?php echo $order['order_id']; ?>, <?php echo $goods['product_id']; ?>, <?php echo $goods['sales_price']; ?>)">[修改价格]</a>
							<?php else: ?>
								-
							<?php endif; ?>
							</p>
						</div>
					</div>
					<?php endforeach; ?>
				</div>
				<?php endforeach; ?>
			</div>

			<div class="m-t-30">
				<div class="site-page">
					<?php echo $page_html; ?>
				</div>
			</div>
			<div class="m-t-50"></div>
		</div>
	</div>
</div>

<script type="text/javascript">
function adjustAddress(id) {
	var title = '修改订单地址';
	var page_url = "<?php echo YUrl::createShopUrl('', 'Order', 'adjustAddress'); ?>?order_id="+id;
	postDialog('adjustAddress', page_url, title, 520, 300);
}

function deliver(id) {
	var title = '发货';
	var page_url = "<?php echo YUrl::createShopUrl('', 'Order', 'deliver'); ?>?order_id="+id;
	postDialog('deliver', page_url, title, 380, 180);
}

function adjustPrice(order_id, product_id, old_price) {
	var title = '修改价格';
	var page_url = "<?php echo YUrl::createShopUrl('', 'Order', 'adjustPrice'); ?>?order_id=" + order_id + '&product_id=' + product_id + '&old_price=' + old_price;
	postDialog('adjustPrice', page_url, title, 300, 150);
}

/**
 * 切换状态。
 * @param number order_status 订单状态：。
 */
function chanageStatus(order_status) {
	var redirect_url = "<?php echo YUrl::createShopUrl('', 'Order', 'List'); ?>?order_status="+order_status;
	window.location.href=redirect_url
}

// 查看买家留言。
function viewBuyerMessage(message) {
	textDialog('viewBuyerMessage', '查看买家留言', message, 300, 100);
}

</script>

<?php
require_once (APP_VIEW_PATH . DIRECTORY_SEPARATOR . 'common/footer.php');
?>