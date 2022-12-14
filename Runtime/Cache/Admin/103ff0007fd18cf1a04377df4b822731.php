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
<link href="__ROOT__/Style/Swfupload/swfupload.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="__ROOT__/Style/Swfupload/handlers.js"></script>
<script type="text/javascript" src="__ROOT__/Style/Swfupload/swfupload.js"></script>
<script type="text/javascript">
//swf上传后排序
var IS_AD = true;
function rightPic(o){
	 var o = $("#albCtok"+o);
	 if( o.next().length > 0) {
		  var tmp = o.clone();
		  var oo = o.next();
		  o.remove();
		  oo.after(tmp);
	 }else{
		alert("已经是最后一个了"); 
	 }
}
//swf上传后排序
function leftPic(o){
	 var o = $("#albCtok"+o);
	 if( o.prev().length > 0) {
		  var tmp = o.clone();
		  var oo = o.prev();
		  o.remove();
		  oo.before(tmp);
	 }else{
		alert("已经是第一个了"); 
	 }
}
//swf上传后删除图片start
function delPic(id){
	var imgpath = $("#albCtok"+id).find("input[type='hidden']").eq(0).val();
	var datas = {'picpath':imgpath,'oid':id};
	$.post("__URL__/swfupload?delpic", datas, picdelResponse,'json');
}

function picdelResponse(res){
	var imgdiv = $("#albCtok"+res.data);
		imgdiv.remove();
		ui.success(res.info);
		ui.box.close();
}
//swf上传后删除图片end
</script>
<link rel="stylesheet" href="/Style/kindeditor/themes/default/default.css" />
<script charset="utf-8" src="/Style/kindeditor/kindeditor-min.js"></script>
<script charset="utf-8" src="/Style/kindeditor/lang/zh_CN.js"></script>
		<script>
			KindEditor.ready(function(K) {
				var editor = K.editor({
					allowFileManager : true
				});
				K('#J_selectImage').click(function() {
					editor.loadPlugin('multiimage', function() {
						editor.plugin.multiImageDialog({
							clickFn : function(urlList) {
								var div = K('#J_imageView');
								div.html('');
								K.each(urlList, function(i, data) {									
									addImage(data.url,i,data.url)									
									//div.append('<img src="' + data.url + '">');
								});
								editor.hideDialog();
							}
						});
					});
				});
			});
		</script>
<style type="text/css">
.albCt{height:200px}
</style>
<div class="so_main">

<div class="page_tit">添加广告</div>
<div class="page_tab"></div>
<div class="form2">
	<form method="post" action="__URL__/doEdit" onsubmit="return subcheck();" enctype="multipart/form-data">
	<input type="hidden" name="id" value="<?php echo ($vo["id"]); ?>" />
	<input type="hidden" name="ad_type" value="<?php echo ($vo["ad_type"]); ?>" />
	<div id="tab_1">
	
	<dl class="lineD"><dt>广告位置：</dt><dd><input name="title" id="title"  class="input" type="text" value="<?php echo ($vo["title"]); ?>" ><span id="tip_title" class="tip">*</span></dd></dl>
	<dl class="lineD"><dt>开始时间：</dt><dd><input onclick="WdatePicker({maxDate:'#F{$dp.$D(\'end_time\')||\'2025-10-01\'}'});" name="start_time" id="start_time"  class="input" type="text" value="<?php echo (date('Y-m-d',$vo["start_time"])); ?>"><span id="tip_start_time" class="tip">开始展示在网站的时间</span></dd></dl>
	<dl class="lineD"><dt>结束时间：</dt><dd><input onclick="WdatePicker({minDate:'#F{$dp.$D(\'start_time\')}',maxDate:'2025-10-01'});" name="end_time" id="end_time"  class="input" type="text" value="<?php echo (date('Y-m-d',$vo["end_time"])); ?>"><span id="tip_end_time" class="tip">停止展示的时间</span></dd></dl>
	<dl class="lineD" style="display:none"><dt>广告图片：</dt><dd><div style="display: inline; border: solid 1px #7FAAFF; background-color: #C5D9FF; padding: 2px;"><span id="spanButtonPlaceholder"></span></div><span class="tip">*</span></dd></dl>
    <dl class="lineD"><dt>广告图片：</dt><dd> <input type="button" id="J_selectImage" value="批量上传" />
		<div id="J_imageView"></div></dd></dl>
	<dl class="lineD"><dt>图片预览：</dt>
	<dd>
	<table cellpadding="0" cellspacing="0" width="100%">
    <tr id="handfield">
      <td colspan="4" class="bline" style="background:url(images/albviewbg.gif) #fff 0 20px no-repeat;">
       	<table width='100%' height='160' style="margin:0 0 20px 0">
       		<tr>
       			<td>
			 		<div id="thumbnails">
				<?php $x=1000;foreach(unserialize($vo['content']) as $v){ $x--; ?>
						<div class="albCt" id="albCtok<?php echo $x; ?>">
							<img width="120" height="120" src="__ROOT__/<?php echo $v['img']; ?>"><a onclick="javascript:delPic(<?php echo $x; ?>)" href="javascript:;">[删除]</a><a onclick="javascript:leftPic(<?php echo $x; ?>)" href="javascript:;">[前移]</a><a onclick="javascript:rightPic(<?php echo $x; ?>)" href="javascript:;">[后移]</a><div style="margin-top:10px">注释：<input type="text" style="width:190px;" value="<?php echo $v['info']; ?>" name="picinfo[]"><input type="hidden" value="__ROOT__/<?php echo $v['img']; ?>" name="swfimglist[]"></div><div style="margin-top:10px">地址：<input type="text" style="width:190px;" value="<?php echo $v['url']; ?>" name="urlinfo[]"></div>
						</div>				
				<?php } ?>
					</div>
				</td>
			</tr>
		</table>
      </td>
    </tr>
	</table>
	</dd></dl>
	
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