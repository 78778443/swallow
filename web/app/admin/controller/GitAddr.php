<?php
declare (strict_types=1);

namespace app\admin\controller;

use think\facade\Db;
use think\facade\View;
use think\Request;

class GitAddr extends Common
{
    public function index(Request $request)
    {
        $where = ['project_id' => 1];
        $totalNum = Db::table('git_addr')->where($where)->count();


        $list = Db::name('git_addr')->where($where)->paginate([
            'list_rows' => 10,
            'var_page' => 'page',
        ]);
        $mainList = $list->items();
        $page = $list->render();


        $data = ['mainList' => $mainList, 'totalNum' => $totalNum, 'page' => $page];

        return View::fetch('index', $data);
    }
}
