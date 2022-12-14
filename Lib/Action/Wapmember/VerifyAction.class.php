<?php
// 本类由系统自动生成，仅供测试用途
class VerifyAction extends MCommonAction {
    public function index(){

    	$user_phone = M('members')->where("id={$this->uid}")->getField('user_phone');
		$this->assign("user_phone",$user_phone);
    	if(isset($_GET['show'])&&$_GET['show']==1){
    			$isid = M('members_status')->getFieldByUid($this->uid,'id_status');
				$this->assign("id_status",$isid);
				$this->display("indexshow");
				die;
    	}
		//if(!$_GET['id']) redirect(__APP__."/member/verify?id=1#chip-1");
		$isid = M('members_status')->getFieldByUid($this->uid,'id_status');
		$this->assign("id_status",$isid);

		$this->display();
    }
	 public function ceshi(){
	     	//?show=1
    	header("Content-Type: text/html;charset=utf-8");
  //获取token
		$datass['appId']='2018050916380600068'; 
		$datass['appKey']='JM34AbbcRI9VzQ';
		 
    /* 获取长效令牌 */
		$token =	yunhetong_login("https://api.yunhetong.com/api/auth/login",$datass['appId'],$datass['appKey']);
	//查询用户
	 $data['uname']="王兆镇";////用户姓名（最长 15 字符）成采南
	 $data['identityRegion']="0";////身份地区：0 大陆，1 香港，2 台湾，3 澳门
 $data['certifyNum']="372330199303086157";////身份证号码，应用内唯一 520181198002175907
 $data['phoneRegion']="0";////手机号地区：0 大陆，1 香港、澳门，2 台湾
	 $data['phoneNo']="18354393241";//手机号：1.大陆,首位为 1，长度 11 位纯数字；2.香港、澳门,长度为 8 的纯数字；3.台湾,长度为 10 的纯数字
 $data['caType']="B2";//证书类型：B2 长效 CA 证书，固定字段
	  $user_userInfo_signerIds=user_userInfo_signerIds("https://api.yunhetong.com/api/user/userInfo/signerIds",$data,$token);
	  var_dump($user_userInfo_signerIds);
	      }
    public function welcome(){
		$data['content'] = $this->fetch();
		exit(json_encode($data));
    }
    public function email(){
		$email = M('members')->field('user_email')->find($this->uid);
		$this->assign("email_status",M('members_status')->getFieldByUid($this->uid,'email_status'));
		$this->assign("email",M('members')->getFieldById($this->uid,'user_email'));
		$this->display();
    }
    public function emailvalided(){
		$status = M("members_status")->getFieldByUid($this->uid,'email_status');
		ajaxmsg('',$status);
    }
  	public function emailvsend1(){
		$status=Notice(8,$this->uid);
		if($status) ajaxmsg('邮件已发送，请注意查收！',1);
		else ajaxmsg('邮件发送失败,请重试！',0);
    }
	
