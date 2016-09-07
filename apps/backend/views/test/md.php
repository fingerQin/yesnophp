<?php
use common\YUrl;
?>
<!DOCTYPE html>
<html>
<title>Hello Strapdown</title>

<xmp theme="united" style="display:none;"> 本文档适用于驾校微信与APP。 [TOC] ###
一、加密原理 此版本勿须加密。直接将接口需要的参数以JSON格式POST提交即可。 ### 二、固定参数 | 参数 | 名称 | 类型 | 说明
| |----------|----------|----------|----------| | method | 接口名称 | String
| 接口服务器通过此参数路由到指定接口处理 | | v | 接口版本号 | Integer | 区别同接口不同版本 | | unique |
设备唯一号 | String | 微信此值为空字符串，APP调用必须设置此值 | | source| 来源 | String |
wechat、ios、android | ### 三、接口列表 #### 3.1 地推注册教练[ditui.coach.register] |
参数 | 名称 | 类型 | 说明 | |----------|----------|----------|----------| |
realname | 真实姓名 | String | 教练真实姓名（20个字符以内） | | sex | 教练性别 | Integer |
性别：1男、2女 | | birthday | 教练生日 | String | 格式：19901201 | | avatar | 头像地址 |
String | 通过头像上传接口获取图片地址 | | start_year | 哪年开始做教练 | Integer | 格式：1990 | |
driver_year | 哪年取得驾照 | Integer | 格式：1988 | | car_model | 汽车型号 | String |
宝马X5 | | mobilephone | 教练手机号 | String | | | start_entry_fee | 报名费首款 |
Float | 10万以内 | | end_entry_fee| 报名费尾款 | Float | 10万以内 | | info | 教练介绍 |
String | 500字以内 | | idcard_a | 身份证正面地址 | String | | | idcard_b | 身份证反面地址
| String | | | coach_a | 教练证正面地址 | String | | | coach_b | 教练证反面地址 |
String | | 返回数据: ``` { "errcode": 0, "errmsg": "添加成功" } 或 { "errcode":
-1, "errmsg": "添加失败" } ``` #### 3.2 地推查看自己推广的教练列表[ditui.coach.list] | 参数
| 名称 | 类型 | 说明 | |----------|----------|----------|----------| | token |
会话token | String | | | page | 当前页码 | Integer| 默认值1 | | count | 每页显示条数 |
Integer | 默认值20 | 返回数据： ``` 空数据的例子 { "errcode": 0, "errmsg": "ok",
"data": { "list": [], "total": 0, "page": 1, "count": 20, "isnext":
false } } 有数据的例子 { "errcode": 0, "errmsg": "ok", "data": { "list": [ {
"coach_id": 2, "avatar": "images/voucher/20160516/57398b21d2419.jpg",
"realname": "崔胜利", "birthday": "1980-05-16", "start_year": 1995,
"driver_year": 1992, "mobilephone": "18829291110", "car_model": "雷克萨斯"
}, { "coach_id": 1, "avatar":
"images/voucher/20160516/57398a9414884.jpg", "realname": "张飞飞",
"birthday": "1988-08-08", "start_year": 2010, "driver_year": 2008,
"mobilephone": "18003090586", "car_model": "宝马X6" } ], "total": 2,
"page": 1, "count": 20, "isnext": false } } ``` 返回数据说明 | 参数 | 名称 | 类型 |
说明 | |----------|----------|----------|----------| | coach_id | 教练ID |
Integer | | | avatar | 教练头像 | String | | | realname | 真实姓名 | String | |
| birthday | 生日 | String | | | start_year | 开始做教练时间 | String | | |
driver_year | 驾照领取时间 | String | | | mobilephone | 手机号码 | String | | |
car_model | 汽车型号 | String | | #### 3.3 教练获取自己的个人信息[coach.my.detail] | 参数
| 名称 | 类型 | 说明 | |----------|----------|----------|----------| | token |
会话token | String | | ``` { "errcode": 0, "errmsg": "ok", "data": {
"coach_id": 1, "avatar":
"http://files.yueyue.com/images/voucher/20160516/57398a9414884.jpg",
"realname": "张飞飞", "sex": 1, "birthday": "1988-08-08", "start_year":
2010, "driver_year": 2008, "car_model": "宝马X6", "mobilephone":
"18003090586", "start_entry_fee": 3000, "end_entry_fee": 2800, "info":
"", "school_id": 3, "school_name": "腾龙驾校", "student_count": 1 } } ``` |
参数 | 名称 | 类型 | 说明 | |----------|----------|----------|----------| |
coach_id| 教练ID | Integer| | | avatar | 教练头像 | String | | | realname|
教练姓名 | String | | | sex| 性别 | Integer| 1男、2女、0保密 | | birthday | 生日 |
String | | | start_year| 做教练年份 | Integer| | | driver_year| 领驾照年份 |
Integer| | | car_model| 汽车型号 | String | | | mobilephone| 手机号码 | String |
| | start_entry_fee| 报名首款 | Float | | | end_entry_fee| 报名尾款 | Float | |
| info | 教练简介 | String | | | school_id| 驾校ID | Integer | | |
school_name| 驾校名称 | String | | | student_count| 历史学员总数 | Integer | |

