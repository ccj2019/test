<?php
// 全局设置
class MemberimportAction extends ACommonAction
{
	  /**
    +----------------------------------------------------------
    * 默认操作
    +----------------------------------------------------------
    */
    public function index()
    {
		
		 
		$map=array();
		if($_REQUEST['uname']){
			$map['m.user_name'] = array("like",urldecode($_REQUEST['uname'])."%");
			$search['uname'] = urldecode($_REQUEST['uname']);	
		}
		if($_REQUEST['realname']){
			$map['mi.real_name'] = urldecode($_REQUEST['realname']);
			$search['realname'] = $map['mi.real_name'];	
		}
		if($_REQUEST['is_vip']=='yes'){
			$map['m.user_leve'] = 1;
			$map['m.time_limit'] = array('gt',time());
			$search['is_vip'] = 'yes';	
		}elseif($_REQUEST['is_vip']=='no'){
			$map['_string'] = 'm.user_leve=0 ';
			$search['is_vip'] = 'no';	
		}
		if($_REQUEST['is_transfer']=='yes'){
			$map['m.is_transfer'] = 1;
		}elseif($_REQUEST['is_transfer']=='no'){
			$map['m.is_transfer'] = 0;
		}
		
		if(session('admin_is_kf')==1){
				$map['m.customer_id'] = session('admin_id');
		}else{
			if($_REQUEST['customer_name']){
				$map['m.customer_id'] = $_REQUEST['customer_id'];
				$search['customer_id'] = $map['m.customer_id'];	
				$search['customer_name'] = urldecode($_REQUEST['customer_name']);	
			}
			
			if($_REQUEST['customer_name']){
				$cusname = urldecode($_REQUEST['customer_name']);
				$kfid = M('ausers')->getFieldByUserName($cusname,'id');
				$map['m.customer_id'] = $kfid;
				$search['customer_name'] = $cusname;	
				$search['customer_id'] = $kfid;	
			}
		}
		if(!empty($_REQUEST['bj']) && !empty($_REQUEST['lx']) && !empty($_REQUEST['money'])){
			$map[$_REQUEST['lx']] = array($_REQUEST['bj'],$_REQUEST['money']);
			$search['bj'] = $_REQUEST['bj'];	
			$search['lx'] = $_REQUEST['lx'];	
			$search['money'] = $_REQUEST['money'];	
		}

		if(!empty($_REQUEST['start_time']) && !empty($_REQUEST['end_time'])){
			$timespan = strtotime(urldecode($_REQUEST['start_time'])).",".strtotime(urldecode($_REQUEST['end_time']));
			$map['m.reg_time'] = array("between",$timespan);
			$search['start_time'] = urldecode($_REQUEST['start_time']);	
			$search['end_time'] = urldecode($_REQUEST['end_time']);	
		}elseif(!empty($_REQUEST['start_time'])){
			$xtime = strtotime(urldecode($_REQUEST['start_time']));
			$map['m.reg_time'] = array("gt",$xtime);
			$search['start_time'] = $xtime;	
		}elseif(!empty($_REQUEST['end_time'])){
			$xtime = strtotime(urldecode($_REQUEST['end_time']));
			$map['m.reg_time'] = array("lt",$xtime);
			$search['end_time'] = $xtime;	
		}

		//分页处理
		import("ORG.Util.Page");
		$count = M('members m')->where($map)->count('m.id');
		$p = new Page($count, C('ADMIN_PAGE_SIZE'));
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		//分页处理
		
		$field= 'm.id,m.user_phone,m.reg_time,m.reg_ip,m.credits,m.user_name,m.customer_name,m.user_leve,m.time_limit,m.user_email,m.user_leve';
		$list = M('members m')->field($field)->where($map)->limit($Lsql)->order('m.id DESC')->select();
		
		$list=$this->_listFilter($list);
		foreach($list as $key=>$val){
			$vx=array();
			$vx = M('member_info')->where("uid=".$val["id"])->find();
			$val["real_name"]=$vx["real_name"];
			
			$vx=array();
			$vx = M('member_money')->where("uid=".$val["id"])->find();
			$vxx=array();
			$vxx = M('member_login')->where("uid=".$val["id"])->order("id desc")->find();
			$val["last_time"]=$vxx["add_time"];
			$val["last_ip"]=$vxx["ip"];
			$val["account_money"]=$vx["account_money"]+$vx["back_money"];  //会员可用余额
			$val["money_freeze"]=$vx["money_freeze"];   //会员冻结金额

			$val["yubi_freeze"]=$vx["yubi_freeze"];   //会员冻结鱼币
			$val["yubi"]=$vx["yubi"];   //鱼币

			$val["money_collect"]=$vx["money_collect"];//会员待收金额
			$money=getMemberMoneySummary($vx["id"]);
			
			$val["idcard"]=$vx["idcard"];//会员身份号码
			$val["cell_phone"]=$vx["cell_phone"];//会员手机号码
			$val["user_email"]=$vx["user_email"];//会员邮箱
			$val["credits"]=$vx["credits"];//会员积分
			$val["member_login"]= M('member_login')->where("uid=".$val["id"])->count();;//登录次数
			$val["money_off"]=M('member_payonline')->where("way='off' and status=1 and uid=".$val["id"])->sum('money');//线下充值金额
			$val["money_online"]=M('member_payonline')->where("way<>'off' and status=1 and uid=".$val["id"])->sum('money');//在线充值金额
			$val["money_allmoney"]=M('member_payonline')->where("status=1 and uid=".$val["id"])->sum('money');//网站充值总额
			$val["money_off_jl"]=M('member_moneylog')->where("type=32 and uid=".$val["id"])->sum('affect_money');;//线下充值奖励
			
			$val["withdraw_money"]=M('member_withdraw')->where("withdraw_status=2 and uid=".$val["id"])->sum('withdraw_money');//提现总额
			
			$val["withdraw_djmoney"]=M('member_withdraw')->where("withdraw_status=0  and uid=".$val["id"])->sum('withdraw_money');//提现冻结金额
			
			$val["withdraw_sxfmoney"]=M('member_withdraw')->where("withdraw_status=2 and uid=".$val["id"])->sum('second_fee');//提现手续费
			
			$val["xy_money"]=$vx["credit_cuse"];//信用额度
			


			$lists[]=$val;
		}
		
        $this->assign("bj", array("gt"=>'大于',"eq"=>'等于',"lt"=>'小于'));
        $this->assign("lx", array("mm.account_money"=>'充值可用余额',"mm.back_money"=>'回款可用余额',"mm.money_freeze"=>'冻结金额',"mm.money_collect"=>'待收金额'));
        $this->assign("list", $lists);
        $this->assign("pagebar", $page);
        $this->assign("search", $search);
        $this->assign("query", http_build_query($search));
        $this->display();
    }

	
	
