<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3c.org/TR/1999/REC-html401-19991224/loose.dtd">
<!-- saved from url=(0052)http://metinfo.zihaistar.com/admin/index.php?lang=cn -->
<HTML xmlns="http://www.w3.org/1999/xhtml"><HEAD>
  <title><?php echo ($ts['site']['site_name']); ?>后台管理</title>
<META http-equiv=Content-Type content="text/html; charset=utf-8">
<!--[if IE 6]>
<script src="__ROOT__/Style/A20141030/js/DD_belatedPNG.js"></script>
<script>
  PNG.fix('.png');
</script>
<![endif]-->
<link rel="shortcut icon" href="__PUBLIC__/images/favicon.ico" />
<link rel="bookmark"href="__PUBLIC__/images/favicon.ico" />
<LINK href="__ROOT__/Style/A20141030/style/metinfo_box.css" rel=stylesheet>
<LINK href="__ROOT__/Style/A20141030/js/nanoscroller.css" rel=stylesheet>
<link rel="stylesheet" type="text/css" href="__ROOT__/Style/A20141030/style/metinfo.css">
<link rel="stylesheet" type="text/css" href="__ROOT__/Style/A20141030/style/newstyle.css">
<link rel="stylesheet" media="screen" type="text/css" href="__ROOT__/Style/A20141030/style/colorpicker.css">
<SCRIPT src="__ROOT__/Style/A20141030/js/jQuery1.7.2.js" type=text/javascript></SCRIPT>
<!-- <SCRIPT src="__ROOT__/Style/A20141030/js/cookie.js" type=text/javascript></SCRIPT> -->
<SCRIPT src="__ROOT__/Style/A20141030/js/jquery.nanoscroller.js" type=text/javascript></SCRIPT>
<script type="text/javascript">
  var current_channel   = null;
  var current_menu_root = null;
  var current_menu_sub  = null;
/*
  var viewed_channel    = new Array();
  
  $(document).ready(function(){
    switchChannel('0');
  });
  
  //切换频道（即头部的tab）
  function switchChannel(channel) {
    if(current_channel == channel) return false;
    
    $('#channel_'+current_channel).removeClass('on');
    $('#channel_'+channel).addClass('on');
    
    $('#root_'+current_channel).css('display', 'none');
    $('#root_'+channel).css('display', 'block');
    
    var tmp_menulist = $('#root_'+channel).find('a');
    tmp_menulist.each(function(i, n) {
      // 防止重复点击ROOT菜单
      if( i == 0 && $.inArray($(n).attr('id'), viewed_channel) == -1 ) {
        $(n).click();
        viewed_channel.push($(n).attr('id'));
      }
      if ( i == 1 ) {
        $(n).click();
      }
    });
    current_channel = channel;
  }
  
  function switch_root_menu(root) {
    root = $('#tree_'+root);
    if (root.css('display') == 'block') {
      root.css('display', 'none');
      root.parent().css('backgroundImage', 'url(__ROOT__/Style/A/images/ArrOn.png)');
    }else {
      root.css('display', 'block');
      root.parent().css('backgroundImage', 'url(__ROOT__/Style/A/images/ArrOff.png)');
    }
  }
  
 */
  function switch_sub_menu(sub, url) {
  
      if(current_menu_sub) {
  
        $('#menu_'+current_menu_sub).attr('class', 'submenuA');
  
      }
      
      $('#menu_'+sub).attr('class', 'submenuB');
  
    
    current_menu_sub = sub;
    $('#menu_16').attr('class', 'suaxin');
    parent.MainIframe.location = url;
  }
 
  /*
  function resetEscAndF5(e) {
    e = e ? e : window.event;
    actualCode = e.keyCode ? e.keyCode : e.charCode;
    if(actualCode == 116 && parent.MainIframe) {
      parent.MainIframe.location.reload();
      if(document.all) {
        e.keyCode = 0;
        e.returnValue = false;
      } else {
        e.cancelBubble = true;
        e.preventDefault();
      }
    }
  }
  
  function _attachEvent(obj, evt, func, eventobj) {
    eventobj = !eventobj ? obj : eventobj;
    if(obj.addEventListener) {
      obj.addEventListener(evt, func, false);
    } else if(eventobj.attachEvent) {
      obj.attachEvent('on' + evt, func);
    }
  }
  
  _attachEvent(document.documentElement, 'keydown', resetEscAndF5);
  _attachEvent(window, 'keydown', resetEscAndF5, parent.frames[0]);
  */
