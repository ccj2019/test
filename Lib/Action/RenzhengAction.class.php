<?php

class RenzhengAction extends Action 
{
	/** 产品密钥ID，产品标识 */
	protected $SECRETID="114936bc54f52bc8e48558e3c58dc6a0";
	/** 产品私有密钥，服务端生成签名信息使用，请严格保管，避免泄露 */
	protected $SECRETKEY="c5c7edfa3090ba43b48f741a399531c6";
	/** 业务ID，易盾根据产品业务特点分配 */
	protected $BUSINESSID="bc662682d6834abd8f8c04bb17a0ba61";
    protected $BUSINESSIDL="3d8c21507ec5417aa4d6069886c47d24";

	/** 易盾身份认证服务身份证实名认证在线检测接口地址 */
	protected $API_URL="https://verify.dun.163yun.com/v1/idcard/check";

    protected $API_URLL="https://verify.dun.163yun.com/v1/bankcard/check";
	/** api version */
	protected $VERSION="v1";
	/** API timeout*/
	protected $API_TIMEOUT=2;
	/** php内部使用的字符串编码 */
	protected $INTERNAL_STRING_CHARSET="auto";

	/**
	 * 计算参数签名
	 * $params 请求参数
	 * $secretKey secretKey
	 */
	public function gen_signature($secretKey, $params){
	    ksort($params);
	    $buff="";
	    foreach($params as $key=>$value){
	        if($value !== null) {
	           $buff .=$key;
	           $buff .=$value;
	        }
	    }
	    $buff .= $secretKey;
	    return md5($buff);
	}

	/**
	 * 将输入数据的编码统一转换成utf8
	 * @params 输入的参数
	 */
	public function toUtf8($params){
	    $utf8s = array();
	    foreach ($params as $key => $value) {
	        $utf8s[$key] = is_string($value) ? mb_convert_encoding($value, "UTF-8", "auto") : $value;
	    }
	    return $utf8s;
	}

	/**
	 * 易盾身份认证服务身份证实名认证在线检测请求接口简单封装
	 * $params 请求参数
	 */
	public function check($params){
	    $params["secretId"] = $this->SECRETID;
	    $params["businessId"] = $this->BUSINESSID;
	    $params["version"] = $this->VERSION;
	    $params["timestamp"] =round(microtime(true) * 1000);//$time; //sprintf("%d", round(microtime(true)*1000));// time in milliseconds
	    $params["nonce"] = sprintf("%d", rand()); // random int
	    //var_dump($params);
	    $params = $this->toUtf8($params);
	    $params["signature"] =  $this->gen_signature($this->SECRETKEY, $params);
	    //var_dump($params);

	    $options = array(
	        'http' => array(
	            "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36",
	            'method'  => 'POST',
	            'timeout' => $this->API_TIMEOUT, // read timeout in seconds
	            'content' => http_build_query($params),
	        ),
	    );
	   //$this->curl_post($);
	    $context  = stream_context_create($options);
	    $result = file_get_contents($this->API_URL, false, $context);//var_dump($result);
	    if($result === FALSE){
	        return array("code"=>500, "msg"=>"file_get_contents failed.");
	    }else{
	        return json_decode($result, true);
	    }
	}

	// 简单测试
	public function main($name,$cardNo){
	    //echo "mb_internal_encoding=".mb_internal_encoding()."\n";
	    $params = array(
	        "name"=>$name,
	        "cardNo"=>$cardNo,
	    );
	    //var_dump($params);
	    $ret = $this->check($params);
	    //var_dump($ret);exit();
	    if ($ret["code"] == 200) {
	        $status= $ret["result"]["status"];
	        $taskId = $ret["result"]["taskId"];
	        $reasonType = $ret["result"]["reasonType"];
	        if ($status == 1) {
	        	$jsons["status"]="1";
	            $jsons["msg"]= "认证通过";
	        }else if ($status == 2){
	        	if($reasonType==2){
	        		$jsons["msg"]= "输入姓名和身份证号不一致";
	        	}else if($reasonType==2){
	        		$jsons["msg"]= "查无此身份证";
	        	}else{
	        		$jsons["msg"]= "认证失败2";
	        	}
	        	$jsons["status"]="0";
	        }else if ($status == 0) {
	            $jsons["msg"]= "在线认证结果：待定\n";
	            $jsons["status"]="0";
	        }
	    }else{
	        $jsons["msg"]=    $ret["msg"] ;
	        $jsons["status"]="0";
	    }
	    return $jsons;
	}


    /**
     * 易盾身份认证服务银行卡认证在线检测请求接口简单封装
     * $params 请求参数
     */
    //返回当前的毫秒时间戳
    public function msectime() {
            list($msec,$sec)= explode(' ', microtime());
            $msectime = (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
            return $msectime;
    }
    public function checkyhk($params){
        $params["secretId"] = $this->SECRETID;
        $params["businessId"] = $this->BUSINESSIDL;
        $params["version"] = $this->VERSION;
        $params["timestamp"] =$this->msectime();// sprintf("%d", round(microtime(true)*1000));// time in milliseconds
        $params["nonce"] = sprintf("%d", rand()); // random int

        $params = $this->toUtf8($params);
        $params["signature"] =  $this->gen_signature($this->SECRETKEY, $params);

        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'timeout' => $this->API_TIMEOUT, // read timeout in seconds
                'content' => http_build_query($params),
            ),
        );
        $context  = stream_context_create($options);
        $result = file_get_contents($this->API_URLL, false, $context);

        if($result === FALSE){
            return array("code"=>500, "msg"=>"file_get_contents failed.");
        }else{
            return json_decode($result, true);
        }
    }

// 简单测试
    public function mainyhk($name,$idCardNo,$bankCardNo,$phone){
        //echo "mb_internal_encoding=".mb_internal_encoding()."\n";
        $params = array(
            "name"=>$name,
            "idCardNo"=>$idCardNo,
            "bankCardNo"=>$bankCardNo,
            "phone"=>$phone,
        );

        $ret = $this->checkyhk($params);

        if ($ret["code"] == 200) {
            $status= $ret["result"]["status"];
            $taskId = $ret["result"]["taskId"];
            if ($status == 1) {
                //echo "taskId={$taskId}，银行卡认证成功";
                $jsons["status"]="1";
                $jsons["msg"]= "银行卡认证成功";
            } else if ($status == 2) {
                $reasonType = $ret["result"]["reasonType"];
               // echo "taskId={$taskId}，银行卡认证不通过，不通过原因：".$reasonType."\n";
                $jsons["status"]="0";
                $jsons["msg"]= "银行卡认证不通过";
            }
        }else{
            //var_dump("12");
            $jsons["msg"]=$ret['msg'] ;
            $jsons["status"]="0";// error handler
        }
        //var_dump($jsons);exit;
        return $jsons;
    }

}
//main();


?>

