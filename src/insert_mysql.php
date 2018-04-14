<?php

/**
 * @Author: Tekin
 * @Date:   2018-04-14 20:54:52
 * @Last Modified 2018-04-14
 */
function read_qqip($qq_datafile)
{
    if (!$fd = @fopen($qq_datafile, 'rb')) {
        return 'Invalid IP data file';
    }

    //获取文件的前8个字节
    if (!($DataBegin = fread($fd, 4)) || !($DataEnd = fread($fd, 4))) {
        return;
    }

    //$ipbegin指向第一个起始IP的位置(索引区)
    @$ipbegin = implode('', unpack('L', $DataBegin));
    if ($ipbegin < 0) {
        $ipbegin += pow(2, 32);
    }

    //$ipend指向最后一个起始IP的位置(索引区)
    @$ipend = implode('', unpack('L', $DataEnd));
    if ($ipend < 0) {
        $ipend += pow(2, 32);
    }

    //索引区每条记录的长度为7个字节，$ipAllNum表示的是IP段的个数
    $ipAllNum = ($ipend - $ipbegin) / 7 + 1;

    //在起始IP区进行索引
    $result = array(array());
    for ($i = 0; $i < $ipAllNum; $i++) {
        fseek($fd, $ipbegin + 7 * $i);
        $ip_start = fread($fd, 4); //读取IP段的起始IP的二进制串

        if (strlen($ip_start) < 4) {
            fclose($fd);
            return 'System Error';
        }
        $ip_start = implode('', unpack('L', $ip_start));
        if ($ip_start < 0) {
            $ip_start += pow(2, 32);
        }

        $result[$i]['IP_START'] = $ip_start; //获取IP段的起始IP值

        $DataSeek = fread($fd, 3); //寻找结束IP
        if (strlen($DataSeek) < 3) {
            fclose($fd);
            return 'System Error';
        }
        $DataSeek = implode('', unpack('L', $DataSeek . chr(0)));
        fseek($fd, $DataSeek);
        $ip_end = fread($fd, 4); //结束IP二进制码
        if (strlen($ip_end) < 4) {
            fclose($fd);
            return 'System Error';
        }
        $ip_end = implode('', unpack('L', $ip_end)); //结束IP值
        if ($ip_end < 0) {
            $ip_end += pow(2, 32);
        }

        $result[$i]['IP_END'] = $ip_end; //读取结束IP值

        $ipAddr1 = '';
        $ipAddr2 = '';
        //下面开始读取国家和地区信息
        $ipFlag = fread($fd, 1); //指向国家串前的一个字节
        if ($ipFlag == chr(1)) {
            //为0x01表明国家、地区与前面的IP信息重复
            $ipSeek = fread($fd, 3); //国家、地区字符串的偏移量
            if (strlen($ipSeek) < 3) {
                fclose($fd);
                return 'System Error';
            }
            $ipSeek = implode('', unpack('L', $ipSeek . chr(0)));
            fseek($fd, $ipSeek); //返回到国家、地区偏移量处
            $ipFlag = fread($fd, 1);
        }
        if ($ipFlag == chr(2)) {
            //为0x02,表明国家串与前面的国家或地区重复
            $AddrSeek = fread($fd, 3); //国家串的偏移量
            if (strlen($AddrSeek) < 3) {
                fclose($fd);
                return 'System Error';
            }
            $ipFlag = fread($fd, 1); //读取地区串前的第一个字节
            if ($ipFlag == chr(2)) {
                //出现0x02说明地区串与前面的国家或地区串重复
                $AddrSeek2 = fread($fd, 3); //地区串的偏移量
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
                $ipAddr2 .= $char;
            }

            $AddrSeek = implode('', unpack('L', $AddrSeek . chr(0)));
            fseek($fd, $AddrSeek);
            while (($char = fread($fd, 1)) != chr(0)) {
                $ipAddr1 .= $char;
            }

        } else {
//国家串不与前面重复
            fseek($fd, -1, SEEK_CUR);
            while (($char = fread($fd, 1)) != chr(0)) {
                $ipAddr1 .= $char;
            }
            //读取国家串
            $ipFlag = fread($fd, 1);
            if ($ipFlag == chr(2)) {
                //地区串与前面的串重复
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
                $ipAddr2 .= $char;
            }
            //地区串
        }
        if (preg_match('/http/i', $ipAddr2)) {
            $ipAddr2 = '';
        }

        $ipaddr = "$ipAddr1";
        $ipaddr = preg_replace('/CZ88\.NET/is', '', $ipaddr);
        $ipaddr = preg_replace('/^\s*/is', '', $ipaddr);
        $ipaddr = preg_replace('/\s*$/is', '', $ipaddr);
        if (preg_match('/http/i', $ipaddr) || $ipaddr == '') {
            $ipaddr = 'Unknown';
        }
        $ipaddr = mb_convert_encoding($ipaddr, "utf-8", "gb2312");
        $ipAddr2 = mb_convert_encoding($ipAddr2, "utf-8", "gb2312");
        $result[$i]['BIG_AREA'] = $ipaddr;
        $result[$i]['SMALL_AREA'] = $ipAddr2;
        //echo $result[$i]['BIG_AREA'].'---'.$result[$i]['SMALL_AREA'].'<br>';

    }
    return $result;
}
