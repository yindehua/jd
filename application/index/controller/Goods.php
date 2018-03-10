<?php
namespace app\index\controller;

//前台商品控制器
class Goods extends  \think\Controller{

	//带关键字的商品了列表页面显示
	public function goodslist($goods_pid='',$goods_order='id'){
		if($goods_pid=='')
		{
			$this->redirect('index/index');
		}

		$goods_exist=db('goods')->where('goods_pid','eq',$goods_pid)
		->where('goods_status','eq','1')->select();
		if(empty($goods_exist)){
			$this->redirect('index/index');
		}

		//判断传过来的参数是按什么要求排序
		if($goods_order=='goods_sales')
		{
			$goods_order='goods_sales desc';
		}
		else if($goods_order=='goods_price_asc')
		{
			$goods_order='goods_price asc';
		}
		else if($goods_order=='goods_price_desc')
		{
			$goods_order='goods_price desc';
		}
		else
		{
			$goods_order='goods_id';
		}

		$this->assign('goods_pid',$goods_pid);
		$goods_model= new \app\admin\model\Goods;
		$cate_model = new \app\admin\model\Cate;
		//$query->where('goods_pid','eq',$goods_pid);
		$goods_all=$goods_model->all(function($query) use ($goods_pid,$goods_order)
		{
			$query->where('goods_pid','eq',$goods_pid)->where('goods_status','eq','1')->order($goods_order);//闭包方式实现分页
		});
		
		//获取无限分类
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
		$page_class=new Page($goods_totle,8);//每页多少条数据
		$show=$page_class->fpage();//模板显示的内容
		$limit=$page_class->setlimit();//获取limit信息
		$limit=explode(',',$limit);
		//dump($limit);
		$list=array_slice($goods_info,$limit[0],$limit[1]);
		$this->assign('show', $show);
		$this->assign('goods_info', $list);
		return $this->fetch();
	
	}
	public function introduction($goods_id=''){
		//上面详细信息页面
		if($goods_id=='')
			{$this->redirect('index/index');}
		$goods_find=db('goods')->find($goods_id);//以商品id去数据库查找数据
		if(empty($goods_find))
		{$this->redirect('index/index');}
		$goods_status=$goods_find['goods_status'];//获取商品状态，看看是在售还是下架
		if($goods_status==0){
			$this->redirect('index/index');
		}

		//通过商品的id把带有商品关键字的数据找出来
		$goods_model= new \app\admin\model\Goods;
		$goods_get = $goods_model->get($goods_id);
		$goods_get_toArray=$goods_get->toArray();//把得到的对象转换为数组,其内容并不带有关键字
		$goods_keywords=$goods_get->keywords;//返回Goods为参数对应keywords的id
		$goods_keywords_toArray=$goods_keywords->toArray();//把得到的对象转换为数组
		$goods_get_toArray['keywords']=$goods_keywords_toArray;//把转换过来的数组传给$value['keywords']
		$this->assign('goods_introduction',$goods_get_toArray);


		//商品定位的实现
		$goods_pid=$goods_find['goods_pid'];
		$cate_select=db('cate')->select();
		$cate_model= new \app\admin\model\Cate;
		$cate_in=$cate_model->getFather($cate_select,$goods_pid);
		$this->assign('cate_in',$cate_in);
		//dump($cate_in);die;

		return $this->fetch();

	}





}