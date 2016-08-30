<?php
use common\YUrl;
require_once(APP_VIEW_PATH . DIRECTORY_SEPARATOR . 'common/header.php');
?>

<div class="subnav">
    <div class="content-menu ib-a blue line-x">
    	<a class="add fb" href="javascript:postDialog('add', '<?php echo YUrl::createBackendUrl('', 'District', 'add'); ?>', '添加地区', 480, 240)"><em>添加配置</em></a>
    	<a href='javascript:;' class="on"><em>省市区列表</em></a>    
    	<a style="float:right;" class="add fb" href="###" onclick="normalDialog('clearCache', '<?php echo YUrl::createBackendUrl('', 'District', 'createJsonFile'); ?>', '您确定要生成省市区JSON文件吗？')" title="生成省市区JSON文件"><em>生成省市区JSON文件</em></a>
    </div>
</div>
<style type="text/css">
	html{_overflow-y:scroll}
</style>


</body>
</html>