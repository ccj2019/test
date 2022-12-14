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
  <div class="page_tit">红包规则</div>
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
   <a class="btn_a" href="__URL__/rules_add"><span>添加规则</span></a>
  </div>
  
  <div class="list">
  <table id="area_list" width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>    
    <th class="line_l">ID</th>
    <th class="line_l">规则类型</th>
    <th class="line_l">红包金额</th>  
    <th class="line_l">积分</th> 
    <th class="line_l">使用规则</th>
    <th class="line_l">有效天数</th>
    <th class="line_l">发放规则</th>
    <th class="line_l">规则有效（起）</th>
    <th class="line_l">规则失效（止）</th>        
    <th class="line_l">操作</th>
  </tr>
  <?php if(is_array($rList)): $i = 0; $__LIST__ = $rList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr overstyle='on' id="list_<?php echo ($vo["id"]); ?>">        
        <td><?php echo ($vo["id"]); ?></td>
        <td><?php if($vo["type"] == 3): ?>投资红包<?php elseif($vo["type"] == 1): ?>签到<?php elseif($vo["type"] == 4): ?>全部发放规则<?php else: ?>注册红包<?php endif; ?></td>            
        <td><?php if($vo["money_type"] == 2): echo ($vo["money_bonus"]); endif; ?></td>
        <td><?php if($vo["money_type"] == 1): echo ($vo["money_bonus"]); endif; ?></td>
        <td><?php if($vo["money_type"] != 1): echo ($vo["money_bonus"]); ?>最低投资<font color="#009900"><?php echo ($vo["bonus_invest_min"]); ?></font>元可用<?php else: ?>请在积分商城兑换<?php endif; ?></td>                
        <td><?php if($vo["money_type"] != 1): echo ($vo["expired_day"]); else: ?>长期有效<?php endif; ?></td>
        <td><?php if($vo["type"] == 3): ?>投资<font color="#009900"><?php echo ($vo["invest_money"]); ?></font>元以上<?php elseif($vo["type"] == 1): ?>签到<?php echo intval($vo['invest_money']);?>天<?php elseif($vo["type"] == 4): ?>全部发放规则<?php else: ?>注册成功后<?php endif; ?></td>            
        <td><?php echo (date("Y-m-d H:i:s",$vo["start_time"])); ?></td>
        <td><?php echo (date("Y-m-d H:i:s",$vo["end_time"])); ?></td>                
        <td>    
        	<?php if($vo["type"] == 4): ?><a href="<?php echo U('rules_edit');?>?id=<?php echo ($vo["id"]); ?>&rules_addpubBonus=1">全部发放</a> |<?php endif; ?>
          <a href="<?php echo U('rules_edit');?>?id=<?php echo ($vo["id"]); ?>">修改</a> |       
          <a href="<?php echo U('rules_del');?>?id=<?php echo ($vo["id"]); ?>">删除</a>
        </td>
      </tr><?php endforeach; endif; else: echo "" ;endif; ?>
  </table>

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