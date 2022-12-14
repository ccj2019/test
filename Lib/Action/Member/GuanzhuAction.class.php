<?php
// 本类由系统自动生成，仅供测试用途
class GuanzhuAction extends MCommonAction {
	
	 public function index(){
            //分页处理
            import("ORG.Util.Page");
            $count = M('pro_guanzhu') ->count('id');
            $p = new Page($count, 10);
            $page = $p->show();
            $Lsql = "{$p->firstRow},{$p->listRows}";
    	    $list= M('pro_guanzhu')->join("lzh_borrow_info  on lzh_borrow_info.id=lzh_pro_guanzhu.bid ") ->limit($Lsql)->order("lzh_pro_guanzhu.id desc")->select();
            $this->assign('list', $list);
            $this->assign('pagebar', $page);
            $this->assign('count', $count);
            $this->assign('glomodulename','tendout');
    	 	$this->display();
	 }
     public function quxiaoguanzhu()
    {

           
        $b = M('pro_guanzhu')->where('bid='.$_POST['tid']." and uid = {$this->uid}")->count('id');
        if ($b>0) {
                // M('borrow_info')->where("id =".$_POST['tid'])->setDec("gz_num",1);
            $Model = new Model(); // 实例化一个model对象 没有对应任何数据表

            $sql="update lzh_borrow_info set  gz_num=gz_num-1  where id =".$_POST['tid'];
                     //echo $sql;die;
            $Model->query($sql);


            M('pro_guanzhu')->where('bid='.$_POST['tid']." and uid = {$this->uid}")->delete();
            $jsons['message'] = '取消关注成功';
            $jsons['status'] = '1';
        }
        exit(json_encode($jsons));
    }


	 public function bespeak(){
         $pre = C('DB_PREFIX');
         //分页处理
         import("ORG.Util.Page");
         $where['b.bespeak_uid'] = Array('eq',$this->uid);
         $where['b.bespeak_point'] = Array('neq',1);
         $where['bi.bespeak_able'] = Array('eq',1);
         $count = $list = M('borrow_info bi')
             ->join("{$pre}bespeak b on bi.id = b.borrow_id")
             ->where($where)
             ->order('b.id DESC')
             ->count('b.borrow_id');
         $p = new Page($count, 10);
         $page = $p->show();
         $field = "b.borrow_id,bi.borrow_name,bi.borrow_img,bi.start_time,bi.bespeak_days,b.bespeak_money,b.bespeak_status,b.add_time";
         $Lsql = "{$p->firstRow},{$p->listRows}";
         $list = M('borrow_info bi')
             ->distinct(true)
             ->field($field)
             ->join("{$pre}bespeak b on bi.id = b.borrow_id")
             ->where($where)
             ->order('b.id DESC')
             ->limit($Lsql)
             ->select();
         $this->assign('list', $list);
         $this->assign('pagebar', $page);
         $this->assign('count', $count);
         $this->assign('glomodulename','tendout');
         $this->display();
     }     
     public function zdlist(){
        $bespeak=M("bespeak i");
        //分页处理
        import("ORG.Util.Page");
        $map['bespeak_uid'] = $this->uid;
        $map['bespeak_point'] ='1.00';
        
        $size=10;
        $count =$bespeak ->where($map)->join("lzh_borrow_info b ON b.id=i.borrow_id")->count('i.id');
        $p = new Page($count, $size);
        $page = $p->show();
        $Lsql = "{$p->firstRow},{$p->listRows}";
        $field="i.*,b.borrow_name,b.borrow_img,b.borrow_type";
        $arr=array("智投中","已成功","投资失败");

        $typelist = get_type_leve_list('0','Productcategory');
        $list=$bespeak ->where($map)->field($field)->join("lzh_borrow_info b ON b.id=i.borrow_id")->order('i.add_time DESC')->limit($Lsql)->select();/**/
        foreach ($list as $k => $v) {
            $list[$k]["status"] =$arr[$v["bespeak_status"]];
            $list[$k]["leixing"] =$typelist[$v["borrow_type"]]["type_name"];
        }
        $this->assign('list', $list);
        $this->assign('page', $page);
        $this->assign('count', $count);

        $this->display();
     }

