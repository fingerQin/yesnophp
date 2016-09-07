<?php
use common\YUrl;
?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>UploadiFive Test</title>
<script src="<?php echo YUrl::assets('js', '/jquery.min.js'); ?>"
	type="text/javascript"></script>
<script language="javascript" type="text/javascript"
	src="<?php echo YUrl::assets('js', '/uploadify/jquery.uploadify.min.js'); ?>"></script>
<link
	href="<?php echo YUrl::assets('js', '/uploadify/uploadify.css'); ?>"
	rel="stylesheet" type="text/css">
<style type="text/css">
body {
	font: 13px Arial, Helvetica, Sans-serif;
}
</style>
</head>

<body>
	<h1>Uploadify Demo</h1>
	<form>
		<div id="queue"></div>
		<input id="file_upload" name="file_upload" type="file"
			multiple="false">
	</form>

	<script type="text/javascript">
		<?php $timestamp = time();?>
		$(function() {
			$('#file_upload').uploadify({
				'formData'      : {
					'timestamp' : '<?php echo $timestamp;?>',
					'token'     : '<?php echo md5('unique_salt' . $timestamp);?>'
				},
				'swf'           : '<?php echo YUrl::assets('js', '/uploadify/uploadify.swf'); ?>',
				'uploader'      : 'uploadify.php',
				'buttonText'    : '浏览文件',
				'queueSizeLimit': 1,
				'multi'         : false,
				'auto'          : true
			});
		});
	</script>
</body>
</html>