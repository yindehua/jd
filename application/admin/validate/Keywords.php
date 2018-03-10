<?php
namespace app\admin\validate;

use think\Validate;

class Keywords extends Validate
{
    protected $rule = [

        'keywords_name'  =>  'require|max:10|unique:keywords,keywords_name',
      
    ];
    protected $message=[

    	'keywords_name.require' => '请输入关键字',
    	'keywords_name.max'=>'请输入小于10个字符的关键字',
    	'keywords_name.unique'=>'关键字已存在',
    ];



}