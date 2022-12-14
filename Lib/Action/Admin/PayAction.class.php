<?php
// 全局设置
class PayAction extends ACommonAction
{
	public function payimport()
	{
		$action = empty($_POST['action']) ? '' : $_POST['action'];
		if($action=='doimport'){			
			if(!empty($_FILES['importfile']['name'])){				
				$this->saveRule = 'uniqid';
				$this->savePathNew = C('ADMIN_UPLOAD_DIR').'import/' ;
				$this->thumbMaxWidth = C('ARTICLE_UPLOAD_W');
				$this->thumbMaxHeight = C('ARTICLE_UPLOAD_H');
				$info = $this->CUpload();
				$data['import_file'] = $info[0]['savepath'].$info[0]['savename'];
				if(is_file($data['import_file'])){

					set_time_limit(0); //脚本不超时

					//include 'PHPExcel/IOFactory.php';	
					import("ORG.Net.PHPExcel.PHPExcel");	 
					$inputFileType = 'Excel5';    //这个是读 xls的
					if(strstr($data['import_file'], '.xlsx'))
					$inputFileType = 'Excel2007';
					//$inputFileName = './sampleData/example2.xls';
					$inputFileName = $data['import_file'];		 
			       
			        $objReader = PHPExcel_IOFactory::createReader($inputFileType);
			        $objPHPExcel = $objReader->load($inputFileName);
			        /*
			        $sheet = $objPHPExcel->getSheet(0);
			        $highestRow = $sheet->getHighestRow(); //取得总行数
			        $highestColumn = $sheet->getHighestColumn(); //取得总列
			        */   
			        $objWorksheet = $objPHPExcel->getActiveSheet();//取得总行数
			        $highestRow = $objWorksheet->getHighestRow();//取得总列数
			        $highestColumn = $objWorksheet->getHighestColumn();
			        $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);//总列数
			        
			        $headtitle=array();
			        $list = array();					        
			        $m = M('member_payonline');	        
			        for ($row = 2;$row <= $highestRow;$row++)
			        {
						//echo 1;
			            $strs=array();
			            //注意highestColumnIndex的列数索引从0开始
			            for ($col = 0;$col < $highestColumnIndex;$col++)
			            { 
			                $strs[$col] =$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
			            } 
			            //获取用户，判断用户是否存在。
			            //格式为（用户名，充值金额，站内信标题，充值说明）
			            $uid = M("members")->where("user_name='{$strs[0]}'")->getfield('id');
			            $data = array();
			            if($uid){
							if(is_numeric($strs[1])){
							  $data['uid'] = $uid;	
							  $data['nid'] = 'online';
							  $data['money'] = $strs[1];
							  $data['fee'] = 0;
							  $data['way'] = 'online';
							  $data['status'] = 0;
							  $data['add_time'] = time();
							  $data['add_ip'] = get_client_ip();
							  $data['tran_id'] = '0000000000';
							  $data['off_bank'] = '网站导入';
							  $data['off_way'] = $strs[3];
							  $data['deal_user'] = '';
							  $data['deal_uid'] = '0';
							}
			            }
			            $newid = 0;
			            
			            if(is_array($data) && count($data)>0){
			            	$newid = $m->add($data);
			            	$list[] = array(
			            		'id' => $newid,
			            		'msg_title' => $strs[2],
			            		'desc' => $strs[3]
			            	);
			            }
			        }	
					if(count($list)>0) {
						$this->_audit($list);
						$this->success("网站充值导入成功!");
						die;
					}
				}
			}
			$this->success("网站充值提交失败，请重试");
		}
		$this->display();
	}
	
	/*上传后审核*/
	private function _audit(array $list){
		foreach ($list as $key => $value) {
			$this->_doEdit($value['id'],$value['msg_title'],$value['desc']);
		}
	}
	private function _doEdit($id,$msg_title,$desc){
		$id=intval($id);	
		$status = 1;
		 
		$statusx = M('member_payonline')->getFieldById($id,"status");
		if ($statusx!=0){
			$this->error("请不要重复提交表单");
			return ;
		}
		if($status==1){
			$vo = M('member_payonline')->field('money,fee,uid,way')->find($id);
			$newid = memberMoneyLog($vo['uid'],27,$vo['money']-$vo['fee'],"(导入)".$desc);
			
			if($newid){				
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
					//$newidx = memberMoneyLog($vo['uid'],32,$vo['money']*$fee_rate,"线下充值奖励");
				}
				/////////////////////////////
				/*
				$offline_reward = explode("|",$this->glo['offline_reward']);
				if($vo['money']>$offline_reward[0]){
					$fee_rate = 0<$offline_reward[1]?($offline_reward[1]/1000):0;
					$newidx = memberMoneyLog($vo['uid'],32,$vo['money']*$fee_rate,"线下充值奖励");
				}*/
				$save['deal_user'] = session('adminname');
				$save['deal_uid'] = $this->admin_id;
				$save['status'] = 1;
				M('member_payonline')->where("id={$id}")->save($save);
				$vx = M('members')->field("user_name,user_phone")->find($vo['uid']);
				//if($vo['way']=="off") SMStip("payoffline",$vx['user_phone'],array("#USERANEM#","#MONEY#"),array($vx['user_name'],$vo['money']));
				//else  SMStip("payonline",$vx['user_phone'],array("#USERANEM#","#MONEY#"),array($vx['user_name'],$vo['money']));
				addInnerMsg($vo['uid'], $msg_title, $desc);
				$this->success("处理成功");
			}
			else $this->error("处理失败");
		}else{
			$save['deal_user'] = session('adminname');
			$save['deal_uid'] = $this->admin_id;
			$save['status'] = 3;
			$newid = M('member_payonline')->where("id={$id}")->save($save);
			if($newid) $this->success("处理成功");
			else $this->error("处理失败");
		}
	}
}
