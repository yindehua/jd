<?php
namespace app\admin\controller;

//关键字控制器

class Keywords extends \think\Controller
{
	//获取关键字列表并显示方法
	public function keywordslist(){
		$keywords_select=db('keywords')->paginate(10);
		if($keywords_select){
			$this->assign('keywords_select',$keywords_select);
			return $this->fetch();
		}else{
			$this->redirect('index/index');
		}
	}
	//添加关键字
	public function add(){

		return view();
	}
	//把页面表单带过来的数据插入数据库
	public function addhanddle(){
		// dump($_GET['keywords_name']);
		// die;
		$post=request()->post();
		// dump($post);die;
		//验证器验证输入信息
		$validate=validate('keywords');
		
		if(!$validate->check($post))
		{
			$this->error($validate->getError(),'keywords/add');
		}
		else
		{
			$keywords_add_result=db('keywords')->insert($post);
			if($keywords_add_result)
			{
				$this->success('关键字添加成功','keywords/add');
			}
			else
			{
				$this->error('关键字添加失败','keywords/add');
			}
		}
	}


	    //修改关键字页面，把用户输入的参数带回来
		public function upd($keywords_id=''){
		if($keywords_id=='')
		{$this->redirect('keywords/keywordslist');}//如果没有id，就回到原来的页面，重定向redirect
		$keywords_find=db('keywords')->find($keywords_id);//助手函数，查找数据
		if($keywords_find=='')
		{$this->redirect('keywords/keywordslist');}
		//dump($keywords_find);die;
		$this->assign('keywords_find',$keywords_find);
		return $this->fetch();
		}

		//把表单传过来的修改内容插入数据库
		public function updhanddle(){
		$post = request()->post();
		$keywords_upd=db('keywords')->update($post);
		//post获取的数据直接插入数据库
		if($keywords_upd!==false)
		{
			$this->success('修改关键字成功','keywords/keywordslist');
		}
		else
		{
			$this->error('修改关键字失败','keywords/keywordslist');
		}
		}
}