</script>
<SCRIPT type=text/javascript>
/*var lang='cn';
var atop='未购买商业授权';
var user_msg = new Array();
  user_msg['jsx1']='载入中...';
  user_msg['langtips1']='当前后台管理的网站语言：';
$(function(){
  var clang = $.cookie('clang');
  if(clang){
    window.location.href='index.php?lang='+clang;
    $.cookie('clang', null);
  }
});*/
//宽窄版切换
$(document).ready(function() {
  $("#kzqie").click(function(){
        var my=$(this);
    if(my.text()=='宽版'){
      $('#content,#top .topnbox').animate({ width: '99%'}, 380);
      $.ajax({url : 'include/config.php?lang=cn&met_kzqie=1',type: "POST"});
      my.attr('title','切换到窄版');
      my.text('窄版');
      setTimeout("topwidth(400)",100);
    }else{
      $('#content,#top .topnbox').animate({ width: '1000px'}, 380);
      $.ajax({url : 'include/config.php?lang=cn&met_kzqie=0',type: "POST"});
      my.attr('title','宽版');
      my.text('宽版');
      setTimeout("topwidth(400)",400);
    }
  });
});
</SCRIPT>
<STYLE>#content {
  WIDTH: 99%
}
#top .topnbox {
  WIDTH: 99%
}
/* #top .floatr LI A SPAN {
  BEHAVIOR: url(templates/met/images/iepngfix.htc)
} */
</STYLE>
<META content="MSHTML 6.00.2900.6550" name=GENERATOR></HEAD>
<BODY id=indexid>
<DIV id=metcmsbox>
<DIV id=top>
<DIV class=topnbox>
<DIV class=floatr>
<DIV class=top-r-box>
<DIV class=top-right-boxr>
<DIV class=top-r-t>
<OL class=rnav>
  <LI class=list>您好 <A class=tui id="mydata" href=""><?php echo session("admin_real_name");?></A> </LI>
  <LI class=line>| </LI>
  <LI class=list><A class=tui id=outhome title=退出 
  href="__URL__/logout" 
  target=_top>退出</A> </LI>
  <LI class=line>| </LI>
  <LI class=list><A id=kzqie title=切换到窄版 href="javascript:;">窄版</A> </LI>
  </OL>
<DIV class=clear></DIV><a  id="menu_16" class="suaxin" style=" color:#3b5999" hidefocus="true" onClick="switch_sub_menu('16', '/admin/global/cleanall.html');" href="javascript:void(0)"><P style="line-height:30px; margin-top:5px; background:#105488;width:80px; text-align:center; float:right; border-radius:15px;color:#fff;">清空缓存</P></a></DIV></DIV>
<DIV></DIV>
<DIV class="nav">
<UL id="topnav">
  <?php $i=1;foreach($menu_left as $keyt => $menu_1) {if($menu_1[2]==0) continue; ?>
  <LI class=list id="metnav_1">
    <!-- <A class="onnav" id="nav_1 hideFocus"   href="javascript:;"> -->
  <a id="channel_<?php echo $keyt; ?>" href="javascript:void(0)" hidefocus="true" style="outline:none;" class="cd_top_title <?php if($keyt==0){ ?>onnav<?php } ?>" />
      <SPAN class="c<?php echo $i; ?> png"></SPAN>
      <P ><?php echo $menu_1[0]; ?></P>
    </A>
  </LI>
  <?php $i++;} ?>
</UL></DIV></DIV></DIV>
<DIV class=floatl><A id="met_logo hideFocus" href=""><IMG src="__ROOT__/Public/web/img/guanyu/hou1.png"></A> </DIV>
  
