DROP DATABASE IF EXISTS yesnophp;
CREATE DATABASE yesnophp DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
use yesnophp;

DROP TABLE IF EXISTS ms_user;
CREATE TABLE ms_user(
	user_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '用户ID',
	username CHAR(20) NOT NULL COMMENT '账号',
	password CHAR(32) NOT NULL COMMENT '密码',
	salt CHAR(6) NOT NULL COMMENT '密码盐',
	user_type CHAR(10) NOT NULL COMMENT '用户类型。nomarl、shop',
	mobilephone CHAR(11) NOT NULL DEFAULT '' COMMENT '手机号码',
	mobilephone_ok TINYINT(1) NOT NULL DEFAULT '0' COMMENT '手机验证状态：0未验证、1已验证',
	mobilephone_time INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '手机验证通过时间',
	email CHAR(50) NOT NULL DEFAULT '' COMMENT '邮箱',
	email_ok TINYINT(1) NOT NULL DEFAULT '0' COMMENT '邮箱验证状态：0未验证、1已验证',
	email_time INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '邮箱验证通过时间',
	last_login_time INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '最后登录时间',
	reg_time INT(11) UNSIGNED NOT NULL COMMENT '注册时间',
	PRIMARY KEY(user_id),
	UNIQUE KEY `username_unique` (username),
	KEY `mobilephone_key` (mobilephone),
	KEY `email_key` (email)
) ENGINE = InnoDB DEFAULT CHARSET UTF8 COMMENT '用户表';

# 用户副表。
DROP TABLE IF EXISTS ms_user_data;
CREATE TABLE ms_user_data(
	id INT(11) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
	user_id INT(11) UNSIGNED NOT NULL COMMENT '用户ID',
	nickname CHAR(10) NOT NULL DEFAULT '' COMMENT '昵称',
	realname CHAR(10) NOT NULL DEFAULT '' COMMENT '真实姓名',
	avatar CHAR(50) NOT NULL DEFAULT '' COMMENT '头像地址',
	mobilephone CHAR(11) NOT NULL DEFAULT '' COMMENT '手机号码',
	signature CHAR(50) NOT NULL DEFAULT '' COMMENT '个性签名',
	birthday CHAR(10) NOT NULL DEFAULT '0000-00-00' COMMENT '生日',
	sex TINYINT(1) NOT NULL DEFAULT '0' COMMENT '性别：1男、2女、0保密',
	email CHAR(50) NOT NULL DEFAULT '' COMMENT '邮箱',
	created_time INT(11) UNSIGNED NOT NULL COMMENT '创建时间',
	modified_time INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改时间',
	PRIMARY KEY(id),
	KEY(user_id)
) ENGINE = InnoDB DEFAULT CHARSET UTF8 COMMENT '用户副表';

# 用户登录历史表
# 记录用户的登录行为，提供风险评估。
DROP TABLE IF EXISTS ms_user_login;
CREATE TABLE ms_user_login(
	id INT(11) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
	user_id INT(11) UNSIGNED NOT NULL COMMENT '用户ID',
	login_time INT(10) NOT NULL COMMENT '登录时间',
	login_ip CHAR(50) NOT NULL COMMENT '登录IP',
	login_entry TINYINT(1) NOT NULL COMMENT '登录入口：1PC、2APP、3WAP',
	PRIMARY KEY(id),
	KEY(user_id)
) ENGINE = InnoDB DEFAULT CHARSET UTF8 COMMENT '用户登录历史表';


# 此表要保存账号历史禁用记录
DROP TABLE IF EXISTS ms_user_blacklist;
CREATE TABLE ms_user_blacklist(
	id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
	user_id INT(11) UNSIGNED NOT NULL COMMENT '用户ID',
	username CHAR(20) NOT NULL COMMENT '账号',
	ban_type SMALLINT(1) NOT NULL COMMENT '禁用类型：1永久封禁、2临时封禁',
	ban_start_time INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '封禁开始时间',
	ban_end_time INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '封禁截止时间',
	ban_reason CHAR(255) NOT NULL DEFAULT '' COMMENT '账号封禁原因',
	status TINYINT(1) NOT NULL DEFAULT '0' COMMENT '记录状态：0失效、1生效',
	created_by INT(11) UNSIGNED NOT NULL COMMENT '创建人',
	created_time INT(11) UNSIGNED NOT NULL COMMENT '创建时间',
	modified_by INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改人',
	modified_time INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改时间',
	PRIMARY KEY(id),
	KEY(username),
	KEY(user_id)
) ENGINE = InnoDB DEFAULT CHARSET UTF8 COMMENT '用户黑名单表';


# 记录第三方登录绑定。
DROP TABLE IF EXISTS ms_user_bind;
CREATE TABLE ms_user_bind(
	id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
	user_id INT(11) UNSIGNED NOT NULL COMMENT '用户ID',
	bind_type CHAR(10) NOT NULL COMMENT '绑定类型：qq、weibo、weixin',
	openid VARCHAR(100) NOT NULL COMMENT 'openid',
	created_time INT(11) UNSIGNED NOT NULL COMMENT '创建时间',
	status TINYINT(1) NOT NULL DEFAULT '0' COMMENT '记录状态：0失效、1生效',
	PRIMARY KEY(id),
	KEY(openid),
	KEY(user_id),
	KEY(bind_type)
) ENGINE = InnoDB DEFAULT CHARSET UTF8 COMMENT '用户绑定表';


DROP TABLE IF EXISTS `ms_sms_log`;
CREATE TABLE ms_sms_log (
log_id INT(11) UNSIGNED AUTO_INCREMENT COMMENT '日志ID',
op_type TINYINT(1) NOT NULL COMMENT '操作类型：1发送、2验证',
mobilephone CHAR(11) NOT NULL COMMENT '手机号码',
sms_txt CHAR(200) NOT NULL COMMENT '短信内容',
sms_code CHAR(6) NOT NULL DEFAULT '' COMMENT '验证码。如果是非验证码短信，此值为空字符串',
is_destroy TINYINT(1) NOT NULL DEFAULT '0' COMMENT '是否验证成功立即销毁。1是、0否',
created_time INT(11) UNSIGNED NOT NULL COMMENT '创建时间',
PRIMARY KEY(log_id),
KEY(mobilephone)
)ENGINE = InnoDB DEFAULT CHARSET = 'UTF8' COMMENT '短信发送/验证日志表';


# 敏感词表
DROP TABLE IF EXISTS ms_sensitive;
CREATE TABLE ms_sensitive(
	id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
	lv TINYINT(1) NOT NULL DEFAULT '0' COMMENT '敏感等级',
	val VARCHAR(50) NOT NULL COMMENT '敏感词',
	created_by INT(11) UNSIGNED NOT NULL COMMENT '创建人',
	created_time INT(11) UNSIGNED NOT NULL COMMENT '创建时间',
	modified_by INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改人',
	modified_time INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改时间',
	PRIMARY KEY(id),
	KEY(val)
) ENGINE = InnoDB DEFAULT CHARSET UTF8 COMMENT '敏感词表';

# IP黑名单表
DROP TABLE IF EXISTS ms_ip_ban;
CREATE TABLE ms_ip_ban(
	id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
	ip VARCHAR(15) NOT NULL COMMENT 'IP地址',
	remark CHAR(50) NOT NULL DEFAULT '' COMMENT '备注',
	created_by INT(11) UNSIGNED NOT NULL COMMENT '创建人',
	created_time INT(11) UNSIGNED NOT NULL COMMENT '创建时间',
	modified_by INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改人',
	modified_time INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改时间',
	PRIMARY KEY(id),
	KEY(ip)
) ENGINE = InnoDB DEFAULT CHARSET UTF8 COMMENT 'IP黑名单表';

DROP TABLE IF EXISTS ms_remind;
CREATE TABLE ms_remind(
	remind_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '提醒ID',
	user_id INT(11) UNSIGNED NOT NULL COMMENT '用户ID',
	remind_type SMALLINT(3) NOT NULL COMMENT '提醒类型:1、一次性提醒、2、每天提醒、3、每周提醒、4、每月提醒、5、每年提醒',
	title CHAR(20) NOT NULL COMMENT '提醒标题',
	content VARCHAR(255) NOT NULL DEFAULT '' COMMENT '提醒内容',
	remind_year SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT '提醒时间的年份',
	remind_month SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT '提醒时间的月份',
	remind_day SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT '提醒时间的日',
	remind_hour SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT '提醒时间的时',
	remind_minute SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT '提醒时间的分',
	remind_second SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT '提醒时间的秒',
	status TINYINT(1) NOT NULL DEFAULT '0' COMMENT '记录状态：1生效、0失效',
	is_deleted TINYINT(1) NOT NULL DEFAULT '0' COMMENT '删除状态：1已删除、0未删除',
	created_time INT(11) UNSIGNED NOT NULL COMMENT '创建时间',
	PRIMARY KEY(remind_id),
	KEY(user_id)
) ENGINE = InnoDB DEFAULT CHARSET UTF8 COMMENT '提醒表';

# 此表会保存目前需要提醒的数据。定时器会定时处理。
# 此表每天保存最新的定时数据。
# 前一天的定时数据通过更换表象来备份。
DROP TABLE IF EXISTS ms_remind_data;
CREATE TABLE ms_remind_data(
	once_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
	remind_id INT(11) UNSIGNED NOT NULL COMMENT '提醒ID',
	user_id INT(11) UNSIGNED NOT NULL COMMENT '用户ID',
	title CHAR(20) NOT NULL COMMENT '提醒标题',
	content CHAR(255) NOT NULL DEFAULT '' COMMENT '提醒内容',
	remind_time INT(11) UNSIGNED NOT NULL COMMENT '提醒时间',
	is_ok TINYINT(1) NOT NULL DEFAULT '0' COMMENT '是否已经提醒：1是、0否',
	created_time INT(11) UNSIGNED NOT NULL COMMENT '创建时间',
	PRIMARY KEY(once_id),
	KEY(user_id)
) ENGINE = InnoDB DEFAULT CHARSET UTF8 COMMENT '提醒处理表';


# 记录周更新备份一次，按月份保存历史数据。
DROP TABLE IF EXISTS ms_log;
CREATE TABLE ms_log(
	log_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
	log_type TINYINT(1) NOT NULL DEFAULT '0' COMMENT '日志类型：参见models\Log常量',
	log_user_id INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '操作用户ID',
	log_time INT(11) UNSIGNED NOT NULL COMMENT '日志产生时间',
	errcode INT(11) NOT NULL DEFAULT '0' COMMENT '错误编号',
	content TEXT COMMENT '日志内容',
	created_time INT(11) UNSIGNED NOT NULL COMMENT '日志创建时间',
	PRIMARY KEY(log_id)
) ENGINE = InnoDB DEFAULT CHARSET UTF8 COMMENT '系统日志表';


# 字典类型表
DROP TABLE IF EXISTS ms_dict_type;
CREATE TABLE ms_dict_type(
	dict_type_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
	type_code CHAR(50) NOT NULL COMMENT '字典类型编码',
    type_name CHAR(50) NOT NULL COMMENT '字典类型名称',
    description CHAR(200) NOT NULL DEFAULT '' COMMENT '字典类型描述',
    status TINYINT(1) NOT NULL DEFAULT '0' COMMENT '状态：0失效、1有效、2删除',
    created_by INT(11) UNSIGNED NOT NULL COMMENT '类型创建人',
	created_time INT(11) UNSIGNED NOT NULL COMMENT '类型创建时间',
	modified_by INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '类型修改人',
	modified_time INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '类型修改时间',
	PRIMARY KEY(dict_type_id),
	KEY `type_code` (type_code)
) ENGINE = InnoDB DEFAULT CHARSET UTF8 COMMENT '字典类型表';
INSERT INTO ms_dict_type (`dict_type_id`, `type_code`, `type_name`, `description`, `status`, `created_by`, `created_time`, `modified_by`, `modified_time`) 
VALUES ('1', 'category_type_list', '分类类型列表', '此分类类型列表用在分类列表中。', '1', '1', unix_timestamp(now()), '0', '0'),
('2', 'wechat_type', '公众号类型', '公众号类型', '1', '1', unix_timestamp(now()), '0', '0');

