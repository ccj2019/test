<?php

class WechattestAction extends Action 
{


    protected $key = '2f46140df07853ac09098638e435b0cc';//密钥

    protected $appid = 'wx32c335c0c20608c8';//appid

    protected $mch_id = '1547037081';//商户号

    private $SSLCERT_PATH = '/ssl/apiclient_cert.pem';
    private $SSLKEY_PATH =  '/ssl/apiclient_key.pem';

    public function ttt($data)
    {
        $out_trade_no = $data['out_trade_no'];
        $out_refund_no = $data['out_refund_no'];
        $refund_fee = $data['refund_fee'];//退款金额	
        $total_fee = $data['total_fee'];//订单金额
 
        if ($refund_fee){
            // $out_trade_no = 'trade'.time();;
            // //$out_refund_no 商户退款单号 自定义而已
            // $out_refund_no = 'refund'.time();
 
            //统一下单退款参数构造
            $unifiedorder = array(
                'appid' => $this->appid,
                'mch_id' => $this->mch_id,
                'nonce_str' => self::getNonceStr(),
                'out_trade_no' => $out_trade_no, //商户订单号	商户系统内部订单号，要求32个字符内，只能是数字、大小写字母_-|*@ ，且在同一个商户号下唯一。
                'out_refund_no' => $out_refund_no, //商户退款单号	商户系统内部的退款单号，商户系统内部唯一，只能是数字、大小写字母_-|*@ ，同一退款单号多次请求只退一笔。
                'total_fee' => $total_fee, //订单金额
                'refund_fee' => intval(floatval($refund_fee)), //退款金额	
            );
            //   return self::getNonceStr().'---$out_trade_no---'.$out_trade_no.'---$out_refund_no---'.$out_refund_no.'---$total_fee---'.$total_fee.'---$refund_fee---'.$refund_fee;
            $unifiedorder['sign'] = self::makeSign($unifiedorder);
            // return $unifiedorder['sign'];
            //请求数据
            $xmldata = self::array2xml($unifiedorder);
            // var_dump($xmldata);exit;
            $opUrl = "https://api.mch.weixin.qq.com/secapi/pay/refund";
            $res = self::postXmlCurl($opUrl, $xmldata);
            if (!$res) {
                self::return_err("Can't connect the server");
            }
            $content = self::xml2array($res);
            // if (strval($content['result_code']) == 'FAIL') {
            //     self::return_err(strval($content['err_code_des']));
            // }
            // if (strval($content['return_code']) == 'FAIL') {
            //     self::return_err(strval($content['return_msg']));
            // }
            if(strval($content['result_code']) == 'SUCCESS'){
                return true;
            }
            // var_dump($res);exit;
            return false;
        }else{
            return false;
        }
    }
    
    //---------------------------------------------------------------用到的函数------------------------------------------------------------
 
