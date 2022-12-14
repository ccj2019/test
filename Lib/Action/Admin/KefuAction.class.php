<?php
// 全局设置
class KefuAction extends ACommonAction
{
	function __construct(){
		parent::__construct();
		$this->db = M('kefu');
		$this->db_gx = M('kefu_gx');
	}
    /**
    +----------------------------------------------------------
    * 默认操作
    +----------------------------------------------------------
    */
    public function index()
    {
		$list = $this->db->select();
		$zt=array("正常","冻结");
		$this->assign("zt",$zt);

		$this->_list($this->db,$field,'','time','DESC');
        $this->display();
    }
    //客服详情
    public function select($data)
    {
        $id = $data['kfid'];

        $start = $data['start_time'];

        $end = $data['end_time'];

        $timespan = strtotime(urldecode($start)).",".strtotime(urldecode($end));

        $map['add_time'] = $bet =  array("between",$timespan);

        $find = M('kefu_gx')->where(array('kfid'=>$id))->select();

        $list['count'] = M('kefu_gx')->where(array('kfid'=>$id))->count();

        $list['num'] = M('kefu_gx')->where(array('kfid'=>$id,'time'=>$bet))->count();

        $count = 0;
        $borrow_num =0;
        $borrow_sum_money = 0;
        $borrow_sum_yubi = 0;
        $bonus_num = 0;
        foreach ($find as $k => $v){

            $empty = M('member_moneylog')->where(array('uid'=>$v['memid']))->find();

            if(!empty($empty)){
                $count++;
            }
            $map['investor_uid'] = $v['memid'];

            $borrow_num += M('borrow_investor')->where($map)->count();

            $borrow_sum_money += M('borrow_investor')->where($map)->sum('investor_capital');

            $borrow_sum_yubi += M('borrow_investor')->where($map)->sum('yubi');

            $bonus = M('borrow_investor')->where($map)->select();

            $bonus = array_unique(array_column($bonus,'bonus_id'));

            $bonus_num += M('member_bonus')->where(array('id'=>array('in',$bonus)))->sum('money_bonus');

        }

        $list['empty'] = $count;

        $list['borrow_num'] = $borrow_num;

        $list['borrow_sum_money'] = $borrow_sum_money;

        $list['borrow_sum_yubi'] = $borrow_sum_yubi;

        $list['bonus_num'] = $bonus_num;

        $list['borrow_sum_yue'] = $borrow_sum_money - $borrow_sum_yubi - $bonus_num;

        return $list;

//        $this->assign('list',$list);
//
//        $this->display();


    }
    public function edit(){
    	if(isset($_GET['id'])&&!empty($_GET['id'])){
    		$res=$this->db->find($_GET['id']);
    	}else{
    		$res["type"]='0';
    	}
    	$this->assign("vo",$res);
		$this->display('edit');
	}
	public function doEdits(){
		$isedit=0;
		if(isset($_POST['id'])&&!empty($_POST['id'])){
			$isedit=1;
			$res = $this->updateinfo($this->db);
		}else{
			$_POST["time"]=time();
			$res = $this->addinfo($this->db);
		}
		//var_dump($this->db);exit;
		if($res){
			if($isedit){
				 //成功提示
	            $this->assign('jumpUrl', __URL__."/".session('listaction'));
	            $this->success(L('修改成功'));
			}else{
				$this->assign('jumpUrl', __URL__);
            	$this->success(L('新增成功'));
				// $this->success('操作成功');
			}
		}else{
			$this->error('操作失败');
		}
	}
	//绑定客服客户
	public function khlist(){

		$res=$this->db->where(" type=0 ")->select();
		$typelist = $res;

		$this->assign('kflist',$typelist);
		foreach ($typelist as $tk => $tv) {
			$typeids[] = $tv['id'];
		}
		$typeids = implode(",", $typeids);
		$map['kfid'] = array('in',"$typeids");
		// $this->assign("type",$typelist);
		if(isset($_REQUEST['kfid'])&&!empty($_REQUEST['kfid'])){
			$map['kfid'] = $_REQUEST['kfid'];
			$this->assign('kfid',$_REQUEST['kfid']);
		}

		$nian=$_REQUEST["nian"];
		$yue=$_REQUEST["yue"];
		if($nian&&$yue){
			$this->assign('nian',$nian);
			$this->assign('yue',$yue);
			$riqi=strtotime($nian."-".$yue);
			$map['gx.time'] =array('between', array($riqi, strtotime("+1 months",$riqi)));
		}

        session('listaction',ACTION_NAME);
        $order="gx.time desc";
        //取得满足条件的记录数
        $count = $this->db_gx->where($map)->count(); 
        import("ORG.Util.Page");
        //创建分页对象
        $listRows = !empty($_REQUEST['listRows'])?$_REQUEST['listRows']:C('ADMIN_PAGE_SIZE');
        $p = new Page($count, $listRows);
   //var_dump($p);
        //分页查询数据
        //$list =M('kefu_gx gx')->join("lzh_members m ON m.id=gx.memid")->join("lzh_kefu kf ON kf.id=gx.kfid")->field('gx.*,m.user_name,m.user_phone,kf.name,kf.phone')->where($map)->limit($p->firstRow . ',' . $p->listRows)->select();

		$list= M('kefu_gx gx')->join("lzh_members m ON m.id=gx.memid")->join("lzh_kefu kf ON kf.id=gx.kfid")->field('gx.*,m.user_name,m.user_phone,kf.name,kf.phone')->where($map)->order($order)->limit($p->firstRow . ',' . $p->listRows)->select();
        //var_dump(M('kefu_gx gx')->getlastsql());
        //分页跳转的时候保证查询条件
        foreach ($map as $key => $val) {
            if (!is_array($val)) {
                $p->parameter .= "$key=" . urlencode($val) . "&";
            }
        }
    
	    foreach($search as $key => $val){
	            if (!is_array($val)) {
	                $p->parameter .= "$key=" . urlencode($val) . "&";
	            }
	    }
	         
        // if (method_exists($this, '_listFilter')) {
        //     $list = $this->_listFilter($list);
        // }
        //分页显示
        $page = $p->show();

        //列表排序显示
        $sortImg = $sort;                                   //排序图标
        $sortAlt = $sort == 'desc' ? '升序排列' : '倒序排列';    //排序提示
        $sort = $sort == 'desc' ? 1 : 0;                     //排序方式
//  	var_dump($list);die;
        //模板赋值显示
        $this->assign('list', $list);
        
        $this->assign("pagebar", $page);
        //var_dump($p);

        $this->display();
	}
	public function khadd(){
    	$res["time"]=time();
    	$this->assign("vo",$res);

    	$kflist=$this->db->select();
		$this->assign('kflist',$kflist);
		$this->display('khadd');
	}
	public function khdel(){
  //   	$res["time"]=time();
  //   	$this->assign("vo",$res);
		// $this->display('khedit');
		$id = $_GET['id'];
		$res = $this->delinfo($this->db_gx,$id);
		if($res){
			$this->success('删除成功！');
		}
	}
	public function doadd(){
		
		
		$_POST["time"]=time();
		$_POST["memid"]=$_POST["borrow_uid"];


		$map["memid"]=$_POST["borrow_uid"];
		$info=$this->db_gx->where($map)->find();
		if($info){
			$kfinfo=$this->db->where("id=".$info["kfid"])->find();
			$this->error('用户已经被绑定到客服：'.$kfinfo["name"]);
		}else{
			$res = $this->addinfo($this->db_gx);
			if($res){
				//$this->assign('jumpUrl', __URL__."/khlist");
				// $title="给客服绑定客户";
				// $mes="把客户id为{".$_POST["borrow_uid"]."},绑定到了{".$_POST["kfid"]."}";
				// $this->do_log($title,$mes);
	            $this->success('绑定成功', __URL__."/khlist");

			}else{
				$this->error('绑定失败');
			}
		}
		
	}
	//未绑定客服客户
	public function wkhlist(){
		// $subsql = M('kefu_gx')->field('memid')->buildSql();
  //       $map["id"]=array("not in",$subsql);

		if(isset($_REQUEST['user_name'])&&!empty($_REQUEST['user_name'])){
			//$map['user_name'] =array("like","%".$_REQUEST["user_name"]."%");
			$map="id not in (select memid from lzh_kefu_gx) and user_name like '%".$_REQUEST["user_name"]."%'";
			$this->assign('user_name',$_REQUEST['user_name']);
		}else{
			$map="id not in (select memid from lzh_kefu_gx)";
		}
		

        session('listaction',ACTION_NAME);
        $order="id desc";
        //取得满足条件的记录数
        $count = M('members')->where($map)->count(); 
        import("ORG.Util.Page");
        //创建分页对象
        $listRows = !empty($_REQUEST['listRows'])?$_REQUEST['listRows']:C('ADMIN_PAGE_SIZE');
        $p = new Page($count, $listRows);
        //分页查询数据
        
		$list= M('members')->field('id,user_name,user_phone,is_guide,reg_time')->where($map)->order($order)->limit($p->firstRow . ',' . $p->listRows)->select();
		//var_dump(M('members')->getlastsql());
		$arr=array("未实名","已实名");
		foreach ($list as $k => $v) {
			$id_status=M("members_status")->where("uid=".$v["id"])->getField("id_status");
			$list[$k]["renzheng"]=$arr[$id_status];
			$lmp["investor_uid"]=$v["id"];
			$lmp["borrow_id"]=array("neq",1);
			$tz=M('borrow_investor')->where($lmp)->count();
			if($tz>0){
				$list[$k]["touzi"]="已投资";
			}else{
				$list[$k]["touzi"]="未投资";
			}
		}
        //分页跳转的时候保证查询条件
        foreach ($map as $key => $val) {
            if (!is_array($val)) {
                $p->parameter .= "$key=" . urlencode($val) . "&";
            }
        }
    
	    foreach($search as $key => $val){
	            if (!is_array($val)) {
	                $p->parameter .= "$key=" . urlencode($val) . "&";
	            }
	    }
	        
        //分页显示
        $page = $p->show();

        //列表排序显示
        $sortImg = $sort;                                   //排序图标
        $sortAlt = $sort == 'desc' ? '升序排列' : '倒序排列';    //排序提示
        $sort = $sort == 'desc' ? 1 : 0;                     //排序方式
//  	var_dump($list);die;
        //模板赋值显示
        $this->assign('list', $list);
        
        $this->assign("pagebar", $page);
        //var_dump($p);

        $this->display();
	}
	public function khbangding(){
		$res["time"]=time();
    	$this->assign("vo",$res);

    	$kflist=$this->db->select();
		$this->assign('kflist',$kflist);
		$id=$_REQUEST["id"];
		$info=M('members')->field('id,user_name,user_phone')->where("id=".$id)->find();
		$this->assign('info',$info);
		//var_dump($info);
		$this->display();
	}
	public function dobangding(){

		$_POST["time"]=time();
		$_POST["memid"]=$_POST["borrow_uid"];


		$map["memid"]=$_POST["borrow_uid"];
		$info=$this->db_gx->where($map)->find();
		if($info){
			$kfinfo=$this->db->where("id=".$info["kfid"])->find();
			$this->error('用户已经被绑定到客服：'.$kfinfo["name"]);
		}else{
			$res = $this->addinfo($this->db_gx);
	
			//var_dump($this->db);exit;
			if($res){
				$this->assign('jumpUrl', __URL__."/khlist");
	            $this->success(L('绑定成功'));

			}else{
				$this->error('绑定失败');
			}
		}
	}

