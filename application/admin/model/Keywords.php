<?php
namespace app\admin\model;
//Goods数据模型层
class Keywords extends \think\Model
{
	protected $resultSetType='collection';
	public function goods(){
		return $this->belongsToMany('Goods','goods_keywords');
	}
}