<?php
// 本类由系统自动生成，仅供测试用途
class MemberaddressAction extends MCommonAction {
 
	public function index(){
			$va=M("member_address as a")
//		->join(' member_info as i ON i.uid = a.uid')
//		->field('member_address.*')
		->where("uid={$this->uid}")->select();
		$area=M("area")->select();
		$this->assign("area",$area);
		$this->assign("va",$va);
		
// var_dump($va);die;
		$this->display();
	}
	
	
 	public function add(){
 		    	    	$str=getenv("HTTP_REFERER");
			     if(!eregi("/Memberaddress/","$str")){ 
					 setcookie('url',$str);
					 $this->assign("shangyiyeurl",$str ); 
					  $this->assign("xuze",1 ); 
				}else{
					if(empty($_COOKIE['url'])){
						$this->assign("shangyiyeurl", "/Wapmember/index/"); 
					}else{
						$this->assign("shangyiyeurl",$_COOKIE['url']); 
					}
					
					
			//		echo "0"; die;
				}
		
//   var_dump($_COOKIE['url']);
	//var_dump($_COOKIE['url'] );
		$this->display();
	}
	
 	public function save(){
 		$va=M("member_address as a")
//		->join(' member_info as i ON i.uid = a.uid')
//		->field('member_address.*')
		->where("id={$_GET['id']}")->find();
		$this->assign("va",$va);
		$this->display();
	}
		public function delete(){
 		$va=M("member_address")
//		->join(' member_info as i ON i.uid = a.uid')
//		->field('member_address.*')
		->where("id={$_GET['id']}")->delete();
		 echo  '<script type="text/javascript">alert("成功");window.location.href="'.__APP__.'/Wapmember/memberaddress";</script>';
	}
		public function save_do(){
//				var_dump($_POST);
//		die;
//$_POST['default']=1;
if($_POST['default']==1){
	
 		$va=M("member_address")
//		->join(' member_info as i ON i.uid = a.uid')
//		->field('member_address.*')
		->where("uid={$this->uid}")->save(["default"=>0]); 
	
} else{
	 $_POST['default']=0;
}
// 	var_dump(M("member_address")->getlastsql());
	
		$va=M("member_address")
//		->join(' member_info as i ON i.uid = a.uid')
//		->field('member_address.*')
		->where("id={$_POST['id']}")->save($_POST); 
//		var_dump(M("member_address")->getlastsql());
//		die;
 	 	echo  '<script type="text/javascript">alert("成功");window.location.href="'.__APP__.'/Wapmember/memberaddress";</script>';
	}
 public function add_do(){
//				var_dump($_POST);
//		die;
if($_POST['default']==1){
	
 		$va=M("member_address")
//		->join(' member_info as i ON i.uid = a.uid')
//		->field('member_address.*')
		->where("uid={$this->uid}")->save(["default"=>0]); 
	
}
$_POST['uid']=$this->uid;
 		$va=M("member_address")
//		->join(' member_info as i ON i.uid = a.uid')
//		->field('member_address.*') 
		->add($_POST); 
// 	var_dump(M("member_address")->getlastsql());
//var_dump(session('surl'));
// 	die;
	 	echo  '<script type="text/javascript">alert("成功");window.location.href="'.$_COOKIE['url'].'";</script>';
	}










    
}