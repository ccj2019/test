<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" /> 
	<title><?php if(!empty($web_title)): echo ($web_title); ?>-<?php endif; echo ($glo["web_name"]); ?></title>
	<meta name="keywords" content="<?php echo ($glo["web_keywords"]); ?>" />
	<meta name="description" content="<?php echo ($glo["web_descript"]); ?>" />
	<link rel="shortcut icon" href="__PUBLIC__/images/favicon.ico" />
        	<link rel="bookmark"href="__PUBLIC__/images/favicon.ico" />
	<link rel="stylesheet" type="text/css" href="__PUBLIC__/web/css/all.css" />
	<link rel="stylesheet" type="text/css" href="__PUBLIC__/web/css/ldy.css" />
	<link rel="stylesheet" type="text/css" href="__PUBLIC__/web/css/ltt.css" />
	<script src="__PUBLIC__/web/js/jquery-1.8.3.min.js"></script>
	<script type="text/javascript" src="__PUBLIC__/web/js/ldy.js"></script>

<script src="/Style/file/layer/layer.min.js"></script>
<script type="text/javascript" src="__PUBLIC__/web/js/layer.js"></script>
<link rel="stylesheet" type="text/css" href="__PUBLIC__/shop/css/tfindex.css" />
<style type="text/css">
	body {
		background: #f8f8f8;
	}
</style>
 	<!--<meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">-->
<link rel="stylesheet" type="text/css" href="/Public/new/css/mstyle.css"/>
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

<body><div class="tfggHeaderBox">
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
<!--内容部分-->
<style type="text/css">
.tabvbox2 .bottom ul li{
	text-align: left;
	padding-left: 100px;
}
</style>
<div class="box navls">
	<span><a href="<?php echo U('/member/index/index');?>">会员中心</a></span>
	<span>&gt;</span>
	<span><a href="<?php echo U('/member/index/index');?>">账号信息</a></span>
</div>
<div class="box clearfix">
	<style type="text/css">
