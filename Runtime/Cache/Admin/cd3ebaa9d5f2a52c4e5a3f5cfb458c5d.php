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
<script type="text/javascript">
	var delUrl = '__URL__/doDel';
	var addUrl = '__URL__/add';
	var editUrl = '__URL__/edit';
	var editTitle = '修改会员类型';
	var isSearchHidden = 1;
	var searchName = "搜索/筛选会员";
</script>
<div class="so_main">
  <div class="page_tit">红包列表</div>
<!--搜索/筛选会员-->
    <div id="search_div" style="display:none">
  	<div class="page_tit"><script type="text/javascript">document.write(searchName);</script> [ <a href="javascript:void(0);" onclick="dosearch();">隐藏</a> ]</div>
	
	<div class="form2">
	<form method="get" action="__URL__/index">
    <?php if($search["customer_id"] > 0): ?><input type="hidden" name="customer_id" value="<?php echo ($search["customer_id"]); ?>" /><?php endif; ?>
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

    <div class="page_btm">
      <input type="submit" class="btn_b" value="确定" />
    </div>
	</form>
  </div>
  </div>
<!--搜索/筛选会员-->

  <div class="Toolbar_inbox">
  	<div class="page right"><?php echo ($pagebar); ?></div>
    <a onclick="dosearch();" class="btn_a" href="javascript:void(0);"><span class="search_action">搜索/筛选</span></a>
  <!--  <a onclick="del();" class="btn_a" href="javascript:void(0);"><span>删除会员(谨慎操作)</span></a>-->
   <!-- <a class="btn_a" href="__URL__/export?<?php echo ($query); ?>"><span>将当前条件下数据导出为Excel</span></a> -->

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
    <th class="line_l">红包金额</th>
    <th class="line_l">最低投资金额</th>          
    <th class="line_l">有效时间（起）</th>
    <th class="line_l">有效时间（止）</th>    
    <th class="line_l">红包类型</th>
    <th class="line_l">当前状态</th>
    <th class="line_l">操作</th>
  </tr>
  <?php if(is_array($bonusList)): $i = 0; $__LIST__ = $bonusList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr overstyle='on' id="list_<?php echo ($vo["id"]); ?>">
        <td><input type="checkbox" name="checkbox" id="checkbox2" onclick="checkon(this)" value="<?php echo ($vo["id"]); ?>"></td>
        <td><?php echo ($vo["id"]); ?></td>
        <td><a onclick="loadUser(<?php echo ($vo["uid"]); ?>,'<?php echo ($vo["user_name"]); ?>')" href="javascript:void(0);"><?php echo ($vo["user_name"]); ?></a><?php if($vo['is_mark'] == 1): ?><font color="green">[马甲]</font><?php endif; ?></td>
        <td><?php echo (($vo["real_name"])?($vo["real_name"]):"&nbsp;"); ?></td>            
        <td><font color="#009900"><?php echo ($vo["money_bonus"]); ?></font></td> 
        <td><font color="#009900"><?php echo ($vo["bonus_invest_min"]); ?></font></td>                               
        <td><?php echo (date("Y-m-d H:i:s",$vo["start_time"])); ?></td>
        <td><?php echo (date("Y-m-d H:i:s",$vo["end_time"])); ?></td>        
        <td><?php echo ($typeArr[$vo['type']]); ?></td>         
        <td><?php echo ($statusArr[$vo['status']]); ?></td>         
        <td>
          <?php if($vo['status'] == 0): ?><a href="<?php echo U('edit');?>?id=<?php echo ($vo["id"]); ?>&status=1">[启用]</a>
          <?php elseif($vo['status'] == 1): ?> 
            <a href="<?php echo U('edit');?>?id=<?php echo ($vo["id"]); ?>&status=0">[停用]</a>
          <?php else: ?>
            --<?php endif; ?>
        </td>
      </tr><?php endforeach; endif; else: echo "" ;endif; ?>
  </table>

  </div>
  
  <div class="Toolbar_inbox">
  	<div class="page right"><?php echo ($pagebar); ?></div>
    <a onclick="dosearch();" class="btn_a" href="javascript:void(0);"><span class="search_action">搜索/筛选</span></a>
    <!--<a onclick="del();" class="btn_a" href="javascript:void(0);"><span>删除会员(谨慎操作)</span></a>-->
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