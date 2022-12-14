<?php

// 全局设置

class WithdrawlogwaitAction extends ACommonAction

{

    /**

    +----------------------------------------------------------

    * 默认操作 待审核提现

    +----------------------------------------------------------

    */

	public function index()

    {$map=array();

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

	

		//分页处理

		import("ORG.Util.Page");

		$map['w.withdraw_status'] =0;

		$count = M('member_withdraw w')->join("{$this->pre}members m ON m.id=w.uid ")->join("lzh_member_info mi ON w.uid=mi.uid")->where($map)->count('w.id');

		$all_money = M('member_withdraw w')->join("{$this->pre}members m ON w.uid=m.id")->join("lzh_member_info mi ON w.uid=mi.uid")->where($map)->sum("w.withdraw_money");

		$this->assign("all_money", $all_money);

		$p = new Page($count, C('ADMIN_PAGE_SIZE'));

		$page = $p->show();

		$Lsql = "{$p->firstRow},{$p->listRows}";

		//分页处理



		//$field= 'w.*,m.user_name,mi.real_name,w.id,w.uid ';

//		$list = M('member_withdraw w')->field($field)->join("{$this->pre}members m ON w.uid=m.id")->join("{$this->pre}member_info mi ON w.uid=mi.uid")->where($map)->order(' w.id ASC ')->limit($Lsql)->select();

		$field= 'm.user_name,mi.real_name,w.id,w.uid,(mm.account_money+mm.back_money) all_money,mi.real_name,w.*';

		$list = M('member_withdraw w')->field($field)->join("lzh_members m ON w.uid=m.id")->join("lzh_member_info mi ON w.uid=mi.uid")->join("lzh_member_money mm on w.uid = mm.uid")->where($map)->order(' w.id DESC ')->limit($Lsql)->select();

	//	var_dump($list);die;
		$listType = C('WITHDRAW_STATUS');

		unset($listType[1],$listType[2],$listType[3]);

		$this->assign("bj", array("gt"=>'大于',"eq"=>'等于',"lt"=>'小于'));

        $this->assign("list", $list);

		$this->assign("status",$listType);

        $this->assign("pagebar", $page);

        $this->assign("search", $search);

        $this->assign("query", http_build_query($search));

		

        $this->display();

    }

	//编辑

    public function edit() {

        $model = M('member_withdraw');

        $id = intval($_REQUEST['id']);

        $vo = $model->find($id);

		//$vo['uname'] = M("members")->getFieldById($vo['uid'],'user_name');

	 	$listType = C('WITHDRAW_STATUS');

		unset($listType[0],$listType[2]);

		$this->assign('type_list',$listType);

        $vo['uname']=M("member_banks")->where("id = 1")->getFieldByUid($vo['uid'],'realname');

    	$vo['real_name'] = M("member_banks")->where("is_default = 1")->getFieldByUid($vo['uid'],'realname');

		$vo['bank_num'] = M("member_banks")->where("is_default = 1")->getFieldByUid($vo['uid'],'bank_num');

		$vo['bank_province'] = M("member_banks")->where("is_default = 1")->getFieldByUid($vo['uid'],'bank_province');

		$vo['bank_city'] = M("member_banks")->where("is_default = 1")->getFieldByUid($vo['uid'],'bank_city');

		$vo['bank_address'] = M("member_banks")->where("is_default = 1")->getFieldByUid($vo['uid'],'bank_address');

		$vo['bank_name'] = M("member_banks")->where("is_default = 1")->getFieldByUid($vo['uid'],'bank_name');

		/*	*/

	 	 /////////////////////////////////
//		var_dump($vo); 
//var_dump($listType);die;
	 	$field= 'w.*,m.user_name,mi.real_name,w.id,w.uid,(mm.account_money+mm.back_money) all_money';
		if(empty($vo["bank_num"])){
			$field .=",mb.bank_num,mb.bank_province,mb.bank_city,mb.bank_address,mb.bank_name";
		}
	
//mb.bank_num,mb.bank_province,mb.bank_city,mb.bank_address,mb.bank_name
//		$list = M('member_withdraw w')->field($field)->join("lzh_members m ON w.uid=m.id")->join("lzh_member_info mi ON w.uid=mi.uid")->join("lzh_member_money mm on w.uid = mm.uid")->join("lzh_member_banks mb on w.uid = mb.uid")->where("w.id=$id")->order(' w.id ASC ')->limit($Lsql)->select();
$list = M('member_withdraw w')->field($field)->join("lzh_members m ON w.uid=m.id")->join("lzh_member_info mi ON w.uid=mi.uid")->join("lzh_member_money mm on w.uid = mm.uid");
if(empty($vo["bank_num"])){
	$list =$list->join("lzh_member_banks mb on w.uid = mb.uid");
}

$list =$list->where("w.id=$id")->order(' w.id ASC ')->limit($Lsql)->select();
//var_dump($list);die;

//   var_dump($list,$list[0]['bank_id'],M('member_withdraw w')->getlastsql());die;
$vzo=M("member_banks")->where("id =  ".$list[0]['bank_id'])->find();
// var_dump($list);
//   var_dump($vo) ;die;
		foreach($list as $v){

// 			$vo['uname'] =$v['user_name'];

// 			$vo['real_name'] = $v['real_name'];

// 			$vo['bank_num'] =$v['bank_num'];

// 			$vo['bank_province'] = $v['bank_province'];

// 			$vo['bank_city'] =$v['bank_city'];

// 			$vo['bank_address'] = $v['bank_address'];

// 			$vo['bank_name'] =$v['bank_name'];
	   $za=$v;
		$vzo['all_money']=	$vo['all_money'] =$v['all_money'];

		$vzo['withdraw_fee']=	$vo['withdraw_fee'] =$v['withdraw_fee'];

		}
 
	 //////////////////////////////////////
// aa

	$vzo['real_name']=	$vzo['realname'];
	$vzo['uname']=		$vo['uname'];
	$vzo["id"]= $za['id'];
	$vzo=array_merge($vzo,$za);
		 if(isset($vzo['bank_num'])&&!empty($vzo['bank_num'])){
	    $save['uid']	 =$vzo['uid'];
        // $save['bank_id'] =$vzo['bank_id'];
        $save['bank_num'] =$vzo['bank_num'];
        $save['bank_name']  =$vzo['bank_name'];
        $save['bank_address']  =$vo['bank_province'].$vo['bank_city'].$vo['bank_address'];
       
        M('member_withdraw')->where("id =" .$za['id'])->save($save); 
	 }
        $this->assign('vo', $vzo);

        $this->display();

    }



