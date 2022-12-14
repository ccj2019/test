<?php

// 本类由系统自动生成，仅供测试用途

class SafeAction extends MCommonAction {

    public function contract_show(){
    	$this->display();
    }

    public function index(){
//  	$minfo = M('member_info')->find($this->uid);
    	$ids = M('members_status')->getFieldByUid($this->uid,'id_status');
    	$emails = M('members_status')->getFieldByUid($this->uid,'email_status');
    	$phones = M('members_status')->getFieldByUid($this->uid,'phone_status');
    	$email = M('members')->getFieldById($this->uid,'user_email');
    	$phone = M('members')->getFieldById($this->uid,'user_phone');
		
				$minfo2 =getMinfo(session("u_id"),true);
				
					$minfo =getMinfo(session("u_id"),true);
        $all_money = getFloatvalue($minfo['account_money']+$minfo['money_collect']+$minfo['money_freeze'],2);
        // 累计收益
        $minfo['receive_interests'] = M('borrow_investor')->where('investor_uid = '.$uid.' and status = 5')->sum('receive_interest');
		  $this->assign("minfo2", $minfo2);
    	$this->assign("minfo",$minfo);
    	$this->assign("ids",$ids);
    	$this->assign("email",$email);
    	$this->assign("emails",$emails);
    	$this->assign("phones",$phones);
    	$this->assign("phone",$phone);
		$this->display();

    }



    public function email(){

		$sq = M('member_safequestion')->find($this->uid);

		$email = M('members')->getFieldById($this->uid,'user_email');

		$this->assign("sq",$sq);

		$this->assign("email",$email);

		$data['html'] = $this->fetch();

		exit(json_encode($data));

    }



    public function idcard(){

		$ids = M('members_status')->getFieldByUid($this->uid,'id_status');

		if($ids==1){

			$vo = M("member_info")->field('idcard,real_name')->find($this->uid);

			$this->assign("vo",$vo);

			$data['html'] = $this->fetch();

		}

		else  $data['html'] = '<script type="text/javascript">layer.msg("您还未完成身份验证，请先进行实名认证");setTimeout("window.location.href=\''.__APP__.'/member/verify/index.html?id=1#chip-3\'",2000);</script>';

		

		exit(json_encode($data));

		

    }



    public function safequestion(){

		$sqs = M('members_status')->getFieldByUid($this->uid,'safequestion_status');

		if($sqs==0){

			$data['html'] = '<script type="text/javascript">layer.msg("您还未设置安全问题，请先设置安全问题");setTimeout("window.location.href=\''.__APP__.'/member/verify/index.html#chip-6\'",2000);</script>';

		}else{

			$sq = M('member_safequestion')->find($this->uid);

			$this->assign("sq",$sq);

			$this->assign("userphone",M('members')->getFieldById($this->uid,'user_phone'));

			$data['html'] = $this->fetch();

		}

		exit(json_encode($data));

    }

	public function changesafe(){

		$map['answer1'] = text($_POST['a1']);

		$map['answer2']  = text($_POST['a2']);

		$map['uid']  = $this->uid;

		$c = M('member_safequestion')->where($map)->count('uid');

		if($c==0) ajaxmsg('',0);

		else{

			session('temp_safequestion',1);

			ajaxmsg();

		}

	}

	public function changesafeact(){

		$is_can = session('temp_safequestion');

		if($is_can==1){

			$data['uid'] = $this->uid;

			$data['question1'] = text($_POST['q1']);

			$data['question2'] = text($_POST['q2']);

			$data['answer1'] = text($_POST['a1']);

			$data['answer2'] = text($_POST['a2']);

			$newid = M('member_safequestion')->save($data);

			if($newid){

				session('temp_safequestion',NULL);

				ajaxmsg();

			}

			else ajaxmsg('',0);

		}else{

			ajaxmsg('',0);

		}

	

	}



    public function cellphone(){

		$sq = M('member_safequestion')->find($this->uid);

		$phone = M('members')->getFieldById($this->uid,'user_phone');

		$this->assign("sq",$sq);

		$this->assign("phone",$phone);

		$this->display();


    }

	public function sendphonecode(){

		$r = Notice(3,$this->uid);

		if($r) ajaxmsg();

		else ajaxmsg('',0);

	}

	public function sendphonecodex(){

		$p = text($_POST['phone']);

		$c = M('members')->where("user_phone='{$p}'")->count('id');

		if($c>0) ajaxmsg('手机号已存在！',2);

		$r = Notice(4,$this->uid,array('phone'=>$p));

		if($r) ajaxmsg('验证码发送成功！');

		else ajaxmsg('验证码发送失败！',0);

	}

	public function changephone(){

		$vcode = text($_POST['code']);

		$pcode = is_verify($this->uid,$vcode,4,10*60);

		if($pcode){

			session('temp_phone',1);

			ajaxmsg();

		}

		else ajaxmsg('',0);

	}

	public function changephoneact(){


		$vcode = text($_POST['code']);
		$old_phone = text($_POST['old_phone']);
		$new_phone = text($_POST['new_phone']);
		$pcode = is_verify($this->uid,$vcode,5,10*60);
		$oldid = M('members')->where("id={$this->uid} and user_phone = {$old_phone}")->find();

		if($pcode&&$oldid){

			$newid = M('members')->where("id={$this->uid}")->setField('user_phone',text($_POST['new_phone']));

			if($newid) $this->success('修改成功',__APP__."/member");

			else  $this->error('修改失败',__APP__."/member");

		}

		else $this->error('修改失败',__APP__."/member");

	}

	

	

	public function sendemailtov(){

		$r = Notice(5,$this->uid);

		if($r) ajaxmsg();

		else ajaxmsg('',0);

	}

	

	public function doemailchangephone(){

		$code = text($_POST['safecode']);

		$r = is_verify($this->uid,$code,6,10*60);

		if(!$r) ajaxmsg("",2);

		$map['answer1'] = text($_POST['qone']);

		$map['answer2']  = text($_POST['qtwo']);

		$map['uid']  = $this->uid;

		$c = M('member_safequestion')->where($map)->count('uid');

		if($c==0) ajaxmsg('',0);

		session('temp_phone',1);

		ajaxmsg();

	}

	

	

	public function sendverify(){

		$r = Notice(2,$this->uid);

		if($r) echo(1);

		else echo(0);

	}

	

	public function verifyep(){

		$pcode = is_verify($this->uid,text($_POST['pcode']),3,10*60);

		$ecode = is_verify($this->uid,text($_POST['ecode']),3,10*60);



		if($pcode && $ecode){

			session('temp_safequestion',1);

			ajaxmsg();

		}else{

			ajaxmsg('',0);

		}

	}

	

	



}