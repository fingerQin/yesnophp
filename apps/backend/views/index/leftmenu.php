<?php
use common\YUrl;
foreach ($left_menu as $menu) :
    ?>
<h3 class="f14">
	<span class="switchs cu on" title="展开与收缩"></span><?php echo $menu['name']; ?>
</h3>
<ul>
<?php foreach ($menu['sub_menu'] as $sub): ?>

	<?php $menu_url = YUrl::createBackendUrl($sub['m'], $sub['c'], $sub['a']); ?>
	<li id="_MP<?php echo $sub['menu_id']; ?>" class="sub_menu"><a
		href="javascript:_MP(<?php echo $sub['menu_id']; ?>, '<?php echo $menu_url; ?>');"
		hidefocus="true" style="outline: none;"><?php echo $sub['name']; ?></a></li>

<?php endforeach; ?>
</ul>
<?php endforeach; ?>

<script type="text/javascript">
$(".switchs").each(function(i){
	var ul = $(this).parent().next();
	$(this).click(
	function(){
		if(ul.is(':visible')){
			ul.hide();
			$(this).removeClass('on');
				}else{
			ul.show();
			$(this).addClass('on');
		}
	})
});
</script>