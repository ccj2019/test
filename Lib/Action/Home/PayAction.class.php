<?php
// 本类由系统自动生成，仅供测试用途
class PayAction extends HCommonAction {
	var $paydetail = NULL;
	var $payConfig = NULL;
	var $locked = false;
	var $return_url = "";
	var $notice_url = "";
	var $member_url = "";
	
	public function _Myinit(){
		// $_SERVER['HTTP_HOST'] = 'lg803.zihaistar.com';
		$this->return_url = "http://".$_SERVER['HTTP_HOST'].U("pay/payreturn",'','');
		$this->notice_url = "http://".$_SERVER['HTTP_HOST'].U("pay/paynotice",'','');
		$this->member_url = "http://".$_SERVER['HTTP_HOST']."/member";
		$this->payConfig = FS("Webconfig/payconfig");
		$this->ipsnotice_url = "http://".$_SERVER['HTTP_HOST']."/Pay/payipsnotice";//环迅主动对账
	}
	
	public function offline(){
		$this->getPaydetail();
	
	 if(floatval($_POST['money_off'])<=0){
	     $this->error("请输入正确的数值");die;
	 }
		$this->paydetail['money'] = floatval($_POST['money_off']);
		//本地要保存的信息
		$this->paydetail['fee'] = 0;
		$this->paydetail['nid'] = 'offline';
		$this->paydetail['way'] = 'off';
		$this->paydetail['tran_id'] = text($_POST['tran_id']);
		$this->paydetail['off_bank'] = text($_POST['off_bank']);
		$this->paydetail['off_way'] = text($_POST['off_way']);
		$this->paydetail['way_img'] = text($_POST['way_img']);
	//$this->ajaxReturn($_POST);die;
		$newid = M('member_payonline')->add($this->paydetail);
		if($newid) {
			//线下充值提醒
			notice1("10",$this->uid);
			$this->ajaxReturn('线下充值提交成功，请等待管理员审核',1,1);die;
			echo "<script>alert('线下充值提交成功，请等待管理员审核');  	window.location='/wapmember/'   </script>";
  
//			$this->success("线下充值提交成功，请等待管理员审核",__APP__."/member/charge/chargelog.html");
		} else{
			$this->ajaxReturn('线下充值提交失败，请重试',0,0);die;
			echo "<script>alert('线下充值提交失败，请重试');  	window.location='/wapmember/'   </script>";
//			$this->success("线下充值提交失败，请重试");
		} 
	}
	public function unionpay(){		
		header ( 'Content-type:text/html;charset=utf-8' );
		$this->getPaydetail();	
		define('transResvered', "trans_");
		define('cardResvered', "card_");
		define('transResveredKey', "TranReserved");
		define('signatureField', "Signature");		
		
		include_once APP_PATH.'Lib/Pay/Unionpay/util/common.php';
		include_once APP_PATH.'Lib/Pay/Unionpay/util/SecssUtil.class.php';
	    $dispatchMap = array(
	        // 配置个人网银交易转发地址
	        "0001" => "/chinapay_demo/page/pay/b2cPaySend.php",
	        "0004" => "/chinapay_demo/page/pay/b2cPaySend.php",
	        // 配置退款交易转发地址
	        "0401" => "/chinapay_demo/page/refund/b2cRefundSend.php",
	        // 配置查询交易转发地址
	        "0502" => "/chinapay_demo/page/query/b2cQuerySend.php"
	    );
        $TranType = '0001';
	    $dispatchUrl = $dispatchMap[$TranType];
//        $merId = '601201512144946';
        $merId = '481621606020001';    
        $orderId = date('YmdHis').substr(microtime(),2,2);	
        $txnAmt = intval(strval($this->paydetail['money']*100));
        // $txnAmt = 1;		
        $TranDate = date('Ymd');
        $TranTime = date('His');
        $frontUrl = $this->return_url."?payid=unionpay";
        $backUrl = $this->notice_url."?payid=unionpay";
	    $postData = array(
	            'MerId' => $merId,
			    'MerOrderNo' => $orderId,
			    'OrderAmt' => $txnAmt,
			    'TranDate' => $TranDate,
			    'TranTime' => $TranTime,
			    'TranType' => $TranType,
			    'BusiType' => '0001',
			    'Version' => '20140728',
			    'SplitType' => '',
			    'SplitMethod' => '',
			    'MerSplitMsg' => '',
			    'BankInstNo' => '',
			    'PayTimeOut' => '',
			    'TimeStamp' => '',
			    'RemoteAddr' => '',
			    'CurryNo' => 'CNY',
			    'AccessType' => '0',
			    'AcqCode' => '',
			    'CommodityMsg' => '',
			    'MerPageUrl' => $frontUrl,
			    'MerBgUrl' => $backUrl,
			    'MerResv' => 'MerResv',
			    'trans_BusiId' => '',
			    'trans_P1' => '',
			    'trans_P2' => '',
			    'trans_P3' => '',
			    'trans_P4' => '',
			    'trans_P5' => '',
			    'trans_P6' => '',
			    'trans_P7' => '',
			    'trans_P8' => '',
			    'trans_P9' => '',
			    'trans_P10' => '',
	    	);
                       
	        $transResvedJson = array();
	        $cardInfoJson = array();
	        $sendMap = array();
	        foreach ($postData as $key => $value) {
	            if (isEmpty($value)) {
	                continue;
	            }
	            if (startWith($key, transResvered)) {
	                // 组装交易扩展域
	                $key = substr($key, strlen(transResvered));
	                $transResvedJson[$key] = $value;
	            } else 
	                if (startWith($key, cardResvered)) {
	                    // 组装有卡交易信息域
	                    $key = substr($key, strlen(cardResvered));
	                    $cardInfoJson[$key] = $value;
	                } else {
	                    $sendMap[$key] = $value;
	                }
	        } 
	        $transResvedStr = null;
	        $cardResvedStr = null;
	        if (count($transResvedJson) > 0) {
	            $transResvedStr = json_encode($transResvedJson);
	        }
	        if (count($cardInfoJson) > 0) {
	            $cardResvedStr = json_encode($cardInfoJson);
	        }
	        
	        $secssUtil = new SecssUtil();
	        
	        if (! isEmpty($transResvedStr)) {
	            $transResvedStr = $secssUtil->decryptData($transResvedStr);
	            $sendMap[transResveredKey] = $transResvedStr;
	        }
	        if (! isEmpty($cardResvedStr)) {
	            $cardResvedStr = $secssUtil->decryptData($cardResvedStr);
	            $sendMap[cardResveredKey] = $cardResvedStr;
	        }
	  //       ini_set("display_errors", "On");
			// error_reporting(E_ALL | E_STRICT);
	        $securityPropFile = APP_PATH.'Lib/Pay/Unionpay/config/security.properties';
                //echo $securityPropFile;exit;
	        $secssUtil->init($securityPropFile);
	        $secssUtil->sign($sendMap);
	        $sendMap[signatureField] = $secssUtil->getSign();
	        $submitdata = $sendMap;
	        // dump($sendMap);die;
            unset( $this->paydetail['bank'] );
            $this->paydetail['fee'] = getfloatvalue( $this->payConfig['allinpay']['feerate'] * $this->paydetail['money']/100,2);
            $this->paydetail['nid'] = $orderId;
            $this->paydetail['way'] = "unionpay";	
            // $this->paydetail['money'] = '0.01';
            $rs = M("member_payonline" )->add( $this->paydetail);	
            if($rs){
	            $html_form = $this->create( $submitdata, 'https://payment.chinapay.com/CTITS/service/rest/page/nref/000000000017/0/0/0/0/0' );            	
            }else{
            	$this->error('充值失败，请重试！');
            }
	}
	public function allinpay(){
		if ( $this->payConfig['allinpay']['enable'] == 0 )
		{
			exit( "对不起，该支付方式被关闭，暂时不能使用!" );
		}
		$this->getPaydetail();			
		$submitdata['inputCharset'] = "1";
		$submitdata['pickupUrl'] = "http://".$_SERVER['HTTP_HOST'];
		$submitdata['pickupUrl'] = $this->return_url."/payid/allinpay";
		$submitdata['receiveUrl'] = $this->notice_url."/payid/allinpay";
		$submitdata['version'] = 'v1.0';
		$submitdata['language'] = "1";
		$submitdata['signType'] = "0";
		$submitdata['merchantId'] = $this->payConfig['allinpay']['MerNo'];
		$submitdata['orderNo'] = date("YmdHis").mt_rand( 100000,999999);
		$submitdata['orderAmount'] = intval($this->paydetail['money']*100);
		$submitdata['orderCurrency'] = '156';
		$submitdata['orderDatetime'] = date("YmdHis",time());
		$submitdata['productName'] = $this->glo['web_name']."帐户充值";
		$submitdata['payType'] = "0";
		$submitdata['signMsg'] = $this->getSign('allinpay',$submitdata);
               // echo "<pre>";print_r($submitdata);exit;
		unset( $this->paydetail['bank'] );
		$this->paydetail['fee'] = getfloatvalue( $this->payConfig['allinpay']['feerate'] * $this->paydetail['money']/100,2);
		$this->paydetail['nid'] = $this->createnid("allinpay",$submitdata['orderNo']);
		$this->paydetail['way'] = "allinpay";		
		M("member_payonline" )->add( $this->paydetail);		
		// $this->create($submitdata, "http://ceshi.allinpay.com/gateway/index.do" );		//测试环境			
		$this->create($submitdata, "https://service.allinpay.com/gateway/index.do" );		//生产环境			
	}
	
