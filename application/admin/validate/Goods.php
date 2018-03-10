<?php
namespace app\admin\validate;

use think\Validate;

class Goods extends Validate
{
    protected $rule = [
        'goods_name'  =>  'require|max:20',
        'goods_thumb' =>  'require',
        'goods_price' =>  'require|egt:1|float',
        'goods_after_price' =>  'egt:1|float',
        'goods_sales' =>  'require|egt:1|float',
        'goods_inventory' => 'require|egt:1|float',
        'goods_pid' => 'require',
    ];
    protected $message=[
    	'goods_name.require'=>'请输入商品名称',
    	'goods_thumb.require'=>'请上传商品图片',
    	'goods_price.require'=>'请输入商品价格',
    	'goods_sales.require'=>'请输入商品销量',
    	'goods_inventory.require'=>'请输入商品库存',
    	'goods_pid.require'=>'请选择商品所属分类',
    	'goods_name.max'=>'商品名称最多20个字',
    	'goods_price.egt'=>'商品价格必须大于0',
    	'goods_price.float'=>'商品价格必须为数值',
    	'goods_after_price.egt'=>'商品促销价格必须大于0',
    	'goods_after_price.float'=>'商品促销价格必须为数值',
    	'goods_slaes.egt'=>'商品销量必须大于0',
    	'goods_slaes.float'=>'商品销量必须为数值',
    	'goods_inventory.egt'=>'商品库存必须大于0',
    	'goods_inventory.float'=>'商品库存必须为数值',


    ];



}