	 public function doEdit() {

        $model = D("member_withdraw");

		$status = intval($_POST['withdraw_status']);

		$id = intval($_POST['id']);

		$deal_info = $_POST['deal_info'];

		$secondfee = floatval($_POST['second_fee']);

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

			// memberMoneyLog($vo['uid'],12,$vo['withdraw_money']+$vo['withdraw_fee'],"提现未通过,返还",'0','@网站管理员@');

		}else if($vo['withdraw_status']<>2 && $status==2){

			addInnerMsg($vo['uid'],"您的提现已完成","您的提现已完成");

			if( ($vo['all_money'] - $vo['second_fee'])<0 ){

				memberMoneyLog($vo['uid'],29,-($vo['withdraw_money']-$vo['second_fee']),"提现成功,扣除实际手续费".$vo['second_fee']."元，减去冻结资金，到帐金额".($vo['withdraw_money']-$vo['second_fee']),'0','@网站管理员@',0);

				//$model->withdraw_money = $vo['withdraw_money']+$vo['withdraw_fee'];

				$model->success_money = $vo['withdraw_money']-$vo['second_fee'];

			}else{

				memberMoneyLog($vo['uid'],29,-($vo['withdraw_money']),"提现成功,扣除实际手续费".$vo['second_fee']."元，减去冻结资金，到帐金额".($vo['withdraw_money']),'0','@网站管理员@');

				$model->success_money = $vo['withdraw_money'];

			}

				

			SMStip("withdraw",$um['user_phone'],array("#USERANEM#","#MONEY#"),array($um['user_name'],($vo['withdraw_money']-$vo['second_fee'])));

			

		}elseif($vo['withdraw_status']<>1 && $status==1){

			

			

			//addInnerMsg($vo['uid'],"您的提现申请已通过","您的提现申请已通过，正在处理中");

			

//			if($vo['all_money']  <=$secondfee ){

//				memberMoneyLog($vo['uid'],36,0,"提现申请已通过，扣除实际手续费".$secondfee."元，到帐金额".($vo['withdraw_money']-$secondfee)."元",'0','@网站管理员@',$vo['withdraw_fee']-$secondfee);

//				$model->success_money = $vo['withdraw_money']-$secondfee;

//				

//			}else{

//				

			memberMoneyLog($vo['uid'],36,0,"提现申请已通过，扣除实际手续费".$secondfee."元，到帐金额".($vo['withdraw_money'])."元",'0','@网站管理员@',$secondfee);

				//print_r($vo);

			//echo $vo['withdraw_fee'];

			//exit;

				

			$model->success_money = $vo['withdraw_money'];

			//}

			$model->withdraw_fee = $vo['withdraw_fee'];

			$model->second_fee = $secondfee;

		}

		

		

		

		//////////////////////////

		$result = $model->save();

		

        if ($result) { //保存成功

          //成功提示

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