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


<script type="text/javascript">

	var delUrl = '__URL__/doDel';

	var addUrl = '__URL__/add';

	var isSearchHidden = 1;

	var searchName = "搜索/筛选会员";

</script>

<div class="so_main">

  <div class="page_tit">会员充值详情</div>

<!--搜索/筛选会员-->

    <!-------- 搜索游戏 -------->

  <div id="search_div" style="display:none">

  	<div class="page_tit"><script type="text/javascript">document.write(searchName);</script> [ <a href="javascript:void(0);" onclick="dosearch();">隐藏</a> ]</div>

	

	<div class="form2">

	<form method="get" action="__URL__/charge">

    <?php if($search["uid"] > 0): ?><input type="hidden" name="uid" value="<?php echo ($search["uid"]); ?>" /><?php endif; ?>

    <dl class="lineD">

      <dt>会员名：</dt>

      <dd>

        <input name="uname" style="width:190px" id="title" type="text" value="<?php echo ($search["uname"]); ?>">

        <span>不填则不限制</span>

      </dd>

    </dl>
    <dl class="lineD">

      <dt>真实姓名名：</dt>

      <dd>

        <input name="rname" style="width:190px" id="title" type="text" value="<?php echo ($search["rname"]); ?>">

        <span>不填则不限制</span>

      </dd>

    </dl>
    <dl class="lineD">

      <dt>对帐订单号：</dt>

      <dd>

        <input name="tran_id" style="width:190px" id="title" type="text" value="<?php echo ($search["tran_id"]); ?>">

        <span>不填则不限制</span>

      </dd>

    </dl>

	

    <dl class="lineD">

      <dt>充值金额：</dt>

      <dd>

      <select name="bj" id="bj" style="width:80px"  class="c_select"><option value="">--请选择--</option><?php foreach($bj as $key=>$v){ if($search["bj"]==$key && $search["bj"]!=""){ ?><option value="<?php echo ($key); ?>" selected="selected"><?php echo ($v); ?></option><?php }else{ ?><option value="<?php echo ($key); ?>"><?php echo ($v); ?></option><?php }} ?></select>

      <input name="money" id="money" style="width:100px" class="input" type="text" value="<?php echo ($search["money"]); ?>" >

        <span>不填则不限制</span>

      </dd>

    </dl>

	

	<dl class="lineD"><dt>充值时间(开始)：</dt><dd><input onclick="WdatePicker({maxDate:'#F{$dp.$D(\'end_time\')||\'2025-10-01\'}',dateFmt:'yyyy-MM-dd HH:mm:ss',alwaysUseStartDate:true});" name="start_time" id="start_time"  class="input Wdate" type="text" value="<?php echo (mydate('Y-m-d H:i:s',$search["start_time"])); ?>"><span id="tip_start_time" class="tip">只选开始时间则查询从开始时间往后所有</span></dd></dl>

	<dl class="lineD"><dt>充值时间(结束)：</dt><dd><input onclick="WdatePicker({minDate:'#F{$dp.$D(\'start_time\')}',maxDate:'2025-10-01',dateFmt:'yyyy-MM-dd HH:mm:ss',alwaysUseStartDate:true});" name="end_time" id="end_time"  class="input Wdate" type="text" value="<?php echo (mydate('Y-m-d H:i:s',$search["end_time"])); ?>"><span id="tip_end_time" class="tip">只选结束时间则查询从结束时间往前所有</span></dd></dl>

	

    <dl class="lineD">

      <dt>充值状态：</dt>

      <dd>

      <select name="status" id="status" style="width:150px"  class="c_select"><option value="">--请选择--</option><?php foreach($status as $key=>$v){ if($search["status"]==$key && $search["status"]!=""){ ?><option value="<?php echo ($key); ?>" selected="selected"><?php echo ($v); ?></option><?php }else{ ?><option value="<?php echo ($key); ?>"><?php echo ($v); ?></option><?php }} ?></select>

        <span>不填则不限制</span>

      </dd>

    </dl>

	

    <dl class="lineD">

      <dt>充值方式：</dt>

      <dd>

      <select name="way" id="way" style="width:150px"  class="c_select"><option value="">--请选择--</option><?php foreach($way as $key=>$v){ if($search["way"]==$key && $search["way"]!=""){ ?><option value="<?php echo ($key); ?>" selected="selected"><?php echo ($v); ?></option><?php }else{ ?><option value="<?php echo ($key); ?>"><?php echo ($v); ?></option><?php }} ?></select>

      <select name="off_bank">

      <option value="">---请选择---</option>

      <?php if(is_array($off_bank)): foreach($off_bank as $key=>$vo): ?><option value="<?php echo ($vo["off_bank"]); ?>" <?php if($search['off_bank'] == $vo['off_bank']): ?>selected<?php endif; ?> ><?php echo ($vo["off_bank"]); ?></option><?php endforeach; endif; ?>

    </select>

        <span>不填则不限制</span>

      </dd>

    </dl>



    <div class="page_btm">

      <input type="submit" class="btn_b" value="确定" />

    </div>

	</form>

  </div>

  </div>

