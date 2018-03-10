<?php
namespace app\admin\controller;
if(!isset($_SESSION['imgupload'])){
	session_start();
}

class Goods extends \think\Controller
{
	//添加商品页面显示
	public function add(){	
		if(cookie('imgupload'))
		{	$cookie_arr=unserialize(cookie('imgupload'));

			foreach($cookie_arr as $key => $value)
			{
			$url_pre=DS.'public';
			$url=str_replace($url_pre,'.',$value);
			if(file_exists($url))
			{unlink($url);}
			}
			//rmdir(dirname($url));//删除空文件夹
		}

		unset($_SESSION['imgupload']);
		//以防有session影响添加内容
		if(session('goods_thumb'))
			{	//点击添加图片以后判断是否有session，通过session找到图片并删除
				$url_pre=DS.'public';
				$url=str_replace($url_pre,'.',session('goods_thumb'));
				if(file_exists($url))
				{unlink($url);}
				
			}
			session('goods_thumb',null);
		$cate_select=db('cate')->select();//到数据库选取数据
		$cate_model=model('Cate');//new一个Cate的model
    	$cate_list = $cate_model->getChildren($cate_select);//new的对象用于调用getCate，读取子类
		//获取无限分类
		$this->assign('cate_list',$cate_list);//分发到页面
		return $this->fetch();
	}
	//添加商品表单提交的处理
	public function addhanddle(){
		$post=request()->post();//获取表单带过来的信息
		//dump($post);die;
		$post['goods_thumb']=session('goods_thumb');//获取缩略图的地址
		//判断goods_status有没有带过来，没有的话赋0
		$post['goods_status']=isset($post['goods_status'])?$post['goods_status']:'0';
		//判断goods_pid有没有带过来，没有的话为null
		$post['goods_pid']=isset($post['goods_pid'])?$post['goods_pid']:null;
		//判断促销价格是否为空
		$post['goods_after_price']=empty($post['goods_after_price'])?'0':$post['goods_after_price'];
		if($post['goods_after_price']!=0)
		{
			if($post['goods_after_price']>=$post['goods_price'])
			{
				$this->error('促销价格不能大于商品价格');
			}
		}
		$imgupload = $_SESSION['imgupload'];
		//dump($post);die;
		// //验证器验证数据
		$validate=validate('Goods');

		if(!$validate->check($post))
		{
			$this->error($validate->getError(),'goods/add');
		}
		$goods_add_result=db('goods')->insertGetId($post);
		
		if($goods_add_result)
			{
				session('goods_thumb',null);
				$goods_model=new \app\admin\model\Goods;
				$goods=$goods_model->find($goods_add_result);
				foreach ($imgupload as $key => $value){
					$goods->img()->save(['url'=>$value]);
				}
				$this->success('商品添加成功！','goods/add');}
		else{
				session('goods_thumb',null);
				$this->success('商品添加失败！','goods/add');
			}
	}
	//上传图片
	public function uploadthumb()
	{
	// 获取表单上传文件 例如上传了001.jpg
    $file = request()->file('goods_thumb');
    // 移动到框架应用根目录/public/uploads/ 目录下
    $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
    //dump(ROOT_PATH);(string(19) "D:\phpStudy\WWW\jd\")
    if($info){
        // 成功上传后 获取上传信息
        // 输出 jpg
        $address = DS.'public' . DS . 'uploads'.DS.$info->getSaveName();
        session('goods_thumb',$address);//把图片信息放到session
        return $address; 
     }else{
         // 上传失败获取错误信息
         echo $file->getError();
    }
	}
	//删除所上传的图片
	public function canclethumb(){
		if(request()->isAjax()){
			if(session('goods_thumb'))
			{	//判断是否有session，通过session来找图片
				$url_pre=DS.'public';
				$url=str_replace($url_pre,'.',session('goods_thumb'));
				if(file_exists($url))
				{unlink($url);}//删除图片
			}
		}	
		//把图片的session删除
		session('goods_thumb',null);	
	}


