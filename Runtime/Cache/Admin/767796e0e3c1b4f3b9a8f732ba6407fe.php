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
  <div class="page_tit">文章管理</div>
  <script type="text/javascript" src="__ROOT__/Style/My97DatePicker/WdatePicker.js" language="javascript"></script>
<script type="text/javascript">
  var searchName = "搜索/筛选借款";
</script>
  <div id="search_div" style="display:none">
    <div class="page_tit"><script type="text/javascript">document.write(searchName);</script> [ <a href="javascript:void(0);" onclick="dosearch();">隐藏</a> ]</div>
  
  <div class="form2">
  <form method="get" action="__URL__">    
   <dl class="lineD">
      <dt>文章标题：</dt>
      <dd>
        <input name="title" style="width:190px" id="title" type="text" value="<?php echo ($search["title"]); ?>">
        <span>不填则不限制</span>
      </dd>
    </dl>
    <dl class="lineD">
      <dt>所属栏目：</dt>
      <dd>
        <select name="type_id" class="c_select">
          <!-- <?php if(is_array($catlist)): $i = 0; $__LIST__ = $catlist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$foo): $mod = ($i % 2 );++$i;?>-->
                <option value="<?php echo ($foo["id"]); ?>" <?php if($search['type_id'] == $foo['id']): ?>selected<?php endif; ?> ><?php echo ($foo["html"]); ?>|--<?php echo ($foo["type_name"]); ?></option>            
                <!--<?php endforeach; endif; else: echo "" ;endif; ?> -->
        </select>        
        <span>不填则不限制</span>
      </dd>
    </dl>
    <div class="page_btm">
      <input type="submit" class="btn_b" value="确定" />
    </div>
  </form>
  </div>
  </div>
  <div class="Toolbar_inbox">
    <div class="page right"><?php echo ($pagebar); ?></div>
    <a onclick="dosearch();" class="btn_a" href="javascript:void(0);"><span class="search_action">搜索/筛选</span></a>
    <a class="btn_a" href="__URL__/add"><span>添加文章</span></a>
    <a onclick="del();" class="btn_a" href="javascript:void(0);"><span>删除文章</span></a>
  </div>
  
  <div class="list">
  <table id="area_list" width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <th style="width:30px;">
        <input type="checkbox" id="checkbox_handle" onclick="checkAll(this)" value="0">
        <label for="checkbox"></label>
    </th>
    <th class="line_l">ID</th>
    <th class="line_l">文章标题</th>
    <th class="line_l">所属栏目</th>
    <th class="line_l">发布人</th>
    <th class="line_l">添加时间</th>
    <th class="line_l">操作</th>
  </tr>
  <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr overstyle='on' id="list_<?php echo ($vo["id"]); ?>">
        <td><input type="checkbox" name="checkbox" id="checkbox2" onclick="checkon(this)" value="<?php echo ($vo["id"]); ?>"></td>
        <td><?php echo ($vo["id"]); ?></td>
        <td><?php echo ($vo["title"]); ?></td>
        <td><?php echo ($vo["type_name"]); ?></td>
        <td><?php echo (($vo["art_writer"])?($vo["art_writer"]):'匿名'); ?></td>
        <td><?php echo (date('Y年m月d日H时',$vo["art_time"])); ?></td>
        <td>
            <a href="__URL__/edit?id=<?php echo ($vo['id']); ?>">编辑</a> 
            <a href="javascript:void(0);" onclick="del(<?php echo ($vo['id']); ?>);">删除</a>  
        </td>
      </tr><?php endforeach; endif; else: echo "" ;endif; ?>
  </table>
  </div>
  
  <div class="Toolbar_inbox">
    <div class="page right"><?php echo ($pagebar); ?></div>
    <a class="btn_a" href="__URL__/add"><span>添加文章</span></a>
    <a onclick="del();" class="btn_a" href="javascript:void(0);"><span>删除文章</span></a>
  </div>
</div>
<script type="text/javascript" src="__ROOT__/Style/A/js/adminbase.js"></script>
</body>
</html>