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


<script type="text/javascript" src="/Public/new/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="__PUBLIC__/web/js/cui-timer.js"></script>
<link rel="stylesheet" type="text/css" href="__PUBLIC__/tfgg/tfInvst.css?v=123" />
<div class="tfBannerBox" style="margin-top: 0;">
	<?php $_result=get_ad(37);if(is_array($_result)): $i = 0; $__LIST__ = $_result;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$voa): $mod = ($i % 2 );++$i;?><a href="<?php echo ($voa["url"]); ?>"><img src="../<?php echo ($voa["img"]); ?>" class='tfBanner <?php if($i == 1): ?>active<?php endif; ?>'></a><?php endforeach; endif; else: echo "" ;endif; ?>

	<div class="tfBannerDianBox">
		<?php $_result=get_ad(37);if(is_array($_result)): $i = 0; $__LIST__ = $_result;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$voa): $mod = ($i % 2 );++$i;?><div class='tfBannerDian <?php if($i == 1): ?>active<?php endif; ?>'></div><?php endforeach; endif; else: echo "" ;endif; ?>
	</div>
</div>
<div class="tfInvstBox1">
	<a href="__URL__/xmlist/type/1.html">
	<div class="tfInvstBox1Div3" style="width: 295px;     margin-right: 23px;background: url(__PUBLIC__/cpublic/image/ycyz.jpg);">
		渔场养殖
	</div></a>
	<div class="tfInvstBox1Div2">
		<div class="tfInvstBox1Div2Title1">SEARCH</div>
		<div class="tfInvstBox1Div2Title1" style="position: relative;">POPULAR ITEMS</div>
		<div class="tfInvstBox1Div2SearchBox">
			<input type="search" name="search" placeholder="搜索项目">
		</div>
		<img class="sousuojiantou" id="ss" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABYAAAAZCAYAAAA14t7uAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAGySURBVHgBrZU/UsJAFMbfC/inUTlCRnDGEm4AJ1B0KOj0BlBI7AglWMANsBaY4AnQG1haOLhHyFgBIXm+3UlmGAbBJfma7G7efPnyy9sNgIYeh9lna5id/KfWAA0RwhVfitYgZ++q1TMOsKUGSE02L26rRdCUNTrvcvQaAbiY8gvtshCb6rQSSx0bAaemD06UoaXh1PpmJhHjVlm44B2W+V1dRMwfnRrNRIyl2tVPAQGW1YSxNAbntUSMlXnl642QbDVBbD68XORX72t/vHWFfV3kThHzn6DQu2dUECNxpOOUL5EIRmIenqT60bpKbDmmCTFEi1Qe09AHggw/oMWYbLRGWblwB0mKsCRRZCBxLc/SsDio04E3hjhCMpHQDmfjdkW8xu4KKcbpMM5rZiDAC0rtqhCxu0KddMoU3MhUrqchhhrDrEwZbmmyI1OpvVFYzqUJgTfhtCYR9TqVaX31/v4oQlPJdd10b2Pexr3IVHLdVKONojHK1ZCoK8eB4RefbsR7bGPF1fe+5ViebJ3baeuvWj0U/sIJR+NtplJ7tJviWt9VpWfMv6T5bOZGZ+42/QIuO69PwtrpDwAAAABJRU5ErkJggg==">
	</div>
<a href="__URL__/xmlist/type/2.html">
	<div class="tfInvstBox1Div3" style="width: 313px;background: url(__PUBLIC__/cpublic/image/hymc.jpg);">
		海洋牧场
	</div>
</a>
	<a href="__URL__/xmlist/type/3.html">
	<div class="tfInvstBox1Div4" style="background: url(__PUBLIC__/cpublic/image/hsyc.jpg);">
		海上渔船
	</div></a>
	<div class="tfInvstBox1Div5"></div>
	<a href="__URL__/xmlist/type/4.html">
	<div class="tfInvstBox1Div6"  style="background: url(__PUBLIC__/cpublic/image/hxsg.jpg);">
		海鲜收购
	</div>
	</a>
</div>
<div class="tfInvstBox2">
	<div class="tfInvstBox2Bg"></div>
</div>
<div class="tfInvstBox2ListBox">
<?php if(is_array($yzlist)): $i = 0; $__LIST__ = $yzlist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><a href="__APP__/invest/detail/id/<?php echo ($vo["id"]); ?>.hmtl">
	<div class="tfInvstBox2ListBoxList">
		<?php if($vo["borrow_status"] == 2): ?><div class="fenleiBoxtf hong">热销</div>
			<div class="shangpinSuoLue tflistimgsuolue" style="background-image: url(<?php echo ($vo["borrow_img"]); ?>);">
				<div class="dajiliBox">距结束
					<span class="cui-timer" data-config = '["endtime":"<?php echo (date("Y-m-d H:i:s",$vo["endtimes"])); ?>","msec":false]'></span></div>
			</div><?php endif; ?>
		<?php if($vo["borrow_status"] == 1): ?><div class="fenleiBoxtf huang">预热</div>
			<div class="shangpinSuoLue tflistimgsuolue" style="background-image: url(<?php echo ($vo["borrow_img"]); ?>);">
				<div class="dajiliBox">距开始
					<span class="cui-timer" data-config = '["endtime":"<?php echo (date("Y-m-d H:i:s",$vo["start_time"])); ?>","msec":false]'></span>
				</div>
			</div><?php endif; ?>


		<div class="tfInvstBox2ListBoxListMain">
			<div class="tfInvstBox2ListBoxListMainTitle1">
		   <?php echo ($vo["borrow_name"]); ?>
			</div>
			<div class="tfInvstBox2ListBoxListMainTitle2">周期 <?php echo ($vo["borrow_duration"]); ?>个月</div>
			<div style="margin-top: 10px;">
				<div class="tfInvstBox2ListBoxListMainTitle3">可购买<?php echo ($vo["fenshu"]); ?>份</div>
			</div>
			<div class="tfInvstBox2ListBoxListMainTitle4">
				<div style="color: #FF1A1A;">￥<span style="font-size: 40px;"><?php echo ($vo["borrow_min"]); ?></span></div>
				<?php if($vo["borrow_status"] == 1): ?><div class="tfInvstBox2Button">去预览</div><?php endif; ?>
				<?php if($vo["borrow_status"] == 2): ?><div class="tfInvstBox2Button qianggou">
						<div style="line-height: 34px;">去抢购</div>
						<div class="tfJinDuTiaoBox">
							<div class="tfjinduKeZi">
								<div class="tfjinduNei" style="width: <?php echo ($vo["progress"]); ?>%;"></div>
							</div>
							<?php echo ($vo["progress"]); ?>%
						</div>
					</div><?php endif; ?>
			</div>
		</div>
	</div>
	</a><?php endforeach; endif; else: echo "" ;endif; ?>




</div>

<script type="text/javascript">

	$(function(){
		$('.cui-timer').Cuitimer();
	});

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

	$('#ss').on('click',function(){
		var search = $('input[name="search"]').val();//搜索的文字
		location.href = '__URL__/xmlist?name='+search;
	})

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