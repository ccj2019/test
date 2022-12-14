<?php
// 全局设置
class WithdrawlogingAction extends ACommonAction
{
    /**
    +----------------------------------------------------------
    * 默认操作 提现处理中
    +----------------------------------------------------------
    */
	public function index()
    {
		$map=array();
		if($_REQUEST['uid'] && $_REQUEST['uname']){
			$map['w.uid'] = $_REQUEST['uid'];
			$search['uid'] = $map['w.uid'];	
			$search['uname'] = urldecode($_REQUEST['uname']);	
		}
		
		if($_REQUEST['uname'] && !$search['uid']){
			$map['m.user_name'] = array("like",urldecode($_REQUEST['uname'])."%");
			$search['uname'] = urldecode($_REQUEST['uname']);	
		}
		if($_REQUEST['rname'] && !$search['uid']){

			$map['mi.real_name'] = array("like",urldecode($_REQUEST['rname'])."%");

			$search['rname'] = urldecode($_REQUEST['rname']);	

		}
		if($_REQUEST['deal_user']){
			$map['w.deal_user'] = urldecode($_REQUEST['deal_user']);
			$search['deal_user'] = $map['w.deal_user'];	
		}
		
		if(!empty($_REQUEST['bj']) && !empty($_REQUEST['money'])){
			$map['w.withdraw_money'] = array($_REQUEST['bj'],$_REQUEST['money']);
			$search['bj'] = $_REQUEST['bj'];	
			$search['money'] = $_REQUEST['money'];	
		}
		if(!empty($_REQUEST['start_time']) && !empty($_REQUEST['end_time'])){
			$timespan = strtotime(urldecode($_REQUEST['start_time'])).",".strtotime(urldecode($_REQUEST['end_time']));
			$map['w.add_time'] = array("between",$timespan);
			$search['start_time'] = urldecode($_REQUEST['start_time']);	
			$search['end_time'] = urldecode($_REQUEST['end_time']);	
		}elseif(!empty($_REQUEST['start_time'])){
			$xtime = strtotime(urldecode($_REQUEST['end_time']));
			$map['w.add_time'] = array("gt",$xtime);
			$search['start_time'] = $xtime;	
		}elseif(!empty($_REQUEST['end_time'])){
			$xtime = strtotime(urldecode($_REQUEST['end_time']));
			$map['w.add_time'] = array("lt",$xtime);
			$search['end_time'] = $xtime;	
		}
		if(session('admin_is_kf')==1)	$map['m.customer_id'] = session('admin_id');
	//	$map['bank.is_default'] = array("eq",1);
		//分页处理
		import("ORG.Util.Page");
		$map['w.withdraw_status'] =1;
		$count = M('member_withdraw w')->join("{$this->pre}members m ON m.id=w.uid")->where($map)->count('w.id');
		$all_money = M('member_withdraw w')->join("{$this->pre}members m ON w.uid=m.id")->where($map)->sum("w.withdraw_money");
		$this->assign("all_money", $all_money);
		$p = new Page($count, C('ADMIN_PAGE_SIZE'));
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		//分页处理
		//print_r($map);
		//$field= 'w.*,m.user_name,mi.real_name,w.id,w.uid,bank.name,bank.*,(mm.account_money+mm.back_money) all_money';
		//$list = M('member_withdraw w')->field($field)->join("lzh_members m ON w.uid=m.id")->join("lzh_member_banks bank ON w.uid=bank.uid")->join("lzh_member_info mi ON w.uid=mi.uid")->join("lzh_member_money mm on w.uid = mm.uid")->where($map)->order(' w.id DESC ')->limit($Lsql)->select();
		
		
		$field= 'w.*,m.user_name,mi.real_name,w.id,w.uid,(mm.account_money+mm.back_money) all_money,mi.real_name';
		$list = M('member_withdraw w')
		->field($field)
		->join("lzh_members m ON w.uid=m.id","LEFT")
	//	->join("lzh_member_banks bank ON w.uid=bank.uid","LEFT")
		->join("lzh_member_info mi ON w.uid=mi.uid","LEFT")->join("lzh_member_money mm on w.uid = mm.uid","LEFT")->where($map)->order(' w.id DESC ')->limit($Lsql)->group('w.id')->select();
	
 //	echo M('member_withdraw w')->getlastsql();die;
	//		var_dump($list,1);die;
 
// 		var_dump(count($list),1);die;
		
		$listType = C('WITHDRAW_STATUS');
		unset($listType[0],$listType[2],$listType[3]);
		$this->assign("bj", array("gt"=>'大于',"eq"=>'等于',"lt"=>'小于'));
        $this->assign("list", $list);
		$this->assign("status",$listType);
        $this->assign("pagebar", $page);
        $this->assign("search", $search);
        $this->assign("query", http_build_query($search));
		
        $this->display();
    }
	
