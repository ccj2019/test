<?php
class YundanAction extends ACommonAction
{

	public function index()
	{
//		if(!empty($_REQUEST['num'])){
//			$map['num'] = $_REQUEST['num'];
//			$search['num'] = $_REQUEST['num'];
//		}
		if(!empty($_REQUEST['ordernum'])){
			$map['ordernum'] =array("like","%".$_REQUEST['ordernum']."%");
			$search['ordernum'] = $_REQUEST['ordernum'];
		}
		$this->assign("xaction",'index');
		$this->assign("search", $search);
		$this->assign("query", http_build_query($search));
//		$map['type']='1';
		$field = "*";
		$this->_list(M("yundan"), $field,$map, "id", "DESC" );
		//var_dump(M("yundan")->getLastSql());exit();
		$this->display();
	}
	public function _listFilter($list){
		$listType = C('ztstatus');
		$row=array();
		foreach($list as $key=>$v){
			$v['zhuangtai'] = $listType[$v['status']];
            $mmap["zp.id"]=$v["id"];
			$files="z.title";
			$zinfo=m("yundan zp")->field($files)->join('lzh_zeng_pin z on zp.zpid=z.id')->where($mmap)->find();
			$v['title'] = $zinfo['title'];
			$row[$key]=$v;
		}
		return $row;
	}
	public function add()
	{
		$id = intval($_GET['id'] );
		$this->assign( "zhuangtai", C('ztstatus') );
		$vo =M( "yundan" )->find( $id );
		$vo['title'] = M("zeng_pin")->getFieldById($vo['zpid'],'title');
		$this->assign( "vo", $vo );
		$zplist=M("zeng_pin")->order("id desc")->select();
		$this->assign('zplist',$zplist);
		$this->assign('id',$id);
		$this->display();
	}
	public function edit()
	{
		$id = intval($_GET['id'] );
		$this->assign( "zhuangtai", C('ztstatus') );
		$vo =M( "yundan" )->find( $id );
		$vo['title'] = M("zeng_pin")->getFieldById($vo['zpid'],'title');
		$this->assign( "vo", $vo );
		$zplist=M("zeng_pin")->order("id desc")->select();
		$this->assign('zplist',$zplist);
		$this->assign('id',$id);
		$this->display();
	}

	public function _doAddFilter($m)
	{
		if($m->zpid=='--请选择--'){
			$this->error("请选择赠品");
		}
		if(!empty($m->wuliu)&&!empty($m->yundan)){
			$m->fhtime=time();
			$m->status=2;
			$data['fh_time']=time();
			$dt['name']=M("zeng_pin")->getFieldById($m->zpid,'title');
			$dt['phone']=$m->phone;
			$dt['danhao']=$m->yundan;
			notice1("14", $_POST['uid'],$dt);
		}
		$m->ordernum=sprintf('%s-%s-%s', 'LP',rand(1000,9999),time());
		$m->time=time();
		return $m;
	}

	public function _doEditFilter($m)
	{
		if(!empty($m->wuliu)&&!empty($m->yundan)&&empty($m->fhtime)){
			$m->fhtime=time();
			$m->status=2;
			$dt['name']=M("zeng_pin")->getFieldById($m->zpid,'title');
			$dt['phone']=$m->uphone;
			$dt['danhao']=$m->yundan;
			notice1("14", $_POST['uid'],$dt);
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
