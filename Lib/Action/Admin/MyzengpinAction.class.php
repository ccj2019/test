<?php
class MyzengpinAction extends ACommonAction
{

	public function index()
	{
		$map['num'] =array('neq',0);
		$this->assign("xaction",'index');
		$this->assign("search", $search);
		$this->assign("query", http_build_query($search));

		$field = "*";
		$this->_list(M("member_zengpin"), $field,$map, "id", "DESC" );
		$this->display();
	}

	public function _listFilter($list){

		$row=array();
		foreach($list as $key=>$v){

			if($v['type']==1){
				$mmap["mz.id"]=$v["id"];
				$files="mm.user_name,z.title,z.zxprice,b.borrow_name";
				$zinfo=m("member_zengpin mz")->field($files)
					->join('lzh_borrow_info b on mz.borrow_id=b.id')
					->join('lzh_members mm on mz.uid=mm.id')
					->join('lzh_zeng_pin z on mz.zpid=z.id')->where($mmap)->find();
				$v['user_name'] =$zinfo['user_name'];
				$v['title'] = $zinfo['title'];

				$v['borrow_name'] = $zinfo['borrow_name'];
				$row[$key]=$v;
			}
			if($v['type']==2){
				$mmap["mz.id"]=$v["id"];
				$files="mm.user_name,z.title,z.zxprice,b.title as borrow_name";
				$zinfo=m("member_zengpin mz")->field($files)
					->join('lzh_ys_good b on mz.borrow_id=b.id')
					->join('lzh_members mm on mz.uid=mm.id')
					->join('lzh_zeng_pin z on mz.zpid=z.id')->where($mmap)->find();
				$v['user_name'] =$zinfo['user_name'];
				$v['title'] = $zinfo['title'];

				$v['borrow_name'] = $zinfo['borrow_name'];
				$row[$key]=$v;
			}
			if($v['type']==3){
				$mmap["mz.id"]=$v["id"];
				$files="mm.user_name,z.title,z.zxprice";
				$zinfo=m("member_zengpin mz")->field($files)
					->join('lzh_members mm on mz.uid=mm.id')
					->join('lzh_zeng_pin z on mz.zpid=z.id')->where($mmap)->find();
				$v['user_name'] =$zinfo['user_name'];
				$v['title'] = $zinfo['title'];

				$v['borrow_name'] = $v['marke'];
				$row[$key]=$v;

			}

		}
		return $row;
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
	public function htindex(){
		$this->assign("xaction",'index');
		$this->assign("search", $search);
		$this->assign("query", http_build_query($search));
		$map['type']=3;
		$field = "*";
		$this->_list(M("member_zengpin"), $field,$map, "id", "DESC" );
		$this->display();
	}

}

?>
