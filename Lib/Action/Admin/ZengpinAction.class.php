<?php
class ZengpinAction extends ACommonAction
{

	public function index()
	{
		$map='';
		if(isset($_REQUEST['title'])&&!empty($_REQUEST['title'])){
			$map['title'] = array('like',"%".$_REQUEST['title']."%");
			$this->assign('title',$_REQUEST['title']);
		}

		$field = "*";
		$this->_list( M("zeng_pin"), $field, $map, "id", "DESC" );
		$this->display();
	}
	public function getzp()
	{
		$map='';
		if(isset($_REQUEST['title'])&&!empty($_REQUEST['title'])){
			$map['title'] = array('like',"%".$_REQUEST['title']."%");
			$this->assign('title',$_REQUEST['title']);
		}
		$field = "title,id";
		$list=M("zeng_pin")->field($field)->where($map)->limit('0,15')->select();
		$jsons['list'] =$list;
		outJson($jsons);
	}

	public function edit()
	{
		$id = intval($_GET['id'] );
		$vo =M( "zeng_pin" )->find( $id );
		$this->assign( "vo", $vo );
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

	public function ajaxsearch(){
		$map='';
		if(isset($_REQUEST['title'])&&!empty($_REQUEST['title'])){
			$map['title'] = array('like',"%".$_REQUEST['title']."%");
			$this->assign('title',$_REQUEST['title']);
		}
		$field = "*";
		$this->_list( M("zeng_pin"), $field, $map, "id", "DESC" );
		$this->display();
	}
	public function dohtadd(){
		$uids=$_POST['borrow_uids'];
		$uidlist=explode(',',$uids);
		unset($uidlist[0]);
		$conds=array();
		foreach ($uidlist as $item) {
			$data['uid']=$item;
			$data['allnum']=$data['num']=$_POST['allnum'];
			$data['zpid']=$_POST['zpid'];
			$data['add_time']=time();
			$data['borrow_id']= $this->admin_id;
			$data['type']='3';
			$data['marke']=$_POST['marke'];
			$conds[]=$data;
		}
		$newxid=M("member_zengpin")->addAll($conds);
		if($newxid) $this->success("处理成功");
		else $this->error("处理失败");
	}
}

?>
