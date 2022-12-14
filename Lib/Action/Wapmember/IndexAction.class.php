<?php
// 本类由系统自动生成，仅供测试用途
class IndexAction extends MCommonAction {
	
	 public function sign(){
	 	$this->display();
	 }
	 	 public function bbb() {
 //var_dump(session('reg_code'));
		var_dump($_SESSION['reg_code']);  
		 
		 
    	//ump());exit;
    }
		 
    public function sendcode()
    {
    	 if(empty($_REQUEST['type']) && ($_REQUEST['type']==4)){
              	if(empty($_REQUEST['vcode']) ||  $_SESSION["verify"]!=md5($_REQUEST['vcode']) ){
              			$json['status'] = "p";
                       	$json['info'] = "验证码不正确！1";
                       exit(json_encode($json));die;
       }
       }
       	
		     
    	//session('reg_code', 44444); 
    	//$_SESSION['reg_code'] = '5555';
		//echo $_SESSION['reg_code'];
//	 	$_REQUEST['phone']="18354393241";
        $phone = $_REQUEST['phone'];
    if(!preg_match('/^1\d{10}$/',$phone)){ 
           $json['status'] = "p";
           $json['info'] = "手机号格式不正确！";
           exit(json_encode($json));
       }   /*    
       if(empty($phone) || strlen($phone)!=11 ){
               $json['status'] = "f";
                       $json['info'] = "手机号格式不正确！";
                       exit(json_encode($json));die;
       }*/
//     if(isset($_SESSION['reg_code_time']) && (time()-$_SESSION['reg_code_time']) < 120){
//             $json['status'] = "f";
//                     $json['info'] = "您的操作过于频繁，请稍后重试。";
//                     exit(json_encode($json));die;
//     }
       $datag = $this->glo;
       $webname = $datag['web_name'];
       $rs = M('members')->where('user_phone='.$phone)->find();
       if($_REQUEST['type']!=4){
             if(!$rs){
               $json['status'] = "f";
                $json['info'] = "该手机号不存在！";
                exit(json_encode($json));
          }   
       }
     
	   
    preg_match_all('/\d/S', rand(1000,9999).rand(1000,9999), $matches);        
            $arr  = join('', $matches[0]);
            $code = substr($arr, 0, 4); 
            session('reg_code', $code);              
            session('user_phone',$phone);
            $content="您的验证码是".$code."，120秒内有效，若非本人操作请忽略此消息。";//要发送的短信内容
             $type = empty($_REQUEST['type'])?1:$_REQUEST['type'];
        $result = Notice1($type,$this->uid,array('phone'=>$phone,"code"=>$code));
           //type
           
//          $result = sendsms($phone,$content);
//			   exit(json_encode($result));     
// exit(json_encode($result)); 
		if($result  === true){
                       session('reg_code_time',time());
                       $json['status'] = "y";
                       $json['info'] = "验证码已经发送至您的号码为：".$phone."的手机！";          
               }else if($result  === FALSE){
    	
                       $json['status'] = "n";
                       $json['info'] = empty($sendRs) ? '发送失败！' : $sendRs;
               }else{
    	
                       $json['status'] = "n";
                       $json['info'] = empty($result) ? '发送失败！' : $result;
               }
//			   else{
//  	
//                     $json['status'] = "n";
//                     $json['info'] = empty($sendRs) ? '发送失败！' : $sendRs;
//             }
			    exit(json_encode($json));
               // 未开通短信前，测试用
               // $json['status'] = "y";
               // $json['info'] = "验证码已经发送至您的号码为：".$phone."的手机！".$code;     
//             exit(json_encode($json));
//echo $statusStr[$result];
//	    



	   
	//建周   短信接口
//      $phone = $_REQUEST['phone'];
//   
//     if(!preg_match('/^(13|15|18|14|17|19)[0-9]{9}$/',$phone)){ 
//         $json['status'] = "p";
//         $json['info'] = "手机号格式不正确！";
//         exit(json_encode($json));
//     }     
//     if(empty($phone) || strlen($phone)!=11 ){
//             $json['status'] = "f";
//                     $json['info'] = "手机号格式不正确！";
//                     exit(json_encode($json));die;
//     }
//     if(isset($_SESSION['reg_code_time']) && (time()-$_SESSION['reg_code_time']) < 120){
//             $json['status'] = "f";
//                     $json['info'] = "您的操作过于频繁，请稍后重试。";
//                     exit(json_encode($json));die;
//     }
//     $datag = $this->glo;
//     $webname = $datag['web_name'];
//     $rs = M('members')->where('user_phone='.$phone)->find();
//     if($rs){
//             $json['status'] = "f";
//                     $json['info'] = "该手机号已在本站注册，不可重复注册！";
//                     exit(json_encode($json));
//     }        
//             preg_match_all('/\d/S', rand(1000,9999).rand(1000,9999), $matches);        
//     $arr  = join('', $matches[0]);
//     $code = substr($arr, 0, 4);        
//     session('reg_code', $code);              
//             session('user_phone',$phone);        
//             if(empty($code)) die;
//             $content= "您正在注册网站会员，手机验证码。{$code}";
//             $sendRs = sendsms($phone, $content,1); 
//			 
//                     session('reg_code_time',time());
//                     $json['status'] = "y";
//                     $json['info'] = "测试站,你的验证码是$code";          
//            die;   
//             if($sendRs === true){
//                     session('reg_code_time',time());
//                     $json['status'] = "y";
//                     $json['info'] = "验证码已经发送至您的号码为：".$phone."的手机！";          
//             }else{
//                     $json['status'] = "n";
//                     $json['info'] = empty($sendRs) ? '发送失败！' : $sendRs;
//             }
//             // 未开通短信前，测试用
//             // $json['status'] = "y";
//             // $json['info'] = "验证码已经发送至您的号码为：".$phone."的手机！".$code;     
//             exit(json_encode($json));
    }
        
