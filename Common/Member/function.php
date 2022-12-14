<?php
function getvips(){
	$vminfo = M('members')->field("user_leve,time_limit")->find($_SESSION["u_id"]);
	return $vminfo["user_leve"];
}
function getFriendList($map,$size,$xuid=0){
	//if(empty($map['f.uid'])) return;
	$pre = C('DB_PREFIX');
	
	//分页处理
	import("ORG.Util.Page");
	$count = M('member_friend f')->where($map)->count('f.id');
	$p = new Page($count, $size);
	$page = $p->show();
	$Lsql = "{$p->firstRow},{$p->listRows}";
	//分页处理
	$list = M('member_friend f')->field("f.uid,f.friend_id,f.add_time,m.user_name,m.credits,fm.user_name as funame,fm.credits as fcredits")->join("{$pre}members m ON f.uid = m.id")->join("{$pre}members fm ON f.friend_id = fm.id")->where($map)->limit($Lsql)->select();
	foreach($list as $key=>$v){
		if($map['f.apply_status']==0){
			$list[$key]['user_name'] = $v['user_name'];
			$list[$key]['credits'] = $v['credits'];
		}else{
			$list[$key]['user_name'] = $v['funame'];
			$list[$key]['credits'] = $v['fcredits'];
		}
	}
	
	$row=array();
	$row['list'] = $list;
	$row['page'] = $page;
	return $row;
}
//获取商品,包括分页数据
function getMsgList($parm=array()){
	$M = new Model('member_msg');
	$pre = C('DB_PREFIX');
	$field=true;
	$orderby = " id DESC";
	
	
	if($parm['pagesize']){
		//分页处理
		import("ORG.Util.Page");
		$count = $M->where($parm['map'])->count('id');
		$p = new Page($count, $parm['pagesize']);
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		//分页处理
	}else{
		$page="";
		$Lsql="{$parm['limit']}";
	}
	$data = M('member_msg')->field(true)->where($parm['map'])->order($orderby)->limit($Lsql)->select();
		
	$symbol = C('MONEY_SYMBOL');
	$suffix=C("URL_HTML_SUFFIX");
	foreach($data as $key=>$v){
/*		if($v['bids_type']==1){
			$data[$key]['bids_money'] = 0;
			$data[$key]['bids_free'] = $v['bids'];
		}else{
			$data[$key]['bids_money'] = $v['bids'];
			$data[$key]['bids_free'] = 0;
		}
*/	}
	
	$row=array();
	$row['list'] = $data;
	$row['page'] = $page;
	$row['count'] = $count;
	return $row;
}
//获取特定栏目下文章列表
function getArticleList($parm,$order="DESC"){
	if(empty($parm['type_id'])) return;
	$map['type_id'] = $parm['type_id'];
	$Osql="id DESC";
    	if(strcasecmp($order, "ASC") == 0){
        	$Osql = "id ASC";
    	}
	$field="id,title,art_set,art_time,art_url";
	//查询条件 
	if($parm['pagesize']){
		//分页处理
		import("ORG.Util.Page");
		$count = M('article')->where($map)->count('id');
	
		$p = new Page($count, $parm['pagesize']);
			if($count>1){
			    	$page = $p->show();
	            	$Lsql = "{$p->firstRow},{$p->listRows}";
			}else{
			    	$page = "";
	            	$Lsql = "0,{$parm['pagesize']}";
			}
	
		//分页处理
	}else{
		$page="";
		$Lsql="{$parm['limit']}";
	}
	$data = M('article')->field($field)->where($map)->order($Osql)->limit($Lsql)->select();
	$suffix=C("URL_HTML_SUFFIX");
	$typefix = get_type_leve_nid($map['type_id']);
	$typeu = implode("/",$typefix);
	foreach($data as $key=>$v){
		if($v['art_set']==1) $data[$key]['arturl'] = (stripos($v['art_url'],"http://")===false)?"http://".$v['art_url']:$v['art_url'];
		//elseif(count($typefix)==1) $data[$key]['arturl'] = 
		else $data[$key]['arturl'] = MU("{$typeu}/","article",array("id"=>$v['id'],"suffix"=>$suffix));  //会员中心  文章  ///  特殊/.......
	}
	$row=array();
	$row['list'] = $data;
	$row['page'] = $page;
	return $row;
}
function getWithDrawLog($map,$size,$limit=10){
	if(empty($map['uid'])) return;
	
	if($size){
		//分页处理
		import("ORG.Util.Page");
		$count = M('member_withdraw')->where($map)->count('id');
		$p = new Page($count, $size);
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		//分页处理
	}else{
		$page="";
		$Lsql="{$parm['limit']}";
	}
	
	$status_arr =array('待审核','审核通过,处理中','已提现','审核未通过');
	$list = M('member_withdraw')->where($map)->order('id DESC')->limit($Lsql)->select();
	foreach($list as $key=>$v){
		$list[$key]['status'] = $status_arr[$v['withdraw_status']];
	}
	
	$row=array();
	$row['list'] = $list;
	$row['page'] = $page;
	$map['status'] = 1;
	$row['success_money'] = M('member_payonline')->where($map)->sum('money');
	$map['status'] = array('neq','1');
	$row['fail_money'] = M('member_payonline')->where($map)->sum('money');
	return $row;
}
function getChargeLog($map,$size,$limit=10){
	if(empty($map['uid'])) return;
	
	if($size){
		//分页处理
		import("ORG.Util.Page");
		$count = M('member_payonline')->where($map)->count('id');
		$p = new Page($count, $size);
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		//分页处理
	}else{
		$page="";
		$Lsql="{$parm['limit']}";
	}
	
	$status_arr =array('购买未完成','购买成功','签名不符','购买失败');
	$list = M('member_payonline')->where($map)->order('id DESC')->limit($Lsql)->select();
	foreach($list as $key=>$v){
 		$list[$key]['status'] = $status_arr[$v['status']];
 	}
//	
	$row=array();
	$row['list'] = $list;
	$row['page'] = $page;
	$map['status'] = 1;
	$row['success_money'] = M('member_payonline')->where($map)->sum('money');
	$map['status'] = array('neq','1');
	$row['fail_money'] = M('member_payonline')->where($map)->sum('money');
	return $row;
}
//集合起每笔借款的每期的还款状态(逾期)
function getMBreakRepaymentList($uid=0,$size=10,$Wsql=""){
	if(empty($uid)) return;
	$pre = C('DB_PREFIX');
	
	if($size){
		//分页处理
		import("ORG.Util.Page");
	//	$count = M()->query("select d.id as count from {$pre}investor_detail d where d.borrow_id in(select tb.id from {$pre}borrow_info tb where tb.borrow_uid={$uid}) AND tb.borrow_status=6 AND d.deadline<".time()." AND d.repayment_time=0 {$Wsql} group by d.sort_order,d.borrow_id");
		$count = M()->query("select d.id as count from {$pre}investor_detail d left join {$pre}borrow_info b ON b.id=d.borrow_id where d.borrow_uid ={$uid} AND b.borrow_status=6 AND d.deadline<".time()." AND d.repayment_time=0 {$Wsql} group by d.sort_order,d.borrow_id");
		$count = count($count);
		$p = new Page($count, $size);
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		//分页处理
	}else{
		$page="";
		$Lsql="{$parm['limit']}";
	}
	
	$field = "b.borrow_name,d.status,d.total,d.borrow_id,d.sort_order,sum(d.capital) as capital,sum(d.interest) as interest,d.deadline,b.*";
	
	$sql = "select {$field} from {$pre}investor_detail d left join {$pre}borrow_info b ON b.id=d.borrow_id where d.borrow_uid ={$uid} AND b.borrow_status=6 AND d.deadline<".time()." AND d.repayment_time=0 {$Wsql} group by d.sort_order,d.borrow_id order by d.borrow_id,d.sort_order limit {$Lsql}";
	// echo $sql;
	$list = M()->query($sql);
	 print_r(M()->getlastsql());
	 //print_r($list);
	//exit;
	
	$status_arr =array('还未还','已还完','已提前还款','愈期还款','网站代还本金');
	$glodata = get_global_setting();
	$expired = explode("|",$glodata['fee_expired']);
	$call_fee = explode("|",$glodata['fee_call']);
	foreach($list as $key=>$v){
		$list[$key]['status'] = $status_arr[$v['status']];
		$list[$key]['breakday'] = getExpiredDays($v['deadline']);
		
		if($list[$key]['breakday']>$expired[0]){
			$list[$key]['expired_money'] = getExpiredMoney($list[$key]['breakday'],$v['capital'],$v['interest']);
		}
		
		if($list[$key]['breakday']>$call_fee[0]){
			$list[$key]['call_fee'] = getExpiredCallFee($list[$key]['breakday'],$v['capital'],$v['interest']);
		}
		
		$list[$key]['allneed'] = $list[$key]['call_fee'] + $list[$key]['expired_money'] + $v['capital'] + $v['interest'];
	}
	$row=array();
	$row['list'] = $list;
	$row['page'] = $page;
	$row['count'] = $count;
	return $row;
}
//集合起每笔借款的每期的还款状态(逾期)
function getMBreakInvestList($map,$size=10){
	$pre = C('DB_PREFIX');
	
	if($size){
		//分页处理
		import("ORG.Util.Page");
		$count = M('investor_detail d')->where($map)->count('d.id');
		$p = new Page($count, $size);
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		//分页处理
	}else{
		$page="";
		$Lsql="{$parm['limit']}";
	}
	
	$field = "m.user_name as borrow_user,b.borrow_interest_rate,d.borrow_id,b.borrow_name,d.status,d.total,d.borrow_id,d.sort_order,d.interest,d.capital,d.deadline,d.sort_order";
	$list =M('investor_detail d')->field($field)->join("{$pre}borrow_info b ON b.id=d.borrow_id")->join("{$pre}members m ON m.id=b.borrow_uid")->where($map)->limit($Lsql)->select();
	$status_arr =array('还未还','已还完','已提前还款','愈期还款','网站代还本金');
	$glodata = get_global_setting();
	$expired = explode("|",$glodata['fee_expired']);
	$call_fee = explode("|",$glodata['fee_call']);
	foreach($list as $key=>$v){
		$list[$key]['status'] = $status_arr[$v['status']];
		$list[$key]['breakday'] = getExpiredDays($v['deadline']);
	}
	$row=array();
	$row['list'] = $list;
	$row['page'] = $page;
	$row['count'] = $count;
	return $row;
}
function getBorrowList($map,$size,$limit=10){
	
	if(empty($map['borrow_uid'])) return;
	$Model = D("BorrowView");
	if($size){
		//分页处理
		import("ORG.Util.Page");		
		$subQuery = $Model->field(true)->where($map)->order('add_time desc')->group('id')->buildSql();
		$count = M()->table($subQuery.' a')->count();
		$p = new Page($count, $size);
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		//分页处理
	}else{
		$page="";
		$Lsql="{$parm['limit']}";
	}
	
	$Bconfig = C("BORROW");
	$status_arr =$Bconfig['BORROW_STATUS_SHOW'];
	$type_arr =$Bconfig['REPAYMENT_TYPE'];
	//$list = M('borrow_info')->where($map)->order('id DESC')->limit($Lsql)->select();
	/////////////使用了视图查询操作 fans 2013-05-22/////////////////////////////////	
	$list = $Model->field(true)->where($map)->order('add_time desc')->group('id')->limit($Lsql)->select();
	/////////////使用了视图查询操作 fans 2013-05-22/////////////////////////////////
	
 //var_dump($list); die;
 //var_dump($Model->getlastsql());//die;
//	echo("<br>");
//	var_dump($list);die;
	foreach($list as $key=>$v){
		$list[$key]['status'] = $status_arr[$v['borrow_status']];
		$list[$key]['repayment_type_num'] = $v['repayment_type'];
		$list[$key]['repayment_type'] = $type_arr[$v['repayment_type']];
		$list[$key]['progress'] = getFloatValue($v['has_borrow']/$v['borrow_money']*100,2);
		if($map['borrow_status']==6){
			$vx = M('investor_detail')->field('deadline')->where("borrow_id={$v['id']} and status=7")->order("deadline ASC")->find();
			$list[$key]['repayment_time'] = $vx['deadline'];
		}
		if($map['borrow_status']==5 || $map['borrow_status']==1){
			$vd = M('borrow_verify')->field(true)->where("borrow_id={$v['id']}")->find();
			$list[$key]['dealinfo'] = $vd;
		}		
	}
	
	$row=array();
	$row['list'] = $list;
	$row['page'] = $page;
	//$map['status'] = 1;
	//$row['success_money'] = M('member_payonline')->where($map)->sum('money');
	//$map['status'] = array('neq','1');
	//$row['fail_money'] = M('member_payonline')->where($map)->sum('money');
	return $row;
}
function getTenderList2($map,$size,$limit=10){
	$pre = C('DB_PREFIX');
	$Bconfig = C("BORROW");
	//if(empty($map['i.investor_uid'])) return;
	if(empty($map['investor_uid'])) return;
	if($size){
		//分页处理
		import("ORG.Util.Page");
		// $count = M('borrow_investor i')->where($map)->count('i.id');
		$count = M('borrow_investor i')->field($field)->where($map)->join("{$pre}borrow_info b ON b.id=i.borrow_id")->count('i.id');
		$p = new Page($count, $size);
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		//分页处理
	}else{
		$page="";
		$Lsql="{$parm['limit']}";
	}
	
	$type_arr =C('BORROW_TYPE');
	
 
	//,Borrow.id as id
/*	$list = M('borrow_investor i')->field($field)->where($map)->join("{$pre}borrow_info b ON b.id=i.borrow_id")->join("{$pre}members m ON m.id=b.borrow_uid")->order('i.deadline ASC')->limit($Lsql)->select();*/
	
	/////////////////////////视图查询 fan 20130522//////////////////////////////////////////
	$Model = D("TenderListView");
	if($map['add_time']!=""){
		$map['invest_time']=$map['add_time'];
		unset($map['add_time']);	
	}
	// $list=$Model->field(true)->where($map)->order('times ASC')->group('id')->limit($Lsql)->select();		
	$list=$Model->field(true)->where($map)->order('invest_time desc,borrowid desc')->group('id')->limit($Lsql)->select();

	
//   var_dump($list);die;
	////////////////////////视图查询 fan 20130522//////////////////////////////////////////
	foreach($list as $key=>$v){
		//if($map['i.status']==4){
//		if($map['status']==4){
//			$list[$key]['total'] = ($v['borrow_type']==3)?"1":$v['borrow_duration'];
//			$list[$key]['back'] = $v['has_pay'];
//			$vx = M('investor_detail')->field('deadline')->where("borrow_id={$v['borrowid']} and status=7")->order("deadline ASC")->find();
//			$list[$key]['repayment_time'] = $vx['deadline'];
//		}
		$field = "id,borrow_uid,borrow_money,borrow_status,borrow_type,has_borrow,has_vouch,borrow_interest_rate,borrow_duration,repayment_type,collect_time,borrow_min,borrow_max,password,borrow_use,deadline,pid,borrow_img";
		$vo = M('borrow_info')->field($field)->find($list[$key]['borrow_id']);
		$pro = M('pro_category')->find($vo['pid']);
		$list[$key]['borrow_status']=$vo["borrow_status"];
		$list[$key]['borrow_img']=$vo["borrow_img"];
		$list[$key]['type_name']=$pro["type_name"];
		
		// $list[$key]['investor_capital']=$v["investor_capital"];
//
//		if($vo['borrow_status'] == 7){
//			$list[$key]['hk_time'] = $vo['deadline'];
//		}else{
//			if(!empty($v['deadline'])){
//
//				$list[$key]['hk_time'] = $v['deadline'];
//			}else{
//				$list[$key]['hk_time'] = '';
//
//			}
//			// if(!empty($v['second_verify_time'])){
//			// 	$list[$key]['hk_time'] = $v['second_verify_time']+$vo['borrow_duration']*24*3600;
//			// }else{
//			// 	$list[$key]['hk_time'] = '';
//			// }
//		}
//		// 销售周期
//		if(empty($v['start_time'])){
//			$list[$key]['start_time'] = $v['first_verify_time'];
//		}
//		if(!empty($v['second_verify_time'])){
//			$list[$key]['sell_deadline_cn_last'] = ceil(($v['sell_deadline'] - time())/(24*3600)).'天';
//			$list[$key]['sell_deadline_cn'] = ceil(($v['sell_deadline'] - $v['second_verify_time'])/(24*3600)).'天';
//			// $list[$key]['sell_deadline_cn_last'] = second2string($v['sell_deadline'] - time());
//		}else{
//			// $list[$key]['sell_deadline_cn_last'] = '投资中';
//		}
		// $list[$key]['sell_deadline_cn'] = second2string($list[$key]['repayment_time'] - $v['second_verify_time'],2);	
		// $list[$key]['sell_deadline_cn'] = second2string(strtotime(date('Y-m-d 00:00:00',$v['repayment_time'])) - $v['second_verify_time'],2);
	}
	
	$row=array();
	$row['list'] = $list;
	$row['page'] = $page;
	if($map['invest_time']!=""){
		$map['add_time']=$map['invest_time'];
		unset($map['invest_time']);	
	}
	$row['total_money'] = M('borrow_investor i')->where($map)->sum('investor_capital');
	$row['lixi'] = M('borrow_investor i')->where($map)->sum('investor_interest');
	$row['total_num'] = $count;

		$row['list'] = $list;
	$row['page'] = $page;
	return $row;
}

