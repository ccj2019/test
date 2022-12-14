<?php
// 本类由系统自动生成，仅供测试用途
class CapitalAction extends MCommonAction {
    public function index(){
		$this->display();
    }
    
    
    public function hehe(){
        
        //member/Capital/hehe 
	      $per = C('DB_PREFIX');
  
    $borrowinvestor = M("borrow_investor bi")  
	->join("{$per}borrow_info b ON bi.borrow_id=b.id")
	->field('bi.investor_capital,bi.id,b.borrow_interest_rate ,bi.investor_uid as uid,bi.id as iid')
  	->where("bi.investor_uid=".$this->uid."  and bi.borrow_id=1 and bi.status!=110")
	->find();
	//$receive_interest=  $borrowinvestor['investor_capital']*$borrowinvestor['borrow_interest_rate']/100/360;
	//echo  $borrowinvestor['borrow_interest_rate']/100/360;die;
	//echo ;die;
	//echo bcdiv(bcdiv($borrowinvestor['borrow_interest_rate'], 100, 2), 360);die;
	//echo bcdiv(bcdiv($borrowinvestor['borrow_interest_rate'], 100, 2), 360, 2);die;
	$receive_interest = bcadd($borrowinvestor['investor_capital']*$borrowinvestor['borrow_interest_rate']/100/360, 0, 2);
		//echo M("borrow_investor bi") ->getlastsql();die;
	$money= M('member_money mm') ->join("lzh_members m ON m.id=mm.uid")	->where("m.id=".$this->uid) ->find();
// echo M('member_money mm')  ->getlastsql();die;
            //每期收益
 //,b.user_name
 if(empty($borrowinvestor)){
      $investlog['starttime'] = time();
             $investlog['endtime'] = time();
             $investlog['deadline'] = time();
             $investlog['substitute_time'] = time();
             $investlog['income'] = 1;
             $investlog['has_capital'] = 1; 
             $investlog['borrow_interest_rate'] = $borrowinvestor['borrow_interest_rate'];
             $investlog['total'] = $receive_interest + $borrowinvestor['investor_capital'];
             $investlog['receive_capital'] = $borrowinvestor['investor_capital'];
             $investlog['receive_interest'] = $receive_interest;
             $investlog['interest'] = $receive_interest;

		
			 
			 $investlog['repayment_time'] = time();

			 $investlog['borrow_id'] = 1;
			 $investlog['investor_uid'] = $this->uid;
			
			$investlog['borrow_uid'] =0; 
			 $investlog['invest_id'] = $borrowinvestor['iid'];
			 
            $investlog['nums'] =  1;
            $investlog['has_capital'] = 0;
            $investlog['investor'] = $money['user_name'];
            $investlog['capital'] = $borrowinvestor['investor_capital']; //投资金额
            $investlog['benjin'] = $borrowinvestor['investor_capital']; //本金
            $investlog['invest'] = $receive_interest; //收益
            $investlog['allmoney'] = $receive_interest + $borrowinvestor['investor_capital']; //收益
            $investlog['rate'] = 0; //支持比例
			$investlog['status'] = 0; //支持比例  
			//var_dump($investlog,1);die;
		 	$invest_defail_id = M('investor_detail')->add($investlog);
			//echo M('investor_detail')->getlastsql();die;
			$invest_defail_id = M('member_moneylog')->add(['uid'=>$this->uid,'type'=>'9','yubi'=>$money['yubi'],'freeze_yubi'=>$money['yubi_freeze'],'yongjin'=>$money['yongjin'],'affect_money'=>$receive_interest,'account_money'=>$money['account_money']+$receive_interest,'back_money'=>$money['back_money'],'collect_money'=>$money['collect_money'],'freeze_money'=>$money['money_freeze'],'info'=>'体验金回款','add_time'=>$add_times,'add_ip'=>get_client_ip(),'target_uid'=>0,'target_uname'=>'','experience_money'=>$borrowinvestor['investor_capital']]);
            //memberMoneyLog
	//	 echo M('lzh_member_moneylog')->getlastsql();die;
		    $invest_defail_id = M('member_money')->save(["uid"=>$money['uid'],"money_freeze"=>$money['money_freeze']-$borrowinvestor['investor_capital'],'yubi'=>$money['yubi'],'yubi_freeze'=>$money['yubi_freeze'],'yongjin'=>$money['yongjin'],"money_collect"=>$money['money_collect'],"account_money"=>$money['account_money']+$receive_interest,"back_money"=>$money['back_money'],"credit_limit"=>$money['credit_limit'],"credit_cuse"=>$money['credit_cuse'],"borrow_vouch_limit"=>$money['borrow_vouch_limit'],"borrow_vouch_cuse"=>$money['borrow_vouch_cuse'],"invest_vouch_limit"=>$money['invest_vouch_limit'],"invest_vouch_cuse"=>$money['invest_vouch_cuse'],"money_experience"=>$money['money_experience'],"update_time"=>time()]);
		    M("borrow_investor")    ->where("id=".$borrowinvestor['id']) ->save(['status'=>110]);
 }
			
 	//var_dump(10);
	
    }
    
    
    public function qiandaotime(){
        
        	$members=M("members") ->order("id asc") ->select();
	 
		foreach ($members as $value) {
		    	$va=strtotime(date("Y-m-1 0:0:0",time()));
		    	$mapa['uid'] =$value['id']; 
		//var_dump(date("Y-m-1 0:0:0",$va));
	        	$mapa['sign_time']=["egt",$va];
		      
        	 	$sustain=M("member_sign")->where($mapa)->order("sign_time asc") ->select();
        	 		$sustainfind=M("member_sign")->where($mapa)->order("sign_time asc") ->find();
        	 		$iz=$sustainfind['sustain_day'];
		    	foreach ($sustain as $svalue) {
		        
		        M("member_sign") ->where("id = ".$svalue['id'] )->save(["sustain_day"=>$iz++]);
		 
		    // code...
		    	}
		}
    }
	public function qiandao(){
	    
		$map['uid'] = $this->uid;
		$va = getCreditsLog($map,1);
		$av=$va['list'][0];
		$va=strtotime(date("Y-m-1 0:0:0",time()));
		$map['sign_time']=["EGT",$va];

//		$list = getCreditsLog($map,30);
	 //var_dump($list);die;
	 	$mapa['uid'] = $this->uid;
		$va=strtotime(date("Y-m-1 0:0:0",time()));
		//var_dump(date("Y-m-1 0:0:0",$va));
		$mapa['sign_time']=["egt",$va];
	 	$sustain_day=M("member_sign")->where($mapa)->order("sign_time desc")
		->find();
		$mapa2['uid'] = $this->uid;
		$mapa2['sign_time']=["lt",$va];
	 	$sustain_day2=M("member_sign")->where($mapa2)->order("sign_time desc")
		->find();
		//var_dump($sustain_day2); die;
	 	//var_dump($sustain_day['sustain_day'],$sustain_day2['sustain_day']); die;
		if($sustain_day['sustain_day']>$sustain_day2['sustain_day']){
		    	$sustain_day['sustain_day']=$sustain_day['sustain_day']-$sustain_day2['sustain_day'];
		}else{
		    	$sustain_day['sustain_day']=$sustain_day['sustain_day'];
		}
//	var_dump($sustain_day['sustain_day'],$sustain_day2['sustain_day']); die;
//	echo M("member_sign")->getlastsql();		
// 	if($sustain_day['status']==0&&$sustain_day['sustain_day']==1){
// 		$this->assign("ling",1);
// 	}	else if($sustain_day['status']==0&&$sustain_day['sustain_day']==0){
// 		$this->assign("ling",0);
// 	}
// 		else if($sustain_day['status']==0&&$sustain_day['sustain_day']==7){
// 		$this->assign("ling",7);
// 	}	else if($sustain_day['status']==0&&$sustain_day['sustain_day']==14){	
// 		$this->assign("ling",14);
// 	}	else if($sustain_day['status']==0&&$sustain_day['sustain_day']==21){
// 		$this->assign("ling",21);
// 	}else if($sustain_day['status']==0&&$sustain_day['sustain_day']==30){
// 		$this->assign("ling",30);
// 	}else{
// 		$this->assign("ling",$sustain_day['sustain_day']+1);
// 	}	
	//	var_dump($sustain_day['sustain_day']); 
		
		
		
		$addsign['sign_time']=time();
		$addsign['uid']=$this->uid;
			//php获取今日起始时间戳和结束时间戳

		$this->assign("find",$av); 
 		$endYesterday=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;
 	 	$beginYesterday=mktime(0,0,0,date('m'),date('d'),date('Y'));
		if($beginYesterday<intval($av['add_time'])){
			if( (intval($av['add_time'])< $endYesterday)){
				$this->assign("yi","已签到");
			}  
			else{
				$this->assign("yi","点击签到");
			}
		
		}	else{
				$this->assign("yi","点击签到");
			}
	 	
	 	
	 			$map['sign_time']=["egt",$va];
	 	$list = M("member_sign")->where($map)->group('sign_time')->order("sign_time ASC")
		 ->select();
		 
		 
	if(isset($list[0])&&$list[0]['status']==0&&count($list)>=1){
		$this->assign("ling1",1);
		
	}else	if(isset($list[0])&&$list[0]['status']==1&&count($list)>=1){
		$this->assign("ling1",2);
		
	}else{
	    	$this->assign("ling1",0);
	}
	
	 if(isset($list[6])&&$list[6]['status']==0&&count($list)>=7){
		$this->assign("ling2",1);
	}else	if(isset($list[6])&&$list[6]['status']==1&&count($list)>=1){
		$this->assign("ling2",2);
		
	}else{
	    	$this->assign("ling2",0);
	}
	if(isset($list[13])&&$list[13]['status']==0&&count($list)>=14){	
		$this->assign("ling3",1);
	}else	if(isset($list[13])&&$list[13]['status']==1&&count($list)>=1){
		$this->assign("ling3",2);
		
	}else{
	    	$this->assign("ling3",0);
	}
	if(isset($list[20])&&$list[20]['status']==0&&count($list)>=20){
		$this->assign("ling4",1);
	}else	if(isset($list[20])&&$list[20]['status']==1&&count($list)>=1){
		$this->assign("ling4",2);
		
	}else{
	    	$this->assign("ling4",0);
	}
		     
    if(isset($list[27])&&date('m',time() )== 2&&$list[27]['status']==0&&count($list)>=27){
    	$this->assign("ling5",1);
    }else if(isset($list[27])&&date('m',time()) == 2&&$list[27]['status']==1&&count($list)>=1){
    	$this->assign("ling5",2);
    }else if(isset($list[29])&&$list[29]['status']==0&&count($list)>=29){
    	$this->assign("ling5",1);
    }else if(isset($list[29])&&$list[29]['status']==1&&count($list)>=1){
    	$this->assign("ling5",2);
    }else{
    	$this->assign("ling5",0);
    }
		$this->assign("ling",count($list));
 
	
//  	var_dump(count($list));die;
	
		$this->assign("volist",$list);
		$minfo =getMinfo($this->uid,true);
		$this->assign("minfo", $minfo);
		// //待回款日期
		// $dhktime=array();
		// $hktime=array();
		// 		//->field("lzh_borrow_info.lead_time,lzh_borrow_info.borrow_duration")->where("lzh_borrow_investor.investor_uid={$this->uid} and lzh_borrow_info.borrow_status=6")
		// $dhkbinfo = m("borrow_info")->alias('a')->field("a.lead_time,a.borrow_duration")->join('lzh_borrow_investor b on a.id = b.borrow_id')->where("b.investor_uid={$this->uid} and a.borrow_status=6") ->select();
		//  //	var_dump($dhkbinfo,m("borrow_info")->getlastsql());
		// foreach ($dhkbinfo as $k => $val) {
		// 	$dhktime[]=date('Y-n-j',$val['lead_time']);
		// 	//+$val['borrow_duration']*30*60*60*24
		// //	var_dump($ccccccccccccccc);
	
		// }
		// $hkbinfo =m("borrow_info")->alias('a')->field("a.lead_time,a.borrow_duration")->join('lzh_borrow_investor b on a.id = b.borrow_id')->where("b.investor_uid={$this->uid} and a.borrow_status=7") ->select();
		// foreach ($hkbinfo as $k => $val) {
		// 	$hktime[]=date('Y-n-j',$val['lead_time']);//$val['borrow_duration']*30*60*60*24
		// //	var_dump($val['lead_time']);
		// }
		$uid=$this->uid;
		
 
	//回款日历
        $oldtime =date("Y-m-1",time())." 00:00:00";
        $emp_time =date("Y-m-31",time()).' 23:59:59' ;
        $stime = strtotime($oldtime);
        $etime = strtotime($emp_time);
        // $mapx['b.lead_time'] = ['between',[$stime,$etime]];
        // $mapx['investor_uid'] = $this->uid;
        // $mapx['i.status'] = ["neq",3];

        // $mapx['i.id'] = ["in",$idsd];

        // $field = "b.borrow_duration,b.borrow_name,b.borrow_interest_rate,b.full_time,b.borrow_status as bstatus,b.id,i.investor_capital,i.add_time,i.status,i.borrow_id,i.deadline,i.id as inid,i.investor_way,i.member_interest_rate_id,i.bonus_id,b.borrow_img,i.is_experience,i.contractId,i.is_sign,i.step,b.lead_time";

        // $tenlist =  M('borrow_investor i')
        //     ->field($field)
        //     ->join('lzh_borrow_info b on b.id = i.borrow_id')
        //     ->where($mapx)->order('b.id desc')
        //     ->select();
        // //var_dump(count( $tenlist), M('borrow_investor i')->getlastsql());die;
        // foreach($tenlist as $k=>$v){
        //     $tenlist[$k]["lead_time_c"]=(intval($v["lead_time"]));
        // }
        // // var_dump(M('borrow_investor i')->getlastsql(),$tenlist);
        // $this->assign('tenlist',$tenlist);
        //回款日历结束

        // $start=strtotime(date("Y-m-1 0:0:0",time()));
        // $end=strtotime(date("Y-m-t 23:59:59",time()));		
		
// //已收本金
// 		$zz= M('borrow_investor bi')->join('lzh_borrow_info b on b.id = bi.borrow_id')->where("investor_uid={$uid} and b.borrow_status =7  and b.lead_time>={$start} and b.lead_time<={$end} and bi.status!=3")->sum('investor_capital');
// 		//待收本金
// 		$dsbj= M('borrow_investor bi')->join('lzh_borrow_info b on b.id = bi.borrow_id')->where("investor_uid={$uid} and b.borrow_status =6  and b.lead_time>={$start} and b.lead_time<={$end}  and bi.status!=3")->sum('investor_capital');
// 	//	var_dump(M('borrow_investor bi')->getlastsql());die;
// 		//已返利润
// 		$minfo["receive_interests"]	=M('investor_detail bi')->join('lzh_borrow_info b on b.id = bi.borrow_id')->where("investor_uid={$uid}  and b.borrow_status =7 and b.lead_time>={ $start} and b.lead_time<={$end}  and bi.status!=3")->sum('receive_interest');
// //M('investor_detail')->where("investor_uid={$uid} ")->sum('receive_interest');
	//	$yflr= M('borrow_investor bi')->join('lzh_borrow_info b on b.id = bi.borrow_id')->where("investor_uid={$uid} and b.borrow_status =7")->sum('receive_capital');
		//var_dump($zz); 
//echo 121;



		$map['uid'] = $this->uid;
		$start=strtotime(date("Y-m-1 0:0:0",time())); 
		$end=strtotime(date("Y-m-t 23:59:59",time()));  
//	 $va2=strtotime(date("Y-m-1 0:0:0",time()));
		//var_dump(date("Y-m-1 0:0:0",$va));

 		$ids=$this->getid(); //var_dump($ids);
		$idsd=$ids['a'][date("Y-n",time())];

		$mapx['i.id'] = ["in",$idsd];

        $field = "b.borrow_duration,b.borrow_name,b.borrow_interest_rate,b.full_time,b.borrow_status as bstatus,b.id,i.investor_capital,i.add_time,i.status,i.borrow_id,i.deadline,i.id as inid,i.investor_way,i.member_interest_rate_id,i.bonus_id,b.borrow_img,i.is_experience,i.contractId,i.is_sign,i.step,b.lead_time";

        $tenlist =  M('borrow_investor i')
            ->field($field)
            ->join('lzh_borrow_info b on b.id = i.borrow_id')
            ->where($mapx)->order('b.id desc')
            ->select();
        //var_dump(count( $tenlist), M('borrow_investor i')->getlastsql());die;
        foreach($tenlist as $k=>$v){
            $tenlist[$k]["lead_time_c"]=(intval($v["lead_time"]));
        }
        // var_dump(M('borrow_investor i')->getlastsql(),$tenlist);
        //$this->assign('tenlist',$tenlist);

        //var_dump($ids['b']);
//已收本金
// 	$tpl_var['sum']['receive_interests']=M('borrow_investor bi')->join('lzh_borrow_info b on b.id = bi.borrow_id')->where("investor_uid={$uid} and b.borrow_status =7  and b.lead_time>={$start} and b.lead_time<={$end} and bi.status!=3")->sum('investor_capital');
// 	 //var_dump(M('borrow_investor bi')->getlastsql());die;
// //	 M('borrow_investor bi')->join('lzh_borrow_info b on b.id = bi.borrow_id')->where("investor_uid={$uid} and b.borrow_status =7 and b.lead_time>={ $start} and b.lead_time<={$end} and bi.status!=3")->sum('investor_capital');
// 		//待收本金
// 	$tpl_var['sum']['investor_capital']= M('borrow_investor bi')->join('lzh_borrow_info b on b.id = bi.borrow_id')->where("investor_uid={$uid} and b.borrow_status =6 and b.lead_time>={$start} and b.lead_time<={$end} and bi.status!=3")->sum('investor_capital');
// 		//已返利润
// 	$tpl_var['sum']['shouyi'] = M('investor_detail bi')->join('lzh_borrow_info b on b.id = bi.borrow_id')->where("investor_uid={$uid}  and b.borrow_status =7 and b.lead_time>={$start}  and b.lead_time<={$end}  ")->sum('receive_interest');
	
	
       // $tpl_var['sum']['capital'] = M('investor_detail t1')->where("t1.investor_uid={$uid} and t1.repayment_time<=0")->join("{$pre}borrow_info t2 on t1.borrow_id =  t2.id")->group('t2.borrow_status')->getField('t2.borrow_status,sum(t1.capital)');
      //  $tpl_var['sum']['interest'] = M('investor_detail t1')->where("t1.investor_uid={$uid}")->sum("t2.receive_interest");
        //累计奖励
      //  $tpl_var['sum']['shouyi'] = M('member_moneylog')->where('type in(13,20,32,34,50) and uid={$uid}')->sum('affect_money');
        // 回收中
        $tpl_var['list4'] = M('borrow_investor bi')->join($pre.'borrow_info b on b.id = bi.borrow_id')->where("investor_uid={$uid} and b.borrow_status in(2,4)")->field('count(bi.id) as count,sum(bi.investor_capital) as sum')->find();
        $tpl_var['list6'] = M('borrow_investor bi')->join($pre.'borrow_info b on b.id = bi.borrow_id')->where("investor_uid={$uid} and b.borrow_status =6")->field('count(bi.id) as count,sum(bi.investor_capital) as sum')->find();
        $tpl_var['list7'] = M('borrow_investor bi')->join($pre.'borrow_info b on b.id = bi.borrow_id')->where("investor_uid={$uid} and b.borrow_status =7")->field('count(bi.id) as count,sum(bi.investor_capital) as sum')->find();
        $tpl_var['list_all']['count'] = $tpl_var['list4']['count'] + $tpl_var['list6']['count'] + $tpl_var['list7']['count'];
        $tpl_var['list_all']['sum'] = $tpl_var['list4']['sum'] + $tpl_var['list6']['sum'] + $tpl_var['list7']['sum'];



        $fqhk=$ids['b'][date("Y-n",time())]['bx'];
        $fqhkd=$ids['b'][date("Y-n",time())]['lx'];
        //$fqhkdl=$ids['b'][date("Y-n",time())]['al'];

        $yhbj["bi.id"]=["in",$fqhk];
        $yhbj["bi.investor_uid"]=$uid;
        $yhbj["b.borrow_status"]=7;
        $yhbj["bi.status"]=["neq",3];

        $tpl_var['receive_interests']=M('borrow_investor bi')->join('lzh_borrow_info b on b.id = bi.borrow_id')->where($yhbj)->sum('investor_capital');

        // $xyhbj["investor_uid"]=$uid;
        // $xyhbj["invest_id"]=["in",$fqhk];
        // $xyhbj["status"]=1;
        // $bxlx= M('investor_detail')->where($xyhbj)->sum('receive_interest');

        $dyhbj["bi.id"]=["in",$fqhk];
        $dyhbj["bi.investor_uid"]=$uid;
        $dyhbj["b.borrow_status"]=6;
        $dyhbj["bi.status"]=["neq",3];

        $tpl_var['investor_capital']= M('borrow_investor bi')->join('lzh_borrow_info b on b.id = bi.borrow_id')->where($dyhbj)->sum('investor_capital');

        
        $ldyhbj["id"]=["in",$fqhkd];
        // $ldyhbj["investor_uid"]=$uid;
        $ldyhbj["status"]=1;
		//已返利润
	    $tpl_var['shouyi'] = M('investor_detail')->where($ldyhbj)->sum('receive_interest');//+$bxlx;

	    $this->assign("tpl_var", $tpl_var);



		$this->assign("minfo", $minfo);
		$this->assign("zz", $zz);
		$this->assign("dsbj", $dsbj);
	//	$this->assign("yflr",$yflr); 
		$this->assign("dhktime", json_encode($dhktime));
		$this->assign("hktime", json_encode($hktime));




		$hkid=$ids['b'][date("Y-n",time())]['hk'];
        foreach($tenlist as $k=>$v){
             $sort_order=1;
             if($v["total"]>1){
             	if(!empty($v["hkday"])){
             		$yue=$v["hkday"];
					$dt=date('Y-n-j',$v['lead_time']);
					for ($i=0; $i < $v["total"]; $i++) { 
						if(date('Y-n',strtotime("$dt+".$yue*$i." days"))==date("Y-n",time())){
							$a=strtotime("$dt+".$yue*$i."days");
							$sort_order=$i+1;
						}
					}
             	}else{
//                    $yue=$v["borrow_duration"]/$v["total"];
//                    $dt=date('Y-n-j',$v['lead_time']);
//                    for ($i=0; $i < $v["total"]; $i++) {
//
//                        $stime=strtotime("$dt+".$yue*$i."month");
//                        if($stime>1612579704&&in_array($val["id"],array(1613,1704,1872,1962))){
//                            $stime= strtotime("last day of +".$yue*$i." month", strtotime($dt)); //strtotime("$dt+".$i."month");
//                        }
//
//                        $tenlist[$a]["hkzt2"]=date('Y-n-j',$stime);//date('Y-n-j',strtotime("$dt+".$yue*$i."month"));
//                        if(date('Y-n-j',$stime)==$_POST["date"]){
//
//
//                        }

					$yue=$v["borrow_duration"]/$v["total"];
					$dt=date('Y-n-j',$v['lead_time']);
					for ($i=0; $i < $v["total"]; $i++) {

                        $stime=strtotime("$dt+".$yue*$i."month");
                        if($stime>1612579704&&in_array($v["id"],array(1613,1704,1872,1962))){
                            $stime= strtotime("last day of +".$yue*$i." month", strtotime($dt)); //strtotime("$dt+".$i."month");
                        }
                        if ($v["id"] == 2102) {
                            $stime = strtotime("last day of +" . $yue * $i . " month", strtotime($dt)); //strtotime("$dt+".$i."month");

                        }
                        if ($stime > 1644979704 && in_array($v["id"], array(
                                2368
                            )) && $stime < 1646275704) {
                            $stime = strtotime("last day of +" . $yue * $i . " month", strtotime($dt)); //strtotime("$dt+".$i."month");
                        }


                        if(date('Y-n',$stime)==date("Y-n",time())){
                                $a=$stime;//strtotime("$dt+".$yue*$i."month");
                                $sort_order=$i+1;
                        }
					}
				}
			
			}else{
				$a=$v['lead_time'];
			}
			

			$tenlist[$k]["sort_order"]=$sort_order;
			$mpp["invest_id"]=$v["inid"];
			$mpp["status"]=1;
			$mpp["sort_order"]=$sort_order;
			$mpp["investor_uid"]=$this->uid;
			$minfo=M("investor_detail")->where($mpp)->find();
			
			if($minfo){
				$tenlist[$k]["hkzt"]=1;
				$tenlist[$k]["shouyi"]=$minfo["receive_interest"]+$minfo["receive_capital"];
			}else{
				$tenlist[$k]["hkzt"]=0;
				//$tenlist[$a]["hklx"]="未回款";
				if($sort_order==$v["total"]){
					$tenlist[$k]["shouyi"]=$v["investor_capital"];
				}else{
					$tenlist[$k]["shouyi"]='';
				}
			}
			if($sort_order==$v["total"]){
				if(empty($minfo)){
					$tenlist[$k]["hklx"]="本金";  
				}else{
					$tenlist[$k]["hklx"]="本金+收益";  
				}
				 
			}else{
				$tenlist[$k]["hklx"]="第".$sort_order."次收益";
			}



			$tenlist[$k]["lead_time_c"]=$a;
			if($sort_order==$v["total"]){
				$tenlist[$k]["bj"]=1;
			}else{
				$tenlist[$k]["bj"]=0;
			}
        }
        //var_dump($tenlist); 
        $tenlist=$this->paixu($tenlist);
        $this->assign('tenlist',$tenlist);
        $mdata = M('members')->find($uid);
        $this->assign('mdata',$mdata);
        $boncount = M('member_bonus')->where('status=1 and uid='.$this->uid)->count('id');
        $bankcount = M('member_banks')->where('uid='.$this->uid)->count('id');
        $ratecount = M('member_interest_rate')->where('status=1 and uid='.$this->uid)->count('id');
        $this->assign('ratecount',$ratecount);
        $this->assign('bankcount',$bankcount);
        $this->assign('boncount',$boncount);
        $this->assign('msg',$msgList);
        $glo = array('web_title'=> '会员中心');
        $this->assign("mx",getMemberBorrowScan($this->uid));
        $this->assign($glo);		
		$ymap['investor_uid'] = $this->uid;
		$ymap['BorrowInvestor.status'] = array("in","5,6,1,4");
		$ylist = m("borrow_info")->alias('a')->field("a.lead_time,a.borrow_duration,a.total,a.id")->join('lzh_borrow_investor b on a.id = b.borrow_id')->where("b.investor_uid={$this->uid} and a.borrow_status=7") ->select(); 
		
		// $tzmap['investor_uid'] = $this->uid;
		// $tzmap['BorrowInvestor.status'] =array("in","1,4,5,6");

		$tzmap['b.investor_uid'] = $this->uid;
		$tzmap['a.borrow_status']  =array("in","6,7");
		$tzlist = m("borrow_info")->alias('a')->field("a.hkday,a.lead_time,a.borrow_duration,a.total,a.id,b.id as inid")->join('lzh_borrow_investor b on a.id = b.borrow_id')->where($tzmap) ->select();;
	 

 		// var_dump($ylist['list']);die;	
//var_dump($tzlist,"1");die;
 $aa=array();
		foreach ($tzlist as $k => $val) {
			// $mpp["investor_uid"]=$this->uid;
			// $mpp["status"]=0;
			// $mpp["sort_order"]=
			
			// $dhktime[]=date('Y-n-j',$val['lead_time']);
			// $dhk[date('Y-n-j',$val['lead_time'])][]=$val["id"];
// var_dump($val,"1");

			
//var_dump($val["total"]);
			if($val["total"]>1){
				 // var_dump($val["hkday"]);
				if(!empty($val["hkday"])){

					$yue=$val["hkday"];     
					$dt=date('Y-n-j',$val['lead_time']);
					for ($i=1; $i < $val["total"]; $i++) { 
						//var_dump($i);
						//$mpp["borrow_id"]=$val["id"];
						$mpp["invest_id"]=$val["inid"];
						$mpp["status"]=1;
						$mpp["sort_order"]=$i+1;
						$mpp["investor_uid"]=$this->uid;
						$minfo=M("investor_detail")->where($mpp)->find();
						if($minfo){
							//var_dump(M("investor_detail")->getlastsql());
							$hktime[]=date('Y-n-j',strtotime("$dt+".$yue*$i."days"));
		 		            $hkt[date('Y-n-j',strtotime("$dt+".$yue*$i."days"))][]=$val["id"];
		 		            $hktd[date('Y-n',strtotime("$dt+".$yue*$i."days"))][]=$val["id"];

							
						}else{
						    $dhktime[]=date('Y-n-j',strtotime("$dt+".$yue*$i."days"));
						    $dhk[date('Y-n-j',strtotime("$dt+".$yue*$i."days"))][]=$val["id"];
						    $dhkd[date('Y-n',strtotime("$dt+".$yue*$i."days"))][]=$val["id"];
						}
						//var_dump($mpp);
					}

				}else{
					$yue=$val["borrow_duration"]/$val["total"];
					$dt=date('Y-n-j',$val['lead_time']);
					for ($i=1; $i < $val["total"]; $i++) { 
						//var_dump($i);
						//$mpp["borrow_id"]=$val["id"];
						$mpp["invest_id"]=$val["inid"];
						$mpp["status"]=1;
						$mpp["sort_order"]=$i+1;
						$mpp["investor_uid"]=$this->uid;
						$minfo=M("investor_detail")->where($mpp)->find();

                        $stime=strtotime("$dt+".$yue*$i."month");
                        if($stime>1612579704&&in_array($val["id"],array(1613,1704,1872,1962))){
                            $stime= strtotime("last day of +".$yue*$i." month", strtotime($dt)); //strtotime("$dt+".$i."month");

                        }

						if($minfo){
							//var_dump(M("investor_detail")->getlastsql());
                            $hktime[]=date('Y-n-j',$stime);//date('Y-n-j',strtotime("$dt+".$yue*$i."month"));
//                            $hkt[date('Y-n-j',strtotime("$dt+".$yue*$i."month"))][]=$val["id"];
//                            $hktd[date('Y-n',strtotime("$dt+".$yue*$i."month"))][]=$val["id"];
                            $hkt[date('Y-n-j',$stime)][]=$val["id"];
                            $hktd[date('Y-n',$stime)][]=$val["id"];
							
						}else{
                            $dhktime[]=date('Y-n-j',$stime);
                            $dhk[date('Y-n-j',$stime)][]=$val["id"];
                            $dhkd[date('Y-n',$stime)][]=$val["id"];
						}
					}
				}
				

			}
			//else{
			       // var_dump($val["id"]);
                    //$mpp["borrow_id"]=$val["id"];
                    $mpp["invest_id"]=$val["inid"];
					$mpp["status"]=1;
					$mpp["sort_order"]=1;
					$mpp["investor_uid"]=$this->uid;
					$minfo=M("investor_detail")->where($mpp)->find();
					
					if($minfo){
//var_dump($minfo);
						//var_dump(M("investor_detail")->getlastsql());
						$hktime[]=date('Y-n-j',$val['lead_time']);//$val['borrow_duration']*30*60*60*24
						$hkt[date('Y-n-j',$val['lead_time'])][]=$val["id"];
						$hktd[date('Y-n',$val['lead_time'])][]=$val["id"];

						// $dhktime[]=date('Y-n-j',strtotime("$dt+".$yue*$i."month"));
						// $dhk[date('Y-n-j',strtotime("$dt+".$yue*$i."month"))][]=$val["id"];
					}else{
					    // $hktime["time"][]=date('Y-n-j',strtotime("$dt+".$yue*$i."month"));
	 		     //        $hkt[date('Y-n-j',strtotime("$dt+".$yue*$i."month"))][]=$val["id"];
						$dhktime[]=date('Y-n-j',$val['lead_time']);
						$dhk[date('Y-n-j',$val['lead_time'])][]=$val["id"];
						$dhkd[date('Y-n',$val['lead_time'])][]=$val["id"];
					}
			   // $dhktime[]=date('Y-n-j',$val['lead_time']);
			   // $dhk[date('Y-n-j',$val['lead_time'])][]=$val["id"];
			//}

		}//exit();
		$dhktime=array_unique($dhktime);
	    $this->assign("dhktime", json_encode($dhktime));
		$this->assign("dhk", json_encode($dhk));

 		//array_unique($hktime["time"]);

		$hktime=array_unique($hktime);
		$this->assign("hktime", json_encode($hktime));
		$this->assign("hkt", json_encode($hkt));



		$this->display();
    }
public function paixu($list){
 		//$list = [6, 2, 4, 8, 5, 9];
		$len = count($list);
		$n = count($list) - 1;
		for ($i = 0; $i < $len; $i++) {
			for ($j = 0; $j < $n; $j++) {
			    if ($list[$j]['lead_time_c'] > $list[$j + 1]['lead_time_c']) {
					$tmp = $list[$j];
					$list[$j] = $list[$j + 1];
					$list[$j + 1] = $tmp;
				}
			}
		}
		return $list;
 	}
 	public function paixu1($list){
 		//$list = [6, 2, 4, 8, 5, 9];
		$len = count($list);
		$n = count($list) - 1;
		for ($i = 0; $i < $len; $i++) {
			for ($j = 0; $j < $n; $j++) {
			    if ($list[$j]['hk_time_c'] > $list[$j + 1]['hk_time_c']) {
					$tmp = $list[$j];
					$list[$j] = $list[$j + 1];
					$list[$j + 1] = $tmp;
				}
			}
		}
		return $list;
 	}
 	public function getid(){
 		$tzmap['b.investor_uid'] = $this->uid;
		$tzmap['a.borrow_status']  =array("in","6,7");
		//$tzmap['a.id']  =array("in","1364,1365,1366,1367");
		$tzlist = m("borrow_info")->alias('a')->field("a.hkday,a.lead_time,a.borrow_duration,a.total,a.id,b.id as idss")->join('lzh_borrow_investor b on a.id = b.borrow_id')->where($tzmap) ->select();;
 		$aa=array();
		foreach ($tzlist as $k => $val) {
			if($val["total"]>1){

				if(!empty($val["hkday"])){
             		$yue=$val["hkday"];
             		$dt=date('Y-n-j',$val['lead_time']);
					for ($i=0; $i < $val["total"]; $i++) { 
		
							$stime=strtotime("$dt+".$yue*$i."days");
							$hktime[]=date('Y-n-j',$stime);
		 		            $hkt[date('Y-n-j',$stime)][]=$val["id"];
		 		            $hktd[date('Y-n',$stime)][]=$val["idss"];

		 		            if(($i+1)==$val["total"]){
		 		            	$hktdb[date('Y-n',$stime)]['bx'][]=$val["idss"];
		 		            	
		 		            }else{
		 		            	// $vid=m("investor_detail")->where("sort_order='".($i+1)."' and invest_id=".$val["idss"])->getField("id");
		 		            	// $hktdb[date('Y-n',$stime)]['lx'][]=$vid;
		 		            }
		 		            $vid=m("investor_detail")->where("sort_order='".($i+1)."' and invest_id=".$val["idss"])->getField("id");
		 		            if(!empty($vid)){
		 		            	$hktdb[date('Y-n',$stime)]['lx'][]=$vid;
		 		            	//$hktdb[date('Y-n',$stime)]['hk'][]=$vid["invest_id"];
		 		            }
		 		            
					}
             	}else{
             		$yue=$val["borrow_duration"]/$val["total"];
					$dt=date('Y-n-j',$val['lead_time']);
					for ($i=0; $i < $val["total"]; $i++) { 
		
							$stime=strtotime("$dt+".$yue*$i."month");
							$hktime[]=date('Y-n-j',$stime);
		 		            $hkt[date('Y-n-j',$stime)][]=$val["id"];
		 		            $hktd[date('Y-n',$stime)][]=$val["idss"];

		 		            if(($i+1)==$val["total"]){
		 		            	$hktdb[date('Y-n',$stime)]['bx'][]=$val["idss"];
		 		            	
		 		            }else{
		 		            	// $vid=m("investor_detail")->where("sort_order='".($i+1)."' and invest_id=".$val["idss"])->getField("id");
		 		            	// $hktdb[date('Y-n',$stime)]['lx'][]=$vid;
		 		            }
		 		            $vid=m("investor_detail")->where("sort_order='".($i+1)."' and invest_id=".$val["idss"])->getField("id");
		 		            if(!empty($vid)){
		 		            	$hktdb[date('Y-n',$stime)]['lx'][]=$vid;
		 		            	//$hktdb[date('Y-n',$stime)]['hk'][]=$vid["invest_id"];
		 		            }
		 		            
					}
             	}
				

			}
						$hktime[]=date('Y-n-j',$val['lead_time']);
						$hkt[date('Y-n-j',$val['lead_time'])][]=$val["id"];
						$hktd[date('Y-n',$val['lead_time'])][]=$val["idss"];

						$hktd[date('Y-n',$val['lead_time'])][]=$val["idss"];
						if($val["total"]==1){
							$hktdb[date('Y-n',$val['lead_time'])]['bx'][]=$val["idss"];
							$vid=m("investor_detail")->where("sort_order='1' and invest_id=".$val["idss"])->getField("id");
	 		           		if(!empty($vid)){
		 		            	$hktdb[date('Y-n',$val['lead_time'])]['lx'][]=$vid;
		 		            	//$hktdb[date('Y-n',$val['lead_time'])]['hk'][]=$vid["invest_id"];
		 		            }
						}

						

		}

		$data=array("a"=>$hktd,"b"=>$hktdb);
		return $data;

 	}
 public function zindexapi(){
        recoverBonus($this->uid);

        $ucLoing = de_xie($_COOKIE['LoginCookie']);
        setcookie('LoginCookie','',time()-10*60,"/");
        $uid = $this->uid;
        $minfo =getMinfo(session("u_id"),true);
        $all_money = getFloatvalue($minfo['account_money']+$minfo['money_collect']+$minfo['money_freeze'],2);
        // 累计收益
        $minfo['receive_interests'] = M('borrow_investor')->where('investor_uid = '.$uid.' and status = 5')->sum('receive_interest');
        $pre = C('DB_PREFIX');
        $this->assign("uid",$uid);
        // 资料完善度
        $mVerify = m("members m")->field("m.credits,m.id,m.user_leve,m.time_limit,s.id_status,s.phone_status,s.email_status,s.video_status,s.face_status")->join("{$pre}members_status s ON s.uid=m.id")->where("m.id={$uid} ")->find();
        $mInfoProgress = 10;
        $mVerify['id_status'] and $mInfoProgress += 20;
        $mVerify['phone_status'] and $mInfoProgress += 20;
        $mVerify['email_status'] and $mInfoProgress += 20;
        $mDataInfo = M('member_data_info')->where("uid={$this->uid} and status = 1")->count('id');
        $mDataInfo > 1 and $mInfoProgress += 5;
        $mDataInfo > 3 and $mInfoProgress += 5;
        $mDataInfo > 5 and $mInfoProgress += 10;//60
        $pinpass = M('members')->where("id={$this->uid}")->find();
        $pinpass['pin_pass'] and $mInfoProgress += 10;
        if($mInfoProgress>100)$mInfoProgress=100;

        $this->assign('mInfoProgress', $mInfoProgress);
        // 投资进行中总额

        $map['uid'] = $this->uid;

        $start=strtotime(date("Y-m-1 0:0:0",strtotime($_POST["date"]."-01  0:0:1")));

        $end=strtotime(date("Y-m-t 23:59:59",strtotime($_POST["date"]."-01  0:0:1")));

        // //已收本金
        // $tpl_var['sum']['receive_interests']=M('borrow_investor bi')->join('lzh_borrow_info b on b.id = bi.borrow_id')->where("investor_uid={$uid} and b.borrow_status =7  and b.lead_time>={$start} and b.lead_time<={$end} and bi.status!=3")->sum('investor_capital');
        // //待收本金
        // $tpl_var['sum']['investor_capital']= M('borrow_investor bi')->join('lzh_borrow_info b on b.id = bi.borrow_id')->where("investor_uid={$uid} and b.borrow_status =6 and b.lead_time>={$start} and b.lead_time<={$end} and bi.status!=3")->sum('investor_capital');
        // //已返利润
        // $tpl_var['sum']['shouyi'] = M('investor_detail bi')->join('lzh_borrow_info b on b.id = bi.borrow_id')->where("investor_uid={$uid}  and b.borrow_status =7 and b.lead_time>={$start}  and b.lead_time<={$end}  ")->sum('receive_interest');
        

        $ids=$this->getid(); //var_dump($ids);
		$idsd=$ids['a'][$_POST["date"]];
		//var_dump($mapx);exit();

		$mapd['i.id'] = ["in",$idsd];
		//$mapx['i.id'] = ["in",$ids];

        $field = "b.hkday,b.total,b.borrow_duration,b.borrow_name,b.borrow_interest_rate,b.full_time,b.borrow_status as bstatus,b.id,i.investor_capital,i.add_time,i.status,i.borrow_id,i.deadline,i.id as inid,i.investor_way,i.member_interest_rate_id,i.bonus_id,b.borrow_img,i.is_experience,i.contractId,i.is_sign,i.step,b.lead_time";
	  
        $tenlist =  M('borrow_investor i')
        ->join('lzh_borrow_info b on b.id = i.borrow_id')
        ->field($field)
        ->where($mapd)->order('b.id desc')
         ->select();
        // $data["a"]=M('borrow_investor i')->getlastsql();
        $tpl_var['dd']=$ids['b'][$_POST["date"]]; 
        $fqhk=$ids['b'][$_POST["date"]]['bx'];
        $fqhkd=$ids['b'][$_POST["date"]]['lx'];
        //$fqhkdl=$ids['b'][$_POST["date"]]['al'];

        $yhbj["bi.id"]=["in",$fqhk];
        $yhbj["bi.investor_uid"]=$uid;
        $yhbj["b.borrow_status"]=7;
        $yhbj["bi.status"]=["neq",3];

        $tpl_var['sum']['receive_interests']=M('borrow_investor bi')->join('lzh_borrow_info b on b.id = bi.borrow_id')->where($yhbj)->sum('investor_capital');

        // $xyhbj["investor_uid"]=$uid;
        // $xyhbj["invest_id"]=["in",$fqhk];
        // $xyhbj["status"]=1;
        // $bxlx= M('investor_detail')->where($xyhbj)->sum('receive_interest');

        $dyhbj["bi.id"]=["in",$fqhk];
        $dyhbj["bi.investor_uid"]=$uid;
        $dyhbj["b.borrow_status"]=6;
        $dyhbj["bi.status"]=["neq",3];

        $tpl_var['sum']['investor_capital']= M('borrow_investor bi')->join('lzh_borrow_info b on b.id = bi.borrow_id')->where($dyhbj)->sum('investor_capital');

        
        $ldyhbj["id"]=["in",$fqhkd];
        // $ldyhbj["investor_uid"]=$uid;
        $ldyhbj["status"]=1;
		//已返利润
	    $tpl_var['sum']['shouyi'] = M('investor_detail')->where($ldyhbj)->sum('receive_interest');//+$bxlx;





        $start=strtotime(date("Y-m-1 0:0:0",strtotime($_POST["date"]."-01  0:0:1")));
        $end=strtotime(date("Y-m-t 23:59:59",strtotime($_POST["date"]."-01  0:0:1")));
        $map="b.lead_time >=".$start." and b.lead_time <=".$end." and investor_uid=".$this->uid." and i.status!=3"." and b.id != 1";
        $field = "b.borrow_duration,b.borrow_name,b.borrow_interest_rate,b.full_time,b.borrow_status as bstatus,b.id,i.investor_capital,i.add_time,i.status,i.borrow_id,i.deadline,i.id as inid,i.investor_way,i.member_interest_rate_id,i.bonus_id,b.borrow_img,i.is_experience,i.contractId,i.is_sign,i.step,b.lead_time";

        // $tenlist =  M('borrow_investor i')
        //     ->join('lzh_borrow_info b on b.id = i.borrow_id')
        //     ->field($field)
        //     ->where($map)->order('b.id desc')
        //     ->select();
        foreach($tenlist as $a=>$v){
            $vtime=(intval($v["lead_time"]));
            $tenlist[$a]["lead_time"]=$vtime;
            $tenlist[$a]["borrowname"]=cnsubstr($v["borrow_name"],10);
            $tenlist[$a]["hk_time_c"]=date("Y-m-d",$vtime);
            $tenlist[$a]["getFloatvalue_c"]=getFloatvalue($v['investor_capital']-(bounsmoney($v['bonus_id'])),2);
        }
        if(is_array($tenlist)&&!empty($tenlist)){
            $data["state"]="1";
            $data["list"]=$tenlist;

        }else{
            $data["state"]="0";
            $data["list"]=$tenlist;
        }
        $tpl_var['tenlist']=$data["list"];
        ajaxmsg($tpl_var,1);die;
    }

// 	public function qiandaobonus(){
// 		  //  	die;
// 				$sustain_day=M("member_sign")->where("uid={$this->uid}")->order("sign_time desc")
// 		->find();
// 				$map['uid'] = $this->uid;
// 		$va = getCreditsLog($map,1);
// 		$av=$va['list'][0];
// 		$va=strtotime(date("Y-m-1 0:0:0",time()));
// 			$map['sign_time']=["EGT",$va];
// 			$list = M("member_sign")->where($map)->order("sign_time desc")
// 		 ->select();
// // 			var_dump($sustain_day,1);	die;	
//  //var_dump($sustain_day['status']==0,1);die;
// 	if($sustain_day['status']==0){
// // 	 var_dump(2);die;
// 		 //pubBonusByRules($this->uid,1,$sustain_day['sustain_day']);
// 			 $member_bonus_rules = M('member_bonus_rules')->where("invest_money=".count($list).".00 and type=1")->find();	

 
		 
// 		  //var_dump($member_bonus_rules);die;
// // 	 var_dump(M('member_bonus_rules')->getlastsql());die;
// 	//money_type

// 		if($member_bonus_rules['money_type']==2){
// //			var_dump(12);
// //die;
// 				 $pubData = array(
// 			            'uid' => $this->uid,
// 			            'money_bonus' =>$member_bonus_rules['money_bonus'],
// 			            'money_type' =>$member_bonus_rules['money_type'],
// 			            'add_time' =>time(),
// 			            'start_time' => strtotime(date('Y-m-d 00:00:00')),
// 			            'end_time'   => strtotime(date("Y-m-d", strtotime("+1 months", strtotime(date("Y-m-d"))))),
// 			            'bonus_invest_min' => $member_bonus_rules['bonus_invest_min'],            
// 			            );
// 			       	$rs = M('member_bonus')->add($pubData); 
// 		}else if($member_bonus_rules['money_type']==1){
// //var_dump(11);
// //die;
// 					$addsign['integral']=$member_bonus_rules['money_bonus'];
// //							var_dump($addsign['integral']);
// //die;
// 				$zz = memberCreditsLog($this->uid, 13, intval($addsign['integral']), $info = "累计签到奖励积分");
// //				var_dump($zz);
// //die;
// 		}
	
					
				
				
			
// 	}
// //var_dump($member_bonus_rules['money_type']);
// //die;	
	
// //	else if($sustain_day['status']==0&&$sustain_day['sustain_day']==7){
// //		$z=1;//$_GET['ling']=7;
// //	}	else if($sustain_day['status']==0&&$sustain_day['sustain_day']==14){	
// //		$_GET['ling']=14;
// //	}	else if($sustain_day['status']==0&&$sustain_day['sustain_day']==21){
// //		$_GET['ling']=21;
// //	}else if($sustain_day['status']==0&&$sustain_day['sustain_day']==30){
// //		$_GET['ling']=30;
// //	}
	
// //			if($_GET['ling']==1){ 
// //					//添加1元优惠卷
// //					 $pubData = array(
// //		            'uid' => $this->uid,
// //		            'money_bonus' =>1,
// //		               'start_time' => strtotime(date('Y-m-d 00:00:00')),
// //		            'end_time'   => strtotime(date("Y-m-d", strtotime("+1 months", strtotime(date("Y-m-d"))))),
// //		            'bonus_invest_min' => 0,            
// //		            );
// //		       	$rs = M('member_bonus')->add($pubData); 
// //				//红包添加
// //		   
// //			}else if($_GET['ling']==14){ 
// //					//添加8元优惠卷
// //					 $pubData = array(
// //		            'uid' => $this->uid,
// //		            'money_bonus' =>8,
// //		               'start_time' => strtotime(date('Y-m-d 00:00:00')),
// //		            'end_time'   => strtotime(date("Y-m-d", strtotime("+1 months", strtotime(date("Y-m-d"))))),
// //		            'bonus_invest_min' => 0,            
// //		            );
// //		       	$rs = M('member_bonus')->add($pubData); 
// //				//红包添加
// //		   
// //			}else if($_GET['ling']==30){ 
// //					//添加20元优惠卷
// //					 $pubData = array(
// //		            'uid' => $this->uid,
// //		            'money_bonus' =>20,
// //		               'start_time' => strtotime(date('Y-m-d 00:00:00')),
// //		            'end_time'   => strtotime(date("Y-m-d", strtotime("+1 months", strtotime(date("Y-m-d"))))),
// //		            'bonus_invest_min' => 0,            
// //		            );
// //		       	$rs = M('member_bonus')->add($pubData); 
// //				//红包添加
// //		   
// //			}

// 			$data["status"]='1';
// 			$sustain_day=M("member_sign")->where("uid={$this->uid}")->save($data);
// 		//	var_dump(M("member_sign")->getlastsql());die;
//   			 header("location:/member/capital/qiandao");
// 		}