.lst2 .p1 img{
    display: inline;
}
</style>
<div class="Lxlbox">
    <div class="lst1">
        <p class="p1">
            <span>账户安全级别</span>
            <span class="bfb"><?php echo ($mInfoProgress); ?>%</span>
        </p>
        <div class="p2">
            <p class="p3" style="width: <?php echo ($mInfoProgress); ?>%"></p>
        </div>
    </div>
    <div class="list2box">
        <div class="lst2 left-top-zhanghu">
            <div class="p1" data-a="1">
                <img src="/Public/web/img/huiyuan/zhanghu.png" />
                <span>账户中心</span>
                <span class="smp1">&gt;</span>
            </div>
            <div class="p2">
                <ul>
                    <a href="<?php echo U('/member');?>">
						<li class="left-left-index-index">账户信息</li>
					</a>
                    <a href="<?php echo U('charge/index');?>">
						<li class="left-left-charge-index">购买鱼币</li>
					</a>
                    <a href="<?php echo U('withdraw/withdraw');?>">
						<li class="left-left-withdraw-withdraw">我的提现</li>
					</a>
                    <a href="__APP__/member/bonus/youhuijun">
						<li class="left-left-bonus-youhuijun">我的红包</li>
					</a>
                    <a href="__APP__/member/capital/detail">
						<li class="left-left-capital-detail">收支明细</li>
					</a>
                    <a href="__APP__/member/credit/index">
						<li class="left-left-credit-index">积分明细</li>
					</a> 
                    <a href="__APP__/member/experience/index">
						<li class="left-left-experience-index">体验金</li>
					</a>
                    <!--<a href="<?php echo U('agreement/index');?>">-->
                        <!--<li class="left-left-agreement-index">合同管理</li>-->
                    <!--</a> -->
                </ul>
            </div>
        </div>
        <div class="lst2 left-top-xiangmu">
            <div class="p1" data-a="1">
                <img src="/Public/web/img/huiyuan/wode.png" />
                <span>产品订单</span>
                <span class="smp1">&gt;</span>
            </div>
            <div class="p2">
                <ul>
                    <a href="<?php echo U('tendout/index',array('type'=>1));?>">
						<li class="left-tendout-1">养殖订单</li>
					</a>
                    <a href="<?php echo U('tendout/index',array('type'=>2));?>">
						<li class="left-tendout-2">销售订单</li>
					</a>
                    <a href="<?php echo U('tendout/index',array('type'=>3));?>">
						<li class="left-tendout-3">共建订单</li>
					</a>
					 
                    <a href="<?php echo U('tendout/zhongzhi',array('type'=>4));?>">
                        <li class="left-tendout-4">种植订单</li>
                    </a>

                     <a href="<?php echo U('guanzhu/zdlist',array('type'=>5));?>">
                        <li class="left-tendout-5">快捷购买</li>
                    </a>

			      <!--   <a href="<?php echo U('guanzhu/bespeak',array('type'=>6));?>">
                        <li class="left-tendout-6">我的预约</li>
                    </a> -->

                      <!--<a href="<?php echo U('guanzhu/index',array('type'=>6));?>">-->
                        <!--<li class="left-tendout-6">我的关注</li>-->
                    <!--</a>-->

                </ul>
            </div>
        </div>
        <div class="lst2 left-top-fqguanli">
            <div class="p1" data-a="1">
                <img src="/Public/web/img/huiyuan/faqi.png" />
                <span>发起管理</span>
                <span class="smp1">&gt;</span>
            </div>
            <div class="p2">
                <ul>
                    <a href="<?php echo U('borrowin/index');?>">
						<li class="left-borrowin-index">发起项目</li>
					</a>
                    <a href="<?php echo U('borrowin/borrowpaying');?>">
						<li class="left-borrowin-borrowpaying">项目收益</li>
					</a>
                    <a href="<?php echo U('borrowin/borrowlog');?>">
                        <li class="left-borrowin-borrowlog">放款记录</li>
                    </a>
                </ul>
            </div>
        </div>
        <div class="lst2 left-top-gerensz">
            <div class="p1" data-a="1">
                <img src="/Public/web/img/huiyuan/Settings.png" />
                <span>个人设置</span>
                <span class="smp1">&gt;</span>
            </div>
            <div class="p2">
                <ul>
                    <a href="<?php echo U('memberinfo/index');?>">
						<li class="left-memberinfo">个人资料</li>
					</a>
                    <a href="__APP__/member/safe">
						<li class="left-safe">安全信息</li>
					</a>
                    <a href="__APP__/member/memberaddress">
						<li class="left-memberaddress">收货地址</li>
					</a>
                    <a href="__APP__/member/bank/index">
						<li class="left-bank">银行卡</li>
					</a>
                </ul>
            </div>
        </div>
        <div class="lst2 left-top-xiaoxi">
			<div class="p1" data-a="1">
				<img src="/Public/web/img/huiyuan/xiaoxi.png" />
				<span>消息中心</span>
				<span class="smp1">&gt;</span>
			</div>
			<div class="p2">
				<ul>
					<a href="<?php echo U('msg/sysmsg');?>">
						<li class="left-sysmsg left-viewmsg">站内通知</li>
					</a>
					<a href="<?php echo U('msg/projectmsg');?>">
						<li class="left-projectmsg">项目动态</li>
					</a>

				</ul>
			</div>
		</div>
        <div class="lst2 left-top-yaoqing">
            <div class="p1" data-a="1">
                <img src="/Public/web/img/huiyuan/yaoqing.png" />
                <span>我的邀请</span>
                <span class="smp1">&gt;</span>
            </div>
            <div class="p2">
                <ul>
                    <a href="<?php echo U('promotion/index');?>">
						<li class="left-yaoqing">邀请记录</li>
					</a>
                </ul>
            </div>
        </div>
        <div class="lst2 left-top-duihuan">
            <div class="p1" data-a="1">
                <img src="/Public/web/img/huiyuan/jifen.png" />
                <span>商城订单</span>
                <span class="smp1">&gt;</span>
            </div>
            <div class="p2">
                <ul>
                    <a href="__APP__/member/shoporder">
						<li class="left-duihuan">商城订单</li>
					</a>
                </ul>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    if ('<?php echo ($glomodulename); ?>' == 'shoporder') {
        $('.left-top-duihuan').addClass('on').siblings().removeClass('on');
        $('.left-top-duihuan .smp1').html('v');
        $('.left-duihuan').css({"background":"#e1f5ff","border-left":"10px solid #00a0ea"});
    }else if('<?php echo ($glomodulename); ?>' == 'promotion'){
        $('.left-top-yaoqing').addClass('on').siblings().removeClass('on');
        $('.left-top-yaoqing .smp1').html('v');
        $('.left-yaoqing').css({"background":"#e1f5ff","border-left":"10px solid #00a0ea"});
    }else if('<?php echo ($glomodulename); ?>' == 'msg'){
        $('.left-top-xiaoxi').addClass('on').siblings().removeClass('on');
        $('.left-top-xiaoxi .smp1').html('v');
        $('.left-<?php echo ($gloactionname); ?>').css({"background":"#e1f5ff","border-left":"10px solid #00a0ea"});
    }else if('<?php echo ($glomodulename); ?>' == 'memberinfo'){
        $('.left-top-gerensz').addClass('on').siblings().removeClass('on');
        $('.left-top-gerensz .smp1').html('v');
        $('.left-memberinfo').css({"background":"#e1f5ff","border-left":"10px solid #00a0ea"});
    }else if('<?php echo ($glomodulename); ?>' == 'safe'){
        $('.left-top-gerensz').addClass('on').siblings().removeClass('on');
        $('.left-top-gerensz .smp1').html('v');
        $('.left-safe').css({"background":"#e1f5ff","border-left":"10px solid #00a0ea"});
    }else if('<?php echo ($glomodulename); ?>' == 'memberaddress'){
        $('.left-top-gerensz').addClass('on').siblings().removeClass('on');
        $('.left-top-gerensz .smp1').html('v');
        $('.left-memberaddress').css({"background":"#e1f5ff","border-left":"10px solid #00a0ea"});
    }else if('<?php echo ($glomodulename); ?>' == 'bank'){
        $('.left-top-gerensz').addClass('on').siblings().removeClass('on');
        $('.left-top-gerensz .smp1').html('v');
        $('.left-bank').css({"background":"#e1f5ff","border-left":"10px solid #00a0ea"});
    }else if('<?php echo ($glomodulename); ?>' == 'borrowin'){
        $('.left-top-fqguanli').addClass('on').siblings().removeClass('on');
        $('.left-top-fqguanli .smp1').html('v');
        $('.left-borrowin-<?php echo ($gloactionname); ?>').css({"background":"#e1f5ff","border-left":"10px solid #00a0ea"});
    }else if('<?php echo ($glomodulename); ?>' == 'tendout'){
        $('.left-top-xiangmu').addClass('on').siblings().removeClass('on');
        $('.left-top-xiangmu .smp1').html('v');
        $('.left-tendout-<?php echo ($_GET["type"]); ?>').css({"background":"#e1f5ff","border-left":"10px solid #00a0ea"});
    }else if('<?php echo ($glomodulename); ?>' == 'guanzhu'){
        $('.left-top-xiangmu').addClass('on').siblings().removeClass('on');
        $('.left-top-xiangmu .smp1').html('v');
        $('.left-tendout-<?php echo ($_GET["type"]); ?>').css({"background":"#e1f5ff","border-left":"10px solid #00a0ea"});
    }else{
        $('.left-top-zhanghu').addClass('on').siblings().removeClass('on');
        $('.left-top-zhanghu .smp1').html('v');
        $('.left-left-<?php echo ($glomodulename); ?>-<?php echo ($gloactionname); ?>').css({"background":"#e1f5ff","border-left":"10px solid #00a0ea"});
    }
