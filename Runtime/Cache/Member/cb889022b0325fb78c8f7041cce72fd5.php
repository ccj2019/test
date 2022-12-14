<?php if (!defined('THINK_PATH')) exit();?><style>
    input::-webkit-input-placeholder{
    color: #737373 !important;
}
input:-moz-placeholder{
    color: #737373 !important;
}
input::-moz-placeholder{
    color: #737373 !important;
}
input:-ms-input-placeholder{
    color: #737373 !important;
}
input : -webkit-autofill {
background-color : #FAFFBD ;
background-image : none ;
color : #000 ;
}
</style><!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" /> 
	<title><?php if(!empty($web_title)): echo ($web_title); ?>-<?php endif; echo ($glo["web_name"]); ?></title>
	<meta name="keywords" content="<?php echo ($glo["web_keywords"]); ?>" />
	<meta name="description" content="<?php echo ($glo["web_descript"]); ?>" />
    	<link rel="shortcut icon" href="__PUBLIC__/images/favicon.ico" />
    	<link rel="bookmark"href="__PUBLIC__/images/favicon.ico" />	

	<link rel="stylesheet" href="/Public/web/css/jquery.fullPage.css">
	<link rel="stylesheet" type="text/css" href="/Public/web/css/swiper-4.3.3.min.css" />
	<link rel="stylesheet" type="text/css" href="/Public/web/css/all.css" />
	<link rel="stylesheet" type="text/css" href="/Public/web/css/ldy.css" />
	<link rel="stylesheet" type="text/css" href="/Public/web/css/ltt.css" />
	<script src="/Public/web/js/jquery-1.8.3.min.js"></script>
	<script type="text/javascript" src="/Public/web/js/swiper-4.3.3.min.js"></script>
	<script src="/Public/web/js/jquery-ui-1.10.3.min.js"></script>
	<script src="/Public/web/js/jquery.fullPage.min.js"></script>
	<script type="text/javascript" src="/Public/web/js/ldy.js"></script>
	<script src="/Style/file/layer/layer.min.js"></script>
<script type="text/javascript" src="/Public/web/js/layer.js"></script>
        <link rel="stylesheet" type="text/css" href="__PUBLIC__/shop/css/tfindex.css" />
 <!--<meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">-->

<link rel="stylesheet" type="text/css" href="/Public/new/css/style.css"/>
<script type="text/javascript" src="/Public/new/js/index.js"></script>
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
  <div class="tfggHeaderBox">
            <div class="tfggHeaderBoxMain">
                <img src="__PUBLIC__/shop/img/logo.png" class="tfggHeaderBoxMainLogo">
                <div class="tfggHeaderBoxMainRight">
                    <div class="tfggHeaderBoxMainRightMenuBox">
                        <a href="/"  <?php if($dh == 1): ?>class="active"<?php endif; ?> >首页</a>
                        <a href="__APP__/invest">渔业养殖</a>
                        <!--<a href="__APP__/zhongzhi">联合种植</a>-->
                        <a href="__APP__/article/video">视频直播</a>
                        <a href="__APP__/article/active">活动公告</a>
                        <a href="__APP__/article/about">关于我们</a>

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
                    <div class="tfggHeaderBoxMainRightImgBox">
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




