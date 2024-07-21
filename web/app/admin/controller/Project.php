<?php
declare (strict_types=1);

namespace app\admin\controller;

use app\admin\model\DomainModel;
use app\admin\validate\ProjectConf;
use app\admin\validate\ProjectConfValidate;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\Route;
use think\facade\Session;
use think\facade\View;
use think\Request;

class Project extends Common
{

    public function index(Request $request)
    {

        $userInfo = Session::get('userInfo');
        $where = [];
        $totalNum = Db::name('project')->where(['user_id' => $userInfo['id']])->where($where)->count();
        $list = Db::name('project')->where(['user_id' => $userInfo['id']])->where($where)->paginate([
            'list_rows' => 10,
            'var_page' => 'page',
        ]);
        $mainList = $list->items();
        $page = $list->render();

        $data = ['mainList' => $mainList, 'totalNum' => $totalNum, 'page' => $page];

        return View::fetch('index', $data);
    }


    public function setting(Request $request)
    {

        $projectId = 1;
        $where = ['project_id' => $projectId];
        $projectConf = Db::table('project_conf')->where($where)->column('value', 'key');

        $mainList = Db::table('git_addr')->where($where)->select()->toArray();
        $data = ['mainList' => $mainList, 'projectConf' => $projectConf];
        View::assign($request->param());

        return View::fetch('setting', $data);
    }

    public function _add_domain(Request $request)
    {
        $domainStr = $request->param('domain');
        $domainArr = explode("\n", $domainStr);
        foreach ($domainArr as $domain) {
            $data = ['domain' => $domain, 'project_id' => 1];
            $data['user_id'] = Session::get('userInfo')['id'];
            Db::table('domain')->extra('IGNORE')->insert($data);
            Db::table('git_addr')->extra('IGNORE')->insert($data);
        }


        return redirect($_SERVER['HTTP_REFERER']);
    }


    public function _del_domain(int $id)
    {
        Db::table('git_addr')->delete($id);


        return redirect($_SERVER['HTTP_REFERER']);
    }


    public function update_project_conf(Request $request)
    {

        $params = array_map('trim', $request->param());
        try {
            validate(ProjectConfValidate::class)->check($params);
        } catch (ValidateException $e) {
            // 验证失败 输出错误信息
            $this->error($e->getMessage());
        }


        foreach ($params as $key => $value) {
            $data = ['value' => $value, 'key' => $key, 'project_id' => 1];
            if ($data['key'] == 'cycle_start_time') $data['value'] = str_replace('T', ' ', $data['value']);
            Db::table('project_conf')->replace(true)->strict(false)->save($data);
        }

        //对接蜻蜓API
        $retUrl['paramRet'] = DomainModel::setQingtingParams();
        //修改周期运行时间
        $retUrl['ruleRet'] = DomainModel::setRunRule();

        return redirect((string)url("project/setting", $retUrl));
    }

    public function _del($id)
    {
        Db::table('project')->delete($id);

        return redirect($_SERVER['HTTP_REFERER']);
    }

    public function _add(Request $request)
    {
        $gitAddr = $request->param('name');
        $desc = $request->param('desc');

        $data = ['name' => $gitAddr, 'desc' => $desc];
        $data['user_id'] = Session::get('userInfo')['id'];
        $projectId = Db::table('project')->strict(false)->extra('IGNORE')->insertGetId($data);


        $data = ['project_id' => $projectId];
        $gitAddr = $request->param('git_addr');
        $gitAddrArr = explode("\n", $gitAddr);

        foreach ($gitAddrArr as $url) {
            $data['git_addr'] = trim($url);
            $data['user_id'] = Session::get('userInfo')['id'];
            Db::table('git_addr')->strict(false)->extra('IGNORE')->insert($data);
        }

        return redirect($_SERVER['HTTP_REFERER']);

    }
}
