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



<div class="gwc">
    <p class="gwc_1">核对订单</p>
    <div class="gwc_list">
        <div class="gwc_dz">
            <div class="sh_xx">收货人信息 <a href="/member/memberaddress">+新增收货地址</a></div>

            <ul id="admoren" style="display:none;">

            <li>
          

            </li>
            </ul> 


            <ul id="adlist">
<?php if(is_array($adlist)): $i = 0; $__LIST__ = $adlist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li data="<?php echo ($vo["id"]); ?>" name="<?php echo ($vo["name"]); ?>" address="<?php echo ($vo["address"]); ?>">
      <p class="sh_name"><?php echo ($vo["name"]); ?></p><span><?php echo ($vo["address"]); ?>&nbsp;&nbsp;<?php echo ($vo["main_phone"]); ?> </span></li><?php endforeach; endif; else: echo "" ;endif; ?>
            </ul>   <div class="clear"></div>
            <a href="##" class="shouqi">[收起地址∧]</a>

         </div>
        <div class="clear"></div>
        <div class="gwc_topd">
            确认订单信息 <a href="__URL__/car_list" style="float: right;
    font-size: 14px;
    color: #e45126;"><返回购物车修改</a>
        </div>
        <div class="clear"></div>
 <style type="text/css">
	.gwc_topsd,.gwc_listsd{
		border-bottom: solid 1px #e7e7e7;
	}
	.zz_order{
		border-bottom: none;
	}

</style>   
    <div class="zz_order">
            <div class="gwc_topsd">
                <span>&nbsp;</span>
                <span>商品</span>
                <span>单价</span>
                <span>数量</span>
                <span>金额</span>
          <div class="clear"></div>
            </div>

<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="gwc_listsd">
                <span><img src="<?php echo ($vo["art_img"]); ?>" /> </span>
                <span><?php echo ($vo["title"]); ?></span>
                <span id="dd_danjia">￥<?php echo ($vo["art_jiage"]); ?></span>
                <span class="zz_sl">
<?php echo ($vo["num"]); ?>

</span>
        <span id="dd_zongjias">￥<?php echo ($vo["art_zj"]); ?></span>
       
        <div class="clear"></div>
        </div><?php endforeach; endif; else: echo "" ;endif; ?>


       </div>
<!-- <form method="post"  name="post_order" id="post_order" action="__URL__/post_order"> -->
      <input type="hidden" id="address" name="address"/>
      <input type="hidden" id="carids" name="carids" value="<?php echo ($carids); ?>" />
      <input name="pay_way" type="hidden" id="pay_way" />
      <input name="jifen" type="hidden" id="jifen" value="0" />
      <input name="jine" type="hidden" id="jine" value="<?php echo ($zongjia); ?>"/>
<!-- </form> -->

      <div class="gwc_liuyan">
      	<span>给商家留言:</span>
		<textarea id="liuyan" name="liuyan" class="liuyan" maxlength="150"></textarea>
		<div class="clear"></div>
      </div>

<?php if($jsons["kyjifen"] > 0): ?><div class="gwc_jifen">
      	<p><input type="checkbox" id="jifenck" name="jifenck" onclick="checkon(this)">使用<?php echo ($jsons["shiyong"]); ?>个积分  
      		<span id="dixian"  zjf="<?php echo ($zjf); ?>" wjf="<?php echo ($zongjia); ?>" syjf="<?php echo ($jsons["shiyong"]); ?>">￥-<?php echo ($jsons["dixian"]); ?></span></p>
		(你有<?php echo ($jsons["kyjifen"]); ?>个积分，可用<?php echo ($jsons["kyjifen"]); ?>个)
		<div class="clear"></div>
      </div><?php endif; ?>
        
<!-- <div class="gwc_botd">

          

            优惠券：<select name="yhq" id="yhq" class="yhq">
              <option>1</option>
              <option>1</option>
              <option>2</option>
              <option>21</option>
            </select>

 </div> -->
  
  
    </div>
    
    
    <div class="dd_zf">
        
        <!--<div class="zffs" data="2">-->
            <!--<img src="/Public/new/images/dd_17.jpg" /> <p class="zf_fs"> 微信支付</p>-->
        <!--</div>-->
        <div class="zffs" data="1">
            <img src="/Public/new/images/dd_15.jpg" /> <p class="zf_fs">支付宝支付</p>
        </div>
        
        
        <span>选择支付方式:</span>
        
    </div>
    
    <div class="dd_xq">
        <p class="dd_xq_p">实付：<span id="dd_zongjia">￥<?php echo ($zongjia); ?></span></p>

        <p><span>寄送至：</span><label id="pd_address"><?php echo ($adlist[0]['address']); ?></label></p>
        <p><span>收货人：</span><label id="pd_ren"><?php echo ($adlist[0]['name']); ?></label></p>
        
        <a href="javascript:void(0);" onclick="tijiao()" id="zf_btn">立即支付</a>
    </div>
  
</div>


<script type="text/javascript">

function checkon(o){
	let dixian=$("#dixian").attr('zjf');
	let zongjia=$("#dixian").attr('wjf');
	let syjf=$("#dixian").attr('syjf');
	let zj;

	if($("#jifenck").prop("checked")==true){
		zj=dixian;
		$("#jifen").val(syjf);
	}else{
		zj=zongjia;
		$("#jifen").val(0);
	}
	
	$("#jine").val(zj);
	$("#dd_zongjia").html("￥"+zj);
}


function donum(num){
    var id=$("#id").val();
    $.ajax({
    url: "__URL__/do_car_num",
    type: "post",
    dataType: "json",
    data: {"num":num,"id":id},
    success: function(d) {
       $("#dd_zongjia").html("￥"+d);
       $("#dd_zongjias").html("￥"+d);
    }
  }); 
}
function tijiao(){
    let address=$("#address").val();
    let carids=$("#carids").val();
    let pay_way=$("#pay_way").val();
    let jifen=$("#jifen").val();
    let jine=$("#jine").val();
    let liuyan=$("#liuyan").val();
    $.ajax({
            url: "__URL__/post_order",
            type: "post",
            dataType: "json",
            data: {"address":address,"carids":carids,"pay_way":pay_way,"jifen":jifen,"jine":jine,"liuyan":liuyan},
            success: function(d) {
               if(d.status==1){
                    if(d.pay_way==1){
                        window.location.href="__URL__/zhifubao/id/"+d.ordernums;
                    }else if(d.pay_way==2){
                        window.location.href="__URL__/weixin/id/"+d.ordernums;
                    }else if(d.pay_way==3){
                        window.location.href="/member/shoporder";
                    }

               }else{
                    alert(d.msg);
               } 
            }
    });
}

$(function(){

    /*订单数量*/
    $(".kucun #jia").click(function(){
        var b=Number($(".kucun #shuliang").val());
        $(".kucun #shuliang").val(Number(b)+1)
        donum(Number(b)+1);
    })
    
    $(".kucun #jian").click(function(){
        var b=Number($(".kucun #shuliang").val());
        if(b>1){
            $(".kucun #shuliang").val(Number(b)-1);
            donum(Number(b)-1);
        }
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