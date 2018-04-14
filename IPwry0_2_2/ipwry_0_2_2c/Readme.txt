纯真IP数据库(qqwry.dat)转换成最新的IP数据库格式(ipwry.dat)
发表于 2011-02-25   |   分类于 杂七杂八   |   5 Comments
ipwry.dat相比qqwry.dat占用空间更小，我们可以将纯真IP数据库(qqwry.dat)转换成最新的IP数据库格式(ipwry.dat)，两种格式都是CNSS大神发明。

下面是具体的转换方法：


ipwry 0.2.2c
使用说明:
-i [ --input ] arg (=qqwry.dat)  : 输入文件
-o [ --output ] arg (=ipwry.dat) : 输出文件
-l [ --lzma ]                    : 使用LZMA压缩数据
-d [ --discard ] arg             : 要丢弃的地理地址
-f [ --ipsearcher ]              : 生成与此程序版本相配的ipsearcher.dll
-g [ --gbwry ]                   : 生成gbwry.dat文件
-t [ --text ]                    : 生成文本文件
-v [ --ver ]                     : 显示版本
-h [ --help ]                    : 帮助
制作方法:

1、将下面的代码保存为 *.bat 后缀的文件，如“IPwry.bat”

@Echo Off
title qqwry.dat转换为ipwry.dat
IPwry -i QQwry.dat -o IPwry.dat -l
Echo IPwry.dat更新生成完毕
pause
Exit
2、将“QQwry.dat”及“IPwry.exe”等放在同一文件夹下。

3、执行“IPwry.bat”便可以生成CNSS格式的“IPwry.dat”IP文件了。

相关文件下载地址:

1、ipwry 0.2.2c

在下载页面选择“ipwry_0_2_2c.zip”这个

http://cosoft.org.cn/projects/ipwry/

附带“IPwry.bat”的ipwry_0_2_2c.zip压缩包

2、纯真数据库

http://www.cz88.net/

附带源码：IPwry0_2_2