    public function emailvsend(){
		$data['user_email'] = text($_POST['email']);
		$newid = M('members')->where("id = {$this->uid}")->save($data);//更改邮箱，重新激活
		if($newid){
			$status=Notice(8,$this->uid);
			if($status) ajaxmsg('邮件已发送，请注意查收！',1);
			else ajaxmsg('邮件发送失败,请重试！',0);
		}else{
			 ajaxmsg('新邮件修改失败',2);
		}
    }
	public function ckemail(){
		$map['user_email'] = text($_POST['Email']);
		$count = M('members')->where($map)->count('id');
        
		if ($count>0) {
			$json['status'] = 0;
			exit(json_encode($json));
        } else {
			$json['status'] = 1;
			exit(json_encode($json));
        }
	}
    public function idcard(){
		$isid = M('members_status')->getFieldByUid($this->uid,'id_status');
		$this->assign("id_status",$isid);
		$data['html'] = $this->fetch();
		exit(json_encode($data));
    }
    public function saveid(){
    	if($_POST["yzm"]!=session('reg_code')){
    		ajaxmsg("验证码不正确！",0);exit();

    	}
		  		$member=M('members')->where("id = {$this->uid}")->find();
				if(isset($member['recommend_id'])&&!empty($member['recommend_id'])){
					$recommend_ida=$member['recommend_id'];
				}else if(isset($member['recommend_bid'])&&!empty($member['recommend_bid'])){
					$recommend_ida=$member['recommend_bid'];
				}else if(isset($member['recommend_cid'])&&!empty($member['recommend_cid'])){
					$recommend_ida=$member['recommend_cid'];
				}
				
						 
			/**/
			 
		//	var_dump(1);die;
 			/*		 
			$pubDataa = array(
            'uid' => $recommend_ida,
            'money_bonus' => '18',
            'start_time' => date('Y-m-d 00:00:00'),
            'end_time'   => date('Y-m-d 23:59:59', strtotime("+" . $value['expired_day'] . " day", time())),
            'bonus_invest_min' => $value['bonus_invest_min'],            
            );		
			 
  pubBonus($pubData,101);*/
									//合同印章
				header("Content-Type: text/html;charset=utf-8");
////获取token
	$globalz=M('global')->select();
			 
			foreach($globalz as $k=>$v){
				$global[$v['code']]=$v['text'];
			}
		$datass['appId']=$global['appid']; 
   		$datass['appKey']=$global['appkey'];
   
				$dataa['signerid']=$signerId;
//				//var_dump($data);die;
// 	 
//				 	 
//  /* 获取长效令牌 */
		$token = yunhetong_login("https://api.yunhetong.com/api/auth/login",$datass['appId'],$datass['appKey']);
//	 
//	//查询用户
//		// $user_userInfo_signerIds=user_userInfo_signerIds("https://api.yunhetong.com/api/user/userInfo/signerIds",'',$token);
//		//var_dump($user_userInfo_signerIds); die;
//	//创建个人用户
//	//测试(2947848) ; 
//	//$signerId=2947848;
////	 
//		
		$dataz['uname']= $_POST['realname'];////用户姓名（最长 15 字符）成采南
		$dataz['identityRegion']= 0;////身份地区：0 大陆，1 香港，2 台湾，3 澳门
		$dataz['certifyNum']= $_POST['idcard'];////身份证号码，应用内唯一 520181198002175907
		$dataz['phoneRegion']= 0;////手机号地区：0 大陆，1 香港、澳门，2 台湾
		$dataz['phoneNo']=$member['user_phone'];//手机号：1.大陆,首位为 1，长度 11 位纯数字；2.香港、澳门,长度为 8 的纯数字；3.台湾,长度为 10 的纯数字
		$dataz['caType']= "B2";//证书类型：B2 长效 CA 证书，固定字段//证书类型：A1 CFCA 场景， A2 CFCA 长效， B1  ZJCA 场景，B2 ZJCA 长效
	
		$signerId= user_person("https://api.yunhetong.com/api/user/person",$dataz,$token);
//	  	var_dump($signerId);die;  
// var_dump($signerId); 
//		 
//		 	
////		  
////// 创建个人印模 
////
   M('members')->where("uid = {$this->uid}")->save($dataa);
   $datapm['signerId']=$signerId;
   $datapm['borderType']='B2';//边框样式，B1=有边框，B2=无边框
$datapm['fontFamily']='F1'; //字体样式，F1=楷体，F2=华文仿宋，F3=华文楷体，F4=微软雅黑
		$user_personMoulage= user_personMoulage("https://api.yunhetong.com/api/user/personMoulage",$datapm,$token) ;	
    	$user_type = $_POST['user_type'];
    	$riskint = intval($_POST['riskint']);
//		
//			 
// 	 
//
//		 
    	
    	$user_personMoulage=1;
		$isimg = session('idcardimg');
		$isimg2 = session('idcardimg2');
		$data['real_name'] = text($_POST['realname']);
		$data['idcard'] = text($_POST['idcard']);
			
 //company_name , company_idcard
		$data['company_name'] = text($_POST['company_name']);
		$data['company_idcard'] = text($_POST['company_idcard']);
		$data['up_time'] = time();
		$realname = $data['real_name'];
		$idcard   = $data['idcard'];
	 	$rsVerify = 1 ;
		writelog($rsVerify);
 
 		$code = $_POST['Verification'];

// 
//		if($member['user_phone'] != strval($phone)){
//         $json['status'] = "m";
//         $json['info'] = "手机号不正确！";
//         ajaxmsg('',5);
//    	}
//      if($_SESSION['verify'] != strval($code)){
//         $json['status'] = "m";
//         $json['info'] = "验证码不正确！";
//         ajaxmsg('',4);
//    	}

		if($rsVerify){
			
			/////////////////////////
			$data1['idcard'] = text($_POST['idcard']);
			$data1['up_time'] = time();
			$data1['uid'] = $this->uid;
			$data1['status'] = 0;
			$b = M('name_apply')->where("uid = {$this->uid}")->count('uid');
			if($b==1){
				M('name_apply')->where("uid ={$this->uid}")->save($data1);
			}else{
				M('name_apply')->add($data1);
			}

			//if($isimg!=1) ajaxmsg("请先上传身份证正面图片",0);
			//if($isimg2!=1) ajaxmsg("请先上传身份证反面图片",0);
			if(empty($data['real_name'])||empty($data['idcard'])){
				ajaxmsg("请填写真实姓名和身份证号码",0);
			} 
			$xuid = M('member_info')->getFieldByIdcard($data['idcard'],'uid');
			if($xuid>0 && $xuid!=$this->uid) ajaxmsg("此身份证号码已被人使用",0);
			$c = M('member_info')->where("uid = {$this->uid}")->count('uid');
			
			
	
			

			
			if(!empty($signerId)){
				$data['signerid']=$signerId;
				//var_dump($data);die;
			if($c==1){
				$newid = M('member_info')->where("uid = {$this->uid}")->save($data);
			}else{
				$data['uid'] = $this->uid;
				$newid = M('member_info')->add($data);
			}	
			}else{
				$newid=0;
			}
			
			writelog($newid);
//			session('idcardimg',NULL);
//			session('idcardimg2',NULL);
			if($newid){

				
				
				
				
				$ms=M('members_status')->where("uid={$this->uid}")->count();
				if($ms > 0){
					$rs = 1;
				}else{
					$dt['uid'] = $this->uid;
					$dt['id_status'] = 0;
					$rs = M('members_status')->add($dt);
					$rs = 1;
				}
				if($rs){
					$pubDataa = array(
						'uid' => $recommend_ida,
						'money_bonus' => "18.0",
						'start_time' => date('Y-m-d 00:00:00',time()),
						'end_time'   => date('Y-m-d 23:59:59',time()+30*24*60*60),
						'bonus_invest_min' => '1000'        
					);		

					pubBonus($pubDataa);
					pubBonusByRules($this->uid,2);
					addInnerMsg($this->uid,"实名认证通过",'自动认证');
						
						$ms = M('members_status')->where("uid={$this->uid}")->setField('id_status',1);
						$name_apply_data['status']=1;
						$name_apply_data['deal_info']= '自动认证';
						$new = M("name_apply")->where("uid={$this->uid}")->save($name_apply_data);
						
//"user_type":user_type,"riskint":riskint,"phone":phone,"Verification":Verification},
 
						 M('members')->where("id = {$this->uid}")->save(["user_type"=>$user_type,"riskint"=>$riskint,"is_guide"=>1,"signerid"=>$signerId,"moulageId"=>$signerId]);
						 
						if(!($ms === false)){
						    
							ajaxmsg('实名认证通过',1);
						}
						ajaxmsg('实名认证通过',1);
					// }else{
						// $ms=M('members_status')->where("uid={$this->uid}")->setField('id_status',3);
						// if($ms==1){
						// 	ajaxmsg('您的申请提交成功，请耐心等待审核……',1);
						// }else{
						// 	$dt['uid'] = $this->uid;
						// 	$dt['id_status'] = 3;
						// 	M('members_status')->add($dt);
						// }
					// }
					// if($rsVerify){
					// 	addInnerMsg($this->uid,"实名认证通过",'自动认证');
					// 	$ms = M('members_status')->where("uid={$this->uid}")->setField('id_status',1);
					// 	$name_apply_data['status']=1;
					// 	$name_apply_data['deal_info']= '自动认证';
					// 	$new = M("name_apply")->where("uid={$this->uid}")->save($name_apply_data);
					// 	if(!($ms === false)){
					// 		ajaxmsg();
					// 	}
					// }
				}
			}
		}
		ajaxmsg("您输入的真实姓名与身份证号不匹配，请重新输入！",0);
    }
    public function safequestion(){
		$isid = M('members_status')->getFieldByUid($this->uid,'safequestion_status');
		$this->assign("safe_question",$isid);
		$data['html'] = $this->fetch();
		exit(json_encode($data));
    }
	