		public function qiandaobonus(){
		  //  	die;
		  	$va=strtotime(date("Y-m-1 0:0:0",time()));
		//var_dump(date("Y-m-1 0:0:0",$va));
		$mapa['sign_time']=["egt",$va];
		$date=$_GET["date"]-1;
		$mapa['uid']=["eq",$this->uid];
		$sustain_day=M("member_sign")->where($mapa)->order("sign_time ASC")->group('sign_time')->limit($date.",1")->select();
// 		var_dump(M("member_sign")->getlastsql(),1);die;
	  
 
	if($sustain_day[0]['status']==0){
// 	 var_dump(2);die;
		 //pubBonusByRules($this->uid,1,$sustain_day['sustain_day']);
		 
		 
// 				$map['uid'] = $this->uid;
// 		$va = getCreditsLog($map,1);
// 		$av=$va['list'][0];
// 		$va=strtotime(date("Y-m-1 0:0:0",time()));
// 			$map['sign_time']=["EGT",$va];
// 			$list = M("member_sign")->where($map)->order("sign_time desc")
// 		 ->select();
		 
		 
			 $member_bonus_rules = M('member_bonus_rules')->where("invest_money=".$_GET["date"].".00 and type=1")->find();	

 
		 
		  //var_dump($member_bonus_rules);die;
		  //var_dump($member_bonus_rules['money_type']);die;
// 	 var_dump(M('member_bonus_rules')->getlastsql());die;
	//money_type

		if($member_bonus_rules['money_type']==2){ 
				 $pubData = array(
			            'uid' => $this->uid,
			            'money_bonus' =>$member_bonus_rules['money_bonus'],
			            'money_type' =>$member_bonus_rules['money_type'],
			            'add_time' =>time(),
			            'start_time' => strtotime(date('Y-m-d 00:00:00')),
			            'end_time'   => strtotime(date("Y-m-d 23:59:59", strtotime("+1 months", strtotime(date("Y-m-d"))))),
			            'bonus_invest_min' => $member_bonus_rules['bonus_invest_min'],            
			            );
			       	$rs = M('member_bonus')->add($pubData); 
		}else if($member_bonus_rules['money_type']==1){
                    // var_dump(2);
                    // die;
					$addsign['integral']=$member_bonus_rules['money_bonus'];
					 
				$zz = memberCreditsLog($this->uid, 13, intval($addsign['integral']), $info = "累计签到奖励积分");
//				var_dump($zz);
//die;
        		} 	 
        	} 
			$data["status"]='1';
			$wheredata["id"]=$sustain_day[0]["id"];
			$wheredata["uid"]=$this->uid;	
		    $res= M("member_sign")->where($wheredata)->save($data);
// 			var_dump(M("member_sign")->getlastsql());die;
   			// header("location:member/capital/qiandao");
            $this->redirect('Capital/qiandao');
		}
	
