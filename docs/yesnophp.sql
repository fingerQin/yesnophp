DROP DATABASE IF EXISTS yesnophp;
CREATE DATABASE yesnophp DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
use yesnophp;

DROP TABLE IF EXISTS ms_users;
CREATE TABLE ms_users(
	user_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '用户ID',
	username CHAR(20) NOT NULL COMMENT '账号',
	password CHAR(32) NOT NULL COMMENT '密码',
	salt CHAR(6) NOT NULL COMMENT '密码盐',
	mobilephone CHAR(11) NOT NULL DEFAULT '' COMMENT '手机号码',
	is_verify_mobilephone TINYINT(1) NOT NULL DEFAULT '0' COMMENT '手机验证状态：0未验证、1已验证',
	verify_mobilephone_time INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '手机验证通过时间',
	email CHAR(50) NOT NULL DEFAULT '' COMMENT '邮箱',
	is_verify_email TINYINT(1) NOT NULL DEFAULT '0' COMMENT '邮箱验证状态：0未验证、1已验证',
	verify_email_time INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '邮箱验证通过时间',
	reg_time INT(11) UNSIGNED NOT NULL COMMENT '注册时间',
	PRIMARY KEY(user_id),
	UNIQUE KEY `username_unique` (username),
	UNIQUE KEY `mobilephone_unique` (mobilephone),
	UNIQUE KEY `email_unique` (email)
) ENGINE = InnoDB DEFAULT CHARSET UTF8 COMMENT '用户表';


DROP TABLE IF EXISTS ms_users_data;
CREATE TABLE ms_users_data(
	id INT(11) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
	user_id INT(11) UNSIGNED NOT NULL COMMENT '用户ID',
	realname CHAR(20) NOT NULL DEFAULT '' COMMENT '姓名',
	avatar CHAR(50) NOT NULL DEFAULT '' COMMENT '头像地址',
	signature CHAR(50) NOT NULL DEFAULT '' COMMENT '个性签名',
	PRIMARY KEY(id),
	KEY(user_id)
) ENGINE = InnoDB DEFAULT CHARSET UTF8 COMMENT '用户表副表';


# 用户登录历史表
# 记录用户的登录行为，提供风险评估。
DROP TABLE IF EXISTS ms_users_login;
CREATE TABLE ms_users_login(
	id INT(11) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
	user_id INT(11) UNSIGNED NOT NULL COMMENT '用户ID',
	login_time INT(10) NOT NULL COMMENT '登录时间',
	login_ip CHAR(50) NOT NULL COMMENT '登录IP',
	login_entry TINYINT(1) NOT NULL COMMENT '登录入口：1PC、2APP、3WAP',
	PRIMARY KEY(id),
	KEY(user_id)
) ENGINE = InnoDB DEFAULT CHARSET UTF8 COMMENT '用户登录历史表';


# 此表要保存账号历史禁用记录
DROP TABLE IF EXISTS ms_users_blacklist;
CREATE TABLE ms_users_blacklist(
	id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
	user_id INT(11) UNSIGNED NOT NULL COMMENT '用户ID',
	username CHAR(20) NOT NULL COMMENT '账号',
	ban_type SMALLINT(1) NOT NULL COMMENT '禁用类型：1永久封禁、2临时封禁',
	ban_start_time INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '封禁开始时间',
	ban_end_time INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '封禁截止时间',
	ban_reason CHAR(255) NOT NULL DEFAULT '' COMMENT '账号封禁原因',
	status TINYINT(1) NOT NULL DEFAULT '0' COMMENT '记录状态：0失效、1生效',
	created_by CHAR(30) NOT NULL COMMENT '创建人',
	created_time INT(11) UNSIGNED NOT NULL COMMENT '创建时间',
	modified_by CHAR(30) NOT NULL DEFAULT '' COMMENT '修改人',
	modified_time INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改时间',
	PRIMARY KEY(id),
	KEY(username),
	KEY(user_id)
) ENGINE = InnoDB DEFAULT CHARSET UTF8 COMMENT '用户黑名单表';

# 记录第三方绑定。
DROP TABLE IF EXISTS ms_users_bind;
CREATE TABLE ms_users_bind(
	id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
	user_id INT(11) UNSIGNED NOT NULL COMMENT '用户ID',
	openid VARCHAR(100) NOT NULL COMMENT 'openid',
	created_time INT(11) UNSIGNED NOT NULL COMMENT '创建时间',
	status TINYINT(1) NOT NULL DEFAULT '0' COMMENT '记录状态：0失效、1生效',
	PRIMARY KEY(id),
	KEY(openid),
	KEY(user_id)
) ENGINE = InnoDB DEFAULT CHARSET UTF8 COMMENT '用户绑定表';

# 敏感词表
DROP TABLE IF EXISTS ms_sensitive;
CREATE TABLE ms_sensitive(
	id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
	lv TINYINT(1) NOT NULL DEFAULT '0' COMMENT '敏感等级',
	val VARCHAR(50) NOT NULL COMMENT '敏感词',
	created_by CHAR(30) NOT NULL COMMENT '创建人',
	created_time INT(11) UNSIGNED NOT NULL COMMENT '创建时间',
	modified_by CHAR(30) NOT NULL DEFAULT '' COMMENT '修改人',
	modified_time INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改时间',
	PRIMARY KEY(id),
	KEY(val)
) ENGINE = InnoDB DEFAULT CHARSET UTF8 COMMENT '敏感词表';

# IP黑名单表
DROP TABLE IF EXISTS ms_ip_ban;
CREATE TABLE ms_ip_ban(
	id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
	ip VARCHAR(15) NOT NULL COMMENT 'IP地址',
	remark VARCHAR(255) NOT NULL DEFAULT '' COMMENT '备注',
	created_by CHAR(30) NOT NULL COMMENT '创建人',
	created_time INT(11) UNSIGNED NOT NULL COMMENT '创建时间',
	modified_by CHAR(30) NOT NULL DEFAULT '' COMMENT '修改人',
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
    created_by CHAR(30) NOT NULL COMMENT '类型创建人',
	created_time INT(11) UNSIGNED NOT NULL COMMENT '类型创建时间',
	modified_by CHAR(30) NOT NULL DEFAULT '' COMMENT '类型修改人',
	modified_time INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '类型修改时间',
	PRIMARY KEY(dict_type_id),
	KEY `type_code` (type_code)
) ENGINE = InnoDB DEFAULT CHARSET UTF8 COMMENT '字典类型表';
INSERT INTO ms_dict_type (`dict_type_id`, `type_code`, `type_name`, `description`, `status`, `created_by`, `created_time`, `modified_by`, `modified_time`) 
VALUES ('1', 'category_type_list', '分类类型列表', '此分类类型列表用在分类列表中。', '1', '1', '1459477753', '', '0');