function getTenderList($map,$size,$limit=10){
	$pre = C('DB_PREFIX');
	$Bconfig = C("BORROW");
	//if(empty($map['i.investor_uid'])) return;
	if(empty($map['investor_uid'])) return;
	if($size){
		//分页处理
		import("ORG.Util.Page");
		// $count = M('borrow_investor i')->where($map)->count('i.id');
		$count = M('borrow_investor i')->field($field)->where($map)->join("{$pre}borrow_info b ON b.id=i.borrow_id")->count('i.id');
		$p = new Page($count, $size);
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		//分页处理
	}else{
		$page="";
		$Lsql="{$parm['limit']}";
	}
	
	$type_arr =C('BORROW_TYPE');
	
	/*$field = "i.*,i.add_time as invest_time,m.user_name as borrow_user,b.borrow_duration,b.has_pay,b.borrow_interest_rate,b.add_time as borrow_time,b.borrow_money,b.borrow_name,m.credits,b.repayment_type,b.borrow_type";
	
	$list = M('borrow_investor i')->field($field)->where($map)->join("{$pre}borrow_info b ON b.id=i.borrow_id")->join("{$pre}members m ON m.id=b.borrow_uid")->order('i.deadline ASC')->limit($Lsql)->select();*/
	
	/////////////////////////视图查询 fan 20130522//////////////////////////////////////////
	$Model = D("TenderListView");
	if($map['add_time']!=""){
		$map['invest_time']=$map['add_time'];
		unset($map['add_time']);	
	}
	// $list=$Model->field(true)->where($map)->order('times ASC')->group('id')->limit($Lsql)->select();		
	$list=$Model->field(true)->where($map)->order('invest_time desc,borrowid desc')->group('id')->limit($Lsql)->select();	
	// echo $Model->getLastSql();die;
	////////////////////////视图查询 fan 20130522//////////////////////////////////////////
	foreach($list as $key=>$v){
		//if($map['i.status']==4){
		if($map['status']==4){
			$list[$key]['total'] = ($v['borrow_type']==3)?"1":$v['borrow_duration'];
			$list[$key]['back'] = $v['has_pay'];
			$vx = M('investor_detail')->field('deadline')->where("borrow_id={$v['borrowid']} and status=7")->order("deadline ASC")->find();
			$list[$key]['repayment_time'] = $vx['deadline'];
		}
		$field = "id,borrow_uid,borrow_money,borrow_status,borrow_type,has_borrow,has_vouch,borrow_interest_rate,borrow_duration,repayment_type,collect_time,borrow_min,borrow_max,password,borrow_use,deadline,pid,borrow_img";
		$vo = M('borrow_info')->field($field)->find($list[$key]['borrow_id']);
		$pro = M('pro_category')->find($vo['pid']);
		$list[$key]['borrow_status']=$vo["borrow_status"];
		$list[$key]['borrow_img']=$vo["borrow_img"];
		$list[$key]['type_name']=$pro["type_name"];
		
		// $list[$key]['investor_capital']=$v["investor_capital"];
			
		if($vo['borrow_status'] == 7){
			$list[$key]['hk_time'] = $vo['deadline'];
		}else{
			if(!empty($v['deadline'])){

				$list[$key]['hk_time'] = $v['deadline'];
			}else{
				$list[$key]['hk_time'] = '';

			}
			// if(!empty($v['second_verify_time'])){
			// 	$list[$key]['hk_time'] = $v['second_verify_time']+$vo['borrow_duration']*24*3600;
			// }else{
			// 	$list[$key]['hk_time'] = '';
			// }
		}
		// 销售周期
		if(empty($v['start_time'])){
			$list[$key]['start_time'] = $v['first_verify_time'];
		}
		if(!empty($v['second_verify_time'])){
			$list[$key]['sell_deadline_cn_last'] = ceil(($v['sell_deadline'] - time())/(24*3600)).'天';
			$list[$key]['sell_deadline_cn'] = ceil(($v['sell_deadline'] - $v['second_verify_time'])/(24*3600)).'天';
			// $list[$key]['sell_deadline_cn_last'] = second2string($v['sell_deadline'] - time());			
		}else{
			// $list[$key]['sell_deadline_cn_last'] = '投资中';
		}
		// $list[$key]['sell_deadline_cn'] = second2string($list[$key]['repayment_time'] - $v['second_verify_time'],2);	
		// $list[$key]['sell_deadline_cn'] = second2string(strtotime(date('Y-m-d 00:00:00',$v['repayment_time'])) - $v['second_verify_time'],2);
	}
	
	$row=array();
	$row['list'] = $list;
	$row['page'] = $page;
	if($map['invest_time']!=""){
		$map['add_time']=$map['invest_time'];
		unset($map['invest_time']);	
	}
	$row['total_money'] = M('borrow_investor i')->where($map)->sum('investor_capital');
	$row['lixi'] = M('borrow_investor i')->where($map)->sum('investor_interest');
	$row['total_num'] = $count;
	
	return $row;
}
function getBackingList($map,$size,$limit=10){
	$pre = C('DB_PREFIX');
	if(empty($map['d.investor_uid'])) return;
	
	if($size){
		//分页处理
		import("ORG.Util.Page");
		$count = M('investor_detail d')->where($map)->count('d.id');
		$p = new Page($count, $size);
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		//分页处理
	}else{
		$page="";
		$Lsql="{$parm['limit']}";
	}
	
	$type_arr =C('BORROW_TYPE');
	$field = true;
	$list = M('investor_detail d')->field($field)->where($map)->order('d.id DESC')->limit($Lsql)->select();
	foreach($list as $key=>$v){
		//$list[$key]['status'] = $status_arr[$v['status']];
	}
	
	$row=array();
	$row['list'] = $list;
	$row['page'] = $page;
	$sx = M('investor_detail d')->field("sum(d.capital + d.interest) as tox")->where("d.status=1 AND d.investor_uid={$map['d.investor_uid']}")->find();
	$sxcount = M('borrow_investor')->where("status=4 AND investor_uid={$map['d.investor_uid']}")->count("id");
	$month = M('investor_detail d')->field("sum(d.capital + d.interest) as tox")->where($map)->find();
	$row['month_total'] = $month['tox'];
	$row['total_money'] = $sx['tox'];
	$row['total_num'] = $sxcount;
	return $row;
}
function getMVouchList($map,$size){
	$pre = C('DB_PREFIX');
	if(empty($map['v.uid'])) return;
	
	if($size){
		//分页处理
		import("ORG.Util.Page");
		$count = M('borrow_vouch v')->where($map)->count('v.id');
		$p = new Page($count, $size);
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		//分页处理
	}else{
		$page="";
		$Lsql="10";
	}
	
	$type_arr =C('BORROW_TYPE');
	$field = "v.*,b.borrow_name,b.borrow_status,b.total,b.has_pay,b.borrow_duration,b.borrow_money,b.has_borrow,b.has_vouch,b.repayment_type,m.user_name as borrow_user,m.credits";
	$list = M('borrow_vouch v')->field($field)->join("{$pre}borrow_info b ON b.id=v.borrow_id")->join("{$pre}members m ON m.id=b.borrow_uid")->where($map)->limit($Lsql)->select();
	$status_arr = array(
		"3"=>'标未满，流标',
		"1"=>'初审未通过',
		"5"=>'复审未通过'
	);
	foreach($list as $key=>$v){
		$list[$key]['borrow_progress'] = ceil($v['has_borrow']/$v['borrow_money']);
		$list[$key]['vouch_progress'] = ceil($v['has_vouch']/$v['borrow_money']);
		if($map['v.status']==2) $list[$key]['reason'] = $status_arr[$v['borrow_status']];
	}
	
	$row=array();
	$row['list'] = $list;
	$row['page'] = $page;
	return $row;
}