</DIV></DIV>
<DIV id=content>
<DIV class="floatl" id="metleft">
<DIV class="floatl_box">
<DIV class="nav_list" id="leftnav">
<DIV class=fast><A href="__APP__" target="_blank">网站首页</A> </DIV>
<?php $i=0;foreach($menu_left as $key => $menu_1) { ?>
<UL class="de_left_nav" <?php if($i>0){ ?>style="DISPLAY: none"<?php } ?>>
  <!-- <LI>
    <A class="on de_left_nav_a_first" id=nav_1_8 hideFocus href="__URL__/home.html" target=MainIframe><?php echo $menu_1[0]; ?></A>
  </LI> -->
  <?php $j=0; foreach($menu_1['low_title'] as $key2 => $menu_2) { if($menu_2[2]==0) continue;?>
  <LI>
    <A id="" <?php if($j == 0): ?>class="de_left_nav_a_first"<?php endif; ?>  href="<?php echo $menu_1[$key2][0][1]; ?>" target="MainIframe"><?php echo $menu_2[0]; ?></A>
  </LI>
  <?php $j++;} ?>
</UL>
<?php $i++;} ?>
</DIV>
<DIV class="claer"></DIV>
<DIV class="left_footer">
<DIV class="left_footer_box"><A href="http://www.zihai0531.com" target=_blank style="display:none">联系开发团队</A></DIV></DIV></DIV></DIV>
<DIV class=floatr id="metright">
<DIV class="iframe">
<DIV class="min">
  <div class="metinfotop">
  <div class="position"><span style="float:left;">欢迎访问！</span><span style="float:right;margin-right:10px;">当前时间：<?php echo date('Y年m月d日',time()); ?></span></div>
  </div>
  <div class="clear"></div>
<script type="text/javascript">
//$("html",parent.document).find('.returnover').remove();
</script>
<div class="stat_list">
  
<?php foreach($menu_left as $key => $menu_1) { ?>
    <div class="de_right_div" <?php if($key>0){echo "style='display:none;'";} ?> >
<?php $i=0; foreach($menu_1['low_title'] as $key2 => $menu_2) { if($menu_2[2]==0) continue;?>
  <ul class="de_right_nav" <?php if($i>0){echo "style='display:none;'";} ?> >
  <?php $j=0; foreach($menu_1[$key2] as $key3 => $menu_3) { if($menu_3[2]==0) continue;?>
    <li>
      <a <?php if($j==0){ ?>class="a_on"<?php } ?> href="<?php echo ($menu_3['1']); ?>" target="MainIframe"><?php echo ($menu_3['0']); ?></a>
    </li>
    <?php $j++;} ?> 
  </ul>
    <?php $i++ ;} ?>   
  </div>  
<?php } ?>
</div>
<iframe onload="nof5()" id="MainIframe" name="MainIframe" src="__URL__/home.html" scrolling="yes" src="" width="100%" height="100%" frameborder="0" noresize></iframe>
<!-- 
<iframe onload="nof5()" id="MainIframe" name="MainIframe" scrolling="yes" src="" width="100%" height="100%" frameborder="0" noresize> </iframe>
 -->
</DIV></DIV></DIV>
<DIV class=clear></DIV></DIV></DIV>
<DIV class=footer>Powered by <B><A href="http://www.p2p.zihaistar.com/" 
target=_blank>铭万网络</A></B> ©20018-2022 &nbsp;<A 
href="http://www.p2p.zihaistar.com/" target=_blank>铭万网络</A></DIV>
<SCRIPT src="__ROOT__/Style/A20141030/js/metinfo.js" type=text/javascript></SCRIPT>
<script type="text/javascript">
  $(function(){
    $(".cd_top_title").each(function(index, el) {      
      $(this).click(function(event) {
        $('.stat_list').show();
        $(".cd_top_title").removeClass('onnav on');
        $(this).addClass('onnav on');
        $(".de_left_nav").hide().eq(index).show().children('li').children('a').removeClass('de_left_nav_a_first').parent('li').eq(0).children('a').addClass('de_left_nav_a_first'); 
        var href= $(".de_left_nav").eq(index).children('li').eq(0).children('a').attr('href');
        $("iframe#MainIframe").attr('src', href);
        $(".de_right_div").hide().eq(index).show();        
      });
    });
    $(".de_left_nav").each(function(index, el) {  
      $(this).children('li').each(function(i, el) {
        $(this).children('a').click(function(event) {
      //alert($(this).parent().index());
          $('.stat_list').show();
          $(".de_left_nav li a").removeClass('de_left_nav_a_first');
          $(this).addClass('de_left_nav_a_first'); 
          $(".de_right_div").eq(index).children('ul').hide();
          $(".de_right_div").hide();
          $(".de_right_div").eq(index).show();
          $(".de_right_div").eq(index).children('ul').eq($(this).parent("li").index()).children('li').siblings('li').children('a').removeClass('a_on');
          $(".de_right_div").eq(index).children('ul').eq($(this).parent("li").index()).children('li').children('a').eq(0).addClass('a_on');
          $(".de_right_div").eq(index).children('ul').eq($(this).parent("li").index()).show();
        });
      });
    });
    $(".de_right_nav li a").click(function(event) {
      $(".de_right_nav li a").removeClass("a_on");
      $(this).addClass('a_on');
    });
  });
