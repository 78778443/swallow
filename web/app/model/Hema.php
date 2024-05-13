<?php

namespace app\model;

use think\facade\Db;
use think\Model;


class Hema extends Model
{
    public static function start()
    {
        //安装工具
        $toolPath = "/data/tools/hema/";
        self::autoDownTool($toolPath);

        //读取上游数据
        $where = [];
        $list = Db::table('git_addr')->whereNotNull('code_path')->where($where)
            ->whereTime('hema_scan_time', '<=', date('Y-m-d', time() - (7 * 86400)))
            ->select()->toArray();


        //处理数据
        $data = [];
        foreach ($list as $value) {


            $codePath = $value['code_path'];
            //执行检测脚本
            self::execTool($toolPath, $codePath);

            //录入检测结果
            $tempList = self::writeData($codePath, $toolPath,$value['git_addr']);

            //开始执行
            $data = array_merge($data, $tempList);

            if (empty($data)) {
                print_r("hema 扫描失败:{$value['code_path']}\n");
                continue;
            }
            Db::table('hema')->replace()->strict(false)->insertAll($data);

            //更新扫描时间
            $data = ['hema_scan_time' => date('Y-m-d H:i:s')];
            Db::table('git_addr')->where(['id' => $value['id']])->update($data);
        }
    }

    public static function execTool(string $toolPath, string $codePath)
    {

        $cmd = "cd {$toolPath} && ./hm scan {$codePath}";
        echo $cmd . PHP_EOL;
        exec($cmd);
        return true;

    }

    public static function writeData($codePath, string $toolPath,$gitAddr)
    {

        $outPath = "{$toolPath}/result.csv";
        if (!file_exists($outPath)) {
            print_r("没有找到结果文件:{$outPath}\n");
            return [];
        }
        $result = self::readCsv($codePath, $outPath);
        //去掉表头
        if (isset($result[0])) unset($result[0]);
        $data = [];
        foreach ($result as $val) {
            $oneData = [
                'type' => $val[1],
                'filename' => $val[2],
                'git_addr' => $gitAddr,
            ];
            $data[] = $oneData;
        }
        return $data;
    }

    /**
     * [ReadCsv 读取CSV为数组]
     * @param string $uploadfile [文件路径]
     */
    public static function readCsv($codeBasePath, $uploadfile = '')
    {
        $file = fopen($uploadfile, "r");
        while (!feof($file)) {
            $data[] = fgetcsv($file);
        }
        foreach ($data as $key => &$value) {
            if (!$value) {
                unset($data[$key]);
            }
            //        $value[2] = str_replace("{$codeBasePath}/", "", $value[2]);
        }
        fclose($file);
        return $data;
    }


    public static function autoDownTool($toolPath)
    {
        if (file_exists($toolPath)) {
            return true;
        }
        $dirName = dirname($toolPath);
        !file_exists($dirName) && mkdir($dirName, 0777, true);

        $cmd = "cd {$dirName} && git clone --depth=1 https://gitee.com/songboy/hema.git  && chmod -R 777 hema";
        echo "正在下载工具 $cmd " . PHP_EOL;
        exec($cmd);

        $cmd = "cd {$toolPath} && tar -zxvf hm-linux-amd64.tgz";
        exec($cmd);
    }
}