    private function hehe(){
        $per = C('DB_PREFIX');

        $borrow_investor = M("borrow_investor");
        $borrow_investor->startTrans();
        $borrow_investor->lock(true)->field("id")->select();
        $borrowinvestor = M("borrow_investor bi")
            ->join("{$per}borrow_info b ON bi.borrow_id=b.id")
            ->field('bi.investor_capital,bi.id,b.borrow_interest_rate ,bi.investor_uid as uid,bi.id as iid,bi.add_time')
            ->where("bi.investor_uid=".$this->uid."  and bi.borrow_id=1 and bi.status!=110")
            ->find();
        if($borrowinvestor['add_time'] + 86400 > time()) {
            return false;
        }
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
        if(!empty($borrowinvestor)){
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
            //$invest_defail_id = M('member_moneylog')->add(['uid'=>$this->uid,'type'=>'9','affect_money'=>$receive_interest,'account_money'=>$money['account_money']+$receive_interest,'back_money'=>'0','collect_money'=>'0','freeze_money'=>'0','info'=>'体验金回款','add_time'=>time(),'add_ip'=>get_client_ip(),'target_uid'=>0,'target_uname'=>'','experience_money'=>$borrowinvestor['investor_capital']]);
            $add_times=$borrowinvestor['add_time'] + 86400;
            $invest_defail_id = M('member_moneylog')->add(['uid'=>$this->uid,'type'=>'9','yubi'=>$money['yubi'],'freeze_yubi'=>$money['freeze_yubi'],'yongjin'=>$money['yongjin'],'affect_money'=>$receive_interest,'account_money'=>$money['account_money']+$receive_interest,'back_money'=>'0','collect_money'=>'0','freeze_money'=>'0','info'=>'体验金回款','add_time'=>$add_times,'add_ip'=>get_client_ip(),'target_uid'=>0,'target_uname'=>'','experience_money'=>$borrowinvestor['investor_capital']]);
            
            //memberMoneyLog
            //   echo M('lzh_member_moneylog')->getlastsql();die;
            $experience = M('member_experience')->where("uid=".$this->uid." and paystatus=1")->find();
            if(isset($experience) && !empty($experience)) {
                M('member_experience')->where("id = {$experience['id']}")->save(["paystatus" => 3]);
            }
            $invest_defail_id = M('member_money')->save(["uid"=>$money['uid'],"money_freeze"=>$money['money_freeze']-$borrowinvestor['investor_capital'],'yubi'=>$money['yubi'],'yubi_freeze'=>$money['freeze_yubi'],'yongjin'=>$money['yongjin'],"money_collect"=>$money['money_collect'],"account_money"=>$money['account_money']+$receive_interest,"back_money"=>$money['back_money'],"credit_limit"=>$money['credit_limit'],"credit_cuse"=>$money['credit_cuse'],"borrow_vouch_limit"=>$money['borrow_vouch_limit'],"borrow_vouch_cuse"=>$money['borrow_vouch_cuse'],"invest_vouch_limit"=>$money['invest_vouch_limit'],"invest_vouch_cuse"=>$money['invest_vouch_cuse'],"money_experience"=>$money['money_experience'],"update_time"=>time()]);
            M("borrow_investor")->where("id=".$borrowinvestor['iid'])->save(['status'=>110]);
        }
        $borrow_investor->commit();
    }
    public function index(){
        $this->hehe();
//  	var_dump($this->uid);die;
        session($_REQUEST['session_id'],$this->uid);
        recoverBonus($this->uid);
		$ucLoing = de_xie($_COOKIE['LoginCookie']);
		setcookie('LoginCookie','',time()-10*60,"/");
		$uid = $this->uid;		
		$minfo =getMinfo(session("u_id"),true);
		//var_dump($minfo);
        $all_money = getFloatvalue($minfo['account_money']+$minfo['money_collect']+$minfo['money_freeze'],2);
        // 累计收益
        $minfo['receive_interests'] = M('borrow_investor')->where('investor_uid = '.$uid.' and status = 5')->sum('receive_interest');
        $pre = C('DB_PREFIX');
        $this->assign("uid",$uid);      
        // 资料完善度
        $mVerify = m("members m")->field("m.credits,m.id,m.user_leve,m.time_limit,s.id_status,s.phone_status,s.email_status,s.video_status,s.face_status")->join("{$pre}members_status s ON s.uid=m.id")->where("m.id={$uid}")->find();
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
        $tpl_var['sum']['capital'] = M('investor_detail t1')->where("t1.investor_uid={$uid} and t1.repayment_time<=0")->join("{$pre}borrow_info t2 on t1.borrow_id =  t2.id")->group('t2.borrow_status')->getField('t2.borrow_status,sum(t1.capital)');
        $tpl_var['sum']['interest'] = M('investor_detail t1')->where("t1.investor_uid={$uid}")->sum("t2.receive_interest");
        //累计奖励
        $tpl_var['sum']['shouyi'] = M('member_moneylog')->where('type in(13,20,32,34,50) and uid={$uid}')->sum('affect_money');
        // 回收中
        $tpl_var['list4'] = M('borrow_investor bi')->join($pre.'borrow_info b on b.id = bi.borrow_id')->where("investor_uid={$uid} and b.borrow_status in(2,4)")->field('count(bi.id) as count,sum(bi.investor_capital) as sum')->find();

        $tpl_var['list6'] = M('borrow_investor bi')->join($pre.'borrow_info b on b.id = bi.borrow_id')->where("investor_uid={$uid} and b.borrow_status =6")->field('count(bi.id) as count,sum(bi.investor_capital) as sum')->find();

        $tpl_var['list7'] = M('borrow_investor bi')->join($pre.'borrow_info b on b.id = bi.borrow_id')->where("investor_uid={$uid} and b.borrow_status =7")->field('count(bi.id) as count,sum(bi.investor_capital) as sum')->find();

        $tpl_var['list_all']['count'] = $tpl_var['list4']['count'] + $tpl_var['list6']['count'] + $tpl_var['list7']['count'];
        $tpl_var['list_all']['sum'] = $tpl_var['list4']['sum'] + $tpl_var['list6']['sum'] + $tpl_var['list7']['sum'];
        
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
        $map['investor_uid'] = $this->uid;

        $field = "b.borrow_duration,b.borrow_name,b.borrow_interest_rate,b.full_time,b.borrow_status,b.id,i.investor_capital,i.add_time,i.status,i.borrow_id,i.deadline,i.id as inid,i.investor_way,i.member_interest_rate_id,i.bonus_id,i.is_experience,i.contractId,i.is_sign,i.step";
        $tenlist =  M('borrow_investor i')->field($field)->join('lzh_borrow_info b on b.id = i.borrow_id')->where($map)->order('b.id desc')->limit(10)->select();
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
                $this->assign($glo);
		$this->display();
    }
    public function qd(){
        $uid = $this->uid;
        $time = date('Y-m-d');
        $ip = get_client_ip();
        $qd = M('qiandao')->where('uid='.$this->uid." and addtime='{$time}'")->count();
        // echo M('qiandao')->getlastsql();exit;
        if($qd > 0){
            exit(json_encode('n'));
        }else{
            $data['uid'] = $uid;
            $data['addtime'] = $time;
            $data['ip'] = $ip;
            $data['jifen'] = '10';
            $res = M('qiandao')->add($data);
            if($res){
               $jf = M('members')->where('id='.$uid)->setInc('credits',10);
              // echo M('members')->getlastsql();exit;
               if($jf){
                   exit(json_encode('y'));
               }
            }
        }
    }
    public function choujiang(){
        $pre = C('DB_PREFIX');
        $uid = $this->uid;
        if(!$this->uid) $this->error("请先登陆",__APP__."/member/common/login");
        $today_bin = strtotime( date("Y-m-d 00:00:00") );
        $today_end = strtotime( date("Y-m-d 23:59:59") );
        $map['uid'] = $uid;
        $map['add_time'] = array('between',array($today_bin,$today_end));
        $cont = M('member_win')->where($map)->count();

        $gob = get_global_setting();
        if($cont >= $gob['web_lottery_number']){
            $this->error("你今天的抽奖次数已经用完",__APP__."/member/");
        }
        $list_win = M('member_win t1')->field('t1.*,t2.user_name')->join($pre.'members t2 on t1.uid=t2.id')->where('t1.uid='.$uid.' and t1.prize_title != "谢谢惠顾"')->order('id desc')->limit('100')->select();
        $this->assign('list_win',$list_win);
        $zhuanpan = M('zhuanpan t1')->field('t1.*')->order('id desc')->select();
        $arr = array();
        foreach ($zhuanpan as $key => $value) {
            $jpname[$key]=$value['title'];
            $arr[$key]=$value['rate'];
            if($key%2==0){
                $jpys[$key]='#16a9b3';
            }else{
                $jpys[$key]='#10bfd2';
            }
        }
        $ratelenth = bingo_rand($arr)+1;
        $this->assign('ratelenth',$ratelenth);
        $this->assign('zhuanpan',$zhuanpan);
        $this->assign('jpname',json_encode($jpname));
        $this->assign('jpys',json_encode($jpys));


        $this->display();
    }
    public function winning(){
        $uid = $this->uid;
        $today_bin = strtotime( date("Y-m-d 00:00:00") );
        $today_end = strtotime( date("Y-m-d 23:59:59") );
        $map['uid'] = $uid;
        $map['add_time'] = array('between',array($today_bin,$today_end));
        $cont = M('member_win')->where($map)->count();
        $gob = get_global_setting();
        if($cont >= $gob['web_lottery_number']){
            $this->error("你今天的抽奖次数已经用完",__APP__."/member/");
        }

        $title = $_POST['title'];
        $r = M('zhuanpan')->where('title="'.$title.'"')->find();
        if($r['jp_type'] == 1){
            //红包
            $bonus['uid'] = $uid;
            $bonus['money_bonus'] = $r['prize'];
            $bonus['bonus_invest_min'] = '0.00';
            $bonus['start_time'] = strtotime(date('Y-m-d H:i:s'));
            $bonus['end_time'] = date('Y-m-d H:i:s', strtotime("+30 day"));
            $rs = pubBonus($bonus);
            if ($rs['status'] == 0) { //保存成功                
                $this->error('发放失败！',__APP__."/member/");die;
            }
        }else if($r['jp_type'] == 2){
            //体验金
            $money_experience = $r['prize'];
            $experience_duration = 30;
            $rs = pubExperienceMoney($uid,$money_experience,4,$experience_duration);
        }else if($r['jp_type'] == 3){
            //加息券
            $rdata['uid']=$uid;
            $rdata['interest_rate'] = $r['prize'];
            
            $rdata['start_time'] = time();
            $rdata['end_time'] = strtotime(date('Y-m-d H:i:s', strtotime("+30 day")));
            $rdata['status'] = '1';
            $rdata['type'] = '1';
            $rdata['add_time'] = time();
            $rs = M('member_interest_rate')->add($rdata);   
        }
        // else{
        //     //谢谢
        //     $star = array('state'=>'1','info'=>'谢谢惠顾！');
        //     echo json_encode($star);
        //     die;
        // }
        $data['uid']=$uid;
        $data['prize_id']=$r['id'];
        $data['prize']=$r['prize'];
        $data['prize_title']=$r['title'];
        $data['add_time']=time();
        $newid = M('member_win')->add($data);
        if($newid){

            $star = array('state'=>'1','info'=>$r['title']);
            echo json_encode($star);
        }else{
            $star = array('state'=>'0','info'=>'抽奖失败');
            echo json_encode($star);
        }
    }
}
