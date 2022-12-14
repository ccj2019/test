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
                // 通过深入 Document 内部对 body 进行检测，获取窗口大小
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
                        <a href="/"  <?php if($dh == 1): ?>class="active"<?php endif; ?> >首页</a>
                        <a   <?php if($dh == 2): ?>class="active"<?php endif; ?>  href="__APP__/invest">渔业养殖</a>
                        <a   <?php if($dh == 5): ?>class="active"<?php endif; ?>  href="__APP__/article/video">视频直播</a>
                        <a   <?php if($dh == 6): ?>class="active"<?php endif; ?>  href="__APP__/article/active">活动公告</a>
                        <a   <?php if($dh == 7): ?>class="active"<?php endif; ?>  href="__APP__/article/about">关于我们</a>

                    </div>
                    <div class="wechatAppBox">
                        <div class="wechatAppBoxLi">
                            微信
                            <div class="tfErweimaBox">
                                <img src="/Public/web/img/shouye/erwei-w.png" />
                                微信公众号
                            </div>
                        </div>
                        <div class="wechatAppBoxLi">
                            APP下载
                            <div class="tfErweimaBox">
                                <img src="/Public/web/img/shouye/erwei-app.png" />
                                APP下载
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
    margin-left: 0.13rem;">退出</a><?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <div style="height: 1.12rem;"></div>
<style type="text/css">
  	.swiper-container {
    width: 1060px;
	}  
  	.swiper-button-prev{
  		background: url(__PUBLIC__/web/img/guanyu/le.png) no-repeat;
  		width: 50px;
  		height: 50px;
  		outline: none;
  	}
  	.swiper-button-next{
  		background: url(__PUBLIC__/web/img/guanyu/le1.png) no-repeat;
  		width: 50px;
  		height: 50px;
  		outline: none;
  	}
  	.l_tab1>div:nth-child(2) p{
  		padding-top: 10px;
  	}
  	.l_tab1>div{
  		height: 250px;
  	}
</style>
<!--图片-->
<div class="l_banner">
	<img src="__PUBLIC__/web/img/guanyu/11.jpg" alt="" />
</div>
<!--公司简介-->
<div class="gsjj">
	<div class="box">
		<div class="tabme">
			<span class="on">公司简介</span>
			<span><a href="#div1">资质证件</a></span>
		<!-- 	<span><a href="#div2">法律保障条例</a></span> -->
			<span><a href="#div3">联系我们</a></span>
		</div>
	</div>
</div>
		
<!--背景-->
<div class="l_usbeij">
	<div class="box clearfix" >
		<div class="l_gstop clearfix">
			<span>公司简介</span>
			<span>company profile</span>
			<span></span>
		</div>
		<div class="l_tab1 clearfix">
	<!--	<style>
		.l_tab1 >.fl>img{
			width: 500px; height: 250px;  
			/*margin-left: 32px;margin-top: 65px;*/
		}
		</style>
		<?php $_result=get_ad(38);if(is_array($_result)): $i = 0; $__LIST__ = $_result;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$voa): $mod = ($i % 2 );++$i;?><img src="/<?php echo ($voa["img"]); ?>" style="  width: 500px; height: 250px;  margin-left: 32px;margin-top: 65px;"/><?php endforeach; endif; else: echo "" ;endif; ?> 
		-->
		<div class="fl " style="background:none; text-align:center;">
