<?php
// 本类由系统自动生成，仅供测试用途
class QianbaoAction extends MCommonAction {
	public $urls;
	public function __construct()
    {
        parent::__construct();

       	$this->urls="https://".$_SERVER['SERVER_NAME'];
    }
    public function docanshu(){
    	var_dump($_POST["risk_item"]);exit();
    }
    public function index(){
      //  var_dump(file_get_contents($_SERVER['DOCUMENT_ROOT'].'/Public/lianlian/rsa_private_key.pem'));
    	//a、b、c、d、e、f、g、h、i、j、k、l、m、n、o、p、q、r、s、t、u、v、w、x、y、z
    	$ids = M('members_status')->getFieldByUid($this->uid, 'id_status');
        if($ids!=1){
        	$this->success("请先通过实名认证后再进行钱包开户。",__APP__.'/member/verify/idcard.html');
        	exit();
        }

    	$minfo = M('members')->where("id={$this->uid}")->find();
    	$minf = M('member_info')->where("uid={$this->uid}")->find();
    	$this->assign("minfo",$minfo);
    	$this->assign("minf",$minf);
    	$data["version"]=C('version');
		$data["oidPartner"]=C('oidPartner');
		$data["timeStamp"]=date('YmdHis');
		$data["signType"]=C('signType');
		
		$data["userRequestIp"]=get_client_ip();
		$data["userId"]="mw-".substr(md5($minfo['id']),5,8).$minfo["id"];//修改手机号需要修改钱包的用户id
		$data["returnUrl"]=$this->urls."/Member/Qianbao/kaihufh";
		$data["userName"]=$minf["real_name"];
		$data["idCardNo"]=$minf["idcard"];
		//$data["idCardType"]=0;
		$data["bindMobile"]=$minfo['user_phone'];
		$data["userType"]="0";
		Vendor('Lianlian.LLpaySubmit');
		Vendor('Lianlian.Llpayconfig');
		$Llpayconfig = new Llpayconfig();
		$llpay_config=$Llpayconfig->llpay_config();
		//开户接口
		$llpay_kaihu = 'https://wallet.lianlianpay.com/llcomponent/openuser.htm';

		//建立请求
		$llpaySubmit = new LLpaySubmit($llpay_config);
		$html_text = $llpaySubmit->buildRequestPara($data);

		$data["sign"]=$html_text["sign"];

		$this->assign("data",$data);

		if(empty($minf['lianlianid'])){
			$kdata["lianlianid"]=$data["userId"];
			$kdata["iskaihu"]=0;
			M('member_info')->where("uid={$this->uid}")->save($kdata);
		}
		$this->display();
	}
	public function kaihufh(){
    	if (empty($_POST) && false !== strpos($this->contentType(), 'application/json')) {
            $content = file_get_contents('php://input');
            $post    = (array)json_decode($content, true);
        } else {
            $post = $_POST;
        }
        if($post["errorCode"]=='0000'){
        	$kdata["iskaihu"]=1;
			M('member_info')->where("lianlianid=$userid")->save($kdata);
			$this->success("恭喜您开户成功",__APP__.'/Member/Qianbao/index');
        }else{
        	$this->error("开户操作未成功",__APP__.'/Member/Qianbao/index');
        }
    }
    
