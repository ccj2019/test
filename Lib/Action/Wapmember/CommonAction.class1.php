<?php
// 本类由系统自动生成，仅供测试用途
class CommonAction extends WapmCommonAction {
    var $notneedlogin=true;
    public function index(){
        $this->display();
    }
  public function pinpass(){
		session('verify',md5(rand(1000,9999))); 
		$this->display();
    }
	 public function Verification(){
	 	$phone=$_POST['phone'];
		  	if(empty($_REQUEST['vcode']) ||  $_SESSION["verify"]!=md5($_REQUEST['vcode']) ){
              			$json['status'] = "p";
                       	$json['info'] = "验证码不正确！1";
                       exit(json_encode($json));die;
       }
		     
		               preg_match_all('/\d/S', rand(1000,9999).rand(1000,9999), $matches);        
            $arr  = join('', $matches[0]);
            $code = substr($arr, 0, 4); 
            session('reg_code', $code);              
            session('user_phone',$phone);
            $content="您的验证码是".$code."，120秒内有效，若非本人操作请忽略此消息。";//要发送的短信内容
            $result =Notice1(1,$this->uid,array('phone'=>$phone,"code"=>$code));
            // sendsms($phone,$content);

		if($result === true){
                       session('reg_code_time',time());
                       $json['status'] = "y";
                       $json['info'] = "验证码已经发送至您的号码为：".$phone."的手机！";          
               }else{
                       $json['status'] = "n";
                       $json['info'] = empty($sendRs) ? '发送失败！' : $sendRs;
               }
			   
			    exit(json_encode($json));
 }
//   exit(json_encode($code));
		
//	          preg_match_all('/\d/S', rand(1000,9999).rand(1000,9999), $matches);        
//     $arr  = join('', $matches[0]);
//     $code = substr($arr, 0, 4);        
//     session('verify', $code);              
//             session('user_phone',$phone);        
//             if(empty($code)) die;
//             $content= "您正在注册网站会员，手机验证码。{$code}";
//             $sendRs = sendsms($phone, $content,1);       
//             if($sendRs === true){
//                     session('reg_code_time',time());
//                     $json['status'] = "y";
//                     $json['info'] = "验证码已经发送至您的号码为：".$phone."的手机！";          
//             }else{
//                     $json['status'] = "n";
//                     $json['info'] = empty($sendRs) ? '发送失败！' : $sendRs;
//             }
//			   
//             // 未开通短信前，测试用
//              $json['status'] = "s";
//              $json['info'] = "未开通短信前，测试用:验证码已经发送至您的号码为：".$phone."的手机！".$code;     
//             exit(json_encode($json));
//             }
    public function changepass(){
		$user_phone = strval($_POST['phone']);
		$newpwd1 = md5($_POST['newpwd1']);
		 $code = $_POST['Verification'];
        $phone = $_POST['phone'];
        if($_SESSION['reg_code'] != strval($code)){
           $json['status'] = "m";
           $json['info'] = "验证码不正确！";
           ajaxmsg('',3);
       	}
       	//id={$this->uid} AND 
		$c = M('members')->where("user_phone = '{$user_phone}'")->count();
		$find = M('members')->where("user_phone = '{$user_phone}'")->find();
		if($c==0) ajaxmsg('',2);
		$newid = M('members')->where("user_phone = '{$user_phone}'")->setField('user_pass',$newpwd1);
		if($newid){
			MTip('chk1',$find["id"]);
			ajaxmsg();
		}
		else ajaxmsg('',0);
    }
	
	
    public function login(){
    	//echo 1111;exit;
        //session('u_id',3634);
    //  session('u_user_name',"ginagu");
     if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ) {
           header("location:/Wapmember/common/weilogin.html") ;
    }    
  
