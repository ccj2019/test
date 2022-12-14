<?php
/**
 * 理财产品	前台主控制器
 */
class ProductAction extends HCommonAction {
    public function index(){	
		$strUrl = "/product".'?1=1';
		static $newpars;
		$Bconfig = require C("APP_ROOT")."Conf/borrow_config.php";
		$per = C('DB_PREFIX');


		$curl = $_SERVER['REQUEST_URI'];

		$urlarr = parse_url($curl);
		parse_str($urlarr['query'],$surl);//array获取当前链接参数，2.
		
		$urlArr = array('borrow_money','borrow_interest_rate','borrow_name','borrow_status','repayment_type','borrow_use','money_from','money_to','borrow_duration','is_reward','province','city','area','borrow_type');
		$maprow = array();
		$searchMap =  array();

		foreach($urlArr as $vs){
			$maprow[$vs] = text($surl[$vs]);
		}

		//searchMap
		if(in_array($maprow['borrow_status'],array(2,3,4,6,7,8,9))){
			if($maprow['borrow_status']==9){
				$searchMap['borrow_status']=array("in",'2,4,6,7');
			}else{
				$searchMap['borrow_status']=$maprow['borrow_status'];
			}
			$strUrl .= '&borrow_status='.$maprow['borrow_status'];
		}else{
			$searchMap['borrow_status']=array("in",'2');
		}
		if(in_array($maprow['borrow_type'],array(1,2,3,4,5))){
			$searchMap['borrow_type']=$maprow['borrow_type'];
			$strUrl .= '&borrow_type='.$maprow['borrow_type'];
			$this->assign("borrow_type", $maprow['borrow_type']);
		}else{
			$this->assign("borrow_type", 0);
		}

		
		//$searchMap['borrow_type']=$maprow['borrow_type'];
		//echo $searchMap['borrow_type'];
		$this->assign("borrow_status", $maprow['borrow_status']);
		
		if(!empty($maprow['borrow_name'])) $searchMap['b.borrow_name'] = array("like","%{$maprow['borrow_name']}%");
		if(!empty($maprow['repayment_type'])) $searchMap['b.repayment_type'] =intval($maprow['repayment_type']);
		if(!empty($maprow['borrow_use'])) $searchMap['b.borrow_use'] =intval($maprow['borrow_use']);
		if($maprow['money_from'] < $maprow['money_to']){
			$bt = intval($maprow['money_from']).",".intval($maprow['money_to']);
			$searchMap['b.borrow_money'] =array("between","{$bt}");
		}
		if(!empty($maprow['borrow_duration'])){
			 if($maprow['borrow_duration']==1){
			 	$searchMap['b.borrow_duration'] =array("in",'1,2,3');
			 }elseif($maprow['borrow_duration']==2){
				$searchMap['b.borrow_duration'] =array("in",'4,5,6');	 
			 }elseif($maprow['borrow_duration']==3){
				$searchMap['b.borrow_duration'] =array("in",'7,8,9');	 
			}elseif($maprow['borrow_duration']==4){
				$searchMap['b.borrow_duration'] =array("in",'10,11,12');	 
			}elseif($maprow['borrow_duration']==5){
				$searchMap['b.borrow_duration'] =array("in",'13,14,15,16,17,18,19,20,21,22,23,24');	 
			}elseif($maprow['borrow_duration']==6){
				$searchMap['b.borrow_duration'] =array("gt",'24');	 
			 }
			 if(in_array($maprow['borrow_duration'],array(1,2,3,4))){
			 	$searchMap['b.repayment_type']=array("neq",'1');
			 }
			 $strUrl .= '&borrow_duration='.$maprow['borrow_duration'];
		}else{
			$maprow['borrow_duration']=0;
		}
		if(!empty($maprow['borrow_interest_rate'])){					
			 if($maprow['borrow_interest_rate']==1){
			 	$searchMap['b.borrow_interest_rate'] = array('between',array('0','10'));
			 }elseif($maprow['borrow_interest_rate']==2){
				$searchMap['b.borrow_interest_rate'] = array('between',array('11','20'));
			 }elseif($maprow['borrow_interest_rate']==3){
				$searchMap['b.borrow_interest_rate'] = array('between',array('21','100'));
			 }
			 $strUrl .= '&borrow_interest_rate='.$maprow['borrow_interest_rate'];
		}else{
			$maprow['borrow_interest_rate']=0;
		}
		if(!empty($maprow['borrow_money'])){					
			 if($maprow['borrow_money']==1){
			 	$searchMap['b.borrow_money'] = array('between',array('0','10000'));
			 }elseif($maprow['borrow_money']==2){
				$searchMap['b.borrow_money'] = array('between',array('10001','50000'));
			 }elseif($maprow['borrow_money']==3){
				$searchMap['b.borrow_money'] = array('gt','50000');
			 }
			 $strUrl .= '&borrow_money='.$maprow['borrow_money'];
		}else{
			$maprow['borrow_money']=0;
		}

		$this->assign("borrow_interest_rate", $maprow['borrow_interest_rate']);		
		$this->assign("borrow_money", $maprow['borrow_money']);		

		$this->assign("borrow_duration", $maprow['borrow_duration']);
		if(!empty($maprow['is_reward'])) $searchMap['b.is_reward'] =intval($maprow['is_reward']);
		if(!empty($maprow['province'])) $searchMap['b.province'] =intval($maprow['province']);
		if(!empty($maprow['city'])) $searchMap['b.city'] =intval($maprow['city']);
		if(!empty($maprow['area'])) $searchMap['b.area'] =intval($maprow['area']);
		//searchMap
		//if(is_array($searchMap['borrow_status'])) $searchMap['collect_time']=array('gt',time());
		if(empty($maprow['borrow_status'])){
			$searchMap['borrow_status']=array("in",'2,4,6,7');
		}
		$parm['map'] = $searchMap;
		$parm['pagesize'] = 8;
		//排序
		(strtolower($_GET['sort'])=="asc")?$sort="desc":$sort="asc";
		unset($surl['orderby'],$surl['sort']);
		$orderUrl = http_build_query($surl);
		if($_GET['orderby']){
			if(strtolower($_GET['orderby'])=="credits") $parm['orderby'] = "m.credits ".text($_GET['sort']);
			elseif(strtolower($_GET['orderby'])=="rate") $parm['orderby'] = "b.borrow_interest_rate ".text($_GET['sort']);
			elseif(strtolower($_GET['orderby'])=="borrow_money") $parm['orderby'] = "b.borrow_money ".text($_GET['sort']);
			else $parm['orderby']="b.id DESC";
		}else{
			$parm['orderby']="b.borrow_status ASC,b.id DESC";
		}
		$Sorder['Corderby'] = strtolower(text($_GET['orderby']));
		$Sorder['Csort'] = strtolower(text($_GET['sort']));
		$Sorder['url'] = $orderUrl;
		$Sorder['sort'] = $sort;
		$Sorder['orderby'] = text($_GET['orderby']);
		//排序
		
		$list = getProductList($parm);
		
		$this->assign("Sorder",$Sorder);
		$this->assign("searchMap",$maprow);
		$this->assign("Bconfig",$Bconfig);
		$this->assign("list",$list);


		// 往期理财服务
		/*$parm = array(	'map'=>array(
								'borrow_status'=>array("in",'7')
								),
						'pagesize'=>10,
						'orderby'=>'b.borrow_status ASC,b.id DESC'
		);
		$tpl_var['list_done'] = getProductList($parm);*/
		$tpl_var['strUrl'] = $strUrl;
		$this->assign($tpl_var);		
		$glo = array('web_title'=>'理财专区');
    	$this->assign($glo);
		$this->display();
    }
	/////////////////////////////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////////////////
	private function _tendlist($list){
		$areaList = getArea();
		$row=array();
		foreach($list as $key=>$v){
			$v['location'] = $areaList[$v['province']].$areaList[$v['city']].$areaList[$v['area']];
			$v['breakday'] = getExpiredDays($v['deadline']);
			$v['expired_money'] = getExpiredMoney($v['breakday'],$v['total_expired'],$v['interest']);
			$v['call_fee'] = getExpiredCallFee($v['breakday'],$v['total_expired'],$v['interest']);
			$row[$key]=$v;
		}
		return $row;
	}
	