	public function guanli(){
    	$minf = M('member_info')->where("uid={$this->uid}")->find();
    	//var_dump($minf["lianlianid"]);exit();
    	// $idd=$this->uid;
    	//  var_dump("mw-".substr(md5($idd),5,8).$idd); exit();

    	if(!empty($minf["lianlianid"])&&$minf["iskaihu"]==1){
    		$this->assign("minf",$minf);
	    	$data["version"]=C('version');
			$data["oidPartner"]=C('oidPartner');
			$data["timeStamp"]=date('YmdHis');
			$data["signType"]=C('signType');
			$data["userRequestIp"]=get_client_ip();
			$data["userId"]=$minf["lianlianid"];
			$data["returnUrl"]=$this->urls."/Member/Qianbao/kaihufh";
			$data["notifyUrl"]=$this->urls."/Member/Qianbao/notifyUrl";

			Vendor('Lianlian.LLpaySubmit');
			Vendor('Lianlian.Llpayconfig');
			$Llpayconfig = new Llpayconfig();
			$llpay_config=$Llpayconfig->llpay_config();
			//$llpay_guanli = 'https://wallet.lianlianpay.com/llcomponent/useradmin.htm';
			//建立请求
			$llpaySubmit = new LLpaySubmit($llpay_config);
			$html_text = $llpaySubmit->buildRequestPara($data);
			$data["sign"]=$html_text["sign"];
			$this->assign("data",$data);
			$this->display();
    	}else{
    		$ids = M('members_status')->getFieldByUid($this->uid, 'id_status');
	        if($ids!=1){
	        	$this->success("请先通过实名认证后再进行钱包开户。",__APP__.'/member/verify/idcard.html');
	        	exit();
	        }

	    	$minfo = M('members')->where("id={$this->uid}")->find();
	    	$minf = M('member_info')->where("uid={$this->uid}")->find();
	    	$this->assign("minfo",$minfo);
	    	$this->assign("minf",$minf);
	    	$data["version"]=C('version');
			$data["oidPartner"]=C('oidPartner');
			$data["timeStamp"]=date('YmdHis');
			$data["signType"]=C('signType');
			
			$data["userRequestIp"]=get_client_ip();
			$data["userId"]="mw-".substr(md5($minfo['id']),5,8).$minfo["id"];//修改手机号需要修改钱包的用户id


			$data["returnUrl"]=$this->urls."/Member/Qianbao/kaihufh";
			$data["userName"]=$minf["real_name"];
			$data["idCardNo"]=$minf["idcard"];
			//$data["idCardType"]=0;
			$data["bindMobile"]=$minfo['user_phone'];
			$data["userType"]="0";
			Vendor('Lianlian.LLpaySubmit');
			Vendor('Lianlian.Llpayconfig');
			$Llpayconfig = new Llpayconfig();
			$llpay_config=$Llpayconfig->llpay_config();
			//开户接口
			$llpay_kaihu = 'https://wallet.lianlianpay.com/llcomponent/openuser.htm';

			//建立请求
			$llpaySubmit = new LLpaySubmit($llpay_config);
			$html_text = $llpaySubmit->buildRequestPara($data);

			$data["sign"]=$html_text["sign"];

			$this->assign("data",$data);

			if(empty($minf['lianlianid'])){
				$kdata["lianlianid"]=$data["userId"];
				$kdata["iskaihu"]=0;
				M('member_info')->where("uid={$this->uid}")->save($kdata);
			}
			$this->display('index');
    	}
	}
	public function notifyUrl(){
    	$post = file_get_contents('php://input');
   //  	if($post["errorCode"]=='0000'){
   //      	$kdata["iskaihu"]=1;
			// M('member_info')->where("lianlianid=$userid")->save($kdata);
			// //$this->success("恭喜您开户成功",__APP__.'/Member/Qianbao/index');
   //      }else{
   //      	//$this->error("开户操作未成功",__APP__.'/Member/Qianbao/index');
   //      }
    }
    // houtai.rzmwzc.com/Member/Qianbao/tiReturn
    // houtai.rzmwzc.com/member/Qianbao/tiReturn
	public function tixian(){
		

		$pre = C('DB_PREFIX');
		
		$field = "m.user_name,m.user_phone,i.real_name,(mm.account_money) as all_money,mm.account_money,mm.back_money,i.real_name,b.bank_num,b.bank_name,b.bank_address";
		$vo = M('members m')->field($field)->join("{$pre}member_info i on i.uid = m.id")->join("{$pre}member_money mm on mm.uid = m.id")->join("{$pre}member_banks b on b.uid = m.id")->where("m.id={$this->uid}")->order("b.is_default desc")->find();

		// if(empty($vo['bank_num'])){
		// 	$this->error('您还未绑定银行帐户，请先绑定',__APP__.'/member/bank/index.html');

		// }else{
		// 	if($_REQUEST['bankid']){
  //   			$d['is_default']=0;
		// 		M('member_banks')->where('uid = '.$this->uid)->save($d);
		// 		$d1['is_default']=1;
		// 		M('member_banks')->where('uid = '.$this->uid.' and id='.$_REQUEST['bankid'])->save($d1);
	 //    	}

		// 	$vobank = M("member_banks")->where("(uid={$this->uid})")->order('is_default desc')->find();
		// 	$tqfee = explode( "|", $this->glo['fee_tqtx']);
		// 	$this->assign( "fee",$tqfee);
		// 	$now_5time=time()-3600*$tqfee[0];//48小时内时间
		// 	$now_15time=time()-3600*24*$tqfee[1];//15天内时间
		// 	$m_info = M('member_moneylog')->field("sum(affect_money) as money")->where("uid={$this->uid} and (type=3 or type=27) and add_time>{$now_5time}")->order('id desc')->find();  //会员48小时充值的金额
		// 	$m_a_info = M('member_moneylog')->field("sum(affect_money) as money")->where("uid={$this->uid} and (type=3 or type=27) and add_time<{$now_5time} and add_time>{$now_15time}")->order('id desc')->find(); //48小时到15天内充值的金额
		// 	//$vo["free_money"]=bcsub($vo['account_money'],$m_info["money"]+$m_a_info[" money"],2); //免费提现的额度			
		// 	$accountIn = M('member_money_account_in')->field('sum((money_in - money_out)) as money')->where("uid = '{$this->uid}' and status in(1,2) and money_in_type in(0,1,2)")->order('id asc')->find();		
		// 	$vo["free_money"] = isset($accountIn['money']) ? $accountIn['money'] : '0.00';
		// 	$vo["free_money"] = $vo["free_money"] > $vo['all_money'] ? $vo['all_money'] : $vo["free_money"];

		// 	$vo['bank_num']=str_pad($vo['bank_num'],19,"0",STR_PAD_LEFT);	
		// 	$this->assign( "vo",$vo);

		// 	$bank_list=C('BANK_NAME');
		// 	$vo['bank_name']=$bank_list[$vo['bank_name']];
			
		// }
		$MM = m("member_money")->field("money_freeze,money_collect,account_money")->find($this->uid);
		// $va = getFloatvalue($MM['account_money']+$MM['money_collect']+$MM['money_freeze'],2);
		$va = $MM['account_money'];
		//var_dump($va,$MM['account_money']);
		$this->assign("va",$va);
		$this->assign("vobank",$vobank);
		$this->assign( "vo",$vo);
		$this->display();
		
	}
	public function dotx(){
		$pre = C('DB_PREFIX');
		$withdraw_money = floatval($_POST['amount']);
		$pwd = md5($_POST['pwd']);
		$vm = getMinfo($this->uid,'m.pin_pass');
		if(empty($vm['pin_pass'])){
			$vo = M('members m')->field('mm.account_money,mm.back_money,(mm.account_money+mm.back_money) all_money,m.user_leve,m.time_limit')->join("{$pre}member_money mm on mm.uid = m.id")->where("m.id={$this->uid} AND (m.pin_pass='{$pwd}' or m.user_pass='{$pwd}')")->find();
		}else{
			$vo = M('members m')->field('mm.account_money,mm.back_money,(mm.account_money+mm.back_money) all_money,m.user_leve,m.time_limit')->join("{$pre}member_money mm on mm.uid = m.id")->where("m.id={$this->uid} AND (m.pin_pass='{$pwd}')")->find();
		}
		if(!is_array($vo)) 	ajaxmsg("支付密码错误",0);
		$all_money=$vo['account_money'];
		if($vo['all_money']<$withdraw_money) ajaxmsg("提现额大于帐户余额",2);
		$bank_id =   $_POST['bank_id'] ;
		$wmap['withdraw_status'] = array("neq",3);
		ini_set('date.timezone','Asia/Shanghai');
		$start=strtotime(date("Y-m-d 00:00:01",time()));
		$end=strtotime(date("Y-m-d 23:59:59",time()));
		$wmap['add_time'] = array("between","{$start},{$end}");
		$wmap['bank_id'] = $bank_id;
		$today_money = M('member_withdraw')->where($wmap)->sum('withdraw_money');	
		
		$today_time = M('member_withdraw')->where($wmap)->count('id');	
		
		$tqfee = explode("|",$this->glo['fee_tqtx']);
	
		/*
		：	1、提现次数：每天可以提现10次
			2、提现上下限：1元<=提现金额<=1000000元
			3、提现手续费：2元/笔
			4、未投资手续费：未投资的部分另外扣取千分之3的手续费
		*/
		$today_limit = $tqfee[0];		
		if(!empty($today_limit) && $today_time>=$today_limit){
			$message = "一天最多只能提现{$today_limit}次";
			ajaxmsg($message,2);
			exit;
		}
		$one_limit_min = $tqfee[1];
		$one_limit_max = $tqfee[2];

		//if($withdraw_money < $one_limit_min || $withdraw_money > $one_limit_max*10000) ajaxmsg("单笔提现金额限制为{$one_limit_min}-{$one_limit_max}万元",2);
		//获取充值未投资充值部分
		$accountIn = M('member_money_account_in')->field('sum((money_in - money_out)) as money')->where("uid = '{$this->uid}' and status in(1,2) and money_in_type in(0,1,2)")->order('id asc')->find();		
		$noInvestMoney = isset($accountIn['money']) ? $accountIn['money'] : '0.00';
		$noInvestMoney = $noInvestMoney > $vo['account_money'] ? $vo['account_money'] : $noInvestMoney;		
		$noNeedFeeMoney = $vo['account_money'] - $noInvestMoney;
		$needFeeMoney = $noNeedFeeMoney > $withdraw_money ? '0.00' : bcsub($withdraw_money, $noNeedFeeMoney, 2);
		// $fee = getfloatvalue($tqfee[3],2);
		// $fee += $needFeeMoney * getfloatvalue($tqfee[4],2) / 1000;
		$fee = 0.00;
		$member_banks = M('member_banks') ->where("id = ".$bank_id) ->find();	

		$moneydata['bank_num'] = $member_banks['bank_num'];
		$moneydata['bank_name'] = $member_banks['bank_name'];
		$moneydata['bank_id'] = $bank_id;
//		var_dump(M('lzh_member_banks')->getlastsql());
//		die;	
		$moneydata['withdraw_money'] = $withdraw_money;
		$moneydata['withdraw_fee'] = 0;
		$moneydata['second_fee'] = $fee;
		$moneydata['second_money'] = $withdraw_money;
		$moneydata['withdraw_status'] = 0;
		$moneydata['uid'] =$this->uid;
		$moneydata['add_time'] = time();
		$moneydata['add_ip'] = get_client_ip();

		$moneydata["orderNo"]="LLTX".date('Ymd').substr(microtime(),2,6);
		$moneydata["type"]=1;

		$newid = M('member_withdraw')->add($moneydata);		

		if($newid){
			memberMoneyLog($this->uid,4,-$withdraw_money,"提现，手续费".$fee."元"."其中未投资金额为{$needFeeMoney}元",'0','@网站管理员@',-($fee));
			MTip('chk6',$this->uid);
			$this->fukuan($this->uid,$withdraw_money,$name='用户提现',$info='用户提现',$moneydata["orderNo"]);
			//ajaxmsg('提现申请已提交',1);	
		}
		exit;
	}

