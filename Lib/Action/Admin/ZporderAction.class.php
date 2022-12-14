<?php
class ZporderAction extends ACommonAction
{

	public function index1()
	{
		if(!empty($_REQUEST['ordernum'])){
			$map['ordernum'] = $_REQUEST['ordernum'];
			$search['ordernum'] = $_REQUEST['ordernum'];
		}
		if(!empty($_REQUEST['id'])){
			$map['borrow_id'] = $_REQUEST['id'];
			$search['borrow_id'] = $_REQUEST['id'];
		}
		$this->assign("xaction",'index');
		$this->assign("search", $search);
		$this->assign("query", http_build_query($search));
		$map['type']='1';
		$field = "*";
		$this->_list(M("zporder"), $field,$map, "id", "DESC" );
		$this->display();
	}

	public function index()
	{
		//分页处理
		import("ORG.Util.Page");
		if(!empty($_REQUEST['ordernum'])){
			$map['zp.ordernum'] = $_REQUEST['ordernum'];
			$search['ordernum'] = $_REQUEST['ordernum'];
		}
		if(!empty($_REQUEST['id'])){
			$map['borrow_id'] = $_REQUEST['id'];
			$search['borrow_id'] = $_REQUEST['id'];
		}
		if(!empty($_REQUEST['name'])){
			$map['m.real_name']=array('like',"%". $_REQUEST['name']."%");
			$search['name'] = $_REQUEST['name'];
		}
		$map['zp.type']='1';
		$order="zp.add_time desc";
		$count =M("zporder zp")->where($map)->join("{$this->pre}member_info m ON m.uid=zp.uid")->order($order)->count();
		$p = new Page($count, C('ADMIN_PAGE_SIZE'));
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		//分页处理
		$field= 'zp.*';
		$list=M("zporder zp")->field($field)->where($map)->join("{$this->pre}member_info m ON m.uid=zp.uid")->order($order)->limit($Lsql)->select();
		$list = $this->_listFilter($list);
		//$this->assign("bj", array("gt"=>'大于',"eq"=>'等于',"lt"=>'小于'));
		$this->assign("list", $list);
		$this->assign("pagebar", $page);
		$this->assign("search", $search);
		//$this->assign("xaction",ACTION_NAME);
		$this->assign("query", http_build_query($search));
		$this->display();
	}

