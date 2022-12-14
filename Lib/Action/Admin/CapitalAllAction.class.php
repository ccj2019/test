<?php
// 全局设置
class CapitalAllAction extends ACommonAction
{
    /**
    +----------------------------------------------------------
    * 默认操作
    +----------------------------------------------------------
    */
    public function index()
    {
		$map=array();
		$paymap=array();
		$paymap['status'] =1;
		if(!empty($_REQUEST['start_time']) && !empty($_REQUEST['end_time'])){
			$timespan = strtotime(urldecode($_REQUEST['start_time'])).",".strtotime(urldecode($_REQUEST['end_time']));
			$map['add_time'] = array("between",$timespan);
			$paymap['add_time'] = array("between",$timespan);
			$search['start_time'] = strtotime(urldecode($_REQUEST['start_time']));	
			$search['end_time'] = strtotime(urldecode($_REQUEST['end_time']));	
		}elseif(!empty($_REQUEST['start_time'])){
			$xtime = strtotime(urldecode($_REQUEST['start_time']));
			$map['add_time'] = array("gt",$xtime);
			$paymap['add_time'] = array("gt",$xtime);
			$search['start_time'] = $xtime;	
		}elseif(!empty($_REQUEST['end_time'])){
			$xtime = strtotime(urldecode($_REQUEST['end_time']));
			$map['add_time'] = array("lt",$xtime);
			$paymap['add_time'] = array("lt",$xtime);
			$search['end_time'] = $xtime;	
		}
	    $account_money = M('member_money')->sum('account_money');
	    $this->assign("account_money",$account_money);

	    $yubi = M('member_money')->sum('yubi');
	    $this->assign("yubi",$yubi);

	    $money_freeze = M('member_money')->sum('money_freeze');
	    $this->assign("money_freeze",$money_freeze);

	    $yubi_freeze = M('member_money')->sum('yubi_freeze');
	    $this->assign("yubi_freeze",$yubi_freeze);
	    

		$list = M("member_moneylog")->field('type,sum(affect_money) as money')->where($map)->group('type')->select();
		//print_r($list);
		$row=array();
		$name = C('MONEY_LOG');
		foreach($list as $v){
			$row[$v['type']]['money']= ($v['money']>0)?$v['money']:$v['money']*(-1);
			$row[$v['type']]['name']= $name[$v['type']];
		}
		//print_r($list);
		$paymap['way'] =array(array("neq","off"),array("neq","online"));
   		$row[3]["money"] = m("member_payonline")->where($paymap)->sum('money');
		unset($paymap['way']);
		$paymap['way'] ="off";
    	$row[27]["money"] = m("member_payonline")->where($paymap)->sum('money');
		unset($paymap['way']);
		$paymap['way'] =array("eq","online");
   		$row[10000]["money"] = m("member_payonline")->where($paymap)->sum('money');
		$map['withdraw_status'] =2;
		$tx = M('member_withdraw')->where($map)->sum("second_fee");
		$row['tx']['name']= '提现手续费';
		$row['tx']['money']= $tx;
		
		$add_time = $map['add_time'];
		$map=array();
		$map1['deadline'] = $add_time;
		$map1['status'] = array("in","7,3,4,5");
		$map2['deadline'] = $add_time;
		$map2['status'] = array("in","3,4,5");
		//逾期
		$row['expired']['money'] = M('investor_detail')->where($map1)->sum('capital');
		$row['expired']['re_money'] = M('investor_detail')->where($map2)->sum('capital');
		//逾期
		
		//会员统计
		$mm = M('members')->count("id");
		$row['mm']['name']= '会员数';
		$row['mm']['num']= $mm;
		$ms_phone = M('members_status')->where("phone_status=1")->count("uid");
		$ms_id = M('members_status')->where("id_status=1")->count("uid");
		$ms_video = M('members_status')->where("video_status=1")->count("uid");
		$ms_face = M('members_status')->where("face_status=1")->count("uid");
		$ms_vip = M('members')->where("user_leve=1 AND time_limit>".time())->count("id");
		$row['mm']['name']= '会员数';
		$row['mm']['num']= $mm;
		$row['mm']['ms_phone']= $ms_phone;
		$row['mm']['ms_id']= $ms_id;
		$row['mm']['ms_video']= $ms_video;
		$row['mm']['ms_face']= $ms_face;
		$row['mm']['ms_vip']= $ms_vip;
		//会员统计
		//print_r($row["27"]);
		$this->assign("search",$search);
		$this->assign('list',$row);
        $this->display();
    }
	
}
?>