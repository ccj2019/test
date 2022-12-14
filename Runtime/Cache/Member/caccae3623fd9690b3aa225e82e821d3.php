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
	<div class="box navls">
		<span><a href="<?php echo U('/member/index/index');?>">会员中心</a></span>
		<span>&gt;</span>
		<span><a href="<?php echo U('/member/index/borrowbreak');?>">项目收益</a></span>
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
				<div class="on"><span>发放收益</span></div>
			</div>
			<div class="tabv2box">
				<div class="tabv2 show">
					<div class="Ffsy">
						<div class="Ffsy-n">
							<h3><?php echo ($binfo["borrow_name"]); ?></h3>
							<h4>收益设置：</h4>	
							<!--收益选项-->
							<div class="FSy clearfix">
								<div class="FSy1">
									<div class="FSyn">收益年度：</div>
									<div>
										<select name="investyear" id="investyear">
											<option value="">请选择</option>
											<option value="2018">2018</option>
											<option value="2019">2019</option>
											<option value="2020">2020</option>
											<option value="2021">2021</option>
											<option value="2022">2022</option>
											<option value="2023">2023</option>
											<option value="2024">2024</option>
											<option value="2025">2025</option>
										</select>
									</div>
								</div>
								<div class="FSy1">
									<div class="FSyn">收益期数：</div>
									<div>
										<select name="sort_order" id="sort_order" >
											<option value="0">请选择</option>
											 <?php if(is_array($date)): $key = 0; $__LIST__ = $date;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vb): $mod = ($key % 2 );++$key;?><option value="<?php echo ($vb); ?>">第<?php echo ($vb); ?>期</option><?php endforeach; endif; else: echo "" ;endif; ?>
										</select>
									</div>
								</div>
								<div class="FSy1">
									<div class="FSyn">本期收益金额：</div>
									<div id="shouyi">0</div>
								</div>

								<div class="FSy1">
									<div class="FSyn">发放时间：</div>
									<div>
										<input type="date" name="starttime" id="starttime" /><span style="float: left; margin:0 10px;">-</span>
										<input type="date" name="endtime" id="endtime" />
										
									</div>
								</div>
								<div class="FSy1">
									<div class="FSyn">本期本金：   </div>
									<div id="benji">0</div>
								</div>
								<div class="FSy1">
									<div class="FSyn">销售差价：   </div>
									<div id="chajia">0</div>
								</div>

								<div class="clearfix">	</div>
								<button class="anNIOU" onclick="generate(<?php echo ($bid); ?>)">
									生成收益明细
								</button>
							</div>
								
							<!--介绍-->
							<div class="jSasp">
								<p>
								注：依据本次收益金额、投资人投资金额所占支持比例进行计算，自动计算投资人收益金额。
								</p>
								<p>
								算法：单个投资人每期分红金额=每期分红金额/（已筹总额*收益率）*投资金额*收益率=每期分红金额*（投资金&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 额/已筹总额）
								</p>
								<p>
								支持比例=投资金额/已筹总额
								</p>
								<p>
								平均年收益率=本期收益金额/已筹总额/收益周期（月数）*12
								</p>
								<p>
								<br/>
								</p>
							</div>
						</div>
						<div >
							<!--项目收益-->
							<div class="XmBhbox clearfix">
							  	<div class="touTz on">
							  	  	<span>期数</span>
							  	  	<span>收益金额</span>
									<span>销售差价</span>
							  	  	<span>周期</span>
							  	  	<span>总金额</span>
							  	  	<span>平均年收益率</span>
							  	  	<span>收益开始时间</span>
							  	  	<span>收益结束时间</span>
							  	  	<span>操作</span>
							  	</div>
							  	<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="touTz1 on">
								        <span><?php echo ($vo["sort_order"]); ?></span>
										<span><?php echo ($vo["interest"]); ?></span>
									    <span><?php echo ($vo["chajia"]); ?></span>
										<span><?php echo ($vo["borrow_duration"]); ?>个月</span>
										<span><?php echo ($vo["allmoney"]); ?></span>
										<span><?php echo ($vo["year_invest"]); ?></span>
										<span><?php echo (date("Y-m-d",$vo["starttime"])); ?></span>
										<span><?php echo (date("Y-m-d",$vo["endtime"])); ?></span>
										<span>
											<?php if($vo["is_repmeny"] == 0): ?><a href="__URL__/invest_detail_del?id=<?php echo ($vo["id"]); ?>">删除</a><?php endif; ?>
											<?php if($vo["is_repmeny"] == 1): if($vo["status"] == 1): ?>已还款
												<?php else: ?>
													待审核<?php endif; endif; ?>
										</span>
							  	</div><?php endforeach; endif; else: echo "" ;endif; ?>
								<div class="touTz1 on touzilog"></div>
							</div>
							<!--编号投资人-->
							<div class="XmBhbox clearfix showsy" style="display: none;">
								<div class="touTz on">
									<span>编号</span>
									<span>投资人</span>
									<span>回购金额</span>
									<span>收益</span>
									<span>差价</span>
									<span>本金</span>
									<span style="width: 23%;"> 最终收益（含本金,差价）</span>
								</div>
								<div class="huikuanmingxi"></div>
						    </div>
						</div>
			            <div  class="symios">
			               	<form action="__URL__/subgenerate" method="post" name="myform" id="myform">
				               	<h4>收益描述：</h4>
				               	<!--编辑器没有-->
				               	<textarea name="info" id="info" rows="" cols=""></textarea>
				               	<input type="hidden" name="borrow_id" id="borrow_id" />
				               	<input type="hidden" name="invest_orderid" id="invest_orderid" />
				               	<input type="hidden" name="capital" id="capital" />
				               	<input type="hidden" name="has_capital" id="has_capital" />
				               	<!--提交-->
				               	<div class="btnTjiao">
				               		<input type="button" onclick="sub()" value="提交审核" class="button" >
				               		<a onclick="subreg()">重置</a>
				               		
				               	</div>
			               	</form>
			            </div>
					</div>
					
				</div>
			</div>
		</div>
		<input type="hidden" id="bid" value="<?php echo ($binfo["id"]); ?>">
		<input type="hidden" id="shouyis" value="0">
		<input type="hidden" id="chajias" value="0">
	</div>
