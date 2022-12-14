<?php
// 本类由系统自动生成，仅供测试用途
class WithdrawAction extends MCommonAction {
	
    public function index(){
    	    	
		header("Content-type:text/html;charset=utf-8");

	 
		$this->display();
    }
    public function withdraw(){
    	

		$pre = C('DB_PREFIX');
		
		$field = "m.user_name,m.user_phone,i.real_name,(mm.account_money) as all_money,mm.account_money,mm.back_money,i.real_name,b.bank_num,b.bank_name,b.bank_address";
		$vo = M('members m')->field($field)->join("{$pre}member_info i on i.uid = m.id")->join("{$pre}member_money mm on mm.uid = m.id")->join("{$pre}member_banks b on b.uid = m.id")->where("m.id={$this->uid} and b.is_default=1")->find();

		if(empty($vo['bank_num'])){
			$this->error('您还未绑定银行帐户，请先绑定',__APP__.'/member/bank/index.html');

		}else{
			if($_REQUEST['bankid']){
    			$d['is_default']=0;
				M('member_banks')->where('uid = '.$this->uid)->save($d);
				$d1['is_default']=1;
				M('member_banks')->where('uid = '.$this->uid.' and id='.$_REQUEST['bankid'])->save($d1);
	    	}

			$vobank = M("member_banks")->where("(uid={$this->uid}) and (is_default=1)")->find();	
			$tqfee = explode( "|", $this->glo['fee_tqtx']);
			$this->assign( "fee",$tqfee);
			$now_5time=time()-3600*$tqfee[0];//48小时内时间
			$now_15time=time()-3600*24*$tqfee[1];//15天内时间
			$m_info = M('member_moneylog')->field("sum(affect_money) as money")->where("uid={$this->uid} and (type=3 or type=27) and add_time>{$now_5time}")->order('id desc')->find();  //会员48小时充值的金额
			$m_a_info = M('member_moneylog')->field("sum(affect_money) as money")->where("uid={$this->uid} and (type=3 or type=27) and add_time<{$now_5time} and add_time>{$now_15time}")->order('id desc')->find(); //48小时到15天内充值的金额
			//$vo["free_money"]=bcsub($vo['account_money'],$m_info["money"]+$m_a_info[" money"],2); //免费提现的额度			
			$accountIn = M('member_money_account_in')->field('sum((money_in - money_out)) as money')->where("uid = '{$this->uid}' and status in(1,2) and money_in_type in(0,1,2)")->order('id asc')->find();		
			$vo["free_money"] = isset($accountIn['money']) ? $accountIn['money'] : '0.00';
			$vo["free_money"] = $vo["free_money"] > $vo['all_money'] ? $vo['all_money'] : $vo["free_money"];

			$vo['bank_num']=str_pad($vo['bank_num'],19,"0",STR_PAD_LEFT);	
			$this->assign( "vo",$vo);

			$bank_list=C('BANK_NAME');
			$vo['bank_name']=$bank_list[$vo['bank_name']];
			
		}
		$MM = m("member_money")->field("money_freeze,money_collect,account_money")->find($this->uid);
		// $va = getFloatvalue($MM['account_money']+$MM['money_collect']+$MM['money_freeze'],2);
		$va = $MM['account_money'];
//var_dump($va,$MM['account_money']);
		$this->assign("va",$va);
		$this->assign("vobank",$vobank);
		$this->assign( "vo",$vo);
		$this->display();
    }
	
