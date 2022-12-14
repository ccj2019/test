<?php
/*********************/
/*  Version : 10.0    */
/*  Author  : Tech Lee  */
/*********************/
// 实名认证{}

//编号 : $(bianhao)
//甲方 : $(jiafang)
//身份证 : $(idno)

//乙方公司 : $(yifang_company_name)
//乙方代表人 : $(yifang_name)
//乙方信用代码 : $(yifang_xinyongdaima)

//丙方(平台) : $(bingfang)
//丙方(平台) : $(bingfang_xinyongdaima)
//项目名 : $(deal_name)
//
//
//
//甲章 : jia_sign 
//乙章 : yi_sidn 
//丙章 : bing_sign

function TCaptcha(){
 
$aid='2024559842';
$AppSecretKey='0rvpQRhlxmJo29MBSuNf5Lg**';
$Ticket=$_POST['ticket'];
$Randstr=$_POST['randstr'];
$UserIP=$_SERVER['REMOTE_ADDR'];
//初始化
$aa = file_get_contents('https://ssl.captcha.qq.com/ticket/verify?aid='.$aid.'&AppSecretKey='.$AppSecretKey.'&Ticket='.$Ticket.'&Randstr='.$Randstr.'&UserIP='.$UserIP);

return $aa;
exit;
}
function membersuser($date=0){
 
    if(is_array($date)){
        if(M('members')->where($date)->find()){
            return 1;
        }else{
            return 0;
        }
    }else{
        return 0;
    }
}

function create_agreeperson($name , $idCard,$user_phone,$uid){
    $glodata = get_global_setting();
    $per = C('DB_PREFIX');
    $tokenurl = "https://api.yunhetong.com/api/auth/login";
    $tokendata['appId']=$glodata['appid'];
    $tokendata['appKey']=$glodata['appkey'];
    $tokendata['signerId']='';
    $res = url_request_token($tokendata,$tokenurl);
    if($res['code'] == '200'){
        $murl = "https://api.yunhetong.com/api/user/person";
        $mdata['userName']=$name;
        $mdata['identityRegion']=0;
        $mdata['certifyNum']=$idCard;
        $mdata['phoneRegion']=0;
        $mdata['phoneNo']=$user_phone;
        $mdata['caType']='B2';
        $mres = url_request_json($mdata,$murl,$res['token']);
        $mrs = json_decode($mres,true);
        writeLog($mdata);
        writeLog($mrs);
        if($mrs['code']=='200'){
            $m['signerid']=$mrs['data']['signerId'];
            $newid=M('members')->where("id={$uid}")->save($m);
            return true;
        }else{
            return false;
        }
    }else{
        return false;
    }
}
//创建个人印模
function create_personmoulage($uid){
    $glodata = get_global_setting();
    $per = C('DB_PREFIX');
    $minfo = M("members m")->join("{$per}member_info mi ON mi.uid=m.id")->field('m.*,mi.real_name,mi.idcard')->where("m.id={$uid}")->find();
    $tokenurl = "https://api.yunhetong.com/api/auth/login";
    $tokendata['appId']=$glodata['appid'];
    $tokendata['appKey']=$glodata['appkey'];
    $tokendata['signerId']=$minfo['signerid'];
    $res = url_request_token($tokendata,$tokenurl);

    if($res['code'] == '200'){
        $murl = "https://api.yunhetong.com/api/user/personMoulage";
        $mdata['signerId']=$minfo['signerid'];
        $mdata['borderType']='B1';
        $mdata['fontFamily']='F1';
        $mres = url_request_json($mdata,$murl,$res['token']);
        writeLog($mres);
        $mrs = json_decode($mres,true);
        if($mrs['code']=='200'){
            $m['moulageId']=$mrs['data']['moulageId'];
            $newid=M('members')->where("id={$uid}")->save($m);
            if($newid){
                return true;
            }
        }else{
            return false;
        }
    }else{
        return false;
    }
}
//创建企业用户
function create_agreecompany($name , $idCard,$user_phone,$uid){
    $glodata = get_global_setting();
    $per = C('DB_PREFIX');
    $tokenurl = "https://api.yunhetong.com/api/auth/login";
    $tokendata['appId']=$glodata['appid'];
    $tokendata['appKey']=$glodata['appkey'];
    $tokendata['signerId']='';
    $res = url_request_token($tokendata,$tokenurl);
    if($res['code'] == '200'){
        $murl = "https://api.yunhetong.com/api/user/company";
        $mdata['userName']=$name;
        $mdata['certifyType']=1;
        $mdata['certifyNum']=$idCard;
        $mdata['phoneNo']=$user_phone;
        $mdata['caType']='B2';
        $mres = url_request_json($mdata,$murl,$res['token']);
        $mrs = json_decode($mres,true);
        writeLog($mrs);
        if($mrs['code']=='200'){
            $m['signerId']=$mrs['data']['signerId'];
            $newid=M('members')->where("id={$uid}")->save($m);
            return true;
        }else{
            return false;
        }
    }else{
        return false;
    }
}
//创建企业印模 
function create_companymoulage($uid){
    $glodata = get_global_setting();
    $per = C('DB_PREFIX');
    $minfo = M("members m")->join("{$per}member_info mi ON mi.uid=m.id")->field('m.*,mi.real_name,mi.idcard')->where("m.id={$uid}")->find();
    $tokenurl = "https://api.yunhetong.com/api/auth/login";
    $tokendata['appId']=$glodata['appid'];
    $tokendata['appKey']=$glodata['appkey'];
    $tokendata['signerId']=$minfo['signerid'];
    $res = url_request_token($tokendata,$tokenurl);
    if($res['code'] == '200'){
        $murl = "https://api.yunhetong.com/api/user/companyMoulage";
        
        $mdata['signerId']=$minfo['signerId'];
        $mdata['styleType']='1';
        $mdata['textContent']='';
        $mdata['keyContent']='5103010000417';
        $mres = url_request_json($mdata,$murl,$res['token']);
        $mrs = json_decode($mres,true);
        // var_dump($mrs);
        if($mrs['code']=='200'){
            $m['moulageId']=$mrs['data']['moulageId'];
            $newid=M('members')->where("id={$uid}")->save($m);
            if($newid){
                return true;
            }
        }else{
            return false;
        }
    }else{
        return false;
    }
}
//分销返佣
function distribution_maid($id){
    $datag = get_global_setting();
    $regtime = $datag['reg_reward_time']*24*60*60;
	$uid = M("member_payonline")->field('uid')->find($id);
    $minfo = M('members')->field('reg_time')->find($uid);
    $reg = $minfo['reg_time']+$regtime;
    // var_dump( M('members')->getlastsql());die;
    if($reg>time()){
       
        $vo = M('member_payonline')->field('money,fee,uid')->find($id);
        $minf = M('members')->field('recommend_id,recommend_bid,recommend_cid')->find($vo['uid']);
        $first = M('members')->field('id')->where('id='.$minf['recommend_id'])->find();
        //  var_dump($vo,$minf,$first);die;
        if($first){
            if($datag['first_reward']>0){
                $adddata['uid']=$first['id'];
                $adddata['money']=round($vo['money']*$datag['first_reward'],2);
                $adddata['add_time']=time();
                $adddata['tid']=$vo['uid'];
                $adddata['ordernums']=time();
                $adddata['info']='会员线下充值一级返佣';
                M("member_maidlog")->add($adddata);
                memberMoneyLog($adddata['uid'],221,$adddata['money'],"会员线下充值一级返佣",$vo['uid']); 
            }
        }
        $second = M('members')->field('id')->where('id='.$minf['recommend_bid'])->find();
        if($second){
            
        // var_dump(1);die;
            if($datag['second_reward']>0){
                $adddata['uid']=$second['id'];
                $adddata['money']=round($vo['money']*$datag['second_reward'],2);
                $adddata['add_time']=time();
                $adddata['tid']=$vo['uid'];
                $adddata['ordernums']=time();
                $adddata['info']='会员线下充值二级返佣';
                M("member_maidlog")->add($adddata);
                memberMoneyLog($adddata['uid'],221,$adddata['money'],"会员线下充值二级返佣",$vo['uid']); 
            }
        }
        $third = M('members')->field('id')->where('id='.$minf['recommend_cid'])->find();
        if($third){
            
        // var_dump(1);die;
            if($datag['third_reward']>0){
                $adddata['uid']=$third['id'];
                $adddata['money']=round($vo['money']*$datag['third_reward'],2);
                $adddata['add_time']=time();
                $adddata['tid']=$vo['uid'];
                $adddata['ordernums']=time();
                $adddata['info']='会员线下充值三级返佣';
                M("member_maidlog")->add($adddata);
                memberMoneyLog($adddata['uid'],221,$adddata['money'],"会员线下充值三级返佣");
            }
        }
    }
 
}

//分销返佣20190304
function distribution_maid20190304($id){
  $pre = C('DB_PREFIX'); 
	
	$fieldx = "bi.investor_capital,bi.investor_interest,bi.investor_capital+bi.investor_interest as yingshou,bi.receive_capital,bi.receive_interest,bi.add_time,m.user_name,m.id as userid,bi.is_auto,mi.real_name,bi.investor_uid";
	
	$investinfo = M("borrow_investor bi")
	->field($fieldx)
	->join("{$pre}members m ON bi.investor_uid = m.id")
	->join("{$pre}member_info mi on mi.uid = m.id")
	->where("bi.borrow_id={$id}")
	->order("bi.id DESC")
	->select();
 
    foreach($investinfo as $k=>$v){
    	$datag = get_global_setting();
	    $regtime = $datag['reg_reward_time']*24*60*60;
	    $minfo = M('members')->field('reg_time')->find($v["userid"]);
	    $reg = $minfo['reg_time']+$regtime;
	    // var_dump( M('members')->getlastsql());die;
	    
	
	    if($reg>time()){
	       
	       // $vo = M('member_payonline')->field('money,fee,uid')->find($v["id"]);
	        $minf = M('members')->field('recommend_id,recommend_bid,recommend_cid')->find($v['investor_uid']);
			
			     // var_dump($first,$v['investor_uid'],1) ;
			
	        $first = M('members')->field('id')->where('id='.$minf['recommend_id'])->find();
	        //  var_dump($vo,$minf,$first);die;
	        if($first){
	            if($datag['first_reward']>0){
	                $adddata['uid']=$first['id'];
	                $adddata['money']=round($v['investor_capital']*$datag['first_reward'],2);
	                $adddata['add_time']=time();
	                $adddata['tid']=$v['investor_uid'];
	                $adddata['ordernums']=time();
	                $adddata['info']='会员线下充值一级返佣';
	                M("member_maidlog")->add($adddata);
	                memberMoneyLog($adddata['uid'],221,$adddata['money'],"会员线下充值一级返佣"); 
	            }
	        }
	        $second = M('members')->field('id')->where('id='.$minf['recommend_bid'])->find();
	        if($second){
	            
	        // var_dump(1);die;
	            if($datag['second_reward']>0){
	                $adddata['uid']=$second['id'];
	                $adddata['money']=round($v['investor_capital']*$datag['second_reward'],2);
	                $adddata['add_time']=time();
	                $adddata['tid']=$v['investor_uid'];
	                $adddata['ordernums']=time();
	                $adddata['info']='会员线下充值二级返佣';
	                M("member_maidlog")->add($adddata);
	                memberMoneyLog($adddata['uid'],221,$adddata['money'],"会员线下充值二级返佣");
	            }
	        }
	        $third = M('members')->field('id')->where('id='.$minf['recommend_cid'])->find();
	        if($third){
	            
	        // var_dump(1);die;
	            if($datag['third_reward']>0){
	                $adddata['uid']=$third['id'];
	                $adddata['money']=round($v['investor_capital']*$datag['third_reward'],2);
	                $adddata['add_time']=time();
	                $adddata['tid']=$v['investor_uid'];
	                $adddata['ordernums']=time();
	                $adddata['info']='会员线下充值三级返佣';
	                M("member_maidlog")->add($adddata);
	                memberMoneyLog($adddata['uid'],221,$adddata['money'],"会员线下充值三级返佣");
	            }
	        }
	    }
		
    }
    // die;
    
 
}



function is_investor($uid){
    $count = M('borrow_investor')->where('investor_uid = '.$uid)->count('id');
    return $count>0?'有过投资':'未投过资';
}
// 根据规则发红包
function pubBonusByRules($uid,$type = 2,$invest_money = 0.01){
     if(!in_array($type, array(2,3))) return false;
		// var_dump($mbrList);die;    
    if($type == 2)
        $mbrList = M('member_bonus_rules')->where('type = 2 and UNIX_TIMESTAMP() between `start_time` and `end_time`')->select();
    else
        $mbrList = M('member_bonus_rules')->where('type = 3 and invest_money <='.$invest_money.' and UNIX_TIMESTAMP() between `start_time` and `end_time`')->order('money_bonus desc,expired_day desc,bonus_invest_min asc')->limit(1)->select();  
    		//var_dump($mbrList);die;    
    foreach ($mbrList as $key => $value) {
    	
        $pubData = array(
            'uid' => $uid,
            'money_bonus' => $value['money_bonus'],
            'start_time' => date('Y-m-d 00:00:00'),
            'end_time'   => date('Y-m-d 23:59:59', strtotime("+" . $value['expired_day'] . " day", time())),
            'bonus_invest_min' => $value['bonus_invest_min'],            
            );
        $rs = pubBonus($pubData,$type);
    
    }
}

// 发红包 pubBonus pubBonusByRules
function pubBonus($data,$type = 1){
	//var_dump(1);die;
    $borrow_username = empty($data['borrow_username']) ? '' : $data['borrow_username'];
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
    $addData['type'] = $type;
    $addData['add_time'] = time();

    if(@$data['uid'] > 0){
        $map['id'] = $data['uid'];
    }else{
        $map['user_name'] = ["IN",$borrow_username];
    }
    $borrow_member = M('members')->where($map)->find();         
    $addData['uid'] = @$borrow_member['id'];
    if(empty($addData['uid'])) {
        return array('status' => 0, 'tips' => '用户不存在！');           
    }
    $rs = M('member_bonus')->add($addData);            
    if ($rs) { //保存成功                
        return array('status' => 1, 'tips' => '发放成功！');
    } else {            
        return array('status' => 0, 'tips' => '发放失败！');        
    } 
}
//更新过期红包
function recoverBonus($uid = ''){
    $time=strtotime(date("Y-m-d"));
    $whereStr = "status not in(2,3) and end_time < ".$time;
    $uid and $whereStr .= " and uid = {$uid}";
    $rs = M('member_bonus')->where($whereStr)->save(array('status'=>3));  
}

// 回收体验金
function recoverExperienceMoney($id = '')
{
    $model                    = M('member_experience');
    $countRecover             = 0;
    $whereStr                 = 'status != 3 && has_recover < money_experience && end_time < UNIX_TIMESTAMP() ';
    !empty($id) and $whereStr = 'status != 3 && id = ' . $id;
    // $whereStr .= intval($uid) > 0 ? 'and uid = '.$uid : '' ;
    $experienceList = $model->where($whereStr)
        ->order('end_time ASC')
        ->select(); // 查询需要回收的体验金
    // echo $model->getlastsql();
    // dump($experienceList);die;
    foreach ($experienceList as $key => $value) {
        $model->startTrans();
        $waitRecoverMoney  = bcsub($value['money_experience'], $value['has_recover'], 2); // 需要回收的
        $nowUserExperience = m("member_money")->where("uid = '{$value['uid']}'")->getField('money_experience');
        $nowRecoverMoney   = $nowUserExperience < $waitRecoverMoney ? $nowUserExperience : $waitRecoverMoney;
        $info              = !empty($id) ? '管理员手动回收' : '体验金到期自动回收';
        $rsRecover         = memberMoneyLog($value['uid'], 101, $nowRecoverMoney, $info, 0,'@网站管理员@');
        // var_dump($rsRecover );die;
        $hasRecover        = $value['has_recover'] + $nowRecoverMoney; //本条体验金已回收的金额
        if ($rsRecover) {
            $saveData = array(
                'id'          => $value['id'],
                'has_recover' => $hasRecover,
            );
            if ($hasRecover >= $value['money_experience']) {
                $saveData['status'] = 3;
            }
            //本条体验金回收完成
            else {
                $saveData['status'] = 2;
            }
            //回收中
            $rsUpdate = $model->save($saveData);
        }
        if (isset($rsUpdate) && $rsUpdate) {
            $model->commit();
            $countRecover++;
        } else {
            $model->rollback();
        }
    }
    return $countRecover;
}
//回收体验金
function recoverexmoney($uid,$money_experience)
{
    $model                    = M('member_experience');
     $whereStr                 = 'status != 3 && money_experience > '.$money_experience.' && end_time > UNIX_TIMESTAMP() ';

}
// 发放体验金
function pubExperienceMoney($uid, $experience_money, $type, $experience_duration = '')
{
    $typeArr = C('EXPERIENCE_MONEY');
    $typeArr = $typeArr['TYPE'];    
    if ($experience_money <= 0 || !isset($typeArr[$type])) {
        return false;
    }
//    $r = M('member_experience')->where('uid='.$uid)->find();
//    if(!empty($r)){
//        return false;
//    }
    $_P_fee                             = get_global_setting();
    $experience_money_dur               = empty($experience_duration) ? intval($_P_fee['experience_money_dur']) : intval($experience_duration);
    $experienceData['uid']              = $uid;
    $experienceData['money_experience'] = $experience_money;
    $experienceData['type']             = $type;
    $experienceData['start_time']       = time();
    $experienceData['end_time']         = strtotime("+" . $experience_money_dur . " day", $experienceData['start_time']);
    $experienceData['status']           = 0;
	 $experienceData['update_time']           = time();
        $rs = M('member_experience')->add($experienceData);
    if ($rs) {
        //echo 1;
        $rs = memberMoneyLog($uid, 100, $experience_money, $typeArr[$type],0,'@网站管理员@');
        //var_dump($rs);
        //echo '<br/>';
        // if(!$rs) {
        //     $arr = array();
        //     $arr[]=$uid;
        //     $arr[]=$experience_money;
        //     $arr[]=$typeArr[$type];
        //     var_dump($arr);
        // }
    }
    //die;
    return $rs;
}
//注册发放体验金
function pubExpMoneyOnreg($uid){
    $_P_fee = get_global_setting();
    list($experience_reg_1, $experience_reg_2) = explode('|', $_P_fee['experience_reg']);
    if($experience_reg_1 > 0){
        pubExperienceMoney($uid,getFloatvalue($experience_reg_1),1);
    }
    $rs = M('members')->field('recommend_id')->find($uid);
    if(@$rs['recommend_id'] > 0 && $experience_reg_2 > 0){
        pubExperienceMoney($rs['recommend_id'],getFloatvalue($experience_reg_2),5);
    }
}

//加息券是否可用
function canUseInterest($uid,$rid)
{
    $rs = M('member_interest_rate')->where(" id = '{$rid}' and uid='{$uid}' and status = 1 and (" . time() . " > start_time and " . time() . " < end_time )")->find();
    if (isset($rs['id'])) {
        return $rs;
    } else {
        $rs['status']      = 0;
        return $rs;
    }
}


//红包是否可用
function canUseBouns($bonus_id, $uid, $binfo)
{
    if (!isset($binfo['is_bonus']) || $binfo['is_bonus'] != 1) {
        return false;
    }

    $rs = M('member_bonus')->where("id='{$bonus_id}' and uid='{$uid}' and status = 1 and (" . time() . " > start_time and " . time() . " < end_time )")->find();
    if (isset($rs['id'])) {
        return $rs;
    } else {
        return false;
    }
}
//红包金额
function bounsmoney($bonus_id){
    $rs = M('member_bonus')->where("id='{$bonus_id}'")->find();
    return $rs['money_bonus'];
}

//加息券
function intestrate($rid){
    $rs = M('member_interest_rate')->where("id='{$rid}'")->find();
    return $rs['interest_rate'];
}
//加息券
function getintestrate($investor_capital,$rid,$borrow_id){

    $borrowinfo = M('borrow_info')->where("id='{$borrow_id}'")->find();
    $rs = M('member_interest_rate')->where("id='{$rid}'")->find();
    $affect_inter_money= round($investor_capital*$rs['interest_rate']/100/360*$borrowinfo['borrow_duration'],2);
    return $affect_inter_money;
}


// 入账操作
//type(0|调整余额，1|线上充值，2|线下充值，3|借款入账，4|回款入账)
function inAccountMoney($uid,$inMoney,$type){
    $moneyInData = array(
        'uid' => $uid,
        'money_in' => $inMoney,
        'money_in_type' => $type,
        'add_time' => time(),
        'status' => '1',
        );
    return M('member_money_account_in')->add($moneyInData);
}
// 入账操作
//出账操作
function useAccountMoney($uid,$useMoney){
    $model = M('member_money_account_in');
    $canUseList = $model->where("status in(1,2) and money_in > money_out and uid = '{$uid}'")->order('id asc')->select();
    $needUseMoney = $useMoney;
    foreach ($canUseList as $key => $value) {
        if ($needUseMoney <= 0) {
            continue;
        }                
        $rowUseMoney = 0; //本条记录使用的钱
        $canUseMoney = bcsub($value['money_in'], $value['money_out'], 2); // 本条记录可以使用的钱
        if ($needUseMoney <= $canUseMoney) {
            $rowUseMoney = $needUseMoney;
        } else {
            $rowUseMoney = $canUseMoney;
        }
        $needUseMoney = bcsub($needUseMoney, $rowUseMoney, 2);
        $hasOut   = $value['money_out'] + $rowUseMoney; //本条回款的金额
        if ($hasOut) {
            $saveData = array(
                'id'          => $value['id'],
                'money_out' => $hasOut,
            );
            if ($hasOut >= $value['money_in']) {
                $saveData['status'] = 3;
            }
            //本条已使用
            else {
                $saveData['status'] = 2;
            }
            $rsUpdate = $model->save($saveData);
        }
        if (isset($rsUpdate) && $rsUpdate) {
            $countUse++;
        }        
    }
}
//出账操作
/**
 * 
 * 单笔实时接口
 * TRX_CODE:100014--单笔实时代付
 * TRX_CODE:100011--单笔实时代收
 * @var unknown_type
 */  
function doCash($uid,$money,$info=''){           
    return ture;
}
function my_filename() {
  return date('ymdHis',time()).'_'.mt_rand();
}
function time2string($second){
    $day = floor($second/(3600*24));
    $second = $second%(3600*24);//除去整天之后剩余的时间
    $hour = floor($second/3600);
    $second = $second600;//除去整小时之后剩余的时间
    $minute = floor($second/60);
    $second = $second;//除去整分钟之后剩余的时间
    //返回字符串
    return $day.'天'.$hour.'小时';
}
//这个星期的星期一  
// @$timestamp ，某个星期的某一个时间戳，默认为当前时间  
// @is_return_timestamp ,是否返回时间戳，否则返回时间格式  
function this_monday($timestamp=0,$is_return_timestamp=true){  
    static $cache ;  
    $id = $timestamp.$is_return_timestamp;  
    if(!isset($cache[$id])){  
        if(!$timestamp) $timestamp = time();  
        $monday_date = date('Y-m-d', $timestamp-86400*date('w',$timestamp)+(date('w',$timestamp)>0?86400:-/*6*86400*/518400));  
        if($is_return_timestamp){  
            $cache[$id] = strtotime($monday_date);  
        }else{  
            $cache[$id] = $monday_date;  
        }  
    }  
    return $cache[$id];  
  
}  
  
//这个星期的星期天  
// @$timestamp ，某个星期的某一个时间戳，默认为当前时间  
// @is_return_timestamp ,是否返回时间戳，否则返回时间格式  
function this_sunday($timestamp=0,$is_return_timestamp=true){  
    static $cache ;  
    $id = $timestamp.$is_return_timestamp;  
    if(!isset($cache[$id])){  
        if(!$timestamp) $timestamp = time();  
        $sunday = this_monday($timestamp) + /*6*86400*/518400;  
        if($is_return_timestamp){  
            $cache[$id] = $sunday;  
        }else{  
            $cache[$id] = date('Y-m-d',$sunday);  
        }  
    }  
    return $cache[$id];  
}
//这个月的第一天  
// @$timestamp ，某个月的某一个时间戳，默认为当前时间  
// @is_return_timestamp ,是否返回时间戳，否则返回时间格式  
  
function month_firstday($timestamp = 0, $is_return_timestamp=true){  
    static $cache ;  
    $id = $timestamp.$is_return_timestamp;  
    if(!isset($cache[$id])){  
        if(!$timestamp) $timestamp = time();  
        $firstday = date('Y-m-d', mktime(0,0,0,date('m',$timestamp),1,date('Y',$timestamp)));  
        if($is_return_timestamp){  
            $cache[$id] = strtotime($firstday);  
        }else{  
            $cache[$id] = $firstday;  
        }  
    }  
    return $cache[$id];  
}  
  
//这个月的最后一天  
// @$timestamp ，某个月的某一个时间戳，默认为当前时间  
// @is_return_timestamp ,是否返回时间戳，否则返回时间格式  
  
function month_lastday($timestamp = 0, $is_return_timestamp=true){  
    static $cache ;  
    $id = $timestamp.$is_return_timestamp;  
    if(!isset($cache[$id])){  
        if(!$timestamp) $timestamp = time();  
        $lastday = date('Y-m-d', mktime(0,0,0,date('m',$timestamp),date('t',$timestamp),date('Y',$timestamp)));  
        if($is_return_timestamp){  
            $cache[$id] = strtotime($lastday);  
        }else{  
            $cache[$id] = $lastday;  
        }  
    }  
    return $cache[$id];  
}  
/*
$data=0 不限制 1 全部  2 月  3 日
*/
function daishou($data=1,$uid){
    $map=array();
    $map["i.investor_uid"]=$uid;
    $map["b.borrow_type"]=array("neq",3);
    $map["b.borrow_type"]=array("neq",3);
//  $map["b.borrow_status"]=array("gt",2);
    $map["b.borrow_status"]=array("between","3,6");
    if($data==2){
            $now_month = date('Y-m',time())."-01 00:00:00";
            $now_month = strtotime($now_month);//本月一号零点
            $map["i.add_time"]=array("gt",$now_month);
    }elseif($data==3){
            $now_time = date('Y-m-d',time())." 00:00:00";
            $now_time = strtotime($now_time);//本月一号零点
            $map["i.add_time"]=array("gt",$now_time);
    }
    $map["i.receive_interest"]=array("eq",0);
    $vo = M("borrow_investor i")->join("lzh_borrow_info b on i.borrow_id=b.id")->field("sum(i.investor_capital) as money")->where($map)->find();
    $vo0 = M("borrow_investor i")->join("lzh_borrow_info b on i.borrow_id=b.id")->field("sum(i.investor_interest) as money")->where($map)->find();
    $map["b.borrow_status"]=2;
    $map["b.pause"]=1;
    $vo2 = M("borrow_investor i")->join("lzh_borrow_info b on i.borrow_id=b.id")->field("sum(i.investor_capital) as money")->where($map)->find();
    $vo3 = M("borrow_investor i")->join("lzh_borrow_info b on i.borrow_id=b.id")->field("sum(i.investor_interest) as money")->where($map)->find();
    return $vo["money"]+$vo2["money"]+$vo0["money"]+$vo3["money"];
    return $vo["money"]+$vo2["money"];
} 
  
