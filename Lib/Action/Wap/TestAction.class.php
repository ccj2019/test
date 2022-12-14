<?php

class TestAction extends Action
{


    public function data(){
        
        $wechat = new WechattestAction();
        $res = $wechat->ttt($_REQUEST['ordernums']);
        var_dump($res);
    }
    public function test()
    {
        vendor('Alipawap.aop.request.AlipayTradeAppPayRequest');
        vendor('Alipawap.aop.AopClient');

        $aop = new AopClient ();

        $aop->gatewayUrl = 'https://openapi.alipay.com/gateway.do';
        $aop->appId = '2019111569140663';//你的appid
        $aop->rsaPrivateKey = "MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQCwWVsFhlcuUYAVJGIR8tgB8eH+IMYp0dReowkzY0Uv7Lk2pDJCfTN6K9jnVva8ZNCJ9zPW6A8qKOjX54YIwKIEmFdWgkCpN7rJq6VrPxP1f+ReJUhSDDuMbtsDQazrLupwkAis7hcLpFs74IgGmozQy5udui6qCgfkFoC1pOMjj30RewRDg6g7CXsoDp0faE8hjlv+dYuMPKAXS88ESWQTDq5gCGj9IjmiDznCByct3iduvPpxWC72nFxlGfpOfWQGTYA9TtKux3pLE79TS2ZF5AYDfrScsKTHgWxdtN5dkiNF1r6QSXF+baqKYz6yqkOkMlYFbV6Yj+Yz3X3uuimvAgMBAAECggEAI4j99HELpEO8AYahVGiQ6mNpXKISDF9B4ggMVJSOXoffEomnrwo3k0i+nm9BrNjLxOFRSt7cH2y67raypZTbkP15q+73RKH8O6Mg5CmDxhaNWAM/PXlFjpjP/SoAxCtiBmiftukLL8dgswIdpucBtRkyNGhN+ummiWmXagLd2k47R4+MoWvWwcjgcCt/WeTBr506rllXwd64NZdKVOSFW/NC1vhEymWVnN576dhhCErwSKLIaysS5sSDD4JcJ+nKuFW12xmDSMkRZ+OlAaV+d4d8yMBCtuTXHjzHm5s+ogrqum0fHC4q95bDp8zUe0cb0io9PGffJJF1Sw0SE9lDoQKBgQDzEHHsd6qeF8xKRnCYYjMfWNIrM4MdOdSeypU6i+rJIKwoSXDOsPNO8TKqyT3IjMuS/I/+i2b9jeR3385qPdf+/JdtWtxLk7EXUkkTMAI7zm3aiD9ANQBpgGjavJxVuWsuGEqKB9k4gxolZQrqoLMuvX84i4KvShyxmtqUl3g6FwKBgQC5u/g5ZCrDi1ghx4awJ3mV5dV2jHLp2/QyYHT7cMEJvjoSMxEdBJspwuNKEhkQdJCcXQZ29Lk1GGXRwWZ5zjvPnWrlAO7bRS9jxHhicwbJ/IWiEzT/Qfk8V6chTerHu/dBX8v9w52jmsskF84HiR011MlhgWqslYEWgIpQacOEKQKBgDTW4107mnypwcB31axa1LBA19eKaDtnQgCPG6fRmOXGU4aiiIJ2Vz5XEuOrweGiMfMvMoJsfaQrP3qLzcysyxLkiGq+cNuBLONcAQTJ0AJ+WsVCDzX/D6rfvmumyvmbyGAJ6dJd6GvvnDR65cehzbIggdKiCXPf0aMOfewFjZ5bAoGAelWCk1qUiCOZsvYWkqQNg1vUk7bCYaJMX3oE0zBbFS3EVgRouzIzePgyeLEe7SW5siPbhDKAYqZlOhkmhAgAgSjwJVHOAYK6Sf44RK/6wsOeyTfZal9r6ADbxiXqBBCcNOUCGWzkwcPNFULQo0n+gVxcH+r79hyq38VeVMS00LECgYEA4w65jlfXDzp9AMY2jcs2SJODj91r7i6dtsiQ0buMFwoW2WGQEHe7MU+mIEZ+k0HtSyWBnqfAmMBMAXizomUVejKONjGNKpgS6u17AhD8MVYczWJA8HkHQPH4/TiugYksIFM+gER6gPZc4vHV4dXrwVNxLS9wU0nHsl5fbUKSRhE=";//你的应用私钥
        $aop->alipayrsaPublicKey = "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEApSIPXnMWB5ez2YIkVOHt718r+pqL+r/Dhsoy8oPdbkqw778NHw4579rQFxT5gyYlr5DACGP0rmrYyEVhvaFlQwIOa7GrRzeMBIHXurkSYYUQkUBH2kjeSxfHDUCDSlyyFW3ebxgS2I5rq4sQOM0QK8Mm6RHcVRqAvSxNQ0iXRlR1WYCLxeAhSTHdj6p/HPl+U0q9nlTI9hRyNvpj77Vyz7Hy6UB0C2FlDID5dRIcQPiD+8ldVdTVnsJ827vOtKEhqnbeMx7NFNVrHbwIPmo88VIjXZiQlsLds2fFHYgG9vZMwqB3vqHaNCfz66KgVDqRBCWNGxr23EVcuNXmzPc7dQIDAQAB";//你的支付宝公钥
        $aop->apiVersion = '1.0';
        $aop->signType = 'RSA2';
        $aop->postCharset = 'utf-8';
        $aop->format = 'json';

        $request = new AlipayTradeAppPayRequest ();
        $request->setBizContent("{" .
            "\"total_amount\":\"0.01\"," .          //付款金额
            "\"body\":\"Iphone6 16G\"," .           //商品描述
            "\"subject\":\"大乐透\"," .            //订单名称
            "\"out_trade_no\":\"70501111111S001111119\"," .     //订单号
            "  }");
        $responseNode = $result = $aop->sdkExecute($request);

//        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        echo json_encode(array('status'=>200,'msg'=>$responseNode));
    }


    // public function index(){
    //     include './WechatActiontest.class.php';
    //     //退还微信支付部分
	// 	// $data['out_trade_no'] = $_REQUEST['ordernums'];
	// 	// $data['out_refund_no'] = $_REQUEST['ordernums'];
	// 	// $data['total_fee'] = 1;//$order['money'] + $order['yunfei'] - $hongbao['hongbao'];//支付金额
	// 	// $data['refund_fee'] = 1;//$order['money'] - $hongbao['hongbao'];
	// 	// $data['refund_fee'] = 1;//$order['money'] - $hongbao['hongbao'];
	// 	// $data['notify_url'] = '';
	// 	// $this->
	// 	$wechat = new WechatActiontest();
        
    //     $url = 'https://api.mch.weixin.qq.com/secapi/pay/refund';
    //     echo('NDD-124223-1589858167');exit;
	// 	$res = $wechat->ttt($_REQUEST['ordernums']);
		
	// 	var_dump($res);
    // }
}