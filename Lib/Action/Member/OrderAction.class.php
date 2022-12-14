<?php
// 本类由系统自动生成，仅供测试用途
class OrderAction extends MCommonAction {

    public function index(){		//分页处理		import("ORG.Util.Page");		$count = M('order b')->join("{$this->pre}members m ON m.id=b.uid")->join("{$this->pre}market x ON x.id=b.gid")->where($map)->count('b.id');		$p = new Page($count, C('ADMIN_PAGE_SIZE'));		$page = $p->show();		$Lsql = "{$p->firstRow},{$p->listRows}";		//分页处理		$field= 'b.id,b.uid,b.gid,b.jifen,b.jine,b.num,b.add_time,b.add_ip,b.action,m.id mid,m.user_name,x.title';		$list = M('order b')->field($field)->join("{$this->pre}members m ON m.id=b.uid")->join("{$this->pre}market x ON x.id=b.gid")->where($map)->limit($Lsql)->order("b.id DESC")->select();		$list = $this->_listFilter($list);		        $this->assign("bj", array("gt"=>'大于',"eq"=>'等于',"lt"=>'小于'));        $this->assign("list", $list);        $this->assign("pagebar", $page);        $this->assign("search", $search);		$this->assign("xaction",ACTION_NAME);        $this->assign("query", http_build_query($search));		 var_dump($list);die;		$this->display();
    }

    public function detail(){
		$logtype = C('MONEY_LOG');
		$this->assign('log_type',$logtype);

		$map['uid'] = $this->uid;
		if($_GET['start_time']&&$_GET['end_time']){
			$_GET['start_time'] = strtotime($_GET['start_time']." 00:00:00");
			$_GET['end_time'] = strtotime($_GET['end_time']." 23:59:59");
			
			if($_GET['start_time']<$_GET['end_time']){
				$map['add_time']=array("between","{$_GET['start_time']},{$_GET['end_time']}");
				$search['start_time'] = $_GET['start_time'];
				$search['end_time'] = $_GET['end_time'];
			}
		}
		if(!empty($_GET['log_type'])){
				$map['type'] = intval($_GET['log_type']);
				$search['log_type'] = intval($_GET['log_type']);
		}

		$list = getOrderList($map,15);

		$this->assign('search',$search);
		$this->assign("list",$list['list']);		
		$this->assign("pagebar",$list['page']);	
        $this->assign("query", http_build_query($search));
		$data['html'] = $this->fetch();
		exit(json_encode($data));
    }
	
	public function export(){
		import("ORG.Io.Excel");

		$map=array();
		$map['uid'] = $this->uid;

		$list = getMoneyLog($map,100000);
		
		$logtype = C('MONEY_LOG');
		$row=array();
		$row[0]=array('序号','发生日期','类型','影响金额','可用余额','冻结金额','待收金额','说明');
		$i=1;
		foreach($list['list'] as $v){
				$row[$i]['i'] = $i;
				$row[$i]['uid'] = date("Y-m-d H:i:s",$v['add_time']);
				$row[$i]['card_num'] = $v['type'];
				$row[$i]['card_pass'] = $v['affect_money'];
				$row[$i]['card_mianfei'] = $v['account_money']+$v['back_money'];
				$row[$i]['card_mianfei0'] = $v['freeze_money'];
				$row[$i]['card_mianfei1'] = $v['collect_money'];
				$row[$i]['card_mianfei2'] = $v['info'];
				$i++;
		}
		
		$xls = new Excel_XML('UTF-8', false, 'moneyLog');
		$xls->addArray($row);
		$xls->generateXML("moneyLog");
	}


}