/* 获取长效令牌 */
function yunhetong_login($url,$appId,$appKey){ // 模拟提交数据函数
 
   	$curl = curl_init();
 
		curl_setopt_array($curl, array(
		  CURLOPT_URL => $url,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
			    CURLOPT_HEADER => true,//false时，取得code
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "POST",
		  CURLOPT_POSTFIELDS => "{\n\"appId\":\"$appId\",\n\"appKey\":\"$appKey\"\n}",
		  CURLOPT_HTTPHEADER => array(
			"cache-control: no-cache",
			"content-type: application/json"
		  ),
		));
 
		$response = curl_exec($curl);
		
		$err = curl_error($curl);
 
		curl_close($curl);
 
		if ($err) {
		  return "cURL Error #:" . $err.die;
		} else {
			 
		    //echo $response;
				$headArr = explode("\r\n", $response);
				foreach ($headArr as $loop) {
					if(strpos($loop, "token") !== false){
					   $token = trim(substr($loop, 6));
					   //$token = trim($loop);
					}
					if(strpos($loop, "code") !== false){
					   //$code = trim(substr($loop, 6));
					   $rp = trim($loop);
					}
				}
		}
	
			$arr = json_decode($rp, true);
			$code = $arr['code'];//code=200 说明成功
			$msg = $arr['msg'];
			//不成功
			if($code!="200" || !$token){
				return(0); die;
					print_r( "获取长效令牌,原因：".$msg).die;
			}else{
					return($token); 
			} 
			//
		
		//return $response; // 返回数据，json格式
}
//创建个人用户

