<?php
// 全局设置
class CapitalDetailAction extends ACommonAction
{
    /**
    +----------------------------------------------------------
    * 默认操作
    +----------------------------------------------------------
    */
    public function index()
    {
		$map=array();

        if(isset($_REQUEST['laiyuan']) && $_REQUEST['laiyuan'] != ''){
            $map['l.laiyuan'] = intval($_REQUEST['laiyuan']);
            $search['laiyuan'] = $map['l.laiyuan'];
        }

		if($_REQUEST['uid'] && $_REQUEST['uname']){
			$map['l.uid'] = $_REQUEST['uid'];
			$search['uid'] = $map['l.uid'];	
			$search['uname'] = urldecode($_REQUEST['uname']);	
		}
		
		if($_REQUEST['uname'] && !$search['uid']){
			$map['m.user_name'] = array("like",urldecode($_REQUEST['uname'])."%");
			$map['m.user_name'] = urldecode($_REQUEST['uname']);	
			$search['uname'] = urldecode($_REQUEST['uname']);	
		}
		if($_REQUEST['rname'] && !$search['uid']){
			$map['n.real_name'] = array("like",urldecode($_REQUEST['rname'])."%");
			$map['n.real_name'] = urldecode($_REQUEST['rname']);	
			$search['rname'] = urldecode($_REQUEST['rname']);	
		}

		if($_REQUEST['target_uname']){
			$map['l.target_uname'] = urldecode($_REQUEST['target_uname']);
			$search['target_uname'] = $map['l.target_uname'];	
		}
		if(isset($_REQUEST['type']) && $_REQUEST['type'] != ''){
			$map['l.type'] = intval($_REQUEST['type']);
			$search['type'] = $map['l.type'];	
		}
		
		if(!empty($_REQUEST['bj']) && !empty($_REQUEST['money'])){
			$map['l.affect_money'] = array($_REQUEST['bj'],$_REQUEST['money']);
			$search['bj'] = $_REQUEST['bj'];	
			$search['money'] = $_REQUEST['money'];	
		}
		if(!empty($_REQUEST['start_time']) && !empty($_REQUEST['end_time'])){
			$timespan = strtotime(urldecode($_REQUEST['start_time'])).",".strtotime(urldecode($_REQUEST['end_time']));
			$map['l.add_time'] = array("between",$timespan);
			$search['start_time'] = urldecode($_REQUEST['start_time']);	
			$search['end_time'] = urldecode($_REQUEST['end_time']);	
		}elseif(!empty($_REQUEST['start_time'])){
			$xtime = strtotime(urldecode($_REQUEST['start_time']));
			$map['l.add_time'] = array("gt",$xtime);
			$search['start_time'] = $xtime;	
		}elseif(!empty($_REQUEST['end_time'])){
			$xtime = strtotime(urldecode($_REQUEST['end_time']));
			$map['l.add_time'] = array("lt",$xtime);
			$search['end_time'] = $xtime;	
		}
		if(session('admin_is_kf')==1)	$map['m.customer_id'] = session('admin_id');
		//分页处理
		import("ORG.Util.Page");
		$count = M('member_moneylog l')->join("{$this->pre}members m ON m.id=l.uid")->join("{$this->pre}member_info n ON n.uid=l.uid")->where($map)->count('l.id');
		$p = new Page($count, C('ADMIN_PAGE_SIZE'));
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		//分页处理
		
		$field= 'l.yongjin,l.id,l.add_time,m.user_name,l.yubi,l.freeze_yubi,l.affect_money,l.freeze_money,l.collect_money,(l.account_money+l.back_money) account_money,yubi,l.freeze_yubi,l.experience_money,l.target_uname,l.type,l.info,n.real_name';
		$order = "l.id DESC";
		$list = M('member_moneylog l')->field($field)->join("{$this->pre}members m ON m.id=l.uid")->join("{$this->pre}member_info n ON n.uid=l.uid")->where($map)->limit($Lsql)->order($order)->select();

        $this->assign("bj", array("gt"=>'大于',"eq"=>'等于',"lt"=>'小于'));
        $this->assign("type", C('MONEY_LOG'));
        $this->assign("laiyuan", array('1'=>'寻渔记','2'=>'小程序'));
        $this->assign("list", $list);
        $this->assign("pagebar", $page);
        $this->assign("search", $search);
        $this->assign("query", http_build_query($search));
        $this->display();
    }
	