<script type="text/javascript" src="/Public/wap/js/qrcode.min.js"></script>
<!--内容-->
<div class="l-omg" style="background: url(__PUBLIC__/web/img/yindao/bj.jpg) no-repeat center center;">
<div style="width: 1130;margin: 0 auto;">
	<div class="l-mlogin" style="float:right; margin-top:80px;" >
		<div class="l-mlg">
			<div class="clearfix dl-box">
				<span class="mylgon">登录</span>  
				<div class="mylgon1"><span>没有账号？</span>
					<span><a href="/member/common/register">立即注册</a></span>
				</div>
			</div>
			 <form action="" method="POST">
					
			   <div class="inp-box">
				  <span><img src="/Public/web/img/yindao/shouji-5.png"/></span>
				  <input type="text" name="sUserName" id="sUserName" autocomplete="off" placeholder="请输入手机号/用户名"/>
			   </div>	
				 <div class="inp-box">
				  <span><img src="/Public/web/img/yindao/mima-5.png"/>
				  </span><input type="password" name="sPassword" id="sPassword" autocomplete="off"   placeholder="请输入密码"/>

				  <input type="hidden" name="url" id="url" value="<?php echo ($url); ?>"/>
			   </div>	
			   <p class="wjmima"><a href="/member/common/getpass/">忘记密码？</a>	</p>
			   <!--<p class="qitalogn">&lt;!&ndash;-&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;其他登录方式&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&ndash;&gt;</p>-->
			   <!--<p class="qitadengl">	-->
			   	<!--<span><a href="javascript:;" onclick="weixinlog()">-->
			   	    <!--<img style="display: inline;" src="/Public/web/img/yindao/weixin-2.png"/></a></span>-->
			   	<!--&lt;!&ndash; <span><a href="##"><img src="/Public/web/img/yindao/qq-2.png"/></a></span> &ndash;&gt;-->
			   	<!--</p>-->
			   <input type="button" autocomplete="off"  style="height:40px;" name="" class="lijsubm" onclick="loginsub()" value="立即登录">
			</form>
			
		</div>
	</div>
	</div>
</div>
<div class="dome-1">
<div class="dome-2" style="background:none;    margin-top: 15%;">
<div id="qrcode" style="width: 125px; margin: 0px auto;"></div>
</div>
</div>
<script type="text/javascript">
$('input').bind('keyup', function(event) {
　　if (event.keyCode == "13") {
　　　　//回车执行查询
　　　loginsub()
　　}
});
function loginsub() {
	var sUserName = $("#sUserName").val();
	var sPassword = $("#sPassword").val();
	if(sUserName == ''){
		layer.msg("请填写手机号")
        return false;
	}
	if(sPassword == ''){
		layer.msg("请填写密码")
        return false;
	}

	$.ajax({
		url: "__APP__/member/common/actlogin",
		timeout: 5000,
		cache: false,
		type: "post",
		dataType: "json",
		data: {"sUserName":sUserName,"sPassword":sPassword},
		success: function (d, s, r) {
			if(d.status==0){
				   layer.msg(d.message);
			}else{
				layer.msg('登录成功',{shift: -1},function(){
					if($("#url").val()!=''){
						window.location.href=$("#url").val();
					}else{
						window.location.href="__APP__/member";
					}
	                
	            });
			}
		}
	});
}
function weixinlog(){
	$.ajax({
		url: "__APP__/member/common/weixinlog",
		timeout: 5000,
		cache: false,
		type: "get",
		dataType: "json",
		success: function (d, s, r) {
			$(".dome-1").show();
			var qrcode = new QRCode('qrcode', {
	            text:d.wxurl,
	            width: 256,
	            height: 256,
	            colorDark: '#000000',
	            colorLight: '#ffffff',
	            correctLevel: QRCode.CorrectLevel.H
	        });
	        setInterval("getislogin('"+d.session_id+"')",1000*3); //单位毫秒
		}
	});
}
$(function(){
	$(".dome-1").click(function(){
		$(".dome-1").hide();
	})

	$(".dome-2 img").click(function(){
		$(".dome-1").hide();
	})
})
function getislogin(session_id){

	var session_id = session_id;
	$.ajax({
		url: "__APP__/home/index/islogin",
		timeout: 5000,
		cache: false,
		type: "post",
		dataType: "json",
		data:{'session_id':session_id},
		success: function (d, s, r) {
			console.log(d);
			if(d.status == 1){
				layer.alert('登录成功',4,function(){
					window.location.href='/member';
				});
			}
		}
	});
}
</script>
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