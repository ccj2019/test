<?php 
// 全局设置
class InvestorAction extends ACommonAction
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
		
		//echo $where;

		/*投资排行*/
		//$map['realname'] = 
		$pre = C('DB_PREFIX');
		import("ORG.Util.Page");
		$count = M('borrow_investor b')->join("{$pre}borrow_info bi on b.borrow_id=bi.id")->join("{$pre}member_info m on b.investor_uid = m.uid")->join("{$pre}members ms on b.investor_uid = ms.id")->field('b.investor_capital,b.add_time,b.borrow_id,bi.borrow_status,bi.borrow_name,m.real_name,ms.user_name')->where($where)->count('b.id');
		$p = new Page($count, 30);
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		$InvestmentRanking = M('borrow_investor b')->join("{$pre}borrow_info bi on b.borrow_id=bi.id")->join("{$pre}member_info m on b.investor_uid = m.uid")->join("{$pre}members ms on b.investor_uid = ms.id")->field('b.investor_capital,b.add_time,b.borrow_id,bi.borrow_status,bi.borrow_name,m.real_name,ms.user_name')->where($where)->order("b.add_time desc")->limit($Lsql)->select();	
		
		$this->assign('search',$search);
		$this->assign("query", http_build_query($search));

		$this->assign("pagebar", $page);
		$this->assign('InvestmentRanking',$InvestmentRanking);

		/*本月排行*/
		/*
		//今日排行		
		$InvestmentRanking = M('borrow_investor b')->join("{$pre}member_info m on b.investor_uid = m.uid")->field('b.investor_capital,b.add_time,m.real_name')->where("date_format(b.add_time,'%Y-%m-%d') = curdate()")->limit(6)->select();	
		$this->assign('InvestmentRanking_now_day',$InvestmentRanking);*/

		$this->display();
	}


	public function investorlist(){
		//dump($_GET);die;
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
		}elseif($method == 'byday1'){			
			$where = " bi.borrow_type<>3 and bi.borrow_duration=1";	
		}elseif($method == 'byday2'){			
			$where = " bi.borrow_type<>3 and bi.borrow_duration=2";	
		}elseif($method == 'byday3'){			
			$where = " bi.borrow_type<>3 and bi.borrow_duration=3";	
		}elseif($method == 'byday'){
			$where = " FROM_UNIXTIME(b.add_time,'%Y-%m-%d')  = curdate()";
		}
		
		$pre = C('DB_PREFIX');
		import("ORG.Util.Page");
		$count =M('borrow_investor b')->join("{$pre}borrow_info bi on b.borrow_id=bi.id")->join("{$pre}member_info m on b.investor_uid = m.uid")->join("{$pre}members ms on b.investor_uid = ms.id")->field('b.investor_capital,b.add_time,b.borrow_id,bi.borrow_status,bi.id,bi.borrow_name,m.real_name,ms.user_name')->where($where)->count('b.id');	
		
		$p = new Page($count);
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		$list = M('borrow_investor b')->join("{$pre}borrow_info bi on b.borrow_id=bi.id")->join("{$pre}member_info m on b.investor_uid = m.uid")->join("{$pre}members ms on b.investor_uid = ms.id")->field('b.investor_capital,b.add_time,b.borrow_id,bi.borrow_status,bi.id,bi.borrow_name,m.real_name,ms.user_name')->where($where)->order("b.id desc")->limit($Lsql)->select();
		$this->assign("pagebar", $page);
		$search['method'] = $method;
		$this->assign("query", http_build_query($search));
		$this->assign('InvestmentRanking',$list);
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


		$pre = C('DB_PREFIX');
		$list = M('borrow_investor b')->join("{$pre}borrow_info bi on b.borrow_id=bi.id")->join("{$pre}member_info m on b.investor_uid = m.uid")->join("{$pre}members ms on b.investor_uid = ms.id")->field('b.investor_capital,b.add_time,b.borrow_id,bi.borrow_status,bi.id,bi.borrow_name,m.real_name,ms.user_name')->where($where)->order("b.id desc")->select();

		$row=array();
		$row[0]=array('序号','投资账户','投资人真实姓名','投资余额','投资时间','投资项目','项目编号','项目状态');
		$i=1;

		foreach($list as $v){
				/*状态*/						
				if($v['borrow_status'] == 3){
					$borrow_status = '已流标';
				}elseif($v['borrow_status'] == 4){
					$borrow_status = '等待审核';
				}elseif($v['borrow_status'] == 6){
					$borrow_status = '还款中';
				}elseif($v['borrow_status'] > 6){
					$borrow_status = '已完成';
				}else{
					$borrow_status = '投标中';
				}


				$row[$i] = array(
						'i' => $i,
						'user_name' => $v['user_name'],
						'real_name' => $v['real_name'],
						'investor_capital' => $v['investor_capital'],
						'add_time' => date("Y-m-d",$v['add_time']),
						'borrow_name' => $v['borrow_name'],
						'id' => $v['id'],
						'borrow_status' => $borrow_status
					);
				/*$row[$i]['i'] = $i;
				$row[$i]['uid'] = $v['id'];
				$row[$i]['card_num'] = $v['user_name'];
				$row[$i]['card_pass'] = $v['real_name'];
				$row[$i]['card_mianfei'] = $v['money_freeze'] + $v['account_money'] + $v['money_collect'];
				$row[$i]['card_mianfei1'] = $v['account_money'];
				$row[$i]['card_mianfei2'] = $v['money_freeze'];
				$row[$i]['card_mianfei3'] = $v['money_collect'];
				$row[$i]['card_timelimit'] = date("Y-m-d",$v['reg_time']);*/
				$i++;
		}
		
		//dump($list);die;

		$xls = new Excel_XML('UTF-8', true, 'datalist');
		$xls->addArray($row);
		//$xls->generateXML("datalistcard");
		$xls->generateXML("investorlist"."_".time());
	}
}
?>