function user_person($url,$data,$token){ 
		$userName =$data['uname'];////用户姓名（最长 15 字符）成采南
		$identityRegion =$data['identityRegion'];////身份地区：0 大陆，1 香港，2 台湾，3 澳门
		$certifyNum =$data['certifyNum'];////身份证号码，应用内唯一 520181198002175907
		$phoneRegion =$data['phoneRegion'];////手机号地区：0 大陆，1 香港、澳门，2 台湾
		$phoneNo = $data['phoneNo'];//手机号：1.大陆,首位为 1，长度 11 位纯数字；2.香港、澳门,长度为 8 的纯数字；3.台湾,长度为 10 的纯数字
		$caType =$data['caType'];//证书类型：B2 长效 CA 证书，固定字段
 
		$curl2 = curl_init();
		curl_setopt_array($curl2, array(
		  CURLOPT_URL => $url,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "POST",
		  CURLOPT_POSTFIELDS => "{\n\"caType\": \"$caType\",\n\"certifyNum\": \"$certifyNum\",\n\"identityRegion\": \"$identityRegion\",\n\"phoneNo\": \"$phoneNo\",\n\"phoneRegion\": \"$phoneRegion\",\n\"userName\": \"$userName\"\n}",
		 
		  CURLOPT_HTTPHEADER => array(
			"cache-control: no-cache",
			"content-type: application/json",
			"token: $token"
		  ),
		));
 
		$response2 = curl_exec($curl2);
		$err2 = curl_error($curl2);
 
		curl_close($curl2);
 
		if ($err2) {
		  return(0); die;  echo "cURL Error #:" . $err2.die;
		} else {
		    //echo $response2;
			$arr2 = json_decode($response2, true);
			$code2 = $arr2['code'];//code=200 说明成功
			$msg2 = $arr2['msg'];
			
				//不成功
				if($code2!=200){
					ajaxmsg("创建个人用户失败,原因：".$msg2,0);die;
					return("创建个人用户失败,原因：".$msg2 ); die;
					print_r( "创建个人用户失败,原因：".$msg2).die;
				}else{
					$signerId = $arr2['data']['signerId'];
					return intval($signerId);die;
						var_dump($arr2);die;
				} 
		} 
 }
 
 //查询个人用户

