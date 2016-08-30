<?php
use common\YUrl; 
?>
<div class="site-sidebar">
	<dl>
		<dt>账户管理</dt>
		<dd>
			<a href="<?php echo YUrl::createAccountUrl('', 'Safe', 'index'); ?>" ><span>·</span>账号安全</a>
		</dd>
		<dd>
			<a href="<?php echo YUrl::createAccountUrl('', 'Safe', 'userinfo'); ?>" ><span>·</span>个人信息</a>
		</dd>
		<dd>
			<a href="<?php echo YUrl::createAccountUrl('', 'Bind', 'index'); ?>" ><span>·</span>绑定授权</a>
		</dd>
		<dd>
			<a href="<?php echo YUrl::createAccountUrl('', 'Safe', 'list'); ?>" ><span>·</span>登录记录</a>
		</dd>
	</dl>

</div>