	////////////////////////////////////////////////////////////////////////////////////
    public function detail(){
		if($_GET['type']=='commentlist'){
			//评论
			$cmap['tid'] = intval($_GET['id']);
			$clist = getCommentList($cmap,5);
			$this->assign("commentlist",$clist['list']);
			$this->assign("commentpagebar",$clist['page']);
			$this->assign("commentcount",$clist['count']);
			$data['html'] = $this->fetch('commentlist');
			exit(json_encode($data));
		}




		$pre = C('DB_PREFIX');
		$id = intval($_GET['id']);
		$Bconfig = require C("APP_ROOT")."Conf/borrow_config.php";
		$this->assign("Bconfig",$Bconfig);
		
		//合同ID
		if($this->uid){
			$invs = M('pro_borrow_info')->field('id')->where("borrow_id={$id} AND (investor_uid={$this->uid} OR borrow_uid={$this->uid})")->find();
			if($invs['id']>0) $invsx=$invs['id'];
			elseif(!is_array($invs)) $invsx='no';
		}else{
			$invsx='login';
		}
		$this->assign("invid",$invsx);
		//合同ID
		//borrowinfo
		$borrowinfo = M("pro_borrow_info")->field(true)->find($id);
		
		//点击
		$ip=get_client_ip();
		$now_time=strtotime(date('Y-m-d'));
		$hit_array = M("pro_hits")->field(true)->where('add_ip="'.$ip.'" and bid='.$id." and add_time>".$now_time)->find();
		if(!$hit_array){
			$save['bid'] = $id;
			$save['add_time'] =time();
			$save['add_ip'] =$ip;
			M("pro_hits")->data($save)->add();
			M("pro_borrow_info")->where('id='.$id)->setField('hits',$borrowinfo["hits"]+1);
		}
		
		
		
		
		if(bcsub($borrowinfo['borrow_money'],$borrowinfo['has_borrow'],2)==0 and $borrowinfo["borrow_status"]==2){
			//echo bcsub($borrowinfo['borrow_money'],$borrowinfo['has_borrow'],2);
			borrowfull_pro($id,$borrowinfo["borrow_type"]);	
		}
		
		//echo M("borrow_info")->LastSql();
		//print_r($borrowinfo);
		if(!is_array($borrowinfo) || ($borrowinfo['borrow_status']==0 && $this->uid!=$borrowinfo['borrow_uid']) ) $this->error("数据有误");
		$borrowinfo['biao'] = $borrowinfo['borrow_times'];
		$borrowinfo['need'] = bcsub($borrowinfo['borrow_money'], $borrowinfo['has_borrow'],2);

		$borrowinfo['need_number'] = intval($borrowinfo['need']/$borrowinfo['each_money']);

		$borrowinfo['lefttime'] =$borrowinfo['collect_time'] - time();
		$borrowinfo['progress'] = getFloatValue($borrowinfo['has_borrow']/$borrowinfo['borrow_money']*100,2);
		$borrowinfo['vouch_progress'] = getFloatValue($borrowinfo['has_vouch']/$borrowinfo['borrow_money']*100,2);
		$borrowinfo['vouch_need'] = $borrowinfo['borrow_money'] - $borrowinfo['has_vouch'];
		$borrowinfo["borrow_img"]=str_replace("'","",$borrowinfo["borrow_img"]);
		if($borrowinfo['borrow_img']=="")$borrowinfo['borrow_img']="UF/Uploads/borrowimg/nopic.png";

		$borrow_interest_rate_arr = explode('.', $borrowinfo['borrow_interest_rate'],2);
		$borrowinfo['borrow_interest_rate_1'] = '00';
		$borrowinfo['borrow_interest_rate_2'] = '00';
		if(isset($borrow_interest_rate_arr[0])) $borrowinfo['borrow_interest_rate_1'] = $borrow_interest_rate_arr[0];
		if(isset($borrow_interest_rate_arr[1])) $borrowinfo['borrow_interest_rate_2'] = $borrow_interest_rate_arr[1];

		
		$this->assign("binfo",$borrowinfo);
		//borrowinfo
		//此标借款利息还款相关情况
		if($borrowinfo['repayment_type']==2){
			$money = 100;
			$rate = $borrowinfo['borrow_interest_rate'];
			$month = $borrowinfo['borrow_duration'];
			
			$monthData['money'] = $money;
			$monthData['year_apr'] = $rate;
			$monthData['duration'] = $month;
			$monthData['type'] = "all";
			$repay_detail = EqualMonth($monthData);
			$this->assign('repay_detail',$repay_detail);
		}elseif($borrowinfo['repayment_type']==3){
			$money = 100;
			$rate = $borrowinfo['borrow_interest_rate'];
			$month = $borrowinfo['borrow_duration'];
			
			$monthData['account'] = $money;
			$monthData['year_apr'] = $rate;
			$monthData['month_times'] = $month;
			$monthData['type'] = "all";
			$repay_detail = EqualSeason($monthData);
			$this->assign('repay_detail',$repay_detail);
		}elseif($borrowinfo['repayment_type'] == 4){
			$money = 100;
			$rate = $borrowinfo['borrow_interest_rate'];
			$month = $borrowinfo['borrow_duration'];
			$parm['month_times'] = $month;
			$parm['account'] = $money;
			$parm['year_apr'] = $rate;
			$parm['type'] = "all";
			$repay_detail = EqualEndMonth($parm);
			$repay_detail['repayment_money'] = $repay_detail['repayment_account'];
			$this->assign( "repay_detail", $repay_detail );
		}elseif($borrowinfo['repayment_type']==1){
			$repay_detail['repayment_money'] = getFloatValue(100+100*$borrowinfo['borrow_interest_rate']*$borrowinfo['borrow_duration']/100,2);
			$this->assign('repay_detail',$repay_detail);
		}
		//此标借款利息还款相关情况
		//memberinfo
		$memberinfo = M("members m")->field("m.id,m.customer_name,m.customer_id,m.user_name,m.reg_time,m.credits,fi.*,mi.*")->join("{$pre}member_financial_info fi ON fi.uid = m.id")->join("{$pre}member_info mi ON mi.uid = m.id")->where("m.id={$borrowinfo['borrow_uid']}")->find();
		$areaList = getArea();
		$memberinfo['location'] = $areaList[$memberinfo['province']].$areaList[$memberinfo['city']];
		$memberinfo['location_now'] = $areaList[$memberinfo['province_now']].$areaList[$memberinfo['city_now']];
		$this->assign("minfo",$memberinfo);
		//memberinfo
		//vouch_list
		$vouch_list = M("borrow_vouch")->field(true)->where("borrow_id={$id}")->select();
		$this->assign("vouch_list",$vouch_list);
		$this->assign("Vstatus",array(0=>'担保中',1=>"担保完成"));
		//vouch_list
		
		//data_list
		$data_list = M("member_data_info")->field('type,add_time,data_url,count(status) as num,sum(deal_credits) as credits')->where("uid={$borrowinfo['borrow_uid']} AND status=1")->group('id')->select();
		$this->assign("data_list",$data_list);
		//data_list
		
		//paying_list
		//$paying_list = getMemberBorrow($borrowinfo['borrow_uid']);
		$paying_list = getMemberThisBorrow($borrowinfo['borrow_uid'],200,$id); //当前标的情况
		$this->assign("paying_list",$paying_list);
		//paying_list

		//近期还款的投标
		//$time1 = microtime(true)*1000;
		$history = getDurationCount($borrowinfo['borrow_uid']);
		$this->assign("history",$history);
		//$time2 = microtime(true)*1000;
		//echo $time2-$time1;

		//investinfo
		$fieldx = "bi.investor_capital,bi.add_time,m.user_name,bi.is_auto";
		$investinfo = M("pro_borrow_investor bi")->field($fieldx)->join("{$pre}members m ON bi.investor_uid = m.id")->where("bi.borrow_id={$id}")->order("bi.id DESC")->select();
		$this->assign("investinfo",$investinfo);
		//investinfo
		
		//帐户资金情况
		$this->assign("mainfo", getMinfo($borrowinfo['borrow_uid'],true));
		$this->assign("capitalinfo", getMemberBorrowScan($borrowinfo['borrow_uid']));
		//帐户资金情况
		
		$sss=getMemberBorrowScan($borrowinfo['borrow_uid']);
		//print_r($sss);
		
		$this->assign("mx",$sss);
		//echo $sss["borrow"]["4"]["money"];
		//print_r($sss["repayment"]);
		$this->assign("jiekuan",getFloatValue($sss["borrow"]["4"]["money"]+$sss["borrow"]["6"]["money"],2));
		$this->assign("yihuan",getFloatValue($sss["repayment"]["2"]["capital"]+$sss["repayment"]["2"]["interest"],2));
		$this->assign("daihuan",getFloatValue($sss["borrow"]["4"]["money"]+$sss["borrow"]["6"]["money"]+$sss["repayment"]["7"]["interest"],2));
		
		
		$glo = array('web_title'=>$borrowinfo['borrow_name'].' - 理财专区');
    	$this->assign($glo);	
		$this->display();
    }
	
