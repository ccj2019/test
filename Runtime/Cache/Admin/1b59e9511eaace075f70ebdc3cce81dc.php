<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?php echo ($ts['site']['site_name']); ?>管理后台</title>
<link rel="shortcut icon" href="__PUBLIC__/images/favicon.ico" />
<link rel="bookmark"href="__PUBLIC__/images/favicon.ico" />
<link href="__ROOT__/Style/A/css/style.css" rel="stylesheet" type="text/css">
<link href="__ROOT__/Style/A/js/tbox/box.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="__ROOT__/Style/A/js/jquery.js"></script>
<script type="text/javascript" src="__ROOT__/Style/A/js/common.js"></script>
<script type="text/javascript" src="__ROOT__/Style/A/js/tbox/box.js"></script>
</head>
<body>
<script type="text/javascript" src="__ROOT__/Style/My97DatePicker/WdatePicker.js" language="javascript"></script>
<style type="text/css">
.tip{color:#F2F4F6}
</style>
<div class="so_main">
  <!--列表模块-->
  <form name="sdf" id="sdf" action="__URL__/index" method="get">
  <div class="Toolbar_inbox">
  	<span>从<input onclick="WdatePicker({maxDate:'#F{$dp.$D(\'end_time\')||\'2025-10-01\'}',startDate:'%y-%M-%d 00:00:00',dateFmt:'yyyy-MM-dd HH:mm:ss',alwaysUseStartDate:true});" name="start_time" id="start_time"  class="input Wdate" type="text" value="<?php echo (mydate('Y-m-d H:i:s',$search["start_time"])); ?>"><span id="tip_start_time" class="tip">只选开始时间则查询从开始时间往后所有</span>到<input onclick="WdatePicker({minDate:'#F{$dp.$D(\'start_time\')}',maxDate:'2025-10-01',startDate:'%y-%M-%d 23:59:59',dateFmt:'yyyy-MM-dd HH:mm:ss',alwaysUseStartDate:true});" name="end_time" id="end_time"  class="input Wdate" type="text" value="<?php echo (mydate('Y-m-d H:i:s',$search["end_time"])); ?>"><span id="tip_end_time" class="tip">只选结束时间则查询从结束时间往前所有</span></span>
    <a href="javascript:;" onclick="javascript:document.forms.sdf.submit();" class="btn_a"><span>统计</span></a></div>
</form>
<style type="text/css">
.ssx a{height:30px; line-height:30px}
.list td{border-right:1px solid #E3E6EB; width:30%}
.lx{width:100%; overflow:hidden; height:30px; line-height:30px}
.lx dt,.lx dd{float:left; width:40%}
.lx dt{text-align:right;}
.lx dd{text-align:left; text-indent:10px}
</style>
  <div class="list">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <th class="line_l">会员统计</th>
        <th class="line_l">资金进出统计</th>
        <th class="line_l">借款统计</th>
      </tr>
      <tr>
        <td valign="top">
        	<dl class="lx"><dt>会员总数:</dt><dd><?php echo ($list["mm"]["num"]); ?>人</dd></dl>
        	<dl class="lx"><dt>通过身份认证:</dt><dd><?php echo ($list["mm"]["ms_id"]); ?>人</dd></dl>
        	<dl class="lx"><dt>通过手机认证:</dt><dd><?php echo ($list["mm"]["ms_phone"]); ?>人</dd></dl>
            <dl class="lx"><dt>可用余额:</dt><dd><?php echo ($account_money); ?>元</dd></dl>
            <dl class="lx"><dt>可用鱼币:</dt><dd><?php echo ($yubi); ?>元</dd></dl>
            <dl class="lx"><dt>冻结余额:</dt><dd><?php echo ($money_freeze); ?>元</dd></dl>
            <dl class="lx"><dt>冻结鱼币:</dt><dd><?php echo ($yubi_freeze); ?>元</dd></dl>
        </td>
        <td valign="top">
        	<dl class="lx"><dt>网站充值:</dt><dd><?php echo (fmoney($list["10000"]["money"])); ?></dd></dl>
         	<dl class="lx"><dt>线上充值:</dt><dd><?php echo (fmoney($list["3"]["money"])); ?></dd></dl>
         	<dl class="lx"><dt>线下充值:</dt><dd><?php echo (fmoney($list["27"]["money"])); ?></dd></dl>
         	<dl class="lx"><dt>成功提现:</dt><dd><?php echo (fmoney($list["29"]["money"])); ?></dd></dl>
         	<dl class="lx"><dt>总计:</dt><dd><?php echo Fmoney($list['27']['money']+$list['3']['money']+$list['10000']['money']-$list['29']['money']);?></dd></dl>
        </td>
        <td valign="top">
         	<dl class="lx"><dt>成功借出总额:</dt><dd><?php echo (fmoney($list["17"]["money"])); ?></dd></dl>
         	<dl class="lx"><dt>已还款总额:</dt><dd><?php echo (fmoney($list["11"]["money"])); ?></dd></dl>
         	<dl class="lx"><dt>未还款总额:</dt><dd><?php echo Fmoney($list['17']['money']-$list['11']['money']);?></dd></dl>
         	<dl class="lx"><dt>冻结中的保证金总额:</dt><dd><?php echo Fmoney($list['19']['money']-$list['24']['money']);?></dd></dl>
        </td>
	</tr>
    <tr>
        <th class="line_l">逾期统计</th>
        <th class="line_l"><!--网站收益统计--></th>
        <th class="line_l">投资者收益统计</th>
    </tr>
    <tr>
        <td valign="top">
         	<dl class="lx"><dt>逾期已还款总额:</dt><dd><?php echo (fmoney($list['expired']['re_money'])); ?></dd></dl>
         	<dl class="lx"><dt>逾期未还款总额:</dt><dd><?php echo Fmoney($list['expired']['money']-$list['expired']['re_money']);?></dd></dl>
         	<dl class="lx"><dt>催收费总额:</dt><dd><?php echo (fmoney($list["31"]["money"])); ?></dd></dl>
         	<dl class="lx"><dt>逾期罚息总额:</dt><dd><?php echo (fmoney($list["30"]["money"])); ?></dd></dl>
        </td>
        <td valign="top">
         	<!--<dl class="lx"><dt>借入者成交管理费总额:</dt><dd><?php echo (fmoney($list["18"]["money"])); ?></dd></dl>

         	<dl class="lx"><dt>投资者成交管理费总额:</dt><dd><?php echo (fmoney($list["23"]["money"])); ?></dd></dl>

         	<dl class="lx"><dt>提现手续费总额:</dt><dd><?php echo (fmoney($list["tx"]["money"])); ?></dd></dl>

         	<dl class="lx"><dt>实名认证费用总额:</dt><dd><?php echo (fmoney($list["25"]["money"])); ?></dd></dl>

         	<dl class="lx"><dt>总计:</dt><dd>

            <?php echo Fmoney($list['18']['money']+$list['23']['money']+$list['tx']['money']+$list['14']['money']+$list['25']['money']+$list['22']['money']+$list['26']['money']);?>

            </dd></dl>-->
        </td>
        <td valign="top">
         	<dl class="lx"><dt>成功借款利息总额:</dt><dd><?php echo (fmoney($list["28"]["money"])); ?></dd></dl>
         	<dl class="lx"><dt>成功借款投标奖励总额:</dt><dd><?php echo (fmoney($list["21"]["money"])); ?></dd></dl>
         	<dl class="lx"><dt>邀请注册奖金总额:</dt><dd><?php echo (fmoney($list["13"]["money"])); ?></dd></dl>
         	<dl class="lx"><dt>线下充值奖励:</dt><dd><?php echo (fmoney($list["32"]["money"])); ?></dd></dl>
         	<dl class="lx"><dt>总计:</dt><dd><?php echo Fmoney($list['28']['money']+$list['21']['money']+$list['32']['money']);?>
</dd></dl>
        </td>
      </tr>
    </table>
  </div>
<!--<script type="text/javascript">
var _bdhmProtocol = (("https:" == document.location.protocol) ? " https://" : " http://");
document.write(unescape("%3Cscript src='" + _bdhmProtocol + "hm.baidu.com/h.js%3Ff708bea5a5b94473d52231dbe92e5017' type='text/javascript'%3E%3C/script%3E"));
</script>
-->
</div>
<script type="text/javascript" src="__ROOT__/Style/A/js/adminbase.js"></script>
</body>
</html>