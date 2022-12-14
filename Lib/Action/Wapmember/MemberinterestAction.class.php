<?php
class MemberinterestAction extends MCommonAction {
    public function index(){
		$this->display();
    }
    public function memberinterest(){

        $whereStr = "status not in(2,3) and end_time < UNIX_TIMESTAMP()";
        $whereStr .= " and uid = {$this->uid}";
        $rs = M('member_interest_rate')->where($whereStr)->save(array('status'=>3)); 

        $experience_moneyArr = C('EXPERIENCE_MONEY');
        $typeArr = $experience_moneyArr['TYPE'];
        $statusArr = $experience_moneyArr['STATUS'];       
        $statusArr = array('已禁用','未使用','已使用','已过期');
        if($_GET['status']){
            $map['t1.status'] = $_GET['status'];
        }else{
            $map['t1.status'] = '1';
        }
        $map['t1.uid'] = $this->uid;
    	$count = M('member_interest_rate t1')->where($map)->count('t1.id');    	
        import("ORG.Util.Page");
		$p = new Page($count, 10);
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
    	$memberinterestList = M('member_interest_rate t1')->where($map)->order('t1.id desc,t1.end_time asc')->limit($Lsql)->select();    	
    	$this->assign('memberinterestList',$memberinterestList);
    	$this->assign('pagebar',$page);
        $this->assign("statusArr", $statusArr);
	    $this->display();
    }
}
