<?php
// 本类由系统自动生成，仅供测试用途
class MemberaddressAction extends MCommonAction {
 
	public function index(){
		$va=M("member_address")->where("uid={$this->uid}")->select();
		$this->assign("va",$va); 
		$area=M("area")->where("sort_order=1")->select();
		$this->assign("area",$area);
		$this->assign("order_id",$_REQUEST['order_id']?$_REQUEST['order_id']:0);
		$this->display();
	}
 	public function add(){
		$this->display();
	}
	public function getarea(){
		$rid = intval($_GET['rid']);
		if(empty($rid)) return;
		$map['reid'] = $rid;
		$alist = M('area')->field('id,name')->order('sort_order asc')->where($map)->select();
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
	
 	public function save(){
 		$address=M("member_address")->where("id={$_GET['id']}")->find();
 		$province = M('area')->field('id')->where('name = "'.$address['province'].'"')->find();
 		$city = M('area')->field('id')->where('name = "'.$address['city'].'"')->find();
 		$district = M('area')->field('id')->where('name = "'.$address['district'].'"')->find();
 		$vobank['province']=$province['id'];
 		$vobank['city']=$city['id'];
 		$vobank['district']=$district['id'];
		$this->assign("vobank",$vobank);
		$this->assign("address",$address);
		$this->assign("order_id",$_REQUEST['order_id']?$_REQUEST['order_id']:0);
		$this->display();
	}
 	public function delete(){
 		$address=M("member_address")->where("id={$_GET['id']}")->delete();
 		 
		  echo  '<script type="text/javascript">alert("成功");window.location.href="'.__APP__.'/member/memberaddress";</script>';
	}
	public function save_do(){
		if($_POST['default']==1){
			$va=M("member_address")->where("uid={$this->uid}")->save(["default"=>0]); 
		}

 		$va=M("member_address")->where("id={$_POST['id']}")->save($_POST); 
 	
	 	if($va){
 			ajaxmsg();
 		}else{ajaxmsg('操作失败，请重试',0);}
	}
 	public function add_do(){
		if($_POST['default']==1){
 			$va=M("member_address")->where("uid={$this->uid}")->save(["default"=>0]); 
		}
		$_POST['uid']=$this->uid;
 		$va=M("member_address")->add($_POST);
 		if($va){
 			ajaxmsg();
 		}else{ajaxmsg('操作失败，请重试',0);}
	}










    
}