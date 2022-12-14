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
<script type="text/javascript">
  var delUrl = '__URL__/doDel';
  var addUrl = '__URL__/add';
  var editUrl = '__URL__/edit';
  var editTitle = '修改会员类型';
  var isSearchHidden = 1;
  var searchName = "搜索/筛选会员";
</script>
<div class="so_main">
  <div class="page_tit">会员列表</div>
<!--搜索/筛选会员-->
    <div id="search_div" style="display:none">
    <div class="page_tit"><script type="text/javascript">document.write(searchName);</script> [ <a href="javascript:void(0);" onclick="dosearch();">隐藏</a> ]</div>
  
  <div class="form2">
  <form method="get" action="__URL__/index">
    <?php if($search["customer_id"] > 0): ?><input type="hidden" name="customer_id" value="<?php echo ($search["customer_id"]); ?>" /><?php endif; ?>
   <dl class="lineD">
      <dt>是否为VIP：</dt>
      <dd>
       <?php $i=0;$___KEY=array ( 'yes' => '是', 'no' => '否', ); foreach($___KEY as $k=>$v){ if(strlen("1key")==1 && $i==0){ ?><input type="radio" name="is_vip" value="<?php echo ($k); ?>" id="is_vip_<?php echo ($i); ?>" checked="checked" /><?php }elseif(("key1"=="key1"&&$search["is_vip"]==$k)||("key"=="value"&&$search["is_vip"]==$v)){ ?><input type="radio" name="is_vip" value="<?php echo ($k); ?>" id="is_vip_<?php echo ($i); ?>" checked="checked" /><?php }else{ ?><input type="radio" name="is_vip" value="<?php echo ($k); ?>" id="is_vip_<?php echo ($i); ?>" /><?php } ?><label for="is_vip_<?php echo ($i); ?>"><?php echo ($v); ?></label>&nbsp;&nbsp;&nbsp;&nbsp;<?php $i++; } ?><span id="tip_is_vip" class="tip">不填则不限制</span>
        <span>不填则不限制</span>
      </dd>
    </dl>
   <dl class="lineD">
      <dt>会员名：</dt>
      <dd>
        <input name="uname" style="width:190px" id="title" type="text" value="<?php echo ($search["uname"]); ?>">
        <span>不填则不限制</span>
      </dd>
    </dl>
    <dl class="lineD">
      <dt>真实姓名：</dt>
      <dd>
        <input name="realname" style="width:190px" id="title" type="text" value="<?php echo ($search["realname"]); ?>">
        <span>不填则不限制</span>
      </dd>
    </dl>
    <dl class="lineD">
      <dt>所属客服：</dt>
      <dd>
        <input name="customer_name" style="width:190px" id="title" type="text" value="<?php echo ($search["customer_name"]); ?>">
        <span>不填则不限制</span>
      </dd>
    </dl>
    <dl class="lineD">
      <dt>手机：</dt>
      <dd>
        <input name="user_phone" style="width:190px" id="title" type="text" value="<?php echo ($search["user_phone"]); ?>">
        <span>不填则不限制</span>
      </dd>
    </dl>
  
    <dl class="lineD">
      <dt>余额：</dt>
      <dd>
      <select name="lx" id="lx" style="width:100px"  class="c_select"><option value="">--请选择--</option><?php foreach($lx as $key=>$v){ if($search["lx"]==$key && $search["lx"]!=""){ ?><option value="<?php echo ($key); ?>" selected="selected"><?php echo ($v); ?></option><?php }else{ ?><option value="<?php echo ($key); ?>"><?php echo ($v); ?></option><?php }} ?></select>
      <select name="bj" id="bj" style="width:80px"  class="c_select"><option value="">--请选择--</option><?php foreach($bj as $key=>$v){ if($search["bj"]==$key && $search["bj"]!=""){ ?><option value="<?php echo ($key); ?>" selected="selected"><?php echo ($v); ?></option><?php }else{ ?><option value="<?php echo ($key); ?>"><?php echo ($v); ?></option><?php }} ?></select>
      <input name="money" id="money" style="width:100px" class="input" type="text" value="<?php echo ($search["money"]); ?>" >
        <span>不填则不限制</span>
      </dd>
    </dl>
  <dl class="lineD"><dt>注册时间(开始)：</dt><dd><input onclick="WdatePicker({maxDate:'#F{$dp.$D(\'end_time\')||\'2020-10-01\'}',startDate:'%y-%M-%d 00:00:00',dateFmt:'yyyy-MM-dd HH:mm:ss',alwaysUseStartDate:true});" name="start_time" id="start_time"  class="input Wdate" type="text" value="<?php echo (mydate('Y-m-d H:i:s',$search["start_time"])); ?>"><span id="tip_start_time" class="tip">只选开始时间则查询从开始时间往后所有</span></dd></dl>
  <dl class="lineD"><dt>注册时间(结束)：</dt><dd><input onclick="WdatePicker({minDate:'#F{$dp.$D(\'start_time\')}',maxDate:'2020-10-01',startDate:'%y-%M-%d 23:59:59',dateFmt:'yyyy-MM-dd HH:mm:ss',alwaysUseStartDate:true});" name="end_time" id="end_time"  class="input Wdate" type="text" value="<?php echo (mydate('Y-m-d H:i:s',$search["end_time"])); ?>"><span id="tip_end_time" class="tip">只选结束时间则查询从结束时间往前所有</span></dd></dl>
    <div class="page_btm">
      <input type="submit" class="btn_b" value="确定" />
    </div>
  </form>
  </div>
  </div>
