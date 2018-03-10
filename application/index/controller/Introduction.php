<?php

namespace app\index\controller;

use think\Controller;

class Introduction extends Controller
{
    //显示商品详情的控制器
    public function introduction()
    {
        //
        //return $this->fetch();
        return view('introduction');

    }
}