	public function export(){
		import("ORG.Io.Excel");
		$map=array();
		if($_REQUEST['uid'] && $_REQUEST['uname']){
			$map['w.uid'] = $_REQUEST['uid'];
			$search['uid'] = $map['w.uid'];	
			$search['uname'] = urldecode($_REQUEST['uname']);	
		}
		
		if($_REQUEST['uname'] && !$search['uid']){
			$map['m.user_name'] = array("like",urldecode($_REQUEST['uname'])."%");
			$search['uname'] = urldecode($_REQUEST['uname']);	
		}
		
		if($_REQUEST['deal_user']){
			$map['w.deal_user'] = urldecode($_REQUEST['deal_user']);
			$search['deal_user'] = $map['w.deal_user'];	
		}
		
		if(!empty($_REQUEST['bj']) && !empty($_REQUEST['money'])){
			$map['w.withdraw_money'] = array($_REQUEST['bj'],$_REQUEST['money']);
			$search['bj'] = $_REQUEST['bj'];	
			$search['money'] = $_REQUEST['money'];	
		}
		if(!empty($_REQUEST['start_time']) && !empty($_REQUEST['end_time'])){
			$timespan = strtotime(urldecode($_REQUEST['start_time'])).",".strtotime(urldecode($_REQUEST['end_time']));
			$map['w.add_time'] = array("between",$timespan);
			$search['start_time'] = urldecode($_REQUEST['start_time']);	
			$search['end_time'] = urldecode($_REQUEST['end_time']);	
		}elseif(!empty($_REQUEST['start_time'])){
			$xtime = strtotime(urldecode($_REQUEST['end_time']));
			$map['w.add_time'] = array("gt",$xtime);
			$search['start_time'] = $xtime;	
		}elseif(!empty($_REQUEST['end_time'])){
			$xtime = strtotime(urldecode($_REQUEST['end_time']));
			$map['w.add_time'] = array("lt",$xtime);
			$search['end_time'] = $xtime;	
		}
		if(session('admin_is_kf')==1)	$map['m.customer_id'] = session('admin_id');
		$map['w.withdraw_status'] =1;
		$field= 'w.*,m.user_name,m.user_phone,mi.real_name,w.id,w.uid,bank.*,(mm.account_money+mm.back_money) all_money';
		$list = M('member_withdraw w')->field($field)->join("lzh_members m ON w.uid=m.id")->join("lzh_member_banks bank ON w.uid=bank.uid")->join("lzh_member_info mi ON w.uid=mi.uid")->join("lzh_member_money mm on w.uid = mm.uid")->where($map)->order(' w.id DESC ')->select();
		
		$row=array();
		$row[0]=array('序号','用户ID','用户名','用户电话','真实姓名','提现金额','到账金额','提现手续费','提现时间','提现状态','处理人','处理时间','银行账号','开户行');
		$i=1;
		foreach($list as $v){
				$row[$i]['i'] = $i;
				$row[$i]['uid'] = $v['id'];
				$row[$i]['card_num'] = $v['user_name'];
				$row[$i]['card_num2'] = $v['user_phone'];
				$row[$i]['card_pass'] = $v['real_name'];
				$row[$i]['card_mianfei'] = $v['withdraw_money'];
				$row[$i]['card_mianfeix'] = bcsub($v['withdraw_money'],$v['second_fee'],2);
				$row[$i]['card_second_fee'] = $v['second_fee'];
				$row[$i]['card_mianfei1'] = date("Y-m-d",$v['add_time']);
				if($v['withdraw_status']==0){
					$row[$i]['card_mianfei2'] = "待审核";
				}else if( $v['withdraw_status']==1){
					$row[$i]['card_mianfei2'] = "审核通过,处理中";
				}else if( $v['withdraw_status']==2){
					$row[$i]['card_mianfei2'] = "已提现";
				}else if( $v['withdraw_status']==3){
					$row[$i]['card_mianfei2'] = "未通过";
				}
				$row[$i]['card_mianfei3'] = $v['deal_user'];
				$row[$i]['card_timelimit'] = date("Y-m-d",$v['deal_time']);
				$row[$i]['card_bank_num'] = $v['bank_num']." ";
				$row[$i]['card_bank_province'] = $v['bank_province'].$v['bank_city'].$v['bank_name'].$v['bank_address'];
				$i++;
		}
		//print_r($row);
		//exit;
		$xls = new Excel_XML('UTF-8', true, 'datalist');
		$xls->addArray($row);
		$xls->generateXML("datalistcard");
	}
	
