# ip_location 纯真IP地址数据库
ip location tool,  php IP 数据库读取， 纯真IP数据库读取，MYSQL, IP获取装换工具合集项目

1. PHP高速解析纯真IP数据，纯真IP数据库更新工具；
2. 纯真IP数据库mysql版本;


qqwry.dat 数据库格式

qqwry.dat转换为ipwry.dat

纯真IP地址qqwry.dat mysql数据库版本
ip_location.sql.gz  使用adminer导入即可


[(纯真IP数据库转换MYSQL数据库方法)](纯真IP数据库转换MYSQL数据库.md)

[IPwry格式IP数据转换说明](IPwry0_2_2/ipwry_0_2_2c/Readme.md)


纯真IP数据库更新方法与工具
ip/ip.exe


# mysql 查询IP地址信息示例

```sql
	SELECT *  FROM ip_location WHERE INET_ATON( '8.8.8.8' ) BETWEEN INET_ATON( begin_ip ) AND INET_ATON( end_ip );
```