	public function qiandaodong(){
	 
//		id
//sign_time
//uid
//sustain_day
//cumulative_day
//cumulative_integral
		$list = M("member_sign")->where("uid={$this->uid}")->order("sign_time desc")
		 ->find();
		 $addsign['sign_time']=strtotime(date("Y-m-d 0:0:0",time()));
		//$addsign['sign_time']=time();
		$addsign['uid']=$this->uid;
		
			//php获取今日起始时间戳和结束时间戳
			
			

 	$endYesterday=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;
 	 $beginYesterday=mktime(0,0,0,date('m'),date('d'),date('Y'));
		if($beginYesterday<=intval($list['sign_time'])){
			if( (intval($list['sign_time'])< $endYesterday)){
					ajaxmsg("已签到",0);
			} 
		
		}	
//	var_dump($endYesterday,$beginYesterday,intval($list['sign_time']))	;	
//die;		
	
			//php获取昨日起始时间戳和结束时间戳
			
			
			
 	$beginYesterday=mktime(0,0,0,date('m'),date('d')-1,date('Y'));
 	$endYesterday=mktime(0,0,0,date('m'),date('d'),date('Y'))-1;
	
//var_dump($beginYesterday,'<br>',intval($list['sign_time']),'<br>',$endYesterday);die;
		if($beginYesterday<intval($list['sign_time'])){
		    	$addsign['sustain_day']=$list['sustain_day']+1;
		/*	if( (intval($list['sign_time'])< $endYesterday)){
					$addsign['sustain_day']=$list['sustain_day']+1;
			}else{
					$addsign['sustain_day']=1;
			}*/
		
		}else{
			$addsign['sustain_day']=1;
		}
		 
if($addsign['sustain_day']==1){
	$addsign['money_type']=2;
}else if($addsign['sustain_day']==7){
	$addsign['money_type']=1;
} else if($addsign['sustain_day']==14){
	$addsign['money_type']=2;
}else if($addsign['sustain_day']==21){
	$addsign['money_type']=1;
}else if($addsign['sustain_day']==30){
	$addsign['money_type']=2;
}   
//			echo $addsign['sustain_day'];die;
		if($addsign['sustain_day']==1){
			$addsign['integral']=10;
			//添加1元优惠卷
//			 $pubData = array(
//          'uid' => $this->uid,
//          'money_bonus' =>1,
//             'start_time' => strtotime(date('Y-m-d 00:00:00')),
//          'end_time'   => strtotime(date("Y-m-d", strtotime("+1 months", strtotime(date("Y-m-d"))))),
//          'bonus_invest_min' => 0,            
//          );
//     	$rs = M('member_bonus')->add($pubData); 
//		//红包添加
   
		}else if($addsign['sustain_day']<7){
			$addsign['integral']=10;
		
		}else if($addsign['sustain_day']<14){
				$addsign['integral']=10;
				//$addsign['integral']=200;
			
		 
			
		}else if($addsign['sustain_day']<21){
			$addsign['integral']=10;
//			//红包添加
//				 $pubData = array(
//          'uid' => $this->uid,
//          'money_bonus' =>8,
//              'start_time' => strtotime(date('Y-m-d 00:00:00')),
//              'end_time'   => strtotime(date("Y-m-d", strtotime("+1 months", strtotime(date("Y-m-d"))))),
//          'bonus_invest_min' => 0,            
//          );
//     	$rs = M('member_bonus')->add($pubData); 
//		//红包添加
       //pubBonus($pubData,2);
			// pubBonusByRules($this->uid,2);
			//添加红包8元优惠卷
		}else if($addsign['sustain_day']<30){
			$addsign['integral']=10;
			
			//$addsign['integral']=1000;
			 
		}else if($addsign['sustain_day']>30){
			$addsign['integral']=10; 
//			//红包添加
//						 $pubData = array(
//          'uid' => $this->uid,
//          'money_bonus' =>20,
//          'start_time' => strtotime(date('Y-m-d 00:00:00')),
//              'end_time'   => strtotime(date("Y-m-d", strtotime("+1 months", strtotime(date("Y-m-d"))))),
//          'bonus_invest_min' => 0,            
//          );
//			$rs = M('member_bonus')->add($pubData); 
//			//红包添加
      // pubBonus($pubData,2);
			// pubBonusByRules($this->uid,3);
			//添加红包20元优惠卷
		}
		//判断
//		var_dump(M('member_bonus')->getlastsql());
//		 die;
		$addsign['cumulative_day']=M("member_sign")->where("uid={$this->uid}")->order("sign_time desc")
		->limit('0,30')->count;

		$zz = memberCreditsLog($this->uid, 13, $addsign['integral'], $info = "签到积分");
			$list = M("member_sign")->where("uid={$this->uid}")->order("sign_time desc")
		->limit('0,30')->add($addsign);
		ajaxmsg(sustain_day);die;	
	 return sustain_day;
	 
//		var_dump(M("member_sign")->getlastsql());
//		var_dump($list);
	
		 
    }

// 		public function qiandaobonus(){
// 				$sustain_day=M("member_sign")->where("uid={$this->uid}")->order("sign_time desc")
// 		->find();	
// 			//var_dump($sustain_day);		
// 	if($sustain_day['status']==0){
// 		//var_dump(1);
// 		 //pubBonusByRules($this->uid,1,$sustain_day['sustain_day']);
// 			 $member_bonus_rules = M('member_bonus_rules')->where("invest_money=".$sustain_day['sustain_day'].".00 and type=1")->find();	
			 
// 			// var_dump($member_bonus_rules);
// 	// var_dump(M('member_bonus_rules')->getlastsql());die;
// 	//money_type
// 		if($member_bonus_rules['money_type']==2){
// //			var_dump(12);
// //die;
// 				 $pubData = array(
// 			            'uid' => $this->uid,
// 			            'money_bonus' =>$member_bonus_rules['money_bonus'],
// 			            'money_type' =>$member_bonus_rules['money_type'],
// 			            'add_time' =>time(),
// 			            'start_time' => strtotime(date('Y-m-d 00:00:00')),
// 			            'end_time'   => strtotime(date("Y-m-d", strtotime("+1 months", strtotime(date("Y-m-d"))))),
// 			            'bonus_invest_min' => $member_bonus_rules['bonus_invest_min']           
// 			            );
// 			       	$rs = M('member_bonus')->add($pubData); 
// 		}else if($member_bonus_rules['money_type']==1){
// //var_dump(11);
// //die;
// 					$addsign['integral']=$member_bonus_rules['money_bonus'];
// //							var_dump($addsign['integral']);
// //die;
// 				$zz = memberCreditsLog($this->uid, 13, intval($addsign['integral']), $info = "累计签到奖励积分");
// //				var_dump($zz);
// //die;
// 		}
	
					
				
				
			
// 	}
// //var_dump($member_bonus_rules['money_type']);
// //die;	
	
// //	else if($sustain_day['status']==0&&$sustain_day['sustain_day']==7){
// //		$z=1;//$_GET['ling']=7;
// //	}	else if($sustain_day['status']==0&&$sustain_day['sustain_day']==14){	
// //		$_GET['ling']=14;
// //	}	else if($sustain_day['status']==0&&$sustain_day['sustain_day']==21){
// //		$_GET['ling']=21;
// //	}else if($sustain_day['status']==0&&$sustain_day['sustain_day']==30){
// //		$_GET['ling']=30;
// //	}
	
// //			if($_GET['ling']==1){ 
// //					//添加1元优惠卷
// //					 $pubData = array(
// //		            'uid' => $this->uid,
// //		            'money_bonus' =>1,
// //		               'start_time' => strtotime(date('Y-m-d 00:00:00')),
// //		            'end_time'   => strtotime(date("Y-m-d", strtotime("+1 months", strtotime(date("Y-m-d"))))),
// //		            'bonus_invest_min' => 0,            
// //		            );
// //		       	$rs = M('member_bonus')->add($pubData); 
// //				//红包添加
// //		   
// //			}else if($_GET['ling']==14){ 
// //					//添加8元优惠卷
// //					 $pubData = array(
// //		            'uid' => $this->uid,
// //		            'money_bonus' =>8,
// //		               'start_time' => strtotime(date('Y-m-d 00:00:00')),
// //		            'end_time'   => strtotime(date("Y-m-d", strtotime("+1 months", strtotime(date("Y-m-d"))))),
// //		            'bonus_invest_min' => 0,            
// //		            );
// //		       	$rs = M('member_bonus')->add($pubData); 
// //				//红包添加
// //		   
// //			}else if($_GET['ling']==30){ 
// //					//添加20元优惠卷
// //					 $pubData = array(
// //		            'uid' => $this->uid,
// //		            'money_bonus' =>20,
// //		               'start_time' => strtotime(date('Y-m-d 00:00:00')),
// //		            'end_time'   => strtotime(date("Y-m-d", strtotime("+1 months", strtotime(date("Y-m-d"))))),
// //		            'bonus_invest_min' => 0,            
// //		            );
// //		       	$rs = M('member_bonus')->add($pubData); 
// //				//红包添加
// //		   
// //			}

// 			$data["status"]='1';
// 			$sustain_day=M("member_sign")->where("uid={$this->uid}")->save($data);
// 		//	var_dump(M("member_sign")->getlastsql());die;
//   			 header("location:/member/capital/qiandao");
// 		}
	
	
	public function zindex(){
		$this->display();
    } 
    public function summary(){
	
		$this->assign("vo",getMemberMoneySummary($this->uid));
		////////////////////////////////////////////////////////////////
		$minfo =getMinfo($this->uid,true);
		$levename=getLeveName($minfo["credits"]);
		$levetixian=getLeveTixian($minfo["credits"]);
		$this->assign( "levename",$levename);
		$this->assign( "levetixian",$levetixian);
		$this->assign("minfo",$minfo);
		////////////////////////////////////////////////////////////////////
		$data['html'] = $this->fetch();
		exit(json_encode($data));
    }
    public function detail(){
		$logtype = C('MONEY_LOGS');
		$this->assign('log_type',$logtype);
		$map['uid'] = $this->uid;
		//$map['type']= array('not in','15,28,24');
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
		$list = getMoneyLog($map,15);
		$this->assign('search',$search);
		$this->assign("list",$list['list']);		
		$this->assign("pagebar",$list['page']);	
        $this->assign("query", http_build_query($search));
		$this->display();
    }
     public function hongbao(){
		$logtype = C('MONEY_LOG');
		$this->assign('log_type',$logtype);
		$map['uid'] = $this->uid;
		//$map['type']= array('not in','15,28,24');
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
		$map['type'] = array('in','1,32,50');
		$list = getMoneyLog($map,15);
		$this->assign('search',$search);
		$this->assign("list",$list['list']);		
		$this->assign("pagebar",$list['page']);	
        $this->assign("query", http_build_query($search));
		$this->display();
    }
	
	public function export(){
		import("ORG.Io.Excel");
		$map=array();
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
				$row[$i]['card_mianfei'] = ($v['account_money']+$v['back_money']);
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