<?php
// 本类由系统自动生成，仅供测试用途
class CapitalAction extends MCommonAction {
    
    public function ascssac(){
        	$members=M("members")->select();
        	foreach($members as $a=>$v){
        	    	$mx=M("borrow_investor  i")->join("lzh_borrow_info b on i.borrow_id=b.id")->where(" i.investor_uid={$v['id']}   and b.borrow_status=6 and i.status != 3 ")->sum("investor_capital");
        	   
                		$zz=M("member_money m") ->where("uid=".$v['id'])->find();
                		if($zz["update_time"]<=0){
                		    	M("member_money m") ->where("uid=".$v['id'])->save(["money_collect"=>0]);
                			M("member_money m") ->where("uid=".$v['id'])->save(["money_collect"=> $mx,"update_time"=>time()]);
                		}
                		$ic=getincode();
                		 
                	M("members")->where("id =".$v['id'])->save(["incode"=>$ic])	;
        	}
         
    }   
    public function updatetime(){
        	/* M("lzh_borrow_info")->save(["add_time"=>"add_time+28800"]);
        
        	
lzh_borrow_info
（
add_time
collect_time
full_time
deadline
first_verify_time
second_verify_time
start_time 
car_reg_time
up_time
borrow_time
lead_time
）

lzh_borrow_investor
（
 
add_time
deadline 
repayment_time 
）
lzh_investor_detail（
 
repayment_time 
deadline 
substitute_time 
starttime
endtime 
）

lzh_member_bonus（
 
add_time
start_time
end_time
use_time 
）

lzh_member_sign（
sign_time
）

lzh_member_withdraw（ 
add_time 
deal_time   
）


        	
        	*/
 
         
    }
      public function showidnexs(){
	 	recoverBonus($this->uid);
		$ucLoing = de_xie($_COOKIE['LoginCookie']);
		setcookie('LoginCookie','',time()-10*60,"/");
		$uid = $this->uid;		
        //投资项目
        // $_POST["date"]="2019-10-24";
        // $_POST["ids"]="1356";
         $oldtime =$_POST["date"]." 00:00:00"; 

         $emp_time =$_POST["date"].' 23:59:59' ; 
         $stime = strtotime($oldtime); 
       //  var_dump($stime);
         $etime = strtotime($emp_time);
        // $map['b.lead_time'] =array('gt',$stime1);
        // $map['b.lead_time'] = array('lt',$etime);
     
        // $map['investor_uid'] = $this->uid;
        $ids=$_POST["ids"];

        //  $map="b.lead_time >=".$stime." and b.lead_time <=".$etime." and investor_uid=".$this->uid." and i.status!=3"." and b.id != 1";
        $map="b.id in ($ids) and investor_uid=".$this->uid." and i.status!=3"." and b.id != 1";

        $field = "b.hkday,b.total,b.borrow_duration,b.borrow_name,b.borrow_interest_rate,b.full_time,b.borrow_status as bstatus,b.id,i.investor_capital,i.add_time,i.status,i.borrow_id,i.deadline,i.id as inid,i.investor_way,i.member_interest_rate_id,i.bonus_id,b.borrow_img,i.is_experience,i.contractId,i.is_sign,i.step,b.lead_time";
	 
		//分页处理
	  
        $tenlist =  M('borrow_investor i')
        ->join('lzh_borrow_info b on b.id = i.borrow_id')
        ->field($field)
        ->where($map)->order('b.id desc')
         ->select();
       	$benjine=0;
       	$shouyie=0;
       	$yhk=0;
         foreach($tenlist as $a=>$v){
         	//var_dump($v);
             $vtime=(intval($v["lead_time"]));
             
             $tenlist[$a]["borrowname"]=cnsubstr($v["borrow_name"],10);
             $tenlist[$a]["hk_time_c"]=date("Y-m-d",$vtime); 
             $tenlist[$a]["getFloatvalue_c"]=getFloatvalue($v['investor_capital']-(bounsmoney($v['bonus_id'])),2); 


             $sort_order=1;

             if($v["total"]>1){
             	if(!empty($v["hkday"])){
             		$yue=$v["hkday"];
					$dt=date('Y-n-j',$v['lead_time']);
					for ($i=0; $i < $v["total"]; $i++) { 
						$tenlist[$a]["hkzt2"]=date('Y-n-j',strtotime("$dt+".$yue*$i."days"));
						if(date('Y-n-j',strtotime("$dt+".$yue*$i."days"))==$_POST["date"]){
							$sort_order=$i+1;
							$b=strtotime("$dt+".$yue*$i."days");
						}
					}
             	}else{
             		$yue=$v["borrow_duration"]/$v["total"];
					$dt=date('Y-n-j',$v['lead_time']);
					for ($i=0; $i < $v["total"]; $i++) { 
						$tenlist[$a]["hkzt2"]=date('Y-n-j',strtotime("$dt+".$yue*$i."month"));
						if(date('Y-n-j',strtotime("$dt+".$yue*$i."month"))==$_POST["date"]){
							$sort_order=$i+1;
							$b=strtotime("$dt+".$yue*$i."month");
						}
					}
             	}
				

			}else{
				  $b=$v["lead_time"];
			}
			$mpp["invest_id"]=$v["inid"];
			$mpp["status"]=1;
			$mpp["sort_order"]=$sort_order;
			$mpp["investor_uid"]=$this->uid;
			$minfo=M("investor_detail")->where($mpp)->find();
			// if($minfo){
			// 	$tenlist[$a]["hkzt"]=1;
			// }else{
			// 	$tenlist[$a]["hkzt"]=0;
			// }
			
			// if($v["total"]>12){
			// 	$lixi=getFloatvalue(($v["borrow_interest_rate"]/100*($v["total"]/12)*$v["investor_capital"]/$v["total"]),2);
			// }else{
			// 	$lixi=getFloatvalue(($v["borrow_interest_rate"]/100*$v["investor_capital"]/$v["total"]),2);
			// }
			//var_dump($lixi);
			if($minfo){
				$tenlist[$a]["hkzt"]=1;
				$tenlist[$a]["shouyi"]=$minfo["receive_interest"]+$minfo["receive_capital"];
				$yhk+=1;
				$shouyie+=$minfo["receive_interest"];

			}else{
				$tenlist[$a]["hkzt"]=0;
				//$tenlist[$a]["hklx"]="未回款";
				if($sort_order==$v["total"]){
					$tenlist[$a]["shouyi"]=$v["investor_capital"];
				}else{
					$tenlist[$a]["shouyi"]='';
				}
			}

			if($sort_order==$v["total"]){
				$benjine+=$v["investor_capital"];
				if(empty($minfo)){
					$tenlist[$a]["hklx"]="本金";  
				}else{
					$tenlist[$a]["hklx"]="本金+收益";
					
				}
			}else{
				$tenlist[$a]["hklx"]="第".$sort_order."次收益";
			}

			$tenlist[$a]["hk_time_c"]=date("Y-m-d",$b);

			if($sort_order==$v["total"]){
				$tenlist[$a]["bj"]=1;
			}else{
				$tenlist[$a]["bj"]=0;
			}
         }
         if(is_array($tenlist)&&!empty($tenlist)){
               $data["state"]="1";
         	   $data["list"]=$tenlist;

         	   $hao=explode('-',$_POST["date"]);
        	   
        	   if(count($tenlist)==$yhk){
        	   		$data["hks"]="<p  style='border: 0; border-bottom: 10px solid #fff; border-top: 10px solid #fff;'>".$hao[2]."号总回款:".($benjine+$shouyie)."(本金".$benjine."+收益".$shouyie.")</p>";
        	   }  
         }else{
               $data["state"]="0";
               $data["list"]=$tenlist;
         }
        //=count($tenlist);

        // $data["benjine"]=$benjine;
        // $data["shouyie"]=$shouyie;
        //var_dump($data);exit();	
        echo json_encode($data);
        // var_dump( M('borrow_investor i')->getlastsql());die;
       
 
    }
    
