<?php
// 本类由系统自动生成，仅供测试用途
class BankAction extends MCommonAction {
    public function index(){
    	$str=getenv("HTTP_REFERER");
     if(eregi("/withdraw/","$str")){ 
		 setcookie('url',$str);
		 $this->assign("shangyiyeurl",$str ); 
		  $this->assign("xuze",1 ); 
	}else{
		if(empty($_COOKIE['url'])){
			$this->assign("shangyiyeurl", "/Wapmember/safe/"); 
		}else{
			$this->assign("shangyiyeurl",$_COOKIE['url']); 
		}
		
		
//		echo "0"; die;
	}
			$voinfo = M("member_info")->field('idcard,real_name,sex')->find($this->uid);
			$vobank = M("member_banks")->where('uid='.$this->uid)->field(true)->select();		
			foreach($vobank as $k=>$v){
				if($v['bank_num']!=""){
				//$vobank['bank_num']+=0;
//				$vobank[$k]['bank_num']=str_pad($v['bank_num'],19,"0",STR_PAD_LEFT);
				//$vobank['bank_num']=sprintf("%019d",$vobank['bank_num']);
			}			
			$vobank[$k]['bank_province'] = M('area')->getFieldByName("{$v['bank_province']}",'id');
			$vobank[$k]['bank_city'] = M('area')->getFieldByName("{$v['bank_city']}",'id');
			}
			//var_dump($vobank);
			
			$voinfo["chenghu"]=$voinfo["sex"]=="女"?"女士":"先生";
			$this->assign("voinfo",$voinfo);
			$this->assign("vobank",$vobank);			
			$this->assign("bank_list",C('BANK_NAME'));
			session('verify',md5(rand(1000,9999))); 
			$this->display();
		 
		
    }
    
