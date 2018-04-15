# 纯真IP数据库转换MYSQL数据库

## 第一步，使用IP.EXE 升级至最新数据库，
## 第二步，IP.EXE 解压数据库
## 第三步，使用editplus等编辑器打开解压后的txt数据库文件执行下面的替换，然后保存为 .sql文件


### 先替换英文双引号  " 为中文双引号 ”


正则：
### 必须先执行这条，匹配只有省份的数据

	^([0-9.]{1,})(\s{1,})([0-9.]{1,})(\s{1,})([\－\u4e00-\u9fa5_a-zA-Z.()（）/·”]+)$
	^([0-9.]{1,})(\s{1,})([0-9.]{1,})(\s{1,})([\－\u4e00-\u9fa5_a-zA-Z.()（）/·”]+)(\s{1,})$

### 然后在执行

	^([0-9.]{1,})(\s{1,})([0-9.]{1,})(\s{1,})([－\u4e00-\u9fa5_a-zA-Z.()（）/·”]+)(\s{1,})(.*)$


### 替换为：
	INSERT INTO `ip_location`(`begin_ip`, `end_ip`, `country`, `area`) VALUES ("$1","$3","$5","$7");


## 第四步，创建数据库表，导入刚刚替换完成后的SQL文件

ipdata数据库表创建SQL:

	CREATE TABLE `ip_location` (
	  `id` bigint(16) unsigned NOT NULL AUTO_INCREMENT,
	  `begin_ip` varchar(20) DEFAULT '' COMMENT '起始IP地址',
	  `end_ip` varchar(20) DEFAULT '' COMMENT '结束IP地址',
	  `country` varchar(200) DEFAULT '' COMMENT '省份/国家',
	  `area` varchar(500) DEFAULT '' COMMENT '地区',
	  PRIMARY KEY (`id`),
	  KEY `ip` (`begin_ip`,`end_ip`) USING BTREE COMMENT 'IP地址索引',
	  KEY `country` (`country`) USING BTREE,
	  FULLTEXT KEY `area` (`area`)
	) ENGINE=MyISAM AUTO_INCREMENT=450826 DEFAULT CHARSET=utf8 COMMENT='纯真IP数据库mysql utf8 版, By Tekin';





## 正则测试数据

	183.60.237.100  183.60.237.100  广东省云浮市·电信  YUNNAN.WS
	40.68.104.0     40.73.255.255   美国  
	40.68.48.0      40.68.48.15     欧洲 Microsoft公司
	40.68.48.16     40.68.48.255    美国  
	40.74.0.0       40.74.63.255    荷兰 北荷兰省阿姆斯特丹Microsoft数据中心
	61.186.49.0     61.186.49.255   海南省东方市/五指山市电信  YUNNAN.WS

	155.254.128.0   155.254.130.255 美国  CZ88.NET
	155.254.131.0   155.255.255.255 北美地区  CZ88.NET
	156.0.0.0       156.0.0.255     毛里求斯  CZ88.NET
	156.0.1.0       156.0.255.255    CZ88.NET
	156.1.0.0       156.7.255.255   美国  CZ88.NET

	82.179.160.0    82.179.163.255  俄罗斯 Fund "St.Petersburg International Economic Forum"
	82.179.164.0    82.179.167.255  俄罗斯 ZET-Telecom Ltd
	82.179.168.0    82.179.171.255  俄罗斯 Global Policy Forum
	82.179.172.0    82.179.175.255  俄罗斯 "Integrated security" ( LTD KSB )
	82.179.176.0    82.179.191.255  俄罗斯 莫斯科国立电子技术学院(技术大学)
	82.179.192.0    82.179.199.255  俄罗斯 PLUSINFO ISP company
	82.179.200.0    82.179.200.255  俄罗斯 Agency of Intellectual Resources
	82.179.201.0    82.179.201.255  俄罗斯 Joint Stock Company Platforma
	82.179.202.0    82.179.203.255  俄罗斯 JSK "ComMaster" Moscow DSL Network
	82.179.228.0    82.179.229.255  俄罗斯 "Firma Rial" LTD
	82.179.230.0    82.179.230.255  俄罗斯 莫斯科动力学院
	82.179.231.0    82.179.231.255  俄罗斯 "Firma Rial" LTD
	193.219.183.0   193.219.183.255 立陶宛 Open Society Fund/ "School Computerization"/ Vilnius
	218.27.137.250  218.27.137.250  吉林省吉林市 金桥网吧(高薪区颐馨小区15号楼7号)

	202.103.54.26   202.103.54.26   湖北省荆州市 新南门内"无名"网吧
	218.27.136.19   218.27.136.19   吉林省吉林市 铁东"龙东""世纪风暴"网吧


	218.89.187.58","218.89.187.58","四川省成都市","站北星美数码"一网情"店");
	Moscow State Technological University "Stankin"


