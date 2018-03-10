<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:67:"D:\phpStudy\WWW\jd\public/../application/admin\view\test\index.html";i:1520597465;}*/ ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
</head>
<body>
	<?php if(is_array($getCate) || $getCate instanceof \think\Collection || $getCate instanceof \think\Paginator): $i = 0; $__LIST__ = $getCate;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
	<!-- <h1><?php echo $vo['cate_id']; ?></h1>
	<h1><?php echo $vo['cate_name']; ?></h1>
	<h1><?php echo $vo['cate_sort']; ?></h1>
	<h1><?php echo $vo['cate_pid']; ?></h1>
	<br> -->
	<?php endforeach; endif; else: echo "" ;endif; ?>

	


</body>
</html>>