<?php
class BonusAction extends ACommonAction {
    public function index(){           
        $experience_moneyArr = C('EXPERIENCE_MONEY');
        $bonus_type = C('BONUS_TYPE');
        $typeArr = $bonus_type;
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
		$count = M('member_bonus t1')->join('__MEMBERS__ t2 on t1.uid = t2.id')->join('LEFT JOIN __MEMBER_INFO__ t3 on t1.uid = t3.uid')->where($map)->count('t1.id');        
        import("ORG.Util.Page");
		$p = new Page($count, 10);
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
    	$bonusList = M('member_bonus t1')->join('__MEMBERS__ t2 on t1.uid = t2.id')->join('LEFT JOIN __MEMBER_INFO__ t3 on t1.uid = t3.uid')->field('t1.*,t2.user_name,t3.real_name')->where($map)->order('t1.id desc,t1.end_time asc')->limit($Lsql)->select();    	
    	$this->assign('bonusList',$bonusList);
    	$this->assign('pagebar',$page);    	
        $this->assign("search", $search);
        $this->assign("statusArr", $statusArr);
        $this->assign("typeArr", $typeArr);
        $this->assign("query", http_build_query($search));
		$this->display();
    }
    public function add(){
    	
        if(!empty($_POST['borrow_username'])){                 
            $rs = $this->xbfafang($_POST);
            if ($rs['status'] == 1) { //保存成功                
                $this->success(L('发放成功'),'index');die;
            } else {            
                $this->error($rs['tips']);die;
            } 
        }
        $this->display();
    }
    public function xbfafang($data){
        //var_dump($data);exit();
        //var_dump(1);die;
        //$borrow_username = empty($data['borrow_username']) ? '' : $data['borrow_username'];
        $addData['money_bonus'] = empty($data['money_bonus']) ? '' : getFloatValue($data['money_bonus'],2);
        
        $addData['start_time'] = empty($data['start_time']) ? '' : strtotime($data['start_time']);
        $addData['end_time'] = empty($data['end_time']) ? '' : strtotime($data['end_time']);

        if(empty($addData['money_bonus']) || $addData['money_bonus']<= 0) {
            return array('status' => 0, 'tips' => '请正确输入红包金额！'. $addData['money_bonus'].'1');        
        }
        if(empty($addData['start_time']) || empty($addData['end_time']) ) {
            return array('status' => 0, 'tips' => '请正确输入有效起止时间！');
        }

        $addData['bonus_invest_min'] = empty($data['bonus_invest_min']) || $data['bonus_invest_min'] <=0 ? 0.00 : getFloatValue($data['bonus_invest_min'],2);

        $addData['status'] = '1';
        $addData['type'] = '1';
        $addData['add_time'] = time();
        
        
        $uid= $data["borrow_uids"];
        $uid=substr($uid,1);
        $borrow_member = M('members')->where("id in(".$uid.")")->select();
        M()->startTrans(); //r
        $yhq=true;
        foreach ($borrow_member as $k => $v) {
            for ($i = 0;$i<$data['money_num'];$i++){
                $addData['uid']=$v["id"];
                $rs = M('member_bonus')->add($addData);
                if(!$rs){
                    $yhq=false;
                }
            }
        }

        //var_dump(M('members')->getlastsql());
        // var_dump($borrow_member);
        // exit();   

        // if(@$data['uid'] > 0){
        //     $map['id'] = $data['uid'];
        // }else{
        //     $map['user_name'] = ["IN",$borrow_username];
        // }
        // $borrow_member = M('members')->where($map)->find();         
        // $addData['uid'] = @$borrow_member['id'];
        // if(empty($addData['uid'])) {
        //     return array('status' => 0, 'tips' => '用户不存在！');           
        // }
        //$rs = M('member_bonus')->add($addData);            
    

        if($yhq){
            M()->commit();
            return array('status' => 1, 'tips' => '发放成功！');
        }else{
            M()->rollback();
            return array('status' => 0, 'tips' => '发放失败！');  
        }


    }
    public function edit(){
        $id = intval($_REQUEST['id']);
        $status = intval($_REQUEST['status']);
        $nowRow = M('member_bonus')->find($id);
        if($nowRow['status'] != $status && in_array($nowRow['status'], array(0,1))){
            $data['status'] = $status;
            $data['id'] = $id;
            $rs = M('member_bonus')->save($data);
            if($rs){
                $this->success(L('操作成功'));die;
            }
        }                  
        $this->error(L('操作失败'));     
    }
    /**
     * 红包规则
     */
    public function rules(){
        $rList = M('member_bonus_rules')->order('type asc,money_bonus asc')->select();
        $this->assign('rList',$rList);
        $this->display();
    }
    public function rules_add(){
        if($_POST){
            if(@$_POST['money_bonus'] < 0.01 || @$_POST['invest_money'] < 0 || @$_POST['bonus_invest_min'] < 0 || @$_POST['expired_day'] < 1){
                $this->error(L('填写的规则有误！'));     die;
            }
            $model = M('member_bonus_rules');
            $model->create();

            $addData['start_time'] = empty($_POST['start_time']) ? '' : strtotime($_POST['start_time']);
            $addData['end_time'] = empty($_POST['end_time']) ? '' : strtotime($_POST['end_time']);
            if(empty($addData['start_time']) || empty($addData['end_time']) ) {
                $this->error(L('请正确输入有效起止时间！'));     die;                
            }
			$addData['money_type']=$_POST['money_type'];
            $model->start_time = $addData['start_time'];
            $model->end_time = $addData['end_time'];
            $model->add_time = time();
            if(!empty($model->id)){
                $rs = $model->save();
            }else{
                $rs = $model->add();    
            }
            
            if($rs){
//          	var_dump( M('member_bonus_rules')->getlastsql());die;
                $this->success('操作成功','rules');
            }else{
                $this->error(L('操作失败'));                
            }
            die;
        }
        $rules['type'] = '2';
        $this->assign('rules',$rules);
        $this->display();   
    }
    public function rules_edit(){
        $id = intval($_GET['id']);
        $rules = M('member_bonus_rules')->find($id);
        $this->assign('rules',$rules);
		if(isset($_GET["rules_addpubBonus"])&&!empty($_GET["rules_addpubBonus"])){
		 $z='';
			$member= M('members')->select();
			foreach($member as $k=>$v ){
				     $pubData = array(
            'uid' => $v["id"],
            'money_bonus' => $rules['money_bonus'],
            'start_time' => date('Y-m-d 00:00:00'),
            'end_time'   => date('Y-m-d 23:59:59', strtotime("+" . $rules['expired_day'] . " day", time())),
            'bonus_invest_min' => $rules['bonus_invest_min'],            
            );
        		$rs = pubBonus($pubData,2);
				//var_dump($rs);die;
				if($rs){
					echo "成功<br/>";
				}else{
					echo "失败<br/>";
				}
				 $z=$k;
//				  var_dump($z);die;
			}
// 		 var_dump($z+1);die;
			$z=$z+1;
		 if($z==count($member)){
		 	 $this->success('操作成功');
		 }
		}else{
			   $this->display('rules_add');   
		}
     //$this->display('rules_add');  
    }
    public function rules_del(){
        $id = intval($_GET['id']);
        $rs = M('member_bonus_rules')->delete($id);        
        if($rs){
            $this->success('删除成功','rules');
        }else{
            $this->error(L('删除失败'));                
        }
    }  public function rules_addpubBonus(){
    	
 
    }
    public function guoqi(){
        $map["b.end_time"]=array("between",time().",".(time()+259200));
        $map["b.money_bonus"]=array("gt","10");
        $map["b.tixing"]='1';
        $map["b.status"]="1";
        $gqlist = M('members m')->field('m.id,m.user_name,m.user_phone,b.end_time,b.start_time,b.id as bid,b.money_bonus,bi.real_name')->join('lzh_member_bonus b on m.id=b.uid')->join('lzh_member_info bi on m.id=bi.uid')->where($map)->select();
        $this->assign("bonusList",$gqlist);
        $this->display();
    }
    public function guoqis(){
        $map["b.end_time"]=array("between",time().",".(time()+259200));
        $map["b.money_bonus"]=array("gt","10");
        $map["b.tixing"]='1';
        $map["b.status"]="1";
        $gqlist = M('members m')->field('m.id,m.user_name,m.user_phone,b.end_time,b.start_time,b.id as bid,b.money_bonus')->join('lzh_member_bonus b on m.id=b.uid')->where($map)->select();
        foreach ($gqlist as $k => $v) {
           $data["time"]=date("Y-m-d H:i:s",$v["end_time"]);
           $data["money"]=$v["money_bonus"];
           $a=notice1("12",$v["id"],$data);

           $mmp["id"]=$v["bid"];
           $dat["tixing"]="2";
           $list=M("member_bonus")->where($mmp)->save($dat);

        }

        print_r("总共发送".(count($gqlist))."条提醒！");

    }
}
