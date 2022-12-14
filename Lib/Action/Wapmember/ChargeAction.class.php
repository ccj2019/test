<?php
// 本类由系统自动生成，仅供测试用途
class ChargeAction extends MCommonAction {
    public function index(){
    
		$ids = M('members_status')->getFieldByUid($this->uid,'id_status');
	
		if($ids==1){
		}else{
			echo "<script type='text/javascript'>";
			 		echo "alert('您还未完成身份验证，请先进行实名认证！');";
        				echo "window.location.href='/wapmember/verify/index.html';";
			        	echo "</script>";die; 
		//window.location.href
			//$this->assign('jumpUrl', __APP__."/wapmember/verify/index.html");
						//var_dump($ids );die;
			//$this->error('您还未完成身份验证，请先进行实名认证');
			exit;				
		}
		$MM = m("member_money")->field("money_freeze,money_collect,account_money")->find($this->uid);
		if (!is_array($MM)) {
			m("member_money")->add(array(
				"uid" => $this->uid
			));
		}
		$this->display();
    }
    public function allcharge(){
		$data['html'] = $this->fetch();
		exit(json_encode($data));
    }
	
    public function chargepay(){
	 var_dump($_POST);die;
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
		$dataz['money']=(float)$_POST['money_order'];
		$dataz['fee']=0;
		$dataz['way']="ll";
		//	$status_arr =array('待审核','审核通过,处理中','已提现','审核未通过');
		$dataz['status']=0;
		$dataz['add_time']=time();
		$dataz['add_ip']=get_client_ip();
		$dataz['deal_info'] = "手机端充值" ;
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
 //var_dump($dataz['money'],M('member_payonline')->getlastsql());die;
		if($newid) {
		 
 $member_info=	M('member_info')->where("uid=".$this->uid)->find($dataz);
 $member_infoz=	M('members')->where("id=".$this->uid)->find($dataz);
 $date['user_id']=$this->uid;
 $date['busi_partner']=(isset($_REQUEST['busi_partner'])&&!empty($_REQUEST['busi_partner']))?$_REQUEST['busi_partner']:'109001';
 $date['no_order']=	$dataz['nid'];
 $date['money_order']=$dataz['money'];
 $date['name_goods']= '商品支付';
 $date['url_order']= '日照铭万';
 $date['info_order']= '商品支付';
 $date['bank_code']='';
 
 $date['pay_type']= '';
 $date['id_no']= $member_info['idcard'];
$date['acct_name']= $member_info['real_name']; 
$phone=empty($member_infoz['user_phone'])?'18354393241':$member_infoz['user_phone'];
 $date['no_agree']= '';
 $date['flag_modify']= '';
 $date['card_no']= '';
 $date['back_url']= 'https://www.rzgthy.com/llp/hllp/return_url.php';
 $date['shareing_data']= '';
$phone=empty($member_infoz['user_phone'])?'18354393241':$member_infoz['user_phone'];
$date['risk_item']= '{\"user_info_bind_phone\":\"'. $phone.'\",\"user_info_dt_register\":\"'. date("YmdHis",$member_infoz['reg_time']).'\",\"frms_addr_category\":\"4008\",\"user_info_mercht_userno\":\"'.$member_infoz['id'].'\",\"goods_name\":\"商品购买\",\"delivery_full_name\":\"'.$member_info['real_name'].'\",\"delivery_addr_province\":\"370000\",\"delivery_addr_city\":\"371100\",\"delivery_phone\":\"'. $phone.'\",\"logistics_mode\":\"2\",\"delivery_cycle\":\"1h\",\"goods_count\":\"1\",\"virtual_goods_status\":\"0\", \"risk_state\":\"1\"}';
 $date['valid_order']='';
 
 // var_dump( $date['risk_item'],1);die;
//t_money  user_id
$formurl='https://www.rzgthy.com/llp/hllp/index.php'; 
		if(isset($_POST['id_no'])&&!empty($_POST['id_no'])){
				
				 $date['id_no']= $member_info['idcard'];
				 $date['acct_name']= $member_info['real_name']; 
				$phone=empty($member_infoz['user_phone'])?'18354393241':$member_infoz['user_phone'];
				$date['risk_item']= '{\"user_info_bind_phone\":\"'. $phone.'\",\"user_info_dt_register\":\"'. date("YmdHis",$member_infoz['reg_time']).'\",\"frms_addr_category\":\"4008\",\"user_info_mercht_userno\":\"'.$member_infoz['id'].'\",\"goods_name\":\"商品购买\",\"delivery_full_name\":\"'.$member_info['real_name'].'\",\"delivery_addr_province\":\"370000\",\"delivery_addr_city\":\"371100\",\"delivery_phone\":\"'. $phone.'\",\"logistics_mode\":\"2\",\"delivery_cycle\":\"1h\",\"goods_count\":\"1\",\"virtual_goods_status\":\"0\", \"risk_state\":\"1\"}';
				 //'{\"frms_ware_category\":\"4008\",\"user_info_mercht_userno\":\"123456\",\"user_info_dt_register\":\"20141015165530\",\"user_info_full_name\":\"张三\",\"user_info_id_no\":\"3306821990012121221\",\"user_info_identify_type\":\"1\",\"user_info_identify_state\":\"1\"}';
				 $formurl='https://www.rzgthy.com/llpay/h5/index.php';
				// var_dump($formurl,'1',(isset($_POST['id_no'])&&!empty($_POST['id_no'])));die; 
			}
			
		//	idcard real_name
		echo '<form action="'.$formurl.'" method="post" id="submitForm"   name="submitForm">';
		//			  var_dump($formurl,(isset($_POST['id_no'])&&!empty($_POST['id_no'])));die; 
		foreach($date as $k=>$v){
			if($k== 'risk_item'){
			 echo 	'<textarea rows="5" style="display: none;" cols="60"  size="30" name="risk_item">'.$v.'</textarea>';
			}else{
		            echo '<input name="'.$k.'" type="hidden" value="'.$v.'" />';
			}
		       
			
		}
		 
			 //die;
           	echo ' </form>';
			 //die;
          	echo ' <script type="text/javascript">function load_submit(){document.submitForm.submit()}load_submit();</script>';			 
 
			$this->ajaxReturn("线上充值提交成功，请等待管理员审核",1,1);die;
			echo "<script>alert('线上充值提交成功，请等待管理员审核');  	window.location='/wapmember/'   </script>";
  
			// $this->success("线下充值提交成功，请等待管理员审核",__APP__."/member/charge/chargelog.html");
		} else{
			$this->ajaxReturn('线上充值提交失败，请重试',0,0);die;
			echo "<script>alert('线上充值提交失败，请重试');  	window.location='/wapmember/'   </script>";
			// $this->success("线下充值提交失败，请重试");
		} 				 
 


		//	</form>
		//	</form>
    }


