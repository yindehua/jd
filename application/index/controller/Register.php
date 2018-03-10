<?php

namespace app\index\controller;

use think\Controller;
//用户注册控制器
class Register extends Controller
{
    public function register()
    {
        //
        //return $this->fetch();
        return view('register');

    }


}
