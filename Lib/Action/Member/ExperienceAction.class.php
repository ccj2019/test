<?php
class ExperienceAction extends MCommonAction {
    public function index(){
 	$experience_moneyArr = C('EXPERIENCE_MONEY');
    	$typeArr = $experience_moneyArr['TYPE'];
        $statusArr = $experience_moneyArr['STATUS'];  

        $minfo = getMinfo($this->uid,true);
		$money_experience=M('member_experience') ->where('uid = '.$this->uid) ->select();
		//var_dump($money_experience,$this->uid);
		//die;
		$this->assign('money_experience',$money_experience);
        //$this->assign('money_experience',$minfo['money_experience']);

        $map['t1.uid'] = $this->uid;
    	$count = M('member_experience t1')->join('__MEMBERS__ t2 on t1.uid = t2.id')->join('LEFT JOIN __MEMBER_INFO__ t3 on t1.uid = t3.uid')->where($map)->count('t1.id');    	
        import("ORG.Util.Page");
		$p = new Page($count, 10);
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
    	$experienctList = M('member_experience t1')->join('__MEMBERS__ t2 on t1.uid = t2.id')->join('LEFT JOIN __MEMBER_INFO__ t3 on t1.uid = t3.uid')->join('__MEMBER_MONEY__ t4 on t4.uid = t1.uid')->field('t1.*,t2.user_name,t3.real_name,t4.money_experience as account_money_experience')->where($map)->order('t1.id desc,t1.end_time asc')->limit($Lsql)->select();    	
    	$this->assign('experienctList',$experienctList);
        $field = "b.borrow_duration,b.borrow_name,b.borrow_interest_rate,b.borrow_status,b.id,i.investor_capital,i.add_time,i.status,i.borrow_id,i.deadline,i.id as inid";
        $expborlist = M('borrow_investor i')->field($field)->join('lzh_borrow_info b on b.id = i.borrow_id')->where('i.is_experience=1 and i.investor_uid = '.$this->uid)->order('i.id desc')->limit(1000)->select();


        $this->assign('expborlist',$expborlist);
    	$this->assign('pagebar',$page);
    	$this->assign('typeArr',$typeArr);
    	$this->assign('statusArr',$statusArr);
	    $this->display();
    }
    public function experience(){
    	$experience_moneyArr = C('EXPERIENCE_MONEY');
    	$typeArr = $experience_moneyArr['TYPE'];
        $statusArr = $experience_moneyArr['STATUS'];  

        $minfo = getMinfo($this->uid,true);
        $this->assign('money_experience',$minfo['money_experience']);

        $map['t1.uid'] = $this->uid;
    	$count = M('member_experience t1')->join('__MEMBERS__ t2 on t1.uid = t2.id')->join('LEFT JOIN __MEMBER_INFO__ t3 on t1.uid = t3.uid')->where($map)->count('t1.id');    	
        import("ORG.Util.Page");
		$p = new Page($count, 10);
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
    	$experienctList = M('member_experience t1')->join('__MEMBERS__ t2 on t1.uid = t2.id')->join('LEFT JOIN __MEMBER_INFO__ t3 on t1.uid = t3.uid')->join('__MEMBER_MONEY__ t4 on t4.uid = t1.uid')->field('t1.*,t2.user_name,t3.real_name,t4.money_experience as account_money_experience')->where($map)->order('t1.id desc,t1.end_time asc')->limit($Lsql)->select();    	
    	$this->assign('experienctList',$experienctList);
        $field = "b.borrow_duration,b.borrow_name,b.borrow_interest_rate,b.borrow_status,b.id,i.investor_capital,i.add_time,i.status,i.borrow_id,i.deadline,i.id as inid";
        $expborlist = M('borrow_investor i')->field($field)->join('lzh_borrow_info b on b.id = i.borrow_id')->where('i.is_experience=1 and i.investor_uid = '.$this->uid)->order('i.id desc')->limit(1000)->select();


        $this->assign('expborlist',$expborlist);
    	$this->assign('pagebar',$page);
    	$this->assign('typeArr',$typeArr);
    	$this->assign('statusArr',$statusArr);
	    $this->display();
    }
}
