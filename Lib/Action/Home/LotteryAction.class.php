<?php 
class LotteryAction extends HCommonAction {
	public function index(){
		$m = M('lottery_config');
		$nowLottery = $m->order('id desc')->find();
		$nowLottery['prize'] = unserialize($nowLottery['prize']);
		$this->assign('nowLottery',$nowLottery);

		//获取当前活动
		$m = M('lottery_config');
		$nowLottery = $m->order('id desc')->find();
		$nowLottery['prize'] = unserialize($nowLottery['prize']);
		$prize_arr = array( 
		    '0' => array('id'=>1,'min'=>1,'max'=>29,'prize'=>'一等奖','v'=>1,'rs'=>'礼物1'), 
		    '1' => array('id'=>2,'min'=>302,'max'=>328,'prize'=>'二等奖','v'=>2,'rs'=>'礼物2'), 
		    '2' => array('id'=>3,'min'=>242,'max'=>268,'prize'=>'三等奖','v'=>5,'rs'=>'礼物3'), 
		    '3' => array('id'=>4,'min'=>182,'max'=>208,'prize'=>'四等奖','v'=>7,'rs'=>'礼物4'), 
		    '4' => array('id'=>5,'min'=>122,'max'=>148,'prize'=>'五等奖','v'=>10,'rs'=>'礼物5'), 
		    '5' => array('id'=>6,'min'=>62,'max'=>88,'prize'=>'六等奖','v'=>25,'rs'=>'礼物6'), 
		    '6' => array('id'=>7,'min'=>array(32,92,152,212,272,332), 
			'max'=>array(58,118,178,238,298,358),'prize'=>'七等奖','v'=>50,'rs'=>'礼物7') 
		); 

		foreach ($prize_arr as $key => $value) {
			$prize_arr[$key]['prize'] = $nowLottery['prize'][$key]['prize'];
			$prize_arr[$key]['v'] = $nowLottery['prize'][$key]['v'];
			$prize_arr[$key]['rs'] = $nowLottery['prize'][$key]['rs'];			
		}		
		$this->display();
	}
	public function data(){
		function getRand($proArr) { 
		    $result = ''; 		 
		    //概率数组的总概率精度 
		    $proSum = array_sum($proArr); 
		 
		    //概率数组循环 
		    foreach ($proArr as $key => $proCur) { 
		        $randNum = mt_rand(1, $proSum); 
		        if ($randNum <= $proCur) { 
		            $result = $key; 
		            break; 
		        } else { 
		            $proSum -= $proCur; 
		        } 
		    } 
		    unset ($proArr); 		 
		    return $result; 
		}
		$result['status'] = 1;
		if(!$this->uid) {
			$result['status'] = 0; 
			$result['msg'] = '您还没有登录！'; 
			exit(json_encode($result));
		}
		//获取当前活动
		$m = M('lottery_config');
		$nowLottery = $m->order('id desc')->find();
		$nowLottery['prize'] = unserialize($nowLottery['prize']);
		if(!$nowLottery['open']){
			$result['status'] = 0; 
			$result['msg'] = '活动还没有开始哦！'; 
			exit(json_encode($result));
		}

		// 抽奖次数
		$has_times = M('lottery')->where('uid = '.$this->uid.' and cfg_id = '.$nowLottery['id'])->count('id');		
		if($has_times>=$nowLottery['ptimes']){
			$result['status'] = 0; 
			$result['msg'] = '您的抽奖次数用完啦！'; 
			exit(json_encode($result));
		}

		$prize_arr = array( 
		    '0' => array('id'=>1,'min'=>1,'max'=>29,'prize'=>'一等奖','v'=>1,'rs'=>'礼物1'), 
		    '1' => array('id'=>2,'min'=>302,'max'=>328,'prize'=>'二等奖','v'=>2,'rs'=>'礼物2'), 
		    '2' => array('id'=>3,'min'=>242,'max'=>268,'prize'=>'三等奖','v'=>5,'rs'=>'礼物3'), 
		    '3' => array('id'=>4,'min'=>182,'max'=>208,'prize'=>'四等奖','v'=>7,'rs'=>'礼物4'), 
		    '4' => array('id'=>5,'min'=>122,'max'=>148,'prize'=>'五等奖','v'=>10,'rs'=>'礼物5'), 
		    '5' => array('id'=>6,'min'=>62,'max'=>88,'prize'=>'六等奖','v'=>25,'rs'=>'礼物6'), 
		    '6' => array('id'=>7,'min'=>array(32,92,152,212,272,332), 
			'max'=>array(58,118,178,238,298,358),'prize'=>'七等奖','v'=>50,'rs'=>'礼物7') 
		); 
		foreach ($prize_arr as $key => $value) {
			$prize_arr[$key]['prize'] = $nowLottery['prize'][$key]['prize'];
			$prize_arr[$key]['v'] = $nowLottery['prize'][$key]['v'];
			$prize_arr[$key]['rs'] = $nowLottery['prize'][$key]['rs'];			
		}

		
			foreach ($prize_arr as $key => $val) { 
			    $arr[$val['id']] = $val['v']; 
			} 
			 
			$rid = getRand($arr); //根据概率获取奖项id 
			 
			$res = $prize_arr[$rid-1]; //中奖项 
			$min = $res['min']; 
			$max = $res['max']; 
			if($res['id']==7){ //七等奖 
			    $i = mt_rand(0,5); 
			    $result['angle'] = mt_rand($min[$i],$max[$i]); 
			}else{ 
			    $result['angle'] = mt_rand($min,$max); //随机生成一个角度 
			} 
			$result['prize'] = $res['prize'];
			$result['rs'] = $res['rs'];
			if($result['status'] == 1){
				$addData['uid'] = $this->uid;
				$addData['prize_id'] = $res['id'];
				$addData['prize'] = $res['prize'].'：'.$res['rs'];
				$addData['add_time'] = time();
				$addData['add_ip'] = get_client_ip();
				$addData['status'] = 0;
				$addData['cfg_id'] = $nowLottery['id'];
				$rs = M('lottery')->add($addData);
				if(!$rs){
					$result['status'] = 0;
					$result['msg'] = '抽奖失败，请稍后再试！'; 
				}
			}
			echo json_encode($result); 
		}
}
?>