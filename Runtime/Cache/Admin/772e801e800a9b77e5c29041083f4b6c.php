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
	<form method="post" id="form-add" action="<?php echo U('dohtadd');?>">
	<div id="tab_1">
	<dl class="lineD"><dt>用户名：</dt><dd>
    	<!-- <input type="hidden" class="spec81" name="borrow_uid" id="borrow_uid" value="<?php echo ($vo["recommend_id"]); ?>" /><br> -->
    	<input type="hidden" class="spec81" name="borrow_uids" id="borrow_uids" value="" />
		<input type="hidden" class="spec81" name="zpid" id="zpid" value="" />
		<br>
    	<span id="borrow_usernames">

    	</span>

<br>
    	<input id="borrow_username" readonly="readonly"/>
		<span class="search_member" onclick="showurl('<?php echo U('admin/members/ajaxsearch');?>','搜索会员');">搜索</span>
                (<span style="color:red" id="_day_lilv">点击“搜索”查找会员</span>)
    </dd></dl>

		<dl class="lineD"><dt>赠品名称：</dt><dd>

		<input id="zengping_name" readonly="readonly"/>
		<span class="search_member" onclick="showurl('<?php echo U('admin/zengpin/ajaxsearch');?>','搜索赠品');">搜索</span>

		</dd></dl>

	<dl class="lineD">
		<dt>发放数量：</dt><dd>
		<input name="allnum" id="allnum"  class="input" type="text" value="1" >
		<span class="tip"></span></dd>
	</dl>

		<dl class="lineD">
			<dt>发放原因：</dt><dd>
			<input name="marke" id="marke"  class="input" type="text" value="" >
			<span class="tip">
				例：五周年活动
			</span></dd>
		</dl>

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