	//客服客户统计
	public function khindex()
    {
    	$data = $this->select($_REQUEST);

    	$kflist=$this->db->select();
		$this->assign('kflist',$kflist);

		//var_dump($_REQUEST);
		$kfid=$_REQUEST["kfid"];
		// $start_time=$_REQUEST["start_time"];
		// $end_time=$_REQUEST["end_time"];
		
		$search['start_time'] = strtotime(urldecode($_REQUEST['start_time']));	
		$search['end_time'] = strtotime(urldecode($_REQUEST['end_time']));

		$typelist=$this->db_gx->where("kfid=".$kfid)->select();
		foreach ($typelist as $tk => $tv) {
			$typeids[] = $tv['memid'];
		}
		$typeids = implode(",", $typeids);

		$map['i.investor_uid'] = array('in',"$typeids");
		$timespan = strtotime(urldecode($_REQUEST['start_time'])).",".strtotime(urldecode($_REQUEST['end_time']));
		$map['i.add_time']=array("between",$timespan);
		$map["b.id"]=array("neq",1);
		$map["i.status"]=array("gt",3);
		$order="b.full_time desc,b.borrow_duration asc,b.id desc";

		$list= M('borrow_investor i')->join("lzh_borrow_info b ON b.id=i.borrow_id")->join("LEFT JOIN lzh_member_bonus bon ON bon.id = i.bonus_id")->join("lzh_members m ON m.id=i.investor_uid")->field('bon.money_bonus,m.user_name,i.id,i.investor_capital,i.add_time,i.yubi,b.borrow_duration,b.borrow_name')->where($map)->order($order)->select();
       	foreach ($list as $k => $v) {
       		// $bonusmone=0;
       		// if($v["bonus_id"]){
	       	// 	 $bonusmoney=M("member_bonus")->where("id=".$v["bonus_id"])->getField('money_bonus');
	       	// }
            $list[$k]["yue"]=round(($v["investor_capital"]-$v["yubi"]- $v["money_bonus"]),2);

       	}
		// var_dump($list);
		// var_dump(M('borrow_investor i')->getlastsql());
		$this->assign("search",$search);
		$this->assign("kfid",$kfid);
		$this->assign("list",$list);
		$this->assign('kfdata', $data);
		$this->display();
    }


