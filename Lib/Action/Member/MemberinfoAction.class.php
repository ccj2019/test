<?php
// 本类由系统自动生成，仅供测试用途
class MemberinfoAction extends MCommonAction {
    public function index(){
		$model=M('member_info');
		$member_info = $model->find($this->uid);
		if(!is_array($member_info)){
			$model->add(array('uid'=>$this->uid));
		} else {
			 
			$this->assign('member_info',$member_info);	
		}
		$ids = M('members_status')->getFieldByUid($this->uid,'id_status');
			$this->assign("ids",$ids); 
		$model=M('member_contact_info');
	 
		$act = $model->find($this->uid);
		if(!is_array($act)) $model->add(array('uid'=>$this->uid));
		else $this->assign('act',$act);


		$va=M("member_address as a")->where("uid={$this->uid} and a.default=1")->find();
		$member = M("members")->where("id={$this->uid}")->find();
		$this->assign("member",$member);
	
		$this->assign("va",$va);	
		$this->display();
    }  
	public function savexixi(){
		$model=M('member_info');
		$mi['company_name']=trim($_POST['company_name']);
		$mi['company_idcard']=trim($_POST['company_idcard']);
		$mi['nick_name']=trim($_POST['nick_name']);
		$realname = $mi['real_name']= trim($_POST['real_name']);
		$idcard = $mi['idcard']= trim($_POST['idcard']);
		$mi['sex']= trim($_POST['sex']);
		$user_type = $m['user_type']= trim($_POST['user_type']);
		$m['riskint']= trim($_POST['riskint']);
		$data1['idcard'] = $mi['idcard'];
		$data1['up_time'] = time();
		$data1['uid'] = $this->uid;
		$data1['status'] = 0;

		$mpp["id"]=array("neq",$this->uid);
		$mpp["user_name"]=trim($_POST["user_name"]);

		$nv=M('members')->where($mpp)->find();
		if($nv){
			$this->error("用户名已存在请修改");
		}else{
			$m["is_name"]=1;
			$m['user_name']= trim($_POST['user_name']);
		}

		if(empty($mi['real_name'])||empty($mi['idcard']))  $this->error("请填写真实姓名和身份证号码");

		$minf = M('member_info')->where("uid={$this->uid}")->find();
		$minfo = M('members')->where("id={$this->uid}")->find();
			if(isset($minfo['recommend_id'])&&!empty($minfo['recommend_id'])){
					$recommend_ida=$minfo['recommend_id'];
				}else if(isset($minfo['recommend_bid'])&&!empty($minfo['recommend_bid'])){
					$recommend_ida=$minfo['recommend_bid'];
				}else if(isset($minfo['recommend_cid'])&&!empty($minfo['recommend_cid'])){
					$recommend_ida=$minfo['recommend_cid'];
				}
				
		
		$phone = $minfo['user_phone'];
		if($minf['real_name']==''){
			if($user_type==1){
				$rsVerify = create_agreeperson($realname,$idcard,$phone,$this->uid);
			}else{
				$rsVerify = create_agreecompany($mi['company_name'],$mi['company_idcard'],$phone,$this->uid);
			}
			if($rsVerify==false){
				$this->error("身份证认证失败");
				exit();
			}
			if($user_type==1){
				create_personmoulage($this->uid);
			}else{
				create_companymoulage($this->uid);
			}
			/////////////////////////
			$data1['idcard'] = text($_POST['idcard']);
			$data1['up_time'] = time();
			$data1['uid'] = $this->uid;
			$data1['status'] = 0;
			$b = M('name_apply')->where("uid = {$this->uid}")->count('uid');
			if($b==1){
				M('name_apply')->where("uid ={$this->uid}")->save($data1);
			}else{
				M('name_apply')->add($data1);
			}
			//if($isimg!=1) ajaxmsg("请先上传身份证正面图片",0);
			//if($isimg2!=1) ajaxmsg("请先上传身份证反面图片",0);
			
			$xuid = M('member_info')->getFieldByIdcard($mi['idcard'],'uid');
			if($xuid>0 && $xuid!=$this->uid) $this->error("此身份证号码已被人使用");
			$c = M('member_info')->where("uid = {$this->uid}")->count('uid');
			
			if($c==1){
				$newid = 1;
			}else{
				$mi['uid'] = $this->uid;
				$newid = M('member_info')->add($mi);
			}
			if($newid){

				$ms=M('members_status')->where("uid={$this->uid}")->count();
				if($ms > 0){
					$rs = 1;
				}else{
					$dt['uid'] = $this->uid;
					$dt['id_status'] = 0;
					$rs = M('members_status')->add($dt);
					$rs = 1;
				}
				if($rs){
					$pubDataa = array(
						'uid' => $recommend_ida,
						'money_bonus' => "18.0",
						'start_time' => date('Y-m-d 00:00:00',time()),
						'end_time'   => date('Y-m-d 23:59:59',time()+30*24*60*60),
						'bonus_invest_min' => '1000'        
					);		
					pubBonus($pubDataa);
					pubBonusByRules($this->uid,2);
					addInnerMsg($this->uid,"实名认证通过",'自动认证');
					
					$ms = M('members_status')->where("uid={$this->uid}")->setField('id_status',1);
					$name_apply_data['status']=1;
					$name_apply_data['deal_info']= '自动认证';
					$new = M("name_apply")->where("uid={$this->uid}")->save($name_apply_data);
					
				}
			}

			
		}

		$member_info = $model->where("uid={$this->uid}")->save($mi);
		$member = M("members")->where("id={$this->uid}")->save($m);
		$this->success("修改成功");
		die;
		 
    }  
	