<img src="__PUBLIC__/web/img/guanyu/111.jpg"  style="width: 420px;margin-top: 30px;" /> <!-- style="    width: 80%;  height: 66%;  margin-left: 7%; margin-top: 10%;" -->
				</div> 
			<div class="fl">
				<?php echo ($about["type_content"]); ?>
			</div>
		</div>
		<div class="l_gstop clearfix"  id="div1">
			<span>资质证件  </span>
			<span>Credentials</span>
			<span></span>
		</div>
		<!--轮播-->
		<div class="l_lunbo">
			<div class="swiper-container tz-gallery">
			    <div class="swiper-wrapper ">
				
			    	<?php $_result=getArtList(521,20);if(is_array($_result)): $i = 0; $__LIST__ = $_result;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="swiper-slide" style="margin-right: 10px;">
				              <a class="lightbox" href="/<?php echo ($vo["art_img"]); ?>">
				        	<div class="l_huihui" style="width: 260px;">
				        	  
		                      <img src="/<?php echo ($vo["art_img"]); ?>" alt="Park">
		                
				        	 
				        	</div>
				        	</a>
				        </div><?php endforeach; endif; else: echo "" ;endif; ?>
			    </div>
			</div>
		   	<!-- 如果需要导航按钮 -->
		    <div class="swiper-button-prev"></div>
		    <div class="swiper-button-next"></div>
		</div>
	<!-- 	<div class="l_gstop clearfix" id="div2"style="margin-top:60px">
			<span>法律保障条例</span>
			<span>Regulations</span>
			<span></span>
		</div> -->
		
		<!--法律保障条例-->
	<!-- 	<div class="l_bztl clearfix">
			<?php $_result=getArtList(517,3);if(is_array($_result)): $i = 0; $__LIST__ = $_result;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div>
			<p class="p1"><?php echo ($vo["title"]); ?>		<a href="/article/index/cid/<?php echo ($vo["type_id"]); ?>/id/<?php echo ($vo["id"]); ?>.html"></a> </p>
				<p class="p2"><?php echo (restrlen(clearhtml(trim($vo["art_content"])),60)); ?>	<a href="/article/index/cid/<?php echo ($vo["type_id"]); ?>/id/<?php echo ($vo["id"]); ?>.html"></a> </p>
			<p class="p3"></p>	<a href="/article/index/cid/<?php echo ($vo["type_id"]); ?>/id/<?php echo ($vo["id"]); ?>.html"></a> 
			
			</div><?php endforeach; endif; else: echo "" ;endif; ?>
		</div> -->
		<div id="" style="height: 160px;"></div>
		<div class="l_gstop clearfix" id="div3">
			<span>联系我们</span>
			<span>Contact</span>
			<span></span>
		</div>
		<div class="mapbox clearfix">
		<style>
		#allmap #BMapLib_SearchInfoWindow0 img{
		    width: 23px !important;
			height: 39px !important;
		}
		</style>
			<div class="map" id="allmap">
				 
			</div>
			<script type="text/javascript" src="https://api.map.baidu.com/api?v=2.0&ak=EBa850e0da6958d3e111caebc3dc9cba"></script>
	<script type="text/javascript" src="https://api.map.baidu.com/library/SearchInfoWindow/1.5/src/SearchInfoWindow_min.js"></script>
	<link rel="stylesheet" href="https://api.map.baidu.com/library/SearchInfoWindow/1.5/src/SearchInfoWindow_min.css" />
 

	 
<script type="text/javascript">
	// 百度地图API功能
    var map = new BMap.Map('allmap');
    var poi = new BMap.Point(119.555763,35.392227);
    map.centerAndZoom(poi, 16);
    map.enableScrollWheelZoom();

    var content = '<div style="margin:0;line-height:20px;padding:2px;">' +
                    '<img src="../img/baidu.jpg" alt="" style="float:right;zoom:1;overflow:hidden;width:100px;height:100px;margin-left:3px;"/>' +
                    '地址：山东省日照市东港区石臼街道海滨二路156号<br/>电话：400-110-0216<br/>简介：日照铭万网络科技有限公司。' +
                  '</div>';

    //创建检索信息窗口对象
    var searchInfoWindow = null;
	searchInfoWindow = new BMapLib.SearchInfoWindow(map, content, {
			title  : "日照铭万网络科技有限公司",      //标题
			width  : 290,             //宽度
			height : 105,              //高度
			panel  : "panel",         //检索结果面板
			enableAutoPan : true,     //自动平移
			searchTypes   :[
				BMAPLIB_TAB_SEARCH,   //周边检索
				BMAPLIB_TAB_TO_HERE,  //到这里去
				BMAPLIB_TAB_FROM_HERE //从这里出发
			]
		});
    var marker = new BMap.Marker(poi); //创建marker对象
    marker.enableDragging(); //marker可拖拽
    marker.addEventListener("click", function(e){
	    searchInfoWindow.open(marker);
    })
    map.addOverlay(marker); //在地图中添加marker
	//样式1
	var searchInfoWindow1 = new BMapLib.SearchInfoWindow(map, "信息框1内容", {
		title: "信息框1", //标题
		panel : "panel", //检索结果面板
		enableAutoPan : true, //自动平移
		searchTypes :[
			BMAPLIB_TAB_FROM_HERE, //从这里出发
			BMAPLIB_TAB_SEARCH   //周边检索
		]
	});
	function openInfoWindow1() {
		searchInfoWindow1.open(new BMap.Point(116.319852,40.057031));
	}
	 
	function openInfoWindow3() {
		searchInfoWindow3.open(new BMap.Point(116.328852,40.057031)); 
	}
</script>
			
			<div class="map1">	
			    <img src="__PUBLIC__/web/img/guanyu/logo3.png" alt="" />
			    <p class="p1"><?php echo ($glo["web_name"]); ?></p>
			    <p>服务电话：<?php echo ($glo["botttom_phone"]); ?></p>
			    <p style="
      width: 360px;
    padding-left: 82px;
    line-height: 25px;
">公司地址：<?php echo ($glo["botttom_addr"]); ?></p>
			</div>
		</div>	
	</div>
	<!--联系我们-->
</div>
<div class="clear"></div>

