<?php
// 本类由系统自动生成，仅供测试用途
class ChargeAction extends MCommonAction {
    public function index(){
		$ids = M('members_status')->getFieldByUid($this->uid,'id_status');
	
		if($ids==1){
		}else{
			$this->assign('jumpUrl', __APP__."/member/Memberinfo#chip-1");
			$this->error('您还未完成身份验证，请先进行实名认证');
			exit;				
		}
		$MM = m("member_money")->field("money_freeze,money_collect,account_money,yubi")->find($this->uid);
		
	 
		$model=M('member_info');
		$minfo = $model->find($this->uid);
		//var_dump($MM['account_money'],$MM['money_collect'],$MM['money_freeze']);die;
		$va = getFloatvalue($MM['yubi'],2);
		//var_dump($va);die;
		$this->assign("va",$va);
		$this->assign("minfo",$minfo);
		$this->assign("payConfig",FS("Webconfig/payconfig"));
		
		$this->display();

    }
	
    public function paydemo(){
    	 
		//<form action="/paydemo/unitivePay.php" method="post" id="submitForm">
		//<form action="./unitivePay.php" method="post" id="submitForm">
		/*商户名称： 日照铭万网络科技有限公司
		商户号： 3710000124
		密钥： 9p57uIIFCHh6UiLNkLqDhlZ0D0NSLRgj
		业务类型： 投资理财
		业务类型代码： BIZ01101*/
		//$_POST['money']=$_POST['money_off'];
		$dataz['uid']=$this->uid;
		$dataz['nid']=date('YmdHis').substr(microtime(),2,2);
		$dataz['money']=(float)$_GET['money_off'];
		$dataz['fee']=0;
		$dataz['way']="ff";
		//	$status_arr =array('待审核','审核通过,处理中','已提现','审核未通过');
		$dataz['status']=0;
		$dataz['add_time']=time();
		$dataz['add_ip']=get_client_ip();
        $dataz['deal_info'] = "电脑端充值" ;
		//$dataz['tran_id']="";
		//$dataz['off_bank']="";
		//$dataz['off_way']="";
		//$dataz['deal_user']="";
		//$dataz['deal_uid']="";
		//$dataz['source_from']="";
		//$dataz['way_img']="";	
		//	

		//$this->ajaxReturn($_POST);die;
		$newid = M('member_payonline')->add($dataz);
		//var_dump(M('member_payonline')->getlastsql());die;
		if($newid) {
			$data['encoding']="utf8";
//["allinpay"]=>
//array(4) {
//  ["enable"]=>
//  string(1) "1"
//  ["feerate"]=>
//  string(1) "0"
//  ["MerNo"]=>
//  string(15) "109085311806006"
//  ["MD5key"]=>
//  string(8) "Tnqcy321"
//}
 $payconfig = fs("Webconfig/payconfig");
			$data['merAcct']=$payconfig["allinpay"]["MerNo"];//"3710000124";//商户代码
			$data['bizType']='BIZ01101';//"BIZ01101";//产品业务类型
			$data['tradeProcess']=$payconfig["allinpay"]["MerNo"];//"3710000124";//商户代码
			$data['totalBizType']='BIZ01101';//"BIZ01101";//业务类型 

			$data['backurl']="https://".$_SERVER['HTTP_HOST']."/member/charge/";//支付成功后跳转回的页面链接
			$data['returnurl']="https://".$_SERVER['HTTP_HOST']."/member/charge/";//用户不进行支付，返回商户系统时跳转的页面链接
			$data['noticeurl']="https://".$_SERVER['HTTP_HOST']."/home/common/savepaydemo";//异步后台通知地址,如果不传此参数，则不会后台通知

			$data['merKey']="9p57uIIFCHh6UiLNkLqDhlZ0D0NSLRgj";//签名密钥，由支付系统生成

			$data['description']=$newid;//透传给业务系统的描述信息
			$data['totalPrice']=$dataz['money'];//支付金额
			$data['productNumber']="1";//产品数量
			$data['requestId']=$dataz['nid'];//商户订单号自定义
			$data['productId']=$data['requestId'];//产品ID自定义
			$data['productName']=$data['requestId'];//产品名称自定义		 
			$data['fund']=$dataz['money'];//产品定价（精确到分，不可为负）自定义	



		echo '<form action="https://'.$_SERVER['HTTP_HOST'].'/paydemo/unitivePay/unitivePay.php" method="post" id="submitForm"   name="submitForm">';
		foreach($data as $k=>$v){
			   
		            echo '<input name="'.$k.'" type="text" value="'.$v.'" />';
		       
			
		}
   
           	echo ' </form>';
          	echo ' <script type="text/javascript">function load_submit(){document.submitForm.submit()}load_submit();</script>';			 
 
			$this->ajaxReturn("线上充值提交成功，请等待管理员审核",1,1);die;
			echo "<script>alert('线上充值提交成功，请等待管理员审核');  	window.location='/wapmember/'   </script>";
  
			// $this->success("线上充值提交成功，请等待管理员审核",__APP__."/member/charge/chargelog.html");
		} else{
			$this->ajaxReturn('线上充值提交失败，请重试',0,0);die;
			echo "<script>alert('线上充值提交失败，请重试');  	window.location='/wapmember/'   </script>";
			// $this->success("线上充值提交失败，请重试");
		} 				 
 


		//	</form>
		//	</form>
    }
    public function smf(){

    	// 表单令牌验证
  //       if($_GET['yz']!=$_SESSION["yz"]||$_SESSION["yz"]=='') {
  //           $this->error("禁止重复提交表单",__APP__.'/Member/Charge/index');
  //       }
  //       $_SESSION["yz"]='';

  //   	$code = $_GET['yzm'];
  //       if($_SESSION['verify'] != md5($code)){
  //          $this->error('验证码不正确！',__APP__.'/Member/Charge/index');
  //       }else{
  //       	$dataz['money']=(float)$_GET['money_off'];
	        
		// 	$dataz['uid']=$this->uid;
		// 	$dataz['nid']="SMF-".date('Ymd').'-'.substr(microtime(),2,6);
		// 	$dataz['money']=(float)$_GET['money_off'];
		// 	$dataz['fee']=0;
		// 	$dataz['way']="smf";
		// 	//	$status_arr =array('待审核','审核通过,处理中','已提现','审核未通过');
		// 	$dataz['status']=0;
		// 	$dataz['add_time']=time();
		// 	$dataz['add_ip']=get_client_ip();
	 //        $dataz['deal_info'] = "扫码付充值" ;
		// 	$newid = M('member_payonline')->add($dataz);
		// 	$this->assign("money_off",$dataz['money']);
		// }
		$dataz['money']=(float)$_GET['money_off'];
		$this->assign("money_off",$dataz['money']);
		$yz=substr(microtime(),2,6);
		$_SESSION["yz"]=$yz;
		$this->assign("yz",$yz);

		$this->display();		 
    }
    public function dosaoma(){

    	 //表单令牌验证
        if($_POST['yz']!=$_SESSION["yz"]||$_SESSION["yz"]=='') {
            $this->error("禁止重复提交表单",__APP__.'/Member/Charge/index');
        }
        $_SESSION["yz"]='';

        $ids = M('members_status')->getFieldByUid($this->uid, 'id_status');
        if($ids!=1){
//            $jsons['msg'] = "请先通过实名认证后再进行充值。";
//            $jsons['status'] = '0';
//            outJson($jsons);
            $this->error("请先通过实名认证后再进行充值。",__APP__.'/Member');
        }


    	// $code = $_GET['yzm'];
     //    if($_SESSION['verify'] != md5($code)){
     //       $this->error('验证码不正确！',__APP__.'/Wapmember/Charge/charges');
     //    }else{
        	//$dataz['money']=(float)$_GET['money_off'];
	        
			$dataz['uid']=$this->uid;
			$dataz['nid']="SMF-".date('Ymd').'-'.substr(microtime(),2,6);
			$dataz['money']=(float)$_POST['money_off'];
			$dataz['fee']=0;
			$dataz['way']="smf";
			//	$status_arr =array('待审核','审核通过,处理中','已提现','审核未通过');
			$dataz['status']=0;
			$dataz['add_time']=time();
			$dataz['way_img']=$_POST['way_img'];;
			$dataz['add_ip']=get_client_ip();
	        $dataz['deal_info'] = "扫码付充值" ;
			$newid = M('member_payonline')->add($dataz);
			// $this->assign("money_off",$dataz['money']);
			// $this->assign("newid",$newid);

		if($newid){
			notice1("10",$this->uid);
			$this->success("提交成功，等待审核！",__APP__.'/Member');
		}else{
            $this->error("提交失败，请保留存单，联系客服！",__APP__.'/Member');
        }

		//}

    }
	