    //编辑
    public function edit() {
        $model = M('member_withdraw');
        $id = intval($_REQUEST['id']);
        $vo = $model->find($id);
		//$vo['uname'] = M("members")->getFieldById($vo['uid'],'user_name');
	 	$listType = C('WITHDRAW_STATUS');
		unset($listType[0],$listType[1]);
		$this->assign('type_list',$listType);
		/*$vo['real_name'] = M("member_info")->getFieldByUid($vo['uid'],'real_name');
	
		$vo['bank_num'] = M("member_banks")->getFieldByUid($vo['uid'],'bank_num');
		$vo['bank_province'] = M("member_banks")->getFieldByUid($vo['uid'],'bank_province');
		$vo['bank_city'] = M("member_banks")->getFieldByUid($vo['uid'],'bank_city');
		$vo['bank_address'] = M("member_banks")->getFieldByUid($vo['uid'],'bank_address');
		$vo['bank_name'] = M("member_banks")->getFieldByUid($vo['uid'],'bank_name');*/
		
	 /////////////////////////////////
	 	$field= 'w.*,m.user_name,mi.real_name,w.id,w.uid,(mm.account_money+mm.back_money) all_money';
		$list = M('member_withdraw w')->field($field)->join("lzh_members m ON w.uid=m.id")->join("lzh_member_info mi ON w.uid=mi.uid")->join("lzh_member_money mm on w.uid = mm.uid")->where("w.id=$id")->order(' w.id ASC ')->limit($Lsql)->select();
		foreach($list as $v){
			$vo['uname'] =$v['user_name'];
			$vo['real_name'] = $v['real_name'];
			$vo['bank_num'] =$v['bank_num'];
			$vo['bank_province'] = $v['bank_province'];
			$vo['bank_city'] =$v['bank_city'];
			$vo['bank_address'] = $v['bank_address'];
			$vo['bank_name'] =$v['bank_name'];
			$vo['all_money'] =$v['all_money'];
			$vo['withdraw_fee'] =$v['withdraw_fee'];
		}
	 //////////////////////////////////////
        $this->assign('vo', $vo);
        $this->display();
    }
	
