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
<style type="text/css">
		.bgxmlis>div{
		width: 13%;
		text-align: center;
		padding-left: 0;
	}
	.tou1 span{
		width: 13%;
		text-align: center;
		padding-left: 0;
		margin-left: 0;
	}
</style>
<!--内容部分-->
<div class="box navls">
    <span><a href="<?php echo U('/member');?>">会员中心</a></span>
    <span>&gt;</span>
    <span><a >发起项目</a></span>

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
    <div class="tabvbox">
        <div class="tabv clearfix">
            <div <?php if($borrow_status == '' ): ?>class="on"<?php endif; ?>><span><a href="<?php echo U('borrowin/index');?>">所有项目</a></span></div>
            <div <?php if($borrow_status == 'start' ): ?>class="on"<?php endif; ?>><span><a href="<?php echo U('borrowin/index',array('borrow_status'=>'start'));?>">发起成功</a></span></div>
            <div <?php if($borrow_status == 'nostart' ): ?>class="on"<?php endif; ?>><span><a href="<?php echo U('borrowin/index',array('borrow_status'=>'nostart'));?>">未发起</a></span></div> 
        </div>
        <div class="tabv2box">
            <div class="tabv2 show">
                 <!--充值记录-->
                <div class="bgbx">
                    <div class="tou1">  
                        <span style="width: 230px;">项目名称</span>
                        <span>发起时间</span>
                        <span>类型</span> 
                        <span>筹集资金</span>
                        <span style="width: 115px">状态</span>
                        <span style="width: 14%;">操作</span>
                    </div>
                    <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="bgxmlis">
                        <div class="bt" style="width: 230px !important;">
                            <img src="<?php echo ($vo["borrow_img"]); ?>" width="60" alt="" />
                            <p><?php echo ($vo["borrow_name"]); ?></p>
                        </div>
                        <div>
                            <?php echo (date("Y-m-d",$vo["add_time"])); ?>
                        </div>
                        <div>
                            联合养殖
                        </div>
                        <div class="zijin">
                            ¥<?php echo ($vo["borrow_money"]); ?>
                        </div>
                        <div class="cg" style="width: 15%;">
                            <?php if($vo["borrow_status"] == 0): ?>未发起
                            <?php else: ?>
                                已发起<?php endif; ?>
                           
                        </div>
                        <div class="xqing" style="width: 15%;">
                            <a href="<?php echo U('/invest/detail',array('id'=>$vo['id']));?>">查看详情</a>
                        </div>
                    </div><?php endforeach; endif; else: echo "" ;endif; ?>
                    
                </div>
                <!--分页-->
                <div class="aFye clearfix">
                    <p><?php echo ($pagebar); ?></p>
                </div>
            </div>
        </div>
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