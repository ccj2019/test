<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" /> 
    <title><?php echo ($glo["web_name"]); ?></title>
    <meta name="keywords" content="<?php echo ($glo["web_keywords"]); ?>" />
    <meta name="description" content="<?php echo ($glo["web_descript"]); ?>" />
        <link rel="shortcut icon" href="__PUBLIC__/images/favicon.ico" />
        <link rel="bookmark"href="__PUBLIC__/images/favicon.ico" />


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
   <a href="/shop/car_list">
            <div class="car_n">
                <span id="carnum"><?php echo ($carnum); ?></span>
            </div>
        </a>
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
                    </div>
                </div>
            </div>
        </div>
        <div class="tfFenLeiReMaiBox">
            <div class="tfFenLeiReMaiBoxMain">
                <img src="__PUBLIC__/shop/img/tfremaituijian.png" class="tfFenLeiReMaiBoxMainRemai">
                <?php if(is_array($tjgoods)): $i = 0; $__LIST__ = $tjgoods;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><a href="__APP__/Shop/shop_show/id/<?php echo ($vo["id"]); ?>.hmtl"> <div class="tfFenLeiReMaiBoxMainRightDiv">
                    <img src="<?php echo ($vo["art_img"]); ?>" >
                    <div class="tfFenLeiReMaiBoxMainRightDivRight">
                        <div class="tfFenLeiReMaiBoxMainRightDivRightTitle1"><?php echo (cnsubstr($vo["title"],20)); ?></div>
                        <div class="tfFenLeiReMaiBoxMainRightDivRightJiage">￥<?php echo ($vo["art_jiage"]); ?></div>
                        <div class="tfFenLeiReMaiBoxMainRightDivRightAnNiu">立即购买</div>
                    </div>   </div></a><?php endforeach; endif; else: echo "" ;endif; ?>
                
               
            </div>
        </div>
        
        <div class="tfFenleiMenuFenLeiBox">
            <div class="tfFenleiMenuFenLeiBoxMain">
                <div class="tfFenleiMenuFenLeiBoxMainDiv1">

                    <div  class="tfFenleiMenuFenLeiBoxMainDiv1QuanBuShangPin fls" data="0">全部商品</div>
                    <?php if(is_array($tlist)): $i = 0; $__LIST__ = $tlist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div  class="tfFenleiMenuFenLeiBoxMainDiv1Menu1 fls <?php if($vo["id"] == $type_id): ?>active<?php endif; ?>" data="<?php echo ($vo["id"]); ?>"><?php echo ($vo["type_name"]); ?></div><?php endforeach; endif; else: echo "" ;endif; ?>

                   
                </div>
            </div>
            <div class="tfFenleiMenuFenLeiBoxMainBottom tfFenleiMenuFenLeiBoxMain">
                
                <div class="tfFenleiMenuFenLeiBoxMainBottomLeft <?php if(513 == $type_id): ?>active<?php endif; ?>"  data="<?php echo ($vo["id"]); ?>">
                        <!-- 当前选中的二级分类需要添加 class "active" -->
                </div>


                <?php if(is_array($tlist)): $i = 0; $__LIST__ = $tlist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><!-- 当前选中的一级分类需要添加 class "active" -->
                    <div class="tfFenleiMenuFenLeiBoxMainBottomLeft <?php if($vo["id"] == $type_id): ?>active<?php endif; ?>"  data="<?php echo ($vo["id"]); ?>">
                        <!-- 当前选中的二级分类需要添加 class "active" -->
                        <?php if(is_array($vo['zlist'])): $i = 0; $__LIST__ = $vo['zlist'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v1): $mod = ($i % 2 );++$i;?><div class="tfFenleiMenuFenLeiBoxMainBottomLeftDiv <?php if($v1["id"] == $type_ids): ?>active<?php endif; ?>" data-id="<?php echo ($v1["id"]); ?>"><?php echo ($v1["type_name"]); ?></div><?php endforeach; endif; else: echo "" ;endif; ?>
                    </div><?php endforeach; endif; else: echo "" ;endif; ?>                
                <div class="tfFenleiMenuFenLeiBoxMainBottomRight">
                    <div class="tfFenleiMenuFenLeiBoxMainBottomRightDiv">

                            <div class="tfFenleitfggSousuonaliRightBox">
                              <!--   <input type="hidden" name="fenlei" value="" /> --><!-- 当前二级分类的id需要赋值到这个input -->
                                <input type="text" name="search" placeholder="龙利鱼" autocomplete="off" value="<?php echo ($name); ?>"/>
                                <div class="tfFenleitfggGoSouguo" > 
                                    <img  src="__PUBLIC__/shop/img/sousuo222.png" >搜索</div>
                            </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tfFenLeiYeShangPinBox">
            <div class="tfIndexChaoZhiTuanGouBox">
                
                <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="tfIndexChaoZhiTuanGou">
						 <a href="__APP__/Shop/shop_show/id/<?php echo ($vo["id"]); ?>.hmtl">
							<div class="suolue tfImg" style="background-image: url(<?php echo ($vo["art_img"]); ?>);"></div>
							<div class="tfShenglue2"><?php echo (cnsubstr($vo["title"],20)); ?></div>
							<div class="tfIndexChaoZhiTuanGouBottom"><span>￥<?php echo ($vo["art_jiage"]); ?></span>  <img src="__PUBLIC__/shop/img/xiaohongche.png"></div>
						</a>
					</div><?php endforeach; endif; else: echo "" ;endif; ?>
              
              
                
                
            </div>
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
   

            //点击全部商品
        $('.fls').on('click',function(){
            //页面取消分类高亮
            $('.tfFenleiMenuFenLeiBoxMainBottomLeftDiv,.tfFenleiMenuFenLeiBoxMainBottomLeft,.tfFenleiMenuFenLeiBoxMainDiv1Menu1').removeClass('active');
            //携带参数跳转当前页面
            var search = $('input[name="search"]').val();//搜索的文字
            var fenlei = $(this).attr("data");//二级分类的ID
            //alert("1");
            location.href = '?type_id='+fenlei+'&name='+search;
         })    
       
        

        // $('.tfFenleiMenuFenLeiBoxMainDiv1Menu1').on('click',function(){
        //     $('.tfFenleiMenuFenLeiBoxMainBottomLeft').removeClass('active').eq($(this).index()-1).addClass('active');
        // })
        //点击二级分类
        $('.tfFenleiMenuFenLeiBoxMainBottomLeftDiv').on('click',function(){
            //携带参数跳转当前页面
            var key =  $(this).attr("data-id");//二级分类的ID

            var search = $('input[name="search"]').val();//搜索的文字

            var fenlei =$(this).parent(".tfFenleiMenuFenLeiBoxMainBottomLeft").attr("data");
            location.href = '?type_id='+key+'&name='+search;
        })

        
        $('.tfFenleitfggGoSouguo').on('click',function(){
             var search = $('input[name="search"]').val();//搜索的文字
             location.href = '?type_id=<?php echo ($type_id); ?>&name='+search+'&keys=<?php echo ($keys); ?>';
        })
    </script>