	 public function doEdit() {
		 
        $model = D("member_withdraw");
		$status = intval($_POST['withdraw_status']);
		$id = intval($_POST['id']);
		$deal_info = $_POST['deal_info'];
		$secondfee = floatval($_POST['withdraw_fee']);
        if (false === $model->create()) {
            $this->error($model->getError());
        }
        //保存当前数据对象
		$model->withdraw_status = $status;
		$model->deal_info = $deal_info;
		$model->deal_time=time();
		$model->deal_user=session('adminname');
		////////////////////////
		$field= 'w.*,w.id,w.uid,(mm.account_money+mm.back_money) all_money';
		$vo = M("member_withdraw w")->field($field)->join("lzh_member_money mm on w.uid = mm.uid")->find($id);
		$um = M('members')->field("user_name,user_phone")->find($vo['uid']);
		if($vo['withdraw_status']<>3 && $status==3){
			
			addInnerMsg($vo['uid'],"您的提现申请审核未通过","您的提现申请审核未通过");
			if( ($vo['all_money']-$vo['second_fee'])<=0 ){
				  memberMoneyLog($vo['uid'],12,$vo['withdraw_money'],"提现未通过,返还,其中提现金额：".$vo['withdraw_money']."元，手续费：".$vo['second_fee']."元",'0','@网站管理员@',$vo['second_fee']);
			}else{
				 memberMoneyLog($vo['uid'],12,$vo['withdraw_money'],"提现未通过,返还",'0','@网站管理员@',$vo['second_fee']);
			}
			$model->success_money = 0;
			
		}else if($vo['withdraw_status']<>2 && $status==2){
			// if( ($vo['all_money'] - $vo['second_fee'])<0 ){
				// memberMoneyLog($vo['uid'],29,-($vo['withdraw_money']),"提现成功,扣除实际手续费".$vo['second_fee']."元，减去冻结资金，实际到账到帐金额".(bcsub($vo['withdraw_money'],$vo['second_fee'],2)),'0','@网站管理员@',0);
				$model->success_money = $vo['withdraw_money']-$vo['second_fee'];
			// }else{
			// 	$model->success_money = $vo['withdraw_money'];
			// }
			// $rs = doCash($vo['uid'],$model->success_money,$deal_info);			
			// if($rs!=false){
			// 	$model->cfg_req_sn = $rs;					
			notice1(8,$vo['uid'], $data = array("MONEY"=>$vo['withdraw_money']-intval($_POST['withdraw_fee'])));
				memberMoneyLog($vo['uid'],29,-($vo['withdraw_money']),"提现成功,扣除实际手续费".$vo['second_fee']."元，减去冻结资金，实际到账到帐金额".(bcsub($vo['withdraw_money'],$vo['second_fee'],2)),'0','@网站管理员@');
			// }else{
			// 	$this->error(L('处理失败！'));
			// }				
			//exit;
			SMStip("withdraw",$um['user_phone'],array("#USERANEM#","#MONEY#"),array($um['user_name'],($vo['withdraw_money']-$vo['second_fee'])));
		}elseif($vo['withdraw_status']<>1 && $status==1){
			//echo 3;
			//exit;
			addInnerMsg($vo['uid'],"您的提现申请已通过","您的提现申请已通过，正在处理中");
			
			if($vo['all_money']  <=$secondfee ){
				memberMoneyLog($vo['uid'],36,-($vo['withdraw_money']-$secondfee),"提现申请已通过，扣除实际手续费".$secondfee."元，到帐金额".($vo['withdraw_money']-$secondfee)."元",'0','@网站管理员@',$vo['withdraw_fee']-$secondfee);
				$model->success_money = $vo['withdraw_money']-$secondfee;
			}else{
				memberMoneyLog($vo['uid'],36,-$vo['withdraw_money'],"提现申请已通过，扣除实际手续费".$secondfee."元，到帐金额".($vo['withdraw_money'])."元",'0','@网站管理员@',$vo['withdraw_fee']-$secondfee);
				$model->success_money = $vo['withdraw_money'];
			}
			$model->withdraw_fee = $vo['withdraw_fee'];
			$model->second_fee = $secondfee;
		}
		//////////////////////////
		$result = $model->save();
		
        if ($result) { //保存成功
          //成功提示
        	useAccountMoney($vo['uid'],$vo['withdraw_money']);
            $this->assign('jumpUrl', __URL__);
            $this->success(L('修改成功'));
        } else {
			//$this->assign("waitSecond",10000);
            //失败提示
            $this->error(L('修改失败'));
        }
    }
	
	public function _listFilter($list){
	 	$listType = C('WITHDRAW_STATUS');
		$row=array();
		
		foreach($list as $key=>$v){
			$v['withdraw_status'] = $listType[$v['withdraw_status']];
			$v['uname'] = M("members")->getFieldById($v['uid'],'user_name');
			$v['real_name'] = M("member_info")->getFieldById($v['uid'],'real_name');
			$row[$key]=$v;
		}
		return $row;
	}
}
?>
