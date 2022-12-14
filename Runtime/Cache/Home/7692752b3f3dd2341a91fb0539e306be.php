<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" /> 
    <title><?php echo ($glo["web_name"]); ?></title>
    <meta name="keywords" content="<?php echo ($glo["web_keywords"]); ?>" />
    <meta name="description" content="<?php echo ($glo["web_descript"]); ?>" />
        <link rel="shortcut icon" href="__PUBLIC__/images/favicon.ico" />
        <link rel="bookmark"href="__PUBLIC__/images/favicon.ico" />


<!--<meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests"> -->

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
		<style>
			.tfDaDuiShangPinBox .tfDivTd{
				height: auto;
				padding-bottom: 10px;
			}
		</style>


</head>

<body>
  <a href="/shop/car_list">
            <div class="car_n">
                <span id="carnum"><?php echo ($carnum); ?></span>
            </div>
        </a>
       <div class="tfggHeaderBox" style="box-shadow: 0px 0px 2px rgba(0, 0, 0, 0.25);">
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
                    <div class="tfggHeaderBoxMainRightImgBox" style="margin-left: 20px;">
                        <a href="__APP__/member/index"><img src="__PUBLIC__/shop/img/tfUser.png"></a>
                        <a href="__APP__/shop/car_list"><img src="__PUBLIC__/shop/img/tfCart.png" class="tfgouwuche"></a>

       
                 <?php if($UID > '0'): ?><a href="<?php echo U('/member/common/actlogout');?>"  style="    font-size: 0.2rem;
    display: block;
    float: left;
    margin-top: 0.4rem;
    color: #bf5757;
    margin-left: 0.13rem;">退出</a><?php endif; ?>
     

                    </div>
                </div>
            </div>
        </div>





