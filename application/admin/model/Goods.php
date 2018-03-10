<?php
namespace app\admin\model;
//Goods数据模型层
class Goods extends \think\Model
{
	protected $resultSetType='collection';
	public function keywords(){
		//关键字与商品的多对多关系
		return $this->belongsToMany('Keywords','goods_keywords');
	}


	public function cate(){
		//分类和商品的一对多关系
		return $this->belongsTo('Cate','goods_pid');
	}

	public function img(){
		//商品和细节图的一对多关系
		return $this->hasMany('Img');
	}
}