	public function validate(){
		$pre = C('DB_PREFIX');
		$withdraw_money = floatval($_POST['amount']);
		$pwd = md5($_POST['pwd']);
		$vo = M('members m')->field('mm.account_money,mm.back_money,m.user_leve,m.time_limit')->join("{$pre}member_money mm on mm.uid = m.id")->where("m.id={$this->uid} AND (m.pin_pass='{$pwd}' or m.user_pass='{$pwd}')")->find();
		if(!is_array($vo)) ajaxmsg("",0);
		$all_money=$vo['account_money'];
		if($vo['account_money']<$withdraw_money) ajaxmsg("提现额大于帐户余额",2);
		$start = strtotime(date("Y-m-d",time())." 00:00:00");
		$end = strtotime(date("Y-m-d",time())." 23:59:59");
		$wmap['uid'] = $this->uid;
		$wmap['withdraw_status'] = array("neq",3);
		$wmap['add_time'] = array("between","{$start},{$end}");
		$today_money = M('member_withdraw')->where($wmap)->sum('withdraw_money');	
		$today_time = M('member_withdraw')->where($wmap)->count('id');	
		$tqfee = explode("|",$this->glo['fee_tqtx']);
		/*
		：	1、提现次数：每天可以提现10次
			2、提现上下限：1元<=提现金额<=1000000元
			3、提现手续费：2元/笔
			4、未投资手续费：未投资的部分另外扣取千分之3的手续费
		*/
		$today_limit = $tqfee[0];		
		if(!empty($today_limit) && $today_time>=$today_limit){
			$message = "一天最多只能提现{$today_limit}次";
			ajaxmsg($message,2);
			exit;
		}
		$one_limit_min = $tqfee[1];
		$one_limit_max = $tqfee[2];

		if($withdraw_money < $one_limit_min || $withdraw_money > $one_limit_max*10000) ajaxmsg("单笔提现金额限制为{$one_limit_min}-{$one_limit_max}万元",2);
		//获取充值未投资充值部分
		$accountIn = M('member_money_account_in')->field('sum((money_in - money_out)) as money')->where("uid = '{$this->uid}' and status in(1,2) and money_in_type in(0,1,2)")->order('id asc')->find();		
		$noInvestMoney = isset($accountIn['money']) ? $accountIn['money'] : '0.00';		
		$noInvestMoney = $noInvestMoney > $vo['account_money'] ? $vo['account_money'] : $noInvestMoney;		
		$noNeedFeeMoney = $vo['account_money'] - $noInvestMoney;
		$needFeeMoney = $noNeedFeeMoney > $withdraw_money ? '0.00' : bcsub($withdraw_money, $noNeedFeeMoney, 2);
		// $fee = getfloatvalue($tqfee[3],2);
		// $fee += $needFeeMoney * getfloatvalue($tqfee[4],2) / 1000;
		// $fee = getfloatvalue($fee,2);
		$fee = 0.00;
		$message = "您好，您申请提现{$withdraw_money}元，需收取提现手续费{$fee}元，其中未投资金额为{$needFeeMoney}元，确认要提现吗？";
		
		ajaxmsg("{$message}", 1);
		exit;
		
	}
	public function actwithdraw(){
		$pre = C('DB_PREFIX');
		$withdraw_money = floatval($_POST['amount']);
		$pwd = md5($_POST['pwd']);
		$vm = getMinfo($this->uid,'m.pin_pass');
		if(empty($vm['pin_pass'])){
			$vo = M('members m')->field('mm.account_money,mm.back_money,(mm.account_money+mm.back_money) all_money,m.user_leve,m.time_limit')->join("{$pre}member_money mm on mm.uid = m.id")->where("m.id={$this->uid} AND (m.pin_pass='{$pwd}' or m.user_pass='{$pwd}')")->find();
		}else{
			$vo = M('members m')->field('mm.account_money,mm.back_money,(mm.account_money+mm.back_money) all_money,m.user_leve,m.time_limit')->join("{$pre}member_money mm on mm.uid = m.id")->where("m.id={$this->uid} AND (m.pin_pass='{$pwd}')")->find();
		}
		if(!is_array($vo)) ajaxmsg("支付密码错误",0);
		$all_money=$vo['account_money'];
		$bank_id =   $_POST['bank_id'] ;
		$wmap['withdraw_status'] = array("neq",3);
ini_set('date.timezone','Asia/Shanghai');
		$start=strtotime(date("Y-m-d 00:00:01",time()));
		$end=strtotime(date("Y-m-d 23:59:59",time()));
		$wmap['add_time'] = array("between","{$start},{$end}");
		
		$today_money = M('member_withdraw')->where($wmap)->sum('withdraw_money');	
		
		$today_time = M('member_withdraw')->where($wmap)->count('id');	
			//var_dump(M('member_withdraw')->getlastsql());die;
		$tqfee = explode("|",$this->glo['fee_tqtx']);
	
		/*
		：	1、提现次数：每天可以提现10次
			2、提现上下限：1元<=提现金额<=1000000元
			3、提现手续费：2元/笔
			4、未投资手续费：未投资的部分另外扣取千分之3的手续费
		*/
		$today_limit = $tqfee[0];		
		if(!empty($today_limit) && $today_time>=$today_limit){
			$message = "一天最多只能提现{$today_limit}次";
			ajaxmsg($message,2);
			exit;
		}
		$one_limit_min = $tqfee[1];
		$one_limit_max = $tqfee[2];

		if($withdraw_money < $one_limit_min || $withdraw_money > $one_limit_max*10000) ajaxmsg("单笔提现金额限制为{$one_limit_min}-{$one_limit_max}万元",2);
		//获取充值未投资充值部分
		$accountIn = M('member_money_account_in')->field('sum((money_in - money_out)) as money')->where("uid = '{$this->uid}' and status in(1,2) and money_in_type in(0,1,2)")->order('id asc')->find();		
		$noInvestMoney = isset($accountIn['money']) ? $accountIn['money'] : '0.00';
		$noInvestMoney = $noInvestMoney > $vo['account_money'] ? $vo['account_money'] : $noInvestMoney;		
		$noNeedFeeMoney = $vo['account_money'] - $noInvestMoney;
		$needFeeMoney = $noNeedFeeMoney > $withdraw_money ? '0.00' : bcsub($withdraw_money, $noNeedFeeMoney, 2);
		// $fee = getfloatvalue($tqfee[3],2);
		// $fee += $needFeeMoney * getfloatvalue($tqfee[4],2) / 1000;
		$fee = 0.00;
		$member_banks = M('member_banks') ->where("id = ".$bank_id) ->find();	
//		id
//uid
//bank_id
//bank_num
//bank_name
//bank_name_cn
//bank_address
//withdraw_money
//withdraw_status
//withdraw_fee
//add_time
//add_ip
//deal_time
//deal_user
//deal_info
//second_fee
//success_money
//second_money
//cfg_req_sn
		$moneydata['bank_num'] = $member_banks['bank_num'];
		$moneydata['bank_name'] = $member_banks['bank_name'];
		$moneydata['bank_id'] = $bank_id;
//		var_dump(M('lzh_member_banks')->getlastsql());
//		die;	
		$moneydata['withdraw_money'] = $withdraw_money;
		$moneydata['withdraw_fee'] = 0;
		$moneydata['second_fee'] = $fee;
		$moneydata['second_money'] = $withdraw_money;
		$moneydata['withdraw_status'] = 0;
		$moneydata['uid'] =$this->uid;
		$moneydata['add_time'] = time();
		$moneydata['add_ip'] = get_client_ip();			
		$newid = M('member_withdraw')->add($moneydata);			
		if($newid){
			memberMoneyLog($this->uid,4,-$withdraw_money,"提现，手续费".$fee."元"."其中未投资金额为{$needFeeMoney}元",'0','@网站管理员@',-($fee));
			MTip('chk6',$this->uid);			
			ajaxmsg("恭喜，提现申请提交成功",1);
		}
		exit;
			
		
	}
	