	public function zindex1()
	{
		if(!empty($_REQUEST['ordernum'])){
			$map['ordernum'] = $_REQUEST['ordernum'];
			$search['ordernum'] = $_REQUEST['ordernum'];
		}
		if(!empty($_REQUEST['id'])){
			$map['borrow_id'] = $_REQUEST['id'];
			$search['borrow_id'] = $_REQUEST['id'];
		}
		$this->assign("xaction",'zindex');
		$this->assign("search", $search);
		$this->assign("query", http_build_query($search));
		$map['type']='2';

		$field = "*";
		$this->_list(M("zporder"), $field,$map, "id", "DESC" );
		$this->display();
	}
	public function zindex()
	{
		//分页处理
		import("ORG.Util.Page");
		if(!empty($_REQUEST['ordernum'])){
			$map['zp.ordernum'] = $_REQUEST['ordernum'];
			$search['ordernum'] = $_REQUEST['ordernum'];
		}
		if(!empty($_REQUEST['id'])){
			$map['borrow_id'] = $_REQUEST['id'];
			$search['borrow_id'] = $_REQUEST['id'];
		}
		if(!empty($_REQUEST['name'])){
			$map['m.real_name']=array('like',"%". $_REQUEST['name']."%");
			$search['name'] = $_REQUEST['name'];
		}
		$map['_string'] = 'zp.type=2 or (zp.type=3 AND zp.smstatus=3)';
		$order="zp.add_time desc";
		
		$count =M("zporder zp")->where($map)->join("{$this->pre}member_info m ON m.uid=zp.uid")->order($order)->count();
		$p = new Page($count, C('ADMIN_PAGE_SIZE'));
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		//分页处理
		$field= 'zp.*';
		$list=M("zporder zp")->field($field)->where($map)->join("{$this->pre}member_info m ON m.uid=zp.uid")->order($order)->limit($Lsql)->select();
		//var_dump(M("zporder zp")->getLastSql());
		$list = $this->_listFilter($list);
		//$this->assign("bj", array("gt"=>'大于',"eq"=>'等于',"lt"=>'小于'));
		$this->assign("list", $list);
		$this->assign("pagebar", $page);
		$this->assign("search", $search);
		//$this->assign("xaction",ACTION_NAME);
		$this->assign("query", http_build_query($search));
		$this->display();
	}
	public function _listFilter($list){
		$listType = C('ztstatus');
		$row=array();
		foreach($list as $key=>$v){
			if($v['ztype']=='1'){
				$v['zhuangtai'] = $listType[$v['status']];
				$mmap["zp.id"]=$v["id"];
				$files="mm.user_name,z.title,z.zxprice,b.borrow_name";
				$zinfo=m("zporder zp")->field($files)
					->join('lzh_borrow_info b on zp.borrow_id=b.id')
					->join('lzh_members mm on zp.uid=mm.id')
					->join('lzh_zeng_pin z on zp.zpid=z.id')->where($mmap)->find();
				$v['user_name'] =$zinfo['user_name'];
				$v['title'] = $zinfo['title'];
				$v['zxprice'] = $zinfo['zxprice'];
				$v['borrow_name'] = $zinfo['borrow_name'];
				$row[$key]=$v;
			}
			if($v['ztype']=='2'){
				$v['zhuangtai'] = $listType[$v['status']];
				$mmap["zp.id"]=$v["id"];
				$files="mm.user_name,z.title,z.zxprice,b.title as borrow_name";
				$zinfo=m("zporder zp")->field($files)
					->join('lzh_ys_good b on zp.borrow_id=b.id')
					->join('lzh_members mm on zp.uid=mm.id')
					->join('lzh_zeng_pin z on zp.zpid=z.id')->where($mmap)->find();
				$v['user_name'] =$zinfo['user_name'];
				$v['title'] = $zinfo['title'];
				$v['zxprice'] = $zinfo['zxprice'];
				$v['borrow_name'] = $zinfo['borrow_name'];
				$row[$key]=$v;
			}
			if($v['ztype']=='3'){
				$v['zhuangtai'] = $listType[$v['status']];
				$mmap["zp.id"]=$v["id"];
				$files="mm.user_name,z.title,z.zxprice,b.marke as borrow_name";
				$zinfo=m("zporder zp")->field($files)
					->join('lzh_member_zengpin b on zp.mzpid=b.id')
					->join('lzh_members mm on zp.uid=mm.id')
					->join('lzh_zeng_pin z on zp.zpid=z.id')->where($mmap)->find();
				$v['user_name'] =$zinfo['user_name'];
				$v['title'] = $zinfo['title'];
				$v['zxprice'] = $zinfo['zxprice'];
				$v['borrow_name'] = $zinfo['borrow_name'];
				$row[$key]=$v;
			}

		}
//		var_dump($row);
		return $row;
	}

	public function edit()
	{
		$id = intval($_GET['id'] );
		$this->assign( "zhuangtai", C('ztstatus') );
		$vo =M( "zporder" )->find( $id );
		$vo['title'] = M("zeng_pin")->getFieldById($vo['zpid'],'title');

		$shxx=M('zposh')->where(array('zpoid'=>$vo['id']))->find();
		$shxx['images']=explode(',',$shxx['images']);

		$this->assign( "shxx", $shxx );
		$this->assign( "vo", $vo );
		$this->display();
	}

	public function _doAddFilter($m)
	{

	}

