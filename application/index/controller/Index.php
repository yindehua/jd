<?php
namespace app\index\controller;
//use think\Controller;
//商城首页控制器
class Index extends \think\Controller
{

    public function index()
    {
    	$cate_select = db('cate')->select();//链接数据库读取数组
    	$cate_model = model('cate');//新建一个前台的model对象

    	//用新建的model对象去调用，getChildren方法，把从数据库都出来的数组作为参数传入
    	$cate_list = $cate_model->getChildren($cate_select);
		$this->assign('cate_list',$cate_list);//分发到页面
        //return $this->fetch();
       return view();
    }
    


    
}
