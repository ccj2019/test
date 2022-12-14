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

	var searchName = "搜索/筛选";

</script>

<div class="so_main">

  <div class="page_tit">投资列表</div>

<!--搜索/筛选会员-->

    <div id="search_div" style="display:none">

  	<div class="page_tit"><script type="text/javascript">document.write(searchName);</script> [ <a href="javascript:void(0);" onclick="dosearch();">隐藏</a> ]</div>

	

	<div class="form2">

	<form method="get" action="__URL__/index">


   <dl class="lineD">

      <dt>会员名：</dt>

      <dd>

        <input name="uname" style="width:190px" id="title" type="text" value="<?php echo ($search["uname"]); ?>">

        <span>不填则不限制</span>

      </dd>

    </dl>

    <dl class="lineD">

      <dt>真实姓名：</dt>

      <dd>

        <input name="realname" style="width:190px" id="title" type="text" value="<?php echo ($search["realname"]); ?>">

        <span>不填则不限制</span>

      </dd>

    </dl>


	<dl class="lineD"><dt>投标时间(开始)：</dt><dd><input onclick="WdatePicker({maxDate:'#F{$dp.$D(\'end_time\')||\'2023-10-01\'}',startDate:'%y-%M-%d 00:00:00',dateFmt:'yyyy-MM-dd HH:mm:ss',alwaysUseStartDate:true});" name="start_time" id="start_time"  class="input Wdate" type="text" value="<?php echo ($search["start_time"]); ?>"><span id="tip_start_time" class="tip">只选开始时间则查询从开始时间往后所有</span></dd></dl>

	<dl class="lineD"><dt>投标时间(结束)：</dt><dd><input onclick="WdatePicker({minDate:'#F{$dp.$D(\'start_time\')}',maxDate:'2023-10-01',startDate:'%y-%M-%d 23:59:59',dateFmt:'yyyy-MM-dd HH:mm:ss',alwaysUseStartDate:true});" name="end_time" id="end_time"  class="input Wdate" type="text" value="<?php echo ($search["end_time"]); ?>"><span id="tip_end_time" class="tip">只选结束时间则查询从结束时间往前所有</span></dd></dl>



    <div class="page_btm">

      <input type="submit" class="btn_b" value="确定" />

    </div>

	</form>

  </div>

  </div>

<!--搜索/筛选会员-->



  <div class="Toolbar_inbox">

  	<div class="page right"></div>

    <a onclick="dosearch();" class="btn_a" href="javascript:void(0);"><span class="search_action">搜索/筛选</span></a>

    <a class="btn_a" href="__URL__/export?<?php echo ($query); ?>"><span>将当前条件下数据导出为Excel</span></a>

  </div>

  

  <div class="list">

  <table id="area_list" width="100%" border="0" cellspacing="0" cellpadding="0">

  <tr>

    <!-- <th style="width:30px;">
    
        <input type="checkbox" id="checkbox_handle" onclick="checkAll(this)" value="0">
    
        <label for="checkbox"></label>
    
    </th> -->

    
    <th class="line_l">排名</th>
    <th class="line_l">投资账户</th>
    <th class="line_l">投资人真实姓名</th>
    <th class="line_l">投资金额</th>
  </tr>

      <?php if(is_array($InvestmentRanking)): foreach($InvestmentRanking as $key=>$vo): ?><tr overstyle='on' id="list_<?php echo ($key); ?>">
    <!--     <td><input type="checkbox" name="checkbox" id="checkbox2" onclick="checkon(this)" value=""></td> -->
    
        <td class="line_l"><?php echo $key+1; ?></td>
        <td class="line_l"><?php echo ($vo["user_name"]); ?></td>
        <td class="line_l"><?php echo ($vo["real_name"]); ?></td>
        <td class="line_l"><?php echo ($vo["moeny"]); ?></td>
      
      </tr><?php endforeach; endif; ?>

  </table>



  </div>

  

  <div class="Toolbar_inbox">

  	<div class="page right"><?php echo ($pagebar); ?></div>

    <a onclick="dosearch();" class="btn_a" href="javascript:void(0);"><span class="search_action">搜索/筛选会员</span></a>

    <a class="btn_a" href="__URL__/export?<?php echo ($query); ?>"><span>将当前条件下数据导出为Excel</span></a>

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