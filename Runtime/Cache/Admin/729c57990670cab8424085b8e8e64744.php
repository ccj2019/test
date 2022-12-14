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
    var isSearchHidden = 1;
</script>
<div class="so_main">
  <div class="page_tit">赠品管理</div>
  <!--搜索/筛选会员-->
  
  <div class="Toolbar_inbox">
  	<div class="page right"><?php echo ($pagebar); ?></div>
    &nbsp;
    <!--<a onclick="dosearch();" class="btn_a" href="javascript:void(0);"><span class="search_action">搜索/筛选</span></a>-->
    <!--<a class="btn_a" href="__URL__/export?<?php echo ($query); ?>"><span>将当前条件下数据导出为Excel</span></a>-->
  </div>
  
  <div class="list">
  <table id="area_list" width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <th style="width:30px;">
        <input type="checkbox" id="checkbox_handle" onclick="checkAll(this)" value="0">
        <label for="checkbox"></label>
    </th>
    <th class="line_l">ID</th>

    <th class="line_l">获取</th>
    <th class="line_l">客户</th>
    <th class="line_l">名称</th>
    <th class="line_l">总数量</th>
    <th class="line_l">待处理数量</th>

    <th class="line_l">类型</th>
    <th class="line_l">获取时间</th>

  </tr>
  <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr overstyle='on' id="list_<?php echo ($vo["id"]); ?>">
        <td><input type="checkbox" name="checkbox" id="checkbox2" onclick="checkon(this)" value="<?php echo ($vo["id"]); ?>"></td>
        <td><?php echo ($vo["id"]); ?></td>
        <td><?php echo ($vo["marke"]); ?></td>

        <td>
          <!--<a onclick="loadUser(<?php echo ($vo["uid"]); ?>,'<?php echo ($vo["user_name"]); ?>')" href="javascript:void(0);"><?php echo ($vo["user_name"]); ?></a>-->
          <?php echo user_names($vo['uid']);?>
        </td>

        <td><?php echo getzengpin($vo['zpid']);?></td>
        <td><?php echo ($vo["allnum"]); ?></td>
        <td><?php echo ($vo["num"]); ?></td>
        <td>
          <?php echo C('zhengpin_hqfs')[$vo['type']]; ;?>
      </td>
        <td><?php echo (date('Y-m-d H:i:s',$vo["add_time"])); ?></td>


        <!--<td>-->
            <!--<a href="__URL__/edit?id=<?php echo ($vo['id']); ?>">编辑</a> -->
            <!--<a href="javascript:void(0);" onclick="del(<?php echo ($vo['id']); ?>);">删除</a>  -->
        <!--</td>-->
      </tr><?php endforeach; endif; else: echo "" ;endif; ?>
  </table>

  </div>
  
  <div class="Toolbar_inbox">
  	<div class="page right"><?php echo ($pagebar); ?></div>

    <a onclick="del();" class="btn_a" href="javascript:void(0);"><span>删除</span></a>
  </div>
</div>


<script type="text/javascript" src="__ROOT__/Style/A/js/adminbase.js"></script>
</body>
</html>