    public function index(){
     
		// $ minfo $sql="SELECT SUM(investor_capital) AS tp_sum FROM lzh_borrow_investor i LEFT JOIN lzh_borrow_info b on i.borrow_id=b.id WHERE i.investor_uid=1334 and b.borrow_status=6 LIMIT 1"
   
    	
    	$mx=M("borrow_investor  i")->join("lzh_borrow_info b on i.borrow_id=b.id")->where(" i.investor_uid={$this->uid}   and b.borrow_status=6  and i.status !=3")->sum("investor_capital");
	  
    	$this->assign("mx",$mx);
		$mx1=M("borrow_investor i")->join("lzh_borrow_info b on i.borrow_id=b.id")->where(" i.investor_uid={$this->uid} and b.pid = 1  and b.borrow_status=6 and i.status !=3 ")->sum("investor_capital");
    	$this->assign("mx1",$mx1);
		$mx2=M("borrow_investor i")->join("lzh_borrow_info b on i.borrow_id=b.id")->where(" i.investor_uid={$this->uid} and b.pid = 2  and b.borrow_status=6 and i.status !=3  ")->sum("investor_capital");
    	$this->assign("mx2",$mx2);
		$mx3=M("borrow_investor i")->join("lzh_borrow_info b on i.borrow_id=b.id")->where(" i.investor_uid={$this->uid} and b.pid = 3  and b.borrow_status=6 and i.status !=3 ")->sum("investor_capital");
    	$this->assign("mx3",$mx3);
		$ms=M("investor_detail i")->join("lzh_borrow_info b on i.borrow_id=b.id")->where("investor_uid={$this->uid} and b.borrow_status!=5   and i.status !=3 ")->sum("receive_interest");
	//	var_dump(M("borrow_investor i")->getlastsql());
    	$this->assign("ms",$ms);
		$ms1=M("investor_detail i")->join("lzh_borrow_info b on i.borrow_id=b.id")->where("investor_uid={$this->uid} and b.pid = 1  and b.borrow_status=7  ")->sum("receive_interest");
    	$this->assign("ms1",$ms1);
		$ms2=M("investor_detail i")->join("lzh_borrow_info b on i.borrow_id=b.id")->where("investor_uid={$this->uid} and b.pid = 2  and b.borrow_status=7    ")->sum("receive_interest");
    	$this->assign("ms2",$ms2);
		$ms3=M("investor_detail i")->join("lzh_borrow_info b on i.borrow_id=b.id")->where("investor_uid={$this->uid} and b.pid = 3  and b.borrow_status=7    ")->sum("receive_interest");
    	$this->assign("ms3",$ms3);
		 $va=m("borrow_investor")->field($field)->where("investor_uid = {$this->uid}")->group("status")->select();
		 //var_dump(1);die;
		 // investor_detail $minfo['receive_interests'] = M('investor_detail')->where("investor_uid={$uid} ")->sum('receive_interest');
		 //var_dump(m("borrow_investor")->getlastsql());
		//receive_capital
		//var_dump(M("borrow_investor i")->getlastsql()); 
		//var_dump($mx);
		$this->assign("vo",getMemberMoneySummary($this->uid));
		////////////////////////////////////////////////////////////////
		$minfo =getMinfo($this->uid,true);
		
		
			/*$member_withdraw=M("member_withdraw")->where("investor_uid={$this->uid} and withdraw_status=0")->sum("receive_interest");*/
		//echo M("member_withdraw")->getlastsql();die; 
			//echo $member_withdraw;die;
			$member_withdraw=M("member_withdraw")->where("uid={$this->uid} and (withdraw_status=0 or withdraw_status=1)")->sum("withdraw_money");
			//echo M("member_withdraw")->getlastsql();die; 
    	$this->assign("member_withdraw",$member_withdraw);
		
		$levename=getLeveName($minfo["credits"]);
		$levetixian=getLeveTixian($minfo["credits"]);
		$this->assign( "levename",$levename);
		$this->assign( "levetixian",$levetixian);
		$this->assign("minfo",$minfo);
		////////////////////////////////////////////////////////////////////
//		$data['html'] = $this->fetch();
//		exit(json_encode($data));
 
		$this->display();
    }
	/*
	 public function index(){
		//$sql="SELECT SUM(investor_capital) AS tp_sum FROM lzh_borrow_investor i LEFT JOIN lzh_borrow_info b on i.borrow_id=b.id WHERE i.investor_uid=1334 and b.borrow_status=6 LIMIT 1"
    	$mx=M("borrow_investor  i")->join("lzh_borrow_info b on i.borrow_id=b.id")->where(" i.investor_uid={$this->uid} and  i.status=5")->sum("investor_capital");
		//var_dump($mx);die;

		  //var_dump(M("borrow_investor  i")->getlastsql());
    	$this->assign("mx",$mx);
		$mx1=M("borrow_investor i")->join("lzh_borrow_info b on i.borrow_id=b.id")->where(" i.investor_uid={$this->uid} and b.pid = 1 and  i.status=5")->sum("investor_capital");
    	$this->assign("mx1",$mx1);
		$mx2=M("borrow_investor i")->join("lzh_borrow_info b on i.borrow_id=b.id")->where(" i.investor_uid={$this->uid} and b.pid = 2  and  i.status=5")->sum("investor_capital");
    	$this->assign("mx2",$mx2);
		$mx3=M("borrow_investor i")->join("lzh_borrow_info b on i.borrow_id=b.id")->where(" i.investor_uid={$this->uid} and b.pid = 3 and   i.status=5")->sum("investor_capital");
    	$this->assign("mx3",$mx3);
		$ms=M("borrow_investor")->where("investor_uid={$this->uid} and  i.status!=5")->sum("receive_capital");
    	$this->assign("ms",$ms);
		$ms1=M("borrow_investor i")->join("lzh_borrow_info b on i.borrow_id=b.id")->where("investor_uid={$this->uid} and b.pid = 1  and  i.status!=5")->sum("receive_capital");
    	$this->assign("ms1",$ms1);
		$ms2=M("borrow_investor i")->join("lzh_borrow_info b on i.borrow_id=b.id")->where("investor_uid={$this->uid} and b.pid = 2  and i.status!=5")->sum("receive_capital");
    	$this->assign("ms2",$ms2);
		$ms3=M("borrow_investor i")->join("lzh_borrow_info b on i.borrow_id=b.id")->where("investor_uid={$this->uid} and b.pid = 3  and  i.status!=5")->sum("receive_capital");
    	$this->assign("ms3",$ms3);
		
		//receive_capital
		//var_dump(M("borrow_investor i")->getlastsql()); 
		//var_dump($mx);
		$this->assign("vo",getMemberMoneySummary($this->uid));
		////////////////////////////////////////////////////////////////
		$minfo =getMinfo($this->uid,true);
		$levename=getLeveName($minfo["credits"]);
		$levetixian=getLeveTixian($minfo["credits"]);
		$this->assign( "levename",$levename);
		$this->assign( "levetixian",$levetixian);
		$this->assign("minfo",$minfo);
		////////////////////////////////////////////////////////////////////
//		$data['html'] = $this->fetch();
//		exit(json_encode($data));
		$this->display();
    }
	
	**/
    public function xiangqing(){
    	//在投的
 	 
		//分页处理

    	//b.borrow_status in(2,4)回收中的
    	$map['i.investor_uid']=$this->uid;
    	if($_GET['borrow_status']==2){
    			$map['b.borrow_status']=['in','6,7'];
    			    	import("ORG.Util.Page");
            		$count = M('borrow_investor i')->where($map)->count('b.id');
            		$p = new Page($count, 15);
            		$page = $p->show();
            		$Lsql = "{$p->firstRow},{$p->listRows}";
            		//分页处理
            	 	$this->assign("page",$page);
                      
               
             	$mx3=M("investor_detail i")->join("lzh_borrow_info b on i.borrow_id=b.id")->where($map)->order('i.id desc')->	limit($Lsql)->select();
             //	var_dump(M("investor_detail i")->getlastsql());die;
    	}else{
    		$map['b.borrow_status']=['in','6,7'];
    		    	import("ORG.Util.Page");
		$count = M('borrow_investor i')->where($map)->count('b.id');
		$p = new Page($count, 15);
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		//分页处理
	 	$this->assign("page",$page);
	 			$field= 'i.*,b.borrow_status';
             
             	$mx3=M("borrow_investor i")->field($field)->join("lzh_borrow_info b on i.borrow_id=b.id")->where($map)->order('i.id desc')->	limit($Lsql)->select();
    	}
    	//	$map['b.borrow_status']=['in','7'];

      
      
    
		$this->assign( "list",$mx3);
		
		$this->assign( "borrow_status",empty($_GET['borrow_status'])?0:$_GET['borrow_status']);
    	$this->display();
    }
	public function chargelog(){
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
		$list = getChargeLog($map,10);
//		var_dump($list);die;
		$this->assign('search',$search);
		$this->assign("list",$list['list']);
		$this->assign("pagebar",$list['page']);
		$this->assign("success_money",$list['success_money']);
		$this->assign("fail_money",$list['fail_money']);
		$model=M('member_banks');
		$mbank = $model->where('uid = '.$this->uid)->find();
		$this->assign('mbank',$mbank);
		$this->display();
    }
    