	public function investcheck(){
		$pre = C('DB_PREFIX');
		if(!$this->uid) ajaxmsg('',3);
		$pin = md5($_POST['pin']);
		
		$vm = getMinfo($this->uid);
		$amoney = $vm['account_money']+$vm['back_money'];
		$uname = session('u_user_name');
		$pin_pass = $vm['pin_pass'];		
		$pin = md5($_POST['pin']);		
		if($pin<>$pin_pass) ajaxmsg("支付密码错误，请重试",0);		
		if($money>$amoney){
			$msg = "尊敬的{$uname}，您准备购买{$money}元，但您的账户可用余额为{$amoney}元，您要先去充值吗？";
			ajaxmsg($msg,2);
		}else{
			$msg = "尊敬的{$uname}，您的账户可用余额为{$amoney}元，您确认购买吗？";
			ajaxmsg($msg,1);
		}
	}
		
	public function investmoney(){
		
		if(!$this->uid) exit;
		/****** 防止模拟表单提交 *********/
		$cookieKeyS = cookie(strtolower(MODULE_NAME)."-invest");
		//echo cookie(strtolower(MODULE_NAME)."-invest");
		//echo $_REQUEST['cookieKey'];
		//exit;
		if($cookieKeyS!=$_REQUEST['cookieKey']){
			//$this->error("数据校验有误");
		}
		/****** 防止模拟表单提交 *********/
		$invest_number = intval($_POST['invest_number']);
		$borrow_id = intval($_POST['borrow_id']);
		$vo = M('pro_borrow_info')->find($borrow_id);
		if($vo['invest_method']==1){	
			$money = $invest_number*$vo['each_money'];
		}else{
			$money = getFloatValue($_POST['invest_money'],2);	
		}	
		

		$m = M("member_money")->field('account_money,back_money')->find($this->uid);
		$amoney = $m['account_money']+$m['back_money'];
		$uname = session('u_user_name');
		if($amoney<$money) $this->error("尊敬的{$uname}，您准备投标{$money}元，但您的账户可用余额为{$amoney}元，请先去充值再投标.");
		
		$vm = getMinfo($this->uid);
		$pin_pass = $vm['pin_pass'];
		$pin = md5($_POST['pin']);
		if($pin<>$pin_pass) $this->error("支付密码错误，请重试");  //   暂时取消支付密码



		$result = $this->investverify($borrow_id,$_POST);
		$done = false;
		if(!empty($result['msg'])){
			$this->error($result['msg']);
		}else{
			$done = investMoney_pro($this->uid,$borrow_id,$money);
		}
		if($done===true) {
			$this->success("恭喜成功投标{$money}元");
		}else if($done){
			$this->error($done);
		}else{
			$this->error("对不起，投标失败，请重试!");
		}
	}

	
	public function ajax_invest(){
		if(!$this->uid) {
			ajaxmsg("请先登陆",0);
		}
		
		/****** 防止模拟表单提交 *********/
		$cookieTime = 15*3600;
		$cookieKey=md5(MODULE_NAME."-".time());
		cookie(strtolower(MODULE_NAME)."-invest",$cookieKey,$cookieTime);
		$this->assign("cookieKey",$cookieKey);
		/****** 防止模拟表单提交 *********/
		$pre = C('DB_PREFIX');
		$id=intval($_GET['id']);

		$result = $this->investverify($id,$_GET);	
		if(!empty($result['msg'])) ajaxmsg($result['msg'],$result['status']);

		$vo = M('pro_borrow_info')->find($id);
		$vo['need_number'] = intval($vo['need']/$vo['each_money']);

		$vo['invest_number'] = isset($_GET['invest_number']) ? $_GET['invest_number'] : 0;
		$vo['invest_money']  = isset($_GET['invest_money']) ? $_GET['invest_money'] : 0;


		$vm = getMinfo($this->uid);
		$pin_pass = $vm['pin_pass'];
		$has_pin = (empty($pin_pass))?"no":"yes";
		$this->assign("has_pin",$has_pin);
		$this->assign("vo",$vo);
		
		$data['content'] = $this->fetch();

		ajaxmsg($data);
	}
	
