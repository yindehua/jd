<?php
namespace app\admin\model;
class Img extends \think\Model{
	protected $resultSetType='collection';
	public function goods(){
		return $this->belongsTo('Goods');
	}
}