	public function sqpay(){
		if ( $this->payConfig['sqpay']['enable'] == 0 )
		{
			exit( "对不起，该支付方式被关闭，暂时不能使用!" );
		}
		$this->getPaydetail();
		
		//print_r($this->payConfig['ecpss']);
	//	exit;
		$submitdata['MerNo'] = $this->payConfig['sqpay']['MerNo'];
		$submitdata['Amount'] = number_format( $this->paydetail['money'], 2, ".", "" );
		$submitdata['BillNo'] = date('YmdHis').substr(microtime(),2,2);
		$submitdata['ReturnURL'] = $this->return_url."/payid/sqpay";
		$submitdata['NotifyURL'] = $this->notice_url."/payid/sqpay";
		$submitdata['PayType'] = "CSPAY";
		$submitdata['PaymentType'] = "";
		$submitdata['MerRemark'] = $this->glo['web_name']."帐户充值";
		$submitdata['products'] = $this->glo['web_name']."帐户充值";
		$submitdata['MD5info'] = $this->sqgetSign( $submitdata['MerNo'], $submitdata['BillNo'],$submitdata['Amount'],$submitdata['ReturnURL'],$this->payConfig['sqpay']['MD5key']);	
		unset( $this->paydetail['bank'] );
		$this->paydetail['fee'] = getfloatvalue( $this->payConfig['sqpay']['feerate'] * $this->paydetail['money']/100,2);
		$this->paydetail['nid'] = $this->createnid("sqpay",$submitdata['BillNo']);
		$this->paydetail['way'] = "sqpay";		
		M("member_payonline" )->add( $this->paydetail );	
		// dump($submitdata);die;	
		$this->create($submitdata, "https://www.95epay.cn/sslpayment" );		//正式环境	
    }
	
	
	public function sqgetSign($MerNo, $BillNo, $Amount, $ReturnURL, $MD5key){
	  $sign_params  = array(
		  'MerNo'       => $MerNo,
		  'BillNo'       => $BillNo, 
		  'Amount'         => $Amount,   
		  'ReturnURL'       => $ReturnURL
	  );
	  $sign_str = "";
	  ksort($sign_params);
	  foreach ($sign_params as $key => $val) {
		$sign_str .= sprintf("%s=%s&", $key, $val);                
	  }
  	  return strtoupper(md5($sign_str. strtoupper(md5($MD5key))));   
	}
	
	public function sqregetSign($MerNo, $BillNo, $Amount, $ReturnURL, $MD5key){
	  $sign_params  = array(
		  'MerNo'       => $MerNo,
		  'BillNo'       => $BillNo, 
		  'Amount'         => $Amount,   
		  'Succeed'       => $ReturnURL
	  );
	  $sign_str = "";
	  ksort($sign_params);
	  foreach ($sign_params as $key => $val) {
		$sign_str .= sprintf("%s=%s&", $key, $val);                
	  }
  	  return strtoupper(md5($sign_str. strtoupper(md5($MD5key))));   
	}
	
    public function guofubaopay(){
		if($this->payConfig['guofubao']['enable']==0) exit("对不起，该支付方式被关闭，暂时不能使用!");
		$this->getPaydetail();
		$submitdata['charset'] = 2;
		$submitdata['language'] = 1;
		$submitdata['version'] = "2.0";
		$submitdata['tranCode'] = '8888';
		$submitdata['feeAmt'] = 0;
		$submitdata['currencyType'] = 156;
		$submitdata['merOrderNum'] = "guofu".time().rand(10000,99999);
		$submitdata['tranDateTime'] = date("YmdHis",time());
		$submitdata['tranIP'] = get_client_ip();
		$submitdata['goodsName'] = $this->glo['web_name']."帐户充值";
		$submitdata['frontMerUrl'] = $this->return_url."?payid=gfb";
		$submitdata['backgroundMerUrl'] = $this->notice_url."?payid=gfb";
		$submitdata['merchantID'] = $this->payConfig['guofubao']['merchantID'];//商户ID
		$submitdata['virCardNoIn'] = $this->payConfig['guofubao']['virCardNoIn'];//国付宝帐户
		$submitdata['tranAmt'] = $this->paydetail['money'];
		if($this->paydetail['bank']) $submitdata['bankCode'] = $this->paydetail['bank'];//银行直联必须
		$submitdata['userType'] = 1;//银行直联,1个人,2企业
		$submitdata['signValue'] = $this->getSign('gfb',$submitdata);
		
		//本地要保存的信息
		unset($this->paydetail['bank']);
		$this->paydetail['fee'] = getFloatValue($this->payConfig['guofubao']['feerate'] * $this->paydetail['money'] / 100,2);
		$this->paydetail['nid'] = $this->createnid('gfb',$submitdata['tranDateTime']);
		$this->paydetail['way'] = 'gfb';
		M('member_payonline')->add($this->paydetail);
		$this->create($submitdata,"https://www.gopay.com.cn/PGServer/Trans/WebClientAction.do?");
    }
	//环迅支付
	public function ips(){
		if ( $this->payConfig['ips']['enable'] == 0 )
		{
						exit( "对不起，该支付方式被关闭，暂时不能使用!" );
		}
		$this->getPaydetail( );
		$submitdata['Mer_code'] = $this->payConfig['ips']['MerCode'];
		$submitdata['Billno'] = date( "YmdHis" ).mt_rand( 100000, 999999 );
		$submitdata['Date'] = date( "Ymd" );
		$submitdata['Amount'] = number_format( $this->paydetail['money'], 2, ".", "" );
		$submitdata['DispAmount'] = $submitdata['Amount'];
		$submitdata['Currency_Type'] = "RMB";
		$submitdata['Gateway_Type'] = "01";
		$submitdata['Lang'] = "GB";
		$submitdata['Merchanturl'] = $this->return_url."?payid=ips";
		$submitdata['FailUrl'] = $this->return_url."?payid=ips";
		$submitdata['ErrorUrl'] = "";
		$submitdata['Attach'] = "";
		$submitdata['OrderEncodeType'] = "5";
		$submitdata['RetEncodeType'] = "17";
		$submitdata['Rettype'] = "1";
		//$submitdata['DoCredit'] = "1";//环迅支付网银直连必须
		//if($this->paydetail['bank']) $submitdata['Bankco'] = $this->paydetail['bank'];
		//$submitdata['ServerUrl'] = $this->notice_url."?payid=ips";
		$submitdata['ServerUrl'] = $this->ipsnotice_url;//环迅主动对账 提交地址不能带参数
		$submitdata['SignMD5'] = $this->getSign( "ips", $submitdata );
		unset( $this->paydetail['bank'] );
		$this->paydetail['fee'] = getfloatvalue( $this->payConfig['ips']['feerate'] * $this->paydetail['money'] / 100, 2 );
		$this->paydetail['nid'] = $this->createnid( "ips", $submitdata['Billno'] );
		$this->paydetail['way'] = "ips";
		M( "member_payonline" )->add( $this->paydetail );
		$this->create( $submitdata, "https://pay.ips.com.cn/ipayment.aspx" );		//正式环境
		//$this->create( $submitdata, "http://pay.ips.net.cn/ipayment.aspx" );		//测试环境
	}
	
