<?php
use common\YCore; 
use common\YUrl;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=7" />
<?php
if ($second > 0) {
	echo '<meta http-equiv="refresh" content="' . $second . ';url=' . $url . '">';
	$tip_label = "页页将在{$second}秒后自动跳转";
}
?>
<title>提示信息</title>
<style type="text/css">
<!--
*{ padding:0; margin:0; font-size:12px}
a:link,a:visited{text-decoration:none;color:#0068a6}
a:hover,a:active{color:#ff6600;text-decoration: underline}
.showMsg{border: 1px solid #1e64c8; zoom:1; width:450px; height:172px;position:absolute;top:44%;left:50%;margin:-87px 0 0 -225px}
.showMsg h5{background-image: url(<?php echo YUrl::assets('image', '/backend/msg.png'); ?>);background-repeat: no-repeat; color:#fff; padding-left:35px; height:25px; line-height:26px;*line-height:28px; overflow:hidden; font-size:14px; text-align:left}
.showMsg .content{ padding:46px 12px 10px 45px; font-size:14px; height:64px; text-align:left}
.showMsg .bottom{ background:#e4ecf7; margin: 0 1px 1px 1px;line-height:26px; *line-height:30px; height:26px; text-align:center}
.showMsg .ok,.showMsg .guery{background: url(<?php echo YUrl::assets('image', '/backend/msg_bg.png'); ?>) no-repeat 0px -560px;}
.showMsg .guery{background-position: left -460px;}
-->
</style>
<script type="text/javaScript" src="<?php echo YUrl::assets('js', '/backend/jquery.min.js'); ?>"></script>
<script language="JavaScript" src="<?php echo YUrl::assets('js', '/backend/backend_common.js'); ?>"></script>
</head>
<body>
<div class="showMsg" style="text-align:center">
	<h5>提示信息</h5>
    <div class="content guery" style="display:inline-block;display:-moz-inline-stack;zoom:1;*display:inline;max-width:330px"><?php echo $message; ?></div>
    <div class="bottom">
    	<a href="javascript:history.back();" >[点这里返回上一页]</a>
			        </div>
</div>
<script style="text/javascript">
	function close_dialog() {
		window.top.right.location.reload();window.top.art.dialog({id:""}).close();
	}
</script>

</body>
</html>