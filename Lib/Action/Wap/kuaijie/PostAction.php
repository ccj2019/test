<?php
date_default_timezone_set('Asia/Shanghai');

	class Aes{
		public static function Encrypt($data,$key){
			$decodeKey = base64_decode($key);
			$iv     = substr($decodeKey,0,16);
			$encrypted = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $decodeKey, $data, MCRYPT_MODE_CBC, $iv); 

			return $encrypted;
		}

		public static function Decrypt($data,$key){
			$decodeKey = base64_decode($key);
			$data = base64_decode($data);
			$iv = substr($decodeKey,0,16);
			$encrypted = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $decodeKey, $data, MCRYPT_MODE_CBC, $iv); 

			return $encrypted;
		}
	}
	
	
	/*
	接收参数，请加上判断 是否符合当前要求
	比如钱是否为空了，格式等问题
	*/
	
	
	/*
	注意   关于网站编码问题
	请注意，我司接口编码为gb2312
	如涉及编码问题  请根据   iconv 和 urlencode  
	请在传递参数时 进行相应的转码
	例：	urlencode(iconv("UTF-8","gb2312//IGNORE",$goods_name))
	 	urlencode($goods_name)
	*/
	
	/*
	以下仅为参考
	*/
	
	//3DES加密
//echo "解密：" . $rep->decrypt ( $rep->encrypt ( $input ) );
	//获取ip
	$onlineip = "";
	if($_SERVER['HTTP_CLIENT_IP']){
		$onlineip=$_SERVER['HTTP_CLIENT_IP'];
	}elseif($_SERVER['HTTP_X_FORWARDED_FOR']){
		$onlineip=$_SERVER['HTTP_X_FORWARDED_FOR'];
	}else{
		$onlineip=$_SERVER['REMOTE_ADDR'];
	}
	$onlineip = "127.0.0.1";
	//echo $onlineip;
	// 获取url 
	$URL=$_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	$geturl=str_replace('PostAction.php','',$URL);
		$agent_id = $_POST['agentId'];
		$version = 1;
		$user_identity = $_POST['userIdentity'];
		$hy_auth_uid = $_POST['hyAuthUid'];
		$mobile = $_POST['mobile'];
		$device_type = $_POST['deviceType'];
		$device_id = $_POST['deviceId'];
		
		/*
		$custom_page =0;
		echo "a".$_POST['select'][1].select;
		if($_POST['select'][1] = "1")
		{$custom_page = 1;}
		else
		{$custom_page = 0;}
		*/
		$custom_page ="";
		foreach( $_POST['hobby'] as $i)
		{
		 echo '<br>';
		 $custom_page .= $i;
		}
		echo $custom_page;
		
		$display = $_POST['display'];
		
		$return_url = $_POST['returnUrl'];
		
		$notify_url = $_POST['notifyUrl'];
		$agent_bill_id = $_POST['agentBillId'];
		$agent_bill_time = date('YmdHis');
		
		$pay_amt = $_POST['payAmt'];
		$goods_name = $_POST['goodsName'];
		$goods_note = $_POST['goodsNote'];
		$goods_num = $_POST['goodsNum'];
		$user_ip = $onlineip;
		
		$ext_param1 = $_POST['extParam1'];
		$ext_param2 = $_POST['extParam2'];
		$auth_card_type = $_POST['authCardType'];
		
		$timestamp = time()*1000;
		
		$AES = $_POST['AES'];
		$Key = $_POST['Key'];
