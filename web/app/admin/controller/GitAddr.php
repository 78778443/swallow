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
        $where = [];
        $totalNum = Db::table('git_addr')->where($where)->count();


        $list = Db::name('git_addr')->where($where)->paginate([
            'list_rows' => 10,
            'var_page' => 'page',
        ]);
        $mainList = $list->items();
        $page = $list->render();

        foreach ($mainList as &$item) {
            $where = ['project_id' => $item['project_id']];
            $item['fortify'] = Db::table('fortify')->where($where)->count();
            $item['semgrep'] = Db::table('semgrep')->where($where)->count();
            $item['webshell'] = Db::table('hema')->where($where)->count();
        }

        $data = ['mainList' => $mainList, 'totalNum' => $totalNum, 'page' => $page];

        return View::fetch('index', $data);
    }


    public function detail(Request $request)
    {
        $id = $request->param('id');
        $gitInfo = Db::table('git_addr')->find($id);
        $prInfo = Db::table('project')->find($gitInfo['project_id']);

        //设置条件
        $where = ['git_addr' => $gitInfo['git_addr'], 'project_id' => $gitInfo['project_id']];

        //查询列表数据
        $fortifyList = Db::table('fortify')->where($where)->limit(10)->select()->toArray();
        $semgrepList = Db::table('semgrep')->where($where)->limit(10)->select()->toArray();
        $hemaList = Db::table('hema')->where($where)->limit(10)->select()->toArray();

        $data = ['gitInfo' => $gitInfo, 'prInfo' => $prInfo,
            'fortifyList' => $fortifyList,
            'semgrepList' => $semgrepList,
            'hemaList' => $hemaList,
            'fortifyCount' => Db::table('fortify')->where($where)->count(),
            'semgrepCount' => Db::table('semgrep')->where($where)->count(),
            'hemaCount' => Db::table('hema')->where($where)->count(),
        ];
        return View::fetch('detail', $data);
    }

    public function _add(Request $request)
    {
        $gitAddr = $request->param('git_addr');
        $data = ['project_id' => 1];

        $gitAddrArr = explode("\n", $gitAddr);

        foreach ($gitAddrArr as $url) {
            $data['git_addr'] = trim($url);
            Db::table('git_addr')->strict(false)->extra('IGNORE')->insert($data);
        }

        return redirect($_SERVER['HTTP_REFERER']);

    }

    public function _del($id)
    {
        Db::table('git_addr')->delete($id);

        return redirect($_SERVER['HTTP_REFERER']);
    }

    public function _scan($id)
    {
        $data = ['fortify_scan_time' => null, 'semgrep_scan_time' => null, 'hema_scan_time' => null,];
        Db::table('git_addr')->where(['id' => $id])->update($data);

        return redirect($_SERVER['HTTP_REFERER']);
    }
}