	//网银在线
	public function chinabank(){
		if($this->payConfig['chinabank']['enable']==0) exit("对不起，该支付方式被关闭，暂时不能使用!");
		$this->getPaydetail();
		$submitdata['v_mid'] = $this->payConfig['chinabank']['mid'];
		$submitdata['v_oid'] = "chinabank".time().rand(10000,99999);
		$submitdata['v_amount'] = $this->paydetail['money'];
		$submitdata['v_moneytype'] = 'CNY';
		$submitdata['v_url'] = $this->notice_url."?payid=chinabank";
		$submitdata['v_md5info'] = strtoupper($this->getSign('chinabank',$submitdata));
		
		//if($this->paydetail['bank']) $submitdata['bankCode'] = $this->paydetail['bank'];//银行直联必须
		//$submitdata['userType'] = 1;//银行直联,1个人,2企业
		
		//本地要保存的信息
		unset($this->paydetail['bank']);
		$this->paydetail['fee'] = getFloatValue($this->payConfig['chinabank']['feerate'] * $this->paydetail['money'] / 100,2);
		$this->paydetail['nid'] = $this->createnid('chinabank',$submitdata['v_oid']);
		$this->paydetail['way'] = 'chinabank';
		M('member_payonline')->add($this->paydetail);
		$this->create($submitdata,"https://Pay3.chinabank.com.cn/PayGate");
	}
	
	//宝付接口
	public function baofoo(){
		if($this->payConfig['baofoo']['enable'] == 0)
		{
			exit( "对不起，该支付方式被关闭，暂时不能使用!" );
		}
		$this->getPaydetail( );
		$submitdata['MemberID'] = $this->payConfig['baofoo']['MerCode'];
		$submitdata['TerminalID'] = $this->payConfig['baofoo']['pkey'];
		$submitdata['InterfaceVersion'] = "4.0";
		$submitdata['KeyType'] = 1;
		$submitdata['PayID'] = "";
		$submitdata['TradeDate'] = date("Ymdhis");
		$submitdata['TransID'] =  date("YmdHis").mt_rand( 1000, 9999 );
		$submitdata['OrderMoney'] = number_format( $this->paydetail['money'], 2, ".", "" ) * 100;
		$submitdata['ProductName'] = urlencode($this->glo['web_name']."帐户充值" );
		$submitdata['Amount'] =  "1";
		$submitdata['Username'] =  "";
		$submitdata['AdditionalInfo'] ="";
		$submitdata['PageUrl'] = $this->return_url."?payid=baofoo";
		$submitdata['ReturnUrl'] = $this->notice_url."?payid=baofoo";
		$submitdata['NoticeType'] =  "1";
		$submitdata['Signature'] = $this->getSign( "baofoo", $submitdata );
		//print_r($submitdata);
		//exit;
		unset( $this->paydetail['bank'] );
		$this->paydetail['fee'] = getfloatvalue( $this->payConfig['baofoo']['feerate'] * $this->paydetail['money']/100, 2 );
		$this->paydetail['nid'] = $this->createnid("baofoo", $submitdata['TransID']);
		$this->paydetail['way'] = "baofoo";
		M("member_payonline")->add( $this->paydetail );
		//$this->create( $submitdata, "http://paygate.baofoo.com/PayReceive/payindex.aspx" );//正式环境
		//$this->create( $submitdata, "http://paytest.baofoo.com/PayReceive/payindex.aspx" );//测试环境
		$this->create( $submitdata, "https://gw.baofoo.com/payindex" );//借贷分离地址
	}
	
	//盛付通接口
	public function shengpay(){
		if($this->payConfig['shengpay']['enable'] == 0)
		{
			exit( "对不起，该支付方式被关闭，暂时不能使用!" );
		}
		$this->getPaydetail();
		$submitdata['Name'] = "B2CPayment";
		$submitdata['Version'] = "V4.1.1.1.1";
		$submitdata['Charset'] = "UTF-8";
		$submitdata['MsgSender'] = $this->payConfig['shengpay']['MerCode'];
		$submitdata['SendTime'] = date("Ymdhis");
		$submitdata['OrderNo'] = date("YmdHis").mt_rand( 1000, 9999 );
		$submitdata['OrderAmount'] = number_format( $this->paydetail['money'], 2, ".", "" );
		$submitdata['OrderTime'] =date("Ymdhis");
		$submitdata['PayType'] = "PT001";
		//$submitdata['PayChannel'] = "19";/*（19 储蓄卡，20 信用卡）做直连时，储蓄卡和信用卡需要分开*/
		//$submitdata['InstCode'] = "CMB";/*银行编码，参看接口文档*/
		$submitdata['PageUrl'] = $this->return_url."?payid=shengpay";
		$submitdata['NotifyUrl'] = $this->notice_url."?payid=shengpay";
		$submitdata['ProductName'] = $this->glo['web_name']."帐户充值";
		$submitdata['BuyerContact'] = "";
		$submitdata['BuyerIp'] = "";
		$submitdata['Ext1'] = "";
		$submitdata['Ext2'] = "";
		$submitdata['SignType'] = "MD5";
		$submitdata['SignMsg'] = $this->getSign("shengpay", $submitdata );
		unset( $this->paydetail['bank'] );
		$this->paydetail['fee'] = getfloatvalue( $this->payConfig['shengpay']['feerate'] * $this->paydetail['money']/100, 2 );
		$this->paydetail['nid'] = $this->createnid("shengpay", $submitdata['OrderNo']);
		$this->paydetail['way'] = "shengpay";
		M("member_payonline")->add( $this->paydetail );
		//echo $submitdata['SignMsg'];
		$this->create( $submitdata, "https://mas.sdo.com/web-acquire-channel/cashier.htm" );//正式环境
		//$this->create( $submitdata, "https://mer.mas.sdo.com/web-acquire-channel/cashier.htm" );//测试环境
	}
	
	
	//易通支付
	public function etone(){
		
		if($this->payConfig['etone']['enable'] == 0)
		{
			exit( "对不起，该支付方式被关闭，暂时不能使用!" );
		}
		$this->getPaydetail();
		
		$submitdata['version'] = "1.0.0";
		$submitdata['transCode'] = "8888";
		$submitdata['merchantId'] = "888888888888888";
		//$this->payConfig['etone']['MerCode']
		$submitdata['merOrderNum'] = date("YmdHis").mt_rand( 1000, 9999 );
		$submitdata['bussId'] = "100000";
		$submitdata['tranAmt'] = number_format( $this->paydetail['money'], 2, ".", "" );
		$submitdata['sysTraceNum'] = date("YmdHis").mt_rand( 1000, 9999 );
		$submitdata['tranDateTime'] = date("YmdHis");
		$submitdata['currencyType'] = "156";
		$submitdata['merURL'] =$this->return_url."?payid=etone";
		$submitdata['orderInfo'] = strToHex($this->glo['web_name']."帐户充值");
		$submitdata['bankId'] = "";
		$submitdata['stlmId'] ="";
		$submitdata['userId'] ="";
		$submitdata['userIp'] = "";
		$submitdata['backURL'] = $this->notice_url."?payid=etone";
		$submitdata['reserver1'] = "";
		$submitdata['reserver2'] = "";
		$submitdata['reserver3'] = "";
		$submitdata['reserver4'] = "";
		//$submitdata['entryType'] ="1";
		//print_r($submitdata);
		$submitdata['signValue'] = $this->getSign("etone", $submitdata );
		
		//exit;
		unset( $this->paydetail['bank'] );
		$this->paydetail['fee'] = getfloatvalue( $this->payConfig['etone']['feerate'] * $this->paydetail['money']/100, 2 );
		$this->paydetail['nid'] = $this->createnid("etone", $submitdata['merOderNum']);
		$this->paydetail['way'] = "etone";
		M("member_payonline")->add( $this->paydetail );
		//echo $submitdata['SignMsg'];
		$this->create( $submitdata, "http://58.56.23.89:7002/NetPay/BankSelect.action" );//测试环境
	}
	