	public function dotixian(){
		$minf = M('member_info')->where("uid={$this->uid}")->find();
    	$this->assign("minf",$minf);
    	$data["version"]=C('version');
		$data["oidPartner"]=C('oidPartner');
		$data["timeStamp"]=date('YmdHis');
		$data["signType"]=C('signType');
		$data["userRequestIp"]=get_client_ip();
		$data["userId"]=$minf["lianlianid"];
		$data["returnUrl"]=$this->urls."/Member/Qianbao/tiReturn";
		$data["notifyUrl"]=$this->urls."/Member/Qianbaoyb/tiNotify";
		$data["orderNo"]="LLTX".date('Ymd').substr(microtime(),2,6);
		$data["orderDate"]=date('YmdHis');
		$this->assign("data",$data);
		$this->display();
	}

	public function dotixians(){
		$minf = M('member_info')->where("uid={$this->uid}")->find();
    	$this->assign("minf",$minf);

    	$data["version"]=C('version');
		$data["oidPartner"]=C('oidPartner');
		$data["timeStamp"]=date('YmdHis');
		$data["signType"]=C('signType');
		$data["userRequestIp"]=get_client_ip();
		$data["userId"]=$minf["lianlianid"];
		$data["returnUrl"]=$this->urls."/Member/Qianbao/dtiReturn";
		$data["notifyUrl"]=$this->urls."/Member/Qianbaoyb/dtiNotify";
		$data["orderNo"]="LLTX".date('Ymd').substr(microtime(),2,6);
		$data["moneyOrder"]=(float)$_REQUEST["money_order"];
		$data["orderDate"]=date('YmdHis');
		$data["sign"]=$this->qianming($data);
		$this->assign("data",$data);
		$this->display();
	}
	public function dtiReturn(){
        $post = file_get_contents('php://input');
        parse_str($post);

        if($errorCode=="0000"){
			$this->success($errorMessage,__APP__.'/Member/index/index');
		}else{
			$this->error($errorMessage,__APP__.'/Member/index/index');
		}
    }

