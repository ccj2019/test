<?php

class WechatAction extends Action 
{
    // private $url = 'https://api.mch.weixin.qq.com/secapi/pay/refund';
    //    private $url = 'https://api.mch.weixin.qq.com/pay/unifiedorder';

    protected $key = '2f46140df07853ac09098638e435b0cc';//密钥

    protected $appid = 'wx32c335c0c20608c8';//appid

    protected $mch_id = '1547037081';//商户号

    private $SSLCERT_PATH = '../cert/apiclient_cert.pem';
    private $SSLKEY_PATH = '../cert/apiclient_key.pem';

    public function open($data,$url)
    {
        $data['appid'] = $this->appid;
        $data['mch_id'] = $this->mch_id;
        $data['nonce_str'] = md5(rand(1000,9999));
        $data['sign_type'] = 'MD5';

        // $data['out_trade_no'] = $order['ordernums'];//商户订单号
        // $data['out_refund_no'] = $order['ordernums'];//商户系统内部的退款单号
        // $data['total_fee'] = $order['money'];   //订单金额
        // $data['refund_fee'] = $order['money']; //退款金额
        // $data['notify_url'] = 'notify_url'; //回调地址

        $data['sign'] = $this->getSign($data);

        $res = $this->arrayToXml($data);
        // var_dump($res);exit();

        return $this->postXmlSSLCurl($res,$url,$second=30,1);
    }



    public function getSign($paramArr){//print_r($paramArr);
        ksort($paramArr);
        $paramStr = http_build_query($paramArr);
        $paramStr=urldecode($paramStr);
        $param_temp=$paramStr.'&key='.$this->key;//echo $param_temp.'<br>';
        $signValue=strtoupper(md5($param_temp));//echo $signValue.'<br>';
        return $signValue;

    }

    function arrayToXml($arr) {
        $xml = "<xml>";
        foreach ($arr as $key => $val){
            if (is_numeric($val)){
                $xml.="<$key>$val</$key>";
            }
            else{
                $xml.="<$key><![CDATA[$val]]></$key>";
            }
                // $xml.="<$key><![CDATA[$val]]></$key>";
        }
        $xml.="</xml>";
        return $xml;
    }

    function postXmlSSLCurl($xml,$url,$second=30,$ssl=0)
    {
//        var_dump($xml,$url);exit();

        $ch = curl_init();
        //超时时间
        curl_setopt($ch,CURLOPT_TIMEOUT,$second);
        //这里设置代理，如果有的话
        //curl_setopt($ch,CURLOPT_PROXY, '8.8.8.8');
        //curl_setopt($ch,CURLOPT_PROXYPORT, 8080);
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
        //设置header
        curl_setopt($ch,CURLOPT_HEADER,FALSE);
        //要求结果为字符串且输出到屏幕上
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);
       
        if($ssl==1){
            //设置证书
            //curl_setopt($ch,CURLOPT_CAINFO, $this->SSLROOTCA_PATH);
            //使用证书：cert 与 key 分别属于两个.pem文件
            //默认格式为PEM，可以注释
            curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
            curl_setopt($ch,CURLOPT_SSLCERT, $this->SSLCERT_PATH);
            //默认格式为PEM，可以注释
            curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
            curl_setopt($ch,CURLOPT_SSLKEY, $this->SSLKEY_PATH);
        }

        //post提交方式
        curl_setopt($ch,CURLOPT_POST, true);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$xml);
        $data = curl_exec($ch);

        //返回结果
        if($data){
            curl_close($ch);
            $obj = simplexml_load_string($data,"SimpleXMLElement", LIBXML_NOCDATA);
            $test = json_decode(json_encode($obj),true);
//            var_dump($test);exit();
        $test['timeStamp'] = time();
        $paySign['appId'] = $this->appid;
        $paySign['timeStamp'] = $test['timeStamp'];
        $paySign['nonceStr'] = $test['nonce_str'];
        $paySign['package'] = "prepay_id=".$test['prepay_id'];
        $paySign['signType']= 'MD5';
        $test['paySign'] =  $this->getSign($paySign); 
            return $test;
        } else {
            $error = curl_errno($ch);
            curl_close($ch);
            return $error;
        }
    }
}