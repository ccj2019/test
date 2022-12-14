<?php
class MemberinterestAction extends ACommonAction {
    public function index(){
        $experience_moneyArr = C('EXPERIENCE_MONEY');
        $typeArr = $experience_moneyArr['TYPE'];
        $statusArr = $experience_moneyArr['STATUS'];       
        $statusArr = array('已禁用','未使用','已使用');
        $map=array();
        if($_REQUEST['uname']){
            $map['t2.user_name'] = array("like",urldecode($_REQUEST['uname'])."%");
            $search['uname'] = urldecode($_REQUEST['uname']);   
        }
        if($_REQUEST['realname']){
            $map['t3.real_name'] = urldecode($_REQUEST['realname']);
            $search['realname'] = urldecode($_REQUEST['realname']);
        }    	
		$count = M('member_interest_rate t1')->join('__MEMBERS__ t2 on t1.uid = t2.id')->join('LEFT JOIN __MEMBER_INFO__ t3 on t1.uid = t3.uid')->where($map)->count('t1.id');    


        import("ORG.Util.Page");
        $p = new Page($count, C('ADMIN_PAGE_SIZE'));
        $page = $p->show();
        $Lsql = "{$p->firstRow},{$p->listRows}";

    	$interestList = M('member_interest_rate t1')->join('__MEMBERS__ t2 on t1.uid = t2.id')->join('LEFT JOIN __MEMBER_INFO__ t3 on t1.uid = t3.uid')->field('t1.*,t2.user_name,t3.real_name')->where($map)->order('t1.id desc,t1.end_time asc')->limit($Lsql)->select();    	
    	$this->assign('interestList',$interestList);
    	$this->assign('pagebar',$page);    	
        $this->assign("search", $search);
        $this->assign("statusArr", $statusArr);
        $this->assign("query", http_build_query($search));
		$this->display();
    }
    public function add(){
        if(!empty($_POST['borrow_username'])){
            $borrow_username = empty($_POST['borrow_username']) ? '' : $_POST['borrow_username'];
            $data['interest_rate'] = empty($_POST['interest_rate']) ? '' : getFloatValue($_POST['interest_rate'],2);
            
            $data['start_time'] = empty($_POST['start_time']) ? '' : strtotime($_POST['start_time']);
            $data['end_time'] = empty($_POST['end_time']) ? '' : strtotime($_POST['end_time']);
            $data['interest_cause'] = empty($_POST['interest_cause']) ? '' : $_POST['interest_cause'];

            if(empty($data['interest_rate']) || $data['interest_rate']<= 0) {$this->error(L('请正确输入加息利率！'));die;}
            if(empty($data['start_time']) || empty($data['end_time']) ) {$this->error(L('请正确输入有效起止时间！ '));die;}

            $data['status'] = '1';
            $data['type'] = '1';
            $data['add_time'] = time();

            $borrow_member = M('members')->where(array('user_name'=>$borrow_username))->find();         
            $data['uid'] = @$borrow_member['id'];
            if(empty($data['uid'])) {$this->error(L('用户不存在！'));die;}
            $rs = M('member_interest_rate')->add($data);            
            if ($rs) { //保存成功                
                $this->success(L('发放成功'),'index');die;
            } else {            
                $this->error(L('发放失败'));die;
            } 
        }
        $this->display();
    }
    public function edit(){
        $id = intval($_REQUEST['id']);
        $status = intval($_REQUEST['status']);
        $nowRow = M('member_interest_rate')->find($id);
        if($nowRow['status'] != $status && in_array($nowRow['status'], array(0,1))){
            $data['status'] = $status;
            $data['id'] = $id;
            $rs = M('member_interest_rate')->save($data);
            if($rs){
                $this->success(L('操作成功'));die;
            }
        }                  
        $this->error(L('回收失败'));     
    }
}
