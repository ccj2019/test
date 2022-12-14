<?php
// 全局设置
class PaylogAction extends ACommonAction
{
    /**
    +----------------------------------------------------------
    * 默认操作
    +----------------------------------------------------------
    */
    public function index()
    {
		$save['deal_user'] = session('adminname');
		$save['status'] = 3;
		
		// var_dump($save,"<br/>");
		M('member_payonline')->where("status=0 and way='smf' and add_time < ".(time()-86400))->save($save);

		//var_dump(M('member_payonline'));exit();


		
		if(!empty($_REQUEST['status']) && $_REQUEST['status']>-1){
			$map['status'] = intval($_REQUEST['status']);
			$search['status'] = $map['status'];
		}else{
			$search['status'] = -1;
		}
		if(!empty($_REQUEST['way'])){
			
			if($_REQUEST['way']=='线下充值')
			{
				$map['way'] ='off'; //$_REQUEST['way'];
			}else
			{
				$map['way'] = $_REQUEST['way'];
			}
			$search['way'] = $map['way'];
		}
		if(!empty($_REQUEST['off_bank'])){			
				$map['off_bank'] = $_REQUEST['off_bank'];	
				$search['off_bank'] = $map['off_bank'];
		}
		if(!empty($_REQUEST['uname'])){
			$uid = M("members")->getFieldByUserName(text(urldecode($_REQUEST['uname'])),'id');
			$map['uid'] = $uid;
			$search['uid'] = $map['uid'];
		}
		if(!empty($_REQUEST['dealuser'])){
			$map['deal_user'] = text(urldecode($_REQUEST['dealuser']));
			$search['dealuser'] = $map['deal_user'];
		}
		if(!empty($_REQUEST['uid'])){
			$map['uid'] = intval($_REQUEST['uid']);
			$search['uid'] = $map['uid'];
		}
		if(!empty($_REQUEST['start_time'])&&!empty($_REQUEST['end_time'])){
			$start_time = strtotime($_REQUEST['start_time']);
			$end_time = strtotime($_REQUEST['end_time']);
			$map['add_time'] = array("between","{$start_time},{$end_time}");
			$search['start_time'] = $_REQUEST['start_time'];
			$search['end_time'] = $_REQUEST['end_time'];
			$xtime['start_time'] = $_REQUEST['start_time'];
			$xtime['end_time'] = $_REQUEST['end_time'];
		}
		$this->assign('search',$search);
		
	 	$listType = C('PAYLOG_TYPE');
		$listType[-1]="不限制";
		$this->assign('type_list',$listType);
		$field= 'id,uid,status,money,add_time,tran_id,way,off_bank,off_way,deal_user,deal_info,nid';
		$this->_list(D('Paylog'),$field,$map,'id','DESC',$xtime);
		//$search['a'] = 1;
		$this->assign("query", http_build_query($search));
		//$search['a'] = 2;
		//$this->assign("query1", http_build_query($search));
        $this->display();
    }
	