#### 3.4 教练获取名下学员列表[coach.student.list] | 参数 | 名称 | 类型 | 说明 |
|----------|----------|----------|----------| | token | 会话token | String
| | | mobilephone| 学员手机号 | String | 如果没有请传空字符串 | | page | 当前页码 |
Integer| 默认值1 | | count | 每页显示条数 | Integer | 默认值20 | ``` ### 发生错误的情况 {
"errcode": -1, "errmsg": "您不是教练" } ### 有数据的情况 { "errcode": 0, "errmsg":
"ok", "data": { "list": [ { "student_id": 1, "realname": "覃礼钧",
"mobilephone": "18575202691", "address": "仲恺大道666号", "study_times": 0,
"last_study_date": null, "avatar": "", "course_1" : "科目一通过状态",
"course_2" : "科目二通过状态", "course_3" : "科目三通过状态", "course_4" : "科目四通过状态",
} ], "total": 1, "page": 1, "count": 20, "isnext": false } } # 没数据的情况 {
"errcode": 0, "errmsg": "ok", "data": { "list": [], "total": 0, "page":
1, "count": 20, "isnext": false } } ``` 返回数据说明 | 参数 | 名称 | 类型 | 说明 |
|----------|----------|----------|----------| | student_id | 学员ID |
Integer | | | realname | 学员姓名 | Integer | | | mobilephone | 学员手机号码 |
Integer | | | address | 接送地址 | Integer | | | study_times | 学车次数 |
Integer | | | last_study_date | 最后一次学车时间 | Integer | | | avatar | 学员头像 |
String | | #### 3.5 教练设置报名费价格接口[coach.set.price] | 参数 | 名称 | 类型 | 说明 |
|----------|----------|----------|----------| | token | 教练会话token |
String | | | start_entry_fee | 报名费首款 | Float | | | end_entry_fee| 报名费尾款
| Float | | 返回数据： ``` { "errcode": 0, "errmsg": "设置成功" } ``` #### 3.6
发送绑定微信钱包短信验证码[coach.wechat.wallet.bind.sms.code] | 参数 | 名称 | 类型 | 说明 |
|----------|----------|----------|----------| | token | 学员会话token |
String | | ``` { "errcode": 0, "errmsg": "发送成功" } ``` #### 3.7
教练绑定微信钱包[coach.wechat.wallet.bind] | 参数 | 名称 | 类型 | 说明 |
|----------|----------|----------|----------| | token | 学员会话token |
String | | | code | 短信验证码 | String | | ``` { "errcode": 0, "errmsg":
"绑定成功" } ``` #### 3.8 教练收益信息接口[coach.cash.info] | 参数 | 名称 | 类型 | 说明 |
|----------|----------|----------|----------| | token | 学员会话token |
String | | ``` { "errcode": 0, "errmsg": "ok", "data": { "paid_money":
0, "unpaid_money": 0 } } ``` 返回参数说明 | 参数 | 名称 | 类型 | 说明 |
|----------|----------|----------|----------| | paid_money| 已付款的收益 |
Float| | | unpaid_money| 未付款的收益 | Float| | #### 3.9
教练获取学员详情[coach.student.detail] | 参数 | 名称 | 类型 | 说明 |
|----------|----------|----------|----------| | token | 教练会话token |
String | | | student_id | 学员ID | Integer | | ``` { "errcode": 0,
"errmsg": "ok", "data": { "student_id": 1, "realname": "覃礼钧",
"course_1": 1, "course_2": 0, "course_3": 0, "course_4": 0, "sex": 1,
"mobilephone": "18575202691", "address": "仲恺大道666号", "avatar": "" } }
``` 返回参数说明 | 参数 | 名称 | 类型 | 说明 |
|----------|----------|----------|----------| | student_id| 学员ID |
Integer| | | realname| 学员真实姓名 | String | | | course_1| 学员科目一学习进度 |
Integer| | | course_2| 学员科目二学习进度 | Integer | | | course_3| 学员科目三学习进度 |
Integer| | | course_4| 学员科目四学习进度 | Integer | | | sex| 学员性别 | Integer|
1男、2女、0保密 | | mobilephone| 学员手机号码 | String | | | address| 接送地点 | String
| | | avatar| 头像地址 | String | | #### 3.10
教练获取学员报名订单[coach.student.order.list] >
此接口应用于教练查看自己收益时，点击查看详情会展示列表数据。数据就来源于此接口。 | 参数 | 名称 | 类型 | 说明 |
|----------|----------|----------|----------| | token | 会话token | String
| | | student_id | 学员ID | Integer| 全部传-1 | | pay_status | 付款状态 |
Integer| 默认值-1。系统付款给教练的钱的状态。 | | page | 当前页码 | Integer| 默认值1 | | count |
每页显示条数 | Integer | 默认值20 | ``` { "errcode": 0, "errmsg": "ok", "data": {
"list": [ { "order_id": 3, "coach_id": 1, "student_id": 1,
"start_entry_fee": "首款费用", "start_entry_fee_pay_status": "首款支付状态",
"start_entry_fee_paytime": "首款支付时间", "end_entry_fee": "尾款费用",
"end_entry_fee_pay_status": "尾款支付状态", "end_entry_fee_paytime": "尾款支付时间",
"refund_status": "退款状态", "created_time": "订单创建时间", "stu_name": "覃礼钧",
"mobilephone": "18575202691", "sex": 1, "avatar": "" } ], "total": 1,
"page": 1, "count": 20, "isnext": false } } 没有数据： { "errcode": 0,
"errmsg": "ok", "data": { "list": [], "total": 0, "page": 1, "count":
20, "isnext": false } } ``` #### 3.11
教练对学员评价回复[coach.student.order.appraise.reply] | 参数 | 名称 | 类型 | 说明 |
|----------|----------|----------|----------| | token | 会话token | String
| | | practice_id | 学车记录ID | Integer | | | typeid| 回复类型 | Integer |
1主评回复、2追评回复 | | comment | 回复内容 | String | 200个字符 | 返回数据： ``` {
"errcode": 0, "errmsg": "ok" } ### 失败数据： { "errcode": -1, "errmsg":
"您还不能评价" } ``` #### 3.12 教练设置学员的科目学习进度[coach.student.course.set] | 参数 |
名称 | 类型 | 说明 | |----------|----------|----------|----------| | token |
会话token | String | | | student_id| 学员ID | Integer | | | course_no| 科目编号
| Integer | 科目编号：1、2、3、4分别代表科目1、2、3、4 | | status| 科目进度状态 | Integer |
0未通过、1已通过 | 返回数据： ``` { "errcode": 0, "errmsg": "ok" } ``` #### 3.13
教练获取学员的学车记录[coach.student.practice.history] > 学车记录会带上评价内容。 | 参数 | 名称 |
类型 | 说明 | |----------|----------|----------|----------| | token |
会话token | String | | | student_id| 学员ID | Integer | 默认值-1全部。 | |
comment_status | 评论状态 | Integer| -1全部、0未评论、1已初评、2已追评。3已评（包含1、2） | |
reply_status | 回复状态 | Integer | 0未回复、1已初回复、2已追加回复。 3已评（包含1、2）| | page |
当前页码 | Integer | | | count | 每页显示条数 | Integer | | ``` { "errcode": 0,
"errmsg": "ok", "data": { "list": [ { "practice_id": 1, "student_id": 1,
"coach_id": 1, "comment_status": 2, "reply_status": 2, "created_time":
1484545220, "stu_name": "覃礼钧", "mobilephone": "18575202691", "sex": 1,
"address": "仲恺大道666号", "avatar": "", "appraise": { "score1": 5,
"score2": 5, "score3": 5, "client_ip": "127.0.0.1", "content1":
"这个教练很不错", "content1_time": 1463384889, "content2": "这个教练很不错",
"content2_time": 1463453922, "reply1": "谢谢您的评价", "reply1_time":
1463384889, "reply2": "谢谢", "reply2_time": 1463384889 } } ], "total": 1,
"page": 1, "count": 20, "isnext": false } } ``` #### 3.14
学员获取预约单列表[student.order.list] | 参数 | 名称 | 类型 | 说明 |
|----------|----------|----------|----------| | token | 会话token | String
| | | comment_status | 评论状态 | Integer| -1全部、0未评论、1已初评、2已追评。 | |
reply_status | 回复状态 | Integer | 0未回复、1已初回复、2已追加回复。 | | page | 当前页码 |
Integer| 默认值1 | | count | 每页显示条数 | Integer | 默认值20 | 返回数据： ``` {
"errcode": 0, "errmsg": "ok", "data": { "list": [ { "order_id": 1,
"order_type": 2, "coach_id": 1, "student_id": 1, "fee": 3000,
"pay_status": 1, "pay_time": 0, "address": "仲恺大道666号", "comment_status":
2, "reply_status": 0, "refund_status": 0, "created_time": 1463394889,
"appraise": { "comment": { "stu_name": "覃礼钧", "stu_mobilephone":
"18575202691", "content1": "这个教练很不错", "content1_time": 1463384889,
"reply1": "谢谢您的评价", "reply1_time": 1463384889, "content2": "很不错的老司机",
"content2_time": 1463453922, "reply2": "谢谢", "reply2_time": 1463384889 }
}, "coach_name": "张飞飞" } ], "total": 1, "page": 1, "count": 20,
"isnext": false } } ``` 返回数据说明： | 参数 | 名称 | 类型 | 说明 |
|----------|----------|----------|----------| | order_id | 订单ID |
Integer | | | order_type| 订单类型 | Integer| 1:试练预约订单、2:报名订单、3:练习预约订单 | |
coach_id | 教练ID | Integer | | | student_id| 学员ID | Integer| | | fee | 金额
| Float | | | pay_status| 支付状态 | Integer| | | pay_time | 支付时间戳 | Integer
| | | address| 接送地点 | String | | | comment_status | Integer | String |
0未评论、1已初评、2已追评 | | reply_status| 订单ID | Integer| 0未回复、1已初回复、2已追加回复 | |
refund_status | 退款状态 | Integer | 0未退款、1:退款中、2:同意退款、3:不同意退款、4:已退款、5:已关闭 |
| created_time| 下单时间戳 | Integer| | | coach_name| 教练名称 | String | | |
stu_name| 学员姓名 | String | | | stu_mobilephone| 学员手机号 | String | | |
content1| 学员初评内容 | String | | | content1_time| 学员初评时间戳 | Integer| | |
reply1| 教练初评内容 | String | | | reply1_time| 教练初评时间戳 | Integer| | |
content2| 学员追加回复内容 | String | | | content2_time| 学员追加回复时间戳 | Integer| |
reply2| 教练追加回复内容 | String | | | reply2_time| 教练追加回复时间戳 | Integer| ####
3.15 学员获取报名订单详情[student.order.detail] | 参数 | 名称 | 类型 | 说明 |
|----------|----------|----------|----------| | token | 会话token | String
| | | order_id| 订单ID | Integer | | 返回数据： ``` { "errcode": 0, "errmsg":
"ok", "data": { "order_id": 1, "coach_id": 1, "student_id": 1,
"start_entry_fee": 3000, "start_entry_fee_pay_status": 1,
"start_entry_fee_paytime": 1484583730, "end_entry_fee": 2800,
"end_entry_fee_paytime": 0, "end_entry_fee_pay_status": 0, "address":
"仲恺大道666号", "refund_status": 0, "created_time": 1483928200,
"coach_name": "张飞飞" } } ``` 返回数据说明： | 参数 | 名称 | 类型 | 说明 |
|----------|----------|----------|----------| | order_id | 订单ID |
Integer | | | order_type| 订单类型 | Integer| 1:试练预约订单、2:报名订单、3:练习预约订单 | |
coach_id | 教练ID | Integer | | | student_id| 学员ID | Integer| | |
start_entry_fee| 金额 | Float | | | start_entry_fee_pay_status| 支付状态 |
Integer| | | start_entry_fee_paytime| 支付时间戳 | Integer | | |
end_entry_fee| 金额 | Float | | | end_entry_fee_paytime| 支付状态 | Integer| |
| end_entry_fee_pay_status| 支付时间戳 | Integer | | | address| 接送地点 |
String| | | refund_status | 退款状态 | Integer |
0未退款、1:退款中、2:同意退款、3:不同意退款、4:已退款、5:已关闭 | | created_time | 下单时间 | Integer
| | | coach_name | 教练名称 | String | | #### 3.16
学员对学车记录评价[student.practice.appraise] | 参数 | 名称 | 类型 | 说明 |
|----------|----------|----------|----------| | token | 会话token | String
| | | practice_id | 学车记录ID | Integer | | | score1 | 教练态度评分 | Float | | |
score2 | 教练技能评分 | Float | | | score3 | 教练车况评分 | Float | | | content|
评价内容 | String| 评论内容长度不能超过200个字符 | ``` { "errcode": -1, "errmsg":
"您已经评价了" } ``` #### 3.17 学员对学车记录追加评论[student.practice.comment] | 参数 | 名称
| 类型 | 说明 | |----------|----------|----------|----------| | token |
会话token | String | | | practice_id | 学车记录ID | Integer | | | content |
评价内容 | String| 评论内容长度不能超过200个字符 | ``` { "errcode": 0, "errmsg": "评论成功" }
``` #### 3.18 学员获取教练列表[student.coach.list] | 参数 | 名称 | 类型 | 说明 |
|----------|----------|----------|----------| | token | 会话token | String
| | | district_id | 城市ID | Integer | 根据这个值显示该地区的教练 | | order_by | 排序 |
String |
排序。default:默认排序、kb_asc/kb_desc:口碑、jl_asc/jl_desc:教龄、jg_asc/jg_desc:价格 |
| page | 当前页码 | Integer| 默认值1 | | count | 每页显示条数 | Integer | 默认值20 |