    public function addimg(){
		$aa=$_GET['name'];

		if(isset($_GET['name'])&&!empty($_GET['name'])){
			 $aa=$_GET['name'];
		}else{
			 $aa='image';
		}
		if(isset($_GET['size'])&&!empty($_GET['size'])){
			  $validate['size']=$_GET['size'];
		}else{
			  $validate['size']=30000;
		}
		if(isset($_GET['ext'])&&!empty($_GET['ext'])){
			  $validate['ext']=$_GET['ext'];
		} else{
			  $validate['ext']='jpg,png,gif';
		}
		if(isset($_GET['ds'])&&!empty($_GET['ds'])){
			  $sd=$_GET['ds'];
		}else{
			 $sd='uploads';
		}
 
 		$url= '/Public/upload/';
		$name=time().'_'.rand(1, 99);
		import('ORG.Net.UploadFile');
		$upload = new UploadFile();// 实例化上传类 
		$upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
		$upload->savePath = $url ;// 设置附件上传目录
		$upload->saveRule = $name; 
		if($upload->upload()) {// 上传成功 获取上传文件信息
			 
		    $info =  $upload->getUploadFileInfo();
			$msg = $url.$info[0]['savename'];
		}else{// 上传错误提示错误信息
		     $msg = $upload->getErrorMsg();
		}
		 
		 
		 $data['user_img'] =$msg;
		$model=M('member_info');
	  	 
	  		    
    		$member_info = $model->find($this->uid);
		 
			if(!is_array($member_info)|| empty($member_info)){
					$data['uid']=$this->uid;
				$model->add($data);
			}
			else {
				  $model->where("uid = {$this->uid}")->save($data); 
			}
 
	    	 
    		 
		 echo($msg) ;
    }
	
	
	public function nick_name(){
	   
		$data=$_POST;
		  
		$model=M('member_info');
	  	if(isset($data)&&is_array($data)){
	  		    
    		$member_info = $model->find($this->uid);
		 
			if(!is_array($member_info)){
					$data['uid']=$this->uid;
				$model->add($data);
			}
			else {
				  $model->where("uid = {$this->uid}")->save($data); 
			}
	  	}
	    	 
    		$member_info = $model->find($this->uid);
			 
				$this->assign('member_info',$member_info);	
	 
			$json['content'] = $this->fetch();
 
			 
	 
			
		$this->display();
    }
	public function cell_phone(){
		$model=M('member_info');
		$model2=M('members');
		
	  	if(isset($_POST['Verification'])&&!empty($_POST['Verification'])){
	  			$data=$_POST;
                $rs = M('members')->where('user_phone='.$_POST['phone'])->find();
                if($rs){
                    $this->error("该手机号已在本站已绑定，不可重复使用！");
                    exit();
                }

                $code = $_POST['Verification'];
		        if($_SESSION['verify'] != strval($code)){
		           $json['status'] = "m";
		           $json['info'] = "验证码不正确！";
		           $this->error("验证码不正确！");
		           exit();
		       	}  
 				$data['cell_phone']=$_POST['phone'];
				$data2['user_phone']=$_POST['phone'];
	    		$member_info = $model->find($this->uid);
				if(!is_array($member_info)){
					$model2->where("uid = {$this->uid}")->save($data2); 
					$data['uid']=$this->uid;
					$model->add($data);
				}else{
					$model->where("uid = {$this->uid}")->save($data);
					$model2->where("id = {$this->uid}")->save($data2); 
					
				}
		 		$this->success("手机号修改成功",__APP__.'/member/safe');
		 		exit();
	  	}	
    	$member_info = M('members')->find($this->uid);
		$this->assign('member_info',$member_info);	
		$this->display();
    }
    public function cell_phones(){
		$model=M('member_info');
		$model2=M('members');
		$member_infos = M('member_info')->find($this->uid);
 		$vobank = M("member_banks")->where('is_default=1 and uid='.$this->uid)->field(true)->find();
	  	if(isset($_POST['phone'])&&!empty($_POST['phone'])){

		  		$rs = M('members')->where('user_phone='.$_POST['phone'])->find();
		        if($rs){
                    $this->error("该手机号已在本站已绑定，不可重复使用！");
		            exit();
		        }        

	  		    $idcard=$_POST['idcard'];
	  		    $banknum=$_POST['banknum'];
	  		    if($member_infos["idcard"]!=$idcard){
		           $this->error("身份证号码错误！");
		           exit();
	  		    }
	  		   
	  		    if(substr(str_replace(' ','',$vobank["bank_num"]),-6)!=$banknum){
		           $this->error("默认卡号后6位不正确！");
		           exit();
	  		    }
	  		
 				$data['cell_phone']=$_POST['phone'];
				$data2['user_phone']=$_POST['phone'];
	    		$member_info = $model->find($this->uid);
				if(!is_array($member_info)){
					$model2->where("uid = {$this->uid}")->save($data2); 
					$data['uid']=$this->uid;
					$model->add($data);
				}else{
					$model->where("uid = {$this->uid}")->save($data);
					$model2->where("id = {$this->uid}")->save($data2); 
					
				}
		 		$this->success("手机号修改成功",__APP__.'/member/safe');
		 		exit();
	  	}	
    	$member_info = M('members')->find($this->uid);
		$this->assign('member_info',$member_info);	
		$this->display();
    }
	