	public function backwithdraw(){
		$id = intval($_GET['id']);
		$map['withdraw_status'] = 0;
		$map['uid'] = $this->uid;
		$map['id'] = $id;
		$vo = M('member_withdraw')->where($map)->find();
		if(!is_array($vo)) ajaxmsg('',0);
		///////////////////////////////////////////////
		$field = "(mm.account_money+mm.back_money) all_money,mm.account_money,mm.back_money";
		$m = M('member_money mm')->field($field)->where("mm.uid={$this->uid}")->find();
		////////////////////////////////////////////////////
		$newid = M('member_withdraw')->where($map)->delete();
		if($newid){
			if(($m['all_money']-$vo['withdraw_money'] - $vo['withdraw_fee'])<0 ){
				$res = memberMoneyLog($this->uid,5,$vo['withdraw_money'],"撤消提现");
			}else{
				$res = memberMoneyLog($this->uid,5,$vo['withdraw_money'],"撤消提现",'0','@网站管理员@',$vo['withdraw_fee']);
			}
		}
		if($res) ajaxmsg();
		else ajaxmsg("",0);
	}
    public function withdrawlog(){
    	
		if($_GET['start_time']&&$_GET['end_time']){
			$_GET['start_time'] = strtotime($_GET['start_time']." 00:00:00");
			$_GET['end_time'] = strtotime($_GET['end_time']." 23:59:59");
			
			if($_GET['start_time']<$_GET['end_time']){
				$map['add_time']=array("between","{$_GET['start_time']},{$_GET['end_time']}");
				$search['start_time'] = $_GET['start_time'];
				$search['end_time'] = $_GET['end_time'];
			}
		}
		$map['uid'] = $this->uid;
		$list = getWithDrawLog($map,15);
		$this->assign('search',$search);
		$this->assign("list",$list['list']);
		$this->assign("pagebar",$list['page']);
		$model=M('member_banks');
		$mbank = $model->where('uid = '.$this->uid)->find();
		$this->assign('mbank',$mbank);
		$this->display();
    }
}
