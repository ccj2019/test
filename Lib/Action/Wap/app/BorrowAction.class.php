<?php
class BorrowAction extends HCommonAction {
    public function index(){
    	if(!$this->uid){
    		$url="/wapmember/common/login";
echo "<script language='javascript'type='text/javascript'>"; 
echo "alert('请先登陆');"; 
echo "window.location.href='$url'"; 
echo "</script>"; die;

    	}
		$this->display();
    }
    public function zhaodelete() {
			 $aa=$_SERVER['DOCUMENT_ROOT'].strval($_POST['url']);
			   
			    if(file_exists($aa)){
        			$info ='1';
			        unlink($aa);
			    }else{
			        $info ='原头像没找到:'.$aa;
			    }
			    echo $info;
			 
	}
	public function zhao() {
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
		$upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
		$upload->savePath = $url ;// 设置附件上传目录
		$upload->saveRule = $name; 
		if($upload->upload()) {// 上传成功 获取上传文件信息
			 
		    $info =  $upload->getUploadFileInfo();
			$msg = $url.$info[0]['savename'];
		      ajaxmsg($msg,1);
		
		}else{// 上传错误提示错误信息
		$msg = $upload->getErrorMsg();
		    ajaxmsg($msg,0);
		}
	}
	public function view(){
		
		$id = intval($_GET['id']);
		if($_GET['type']=="subsite") $vo = M('article_area')->find($id);
		else $vo = M('article')->find($id);
		$this->assign("vo",$vo);
		//left
		$typeid = $vo['type_id'];
		$listparm['type_id']=$typeid;
		$listparm['limit']=20;
		if($_GET['type']=="subsite"){
			$listparm['area_id'] = $this->siteInfo['id'];
			$leftlist = getAreaTypeList($listparm);
		}else	$leftlist = getTypeList($listparm);
		
		$this->assign("leftlist",$leftlist);
		$this->assign("cid",$typeid);
		
		if($_GET['type']=="subsite"){
			$vop = D('Aacategory')->field('type_name,parent_id')->find($typeid);
			if($vop['parent_id']<>0) $this->assign('cname',D('Aacategory')->getFieldById($vop['parent_id'],'type_name'));
			else $this->assign('cname',$vop['type_name']);
		}else{
			$vop = D('Acategory')->field('type_name,parent_id')->find($typeid);
			if($vop['parent_id']<>0) $this->assign('cname',D('Acategory')->getFieldById($vop['parent_id'],'type_name'));
			else $this->assign('cname',$vop['type_name']);
		}
		$this->display();
	}
public function post1(){
	if(!$this->uid){
    	$url="/wapmember/common/login";
		echo "<script language='javascript'type='text/javascript'>"; 
		echo "alert('请先登陆');"; 
		echo "window.location.href='$url'"; 
		echo "</script>"; die;
    }
	if(empty($_GET['id'])){
		//$this->error("参数错误");
			$this->error('参数错误',__APP__.'/Wap/Borrow/index');
	}else{
		$this->assign('id',$_GET['id']);
		$this->display();	
	}
		
}
	public function post(){
//	echo($this->uid);	die;
		if(!$this->uid){
			$url="/wapmember/common/login";
 echo "<script language='javascript'type='text/javascript'>"; 
echo "window.location.href='$url'"; 
echo "</script>"; die;
			 $this->error("请先登陆","/wapmember/common/login");
		}
			$this->display();
		die;
		$vminfo = M('members')->field("user_type")->find($this->uid);
		// if($vminfo["user_type"]!=2)$this->error("您的会员类型为项目投资人，不能发布众筹项目申请。","/");
		$catlist = M('pro_category')->select();		
		$this->assign('catlist',$catlist);		
		
		$borrow_config = C("BORROW");
		$borrow_model = isset($_GET['borrow_model']) && in_array($_GET['borrow_model'], array_keys($borrow_config['BORROW_MODEL'])) ? $_GET['borrow_model'] : '1';
		$borrow_model_cn = $borrow_config['BORROW_MODEL'][$borrow_model];	
		$this->assign("borrow_model_cn",$borrow_model_cn);		
		$this->assign("borrow_model",$borrow_model);	
	
		
		$ids = M('members_status')->getFieldByUid($this->uid,'id_status');
		if($ids!=1){
			//$this->error("请先通过实名认证后在进行投标",__APP__."/member/verify#chip-3");
		}
		$phones = M('members_status')->getFieldByUid($this->uid,'phone_status');
		if($phones!=1){
			//$this->error("请先通过手机认证后在进行投标",__APP__."/member/verify#chip-2");
		}
		$emails = M('members_status')->getFieldByUid($this->uid,'email_status');
		if($emails!=1){
			//$this->error("请先通过邮箱认证后在进行投标",__APP__."/member/verify#chip-1");
		}
		$gtype = 'normal';
		$vkey = md5(time().$gtype);
		$borrow_type=1;
		cookie($vkey,$borrow_type,3600);
		$borrow_duration_day = explode("|",$this->glo['borrow_duration_day']);
		$day = range($borrow_duration_day[0],$borrow_duration_day[1]);
		$day_time=array();
		foreach($day as $v){
			$day_time[$v] = $v."天";
		}
		$borrow_duration = explode("|",$this->glo['borrow_duration']);
		$month = range($borrow_duration[0],$borrow_duration[1]);
		$month_time=array();
		foreach($month as $v){
			$month_time[$v] = $v."个月";
		}
	
		$rate_lixt = explode("|",$this->glo['rate_lixi']);
		
		$this->assign("borrow_model",$borrow_model);		
		$this->assign("borrow_use",$borrow_config['BORROW_USE']);
		$this->assign("borrow_min",$borrow_config['BORROW_MIN']);
		$this->assign("borrow_max",$borrow_config['BORROW_MAX']);
		$this->assign("borrow_time",$borrow_config['BORROW_TIME']);
		$this->assign("BORROW_TYPE",$borrow_config['BORROW_TYPE']);
		$this->assign("borrow_type",$borrow_type);
		$this->assign("borrow_day_time",$day_time);
		$this->assign("borrow_month_time",$month_time);
		$this->assign("repayment_type",$borrow_config['REPAYMENT_TYPE']);
		$this->assign("vkey",$vkey);
		$this->assign("rate_lixt",$rate_lixt);
		
		$citylist=Array ( 283 => "济南",284 => "青岛",285=> "滨州",286 => "德州" ,287 => "东营" ,288 => "菏泽" ,289 => "济宁" ,290 => "莱芜" ,291 => "聊城" ,292 => "临沂" ,293 => "日照" ,294 => "泰安" ,295 => "威海" ,296 => "潍坊" ,297 =>"烟台" ,298 => "枣庄" ,299 => "淄博" );
		$this->assign('city',$citylist);
		
		$this->display();
	}
public function ok(){
		$newid = M("borrow_info")->where("id={$_GET['id']}")->find();
		$this->assign('var',$newid);
		$this->display();
}
	public function save1(){
			// var_dump($_POST);die;
				$_POST['return_info']="<pre>".$_POST['return_info']."</pre>";
		$newid = M("borrow_info")->save($_POST);
		//echo M("borrow_info")->getlastsql();
	header('Content-Type:text/html;charset=utf-8');
	
	 
		if($newid){
				echo "<script type='text/javascript'>";
//			 		echo "alert('发布成功,等待后台审核！');";
 					echo "window.location.href='/wapmember/';";
			        	echo "</script>";die;
			//header("location:/wap/borrow/ok");
	 
		 
			 $this->success("项目发布成功，网站会尽快初审",__APP__."/member/borrowin?id=".$newid);
		}
		else {
			//$newid = M("borrow_info")->wher("id=$_POST['id']")->delete();
					$smasvae="发布失败"; 
					var_dump(1); 
				echo "<script>";
			 	//	echo "alert('发布成功,等待后台审核！');";
        				 echo "window.location.href='/wapmember/';";
			        	echo "</script>";die;
				//var_dump($smasvae); 				
				//echo"<script>alert('".$smasvae."');history.go(-1);</script>";  
			}
		}
	public function save(){

		//$this->error("提交的借款利率有误，请重试",0);die;
		
		if(!$this->uid){
			echo "<script type='text/javascript'>";
			 		echo "alert('请登录！');";
        				 echo "window.location.href='/wapmember/';";
			        	echo "</script>";die;
		}
	
		// redirect(__APP__."/member/common/login/");
	
		$pre = C('DB_PREFIX');
		//相关的判断参数
	
		$rate_lixt = explode("|",$this->glo['rate_lixi']);
		$borrow_duration = explode("|",$this->glo['borrow_duration']);
		$borrow_duration_day = explode("|",$this->glo['borrow_duration_day']);
		$fee_borrow_manage = explode("|",$this->glo['fee_borrow_manage']);
		$vminfo = M('members m')->join("{$pre}member_info mf ON m.id=mf.uid")->field("m.user_leve,m.time_limit,mf.province_now,mf.city_now,mf.area_now")->where("m.id={$this->uid}")->find();
		//相关的判断参数
		$borrow['borrow_type'] = $_POST['borrow_type'];
	//	if($borrow['borrow_type']==0) $this->error("校验数据有误，请重新发布");
		

	    if(floatval($_POST['borrow_interest_rate'])>$rate_lixt[1] || floatval($_POST['borrow_interest_rate'])<$rate_lixt[0]){
	    	echo "<script type='text/javascript'>";
	 		echo "alert('提交的借款利率有误，请重试！');";
			echo "history.go(-1);";
	        echo "</script>";die;
	    	//$this->error("提交的借款利率有误，请重试",0);
	    }
			
		$borrow['borrow_money'] = intval($_POST['borrow_money']);
		$borrow['borrow_cat'] = intval($_POST['borrow_cat']);
		$borrow['borrow_model'] = intval($_POST['borrow_model']);		
		
		$_minfo = getMinfo($this->uid,true);
		$_capitalinfo = getMemberBorrowScan($this->uid);
		///////////////////////////////////////////////////////
		$borrowNum=M('borrow_info')->field("borrow_type,count(id) as num,sum(borrow_money) as money,sum(repayment_money) as repayment_money")->where("borrow_uid = {$this->uid} AND borrow_status=6 ")->group("borrow_type")->select();
		$borrowDe = array();
		foreach ($borrowNum as $k => $v) {
			$borrowDe[$v['borrow_type']] = $v['money'] - $v['repayment_money'];
		}
		///////////////////////////////////////////////////
		//echo $borrow['borrow_type'];
		//exit;

	
		$borrow['borrow_uid'] = $this->uid;
		$borrow['borrow_name'] = text($_POST['borrow_name']);
		$borrow['borrow_duration'] = 1;//秒标固定为一月
		$borrow['shouyilv'] = text($_POST['shouyilv']);

		$borrow['hkday'] = intval($_POST['hkday']);
	
		$borrow['borrow_interest_rate'] = floatval($_POST['borrow_interest_rate']);
		if(strtolower($_POST['is_day'])=='yes') $borrow['repayment_type'] = 1;
		elseif($borrow['borrow_type']==3) $borrow['repayment_type'] = 2;//秒标按月还
		else $borrow['repayment_type'] = intval($_POST['repayment_type']);
		
		if($_POST['show_tbzj']==1) $borrow['is_show_invest'] = 1;//共几期(分几次还)
		
		$borrow['total'] = ($borrow['repayment_type']==1)?1:$borrow['borrow_duration'];//共几期(分几次还)
		if($borrow['repayment_type']==5){
			$borrow['total']=1;	
		}
			
		//echo $borrow['total'];
		//exit;
		$borrow['borrow_video'] = $_POST["borrow_video"];
		$borrow['pid'] = $_POST["pid"];
		$borrow['new_user_only'] = $_POST["new_user_only"];
        $borrow['bespeak_able'] = $_POST["bespeak_able"];
        $borrow['bespeak_days'] = $this->glo["bespeak_days"];
		$borrow['borrow_status'] = 0;
		$borrow['borrow_duration'] = intval($_POST['borrow_duration']);
		$borrow['total'] = intval($_POST['total']);
		$borrow['borrow_use'] = intval($_POST['borrow_use']);
		$borrow['add_time'] = time();
		$borrow['collect_day'] = intval($_POST['borrow_time']);
		$borrow['add_ip'] = get_client_ip();
		$borrow['borrow_info'] = stripslashes(htmlspecialchars_decode($_POST['borrow_info']));
		$borrow['borrow_con'] = stripslashes(htmlspecialchars_decode($_POST['borrow_con']));
		$borrow['reward_type'] = intval($_POST['reward_type']);
		$borrow['reward_num'] = floatval($_POST["reward_type_{$borrow['reward_type']}_value"]);
		$borrow['borrow_min'] = 50;
		$borrow['borrow_max'] = intval($_POST['borrow_max']);
		$borrow['province'] = "22";
		$borrow['city'] =$_POST["city"];;
		$borrow['area'] = "";
		$oldtime = $_POST['lead_time']; 

      
		$borrow['lead_time'] =  strtotime($oldtime);
		if($_POST['is_pass']&&intval($_POST['is_pass'])==1) $borrow['password'] = $_POST['password'];
	//	$borrow['repayment_type']=$borrow['repayment_type']==5?4:$borrow['repayment_type'];
		//借款费和利息
		$borrow['borrow_interest'] = getBorrowInterest($borrow['repayment_type'],$borrow['borrow_money'],$borrow['borrow_duration'],$borrow['borrow_interest_rate']);
		
		
		if($borrow['repayment_type'] == 1){//按天还
			$fee_rate = (is_numeric($fee_borrow_manage[0]))?($fee_borrow_manage[0]/1000):0;
			$borrow['borrow_fee'] = getFloatValue($fee_rate*$borrow['borrow_money']*$borrow['collect_day'],2);
			
		}else{
			$fee_rate_1=(is_numeric($fee_borrow_manage[1]))?($fee_borrow_manage[1]/1000):0;
			$borrow['borrow_fee']=getFloatValue($borrow['borrow_money']*$fee_rate_1*$borrow['collect_day'],2);
		}
		
		if($borrow['repayment_type']==5){
			$fee_rate = (is_numeric($fee_borrow_manage[0]))?($fee_borrow_manage[0]/100):0.001;
			$borrow['borrow_fee'] = getFloatValue($fee_rate*$borrow['borrow_money']*$borrow['collect_day'],2);
		}
		
		if($borrow['borrow_type']==3){//秒还标
			if($borrow['reward_type']>0){
				if($borrow['reward_type']==1) $_reward_money = getFloatValue($borrow['borrow_money']*$borrow['reward_num']/100,2);
				elseif($borrow['reward_type']==2) $_reward_money = getFloatValue($borrow['reward_num'],2);
			}
			$_reward_money =floatval($_reward_money);
			$__reward_money=0;
			$borrow['borrow_fee']=0;
	
			if(($_minfo['account_money'])<($borrow['borrow_fee']+$_reward_money))
			{
					echo "<script type='text/javascript'>";
			 		echo "alert('发布此标您最少需保证您的帐户余额大于等于".($borrow['borrow_fee']+$_reward_money)."元，以确保可以支付投标奖励费用！');";
        				 echo "history.go(-1);";
			        	echo "</script>";die;
			} 
			//$this->error("发布此标您最少需保证您的帐户余额大于等于".($borrow['borrow_fee']+$_reward_money)."元，以确保可以支付投标奖励费用");
		}
		
		
 		
		$borrow['borrow_img']=$_POST['borrow_img'];
		//投标上传图片资料（暂隐）
		foreach($_POST['swfimglist'] as $key=>$v){
			if($key>10) break;
			$row[$key]['img'] = substr($v,1);
			$row[$key]['info'] = $_POST['picinfo'][$key];
		}
		$borrow['updata']=serialize($row);
		
		$borrow['auto_info'] = $this->glo['ttxf_auto_all'];
		
		$borrow['p_auto_info'] = $this->glo['ttxf_auto_p'];
		
 
		
		if($borrow['borrow_name']==""){
			$smasvae="请输入项目标题";
		}
		if($borrow['borrow_money']==""){
			$smasvae="请输入您需要募集的金额";
		}
		if($borrow['borrow_money']<1000){
			$smasvae="筹款金额不能小于1000";
		}
		if($borrow['collect_day']==""){
			$smasvae="请设置项目需要募集的时间";
		}
		if($borrow['borrow_model']==""){
			$smasvae="请填写项目类型";
		}
		
		if($borrow['pid']==""){
			$smasvae="请选择所属行业";
		}	//var_dump($_POST);die;
		if($borrow['borrow_img']==""){
			$smasvae="请上传项目封面";
		} 
		if($borrow['borrow_info']==""){
			$smasvae="请填写项目介绍";
		}
		if(isset($smasvae)&&!empty($smasvae)){
				echo"<script>alert('".$smasvae."');history.go(-1);</script>"; 
		}else{
	// 	var_dump($borrow);
	// die;	

		$borrow['content_img']=implode(",",$_POST['content_img']);
				// var_dump($borrow);die;
		$newid = M("borrow_info")->add($borrow);
// 	 echo M("borrow_info")->getlastsql();
// die;
		if($newid){
			
			 
			header("location:/wap/borrow/post1?id=".$newid);
			die;
		 
			 $this->success("项目发布成功，网站会尽快初审",__APP__."/member/borrowin?id=".$newid);}
		else {
				$smasvae="发布失败";  	
				//var_dump($smasvae); 				
				echo"<script>alert('".$smasvae."');history.go(-1);</script>";  
			}
		}
		
		
	}
	
	
	
 
}