function  user_userInfo_signerIds($url,$data,$token){ 
		$userName =$data['uname'];////用户姓名（最长 15 字符）成采南
		$identityRegion =$data['identityRegion'];////身份地区：0 大陆，1 香港，2 台湾，3 澳门
		$certifyNum =$data['certifyNum'];////身份证号码，应用内唯一 520181198002175907
		$phoneRegion =$data['phoneRegion'];////手机号地区：0 大陆，1 香港、澳门，2 台湾
		$phoneNo = $data['phoneNo'];//手机号：1.大陆,首位为 1，长度 11 位纯数字；2.香港、澳门,长度为 8 的纯数字；3.台湾,长度为 10 的纯数字
		$caType =$data['caType'];//证书类型：B2 长效 CA 证书，固定字段
 
		$curl2 = curl_init();
		curl_setopt_array($curl2, array(
		  CURLOPT_URL => $url,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "POST",
		  CURLOPT_POSTFIELDS => "{}",
		  CURLOPT_HTTPHEADER => array(
			"cache-control: no-cache",
			"content-type: application/json",
			"token: $token"
		  ),
		));
 
		$response2 = curl_exec($curl2);
		$err2 = curl_error($curl2);
 
		curl_close($curl2);
 
		if ($err2) {
		  return(0); die;  echo "cURL Error #:" . $err2.die;
		} else {
		    //echo $response2;
			$arr2 = json_decode($response2, true);
			$code2 = $arr2['code'];//code=200 说明成功
			$msg2 = $arr2['msg'];
			
				//不成功
				if($code2!=200){
					return(0); die;print_r( "查询个人用户,原因：".$msg2).die;
				}else{
					$signerId = $arr2['data']['signerId'];
					return $signerId;die;
						var_dump($arr2);die;
				}
			
			
 
		}
		 
	
 }
 /* 创建个人印模 */
 
 function user_personMoulage($url,$data,$token){
$signerId = $data['signerId'];	
$borderType = $data['borderType'];
$fontFamily = $data['fontFamily']; 
 $curl3 = curl_init();
 
		curl_setopt_array($curl3, array(
		  CURLOPT_URL => $url,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "POST",
		   CURLOPT_POSTFIELDS => "{\n\"signerId\": \"$signerId\",\n\"borderType\": \"$borderType\",\n\"fontFamily\": \"$fontFamily\"\n}",
		  CURLOPT_HTTPHEADER => array(
			"cache-control: no-cache",
			"content-type: application/json",
			"token: $token"
		  ),
		));
 
		$response3 = curl_exec($curl3);
		$err3 = curl_error($curl3);
 
		curl_close($curl3);
 
		if ($err3) {
		  return(0); die;echo "cURL Error #:" . $err3.die;
		} else {
		   // echo $response3;
			$arr3 = json_decode($response3, true);
			$code3 = $arr3['code'];//code=200 说明成功
			$msg3 = $arr3['msg'];
				//不成功
				if($code3!=200){
					return(0); die;print_r( "创建个人印模失败,原因：".$msg3).die;
				}else{
					$moulageId = $arr3['data']['moulageId'];
					return $moulageId;die;
						var_dump($arr3);die;
				}
			
		
			
		}
//$moulageId=82;
////print_r($moulageId);
 }
  /* 获取印模列表 */
  ///{signerId}/{pageNum}/{pageSize}
  
 
 function user_moulageId($url,$data,$token){
$signerId = $data['signerId'];	
$pageNum = $data['pageNum'];
$pageSize = $data['pageSize']; 
 $curl3 = curl_init();
 
		curl_setopt_array($curl3, array(
		  CURLOPT_URL => $url,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "POST",
		   CURLOPT_POSTFIELDS => "{\n\"signerId\": \"$signerId\",\n\"pageNum\": \"$pageNum\",\n\"pageSize\": \"$pageSize\"\n}",
		  CURLOPT_HTTPHEADER => array(
			"cache-control: no-cache",
			"content-type: application/json",
			"token: $token"
		  ),
		));
 
		$response3 = curl_exec($curl3);
		$err3 = curl_error($curl3);
 
		curl_close($curl3);
 
		if ($err3) {
		  return(0); die;echo "cURL Error #:" . $err3.die;
		} else {
		   // echo $response3;
			$arr3 = json_decode($response3, true);
			$code3 = $arr3['code'];//code=200 说明成功
			$msg3 = $arr3['msg'];
				//不成功
				if($code3!=200){
					return(0); die;	print_r( "创建个人印模失败,原因：".$msg3).die;
				}else{
					$moulageId = $arr3['data']['moulageId'];
					return $moulageId;die;
						var_dump($arr3);die;
				}
			
		
			
		}
//$moulageId=82;
////print_r($moulageId);
 }
 