  	public function Verification(){
	 	$phone=$_POST['phone'];
		$model2=M('members');
		$members = $model2->find($this->uid);
		if($members['user_phone']==$phone){
	        preg_match_all('/\d/S', rand(1000,9999).rand(1000,9999), $matches);        
		    $arr  = join('', $matches[0]);
		    $code = substr($arr, 0, 4);        
		    session('verify', $code);              
            session('user_phone',$phone);        
            if(empty($code)) die;
            $content= "您正在修改手机号，本次验证码{$code}，若非本人操作请忽略此消息。";
            //$sendRs = sendsms($phone, $content);
			// $sendRs = sendsms($phone, $content);
			$sendRs =Notice1(1,$this->uid,array('phone'=>$phone,"code"=>$code));   
            if($sendRs === true){
                    session('reg_code_time',time());
                    $json['status'] = "y";
                    $json['info'] = "验证码已经发送至您的号码为：".$phone."的手机！";          
            }else{
                    $json['status'] = "n";
                    $json['info'] = empty($sendRs) ? '发送失败！' : $sendRs;
            }
            // 未开通短信前，测试用
             // $json['status'] = "s";
             // $json['info'] = "未开通短信前，测试用:验证码已经发送至您的号码为：".$phone."的手机！".$code;     
            exit(json_encode($json));
        }
   }
	public function indexa(){
		$this->display();
    }
    public function indexinfo(){
    	$json['html'] = $this->fetch();
			exit(json_encode($json));
    }
	public function editmemberinfo(){
		$model=M('member_info');
		if(!$_POST){
			$vo = $model->find($this->uid);
			if(!is_array($vo)) $model->add(array('uid'=>$this->uid));
			else {
				$vo['nick_name'] = M('members')->getFieldById($this->uid,'nick_name');
				$this->assign('vo',$vo);	
			}
			$json['content'] = $this->fetch();
			exit(json_encode($json));
		}
		
		$savedata = textPost($_POST);
		$savedata['uid'] = $this->uid;
		unset($savedata['real_name'],$savedata['idcard'],$savedata['card_img'],$savedata['card_credits']);
		
		M('members')->where(array('id'=>$this->uid))->setField('nick_name',text($_POST['nick_name']));		
        if (false === $model->create($savedata)) {
            $this->error($model->getError());
        }elseif($result = $model->save()) {        	
			$json['message'] = "修改成功";
			$json['status'] = 1;
			exit(json_encode($json));
        } else {
			$json['message'] = "修改成功";
			$json['status'] = '1';
			exit(json_encode($json));
        }
	}
	