	public function qianming($data){
		Vendor('Lianlian.LLpaySubmit');
		Vendor('Lianlian.Llpayconfig');
		$Llpayconfig = new Llpayconfig();
		$llpay_config=$Llpayconfig->llpay_config();
		//$llpay_guanli = 'https://wallet.lianlianpay.com/llcomponent/usercashout.htm.htm';
		//建立请求
		$llpaySubmit = new LLpaySubmit($llpay_config);
		$html_text = $llpaySubmit->buildRequestPara($data);
		$data["sign"]=$html_text["sign"];
		return $data["sign"];
	} 
	public function tiReturn(){
        $post = file_get_contents('php://input');
        parse_str($post);

        if($errorCode=="0000"){
			$this->success($errorMessage,__APP__.'/Member/index/index');
		}else{
			$this->error($errorMessage,__APP__.'/Member/index/index');
		}
    }
    public function tiNotify(){
    	$post = file_get_contents('php://input');
   //  	if($post["errorCode"]=='0000'){
   //      	$kdata["iskaihu"]=1;
			// M('member_info')->where("lianlianid=$userid")->save($kdata);
			// //$this->success("恭喜您开户成功",__APP__.'/Member/Qianbao/index');
   //      }else{
   //      	//$this->error("开户操作未成功",__APP__.'/Member/Qianbao/index');
   //      }
    }
     /**RSA签名
     * $data签名数据(需要先排序，然后拼接)
     * 签名用商户私钥，必须是没有经过pkcs8转换的私钥
     * 最后的签名，需要用base64编码
     * return Sign签名
     */
    function Rsasign($data,$priKey) {
        //转换为openssl密钥，必须是没有经过pkcs8转换的私钥
        $res = openssl_get_privatekey($priKey);
    
        //调用openssl内置签名方法，生成签名$sign
        openssl_sign($data, $sign, $res,OPENSSL_ALGO_MD5);
    
        //释放资源
        openssl_free_key($res);
        
        //base64编码
        $sign = base64_encode($sign);
        //file_put_contents("log.txt","签名原串:".$data."\n", FILE_APPEND);
        return $sign;
    }
    public function chongzhi(){

    	$minf = M('member_info')->where("uid={$this->uid}")->find();
    	$this->assign("minf",$minf);
    	$data["version"]='1.0';
    	$data["platform"]=C('oidPartner');
		$data["oid_partner"]=C('oidPartner');
		$data["timestamp"]=date('YmdHis');
		$data["userreq_ip"]=get_client_ip();
		$data["sign_type"]=C('signType');
		
		$data["user_login"]=$minf["lianlianid"];
		$data["busi_partner"]="110001";

		$data["url_return"]=$this->urls."/Member/Qianbao/czReturn";
		$data["notify_url"]=$this->urls."/Member/Qianbaoyb/czNotify";
	
		//var_dump($data["notify_url"]);
		$data["no_order"]="LLCZ-".date('Ymd').'-'.substr(microtime(),2,6);
		$data["money_order"]=0.01;
		$data["dt_order"]=date('YmdHis');
		$data["name_goods"]="用户充值";
		//var_dump($data);
		Vendor('Lianlian.LLpaySubmit');
		Vendor('Lianlian.Llpayconfig');
		$Llpayconfig = new Llpayconfig();
		$llpay_config=$Llpayconfig->llpay_config();
		$llpay_guanli = 'https://wallet.lianlianpay.com/llcomponent/usercashout.htm.htm';
		//建立请求
		$llpaySubmit = new LLpaySubmit($llpay_config);
		$html_text = $llpaySubmit->buildRequestPara($data);
		$data["sign"]=$html_text["sign"];
		$this->assign("data",$data);
		$newid=$this->chongzhis($this->uid,$data["money_order"],$data["no_order"]);

		$this->display();
		
    } 
   public function chongzhis($uid,$money_off,$nid){
            $dataz['uid']=$uid;
            $dataz['nid']=$nid;
            $dataz['tran_id']=$nid;
            $dataz['money']=(float)$money_off;
            $dataz['fee']=0;
            $dataz['way']="llqb";
            //  $status_arr =array('待审核','审核通过,处理中','已提现','审核未通过');
            $dataz['status']=0;
            $dataz['add_time']=time();
            // $dataz['way_img']=$_REQUEST['way_img'];
            $dataz['add_ip']=get_client_ip();
            $dataz['deal_info'] = "连连钱包支付" ;
            $newid = M('member_payonline')->add($dataz);
            return $newid;
    } 
    public function czReturn(){
    	$post = file_get_contents('php://input');
    	parse_str($post);
    	// $aa="oid_partner=201907180002049144&sign_type=RSA&sign=Jg7KEYvqYV%2FOoLyrxlsObl4eFjOjdAb2W5c%2Bp%2FpAvspDS4BC1K6reZ7%2B%2BHD8%2BEg77ihuqXvHnsWhxoivyLq%2BqnKbF6b7VzzdnpiPSTWmxa2VpB8EHCouQ3qe7vQQl19fzzhUwWtat7hSWXqQJeL4f2RBwUFpGkBa0dwQgqhOmwM%3D&dt_order=20190810111531&no_order=LLQB-20190810-831586&oid_paybill=2019081062344017&money_order=0.01&result_pay=SUCCESS&settle_date=20190810&info_order=&pay_type=2&bank_code=03080000";
    	// var_dump($post);exit();
        if($result_pay=="SUCCESS"){
			$this->success("处理成功",__APP__.'/Member/index/index');
		}else{
			$this->error("处理失败",__APP__.'/Member/index/index');
		}
    }

