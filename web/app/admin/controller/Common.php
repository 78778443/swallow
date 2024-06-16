<?php
declare (strict_types=1);

namespace app\admin\controller;

use app\BaseController;
use think\facade\Db;
use think\facade\View;
use think\facade\Session;
class Common extends BaseController
{

    public function __construct()
    {

        if (empty(Session::get('userInfo'))) {
            header('location:'.URL('login/index'));
            exit();
        }
        $controName = \think\facade\Request::controller();
        $actionName = \think\facade\Request::action();

        $currentHref = strtolower("{$controName}/{$actionName}");

        $menuList = [
            ['title' => '概要', 'href' => 'home/index'],
            ['title' => '项目列表', 'href' => 'project/index'],
            ['title' => '仓库列表', 'href' => 'git_addr/index'],
            ['title' => '<span style="font-size:17px;"> Fortify</span>', 'href' => 'scan/report', 'icon' => '漏洞管理'],
            ['title' => '<span style="font-size:13px;"> SemGrep</span>', 'href' => 'semgrep/index', 'icon' => 'SemGrep'],
            ['title' => '<span style="font-size:13px;"> WebShell</span>', 'href' => 'hema/index', 'icon' => 'WebShell'],
            ['title' => '<span style="font-size:15px;"> Code&nbsp;QL</span>', 'href' => 'codeql/index', 'icon' => 'CodeQL'],
            ['title' => '用户管理', 'href' => 'user/index'],
        ];
        $headImg = 'https://thirdwx.qlogo.cn/mmopen/vi_32/DYAIOgq83erTCOcE08e8ia72SSqabRHQJr43rRJ1s0Tam2gib9RdQUClVicKyGlibLc0AOuzhTI6qpqY74gVrgzsvA/132';
        View::assign('href', $currentHref);
        View::assign('menu_list', $menuList);
        View::assign('userInfo', ['headimgurl' => $headImg]);
    }


    protected function apiReturn($data = [], $status = 0, $msg = '', $isRaw = false)
    {
        if ($isRaw) {
            return json($data);
        }
        $result['code'] = $status;
        $result['msg'] = $msg;
        $result['data'] = $data;


        return json($result);
    }
}