# 字典数据表
DROP TABLE IF EXISTS ms_dict;
CREATE TABLE ms_dict(
	dict_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
	dict_type_id INT(11) UNSIGNED NOT NULL COMMENT '主键',
	dict_code CHAR(50) NOT NULL COMMENT '字典编码',
    dict_value CHAR(255) NOT NULL DEFAULT '' COMMENT '字典值',
    description CHAR(255) NOT NULL DEFAULT '' COMMENT '字典类型描述',
    listorder SMALLINT(1) NOT NULL DEFAULT '0' COMMENT '排序。小在前',
    status TINYINT(1) NOT NULL DEFAULT '0' COMMENT '状态：0失效、1有效、2删除',
    created_by INT(11) UNSIGNED NOT NULL COMMENT '类型创建人',
	created_time INT(11) UNSIGNED NOT NULL COMMENT '类型创建时间',
	modified_by INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '类型修改人',
	modified_time INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '类型修改时间',
	PRIMARY KEY(dict_id),
	KEY(dict_type_id)
) ENGINE = InnoDB DEFAULT CHARSET UTF8 COMMENT '字典数据表';
INSERT INTO ms_dict (`dict_type_id`, `dict_code`, `dict_value`, `description`, `listorder`, `status`, `created_by`, `created_time`, `modified_by`, `modified_time`) 
VALUES 
('1', '1', '文章分类', '文章分类的值最好别更改。因为，会影响此分类关联的子分类。如确实要变更，请检查此ID对应的表ms_category的分类是否有值。如果有请处理之后再变更此值。', '0', '1', '1', unix_timestamp(now()), '0', '0'),
('1', '2', '友情链接分类', '请别随意更改编码值。因为与它关联的子分类数据会失去依赖。', '0', '1', '1', unix_timestamp(now()), '0', '0'),
('1', '3', '商品分类', '请别随意更改编码值。因为与它关联的子分类数据会失去依赖。', '0', '1', '1', unix_timestamp(now()), '0', '0'),
('2', '1', '订阅号', '订阅号。', '0', '1', '1', unix_timestamp(now()), '0', '0'),
('2', '2', '服务号', '服务号。', '0', '1', '1', unix_timestamp(now()), '0', '0'),
('3', '3', '企业号', '企业号。', '0', '1', '1', unix_timestamp(now()), '0', '0');



# 系统配置表
# 一些需要动态修改的配置。
DROP TABLE IF EXISTS ms_config;
CREATE TABLE ms_config(
	config_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
	ctitle CHAR(255) NOT NULL COMMENT '配置标题',
	cname CHAR(255) NOT NULL COMMENT '名称',
	dev_value CHAR(255) NOT NULL DEFAULT '' COMMENT '开发环境值',
	test_value CHAR(255) NOT NULL DEFAULT '' COMMENT '测试环境值',
	product_value CHAR(255) NOT NULL DEFAULT '' COMMENT '正式环境值',
	description CHAR(255) NOT NULL DEFAULT '' COMMENT '配置描述',
    status TINYINT(1) NOT NULL DEFAULT '0' COMMENT '状态：0失效、1有效、2删除',
    created_by INT(11) UNSIGNED NOT NULL COMMENT '类型创建人',
	created_time INT(11) UNSIGNED NOT NULL COMMENT '类型创建时间',
	modified_by INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '类型修改人',
	modified_time INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '类型修改时间',
	PRIMARY KEY(config_id),
	KEY `cname` (cname)
) ENGINE = InnoDB DEFAULT CHARSET UTF8 COMMENT '系统配置表';
INSERT INTO ms_config (`ctitle`, `cname`, `dev_value`, `test_value`, `product_value`, `description`, `status`, `created_by`, `created_time`, `modified_by`, `modified_time`)
VALUES('排他登录', 'is_unique_login', '1', '1', '1', '1是、0否。即同一时间账号只能在一个地方登录。不允许账号在其他地方登录。', 1, 1, unix_timestamp(now()), 0, 0),
('网站名称', 'site_name', '不单麦', '不单麦', '不单麦', '', 1, 1, unix_timestamp(now()), 0, 0),
('PC登录超时时间(分钟)', 'pc_logout_time', '30', '30', '30', '登录超时时间。距离上次最后操作时间大于当前指定时间分钟内将登录超时并退出登录', 1, 1, unix_timestamp(now()), 0, 0),
('管理后台域名', 'backend_domain_name', 'http://dev-backend.budanmai.com/', 'http://test-backend.budanmai.com/', 'http://backend.budanmai.com/', '涉及到网站页面或资源的链接地址', 1, 1, unix_timestamp(now()), 0, 0),
('前台域名', 'frontend_domain_name', 'http://dev-frontend.budanmai.com/', 'http://test-frontend.budanmai.com/', 'http://frontend.budanmai.com/', '涉及到网站页面或资源的链接地址', 1, 1, unix_timestamp(now()), 0, 0),
('微信域名', 'wx_domain_name', 'http://dev-wx.budanmai.com/', 'http://test-wx.budanmai.com/', 'http://wx.budanmai.com/', '涉及到网站页面或资源的链接地址', 1, 1, unix_timestamp(now()), 0, 0),
('接口域名', 'api_domain_name', 'http://dev-api.budanmai.com/', 'http://test-api.budanmai.com/', 'http://api.budanmai.com/', '涉及到网站页面或资源的链接地址', 1, 1, unix_timestamp(now()), 0, 0),
('静态资源域名', 'statics_domain_name', 'http://dev-statics.budanmai.com/', 'http://test-statics.budanmai.com/', 'http://statics.budanmai.com/', '涉及到网站页面或资源的链接地址', 1, 1, unix_timestamp(now()), 0, 0),
('商家中心域名', 'shop_domain_name', 'http://dev-shop.budanmai.com/', 'http://test-shop.budanmai.com/', 'http://shop.budanmai.com/', '涉及到网站页面或商家中心的链接地址', 1, 1, unix_timestamp(now()), 0, 0),
('账户中心域名', 'account_domain_name', 'http://dev-account.budanmai.com/', 'http://test-account.budanmai.com/', 'http://account.budanmai.com/', '涉及到网站页面或账户中心的链接地址', 1, 1, unix_timestamp(now()), 0, 0),
('图片文件资源域名', 'files_domain_name', 'http://dev-files.budanmai.com/', 'http://test-files.budanmai.com/', 'http://files.budanmai.com/', '涉及到网站图片文件部分', 1, 1, unix_timestamp(now()), 0, 0),
('用户权限cookie作用域', 'user_auth_cookie_domain_name', '.budanmai.com', '.budanmai.com', '.budanmai.com', '即此域下所有域名都可以自动登录', 1, 1, unix_timestamp(now()), 0, 0),
('APP登录超时时间(天)', 'app_logout_time', '30', '30', '30', '登录超时时间。距离上次最后操作时间大于当前指定时间分钟内将登录超时并退出登录', 1, 1, unix_timestamp(now()), 0, 0),
('管理员cookie作用域', 'admin_cookie_domain', '.dev-backend.budanmai.com', '.test-backend.budanmai.com', '.backend.budanmai.com', '为避免cookie值被前台使用，配置的域必须是管理后台的域名。', 1, 1, unix_timestamp(now()), 0, 0),
('后台登录超时时间(分钟)', 'admin_logout_time', '30', '30', '30', '超时则需要重新登录', 1, 1, unix_timestamp(now()), 0, 0),
('系统维护状态', 'system_status', '1', '1', '1', '除管理后台之外的地方维护状态。1是正常、0是关闭系统', 1, 1, unix_timestamp(now()), 0, 0),
('系统业务运行等级', 'system_service_level', '0', '0', '0', '示例：1,8 。1:注册功能、2:登录功能、4:找回密码、8:密码修改、16:支付功能、32:短信功能、64:邮件功能、128:评价功能、256:上传功能、512:订单查看功能、1024:提现功能、2048:API接口、4096:微信应用、8192:关闭全站（除后台）', 1, 1, unix_timestamp(now()), 0, 0),
('luosimao短信KEY', 'luosimao_sms_key', '5d68e2564cc9deac5bc8d74935dc4e8c', '5d68e2564cc9deac5bc8d74935dc4e8c', '5d68e2564cc9deac5bc8d74935dc4e8c', 'luosimao短信发送KEY。', 1, 1, unix_timestamp(now()), 0, 0),
('省市区JSON文件更新版本', 'district_json_version', '', '', '', '省市区JSON文件更新版本', 1, 1, unix_timestamp(now()), 0, 0);

# 文件表
# 上传的图片、视频等文件记录在此表中。
# 如果是公开的图片则图片链接是固定的。私有的则图片链接是动态生成的。
DROP TABLE IF EXISTS ms_files;
CREATE TABLE ms_files(
	file_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
	file_name CHAR(50) NOT NULL COMMENT '文件名称',
	file_type TINYINT(1) NOT NULL COMMENT '文件类型：1-图片、2-其他文件',
	file_size INT(11) UNSIGNED NOT NULL COMMENT '文件大小。单位：(byte)',
	file_md5 CHAR(32) NOT NULL COMMENT '文件md5值',
	user_type TINYINT(1) NOT NULL COMMENT '用户类型：1管理员、2普通用户',
	user_id INT(11) UNSIGNED NOT NULL COMMENT '用户ID',
	status TINYINT(1) NOT NULL DEFAULT '0' COMMENT '状态：0失效、1有效、2删除',
	created_time INT(11) UNSIGNED NOT NULL COMMENT '创建时间',
	PRIMARY KEY(file_id)
) ENGINE = InnoDB DEFAULT CHARSET UTF8 COMMENT '文件表';


# 管理员表
DROP TABLE IF EXISTS ms_admin;
CREATE TABLE ms_admin(
	admin_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '管理员ID',
	realname CHAR(20) NOT NULL COMMENT '真实姓名',
	username CHAR(20) NOT NULL COMMENT '账号',
	password CHAR(32) NOT NULL COMMENT '密码',
	salt CHAR(6) NOT NULL COMMENT '密码盐',
	mobilephone CHAR(11) NOT NULL DEFAULT '' COMMENT '手机号码',
	roleid SMALLINT(3) NOT NULL DEFAULT '0' COMMENT '角色ID',
	lastlogintime INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '最后登录时间戳',
	status TINYINT(1) NOT NULL DEFAULT '0' COMMENT '状态：0失效、1有效、2删除',
	created_time INT(11) UNSIGNED NOT NULL COMMENT '创建时间',
	PRIMARY KEY(admin_id),
	KEY(username),
	KEY(mobilephone)
) ENGINE = InnoDB DEFAULT CHARSET UTF8 COMMENT '管理员表';
INSERT INTO ms_admin (admin_id, realname, username, password, salt, status, created_time, roleid)
VALUES(1, '超级管理员', 'admin', 'c7935cc8ee50b752345290d8cf136827', 'abcdef', 1, unix_timestamp(now()), 1);


# 管理员登录历史表	
DROP TABLE IF EXISTS ms_admin_login_history;
CREATE TABLE `ms_admin_login_history` (
  id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  admin_id INT(11) UNSIGNED NOT NULL COMMENT '管理员ID',
  browser_type CHAR(10) NOT NULL COMMENT '浏览器类型。tablet平板、phone手机、computer电脑',
  user_agent VARCHAR(200) NOT NULL COMMENT '浏览器UA',
  ip CHAR(15) NOT NULL COMMENT '登录IP',
  address VARCHAR(100) NOT NULL DEFAULT '' COMMENT 'IP对应的地址信息',
  created_time INT(11) UNSIGNED NOT NULL COMMENT '登录时间',
  PRIMARY KEY(id)
) ENGINE=InnoDB DEFAULT CHARSET UTF8 COMMENT '管理员登录历史表';


# 角色表	
DROP TABLE IF EXISTS ms_admin_role;
CREATE TABLE ms_admin_role(
	roleid INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '角色ID',
	rolename CHAR(20) NOT NULL COMMENT '角色名称',
	listorder SMALLINT(3) NOT NULL DEFAULT '0' COMMENT '排序。小在前。',
	description CHAR(255) NOT NULL DEFAULT '' COMMENT '角色说明',
	status TINYINT(1) NOT NULL DEFAULT '0' COMMENT '状态：0失效、1有效、2删除',
	created_time INT(11) UNSIGNED NOT NULL COMMENT '创建时间',
	is_default TINYINT(1) NOT NULL DEFAULT '0' COMMENT '默认角色拥有最高权限。不可删除此默认角色。超级管理员只能属于此角色，其他用户不可分配此角色',
	PRIMARY KEY(roleid)
) ENGINE = InnoDB DEFAULT CHARSET UTF8 COMMENT '角色表';
INSERT INTO ms_admin_role (roleid, rolename, status, is_default, created_time) VALUES(1, '超级管理员', 1, 1, unix_timestamp(now()));


# 角色权限表	
DROP TABLE IF EXISTS ms_admin_role_priv;
CREATE TABLE `ms_admin_role_priv` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `roleid` SMALLINT(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '角色ID',
  `menu_id` INT(11) UNSIGNED NOT NULL COMMENT '菜单ID',
  PRIMARY KEY(id),
  KEY(roleid)
) ENGINE=InnoDB DEFAULT CHARSET UTF8 COMMENT '角色权限表';


# 文章表
DROP TABLE IF EXISTS ms_news;
CREATE TABLE `ms_news` (
	news_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '文章ID、主键',
	cat_id INT(11) UNSIGNED NOT NULL COMMENT '分类ID。对应ms_category.cat_id',
	title CHAR(50) NOT NULL COMMENT '文章标题',
	code CHAR(20) NOT NULL COMMENT '文章编码(只允许字母数字下划线横线)',
	intro CHAR(250) NOT NULL COMMENT '文章简介。也是SEO中的description',
	keywords CHAR(50) NOT NULL DEFAULT '' COMMENT '文章关键词。也是SEO中的keywords',
	image_url CHAR(100) NOT NULL DEFAULT '' COMMENT '文章列表图片',
	source CHAR(20) NOT NULL DEFAULT '' COMMENT '文章来源',
	display TINYINT(1) NOT NULL DEFAULT '0' COMMENT '文章是否显示。1显示、0隐藏',
	status TINYINT(1) NOT NULL DEFAULT '0' COMMENT '文章状态：0无效、1正常、2删除',
	listorder SMALLINT(6) UNSIGNED NOT NULL DEFAULT '0' COMMENT '排序。小到大排序。',
	hits INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '文章访问量',
	created_by INT(11) UNSIGNED NOT NULL COMMENT '创建人',
	created_time INT(11) UNSIGNED NOT NULL COMMENT '创建时间',
	modified_by INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改人',
	modified_time INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改时间',
	PRIMARY KEY(news_id),
	KEY(created_time),
	KEY(created_by)
) ENGINE=InnoDB DEFAULT CHARSET UTF8 COMMENT '文章表';

