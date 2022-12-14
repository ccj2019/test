<?php
class YsorderAction extends ACommonAction
{

	public function index()
	{
		$field = "*";
		$map['ysid']=$_GET['id'];
		$this->assign('id',$_REQUEST['id']);
		$this->assign('gstatus',$_REQUEST['gs']);
//		var_dump($_REQUEST['gs']);
		$map['payway'] = array('in',"1,2");
		if(isset($_REQUEST['payway'])&&!empty($_REQUEST['payway'])){
			$map['payway'] = $_REQUEST['payway'];
			$this->assign('payway',$_REQUEST['payway']);
		}else{
			$this->assign('payway',0);
		}
		$getval['payway'] = $_REQUEST['payway'];
		$map['status'] = array('in',"0,1,2");
		if(isset($_REQUEST['status'])&&!empty($_REQUEST['status'])){
			$map['status'] = $_REQUEST['status'];
			if($_REQUEST['status']==3){
				$map['status'] = 0;
			}
			$this->assign('status',$_REQUEST['status']);
		}else{
			$this->assign('status',0);
		}
		$getval['status'] = $_REQUEST['status'];

		$start_time=$_REQUEST['start_time'];
		$end_time=$_REQUEST['end_time'];

		if(!empty($start_time)&&!empty($end_time)){
			$map['add_time'] = array('between',array(strtotime($start_time),strtotime($end_time)));
			$this->assign('start_time',$start_time);
			$this->assign('end_time',$end_time);
		}
		$this->_list(M("ys_order"), $field, $map, "id", "DESC" );
		$this->display();
	}

	public function _listFilter($list){
		$listType = C('yszffs');
		$zhuangtai = C('ysostatus');
		$row=array();
		foreach($list as $key=>$v){
			$v['zffs'] = $listType[$v['payway']];
			$v['zhuangtai'] = $zhuangtai[$v['status']];
			$field='i.real_name,m.user_name,m.user_phone';
			$minfo=M("members m")->join("lzh_member_info i on m.id=i.uid")->where("m.id=".$v["buid"])->field($field)->find();
			$v['user_name']=$minfo['user_name'];
			$v['user_phone']=$minfo['user_phone'];
			$v['real_name']=$minfo['real_name'];
			$row[$key]=$v;
		}
		return $row;
	}
	public function delwzf(){
		$map["status"]='0';
		$map["ysid"]=$_GET['id'];
		$list=M('ys_order')->where($map)->find();
		if($list){
			$res=M('ys_order')->where($map)->delete();
			if($res==1){
				$this->success('操作成功');
			}
			if($res==0){
				$this->error('操作失败');
			}
		}else{
			$this->error('不存在需要清除的数据');
		}

	}

	public function export(){
		import("ORG.Io.Excel");
		$map=array();
		$map['o.payway'] = array('in',"1,2");
		if(isset($_REQUEST['p'])&&!empty($_REQUEST['p'])){
			$map['o.payway'] = $_REQUEST['p'];
		}
		$map['o.status'] = array('in',"0,1,2");
		if(isset($_REQUEST['s'])&&!empty($_REQUEST['s'])){
			$map['o.status'] = $_REQUEST['s'];
			if($_REQUEST['s']==3){
				$map['o.status'] = 0;
			}
		}
		$map['o.ysid']= $_REQUEST['oid'];

		$start_time=strtotime($_REQUEST['start_time']);
		$end_time=strtotime($_REQUEST['end_time']);
		if(!empty($start_time)&&!empty($end_time)){
			$map['add_time'] = array('between',"{$start_time},{$end_time}");
//			$this->assign('o.start_time',$start_time);
//			$this->assign('o.end_time',$end_time);
		}


		$field= 'o.*,i.real_name,m.user_name,m.user_phone';
		$list = M('ys_order o')->join('lzh_members m on m.id=o.buid')->join('lzh_member_info i on o.buid=i.uid')->field($field)->where($map)->select();
		$listType = C('yszffs');
		$zhuangtai = C('ysostatus');
		$row=array();
		foreach($list as $key=>$v){
			$v['zffs'] = $listType[$v['payway']];
			$v['zhuangtai'] = $zhuangtai[$v['status']];
			$list[$key]=$v;
		}
		$row=array();
//		$row[0]=array('序号','用户名','真实姓名','购买分数','支付金额','收益','赠品分数','购买时间','收购时间','状态','支付方式');
////		$i=1;
////		foreach($list as $v){
////			$row[$i]['i'] = $i;
////			$row[$i]['user_name'] = $v['user_name'];
////			$row[$i]['real_name'] = $v['real_name'];
////			$row[$i]['fenshu'] = $v['fenshu'];
////			$row[$i]['money'] = $v['money'];//getLeveName($v['credits'],2);
////			$row[$i]['shouyi'] = $v['shouyi'];
////			$row[$i]['zpnum'] =$v['zpnum'];
////			$row[$i]['add_time'] =date("Y-m-d H:i:s",$v['add_time']);//$v['yubi'];
////			$row[$i]['end_time'] =date("Y-m-d H:i:s",$v['end_time']);
////			$row[$i]['zhuangtai'] = $v["zhuangtai"];
////			$row[$i]['zffs'] = $v['zffs'];
////			$i++;
////		}

		$row[0]=array('认购金额','真实姓名','用户名','鱼币','余额','优惠券','支付金额','收益','购买时间','收购时间','状态','支付方式');
		$i=1;
		foreach($list as $v){
			$row[$i]['money'] = $v['money'];
			$row[$i]['real_name'] = $v['real_name'];
			$row[$i]['user_name'] = $v['user_name'];
			$row[$i]['yubi'] = $v['yubi'];
			$row[$i]['account'] = $v['account'];
			$row[$i]['hongbao'] = $v['hongbao'];

			$row[$i]['sfmoney'] = $v['sfmoney'];//getLeveName($v['credits'],2);

			$row[$i]['shouyi'] = $v['shouyi'];
			$row[$i]['add_time'] =date("Y-m-d H:i:s",$v['add_time']);//$v['yubi'];
			$row[$i]['end_time'] =date("Y-m-d H:i:s",$v['end_time']);
			$row[$i]['zhuangtai'] = $v["zhuangtai"];
			$row[$i]['zffs'] = $v['zffs'];

			$i++;
		}

		//print_r($row);
		//exit;
		$xls = new Excel_XML('UTF-8', false, 'datalist');
		$xls->addArray($row);
		$xls->generateXML("datalistcard");
	}


