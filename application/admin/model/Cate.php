<?php
namespace app\admin\model;

//use think\Model;
/**
* 商品分类模型
*/
class Cate extends \think\Model
{
	protected $resultSetType='collection';
	//由父类id得到全部子类id,查看一个父类有没有子类的关键是看有没有元素的pid等于父类的id，递归可实现
	public function getCate($cate_list,$pid=0,$level=1)
	{
		//定义静态数据保存整个搜索结果
		static $arr = array();
		foreach($cate_list as $key => $value)
		{
			//#cate_list为cate数组
			if($value['cate_pid']==$pid)
			{

				$value['cate_level']=$level;
				//level只是为了输出“--”好辨认层次,str_repeat函数按照（$value['cate_level']-1）次数重复‘-’
				$value['str']=str_repeat('—',$value['cate_level']-1);
				$arr[]=$value;//把$value放入arr数组
				//找到一个子类以后继续递归找子类的子类
				$this->getCate($cate_list,$value['cate_id'],$level+1);
			}
		}
		//dump($arr);
		//halt($arr);//为何只显示第一层数据的数据而没有后面的数据
		return $arr;
	}


	//得到全部子级，多维数组
	public function getChildren($cate_list,$pid=0)//第二个参数如不提供将默认为0，即顶层
	{//由父类id得到全部子类id,查看一个父类有没有子类的关键是看有没有元素的pid等于父类的id，递归可实现
		//定义静态数据保存整个搜索结果
		$arr = array();
		foreach($cate_list as $key => $value)
		{
			if($value['cate_pid']==$pid)
			{
				$value['children']=$this->getChildren($cate_list,$value['cate_id']);
				$arr[] = $value;
			}
		}
		return $arr;
	}

	// //查找父类，多维数组
	// public function getFather($cate_list,$pid)
	// {//由子类id得到全部父类递归可实现
	// 	//定义静态数据保存整个搜索结果
	// 	$arr = array();
	// 	foreach($cate_list as $key => $value)
	// 	{
	// 		if($value['cate_id']==$pid)
	// 		{
	// 			$value['father']=$this->getFather($cate_list,$value['cate_pid']);
	// 			$arr[] = $value;
	// 		}
	// 	}
	// 	return $arr;
	// }

	//由子类得到全部父类
	public function getFather($cate_list,$pid=0)
	{
		//定义静态数据保存整个搜索结果
		static $arr = array();
		foreach($cate_list as $key => $value)
		{
			//#cate_list为cate数组
			//dump($cate_list);
			if($value['cate_id']==$pid)
			{
				array_unshift($arr,$value);//翻转数组内容
				//找到一个子类以后继续递归找子类的子类
				$this->getFather($cate_list,$value['cate_pid']);
			}
		}
		
		//dump($arr);
		//halt($arr);//为何只显示第一层数据的数据而没有后面的数据
		return $arr;
	}

	public function goods(){
		//分类和商品的一对多关系
		return $this->hasMany('Cate');
	}
}