    /**
     * 此方法是为了进行 微信退款操作的 专属定制哦
     * (嘁，其实就是照搬了 人家官方的PHP Demo代码咯)
     * TODO 尤其注意代码中涉及到的 "证书使用方式（二选一）"
     * TODO 证书的路径要求为 服务器中的绝对路径[我的服务器为 CentOS6.5]
     * TODO 证书是 在微信支付开发文档中有所提及，可自行获取保存
     */
    protected function curl_post_ssl_refund($url, $vars, $second=30,$aHeader=array())
    {
        $ch = curl_init();
        //超时时间
        curl_setopt($ch,CURLOPT_TIMEOUT,$second);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
        //这里设置代理，如果有的话
        //curl_setopt($ch,CURLOPT_PROXY, '10.206.30.98');
        //curl_setopt($ch,CURLOPT_PROXYPORT, 8080);
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
        //TODO 以下两种方式需选择一种
        /*------- --第一种方法，cert 与 key 分别属于两个.pem文件--------------------------------*/
        //默认格式为PEM，可以注释
        curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
        curl_setopt($ch,CURLOPT_SSLCERT,__DIR__.$this->SSLCERT_PATH);
        //默认格式为PEM，可以注释
        curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
        curl_setopt($ch,CURLOPT_SSLKEY,__DIR__.$this->SSLKEY_PATH);
        /*----------第二种方式，两个文件合成一个.pem文件----------------------------------------*/
        //curl_setopt($ch,CURLOPT_SSLCERT,getcwd().'/all.pem');
 
        if( count($aHeader) >= 1 ){
            curl_setopt($ch, CURLOPT_HTTPHEADER, $aHeader);
        }
 
        curl_setopt($ch,CURLOPT_POST, 1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$vars);
        $data = curl_exec($ch);
        if($data){
            curl_close($ch);
            return $data;
        }
        else {
            $error = curl_errno($ch);
            //echo "call faild, errorCode:$error\n";
            curl_close($ch);
            return false;
        }
    }
	private  function postXmlCurl($url, $xml, $second = 30)
	{
		$ch = curl_init();
		$curlVersion = curl_version();
        $ua = "WXPaySDK/3.0.10 (".PHP_OS.") PHP/".PHP_VERSION." CURL/".$curlVersion['version']." "
		.$this->mch_id;

		//设置超时
		curl_setopt($ch, CURLOPT_TIMEOUT, $second);

		$proxyHost = "0.0.0.0";
		$proxyPort = 0;
		// $config->GetProxy($proxyHost, $proxyPort);
		//如果有配置代理这里就设置代理
		if($proxyHost != "0.0.0.0" && $proxyPort != 0){
			curl_setopt($ch,CURLOPT_PROXY, $proxyHost);
			curl_setopt($ch,CURLOPT_PROXYPORT, $proxyPort);
		}
		curl_setopt($ch,CURLOPT_URL, $url);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,TRUE);
		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,2);//严格校验
		curl_setopt($ch,CURLOPT_USERAGENT, $ua); 
		//设置header
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		//要求结果为字符串且输出到屏幕上
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
        curl_setopt($ch,CURLOPT_SSLCERT, __DIR__.$this->SSLCERT_PATH);
        curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
        curl_setopt($ch,CURLOPT_SSLKEY, __DIR__.$this->SSLKEY_PATH); 
		
		//post提交方式
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
		//运行curl
		$data = curl_exec($ch);
		//返回结果
		if($data){
			curl_close($ch);
			return $data;
		} else {
			$error = curl_errno($ch);
			curl_close($ch);
			throw new WxPayException("curl出错，错误码:$error");
		}
	}

    
    /**
     * 错误返回提示
     * @param string $errMsg 错误信息
     * @param string $status 错误码
     * @return  json的数据
     */
    protected function return_err($errMsg = 'error', $status = 0)
    {
        exit(json_encode(array('status' => $status, 'result' => 'fail', 'errmsg' => $errMsg)));
    }
 
 
    /**
     * 正确返回
     * @param    array $data 要返回的数组
     * @return  json的数据
     */
    protected function return_data($data = array())
    {
        exit(json_encode(array('status' => 1, 'result' => 'success', 'data' => $data)));
    }
 
    /**
     * 将一个数组转换为 XML 结构的字符串
     * @param array $arr 要转换的数组
     * @param int $level 节点层级, 1 为 Root.
     * @return string XML 结构的字符串
     */
    protected function array2xml($arr, $level = 1)
    {
        $s = $level == 1 ? "<xml>" : '';
        foreach ($arr as $tagname => $value) {
            if (is_numeric($tagname)) {
                $tagname = $value['TagName'];
                unset($value['TagName']);
            }
            if (!is_array($value)) {
                $s .= "<{$tagname}>" . (!is_numeric($value) ? '<![CDATA[' : '') . $value . (!is_numeric($value) ? ']]>' : '') . "</{$tagname}>";
            } else {
                $s .= "<{$tagname}>" . $this->array2xml($value, $level + 1) . "</{$tagname}>";
            }
        }
        $s = preg_replace("/([\x01-\x08\x0b-\x0c\x0e-\x1f])+/", ' ', $s);
        return $level == 1 ? $s . "</xml>" : $s;
    }
 
    /**
     * 将xml转为array
     * @param  string $xml xml字符串
     * @return array    转换得到的数组
     */
    protected function xml2array($xml)
    {
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $result = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $result;
    }
 
    /**
     *
     * 产生随机字符串，不长于32位
     * @param int $length
     * @return 产生的随机字符串
     */
    protected function getNonceStr($length = 32)
    {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }
 
    /**
     * 生成签名
     * @return 签名
     */
    protected function makeSign($data)
    {
        //获取微信支付秘钥
        $key = $this->key;
        // 去空
        //$data = array_filter($data);
        //签名步骤一：按字典序排序参数
        ksort($data);
        $string_a = http_build_query($data);
        $string_a = urldecode($string_a);
        //签名步骤二：在string后加入KEY
        //$config=$this->config;
        $string_sign_temp = $string_a . "&key=" . $key;
        //签名步骤三：MD5加密
        $sign = md5($string_sign_temp);
        // 签名步骤四：所有字符转为大写
        $result = strtoupper($sign);
        return $result;
    }
    public function index()
    {
        // $param = array('code'=>'215451');
        // $result = SendSms($param,'18716482623');
        // var_dump($result);
 
    }

}