</script>
<SCRIPT type=text/javascript>
/*
if(!$.cookie('upgraderemind')){
  var src=$("#MainIframe").attr("src");
  if(src.substring(0,15)=='system/sysadmin'){
    $.ajax({
      url: 'http://api.metinfo.cn/sysnew.php?ver=5.2.9&patch=11',
      type: 'GET',
      dataType: 'jsonp',
      jsonp: 'jsoncallback',
      cache: false,
      success: function(data) {
        if(data.metok==1){
          //alert(data.message);
          $("#MainIframe").attr("src",src+'&click=1');
        }
        if(data.metpatch==1&&1){
          $.ajax({
            url: '../include/interface/patch.php?action=patch',
            type: 'GET',
            cache: false,
            success: function(data) {
            }
          }); 
        }
      }
    }); 
  }
  $.cookie('upgraderemind', '1');
}
if(!$.cookie('appsynchronous')|| 1 == 0){
  $.ajax({
    url: 'app/dlapp/upapp.php',
    type: 'GET',
    cache: false,
    success: function(data) {
    }
  }); 
  $.cookie('appsynchronous', '1');
}*/
//$(window).unload(function () { alert("Bye now!"); });
/*function closediv(){
  var type=$('#update_content_control').html().split(',');
  $('#update_content_back').hide();
  $('#update_contents').hide();
  $("#MainIframe").contents().find("#oltest_"+type[0]+" a").eq(0).hide();
  $("#MainIframe").contents().find("#oltest_"+type[0]+" a").eq(1).show();
  $('html,body').css('overflow','auto');
}
*/
function zsyfunc(){
  $('#update_content_back').height($(document).height());
  $('#update_content_back').width($(document).width());
  $('#update_contents').css('left',$(window).width()/2-$('#update_contents').width()/2);
  $('#update_contents').css('top',$(window).height()/2-$('#update_contents').height()/2+$("html,body").scrollTop()+$("body").scrollTop());
}
*/
/*
function showdiv(){
  $('#update_content_back').show();
  $('#update_contents').show();
  $('html,body').css('overflow','hidden');
  zsyfunc();
  $("#update_content_back").css({ opacity: 0.7 });
  var type=$('#update_content_control').html().split(',');
  $.ajax({
    url: 'http://api.metinfo.cn/dl/olupdate_contents.php?type='+type[0]+'&ver='+type[1],
    type: 'GET',
    dataType: 'jsonp',
    jsonp: 'jsoncallback',
    cache: false,
    success: function(data) {
      //alert(data.message);
      $('#update_contents .content').html(data.message);
      $(".nano").nanoScroller({alwaysVisible: true});
    }
  }); 
}
function okdiv(olupdate_type){
  if(olupdate_type==1){
  alert("对不起！您的权限不够，只有创始人才能操作");
  return false;
  }
  var type=$('#update_content_control').html().split(',');
  $('#update_content_back').hide();
  $('#update_contents').hide();
  $("#MainIframe").contents().find("#oltest_"+type[0]+" a").eq(0).show();
  $("#MainIframe").contents().find("#oltest_"+type[0]+" a").eq(1).hide();
  $("#MainIframe").contents().find("#oltest_"+type[0]+" a").eq(0).click();
  $('html,body').css('overflow','auto');
}*/
$(window).on('resize', function(e) {
  zsyfunc();
});
</SCRIPT>
<!-- <DIV id=update_content_control style="DISPLAY: none" onclick=showdiv()></DIV>
<DIV id=update_content_back></DIV>
<DIV id=update_contents>
<H3 class=uptitle>在线升级</H3>
<DIV class=nano>
<DIV class=content></DIV></DIV>
<DIV class=clear></DIV>
<DIV class=botom><A onclick=okdiv(2) href="javascript:void(0)">立即升级</A> <A 
class=nosj onclick=closediv() href="javascript:void(0)">稍后升级</A> 
</DIV></DIV> --></BODY></HTML>