    public function export(){
		import("ORG.Io.Excel");
		$map=array();

		$kflist=$this->db->select();
		$this->assign('kflist',$kflist);

		//var_dump($_REQUEST);
		$kfid=$_REQUEST["kfid"];
		// $start_time=$_REQUEST["start_time"];
		// $end_time=$_REQUEST["end_time"];

		$kfinfo=$this->db->where("id=".$kfid)->find();
		
		$search['start_time'] = strtotime(urldecode($_REQUEST['start_time']));	
		$search['end_time'] = strtotime(urldecode($_REQUEST['end_time']));

		$typelist=$this->db_gx->where("kfid=".$kfid)->select();
		foreach ($typelist as $tk => $tv) {
			$typeids[] = $tv['memid'];
		}
		$typeids = implode(",", $typeids);

		$map['i.investor_uid'] = array('in',"$typeids");
		$timespan = strtotime(urldecode($_REQUEST['start_time'])).",".strtotime(urldecode($_REQUEST['end_time']));
		$map['i.add_time']=array("between",$timespan);
		$map["b.id"]=array("neq",1);
		$map["i.status"]=array("gt",3);
		$order="b.full_time desc,b.borrow_duration asc,b.id desc";


		
		$list= M('borrow_investor i')
		->join("lzh_borrow_info b ON b.id=i.borrow_id")
		->join("lzh_members m ON m.id=i.investor_uid")
		->join("lzh_member_info inf ON inf.uid = m.id")
		->join("LEFT JOIN lzh_member_bonus bon ON bon.id = i.bonus_id")
		->field('m.user_name,i.investor_capital,i.add_time,i.yubi,b.borrow_duration,b.borrow_name,inf.real_name,i.bonus_id,bon.money_bonus')
		->where($map)->order($order)->select();
        
        //var_dump($_REQUEST);exit();
		$type = C('MONEY_LOG');
		$row=array();
		$row[0]=array('序号','投资项目','投资人','真实姓名','投资金额','使用余额','红包','使用鱼币','项目周期','投资时间');
		$i=1;
		foreach($list as $v){
				$row[$i]['i'] = $i;
				$row[$i]['borrow_name'] = $v['borrow_name'];
				$row[$i]['user_name'] = $v['user_name'];
				$row[$i]['real_name'] = $v['real_name'];
				$row[$i]['investor_capital'] = $v['investor_capital'];
				//红包金额
				$bonus = $v['bonus_id']==0?0:$v['money_bonus'];
				$row[$i]['yue'] = round($v['investor_capital']-$v['yubi']-$bonus);
				$row[$i]['hongbao'] = $bonus;
				$row[$i]['yubi'] = $v['yubi'];
				$row[$i]['borrow_duration'] = $v['borrow_duration'];
				$row[$i]['add_time'] = date("Y-m-d H:i:s",$v['add_time']);
				$i++;
		}
		//$kfinfo["name"].'从'.$_REQUEST['start_time'].'到'.$_REQUEST['end_time']
		$xls = new Excel_XML('UTF-8', false,"datalist");
		$xls->addArray($row);
		$xls->generateXML($kfinfo["name"].'从'.$_REQUEST['start_time'].'-'.$_REQUEST['end_time']);
	}

