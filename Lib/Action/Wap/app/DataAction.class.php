<?php

class DataAction extends HCommonAction {
    public function index(){

    	// 投资记录
		$method = isset($_GET['method']) ? $_GET['method'] : '';

		if($method == 'bymonth'){
			$now_month = date('Y-m',time())."-01 00:00:00";
			$now_month = strtotime($now_month);//本月一号零点
			$where = "b.add_time >".$now_month;	
		}elseif($method == 'byweek'){			
			$this_monday = date('Y-m-d',this_monday());
			$this_monday = $this_monday.' 00:00:00';
			$this_monday = strtotime($this_monday);
			$where = "b.add_time >=".$this_monday;	
		}elseif($method == 'byday'){
			$where = " FROM_UNIXTIME(b.add_time,'%Y-%m-%d')  = curdate()";
		}
		$pre = C('DB_PREFIX');
		import("ORG.Util.Page");
		$count = M('borrow_investor b')->where($where)->count('b.id');	
		
		$p = new Page($count, 10);
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		$Investlist = M('borrow_investor b')->join("{$pre}borrow_info bi on b.borrow_id=bi.id")->join("{$pre}member_info m on b.investor_uid = m.uid")->join("{$pre}members ms on b.investor_uid = ms.id")->field('b.investor_capital,b.add_time,b.borrow_id,bi.borrow_status,bi.id,bi.borrow_name,m.real_name,ms.user_name')->where($where)->order("b.add_time desc")->limit($Lsql)->select();	

		//dump($InvestmentRanking);
		$this->assign('Investlist',$Investlist);


		/*投资排行*/
		$pre = C('DB_PREFIX');
		$InvestmentRanking = M('borrow_investor b')->join("{$pre}members ms on b.investor_uid = ms.id")->join("{$pre}borrow_info as bi on bi.id = b.borrow_id ")->field('sum(b.investor_capital) as moeny,ms.user_name,b.investor_uid')->order("moeny desc")->group("b.investor_uid")->where(" bi.borrow_type in(1,5) ")->limit(30)->select();	
		$this->assign('InvestmentRanking',$InvestmentRanking);

    	$this->display();
    }
}
		