返回数据： ``` { "errcode": 0, "errmsg": "ok", "data": { "list": [ {
"coach_id": 2, "avatar": "images/voucher/20160516/57398b21d2419.jpg",
"realname": "崔胜利", "sex": 1, "birthday": "1980-05-16", "start_year":
1995, "driver_year": 1992, "car_model": "雷克萨斯", "mobilephone":
"18829291110", "praise": 0, "trial_price": 50, "school_id": 3,
"study_price": 99, "school_name": "腾龙驾校", "long_driver_year": 20 }, {
"coach_id": 1, "avatar": "images/voucher/20160516/57398a9414884.jpg",
"realname": "张飞飞", "sex": 1, "birthday": "1988-08-08", "start_year":
2010, "driver_year": 2008, "car_model": "宝马X6", "mobilephone":
"18003090586", "praise": 0, "trial_price": 50, "school_id": 3,
"study_price": 100, "school_name": "腾龙驾校", "long_driver_year": 20 } ],
"total": 2, "page": 1, "count": 20, "isnext": false } } ``` 返回数据说明： | 参数
| 名称 | 类型 | 说明 | |----------|----------|----------|----------| |
coach_id | 会话token | String | | | avatar| 头像 | String | | | realname |
真实姓名 | String | | | sex| 性别 | Integer | 1男、2女、0保密 | | birthday | 出生年月 |
String | | | start_year| 哪年开始做教练 | Integer | | | driver_year | 哪年取得驾照 |
Integer | | | car_model| 汽车型号 | String | | | mobilephone | 手机号码 | String
| | | praise| 口碑总平均分 | Float | | | trial_price | 试练价格 | Float | | |
school_id| 驾校ID | Integer | | | study_price| 正式练习价格 | Integer | | |
school_name| 驾校名称 | String | | #### 3.19 学员获取教练详情[student.coach.detail]

