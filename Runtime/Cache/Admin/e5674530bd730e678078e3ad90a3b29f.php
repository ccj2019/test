<?php if (!defined('THINK_PATH')) exit();?><script type="text/javascript">
  $(function(){
      $(".ser_btn").click(function(){
         var url = "/admin/members/ajaxsearch.html";
         var user_name = $("#user_name").val();
         var real_name = $("#real_name").val();
         var user_phone = $("#user_phone").val();
         url = url + '?user_name='+user_name+'&real_name='+real_name+'&user_phone='+user_phone;            
         $("a.close").click();
         showurl(url,'搜索会员');          
         return false;
      });
      $(".Toolbar_inbox a").click(function(){
          var url = $(this).attr('href');
          $("a.close").click();
          showurl(url,'搜索会员');          
          return false;
      });
      $(".member_row").click(function(){
          var user_id = $(this).find('.user_id').val();
          var user_name = $(this).find('.user_name').val();          
          $("#borrow_uid").val(user_id);
          $("#borrow_username").val(user_name);
          $("#borrow_uids").val($("#borrow_uids").val()+","+user_id);
          $("#borrow_usernames").append('/'+user_name);

          $("a.close").click();
      });
       $(".guanbi").click(function(){
   

          $("a.close").click();
      });
      
  });
</script>
<style type="text/css">
  #area_list tr td{height: 25px;}
  .ser_btn{margin: 0px 10px;display: inline-block;padding: 5px;cursor: pointer;border: 1px solid #ABCDEF;border-radius: 3px;}
  .form_div{border-radius: 3px;border: 1px solid #ccc;margin-bottom: 10px;padding: 5px}
  .form_div input{width: 160px !important;}
  .member_row{cursor: pointer;}
  .member_row:hover{background: #d2dbea}
</style>
<div style="margin-top:100px;"></div>
<div class="so_main">
  <div class="page_tit">会员列表</div>
  <div class="form_div">          
      用户名:<input name="user_name" type="text" value="" id="user_name">
      真实姓名:<input name="real_name" type="text" value="" id="real_name">
      手机号:<input name="user_phone" type="text" value="" id="user_phone">
      <span class="ser_btn">搜索</span> <div class="guanbi" style="color:red;">关闭</div>
   </div>   
  
  <div class="list">
  <table id="area_list" width="750" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <th class="line_l">ID</th>
    <th class="line_l">用户名</th>
    <th class="line_l">真实姓名</th>
    <th class="line_l">所属客服</th>
    <th class="line_l">会员类型</th>
    <th class="line_l">可用余额</th>
    <th class="line_l">冻结金额</th>
    <th class="line_l">待收金额</th>   
  </tr>
  <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr overstyle='on' id="list_<?php echo ($vo["id"]); ?>" class="member_row" title="点击即选择此用户">        
        <td><?php echo ($vo["id"]); ?><input name="user_id" class="user_id" value="<?php echo ($vo["id"]); ?>" type="hidden">
        <input name="user_name" class="user_name" value="<?php echo ($vo["user_name"]); ?>" type="hidden">
        </td>
        <td><?php echo ($vo["user_name"]); ?></td>
        <td><?php echo (($vo["real_name"])?($vo["real_name"]):"&nbsp;"); ?></td>
        <td><?php echo (($vo["customer_name"])?($vo["customer_name"]):"&nbsp;"); ?></td>
        <td><?php echo (getlevename($vo["credits"],2)); ?></td>
        <td><?php echo (($vo["account_money"])?($vo["account_money"]):0); ?>元</td>
        <td><?php echo (($vo["money_freeze"])?($vo["money_freeze"]):0); ?>元</td>
        <td><?php echo (($vo["money_collect"])?($vo["money_collect"]):0); ?>元</td>        
      </tr><?php endforeach; endif; else: echo "" ;endif; ?>
  </table>
  </div>
  <div class="Toolbar_inbox">
    <div class="page right"><?php echo ($pagebar); ?></div>
    <div style="clear:both;"></div>
  </div>
</div>
<script type="text/javascript">
function showurl(url,Title){
  ui.box.load(url, {title:Title});
}
</script>