//加密数据
	$aesStr='';
	$aesStr  = $aesStr . 'version=' . $version;
	$aesStr  = $aesStr . '&user_identity=' . $user_identity;
	$aesStr  = $aesStr . '&hy_auth_uid=' . $hy_auth_uid;
	$aesStr  = $aesStr . '&mobile=' . $mobile;
	$aesStr  = $aesStr . '&device_type=' . $device_type;
	$aesStr  = $aesStr . '&device_id=' . $device_id;
	$aesStr  = $aesStr . '&custom_page=' . $custom_page;
	$aesStr  = $aesStr . '&display=' . $display;
	$aesStr  = $aesStr . '&return_url=' . $return_url;
	$aesStr  = $aesStr . '&notify_url=' . $notify_url;
	$aesStr  = $aesStr . '&agent_bill_id=' . $agent_bill_id;
	$aesStr  = $aesStr . '&agent_bill_time=' . $agent_bill_time;
	$aesStr  = $aesStr . '&pay_amt=' . $pay_amt;
	$aesStr  = $aesStr . '&goods_name=' . $goods_name;
	$aesStr  = $aesStr . '&goods_note=' . $goods_note;
	$aesStr  = $aesStr . '&goods_num=' . $goods_num;
	$aesStr  = $aesStr . '&user_ip=' . $user_ip;
	$aesStr  = $aesStr . '&ext_param1=' . $ext_param1;
	$aesStr  = $aesStr . '&ext_param2=' . $ext_param2;
	$aesStr  = $aesStr . '&auth_card_type=' . $auth_card_type;
	$aesStr  = $aesStr . '&timestamp=' . $timestamp;
	
	$encrypt_data = urlencode(base64_encode(Aes::Encrypt($aesStr,$AES)));
	
	//如果需要测试，请把取消关于$is_test的注释  订单会显示详细信息
	//$is_test='1’;
	//if($is_test=='1')
	//{
		//$is_test='1';
	//}
	//签名数据
	$signStr='';
	$signStr  = $signStr . 'agent_bill_id=' . $agent_bill_id;
	$signStr  = $signStr . '&agent_bill_time=' . $agent_bill_time;
	$signStr  = $signStr . '&agent_id=' . $agent_id;
	$signStr  = $signStr . '&auth_card_type=' . $auth_card_type;
	$signStr  = $signStr . '&custom_page=' . $custom_page;
	$signStr  = $signStr . '&device_id=' . $device_id;
	$signStr  = $signStr . '&device_type=' . $device_type;
	$signStr  = $signStr . '&display=' . $display;
	$signStr  = $signStr . '&ext_param1=' . $ext_param1;
	$signStr  = $signStr . '&ext_param2=' . $ext_param2;
	$signStr  = $signStr . '&goods_name=' . $goods_name;
	$signStr  = $signStr . '&goods_note=' . $goods_note;
	$signStr  = $signStr . '&goods_num=' . $goods_num;
	$signStr  = $signStr . '&hy_auth_uid=' . $hy_auth_uid;
	$signStr  = $signStr . '&key=' . $Key;
	$signStr  = $signStr . '&mobile=' . $mobile;
	$signStr  = $signStr . '&notify_url=' . $notify_url;
	$signStr  = $signStr . '&pay_amt=' . $pay_amt;
	$signStr  = $signStr . '&return_url=' . $return_url;
	$signStr  = $signStr . '&timestamp=' . $timestamp;
	$signStr  = $signStr . '&user_identity=' . $user_identity;
	$signStr  = $signStr . '&user_ip=' . $user_ip;
	$signStr  = $signStr . '&version=' . $version;
	//echo $signStr;
	//if ($is_test == '1'){
		//$signStr  = $signStr . '&is_test=' . $is_test;
	//}http://192.168.2.100/PayHeepay/ShortPay/SubmitOrder.aspx
	//获取sign密钥
	$sign='';
	$sign=md5(strtolower($signStr));
	$Url = "http://Pay.heepay.com/ShortPay/SubmitOrder.aspx?agent_id=".$agent_id."&encrypt_data=".$encrypt_data."&sign=".$sign;
	//header("Location: ".$Url);die;
	/*
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_TIMEOUT, 500);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($curl, CURLOPT_URL, $Url);
	
	$val = curl_exec($curl);
	curl_close($curl);
	*/
	$val=file_get_contents($Url);
	$xml = simplexml_load_string($val);
	var_dump($val);
	var_dump($xml);
	$redir=(string)$xml->encrypt_data;
	
	file_put_contents('./ttt.txt', $redir);
	var_dump($redir);
	echo $AES;
	$redirurl=Aes::Decrypt($redir, $AES);
	var_dump($redirurl);//die;
	$arr=explode('redirect_url=', $redirurl);
	echo $arr[1];
	//header('Location:'.$arr[1]);
	echo "<script>top.location='".$arr[1]."'</script>";
?>