| 参数 | 名称 | 类型 | 说明 | |----------|----------|----------|----------| |
token | 会话token | String | | | coach_id| 教练ID | Integer | | 返回数据： ``` {
"errcode": 0, "errmsg": "ok", "data": { "coach_id": 1, "avatar":
"http://files.yueyue.com/images/voucher/20160516/57398a9414884.jpg",
"realname": "张飞飞", "sex": 1, "birthday": "1988-08-08", "start_year":
2010, "driver_year": 2008, "car_model": "宝马X6", "mobilephone":
"18003090586", "start_entry_fee": 3000, "end_entry_fee": 2800, "info":
"", "praise": 0, "school_id": 3, "school_name": "腾龙驾校",
"long_driver_year": 8, "long_start_year": 6, "entry_fee": 5800,
"student_count": 1 } } ``` 返回数据说明： | 参数 | 名称 | 类型 | 说明 |
|----------|----------|----------|----------| | coach_id| 教练ID | Integer
| | | avatar| 教练头像 | String | | | realname| 教练姓名 | String | | | sex|
教练性别 | Integer | 1男、2女、0保密 | | birthday| 教练生日 | String | | | start_year|
做教练年份 | Integer | | | driver_year| 教练领驾照年份 | Integer | | | car_model|
教练车型 | String | | | mobilephone| 教练手机号码 | String | | | start_entry_fee|
报名费首款 | Float | | | end_entry_fee| 报名费尾款 | Float | | | info| 教练介绍 |
String | | | praise | 教练口碑总平均分 | String | | | school_id| 驾校ID | Integer
| | | school_name| 驾校名称 | String | | | long_driver_year| 驾龄| Integer | |
| long_start_year| 教龄 | Integer | | | entry_fee| 报名费| Float| | |
student_count| 教练历史学员数量 | Integer | | #### 3.20 发送短信验证码[system.sms.send]