	public function export(){
		import("ORG.Io.Excel");
		$map=array();
		
		if(!empty($_REQUEST['status']) && $_REQUEST['status']>-1){
			$map['p.status'] = intval($_REQUEST['status']);
			$search['status'] = $map['status'];
		}else{
			$search['status'] = -1;
		}
		if(!empty($_REQUEST['way'])){
			if($_REQUEST['way']=='线下充值')
			{
				$map['p.way'] ='off'; //$_REQUEST['way'];
			}else
			{
				$map['p.way'] = $_REQUEST['way'];
			}
		}
		
		if(!empty($_REQUEST['uname'])){
			$uid = M("members")->getFieldByUserName(text(urldecode($_REQUEST['uname'])),'id');
			$map['p.uid'] = $uid;
			$search['uid'] = $map['uid'];
		}
		if(!empty($_REQUEST['dealuser'])){
			$map['deal_user'] = text(urldecode($_REQUEST['dealuser']));
			$search['dealuser'] = $map['deal_user'];
		}
		if(!empty($_REQUEST['uid'])){
			$map['p.uid'] = intval($_REQUEST['uid']);
			$search['uid'] = $map['uid'];
		}
		if(!empty($_REQUEST['start_time'])&&!empty($_REQUEST['end_time'])){
			$start_time = strtotime($_REQUEST['start_time']);
			$end_time = strtotime($_REQUEST['end_time']);
			$map['p.add_time'] = array("between","{$start_time},{$end_time}");
		
		}
		
	 	$listType = C('PAYLOG_TYPE');
		$listType[-1]="不限制";
		$field= 'p.*,m.user_name,mi.real_name';
		$list = M('member_payonline p')->field($field)->join("lzh_members m ON p.uid=m.id")->join("lzh_member_info mi ON p.uid=mi.uid")->where($map)->order(' p.id DESC ')->select();

		
		$row=array();
		$row[0]=array('序号','用户ID','用户名','真实姓名','充值方式','充值金额','充值时间','充值状态','对帐订单号','处理人');
		$i=1;
		$payType = C('ZF_WAY');
		$status_arr =array('充值未完成','充值成功','签名不符','充值失败');
		foreach($list as $v){
			$row[$i]['i'] = $i;
			$row[$i]['uid'] = $v['uid'];
			$row[$i]['user_name'] = $v['user_name'];
			$row[$i]['real_name'] = $v['real_name'];
			$row[$i]['payType'] = $payType[$v['way']];
			$row[$i]['money'] = $v['money'];
			$row[$i]['add_time'] =  date("Y-m-d H:i:s",$v['add_time']);
			if($v['status']==0){
				$row[$i]['status'] =$v['status']."失败";
			}else{
				$row[$i]['status'] = $v['status']."成功";	
			}
			$row[$i]['status']=$status_arr[$v['status']];
			
			if($v['way']=="off" or $v['way']=="online"){
				$row[$i]['tran_id'] = "入帐银行：".$v["off_bank"]."##充值方式：".$v["off_way"];
			}else{
				$row[$i]['tran_id'] = $v["tran_id"];	
			}
			$row[$i]['deal_user'] = $v["deal_user"];
			$i+=1;
		}
		
		$xls = new Excel_XML('UTF-8', true, 'datalist');
		$xls->addArray($row);
		$xls->generateXML(date("Y-m-d")."datalistpay");
	}
	