	//财付通接口
	public function tenpay()
	{
		if ($this->payConfig['tenpay']['enable'] ==0)
		{
			$this->error( "对不起，该支付方式被关闭，暂时不能使用!" );
		}
		$this->getPaydetail();
		$submitdata['partner'] = $this->payConfig['tenpay']['partner'];
		$submitdata['out_trade_no'] = "tenpay".time().rand(10000, 99999);
		$submitdata['total_fee'] = $this->paydetail['money'] * 100;
		$submitdata['return_url'] = $this->return_url."?payid=tenpay";
		$submitdata['notify_url'] = $this->notice_url."?payid=tenpay";
		$submitdata['body'] = $this->glo['web_name']."帐户充值";
		$submitdata['bank_type'] = "DEFAULT";
		$submitdata['spbill_create_ip'] = get_client_ip();
		$submitdata['fee_type'] = 1;
		$submitdata['subject'] = $this->glo['web_name']."帐户充值";
		$submitdata['sign_type'] = "MD5";
		$submitdata['service_version'] = "1.0";
		$submitdata['input_charset'] = "UTF-8";
		$submitdata['sign_key_index'] = 1;
		$submitdata['trade_mode'] = 1;
		$submitdata['sign'] = $this->getSign("tenpay",$submitdata);
		unset( $this->paydetail['bank']);
		$this->paydetail['fee'] = 0;
		$this->paydetail['nid'] = $this->createnid("tenpay",$submitdata['out_trade_no']);
		$this->paydetail['way'] = "tenpay";
		M("payonline")->add( $this->paydetail);
		$this->create($submitdata, "https://gw.tenpay.com/gateway/pay.htm");
	}
	
	//汇潮支付
	public function ecpss(){
		if ( $this->payConfig['ecpss']['enable'] == 0 )
		{
			exit( "对不起，该支付方式被关闭，暂时不能使用!" );
		}
		$this->getPaydetail();
		
		//print_r($this->payConfig['ecpss']);
	//	exit;
		$submitdata['MerNo'] = $this->payConfig['ecpss']['MerNo'];
		$submitdata['BillNo'] = date("YmdHis").mt_rand( 100000,999999);
		$submitdata['Amount'] = number_format( $this->paydetail['money'], 2, ".", "" );
		$submitdata['ReturnURL'] = $this->return_url."?payid=ecpss";
		$submitdata['AdviceURL'] = $this->notice_url."?payid=ecpss";
		$submitdata['orderTime'] = date('YmdHis',time());
		//$submitdata['defaultBankNumber'] = "BOCSH";
		$submitdata['SignInfo'] = $this->getSign( "ecpss", $submitdata);		
		$submitdata['Remark'] = "";
		$submitdata['products'] = $this->glo['web_name']."帐户充值";// '------------------物品信息
/*
		////////////////////////////////////////
		$submitdata['shippingFirstName'] = "";//'-------------------收货人的姓
		$submitdata['shippingLastName'] = "";//'-------------------收货人的名
		$submitdata['shippingEmail'] = "";//'----------收货人的Email
		$submitdata['shippingPhone'] = "";//'---------------收货人的固定电话
		$submitdata['shippingZipcode'] = "";//'----------------收货人的邮编
		$submitdata['shippingAddress'] = "";//'-------------收货人具体地址
		$submitdata['shippingCity'] = "";// '--------------------收货人所在城市
		$submitdata['shippingSstate'] = "";//'-------------------收货人所在省或者州
		$submitdata['shippingCountry'] = "";// '-------------------收货人所在国家
		$submitdata['products'] = $this->glo['web_name']."帐户充值";// '------------------物品信息
		//////////////////////////////////////////////////////////////////
		$submitdata['MD5info'] = $this->getSign( "ecpss", $submitdata);*/
		//print_r($submitdata);
		//exit;
		unset( $this->paydetail['bank'] );
		$this->paydetail['fee'] = getfloatvalue( $this->payConfig['ecpss']['feerate'] * $this->paydetail['money']/100,2);
		$this->paydetail['nid'] = $this->createnid("ecpss",$submitdata['BillNo']);
		$this->paydetail['way'] = "ecpss";
		
		M("member_payonline" )->add( $this->paydetail );
		$this->create( $submitdata, "https://pay.ecpss.com/sslpayment" );		//正式环境		
	}			
	public function payreturn(){		
		header ( 'Content-type:text/html;charset=utf-8' );			
		$payid = $_REQUEST['payid'];
		switch($payid){
			case 'unionpay':
		        include APP_PATH.'Lib/Pay/Unionpay/util/common.php';
		        include APP_PATH.'Lib/Pay/Unionpay/util/SecssUtil.class.php';
		        
		        $secssUtil = new SecssUtil();
		        $securityPropFile = APP_PATH.'Lib/Pay/Unionpay/config/security.properties';
		        $secssUtil->init($securityPropFile);	        
		       	unset($_POST['payid']);
		        $OrderStatus = @$_POST['OrderStatus'];
		        $MerOrderNo = @$_POST['MerOrderNo'];	        
		        // dump($OrderStatus);die;
		        if ($secssUtil->verify($_POST) && $OrderStatus == '0000') {
		            $this->success("充值完成",__APP__."/member/");	die;
		        } else {
		            $this->error("充值失败",__APP__."/member/");	
		        }
				die;
				break;
			case 'unionpay_app':				
				include_once APP_PATH.'Lib/Pay/UnionpayApp/main.php';
				if (isset ( $_POST ['signature'] )&& verify ( $_POST )) {			
					$orderId = $_POST ['orderId']; //其他字段也可用类似方式获取
					$respCode = $_POST ['respCode']; 
					
					if($respCode == '00' || $respCode == 'A6'){
						$this->success("充值完成",__APP__."/member/");	die;
					}
					//判断respCode=00或A6即可认为交易成功
					//如果卡号我们业务配了会返回且配了需要加密的话，请按此方法解密
	        //if(array_key_exists ("accNo", $_POST)){
					//	$accNo = decryptData($_POST["accNo"]);
					//}
				} 
				$this->error("充值失败",__APP__."/member/");	
				die;
				break;
			case 'allinpay':
                            $signData = $_POST;				
                            unset($signData['signMsg']);
                            $signGet = $this->getSign('allinpayReturn',$signData);									
                            if($_POST['signMsg']==$signGet && isset($_POST['payResult']) && $_POST['payResult']=="1"){
                                    $this->success("充值完成",__APP__."/member/");					
                            }else{//充值失败
                                    $this->error("充值失败",__APP__."/member/");
                            }
			break;
			case 'sqpay':
				$recode = $_REQUEST['Succeed'];
				if($recode=="88"){//充值成功
					$signGet = $this->sqregetSign( $_REQUEST['MerNo'], $_REQUEST['BillNo'],$_REQUEST['Amount'],$_REQUEST['Succeed'],$this->payConfig['sqpay']['MD5key']);
					$nid = $this->createnid('sqpay',$_REQUEST['BillNo']);
					if($_REQUEST['MD5info']==$signGet){//充值完成
						$this->success("充值完成",__APP__."/member/");
					}else{//签名不符
						$this->error("签名不符",__APP__."/member/");
					}
				}else{//充值失败
						$this->error("充值失败",__APP__."/member/");
				}
			break;
			case 'etone':
				$recode = $_REQUEST['respCode'];
				if($recode=="0000"){//充值成功
					$signGet = $this->getSign('etone',$_REQUEST);
					$nid = $this->createnid('etone',$_REQUEST['payMentTime']);
					if($_REQUEST['signValue']==$signGet){//充值完成
						$this->success("充值完成",__APP__."/member/");
					}else{//签名不符
						$this->error("签名不符",__APP__."/member/");
					}
				}else{//充值失败
						$this->error("充值失败",__APP__."/member/");
				}
			break;
			case 'gfb':
				$recode = $_REQUEST['respCode'];
				if($recode=="0000"){//充值成功
					$signGet = $this->getSign('gfb',$_REQUEST);
					$nid = $this->createnid('gfb',$_REQUEST['tranDateTime']);
					if($_REQUEST['signValue']==$signGet){//充值完成
						$this->success("充值完成",__APP__."/member/");
					}else{//签名不符
						$this->error("签名不符",__APP__."/member/");
					}
				}else{//充值失败
						$this->error(auto_charset($_REQUEST['msgExt']),__APP__."/member/");
				}
			break;
			case "ips" :
				$recode = $_REQUEST['succ'];
				if ( $recode == "Y" )
				{
					$signGet = $this->getSign( "ips_return", $_REQUEST );
					$nid = $this->createnid( "ips", $_REQUEST['billno'] );
					if ( $_REQUEST['signature'] == $signGet )
					{
						$this->success( "充值完成", __APP__."/member/" );
					}
					else
					{
						$this->error( "签名不符", __APP__."/member/" );
					}
				}
				else
				{
					$this->error( "充值失败", __APP__."/member/" );
				}
			break;
			case 'chinabank':
				$v_pstatus = $_REQUEST['v_pstatus'];
				if($v_pstatus=="20"){//充值成功
					$signGet = strtoupper($this->getSign('chinabank_return',$_REQUEST));
					$nid = $this->createnid('chinabank',$_REQUEST['v_oid']);
					if($_REQUEST['v_md5str']==$signGet){//充值完成
						$this->success("充值完成",__APP__."/member/");
					}else{//签名不符
						$this->error("签名不符",__APP__."/member/");
					}
				}else{//充值失败
						$this->error("充值失败",__APP__."/member/");
				}
		break;
		case "baofoo" :
			$recode = $_REQUEST['Result'];
			if($recode == "1"){
				$signGet = $this->getSign( "baofoo_return", $_REQUEST );
				$nid = $this->createnid( "baofoo", $_REQUEST['TransID'] );
				if ( $_REQUEST['Md5Sign'] == $signGet )
				{
					$done = $this->payDone(1,$nid,$_REQUEST['TransID']);
					$this->success( "充值完成", __APP__."/member/" );
				}
				else
				{
					$done = $this->payDone(2,$nid,$_REQUEST['TransID']);
					$this->error( "签名不符", __APP__."/member/" );
				}
			}else{
				$this->error(auto_charset($_REQUEST['resultDesc']), __APP__."/member/" );
			}
		break;
		
		case "shengpay" :
			$recode = $_REQUEST['TransStatus'];
			if($recode == "01"){
				$signGet = $this->getSign( "shengpay_return", $_REQUEST );
				$nid = $this->createnid( "shengpay", $_REQUEST['OrderNo'] );
				if ( $_REQUEST['SignMsg'] == $signGet )
				{
					$this->success( "充值完成", __APP__."/member/" );
				}
				else
				{
					$this->error( "签名不符", __APP__."/member/" );
				}
			}else{
				$this->error("充值失败", __APP__."/member/" );
			}
		break;
		case "ecpss":
			$signGet = $this->getSign("ecpss_return", $_REQUEST);
			
			
			if($_REQUEST['MD5info'] == $signGet){
				$recode = $_REQUEST['Succeed'];
				if ($recode=="1" || $recode=="9" || $recode=="19" || $recode=="88") {
					$nid = $this->createnid( "ecpss", $_REQUEST['BillNo']);
					$done = $this->payDone(1,$nid,$_REQUEST['BillNo']);
					$this->success( "充值完成", __APP__."/member/" );
				}else{
					$this->error( "签名不符", __APP__."/member/" );
				}
				
			}else{
				$this->error("充值失败", __APP__."/member/" );
			}
		break;
		case "tenpay" :
			$recode = $_REQUEST['trade_state'];
			if ($recode == "0" )
			{
				$signGet = $this->getSign( "tenpay", $this->getRequest( ) );
				$nid = $this->createnid( "tenpay", $_REQUEST['out_trade_no'] );
				if ( strtolower( $_REQUEST['sign'] ) == $signGet )
				{
					$this->success( "充值完成", __APP__."/member/" );
				}
				else
				{
					$this->error( "充值失败", __APP__."/member/" );
				}
			}
			else
			{
				$this->error( "充值失败", __APP__."/member/" );
				break;
			}
		}
	}
	
