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
	var addTitle = '添加分类';
</script>
<div class="so_main">
  <div class="page_tit">会员兑换记录</div>
  <div class="Toolbar_inbox" style="display:">
  	<div class="page right"><?php echo ($pagebar); ?></div>
   <!--   <a onclick="del();" class="btn_a" href="javascript:void(0);"><span>删除订单</span></a> -->
   &nbsp;
  </div>
  <div class="list">
  <table id="area_list" width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <th style="width:30px;">
        <input type="checkbox" id="checkbox_handle" onclick="checkAll(this)" value="0">
        <label for="checkbox"></label>
    </th>
    <th class="line_l">ID</th>
    <th class="line_l">订单号</th>
    <th class="line_l">用户名</th>
    <th class="line_l">兑换日期</th>
    <th class="line_l">兑换物品</th>
    <th class="line_l">消耗金额</th>
    <th class="line_l">兑换数量</th>
    <th class="line_l">当前状态</th>
    <th class="line_l">操作</th>
  </tr>
  <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr overstyle='on' id="list_<?php echo ($vo["id"]); ?>">
        <td><input type="checkbox" name="checkbox" id="checkbox2" onclick="checkon(this)" value="<?php echo ($vo["id"]); ?>"></td>
        <td><?php echo ($vo["id"]); ?></td>
        <td><?php echo ($vo["ordernums"]); ?></td>
        <td><a onclick="loadUser(<?php echo ($vo["uid"]); ?>,'<?php echo ($vo["user_name"]); ?>')" href="javascript:void(0);"><?php echo ($vo["user_name"]); ?></a></td>
        <td><?php echo (date('Y年m月d日H时',$vo["add_time"])); ?></td>
        <td><?php echo ($vo["title"]); ?></td>
        <td>￥<?php echo ($vo["jine"]); ?>元</td>
        <td><?php echo ($vo["num"]); ?></td>
        <td><?php echo ($vo["zhuangtai"]); ?></td>
        <td><a href="__URL__/edit?id=<?php echo ($vo['oid']); ?>">详情</a><!--  <a href="javascript:void(0);" onclick="del(<?php echo ($vo['id']); ?>);">删除</a>  --> </td>
      </tr><?php endforeach; endif; else: echo "" ;endif; ?>
  </table>

  </div>
  
  <div class="Toolbar_inbox">
  	<div class="page right"><?php echo ($pagebar); ?></div>
<!--     
      <a onclick="del();" class="btn_a" href="javascript:void(0);"><span>删除订单</span></a> -->
         &nbsp;
  </div>
</div>


<script type="text/javascript" src="__ROOT__/Style/A/js/adminbase.js"></script>
</body>
</html>