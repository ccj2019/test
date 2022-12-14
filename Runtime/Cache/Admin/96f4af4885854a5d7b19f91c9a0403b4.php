<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>管理员登陆</title><!-- <script type="text/javascript" src="__ROOT__/Style/A/js/jquery.js"></script>
<link rel="shortcut icon" href="__PUBLIC__/images/favicon.ico" />
<link rel="bookmark"href="__PUBLIC__/images/favicon.ico" />
<link href="__ROOT__/Style/file/a/style/style.css" rel="stylesheet" type="text/css" /> -->
<script src="__ROOT__/Style/file/a/js/jquery-1.8.0.min.js" type="text/javascript" ></script>
<link rel="stylesheet" type="text/css" href="__ROOT__/Style/file/a/css/register.css"/>
</head>
<body>
<div class='signup_container'>
    <h1 class='signup_title'>互联网金融系统</h1>
    <img src='__ROOT__/Style/file/a/images/people.png' id='admin'/>
<div id="signup_forms" class="signup_forms clearfix">
              <form method="post" action="" name="form" id="form">
              <table border="0" cellspacing="0" cellpadding="0" width="100%" height="250">
                  <tr>
                    <td colspan="2">                      
                      <input id="admin_name" name="admin_name" type="text" class="spec1" placeholder="" />
                    </td>
                  </tr>
                  <tr>
                    <td colspan="2">
                    <input type="password" id="admin_pass" name="admin_pass" class="spec1" placeholder="" />
                    </td>
                  </tr>
                  <tr>
                    <td colspan="2">
                    <input id="user_word" name="user_word" type="text" class="spec1" placeholder="" />
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <input  id="code" name="code" maxlength="4" type="text" class="spec2" placeholder="验证码" />
                    </td>
                    <td align="right" class="spec3"><img  src="__URL__/verify" onclick="javascript:this.src='__URL__/verify?'+new Date().getTime()" width="90" height="40"  /></td>
                  </tr>
                  <tr>
                    <td colspan="2" class="spec4">
                    <a  id="btnReg" href="javascript:subform();" onclick="javascript:subform();" onfocus="this.blur();" >登录</a>
                    </td>
                  </tr>
                </table>
      
         </form>
    </div>
    <p class='copyright'>版权所有 互联网金融系统 V10.1管理中心</p>
</div>
<!-- <div class="box">
  <div class="spec1">后台管理系统</div>
    <form method="post" action="__URL__/login" name="form" id="form">
      <input id="admin_name" name="admin_name" type="text" class="spec2" placeholder="用户名" />
        <input type="password" id="admin_pass" name="admin_pass" class="spec2" placeholder="密码" />
        <input id="user_word" name="user_word" type="text" class="spec2" placeholder="口令" />
        <input  id="code" name="code" maxlength="4" type="text" class="spec3" placeholder="验证码" />
        <div class="spec4"><img  src="__URL__/verify" onclick="javascript:this.src='__URL__/verify?'+new Date().getTime()" width="90" height="40"  /></div>
        <div style="clear:both;"></div>
        <div class="spec5"><a  id="btnReg" href="javascript:void(0)" onclick="javascript:subform();" onfocus="this.blur();" >登录</a></div>
    </form>
</div> -->
<script type="text/javascript">
function subform(){
  var frm = document.forms['form'];
    frm.submit();
}
function keyUp(e) {  
           var currKey=0,e=e||event; 
            currKey=e.keyCode||e.which||e.charCode;
  if(currKey==13){
 document.getElementById("btnReg").click();
     }
          } 
   document.getElementById("form").onkeydown = keyUp;
   </script>
</body>
</html>