	public function exportall(){
		import("ORG.Io.Excel");
		$map=array();
		
		if(!empty($_REQUEST['status']) && $_REQUEST['status']>-1){
			$map['p.status'] = intval($_REQUEST['status']);
			$search['status'] = $map['status'];
		}else{
			$search['status'] = -1;
		}
		if(!empty($_REQUEST['way'])){
			if($_REQUEST['way']=='线下充值')
			{
				$map['p.way'] ='off'; //$_REQUEST['way'];
			}else
			{
				$map['p.way'] = $_REQUEST['way'];
			}
		}
		
		if(!empty($_REQUEST['uname'])){
			$uid = M("members")->getFieldByUserName(text(urldecode($_REQUEST['uname'])),'id');
			$map['p.uid'] = $uid;
			$search['uid'] = $map['uid'];
		}
		if(!empty($_REQUEST['dealuser'])){
			$map['deal_user'] = text(urldecode($_REQUEST['dealuser']));
			$search['dealuser'] = $map['deal_user'];
		}
		if(!empty($_REQUEST['uid'])){
			$map['p.uid'] = intval($_REQUEST['uid']);
			$search['uid'] = $map['uid'];
		}
		if(!empty($_REQUEST['start_time'])&&!empty($_REQUEST['end_time'])){
			$start_time = strtotime($_REQUEST['start_time']);
			$end_time = strtotime($_REQUEST['end_time']);
			$map['p.add_time'] = array("between","{$start_time},{$end_time}");
			$amap['add_time'] = array("between","{$start_time},{$end_time}");
			$asql=" and add_time BETWEEN '{$start_time}' AND '{$end_time}'";
			$bsql=" and i.add_time BETWEEN '{$start_time}' AND '{$end_time}'";
		}
		
	 	$listType = C('PAYLOG_TYPE');
		$listType[-1]="不限制";
		$field= 'p.*,m.user_name,mi.real_name';
		$list = M('member_payonline p')->field($field)->join("lzh_members m ON p.uid=m.id")->join("lzh_member_info mi ON p.uid=mi.uid")->where($map)->order(' p.id DESC ')->group("p.uid")->select();
		
		$row=array();
		$row[0]=array('序号','用户ID','用户名','真实姓名','线上充值金额','线下充值金额','网站充值金额','投月标总额');
		$i=1;
		$payType = C('ZF_WAY');
		$status_arr =array('充值未完成','充值成功','签名不符','充值失败');
		
		foreach($list as $v){
			$row[$i]['i'] = $i;
			$row[$i]['uid'] = $v['uid'];
			$row[$i]['user_name'] = $v['user_name'];
			$row[$i]['real_name'] = $v['real_name'];
			$a1=m("member_payonline")->field("sum(money) as money")->where("uid={$v[uid]} AND status=1 and way<>'off' and way<>'online' ".$asql)->find();
			$row[$i]['money'] = $a1['money']==""?0:$a1['money'];
			$a2=m("member_payonline")->field("sum(money) as money")->where("uid={$v[uid]} AND status=1 and way='off' ".$asql)->find();
			$row[$i]['money_off'] = $a2['money']==""?0:$a2['money'];
			$a3=m("member_payonline")->field("sum(money) as money")->where("uid={$v[uid]} AND status=1 and way='online' ".$asql)->find();
			$row[$i]['money_oline'] = $a3['money']==""?0:$a3['money'];
			$a4=M('borrow_investor i')->join("lzh_borrow_info b on b.id=i.borrow_id")->where("i.investor_uid={$v[uid]} and b.borrow_type<>3 ".$bsql)->sum('i.investor_capital'); 
			$row[$i]['money_investor'] = $a4==""?0:$a4;
			unset($a1);
			unset($a2);
			unset($a3);
			unset($a4);
			$i+=1;
		}
		$xls = new Excel_XML('UTF-8', true, 'datalist');
		$xls->addArray($row);
		$xls->generateXML(date("Y-m-d")."datalistpay");
	}
	
	public function edit(){
		setBackUrl();
		$this->assign("id",intval($_GET['id']));
		$vo = M('member_payonline')->field('deal_info')->find(intval($_GET['id']));
		$this->assign("vo",$vo);
		$this->display();
	}
	
