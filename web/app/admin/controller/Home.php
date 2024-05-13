<?php
declare (strict_types=1);

namespace app\admin\controller;

use app\admin\model\DomainModel;
use app\admin\model\PortsModel;
use app\admin\model\ProductCateModel;
use app\admin\model\ProductsModel;
use app\admin\model\UrlsModel;
use think\facade\Db;
use think\facade\View;
use think\Request;

class Home extends Common
{
    public function index(Request $request)
    {

        $bugList = Db::table('fortify')->field('Category,count(Category) as num')->group('Category')->select()->toArray();
        $bugList = array_column($bugList, 'num','Category');
        arsort($bugList);

        $semgrepList = Db::table('semgrep')->field('check_id,count(check_id) as num')->group('check_id')->select()->toArray();
        foreach ($semgrepList as &$item){
            $tempArr =explode(".",$item['check_id']);


            $item['check_id'] = array_pop($tempArr).".".array_pop($tempArr);

        }
        $semgrepList = array_column($semgrepList, 'num','check_id');

        arsort($semgrepList);

        $hemaList = Db::table('hema')->field('type,count(type) as num')->group('type')->select()->toArray();
        $hemaList = array_column($hemaList, 'num','type');
        arsort($hemaList);


        $otherList = [
            ['title' => '风险类型', 'lists' =>$bugList],
            ['title' => '风险函数', 'lists' => $semgrepList],
            ['title' => 'WebShell', 'lists' => $hemaList],
        ];

        $data = ['otherList' => $otherList];

        return View::fetch('index', $data);
    }
}