    public function withdrawlog(){
    	
		if($_GET['start_time']&&$_GET['end_time']){
			$_GET['start_time'] = strtotime($_GET['start_time']." 00:00:00");
			$_GET['end_time'] = strtotime($_GET['end_time']." 23:59:59");
			
			if($_GET['start_time']<$_GET['end_time']){
				$map['add_time']=array("between","{$_GET['start_time']},{$_GET['end_time']}");
				$search['start_time'] = $_GET['start_time'];
				$search['end_time'] = $_GET['end_time'];
			}
		}
		$map['uid'] = $this->uid;
		$list = getWithDrawLog($map,15);
		$this->assign('search',$search);
		$this->assign("list",$list['list']);
		$this->assign("pagebar",$list['page']);
		$model=M('member_banks');
		$mbank = $model->where('uid = '.$this->uid)->find();
		$this->assign('mbank',$mbank);
		$this->display();
    }
	
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
		     M("member_sign")->where($wheredata)->save($data);
// 			var_dump(M("member_sign")->getlastsql());die;
   			 header("location:/wapmember/capital/qiandao");
		}
	
  
	public function qiandao(){
	    $map['uid']=$this->uid;
	    	$field= 'b.uid,b.gid,b.jifen,b.jine,b.num,b.add_time,b.add_ip,b.action,m.id mid,m.user_name,x.*,x.id ma_id,b.id id';
		$zvolist = M('order b')->field($field)->join("lzh_members m ON m.id=b.uid")->join("lzh_market x ON x.id=b.gid")->where($map)->limit(2)->order("b.id DESC")->select();
	    	$this->assign("zvolist",$zvolist);
	   //  	var_dump($zvolist);die;
	    	
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
		//	var_dump($sustain_day2); die;
	 	//var_dump($sustain_day['sustain_day'],$sustain_day2['sustain_day']); die;
		if($sustain_day['sustain_day']>$sustain_day2['sustain_day']){
		    	$sustain_day['sustain_day']=$sustain_day['sustain_day']-$sustain_day2['sustain_day'];
		}else{
		    	$sustain_day['sustain_day']=$sustain_day['sustain_day'];
		}

		
		
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
    	}else	if(isset($list[29])&&$list[29]['status']==1&&count($list)>=1){
    		$this->assign("ling5",2);
    		
    	}else{
    	    	$this->assign("ling5",0);
    	}
 
	//var_dump(count($list),$list['status']);die;
	
		$this->assign("ling",count($list));
 
	
		$this->assign("volist",$list);
		$minfo =getMinfo($this->uid,true);
		$this->assign("minfo", $minfo);
		//待回款日期
		$dhktime=array();
		$hktime=array();
				//->field("lzh_borrow_info.lead_time,lzh_borrow_info.borrow_duration")->where("lzh_borrow_investor.investor_uid={$this->uid} and lzh_borrow_info.borrow_status=6")
		$dhkbinfo = m("borrow_info")->alias('a')->field("a.lead_time,a.borrow_duration")->join('lzh_borrow_investor b on a.id = b.borrow_id')->where("b.investor_uid={$this->uid} and a.borrow_status=6") ->select();
		 //	var_dump($dhkbinfo,m("borrow_info")->getlastsql());
		foreach ($dhkbinfo as $k => $val) {
			$dhktime[]=date('Y-n-j',$val['lead_time']);
			//+$val['borrow_duration']*30*60*60*24
		//	var_dump($ccccccccccccccc);
	
		}
		$hkbinfo =m("borrow_info")->alias('a')->field("a.lead_time,a.borrow_duration")->join('lzh_borrow_investor b on a.id = b.borrow_id')->where("b.investor_uid={$this->uid} and a.borrow_status=7") ->select();
		foreach ($hkbinfo as $k => $val) {
			$hktime[]=date('Y-n-j',$val['lead_time']);//$val['borrow_duration']*30*60*60*24
		//	var_dump($val['lead_time']);
		}
		$uid=$this->uid;
		
 
 
 $start=strtotime(date("Y-m-1 0:0:0",time())); 
   
		$end=strtotime(date("Y-m-t 23:59:59",time()));  
 
		
		
