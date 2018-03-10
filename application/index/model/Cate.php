<?php
namespace app\index\model;
//前台商品分类模型层
class Cate extends \think\Model
{
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
} 
