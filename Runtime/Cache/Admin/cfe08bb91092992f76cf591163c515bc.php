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
	var addTitle = '添加分类';
</script>
<div class="so_main">
  <div class="page_tit">预售管理</div>

  <div class="Toolbar_inbox">
  	<div class="page right"><?php echo ($pagebar); ?></div>
    <a class="btn_a" href="<?php echo U('/admin/ysgood/add');?>"><span>添加预售</span></a>
    <a onclick="del();" class="btn_a" href="javascript:void(0);"><span>删除预售</span></a>
  </div>
  
  <div class="list">
  <table id="area_list" width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <th style="width:30px;">
        <input type="checkbox" id="checkbox_handle" onclick="checkAll(this)" value="0">
        <label for="checkbox"></label>
    </th>
    <th class="line_l">ID</th>
    <th class="line_l">名称</th>
    <th class="line_l">价格</th>
    <th class="line_l">周期</th>
    <th class="line_l">满赠</th>
    <th class="line_l">预售总分数</th>
    <th class="line_l">已售总分数</th>
    <th class="line_l">上线时间</th>
    <th class="line_l">预售详情</th>
    <th class="line_l">操作</th>
  </tr>
  <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr overstyle='on' id="list_<?php echo ($vo["id"]); ?>">
        <td><input type="checkbox" name="checkbox" id="checkbox2" onclick="checkon(this)" value="<?php echo ($vo["id"]); ?>"></td>
        <td><?php echo ($vo["id"]); ?></td>
        <td><?php echo ($vo["title"]); ?></td>
        <td><?php echo ($vo["price"]); ?></td>
        <td><?php echo ($vo["zhouqi"]); ?></td>
        <td><?php echo ($vo["manzeng"]); ?></td>
        <td><?php echo ($vo["ygfenshu"]); ?></td>
        <td><?php echo ($vo["fenshu"]); ?></td>

        <td><?php echo (date('Y-m-d H:i:s',$vo["sx_time"])); ?></td>
        <td>
          <a href="__APP__/Admin/ysorder/index/id/<?php echo ($vo['id']); ?>/gs/<?php echo ($vo["status"]); ?>">查看</a>
        </td>
        <td>
            <a href="__URL__/edit?id=<?php echo ($vo['id']); ?>">编辑</a> 
            <a href="javascript:void(0);" onclick="del(<?php echo ($vo['id']); ?>);">删除</a>  
        </td>
      </tr><?php endforeach; endif; else: echo "" ;endif; ?>
  </table>

  </div>
  
  <div class="Toolbar_inbox">
  	<div class="page right"><?php echo ($pagebar); ?></div>
    <a class="btn_a" href="<?php echo U('/admin/zengpin/add');?>"><span>添加预售</span></a>
    <a onclick="del();" class="btn_a" href="javascript:void(0);"><span>删除预售</span></a>
  </div>
</div>


<script type="text/javascript" src="__ROOT__/Style/A/js/adminbase.js"></script>
</body>
</html>