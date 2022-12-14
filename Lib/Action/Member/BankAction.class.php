<?php
// 本类由系统自动生成，仅供测试用途
class BankAction extends MCommonAction {
    public function index(){
    	$ids = M('members_status')->getFieldByUid($this->uid,'id_status');
    	M('member_banks')->where('uid = '.$this->uid.' and bank_num=""')->delete();
    	if($ids==1){
			$voinfo = M("member_info")->field('idcard,real_name,sex')->find($this->uid);
			$vobank = M("member_banks")->where('uid='.$this->uid)->field(true)->select();		
			foreach($vobank as $k=>$v){
				$vobank[$k]['bank_province'] = M('area')->getFieldByName("{$vobank['bank_province']}",'id');
				$vobank[$k]['bank_city'] = M('area')->getFieldByName("{$vobank['bank_city']}",'id');
		
			}		
			
			$voinfo["chenghu"]=$voinfo["sex"]=="女"?"女士":"先生";
			$this->assign("voinfo",$voinfo);
			$this->assign("vobank",$vobank);			
			$this->assign("bank_list",C('BANK_NAME'));
			$this->assign("bankList",json_encode(C('BANK_NAME')));
			session('verify',md5(rand(1000,9999))); 
			// var_dump($vobank);die;
			$this->display();
		}else{
		$this->error("请先通过实名认证后再进行投标",__APP__."/member/Memberinfo/index.html");
		exit();
		}
		
    }

	public function delete(){
	   	
    	   $vobank = M("member_banks")->where("id=".$_GET['id'])->delete();	
		   if($vobank){
		   	$this->success('删除成功');die;
		   	edit();
		   } else{
		   		$this->error('删除失败');die;
		   		edit();
		   }	
	 	
    }
    public function bank(){
    	$voinfo = M("member_info")->field('idcard,real_name,sex')->find($this->uid);
    	$this->assign("voinfo",$voinfo);
    	$this->assign("bank_list",C('BANK_NAME'));
		$this->display();
    }
	
	public function bindbank(){
		$oldaccount = M('member_banks')->getFieldByUid($this->uid,"bank_num");
		if($_SESSION['bind_bank_code'] != $_REQUEST['yzm']) {
            $json['status'] = "f";
            $json['info'] = "验证码不正确";   
            exit(json_encode($json));
        }
		if($oldaccount){
			$d['is_default']=0;
			M('member_banks')->where('uid = '.$this->uid)->save($d);
		}
		 
		$data['bank_address'] = text($_POST['bank_address']);
		$data['bank_num'] = text($_POST['account']);
		$data['bank_name'] = text($_POST['bankname']);
		$data['bank_province'] = text($_POST['province']);
		$data['bank_city'] = text($_POST['cityName']);

		$data['realname'] = text($_POST['realname']);
		$data['idcard'] = text($_POST['idcard']);
		$data['phone'] = text($_POST['phone']);
		$data['is_default'] = 1;

		$data['add_ip'] = get_client_ip();
		$data['add_time'] = time();

		$data['uid'] = $this->uid;
		// M('member_banks')->where('uid = '.$this->uid)->delete();
		$newid = M('member_banks')->add($data);

		if($newid){
			$mdata =  M("members")->find($this->uid);
			MTip('chk2',$this->uid);
			ajaxmsg();
		}
		else ajaxmsg('操作失败，请重试',0);
	}
    public function sendcode()
    {
        $phone = $_REQUEST['phone'];

        if(isset($_SESSION['bind_code_time']) && (time()-$_SESSION['bind_code_time']) < 60){
            $json['status'] = "f";
            $json['info'] = "您的操作过于频繁，请稍后重试。";
            exit(json_encode($json));die;
        }
        $datag = $this->glo;
        $webname = $datag['web_name'];     
        preg_match_all('/\d/S', rand(1000,9999).rand(1000,9999), $matches);        
        $arr  = join('', $matches[0]);         
        $code = substr($arr , 0 , 4);          
        session('bind_bank_code',$code);
        session('user_phone',$_POST["phone"]);
        $content= "您正在绑定银行卡，本次验证码{$code}，若非本人操作请忽略此消息。";
        $type = empty($_REQUEST['type'])?1:$_REQUEST['type'];
        $type =1;
        $sendRs =  Notice1($type,$this->uid,array('phone'=>$phone,"code"=>$code));
	   // $sendRs = sendsms($_POST["phone"], $content);      
        
        if($sendRs === true){
            session('bind_code_time',time());
            $json['status'] = "y";
            $json['info'] = "验证码已经发送至您的号码为：".$_POST["phone"]."的手机！";         
        }else{
            $json['status'] = "n";            
            $json['info'] = empty($sendRs) ? '发送失败！' : $sendRs;
        }       
        exit(json_encode($json));
    }	
	
	public function getarea(){
		$rid = intval($_GET['rid']);
		if(empty($rid)) return;
		$map['reid'] = $rid;
		$alist = M('area')->field('id,name')->order('sort_order DESC')->where($map)->select();
		//$this->display("Public:empty");
		if(count($alist)===0){
			$str="<option value=''>--该地区下无下级地区--</option>\r\n";
		}else{
			foreach($alist as $v){
				$str.="<option value='{$v['id']}'>{$v['name']}</option>\r\n";
			}
		}
		$data['option'] = $str;
		$res = json_encode($data);
		echo $res;
	}	
}