	//商品列表显示
	public function goodslist($goods_pid=''){
		//new一个Goods的model
      	$goods_model=model('Goods');
		$cate_model=model('Cate');
		$cate_select=db('cate')->select();//到数据库选取数据
		$cate_list = $cate_model->getChildren($cate_select);//new的对象用于调用getCate，读取子类
		$this->assign('cate_list',$cate_list);//分发到页面

		$cate_find=db('cate')->find($goods_pid);
		if($cate_find)
		{	
			//$query->where('goods_pid','eq',$goods_pid);
			$goods_all=$goods_model->all(function($query) use ($goods_pid)
			{
				$query->where('goods_pid','eq',$goods_pid);//闭包方式实现分页
			});
			$this->assign('cate_find',$cate_find);//分发到页面
		}
		//获取无限分类
		else{
			$goods_all=$goods_model->all();
			$this->assign('cate_find','');//分发到页面,初次加载没有参数，会运行这里加载页面
		}//分页，每页多少行
		$goods_all_toArray=$goods_all->toArray();//对象转换为数据
		$goods_info=array();
		foreach($goods_all_toArray as $key=>$value)
		{
		$goods_get=$goods_model->get($value['goods_id']);
		$goods_keywords=$goods_get->keywords;//返回Goods为参数对应keywords的id
		$goods_keywords_toArray=$goods_keywords->toArray();//把得到的对象转换为数组
		$value['keywords']=$goods_keywords_toArray;//把转换过来的数组传给$value['keywords']
		//以下代码为取得catename
		$goods_cate=$goods_get->cate;//返回Goods为参数对应keywords的id
		$goods_cate_toArray=$goods_cate->toArray();//把得到的对象转换为数组
		$value['cate_name']=$goods_cate_toArray['cate_name'];
		$goods_info[]=$value;//把信息压入到数组
		//$goods_info[]=$goods_cate_toArray;
		}
		//$this->assign('goods_select',$goods_select);	
		$this->assign('goods_info',$goods_info);
		//以下代码为了分页
		//从数据库查询结果得到数据总数（上面把查询数据库的结果放到$goods_info）
		$goods_totle=count($goods_info);
		$page_class=new Page($goods_totle,5);//每页多少条数据
		$show=$page_class->fpage();//模板显示的内容
		$limit=$page_class->setlimit();//获取limit信息
		$limit=explode(',',$limit);
		//dump($limit);
		$list=array_slice($goods_info,$limit[0],$limit[1]);
		$this->assign('show', $show);
		$this->assign('goods_info', $list);
		return $this->fetch();
	}


	//显示修改商品信息
	public function upd($goods_id=''){
		if($goods_id=='')//判断goods_id有没有传过来
			{$this->redirect('goods/goodslist');}
			$goods_find=db('goods')->find($goods_id);
			//判断是否取出数据
			if(empty($goods_find)){
				$this->redirect('goods/goodslist');
			}
			//以访有session，先清理以下
			if(session('goods_thumb'))
			{	//点击添加图片以后判断是否有session，通过session找到图片并删除
				$url_pre=DS.'public';
				$url=str_replace($url_pre,'.',session('goods_thumb'));
				if(file_exists($url))
				{unlink($url);}
				
			}
			//先把图片的信息保存到session，如果需要修改图片的话可以用来删除图片
			session('goods_thumb',$goods_find['goods_thumb']);
			$cate_select=db('cate')->select();//到数据库选取数据,获取分类列表
			$cate_model=model('Cate');//new一个Cate的model
			$cate_list = $cate_model->getChildren($cate_select);//new的对象用于调用getCate，读取子类

			//获取无限分类
			//查找父类并分配到页面
			$cate_in=$cate_model->getFather($cate_select,$goods_find['goods_pid']);
			$cate_in_new['one']=$cate_in[0];
			$cate_in_new['two']=$cate_in[1];
			$cate_in_new['three']=$cate_in[2];
			//dump($cate_in);
			$this->assign('cate_in',$cate_in_new);
			$this->assign('cate_list',$cate_list);//分发所有分类到页面
			$this->assign('goods_find',$goods_find);//分发参数对应的商品信息到页面
			//获取商品细节图信息
			$img_select=db('img')->where('goods_id','eq',$goods_id)->select();
			$this->assign('img_select',$img_select);
			return $this->fetch();
	}
	//接收修改表单的信息并修改数据库
	public function updhanddle(){
		$post=request()->post();//获取表单带过来的信息
		 //dump($post);die;
		$post['goods_thumb']=session('goods_thumb');//获取缩略图的地址
		//判断goods_status有没有带过来，没有的话赋0
		$post['goods_status']=isset($post['goods_status'])?$post['goods_status']:'0';
		//判断goods_pid有没有带过来，没有的话为null
		$post['goods_pid']=isset($post['goods_pid'])?$post['goods_pid']:null;
		$post['goods_after_price']=empty($post['goods_after_price'])?'0':$post['goods_after_price'];
		if($post['goods_after_price']!=0)
		{
			if($post['goods_after_price']>=$post['goods_price'])
			{
				$this->error('促销价格不能大于商品价格');
			}
		}

		//验证器验证数据
		$validate=validate('Goods');
		if(!$validate->check($post))
		{
			
			$this->error($validate->getError(),'goods/goodslist');
		}
		$goods_add_result=db('goods')->update($post);
		
		if($goods_add_result)
			{
				session('goods_thumb',null);
				$this->success('商品修改成功！','goods/goodslist');}
		else{
			{
				session('goods_thumb',null);
				$this->success('商品修改失败！','goods/goodslist');}
		}
	}

