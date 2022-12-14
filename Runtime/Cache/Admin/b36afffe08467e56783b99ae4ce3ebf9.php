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
<div class="so_main">

<div class="page_tit">添加广告</div>
<div class="page_tab"></div>
<div class="form2">
	<form method="post" action="__URL__/doAdd" onsubmit="return subcheck();" enctype="multipart/form-data">
	<div id="tab_1">
	
	<dl class="lineD"><dt>广告类型：</dt><dd><?php $i=0;$___KEY=array ( 0 => '普通广告', 1 => '多图广告', ); foreach($___KEY as $k=>$v){ if(strlen("1")==1 && $i==0){ ?><input type="radio" name="ad_type" value="<?php echo ($k); ?>" id="ad_type_<?php echo ($i); ?>" checked="checked" /><?php }elseif(("1"=="key1"&&$_X["_Y"]==$k)||(""=="value"&&$_X["_Y"]==$v)){ ?><input type="radio" name="ad_type" value="<?php echo ($k); ?>" id="ad_type_<?php echo ($i); ?>" checked="checked" /><?php }else{ ?><input type="radio" name="ad_type" value="<?php echo ($k); ?>" id="ad_type_<?php echo ($i); ?>" /><?php } ?><label for="ad_type_<?php echo ($i); ?>"><?php echo ($v); ?></label>&nbsp;&nbsp;&nbsp;&nbsp;<?php $i++; } ?><span id="tip_ad_type" class="tip">*</span></dd></dl>
	<dl class="lineD"><dt>广告位置：</dt><dd><input name="title" id="title"  class="input" type="text" value="" ><span id="tip_title" class="tip">*</span></dd></dl>
	<dl class="lineD"><dt>开始时间：</dt><dd><input onclick="WdatePicker({maxDate:'#F{$dp.$D(\'end_time\')||\'2025-10-01\'}'});" name="start_time" id="start_time"  class="input" type="text" value=""><span id="tip_start_time" class="tip">开始展示在网站的时间</span></dd></dl>
	<dl class="lineD"><dt>结束时间：</dt><dd><input onclick="WdatePicker({minDate:'#F{$dp.$D(\'start_time\')}',maxDate:'2025-10-01'});" name="end_time" id="end_time"  class="input" type="text" value=""><span id="tip_end_time" class="tip">停止展示的时间</span></dd></dl>
	<dl class="lineD"><dt>是否去除P标签：</dt><dd><?php $i=0;$___KEY=array ( 0 => '是', 1 => '否', ); foreach($___KEY as $k=>$v){ if(strlen("1")==1 && $i==0){ ?><input type="radio" name="remove_p" value="<?php echo ($k); ?>" id="remove_p_<?php echo ($i); ?>" checked="checked" /><?php }elseif(("1"=="key1"&&$_X["_Y"]==$k)||(""=="value"&&$_X["_Y"]==$v)){ ?><input type="radio" name="remove_p" value="<?php echo ($k); ?>" id="remove_p_<?php echo ($i); ?>" checked="checked" /><?php }else{ ?><input type="radio" name="remove_p" value="<?php echo ($k); ?>" id="remove_p_<?php echo ($i); ?>" /><?php } ?><label for="remove_p_<?php echo ($i); ?>"><?php echo ($v); ?></label>&nbsp;&nbsp;&nbsp;&nbsp;<?php $i++; } ?></dd></dl>
	<dl class="lineD"><dt>广告内容：</dt>
	  <dd>
		<link rel="stylesheet" href="/Style/kindeditor/themes/default/default.css" />
<script charset="utf-8" src="/Style/kindeditor/kindeditor-min.js"></script>
<script charset="utf-8" src="/Style/kindeditor/lang/zh_CN.js"></script>
<script>
function loadEditor(textareaId) {
	var editor;
	KindEditor.ready(function(K) {
		editor = K.create('textarea[name="'+textareaId+'"]', {
			allowFileManager : true,
			autoHeightMode : true,
			afterCreate : function() {
				this.loadPlugin('autoheight');
			},
			afterUpload : function(url) {
				var firstimageoption = '<option value="' + url + '">' + url + '</option>';
				var selectoption = '<option value="' + url + '" selected="selected">' + url + '</option>';
				$("#firstimage").append(firstimageoption);
				$("#images").append(selectoption);
			},afterBlur: function(){this.sync();}
		});
	});

}
</script>
		<!-- 编辑器调用开始 -->
                <textarea name="content" id="content" style="width:780px;height:320px;"></textarea>
                <script>
                
                    loadEditor("content");
                
                </script>
                <!-- 编辑器调用结束 -->
	  </dd>
	</dl>
	
	</div><!--tab1-->

	<div class="page_btm">
	  <input type="submit" class="btn_b" value="确定" />
	</div>
	</form>
</div>

</div>
<script type="text/javascript" src="__ROOT__/Style/A/js/adminbase.js"></script>
</body>
</html>