//根据模版生成合同 contract_templateContract 
   function contract_templateContract($url,$data,$token){ 
 
        $contractTitle=$data['contractTitle'];
        $templateId=$data['templateId'];
		$zz= $datact['contractData'];
//		=[
//   '${deal_name}'=>$vm[0]["borrow_name"],
// '${bianhao}'=>$vm[0]["id"],
// '${jiafang}'=>$vm[0]["jiafang"],
// '${idno}'=>$vm[0]["idno"],
// '${bingfang}'=>$global["bingfang"],
// '${bingfang_xinyongdaima}'=>$global["bingfang_xinyongdaima"]];  
   
// contractData
// 
// \n\"deal_name\": \"$contractTitle\",
// \n\"bianhao\": \"$contractTitle\",
// \n\"jiafang\": \"$contractTitle\",
// \n\"idno\": \"$contractTitle\",
// \n\"bingfang\": \"$contractTitle\",
// \n\"bingfang_xinyongdaima\": \"$contractTitle\",
$deal_name=   $zz['deal_name'];
  $jiafang =   $zz['jiafang'];
// contractData
   
		$curl4 = curl_init();
 
		curl_setopt_array($curl4, array(
		  CURLOPT_URL =>$url ,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "POST",
		   CURLOPT_POSTFIELDS => "{\n\"${test1}\": \"$jiafang\",\n\"deal_name\": \"$deal_name\",\n\"contractData\": \"$zz\",\n\"contractTitle\": \"$contractTitle\",\n\"templateId\": \"$templateId\"\n}",
		  CURLOPT_HTTPHEADER => array(
			"cache-control: no-cache",
			"content-type: application/json",
			"token: $token"
		  ),
		));

//	var_dump($curl4);die();
	
		$response4 = curl_exec($curl4);
		$err4 = curl_error($curl4);
 
		curl_close($curl4);
 
		if ($err4) {
		  return(0); die;echo "cURL Error #:" . $err4.die;
		} else {
		   // echo $response4;
			$arr4 = json_decode($response4, true);
			$code4 = $arr4['code'];//code=200 说明成功
			$msg4 = $arr4['msg'];
				//不成功
				if($code4!=200){
					return(0); die;print_r( "创建个人生成合同,原因：".$msg4).die;
				}
			$contractId = $arr4['data']['contractId'];
			$contractId=str_replace(",","",number_format($contractId));
		}
 
 
 return($contractId);
          //return(intval($contractId));
//$contractId="1804232025535010";
 }
 