# 文章副表
DROP TABLE IF EXISTS ms_news_data;
CREATE TABLE `ms_news_data` (
	news_id INT(11) UNSIGNED NOT NULL COMMENT '文章ID',
	content TEXT COMMENT '文章内容',
	PRIMARY KEY(news_id)
) ENGINE=InnoDB DEFAULT CHARSET UTF8 COMMENT '文章副表';


# 友情链接表
# 通过一个URL来统一跳转这些友情链接。方便统计。
DROP TABLE IF EXISTS ms_link;
CREATE TABLE `ms_link` (
	link_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
	link_name VARCHAR(50) NOT NULL COMMENT '友情链接名称',
	link_url VARCHAR(100) NOT NULL COMMENT '友情链接URL',
	cat_id INT(11) UNSIGNED NOT NULL COMMENT '友情链接分类ID。对应ms_category.cat_id',
	image_url VARCHAR(100) NOT NULL DEFAULT '' COMMENT '友情链接图片',
	display TINYINT(1) NOT NULL DEFAULT '0' COMMENT '是否显示。1显示、0隐藏',
	status TINYINT(1) NOT NULL DEFAULT '0' COMMENT '状态：0无效、1正常、2删除',
	listorder SMALLINT(6) UNSIGNED NOT NULL DEFAULT '0' COMMENT '排序。小到大排序。',
	hits INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'URL点击量',
	created_by INT(11) UNSIGNED NOT NULL COMMENT '创建人',
	created_time INT(11) UNSIGNED NOT NULL COMMENT '创建时间',
	modified_by INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改人',
	modified_time INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改时间',
	PRIMARY KEY(link_id)
) ENGINE=InnoDB DEFAULT CHARSET UTF8 COMMENT '友情链接表';


# 广告位置接表
DROP TABLE IF EXISTS ms_ad_position;
CREATE TABLE `ms_ad_position` (
	pos_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
	pos_name VARCHAR(50) NOT NULL COMMENT '广告位置名称',
	pos_code VARCHAR(50) NOT NULL COMMENT '广告位置编码。通过编码来读取广告数据',
	pos_ad_count SMALLINT(5) NOT NULL COMMENT '该广告位置显示可展示广告的数量',
	status TINYINT(1) NOT NULL COMMENT '状态：0无效、1正常、2删除',
	created_by INT(11) UNSIGNED NOT NULL COMMENT '创建人',
	created_time INT(11) UNSIGNED NOT NULL COMMENT '创建时间',
	modified_by INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改人',
	modified_time INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改时间',
	PRIMARY KEY(pos_id)
) ENGINE=InnoDB DEFAULT CHARSET UTF8 COMMENT '广告位置接表';

# 广告表
DROP TABLE IF EXISTS ms_ad;
CREATE TABLE `ms_ad` (
	ad_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
	ad_name VARCHAR(50) NOT NULL COMMENT '广告名称',
	pos_id INT(11) UNSIGNED NOT NULL COMMENT '广告位置。对应ms_ad_postion.pos_id',
	ad_image_url VARCHAR(100) NOT NULL COMMENT '广告图片',
	ad_url VARCHAR(100) NOT NULL COMMENT '广告图片URL跳转地址',
	start_time INT(11) UNSIGNED NOT NULL COMMENT '广告生效时间',
	end_time INT(11) UNSIGNED NOT NULL COMMENT '广告失效时间',
	display TINYINT(1) NOT NULL DEFAULT '1' COMMENT '显示状态：1显示、0隐藏',
	status TINYINT(1) NOT NULL COMMENT '状态：0无效、1正常、2删除',
	listorder SMALLINT(6) UNSIGNED NOT NULL DEFAULT '0' COMMENT '排序。小到大排序。',
	remark VARCHAR(255) NOT NULL DEFAULT '' COMMENT '备注',
	created_by INT(11) UNSIGNED NOT NULL COMMENT '创建人',
	created_time INT(11) UNSIGNED NOT NULL COMMENT '创建时间',
	modified_by INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改人',
	modified_time INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改时间',
	PRIMARY KEY(ad_id)
) ENGINE=InnoDB DEFAULT CHARSET UTF8 COMMENT '广告表';


# 分类表
# 所有父分类ID为0的分类，都有一个共同的虚拟顶级父类ID为0。
DROP TABLE IF EXISTS `ms_category`;
CREATE TABLE ms_category(
cat_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '分类ID',
cat_name VARCHAR(50) NOT NULL COMMENT '分类名称',
cat_type SMALLINT(3) NOT NULL COMMENT '分类类型。见category_type_list字典。',
parentid INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '父分类ID',
lv SMALLINT(3) NOT NULL COMMENT '菜单层级',
cat_code VARCHAR(50) NOT NULL COMMENT '分类code编',
is_out_url TINYINT(1) NOT NULL DEFAULT '0' COMMENT '是否外部链接：1是、0否',
out_url VARCHAR(255) NOT NULL DEFAULT '' COMMENT '外部链接地址',
display TINYINT(1) NOT NULL DEFAULT '0' COMMENT '显示状态：1是、0否',
status TINYINT(1) NOT NULL DEFAULT '0' COMMENT '状态：0无效、1正常、2删除',
listorder SMALLINT(5) NOT NULL DEFAULT '0' COMMENT '排序值。小到大排列。',
modified_by INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改人',
modified_time INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改时间戳',
created_time INT(11) UNSIGNED NOT NULL COMMENT '创建时间戳',
created_by INT(11) UNSIGNED NOT NULL COMMENT '管理员账号ID',
PRIMARY KEY(cat_id),
KEY(cat_code)
) ENGINE = InnoDB DEFAULT CHARSET UTF8 COMMENT '分类表';