                $from = isset($_GET['from']) ? $_GET['from'] : '/member/';
                $this->assign('from',$from);
                $this->display();

    }
    public function weilogin(){
    	//echo 1111;exit;
        //session('u_id',3634);
    //  session('u_user_name',"ginagu");
	if(empty($_SESSION['u_user_name'])){
		   	$globalz=M('global')->select();
			foreach($globalz as $k=>$v){
				$global[$v['code']]=$v['text'];
			}
		//	var_dump($global);die;
//	var_dump($global['weixinappid']);	 
  //获取token
//		$datass['appId']='2018050916380600068'; 
//		$datass['appKey']='JM34AbbcRI9VzQ';
//$datass['appId']=$global['appid']; 
// 		$datass['appKey']=$global['appkey'];
		$appid=$global['weixinappid'];//"wx0a01a5ed7857bad7";
		$url="https://open.weixin.qq.com/connect/oauth2/authorize?appid=";
		$url.=$appid;
		$url.="&redirect_uri=";
		$url.="http://".$_SERVER['SERVER_NAME']."/Wapmember/common/weiixnhuidiao";
		$url.="&response_type=code";
		$url.="&scope=snsapi_userinfo";
		$url.="&state="."123";
		$url.="#wechat_redirect";
// 	echo $url;die;
 		     header("location:$url") ;
		 
	}

//参数	是否必须	说明
//appid	是	公众号的唯一标识
//redirect_uri	是	授权后重定向的回调链接地址， 请使用 urlEncode 对链接进行处理
//response_type	是	返回类型，请填写code
//scope	是	应用授权作用域，snsapi_base （不弹出授权页面，直接跳转，只能获取用户openid），snsapi_userinfo （弹出授权页面，可通过openid拿到昵称、性别、所在地。并且， 即使在未关注的情况下，只要用户授权，也能获取其信息 ）
//state	否	重定向后会带上state参数，开发者可以填写a-zA-Z0-9的参数值，最多128字节
//#wechat_redirect	是	无论直接打开还是做页面302重定向时候，必须带此参数
    }
 public function weiixnhuidiao(){
    	//echo 1111;exit;
  //第二步    
  	$globalz=M('global')->select();
			foreach($globalz as $k=>$v){
				$global[$v['code']]=$v['text'];
			}
 
  //  	$globalzz=M('global');
// $globalz= $globalzz->select()
//			foreach($globalz as $k=>$v){
//				$global[$v['code']]=$v['text'];
//			}
//		//	var_dump($global);die;
//		 
////获取token
////		$datass['appId']='2018050916380600068'; 
////		$datass['appKey']='JM34AbbcRI9VzQ';
////$datass['appId']=$global['appid']; 
//// 		$datass['appKey']=$global['appkey'];
 $appid=$global['weixinappid'];//"wx0a01a5ed7857bad7";
 $secret=$global['weixinsecret'];//"4d5be00eaa31ca639f5078b04f20a177";

// var_dump($_GET['code']);
//参数	是否必须	说明
//appid	是	公众号的唯一标识
//secret	是	公众号的appsecret
//code	是	填写第一步获取的code参数
//grant_type	是	填写为 authorization_code
$code = $_GET['code'];
$get_token_url='https://api.weixin.qq.com/sns/oauth2/access_token?appid=';
$get_token_url.=$appid;
$get_token_url.='&secret=';
$get_token_url.=$secret;
$get_token_url.='&code=';
$get_token_url.=$code;
$get_token_url.='&grant_type=authorization_code';


$curl = curl_init(); // 启动一个CURL会话
	curl_setopt($curl, CURLOPT_URL, $get_token_url);
	curl_setopt($curl, CURLOPT_HEADER, 0);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, true);  // 从证书中检查SSL加密算法是否存在
	$res = curl_exec($curl);     //返回api的json对象
	//关闭URL请求
	curl_close($res);
	$json_obj=json_decode($res,true);
	   
$access_token=$json_obj['access_token'];
//var_dump($json_obj);die;
$openid=$json_obj['openid'];
$get_user_info_url='https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';
//var_dump($get_user_info_url);
//die;

 
$curl = curl_init(); // 启动一个CURL会话
	curl_setopt($curl, CURLOPT_URL, $get_user_info_url);
	curl_setopt($curl, CURLOPT_HEADER, 0);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, true);  // 从证书中检查SSL加密算法是否存在
	$res = curl_exec($curl);     //返回api的json对象
	//关闭URL请求
	curl_close($res);
//	$json_obj=json_decode($res,true);
//   
////解析json 
	 
 $user_obj=json_decode($res,true);
$data=array();
 if(isset($_SESSION['openid']) && !empty($_SESSION['openid'])){
 	 $data["openid"] = $_SESSION['openid'];
 }else{
 	 $_SESSION['openid'] =$user_obj['openid'];
//	 var_dump($_SESSION['openid']);
//	 die;
 	  $data["openid"]=$user_obj['openid'];
 }

 $members=M('members')->where($data)->find();
// var_dump($members);
// die;
// var_dump(M('members')->getlastsql(),$user_obj['openid']);die;
// var_dump(M('members')->getlastsql(),$members,$data,$user_obj['openid']);die;

 if(!empty($members["id"])){
 	     	$this->_memberlogin($members['id']);      
           
			echo "<script type='text/javascript'>";
			echo "alert('登录成功！');";
        	echo "window.location.href='/wap';";
			echo "</script>";die;
 }else{
 		echo "<script type='text/javascript'>";
 	echo "window.location.href='/wapmember/common/weixinregister.html?openid=".$user_obj['openid']."'";
 	echo "</script>";die;
 }

 
//var_dump($res);
//$_SESSION['user'] = $user_obj;  
//	返回	  
//参数	描述"oFfm3w74SULva92NNPGfQiOIi3l0
//openid	用户的唯一标识
//nickname	用户昵称
//sex	用户的性别，值为1时是男性，值为2时是女性，值为0时是未知
//province	用户个人资料填写的省份
//city	普通用户个人资料填写的城市
//country	国家，如中国为CN
//headimgurl	用户头像，最后一个数值代表正方形头像大小（有0、46、64、96、132数值可选，0代表640*640正方形头像），用户没有头像时该项为空。若用户更换头像，原有头像URL将失效。
//privilege	用户特权信息，json 数组，如微信沃卡用户为（chinaunicom）
//unionid	只有在用户将公众号绑定到微信开放平台帐号后，才会出现该字段。
}

  public function weixinregister(){
      
 if(empty($_GET['openid'])){
 	return 1;
 }
// var_dump($_GET['openid']);
    	$this->assign("reg_code_time", session('reg_code_time')); 
        $this->assign("list",$list);
        $this->assign("openid",$_GET['openid']); 
        
        
//      preg_match_all('/\d/S',$_SESSION['verify'], $matches);
//          //print_r($matches);
//      $arr = join('',$matches[0]);
//          //print_r($arr);
//      $code = substr($arr , 0 , 4);
//      //echo $code;
//          //echo $code;
//      session('reg_code',$code);
 
        if(isset($_SESSION['invite']) && !empty($_SESSION['invite'])){

            $this->assign("invite",$_SESSION['invite']);
            //$uidx = M('members')->getFieldByUserName(text($_GET['invite']),'id');
            //if($uidx>0) session("tmp_invite_user",$uidx);
            session("tmp_invite_user",$_SESSION['invite']);
        }
        $this->display();
 
    
 
//	var_dump($_GET['openid']);
  	
  }  