<!--底部-->

<script type="text/javascript">
	$("#sort_order").change(function() {
		var aa=$('#sort_order option:selected').val();
		if(aa!=0){
			//$("#shouyi").html('asdfasdf');
			let borrow_id=$("#bid").val();
			$.ajax({
				url: "__URL__/getshouyi",
				type: "post",
				dataType: "json",
				data: {"borrow_id":borrow_id,"qi":aa},
				success: function(d) {
					console.log(d);
					if(d.status == "1"){
						$("#shouyi").html(d.income);
						$("#shouyis").val(d.income);

						$("#benji").html(d.capital);
						$("#chajia").html(d.chajia);
						$("#chajias").val(d.chajia);
						return false;
					}else if(d.status == "0"){
						layer.msg(d.info,{icon: 1});
						return false;
					}
				}
			});
		}else{
			$("#shouyi").html(0);
			$("#shouyis").val(0);
			$("#benji").html(0);
			$("#chajia").html(0);
			$("#chajias").val(0);

		}
	});


	function subreg(){
		document.getElementById("myform").reset();
	}
	function generate(bid){
		var investyear = $("#investyear").val();
		var sort_order = $("#sort_order").val();
		//var income = $("#income").val();
		var income = $("#shouyis").val();
		var chajia = $("#chajias").val();

		//alert(income);return false;
		var starttime = $("#starttime").val();
		var endtime = $("#endtime").val();
		if($("#has_capitals").is(':checked')){
			var has_capital = 1
		}else{
			var has_capital=0
		}
		if (investyear=="") {
				alert('请选择收益年度！');
				return false;
			}
		if (sort_order=="") {
				alert('请选择收益期数！');
				return false;
			}
		if (income=="") {
				alert('请填写本期收益！');
				return false;
			}
		if (starttime=="") {
				alert('发放开始时间！');
				return false;
			}
		if (endtime=="") {
				alert('发放结算时间！');
				return false;
			}

		$.ajax({
				url: "__URL__/generate",
				type: "post",
				dataType: "json",
				data: {
					investyear:investyear,sort_order:sort_order,income:income,starttime:starttime ,endtime:endtime , has_capital:has_capital,bid:bid,chajia:chajia,
				},
				success: function(d) {
				    console.log(d);
					if (d.status == 1) {
						var htm = '';
						var strhtm = '';
						$('.showsy').show();
                        var datalog= d.datalog;
	                   	strhtm +='<span>'+datalog.sort_order+'</span>';
	                   	strhtm +='<span>'+datalog.income+'</span>';
	                   	strhtm +='<span>'+datalog.chajia+'</span>';
	                   	strhtm +='<span>'+datalog.borrow_duration+'个月</span>';
	                   	strhtm +='<span>'+datalog.capital+'</span>';
	                   	strhtm +='<span>'+datalog.year_invest+'</span>';
	                   	strhtm +='<span>'+datalog.starttime+'</span>';
	                   	strhtm +='<span>'+datalog.endtime+'</span>';
		                $.each(d.savedetaillog, function(index, val) {         
		                   	htm +='<div class="touTz1 on ">';           
							htm +='<span>'+val.nums+'</span>';
							htm +='<span>'+val.investor+'</span>';
							htm +='<span>'+val.capital+'元</span>';
							htm +='<span>'+val.invest+'元</span>';
							htm +='<span>'+val.chajia+'</span>';
							// htm +='<span>'+val.rate+'% </span>';
							htm +='<span>'+val.benjin+'元</span>';
							htm +='<span style="width: 23%;">'+val.allmoney+'元</span>';
							htm +='<a href="__URL__/invest_detail_del/id/'+val.id+'">删除</a>';
							htm +='</div>';
		                });
		                $("#borrow_id").val(bid);
		                $("#invest_orderid").val(sort_order);
		                $("#has_capital").val(has_capital);
		                $("#capital").val(datalog.capital);
		                $(".huikuanmingxi").append(htm);
		                $(".touzilog").append(strhtm);
					}
					else if (d.status == 0) {
						alert(d.info);
					}
				}
			});
	}
	function sub(){
		var info = $("#info").val();
		var borrow_id = $("#borrow_id").val();
		var invest_orderid = $("#invest_orderid").val();
		if(borrow_id == ''){
			alert('请先生成收益明细');
			return false;
		}
		if(invest_orderid == ''){
			alert('请先生成收益明细');
			return false;
		}
		if(info == ''){
			alert('填写收益描述');
			return false;
		}
		$("#myform").submit();
		return true;
	}
</script>
</body>
</html>