# 后台菜单表
DROP TABLE IF EXISTS ms_menu;
CREATE TABLE `ms_menu` (
  `menu_id` SMALLINT(6) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` CHAR(40) NOT NULL DEFAULT '',
  `parentid` SMALLINT(6) NOT NULL DEFAULT '0',
  `m` CHAR(50) NOT NULL DEFAULT '',
  `c` CHAR(50) NOT NULL DEFAULT '',
  `a` CHAR(50) NOT NULL DEFAULT '',
  `data` CHAR(255) NOT NULL DEFAULT '',
  `listorder` SMALLINT(6) UNSIGNED NOT NULL DEFAULT '0',
  `display` ENUM('1','0') NOT NULL DEFAULT '1',
  PRIMARY KEY (`menu_id`),
  KEY `listorder` (`listorder`),
  KEY `parentid` (`parentid`),
  KEY `module` (`m`,`c`,`a`)
) ENGINE=InnoDB DEFAULT CHARSET UTF8 COMMENT '后台菜单表';


INSERT INTO `ms_menu` (`menu_id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`)
VALUES

(1000, '常用功能', 0,'','','','',0,'1'),
(1001, '常用功能', 1000,'','','','',0,'1'),
(1002, '修改密码', 1001, 'Index','Admin','editPwd','',0,'1'),
(1003, '登录历史', 1001, 'Index','Admin','loginHistory','',0,'1'),
(1004, '管理后台首页', 1001, 'Index','Index','Index','',0,'0'),
(1005, '管理后台Ajax获取菜单', 1001, 'Index','Index','leftMenu','',0,'0'),
(1006, '管理后台右侧默认页', 1001, 'Index','Index','right','',0,'0'),
(1007, '管理后台面包屑', 1001, 'Index','Index','arrow','',0,'0'),
(1008, '文件上传', '1001', 'Index', 'Index', 'upload', '', '0', '0'),
(1009, '生成首页', '1001', 'Index', 'Index', 'createHomePage', '', '0', '1'),

(2000, '系统设置',0,'','','','',0,'1'),
(2001, '系统配置',2000,'','','','',0,'1'),
(2002, '字典管理',2001,'Index','Dict','index','',0,'1'),
(2003, '添加字典类型',2001,'Index','Dict','addType','',0,'0'),
(2004, '编辑字典类型',2001,'Index','Dict','editType','',0,'0'),
(2005, '删除字典类型',2001,'Index','Dict','deleteType','',0,'0'),
(2006, '字典列表',2001,'Index','Dict','dict','',0,'0'),
(2007, '删除字典',2001,'Index','Dict','delete','',0,'0'),
(2008, '添加字典',2001,'Index','Dict','add','',0,'0'),
(2009, '更新字典',2001,'Index','Dict','edit','',0,'0'),
(2010, '字典类型排序',2001,'Index','Dict','sortType','',0,'0'),
(2011, '字典排序',2001,'Index','Dict','sortDict','',0,'0'),
(2012, '字典缓存清除',2001,'Index','Dict','ClearCache','',0,'0'),
(2013, '配置管理', 2001, 'Index','Config','index','',0,'1'),
(2014, '添加配置', 2001, 'Index','Config','add','',0,'0'),
(2015, '编辑配置', 2001, 'Index','Config','edit','',0,'0'),
(2016, '删除配置', 2001, 'Index','Config','delete','',0,'0'),
(2017, '配置排序', 2001, 'Index','Config','sort','',0,'0'),
(2018, '配置缓存清除', 2001, 'Index','Config','ClearCache','',0,'0'),
(2019, '菜单列表', 2001, 'Index','Menu','index','',0,'1'),
(2020, '添加菜单', 2001, 'Index','Menu','add','',0,'0'),
(2021, '编辑菜单', 2001, 'Index','Menu','edit','',0,'0'),
(2022, '删除菜单', 2001, 'Index','Menu','delete','',0,'0'),
(2023, '菜单排序', 2001, 'Index','Menu','sort','',0,'0'),

(2200, '敏感词管理', 2000,'Index','Sensitive','','',0,'1'),
(2201, '敏感词列表', 2200,'Index','Sensitive','index','',0,'1'),
(2202, '添加敏感词', 2200,'Index','Sensitive','add','',0,'0'),
(2203, '更新敏感词', 2200,'Index','Sensitive','edit','',0,'0'),
(2204, '敏感词删除', 2200,'Index','Sensitive','delete','',0,'0'),

(2300,'IP禁止', 2000,'Index','Ip','','',0,'1'),
(2301,'被禁IP列表',2300,'Index','Ip','index','',0,'1'),
(2302,'添加IP',2300,'Index','Ip','add','',0,'0'),
(2303,'删除IP',2300,'Index','Ip','delete','',0,'0'),

(2400, '省市区管理', 2000, 'Index','District','','',0,'1'),
(2401, '添加省市区', 2400, 'Index','District','add','',0,'0'),
(2402, '编辑省市区', 2400, 'Index','District','edit','',0,'0'),
(2403, '删除省市区', 2400, 'Index','District','delete','',0,'0'),
(2404, '省市区排序', 2400, 'Index','District','sort','',0,'0'),
(2405, '省市区列表', 2400,'Index','District','index','',0,'1'),
(2406, '创建省市区JSON文件', 2400,'Index','District','createJsonFile','',0,'0'),

(2500, '日志管理', 2000,'Index','Log','','',0,'1'),
(2501, '日志查看', 2500,'Index','Log','index','',0,'1'),

(2700, '文件管理', 2000, 'Index','File','','',0,'1'),
(2701, '文件列表', 2700, 'Index','File','index','',0,'1'),
(2702, '更新文件', 2700, 'Index','File','edit','',0,'0'),
(2703, '添加文件', 2700, 'Index','File','add','',0,'0'),
(2704, '删除文件', 2700, 'Index','File','delete','',0,'0'),


(3000, '权限管理',0,'','','','',0,'1'),

(3001, '管理员管理', 3000, 'Index','Admin','','',0,'1'),
(3002, '管理员列表', 3001, 'Index','Admin','index','',0,'1'),
(3003, '添加管理员', 3002, 'Index','Admin','add','',0,'0'),
(3004, '更新管理员', 3003, 'Index','Admin','edit','',0,'0'),
(3005, '删除管理员', 3004, 'Index','Admin','delete','',0,'0'),

(3100, '角色管理', 3000,'Index','Role','','',0,'1'),
(3101, '角色列表', 3100,'Index','Role','index','',0,'1'),
(3102, '添加角色', 3100,'Index','Role','add','',0,'0'),
(3103, '更新角色', 3100,'Index','Role','update','',0,'0'),
(3104, '删除角色', 3100,'Index','Role','delete','',0,'0'),
(3105, '角色赋权', 3100,'Index','Role','setPermission','',0,'0'),


(4000, '内容管理',0,'','','','',0,'1'),

(4001, '分类管理', 4000, 'Index','Category','','',0,'1'),
(4002, '分类列表', 4001, 'Index','Category','index','',0,'1'),
(4003, '添加分类', 4001, 'Index','Category','add','',0,'0'),
(4004, '更新分类', 4001, 'Index','Category','edit','',0,'0'),
(4005, '删除分类', 4001, 'Index','Category','delete','',0,'0'),
(4006, '分类排序', 4001, 'Index','Category','sort','',0,'0'),

(4100, '文章管理', 4000, 'Index','News','','',0,'1'),
(4101, '文章列表', 4100, 'Index','News','index','',0,'1'),
(4102, '添加文章', 4100, 'Index','News','add','',0,'0'),
(4103, '更新文章', 4100, 'Index','News','edit','',0,'0'),
(4104, '删除文章', 4100, 'Index','News','sort','',0,'0'),
(4105, '文章排序', 4100, 'Index','News','sort','',0,'0'),

(4200, '友情链接', 4000, 'Index','Link','','',0,'1'),
(4201, '友情链接列表', 4200,'Index','Link','index','',0,'1'),
(4202, '添加友情链接', 4200,'Index','Link','add','',0,'0'),
(4203, '更新友情链接', 4200,'Index','Link','edit','',0,'0'),
(4204, '删除友情链接', 4200,'Index','Link','delete','',0,'0'),
(4205, '友情链接排序', 4200,'Index','Link','sort','',0,'0'),

(4300, '广告管理', 4000,'Index','Ad','','',0,'1'),
(4301, '广告位置列表',4300,'Index','Ad','positionList','',0,'1'),
(4302, '添加广告位置',4300,'Index','Ad','positionAdd','',0,'0'),
(4303, '更新广告位置',4300,'Index','Ad','positionEdit','',0,'0'),
(4304, '删除广告位置',4300,'Index','Ad','positionDelete','',0,'0'),
(4305, '广告列表',4300,'Index','Ad','index','',0,'0'),
(4306, '添加广告',4300,'Index','Ad','add','',0,'0'),
(4307, '更新广告',4300,'Index','Ad','edit','',0,'0'),
(4308, '删除广告',4300,'Index','Ad','delete','',0,'0'),
(4309, '广告排序',4300,'Index','Ad','sort','',0,'0'),



(5000, '用户管理',0,'','','','',0,'1'),

(5001, '用户管理',5000,'Index','User','','',0,'1'),
(5002, '用户列表',5001,'Index','User','index','',0,'1'),
(5003, '添加用户',5001,'Index','User','add','',0,'0'),
(5004, '更新用户',5001,'Index','User','edit','',0,'0'),
(5005, '禁用用户',5001,'Index','User','forbid','',0,'0'),
(5006, '查看用户详情',5001,'Index','User','view','',0,'0'),
(5007, '解禁用户',5001,'Index','User','unforbid','',0,'0'),

(6000, '第三方应用', 0,'','','','',0,'1'),
(6001, '公众号管理',6000,'Index','WeChat','','',0,'1'),
(6002, '公众号列表',6001,'Index','WeChat','accountList','',0,'1'),
(6003, '添加公众号',6001,'Index','WeChat','addCccount','',0,'0'),
(6004, '编辑公众号',6001,'Index','WeChat','editAccount','',0,'0'),
(6005, '删除公众号',6001,'Index','WeChat','deleteAccount','',0,'0'),
(6006, '公众号菜单列表',6001,'Index','WeChat','accountMenuList','',0,'0'),
(6007, '添加公众号菜单',6001,'Index','WeChat','addAccountMenu','',0,'0'),
(6008, '修改公众号菜单',6001,'Index','WeChat','editAccountMenu','',0,'0'),
(6009, '删除公众号菜单',6001,'Index','WeChat','deleteAccountMenu','',0,'0'),
(6010, '推送菜单到微信公众号',6001,'Index','WeChat','pushAccountMenuToWeChat','',0,'0'),
(6011, '图文素材管理', 6001, 'Index', 'WeChat', 'imageTextList', '', '0', '0'),
(6012, '图文文章列表', 6001, 'Index', 'WeChat', 'invoice', '', '0', '0'),
(6013, '添加图文', 6001, 'Index', 'WeChat', 'addImageText', '', '0', '0'),
(6014, '删除图文', 6001, 'Index', 'WeChat', 'deleteImageText', '', '0', '0'),
(6015, '图文文章列表', 6001, 'Index', 'WeChat', 'imageTextArticleList', '', '0', '0'),
(6016, '添加图文文章', 6001, 'Index', 'WeChat', 'addImageTextArticle', '', '0', '0'),
(6017, '编辑图文文章', 6001, 'Index', 'WeChat', 'editImageTextArticle', '', '0', '0'),
(6018, '删除图文文章', 6001, 'Index', 'WeChat', 'deleteImageTextArticle', '', '0', '0'),

(7000, '商城管理', 0,'','','','',0,'1'),
(7001, '商城管理',7000,'Index','Shop','','',0,'1'),
(7002, '商家列表',7001,'Index','Shop','list','',0,'1'),
(7003, '添加商家',7001,'Index','Shop','add','',0,'0'),
(7004, '编辑商家',7001,'Index','Shop','edit','',0,'0'),
(7005, '商家删除',7001,'Index','Shop','delete','',0,'0'),
(7007, '商品列表',7001,'Index','Goods','list','',0,'1'),
(7008, '商品删除',7001,'Index','Goods','delete','',0,'0'),
(7009, '订单列表',7001,'Index','Order','list','',0,'1'),
(7010, '评论列表',7001,'Index','comment','list','',0,'1'),
(7011, '评论删除', 7001, 'Index', 'comment', 'delete', '', '0', '0');


# --------------- 游戏相关 start ------------#

### 初始化游戏模型需要的字典数据
INSERT INTO `ms_dict_type` (`dict_type_id`, `type_code`, `type_name`, `description`, `status`, `created_by`, `created_time`, `modified_by`, `modified_time`) 
VALUES ('20', 'game_ledou_consume_code', '游戏乐豆消费编码', '游戏乐豆消费编码：通过此编码可以知道乐豆是在何种情况下消费。比如：add_ssq_reward 代表双色球中奖增加。', '1', '1', unix_timestamp(now()), '0', '0');

INSERT INTO `ms_dict` (`dict_type_id`, `dict_code`, `dict_value`, `description`, `listorder`, `status`, `created_by`, `created_time`, `modified_by`, `modified_time`) VALUES ('20', 'add_ssq_reward', '双色球中奖', '双色球中奖', '0', '1', '1', unix_timestamp(now()), '0', '0');
INSERT INTO `ms_dict` (`dict_type_id`, `dict_code`, `dict_value`, `description`, `listorder`, `status`, `created_by`, `created_time`, `modified_by`, `modified_time`) VALUES ('20', 'add_dlt_reward', '大乐透中奖', '大乐透中奖', '0', '1', '1', unix_timestamp(now()), '0', '0');
INSERT INTO `ms_dict` (`dict_type_id`, `dict_code`, `dict_value`, `description`, `listorder`, `status`, `created_by`, `created_time`, `modified_by`, `modified_time`) VALUES ('20', 'add_ssc_reward', '时时彩中奖', '时时彩中奖', '0', '1', '1', unix_timestamp(now()), '0', '0');
INSERT INTO `ms_dict` (`dict_type_id`, `dict_code`, `dict_value`, `description`, `listorder`, `status`, `created_by`, `created_time`, `modified_by`, `modified_time`) VALUES ('20', 'cut_ssq_bet', '双色球投注', '双色球投注', '0', '1', '1', unix_timestamp(now()), '0', '0');
INSERT INTO `ms_dict` (`dict_type_id`, `dict_code`, `dict_value`, `description`, `listorder`, `status`, `created_by`, `created_time`, `modified_by`, `modified_time`) VALUES ('20', 'cut_dlt_bet', '大乐透投注', '大乐透投注', '0', '1', '1', unix_timestamp(now()), '0', '0');
INSERT INTO `ms_dict` (`dict_type_id`, `dict_code`, `dict_value`, `description`, `listorder`, `status`, `created_by`, `created_time`, `modified_by`, `modified_time`) VALUES ('20', 'cut_ssc_bet', '时时彩投注', '时时彩投注', '0', '1', '1', unix_timestamp(now()), '0', '0');
INSERT INTO `ms_dict` (`dict_type_id`, `dict_code`, `dict_value`, `description`, `listorder`, `status`, `created_by`, `created_time`, `modified_by`, `modified_time`) VALUES ('20', 'add_checkin', '每日签到', '每日签到', '0', '1', '1', unix_timestamp(now()), '0', '0');
INSERT INTO `ms_dict` (`dict_type_id`, `dict_code`, `dict_value`, `description`, `listorder`, `status`, `created_by`, `created_time`, `modified_by`, `modified_time`) VALUES ('20', 'add_buy_goods', '购买商品赠送', '购买商品赠送', '0', '1', '1', unix_timestamp(now()), '0', '0');


DROP TABLE IF EXISTS `gm_ledou`;
CREATE TABLE gm_ledou(
id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
user_id INT(11) UNSIGNED NOT NULL COMMENT '玩家ID。对应ms_user.user_id',
ledou INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '乐豆数量。包含未用完的赠送的乐豆。',
modified_time INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改时间戳',
created_time INT(11) UNSIGNED NOT NULL COMMENT '创建时间戳',
PRIMARY KEY(id),
KEY(user_id)
) ENGINE = InnoDB DEFAULT CHARSET UTF8 COMMENT '玩家乐豆表';

DROP TABLE IF EXISTS `gm_ledou_consume`;
CREATE TABLE gm_ledou_consume(
id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
user_id INT(11) UNSIGNED NOT NULL COMMENT '玩家ID。对应ms_user.user_id',
consume_type TINYINT(1) NOT NULL COMMENT '消费类型：1增加、2扣减',
consume_code CHAR(20) NOT NULL COMMENT '类型编码。通过编码可以知晓是因何产生的。编码通过字典配置。',
ledou INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '影响的乐豆数量',
created_time INT(11) UNSIGNED NOT NULL COMMENT '创建时间戳',
PRIMARY KEY(id),
KEY(user_id)
) ENGINE = InnoDB DEFAULT CHARSET UTF8 COMMENT '乐豆消费记录';


DROP TABLE IF EXISTS `gm_bet_record`;
CREATE TABLE gm_bet_record(
bet_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
user_id INT(11) UNSIGNED NOT NULL COMMENT '玩家ID。对应ms_user.user_id',
game_id INT(11) UNSIGNED NOT NULL COMMENT '游戏ID',
bet_ledou INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '投注的乐豆数量',
bet_status TINYINT(1) NOT NULL COMMENT '中奖状态：0待开奖、1已中奖、2未中奖',
reward_ledou INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '中奖乐豆',
modified_time INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改时间戳',
created_time INT(11) UNSIGNED NOT NULL COMMENT '投注时间戳',
PRIMARY KEY(bet_id),
KEY(user_id)
) ENGINE = InnoDB DEFAULT CHARSET UTF8 COMMENT '投注记录';


DROP TABLE IF EXISTS `gm_bet_record_number`;
CREATE TABLE gm_bet_record_number(
id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
bet_id INT(11) UNSIGNED NOT NULL COMMENT '投注记录ID。对应ms_bet_record.bet_id',
bet_ledou INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '投注的乐豆数量',
bet_number CHAR(100) NOT NULL COMMENT '投注号码',
bet_status TINYINT(1) NOT NULL COMMENT '中奖状态：0待开奖、1已中奖、2未中奖',
bet_level SMALLINT(3) NOT NULL DEFAULT '0' COMMENT '中奖等级。有些游戏是没有等级的。默认就是0。根据游戏特点选择是否使用此字段。',
reward_ledou INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '中奖乐豆',
modified_time INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改时间戳',
created_time INT(11) UNSIGNED NOT NULL COMMENT '投注时间戳',
PRIMARY KEY(id),
KEY(bet_id)
) ENGINE = InnoDB DEFAULT CHARSET UTF8 COMMENT '投注号码记录表';


DROP TABLE IF EXISTS `gm_game`;
CREATE TABLE gm_game(
game_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '游戏ID',
game_name CHAR(50) NOT NULL COMMENT '游戏名称',
game_code CHAR(20) NOT NULL COMMENT '游戏编码',
modified_by INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改人',
modified_time INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改时间戳',
created_time INT(11) UNSIGNED NOT NULL COMMENT '创建时间戳',
created_by INT(11) UNSIGNED NOT NULL COMMENT '管理员账号ID',
PRIMARY KEY(game_id)
) ENGINE = InnoDB DEFAULT CHARSET UTF8 COMMENT '游戏种类表';
INSERT INTO `gm_game` (`game_id`, `game_name`, `game_code`, `modified_by`, `modified_time`, `created_time`, `created_by`) VALUES ('1', '双色球', 'ssq', '0', '0', unix_timestamp(now()), '1');
INSERT INTO `gm_game` (`game_id`, `game_name`, `game_code`, `modified_by`, `modified_time`, `created_time`, `created_by`) VALUES ('2', '大乐透', 'dlt', '0', '0', unix_timestamp(now()), '1');
INSERT INTO `gm_game` (`game_id`, `game_name`, `game_code`, `modified_by`, `modified_time`, `created_time`, `created_by`) VALUES ('3', '时时彩', 'ssc', '0', '0', unix_timestamp(now()), '1');


# 一元购只有点击开启才会创建第一期的期号数据。
# 如果是中途停止之后再开启的，则判断之前的期号再后延。
DROP TABLE IF EXISTS `gm_yyg`;
CREATE TABLE gm_yyg(
yyg_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
qh_number INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '当前正在进行的期号',
yyg_name CHAR(50) NOT NULL COMMENT '一元购活动名称',
yyg_image_url CHAR(80) NOT NULL COMMENT '活动主图',
yyg_desc CHAR(250) NOT NULL COMMENT '一元购活动介绍',
yyg_price INT(11) UNSIGNED NOT NULL COMMENT '一元购价格',
yyg_richtext VARCHAR(1000) NOT NULL COMMENT '一元购图文详情',
listorder SMALLINT(1) NOT NULL DEFAULT '0' COMMENT '排序。小在前',
yyg_start TINYINT(1) NOT NULL COMMENT '一元购开启状态：1开启、0关闭',
status TINYINT(1) NOT NULL COMMENT '状态：0无效、1正常、2删除',
modified_by INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改人',
modified_time INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改时间戳',
created_time INT(11) UNSIGNED NOT NULL COMMENT '创建时间戳',
created_by INT(11) UNSIGNED NOT NULL COMMENT '创建人',
PRIMARY KEY(yyg_id)
) ENGINE = InnoDB DEFAULT CHARSET UTF8 COMMENT '一元购活动列表';


# 一元购期号表
DROP TABLE IF EXISTS `gm_yyg_qh`;
CREATE TABLE gm_yyg_qh(
qh_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
qh_number INT(11) UNSIGNED NOT NULL COMMENT '期号',
yyg_id INT(11) UNSIGNED NOT NULL COMMENT '一元购ID',
ok_times INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '已参与人次',
winner_id INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '中奖用户ID',
winner_time INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '中奖时间',
modified_time INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改时间戳',
created_time INT(11) UNSIGNED NOT NULL COMMENT '创建时间戳',
PRIMARY KEY(qh_id)
) ENGINE = InnoDB DEFAULT CHARSET UTF8 COMMENT '一元购期号表';


# 一元购活动相册图片最多允许5张。
DROP TABLE IF EXISTS gm_yyg_image;
CREATE TABLE gm_yyg_image(
image_id INT(11) UNSIGNED AUTO_INCREMENT COMMENT '主键ID',
yyg_id INT(11) UNSIGNED NOT NULL COMMENT '一元购活动ID',
image_url VARCHAR(100) NOT NULL COMMENT '图片URL',
status TINYINT(1) NOT NULL COMMENT '状态：0无效、1正常、2删除',
modified_by INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改人',
modified_time INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改时间戳',
created_time INT(11) UNSIGNED NOT NULL COMMENT '创建时间戳',
created_by INT(11) UNSIGNED NOT NULL COMMENT '创建人',
PRIMARY KEY(image_id),
KEY(yyg_id)
) ENGINE = InnoDB DEFAULT CHARSET UTF8 COMMENT '一元购活动相册';


# 一元购参与记录
# 参与记录按照时间分表
DROP TABLE IF EXISTS `gm_yyg_history_1`;
CREATE TABLE gm_yyg_history_1(
id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
yyg_id INT(11) UNSIGNED NOT NULL COMMENT '一元购ID',
qh_number INT(11) UNSIGNED NOT NULL COMMENT '期号',
user_id INT(11) UNSIGNED NOT NULL COMMENT '参与用户ID',
do_times INT(11) UNSIGNED NOT NULL COMMENT '用户参与人次',
created_time INT(11) UNSIGNED NOT NULL COMMENT '创建时间戳',
PRIMARY KEY(id),
KEY(user_id),
KEY(yyg_id, qh_number)
) ENGINE = InnoDB DEFAULT CHARSET UTF8 COMMENT '一元购参与记录';

DROP TABLE IF EXISTS `gm_yyg_history_2`;
CREATE TABLE gm_yyg_history_2(
id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
yyg_id INT(11) UNSIGNED NOT NULL COMMENT '一元购ID',
qh_number INT(11) UNSIGNED NOT NULL COMMENT '期号',
user_id INT(11) UNSIGNED NOT NULL COMMENT '参与用户ID',
do_times INT(11) UNSIGNED NOT NULL COMMENT '用户参与人次',
created_time INT(11) UNSIGNED NOT NULL COMMENT '创建时间戳',
PRIMARY KEY(id),
KEY(user_id),
KEY(yyg_id, qh_number)
) ENGINE = InnoDB DEFAULT CHARSET UTF8 COMMENT '一元购参与记录';

DROP TABLE IF EXISTS `gm_yyg_history_3`;
CREATE TABLE gm_yyg_history_3(
id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
yyg_id INT(11) UNSIGNED NOT NULL COMMENT '一元购ID',
qh_number INT(11) UNSIGNED NOT NULL COMMENT '期号',
user_id INT(11) UNSIGNED NOT NULL COMMENT '参与用户ID',
do_times INT(11) UNSIGNED NOT NULL COMMENT '用户参与人次',
created_time INT(11) UNSIGNED NOT NULL COMMENT '创建时间戳',
PRIMARY KEY(id),
KEY(user_id),
KEY(yyg_id, qh_number)
) ENGINE = InnoDB DEFAULT CHARSET UTF8 COMMENT '一元购参与记录';

DROP TABLE IF EXISTS `gm_yyg_history_4`;
CREATE TABLE gm_yyg_history_4(
id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
yyg_id INT(11) UNSIGNED NOT NULL COMMENT '一元购ID',
qh_number INT(11) UNSIGNED NOT NULL COMMENT '期号',
user_id INT(11) UNSIGNED NOT NULL COMMENT '参与用户ID',
do_times INT(11) UNSIGNED NOT NULL COMMENT '用户参与人次',
created_time INT(11) UNSIGNED NOT NULL COMMENT '创建时间戳',
PRIMARY KEY(id),
KEY(user_id),
KEY(yyg_id, qh_number)
) ENGINE = InnoDB DEFAULT CHARSET UTF8 COMMENT '一元购参与记录';

DROP TABLE IF EXISTS `gm_yyg_history_5`;
CREATE TABLE gm_yyg_history_5(
id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
yyg_id INT(11) UNSIGNED NOT NULL COMMENT '一元购ID',
qh_number INT(11) UNSIGNED NOT NULL COMMENT '期号',
user_id INT(11) UNSIGNED NOT NULL COMMENT '参与用户ID',
do_times INT(11) UNSIGNED NOT NULL COMMENT '用户参与人次',
created_time INT(11) UNSIGNED NOT NULL COMMENT '创建时间戳',
PRIMARY KEY(id),
KEY(user_id),
KEY(yyg_id, qh_number)
) ENGINE = InnoDB DEFAULT CHARSET UTF8 COMMENT '一元购参与记录';

# --------------- 游戏相关 end   ------------#


# --------------- 商城 start   ------------#

# 商城字典初始化。
INSERT INTO `ms_dict_type` (`dict_type_id`, `type_code`, `type_name`, `description`, `status`, `created_by`, `created_time`, `modified_by`, `modified_time`) VALUES ('30', 'order_operation_code', '订单操作编码', '订单操作编码：标识下单之后，买家或卖家对订单的操作。', '1', '1', unix_timestamp(now()), '0', '0');
INSERT INTO `ms_dict` (`dict_type_id`, `dict_code`, `dict_value`, `description`, `listorder`, `status`, `created_by`, `created_time`, `modified_by`, `modified_time`) VALUES ('30', 'payment', '订单支付', '订单支付', '0', '1', '1', unix_timestamp(now()), '0', '0');
INSERT INTO `ms_dict` (`dict_type_id`, `dict_code`, `dict_value`, `description`, `listorder`, `status`, `created_by`, `created_time`, `modified_by`, `modified_time`) VALUES ('30', 'shipped', '订单发货', '订单发货', '0', '1', '1', unix_timestamp(now()), '0', '0');
INSERT INTO `ms_dict` (`dict_type_id`, `dict_code`, `dict_value`, `description`, `listorder`, `status`, `created_by`, `created_time`, `modified_by`, `modified_time`) VALUES ('30', 'trade_successed', '交易成功', '交易成功或确认收货', '0', '1', '1', unix_timestamp(now()), '0', '0');
INSERT INTO `ms_dict` (`dict_type_id`, `dict_code`, `dict_value`, `description`, `listorder`, `status`, `created_by`, `created_time`, `modified_by`, `modified_time`) VALUES ('30', 'canceled', '买家订单取消', '订单取消。只能由买家操作才能变成这个状态。', '0', '1', '1', unix_timestamp(now()), '0', '0');
INSERT INTO `ms_dict` (`dict_type_id`, `dict_code`, `dict_value`, `description`, `listorder`, `status`, `created_by`, `created_time`, `modified_by`, `modified_time`) VALUES ('30', 'closed', '商家订单关闭', '订单关闭。只能由商家操作才能变成这个状态。', '0', '1', '1', unix_timestamp(now()), '0', '0');
INSERT INTO `ms_dict` (`dict_type_id`, `dict_code`, `dict_value`, `description`, `listorder`, `status`, `created_by`, `created_time`, `modified_by`, `modified_time`) VALUES ('30', 'edit_address', '商家编辑收货地址', '商家编辑收货地址。当下单用户填写了错误的收货地址之后，可以要求商家修改。', '0', '1', '1', unix_timestamp(now()), '0', '0');
INSERT INTO `ms_dict` (`dict_type_id`, `dict_code`, `dict_value`, `description`, `listorder`, `status`, `created_by`, `created_time`, `modified_by`, `modified_time`) VALUES ('30', 'edit_logistics', '商家修改物流信息', '商家修改物流信息', '0', '1', '1', unix_timestamp(now()), '0', '0');
INSERT INTO `ms_dict` (`dict_type_id`, `dict_code`, `dict_value`, `description`, `listorder`, `status`, `created_by`, `created_time`, `modified_by`, `modified_time`) VALUES ('30', 'deleted_order', '删除订单', '删除订单', '0', '1', '1', unix_timestamp(now()), '0', '0');

INSERT INTO `ms_dict_type` (`dict_type_id`, `type_code`, `type_name`, `description`, `status`, `created_by`, `created_time`, `modified_by`, `modified_time`) VALUES ('40', 'logistics_list', '常用快递', '常用快递', '1', '1', unix_timestamp(now()), '0', '0');
INSERT INTO `ms_dict` (`dict_type_id`, `dict_code`, `dict_value`, `description`, `listorder`, `status`, `created_by`, `created_time`, `modified_by`, `modified_time`) VALUES ('40', 'sf', '顺风速递', '顺风速递', '0', '1', '1', unix_timestamp(now()), '0', '0');
INSERT INTO `ms_dict` (`dict_type_id`, `dict_code`, `dict_value`, `description`, `listorder`, `status`, `created_by`, `created_time`, `modified_by`, `modified_time`) VALUES ('40', 'ems', '邮政EMS', '邮政EMS', '0', '1', '1', unix_timestamp(now()), '0', '0');
INSERT INTO `ms_dict` (`dict_type_id`, `dict_code`, `dict_value`, `description`, `listorder`, `status`, `created_by`, `created_time`, `modified_by`, `modified_time`) VALUES ('40', 'yt', '圆通速递', '圆通速递', '0', '1', '1', unix_timestamp(now()), '0', '0');
INSERT INTO `ms_dict` (`dict_type_id`, `dict_code`, `dict_value`, `description`, `listorder`, `status`, `created_by`, `created_time`, `modified_by`, `modified_time`) VALUES ('40', 'st', '申通速递', '申通速递', '0', '1', '1', unix_timestamp(now()), '0', '0');
INSERT INTO `ms_dict` (`dict_type_id`, `dict_code`, `dict_value`, `description`, `listorder`, `status`, `created_by`, `created_time`, `modified_by`, `modified_time`) VALUES ('40', 'zt', '中通快递', '中通快递', '0', '1', '1', unix_timestamp(now()), '0', '0');
INSERT INTO `ms_dict` (`dict_type_id`, `dict_code`, `dict_value`, `description`, `listorder`, `status`, `created_by`, `created_time`, `modified_by`, `modified_time`) VALUES ('40', 'tt', '天天快递', '天天快递', '0', '1', '1', unix_timestamp(now()), '0', '0');
INSERT INTO `ms_dict` (`dict_type_id`, `dict_code`, `dict_value`, `description`, `listorder`, `status`, `created_by`, `created_time`, `modified_by`, `modified_time`) VALUES ('40', 'yd', '韵达快递', '韵达快递', '0', '1', '1', unix_timestamp(now()), '0', '0');
INSERT INTO `ms_dict` (`dict_type_id`, `dict_code`, `dict_value`, `description`, `listorder`, `status`, `created_by`, `created_time`, `modified_by`, `modified_time`) VALUES ('40', 'htky', '百世快递', '百世快递', '0', '1', '1', unix_timestamp(now()), '0', '0');
INSERT INTO `ms_dict` (`dict_type_id`, `dict_code`, `dict_value`, `description`, `listorder`, `status`, `created_by`, `created_time`, `modified_by`, `modified_time`) VALUES ('40', 'qfkd', '全峰快递', '全峰快递', '0', '1', '1', unix_timestamp(now()), '0', '0');
INSERT INTO `ms_dict` (`dict_type_id`, `dict_code`, `dict_value`, `description`, `listorder`, `status`, `created_by`, `created_time`, `modified_by`, `modified_time`) VALUES ('40', 'dbwl', '德邦物流', '德邦物流', '0', '1', '1', unix_timestamp(now()), '0', '0');
INSERT INTO `ms_dict` (`dict_type_id`, `dict_code`, `dict_value`, `description`, `listorder`, `status`, `created_by`, `created_time`, `modified_by`, `modified_time`) VALUES ('40', 'rufengda', '如风达快递', '如风达快递', '0', '1', '1', unix_timestamp(now()), '0', '0');
INSERT INTO `ms_dict` (`dict_type_id`, `dict_code`, `dict_value`, `description`, `listorder`, `status`, `created_by`, `created_time`, `modified_by`, `modified_time`) VALUES ('40', 'zjs', '宅急送', '宅急送', '0', '1', '1', unix_timestamp(now()), '0', '0');

INSERT INTO `ms_config` (`ctitle`, `cname`, `dev_value`, `test_value`, `product_value`, `description`, `status`, `created_by`, `created_time`, `modified_by`, `modified_time`) VALUES ('用户收货地址最大数量', 'max_user_address_count', '20', '20', '20', '允许创建的用户地址最大数量值。', '1', '1', unix_timestamp(now()), '0', '0');

DROP TABLE IF EXISTS mall_shop;
CREATE TABLE mall_shop(
shop_id INT(11) UNSIGNED AUTO_INCREMENT COMMENT '商家ID',
shop_name CHAR(20) NOT NULL COMMENT '商家名称',
shop_logo CHAR(80) NOT NULL DEFAULT '' COMMENT '商家LOGO',
shop_notice CHAR(250) NOT NULL DEFAULT '' COMMENT '商家公告。比如：由于商家探亲，暂停营业3天。',
link_man CHAR(20) NOT NULL DEFAULT '' COMMENT '联系人',
mobilephone CHAR(11) NOT NULL DEFAULT '' COMMENT '商家对外联系手机。用作联系商家使用。',
telephone CHAR(12) NOT NULL DEFAULT '' COMMENT '座机号码',
qq CHAR(15) NOT NULL DEFAULT '' COMMENT '联系QQ。通过网页可以直接与QQ联通聊天。',
user_id INT(11) UNSIGNED NOT NULL COMMENT '用户ID。即该店铺最高管理者,所有人',
max_goods_count INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '允许发布的商品数量。0不限制，其他值代表允许发布的数量',
is_allow_delete_comment TINYINT(1) NOT NULL DEFAULT '0' COMMENT '是否允许商家删除用户评论：0否、1是',
is_lock TINYINT(1) NOT NULL DEFAULT '0' COMMENT '是否锁定冻结商家。一旦锁定冻结商家只能以只读方式进入商家中心。',
status TINYINT(1) NOT NULL COMMENT '商家状态：0无效、1正常、2删除',
modified_by INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改人',
modified_time INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改时间戳',
created_time INT(11) UNSIGNED NOT NULL COMMENT '创建时间戳',
created_by INT(11) UNSIGNED NOT NULL COMMENT '创建人',
PRIMARY KEY(shop_id),
KEY(shop_name)
) ENGINE = InnoDB DEFAULT CHARSET UTF8 COMMENT '商家表';

DROP TABLE IF EXISTS mall_shop_admin;
CREATE TABLE mall_shop_admin(
admin_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '商家管理员ID',
user_id INT(11) UNSIGNED NOT NULL COMMENT '用户ID',
shop_id INT(11) UNSIGNED NOT NULL COMMENT '商家ID',
admin_type CHAR(10) NOT NULL COMMENT '管理员类型：admin、service',
invite_status TINYINT(1) NOT NULL COMMENT '邀请状态：0被邀请人未处理、1被邀请人同意、2被邀请人拒绝',
status TINYINT(1) NOT NULL DEFAULT '0' COMMENT '状态：0无效、1正常、2删除',
modified_by INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改人',
modified_time INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改时间戳',
created_time INT(11) UNSIGNED NOT NULL COMMENT '创建时间戳',
created_by INT(11) UNSIGNED NOT NULL COMMENT '创建人',
PRIMARY KEY(admin_id),
KEY(shop_id),
KEY(user_id)
) ENGINE = InnoDB DEFAULT CHARSET UTF8 COMMENT '商家管理员表';

# 只允许两级菜单。
DROP TABLE IF EXISTS mall_shop_auth;
CREATE TABLE mall_shop_auth(
menu_id SMALLINT(6) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
name CHAR(40) NOT NULL DEFAULT '商家中心菜单名称',
shop_id INT(11) UNSIGNED NOT NULL COMMENT '商家ID',
shop_admin_type CHAR(20) NOT NULL COMMENT '商家管理类型',
parentid SMALLINT(6) NOT NULL DEFAULT '0',
m CHAR(50) NOT NULL DEFAULT '',
c CHAR(50) NOT NULL DEFAULT '',
a CHAR(50) NOT NULL DEFAULT '',
data CHAR(255) NOT NULL DEFAULT '',
listorder SMALLINT(6) UNSIGNED NOT NULL DEFAULT '0',
display ENUM('1','0') NOT NULL DEFAULT '1',
PRIMARY KEY (`menu_id`),
KEY `listorder` (`listorder`),
KEY `parentid` (`parentid`),
KEY `module` (`m`,`c`,`a`),
KEY(shop_id)
) ENGINE = InnoDB DEFAULT CHARSET UTF8 COMMENT '商家权限表';

DROP TABLE IF EXISTS mall_shop_category;
CREATE TABLE mall_shop_category(
cat_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '分类ID',
cat_name VARCHAR(50) NOT NULL COMMENT '分类名称',
shop_id INT(11) UNSIGNED NOT NULL COMMENT '商家ID',
status TINYINT(1) NOT NULL DEFAULT '0' COMMENT '状态：0无效、1正常、2删除',
listorder SMALLINT(5) NOT NULL DEFAULT '0' COMMENT '排序值。小到大排列。',
modified_by INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改人用户ID',
modified_time INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改时间戳',
created_time INT(11) UNSIGNED NOT NULL COMMENT '创建时间戳',
created_by INT(11) UNSIGNED NOT NULL COMMENT '创建人用户ID',
PRIMARY KEY(cat_id)
) ENGINE = InnoDB DEFAULT CHARSET UTF8 COMMENT '商家商品自定义分类';


DROP TABLE IF EXISTS mall_freight_tpl;
CREATE TABLE mall_freight_tpl(
tpl_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '运费模板ID',
shop_id INT(11) UNSIGNED NOT NULL COMMENT '商家ID',
freight_name CHAR(20) NOT NULL COMMENT '运费模板名称',
send_time SMALLINT(5) UNSIGNED NOT NULL DEFAULT '12' COMMENT '发货时间。单位(小时)。0代表立即发货。',
bear_freight TINYINT(1) NOT NULL COMMENT '运费承担：1卖家包邮、2买家承担运费（通过规则运费可能为0）',
fright_type TINYINT(1) NOT NULL DEFAULT '1' COMMENT '计费类型：1计件、2计重',
base_step INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '基础计费步长',
base_freight INT(11) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '基础步长运费。可以设置为0。',
rate_step INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '计费步长。设置为0代表取消按步长记费',
step_freight INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '每步长计费多少钱。设置为0代表不计费',
no_area VARCHAR(1000) NOT NULL DEFAULT '' COMMENT '不配送区域。格式：1111,222,333。一般不配送区域是只港澳台西藏内蒙古新疆。最多只允许设置100个。',
baoyou_fee INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '商品满多少元包邮。设置为0取消此条件。',
status TINYINT(1) NOT NULL COMMENT '状态：0无效、1正常、2删除',
modified_by INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改人用户ID',
modified_time INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改时间戳',
created_time INT(11) UNSIGNED NOT NULL COMMENT '创建时间戳',
created_by INT(11) UNSIGNED NOT NULL COMMENT '创建人用户ID',
PRIMARY KEY(tpl_id),
KEY(shop_id)
) ENGINE = InnoDB DEFAULT CHARSET UTF8 COMMENT '商品运费模板';


DROP TABLE IF EXISTS `mall_appraise`;
CREATE TABLE mall_appraise (
aid INT(11) UNSIGNED AUTO_INCREMENT COMMENT '评价ID',
shop_id INT(11) UNSIGNED NOT NULL COMMENT '商家ID',
t1 INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '宝贝描述相符评价总次数',
s1 INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '宝贝描述相符总分',
p1 DOUBLE(8,2) NOT NULL DEFAULT '5.0' COMMENT '宝贝描述相符平均分',
t2 INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '卖家服务态度评价总次数',
s2 INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '卖家服务态度总分',
p2 DOUBLE(8,2) NOT NULL DEFAULT '5.0' COMMENT '卖家服务态度平均分',
t3 INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '物流服务质量评价总次数',
s3 INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '物流服务质量总分',
p3 DOUBLE(8,2) NOT NULL DEFAULT '5.0' COMMENT '物流服务质量平均分',
modified_time INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改时间戳',
created_time INT(11) UNSIGNED NOT NULL COMMENT '创建时间戳',
PRIMARY KEY(aid),
UNIQUE KEY(shop_id)
)ENGINE = InnoDB DEFAULT CHARSET = 'UTF8' COMMENT '评价表';


# 一个货品一条评价记录。
# 初评必须一次性评价订单中所有的货品。
DROP TABLE IF EXISTS `mall_appraise_detail`;
CREATE TABLE mall_appraise_detail (
aid INT(11) UNSIGNED AUTO_INCREMENT COMMENT '评价ID',
shop_id INT(11) UNSIGNED NOT NULL COMMENT '商家ID',
order_id INT(11) UNSIGNED NOT NULL COMMENT '订单ID。对应mall_order.order_id',
sub_order_id INT(11) UNSIGNED NOT NULL COMMENT '订单ID。对应mall_order_item.sub_order_id',
goods_id INT(11) UNSIGNED NOT NULL COMMENT '商品ID。对应mall_goods.goods_id',
product_id INT(11) UNSIGNED NOT NULL COMMENT '货品ID。对应mall_product.product_id',
user_id INT(11) UNSIGNED NOT NULL COMMENT '用户ID。对应ms_user.user_id',
score1 DOUBLE(8,2) NOT NULL COMMENT '宝贝描述相符评分',
score2 DOUBLE(8,2) NOT NULL COMMENT '卖家服务态度评分',
score3 DOUBLE(8,2) NOT NULL COMMENT '物流服务质量评分',
client_ip CHAR(15) NOT NULL COMMENT '用户IP',
created_time INT(11) UNSIGNED NOT NULL COMMENT '创建时间',
PRIMARY KEY(aid),
KEY(order_id),
KEY(user_id),
UNIQUE KEY(sub_order_id)
)ENGINE = InnoDB DEFAULT CHARSET = 'UTF8' COMMENT '评价详情表';

DROP TABLE IF EXISTS `mall_comment`;
CREATE TABLE mall_comment (
cid INT(11) UNSIGNED AUTO_INCREMENT COMMENT '评价ID',
shop_id INT(11) UNSIGNED NOT NULL COMMENT '商家ID',
order_id INT(11) UNSIGNED NOT NULL COMMENT '订单ID。对应mall_order.order_id',
sub_order_id INT(11) UNSIGNED NOT NULL COMMENT '订单ID。对应mall_order_item.sub_order_id',
goods_id INT(11) UNSIGNED NOT NULL COMMENT '商品ID。对应mall_goods.goods_id',
product_id INT(11) UNSIGNED NOT NULL COMMENT '货品ID。对应mall_product.product_id',
user_id INT(11) UNSIGNED NOT NULL COMMENT '用户ID。对应ms_user.user_id',
content1 CHAR(200) NOT NULL COMMENT '主评',
content1_time INT(11) UNSIGNED NOT NULL COMMENT '主评时间',
reply1 CHAR(200) NOT NULL DEFAULT '' COMMENT '主评回复',
reply1_time INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '主评回复时间',
content2 CHAR(200) NOT NULL DEFAULT '' COMMENT '追评',
content2_time INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '追评时间',
reply2 CHAR(200) NOT NULL DEFAULT '' COMMENT '追评回复',
reply2_time INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '追评回复时间',
client_ip CHAR(15) NOT NULL COMMENT '用户IP',
is_display TINYINT(1) NOT NULL DEFAULT '1' COMMENT '是否显示',
PRIMARY KEY(cid),
KEY(order_id),
KEY(user_id),
UNIQUE KEY(sub_order_id)
)ENGINE = InnoDB DEFAULT CHARSET = 'UTF8' COMMENT '买家评论表';


# 商品规格值
# spec_val_json = [
# '颜色' => ['红色', '金色', '白银'],
# '尺寸' => ['35', '36', '38', '39']
# ];
DROP TABLE IF EXISTS mall_goods;
CREATE TABLE mall_goods(
goods_id INT(11) UNSIGNED AUTO_INCREMENT COMMENT '商品ID',
goods_name VARCHAR(100) NOT NULL COMMENT '商品名称',
shop_id INT(11) UNSIGNED NOT NULL COMMENT '商家ID',
cat_code VARCHAR(50) NOT NULL COMMENT '商品分类编码。对应ms_category.cat_code',
custom_cat_id INT(11) NOT NULL DEFAULT '0' COMMENT '自定义商品分类',
slogan VARCHAR(50) NOT NULL DEFAULT '' COMMENT '广告语、标识',
min_market_price DOUBLE(8,2) NOT NULL DEFAULT '0.00' COMMENT '商品最低市场价格',
max_market_price DOUBLE(8,2) NOT NULL DEFAULT '0.00' COMMENT '商品最高市场价格',
min_price DOUBLE(8,2) NOT NULL DEFAULT '0.00' COMMENT '商品最低销售价格',
max_price DOUBLE(8,2) NOT NULL DEFAULT '0.00' COMMENT '商品最高销售价格',
goods_img VARCHAR(100) NOT NULL DEFAULT '' COMMENT '商品图片',
weight INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '重量。单位(g)',
buy_count INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '购买次数',
month_buy_count INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '近30天购买次数',
listorder SMALLINT(5) NOT NULL DEFAULT '0' COMMENT '排序值。小到大排列。',
marketable TINYINT(1) NOT NULL COMMENT '上下架状态：1上架、0下架',
marketable_time INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '上下架时间',
status TINYINT(1) NOT NULL COMMENT '商品状态：0无效、1正常、2删除',
spec_val_json VARCHAR(1000) NOT NULL DEFAULT '' COMMENT '商品规格。json格式。',
limit_count SMALLINT(10) NOT NULL DEFAULT '0' COMMENT '限购数量。0不限购。',
freight_tpl_id INT(11) UNSIGNED NOT NULL COMMENT '运费模板ID',
description TEXT NOT NULL COMMENT '商品详情',
modified_by INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改人',
modified_time INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改时间戳',
created_time INT(11) UNSIGNED NOT NULL COMMENT '创建时间戳',
created_by INT(11) UNSIGNED NOT NULL COMMENT '创建人',
PRIMARY KEY(goods_id),
KEY(shop_id),
KEY(cat_code),
KEY(custom_cat_id)
) ENGINE = InnoDB DEFAULT CHARSET UTF8 COMMENT '商品表';

DROP TABLE IF EXISTS mall_product;
CREATE TABLE mall_product(
product_id INT(11) UNSIGNED AUTO_INCREMENT COMMENT '货品ID',
goods_id INT(11) UNSIGNED NOT NULL COMMENT '商品ID',
market_price DOUBLE(8,2) NOT NULL COMMENT '市场价格',
sales_price DOUBLE(8,2) NOT NULL COMMENT '销售价格',
stock INT(11) UNSIGNED NOT NULL COMMENT '货品库存',
spec_val VARCHAR(100) NOT NULL DEFAULT '' COMMENT '规格值：颜色:红色|尺寸:35',
status TINYINT(1) NOT NULL COMMENT '商品状态：0无效、1正常、2删除',
modified_by INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改人',
modified_time INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改时间戳',
created_time INT(11) UNSIGNED NOT NULL COMMENT '创建时间戳',
created_by INT(11) UNSIGNED NOT NULL COMMENT '创建人',
PRIMARY KEY(product_id),
KEY(goods_id)
) ENGINE = InnoDB DEFAULT CHARSET UTF8 COMMENT '货品表';

# 相册图片最多允许5张。
DROP TABLE IF EXISTS mall_goods_image;
CREATE TABLE mall_goods_image(
image_id INT(11) UNSIGNED AUTO_INCREMENT COMMENT '主键ID',
goods_id INT(11) UNSIGNED NOT NULL COMMENT '商品ID',
image_url VARCHAR(100) NOT NULL COMMENT '图片URL',
status TINYINT(1) NOT NULL COMMENT '状态：0无效、1正常、2删除',
modified_by INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改人',
modified_time INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改时间戳',
created_time INT(11) UNSIGNED NOT NULL COMMENT '创建时间戳',
created_by INT(11) UNSIGNED NOT NULL COMMENT '创建人',
PRIMARY KEY(image_id),
KEY(goods_id)
) ENGINE = InnoDB DEFAULT CHARSET UTF8 COMMENT '商品相册表';

# 最多20个收货地址
DROP TABLE IF EXISTS mall_user_address;
CREATE TABLE mall_user_address(
address_id INT(11) UNSIGNED AUTO_INCREMENT COMMENT '地址ID',
user_id INT(11) UNSIGNED NOT NULL COMMENT '用户ID',
realname CHAR(10) NOT NULL COMMENT '收货人姓名',
zipcode CHAR(6) DEFAULT NULL COMMENT '收货人邮编',
mobilephone CHAR(11) DEFAULT NULL COMMENT '收货人手机',
district_id INT(11) UNSIGNED NOT NULL COMMENT '地区id,ms_district.district_id',
address CHAR(50) NOT NULL COMMENT '收货人地址。除省市区街道后的部分',
is_default TINYINT(1) NOT NULL DEFAULT '0' COMMENT '是否默认收货地址：0否、1是',
status TINYINT(1) NOT NULL COMMENT '状态：0无效、1正常、2删除',
modified_time INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改时间戳',
created_time INT(11) UNSIGNED NOT NULL COMMENT '创建时间戳',
PRIMARY KEY(address_id),
KEY(user_id),
KEY(mobilephone),
KEY(district_id)
) ENGINE = InnoDB DEFAULT CHARSET UTF8 COMMENT '用户收货地址表';

DROP TABLE IF EXISTS mall_cart;
CREATE TABLE mall_cart(
id INT(11) UNSIGNED AUTO_INCREMENT COMMENT '主键ID',
user_id INT(11) UNSIGNED NOT NULL COMMENT '用户ID',
shop_id INT(11) UNSIGNED NOT NULL COMMENT '商家ID',
goods_id INT(11) UNSIGNED NOT NULL COMMENT '商品ID',
product_id INT(11) UNSIGNED NOT NULL COMMENT '货品ID',
quantity INT(11) UNSIGNED NOT NULL COMMENT '购买数量',
status TINYINT(1) NOT NULL COMMENT '状态：0无效、1正常、2删除',
modified_time INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改时间戳',
created_time INT(11) UNSIGNED NOT NULL COMMENT '创建时间戳',
PRIMARY KEY(id),
KEY(user_id, product_id)
) ENGINE = InnoDB DEFAULT CHARSET UTF8 COMMENT '用户购物车表';

DROP TABLE IF EXISTS mall_order;
CREATE TABLE mall_order(
order_id INT(11) UNSIGNED AUTO_INCREMENT COMMENT '订单ID',
order_sn CHAR(50) NOT NULL COMMENT '订单号',
user_id INT(11) UNSIGNED NOT NULL COMMENT '用户ID。对应ms_user.user_id',
shop_id INT(11) UNSIGNED NOT NULL COMMENT '商家ID',
total_price DOUBLE(8,2) NOT NULL COMMENT '订单实付金额',
payment_price DOUBLE(8,2) NOT NULL COMMENT '订单实付金额',
pay_status TINYINT(1) NOT NULL DEFAULT '0' COMMENT '支付状态：0未支付、1已支付',
pay_time INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '支付时间戳',
order_status SMALLINT(3) NOT NULL DEFAULT '0' COMMENT '订单状态：0待付款、1已付款、2已发货、3交易成功、4交易关闭、5交易取消',
shipping_time INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '发货时间戳',
done_time INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '交易成功时间戳',
closed_time INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '交易关闭时间戳',
cancel_time INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '交易取消时间戳',
need_invoice TINYINT(1) UNSIGNED NOT NULL COMMENT '是否需要发票：0不需要、1需要',
invoice_type TINYINT(1) UNSIGNED NOT NULL COMMENT '发票类型：1个人、2公司',
invoice_name CHAR(50) NOT NULL DEFAULT '' COMMENT '发票抬头',
receiver_name CHAR(20) NOT NULL COMMENT '收货人姓名',
receiver_province CHAR(20) DEFAULT NULL COMMENT '收货人省，存中文',
receiver_city CHAR(20) DEFAULT NULL COMMENT '收货人市，存中文',
receiver_district CHAR(20) DEFAULT NULL COMMENT '收货人区，存中文',
receiver_street CHAR(20) DEFAULT NULL COMMENT '收货人街道，存中文',
receiver_address CHAR(100) NOT NULL COMMENT '收货人地址',
receiver_zip CHAR(6) DEFAULT NULL COMMENT '收货人邮编',
receiver_mobile CHAR(11) DEFAULT NULL COMMENT '收货人手机',
buyer_message CHAR(50) DEFAULT NULL COMMENT '买家留言，给卖家看的',
freight_price DOUBLE(8,2) NOT NULL DEFAULT '0.00' COMMENT '运费',
comment_status TINYINT(1) NOT NULL DEFAULT '0' COMMENT '评论状态：0未评论、1已评论',
reply_status TINYINT(1) NOT NULL DEFAULT '0' COMMENT '回复状态：0未回复、1已回复',
refund_status TINYINT(1) NOT NULL DEFAULT '0' COMMENT '退款状态：0未退款、1部分退款中、2整单退款中、3卖家拒绝退款、4买家取消退款',
status TINYINT(1) NOT NULL COMMENT '状态：0无效、1正常、2删除',
modified_by INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改人',
modified_time INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改时间戳',
created_time INT(11) UNSIGNED NOT NULL COMMENT '下单时间戳',
created_by INT(11) UNSIGNED NOT NULL COMMENT '创建人',
PRIMARY KEY(order_id),
UNIQUE KEY(order_sn),
KEY(user_id, order_status)
) ENGINE = InnoDB DEFAULT CHARSET UTF8 COMMENT '订单主表';

DROP TABLE IF EXISTS mall_order_item;
CREATE TABLE mall_order_item(
sub_order_id INT(11) UNSIGNED AUTO_INCREMENT COMMENT '子订单ID',
order_id INT(11) UNSIGNED NOT NULL COMMENT '主订单ID',
goods_id INT(11) UNSIGNED NOT NULL COMMENT '商品ID',
goods_name CHAR(100) NOT NULL COMMENT '商品名称',
goods_image CHAR(80) NOT NULL COMMENT '商品图片',
product_id INT(11) UNSIGNED NOT NULL COMMENT '货品ID',
spec_val CHAR(100) NOT NULL DEFAULT '' COMMENT '规格值',
market_price DOUBLE(8,2) NOT NULL COMMENT '市场价',
sales_price DOUBLE(8,2) NOT NULL COMMENT '销售价',
is_edit_price TINYINT(1) NOT NULL DEFAULT '0' COMMENT '是否改价。1是、0否。',
old_price DOUBLE(8,2) NOT NULL DEFAULT '0.00' COMMENT '改价前的价格',
quantity SMALLINT(3) UNSIGNED NOT NULL COMMENT '购买数量',
payment_price DOUBLE(8,2) NOT NULL COMMENT '实付金额=销售价*购买数量',
total_price DOUBLE(8,2) NOT NULL COMMENT '商品总额=市场价*购买数量',
comment_status TINYINT(1) NOT NULL DEFAULT '0' COMMENT '评论状态：0未评论、1已初评、2已追评',
reply_status TINYINT(1) NOT NULL DEFAULT '0' COMMENT '回复状态：0未回复、1已回复、2已追加回复',
refund_status TINYINT(1) NOT NULL DEFAULT '0' COMMENT '退款状态：0未退款、1退款中、2卖家拒绝退款、3买家取消退款',
modified_by INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改人',
modified_time INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改时间戳',
created_time INT(11) UNSIGNED NOT NULL COMMENT '创建时间戳',
created_by INT(11) UNSIGNED NOT NULL COMMENT '创建人',
PRIMARY KEY(sub_order_id),
KEY(order_id)
) ENGINE = InnoDB DEFAULT CHARSET UTF8 COMMENT '订单明细表';

DROP TABLE IF EXISTS mall_order_log;
CREATE TABLE mall_order_log(
log_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
order_id INT(11) UNSIGNED NOT NULL COMMENT '主订单ID',
action_type VARCHAR(20) NOT NULL COMMENT '操作类型：canceled取消、closed关闭、edit_address修改收货地址、edit_logistics修改物流信息、trade_successed交易成功、shipped已发货、payment支付',
log_content VARCHAR(1000) NOT NULL DEFAULT '' COMMENT '操作内容。如果是修改地址要把新旧地址放里面。',
user_id INT(11) UNSIGNED NOT NULL COMMENT '操作人',
created_time INT(11) UNSIGNED NOT NULL COMMENT '创建时间戳',
PRIMARY KEY(log_id),
KEY(order_id)
) ENGINE = InnoDB DEFAULT CHARSET UTF8 COMMENT '订单操作日志表';

# 物流单号在未确认收货之前均可修改。
DROP TABLE IF EXISTS mall_logistics;
CREATE TABLE mall_logistics(
id INT(11) UNSIGNED AUTO_INCREMENT COMMENT '主键ID',
order_id INT(11) UNSIGNED NOT NULL COMMENT '订单ID',
logistics_code VARCHAR(20) NOT NULL DEFAULT '' COMMENT '物流编码',
logistics_number VARCHAR(50) NOT NULL DEFAULT '' COMMENT '物流单号',
modified_by INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改人',
modified_time INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改时间戳',
created_time INT(11) UNSIGNED NOT NULL COMMENT '创建时间戳',
created_by INT(11) UNSIGNED NOT NULL COMMENT '创建人',
PRIMARY KEY(id)
) ENGINE = InnoDB DEFAULT CHARSET UTF8 COMMENT '订单物流信息表';

DROP TABLE IF EXISTS mall_payment_log;
CREATE TABLE mall_payment_log(
payment_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
user_id INT(11) UNSIGNED NOT NULL COMMENT '用户ID',
payment_code VARCHAR(20) NOT NULL COMMENT '支付类型编码。对应ms_payment_cfg.payment_code',
order_id INT(11) UNSIGNED NOT NULL COMMENT '主订单ID',
serial_number VARCHAR(50) NOT NULL COMMENT '支付流水号',
amount DOUBLE(8,2) NOT NULL COMMENT '支付金额',
created_time INT(11) UNSIGNED NOT NULL COMMENT '创建时间戳',
PRIMARY KEY(payment_id),
KEY(order_id)
) ENGINE = InnoDB DEFAULT CHARSET UTF8 COMMENT '支付记录表';

DROP TABLE IF EXISTS mall_payment_cfg;
CREATE TABLE mall_payment_cfg(
cfg_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
payment_code VARCHAR(20) NOT NULL COMMENT '支付类型编码。',
modified_by INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改人',
modified_time INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改时间戳',
created_time INT(11) UNSIGNED NOT NULL COMMENT '创建时间戳',
created_by INT(11) UNSIGNED NOT NULL COMMENT '创建人',
PRIMARY KEY(cfg_id)
) ENGINE = InnoDB DEFAULT CHARSET UTF8 COMMENT '支付配置表';

DROP TABLE IF EXISTS mall_coupon;
CREATE TABLE mall_coupon(
coupon_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '优惠券ID',
coupon_name VARCHAR(20) NOT NULL COMMENT '优惠券名称',
money INT(11) UNSIGNED NOT NULL COMMENT '优惠券金额',
order_money INT(11) UNSIGNED NOT NULL COMMENT '订单金额多少可用',
get_start_time INT(11) UNSIGNED NOT NULL COMMENT '领取开始时间',
get_end_time INT(11) UNSIGNED NOT NULL COMMENT '领取截止时间',
limit_quantity SMALLINT(3) UNSIGNED NOT NULL COMMENT '每人限领优惠券数量',
expiry_date INT(11) UNSIGNED NOT NULL COMMENT '使用有效期截止',
shop_id INT(11) UNSIGNED NOT NULL COMMENT '商家ID',
status TINYINT(1) NOT NULL COMMENT '状态：0无效、1正常、2删除',
modified_by INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改人',
modified_time INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改时间戳',
created_time INT(11) UNSIGNED NOT NULL COMMENT '创建时间戳',
created_by INT(11) UNSIGNED NOT NULL COMMENT '创建人',
PRIMARY KEY(coupon_id),
KEY(shop_id)
) ENGINE = InnoDB DEFAULT CHARSET UTF8 COMMENT '优惠券表';

DROP TABLE IF EXISTS mall_user_coupon;
CREATE TABLE mall_user_coupon(
id INT(11) UNSIGNED AUTO_INCREMENT COMMENT '主键ID',
coupon_id INT(11) UNSIGNED NOT NULL COMMENT '优惠券ID',
user_id INT(11) UNSIGNED NOT NULL COMMENT '用户ID',
shop_id INT(11) UNSIGNED NOT NULL COMMENT '商家ID',
is_use TINYINT(1) NOT NULL COMMENT '是否使用',
use_time INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '使用时间',
modified_time INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改时间戳',
created_time INT(11) UNSIGNED NOT NULL COMMENT '创建时间戳',
PRIMARY KEY(id),
KEY(user_id),
KEY(coupon_id),
KEY(shop_id)
) ENGINE = InnoDB DEFAULT CHARSET UTF8 COMMENT '用户优惠券表';
# --------------- 商城 end ------------#



# ------------------ 微信相关 start ------------#
DROP TABLE IF EXISTS `wx_account`;
CREATE TABLE wx_account(
account_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
wx_sn CHAR(32) NOT NULL COMMENT '公众号编码。用在公众号接口配置中。用来识别接口属于哪个公众号',
wx_account VARCHAR(80) NOT NULL COMMENT '微信公众号账号',
wx_type TINYINT(1) NOT NULL COMMENT '公众号类型:1订阅号、2服务号、3企业号',
wx_auth TINYINT(1) NOT NULL COMMENT '公众号是否认证。1是、0否。',
wx_appid VARCHAR(50) NOT NULL COMMENT '微信公众号appid',
wx_appsecret VARCHAR(50) NOT NULL COMMENT '微信公众号密钥',
wx_token CHAR(32) NOT NULL COMMENT '公众号Token。用于验证接口。',
wx_aeskey CHAR(43) NOT NULL COMMENT '公众号EncodingAESKey',
wx_cert_path CHAR(100) NOT NULL DEFAULT '' COMMENT '公众号支付证书地址',
wx_cert_key CHAR(100) NOT NULL DEFAULT '' COMMENT '公众号支付密钥地址',
wx_report_level SMALLINT(3) NOT NULL DEFAULT '1' COMMENT '微信支付上报等级',
wx_proxy_host CHAR(20) NOT NULL DEFAULT '0.0.0.0' COMMENT '支付代理HOST',
wx_proxy_port CHAR(10) NOT NULL DEFAULT '0' COMMENT '支付代理端口',
status TINYINT(1) NOT NULL COMMENT '状态：0无效、1正常、2删除',
modified_by INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改人',
modified_time INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改时间戳',
created_time INT(11) UNSIGNED NOT NULL COMMENT '创建时间戳',
created_by INT(11) UNSIGNED NOT NULL COMMENT '管理员账号ID',
PRIMARY KEY(account_id),
UNIQUE KEY(wx_sn)
) ENGINE = InnoDB DEFAULT CHARSET UTF8 COMMENT '微信公众号表';

DROP TABLE IF EXISTS `wx_menu`;
CREATE TABLE wx_menu(
menu_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
account_id INT(11) UNSIGNED NOT NULL COMMENT '微信公众号ID。关联ms_wx_account.account_id',
menu_name CHAR(20) NOT NULL COMMENT '菜单名称(注意与微信开放的接口规定的长度一致)',
menu_type CHAR(10) NOT NULL COMMENT '菜单类型。click、view',
parentid INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '菜单的父ID',
menu_level TINYINT(1) NOT NULL COMMENT '菜单等级。微信目前只允许两级。所以，只可能出现1和2.',
menu_key CHAR(30) NOT NULL DEFAULT '' COMMENT '菜单key。如果对应的菜单类型没有key请直接忽略。',
is_outside TINYINT(1) NOT NULL DEFAULT '0' COMMENT '是否为站外链接:0否、1是',
outside_url CHAR(255) NOT NULL DEFAULT '' COMMENT '站外链接',
module_name CHAR(50) NOT NULL DEFAULT '' COMMENT '模块名称。组成站内链接的模块名称。',
ctrl_name CHAR(50) NOT NULL DEFAULT '' COMMENT '控制器名称。组成站内链接的控制器名称。',
action_name CHAR(50) NOT NULL DEFAULT '' COMMENT '操作名称。组成站内链接的操作名称。',
url_query CHAR(100) NOT NULL DEFAULT '' COMMENT 'URL附带参数。组成站内链接时的附加参数。格式：username=winer&sex=1',
display TINYINT(1) NOT NULL DEFAULT '0' COMMENT '菜单是否显示。临时性的关闭。',
status TINYINT(1) NOT NULL DEFAULT '0' COMMENT '状态：0无效、1正常、2删除',
modified_by INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改人',
modified_time INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改时间戳',
created_time INT(11) UNSIGNED NOT NULL COMMENT '创建时间戳',
created_by INT(11) UNSIGNED NOT NULL COMMENT '管理员账号ID',
PRIMARY KEY(menu_id)
) ENGINE = InnoDB DEFAULT CHARSET UTF8 COMMENT '微信公众号菜单表';

DROP TABLE IF EXISTS `wx_event`;
CREATE TABLE wx_event(
id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
openid VARCHAR(50) NOT NULL COMMENT '事件发送者openid',
developer VARCHAR(50) NOT NULL DEFAULT '' COMMENT '开发者账号',
msg_type VARCHAR(20) NOT NULL COMMENT '消息类型',
event_xml TEXT NOT NULL COMMENT '事件XML内容',
created_time INT(11) UNSIGNED NOT NULL COMMENT '创建时间戳',
PRIMARY KEY(id)
) ENGINE = InnoDB DEFAULT CHARSET utf8mb4 COMMENT '微信公众号事件记录表';

DROP TABLE IF EXISTS `wx_news`;
CREATE TABLE wx_news(
news_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
account_id INT(11) UNSIGNED NOT NULL COMMENT '微信公众号ID。关联ms_wx_account.account_id',
title CHAR(30) NOT NULL COMMENT '图文消息名称。只作区别用',
is_push TINYINT(1) NOT NULL DEFAULT '0' COMMENT '是否推送到微信公众号。1是、0否。',
push_time INT(11) NOT NULL DEFAULT '0' COMMENT '推送时间戳',
status TINYINT(1) NOT NULL DEFAULT '0' COMMENT '状态：0删除、1正常',
modified_by INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改人',
modified_time INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改时间戳',
created_time INT(11) UNSIGNED NOT NULL COMMENT '创建时间戳',
created_by INT(11) UNSIGNED NOT NULL COMMENT '管理员账号ID',
PRIMARY KEY(news_id),
KEY(account_id),
KEY(title)
) ENGINE = InnoDB DEFAULT CHARSET utf8mb4 COMMENT '微信公众号图文消息表';


DROP TABLE IF EXISTS `wx_news_item`;
CREATE TABLE wx_news_item(
item_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
news_id INT(11) UNSIGNED NOT NULL COMMENT '所属图文消息ID。对应 wx_news.news_id',
title CHAR(30) NOT NULL COMMENT '图文消息标题',
description CHAR(100) NOT NULL COMMENT '图文消息描述',
image_url CHAR(80) NOT NULL COMMENT '图片链接，支持JPG、PNG格式，较好的效果为大图360*200，小图200*200。',
news_url CHAR(100) NOT NULL COMMENT '点击图文消息跳转链接。',
status TINYINT(1) NOT NULL DEFAULT '0' COMMENT '状态：0删除、1正常',
modified_by INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改人',
modified_time INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改时间戳',
created_time INT(11) UNSIGNED NOT NULL COMMENT '创建时间戳',
created_by INT(11) UNSIGNED NOT NULL COMMENT '管理员账号ID',
PRIMARY KEY(item_id),
KEY(news_id)
) ENGINE = InnoDB DEFAULT CHARSET utf8mb4 COMMENT '微信公众号图文文章明细表';
# ------------------ 微信相关 end ------------#

DROP TABLE IF EXISTS ms_favorites;
CREATE TABLE ms_favorites(
id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
user_id INT(11) UNSIGNED NOT NULL COMMENT '用户ID',
obj_type TINYINT(1) NOT NULL COMMENT '收藏类型：1商品收藏、2文章收藏、3问答收藏、4IT题目收藏',
obj_id INT(11) UNSIGNED NOT NULL COMMENT '商品ID/文章ID/问答ID/IT题目ID',
status TINYINT(1) NOT NULL DEFAULT '0' COMMENT '状态：0无效、1正常、2删除',
modified_time INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改时间',
created_time INT(11) UNSIGNED NOT NULL COMMENT '创建时间',
PRIMARY KEY(id),
KEY(user_id)
) ENGINE = InnoDB DEFAULT CHARSET UTF8 COMMENT '用户收藏夹';

CREATE TABLE ms_session (
  session_id varchar(100) NOT NULL COMMENT 'php session_id',
  session_expire int(11) UNSIGNED NOT NULL COMMENT 'session到期时间',
  session_data blob,
  UNIQUE KEY `session_id` (`session_id`)
)ENGINE = MyISAM DEFAULT CHARSET=utf8 COMMENT 'session表';

CREATE TABLE ms_cache (
  cache_key varchar(100) NOT NULL COMMENT '缓存key',
  cache_expire int(11) UNSIGNED NOT NULL COMMENT '缓存到期时间',
  cache_data blob,
  UNIQUE KEY `cache_key` (`cache_key`)
)ENGINE = MyISAM DEFAULT CHARSET=utf8 COMMENT '缓存表';