	public function doEdit(){
        $status=1;
		$id=intval($_POST['id']);	
		$status = intval($_POST['status']);

		$mapd['id']=$this->admin_id;

		$info=M('ausers')->where($mapd)->find();
		if($info['real_name']!=session('adminname')){
			$this->error("处理失败");
		}

		$statusx = M('member_payonline')->getFieldById($id,"status");
		if ($statusx!=0){
			$this->error("请不要重复提交表单");
		}
		$vo = M('member_payonline')->field('money,fee,uid,way')->find($id);
		$vx = M('members')->field("user_name,user_phone")->find($vo['uid']);
		$save['source_from']=get_client_ip();
		if($status==1){
			//$vo = M('member_payonline')->field('money,fee,uid,way')->find($id);
// 			var_dump($vo);  
// 				die;
			$newid = memberMoneyLog($vo['uid'],27,$vo['money']-$vo['fee'],$_POST["deal_info"]);
			
			if($newid){
				//分销返佣
				// distribution_maid($id);
				$zv=$vo['money']-$vo['fee'];
			 notice1(6, $vo['uid'], $data = array("MONEY"=>$zv));
				////////////////////////////
				if($vo['way']=="off"){
					$tqfee = explode( "|", $this->glo['offline_reward']);
					$fee[0] = explode( "-", $tqfee[0]);
					$fee[2] = explode( "-", $tqfee[2]);
					$fee[1] = floatval($tqfee[1]);
					$fee[3] = floatval($tqfee[3]);
					$fee[4] = floatval($tqfee[4]);
					$fee[5] = floatval($tqfee[5]);
					if($vo['money']>=$fee[0][0] && $vo['money']<=$fee[0][1]){
						$fee_rate = 0<$fee[1]?($fee[1]/1000):0;
					}else if($vo['money']>$fee[2][0] && $vo['money']<=$fee[2][1]){
						$fee_rate = 0<$fee[3]?($fee[3]/1000):0;
					}else if($vo['money']>$fee[4]){
						$fee_rate = 0<$fee[5]?($fee[5]/1000):0;
					}else{
						$fee_rate = 0;
					}
					$newidx = memberMoneyLog($vo['uid'],32,$vo['money']*$fee_rate,"线下充值奖励");
				}
				/////////////////////////////
				/*
				$offline_reward = explode("|",$this->glo['offline_reward']);
				if($vo['money']>$offline_reward[0]){
					$fee_rate = 0<$offline_reward[1]?($offline_reward[1]/1000):0;
					$newidx = memberMoneyLog($vo['uid'],32,$vo['money']*$fee_rate,"线下充值奖励");
				}*/
				$save['deal_user'] = session('adminname');
				$save['deal_uid'] = $this->admin_id;
				$save['deal_info'] = $_POST["deal_info"];
				$save['status'] = 1;
		
				// var_dump($save,"<br/>");
				M('member_payonline')->where("id={$id}")->save($save);
				//M('member_payonline')->commit();
				// var_dump(M('member_payonline')->getlastsql(),"<br/>",$save,"<br/>2");
				// die;
				//$vx = M('members')->field("user_name,user_phone")->find($vo['uid']);
				if($vo['way']=="off") SMStip("payoffline",$vx['user_phone'],array("#USERANEM#","#MONEY#"),array($vx['user_name'],$vo['money']));
				else  {
					addInnerMsg($vo['uid'],'成功充值'.$vo['money'].'元','您刚刚成功充值了'.$vo['money'].'元！');
					SMStip("payonline",$vx['user_phone'],array("#USERANEM#","#MONEY#"),array($vx['user_name'],$vo['money']));
				};

				//入账记录 开始
				inAccountMoney($vo['uid'],$vo['money'],$vo['way']=="off" ? '2' : '1');
				//入账记录 结束
				$this->success("处理成功");
			}
			else $this->error("处理失败");
		}else{
			
			$save['deal_user'] = session('adminname');
			$save['deal_uid'] = $this->admin_id;
			$save['deal_info'] = $_POST["deal_info"];
			$save['status'] = 3;
			$newid = M('member_payonline')->where("id={$id}")->save($save);

			$payType =C('ZF_WAY');
			notice1(11, $vo['uid'], $data = array("user_name"=>$vx['user_name'],"fangshi"=>$payType[$vo["way"]],"yuanyin"=>$save['deal_info']));

			if($newid) $this->success("处理成功");
			else $this->error("处理失败");
		}
	}
	
	public function _listFilter($list){
	 	$listType = C('PAYLOG_TYPE');
	 	$payType =C('ZF_WAY');
	 	$off_bank = M('member_payonline')->field('DISTINCT off_bank')->where("off_bank !='' ")->select();
	 	$off_bank = array_filter($off_bank); 	
	 	$this->assign('off_bank',$off_bank);
		$this->assign("payType",$payType);
		$row=array();
		foreach($list as $key=>$v){
			$v['status_num'] = $v['status'];
			$v['status'] = $listType[$v['status']];
			$v['uname'] = M("members")->getFieldById($v['uid'],'user_name');
			$v['deal_info'] =$v['deal_info'];
			$v['xway'] = $payType[$v['way']];
			$row[$key]=$v;
		}
// 		var_dump($row);die;
		return $row;
	}
	
