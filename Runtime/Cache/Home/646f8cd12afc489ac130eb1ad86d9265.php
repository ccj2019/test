<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" /> 
	<title><?php echo ($glo["web_name"]); ?></title>
	<meta name="keywords" content="<?php echo ($glo["web_keywords"]); ?>" />
	<meta name="description" content="<?php echo ($glo["web_descript"]); ?>" />
    	<link rel="shortcut icon" href="__PUBLIC__/images/favicon.ico" />
    	<link rel="bookmark"href="__PUBLIC__/images/favicon.ico" />

<link rel="stylesheet" type="text/css" href="/Public/new/css/style.css"/>
<script type="text/javascript" src="/Public/new/js/jquery-1.7.2.min.js"></script>
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




<div class="zz_con">
    <div class="zcon_l">
        <img src="<?php echo ($vo["art_img"]); ?>" class="zt" />
        <ul>
            <li><img src="<?php echo ($vo["art_img"]); ?>" /></li>
        </ul>
    </div>
    
    <div class="zcon_r">
        <div class="zz_name" style="border:none;">
            <h2><?php echo ($vo["title"]); ?><!-- <span><i>积分</i>每2000积分可抵1元</span> --></h2>
            <p><?php echo ($vo["art_miaoshu"]); ?></p>
        </div>
 <div class="sprice">
    价格<span class="s_pc">￥<?php echo ($vo["art_jiage"]); ?></span>
    
    <span class="s_jy"><?php echo ($vo["jiaoyi"]); ?><br />交易成功</span>
 </div>
<div class="clear"></div>
<div class="peisong">
   <!--  <p>配送<span>山东省日照市</span></p> -->
    <p>快递<span>免运费</span></p>
    <p><span>7天内发货</span></p>
</div>

<p class="zhongliang">重量：<span><?php echo ($vo["zhongliang"]); ?></span></p>
<form action="/Shop/add_car" id="add_car"  method="post">
<div class="kucun">
    <p>


        <span id="jian">-</span>
        <span><input type="text" name="num" id="shuliang" value="1" style="width: 36px;border: none; text-align: center;"  onkeyup="value=value.replace(/^(0+)|[^\d]+/g,'')" maxlength='2'/></span>
        <span id="jia">+</span>

        <input type="hidden" name="gid" id="id" value="<?php echo ($vo["id"]); ?>"/>
       
    </p>
    库存<?php echo ($vo["art_writer"]); ?>件
</div>
</form> 
<div class="clear"></div>

<a href="##" class="sc_btn">立即购买</a>

<a href="##" class="sc_gwc">加入购物车</a>


    </div>
    <div class="clear"></div>
    
    <div class="shop_l">
        <div class="shop_title">
            热销产品
        </div>
        
        <ul>
            <?php if(is_array($rlist)): $i = 0; $__LIST__ = $rlist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vod): $mod = ($i % 2 );++$i;?><a href="/Shop/shop_show?id=<?php echo ($vod['id']); ?>">
                <li>
                <img src="<?php echo ($vod["art_img"]); ?>" />
                
            
                <?php echo ($vod["title"]); ?>
              <span>
                ￥<?php echo ($vod["art_jiage"]); ?>
              </span>
            </li>
        </a><?php endforeach; endif; else: echo "" ;endif; ?>
        </ul>
    </div>
     <div class="shop_r">
         <div class="zcon_b">
            <div class="zcon_tab">
                <span class="hover">产品详情</span>
               <!--  <span>产品规格</span> -->
            </div>
            
            <div class="zcon_con">
                    <?php echo ($vo["art_content"]); ?> 
            </div>

            <div class="zcon_con" style="display:none">
                  
            </div>
            
        </div>
      </div>  
    
        <div class="clear"></div>
    

</div>
<a href="/shop/car_list">
<div class="car_n">
    <span><?php echo ($carnum); ?></span>
</div></a>
<script type="text/javascript">

$(function(){

    /*订单数量*/
    $(".kucun #jia").click(function(){
        var b=Number($(".kucun #shuliang").val());
        $(".kucun #shuliang").val(Number(b)+1)
       // donum(Number(b)+1);
    })
    
    $(".kucun #jian").click(function(){
        var b=Number($(".kucun #shuliang").val());
        if(b>1){
            $(".kucun #shuliang").val(Number(b)-1);
           // donum(Number(b)-1);
        }
    })


    $(".sc_gwc").click(function(){
      $(".sc_gwc").attr('disabled','disabled');
      var num='<?php echo ($vo["art_writer"]); ?>';
      if(num<Number($("#shuliang").val())){
        alert("库存不足购买数量!");
        return false;
      }
      $("#add_car").submit();
    })

     $(".sc_btn").click(function(){
      $(".sc_btn").attr('disabled','disabled');
      var num='<?php echo ($vo["art_writer"]); ?>';
      if(num<Number($("#shuliang").val())){
        alert("库存不足购买数量!");
        return false;
      }
      $("#add_car").attr("action","/Shop/goumai");;

      $("#add_car").submit();
    })
})
</script>
<!--底部-->
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

       <a target="_blank"  href="https://www.beian.gov.cn/portal/registerSystemInfo?recordcode=37110302000118"><img src="/Public/new/images/gg_07.png" /></a>

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
  <a class="top" href="javascript:void(0)"><img src="images/top.jpg"><span>返回顶部</span></a>
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