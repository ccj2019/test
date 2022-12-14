<?php if (!defined('THINK_PATH')) exit();?><script type="text/javascript">
  $(function(){
      $(".ser_btn").click(function(){
         var url = "/admin/zengpin/ajaxsearch.html";
         var title = $("#title").val();
         url = url + '?title='+title;
         $("a.close").click();
         showurl(url,'搜索赠品');
         return false;
      });
      $(".Toolbar_inbox a").click(function(){
          var url = $(this).attr('href');
          $("a.close").click();
          showurl(url,'搜索赠品');
          return false;
      });
      $(".member_row").click(function(){

          var zpid = $(this).find('.zpid').val();
          var title = $(this).find('.title').val();

          $("#zpid").val(zpid);
          $("#zengping_name").val(title);

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
  <div class="page_tit">赠品列表</div>
  <div class="form_div">          
      赠品名称:<input name="title" type="text" value="" id="title">
      <span class="ser_btn">搜索</span> <div class="guanbi" style="color:red;">关闭</div>
   </div>   
  
  <div class="list">
  <table id="area_list" width="750" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <th class="line_l">ID</th>
    <th class="line_l">名称</th>
    <th class="line_l">价格</th>
    <th class="line_l">折现价格</th>
    <th class="line_l">添加时间</th>
  </tr>
  <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr overstyle='on' id="list_<?php echo ($vo["id"]); ?>" class="member_row" title="点击即选择此赠品">
        <td><?php echo ($vo["id"]); ?>
          <input name="id" class="zpid" value="<?php echo ($vo["id"]); ?>" type="hidden">
          <input name="title" class="title" value="<?php echo ($vo["title"]); ?>" type="hidden">
        </td>
        <td><?php echo ($vo["title"]); ?></td>
        <td><?php echo ($vo["price"]); ?></td>
        <td><?php echo ($vo["zxprice"]); ?></td>
        <td><?php echo (date('Y-m-d',$vo["add_time"])); ?></td>

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