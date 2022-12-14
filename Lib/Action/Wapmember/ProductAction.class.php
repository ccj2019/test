<?php
// 本类由系统自动生成，仅供测试用途
class ProductAction extends MCommonAction {

    public function index(){
		$this->display();
    }

    public function summary(){
		$uid = $this->uid;
		$pre = C('DB_PREFIX');
		
		$this->assign("dc",M('pro_investor_detail')->where("investor_uid = {$this->uid}")->sum('substitute_money'));
		$this->assign("mx",getMemberProductScan($this->uid));
		$data['html'] = $this->fetch();
		exit(json_encode($data));
    }
	
	public function tending(){
		//$map['i.investor_uid'] = $this->uid;
//		$map['i.status'] = 1;
		$map['investor_uid'] = $this->uid;
		$map['status'] = 1;
		
		$list = getTenderList($map,15);
		$this->assign("list",$list['list']);
		$this->assign("pagebar",$list['page']);
		$this->assign("total",$list['total_money']);
		$this->assign("num",$list['total_num']);
		$data['html'] = $this->fetch();
		//$this->display("Public:_footer");
		exit(json_encode($data));
	}

	public function tendbacking(){
		//$map['i.investor_uid'] = $this->uid;
//		$map['i.status'] = 4;
		$pre = C('DB_PREFIX');
		$map['investor_uid'] = $this->uid;
		
		if($_GET['start_time2']&&$_GET['end_time2']){
			$_GET['start_time2'] = strtotime($_GET['start_time2']." 00:00:00");
			$_GET['end_time2'] = strtotime($_GET['end_time2']." 23:59:59");
			
			if($_GET['start_time2']<$_GET['end_time2']){
				$map['add_time']=array("between","{$_GET['start_time2']},{$_GET['end_time2']}");
				$search['start_time2'] = $_GET['start_time2'];
				$search['end_time2'] = $_GET['end_time2'];
			}
		}

		$map['status'] = 6;
		$list = getTenderList_pro($map,15);
		
		// echo M("pro_borrow_investor t1")->getlastsql();
		// dump($list);die;
		
		$this->assign("list",$list['list']);
		$this->assign("pagebar",$list['page']);
		$this->assign("total",$list['total_money']);
		$this->assign("alltotal",$list['total_money']+$list['lixi']);
		$this->assign("num",$list['total_num']);
		//$this->display("Public:_footer");

		$data['html'] = $this->fetch();
		exit(json_encode($data));
	}


	public function tenddone(){
		//$map['i.investor_uid'] = $this->uid;
//		$map['i.status'] = array("in","5,6");
		$map['investor_uid'] = $this->uid;
		$map['status'] = array("in","7");

		$list = getTenderList_pro($map,15);
		$this->assign("list",$list['list']);
		$this->assign("pagebar",$list['page']);
		$this->assign("total",$list['total_money']);
		$this->assign("num",$list['total_num']);
		//$this->display("Public:_footer");

		$data['html'] = $this->fetch();
		exit(json_encode($data));
	}

	public function tendbreak(){
		$map['d.status'] = array('neq',0);
		$map['d.repayment_time'] = array('eq',"0");
		$map['d.deadline'] = array('lt',time());
		$map['d.investor_uid'] = $this->uid;
		
		$list = getMBreakInvestList($map,15);
		$this->assign("list",$list['list']);
		$this->assign("pagebar",$list['page']);
		$this->assign("total",$list['total_money']);
		$this->assign("num",$list['total_num']);
		//$this->display("Public:_footer");
	
		$data['html'] = $this->fetch();
		exit(json_encode($data));
	}

    public function tendoutdetail(){
		$pre = C('DB_PREFIX');
		$status_arr =array('还未还','已还完','已提前还款','迟还','网站代还本金','逾期还款','','等待还款');
		$investor_id = intval($_GET['id']);
		$this->assign('id',$investor_id);
		$vo = M("pro_borrow_investor i")->field("b.borrow_name")->join("{$pre}pro_borrow_info b ON b.id=i.borrow_id")->where("i.investor_uid={$this->uid} AND i.id={$investor_id}")->find();
		if(!is_array($vo)) $this->error("数据有误");
		$map['invest_id'] = $investor_id;
		$list = M('pro_investor_detail')->field(true)->where($map)->select();
		
		$this->assign("status_arr",$status_arr);
		$this->assign("list",$list);
		$this->assign("name",$vo['borrow_name'].$investor_id);
		$data['html'] = $this->fetch();
		exit(json_encode($data));
    }


}