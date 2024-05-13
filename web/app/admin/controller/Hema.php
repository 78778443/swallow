<?php
declare (strict_types=1);

namespace app\admin\controller;

use app\admin\model\FortifyModel;
use app\admin\model\HemaModel;
use think\facade\Db;
use think\facade\View;
use think\Request;

class Hema extends Common
{
    public function index(Request $request)
    {
        $project_id = $request->param('project_id', 0);
        $where = empty($project_id) ? [] : ['project_id' => $project_id];
        $countList = HemaModel::getDetailCount($where);

        $list = Db::name('hema')->where($where)->paginate([
            'list_rows' => 10,
            'var_page' => 'page',
            'query' => $request->param(),
        ]);


        $bugList['list'] = $list->items();

        $page = $list->render();
        foreach ($bugList['list'] as &$item) {
            $item['tags'] = [];
        }

        $data = ['countList' => $countList, 'bugList' => $bugList, 'page' => $page];

        return View::fetch('index', $data);
    }

}