# 字典数据表
DROP TABLE IF EXISTS ms_dict;
CREATE TABLE ms_dict(
	dict_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
	dict_type_id INT(11) UNSIGNED NOT NULL COMMENT '主键',
	dict_code CHAR(50) NOT NULL COMMENT '字典编码',
    dict_name CHAR(255) NOT NULL COMMENT '字典名称',
    description CHAR(255) NOT NULL DEFAULT '' COMMENT '字典类型描述',
    listorder SMALLINT(1) NOT NULL DEFAULT '0' COMMENT '排序。小在前',
    status TINYINT(1) NOT NULL DEFAULT '0' COMMENT '状态：0失效、1有效、2删除',
    created_by CHAR(30) NOT NULL COMMENT '类型创建人',
	created_time INT(11) UNSIGNED NOT NULL COMMENT '类型创建时间',
	modified_by CHAR(30) NOT NULL DEFAULT '' COMMENT '类型修改人',
	modified_time INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '类型修改时间',
	PRIMARY KEY(dict_id),
	KEY(dict_type_id)
) ENGINE = InnoDB DEFAULT CHARSET UTF8 COMMENT '字典数据表';
INSERT INTO ms_dict (`dict_type_id`, `dict_code`, `dict_name`, `description`, `listorder`, `status`, `created_by`, `created_time`, `modified_by`, `modified_time`) 
VALUES ('1', '1', '文章分类', '文章分类的值最好别更改。因为，会影响此分类关联的子分类。如确实要变更，请检查此ID对应的表ms_category的分类是否有值。如果有请处理之后再变更此值。', '0', '1', '1', '1459477892', '', '0'),
('1', '2', ' 友情链接分类', '请别随意更改编码值。因为与它关联的子分类数据会失去依赖。', '0', '1', '1', '1459478428', '', '0'),
('1', '3', '商品分类', '请别随意更改编码值。因为与它关联的子分类数据会失去依赖。', '0', '1', '1', '1460080115', '1', '1460080280');


# 系统配置表
# 一些需要动态修改的配置。
DROP TABLE IF EXISTS ms_config;
CREATE TABLE ms_config(
	config_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
	ctitle CHAR(255) NOT NULL COMMENT '配置标题',
	cname CHAR(255) NOT NULL COMMENT '名称',
	cvalue CHAR(255) NOT NULL COMMENT '值',
	description CHAR(255) NOT NULL DEFAULT '' COMMENT '配置描述',
    status TINYINT(1) NOT NULL DEFAULT '0' COMMENT '状态：0失效、1有效、2删除',
    created_by CHAR(30) NOT NULL COMMENT '类型创建人',
	created_time INT(11) UNSIGNED NOT NULL COMMENT '类型创建时间',
	modified_by CHAR(30) NOT NULL DEFAULT '' COMMENT '类型修改人',
	modified_time INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '类型修改时间',
	PRIMARY KEY(config_id),
	KEY `cname` (cname),
	KEY(cvalue)
) ENGINE = InnoDB DEFAULT CHARSET UTF8 COMMENT '系统配置表';
INSERT INTO ms_config (`ctitle`, `cname`, `cvalue`, `description`, `status`, `created_by`, `created_time`, `modified_by`, `modified_time`)
VALUES('排他登录', 'is_unique_login', '1', '1是、0否。即同一时间账号只能在一个地方登录。不允许账号在其他地方登录。', 1, 'winer', '0', '', 0),
('网站名称', 'site_name', 'yesno', '', 1, 'winer', 0, '', 0),
('PC登录超时时间(分钟)', 'pc_logout_time', '30', '登录超时时间。距离上次最后操作时间大于当前指定时间分钟内将登录超时并退出登录', 1, 'winer', 0, '', 0),
('管理后台域名', 'backend_domain_name', 'http://backend.yesnophp.com/', '涉及到网站页面或资源的链接地址', 1, 'winer', 0, '', 0),
('前台域名', 'frontend_domain_name', 'http://frontend.yesnophp.com/', '涉及到网站页面或资源的链接地址', 1, 'winer', 0, '', 0),
('静态资源域名', 'statics_domain_name', 'http://statics.yesnophp.com/', '涉及到网站页面或资源的链接地址', 1, 'winer', 0, '', 0),
('图片文件资源域名', 'files_domain_name', 'http://files.yesnophp.com/', '涉及到网站图片文件部分', 1, 'winer', 0, '', 0),
('用户权限cookie作用域', 'user_auth_cookie_domain_name', '.yesnophp.com', '即此域下所有域名都可以自动登录', 1, 'winer', 0, '', 0),
('APP登录超时时间(天)', 'app_logout_time', '30', '登录超时时间。距离上次最后操作时间大于当前指定时间分钟内将登录超时并退出登录', 1, 'winer', 0, '', 0),
('管理员cookie作用域', 'admin_cookie_domain', '.backend.yesnophp.com', '为避免cookie值被前台使用，配置的域必须是管理后台的域名。', 1, 'winer', 0, '', 0),
('后台登录超时时间(分钟)', 'admin_logout_time', '30', '超时则需要重新登录', 1, 'winer', 0, '', 0);

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
VALUES(1, '覃礼钧', 'winerqin', 'c7935cc8ee50b752345290d8cf136827', 'abcdef', 1, 0, 1);


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
INSERT INTO ms_admin_role (roleid, rolename, status, is_default, created_time) VALUES(1, '超级管理员', 1, 1, 0);


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
	title VARCHAR(100) NOT NULL COMMENT '文章标题',
	intro VARCHAR(500) NOT NULL COMMENT '文章简介。也是SEO中的description',
	keywords VARCHAR(100) NOT NULL DEFAULT '' COMMENT '文章关键词。也是SEO中的keywords',
	image_url VARCHAR(100) NOT NULL DEFAULT '' COMMENT '文章列表图片',
	source VARCHAR(50) NOT NULL DEFAULT '' COMMENT '文章来源',
	display TINYINT(1) NOT NULL DEFAULT '0' COMMENT '文章是否显示。1显示、0隐藏',
	status TINYINT(1) NOT NULL DEFAULT '0' COMMENT '文章状态：0无效、1正常、2删除',
	listorder SMALLINT(6) UNSIGNED NOT NULL DEFAULT '0' COMMENT '排序。小到大排序。',
	hits INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '文章访问量',
	created_by CHAR(30) NOT NULL COMMENT '创建人',
	created_time INT(11) UNSIGNED NOT NULL COMMENT '创建时间',
	modified_by CHAR(30) NOT NULL DEFAULT '' COMMENT '修改人',
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
	created_by CHAR(30) NOT NULL COMMENT '创建人',
	created_time INT(11) UNSIGNED NOT NULL COMMENT '创建时间',
	modified_by CHAR(30) NOT NULL DEFAULT '' COMMENT '修改人',
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
	created_by CHAR(30) NOT NULL COMMENT '创建人',
	created_time INT(11) UNSIGNED NOT NULL COMMENT '创建时间',
	modified_by CHAR(30) NOT NULL DEFAULT '' COMMENT '修改人',
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
	created_by CHAR(30) NOT NULL COMMENT '创建人',
	created_time INT(11) UNSIGNED NOT NULL COMMENT '创建时间',
	modified_by CHAR(30) NOT NULL DEFAULT '' COMMENT '修改人',
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

