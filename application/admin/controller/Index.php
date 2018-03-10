<?php

namespace app\admin\controller;

use think\Controller;

class Index extends Controller
{
    //后台主页
    public function index()
    {
        return view('index');
    }

    public function index1(){
        return view();
    }

    public function index2(){
        if (request()->isAjax()) {
            $post = request()->post();
            $post_val = $post['val'];
            $keywords_like = db('keywords')->where('keywords_name','like','%'.$post_val.'%')->limit(3)->select();
            return $keywords_like;
        }
    }

    public function duotushangchuan(){
        unset($_SESSION['fileupload']);
        return view();
    }

    public function duotuhanddle(){
        $file = request()->file('goods_img');
        $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
        if($info){
            $rand = rand(1,100);
            $address = DS.'public' . DS . 'uploads'.DS.$info->getSaveName();
            $_SESSION['goods_img'][] = $address;
            return $address;
        }else{
            echo $file->getError();
        }
    }


     public function a(){
        if (request()->isAjax()) {
            $post = request()->post();
            $img_index = $post['index'];
            $img_address = $_SESSION['fileupload'][$img_index];
            $url_pre = DS.'jd'.DS.'public';
            $url = str_replace($url_pre,'.',$img_address);
            if (file_exists($url)) {
                unlink($url);
            }
        }
    }

}