<div class="footer">
<div class="foot_con">
  <img  class="foot_l" src="/Public/new/images/xyj_21.jpg" />
    <div class="foot_r">
    <div class="foot_zz"></div>
      <div class="foot_r_l" style="width:200px;">
          <li><a href="<?php echo U('/article/cateshow',array('cid'=>523));?>">服务条款</a></li>
          <li><a href="<?php echo U('/article/cateshow',array('cid'=>533));?>">隐私策略</a></li>
        <!--     <li><a href="<?php echo U('/article/cateshow',array('cid'=>523));?>">服务介绍</a></li> -->
          <!--  
            <li><a href="<?php echo U('/article/cateshow',array('cid'=>523));?>">撰写指南</a></li> -->
        </div>
        <div class="foot_r_l" style="width:400px; margin-left:40px;">
            <li><a href="/invest/index.html">渔业养殖</a></li>
            <!--<li><a href="/zhongzhi/index.html">联合种植</a></li>-->
            <li><a href="/">海产商城</a></li>
            <li><a href="/article/news_list.html">最新资讯</a></li>
            <li><a href="<?php echo U('/article/video');?>">视频直播</a></li>
            <li><a href="<?php echo U('/article/active');?>">活动公告</a></li>
            <li><a href="<?php echo U('/article/about');?>">关于我们</a></li>
            <li><a href="<?php echo U('/member/index/index');?>">会员中心</a></li>
        </div>
        <div class="foot_r_r">
        
        <div class="ftm_ewm">
                  <img src="/Public/web/img/shouye/erwei-w.png" />
        </div>     
          微信公众号
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
           公司地址：山东省日照市东港区石臼街道海滨二路156号  <a href="https://www.beian.gov.cn/portal/registerSystemInfo?recordcode=37110302000118" style="color:#fff;"  target="_blank">鲁公网安备 37110302000118号 </a>      <a href="https://beian.miit.gov.cn" style="color:#fff;"  target="_blank">鲁ICP备17003630号-1</a><br>


           <span style="line-height:25px; display:black;">寻渔记-日照铭万网络科技有限公司版权所有 </span>
      市场有风险 投资需谨慎<br>


       </div>
    </div>
</div>
</div>
</div>

<div class="scroll" style="display: none; "> 
  <a class="top" href="javascript:void(0)"><img src="/Public/new/images/top.jpg"><span>返回顶部</span></a>
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
		style="height: 0.9rem;display: flex;align-items: center;justify-content: center;position: relative;background-image: url(https://www.bsan.org.cn/images/bg_small.png);background-size: cover;overflow: hidden;background-position: center center;text-align: center;" title="点击查看电子证书">
			<div style="color: #fff;font-size: 0.25rem;text-align:center;display:inline-block;">
				<img src="https://www.bsan.org.cn/images/logo-xf.jpg" style="height: 0.6rem;margin-right: 0.10rem;border-radius: 0.04rem;float:left;">
				<span style="font-size: 0.31rem;letter-spacing: 1px;font-weight: 600;display:inline-block;line-height: 0.60rem;float: left;color: white !important;">
					寻渔记入选互联网海洋产业行业典范企业</span>
			</div>
		</a>

<style type="text/css">
#newBridge{
	display: none;
}
</style>
<script>
baguetteBox.run('.tz-gallery');
	var mySwiper = new Swiper ('.swiper-container', {
   
    autoplay:{
    delay: 2500,
    disableOnInteraction: false,
   },
	slidesPerView: 4,//一行显示3个   
    // 如果需要前进后退按钮
    navigation: {
      nextEl: '.swiper-button-next',
      prevEl: '.swiper-button-prev',
    },
  })      
  
</script>
   <script type="text/javascript" charset="utf-8">
    window.onload = function() {
            var oImg = document.getElementsByTagName("img")[0];
            function fnWheel(obj, fncc) {
                obj.onmousewheel = fn;
                if (obj.addEventListener) {
                    obj.addEventListener('DOMMouseScroll', fn, false);
                }

                function fn(ev) {
                    var oEvent = ev || window.event;
                    var down = true;

                    if (oEvent.detail) {
                        down = oEvent.detail > 0
                    } else {
                        down = oEvent.wheelDelta < 0
                    }

                    if (fncc) {
                        fncc.call(this, down, oEvent);
                    }

                    if (oEvent.preventDefault) {
                        oEvent.preventDefault();
                    }

                    return false;
                }


            };
            fnWheel(oImg, function(down, oEvent) {

                var oldWidth = this.offsetWidth;
                var oldHeight = this.offsetHeight;
                var oldLeft = this.offsetLeft;
                var oldTop = this.offsetTop;

                var scaleX = (oEvent.clientX - oldLeft) / oldWidth; //比例
                var scaleY = (oEvent.clientY - oldTop) / oldHeight;

                if (down) {
                    this.style.width = this.offsetWidth * 0.9 + "px";
                    this.style.height = this.offsetHeight * 0.9 + "px";
                } else {
                    this.style.width = this.offsetWidth * 1.1 + "px";
                    this.style.height = this.offsetHeight * 1.1 + "px";
                }

                var newWidth = this.offsetWidth;
                var newHeight = this.offsetHeight;

                this.style.left = oldLeft - scaleX * (newWidth - oldWidth) + "px";
                this.style.top = oldTop - scaleY * (newHeight - oldHeight) + "px";
            });
        }
     
    </script> 
</html>