		/**
    +----------------------------------------------------------
    * 线上充值操作
    +----------------------------------------------------------
    */
    public function paylogonline()
    {
		
	
		if(!empty($_REQUEST['status'])){
			$map['status'] = intval($_REQUEST['status']);
			$search['status'] = $map['status'];
		}
		if(!empty($_REQUEST['uname'])){
			$uid = M("members")->getFieldByUserName(text($_REQUEST['uname']),'id');
			$map['uid'] = $uid;
			$search['uid'] = $map['uid'];
		}
		if(!empty($_REQUEST['uid'])){
			$map['uid'] = intval($_REQUEST['uid']);
			$search['uid'] = $map['uid'];
		}
		if(!empty($_REQUEST['way'])){
			if($_REQUEST['way']=='线下充值')
			{
				$map['way'] ='off'; //$_REQUEST['way'];
			}else
			{
				$map['way'] = $_REQUEST['way'];
			}
		}
		if(!empty($_REQUEST['dealuser'])){
			$map['deal_user'] = text(urldecode($_REQUEST['dealuser']));
			$search['dealuser'] = $map['deal_user'];
		}
		
		if(!empty($_REQUEST['start_time'])&&!empty($_REQUEST['end_time'])){
			$start_time = strtotime($_REQUEST['start_time']." 00:00:00");
			$end_time = strtotime($_REQUEST['end_time']." 23:59:59");
			$map['add_time'] = array("between","{$start_time},{$end_time}");
			$search['start_time'] = $_REQUEST['start_time'];
			$search['end_time'] = $_REQUEST['end_time'];
			$xtime['start_time'] = $_REQUEST['start_time'];
			$xtime['end_time'] = $_REQUEST['end_time'];
		}
		$this->assign('search',$search);
		
	 	$listType = C('PAYLOG_TYPE');
		$listType[-1]="不限制";
		$this->assign('type_list',$listType);
		$field= 'id,uid,status,money,add_time,tran_id,way,off_bank,off_way,deal_user,deal_info,way_img';
		$map['way']=array("not in",'off,online');
		$this->_list(D('Paylog'),$field,$map,'id','DESC',$xtime);
        $this->display();
    }
	