| 参数 | 名称 | 类型 | 说明 | |----------|----------|----------|----------| |
mobilephone | 手机号码 | String | | 返回数据： ``` { "errcode": 0, "errmsg":
"短信发送成功" } 或 { "errcode": -1, "errmsg": "获取验证码时间间隔过短" } ``` #### 3.21
验证码短信验证码是否正确[system.sms.check] | 参数 | 名称 | 类型 | 说明 |
|----------|----------|----------|----------| | type_code| 验证码类型 |
String | register、findpwd | | mobilephone | 手机号码 | String | | | code|
短信验证码 | String | | 返回数据: ``` { "errcode": 0, "errmsg": "短信验证成功" } 或 {
"errcode": -1, "errmsg": "短信验证码不正确" } ``` #### 3.22
微信公众号支付签名数据[pay.wechat.data] | 参数 | 名称 | 类型 | 说明 |
|----------|----------|----------|----------| | token | 会话token | String
| | | order_id | 订单ID | Integer | | 返回数据： ``` { "errcode": 0, "errmsg":
"ok", "data": { "data": { "appId": "wx42cb58bec45babeb", "nonceStr":
"skqb9sbgqld9j934jo8jpq7d6vk0wipv", "package":
"prepay_id=wx201605161641440a20d4f2ef0269440075", "signType": "MD5",
"timeStamp": "1463388103", "paySign": "6AC290D59D5EA07F37C3C2559CCC08F7"
} } } ``` #### 3.23 获取微信JSSDK配置[pay.wechat.js.sdk.config] | 参数 | 名称 | 类型
| 说明 | |----------|----------|----------|----------| | url | URL地址 |
String | 调起微信支付的URL地址 | 返回数据： ```json { "errcode": 0, "errmsg": "ok",
"data": { "data": { "appId": "wx42cb58bec45babeb", "nonceStr":
"1zzVQ6Wdq6", "timestamp": 1463387694, "url":
"http://wx.testweixin.top/index/init/index", "signature":
"af5ddbcb1bdef6c5d029f692c0cfba54456a6e61" } } } ``` #### 3.24
用户登录接口[user.login] | 参数 | 名称 | 类型 | 说明 |
|----------|----------|----------|----------| | username | 账号或手机号 |
String | | | password | 密码 | String | | 返回数据 ``` { "errcode": 0,
"errmsg": "登录成功", "data": { "token":
"66f3UlJWVAZTAwcGUlAHBFECVQ0DAwdVAgMIV1oJawIKAVRTAQcAD1JSUQFWAQYEVgAEWQFQUQUDBQhQCwlXbQIMUlFRXlwEWwU+BA"
} } ``` 返回参数说明 | 参数 | 名称 | 类型 | 说明 |
|----------|----------|----------|----------| | token | 用户会话权限 | String
| | #### 3.25 订单提交接口[pay.order.submit] | 参数 | 名称 | 类型 | 说明 |
|----------|----------|----------|----------| | token | 学员会话token |
String | | | coach_id | 教练ID | Integer | | | op_type | 类型 | Integer |
1:报名首款、2:报名尾款 | 返回数据 ``` { "errcode": 0, "errmsg": "ok", "data": {
"order_id": "3" } } ``` 订单提交成功会返回order_id。通过此ID调用微信支付接口得到加密的签名信息实现微信支付。



