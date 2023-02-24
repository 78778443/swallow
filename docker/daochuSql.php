<?php

use think\facade\Db;

require_once "../web/vendor/autoload.php";

$to_file_name = "swallow.sql"; //导出文件名
$gitPath = "/mnt/d/mycode/information-gathering/web/swallow.sql"; //导出文件名
file_put_contents($to_file_name, "CREATE DATABASE swallow;\r\nUSE swallow;\r\n\r\n");


//链接数据库
Db::setConfig([
    // 默认数据连接标识
    'default' => 'mysql',
    // 数据库连接信息
    'connections' => [
        'mysql' => [
            // 数据库类型
            'type' => 'mysql',
            // 主机地址
            'hostname' => '124.70.51.244',
            'port' => '3306',
            'password' => 'PWWsswFh4RHepW5w',
            // 用户名
            'username' => 'swallow',
            // 数据库名
            'database' => 'swallow',
            // 数据库编码默认采用utf8
            'charset' => 'utf8',
            // 数据库调试模式
            'debug' => true,
        ],
    ],
]);
//获取表名
$tabList = Db::query("show tables;");

$tabList = array_column($tabList, 'Tables_in_swallow');
//将每个表的表结构导出到文件
foreach ($tabList as $val) {
    $sql = "show create table " . $val;
    $res = Db::query($sql);
    $info = "DROP TABLE IF EXISTS `" . $val . "`;\r\n";
    $sqlStr = $info . $res[0]['Create Table'] . ";\r\n\r\n";
//追加到文件
    file_put_contents($to_file_name, $sqlStr, FILE_APPEND);
}

file_put_contents($gitPath, file_get_contents($to_file_name));


function exportData($tabList, $to_file_name)
{
    //将每个表的数据导出到文件
    foreach ($tabList as $val) {
        $sql = "select * from " . $val;

        $res = Db::query($sql);
        //如果表中没有数据，则跳出循环
        if (count($res) < 1) continue;
        $info = "-- ----------------------------\r\n";
        $info .= "-- Records for `" . $val . "`\r\n";
        $info .= "-- ----------------------------\r\n";
        file_put_contents($to_file_name, $info, FILE_APPEND);
        //读取数据
        foreach ($res as $v) {
            $sqlStr = "INSERT INTO `" . $val . "` VALUES (";
            foreach ($v as $zd) {
                $sqlStr .= "'" . $zd . "', ";
            }
            //去掉最后一个逗号和空格
            $sqlStr = substr($sqlStr, 0, strlen($sqlStr) - 2);
            $sqlStr .= ");\r\n";
            file_put_contents($to_file_name, $sqlStr, FILE_APPEND);
        }

        file_put_contents($to_file_name, "\r\n", FILE_APPEND);
    }
}