	/**
    +----------------------------------------------------------
    * 线下充值操作
    +----------------------------------------------------------
    */
    public function paylogoffline()
    {
	
		if(!empty($_REQUEST['status'])){
			$map['status'] = intval($_REQUEST['status']);
			$search['status'] = $map['status'];
		}
		if(!empty($_REQUEST['uname'])){
			$uid = M("members")->getFieldByUserName(text($_REQUEST['uname']),'id');
			$map['uid'] = $uid;
			$search['uid'] = $map['uid'];
		}
		if(!empty($_REQUEST['uid'])){
			$map['uid'] = intval($_REQUEST['uid']);
			$search['uid'] = $map['uid'];
		}
		if(!empty($_REQUEST['way'])){
			if($_REQUEST['way']=='线下充值')
			{
				$map['way'] ='off'; //$_REQUEST['way'];
			}else
			{
				$map['way'] = $_REQUEST['way'];
			}
		}
		if(!empty($_REQUEST['dealuser'])){
			$map['deal_user'] = text(urldecode($_REQUEST['dealuser']));
			$search['dealuser'] = $map['deal_user'];
		}
		if(!empty($_REQUEST['start_time'])&&!empty($_REQUEST['end_time'])){
			$start_time = strtotime($_REQUEST['start_time']." 00:00:00");
			$end_time = strtotime($_REQUEST['end_time']." 23:59:59");
			$map['add_time'] = array("between","{$start_time},{$end_time}");
			$search['start_time'] = $_REQUEST['start_time'];
			$search['end_time'] = $_REQUEST['end_time'];
			$xtime['start_time'] = $_REQUEST['start_time'];
			$xtime['end_time'] = $_REQUEST['end_time'];
		}
		$this->assign('search',$search);
		
	 	$listType = C('PAYLOG_TYPE');
		$listType[-1]="不限制";
		$this->assign('type_list',$listType);
		$field= 'id,uid,way_img,status,money,add_time,tran_id,way,off_bank,off_way,deal_user,deal_info';
		$map['way']='off';
		$this->_list(D('Paylog'),$field,$map,'add_time','DESC',$xtime);
        $this->display();
    }
	public function dobulu(){
		$oid=$_REQUEST['oid'];

		$map['nid']=$oid;
		$vo = M('member_payonline')->where($map)->field('id,money,fee,uid,way,way_img')->find(); // var_dump($vo);
		if(empty($vo)){
			$data['msg']='订单不存在！';
			exit;
		}else if($vo['status']!=0){
			$data['msg']='订单状态错误！';
			exit;
		}else{
			$signStr='';
			$signStr  = $signStr . 'version=1';
			$signStr  = $signStr . '&agent_id='.C('hfb')['agent_id'];
			$signStr  = $signStr . '&agent_bill_id=' . $oid;
			$signStr  = $signStr . '&agent_bill_time=' . $vo['way_img'];
			$signStr = $signStr . '&key='.C('hfb')['key'];//商户签名密钥
			$sign=md5($signStr);
			$url='https://Pay.Heepay.com/DirectPay/query.aspx?version=1&agent_id='.C('hfb')['agent_id'].'&agent_bill_id='.$oid.'&agent_bill_time='.$vo['way_img'].'&remark=&return_mode=1&sign='.$sign;
			//  var_dump($url);
			$res=httpGet1($url);
			//var_dump($res);//exit;
			writeLog($res);
			if($res['ret_code']=='0000'&&$res['result']==1){
				$vx = M('members')->field("user_name,user_phone")->find($vo['uid']);
				if(!$vo){
					return false;
				}
				$newid = memberMoneyLog($vo['uid'],27,$vo['money']-$vo['fee'],'支付宝在线充值');
				writeLog('zfbbl');
				if($newid){
					//分销返佣
					// distribution_maid($id);
					$zv=$vo['money']-$vo['fee'];
					$save['deal_user'] = '超级管理员';
					$save['deal_uid'] = 0;
					if($vo['way']=='zfb'){
						$save['deal_info'] = '支付宝在线充值';
					}
					if($vo['way']=='wx'){
						$save['deal_info'] = '微信在线充值';
					}
					$save['status'] = 1;
					M('member_payonline')->where("id=".$vo['id'])->save($save);
					addInnerMsg($vo['uid'],'成功充值'.$vo['money'].'元','您刚刚成功充值了'.$vo['money'].'元！');
					notice1(5, $vo['uid'], $data1 = array("MONEY"=>$zv));
					$jsons['status']=1;
					$jsons['msg']=$res['ret_msg'];
					$jsons['data']=$res;
				}else{
					$jsons['status']=0;
					$jsons['msg']='失败';
				}
			}else{
				$jsons['status']=0;
				$jsons['msg']=$res['ret_msg'];
			}
		}
		if($jsons['status']=='1') $this->success($jsons['msg']);
		else $this->error($jsons['msg']);
//		if($jsons['status']=='1'){
//			$jsons['status']=1;
//			$jsons['msg']=$res['ret_msg'];
//			$jsons['data']=$res;
//		}else{
//			$jsons['status']=0;
//			$jsons['msg']='操作失败！';
//		}

	}
}
?>