</script>
	<div class="tabvbox2">
		<!--会员中心-->
		<div class="opt">
			<div class="tt clearfix">
				<div class="tx fl">
					<img src="<?php echo (($minfoz["user_img"])?($minfoz["user_img"]):'/public/web/img/zuixinzixun/tx.png'); ?>" alt="" />
				</div>
				<div class="med fl clearfix">
					<div class="name">
						<p>尊敬的<span><?php echo ($_SESSION['u_user_name']); ?></span>，您好~</p>
					</div>
					<div class="phone">
						<ul class="clearfix">
							<?php echo (getverify($minfoz["id"])); ?>
						</ul>
					</div>
				</div>
				<div class="cz fl">
					<a href="<?php echo U('charge/index');?>">购买</a>
				</div>
				<div class="qd fl">
					<a href="__APP__/member/capital/qiandao">签到</a>
				</div>
			</div>
			<div class="bott">
				<ul>
					<li>
						<p class="p1">资产金额</p>
						<p class="p2">
					  ¥<?php echo getFloatvalue($all_money,2);?>
					  <!--
					  ¥<?php echo getFloatvalue($minfoz['account_money']+$minfoz['money_collect']+$minfoz['money_freeze'],2);?>-->
						</p>
					</li>
					<li>
						<p class="p1">投资收益</p>
						<p class="p2">
							￥<?php echo getFloatvalue($minfoz['receive_interests'],2);?>
						</p>
					</li>
				</ul>
			</div>
		</div>
		<div class="bottom">
			<p class="p1">资产状况</p>
			<ul>
				<li>
					<p>可用金额：<span><?php echo getFloatvalue($minfoz['account_money'],2);?></span></p>
					
					
					
					
					
				</li>
				
				 <li style="margin-bottom:0;">
					<p>待收本金：<span><?php echo (fmoney($dsbj,false)); ?></span></p>
				</li> 
				
				
				
			</ul>
			<ul style="border: none;">
				<!-- <li>
					<p>已收本金：<span><?php echo (($zz)?($zz):"0.00"); ?></span></p>
				</li> -->
				<li> <p>积分总额：<span><?php echo (($minfoz["credits"])?($minfoz["credits"]):"0"); ?></span></p>
				</li>
				<li>
					<p>鱼　　币：<span><?php echo (($yubi)?($yubi):"0"); ?></span></p>
				</li>
				<!--<li>
					<p>累计充值：<span><?php echo (($msummary["ljczje"])?($msummary["ljczje"]):"0.00"); ?></span></p>
				</li>
				<li style="margin-bottom:0;">
					<p>累计提现：<span><?php echo (($msummary["ljtxje"])?($msummary["ljtxje"]):"0.00"); ?></span></p>
				</li>-->
			</ul>
			<div class="clear"></div>
		</div>
	</div>
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