	public function _doEditFilter($m)
	{
		if(!empty($m->wuliu)&&!empty($m->yundan)){
			$m->fh_time=time();
			$dt['name']=M("zeng_pin")->getFieldById($m->zpid,'title');
			$dt['phone']=$m->uphone;
			$dt['danhao']=$m->yundan;
			notice1("14", $_POST['uid'],$dt);
		}

		if($m->status==5){
			$shdata['up_time']=time();
			$shdata['glmark']=$_REQUEST['glmark'];
			$shdata['status']=2;
			M('zposh')->where('zpoid='.$m->id)->save($shdata);
		}
		return $m;
	}

	public function export(){
		import("ORG.Io.Excel");
		$map=array();
		if(!empty($_REQUEST['ordernum'])){
			$map['ordernum'] = $_REQUEST['ordernum'];
			$search['ordernum'] = $_REQUEST['ordernum'];
		}
		if(!empty($_REQUEST['id'])){
			$map['borrow_id'] = $_REQUEST['id'];
			$search['borrow_id'] = $_REQUEST['id'];
		}
		$map['type']=1;

		$files="zp.*,mm.user_name,z.title,z.zxprice,b.borrow_name";
		$list=m("zporder zp")->field($files)
			->join('lzh_borrow_info b on zp.borrow_id=b.id')
			->join('lzh_members mm on zp.uid=mm.id')
			->join('lzh_zeng_pin z on zp.zpid=z.id')->where($map)->order("zp.id desc")->select();


		$type = C('MONEY_LOG');
		$row=array();
		$row[0]=array('序号','订单编号','项目名称','赠品名称','会员名称','数量','姓名','电话','地区','地址','添加时间');
		$i=1;
		foreach($list as $v){
			$row[$i]['i'] = $i;
			$row[$i]['ordernum'] = $v['ordernum'];
			$row[$i]['borrow_name'] = $v['borrow_name'];
			$row[$i]['title'] = $v['title'];
			$row[$i]['user_name'] = $v['user_name'];
			$row[$i]['num'] = $v['num'];
			$row[$i]['uname'] = $v['uname'];
			$row[$i]['uphone'] = $v['uphone'];
			$row[$i]['province'] = $v['province'].'-'.$v['city'].'-'.$v['district'];
			$row[$i]['address'] = $v['address'];
			$row[$i]['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
			$i++;
		}
		$xls = new Excel_XML('UTF-8', false, 'datalist');
		$xls->addArray($row);
		$xls->generateXML($list[0]['borrow_name'].'赠品折现');
	}
	public function export1(){
		import("ORG.Io.Excel");
		$map=array();
		if(!empty($_REQUEST['ordernum'])){
			$map['ordernum'] = $_REQUEST['ordernum'];
			$search['ordernum'] = $_REQUEST['ordernum'];
		}
		if(!empty($_REQUEST['id'])){
			$map['borrow_id'] = $_REQUEST['id'];
			$search['borrow_id'] = $_REQUEST['id'];
		}
		$map['type']=2;
		$files="zp.*,mm.user_name,z.title,z.zxprice,b.borrow_name";
		$list=m("zporder zp")->field($files)
			->join('lzh_borrow_info b on zp.borrow_id=b.id')
			->join('lzh_members mm on zp.uid=mm.id')
			->join('lzh_zeng_pin z on zp.zpid=z.id')->where($map)->order("zp.id desc")->select();
		$type = C('MONEY_LOG');
		$row=array();

		$row[0]=array('序号','订单编号','项目名称','赠品名称','会员名称','折现单价','数量','折现金额','添加时间');
		$i=1;
		foreach($list as $v){
			$row[$i]['i'] = $i;
			$row[$i]['ordernum'] = $v['ordernum'];
			$row[$i]['borrow_name'] = $v['borrow_name'];
			$row[$i]['title'] = $v['title'];
			$row[$i]['user_name'] = $v['user_name'];
			$row[$i]['zxprice'] = $v['zxprice'];
			$row[$i]['num'] = $v['num'];
			$row[$i]['money'] = $v['money'];
			$row[$i]['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
			$i++;
		}
		$xls = new Excel_XML('UTF-8', false, 'datalist');
		$xls->addArray($row);
		$xls->generateXML($list[0]['borrow_name'].'赠品折现');
	}




}

?>