<!--搜索/筛选会员-->
  <div class="Toolbar_inbox">
    <div class="page right"><?php echo ($pagebar); ?></div>
    <a onclick="dosearch();" class="btn_a" href="javascript:void(0);"><span class="search_action">搜索/筛选会员</span></a>
  <!--  <a onclick="del();" class="btn_a" href="javascript:void(0);"><span>删除会员(谨慎操作)</span></a>-->
   <a class="btn_a" href="__URL__/export?<?php echo ($query); ?>"><span>将当前条件下数据导出为Excel</span></a>
  </div>
  
  <div class="list">
  <table id="area_list" width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <th style="width:30px;">
        <input type="checkbox" id="checkbox_handle" onclick="checkAll(this)" value="0">
        <label for="checkbox"></label>
    </th>
    
    <!--ID ；用户名；真实姓名；身份证号；收货人姓名；收货人地址；收货人电话；账户总金额（可用+冻结+代收）；可用余额；冻结金额；代收金额；积分；体验金余额；累计充值；投资项目个数；投资项目总额；项目分红个数；项目分红总额；-->
    <th class="line_l">ID</th>
    <th class="line_l">用户名</th>
    <th class="line_l">真实姓名</th>
    <th class="line_l">手机号</th>
    <th class="line_l">收货人姓名</th>
    <th class="line_l">收货人地址</th>
    <th class="line_l">收货人电话</th>
    <th class="line_l">账户总金额</th>
    <th class="line_l">可用余额</th>
    <th class="line_l">冻结金额</th>
    <th class="line_l">可用鱼币</th>
    <th class="line_l">冻结鱼币</th>

    <th class="line_l">待收金额</th>
    <th class="line_l">积分</th>
    <th class="line_l">体验金余额</th>
    <th class="line_l">累计充值</th>
    <th class="line_l">投资项目个数</th>
    <th class="line_l">投资项目总额</th>
    <th class="line_l">项目分红个数</th>
   	<th class="line_l">项目分红总额</th>
    <th class="line_l">操作</th>
  <!--   <th class="line_l">所属客服</th>
   <th class="line_l">会员类型</th>-->
    <!--<th class="line_l">可用余额</th>
    <th class="line_l">体验金</th>
    <th class="line_l">冻结金额</th>
    <th class="line_l">待收金额</th>
    <th class="line_l">注册时间</th>
    <th class="line_l">注册IP</th>
    <th class="line_l">最近登录时间</th>
    <th class="line_l">最近登录IP</th>
    <th class="line_l">操作</th>-->
  </tr>
  <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr overstyle='on' id="list_<?php echo ($vo["id"]); ?>">
        <td><input type="checkbox" name="checkbox" id="checkbox2" onclick="checkon(this)" value="<?php echo ($vo["id"]); ?>"></td>
        <td><?php echo ($vo["id"]); ?></td>
        <td><a onclick="loadUser(<?php echo ($vo["id"]); ?>,'<?php echo ($vo["user_name"]); ?>')" href="javascript:void(0);"><?php echo ($vo["user_name"]); ?></a></td>
        <td><?php echo (($vo["real_name"])?($vo["real_name"]):"&nbsp;"); ?></td>
        <td><?php echo (($vo["user_phone"])?($vo["user_phone"]):"&nbsp;"); ?></td>
        <!--收货信息-->
        <!--id
