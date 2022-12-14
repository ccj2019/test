<?php 
// 全局设置
class RankingAction extends ACommonAction
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
		$where.= "and bi.borrow_type <> 3 ";
		//echo $where;

		/*投资排行*/
		//$map['realname'] = 
		$pre = C('DB_PREFIX');
		import("ORG.Util.Page");
		$count = M('borrow_investor b')->join("{$pre}borrow_info bi on bi.id=b.borrow_id")->join("{$pre}members ms on b.investor_uid = ms.id")->field('sum(b.investor_capital) as moeny,ms.user_name')->where($where)->order("moeny desc")->group("b.investor_uid")->count('b.id');
		$p = new Page($count, 30);
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


	public function ranklist(){
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
		}elseif($method == 'byday'){
			$where = " FROM_UNIXTIME(b.add_time,'%Y-%m-%d')  = curdate()";
		}
		$where.= " and bi.borrow_type <> 3 ";
		//echo $where;
		
		$pre = C('DB_PREFIX');
		import("ORG.Util.Page");
		$count = M('borrow_investor b')->join("{$pre}borrow_info bi on bi.id=b.borrow_id")->join("{$pre}members ms on b.investor_uid = ms.id")->field('sum(b.investor_capital) as moeny,ms.user_name,b.investor_uid')->where($where)->order("moeny desc")->group("b.investor_uid")->count('b.id');		
		$p = new Page($count, 100);
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		$list = M('borrow_investor b')->join("{$pre}borrow_info bi on bi.id=b.borrow_id")->join("{$pre}members ms on b.investor_uid = ms.id")->field('sum(b.investor_capital) as moeny,ms.user_name,b.investor_uid')->where($where)->order("moeny desc")->group("b.investor_uid")->limit($Lsql)->select();	
		foreach($list as $key=>$val){
			$vx=array();
			$vx = M('member_info')->where("uid=".$val["investor_uid"])->find();
			$val["real_name"]=$vx["real_name"];
			$lists[]=$val;
		}
		$this->assign("pagebar", $page);
		$search['method'] = $method;
		$this->assign("query", http_build_query($search));
		$this->assign('InvestmentRanking',$lists);
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
$where.= " and bi.borrow_type <> 3 ";

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
		$xls->generateXML("rankinglist"."_".time());
	}

	public function rankingcount(){

		$data = M('borrow_info')->select();
		$column = array_values(array_column($data,'pid'));
		$unique = array_unique($column);

		$typelist = get_type_leve_list('0','Productcategory');//分级栏目
		$pid=array();
		foreach($typelist as $key =>$val){
			$pid[$val["id"]]=$val["type_name"];
		}
		// var_dump($pid);exit;
		foreach($unique as $item=>$v){
			$list[$v]['pid'] = $pid[$v];
			$list[$v]['borrow_money'] = M('borrow_info')->where(array('pid'=>$v))->sum('has_borrow');
			// $list[$v]['borrow_fee'] = M('borrow_info')->where(array('pid'=>$v))->sum('borrow_fee');
		}

		$last_names = array_column($list,'borrow_money');
		array_multisort($last_names,SORT_DESC,$list);

		$this->assign('list',$list);
		$this->display();
	}

	public function rankingcountexport(){
		import("ORG.Io.Excel");
		$data = M('borrow_info')->select();
		$column = array_values(array_column($data,'pid'));
		$unique = array_unique($column);

		$typelist = get_type_leve_list('0','Productcategory');//分级栏目
		$pid=array();
		foreach($typelist as $key =>$val){
			$pid[$val["id"]]=$val["type_name"];
		}
		// var_dump($pid);exit;
		foreach($unique as $item=>$v){
			$list[$v]['pid'] = $pid[$v];
			$list[$v]['borrow_money'] = M('borrow_info')->where(array('pid'=>$v))->sum('has_borrow');
			// $list[$v]['borrow_fee'] = M('borrow_info')->where(array('pid'=>$v))->sum('borrow_fee');
		}

		$last_names = array_column($list,'borrow_money');
		array_multisort($last_names,SORT_DESC,$list);

		$row=array();
		$row[0]=array('排名','投资类型','投资金额');
		$i=1;

		foreach($list as $v){
			// $vx=array();
			// $vx = M('member_info')->where("uid=".$v["investor_uid"])->find();
				$row[$i] = array(
						'i' => $i,
						'pid' => $v['pid'],
						'borrow_money' => $v["borrow_money"],
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
		$xls->generateXML("rankinglist"."_".time());
	}


	public function borrowcount(){
		$map = array();

		import("ORG.Util.Page");

		$count = M('borrow_info b')->join("{$this->pre}members m ON m.id=b.borrow_uid")->where($map)->count('b.id');

		$p = new Page($count, C('ADMIN_PAGE_SIZE'));

		$page = $p->show();

		$Lsql = "{$p->firstRow},{$p->listRows}";

		//分页处理

		$field= 'b.id as id,b.has_borrow,b.borrow_name,b.borrow_uid,b.borrow_duration,b.borrow_type,b.updata,b.borrow_money,b.borrow_fee,b.borrow_interest_rate,b.repayment_type,b.borrow_status,b.add_time,m.user_name,b.lead_time,m.id mid,b.is_tuijian,b.pid';

		$list = M('borrow_info b')->field($field)->join("{$this->pre}members m ON m.id=b.borrow_uid")->where($map)->limit($Lsql)->order("b.id DESC")->select();

		// $list = $this->_listFilter($list);
		
		foreach($list as $k=>$v){
			// 众筹时间，发放时间，上标金额，预计收益，使用优惠券和奖励金额
			$list[$k]['add_time'] = $v['add_time'];//众筹时间
			$list[$k]['lead_time'] = $v['lead_time'];//发放时间
			$list[$k]['has_borrow'] = $v['has_borrow'];//上标金额
			$list[$k]['yuji'] = $v['borrow_money']*$v['borrow_interest_rate']/100;//预计收益
			$bu = array_values(array_column(M('borrow_investor')->where(array('borrow_id'=>$v['id']))->select(),'bonus_id'));//bonus_id
			$bonus = M('member_bonus')->where(array('id'=>array('in',$bu)))->sum('money_bonus');
			$list[$k]['youhuiquan'] = $bonus;
			$list[$k]['has_borrow'] = $v['has_borrow'];
			$yubi = M('borrow_investor')->where(array('borrow_id'=>$v['id']))->sum('yubi');
			$list[$k]['yue'] = $v['has_borrow']-$bonus-$yubi;
		}
		// var_dump($list);exit;
        $this->assign("list", $list);

        $this->assign("pagebar", $page);

		$this->display();
	}
}
?>