<div class="tfBannerBox">
            <?php $_result=get_ad(37);if(is_array($_result)): $i = 0; $__LIST__ = $_result;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$voa): $mod = ($i % 2 );++$i;?><a  href="<?php echo ($voa["url"]); ?>"><img src="<?php echo ($voa["img"]); ?>" class='tfBanner <?php if($i == 1): ?>active<?php endif; ?>'></a><?php endforeach; endif; else: echo "" ;endif; ?> 

            <div class="tfBannerDianBox">
            <?php $_result=get_ad(37);if(is_array($_result)): $i = 0; $__LIST__ = $_result;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$voa): $mod = ($i % 2 );++$i;?><div class='tfBannerDian <?php if($i == 1): ?>active<?php endif; ?>'></div><?php endforeach; endif; else: echo "" ;endif; ?> 
            </div>
        </div>
        <div class="tfggMainBox">
           
                <div class="tfggSousuonali">
                    <img src="__PUBLIC__/shop/img/indexNiuBi.png" class="tfggSousuonaliLeftImg">
                    <div class="tfggSousuonaliRightBox">
                        <input type="text" name="search" placeholder="龙利鱼" autocomplete="off"/>
                        <div class="tfggGoSouguo" >搜索</div>
                    </div>
                </div>
         
            <div class="wuGongGeBox">


                <div class="wuGongGeBoxDiv" style="background-image: url(__PUBLIC__/shop/img/tfgg.jpg);">
                    <div class="wuGongGeBoxDivJuzhongDiv">
                        <div class="wuGongGeBoxDivJuzhongDivTitle1">SEAFOOD</div>
                        <div class="wuGongGeBoxDivJuzhongDivTitle2">自养海鲜</div>
                        <a href="__APP__/Index/shop/type_id/56.html">
                            <div class="wuGongGeBoxDivJuzhongDivTitle3">立即选购</div>
                        </a>
                    </div>
                </div>
                <div class="wuGongGeBoxDiv" style="background-image: url(__PUBLIC__/shop/img/tfgg1.jpg);">
                    <div class="wuGongGeBoxDivJuzhongDiv">
                        <div class="wuGongGeBoxDivJuzhongDivTitle1">DRIED FOOD</div>
                        <div class="wuGongGeBoxDivJuzhongDivTitle2">海鲜干货</div>
                        <a href="__APP__/Index/shop/type_id/56.html">
                            <div class="wuGongGeBoxDivJuzhongDivTitle3">立即选购</div>
                        </a>
                    </div>
                </div>
                <div class="wuGongGeBoxDiv" style="background-image: url(__PUBLIC__/shop/img/tfgg2.jpg);">
                    <div class="wuGongGeBoxDivJuzhongDiv">
                        <div class="wuGongGeBoxDivJuzhongDivTitle1">STREET FOOD</div>
                        <div class="wuGongGeBoxDivJuzhongDivTitle2">即食小吃</div>
                        <a href="__APP__/Index/shop/type_id/56.html">
                            <div class="wuGongGeBoxDivJuzhongDivTitle3">立即选购</div>
                        </a>
                    </div>
                </div>
                <div class="wuGongGeBoxDiv" style="background-image: url(__PUBLIC__/shop/img/tfgg3.jpg);">
                    <div class="wuGongGeBoxDivJuzhongDiv">
                        <div class="wuGongGeBoxDivJuzhongDivTitle1">COUPON</div>
                        <div class="wuGongGeBoxDivJuzhongDivTitle2">船票优惠券</div>
                        <a href="__APP__/Index/shop/type_id/56.html">
                            <div class="wuGongGeBoxDivJuzhongDivTitle3">立即选购</div>
                        </a>
                    </div>
                </div>


            </div>
            <div class="tfRexiaoBox">
                <div class="tfRexiaoBoxTitle1">
                    <div class="tfRexiaoBoxTitle1Left">
                        热销商品
                    </div>
                    <div class="tfRexiaoBoxTitle1Right">
                        <div class="tfRexiaoBoxTitle1RightTop1">超值爆款嗨不停</div>
                        <div class="tfRexiaoBoxTitle1RightTop2">好物疯抢</div>
                    </div>
                </div>
                <div class="tfRexiaoShangPinBox">

                <?php if(is_array($tjgoods)): $i = 0; $__LIST__ = $tjgoods;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><a href="/Shop/shop_show/id/<?php echo ($vo["id"]); ?>.hmtl">
                        <div class="tfRexiaoShangPin">
                            <div class="tfRexiaoShangPinTopDiv">
                                <div class="tfRexiaoShangPinTopDivLeft"><span><?php echo ($i); ?></span></div>
                                <div class="tfRexiaoShangPinTopDivRight">
                                    <div class="tfRexiaoShangPinTopDivRight1 tfshenglue1"><?php echo (cnsubstr($vo["title"],20)); ?></div>
                                    <div class="tfRexiaoShangPinTopDivRight2 tfshenglue1"><?php echo ($vo["art_miaoshu"]); ?></div>
                                    <div class="tfRexiaoShangPinTopDivRight3">¥<?php echo ($vo["art_jiage"]); ?></div>
                                </div>
                            </div>
                            <div class="tfRexiaoShangPinTu tfImg" style="background-image: url(<?php echo ($vo["art_img"]); ?>);"></div>
                        </div>
                    </a><?php endforeach; endif; else: echo "" ;endif; ?>    
                </div>
            </div>
            <div class="tfChaoZhiAd">
                <div class="tfChaoZhiAdLeftBox">
                    <div class="tfChaoZhiAdLeftBoxMenuBox">

                        

                         <?php if(is_array($tinfo)): $i = 0; $__LIST__ = $tinfo;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v1): $mod = ($i % 2 );++$i;?><a href="__APP__/Index/shop?name=<?php echo ($v1); ?>">
                                    <div class="tfChaoZhiAdLeftBoxMenu"><?php echo ($v1); ?></div>
                                </a><?php endforeach; endif; else: echo "" ;endif; ?>


                    </div>
                    <a href="__APP__/Index/shop.html">
                        <div class="tfChaoZhiAdLeftBoxChakan">查看更多>></div>
                    </a>
                </div>
                <div class="tfChaoZhiAdRightBox">
                    <?php if(is_array($guanggao)): $i = 0; $__LIST__ = array_slice($guanggao,0,2,true);if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><a  href="<?php echo ($vo["url"]); ?>"> 
                            <div class="tfChaoZhiAdRightBoxImg tfImg" style="background-image: url(<?php echo ($vo["img"]); ?>);"></div>
                        </a><?php endforeach; endif; else: echo "" ;endif; ?>
                </div>
            </div>
            <div class="tfDaDuiShangPinBox">
                <div class="tfDivTable">
                    <?php if(is_array($tjlist)): $i = 0; $__LIST__ = $tjlist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><a href="/Shop/shop_show/id/<?php echo ($vo["id"]); ?>.hmtl">
                            <div class="tfDivTd">
                                <div class="tfDivTdTitle1 tfshenglue1"><?php echo (cnsubstr($vo["title"],20)); ?></div>
                                <div class="tfDivTdTitle2">¥<?php echo ($vo["art_jiage"]); ?></div>
                                <div class="tfDivTdImg tfImg" style="background-image: url(<?php echo ($vo["art_img"]); ?>);"></div>
                            </div>
                        </a><?php endforeach; endif; else: echo "" ;endif; ?>
                </div>
            </div>
            <?php if(is_array($guanggao)): $i = 0; $__LIST__ = array_slice($guanggao,2,1,true);if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><a  href="<?php echo ($vo["url"]); ?>"> 
                    <div class="tfDaAd tfImg" style="background-image: url(<?php echo ($vo["img"]); ?>)"></div>
                </a><?php endforeach; endif; else: echo "" ;endif; ?>

            <div class="tfDanPinTuiJianTop1">
                <img src="__PUBLIC__/shop/img/tfdanpintuijian.png" class="tfDanPinTuiJianTop1leftImg">
                <a href="__APP__/Index/shop.html">
                    <img src="__PUBLIC__/shop/img/chakanJianTou.png" class="tfChakanjiantou">
                </a>
            </div>
         <div class="tfshangPinDiSanGeBox">
                <?php if(is_array($danpin)): $i = 0; $__LIST__ = $danpin;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="tfshangPinDiSanGe">
                        <div class="tfTuijian"></div>
                        <div class="tfshangPinDiSanGeImg tfImg" style="background-image: url(<?php echo ($vo["art_img"]); ?>);"></div>
                        <div class="tfshangPinDiSanGeTitle1">¥<?php echo ($vo["art_jiage"]); ?></div>
                        <a href="/Shop/shop_show/id/<?php echo ($vo["id"]); ?>.hmtl">
                            <div class="tfshangPinDiSanGeTitle2">立即购买</div>
                        </a>
                    </div><?php endforeach; endif; else: echo "" ;endif; ?>
                
            </div>
              <!--  <?php if(is_array($guanggao)): $i = 0; $__LIST__ = array_slice($guanggao,3,1,true);if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><a  href="<?php echo ($vo["url"]); ?>"> 
                    <div class="tfDaAd tfImg" style="background-image: url(<?php echo ($vo["img"]); ?>)"></div>
                </a><?php endforeach; endif; else: echo "" ;endif; ?>
            <div class="tfDanPinTuiJianTop1">
                <img src="__PUBLIC__/shop/img/tfchaozhituangoulogo.png" class="tfDanPinTuiJianTop1leftImg2">
                <a href="">
                    <img src="__PUBLIC__/shop/img/chakanJianTou.png" class="tfChakanjiantou">
                </a>
            </div>
            <div class="tfCHaoZhiTuanGouBox">
                <?php if(is_array($tuangou)): $i = 0; $__LIST__ = $tuangou;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="tfCHaoZhiTuanGou">
                        <div style="height: 0.22rem;"></div>
                        <div class="tftuangouSuolUE tfImg" style="background-image: url(<?php echo ($vo["art_img"]); ?>);"></div>
                        <div class="tfCHaoZhiTuanGouTitle1 tfshenglue1">
                            <font color="#FF9A14">(三人团)</font><?php echo (cnsubstr($vo["title"],20)); ?>
                        </div>
                        <div class="tfCHaoZhiTuanGouTitle2">¥<?php echo ($vo["art_jiage"]); ?></div>
                        <a href="/Shop/shop_show/id/<?php echo ($vo["id"]); ?>.hmtl">
                            <div class="tfCHaoZhiTuanGouTitle3">立即购买</div>
                        </a>
                    </div><?php endforeach; endif; else: echo "" ;endif; ?>
           
                
            </div> -->
        </div>
   <div class="tfIndexBottomAd">
    <?php if(is_array($guanggao)): $i = 0; $__LIST__ = array_slice($guanggao,4,2,true);if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><a  href="<?php echo ($vo["url"]); ?>"> 
                <div class="tfIndexBottomAdImg tfImg" style="background-image: url(<?php echo ($vo["img"]); ?>);"></div>
            </a><?php endforeach; endif; else: echo "" ;endif; ?>
    </div>


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