	public function editcontact(){
		$model=M('member_contact_info');
		if(!$_POST){
			$vo = $model->find($this->uid);
			if(!is_array($vo)) $model->add(array('uid'=>$this->uid));
			else $this->assign('vo',$vo);
			$json['content'] = $this->fetch();
			exit(json_encode($json));
		}
		
		$savedata = textPost($_POST);
		$savedata['uid'] = $this->uid;
        if (false === $model->create($savedata)) {
            $this->error($model->getError());
        }elseif ($result = $model->save()) {
			$json['message'] = "修改成功";
			$json['status'] = 1;
			exit(json_encode($json));
        } else {
			$json['message'] = "修改失败或者资料没有改动";
			$json['status'] = 0;
			exit(json_encode($json));
        }
	}
	
	public function editdepartment(){
		$model=M('member_department_info');
		if(!$_POST){
			$vo = $model->find($this->uid);
			if(!is_array($vo)) $model->add(array('uid'=>$this->uid));
			else $this->assign('vo',$vo);
			$json['content'] = $this->fetch();
			exit(json_encode($json));
		}
		
		$savedata = textPost($_POST);
		$savedata['uid'] = $this->uid;
        if (false === $model->create($savedata)) {
            $this->error($model->getError());
        }elseif ($result = $model->save()) {
			$json['message'] = "修改成功";
			$json['status'] = 1;
			exit(json_encode($json));
        } else {
			$json['message'] = "修改失败或者资料没有改动";
			$json['status'] = 0;
			exit(json_encode($json));
        }
	}
	
	public function edithouse(){
		$model=M('member_house_info');
		if(!$_POST){
			$vo = $model->find($this->uid);
			if(!is_array($vo)) $model->add(array('uid'=>$this->uid));
			else $this->assign('vo',$vo);
			$json['content'] = $this->fetch();
			exit(json_encode($json));
		}
		
		$savedata = textPost($_POST);
		$savedata['uid'] = $this->uid;
        if (false === $model->create($savedata)) {
            $this->error($model->getError());
        }elseif ($result = $model->save()) {
			$json['message'] = "修改成功";
			$json['status'] = 1;
			exit(json_encode($json));
        } else {
			$json['message'] = "修改失败或者资料没有改动";
			$json['status'] = 0;
			exit(json_encode($json));
        }
		
	}
	
	public function editfinancial(){
		$model=M('member_financial_info');
		if(!$_POST){
			$vo = $model->find($this->uid);
			if(!is_array($vo)) $model->add(array('uid'=>$this->uid));
			else $this->assign('vo',$vo);
			$json['content'] = $this->fetch();
			exit(json_encode($json));
		}
		
		$savedata = textPost($_POST);
		$savedata['uid'] = $this->uid;
        if (false === $model->create($savedata)) {
            $this->error($model->getError());
        }elseif ($result = $model->save()) {
			$json['message'] = "修改成功";
			$json['status'] = 1;
			exit(json_encode($json));
        } else {
			$json['message'] = "修改失败或者资料没有改动";
			$json['status'] = 0;
			exit(json_encode($json));
        }
	}
	
	public function editensure(){
		$model=M('member_ensure_info');
		if(!$_POST){
			$vo = $model->find($this->uid);
			if(!is_array($vo)) $model->add(array('uid'=>$this->uid));
			else $this->assign('vo',$vo);
			$json['content'] = $this->fetch();
			exit(json_encode($json));
		}
		
		$savedata = textPost($_POST);
		$savedata['uid'] = $this->uid;
		
		
        if (false === $model->create($savedata)) {
            $this->error($model->getError());
        }elseif ($result = $model->save()) {
			if($nid) $json['message'] = "修改成功";
			else $json['message'] = "修改成功";
			$json['status'] = 1;
			exit(json_encode($json));
        } else {
			if($nid) $json['message'] = "修改失败或者资料没有改动";
			else  $json['message'] = "修改失败或者资料没有改动";
			$json['status'] = 0;
			exit(json_encode($json));
        }
	}
	