<!--搜索/筛选会员-->



  <div class="Toolbar_inbox">

  	<div class="page right"><?php echo ($pagebar); ?></div>

    <a onclick="dosearch();" class="btn_a" href="javascript:void(0);"><span class="search_action">搜索/筛选会员</span></a>

    <a class="btn_a" href="__URL__/chargeexport?<?php echo ($query); ?>"><span>将当前条件下数据导出为Excel</span></a>

  </div>

  

  <div class="list">

  <table id="area_list" width="100%" border="0" cellspacing="0" cellpadding="0">

  <tr>

    <th style="width:30px;">

        <input type="checkbox" id="checkbox_handle" onclick="checkAll(this)" value="0">

        <label for="checkbox"></label>

    </th>

    <th class="line_l">ID</th>

    <th class="line_l">用户名</th>

    <th class="line_l">真实姓名</th>

    <th class="line_l">充值金额</th>

    <th class="line_l">充值手续费</th>

    <th class="line_l">充值状态</th>

    <th class="line_l">对账订单号</th>

    <th class="line_l">充值方式</th>

    <th class="line_l">充值时间</th>


  </tr>

  <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr overstyle='on' id="list_<?php echo ($vo["id"]); ?>">

        <td><input type="checkbox" name="checkbox" id="checkbox2" onclick="checkon(this)" value="<?php echo ($vo["id"]); ?>"></td>

        <td><?php echo ($vo["id"]); ?></td>

        <td><?php echo ($vo["user_name"]); ?></td>

        <td><?php echo ($vo["real_name"]); ?></td>

        <td><?php echo (($vo["money"])?($vo["money"]):0); ?>元</td>

        <td><?php echo (($vo["fee"])?($vo["fee"]):0); ?>元</td>

        <td><?php echo ($status[$vo['status']]); ?></td>

        <td><?php echo (($vo["tran_id"])?($vo["tran_id"]):"无"); ?></td>

        <td><?php echo ($way[$vo['way']]); ?></td>

        <td><?php echo (date("Y-m-d H:i:s",$vo["add_time"])); ?></td>



      </tr><?php endforeach; endif; else: echo "" ;endif; ?>

  </table>



  </div>

  

  <div class="Toolbar_inbox">

  	<div class="page right"><?php echo ($pagebar); ?></div>

    <a onclick="dosearch();" class="btn_a" href="javascript:void(0);"><span class="search_action">搜索/筛选会员</span></a>

    <a class="btn_a" href="__URL__/chargeexport?<?php echo ($query); ?>"><span>将当前条件下数据导出为Excel</span></a>

  </div>

</div>

<script type="text/javascript">

function showurl(url,Title){

	ui.box.load(url, {title:Title});

}

</script>



<script type="text/javascript" src="__ROOT__/Style/A/js/adminbase.js"></script>
</body>
</html>