     public function zhifu(){
     	$this->display();
     }
     public function zhifus(){
    	$minf = M('member_info')->where("uid={$this->uid}")->find();
    	$this->assign("minf",$minf);

    	$minfo = M('members')->where("id={$this->uid}")->find();
    	$this->assign("minfo",$minfo);

    	$data["version"]=C('version');
		$data["oid_partner"]=C('oidPartner');
		$data["user_id"]=$minf["lianlianid"];
		$data["timestamp"]=date('YmdHis');
		$data["sign_type"]=C('signType');
		$data["busi_partner"]="101001";
		$data["no_order"]="LLCZ-".date('Ymd').'-'.substr(microtime(),2,6);
		$data["dt_order"]=date('YmdHis');
		$data["name_goods"]="用户充值";
		$data["money_order"]=(float)$_REQUEST["money_order"];
		$data["notify_url"]=$this->urls."/Member/Qianbaoyb/zfNotify";
		$data["url_return"]=$this->urls."/Member/Qianbao/zfReturn";
		$data["userreq_ip"]=get_client_ip();
		$data["col_oidpartner"]=C('oidPartner');
		
		// $dbc["user_info_bind_phone"]=$minfo["user_phone"];
		// $dbc["user_info_dt_register"]=date('YmdHis',$minfo['reg_time']);
		// $dbc["frms_ware_category"]=1999;
		// $dbc["user_info_mercht_userno"]=$minf['uid'];

 		$dbc["user_info_bind_phone"]=$minfo["user_phone"];
		$dbc["user_info_dt_register"]=date('YmdHis',$minfo['reg_time']);
		$dbc["frms_ware_category"]=2025;
		$dbc["user_info_mercht_userno"]=$minf['uid'];
		$dbc["goods_name"]="用户充值";

		$dbc["user_info_full_name"]=$minf['real_name'];
		$dbc["user_info_id_type"]=0;

		$dbc["user_info_id_no"]=$minf['idcard'];
		$dbc["user_info_identify_state"]=1;
		$dbc["user_info_identify_type"]=4;



 		//var_dump();exit();

		//$data["risk_item"]="{'user_info_bind_phone':'".$minfo["user_phone"]."','user_info_dt_register':'".date('YmdHis',$minfo['reg_time'])."','frms_ware_category':'1999','user_info_mercht_userno':'".$minf['uid']."'}";

		$data["risk_item"]=json_encode($dbc);

		
	
		//var_dump($data["notify_url"]);
		
		
		//var_dump($data);
		Vendor('Lianlian.LLpaySubmit');
		Vendor('Lianlian.Llpayconfig');
		$Llpayconfig = new Llpayconfig();
		$llpay_config=$Llpayconfig->llpay_config();
		// $llpay_guanli = 'https://wallet.lianlianpay.com/llcomponent/usercashout.htm.htm';
		// //建立请求
	    $llpaySubmit = new LLpaySubmit($llpay_config);
		$html_text = $llpaySubmit->buildRequestPara($data);
		$data["sign"]=$html_text["sign"];
		$this->assign("data",$data);
		$newid=$this->zhifuse($this->uid,$data["money_order"],$data["no_order"]);
        //writeLog("asdf");
        //$this->pre($data);
		$this->display();
		
    }
    public function zhifuse($uid,$money_off,$nid){
            $dataz['uid']=$uid;
            $dataz['nid']=$nid;
            $dataz['tran_id']=$nid;
            $dataz['money']=(float)$money_off;
            $dataz['fee']=0;
            $dataz['way']="llqb";
            //  $status_arr =array('待审核','审核通过,处理中','已提现','审核未通过');
            $dataz['status']=0;
            $dataz['add_time']=time();
            // $dataz['way_img']=$_REQUEST['way_img'];
            $dataz['add_ip']=get_client_ip();
            $dataz['deal_info'] = "连连钱包支付" ;
            $newid = M('member_payonline')->add($dataz);
            return $newid;
    } 
    public function zfReturn(){
    	$post = file_get_contents('php://input');
    	parse_str($post);
    	// $aa="oid_partner=201907180002049144&sign_type=RSA&sign=Jg7KEYvqYV%2FOoLyrxlsObl4eFjOjdAb2W5c%2Bp%2FpAvspDS4BC1K6reZ7%2B%2BHD8%2BEg77ihuqXvHnsWhxoivyLq%2BqnKbF6b7VzzdnpiPSTWmxa2VpB8EHCouQ3qe7vQQl19fzzhUwWtat7hSWXqQJeL4f2RBwUFpGkBa0dwQgqhOmwM%3D&dt_order=20190810111531&no_order=LLQB-20190810-831586&oid_paybill=2019081062344017&money_order=0.01&result_pay=SUCCESS&settle_date=20190810&info_order=&pay_type=2&bank_code=03080000";
    	// var_dump($post);exit();
        if($result_pay=="SUCCESS"){
			$this->success("处理成功",__APP__.'/Member/index/index');
		}else{
			$this->error("处理失败",__APP__.'/Member/index/index');
		}
    }
    function make_password($length = 11)
	{
	    // 密码字符集，可任意添加你需要的字符
	    $chars = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 
	    'i', 'j', 'k', 'l','m', 'n', 'o', 'p', 'q', 'r', 's', 
	    't', 'u', 'v', 'w', 'x', 'y','z', 'A', 'B', 'C', 'D', 
	    'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L','M', 'N', 'O', 
	    'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y','Z', 
	    '0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
	    // 在 $chars 中随机取 $length 个数组元素键名
	    $keys = array_rand($chars, $length); 
	    $password = '';
	    for($i = 0; $i < $length; $i++)
	    {
	        // 将 $length 个数组元素连接成字符串
	        $password .= $chars[$keys[$i]];
	    }
	    return $password;
	}

