<?php
// 本类由系统自动生成，仅供测试用途
class CommonAction extends HCommonAction {
		
	 public function  savepaydemo(){	
		   // /home/common/savepaydemo
		     // return 1;die;
 	    $aa=$_POST;
		 
	  
 		$requestId=$_POST['requestId'] ;
				$done = false;
		$Moneylog = D('member_payonline'); 
		
 
		
		if($_POST['status']==2){
		 
				$updata['status'] = 1; 
				
				$updata['tran_id'] = $_POST['payId'] ; 
			
				$vo = M('member_payonline')->field('uid,money,fee,status,id')->where("nid='{$requestId}'")->find();
					
				if($vo['status']!=0 || !is_array($vo)) return;
				
				$xid = $Moneylog->where("uid={$vo['uid']} AND nid='{$requestId}'")->save($updata);
				//print_r($xid);
				$tmoney = floatval($vo['money'] - $vo['fee']);
				if($xid) $newid = memberMoneyLog($vo['uid'],3,$tmoney,"充值订单号:".$requestId,0,'@网站管理员@');//更新成功才充值,避免重复充值  
				if($newid){ 
					notice1("5", $vo['uid'], $data = array("MONEY"=>$vo['money'])); 
				} 
				if(!$newid){
					$updata['status'] = 0;
					$Moneylog->where("uid={$vo['uid']} AND nid='{$nid}'")->save($updata);
					return false;
				}
				$vx = M("members")->field("user_phone,user_name")->find($vo['uid']); 
			 
		}
		var_dump(1);die;
	  
}

    public function video(){
		if(!$this->uid) ajaxmsg("请先登陆",0);
		$vs = M('members_status')->getFieldByUid($this->uid,'video_status');
		if($vs==1) ajaxmsg("您已通过视频认证，无需再次认证",0);
		$vxs = M('video_apply')->where("uid={$this->uid} AND apply_status=0")->count('id');
		if($vxs>=1) ajaxmsg("您已经提交申请，请等待客服人员处理",0);

		
		$newid = memberMoneyLog($this->uid,22,-($this->glo['fee_video']),$info="申请视频认证");
		
		if($newid){
			$save['uid'] = $this->uid;
			$save['add_time'] = time();
			$save['add_ip'] = get_client_ip();
			$save['apply_status'] = 0;
			$newidx = M('video_apply')->add($save);
			if($newidx) ajaxmsg("申请成功，请等待客服与您联系");
			else ajaxmsg("申请失败，如已被扣除费用，请联系客服");
		}
		else ajaxmsg("申请失败，请重试");
	}
    public function face(){
		if(!$this->uid) ajaxmsg("请先登陆",0);
		$vs = M('members_status')->getFieldByUid($this->uid,'face_status');
		if($vs==1) ajaxmsg("您已通过现场认证，无需再次认证",0);
		$vxs = M('face_apply')->where("uid={$this->uid} AND apply_status=0")->count('id');
		if($vxs>=1) ajaxmsg("您已经提交申请，请等待客服人员处理",0);

		$newid = memberMoneyLog($this->uid,26,-($this->glo['fee_face']),$info="申请现场认证");
		
		if($newid){
			$save['uid'] = $this->uid;
			$save['add_time'] = time();
			$save['add_ip'] = get_client_ip();
			$save['apply_status'] = 0;
			$newidx = M('face_apply')->add($save);
			if($newidx) ajaxmsg("申请成功，请等待客服与您联系");
			else ajaxmsg("申请失败，请重试");
		}
		else ajaxmsg("申请失败，请重试");
	}
}