	public function export(){
		import("ORG.Io.Excel");
        $map['l.laiyuan'] ='1';
		$map=array();
		if($_REQUEST['uid'] && $_REQUEST['uname']){
			$map['l.uid'] = $_REQUEST['uid'];
			$search['uid'] = $map['l.uid'];	
			$search['uname'] = urldecode($_REQUEST['uname']);	
		}
		
		if($_REQUEST['uname'] && !$search['uid']){
			$map['m.user_name'] = array("like",urldecode($_REQUEST['uname'])."%");
			$search['uname'] = urldecode($_REQUEST['uname']);	
		}
		if($_REQUEST['target_uname']){
			$map['l.target_uname'] = urldecode($_REQUEST['target_uname']);
			$search['target_uname'] = $map['l.target_uname'];	
		}
		if(isset($_REQUEST['type']) && $_REQUEST['type'] != ''){
			$map['l.type'] = intval($_REQUEST['type']);
			$search['type'] = $map['l.type'];	
		}
		
		if(!empty($_REQUEST['bj']) && !empty($_REQUEST['money'])){
			$map['l.affect_money'] = array($_REQUEST['bj'],$_REQUEST['money']);
			$search['bj'] = $_REQUEST['bj'];	
			$search['money'] = $_REQUEST['money'];	
		}
		if(!empty($_REQUEST['start_time']) && !empty($_REQUEST['end_time'])){
			$timespan = strtotime(urldecode($_REQUEST['start_time'])).",".strtotime(urldecode($_REQUEST['end_time']));
			$map['l.add_time'] = array("between",$timespan);
			$search['start_time'] = urldecode($_REQUEST['start_time']);	
			$search['end_time'] = urldecode($_REQUEST['end_time']);	
		}elseif(!empty($_REQUEST['start_time'])){
			$xtime = strtotime(urldecode($_REQUEST['start_time']));
			$map['l.add_time'] = array("gt",$xtime);
			$search['start_time'] = $xtime;	
		}elseif(!empty($_REQUEST['end_time'])){
			$xtime = strtotime(urldecode($_REQUEST['end_time']));
			$map['l.add_time'] = array("lt",$xtime);
			$search['end_time'] = $xtime;	
		}
		if(session('admin_is_kf')==1)	$map['m.customer_id'] = session('admin_id');
		$field= 'l.id,l.add_time,m.user_name,l.yubi,l.freeze_yubi,l.affect_money,l.freeze_money,l.collect_money,(l.account_money+l.back_money) account_money,l.target_uname,l.type,l.info';
		$list = M('member_moneylog l')->field($field)->join("{$this->pre}members m ON m.id=l.uid")->where($map)->limit($Lsql)->select();
		
		$type = C('MONEY_LOG');
		$row=array();
		$row[0]=array('序号','用户ID','用户名','交易对方','交易类型','影响金额','可用余额','冻结金额','可用鱼币','冻结鱼币','待收金额','发生时间','备注');
		$i=1;
		foreach($list as $v){
				$row[$i]['i'] = $i;
				$row[$i]['uid'] = $v['id'];
				$row[$i]['card_num'] = $v['user_name'];
				$row[$i]['card_pass'] = $v['target_uname'];
				$row[$i]['card_mianfei'] = $type[$v['type']];
				$row[$i]['card_mianfei0'] = $v['affect_money'];
				$row[$i]['card_mianfei1'] = $v['account_money'];
				$row[$i]['card_mianfei2'] = $v['freeze_money'];
				$row[$i]['card_mianfei4'] = $v['yubi'];
				$row[$i]['card_mianfei5'] = $v['freeze_yubi'];

				$row[$i]['card_mianfei3'] = $v['collect_money'];
				$row[$i]['card_timelimit'] = date("Y-m-d H:i:s",$v['add_time']);
				$row[$i]['info'] = $v['info'];;
				$i++;
		}
		
		$xls = new Excel_XML('UTF-8', false, 'datalist');
		$xls->addArray($row);
		$xls->generateXML("datalistcard");
	}
	
}
?>