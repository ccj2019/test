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
    	$member_info = M("members")->find($this->uid);
    	$this->assign('member_info',$member_info);
    	$this->display();
    }
    public function pinpass(){
		$member_info = M("members")->find($this->uid);
    	$this->assign('member_info',$member_info);
    	$this->display();
    }
	public function Verification(){
	 	$phone=$_POST['phone'];
   		preg_match_all('/\d/S', rand(1000,9999).rand(1000,9999), $matches);        
	    $arr  = join('', $matches[0]);
	    $code = substr($arr, 0, 4);        
	    session('verify', $code);              
        session('user_phone',$phone);        
        if(empty($code)) die;
        $content= "您正在修改登录密码，本次验证码{$code}，若非本人操作请忽略此消息。";
		 $type = empty($_REQUEST['type'])?1:$_REQUEST['type'];
         $sendRs =  Notice1($type,$this->uid,array('phone'=>$phone,"code"=>$code));
//      $sendRs = sendsms($phone, $content);       
        if($sendRs === true){
                session('reg_code_time',time());
                $json['status'] = "y";
                $json['info'] = "验证码已经发送至您的号码为：".$phone."的手机！";          
        }else{
                $json['status'] = "n";
                $json['info'] = empty($sendRs) ? '发送失败！' : $sendRs;
        }    
        exit(json_encode($json));
    }
    public function changepass(){
		$user_phone = strval($_POST['phone']);
		$newpwd1 = md5($_POST['newpwd1']);
		$code = $_POST['Verification'];
        $phone = $_POST['phone'];
        if($_SESSION['verify'] != strval($code)){
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
    	$user_phone = strval($_POST['phone']);
		$newpwd1 = md5($_POST['newpwd1']);
		$code = $_POST['Verification'];
        $phone = $_POST['phone'];
        if($_SESSION['verify'] != strval($code)){
           $json['status'] = "m";
           $json['info'] = "验证码不正确！";
           ajaxmsg('',3);
       	}
		$c = M('members')->where("id={$this->uid} AND user_phone = '{$user_phone}'")->count();
		if($c==0) ajaxmsg('',2);
		$newid = M('members')->where("id={$this->uid}")->setField('pin_pass',$newpwd1);
		if($newid){
			MTip('chk1',$this->uid);
			ajaxmsg();
		}
		else ajaxmsg('',0);
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
        preg_match_all('/\d/S',$_SESSION['verify'], $matches);          
        $arr = join('',$matches[0]);            
        $code = substr($arr , 0 , 4);           
        session('get_pin_code',$code);
        session('user_phone',$_POST["txtPhone"]);
        if(empty($_SESSION['verify']) || empty($code)) die;

        $content= "您正在修改支付密码，本次验证码{$code}，若非本人操作请忽略此消息。";
        $type = empty($_REQUEST['type'])?1:$_REQUEST['type'];
        $sendRs =  Notice1($type,$this->uid,array('phone'=>$phone,"code"=>$code));
//	    $sendRs = sendsms($_POST["txtPhone"], $content,1);      
        
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