(2000, '系统设置',0,'','','','',0,'1'),
(2001, '系统配置',2000,'','','','',0,'1'),
(2002, '字典类型列表',2001,'Index','Dict','index','',0,'1'),
(2003, '添加字典类型',2001,'Index','Dict','addType','',0,'0'),
(2004, '编辑字典类型',2001,'Index','Dict','editType','',0,'0'),
(2005, '删除字典类型',2001,'Index','Dict','deleteType','',0,'0'),
(2006, '字典列表',2001,'Index','Dict','dict','',0,'0'),
(2007, '删除字典',2001,'Index','Dict','delete','',0,'0'),
(2008, '添加字典',2001,'Index','Dict','add','',0,'0'),
(2009, '更新字典',2001,'Index','Dict','edit','',0,'0'),
(2010, '字典类型排序',2001,'Index','Dict','sortType','',0,'0'),
(2011, '字典排序',2001,'Index','Dict','sortDict','',0,'0'),
(2012, '配置列表', 2001, 'Index','Config','index','',0,'1'),
(2013, '添加配置', 2001, 'Index','Config','add','',0,'0'),
(2014, '编辑配置', 2001, 'Index','Config','edit','',0,'0'),
(2015, '删除配置', 2001, 'Index','Config','delete','',0,'0'),
(2016, '配置排序', 2001, 'Index','Config','sort','',0,'0'),
(2017, '菜单列表', 2001, 'Index','Menu','index','',0,'1'),
(2018, '添加菜单', 2001, 'Index','Menu','add','',0,'0'),
(2019, '编辑菜单', 2001, 'Index','Menu','edit','',0,'0'),
(2020, '删除菜单', 2001, 'Index','Menu','delete','',0,'0'),
(2021, '菜单排序', 2001, 'Index','Menu','sort','',0,'0'),

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


(6000, '微信公众号', 0,'','','','',0,'1'),
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
(6011, '用户分组列表',6001,'Index','WeChat','userGroupList','',0,'0'),
(6012, '添加用户分组',6001,'Index','WeChat','addUserGroup','',0,'0'),
(6013, '编辑用户分组',6001,'Index','WeChat','editUserGroup','',0,'1'),
(6014, '删除用户分组',6001,'Index','WeChat','deleteUserGroup','',0,'0'),
(6015, '移动用户至其他分组',6001,'Index','WeChat','moveUserToOtherGroup','',0,'0'),
(6016, '关注用户管理',6001,'Index','WeChat','attentionUserList','',0,'1'),
(6017, '用户详情',6001,'Index','WeChat','userDetail','',0,'0'),
(6018, '设置用户备注名', 6001, 'Index', 'setUserRemarkName', 'invoice', '', '0', '0'),
(6019, '多媒体文件管理', 6001, 'Index', 'WeChat', 'multimediaList', '', '0', '1'),
(6020, '添加多媒体文件', 6001, 'Index', 'WeChat', 'addMultimediaList', '', '0', '0'),
(6021, '删除多媒体文件', 6001, 'Index', 'WeChat', 'deleteMultimediaList', '', '0', '0'),
(6022, '图文素材管理', 6001, 'Index', 'WeChat', 'imageTextList', '', '0', '1'),
(6023, '图文文章列表', 6001, 'Index', 'WeChat', 'invoice', '', '0', '0'),
(6024, '添加图文', 6001, 'Index', 'WeChat', 'addImageText', '', '0', '0'),
(6025, '删除图文', 6001, 'Index', 'WeChat', 'deleteImageText', '', '0', '0'),
(6026, '图文文章列表', 6001, 'Index', 'WeChat', 'imageTextArticleList', '', '0', '0'),
(6027, '添加图文文章', 6001, 'Index', 'WeChat', 'addImageTextArticle', '', '0', '0'),
(6028, '编辑图文文章', 6001, 'Index', 'WeChat', 'editImageTextArticle', '', '0', '0'),
(6029, '删除图文文章', 6001, 'Index', 'WeChat', 'deleteImageTextArticle', '', '0', '0');

# --------------- 游戏相关 start ------------#

