<?php
declare (strict_types=1);

namespace app\admin\controller;

use app\admin\validate\ProjectConf;
use app\BaseController;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\Route;
use think\facade\View;
use think\Request;
use think\facade\Session;
class Login extends BaseController
{

    public function index(Request $request)
    {


        return View::fetch('index');
    }

    public function _dologin(Request $request)
    {
        $username = $request->param('username');
        $password = $request->param('password');

        $where = ['username'=>$username,'password'=>md5($password)];
        $userInfo = Db::table('user')->where($where)->find();

        Session::set('userInfo',$userInfo);
        return redirect('/admin/home/index');
    }

    public function logout(Request $request)
    {
        Session::delete('userInfo');

        return redirect('/admin/login/index');
    }

}