	private function investverify($borrow_id,$data){
		$Bconfig = require C("APP_ROOT")."Conf/borrow_config.php";		
		$vo = M('pro_borrow_info')->find($borrow_id);

		$invest_number = isset($data['invest_number']) ? $data['invest_number'] : 0;
		$invest_money  = isset($data['invest_money']) ? $data['invest_money'] : 0;



		$ids = M('members_status')->getFieldByUid($this->uid,'id_status');
		$result = array('msg'=>'','status'=>0);
		if($ids!=1){
			$result['msg'] = '请先通过实名认证后在进行投标。';			
		}
		$phones = M('members_status')->getFieldByUid($this->uid,'phone_status');
		if($phones!=1){
			$result['msg'] = '请先通过手机认证后在进行投标。';			
		}
		$emails = M('members_status')->getFieldByUid($this->uid,'email_status');
		if($emails!=1){
			$result['msg'] = '请先通过邮箱认证后在进行投标。';					
		}
		if($vo['pause']==1){
			$result['msg'] = '此标当前已经截标，请等待管理员开启。';			
		}
		
		if($this->uid == $vo['borrow_uid']) $result['msg'] = '不能去购买自己的项目';
		if($vo['pause']==1)	$result['msg'] = "此项目当前已经截标，请等待管理员开启。";		
		if($vo['borrow_status'] <> 2) $result['msg'] = "只能购买正在认购的项目！";		

		$vo['need'] = bcsub($vo['borrow_money'],$vo['has_borrow'],2);
		if($vo['need']<0) $result['msg'] = "投标金额不能超出借款剩余金额";

		$capital = M('pro_borrow_investor')->where("borrow_id={$borrow_id} AND investor_uid={$this->uid}")->sum('investor_capital');

		$capital = empty($capital) ? 0 : $capital;

		//按份
		if($vo['invest_method']==1){			
			$has_invest_number = $capital/$vo['each_money'];
			if($has_invest_number>=$vo['each_number']&&$vo['each_number']>0) {
				$result['msg'] = "您已购买{$has_invest_number}份,不可再次购买该产品，请选择其他理财产品。";
			}
			$can_invest_money = bcsub($vo['borrow_money'],$vo['has_borrow'],2);

			$can_invest_number = $can_invest_money/$vo['each_number'];
			if($invest_number > $can_invest_number and $can_invest_number<>""){
				$result['msg'] = "你最多只能购买{$can_invest_number}份！";	
			}
		}else{

			if($invest_money<$vo['borrow_min']){
				$result['msg'] = "此标最小投标金额为".$vo['borrow_min']."元";return $result;
			}
			if($invest_money>$vo['borrow_max'] and $vo['borrow_max']!=0){
				$result['msg'] = "此标最大投标金额为".$vo['borrow_max']."元";return $result;
			}
			if(($capital+$invest_money)>$vo['borrow_max']&&$vo['borrow_max']>0){
				$xtee = $vo['borrow_max'] - $capital;
				($xtee<0) ? $xtee=0 : '';
				$result['msg'] = "您已投标{$capital}元，此投上限为{$vo['borrow_max']}元，你最多只能再投{$xtee}";
				return $result;
			}
			$need = bcsub($vo['borrow_money'],$vo['has_borrow'],2);
			$caninvest =bcsub($need ,$vo['borrow_min'],2);		
			if( $invest_money>$caninvest && ($need-$invest_money)<>0 ){
				
				if(intval($need)==0 or $need=="0.00"){
					$result['msg'] = "尊敬的{$uname}，此标已经投满";
				}
				if($invest_money>$need){
					$result['msg'] = "尊敬的{$uname}，此标还差{$need}元满标,您最多只能再投{$need}元";
				}
				$result['msg'] =  "尊敬的{$uname}，此标还差{$need}元满标,如果您投标{$invest_money}元，将导致最后一次投标最多只能投".(bcsub($need,$invest_money,2))."元，小于最小投标金额{$vo['borrow_min']}元，所以您本次可以选择<font color='#FF0000'>满标</font>或者投标金额必须<font color='#FF0000'>小于等于{$caninvest}元</font>";
				if($caninvest<$vo['borrow_min']) $result['msg'] = "尊敬的{$uname}，此标还差{$need}元满标,如果您投标{$invest_money}元，将导致最后一次投标最多只能投".(bcsub($need,$invest_money,2))."元，小于最小投标金额{$vo['borrow_min']}元，所以您本次可以选择<font color='#FF0000'>满标</font>即投标金额必须<font color='#FF0000'>等于{$need}元</font>";
				
			}
			if(($need-$invest_money)<0 ){
				$result['msg'] = "尊敬的{$uname}，此标还差{$need}元满标,您最多只能再投{$need}元";
			}
		}		
		return $result;
	}
	