#### 3.26 学员获取我的教练接口[student.my.coach.detail] | 参数 | 名称 | 类型 | 说明 |
|----------|----------|----------|----------| | token | 学员token会话 |
String | | ``` { "errcode": 0, "errmsg": "ok", "data": { "coach_id": 1,
"avatar":
"http://files.yueyue.com/images/voucher/20160516/57398a9414884.jpg",
"realname": "张飞飞", "sex": 1, "birthday": "1988-08-08", "start_year":
2010, "driver_year": 2008, "car_model": "宝马X6", "mobilephone":
"18003090586", "start_entry_fee": 3000, "end_entry_fee": 2800, "info":
"", "praise": 0, "school_id": 3, "school_name": "腾龙驾校",
"long_driver_year": 8, "long_start_year": 6, "entry_fee": 5800,
"student_count": 1, "order": { "start_entry_fee": "报名时的首款",
"start_entry_fee_pay_status": "首款是否支付", "start_entry_fee_paytime":
"首款支持时间", "end_entry_fee": "报名时的的尾款", "end_entry_fee_pay_status":
"尾款支付状态", "end_entry_fee_paytime": "尾款支付时间" } } } ``` | 参数 | 名称 | 类型 |
说明 | |----------|----------|----------|----------| | coach_id| 教练ID |
Integer| | | avatar| 教练头像 | String | | | realname| 教练姓名 | String | | |
sex| 教练性别 | Integer| 1男、2女、0保密 | | birthday| 教练生日 | String | | |
start_year| 做教练年份 | Integer | | | driver_year| 教练领驾照年份 | Integer| | |
car_model| 教练车型 | String | | | mobilephone| 教练手机号码 | String | | |
start_entry_fee| 报名费首款 | Float | | | end_entry_fee| 报名费尾款 | Float | | |
info| 教练介绍 | String | | | praise | 教练口碑总平均分 | Float| | | school_id| 驾校ID
| Integer| | | school_name| 驾校名称 | String | | | long_driver_year| 驾龄|
Integer| | | long_start_year| 教龄 | Integer| | | entry_fee| 报名费| Float |
| | student_count| 教练历史学员数量 | Integer| | #### 3.27
学员获取用户详情接口[student.detail] | 参数 | 名称 | 类型 | 说明 |
|----------|----------|----------|----------| | token | 学员会话token |
String | | 返回数据 ``` { "errcode": 0, "errmsg": "ok", "data": {
"reg_time": "2016-05-16 15:48:09", "username": "18575202691",
"realname": "覃礼钧", "course_1": 1, "course_2": 0, "course_3": 0,
"course_4": 0, "sex": 1, "mobilephone": "18575202691", "coach_id": 1,
"coach_name": "张飞飞", "address": "仲恺大道666号", "signature": "", "avatar":
"" } } ``` 返回结果说明 | 参数 | 名称 | 类型 | 说明 |
|----------|----------|----------|----------| | reg_time | 注册时间 | String
| | | username | 学员账号 | String | | | realname | 真实名称 | String | | |
course_1 | 科目一进度状态 | Integer| | | course_2 | 科目二进度状态 | Integer| | |
course_3 | 科目三进度状态 | Integer| | | course_4 | 科目四进度状态 | Integer| | | sex|
学员性别 | Integer| 1男、2女、0保密 | | mobilephone| 学员手机号码 | String | | |
coach_id| 教练ID | Integer| | | coach_name| 教练姓名 | String | | | address|
学员接送地点| String | | | signature| 学员个性签名 | String | | | avatar| 学员头像 |
String | | #### 3.28 获取推荐教练列表接口[system.coach.recommend.list] | 参数 | 名称 |
类型 | 说明 | |----------|----------|----------|----------| | district_id |
城市ID | String | | ``` { "errcode": 0, "errmsg": "ok", "data": [ {
"coach_id": 1, "praise": 5, "avatar":
"http://files.yueyue.com/images/voucher/20160516/57398b21d2419.jpg",
"realname": "崔胜利", "sex": 1, "start_year": 1995, "driver_year": 1992,
"mobilephone": "18829291110", "start_entry_fee": 50, "end_entry_fee":
99, "long_start_year": 21, "long_driver_year": 24 }, { "coach_id": 2,
"praise": 5, "avatar":
"http://files.yueyue.com/images/voucher/20160516/57398a9414884.jpg",
"realname": "张飞飞", "sex": 1, "start_year": 2010, "driver_year": 2008,
"mobilephone": "18003090586", "start_entry_fee": 50, "end_entry_fee":
100, "long_start_year": 6, "long_driver_year": 8 } ] } ``` 返回数据说明 | 参数 |
名称 | 类型 | 说明 | |----------|----------|----------|----------| | avatar |
教练头像 | String | | | realname | 姓名 | String | | | sex| 性别 | Integer|
1男、2女、0保密 | | start_year | 开始做教练的年份 | Integer | | | driver_year | 领驾照的年份
| Integer | | | mobilephone | 手机号码 | String | | | trial_price| | Float|
| | study_price| 城市ID | Float| | | long_start_year| 做教练多少年 | Integer| |
| long_driver_year | 领驾照多少年 | Integer| | #### 3.29
学员获取自己学车记录[student.practice.history] | 参数 | 名称 | 类型 | 说明 |
|----------|----------|----------|----------| | token | 会话token | String
| | | comment_status | 评论状态 | Integer| -1全部、0未评论、1已初评、2已追评。3已评（包含1、2） |
| reply_status | 回复状态 | Integer | 0未回复、1已初回复、2已追加回复。 3已评（包含1、2）| | page
| 当前页码 | Integer | | | count | 每页显示条数 | Integer | | ``` { "errcode": 0,
"errmsg": "ok", "data": { "list": [ { "practice_id": 2, "student_id": 1,
"coach_id": 1, "comment_status": 0, "reply_status": 0, "created_time":
1484545220, "stu_name": "覃礼钧", "mobilephone": "18575202691", "sex": 1,
"address": "仲恺大道666号", "avatar":
"http://files.yueyue.com/images/voucher/20160518/573c158ce5ca0.jpeg",
"appraise": { "score1": 0, "score2": 0, "score3": 0, "client_ip": "",
"content1": "", "content1_time": "", "content2": "", "content2_time":
"", "reply1": "", "reply1_time": "", "reply2": "", "reply2_time": "" }
}, { "practice_id": 1, "student_id": 1, "coach_id": 1, "comment_status":
2, "reply_status": 2, "created_time": 1484545220, "stu_name": "覃礼钧",
"mobilephone": "18575202691", "sex": 1, "address": "仲恺大道666号", "avatar":
"http://files.yueyue.com/images/voucher/20160518/573c158ce5ca0.jpeg",
"appraise": { "score1": 5, "score2": 5, "score3": 5, "client_ip":
"127.0.0.1", "content1": "这个教练很不错", "content1_time": 1463384889,
"content2": "这个教练很不错", "content2_time": 1463453922, "reply1": "谢谢您的评价",
"reply1_time": 1463384889, "reply2": "谢谢", "reply2_time": 1463384889 } }
], "total": 2, "page": 1, "count": 20, "isnext": false } } ``` 返回参数说明 |
参数 | 名称 | 类型 | 说明 | |----------|----------|----------|----------| |
practice_id| 学车记录ID | Integer | | | student_id| 学员ID | Integer | | |
coach_id| 教练ID | Integer| | | comment_status| 会话token | Integer |
0未评论、1已初评、2已追评 | | reply_status| 教练评价状态 | Integer | 0未回复、1已初回复、2已追加回复 |
| created_time| 评价时间戳 | Integer| | | stu_name| 学员姓名 | String | | |
mobilephone| 学员手机号码 | String | | | sex| 学员性别 | Integer| | | address|
学员接着地址 | String | | | avatar| 学员头像 | String | | | score1| 教练态度评分 | Float
| | | score2| 教练技能评分 | Float| | | score3| 教练车况评分 | Float| | | client_ip|
学员评价IP地址 | String | | | content1| 学员初评内容 | String | | | content1_time|
学员初评时间戳 | Integer| | | content2| 学员追评内容 | String | | | content2_time|
学员追评时间戳 | Integer| | | reply1| 教练初评内容 | String | | | reply1_time|
教练初评时间戳| String | | | reply2| 教练追评内容| String | | | reply2_time| 教练追评时间戳
| Integer| | #### 3.30 学车报名费微信支付接口[pay.wechat.practice.order.submit] >
此接口会返回微信公众号支付时需要的签名信息。 | 参数 | 名称 | 类型 | 说明 |
|----------|----------|----------|----------| | token | 学员会话token |
String | | | coach_id | 教练ID | Integer | | | op_type | 缴费类型 | Integer |
1:报名首款、2:报名尾款 | 返回数据 ``` { "errcode": 0, "errmsg": "ok", "data": {
"appId": "wx42cb58bec45babeb", "nonceStr":
"mk2h7gw4y3lzd7byahnxporvyoncxvhu", "package":
"prepay_id=wx2016051910530443c0db7b710617646298", "signType": "MD5",
"timeStamp": 1463626383, "paySign": "5DED388B28B55582DC290E9B18311545" }
} ``` #### 3.31 学员打卡接口[student.sign.in] > 调用此接口前，一定要提示学员打卡记录真实有效。 | 参数 |
名称 | 类型 | 说明 | |----------|----------|----------|----------| | token |
学员会话token | String | | ``` { "errcode": 0, "errmsg": "打卡成功" } ``` ####
3.32 学员设置基本信息接口[student.userinfo.set] > 调用此接口前，一定要提示学员打卡记录真实有效。 | 参数 |
名称 | 类型 | 说明 | |----------|----------|----------|----------| | token |
学员会话token | String | | | realname | 真实姓名 | String | 必须 | | sex| 性别 |
Integer | 可不传默认0。1男、2女、0保密。 | | address | 学车接送地点 | String | 可不传。 | |
avatar | 头像地址 | String | 可不传。如果是微信jssdk上传的，则格式为：wechat_素材ID | ``` {
"errcode": 0, "errmsg": "设置成功" } ``` #### 3.33
用户查看学员对教练的评价列表接口[system.coach.appraise.list] | 参数 | 名称 | 类型 | 说明 |
|----------|----------|----------|----------| | coach_id | 教练ID |
Integer | | | page | 当前页码 | Integer | 默认1 | > 默认每页显示10条。 ``` {
"errcode": 0, "errmsg": "ok", "data": { "list": [], "total": 0, "page":
1, "count": 10, "isnext": false } } 有数据的情况 { "errcode": 0, "errmsg":
"ok", "data": { "list": [ { "practice_id": 1, "student_id": 1,
"coach_id": 1, "comment_status": 2, "reply_status": 2, "created_time":
1484545220, "stu_name": "覃礼钧", "avatar": "", "appraise": { "score1": 5,
"score2": 5, "score3": 5, "client_ip": "127.0.0.1", "content1":
"这个教练很不错", "content1_time": 1463384889, "content2": "这个教练很不错",
"content2_time": 1463453922, "reply1": "谢谢您的评价", "reply1_time":
1463384889, "reply2": "谢谢", "reply2_time": 1463384889 } } ], "total": 1,
"page": 1, "count": 20, "isnext": false } } ``` #### 3.34
获取教练收益列表接口[coach.earning.list] | 参数 | 名称 | 类型 | 说明 |
|----------|----------|----------|----------| | coach_id | 教练ID |
Integer | | | earing_type | 收益类型 | Integer | 默认-1。-1全部、1报名首款到账、2报名尾款到账。
| | page | 当前页码 | Integer | 默认1 | | count | 每页显示条数 | Integer | 默认20 |

``` { "errcode": 0, "errmsg": "ok", "data": { "list": [ { "earing_id":
"收益ID", "coach_id": "教练ID", "earing_type": "收益类型", "money": "收益金额",
"student_id": 1, "stu_name": "张木木", "mobilephone": "18575202691",
"avatar": "" } ], "total": 1, "page": 1, "count": 20, "isnext": false }
} ``` </xmp>

<script type="text/javascript"
	src="<?php echo YUrl::assets('js', '/strapdown/strapdown.js'); ?>"></script>
</html>