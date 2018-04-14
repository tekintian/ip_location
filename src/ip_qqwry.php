<?php
header("Content-type: text/html; charset=utf-8");
/**
 * @Author: Tekin
 * @Date:   2018-04-14 18:11:57
 * @Last Modified 2018-04-14
 */
include '../test_helper.php';

/**
 * 获取IP对于地理位置信息
 * 使用的是纯真IP数据库
 */
function get_ip_locate($ip)
{
    //IP数据文件路径
    $dat_path = 'qqwry.dat';
    //增加主机IP解析
    $ip = preg_match("/^[a-z]/i", strtolower($ip)) > 0 ? gethostbyname($ip) : $ip;

    //检查IP地址
    if (!preg_match("/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/", $ip)) {
        return 'IP address Error';
    }
    //打开IP数据文件
    if (!$fd = @fopen($dat_path, 'rb')) {
        return 'IP date file not exists or access denied';
    }

    //分解IP进行运算，得出整形数
    // $ip = explode('.', $ip);
    // $ipNum = $ip[0] * 16777216 + $ip[1] * 65536 + $ip[2] * 256 + $ip[3];

    $ipNum = ip2long($ip); //利用PHP函数计算iP的整数

    //获取IP数据索引开始和结束位置
    $DataBegin = fread($fd, 4);
    $DataEnd = fread($fd, 4);
    $ipbegin = implode('', unpack('L', $DataBegin));
    if ($ipbegin < 0) {
        $ipbegin += pow(2, 32);
    }

    $ipend = implode('', unpack('L', $DataEnd));
    if ($ipend < 0) {
        $ipend += pow(2, 32);
    }

    $ipAllNum = ($ipend - $ipbegin) / 7 + 1;

    $BeginNum = 0;
    $EndNum = $ipAllNum;

    //使用二分查找法从索引记录中搜索匹配的IP记录
    while ($ip1num > $ipNum || $ip2num < $ipNum) {
        $Middle = intval(($EndNum + $BeginNum) / 2);

        //偏移指针到索引位置读取4个字节
        fseek($fd, $ipbegin + 7 * $Middle);
        $ipData1 = fread($fd, 4);
        if (strlen($ipData1) < 4) {
            fclose($fd);
            return 'System Error';
        }
        //提取出来的数据转换成长整形，如果数据是负数则加上2的32次幂
        $ip1num = implode('', unpack('L', $ipData1));
        if ($ip1num < 0) {
            $ip1num += pow(2, 32);
        }

        //提取的长整型数大于我们IP地址则修改结束位置进行下一次循环
        if ($ip1num > $ipNum) {
            $EndNum = $Middle;
            continue;
        }

        //取完上一个索引后取下一个索引
        $DataSeek = fread($fd, 3);
        if (strlen($DataSeek) < 3) {
            fclose($fd);
            return 'System Error';
        }
        $DataSeek = implode('', unpack('L', $DataSeek . chr(0)));
        fseek($fd, $DataSeek);
        $ipData2 = fread($fd, 4);
        if (strlen($ipData2) < 4) {
            fclose($fd);
            return 'System Error';
        }
        $ip2num = implode('', unpack('L', $ipData2));
        if ($ip2num < 0) {
            $ip2num += pow(2, 32);
        }

        //没找到提示未知
        if ($ip2num < $ipNum) {
            if ($Middle == $BeginNum) {
                fclose($fd);
                return 'Unknown';
            }
            $BeginNum = $Middle;
        }
    }

    //下面的代码读晕了，没读明白，有兴趣的慢慢读
    $ipFlag = fread($fd, 1);
    if ($ipFlag == chr(1)) {
        $ipSeek = fread($fd, 3);
        if (strlen($ipSeek) < 3) {
            fclose($fd);
            return 'System Error';
        }
        $ipSeek = implode('', unpack('L', $ipSeek . chr(0)));
        fseek($fd, $ipSeek);
        $ipFlag = fread($fd, 1);
    }

    if ($ipFlag == chr(2)) {
        $AddrSeek = fread($fd, 3);
        if (strlen($AddrSeek) < 3) {
            fclose($fd);
            return 'System Error';
        }
        $ipFlag = fread($fd, 1);
        if ($ipFlag == chr(2)) {
            $AddrSeek2 = fread($fd, 3);
            if (strlen($AddrSeek2) < 3) {
                fclose($fd);
                return 'System Error';
            }
            $AddrSeek2 = implode('', unpack('L', $AddrSeek2 . chr(0)));
            fseek($fd, $AddrSeek2);
        } else {
            fseek($fd, -1, SEEK_CUR);
        }

        while (($char = fread($fd, 1)) != chr(0)) {
            $end_ip .= $char;
        }

        $AddrSeek = implode('', unpack('L', $AddrSeek . chr(0)));
        fseek($fd, $AddrSeek);

        while (($char = fread($fd, 1)) != chr(0)) {
            $start_ip .= $char;
        }

    } else {
        fseek($fd, -1, SEEK_CUR);
        while (($char = fread($fd, 1)) != chr(0)) {
            $start_ip .= $char;
        }

        $ipFlag = fread($fd, 1);
        if ($ipFlag == chr(2)) {
            $AddrSeek2 = fread($fd, 3);
            if (strlen($AddrSeek2) < 3) {
                fclose($fd);
                return 'System Error';
            }
            $AddrSeek2 = implode('', unpack('L', $AddrSeek2 . chr(0)));
            fseek($fd, $AddrSeek2);
        } else {
            fseek($fd, -1, SEEK_CUR);
        }
        while (($char = fread($fd, 1)) != chr(0)) {
            $end_ip .= $char;
        }
    }
    fclose($fd);

    //最后做相应的替换操作后返回结果
    if (preg_match('/http/i', $end_ip)) {
        $end_ip = '';
    }
    $address = "$start_ip $end_ip";
    $address = preg_replace('/CZ88.Net/is', '', $address);
    $address = preg_replace('/^s*/is', '', $address);
    $address = preg_replace('/s*$/is', '', $address);
    if (preg_match('/http/i', $address) || $address == '') {
        $address = 'Unknown';
    }
    //iconv('GB2312', 'UTF-8', $address);
    //iconv("UTF-8", "GB2312//IGNORE", $data)
    return mb_convert_encoding($address, 'UTF-8', 'GB2312');
}

/**
 * 获取客户端IP地址
 *
 * @return     <type>  The client ip.
 */
function get_client_ip()
{
    if (getenv('HTTP_CLIENT_IP')) {
        $ip = getenv('HTTP_CLIENT_IP');
    } elseif (getenv('HTTP_X_FORWARDED_FOR')) {
        //获取客户端用代理服务器访问时的真实ip 地址
        $ip = getenv('HTTP_X_FORWARDED_FOR');
    } elseif (getenv('HTTP_X_FORWARDED')) {
        $ip = getenv('HTTP_X_FORWARDED');
    } elseif (getenv('HTTP_FORWARDED_FOR')) {
        $ip = getenv('HTTP_FORWARDED_FOR');
    } elseif (getenv('HTTP_FORWARDED')) {
        $ip = getenv('HTTP_FORWARDED');
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

//  调用举例（速度很快）
echo get_ip_locate('8.8.8.8');

//输出: 美国 加利福尼亚州圣克拉拉县山景市谷歌公司DNS服务器