	public function getarea(){
		$rid = intval($_GET['rid']);
		if(empty($rid)){
			$data['NoCity'] = 1;
			exit(json_encode($data));
		}
		$map['reid'] = $rid;
		$alist = M('area')->field('id,name')->order('sort_order DESC')->where($map)->select();

		if(count($alist)===0){
			$str="<option value=''>--该地区下无下级地区--</option>\r\n";
		}else{
			if($rid==1) $str.="<option value='0'>请选择省份</option>\r\n";
			foreach($alist as $v){
				$str.="<option value='{$v['id']}'>{$v['name']}</option>\r\n";
			}
		}
		$data['option'] = $str;
		$res = json_encode($data);
		echo $res;
	}	
	
	public function addfriend(){
		if(!$this->uid) ajaxmsg("请先登陆",0);
		$fuid = intval($_POST['fuid']);
		$type = intval($_POST['type']);
		if(!$fuid||!$type) ajaxmsg("提交的数据有误",0);
		
		$save['uid'] = $this->uid;
		$save['friend_id'] = $fuid;
		$vo = M('member_friend')->where($save)->find();	
		
		if($type==1){//加好友
		if($this->uid == $fuid) ajaxmsg("您不能对自己进行好友相关的操作",0);
			if(is_array($vo)){
				if($vo['apply_status']==3){
					$msg="已经从黑名单移至好友列表";
					$newid = M('member_friend')->where($save)->setField("apply_status",1);
				}elseif($vo['apply_status']==1){
					$msg="已经在你的好友名单里，不用再次添加";
				}elseif($vo['apply_status']==0){
					$msg="已经提交加好友申请，不用再次添加";
				}elseif($vo['apply_status']==2){
					$msg="好友申请提交成功";
					$newid = M('member_friend')->where($save)->setField("apply_status",0);
				}
			}else{
				$save['uid'] = $this->uid;
				$save['friend_id'] = $fuid;
				$save['apply_status'] = 0;
				$save['add_time'] = time();
				$newid = M('member_friend')->add($save);	
				$msg="好友申请成功";
			}
		}elseif($type==2){//加黑名单
		if($this->uid == $fuid) ajaxmsg("您不能对自己进行黑名单相关的操作",0);
			if(is_array($vo)){
				if($vo['apply_status']==3) $msg="已经在黑名单里了，不用再次添加";
				else{
					$msg="成功移至黑名单";
					$newid = M('member_friend')->where($save)->setField("apply_status",3);	
				}
			}else{
				$save['uid'] = $this->uid;
				$save['friend_id'] = $fuid;
				$save['apply_status'] = 3;
				$save['add_time'] = time();
				$newid = M('member_friend')->add($save);	
				$msg="成功加入黑名单";
			}
		}
		if($newid) ajaxmsg($msg);
		else ajaxmsg($msg,0);
	}
	
	
	public function innermsg(){
		if(!$this->uid) ajaxmsg("请先登陆",0);
		$fuid = intval($_GET['uid']);
		if($this->uid == $fuid) ajaxmsg("您不能对自己进行发送站内信的操作",0);
		$this->assign("touid",$fuid);
		$data['content'] = $this->fetch("Public:innermsg");
		ajaxmsg($data);
	}
	public function doinnermsg(){
		$touid = intval($_POST['to']);
		$msg = text($_POST['msg']);	
		$title = text($_POST['title']);	
		$newid = addMsg($this->uid,$touid,$title,$msg);
		if($newid) ajaxmsg();
		else ajaxmsg("发送失败",0);
		
	}
	