function EnHtml($v)
{
    return $v;
}
function mydate($format, $time, $default = "")
{
    if (10000 < intval($time)) {
        return date($format, $time);
    } else {
        return $default;
    }
}
function textPost($data)
{
    if (is_array($data)) {
        foreach ($data as $key => $v) {
            $x[$key] = text($v);
        }
    }
    return $x;
}
function MU($url, $type, $vars = array(), $domain = false)
{
    $path = explode("/", trim($url, "/"));
    $model = strtolower($path[1]);
    $action = isset($path[2]) ? strtolower($path[2]) : "";
    $http = ud($path, $domain);
    switch ($type) {
        case
        "article" :
            if (!isset($vars['id'])) {
                unset($path[0]);
                $url = implode("/", $path) . "/";
                $newurl = $url;
            } else {
                if ($UN_1 || strtolower(GROUP_NAME) == strtolower(c("DEFAULT_GROUP"))) {
                    unset($path[0]);
                    $url = implode("/", $path) . "/";
                }
                $newurl = $url . $vars['id'] . $vars['suffix'];
            }
            break;
        case "typelist" :
            if ($UN_1 || strtolower(GROUP_NAME) == strtolower(c("DEFAULT_GROUP"))) {
                unset($path[0]);
                $url = implode("/", $path);
            }
            $newurl = $url . $vars['suffix'];
            break;
        default :
    }
    return $http . $newurl;
}
function UD($url = array(), $domain = false)
{
    $isDomainGroup = true;
    $isDomainD = false;
    $asdd = c("APP_SUB_DOMAIN_DEPLOY");
    if ($asdd) {
        foreach (c("APP_SUB_DOMAIN_RULES") as $keyr => $ruler) {
            if (strtolower($url[0] . "/") == strtolower($ruler[0])) {
                $isDomainGroup = true;
                $isDomainD = true;
                break;
            }
        }
    }
    if (strtolower(GROUP_NAME) == strtolower(c("DEFAULT_GROUP"))) {
        $isDomainGroup = true;
    }
    if ($domain === true) {
        $domain = $_SERVER['HTTP_HOST'];
        if ($asdd) {
            $xdomain = explode(".", $_SERVER['HTTP_HOST']);
            if (!isset($xdomain[2])) {
                $ydomain = "www." . $_SERVER['HTTP_HOST'];
            } else {
                $ydomain = $_SERVER['HTTP_HOST'];
            }
            $domain = $domain == "localhost" ? "localhost" : "www" . strstr($ydomain, ".");
            foreach (c("APP_SUB_DOMAIN_RULES") as $key => $rule) {
                if (false === strpos($key, "*") && $isDomainD) {
                    $domain = $key . strstr($domain, ".");
                    $url = substr_replace($url, "", 0, strlen($rule[0]));
                    break;
                }
            }
        }
    }
    if (!$isDomainGroup) {
        $gpurl = __APP__ . "/" . $url[0] . "/";
    } else {
        $gpurl = __APP__ . "/";
    }
    if ($domain) {
        $url = "http://" . $domain . $gpurl;
    } else {
        $url = $gpurl;
    }
    return $url;
}
function Mheader($type)
{
    header("Content-Type:text/html;charset={$type}");
}
function auto_charset($fContents, $from = "gbk", $to = "utf-8")
{
    $from = strtoupper($from) == "UTF8" ? "utf-8" : $from;
    $to = strtoupper($to) == "UTF8" ? "utf-8" : $to;
    if ($to == "utf-8" && is_utf8($fContents) || strtoupper($from) === strtoupper($to) || empty($fContents) || is_scalar($fContents) && !is_string($fContents)) {
        return $fContents;
    }
    if (is_string($fContents)) {
        if (function_exists("mb_convert_encoding")) {
            return mb_convert_encoding($fContents, $to, $from);
        } else if (function_exists("iconv")) {
            return iconv($from, $to, $fContents);
        } else {
            return $fContents;
        }
    } else if (is_array($fContents)) {
        foreach ($fContents as $key => $val) {
            $_key = auto_charset($key, $from, $to);
            $fContents[$_key] = auto_charset($val, $from, $to);
            if ($key != $_key) {
                unset($fContents[$key]);
            }
        }
        return $fContents;
    } else {
        return $fContents;
    }
}
function is_utf8($string)
{
    return preg_match("%^(?:\r\n\t\t [\\x09\\x0A\\x0D\\x20-\\x7E]            # ASCII\r\n\t   | [\\xC2-\\xDF][\\x80-\\xBF]             # non-overlong 2-byte\r\n\t   |  \\xE0[\\xA0-\\xBF][\\x80-\\xBF]        # excluding overlongs\r\n\t   | [\\xE1-\\xEC\\xEE\\xEF][\\x80-\\xBF]{2}  # straight 3-byte\r\n\t   |  \\xED[\\x80-\\x9F][\\x80-\\xBF]        # excluding surrogates\r\n\t   |  \\xF0[\\x90-\\xBF][\\x80-\\xBF]{2}     # planes 1-3\r\n\t   | [\\xF1-\\xF3][\\x80-\\xBF]{3}          # planes 4-15\r\n\t   |  \\xF4[\\x80-\\x8F][\\x80-\\xBF]{2}     # plane 16\r\n   )*\$%xs", $string);
}
function get_date($date, $t = "d", $n = 0)
{
    if ($t == "d") {
        $firstday = date("Y-m-d 00:00:00", strtotime("{$n} day"));
        $lastday = date("Y-m-d 23:59:59", strtotime("{$n} day"));
    } else if ($t == "w") {
        if ($n != 0) {
            $date = date("Y-m-d", strtotime("{$n} week"));
        }
        $lastday = date("Y-m-d 00:00:00", strtotime("{$date} Sunday"));
        $firstday = date("Y-m-d 23:59:59", strtotime("{$lastday} -6 days"));
    } else if ($t == "m") {
        if ($n != 0) {
            if (date("m", time()) == 1) {
                $date = date("Y-m-d", strtotime("{$n} months -1 day"));
            } else {
                $date = date("Y-m-d", strtotime("{$n} months"));
            }
        }
        $firstday = date("Y-m-01 00:00:00", strtotime($date));
        $lastday = date("Y-m-d 23:59:59", strtotime("{$firstday} +1 month -1 day"));
    }
    return array(
        $firstday,
        $lastday
    );
}
function rand_string($ukey = "", $len = 6, $type = "1", $utype = "1", $addChars = "")
{
    $str = "";
    switch ($type) {
        case 0 :
            $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz" . $addChars;
            break;
        case 1 :
            $chars = str_repeat("0123456789", 3);
            break;
        case 2 :
            $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ" . $addChars;
            break;
        case 3 :
            $chars = "abcdefghijklmnopqrstuvwxyz" . $addChars;
            break;
        default :
            $chars = "ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789" . $addChars;
            break;
    }
    if (10 < $len) {
        $chars = $type == 1 ? str_repeat($chars, $len) : str_repeat($chars, 5);
    }
    $chars = str_shuffle($chars);
    $str = substr($chars, 0, $len);
    if (!empty($ukey)) {
        $vd['code'] = $str;
        $vd['send_time'] = time();
        $vd['ukey'] = $ukey;
        $vd['type'] = $utype;
        m("verify")->add($vd);
    }
    return $str;
}
function is_verify($uid, $code, $utype, $timespan)
{
    if (!empty($uid)) {
        $vd['ukey'] = $uid;
    }
    $vd['type'] = $utype;
    $vd['send_time'] = array(
        "lt",
        time() + $timespan
    );
    $vd['code'] = $code;
    $vo = m("verify")->field("ukey")->where($vd)->find();
    if (is_array($vo)) {
        return $vo['ukey'];
    } else {
        return false;
    }
}
function get_global_setting()
{
    $list = array();
    if (!s("global_setting")) {
        $list_t = m("global")->field("code,text")->select();
        foreach ($list_t as $key => $v) {
            $list[$v['code']] = de_xie($v['text']);
        }
        s("global_setting", $list);
        s("global_setting", $list, 3600 * c("TTXF_TMP_HOUR"));
    } else {
        $list = s("global_setting");
    }
    return $list;
}
function get_user_acl($uid = "")
{
    $model = strtolower(MODULE_NAME);
    if (empty($uid)) {
        return false;
    }
    $gid = m("ausers")->field("u_group_id")->find($uid);
    $al = get_group_data($gid['u_group_id']);

    $acl = $al['controller'];
    $acl_key = acl_get_key();
     //  var_dump($acl_key,$acl[$model]);
 // die;
    if (array_keys($acl[$model], $acl_key)) {
        return true;
    } else {
        return false;
        //return true;
    }
}
function get_group_data($gid = 0)
{
    $gid = intval($gid);
    $list = array();
    if ($gid == 0) {
        if (!s("ACL_all")) {
            $_acl_data = m("acl")->select();
            $acl_data = array();
            foreach ($_acl_data as $key => $v) {
                $acl_data[$v['group_id']] = $v;
                $acl_data[$v['group_id']]['controller'] = unserialize($v['controller']);
            }
            s("ACL_all", $acl_data, c("ADMIN_CACHE_TIME"));
            $list = $acl_data;
        } else {
            $list = s("ACL_all");
        }
    } else if (!s("ACL_" . $gid)) {
        $_acl_data = m("acl")->find($gid);
        $_acl_data['controller'] = unserialize($_acl_data['controller']);
        $acl_data = $_acl_data;
        s("ACL_" . $gid, $acl_data, c("ADMIN_CACHE_TIME"));
        $list = $acl_data;
    } else {
        $list = s("ACL_" . $gid);
    }
    // var_dump($list);die;
    return $list;
}
function rmdirr($dirname)
{
    if (!file_exists($dirname)) {
        return false;
    }
    if (is_file($dirname) || is_link($dirname)) {
        return unlink($dirname);
    }
    $dir = dir($dirname);
    while (false !== ($entry = $dir->read())) {
        if ($entry == "." || $entry == "..") {
            continue;
        }
        rmdirr($dirname . DIRECTORY_SEPARATOR . $entry);
    }
    $dir->close();
    return rmdir($dirname);
}
function Rmall($dirname)
{
    if (!file_exists($dirname)) {
        return false;
    }
    if (is_file($dirname) || is_link($dirname)) {
        return unlink($dirname);
    }
    $dir = dir($dirname);
    while (false !== ($file = $dir->read())) {
        if ($file == "." || $file == "..") {
            continue;
        }
        if (!is_dir($dirname . "/" . $file)) {
            unlink($dirname . "/" . $file);
        } else {
            rmall($dirname . "/" . $file);
        }
        rmdir($dirname . "/" . $file);
    }
    $dir->close();
    rmdir($dirname);
    return true;
}
function ReadFiletext($filepath)
{
    $htmlfp = @fopen($filepath, "r");
    while ($data = @fread($htmlfp, 1000)) {
        $string .= $data;
    }
    @fclose($htmlfp);
    return $string;
}
function MakeFile($con, $filename)
{
    makedir(dirname($filename));
    $fp = fopen($filename, "w");
    fwrite($fp, $con);
    fclose($fp);
}
function MakeDir($dir)
{
    return is_dir($dir) || makedir(dirname($dir)) && mkdir($dir, 511);
}
function get_home_friend($type, $datas = array())
{
    $condition['is_show'] = array("eq", 1);
    $condition['link_type'] = array(
        "eq",
        $type
    );
    $type = "friend_home" . $type;
    if (!s($type)) {
        $_list = m("friend")->field("link_txt,link_href,link_img,link_type")->where($condition)->order("link_order DESC")->select();
        $list = array();
        foreach ($_list as $key => $v) {
            $list[$key] = $v;
        }
        s($type, $list, 3600 * c("HOME_CACHE_TIME"));
    } else {
        $list = s($type);
    }
    return $list;
}
function auto_next($uid){
    
    $d=M("auto_borrow mm");
    $list=$d->field(true)->where("status=1")->order("yongdao asc,id asc")->group("uid")->select();
    //return M("auto_borrow")->getlastsql;
    //exit;
    foreach($list as $key=>$val){
        $vminfo=array();
        $vminfo = getminfo($val['uid'], "m.user_leve,m.time_limit,mm.account_money");
        //echo $val["id"]."--".$vminfo["account_money"]."<br>";
        if($vminfo["account_money"]<50) continue;
        $s[]=$val["uid"];
    }
    $next=array_keys($s,"".$uid,true);
    //print_r($next[0]);
    if(isset($next[0])){
        $next_id=$next[0]+1;
    }else{
        $next_id=0; 
    }
    //echo $next_id;
    return $next_id;
}
function get_ad($id)
{
    $stype = "home_ad" . $id;
    if (!s($stype)) {
        $list = array();
        $condition['start_time'] = array(
            "lt",
            time()
        );
        $condition['end_time'] = array(
            "gt",
            time()
        );
        $condition['id'] = array(
            "eq",
            $id
        );
        $_list = m("ad")->field("ad_type,content")->where($condition)->find();
        if ($_list['ad_type'] == 1) {
            $_list['content'] = unserialize($_list['content']);
        }
        $list = $_list;
        s($stype, $list, 3600 * c("HOME_CACHE_TIME"));
    } else {
        $list = s($stype);
    }
    if ($list['ad_type'] == 0 || !$list['content']) {
        if (!$list['content']) {
            $list['content'] = "广告未上传或已过期";
        }
        echo $list['content'];
    } else {
        return $list['content'];
    }
}
function get_type_leve($id = "0")
{
    $model = d("Acategory");
    if (!s("type_son_type")) {
        $allid = array();
        $data = $model->field("id,type_nid")->where("parent_id = {$id}")->select();
        if (0 < count($data)) {
            foreach ($data as $v) {
                $allid[$v['type_nid']] = $v['id'];
                $data_1 = array();
                $data_1 = $model->field("id,type_nid")->where("parent_id = {$v['id']}")->select();
                if (0 < count($data_1)) {
                    foreach ($data_1 as $v1) {
                        $allid[$v['type_nid'] . "-" . $v1['type_nid']] = $v1['id'];
                        $data_2 = array();
                        $data_2 = $model->field("id,type_nid")->where("parent_id = {$v1['id']}")->select();
                        if (0 < count($data_2)) {
                            foreach ($data_2 as $v2) {
                                $allid[$v['type_nid'] . "-" . $v1['type_nid'] . "-" . $v2['type_nid']] = $v2['id'];
                                $data_3 = array();
                                $data_3 = $model->field("id,type_nid")->where("parent_id = {$v2['id']}")->select();
                                if (0 < count($data_3)) {
                                    foreach ($data_3 as $v3) {
                                        $allid[$v['type_nid'] . "-" . $v1['type_nid'] . "-" . $v2['type_nid'] . "-" . $v3['type_nid']] = $v3['id'];
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        s("type_son_type", $allid, 3600 * c("HOME_CACHE_TIME"));
    } else {
        $allid = s("type_son_type");
    }
    return $allid;
}
function get_area_type_leve($id = "0", $area_id = 0)
{
    $model = d("Aacategory");
    if (!s("type_son_type_area" . $area_id)) {
        $allid = array();
        $data = $model->field("id,type_nid")->where("parent_id = {$id} AND area_id={$area_id}")->select();
        if (0 < count($data)) {
            foreach ($data as $v) {
                $allid[$area_id . $v['type_nid']] = $v['id'];
                $data_1 = array();
                $data_1 = $model->field("id,type_nid")->where("parent_id = {$v['id']}")->select();
                if (0 < count($data_1)) {
                    foreach ($data_1 as $v1) {
                        $allid[$area_id . $v['type_nid'] . "-" . $v1['type_nid']] = $v1['id'];
                        $data_2 = array();
                        $data_2 = $model->field("id,type_nid")->where("parent_id = {$v1['id']}")->select();
                        if (0 < count($data_2)) {
                            foreach ($data_2 as $v2) {
                                $allid[$area_id . $v['type_nid'] . "-" . $v1['type_nid'] . "-" . $v2['type_nid']] = $v2['id'];
                                $data_3 = array();
                                $data_3 = $model->field("id,type_nid")->where("parent_id = {$v2['id']}")->select();
                                if (0 < count($data_3)) {
                                    foreach ($data_3 as $v3) {
                                        $allid[$area_id . $v['type_nid'] . "-" . $v1['type_nid'] . "-" . $v2['type_nid'] . "-" . $v3['type_nid']] = $v3['id'];
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        s("type_son_type_area" . $area_id, $allid, 3600 * c("HOME_CACHE_TIME"));
    } else {
        $allid = s("type_son_type_area" . $area_id);
    }
    return $allid;
}
function get_type_leve_nid($id = "0")
{
    if (empty($id)) {
        return;
    }
    global $allid;
    static $r = array();
    get_type_leve_nid_run($id);
    $r = $allid;
    $GLOBALS['GLOBALS']['allid'] = NULL;
    return array_reverse($r);
}
function get_type_leve_nid_run($id = "0")
{
    global $allid;
    $data_parent = $data = "";
    $data = d("Acategory")->field("parent_id,type_nid")->find($id);
    $data_parent = d("Acategory")->field("id,type_nid")->where("id = {$data['parent_id']}")->find();
    if (0 < isset($data_parent['type_nid'])) {
        if (!isset($allid[0])) {
            $allid[] = $data['type_nid'];
        }
        $allid[] = $data_parent['type_nid'];
        get_type_leve_nid_run($data_parent['id']);
    } else if (!isset($allid[0])) {
        $allid[] = $data['type_nid'];
    }
}
function get_type_leve_area_nid($id = "0", $area_id = 0)
{
    if (empty($id) || empty($area_id)) {
        return;
    }
    global $allid_area;
    static $r = array();
    get_type_leve_area_nid_run($id);
    $r = $allid_area;
    $GLOBALS['GLOBALS']['allid_area'] = NULL;
    return array_reverse($r);
}
function get_type_leve_area_nid_run($id = "0")
{
    global $allid_area;
    $data_parent = $data = "";
    $data = d("Aacategory")->field("parent_id,type_nid,area_id")->find($id);
    $data_parent = d("Aacategory")->field("id,type_nid,area_id")->where("id = {$data['parent_id']}")->find();
    if (0 < isset($data_parent['type_nid'])) {
        if (!isset($allid_area[0])) {
            $allid_area[] = $data['type_nid'];
        }
        $allid_area[] = $data_parent['type_nid'];
        get_type_leve_area_nid_run($data_parent['id']);
    } else if (!isset($allid_area[0])) {
        $allid_area[] = $data['type_nid'];
    }
}
function get_son_type($id)
{
    $tempname = "type_sfs_son_all" . $id;
    if (!s($tempname)) {
        $row = get_son_type_run($id);
        s($tempname, $row, 3600 * c("HOME_CACHE_TIME"));
    } else {
        $row = s($tempname);
    }
    return $row;
}
function get_son_type_run($id)
{
    static $rerow = NULL;
    global $allid;
    $data = m("type")->field("id")->where("parent_id in ({$id})")->select();
    if (0 < count($data)) {
        foreach ($data as $key => $v) {
            $allid[] = $v['id'];
            $nowid[] = $v['id'];
        }
        $id = implode(",", $nowid);
        get_son_type_run($id);
    }
    $rerow = $allid;
    $allid = array();
    return $rerow;
}
function get_type_son($id = 0)
{
    $tempname = "type_son_all" . $id;
    if (!s($tempname)) {
        $row = get_type_son_all($id);
        s($tempname, $row, 3600 * c("HOME_CACHE_TIME"));
    } else {
        $row = s($tempname);
    }
    return $row;
}
function get_type_son_all($id = "0")
{
    static $rerow = NULL;
    global $get_type_son_all_row;
    if (empty($id)) {
        exit();
    }
    $data = m("type")->where("parent_id = {$id}")->field("id")->select();
    foreach ($data as $key => $v) {
        $get_type_son_all_row[] = $v['id'];
        $data_son = m("type")->where("parent_id = {$v['id']}")->field("id")->select();
        if (0 < count($data_son)) {
            get_type_son_all($v['id']);
        }
    }
    $rerow = $get_type_son_all_row;
    $get_type_son_all_row = array();
    return $rerow;
}
function get_type_parent_nid()
{
    $row = array();
    $p_nid_new = array();
    if (!s("type_parent_nid_temp")) {
        $data = m("type")->field("id")->select();
        if (0 < count($data)) {
            foreach ($data as $key => $v) {
                $p_nid = get_type_leve_nid($v['id']);
                $i = $n = count($p_nid);
                if (1 < $i) {
                    $j = 0;
                    for (; $j < $n; ++$j, --$i) {
                        $p_nid_new[$i - 1] = $p_nid[$j];
                    }
                } else {
                    $p_nid_new = $p_nid;
                }
                $row[$v['id']] = $p_nid_new;
            }
        }
        s("type_parent_nid_temp", $row, 3600 * c("HOME_CACHE_TIME"));
    } else {
        $row = s("type_parent_nid_temp");
    }
    return $row;
}
function get_type_list($model, $field = true)
{
    $acaheName = md5("type_list_temp" . $model . $field);
    if (!s($acaheName)) {
        $list = d(ucfirst($model))->getField($field);
        s($acaheName, $list, 3600 * c("HOME_CACHE_TIME"));
    } else {
        $list = s($acaheName);
    }
    return $list;
}
function get_type_infos()
{
    $row = array();
    $type_list = get_type_list("acategory", "id,type_nid,type_set");
    if (!isset($_GET['typeid'])) {
        $type_nid = get_type_leve();
        $rurl = explode("?", $_SERVER['REQUEST_URI']);
        $xurl_tmp = explode("/", str_replace(array("index.html", ".html"), array("", ""), $rurl[0]));
        $zu = implode("-", array_filter($xurl_tmp));
        $typeid = $type_nid[$zu];
        $typeset = $type_list[$typeid]['type_set'];
    } else {
        $typeid = intval($_GET['typeid']);
        $typeset = $type_list[$typeid]['type_set'];
    }
    if ($typeset == 1) {
        $templet = "list_index";
    } else {
        $templet = "index_index";
    }
    $row['typeset'] = $typeset;
    $row['templet'] = $templet;
    $row['typeid'] = $typeid;
    return $row;
}
function get_area_type_infos($area_id = 0)
{
    $row = array();
    $type_list = get_type_list("aacategory", "id,type_nid,type_set,area_id");
    if (!isset($_GET['typeid'])) {
        $type_nid = get_area_type_leve(0, $area_id);
        $rurl = explode("?", $_SERVER['REQUEST_URI']);
        $xurl_tmp = explode("/", str_replace(array("index.html", ".html"), array("", ""), $rurl[0]));
        $zu = implode("-", array_filter($xurl_tmp));
        $typeid = $type_nid[$area_id . $zu];
        $typeset = $type_list[$typeid]['type_set'];
    } else {
        $typeid = intval($_GET['typeid']);
        $typeset = $type_list[$typeid]['type_set'];
    }
    if ($typeset == 1) {
        $templet = "list_index";
    } else {
        $templet = "index_index";
    }
    $row['typeset'] = $typeset;
    $row['templet'] = $templet;
    $row['typeid'] = $typeid;
    return $row;
}
function get_type_leve_list($id = 0, $modelname = false)
{
    static $rerow = NULL;
    global $get_type_leve_list_run_row;
    if (!$modelname) {
        $model = d("type");
    } else {
        $model = d(ucfirst($modelname));
    }

    $stype = $modelname . "home_type_leve_list" . $id;

    if (!s($stype)) {
        get_type_leve_list_run($id, $model);
        $rerow = $get_type_leve_list_run_row;
        $GLOBALS['GLOBALS']['get_type_leve_list_run_row'] = NULL;
        $data = $rerow;
        s($stype, $data, 3600 * c("HOME_CACHE_TIME"));
    } else {
        $data = s($stype);
    }
//			var_dump($model->getlastsql());die;
//	var_dump($data);die;
    return $data;
}
function get_type_leve_list_run($id = 0, $model)
{
    global $get_type_leve_list_run_row;
    $spa = "----";
    if (count($get_type_leve_list_run_row) < 1) {
        $get_type_leve_list_run_row = array();
    }
    $typelist = $model->where("parent_id={$id}")->field("type_name,id,parent_id")->order("sort_order DESC")->select();
    foreach ($typelist as $k => $v) {
        $leve = intval(get_typeleve($v['id'], $model));
        $v['type_name'] = str_repeat($spa, $leve) . $v['type_name'];
        $get_type_leve_list_run_row[] = $v;
        $typelist_s1 = $model->where("parent_id={$v['id']}")->field("type_name,id")->select();
        if (0 < count($typelist_s1)) {
            get_type_leve_list_run($v['id'], $model);
        }
    }
}
function get_type_leve_list_area($id = 0, $modelname = false, $area_id = 0)
{
    static $rerow = NULL;
    global $get_type_leve_list_area_run_row;
    if (!$modelname) {
        $model = d("type");
    } else {
        $model = d(ucfirst($modelname));
    }
    $stype = $modelname . "home_type_leve_list_area" . $id . $area_id;
    if (!s($stype)) {
        get_type_leve_list_area_run($id, $model, $area_id);
        $rerow = $get_type_leve_list_area_run_row;
        $GLOBALS['GLOBALS']['get_type_leve_list_area_run_row'] = NULL;
        $data = $rerow;
        s($stype, $data, 3600 * c("HOME_CACHE_TIME"));
    } else {
        $data = s($stype);
    }
    return $data;
}
function get_type_leve_list_area_run($id = 0, $model, $area_id)
{
    global $get_type_leve_list_area_run_row;
    $spa = "----";
    if (count($get_type_leve_list_area_run_row) < 1) {
        $get_type_leve_list_area_run_row = array();
    }
    $typelist = $model->where("parent_id={$id} AND area_id={$area_id}")->field("type_name,id,parent_id")->order("sort_order DESC")->select();
    foreach ($typelist as $k => $v) {
        $leve = intval(get_typeleve($v['id'], $model));
        $v['type_name'] = str_repeat($spa, $leve) . $v['type_name'];
        $get_type_leve_list_area_run_row[] = $v;
        $typelist_s1 = $model->where("parent_id={$v['id']}")->field("type_name,id")->select();
        if (0 < count($typelist_s1)) {
            get_type_leve_list_area_run($v['id'], $model, $area_id);
        }
    }
}
function get_typeLeve($typeid, $model)
{
    $typeleve = 0;
    global $typeleve;
    static $rt = 0;
    get_typeleve_run($typeid, $model);
    $rt = $typeleve;
    unset($GLOBALS['typeleve']);
    return $rt;
}
function get_typeLeve_run($typeid, $model)
{
    global $typeleve;
    $condition['id'] = $typeid;
    $v = $model->field("parent_id")->where($condition)->find();
    if (0 < $v['parent_id']) {
        ++$typeleve;
        get_typeleve_run($v['parent_id'], $model);
    }
}
function de_xie($arr)
{
    $data = array();
    if (is_array($arr)) {
        foreach ($arr as $key => $v) {
            if (is_array($v)) {
                foreach ($v as $skey => $sv) {
                    if (is_array($sv)) {
                    } else {
                        $v[$skey] = stripslashes($sv);
                    }
                }
                $data[$key] = $v;
            } else {
                $data[$key] = stripslashes($v);
            }
        }
    } else {
        $data = stripslashes($arr);
    }
    return $data;
}
function text($text, $parseBr = false, $nr = false)
{
    $text = htmlspecialchars_decode($text);
    $text = safe($text, "text");
    if (!$parseBr && $nr) {
        $text = str_ireplace(array("\r", "\n", "\t", "&nbsp;"), "", $text);
        $text = htmlspecialchars($text, ENT_QUOTES);
    } else if (!$nr) {
        $text = htmlspecialchars($text, ENT_QUOTES);
    } else {
        $text = htmlspecialchars($text, ENT_QUOTES);
        $text = nl2br($text);
    }
    $text = trim($text);
    return $text;
}
function safe($text, $type = "html", $tagsMethod = true, $attrMethod = true, $xssAuto = 1, $tags = array(), $attr = array(), $tagsBlack = array(), $attrBlack = array())
{
    $text_tags = "";
    $font_tags = "<i><b><u><s><em><strong><font><big><small><sup><sub><bdo><h1><h2><h3><h4><h5><h6>";
    $base_tags = $font_tags . "<p><br><hr><a><img><map><area><pre><code><q><blockquote><acronym><cite><ins><del><center><strike>";
    $form_tags = $base_tags . "<form><input><textarea><button><select><optgroup><option><label><fieldset><legend>";
    $html_tags = $base_tags . "<ul><ol><li><dl><dd><dt><table><caption><td><th><tr><thead><tbody><tfoot><col><colgroup><div><span><object><embed>";
    $all_tags = $form_tags . $html_tags . "<!DOCTYPE><html><head><title><body><base><basefont><script><noscript><applet><object><param><style><frame><frameset><noframes><iframe>";
    $text = strip_tags($text, ${$type . "_tags"});
    if ($type != "all") {
        while (preg_match("/(<[^><]+) (onclick|onload|unload|onmouseover|onmouseup|onmouseout|onmousedown|onkeydown|onkeypress|onkeyup|onblur|onchange|onfocus|action|background|codebase|dynsrc|lowsrc)([^><]*)/i", $text, $mat)) {
            $text = str_ireplace($mat[0], $mat[1] . $mat[3], $text);
        }
        while (preg_match("/(<[^><]+)(window\\.|javascript:|js:|about:|file:|document\\.|vbs:|cookie)([^><]*)/i", $text, $mat)) {
            $text = str_ireplace($mat[0], $mat[1] . $mat[3], $text);
        }
    }
    return $text;
}
function h($text, $tags = null)
{
    $text = trim($text);
    $text = preg_replace("/<!--?.*-->/", "", $text);
    $text = preg_replace("/<!--?.*-->/", "", $text);
    $text = preg_replace("/<\\?|\\?>/", "", $text);
    $text = preg_replace("/<script?.*\\/script>/", "", $text);
    $text = str_replace("[", "&#091;", $text);
    $text = str_replace("]", "&#093;", $text);
    $text = str_replace("|", "&#124;", $text);
    $text = preg_replace("/\\r?\\n/", "", $text);
    $text = preg_replace("/<br(\\s\\/)?>/i", "[br]", $text);
    $text = preg_replace("/(\\[br\\]\\s*){10,}/i", "[br]", $text);
    while (preg_match("/(<[^><]+) (lang|on|action|background|codebase|dynsrc|lowsrc)[^><]+/i", $text, $mat)) {
        $text = str_replace($mat[0], $mat[1], $text);
    }
    while (preg_match("/(<[^><]+)(window\\.|javascript:|js:|about:|file:|document\\.|vbs:|cookie)([^><]*)/i", $text, $mat)) {
        $text = str_replace($mat[0], $mat[1] . $mat[3], $text);
    }
    if (empty($tags)) {
        $tags = "table|tbody|td|th|tr|i|b|u|strong|img|p|br|div|span|em|ul|ol|li|dl|dd|dt|a|alt|h[1-9]?";
        $tags .= "|object|param|embed";
    }
    $text = preg_replace("/<(\\/?(?:" . $tags . "))( [^><\\[\\]]*)?>/i", "[\\1\\2]", $text);
    $text = preg_replace("/<\\/?(html|head|meta|link|base|basefont|body|bgsound|title|style|script|form|iframe|frame|frameset|applet|id|ilayer|layer|name|style|xml)[^><]*>/i", "", $text);
    while (preg_match("/<([a-z]+)[^><\\[\\]]*>[^><]*<\\/\\1>/i", $text, $mat)) {
        $text = str_replace($mat[0], str_replace(">", "]", str_replace("<", "[", $mat[0])), $text);
    }
    while (preg_match("/(\\[[^\\[\\]]*=\\s*)(\\\"|')([^\\2\\[\\]]+)\\2([^\\[\\]]*\\])/i", $text, $mat)) {
        $text = str_replace($mat[0], $mat[1] . "|" . $mat[3] . "|" . $mat[4], $text);
    }
    $text = str_replace("<", "&lt;", $text);
    $text = str_replace(">", "&gt;", $text);
    $text = str_replace("\"", "&quot;", $text);
    $text = str_replace("[", "<", $text);
    $text = str_replace("]", ">", $text);
    $text = str_replace("|", "\"", $text);
    $text = str_replace("  ", " ", $text);
    return $text;
}
function get_thumb_pic($str)
{
    $path = explode("/", $str);
    $sc = count($path);
    $path[$sc - 1] = "thumb_" . $path[$sc - 1];
    return implode("/", $path);
}
function get_kvtable($nid = "")
{
    $stype = "kvtable" . $nid;
    $list = array();
    if (!s($stype)) {
        if (!empty($nid)) {
            $tmplist = m("kvtable")->where("nid='{$nid}'")->field(true)->select();
        } else {
            $tmplist = m("rule")->field(true)->select();
        }
        foreach ($tmplist as $v) {
            $list[$v['id']] = $v;
        }
        s($stype, $list, 3600 * c("HOME_CACHE_TIME"));
        $row = $list;
    } else {
        $list = s($stype);
        $row = $list;
    }
    return $row;
}
function cnsubstr($str, $length, $start = 0, $charset = "utf-8", $suffix = true)
{
    $str = strip_tags($str);
    if (function_exists("mb_substr")) {
        if (mb_strlen($str, $charset) <= $length) {
            return $str;
        }
        $slice = mb_substr($str, $start, $length, $charset);
    } else {
        $re['utf-8'] = "/[\x01-]|[ - ][ - ]|[ - ][ - ]{2}|[ - ][ - ]{3}/";
        $re['gb2312'] = "/[\x01-]|[ - ][ - ]/";
        $re['gbk'] = "/[\x01-]|[ - ][@- ]/";
        $re['big5'] = "/[\x01-]|[ - ]([@-~]| - ])/";
        preg_match_all($re[$charset], $str, $match);
        if (count($match[0]) <= $length) {
            return $str;
        }
        $slice = join("", array_slice($match[0], $start, $length));
    }
    if ($suffix) {
        return $slice . "…";
    }
    return $slice;
}

function clearHtml($str)
{
    $str = strip_tags($str);
    //首先去掉头尾空格
    $str = trim($str);
    //接着去掉两个空格以上的
    $str = preg_replace('/\s(?=\s)/', '', $str);
    //最后将非空格替换为一个空格
    $str = preg_replace('/[\n\r\t]/', ' ', $str);
    return $str;
}

function getLastTimeFormt($time, $type = 0)
{
    if ($type == 0) {
        $f = "m-d H:i";
    } else if ($type == 1) {
        $f = "Y-m-d H:i";
    }
    $agoTime = time() - $time;
    if ($agoTime <= 60 && 0 <= $agoTime) {
        return $agoTime . "秒前";
    } else if ($agoTime <= 3600 && 60 < $agoTime) {
        return intval($agoTime / 60) . "分钟前";
    } else if (date("d", $time) == date("d", time()) && 3600 < $agoTime) {
        return "今天 " . date("H:i", $time);
    } else if (date("d", $time + 86400) == date("d", time()) && $agoTime < 172800) {
        return "昨天 " . date("H:i", $time);
    } else {
        return date($f, $time);
    }
}
function get_avatar($uid, $size = "big", $type = "")
{
    
    $size = in_array($size, array("big", "middle", "small")) ? $size : "big";
    $uid = abs(intval($uid));
    $uid = sprintf("%09d", $uid);
    $dir1 = substr($uid, 0, 3);
    $dir2 = substr($uid, 3, 2);
    $dir3 = substr($uid, 5, 2);
    $typeadd = $type == "real" ? "_real" : "";
    $path = __ROOT__ . "/Style/header/customavatars/" . $dir1 . "/" . $dir2 . "/" . $dir3 . "/" . substr($uid, -2) . $typeadd . "_avatar_{$size}.jpg";
    
    if (!file_exists(c("WEB_ROOT") . $path)) {
        $path = __ROOT__ . "/Style/header/images/" . "noavatar_{$size}.gif";
    }
    return $path;
}
function get_app_avatar($uid){
    $res = M('members')->find($uid);
    if($res['avatar']){
        return $res['avatar'];
    }else{
       $path = __ROOT__ . "/Style/header/images/noavatar_big.gif"; 
       return $path;
    }
}
function get_Area_list($id = "")
{
    $cacheName = "temp_area_list_s";
    if (!s($cacheName)) {
        $list = m("area")->getField("id,name");
        s($cacheName, $list, 3.6e+009);
    } else {
        $list = s($cacheName);
    }
    if (!empty($id)) {
        return $list[$id];
    } else {
        return $list;
    }
}
function ip2area($ip = "")
{
    if (strlen($ip) < 6) {
        return;
    }
    import("ORG.Net.IpLocation");
    $Ip = new IpLocation("CoralWry.dat");
    $area = $Ip->getlocation($ip);
    $area = auto_charset($area);
    if ($area['country']) {
        $res = $area['country'];
    }
    if ($area['area']) {
        $res = $res . "(" . $area['area'] . ")";
    }
    if (empty($res)) {
        $res = "未知";
    }
    return $res;
}
function second2string($second, $type = 0)
{
    $day = floor($second / 86400);
    $second %= 86400;
    $hour = floor($second / 3600);
    $second %= 3600;
    $minute = floor($second / 60);
    $second %= 60;
    switch ($type) {
        case 0 :
            if (1 <= $day) {
                $res = $day . "天";
            } else if (1 <= $hour) {
                $res = $hour . "小时";
            } else {
                $res = $minute . "分钟";
            }
            break;
        case 1 :
            if (5 <= $day) {
                $res = date("Y-m-d H:i", time() + $second);
            } else if (1 <= $day && $day < 5) {
                $res = $day . "天前";
            } else if (1 <= $hour) {
                $res = $hour . "小时前";
            } else {
                $res = $minute . "分钟前";
                break;
            }
        case '2':
            if (1 <= $day) {
                $res = $day;
            }
            if($hour>0 || $minute>0){
                $res++;
            }
            $res .=  "天";
            break;            
    }
    return $res;
}
function getOrderSN($type, $id, $uid)
{
    switch ($type) {
        case "buy" :
            $str = substr(time(), 5) . $id . $uid;
            $str = "A" . str_pad($str, 14, "0", STR_PAD_LEFT);
            break;
        case "bc" :
            $str = substr(time(), 5) . $id . $uid;
            $str = "B" . str_pad($str, 14, "0", STR_PAD_LEFT);
            break;
    }
    return $str;
}
function FS($filename, $data = "", $path = "")
{
    $path = c("WEB_ROOT") . $path;
    if ($data == "") {
        $f = explode("/", $filename);
        $num = count($f);
        if (2 < $num) {
            $fx = $f;
            array_pop($f);
            $pathe = implode("/", $f);
            $re = f($fx[$num - 1], "", $pathe . "/");
        } else {
            isset($f[1]) ? ($re = f($f[1], "", c("WEB_ROOT") . $f[0] . "/")) : ($re = f($f[0]));
        }
        return $re;
    } else if (!empty($path)) {
        $re = f($filename, $data, $path);
    } else {
        $re = f($filename, $data);
    }
}
function formtUrl($url)
{
    if (!stristr($url, "http://")) {
        $url = str_replace("http://", "", $url);
    }
    $fourl = explode("/", $url);
    $domain = get_domain("http://" . $fourl[0]);
    $perfix = str_replace($domain, "", $fourl[0]);
    return $perfix . $domain;
}
function get_domain($url)
{
    $pattern = "/[/w-]+/.(com|net|org|gov|biz|com.tw|com.hk|com.ru|net.tw|net.hk|net.ru|info|cn|com.cn|net.cn|org.cn|gov.cn|mobi|name|sh|ac|la|travel|tm|us|cc|tv|jobs|asia|hn|lc|hk|bz|com.hk|ws|tel|io|tw|ac.cn|bj.cn|sh.cn|tj.cn|cq.cn|he.cn|sx.cn|nm.cn|ln.cn|jl.cn|hl.cn|js.cn|zj.cn|ah.cn|fj.cn|jx.cn|sd.cn|ha.cn|hb.cn|hn.cn|gd.cn|gx.cn|hi.cn|sc.cn|gz.cn|yn.cn|xz.cn|sn.cn|gs.cn|qh.cn|nx.cn|xj.cn|tw.cn|hk.cn|mo.cn|org.hk|is|edu|mil|au|jp|int|kr|de|vc|ag|in|me|edu.cn|co.kr|gd|vg|co.uk|be|sg|it|ro|com.mo)(/.(cn|hk))*/";
    preg_match($pattern, $url, $matches);
    if (0 < count($matches)) {
        return $matches[0];
    } else {
        $rs = parse_url($url);
        $main_url = $rs['host'];
        if (!strcmp(long2ip(sprintf("%u", ip2long($main_url))), $main_url)) {
            return $main_url;
        } else {
            $arr = explode(".", $main_url);
            $count = count($arr);
            $endArr = array("com", "net", "org");
            if (in_array($arr[$count - 2], $endArr)) {
                $domain = $arr[$count - 3] . "." . $arr[$count - 2] . "." . $arr[$count - 1];
            } else {
                $domain = $arr[$count - 2] . "." . $arr[$count - 1];
            }
            return $domain;
        }
    }
}
function getFloatValue($f, $len)
{
    $tmpInt = intval($f);
    $tmpDecimal = bcsub($f , $tmpInt,2);
    $str = "{$tmpDecimal}";
    $subStr = strstr($str, ".");
    if (false === $subStr) {
        0 < $len ? ($repetstr = "." . str_repeat("0", $len)) : ($repetstr = "");
        return $tmpInt . $repetstr;
    }
    if (strlen($subStr) < $len + 1) {
        $repeatCount = $len + 1 - strlen($subStr);
        $str = $str . "" . str_repeat("0", $repeatCount);
    }
    return $tmpInt . "" . substr($str, 1, 1 + $len);
}
function get_remote_img($content)
{
    $rt = c("WEB_ROOT");
    $img_dir = c("REMOTE_IMGDIR") ? c("REMOTE_IMGDIR") : "/UF/Remote";
    $base_dir = substr($rt, 0, strlen($rt) - 1);
    $content = stripslashes($content);
    $img_array = array();
    preg_match_all("/(src|SRC)=[\"|'| ]{0,}(http:\\/\\/(.*)\\.(gif|jpg|jpeg|bmp|png|ico))/isU", $content, $img_array);
    $img_array = array_unique($img_array[2]);
    set_time_limit(0);
    $imgUrl = $img_dir . "/" . strftime("%Y%m%d", time());
    $imgPath = $base_dir . $imgUrl;
    $milliSecond = strftime("%H%M%S", time());
    if (!is_dir($imgPath)) {
        makedir($imgPath, 511);
    }
    foreach ($img_array as $key => $value) {
        $value = trim($value);
        $get_file = @file_get_contents($value);
        $rndFileName = $imgPath . "/" . $milliSecond . $key . "." . substr($value, -3, 3);
        $fileurl = $imgUrl . "/" . $milliSecond . $key . "." . substr($value, -3, 3);
        if ($get_file) {
            $fp = @fopen($rndFileName, "w");
            @fwrite($fp, $get_file);
            @fclose($fp);
        }
        $content = ereg_replace($value, $fileurl, $content);
    }
    return $content;
}
function ajaxmsg($msg = "", $type = 1, $is_end = true)
{
    
    $json['status'] = $type;
    if (is_array($msg)) {
        foreach ($msg as $key => $v) {
            $json[$key] = $v;
        }
    } else if (!empty($msg)) {
        $json['message'] = $msg;
    }
    if ($is_end) {
        exit(json_encode($json));
    } else {
        echo json_encode($json);
    }
}
function hidecard($cardnum, $type = 1, $default = "")
{
    if (empty($cardnum)) {
        return $default;
    }
    if ($type == 1) {
        $cardnum = substr($cardnum, 0, 3) . str_repeat("*", 12) . substr($cardnum, strlen($cardnum) - 4);
    } else if ($type == 2) {
        $cardnum = substr($cardnum, 0, 3) . str_repeat("*", 5) . substr($cardnum, strlen($cardnum) - 4);
    } else if ($type == 3) {
        $cardnum = str_repeat("*", strlen($cardnum) - 4) . substr($cardnum, strlen($cardnum) - 4);
    } else if ($type == 4) {
        $cardnum = substr($cardnum, 0, 3) . str_repeat("*", strlen($cardnum) - 3);
    }else if ($type == 5) {
         $cardnum = mb_substr($cardnum, 0, 1, 'utf-8') . str_repeat("*", 2) . mb_substr($cardnum,-1,1,'utf-8');
    }else if ($type == 6) {
         $cardnum = mb_substr($cardnum, 0, 3, 'utf-8') . str_repeat("***", 2) . substr($cardnum, strlen($cardnum) - 2);
    }
    //$cardnum=iconv("UTF-8", "GBK", $cardnum); 
    return $cardnum;
}
function setmb($size)
{
    $mbsize = $size / 1024 / 1024;
    if (0 < $mbsize) {
        list($t1, $t2) = explode(".", $mbsize);
        $mbsize = $t1 . "." . substr($t2, 0, 2);
    }
    if ($mbsize < 1) {
        $kbsize = $size / 1024;
        list($t1, $t2) = explode(".", $kbsize);
        $kbsize = $t1 . "." . substr($t2, 0, 2);
        return $kbsize . "KB";
    } else {
        return $mbsize . "MB";
    }
}
function getMoneyFormt($money)
{
    if (10000 <= $money) {
        $res = getFloatvalue($money / 10000,2) . "万";
    } else {
        $res = getFloatvalue($money, 2);
    }
    return $res;
}
function getArea()
{
    $area = fs("Webconfig/area");
    if (!is_array($area)) {
        $list = m("area")->getField("id,name");
        fs("area", $list, "Webconfig/");
    } else {
        return $area;
    }
}
function getLeveIco($num, $type = 1)
{
    $leveconfig = fs("Webconfig/leveconfig");
    foreach ($leveconfig as $key => $v) {
        if ($v['start'] <= $num && $num < $v['end']) {
            if ($type == 1) {
                return "/UF/leveico/" . $v['icoName'];
            } else {
                return "<img src=\"" . __ROOT__ . "/UF/leveico/" . $v['icoName'] . "\" title=\"" . $v['name'] . "\"/>";
            }
        }
    }
}
function getLeveName($num, $type = 1)
{
    $leveconfig = fs("Webconfig/leveconfig");
    foreach ($leveconfig as $key => $v) {
        if ($v['start'] <= $num && $num < $v['end']) {
            if ($type == 1) {
                return $v['name'];
            } else {
                return $v['name'];
            }
        }
    }
}
function getLeveId($num, $type = 1)
{
    $leveconfig = fs("Webconfig/leveconfig");    
    foreach ($leveconfig as $key => $v) {        
        if ($v['start'] <= $num && $num < $v['end']) {
            if ($type == 1) {
                return $key;
            } else {
                return $key;
            }
        }
    }
}
function getLeveTixian($num, $type = 1)
{   
    $datag = get_global_setting();
    return $datag["fee_invest_manage"];
    $leveconfig = fs("Webconfig/leveconfig");
    foreach ($leveconfig as $key => $v) {
        if ($v['start'] <= $num && $num < $v['end']) {
            if ($type == 1) {
                return $v['txfeiyong'];
            } else {
                return $v['txfeiyong'];
            }
        }
    }
}
function getAgeName($num)
{
    $ageconfig = fs("Webconfig/ageconfig");
    foreach ($ageconfig as $key => $v) {
        if ($v['start'] <= $num && $num < $v['end']) {
            return $v['name'];
        }
    }
}
function getLocalhost()
{
    $vo['id'] = 1;
    $vo['name'] = "主站";
    $vo['domain'] = "www";
    return $vo;
}
function Fmoney($money,$flag="￥")
{
    if (!is_numeric($money)) {
        return $flag."0.00";
    }
    $sb = "";
    if ($money < 0) {
        $sb = "-";
        $money *= -1;
    }
    $dot = explode(".", $money);
    $tmp_money = strrev_utf8($dot[0]);
    $format_money = "";
    $i = 3;
    for (; $i < strlen($dot[0]); $i += 3) {
        $format_money .= substr($tmp_money, 0, 3) . ",";
        $tmp_money = substr($tmp_money, 3);
    }
    $format_money .= $tmp_money;
    if (empty($sb)) {
        $format_money = $flag . strrev_utf8($format_money);
    } else {
        $format_money = $flag."-" . strrev_utf8($format_money);
    }
    if ($dot[1]) {
        return $format_money . "." . $dot[1];
    } else {
        return $format_money;
    }
}
function strrev_utf8($str)
{
    return join("", array_reverse(preg_split("//u", $str)));
}
function getInvestUrl($id)
{
    return __APP__ . "/invest/{$id}" . c("URL_HTML_SUFFIX");
}
function getinvestnums($bid){
    return m("borrow_investor")->where("borrow_id={$bid}")->count();
}
function get_invest_list($bid){
    $row = m("investor_detail")->where("borrow_id={$bid}")->group("sort_order")->select();
    return $row;
}
function get_admin_name($id = false)
{
    $stype = "adminlist";
    $list = array();
    if (!s($stype)) {
        $rule = m("ausers")->field("id,user_name")->select();
        foreach ($rule as $v) {
            $list[$v['id']] = $v['user_name'];
        }
        s($stype, $list, 3600 * c("HOME_CACHE_TIME"));
        if (!$id) {
            $row = $list;
        } else {
            $row = $list[$id];
        }
    } else {
        $list = s($stype);
        if ($id === false) {
            $row = $list;
        } else {
            $row = $list[$id];
        }
    }
    return $row;
}
function getIco($map,$class="mem_spec65")
{
    $str = "";
    if (0 < $map['reward_type']) {
        $str .= "<span class=\"{$class} att_jiang\">奖</span> ";
    }
    if ($map['borrow_type'] == 2) {
        $str .= "<span class=\"{$class} att_bao\">保</span> ";
    } else if ($map['borrow_type'] == 3) {
        $str .= "<span style='' class=\"{$class} att_miao\">秒</span> ";
    } else if ($map['borrow_type'] == 4) {
        $str .= "<span class=\"{$class} att_jing\">净</span> ";
    } else if ($map['borrow_type'] == 1) {
        $str .= "<span style='' class=\"{$class} att_xin\">信</span >";
    } else if ($map['borrow_type'] == 5) {
        $str .= "<span class=\"{$class} att_di\">抵</span> ";
    }
    if ($map['repayment_type'] == 1) {
        $str .= " <span class=\"{$class} att_tian\">天</span> ";
    }
    return $str;
}
function addMsg($from, $to, $title, $msg, $type = 1)
{
    if (empty($from) || empty($to)) {
        return;
    }
    $data['from_uid'] = $from;
    $data['from_uname'] = m("members")->getFieldById($from, "user_name");
    $data['to_uid'] = $to;
    $data['to_uname'] = m("members")->getFieldById($to, "user_name");
    $data['title'] = $title;
    $data['msg'] = $msg;
    $data['add_time'] = time();
    $data['is_read'] = 0;
    $data['type'] = $type;
    $newid = m("member_msg")->add($data);
    return $newid;
}
function getSubSite()
{
    $map['is_open'] = 1;
    $list = m("area")->field(true)->where($map)->select();
    $cdomain = explode(".", $_SERVER['HTTP_HOST']);
    $cpx = array_pop($cdomain);
    $doamin = array_pop($cdomain);
    $host = "." . $doamin . "." . $cpx;
    foreach ($list as $key => $v) {
        $list[$key]['host'] = "http://" . $v['domain'] . $host;
    }
    return $list;
}
function notice($type, $uid, $data = array())
{
    $datag = get_global_setting();
    $datag = de_xie($datag);
    $msgconfig = fs("Webconfig/msgconfig");
    $emailTxt = fs("Webconfig/emailtxt");
    $smsTxt = fs("Webconfig/smstxt");
    $msgTxt = fs("Webconfig/msgtxt");
    $emailTxt = de_xie($emailTxt);
    $smsTxt = de_xie($smsTxt);
    $msgTxt = de_xie($msgTxt); 
    import("ORG.Net.Email");
    $port = 25;
    $smtpserver = $msgconfig['stmp']['server'];
    $smtpuser = $msgconfig['stmp']['user'];
    $smtppwd = $msgconfig['stmp']['pass'];
    $mailtype = "HTML";
    $sender = $msgconfig['stmp']['user'];
    $smtp = new smtp($smtpserver, $port, true, $smtpuser, $smtppwd, $sender);
    $minfo = m("members")->field("user_email,user_name,user_phone")->find($uid);
    $uname = $minfo['user_name'];
//	var_dump(1);die;
    switch ($type) {
        case 1 :
            $vcode = rand_string($uid, 32, 0, 1);
            $link = "<a href=\"" . c("WEB_URL") . "/member/common/emailverify?vcode=" . $vcode . "\">点击链接验证邮件</a>";
            $body = str_replace(array(
                "#UserName#"
            ), array(
                $uname
            ), $msgTxt['regsuccess']);
            addInnerMsg($uid, "恭喜您注册成功", $body);
            $subject = "您刚刚在" . $datag['web_name'] . "注册成功";
            $body = str_replace(array(
                "#UserName#",
                "#LINK#"
            ), array(
                $uname,
                $link
            ), $emailTxt['regsuccess']);
            $to = $minfo['user_email'];
            $send = $smtp->sendmail($to, $sender, $subject, $body, $mailtype);
            return $send;
        case 2 :
            $vcode = rand_string($uid, 10, 3, 3);
            $pcode = rand_string($uid, 6, 1, 3);
            $subject = "您刚刚在" . $datag['web_name'] . "注册成功";
            $body = str_replace(array(
                "#CODE#"
            ), array(
                $vcode
            ), $emailTxt['safecode']);
            $to = $minfo['user_email'];
            $send = $smtp->sendmail($to, $sender, $subject, $body, $mailtype);
            $content = str_replace(array(
                "#CODE#"
            ), array(
                $pcode
            ), $smsTxt['safecode']);
            $sendp = sendsms($minfo['user_phone'], $content);
            return $send;
        case 3 :
            $vcode = rand_string($uid, 6, 1, 4);
            $content = str_replace(array(
                "#CODE#"
            ), array(
                $vcode
            ), $smsTxt['safecode']);
            $send = sendsms($minfo['user_phone'], $content);
            return $send;
        case 4 :
            $vcode = rand_string($uid, 6, 1, 5);
            $content = str_replace(array(
                "#CODE#"
            ), array(
                $vcode
            ), $smsTxt['safecode']);
            $send = sendsms($data['phone'], $content);
            return $send;
        case 5 :
            $vcode = rand_string($uid, 10, 1, 6);
            $subject = "您刚刚在" . $datag['web_name'] . "申请更换手机的安全码";
            $body = str_replace(array(
                "#CODE#"
            ), array(
                $vcode
            ), $emailTxt['changephone']);
            $to = $minfo['user_email'];
            $send = $smtp->sendmail($to, $sender, $subject, $body, $mailtype);
            return $send;
        case 6 :
            $subject = "恭喜，你在" . $datag['web_name'] . "发布的项目审核通过";
            $body = str_replace(array(
                "#UserName#"
            ), array(
                $uname
            ), $emailTxt['verifysuccess']);
            $to = $minfo['user_email'];
            $send = $smtp->sendmail($to, $sender, $subject, $body, $mailtype);
            $body = str_replace(array(
                "#UserName#"
            ), array(
                $uname
            ), $msgTxt['verifysuccess']);
            addInnerMsg($uid, "恭喜项目审核通过", $body);
            return $send;
        case 7 :
            $vcode = rand_string($uid, 32, 0, 7);
            $link = "<a href=\"" . c("WEB_URL") . "/member/common/getpasswordverify?vcode=" . $vcode . "\">点击链接验证邮件</a>";
            $subject = "您刚刚在" . $datag['web_name'] . "申请了密码找回";
            $body = str_replace(array(
                "#UserName#",
                "#LINK#"
            ), array(
                $uname,
                $link
            ), $emailTxt['getpass']);
            $to = $minfo['user_email'];
            $send = $smtp->sendmail($to, $sender, $subject, $body, $mailtype);
            return $send;
        case 8 :
            $vcode = rand_string($uid, 32, 0, 1);
            $link = "<a href=\"" . c("WEB_URL") . "/member/common/emailverify?vcode=" . $vcode . "\">点击链接验证邮件</a>";
            $subject = "您刚刚在" . $datag['web_name'] . "申请邮件验证";
            $body = str_replace(array(
                "#UserName#",
                "#LINK#"
            ), array(
                $uname,
                $link
            ), $emailTxt['regsuccess']);
            $to = $minfo['user_email'];
            $send = $smtp->sendmail($to, $sender, $subject, $body, $mailtype);
            return $send;
    }
}

function sendsmsaly($phone,$data,$moban,$content){
    vendor('Alisms.sendSms');
    $sendSms = new sendSms();
    $template=$moban;
    $phone=$phone;
    $param=$data;
    $jsdata=$sendSms->alisendSms($phone,$template,$param);
    $jsdata=json_decode(json_encode($jsdata),true);

    $client_ip = explode(':', get_client_ip(),2);
    $addData['content'] = $content;
    $addData['type'] = $type;
    $addData['cellphone'] = $mob;
    $addData['add_time'] = time();
    $addData['add_ip'] = $client_ip[0];    
    $addData['status'] = 0;
    $addData['uid'] = session("u_id");
    $addData['aid'] = session("admin");

    $m = M('log_sms');
    $newid = $m->add($addData);
    if($jsdata['Code']=='OK'){
        $m->save(array('status'=>1,'id'=>$newid));
        return true;
        // var_dump("发送成功！");
    }else{
        $m->save(array('status'=>2,'id'=>$newid));
        return false;     
        //var_dump($jsdata["Message"]);
    } 
}
function notice1($type, $uid, $data = array())
{
    $datag = get_global_setting();
    $datag = de_xie($datag);
    $msgconfig = fs("Webconfig/msgconfig"); 
    $smsTxt = fs("Webconfig/smstxt");
    $msgTxt = fs("Webconfig/msgtxt"); 
    $msgTxt = de_xie($msgTxt); 

    $minfo = m("members")->field("user_email,user_name,user_phone")->find($uid);
    $uname = $minfo['user_name'];

    $typed=$datag["dx_type"]; //var_dump($typed);exit();
    if($typed==1){
        switch ($type) {
            case 1 :
                $content = str_replace(array(
                    "#CODE#"
                ), array(
                    $data["code"]
                ), $smsTxt['alyzm']["content"]);
                $phone=$data["phone"];
                $datas=array("code"=>$data["code"]);
                $moban="SMS_162545326";
                $sendp = sendsmsaly($phone,$datas,$moban,$content);
                return $sendp;
            case 2 : 
    //             $content = str_replace(array(
    //                 "#CODE#",
    //                 "#UserName#"
    //             ), array(
    //                 $data["code"],
    //                 $uname
    //             ), $smsTxt['verify_phone']);
    // //              var_dump($content);
    //                         //die;
    //             $sendp = sendsms20181216($data['phone'],$content);
                //sendsms($data['phone'], $content);
                $content = str_replace(array(
                    "#CODE#"
                ), array(
                    $data["code"]
                ), $smsTxt['alyzm']["content"]);
                $phone=$data["phone"];
                $datas=array("code"=>$data["code"]);
                $moban="SMS_162545326";
                $sendp = sendsmsaly($phone,$datas,$moban,$content);
                return $sendp;
            case 3 :
    //                 $content = str_replace(array(
    //                 "#CODE#",
    //                 "#UserName#"
    //             ), array(
    //                 $data["code"],
    //                 $uname
    //             ), $smsTxt['safecode']);
    // //              var_dump($content);
    //                         //die;
    //             $sendp = sendsms20181216($data['phone'],$content);
    //             //sendsms($data['phone'], $content);
    //             return $sendp;
                $content = str_replace(array(
                    "#CODE#"
                ), array(
                    $data["code"]
                ), $smsTxt['alyzm']["content"]);
                $phone=$data["phone"];
                $datas=array("code"=>$data["code"]);
                $moban="SMS_162545326";
                $sendp = sendsmsaly($phone,$datas,$moban,$content);
                return $sendp;
            case 4 :
    //             $content = str_replace(array(
    //                 "#CODE#",
    //                 "#UserName#"
    //             ), array(
    //                 $data["code"],
    //                 $uname
    //             ), $smsTxt['changephone']);
    // //              var_dump($content);
    //                         //die;
    //             $sendp = sendsms20181216($data['phone'],$content);
    //             //sendsms($data['phone'], $content);
    //             return $sendp;
                $content = str_replace(array(
                    "#CODE#"
                ), array(
                    $data["code"]
                ), $smsTxt['alyzm']["content"]);
                $phone=$data["phone"];
                $datas=array("code"=>$data["code"]);
                $moban="SMS_162545326";
                $sendp = sendsmsaly($phone,$datas,$moban,$content);
                return $sendp;
            case 5 :
                //线上充值成功
    //             $content = str_replace(array(
    //                 "#MONEY#",
    //                 "#UserName#"
    //             ), array(
    //                 $data["MONEY"],
    //                 $uname
    //             ), $smsTxt["payonline"]['content']);
    // //              var_dump($content);
    //                         //die;
    //             $sendp = sendsms20181216($minfo["user_phone"],$content);
    //             //sendsms($data['phone'], $content);
    //             return $sendp;
    //       线上充值成功：sms[payonline][content]
                $content = str_replace(array(
                    "#MONEY#",
                    "#UserName#"
                ), array(
                    $data["MONEY"],
                    $uname
                ), $smsTxt["payoffline"]["content"]);

                $phone=$minfo["user_phone"];
                $datas=array("user_name"=>$uname,"money"=>$data["MONEY"]);
                $moban="SMS_181192282";
                $sendp = sendsmsaly($phone,$datas,$moban,$content);
                return $sendp;

            case 6 :
                            //线下充值成功
                $content = str_replace(array(
                    "#MONEY#",
                    "#UserName#"
                ), array(
                    $data["MONEY"],
                    $uname
                ), $smsTxt["payoffline"]["content"]);

                $phone=$minfo["user_phone"];
                $datas=array("user_name"=>$uname,"money"=>$data["MONEY"]);
                $moban="SMS_181192282";
                $sendp = sendsmsaly($phone,$datas,$moban,$content);
                return $sendp;
    //
    //线下充值成功：sms[payoffline][content]


            case 7 :
                        //还款到帐
                $content = str_replace(array(
                    "#BORROW_NAME#",
                    "#UserName#"
                ), array(
                    $data["BORROW_NAME"],
                    $uname
                ), $smsTxt["payback"]["content"]);
                    // var_dump($minfo,$content);die;
                //$sendp = sendsms20181216($minfo["user_phone"],$content);
                
                $phone=$minfo["user_phone"];
                $datas=array("BORROW_NAME"=>$data["BORROW_NAME"]);
                $moban="SMS_181856254";
                $sendp = sendsmsaly($phone,$datas,$moban,$content);
                return $sendp;

                //sendsms($data['phone'], $content);
               // return $sendp;
    //还款到帐：sms[payback][content]
    //      

            case 8 :
                                //提现成功
                $content = str_replace(array(
                    "#MONEY#",
                    "#UserName#"
                ), array(
                    $data["MONEY"],
                    $uname
                ), $smsTxt["alywithdraw"]["content"] );

                $phone=$minfo["user_phone"];
                $datas=array("money"=>$data["MONEY"]);
                $moban="SMS_181851318";
                $sendp = sendsmsaly($phone,$datas,$moban,$content);
                return $sendp;

    //提现成功： sms[withdraw][content] 
            case 9 :       
       //发标初审通过
                $content = str_replace(array( 
                     "#BORROW_NAME#",
                     "#UserName#"
                ), array(
                    $data["BORROW_NAME"], 
                     $uname
                ), $smsTxt["firstV"]["content"] );

                $phone=$minfo["user_phone"];
                $datas=array();
                $moban="SMS_181868712";
                $sendp = sendsmsaly($phone,$datas,$moban,$content);
                return $sendp;
    //发标初审通过： sms[firstV][content]
            case 10 :
                //发标初审通过
                $time=date("Y-m-d H:i:s",time());
                $content = str_replace(array(
                    "#UserName#",
                    "#time#"
                ), array(
                    $uname,
                    $time
                ), $smsTxt['xxchognzhi']);
    //              var_dump($content);
                //die;
                // date_default_timezone_set('Asia/Shanghai');
                // $h=date("H");
                // if($h>=18 || $h<=6){
                //    //$sendp = sendsms20181216('15563335882',$content);
                //    $phone='15563335882'; 
                // }
                
                // if($h>6&&$h<18){
                //     //$sendp = sendsms20181216('15165036880',$content);
                //     $phone='15165036880'; 
                //     //$phone='13651076469'; 
                // }
                $phone='17865551993';
                //sendsms($data['phone'], $content);

                //$phone=$data["phone"];
                $datas=array("UserName"=>$uname,"time"=>$time);
                $moban="SMS_181690237";
                $sendp = sendsmsaly($phone,$datas,$moban,$content);
                return $sendp;
            //充值失败： sms[firstV][content]
            case 11 :

               $content = str_replace(array(
                    "#user_name#",
                    "#fangshi#",
                    "#yuanyin#"
                ), array(
                    $data["user_name"],
                    $data["fangshi"],
                    $data["yuanyin"]
                ), $smsTxt['shibai']["content"]);

                $phone=$minfo["user_phone"];
                $datas=array("user_name"=>$data["user_name"],"fangshi"=>$data["fangshi"],"yuanyin"=>$data["yuanyin"]);
                $moban="SMS_181856236";
                $sendp = sendsmsaly($phone,$datas,$moban,$content);
                return $sendp;
            //优惠券过期提醒： sms[firstV][content]
            case 12 :
                $content = str_replace(array(
                    "#code#",
                    "#time#"
                ), array(
                    $data["money"],
                    $data["time"]
                ), $smsTxt['alguoqi']);


                $phone=$minfo["user_phone"];
                $datas=array("code"=>$data["money"],"time"=>$data["time"]);
                $moban="SMS_184620275";
                $sendp = sendsmsaly($phone,$datas,$moban,$content);
                return $sendp;
            //积分商城发货通知： sms[firstV][content]
            case 13 :
                $content = str_replace(array(
                    "#name#",
                    "#danhao#"
                ), array(
                    $data["name"],
                    $data["danhao"]
                ), $smsTxt['jffahuo']);
                $phone=$data["phone"];
                $datas=array("name"=>$data["name"],"danhao"=>$data["danhao"]);
                $moban="SMS_227739727";
                $sendp = sendsmsaly($phone,$datas,$moban,$content);
                return $sendp;
            //赠品发货通知： sms[firstV][content]
            case 14 :
                $content = str_replace(array(
                    "#name#",
                    "#danhao#"
                ), array(
                    $data["name"],
                    $data["danhao"]
                ), $smsTxt['zpfahuo']);
                $phone=$data["phone"];
                $datas=array("name"=>$data["name"],"danhao"=>$data["danhao"]);
                $moban="SMS_227749740";
                $sendp = sendsmsaly($phone,$datas,$moban,$content);
                return $sendp;

        }


    }
    if($typed==2){
        switch ($type) {
            case 1 :
     			 $content = str_replace(array(
                    "#CODE#",
                    "#UserName#"
                ), array(
                    $data["code"],
                    $data["phone"]
                ), $smsTxt['yzm']);
    				// var_dump($content);
    				// 		die;
                $sendp = sendsms20181216($data['phone'],$content);
                //sendsms($data['phone'], $content);
                return $sendp;
            case 2 : 
                $content = str_replace(array(
                    "#CODE#",
                    "#UserName#"
                ), array(
                    $data["code"],
                    $uname
                ), $smsTxt['verify_phone']);
    //				var_dump($content);
    						//die;
                $sendp = sendsms20181216($data['phone'],$content);
                //sendsms($data['phone'], $content);
                return $sendp;
            case 3 :
                    $content = str_replace(array(
                    "#CODE#",
                    "#UserName#"
                ), array(
                    $data["code"],
                    $uname
                ), $smsTxt['safecode']);
    //				var_dump($content);
    						//die;
                $sendp = sendsms20181216($data['phone'],$content);
                //sendsms($data['phone'], $content);
                return $sendp;
            case 4 :
                $content = str_replace(array(
                    "#CODE#",
                    "#UserName#"
                ), array(
                    $data["code"],
                    $uname
                ), $smsTxt['changephone']);
    //				var_dump($content);
    						//die;
                $sendp = sendsms20181216($data['phone'],$content);
                //sendsms($data['phone'], $content);
                return $sendp;
            case 5 :
    			//线上充值成功
    			$content = str_replace(array(
                    "#MONEY#",
                    "#UserName#"
                ), array(
                    $data["MONEY"],
                    $uname
                ), $smsTxt["payonline"]['content']);
    //				var_dump($content);
    						//die;
                $sendp = sendsms20181216($minfo["user_phone"],$content);
                //sendsms($data['phone'], $content);
                return $sendp;
    //  	 线上充值成功：sms[payonline][content]

            case 6 :
    						//线下充值成功
    			$content = str_replace(array(
                    "#MONEY#",
                    "#UserName#"
                ), array(
                    $data["MONEY"],
                    $uname
                ), $smsTxt["payoffline"]["content"]);
    //				var_dump($content);
    						//die;
                $sendp = sendsms20181216($minfo["user_phone"],$content);
                //sendsms($data['phone'], $content);
                return $sendp;
    //
    //线下充值成功：sms[payoffline][content]


            case 7 :
     					//还款到帐
    			$content = str_replace(array(
                    "#BORROW_NAME#",
                    "#UserName#"
                ), array(
                    $data["BORROW_NAME"],
                    $uname
                ), $smsTxt["payback"]["content"]);
    				// var_dump($minfo,$content);die;
                $sendp = sendsms20181216($minfo["user_phone"],$content);
                
    						
                //sendsms($data['phone'], $content);
                return $sendp;
    //还款到帐：sms[payback][content]
    //		

            case 8 :
    							//提现成功
    			$content = str_replace(array(
                    "#MONEY#",
                    "#UserName#"
                ), array(
                    $data["MONEY"],
                    $uname
                ), $smsTxt["withdraw"]["content"] );
    //				var_dump($content);
    						//die;
                $sendp = sendsms20181216($minfo["user_phone"],$content);
                //sendsms($data['phone'], $content);
                return $sendp;
    //提现成功： sms[withdraw][content] 
            case 9 :
    							//发标初审通过
    			$content = str_replace(array( 
    				 "#BORROW_NAME#",
    				 "#UserName#"
                ), array(
                    $data["BORROW_NAME"], 
                     $uname
                ), $smsTxt["firstV"]["content"] );
    //				var_dump($content);
    						//die;
                $sendp = sendsms20181216($minfo["user_phone"],$content);
                //sendsms($data['phone'], $content);
                return $sendp;
     
    //发标初审通过： sms[firstV][content]
            case 10 :
                //发标初审通过
                $content = str_replace(array(
                    "#UserName#",
                    "#time#"
                ), array(
                    $uname,
                    date("Y-m-d H:i:s",time())
                ), $smsTxt['xxchognzhi']);
    //              var_dump($content);
                //die;
                // date_default_timezone_set('Asia/Shanghai');
                // $h=date("H");
                // if($h>=18 || $h<=6){
                //    $sendp = sendsms20181216('15563335882',$content); 
                // }
                
                // if($h>6&&$h<18){
                //     $sendp = sendsms20181216('15165036880',$content);
                // }
                $sendp = sendsms20181216('15563335882',$content);
                //sendsms($data['phone'], $content);
                return $sendp;
            //充值失败： sms[firstV][content]
            case 11 :
                $content = str_replace(array(
                    "#user_name#",
                    "#fangshi#",
                    "#yuanyin#"
                ), array(
                    $data["user_name"],
                    $data["fangshi"],
                    $data["yuanyin"]
                ), $smsTxt['shibai']["content"]);

                $sendp = sendsms20181216($minfo["user_phone"],$content);
                return $sendp;
            //优惠券过期提醒： sms[firstV][content]
            case 12 : 
                $content = str_replace(array(
                    "#code#",
                    "#time#"
                ), array(
                    $data["money"],
                    $data["time"],
                ), $smsTxt['guoqi']["content"]);

                $sendp = sendsms20181216($minfo["user_phone"],$content);
                return $sendp;
               
        }
    }   
}


function SMStip($type, $mob, $from = array(), $to = array())
{
    if (empty($mob)) {
        return;
    }
	//测试
	return true;
    $datag = get_global_setting();
    $datag = de_xie($datag);
    $smsTxt = fs("Webconfig/smstxt");
    $smsTxt = de_xie($smsTxt);
    if ($smsTxt[$type]['enable'] == 1) {
        $body = str_replace($from, $to, $smsTxt[$type]['content']);
        $to = $minfo['user_email'];
        $send = sendsms($mob, $body);
    } else {
        return;
    }
}
function MTip($type, $uid = 0, $info = "")
{
    $tpyed='1';
    $datag = get_global_setting();
    $typed=$datag["dx_type"];
    $datag = get_global_setting();
    $datag = de_xie($datag);
    $port = 25;
    $id1 = "{$type}_1";
    $id2 = "{$type}_2";
    $per = c("DB_PREFIX");
    $sql = "select 1 as tip1,0 as tip2,m.user_email,m.id ,m.user_phone from {$per}members m WHERE m.id={$uid}";
    $memail = m()->query($sql);
    switch ($type) {
        case "chk1" :
            $to = "";
            $subject = "您刚刚在" . $datag['web_name'] . "修改了登录密码";
            $body = "您刚刚在" . $datag['web_name'] . "修改了登录密码,如不是自己操作,请尽快联系客服";
            $innerbody = "您刚刚修改了登录密码,如不是自己操作,请尽快联系客服";
            foreach ($memail as $v) {
                if (0 < $v['tip1']) {
                    addInnerMsg($v['id'], "您刚刚修改了登录密码", $innerbody);
                }
                if (0 < $v['tip2']) {
                    $to = empty($to) ? $v['user_email'] : $to . "," . $v['user_email'];
                }
            }
            break;
        case "chk2" :
            $to = "";
            $subject = "您刚刚在" . $datag['web_name'] . "修改了提现的银行帐户";
            $body = "您刚刚在" . $datag['web_name'] . "修改了提现的银行帐户,如不是自己操作,请尽快联系客服";
            $innerbody = "您刚刚修改了提现的银行帐户,如不是自己操作,请尽快联系客服";
            foreach ($memail as $v) {
                if (0 < $v['tip1']) {
                    addInnerMsg($v['id'], "您刚刚修改了提现的银行帐户", $innerbody);
                }
                if (0 < $v['tip2']) {
                    $to = empty($to) ? $v['user_email'] : $to . "," . $v['user_email'];
                }
            }
            break;
        case "chk6" :
            $to = "";
            $subject = "您刚刚在" . $datag['web_name'] . "申请了提现操作";
            $body = "您刚刚在" . $datag['web_name'] . "申请了提现操作,如不是自己操作,请尽快联系客服";
            $innerbody = "您刚刚申请了提现操作,如不是自己操作,请尽快联系客服";
            foreach ($memail as $v) {
                if (0 < $v['tip1']) {
                    addInnerMsg($v['id'], "您刚刚申请了提现操作", $innerbody);
                }
                if (0 < $v['tip2']) {
                    $to = empty($to) ? $v['user_email'] : $to . "," . $v['user_email'];
                }
            }
            break;
        case "chk7" :
            $to = "";
            $subject = "您在" . $datag['web_name'] . "发布的众筹项目刚刚初审未通过";
            $body = "您在" . $datag['web_name'] . "发布的第{$info}号众筹项目刚刚初审未通过";
            $innerbody = "您发布的第{$info}号众筹项目刚刚初审未通过";
            foreach ($memail as $v) {
                if (0 < $v['tip1']) {
                    addInnerMsg($v['id'], "刚刚您的众筹项目初审未通过", $innerbody);
                }
                if (0 < $v['tip2']) {
                    $to = empty($to) ? $v['user_email'] : $to . "," . $v['user_email'];
                }
            }
            break;
        case "chk8" :
            $to = "";
            $subject = "您在" . $datag['web_name'] . "发布的众筹项目刚刚初审通过";
            $body = "您在" . $datag['web_name'] . "发布的第{$info}号众筹项目刚刚初审通过";
            $innerbody = "您发布的第{$info}号众筹项目刚刚初审通过";
            foreach ($memail as $v) {
                if (0 < $v['tip1']) {
                    addInnerMsg($v['id'], "刚刚您的众筹项目初审通过", $innerbody);
                }
                if (0 < $v['tip2']) {
                    $to = empty($to) ? $v['user_email'] : $to . "," . $v['user_email'];
                }
            }
            break;
        case "chk9" :
            $to = "";
            $subject = "您在" . $datag['web_name'] . "发布的众筹项目刚刚复审通过";
            $body = "您在" . $datag['web_name'] . "发布的第{$info}号众筹项目刚刚复审通过";
            $innerbody = "您发布的第{$info}号众筹项目刚刚复审通过";
            foreach ($memail as $v) {
                if (0 < $v['tip1']) {
                    addInnerMsg($v['id'], "刚刚您的众筹项目复审通过", $innerbody);
                }
                if (0 < $v['tip2']) {
                    $to = empty($to) ? $v['user_email'] : $to . "," . $v['user_email'];
                }
            }
            break;
        case "chk12" :
            $to = "";
            $subject = "您在" . $datag['web_name'] . "的发布的众筹项目刚刚复审未通过";
            $body = "您在" . $datag['web_name'] . "的发布的第{$info}号众筹项目复审未通过";
            $innerbody = "您发布的第{$info}号众筹项目复审未通过";
            foreach ($memail as $v) {
                if (0 < $v['tip1']) {
                    addInnerMsg($v['id'], "刚刚您的众筹项目复审未通过", $innerbody);
                }
                if (0 < $v['tip2']) {
                    $to = empty($to) ? $v['user_email'] : $to . "," . $v['user_email'];
                }
            }
            break;
        case "chk10" :
            $to = "";
            $subject = "您在" . $datag['web_name'] . "的众筹项目已满标";
            $body = "刚刚您在" . $datag['web_name'] . "的第{$info}号众筹项目已满标，请登录查看";
            $innerbody = "刚刚您的众筹项目已满标";
            foreach ($memail as $v) {
                if (0 < $v['tip1']) {
                    addInnerMsg($v['id'], "刚刚您的第{$info}号众筹项目已满标", $innerbody);
                }
                if (0 < $v['tip2']) {
                    $to = empty($to) ? $v['user_email'] : $to . "," . $v['user_email'];
                }
            }
            break;
        case "chk11" :
            $to = "";
            $subject = "您在" . $datag['web_name'] . "的众筹项目已流标";
            $body = "您在" . $datag['web_name'] . "发布的第{$info}号众筹项目已流标，请登录查看";
            $innerbody = "您的第{$info}号众筹项目已流标";
            foreach ($memail as $v) {
                if (0 < $v['tip1']) {
                    addInnerMsg($v['id'], "刚刚您的众筹项目已流标", $innerbody);
                }
                if (0 < $v['tip2']) {
                    $to = empty($to) ? $v['user_email'] : $to . "," . $v['user_email'];
                }
            }
            break;
        case "chk25" :
            $to = "";
            $subject = "您在" . $datag['web_name'] . "的借入的还款进行了还款操作";
            $body = "您对在" . $datag['web_name'] . "借入的第{$info}号项目进行了还款，请登录查看";
            $innerbody = "您对借入的第{$info}号项目进行了还款";
            foreach ($memail as $v) {
                if (0 < $v['tip1']) {
                    addInnerMsg($v['id'], "您对借入标还款进行了还款操作", $innerbody);
                }
                if (0 < $v['tip2']) {
                    $to = empty($to) ? $v['user_email'] : $to . "," . $v['user_email'];
                }
            }
            break;
        case "chk27" :
            $to = "";
            $subject = "您在" . $datag['web_name'] . "设置的自动投标按设置投了新标";
            $body = "您在" . $datag['web_name'] . "设置的自动投标按设置对第{$info}号项目进行了投标，请登录查看";
            $innerbody = "您设置的自动投标对第{$info}号项目进行了投标";
            foreach ($memail as $v) {
                if (0 < $v['tip1']) {
                    addInnerMsg($v['id'], "您设置的自动投标按设置投了新标", $innerbody);
                }
                if (0 < $v['tip2']) {
                    $to = empty($to) ? $v['user_email'] : $to . "," . $v['user_email'];
                }
            }
            break;
        case "chk14" :
            $to = "";
            $subject = "您在" . $datag['web_name'] . "投标的项目流标了";
            $body = "您在" . $datag['web_name'] . "投标的第{$info}号项目借出成功了";
            $innerbody = "您投标的项目成功了";
            foreach ($memail as $v) {
                if (0 < $v['tip1']) {
                    addInnerMsg($v['id'], "您投标的第{$info}号项目项目成功", $innerbody);
                }
                if (0 < $v['tip2']) {
                    $to = empty($to) ? $v['user_email'] : $to . "," . $v['user_email'];
                }
            }
            break;
        case "chk15" :
            $to = "";
            $subject = "您在" . $datag['web_name'] . "投标的项目流标了";
            $body = "您在" . $datag['web_name'] . "投标的第{$info}号项目流标了，相关资金已经返回帐户，请登录查看";
            $innerbody = "您投标的项目流标了";
            foreach ($memail as $v) {
                if (0 < $v['tip1']) {
                    addInnerMsg($v['id'], "您投标的第{$info}号项目流标了，相关资金已经返回帐户", $innerbody);
                }
                if (0 < $v['tip2']) {
                    $to = empty($to) ? $v['user_email'] : $to . "," . $v['user_email'];
                }
            }
            break;
        case "chk16" :
            $to = "";
            $datag = get_global_setting();
    $datag = de_xie($datag);
    $msgconfig = fs("Webconfig/msgconfig"); 
    $smsTxt = fs("Webconfig/smstxt");
    $msgTxt = fs("Webconfig/msgtxt"); 
    $msgTxt = de_xie($msgTxt); 
            
            $subject = "您在" . $datag['web_name'] . "借出的项目收到了新的还款";
            $body = "您在" . $datag['web_name'] . "借出的第{$info['id']}号项目收到了新的还款，请登录查看";
            $innerbody = "您借出的项目收到了新的还款";
            foreach ($memail as $v) {
                if (0 < $v['tip1']) {

        				//还款到帐
//        			$content = str_replace(array(
//                        "#BORROW_NAME#",
//                        "#UserName#"
//                    ), array(
//                        $info['BORROW_NAME'],
//                        $uname
//                    ), $smsTxt["payback"]["content"]);
//
//        			  //var_dump($v["user_phone"],$content);die;
//                    $sendp = sendsms20181216($v["user_phone"],$content);
//                        // var_dump($sendp,$v["user_phone"]);die;
                    if($typed=='1'){
                        $content = str_replace(array(
                                  "#xiangmu#",
                                  "#qi#",
                                  "#shijian#",
                                  "#qian#"
                              ),array(
                                $info['xiangmu'],
                                $info['qi'],
                                $info['shijian'],
                                $info['qian']
                              ),$smsTxt["alhuibao"]["content"]);

                        $phone=$v["user_phone"];
                        $datas=array("xiangmu"=>$info['xiangmu'],"qi"=>$info['qi'],"shijian"=>$info['shijian'],"qian"=>$info['qian']);
                        $moban="SMS_181859072";
                        $sendp = sendsmsaly($phone,$datas,$moban,$content);

                        // $content = str_replace(array(
                        //     "#BORROW_NAME#",
                        //     "#UserName#"
                        // ), array(
                        //     $data["BORROW_NAME"],
                        //     $uname
                        // ), $smsTxt["payback"]["content"]);
                        //     // var_dump($minfo,$content);die;
                        // //$sendp = sendsms20181216($minfo["user_phone"],$content);
                        
                        // $phone=$v["user_phone"];
                        // $datas=array("BORROW_NAME"=>$info["xiangmu"]);
                        // $moban="SMS_181856254";
                        // $sendp = sendsmsaly($phone,$datas,$moban,$content);
                        // return $sendp;

                    }else{
                        $content = str_replace(array(
                                  "#xiangmu#",
                                  "#qi#",
                                  "#shijian#",
                                  "#qian#"
                              ),array(
                                $info['xiangmu'],
                                $info['qi'],
                                $info['shijian'],
                                $info['qian']
                              ),$smsTxt["huibao"]);
                              $sendp = sendsms20181216($v["user_phone"],$content);
                    }
                     


        
                   


                     
                    addInnerMsg($v['id'], "您借出的".$info['xiangmu']."项目收到了新的还款", $innerbody);
                }
                if (0 < $v['tip2']) {
                    $to = empty($to) ? $v['user_email'] : $to . "," . $v['user_email'];
                }
            }
            break;
        case "chk18" :
            $to = "";
            $subject = "您在" . $datag['web_name'] . "借出的项目逾期网站代还了本金";
            $body = "您在" . $datag['web_name'] . "借出的第{$info}号项目逾期网站代还了本金，请登录查看";
            $innerbody = "您借出的第{$info}号项目逾期网站代还了本金";
            foreach ($memail as $v) {
                if (0 < $v['tip1']) {
                    addInnerMsg($v['id'], "您借出的第{$info}号项目逾期网站代还了本金", $innerbody);
                }
                if (0 < $v['tip2']) {
                    $to = empty($to) ? $v['user_email'] : $to . "," . $v['user_email'];
                }
            }
            break;
    }
    return $send;
}
function canInvestMoney($uid, $borrow_id, $money, $_is_auto = 0, $is_experience = 0, $member_interest_rate_id = 0, $bonus_id = 0, $borrow_pass = '')
{  
    $money_type      = "1";
    $rs['status']    = "0";
    $rs['tips_type'] = "3";
    //检查该标的状态
    $binfo         = M("borrow_info")->field("borrow_uid,borrow_money,has_vouch,borrow_max,borrow_min,borrow_name,borrow_interest_rate,borrow_type,borrow_duration,borrow_duration,borrow_status,repayment_type,has_borrow,pause,is_bonus,is_bonus_invest_min,is_experience")->find($borrow_id);
  
    $minfo         = getMinfo($uid, 'm.user_name,m.pin_pass,mm.account_money,mm.back_money,mm.money_experience,mm.yubi');
    $uname         = $minfo['user_name'];
    $account_money = $minfo['account_money']+$minfo['yubi'];
    
    //是否为投资中的标
    /*if (@$binfo["borrow_status"] != 2) {
        $rs['tips'] = "该项目还没有审核，请等待开启后进行投标！";
        return $rs;
    }   */ 
    
    //红包和体验金是否同时使用
    if (!empty($bonus_id) && !empty($is_experience)) {
        $rs['tips'] = '红包和体验金不可同时使用！';
        return $rs;
    }
    //体验金是否可用
    if ($is_experience == 1 && $binfo['is_experience'] != 1) {
        $rs['tips'] = '该项目不可使用体验金！';
        return $rs;
    }
    if ($is_experience != 1 && $binfo['is_experience'] == 1) {
        $rs['tips'] = '该项目必须使用体验金！';
        return $rs;
    }
    if ($is_experience == 1 && $binfo['is_bonus'] == 1) {
        $rs['tips'] = '该项目不可使用体验金！';
        return $rs;
    }
    if ($is_experience == 1) {
        $money_type = 2;
    }
    //红包是否可用
    // $bonus_id = !empty($_POST['bonus_id']) ? intval($_POST['bonus_id']) : '0';
    if (!empty($bonus_id)) {
        if ($binfo['is_bonus'] != 1) {
            $rs['tips'] = '该项目不可使用红包！';
            return $rs;
        }
        if ($binfo['is_experience'] == 1) {
            $rs['tips'] = '该项目不可使用红包！';
            return $rs;
        }
        $bonusInfo = canUseBouns($bonus_id, $uid, $binfo);
        if (isset($bonusInfo['money_bonus']) && $bonusInfo['money_bonus'] > 0) {
            $money_bonus      = $bonusInfo['money_bonus'];
            $money_type       = 3;
            $bonus_invest_min = $bonusInfo['bonus_invest_min'] > $binfo['is_bonus_invest_min'] ? $bonusInfo['bonus_invest_min'] : $binfo['is_bonus_invest_min'];
            if ($money < $bonus_invest_min) {
                $rs['tips'] = '最低投资' . $bonus_invest_min . '元，才能使用红包！';
                return $rs;
            }
        } else {
            $rs['tips'] = '红包不可用，请选择其他红包！';
            return $rs;
        }
    }

//  if ($money < $binfo['borrow_min']) {
//      $rs['tips'] = "该项目最小投标金额为" . $binfo['borrow_min'] . "元";
//      return $rs;
//  }
    if ($money > $binfo['borrow_max'] and $binfo['borrow_max'] != 0) {
        $rs['tips'] = "该项目最大投标金额为" . $binfo['borrow_max'] . "元";
        return $rs;
    }
    switch ($money_type) {
        case '1':
            if ($money > $account_money) {
                $rs['tips']      = "账户鱼币+余额不足！";
                $rs['tips_type'] = 2;
                return $rs;
            }
            break;
        case '2':
            if ($money > $minfo['money_experience']) {
                $rs['tips'] = "体验金账户余额不足，请使用账户余额重新投该项目。";
                return $rs;
            }
            break;
        case '3':
            // if (bcsub($money, $money_bonus, 2) < 0) {
            //     $rs['tips'] = "投资金额不可低于红包金额！";
            //     return $rs;
            // } else
            if (bcsub($money, $money_bonus, 2) > $account_money) {
                $rs['tips']      = "账户鱼币+余额不足！";
                $rs['tips_type'] = 2;
                return $rs;
            }
            break;
        default:
            # code...
            break;
    }

    $rs['tips']        = "";
    $rs['status']      = "1";
    $rs['money']       = $money;
    $rs['money_bonus'] = isset($money_bonus) ? $money_bonus : 0;
    $rs['money_type']  = $money_type;
    return $rs;
}

function investMoney($uid, $borrow_id, $money, $_is_auto = 0, $from = 1,$is_experience = 0, $member_interest_rate_id = 0, $bonus_id = 0, $money_bonus = 0, $borrow_pass = '',$zt=0,$sg=0,$xsinvestor_capital=0)
{
    $pre = c("DB_PREFIX");
    $done = false;
    $datag = get_global_setting();
    $investMoney = d("borrow_investor");
    $investMoney->startTrans();
    $binfo = m("borrow_info")->lock(true)->field("borrow_uid,borrow_money,borrow_name,borrow_interest_rate,borrow_type,borrow_duration,borrow_duration,borrow_status,repayment_type,has_borrow,sghas_borrow,bespeak_able,start_time,bespeak_days")->find($borrow_id);
    if($binfo)
    {
    $vminfo = getminfo($uid, "m.user_leve,m.time_limit,mm.account_money,mm.back_money,mm.yubi");
    $canInvestMoney = canInvestMoney($uid, $borrow_id, $money, $_is_auto, $is_experience, $member_interest_rate_id, $bonus_id, $borrow_pass);
    if ($canInvestMoney['status'] == 0) {
        $investMoney->rollback();        
        return $canInvestMoney['tips'];
    }

    if($binfo["borrow_status"]!=2){
        $investMoney->rollback();
         return "当前项目还没有审核，请等待开启后进行投资";  
    }
    if ($money<1) {
        $investMoney->rollback();
        return "最低投标金额为1元";
    }
    // if ($vminfo['account_money'] < $money ) {
    //     $investMoney->rollback();
    //     return "对不起，可用余额不足，不能投资该项目";
    // }
    
    $borrow_name=$binfo["borrow_name"];
    $minfo =getMinfo($uid,true);
    $levename=getLeveName($minfo["credits"]);
    $levetixian=getLeveTixian($minfo["credits"]);
    //对应等级的 利息管理费  资海
    if($binfo["borrow_type"]==3)$levetixian=0;
    $fee_rate = $levetixian / 100;
    
    $havemoney = $binfo['has_borrow'];
    $sghas_borrow = $binfo['sghas_borrow'];
    $has=bcsub($binfo['borrow_money'], $havemoney,2);
    $hasb=bcsub($has, $money,2);
    if ($hasb < 0) {
        $investMoney->rollback();
        return "对不起，此项目还差" . (bcsub($binfo['borrow_money'] , $havemoney,2)) . "元，您最多投资" . (bcsub($binfo['borrow_money'] , $havemoney,2)) . "元";
    }
    
    $investinfo['status'] = 1;
    $investinfo['borrow_id']    = $borrow_id;
    $investinfo['investor_uid'] = $uid;
    $investinfo['borrow_uid']   = $binfo['borrow_uid'];
    $investinfo['investor_capital'] = $money;
    $investinfo['is_auto']  = $_is_auto;
    $investinfo['add_time'] = time();
    $investinfo['investor_interest'] = '0.00';
    $investinfo['invest_fee'] = getfloatvalue($fee_rate * $investinfo['investor_interest'], 2);
    $investinfo['investor_way'] = $from;

    //自提分数
    $investinfo['fenshu']    = $zt+$sg;
    $investinfo['ztfenshu']    = $zt;
    $investinfo['xsinvestor_capital']=$xsinvestor_capital;

    $investinfo['member_interest_rate_id']    = $member_interest_rate_id;
    if($member_interest_rate_id != 0 ){
        M('member_interest_rate')->save(array('id' => $member_interest_rate_id, 'status' => 2, 'use_time' => time()));
    }
    $investinfo['is_experience']    = $is_experience;
    //红包
    $investinfo['bonus_id'] = $bonus_id;
    if (!empty($bonus_id)) {
        $rs = M('member_bonus')->save(array('id' => $bonus_id, 'status' => 2, 'use_time' => time()));
        if (!$rs) {
            $investMoney->rollback();
            return "系统繁忙，请重试！";
        }
    }
    
    //红包鱼币计算  
    if($money_bonus){
        $syyb=$vminfo["yubi"]+$money_bonus;
    }else{
        $syyb=$vminfo["yubi"];
    }
    if($syyb>$money){
        $investinfo['yubi'] =$money-$money_bonus;
    }else{
        $investinfo['yubi'] = $vminfo["yubi"];
    }


    $invest_info_id = m("borrow_investor")->add($investinfo);    
    
    $capital = M('borrow_investor')->where("borrow_id={$borrow_id}")->sum('investor_capital');
    
    if($capital > $binfo["borrow_money"]){
        $investMoney->rollback();
        return "投资金额有误";
    }
    
    if ($invest_info_id) {
        $is_bespeak = false;
        //体验金
        if ($is_experience) {
            $res = memberMoneyLog($uid, 102, 0 - $money, "对{$borrow_name}进行投标", $binfo['borrow_uid'], "", false);
            //红包
        } elseif ($bonus_id) {
            //使用鱼币部分
            if($investinfo['yubi']>0){
                $res = memberMoneyLog($uid, 6, 0 - $investinfo['yubi'], "对（{$borrow_name}）进行投标扣除鱼币", $binfo['borrow_uid'], "", false);
                if($syyb<$money){
                    $res = memberMoneyLog($uid, 6, 0 - (bcsub($money,$syyb,2)), "对（{$borrow_name}）进行投标扣除余额", $binfo['borrow_uid'], "", false);
                }
            }else{
                //使用余额部分
                $res = memberMoneyLog($uid, 6, 0 - (bcsub($money, $money_bonus, 2)), "对（{$borrow_name}）进行投标扣除余额", $binfo['borrow_uid'], "", false);
            }
            // //使用余额部分
            // $res = memberMoneyLog($uid, 6, 0 - (bcsub($money, $money_bonus, 2)), "对（{$borrow_name}）进行投标", $binfo['borrow_uid'], "", false);

            //使用红包部分
            $res and $res = memberMoneyLog($uid, 110, 0 - $money_bonus, "对（{$borrow_name}）进行投标", $binfo['borrow_uid'], "", false);
        } else {
            writeLog($investinfo);
            $bespeak = M('bespeak')->field("bespeak_point")->where("borrow_id={$borrow_id} AND bespeak_uid={$uid} AND bespeak_status=0")->find();
            if($binfo['bespeak_able'] == 1 && $binfo['start_time'] + $binfo['bespeak_days']*60*60*24 > time() && $bespeak){
                $is_bespeak = true;
                m("bespeak")->where("borrow_id={$borrow_id} and bespeak_uid={$uid} AND bespeak_status=0")->save(["bespeak_status"=>1]);
                $res = memberMoneyLog($uid, 6, 0 - $money*(1-$bespeak["bespeak_point"]), "对（{$borrow_name}）进行投标（预约）", $binfo['borrow_uid'], "", false);
            } else {

                if($investinfo['yubi']>0){
                    $res = memberMoneyLog($uid, 6, 0 - $investinfo['yubi'], "对（{$borrow_name}）进行投标扣除鱼币", $binfo['borrow_uid'], "", false);
                    if($syyb<$money){
                        $res = memberMoneyLog($uid, 6,  0 - (bcsub($money,$syyb,2)), "对（{$borrow_name}）进行投标扣除余额", $binfo['borrow_uid'], "", false);
                    }
                }else{
                    //使用余额部分
                    $res = memberMoneyLog($uid, 6, 0 - $money, "对（{$borrow_name}）进行投标扣除余额", $binfo['borrow_uid'], "", false);
                }


                //$res = memberMoneyLog($uid, 6, 0 - $money, "对（{$borrow_name}）进行投标", $binfo['borrow_uid'], "", false);
            }
        }

        $upborrowsql = "update `{$pre}borrow_info` set ";
        $upborrowsql .= "`has_borrow`=" . ($havemoney + $money) . ",`sghas_borrow`=" . ($sghas_borrow+$xsinvestor_capital) . ",`borrow_times`=`borrow_times`+1";
        $upborrowsql .= " WHERE `id`={$borrow_id}";
        $upborrow_res = m()->execute($upborrowsql);
        
        if ($havemoney + $money == $binfo['borrow_money']) {
            borrowfull($borrow_id, $binfo['borrow_type']);
        }
        
        if (!$res) {
            $investMoney->rollback();            
            m("borrow_investor")->where("id={$invest_info_id}")->delete();
            if(isset($is_bespeak) && $is_bespeak){
                m("bespeak")->where("borrow_id={$borrow_id} and bespeak_uid={$uid}")->save(["bespeak_status"=>0]);
            }
            $upborrowsql = "update `{$pre}borrow_info` set ";
            $upborrowsql .= "`has_borrow`=" . $havemoney . ",`sghas_borrow`=" . $sghas_borrow . ",`borrow_times`=`borrow_times`-1";
            $upborrowsql .= " WHERE `id`={$borrow_id}";            
            $upborrow_res = m()->execute($upborrowsql);
            $done = false;
            return "投资金额有误";
        }        
        $binfo = m("borrow_info")->field("zpid,is_huodong,huodongnum,borrow_uid,borrow_money,borrow_name,borrow_interest_rate,borrow_type,borrow_duration,repayment_type,has_borrow")->find($borrow_id);

        $capital = M('borrow_investor')->where("borrow_id={$borrow_id}")->sum('investor_capital');
        if($capital > $binfo['borrow_money']){
            $done = false;
            $investMoney->rollback();
            return $capital."投资金额有误";
        }else{
            $done = true;
			//paystatus
            if($borrow_id==1){
				    $experience = M('member_experience')->where("uid={$uid}"." and paystatus =0")->find();
				    if(isset($experience) && !empty($experience)) {
                        M('member_experience')->where("id = {$experience['id']}")->save(["paystatus" => 1, 'update_time' => time()]);
                    }
			}
			//发放赠品
            if($binfo["is_huodong"]=='1'){
                $yzp=M("member_zengpin")->where(array("borrow_id"=>$borrow_id,"uid"=>$uid))->find();
                $ytmoney=M('borrow_investor')->where(array("borrow_id"=>$borrow_id,"investor_uid"=>$uid))->sum('investor_capital');
                $zpfs=floor($ytmoney/$binfo["huodongnum"]);
                if(!empty($yzp)){
                    $xnum=$zpfs-($yzp['allnum']-$yzp['num']);
                    gxzengpin($yzp["id"],$borrow_id,$zpfs,$xnum);
                }else{
                    if($zpfs>0){
                        ffzengpin($uid,$invest_info_id,$zpfs,$binfo['zpid'],$borrow_id);
                    }
                }
            }
            $investMoney->commit();
            // 微信消息
        }
    } else {
        $investMoney->rollback();
    }
 
    return $done;
    }else{
        return "系统繁忙请重试";   
    }
}
function  ffzengpin($uid,$investor_id,$zpfs,$zpid,$borrow_id){
    $data['uid']=$uid;
    $data['investor_id']=$investor_id;
    $data['num']=$zpfs;
    $data['allnum']=$zpfs;
    $data['zpid']=$zpid;
    $data['add_time']=time();
    $data['borrow_id']=$borrow_id;
    $data['type']='1';
    M("member_zengpin")->add($data);
}
function  gxzengpin($id,$borrow_id,$zpfs,$xnum){
    $map["borrow_id"]=$borrow_id;
    $map["id"]=$id;
    M()->startTrans();
    m('member_zengpin')->lock(true)->where($map)->find();
    $data["allnum"]=$zpfs;
    $data["num"]=$xnum;
    M("member_zengpin")->where($map)->save($data);
    M()->commit();
}

function  yffzengpin($uid,$investor_id,$zpfs,$zpid,$borrow_id){
    $data['uid']=$uid;
    $data['investor_id']=$investor_id;
    $data['num']=$zpfs;
    $data['allnum']=$zpfs;
    $data['zpid']=$zpid;
    $data['add_time']=time();
    $data['borrow_id']=$borrow_id;
    $data['type']='2';
    $res=M("member_zengpin")->add($data);
    //var_dump(M("member_zengpin")->getLastSql());
    return $res;
}
function  ygxzengpin($id,$borrow_id,$zpfs,$xnum){
    $map["borrow_id"]=$borrow_id;
    $map["id"]=$id;
    m('member_zengpin')->where($map)->find();
    $data["allnum"]=$zpfs;
    $data["num"]=$xnum;
    return M("member_zengpin")->where($map)->save($data);
}


function zinvestMoney($uid, $borrow_id, $money,$yubi,$_is_auto = 0, $from = 1,$is_experience = 0, $member_interest_rate_id = 0, $bonus_id = 0, $money_bonus = 0, $borrow_pass = '')
{
    $pre = c("DB_PREFIX");
    $done = false;
    $datag = get_global_setting();
    $investMoney = d("borrow_investor");
    $investMoney->startTrans();
    $binfo = m("borrow_info")->lock(true)->field("borrow_uid,borrow_money,borrow_min,borrow_name,borrow_interest_rate,borrow_type,borrow_duration,borrow_duration,borrow_status,repayment_type,has_borrow,sghas_borrow")->find($borrow_id);
    if($binfo)
    {
        $vminfo = getminfo($uid, "m.user_leve,m.time_limit,mm.account_money,mm.back_money,mm.yubi");

        if ($money<1) {
            $investMoney->rollback();
            return "最低投标金额为1元";
        }
        $borrow_name=$binfo["borrow_name"];
        $minfo =getMinfo($uid,true);
        $levename=getLeveName($minfo["credits"]);
        $levetixian=getLeveTixian($minfo["credits"]);
        //对应等级的 利息管理费  资海
        if($binfo["borrow_type"]==3)$levetixian=0;
        $fee_rate = $levetixian / 100;

        $havemoney = $binfo['has_borrow'];
        $sghas_borrow = $binfo['sghas_borrow'];
        $has=bcsub($binfo['borrow_money'], $havemoney,2);
        $hasb=bcsub($has, $money,2);
        if ($hasb < 0) {
            $investMoney->rollback();
            return "对不起，此项目还差" . (bcsub($binfo['borrow_money'] , $havemoney,2)) . "元，您最多投资" . (bcsub($binfo['borrow_money'] , $havemoney,2)) . "元";
        }

        $investinfo['status'] = 1;
        $investinfo['borrow_id']    = $borrow_id;
        $investinfo['investor_uid'] = $uid;
        $investinfo['borrow_uid']   = $binfo['borrow_uid'];
        $investinfo['investor_capital'] = $money;
        $investinfo['is_auto']  = $_is_auto;
        $investinfo['add_time'] = time();
        $investinfo['investor_interest'] = '0.00';
        $investinfo['invest_fee'] = getfloatvalue($fee_rate * $investinfo['investor_interest'], 2);
        $investinfo['investor_way'] = $from;

        $investinfo['member_interest_rate_id']    = $member_interest_rate_id;
        if($member_interest_rate_id != 0 ){
            M('member_interest_rate')->save(array('id' => $member_interest_rate_id, 'status' => 2, 'use_time' => time()));
        }
        $investinfo['is_experience']    = $is_experience;
        //红包
        $investinfo['bonus_id'] = $bonus_id;

        $investinfo['yubi'] = $yubi;

        $fenshu=$money/$binfo["borrow_min"];
        $investinfo['fenshu']    = $fenshu;
        $investinfo['ztfenshu']    = 0;
        $investinfo['xsinvestor_capital'] = $money;
        writeLog($investinfo);writeLog('-xxxxxxxxx');
        $invest_info_id = m("borrow_investor")->add($investinfo);

        $capital = M('borrow_investor')->where("borrow_id={$borrow_id}")->sum('investor_capital');

        if($capital > $binfo["borrow_money"]){
            $investMoney->rollback();
            return "投资金额有误";
        }

        if ($invest_info_id) {

            $upborrowsql = "update `{$pre}borrow_info` set ";
            //$upborrowsql .= "`has_borrow`=" . ($havemoney + $money) . ",`borrow_times`=`borrow_times`+1";
            $upborrowsql .= "`has_borrow`=" . ($havemoney + $money) . ",`sghas_borrow`=" . ($sghas_borrow+$money) . ",`borrow_times`=`borrow_times`+1";
            $upborrowsql .= " WHERE `id`={$borrow_id}";
            $upborrow_res = m()->execute($upborrowsql);

            if ($havemoney + $money == $binfo['borrow_money']) {
                borrowfull($borrow_id, $binfo['borrow_type']);
            }

            $binfo = m("borrow_info")->field("zpid,is_huodong,huodongnum,borrow_uid,borrow_money,borrow_name,borrow_interest_rate,borrow_type,borrow_duration,repayment_type,has_borrow")->find($borrow_id);
            $capital = M('borrow_investor')->where("borrow_id={$borrow_id}")->sum('investor_capital');
            if($capital > $binfo['borrow_money']){
                $done = false;
                $investMoney->rollback();
                return "投资金额有误";
            }else{
                $done = true;
                //发放赠品
                if($binfo["is_huodong"]=='1'){
                    $yzp=M("member_zengpin")->where(array("borrow_id"=>$borrow_id,"uid"=>$uid))->find();
                    $ytmoney=M('borrow_investor')->where(array("borrow_id"=>$borrow_id,"investor_uid"=>$uid))->sum('investor_capital');
                    $zpfs=floor($ytmoney/$binfo["huodongnum"]);
                    if(!empty($yzp)){
                        $xnum=$zpfs-($yzp['allnum']-$yzp['num']);
                        gxzengpin($yzp["id"],$borrow_id,$zpfs,$xnum);
                    }else{
                        if($zpfs>0){
                            ffzengpin($uid,$invest_info_id,$zpfs,$binfo['zpid'],$borrow_id);
                        }
                    }
                }
                $investMoney->commit();
                // 微信消息
            }
        } else {
            $investMoney->rollback();
        }

        return $done;
    }else{
        return "系统繁忙请重试";
    }
}
function bespeakMoney($uid, $borrow_id, $money)
{
    $datag = get_global_setting();
    $done = false;
    $bespeakMoney = d("bespeak") ;
    $bespeakMoney->startTrans();
    $binfo = m("borrow_info")->lock(true)->field("borrow_uid,bespeak_money,borrow_name")->find($borrow_id);
    if($binfo)
    {
        $bespeak['borrow_id']    = $borrow_id;
        $bespeak['bespeak_uid'] = $uid;
        $bespeak['bespeak_money'] = $money;
        $bespeak['bespeak_point'] = $datag["bespeak_point"];
        $bespeak['bespeak_status'] = 0;
        $bespeak['add_time'] = time();

        $bespeak_id = m("bespeak")->add($bespeak);

        if ($bespeak_id) {
            writeLog($bespeak);
            $res = memberMoneyLog($uid, 6, 0 - $money*$bespeak["bespeak_point"], "对（{$binfo['borrow_name']}）进行预约", $binfo['borrow_uid'], "", false);
            $binfo = m("borrow_info")->where("id={$borrow_id}")->save(["bespeak_money"=>$binfo["bespeak_money"]+$money]);
            if(!$binfo){
                $bespeakMoney->rollback();
                m("$bespeak")->where("id={$bespeak_id}")->delete();
                return "更新投标金额时出错";
            }
            if (!$res) {
                $bespeakMoney->rollback();
                m("borrow_info")->where("id={$borrow_id}")->save(["bespeak_money"=>$binfo["bespeak_money"]-$money]);
                m("bespeak")->where("id={$bespeak_id}")->delete();
                return "保存资金日志时出错";
            }
            $done = true;
            $bespeakMoney->commit();
        } else {
            $bespeakMoney->rollback();
            return "保存预约信息时出错";
        }
        return $done;
    }else{
        return "系统繁忙请重试";
    }
}
function back_money($uid){
    $datag = get_global_setting();
    $times=$datag["back_money_time"];
    $timemes=time()-24*3600*$times;//N天前的时间
    
    $m1=M('investor_detail i')->join("lzh_borrow_info b on b.id=i.borrow_id")->where("i.repayment_time<>'' and i.investor_uid={$uid} and i.repayment_time>{$timemes}")->sum('i.capital');  //回款本金
    
    
    $m2=M('investor_detail i')->join("lzh_borrow_info b on b.id=i.borrow_id")->where("i.repayment_time<>'' and i.investor_uid={$uid} and i.repayment_time>{$timemes}")->sum('i.interest');  //回款利息
    
    $m3=M('members')->field("i_back_money")->where("id={$uid}")->find();
    //echo M('members')->getlastsql();
    return $m1+$m2-$m3["i_back_money"]; //回款金额  
}
function borrowFull($borrow_id, $btype = 0)
{
    
   //if ($btype == 3) {
   //borrowApproved($borrow_id);
   //borrowrepayment($borrow_id, 1);
   //} else {
    // M()->startTrans();
    // $binfo=m("borrow_info")->where("id={$borrow_id}")->find();
    // if($binfo["borrow_status"]==2){
    //     if(!empty($binfo["hkday"])){
    //          $saveborrow['lead_time'] =strtotime('+'.$binfo["hkday"].' day');
    //     }else{
    //          $month=$binfo["borrow_duration"]/$binfo["total"];
    //          $saveborrow['lead_time'] =strtotime('+'.$month.' month +1 day');
    //     }
    //     $saveborrow['pause'] = 0;
    //     $saveborrow['borrow_status'] = 6; 
    //     $saveborrow['full_time'] = time();
    //     $saveborrow['second_verify_time'] =time();
    //     $upborrow_res = m("borrow_info")->where("id={$borrow_id} and borrow_status=2")->save($saveborrow);

    //     if($upborrow_res){
    //         M()->commit();
    //         borrow_overds($binfo);
    //     }else{
    //         M()->rollback();
    //     }
    // }
        $saveborrow['pause'] = 0;
        $saveborrow['borrow_status'] = 4;
        $saveborrow['full_time'] = time();
        $upborrow_res = m("borrow_info")->where("id={$borrow_id}")->save($saveborrow);
   //}
    $pre = C('DB_PREFIX');
    $binfo = m("borrow_info")->field("borrow_name,borrow_type,borrow_interest_rate,borrow_duration,borrow_money")->find($borrow_id);
    $membersList = M('borrow_investor bi')->join($pre."members m on bi.investor_uid = m.id")->field("m.user_phone")->where("bi.borrow_id = '{$borrow_id}' and m.user_phone!=''")->select();
    $datag = get_global_setting();
    $typed=$datag["dx_type"];
    //var_dump($membersList);
    if($typed==1){
        $tipPhoneArr = array();
        foreach ($membersList as $key => $value) {

                $content = "尊敬的用户，您好！您购买的“{$binfo['borrow_name']}”项目，现已完成待平台审核，请及时关注！";
                $phone=$value["user_phone"];
                $datas=array("borrow_name"=>$binfo['borrow_name']);
                $moban="SMS_181864133";
                $sendp = sendsmsaly($phone,$datas,$moban,$content);
                //var_dump($phone);
                //return $sendp;
            //$tipPhoneArr[] = $value['user_phone'];
        }
    }else{
        $tipPhoneArr = array();
        foreach ($membersList as $key => $value) {
            $tipPhoneArr[] = $value['user_phone'];
        }
        $tipPhoneStr = implode($tipPhoneArr, ',');    
        $content = "尊敬的用户，您好！您投资的“{$binfo['borrow_name']}”项目，现已筹满待平台审核，请及时关注！"; 
           
        sendsms($tipPhoneStr, $content);  
    }
    //var_dump("2");
}
function borrow_overds($v){
            $appid = borrowApproved($v["id"]);

            // if(!$appid){
            //     return false;
            // } 
            distribution_maid20190304($v["id"]);

            MTip('chk9',$v['borrow_uid'],$v['id']);


            $vss = M("members")->field("user_phone,user_name")->where("id = {$v['borrow_uid']}")->find();

            SMStip("approve",$vss['user_phone'],array("#USERANEM#","ID"),array($vss['user_name'],$v["id"]));


                    $data['typename'] = '产品上线';

                    $data['type'] = 1; 

                    $data['stars'] = 0;

                    $data['add_time'] = time();

                    $data['uid'] = '';

                    $data['uname'] = '管理员';

                    $data['tid'] = (int)($v["id"]);

                    $data['name'] = $v["borrow_name"];//M('borrow_info')->getFieldById($m->id, 'borrow_name');

                    $data['dycomment'] =$v["borrow_name"]. " 已认购成功， 感谢您的参与！";
                    // M('borrow_info')->getFieldById($m->id, 'borrow_name'). " 已认购成功， 感谢您的参与！";

                    $newid = M('dynamic')->add($data);

     

                $verify_info['borrow_id'] =$v["id"];

                $verify_info['deal_info_2'] = text($_POST['deal_info_2']);

                $verify_info['deal_user_2'] = 101;

                $verify_info['deal_time_2'] = time();

                $verify_info['deal_status_2'] = 6;

           
                M('borrow_verify')->save($verify_info);

}
function borrowRefuse($borrow_id, $type)
{
    $pre            = c("DB_PREFIX");
    $done           = false;
    $borrowInvestor = d("borrow_investor");
    $binfo          = m("borrow_info")->field("borrow_type,borrow_money,borrow_name,borrow_uid,borrow_duration,repayment_type")->find($borrow_id);
    $borrow_name    = $binfo["borrow_name"];

    //扣除冻结已失效预约金
    $bespeak = M("bespeak")->field(true)->where("borrow_id = {$borrow_id} and bespeak_status = 0")->select();
    if(is_array($bespeak)) {
        foreach ($bespeak as $v) {
            memberMoneyLog($v['bespeak_uid'], 223, 0 - $v['bespeak_money'] * $v['bespeak_point'], "{$borrow_name}募集失败，扣除冻结已失效预约金", $binfo['borrow_uid']);
        }
    }

    $investorList   = $borrowInvestor->field("id,investor_uid,investor_capital,is_experience")->where("borrow_id={$borrow_id}")->select();
    m("investor_detail")->where("borrow_id={$borrow_id}")->delete();
    if ($binfo['borrow_type'] == 1) {
        $limit_credit = memberlimitlog($binfo['borrow_uid'], 12, $binfo['borrow_money'], $info = "{$borrow_name}流标");
    }
    $borrowInvestor->startTrans();
    $bstatus       = $type == 2 ? 3 : 5;
    $upborrow_info = m("borrow_info")->where("id={$borrow_id}")->setField("borrow_status", $bstatus);
    $buname        = m("members")->getFieldById($binfo['borrow_uid'], "user_name");
    if (is_array($investorList)) {
        $upsummary_res = m("borrow_investor")->where("borrow_id={$borrow_id}")->setField("status", $type);
        foreach ($investorList as $v) {
            mtip("chk15", $v['investor_uid'], $borrow_id);
            $accountMoney_investor       = m("member_money")->field(true)->find($v['investor_uid']);
            $datamoney_x['uid']          = $v['investor_uid'];
            $datamoney_x['type']         = $type == 3 ? 16 : 8;
            $datamoney_x['affect_money'] = $v['investor_capital'];
            if ($v['is_experience']) {
                $datamoney_x['experience_money'] = $accountMoney_investor['money_experience'] + $datamoney_x['affect_money'];
                $datamoney_x['account_money']    = $accountMoney_investor['account_money'];
            } else {
                $datamoney_x['account_money']    = $accountMoney_investor['account_money'] + $datamoney_x['affect_money'];
                $datamoney_x['experience_money'] = $accountMoney_investor['money_experience'];
            }

            $datamoney_x['collect_money'] = $accountMoney_investor['money_collect'];
            $datamoney_x['freeze_money']  = $accountMoney_investor['money_freeze'] - $datamoney_x['affect_money'];
            $mmoney_x['money_freeze']     = $datamoney_x['freeze_money'];
            $mmoney_x['money_collect']    = $datamoney_x['collect_money'];

            $mmoney_x['account_money']    = $datamoney_x['account_money'];
            $mmoney_x['money_experience'] = $datamoney_x['experience_money'];

            $_xstr                       = $type == 3 ? "复审未通过" : "募集期内标未满，流标";
            $datamoney_x['info']         = "{$borrow_name}" . $_xstr . "，返回冻结资金";
            $datamoney_x['add_time']     = time();
            $datamoney_x['add_ip']       = get_client_ip();
            $datamoney_x['target_uid']   = $binfo['borrow_uid'];
            $datamoney_x['target_uname'] = $buname;
            $moneynewid_x                = m("member_moneylog")->add($datamoney_x);
            if ($moneynewid_x) {
                $bxid = m("member_money")->where("uid={$datamoney_x['uid']}")->save($mmoney_x);
            }
        }

    } else {
        $moneynewid_x  = true;
        $bxid          = true;
        $upsummary_res = true;
    }

    if ($moneynewid_x && $upsummary_res && $bxid && $upborrow_info) {
        $done = true;
        $borrowInvestor->commit();
        /////////////////////////回款续投奖励预奖励取消开始 2013-05-10 fans///////////////////////////////
        $listreward = M("today_reward")->field("reward_uid,reward_money")->where("borrow_id={$borrow_id} AND reward_status=0")->select();
        if (!empty($listreward)) {
            // foreach($listreward as $v)
            // {
            //  membermoneylog( $v['reward_uid'],35,0-$v['reward_money'],"续投奖励({$borrow_name})预奖励取消",0,"@网站管理员@");
            // }
            $updata_s['deal_time']     = time();
            $updata_s['reward_status'] = 2;
            M("today_reward")->where("borrow_id={$borrow_id} AND reward_status=0")->save($updata_s);
        }
        /////////////////////////回款续投奖励预奖励取消结束 2013-05-10 fans///////////////////////////////
    } else {
        $borrowInvestor->rollback();
    }
    return $done;
}
function borrowApproved($borrow_id)
{
    
    $pre = c("DB_PREFIX");
    $done = false;
    $borrowInvestor = D("borrow_investor");
    $borrowInvestor->startTrans();
    $binfo = M("borrow_info")->field("loan_certificate,borrow_type,reward_type,reward_num,borrow_name,borrow_fee,borrow_money,borrow_uid,borrow_duration,repayment_type")->find($borrow_id);
    $borrow_name=$binfo["borrow_name"];

    //扣除冻结已失效预约金
    $bespeak = M("bespeak")->field(true)->where("borrow_id = {$borrow_id} and bespeak_status = 0")->select();
    if(is_array($bespeak)) {
        foreach ($bespeak as $v) {
            memberMoneyLog($v['bespeak_uid'], 223, 0 - $v['bespeak_money'] * $v['bespeak_point'], "{$borrow_name}复审通过，项目完成，扣除冻结已失效预约金", $binfo['borrow_uid']);
        }
    }

    $investorList = $borrowInvestor->field("id,investor_uid,investor_capital,investor_interest,is_experience,bonus_id,yubi")->where("borrow_id={$borrow_id}")->select();
    if(!$investorList) return false;
    $endTime = strtotime(date("Y-m-d", time()) . " 23:59:59");
    // if ($binfo['repayment_type'] == 1) {
    //     $deadline_last = strtotime("+{$binfo['borrow_duration']} day", $endTime);
    // } else {
    //     $deadline_last = strtotime("+{$binfo['borrow_duration']} month", $endTime);
    // }    
    // 按天算
    $dalist = $binfo['borrow_duration']-1;
    $deadline_last = strtotime("+{$dalist} day", $endTime);

    $_investor_num = count($investorList);
    $glodata = get_global_setting();
    $credit_investor = $glodata['credit_investor']?$glodata['credit_investor']:"100";//投资人奖励规则

    $condata['itemid']=$borrow_id;
    $condata['loan_certificate']=$binfo['loan_certificate'];
    foreach ($investorList as $key => $v) {
        $condata['conid']=$v['id'];
        $condata['uid']= $v['investor_uid'];
        $condata['add_time']=time();
        $conds[]=$condata;
        //print_r($v);
        //echo $v['investor_uid']."<br>";
        //echo getfloatvalue($v['investor_capital']);
        membercreditslog($v['investor_uid'],12,$v['investor_capital']/$credit_investor, "成功投标{$borrow_id}号标，奖励积分");
        //membercreditslog($v['investor_uid'],12,getfloatvalue($v['investor_capital']), "成功投标{$borrow_name}，奖励积分");
        //$credits_result = membercreditslog($binfo['borrow_uid'], 6, $credits_money * 2, $credits_info););
        if (0 < $binfo['reward_type']) {
            if ($binfo['reward_type'] == 1) {
                $_reward_money = getfloatvalue($v['investor_capital'] * $binfo['reward_num'] / 100, 2);
            } else if ($binfo['reward_type'] == 2) {
                $_reward_money = getfloatvalue($binfo['reward_num'] / $_investor_num, 2);
            }
        }
        
        $investorList[$key]['reward_money'] = floatval($_reward_money);
        mtip("chk14", $v['investor_uid'], $borrow_id);
        $saveinfo = array();
        $saveinfo['id'] = $v['id'];
        $saveinfo['reward_money'] = floatval($_reward_money);
        $saveinfo['deadline'] = $deadline_last;
        $saveinfo['status'] = 4;
        $upsummary_res = $borrowInvestor->save($saveinfo);
    }
    $res5=M("contract")->addAll($conds);
        //exit;
    //echo $deadline_last;
    $upborrow_res = m()->execute("update `{$pre}borrow_info` set `deadline`={$deadline_last} WHERE `id`={$borrow_id}");
    switch ($binfo['repayment_type']) {
        case 2 :
        case 3 :
        case 4 :
            $i = 1;
            for (; $i <= $binfo['borrow_duration']; ++$i) {
                $deadline = 0;
                $deadline = strtotime("+{$i} month", $endTime);
                $updetail_res = m()->execute("update `{$pre}investor_detail` set `deadline`={$deadline},`status`=7 WHERE `borrow_id`={$borrow_id} AND `sort_order`={$i}");
            }
            break;
        case 5 :
        case 1 :
            $deadline = 0;
            $deadline = $deadline_last;
            $updetail_res = m()->execute("update `{$pre}investor_detail` set `deadline`={$deadline},`status`=7 WHERE `borrow_id`={$borrow_id}");
            break;
    }
    
    // if ($updetail_res && $upsummary_res && $upborrow_res) {修改资海  复审不成功的问题
    // if ($updetail_res && $upsummary_res && $upborrow_res) {
        
        $done = true;
        
        $_P_fee = get_global_setting();
        $_borraccount = memberMoneyLog($binfo['borrow_uid'], 17, $binfo['borrow_money'], "{$borrow_name}复审通过，项目金额入帐");        
        inAccountMoney($binfo['borrow_uid'],$binfo['borrow_money'],3);
        if (!$_borraccount) {
            return false;
        }
        $_borrfee = memberMoneyLog($binfo['borrow_uid'], 18, 0 - $binfo['borrow_fee'], "{$borrow_name}项目成功，扣除项目管理费");
        if (!$_borrfee) {
            return false;
        }
       // $_freezefee = memberMoneyLog($binfo['borrow_uid'], 19, 0 - $binfo['borrow_money'] * $_P_fee['money_deposit'] / 100, "{$borrow_name}项目成功，冻结{$_P_fee['money_deposit']}%的保证金");
        if (!$_freezefee) {
          //  return false;
        }
        $_freezefee=true;
        $_investor_num = count($investorList);
        $_remoney_do = true;
        foreach ($investorList as $v) {
            
            if (0 < $v['reward_money']) {
                $_remoney_do = false;
                $_reward_m = memberMoneyLog($v['investor_uid'], 20, $v['reward_money'], "{$borrow_name}复审通过，获取投标奖励", $binfo['borrow_uid']);
                $_reward_m_give = memberMoneyLog($binfo['borrow_uid'], 21, 0 - $v['reward_money'], "{$borrow_name}复审通过，支付投标奖励", $v['investor_uid']);
                if ($_reward_m && $_reward_m_give) {
                    $_remoney_do = true;
                }
            }
            $remcollect = memberMoneyLog($v['investor_uid'], 15, $v['investor_capital'], "{$borrow_name}复审通过，项目完成，扣除冻结资金", $binfo['borrow_uid'], $target_uname = "",$shiwu=true,$v["yubi"]);

            useAccountMoney($v['investor_uid'],$v['investor_capital']);

           // $reinterestcollect = memberMoneyLog($v['investor_uid'], 28, $v['investor_interest'], "{$borrow_name}复审通过，应收利息成为待收金额", $binfo['borrow_uid']);
            
            //////////////////////邀请奖励开始////////////////////////////////////////            
            $vo = M('members')->field('user_name,recommend_id')->find($v['investor_uid']);
            $_rate = $_P_fee['award_invest']/1000;//推广奖励
            $jiangli = getFloatValue($_rate * $v['investor_capital'],2);
            if($vo['recommend_id']!=0){
                if(($binfo['borrow_type']=='1' || $binfo['borrow_type']=='2' || $binfo['borrow_type']=='5') && $binfo['repayment_type']!='1'){
                memberMoneyLog($vo['recommend_id'],13,$jiangli,hidecard($vo['user_name'],2)."对{$borrow_name}投资成功，你获得推广奖励".$jiangli."元。",$v['investor_uid']);
                }
            }            
            /////////////////////邀请奖励结束/////////////////////////////////////////
            //发放投资红包      
            if(!$v['is_experience'] && !$v['is_bonus'])      
            pubBonusByRules($v['investor_uid'],3,$v['investor_capital']);

            ////////////////////投资奖励开始////////////////////////////////////////
            //首投奖励
//             $stsql="select  *  from lzh_borrow_investor where investor_uid=".$v["investor_uid"]." and borrow_id!=1 group by borrow_id";
//             $stcount=m()->execute($stsql);
//             if($stcount==1&&$v['investor_capital']>=10000){
//                 memberMoneyLog($v['investor_uid'], 21, 18, "{$borrow_name}首投投标奖励", $binfo['borrow_uid']);
//             }
             //续投发红包
//             if($stcount>1){
//
//                 $jmap="SELECT * FROM `lzh_borrow_info` WHERE id=".$borrow_id." and (( `borrow_name` LIKE '%私人订制%' ) or  ( `borrow_name` LIKE '%三人成团%' )) limit 1";
//                 $res=M()->query($jmap);
//                 if(empty($res)){
                     if($v["bonus_id"]){
                         $bonusmoney=M("member_bonus")->where("id=".$v["bonus_id"])->getField('money_bonus');
                         $xtmoney=$v["investor_capital"]-$v["yubi"]- $bonusmoney;
                     }else{
                         $xtmoney=$v["investor_capital"]-$v["yubi"];
                     }
                     $xtmoney=floor($xtmoney*0.002);
                     if($xtmoney>0){
                         memberMoneyLog($v['investor_uid'], 21, $xtmoney, "{$borrow_name}续养奖励", $binfo['borrow_uid']);
                     }
//                 }
//             }

        }

        $listreward =M("today_reward")->field("reward_uid,reward_money")->where("borrow_id={$borrow_id} AND reward_status=0")->select();
        if(!empty($listreward))
        {
            foreach($listreward as $v)
            {
                //membermoneylog($v['reward_uid'],34,$v['reward_money'],"续投奖励({$borrow_name})预奖励到账",0,"@网站管理员@");
            }
            $updata_s['deal_time'] = time();
            $updata_s['reward_status'] = 1;
            M("today_reward")->where("borrow_id={$borrow_id} AND reward_status=0")->save($updata_s);
        }

    if (!$_remoney_do || !$remcollect) {
        $borrowInvestor->rollback();
        return false;
    } else {        
        $borrowInvestor->commit();
        // 若为秒标，复审通过，进入还款
        if($binfo["borrow_type"]==3){
           // borrowrepayment($borrow_id, 1);             
        }        
    }
   
    return $done;
}
function lastRepayment($binfo)
{
    $x = true;
    if ($binfo['borrow_type'] == 2) {
        $x = false;
        $x = memberlimitlog($binfo['borrow_uid'], 8, $binfo['borrow_money'], $info = "{$binfo['id']}号标还款完成");
        if (!$x) {
            return false;
        }
        $vocuhlist = m("borrow_vouch")->field("uid,vouch_money")->where("borrow_id={$binfo['id']}")->select();
        foreach ($vocuhlist as $vv) {
            $x = memberlimitlog($vv['uid'], 10, $vv['vouch_money'], $info = "您担保的{$binfo['id']}号标还款完成");
        }
    } else if ($binfo['borrow_type'] == 1) {
        $x = false;
        $x = memberlimitlog($binfo['borrow_uid'], 7, $binfo['borrow_money'], $info = "{$binfo['id']}号标还款完成");
    }
    if (!$x) {
        return false;
    }
    $_P_fee = get_global_setting();
    $accountMoney_borrower = m("member_money")->field("account_money,money_collect,money_freeze")->find($binfo['borrow_uid']);
    $datamoney_x['uid'] = $binfo['borrow_uid'];
    $datamoney_x['type'] = 24;
    $datamoney_x['affect_money'] = $binfo['borrow_money'] * $_P_fee['money_deposit'] / 100;
    $datamoney_x['account_money'] = $accountMoney_borrower['account_money'] + $datamoney_x['affect_money'];
    $datamoney_x['collect_money'] = $accountMoney_borrower['money_collect'];
    $datamoney_x['freeze_money'] = $accountMoney_borrower['money_freeze'] - $datamoney_x['affect_money'];
    $mmoney_x['money_freeze'] = $datamoney_x['freeze_money'];
    $mmoney_x['money_collect'] = $datamoney_x['collect_money'];
    $mmoney_x['account_money'] = $datamoney_x['account_money'];
    $datamoney_x['info'] = "对{$binfo['id']}还款完成的解冻保证金";
    $datamoney_x['add_time'] = time();
    $datamoney_x['add_ip'] = get_client_ip();
    $datamoney_x['target_uid'] = 0;
    $datamoney_x['target_uname'] = "@网站管理员@";
    $moneynewid_x = m("member_moneylog")->add($datamoney_x);
    if ($moneynewid_x) {
        $bxid = m("member_money")->where("uid={$datamoney_x['uid']}")->save($mmoney_x);
    }
    if ($bxid && $x) {
        return true;
    } else {
        return false;
    }
}
function borrowRepayment($borrow_id, $sort_order, $type = 1)
{
    $pre = c("DB_PREFIX");
    $done = false;
    $borrowDetail = d("investor_detail");
    $binfo = m("borrow_info")->field("id,borrow_uid,borrow_name,borrow_type,borrow_money,borrow_duration,repayment_type,has_pay,total,deadline")->find($borrow_id);
    $borrow_name= $binfo["borrow_name"];
    $b_member = m("members")->field("user_name")->find($binfo['borrow_uid']);
    if ($sort_order <= $binfo['has_pay']) {        
        return "本期已还过，不用再还";
    }
    if ($binfo['has_pay'] == $binfo['total']) {
        return "此标已经还完，不用再还";
    }
    if ($binfo['has_pay'] + 1 < $sort_order) {
        return (("对不起，此项目第" . ($binfo['has_pay'] + 1)) . "期还未还，请先还第" . ($binfo['has_pay'] + 1)) . "期";
    }
    if (time() < $binfo['deadline'] && $type == 2) {
        return "此标还没逾期，不用代还";
    }
    $voxe = $borrowDetail->field("sort_order,sum(capital) as capital, sum(interest) as interest,deadline,substitute_time")->where("borrow_id={$borrow_id}")->group("sort_order")->select();
    foreach ($voxe as $ee => $ss) {
        if ($ss['sort_order'] == $sort_order) {
            $vo = $ss;
        }
    }
    
    if ($vo['deadline'] < time()) {
        $is_expired = true;
        if (0 < $vo['substitute_time']) {
            $is_substitute = true;
        } else {
            $is_substitute = false;
        }
        $expired_days = getexpireddays($vo['deadline']);
        $expired_money = getexpiredmoney($expired_days, $vo['capital'], $vo['interest']);
        $call_fee = getexpiredcallfee($expired_days, $vo['capital'], $vo['interest']);
    } else {
        $is_expired = false;
        $expired_days = 0;
        $expired_money = 0;
        $call_fee = 0;
    }
    
    mtip("chk25", $binfo['borrow_uid'], $borrow_id);
    $accountMoney_borrower = m("member_money")->field("money_freeze,money_collect,account_money")->find($binfo['borrow_uid']);
    if ($type == 1 && $binfo['borrow_type'] != 3 && $accountMoney_borrower['account_money'] < $vo['capital'] + $vo['interest'] + $expired_money + $call_fee) {
        return "帐户可用余额不足，本期还款共需" . ($vo['capital'] + $vo['interest'] + $expired_money + $call_fee) . "元，请先充值";
    }
    if ($is_substitute && $is_expired) {
        $borrowDetail->startTrans();
        $datamoney_x['uid'] = $binfo['borrow_uid'];
        $datamoney_x['type'] = 11;
        $datamoney_x['affect_money'] = 0 - ($vo['capital'] + $vo['interest']);
        $datamoney_x['account_money'] = $accountMoney_borrower['account_money'] + $datamoney_x['affect_money'];
        $datamoney_x['collect_money'] = $accountMoney_borrower['money_collect'];
        $datamoney_x['freeze_money'] = $accountMoney_borrower['money_freeze'];
        $mmoney_x['money_freeze'] = $datamoney_x['freeze_money'];
        $mmoney_x['money_collect'] = $datamoney_x['collect_money'];
        $mmoney_x['account_money'] = $datamoney_x['account_money'];
        $datamoney_x['info'] = "对{$borrow_name}第{$sort_order}期还款";
        $datamoney_x['add_time'] = time();
        $datamoney_x['add_ip'] = get_client_ip();
        $datamoney_x['target_uid'] = 0;
        $datamoney_x['target_uname'] = "@网站管理员@";
        $moneynewid_x = m("member_moneylog")->add($datamoney_x);
        if ($moneynewid_x) {
            $bxid = m("member_money")->where("uid={$datamoney_x['uid']}")->save($mmoney_x);
        }
        $accountMoney_borrower = m("member_money")->field("money_freeze,money_collect,account_money")->find($binfo['borrow_uid']);
        $datamoney_x = array();
        $mmoney_x = array();
        $datamoney_x['uid'] = $binfo['borrow_uid'];
        $datamoney_x['type'] = 30;
        $datamoney_x['affect_money'] = 0 - $expired_money;
        $datamoney_x['account_money'] = $accountMoney_borrower['account_money'] + $datamoney_x['affect_money'];
        $datamoney_x['collect_money'] = $accountMoney_borrower['money_collect'];
        $datamoney_x['freeze_money'] = $accountMoney_borrower['money_freeze'];
        $mmoney_x['money_freeze'] = $datamoney_x['freeze_money'];
        $mmoney_x['money_collect'] = $datamoney_x['collect_money'];
        $mmoney_x['account_money'] = $datamoney_x['account_money'];
        $datamoney_x['info'] = "{$borrow_name}第{$sort_order}期的逾期罚息";
        $datamoney_x['add_time'] = time();
        $datamoney_x['add_ip'] = get_client_ip();
        $datamoney_x['target_uid'] = 0;
        $datamoney_x['target_uname'] = "@网站管理员@";
        $moneynewid_x = m("member_moneylog")->add($datamoney_x);
        if ($moneynewid_x) {
            $bxid = m("member_money")->where("uid={$datamoney_x['uid']}")->save($mmoney_x);
        }
        $accountMoney_borrower = m("member_money")->field("money_freeze,money_collect,account_money")->find($binfo['borrow_uid']);
        $datamoney_x = array();
        $mmoney_x = array();
        $datamoney_x['uid'] = $binfo['borrow_uid'];
        $datamoney_x['type'] = 31;
        $datamoney_x['affect_money'] = 0 - $call_fee;
        $datamoney_x['account_money'] = $accountMoney_borrower['account_money'] + $datamoney_x['affect_money'];
        $datamoney_x['collect_money'] = $accountMoney_borrower['money_collect'];
        $datamoney_x['freeze_money'] = $accountMoney_borrower['money_freeze'];
        $mmoney_x['money_freeze'] = $datamoney_x['freeze_money'];
        $mmoney_x['money_collect'] = $datamoney_x['collect_money'];
        $mmoney_x['account_money'] = $datamoney_x['account_money'];
        $datamoney_x['info'] = "{$borrow_name}第{$sort_order}期的逾期催收费";
        $datamoney_x['add_time'] = time();
        $datamoney_x['add_ip'] = get_client_ip();
        $datamoney_x['target_uid'] = 0;
        $datamoney_x['target_uname'] = "@网站管理员@";
        $moneynewid_x = m("member_moneylog")->add($datamoney_x);
        if ($moneynewid_x) {
            $bxid = m("member_money")->where("uid={$datamoney_x['uid']}")->save($mmoney_x);
        }
        $updetail_res = m()->execute("update `{$pre}investor_detail` set `repayment_time`=" . time() . " WHERE `borrow_id`={$borrow_id} AND `sort_order`={$sort_order}");
        if ($updetail_res && $bxid) {
            $borrowDetail->commit();
            return true;
        } else {
            $borrowDetail->rollback();
            return false;
        }
    }
    
    $detailList = $borrowDetail->field("invest_id,investor_uid,capital,interest,interest_fee,borrow_id,total")->where("borrow_id={$borrow_id} AND sort_order={$sort_order}")->select();
    
    /*************************************逾期还款积分与还款状态处理开始 20130509 fans***********************************/
    $datag = get_global_setting();
    $credit_borrow = explode("|",$datag['credit_borrow']);
    if($type==1){//客户自己还款才需要记录这些操作
        $day_span = ceil(($vo['deadline']-time())/(3600*24));
        $credits_money = intval($vo['capital']/$credit_borrow[4]);
        $credits_info = "对第{$borrow_id}号标的还款操作";
        if($day_span>0 && $day_span<=3){//正常还款
            $credits_result = memberCreditsLog($binfo['borrow_uid'],3,$credits_money*$credit_borrow[0],$credits_info);
            $idetail_status=1;
        }elseif($day_span>-3 && $day_span<=0){//迟还
            $credits_result = memberCreditsLog($binfo['borrow_uid'],4,$credits_money*$credit_borrow[1],$credits_info);
            $idetail_status=3;
        }elseif($day_span<=-3){//逾期还款
            $credits_result = memberCreditsLog($binfo['borrow_uid'],5,$credits_money*$credit_borrow[2],$credits_info);
            $idetail_status=5;
        }elseif($day_span>3){//提前还款
            $credits_result = memberCreditsLog($binfo['borrow_uid'],6,$credits_money*$credit_borrow[3],$credits_info);
            $idetail_status=2;
        }
        if(!$credits_result) return "因积分记录失败，未完成还款操作";
    }
    /*************************************逾期还款积分与还款状态处理结束 20130509 fans***********************************/
    
    $borrowDetail->startTrans();
    $bxid = true;
    if ($type == 1) {
        $bxid = false;
        $datamoney_x['uid'] = $binfo['borrow_uid'];
        $datamoney_x['type'] = 11;
        $datamoney_x['affect_money'] = 0 - ($vo['capital'] + $vo['interest']);
        $datamoney_x['account_money'] = $accountMoney_borrower['account_money'] + $datamoney_x['affect_money'];
        $datamoney_x['collect_money'] = $accountMoney_borrower['money_collect'];
        $datamoney_x['freeze_money'] = $accountMoney_borrower['money_freeze'];
        $mmoney_x['money_freeze'] = $datamoney_x['freeze_money'];
        $mmoney_x['money_collect'] = $datamoney_x['collect_money'];
        $mmoney_x['account_money'] = $datamoney_x['account_money'];
        $datamoney_x['info'] = "对{$borrow_name}第{$sort_order}期还款";
        $datamoney_x['add_time'] = time();
        $datamoney_x['add_ip'] = get_client_ip();
        $datamoney_x['target_uid'] = 0;
        $datamoney_x['target_uname'] = "@网站管理员@";
        $moneynewid_x = m("member_moneylog")->add($datamoney_x);
        if ($moneynewid_x) {
            $bxid = m("member_money")->where("uid={$datamoney_x['uid']}")->save($mmoney_x);
        }
        if ($is_expired) {
            if (0 < $expired_money) {
                $accountMoney_borrower = m("member_money")->field("money_freeze,money_collect,account_money")->find($binfo['borrow_uid']);
                $datamoney_x = array();
                $mmoney_x = array();
                $datamoney_x['uid'] = $binfo['borrow_uid'];
                $datamoney_x['type'] = 30;
                $datamoney_x['affect_money'] = 0 - $expired_money;
                $datamoney_x['account_money'] = $accountMoney_borrower['account_money'] + $datamoney_x['affect_money'];
                $datamoney_x['collect_money'] = $accountMoney_borrower['money_collect'];
                $datamoney_x['freeze_money'] = $accountMoney_borrower['money_freeze'];
                $mmoney_x['money_freeze'] = $datamoney_x['freeze_money'];
                $mmoney_x['money_collect'] = $datamoney_x['collect_money'];
                $mmoney_x['account_money'] = $datamoney_x['account_money'];
                $datamoney_x['info'] = "{$borrow_name}第{$sort_order}期的逾期罚息";
                $datamoney_x['add_time'] = time();
                $datamoney_x['add_ip'] = get_client_ip();
                $datamoney_x['target_uid'] = 0;
                $datamoney_x['target_uname'] = "@网站管理员@";
                $moneynewid_x = m("member_moneylog")->add($datamoney_x);
                if ($moneynewid_x) {
                    $bxid = m("member_money")->where("uid={$datamoney_x['uid']}")->save($mmoney_x);
                }
            }
            if (0 < $call_fee) {
                $accountMoney_borrower = m("member_money")->field("money_freeze,money_collect,account_money")->find($binfo['borrow_uid']);
                $datamoney_x = array();
                $mmoney_x = array();
                $datamoney_x['uid'] = $binfo['borrow_uid'];
                $datamoney_x['type'] = 31;
                $datamoney_x['affect_money'] = 0 - $call_fee;
                $datamoney_x['account_money'] = $accountMoney_borrower['account_money'] + $datamoney_x['affect_money'];
                $datamoney_x['collect_money'] = $accountMoney_borrower['money_collect'];
                $datamoney_x['freeze_money'] = $accountMoney_borrower['money_freeze'];
                $mmoney_x['money_freeze'] = $datamoney_x['freeze_money'];
                $mmoney_x['money_collect'] = $datamoney_x['collect_money'];
                $mmoney_x['account_money'] = $datamoney_x['account_money'];
                $datamoney_x['info'] = "{$borrow_name}第{$sort_order}期的逾期催收费";
                $datamoney_x['add_time'] = time();
                $datamoney_x['add_ip'] = get_client_ip();
                $datamoney_x['target_uid'] = 0;
                $datamoney_x['target_uname'] = "@网站管理员@";
                $moneynewid_x = m("member_moneylog")->add($datamoney_x);
                if ($moneynewid_x) {
                    $bxid = m("member_money")->where("uid={$datamoney_x['uid']}")->save($mmoney_x);
                }
            }
        }
    }
    $upborrowsql = "update `{$pre}borrow_info` set ";
    $upborrowsql .= "`repayment_money`=`repayment_money`+{$vo['capital']}";
    $upborrowsql .= ",`repayment_interest`=`repayment_interest`+ {$vo['interest']}";
    if ($sort_order == $binfo['total']) {
        $upborrowsql .= ",`borrow_status`=7";
    }
    if ($type == 2) {
        $total_subs = $vo['capital'] + $vo['interest'];
        $upborrowsql .= ",`substitute_money`=`substitute_money`+ {$total_subs}";
    }
    if ($type == 1) {
        $upborrowsql .= ",`has_pay`={$sort_order}";
    }
    if ($is_expired) {
        $upborrowsql .= ",`expired_money`=`expired_money`+{$expired_money}";
    }
    $upborrowsql .= " WHERE `id`={$borrow_id}";
    $upborrow_res = m()->execute($upborrowsql);
    if ($type == 2) {
        $updetail_res = m()->execute("update `{$pre}investor_detail` set `receive_capital`=`capital`,`substitute_time`=" . time() . " ,`substitute_money`=`substitute_money`+{$total_subs},`status`=4 WHERE `borrow_id`={$borrow_id} AND `sort_order`={$sort_order}");
    } else if ($is_expired) {
        $updetail_res = m()->execute("update `{$pre}investor_detail` set `receive_capital`=`capital` ,`receive_interest`=(`interest`-`interest_fee`),`repayment_time`=" . time() . ",`call_fee`={$call_fee},`expired_money`={$expired_money},`expired_days`={$expired_days},`status`={$idetail_status} WHERE `borrow_id`={$borrow_id} AND `sort_order`={$sort_order}");
    } else {
        $updetail_res = m()->execute("update `{$pre}investor_detail` set `receive_capital`=`capital` ,`receive_interest`=(`interest`-`interest_fee`),`repayment_time`=" . time() . ", `status`={$idetail_status} WHERE `borrow_id`={$borrow_id} AND `sort_order`={$sort_order}");
    }
    $smsUid = "";
    foreach ($detailList as $v) {
        
        //用于判断是否债权转让 ,债权转让日志不一样
        $bond = M("invest_bond")->field("serialid")->where("invest_id={$v['invest_id']} and status=1")->find();
        //新增  
        
        
        
        $getInterest = $v['interest'] - $v['interest_fee'];
        $upsql = "update `{$pre}borrow_investor` set ";
        $upsql .= "`receive_capital`=`receive_capital`+{$v['capital']},";
        $upsql .= "`receive_interest`=`receive_interest`+ {$getInterest},";
        if ($type == 2) {
            $total_s_invest = $v['capital'] + $getInterest;
            $upsql .= "`substitute_money` = `substitute_money` + {$total_s_invest},";
        }
        if ($sort_order == $binfo['total']) {
            $upsql .= "`status`=5,";
        }
        $upsql .= "`paid_fee`=`paid_fee`+{$v['interest_fee']}";
        $upsql .= " WHERE `id`={$v['invest_id']}";
        $upinfo_res = m()->execute($upsql);
        if ($upinfo_res) {
            $accountMoney = m("member_money")->field("money_freeze,money_collect,account_money")->find($v['investor_uid']);
            $datamoney['uid'] = $v['investor_uid'];
            $datamoney['type'] = $type == 2 ? "10" : "9";
            $datamoney['affect_money'] = $v['capital'] + $v['interest'];
            $datamoney['account_money'] = $accountMoney['account_money'] + $datamoney['affect_money'];
            $datamoney['collect_money'] = $accountMoney['money_collect'] - $datamoney['affect_money'];
            $datamoney['freeze_money'] = $accountMoney['money_freeze'];
            $mmoney['money_freeze'] = $datamoney['freeze_money'];
            $mmoney['money_collect'] = $datamoney['collect_money'];
            $mmoney['account_money'] = $datamoney['account_money'];
            $datamoney['info'] = $type == 2 ? "网站对{$borrow_name}第{$sort_order}期代还" : "会员对{$borrow_name}第{$sort_order}期还款";
            $bond['serialid'] &&  $datamoney['info'] = ($type==2)?"网站对{$bond['serialid']}号债权第{$sort_order}期代还":"收到会员对{$bond['serialid']}号债权第{$sort_order}期的还款";  ///债权转让日志
            
            
            $datamoney['add_time'] = time();
            $datamoney['add_ip'] = get_client_ip();
            if ($type == 2) {
                $datamoney['target_uid'] = 0;
                $datamoney['target_uname'] = "@网站管理员@";
            } else {
                $datamoney['target_uid'] = $binfo['borrow_uid'];
                $datamoney['target_uname'] = $b_member['user_name'];
            }
            $moneynewid = m("member_moneylog")->add($datamoney);
            if ($moneynewid) {
                $xid = m("member_money")->where("uid={$datamoney['uid']}")->save($mmoney);
            }
            if ($type == 2) {
                mtip("chk18", $v['investor_uid'], $borrow_id);
            } else {
                mtip("chk16", $v['investor_uid'], $borrow_id);
            }
            $smsUid .= empty($smsUid) ? $v['investor_uid'] : ",{$v['investor_uid']}";
            $xid_z = true;
            if (0 < $v['interest_fee'] && $type == 1) {
                $xid_z = false;
                $accountMoney = m("member_money")->field("money_freeze,money_collect,account_money")->find($v['investor_uid']);
                $datamoney_z['uid'] = $v['investor_uid'];
                $datamoney_z['type'] = 23;
                $minfo =getMinfo($v['investor_uid'],true);
                $levename=getLeveName($minfo["credits"]);
                $levetixian=getLeveTixian($minfo["credits"]);
                //对应等级的 利息管理费  资海
                
                if($binfo["borrow_type"]==3)$levetixian=0;
                $fee_rate = $levetixian / 100;
                $v['interest_fee']=getfloatvalue($v['interest']*$fee_rate,2);
                $datamoney_z['affect_money'] = 0 - $v['interest_fee'];
                $datamoney_z['account_money'] = $mmoney['account_money'] + $datamoney_z['affect_money'];
                $datamoney_z['collect_money'] = $mmoney['money_collect'];
                $datamoney_z['freeze_money'] = $mmoney['money_freeze'];
                $mmoney_z['money_freeze']  = $datamoney_z['freeze_money'];
                $mmoney_z['money_collect'] = $datamoney_z['collect_money'];
                $mmoney_z['account_money'] = $datamoney_z['account_money'];
                $datamoney_z['info'] = "支付{$borrow_name}第{$sort_order}期还款的利息管理费";
                $datamoney_z['add_time'] = time();
                $datamoney_z['add_ip'] = get_client_ip();
                $datamoney_z['target_uid'] = 0;
                $datamoney_z['target_uname'] = "@网站管理员@";
                $moneynewid_z = m("member_moneylog")->add($datamoney_z);
                if ($moneynewid_z) {
                    $xid_z = m("member_money")->where("uid={$datamoney_z['uid']}")->save($mmoney_z);
                }
            }
        }
    }
    if ($updetail_res && $upinfo_res && $xid && $upborrow_res && $bxid && $xid_z) {
        $borrowDetail->commit();        
        $rs = new BondBehavior();
        $rs->cancelBond($borrow_id);
        $_last = true;
        if ($binfo['total'] == $binfo['has_pay'] + 1 && $type == 1) {
            $_last = false;
            $_is_last = lastrepayment($binfo);
            if ($_is_last) {
                $_last = true;
            }
        }
        if ($_last === false) {
           // return "因满标操作未完成，还款操作失败";
        }
        $done = true;
        $vphone = m("members")->field("user_phone")->where("id in({$smsUid})")->select();
        $sphone = "";
        foreach ($vphone as $v) {
            $sphone .= empty($sphone) ? $v['user_phone'] : ",{$v['user_phone']}";
        }
        smstip("payback", $sphone, array(
            "#ID#",
            "#ORDER#"
        ), array(
            $borrow_id,
            $sort_order
        ));
        $done = true;
    } else {
        $borrowDetail->rollback();
        $done = false;
    }
    return $done;
}
function getBorrowInterestRate($rate, $duration)
{
    return $rate / 1200 * $duration;
}
//sendsms(手机号, 内容,$type=0)
function sendsms($mob, $content,$type=0)
{

    $content = str_replace('【','(', $content);
    $content = str_replace('】',')', $content);
    // if(empty($host)){return '非法操作';}
    $msgconfig = fs("Webconfig/msgconfig");
    $user = $msgconfig['sms']['user'];
    $pass = md5($msgconfig['sms']['pass']);
    //建周短信使用分号隔开多个手机号
    $statusStr = array(
        "0" => "短信发送成功", 
        "-1" => "参数不全",
        "-2" => "服务器空间不支持,请确认支持curl或者fsocket，联系您的空间商解决或者更换空间！",
        "30" => "密码错误",
        "40" => "账号不存在",
        "41" => "余额不足",
        "42" => "帐户已过期",
        "43" => "IP地址限制",
        "50" => "内容含有敏感词"
    );
    // $user = "gthy"; //短信平台帐号
    // $pass = md5("1010890372..."); //短信平台密码
    
    $client_ip = explode(':', get_client_ip(),2);
    $addData['content'] = $content;
    $addData['type'] = $type;
    $addData['cellphone'] = $mob;
    $addData['add_time'] = time();
    $addData['add_ip'] = $client_ip[0];    
    $addData['status'] = 0;
    $addData['uid'] = session("u_id");
    $addData['aid'] = session("admin");
//	var_dump($mob);
    // if(strlen($mob)!=11){return '手机号格式不正确';} 
    $m = M('log_sms');
    //限制一：12小时内不得超过300 
    $countCellphone12ha = $m->where("FROM_UNIXTIME(`add_time`) >= DATE_SUB(NOW(),INTERVAL 12 HOUR) and status>0")->count('id');
    // if($countCellphone12ha>300) {return '您的操作过于频繁，请稍后重试。';} 3587
    //限制二：单个手机号12小时不得超过20;
    $countCellphone12hs = $m->where("`cellphone` = '{$mob}' and FROM_UNIXTIME(`add_time`) >= DATE_SUB(NOW(),INTERVAL 12 HOUR) and status>0")->count('id');
    if($countCellphone12hs>20) {return '您的操作过于频繁，请稍后重试。';}
    //限制三：单个手机号300秒不得超过3条
    $countCellphone = $m->where("`cellphone` = '{$mob}' and add_time > (unix_timestamp()-300)")->count('id');
    //限制四：注册验证短信，单个手机号或单个ip300秒不得超过5条
    if($type == 1){
        $countCellphone = M('log_sms')->where("(`cellphone` = '{$mob}' or add_ip like '%{$addData['add_ip']}%') and add_time > (unix_timestamp()-300)")->count('id');
    }
    if($countCellphone >= 5){return '您的操作过于频繁，请稍后重试。';} 
    $newid = $m->add($addData);
    $mob = $mob;
    $content = $msgconfig['sms']['note'].$content;
    writeLog($content);           
    $smsapi = "http://api.smsbao.com/";
    $sendurl = $smsapi."sms?u=".$user."&p=".$pass."&m=".$mob."&c=".urlencode($content);
    $rs =file_get_contents($sendurl);        
    writeLog($rs);           
    if($rs == 0){
        $m->save(array('status'=>1,'id'=>$newid));
        return true;
    }else{        
        $m->save(array('status'=>2,'id'=>$newid));
        return false;        
    }
}
function sendsms20181216($mob, $content,$type=0)
{

    $content = str_replace('【','(', $content);
    $content = str_replace('】',')', $content);
    
 
    // if(empty($host)){return '非法操作';}
    $msgconfig = fs("Webconfig/msgconfig");
    $user = $msgconfig['sms']['user'];
    // var_dump($user,$msgconfig['sms']['pass'],);die;
    $pass = md5($msgconfig['sms']['pass']);
    //建周短信使用分号隔开多个手机号
    $statusStr = array(
        "0" => "短信发送成功",
        "-1" => "参数不全",
        "-2" => "服务器空间不支持,请确认支持curl或者fsocket，联系您的空间商解决或者更换空间！",
        "30" => "密码错误",
        "40" => "账号不存在",
        "41" => "余额不足",
        "42" => "帐户已过期",
        "43" => "IP地址限制",
        "50" => "内容含有敏感词"
    );
    // $user = "gthy"; //短信平台帐号
    // $pass = md5("1010890372..."); //短信平台密码
    
    $client_ip = explode(':', get_client_ip(),2);
    $addData['content'] = $content;
    $addData['type'] = $type;
    $addData['cellphone'] = $mob;
    $addData['add_time'] = time();
    $addData['add_ip'] = $client_ip[0];    
    $addData['status'] = 0;
    $addData['uid'] = session("u_id");
    $addData['aid'] = session("admin");
//	var_dump($mob);
    // if(strlen($mob)!=11){return '手机号格式不正确';}
    $m = M('log_sms');
//  //限制一：12小时内不得超过300
//  $countCellphone12ha = $m->where("FROM_UNIXTIME(`add_time`) >= DATE_SUB(NOW(),INTERVAL 12 HOUR) and status>0")->count('id');
//  if($countCellphone12ha>300) {return '您的操作过于频繁，请稍后重试。';}
//  //限制二：单个手机号12小时不得超过20;
//  $countCellphone12hs = $m->where("`cellphone` = '{$mob}' and FROM_UNIXTIME(`add_time`) >= DATE_SUB(NOW(),INTERVAL 12 HOUR) and status>0")->count('id');
//  if($countCellphone12hs>20) {return '您的操作过于频繁，请稍后重试。';}
//  //限制三：单个手机号300秒不得超过3条
//  $countCellphone = $m->where("`cellphone` = '{$mob}' and add_time > (unix_timestamp()-300)")->count('id');
//  //限制四：注册验证短信，单个手机号或单个ip300秒不得超过5条
//  if($type == 1){
//      $countCellphone = M('log_sms')->where("(`cellphone` = '{$mob}' or add_ip like '%{$addData['add_ip']}%') and add_time > (unix_timestamp()-300)")->count('id');
//  }
//  if($countCellphone >= 5){return '您的操作过于频繁，请稍后重试。';} 
    $newid = $m->add($addData);
    $mob = $mob;
    $content = $msgconfig['sms']['note'].$content;
    writeLog($content);           
    $smsapi = "http://api.smsbao.com/";
    $sendurl = $smsapi."sms?u=".$user."&p=".$pass."&m=".$mob."&c=".urlencode($content);
    //   var_dump($sendurl);die;
    // var_dump($sendurl,1);die;
//   file_put_contents(time().'smssend.txt', var_export($sendurl, true));
    //$rs =file_get_contents($sendurl);   
    
    $curl = curl_init();
    //设置抓取的url
    curl_setopt($curl, CURLOPT_URL, $sendurl);
    //设置头文件的信息作为数据流输出
    curl_setopt($curl, CURLOPT_HEADER, 0);
    //设置获取的信息以文件流的形式返回，而不是直接输出。
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    
    //执行命令
    $rs = curl_exec($curl);
    //关闭URL请求
    curl_close($curl);
    //显示获得的数据
    //var_dump($rs);die;
    
    //  file_put_contents(time().'smssend1.txt', var_export($rs, true));
     
    // return $rs;
    // writeLog($rs);           
    if($rs == 0){
        $m->save(array('status'=>1,'id'=>$newid));
        return true;
    }else{        
        $m->save(array('status'=>2,'id'=>$newid));
        return false;        
    }
}
function getOrderList($map, $size)
{   $pre = c("DB_PREFIX");
    if (empty($map['uid'])) {
        return;
    }
    if ($size) {
        import("ORG.Util.Page");
        $count = m('order b')->join("{$pre}market m ON m.id=b.gid")->where($map)->count('b.id');
        $p = new Page($count, $size);
        $page = $p->show();
        $Lsql = "{$p->firstRow},{$p->listRows}";
    }
        $field= 'b.id,b.uid,b.gid,b.jifen,b.jine,b.num,b.add_time,b.add_ip,b.yijian,b.action,m.title';
        $list = M('order b')->field($field)->join("{$pre}market m ON m.id=b.gid")->where($map)->limit($Lsql)->order("b.id DESC")->select();
        //$list = $this->_listFilter($list);
    $type_arr = c("MONEY_LOG");
    foreach ($list as $key => $v) {
        $list[$key]['type'] = $type_arr[$v['type']];
    }
    $row = array();
    $row['list'] = $list;
    $row['page'] = $page;
    return $row;
}
function getCreditsLog($map, $size)
{
    if (empty($map['uid'])) {
        return;
    }
    if ($size) {
        import("ORG.Util.Page");
        $count = m("member_creditslog")->where($map)->count("id");
        $p = new Page($count, $size);
        $page = $p->show();
        $Lsql = "{$p->firstRow},{$p->listRows}";
    }
    $list = m("member_creditslog")->where($map)->order("id DESC")->limit($Lsql)->select();
    $type_arr = c("MONEY_LOG");
    foreach ($list as $key => $v) {
        $list[$key]['type_c'] = $type_arr[$v['type']];
    }
    $row = array();
    $row['list'] = $list;
    $row['page'] = $page;
    $row['count'] = $count;
    return $row;
}
function getMoneyLog($map, $size)
{
    if (empty($map['uid'])) {
        return;
    }
    $row = array();
    if ($size) {
        import("ORG.Util.Page");
        $count = m("member_moneylog")->where($map)->count("id");
        $p = new Page($count, $size);
        $row['_page'] = pageSet($p);
        $page = $p->show();
        $Lsql = "{$p->firstRow},{$p->listRows}";
    }
    $list = m("member_moneylog")->where($map)->order("id DESC")->limit($Lsql)->select();
    $type_arr = c("MONEY_LOG");
    foreach ($list as $key => $v) {
        $list[$key]['typeid'] = $v['type'];
        $list[$key]['type'] = $type_arr[$v['type']];
    }
    $row['list'] = $list;
    $row['page'] = $page;
    return $row;
}
function memberMoneyLog($uid, $type, $amoney, $info = "", $target_uid = 0, $target_uname = "",$shiwu=true,$yubi=0,$benjin=0)
{
    $xva = floatval($amoney);
    if (empty($xva)) {
        return true;
    }
    $done = false;

    $MM = m("member_money")->lock(true)->field("money_freeze,money_collect,account_money,money_experience,yubi,yubi_freeze,yongjin")->find($uid);
    if (!is_array($MM)) {
        m("member_money")->add(array(
            "uid" => $uid
        ));
        $MM = m("member_money")->lock(true)->field("money_freeze,money_collect,account_money,money_experience,yubi,yubi_freeze,yongjin")->find($uid);
    }

    if($MM==false){
        return false;
    }
    $Moneylog = d("member_moneylog");
    if (in_array($type, array("71", "72", "73", "733"))) {
        $type_save = 7;
    } else {
        $type_save = $type;
    }
    if (empty($target_uname) && 0 < $target_uid) {
        $tname = M("members")->getFieldById($target_uid, "user_name");
    } else {
        $tname = $target_uname;
    }
    if (empty($target_uid) && empty($target_uname)) {
        $target_uid = 0;
        $target_uname = "@网站管理员@";
    }
    if($shiwu){
        $Moneylog->startTrans();
    }
    $data['uid'] = $uid;
    $data['type'] = $type_save;
    $data['info'] = $info;
    $data['target_uid'] = $target_uid;
    $data['target_uname'] = $tname;
    $data['add_time'] = time();
    $data['add_ip'] = get_client_ip();
    switch ($type) {
        case 6 :
            if(($MM['yubi']+ $amoney)>=0){
                $data['affect_money'] = $amoney;
                $data['yubi'] = bcadd($MM['yubi'] , $amoney,2);
                $data['collect_money'] = $MM['money_collect'];
                $data['freeze_yubi'] = bcsub($MM['yubi_freeze'] , $amoney,2);

                $data['account_money'] = $MM['account_money'];
                $data['freeze_money'] =$MM['money_freeze'];
                $data['yongjin'] =$MM['yongjin'];
            }else{
                $yamoney=$amoney+$MM["yubi"];
                $bamoney=-$MM["yubi"];
                $data['affect_money'] = $amoney;
                $data['account_money'] = bcadd($MM['account_money'] , $yamoney,2);
                $data['collect_money'] = $MM['money_collect'];
                $data['freeze_money'] = bcsub($MM['money_freeze'] , $yamoney,2);

                $data['yubi'] = bcadd($MM['yubi'] , $bamoney,2);
                $data['freeze_yubi'] = bcsub($MM['yubi_freeze'] , $bamoney,2);
                $data['yongjin'] =$MM['yongjin'];

            }
            break;
        case 4 :
        case 5 :
        case 8 :
        case 12 :
        case 19 :
        case 24 :
       
            $data['affect_money'] = $amoney;
            $data['account_money'] = bcadd($MM['account_money'] , $amoney,2);
            $data['collect_money'] = $MM['money_collect'];
            $data['freeze_money'] = bcsub($MM['money_freeze'] , $amoney,2);

            $data['yubi'] =$MM['yubi'];
            $data['freeze_yubi'] =$MM['yubi_freeze'];
            $data['yongjin'] =$MM['yongjin'];
            break;
        case 17 :
        case 18 :
        case 20 :
        case 21 :
            $data['affect_money'] = $amoney;
            $data['account_money'] = bcadd($MM['account_money'] , $amoney,2);
            $data['collect_money'] = $MM['money_collect'];
            $data['freeze_money'] = $MM['money_freeze'];

            $data['yubi'] =$MM['yubi'];
            $data['freeze_yubi'] =$MM['yubi_freeze'];
            $data['yongjin'] =$MM['yongjin'];

            break;
        case 221 :
            $data['affect_money'] = $amoney;
            $data['account_money'] = bcadd($MM['account_money'] , $amoney,2);
            $data['collect_money'] = $MM['money_collect'];
            $data['freeze_money'] = $MM['money_freeze'];

            $data['yubi'] =$MM['yubi'];
            $data['freeze_yubi'] =$MM['yubi_freeze'];
            $data['yongjin'] =$MM['yongjin'];

            break;
        case 9 :
        case 10 :
            $data['affect_money'] = $amoney;
            $data['account_money'] = bcadd($MM['account_money'] , $amoney,2);
            $data['collect_money'] = bcsub($MM['money_collect'] , $amoney,2);
            $data['freeze_money'] = $MM['money_freeze'];

            $data['yubi'] =$MM['yubi'];
            $data['freeze_yubi'] =$MM['yubi_freeze'];
            $data['yongjin'] =$MM['yongjin'];
            break;
        case 15 :
            $data['affect_money'] = $amoney;
            $data['account_money'] = $MM['account_money'];

            $data['collect_money'] = bcadd($MM['money_collect'] , $amoney,2);
            $data['freeze_money'] = bcsub($MM['money_freeze'] , ($amoney-$yubi),2);


            $data['freeze_yubi'] = bcsub($MM['yubi_freeze'] , $yubi,2);
            $data['yubi'] = $MM['yubi'];
            $data['yongjin'] =$MM['yongjin'];
//            $yamoney=$amoney+$MM["yubi"];
//            $bamoney=-$MM["yubi"];
//            $data['affect_money'] = $amoney;
//            $data['account_money'] = bcadd($MM['account_money'] , $yamoney,2);
//            $data['collect_money'] = $MM['money_collect'];
//            $data['freeze_money'] = bcsub($MM['money_freeze'] , $yamoney,2);
//
//            $data['yubi'] = bcadd($MM['yubi'] , $bamoney,2);
//            $data['freeze_yubi'] = bcsub($MM['yubi_freeze'] , $bamoney,2);

            break;
        case 28 :
        case 73 :
            $data['affect_money'] = $amoney;
            $data['account_money'] = $MM['account_money'];            
            $data['collect_money'] = bcadd($MM['money_collect'] , $amoney,2);
            $data['freeze_money'] = $MM['money_freeze'];


            $data['yubi'] =$MM['yubi'];
            $data['freeze_yubi'] =$MM['yubi_freeze'];
            $data['yongjin'] =$MM['yongjin'];
            break;
        case 29 :
        case 72 :
        case 223:
            $data['affect_money'] = $amoney;
            $data['account_money'] = $MM['account_money'];
            $data['collect_money'] = $MM['money_collect'];            
            $data['freeze_money'] = bcadd($MM['money_freeze'] , $amoney,2);

            $data['yubi'] =$MM['yubi'];
            $data['freeze_yubi'] =$MM['yubi_freeze'];
            $data['yongjin'] =$MM['yongjin'];
            break;
        //加息券
        case 120:
            $data['affect_money']     = $amoney;
            $data['account_money']    = bcadd($MM['account_money'] , $amoney,2);
            $data['collect_money']    = $MM['money_collect'];
            $data['freeze_money']     = $MM['money_freeze'];
            $data['experience_money'] = $MM['money_experience'];

            $data['yubi'] =$MM['yubi'];
            $data['freeze_yubi'] =$MM['yubi_freeze'];
            $data['yongjin'] =$MM['yongjin'];
            break;
         //体验金部分-开始
        //体验金发放
        case 100:
            $data['affect_money']     = $amoney;
            $data['account_money']    = $MM['account_money'];
            $data['collect_money']    = $MM['money_collect'];
            $data['freeze_money']     = $MM['money_freeze'];
            $data['experience_money'] = $MM['money_experience'] + $amoney;


            $data['yubi'] =$MM['yubi'];
            $data['freeze_yubi'] =$MM['yubi_freeze'];
            $data['yongjin'] =$MM['yongjin'];
            break;
        //体验金回收
        case 101:
            $data['affect_money']     = $amoney;
            $data['account_money']    = $MM['account_money'];
            $data['collect_money']    = $MM['money_collect'];
            $data['freeze_money']     = $MM['money_freeze'];
            $data['experience_money'] = bcsub($MM['money_experience'], $amoney, 2);


            $data['yubi'] =$MM['yubi'];
            $data['freeze_yubi'] =$MM['yubi_freeze'];
            $data['yongjin'] =$MM['yongjin'];
            break;
        //体验金投标冻结
        case 102:
            $data['affect_money']     = $amoney;
            $data['account_money']    = $MM['account_money'];
            $data['collect_money']    = $MM['money_collect'];
            $data['freeze_money']     = $MM['money_freeze'] - $amoney;
            $data['experience_money'] = $MM['money_experience'] + $amoney;

            $data['yubi'] =$MM['yubi'];
            $data['freeze_yubi'] =$MM['yubi_freeze'];
            $data['yongjin'] =$MM['yongjin'];
            break;
        //体验金还款（本金）
        case 104:
            $data['affect_money']     = $amoney;
            $data['account_money']    = $MM['account_money'];
            $data['collect_money']    = $MM['money_collect'] - $amoney;
            $data['freeze_money']     = $MM['money_freeze'];
            // $data['experience_money'] = $MM['money_experience'] + $amoney;
            $data['experience_money'] = $MM['money_experience'];

            $data['yubi'] =$MM['yubi'];
            $data['freeze_yubi'] =$MM['yubi_freeze'];
            $data['yongjin'] =$MM['yongjin'];
            break;
        //体验金部分-结束
        //红包 - 开始
        case 110:
            $data['affect_money']     = $amoney;
            $data['account_money']    = $MM['account_money'];
            $data['collect_money']    = $MM['money_collect'];
            $data['freeze_money']     = $MM['money_freeze'] - $amoney;
            $data['experience_money'] = $MM['money_experience'];
     
            $data['yubi'] =$MM['yubi'];
            $data['freeze_yubi'] =$MM['yubi_freeze'];
            $data['yongjin'] =$MM['yongjin'];
            break;
        //申请生成收益
        case 121:
            $data['affect_money']     = $amoney;
            $data['account_money']    = bcadd($MM['account_money'] , $amoney,2);// $MM['account_money']+$amoney;
            $data['collect_money']    = $MM['money_collect'];
            $data['freeze_money']     = bcsub($MM['money_freeze'] , $amoney,2);//$MM['money_freeze'] - $amoney;
            $data['experience_money'] = $MM['money_experience'];
            $data['yubi'] =$MM['yubi'];
            $data['freeze_yubi'] =$MM['yubi_freeze'];
            $data['yongjin'] =$MM['yongjin'];
            break;
        //删除生成收益
        case 122:
            $data['affect_money']     = $amoney;
            $data['account_money']    = bcsub($MM['account_money'] , $amoney,2);//$MM['account_money']-$amoney;
            $data['collect_money']    = $MM['money_collect'];
            $data['freeze_money']     = bcadd($MM['money_freeze'] , $amoney,2);//$MM['money_freeze'] + $amoney;
            $data['experience_money'] = $MM['money_experience'];

            $data['yubi'] =$MM['yubi'];
            $data['freeze_yubi'] =$MM['yubi_freeze'];
            $data['yongjin'] =$MM['yongjin'];
            break;
        //充值鱼币审核
        case 27:
        case 3 :
        case 321 :
            $data['affect_money']     = $amoney;
            $data['yubi']    =  bcadd($MM['yubi'] , $amoney,2);
            $data['freeze_yubi']=$MM['yubi_freeze'];
            $data['account_money'] = $MM['account_money'];
            $data['collect_money'] = $MM['money_collect'];
            $data['freeze_money'] = $MM['money_freeze'];
            $data['yongjin'] =$MM['yongjin'];
            break;
        //红包 - 结束
         case 222 :        
            if(($yubi+ $amoney)>=0){
                $data['affect_money'] = $amoney;
                $data['yubi'] = bcadd($MM['yubi'] , $amoney,2);
                $data['collect_money'] = $MM['money_collect'];
                $data['freeze_yubi'] = bcsub($MM['yubi_freeze'] , $amoney,2);
                $data['account_money'] = $MM['account_money'];
                $data['freeze_money'] =$MM['money_freeze'];
                $data['yongjin'] =$MM['yongjin'];
            }else{
                $yamoney=bcadd($amoney,$yubi,2);//$amoney+$yubi;
                $bamoney=-$yubi;
                $data['affect_money'] = $amoney;
                $data['account_money'] = bcadd($MM['account_money'] , $yamoney,2);
                $data['collect_money'] = $MM['money_collect'];
                $data['freeze_money'] = bcsub($MM['money_freeze'] , $yamoney,2);
                $data['yubi'] = bcadd($yubi , $bamoney,2);
                $data['freeze_yubi'] = bcsub($MM['yubi_freeze'] , $bamoney,2);
                $data['yongjin'] =$MM['yongjin'];
            }

            break;    
        case 71 :
            $data['affect_money'] = $amoney;
            $data['account_money'] = bcadd($MM['account_money'] , $amoney,2);
            $data['collect_money'] = $MM['money_collect'];
            $data['freeze_money'] = $MM['money_freeze'];
            $data['freeze_yubi'] = $MM['yubi_freeze'];
            $data['yubi'] = $MM['yubi'];
            $data['yongjin'] =$MM['yongjin'];
            break;
        case 733 :
            $data['affect_money'] = $amoney;
            $data['account_money'] = $MM['account_money'];
            $data['collect_money'] = $MM['money_collect'];
            $data['freeze_money'] = $MM['money_freeze'];
            $data['freeze_yubi'] = $MM['yubi_freeze'];
            $data['yubi'] =bcadd($MM['yubi'] , $amoney,2);  //$MM['yubi'];
            $data['yongjin'] =$MM['yongjin'];
            break;  
        //成团发放
        case 301:
            $data['affect_money']     = $amoney;
            $data['account_money']    = bcadd($MM['account_money'] , $amoney,2);
            $data['collect_money']    = $MM['money_collect'];
            $data['freeze_money']     = $MM['money_freeze'];
            $data['experience_money'] = $MM['money_experience'];
     
            $data['yubi'] =$MM['yubi'];
            $data['freeze_yubi'] =$MM['yubi_freeze'];
            $data['yongjin'] =$MM['yongjin'];
            break;
        //增加人数发放
        case 302:
            $data['affect_money']     = $amoney;
            $data['account_money']    = bcadd($MM['account_money'] , $amoney,2);
            $data['collect_money']    = $MM['money_collect'];
            $data['freeze_money']     = $MM['money_freeze'];
            $data['experience_money'] = $MM['money_experience'];
            $data['laiyuan']=2;

            $data['yubi'] =$MM['yubi'];
            $data['freeze_yubi'] =$MM['yubi_freeze'];
            $data['yongjin'] =$MM['yongjin'];
            break;
            //小程序加佣金
        case 303:
            $data['affect_money']     = $amoney;
            $data['account_money']    = $MM['account_money'];
            $data['collect_money']    = $MM['money_collect'];
            $data['freeze_money']     = $MM['money_freeze'];
            $data['experience_money'] = $MM['money_experience'];
            $data['laiyuan']=2;

            $data['yubi'] =$MM['yubi'];
            $data['freeze_yubi'] =$MM['yubi_freeze'];
            $data['yongjin'] = bcadd($MM['yongjin'] , $amoney,2);
            break;
            //小程序扣除佣金
        case 304:
            $data['affect_money']     = $amoney;
            $data['account_money']    = $MM['account_money'];//bcadd($MM['account_money'] , $amoney,2);
            $data['collect_money']    = $MM['money_collect'];
            $data['freeze_money']     = $MM['money_freeze'];
            $data['experience_money'] = $MM['money_experience'];
            $data['laiyuan']=2;
        
            $data['yubi'] =$MM['yubi'];
            $data['freeze_yubi'] =$MM['yubi_freeze'];
            $data['yongjin'] =bcsub($MM['yongjin'] , $amoney,2);
            break;
        //赠品折现
        case 310:
            $data['affect_money']     = $amoney;
            $data['account_money']    = bcadd($MM['account_money'] , $amoney,2);//bcadd($MM['account_money'] , $amoney,2);
            $data['collect_money']    = $MM['money_collect'];
            $data['freeze_money']     = $MM['money_freeze'];
            $data['experience_money'] = $MM['money_experience'];

            $data['yubi'] =$MM['yubi'];
            $data['freeze_yubi'] =$MM['yubi_freeze'];
            $data['yongjin'] =$MM['yongjin'];
            break;
        //赠品折现冻结
        case 311:
            $data['affect_money']     = $amoney;
            $data['account_money']    = $MM['account_money'];//bcadd($MM['account_money'] , $amoney,2);
            $data['collect_money']    = $MM['money_collect'];
            $data['freeze_money']     = bcadd($MM['money_freeze'] , $amoney,2);
            $data['experience_money'] = $MM['money_experience'];

            $data['yubi'] =$MM['yubi'];
            $data['freeze_yubi'] =$MM['yubi_freeze'];
            $data['yongjin'] =$MM['yongjin'];
            break;
        //赠品折现解冻
        case 312:
            $data['affect_money']     = $amoney;
            $data['account_money']    = bcadd($MM['account_money'] , $amoney,2);//bcadd($MM['account_money'] , $amoney,2);
            $data['collect_money']    = $MM['money_collect'];
            $data['freeze_money']     = bcsub($MM['money_freeze'] , $amoney,2);
            $data['experience_money'] = $MM['money_experience'];

            $data['yubi'] =$MM['yubi'];
            $data['freeze_yubi'] =$MM['yubi_freeze'];
            $data['yongjin'] =$MM['yongjin'];
            break;
        //预购商品扣款加代收
        case 313 :
            if(($MM['yubi']+ $amoney)>=0){
                $data['affect_money'] = $amoney;
                $data['yubi'] = bcadd($MM['yubi'] , $amoney,2);
                $data['collect_money'] =bcsub($MM['money_collect'] , $amoney,2);
                $data['freeze_yubi'] =$MM['yubi_freeze'];

                $data['account_money'] = $MM['account_money'];
                $data['freeze_money'] =$MM['money_freeze'];
                $data['yongjin'] =$MM['yongjin'];
            }else{
                $yamoney=$amoney+$MM["yubi"];
                $bamoney=-$MM["yubi"];
                $data['affect_money'] = $amoney;
                $data['account_money'] = bcadd($MM['account_money'] , $yamoney,2);
                $data['collect_money'] =bcsub($MM['money_collect'] , $amoney,2);

                $data['freeze_money'] =$MM['money_freeze'];

                $data['yubi'] = bcadd($MM['yubi'] , $bamoney,2);

                $data['freeze_yubi'] = $MM['yubi_freeze'];

                $data['yongjin'] =$MM['yongjin'];

            }
            break;
        //项目方回款扣除金额
        case 314 :
            if(($MM['yubi']+ $amoney)>=0){
                $data['affect_money'] = $amoney;
                $data['yubi'] = bcadd($MM['yubi'] , $amoney,2);
                $data['collect_money'] =$MM['money_collect'];//bcsub($MM['money_collect'] , $amoney,2);
                $data['freeze_yubi'] =$MM['yubi_freeze'];
                $data['account_money'] = $MM['account_money'];
                $data['freeze_money'] =$MM['money_freeze'];
                $data['yongjin'] =$MM['yongjin'];
            }else{
                $yamoney=$amoney+$MM["yubi"];
                $bamoney=-$MM["yubi"];
                $data['affect_money'] = $amoney;
                $data['account_money'] = bcadd($MM['account_money'] , $yamoney,2);
                $data['collect_money'] =$MM['money_collect']; //bcsub($MM['money_collect'] , $amoney,2);
                $data['freeze_money'] =$MM['money_freeze'];
                $data['yubi'] = bcadd($MM['yubi'] , $bamoney,2);
                $data['freeze_yubi'] = $MM['yubi_freeze'];
                $data['yongjin'] =$MM['yongjin'];
            }
            break;
        //还款增余额减代收
        case 315:
            $data['affect_money']     = $amoney;
            $data['account_money']    =  bcadd($MM['account_money'] ,$amoney,2);//bcadd($MM['account_money'] , $amoney,2);
            $data['collect_money']    =  bcadd($MM['money_collect'] , $benjin,2);
            $data['freeze_money']     = $MM['money_freeze'];
            $data['experience_money'] = $MM['money_experience'];

            $data['yubi'] =$MM['yubi'];
            $data['freeze_yubi'] =$MM['yubi_freeze'];
            $data['yongjin'] =$MM['yongjin'];
            break;
        case 316 :
            $data['affect_money'] = $amoney;
            $data['account_money'] = bcadd($MM['account_money'] , $amoney,2);
            $data['collect_money'] = $MM['money_collect'];
            $data['freeze_money'] = $MM['money_freeze'];
            $data['yubi'] =$MM['yubi'];
            $data['freeze_yubi'] =$MM['yubi_freeze'];
            $data['yongjin'] =$MM['yongjin'];

            break;
        //支付宝支付成功增加代收
        case 317 :
        case 327 :
            $data['affect_money'] = $amoney;
            $data['yubi'] = $MM['yubi'];
            $data['collect_money'] =bcadd($MM['money_collect'] , $amoney,2);
            $data['freeze_yubi'] =$MM['yubi_freeze'];

            $data['account_money'] = $MM['account_money'];
            $data['freeze_money'] =$MM['money_freeze'];
            $data['yongjin'] =$MM['yongjin'];
            break;
        //支付宝支付成功增加代收红包部分
        case 318 :
            $data['affect_money'] = $amoney;
            $data['yubi'] = $MM['yubi'];
            $data['collect_money'] =bcadd($MM['money_collect'] , $amoney,2);
            $data['freeze_yubi'] =$MM['yubi_freeze'];

            $data['account_money'] = $MM['account_money'];
            $data['freeze_money'] =$MM['money_freeze'];
            $data['yongjin'] =$MM['yongjin'];
            break;
        //扣除鱼币余额部分
        case 319 :
            if(($MM['yubi']+ $amoney)>=0){
                $data['affect_money'] = $amoney;
                $data['yubi'] = bcadd($MM['yubi'] , $amoney,2);
                $data['collect_money'] = $MM['money_collect'];
                $data['freeze_yubi'] =$MM['yubi_freeze'];

                $data['account_money'] = $MM['account_money'];
                $data['freeze_money'] =$MM['money_freeze'];
                $data['yongjin'] =$MM['yongjin'];
            }else{
                $yamoney=$amoney+$MM["yubi"];
                $bamoney=-$MM["yubi"];
                $data['affect_money'] = $amoney;
                $data['account_money'] = bcadd($MM['account_money'] , $yamoney,2);
                $data['collect_money'] = $MM['money_collect'];
                $data['freeze_money'] =$MM['money_freeze'];

                $data['yubi'] = bcadd($MM['yubi'] , $bamoney,2);
                $data['freeze_yubi'] = $MM['yubi_freeze'] ;
                $data['yongjin'] =$MM['yongjin'];

            }
            break;
            //购买成功增余额
        case 320 :
            $data['affect_money'] = $amoney;
            $data['account_money'] = bcadd($MM['account_money'] , $amoney,2);
            $data['collect_money'] = $MM['money_collect'];
            $data['freeze_money'] = $MM['money_freeze'];
            $data['yubi'] =$MM['yubi'];
            $data['freeze_yubi'] =$MM['yubi_freeze'];
            $data['yongjin'] =$MM['yongjin'];
            break;

        default :
            $data['affect_money'] = $amoney;
            $data['account_money'] = bcadd($MM['account_money'] , $amoney,2);
            $data['collect_money'] = $MM['money_collect'];
            $data['freeze_money'] = $MM['money_freeze'];

            $data['yubi'] =$MM['yubi'];
            $data['freeze_yubi'] =$MM['yubi_freeze'];
            $data['yongjin'] =$MM['yongjin'];
            break;
    }
    if (!isset($data['experience_money'])) {$data['experience_money'] = $MM['money_experience'] ? $MM['money_experience'] : '0.00'; }
    if($type==303&&$type==304){
         $mmoney['laiyan'] = 2;
    }
    $newid = M("member_moneylog")->add($data);
    //var_dump($data);
    $mmoney['money_freeze'] = $data['freeze_money'];
    $mmoney['money_collect'] = $data['collect_money'];
    $mmoney['account_money'] = $data['account_money'];
    $mmoney['money_experience'] = $data['experience_money'];

    $mmoney['yubi'] = $data['yubi'];
    $mmoney['yubi_freeze'] =$data['freeze_yubi'];
    $mmoney['yongjin'] =$data['yongjin'];


    writeLog('**********************************');
    writeLog(M("member_moneylog")->getlastsql());
//    writeLog($newid);
//    writeLog($mmoney);
   writeLog($type);
    writeLog('**********************************');
    if ($newid) {
        $xid = m("member_money")->where("uid={$uid}")->save($mmoney);
    }
    if ($xid) {
        if($shiwu){
            $Moneylog->commit();
        }
        $done = true;
    } else {
        if($shiwu){
            $Moneylog->rollback();
        }
        $done = false;
    }
    return $done;
}
function memberLimitLog($uid, $type, $alimit, $info = "")
{
    $xva = floatval($alimit);
    if (empty($xva)) {
        return true;
    }
    $done = false;
    $MM = m("member_money")->field("money_freeze,money_collect,account_money", true)->find($uid);
    if (!is_array($MM)) {
        m("member_money")->add(array(
            "uid" => $uid
        ));
    }
    $Moneylog = d("member_moneylog");
    if (in_array($type, array("71", "72", "73", "733"))) {
        $type_save = 7;
    } else {
        $type_save = $type;
    }
    $Moneylog->startTrans();
    $data['uid'] = $uid;
    $data['type'] = $type_save;
    $data['info'] = $info;
    $data['add_time'] = time();
    $data['add_ip'] = get_client_ip();
    $data['credit_limit'] = 0;
    $data['borrow_vouch_limit'] = 0;
    $data['invest_vouch_limit'] = 0;
    switch ($type) {
        case 1 :
        case 4 :
        case 7 :
        case 12 :
            $_data['credit_limit'] = $alimit;
            break;
        case 2 :
        case 5 :
        case 8 :
            $_data['borrow_vouch_limit'] = $alimit;
            break;
        case 3 :
        case 6 :
        case 9 :
        case 10 :
            $_data['invest_vouch_limit'] = $alimit;
            break;
        case 11 :
            $_data['credit_limit'] = $alimit;
            $mmoney['credit_limit'] = $MM['credit_limit'] + $_data['credit_limit'];
            break;
    }
    $data = array_merge($data, $_data);
    $newid = m("member_limitlog")->add($data);
    $mmoney['credit_cuse'] = $MM['credit_cuse'] + $data['credit_limit'];
    $mmoney['borrow_vouch_cuse'] = $MM['borrow_vouch_cuse'] + $data['borrow_vouch_limit'];
    $mmoney['invest_vouch_cuse'] = $MM['invest_vouch_cuse'] + $data['invest_vouch_limit'];
    if ($newid) {
        $xid = m("member_money")->where("uid={$uid}")->save($mmoney);
    }
    if ($xid) {
        $Moneylog->commit();
        $done = true;
    } else {
        $Moneylog->rollback();
    }
    return $done;
}
function memberCreditsLog($uid, $type, $acredits, $info = "", $id = "",$sw=true)
{
    if ($acredits == 0) {
        return true;
    }
    $done = false;
    $mCredits = m("members")->getFieldById($uid, "credits");
    $Creditslog = d("member_creditslog");
    $Creditslog->startTrans();
    $data['uid'] = $uid;
    $data['type'] = $type;
    $data['affect_credits'] = $acredits;
    $data['account_credits'] = $mCredits + $acredits;
    $data['info'] = $info;
    $data['add_time'] = time();
    $data['add_ip'] = get_client_ip();
    $newid = $Creditslog->add($data);
    if($data['account_credits']>=0){
        $xid = m("members")->where("id={$uid}")->setField("credits", $data['account_credits']);
    }
    if($sw){
        if ($xid) {
            $Creditslog->commit();
            $done = true;
        } else {
            $Creditslog->rollback();
        }
    }else{
        $done = true;
    }
    return $done;
}
function getMemberMoneySummary($uid)
{
    $pre = c("DB_PREFIX");
    $umoney = m("member_money")->field(true)->find($uid);
    $withdraw = m("member_withdraw")->field("withdraw_status,sum(withdraw_money) as withdraw_money,sum(withdraw_fee) as withdraw_fee,sum(second_fee) as second_fee")->where("uid={$uid}")->group("withdraw_status")->select();
    $withdraw_row = array();
    foreach ($withdraw as $wkey => $wv) {
        $withdraw_row[$wv['withdraw_status']] = $wv;
    }
    $withdraw0 = $withdraw_row[0];
    $withdraw1 = $withdraw_row[1];
    $withdraw2 = $withdraw_row[2];
    $payonline = m("member_payonline")->where("uid={$uid} AND status=1")->sum("money");
    $commission1 = m("borrow_investor")->where("investor_uid={$uid}")->sum("paid_fee");
    $commissionreward_money = m("borrow_investor")->where("investor_uid={$uid}")->sum("reward_money");
    $commissionzftbjl = m("borrow_investor")->where("borrow_uid={$uid}")->sum("reward_money");
    $commission2 = m("borrow_info")->where("borrow_uid={$uid} AND borrow_status in(3,4)")->sum("borrow_fee");
    $uplevefee = m("member_moneylog")->where("uid={$uid} AND type=2")->sum("affect_money");
    $ljtgjl = m("member_moneylog")->where("uid={$uid} AND type=13")->sum("affect_money");
    $czfee = m("member_payonline")->where("uid={$uid} AND status=1")->sum("fee");
    $capitalinfo = getmemberborrowscan($uid);
    $money['zye'] = $umoney['account_money'] + $umoney['money_collect'] + $umoney['money_freeze'];
    $money['kyxjje'] = $umoney['account_money'];
    $money['djje'] = $umoney['money_freeze'];
    $money['jjje'] = 0;
    $money['dsbx'] = $umoney['money_collect'];
    $money['dfbx'] = $capitalinfo['tj']['dhze'];
    $money['dxrtb'] = $capitalinfo['tj']['dqrtb'];
    $money['dshtx'] = $withdraw0['withdraw_money'];
    $money['clztx'] = $withdraw1['withdraw_money'];
    $money['total_1'] = $money['kyxjje'] + $money['jjje'] + $money['dsbx'] - $money['dfbx'] + $money['dxrtb'] + $money['dshtx'] + $money['clztx'];
    $money['jzlx'] = $capitalinfo['tj']['earnInterest'];
    
    $minfo =getMinfo($uid,true);
    $levename=getLeveName($minfo["credits"]);
    $levetixian=getLeveTixian($minfo["credits"]);
    $money['lxgl'] = $capitalinfo['tj']['earnInterest']*$levetixian/100;
    
    $money['ljtgjl'] = $ljtgjl;
    $money['zftbjl'] = $commissionzftbjl;
    $money['ljtbjl'] = $commissionreward_money;
    
    $money['jflx'] = $capitalinfo['tj']['payInterest'];
    $money['ljjj'] = $umoney['reward_money'];
    $money['ljhyf'] = $uplevefee;
    $money['ljtxsxf'] = $withdraw2['withdraw_fee']+$withdraw2['second_fee'];
    $money['ljczsxf'] = $czfee;
    $money['total_2'] = $money['jzlx'] - $money['jflx'] + $money['ljjj'] - $money['ljhyf'] - $money['ljtxsxf'] - $money['ljczsxf'];
    $money['ljtzje'] = $capitalinfo['tj']['borrowOut'];
    $money['ljjrje'] = $capitalinfo['tj']['borrowIn'];
    $money['ljczje'] = $payonline;
    $money['ljtxje'] = $withdraw2['withdraw_money'];
    $money['ljzfyj'] = $commission1 + $commission2;
    $money['dslxze'] = $capitalinfo['tj']['willgetInterest'];
    $money['ysbj'] = $capitalinfo['tj']['ysbj'];
    $money['dflxze'] = $capitalinfo['tj']['willpayInterest'];
    return $money;
}
function getBorrowInvest($borrowid = 0, $uid)
{
    if (empty($borrowid)) {
        return;
    }
    $vx = m("borrow_info")->field("id,second_verify_time")->where("id={$borrowid} AND borrow_uid={$uid}")->find();
    if (!is_array($vx)) {
        return;
    }
    $binfo = m("borrow_info")->field("borrow_name,borrow_uid,borrow_type,borrow_duration,borrow_money,borrow_interest,repayment_type,has_pay,total,deadline")->find($borrowid);
    $list = array();
    switch ($binfo['repayment_type']) {
        case 1 :
            $field = "borrow_id,sort_order,sum(capital) as capital,sum(interest) as interest,status,sum(receive_interest+receive_capital+if(receive_capital>0,interest_fee,0)) as paid,deadline";
            $vo = m("investor_detail")->field($field)->where("borrow_id={$borrowid} AND `sort_order`=1")->group("sort_order")->find();
            $status_arr = array("还未还", "已还完", "已提前还款", "愈期还款", "网站代还本金");
            $vo['status'] = $status_arr[$vo['status']];
            $vo['needpay'] = getfloatvalue(sprintf("%.2f", $vo['interest'] + $vo['capital'] - $vo['paid']), 2);
            $list[] = $vo;
            break;
        case 5:
            $field = "borrow_id,sort_order,sum(capital) as capital,sum(interest) as interest,status,sum(receive_interest+receive_capital+if(receive_capital>0,interest_fee,0)) as paid,deadline";
            $vo = m("investor_detail")->field($field)->where("borrow_id={$borrowid} AND `sort_order`=1")->group("sort_order")->find();
            $status_arr = array("还未还", "已还完", "已提前还款", "愈期还款", "网站代还本金");
            $vo['status'] = $status_arr[$vo['status']];
            $vo['needpay'] = getfloatvalue(sprintf("%.2f", $vo['interest'] + $vo['capital'] - $vo['paid']), 2);
            $list[] = $vo;
            break;
        default :
            $i = 1;
            $all_capital=0;
            $all_interest=0;
            for (; $i <= $binfo['borrow_duration']; ++$i) {
               $field = "borrow_id,total,sort_order,sum(capital) as capital,sum(interest) as interest,status,sum(receive_interest+receive_capital+if(receive_capital>0,interest_fee,0)) as paid,deadline";
                $vo = m("investor_detail")->field($field)->where("borrow_id={$borrowid} AND `sort_order`={$i}")->group("sort_order")->find();
                $status_arr = array("还未还", "已还完", "已提前还款", "愈期还款", "网站代还本金");
                // $bigtime=$vo['deadline'];
                // $vo['deadline']= $vo['deadline']-(($vo["total"]-$i)*30*86400);
                // $vo['deadline']=substr(date("Y-m-d",$bigtime),-2)==date("d",$vo['deadline'])?$vo['deadline']:strtotime(date("Y-m",$vo['deadline'])."-".substr(date("Y-m-d",$bigtime),-2));
                $endTime = strtotime(date("Y-m-d", $vx['second_verify_time']) . " 23:59:59");
                $vo['deadline'] = strtotime("+{$i} month", $endTime);
                $vo['status']=$vo['status']==7?0:$vo['status'];
                $vo['status'] = $status_arr[$vo['status']];
                $all_capital+=$vo['capital'];
                $all_interest+=$vo['interest'];
                if($binfo['borrow_duration']==$i){
                    if(bcsub($all_capital,$binfo["borrow_money"],2)!=0){
                        $all_capital=bcsub($all_capital,$vo['capital'],2);
                        $vo['capital']=bcsub($binfo["borrow_money"],$all_capital,2);
                    }
                    if(bcsub($all_interest,$binfo["borrow_interest"],2)!=0){
                        $all_interest=bcsub($all_interest,$vo['interest'],2);
                        $vo['interest']=bcsub($binfo["borrow_interest"],$all_interest,2);;
                    }
                }
                $vo['needpay'] = getfloatvalue(sprintf("%.2f", $vo['interest'] + $vo['capital'] - $vo['paid']), 2);
            
                $list[] = $vo;
            }
    }
    $row = array();
    $row['list'] = $list;
    $row['name'] = $binfo['borrow_name'];
    return $row;
}
function getDurationCount($uid = 0)
{
    if (empty($uid)) {
        return;
    }
    $pre = c("DB_PREFIX");
    $field = "d.status,d.repayment_time";
    $sql = "select {$field} from {$pre}investor_detail d left join {$pre}borrow_info b ON b.id=d.borrow_id where d.borrow_id in(select tb.id from {$pre}borrow_info tb where tb.borrow_uid={$uid}) group by d.borrow_id, d.sort_order";
    $list = m()->query($sql);
    $week_1 = array(
        strtotime("-7 day", strtotime(date("Y-m-d", time()) . " 00:00:00")),
        strtotime(date("Y-m-d", time()) . " 23:59:59")
    );
    $time_1 = array(
        strtotime("-1 month", strtotime(date("Y-m-d", time()) . " 00:00:00")),
        strtotime(date("Y-m-d", time()) . " 23:59:59")
    );
    $time_6 = array(
        strtotime("-6 month", strtotime(date("Y-m-d", time()) . " 00:00:00")),
        strtotime(date("Y-m-d", time()) . " 23:59:59")
    );
    $row_time_1 = array();
    $row_time_2 = array();
    $row_time_3 = array();
    $row_time_4 = array();
    foreach ($list as $v) {
        switch ($v['status']) {
            case 1 :
                if ($time_6[0] < $v['repayment_time'] && $v['repayment_time'] < $time_6[1]) {
                    $row_time_3['zc'] = $row_time_3['zc'] + 1;
                    if ($week_1[0] < $v['repayment_time'] && $v['repayment_time'] < $week_1[1]) {
                        $row_time_1['zc'] = $row_time_1['zc'] + 1;
                    }
                    if ($time_1[0] < $v['repayment_time'] && $v['repayment_time'] < $time_1[1]) {
                        $row_time_2['zc'] = $row_time_2['zc'] + 1;
                    }
                }
                $row_time_4['zc'] = $row_time_4['zc'] + 1;
                break;
            case 2 :
                if ($time_6[0] < $v['repayment_time'] && $v['repayment_time'] < $time_6[1]) {
                    $row_time_3['tq'] = $row_time_3['tq'] + 1;
                    if ($week_1[0] < $v['repayment_time'] && $v['repayment_time'] < $week_1[1]) {
                        $row_time_1['tq'] = $row_time_1['tq'] + 1;
                    }
                    if ($time_1[0] < $v['repayment_time'] && $v['repayment_time'] < $time_1[1]) {
                        $row_time_2['tq'] = $row_time_2['tq'] + 1;
                    }
                }
                $row_time_4['tq'] = $row_time_4['tq'] + 1;
                break;
            case 3 :
                if ($time_6[0] < $v['repayment_time'] && $v['repayment_time'] < $time_6[1]) {
                    $row_time_3['ch'] = $row_time_3['ch'] + 1;
                    if ($week_1[0] < $v['repayment_time'] && $v['repayment_time'] < $week_1[1]) {
                        $row_time_1['ch'] = $row_time_1['ch'] + 1;
                    }
                    if ($time_1[0] < $v['repayment_time'] && $v['repayment_time'] < $time_1[1]) {
                        $row_time_2['ch'] = $row_time_2['ch'] + 1;
                    }
                }
                $row_time_4['ch'] = $row_time_4['ch'] + 1;
                break;
            case 5 :
                if ($time_6[0] < $v['repayment_time'] && $v['repayment_time'] < $time_6[1]) {
                    $row_time_3['yq'] = $row_time_3['yq'] + 1;
                    if ($week_1[0] < $v['repayment_time'] && $v['repayment_time'] < $week_1[1]) {
                        $row_time_1['yq'] = $row_time_1['yq'] + 1;
                    }
                    if ($time_1[0] < $v['repayment_time'] && $v['repayment_time'] < $time_1[1]) {
                        $row_time_2['yq'] = $row_time_2['yq'] + 1;
                    }
                }
                $row_time_4['yq'] = $row_time_4['yq'] + 1;
                break;
            case 6 :
                if ($time_6[0] < $v['repayment_time'] && $v['repayment_time'] < $time_6[1]) {
                    $row_time_3['wh'] = $row_time_3['wh'] + 1;
                    if ($week_1[0] < $v['repayment_time'] && $v['repayment_time'] < $week_1[1]) {
                        $row_time_1['wh'] = $row_time_1['wh'] + 1;
                    }
                    if ($time_1[0] < $v['repayment_time'] && $v['repayment_time'] < $time_1[1]) {
                        $row_time_2['wh'] = $row_time_2['wh'] + 1;
                    }
                }
                $row_time_4['wh'] = $row_time_4['wh'] + 1;
                break;
        }
    }
    $row['history1'] = $row_time_1;
    $row['history2'] = $row_time_2;
    $row['history3'] = $row_time_3;
    $row['history4'] = $row_time_4;
    return $row;
}
function getMemberBorrow($uid = 0, $size = 10)
{
    if (empty($uid)) {
        return;
    }
    $pre = c("DB_PREFIX");
    $field = "b.borrow_name,d.total,d.borrow_id,d.sort_order,sum(d.capital) as capital,sum(d.interest) as interest,d.status,sum(d.receive_interest+d.receive_capital+if(d.receive_capital>0,d.interest_fee,0)) as paid,d.deadline";
    $sql = "select {$field} from {$pre}investor_detail d left join {$pre}borrow_info b ON b.id=d.borrow_id where d.borrow_id in(select tb.id from {$pre}borrow_info tb where tb.borrow_status=6 AND tb.borrow_uid={$uid}) AND d.repayment_time=0 group by d.sort_order, d.borrow_id order by  d.borrow_id,d.sort_order limit 0,10";
    $list = m()->query($sql);
    $status_arr = array("还未还", "已还完", "已提前还款", "愈期还款", "网站代还本金");
    foreach ($list as $key => $v) {
        $list[$key]['status'] = $status_arr[$v['status']];
    }
    $row = array();
    $row['list'] = $list;
    return $row;
}
function getMemberThisBorrow($uid = 0, $size = 10,$id=0)
{
    if (empty($uid)) {
        return;
    }
    $pre = c("DB_PREFIX");
    $vx = m("borrow_info")->field("id,second_verify_time")->where("id={$id} AND borrow_uid={$uid}")->find();
    $field = "b.borrow_name,d.total,d.borrow_id,d.sort_order,sum(d.capital) as capital,sum(d.interest) as interest,d.status,sum(d.receive_interest+d.receive_capital+if(d.receive_capital>0,d.interest_fee,0)) as paid,d.deadline";
   /// $sql = "select {$field} from {$pre}investor_detail d left join {$pre}borrow_info b ON b.id=d.borrow_id where d.borrow_id in(select tb.id from {$pre}borrow_info tb where tb.borrow_status=6 AND tb.borrow_uid={$uid}) AND d.repayment_time=0 and d.borrow_id={$id} group by d.sort_order, d.borrow_id order by  d.borrow_id,d.sort_order limit 0,10";
    $sql = "select {$field} from {$pre}investor_detail d left join {$pre}borrow_info b ON b.id=d.borrow_id where d.borrow_id in(select tb.id from {$pre}borrow_info tb where tb.borrow_status=6 AND tb.borrow_uid={$uid}) and d.borrow_id={$id} group by d.sort_order, d.borrow_id order by  d.borrow_id,d.sort_order asc limit 0,100";
   
    $list = m()->query($sql);
    $status_arr = array("还未还", "已还完", "已提前还款", "愈期还款", "网站代还本金");
    foreach ($list as $key => $v) {
       $bigtime=$list[$key]['deadline'];
        // $list[$key]['deadline']= $v['deadline']-(($v["total"]-$key-1)*30*86400);
        // $list[$key]['deadline']=substr(date("Y-m-d",$bigtime),-2)==date("d",$list[$key]['deadline'])?$list[$key]['deadline']:strtotime(date("Y-m",$list[$key]['deadline'])."-".substr(date("Y-m-d",$bigtime),-2));
        
        $endTime = strtotime(date("Y-m-d", $vx['second_verify_time']) . " 23:59:59");
        $tmp_i = $key+1;
        $list[$key]['deadline'] = strtotime("+{$tmp_i} month", $endTime);
        $v['status']=$v['status']==7?0:$v['status'];
        $list[$key]['status'] = $status_arr[$v['status']];
    }
    $row = array();
    $row['list'] = $list;
    //print_r($row);
    //exit;
    return $row;
}
function getLeftTime($timeend, $type = 1)
{
    if ($type == 1) {
        $timeend = strtotime(date("Y-m-d", $timeend) . " 23:59:59");
        $timenow = strtotime(date("Y-m-d", time()) . " 23:59:59");
        $left = ceil(($timeend - $timenow) / 3600 / 24).'天';
    } else {
        $left_arr = timediff(time(), $timeend);
        $left = $left_arr['day'] . "天" . $left_arr['hour'] . "时" . $left_arr['min'] . "分" . $left_arr['sec'] . "秒";
    }
    return $left;
}
function getLeftTimes($timeend)
{
    if ($timeend >time()) {
        $timeend = strtotime(date("Y-m-d", $timeend) . " 23:59:59");
        $timenow = strtotime(date("Y-m-d", time()) . " 23:59:59");
        $left = ceil(($timeend - $timenow) / 3600 / 24).'天';
    } else {
        $left="0天";
    }
    return $left;
}

 


function timediff($begin_time, $end_time)
{
    if ($begin_time < $end_time) {
        $starttime = $begin_time;
        $endtime = $end_time;
    } else {
        $starttime = $end_time;
        $endtime = $begin_time;
    }
    $timediff = $endtime - $starttime;
    $days = intval($timediff / 86400);
    $remain = $timediff % 86400;
    $hours = intval($remain / 3600);
    $remain %= 3600;
    $mins = intval($remain / 60);
    $secs = $remain % 60;
    $res = array(
        "day" => $days,
        "hour" => $hours,
        "min" => $mins,
        "sec" => $secs
    );
    return $res;
}
function addInnerMsg($uid, $title, $msg)
{
    if (empty($uid)) {
        return;
    }
    $data['uid'] = $uid;
    $data['title'] = $title;
    $data['msg'] = $msg;
    $data['send_time'] = time();
    m("inner_msg")->add($data);
}
function getTypeList($parm)
{
    $Osql = "sort_order DESC";
    $field = "id,type_name,type_set,add_time,type_url,type_nid,parent_id";
    $Lsql = "{$parm['limit']}";
    $pc = d("Acategory")->where("parent_id={$parm['type_id']}")->count("id");
    if (0 < $pc) {
        $map['is_hiden'] = 0;
        $map['parent_id'] = $parm['type_id'];
        $data = d("Acategory")->field($field)->where($map)->order($Osql)->limit($Lsql)->select();
    } else if (!isset($parm['notself'])) {
        $map['is_hiden'] = 0;
        $map['parent_id'] = d("Acategory")->getFieldById($parm['type_id'], "parent_id");
        $data = d("Acategory")->field($field)->where($map)->order($Osql)->limit($Lsql)->select();
    }
    $typefix = get_type_leve_nid($parm['type_id']);
    $typeu = $typefix[0];
    $suffix = c("URL_HTML_SUFFIX");
    foreach ($data as $key => $v) {
        if ($v['type_set'] == 2) {
            if (empty($v['type_url'])) {
                $data[$key]['turl'] = "javascript:alert('请在后台添加此栏目链接');";
            } else {
                $data[$key]['turl'] = $v['type_url'];
            }
        } else if ($parm['type_id'] == 0 || $v['parent_id'] == 0 && count($typefix) == 1) {
            $data[$key]['turl'] = mu("{$v['type_nid']}/index", "typelist", array(
                "suffix" => $suffix
            ));
        } else {
            $data[$key]['turl'] = mu("Home/{$typeu}/{$v['type_nid']}", "typelist", array(
                "suffix" => $suffix
            ));
        }
    }
    $row = array();
    $row = $data;
    return $row;
}
function newTip($borrow_id)
{
    $binfo = m("borrow_info")->field("borrow_type,borrow_interest_rate,borrow_duration")->find();
    if ($binfo['borrow_type'] == 3) {
        $map['borrow_type'] = 3;
    } else {
        $map['borrow_type'] = 0;
    }
    $tiplist = m("borrow_tip")->field(true)->where($map)->select();
    foreach ($tiplist as $key => $v) {
        $minfo = m("members")->field("account_money,user_phone")->find($v['uid']);
        if ($v['interest_rate'] <= $binfo['borrow_interest_rate'] && $v['doration_from'] <= $binfo['borrow_duration'] && $binfo['borrow_duration'] <= $v['doration_to'] && $v['account_money'] <= $minfo['account_money']) {
            empty($tipPhone) ? ($tipPhone .= "{$v['user_phone']}") : ($tipPhone .= ",{$v['user_phone']}");
        }
    }
    $smsTxt = fs("Webconfig/smstxt");
    $smsTxt = de_xie($smsTxt);
    sendsms($tipPhone, $smsTxt['newtip']);
}
function autoInvest($borrow_id)
{
    $binfo = m("borrow_info")->field("borrow_uid,auto_info,p_auto_info,borrow_money,borrow_type,repayment_type,borrow_interest_rate,borrow_duration,has_vouch,has_borrow,borrow_max,borrow_min,can_auto")->find($borrow_id);
    if ($binfo['borrow_type'] == 3) {
        return true;
    }
    if ($binfo['can_auto'] == 0) {
        return true;
    }
    if ($binfo['borrow_type'] == 2 && $binfo['has_vouch'] < $binfo['borrow_money']) {
        return true;
    }
    
    $map['status'] = 1;
    $autolist = m("auto_borrow")->field(true)->where($map)->order("yongdao asc,id asc")->select();
    $needMoney = $binfo['borrow_money'] - $binfo['has_borrow'];
    
    $borrow_user_info = m("members")->field("user_type,credits")->find();
    $have_auto_do = array();
    
    foreach ($autolist as $key => $v) {
        
        if (in_array($v['uid'], $have_auto_do) || $v['uid'] == $binfo['borrow_uid']) {
            continue;
        }
        if ($v['my_friend'] == 1 || $v['not_black'] == 1) {
            $vo = m("member_friend")->where("uid={$v['uid']} AND friend_id={$binfo['borrow_uid']}")->find();
            if ($v['my_friend'] == 1 && $vo['apply_status'] != 1) {
                continue;
            }
            if ($v['not_black'] == 1 && $vo['apply_status'] == 3) {
                continue;
            }
        }
        if (0 < intval($v['target_user']) && intval($v['target_user']) != $borrow_user_info['user_type']) {
            continue;
        }
        if ($v['borrow_credit_status'] == 1 && !($borrow_user_info['credits'] <= $v['borrow_credit_last'] && $v['borrow_credit_first'] <= $borrow_user_info['credits'])) {
            continue;
        }
        if (0 < intval($v['repayment_type']) && $v['repayment_type'] != $binfo['repayment_type']) {
            continue;
        }
        if ($v['timelimit_status'] == 1 && !($binfo['borrow_duration'] <= $v['timelimit_month_last'] && $v['timelimit_month_first'] <= $binfo['borrow_duration'])) {
            continue;
        }
        if ($v['apr_status'] == 1 && !($binfo['borrow_interest_rate'] <= $v['apr_last'] && $v['apr_first'] <= $binfo['borrow_interest_rate'])) {
            continue;
        }
        if (0 < intval($v['borrow_type']) && $v['borrow_type'] != $binfo['borrow_type']) {
            continue;
        }
        if ($v['tender_type'] == 1) {
            $investMoney = $v['tender_max_account'];
        } else if ($v['tender_type'] == 2) {
            $investMoney = $v['tender_rate'] * $binfo['borrow_money'] / 100;
        }else{
            continue;   
        }
        
        $v_binfo=array();
        $vbinfo=array();
        $v_binfo = m("borrow_info")->field("borrow_money,has_borrow")->find($borrow_id);
        $v_auto_money=$v_binfo['borrow_money'];
        if($binfo["auto_info"]!=0){
            $v_auto_money=$v_binfo['borrow_money']*$binfo["auto_info"]/100;
        }
        
        $v_needMoney = $v_auto_money - $v_binfo['has_borrow'];
        
        
        if($v_needMoney<=0)return;
        
        
        
        
        if ($binfo['borrow_max']==0) {
            $vbinfo['borrow_max']=$v_needMoney;
        }
    
         if ($binfo['borrow_max'] ==0 && $v['tender_max_account']==0) {
            $investMoney = $vbinfo['borrow_max'];
        }
        
        //$investMoney=110;
        if($v["tender_account"]!="0.00"){
            if($investMoney<$v["tender_account"]){continue;}
        }
        
        $vminfo = getminfo($v['uid'], "m.user_leve,m.time_limit,mm.account_money");
        
        if($v["bao_account"]!="0.00"){
          if($vminfo['account_money']-$v["bao_account"]<$investMoney){
              if($vminfo['account_money']-$v["bao_account"]>50){
                  $investMoney=$vminfo['account_money']-$v["bao_account"];
              }else{
                  $max_id=M("auto_borrow")->max('yongdao');
                  $sdata["yongdao"]=$max_id+1;
                  M("auto_borrow")->where('uid='.$v["uid"])->save($sdata);
                  continue; 
              }
          }
        }else{
            //echo $v_needMoney;
          if ($vminfo['account_money'] < $investMoney) {
              $investMoney=$vminfo['account_money'];
          }
          if($v_needMoney<$investMoney){
              $investMoney= $v_needMoney;
          }
          
        }
        
        //echo $vbinfo['borrow_max']."----".$investMoney."----".$v['uid']."<br>";
        //echo $vbinfo['borrow_max']."----".$investMoney."----".$v['uid']."<br>";
        if($binfo['borrow_min']>$investMoney){
            $max_id=M("auto_borrow")->max('yongdao');
            $sdata["yongdao"]=$max_id+1;
            M("auto_borrow")->where('uid='.$v["uid"])->save($sdata);
            continue;   
        }
        $investMoney=floor($investMoney);
        if($investMoney==0){
            $max_id=M("auto_borrow")->max('yongdao');
            $sdata["yongdao"]=$max_id+1;
            M("auto_borrow")->where('uid='.$v["uid"])->save($sdata);
            continue;   
        }
        
        if($investMoney<50){
            $max_id=M("auto_borrow")->max('yongdao');
            $sdata["yongdao"]=$max_id+1;
            M("auto_borrow")->where('uid='.$v["uid"])->save($sdata);
            continue;   
        }
        
        
        if($binfo["p_auto_info"]!=0){
            $pv_needMoney=$v_binfo['borrow_money']*$binfo["p_auto_info"]/100;   
            if($pv_needMoney<$investMoney)$investMoney=$pv_needMoney;
        }
        
        if($v["tender_account"]!="0.00"){
            if($investMoney<$v["tender_account"]){
                $max_id=M("auto_borrow")->max('yongdao');
                $sdata["yongdao"]=$max_id+1;
                M("auto_borrow")->where('uid='.$v["uid"])->save($sdata);
                continue;
            }
        }
        
        if($investMoney>$v_needMoney)$investMoney=$v_needMoney;
        
        $x = investmoney($v['uid'], $borrow_id, $investMoney, 1);  
        if ($x === true) {
            $max_id=M("auto_borrow")->max('yongdao');
            if($max_id=="")$max_id=0;
            $sdata["yongdao"]=$max_id+1;
            M("auto_borrow")->where('uid='.$v["uid"])->save($sdata);  //根据会员编号 全部顺延   2014-9-26
            $sdata["sdata"]=array();
            $have_auto_do[] = $v['uid'];
            mtip("chk27", $v['uid'], $borrow_id);
        }
    }
    
}
function getBorrowInterest($type, $money, $duration, $rate)
{
    switch ($type) {
        case 1 :
            $interest=getfloatvalue($rate * $money * $duration/100/365, 2);
            break;
        case 2 :
            $parm['duration'] = $duration;
            $parm['money'] = $money;
            $parm['year_apr'] = $rate;
            $parm['type'] = "all";
            $intre = equalmonth($parm);
            $interest = $intre['repayment_money'] - $money;
            break;
        case 3 :
            $parm['month_times'] = $duration;
            $parm['account'] = $money;
            $parm['year_apr'] = $rate;
            $parm['type'] = "all";
            $intre = equalseason($parm);
            $interest = $intre['interest'];
            break;
        case 4 :
            $parm['month_times'] = $duration;
            $parm['account'] = $money;
            $parm['year_apr'] = $rate;
            $parm['type'] = "all";
            $intre = equalendmonth($parm);
            $interest = $intre['interest'];
            break;
        case 5 :
            $parm['month_times'] = $duration;
            $parm['account'] = $money;
            $parm['year_apr'] = $rate;
            $parm['type'] = "all";
            $intre = EqualEndMonthOnly($parm);
            $interest = $intre['interest'];
            break;
        case 6 :
            $parm['month_times'] = $duration;
            $parm['account'] = $money;
            $parm['year_apr'] = $rate;
            $parm['type'] = "all";
            $intre = EqualYearHalf($parm);
            $interest = $intre['interest'];
            break;
        case 7 :
            $parm['month_times'] = $duration;
            $parm['account'] = $money;
            $parm['year_apr'] = $rate;
            $parm['type'] = "all";
            $intre = EqualYear($parm);
            $interest = $intre['interest'];
            break;
    }
    return $interest;
}
function EqualMonth($data = array())
{
    if (isset($data['money']) && 0 < $data['money']) {
        $account = $data['money'];
    } else {
        return "";
    }
    if (isset($data['year_apr']) && 0 < $data['year_apr']) {
        $year_apr = $data['year_apr'];
    } else {
        return "";
    }
    if (isset($data['duration']) && 0 < $data['duration']) {
        $duration = $data['duration'];
    }
    if (isset($data['borrow_time']) && 0 < $data['borrow_time']) {
        $borrow_time = $data['borrow_time'];
    } else {
        $borrow_time = time();
    }
    $month_apr = $year_apr / 1200;
    $_li = pow(1 + $month_apr, $duration);
    $repayment = round($account * ($month_apr * $_li) / ($_li - 1), 2);
    $_result = array();
    $all_repayment_interest = bcsub($repayment * $duration,$data['money'],2);
    //echo $all_repayment;
    //exit;
    if (isset($data['type']) && $data['type'] == "all") {
        $_result['repayment_money'] = $repayment * $duration;
        $_result['monthly_repayment'] = $repayment;
        $_result['month_apr'] = round($month_apr * 100, 2);
    } else {
        $i = 0;
        for (; $i < $duration; ++$i) {
            if ($i == 0) {
                $interest = round($account * $month_apr, 2);
            } else {
                $_lu = pow(1 + $month_apr, $i);
                $interest = round(($account * $month_apr - $repayment) * $_lu + $repayment, 2);
            }
            
            $_result[$i]['repayment_money'] = getfloatvalue($repayment, 2);
            $_result[$i]['repayment_time'] = get_times(array(
                "time" => $borrow_time,
                "num" => $i + 1
            ));
            
            $_result[$i]['interest'] = getfloatvalue($interest, 2);
            $_result[$i]['capital'] = bcsub($repayment,$interest,2);
            //print_r($_result[$i]['interest']);
            //echo "<br>";
            //echo $account;
            $all_capital += $_result[$i]['capital'];
            $all_interest += $_result[$i]['interest'];
            //判断最后一期            
            if(($i+1)==$duration){
                
                $all_capital= $account;
                //echo $all_interest.'=='.$all_repayment_interest;
                if(bcsub($all_capital,$account,2)!=0){
                        $all_capital=bcsub($all_capital,$_result[$i]['capital'],2);
                        $_result[$i]['capital']=bcsub($account,$all_capital,2);
                }
                if(bcsub($all_interest,$data["investor_interest"],2)!=0){
                        $all_interest=bcsub($all_interest,$_result[$i]['interest'],2);
                        $_result[$i]['interest']=bcsub($all_repayment_interest,$all_interest,2);;
                }
            }
        }
    }
    return $_result;
}
/////////////////////////////////////////一次性还款//////////////////////////////////////
//到期还本，按月付息
function EqualEndMonthOnly($data = array()){
  
  //项目的月数
  if (isset($data['month_times']) && $data['month_times']>0){
      $month_times = $data['month_times'];
  }
  //项目的总金额
  if (isset($data['account']) && $data['account']>0){
      $account = $data['account'];
  }else{
      return "";
  }
  
  //项目的年利率
  if (isset($data['year_apr']) && $data['year_apr']>0){
      $year_apr = $data['year_apr'];
  }else{
      return "";
  }
  
  //项目的时间
  if (isset($data['borrow_time']) && $data['borrow_time']>0){
      $borrow_time = $data['borrow_time'];
  }else{
      $borrow_time = time();
  }
  
  //月利率
  $month_apr = $year_apr/(12*100);
  
  $interest = getFloatValue($account*$month_apr*$month_times,2);//利息等于应还金额*月利率*项目月数
  if (isset($data['type']) && $data['type']=="all"){
      $_resul['repayment_account'] = $account + $interest;
      $_resul['monthly_repayment'] = $interest;
      $_resul['month_apr'] = round($month_apr*100,2);
      $_resul['interest'] = $interest;
      $_resul['capital'] = $account;
      return $_resul;
  }
}
function EqualSeason($data = array())
{
    if (isset($data['month_times']) && 0 < $data['month_times']) {
        $month_times = $data['month_times'];
    }
    if ($month_times % 3 != 0) {
        return false;
    }
    if (isset($data['account']) && 0 < $data['account']) {
        $account = $data['account'];
    } else {
        return "";
    }
    if (isset($data['year_apr']) && 0 < $data['year_apr']) {
        $year_apr = $data['year_apr'];
    } else {
        return "";
    }
    if (isset($data['borrow_time']) && 0 < $data['borrow_time']) {
        $borrow_time = $data['borrow_time'];
    } else {
        $borrow_time = time();
    }
    $month_apr = $year_apr / 1200;
    $_season = $month_times / 3;
    $_season_money = round($account / $_season, 2);
    $_yes_account = 0;
    $repayment_account = 0;
    $_all_interest = 0;
    $i = 0;
    for (; $i < $month_times; ++$i) {
        $repay = $account - $_yes_account;
        $interest = round($repay * $month_apr, 2);
        $repayment_account += $interest;
        $capital = 0;
        if ($i % 3 == 2) {
            $capital = $_season_money;
            $_yes_account += $capital;
            $repay = $account - $_yes_account;
            $repayment_account += $capital;
        }
        $_result[$i]['repayment_money'] = getfloatvalue($interest + $capital, 2);
        $_result[$i]['repayment_time'] = get_times(array(
            "time" => $borrow_time,
            "num" => $i + 1
        ));
        $_result[$i]['interest'] = getfloatvalue($interest, 2);
        $_result[$i]['capital'] = getfloatvalue($capital, 2);
        $_all_interest += $interest;
    }
    if (isset($data['type']) && $data['type'] == "all") {
        $_resul['repayment_money'] = $repayment_account;
        $_resul['monthly_repayment'] = round($repayment_account / $_season, 2);
        $_resul['month_apr'] = round($month_apr * 100, 2);
        $_resul['interest'] = $_all_interest;
        return $_resul;
    } else {
        return $_result;
    }
}
function EqualEndMonth($data = array())
{
    if (isset($data['month_times']) && 0 < $data['month_times']) {
        $month_times = $data['month_times'];
    }
    if (isset($data['account']) && 0 < $data['account']) {
        $account = $data['account'];
    } else {
        return "";
    }
    if (isset($data['year_apr']) && 0 < $data['year_apr']) {
        $year_apr = $data['year_apr'];
    } else {
        return "";
    }
    if (isset($data['borrow_time']) && 0 < $data['borrow_time']) {
        $borrow_time = $data['borrow_time'];
    } else {
        $borrow_time = time();
    }
    $month_apr = $year_apr / 1200;
    $_yes_account = 0;
    $repayment_account = 0;
    $_all_interest = 0;
    $interest = round($account * $month_apr, 2);
    $i = 0;
    for (; $i < $month_times; ++$i) {
        $capital = 0;
        if ($i + 1 == $month_times) {
            $capital = $account;
        }
        $_result[$i]['repayment_account'] = $interest + $capital;
        $_result[$i]['repayment_time'] = get_times(array(
            "time" => $borrow_time,
            "num" => $i + 1
        ));
        $_result[$i]['interest'] = $interest;
        $_result[$i]['capital'] = $capital;
        $_all_interest += $interest;
    }
    if (isset($data['type']) && $data['type'] == "all") {
        $_resul['repayment_account'] = $account + $interest * $month_times;
        $_resul['monthly_repayment'] = $interest;
        $_resul['month_apr'] = round($month_apr * 100, 2);
        $_resul['interest'] = $_all_interest;
        return $_resul;
    } else {
        return $_result;
    }
}
function EqualYearHalf($data = array())
{
    if (isset($data['month_times']) && 0 < $data['month_times']) {
        $month_times = $data['month_times'];
    }
    if ($month_times % 6 != 0) {
        return false;
    }
    if (isset($data['account']) && 0 < $data['account']) {
        $account = $data['account'];
    } else {
        return "";
    }
    if (isset($data['year_apr']) && 0 < $data['year_apr']) {
        $year_apr = $data['year_apr'];
    } else {
        return "";
    }
    if (isset($data['borrow_time']) && 0 < $data['borrow_time']) {
        $borrow_time = $data['borrow_time'];
    } else {
        $borrow_time = time();
    }
    $month_apr = $year_apr / 1200;
    $_season = $month_times / 6;
    $_season_money = round($account / $_season, 2);
    $_yes_account = 0;
    $repayment_account = 0;
    $_all_interest = 0;
    $i = 0;
    for (; $i < $month_times; ++$i) {
        $repay = $account - $_yes_account;
        $interest = round($repay * $month_apr, 2);
        $repayment_account += $interest;
        $capital = 0;
        if ($i % 6 == 5) {
            $capital = $_season_money;
            $_yes_account += $capital;
            $repay = $account - $_yes_account;
            $repayment_account += $capital;
        }
        $_result[$i]['repayment_money'] = getfloatvalue($interest + $capital, 2);
        $_result[$i]['repayment_time'] = get_times(array(
            "time" => $borrow_time,
            "num" => $i + 1
        ));
        $_result[$i]['interest'] = getfloatvalue($interest, 2);
        $_result[$i]['capital'] = getfloatvalue($capital, 2);
        $_all_interest += $interest;
    }
    if (isset($data['type']) && $data['type'] == "all") {
        $_resul['repayment_money'] = $repayment_account;
        $_resul['monthly_repayment'] = round($repayment_account / $_season, 2);
        $_resul['month_apr'] = round($month_apr * 100, 2);
        $_resul['interest'] = $_all_interest;
        return $_resul;
    } else {
        return $_result;
    }
}
function EqualYear($data = array())
{
    if (isset($data['month_times']) && 0 < $data['month_times']) {
        $month_times = $data['month_times'];
    }
    if ($month_times % 12 != 0) {
        return false;
    }
    if (isset($data['account']) && 0 < $data['account']) {
        $account = $data['account'];
    } else {
        return "";
    }
    if (isset($data['year_apr']) && 0 < $data['year_apr']) {
        $year_apr = $data['year_apr'];
    } else {
        return "";
    }
    if (isset($data['borrow_time']) && 0 < $data['borrow_time']) {
        $borrow_time = $data['borrow_time'];
    } else {
        $borrow_time = time();
    }
    $month_apr = $year_apr / 1200;
    $_season = $month_times / 12;
    $_season_money = round($account / $_season, 2);
    $_yes_account = 0;
    $repayment_account = 0;
    $_all_interest = 0;
    $i = 0;
    for (; $i < $month_times; ++$i) {
        $repay = $account - $_yes_account;
        $interest = round($repay * $month_apr, 2);
        $repayment_account += $interest;
        $capital = 0;
        if ($i % 12 == 11) {
            $capital = $_season_money;
            $_yes_account += $capital;
            $repay = $account - $_yes_account;
            $repayment_account += $capital;
        }
        $_result[$i]['repayment_money'] = getfloatvalue($interest + $capital, 2);
        $_result[$i]['repayment_time'] = get_times(array(
            "time" => $borrow_time,
            "num" => $i + 1
        ));
        $_result[$i]['interest'] = getfloatvalue($interest, 2);
        $_result[$i]['capital'] = getfloatvalue($capital, 2);
        $_all_interest += $interest;
    }
    if (isset($data['type']) && $data['type'] == "all") {
        $_resul['repayment_money'] = $repayment_account;
        $_resul['monthly_repayment'] = round($repayment_account / $_season, 2);
        $_resul['month_apr'] = round($month_apr * 100, 2);
        $_resul['interest'] = $_all_interest;
        return $_resul;
    } else {
        return $_result;
    }
}
function getMinfo($uid, $field = "m.unionid,m.is_ssq,m.pin_pass,mm.account_money,mm.yubi,mm.money_experience,mm.back_money,IFNULL(m.nick_name,m.user_name) as nick_name")
{

    $pre = c("DB_PREFIX");
    $MM = m("member_money")->field("money_freeze,money_collect,account_money")->find($uid);
    if (!is_array($MM)) {
        m("member_money")->add(array(
            "uid" => $uid
        ));
    }
    $vm = m("members m")->field($field)->join("{$pre}member_money mm ON mm.uid=m.id")->join("{$pre}member_info mi on mi.uid = m.id")->where("m.id={$uid}")->find();
    //echo  m("members m")->getlastsql();
    return $vm;
}
function getMinfo_login_last($uid, $field = "")
{
    $pre = c("DB_PREFIX");
    $vm = m("member_login m")->where("m.uid={$uid}")->order('id desc')->find();
    
    return $vm;
}
function getMemberInfoDone($uid)
{
    $pre = c("DB_PREFIX");
    $field = "m.id,m.id as uid,m.user_name,mbank.uid as mbank_id,mi.uid as mi_id,mhi.uid as mhi_id,mci.uid as mci_id,mdpi.uid as mdpi_id,mei.uid as mei_id,mfi.uid as mfi_id,s.phone_status,s.id_status,s.email_status,s.safequestion_status";
    $row = m("members m")->field($field)->join("{$pre}member_banks mbank ON m.id=mbank.uid")->join("{$pre}member_contact_info mci ON m.id=mci.uid")->join("{$pre}member_department_info mdpi ON m.id=mdpi.uid")->join("{$pre}member_house_info mhi ON m.id=mhi.uid")->join("{$pre}member_ensure_info mei ON m.id=mei.uid")->join("{$pre}member_info mi ON m.id=mi.uid")->join("{$pre}member_financial_info mfi ON m.id=mfi.uid")->join("{$pre}members_status s ON m.id=s.uid")->where("m.id={$uid}")->find();
    $is_data = m("member_data_info")->where("uid={$row['uid']}")->count("id");
    $i == 0;
    if (0 < $row['mbank_id']) {
        ++$i;
        $row['mbank'] = "<span style='color:green'>已填写</span>";
    } else {
        $row['mbank'] = "<span style='color:black'>未填写</span>";
    }
    if (0 < $row['mci_id']) {
        ++$i;
        $row['mci'] = "<span style='color:green'>已填写</span>";
    } else {
        $row['mci'] = "<span style='color:black'>未填写</span>";
    }
    if (0 < $is_data) {
        $row['mdi_id'] = $is_data;
        $row['mdi'] = "<span style='color:green'>已填写</span>";
    } else {
        $row['mdi'] = "<span style='color:black'>未填写</span>";
    }
    if (0 < $row['mhi_id']) {
        ++$i;
        $row['mhi'] = "<span style='color:green'>已填写</span>";
    } else {
        $row['mhi'] = "<span style='color:black'>未填写</span>";
    }
    if (0 < $row['mdpi_id']) {
        ++$i;
        $row['mdpi'] = "<span style='color:green'>已填写</span>";
    } else {
        $row['mdpi'] = "<span style='color:black'>未填写</span>";
    }
    if (0 < $row['mei_id']) {
        ++$i;
        $row['mei'] = "<span style='color:green'>已填写</span>";
    } else {
        $row['mei'] = "<span style='color:black'>未填写</span>";
    }
    if (0 < $row['mfi_id']) {
        ++$i;
        $row['mfi'] = "<span style='color:green'>已填写</span>";
    } else {
        $row['mfi'] = "<span style='color:black'>未填写</span>";
    }
    if (0 < $row['mi_id']) {
        ++$i;
        $row['mi'] = "<span style='color:green'>已填写</span>";
    } else {
        $row['mi'] = "<span style='color:black'>未填写</span>";
    }
    $row['i'] = $i;
    return $row;
}
function getVerify($uid)
{
    $pre = c("DB_PREFIX");
    $vo = M("members m")->field("m.id,m.user_leve,m.time_limit,s.id_status as id_status,s.phone_status,s.email_status,s.video_status,s.face_status,m.pin_pass as pin_pass")->join("{$pre}members_status s ON s.uid=m.id")->where("m.id={$uid}")->find();
//var_dump(M("members m")->getlastsql(),'1');
    $str = "";
    $vobank = M("member_banks")->where('uid='.$uid) ->find();  
 // var_dump($vobank,$vo['id_status'] ,$uid,'1');
    if ($vo['phone_status'] == 1) {
        $str .= "<a href=\"" . __APP__ . "/member/Memberinfo/cell_phone.html\" title=\"手机验证，已绑定\"><img alt=\"手机认证通过\" src=\"" . __ROOT__ . "/Public/web/img/zuixinzixun/phone.png\"/></a> ";
    } else {
        $str .= "<a href=\"" . __APP__ . "/member/Memberinfo/cell_phone.html\" title=\"手机验证，未绑定\"><img alt=\"手机认证未通过\" src=\"" . __ROOT__ . "/Public/web/img/zuixinzixun/phone.png\"/></a> ";
    }

    if ($vo['id_status'] == 1) {
        $str .= "<a titile=\"实名认证通过\" title=\"实名认证通过\"><img alt=\"实名认证通过\" src=\"" . __ROOT__ . "/Public/web/img/zuixinzixun/idcard1.png\"/></a> ";
    } else {
        $str .= "<a href=\"" . __APP__ . "/member/verify/idcard.html\" title=\"实名认证未通过\" ><img alt=\"实名认证未通过\" src=\"" . __ROOT__ . "/Public/web/img/zuixinzixun/idcard2.png\"/></a> ";
    }
    
     //  var_dump($vobank,$vo ,$uid,'2');
  //  die;
    if(!empty($vobank['bank_num'])){
        $str .= "<a href=\"" . __APP__ . "/member/bank/index.html\" title=\"已绑定银行卡\"><img alt=\"已绑定银行卡\" src=\"" . __ROOT__ . "/Public/web/img/zuixinzixun/CreditCard.png\"/></a> ";
    }else{
        $str .= "<a href=\"" . __APP__ . "/member/bank/index.html\" title=\"未绑定银行卡\"><img alt=\"未绑定银行卡\" src=\"" . __ROOT__ . "/Public/web/img/zuixinzixun/CreditCard1.png\"/></a> ";   
    }

    if(!empty($vo['pin_pass'])){
        $str .= "<a href=\"" . __APP__ . "/member/user/pinpass.html\" title=\"已设置支付密码\"><img alt=\"已设置支付密码\" src=\"" . __ROOT__ . "/Public/web/img/zuixinzixun/pinss1.png\"/></a> ";
    }else{
        $str .= "<a href=\"" . __APP__ . "/member/user/pinpass.html\" title=\"未设置支付密码\"><img alt=\"未设置支付密码\" src=\"" . __ROOT__ . "/Public/web/img/zuixinzixun/pinss.png\"/></a> ";   
    }
    
    if ($vo['email_status'] == 1) {
        // $str .= "<a href=\"" . __APP__ . "/member/verify/email.html\"><img alt=\"邮件认证通过\" src=\"" . __ROOT__ . "/Style/M/images/renzheng/email.png\"/></a> ";
    } else {
        // $str .= "<a href=\"" . __APP__ . "/member/verify/email.html\"><img alt=\"邮件认证未通过\" src=\"" . __ROOT__ . "/Style/M/images/renzheng/email_0.png\"/></a> ";
    }
    if ($vo['user_leve'] != 0 && time() < $vo['time_limit']) {
        // $str .= "<a href=\"" . __APP__ . "/member/vip/index.html?ss=7#chip-1\"><img alt=\"VIP会员\" src=\"" . __ROOT__ . "/Style/M/images/renzheng/vip.png\"/></a> ";
    } else {
        // $str .= "<a href=\"" . __APP__ . "/member/vip/index.html?ss=8#chip-1\"><img alt=\"不是VIP会员\" src=\"" . __ROOT__ . "/Style/M/images/renzheng/vip_0.png\"/></a> ";
    }
    

    if ($vo['video_status'] == 1) {
      //  $str .= "<a href=\"javascript:;\" onclick=\"alert('已通过视频认证');\"><img alt=\"视频认证通过\" src=\"" . __ROOT__ . "/Style/M/images/renzheng/video.png\"/></a> ";
    } else {
      //  $str .= "<a href=\"javascript:;\" onclick=\"videoverify();\"><img alt=\"未进行视频认证\" src=\"" . __ROOT__ . "/Style/M/images/renzheng/video_0.png\"/></a> ";
    }
    if ($vo['face_status'] == 1) {
      //  $str .= "<a href=\"javascript:;\" onclick=\"alert('已通过现场认证');\"><img alt=\"现场认证通过\" src=\"" . __ROOT__ . "/Style/M/images/renzheng/place.png\"/></a> ";
    } else {
       // $str .= "<a href=\"javascript:;\" onclick=\"faceverify();\"><img alt=\"未进行现场认证\" src=\"" . __ROOT__ . "/Style/M/images/renzheng/place_0.png\"/></a> ";
    }
    return $str;
}

function getVerifywap($uid)
{
    $pre = c("DB_PREFIX");
    $vo = m("members m")->field("m.id,m.user_leve,m.time_limit,s.id_status,s.phone_status,s.email_status,s.is_vip,s.video_status,s.face_status")->join("{$pre}members_status s ON s.uid=m.id")->where("m.id={$uid}")->find();
    $str = "";
    
    if ($vo['is_vip'] == 1) {
        $str .= "超级vip";
    }else{
    	 $str .= "普通会员";
    }
    return $str;
}
function get_times($data = array())
{
    if (isset($data['time']) && $data['time'] != "") {
        $time = $data['time'];
    } else if (isset($data['date']) && $data['date'] != "") {
        $time = strtotime($data['date']);
    } else {
        $time = time();
    }
    if (isset($data['type']) && $data['type'] != "") {
        $type = $data['type'];
    } else {
        $type = "month";
    }
    if (isset($data['num']) && $data['num'] != "") {
        $num = $data['num'];
    } else {
        $num = 1;
    }
    if ($type == "month") {
        $month = date("m", $time);
        $year = date("Y", $time);
        $_result = strtotime("{$num} month", $time);
        $_month = ( integer )date("m", $_result);
        if (12 < $month + $num) {
            $_num = $month + $num - 12;
            $year += 1;
        } else {
            $_num = $month + $num;
        }
        if ($_num != $_month) {
            $_result = strtotime("-1 day", strtotime("{$year}-{$_month}-01"));
        }
    } else {
        $_result = strtotime("{$num} {$type}", $time);
    }
    if (isset($data['format']) && $data['format'] != "") {
        return date($data['format'], $_result);
    } else {
        return $_result;
    }
}
function getMemberBorrowScan($uid)
{
    $field = "borrow_status,count(id) as num,sum(borrow_money) as money,sum(repayment_money) as repayment_money";
    $borrowNum = m("borrow_info")->field($field)->where("borrow_uid = {$uid}")->group("borrow_status")->select();
    foreach ($borrowNum as $v) {
        $borrowCount[$v['borrow_status']] = $v;
    }
    $field = "status,sort_order,borrow_id,sum(capital) as capital,sum(interest) as interest";
    $repaymentNum = m("investor_detail")->field($field)->where("borrow_uid = {$uid}")->group("sort_order,borrow_id")->select();
    foreach ($repaymentNum as $v) {
        $repaymentStatus[$v['status']]['capital'] += $v['capital'];
        $repaymentStatus[$v['status']]['interest'] += $v['interest'];
        ++$repaymentStatus[$v['status']]['num'];
    }
    $field = "status,count(id) as num,sum(investor_capital) as investor_capital,sum(reward_money) as reward_money,sum(investor_interest) as investor_interest,sum(receive_capital) as receive_capital,sum(receive_interest) as receive_interest";
    $investNum = m("borrow_investor")->field($field)->where("investor_uid = {$uid}")->group("status")->select();
    $_reward_money = 0;
    foreach ($investNum as $v) {
        $investStatus[$v['status']] = $v;
        $_reward_money += floatval($v['reward_money']);
    }
    $field = "borrow_id,sort_order,sum(`capital`) as capital,count(id) as num";
    //$expiredNum = m("investor_detail")->field($field)->where("`repayment_time`=0 and `deadline`<" . time() . " and borrow_uid={$uid}")->join()->group("borrow_id,sort_order")->select();
    $pre = C('DB_PREFIX');
    $sql = "select b.borrow_id,b.sort_order,sum(`b.capital`) as capital,count(b.id) as num from {$pre}investor_detail d left join {$pre}borrow_info b ON b.id=d.borrow_id where d.borrow_uid ={$uid} AND b.borrow_status=6 AND d.deadline<".time()." AND d.repayment_time=0 group by d.sort_order,d.borrow_id order by d.borrow_id,d.sort_order limit";
    $expiredNum = M()->query($sql);
    
    $_expired_money = 0;
    foreach ($expiredNum as $v) {
        $expiredStatus[$v['borrow_id']][$v['sort_order']] = $v;
        $_expired_money += floatval($v['capital']);
    }
    //print_r($expiredNum);
    //$Model = new Model();
    //count($expiredNum);
    
    $rowtj['expiredMoney'] = getfloatvalue($_expired_money, 2);
    $rowtj['expiredNum'] = ($_expired_money=="0"?0:count($expiredNum));
    //echo $rowtj['expiredNum'];
    $field = "borrow_id,sort_order,sum(`capital`) as capital,count(id) as num";
    $expiredInvestNum = m("investor_detail")->field($field)->where("`repayment_time`=0 and `deadline`<" . time() . " and investor_uid={$uid} AND status <> 0")->group("borrow_id,sort_order")->select();
    $_expired_invest_money = 0;
    foreach ($expiredInvestNum as $v) {
        $expiredInvestStatus[$v['borrow_id']][$v['sort_order']] = $v;
        $_expired_invest_money += floatval($v['capital']);
    }
    //print_r($borrowCount);
    //exit;
    $rowtj['expiredInvestMoney'] = getfloatvalue($_expired_invest_money, 2);
    $rowtj['expiredInvestNum'] = count($expiredInvestNum);
    $rowtj['jkze'] = getfloatvalue(floatval($borrowCount[6]['money'] + $borrowCount[7]['money'] + $borrowCount[8]['money'] + $borrowCount[9]['money']), 2);
    $rowtj['yhze'] = getfloatvalue(floatval($borrowCount[6]['repayment_money'] + $borrowCount[7]['repayment_money'] + $borrowCount[8]['repayment_money'] + $borrowCount[9]['repayment_money']), 2);
    $rowtj['dhze'] = getfloatvalue($rowtj['jkze'] - $rowtj['yhze'], 2);
    $rowtj['jcze'] = getfloatvalue(floatval($investStatus[4]['investor_capital'] + $investStatus[5]['investor_capital'] + $investStatus[6]['investor_capital']), 2);
    $rowtj['ysze'] = getfloatvalue(floatval($investStatus[4]['receive_capital'] + $investStatus[5]['receive_capital'] + $investStatus[6]['receive_capital']), 2);
    //var_dump($rowtj['ysze']);die;
    $rowtj['dsze'] = getfloatvalue($rowtj['jcze'] - $rowtj['ysze'], 2);
    $rowtj['fz'] = getfloatvalue($rowtj['jcze'] - $rowtj['jkze'], 2);
    $rowtj['dqrtb'] = getfloatvalue($investStatus[1]['investor_capital'], 2);
    $rowtj['earnInterest'] = getfloatvalue(floatval($investStatus[5]['receive_interest'] + $investStatus[6]['receive_interest']), 2);
    $rowtj['payInterest'] = getfloatvalue(floatval($repaymentStatus[1]['interest'] + $repaymentStatus[2]['interest'] + $repaymentStatus[3]['interest'] + $repaymentStatus[3]['interest']), 2);
    $rowtj['willgetInterest'] = getfloatvalue(floatval($investStatus[4]['investor_interest'] - $investStatus[4]['receive_interest']), 2);
    $rowtj['willpayInterest'] = getfloatvalue(floatval($repaymentStatus[7]['interest']), 2);
    $rowtj['borrowOut'] = getfloatvalue(floatval($investStatus[4]['investor_capital'] + $investStatus[5]['investor_capital'] + $investStatus[6]['investor_capital']), 2);
    $rowtj['borrowIn'] = getfloatvalue(floatval($borrowCount[6]['money'] + $borrowCount[7]['money'] + $borrowCount[8]['money'] + $borrowCount[9]['money']), 2);
    $rowtj['jkcgcs'] = $borrowCount[6]['num'] + $borrowCount[7]['num'] + $borrowCount[8]['num'] + $borrowCount[9]['num'];
    $rowtj['jkallnum'] = $borrowCount[6]['num'] + $borrowCount[7]['num'] + $borrowCount[8]['num'] + $borrowCount[9]['num']+ $rowtj['expiredNum'];
	//
    $rowtj['ysbj'] = getfloatvalue(floatval($investStatus[5]['investor_capital']), 2);
    $rowtj['tbjl'] = $_reward_money;
    $row = array();
    $row['borrow'] = $borrowCount;
    $row['repayment'] = $repaymentStatus;
    $row['invest'] = $investStatus;
    $row['tj'] = $rowtj;
    return $row;
}
function getUserWC($uid)
{
    $row = array();
    $field = "count(id) as num,sum(withdraw_money) as money";
    $row['W'] = m("member_withdraw")->field($field)->where("uid={$uid} AND withdraw_status=2")->find();
    $field = "count(id) as num,sum(money) as money";
    $row['C'] = m("member_payonline")->field($field)->where("uid={$uid} AND status=1 and way<>'off' and way<>'online'")->find();
    $field = "count(id) as num,sum(money) as money";
    $row['D'] = m("member_payonline")->field($field)->where("uid={$uid} AND status=1 and way='off'")->find();
    //$field = "count(id) as num,sum(affect_money) as money";
   // $row['E'] = m("member_moneylog")->field($field)->where("uid={$uid} AND type=7")->find();
    $field = "count(id) as num,sum(money) as money";
    $row['E'] = m("member_payonline")->field($field)->where("uid={$uid} AND status=1 and way='online'")->find();
    return $row;
}
function getExpiredDays($deadline)
{
    if ($deadline < 1000) {
        return "数据有误";
    }
    return ceil((time() - $deadline) / 3600 / 24);
}
function getExpiredMoney($expired, $capital, $interest)
{
    $glodata = get_global_setting();
    $expired_fee = explode("|", $glodata['fee_expired']);
    if ($expired <= $expired_fee[0]) {
        return 0;
    }
    return getfloatvalue(($capital + $interest) * $expired * $expired_fee[1] / 1000, 2);
}
function getExpiredCallFee($expired, $capital, $interest)
{
    $glodata = get_global_setting();
    $call_fee = explode("|", $glodata['fee_call']);
    if ($expired <= $call_fee[0]) {
        return 0;
    }
    return getfloatvalue(($capital + $interest) * $expired * $call_fee[1] / 1000, 2);
}
function getNet($minfo, $capitalinfo)
{
    return getfloatvalue($minfo['account_money'] + $minfo['money_freeze'] + $minfo['money_collect'] - intval($capitalinfo['borrow'][6]['money'] - $capitalinfo['borrow'][6]['repayment_money']), 2);
}
function setBackUrl($per = "", $suf = "")
{
    $url = $_SERVER['HTTP_REFERER'];
    $urlArr = parse_url($url);
    $query = $per . "?1=1&" . $urlArr['query'] . $suf;
    session("listaction", $query);
}
function canInvestMoneyCheck($uid, $borrow_id)
{
    $rs['status'] = "0";
    $minfo        = getMinfo($uid, 'm.pin_pass,mm.account_money');
    // var_dump($minfo);exit;    
    $field      = "id,borrow_uid,borrow_money,borrow_status,has_borrow,borrow_min,borrow_max,password,pause,daishou,daishou_money,has_vouch,is_experience,is_bonus,is_memberinterest";
    $borrowinfo = M('borrow_info')->field($field)->find($borrow_id);
    if ($uid == $borrowinfo['borrow_uid']) {
        $rs["tips"] = "不能去投自己的项目";
        return $rs;
    }

    if ($borrowinfo['borrow_type'] == 3 && $borrowinfo["daishou"] != 0) {
        //秒标检查
        $dashou_name = array(0 => "不限制", 1 => "全部待收", 2 => "当月待收", 3 => "当日待收");
        if ($borrowinfo["daishou_money"] > daishou($borrowinfo["daishou"], $uid)) {
            $rs["tips"] = "当前项目的" . $dashou_name[$borrowinfo["daishou"]] . "限制为：" . $borrowinfo["daishou_money"] . "元！";
            return $rs;
        }
    }
    if ($borrowinfo['pause'] == 1) {
        $rs['tips'] = "此项目当前已经截标，请等待管理员开启。";
        return $rs;
    }
    if ($borrowinfo['borrow_status'] != 2) {
        $rs['tips'] = "只能投正在借款中的标";
        return $rs;
    }
    //担保标检测
    if ($borrowinfo['borrow_type'] == 2 && $borrowinfo['has_vouch'] < $borrowinfo['borrow_money']) {
        $rs['tips'] = "此项目担保还未完成，您可以担保此项目或者等担保完成再投此项目。";
        return $rs;
    }
    $borrowinfo['need'] = bcsub($borrowinfo['borrow_money'], $borrowinfo['has_borrow'], 2);
    if ($borrowinfo['need'] <= 0) {
        $rs['tips'] = "此项目已经投满";
        return $rs;
    }
    $borrowinfo['borrow_min_cn'] = $borrowinfo['borrow_min'] < 50 ? "50元" : $borrowinfo['borrow_min'] . "元";
    $borrowinfo['borrow_max_cn'] = $borrowinfo['borrow_max'] < 50 ? '无限' : $borrowinfo['borrow_max'] . "元";

    //优惠券信息:
    $member_interest_rate_list = M('member_interest_rate')->where("uid='{$uid}' and status = 1 and (" . time() . " > start_time and " . time() . " < end_time )")->field("id,interest_rate,FROM_UNIXTIME(end_time,'%Y-%m-%d') as end_time_cn")->select();

    //红包信息
    $bonus_list = M('member_bonus')->where("uid='{$uid}' and status = 1 and (" . time() . " > start_time and " . time() . " < end_time )")->field("id,money_bonus,FROM_UNIXTIME(end_time,'%Y-%m-%d') as end_time_cn")->select();

    //是否设置支付密码
    $pin_pass = $vm['pin_pass'];
    $has_pin  = (empty($pin_pass)) ? "0" : "1";

    $rs['account_money']    = $minfo['account_money'];
    $rs['money_experience'] = $minfo['money_experience'];

    $rs['has_pin']    = $has_pin;
    $rs['borrowinfo'] = $borrowinfo;

    $rs['member_interest_rate_list'] = $member_interest_rate_list;
    $rs['bonus_list']                = $bonus_list;
    $rs['status']                    = "1";
    return $rs;
}
function get_TenderList($map,$size,$limit=10){
    $pre = C('DB_PREFIX');
    $lists = M('borrow_investor')->field("FROM_UNIXTIME(add_time,'%Y年%m月') as months")->where($map)->group('months')->order("id desc")->select();
    $list=array();

    $sql ='';
    foreach ($lists as $key => $value) {
        foreach ($map as $k => $v) {
            if(is_array($v)){
                $sql .= 'i.'.$k .' ' . $v['0'].'  ("' .$v['1']. ' ") and ';
            }else{
                $sql .= 'i.'.$k .' = ' .$v. ' and ';
            }
        }
        $lists1=M('borrow_investor i')->join("lzh_borrow_info b on i.borrow_id=b.id")->field("i.id,i.add_time as invest_time,i.borrow_id,i.investor_capital,i.investor_interest,sum(i.investor_capital+i.investor_interest) as capmoney,i.deadline,b.borrow_name")->where($sql." FROM_UNIXTIME(i.add_time,'%Y年%m月') = '".$value['months']."'")->group('i.id')->select();
        $dayrow = array();
        $row = array();
        foreach ($lists1 as $k => $val) {
            $dayrow[date('Y-m-d',$val['invest_time'])][] = $val;
        }

            $i=0;
        foreach ($dayrow as $k1 => $val1) {
            $sum = 0;
            foreach ($val1 as $k => $val) {
                $sum = $sum + $val['investor_capital'] + $val['investor_interest'];
                $val1[$k]['invest_time'] = date('Y-m-d',$val['invest_time']);
                $val1[$k]['deadline'] = date('Y-m-d',$val['deadline']);
            }
            $row[$i]['daydate']=$k1;
            $row[$i]['allcapinvest'] = $sum;
            $row[$i]['list'] = $val1;
            $i++;
        }

        $list[$key]['months']=$value['months'];
        $list[$key]['tongji']=M('borrow_investor i')->field("sum(i.investor_capital+i.investor_interest) as allcaptital,sum(i.investor_interest) as allinterest")->where($sql." FROM_UNIXTIME(i.add_time,'%Y年%m月') = '".$value['months']."'")->find();
        $list[$key]['tongji']['allcaptital'] = $list[$key]['tongji']['allcaptital']?$list[$key]['tongji']['allcaptital']:'0.00';
        $list[$key]['tongji']['allinterest'] = $list[$key]['tongji']['allinterest']?$list[$key]['tongji']['allinterest']:'0.00';
        $list[$key]['list']=$row;
    }
    return $list;
}
function get_TenderingList($map,$months){
    $pre = C('DB_PREFIX');
    $sql ='';
    $list=array();
    $dayrow=array();
    $row=array();
    foreach ($map as $k => $v) {
        if(is_array($v)){
            $sql .= 'i.'.$k .' ' . $v['0'].'  ("' .$v['1']. ' ") and ';
        }else{
            $sql .= 'i.'.$k .' = ' .$v. ' and ';
        }
    }
    $lists=M('borrow_investor i')->join("lzh_borrow_info b on i.borrow_id=b.id")->field("i.id,i.add_time as invest_time,i.borrow_id,i.investor_capital,i.investor_interest,sum(i.investor_capital+i.investor_interest) as capmoney,i.deadline,b.borrow_name")->where($sql." FROM_UNIXTIME(i.add_time,'%Y-%m') = '".$months."'")->group('i.id')->order("i.id desc")->select();
    foreach ($lists as $key => $value) {
        $dayrow[date('Y-m-d',$value['invest_time'])][] = $value;
    }
    $i=0;
    foreach ($dayrow as $key => $value) {
        $sum = 0;
        foreach ($value as $k => $val) {
            $sum = $sum + $val['investor_capital'] + $val['investor_interest'];
            $value[$k]['invest_time'] = date('Y-m-d',$val['invest_time']);
            $value[$k]['deadline'] = date('Y-m-d',$val['deadline']);

        }
        $row[$i]['daydate']=$key;
        $row[$i]['allcapinvest'] = $sum;
        $row[$i]['list'] = $value;
        $i++;
    }
    $list['tongji']=M('borrow_investor i')->field("sum(i.investor_capital+i.investor_interest) as allcaptital,sum(i.investor_interest) as allinterest")->where($sql." FROM_UNIXTIME(i.add_time,'%Y-%m') = '".$months."'")->find();
    // echo M('borrow_investor i')->getlastsql();
    $list['tongji']['allcaptital'] = $list['tongji']['allcaptital']?$list['tongji']['allcaptital']:'0.00';
    $list['tongji']['allinterest'] = $list['tongji']['allinterest']?$list['tongji']['allinterest']:'0.00';
    $list['list']=$row?$row:array();
    return $list;
}
function get_user_name($uid){
    $pre = C('DB_PREFIX');

    $map['m.id'] = $uid;

    $field = "user_name";

    $list = M('members m')->field($field)->where($map)->find();

    return  $list['user_name'];
}
function get_pro_name($pid){
    $res = m("pro_category")->find($pid);
    return $res['type_name'];
}

        // returns true if one of the specified mobile browsers is detected
        // 如果监测到是指定的浏览器之一则返回true


function is_mobile(){
    $regex_match="/(nokia|iphone|android|motorola|^mot\-|softbank|foma|docomo|kddi|up\.browser|up\.link|";

    $regex_match.="htc|dopod|blazer|netfront|helio|hosin|huawei|novarra|CoolPad|webos|techfaith|palmsource|";

    $regex_match.="blackberry|alcatel|amoi|ktouch|nexian|samsung|^sam\-|s[cg]h|^lge|ericsson|philips|sagem|wellcom|bunjalloo|maui|";

    $regex_match.="symbian|smartphone|midp|wap|phone|windows ce|iemobile|^spice|^bird|^zte\-|longcos|pantech|gionee|^sie\-|portalmmm|";

    $regex_match.="jig\s browser|hiptop|^ucweb|^benq|haier|^lct|opera\s*mobi|opera\*mini|320x320|240x320|176x220";

    $regex_match.=")/i";

    // preg_match()方法功能为匹配字符，既第二个参数所含字符是否包含第一个参数所含字符，包含则返回1既true
    return preg_match($regex_match, strtolower($_SERVER['HTTP_USER_AGENT']));
}

function getincode(){
    $chars = "abcdefghijkmnpqrstuvwxyz23456789";
    $chars = str_shuffle($chars);
    $str = substr($chars, 0, 6);
    $is_cun = m("members")->where("incode = '".$str."'")->count();
    if($is_cun>0){
        getincode();
    }
    return $str;
}
function getyhincode(){
    $chars = "abcdefghijkmnpqrstuvwxyz";
    $chars = str_shuffle($chars);
    $str = 'xyj_'.substr($chars, 0,5).rand(10000,99999);
    $is_cun = m("members")->where("yhname = '".$str."'")->count();
    if($is_cun>0){
        getyhincode();
    }
    return $str;
}
function Sec2Time($time){
    if(is_numeric($time)){
        $value = array(
            "days" => 0, "hours" => 0,
            "minutes" => 0, "seconds" => 0,
        );
        if($time >= 86400){
            $value["days"] = floor($time/86400);
            $time = ($time%86400);
        }
        if($time >= 3600){
            $value["hours"] = floor($time/3600);
            $time = ($time%3600);
        }
        if($time >= 60){
            $value["minutes"] = floor($time/60);
            $time = ($time%60);
        }
        $value["seconds"] = floor($time);
        $t = ($value["days"] ? $value["days"] . "天" : "") . ($value["hours"] ? $value["hours"] . "时" : "") . ($value["minutes"] ? $value["minutes"] . "分" : "");
        $t .= $t ? ($value["seconds"] ? $value["seconds"] . "秒" : ""): ($value["seconds"]."秒");
        Return $t;

    }else{
        return (bool) FALSE;
    }
}
function doTODO(){
    $per = C('DB_PREFIX');

    $date = date('Y-m-d 00:00:00',time());
    $admin_log = M("admin_log");
    $admin_log->startTrans();
    $auserLastLoginLog = $admin_log->lock(true)->where("type = 0 and add_time > ".strtotime($date))->find();
    adminCreditsLog(0,"管理员[N]登录成功！");
    $admin_log->commit();
    //if(empty($auserLastLoginLog)){
        $borrow_investor = M("borrow_investor");
        $borrow_investor->startTrans();
        $borrow_investor->lock(true)->field("id")->select();
        $borrowinvestor = M("borrow_investor bi")
            ->join("{$per}borrow_info b ON bi.borrow_id=b.id")
            ->field('bi.investor_capital,bi.id,b.borrow_interest_rate ,bi.investor_uid as uid,bi.id as iid,bi.add_time')
            ->where("bi.borrow_id=1 and bi.status!=110")
            ->select();
        foreach($borrowinvestor as $key=>$value){
            if($value['add_time'] + 86400 > time()) {
                continue;
            }
            $receive_interest = bcadd($value['investor_capital']*$value['borrow_interest_rate']/100/360, 0, 2);
            $money= M('member_money mm') ->join("lzh_members m ON m.id=mm.uid")	->where("m.id=".$value["uid"]) ->find();

            //每期收益
            if(!empty($value)){
                $investlog['starttime'] = time();
                $investlog['endtime'] = time();
                $investlog['deadline'] = time();
                $investlog['substitute_time'] = time();
                $investlog['income'] = 1;
                $investlog['has_capital'] = 1;
                $investlog['borrow_interest_rate'] = $value['borrow_interest_rate'];
                $investlog['total'] = $receive_interest + $value['investor_capital'];
                $investlog['receive_capital'] = $value['investor_capital'];
                $investlog['receive_interest'] = $receive_interest;
                $investlog['interest'] = $receive_interest;
                $investlog['repayment_time'] = time();
                $investlog['borrow_id'] = 1;
                $investlog['investor_uid'] = $value["uid"];
                $investlog['borrow_uid'] =0;
                $investlog['invest_id'] = $value['iid'];
                $investlog['nums'] =  1;
                $investlog['has_capital'] = 0;
                $investlog['investor'] = $money['user_name'];
                $investlog['capital'] = $value['investor_capital']; //投资金额
                $investlog['benjin'] = $value['investor_capital']; //本金
                $investlog['invest'] = $receive_interest; //收益
                $investlog['allmoney'] = $receive_interest + $value['investor_capital']; //收益
                $investlog['rate'] = 0; //支持比例
                $investlog['status'] = 0; //支持比例
                M('investor_detail')->add($investlog);
            
                M('member_moneylog')->add(['uid'=>$value["uid"],'type'=>'9','affect_money'=>$receive_interest,'yubi'=>$money['yubi'],'freeze_yubi'=>$money['yubi_freeze'],'yongjin'=>$money['yongjin'],'account_money'=>$money['account_money']+$receive_interest,'back_money'=>$money['back_money'],'collect_money'=>$money['collect_money'],'freeze_money'=>$money['money_freeze']-$value['investor_capital'],'info'=>'体验金回款','add_time'=>time(),'add_ip'=>get_client_ip(),'target_uid'=>0,'target_uname'=>'','experience_money'=>$money['money_experience']]);
                $experience = M('member_experience')->where("uid=".$value["uid"]." and paystatus=1")->find();
                if(isset($experience) && !empty($experience)) {
                    M('member_experience')->where("id = {$experience['id']}")->save(["paystatus" => 3]);
                }
                M('member_money')->save(["uid"=>$money['uid'],"money_freeze"=>$money['money_freeze']-$value['investor_capital'],"account_money"=>$money['account_money']+$receive_interest,"update_time"=>time()]);
           
               // M('member_money')->save(["uid"=>$money['uid'],"money_freeze"=>$money['money_freeze']-$value['investor_capital'],'yubi'=>$money['yubi'],'yubi_freeze'=>$money['yubi_freeze'],'yongjin'=>$money['yongjin'],"money_collect"=>$money['money_collect'],"account_money"=>$money['account_money']+$receive_interest,"back_money"=>$money['back_money'],"credit_limit"=>$money['credit_limit'],"credit_cuse"=>$money['credit_cuse'],"borrow_vouch_limit"=>$money['borrow_vouch_limit'],"borrow_vouch_cuse"=>$money['borrow_vouch_cuse'],"invest_vouch_limit"=>$money['invest_vouch_limit'],"invest_vouch_cuse"=>$money['invest_vouch_cuse'],"money_experience"=>$money['money_experience'],"update_time"=>time()]);
                M("borrow_investor")->where("id=".$value['iid'])->save(['status'=>110]);
            }
        }
        $borrow_investor->commit();
    //}
}
/**
     * 发起HTTPS请求
     */
     function curl_post($url,$data,$header,$post=1)
     {
       //初始化curl
       $ch = curl_init();
       //参数设置  
       $res= curl_setopt ($ch, CURLOPT_URL,$url);
       curl_setopt($ch, CURLOPT_POST, $post);

       curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
       curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
       curl_setopt ($ch, CURLOPT_HEADER, 0);
      

       if($post)
           curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
       curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
       curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
       curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
       $result = curl_exec ($ch);
       curl_close($ch);

         //$data = array("name" => "Hagrid", "age" => "36");
//         $data = json_encode($data);
//         $ch = curl_init('http://api.local/rest/users');
//         curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
//         curl_setopt($ch, CURLOPT_POSTFIELDS,$data_string);
//         curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
//         curl_setopt($ch, CURLOPT_HTTPHEADER, array(
//                 'Content-Type: application/json',
//                 'Content-Length: ' . strlen($data))
//         );

       return $result;
    }
    //退款接口
function tuikuanapi($pay_way,$ordernums,$money){
    if($pay_way==1){
        $data['WIDout_trade_no']=$ordernums;
        //订单名称，必填
        $data['WIDsubject']="海鲜商城购买";
        //付款金额，必填
        $data['WIDrefund_amount']=$money;
        //商品描述，可空
        $data['WIDrefund_reason']="商品退款";

        $data['WIDout_request_no']="TK_".time();
        vendor('Alipawap.wappay.pay');
        $wappay = new pay();
        $aa=$wappay->tuikuan($data);
        $resultCode=$aa->code;
        if(!empty($resultCode)&&$resultCode == 10000){
            $rdata["trade_no"]=$aa->trade_no;
            $rdata["status"]="1";
        } else {
            //$rdata["trade_no"]=$aa->sub_msg;
            $rdata["msg"]=$aa->sub_msg;
            $rdata["status"]="0";
        }
    }
    if($pay_way==2){
        $data['out_trade_no'] = $ordernums;
        $data['out_refund_no'] = "TK-".time();
        $data['total_fee'] = $money*100;//支付金额
        $data['refund_fee'] =$money*100;//退款金额
        vendor('Weixin.jsapi');
        $jsapi = new jsapi();
        $content=$jsapi->tuikuan($data);

        if($content["result_code"]=="FAIL"){
            $rdata["msg"]=$content["err_code_des"];
            $rdata["status"]="0";
        }
        if($content["return_code"]=="FAIL"){
            $rdata["msg"]=$content["return_msg"];
            $rdata["status"]="0";
        }
        if($content["result_code"]=="SUCCESS"){
            $rdata["trade_no"]=$content["out_refund_no"];
            $rdata["status"]="1";
        }
    }
    return $rdata;
}
function dqzhexian($borrow_name,$bid){
    //活动项目到期后自动提现
        $mmap['b.id']=$bid;
        $mmap['mz.num']=array("gt",0);
        $zfiles='mz.zpid,mz.id,mz.uid,z.zxprice,mz.num';
        $zinfo=m("member_zengpin mz")->field($zfiles)
            ->join('lzh_borrow_info b on mz.borrow_id=b.id')
            ->join('lzh_zeng_pin z on z.id=mz.zpid')->where($mmap)->select();
        $resd=true;
        foreach ($zinfo as $v){
            $money=$v["zxprice"]*$v['num'];
            $result=memberMoneyLog($v["uid"],310,$money,$borrow_name."项目赠品折现", 0, "",false);
            $data['num']=$v['num'];
            $data['type']='2';
            $data['mzpid']=$v['id'];
            $data['uid']=$v["uid"];
            $data['add_time']=time();
            $data['zpid']=$v["zpid"];
            $data['money']=$money;
            $data['status']='2';
            $data['borrow_id']=$bid;
            $data['ztype']=1;
            $data['ordernum']=sprintf('%s-%s-%s-%s', 'ZX',$v["uid"],$v['id'],time());
            $res2=M("zporder")->add($data);
            if($result&&$res2){
                $zdata['num']=0;
                $zdata['status']='2';
                $res=m('member_zengpin')->where(array("id"=>$v['id']))->save($zdata);
                if(!$res){
                    $resd=false;
                }
            }else{
                $resd=false;
            }
        }

        if($resd){
            M("borrow_info")->where("id=".$bid)->save(array("iszx"=>1));
        }
}
function ygzhexian($borrow_name,$bid){
    //预购商品自动折现
    $mmap['b.id']=$bid;
    $mmap['mz.num']=array("gt",0);

    $zfiles='mz.zpid,mz.id,mz.uid,z.zxprice,mz.num';
    $zinfo=m("member_zengpin mz")->field($zfiles)
        ->join('lzh_ys_good b on mz.borrow_id=b.id')
        ->join('lzh_zeng_pin z on z.id=mz.zpid')->where($mmap)->select();
    //var_dump($zinfo);exit;
    $resd=true;
    foreach ($zinfo as $v){
        $money=$v["zxprice"]*$v['num'];
        $result=memberMoneyLog($v["uid"],310,$money,$borrow_name."项目赠品折现", 0, "",false);
        $data['num']=$v['num'];
        $data['type']='2';
        $data['mzpid']=$v['id'];
        $data['uid']=$v["uid"];
        $data['add_time']=time();
        $data['zpid']=$v["zpid"];
        $data['money']=$money;
        $data['status']='2';
        $data['borrow_id']=$bid;
        $data['ztype']=2;
        $data['ordernum']=sprintf('%s-%s-%s-%s', 'ZX',$v["uid"],$v['id'],time());
        $res2=M("zporder")->add($data);
        if($result&&$res2){
            $zdata['num']=0;
            $zdata['status']='2';
            $res=m('member_zengpin')->where(array("id"=>$v['id']))->save($zdata);
            if(!$res){
                $resd=false;
            }
        }else{
            $resd=false;
        }
    }
    if($resd){
        M("ys_good")->where("id=".$bid)->save(array("iszx"=>1));
    }

}
function zmzhexian($bid){
    //活动项目到期后自动提现
    $mmap['zo.id']=$bid;
//    $mmap['zo.zmnum']=array("gt",0);
    $zfiles='zo.zpid,zo.id,zo.uid,z.zxprice,zo.zmnum,z.title';
    $zinfo=m("zporder zo")->field($zfiles)->join('lzh_zeng_pin z on z.id=zo.zpid')->where($mmap)->select();
    $resd=true;
    foreach ($zinfo as $v){
        if($v['zmnum']>0){
            $money=$v["zxprice"]*$v['zmnum'];
            $result=memberMoneyLog($v["uid"],310,$money,"您的赠品".$v['title']."出售超时自动折现", 0, "",false);
            //$data['smstatus']='3';
            $data['money']=$money;
            $data['zxtime']=time();
        }
        $data['smstatus']='3';
        $mmp['id']=$v['id'];
        $res2=M("zporder")->where($mmp)->save($data);
        if($result&&$res2){
            $resd=true;
        }else{
            $resd=false;
        }
    }
}
function zmquxiao($id) {
    $Model = M();
    $Model->startTrans();
    $oinfo = M("order")->where("ordernums='{$id}'")->find();
    if ($oinfo["action"] != '3') {
        $Model->commit();
    } else {
        if ($oinfo['type'] == "3") {
            $ddcc['zmnum']=array('exp',"zmnum+".$oinfo['num']);
            $ddcc['smstatus']='1';
            $mk = M('zporder')->where("id=" . $oinfo["zoid"])->save($ddcc);
        }
        $data["action"] = "9";
        $newxid = M("order")->where("ordernums='{$id}'")->save($data);
        if ($newxid) {
            $Model->commit();
        } else {
            $Model->rollback();
        }
    }
}

function httpGet1($url) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, 500);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_URL, $url);
    $res = curl_exec($curl);
    curl_close($curl);

    $xmlString = <<<XML
$res
XML;
    $data = simplexml_load_string($xmlString,'SimpleXMLElement',LIBXML_NOCDATA);
    $json = json_encode($data);
    $array = json_decode($json,TRUE);
    return $array;
}
function convertAmountToCn($amount, $type = 1) {
    // 判断输出的金额是否为数字或数字字符串
    if(!is_numeric($amount)){
        return "要转换的金额只能为数字!";
    }

    // 金额为0,则直接输出"零元整"
    if($amount == 0) {
        return "零元整";
    }

    // 金额不能为负数
    if($amount < 0) {
        return "要转换的金额不能为负数!";
    }

    // 金额不能超过万亿,即12位
    if(strlen($amount) > 12) {
        return "要转换的金额不能为万亿及更高金额!";
    }

    // 预定义中文转换的数组
    $digital = array('零', '壹', '贰', '叁', '肆', '伍', '陆', '柒', '捌', '玖');
    // 预定义单位转换的数组
    $position = array('仟', '佰', '拾', '亿', '仟', '佰', '拾', '万', '仟', '佰', '拾', '元');

    // 将金额的数值字符串拆分成数组
    $amountArr = explode('.', $amount);

    // 将整数位的数值字符串拆分成数组
    $integerArr = str_split($amountArr[0], 1);

    // 将整数部分替换成大写汉字
    $result = '';//前缀
    $integerArrLength = count($integerArr);     // 整数位数组的长度
    $positionLength = count($position);         // 单位数组的长度
    for($i = 0; $i < $integerArrLength; $i++) {
        // 如果数值不为0,则正常转换
        if($integerArr[$i] != 0){
            $result = $result . $digital[$integerArr[$i]] . $position[$positionLength - $integerArrLength + $i];
        }else{
            // 如果数值为0, 且单位是亿,万,元这三个的时候,则直接显示单位
            if(($positionLength - $integerArrLength + $i + 1)%4 == 0){
                $result = $result . $position[$positionLength - $integerArrLength + $i];
            }
        }
    }

    // 如果小数位也要转换
    if($type == 0) {
        // 将小数位的数值字符串拆分成数组
        $decimalArr = str_split($amountArr[1], 1);
        // 将角替换成大写汉字. 如果为0,则不替换
        if($decimalArr[0] != 0){
            $result = $result . $digital[$decimalArr[0]] . '角';
        }
        // 将分替换成大写汉字. 如果为0,则不替换
        if($decimalArr[1] != 0){
            $result = $result . $digital[$decimalArr[1]] . '分';
        }
    }else{
        $result = $result . '整';
    }
    return $result;
}