//已收本金
		$zz= M('borrow_investor bi')->join('lzh_borrow_info b on b.id = bi.borrow_id')->where("investor_uid={$uid} and b.borrow_status =7  and b.lead_time>={$start} and b.lead_time<={$end} and bi.status!=3")->sum('investor_capital');
		//待收本金
		$dsbj= M('borrow_investor bi')->join('lzh_borrow_info b on b.id = bi.borrow_id')->where("investor_uid={$uid} and b.borrow_status =6  and b.lead_time>={$start} and b.lead_time<={$end}  and bi.status!=3")->sum('investor_capital');
	//	var_dump(M('borrow_investor bi')->getlastsql());die;
		//已返利润
$minfo["receive_interests"]	=M('investor_detail bi')->join('lzh_borrow_info b on b.id = bi.borrow_id')->where("investor_uid={$uid}  and b.borrow_status =7 and b.lead_time>={ $start} and b.lead_time<={$end}  and bi.status!=3")->sum('receive_interest');
//M('investor_detail')->where("investor_uid={$uid} ")->sum('receive_interest');
	//	$yflr= M('borrow_investor bi')->join('lzh_borrow_info b on b.id = bi.borrow_id')->where("investor_uid={$uid} and b.borrow_status =7")->sum('receive_capital');
		//var_dump($zz); 
