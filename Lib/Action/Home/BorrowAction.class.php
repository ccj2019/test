<?php
class BorrowAction extends HCommonAction {
    public function index(){
		$article = m("article_category")->field('type_content')->where('id=523')->find();
        $this->assign("article",$article);
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
		   	echo json_encode(array("error"=>"0","pic"=>$msg,"name"=>$info[0]['savename']));
			exit();
		}else{// 上传错误提示错误信息
		    echo $msg = $upload->getErrorMsg();
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
		$borrow_info = M("borrow_info")->where("id={$_GET['id']}")->find();
		$this->assign('borrow_info',$borrow_info);
		$this->assign('id',$_GET['id']);
		$this->display();
	}
	public function post(){
		if(!$this->uid) $this->error("请先登陆",__APP__."/member/common/login");
		$ids = M('members_status')->getFieldByUid($this->uid,'id_status');
		if($ids!=1){
			$this->error("请先通过实名认证后在进行投标",__APP__."/member/Memberinfo/index.html");
		}
		$phones = M('members_status')->getFieldByUid($this->uid,'phone_status');
		if($phones!=1){
			$this->error("请先通过手机认证后在进行投标",__APP__."/member/safe/cellphone");
		}
		$searchMaps['borrow_status']=array("in",'2');
        $parm['limit'] = 1;
        $parm['map']      = $searchMaps;
        $parm['orderby'] = "id DESC";
        $list_type3 = getBorrowList($parm); 
        $this->assign('list_type3',$list_type3);
		$this->display();
	}
	public function ok(){
		$borrow_info = M("borrow_info")->where("id={$_GET['id']}")->find();
		$this->assign('borrow_info',$borrow_info);
		$this->display();
	}
	public function save1(){
		$id = $_POST['id'];
		$data=$_POST;
        $data['return_info']=$_POST['return_info'];
		$data['shortcut']=str_replace('，',',',$_POST['shortcut']);
		$data['borrow_status']=0;
		$newid = M("borrow_info")->save($data);
		if($newid){
			$this->success("发布成功，已提交管理员审核",__APP__."/member");
			exit();
		}else{ 
			$this->success("发布失败",__APP__."/member");
			exit();
		}
	}
	public function savestatus(){
		$data['id']=$_GET['id'];
		$data['borrow_status']=0;
		$newid = M("borrow_info")->save($data);
		if($newid){
			$this->success("已提交审核",__APP__."/member");
			exit();
		}else{ 
			$this->error("提交审核失败",__APP__."/member");
			exit();
		}
	}
	public function save(){
		
		if(!$this->uid) redirect(__APP__."/member/common/login");

		$pre = C('DB_PREFIX');
		//相关的判断参数

		$rate_lixt = explode("|",$this->glo['rate_lixi']);
		$borrow_duration = explode("|",$this->glo['borrow_duration']);
		$borrow_duration_day = explode("|",$this->glo['borrow_duration_day']);
		$fee_borrow_manage = explode("|",$this->glo['fee_borrow_manage']);
		$vminfo = M('members m')->join("{$pre}member_info mf ON m.id=mf.uid")->field("m.user_leve,m.time_limit,mf.province_now,mf.city_now,mf.area_now")->where("m.id={$this->uid}")->find();
		//相关的判断参数
		$borrow['borrow_type'] =1;
		
	
	    if(floatval($_POST['borrow_interest_rate'])>$rate_lixt[1] || floatval($_POST['borrow_interest_rate'])<$rate_lixt[0]) $this->error("提交的借款利率有误，请重试",0);
		
		$borrow['borrow_money'] = intval($_POST['borrow_money']);
		$borrow['borrow_cat'] = intval($_POST['borrow_cat']);
		$borrow['borrow_model'] = intval($_POST['borrow_model']);		
		
		$_minfo = getMinfo($this->uid,true);
		// $_capitalinfo = getMemberBorrowScan($this->uid);
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
		$borrow['borrow_duration'] = text($_POST['borrow_duration']);//秒标固定为一月
		//项目收益率
		$borrow['shouyilv'] = text($_POST['shouyilv']);

		$borrow['hkday'] = intval($_POST['hkday']);
		
		$borrow['borrow_interest_rate'] = floatval($_POST['borrow_interest_rate']);
		if(strtolower($_POST['is_day'])=='yes') $borrow['repayment_type'] = 1;
		elseif($borrow['borrow_type']==3) $borrow['repayment_type'] = 2;//秒标按月还
		else $borrow['repayment_type'] = intval($_POST['repayment_type']);
		
		if($_POST['show_tbzj']==1) $borrow['is_show_invest'] = 1;//共几期(分几次还)
		
		$borrow['total'] = $_POST['total'];//共几期(分几次还)
		// if($borrow['repayment_type']==5){
		// 	$borrow['total']=1;	
		// }
		
		//echo $borrow['total'];
		//exit;
		$borrow['borrow_video'] = $_POST["borrow_video"];
		$borrow['pid'] = $_POST["pid"];
		$borrow['new_user_only'] = $_POST["new_user_only"];
        $borrow['bespeak_able'] = $_POST["bespeak_able"];
        $borrow['zhitou_able'] = $_POST["zhitou_able"];
        $borrow['bespeak_days'] = $this->glo["bespeak_days"];
		$borrow['borrow_status'] = '-1';
		$borrow['borrow_use'] = intval($_POST['borrow_use']);
		$borrow['add_time'] = time();
		$borrow['collect_day'] = intval($_POST['borrow_time']);
		$borrow['add_ip'] = get_client_ip();
		$borrow['borrow_info'] = stripslashes(htmlspecialchars_decode($_POST['borrow_info']));
		$borrow['borrow_con'] ="<pre>". $_POST['borrow_con'].'</pre>';//stripslashes(htmlspecialchars_decode($_POST['borrow_con']))
		$borrow['reward_type'] = intval($_POST['reward_type']);
		$borrow['reward_num'] = floatval($_POST["reward_type_{$borrow['reward_type']}_value"]);
		$borrow['borrow_min'] = 50;
		$oldtime = $_POST['lead_time'].' 22:19:21'; 

      
		$borrow['lead_time'] =  strtotime($oldtime);

		//ccj——2020-11-9
		$borrow['xs_time'] =  strtotime($_POST['xs_time']);
		$borrow['shoujia'] =  floatval($_POST['shoujia']);
        $borrow['sg_time'] =  strtotime($_POST['sg_time']);
        $borrow['art_writer'] =$_POST['art_writer'];
        $borrow['xj_time'] =  strtotime($_POST['xj_time']);


		$borrow['borrow_max'] = intval($_POST['borrow_max']);
		$borrow['province'] = "22";
		$borrow['city'] =$_POST["city"];;
		$borrow['area'] = "";
		$borrow['repayment_type']=1;
	
		if($_POST['is_pass']&&intval($_POST['is_pass'])==1) $borrow['password'] = $_POST['password'];
		//借款费和利息
		$borrow['borrow_interest'] = getBorrowInterest($borrow['repayment_type'],$borrow['borrow_money'],$borrow['borrow_duration'],$borrow['borrow_interest_rate']);
		
		
		if($borrow['repayment_type'] == 1){//按天还
			$fee_rate = (is_numeric($fee_borrow_manage[0]))?($fee_borrow_manage[0]/1000):0;
			$borrow['borrow_fee'] = getFloatValue($fee_rate*$borrow['borrow_money']*$borrow['collect_day'],2);
			
		}
		
		
 
		$borrow['borrow_img']=$_POST['borrow_img'];
		// $borrow['content_img']=$_POST['content_img'];
		$borrow['content_img']=implode(",",$_POST['content_img']);
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
		if($borrow['collect_day']==""){
			$smasvae="请设置项目需要募集的时间";
		}
		if($borrow['borrow_model']==""){
			$smasvae="请填写项目类型";
		}
		
		if($borrow['pid']==""){
			$smasvae="请选择所属行业";
		}
		if($borrow['borrow_img']==""){
			$smasvae="请上传项目封面";
		}
		if($borrow['borrow_info']==""){
			$smasvae="请填写项目介绍";
		}
		
		if(isset($smasvae)&&!empty($smasvae)){
				$this->success($smasvae,__APP__."/borrow/index");
 			exit();
		}else{
		
		
		$newid = M("borrow_info")->add($borrow);
		//echo M("borrow_info")->getlastsql();
	
		if($newid){
			$this->success("请进行下一步",__APP__."/borrow/post1?id=".$newid);
			die;
			// $this->success("项目发布成功，网站会尽快初审",__APP__."/member/borrowin?id=".$newid);
		}else {
			$this->success("发布失败",__APP__."/borrow/index");
 			exit();
			}
		}
		
		
	}
	
	public function getarea(){
		$rid = intval($_GET['rid']);
		if(empty($rid)) return;
		$map['reid'] = $rid;
		$alist = M('area')->field('id,name')->order('sort_order DESC')->where($map)->select();
		//$this->display("Public:empty");
		if(count($alist)===0){
			$str="<option value=''>--该地区下无下级地区--</option>\r\n";
		}else{
			foreach($alist as $v){
				$str.="<option value='{$v['id']}'>{$v['name']}</option>\r\n";
			}
		}
		$data['option'] = $str;
		$res = json_encode($data);
		echo $res;
	}
	
 
}