	public function editdata(){
		$Bconfig = require C("APP_ROOT")."Conf/borrow_config.php";
		$model=M('member_data_info');
		if(!$_FILES){
			import("ORG.Util.Page");
			$count = $model->where("uid={$this->uid}")->count('id');
			$p = new Page($count, 15);
			$page = $p->show();
			$Lsql = "{$p->firstRow},{$p->listRows}";
			$list = $model->field('id,data_url,data_name,add_time,status,type,ext,size,deal_info,deal_credits')->where("uid={$this->uid}")->order("type DESC")->limit($Lsql)->select();
			
			$this->assign('Bconfig',$Bconfig);
			$this->assign('list',$list);
			$this->assign('page',$page);
			$json['html'] = $this->fetch();
			exit(json_encode($json));
		}
		
		$this->savePathNew = C('MEMBER_UPLOAD_DIR').'MemberData/'.$this->uid.'/' ;
		$this->saveRule = date("YmdHis",time()).rand(0,1000);
		$info = $this->CUpload();
		$savedata['data_url'] = $info[0]['savepath'].$info[0]['savename'];
		$savedata['size'] = $info[0]['size'];
		$savedata['ext'] = $info[0]['extension'];
		$savedata['data_name'] = text(urldecode($_GET['name']));
		$savedata['type'] = intval($_GET['data_type']);
		$savedata['uid'] = $this->uid;
		$savedata['add_time'] = time();
		$savedata['status'] = 0;
		
        if (false === $model->create($savedata)) {
            $this->error($model->getError());
        }elseif ($result = $model->add()) {
			$json['message'] = "文件上传成功";
			$json['status'] = 1;
			exit(json_encode($json));
        } else {
			$json['message'] = "文件上传失败";
			$json['status'] = 0;
			exit(json_encode($json));
        }
	}
	public function delfile(){
		$id = intval($_POST['id']);
		
		$model=M('member_data_info');
		$vo = $model->field("uid,status")->where("id={$id}")->find();
		if(!is_array($vo)) ajaxmsg("提交数据有误",0);
		else if($vo['uid']!=$this->uid) ajaxmsg("不是你的资料",0);
		else if($vo['status']==1) ajaxmsg("审核通过的资料不能删除",0);
		else{
			$newid = $model->where("id={$id}")->delete();
		}
		if($newid) ajaxmsg();
		else ajaxmsg('删除失败，请重试',0);
	}
	//swf上传图片
	public function swfUpload(){
		if($_POST['picpath']){
			$imgpath = substr($_POST['picpath'],1);
			if(in_array($imgpath,$_SESSION['imgfiles'])){
					 unlink(C("WEB_ROOT").$imgpath);
					 $thumb = get_thumb_pic($imgpath);
				$res = unlink(C("WEB_ROOT").$thumb);
				if($res) $this->success("删除成功","",$_POST['oid']);
				else $this->error("删除失败","",$_POST['oid']);
			}else{
				$this->error("图片不存在","",$_POST['oid']);
			}
		}else{
			$this->savePathNew = C('MEMBER_UPLOAD_DIR').'MemberData/' ;
			$this->thumbMaxWidth = "1000,1000";
			$this->thumbMaxHeight = "1000,1000";
			$this->saveRule = date("YmdHis",time()).rand(0,1000);
			$info = $this->CUpload();
			$data['product_thumb'] = $info[0]['savepath'].$info[0]['savename'];
			if(!isset($_SESSION['count_file'])) $_SESSION['count_file']=1;
			else $_SESSION['count_file']++;
			$_SESSION['imgfiles'][$_SESSION['count_file']] = $data['product_thumb'];
			echo "{$_SESSION['count_file']}:".__ROOT__."/".$data['product_thumb'];//返回给前台显示缩略图
		}
	}
	
	public function getarea(){
		$rid = intval($_GET['rid']);
		if(empty($rid)){
			$data['NoCity'] = 1;
			exit(json_encode($data));
		}
		$map['reid'] = $rid;
		$alist = M('area')->field('id,name')->order('sort_order DESC')->where($map)->select();
		if(count($alist)===0){
			$str="<option value=''>--该地区下无下级地区--</option>\r\n";
		}else{
			if($rid==1) $str.="<option value='0'>请选择省份</option>\r\n";
			foreach($alist as $v){
				$str.="<option value='{$v['id']}'>{$v['name']}</option>\r\n";
			}
		}
		$data['option'] = $str;
		$res = json_encode($data);
		echo $res;
	}	
	
}