     public function zidong(){
         $half_a_year_before = time() - $this->glo["new_user_time"]*24*60*60;
        $where = null;
        $where['investor_uid'] = array('eq',"{$this->uid}");
        $where['add_time'] = array(array('egt',"{$half_a_year_before}"),array('elt',time()));
        $where['borrow_id'] = array('neq',1);
        $newUser = count(M('borrow_investor')->where($where)->find());



        $where = null;
        $where['bespeak_uid'] = array('eq',"{$this->uid}");
        $where['add_time'] = array(array('egt',"{$half_a_year_before}"),array('elt',time()));
        $where['borrow_id'] = array('neq',1);
        $bespeakUser=count(M('bespeak')->where($where)->find());
        $newUser = max($newUser,$bespeakUser);

   
        if ($newUser > 0) {//老用户
             $map["new_user_only"]=array("neq","1");
             $map["zhitou_able"]=0;
        }else{//新用户
             $map["zhitou_able"]=array("neq","1");
        }

        $map["borrow_status"]=1;

        $map["start_time"]=array("gt",time());
        $map["borrow_uid"]=array("neq",$this->uid);
        $map['id'] =array('neq',1);
        $zdlist=M('borrow_info')->where($map)->select();

        foreach ($zdlist as $k => $v) {

            //预约已投金额
            $bespeak_invest_money = M('bespeak')->where("borrow_id={$v['id']} and bespeak_status = 1")->sum('bespeak_money');
            //非预约已投金额
            $bespeak_not_invest_money = bcsub($v["has_borrow"], $bespeak_invest_money, 2);
            //非预约金额
            $not_invest_money = bcsub($v["borrow_money"], $v["bespeak_money"], 2);
            //可投金额
            $can_invest_money = bcsub($not_invest_money, $bespeak_not_invest_money, 2);

            $zdlist[$k]["shengyu"] =$can_invest_money;
            if($v["is_huodong"]==1&&!empty($v['zpid'])){
                $zdlist[$k]["zpname"]=M("zeng_pin")->where('id='.$v['zpid'])->getField('title');
                //$ytmoney=M('borrow_investor')->where("borrow_id="+$v["id"]+" and investor_uid= {$this->uid}")->sum('investor_capital');
                $ytmoney=M('bespeak')->where(array("borrow_id"=>$v["id"],"bespeak_uid"=>$this->uid))->sum('bespeak_money');
                $zdlist[$k]["yt_money"]=$ytmoney%$v['huodongnum'];
            }
        }
 
  
        $this->assign("zdlist",$zdlist);


        $vm = getMinfo($this->uid,'m.pin_pass,mm.account_money,mm.back_money,mm.money_experience,mm.yubi');
        $amoney = $vm['account_money']+$vm['back_money'];
        
        $pin_pass = $vm['pin_pass'];
        $has_pin  = (empty($pin_pass)) ? "no" : "yes";
        $this->assign("has_pin",$has_pin);

        $this->assign("account_money",$amoney);
        $this->assign("yubi",$vm["yubi"]);

    
        $this->display();
     }

