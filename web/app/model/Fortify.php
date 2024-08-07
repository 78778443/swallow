<?php

namespace app\model;

use think\facade\Db;
use think\facade\Session;
use think\Model;


class Fortify extends Model
{
    public static function start()
    {
        $obj = new Fortify();
        $obj->autoDownTool();
        //读取上游数据
        $where = [];
        $list = Db::table('git_addr')->whereNotNull('code_path')->where($where)
            ->whereTime('fortify_scan_time', '<=', date('Y-m-d', time() - (7 * 86400)))
            ->select()->toArray();


        //处理数据
        foreach ($list as $item) {

            $outPath = "/tmp/fortify_" . md5($item['code_path']);
            $ret = $obj->execTool($item, $outPath);

            if (empty($ret)) {
                print_r("fortify 扫描失败:{$item['code_path']}\n");
                continue;
            }
            foreach ($ret as &$value) $value['user_id'] = $item['user_id'];
            Db::table('fortify')->replace()->strict(false)->insertAll($ret);

            $data = ['fortify_scan_time' => date('Y-m-d H:i:s')];
            Db::table('git_addr')->where(['id' => $item['id']])->update($data);
        }
    }


    function execTool($codeInfo, $outPath)
    {
        $codePath = $codeInfo['code_path'];
        $buildId = md5($codePath);

        if (file_exists($outPath)) {
            chmod($outPath, 0777);
        }

        if (file_exists($codePath) == false) {
            GitAddr::execTool($codeInfo);
            echo "代码目录不存在:{$codePath} , 即将自动下载... \n";
        }

        $fortifyPath = "/data/tools/fortify";

        if (!file_exists($fortifyPath)) die("fortify 代码扫描器不存在:{$fortifyPath}\n");

        $base = "cd {$fortifyPath}/bin && ";
        if (file_exists("{$outPath}.fpr") == false) {
            $cmd = $base . "./sourceanalyzer -b {$buildId} -clean";
            execLog($cmd);
            $cmd = $base . "./sourceanalyzer -b {$buildId} -Xmx4096M -Xms2048M -Xss48M     -source 1.8 -machine-output   {$codePath}";
            execLog($cmd);
            $cmd = $base . "./sourceanalyzer -b {$buildId} -scan -format fpr    -f {$outPath}.fpr -machine-output ";
//        $cmd .= " -no-default-rules  -rules  {$fortifyPath}/Core/config/rules/core_php.bin";
            echo $cmd . PHP_EOL;
            execLog($cmd);
        } else {
            print_r("fortify扫描文件 {$outPath}.fpr 已存在,不再重新扫描\n");
        }

        if (file_exists("{$outPath}.xml") == false) {
            $cmd = $base . "./ReportGenerator  -format xml -f {$outPath}.xml -source {$outPath}.fpr -template DeveloperWorkbook.xml";
            execLog($cmd);
        }

        $xmlFile = "{$outPath}.xml";

        if (file_exists($xmlFile) === false) {
            print_r("fortify的XML文件不存在:{$xmlFile}\n");
            return [];
        }

        return $this->getFortifData($xmlFile, $codeInfo);

    }


    function getFortifData($xmlPath,$gitInfo)
    {

        $git_addr = $gitInfo['git_addr'];
        $str = file_get_contents($xmlPath);

        $obj = simplexml_load_string($str, "SimpleXMLElement", LIBXML_NOCDATA);
        $test = json_decode(json_encode($obj), true);

        if (!isset($test['ReportSection'][2])) {
            echo "{$xmlPath} 数据为空";
            return [];
        }

        $list = $test['ReportSection'][2]['SubSection']['IssueListing']['Chart']['GroupingSection'] ?? [];

        $list = isset($list['Issue']) ? [$list] : $list;

        $data = [];
        foreach ($list as &$value) {
            unset($value['MajorAttributeSummary']);
            $value = isset($value['Issue'][0]) ? $value['Issue'] : [$value['Issue']];
            foreach ($value as &$val) {
                unset($val['@attributes']);
                foreach ($val as &$v) {
                    $v = is_string($v) ? $v : json_encode($v);
                }
                $val['hash'] = md5($val['Primary']);
                $val['git_addr'] = $git_addr;
                $val['project_id'] = $gitInfo['project_id'];
                $data[] = $val;
            }
        }


        return $data;
    }


    function autoDownTool()
    {

    }
}