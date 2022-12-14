<?php
// 本类由系统自动生成，仅供测试用途
class QianbaoybAction extends HCommonAction {
	public $urls;
	public function __construct()
    {
        parent::__construct();

       	$this->urls="https://".$_SERVER['SERVER_NAME'];

       	// $aa="oid_partner=201907180002049144&sign_type=RSA&sign=Jg7KEYvqYV%2FOoLyrxlsObl4eFjOjdAb2W5c%2Bp%2FpAvspDS4BC1K6reZ7%2B%2BHD8%2BEg77ihuqXvHnsWhxoivyLq%2BqnKbF6b7VzzdnpiPSTWmxa2VpB8EHCouQ3qe7vQQl19fzzhUwWtat7hSWXqQJeL4f2RBwUFpGkBa0dwQgqhOmwM%3D&dt_order=20190810111531&no_order=LLQB-20190810-831586&oid_paybill=2019081062344017&money_order=0.01&result_pay=SUCCESS&settle_date=20190810&info_order=&pay_type=2&bank_code=03080000";
       	// parse_str($aa);

       
    }
	public function kaihufh(){
    	if (empty($_POST) && false !== strpos($this->contentType(), 'application/json')) {
            $content = file_get_contents('php://input');
            $post    = (array)json_decode($content, true);
        } else {
            $post = $_POST;
        }
        if($post["errorCode"]=='0000'){
        	$kdata["iskaihu"]=1;
			M('member_info')->where("lianlianid=$userid")->save($kdata);
			$this->success("恭喜您开户成功",__APP__.'/Member/Qianbao/index');
        }else{
        	$this->error("开户操作未成功",__APP__.'/Member/Qianbao/index');
        }
    }
    