### 初始化游戏模型需要的字典数据
INSERT INTO `yesnophp`.`ms_dict_type` (`dict_type_id`, `type_code`, `type_name`, `description`, `status`, `created_by`, `created_time`, `modified_by`, `modified_time`) VALUES ('20', 'game_ledou_consume_code', '游戏乐豆消费编码', '游戏乐豆消费编码：通过此编码可以知道乐豆是在何种情况下消费。比如：add_ssq_reward 代表双色球中奖增加。', '1', '1', '1459778934', '', '0');
INSERT INTO `yesnophp`.`ms_dict` (`dict_type_id`, `dict_code`, `dict_name`, `description`, `listorder`, `status`, `created_by`, `created_time`, `modified_by`, `modified_time`) VALUES ('20', 'add_ssq_reward', '双色球中奖', '双色球中奖', '0', '1', '1', '1459779040', '', '0');
INSERT INTO `yesnophp`.`ms_dict` (`dict_type_id`, `dict_code`, `dict_name`, `description`, `listorder`, `status`, `created_by`, `created_time`, `modified_by`, `modified_time`) VALUES ('20', 'add_dlt_reward', '大乐透中奖', '大乐透中奖', '0', '1', '1', '1459779067', '', '0');
INSERT INTO `yesnophp`.`ms_dict` (`dict_type_id`, `dict_code`, `dict_name`, `description`, `listorder`, `status`, `created_by`, `created_time`, `modified_by`, `modified_time`) VALUES ('20', 'add_ssc_reward', '时时彩中奖', '时时彩中奖', '0', '1', '1', '1459779208', '', '0');
INSERT INTO `yesnophp`.`ms_dict` (`dict_type_id`, `dict_code`, `dict_name`, `description`, `listorder`, `status`, `created_by`, `created_time`, `modified_by`, `modified_time`) VALUES ('20', 'cut_ssq_bet', '双色球投注', '双色球投注', '0', '1', '1', '1459779040', '', '0');
INSERT INTO `yesnophp`.`ms_dict` (`dict_type_id`, `dict_code`, `dict_name`, `description`, `listorder`, `status`, `created_by`, `created_time`, `modified_by`, `modified_time`) VALUES ('20', 'cut_dlt_bet', '大乐透投注', '大乐透投注', '0', '1', '1', '1459779067', '', '0');
INSERT INTO `yesnophp`.`ms_dict` (`dict_type_id`, `dict_code`, `dict_name`, `description`, `listorder`, `status`, `created_by`, `created_time`, `modified_by`, `modified_time`) VALUES ('20', 'cut_ssc_bet', '时时彩投注', '时时彩投注', '0', '1', '1', '1459779208', '', '0');
INSERT INTO `yesnophp`.`ms_dict` (`dict_type_id`, `dict_code`, `dict_name`, `description`, `listorder`, `status`, `created_by`, `created_time`, `modified_by`, `modified_time`) VALUES ('20', 'add_checkin', '每日签到', '每日签到', '0', '1', '1', '1459779678', '', '0');
INSERT INTO `yesnophp`.`ms_dict` (`dict_type_id`, `dict_code`, `dict_name`, `description`, `listorder`, `status`, `created_by`, `created_time`, `modified_by`, `modified_time`) VALUES ('20', 'add_buy_goods', '购买商品赠送', '购买商品赠送', '0', '1', '1', '1459779816', '', '0');


DROP TABLE IF EXISTS `ms_ledou`;
CREATE TABLE ms_ledou(
id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
user_id INT(11) UNSIGNED NOT NULL COMMENT '玩家ID。对应ms_users.user_id',
ledou INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '乐豆数量。包含未用完的赠送的乐豆。',
modified_time INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改时间戳',
created_time INT(11) UNSIGNED NOT NULL COMMENT '创建时间戳',
PRIMARY KEY(id),
KEY(user_id)
) ENGINE = InnoDB DEFAULT CHARSET UTF8 COMMENT '玩家乐豆表';

DROP TABLE IF EXISTS `ms_ledou_consume`;
CREATE TABLE ms_ledou_consume(
id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
user_id INT(11) UNSIGNED NOT NULL COMMENT '玩家ID。对应ms_users.user_id',
consume_type TINYINT(1) NOT NULL COMMENT '消费类型：1增加、2扣减',
consume_code CHAR(20) NOT NULL COMMENT '类型编码。通过编码可以知晓是因何产生的。编码通过字典配置。',
ledou INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '影响的乐豆数量',
created_time INT(11) UNSIGNED NOT NULL COMMENT '创建时间戳',
PRIMARY KEY(id),
KEY(user_id)
) ENGINE = InnoDB DEFAULT CHARSET UTF8 COMMENT '乐豆消费记录';


DROP TABLE IF EXISTS `ms_bet_record`;
CREATE TABLE ms_bet_record(
bet_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
user_id INT(11) UNSIGNED NOT NULL COMMENT '玩家ID。对应ms_users.user_id',
game_id INT(11) UNSIGNED NOT NULL COMMENT '游戏ID',
bet_ledou INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '投注的乐豆数量',
bet_status TINYINT(1) NOT NULL COMMENT '中奖状态：0待开奖、1已中奖、2未中奖',
reward_ledou INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '中奖乐豆',
modified_time INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改时间戳',
created_time INT(11) UNSIGNED NOT NULL COMMENT '投注时间戳',
PRIMARY KEY(bet_id),
KEY(user_id)
) ENGINE = InnoDB DEFAULT CHARSET UTF8 COMMENT '投注记录';


