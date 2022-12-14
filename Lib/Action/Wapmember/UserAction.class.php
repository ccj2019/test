<?php
// 本类由系统自动生成，仅供测试用途
class UserAction extends MCommonAction {
    public function index(){
		$this->display();
    }
    public function header(){
		$this->display();
    }
    public function password(){
    		$me=M("members")->where("id=".$this->uid)->find();
			$this->assign("me",$me);
    	$this->display();
    }
    public function pinpass(){
    	    	if(isset($_GET['show'])&&$_GET['show']=1){
    			$me=M("members")->where("id=".$this->uid)->find();
			$this->assign("me",$me);
		
				$this->display("indexshow");
				die;
    	}
    	$me=M("members")->where("id=".$this->uid)->find();
			$this->assign("me",$me);
			
//		session('reg_code',md5(rand(1000,9999))); 
		$this->display();
    }
	 public function Verification(){
	 	$phone=$_POST['phone'];
	 	$type=$_POST['type'];
	 	if(empty($type)){
	 		$type=1;
	 	}
		           preg_match_all('/\d/S', rand(1000,9999).rand(1000,9999), $matches);        
            $arr  = join('', $matches[0]);
            $code = substr($arr, 0, 4); 
            session('reg_code', $code);              
            session('user_phone',$phone);
            $content="您的验证码是".$code."，120秒内有效，若非本人操作请忽略此消息。";//要发送的短信内容
            $result =Notice1($type,$this->uid,array('phone'=>$phone,"code"=>$code));
            //$result = sendsms($phone,$content);

		if($result){
                       session('reg_code_time',time());
                       $json['status'] = "y";
                       $json['info'] = "验证码已经发送至您的号码为：".$phone."的手机！". session('reg_code');          
               }else{
                       $json['status'] = "n";
                       $json['info'] = empty($sendRs) ? '发送失败！' : $sendRs;
               }
			   
			    exit(json_encode($json));
 }
   //  exit(json_encode($code));
		
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
		$c = M('members')->where("id={$this->uid} AND user_phone = '{$user_phone}'")->count();
		if($c==0) ajaxmsg('',2);
		$newid = M('members')->where("id={$this->uid}")->setField('user_pass',$newpwd1);
		if($newid){
			MTip('chk1',$this->uid);
			ajaxmsg();
		}
		else ajaxmsg('',0);
    }
    public function changepin(){
    	$code = $_POST['Verification'];
		if($_SESSION['reg_code'] != strval($code)){
           $json['status'] = "m";
           $json['info'] = "验证码不正确！";
           ajaxmsg('',3);
       	}
    	$user_phone = strval($_POST['phone']);
			$user_phone = strval($_POST['phone']);
		// $old = md5($_POST['oldpwd']);
		$newpwd1 = md5($_POST['newpwd1']);
		$act = @$_POST['act'];
		$c = M('members')->where("id={$this->uid} AND user_phone = '{$user_phone}'")->find();
		// if($old==$newpwd1){
		// 	ajaxmsg("设置失败，请勿让新密码与老密码相同。",0);
		// }
		if($act == 'getpin'){
			$txt_code = @$_POST['txt_code'];
			if(!empty($_SESSION['get_pin_code']) && $_SESSION['get_pin_code'] == $txt_code){
		        $newid = M('members')->where("id={$this->uid}")->setField('pin_pass',$newpwd1);
				if(!($newid === false)) ajaxmsg();
				else ajaxmsg("设置失败，请重试",0);
	        }else{
	            ajaxmsg("手机验证码填不正确！", 0 );
	        }
		}
		$newid = M('members')->where("id={$this->uid}")->setField('pin_pass',$newpwd1);
		if($newid) ajaxmsg();
		else ajaxmsg("设置失败，请重试",0);
		// if(empty($c['pin_pass'])){
		// 	if($c['user_pass'] == $old){
		// 		$newid = M('members')->where("id={$this->uid}")->setField('pin_pass',$newpwd1);
		// 		if($newid) ajaxmsg();
		// 		else ajaxmsg("设置失败，请重试",0);
		// 	}else{
		// 		ajaxmsg("原支付密码(即登陆密码)错误，请重试",0);
		// 	}
		// }else{
		// 	if($c['pin_pass'] == $old){
		// 		$newid = M('members')->where("id={$this->uid}")->setField('pin_pass',$newpwd1);
		// 		if($newid) ajaxmsg();
		// 		else ajaxmsg("设置失败，请重试",0);
		// 	}else{
		// 		ajaxmsg("原支付密码错误，请重试",0);
		// 	}
		// }
    }
    public function msgset(){
		$this->assign("vo",M('sys_tip')->find($this->uid));
		$data['html'] = $this->fetch();
		exit(json_encode($data));
    }
	
	public function savetip(){
		$oldtip = M('sys_tip')->where("uid={$this->uid}")->count('uid');
		$data['tipset'] = text($_POST['Params']);
		$data['uid'] = $this->uid;
		if($oldtip) $newid = M('sys_tip')->save($data);
		else $newid = M('sys_tip')->add($data);
		//$this->display('Public:_footer');
		if($newid) echo 1;
		else echo 0;
	}
    public function sendcode()
    {
        
        $_POST["txtPhone"] = M('members')->getFieldById($this->uid,'user_phone');
        if(isset($_SESSION['reg_code_time']) && (time()-$_SESSION['reg_code_time']) < 120){
            $json['status'] = "f";
            $json['info'] = "您的操作过于频繁，请稍后重试。";
            exit(json_encode($json));die;
        }
        $datag = $this->glo;
        $webname = $datag['web_name'];     
        preg_match_all('/\d/S',$_SESSION['reg_code'], $matches);          
        $arr = join('',$matches[0]);            
        $code = substr($arr , 0 , 4);           
        session('get_pin_code',$code);
        session('user_phone',$_POST["txtPhone"]);
        if(empty($_SESSION['reg_code']) || empty($code)) die;

        $content= "您正在修改支付密码，本次验证码{$code}，若非本人操作请忽略此消息。";
        $sendRs = sendsms($_POST["txtPhone"], $content,1);      
        
        if($sendRs === true){
            session('reg_code_time',time());
            $json['status'] = "y";
            $json['info'] = "验证码已经发送至您的号码为：".$_POST["txtPhone"]."的手机！";         
        }else{
            $json['status'] = "n";
            $json['info'] = empty($sendRs) ? '发送失败！' : $sendRs;
        }
        // 未开通短信前，测试用
        // $json['status'] = "y";
        // $json['info'] = "验证码已经发送至您的号码为：".$_POST["txtPhone"]."的手机！".$code;        
        exit(json_encode($json));
    }
}