uid
name
main_phone
secondary_phone
address
post_code
address_type
province
city
district
default
-->
        <td><?php echo (($vo["member_address"]["name"])?($vo["member_address"]["name"]):"&nbsp;"); ?></td>
        <td><?php echo (($vo["member_address"]["province"])?($vo["member_address"]["province"]):"&nbsp;"); echo (($vo["member_address"]["city"])?($vo["member_address"]["city"]):"&nbsp;"); echo (($vo["member_address"]["district"])?($vo["member_address"]["district"]):"&nbsp;"); echo (($vo["member_address"]["address"])?($vo["member_address"]["address"]):"&nbsp;"); ?></td>
        <td><?php echo (($vo["member_address"]["main_phone"])?($vo["member_address"]["main_phone"]):"&nbsp;"); ?></td>
        
        
        <td><?php echo ($vo['account_money']+$vo['money_freeze']+$vo['money_collect']); ?></td>
        <td><?php echo (($vo["account_money"])?($vo["account_money"]):"0.00"); ?></td>
         <td><?php echo (($vo["money_freeze"])?($vo["money_freeze"]):"0.00"); ?></td>

         <td><?php echo (($vo["yubi"])?($vo["yubi"]):"0.00"); ?></td>
         <td><?php echo (($vo["yubi_freeze"])?($vo["yubi_freeze"]):"0.00"); ?></td>

        <td><?php echo (($vo["money_collect"])?($vo["money_collect"]):"0.00"); ?></td>
       
        <td><?php echo (($vo["credits"])?($vo["credits"]):"0"); ?></td>
        <td><?php echo (($vo["money_experience"])?($vo["money_experience"]):"0.00"); ?></td>
        <td><?php echo (($vo["leijichongzhi"])?($vo["leijichongzhi"]):"0.00"); ?></td>
        <td><?php echo (($vo["tzxmgs"])?($vo["tzxmgs"]):"0"); ?></td>
        <td><?php echo (($vo["tzxmze"])?($vo["tzxmze"]):"0.00"); ?></td>
        <td><?php echo (($vo["xmfxgs"])?($vo["xmfxgs"]):"0"); ?></td>
        <td><?php echo (($vo["xmfhze"])?($vo["xmfhze"]):"0.00"); ?></td>
 
 				<td><a href="__URL__/initpwd?id=<?php echo ($vo['id']); ?>" onclick="return confirm('确认要初始化会员：<?php echo ($vo["user_name"]); ?>的登录密码吗？');">[初始化密码]</a>&nbsp;&nbsp; 
            <a href="javascript:;" onclick="showurl('__URL__/moneyedit?id=<?php echo ($vo['id']); ?>','调整余额');">[调整余额]</a>&nbsp;&nbsp; 
            <?php if($vo["user_leve"] == 0): ?><a href="__URL__/vipedit?id=<?php echo ($vo['id']); ?>&viptype=1" style="display:none">[开启发标]</a>&nbsp;&nbsp; 
            <?php else: ?>
              <a href="__URL__/vipedit?id=<?php echo ($vo['id']); ?>&viptype=0" style="display:none">[关闭发标]</a>&nbsp;&nbsp;<?php endif; ?>
            <!--<a href="javascript:;" onclick="showurl('__URL__/leveedit?id=<?php echo ($vo['id']); ?>','升级会员');">[升级会员]</a>&nbsp;&nbsp; -->
            <a href="__URL__/edit?id=<?php echo ($vo["id"]); ?>">[修改信息]</a>
			<a href="__URL__/member_capital?id=<?php echo ($vo["id"]); ?>">[资金日志]</a>
			</td>
       <!--  <td><?php echo (($vo["customer_name"])?($vo["customer_name"]):"&nbsp;"); ?></td>
       <td><?php echo (getlevename($vo["all_money"],2)); ?></td>-->
        <!--<td><?php echo (($vo["account_money"])?($vo["account_money"]):0); ?>元</td>
        <td><?php echo (($vo["money_experience"])?($vo["money_experience"]):0); ?>元</td>
        <td><?php echo (($vo["money_freeze"])?($vo["money_freeze"]):0); ?>元</td>
        <td><?php echo (($vo["money_collect"])?($vo["money_collect"]):0); ?>元</td>
        <td><?php echo (date("Y-m-d H:i",$vo["reg_time"])); ?></td>
         <td><?php echo ($vo["reg_ip"]); ?></td>
         <td> <?php if($vo["last_time"] != ''): echo (date("Y-m-d H:i",$vo["last_time"])); endif; ?></td>
         <td><?php echo ($vo["last_ip"]); ?></td>
        <td><a href="__URL__/initpwd?id=<?php echo ($vo['id']); ?>" onclick="return confirm('确认要初始化会员：<?php echo ($vo["user_name"]); ?>的登录密码吗？');">[初始化密码]</a>&nbsp;&nbsp; 
            <a href="javascript:;" onclick="showurl('__URL__/moneyedit?id=<?php echo ($vo['id']); ?>','调整余额');">[调整余额]</a>&nbsp;&nbsp; 
            <?php if($vo["user_leve"] == 0): ?><a href="__URL__/vipedit?id=<?php echo ($vo['id']); ?>&viptype=1" style="display:none">[开启发标]</a>&nbsp;&nbsp; 
            <?php else: ?>
              <a href="__URL__/vipedit?id=<?php echo ($vo['id']); ?>&viptype=0" style="display:none">[关闭发标]</a>&nbsp;&nbsp;<?php endif; ?>
            <a href="javascript:;" onclick="showurl('__URL__/leveedit?id=<?php echo ($vo['id']); ?>','升级会员');">[升级会员]</a>&nbsp;&nbsp; 
            <a href="__URL__/edit?id=<?php echo ($vo["id"]); ?>">[修改信息]</a>
      <!--<a href="__URL__/idcardedit?id=<?php echo ($vo["id"]); ?>" >[身份证代传]</a>
        </td>-->
      </tr><?php endforeach; endif; else: echo "" ;endif; ?>
  </table>
  </div>
  
  <div class="Toolbar_inbox">
    <div class="page right"><?php echo ($pagebar); ?></div>
    <a onclick="dosearch();" class="btn_a" href="javascript:void(0);"><span class="search_action">搜索/筛选会员</span></a>
    <!--<a onclick="del();" class="btn_a" href="javascript:void(0);"><span>删除会员(谨慎操作)</span></a>-->
  </div>
</div>
<script type="text/javascript">
function showurl(url,Title){
  ui.box.load(url, {title:Title});
}
</script>
<script type="text/javascript" src="__ROOT__/Style/A/js/adminbase.js"></script>
</body>
</html>