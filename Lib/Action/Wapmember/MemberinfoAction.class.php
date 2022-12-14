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
		$members = M('members')->where("id=".$this->uid)->find();
		$this->assign("members",$members); 
				$this->assign("ids",$ids); 
			$model=M('member_contact_info');
		 
			$act = $model->find($this->uid);
			if(!is_array($act)) $model->add(array('uid'=>$this->uid));
			else $this->assign('act',$act);
			// var_dump($member_info);
			 
	 	$json['content'] = $this->fetch();
			$va=M("member_address as a")
//		->join(' member_info as i ON i.uid = a.uid')
//		->field('member_address.*')
		->where("uid={$this->uid} and a.default=1")->find();
		$this->assign("va",$va);	
		$this->display();
    } 
	    public function indexnotice(){
	    	   preg_match_all('/\d/S', rand(1000,9999).rand(1000,9999), $matches);        
        $arr  = join('', $matches[0]);
        $code = substr($arr, 0, 4); 
		$phone="18354393241";
        session('reg_code', $code);              
        session('user_phone',$phone);
        $content="您的验证码是".$code."，120秒内有效，若非本人操作请忽略此消息。";//要发送的短信内容
//      $result = sendsms($phone,$content);
	    	Notice1(1,$this->uid,array('phone'=>$phone,"code"=>$code));
//			echo "<br/>";
//			Notice1(2,$this->uid,array('phone'=>$phone,"code"=>$code));
//			echo "<br/>";
//			Notice1(3,$this->uid,array('phone'=>$phone,"code"=>$code));
//			echo "<br/>";
//			Notice1(4,$this->uid,array('phone'=>$phone,"code"=>$code));
//			echo "<br/>";
//  		var_dump(notice(1,1,["1"])) ;
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
				  $validate['size']=2880000;
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
        $upload->thumb = true;
        $upload->thumbMaxWidth = 150;
        $upload->thumbMaxHeight = 150;
        $upload->thumbRemoveOrigin = true;
		$upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
		$upload->savePath = $url ;// 设置附件上传目录
		$upload->saveRule = $name; 
		if($upload->upload()) {// 上传成功 获取上传文件信息
			 
		    $info =  $upload->getUploadFileInfo();
			$msg = $url.$upload->thumbPrefix.$info[0]['savename'];
//		      echo($msg) ;
		
		}else{// 上传错误提示错误信息
		     $msg = $upload->getErrorMsg();
			 ajaxmsg($msg,0);
//			  echo($msg) ;
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
 
	    	 
    		ajaxmsg($msg,1); 
		 
    }
	
	
	  public function nick_name(){
// 	   	var_dump($_POST); 
// var_dump($_POST);
//$_POST;
		$data=$_POST;
		  
		$model=M('member_info');
//		   	var_dump($data);die;
// 	var_dump($_POST,$data["nick_name"]);
// 		var_dump(isset($data["nick_name"]),empty($data["nick_name"]));
 
	  		 
    		$member_info = $model->find($this->uid);
			 
				$this->assign('member_info',$member_info);	
	 
			$json['content'] = $this->fetch();
 
			 
	 
			
			$this->display();
	   
//	    	
    }
	  	  public function save_nick_name(){
// 	   	var_dump($_POST); 
// var_dump($_POST);
//$_POST;
		$data=$_POST;
//		  die;
		$model=M('member_info');
//		   	var_dump($data);die;
// 	var_dump($_POST,$data["nick_name"]);
// 		var_dump(isset($data["nick_name"]),empty($data["nick_name"]));
	  	if(isset($data["nick_name"]) && !empty($data["nick_name"])){
	  		    
    		$member_info = $model->find($this->uid);
		 
			if(!is_array($member_info)){
					$data['uid']=$this->uid;
				$acc=$model->add($data);
			}
			else {
				 $acc= $model->where("uid = {$this->uid}")->save($data); 
			}
			if($acc){
						echo "<script type='text/javascript'>";
			 			echo "alert('修改成功！');";
        				echo "window.location.href='/Wapmember';";
			        	echo "</script>";die;
				}else{
						echo "<script type='text/javascript'>";
			 			echo "alert('修改失败！');";
        			 echo "window.location.href='/Wapmember/Memberinfo/nick_name';";
			        	echo "</script>";
//			        	$this->display();
//			        	die;
				}	
	  	} 
	  		 
    		 
	   
//	    	
    }
	  
	 public function cell_phone(){
	   //membres.user_phone,member_info.cell_phone

	
		$model=M('member_info');
		$model2=M('members');
		
	  	if(isset($_POST['Verification'])&&!empty($_POST['Verification'])){
	  		   preg_match_all('/\d/S', rand(1000,9999).rand(1000,9999), $matches);        
	  			$data=$_POST;
	  		  	$code = $_POST['Verification'];
		      
		        if($_SESSION['reg_code'] != strval($code)){
//		           $json['status'] = "m";
//		           $json['info'] = "验证码不正确！";
		     	  echo "<script type='text/javascript'>";
			 		echo "alert('验证码不正确！');";
        				echo "window.history.go(-1);";
			        	echo "</script>";die;
					
		       }  
 				$data['cell_phone']=$_POST['phone'];
				$data2['user_phone']=$_POST['phone'];
	    		$member_info = $model->find($this->uid);
			 $members=$model2->where("user_phone =  ".$_POST['phone'])->find(); 
			 if($members){
			 	 echo "<script type='text/javascript'>";
			 		echo "alert('手机号已绑定！');";
        				echo "window.location.href='/wapmember/';";
			        	echo "</script>";die;
			 }
				if(!is_array($member_info)){
					
					$model2->where("uid = {$this->uid}")->save($data2); 
					$data['uid']=$this->uid;
					$zz=$model->add($data);
				}
				else {
						
					$zz=$model->where("uid = {$this->uid}")->save($data);
				
					$model2->where("id = {$this->uid}")->save($data2); 
					
				}
				   if($zz){
				   	 echo "<script type='text/javascript'>";
			 		echo "alert('成功！');";
        				echo "window.location.href='/wapmember/';";
			        	echo "</script>";die;
				   } 	 
//				var_dump($model->getlastsql()); 
//					var_dump($model2->getlastsql());die; 
		 
	  	}
	    	 	
    		$member_info = $model->find($this->uid);
			$members = $model2->find($this->uid); 
//			var_dump($members);
				$this->assign('member_info',$member_info);	
	 	$this->assign('members',$members);	
			$json['content'] = $this->fetch();
 
			 
	 
			
		$this->display();
    }
  public function Verification(){
	 	$phone=$_POST['phone'];
		$members = $model2->find($this->uid);
			if($members['user_phone']==$phone){
				
		

 

          
               preg_match_all('/\d/S', rand(1000,9999).rand(1000,9999), $matches);        
       $arr  = join('', $matches[0]);
       $code = substr($arr, 0, 4);        
       session('reg_code', $code);              
               session('user_phone',$phone); 
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
$smsapi = "http://api.smsbao.com/";
$user = "gthy"; //短信平台帐号
$pass = md5("1010890372..."); //短信平台密码
$content="【铭万网络】您的验证码是".$code."，30秒内有效，若非本人操作请忽略此消息。";//要发送的短信内容
$phone = "$phone";//要发送短信的手机号码
$sendurl = $smsapi."sms?u=".$user."&p=".$pass."&m=".$phone."&c=".urlencode($content);
$result =file_get_contents($sendurl) ;

		if($result == 0){
                       session('reg_code_time',time());
                       $json['status'] = "y";
                       $json['info'] = "验证码已经发送至您的号码为：".$phone."的手机！";          
               }else{
                       $json['status'] = "n";
                       $json['info'] = empty($sendRs) ? '发送失败！' : $sendRs;
               }
			   
			    exit(json_encode($json));		
//	          preg_match_all('/\d/S', rand(1000,9999).rand(1000,9999), $matches);        
//		       $arr  = join('', $matches[0]);
//		       $code = substr($arr, 0, 4);        
//		       session('verify', $code);              
//             session('user_phone',$phone);        
//             if(empty($code)) die;
//             $content= "您正在注册网站会员，手机验证码。{$code}";
//             $sendRs = sendsms($phone, $content,1);       
//             if($sendRs === true){
//                     session('reg_code_time',time());
//                     $json['status'] = "y";
//                     $json['info'] = "验证码已经发送至您的号码为：".$phone."的手机！";          
//             }else{
//                     $json['status'] = "n";
//                     $json['info'] = empty($sendRs) ? '发送失败！' : $sendRs;
//             }
//			   
//             // 未开通短信前，测试用
//              $json['status'] = "s";
//              $json['info'] = "未开通短信前，测试用:验证码已经发送至您的号码为：".$phone."的手机！".$code;     
//             exit(json_encode($json));
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
