<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" /> 
	<title><?php echo ($glo["web_name"]); ?></title>
	<meta name="keywords" content="<?php echo ($glo["web_keywords"]); ?>" />
	<meta name="description" content="<?php echo ($glo["web_descript"]); ?>" />
    	<link rel="shortcut icon" href="__PUBLIC__/images/favicon.ico" />
    	<link rel="bookmark"href="__PUBLIC__/images/favicon.ico" />
	<link rel="stylesheet" href="/Public/web/css/jquery.fullPage.css">
	<link rel="stylesheet" type="text/css" href="/Public/web/css/swiper-4.3.3.min.css" />
		<link rel="stylesheet" type="text/css" href="/Public/web/css/baguetteBox.min.css" />
	<link rel="stylesheet" type="text/css" href="/Public/web/css/gallery-grid.css" />
	<link rel="stylesheet" type="text/css" href="/Public/web/css/all.css" />
	<link rel="stylesheet" type="text/css" href="/Public/web/css/ldy.css" />
	<link rel="stylesheet" type="text/css" href="/Public/web/css/ltt.css" />
	
	<script type="text/javascript" src="/Public/web/js/jquery-1.8.3.min.js"></script>
	<script type="text/javascript" src="/Public/web/js/swiper-4.3.3.min.js"></script>

 	<script type="text/javascript" src="/Public/web/js/baguetteBox.min.js"></script> 
	<script type="text/javascript" src="/Public/web/js/jquery-ui-1.10.3.min.js"></script>
	<script type="text/javascript" src="/Public/web/js/jquery.fullPage.min.js"></script>
	<script type="text/javascript" src="/Public/web/js/ldy.js"></script>
	<script type="text/javascript" src="/Style/file/layer/layer.min.js"></script>
    <script type="text/javascript" src="/Public/web/js/layer.js"></script>

<link rel="stylesheet" type="text/css" href="/Public/new/css/style.css"/>
<script type="text/javascript" src="/Public/new/js/index.js"></script>

 	<!--<meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">-->
  <link rel="stylesheet" type="text/css" href="__PUBLIC__/shop/css/tfindex.css" />
        <script type="text/javascript">
            function jisuanFontsize(){
                var winWidth;
                if (window.innerWidth)
                winWidth = window.innerWidth;
                else if ((document.body) && (document.body.clientWidth))
                winWidth = document.body.clientWidth;
                // ???????????? Document ????????? body ?????????????????????????????????
                if (document.documentElement && document.documentElement.clientHeight && document.documentElement.clientWidth)
                {
                winWidth = document.documentElement.clientWidth;
                }
                if(winWidth<1300){
                    winWidth=1300
                }
                if(winWidth>1920){
                    winWidth=1920
                }
                document.getElementsByTagName('html')[0].style="font-size: calc("+winWidth+"px / 1920 * 100);"
            }
            jisuanFontsize();
            window.onresize = function(){
                jisuanFontsize()
            }
        </script>

        
</head>


<body>
<body>
       <div class="tfggHeaderBox" style="box-shadow: 0px 0px 2px rgba(0, 0, 0, 0.25);">
            <div class="tfggHeaderBoxMain">
                <img src="__PUBLIC__/shop/img/logo.png" class="tfggHeaderBoxMainLogo">
                <div class="tfggHeaderBoxMainRight">
                    <div class="tfggHeaderBoxMainRightMenuBox">
                        <a href="/"  <?php if($dh == 1): ?>class="active"<?php endif; ?> >??????</a>
                        <a   <?php if($dh == 2): ?>class="active"<?php endif; ?>  href="__APP__/invest">????????????</a>
                        <a   <?php if($dh == 5): ?>class="active"<?php endif; ?>  href="__APP__/article/video">????????????</a>
                        <a   <?php if($dh == 6): ?>class="active"<?php endif; ?>  href="__APP__/article/active">????????????</a>
                        <a   <?php if($dh == 7): ?>class="active"<?php endif; ?>  href="__APP__/article/about">????????????</a>

                    </div>
                    <div class="wechatAppBox">
                        <div class="wechatAppBoxLi">
                            ??????
                            <div class="tfErweimaBox">
                                <img src="/Public/web/img/shouye/erwei-w.png" />
                                ???????????????
                            </div>
                        </div>
                        <div class="wechatAppBoxLi">
                            APP??????
                            <div class="tfErweimaBox">
                                <img src="/Public/web/img/shouye/erwei-app.png" />
                                APP??????
                            </div>
                        </div>
                    </div>
                    <div class="tfggHeaderBoxMainRightImgBox" style="margin-left: 0.2rem;">
                        <a href="__APP__/member/index"><img src="__PUBLIC__/shop/img/tfUser.png"></a>
                        <a href="__APP__/shop/car_list"><img src="__PUBLIC__/shop/img/tfCart.png" class="tfgouwuche"></a>

                        
                 <?php if($UID > '0'): ?><a href="<?php echo U('/member/common/actlogout');?>"  style="    font-size: 0.2rem;
    display: block;
    float: left;
    margin-top: 0.44rem;
    color: #bf5757;
    margin-left: 0.13rem;">??????</a><?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <div style="height: 1.12rem;"></div>
<script src="__PUBLIC__/date/jquery-2.2.3.js" type="text/javascript"></script>
<script src="__PUBLIC__/date/WdatePicker.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="__PUBLIC__/date/skin/WdatePicker.css" />
<style>
.list{
    width:1200px; 
    margin:20px auto;
    margin-top: 60px;
    min-height: 390px;
}