	    public function savebank(){
    	
    	if(isset($_GET['show'])&&$_GET['show']==1){
    	 $voinfo = M("member_info mi")->join("lzh_members on lzh_members.id=mi.uid")->field('mi.idcard,mi.real_name real_name,mi.sex,lzh_members.*')->find($this->uid);
			//var_dump($voinfo);die;
			$vobank = M("member_banks")->where('uid='.$this->uid)->field(true)->find();			
			if($vobank['bank_num']!=""){
				//$vobank['bank_num']+=0;
				//$vobank['bank_num']=str_pad($vobank['bank_num'],19,"0",STR_PAD_LEFT);
				//$vobank['bank_num']=sprintf("%019d",$vobank['bank_num']);
			}	
			if($voinfo['real_name']==''){
    				echo "<script type='text/javascript'>";
			 		 
        				echo "window.location.href='/Wapmember/';";
			        	echo "</script>";die;  
    		}		
			$vobank['bank_province'] = M('area')->getFieldByName("{$vobank['bank_province']}",'id');
			$vobank['bank_city'] = M('area')->getFieldByName("{$vobank['bank_city']}",'id');
			$voinfo["chenghu"]=$voinfo["sex"]=="女"?"女士":"先生";
			$this->assign("voinfo",$voinfo);
			$this->assign("vobank",$vobank);			
			$this->assign("bank_list",C('BANK_NAME'));
			session('verify',md5(rand(1000,9999))); 
			$this->display("indexshow");
			die;
    	}
			$voinfo = M("member_info mi")->join("lzh_members on lzh_members.id=mi.uid")->field('mi.idcard,mi.real_name real_name,mi.sex,lzh_members.*')->find($this->uid);
			//var_dump($voinfo);die;
			$vobank = M("member_banks")->where('uid='.$this->uid." and id =".$_GET["banks_id"])->field(true)->find();	
//			var_dump( M("member_banks")->getlastsql());die;
//var_dump($vobank);
			if($vobank['bank_num']!=""){
				//$vobank['bank_num']+=0;
				//$vobank['bank_num']=str_pad($vobank['bank_num'],19,"0",STR_PAD_LEFT);
				//$vobank['bank_num']=sprintf("%019d",$vobank['bank_num']);
			}	
			if($voinfo['real_name']==''){
    				echo "<script type='text/javascript'>";
			 		echo "alert('您还没有实名认证！');";
        				echo "window.location.href='/Wapmember/verify/index.html?id=1#chip-3';";
			        	echo "</script>";die;  
    		}		
			$vobank['bank_province'] = M('area')->getFieldByName("{$vobank['bank_province']}",'id');
			$vobank['bank_city'] = M('area')->getFieldByName("{$vobank['bank_city']}",'id');
			$voinfo["chenghu"]=$voinfo["sex"]=="女"?"女士":"先生";
			$this->assign("voinfo",$voinfo);
			$this->assign("vobank",$vobank);			
			$this->assign("bank_list",C('BANK_NAME'));
			session('verify',md5(rand(1000,9999))); 
			//$this->assign("bank_list",C('BANK_NAME'));
			$this->display();
	 
    	
	 
    }
    public function bank(){
    	
    	if(isset($_GET['show'])&&$_GET['show']==1){
    	 $voinfo = M("member_info mi")->join("lzh_members on lzh_members.id=mi.uid")->field('mi.idcard,mi.real_name real_name,mi.sex,lzh_members.*')->find($this->uid);
			//var_dump($voinfo);die;
			$vobank = M("member_banks")->where('uid='.$this->uid)->field(true)->find();			
			if($vobank['bank_num']!=""){
				//$vobank['bank_num']+=0;
				$vobank['bank_num']=str_pad($vobank['bank_num'],19,"0",STR_PAD_LEFT);
				//$vobank['bank_num']=sprintf("%019d",$vobank['bank_num']);
			}	
			if($voinfo['real_name']==''){
    				echo "<script type='text/javascript'>";
			 		 
        				echo "window.location.href='/Wapmember/';";
			        	echo "</script>";die;  
    		}		
			$vobank['bank_province'] = M('area')->getFieldByName("{$vobank['bank_province']}",'id');
			$vobank['bank_city'] = M('area')->getFieldByName("{$vobank['bank_city']}",'id');
			$voinfo["chenghu"]=$voinfo["sex"]=="女"?"女士":"先生";
			$this->assign("voinfo",$voinfo);
			$this->assign("vobank",$vobank);			
			$this->assign("bank_list",C('BANK_NAME'));
			session('verify',md5(rand(1000,9999))); 
			$this->display("indexshow");
			die;
    	}
			$voinfo = M("member_info mi")->join("lzh_members on lzh_members.id=mi.uid")->field('mi.idcard,mi.real_name real_name,mi.sex,lzh_members.*')->find($this->uid);
			//var_dump($voinfo);die;
			$vobank = M("member_banks")->where('uid='.$this->uid)->field(true)->find();			
			if($vobank['bank_num']!=""){
				//$vobank['bank_num']+=0;
				$vobank['bank_num']=str_pad($vobank['bank_num'],19,"0",STR_PAD_LEFT);
				//$vobank['bank_num']=sprintf("%019d",$vobank['bank_num']);
			}	
			if($voinfo['real_name']==''){
    				echo "<script type='text/javascript'>";
			 		echo "alert('您还没有实名认证！');";
        				echo "window.location.href='/Wapmember/verify/index.html?id=1#chip-3';";
			        	echo "</script>";die;  
    		}		
			$vobank['bank_province'] = M('area')->getFieldByName("{$vobank['bank_province']}",'id');
			$vobank['bank_city'] = M('area')->getFieldByName("{$vobank['bank_city']}",'id');
			$voinfo["chenghu"]=$voinfo["sex"]=="女"?"女士":"先生";
			$this->assign("voinfo",$voinfo);
			$this->assign("vobank",$vobank);			
			$this->assign("bank_list",C('BANK_NAME'));
			session('verify',md5(rand(1000,9999))); 
			$this->display();
		
    }
	public function savebindbank(){
//	 var_dump($_POST);die;
//var_dump($_SESSION['reg_code']);
			 	 if($_SESSION['reg_code'] != $_REQUEST['reg_code']) {
            
          ajaxmsg('验证码不正确',0);
        }	
		$data['id'] =  $_POST['bank_id'] ;		
		$data['bank_num'] = text($_POST['account']);
		$data['bank_address'] = text($_POST['bank_address']);
		$oldaccount = M('member_banks')->where("bank_num ={$data['bank_num']} and uid ={$this->uid}")->count();
		//echo M('member_banks')->getLastSql();
		$data['bank_name'] = text($_POST['bankname']);
		$data['realname'] = text($_POST['realname']);
		$data['bank_province'] = text($_POST['province']);
		$data['bank_city'] = text($_POST['cityName']);
//		$data['bank_province'] = text($_POST['province']);
//		$data['bank_city'] = text($_POST['cityName']);
		$data['add_ip'] = get_client_ip();
		$data['add_time'] = time();
		
			$data['is_default'] = intval($_POST['is_default']);
				if($data['is_default']==1){
				$newid = M('member_banks')->where("uid = ".$this->uid)->save(["is_default"=>0]);
			}
//is_default
//		if($oldaccount){
//			/////////////////////新增银行卡修改锁定开关 开始 20130510 fans///////////////////////////
////			if(intval($this->glo['edit_bank'])!= 1){
////				ajaxmsg("为了您的帐户资金安全，银行卡已锁定，如需修改，请联系客服", 0 );
////			}
//			/////////////////////新增银行卡修改锁定开关 结束 20130510 fans///////////////////////////
//			$old = text($_POST['oldaccount']);
//			if($old <> $oldaccount) ajaxmsg('原银卡号不对',0);
//			$newid = M('member_banks')->where('uid='.$this->uid)->save($data);
//		}else{
			$data['uid'] = $this->uid;
			
//			M('member_banks')->where('uid = '.$this->uid)->delete();
			if($oldaccount>0){
				
				$newid = M('member_banks')->save($data);
			}else{
				$newid = M('member_banks')->save($data);
			}
			
			//echo M('member_banks')->getLastSql();
//		}
		if($newid){
//			if($data['is_default']==1){
//				$newid = M('member_banks')->where("uid = ".$this->uid)->save(["is_default"=>0]);
//			}
//			$mdata =  M("members")->find($this->uid);
//			if(!empty($mdata['recommend_id']) && $mdata['is_fafang'] == 0){
//              $rec_bonus_money = explode('|', $this->glo['rec_bonus_money']);
//              $recount = M('members')->where("recommend_id = ".$mdata['recommend_id'])->count('id');
//              $bonus['uid'] = $mdata['recommend_id'];
//              //加息券
//              $rdata['uid']=$mdata['recommend_id'];
//              $rdata['interest_rate'] = $rec_bonus_money['0'];
//              $rdata['start_time'] = time();
//              $rdata['end_time'] = strtotime(date('Y-m-d H:i:s', strtotime("+30 day")));
//              $rdata['status'] = '1';
//              $rdata['type'] = '1';
//              $rdata['interest_cause'] = '推荐奖励';
//              $rdata['add_time'] = time();
//              $rs = M('member_interest_rate')->add($rdata);
//              $od['is_fafang'] = 1;
//              M('members')->where('id='.$this->uid)->save($od);
//          }
//			MTip('chk2',$this->uid);
			ajaxmsg('',1);
		}
		else ajaxmsg('操作失败，请重试',0);
	}
  public function abindbank(){
	 
		 	
		$data['bank_num'] = text($_POST['account']);
		$oldaccount = M('member_banks')->where("bank_num ={$data['bank_num']} and uid ={$this->uid}")->count();
		//echo M('member_banks')->getLastSql();
		$data['bank_name'] = text($_POST['bankname']);
		$data['bank_address'] = text($_POST['bankaddress']);
		$data['realname'] = text($_POST['realname']);
		$data['bank_province'] = text($_POST['province']);
		$data['bank_city'] = text($_POST['cityName']);
		$data['add_ip'] = get_client_ip();
		$data['add_time'] = time();
		
			$data['is_default'] = intval($_POST['is_default']);
				if($data['is_default']==1){
				$newid = M('member_banks')->where("uid = ".$this->uid)->save(["is_default"=>0]);
			}
//is_default
//		if($oldaccount){
//			/////////////////////新增银行卡修改锁定开关 开始 20130510 fans///////////////////////////
////			if(intval($this->glo['edit_bank'])!= 1){
////				ajaxmsg("为了您的帐户资金安全，银行卡已锁定，如需修改，请联系客服", 0 );
////			}
//			/////////////////////新增银行卡修改锁定开关 结束 20130510 fans///////////////////////////
//			$old = text($_POST['oldaccount']);
//			if($old <> $oldaccount) ajaxmsg('原银卡号不对',0);
//			$newid = M('member_banks')->where('uid='.$this->uid)->save($data);
//		}else{
			$data['uid'] = $this->uid;
				$data['bank_address'] =$_POST['bank_address'];
			
//			M('member_banks')->where('uid = '.$this->uid)->delete();
			if($oldaccount>0){
				
				$newid = M('member_banks')->save($data);
			}else{
				$newid = M('member_banks')->add($data);
			}
			
			//echo M('member_banks')->getLastSql();
//		}
		if($newid){
//			if($data['is_default']==1){
//				$newid = M('member_banks')->where("uid = ".$this->uid)->save(["is_default"=>0]);
//			}
//			$mdata =  M("members")->find($this->uid);
//			if(!empty($mdata['recommend_id']) && $mdata['is_fafang'] == 0){
//              $rec_bonus_money = explode('|', $this->glo['rec_bonus_money']);
//              $recount = M('members')->where("recommend_id = ".$mdata['recommend_id'])->count('id');
//              $bonus['uid'] = $mdata['recommend_id'];
//              //加息券
//              $rdata['uid']=$mdata['recommend_id'];
//              $rdata['interest_rate'] = $rec_bonus_money['0'];
//              $rdata['start_time'] = time();
//              $rdata['end_time'] = strtotime(date('Y-m-d H:i:s', strtotime("+30 day")));
//              $rdata['status'] = '1';
//              $rdata['type'] = '1';
//              $rdata['interest_cause'] = '推荐奖励';
//              $rdata['add_time'] = time();
//              $rs = M('member_interest_rate')->add($rdata);
//              $od['is_fafang'] = 1;
//              M('members')->where('id='.$this->uid)->save($od);
//          }
//			MTip('chk2',$this->uid);
			ajaxmsg('',1);
		}
		else ajaxmsg('操作失败，请重试'.$oldaccount,0);
	}
  
