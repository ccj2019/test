<?php
// 全局设置
class GuaranteeAction extends ACommonAction
{
    /**
    +----------------------------------------------------------
    * 默认操作
    +----------------------------------------------------------
    */
    public function index()
    {
    	session('listaction',ACTION_NAME);
		$field= 'g.*,t.title as type_name';
		$list = M('Guarantee g')->field($field)->join("lzh_guartype t on t.id=g.type_id")->select();
		$this->assign('list',$list);
        $this->display();
    }
    public function control(){
		$list = M('guartype')->where(1)->order('sort_order')->select();
		$this->assign('list',$list);
        $this->display();
    }
	
    public function _addFilter()
    {
		/*foreach ( $GLOBALS['_POST']['swfimglist'] as $key => $v )
			{
				$row[$key]['img'] = substr( $v, 1 );
				$row[$key]['info'] = $_POST['picinfo'][$key];
				$row[$key]['url'] = $_POST['urlinfo'][$key];
			}
			$m->db_pics = serialize( $row );*/
		
		$typelist = M('guartype')->where(1)->select();
		//$typelist = get_type_leve_list('0','guartype');//分级栏目
		$this->assign('type_list',$typelist);

		//return $m;
    }
	

	public function _doAddFilter($m){
		if(!empty($_FILES['imgfile']['name'])){
			$this->saveRule = date("YmdHis",time()).rand(0,1000);
			$this->savePathNew = C('ADMIN_UPLOAD_DIR').'Danbao/' ;
			$this->thumbMaxWidth = C('ARTICLE_UPLOAD_W');
			$this->thumbMaxHeight = C('ARTICLE_UPLOAD_H');
			$info = $this->CUpload();
			$data['art_img'] = $info[0]['savepath'].$info[0]['savename'];
		}
		if($data['art_img']) $m->art_img=$data['art_img'];
		foreach ( $GLOBALS['_POST']['swfimglist'] as $key => $v )
			{
				$row[$key]['img'] = substr( $v, 1 );
				$row[$key]['info'] = $_POST['picinfo'][$key];
				$row[$key]['url'] = $_POST['urlinfo'][$key];
			}
			$m->db_pics = serialize( $row );
	
		return $m;
	}

	public function _doEditFilter($m){
		if(!empty($_FILES['imgfile']['name'])){
			$this->saveRule = date("YmdHis",time()).rand(0,1000);
			$this->savePathNew = C('ADMIN_UPLOAD_DIR').'Danbao/' ;
			$this->thumbMaxWidth = C('ARTICLE_UPLOAD_W');
			$this->thumbMaxHeight = C('ARTICLE_UPLOAD_H');
			$info = $this->CUpload();
			
			$data['art_img'] = $info[0]['savepath'].$info[0]['savename'];
		}
		if($data['art_img']) $m->art_img=$data['art_img'];
			foreach ( $GLOBALS['_POST']['swfimglist'] as $key => $v )
			{
				$row[$key]['img'] = substr( $v, 1 );
				$row[$key]['info'] = $_POST['picinfo'][$key];
				$row[$key]['url'] = $_POST['urlinfo'][$key];
			}
			
			$m->db_pics = serialize( $row );
	//echo $m->db_pics;
	//echo $m->db_pics;exit;	
		return $m;
	
	}

	public function _editFilter($id){
		/*foreach ( $GLOBALS['_POST']['swfimglist'] as $key => $v )
			{
				$row[$key]['img'] = substr( $v, 1 );
				$row[$key]['info'] = $_POST['picinfo'][$key];
				$row[$key]['url'] = $_POST['urlinfo'][$key];
			}
			$id->db_pics = serialize( $row );
			*/
			$typelist = M('guartype')->where(1)->select();
		//$typelist = get_type_leve_list('0','guartype');//分级栏目
		$this->assign('type_list',$typelist);
	//	return $id;
	}
	
	public function _listFilter($list){		
	 	$listType = D('Guartype')->getField('id,type_name');
		$row=array();
		foreach($list as $key=>$v){
			$v['type_name'] = $listType[$v['type_id']];
			$row[$key]=$v;
		}
		return $row;
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
			$this->savePathNew = C( "ADMIN_UPLOAD_DIR" )."Guarantee/";
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

	///////////////////////////////////	
	public function ajaxsearch(){

		$map = array();
		$title = isset($_REQUEST['title']) ? $_REQUEST['title'] : '';
		$db_tel = isset($_REQUEST['db_tel']) ? $_REQUEST['db_tel'] : '';
		$type_name = isset($_REQUEST['type_name']) ? $_REQUEST['type_name'] : '';
		$title and $map['g.title'] = $title;
		$db_tel and $map['g.db_tel'] = $db_tel;
		$type_name and $map['gt.title'] = urldecode($_REQUEST['type_name']);

		//分页处理
		import("ORG.Util.Page");
		$count = M('guarantee g')->join("guartype gt on gt.id=g.type_id")->where($map)->count('g.id');
		$p = new Page($count, C('ADMIN_PAGE_SIZE'));
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		//分页处理
		
		$field= 'g.id,g.db_tel,g.title,g.db_zijin,g.db_chengli,g.db_hezuo,g.db_address,g.type_id';
		$list = M('guarantee g')->field($field)->join("{$this->pre}guartype gt ON gt.id=g.type_id")->where($map)->limit($Lsql)->order('g.id DESC')->select();
		$list=$this->_listFilter($list);
		foreach($list as $key=>$val){
			$vx=array();
			$vx = M('guartype')->where("id=".$val["type_id"])->find();
			$val["type_name"]=$vx["title"];
			$vx=array();
			$vx = M('member_money')->where("uid=".$val["id"])->find();
			$vxx=array();
			$vxx = M('member_login')->where("uid=".$val["id"])->order("id desc")->find();
			$val["last_time"]=$vxx["add_time"];
			$val["last_ip"]=$vxx["ip"];
			//print_r($vx);
			//echo "<br>";
			$val["account_money"]=$vx["account_money"]+$vx["back_money"];
			$val["money_freeze"]=$vx["money_freeze"];
			$val["money_collect"]=$vx["money_collect"];
			$lists[]=$val;
		}
		$this->assign("list", $lists);
		$this->assign("pagebar", $page);

		$this->display();
	}
}


?>