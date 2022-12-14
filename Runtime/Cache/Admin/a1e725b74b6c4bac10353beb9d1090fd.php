<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>提示信息</title>
<style type="text/css">
	*{margin:0; padding:0;}
	body{font-size:12px; color:#000; background:#fff; font-style:normal !important;}
	img{border:none;}
	a{text-decoration:none; color:#000;}
	a:hover{text-decoration:none; color:#000;}
	li{list-style:none;}
	.sucess{width:604px; height:221px; background:url(/Style/tip/images/bg1.png) no-repeat; margin:0 auto; position:absolute; left:50%; top:100px; margin-left:-302px;}
	.spec1{ text-align:right; padding-right:19px; padding-top:17px; position:relative;}
	.spec1 a{ position:absolute; top:15px; right:20px; height:22px; width:22px; display:block;}
	.spec2{font-size:20px; font-family:微软雅黑; color:#4d824d; text-align:center; padding-top:20px; padding-bottom:20px; font-weight:bold;}
	.spec3{font-size:14px; color:#4d4a4a; text-align:center; font-family:微软雅黑;}
	.spec3 a{color:#a6292c;}
	.spec3 span{color:#a6292c;}
	.shibai{width:604px; height:221px; background:url(/Style/tip/images/bg2.png) no-repeat; margin:0 auto;position:absolute; left:50%; top:50%; margin-top:-110px; margin-left:-302px;}
	.spec4{font-size:24px; font-family:微软雅黑; color:#a2292b; text-align:center; padding-top:20px; padding-bottom:20px;font-weight:bold;}
</style>
<?php  $waitSecond=3; ?>
<script>
function Jump(){
    window.location.href = '<?php echo ($jumpUrl); ?>';
}
document.onload = setTimeout("Jump()" , <?php echo ($waitSecond); ?>* 1000);
</script>
<base target="_self" />
</head>
<body>
<?php if(($status) == "1"): ?><div class="sucess png">
      <p class="spec1"><a href="<?php echo ($jumpUrl); ?>"></a><img src="/Style/tip/images/gb.png" class="png" /></p>
      <p class="spec2"><?php echo ($message); ?></p>
      <p class="spec3">系统将在<span><?php echo ($waitSecond); ?></span>秒后自动跳转，如果不想等待，直接点击<a href="<?php echo ($jumpUrl); ?>">这里</a>跳转</p>
  </div><?php endif; ?>
<?php if(($status) == "0"): ?><div class="shibai png">
      <p class="spec1"><a href="<?php echo ($jumpUrl); ?>"></a><img src="/Style/tip/images/gb2.png" class="png" /></p>
      <p class="spec4"><?php echo ($error); ?></p>
      <p class="spec3">系统将在<span><?php echo ($waitSecond); ?></span>秒后自动跳转，如果不想等待，直接点击<a href="<?php echo ($jumpUrl); ?>">这里</a>跳转</p>
  </div><?php endif; ?>
</body>
</html>