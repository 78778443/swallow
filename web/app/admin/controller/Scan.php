<?php
declare (strict_types=1);

namespace app\admin\controller;

use app\admin\model\FortifyModel;
use think\facade\Db;
use think\facade\Session;
use think\facade\View;
use think\Request;

class Scan extends Common
{
    public function report(Request $request)
    {
        $userInfo = Session::get('userInfo');
        $project_id = $request->param('project_id', 0);
        $where = empty($project_id) ? [] : ['project_id' => $project_id];
        $countList = FortifyModel::getDetailCount($project_id);


        if ($request->param('is_repair') !== null) $where['is_repair'] = $request->param('is_repair');
        if ($request->param('Folder') !== null) $where['Folder'] = $request->param('Folder');
        $list = Db::name('fortify')->where($where)->paginate([
            'list_rows' => 10,
            'var_page' => 'page',
            'query' => $request->param(),
        ]);
        $bugList['list'] = $list->items();
        $page = $list->render();

        foreach ($bugList['list'] as &$item) {
            $item['primary_info'] = json_decode($item['Primary'], true);
            $item['tags'] = [];
        }

        $data = [
            'countList' => $countList, 'bugList' => $bugList,
            'page' => $page, 'param' => $request->param()
        ];

        return View::fetch('fortify/report', $data);
    }


    public function detail(Request $request)
    {
        $userInfo = Session::get('userInfo');
        $id = $request->param('id');
        $where = [ 'id' => $id];
        $info = Db::table('fortify')->where($where)->find();

        $where = ['project_id' => $info['project_id'],'git_addr'=>$info['git_addr']];
        $preId = Db::table('fortify')->where($where)->where('id', '<', $id)->value('id');
        $nextId = Db::table('fortify')->where($where)->where('id', '>', $id)->value('id');

        return View::fetch('fortify/detail', ['info' => $info, 'preId' => $preId, 'nextId' => $nextId]);
    }
}
