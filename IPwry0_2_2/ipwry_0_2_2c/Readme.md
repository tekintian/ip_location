# 纯真IP数据库(qqwry.dat)转换成最新的IP数据库格式(ipwry.dat)

ipwry.dat相比qqwry.dat占用空间更小，我们可以将纯真IP数据库(qqwry.dat)转换成最新的IP数据库格式(ipwry.dat)，两种格式都是CNSS大神发明。


## ipwry 0.2.2c使用说明:
-i [ --input ] arg (=qqwry.dat)  : 输入文件
-o [ --output ] arg (=ipwry.dat) : 输出文件
-l [ --lzma ]                    : 使用LZMA压缩数据
-d [ --discard ] arg             : 要丢弃的地理地址
-f [ --ipsearcher ]              : 生成与此程序版本相配的ipsearcher.dll
-g [ --gbwry ]                   : 生成gbwry.dat文件
-t [ --text ]                    : 生成文本文件
-v [ --ver ]                     : 显示版本
-h [ --help ]                    : 帮助

## windows下面转换方法:

	convert.bat  转换为ipwry.dat格式
	convert_ipwry_txt.bat  转换为txt格式
