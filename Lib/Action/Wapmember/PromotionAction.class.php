<?php
// 本类由系统自动生成，仅供测试用途
class PromotionAction extends MCommonAction {
    public function index(){
		$this->display();
    }
    public function  yaoqing(){
		$_P_fee=get_global_setting();
		$this->assign("reward",$_P_fee);
		$incode = M('members')->getFieldById($this->uid,'incode');
		$this->assign("incode",$incode);
		
		$this->display();
    }
    public function promotion(){
		$_P_fee=get_global_setting();
		$this->assign("reward",$_P_fee);
		$this->assign("incode",M('members')->getFieldById($this->uid,'incode'));		
		// $frist_money = explode('|', $_P_fee['frist_money']);
		// $this->assign('cfg_money1',$_P_fee['recommend_money']);
		// $this->assign('cfg_money2',$frist_money[0]);
		// $this->assign('cfg_money3',$frist_money[1]);
		$this->display();
    }
    public function promotionlog(){
		$map['uid'] = $this->uid;
		$map['type'] = array("in","1,13,221");
		$list = getMoneyLog($map,15);
		
		$totalR = M('member_moneylog')->where("uid={$this->uid} AND type in(1,13,221)")->sum('affect_money');
		$this->assign("totalR",$totalR);		
		$this->assign("CR",M('members')->getFieldById($this->uid,'reward_money'));		
		$this->assign("list",$list['list']);		
		$this->assign("pagebar",$list['page']);		
		// $data['html'] = $this->fetch();
		// exit(json_encode($data));
		$this->display();
    }
	public function promotionfriend(){
		$pre = C('DB_PREFIX');
		$uid=session('u_id');
		// $field = " m.id,m.user_name,m.reg_time,sum(ml.affect_money) jiangli ";
		$field1 = " m.user_name,m.reg_time,m.recommend_id,m.recommend_bid,m.recommend_cid,m.id";
		
		// $vm = M("members m")->field($field)->join(" lzh_member_moneylog ml ON m.id = ml.target_uid ")->where(" m.recommend_id ={$uid} AND ml.type =13")->group("ml.target_uid")->select();
		
		$vm1 = M("members m")->field($field1)->where(" m.recommend_id ={$uid}")->group("m.id")->order("id desc")->select();
		//var_dump( M("members m")->getlastsql());
		// $this->assign("vm",$vm);	
		$this->assign("vi",$vm1);
		$this->display();
    }
}