//添加签署者
    function contract_signer($url,$data,$token){ 
$idType= $data["idType"];//参数类型：0 合同 ID，1 合同自定义编号
 //ID 内容
	$contractId=str_replace(",","",number_format($data["contractId"]));
$signerId=$data["signerId"];//签署者 id 
$signPositionType = $data["signPositionType"];////签署的定位方式：0=关键字定位，1=签 名占位符定位，2=签署坐标
$positionContent = $data["positionContent"];////对应定位方式的内容，如果用签名占位符 定位可以传多个签名占位符，并以分号隔开,最多 20 个;如果用签署坐标定位， 则该参数包含三个信息：“页面,x 轴坐标,y 轴坐标”（如 20,30,49）
$signValidateType = $data["signValidateType"];////签署验证方式：0=不校验，1=短信验证
$signMode = $data["signMode"];////印章使用类型（针对页面签署）：0=指定印章，1=每次绘制
//return $positionContent;
		$curl5 = curl_init();
 //return $contractId;
		curl_setopt_array($curl5, array(
		  CURLOPT_URL => $url,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "POST",
		   //CURLOPT_POSTFIELDS => "{\n\"contractTitle\": \"$contractTitle\",\n\"templateId\": \"$templateId\"\n}",
			 CURLOPT_POSTFIELDS => "{\"idType\": \"$idType\",\n\"idContent\": \"$contractId\",\n\"signers\": [{\"signerId\": \"$signerId\",\n\"signPositionType\": \"1\",\n\"positionContent\": \"$positionContent\",\n\"signValidateType\": \"0\"}\n]\n}",
		  CURLOPT_HTTPHEADER => array(
			"cache-control: no-cache",
			"content-type: application/json",
			"token: $token"
		  ),
		));
 
		$response5 = curl_exec($curl5);
		$err5 = curl_error($curl5);
 
		curl_close($curl5);
        writeLog($err5);
		if ($err5) {
//		 return(0); die; echo "cURL Error #:" . $err5.die;
		} else {
		    //echo $response5;
			$arr5 = json_decode($response5, true);
			$code5 = $arr5['code'];//code=200 说明成功
			$msg5 = $arr5['msg'];
            writeLog($arr5);
		}
		//不成功
		if($code5!=200){
 		return(0); die;
//	return"添加签署者,原因：".$msg5.$code5['code'];
			print_r( "添加签署者,原因：".$msg5.$code5['code']);die;
		}else{
			return(1); die; print_r(1);
		}
 
         // print_r($code5);
 }
 