//echo 121;

		$article = m("article_category")->field('type_content')->where('id=524')->find();
        $this->assign( "article",$article);

		$this->assign("minfo", $minfo);
		$this->assign("zz", $zz);
		$this->assign("dsbj", $dsbj);
		$this->assign("yflr",$yflr); 
		$this->assign("dhktime", json_encode($dhktime));
		$this->assign("hktime", json_encode($hktime));
		$this->display();
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
    
    public function zindexapi(){
        // $_POST["date"]="2019-1";
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
        // echo m("members m")->getlastsql();exit;
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
// //	 $va2=strtotime(date("Y-m-1 0:0:0",time()));
  	 // var_dump($_POST);
  	 // var_dump(date("Y-m-t",time()));
  	 // die;
 
        //已收本金
	// $tpl_var['sum']['receive_interests']=M('borrow_investor bi')->join('lzh_borrow_info b on b.id = bi.borrow_id')->where("investor_uid={$uid} and b.borrow_status =7  and b.lead_time>={$start} and b.lead_time<={$end} and bi.status!=3")->sum('investor_capital'); 
	// 	//待收本金
	// $tpl_var['sum']['investor_capital']= M('borrow_investor bi')->join('lzh_borrow_info b on b.id = bi.borrow_id')->where("investor_uid={$uid} and b.borrow_status =6 and b.lead_time>={$start} and b.lead_time<={$end} and bi.status!=3")->sum('investor_capital');
	// 	//已返利润
	// $tpl_var['sum']['shouyi'] = M('investor_detail bi')->join('lzh_borrow_info b on b.id = bi.borrow_id')->where("investor_uid={$uid}  and b.borrow_status =7 and b.lead_time>={$start}  and b.lead_time<={$end}  ")->sum('receive_interest');
  
  
  
  	 
  //       $start=strtotime(date("Y-m-1 0:0:0",strtotime($_POST["date"]."-01  0:0:1"))); 
   
		// $end=strtotime(date("Y-m-t 23:59:59",strtotime($_POST["date"]."-01  0:0:1")));  
        // $map['b.lead_time'] =array('gt',$stime1);
        // $map['b.lead_time'] = array('lt',$etime);
     
        // $map['investor_uid'] = $this->uid;
        
          //$map="b.lead_time >=".$start." and b.lead_time <=".$end." and investor_uid=".$this->uid." and i.status!=3"." and b.id != 1";


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


	    //$hkid=$ids['b'][date("Y-n",time())]['hk'];
         foreach($tenlist as $a=>$v){
             $tenlist[$a]["borrowname"]=cnsubstr($v["borrow_name"],10);
             // $tenlist[$a]["hk_time_c"]=date("Y-m-d",$vtime); 
             $tenlist[$a]["getFloatvalue_c"]=getFloatvalue($v['investor_capital']-(bounsmoney($v['bonus_id'])),2);

          

            $sort_order=1;
             if($v["total"]>1){
             	if(!empty($v["hkday"])){
             		$yue=$v["hkday"];
					$dt=date('Y-n-j',$v['lead_time']);
					for ($i=0; $i < $v["total"]; $i++) { 
						if(date('Y-n',strtotime("$dt+".$yue*$i." days"))==$_POST["date"]){
							$ab=strtotime("$dt+".$yue*$i."days");
							$sort_order=$i+1;
						}
					}
             	}else{
					$yue=$v["borrow_duration"]/$v["total"];
					$dt=date('Y-n-j',$v['lead_time']);
					for ($i=0; $i < $v["total"]; $i++) { 
						if(date('Y-n',strtotime("$dt+".$yue*$i."month"))==$_POST["date"]){
							$ab=strtotime("$dt+".$yue*$i."month");
							$sort_order=$i+1;
						}
					}
				}

			}else{
				$ab=$v['lead_time'];
			}
			
			$mpp["invest_id"]=$v["inid"];
			$mpp["status"]=1;
			$mpp["sort_order"]=$sort_order;
			$mpp["investor_uid"]=$this->uid;
			$minfo=M("investor_detail")->where($mpp)->find();
			// if($minfo){
			// 	$tenlist[$a]["hkzt"]=1;
			// }else{
			// 	$tenlist[$a]["hkzt"]=0;
			// }
			
			if($minfo){
				$tenlist[$a]["hkzt"]=1;
				$tenlist[$a]["shouyi"]=$minfo["receive_interest"]+$minfo["receive_capital"];
			}else{
				$tenlist[$a]["hkzt"]=0;
				//$tenlist[$a]["hklx"]="未回款";
				if($sort_order==$v["total"]){
					$tenlist[$a]["shouyi"]=$v["investor_capital"];
				}else{
					$tenlist[$a]["shouyi"]='';
				}
			}
			if($sort_order==$v["total"]){
				if(empty($minfo)){
					$tenlist[$a]["hklx"]="本金";  
				}else{
					$tenlist[$a]["hklx"]="本金+收益";  
				}
				 
			}else{
				$tenlist[$a]["hklx"]="第".$sort_order."次收益";
			}

		


            $tenlist[$a]["hk_time_c"]=date("Y-m-d",$ab); 
            if($sort_order==$v["total"]){
				$tenlist[$a]["bj"]=1;
			}else{
				$tenlist[$a]["bj"]=0;
			}

         }
         $tpl_var["ad"]=$idsd;
         if(is_array($tenlist)&&!empty($tenlist)){
               $data["state"]="1";
         $data["list"]=$tenlist;
             
         }else{
               $data["state"]="0";
             $data["list"]=$tenlist;
         }
       
   	$tpl_var['d']=$_POST["date"];
  
        // $this->assign('sum',$tpl_var); 
//  var_dump($tpl_var);
   	$data["list"]=$this->paixu1($data["list"]);
 	$tpl_var['tenlist']=$data["list"];
		ajaxmsg($tpl_var,1);die;	
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
	public function zindex(){
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
        // echo m("members m")->getlastsql();exit;
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
		$start=strtotime(date("Y-m-1 0:0:0",time())); 
		$end=strtotime(date("Y-m-t 23:59:59",time()));  
//	 $va2=strtotime(date("Y-m-1 0:0:0",time()));
		//var_dump(date("Y-m-1 0:0:0",$va));

 		$ids=$this->getid(); //var_dump($ids);
		$idsd=$ids['a'][date("Y-n",time())];
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

//var_dump($ids['b'][date("Y-n",time())]);

//      var_dump($tpl_var);
        $jb_name = getLeveName($all_money);
        $this->assign('sum',$tpl_var);
        $this->assign("minfo", $minfo);
        $this->assign("all_money", $all_money);
        $this->assign("jb_name", $jb_name);
        $this->assign("uclogin", $ucLoing);
        $this->assign("msummary",getMemberMoneySummary($this->uid));
        // 站内消息
        $msgList = M('inner_msg')->where('uid='.$this->uid)->order('status asc,id DESC')->limit(4)->select();
        //投资项目
        $mapx['investor_uid'] = $this->uid;
        $mapx['i.status'] = ["neq",3];

		//var_dump($mapx);exit();

		$mapx['i.id'] = ["in",$idsd];

        //." and i.status!=3"
    $field = "b.hkday,b.total,b.borrow_duration,b.borrow_name,b.borrow_interest_rate,b.full_time,b.borrow_status as bstatus,b.id,i.investor_capital,i.add_time,i.status,i.borrow_id,i.deadline,i.id as inid,i.investor_way,i.member_interest_rate_id,i.bonus_id,b.borrow_img,i.is_experience,i.contractId,i.is_sign,i.step,b.lead_time";

	//分页处理
	import("ORG.Util.Page");
	$count = M('borrow_info b')->where($mapx)->count('b.id');
	$p = new Page($count, 15);
	$page = $p->show();
	$Lsql = "{$p->firstRow},{$p->listRows}";
	$this->assign("page",$page);
	 	
	// $oldtime =date("Y-m-1",time())." 00:00:00"; 

	// $emp_time =date("Y-m-31",time()).' 23:59:59' ; 
	// $stime = strtotime($oldtime); 
 //    $etime = strtotime($emp_time);
        // $map['b.lead_time'] =array('gt',$stime1);
        // $map['b.lead_time'] = array('lt',$etime);
     
        // $map['investor_uid'] = $this->uid;
        
        //   $map="b.lead_time >=".$stime." and b.lead_time <=".$etime
	 	//$mapx['b.lead_time'] = ['between',[$stime,$etime]];//1
        $tenlist =  M('borrow_investor i')
        ->field($field)
        ->join('lzh_borrow_info b on b.id = i.borrow_id')
        ->where($mapx)->order('b.id desc')->	limit($Lsql)
         ->select();
//var_dump($mapx);exit();
         	//var_dump(m("borrow_investor i")->getlastsql());exit();
          //var_dump(count( $tenlist), M('borrow_investor i')->getlastsql());die;
        //var_dump($ids['b'][date("Y-n",time())]['bx']);
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


					$yue=$v["borrow_duration"]/$v["total"];
					$dt=date('Y-n-j',$v['lead_time']);
					for ($i=0; $i < $v["total"]; $i++) { 

						if(date('Y-n',strtotime("$dt+".$yue*$i."month"))==date("Y-n",time())){
							$a=strtotime("$dt+".$yue*$i."month");
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
						if($minfo){
							//var_dump(M("investor_detail")->getlastsql());
							$hktime[]=date('Y-n-j',strtotime("$dt+".$yue*$i."month"));
		 		            $hkt[date('Y-n-j',strtotime("$dt+".$yue*$i."month"))][]=$val["id"];
		 		            $hktd[date('Y-n',strtotime("$dt+".$yue*$i."month"))][]=$val["id"];
							
						}else{
						    $dhktime[]=date('Y-n-j',strtotime("$dt+".$yue*$i."month"));
						    $dhk[date('Y-n-j',strtotime("$dt+".$yue*$i."month"))][]=$val["id"];
						    $dhkd[date('Y-n',strtotime("$dt+".$yue*$i."month"))][]=$val["id"];
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

		 //var_dump(json_encode($dhktime));
	 //    var_dump(json_encode($hktime));
// 		$this->assign("tzlist",$tzlist);
// 		$this->assign("ylist",$ylist);		
		 
//var_dump($hktime);
	 
				
		$this->display();
    } 
    public function summary(){
	
		$this->assign("vo",getMemberMoneySummary($this->uid));
		$money= M('borrow_investor')->where('status=1 and uid='.$this->uid)->count('id');
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