	public function dobulu(){

		vendor('AppPay.pay');
		$wappay = new pay();//var_dump($wappay);exit;
		$res=$wappay->bulu($_GET['oid']);
		if($res==1){
			$this->success('操作成功');
		}
		if($res==0){
			$this->error('未查到支付信息');
		}
//		$this->error('非法操作');
	}
	public function add(){
		$zplist=M("zeng_pin")->order("id desc")->select();
		$this->assign('zplist',$zplist);
		$this->display();
	}
	public function edit()
	{
		$id = intval($_GET['id'] );
		$vo =M( "ys_good" )->find( $id );
		$this->assign( "vo", $vo );
		$zplist=M("zeng_pin")->order("id desc")->select();
		$this->assign('zplist',$zplist);
		$borrow_status = C("YG_STATUS");
		if($vo['status']=='1'){
			for($i=1;$i<=10;$i++){
				if(in_array($i,array("1","2")) ) continue;
				unset($borrow_status[$i]);
			}
		}
		if($vo['status']=='2'){
			for($i=1;$i<=10;$i++){
				if(in_array($i,array("1","2",'3')) ) continue;
				unset($borrow_status[$i]);
			}
		}

		$this->assign('status',$borrow_status);
		$this->display();
	}

	public function _doAddFilter($m)
	{
		$m->add_time = time( );
		foreach ( $GLOBALS['_POST']['swfimglist'] as $key => $v )
		{
			$row[$key]['img'] = substr( $v, 1 );
			$row[$key]['info'] = $_POST['picinfo'][$key];
			$row[$key]['url'] = $_POST['urlinfo'][$key];
		}
		$m->images = serialize( $row );
		$m->end_time=strtotime($m->end_time);
		return $m;
	}

	public function _doEditFilter($m)
	{
		foreach ( $GLOBALS['_POST']['swfimglist'] as $key => $v )
		{
			$row[$key]['img'] = substr( $v, 1 );
			$row[$key]['info'] = $_POST['picinfo'][$key];
			$row[$key]['url'] = $_POST['urlinfo'][$key];
		}
		$m->images = serialize( $row );
//		var_dump($m);exit;
		if($m->status=='2' && empty($m->sx_time)){
			$m->sx_time=time();
		}
		$m->end_time=strtotime($m->end_time);
		return $m;
	}

	public function swfUpload( )
	{
		if ( $_POST['picpath'] )
		{
			$imgpath = substr( $_POST['picpath'], 1 );
			if ( in_array( $imgpath, $_SESSION['imgfiles'] ) )
			{
				unlink( C( "WEB_ROOT" ).$imgpath );
				$thumb = get_thumb_pic( $imgpath );
				$res = unlink(C("WEB_ROOT").$thumb );
				if ( $res )
				{
					$this->success("删除成功", "", $_POST['oid'] );
				}
				else
				{
					$this->error( "删除失败", "", $_POST['oid'] );
				}
			}
			else
			{
				$this->error( "图片不存在", "", $_POST['oid'] );
			}
		}
		else
		{
			//$_REQUEST["PHPSESSID"]=
			$this->savePathNew = C( "ADMIN_UPLOAD_DIR" )."Ad/";
			$this->thumbMaxWidth = "100";
			$this->thumbMaxHeight = "100";
			$this->saveRule = date( "YmdHis", time()).rand(0,1000);
			$info = $this->CUpload();
			$data['product_thumb'] = $info[0]['savepath'].$info[0]['savename'];
			if ( !isset( $_SESSION['count_file'] ) )
			{
				$_SESSION['count_file'] = 1;
			}
			else
			{
				++$_SESSION['count_file'];
			}
			$_SESSION['imgfiles'][$_SESSION['count_file']] = $data['product_thumb'];
			echo "{$_SESSION['count_file']}:".__ROOT__."/".$data['product_thumb'];
		}
	}

}

?>