	//实现商品删除方法del
	public function del($goods_id=''){
		if($goods_id=='')//判断goods_id有没有传过来
			{$this->redirect('goods/goodslist');}
			$goods_find=db('goods')->find($goods_id);
			//判断是否取出数据
			if(empty($goods_find)){
				$this->redirect('goods/goodslist');
			}
			//删除参数对应的数据库信息
			$goods_del_result=db('goods')->delete($goods_id);
			if($goods_del_result){
				//如果数据库信息删除成功，就删除对应的商品图片
				if($goods_find['goods_thumb'])
				{	//点击添加图片以后判断是否有session，通过session找到图片并删除
				$url_pre=DS.'public';
				$url=str_replace($url_pre,'.',$goods_find['goods_thumb']);
				if(file_exists($url))
				{unlink($url);}
				}

			$goods_keywords_del_result=db('goods_keywords')->where('goods_id','eq',$goods_id)->delete();

				$this->success('商品删除成功！','goods/goodslist');
			}
			else{
				$this->error('商品删除失败！','goods/goodslist');
			}
	}


	//关键字补全
	 public function keywordsAjax(){
        if (request()->isAjax()) {
            $post = request()->post();//获取页面传递过来的参数val
            $post_val = $post['val'];
            $keywords_like = db('keywords')->where('keywords_name','like','%'.$post_val.'%')->limit(1)->select();
            return $keywords_like;//把查找出来的数据返回
        }
    }

    //添加提交过来的关键字的方法
    public function keywordsaddhanddle(){
    	$post=request()->post();
    	$goods_id=array_keys($post)[0];//获取post传过来的key值
    	$keywords_name=array_values($post)[0];//获取post传过来的value值
    	//dump($goods_id);dump($keywords_name);die;
    	if(empty($keywords_name))
    	{
    		$this->error('关键字不能为空','goods/goodslist');
    	}
    	$keywords_find=db('keywords')->where('keywords_name','eq',$keywords_name)->find();
    	//dump($keywords_find);die;
    	if(empty($keywords_find)){
    		$this->error('关键字不存在，请先添加','keywords/add');
    	}
    	$keywords_id=$keywords_find['keywords_id'];
    	$goods_keywords_find=db('goods_keywords')->where('goods_id','eq',$goods_id)->
    	where('keywords_id','eq',$keywords_id)->find();
		if(!empty($goods_keywords_find)){
		$this->redirect('goods/goodslist');
    	}

    	//dump($keywords_id);die;
    	$goods_model=model('goods');
    	$goods=$goods_model->get($goods_id);
    	//添加关联的中间表数据
    	$goods->keywords()->attach($keywords_id);
    	$this->redirect('goods/goodslist');

    }


    //删除商品的关键字，注意不是删除关键字
    public function keywordsdelhanddle($keywords_name='',$goods_id=''){
    	//判断keywords_name，goods_id是否都传过来了
    	if(empty($keywords_name)||empty($goods_id)){
    		$this->redirect('goods/goodslist');
    	}
    	$goods_find=db('goods')->find($goods_id);
    	//判断时候找到goods_id对应的商品
    	if(empty($goods_find)){
    		$this->redirect('goods/goodslist');
    	}
    	//用商品id去找关键字
    	$keywords_find=db('keywords')->where('keywords_name','eq',$keywords_name)->find();
    	if(empty($keywords_find)){
    		$this->redirect('goods/goodslist');
    	}
    	//获取关键字对应的id
    	$keywords_id=$keywords_find['keywords_id'];
    	//判断内容为keywords_id与goods_id的这条goods_keywords数据是否存在
    	$goods_keywords_find=db('goods_keywords')->where('goods_id','eq',$goods_id)->
    	where('keywords_id','eq',$keywords_id)->find();
		if(empty($goods_keywords_find)){
		$this->redirect('goods/goodslist');
    	}

    	$goods_model=model('goods');
    	$goods=$goods_model->get($goods_id);
    	//删除关联的中间表数据
    	$goods->keywords()->detach($keywords_id);
    	$this->redirect('goods/goodslist');
    }

    //多图上传代码
    public function imgupload(){
    	//商品细节图上传,获取表单上传文件 例如上传了001.jpg
    $file = request()->file('goods_img');
    // 移动到框架应用根目录/public/uploads/ 目录下
    $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads'.DS . 'img');
    //dump(ROOT_PATH);(string(19) "D:\phpStudy\WWW\jd\")
    if($info){
        // 成功上传后 获取上传信息
        // 输出 jpg
        $address = DS.'public' . DS . 'uploads'.DS.'img'.DS.$info->getSaveName();
        $_SESSION['imgupload'][]=$address;
        //因为session的内容是数据，但是cookie不能存放数据，先得把session的内容序列化
        $session_str=serialize($_SESSION['imgupload']);
        cookie('imgupload',$session_str,3600);//把信息放入cookie，1小时有效
        return $address; 
     }else{
         // 上传失败获取错误信息
         echo $file->getError();
    }
    }

    //多图上传的删除图片代码
     public function imgcancle(){
    	if (request()->isAjax()) {
            $post = request()->post();
            $img_index = $post['index'];
            $img_address = $_SESSION['imgupload'][$img_index];
            if($img_address==1){
            	$_SESSION['imgupload'][$img_index] = '-1';
            }
            else{
            	$_SESSION['imgupload'][$img_index]='0';
            }
            $url_pre = DS.'public';
            $url = str_replace($url_pre,'.',$img_address);
            if (file_exists($url)) {
                unlink($url);
            }
        }
    }
}
?>