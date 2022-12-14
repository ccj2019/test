<?php
/* *
 * 功能：汇付宝业务参数封装
 * 版本：1.0
 * 修改日期：2022-8-23
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 */

require dirname ( __FILE__ ).DIRECTORY_SEPARATOR.'config.php';
require_once dirname ( __FILE__ ).DIRECTORY_SEPARATOR.'AopSdk.php';

class HeepayTradeService {
	//汇付宝商户号
    public $merch_id;
	//汇付宝网关地址
	public $gateway_url;
	//汇付宝公钥
	public $heepay_public_key;
	//商户私钥
	public $merchant_private_key;
	//商户公钥
    public $merchant_public_key;
	//签名方式
	public $sign_type;
    //日志路径
    public $log_path;

	function __construct($Heepay_config){
	    $this->merch_id = $Heepay_config['merch_id'];
		$this->gateway_url = $Heepay_config['gateway_Url'];
		$this->heepay_public_key = $Heepay_config['heepay_public_key'];
		$this->merchant_private_key = $Heepay_config['merchant_private_key'];
		$this->merchant_public_key = $Heepay_config['merchant_public_key'];
		$this->sign_type = $Heepay_config['sign_type'];
		$this->log_path = $Heepay_config['log_path'];

		if(empty($this->gateway_url)||trim($this->gateway_url)==""){
			throw new Exception("gateway_url should not be NULL!");
		}
		if(empty($this->heepay_public_key)||trim($this->heepay_public_key)==""){
			throw new Exception("heepay_public_key should not be NULL!");
		}
		if(empty($this->merchant_private_key)||trim($this->merchant_private_key)==""){
			throw new Exception("merchant_private_key should not be NULL!");
		}
		if(empty($this->sign_type)||trim($this->sign_type)==""){
			throw new Exception("sign_type should not be NULL!");
		}
		if(empty($this->log_path)||trim($this->log_path)==""){
			throw new Exception("log_path should not be NULL!");
		}

	}
	function HeepayTradeService($Heepay_config) {
		$this->__construct($Heepay_config);
	}

    function aopclientRequestExecute($request,$ispage=false) {
    		$aop = new AopClient ();
    		$aop->gatewayUrl = $this->gateway_url;
    		$aop->appId = $this->merch_id;
    		$aop->rsaPrivateKey =  $this->merchant_private_key;
    		$aop->rsaPublicKey =  $this->merchant_public_key;
    		$aop->heepayPublicKey = $this->heepay_public_key;
    		$aop->apiVersion ="1.0";
    		$aop->postCharset = "utf-8";
    		$aop->format= "json";
    		$aop->encryptType=$this->sign_type;
    		$aop->signType=$this->sign_type;
    		// 开启页面信息输出
    		$aop->debugInfo=true;

    		$this->writeLog("request:".var_export($request,true));
    		if($ispage)
    		{
    			$result = $aop->pageExecute($request,"post");//已注销
    			echo $result;
    		}
    		else
    		{
    		    //var_dump($request);exit;
    			$result = $aop->Execute($request);
    		}

    		//打开后，将报文写入log文件
    		$this->writeLog("response: ".var_export($result,true));
    		return $result;
    }

