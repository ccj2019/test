<?php
// 本类由系统自动生成，仅供测试用途
class AutoborrowAction extends MCommonAction {
    public function index(){
		$pre = c("DB_PREFIX");
		$map['uid'] = $this->uid;
		$vo = M('auto_borrow')->where($map)->find();		
		$this->assign("auto_borrow",$vo);
		$Bconfig = require C("APP_ROOT")."Conf/borrow_config.php";
		$borrow_duration = explode("|",$this->glo['borrow_duration']);
		$month = range($borrow_duration[0],$borrow_duration[1]);
		$month_time=array();
		foreach($month as $v){
			$month_time[$v] = $v."个月";
		}
		$vminfo = getminfo($this->uid, "m.user_leve,m.time_limit,mm.account_money");
		$this->assign("account_money",$vminfo['account_money']);
    	
		$this->assign("borrow_month_time",$month_time);
		$this->assign("repayment_type",$Bconfig['REPAYMENT_TYPE']);	
		$this->assign("borrow_type",$Bconfig['BORROW_TYPE']);	
		$this->assign("xmembertype",C('XMEMBER_TYPE'));	
		$this->assign("all",M('Auto_borrow mm')->join("{$pre}member_money m ON mm.uid=m.uid")->where("mm.status=1 and m.account_money>=50")->count());
		$this->display();
    }
	
	
	public function doedit(){
		$model=D('Auto_borrow');
		$_X=array('my_friend','not_black','borrow_credit_status','apr_status','award_status');
		foreach($_X as $ve){
			if(!isset($_POST[$ve])) $_POST[$ve]=0;
		}
		$map['uid'] = $this->uid;
		$vo = M('auto_borrow')->where($map)->find();
		if(!is_array($vo)){
			$max_id=M("auto_borrow")->max('yongdao');
			$yongdao=$max_id+1;
			 m("Auto_borrow")->add(array(
           	 "uid" => $this->uid,
			 "yongdao"=>$yongdao
       		 ));
		}
		$savedata = textPost($_POST);
		
		$map['uid'] = $this->uid;
		//$map['id'] = $_POST["id"];
		$vo = M('auto_borrow')->where($map)->find();
		if($vo["status"]!=$_POST["status"]){
			$max_id=M("auto_borrow")->max('yongdao');
			$savedata["yongdao"]=$max_id+1;
		}
		
		$vminfo = getminfo($this->uid, "m.user_leve,m.time_limit,mm.account_money");
		if($vminfo["account_money"]<50){
			$this->error("账户余额不足50元，不能设置开启自动投标！");
		}
		
		if($savedata["tender_type"]==1){
			if($savedata["tender_account"]<50){
				$this->error("投标金额不能小于50元");
			}
		}else{
			if($savedata["tender_rate"]<1){
				$this->error("投标比例只能在1%~20%之间");
			}
			if($savedata["tender_rate"]>20){
				$this->error("投标比例只能在1%~20%之间");
			}
		}
		
		if($savedata["tender_account"]>=$savedata["tender_max_account"]){			
			$this->error("最低投标额必须小于最大投标额度");				
		}
		
		$savedata['uid'] = $this->uid;
        if (false === $model->create($savedata)) {
            $this->error($model->getError());
        }
		////////////////////////////////////////
		 if(!empty($model->tender_account)){
			$model->tender_account =intval($model->tender_account);
		}
		if(!empty($model->tender_rate)){
			$model->tender_rate =intval($model->tender_rate);
		}
		$map['uid'] = $this->uid;
		///////////////////////////////////////
		if($result = $model->where($map)->save()) {
			$id = $_POST['id'];
			addInnerMsg($this->uid,"编辑自动投标参数","您对第{$id}号自动投标参数进行了调整，修改人id:{$this->uid}");
			$this->success("编辑成功");
        } else {
			
			$this->error("编辑失败");
        }
	}
}