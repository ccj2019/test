<?php

// 全局设置

class CapitalOnlineAction extends ACommonAction

{

    /**

    +----------------------------------------------------------

    * 默认操作

    +----------------------------------------------------------

    */

    public function charge()

    {

		$map=array();

		if($_REQUEST['uid'] && $_REQUEST['uname']){

			$map['p.uid'] = $_REQUEST['uid'];

			$search['uid'] = $map['p.uid'];	

			$search['uname'] = urldecode($_REQUEST['uname']);	

		}

		

		if($_REQUEST['uname'] && !$search['uid']){

			$map['m.user_name'] = array("like",urldecode($_REQUEST['uname'])."%");

			$search['uname'] = urldecode($_REQUEST['uname']);	

		}

		if($_REQUEST['rname'] && !$search['uid']){
			$map['n.real_name'] = array("like",urldecode($_REQUEST['rname'])."%");
			$map['n.real_name'] = urldecode($_REQUEST['rname']);	
			$search['rname'] = urldecode($_REQUEST['rname']);	
		}

		if($_REQUEST['tran_id']){

			$map['p.tran_id'] = urldecode($_REQUEST['realname']);

			$search['tran_id'] = $map['p.tran_id'];	

		}

		

		if(isset($_REQUEST['status']) && $_REQUEST['status']!=""){

			$map['p.status'] = intval($_REQUEST['status']);

			$search['status'] = $map['p.status'];	

		}

		

		if($_REQUEST['way']){

			$map['p.way'] = $_REQUEST['way'];

			$search['way'] = $map['p.way'];	

		}

		

		if(!empty($_REQUEST['bj']) && !empty($_REQUEST['money'])){

			$map['p.money'] = array($_REQUEST['bj'],$_REQUEST['money']);

			$search['bj'] = $_REQUEST['bj'];	

			$search['money'] = $_REQUEST['money'];	

		}



		if(!empty($_REQUEST['start_time']) && !empty($_REQUEST['end_time'])){

			$timespan = strtotime(urldecode($_REQUEST['start_time'])).",".strtotime(urldecode($_REQUEST['end_time']));

			$map['p.add_time'] = array("between",$timespan);

			$search['start_time'] = urldecode($_REQUEST['start_time']);	

			$search['end_time'] = urldecode($_REQUEST['end_time']);	

		}elseif(!empty($_REQUEST['start_time'])){

			$xtime = strtotime(urldecode($_REQUEST['start_time']));

			$map['p.add_time'] = array("gt",$xtime);

			$search['start_time'] = $xtime;	

		}elseif(!empty($_REQUEST['end_time'])){

			$xtime = strtotime(urldecode($_REQUEST['end_time']));

			$map['p.add_time'] = array("lt",$xtime);

			$search['end_time'] = $xtime;	

		}

		if(!empty($_REQUEST['off_bank'])){			

				$map['p.off_bank'] = $_REQUEST['off_bank'];	

				$search['off_bank'] = $_REQUEST['off_bank'];

		}



		$off_bank = M('member_payonline')->field('DISTINCT off_bank')->where("off_bank !='' ")->select();

	 	$off_bank = array_filter($off_bank); 	

	 	$this->assign('off_bank',$off_bank);



		if(session('admin_is_kf')==1)	$map['m.customer_id'] = session('admin_id');



		

		//分页处理

		import("ORG.Util.Page");

		$count = M('member_payonline p')->join("{$this->pre}members m ON p.uid=m.id")->join("{$this->pre}member_info n ON n.uid=p.uid")->where($map)->count('p.id');

		$p = new Page($count, C('ADMIN_PAGE_SIZE'));

		$page = $p->show();

		$Lsql = "{$p->firstRow},{$p->listRows}";

		//分页处理

		

		$field= 'p.*,m.user_name,n.real_name';

		$list = M('member_payonline p')->field($field)->join("{$this->pre}members m ON p.uid=m.id")->join("{$this->pre}member_info n ON n.uid=p.uid")->where($map)->limit($Lsql)->order("p.id DESC")->select();

		

        $this->assign("way", C('ZF_WAY'));

        $this->assign("bj", array("gt"=>'大于',"eq"=>'等于',"lt"=>'小于'));

        $this->assign("list", $list);

		$this->assign("status",C('PAYLOG_TYPE'));

        $this->assign("pagebar", $page);

        $this->assign("search", $search);

        $this->assign("query", http_build_query($search));

		

        $this->display();

    }