public function selectreg_code(){
	echo $_SESSION['reg_code'];
}
  public function weixinregaction(){
    		error_reporting(E_ALL);
		
            $data['user_name'] = trim(text($_POST['txtUser']));
            $data['user_phone'] = trim(text($_POST['txtPhone']));
           
			$data['openid'] =  $_POST['openid'];
            $code = trim(text($_POST['code']));
          $mev=M('members')->where("user_phone = '{$data['user_phone']}' OR user_name='{$data['user_name']}'")->find(); 
		if(isset($mev)&&empty($mev['openid'])){
			 $me=M('members')->where("user_phone = '{$data['user_phone']}' OR user_name='{$data['user_name']}'")->save(["openid"=>$data['openid']]);
				$this->_memberlogin($members['id']);  
			if($me){
				 session('u_id',$mev["id"]);
                session('u_user_name',$mev['user_name']); 
                if($this->glo['award_reg']>0){
                    pubExperienceMoney($newid,$this->glo['award_reg'],4,30);
                } 
                pubBonusByRules($mev["id"],2);
				$json['status'] = "Y";
                $json['info'] = "绑定成功";
                exit(json_encode($json));
			}else{
				$json['status'] = "c";
                $json['info'] = "绑定失败";
                exit(json_encode($json));
			}
		}
		  if(!empty($me['openid'])&&$me['openid']!=$data['openid']){
		  		$json['status'] = "n";
                $json['info'] = "注册失败，用户名或者手机号已经有人使用";
                exit(json_encode($json));
		  }

            if($code != $_SESSION['reg_code']){
                $json['status'] = "c";
                $json['info'] = "手机验证码不正确";
                exit(json_encode($json));
            }
            
             
            
            //获取推荐人
            $txtIncode = text($_POST['txtIncode']);
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
            // if(session("tmp_invite_user"))  $data['recommend_id'] = session("tmp_invite_user");
			
			if(!empty($me["id"])){
				$data['id']=$me["id"];
		  		$newid = M('members')->save($data);
			}else{
				$data['user_pass'] =  trim(md5("123456"));
				$data['pin_pass'] = trim(md5("123456"));
			  	 $newid = M('members')->add($data);
			}
           
            
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
                // if(!empty($data['recommend_id'])){
                //     $rec_bonus_money = explode('|', $this->glo['rec_bonus_money']);
                //     $recount = M('members')->where("recommend_id = ".$data['recommend_id'])->count('id');
                //     $bonus['uid'] = $data['recommend_id'];
                //     //加息券
                //     $rdata['uid']=$data['recommend_id'];
                //     $rdata['interest_rate'] = $rec_bonus_money['0'];
                //     $rdata['start_time'] = time();
                //     $rdata['end_time'] = strtotime(date('Y-m-d H:i:s', strtotime("+30 day")));
                //     $rdata['status'] = '1';
                //     $rdata['type'] = '1';
                //     $rdata['add_time'] = time();
                //     $rs = M('member_interest_rate')->add($rdata);   

  

                // }
                if($this->glo['award_reg']>0){
                    pubExperienceMoney($newid,$this->glo['award_reg'],4,30);
                }
                // $this->glo['award_reg'] and memberMoneyLog($newid,1,$this->glo['award_reg'],"成功注册奖励",0,'@网站管理员@');
                // Notice(1,$newid,array('email',$data['user_email']));
               // SMStip("regsuccess", $data['user_phone'], array("#USERNAME#"), array($data['user_name']),$newid);
                
              //  pubBonusByRules($newid,2);
                ///Wapmember/verify/index.html
               // Notice(1,$newid,array('email',$data['user_email']));
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
         
    
    public function register(){
//  	var_dump(session('reg_code_time'));
$this->assign("reg_code_time", session('reg_code_time'));
 if(isset($_GET['invite'])&&!empty($_GET['invite'])){
     session('invite',$_GET['invite']);
 }
		     if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ) {
           header("location:/Wapmember/common/weilogin.html") ;
    }  
    	
//   var_dump(date("i:s",session('reg_code_time')));
//	      var_dump(date("i:s",time()));
        $map['is_kf'] = 1;
        //$map['area_id'] = $area;
        $count = M('ausers')->where($map)->count('id');
		
        if($count==0){
            //$map['area_id'] = $city;
            //$count = M('ausers')->where("area_id={$city}")->count('id');
        }
        if($count==0){
            //$map['area_id'] = $province;
            //$count = M('ausers')->where("area_id={$province}")->count('id');
        }
        if($count==0) unset($map['area_id']);       
        
        //分页处理
        import("ORG.Util.Page");
        $count = M('ausers')->where($map)->count('id');
	
        $p = new Page($count, $size);
        $page = $p->show();
        $Lsql = "{$p->firstRow},{$p->listRows}";
        //分页处理
        $list = M('ausers')->where($map)->limit($Lsql)->select();
       
        //$save['province_now'] = $province;
        //$//save['city_now'] = $city;
        //$save['area_now'] = $area;
        //$newid = M('member_info')->where("uid={$this->uid}")->save($save);
        
        $this->assign("list",$list);
        
        
        
        preg_match_all('/\d/S',$_SESSION['verify'], $matches);
            //print_r($matches);
        $arr = join('',$matches[0]);
            //print_r($arr);
        $code = substr($arr , 0 , 4);
        //echo $code;
            //echo $code;
        session('reg_code',$code);
        if($_GET['invite']){

            $this->assign("invite",$_GET['invite']);
            //$uidx = M('members')->getFieldByUserName(text($_GET['invite']),'id');
            //if($uidx>0) session("tmp_invite_user",$uidx);
            session("tmp_invite_user",$_GET['invite']);
        }
        $this->display();
    }
    
    private function actlogin_bak(){
        (false!==strpos($_POST['sUserName'],"@"))?$data['user_email'] = text($_POST['sUserName']):$data['user_name'] = text($_POST['sUserName']);
        $vo = M('members')->field('id,user_name,user_email,user_pass')->where($data)->find();
        if($vo){
            $this->_memberlogin($vo['id']);
            ajaxmsg();
        }else{
            ajaxmsg("用户名不存在",0);    
        }
    }
    
    
    public function actlogin(){
    
        setcookie('LoginCookie','',time()-10*60,"/");
        //uc登陆
        session('login_user_name',$_POST['sUserName']);
        $loginconfig = FS("Webconfig/loginconfig");
        $uc_mcfg  = $loginconfig['uc'];
        if($uc_mcfg['enable']==1){
            require_once C('APP_ROOT')."Lib/Uc/config.inc.php";
            require C('APP_ROOT')."Lib/Uc/uc_client/client.php";
        }
 
        if(preg_match("/^1[34578]\d{9}$/", $_POST['sUserName'])){
            $data['user_phone'] = $_POST['sUserName'];          
        }elseif(filter_var($_POST['sUserName'], FILTER_VALIDATE_EMAIL)){
            $data['user_email'] = text($_POST['sUserName']);
        }else{
            $data['user_name'] = text($_POST['sUserName']);
        }                   
        // (false!==strpos($_POST['sUserName'],"@"))?$data['user_email'] = text($_POST['sUserName']):$data['user_name'] = text($_POST['sUserName']);
        
        $vo = M('members')->field('id,user_name,user_email,user_pass,is_ban')->where($data)->find();
		//  var_dump(M('members')->getlastsql());die;
		//var_dump(M('members')->getLastSql()); die;
        if($vo['is_ban']==1){
        	echo "<script type='text/javascript'>";
			 		echo "alert('您的帐户已被冻结，请联系客服处理！');";
        				echo "window.history.go(-1);";
			        	echo "</script>";die;
        } 
        //ajaxmsg("您的帐户已被冻结，请联系客服处理！",0);
   
        if(!is_array($vo)){
        	    
            //本站登陆不成功，偿试uc登陆及注册本站
            if($uc_mcfg['enable']==1){
                list($uid, $username, $password, $email) = uc_user_login(text($_POST['sUserName']), text($_POST['sPassword']));
                if(@$uid > 0) {
                    $regdata['txtUser'] = text($_POST['sUserName']);
                    $regdata['txtPwd'] = text($_POST['sPassword']);
                    $regdata['txtEmail'] = $email;
                    $newuid = $this->ucreguser($regdata);
					    	echo "<script type='text/javascript'>";
			 		echo "alert('用户名或者密码错误！');";
        				echo "window.history.go(-2);";
			        	echo "</script>";die; 
                    ajaxmsg("用户名或者密码错误！",0);
                    if(is_numeric($newuid)&&$newuid>0){
                        $logincookie = uc_user_synlogin($uid);//UC同步登陆
                        setcookie('LoginCookie',$logincookie,time()+10*60,"/");
                        $this->_memberlogin($newuid);
                     //   ajaxmsg();//登录成功
                    }else{
                    				    	echo "<script type='text/javascript'>";
			 		echo "alert('用户名或者密码错误！');";
        				echo "window.history.go(-1);";
			        	echo "</script>";die;
                        ajaxmsg($newuid,0);
                    }
                }else{
                				    	echo "<script type='text/javascript'>";
			 		echo "alert('用户名或者密码错误！');";
        				echo "window.history.go(-1);";
			        	echo "</script>";die;
                    ajaxmsg("用户名或密码错误！",0);
                }
            }

			    	echo "<script type='text/javascript'>";
			 		echo "alert('用户名或者密码错误！');";
        				echo "window.history.go(-1);";
			        	echo "</script>";die;
            //本站登陆不成功，偿试uc登陆及注册本站
            ajaxmsg("用户名或者密码错误！",0);
        }else{
        	 
			
			//var_dump($vo['user_pass'] == md5($_POST['sPassword']));die;
            if($vo['user_pass'] == md5($_POST['sPassword']) ){//本站登录成功，uc登陆及注册UC
         
              //  ShopBehavior::sysUser($vo['id']);
                //uc登陆及注册UC
                if($uc_mcfg['enable']==1){
                    $dataUC = uc_get_user($vo['user_name']);
                    if($dataUC[0] > 0) {
                        $logincookie = uc_user_synlogin($dataUC[0]);//UC同步登陆
                        setcookie('LoginCookie',$logincookie,time()+10*60,"/");
                    }else{
                        $uid = uc_user_register($vo['user_name'], $_POST['sPassword'], $vo['user_email']);
                        if($uid>0){
                            $logincookie = uc_user_synlogin($dataUC[0]);//UC同步登陆
                            setcookie('LoginCookie',$logincookie,time()+10*60,"/");
                        }
                    }
                }
                //uc登陆及注册UC

                $this->_memberlogin($vo['id']);   
                session($_REQUEST['session_id'],$vo['id']);    
							    	echo "<script type='text/javascript'>";
			 		echo "alert('登录成功！');";
        				echo "window.location.href='/wapmember/';";
			        	echo "</script>";die;          
                ajaxmsg();
            }else{//本站登陆不成功
            			    	echo "<script type='text/javascript'>";
			 		echo "alert('用户名或者密码错误！');";
        				echo "window.history.go(-1);";
			        	echo "</script>";die;
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
		echo "<script type='text/javascript'>";
			 		echo "alert('退出成功！');";
        				echo "window.location.href='/wap/';";
			        	echo "</script>";die;   
        //uc登陆
        $this->assign("uclogout",de_xie($logout));
        $this->success("注销成功",__APP__."/wap");
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
    public function aaa() {
		error_reporting(E_ALL);
		
		session('aaa', time());
		
		var_dump(session('aaa'));  
		 
		 
    	//ump());exit;
    }
	 public function bbb() {
 
		var_dump( $_SESSION['reg_code']);  
		 
		 
    	//ump());exit;
    }
    public function sendcode1() {
    
        $phone = $_REQUEST['phone'];
// 	var_dump($_POST['phone'],$_POST);die;
		
//openid
      if(empty($phone) || strlen($phone)!=11 ){
               $json['status'] = "p";
                       $json['info'] = "手机号格式不正确！1";
                       exit(json_encode($json));die;
       }

       if(!preg_match('/^1[3456789]\d{9}$/',$phone)){ 
           $json['status'] = "p";
           $json['info'] = "手机号格式不正确！";
           exit(json_encode($json));
       }     
 
//     if(isset($_SESSION['reg_code_time']) && (time()-$_SESSION['reg_code_time']) < 120){
//             $json['status'] = "f";
//                     $json['info'] = "您的操作过于频繁，请稍后重试。";
//                     exit(json_encode($json));die;
//     }

 $datag = $this->glo;
       $webname = $datag['web_name'];
	 $_POST["type"]=empty($_POST["type"])?1:$_POST["type"];
//	 var_dump($expression)
if($_POST["type"]==1){
	  $rs = M('members')->where('user_phone='.$phone)->find();
       	if(isset($_REQUEST['openid'])&&!empty($_REQUEST['openid'])){
			 	 if($rs){
               $json['status'] = "f";
                       $json['info'] = "该手机号已在本站注册，不可重复注册！";
                       exit(json_encode($json));
     	 	 }  
		}else{
			 if($rs){
               $json['status'] = "f";
                       $json['info'] = "该手机号已在本站注册，不可重复注册！";
                       exit(json_encode($json));
     	 	 }    
		}
}else if($_POST["type"]==2){
	  $rs = M('members')->where('user_phone='.$phone)->find();
//	  var_dump($rs);
//die;
       	if(isset($_REQUEST['openid'])&&!empty($_REQUEST['openid'])){
			 	 if(!$rs){
               $json['status'] = "f";
                       $json['info'] = "该手机号未在本站注册！";
                       exit(json_encode($json));
      	 }  
		}else{
		   	 if(!$rs){
               $json['status'] = "f";
                       $json['info'] = "该手机号未在本站注册！";
                       exit(json_encode($json));
      		 }  
		}
} else if($_POST["type"]==3){
 
        
}   
     
//        
//             preg_match_all('/\d/S', rand(1000,9999).rand(1000,9999), $matches);        
//     $arr  = join('', $matches[0]);
//     $code = substr($arr, 0, 4); 
//	   
//	      
      

//   
//     $statusStr = array(
//"0" => "短信发送成功",
//"-1" => "参数不全",
//"-2" => "服务器空间不支持,请确认支持curl或者fsocket，联系您的空间商解决或者更换空间！",
//"30" => "密码错误",
//"40" => "账号不存在",
//"41" => "余额不足",
//"42" => "帐户已过期",
//"43" => "IP地址限制",
//"50" => "内容含有敏感词"
//);
//$smsapi = "http://api.smsbao.com/";
//$user = "gthy"; //短信平台帐号
//$pass = md5("1010890372..."); //短信平台密码
//session('reg_code', $code);              
//session('user_phone',$phone); 
////echo session('reg_code');
////echo '<br>';
////echo $code;
////die;
// $content="【铭万网络】您的验证码是".$code."，30秒内有效，若非本人操作请忽略此消息。";//要发送的短信内容
////echo session('reg_code');die;
//$phone = "$phone";//要发送短信的手机号码
//$sendurl = $smsapi."sms?u=".$user."&p=".$pass."&m=".$phone."&c=".urlencode($content);
    preg_match_all('/\d/S', rand(1000,9999).rand(1000,9999), $matches);        
            $arr  = join('', $matches[0]);
            $code = substr($arr, 0, 4); 
            session('reg_code', $code);              
            session('user_phone',$phone);
            $content="您的验证码是".$code."，120秒内有效，若非本人操作请忽略此消息。";//要发送的短信内容
               $result =Notice1(1,$this->uid,array('phone'=>$phone,"code"=>$code));
            // sendsms($phone,$content);
            //sendsms($phone,$content);

		if($result === true){
                       session('reg_code_time',time());
                       $json['status'] = "y";
                       $json['info'] = "验证码已经发送至您的号码为：".$phone."的手机！";          
               }else{
    	
                       $json['status'] = "n";
                       $json['info'] = empty($sendRs) ? '发送失败！' : $sendRs;
               }
			   
			    exit(json_encode($json));
               // 未开通短信前，测试用
               // $json['status'] = "y";
               // $json['info'] = "验证码已经发送至您的号码为：".$phone."的手机！".$code;     
//             exit(json_encode($json));
//echo $statusStr[$result];
//	    



	   
	//建周   短信接口
//      $phone = $_REQUEST['phone'];
//   
//     if(!preg_match('/^(13|15|18|14|17|19)[0-9]{9}$/',$phone)){ 
//         $json['status'] = "p";
//         $json['info'] = "手机号格式不正确！";
//         exit(json_encode($json));
//     }     
//     if(empty($phone) || strlen($phone)!=11 ){
//             $json['status'] = "f";
//                     $json['info'] = "手机号格式不正确！";
//                     exit(json_encode($json));die;
//     }
//     if(isset($_SESSION['reg_code_time']) && (time()-$_SESSION['reg_code_time']) < 120){
//             $json['status'] = "f";
//                     $json['info'] = "您的操作过于频繁，请稍后重试。";
//                     exit(json_encode($json));die;
//     }
//     $datag = $this->glo;
//     $webname = $datag['web_name'];
//     $rs = M('members')->where('user_phone='.$phone)->find();
//     if($rs){
//             $json['status'] = "f";
//                     $json['info'] = "该手机号已在本站注册，不可重复注册！";
//                     exit(json_encode($json));
//     }        
//             preg_match_all('/\d/S', rand(1000,9999).rand(1000,9999), $matches);        
//     $arr  = join('', $matches[0]);
//     $code = substr($arr, 0, 4);        
//     session('reg_code', $code);              
//             session('user_phone',$phone);        
//             if(empty($code)) die;
//             $content= "您正在注册网站会员，手机验证码。{$code}";
//             $sendRs = sendsms($phone, $content,1); 
//			 
//                     session('reg_code_time',time());
//                     $json['status'] = "y";
//                     $json['info'] = "测试站,你的验证码是$code";          
//            die;   
//             if($sendRs === true){
//                     session('reg_code_time',time());
//                     $json['status'] = "y";
//                     $json['info'] = "验证码已经发送至您的号码为：".$phone."的手机！";          
//             }else{
//                     $json['status'] = "n";
//                     $json['info'] = empty($sendRs) ? '发送失败！' : $sendRs;
//             }
//             // 未开通短信前，测试用
//             // $json['status'] = "y";
//             // $json['info'] = "验证码已经发送至您的号码为：".$phone."的手机！".$code;     
//             exit(json_encode($json));
    }    
   
    public function sendcode() {
    	if(empty($_REQUEST['vcode']) ||  $_SESSION["verify"]!=md5($_REQUEST['vcode']) ){
              			$json['status'] = "p";
                       	$json['info'] = "验证码不正确！";
                       exit(json_encode($json));die;
       }
		     
			 	
     
    	//session('reg_code', 44444); 
    	//$_SESSION['reg_code'] = '5555';
		//echo $_SESSION['reg_code'];
//	 $_REQUEST['phone']="18354393241";
        $phone = $_REQUEST['phone'];
// 	var_dump($_POST['phone'],$_POST);die;
		
//openid
      if(empty($phone) || strlen($phone)!=11 ){
               $json['status'] = "p";
                       $json['info'] = "手机号格式不正确！1";
                       exit(json_encode($json));die;
       }

       if(!preg_match('/^1[3456789]\d{9}$/',$phone)){ 
           $json['status'] = "p";
           $json['info'] = "手机号格式不正确！";
           exit(json_encode($json));
       }     
 
//     if(isset($_SESSION['reg_code_time']) && (time()-$_SESSION['reg_code_time']) < 120){
//             $json['status'] = "f";
//                     $json['info'] = "您的操作过于频繁，请稍后重试。";
//                     exit(json_encode($json));die;
//     }

 $datag = $this->glo;
       $webname = $datag['web_name'];
	 $_POST["type"]=empty($_POST["type"])?1:$_POST["type"];
//	 var_dump($expression)
if($_POST["type"]==1){
	  $rs = M('members')->where('user_phone='.$phone)->find();
       	if(isset($_REQUEST['openid'])&&!empty($_REQUEST['openid'])){
			 	 if($rs){
               $json['status'] = "f";
                       $json['info'] = "该手机号已在本站注册，不可重复注册！";
                       exit(json_encode($json));
     	 	 }  
		}else{
			 if($rs){
               $json['status'] = "f";
                       $json['info'] = "该手机号已在本站注册，不可重复注册！";
                       exit(json_encode($json));
     	 	 }    
		}
}else if($_POST["type"]==2){
	  $rs = M('members')->where('user_phone='.$phone)->find();
//	  var_dump($rs);
//die;
       	if(isset($_REQUEST['openid'])&&!empty($_REQUEST['openid'])){
			 	 if(!$rs){
               $json['status'] = "f";
                       $json['info'] = "该手机号未在本站注册！";
                       exit(json_encode($json));
      	 }  
		}else{
		   	 if(!$rs){
               $json['status'] = "f";
                       $json['info'] = "该手机号未在本站注册！";
                       exit(json_encode($json));
      		 }  
		}
}
      
     
//        
//             preg_match_all('/\d/S', rand(1000,9999).rand(1000,9999), $matches);        
//     $arr  = join('', $matches[0]);
//     $code = substr($arr, 0, 4); 
//	   
//	      
      

//   
//     $statusStr = array(
//"0" => "短信发送成功",
//"-1" => "参数不全",
//"-2" => "服务器空间不支持,请确认支持curl或者fsocket，联系您的空间商解决或者更换空间！",
//"30" => "密码错误",
//"40" => "账号不存在",
//"41" => "余额不足",
//"42" => "帐户已过期",
//"43" => "IP地址限制",
//"50" => "内容含有敏感词"
//);
//$smsapi = "http://api.smsbao.com/";
//$user = "gthy"; //短信平台帐号
//$pass = md5("1010890372..."); //短信平台密码
//session('reg_code', $code);              
//session('user_phone',$phone); 
////echo session('reg_code');
////echo '<br>';
////echo $code;
////die;
// $content="【铭万网络】您的验证码是".$code."，30秒内有效，若非本人操作请忽略此消息。";//要发送的短信内容
////echo session('reg_code');die;
//$phone = "$phone";//要发送短信的手机号码
//$sendurl = $smsapi."sms?u=".$user."&p=".$pass."&m=".$phone."&c=".urlencode($content);
    preg_match_all('/\d/S', rand(1000,9999).rand(1000,9999), $matches);        
            $arr  = join('', $matches[0]);
            $code = substr($arr, 0, 4); 
            session('reg_code', $code);              
            session('user_phone',$phone);
            $content="您的验证码是".$code."，120秒内有效，若非本人操作请忽略此消息。";//要发送的短信内容
               $result =Notice1(1,$this->uid,array('phone'=>$phone,"code"=>$code));
            // sendsms($phone,$content);
            //sendsms($phone,$content);

		if($result === true){
                       session('reg_code_time',time());
                       $json['status'] = "y";
                       $json['info'] = "验证码已经发送至您的号码为：".$phone."的手机！";          
               }else{
    	
                       $json['status'] = "n";
                       $json['info'] = empty($sendRs) ? '发送失败！' : $sendRs;
               }
			   
			    exit(json_encode($json));
               // 未开通短信前，测试用
               // $json['status'] = "y";
               // $json['info'] = "验证码已经发送至您的号码为：".$phone."的手机！".$code;     
//             exit(json_encode($json));
//echo $statusStr[$result];
//	    



	   
	//建周   短信接口
//      $phone = $_REQUEST['phone'];
//   
//     if(!preg_match('/^(13|15|18|14|17|19)[0-9]{9}$/',$phone)){ 
//         $json['status'] = "p";
//         $json['info'] = "手机号格式不正确！";
//         exit(json_encode($json));
//     }     
//     if(empty($phone) || strlen($phone)!=11 ){
//             $json['status'] = "f";
//                     $json['info'] = "手机号格式不正确！";
//                     exit(json_encode($json));die;
//     }
//     if(isset($_SESSION['reg_code_time']) && (time()-$_SESSION['reg_code_time']) < 120){
//             $json['status'] = "f";
//                     $json['info'] = "您的操作过于频繁，请稍后重试。";
//                     exit(json_encode($json));die;
//     }
//     $datag = $this->glo;
//     $webname = $datag['web_name'];
//     $rs = M('members')->where('user_phone='.$phone)->find();
//     if($rs){
//             $json['status'] = "f";
//                     $json['info'] = "该手机号已在本站注册，不可重复注册！";
//                     exit(json_encode($json));
//     }        
//             preg_match_all('/\d/S', rand(1000,9999).rand(1000,9999), $matches);        
//     $arr  = join('', $matches[0]);
//     $code = substr($arr, 0, 4);        
//     session('reg_code', $code);              
//             session('user_phone',$phone);        
//             if(empty($code)) die;
//             $content= "您正在注册网站会员，手机验证码。{$code}";
//             $sendRs = sendsms($phone, $content,1); 
//			 
//                     session('reg_code_time',time());
//                     $json['status'] = "y";
//                     $json['info'] = "测试站,你的验证码是$code";          
//            die;   
//             if($sendRs === true){
//                     session('reg_code_time',time());
//                     $json['status'] = "y";
//                     $json['info'] = "验证码已经发送至您的号码为：".$phone."的手机！";          
//             }else{
//                     $json['status'] = "n";
//                     $json['info'] = empty($sendRs) ? '发送失败！' : $sendRs;
//             }
//             // 未开通短信前，测试用
//             // $json['status'] = "y";
//             // $json['info'] = "验证码已经发送至您的号码为：".$phone."的手机！".$code;     
//             exit(json_encode($json));
    }    
    public function regaction(){
    		error_reporting(E_ALL);
		
            $data['user_name'] = trim(text($_POST['txtUser']));
            $data['user_phone'] = trim(text($_POST['txtPhone']));
            $data['user_pass'] =  trim(md5($_POST['txtPwd']));
            $code = trim(text($_POST['code']));
//           session('user_phone'

            if($code != session('reg_code')){
                $json['status'] = "c";
                $json['info'] = "手机验证码不正确";
                exit(json_encode($json));
            }
			  if($data['user_phone'] != session('user_phone')){
                $json['status'] = "c";
                $json['info'] = "手机号不正确";
                exit(json_encode($json));
            }
            $count = M('members')->where("user_phone = '{$data['user_phone']}' OR user_name='{$data['user_name']}'")->count('id');
           // echo M('members')->getLastSql();exit;
            if($count>0){ 
                $json['status'] = "n";
                $json['info'] = "注册失败，用户名或者手机号已经有人使用";
                exit(json_encode($json));
            }
            //uc注册
            $loginconfig = FS("Webconfig/loginconfig");
            $uc_mcfg  = $loginconfig['uc'];
            if($uc_mcfg['enable']==1){
                    require_once C('APP_ROOT')."Lib/Uc/config.inc.php";
                    require C('APP_ROOT')."Lib/Uc/uc_client/client.php";
                    $uid = uc_user_register($data['user_name'], $_POST['txtPwd'], $data['user_email']);
                    if($uid <= 0) {
                            if($uid == -1) {
                                    ajaxmsg('用户名不合法',0);
                            } elseif($uid == -2) {
                                    ajaxmsg('包含要允许注册的词语',0);
                            } elseif($uid == -3) {
                                    ajaxmsg('用户名已经存在',0);
                            }else {
                                    ajaxmsg('未定义',0);
                            }
                    }
            }
            
            //获取推荐人
            $txtIncode = text($_POST['txtIncode']);
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

  			$data['pin_pass'] = trim(md5($_POST['txtPwd']));
            $data['reg_time'] = time();
            $data['reg_ip'] = get_client_ip();
            $data['lastlog_time'] = time();
            $data['lastlog_ip'] = get_client_ip();
            $data['incode'] = getincode();
            // if(session("tmp_invite_user"))  $data['recommend_id'] = session("tmp_invite_user");
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
                // if(!empty($data['recommend_id'])){
                //     $rec_bonus_money = explode('|', $this->glo['rec_bonus_money']);
                //     $recount = M('members')->where("recommend_id = ".$data['recommend_id'])->count('id');
                //     $bonus['uid'] = $data['recommend_id'];
                //     //加息券
                //     $rdata['uid']=$data['recommend_id'];
                //     $rdata['interest_rate'] = $rec_bonus_money['0'];
                //     $rdata['start_time'] = time();
                //     $rdata['end_time'] = strtotime(date('Y-m-d H:i:s', strtotime("+30 day")));
                //     $rdata['status'] = '1';
                //     $rdata['type'] = '1';
                //     $rdata['add_time'] = time();
                //     $rs = M('member_interest_rate')->add($rdata);   

  

                // }
                if($this->glo['award_reg']>0){
                    pubExperienceMoney($newid,$this->glo['award_reg'],4,30);
                }
                // $this->glo['award_reg'] and memberMoneyLog($newid,1,$this->glo['award_reg'],"成功注册奖励",0,'@网站管理员@');
                // Notice(1,$newid,array('email',$data['user_email']));
               // SMStip("regsuccess", $data['user_phone'], array("#USERNAME#"), array($data['user_name']),$newid);
                
               // pubBonusByRules($newid,2);
                
               // Notice(1,$newid,array('email',$data['user_email']));
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
    	error_reporting(E_ALL);
		
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
           // session('reg_code',NULL);
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
    	  	if(empty($_REQUEST['vcode']) ||  $_SESSION["verify"]!=md5($_REQUEST['vcode']) ){
              			$json['status'] = "p";
                       	$json['info'] = "验证码不正确！1";
                       exit(json_encode($json));die;
       }
		     
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
		      	}if(empty($phone) || strlen($phone)!=11 ){
		               	$json['status'] = "f";
		                $json['info'] = "手机号格式不正确！";
		                exit(json_encode($json));die;
		       	}
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
            // sendsms($phone,$content);
            // sendsms($phone,$content);
				if($result  === true){
		            session('reg_code_time',time());
		            $json['status'] = "y";
		            $json['info'] = "验证码已经发送至您的号码为：".$phone."的手机！";          
		        }else{
		            $json['status'] = "n";
		            $json['info'] = empty($sendRs) ? '发送失败！' : $sendRs;
		        }
				exit(json_encode($json));
				//   preg_match_all('/\d/S', rand(1000,9999).rand(1000,9999), $matches);        
				//   $arr  = join('', $matches[0]);
				//   $code = substr($arr, 0, 4);        
				//   session('reg_code', $code);     
				//   session('phone', $_REQUEST['phone']); 
				//   if(empty($code)) die;
				//   $content= "您正在用".$_REQUEST['phone']."找回密码，手机验证码{$code}。";
				//	 $sendRs = sendsms($_REQUEST['phone'], $content,1);      
				//   if($sendRs === true){
				//         $json['status'] = "y";
				//         $json['info'] = "验证码已经发送至您的号码为：".$_REQUEST['phone']."的手机！";         
				//   }else{
				//         $json['status'] = "n";
				//         $json['info'] = empty($sendRs) ? '发送失败！' : $sendRs;
				//   }   
				//    exit(json_encode($json));		 
            }
    }
    public function ckphonecode(){
            if($_SESSION['reg_code'] != $_REQUEST['yzm']) {
                $json['status'] = "f";
                $json['info'] = "验证码不正确";   
                exit(json_encode($json));
             }else{
                $json['status'] = "y";
                exit(json_encode($json));
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

}