	public function bindbank(){
	 
			if($_SESSION['reg_code'] != $_REQUEST['reg_code']) {
            
          ajaxmsg('验证码不正确',0);
        }			
		$data['bank_num'] = text($_POST['account']);
		$oldaccount = M('member_banks')->where("bank_num ={$data['bank_num']} and uid ={$this->uid}")->count();
		//echo M('member_banks')->getLastSql();
		$data['bank_name'] = text($_POST['bankname']);
		$data['bank_address'] = text($_POST['bankaddress']);
			$data['bank_address'] = text($_POST['bank_address']);
		$data['bank_province'] = text($_POST['province']);
		$data['bank_city'] = text($_POST['cityName']);
		$data['add_ip'] = get_client_ip();
		$data['add_time'] = time();

		$data['realname'] = text($_POST['realname']);
		$data['idcard'] = text($_POST['idcard']);

			$data['is_default'] = intval($_POST['is_default']);
				if($data['is_default']==1){
				$newid = M('member_banks')->where("uid = ".$this->uid)->save(["is_default"=>0]);
			}
//is_default
//		if($oldaccount){
//			/////////////////////新增银行卡修改锁定开关 开始 20130510 fans///////////////////////////
////			if(intval($this->glo['edit_bank'])!= 1){
////				ajaxmsg("为了您的帐户资金安全，银行卡已锁定，如需修改，请联系客服", 0 );
////			}
//			/////////////////////新增银行卡修改锁定开关 结束 20130510 fans///////////////////////////
//			$old = text($_POST['oldaccount']);
//			if($old <> $oldaccount) ajaxmsg('原银卡号不对',0);
//			$newid = M('member_banks')->where('uid='.$this->uid)->save($data);
//		}else{
			$data['uid'] = $this->uid;
			
//			M('member_banks')->where('uid = '.$this->uid)->delete();
			if($oldaccount>0){
				$bank_id = M('member_banks')->where("bank_num ={$data['bank_num']} and uid ={$this->uid}")->find();
				$newid = M('member_banks')->where('id='.$bank_id['id'])->save($data);
			}else{
				$newid = M('member_banks')->add($data);
			}
			
			//echo M('member_banks')->getLastSql();
//		}
		if($newid){
//			if($data['is_default']==1){
//				$newid = M('member_banks')->where("uid = ".$this->uid)->save(["is_default"=>0]);
//			}
//			$mdata =  M("members")->find($this->uid);
//			if(!empty($mdata['recommend_id']) && $mdata['is_fafang'] == 0){
//              $rec_bonus_money = explode('|', $this->glo['rec_bonus_money']);
//              $recount = M('members')->where("recommend_id = ".$mdata['recommend_id'])->count('id');
//              $bonus['uid'] = $mdata['recommend_id'];
//              //加息券
//              $rdata['uid']=$mdata['recommend_id'];
//              $rdata['interest_rate'] = $rec_bonus_money['0'];
//              $rdata['start_time'] = time();
//              $rdata['end_time'] = strtotime(date('Y-m-d H:i:s', strtotime("+30 day")));
//              $rdata['status'] = '1';
//              $rdata['type'] = '1';
//              $rdata['interest_cause'] = '推荐奖励';
//              $rdata['add_time'] = time();
//              $rs = M('member_interest_rate')->add($rdata);
//              $od['is_fafang'] = 1;
//              M('members')->where('id='.$this->uid)->save($od);
//          }
//			MTip('chk2',$this->uid);
			ajaxmsg('',1);
			//$json['status'] = "1";
            //$json['info'] = "添加成功！";   
            exit(json_encode($json));

		}
		else ajaxmsg('操作失败，请重试',0);
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
        session('bind_bank_code',$code);
        session('user_phone',$_POST["txtPhone"]);
        if(empty($_SESSION['verify']) || empty($code)) die;

        $content= "您正在修改银行卡，本次验证码{$code}，若非本人操作请忽略此消息。";
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
	
	public function delete(){
	   	
    	   $vobank = M("member_banks")->where("id=".$_GET['id'])->delete();	
		   if($vobank){
		   		echo "<script> 	window.location='/wapmember/bank/index'   </script>";
		   } else{
		   	echo "<script> alert('失败'); 	window.location='/wapmember/bank/index'   </script>";
		   }	
	 	
    }
}
