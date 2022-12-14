<?php if (!defined('THINK_PATH')) exit();?><style type="text/css">
.x_member_form {
    width:750px;
    overflow:hidden
}
.x_member_form .form2 {
    height:auto;
    overflow:auto;
}
.x_member_form .form2 .lineD {
    overflow:hidden
}
.x_member_form .form2 .lineD dt {
    width:150px;
    color:#9CB8CC;
    font-weight:bold
}
.x_member_form .form2 .lineD dd {
    width:200px;
    float:left;
    margin-left:0px
}
.x_member_form .form2 .lineD dd.xwidth {
    width:500px;
}
.x_member_form .form2 .lineD dt.title {
    color:#F00
}
</style>
<div class="so_main x_member_form">
    <div class="page_tit"><?php echo ($user); ?>详细信息1</div>
    <div class="page_tab"><span data="tab_1" class="active">会员资料</span><span data="tab_3">帐户资金</span><span data="tab_2">借款投资</span>
    </div>
    <div class="form2">
        <div id="tab_1">
            <dl class="lineD"><dt>用户名：</dt>
                <dd><?php echo ($vo["user_name"]); ?> | <?php echo ($vo["real_name"]); ?></dd><dt>认证情况：</dt>
                <dd  class="adelete"><?php echo (getverify($vo["uid"])); ?></dd>
                <script>
                    $(".adelete a").attr("href",'') ;
                </script>
            </dl>
            <dl class="lineD"><dt>是否冻结：</dt>
                <dd><?php echo ($vo["is_ban"]); ?></dd> <dt>年龄：</dt>
                <dd><?php echo ($vo["age"]); ?></dd>
            </dl> 
            <dl class="lineD"><dt>手机号码：</dt>
                <dd><?php echo (($vo["user_phone"])?($vo["user_phone"]):"未认证"); ?></dd><dt>身份证号：</dt>
                <dd><?php echo ($vo["idcard"]); ?></dd> 
            </dl> 
            <dl class="lineD"><dt>银行帐号：</dt>
                <dd><?php echo ($vo["bank_num"]); ?></dd><dt>银行名称：</dt>
                <dd><?php echo ($vo["bank_name"]); ?></dd>
            </dl>
            <dl class="lineD"><dt>银行开户行：</dt>
                <dd class="xwidth"><?php echo ($vo["bank_province"]); echo ($vo["bank_city"]); echo ($vo["bank_address"]); ?></dd>
            </dl>
      
        </div>
        <!--tab1-->
        <div id="tab_2" style="display:none">
            <dl class="lineD"><dt class="title">借款金额统计</dt>
                <dd class="xwidth">&nbsp;</dd>
            </dl>
            <dl class="lineD"><dt>借款总额：</dt>
                <dd><?php echo (fmoney($capitalinfo["tj"]["jkze"])); ?></dd><dt>负债情况：</dt>
                <dd>
                    <?php if($capitalinfo['tj']['fz'] < 0): ?>借出小于借入(<?php echo (fmoney($capitalinfo["tj"]["fz"])); ?>)
                        <?php else: ?>借出大于借入(<?php echo (fmoney($capitalinfo["tj"]["fz"])); ?>)<?php endif; ?>
                </dd>
            </dl>
            <dl class="lineD"><dt>已还总额：</dt>
                <dd><?php echo (fmoney($capitalinfo["tj"]["yhze"])); ?></dd><dt>待还总额：</dt>
                <dd><?php echo (fmoney($capitalinfo["tj"]["dhze"])); ?></dd>
            </dl>
            <dl class="lineD"><dt class="title">借还款次数统计</dt>
                <dd class="xwidth">&nbsp;</dd>
            </dl>
            <dl class="lineD"><dt>借款成功次数：</dt>
                <dd><?php echo (($capitalinfo["tj"]["jkcgcs"])?($capitalinfo["tj"]["jkcgcs"]):0); ?>次</dd><dt>正常还款次数：</dt>
                <dd><?php echo (($capitalinfo["repayment"]["1"]["num"])?($capitalinfo["repayment"]["1"]["num"]):0); ?>次</dd>
            </dl>
            <dl class="lineD"><dt>提前还款次数：</dt>
                <dd><?php echo (($capitalinfo["repayment"]["2"]["num"])?($capitalinfo["repayment"]["2"]["num"]):0); ?>次</dd><dt>逾期还款次数：</dt>
                <dd><?php echo (($capitalinfo["repayment"]["5"]["num"])?($capitalinfo["repayment"]["5"]["num"]):0); ?>次</dd>
            </dl>
            <dl class="lineD"><dt>迟还次数：</dt>
                <dd><?php echo (($capitalinfo["repayment"]["3"]["num"])?($capitalinfo["repayment"]["3"]["num"]):0); ?>次</dd><dt>网站代还次数：</dt>
                <dd><?php echo (($capitalinfo["repayment"]["4"]["num"])?($capitalinfo["repayment"]["4"]["num"]):0); ?>次</dd>
            </dl>
            <dl class="lineD"><dt class="title">投资统计</dt>
                <dd class="xwidth">&nbsp;</dd>
            </dl>
            <dl class="lineD"><dt>投资总额：</dt>
                <dd><?php echo (fmoney($capitalinfo["tj"]["jcze"])); ?></dd><dt>投标奖励：</dt>
                <dd><?php echo (fmoney($capitalinfo["tj"]["tbjl"])); ?></dd>
            </dl>
            <dl class="lineD"><dt>已收总额：</dt>
                <dd><?php echo (fmoney($capitalinfo["tj"]["ysze"])); ?></dd><dt>待收总额：</dt>
                <dd><?php echo (fmoney($capitalinfo["tj"]["dsze"])); ?></dd>
            </dl>
        </div>
        <!--tab3-->
        <div id="tab_3" style="display:none">
            <dl class="lineD"><dt class="title">资金情况</dt>
                <dd class="xwidth">&nbsp;</dd>
            </dl>
            <dl class="lineD"><dt>帐户总额：</dt>
                <dd><?php echo (fmoney($vo["money_all"])); ?></dd><dt>待收金额：</dt>
                <dd><?php echo (fmoney($vo["money_collect"])); ?></dd>
            </dl>
            <dl class="lineD"><dt>可用余额：</dt>
                <dd><?php echo ($vo['account_money']+$vo['back_money']); ?></dd><dt>冻结金额：</dt>
                <dd><?php echo (fmoney($vo["money_freeze"])); ?></dd>
            </dl>
            <dl class="lineD"><dt class="title">充值提现</dt>
                <dd class="xwidth">&nbsp;</dd>
            </dl>
            <dl class="lineD"><dt>网站充值次数：</dt>
                <dd><?php echo (($wc["E"]["num"])?($wc["E"]["num"]):0); ?>次</dd><dt>网站充值金额：</dt>
                <dd><?php echo (fmoney($wc["E"]["money"])); ?></dd>
            </dl>
            <dl class="lineD"><dt>线上充值次数：</dt>
                <dd><?php echo (($wc["C"]["num"])?($wc["C"]["num"]):0); ?>次</dd><dt>线上充值金额：</dt>
                <dd><?php echo (fmoney($wc["C"]["money"])); ?></dd>
            </dl>
            <dl class="lineD"><dt>线下充值次数：</dt>
                <dd><?php echo (($wc["D"]["num"])?($wc["D"]["num"]):0); ?>次</dd><dt>线下充值金额：</dt>
                <dd><?php echo (fmoney($wc["D"]["money"])); ?></dd>
            </dl>
            <dl class="lineD"><dt>提现成功次数：</dt>
                <dd><?php echo (($wc["W"]["num"])?($wc["W"]["num"]):0); ?>次</dd><dt>提现成功金额：</dt>
                <dd><?php echo (fmoney($wc["W"]["money"])); ?></dd>
            </dl>
            <dl class="lineD"><dt class="title">额度情况</dt>
                <dd class="xwidth">&nbsp;</dd>
            </dl>
            <dl class="lineD"><dt>借款信用额度：</dt>
                <dd><?php echo (fmoney($vo["credit_limit"])); ?></dd><dt>可用信用额度：</dt>
                <dd><?php echo (fmoney($vo["credit_cuse"])); ?></dd>
            </dl>
        </div>
        <!--tab3-->
        <div class="page_btm">
            <input type="submit" class="btn_b" value="关闭" onclick="closeui();" />
        </div>
    </div>
</div>
<script type="text/javascript">
function closeui() {
    ui.box.close();
}
</script>
<script type="text/javascript" src="__ROOT__/Style/A/js/adminbase.js"></script>