	public function paynotice(){
		$payid = $_REQUEST['payid'];
		writeLog($_POST);
		switch($payid){
			case 'unionpay':
				header ( 'Content-type:text/html;charset=utf-8' );

				include APP_PATH.'Lib/Pay/Unionpay/util/common.php';
		        include APP_PATH.'Lib/Pay/Unionpay/util/SecssUtil.class.php';
		        
		        $secssUtil = new SecssUtil();
		        $securityPropFile = APP_PATH.'Lib/Pay/Unionpay/config/security.properties';
		        $secssUtil->init($securityPropFile);
		        unset($_POST['payid']);
		        $text = array();
		        foreach($_POST as $key=>$value){
		            $text[$key] = urldecode($value);
		        }		        
		        $OrderStatus = @$_POST['OrderStatus'];
		        $MerOrderNo = @$_POST['MerOrderNo'];		        
		        if ($secssUtil->verify($text) && $OrderStatus == '0000') {
		            $done = $this->payDone(1,$MerOrderNo,$MerOrderNo);echo "OK";die;					
		        } else {
		            echo "Fail";
		        }				
				die;
				break;
			case 'unionpay_app':			
				include_once APP_PATH.'Lib/Pay/UnionpayApp/main.php';		
				if (isset ( $_POST ['signature'] )&& verify ( $_POST )) {			
					$orderId = $_POST ['orderId']; //其他字段也可用类似方式获取
					$respCode = $_POST ['respCode']; 
					if($respCode == '00' || $respCode == 'A6'){
						$done = $this->payDone(1,$orderId,$orderId);echo "OK";die;					
					}
					//判断respCode=00或A6即可认为交易成功
					//如果卡号我们业务配了会返回且配了需要加密的话，请按此方法解密
	        //if(array_key_exists ("accNo", $_POST)){
					//	$accNo = decryptData($_POST["accNo"]);
					//}
				} 	echo "Fail";			
				die;
				break;
			case 'allinpay':
				$signData = $_POST;	
                                file_put_contents('1.txt', $signData);
				$signGet = $this->getSign('allinpayReturn',$signData);
                                file_put_contents('2.txt', $signGet);
				if($_POST['signMsg']==$signGet && isset($_POST['payResult']) && $_POST['payResult']=="1" && $_POST['payAmount']>0){	
					$nid = $this->createnid("allinpay", $_REQUEST['orderNo']);
					$done = $this->payDone(1,$nid,$_REQUEST['orderNo']);
				}
                                file_put_contents('3.txt', $done);
				if($done===true) echo "OK";
				else echo "Fail";
			break;
			case 'etone':
				$recode = $_REQUEST['respCode'];
				if($recode=="0000"){//充值成功
					//$signGet = $this->getSign('etone',$_REQUEST);
					$transCode = $_REQUEST["transCode"];
	$merchantId = $_REQUEST["merchantId"];
	$respCode = $_REQUEST["respCode"];
	$sysTraceNum = $_REQUEST["sysTraceNum"];
	$merOrderNum = $_REQUEST["merOrderNum"];
	$orderId = $_REQUEST["orderId"];
	$bussId = $_REQUEST["bussId"];
	$tranAmt = $_REQUEST["tranAmt"];
	$orderAmt = $_REQUEST["orderAmt"];
	$bankFeeAmt = $_REQUEST["bankFeeAmt"];
	$integralAmt = $_REQUEST["integralAmt"];
	$vaAmt = $_REQUEST["vaAmt"];
	$bankAmt = $_REQUEST["bankAmt"];
	$bankId = $_REQUEST["bankId"];
	$sysTraceNum = $_REQUEST["sysTraceNum"];
	$integralSeq = $_REQUEST["integralSeq"];
	$vaSeq = $_REQUEST["vaSeq"];
	$bankSeq = $_REQUEST["bankSeq"];
	$tranDateTime = $_REQUEST["tranDateTime"];
	$payMentTime = $_REQUEST["payMentTime"];
	$settleDate = $_REQUEST["settleDate"];
	$currencyType = $_REQUEST["currencyType"];
	$orderInfo = $_REQUEST["orderInfo"];
	$userId = $_REQUEST["userId"];
	$userIp = $_REQUEST["userIp"];
	$reserver1 = $_REQUEST["reserver1"];
	$reserver2 = $_REQUEST["reserver2"];
	$reserver3 = $_REQUEST["reserver3"];
	$reserver4 = $_REQUEST["reserver4"];
	$signValue = $_REQUEST["signValue"];
					$txnString =  $transCode ."|". $merchantId ."|". $respCode ."|". $sysTraceNum ."|". $merOrderNum ."|"
    			. $orderId ."|". $bussId ."|". $tranAmt ."|". $orderAmt ."|" .$bankFeeAmt ."|". $integralAmt ."|"
				. $vaAmt ."|". $bankAmt ."|". $bankId ."|". $integralSeq ."|". $vaSeq ."|"
				. $bankSeq ."|". $tranDateTime ."|". $payMentTime ."|". $settleDate ."|". $currencyType ."|". $orderInfo ."|". $userId;
					$txnString.="8EF53C251102A4E6";
					$signGet = md5($txnString);
					
					$nid = $this->createnid('etone',$_REQUEST['payMentTime']);
					$money = $_REQUEST['tranAmt'];
					if($_REQUEST['signValue']==$signGet){//充值完成
						$done = $this->payDone(1,$nid,$_REQUEST['orderId']);
					}else{//签名不符
						$done = $this->payDone(2,$nid,$_REQUEST['orderId']);
					}
				}else{//充值失败
					$done = $this->payDone(3,$nid);
				}
				if($done===true) echo "ResCode=0000|JumpURL=".$this->member_url;
				else echo "ResCode=9999|JumpURL=".$this->member_url;
			break;
			case 'gfb':
				$recode = $_REQUEST['respCode'];
				if($recode=="0000"){//充值成功
					$signGet = $this->getSign('gfb',$_REQUEST);
					$nid = $this->createnid('gfb',$_REQUEST['tranDateTime']);
					$money = $_REQUEST['tranAmt'];
					if($_REQUEST['signValue']==$signGet){//充值完成
						$done = $this->payDone(1,$nid,$_REQUEST['orderId']);
					}else{//签名不符
						$done = $this->payDone(2,$nid,$_REQUEST['orderId']);
					}
				}else{//充值失败
					$done = $this->payDone(3,$nid);
				}
				if($done===true) echo "ResCode=0000|JumpURL=".$this->member_url;
				else echo "ResCode=9999|JumpURL=".$this->member_url;
			break;
			case 'chinabank':
				$v_pstatus = $_REQUEST['v_pstatus'];
				if($v_pstatus=="20"){//充值成功
					$signGet = strtoupper($this->getSign('chinabank_return',$_REQUEST));
					$nid = $this->createnid('chinabank',$_REQUEST['v_oid']);
					$money = $_REQUEST['v_amount'];
					if($_REQUEST['v_md5str']==$signGet){//充值完成
						$done = $this->payDone(1,$nid,$_REQUEST['v_oid']);
					}else{//签名不符
						$done = $this->payDone(2,$nid,$_REQUEST['v_oid']);
						echo "签名不正确";
					}
				}else{//充值失败
					$done = $this->payDone(3,$nid);
				}
				if($done===true) echo "ok";
				else echo "error";
			break;
			case "baofoo" :
				$recode = $_REQUEST['Result'];
				if ( $recode == "1" )
				{
					$signGet = $this->getSign( "baofoo_return", $_REQUEST );
					$nid = $this->createnid( "baofoo", $_REQUEST['TransID'] );
					if ($_REQUEST['Md5Sign'] == $signGet){
						$done = $this->payDone(1,$nid,$_REQUEST['TransID']);
					}else{
						$done = $this->payDone(2,$nid,$_REQUEST['TransID']);
					}
				}else{
					$done = $this->payDone(3, $nid );
				}
				if($done===true) echo "OK";
				else echo "Fail";
			break;
			case "shengpay" :
				$recode = $_REQUEST['TransStatus'];
				if ( $recode == "01" )
				{
					$signGet = $this->getSign( "shengpay_return", $_REQUEST );
					$nid = $this->createnid( "shengpay", $_REQUEST['OrderNo'] );
					if ($_REQUEST['SignMsg'] == $signGet){
						$done = $this->payDone(1,$nid,$_REQUEST['OrderNo']);
					}else{
						$done = $this->payDone(2,$nid,$_REQUEST['OrderNo']);
					}
				}else{
					$done = $this->payDone(3,$nid);
				}
				if($done === true){
					echo "OK";
				}else{
					echo "Error";
				}
			break;
			case "ecpss":
				$signGet = $this->getSign("ecpss_return", $_REQUEST);
				if($_REQUEST['MD5info'] == $signGet){
					$recode = $_REQUEST['Succeed'];
					if ($recode=="1" || $recode=="9" || $recode=="19" || $recode=="88") {
						$nid = $this->createnid( "ecpss", $_REQUEST['BillNo']);
						$done = $this->payDone(1,$nid,$_REQUEST['BillNo']);
					}else{
						$done = $this->payDone(2,$nid,$_REQUEST['BillNo']);
					}
				}else{
					$done = $this->payDone(3,$nid);
				}
			break;
			case "sqpay":
				$signGet = $this->sqregetSign( $_REQUEST['MerNo'], $_REQUEST['BillNo'],$_REQUEST['Amount'],$_REQUEST['Succeed'],$this->payConfig['sqpay']['MD5key']);
				if($_REQUEST['MD5info'] == $signGet){
					$recode = $_REQUEST['Succeed'];
					if ($recode=="88") {
						$nid = $this->createnid( "sqpay", $_REQUEST['BillNo']);
						$done = $this->payDone(1,$nid,$_REQUEST['BillNo']);
					}else{
						$done = $this->payDone(2,$nid,$_REQUEST['BillNo']);
					}
				}else{
					$done = $this->payDone(3,$nid);
				}
			break;
			case "tenpay":
				$recode = $_REQUEST['trade_state'];
				if ($recode == "0"){
					$signGet = $this->getSign("tenpay", $_REQUEST);
					$nid = $this->createnid( "tenpay", $_REQUEST['out_trade_no'] );
					if ( strtolower( $_REQUEST['sign']) == $signGet ){
						$done = $this->payDone(1,$nid,$_REQUEST['transaction_id']);
					}else{
						$done = $this->payDone(2,$nid,$_REQUEST['transaction_id']);
					}
				}else{
					$done = $this->payDone(3,$nid);
				}
				if($done === true){
					echo "success";
				}else{
					echo "fail";
				}
			break;
		}
	}
		////////////////////////////////////环迅主动对账////////////////////////////
	