	public function chargeexport(){

		import("ORG.Io.Excel");



		$map=array();

		if($_REQUEST['uid'] && $_REQUEST['uname']){

			$map['p.uid'] = $_REQUEST['uid'];

			$search['uid'] = $map['p.uid'];	

			$search['uname'] = urldecode($_REQUEST['uname']);	

		}

		

		if($_REQUEST['uname'] && !$search['uid']){

			$map['m.user_name'] = array("like",urldecode($_REQUEST['uname'])."%");

			$search['uname'] = urldecode($_REQUEST['uname']);	

		}

		

		if($_REQUEST['tran_id']){

			$map['p.tran_id'] = urldecode($_REQUEST['realname']);

			$search['tran_id'] = $map['p.tran_id'];	

		}

		

		if(isset($_REQUEST['status']) && $_REQUEST['status']!=""){

			$map['p.status'] = intval($_REQUEST['status']);

			$search['status'] = $map['p.status'];	

		}

		

		if($_REQUEST['way']){

			$map['p.way'] = $_REQUEST['way'];

			$search['way'] = $map['p.way'];	

		}

		

		if(!empty($_REQUEST['bj']) && !empty($_REQUEST['money'])){

			$map['p.money'] = array($_REQUEST['bj'],$_REQUEST['money']);

			$search['bj'] = $_REQUEST['bj'];	

			$search['money'] = $_REQUEST['money'];	

		}



		if(!empty($_REQUEST['start_time']) && !empty($_REQUEST['end_time'])){

			$timespan = strtotime(urldecode($_REQUEST['start_time'])).",".strtotime(urldecode($_REQUEST['end_time']));

			$map['p.add_time'] = array("between",$timespan);

			$search['start_time'] = urldecode($_REQUEST['start_time']);	

			$search['end_time'] = urldecode($_REQUEST['end_time']);	

		}elseif(!empty($_REQUEST['start_time'])){

			$xtime = strtotime(urldecode($_REQUEST['start_time']));

			$map['p.add_time'] = array("gt",$xtime);

			$search['start_time'] = $xtime;	

		}elseif(!empty($_REQUEST['end_time'])){

			$xtime = strtotime(urldecode($_REQUEST['end_time']));

			$map['p.add_time'] = array("lt",$xtime);

			$search['end_time'] = $xtime;	

		}

		if(!empty($_REQUEST['off_bank'])){			

				$map['p.off_bank'] = $_REQUEST['off_bank'];	

				$search['off_bank'] = $_REQUEST['off_bank'];

		}

		if(session('admin_is_kf')==1)	$map['m.customer_id'] = session('admin_id');



		$field= 'p.*,m.user_name';

		$list = M('member_payonline p')->field($field)->join("{$this->pre}members m ON p.uid=m.id")->where($map)->limit($Lsql)->select();



		$status = C('PAYLOG_TYPE');

		$row=array();

		$row[0]=array('序号','用户ID','用户名','充值金额','充值手续费','充值状态','对账订单号','充值方式','充值时间');

		$i=1;

		foreach($list as $v){

				$row[$i]['i'] = $i;

				$row[$i]['uid'] = $v['id'];

				$row[$i]['card_1'] = $v['user_name'];

				$row[$i]['card_2'] = $v['money'];

				$row[$i]['card_3'] = $v['fee'];

				$row[$i]['card_4'] = $status[$v['status']];

				$row[$i]['card_5'] = $v['tran_id'];

				$row[$i]['card_6'] = $v['way'];

				$row[$i]['card_7'] = date("Y-m-d H:i:s",$v['add_time']);

				$i++;

		}

		

		$xls = new Excel_XML('UTF-8', true, 'datalist');

		$xls->addArray($row);

		$xls->generateXML("datalistcard");

	}



