<?php
class ExperienceAction extends ACommonAction {
    public function index(){    	        
    	$pre = C('DB_PREFIX');
        $experience_moneyArr = C('EXPERIENCE_MONEY');
    	$typeArr = $experience_moneyArr['TYPE'];
        $statusArr = $experience_moneyArr['STATUS'];       

        $map=array();
        if($_REQUEST['uname']){
            $map['t2.user_name'] = array("like",urldecode($_REQUEST['uname'])."%");
            $search['uname'] = urldecode($_REQUEST['uname']);   
        }
        if($_REQUEST['realname']){
            $map['t3.real_name'] = urldecode($_REQUEST['realname']);
            $search['realname'] = urldecode($_REQUEST['realname']);
        }    	
		$count = M('member_experience t1')->join('__MEMBERS__ t2 on t1.uid = t2.id')->join('LEFT JOIN __MEMBER_INFO__ t3 on t1.uid = t3.uid')->where($map)->count('t1.id');    	
        import("ORG.Util.Page");
		$p = new Page($count, 10);
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
    	$experienctList = M('member_experience t1')->join('__MEMBERS__ t2 on t1.uid = t2.id')->join('LEFT JOIN __MEMBER_INFO__ t3 on t1.uid = t3.uid')->join('__MEMBER_MONEY__ t4 on t4.uid = t1.uid')->field('t1.*,t2.user_name,t3.real_name,t4.money_experience as account_money_experience')->where($map)->order('t1.id desc,t1.end_time asc')->limit($Lsql)->select();    	
        //已使用的体验金
        $hasUse = M('borrow_investor')->where('status not in(3,5) and is_experience = 1 and investor_uid = '.$this->uid)->sum('investor_capital');
        $this->assign('hasUse',$hasUse);
    	$this->assign('experienctList',$experienctList);
    	$this->assign('pagebar',$page);
    	$this->assign('typeArr',$typeArr);
    	$this->assign('statusArr',$statusArr);
        $this->assign("search", $search);
        $this->assign("query", http_build_query($search));
		$this->display();
    }
    public function pub(){
        if(!empty($_POST['borrow_username'])){
            $borrow_username = empty($_POST['borrow_username']) ? '' : $_POST['borrow_username'];
            $money_experience = empty($_POST['money_experience']) ? '' : getFloatValue($_POST['money_experience'],2);
            $experience_duration = empty($_POST['experience_duration']) ? '' : intval($_POST['experience_duration']);
            if(empty($money_experience) || $money_experience<= 0) {$this->error(L('请正确输入体验金金额！'));die;}
            if(empty($experience_duration) || $experience_duration<=0 ) {$this->error(L('请正确输入有效时间！ '));die;}
            $borrow_member = M('members')->where(array('user_name'=>$borrow_username))->find();         
            $uid = @$borrow_member['id'];
            if(empty($uid)) {$this->error(L('用户不存在！'));die;}
            $rs = pubExperienceMoney($uid,$money_experience,4,$experience_duration);
            if ($rs) { //保存成功                
                $this->success(L('发放成功'));die;
            } else {            
                $this->error(L('发放失败'));die;
            } 
        }
        $this->display();
    }
    public function recover(){
        $id = $_REQUEST['id'];
        $rs = recoverExperienceMoney($id);
        if ($rs) { //保存成功
            $this->assign('jumpUrl', __CONTROLLER__."/".session('listaction'));
            $this->success(L('回收成功'),"__URL__/index");
        } else {            
            $this->error(L('回收失败'),"__URL__/index");
        }        
    }
}
