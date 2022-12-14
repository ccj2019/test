<?php
// 本类由系统自动生成，仅供测试用途
class CommonAction extends MCommonAction {
    var $notneedlogin=true;
    public function index(){
        $this->display();
    }
   public function zmembersuser(){
       //  /Member/Common/membersuser
       $phone=$_POST['phone'];
      $avr= membersuser(["user_phone"=>$phone]);
        echo $avr;
    }

	public function savepaydemostatus(){
 	   
 	    $aa=$_POST;
				// $_POST  = 	array (
				// 			  'requestId' => '2019022511001750',
				// 			  'description' => '',
				// 			  'payId' => '181031100326104097',
				// 			  'fiscalDate' => '20181031',
				// 			  'resultSignature' => 'ec3e07520f289e0f031ec094dbc1c5b4',
				// 			  'payType' => '1',
				// 			  'bankCode' => 'ccb',
				// 			  'totalPrice' => '0.01',
				// 			  'tradeAmount' => '0.01',
				// 			  'tradeFee' => '0',
				// 			  'status' => '2',
				// 			  'endTime' => '2018-10-31 15:13:01',
				// 			  'userIdIdentity' => '',
				// 			  'bankAccount' => 'null',
				// 			  'name' => 'null',
				// 			  'idNumber' => 'null',
				// 			); 
		
		
		/*file_put_contents('zzzceshi.txt' ,var_export($aa, true));die;
		var_dump(2);die;*/
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
				if($xid) $newid = memberMoneyLog($vo['uid'],3,$tmoney,"充值订单号:".$requestId,0,'@网站管理员@');//更新成功才充值,避免重复充值 
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
				// 	distribution_maid($vo['id']);
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
 
    public function login(){
        $from = isset($_GET['from']) ? $_GET['from'] : '/member/';
        $url=urldecode($_REQUEST["url"]);
        $this->assign('url',$url); 
        $this->assign('from',$from);
        $this->display();
    }
    public function weixinlog(){
        // var_dump(1);die;
        $session_id=md5(time().rand(1000,999));
        session($session_id,'');
        $wxurl = "http://".$_SERVER['HTTP_HOST'].'/m/usercenter?session_id='.$session_id;
        $json['wxurl'] = $wxurl;
        $json['session_id'] = $session_id;
        exit(json_encode($json));
    }
    
    public function register(){
        $article = m("article_category")->field('type_content')->where('id=523')->find();
        if($_GET['invite']){
            $this->assign("invite",$_GET['invite']);
            session("tmp_invite_user",$_GET['invite']);
        }
        $this->assign("article",$article);
        $this->display();
    }
    public function actlogin(){
        setcookie('LoginCookie','',time()-10*60,"/");
        //uc登陆
        session('login_user_name',$_POST['sUserName']);
        if(preg_match("/^1\d{10}$/", $_POST['sUserName'])){
            $data['user_phone'] = $_POST['sUserName'];          
        }elseif(filter_var($_POST['sUserName'], FILTER_VALIDATE_EMAIL)){
            $data['user_email'] = text($_POST['sUserName']);
        }else{
            $data['user_name'] = text($_POST['sUserName']);
        }                   

        $vo = M('members')->field('id,user_name,user_email,user_pass,is_ban')->where($data)->find();
       
        if($vo['is_ban']==1){
			ajaxmsg("您的帐户已被冻结，请联系客服处理！",0); 
        } 

        if(!is_array($vo)){
            //本站登陆不成功，偿试uc登陆及注册本站
            ajaxmsg("用户名或者密码错误！",0);
        }else{
            if($vo['user_pass'] == md5($_POST['sPassword']) ){//本站登陆成功，uc登陆及注册UC
           
                // ShopBehavior::sysUser($vo['id']);
                //uc登陆及注册UC
                $this->_memberlogin($vo['id']);           
                ajaxmsg();
            }else{//本站登陆不成功
                ajaxmsg("用户名或者密码错误！",0);
            }
        }
    }
    
    public function actlogout(){
        $this->_memberloginout();
        //uc登陆
        $loginconfig = FS("Webconfig/loginconfig");
        $uc_mcfg  = $loginconfig['uc'];
        if($uc_mcfg['enable']==1){
            require_once C('APP_ROOT')."Lib/Uc/config.inc.php";
            require C('APP_ROOT')."Lib/Uc/uc_client/client.php";
            $logout = uc_user_synlogout();
        }
        //uc登陆
        $this->assign("uclogout",de_xie($logout));
        echo '<script>window.location.href="'.__APP__.'"</script>';
        //$this->success("注销成功",__APP__."/");
    }
    
    private function ucreguser($reg){
        $data['user_name'] = text($reg['txtUser']);
        $data['user_pass'] = md5($reg['txtPwd']);
        $data['user_email'] = text($reg['txtEmail']);
        $count = M('members')->where("user_email = '{$data['user_email']}' OR user_name='{$data['user_name']}'")->count('id');
        if($count>0) return "登陆失败,UC用户名冲突,用户名或者邮件已经有人使用";
        $data['reg_time'] = time();
        $data['reg_ip'] = get_client_ip();
        $data['lastlog_time'] = time();
        $data['lastlog_ip'] = get_client_ip();
        $newid = M('members')->add($data);
        
        if($newid){
            session('u_id',$newid);
            session('u_user_name',$data['user_name']);
            Notice(1,$newid,array('email',$data['user_email']));
            //memberMoneyLog($newid,1,$this->glo['award_reg'],"注册奖励");
            return $newid;
        }
        
        return "登陆失败,UC用户名冲突";
    }
    public function ascc(){
   var_dump($_SESSION['verify'],md5(""));
    }
      public function curl(){ 
          
        $aid='2024559842';
        $AppSecretKey='0rvpQRhlxmJo29MBSuNf5Lg**';
        $Ticket=$_POST['ticket'];
        $Randstr=$_POST['randstr'];
        $UserIP=$_SERVER['REMOTE_ADDR'];
        //初始化
        // $aa = file_get_contents('https://ssl.captcha.qq.com/ticket/verify?aid='.$aid.'&AppSecretKey='.$AppSecretKey.'&Ticket='.$Ticket.'&Randstr='.$Randstr.'&UserIP='.$UserIP);
        // 1. 初始化
         $ch = curl_init();
         // 2. 设置选项，包括URL
         
  $url ='https://ssl.captcha.qq.com/ticket/verify?aid='.$aid.'&AppSecretKey='.$AppSecretKey.'&Ticket='.$Ticket.'&Randstr='.$Randstr.'&UserIP='.$UserIP;
 $con = curl_init((string)$url);
 curl_setopt($con, CURLOPT_HEADER, false);
 curl_setopt($con, CURLOPT_RETURNTRANSFER,true);
 curl_setopt($con, CURLOPT_TIMEOUT, (int)$timeout);
            $_SESSION["vazvcodedata"]=$aa=curl_exec($con); 
       exit(json_encode($aa)); 
 // 4. 释放curl句柄
 


         print_r($aa);
        exit;
     }
    public function sendcode()
    {
        $phone = $_REQUEST['phone'];
        $zz=session("vazvcodedata");
        $aa =  json_decode($zz, true);
        
        // file_put_contents('sssss.txt' ,var_export($aa, true));die;
	    $vazvcodedata =$aa;
	   // var_dump($zz,$vazvcodedata,$vazvcodedata['response']);die;
        if(!preg_match('/^(13\d|19\d|16\d|14[579]|15[^4\D]|17[^49\D]|18\d)\d{8}$/',$phone)){ 
           $json['status'] = "p";
           $json['info'] = "手机号格式不正确！";
           exit(json_encode($json));
        }     
        if(empty($phone) || strlen($phone)!=11 ){
            $json['status'] = "f";
            $json['info'] = "手机号格式不正确！";
            exit(json_encode($json));die;
        }   
        if(empty($phone) || strlen($phone)!=11 ){
            $json['status'] = "f";
            $json['info'] = "手机号格式不正确！";
            exit(json_encode($json));die;
        }
        
		 if($vazvcodedata['response']!=1){
              $json['status'] = "vfa";
            $json['info'] = "图形验证码错误，请重新尝试！";
            exit(json_encode($json));die;
        }    
        
       /* if(isset($_SESSION['reg_code_time']) && (time()-$_SESSION['reg_code_time']) < 120){
            $json['status'] = "f";
            $json['info'] = "您的操作过于频繁，请稍后重试。";
            exit(json_encode($json));die;
        }*/
        $datag = $this->glo;
        $webname = $datag['web_name'];
        $rs = M('members')->where('user_phone='.$phone)->find();
        if($rs){
            $json['status'] = "f";
            $json['info'] = "该手机号已在本站注册，不可重复注册！";
            exit(json_encode($json));
        }        
        preg_match_all('/\d/S', rand(1000,9999).rand(1000,9999), $matches);        
        $arr  = join('', $matches[0]);
        $code = substr($arr, 0, 4); 
        session('reg_code', $code);              
        session('user_phone',$phone);
        $content="您的验证码是".$code."，120秒内有效，若非本人操作请忽略此消息。";//要发送的短信内容
        $result =Notice1(1,$this->uid,array('phone'=>$phone,"code"=>$code));
       // $result = sendsms($phone,$content);
        if($result === true){
                session('reg_code_time',time());
                $json['status'] = "y";
                $json['info'] = "验证码已经发送至您的号码为：".$phone."的手机！";         
        }else{
                $json['status'] = "n";
                $json['info'] = empty($result) ? '发送失败！' : $result;
        }   
        exit(json_encode($json));
    }
    public function regaction(){
            $data['user_name'] = text($_POST['txtUser']);
            $data['user_phone'] = text($_POST['txtPhone']);
            $data['user_pass'] = md5($_POST['txtPwd']);
            $code = text($_POST['code']);
            if($code != $_SESSION['reg_code']){
                $json['status'] = "c";
                $json['info'] = "手机验证码不正确";
                exit(json_encode($json));
            }else if(empty($_SESSION['reg_code'])){
            	     $json['status'] = "c";
                $json['info'] = "手机验证码不正确";
                exit(json_encode($json));
            }
            $count = M('members')->where("user_phone = '{$data['user_phone']}' OR user_name='{$data['user_name']}'")->count('id');
           // echo M('members')->getLastSql();exit;
            if($count>0){ 
                $json['status'] = "n";
                $json['info'] = "注册失败，用户名或者手机号已经有人使用";
                exit(json_encode($json));
            }
            //获取推荐人
            $txtIncode = text($_POST['invite']);
            if(!empty($txtIncode)){
                    $txtRecUserid = M('members')->where("incode='".$txtIncode."'")->find();
                    if(!empty($txtRecUserid)) {
                        $data['recommend_id']=$txtRecUserid['id'];
                        $data['recommend_bid']=$txtRecUserid['recommend_id'];
                        $data['recommend_cid']=$txtRecUserid['recommend_bid'];
                    }else{
                        $json['status'] = "n";
                        $json['info'] = "推荐人不存在，若没有推荐人请留空。";
                        exit(json_encode($json));
                    };
            }

            $data['reg_time'] = time();
            $data['reg_ip'] = get_client_ip();
            $data['lastlog_time'] = time();
            $data['lastlog_ip'] = get_client_ip();
            $data['incode'] = getincode();
            $data['yhname']=getyhincode();
            // if(session("tmp_invite_user"))  $data['recommend_id'] = session("tmp_invite_user");
            // var_dump($data);die;
            $newid = M('members')->add($data);
            
            $vo = M('members_status')->where("uid={$newid}")->find();
            if(!$vo){
              M("members_status")->add(array(
                      "uid" => $newid,
                      "phone_status" => 1,
                      "id_status" => 0,
                      "email_status" => 0,
                      "account_status" => 0,
                      "credit_status" => 0,
                      "safequestion_status" => 0,
                      "video_status" => 0,
                      "face_status" => 0
              ));
            }else{
                $data['phone_status'] = 1;
                M("members_status")->where("uid={$newid}")->save($data);        
            }
            if($newid){
                // ShopBehavior::sysUser($newid);
                session('u_id',$newid);
                session('u_user_name',$data['user_name']);
                if($this->glo['award_reg']>0){
                    pubExperienceMoney($newid,$this->glo['award_reg'],4,90);
                }
                //pubBonusByRules($newid,2);
                $json['status'] = "y";
                $json['info'] = "注册成功";
                exit(json_encode($json));
            }
            else{ 
                $json['status'] = "n";
                $json['info'] = "注册失败，请重试";
                exit(json_encode($json));
            } 
            
    }
    
    public function regsafe(){
        $this->display();
    }

    public function changephoneact(){
        $xs = session('reg_code');
            $uid=session("u_id");
            $vcode = text($_POST['code']);
            if($vcode==$xs){
            $newid = M('members')->where("id={$uid}")->setField('user_phone',text($_POST['mobile']));
            $vo = M('members_status')->where("uid={$uid}")->find();
            if(!$vo){
              M("members_status")->add(array(
                  "uid" => $uid,
                  "phone_status" => 1,
                  "id_status" => 0,
                  "email_status" => 0,
                  "account_status" => 0,
                  "credit_status" => 0,
                  "safequestion_status" => 0,
                  "video_status" => 0,
                  "face_status" => 0
              ));
            }else{
                $data['phone_status'] = 1;
                M("members_status")->where("uid={$uid}")->save($data);      
            }
            //echo M('members_status')->getlastsql();
            session('reg_code',NULL);
            $json['status'] = "y";
            $json['info'] = "手机号码验证成功";
            exit(json_encode($json));   
            }else{
                $json['status'] = "n";
            $json['info'] = "验证码填写错误";
            exit(json_encode($json));   
            }
    }
    
    public function emailverify(){
        $code = text($_GET['vcode']);
        $uk = is_verify(0,$code,1,60*1000);
        if(false===$uk){
            $this->error("验证失败");
        }else{
            $this->assign("waitSecond",3);
            $count = M('members_status')->where("uid={$uk}")->count('uid');
            $data['email_status']=1;
            if($count==0){
                $data['uid']=$uk;
                $data['email_status']=1;
                M('members_status')->where("uid={$uk}")->add($data);
            }else{
                M('members_status')->where("uid={$uk}")->save($data);
            }
            $this->success("验证成功",__APP__."/member");
        }
    }
    
    public function getpasswordverify(){
        $code = text($_GET['vcode']);
        $uk = is_verify(0,$code,7,60*1000);
        if(false===$uk){
            $this->error("验证失败");
        }else{
            session("temp_get_pass_uid",$uk);
            $this->display('getpass');
        }
    }
    
    public function setnewpass(){
            $this->display();
    }
    
    public function dosetnewpass(){
        $per = C('DB_PREFIX');
        $uid = session("temp_get_pass_uid");
                $phone = $_SESSION['phone'];
                $data['user_pass'] = md5(trim($_REQUEST['pass']));
                $newid = M('members')->where("user_phone='{$phone}'")->save($data);
        if($newid){
                    $json['status'] = "1";
                    $json['info'] = "修改成功";
                    exit(json_encode($json));   
        }else{
                    $json['status'] = "0";
                    $json['info'] = "修改失败";
                    exit(json_encode($json));   
        }
    }
    
    

    public function ckphone(){
        $map['user_phone'] = text($_REQUEST['phone']);
        $count = M('members')->where($map)->count('id');
        if ($count == '0') {
                $json['status'] = "1";
                exit(json_encode($json));
        } else {
            $phone = $_REQUEST['phone'];
            if(!preg_match('/^(13\d|19\d|16\d|14[579]|15[^4\D]|17[^49\D]|18\d)\d{8}$/',$phone)){ 
               $json['status'] = "p";
               $json['info'] = "手机号格式不正确！";
               exit(json_encode($json));
            }     
            if(empty($phone) || strlen($phone)!=11 ){
                $json['status'] = "f";
                $json['info'] = "手机号格式不正确！";
                exit(json_encode($json));die;
            }
          /*  if(isset($_SESSION['reg_code_time']) && (time()-$_SESSION['reg_code_time']) < 120){
                $json['status'] = "f";
                $json['info'] = "您的操作过于频繁，请稍后重试。";
                exit(json_encode($json));die;
            }*/
     
            preg_match_all('/\d/S', rand(1000,9999).rand(1000,9999), $matches);        
            $arr  = join('', $matches[0]);
            $code = substr($arr, 0, 4); 
            session('reg_code', $code);              
            session('user_phone',$phone);
            $content="您的验证码是".$code."，120秒内有效，若非本人操作请忽略此消息。";//要发送的短信内容
            $result =Notice1(1,$this->uid,array('phone'=>$phone,"code"=>$code));
            //$result = sendsms($phone,$content);
            if($result === true){
                    session('reg_code_time',time());
                    $json['status'] = "y";
                    $json['info'] = "验证码已经发送至您的号码为：".$phone."的手机！";         
            }else{
                    $json['status'] = "n";
                    $json['info'] = empty($result) ? '发送失败！' : $result;
            }   
            exit(json_encode($json));
        }
    }
    
    
    public function ckphonecode(){
        if($_SESSION['reg_code'] != $_REQUEST['yzm']) {
            $json['status'] = "f";
            $json['info'] = "验证码不正确";   
            exit(json_encode($json));
         }else{
            $per = C('DB_PREFIX');
            $phone = $_REQUEST['phone'];
            $data['user_pass'] = md5(trim($_REQUEST['pass']));
            $newid = M('members')->where("user_phone='{$phone}'")->save($data);
            if($newid){
                $json['status'] = "y";
                $json['info'] = "修改成功";
                exit(json_encode($json));   
            }else{
                $json['status'] = "f";
                $json['info'] = "修改失败";
                exit(json_encode($json));   
            }
        }
    }
    
    
    public function verify(){
        import("ORG.Util.Image");
        Image::buildImageVerify();
    }
    
    public function regsuccess(){
        $this->display();
    }
    

    public function getpassword(){
        $d['content'] = $this->fetch();
        echo json_encode($d);
    }

    public function dogetpass(){
        (false!==strpos($_POST['u'],"@"))?$data['user_email'] = text($_POST['u']):$data['user_name'] = text($_POST['u']);
        $vo = M('members')->field('id')->where($data)->find();
        if(is_array($vo)){
            $res = Notice(7,$vo['id']);
            if($res) ajaxmsg();
            else ajaxmsg('',0);
        }else{
            ajaxmsg('',0);
        }
    }
    public function getpass(){
            $this->display();
    }
    public function test(){
        $txtIncode = 'ez2p';
        if(!empty($txtIncode)){
                $txtRecUserid = M('members')->where("incode='".$txtIncode."'")->find();
                if(!empty($txtRecUserid)) {
                        $data['recommend_id']=$txtRecUserid['id'];
                        $data['recommend_bid']=$txtRecUserid['recommend_id'];
                        $data['recommend_cid']=$txtRecUserid['recommend_bid'];
                }else{
                    $json['status'] = "n";
                    $json['info'] = "推荐人不存在，若没有推荐人请留空。";
                    exit(json_encode($json));
                };
                var_dump($data);
        }
    }

}