     public function withdraw()

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
			$map['n.real_name'] = array("like",urldecode($_REQUEST['rname'])."%");
			$map['n.real_name'] = urldecode($_REQUEST['rname']);	
			$search['rname'] = urldecode($_REQUEST['rname']);	
		}
		

		if(isset($_REQUEST['status']) && $_REQUEST['status']!=""){

			$map['w.withdraw_status'] = intval($_REQUEST['status']);

			$search['status'] = $map['w.withdraw_status'];	

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

		$count = M('member_withdraw w')->join("{$this->pre}members m ON w.uid=m.id")->join("{$this->pre}member_info n ON n.uid=w.uid")->where($map)->count('w.id');

		$p = new Page($count, C('ADMIN_PAGE_SIZE'));

		$page = $p->show();

		$Lsql = "{$p->firstRow},{$p->listRows}";

		//分页处理

		

		$field= 'w.*,m.user_name,mm.account_money,n.real_name';

		$list = M('member_withdraw w')->field($field)->join("{$this->pre}members m ON w.uid=m.id")->join("lzh_member_money mm on w.uid = mm.uid")->join("{$this->pre}member_info n ON n.uid=w.uid")->where($map)->limit($Lsql)->order("w.id DESC")->select();

		

        $this->assign("bj", array("gt"=>'大于',"eq"=>'等于',"lt"=>'小于'));

        $this->assign("list", $list);

		$this->assign("status",C('WITHDRAW_STATUS'));

        $this->assign("pagebar", $page);

        $this->assign("search", $search);

        $this->assign("query", http_build_query($search));

		

        $this->display();

    }



	

	public function withdrawexport(){

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

		

		if(isset($_REQUEST['status']) && $_REQUEST['status']!=""){

			$map['w.withdraw_status'] = intval($_REQUEST['status']);

			$search['status'] = $map['w.withdraw_status'];	

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



		$field= 'w.*,m.user_name,mm.account_money';

		$list = M('member_withdraw w')->field($field)->join("{$this->pre}members m ON w.uid=m.id")->join("lzh_member_money mm on w.uid = mm.uid")->where($map)->limit($Lsql)->order("w.id DESC")->select();



		$status = C('WITHDRAW_STATUS');

		$row=array();

		$row[0]=array('序号','用户ID','用户名','提现金额','提现手续费','提现状态','提现时间','处理时间','处理人');

		$i=1;

		foreach($list as $v){

				$row[$i]['i'] = $i;

				$row[$i]['uid'] = $v['id'];

				$row[$i]['card_1'] = $v['user_name'];

				$row[$i]['card_2'] = $v['withdraw_money'];

				$row[$i]['card_3'] = $v['second_fee'];

				//$row[$i]['card_8'] =($v['withdraw_status']==3)?0:(($v['account_money']>$v['second_fee'])?$v['withdraw_money']:$v['withdraw_money']-$v['second_fee']);

				$row[$i]['card_4'] = $status[$v['withdraw_status']];

				$row[$i]['card_5'] = date("Y-m-d H:i:s",$v['add_time']);

				$row[$i]['card_6'] = ($v['deal_time']>0)?date("Y-m-d H:i:s",$v['deal_time']):"未处理";

				$row[$i]['card_7'] = (!empty($v['deal_user']))?$v['deal_user']:'';

				$i++;

		}

		

		$xls = new Excel_XML('UTF-8', true, 'datalist');

		$xls->addArray($row);

		$xls->generateXML("datalistcard");

	}

	

}

?>