//合同签署 
function contract_sign($url,$data,$token){ 
 $idType= $data['idType'];
$contractId=str_replace(",","",number_format($data["idContent"]));
//var_dump($contractId);
   $signerId= intval($data['signerId']);
//print_r($contractId);
 
		$curl6 = curl_init();
 
		curl_setopt_array($curl6, array(
		  CURLOPT_URL => $url,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "POST",
		   CURLOPT_POSTFIELDS => "{\n\"idType\": \"$idType\",\n\"idContent\": \"$contractId\",\n\"signerId\": \"$signerId\"\n}",
		  CURLOPT_HTTPHEADER => array(
			"cache-control: no-cache",
			"content-type: application/json",
			"token: $token"
		  ),
		));
 
		$response6 = curl_exec($curl6);
		$err6 = curl_error($curl6);
 
		curl_close($curl6);
 
		if ($err6) {
		 // echo "cURL Error #:" . $err6.die;
		} else {
		   //  echo $response6;
			$arr6 = json_decode($response6, true);
			$code6 = $arr6['code'];//code=200 说明成功
			$msg6 = $arr6['msg'];

		}
		//不成功
		if($code6!=200){
				if($code6==20105){
				return(1); die;
			}
		    	//echo("<script>alert('".$code6.$msg6."')</script>");
// 			print_r( "合同签署失败,原因：".$msg6);die;
 			return(["state"=>0,'code'=>$code6,"msg"=>$msg6]); die;
			// 20105 已签署
//			print_r( "合同签署失败,原因：".$msg6).die;
		}else{
			return(["state"=>1,'code'=>$code6,"msg"=>$msg6]); die;
 		return(1); die;
			print_r($response6);
		}
 
 }
 
 //合同下载/download/contract
 function download_contract($url,$data,$token){ 
 $idType= $data['idType'];
$contractId=str_replace(",","",number_format($data["idContent"]));
//var_dump($contractId);
    //合同下载
 
  //$contractId="1804241101415029";
  
 
		$curl7 = curl_init();
 
		curl_setopt_array($curl7, array(
		  CURLOPT_URL => "https://api.yunhetong.com/api/contract/download/0/$contractId",
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "GET",
		  //CURLOPT_POSTFIELDS => "{\n\"idType\": \"$idType\",\n\"idContent\": \"$contractId\"\n}",
		  CURLOPT_HTTPHEADER => array(
			"cache-control: no-cache",
			"content-type: application/json",
			"token: $token"
		  ),
		));
 
		$response7 = curl_exec($curl7);
		$err7 = curl_error($curl7);
 
		curl_close($curl7);
 //var_dump("https://api.yunhetong.com/api/contract/download/0/$contractId");
		if ($err7) {
		  echo "cURL Error #:" . $err7.die;
		} else {
		    echo $response7; 
			$arr7 = json_decode($response7, true);
			$code7 = $arr7['code'];//code=200 说明成功
			$msg7 = $arr7['msg'];
			$data7 = $arr7['data'];
				//不成功
				if($code7!=200){
					return(0); die;
					print_r( "合同下载失败,原因：".$msg7).die;
				}else{
					print_r("合同下载成功!");
					header("Location: https://api.yunhetong.com/api/auth/download/$data7"); 
				}
              
		}
 
  } 

 function download_contract1($url,$data,$token){ 
 $idType= $data['idType'];
$contractId=str_replace(",","",number_format($data["idContent"]));
    //合同下载
 
  //$contractId="1804241101415029";
 
		$curl7 = curl_init();
 
		curl_setopt_array($curl7, array(
	    CURLOPT_URL => "https://api.yunhetong.com/api/download/contract",
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "GET",
		 // CURLOPT_POSTFIELDS => "{\n\"idType\": \"0\",\n\"idContent\": \"$contractId\"\n}",
		  CURLOPT_HTTPHEADER => array(
			"cache-control: no-cache",
			"content-type: application/json",
			"token: $token"
		  ),
		));
 
		$response7 = curl_exec($curl7);
		$err7 = curl_error($curl7);
 
		curl_close($curl7);
// var_dump($response7,$token,$contractId);die;
		if ($err7) {
		  echo "cURL Error #:" . $err7.die;
		} else {
		    echo $response7;
			$arr7 = json_decode($response7, true);
			$code7 = $arr7['code'];//code=200 说明成功
			$msg7 = $arr7['msg'];
			$data7 = $arr7['data'];
				//不成功
				if($code7!=200){
					return(0); die;
					print_r( "合同下载失败,原因：".$msg7).die;
				}else{
					print_r("合同下载成功!");
					header("Location: https://api.yunhetong.com/api/auth/download/$data7"); 
				}
              
		}
 
  } 