	public function questionsave(){
		$data['question1'] = text($_POST['q1']);
		$data['question2'] = text($_POST['q2']);
		$data['answer1'] = text($_POST['a1']);
		$data['answer2'] = text($_POST['a2']);
		$data['add_time'] = time();
		$c = M('member_safequestion')->where("uid = {$this->uid}")->count('uid');
		if($c==1) $newid = M("member_safequestion")->where("uid={$this->uid}")->save($data);
		else{
			$data['uid'] = $this->uid;
			$newid = M('member_safequestion')->add($data);
		}
		if($newid){
			M('members_status')->where("uid = {$this->uid}")->setField('safequestion_status',1);
			ajaxmsg();
		}
		else  ajaxmsg("",0);
	}
    public function cellphone(){
		$isid = M('members_status')->getFieldByUid($this->uid,'phone_status');
		$this->assign("phone_status",$isid);
		$data['html'] = $this->fetch();
		exit(json_encode($data));
    }
    public function sendphone(){
		header("Content-type: text/html; charset=utf-8");	
		
		//echo "你好";
		$smsTxt = FS("Webconfig/smstxt");
		//print_r($smsTxt);
		$smsTxt=de_xie($smsTxt);
		//print_r($smsTxt);
		//echo str_replace(array("#UserName#","#CODE#"),array(session('u_user_name'),$code),$smsTxt['verify_phone']);
	//	exit;
		$phone = text($_POST['cellphone']);
		$xuid = M('members')->getFieldByUserPhone($phone,'id');
		if($xuid>0 && $xuid<>$this->uid) ajaxmsg("",2);
		///echo $smsTxt['verify_phone']."你好";
		
		$code = rand_string($this->uid,6,1,2);
		$res = sendsms($phone,str_replace(array("#UserName#","#CODE#"),array(session('u_user_name'),$code),$smsTxt['verify_phone']));
		
		//exit;
		if($res){
			session("temp_phone",$phone);
			ajaxmsg();
		}
		else ajaxmsg("",0);
    }
    public function done(){
		$data['html'] = $this->fetch();
		exit(json_encode($data));
    }
    public function validatephone(){
		$phonestatus = M('members_status')->getFieldByUid($this->uid,'phone_status');
		if($phonestatus==1) ajaxmsg("手机已经通过验证",1);
		if( is_verify($this->uid,text($_POST['code']),2,10*60) ){
			$updata['phone_status'] = 1;
			if(!session("temp_phone")) ajaxmsg("验证失败",0);
			
			$updata1['user_phone'] = session("temp_phone");
			$a = M('members')->where("id = {$this->uid}")->count('id');
			if($a==1) $newid = M("members")->where("id={$this->uid}")->save($updata1);
			else{
				M('members')->where("id={$this->uid}")->setField('user_phone',session("temp_phone"));
			}
			
			$updata2['cell_phone'] = session("temp_phone");
			$b = M('member_info')->where("uid = {$this->uid}")->count('uid');
			if($b==1) $newid = M("member_info")->where("uid={$this->uid}")->save($updata2);
			else{
				$updata2['uid'] = $this->uid;
				M('member_info')->add($updata2);
			}
			$c = M('members_status')->where("uid = {$this->uid}")->count('uid');
			if($c==1) $newid = M("members_status")->where("uid={$this->uid}")->save($updata);
			else{
				$updata['uid'] = $this->uid;
				$newid = M('members_status')->add($updata);
			}
			if($newid){
				ajaxmsg();
				
			}
			else  ajaxmsg("验证失败",0);
		}else{
			ajaxmsg("验证校验码不对，请重新输入！",2);
		}
    }
	public function ajaxupimg(){
		if(!empty($_FILES['imgfile']['name'])){
			$this->fix = false;
			$this->saveRule = date("YmdHis",time()).rand(0,1000)."_{$this->uid}";
			$this->savePathNew = C('MEMBER_UPLOAD_DIR').'Idcard/' ;
			$this->thumbMaxWidth = "1000,1000";
			$this->thumbMaxHeight = "1000,1000";
			$info = $this->CUpload();
			$img = $info[0]['savepath'].$info[0]['savename'];
		}
		if($img){
			$c = M('member_info')->where("uid = {$this->uid}")->count('uid');
			if($c==1){
				$newid = M("member_info")->where("uid={$this->uid}")->setField('card_img',$img);
			}else{
				$data['uid'] = $this->uid;
				$data['card_img'] = $img;
				$newid = M('member_info')->add($data);
			}
			session("idcardimg","1");
			ajaxmsg('',1);
		}
		else  ajaxmsg('',0);
	}
	public function ajaxupimg2(){
		if(!empty($_FILES['imgfile2']['name'])){
			$this->fix = false;
			$this->saveRule = date("YmdHis",time()).rand(0,1000)."_{$this->uid}_back";
			$this->savePathNew = C('MEMBER_UPLOAD_DIR').'Idcard/' ;
			$this->thumbMaxWidth = "1000,1000";
			$this->thumbMaxHeight = "1000,1000";
			$info = $this->CUpload();
			$img = $info[0]['savepath'].$info[0]['savename'];
		}
		if($img){
			$c = M('member_info')->where("uid = {$this->uid}")->count('uid');
			if($c==1){
				$newid = M("member_info")->where("uid={$this->uid}")->setField('card_back_img',$img);
			}else{
				$data['uid'] = $this->uid;
				$data['card_back_img'] = $img;
				$newid = M('member_info')->add($data);
			}
			session("idcardimg2","1");
			ajaxmsg('',1);
		}
		else  ajaxmsg('',0);
	}
    public function face(){
		$this->assign("face_status",M('members_status')->getFieldById($this->uid,'face_status'));
		$data['html'] = $this->fetch();
		exit(json_encode($data));
    }
    public function video(){
		$this->assign("video_status",M('members_status')->getFieldById($this->uid,'video_status'));
		$data['html'] = $this->fetch();
		exit(json_encode($data));
    }
}
