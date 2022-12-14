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
<link href="__ROOT__/Style/Swfupload/swfupload.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="__ROOT__/Style/Swfupload/handlers.js"></script>
<script type="text/javascript" src="__ROOT__/Style/Swfupload/swfupload.js"></script>
<script type="text/javascript" src="__ROOT__/Style/My97DatePicker/WdatePicker.js" language="javascript"></script>

<script type="text/javascript" src="__ROOT__/Style/A20141030/js/calendar/calendar.js"></script>

<link href="__ROOT__/Style/A20141030/js/calendar/calendar-blue.css" rel="stylesheet" type="text/css">

<link rel="stylesheet" href="__ROOT__/Style/kindeditor/themes/default/default.css" />
<script charset="utf-8" src="__ROOT__/Style/kindeditor/kindeditor-min.js"></script>
<script charset="utf-8" src="__ROOT__/Style/kindeditor/lang/zh_CN.js"></script>
<script>
    $(function(){
        var editor;
        KindEditor.ready(function(K) {
            editor = K.create('textarea[name="return_info"]', {
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
    })
</script>

<style type="text/css">
.albCt{height:200px}
#J_imageView img{
    max-width: 200px;
}
/*#password {display: none;}*/
</style>
<div class="so_main">
  <div class="page_tit">审核借款</div>
  <div class="page_tab">
  <span data="tab_1" class="active">基本信息</span>
  <span data="tab_2">项目介绍</span>
  <span data="tab_3">分红及收益</span>
  <span data="tab_4">风控及退出</span>
  <span data="tab_5"  style="display:none">财务分析</span>  
  <span data="tab_6"  style="display:none">项目关注</span>  
  </div>
  <div class="form2">
    <form method="post" action="__URL__/doEdit<?php echo ($xact); ?>" onsubmit="return subcheck();" enctype="multipart/form-data">
      <input type="hidden" name="id" value="<?php echo ($vo["id"]); ?>" />
      <div id="tab_1">
        <?php if($id != '1'): ?><dl class="lineD">
          <dt>项目属性：</dt>
          <dd>
            <?php $i=0;$___KEY=array ( 0 => '普通', 1 => '首页推荐', 2 => '新手专区', ); foreach($___KEY as $k=>$v){ if(strlen("1key")==1 && $i==0){ ?><input type="radio" name="is_tuijian" value="<?php echo ($k); ?>" id="is_tuijian_<?php echo ($i); ?>" checked="checked" /><?php }elseif(("key1"=="key1"&&$vo["is_tuijian"]==$k)||("key"=="value"&&$vo["is_tuijian"]==$v)){ ?><input type="radio" name="is_tuijian" value="<?php echo ($k); ?>" id="is_tuijian_<?php echo ($i); ?>" checked="checked" /><?php }else{ ?><input type="radio" name="is_tuijian" value="<?php echo ($k); ?>" id="is_tuijian_<?php echo ($i); ?>" /><?php } ?><label for="is_tuijian_<?php echo ($i); ?>"><?php echo ($v); ?></label>&nbsp;&nbsp;&nbsp;&nbsp;<?php $i++; } ?>
          </dd>
        </dl>
         <dl class="lineD">
          <dt>合同模板号：</dt>
          <dd>
            <input name="templateid" id="templateid"  class="input" type="text" value="<?php echo ($vo["templateid"]); ?>" ><span id="tip_templateid" class="tip">*</span>
          </dd>
        </dl><?php endif; ?>
        <!--
        <dl class="lineD" style="color:red">
          <dt>是否提前完成：</dt>
          <dd>
            <?php $i=0;$___KEY=array ( 0 => '否', 1 => '是', ); foreach($___KEY as $k=>$v){ if(strlen("1key")==1 && $i==0){ ?><input type="radio" name="pause" value="<?php echo ($k); ?>" id="pause_<?php echo ($i); ?>" checked="checked" /><?php }elseif(("key1"=="key1"&&$vo["pause"]==$k)||("key"=="value"&&$vo["pause"]==$v)){ ?><input type="radio" name="pause" value="<?php echo ($k); ?>" id="pause_<?php echo ($i); ?>" checked="checked" /><?php }else{ ?><input type="radio" name="pause" value="<?php echo ($k); ?>" id="pause_<?php echo ($i); ?>" /><?php } ?><label for="pause_<?php echo ($i); ?>"><?php echo ($v); ?></label>&nbsp;&nbsp;&nbsp;&nbsp;<?php $i++; } ?><span id="tip_pause" class="tip">项目总额为当前投资总额，项目投资完成。</span>
          </dd>
        </dl>
        <dl class="lineD" style="color:red">
          <dt>密码标：</dt>
          <dd>
            <input name="password" id="password"  class="input" type="text" value="<?php echo ($vo["password"]); ?>" >
          </dd>
        </dl>  -->
      
          
       
       <!--//编号 : $(bianhao)
//甲方 : $(jiafang)
//身份证 : $(idno)

//乙方公司 : $(yifang_company_name)
//乙方代表人 : $(yifang_name)
//乙方信用代码 : $(yifang_xinyongdaima) -->
       <!--<dl class="lineD">
          <dt>甲方：</dt>
          <dd>
            <input name="jiafang" id="jiafang"  class="input" type="text" value="<?php echo ($vo["jiafang"]); ?>" ><span id="tip_jiafang" class="tip">*</span>
          </dd>
        </dl>    <dl class="lineD">
          <dt>甲方身份证：</dt>
          <dd>
            <input name="idno" id="idno"  class="input" type="text" value="<?php echo ($vo["idno"]); ?>" ><span id="tip_idno" class="tip">*</span>
          </dd>
        </dl>-->
         <!--<dl class="lineD">
          <dt>乙方公司：</dt>
          <dd>
            <input name="yifang_company_name" id="yifang_company_name"  class="input" type="text" value="<?php echo ($vo["yifang_company_name"]); ?>" ><span id="tip_yifang_company_name" class="tip">*</span>
          </dd>
        </dl>    <dl class="lineD">
          <dt>乙方代表人：</dt>
          <dd>
            <input name="yifang_name" id="yifang_name"  class="input" type="text" value="<?php echo ($vo["yifang_name"]); ?>" ><span id="tip_yifang_name" class="tip">*</span>
          </dd>
        </dl>   
        <dl class="lineD">
          <dt>乙方信用代码：</dt>
          <dd>
            <input name="yifang_xinyongdaima" id="yifang_xinyongdaima"  class="input" type="text" value="<?php echo ($vo["yifang_xinyongdaima"]); ?>" ><span id="tip_yifang_xinyongdaima" class="tip">*</span>
          </dd>
        </dl>   -->
        <dl class="lineD">
          <dt>众筹标题：</dt>
          <dd>
            <input name="borrow_name" id="borrow_name"  class="input" type="text" value="<?php echo ($vo["borrow_name"]); ?>" ><span id="tip_borrow_name" class="tip">*</span>
          </dd>
        </dl>
          <?php if($id != '1'): ?><dl class="lineD">
          <dt>项目类型：</dt>
          <dd>                 
            <select name="pid" id="pid"   class="c_select"><option value="">--请选择--</option><?php foreach($pid as $key=>$v){ if($vo["pid"]==$key && $vo["pid"]!=""){ ?><option value="<?php echo ($key); ?>" selected="selected"><?php echo ($v); ?></option><?php }else{ ?><option value="<?php echo ($key); ?>"><?php echo ($v); ?></option><?php }} ?></select>                      
          </dd>
        </dl>
      <dl class="lineD">
            <dt>新手专享：</dt>
            <dd>
                <?php $i=0;$___KEY=array ( 0 => '否', 1 => '是', ); foreach($___KEY as $k=>$v){ if(strlen("1key")==1 && $i==0){ ?><input type="radio" name="new_user_only" value="<?php echo ($k); ?>" id="new_user_only_<?php echo ($i); ?>" checked="checked" /><?php }elseif(("key1"=="key1"&&$vo["new_user_only"]==$k)||("key"=="value"&&$vo["new_user_only"]==$v)){ ?><input type="radio" name="new_user_only" value="<?php echo ($k); ?>" id="new_user_only_<?php echo ($i); ?>" checked="checked" /><?php }else{ ?><input type="radio" name="new_user_only" value="<?php echo ($k); ?>" id="new_user_only_<?php echo ($i); ?>" /><?php } ?><label for="new_user_only_<?php echo ($i); ?>"><?php echo ($v); ?></label>&nbsp;&nbsp;&nbsp;&nbsp;<?php $i++; } ?>
            </dd>
        </dl>
        <dl class="lineD">
            <dt>可以预约：</dt>
            <dd>
                <?php $i=0;$___KEY=array ( 0 => '否', 1 => '是', ); foreach($___KEY as $k=>$v){ if(strlen("1key")==1 && $i==0){ ?><input type="radio" name="bespeak_able" value="<?php echo ($k); ?>" id="bespeak_able_<?php echo ($i); ?>" checked="checked" /><?php }elseif(("key1"=="key1"&&$vo["bespeak_able"]==$k)||("key"=="value"&&$vo["bespeak_able"]==$v)){ ?><input type="radio" name="bespeak_able" value="<?php echo ($k); ?>" id="bespeak_able_<?php echo ($i); ?>" checked="checked" /><?php }else{ ?><input type="radio" name="bespeak_able" value="<?php echo ($k); ?>" id="bespeak_able_<?php echo ($i); ?>" /><?php } ?><label for="bespeak_able_<?php echo ($i); ?>"><?php echo ($v); ?></label>&nbsp;&nbsp;&nbsp;&nbsp;<?php $i++; } ?>
            </dd>
        </dl>
         <dl class="lineD">
            <dt>可以智投：</dt>
            <dd>
                <?php $i=0;$___KEY=array ( 0 => '是', 1 => '否', 2 => '新手智投', ); foreach($___KEY as $k=>$v){ if(strlen("1key")==1 && $i==0){ ?><input type="radio" name="zhitou_able" value="<?php echo ($k); ?>" id="zhitou_able_<?php echo ($i); ?>" checked="checked" /><?php }elseif(("key1"=="key1"&&$vo["zhitou_able"]==$k)||("key"=="value"&&$vo["zhitou_able"]==$v)){ ?><input type="radio" name="zhitou_able" value="<?php echo ($k); ?>" id="zhitou_able_<?php echo ($i); ?>" checked="checked" /><?php }else{ ?><input type="radio" name="zhitou_able" value="<?php echo ($k); ?>" id="zhitou_able_<?php echo ($i); ?>" /><?php } ?><label for="zhitou_able_<?php echo ($i); ?>"><?php echo ($v); ?></label>&nbsp;&nbsp;&nbsp;&nbsp;<?php $i++; } ?>
            </dd>
        </dl>
        <!--
        <dl class="lineD">
          <dt>是否允许加息券：</dt>
          <dd>
            <?php $i=0;$___KEY=array ( 0 => '否', 1 => '是，只是否允许加息券', ); foreach($___KEY as $k=>$v){ if(strlen("1key")==1 && $i==0){ ?><input type="radio" name="is_memberinterest" value="<?php echo ($k); ?>" id="is_memberinterest_<?php echo ($i); ?>" checked="checked" /><?php }elseif(("key1"=="key1"&&$vo["is_memberinterest"]==$k)||("key"=="value"&&$vo["is_memberinterest"]==$v)){ ?><input type="radio" name="is_memberinterest" value="<?php echo ($k); ?>" id="is_memberinterest_<?php echo ($i); ?>" checked="checked" /><?php }else{ ?><input type="radio" name="is_memberinterest" value="<?php echo ($k); ?>" id="is_memberinterest_<?php echo ($i); ?>" /><?php } ?><label for="is_memberinterest_<?php echo ($i); ?>"><?php echo ($v); ?></label>&nbsp;&nbsp;&nbsp;&nbsp;<?php $i++; } ?>
          </dd>
        </dl>-->
        <!-- <dl class="lineD">
          <dt>是否允许体验金：</dt>
          <dd>
            <?php $i=0;$___KEY=array ( 0 => '否', 1 => '是，只允许使用体验金', ); foreach($___KEY as $k=>$v){ if(strlen("1key")==1 && $i==0){ ?><input type="radio" name="is_experience" value="<?php echo ($k); ?>" id="is_experience_<?php echo ($i); ?>" checked="checked" /><?php }elseif(("key1"=="key1"&&$vo["is_experience"]==$k)||("key"=="value"&&$vo["is_experience"]==$v)){ ?><input type="radio" name="is_experience" value="<?php echo ($k); ?>" id="is_experience_<?php echo ($i); ?>" checked="checked" /><?php }else{ ?><input type="radio" name="is_experience" value="<?php echo ($k); ?>" id="is_experience_<?php echo ($i); ?>" /><?php } ?><label for="is_experience_<?php echo ($i); ?>"><?php echo ($v); ?></label>&nbsp;&nbsp;&nbsp;&nbsp;<?php $i++; } ?>
          </dd>
        </dl>        
        <dl class="lineD">
          <dt>是否允许使用红包：</dt>
          <dd>
            <?php $i=0;$___KEY=array ( 0 => '否', 1 => '是，最低投资金额', ); foreach($___KEY as $k=>$v){ if(strlen("1key")==1 && $i==0){ ?><input type="radio" name="is_bonus" value="<?php echo ($k); ?>" id="is_bonus_<?php echo ($i); ?>" checked="checked" /><?php }elseif(("key1"=="key1"&&$vo["is_bonus"]==$k)||("key"=="value"&&$vo["is_bonus"]==$v)){ ?><input type="radio" name="is_bonus" value="<?php echo ($k); ?>" id="is_bonus_<?php echo ($i); ?>" checked="checked" /><?php }else{ ?><input type="radio" name="is_bonus" value="<?php echo ($k); ?>" id="is_bonus_<?php echo ($i); ?>" /><?php } ?><label for="is_bonus_<?php echo ($i); ?>"><?php echo ($v); ?></label>&nbsp;&nbsp;&nbsp;&nbsp;<?php $i++; } ?>
            <input name="is_bonus_invest_min" id="is_bonus_invest_min"  class="input" type="text" value="<?php echo ($vo["is_bonus_invest_min"]); ?>" ><span id="tip_is_bonus_invest_min" class="tip">元</span>
          </dd>
        </dl> -->
 
 
        
        <dl class="lineD">
          <dt>项目管理费：</dt>
          <dd>
            <input name="borrow_fee" id="borrow_fee"  class="input" type="text" value="<?php echo ($vo["borrow_fee"]); ?>" ><span id="tip_borrow_fee" class="tip">默认为按后台设置计算出来的，如果私下有协议可以改</span>
          </dd>
        </dl>
         <dl class="lineD">
          <dt>项目总金额：</dt>
          <dd>
             <input name="borrow_money" id="borrow_money"  class="input" type="text" value="<?php echo ($vo["borrow_money"]); ?>" ><span id="tip_borrow_money" class="tip">众筹总金额</span>   
          </dd>
        </dl>
        <dl class="lineD">
          <dt>众筹时间(天)：</dt>
          <dd>
            <input name="collect_day" id="collect_day"  class="input" type="text" value="<?php echo ($vo["collect_day"]); ?>" ><span id="tip_collect_day" class="tip">在前台展示天数，如在担心在设定时间内不能募集完成，可修改延长</span>
          </dd>
        </dl>




        <dl class="lineD">
          <dt>售价：</dt>
          <dd>
            <input name="shoujia" id="shoujia"  class="input" type="text" value="<?php echo ($vo["shoujia"]); ?>" ><span id="tip_shoujia" class="tip">商城销售价格</span>
          </dd>
        </dl>
              <dl class="lineD">
                  <dt>库存：</dt>
                  <dd>
                      <input name="art_writer" id="art_writer"  class="input" type="text" value="<?php echo ($vo["art_writer"]); ?>" ><span id="tip_art_writer" class="tip">库存</span>
                  </dd>
              </dl>

        <!--<dl class="lineD">-->
          <!--<dt>众筹时间(天)：</dt>-->
          <!--<dd>-->
            <!--<input name="collect_day" id="collect_day"  class="input" type="text" value="<?php echo ($vo["collect_day"]); ?>" ><span id="tip_collect_day" class="tip">在前台展示天数，如在担心在设定时间内不能募集完成，可修改延长</span>-->
          <!--</dd>-->
        <!--</dl>-->

        <!--doEdit-->
        
       <?php if($xact == 'waitverify2'): ?><dl class="lineD">
               <dt>开售时间：</dt>
               <dd>
                   <input name="xs_time" id="xs_time"  class="input" type="text" value="<?php echo (date('Y-m-d H:i:s',$vo["xs_time"])); ?>" ><span id="tip_xs_time" class="tip">上架商城时间</span>
               </dd>
           </dl>

           <dl class="lineD">
               <dt>下架时间：</dt>
               <dd>
                   <input name="xj_time" id="xj_time"  class="input" type="text" value="<?php echo (date('Y-m-d H:i:s',$vo["xj_time"])); ?>" ><span id="tip_xj_time" class="tip">下架商城时间</span>
               </dd>
           </dl>

           <dl class="lineD">
               <dt>收购时间：</dt>
               <dd>
                   <input name="sg_time" id="sg_time"  class="input" type="text" value="<?php echo (date('Y-m-d H:i:s',$vo["sg_time"])); ?>" ><span id="tip_sg_time" class="tip">到期收购时间</span>
               </dd>
           </dl>
           <script type="text/javascript">
               date = new Date();
               Calendar.setup({
                   inputField     :    "xs_time",
                   ifFormat       :    "%Y-%m-%d %H:%M:%S",
                   showsTime      :    true,
                   timeFormat     :    "24"
               });

               Calendar.setup({
                   inputField     :    "sg_time",
                   ifFormat       :    "%Y-%m-%d %H:%M:%S",
                   showsTime      :    true,
                   timeFormat     :    "24"
               });

               Calendar.setup({
                   inputField     :    "xj_time",
                   ifFormat       :    "%Y-%m-%d %H:%M:%S",
                   showsTime      :    true,
                   timeFormat     :    "24"
               });

           </script>

        <dl class="lineD">
          <dt>预计回款日期：</dt>
          <dd>
           <!--<input name="lead_time" type="date" value="" placeholder="2018-01-01" required="required"> -->
            <input name="lead_time" id="lead_time"  class="input" type="text" value="<?php echo (date('Y-m-d H:i:s',$vo["lead_time"])); ?>" ><span id="tip_lead_time" class="tip">在前台展示天数，如在担心在设定时间内不能募集完成，可修改延长</span>
          </dd>
        </dl>
         <script type="text/javascript">
        date = new Date();
        Calendar.setup({
          inputField     :    "lead_time",
          ifFormat       :    "%Y-%m-%d %H:%M:%S",
          showsTime      :    true,
          timeFormat     :    "24"
        });
        </script><?php endif; ?>
      
        <dl class="lineD">
          <dt>回报类型：</dt>
          <dd>
            <?php $i=0;$___KEY=array ( 1 => '收益回报', 2 => '线下回报', ); foreach($___KEY as $k=>$v){ if(strlen("1key")==1 && $i==0){ ?><input type="radio" name="loan_certificate" value="<?php echo ($k); ?>" id="loan_certificate_<?php echo ($i); ?>" checked="checked" /><?php }elseif(("key1"=="key1"&&$vo["loan_certificate"]==$k)||("key"=="value"&&$vo["loan_certificate"]==$v)){ ?><input type="radio" name="loan_certificate" value="<?php echo ($k); ?>" id="loan_certificate_<?php echo ($i); ?>" checked="checked" /><?php }else{ ?><input type="radio" name="loan_certificate" value="<?php echo ($k); ?>" id="loan_certificate_<?php echo ($i); ?>" /><?php } ?><label for="loan_certificate_<?php echo ($i); ?>"><?php echo ($v); ?></label>&nbsp;&nbsp;&nbsp;&nbsp;<?php $i++; } ?>  
          </dd>
        </dl> 
          
        <dl class="lineD">
          <dt>是否限制：</dt>
          <dd>
            <?php $i=0;$___KEY=array ( 0 => '否', 1 => '是', ); foreach($___KEY as $k=>$v){ if(strlen("1key")==1 && $i==0){ ?><input type="radio" name="is_limit" value="<?php echo ($k); ?>" id="is_limit_<?php echo ($i); ?>" checked="checked" /><?php }elseif(("key1"=="key1"&&$vo["is_limit"]==$k)||("key"=="value"&&$vo["is_limit"]==$v)){ ?><input type="radio" name="is_limit" value="<?php echo ($k); ?>" id="is_limit_<?php echo ($i); ?>" checked="checked" /><?php }else{ ?><input type="radio" name="is_limit" value="<?php echo ($k); ?>" id="is_limit_<?php echo ($i); ?>" /><?php } ?><label for="is_limit_<?php echo ($i); ?>"><?php echo ($v); ?></label>&nbsp;&nbsp;&nbsp;&nbsp;<?php $i++; } ?>
          </dd>
        </dl>

              <dl class="lineD">
                  <dt>活动：</dt>
                  <dd>
                      <?php $i=0;$___KEY=array ( 0 => '否', 1 => '是', ); foreach($___KEY as $k=>$v){ if(strlen("1key")==1 && $i==0){ ?><input type="radio" name="is_huodong" value="<?php echo ($k); ?>" id="is_huodong_<?php echo ($i); ?>" checked="checked" /><?php }elseif(("key1"=="key1"&&$vo["is_huodong"]==$k)||("key"=="value"&&$vo["is_huodong"]==$v)){ ?><input type="radio" name="is_huodong" value="<?php echo ($k); ?>" id="is_huodong_<?php echo ($i); ?>" checked="checked" /><?php }else{ ?><input type="radio" name="is_huodong" value="<?php echo ($k); ?>" id="is_huodong_<?php echo ($i); ?>" /><?php } ?><label for="is_huodong_<?php echo ($i); ?>"><?php echo ($v); ?></label>&nbsp;&nbsp;&nbsp;&nbsp;<?php $i++; } ?>
                  </dd>
              </dl>

              <dl class="lineD">
                  <dt>约标：</dt>
                  <dd>
                      <?php $i=0;$___KEY=array ( 1 => '否', 2 => '是', ); foreach($___KEY as $k=>$v){ if(strlen("1key")==1 && $i==0){ ?><input type="radio" name="leixing" value="<?php echo ($k); ?>" id="leixing_<?php echo ($i); ?>" checked="checked" /><?php }elseif(("key1"=="key1"&&$vo["leixing"]==$k)||("key"=="value"&&$vo["leixing"]==$v)){ ?><input type="radio" name="leixing" value="<?php echo ($k); ?>" id="leixing_<?php echo ($i); ?>" checked="checked" /><?php }else{ ?><input type="radio" name="leixing" value="<?php echo ($k); ?>" id="leixing_<?php echo ($i); ?>" /><?php } ?><label for="leixing_<?php echo ($i); ?>"><?php echo ($v); ?></label>&nbsp;&nbsp;&nbsp;&nbsp;<?php $i++; } ?>
                  </dd>
              </dl>

              <dl class="lineD">
                  <dt>赠品选择：</dt>
                  <dd>
                      <select name="zpid"  class="c_select">
                          <option>--请选择--</option>

                          <?php if(is_array($zplist)): $i = 0; $__LIST__ = $zplist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v1): $mod = ($i % 2 );++$i;?><option value="<?php echo ($v1["id"]); ?>" <?php if($v1["id"] == $vo['zpid']): ?>selected="selected"<?php endif; ?> ><?php echo ($v1["title"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>

                      </select>



                      <!--<select name="title" id="title"   class="c_select"><option value="">--请选择--</option><?php foreach($zpid as $key=>$v){ if($vo["zpid"]==$key && $vo["zpid"]!=""){ ?><option value="<?php echo ($key); ?>" selected="selected"><?php echo ($v); ?></option><?php }else{ ?><option value="<?php echo ($key); ?>"><?php echo ($v); ?></option><?php }} ?></select>-->
                  </dd>
              </dl>

              <dl class="lineD">
                  <dt>送赠品金额：</dt>
                  <dd>
                      <input name="huodongnum" id="huodongnum"  class="input" type="text" value="<?php echo ($vo["huodongnum"]); ?>" ><span id="tip_huodongnum" class="tip">活动项目送一份赠品的金额</span>
                      <?php echo ($vo["huodongnum"]); ?>
                  </dd>
              </dl>




        <dl class="lineD">
          <dt>快捷支持份数：</dt>
          <dd>
            <input name="shortcut" id="shortcut"  class="input" type="text" value="<?php echo ($vo["shortcut"]); ?>" ><span id="tip_shortcut" class="tip">请严格按照示例填写支持份数，例如：10,20,50</span>
          </dd>
        </dl><?php endif; ?>
        <?php if($id == '1'): ?><dl class="lineD">
            <dt>累计投标人数：</dt>
            <dd>
              <textarea name="cinvestinfo" id="cinvestinfo"  class="areabox" ><?php echo count($investinfo);?></textarea>
            </dd>
          </dl> 
            <dl class="lineD">
            <dt>累计投标金额：</dt>
            <dd>
              <textarea name="has_borrow" id="has_borrow"  class="areabox" ><?php echo ($vv["has_borrow"]); ?></textarea>
            </dd>
          </dl>
     
          <!--readonly="readonly"--><?php endif; ?>
        <!--
                 <?php if($vo["borrow_status"] < '3' || $vo["borrow_status"] == '3'): ?><dl class="lineD">
            <dt>初审处理意见：</dt>
            <dd>
              <textarea name="deal_info" id="deal_info"  class="areabox" ><?php echo ($vv["deal_info"]); ?></textarea><span id="tip_deal_info" class="tip">*</span>
            </dd>
          </dl><?php endif; ?>
          
        -->
          <?php if($id == '1'): ?><dl class="lineD" style="display: none;"  >
          <dt> 支持金额：</dt>
          <dd>
            <input name="borrow_min" id="borrow_min"  class="input" type="text" value="<?php echo ($glo["award_reg"]); ?>" ><span id="tip_borrow_min" class="tip">0表示无限制</span>
          </dd>
        </dl>
        <?php else: ?>
          <dl class="lineD" style=""  >
          <dt> 支持金额：</dt>
          <dd>
            <input name="borrow_min" id="borrow_min"  class="input" type="text" value="<?php echo ($vo["borrow_min"]); ?>" ><span id="tip_borrow_min" class="tip">0表示无限制</span>
          </dd>
        </dl><?php endif; ?>
      <?php if($id != '1'): ?><dl class="lineD"  >
          <dt>每人限购数：</dt>
          <dd>
            <input name="max_limit" id="max_limit"  class="input" type="text" value="<?php echo ($vo["max_limit"]); ?>" ><span id="tip_max_limit" class="tip">0表示无限制</span>
          </dd>
        </dl> 

       <dl class="lineD" style=""  >
          <dt> 收益率：</dt>
          <dd>
            <input name="shouyilv" id="shouyilv"  class="input" type="text" value="<?php echo ($vo["shouyilv"]); ?>" ><span id="tip_shouyilv" class="tip">填写后将最先显示</span>
          </dd>
        </dl>
         <dl class="lineD" style=""  >
          <dt> 回款天数：</dt>
          <dd>
            <input name="hkday" id="hkday"  class="input" type="text" value="<?php echo ($vo["hkday"]); ?>" ><span id="tip_hkday" class="tip">多期回款，回款间隔小于一个月的</span>
          </dd>
        </dl>

       <dl class="lineD">
          <dt>回报内容：</dt>
          <dd>          
<textarea name="return_info" id="return_info" style="width:800px;height:40px;"><?php echo ($vo["return_info"]); ?></textarea>
          </dd>
        </dl>
    
        <!-- 
           <dl class="lineD" style="display: none;">
          <dt>最多投资总额：</dt>
          <dd>
            <input name="borrow_max" id="borrow_max"  class="input" type="text" value="" ><span id="tip_borrow_max" class="tip">0表示无限制</span>
          </dd>
        </dl>-->
        <!--<dl class="lineD">
          <dt>项目生产厂家：</dt>
          <dd>
            <input name="borrow_sccj" id="borrow_sccj"  class="input" type="text" value="<?php echo ($vo["borrow_sccj"]); ?>" >
          </dd>
        </dl>-->    
        <dl class="lineD">
          <dt>预计收益：</dt>
          <dd>
            <input name="borrow_interest_rate" id="borrow_interest_rate"  class="input" type="text" value="<?php echo ($vo["borrow_interest_rate"]); ?>" >%
          </dd>
        </dl>
                
        <dl class="lineD">
          <dt>项目周期：</dt>
          <dd>
            <input name="borrow_duration" id="borrow_duration"  class="input" type="text" value="<?php echo ($vo["borrow_duration"]); ?>" >
          </dd>
        </dl> 
        
        <dl class="lineD">
          <dt>项目期数：</dt>
          <dd>
            <input name="total" id="total"  class="input" type="text" value="<?php echo ($vo["total"]); ?>" >
          </dd>
        </dl>
           <dl class="lineD">
          <dt>项目上线时间：</dt>
          <dd>
               <!-- <input type="text" id="start_time" name="start_time" value="<?php echo date('Y-m-d H:i:s',time());?>" class="tip">请选择项目预热时间，不选择为立即上线</span>-->
             <?php if(empty($vo['start_time'] ) or ((int)$vo['start_time'] < 1755423)): ?><input type="text" id="start_time" name="start_time" value="<?php echo date('Y-m-d H:i:s',time());?>" class="tip">请选择项目预热时间，不选择为立即上线</span>
            <?php else: ?>
                 <input type="text" id="start_time" name="start_time" value="<?php echo (date('Y-m-d H:i:s',$vo['start_time'])); ?>" class="tip">请选择项目预热时间，不选择为立即上线</span><?php endif; ?>
           
            
          </dd>
        </dl>
     <script type="text/javascript">
        date = new Date();
        Calendar.setup({
          inputField     :    "start_time",
          ifFormat       :    "%Y-%m-%d %H:%M:%S",
          showsTime      :    true,
          timeFormat     :    "24"
        });
        </script>
        <!--<dl class="lineD">
          <dt>项目上线时间：</dt>
          <dd>
            <input onclick="WdatePicker({maxDate:'#F{$dp.$D('end_time')||'2020-10-01'}',dateFmt:'yyyy-MM-dd HH:mm:ss',alwaysUseStartDate:true});" name="start_time" id="start_time"  class="input Wdate" type="text" value="<?php echo (mydate('Y-m-d H:i:s',$vo["start_time"])); ?>"><span id="tip_start_time" class="tip">请选择项目预热时间，不选择为立即上线</span>
            <div style="display:none"><input onclick="WdatePicker({minDate:'#F{$dp.$D('start_time')}',maxDate:'2020-10-01',dateFmt:'yyyy-MM-dd HH:mm:ss',alwaysUseStartDate:true});" name="end_time" id="end_time"  class="input Wdate" type="text" value="<?php echo (mydate('Y-m-d H:i:s',$search["end_time"])); ?>"><span id="tip_end_time" class="tip">只选结束时间则查询从结束时间往前所有</span></div>
          </dd>
        </dl>--><?php endif; ?>
        
        <dl class="lineD" style="overflow:hidden">
          <dt>项目缩略图：</dt>
          <dd>
            <input type="file" id="borrow_img" name="borrow_img[]" style="float:left"/>
      <span style="float:left"><div style="text-align:left; clear:both; overflow:hidden; width:290px; height:50px"><div id="imgDiv"></div><?php if($vo["borrow_img"] == ''): ?>无缩略图<?php else: ?><img src="<?php echo ($vo["borrow_img"]); ?>" width="100" height="100" /><?php endif; ?></div></span> 宽度:366px 高度:273px 
          </dd>
        </dl>
              <dl class="lineD" style="overflow:hidden">
                  <dt>分享缩略图：</dt>
                  <dd>
                      <input type="file" id="fx_img" name="fx_img[]" style="float:left"/>
                      <span style="float:left"><div style="text-align:left; clear:both; overflow:hidden; width:290px; height:50px">
                          <div id="imgDiv"></div><?php if($vo["fx_img"] == ''): ?>无缩略图<?php else: ?>
                          <img src="<?php echo ($vo["fx_img"]); ?>" width="100" height="100" /><?php endif; ?></div></span> 20k以下
                  </dd>
              </dl>


          <dl class="lineD" style="overflow:hidden">
              <dt>视频图片：</dt>
              <dd>
                  <input type="file" id="shipinimg" name="shipinimg[]" style="float:left"/>
                  <span style="float:left"><div style="text-align:left; clear:both; overflow:hidden; width:290px; height:50px">
                      <div id="imgDiv"></div><?php if($vo["shipinimg"] == ''): ?>无缩略图<?php else: ?>
                      <img src="<?php echo ($vo["shipinimg"]); ?>" width="100" height="100" /><?php endif; ?></div></span>
              </dd>
          </dl>
          <dl class="lineD">
              <dt>视频地址：</dt>
              <dd>
                  <input name="shipin" id="shipin"  class="input" type="text" value="<?php echo ($vo["shipin"]); ?>" >
              </dd>
          </dl>





          <dl class="lineD">
          <dt>一句话介绍：</dt>
          <dd>          
<textarea name="borrow_con" id="borrow_con" style="width:800px;height:40px;"><?php echo ($vo["borrow_con"]); ?></textarea>
          </dd>
        </dl>
        <!-- <dl class="lineD">
          <dt>项目官方群：</dt>
          <dd><input name="contact_qq" type="text" value="<?php echo ($vo["contact_qq"]); ?>"></dd>
        </dl> -->
       <!--  <dl class="lineD">
         <dt>站 长 电 话：</dt>
         <dd><input name="contact_site_phone" type="text" value="<?php echo ($vo["contact_site_phone"]); ?>"></dd>
       </dl> -->
        <!-- <dl class="lineD">
          <dt>客 服 电 话：</dt>
          <dd><input name="contact_kefu_phone" type="text" value="<?php echo ($vo["contact_kefu_phone"]); ?>"></dd>
        </dl> -->
       <!--  <dl class="lineD">
          <dt>监 督 电 话：</dt>
          <dd><input name="contact_jiandu_phone" type="text" value="<?php echo ($vo["contact_jiandu_phone"]); ?>"></dd>
        </dl>
          -->
            <?php if($id != '1'): ?><dl class="lineD">
          <dt>是否通过：</dt>
          <dd>
            <?php $i=0;foreach($borrow_status as $k=>$v){ if(strlen("key1")==1&&$i==0){ ?><input type="radio" name="borrow_status" value="<?php echo ($k); ?>" id="borrow_status_<?php echo ($i); ?>" checked="checked" /><?php }elseif("key1"=="key1"&&$k==$vo["borrow_status"]){ ?><input type="radio" name="borrow_status" value="<?php echo ($k); ?>" id="borrow_status_<?php echo ($i); ?>" checked="checked" /><?php }elseif("key1"=="value1"&&$v==$vo["borrow_status"]){ ?><input type="radio" name="borrow_status" value="<?php echo ($k); ?>" id="borrow_status_<?php echo ($i); ?>" checked="checked" /><?php }else{ ?><input type="radio" name="borrow_status" value="<?php echo ($k); ?>" id="borrow_status_<?php echo ($i); ?>" /><?php } ?><label for="borrow_status_<?php echo ($i); ?>"><?php echo ($v); ?></label>&nbsp;&nbsp;&nbsp;&nbsp;<?php $i++;} ?>
          </dd>
        </dl>
        <?php if($vo["borrow_status"] < '3' || $vo["borrow_status"] == '3'): ?><dl class="lineD">
            <dt>初审处理意见：</dt>
            <dd>
              <textarea name="deal_info" id="deal_info"  class="areabox" ><?php echo ($vv["deal_info"]); ?></textarea><span id="tip_deal_info" class="tip">*</span>
            </dd>
          </dl><?php endif; ?>
        <?php if($vo["borrow_status"] > '3'): ?><dl class="lineD">
            <dt>复审处理意见：</dt>
            <dd>
              <textarea name="deal_info_2" id="deal_info_2"  class="areabox" ><?php echo ($vv["deal_info_2"]); ?></textarea><span id="tip_deal_info_2" class="tip">*</span>
            </dd>
          </dl><?php endif; ?>
        <?php else: ?>
<input type="hidden" name="borrow_status" value="2"  checked="checked"><?php endif; ?>
      </div>
      <!--tab1-->
      <div id="tab_2" style="display:none">
         <dl class="lineD">
          <dt>项目简介：</dt>
          <dd>
          <link rel="stylesheet" href="__ROOT__/Style/kindeditor/themes/default/default.css" />
                <script charset="utf-8" src="__ROOT__/Style/kindeditor/kindeditor-min.js"></script> 
                <script charset="utf-8" src="__ROOT__/Style/kindeditor/lang/zh_CN.js"></script>
                <script>
                  $(function(){
                    var editor;
                    KindEditor.ready(function(K) {
                      editor = K.create('textarea[name="borrow_info"]', {
                        allowFileManager : true,
                        autoHeightMode : true,
                        filterMode:false,
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
                  })

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
                                      var imgs='';
                                      K.each(urlList, function(i, data) {
                                         // addImage(data.url,i,data.url)
                                          if(i==0){
                                              imgs+=data.url;
                                          }else{
                                              imgs+=','+data.url;
                                          }
                                          div.append('<img src="' + data.url + '">');
                                      });

                                      $("#content_img").val(imgs);
                                      editor.hideDialog();
                                  }
                              });
                          });
                      });
                  });


                  </script>
                <textarea name="borrow_info" id="borrow_info" style="width:1000px;height:400px;visibility:hidden;"><?php echo ($vo["borrow_info"]); ?></textarea>
          </dd>
        </dl>

          <dl class="lineD">
              <dt>轮播图：</dt>
              <dd>
                  <input type="button" id="J_selectImage" value="批量上传" />
                  <input type="text"  id="content_img" name="content_img" value="<?php echo ($vo["content_img"]); ?>">
                    <div id="J_imageView">
                        <?php
 $con_img=explode(',',$vo['content_img']); foreach ($con_img as $v1){?><img src="<?php echo $v1;?>"><?php }?>
                    </div>
              </dd>
          </dl>

      </div>
      <div id="tab_3" style="display:none">
               <dl class="lineD">
          <dt>分红及收益：</dt>
          <dd>          
                <script>
                  $(function(){
                    var editor;
                    KindEditor.ready(function(K) {
                      editor = K.create('textarea[name="budget_revenue"]', {
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
                  })
                  </script>
                <textarea name="budget_revenue" id="budget_revenue" style="width:1000px;height:400px;visibility:hidden;"><?php echo ($vo["budget_revenue"]); ?></textarea>
          </dd>
        </dl> 
      </div>
      <div id="tab_4" style="display:none">
                 <dl class="lineD">
          <dt>风控及退出：</dt>
          <dd>
                <script>
                  $(function(){
                    var editor;
                    KindEditor.ready(function(K) {
                      editor = K.create('textarea[name="borrow_feasibility"]', {
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
                  })
                  </script>
                <textarea name="borrow_feasibility" id="borrow_feasibility" style="width:1000px;height:400px;visibility:hidden;"><?php echo ($vo["borrow_feasibility"]); ?></textarea>
          </dd>
        </dl>
      </div>
      <div id="tab_6"  style="display:none">        
        <dl class="lineD">
          <dt>财务分析：</dt>
          <dd>
                <script>
                  $(function(){
                    var editor;
                    KindEditor.ready(function(K) {
                      editor = K.create('textarea[name="borrow_financial"]', {
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
                  })
                  </script>
                <textarea name="borrow_financial" id="borrow_financial" style="width:1000px;height:400px;visibility:hidden;"><?php echo ($vo["borrow_financial"]); ?></textarea>
          </dd>
        </dl>
      </div>
            <div id="tab_5"  style="display:none">        
        <dl class="lineD">
          <dt>项目关注：</dt>
          <dd>
                <script>
                  $(function(){
                    var editor;
                    KindEditor.ready(function(K) {
                      editor = K.create('textarea[name="borrow_follow"]', {
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
                  })
                  </script>
                <textarea name="borrow_follow" id="borrow_follow" style="width:1000px;height:400px;visibility:hidden;"><?php echo ($vo["borrow_follow"]); ?></textarea>
          </dd>
        </dl>
      </div>
      <div class="page_btm">
        <input type="submit" class="btn_b" value="确定" />
      </div>
    </form>
  </div>
</div>
     <script>
            $("#cinvestinfo").attr("readonly","readonly");
              $("#has_borrow").attr("readonly","readonly");
          </script>
<script type="text/javascript">
function addone(){
  var htmladd = '<dl class="lineD"><dt>资料名称：</dt>';
    htmladd+= '<dd><input type="text" name="updata_name[]" value="" />&nbsp;&nbsp;更新时间:<input type="text" name="updata_time[]" onclick="WdatePicker();" class="Wdate" /></dd>';
    htmladd+= '</dl>';
  $(htmladd).appendTo("#tab_3");
}
var cansub = true;
function subcheck(){
  if(!cansub){
    alert("请不要重复提交，如网速慢，请等待！");
    return false; 
  }
  var deal_info = $("#deal_info").val();
  var deal_info_2 = $("#deal_info_2").val();
  var borrow_status = <?php echo ($vo["borrow_status"]); ?>;
  
  if(borrow_status<=3){
    if(deal_info ==""){
      //ui.error("初审处理意见不能为空！");
      //return false;
    }
  }else{
    if( deal_info_2 ==""){
      //ui.error("复审处理意见不能为空！");
      //return false;
    }
  }
  cansub = false;
  return true;
}
</script>
<script type="text/javascript" src="__ROOT__/Style/A/js/adminbase.js"></script>
</body>
</html>