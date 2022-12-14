<?php 
// 全局设置
class DaishouAction extends ACommonAction
{
	public function index(){

		$user_name = isset($_GET['uname']) ? $_GET['uname'] : '';
		$real_name = isset($_GET['realname']) ? $_GET['realname'] : '';
		$start_time = isset($_GET['start_time']) ? $_GET['start_time'] : '';
		$end_time = isset($_GET['end_time']) ? $_GET['end_time'] : '';

		$search['uname'] = $user_name;
		$search['realname'] = $real_name;
		$search['start_time'] = $start_time;
		$search['end_time'] = $end_time;

		$where = ' 1=1 ';
		$user_name and $where.= "and ms.user_name = '{$user_name}' ";
		$real_name and $where.= "and m.real_name = '{$real_name}' ";
		$start_time and $start_time = strtotime($start_time) and $where.= "and b.add_time >= '{$start_time}' ";
		$end_time and $end_time = strtotime($end_time) and $where.= "and b.add_time <= '{$end_time}' ";
		$where.= "and bi.borrow_type <> 3 and b.status in(1,4)";
		//echo $where;

		/*投资排行*/
		//$map['realname'] = 
		$pre = C('DB_PREFIX');
		import("ORG.Util.Page");
		$count = M('borrow_investor b')->join("{$pre}borrow_info bi on bi.id=b.borrow_id")->join("{$pre}members ms on b.investor_uid = ms.id")->field('sum(b.investor_capital) as moeny,ms.user_name')->where($where)->order("moeny desc")->group("b.investor_uid")->count('b.id');
		$p = new Page($count, 100);
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		$InvestmentRanking = M('borrow_investor b')->join("{$pre}borrow_info bi on bi.id=b.borrow_id")->join("{$pre}members ms on b.investor_uid = ms.id")->field('sum(b.investor_capital) as moeny,ms.user_name,b.investor_uid')->where($where)->order("moeny desc")->group("b.investor_uid")->limit($Lsql)->select();	
	//	$list = M('borrow_investor b')->join("{$pre}members ms on b.investor_uid = ms.id")->field('sum(b.investor_capital) as moeny,ms.user_name')->where($where)->order("moeny desc")->group("b.investor_uid")->limit($Lsql)->select();	
		
		
		foreach($InvestmentRanking as $key=>$val){
			$vx=array();
			$vx = M('member_info')->where("uid=".$val["investor_uid"])->find();
			$val["real_name"]=$vx["real_name"];
			$lists[]=$val;
		}
		
		$this->assign('search',$search);
		$this->assign("query", http_build_query($search));

		$this->assign("pagebar", $page);
		$this->assign('InvestmentRanking',$lists);

		/*本月排行*/
		/*
		//今日排行		
		$InvestmentRanking = M('borrow_investor b')->join("{$pre}member_info m on b.investor_uid = m.uid")->field('b.investor_capital,b.add_time,m.real_name')->where("date_format(b.add_time,'%Y-%m-%d') = curdate()")->limit(6)->select();	
		$this->assign('InvestmentRanking_now_day',$InvestmentRanking);*/

		$this->display();
	}

	public function export(){
		import("ORG.Io.Excel");
		$map=array();
		$user_name = isset($_GET['uname']) ? urldecode($_GET['uname']) : '';
		$real_name = isset($_GET['realname']) ? urldecode($_GET['realname']) : '';
		$start_time = isset($_GET['start_time']) ? urldecode($_GET['start_time']) : '';
		$end_time = isset($_GET['end_time']) ? urldecode($_GET['end_time']) : '';
		$method = isset($_GET['method']) ? urldecode($_GET['method']) : '';

		$where = ' 1=1 ';
		if(in_array($method, array('bymonth','byday','byweek'))){
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
		}else{			
			$user_name and $where.= "and ms.user_name = '{$user_name}' ";
			$real_name and $where.= "and m.real_name = '{$real_name}' ";
			$start_time and $start_time = strtotime($start_time) and $where.= "and b.add_time >= '{$start_time}' ";
			$end_time and $end_time = strtotime($end_time) and $where.= "and b.add_time <= '{$end_time}' ";
		}
        $where.= " and bi.borrow_type <> 3  and b.status in(1,4) ";

		$pre = C('DB_PREFIX');
		//$list = M('borrow_investor b')->join("{$pre}members ms on b.investor_uid = ms.id")->field('sum(b.investor_capital) as money,ms.user_name')->where($where)->order("b.add_time desc")->group("b.investor_uid")->select();
		$list = M('borrow_investor b')->join("{$pre}borrow_info bi on bi.id=b.borrow_id")->join("{$pre}members ms on b.investor_uid = ms.id")->field('sum(b.investor_capital) as moeny,ms.user_name,b.investor_uid')->where($where)->order("moeny desc")->group("b.investor_uid")->select();		
		
		$row=array();
		$row[0]=array('序号','投资账户','投资人真实姓名','投资金额');
		$i=1;

		foreach($list as $v){
			$vx=array();
			$vx = M('member_info')->where("uid=".$v["investor_uid"])->find();
				$row[$i] = array(
						'i' => $i,
						'user_name' => $v['user_name'],
						'real_name' => $vx["real_name"],
						'investor_capital' => $v['moeny'],
					);
				$i++;
		}
		

		$xls = new Excel_XML('UTF-8', true, 'datalist');
		$xls->addArray($row);
		//$xls->generateXML("datalistcard");
		$xls->generateXML("rankinglist"."_".time());
	}

}
?>