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

	var delUrl = '__URL__/doDell';

	var addUrl = '__URL__/add';

	var addTitle = '添加分类';

</script>

<div class="so_main">

  <div class="page_tit">商城商品列表</div>



  <div class="Toolbar_inbox">

  	<div class="page right"><?php echo ($pagebar); ?></div>

    <a class="btn_a" href="__URL__/goodadd"><span>添加商品</span></a>

    <a onclick="del();" class="btn_a" href="javascript:void(0);"><span>删除商品</span></a>

<form name="sdf" id="sdf" action="__URL__/index" method="get" style="    display: inline;">类别
       <select name="typeid" class="select">
        <option value="">请选择</option>
              <?php if(is_array($typelist)): $i = 0; $__LIST__ = $typelist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["id"]); ?>" <?php if($vo["id"] == $typeid): ?>selected<?php endif; ?>><?php echo ($vo["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
      </select> 

<a href="javascript:;" onclick="javascript:document.forms.sdf.submit();" class="btn_a"><span>查询</span></a>
</form>



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

    <th class="line_l">类别</th>

    <th class="line_l">图片</th>

    <th class="line_l">原价</th>

    <th class="line_l">拼团优惠</th>

    <th class="line_l">佣金</th>

    <th class="line_l">是否上线</th>

    <th class="line_l">操作</th>

  </tr>

  <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr overstyle='on'   id="list_<?php echo ($vo["id"]); ?>">

        <td><input type="checkbox" name="checkbox" id="checkbox2" onclick="checkon(this)" value="<?php echo ($vo["id"]); ?>"></td>

        <td><?php echo ($vo["id"]); ?></td>

        <td><?php echo ($vo["title"]); ?></td>

        <td><?php echo ($vo["typename"]); ?></td>

        <td><img src="__ROOT__/<?php echo ($vo["art_img"]); ?>" width="30" height="30" /></td>

        <td><?php echo ($vo["yprice"]); ?></td>

        <td><?php echo ($vo["yhprice"]); ?></td>

        <td><?php echo ($vo["yongjin"]); ?></td>

        <td><?php if($vo["online"] == 1): ?>是 <?php else: ?> 否<?php endif; ?></td>

        <td>

            <a href="__URL__/goodedit?id=<?php echo ($vo['id']); ?>">编辑</a> <a href="javascript:void(0);" onclick="del(<?php echo ($vo['id']); ?>);">删除</a>  

        </td>

      </tr><?php endforeach; endif; else: echo "" ;endif; ?>

  </table>



  </div>

  

  <div class="Toolbar_inbox">

  	<div class="page right"><?php echo ($pagebar); ?></div>

    <a class="btn_a" href="__URL__/goodadd"><span>添加商品</span></a>

    <a onclick="del();" class="btn_a" href="javascript:void(0);"><span>删除商品</span></a>

  </div>

</div>





<script type="text/javascript" src="__ROOT__/Style/A/js/adminbase.js"></script>
</body>
</html>