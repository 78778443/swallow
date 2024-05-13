<?php
declare (strict_types=1);

namespace app\admin\controller;

use app\admin\model\DomainModel;
use app\admin\validate\ProjectConf;
use app\admin\validate\ProjectConfValidate;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\Route;
use think\facade\View;
use think\Request;
use think\facade\Session;

class User extends Common
{

    public function __construct()
    {
        parent::__construct();
        $userInfo = Session::get('userInfo');
        if (empty($userInfo['is_admin']))  {
            header('location:'.URL('/admin/home/index'));
            exit();
        }
    }

    public function index(Request $request)
    {
        $where = [];
        $totalNum = Db::table('user')->where($where)->count();


        $list = Db::name('user')->where($where)->paginate([
            'list_rows' => 10,
            'var_page' => 'page',
        ]);
        $mainList = $list->items();
        $page = $list->render();


        $data = ['mainList' => $mainList, 'totalNum' => $totalNum, 'page' => $page];

        return View::fetch('index', $data);
    }

    public function _add(Request $request)
    {
        $username = $request->param('username');
        $password = $request->param('password');
        $data = [];
        $data['username'] = trim($username);
        $data['password'] = md5(trim($password));
        $data['is_admin'] = $request->param('is_admin', 'intval');


        Db::table('user')->strict(false)->extra('IGNORE')->insert($data);


        return redirect($_SERVER['HTTP_REFERER']);

    }

    public function _del($id)
    {
        Db::table('user')->delete($id);

        return redirect($_SERVER['HTTP_REFERER']);
    }


}