DROP TABLE IF EXISTS `ms_bet_record_number`;
CREATE TABLE ms_bet_record_number(
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


DROP TABLE IF EXISTS `ms_game`;
CREATE TABLE ms_game(
game_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '游戏ID',
game_name CHAR(50) NOT NULL COMMENT '游戏名称',
game_code CHAR(20) NOT NULL COMMENT '游戏编码',
modified_by INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改人',
modified_time INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改时间戳',
created_time INT(11) UNSIGNED NOT NULL COMMENT '创建时间戳',
created_by INT(11) UNSIGNED NOT NULL COMMENT '管理员账号ID',
PRIMARY KEY(game_id)
) ENGINE = InnoDB DEFAULT CHARSET UTF8 COMMENT '游戏种类表';
INSERT INTO `yesnophp`.`ms_game` (`game_id`, `game_name`, `game_code`, `modified_by`, `modified_time`, `created_time`, `created_by`) VALUES ('1', '双色球', 'ssq', '0', '0', '1450000000', '1');
INSERT INTO `yesnophp`.`ms_game` (`game_id`, `game_name`, `game_code`, `modified_by`, `modified_time`, `created_time`, `created_by`) VALUES ('2', '大乐透', 'dlt', '0', '0', '1450000000', '1');
INSERT INTO `yesnophp`.`ms_game` (`game_id`, `game_name`, `game_code`, `modified_by`, `modified_time`, `created_time`, `created_by`) VALUES ('3', '时时彩', 'ssc', '0', '0', '1450000000', '1');

# --------------- 游戏相关 end   ------------#


# --------------- 商城 start   ------------#

# 商城字典初始化。
INSERT INTO `yesnophp`.`ms_dict_type` (`dict_type_id`, `type_code`, `type_name`, `description`, `status`, `created_by`, `created_time`, `modified_by`, `modified_time`) VALUES ('30', 'order_operation_code', '订单操作编码', '订单操作编码：标识下单之后，买家或卖家对订单的操作。', '1', '1', '1459862606', '', '0');
INSERT INTO `yesnophp`.`ms_dict` (`dict_type_id`, `dict_code`, `dict_name`, `description`, `listorder`, `status`, `created_by`, `created_time`, `modified_by`, `modified_time`) VALUES ('30', 'payment', '订单支付', '订单支付', '0', '1', '1', '1459862625', '', '0');
INSERT INTO `yesnophp`.`ms_dict` (`dict_type_id`, `dict_code`, `dict_name`, `description`, `listorder`, `status`, `created_by`, `created_time`, `modified_by`, `modified_time`) VALUES ('30', 'shipped', '订单发货', '订单发货', '0', '1', '1', '1459862636', '', '0');
INSERT INTO `yesnophp`.`ms_dict` (`dict_type_id`, `dict_code`, `dict_name`, `description`, `listorder`, `status`, `created_by`, `created_time`, `modified_by`, `modified_time`) VALUES ('30', 'trade_successed', '交易成功', '交易成功或确认收货', '0', '1', '1', '1459862658', '', '0');
INSERT INTO `yesnophp`.`ms_dict` (`dict_type_id`, `dict_code`, `dict_name`, `description`, `listorder`, `status`, `created_by`, `created_time`, `modified_by`, `modified_time`) VALUES ('30', 'canceled', '买家订单取消', '订单取消。只能由买家操作才能变成这个状态。', '0', '1', '1', '1459862684', '1', '1459863259');
INSERT INTO `yesnophp`.`ms_dict` (`dict_type_id`, `dict_code`, `dict_name`, `description`, `listorder`, `status`, `created_by`, `created_time`, `modified_by`, `modified_time`) VALUES ('30', 'closed', '商家订单关闭', '订单关闭。只能由商家操作才能变成这个状态。', '0', '1', '1', '1459862707', '1', '1459863253');
INSERT INTO `yesnophp`.`ms_dict` (`dict_type_id`, `dict_code`, `dict_name`, `description`, `listorder`, `status`, `created_by`, `created_time`, `modified_by`, `modified_time`) VALUES ('30', 'edit_address', '商家编辑收货地址', '商家编辑收货地址。当下单用户填写了错误的收货地址之后，可以要求商家修改。', '0', '1', '1', '1459863200', '1', '1459863247');
INSERT INTO `yesnophp`.`ms_dict` (`dict_type_id`, `dict_code`, `dict_name`, `description`, `listorder`, `status`, `created_by`, `created_time`, `modified_by`, `modified_time`) VALUES ('30', 'edit_logistics', '商家修改物流信息', '商家修改物流信息', '0', '1', '1', '1459863222', '', '0');
INSERT INTO `yesnophp`.`ms_dict` (`dict_type_id`, `dict_code`, `dict_name`, `description`, `listorder`, `status`, `created_by`, `created_time`, `modified_by`, `modified_time`) VALUES ('30', 'deleted_order', '删除订单', '删除订单', '0', '1', '1', '1460126160', '', '0');

INSERT INTO `yesnophp`.`ms_dict_type` (`dict_type_id`, `type_code`, `type_name`, `description`, `status`, `created_by`, `created_time`, `modified_by`, `modified_time`) VALUES ('40', 'logistics_list', '常用快递', '常用快递', '1', '1', '1459863854', '', '0');
INSERT INTO `yesnophp`.`ms_dict` (`dict_type_id`, `dict_code`, `dict_name`, `description`, `listorder`, `status`, `created_by`, `created_time`, `modified_by`, `modified_time`) VALUES ('40', 'sf', '顺风速递', '顺风速递', '0', '1', '1', '1459863993', '', '0');
INSERT INTO `yesnophp`.`ms_dict` (`dict_type_id`, `dict_code`, `dict_name`, `description`, `listorder`, `status`, `created_by`, `created_time`, `modified_by`, `modified_time`) VALUES ('40', 'ems', '邮政EMS', '邮政EMS', '0', '1', '1', '1459864014', '', '0');
INSERT INTO `yesnophp`.`ms_dict` (`dict_type_id`, `dict_code`, `dict_name`, `description`, `listorder`, `status`, `created_by`, `created_time`, `modified_by`, `modified_time`) VALUES ('40', 'yt', '圆通速递', '圆通速递', '0', '1', '1', '1459864157', '1', '1459864418');
INSERT INTO `yesnophp`.`ms_dict` (`dict_type_id`, `dict_code`, `dict_name`, `description`, `listorder`, `status`, `created_by`, `created_time`, `modified_by`, `modified_time`) VALUES ('40', 'st', '申通速递', '申通速递', '0', '1', '1', '1459864210', '', '0');
INSERT INTO `yesnophp`.`ms_dict` (`dict_type_id`, `dict_code`, `dict_name`, `description`, `listorder`, `status`, `created_by`, `created_time`, `modified_by`, `modified_time`) VALUES ('40', 'zt', '中通快递', '中通快递', '0', '1', '1', '1459864310', '', '0');
INSERT INTO `yesnophp`.`ms_dict` (`dict_type_id`, `dict_code`, `dict_name`, `description`, `listorder`, `status`, `created_by`, `created_time`, `modified_by`, `modified_time`) VALUES ('40', 'tt', '天天快递', '天天快递', '0', '1', '1', '1459864348', '', '0');
INSERT INTO `yesnophp`.`ms_dict` (`dict_type_id`, `dict_code`, `dict_name`, `description`, `listorder`, `status`, `created_by`, `created_time`, `modified_by`, `modified_time`) VALUES ('40', 'yd', '韵达快递', '韵达快递', '0', '1', '1', '1459864366', '', '0');
INSERT INTO `yesnophp`.`ms_dict` (`dict_type_id`, `dict_code`, `dict_name`, `description`, `listorder`, `status`, `created_by`, `created_time`, `modified_by`, `modified_time`) VALUES ('40', 'htky', '百世快递', '百世快递', '0', '1', '1', '1459864463', '', '0');
INSERT INTO `yesnophp`.`ms_dict` (`dict_type_id`, `dict_code`, `dict_name`, `description`, `listorder`, `status`, `created_by`, `created_time`, `modified_by`, `modified_time`) VALUES ('40', 'qfkd', '全峰快递', '全峰快递', '0', '1', '1', '1459864539', '', '0');
INSERT INTO `yesnophp`.`ms_dict` (`dict_type_id`, `dict_code`, `dict_name`, `description`, `listorder`, `status`, `created_by`, `created_time`, `modified_by`, `modified_time`) VALUES ('40', 'dbwl', '德邦物流', '德邦物流', '0', '1', '1', '1459864574', '', '0');
INSERT INTO `yesnophp`.`ms_dict` (`dict_type_id`, `dict_code`, `dict_name`, `description`, `listorder`, `status`, `created_by`, `created_time`, `modified_by`, `modified_time`) VALUES ('40', 'rufengda', '如风达快递', '如风达快递', '0', '1', '1', '1459864651', '', '0');
INSERT INTO `yesnophp`.`ms_dict` (`dict_type_id`, `dict_code`, `dict_name`, `description`, `listorder`, `status`, `created_by`, `created_time`, `modified_by`, `modified_time`) VALUES ('40', 'zjs', '宅急送', '宅急送', '0', '1', '1', '1459864697', '', '0');


INSERT INTO `ms_config` (`ctitle`, `cname`, `cvalue`, `description`, `status`, `created_by`, `created_time`, `modified_by`, `modified_time`) VALUES ('用户收货地址最大数量', 'max_user_address_count', '20', '允许创建的用户地址最大数量值。', '1', '1', '1460108076', '', '0');


# 商品规格值
# spec_val_json = [
# '颜色' => ['红色', '金色', '白银'],
# '尺寸' => ['35', '36', '38', '39']
# ]; 
DROP TABLE IF EXISTS ms_goods;
CREATE TABLE ms_goods(
goods_id INT(11) UNSIGNED AUTO_INCREMENT COMMENT '商品ID',
goods_name VARCHAR(100) NOT NULL COMMENT '商品名称',
cat_code VARCHAR(50) NOT NULL COMMENT '商品分类编码。对应ms_category.cat_code',
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
status TINYINT(1) NOT NULL COMMENT '商品状态：0无效、1正常、2删除',
spec_val_json VARCHAR(1000) NOT NULL DEFAULT '' COMMENT '商品规格。json格式。',
description TEXT NOT NULL COMMENT '商品详情',
modified_by INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改人',
modified_time INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改时间戳',
created_time INT(11) UNSIGNED NOT NULL COMMENT '创建时间戳',
created_by INT(11) UNSIGNED NOT NULL COMMENT '创建人',
PRIMARY KEY(goods_id),
KEY(cat_code)
) ENGINE = InnoDB DEFAULT CHARSET UTF8 COMMENT '商品表';

DROP TABLE IF EXISTS ms_product;
CREATE TABLE ms_product(
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
PRIMARY KEY(product_id)
) ENGINE = InnoDB DEFAULT CHARSET UTF8 COMMENT '货品表';

# 相册图片最多允许5张。
DROP TABLE IF EXISTS ms_goods_image;
CREATE TABLE ms_goods_image(
image_id INT(11) UNSIGNED AUTO_INCREMENT COMMENT '主键ID',
goods_id INT(11) UNSIGNED NOT NULL COMMENT '商品ID',
image_url VARCHAR(100) NOT NULL COMMENT '图片URL',
status TINYINT(1) NOT NULL COMMENT '商品状态：0无效、1正常、2删除',
modified_by INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改人',
modified_time INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改时间戳',
created_time INT(11) UNSIGNED NOT NULL COMMENT '创建时间戳',
created_by INT(11) UNSIGNED NOT NULL COMMENT '创建人',
PRIMARY KEY(image_id),
KEY(goods_id)
) ENGINE = InnoDB DEFAULT CHARSET UTF8 COMMENT '商品相册表';

# 最多20个收货地址
DROP TABLE IF EXISTS ms_user_address;
CREATE TABLE ms_user_address(
address_id INT(11) UNSIGNED AUTO_INCREMENT COMMENT '地址ID',
user_id INT(11) UNSIGNED NOT NULL COMMENT '用户ID',
realname CHAR(50) NOT NULL COMMENT '收货人姓名',
zipcode CHAR(6) DEFAULT NULL COMMENT '收货人邮编',
mobilephone CHAR(11) DEFAULT NULL COMMENT '收货人手机',
district_code CHAR(20) NOT NULL COMMENT '地区code编码',
region_type SMALLINT(3) NOT NULL COMMENT '区域的类型，标识区域是省、市还是区县。1:省 2:市 3:区县 4:街道',
address VARCHAR(255) NOT NULL COMMENT '收货人地址。除省市区街道后的部分',
status TINYINT(1) NOT NULL COMMENT '状态：0无效、1正常、2删除',
modified_time INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改时间戳',
created_time INT(11) UNSIGNED NOT NULL COMMENT '创建时间戳',
PRIMARY KEY(address_id),
KEY(user_id),
KEY(mobilephone),
KEY(district_code)
) ENGINE = InnoDB DEFAULT CHARSET UTF8 COMMENT '用户收货地址表';

DROP TABLE IF EXISTS ms_order;
CREATE TABLE ms_order(
order_id INT(11) UNSIGNED AUTO_INCREMENT COMMENT '订单ID',
order_sn CHAR(50) NOT NULL COMMENT '订单号',
user_id INT(11) UNSIGNED NOT NULL COMMENT '用户ID。对应ms_users.user_id',
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

DROP TABLE IF EXISTS ms_order_item;
CREATE TABLE ms_order_item(
sub_order_id INT(11) UNSIGNED AUTO_INCREMENT COMMENT '子订单ID',
order_id INT(11) UNSIGNED NOT NULL COMMENT '主订单ID',
goods_id INT(11) UNSIGNED NOT NULL COMMENT '商品ID',
goods_name CHAR(100) NOT NULL COMMENT '商品名称',
product_id INT(11) UNSIGNED NOT NULL COMMENT '货品ID',
spec_val CHAR(100) NOT NULL DEFAULT '' COMMENT '规格值',
market_price DOUBLE(8,2) NOT NULL COMMENT '市场价',
sales_price DOUBLE(8,2) NOT NULL COMMENT '销售价',
quantity SMALLINT(3) UNSIGNED NOT NULL COMMENT '购买数量',
payment_price DOUBLE(8,2) NOT NULL COMMENT '实付金额=销售价*购买数量',
total_price DOUBLE(8,2) NOT NULL COMMENT '商品总额=市场价*购买数量',
refund_status TINYINT(1) NOT NULL DEFAULT '0' COMMENT '退款状态：0未退款、1退款中、2卖家拒绝退款、3买家取消退款',
modified_by INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改人',
modified_time INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改时间戳',
created_time INT(11) UNSIGNED NOT NULL COMMENT '创建时间戳',
created_by INT(11) UNSIGNED NOT NULL COMMENT '创建人',
PRIMARY KEY(sub_order_id),
KEY(order_id)
) ENGINE = InnoDB DEFAULT CHARSET UTF8 COMMENT '订单明细表';

DROP TABLE IF EXISTS ms_order_log;
CREATE TABLE ms_order_log(
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
DROP TABLE IF EXISTS ms_logistics;
CREATE TABLE ms_logistics(
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

DROP TABLE IF EXISTS ms_payment_log;
CREATE TABLE ms_payment_log(
payment_id INT(11) UNSIGNED NOT NULL COMMENT '主键ID',
user_id INT(11) UNSIGNED NOT NULL COMMENT '用户ID',
payment_code VARCHAR(20) NOT NULL COMMENT '支付类型编码。对应ms_payment_cfg.payment_code',
order_id INT(11) UNSIGNED NOT NULL COMMENT '主订单ID',
serial_number VARCHAR(50) NOT NULL COMMENT '支付流水号',
amount DOUBLE(8,2) NOT NULL COMMENT '支付金额',
created_time INT(11) UNSIGNED NOT NULL COMMENT '创建时间戳',
PRIMARY KEY(payment_id),
KEY(order_id)
) ENGINE = InnoDB DEFAULT CHARSET UTF8 COMMENT '支付记录表';

DROP TABLE IF EXISTS ms_payment_cfg;
CREATE TABLE ms_payment_cfg(
cfg_id INT(11) UNSIGNED NOT NULL COMMENT '主键ID',
payment_code VARCHAR(20) NOT NULL COMMENT '支付类型编码。',
modified_by INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改人',
modified_time INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改时间戳',
created_time INT(11) UNSIGNED NOT NULL COMMENT '创建时间戳',
created_by INT(11) UNSIGNED NOT NULL COMMENT '创建人',
PRIMARY KEY(cfg_id)
) ENGINE = InnoDB DEFAULT CHARSET UTF8 COMMENT '支付配置表';

# --------------- 商城 end     ------------#



# ------------------ 微信相关 start ------------#
DROP TABLE IF EXISTS `ms_wx_account`;
CREATE TABLE ms_wx_account(
account_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
wx_sn CHAR(32) NOT NULL COMMENT '公众号编码。用在公众号接口配置中。用来识别接口属于哪个公众号',
wx_account VARCHAR(80) NOT NULL COMMENT '微信公众号账号',
wx_type TINYINT(1) NOT NULL COMMENT '公众号类型:1订阅号、2服务号、3企业号',
wx_auth TINYINT(1) NOT NULL COMMENT '公众号是否认证。1是、0否。',
wx_appid VARCHAR(50) NOT NULL COMMENT '微信公众号appid',
wx_appsecret VARCHAR(50) NOT NULL COMMENT '微信公众号密钥',
wx_token CHAR(32) NOT NULL COMMENT '公众号Token。用于验证接口。',
wx_aeskey CHAR(43) NOT NULL COMMENT '公众号EncodingAESKey',
status TINYINT(1) NOT NULL COMMENT '状态：0无效、1正常、2删除',
modified_by INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改人',
modified_time INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改时间戳',
created_time INT(11) UNSIGNED NOT NULL COMMENT '创建时间戳',
created_by INT(11) UNSIGNED NOT NULL COMMENT '管理员账号ID',
PRIMARY KEY(account_id),
UNIQUE KEY(wx_sn)
) ENGINE = InnoDB DEFAULT CHARSET UTF8 COMMENT '微信公众号表';

DROP TABLE IF EXISTS `ms_wx_menu`;
CREATE TABLE ms_wx_menu(
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

DROP TABLE IF EXISTS `ms_wx_group`;
CREATE TABLE ms_wx_group(
id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
account_id INT(11) UNSIGNED NOT NULL COMMENT '微信公众号ID。关联ms_wx_account.account_id',
group_id INT(11) UNSIGNED NOT NULL COMMENT '分组ID。微信分配的组ID。',
group_name VARCHAR(100) NOT NULL COMMENT '分组名称',
status TINYINT(1) NOT NULL DEFAULT '0' COMMENT '状态：0删除、1正常',
modified_by INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改人',
modified_time INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改时间戳',
created_time INT(11) UNSIGNED NOT NULL COMMENT '创建时间戳',
created_by INT(11) UNSIGNED NOT NULL COMMENT '管理员账号ID',
KEY(id)
) ENGINE = InnoDB DEFAULT CHARSET UTF8 COMMENT '微信公众号用户分组表';

DROP TABLE IF EXISTS `ms_wx_user`;
CREATE TABLE ms_wx_user(
id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
account_id INT(11) UNSIGNED NOT NULL COMMENT '微信公众号ID。关联ms_wx_account.account_id',
group_id SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT '分组ID。关联ms_wx_group.group_id',
openid VARCHAR(100) NOT NULL COMMENT '用户针对公众号的openid',
user_id INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '用户ID。用户绑定了微信就可以得到其关联的账号。关联ms_users.user_id',
nickname VARCHAR(100) NOT NULL DEFAULT '' COMMENT '用户昵称',
subscribe_time INT(11) UNSIGNED NOT NULL COMMENT '关注公众号时间',
unionid VARCHAR(100) NOT NULL DEFAULT '' COMMENT '公众号unionid',
headimgurl VARCHAR(255) NOT NULL DEFAULT '' COMMENT '头像',
country VARCHAR(50) NOT NULL DEFAULT '' COMMENT '国家',
province VARCHAR(50) NOT NULL DEFAULT '' COMMENT '省份',
city VARCHAR(50) NOT NULL DEFAULT '' COMMENT '城市',
lang VARCHAR(20) NOT NULL DEFAULT '' COMMENT '用户使用语言',
status TINYINT(1) NOT NULL DEFAULT '0' COMMENT '状态：0删除、1正常',
modified_by INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改人',
modified_time INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改时间戳',
created_time INT(11) UNSIGNED NOT NULL COMMENT '创建时间戳',
created_by INT(11) UNSIGNED NOT NULL COMMENT '管理员账号ID',
PRIMARY KEY(id),
KEY(account_id),
KEY(group_id)
) ENGINE = InnoDB DEFAULT CHARSET UTF8 COMMENT '微信公众号用户表';

DROP TABLE IF EXISTS `ms_wx_media`;
CREATE TABLE ms_wx_media(
id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
account_id INT(11) UNSIGNED NOT NULL COMMENT '微信公众号ID。关联ms_wx_account.account_id',
media_id CHAR(50) NOT NULL COMMENT '素材ID',
media_type CHAR(10) NOT NULL COMMENT '素材类型：image图片、voice语音、video视频、thumb缩略图',
url CHAR(50) NOT NULL DEFAULT '' COMMENT '图片类型时，保存图片URL',
status TINYINT(1) NOT NULL DEFAULT '0' COMMENT '状态：0删除、1正常',
modified_by INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改人',
modified_time INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改时间戳',
created_time INT(11) UNSIGNED NOT NULL COMMENT '创建时间戳',
created_by INT(11) UNSIGNED NOT NULL COMMENT '管理员账号ID',
PRIMARY KEY(id)
) ENGINE = InnoDB DEFAULT CHARSET UTF8 COMMENT '微信公众号素材表';

DROP TABLE IF EXISTS `ms_wx_event`;
CREATE TABLE ms_wx_event(
id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
openid VARCHAR(50) NOT NULL COMMENT '事件发送者openid',
developer VARCHAR(50) NOT NULL DEFAULT '' COMMENT '开发者账号',
msg_type VARCHAR(20) NOT NULL COMMENT '消息类型',
event_xml TEXT NOT NULL COMMENT '事件XML内容',
created_time INT(11) UNSIGNED NOT NULL COMMENT '创建时间戳',
PRIMARY KEY(id)
) ENGINE = InnoDB DEFAULT CHARSET utf8mb4 COMMENT '微信公众号事件记录表';

DROP TABLE IF EXISTS `ms_wx_image_text`;
CREATE TABLE ms_wx_image_text(
image_text_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
account_id INT(11) UNSIGNED NOT NULL COMMENT '微信公众号ID。关联ms_wx_account.account_id',
media_id VARCHAR(50) NOT NULL DEFAULT '0' COMMENT '素材ID。如果为0说明还没有发送到微信公众号去。',
title VARCHAR(100) NOT NULL COMMENT '图文名称。只作区别用',
status TINYINT(1) NOT NULL DEFAULT '0' COMMENT '状态：0删除、1正常',
modified_by INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改人',
modified_time INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改时间戳',
created_time INT(11) UNSIGNED NOT NULL COMMENT '创建时间戳',
created_by INT(11) UNSIGNED NOT NULL COMMENT '管理员账号ID',
PRIMARY KEY(image_text_id),
KEY(account_id),
KEY(title)
) ENGINE = InnoDB DEFAULT CHARSET utf8mb4 COMMENT '微信公众号图文表';


DROP TABLE IF EXISTS `ms_wx_image_text_article`;
CREATE TABLE ms_wx_image_text_article(
article_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
image_text_id INT(11) UNSIGNED NOT NULL COMMENT '所属图文ID。对应 ms_wx_image_text.image_text_id',
title VARCHAR(100) NOT NULL COMMENT '文章名称',
thumb_media_id VARCHAR(100) NOT NULL COMMENT '图文消息的封面图片素材id（必须是永久mediaID）',
author VARCHAR(50) NOT NULL COMMENT '作者',
digest VARCHAR(255) NOT NULL DEFAULT '' COMMENT '图文消息的摘要，仅有单图文消息才有摘要，多图文此处为空',
show_cover_pic TINYINT(1) NOT NULL COMMENT '是否显示封面，0为false，即不显示，1为true，即显示',
content TEXT COMMENT '图文消息的具体内容，支持HTML标签，必须少于2万字符，小于1M，且此处会去除JS',
content_source_url VARCHAR(100) NOT NULL COMMENT '图文消息的原文地址，即点击“阅读原文”后的URL',
status TINYINT(1) NOT NULL DEFAULT '0' COMMENT '状态：0删除、1正常',
modified_by INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改人',
modified_time INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改时间戳',
created_time INT(11) UNSIGNED NOT NULL COMMENT '创建时间戳',
created_by INT(11) UNSIGNED NOT NULL COMMENT '管理员账号ID',
PRIMARY KEY(article_id),
KEY(image_text_id)
) ENGINE = InnoDB DEFAULT CHARSET utf8mb4 COMMENT '微信公众号图文文章表';
# ------------------ 微信相关 end ------------#