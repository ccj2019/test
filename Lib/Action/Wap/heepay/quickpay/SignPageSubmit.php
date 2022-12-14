<?php
/* *
 * 功能：快捷支付网关签约(接口名：heepay.agreement.bank.sign.page)接口调试入口页面
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 请确保项目文件有可写权限，不然打印不了日志。
 */

header("Content-type: text/html; charset=utf-8");
require dirname ( __FILE__ ).DIRECTORY_SEPARATOR.'./../config.php';
require dirname ( __FILE__ ).DIRECTORY_SEPARATOR.'./../HeepayTradeService.php';
require_once dirname ( __FILE__ ).DIRECTORY_SEPARATOR.'model/SignPageContentBuilder.php';

if (!empty($_POST['out_trade_no'])&& trim($_POST['out_trade_no'])!=""){
    //商户订单号，商户网站订单系统中唯一订单号，必填
    $out_trade_no = $_POST['out_trade_no'];

    //手机号
    $mobile = $_POST['mobile'];

    //证件号
    $cert_no = $_POST['cert_no'];

    //账户名
    $bank_user_name = $_POST['bank_user_name'];

    //账户号
    $bank_card_no = $_POST['bank_card_no'];

    //订单时间
    $out_trade_time="2022-08-29 12:00:00";

    $payRequestBuilder = new SignPageContentBuilder();
    $payRequestBuilder->setOutTradeNo($out_trade_no);
    $payRequestBuilder->setOutTradeTime($out_trade_time);
    $payRequestBuilder->setMerchUserId($mobile);
    $payRequestBuilder->setDeviceInfo($mobile);

    $payResponse = new HeepayTradeService($config);
    $result=$payResponse->SignPageSubmit($payRequestBuilder);

   // echo "响应的解密签约URL=".$result->data->sign_url;
    $signUrl = $result->data->sign_url;
    var_dump($signUrl);exit;

    echo "<script language='javascript'>location.href='$signUrl';</script>";
    exit();
    return ;
}

?>
<!DOCTYPE html>
<html>
	<head>
	<title>快捷支付网关签约(接口名：heepay.agreement.bank.sign.page)</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<style>
    *{
        margin:0;
        padding:0;
    }
    ul,ol{
        list-style:none;
    }
    body{
        font-family: "Helvetica Neue",Helvetica,Arial,"Lucida Grande",sans-serif;
    }
    .hidden{
        display:none;
    }
    .new-btn-login-sp{
        padding: 1px;
        display: inline-block;
        width: 75%;
    }
    .new-btn-login {
        background-color: #02aaf1;
        color: #FFFFFF;
        font-weight: bold;
        border: none;
        width: 100%;
        height: 30px;
        border-radius: 5px;
        font-size: 16px;
    }
    #main{
        width:100%;
        margin:0 auto;
        font-size:14px;
    }
    .red-star{
        color:#f00;
        width:10px;
        display:inline-block;
    }
    .null-star{
        color:#fff;
    }
    .content{
        margin-top:5px;
    }
    .content dt{
        width:100px;
        display:inline-block;
        float: left;
        margin-left: 20px;
        color: #666;
        font-size: 13px;
        margin-top: 8px;
    }
    .content dd{
        margin-left:120px;
        margin-bottom:5px;
    }
    .content dd input {
        width: 85%;
        height: 28px;
        border: 0;
        -webkit-border-radius: 0;
        -webkit-appearance: none;
    }
    #foot{
        margin-top:10px;
        position: absolute;
        bottom: 15px;
        width: 100%;
    }
    .foot-ul{
        width: 100%;
    }
    .foot-ul li {
        width: 100%;
        text-align:center;
        color: #666;
    }
    .note-help {
        color: #999999;
        font-size: 12px;
        line-height: 130%;
        margin-top: 5px;
        width: 100%;
        display: block;
    }
    #btn-dd{
        margin: 20px;
        text-align: center;
    }
    .foot-ul{
        width: 100%;
    }
    .one_line{
        display: block;
        height: 1px;
        border: 0;
        border-top: 1px solid #eeeeee;
        width: 100%;
        margin-left: 20px;
    }
    .am-header {
        display: -webkit-box;
        display: -ms-flexbox;
        display: box;
        width: 100%;
        position: relative;
        padding: 7px 0;
        -webkit-box-sizing: border-box;
        -ms-box-sizing: border-box;
        box-sizing: border-box;
        background: #1D222D;
        height: 50px;
        text-align: center;
        -webkit-box-pack: center;
        -ms-flex-pack: center;
        box-pack: center;
        -webkit-box-align: center;
        -ms-flex-align: center;
        box-align: center;
    }
    .am-header h1 {
        -webkit-box-flex: 1;
        -ms-flex: 1;
        box-flex: 1;
        line-height: 18px;
        text-align: center;
        font-size: 18px;
        font-weight: 300;
        color: #fff;
    }
</style>
</head>
<body text=#000000 bgColor="#ffffff" leftMargin=0 topMargin=4>
<header class="am-header">
        <h1>快捷支付网关签约(接口名：heepay.agreement.bank.sign.page)</h1>
</header>
<div id="main">
        <form name=alipayment action='' method=post target="_blank">
            <div id="body" style="clear:left">
                <dl class="content">
                    <dt>商户订单号：</dt>
                    <dd>
                        <input id="out_trade_no" name="out_trade_no" />
                    </dd>
                    <hr class="one_line">
                    <dt>手机号：</dt>
                    <dd>
                        <input id="mobile" name="mobile" />
                    </dd>
                    <hr class="one_line">
                    <dt>身份证号：</dt>
                    <dd>
                        <input id="cert_no" name="cert_no" />
                    </dd>
                    <hr class="one_line">
                    <dt>账户名：</dt>
                    <dd>
                        <input id="bank_user_name" name="bank_user_name" />
                    </dd>
                    <hr class="one_line">
                    <dt>账户号：</dt>
                    <dd>
                        <input id="bank_card_no" name="bank_card_no" />
                    </dd>
                    <hr class="one_line">
                    <dt></dt>
                    <dd id="btn-dd">
                        <span class="new-btn-login-sp">
                            <button class="new-btn-login" type="submit" style="text-align:center;">确 认</button>
                        </span>
                        <span class="note-help">如果您点击“确认”按钮，即表示您同意该次的执行操作。</span>
                    </dd>
                </dl>
            </div>
		</form>
        <div id="foot">
			<ul class="foot-ul">
				<li>
					汇付宝
				</li>
			</ul>
		</div>
	</div>
</body>
<script language="javascript">
	function GetDateNow() {
		var vNow = new Date();
		var sNow = "";
		sNow += String(vNow.getFullYear());
		sNow += String(vNow.getMonth() + 1);
		sNow += String(vNow.getDate());
		sNow += String(vNow.getHours());
		sNow += String(vNow.getMinutes());
		sNow += String(vNow.getSeconds());
		sNow += String(vNow.getMilliseconds());
		document.getElementById("out_trade_no").value =  sNow;
		document.getElementById("mobile").value = "11111111111";
		document.getElementById("cert_no").value = "111111111111111111";
        document.getElementById("bank_user_name").value = "张三";
        document.getElementById("bank_card_no").value = "6211111111111111111";
	}
	GetDateNow();
</script>
</html>