	public function export(){
		import("ORG.Io.Excel");
ini_set("max_execution_time", "120000");
		$map=array();
		if($_REQUEST['uname']){
			$map['m.user_name'] = array("like",urldecode($_REQUEST['uname'])."%");
			$search['uname'] = urldecode($_REQUEST['uname']);	
		}
		if($_REQUEST['realname']){
			$map['mi.real_name'] = urldecode($_REQUEST['realname']);
			$search['realname'] = $map['mi.real_name'];	
		}
		if($_REQUEST['is_vip']=='yes'){
			$map['m.user_leve'] = 1;
			//$map['m.time_limit'] = array('gt',time());
			$search['is_vip'] = 'yes';	
		}elseif($_REQUEST['is_vip']=='no'){
			$map['_string'] = 'm.user_leve=0 ';
			$search['is_vip'] = 'no';	
		}
		if($_REQUEST['is_transfer']=='yes'){
			$map['m.is_transfer'] = 1;
		}elseif($_REQUEST['is_transfer']=='no'){
			$map['m.is_transfer'] = 0;
		}
		
		if(session('admin_is_kf')==1){
				$map['m.customer_id'] = session('admin_id');
		}else{
			if($_REQUEST['customer_name']){
				$map['m.customer_id'] = $_REQUEST['customer_id'];
				$search['customer_id'] = $map['m.customer_id'];	
				$search['customer_name'] = urldecode($_REQUEST['customer_name']);	
			}
			
			if($_REQUEST['customer_name']){
				$cusname = urldecode($_REQUEST['customer_name']);
				$kfid = M('ausers')->getFieldByUserName($cusname,'id');
				$map['m.customer_id'] = $kfid;
				$search['customer_name'] = $cusname;	
				$search['customer_id'] = $kfid;	
			}
		}
		if(!empty($_REQUEST['bj']) && !empty($_REQUEST['lx']) && !empty($_REQUEST['money'])){
			$map[$_REQUEST['lx']] = array($_REQUEST['bj'],$_REQUEST['money']);
			$search['bj'] = $_REQUEST['bj'];	
			$search['lx'] = $_REQUEST['lx'];	
			$search['money'] = $_REQUEST['money'];	
		}

		if(!empty($_REQUEST['start_time']) && !empty($_REQUEST['end_time'])){
			$timespan = strtotime(urldecode($_REQUEST['start_time'])).",".strtotime(urldecode($_REQUEST['end_time']));
			$map['m.reg_time'] = array("between",$timespan);
			$search['start_time'] = urldecode($_REQUEST['start_time']);	
			$search['end_time'] = urldecode($_REQUEST['end_time']);	
		}elseif(!empty($_REQUEST['start_time'])){
			$xtime = strtotime(urldecode($_REQUEST['start_time']));
			$map['m.reg_time'] = array("gt",$xtime);
			$search['start_time'] = $xtime;	
		}elseif(!empty($_REQUEST['end_time'])){
			$xtime = strtotime(urldecode($_REQUEST['end_time']));
			$map['m.reg_time'] = array("lt",$xtime);
			$search['end_time'] = $xtime;	
		}
	$field= 'm.id,m.user_phone,m.reg_time,m.credits,m.user_name,m.customer_name,m.user_leve,m.time_limit,mi.real_name,mm.money_freeze,mm.yubi,mm.yubi_freeze,mm.money_collect,(mm.account_money+mm.back_money) account_moneys,m.user_email,mm.account_money,mm.back_money,m.user_leve,mi.idcard,mi.cell_phone';
		$list = M('members m')->field($field)->join("{$this->pre}member_money mm ON mm.uid=m.id")->join("{$this->pre}member_info mi ON mi.uid=m.id")->where($map)->order('m.id DESC')->select();
	//var_dump($list);die;
		
		$row=array();
		$row[0]=array('序号','用户ID','用户名','用户电话','真实姓名','可用余额','冻结金额','可用鱼币','冻结鱼币','待收金额','注册时间','会员身份证', '会员积分','线下充值金额','在线充值金额','网站充值总额' ,'提现总额','提现冻结金额','提现手续费','投资总额','代收总额'/*, 
		'投资总额- 联合养殖标','投资总额-联合销售标','投资总额-共建项目标',' 待收总额- 联合养殖标','待收总额-联合销售标','待收总额-共建项目标' */);
		$i=1;
 

 
	 
 		 
		foreach($list as $v){
		 	$row[$i]['i'] = $i;
				$row[$i]['uid'] = $v['id'];
				$row[$i]['card_num'] = $v['user_name'];
				$row[$i]['card_num2'] = $v['user_phone'];
				$row[$i]['card_pass'] = $v['real_name'];
				/* $row[$i]['card_mianfei'] = getLeveName($v['credits'],2);'会员类型',*/
				$row[$i]['card_second_fee'] = $v['account_money'];
				$row[$i]['card_mianfei1'] =$v['money_freeze'];
				$row[$i]['card_mianfei3'] =$v['yubi'];
				$row[$i]['card_mianfei4'] =$v['yubi_freeze'];
				$row[$i]['card_mianfei2'] = $v["money_collect"];
				$row[$i]['card_timelimit'] = date("Y-m-d",$v['reg_time']); 
				$val["real_name"]=$v["real_name"];
	 
			$vxx=array();
			$vxx = M('member_login')->where("uid=".$v["id"])->order("id desc")->find();
			$val["last_time"]=$vxx["add_time"];
			$val["last_ip"]=$vxx["ip"]; 
			$val["account_money"]=$v["account_money"]+$v["back_money"];  //会员可用余额
			$val["money_freeze"]=$v["money_freeze"];   //会员冻结金额
			$val["money_collect"]=$v["money_collect"];//会员待收金额 
			$val["id"]=$v["id"];
			$row[$i]["idcard"]=$v["idcard"];//会员身份号码 
			$row[$i]["credits"]=$v["credits"];//会员积分
			/*
		 投资总额- 联合养殖标；   投资总额-联合销售标； 投资总额-共建项目标； 待收总额- 联合养殖标；   待收总额-联合销售标； 待收总额-共建项目标；
			*/ 
			$row[$i]["money_off"]=M('member_payonline')->where("way='off' and status=1 and uid=".$val["id"])->sum('money');//线下充值金额
			$row[$i]["money_online"]=M('member_payonline')->where("way<>'off' and status=1 and uid=".$val["id"])->sum('money');//在线充值金额
			$row[$i]["money_allmoney"]=	(int)$row[$i]["money_off"]+(int)$row[$i]["money_online"] ;//网站充值总额 
			$row[$i]["withdraw_money"]=M('member_withdraw')->where("withdraw_status=2 and uid=".$val["id"])->sum('withdraw_money');//提现总额
			$row[$i]["withdraw_djmoney"]=M('member_withdraw')->where("withdraw_status=0  and uid=".$val["id"])->sum('withdraw_money');//提现冻结金额
			$row[$i]["withdraw_sxfmoney"]=M('member_withdraw')->where("withdraw_status=2 and uid=".$val["id"])->sum('second_fee');//提现手续费 
		//	$row[$i]["xy_money"]=$ moeny["credit_cuse"];//信用额度
			
	        $row[$i]["tz_dqhb_dj_money"]=M('investor_detail i') ->where("i.investor_uid=".$val["id"])->sum('i.capital');//投资总额- 联合养殖标； 
	        
	 $row[$i]["tz_dqhb_dj_money"]=	empty($row[$i]["tz_dqhb_dj_money"])?'0.00':$row[$i]["tz_dqhb_dj_money"];
	    /*	$row[$i]["tz_dqhb_dj_money1"]=M('investor_detail i')->join("borrow_info b on i.borrow_id=b.id")->where("  b.pid=1 and i.investor_uid=".$val["id"])->sum('i.capital');//投资总额- 联合养殖标； 
	    	
	       	$row[$i]["tz_dqhb_dj_money2"]=M('investor_detail i')->join("borrow_info b on i.borrow_id=b.id")->where("  b.pid=2 and i.investor_uid=".$val["id"])->sum('i.capital');//投资总额-联合销售标；
	    	
	    	$row[$i]["tz_dqhb_dj_money3"]=M('investor_detail i')->join("borrow_info b on i.borrow_id=b.id")->where("  b.pid=3 and i.investor_uid=".$val["id"])->sum('i.capital');//投资总额-共建项目标；*/
	 	$row[$i]["tz_jz_ds_money"]=M('borrow_investor bi')->join('borrow_info b on b.id = bi.borrow_id')->where(" investor_uid={$uid} and b.borrow_status =6 and bi.status!=3")->sum('investor_capital');//待收总额；
	 $row[$i]["tz_jz_ds_money"]=	empty($row[$i]["tz_jz_ds_money"])?'0.00':$row[$i]["tz_jz_ds_money"];
	    /*	$row[$i]["tz_jz_dhs_money1"]=M('borrow_investor bi')->join('borrow_info b on b.id = bi.borrow_id')->where(" b.pid=1 and investor_uid={$uid} and b.borrow_status =6 and bi.status!=3")->sum('investor_capital');//待收总额- 联合养殖标；
	    	
	    		$row[$i]["tz_jz_dhs_money2"]=M('borrow_investor bi')->join('borrow_info b on b.id = bi.borrow_id')->where(" b.pid=2 and investor_uid={$uid} and b.borrow_status =6 and bi.status!=3")->sum('investor_capital');//待收总额-联合销售标；
	    	
	    	$row[$i]["tz_jz_dhs_money3"]=M('borrow_investor bi')->join('borrow_info b on b.id = bi.borrow_id')->where(" b.pid=3 and investor_uid={$uid} and b.borrow_status =6 and bi.status!=3")->sum('investor_capital');//待收总额-共建项目标； 
	    	
    	  */
			$i++; 
		}	
 
		$xls = new Excel_XML('UTF-8', false, 'datalist');
		$xls->addArray($row);
		$xls->generateXML("datalistcard".date("Y-m-d",time()));
	}

	
    public function viewinfom()
    {	
		$id = intval($_GET['id']);
		$vo = getMemberInfoDetail($id);
		$this->assign("vo",$vo);
        $this->display();
    }

	
	
	public function _listFilter($list){
		$row=array();
		foreach($list as $key=>$v){
			($v['user_leve']==1 && $v['time_limit']>time())?$v['user_type'] = "<span style='color:red'>VIP会员</span>":$v['user_type'] = "普通会员";
			$row[$key]=$v;
		}
		return $row;
	}
	
	public function getusername(){
		$uname = M("members")->getFieldById(intval($_POST['uid']),"user_name");
		if($uname) exit(json_encode(array("uname"=>"<span style='color:green'>".$uname."</span>")));
		else exit(json_encode(array("uname"=>"<span style='color:orange'>不存在此会员</span>")));
	}
	
	 
	///////////////////////////////////	
	
	
}
