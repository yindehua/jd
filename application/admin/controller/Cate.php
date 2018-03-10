<?php
namespace app\admin\controller;
use think\Controller;
//分类管理控制器
class Cate extends Controller{
	//商品列表方法
	public function catelist(){
		//获取所有商品列表
		//到数据表cate获取所有数据组成一个二维数组，但是不按数据库的顺序，重新排序
		$cate_select=db('cate')->order('cate_sort')->select();
		$cate_model=model('Cate');//初始化一个Cate的model
		$cate_list=$cate_model->getCate($cate_select);//用model调用getCate函数，把上面获取的数组传给getCate,
		//以下代码为了分页
		$cate_totle=count($cate_list);//得到数据总数
		$page_class=new Page($cate_totle,10);//每页多少条数据
		$show=$page_class->fpage();//模板显示的内容
		$limit=$page_class->setlimit();//获取limit信息
		$limit=explode(',',$limit);
		$list=array_slice($cate_list,$limit[0],$limit[1]);
		//分发分页数据跟列表信息到页面
		$this->assign('show', $show);
		$this->assign('cate_list', $list);
		return view();
	}
	//到数据库取输出到页面，即提供所有分类，添加的时候可以选父分类
	public function add(){
		$cate_select=db('cate')->select();//到数据库选取数据
		$cate_model=model('Cate');//new一个Cate的model
		$cate_list=$cate_model->getCate($cate_select);//new的对象用于调用getCate，读取子类
		//获取无限分类
		$this->assign('cate_list',$cate_list);
		return view('add');
	}
	//在页面获取用户输入的分类名称跟选择的所属分类添加到数据库
	public function addhanddle()
	{
		$post = request()->post();//post带过来form表单的数据，包括修改内容
		//dump($post);
		$cate_add=db('cate')->insert($post);//post获取的数据直接插入数据库
		if($cate_add)
		{
			$this->success('添加分类成功','cate/add');
		}
		else
		{
			$this->error('添加分类失败','cate/add');
		}
	}
	//显示分类修改界面
	public function upd($cate_id='')
	{
		if($cate_id=='')
			//{$this->error('cate/catelist');}
			{$this->redirect('cate/catelist');}//如果没有id，就回到原来的页面，重定向redirect
		$cate_find=db('cate')->find($cate_id);//助手函数，查找数据
		if($cate_find=='')
			{$this->redirect('cate/catelist');}

		// $cate_findParent=db('cate')->find($cate_pid);//所属分类
		// dump($cate_id);
		// dump($cate_find);
		$cate_select=db('cate')->select();//到数据库选取数据
		$cate_model=model('Cate');//new一个Cate的model
		$cate_list=$cate_model->getCate($cate_select);//此方法没给第二个参数，默认从顶级开始找子类
		//获取无限分类
		$this->assign('cate_list',$cate_list);
		$this->assign('cate_find',$cate_find);
		// $this->assign('cate_findParent',$cate_findParent);
		return $this->fetch();
	}
	//在页面获取用户输入需要修改的分类名称跟选择修改的所属分类添加到数据库
	public function updhanddle()
	{

		$post = request()->post();
		//dump($post);die;
		$cate_upd=db('cate')->update($post);//post获取的数据直接插入数据库
		if($cate_upd!==false)
		{
			$this->success('修改分类成功','cate/catelist');
		}
		else
		{
			$this->error('修改分类失败','cate/catelist');
		}
	}
	//显示删除分类页面
	public function del($cate_id='')
	{
		if($cate_id=='')
			//{$this->error('cate/catelist');}
			{$this->redirect('cate/catelist');}//如果没有id，就回到原来的页面，重定向redirect
		$cate_find=db('cate')->find($cate_id);//助手函数，查找数据,返回该id对应的子类所有信息
		if($cate_find=='')
			{$this->redirect('cate/catelist');}

		$cate_select=db('cate')->select();//到数据库选取数据
		$cate_model=model('Cate');//new一个Cate的model
		//有给第二个参数，不再默认从顶级开始找子类，而是找$cate_id的子类
		$cate_list=$cate_model->getCate($cate_select,$cate_id);//取得参数的所有子类的信息,但不包括自己的信息
		$cate_list[]=$cate_find;//把自己的信息也加入到需要删除的对象里，与上一行配合
		foreach ($cate_list as $key => $value) {
			db('cate')->where('cate_id',$value['cate_id'])->delete();//把$cate_list遍历删除
		}
		$this->redirect('cate/catelist');
	}

	//对分类进行排序
	public function sort(){

		$post=request()->post();//获取页面提交过来的数据，id跟sort
		foreach ($post as $key => $value) {
			db('cate')->update([
					'cate_id'=>$key,
					'cate_sort'=>$value,
				]);
		}
		$this->redirect('cate/catelist');
	}
	



}
