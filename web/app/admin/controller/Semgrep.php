<?php
declare (strict_types=1);

namespace app\admin\controller;

use app\admin\model\SemgrepModel;
use think\facade\Db;
use think\facade\Session;
use think\facade\View;
use think\Request;

class Semgrep extends Common
{
    public function index(Request $request)
    {
        $userInfo = Session::get('userInfo');
        $project_id = $request->param('project_id', 0);
        $where = empty($project_id) ? [] : ['project_id' => $project_id];
        $countList = SemgrepModel::getDetailCount($where);

        $where2 = [];
        if ($request->param('extra') !== null) $where2[] = ['extra', 'like', "%{$request->param('extra')}%"];

        $list = Db::name('semgrep')->where(['user_id' => $userInfo['id']])->where($where)->where($where2)->paginate([
            'list_rows' => 10,
            'var_page' => 'page',
            'query' => $request->param(),
        ]);
        $bugList['list'] = $list->items();
        $page = $list->render();

        foreach ($bugList['list'] as &$item) {
            $item['extra'] = json_decode($item['extra'], true);
            $item['tags'] = [];
        }

        $data = ['countList' => $countList, 'bugList' => $bugList, 'page' => $page];

        return View::fetch('index', $data);
    }

    public function detail(Request $request)
    {
        $userInfo = Session::get('userInfo');
        $id = $request->param('id');
        $where = ['id' => $id];
        $info = Db::table('semgrep')->where(['user_id' => $userInfo['id']])->where($where)->find();

        $where = ['project_id' => $info['project_id']];
        $preId = Db::table('semgrep')->where(['user_id' => $userInfo['id']])->where($where)->where('id', '<', $id)->value('id');
        $nextId = Db::table('semgrep')->where(['user_id' => $userInfo['id']])->where($where)->where('id', '>', $id)->value('id');

        return View::fetch('semgrep/detail', ['info' => $info, 'preId' => $preId, 'nextId' => $nextId]);
    }

}