	public function notifyUrl(){
    	$post = file_get_contents('php://input');
   //  	if($post["errorCode"]=='0000'){
   //      	$kdata["iskaihu"]=1;
			// M('member_info')->where("lianlianid=$userid")->save($kdata);
			// //$this->success("恭喜您开户成功",__APP__.'/Member/Qianbao/index');
   //      }else{
   //      	//$this->error("开户操作未成功",__APP__.'/Member/Qianbao/index');
   //      }
    }
    //提现返回
    public function dtiNotify(){
    	$post = file_get_contents('php://input');
    	// parse_str($post);
    	$status=1;

        $post    = (array)json_decode($post, true);
     //var_dump($post["result_pay"]);exit();
        //writeLog($post);

        $no_order=$_POST["no_order"];

        $model = D("member_withdraw");

        if($_POST["result_pay"]=="SUCCESS"){
        	$status=1;
        }else{
        	$status=3;
        }
		$jsons["ret_code"]="0000";
	    $jsons["ret_msg"] ="交易成功";
	    outJson($jsons);
	}
    //提现返回
    public function tiNotify(){
    	$post = file_get_contents('php://input');
    	// parse_str($post);
    	$status=1;

        $post    = (array)json_decode($post, true);
     //var_dump($post["result_pay"]);exit();
        writeLog($post);

        $no_order=$_POST["no_order"];

        $model = D("member_withdraw");

        if($_POST["result_pay"]=="SUCCESS"){
        	$status=1;
        }else{
        	$status=3;
        }
	
		

		$deal_info ="连连提现";

		$secondfee = 0;

        // if (false === $model->create()) {

        //     $this->error($model->getError());

        // }

        //保存当前数据对象

		$model->withdraw_status = $status;

		$model->deal_info = $deal_info;

		$model->deal_time=time();

		$model->deal_user="钱包系统";

		////////////////////////
		$field= 'w.*,w.id,w.uid,(mm.account_money+mm.back_money) all_money';
		$vo = M("member_withdraw w")->where("no_order='".$no_order."'")->field($field)->join("lzh_member_money mm on w.uid = mm.uid")->find();

		$id=$vo["id"];
		$model->id=$id;
		$um = M('members')->field("user_name,user_phone")->find($vo['uid']);
		if($vo['withdraw_status']<>3 && $status==3){
			addInnerMsg($vo['uid'],"您的提现申请审核未通过","您的提现申请审核未通过");
			if( ($vo['all_money']-$vo['second_fee'])<=0 ){
				  memberMoneyLog($vo['uid'],12,$vo['withdraw_money'],"提现未通过,返还,其中提现金额：".$vo['withdraw_money']."元，手续费：".$vo['second_fee']."元",'0','@网站管理员@',$vo['second_fee']);
			}else{
				 memberMoneyLog($vo['uid'],12,$vo['withdraw_money'],"提现未通过,返还",'0','@网站管理员@',$vo['second_fee']);
			}
			$model->success_money = 0;
			// memberMoneyLog($vo['uid'],12,$vo['withdraw_money']+$vo['withdraw_fee'],"提现未通过,返还",'0','@网站管理员@');
		}else if($vo['withdraw_status']<>2 && $status==2){
			addInnerMsg($vo['uid'],"您的提现已完成","您的提现已完成");
			if( ($vo['all_money'] - $vo['second_fee'])<0 ){
				memberMoneyLog($vo['uid'],29,-($vo['withdraw_money']-$vo['second_fee']),"提现成功,扣除实际手续费".$vo['second_fee']."元，减去冻结资金，到帐金额".($vo['withdraw_money']-$vo['second_fee']),'0','@网站管理员@',0);
				//$model->withdraw_money = $vo['withdraw_money']+$vo['withdraw_fee'];
				$model->success_money = $vo['withdraw_money']-$vo['second_fee'];
			}else{
				memberMoneyLog($vo['uid'],29,-($vo['withdraw_money']),"提现成功,扣除实际手续费".$vo['second_fee']."元，减去冻结资金，到帐金额".($vo['withdraw_money']),'0','@网站管理员@');
				$model->success_money = $vo['withdraw_money'];
			}
			SMStip("withdraw",$um['user_phone'],array("#USERANEM#","#MONEY#"),array($um['user_name'],($vo['withdraw_money']-$vo['second_fee'])));
		}elseif($vo['withdraw_status']<>1 && $status==1){
	
			memberMoneyLog($vo['uid'],36,0,"提现申请已通过，扣除实际手续费".$secondfee."元，到帐金额".($vo['withdraw_money'])."元",'0','@网站管理员@',$secondfee);

			//exit;
			$model->success_money = $vo['withdraw_money'];
			//}
			$model->withdraw_fee = $vo['withdraw_fee'];
			$model->second_fee = $secondfee;

		}

		//////////////////////////
		$result = $model->save();
        if ($result) { //保存成功
           $jsons["ret_code"]="0000";
	       $jsons["ret_msg"] ="交易成功";
	       outJson($jsons);
        }else{
           $jsons["ret_code"]=$_POST["ret_code"];
	       $jsons["ret_msg"] =$_POST["ret_msg"];
	       outJson($jsons);
        }
    }
    //充值返回
    public function czNotify(){
    	$post = file_get_contents('php://input');
    	// parse_str($post);
    	$status=1;

       $post    = (array)json_decode($post, true);
       //var_dump($post["result_pay"]);exit();
        writeLog($post);
   	   //$id=$no_order;
		if($post["result_pay"]=="SUCCESS"){
			$status = 1;
		}else{
			$status = 3;
		}
		$id= M('member_payonline')->where("nid='".$post["no_order"]."'")->getField('id');
	    //writeLog( M('member_payonline')->getlastsql());
		$statusx = M('member_payonline')->getFieldById($id,"status");
		if ($statusx!=0){
			$this->error("请不要重复提交表单");exit();
		}
	
		if($status==1){
			$vo = M('member_payonline')->field('money,fee,uid,way')->find($id);
// 			var_dump($vo);  
// 				die;
			$newid = memberMoneyLog($vo['uid'],27,$vo['money']-$vo['fee'],$post["deal_info"]);
			//writeLog($newid);
			if($newid){
				//分销返佣
				// distribution_maid($id);
				$zv=$vo['money']-$vo['fee'];
			    notice1(6, $vo['uid'], $data = array("MONEY"=>$zv));
				////////////////////////////
				if($vo['way']=="off"){
					$tqfee = explode( "|", $this->glo['offline_reward']);
					$fee[0] = explode( "-", $tqfee[0]);
					$fee[2] = explode( "-", $tqfee[2]);
					$fee[1] = floatval($tqfee[1]);
					$fee[3] = floatval($tqfee[3]);
					$fee[4] = floatval($tqfee[4]);
					$fee[5] = floatval($tqfee[5]);
					if($vo['money']>=$fee[0][0] && $vo['money']<=$fee[0][1]){
						$fee_rate = 0<$fee[1]?($fee[1]/1000):0;
					}else if($vo['money']>$fee[2][0] && $vo['money']<=$fee[2][1]){
						$fee_rate = 0<$fee[3]?($fee[3]/1000):0;
					}else if($vo['money']>$fee[4]){
						$fee_rate = 0<$fee[5]?($fee[5]/1000):0;
					}else{
						$fee_rate = 0;
					}
					$newidx = memberMoneyLog($vo['uid'],32,$vo['money']*$fee_rate,"线下充值奖励");
				}
				/////////////////////////////
				/*
				$offline_reward = explode("|",$this->glo['offline_reward']);
				if($vo['money']>$offline_reward[0]){
					$fee_rate = 0<$offline_reward[1]?($offline_reward[1]/1000):0;
					$newidx = memberMoneyLog($vo['uid'],32,$vo['money']*$fee_rate,"线下充值奖励");
				}*/
				$save['deal_user'] = "钱包系统";
				$save['deal_uid'] = 101;
				$save['deal_info'] = "";
				$save['status'] = 1;
		
				// var_dump($save,"<br/>");
				M('member_payonline')->where("id={$id}")->save($save);
					//writeLog(M('member_payonline')->getlastsql());

				//M('member_payonline')->commit();
				// var_dump(M('member_payonline')->getlastsql(),"<br/>",$save,"<br/>2");
				// die;
				$vx = M('members')->field("user_name,user_phone")->find($vo['uid']);
				if($vo['way']=="off") SMStip("payoffline",$vx['user_phone'],array("#USERANEM#","#MONEY#"),array($vx['user_name'],$vo['money']));
				else  {
					addInnerMsg($vo['uid'],'成功充值'.$vo['money'].'元','您刚刚成功充值了'.$vo['money'].'元！');
					SMStip("payonline",$vx['user_phone'],array("#USERANEM#","#MONEY#"),array($vx['user_name'],$vo['money']));
				};

				//入账记录 开始
				inAccountMoney($vo['uid'],$vo['money'],$vo['way']=="off" ? '2' : '1');
				//入账记录 结束
				$this->success("处理成功");
			}
			else $this->error("处理失败");
		}else{
			$save['deal_user'] = session('adminname');
			$save['deal_uid'] = 101;
			$save['deal_info'] = "";
			$save['status'] = 3;
			$newid = M('member_payonline')->where("id={$id}")->save($save);
		}
    }
    //支付返回
    public function zfNotify(){
    	$post = file_get_contents('php://input');
    	// parse_str($post);
    	$status=1;

       $post    = (array)json_decode($post, true);
     //var_dump($post["result_pay"]);exit();
        writeLog($post);
   	//$id=$no_order;
		if($post["result_pay"]=="SUCCESS"){
			$status = 1;
		}else{
			$status = 3;
		}
		$id= M('member_payonline')->where("nid='".$post["no_order"]."'")->getField('id');
	    //writeLog( M('member_payonline')->getlastsql());
		$statusx = M('member_payonline')->getFieldById($id,"status");
		if ($statusx!=0){
			$this->error("请不要重复提交表单");exit();
		}
	
		if($status==1){
			$vo = M('member_payonline')->field('money,fee,uid,way')->find($id);
// 			var_dump($vo);  
// 				die;
			$newid = memberMoneyLog($vo['uid'],27,$vo['money']-$vo['fee'],$post["deal_info"]);
			//writeLog($newid);
			if($newid){
				//分销返佣
				// distribution_maid($id);
				$zv=$vo['money']-$vo['fee'];
			    notice1(6, $vo['uid'], $data = array("MONEY"=>$zv));
				////////////////////////////
				if($vo['way']=="off"){
					$tqfee = explode( "|", $this->glo['offline_reward']);
					$fee[0] = explode( "-", $tqfee[0]);
					$fee[2] = explode( "-", $tqfee[2]);
					$fee[1] = floatval($tqfee[1]);
					$fee[3] = floatval($tqfee[3]);
					$fee[4] = floatval($tqfee[4]);
					$fee[5] = floatval($tqfee[5]);
					if($vo['money']>=$fee[0][0] && $vo['money']<=$fee[0][1]){
						$fee_rate = 0<$fee[1]?($fee[1]/1000):0;
					}else if($vo['money']>$fee[2][0] && $vo['money']<=$fee[2][1]){
						$fee_rate = 0<$fee[3]?($fee[3]/1000):0;
					}else if($vo['money']>$fee[4]){
						$fee_rate = 0<$fee[5]?($fee[5]/1000):0;
					}else{
						$fee_rate = 0;
					}
					$newidx = memberMoneyLog($vo['uid'],32,$vo['money']*$fee_rate,"线下充值奖励");
				}
				/////////////////////////////
				/*
				$offline_reward = explode("|",$this->glo['offline_reward']);
				if($vo['money']>$offline_reward[0]){
					$fee_rate = 0<$offline_reward[1]?($offline_reward[1]/1000):0;
					$newidx = memberMoneyLog($vo['uid'],32,$vo['money']*$fee_rate,"线下充值奖励");
				}*/
				$save['deal_user'] = "钱包系统";
				$save['deal_uid'] = 101;
				$save['deal_info'] = "";
				$save['status'] = 1;
		
				// var_dump($save,"<br/>");
				M('member_payonline')->where("id={$id}")->save($save);
					//writeLog(M('member_payonline')->getlastsql());

				//M('member_payonline')->commit();
				// var_dump(M('member_payonline')->getlastsql(),"<br/>",$save,"<br/>2");
				// die;
				$vx = M('members')->field("user_name,user_phone")->find($vo['uid']);
				if($vo['way']=="off") SMStip("payoffline",$vx['user_phone'],array("#USERANEM#","#MONEY#"),array($vx['user_name'],$vo['money']));
				else  {
					addInnerMsg($vo['uid'],'成功充值'.$vo['money'].'元','您刚刚成功充值了'.$vo['money'].'元！');
					SMStip("payonline",$vx['user_phone'],array("#USERANEM#","#MONEY#"),array($vx['user_name'],$vo['money']));
				};

				//入账记录 开始
				inAccountMoney($vo['uid'],$vo['money'],$vo['way']=="off" ? '2' : '1');
				//入账记录 结束
				 $jsons["ret_code"]="0000";
			     $jsons["ret_msg"] ="交易成功";
			     outJson($jsons);
			}
			//else $this->error("处理失败");
		}else{
			$save['deal_user'] = "钱包系统";
			$save['deal_uid'] = 101;
			$save['deal_info'] = "";
			$save['status'] = 3;
			$newid = M('member_payonline')->where("id={$id}")->save($save);
		}
    }
    function pre($content) {
        echo "<pre>";
        print_r($content);
        echo "</pre>";
    }	
}