	public function view(){
		$id = intval($_GET['id']);
		if($_GET['type']=="subsite") $vo = M('article_area')->find($id);
		else $vo = M('article')->find($id);
	
		$this->assign("vo",$vo);
		
		//left
		$typeid = $vo['type_id'];
		$listparm['type_id']=$typeid;
		$listparm['limit']=20;
		if($_GET['type']=="subsite"){
			$listparm['area_id'] = $this->siteInfo['id'];
			$leftlist = getAreaTypeList($listparm);
		}else	$leftlist = getTypeList($listparm);
		
		$this->assign("leftlist",$leftlist);
		$this->assign("cid",$typeid);
		
		if($_GET['type']=="subsite"){
			$vop = D('Aacategory')->field('type_name,parent_id')->find($typeid);
			if($vop['parent_id']<>0) $this->assign('cname',D('Aacategory')->getFieldById($vop['parent_id'],'type_name'));
			else $this->assign('cname',$vop['type_name']);
		}else{
			$vop = D('Acategory')->field('type_name,parent_id')->find($typeid);
			if($vop['parent_id']<>0) $this->assign('cname',D('Acategory')->getFieldById($vop['parent_id'],'type_name'));
			else $this->assign('cname',$vop['type_name']);
		}
		
		$this->display();
	}
	

}