    public function fukuan($uid,$money,$name,$info,$no_order){
    	$minf = M('member_info')->where("uid={$uid}")->find();
    	$minfo = M('members')->where("id={$uid}")->find();

		$data["oid_partner"]=C('oidPartner');
		$data["sign_type"]=C('signType');
		$data["col_userid"]=$minf["lianlianid"];
		$data["money_order"]=$money;
  
		$dbc["user_info_bind_phone"]=$minfo["user_phone"];
		$dbc["user_info_dt_register"]=date('YmdHis',$minfo['reg_time']);
		$dbc["frms_ware_category"]=1999;
		$dbc["user_info_mercht_userno"]=$minf['uid'];

		$data["risk_item"]=json_encode($dbc);

		$data["info_order"]=$info;    
		$data["no_order"]=$no_order;
		$data["dt_order"]=date('YmdHis');
		$data["name_goods"]=$name;

		writeLog($data);
		// return $data;exit();

		//$data["sign"]=$this->qianming($data);

		Vendor('Lianlian.LLpaySubmit');
		Vendor('Lianlian.Llpayconfig');
		$Llpayconfig = new Llpayconfig();
		$llpay_config=$Llpayconfig->llpay_config();
		//$llpay_guanli = 'https://wallet.lianlianpay.com/llcomponent/usercashout.htm.htm';
		//建立请求
		$llpaySubmit = new LLpaySubmit($llpay_config);
		$html_text = $llpaySubmit->buildRequestPara($data);
		$data["sign"]=$html_text["sign"];
		//$data["sign"]="kUZVnJ1QeasTNIkuP1rfsc0nF8sjoZIPnW/CeeLtfVExbJL4gF1+ZW5YQp22GG+oZoOrhBaoOFqSaZ/QlxdB60CD93Gq9qi4Y8XqQXik440fpUa22ZAAIWGOovO0956HmYnEitRzdEKJgfnW21BxTxSuB9amB27aDqbQ/W9SZZc8+4oVb3UmN0XpVp/BaRmzdye+58APxkjmBAHih9l7oEOWmxgbbD27glFnDKQ9NIBc4QlYvv0NOVvyLSGmC6Le3cZi4cB+D+7ARpInXpUlguMafkOxSMolccMXAZbSnYiwaEqZ3WE9PQI08250VO6zp23/Cxw4pR9FhNMm9ikcjzPyxkTvh01/1NmOmi1W+SGJJRK8m/w1fdBVQPZ7BUDvCZQByeevHF4eHLBVW51U05MfAp9/0r9CxcDUfqw5eXNQj9qH4fa7y+uX0gvnuoFtLmwfFn/36Ajy2W7TGfkSL/tfn+1AI0zBqKWCZOUuuu9BkPH11+SZ8NNhqmN3q2vBLkQQ5czI3YSUZc1Xmqi5el+Km2pQVkwviySofeOk34e5bAIQdwGm6usJr4FOiFao624GZnzqhgsGZ3hZrzpUdbOS9m9ArpDdN6VTxqSS+OhYT9zLOzzSGDVsUOfQ+VlZdEE1Mir2LLQcSQdBGN4hDy45CdXCGE5kccV9sjr2bjk=";

		//var_dump($data);exit();
  	    //return $data;
		//$data=json_encode($data);
		//$this->pre($data);
		$url="https://walletapi.lianlianpay.com/llwalletapi/traderpayment.htm";

		$json_obj=$this->curl_json($url,$data);
		$json_obj=json_decode($json_obj,true);
       	
       	writeLog($json_obj);

   		if($json_obj["ret_code"]=="0000"){
   			$status=2;
   		}else{
   			$status=3;
   		}
   		//writeLog($no_order);
   		$no_order=$json_obj["no_order"];


   		$oid_paybill=$json_obj["oid_paybill"];
   		$ret_msg=$json_obj["ret_msg"];


   			// $no_order=$_POST["no_order"];
	        $model = D("member_withdraw");
	        //if($_POST["result_pay"]=="SUCCESS"){
	       
	        // }else{
	        // 	$status=3;
	        // }
		
			

			$deal_info ="提现到钱包";

			$secondfee = 0;

	        // if (false === $model->create()) {

	        //     $this->error($model->getError());

	        // }

	        //保存当前数据对象

			$model->withdraw_status = $status;

			$model->deal_info = $ret_msg;

			$model->deal_time=time();

			$model->deal_user="钱包系统";

			////////////////////////
			$field= 'w.*,w.id,w.uid,(mm.account_money+mm.back_money) all_money';
			$mpp["w.orderNo"]=$no_order;
			$vo = M("member_withdraw w")->where($mpp)->field($field)->join("lzh_member_money mm on w.uid = mm.uid")->find();

			$id=$vo["id"];
			$model->id=$id;
			//writeLog("1");
			//writeLog(M("member_withdraw w")->getlastsql());

			$um = M('members')->field("user_name,user_phone")->find($vo['uid']);
			if($vo['withdraw_status']<>3 && $status==3){
				addInnerMsg($vo['uid'],"您的提现申请审核未通过","您的提现申请审核未通过");
				if( ($vo['all_money']-$vo['second_fee'])<=0 ){
					  memberMoneyLog($vo['uid'],12,$vo['withdraw_money'],"提现未通过,返还,其中提现金额：".$vo['withdraw_money']."元，手续费：".$vo['second_fee']."元",'0','@网站管理员@',$vo['second_fee']);
				}else{
					 memberMoneyLog($vo['uid'],12,$vo['withdraw_money'],"提现未通过,返还",'0','@网站管理员@',$vo['second_fee']);
				}
				$model->success_money = 0;
				// memberMoneyLog($vo['uid'],12,$vo['withdraw_money']+$vo['withdraw_fee'],"提现未通过,返还",'0','@网站管理员@');
			}else if($vo['withdraw_status']<>2 && $status==2){
				addInnerMsg($vo['uid'],"您的提现已完成","您的提现已完成");
				if( ($vo['all_money'] - $vo['second_fee'])<0 ){
					memberMoneyLog($vo['uid'],29,-($vo['withdraw_money']-$vo['second_fee']),"提现成功,扣除实际手续费".$vo['second_fee']."元，减去冻结资金，到帐金额".($vo['withdraw_money']-$vo['second_fee']),'0','@网站管理员@',0);
					//$model->withdraw_money = $vo['withdraw_money']+$vo['withdraw_fee'];
					$model->success_money = $vo['withdraw_money']-$vo['second_fee'];
				}else{
					memberMoneyLog($vo['uid'],29,-($vo['withdraw_money']),"提现成功,扣除实际手续费".$vo['second_fee']."元，减去冻结资金，到帐金额".($vo['withdraw_money']),'0','@网站管理员@');
					$model->success_money = $vo['withdraw_money'];
				}
				SMStip("withdraw",$um['user_phone'],array("#USERANEM#","#MONEY#"),array($um['user_name'],($vo['withdraw_money']-$vo['second_fee'])));
			}else if($vo['withdraw_status']<>1 && $status==1){
		
				memberMoneyLog($vo['uid'],36,0,"提现申请已通过，扣除实际手续费".$secondfee."元，到帐金额".($vo['withdraw_money'])."元",'0','@网站管理员@',$secondfee);

				//exit;
				$model->success_money = $vo['withdraw_money'];
				//}
				$model->withdraw_fee = $vo['withdraw_fee'];
				$model->second_fee = $secondfee;

			}
	
			//////////////////////////
			 $result = $model->save();	
			 //writeLog("2");
			 writeLog($model->getlastsql());
	        if ($result) { //保存成功
	        	if($status=="3"){
	        		   $jsons["ret_code"]="0";
	        	}
		        $jsons["ret_msg"] =$json_obj["ret_msg"];
		       
	        }else{
	        	$jsons["ret_code"]="0";
		        $jsons["ret_msg"] =$json_obj["ret_msg"];;
	        }
  			
  			// writeLog("3");
	    //     writeLog($jsons);
   		//     outJson($jsons); 
	        //ajaxmsg(1,"账户余额不足");

	        ajaxmsg($jsons["ret_msg"],$jsons["ret_code"]);
	        // writeLog("3");
	        // writeLog($jsons);
   		    // outJson($jsons); // return $jsons;

// Array
// (
//     [dt_order] =&gt; 20190813160023
//     [money_order] =&gt; 0.01
//     [no_order] =&gt; LLHk20190813455371bfnqstuCHIKMOT
//     [oid_partner] =&gt; 201907180002049144
//     [oid_paybill] =&gt; 2019081376324413
//     [ret_code] =&gt; AT07
//     [ret_msg] =&gt; 账户余额不足
//     [sign] =&gt; FUkFkhBXkQREgPl3SBkR8f3BkXSBXsYqCInpwYMz0kJ2OLW27UMZpx/hrdoeZIPBab9iDAzJk1BsNOgJ3v+Brp0DG/+PjagVSQ4zcnWHgjaJynkdUuTvAVqZ+yoe2x4xjCWoTeSF2DqhIuPgDlvQKrxqZLIiC0x0bzA9vV0yGZA=
//     [sign_type] =&gt; RSA
// )


        //$this->pre($json_obj);
		//$this->display();
		
    }


    function curl_json($url,$params){
		$header = array("Content-type: application/json");// 注意header头，格式k:v
		$arrParams = json_encode($params);
		// $this->pre($arrParams);
		// 	$this->pre($url);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $arrParams);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);// curl函数执行的超时时间（包括连接到返回结束） 秒单位
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);// 连接上的时长 秒单位
		curl_setopt($ch, CURLOPT_URL, $url);
		$ret = curl_exec($ch);

		$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);// 对方服务器返回http code
		curl_close($ch);
		return $ret;
    }
     /**
     * 发起HTTPS请求
     */
     function curl_post($url,$data,$header,$post=1)
     {
       //初始化curl
       $ch = curl_init();
       //参数设置  
       $res= curl_setopt ($ch, CURLOPT_URL,$url);
       curl_setopt($ch, CURLOPT_POST, $post);

       curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
       curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
       curl_setopt ($ch, CURLOPT_HEADER, 0);
      

       if($post)
       curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
       curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
       curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
       $result = curl_exec ($ch);


       curl_close($ch);
       return $result;
     } 

    function pre($content) {
        echo "<pre>";
        print_r($content);
        echo "</pre>";
    }	
}