		public function payipsnotice(){
			$recode = $_REQUEST['succ'];
				if ( $recode == "Y" )
				{
					$signGet = $this->getSign( "ips_return", $_REQUEST );
					$nid = $this->createnid( "ips", $_REQUEST['billno'] );
					if ( $_REQUEST['signature'] == $signGet ){
						$done = $this->payDone( 1, $nid, $_REQUEST['ipsbillno'] );
					}else{
						$done = $this->payDone( 2, $nid, $_REQUEST['ipsbillno'] );
							echo "签名不正确";
					}
				}else{
					$done = $this->payDone( 3, $nid );
				}
				if ( $done === true ){
					echo "ipscheckok";//回复ipscheckok表示已成功接收到该笔订单
				}else{
					echo "交易失败";
				}
		}
	////////////////////////////////////////////////////////////////////////////
	private function payDone($status,$nid,$oid){
		$done = false;
		$Moneylog = D('member_payonline');
		if($this->locked) return false;
		$this->locked = true;
		
		switch($status){
			case 1:
				$updata['status'] = $status;
				//echo $nid;
				//echo $oid."<br>";
				
				$updata['tran_id'] = $oid;
				//echo $updata['tran_id']."<br>";
				
				$vo = M('member_payonline')->field('uid,money,fee,status')->where("nid='{$nid}'")->find();
				if($vo['status']!=0 || !is_array($vo)) return;
				$xid = $Moneylog->where("uid={$vo['uid']} AND nid='{$nid}'")->save($updata);
				//print_r($xid);
				$tmoney = floatval($vo['money'] - $vo['fee']);
				if($xid) $newid = memberMoneyLog($vo['uid'],3,$tmoney,"充值订单号:".$oid,0,'@网站管理员@');//更新成功才充值,避免重复充值
				
				/*if($newid){
				 $u=M("member_moneylog")->where("uid=".$vo['uid']." and type=50")->count();
				 $recM = M('members')->field('user_name,recommend_id')->find($vo['uid']);
				  if($u==0 && $recM['recommend_id']>0){
					  $frist_money = $this->glo['frist_money'];
					  $fm = explode('|', $frist_money);
					  if(isset($fm[1]) && $tmoney>=$fm[0]){
					  	$jiangli = $fm[1];
						  if(!empty($jiangli)){
							  memberMoneyLog($recM['recommend_id'],50,$jiangli,"您推荐用户{$recM['user_name']}成功充值{$tmoney}",0,'@网站管理员@');//首次充值奖励
						  }	
					  }					  
				  }
				}*/

				if($newid){
				 $u=M("member_moneylog")->where("uid=".$vo['uid']." and type=50")->count();
				  if($u==0){
					  $frist_money = getFloatValue($this->glo['frist_money'],2);
					  $jiangli = getFloatValue($frist_money*$tmoney/100,2);
					  if(!empty($jiangli)){
						  memberMoneyLog($vo['uid'],50,$jiangli,"首次线上充值奖励",0,'@网站管理员@');//首次充值奖励
					  }
				  }
				}
				 
				if(!$newid){
					$updata['status'] = 0;
					$Moneylog->where("uid={$vo['uid']} AND nid='{$nid}'")->save($updata);
					return false;
				}
				$vx = M("members")->field("user_phone,user_name")->find($vo['uid']);
				SMStip("payonline",$vx['user_phone'],array("#USERNAME#","#MONEY#"),array($vx['user_name'],$vo['money']));
			break;
			case 2:
				$updata['status'] = $status;
				$updata['tran_id'] = text($oid);
				$xid = $Moneylog->where("uid={$vo['uid']} AND nid='{$nid}'")->save($updata);
			break;
			case 3:
				$updata['status'] = $status;
				$xid = $Moneylog->where("uid={$vo['uid']} AND nid='{$nid}'")->save($updata);
			break;
		}
		
		if($status>0){
			if($xid) $done = true;
		}
		$this->locked = false;
		return $done;
	}
	