 	public function savestatus(){
		// 		$_POST  = 	array (
		//					  'requestId' => '2018110121213627',
		//					  'description' => '',
		//					  'payId' => '181031100326104097',
		//					  'fiscalDate' => '20181031',
		//					  'resultSignature' => 'ec3e07520f289e0f031ec094dbc1c5b4',
		//					  'payType' => '1',
		//					  'bankCode' => 'ccb',
		//					  'totalPrice' => '0.01',
		//					  'tradeAmount' => '0.01',
		//					  'tradeFee' => '0',
		//					  'status' => '2',
		//					  'endTime' => '2018-10-31 15:13:01',
		//					  'userIdIdentity' => '',
		//					  'bankAccount' => 'null',
		//					  'name' => 'null',
		//					  'idNumber' => 'null',
		//					);''
 		$requestId=$_POST['requestId'];
				$done = false;
		$Moneylog = D('member_payonline');
 
		
		if($_POST['status']==2){
		 
				$updata['status'] = 1;
				//echo $nid;
				//echo $oid."<br>";
				
				$updata['tran_id'] = $_POST['payId'];
				//echo $updata['tran_id']."<br>";
			
				$vo = M('member_payonline')->field('uid,money,fee,status,id')->where("nid='{$requestId}'")->find();
					
				if($vo['status']!=0 || !is_array($vo)) return;
				
				$xid = $Moneylog->where("uid={$vo['uid']} AND nid='{$requestId}'")->save($updata);
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
					//在线充值
					notice1("5", $vo['uid'], $data = array("MONEY"=>$vo['money']));
					//分销返佣
					// distribution_maid($vo['id']);
				 // $u=M("member_moneylog")->where("uid=".$vo['uid']." and type=50")->count();
				 //  if($u==0){
					//   $frist_money = getFloatValue($this->glo['frist_money'],2);
					//   $jiangli = getFloatValue($frist_money*$tmoney/100,2);
					//   if(!empty($jiangli)){
					// 	  memberMoneyLog($vo['uid'],50,$jiangli,"首次线上充值奖励",0,'@网站管理员@');//首次充值奖励
					//   }
				 //  }
				} 
				if(!$newid){
					$updata['status'] = 0;
					$Moneylog->where("uid={$vo['uid']} AND nid='{$nid}'")->save($updata);
					return false;
				}
				$vx = M("members")->field("user_phone,user_name")->find($vo['uid']);
				 
			 
			 
		}
		
	 
		//M('member_payonline')->where("requestId=$requestId")->save(['status'=>1]);
		//		 array (
		//			  'requestId' => '130660250101234',
		//			  'description' => '',
		//			  'payId' => '181031100326104097',
		//			  'fiscalDate' => '20181031',
		//			  'resultSignature' => 'ec3e07520f289e0f031ec094dbc1c5b4',
		//			  'payType' => '1',
		//			  'bankCode' => 'ccb',
		//			  'totalPrice' => '0.01',
		//			  'tradeAmount' => '0.01',
		//			  'tradeFee' => '0',
		//			  'status' => '2',
		//			  'endTime' => '2018-10-31 15:13:01',
		//			  'userIdIdentity' => '',
		//			  'bankAccount' => 'null',
		//			  'name' => 'null',
		//			  'idNumber' => 'null',
		//			)
			
			
    }
    public function allcharge(){
		$data['html'] = $this->fetch();
		exit(json_encode($data));
    }
    public function charge(){
    	$ids = M('members_status')->getFieldByUid($this->uid,'id_status');
		if($ids==1){
		}else{
			$this->assign('jumpUrl', __APP__."/member/verify/index.html");
			$this->error('您还未完成身份验证，请先进行实名认证');
			exit;				
		}
		$MM = m("member_money")->field("money_freeze,money_collect,account_money")->find($this->uid);
		if (!is_array($MM)) {
			m("member_money")->add(array(
				"uid" => $this->uid
			));
		}
		$model=M('member_info');
		$minfo = $model->find($this->uid);

		$this->assign("minfo",$minfo);
		$this->assign("payConfig",FS("Webconfig/payconfig"));
		$this->display();
    }
    public function chargeoff(){
		// echo $this->glo['ttxf_off_bank'];
		$bank_list=explode("\n",$this->glo['ttxf_off_bank']);
		//print_r($bank_list);
		$this->assign("bank",$bank_list);
	
		$this->assign("vo",M('article_category')->where("type_name='线下充值'")->find());
		$this->display();
    }
    public function chargelog(){
		$map['uid'] = $this->uid;
		$map['status'] = 1 ;
		if($_GET['start_time']&&$_GET['end_time']){
			$_GET['start_time'] = strtotime($_GET['start_time']." 00:00:00");
			$_GET['end_time'] = strtotime($_GET['end_time']." 23:59:59");
			
			if($_GET['start_time']<$_GET['end_time']){
				$map['add_time']=array("between","{$_GET['start_time']},{$_GET['end_time']}");
				$search['start_time'] = $_GET['start_time'];
				$search['end_time'] = $_GET['end_time'];
			}
		}
		$list = getChargeLog($map,10);
		$this->assign('search',$search);
		$this->assign("list",$list['list']);
		$this->assign("pagebar",$list['page']);
		$this->assign("success_money",$list['success_money']);
		$this->assign("fail_money",$list['fail_money']);
		$model=M('member_banks');
		$mbank = $model->where('uid = '.$this->uid)->find();
		$this->assign('mbank',$mbank);
		$this->display();
    }
    public function zhao() {
		$aa=$_GET['name'];

		if(isset($_GET['name'])&&!empty($_GET['name'])){
			 $aa=$_GET['name'];
		}else{
			 $aa='image';
		}
		if(isset($_GET['size'])&&!empty($_GET['size'])){
			  $validate['size']=$_GET['size'];
		}else{
			  $validate['size']=30000;
		}
		if(isset($_GET['ext'])&&!empty($_GET['ext'])){
			  $validate['ext']=$_GET['ext'];
		} else{
			  $validate['ext']='jpg,png,gif';
		}
		if(isset($_GET['ds'])&&!empty($_GET['ds'])){
			  $sd=$_GET['ds'];
		}else{
			 $sd='uploads';
		}
 
 		$url= '/Public/upload/';
		$name=time().'_'.rand(1, 99);
		import('ORG.Net.UploadFile');
		$upload = new UploadFile();// 实例化上传类 
		$upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
		$upload->savePath = $url ;// 设置附件上传目录
		$upload->saveRule = $name; 
		if($upload->upload()) {// 上传成功 获取上传文件信息
			 
		    $info =  $upload->getUploadFileInfo();
			$msg = $url.$info[0]['savename'];
		    echo($msg) ;
		
		}else{// 上传错误提示错误信息
		    echo $msg = $upload->getErrorMsg();
		}
	}
}