	public function dckefu()
    {
        vendor('PHPExcel.PHPExcel');
        $objPHPExcel = new PHPExcel(); // 实例一个excel核心类

        $sheets = $objPHPExcel->getActiveSheet()->setTitle('pt_order');//设置表格名称
        $objPHPExcel->setActiveSheetIndex()
            ->setCellValue('A1', '项目名称')//账号
            ->setCellValue('B1', '账号')//账号
            ->setCellValue('C1', '真实姓名')//真实姓名
            ->setCellValue('D1', '投标总额')//投标总额
            ->setCellValue('E1', '投标时间')//投标时间
            ->setCellValue('F1', '使用优惠券情况')//使用优惠券情况
            ->setCellValue('G1', '鱼币')//投标方式
            ->setCellValue('H1', '余额')//投标方式
        ;

        $id = $_REQUEST['id'];

        $name = M('kefu')->where(array('id'=>$id))->getField('name');

        $kefu = M('kefu_gx')->where(array('kfid'=>$id))->select();

        $map = array_column($kefu,'memid');

        $i = 2;
        $variable = array();

        foreach ($map as $item){

            $borrow = M('borrow_investor')->where(array('investor_uid' => $item))->select();

            foreach ($borrow as $key) {
				$variable[] = $key;
			}

		}

		$last_names = array_column($variable,'add_time');
		array_multisort($last_names,SORT_ASC,$variable);


       
            foreach ($variable as $k => $v){

                $borrow_name = M('borrow_info')->where(array('id'=>$v['borrow_id']))->getField('borrow_name');
                $sheets = $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $borrow_name);

                $find = M('member_info')->where(array('uid' => $v['investor_uid']))->find();
                $accont = M('members')->where(array('id' => $v['investor_uid']))->find();

                $sheets = $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $accont['user_name']);
                $sheets = $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $find['real_name']);

                $sheets = $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, $v['investor_capital']);
                $sheets = $objPHPExcel->getActiveSheet()->setCellValue('E' . $i, date("Y-m-d H:i:s",$v['add_time']));

                $bonus_id = 0;
                if ($v['bonus_id']==0) {
                    $sheets = $objPHPExcel->getActiveSheet()->setCellValue('F' . $i, "$bonus_id");
                } else {
                    $bonus_id = M('member_bonus')->where(array('id'=>$v['bonus_id']))->getField('money_bonus');
                    $sheets = $objPHPExcel->getActiveSheet()->setCellValue('F' . $i, "$bonus_id");
                }

                $sheets = $objPHPExcel->getActiveSheet()->setCellValue('G' . $i, $v['yubi']);
                $sheets = $objPHPExcel->getActiveSheet()->setCellValue('H' . $i, $v['investor_capital'] - ($v['yubi'] + $bonus_id));

                $i++;
            }

        



        //整体设置字体和字体大小
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial');//整体设置字体
        $objPHPExcel->getDefaultStyle()->getFont()->setSize(10);//整体设置字体大小

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20); //设置列宽度
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20); //设置列宽度
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20); //设置列宽度
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20); //设置列宽度
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20); //设置列宽度
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20); //设置列宽度
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20); //设置列宽度
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20); //设置列宽度


        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename='.'客服'.$name.'.xls'); //excel表格名称
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

        $objWriter->save('php://output');
    }
     public function wexport(){
		import("ORG.Io.Excel");
		$map=array();


		$map="m.id not in (select memid from lzh_kefu_gx)";
        $order="id desc";
		$list= M('members m')->join("lzh_member_info b ON b.uid=m.id")->field('id,user_name,user_phone,is_guide,reg_time,idcard,real_name')->where($map)->order($order)->limit('0,10000')->select();
		//var_dump(M('members')->getlastsql());
		$arr=array("未实名","已实名");
		foreach ($list as $k => $v) {
			$id_status=M("members_status")->where("uid=".$v["id"])->getField("id_status");
			$list[$k]["renzheng"]=$arr[$id_status];
			$lmp["investor_uid"]=$v["id"];
			$lmp["borrow_id"]=array("neq",1);
			$tz=M('borrow_investor')->where($lmp)->count();
			if($tz>0){
				$list[$k]["touzi"]="已投资";
			}else{
				$list[$k]["touzi"]="未投资";
			}
		}


        
        //var_dump($_REQUEST);exit();
		$type = C('MONEY_LOG');
		$row=array();
		$row[0]=array('序号','投资人','真实姓名','身份号','电话','是否投资','是否实名','注册时间');
		$i=1;
		foreach($list as $v){
				$row[$i]['id'] = $v["id"];
				$row[$i]['user_name'] = $v['user_name'];
				$row[$i]['real_name'] = $v['real_name'];
				$row[$i]['idcard'] = $v['idcard'];

				$row[$i]['user_phone'] = $v['user_phone'];
				$row[$i]['touzi'] = $v['touzi'];
				$row[$i]['renzheng'] = $v['renzheng'];
				$row[$i]['reg_time'] = date("Y-m-d H:i:s",$v['reg_time']);
				$i++;
		}
		//$kfinfo["name"].'从'.$_REQUEST['start_time'].'到'.$_REQUEST['end_time']
		$xls = new Excel_XML('UTF-8', false,"datalist");
		$xls->addArray($row);
		$xls->generateXML("未投资");
	}



}
?>