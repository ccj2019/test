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
.sel_fs{width:110px}  
  .search_member{margin: 0px 10px;display: inline-block;padding: 5px;cursor: pointer;border: 1px solid #ABCDEF;border-radius: 3px;}
</style>
<div class="so_main">
<div class="page_tit">发放加息券</div>
<div class="page_tab"><span data="tab_1" class="active">基本信息</span></div>
<div class="form2">
	<form method="post" action="<?php echo U('add');?>">	
	<div id="tab_1">
	<dl class="lineD"><dt>用户名：</dt><dd>
    	<!-- <input type="hidden" class="spec81" name="borrow_uid" id="borrow_uid" value="<?php echo ($vo["recommend_id"]); ?>" /><br> -->
    	<input type="hidden" class="spec81" name="borrow_uids" id="borrow_uids" value="" /><br>
    	<span id="borrow_usernames">
    	</span>
   
<br>
    	<input name="borrow_username" id="borrow_username"  class="input" type="text" value="<?php echo user_name($vo['recommend_id']);?>" >
    	
		<span class="search_member" onclick="showurl('<?php echo U('admin/members/ajaxsearch');?>','搜索会员');">搜索</span>
                (<span style="color:red" id="_day_lilv">请输入用户名，您也可以点击“搜索”查找会员</span>) 
    </dd></dl> 
	<dl class="lineD"><dt>红包金额：</dt><dd><input name="money_bonus" id="money_bonus"  class="input" type="text" value="100.00" ><span class="tip">元</span></dd></dl>

	<dl class="lineD">
                    <dt>红包发放数量：</dt>
                    <dd>
                        <input name="money_num" id="money_num"  class="input" type="text" value="1" >
                        <span class="tip"></span></dd>
                </dl>
                	
	<dl class="lineD"><dt>最低投资金额：</dt><dd><input name="bonus_invest_min" id="bonus_invest_min"  class="input" type="text" value="0.00" ><span class="tip">元</span></dd></dl>	

	  <dl class="lineD"><dt>有效时间(开始)：</dt><dd><input onclick="WdatePicker({maxDate:'#F{$dp.$D(\'end_time\')||\'2025-10-01\'}',startDate:'%y-%M-%d 00:00:00',dateFmt:'yyyy-MM-dd HH:mm:ss',alwaysUseStartDate:true});" name="start_time" id="start_time"  class="input Wdate" type="text" value="<?php echo (mydate('Y-m-d H:i:s',$search["start_time"])); ?>"><span id="tip_start_time" class="tip">只选开始时间则查询从开始时间往后所有</span></dd></dl>
	  <dl class="lineD"><dt>有效时间(结束)：</dt><dd><input onclick="WdatePicker({minDate:'#F{$dp.$D(\'start_time\')}',maxDate:'2025-10-01',startDate:'%y-%M-%d 23:59:59',dateFmt:'yyyy-MM-dd HH:mm:ss',alwaysUseStartDate:true});" name="end_time" id="end_time"  class="input Wdate" type="text" value="<?php echo (mydate('Y-m-d H:i:s',$search["end_time"])); ?>"><span id="tip_end_time" class="tip">只选结束时间则查询从结束时间往前所有</span></dd></dl>

	</div><!--tab1-->
	<div class="page_btm">
	  <input type="submit" class="btn_b" value="确定" />
	</div>
	</form>
</div>
<script type="text/javascript">
function showurl(url,Title){
  ui.box.load(url, {title:Title});
}
</script>
</div>
<script type="text/javascript" src="__ROOT__/Style/A/js/adminbase.js"></script>
</body>
</html>