	private function createnid($type,$static){
			return $static;
	}
	
	private function getPaydetail(){
		if(!$this->uid) exit;
		$this->paydetail['money'] = getFloatValue($_GET['t_money'],2);
		$this->paydetail['fee'] = 0;
		$this->paydetail['add_time'] = time();
		$this->paydetail['add_ip'] = get_client_ip();
		$this->paydetail['status'] = 0;
		$this->paydetail['uid'] = $this->uid;
		$this->paydetail['bank'] = strtoupper($_GET['bankCode']);		
	}
	
	private function getSign($type,$data){
		$md5str="";
		switch($type){
			case "etone":
				$signarray=array(
					"version",
					"transCode",
					"merchantId",
					"merOrderNum",
					"bussId",
					"tranAmt",
					"sysTraceNum",
					"tranDateTime",
					"currencyType",
					"merURL",
					"backURL",
					"orderInfo",
					"userId"
				);
				foreach($signarray as $v){
					if(!isset($data[$v])) $md5str .= "";
					else $md5str .= $data[$v]."|";
				}
				//if(substr($md5str, -1)!="|"){
				$md5str=substr($md5str, 0, -1);
				//}
				//exit;
				//$md5str.="VerficationCode=".$this->payConfig['guofubao']['VerficationCode']."]";
				$md5str.="8EF53C251102A4E6";
				//echo $md5str;
				//echo "<br>";
				$md5str = md5($md5str);
				//echo $md5str;
				//exit;
				return $md5str;
			break;
			case "gfb":
				$signarray=array(
					"version",
					"tranCode",
					"merchantID",
					"merOrderNum",
					"tranAmt",
					"feeAmt",
					"tranDateTime",
					"frontMerUrl",
					"backgroundMerUrl",
					"orderId",
					"gopayOutOrderId",
					"tranIP",
					"respCode"
				);
				foreach($signarray as $v){
					if(!isset($data[$v])) $md5str .= "$v=[]";
					else $md5str .= "$v=[$data[$v]]";
				}
				$md5str.="VerficationCode=[".$this->payConfig['guofubao']['VerficationCode']."]";
				$md5str = md5($md5str);
				return $md5str;
			break;
			case "ips" :
				$md5str = "billno".$data['Billno']."currencytype".$data['Currency_Type']."amount".$data['Amount']."date".$data['Date']."orderencodetype".$data['OrderEncodeType'];
				$md5str .= $this->payConfig['ips']['MerKey'];
				$md5str = md5( $md5str );
				return $md5str;
			break;
			case "ips_return" :
				$md5str = "billno".$data['billno']."currencytype".$data['Currency_type']."amount".$data['amount']."date".$data['date']."succ".$data['succ']."ipsbillno".$data['ipsbillno']."retencodetype".$data['retencodetype'];
				$md5str .= $this->payConfig['ips']['MerKey'];
				$md5str = md5( $md5str );
				return $md5str;
			break;
			case "chinabank":
				$signarray=array(
					"v_amount",
					"v_moneytype",
					"v_oid",
					"v_mid",
					"v_url"
				);
				foreach($signarray as $v){
					if(!isset($data[$v])) $md5str .= "";
					else $md5str .= "$data[$v]";
				}
				$md5str.=$this->payConfig['chinabank']['mkey'];
				$md5str = md5($md5str);
				return $md5str;
			break;
			case "chinabank_return":
				$signarray=array(
					"v_oid",
					"v_pstatus",
					"v_amount",
					"v_moneytype"
				);
				foreach($signarray as $v){
					if(!isset($data[$v])) $md5str .= "";
					else $md5str .= "$data[$v]";
				}
				$md5str.=$this->payConfig['chinabank']['mkey'];
				$md5str = md5($md5str);
				return $md5str;
			break;
			case "baofoo":
				$signarray = array( "MemberID", "PayID", "TradeDate", "TransID","OrderMoney", "PageUrl", "ReturnUrl", "NoticeType");
				foreach ( $signarray as $v )
				{
					$md5str .= $data[$v]."|";
				}
				$md5str.=$this->payConfig['baofoo']['MerKey'];
				//echo $md5str;
				 //exit;
				 
				// tzso7wh3j57ensf3
				 //tzsp7wh3j57ensf3
				//$md5str .= $this->payConfig['baofoo']['pkey'];
				$md5str = md5($md5str);
				return $md5str;
			break;
			case "baofoo_return":
				$signarray = array( "MemberID", "TerminalID", "TransID", "Result", "ResultDesc", "FactMoney", "AdditionalInfo" ,"SuccTime");
				foreach ( $signarray as $v )
				{
				//	$md5str .= $data[$v]."~|~";
				}
				$MARK = "~|~";
				//echo 'MemberID='.$data["MemberID"].$MARK.'TerminalID='.$data["TerminalID"].$MARK.'TransID='.$data["TransID"].$MARK.'Result='.$data["Result"].$MARK.'ResultDesc='.$data["ResultDesc"].$MARK.'FactMoney='.$data["FactMoney"].$MARK.'AdditionalInfo='.$data["AdditionalInfo"].$MARK.'SuccTime='.$data["SuccTime"].$MARK.'Md5Sign='.$this->payConfig['baofoo']['MerKey'];
				//exit;
				$md5str=md5('MemberID='.$data["MemberID"].$MARK.'TerminalID='.$data["TerminalID"].$MARK.'TransID='.$data["TransID"].$MARK.'Result='.$data["Result"].$MARK.'ResultDesc='.$data["ResultDesc"].$MARK.'FactMoney='.$data["FactMoney"].$MARK.'AdditionalInfo='.$data["AdditionalInfo"].$MARK.'SuccTime='.$data["SuccTime"].$MARK.'Md5Sign='.$this->payConfig['baofoo']['MerKey']);
				
				//$md5str = md5( $md5str );
				return $md5str;
			break;
			case "shengpay":
				$signarray=array(
					'Name',
					'Version',
					'Charset',
					'MsgSender',
					'SendTime',
					'OrderNo',
					'OrderAmount',
					'OrderTime',
					'PayType',
					//'PayChannel', /*（19 储蓄卡，20 信用卡）做直连时，储蓄卡和信用卡需要分开*/
					//'InstCode',  /*银行编码，参看接口文档*/
					'PageUrl',
					'NotifyUrl',
					'ProductName',
					'BuyerContact',
					'BuyerIp',
					'Ext1',
					'Ext2',
					'SignType',
				);
				foreach($signarray as $v){
					if(!isset($data[$v])) $md5str .= "";
					else $md5str .= "$data[$v]";
				}
				$md5str.=$this->payConfig['shengpay']['pkey'];//MD5密钥
				$md5str = strtoupper(md5($md5str));
				return $md5str;
			break;
			case "shengpay_return":
				$signarray=array(
					'Name',
					'Version',
					'Charset',
					'TraceNo',
					'MsgSender',
					'SendTime',
					'InstCode',
					'OrderNo',
					'OrderAmount',
					'TransNo',
					'TransAmount',
					'TransStatus',
					'TransType',
					'TransTime',
					'MerchantNo',
					'ErrorCode',
					'ErrorMsg',
					'Ext1',
					'Ext2',
					'SignType',
				);
				foreach($signarray as $v){
					if(!isset($data[$v])) $md5str .= "";
					else $md5str .= "$data[$v]";
				}
				$md5str.=$this->payConfig['shengpay']['mkey'];
				$md5str = strtoupper(md5($md5str));
				return $md5str;
			break;
			case "tenpay" :
				$signPars = "";
				ksort($data);
				foreach ( $data as $k => $v )
				{
					if ("" != $v && "sign" != $k )
					{
						$signPars .= $k."=".$v."&";
					}
				}
				$signPars .= "key=".$this->payConfig['tenpay']['key'];
				$md5str = strtoupper(md5($signPars));
				return $md5str;
			break;
			case "ecpss":
				$signarray=array('MerNo','BillNo','Amount','ReturnURL');//校验源字符串
				foreach($signarray as $v){
					if(!isset($data[$v])) $md5str .= "";
					else $md5str .= '&'."$data[$v]";
				}			    
				$md5str.='&'.$this->payConfig['ecpss']['MD5key'];//MD5密钥
				$md5str = trim($md5str,'&');				
			/*print_r($md5str);die;*/
				//exit;
				
				$md5str = strtoupper(md5($md5str));
				return $md5str;
			break;
			case "ecpss_return":
				$signarray = array( "BillNo", "Amount", "Succeed");//校验源字符串
				foreach ($signarray as $v){
					$md5str .= $data[$v];
				}
				$md5str .= $this->payConfig['ecpss']['MD5key'];
				
				
				$md5str = strtoupper(md5($md5str));
				return $md5str;
			break;
			case "allinpay":	
				foreach ($data as $key => $value) {
					if($value != "" ) $md5str .= $key."=".$value."&";
				}			  
				$md5str.='key='.$this->payConfig['allinpay']['MD5key'];//MD5密钥				
				$md5str = strtoupper(md5($md5str));
				return $md5str;
			case "allinpayReturn":	
				$merchantId=$_POST["merchantId"];
				$version=$_POST['version'];
				$language=$_POST['language'];
				$signType=$_POST['signType'];
				$payType=$_POST['payType'];
				$issuerId=$_POST['issuerId'];
				$paymentOrderId=$_POST['paymentOrderId'];
				$orderNo=$_POST['orderNo'];
				$orderDatetime=$_POST['orderDatetime'];
				$orderAmount=$_POST['orderAmount'];
				$payDatetime=$_POST['payDatetime'];
				$payAmount=$_POST['payAmount'];
				$ext1=$_POST['ext1'];
				$ext2=$_POST['ext2'];
				$payResult=$_POST['payResult'];
				$errorCode=$_POST['errorCode'];
				$returnDatetime=$_POST['returnDatetime'];
				$signMsg=$_POST["signMsg"];
				$bufSignSrc="";
				if($merchantId != "")
				$bufSignSrc=$bufSignSrc."merchantId=".$merchantId."&";		
				if($version != "")
				$bufSignSrc=$bufSignSrc."version=".$version."&";		
				if($language != "")
				$bufSignSrc=$bufSignSrc."language=".$language."&";		
				if($signType != "")
				$bufSignSrc=$bufSignSrc."signType=".$signType."&";		
				if($payType != "")
				$bufSignSrc=$bufSignSrc."payType=".$payType."&";
				if($issuerId != "")
				$bufSignSrc=$bufSignSrc."issuerId=".$issuerId."&";
				if($paymentOrderId != "")
				$bufSignSrc=$bufSignSrc."paymentOrderId=".$paymentOrderId."&";
				if($orderNo != "")
				$bufSignSrc=$bufSignSrc."orderNo=".$orderNo."&";
				if($orderDatetime != "")
				$bufSignSrc=$bufSignSrc."orderDatetime=".$orderDatetime."&";
				if($orderAmount != "")
				$bufSignSrc=$bufSignSrc."orderAmount=".$orderAmount."&";
				if($payDatetime != "")
				$bufSignSrc=$bufSignSrc."payDatetime=".$payDatetime."&";
				if($payAmount != "")
				$bufSignSrc=$bufSignSrc."payAmount=".$payAmount."&";
				if($ext1 != "")
				$bufSignSrc=$bufSignSrc."ext1=".$ext1."&";
				if($ext2 != "")
				$bufSignSrc=$bufSignSrc."ext2=".$ext2."&";
				if($payResult != "")
				$bufSignSrc=$bufSignSrc."payResult=".$payResult."&";
				if($errorCode != "")
				$bufSignSrc=$bufSignSrc."errorCode=".$errorCode."&";
				if($returnDatetime != "")
				$bufSignSrc=$bufSignSrc."returnDatetime=".$returnDatetime;				
				$bufSignSrc.='&key='.$this->payConfig['allinpay']['MD5key'];//MD5密钥					
				$md5str = strtoupper(md5($bufSignSrc));
				return $md5str;
			break;			
		}
	}
	
	private function create($data,$submitUrl){
		$inputstr = "";
		foreach($data as $key=>$v){
			$inputstr .= '
		<input type="hidden"  id="'.$key.'" name="'.$key.'" value="'.$v.'"/>
		';
		}
		
		$form = '
		<form action="'.$submitUrl.'" name="pay" id="pay" method="POST">
';
		$form.=	$inputstr;
		$form.=	'
</form>
		';
		
		$html = '
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml">
<head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>请不要关闭页面,支付跳转中.....</title>
        </head>
<body>
        ';
        $html.=	$form;
        $html.=	'
        <script type="text/javascript">
					document.getElementById("pay").submit();
				 </script>
        ';
        $html.= '
        </body>
</html>
		';
				 
		Mheader('utf-8');
		echo $html;
		exit;
	}
}