    //验证充值限额
    public function verification(){
        $returnArray["message"] = "快捷支付限额：日限1万，月限5万；\n如需大额充值，请选择线下充值或移至pc端操作。";
        $type = 0;
        $where = null;
        $where["uid"] = array("eq","{$this->uid}");
        $where["way"] = array("eq","ll");
        $where["status"] = array("eq","1");
        $date = date('Y-m-d 00:00:00',time());
        $where['add_time'] = array(array('egt', strtotime($date)), array('elt', time()));
        $dailyMoney = M('member_payonline')->where($where)->sum("money");
        $date = date('Y-m-d 00:00:00',time()-30*24*60*60);
        $where['add_time'] = array(array('egt', strtotime($date)), array('elt', time()));
        $monthMoney = M('member_payonline')->where($where)->sum("money");
        if($_POST["money"] + $dailyMoney > 10000){
        }else if($_POST["money"] + $monthMoney > 50000){
        }else{
            $returnArray["message"] = "验证通过";
            $type = 1;
        }
        ajaxmsg($returnArray,$type);
    }

    //验证充值限额
    public function chargesq(){
        // 表单令牌验证
        // if($_GET['yz']!=$_SESSION["yz"]||$_SESSION["yz"]=='') {
        //     $this->error("禁止重复提交表单",__APP__.'/Wapmember/Charge/charges');
        // }
   //      $_SESSION["yz"]='';

   //  	// $code = $_GET['yzm'];
   //   //    if($_SESSION['verify'] != md5($code)){
   //   //       $this->error('验证码不正确！',__APP__.'/Wapmember/Charge/charges');
   //   //    }else{
   //      	$dataz['money']=(float)$_GET['money_off'];
	        
			// $dataz['uid']=$this->uid;
			// $dataz['nid']="SMF-".date('Ymd').'-'.substr(microtime(),2,6);
			// $dataz['money']=(float)$_GET['money_off'];
			// $dataz['fee']=0;
			// $dataz['way']="smf";
			// //	$status_arr =array('待审核','审核通过,处理中','已提现','审核未通过');
			// $dataz['status']=0;
			// $dataz['add_time']=time();
			// $dataz['add_ip']=get_client_ip();
	  //       $dataz['deal_info'] = "扫码付充值" ;
			// $newid = M('member_payonline')->add($dataz);
			// $this->assign("money_off",$dataz['money']);
			// $this->assign("newid",$newid);
		//}
		$money_off=(float)$_POST['money_order'];
		$this->assign("money_off",$money_off);
		$yz=substr(microtime(),2,6);
		$_SESSION["yz"]=$yz;
		$this->assign("yz",$yz);


		Vendor('JSSDK');
        $jssdk = new JSSDK("wx0a01a5ed7857bad7","4d5be00eaa31ca639f5078b04f20a177");
        $signPackage = $jssdk->GetSignPackage();
        $this->assign('signPackage',$signPackage);

		$this->display();

    }
    public function dosaoma(){

    	 //表单令牌验证
        if($_POST['yz']!=$_SESSION["yz"]||$_SESSION["yz"]=='') {
            $this->error("禁止重复提交表单",__APP__.'/Wapmember/Charge/charges');
        }
        $_SESSION["yz"]='';

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
			$this->success("充值充值成功，等待审核！",__APP__.'/Wapmember/');
		}else{
            $this->error("充值失败，请保留存单，联系客服！",__APP__.'/Wapmember/');
        }

