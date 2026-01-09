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

        $where = ['username' => $username, 'password' => md5($password)];
//        $where = ['username' => $username];

        $userInfo = Db::table('user')->where($where)->find();

        if ($userInfo) {
            Session::set('userInfo', $userInfo);
            return redirect('/admin/home/index');
        } else {
            // 登录失败，返回登录页面并显示错误信息
            return redirect('/admin/login/index')->with('error', '用户名或密码错误');
        }
    }

    public function register(Request $request)
    {


        return View::fetch('register');
    }

    public function _doRegister(Request $request)
    {
        $username = $request->param('username');
        $password = $request->param('password');
        $repassword = $request->param('repassword');
        if ($password != $repassword) {
            return redirect('/admin/login/register')->with('error', '两次密码不一致');
        }
        //判断是否已经存在
        $where = ['username' => $username];
        $userInfo = Db::table('user')->where($where)->find();
        if ($userInfo) {
            return redirect('/admin/login/register')->with('error', '用户名已经存在');
        }

        $data = ['username' => $username, 'password' => md5($password)];
        $userId = Db::table('user')->insertGetId($data);
        //获取用户信息
        $userInfo = Db::table('user')->where(['id' => $userId])->find();

        Session::set('userInfo', $userInfo);
        return redirect('/admin/home/index');
    }

    public function logout(Request $request)
    {
        Session::delete('userInfo');

        return redirect('/admin/login/index');
    }

}