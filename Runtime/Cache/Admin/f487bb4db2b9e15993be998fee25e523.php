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

  <div class="page_tit">商城商品列表</div>



  <div class="Toolbar_inbox">

  	<div class="page right"><?php echo ($pagebar); ?></div>

    <a class="btn_a" href="__URL__/add"><span>添加商品</span></a>

    <a onclick="del();" class="btn_a" href="javascript:void(0);"><span>删除商品</span></a>

  </div>

  

  <div class="list">

  <table id="area_list" width="100%" border="0" cellspacing="0" cellpadding="0">

  <tr>

    <th style="width:30px;">

        <input type="checkbox" id="checkbox_handle" onclick="checkAll(this)" value="0">

        <label for="checkbox"></label>

    </th>

    <th class="line_l">商品ID</th>

    <th class="line_l">商品名</th>

   <th class="line_l">所属栏目</th>

    <th class="line_l">图片</th>

    <th class="line_l">实际价格</th>

    <th class="line_l">需要积分</th>

    <th class="line_l">编辑时间</th>

    <th class="line_l">是否上线</th>

    <th class="line_l">操作</th>

  </tr>

  <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr overstyle='on'   id="list_<?php echo ($vo["id"]); ?>">

        <td><input type="checkbox" name="checkbox" id="checkbox2" onclick="checkon(this)" value="<?php echo ($vo["id"]); ?>"></td>

        <td><?php echo ($vo["id"]); ?></td>

        <td><?php echo ($vo["title"]); ?></td>

        <td><?php echo ($vo["type_name"]); ?></td>

        <td><img src="<?php echo ($vo["art_img"]); ?>" width="30" height="30" /></td>

        <td><?php echo ($vo["art_jiage"]); ?>元</td>

        <td><?php echo ($vo["art_jifen"]); ?></td>

        <td><?php echo (date('Y年m月d日H时',$vo["art_time"])); ?></td>

        <td><?php if($vo["art_set"] == 0): ?>是 <?php else: ?> 否<?php endif; ?></td>

        <td>

            <a href="__URL__/edit?id=<?php echo ($vo['id']); ?>">编辑</a> <a href="javascript:void(0);" onclick="del(<?php echo ($vo['id']); ?>);">删除</a>  

        </td>

      </tr><?php endforeach; endif; else: echo "" ;endif; ?>

  </table>



  </div>

  

  <div class="Toolbar_inbox">

  	<div class="page right"><?php echo ($pagebar); ?></div>

    <a class="btn_a" href="__URL__/add"><span>添加商品</span></a>

    <a onclick="del();" class="btn_a" href="javascript:void(0);"><span>删除商品</span></a>

  </div>

</div>





<script type="text/javascript" src="__ROOT__/Style/A/js/adminbase.js"></script>
</body>
</html>