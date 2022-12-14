<?php
class BonusAction extends MCommonAction {
    public function index(){
    	recoverBonus($this->uid);
		$minfo =getMinfo(session("u_id"),true);
		  $this->assign("minfo", $minfo);
		$this->display();
    } 
    public function youhuijun(){
        recoverBonus($this->uid);
		  $bonus_type = C('BONUS_TYPE');
 
		$this->assign("bonus_type", $bonus_type);
	    $experience_moneyArr = C('EXPERIENCE_MONEY');
        $typeArr = $experience_moneyArr['TYPE'];
        $statusArr = $experience_moneyArr['STATUS'];       
        $statusArr = array('已禁用','未使用','已使用','已过期');
        $map=array();
        if($_GET['status']==0){
           
        }else if($_GET['status']==2||$_GET['status']==1){
        	 $map['t1.status'] = $_GET['status'];
        }else if($_GET['status']==3){
            $map['t1.status'] = ['not between','1,2'];
			//$map['t1.status'] = ['neq','2'];
        }
        $map['t1.uid'] = $this->uid;
		$count = M('member_bonus t1')->join('__MEMBERS__ t2 on t1.uid = t2.id')->where($map)->count('t1.id');        
        import("ORG.Util.Page");
		$p = new Page($count, 10);
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
    	$bonusList = M('member_bonus t1')->join('__MEMBERS__ t2 on t1.uid = t2.id')->field('t1.*,t2.user_name')->where($map)->order('t1.id desc,t1.end_time asc')->limit($Lsql)->select();    	
    	$this->assign('bonusList',$bonusList);
    	$this->assign('pagebar',$page);    	
        $this->assign("search", $search);
        $this->assign("statusArr", $statusArr);

        // 红包统计
        $canUse = M('member_bonus t1')->join('__MEMBERS__ t2 on t1.uid = t2.id')->where($map)->group('t1.status')->getField('t1.status as status,sum(t1.money_bonus) as money_bonus');        
	//	var_dump($bonusList);
        $this->assign("canUse", $canUse);
		   $this->assign("status", $_GET['status']);
		/*$data['html'] = $this->fetch();
		exit(json_encode($data));*/
        $this->display();

    } 
    public function bonus(){
        recoverBonus($this->uid);
	    $experience_moneyArr = C('EXPERIENCE_MONEY');
        $typeArr = $experience_moneyArr['TYPE'];
        $statusArr = $experience_moneyArr['STATUS'];       
        $statusArr = array('已禁用','未使用','已使用','已过期');
        $map=array();
        if($_GET['status']){
            $map['t1.status'] = $_GET['status'];
        }else{
            $map['t1.status'] = '1';
        }
        $map['t1.uid'] = $this->uid;
		$count = M('member_bonus t1')->join('__MEMBERS__ t2 on t1.uid = t2.id')->where($map)->count('t1.id');        
        import("ORG.Util.Page");
		$p = new Page($count, 10);
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
    	$bonusList = M('member_bonus t1')->join('__MEMBERS__ t2 on t1.uid = t2.id')->field('t1.*,t2.user_name')->where($map)->order('t1.id desc,t1.end_time asc')->limit($Lsql)->select();    	
    	$this->assign('bonusList',$bonusList);
    	$this->assign('pagebar',$page);    	
        $this->assign("search", $search);
        $this->assign("statusArr", $statusArr);

        // 红包统计
        $canUse = M('member_bonus t1')->join('__MEMBERS__ t2 on t1.uid = t2.id')->where($map)->group('t1.status')->getField('t1.status as status,sum(t1.money_bonus) as money_bonus');        
        $this->assign("canUse", $canUse);
		/*$data['html'] = $this->fetch();
		exit(json_encode($data));*/
        $this->display();
    }
}
