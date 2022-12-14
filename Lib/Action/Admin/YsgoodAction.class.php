<?php
class YsgoodAction extends ACommonAction
{

	public function index()
	{
		$field = "*";
		$this->_list(M("ys_good"), $field, "", "id", "DESC" );
		$this->display();
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