	/**
	 * alipay.trade.wap.pay
	 * @param $builder 业务参数，使用buildmodel中的对象生成。
	 * @return $response 支付宝返回的信息
 	*/
	function SignPageSubmit($builder) {

		$biz_content=$builder->getBizContent();
		//打印业务参数
		$this->writeLog($biz_content);
		$request = new HeepayTradeRequest();
        $request->setApiMethodName("heepay.agreement.bank.sign.page");
        $request->setApiVersion("1.0");
        $request->setMerchId($this->merch_id);
		$request->setBizContent ( $biz_content );
		$request->setTimestamp ( date("Y-m-d H:i:s"));
        //var_dump($request);exit;
		// 统一API入口
		$response = $this->aopclientRequestExecute ($request,false);

		return $response;
	}
    function SignPageQuery($builder){

//	    $data['out_trade_no']='Sxyj_xrymg63432';
//	    $da=json_encode($data);
//	    //var_dump($da);exit;
//        $biz_content=$da;//$builder->getBizContent();

        $biz_content=$builder->getBizContent();
        //打印业务参数
        $this->writeLog($biz_content);
//var_dump($biz_content);exit;
        $request = new HeepayTradeRequest();
        $request->setApiMethodName("heepay.agreement.bank.sign.query");
        $request->setApiVersion("1.0");
        $request->setMerchId($this->merch_id);
        $request->setBizContent ( $biz_content );
        $request->setTimestamp ( date("Y-m-d H:i:s"));
        var_dump($request);//exit;
        // 统一API入口
        $response = $this->aopclientRequestExecute ($request,false);

        return $response;
    }
	/**
	 * alipay.trade.query (统一收单线下交易查询)
	 * @param $builder 业务参数，使用buildmodel中的对象生成。
	 * @return $response 支付宝返回的信息
 	*/
	function Query($builder){
		$biz_content=$builder->getBizContent();
		//打印业务参数
		$this->writeLog($biz_content);
		$request = new AlipayTradeQueryRequest();
		$request->setBizContent ( $biz_content );

		// 首先调用支付api
		$response = $this->aopclientRequestExecute ($request);
		$response = $response->alipay_trade_query_response;
		var_dump($response);
		return $response;
	}

	/**
	 * 验签方法
	 * @param $arr 验签支付宝返回的信息，使用支付宝公钥。
	 * @return boolean
	 */
	function check($arr){
		$aop = new AopClient();
		$aop->alipayrsaPublicKey = $this->alipay_public_key;
		$result = $aop->rsaCheckV1($arr, $this->alipay_public_key, $this->signtype);
		return $result;
	}

    /**
     * 签名方法
     * @param $arr ras1签名字符串。
     * @return boolean
     */
    function qianming($arr,$un=1){
        $aop = new AopClient();
        $aop->rsaPrivateKey = $this->merchant_private_key;
        $result = $aop->sign($arr,'RSA1',$un=1);
        return $result;
    }


    //请确保项目文件有可写权限，不然打印不了日志。
	function writeLog($text) {
		// $text=iconv("GBK", "UTF-8//IGNORE", $text);
		//$text = characet ( $text );
//        if(!empty($this->log_path) && trim($this->log_path)!=""){
//            file_put_contents ( $this->log_path, date ( "Y-m-d H:i:s" ) . "  " . $text . "\r\n", FILE_APPEND );
//        }
        $logTit = '==  ' . date('Y-m-d H:i:s') . '=====================' . PHP_EOL;
        $str    = $logTit . var_export($str, true) . PHP_EOL;
        $open   = fopen(dirname(__FILE__) . "/Log/" . date('Y_m_d') . ".txt", "a");
        fwrite($open, $str . PHP_EOL);
        fclose($open);
    }
//    function writeLog($str)
//    {
//        $logTit = '==  ' . date('Y-m-d H:i:s') . '=====================' . PHP_EOL;
//        $str    = $logTit . var_export($str, true) . PHP_EOL;
//        $open   = fopen(dirname(__FILE__) . "/Log/" . date('Y_m_d') . ".txt", "a");
//        fwrite($open, $str . PHP_EOL);
//        fclose($open);
//    }

	/** *利用google api生成二维码图片
	 * $content：二维码内容参数
	 * $size：生成二维码的尺寸，宽度和高度的值
	 * $lev：可选参数，纠错等级
	 * $margin：生成的二维码离边框的距离
	 */
	function create_erweima($content, $size = '200', $lev = 'L', $margin= '0') {
		$content = urlencode($content);
		$image = '<img src="http://chart.apis.google.com/chart?chs='.$size.'x'.$size.'&amp;cht=qr&chld='.$lev.'|'.$margin.'&amp;chl='.$content.'"  widht="'.$size.'" height="'.$size.'" />';
		return $image;
	}
}

?>