		//}

    }
	public function dosaomawx(){

    	 //表单令牌验证
        if($_POST['yz']!=$_SESSION["yz"]||$_SESSION["yz"]=='') {
            $this->error("禁止重复提交表单",__APP__.'/Wapmember/Charge/charges');
        }
        $_SESSION["yz"]='';


        Vendor('JSSDK');
        //要生成的图片名字
        $targetName ='/Public/uploadsd/'.date('YmdHis').'.jpg';
        $jssdk = new JSSDK("wx0a01a5ed7857bad7","4d5be00eaa31ca639f5078b04f20a177");
        $accessToken = $jssdk->getAccessToken();

        $serverId = $_REQUEST["serverId"];
        //根据微信JS接口上传了图片,会返回上面写的images.serverId（即media_id），填在下面即可
        $str="http://file.api.weixin.qq.com/cgi-bin/media/get?access_token={$accessToken}&media_id={$serverId}";
        $a = file_get_contents($str);

        //以读写方式打开一个文件，若没有，则自动创建
        $resource = fopen($_SERVER['DOCUMENT_ROOT'].$targetName , 'wb');
        //将图片内容写入上述新建的文件
        fwrite($resource, $a);
        //关闭资源
        fclose($resource);

        // $jsons["info"] = $_SERVER['HTTP_HOST'].$targetName;
        // $jsons["status"]= "1";
        // outJson($jsons);


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
			$dataz['way_img']=$targetName;
			$dataz['add_ip']=get_client_ip();
	        $dataz['deal_info'] = "扫码付充值" ;
			$newid = M('member_payonline')->add($dataz);
			// $this->assign("money_off",$dataz['money']);
			// $this->assign("newid",$newid);

		if($newid){
			$this->success("充值充值成功，等待审核！",__APP__.'/Wapmember/');
		}else{
            $this->error("充值失败，请保留存单，联系客服！",__APP__.'/Wapmember/');
        }

		//}

    }

    public function charge(){
    	$ids = M('members_status')->getFieldByUid($this->uid,'id_status');
		if($ids==1){
		}else{
			$this->assign('jumpUrl', __APP__."/wapmember/verify/index.html");
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
    public function charges(){
    	$ids = M('members_status')->getFieldByUid($this->uid,'id_status');
		if($ids==1){
		}else{
			$this->assign('jumpUrl', __APP__."/wapmember/verify/index.html");
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
    public function chargez(){
    	$ids = M('members_status')->getFieldByUid($this->uid,'id_status');
		if($ids==1){
		}else{
			$this->assign('jumpUrl', __APP__."/wapmember/verify/index.html");
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
		Vendor('JSSDK');
        $jssdk = new JSSDK("wx0a01a5ed7857bad7","4d5be00eaa31ca639f5078b04f20a177");
        $signPackage = $jssdk->GetSignPackage();
        $this->assign('signPackage',$signPackage);
        
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
//		var_dump($list);die;
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
}