     public function doadd(){

        $pre = C('DB_PREFIX');
        if (!$this->uid) {
            ajaxmsg('', 0);
        }

         /****** 防止模拟表单提交 *********/
        // if ($_POST['form_id']=='') {
        //     ajaxmsg('数据校验有误！'.$_POST['form_id'], 0);
        //     exit;
        // }
        if(!M()->autoCheckToken($_POST)) {
           ajaxmsg('数据校验有误！', 0);
            exit;
        }
     

        $pin = md5($_POST['pin']);
        $borrow_id = (int) ($_POST['borrow_id']);
        $money = (float) ($_POST['money']);

        $is_experience = isset($_POST['is_experience']) && 1 == $_POST['is_experience'] ? 1 : 0;
        $member_interest_rate_id = 0;
        $bonus_id = isset($_POST['bonus_id']) ? (int) ($_POST['bonus_id']) : 0;
        if ($bonus_id > 0) {
            $bs = M('member_bonus')->where("id='{$bonus_id}'")->find();
            $canInvestMoneys = canInvestMoney($this->uid, $borrow_id, $money+$bs['money_bonus'], 0, $is_experience, '0', $bonus_id, text($_POST['borrow_pass']));

            if (0 == $canInvestMoneys['status']) {
                ajaxmsg($canInvestMoneys['tips'], $canInvestMoneys['status']);
            }
            $money_bonuss = $canInvestMoneys['money_bonus'];
            $money = (float) ($money + $money_bonuss);
        }

        $memberinterest_id = isset($_POST['memberinterest_id']) ? (int) ($_POST['memberinterest_id']) : 0;

        $vm = getMinfo($this->uid, 'm.pin_pass,mm.account_money,mm.back_money,mm.money_experience,mm.yubi');

        if($vm['pin_pass']==''){
            ajaxmsg('支付密码为空，请您设置密码！', 12);
            exit;
        }

        $pin_pass = $vm['pin_pass'];
        if ($pin != $pin_pass) {
            ajaxmsg('支付密码错误，请重试！', 0);
            exit;
        }
        $amoney = $vm['account_money'] + $vm['back_money']+$vm["yubi"];
        $uname = session('u_user_name');
        $amoney = (float) $amoney;

        $binfo = M('borrow_info')->field('id,borrow_money,borrow_status,has_borrow,has_vouch,borrow_min,borrow_type,password,pause,max_limit,bespeak_money,start_time')->find($borrow_id);
        $binfo['borrow_max'] = $binfo['borrow_min']*$binfo['max_limit'];
        $minfo = getMinfo($this->uid, true);
        $levename = getLeveId($minfo['credits']);
        
        //项目开标后不能自动投
        if ($binfo['start_time']<time()) {
            ajaxmsg('此标已经开始众筹，请到项目列表里面投资！', 4);
            exit;
        }
        $ids = M('members_status')->getFieldByUid($this->uid, 'id_status');
        if($ids!=1){
            ajaxmsg("请先通过实名认证后再进行投标。",3);
        }
        $phones = M('members_status')->getFieldByUid($this->uid,'phone_status');
        if($phones!=1){
            ajaxmsg("请先通过手机认证后再进行投标。",3);
        }
        if($binfo['pause']==1){
            ajaxmsg("此标当前已经截标，请等待管理员开启。",0);
        }

        // 50 > 10
        if ($money < $binfo['borrow_min']) {
            ajaxmsg('此标最小投标金额为'.$binfo['borrow_min'].'元', 3);
            exit();
        }
        if ($money > $binfo['borrow_max'] and 0 != $binfo['borrow_max']) {
  
            ajaxmsg("此标最大投标金额为".$binfo['borrow_max']."元",3);
            exit();
        }

        if ($binfo['has_vouch'] < $binfo['borrow_money'] && 2 == $binfo['borrow_type']) {
            ajaxmsg("此标担保还未完成，您可以担保此标或者等担保完成再投标",3);
            exit();
            
        }

        $half_a_year_before = time() - $this->glo["new_user_time"]*24*60*60;
        $where = null;
        $where['investor_uid'] = array('eq',"{$this->uid}");
        $where['add_time'] = array(array('egt',"{$half_a_year_before}"),array('elt',time()));
        $where['borrow_id'] = array('neq',1);
        $newUser = count(M('borrow_investor')->where($where)->find());

        $where = null;
        $where['bespeak_uid'] = array('eq',"{$this->uid}");
        $where['add_time'] = array(array('egt',"{$half_a_year_before}"),array('elt',time()));
        $where['borrow_id'] = array('neq',1);
        $bespeakUser = count(M('bespeak')->where($where)->find());
        $newUser = max($newUser,$bespeakUser);

        $new_user_only = M('borrow_info')->field('new_user_only')->find($borrow_id)['new_user_only'];
        if ($new_user_only == 1 && $newUser > 0) {
            ajaxmsg("此标是新手专享标", 3);
            exit();
        }

        //投标总数检测
        $bespeak=M("bespeak");
        $capital = $bespeak->where("borrow_id={$borrow_id} AND bespeak_uid={$this->uid}")->sum('bespeak_money');

        if (($capital + $money) > $binfo['borrow_max'] && $binfo['borrow_max'] > 0) {
            $xtee = $binfo['borrow_max'] - $capital;
            ajaxmsg("您已预约或自动投总金额{$capital}元，上限为{$binfo['borrow_max']}元，你最多只能再投{$xtee}元", 3);
            exit();
        }



         //预约已投金额
         $bespeak_invest_money = M('bespeak')->where("borrow_id={$binfo['id']} and bespeak_status = 1")->sum('bespeak_money');
         //非预约已投金额
         $bespeak_not_invest_money = bcsub($binfo["has_borrow"], $bespeak_invest_money, 2);
         //非预约金额
         $not_invest_money = bcsub($binfo["borrow_money"], $binfo["bespeak_money"], 2);
         //可投金额
         $need = bcsub($not_invest_money, $bespeak_not_invest_money, 2);



        $caninvest = bcsub($need, $binfo['borrow_min'], 2);

        if ($money > $caninvest && 0 != ($need - $money)) {
            if (0 == (int) $need or '0.00' == $need) {
                ajaxmsg("尊敬的{$uname}，该项目自投投资已完成，请选择其他项目！",0);
                exit();
            }
            
        }
        if($money > $need) { 
            $money=$need;
        }

        $canInvestMoney = canInvestMoney($this->uid, $borrow_id, $money, 0, $is_experience, '0', $bonus_id, text($_POST['borrow_pass']));
    
        if (0 == $canInvestMoney['status']) {
            ajaxmsg($canInvestMoney['tips'], $canInvestMoney['tips_type']);
            exit();
        }
        $money_bonus = $canInvestMoney['money_bonus'];
        $yubis=0;
        if (2 == $canInvestMoney['money_type']) {
            if ($memberinterest_id > 0) {
                $msg = "尊敬的{$uname}，您的体验金账户余额为{$vm['money_experience']}元，您确认投标{$money}元吗？";
            } else {
                $msg = "尊敬的{$uname}，您的体验金账户余额为{$vm['money_experience']}元，您确认投标{$money}元吗？";
            }
            ajaxmsg($msg, 1);
            exit();
        } elseif (3 == $canInvestMoney['money_type']) {
            if ($memberinterest_id > 0) {
                $msg = "尊敬的{$uname}，您确认投标{$money}元（使用{$money_bonus}元红包），投资吗？";
            } else {
                $msg = "尊敬的{$uname}，您确认投标{$money}元（使用{$money_bonus}元红包），投资吗？";
            }
            ajaxmsg($msg, 1);
            exit();
        } elseif ($money <= $amoney) {

            $data["borrow_id"]=$borrow_id;
            $data["bespeak_uid"]=$this->uid;
            $data["bespeak_money"]=$money;
            $data["add_time"]=time();
            $data["bespeak_status"]=0;
            $data["bespeak_point"]=1;

            if($vm["yubi"]>=$money){
                $yubis=$money;
            }else{
                $yubis=$vm["yubi"];
            }
            $data["yubi"]=$yubis;
            $data["bespeak_money"]=$money;

            $borrowInfo = M("borrow_info");
            $borrowInfo->startTrans();

            $borrow_info=M("borrow_info");
            $bmap["id"]=$borrow_id;
            $bwinfo=$borrow_info->where($bmap)->find();

            //自动投记录
            $res=$bespeak->add($data);



            if($bwinfo&&$res){
                $bespeak_money=$bwinfo["bespeak_money"];
                if($bespeak_money==null){
                    $bespeak_money=0;
                }
                $bespeak_money=($bespeak_money+$money);
                $bdata["bespeak_money"]= $bespeak_money;
                //修改标可投金额
                $res1=$borrow_info->where($bmap)->save($bdata);
                if($res1){
                    if(($minfo["account_money"]+$minfo['yubi'])>=$money){
                       //writeLog($investinfo);
                       $res2 = memberMoneyLog($this->uid, 222, 0 - $money, "对（{$bwinfo["borrow_name"]}）进行自动投标", $bwinfo['borrow_uid'], "", false,$yubis);
                        if($res2===true&&$res1){
                            $borrowInfo->commit();
                            $msg = "尊敬的{$uname}，您预约的自动投{$money}元，已经成功！";
                            ajaxmsg($msg, 1);
                            exit();
                        }else{
                            $borrowInfo->rollback();
                            $msg = "尊敬的{$uname}，您预约的自动投{$money}元，未成功！";
                            ajaxmsg($msg, 2);
                            exit();
                        }
                    }else{
                        $msg = "您的可用余额不足！";
                        ajaxmsg($msg, 2);  
                        exit();
                    }
                }
            }else{
                $msg = "尊敬的{$uname}，您预约的自动投{$money}元，未成功！";
                ajaxmsg($msg, 2);  
                exit();
            }
        } else {
            ajaxmsg($msg, 2);
            exit();
        }


     }
 
}
