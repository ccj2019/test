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
<script type="text/javascript" src="/Public/new/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="__PUBLIC__/web/js/cui-timer.js"></script>
<style type="text/css">
	.tfBox {
		width: 940px;
		margin: 50px auto;
		min-width: 940px;
	}
	body {
		background: #fff;
	}
	.tfDetailTopBox{
		overflow: hidden;
	}
	.tfDetailTopBoxLeftImgBox{
		width:400px;
		float: left;
	}
	.tfDetailTopBoxRightBox{
		width: 499px;
		float: right;
	}
	.tfDetailTopBoxLeftImgBoxImg{
		background-size: 100%;
		background-position: center;
		background-repeat: no-repeat;
	}
	.tfDetailTopBoxLeftImgBoxViewImg1{
		width: 400px;
		height: 400px;
	}
	.tfDetailTopBoxLeftImgBoxViewImg2{
		width: 50px;
		height: 50px;
	}
	.tfDetailTopBoxLeftImgBoxImgBBox{
		overflow: hidden;
		margin-top: 20px;
		padding-left: 25px;
		box-sizing: border-box;
	}
	.tfDetailTopBoxLeftImgBoxViewImg2{
		border: 1px solid #fff;
		margin-right: 11px;
		float: left;
	}
	.tfDetailTopBoxLeftImgBoxViewImg2.active{
		border: 1px solid #4969F2;
	}
	.tfDetailTopBoxRightBoxTitle1{
		font-size: 18px;
		line-height: 25px;
		height: 25px;
		color: #333333;
	}
	.tfDetailTopBoxRightBoxTitle2{
		margin-top: 10px;
		font-size: 14px;
		line-height: 20px;
		color: #999999;
	}
	.jindutiaoBox{
		background-color: #f1f1f1;
		background-image: url('data:image/png;base64,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');
		width: 100%;
		height: 60px;
		background-size: 100% 100%;
		margin-top: 20px;
		padding: 10px;
		box-sizing: border-box;
	}
	.yishengBox{
		padding: 0 10px;border-radius: 50px;background-color: rgba(73, 105, 242, 0.41);color: #fff;
	}
	.jindutiao{
		width: 146px;
		height: 6px;
		border-radius: 50px;
		overflow: hidden;
		background-color: #fff;
		margin-right: 12px;
	}
	.jindutiaonei{
		height: 6px;
		width: 50%;
		background: linear-gradient(321.69deg, #4481EB 0%, #04BEFE 100%);
		border-radius: 10px;
	}
	.daojishiBox{
		font-size: 14px;
		line-height: 20px;
		color: #4969F2;
		margin-right: 30px;
	}
	.daojishiBox>span{
		background: linear-gradient(321.69deg, #4481EB 0%, #04BEFE 100%);
		border-radius: 2px;
		color: #fff;
		margin: 0 2px;
		padding: 0 2px;
	}
	.tfDetailTopBoxRightBoxTitle3{
		margin-top: 21px;
		left: 22px;
	}
	.yujishuangheng{
		margin-top: 21px;
		height: 50px;
		line-height: 50px;
		box-sizing: border-box;
		font-size: 14px;
		color: #939494;
		border-top: 1px solid #EEEEEE;
		border-bottom: 1px solid #EEEEEE;
	}
	.yujishuangheng>div{
		margin-left: 60px;
		float: left;
	}
	.tfDetailTopBoxRightBoxTitle4Box{
		font-size: 14px;
		line-height: 25px;
		letter-spacing: -0.408px;
		color: #939494;
		margin-top: 41px;
	}
	.tfDetailTopBoxRightBoxTitle4Box span{
		padding: 0 10px;
		color: #969696;
		border: 1px solid #939494;
		box-sizing: border-box;
		margin-left: 10px;
		margin-right: 2px;
	}
	.lijigoumai{
		width: 230px;
		height: 50px;
		line-height: 50px;
		text-align: center;
		color: #fff;
		font-size: 18px;
		letter-spacing: -0.408px;
		background: linear-gradient(321.69deg, #4481EB 0%, #04BEFE 100%);
		margin-top: 37px;
		cursor: pointer;
		-moz-user-select:none; /*火狐*/
		-webkit-user-select:none; /*webkit浏览器*/
		-ms-user-select:none; /*IE10*/
		-khtml-user-select:none; /*早期浏览器*/
		user-select:none;
	}
	.lijigoumai:hover{
		background: linear-gradient(321.69deg, #04BEFE 0%, #4481EB 100%);
	}
	.tfBottomBox{
		width: 100%;
		border: 1px solid #C4C4C4;
		box-sizing: border-box;
		margin-top: 67px;
	}
	.tfBottomBoxMenuBox{
		height: 50px;
		display: flex;
		align-items: center;
	}
	.tfBottomBoxMenuBox>div{
		width: 110px;
		line-height: 50px;
		position: relative;
		padding-left: 24px;
		box-sizing: border-box;
		margin-right: 60px;
		cursor: pointer;
	}
	.tfBottomBoxMenuBox>div::before{
		content: '';
		height: 18px;
		width: 1px;
		background-color: #000;
		position: absolute;
		top: 50%;
		right: -20px;
		transform: translateY(-50%);
	}
	.tfBottomBoxMenuBox>div.active{
		color: #4969F2;
	}
	.tfBottomBoxMenuBox>div.active::after{
		content: '';
		position: absolute;
		bottom: 0;
		top: auto;
		left: 0;
		width: 100%;
		height: 2px;
		background-color: #4969F2;
		transform: translateY(0);
	}
	.tfNeirongBox{
		display: none;
		overflow: hidden;
	}
	.l-Xmzchi{
		width: 210px;
		height: 80px;
		background: #FFFFFF;
		border: 1px solid #DADADA;
		box-sizing: border-box;
		border-radius: 4px;
		margin-right: 8px;
		font-size: 12px;
		padding: 8px;
		line-height: 20px;
	}
	.l-Xmzchi img{
		width: 60px;
		height: 60px;
		border-radius: 50%;
		margin-right: 9px;
	}
	.tfNeirongBox img{
		max-width:100%
	}
</style>
<?php $borrow_img=explode(',',$binfo['content_img'])?>
<div class="tfBox">
	<div class="tfDetailTopBox">
		<div class="tfDetailTopBoxLeftImgBox">
			<div class="tfDetailTopBoxLeftImgBoxImg  tfDetailTopBoxLeftImgBoxViewImg1" style="background-image: url(<?php echo ($borrow_img[0]); ?>);"></div>
			<div class="tfDetailTopBoxLeftImgBoxImgBBox">
				<?php if(is_array($borrow_img)): $key = 0; $__LIST__ = $borrow_img;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($key % 2 );++$key;?><div class="tfDetailTopBoxLeftImgBoxImg  tfDetailTopBoxLeftImgBoxViewImg2 <?php echo $key == 1?'active':''; ?>" style="background-image: url(<?php echo ($vo); ?>);"></div><?php endforeach; endif; else: echo "" ;endif; ?>
			</div>
		</div>
		<div class="tfDetailTopBoxRightBox">
			<div class="tfDetailTopBoxRightBoxTitle1"><?php echo ($binfo["borrow_name"]); ?></div>
			<div class="tfDetailTopBoxRightBoxTitle2">周期 <?php echo ($binfo["borrow_duration"]); ?> 个月</div>
			<div class="jindutiaoBox">
				<div style="color: #333;font-size: 14px;">
					<?php if($binfo["borrow_status"] == 2): ?>正在热销中
					<span class="yishengBox">还剩<?php echo ($binfo["fenshu"]); ?>份</span>
					<span style="color: #4969F2;float: right;margin-right: 56px;">距结束仅剩</span>

					<?php else: ?>
						<span class="yishengBox">还剩<?php echo ($binfo["fenshu"]); ?>份</span><?php endif; ?>

				</div>
				<div style="display: flex;align-items: center;color: #4969F2;font-size: 14px;margin-top: 5px;justify-content: space-between;">
					<div class="jindutiao">
						<div class="jindutiaonei" style="width: <?php echo ($binfo["progress"]); ?>%;"></div>
					</div>
					<div style="flex: 1;">已购<?php echo ($binfo["progress"]); ?>%</div>
					<div class="daojishiBox">
						<!-- 1天<span>23</span>时<span>49</span>分<span>01</span>秒 -->
						<?php if($binfo["borrow_status"] == 2): ?><span class="cui-timer" data-config = '["endtime":"<?php echo (date("Y-m-d H:i:s",$binfo["endtimes"])); ?>","msec":false]'></span>
						<?php else: ?>
							<?php echo ($binfo["zhuangtai"]); endif; ?>


					</div>
				</div>
			</div>
			<div class="tfDetailTopBoxRightBoxTitle3">
				价格：<span style="color: rgb(255,26,26);">￥<font style="font-size: 22px;"><?php echo ($binfo["borrow_min"]); ?></font> <span style="font-size: 12px;">认购价</span></span>
			</div>
			<div class="yujishuangheng">
				<div>预计销售价：<font style="color: rgb(255,154,20);">￥<?php echo ($binfo["borrow_min"]); ?>/份</font></div>
				<div>预计收购时间：<font style="color: rgb(255,154,20);"><?php echo (date("Y-m-d",$binfo["sg_time"])); ?> </font></div>
			</div>
			<?php if($binfo["is_huodong"] == 1): ?><div class="yujishuangheng" style="border-top: none;margin-top: 0px;">
					<div>赠品：<?php echo ($binfo["zpname"]); ?>,认购满<?php echo ($binfo["huodongnum"]); ?>元送一份 </div>
				</div><?php endif; ?>


			<div class="tfDetailTopBoxRightBoxTitle4Box">
				预计总份数<span><?php echo ($binfo["zongfenshu"]); ?></span>份
			</div>
<?php if($binfo["borrow_status"] == 2): ?><a href="/invest/is_order?id=<?php echo ($binfo["id"]); ?>&money=<?php echo ($binfo["borrow_min"]); ?>"><div class="lijigoumai">立即购买</div></a><?php endif; ?>
			<?php if($binfo["borrow_status"] == 6): ?><a href="javascript:;"><div class="lijigoumai">认购结束</div></a><?php endif; ?>


		</div>
	</div>
	<div class="tfBottomBox">
		<div class="tfBottomBoxMenuBox">
			<div class="active">商品详情</div>
			<div>购买明细</div>
			<div>商品动态</div>
			<div>订购合同</div>
		</div>
		<div style="padding: 20px;box-sizing: border-box;display: block;" class="tfNeirongBox">
			<?php echo ($binfo["borrow_info"]); ?>
		</div>

		<div style="padding: 20px;box-sizing: border-box;" class="tfNeirongBox">
			<div style="padding: 20px 0 20px 15px;background: #F8F8F8;overflow: hidden;">
				<?php if(is_array($investinfo)): $i = 0; $__LIST__ = $investinfo;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vi): $mod = ($i % 2 );++$i;?><div class="l-Xmzchi">
						<img src="<?php echo (($vi["user_img"])?($vi["user_img"]):'/public/wap/img/w-car-con.jpg'); ?>" alt="" />
						<div>
							<div>
								<?php echo (mb_substr($vi["user_name"],0,1,'utf-8')); ?>***
							</div>
							<div style="color: #999999;"><span style="color: red;"><?php echo (fmoney($vi["investor_capital"])); ?></span></div>
							<div style="color: #999999;"> <span style="color: #000;">
								<?php echo (date("Y-m-d H:i:s",$vi["add_time"])); ?>
								<!--<?php echo (($vi['investor_capital']/$binfo['borrow_min'])?($vi['investor_capital']/$binfo['borrow_min']):'1'); ?>份-->

							</span></div>
						</div>
					</div><?php endforeach; endif; else: echo "" ;endif; ?>
			</div>
		</div>

		<div style="padding: 20px;box-sizing: border-box;" class="tfNeirongBox">


			<style type="text/css">
				.SlideIcon{
					margin-top: 25px;
					width: 750px;
				}
				.SlideIcon .swiper-slide{
					width: 100%;
					display: block;
					height: 100%;
				}
				.SlideIcon .swiper-slide a{
					display: block;
				}
				.SlideIcon .swiper-slide a img{
					width: 100%;
					display: block;
				}
			</style>


				<?php if(is_array($dynamiclist)): $i = 0; $__LIST__ = $dynamiclist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vb): $mod = ($i % 2 );++$i;?><div class="clearfix l-a1">
					<div class="l-Xmtime">
						<p><?php echo (date('Y-m-d',$vb["add_time"])); ?></p>
						<p><?php echo (date('H:i:s',$vb["add_time"])); ?></p>
						<?php if($vb["uid"] == $UID): ?><p><br>
								<a href="__URL__/delxmjd/id/<?php echo ($vb["id"]); ?>"  onClick="return confirm('确定删除?');" style="color:#00a0ea;">删除</a>
								<a href="__URL__/detailxg/id/<?php echo ($vb["id"]); ?>" style="color:#00a0ea;">修改</a>
							</p><?php endif; ?>
					</div>
					<div class="l-Xmut">
						<span class="l_Yuan"><img src="__PUBLIC__/web/img/xiangmu/quan1.png" alt="" /><?php echo ($vb["typename"]); ?></span>
						<p><?php echo ($vb["dycomment"]); ?></p>

						<?php if(!empty($vb['imgs'])){ ?>

						<div class="SlideIcon">
							<div class="swiper-container tz-gallery"  id="swiper1">
								<div class="swiper-wrapper" id="swiper-wrapper">

									<?php
 $volistimg=explode(",",$vb['imgs']); foreach($volistimg as $k=>$v){ ?>
									<div class="swiper-slide">
										<a href="<?php echo ($v); ?>">
											<img src="<?php echo ($v); ?>" class="ToImg" alt="Park">

										</a>
									</div>

									<?php }?>

								</div>
								<div class="swiper-button-prev"></div>

								<div class="swiper-button-next"></div>

							</div>


						</div>
						<?php }?>
						
						</div>
					</div><?php endforeach; endif; else: echo "" ;endif; ?>


				<script>
					baguetteBox.run('.tz-gallery');
					var mySwiper = new Swiper('.l-Xmut .SlideIcon .swiper-container', {

						observer:true,//修改swiper自己或子元素时，自动初始化swiper
						observeParents:true,
						navigation: {
							nextEl: '.l-Xmut .SlideIcon .swiper-button-next',
							prevEl: '.l-Xmut .SlideIcon .swiper-button-prev',
						},
						slidesPerView : 5,
						spaceBetween : 20,
					});
				</script>
				<?php if($binfo["borrow_uid"] == $UID): ?><!--增加弹窗-->
					<div class="clearfix l-a2">
						<div class="btn-Gxdt">
							更新动态
						</div>
					</div><?php endif; ?>

		</div>

		<div style="padding: 20px;box-sizing: border-box;" class="tfNeirongBox">
				<?php echo ($article_category["art_content"]); ?>
		</div>
		</div>
	</div>
	<script>
		var interval;
		var time = new Date('2020/12/20 10:10:10').getTime()*1 - new Date().getTime()*1;//结束时间
		// 鼠标经过切换大图
		$('.tfDetailTopBoxLeftImgBoxViewImg2').on('mouseover',function(){
			$('.tfDetailTopBoxLeftImgBoxViewImg2').removeClass('active').eq($(this).index()).addClass('active');
			$('.tfDetailTopBoxLeftImgBoxViewImg1')[0].style.backgroundImage=this.style.backgroundImage
		})
		//点击切换详情、明细、合同
		$('.tfBottomBoxMenuBox>div').on('click',function(){
			$('.tfNeirongBox').hide().eq($(this).index()).show();
			$('.tfBottomBoxMenuBox>div').removeClass('active').eq($(this).index()).addClass('active')
		})
		// 倒计时转换
		function getDuration(my_time) {  
		  var days    = my_time / 1000 / 60 / 60 / 24;
		  var daysRound = Math.floor(days);
		  var hours = my_time / 1000 / 60 / 60 - (24 * daysRound);
		  var hoursRound = Math.floor(hours);
		  var minutes = my_time / 1000 / 60 - (24 * 60 * daysRound) - (60 * hoursRound);
		  var minutesRound = Math.floor(minutes);
		  var seconds = my_time / 1000 - (24 * 60 * 60 * daysRound) - (60 * 60 * hoursRound) - (60 * minutesRound);
		  return [
			  parseInt(daysRound)>=0?parseInt(daysRound):0,
			  parseInt(hoursRound)>=0?parseInt(hoursRound):0,
			  parseInt(minutesRound)>=0?parseInt(minutesRound):0,
			  parseInt(seconds)>=0?parseInt(seconds):0
		  ];
		}
		// function daojishi(e){
		// 	interval = setInterval(function(){
		// 		var arr = getDuration(time);
		// 		$('.daojishiBox').html(arr[0]+'天<span>'+(arr[1]>=10?arr[1]:'0'+arr[1])+'</span>时<span>'+(arr[2]>=10?arr[2]:'0'+arr[2])+'</span>分<span>'+(arr[3]>=10?arr[3]:'0'+arr[3])+'</span>秒');
		// 		time-=1000
		// 	},1000)
		// }
		// daojishi(time);
	</script>
</div>
<script type="text/javascript">
	$(function(){
		$('.cui-timer').Cuitimer();
	});
</script>
<div class="dome-1" style="background:#fff; z-index: 9999;">
	<form action="" method="get">
		<div class="dome-2">
			<div class="dome-2bt">
				<span>更新动态 </span><img src="__PUBLIC__/web/img/xiangmu/guanb.png" class="l-hide"/>
			</div>
			<div class="dome-3">
				<p>
					<span>动态内容:</span><textarea name="dycomment" id="dycomment" rows="" cols=""></textarea>
				</p>
				<div>
					<span style="float: left;">动态图片：</span><div  id="btn1"style="background: url(/Public/web/img/l-tupianshangc.png) center center / 100% 100% no-repeat;
    background-size: 100% 100%;   width: 60px;
    height: 60px;
    resize: none;
    outline: none;
    border: 1px solid #DEDEDE;
    border-radius: 5px;float: left;margin-left: 350px;"></div>
					<div id="ul_pics1" style="clear:left"></div>
					<div  style="clear:left"></div>
				</div>


				<p>
					<span>项目阶段:</span>
					<select name="typename" id="typename">
						<option value="货款发放" selected="selected">货款发放</option>
						<option value="产品动态">产品动态</option>
						<option value="商户回购">商户回购</option>
						<option value="交易完成">交易完成</option>
					</select>
				</p>
				<div style="margin: 0 auto; display: table; padding-top: 30px;">
					<div class="btn-Gxdt1" onclick="subdynamic()">保存</div>
					<div class="btn-Gxdt2">取消</div>
				</div>
			</div>
		</div>
	</form>
</div>
<script type="text/javascript" src="__PUBLIC__/web/js/plupload/plupload.full.min.js"></script>
<script type="text/javascript">


	var uploader2 = new plupload.Uploader({
		runtimes: 'html5,flash,silverlight,html4',
		browse_button: 'btn1',
		url:  "/Borrow/zhao",
		flash_swf_url: '__PUBLIC__/web/js/plupload/Moxie.swf',
		silverlight_xap_url: '__PUBLIC__/web/js/plupload/Moxie.xap',
		filters: {
			max_file_size: '5000kb',
		},
		multi_selection: false,
		init: {
			FilesAdded: function(up, files) {


				var li = '';
				plupload.each(files, function(file) {
					li += "<div id='b2" + file['id'] + "' style='float:left;padding-left: 10px;padding-right: 10px;'></div>";
				});
				$("#ul_pics1").append(li);
				uploader2.start();




			},
			UploadProgress: function(up, file) {
				layer.load();
			},
			FileUploaded: function(up, file, info) {
				layer.closeAll();
				var data = eval("(" + info.response + ")");
				if (data.error == 1) {
					layer.msg('上传图片过大或图片格式不对,请重新上传5M以内,jpg、png格式的图片!',{
						time:2000,
						end:function(){
							//$(".dome-1").hide();
							//  location.reload();
						}
					});
				} else {
					$("#b2" + file.id).css('width','100px').html("<img height='100' width='100' src='" + data.pic + "'/><input name='imgs[]' class='imgs' type='hidden' value='" + data.pic + "'/>");
				}
			},
			Error: function(up, err) {
				layer.closeAll();
				layer.msg('上传图片过大或图片格式不对,请重新上传5M以内,jpg、png格式的图片!',{
					time:2000,
					end:function(){
						//$(".dome-1").hide();
						// location.reload();
					}
				});
			}
		}
	});
	uploader2.init();
</script>
<script type="text/javascript">

	function subcomment(){

		//2.通过jq获得input数组
		var $inputArr = $('.imgs');//length = 3
		//3.循环处理input,并定义结果集
		var result = [];
		var srt = '';
		$inputArr.each(function(){
			//4.将每个input的值放进结果集
			result.push($(this).val());
			srt+=$(this).val()+',';
		});

		//5.打印结果
		console.log(srt);

		var comment = $("#comment").val();
		var borrow_id = <?php echo ($binfo["id"]); ?>;
		if(comment == ''){
			layer.msg('评论内容不能为空');
			return false;
		}
		$.ajax({
			url: "__URL__/addcomment",
			data: {"comment":comment,"tid":borrow_id},
			timeout: 5000,
			cache: false,
			type: "post",
			dataType: "json",
			success: function (d, s, r) {
				if(d){
					if(d.status==1){
						layer.alert('发表成功', 1,!1);
					}else{
						layer.msg(d.message);
					}
				}
			}
		});
	}
	function subdynamic(){
		var $inputArr = $('.imgs');//length = 3
		//3.循环处理input,并定义结果集
		var result = [];
		var srt = '';
		var srtzz=1
		$inputArr.each(function(){
			//4.将每个input的值放进结果集
			result.push($(this).val());
			if(srtzz==1){
				srt+=$(this).val();
			}else{
				srt+=','+$(this).val();
			}

			srtzz++
		});

		var dycomment = $("#dycomment").val();
		var typename = $("#typename").val();
		var borrow_id = <?php echo ($binfo["id"]); ?>;
		if(dycomment == ''){
			layer.msg('动态内容不能为空');
			return false;
		}
		$.ajax({
			url: "__URL__/adddynamic",
			data: {"dycomment":dycomment,"tid":borrow_id,"typename":typename,imgs:srt},
			timeout: 5000,
			cache: false,
			type: "post",
			dataType: "json",
			success: function (d, s, r) {
				if(d){
					if(d.status==1){
						layer.alert('更新成功',4,function(){
							window.location.reload();
						});
					}else{
						layer.msg(d.message);
					}
				}
			}
		});
	}
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