.input-text{
    border: solid 1px #666666; 
}
</style>


<div class="list"> 
    <form action="__URL__/huikuan"  method="post" class="form form-horizontal" id="form-add" name="form-add" enctype="multipart/form-data">
     ???????????????<input type="text" onfocus="" name="time" class="input-text Wdate" id="time" onClick="WdatePicker({dateFmt:'yyyy-MM-dd'})" 
    value='<?php echo (date('Y-m-d',$time)); ?>'> 
  <button class="btn btn-secondary radius" type="submit"> ??????</button>
</from>
    <br><br>
<?php if(is_array($tzlist)): $i = 0; $__LIST__ = $tzlist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><a href="/Member/borrowin/fenhong/id/<?php echo ($vo["id"]); ?>/qi/<?php echo ($vo["qi"]); ?>.html" target="_blank"><?php echo ($i); ?>. <?php echo ($vo["borrow_name"]); ?>   ???<?php echo ($vo["qi"]); ?>???</a><br><?php endforeach; endif; else: echo "" ;endif; ?> 
</div>
        <!--??????-->
<div class="clear"></div>

<div class="footer">
<div class="foot_con">
  <img  class="foot_l" src="/Public/new/images/xyj_21.jpg" />
    <div class="foot_r">
    <div class="foot_zz"></div>
      <div class="foot_r_l" style="width:200px;">
          <li><a href="<?php echo U('/article/cateshow',array('cid'=>523));?>">????????????</a></li>
          <li><a href="<?php echo U('/article/cateshow',array('cid'=>533));?>">????????????</a></li>
        <!--     <li><a href="<?php echo U('/article/cateshow',array('cid'=>523));?>">????????????</a></li> -->
          <!--  
            <li><a href="<?php echo U('/article/cateshow',array('cid'=>523));?>">????????????</a></li> -->
        </div>
        <div class="foot_r_l" style="width:400px; margin-left:40px;">
            <li><a href="/invest/index.html">????????????</a></li>
            <!--<li><a href="/zhongzhi/index.html">????????????</a></li>-->
            <li><a href="/">????????????</a></li>
            <li><a href="/article/news_list.html">????????????</a></li>
            <li><a href="<?php echo U('/article/video');?>">????????????</a></li>
            <li><a href="<?php echo U('/article/active');?>">????????????</a></li>
            <li><a href="<?php echo U('/article/about');?>">????????????</a></li>
            <li><a href="<?php echo U('/member/index/index');?>">????????????</a></li>
        </div>
        <div class="foot_r_r">
        
        <div class="ftm_ewm">
                  <img src="/Public/web/img/shouye/erwei-w.png" />
        </div>     
          ???????????????
        </div>
       <div class="clear"></div>
       <div class="foot_ry">
       <!--<a target="_blank" ><img src="/Public/new/images/xyj_28.jpg" /></a>-->

       <a href="https://www.beian.gov.cn/portal/registerSystemInfo?recordcode=37110302000118" target="_blank" ><img src="/Public/new/images/gg_07.png" /></a>

           <a target="_blank">
               <img src="/Public/new/images/gg_09.png" /></a>


       
       </div>
       <div class="clear"></div>
       <div class="foot_bq">
           ??????????????????????????????????????????????????????????????????156???  <a href="https://www.beian.gov.cn/portal/registerSystemInfo?recordcode=37110302000118" style="color:#fff;"  target="_blank">??????????????? 37110302000118??? </a>      <a href="https://beian.miit.gov.cn" style="color:#fff;"  target="_blank">???ICP???17003630???-1</a><br>


           <span style="line-height:25px; display:black;">?????????-???????????????????????????????????????????????? </span>
      ??????????????? ???????????????<br>


       </div>
    </div>
</div>
</div>
</div>

<div class="scroll" style="display: none; "> 
  <a class="top" href="javascript:void(0)"><img src="/Public/new/images/top.jpg"><span>????????????</span></a>
</div>

</body>
</html>

<script>

window.onload = function () {

var _hmt = _hmt || [];
(function() {
  var hm = document.createElement("script");
  hm.src = "https://hm.baidu.com/hm.js?55018a7c810a0920c9997b042f7da876";
  var s = document.getElementsByTagName("script")[0];
  s.parentNode.insertBefore(hm, s);
})();


// var _hmt = _hmt || [];
//     (function() {
//         var hm = document.createElement("script");
//         hm.src = "https://hm.baidu.com/hm.js?9bf93af51e6ba9068b243c8dd28870b3";
//         var s = document.getElementsByTagName("script")[0];
//         s.parentNode.insertBefore(hm, s);
// })();


}


</script>
<script src="https://kf.bohang.vip/assets/layer/cgwl_3.js"> </script>

<a target="_blank" href="https://www.bsan.org.cn/brand/SF2022S605J9J57Q7.html" 
		style="height: 0.9rem;display: flex;align-items: center;justify-content: center;position: relative;background-image: url(https://www.bsan.org.cn/images/bg_small.png);background-size: cover;overflow: hidden;background-position: center center;text-align: center;" title="????????????????????????">
			<div style="color: #fff;font-size: 0.25rem;text-align:center;display:inline-block;">
				<img src="https://www.bsan.org.cn/images/logo-xf.jpg" style="height: 0.6rem;margin-right: 0.10rem;border-radius: 0.04rem;float:left;">
				<span style="font-size: 0.31rem;letter-spacing: 1px;font-weight: 600;display:inline-block;line-height: 0.60rem;float: left;color: white !important;">
					??????????????????????????????????????????????????????</span>
			</div>
		</a>