<script src="__PUBLIC__/shop/js/jquery.min.js" type="text/javascript" charset="utf-8"></script>
    <script type="text/javascript">
        var tfBannerIndex = 0;
        var tfBannerStatus = true;
        //鼠标经过切换轮播图
        $('.tfBannerDianBox').on('mouseover', '.tfBannerDian', function() {
            tfBannerStatus = false;
            $('.tfBannerDianBox').find('.tfBannerDian').removeClass('active').eq($(this).index()).addClass(
                'active');
            $('body').find('.tfBanner').removeClass('active').eq($(this).index()).addClass('active');
            tfBannerIndex = $(this).index()
        })
        $('.tfBannerDianBox').on('mouseout', '.tfBannerDian', function() {
            tfBannerStatus = true;
        })
        //定时轮播Banner
        setInterval(function() {
            if (tfBannerStatus) {
                tfBannerIndex++;
                if (tfBannerIndex + 1 > $('body').find('.tfBanner').length) {
                    tfBannerIndex = 0
                }
                $('.tfBannerDianBox').find('.tfBannerDian').removeClass('active').eq(tfBannerIndex).addClass(
                    'active');
                $('body').find('.tfBanner').removeClass('active').eq(tfBannerIndex).addClass('active');
            }
        }, 5000)


        
        $('.tfggGoSouguo').on('click',function(){
             var search = $('input[name="search"]').val();//搜索的文字
             location.href = 'Index/shop?name='+search;
        })
    </script>