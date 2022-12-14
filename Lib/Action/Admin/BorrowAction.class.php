<?php

// 全局设置

class BorrowAction extends ACommonAction

{

    /**

    +----------------------------------------------------------

    * 默认操作

    +----------------------------------------------------------

    */

    public function view()

    {

    			

		//获取投资列表

    	$id= empty($_REQUEST['id']) ? '': $_REQUEST['id'];

    	$borrowinfo = M("borrow_info")->find($id);

    	$zpmoney=0;
    	if($borrowinfo["is_huodong"]=='1'){
            $zpmoney=$borrowinfo['huodongnum'];
        }

		//print_r($borrowinfo);

		if(!is_array($borrowinfo) || ($borrowinfo['borrow_status']==0 && $this->uid!=$borrowinfo['borrow_uid']) ) $this->error("数据有误");		

		$borrowinfo['biao'] = $borrowinfo['borrow_times'];

		$borrowinfo['need'] = $borrowinfo['borrow_money'] - $borrowinfo['has_borrow'];

		$borrowinfo['lefttime'] =$borrowinfo['collect_time'] - time();

		$borrowinfo['progress'] = getFloatValue($borrowinfo['has_borrow']/$borrowinfo['borrow_money']*100,2);

		$borrowinfo['vouch_progress'] = getFloatValue($borrowinfo['has_vouch']/$borrowinfo['borrow_money']*100,2);

		$borrowinfo['vouch_need'] = $borrowinfo['borrow_money'] - $borrowinfo['has_vouch'];

		$borrowinfo["borrow_img"]=str_replace("'","",$borrowinfo["borrow_img"]);

		if($borrowinfo['borrow_img']=="")$borrowinfo['borrow_img']="/UF/Uploads/borrowimg/nopic.png";

		$this->assign("vo",$borrowinfo);

		$pre = C('DB_PREFIX'); 

		$fieldx = "bi.is_hetong,bi.contractid,bi.chajia,bi.fenshu,bi.id,bi.ztfenshu,bi.xsinvestor_capital,bi.investor_capital,bi.investor_interest,bi.investor_capital+bi.investor_interest as yingshou,bi.receive_capital,bi.receive_interest,bi.add_time,m.user_name,m.id as userid,bi.is_auto,mi.real_name";

		$investinfo = M("borrow_investor bi")->field($fieldx)->join("{$pre}members m ON bi.investor_uid = m.id")->join("{$pre}member_info mi on mi.uid = m.id")->where("bi.borrow_id={$id}")->order("bi.id ASC")->select();
        foreach ($investinfo as $k=>$v) {
            if(empty($v["fenshu"])){
                $investinfo[$k]["xsinvestor_capital"]=$v["investor_capital"];
            }
            if($zpmoney!=0){
                $investinfo[$k]["zpnum"]=floor($v["investor_capital"]/$zpmoney);
            }
		}

		$this->assign("investinfo",$investinfo);

		

		$this->display();

		//setBackUrl(session('listaction'));	

    }
    public function dozhuanhuan(){

        $id= empty($_REQUEST['id']) ? '': $_REQUEST['id'];
        $borrow_investor = M("borrow_investor")->find($id);

        $borrow_info = M("borrow_info")->find($borrow_investor["borrow_id"]);
        if($borrow_info["borrow_status"]>6){
            $this->error("已回款项目不可变");
        }
        if($borrow_investor["ztfenshu"]==0){
            $this->error("没有可以变更的分数");
        }
        $money=$borrow_investor["ztfenshu"]*$borrow_info["borrow_min"];
        M()->startTrans();
        $sql1="update lzh_borrow_investor set ztfenshu=0,`xsinvestor_capital`=`xsinvestor_capital`+".$money." where id=".$id;
        $res=M()->execute($sql1);


        $sql2="update lzh_borrow_info set `sghas_borrow`=`sghas_borrow`+".$money." where id=".$borrow_investor["borrow_id"];
        $res1=M()->execute($sql2);

        if($res&&$res1){
            M()->commit();
            $this->success("操作成功");
        }else{
            M()->rollback();
            $this->error("操作失败");
        }
    }
    public function borrowdc()
    {
        $id = $_REQUEST['id'];

        vendor('PHPExcel.PHPExcel');
        $objPHPExcel = new PHPExcel(); // 实例一个excel核心类

        $sheets = $objPHPExcel->getActiveSheet()->setTitle('pt_order');//设置表格名称
        $objPHPExcel->setActiveSheetIndex()
            ->setCellValue('A1', '账号')//账号
            ->setCellValue('B1', '真实姓名')//真实姓名
            ->setCellValue('C1', '投标总额')//投标总额
            ->setCellValue('D1', '投标时间')//投标时间
            ->setCellValue('E1', '使用优惠券情况')//使用优惠券情况
            ->setCellValue('F1', '鱼币')//投标方式
            ->setCellValue('G1', '余额')//投标方式
        ;

        $name = M('borrow_info')->where(array('id' => $id))->getField('borrow_name');
        $data = M('borrow_investor')->where(array('borrow_id' => $id))->order("id asc")->select();

        $i = 2;//第一行被表头占有了

        foreach ($data as $v) {
            $find = M('member_info i')->field("i.real_name,m.user_name")->join("lzh_members m ON i.uid = m.id")->where(array('i.uid' => $v['investor_uid']))->find();

            $sheets = $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $find['user_name']);
            $sheets = $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $find['real_name']);

            $sheets = $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $v['investor_capital']);
            $sheets = $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, date("Y-m-d H:i:s",$v['add_time']));

            $bonus_id = 0;
            if ($v['bonus_id']==0) {
                $sheets = $objPHPExcel->getActiveSheet()->setCellValue('E' . $i, "$bonus_id");
            } else {
                $bonus_id = M('member_bonus')->where(array('id'=>$v['bonus_id']))->getField('money_bonus');
                $sheets = $objPHPExcel->getActiveSheet()->setCellValue('E' . $i, "$bonus_id");
            }

            $sheets = $objPHPExcel->getActiveSheet()->setCellValue('F' . $i, $v['yubi']);
            $sheets = $objPHPExcel->getActiveSheet()->setCellValue('G' . $i, $v['investor_capital'] - ($v['yubi'] + $bonus_id));

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


        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename='.$name.'.xls'); //excel表格名称
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

        $objWriter->save('php://output');
    }
    public function vote()

    {

        //获取投资列表

        $pre = C('DB_PREFIX');

        $id  = empty($_REQUEST['id']) ? '' : $_REQUEST['id'];

        $act = empty($_REQUEST['act']) ? '' : $_REQUEST['act'];

        $is_send = empty($_REQUEST['is_send']) ? '0' : $_REQUEST['is_send'];

        $can_vote = empty($_REQUEST['can_vote']) ? '0' : $_REQUEST['can_vote'];

        $m   = D(ucfirst($this->getActionName()));

        $borrowinfo = $m->field('id,can_vote,vote_content,borrow_name')->find($id);

        if ($act == 'edit' && $m->create()) {            

             if($can_vote == 1 && $is_send){                

                //获取所有投资人的手机

                $userPhone = M('borrow_investor bi')->join("{$pre}members m on .m.id = bi.investor_uid")->where("m.user_phone != '' and bi.borrow_id = '{$id}'")->getField('m.id,m.user_phone');

                foreach (array_keys($userPhone) as $key => $value) {

                	addInnerMsg($value,'开启投票',"您参与的“{$borrowinfo['borrow_name']}”项目收到买家报价，请您及时参与投票！");

                }

                $userPhone = array_values($userPhone);

                $content = "用户您好，您投资的“{$borrowinfo['borrow_name']}”项目已开启投票，请及时登录网站参与投票！";

                $userPhone = implode($userPhone, ',');

                $rs = sendsms($userPhone,$content);                

            }            

            if ($m->save()) {

                $this->success('修改成功！');die;

            }

        }



        $fieldx   = "FROM_UNIXTIME(bv.add_time,'%Y-%m-%d %H:%i:%S') as add_time,IF(bv.`status` = 1,'赞成','不赞成') as status,m.user_name,m.id as userid,mi.real_name";

        $votelist = M("borrow_vote bv")->field($fieldx)->join("{$pre}members m ON bv.vote_uid = m.id")->join("{$pre}member_info mi on mi.uid = m.id")->where("bv.borrow_id={$id}")->order("bv.id DESC")->select();



        $this->assign("votelist", $votelist);



        $this->assign('vo', $borrowinfo);

        $this->display();

    }

    public function waitverify()

    {

		$map=array();

 		$map['b.id']=["neq",1];

		$map['b.borrow_status'] = 0;

		if(!empty($_REQUEST['uname'])&&!$_REQUEST['uid'] || $_REQUEST['uname']!=$_REQUEST['olduname']){

			$uid = M("members")->getFieldByUserName(text($_REQUEST['uname']),'id');

			$map['b.borrow_uid'] = $uid;

			$search['uid'] = $map['b.borrow_uid'];

			$search['uname'] = $_REQUEST['uname'];

		}

		if( !empty($_REQUEST['uid'])&&!isset($search['uname']) ){

			$map['b.borrow_uid'] = intval($_REQUEST['uid']);

			$search['uid'] = $map['b.borrow_uid'];

			$search['uname'] = $_REQUEST['uname'];

		}

		if(!empty($_REQUEST['bj']) && !empty($_REQUEST['money'])){

			$map['b.borrow_money'] = array($_REQUEST['bj'],$_REQUEST['money']);

			$search['bj'] = $_REQUEST['bj'];	

			$search['money'] = $_REQUEST['money'];	

		}

		/*	if(!empty($_REQUEST['start_time']) &&  $_REQUEST['start_time']> ){

			$timespan = strtotime(urldecode($_REQUEST['start_time'])).",".strtotime(urldecode($_REQUEST['end_time']));

			$map['b.add_time'] = array("between",$timespan);

			$search['start_time'] = urldecode($_REQUEST['start_time']);	

			$search['end_time'] = urldecode($_REQUEST['end_time']);	

		}

	

		if(!empty($_REQUEST['start_time']) && !empty($_REQUEST['end_time'])){

			$timespan = strtotime(urldecode($_REQUEST['start_time'])).",".strtotime(urldecode($_REQUEST['end_time']));

			$map['b.add_time'] = array("between",$timespan);

			$search['start_time'] = urldecode($_REQUEST['start_time']);	

			$search['end_time'] = urldecode($_REQUEST['end_time']);	

		}elseif(!empty($_REQUEST['start_time'])){

			$xtime = strtotime(urldecode($_REQUEST['start_time']));

			$map['b.add_time'] = array("gt",$xtime);

			$search['start_time'] = $xtime;	

		}elseif(!empty($_REQUEST['end_time'])){

			$xtime = strtotime(urldecode($_REQUEST['end_time']));

			$map['b.add_time'] = array("lt",$xtime);

			$search['end_time'] = $xtime;	

		}*/

		

		//分页处理

		import("ORG.Util.Page");

		$count = M('borrow_info b')->join("{$this->pre}members m ON m.id=b.borrow_uid")->where($map)->count('b.id');

		$p = new Page($count, C('ADMIN_PAGE_SIZE'));

		$page = $p->show();

		$Lsql = "{$p->firstRow},{$p->listRows}";

		//分页处理

		

		$field= 'b.id as id,b.borrow_name,b.borrow_uid,b.borrow_duration,b.borrow_type,b.updata,b.borrow_money,b.borrow_fee,b.borrow_interest_rate,b.repayment_type,b.add_time,m.user_name,m.id mid,b.is_tuijian,b.pid';

		$list = M('borrow_info b')->field($field)->join("{$this->pre}members m ON m.id=b.borrow_uid")->where($map)->limit($Lsql)->order("b.id DESC")->select();

		$list = $this->_listFilter($list);

        $this->assign("bj", array("gt"=>'大于',"eq"=>'等于',"lt"=>'小于'));

        $this->assign("list", $list);

        $this->assign("pagebar", $page);

        $this->assign("search", $search);

		$this->assign("xaction",ACTION_NAME);

        $this->assign("query", http_build_query($search));

		

        $this->display();

    }



    public function waitverifyquanbu()

    {

		$map=array();

 		$map['b.id']=["neq",1];

//		 var_dump(1);

//		 die;

		if(!empty($_REQUEST['uname'])&&!$_REQUEST['uid'] || $_REQUEST['uname']!=$_REQUEST['olduname']){

			$uid = M("members")->getFieldByUserName(text($_REQUEST['uname']),'id');

			$map['b.borrow_uid'] = $uid;

			$search['uid'] = $map['b.borrow_uid'];

			$search['uname'] = $_REQUEST['uname'];

		}

		if( !empty($_REQUEST['uid'])&&!isset($search['uname']) ){

			$map['b.borrow_uid'] = intval($_REQUEST['uid']);

			$search['uid'] = $map['b.borrow_uid'];

			$search['uname'] = $_REQUEST['uname'];

		}

		if(!empty($_REQUEST['bj']) && !empty($_REQUEST['money'])){

			$map['b.borrow_money'] = array($_REQUEST['bj'],$_REQUEST['money']);

			$search['bj'] = $_REQUEST['bj'];	

			$search['money'] = $_REQUEST['money'];	

		}

		if(!empty($_REQUEST['start_time']) && !empty($_REQUEST['end_time'])){

			$timespan = strtotime(urldecode($_REQUEST['start_time'])).",".strtotime(urldecode($_REQUEST['end_time']));

			$map['b.add_time'] = array("between",$timespan);

			$search['start_time'] = urldecode($_REQUEST['start_time']);	

			$search['end_time'] = urldecode($_REQUEST['end_time']);	

		}elseif(!empty($_REQUEST['start_time'])){

			$xtime = strtotime(urldecode($_REQUEST['start_time']));

			$map['b.add_time'] = array("gt",$xtime);

			$search['start_time'] = $xtime;	

		}elseif(!empty($_REQUEST['end_time'])){

			$xtime = strtotime(urldecode($_REQUEST['end_time']));

			$map['b.add_time'] = array("lt",$xtime);

			$search['end_time'] = $xtime;	

		}

		

		//分页处理

		import("ORG.Util.Page");

		$count = M('borrow_info b')->join("{$this->pre}members m ON m.id=b.borrow_uid")->where($map)->count('b.id');

		$p = new Page($count, C('ADMIN_PAGE_SIZE'));

		$page = $p->show();

		$Lsql = "{$p->firstRow},{$p->listRows}";

		//分页处理

		

		$field= 'b.id as id,b.borrow_name,b.borrow_uid,b.borrow_duration,b.borrow_type,b.updata,b.borrow_money,b.borrow_fee,b.borrow_interest_rate,b.repayment_type,b.borrow_status,b.add_time,m.user_name,b.lead_time,m.id mid,b.is_tuijian,b.pid';

		$list = M('borrow_info b')->field($field)->join("{$this->pre}members m ON m.id=b.borrow_uid")->where($map)->limit($Lsql)->order("b.id DESC")->select();

		$list = $this->_listFilter($list);

        $this->assign("bj", array("gt"=>'大于',"eq"=>'等于',"lt"=>'小于'));

        $this->assign("list", $list);

        $this->assign("pagebar", $page);

        $this->assign("search", $search);

		$this->assign("xaction",ACTION_NAME);

        $this->assign("query", http_build_query($search));

		

        $this->display();

    }

	

	public function waitstart()

    {

		$map=array();

		$map['b.id']=["neq",1];

		$map['b.borrow_status'] = 1;

		if(!empty($_REQUEST['uname'])&&!$_REQUEST['uid'] || $_REQUEST['uname']!=$_REQUEST['olduname']){

			$uid = M("members")->getFieldByUserName(text($_REQUEST['uname']),'id');

			$map['b.borrow_uid'] = $uid;

			$search['uid'] = $map['b.borrow_uid'];

			$search['uname'] = $_REQUEST['uname'];

		}

		if( !empty($_REQUEST['uid'])&&!isset($search['uname']) ){

			$map['b.borrow_uid'] = intval($_REQUEST['uid']);

			$search['uid'] = $map['b.borrow_uid'];

			$search['uname'] = $_REQUEST['uname'];

		}

		if(!empty($_REQUEST['bj']) && !empty($_REQUEST['money'])){

			$map['b.borrow_money'] = array($_REQUEST['bj'],$_REQUEST['money']);

			$search['bj'] = $_REQUEST['bj'];	

			$search['money'] = $_REQUEST['money'];	

		}

		if(!empty($_REQUEST['start_time']) && !empty($_REQUEST['end_time'])){

			$timespan = strtotime(urldecode($_REQUEST['start_time'])).",".strtotime(urldecode($_REQUEST['end_time']));

			$map['b.add_time'] = array("between",$timespan);

			$search['start_time'] = urldecode($_REQUEST['start_time']);	

			$search['end_time'] = urldecode($_REQUEST['end_time']);	

		}elseif(!empty($_REQUEST['start_time'])){

			$xtime = strtotime(urldecode($_REQUEST['start_time']));

			$map['b.add_time'] = array("gt",$xtime);

			$search['start_time'] = $xtime;	

		}elseif(!empty($_REQUEST['end_time'])){

			$xtime = strtotime(urldecode($_REQUEST['end_time']));

			$map['b.add_time'] = array("lt",$xtime);

			$search['end_time'] = $xtime;	

		}

		

		//分页处理

		import("ORG.Util.Page");

		$count = M('borrow_info b')->join("{$this->pre}members m ON m.id=b.borrow_uid")->where($map)->count('b.id');

		$p = new Page($count, C('ADMIN_PAGE_SIZE'));

		$page = $p->show();

		$Lsql = "{$p->firstRow},{$p->listRows}";

		//分页处理

		

		$field= 'b.id as id,b.borrow_name,b.borrow_uid,b.borrow_duration,b.borrow_type,b.updata,b.borrow_money,b.borrow_fee,b.borrow_interest_rate,b.repayment_type,b.add_time,m.user_name,m.id mid,b.is_tuijian,b.pid';

		$list = M('borrow_info b')->field($field)->join("{$this->pre}members m ON m.id=b.borrow_uid")->where($map)->limit($Lsql)->order("b.id DESC")->select();

		$list = $this->_listFilter($list);

        $this->assign("bj", array("gt"=>'大于',"eq"=>'等于',"lt"=>'小于'));

        $this->assign("list", $list);

        $this->assign("pagebar", $page);

        $this->assign("search", $search);

		$this->assign("xaction",ACTION_NAME);

        $this->assign("query", http_build_query($search));

		

        $this->display();

    }

    public function waitverify2()

    {

		$map=array();

		$map['b.id']=["neq",1];

		$map['b.borrow_status'] = 4;

		if(!empty($_REQUEST['uname'])&&!$_REQUEST['uid'] || $_REQUEST['uname']!=$_REQUEST['olduname']){

			$uid = M("members")->getFieldByUserName(text($_REQUEST['uname']),'id');

			$map['b.borrow_uid'] = $uid;

			$search['uid'] = $map['b.borrow_uid'];

			$search['uname'] = $_REQUEST['uname'];

		}

		if( !empty($_REQUEST['uid'])&&!isset($search['uname']) ){

			$map['b.borrow_uid'] = intval($_REQUEST['uid']);

			$search['uid'] = $map['b.borrow_uid'];

			$search['uname'] = $_REQUEST['uname'];

		}

		if(!empty($_REQUEST['bj']) && !empty($_REQUEST['money'])){

			$map['b.borrow_money'] = array($_REQUEST['bj'],$_REQUEST['money']);

			$search['bj'] = $_REQUEST['bj'];	

			$search['money'] = $_REQUEST['money'];	

		}

		if(!empty($_REQUEST['start_time']) && !empty($_REQUEST['end_time'])){

			$timespan = strtotime(urldecode($_REQUEST['start_time'])).",".strtotime(urldecode($_REQUEST['end_time']));

			$map['b.add_time'] = array("between",$timespan);

			$search['start_time'] = urldecode($_REQUEST['start_time']);	

			$search['end_time'] = urldecode($_REQUEST['end_time']);	

		}elseif(!empty($_REQUEST['start_time'])){

			$xtime = strtotime(urldecode($_REQUEST['start_time']));

			$map['b.add_time'] = array("gt",$xtime);

			$search['start_time'] = $xtime;	

		}elseif(!empty($_REQUEST['end_time'])){

			$xtime = strtotime(urldecode($_REQUEST['end_time']));

			$map['b.add_time'] = array("lt",$xtime);

			$search['end_time'] = $xtime;	

		}

		

		if(session('admin_is_kf')==1){

				$map['m.customer_id'] = session('admin_id');

		}else{

			if($_REQUEST['customer_id'] && $_REQUEST['customer_name']){

				$map['m.customer_id'] = $_REQUEST['customer_id'];

				$search['customer_id'] = $map['m.customer_id'];	

				$search['customer_name'] = urldecode($_REQUEST['customer_name']);	

			}

			

			if($_REQUEST['customer_name'] && !$search['customer_id']){

				$cusname = urldecode($_REQUEST['customer_name']);

				$kfid = M('ausers')->getFieldByUserName($cusname,'id');

				$map['m.customer_id'] = $kfid;

				$search['customer_name'] = $cusname;	

				$search['customer_id'] = $kfid;	

			}

		}

		//分页处理

		import("ORG.Util.Page");

		$count = M('borrow_info b')->join("{$this->pre}members m ON m.id=b.borrow_uid")->where($map)->count('b.id');

		$p = new Page($count, C('ADMIN_PAGE_SIZE'));

		$page = $p->show();

		$Lsql = "{$p->firstRow},{$p->listRows}";

		//分页处理

		$field= 'b.id as id,b.borrow_name,b.borrow_uid,b.borrow_duration,b.borrow_type,b.borrow_money,b.updata,b.borrow_fee,b.borrow_interest_rate,b.repayment_type,b.full_time,m.user_name,m.id mid,b.is_tuijian,b.pid';

		$list = M('borrow_info b')->field($field)->join("{$this->pre}members m ON m.id=b.borrow_uid")->where($map)->limit($Lsql)->order("b.id DESC")->select();

		$list = $this->_listFilter($list);

		

        $this->assign("bj", array("gt"=>'大于',"eq"=>'等于',"lt"=>'小于'));

        $this->assign("list", $list);

        $this->assign("pagebar", $page);

        $this->assign("search", $search);

		$this->assign("xaction",ACTION_NAME);

        $this->assign("query", http_build_query($search));

		

        $this->display();

    }

	

    public function waitmoney()

    {

		$map=array();

		$map['b.id']=["neq",1];

		$map['b.borrow_status'] = 2;

		if(!empty($_REQUEST['uname'])&&!$_REQUEST['uid'] || $_REQUEST['uname']!=$_REQUEST['olduname']){

			$uid = M("members")->getFieldByUserName(text($_REQUEST['uname']),'id');

			$map['b.borrow_uid'] = $uid;

			$search['uid'] = $map['b.borrow_uid'];

			$search['uname'] = $_REQUEST['uname'];

		}

		if( !empty($_REQUEST['uid'])&&!isset($search['uname']) ){

			$map['b.borrow_uid'] = intval($_REQUEST['uid']);

			$search['uid'] = $map['b.borrow_uid'];

			$search['uname'] = $_REQUEST['uname'];

		}

		if(!empty($_REQUEST['bj']) && !empty($_REQUEST['money'])){

			$map['b.borrow_money'] = array($_REQUEST['bj'],$_REQUEST['money']);

			$search['bj'] = $_REQUEST['bj'];	

			$search['money'] = $_REQUEST['money'];	

		}

		if(!empty($_REQUEST['start_time']) && !empty($_REQUEST['end_time'])){

			$timespan = strtotime(urldecode($_REQUEST['start_time'])).",".strtotime(urldecode($_REQUEST['end_time']));

			$map['b.add_time'] = array("between",$timespan);

			$search['start_time'] = urldecode($_REQUEST['start_time']);	

			$search['end_time'] = urldecode($_REQUEST['end_time']);	

		}elseif(!empty($_REQUEST['start_time'])){

			$xtime = strtotime(urldecode($_REQUEST['start_time']));

			$map['b.add_time'] = array("gt",$xtime);

			$search['start_time'] = $xtime;	

		}elseif(!empty($_REQUEST['end_time'])){

			$xtime = strtotime(urldecode($_REQUEST['end_time']));

			$map['b.add_time'] = array("lt",$xtime);

			$search['end_time'] = $xtime;	

		}

		

		if(session('admin_is_kf')==1){

				$map['m.customer_id'] = session('admin_id');

		}else{

			if($_REQUEST['customer_id'] && $_REQUEST['customer_name']){

				$map['m.customer_id'] = $_REQUEST['customer_id'];

				$search['customer_id'] = $map['m.customer_id'];	

				$search['customer_name'] = urldecode($_REQUEST['customer_name']);	

			}

			

			if($_REQUEST['customer_name'] && !$search['customer_id']){

				$cusname = urldecode($_REQUEST['customer_name']);

				$kfid = M('ausers')->getFieldByUserName($cusname,'id');

				$map['m.customer_id'] = $kfid;

				$search['customer_name'] = $cusname;	

				$search['customer_id'] = $kfid;	

			}

		}

		//分页处理

		import("ORG.Util.Page");

		$count = M('borrow_info b')->join("{$this->pre}members m ON m.id=b.borrow_uid")->where($map)->count('b.id');

		$p = new Page($count, C('ADMIN_PAGE_SIZE'));

		$page = $p->show();

		$Lsql = "{$p->firstRow},{$p->listRows}";

		//分页处理

		$field= 'b.id as id,b.borrow_name,b.borrow_uid,b.borrow_duration,b.has_borrow,b.borrow_type,b.borrow_money,b.updata,b.borrow_fee,b.borrow_interest_rate,b.repayment_type,b.add_time,m.user_name,m.id mid,b.is_tuijian,b.pid';

		$list = M('borrow_info b')->field($field)->join("{$this->pre}members m ON m.id=b.borrow_uid")->where($map)->limit($Lsql)->order("b.id DESC")->select();

		$list = $this->_listFilter($list);

		

        $this->assign("bj", array("gt"=>'大于',"eq"=>'等于',"lt"=>'小于'));

        $this->assign("list", $list);

        $this->assign("pagebar", $page);

        $this->assign("search", $search);

		$this->assign("xaction",ACTION_NAME);

        $this->assign("query", http_build_query($search));

		

        $this->display();

    }

	

    public function repaymenting()

    {

		

		

		$map=array();

		$map['b.id'] = ['neq',1];

		$map['b.borrow_status'] = 6;//还款中

		if(!empty($_REQUEST['uname'])&&!$_REQUEST['uid'] || $_REQUEST['uname']!=$_REQUEST['olduname']){

			$uid = M("members")->getFieldByUserName(text($_REQUEST['uname']),'id');

			$map['b.borrow_uid'] = $uid;

			$search['uid'] = $map['b.borrow_uid'];

			$search['uname'] = $_REQUEST['uname'];

		}

		if( !empty($_REQUEST['uid'])&&!isset($search['uname']) ){

			$map['b.borrow_uid'] = intval($_REQUEST['uid']);

			$search['uid'] = $map['b.borrow_uid'];

			$search['uname'] = $_REQUEST['uname'];

		}

		if(!empty($_REQUEST['bj']) && !empty($_REQUEST['money'])){

			$map['b.borrow_money'] = array($_REQUEST['bj'],$_REQUEST['money']);

			$search['bj'] = $_REQUEST['bj'];	

			$search['money'] = $_REQUEST['money'];	

		}

		if(!empty($_REQUEST['start_time']) && !empty($_REQUEST['end_time'])){

			$timespan = strtotime(urldecode($_REQUEST['start_time'])).",".strtotime(urldecode($_REQUEST['end_time']));

			$map['b.add_time'] = array("between",$timespan);

			$search['start_time'] = urldecode($_REQUEST['start_time']);	
			$search['end_time'] = urldecode($_REQUEST['end_time']);	

		}elseif(!empty($_REQUEST['start_time'])){

			$xtime = strtotime(urldecode($_REQUEST['start_time']));

			$map['b.add_time'] = array("gt",$xtime);

			$search['start_time'] = $xtime;	

		}elseif(!empty($_REQUEST['end_time'])){

			$xtime = strtotime(urldecode($_REQUEST['end_time']));

			$map['b.add_time'] = array("lt",$xtime);

			$search['end_time'] = $xtime;	

		}

		

		if(session('admin_is_kf')==1){

				$map['m.customer_id'] = session('admin_id');

		}else{

			if($_REQUEST['customer_id'] && $_REQUEST['customer_name']){

				$map['m.customer_id'] = $_REQUEST['customer_id'];

				$search['customer_id'] = $map['m.customer_id'];	

				$search['customer_name'] = urldecode($_REQUEST['customer_name']);	

			}

			

			if($_REQUEST['customer_name'] && !$search['customer_id']){

				$cusname = urldecode($_REQUEST['customer_name']);

				$kfid = M('ausers')->getFieldByUserName($cusname,'id');

				$map['m.customer_id'] = $kfid;

				$search['customer_name'] = $cusname;	

				$search['customer_id'] = $kfid;	

			}

		}

		//分页处理

		import("ORG.Util.Page");

		$count = M('borrow_info b')->join("{$this->pre}members m ON m.id=b.borrow_uid")->where($map)->count('b.id');

		$p = new Page($count, C('ADMIN_PAGE_SIZE'));

		$page = $p->show();

		$Lsql = "{$p->firstRow},{$p->listRows}";

		//分页处理

		$field= 'b.is_huodong,b.id as id,b.borrow_name,b.borrow_uid,b.borrow_duration,b.borrow_type,b.borrow_money,b.borrow_interest,b.updata,b.borrow_fee,b.borrow_interest_rate,b.repayment_type,b.repayment_money,b.total,b.has_pay,b.deadline,m.user_name,m.id mid,b.is_tuijian,b.pid';

		$list = M('borrow_info b')->field($field)->join("{$this->pre}members m ON m.id=b.borrow_uid")->where($map)->limit($Lsql)->order("b.id DESC")->select();

		$list = $this->_listFilter($list);

		

        $this->assign("bj", array("gt"=>'大于',"eq"=>'等于',"lt"=>'小于'));

        $this->assign("list", $list);

        $this->assign("pagebar", $page);

        $this->assign("search", $search);

		$this->assign("xaction",ACTION_NAME);

        $this->assign("query", http_build_query($search));

		

        $this->display();

    }

    public function dorepayment()

    {

        //获取投资列表

        $pre         = C('DB_PREFIX');

        $id          = empty($_REQUEST['id']) ? '' : $_REQUEST['id'];

        $has_capital = '1';

        $has_done    = '1';

        $borrowinfo  = M("borrow_info")->field(true)->find($id); 

        if($borrowinfo['borrow_status'] == 7){

        	 $this->error("已还款");

        } 

        if (!empty($_POST['borrow_interest_rate']) && !empty($id)) {

            $borrow_id = $id;

            $permoney  = $_POST['borrow_interest_rate'] / 1000;

            $permoney_rate  = $_POST['borrow_interest_rate'] / 1000;

            if ($has_capital == 1) {

                $permoney += 1;

            }



            $borrow_name    = $borrowinfo["borrow_name"];

            $b_member       = m("members")->field("user_name")->find($borrowinfo['borrow_uid']);

            $borrowInvestor = D("borrow_investor");

            $borrowInvestor->startTrans();

            $investorList = $borrowInvestor->field("id,investor_uid,investor_capital,investor_interest,is_experience,member_interest_rate_id")->where("borrow_id={$borrow_id}")->select();

            $done         = true;

            $allPaymoney  = 0;

            $affect_inter_money  = 0;

            $smsUid       = '';

            $sort_order   = 1;

            $phoneArr = array();

            foreach ($investorList as $key => $value) {

                if (!$done) {

                    break;

                }

                

                $affect_money = $value['investor_capital'] * $permoney;



                $allPaymoney += $affect_money;

                $accountMoney               = M("member_money")->field("money_freeze,money_collect,account_money")->find($value['investor_uid']);

                $datamoney['uid']           = $value['investor_uid'];

                $datamoney['type']          = "9";

                $datamoney['add_time'] = time();

                $datamoney['add_ip']   = get_client_ip();



                if ($borrowinfo['borrow_uid'] == 0) {

                    $datamoney['target_uid']   = 0;

                    $datamoney['target_uname'] = "@网站管理员@";

                } else {

                    $datamoney['target_uid']   = $borrowinfo['borrow_uid'];

                    $datamoney['target_uname'] = $b_member['user_name'];

                }

                //加息券

                if($value['member_interest_rate_id'] != 0){

                	// $canUseInterest = canUseInterest($datamoney['uid'],$value['member_interest_rate_id']);

	                // if ($canUseInterest['status'] == 1) {

                	$rid = $value['member_interest_rate_id'];

                	$uid = $datamoney['uid'];

                	$canUseInterest = M('member_interest_rate')->where(" id = '{$rid}' and uid='{$uid}'")->find();

		            $affect_inter_money= round($value['investor_capital']*$canUseInterest['interest_rate']/100/360*$borrowinfo['borrow_duration'],2);

		            $is_experience_rs = memberMoneyLog($value['investor_uid'], 120, $affect_inter_money, "会员对{$borrow_name}[{$borrow_id}]回报" . "， 回收加息金" . $affect_inter_money . '元（加息券）。',$datamoney['target_uid'], $datamoney['target_uname'], false);

		            $datamoneys['account_money'] = $accountMoney['account_money'] + $affect_inter_money;

		            m("member_money")->where("uid={$uid}")->save($datamoneys);

		            if (!$is_experience_rs) {

                        $borrowInvestor->rollback();

                        $done = false;

                    }

			        // }

                }

                //体验金回收

                if($has_capital && $value['is_experience'] == 1){

                	$affect_money = bcsub( $affect_money, $value['investor_capital'] ,2);                	

                	

                	// $is_experience_rs = memberMoneyLog($value['investor_uid'], 104, $value['investor_capital'], "会员对{$borrow_name}[{$borrow_id}]回报" . "， 回收本金" . $value['investor_capital'] . '元（体验金）。', $datamoney['target_uid'], $datamoney['target_uname'], false);



                	// if (!$is_experience_rs) {

                 //        $borrowInvestor->rollback();

                 //        $done = false;

                 //    }

                    

                	$datamoney['info'] = "会员对{$borrow_name}[{$borrow_id}]回报" . "， 其中体验金" . $value['investor_capital'] . '元（体验金），收益' . $affect_money . '元。';

                }else{

                	$datamoney['info'] = "会员对{$borrow_name}[{$borrow_id}]回报" . "， 其中本金" . $value['investor_capital'] . '元，收益' . bcsub($affect_money, $value['investor_capital'], 2) . '元。';

                }

                $accountMoney               = M("member_money")->field("money_freeze,money_collect,account_money")->find($value['investor_uid']);

                $datamoney['affect_money']  = $affect_money;

                $datamoney['account_money'] = $accountMoney['account_money'] + $datamoney['affect_money'];

                $datamoney['collect_money'] = $accountMoney['money_collect'] - $value['investor_capital'];

                $datamoney['freeze_money']  = $accountMoney['money_freeze'];

                $mmoney['money_freeze']     = $datamoney['freeze_money'];

                $mmoney['money_collect']    = $datamoney['collect_money'];

                $mmoney['account_money']    = $datamoney['account_money'];

                $mmoney['update_time']    = time();

                

                $moneynewid = m("member_moneylog")->add($datamoney);

                inAccountMoney($datamoney['uid'],$datamoney['affect_money'],4);

                if ($moneynewid) {

                    $xid          = m("member_money")->where("uid={$datamoney['uid']}")->save($mmoney);

                    writelog(m("member_money")->getlastsql());

                    writelog($xid);

                    $investorSave = array(

                        'id'                => $value['id'],

                        'investor_interest' => getFloatValue($value['investor_capital'] * $permoney_rate,2),

                        'receive_interest'  => getFloatValue($value['investor_capital'] * $permoney_rate,2),

                        'repayment_time'    => time()

                    );                    

                    if($has_done){

                    	$investorSave['status'] = '5';

                    }

                    $borrowInvestor->save($investorSave);

                    if (!$xid) {

                        $borrowInvestor->rollback();

                        $done = false;

                    }

                }

                $tophone = M('members')->getFieldById($value['investor_uid'],'user_phone');  

                $phoneArr[] = array(                	                	

                	'user_phone'  => $tophone,

                	'investor_capital' => $value['investor_capital'],

                	'receive_interest' => $has_capital && $value['is_experience'] == 1 ? $affect_money : bcsub($affect_money, $value['investor_capital'], 2),

                	'investor_uid' => $value['investor_uid'],

                	);       



                addInnerMsg($value['investor_uid'],'您收到一笔回款，请注意查看资金明细',$datamoney['info']);                

            }

            if ($done) {

                $accountMoney_borrower = m("member_money")->field("money_freeze,money_collect,account_money")->find($borrowinfo['borrow_uid']);

                if ($accountMoney_borrower['account_money'] < $allPaymoney) {

                    $this->error("帐户可用余额不足，本期共需" . $allPaymoney . "元，请先充值");die;

                }

                $datamoney_x['uid']           = $borrowinfo['borrow_uid'];

                $datamoney_x['type']          = 11;

                $datamoney_x['affect_money']  = 0 - $allPaymoney-$affect_inter_money;

                $datamoney_x['account_money'] = $accountMoney_borrower['account_money'] + $datamoney_x['affect_money'];

                $datamoney_x['collect_money'] = $accountMoney_borrower['money_collect'];

                $datamoney_x['freeze_money']  = $accountMoney_borrower['money_freeze'];

                $mmoney_x['money_freeze']     = $datamoney_x['freeze_money'];

                $mmoney_x['money_collect']    = $datamoney_x['collect_money'];

                $mmoney_x['account_money']    = $datamoney_x['account_money'];

                $datamoney_x['info']          = "对{$borrow_name}[{$borrow_id}]回报";

                $datamoney_x['add_time']      = time();

                $datamoney_x['add_ip']        = get_client_ip();

                $datamoney_x['target_uid']    = 0;

                $datamoney_x['target_uname']  = "@网站管理员@";

                $moneynewid_x                 = m("member_moneylog")->add($datamoney_x);

                if ($moneynewid_x) {

                    $bxid = m("member_money")->where("uid={$datamoney_x['uid']}")->save($mmoney_x);

                    useAccountMoney($datamoney_x['uid'],$datamoney_x['affect_money']);

                }

                if (@$bxid) {

                	// 年化收益率=收益/天数*365/本金*100%

                	$repayment_time = time();

                	$sell_days = floor(($repayment_time - $borrowinfo['second_verify_time']) / 86400);

                	$sell_days = $sell_days < 1 ? 1 : $sell_days;

                	$borrow_interest_rate = getFloatValue($borrowinfo['borrow_money'] * $permoney_rate / $sell_days * 365 / $borrowinfo['borrow_money'] * 100,2);

                    $upborrowsql = "update `{$pre}borrow_info` set ";

                    $upborrowsql .= "`repayment_money`=`repayment_money`+{$allPaymoney}";

                    $upborrowsql .= ",`deadline`=".time();

                    // $upborrowsql .= ",`borrow_interest_rate` = {$borrow_interest_rate}";

                    if ($has_done == 1) {

                        $upborrowsql .= ",`borrow_status`=7";

                    }

                    $upborrowsql .= ",`has_pay` = `has_pay` + 1 ";

                    $upborrowsql .= " WHERE `id`={$borrow_id}";

                    $upborrow_res = M()->execute($upborrowsql);                    

                    if ($upborrow_res) {

                        $borrowInvestor->commit();

                        // $vphone = m("members")->field("user_phone")->where("id in({$smsUid})")->select();                        

                        foreach ($phoneArr as $k => $v) {

                            if (empty($v['user_phone'])) {

                                continue;

                            }

	                        smstip("payback", $v['user_phone'], array(

	                            "#BORROW_NAME#",

	                            "#INVESTOR_CAPITAL#",

	                            "#RECEIVE_INTEREST#",

	                            "#BORROW_INTEREST_RATE#",

	                        ), array(

	                            $borrowinfo['borrow_name'],

	                            $v['investor_capital'],

	                            $v['receive_interest'],

	                            $borrow_interest_rate,

	                        ));

	                        $phoneArr[$k]['borrow_name'] = $borrow_name;

	                        $phoneArr[$k]['sell_days'] = $sell_days;

	                        $phoneArr[$k]['borrow_interest_rate'] = $borrow_interest_rate;	                        

                        }

                        recoverExperienceMoney(); //回收体验金

                        /*$data = array(

				                    array(

				                        'investor_capital' => '100.25',

				                        'receive_interest' => '8.86',

				                        'investor_uid' => $uid,

				                        'borrow_name' => '测试项目回款',

				                        'sell_days' => '90',

				                        'borrow_interest_rate' => '16.3',

				                    ),

				                );*/

                        $this->success('操作成功！');

                        die;

                    }

                }

            }

            $this->error('操作失败！');

            $borrowInvestor->rollback();

            die;

        }

        //echo M("borrow_info")->LastSql();

        //print_r($borrowinfo);

        if (!is_array($borrowinfo) || ($borrowinfo['borrow_status'] == 0 && $this->uid != $borrowinfo['borrow_uid'])) {

            $this->error("数据有误");

        }



        $borrowinfo['biao']           = $borrowinfo['borrow_times'];

        $borrowinfo['need']           = $borrowinfo['borrow_money'] - $borrowinfo['has_borrow'];

        $borrowinfo['lefttime']       = $borrowinfo['collect_time'] - time();

        $borrowinfo['progress']       = getFloatValue($borrowinfo['has_borrow'] / $borrowinfo['borrow_money'] * 100, 2);

        $borrowinfo['vouch_progress'] = getFloatValue($borrowinfo['has_vouch'] / $borrowinfo['borrow_money'] * 100, 2);

        $borrowinfo['vouch_need']     = $borrowinfo['borrow_money'] - $borrowinfo['has_vouch'];

        $borrowinfo["borrow_img"]     = str_replace("'", "", $borrowinfo["borrow_img"]);

        if ($borrowinfo['borrow_img'] == "") {

            $borrowinfo['borrow_img'] = "/UF/Uploads/borrowimg/nopic.png";

        }



        $this->assign("binfo", $borrowinfo);

        $pre        = C('DB_PREFIX');

        $fieldx     = "bi.investor_capital,bi.investor_interest,bi.investor_capital+bi.investor_interest as yingshou,bi.receive_capital,bi.receive_interest,bi.add_time,m.user_name,m.id as userid,bi.is_auto,mi.real_name";

        $investinfo = M("borrow_investor bi")->field($fieldx)->join("{$pre}members m ON bi.investor_uid = m.id")->join("{$pre}member_info mi on mi.uid = m.id")->where("bi.borrow_id={$id}")->order("bi.id DESC")->select();

        $this->assign("investinfo", $investinfo);

        $this->display();

    }



		

    public function borrowbreak()

    {//暂时未处理

		$map['deadline'] = array("exp","<>0 AND deadline<".time()." AND `repayment_money`<`borrow_money`");

		$field= 'id,borrow_name,borrow_uid,borrow_duration,borrow_type,borrow_money,borrow_fee,repayment_money,b.updata,borrow_interest_rate,repayment_type,deadline';

		$this->_list(D('Borrow'),$field,$map,'id','DESC');

        $this->display();

    }

	

	public function unfinish(){

		$map=array();

		$map['b.id'] = ['neq',1];

		$map['b.borrow_status'] = array("in","3,5");

		if(!empty($_REQUEST['uname'])&&!$_REQUEST['uid'] || $_REQUEST['uname']!=$_REQUEST['olduname']){

			$uid = M("members")->getFieldByUserName(text($_REQUEST['uname']),'id');

			$map['b.borrow_uid'] = $uid;

			$search['uid'] = $map['b.borrow_uid'];

			$search['uname'] = $_REQUEST['uname'];

		}

		if( !empty($_REQUEST['uid'])&&!isset($search['uname']) ){

			$map['b.borrow_uid'] = intval($_REQUEST['uid']);

			$search['uid'] = $map['b.borrow_uid'];

			$search['uname'] = $_REQUEST['uname'];

		}

		if(!empty($_REQUEST['bj']) && !empty($_REQUEST['money'])){

			$map['b.borrow_money'] = array($_REQUEST['bj'],$_REQUEST['money']);

			$search['bj'] = $_REQUEST['bj'];	

			$search['money'] = $_REQUEST['money'];	

		}

		if(!empty($_REQUEST['start_time']) && !empty($_REQUEST['end_time'])){

			$timespan = strtotime(urldecode($_REQUEST['start_time'])).",".strtotime(urldecode($_REQUEST['end_time']));

			$map['b.add_time'] = array("between",$timespan);

			$search['start_time'] = urldecode($_REQUEST['start_time']);	

			$search['end_time'] = urldecode($_REQUEST['end_time']);	

		}elseif(!empty($_REQUEST['start_time'])){

			$xtime = strtotime(urldecode($_REQUEST['start_time']));

			$map['b.add_time'] = array("gt",$xtime);

			$search['start_time'] = $xtime;	

		}elseif(!empty($_REQUEST['end_time'])){

			$xtime = strtotime(urldecode($_REQUEST['end_time']));

			$map['b.add_time'] = array("lt",$xtime);

			$search['end_time'] = $xtime;	

		}

		

		if(session('admin_is_kf')==1){

				$map['m.customer_id'] = session('admin_id');

		}else{

			if($_REQUEST['customer_id'] && $_REQUEST['customer_name']){

				$map['m.customer_id'] = $_REQUEST['customer_id'];

				$search['customer_id'] = $map['m.customer_id'];	

				$search['customer_name'] = urldecode($_REQUEST['customer_name']);	

			}

			

			if($_REQUEST['customer_name'] && !$search['customer_id']){

				$cusname = urldecode($_REQUEST['customer_name']);

				$kfid = M('ausers')->getFieldByUserName($cusname,'id');

				$map['m.customer_id'] = $kfid;

				$search['customer_name'] = $cusname;	

				$search['customer_id'] = $kfid;	

			}

		}

		//分页处理

		import("ORG.Util.Page");

		$count = M('borrow_info b')->join("{$this->pre}members m ON m.id=b.borrow_uid")->where($map)->count('b.id');

		$p = new Page($count, C('ADMIN_PAGE_SIZE'));

		$page = $p->show();

		$Lsql = "{$p->firstRow},{$p->listRows}";

		//分页处理

		$field= 'b.id as id,b.borrow_name,b.borrow_status,b.borrow_uid,b.borrow_duration,b.borrow_type,b.borrow_money,b.updata,b.borrow_fee,b.borrow_interest_rate,b.repayment_type,b.deadline,m.id mid,m.user_name,v.deal_user_2,v.deal_time_2,v.deal_info_2,b.pid,v.deal_time';

		$list = M('borrow_info b')->field($field)->join("{$this->pre}members m ON m.id=b.borrow_uid")->join("{$this->pre}borrow_verify v ON b.id=v.borrow_id")->where($map)->limit($Lsql)->order("b.id DESC")->select();

		$list = $this->_listFilter($list);

		

        $this->assign("bj", array("gt"=>'大于',"eq"=>'等于',"lt"=>'小于'));

        $this->assign("list", $list);

        $this->assign("pagebar", $page);

        $this->assign("search", $search);

		$this->assign("xaction",ACTION_NAME);

        $this->assign("query", http_build_query($search));

		

        $this->display();

	}

	

	

    public function done()

    {

		$map=array();

		$map['b.id'] = ['neq',1];

		$map['b.borrow_status'] = array("in","7,9");

		if(!empty($_REQUEST['uname'])&&!$_REQUEST['uid'] || $_REQUEST['uname']!=$_REQUEST['olduname']){

			$uid = M("members")->getFieldByUserName(text($_REQUEST['uname']),'id');

			$map['b.borrow_uid'] = $uid;

			$search['uid'] = $map['b.borrow_uid'];

			$search['uname'] = $_REQUEST['uname'];

		}

		if( !empty($_REQUEST['uid'])&&!isset($search['uname']) ){

			$map['b.borrow_uid'] = intval($_REQUEST['uid']);

			$search['uid'] = $map['b.borrow_uid'];

			$search['uname'] = $_REQUEST['uname'];

		}

		if(!empty($_REQUEST['bj']) && !empty($_REQUEST['money'])){

			$map['b.borrow_money'] = array($_REQUEST['bj'],$_REQUEST['money']);

			$search['bj'] = $_REQUEST['bj'];	

			$search['money'] = $_REQUEST['money'];	

		}

		if(!empty($_REQUEST['start_time']) && !empty($_REQUEST['end_time'])){

			$timespan = strtotime(urldecode($_REQUEST['start_time'])).",".strtotime(urldecode($_REQUEST['end_time']));

			$map['b.add_time'] = array("between",$timespan);

			$search['start_time'] = urldecode($_REQUEST['start_time']);	

			$search['end_time'] = urldecode($_REQUEST['end_time']);	

		}elseif(!empty($_REQUEST['start_time'])){

			$xtime = strtotime(urldecode($_REQUEST['start_time']));

			$map['b.add_time'] = array("gt",$xtime);

			$search['start_time'] = $xtime;	

		}elseif(!empty($_REQUEST['end_time'])){

			$xtime = strtotime(urldecode($_REQUEST['end_time']));

			$map['b.add_time'] = array("lt",$xtime);

			$search['end_time'] = $xtime;	

		}

		

		if(session('admin_is_kf')==1){

				$map['m.customer_id'] = session('admin_id');

		}else{

			if($_REQUEST['customer_id'] && $_REQUEST['customer_name']){

				$map['m.customer_id'] = $_REQUEST['customer_id'];

				$search['customer_id'] = $map['m.customer_id'];	

				$search['customer_name'] = urldecode($_REQUEST['customer_name']);	

			}

			

			if($_REQUEST['customer_name'] && !$search['customer_id']){

				$cusname = urldecode($_REQUEST['customer_name']);

				$kfid = M('ausers')->getFieldByUserName($cusname,'id');

				$map['m.customer_id'] = $kfid;

				$search['customer_name'] = $cusname;	

				$search['customer_id'] = $kfid;	

			}

		}

		//分页处理

		import("ORG.Util.Page");

		$count = M('borrow_info b')->join("{$this->pre}members m ON m.id=b.borrow_uid")->where($map)->count('b.id');

		$p = new Page($count, C('ADMIN_PAGE_SIZE'));

		$page = $p->show();

		$Lsql = "{$p->firstRow},{$p->listRows}";

		//分页处理

		$field= 'b.id as id,b.borrow_name,b.borrow_uid,b.borrow_duration,b.borrow_type,b.borrow_money,b.updata,b.borrow_fee,b.borrow_interest_rate,b.repayment_type,b.repayment_money,b.deadline,m.id mid,m.user_name,b.pid,b.lead_time,b.deadline';

		$list = M('borrow_info b')->field($field)->join("{$this->pre}members m ON m.id=b.borrow_uid")->where($map)->limit($Lsql)->order("b.id DESC")->select();

// 		var_dump($list);die;

		$list = $this->_listFilter($list);

// 	var_dump($list[0]["repayment_time"]);die;

        $this->assign("bj", array("gt"=>'大于',"eq"=>'等于',"lt"=>'小于'));

        $this->assign("list", $list);

        $this->assign("pagebar", $page);

        $this->assign("search", $search);

		$this->assign("xaction",ACTION_NAME);

        $this->assign("query", http_build_query($search));

		

        $this->display();

    }

	

    public function fail()

    {

		$map=array();

		$map['b.id'] = ['neq',1];

		$map['b.borrow_status'] = 1;

		if(!empty($_REQUEST['uname'])&&!$_REQUEST['uid'] || $_REQUEST['uname']!=$_REQUEST['olduname']){

			$uid = M("members")->getFieldByUserName(text($_REQUEST['uname']),'id');

			$map['b.borrow_uid'] = $uid;

			$search['uid'] = $map['b.borrow_uid'];

			$search['uname'] = $_REQUEST['uname'];

		}

		if( !empty($_REQUEST['uid'])&&!isset($search['uname']) ){

			$map['b.borrow_uid'] = intval($_REQUEST['uid']);

			$search['uid'] = $map['b.borrow_uid'];

			$search['uname'] = $_REQUEST['uname'];

		}

		if(!empty($_REQUEST['bj']) && !empty($_REQUEST['money'])){

			$map['b.borrow_money'] = array($_REQUEST['bj'],$_REQUEST['money']);

			$search['bj'] = $_REQUEST['bj'];	

			$search['money'] = $_REQUEST['money'];	

		}

		if(!empty($_REQUEST['start_time']) && !empty($_REQUEST['end_time'])){

			$timespan = strtotime(urldecode($_REQUEST['start_time'])).",".strtotime(urldecode($_REQUEST['end_time']));

			$map['b.add_time'] = array("between",$timespan);

			$search['start_time'] = urldecode($_REQUEST['start_time']);	

			$search['end_time'] = urldecode($_REQUEST['end_time']);	

		}elseif(!empty($_REQUEST['start_time'])){

			$xtime = strtotime(urldecode($_REQUEST['start_time']));

			$map['b.add_time'] = array("gt",$xtime);

			$search['start_time'] = $xtime;	

		}elseif(!empty($_REQUEST['end_time'])){

			$xtime = strtotime(urldecode($_REQUEST['end_time']));

			$map['b.add_time'] = array("lt",$xtime);

			$search['end_time'] = $xtime;	

		}

		

		if(session('admin_is_kf')==1){

				$map['m.customer_id'] = session('admin_id');

		}else{

			if($_REQUEST['customer_id'] && $_REQUEST['customer_name']){

				$map['m.customer_id'] = $_REQUEST['customer_id'];

				$search['customer_id'] = $map['m.customer_id'];	

				$search['customer_name'] = urldecode($_REQUEST['customer_name']);	

			}

			

			if($_REQUEST['customer_name'] && !$search['customer_id']){

				$cusname = urldecode($_REQUEST['customer_name']);

				$kfid = M('ausers')->getFieldByUserName($cusname,'id');

				$map['m.customer_id'] = $kfid;

				$search['customer_name'] = $cusname;	

				$search['customer_id'] = $kfid;	

			}

		}

		//分页处理

		import("ORG.Util.Page");

		$count = M('borrow_info b')->join("{$this->pre}members m ON m.id=b.borrow_uid")->where($map)->count('b.id');

		$p = new Page($count, C('ADMIN_PAGE_SIZE'));

		$page = $p->show();

		$Lsql = "{$p->firstRow},{$p->listRows}";

		//分页处理

		$field= 'b.id as id,b.borrow_name,b.borrow_status,b.borrow_uid,b.borrow_duration,b.borrow_type,b.borrow_money,b.updata,b.borrow_fee,b.borrow_interest_rate,b.repayment_type,b.add_time,m.user_name,v.deal_user,v.deal_time,m.id mid,v.deal_info,b.pid';

		$list = M('borrow_info b')->field($field)->join("{$this->pre}members m ON m.id=b.borrow_uid")->join("{$this->pre}borrow_verify v ON b.id=v.borrow_id")->where($map)->limit($Lsql)->order("b.id DESC")->select();

		$list = $this->_listFilter($list);

		

        $this->assign("bj", array("gt"=>'大于',"eq"=>'等于',"lt"=>'小于'));

        $this->assign("list", $list);

        $this->assign("pagebar", $page);

        $this->assign("search", $search);

		$this->assign("xaction",ACTION_NAME);

        $this->assign("query", http_build_query($search));

		

        $this->display();

    }

	

    public function fail2()

    {

		$map=array();

		$map['b.borrow_status'] = 5;

		if(!empty($_REQUEST['uname'])&&!$_REQUEST['uid'] || $_REQUEST['uname']!=$_REQUEST['olduname']){

			$uid = M("members")->getFieldByUserName(text($_REQUEST['uname']),'id');

			$map['b.borrow_uid'] = $uid;

			$search['uid'] = $map['b.borrow_uid'];

			$search['uname'] = $_REQUEST['uname'];

		}

		if( !empty($_REQUEST['uid'])&&!isset($search['uname']) ){

			$map['b.borrow_uid'] = intval($_REQUEST['uid']);

			$search['uid'] = $map['b.borrow_uid'];

			$search['uname'] = $_REQUEST['uname'];

		}

		if(!empty($_REQUEST['bj']) && !empty($_REQUEST['money'])){

			$map['b.borrow_money'] = array($_REQUEST['bj'],$_REQUEST['money']);

			$search['bj'] = $_REQUEST['bj'];	

			$search['money'] = $_REQUEST['money'];	

		}

		if(!empty($_REQUEST['start_time']) && !empty($_REQUEST['end_time'])){

			$timespan = strtotime(urldecode($_REQUEST['start_time'])).",".strtotime(urldecode($_REQUEST['end_time']));

			$map['b.add_time'] = array("between",$timespan);

			$search['start_time'] = urldecode($_REQUEST['start_time']);	

			$search['end_time'] = urldecode($_REQUEST['end_time']);	

		}elseif(!empty($_REQUEST['start_time'])){

			$xtime = strtotime(urldecode($_REQUEST['start_time']));

			$map['b.add_time'] = array("gt",$xtime);

			$search['start_time'] = $xtime;	

		}elseif(!empty($_REQUEST['end_time'])){

			$xtime = strtotime(urldecode($_REQUEST['end_time']));

			$map['b.add_time'] = array("lt",$xtime);

			$search['end_time'] = $xtime;	

		}

		

		if(session('admin_is_kf')==1){

				$map['m.customer_id'] = session('admin_id');

		}else{

			if($_REQUEST['customer_id'] && $_REQUEST['customer_name']){

				$map['m.customer_id'] = $_REQUEST['customer_id'];

				$search['customer_id'] = $map['m.customer_id'];	

				$search['customer_name'] = urldecode($_REQUEST['customer_name']);	

			}

			

			if($_REQUEST['customer_name'] && !$search['customer_id']){

				$cusname = urldecode($_REQUEST['customer_name']);

				$kfid = M('ausers')->getFieldByUserName($cusname,'id');

				$map['m.customer_id'] = $kfid;

				$search['customer_name'] = $cusname;	

				$search['customer_id'] = $kfid;	

			}

		}

		//分页处理

		import("ORG.Util.Page");

		$count = M('borrow_info b')->join("{$this->pre}members m ON m.id=b.borrow_uid")->where($map)->count('b.id');

		$p = new Page($count, C('ADMIN_PAGE_SIZE'));

		$page = $p->show();

		$Lsql = "{$p->firstRow},{$p->listRows}";

		//分页处理

		$field= 'b.id as id,b.borrow_name,b.borrow_status,b.borrow_uid,b.borrow_duration,b.borrow_type,b.borrow_money,b.updata,b.borrow_fee,b.borrow_interest_rate,b.repayment_type,b.add_time,m.user_name,m.id mid,v.deal_user_2,v.deal_time_2,v.deal_info_2,b.pid';

		$list = M('borrow_info b')->field($field)->join("{$this->pre}members m ON m.id=b.borrow_uid")->join("{$this->pre}borrow_verify v ON b.id=v.borrow_id")->where($map)->limit($Lsql)->order("b.id DESC")->select();

		$list = $this->_listFilter($list);

		

        $this->assign("bj", array("gt"=>'大于',"eq"=>'等于',"lt"=>'小于'));

        $this->assign("list", $list);

        $this->assign("pagebar", $page);

        $this->assign("search", $search);

		$this->assign("xaction",ACTION_NAME);

        $this->assign("query", http_build_query($search));

		

        $this->display();

    }

	

    public function _addFilter()

    {

		$typelist = get_type_leve_list('0','acategory');//分级栏目

		$this->assign('type_list',$typelist);

		

    }

	    public function _editFilter($id){

//	    	var_dump($id);

		$Bconfig = C("BORROW");

		$borrow_status = $Bconfig['BORROW_STATUS'];

		

		switch(strtolower(session('listaction'))){

			case "waitverify":

				for($i=-1;$i<=10;$i++){

					if(in_array($i,array("0","1","2","-1")) ) continue;

					unset($borrow_status[$i]);

				}

			break;

			case "waitverifyquanbu":

				session('listaction',"waitverify");

				for($i=-1;$i<=10;$i++){

					if(in_array($i,array("0","1","2","-1")) ) continue;

					unset($borrow_status[$i]);

				}

			break;

			case "waitstart":

				for($i=-1;$i<=10;$i++){

					if(in_array($i,array("1","2","-1")) ) continue;

					unset($borrow_status[$i]);

				}

			break;

			case "waitverify2":

				for($i=-1;$i<=10;$i++){

					if(in_array($i,array("5","6")) ) continue;

					unset($borrow_status[$i]);

				}

			break;

			case "waitmoney":

				for($i=-1;$i<=10;$i++){

					if(in_array($i,array("1","2","3")) ) continue;

					unset($borrow_status[$i]);

				}

			break;

			case "fail":

				unset($borrow_status['3'],$borrow_status['4'],$borrow_status['5']);

			default:

				// session('listaction','edit');

			break;

		}

		///////////////////////////////////////////////////////////////////////////////////

		$danbao = M('article_category')->field('id,type_name')->where('parent_id=354')->select();

		$dblist = array();

		if(is_array($danbao)){

			foreach($danbao as $key => $v){

				$dblist[$v['id']]=$v['type_name'];

			}

		}

		$this->assign("danbao_list",$dblist);//新增担保标A+

		//////////////////////////////////////////////////////////////////////////////

		$this->assign('xact',session('listaction'));



		$btype = $Bconfig['REPAYMENT_TYPE'];

		$this->assign("vv",M("borrow_verify")->find($id));

		$this->assign('borrow_status',$borrow_status);

		$this->assign('type_list',$btype);

		$this->assign('borrow_type',$Bconfig['BORROW_TYPE']);

		$this->assign('borrow_model',$Bconfig['BORROW_MODEL']);

		$citylist = C('BORROW_CITY');

		$this->assign('city',$citylist);

		$pid=array();

		$typelist = get_type_leve_list('0','Productcategory');//分级栏目

		foreach($typelist as $key =>$val){

			$pid[$val["id"]]=$val["type_name"];

		}		

		$this->assign('pid',$pid);

		$zplist=M("zeng_pin")->order("id desc")->select();

        $this->assign('zplist',$zplist);

		$borrow_qc_info = M('borrow_info')->getFieldById($id,'qc_info');		

		$borrow_qc_info = unserialize($borrow_qc_info);

		$qc_info = $Bconfig['QC_INFO'];

		$this->assign('qc_info',$qc_info);

		$this->assign('borrow_qc_info',$borrow_qc_info);

		

		

		$can_invest = M('borrow_info')->getFieldById($id,'can_invest');		

		$can_invest = unserialize($can_invest);		

		$this->assign('can_invest',$can_invest);

		$month = range(1,12);

		$month_time=array();

		foreach($month as $v){

			$month_time[$v] = $v."";

		}

		$this->assign("borrow_month_time",$month_time);



		$leveconfig = FS("Webconfig/leveconfig");

		$this->assign('leveconfig',$leveconfig);

		//templateid

//		$vo["templateid"]=111;

//			$this->assign('vo',$vo);

			$this->assign('id',$id);

		//setBackUrl(session('listaction'));	

    }

    public function tiyanbiao($id){		

		$Bconfig = C("BORROW");

		$borrow_status = $Bconfig['BORROW_STATUS'];

		

		switch(strtolower(session('listaction'))){

			case "waitverify":

				for($i=-1;$i<=10;$i++){

					if(in_array($i,array("0","1","2","-1")) ) continue;

					unset($borrow_status[$i]);

				}

			break;

				case "waitverifyquanbu":

				for($i=-1;$i<=10;$i++){

					if(in_array($i,array("0","1","2","-1")) ) continue;

					unset($borrow_status[$i]);

				}

			break;

			case "waitstart":

				for($i=-1;$i<=10;$i++){

					if(in_array($i,array("1","2","-1")) ) continue;

					unset($borrow_status[$i]);

				}

			break;

			case "waitverify2":

				for($i=-1;$i<=10;$i++){

					if(in_array($i,array("5","6")) ) continue;

					unset($borrow_status[$i]);

				}

			break;

			case "waitmoney":

				for($i=-1;$i<=10;$i++){

					if(in_array($i,array("2","3")) ) continue;

					unset($borrow_status[$i]);

				}

			break;

			case "fail":

				unset($borrow_status['3'],$borrow_status['4'],$borrow_status['5']);

			default:

				// session('listaction','edit');

			break;

		}

		///////////////////////////////////////////////////////////////////////////////////

		$danbao = M('article_category')->field('id,type_name')->where('parent_id=354')->select();

		$dblist = array();

		if(is_array($danbao)){

			foreach($danbao as $key => $v){

				$dblist[$v['id']]=$v['type_name'];

			}

		}

		$this->assign("danbao_list",$dblist);//新增担保标A+

		//////////////////////////////////////////////////////////////////////////////

		$this->assign('xact',session('listaction'));



		$btype = $Bconfig['REPAYMENT_TYPE'];

		$this->assign("vv",M("borrow_verify")->find($id));

		$this->assign('borrow_status',$borrow_status);

		$this->assign('type_list',$btype);

		$this->assign('borrow_type',$Bconfig['BORROW_TYPE']);

		$this->assign('borrow_model',$Bconfig['BORROW_MODEL']);

		$citylist = C('BORROW_CITY');

		$this->assign('city',$citylist);

		$pid=array();

		$typelist = get_type_leve_list('0','Productcategory');//分级栏目

		foreach($typelist as $key =>$val){

			$pid[$val["id"]]=$val["type_name"];

		}		

		$this->assign('pid',$pid);

		$borrow_qc_info = M('borrow_info')->getFieldById($id,'qc_info');		

		$borrow_qc_info = unserialize($borrow_qc_info);

		$qc_info = $Bconfig['QC_INFO'];

		$this->assign('qc_info',$qc_info);

		$this->assign('borrow_qc_info',$borrow_qc_info);

		

		

		$can_invest = M('borrow_info')->getFieldById($id,'can_invest');		

		$can_invest = unserialize($can_invest);		

		$this->assign('can_invest',$can_invest);

		$month = range(1,12);

		$month_time=array();

		foreach($month as $v){

			$month_time[$v] = $v."";

		}

		$this->assign("borrow_month_time",$month_time);



		$leveconfig = FS("Webconfig/leveconfig");

		$this->assign('leveconfig',$leveconfig);

		//setBackUrl(session('listaction'));	

    }

	public function sRepayment(){

		$borrow_id = $_GET['id'];

		$binfo = M("borrow_info")->field("has_pay,total")->find($borrow_id);

		$from = $binfo['has_pay'] + 1;

		for($i=$from;$i<=$binfo['total'];$i++){

			$res = borrowRepayment($borrow_id,$i,2);

		}

		if($res===true) $this->success("代还成功");

		elseif(!empty($res)) $this->error($res);

		else{

			$this->error("代还出错，请重试");

		}

	}

	public function _doAddFilter($m){

		if(!empty($_FILES['imgfile']['name'])){

			$this->saveRule = date("YmdHis",time()).rand(0,1000);

			$this->savePathNew = C('ADMIN_UPLOAD_DIR').'Article/' ;

			$this->thumbMaxWidth = C('ARTICLE_UPLOAD_W');

			$this->thumbMaxHeight = C('ARTICLE_UPLOAD_H');

			$info = $this->CUpload();

			$data['art_img'] = $info[0]['savepath'].$info[0]['savename'];

		}

		if($data['art_img']) $m->art_img=$data['art_img'];

		$m->art_time=time();

		if($_POST['is_remote']==1) $m->art_content = get_remote_img($m->art_content);

		return $m;

	}

	public function doEditWaitverifyquanbu()

    {

    	//var_dump($_POST['borrow_status']);die;

 



         if (strtotime($_POST['start_time'])<time()) {- 

             $this->error('您选择的项目上线时间不对哦');

             exit;

         }

        $m = D(ucfirst($this->getActionName()));

        if (false === $m->create()) {

            $this->error($m->getError());

        }

        $vm = M('borrow_info')->field(true)->find($m->id);



        if ($vm['borrow_status'] == 2 && $m->borrow_status != 2) {- 

            $this->error('已通过初审通过的项目不能改为别的状态');

            exit;

        }



        $borrow['borrow_video'] = text($_POST['borrow_video']);

        $img                    = "";

        $bigimg                 = "";

        if (!empty($_FILES['borrow_img']['name'][0]) or !empty($_FILES['borrow_img']['name'][1])) {

            $this->fix         = false;

            $this->saveRule    = 'my_filename';

            $this->savePathNew = '/UF/Uploads/borrowimg/';

            $info              = $this->CUpload();

            if (!empty($_FILES['borrow_img']['name'][0])) {

                $m->borrow_img = $info[0]['savepath'] . $info[0]['savename'];

            }



            if (!empty($_FILES['borrow_img']['name'][1])) {

                if (!empty($_FILES['borrow_img']['name'][0])) {

                    $m->borrow_img_big = $info[1]['savepath'] . $info[1]['savename'];

                } else {

                    $m->borrow_img_big = $info[0]['savepath'] . $info[0]['savename'];

                }

            }

        }



        $m->start_time = strtotime($_POST["start_time"]);

        if ($vm['borrow_status'] != 2 && $m->borrow_status == 2) {

            //新标提醒

            //newTip($m->id);

            MTip('chk8', $vm['borrow_uid'], $m->id);

            //自动投标

            if ($m->borrow_type == 1) {

                memberLimitLog($vm['borrow_uid'], 1, -($m->borrow_money), $info = "{$m->id}号标初审通过");

            } elseif ($m->borrow_type == 2) {

                memberLimitLog($vm['borrow_uid'], 2, -($m->borrow_money), $info = "{$m->id}号标初审通过");

            }

            $vss = M("members")->field("user_phone,user_name")->where("id = {$vm['borrow_uid']}")->find();

            SMStip("firstV", $vss['user_phone'], array("#USERANEM#", "ID"), array($vss['user_name'], $m->id));
            //Notice1(9,$vss["id"],array('phone'=>$vss['user_phone']));

        }

//      if($m->borrow_status==2) 

//      $m->collect_time = strtotime("+ {$m->collect_day} days");

        if ($m->borrow_status == 2) {

            $m->collect_time = empty(strtotime("+ {$m->collect_day} days"))?"0":strtotime("+ {$m->collect_day} days");

            //$m->is_tuijian = 1;

        }

		$getBorrowInterest=getBorrowInterest($m->repayment_type, $m->borrow_money, $m->borrow_duration, $m->borrow_interest_rate);

        $m->borrow_interest = empty($getBorrowInterest)?"0.00":$getBorrowInterest;



        $m->city = empty($_POST["city"])?"0":$_POST["city"];



        $m->add_time = time(); //修改



        if ($_POST["start_time"] == "") {

            $m->start_time = $m->add_time;

        }

        //保存当前数据对象

        if ($m->borrow_status == 2 || $m->borrow_status == 1 || $m->borrow_status == '-1') {

            $m->first_verify_time = time();

        } else {

            unset($m->first_verify_time);

        }



        unset($m->borrow_uid);

        $bs = text($_POST['borrow_status']);

        if ($result = $m->save()) {

            //保存成功

            if ($bs == 2 || $bs == 1 || $bs == "-1") {

                $verify_info['borrow_id']   = intval($_POST['id']);

                $verify_info['deal_info']   = text($_POST['deal_info']);

                $verify_info['deal_user']   = $this->admin_id;

                $verify_info['deal_time']   = time();

                $verify_info['deal_status'] = $bs;

                if ($vm['first_verify_time'] > 0) {

                    M('borrow_verify')->save($verify_info);

                } else {

                    M('borrow_verify')->add($verify_info);

                }



            }

            if ($vm['borrow_status'] != 2 && $_POST['borrow_status'] == 2 && empty($vm['password']) == true) {

                // autoInvest(intval($_POST['id']));

            }



            //if($vm['borrow_status']<>2 && $_POST['borrow_status']==2)) autoInvest(intval($_POST['id']));

            //成功提示

            

               // die;



            $this->assign('jumpUrl', __URL__ . "/" . session('listaction'));

            $this->success(L('修改成功'));

        } else {

            //失败提示

//			var_dump($m->getlastsql());

//			die;

            $this->error(L('修改失败'));

        }

    }

	

	public function doEditWaitverify()

    {

    	//var_dump($_POST['borrow_status']);die;

 


        $m = D(ucfirst($this->getActionName()));

        if (false === $m->create()) {

            $this->error($m->getError());

        }

        $vm = M('borrow_info')->field(true)->find($m->id);



        if ($vm['borrow_status'] == 2 && $m->borrow_status != 2) {- 

            $this->error('已通过初审通过的项目不能改为别的状态');

            exit;

        }

 

         if (strtotime($_POST['start_time'])<time()) {- 

             $this->error('您选择的项目上线时间不对哦');

             exit;

         }

        $borrow['borrow_video'] = text($_POST['borrow_video']);

        $img                    = "";

        $bigimg                 = "";

        if (!empty($_FILES['borrow_img']['name'][0]) or !empty($_FILES['borrow_img']['name'][1])) {

            $this->fix         = false;

            $this->saveRule    = 'my_filename';

            $this->savePathNew = '/UF/Uploads/borrowimg/';

            $info              = $this->CUpload();

            if (!empty($_FILES['borrow_img']['name'][0])) {

                $m->borrow_img = $info[0]['savepath'] . $info[0]['savename'];

            }



            if (!empty($_FILES['borrow_img']['name'][1])) {

                if (!empty($_FILES['borrow_img']['name'][0])) {

                    $m->borrow_img_big = $info[1]['savepath'] . $info[1]['savename'];

                } else {

                    $m->borrow_img_big = $info[0]['savepath'] . $info[0]['savename'];

                }

            }

        }

        if (!empty($_FILES['fx_img']['name'][0]) or !empty($_FILES['fx_img']['name'][1])) {

            $this->fix         = false;

            $this->saveRule    = 'my_filename';

            $this->savePathNew = '/UF/Uploads/borrowimg/';

            $info              = $this->CUpload();

            if (!empty($_FILES['fx_img']['name'][0])) {

                $m->fx_img = $info[0]['savepath'] . $info[0]['savename'];

            }

        }

        if (!empty($_FILES['shipinimg']['name'][0]) or !empty($_FILES['shipinimg']['name'][1])) {

            $this->fix         = false;

            $this->saveRule    = 'my_filename';

            $this->savePathNew = '/UF/Uploads/borrowimg/';

            $info              = $this->CUpload();

            if (!empty($_FILES['shipinimg']['name'][0])) {

                $m->shipinimg = $info[0]['savepath'] . $info[0]['savename'];

            }

        }


        $m->start_time = strtotime($_POST["start_time"]);



        if ($vm['borrow_status'] != 2 && $m->borrow_status == 2) {

            //新标提醒

            //newTip($m->id);

            MTip('chk8', $vm['borrow_uid'], $m->id);

            //自动投标

            if ($m->borrow_type == 1) {

                memberLimitLog($vm['borrow_uid'], 1, -($m->borrow_money), $info = "{$m->id}号标初审通过");

            } elseif ($m->borrow_type == 2) {

                memberLimitLog($vm['borrow_uid'], 2, -($m->borrow_money), $info = "{$m->id}号标初审通过");

            }

            $vss = M("members")->field("user_phone,user_name")->where("id = {$vm['borrow_uid']}")->find();

            SMStip("firstV", $vss['user_phone'], array("#USERANEM#", "ID"), array($vss['user_name'], $m->id));

            //Notice1(9,$vss["id"],array('phone'=>$vss['user_phone']));

        }else if ($m->borrow_status == 1) { 

			$data['typename'] = '产品上线';

			$data['type'] = 1; 

			$data['stars'] = 0;

			$data['add_time'] = time();

			$data['uid'] = '';

			$data['uname'] = '管理员';

			$data['tid'] = (int)($m->id);

			$data['name'] = M('borrow_info')->getFieldById($m->id, 'borrow_name');

			$data['dycomment'] = M('borrow_info')->getFieldById($m->id, 'borrow_name').' '. date("Y年m月d日",time())."上线预热，将于". date("Y年m月d日",$m->start_time).'开标';

			$newid = M('dynamic')->add($data);

		 

 

 

		}

//      if($m->borrow_status==2) 

//      $m->collect_time = strtotime("+ {$m->collect_day} days");

        if ($m->borrow_status == 2) {

            $m->collect_time = empty(strtotime("+ {$m->collect_day} days"))?"0":strtotime("+ {$m->collect_day} days");

            //$m->is_tuijian = 1;

        }

		$getBorrowInterest=getBorrowInterest($m->repayment_type, $m->borrow_money, $m->borrow_duration, $m->borrow_interest_rate);

        $m->borrow_interest = empty($getBorrowInterest)?"0.00":$getBorrowInterest;



        $m->city = empty($_POST["city"])?"0":$_POST["city"];



        $m->add_time = time(); //修改



        if ($_POST["start_time"] == "") {

            $m->start_time = $m->add_time;

        }

        //保存当前数据对象

        if ($m->borrow_status == 2 || $m->borrow_status == 1 || $m->borrow_status == '-1') {

            $m->first_verify_time = time();

        } else {

            unset($m->first_verify_time);

        }


        unset($m->borrow_uid);

        $bs = text($_POST['borrow_status']);

        if ($result = $m->save()) {

            //保存成功

            if ($bs == 2 || $bs == 1 || $bs == "-1") {

                $verify_info['borrow_id']   = intval($_POST['id']);

                $verify_info['deal_info']   = text($_POST['deal_info']);

                $verify_info['deal_user']   = $this->admin_id;

                $verify_info['deal_time']   = time();

                $verify_info['deal_status'] = $bs;

                if ($vm['first_verify_time'] > 0) {

                	

                    M('borrow_verify')->save($verify_info);

                } else {

                    M('borrow_verify')->add($verify_info);

                }



            }

            if ($vm['borrow_status'] != 2 && $_POST['borrow_status'] == 2 && empty($vm['password']) == true) {

            	notice1(9, $vm['borrow_uid'], $data = array("BORROW_NAME"=>text($_POST['borrow_name'])));

                // autoInvest(intval($_POST['id']));

            }



            //if($vm['borrow_status']<>2 && $_POST['borrow_status']==2)) autoInvest(intval($_POST['id']));

            //成功提示

            

               // die;



            $this->assign('jumpUrl', __URL__ . "/" . session('listaction'));

            $this->success(L('修改成功'));

        } else {

            //失败提示

//			var_dump($m->getlastsql());

//			die;

            $this->error(L('修改失败'));

        }

    }

	public function doEditWaitStart(){

        $m = D(ucfirst($this->getActionName()));

        if (false === $m->create()) {

            $this->error($m->getError());

        }

		$vm = M('borrow_info')->field(true)->find($m->id);

		

		if($vm['borrow_status']==2 && $m->borrow_status<>2){

			$this->error('已通过初审通过的项目不能改为别的状态');

			exit;

		}

		

		$borrow['borrow_video'] = text($_POST['borrow_video']);

		$img="";

		$bigimg="";

		if(!empty($_FILES['borrow_img']['name'][0]) or !empty($_FILES['borrow_img']['name'][1])){

			$this->fix = false;

			$this->saveRule = 'my_filename';

			$this->savePathNew = '/UF/Uploads/borrowimg/';

			$info = $this->CUpload();

			if(!empty($_FILES['borrow_img']['name'][0])){

				$m->borrow_img = $info[0]['savepath'].$info[0]['savename'];

			}

			

			if(!empty($_FILES['borrow_img']['name'][1])){

				if(!empty($_FILES['borrow_img']['name'][0])){

					$m->borrow_img_big = $info[1]['savepath'].$info[1]['savename'];

				}else{

					$m->borrow_img_big = $info[0]['savepath'].$info[0]['savename'];	

				}

			}

		}

        if (!empty($_FILES['fx_img']['name'][0]) or !empty($_FILES['fx_img']['name'][1])) {

            $this->fix         = false;

            $this->saveRule    = 'my_filename';

            $this->savePathNew = '/UF/Uploads/borrowimg/';

            $info              = $this->CUpload();

            if (!empty($_FILES['fx_img']['name'][0])) {

                $m->fx_img = $info[0]['savepath'] . $info[0]['savename'];

            }

        }

        if (!empty($_FILES['shipinimg']['name'][0]) or !empty($_FILES['shipinimg']['name'][1])) {

            $this->fix         = false;

            $this->saveRule    = 'my_filename';

            $this->savePathNew = '/UF/Uploads/borrowimg/';

            $info              = $this->CUpload();

            if (!empty($_FILES['shipinimg']['name'][0])) {

                $m->shipinimg = $info[0]['savepath'] . $info[0]['savename'];

            }

        }

		 $m->start_time=strtotime($_POST["start_time"]);

		 // $m->deadline=strtotime($_POST["deadline"]);

		if($vm['borrow_status']<>2 && $m->borrow_status==2){

		  //新标提醒

			//newTip($m->id);

			MTip('chk8',$vm['borrow_uid'],$m->id);			

		  //自动投标

			if($m->borrow_type==1){

				memberLimitLog($vm['borrow_uid'],1,-($m->borrow_money),$info="{$m->id}号标初审通过");

			}elseif($m->borrow_type==2){

				memberLimitLog($vm['borrow_uid'],2,-($m->borrow_money),$info="{$m->id}号标初审通过");

			}

			$vss = M("members")->field("user_phone,user_name")->where("id = {$vm['borrow_uid']}")->find();

			SMStip("firstV",$vss['user_phone'],array("#USERANEM#","ID"),array($vss['user_name'],$m->id));
			//Notice1(9,$vss["id"],array('phone'=>$vss['user_phone']));

		}

		//if($m->borrow_status==2) $m->collect_time = strtotime("+ {$m->collect_day} days");		

		$m->borrow_interest = getBorrowInterest($m->repayment_type,$m->borrow_money,$m->borrow_duration,$m->borrow_interest_rate);

		

		$m->city=$_POST["city"];

		

		$m->add_time=time();//修改 

		

		if($_POST["start_time"]==""){

			$m->start_time = $m->add_time;

		}

		if($m->borrow_status==2 || $m->borrow_status==1){ 

			$m->collect_time = strtotime("+ {$m->collect_day} days", $m->start_time);

			//$m->is_tuijian = 1;

		}

        //保存当前数据对象

		if($m->borrow_status==2 || $m->borrow_status==1) $m->first_verify_time = time();

		else unset($m->first_verify_time);

		unset($m->borrow_uid);

		$bs = intval($_POST['borrow_status']);

		if(!empty($_POST['qc_info'])){

			$qc_info    = serialize($_POST['qc_info']);

	        $m->qc_info = $qc_info;			

		}

		if(!empty($_POST['can_invest'])){

			$can_invest    = serialize($_POST['can_invest']);

	        $m->can_invest = $can_invest;

		}
        //var_dump($_POST);
        if ($result = $m->save()) { //保存成功
            //var_dump($m->getLastSql());exit;
			if($bs==2 || $bs==1){

				$verify_info['borrow_id'] = intval($_POST['id']);

				$verify_info['deal_info'] = text($_POST['deal_info']);

				$verify_info['deal_user'] = $this->admin_id;

				$verify_info['deal_time'] = time();

				$verify_info['deal_status'] = $bs;

				if($vm['first_verify_time']>0) M('borrow_verify')->save($verify_info);

				else  M('borrow_verify')->add($verify_info);

			}

			if($vm['borrow_status']<>2 && $_POST['borrow_status']==2 && empty($vm['password'])==true) autoInvest(intval($_POST['id']));

			//if($vm['borrow_status']<>2 && $_POST['borrow_status']==2)) autoInvest(intval($_POST['id']));



			

            //成功提示            

            $this->assign('jumpUrl', __URL__."/".session('listaction'));

            $this->success(L('修改成功'));

        } else {

            //失败提示

			

            $this->error(L('修改失败'));

		}	

	}

	public function doEditWaitverify2(){

	 

	  

        $m = D(ucfirst($this->getActionName()));

        if (false === $m->create()) {

            $this->error($m->getError());

        }

		$vm = M('borrow_info')->field('borrow_name,borrow_uid,borrow_money,borrow_status,borrow_type,first_verify_time,updata,danbao,vouch_money,borrow_fee')->find($m->id);

		

		if($m->borrow_status<>5 && $m->borrow_status<>6){

			$this->error('已经满标的的项目只能改为复审通过或者复审未通过');

			exit;

		}		

		if($vm["borrow_status"]<>4){

			$this->error("复审失败");

			exit;	

		}

		////////////////////图片编辑///////////////////////

		if(!empty($_POST['swfimglist'])){

			foreach($_POST['swfimglist'] as $key=>$v){

				$row[$key]['img'] = substr($v,1);

				$row[$key]['info'] = $_POST['picinfo'][$key];

			}

			$m->updata=serialize($row);

		}

		////////////////////图片编辑///////////////////////

		//复审投标检测

		//$capital_sum1=M('investor_detail')->where("borrow_id={$m->id}")->sum('capital');

		$capital_sum2=M('borrow_investor')->where("borrow_id={$m->id}")->sum('investor_capital');

		

		if(($vm['borrow_money']!=$capital_sum2)){

			//$this->error('投标金额不统一，请确认！');

			//exit;

		}

			if($_POST["lead_time"]!=""){

			$m->lead_time=strtotime($_POST["lead_time"]);	

		}else{

			$m->lead_time=strtotime($_POST["lead_time"]);	

		}
        $m->xs_time = strtotime($_POST["xs_time"]);
        $m->xj_time = strtotime($_POST["xj_time"]);

        $m->sg_time = strtotime($_POST["sg_time"]);
	

		  //lead_time

		if($m->borrow_status==6){//复审通过

			$appid = borrowApproved($m->id);

			if(!$appid) $this->error("复审失败");

			// 复审成功，秒标，查询是否自动还款成功。

			distribution_maid20190304($m->id);

			$miao_state = M('borrow_info')->where("id={$m->id}")->getfield('borrow_status');

			//如果还款成功,更新标的状态为'已完成'

			if($miao_state==7) $m->borrow_status=7;

		    //新标提醒

			//newTip($m->id); 

			MTip('chk9',$vm['borrow_uid'],$m->id);

		  //自动投标

			

			$vss = M("members")->field("user_phone,user_name")->where("id = {$vm['borrow_uid']}")->find();

			SMStip("approve",$vss['user_phone'],array("#USERANEM#","ID"),array($vss['user_name'],$m->id));

		    //autoInvest($result);

			

			

			

			

				$data['typename'] = '产品上线';

					$data['type'] = 1; 

					$data['stars'] = 0;

					$data['add_time'] = time();

					$data['uid'] = '';

					$data['uname'] = '管理员';

					$data['tid'] = (int)($m->id);

					$data['name'] = M('borrow_info')->getFieldById($m->id, 'borrow_name');

					$data['dycomment'] = M('borrow_info')->getFieldById($m->id, 'borrow_name'). " 已认购成功， 感谢您的参与！";

					$newid = M('dynamic')->add($data);

				

		}elseif($m->borrow_status==5){//复审未通过

			$appid = borrowRefuse($m->id,3);

			if(!$appid) $this->error("复审失败");

			MTip('chk12',$vm['borrow_uid'],$m->id);

		}

		

        //保存当前数据对象

		$m->second_verify_time = time();

		unset($m->borrow_uid);

		$bs = intval($_POST['borrow_status']);

		if(!empty($_POST['qc_info'])){

			$qc_info    = serialize($_POST['qc_info']);

	        $m->qc_info = $qc_info;			

		}

		if(!empty($_POST['can_invest'])){

			$can_invest    = serialize($_POST['can_invest']);

	        $m->can_invest = $can_invest;

		}

		// $m->deadline=strtotime($_POST["deadline"]);

		unset($m->start_time);

        if ($result = $m->save()) { //保存成功

            if($_POST['is_huodong']=='1'){
                $mmap["borrow_id"]=$_POST['id'];
                $mmap["type"]=2;
                $mmap["status"]=1;
                $zplist=M("zporder")->where($mmap)->select();
                foreach ($zplist as $zk=>$zv){
                    memberMoneyLog($zv['uid'],312,$zv['money'],$vm['borrow_name']."项目复审通过赠品折现，增加余额", 0, "",false);
                    M("zporder")->where('id='.$zv["id"])->save(array("status"=>2));
                }
            }
				$verify_info['borrow_id'] = intval($_POST['id']);

				$verify_info['deal_info_2'] = text($_POST['deal_info_2']);

				$verify_info['deal_user_2'] = $this->admin_id;

				$verify_info['deal_time_2'] = time();

				$verify_info['deal_status_2'] = $bs;

				if($vm['first_verify_time']>0) M('borrow_verify')->save($verify_info);

				else  M('borrow_verify')->add($verify_info);


            //成功提示

            $this->assign('jumpUrl', __URL__."/".session('listaction'));

            $this->success(L('修改成功1'));

        } else {

            //失败提示

            $this->error(L('修改失败'));

		}	

	}

	public function doEditWaitmoney(){

		$fee_borrow_manage = explode("|",$this->glo['fee_borrow_manage']);

	

        $m = D(ucfirst($this->getActionName()));

        if (false === $m->create()) {

            $this->error($m->getError());

        }

        $vm = M('borrow_info')->field('borrow_uid,has_borrow,borrow_type,borrow_money,first_verify_time,borrow_interest_rate,borrow_duration,repayment_type,collect_day,collect_time,borrow_fee,start_time')->find($m->id);

		

		if($_POST["pause"]==1){

		  if($vm["has_borrow"]==0){

			$this->error('该标还没有投资，不能截标！');

			  exit;  

		  }

		  



		  $m->borrow_money=empty($vm["has_borrow"])?0:$vm["has_borrow"];

		 

		 // $m->borrow_money=10000;

		  //\print_r($fee_borrow_manage);

		  $m->has_borrow=$vm["has_borrow"];

		  if($m->repayment_type == 1){//按天还

			  $fee_rate = (is_numeric($fee_borrow_manage[0]))?($fee_borrow_manage[0]/1000):0;

			  $m->borrow_fee = getFloatValue($fee_rate*$m->has_borrow*$m->borrow_duration,2);

		  }else{

			  $fee_rate_1=(is_numeric($fee_borrow_manage[1]))?($fee_borrow_manage[1]/1000):0;

			 // echo $fee_rate_1;

			  //echo $m->has_borrow;

			  $m->borrow_fee=getFloatValue($m->has_borrow*$fee_rate_1*$m->borrow_duration,2);

			 // echo $m->borrow_fee;

		  }

		  if($m->repayment_type ==5){

			  $fee_rate = (is_numeric($fee_borrow_manage[0]))?($fee_borrow_manage[0]/100):0.001;

			  $m->borrow_fee = getFloatValue($fee_rate*$m->has_borrow*$m->borrow_duration,2);

		  }

		

		}




		//招标中的项目流标

		if($m->borrow_status==3){

			//流标返回

			$appid = borrowRefuse($m->id,2);

			if(!$appid) $this->error("流标失败");

			MTip('chk11',$vm['borrow_uid'],$m->id);

			$m->second_verify_time = time();

			//流标操作相当于复审

			$verify_info['borrow_id'] = $m->id;

			$verify_info['deal_info_2'] = text($_POST['deal_info_2']);

			$verify_info['deal_user_2'] = $this->admin_id;

			$verify_info['deal_time_2'] = time();

			$verify_info['deal_status_2'] = $m->borrow_status;

			if($vm['first_verify_time']>0) M('borrow_verify')->save($verify_info);

			else  M('borrow_verify')->add($verify_info);

			

			$vss = M("members")->field("user_phone,user_name")->where("id = {$vm['borrow_uid']}")->find();

			SMStip("refuse",$vss['user_phone'],array("#USERANEM#","ID"),array($vss['user_name'],$verify_info['borrow_id']));

		}else{

			if($vm['collect_day'] < $m->collect_day){

				$spanday = $m->collect_day-$vm['collect_day'];

				$m->collect_time = empty(strtotime("+ {$spanday} day",$vm['collect_time']))?" ":strtotime("+ {$spanday} day",$vm['collect_time']);

			}

			unset($m->second_verify_time);	

		}

		

        //保存当前数据对象

 		unset($m->borrow_uid);

		////////////////////图片编辑///////////////////////

		foreach($_POST['swfimglist'] as $key=>$v){

			$row[$key]['img'] = substr($v,1);

			$row[$key]['info'] = $_POST['picinfo'][$key];

		}

		$m->updata=serialize($row);

		////////////////////图片编辑///////////////////////

		if($_POST["start_time"]!=""){

			$m->start_time=strtotime($_POST["start_time"]);	

		}else{

			$m->start_time="";	

		}

	   

	   

	   

	   $borrow['borrow_video'] = text($_POST['borrow_video']);

		$img="";

		$bigimg="";

		if(!empty($_FILES['borrow_img']['name'][0]) or !empty($_FILES['borrow_img']['name'][1])){

			$this->fix = false;

			$this->saveRule = 'my_filename';

			$this->savePathNew = '/UF/Uploads/borrowimg/';

			$info = $this->CUpload();

			if(!empty($_FILES['borrow_img']['name'][0])){

				$m->borrow_img = $info[0]['savepath'].$info[0]['savename'];

			}

			

			if(!empty($_FILES['borrow_img']['name'][1])){

				if(!empty($_FILES['borrow_img']['name'][0])){

					$m->borrow_img_big = $info[1]['savepath'].$info[1]['savename'];

				}else{

					$m->borrow_img_big = $info[0]['savepath'].$info[0]['savename'];	

				}

			}

		}		

		if(!empty($_POST['qc_info'])){

			$qc_info    = serialize($_POST['qc_info']);

	        $m->qc_info = $qc_info;			

		}

		if(!empty($_POST['can_invest'])){

			$can_invest    = serialize($_POST['can_invest']);

	        $m->can_invest = $can_invest;

		}
		if($m->borrow_status!=1){
			unset($m->start_time);
		}

		unset($m->deadline);

       	if ($result = $m->save()) { //保存成功

	   		//$this->assign("waitSecond",10000);

            //成功提示

			if($_POST["pause"]==1){

				borrowfull($_POST["id"],$_POST["borrow_type"]);	

			}

            $this->assign('jumpUrl', __URL__."/".session('listaction'));

            $this->success(L('修改成功'));

        } else {

            //失败提示

			

            $this->error(L('修改失败'));

		}	

	}

	public function doEditrepaymenting(){

		$this->doEditdone();

	}

	public function doEditdone(){

        $m = D(ucfirst($this->getActionName()));        

		

		$data['id'] = intval($_POST['id']);

		$data['borrow_follow'] = text($_POST['borrow_follow']);

		// $m->create($data);

		$m->create();

        if (!empty($_FILES['fx_img']['name'][0]) or !empty($_FILES['fx_img']['name'][1])) {

            $this->fix         = false;

            $this->saveRule    = 'my_filename';

            $this->savePathNew = '/UF/Uploads/borrowimg/';

            $info              = $this->CUpload();

            if (!empty($_FILES['fx_img']['name'][0])) {

                $m->fx_img = $info[0]['savepath'] . $info[0]['savename'];

            }

        }
        if (!empty($_FILES['shipinimg']['name'][0]) or !empty($_FILES['shipinimg']['name'][1])) {

            $this->fix         = false;

            $this->saveRule    = 'my_filename';

            $this->savePathNew = '/UF/Uploads/borrowimg/';

            $info              = $this->CUpload();

            if (!empty($_FILES['shipinimg']['name'][0])) {

                $m->shipinimg = $info[0]['savepath'] . $info[0]['savename'];

            }

        }
        if($_POST["lead_time"]!=""){
            $m->lead_time=strtotime($_POST["lead_time"]);
        }
 
		unset($m->start_time);

        $vm = M('borrow_info')->field('borrow_uid,has_borrow,borrow_type,borrow_money,first_verify_time,borrow_interest_rate,borrow_duration,repayment_type,collect_day,collect_time,borrow_fee,start_time')->find($m->id);

		



       	if ($result = $m->save()) { //保存成功	   					

            $this->assign('jumpUrl', __URL__."/".session('listaction'));

            $this->success(L('修改成功'));

        } else {

            $this->error(L('修改失败'));

		}	

	}	

	public function doEditFail(){

        $m = D(ucfirst($this->getActionName()));

        if (false === $m->create()) {

            $this->error($m->getError());

        }

		$vm = M('borrow_info')->field('borrow_uid,borrow_status')->find($m->id);

		if($vm['borrow_status']==2 && $m->borrow_status<>2){

			$this->error('已通过审核的项目不能改为别的状态');

			exit;

		}

		

		foreach($_POST['updata_name'] as $key=>$v){

			$updata[$key]['name'] = $v;

			$updata[$key]['time'] = $_POST['updata_time'][$key];

		}

		$m->borrow_interest = getBorrowInterest($m->repayment_type,$m->borrow_money,$m->borrow_duration,$m->borrow_interest_rate);

		$m->updata = serialize($updata);

		$m->collect_time = strtotime($m->collect_time);

        //保存当前数据对象

        if(!empty($_POST['qc_info'])){

			$qc_info    = serialize($_POST['qc_info']);

	        $m->qc_info = $qc_info;			

		}

		if(!empty($_POST['can_invest'])){

			$can_invest    = serialize($_POST['can_invest']);

	        $m->can_invest = $can_invest;

		}

        if ($result = $m->save()) { //保存成功

            //成功提示

            $this->assign('jumpUrl', __URL__."/".session('listaction'));

            $this->success(L('修改成功'));

        } else {

            //失败提示

            $this->error(L('修改失败'));

		}	

	}

	

	

	protected function _AfterDoEdit(){

	 

		switch(strtolower(session('listaction'))){

			case "waitverify":

				$v = M('borrow_info')->field('borrow_uid,borrow_status,deal_time')->find(intval($_POST['id']));

				if(empty($v['deal_time'])){

					$newid = M('members')->where("id={$v['borrow_uid']}")->setInc('credit_use',floatval($_POST['borrow_money']));

					if($newid) M('borrow_info')->where("id={$v['borrow_uid']}")->setField('deal_time',time());

				}

				//$this->assign("waitSecond",1000);

				//Notice();s

			break;

		}

	}

	

	public function _listFilter($list){

		session('listaction',ACTION_NAME);

		$Bconfig = C("BORROW");

	 	$listType = $Bconfig['REPAYMENT_TYPE'];

	 	$BType = $Bconfig['BORROW_TYPE'];

		$typelist = get_type_leve_list('0','Productcategory');//分级栏目

		$pid=array();

		foreach($typelist as $key =>$val){

			$pid[$val["id"]]=$val["type_name"];

		}

		//print_r($pid);

	

		$row=array();

		$aUser = get_admin_name();

		foreach($list as $key=>$v){

			$v['repayment_type_num'] = $v['repayment_type'];

			$v['repayment_type'] = $listType[$v['repayment_type']];

			$v['borrow_type'] = $BType[$v['borrow_type']];

			$v['borrow_type'] =$pid[$v['pid']];

			$where['has_capital']=1;

			$where['borrow_id']=$v["id"];

			$v['repayment_time']= M("investor_detail")->field("repayment_time")->where($where)->find();

// 			var_dump($v['repayment_time']);die

			if($v['deadline']) $v['overdue'] = getLeftTime($v['deadline']) * (-1);

			if($v['borrow_status']==1 || $v['borrow_status']==3 || $v['borrow_status']==5){

				$v['deal_uname_2'] = $aUser[$v['deal_user_2']];

				$v['deal_uname'] = $aUser[$v['deal_user']];

			}

			

			$row[$key]=$v;

		}

		return $row;

	}

	

	

	 public function doweek()

    {

		$map=array();

		$map['b.borrow_status'] = 6;

		if(!empty($_REQUEST['isShow'])){

			$week_1 = array(strtotime(date("Y-m-d",time())." 00:00:00"),strtotime("+7 day",strtotime(date("Y-m-d",time())." 23:59:59")));//一周内

			$map['b.deadline'] = array("between",$week_1);

		}

		if(!empty($_REQUEST['uname'])&&!$_REQUEST['uid'] || $_REQUEST['uname']!=$_REQUEST['olduname']){

			$uid = M("members")->getFieldByUserName(text($_REQUEST['uname']),'id');

			$map['b.borrow_uid'] = $uid;

			$search['uid'] = $map['b.borrow_uid'];

			$search['uname'] = $_REQUEST['uname'];

		}

		if( !empty($_REQUEST['uid'])&&!isset($search['uname']) ){

			$map['b.borrow_uid'] = intval($_REQUEST['uid']);

			$search['uid'] = $map['b.borrow_uid'];

			$search['uname'] = $_REQUEST['uname'];

		}

		if(!empty($_REQUEST['bj']) && !empty($_REQUEST['money'])){

			$map['b.borrow_money'] = array($_REQUEST['bj'],$_REQUEST['money']);

			$search['bj'] = $_REQUEST['bj'];	

			$search['money'] = $_REQUEST['money'];	

		}

		if(!empty($_REQUEST['start_time']) && !empty($_REQUEST['end_time'])){

			$timespan = strtotime(urldecode($_REQUEST['start_time'])).",".strtotime(urldecode($_REQUEST['end_time']));

			$map['b.add_time'] = array("between",$timespan);

			$search['start_time'] = urldecode($_REQUEST['start_time']);	

			$search['end_time'] = urldecode($_REQUEST['end_time']);	

		}elseif(!empty($_REQUEST['start_time'])){

			$xtime = strtotime(urldecode($_REQUEST['start_time']));

			$map['b.add_time'] = array("gt",$xtime);

			$search['start_time'] = $xtime;	

		}elseif(!empty($_REQUEST['end_time'])){

			$xtime = strtotime(urldecode($_REQUEST['end_time']));

			$map['b.add_time'] = array("lt",$xtime);

			$search['end_time'] = $xtime;	

		}

		

		if(session('admin_is_kf')==1){

				$map['m.customer_id'] = session('admin_id');

		}else{

			if($_REQUEST['customer_id'] && $_REQUEST['customer_name']){

				$map['m.customer_id'] = $_REQUEST['customer_id'];

				$search['customer_id'] = $map['m.customer_id'];	

				$search['customer_name'] = urldecode($_REQUEST['customer_name']);	

			}

			

			if($_REQUEST['customer_name'] && !$search['customer_id']){

				$cusname = urldecode($_REQUEST['customer_name']);

				$kfid = M('ausers')->getFieldByUserName($cusname,'id');

				$map['m.customer_id'] = $kfid;

				$search['customer_name'] = $cusname;	

				$search['customer_id'] = $kfid;	

			}

		}

		//分页处理

		import("ORG.Util.Page");

		

		$count = M('borrow_info b')->join("{$this->pre}members m ON m.id=b.borrow_uid")->where($map)->count('b.id');

		//echo M('borrow_info b')->getlastsql();

		$p = new Page($count, C('ADMIN_PAGE_SIZE'));

		$page = $p->show();

		$Lsql = "{$p->firstRow},{$p->listRows}";

		//分页处理

		$field= 'b.id as id,b.borrow_name,b.borrow_uid,b.borrow_duration,b.borrow_type,b.borrow_money,b.borrow_fee,b.borrow_interest_rate,b.repayment_type,b.deadline,m.user_name,m.id mid,b.repayment_money,b.total,b.has_pay,b.pid';

		$list = M('borrow_info b')->field($field)->join("{$this->pre}members m ON m.id=b.borrow_uid")->where($map)->limit($Lsql)->order("b.id DESC")->select();

		$list = $this->_listFilter($list);

		

        $this->assign("bj", array("gt"=>'大于',"eq"=>'等于',"lt"=>'小于'));

        $this->assign("list", $list);

        $this->assign("pagebar", $page);

        $this->assign("search", $search);

		$this->assign("xaction",ACTION_NAME);

        $this->assign("query", http_build_query($search));

		

        $this->display();

    }

	

	public function re_search()

    {

		$map=array();

		$map['b.borrow_status'] = 6;

	

		if(!empty($_REQUEST['start_time']) && !empty($_REQUEST['end_time'])){

			$timespan = strtotime(urldecode($_REQUEST['start_time'])).",".strtotime(urldecode($_REQUEST['end_time']));

			$map['b.add_time'] = array("between",$timespan);

			$search['start_time'] = urldecode($_REQUEST['start_time']);	

			$search['end_time'] = urldecode($_REQUEST['end_time']);	

		}elseif(!empty($_REQUEST['start_time'])){

			$xtime = strtotime(urldecode($_REQUEST['start_time']));

			$map['b.add_time'] = array("gt",$xtime);

			$search['start_time'] = $xtime;	

		}elseif(!empty($_REQUEST['end_time'])){

			$xtime = strtotime(urldecode($_REQUEST['end_time']));

			$map['b.add_time'] = array("lt",$xtime);

			$search['end_time'] = $xtime;	

		}

		

		if(session('admin_is_kf')==1){

				$map['m.customer_id'] = session('admin_id');

		}else{

			if($_REQUEST['customer_id'] && $_REQUEST['customer_name']){

				$map['m.customer_id'] = $_REQUEST['customer_id'];

				$search['customer_id'] = $map['m.customer_id'];	

				$search['customer_name'] = urldecode($_REQUEST['customer_name']);	

			}

			

			if($_REQUEST['customer_name'] && !$search['customer_id']){

				$cusname = urldecode($_REQUEST['customer_name']);

				$kfid = M('ausers')->getFieldByUserName($cusname,'id');

				$map['m.customer_id'] = $kfid;

				$search['customer_name'] = $cusname;	

				$search['customer_id'] = $kfid;	

			}

		}

		//分页处理

		import("ORG.Util.Page");

		

		$count = M('borrow_info b')->join("{$this->pre}members m ON m.id=b.borrow_uid")->where($map)->count('b.id');

		//echo M('borrow_info b')->getlastsql();

		$p = new Page($count, C('ADMIN_PAGE_SIZE'));

		$page = $p->show();

		$Lsql = "{$p->firstRow},{$p->listRows}";

		//分页处理

		$field= 'b.id as id,b.borrow_name,b.borrow_uid,b.borrow_duration,b.borrow_type,b.borrow_money,b.borrow_fee,b.borrow_interest_rate,b.repayment_type,b.deadline,m.user_name,m.id mid,b.repayment_money,b.total,b.has_pay,b.pid';

		$list = M('borrow_info b')->field($field)->join("{$this->pre}members m ON m.id=b.borrow_uid")->where($map)->limit($Lsql)->order("b.id DESC")->select();

		$list = $this->_listFilter($list);

		

        $this->assign("bj", array("gt"=>'大于',"eq"=>'等于',"lt"=>'小于'));

        $this->assign("list", $list);

        $this->assign("pagebar", $page);

        $this->assign("search", $search);

		$this->assign("xaction",ACTION_NAME);

        $this->assign("query", http_build_query($search));

		

        $this->display();

    }

	

	

	//swf上传图片

	public function swfUpload(){

		if($_POST['picpath']){

			$imgpath = substr($_POST['picpath'],1);

			if(in_array($imgpath,$_SESSION['imgfiles'])){

					 unlink(C("WEB_ROOT").$imgpath);

					 $thumb = get_thumb_pic($imgpath);

				$res = unlink(C("WEB_ROOT").$thumb);

				if($res) $this->success("删除成功","",$_POST['oid']);

				else $this->error("删除失败","",$_POST['oid']);

			}else{

				$this->error("图片不存在","",$_POST['oid']);

			}

		}else{

			$this->savePathNew = C('ADMIN_UPLOAD_DIR').'Product/' ;

			$this->thumbMaxWidth = C('PRODUCT_UPLOAD_W');

			$this->thumbMaxHeight = C('PRODUCT_UPLOAD_H');

			$this->saveRule = date("YmdHis",time()).rand(0,1000);

			$info = $this->CUpload();

			$data['product_thumb'] = $info[0]['savepath'].$info[0]['savename'];

			if(!isset($_SESSION['count_file'])) $_SESSION['count_file']=1;

			else $_SESSION['count_file']++;

			$_SESSION['imgfiles'][$_SESSION['count_file']] = $data['product_thumb'];

			echo "{$_SESSION['count_file']}:".__ROOT__."/".$data['product_thumb'];//返回给前台显示缩略图

		}

	}

	public function applicationbouns(){
        $this->autohuikuan();

		$map=array();

		$map['b.id'] = ['neq',1];

		$map['b.borrow_status'] = 6;//还款中

		$map['il.status'] = 0;//还款中


		//分页处理

		import("ORG.Util.Page");

		$count = M('investor_log il')->join("{$this->pre}borrow_info b ON b.id=il.borrow_id")->where($map)->count('il.id');

		$p = new Page($count, C('ADMIN_PAGE_SIZE'));

		$page = $p->show();

		$Lsql = "{$p->firstRow},{$p->listRows}";

		//分页处理

		$field= 'b.id as id,b.borrow_name,b.borrow_uid,b.borrow_duration,b.borrow_type,b.borrow_money,b.borrow_interest,b.updata,b.borrow_fee,b.borrow_interest_rate,b.repayment_type,b.repayment_money,b.total,b.has_pay,il.deadline,il.borrow_id,il.sort_order,il.info,il.capital,il.id as lid';

		$list = M('investor_log il')->join("{$this->pre}borrow_info b ON b.id=il.borrow_id")->field($field)->where($map)->limit($Lsql)->order("il.id DESC")->select();

		$list = $this->_listFilter($list);

		

        $this->assign("bj", array("gt"=>'大于',"eq"=>'等于',"lt"=>'小于'));

        $this->assign("list", $list);

        $this->assign("pagebar", $page);

        $this->assign("search", $search);

		$this->assign("xaction",ACTION_NAME);

        $this->assign("query", http_build_query($search));

		

        $this->display();

	}

    /**
     * 自动回款开始
    */
    public function autohuikuan(){
        $map['total']=1;
        $map['borrow_status']=6;
        $map['pid']=array('not in',array(3));
        $map['lead_time']=array('lt',strtotime(date('Y-m-d 23:59:59')));
        M('borrow_info')->startTrans();
        $list=M('borrow_info')->lock(true)->field('id,borrow_duration,borrow_status')->where($map)->select();
        $investyear=explode('-',date('Y-m-d',time()))[0];
        $fs1=$fs=0;
        foreach ($list as $k=>$v) {
            $dlog=M('investor_log')->where("borrow_id={$v['id']} and sort_order=1")->find();
            if(!$dlog){
                $res=$this->generate($investyear,$v['id']);
                if(!$res){
                    $this->error('生成自动回款失败','/borrow/repaymenting');
                }
                $fs1+=1;
            }
            else{
               $fs=$fs+1;
            }
        }
        M('borrow_info')->commit();
       // $this->success('生成自动回款成功','/borrow/applicationbouns');


    }
    public function generate($investyear,$borrow_id)
    {
        $has_capital=0;
        $sort_order=1;
        $borrowinfo = M('borrow_info')->field(true)->find($borrow_id);
        if (7 == $borrowinfo['borrow_status']) {
            return false;
        }
        $borrowInvestor = D('borrow_investor');
        $income=sprintf("%.2f",$borrowinfo['borrow_interest_rate'] *($borrowinfo['has_borrow'] / $borrowinfo['total']/100));
        //$this->pre($borrowinfo);
        if($borrowinfo["shoujia"]==0){
            $pchajia=0;
        }else{
            $chajia=$borrowinfo["shoujia"]-($borrowinfo["borrow_interest_rate"]*$borrowinfo["borrow_min"]/100+$borrowinfo["borrow_min"]);
            $investorList = $borrowInvestor->field('id,xsfenshu')->where("borrow_id={$borrow_id} and lzh_borrow_investor.status!=3")->select();
            $sc=0;
            foreach ($investorList as $kd=>$vd){
                $sc+=$vd["xsfenshu"];
            }
            $pchajia= $sc*$chajia;
        }
        $starttime=$borrowinfo['full_time'];
        $endtime=$borrowinfo['lead_time'];

        $isnew=true;
        $chajia=0;
        $m = M('member_money')->field('account_money')->find($borrowinfo['borrow_uid']);
        if(empty($borrowinfo["shoujia"])) {
            //var_dump('12');
        }else{
            $chajia=$borrowinfo["shoujia"]-($borrowinfo["borrow_interest_rate"]*$borrowinfo["borrow_min"]/100+$borrowinfo["borrow_min"]);
            if($borrowinfo['total'] == $sort_order){
                $has_capital=1;
            }
            if (1 == $has_capital) {
                $capital1= $income + $borrowinfo['sghas_borrow']+$pchajia;
            } else {
                $capital1= $income;
            }
        }
        //var_dump($capital1);
        if ($m['account_money'] < $capital1) {
            $this->error('还款金额不足，请先充值再进行还款！');
        }

        $investor_detail = M('investor_detail')->where("borrow_id={$borrow_id} and sort_order={$sort_order}")->find();
        if ($investor_detail) {
            $this->error('本期已经提交申请了');
        }
//        $borrowInvestor = D('borrow_investor');
//        $borrowInvestor->startTrans();
        $investorList = $borrowInvestor->field('id,investor_uid,xsfenshu,fenshu,investor_capital,xsinvestor_capital,investor_interest,is_experience,member_interest_rate_id')->where("borrow_id={$borrow_id} and lzh_borrow_investor.status!=3")->select();
        $done = true;
        //var_dump(D('borrow_investor')->getlastsql());
        foreach ($investorList as $key => $value) {
            if (!$done) {
                break;
            }
            $buname = m('members')->getFieldById($value['investor_uid'], 'user_name');
            //$ interest
            //每期收益
            if($isnew){
                $interest = sprintf("%.2f",$income *($value['xsinvestor_capital'] / $borrowinfo['sghas_borrow']));
            }else{
                $interest = sprintf("%.2f",$income *($value['investor_capital'] / $borrowinfo['has_borrow']));
            }
            $investdetail['borrow_id'] = $borrow_id;
            $investdetail['invest_id'] = $value['id'];
            $investdetail['investor_uid'] = $value['investor_uid'];
            $investdetail['borrow_uid'] = $borrowinfo['borrow_uid'];
            if (1 == $has_capital) {
                if($isnew){
                    $investdetail['capital'] = $value['xsinvestor_capital'];
                    if($value["xsfenshu"]!=0){
                        $investdetail['chajia'] = $chajia*$value["xsfenshu"];
                    }else{
                        $investdetail['chajia'] =0;
                    }
                }else{
                    $investdetail['capital'] = $value['investor_capital'];
                    $investdetail['chajia'] =0;
                }
            } else {
                $investdetail['capital'] = 0;
                $investdetail['chajia'] =0;
            }
            $investdetail['investyear'] = $investyear;
            $investdetail['interest'] = $interest;
            $investdetail['interest_fee'] = 0;
            $investdetail['income'] = $income;
            $investdetail['status'] = 0;
            $investdetail['sort_order'] = $sort_order;
            $investdetail['total'] = $borrowinfo['total'];
            $investdetail['starttime'] = strtotime(date('Y-m-d',$starttime))+86400;
            $investdetail['endtime'] = strtotime(date('Y-m-d',$endtime));
            $investdetail['has_capital'] = $has_capital;
            if($value["xsinvestor_capital"]!=0||$value["fenshu"]==0){
                $savedetail[] = $investdetail;
            }
        }
        //平均年收益率 平均年收益率=本期收益金额/已筹总额/收益周期（月数）*12
        $borrow_duration = $borrowinfo['borrow_duration'];
        $invest_defail_id = m('investor_detail')->addAll($savedetail);
        $info=$borrowinfo['borrow_name'].'自动回款';
        $resutl=  $this->subgenerate($info,$borrow_id,1,$capital1);
        if ($invest_defail_id && $resutl) {
           // $borrowInvestor->commit();
            return true;
        }else{
            //$borrowInvestor->rollback();
            return false;
        }
    }
    public function subgenerate($info,$borrow_id,$invest_orderid,$capital)
    {
        $log['info'] = $info;// = $_POST['info'];
        $log['borrow_id'] = $borrow_id;// = $_POST['borrow_id'];
        $log['sort_order'] = $invest_orderid;// = $_POST['invest_orderid'];
        $log['capital'] = $capital;// = $_POST['capital'];
        $log['has_capital'] = $has_capital=0;// = $_POST['has_capital'];
        $borrowinfo = M('borrow_info')->field(true)->find($borrow_id);
        if (7 == $borrowinfo['borrow_status']) {
            $this->error('已还款');
        }
        if($borrowinfo['total'] == $invest_orderid){
            $log['has_capital'] = $has_capital=1;
        }
        $investor_detail = M('investor_detail')->where("borrow_id={$borrow_id} and sort_order={$invest_orderid}")->find();
        $investor_log = M('investor_log')->where("borrow_id={$borrow_id} and sort_order={$invest_orderid}")->find();
        if (!isset($investor_detail)) {
//            var_dump($borrow_id);
//            var_dump( M('investor_detail')->getLastSql());exit;
            $this->error('请先生成收益明细');
        }
        if ($investor_log) {
            $this->error('收益申请重复');
        }
        $log['status'] = 0;
        $log['deadline'] = time();
        $investor_log_id = m('investor_log')->add($log);
        if ($investor_log_id) {
            memberMoneyLog($borrowinfo['borrow_uid'], 121, 0 - $capital, "对{$borrowinfo['borrow_name']}进行还款申请",0,'',false);
            //$this->success('操作成功！', '/Member/');
            return true;
        } else {
            //M('investor_detail')->where("borrow_id={$borrow_id} and sort_order={$invest_orderid}")->delete();
            //$this->error('操作失败！');
            return false;
        }
    }
     /**
      * 自动回款结束
      */

	public function doapprepayment(){

	     

		$pre = C('DB_PREFIX');

		$borrow_id = intval($_POST['bid']);

		$sort_order = intval($_POST['sort_order']);

		$vos = M("borrow_info")->field('id')->where("id={$borrow_id}")->find();

		if(!is_array($vos)) ajaxmsg("数据有误",0);

  		$pre = c("DB_PREFIX");

	    $done = false;

	    $borrowDetail = d("investor_detail");

	    $binfo = m("borrow_info")->field("is_huodong,id,borrow_uid,borrow_name,borrow_type,borrow_money,borrow_duration,repayment_type,has_pay,total,deadline")->find($borrow_id);

	    		$borrow_name==$binfo['borrow_name'];

	    $b_member = m("members")->field("user_name")->find($binfo['borrow_uid']);

	     $voxe = $borrowDetail->field("sort_order,sum(capital) as capital, sum(interest) as interest, sum(chajia) as chajia")->where("borrow_id={$borrow_id}")->group("sort_order")->select();

	    foreach ($voxe as $ee => $ss) {

	        if ($ss['sort_order'] == $sort_order) {

	            $vo = $ss;

	        }

	    }

	    $investor_log = m("investor_log")->where("borrow_id={$borrow_id} AND sort_order={$sort_order}")->find();

	    if ($investor_log['status'] == 1) {        

	        return "本期已还过，不用再还";

	    }



	    $accountMoney_borrower = m("member_money")->field("money_freeze,money_collect,account_money,money_experience,yubi,yubi_freeze")->find($binfo['borrow_uid']);

	    $borrow_name= $binfo["borrow_name"];

	    $detailList = $borrowDetail->field("chajia,invest_id,investor_uid,capital,interest,interest_fee,borrow_id,total,has_capital")->where("borrow_id={$borrow_id} AND sort_order={$sort_order}")->select();

	    

	    $borrowDetail->startTrans();

	    $bxid = false;

        $datamoney_x['uid'] = $binfo['borrow_uid'];

        $datamoney_x['type'] = 11;

        $datamoney_x['affect_money'] = ($investor_log['capital']);

        $datamoney_x['account_money'] = $accountMoney_borrower['account_money'];

        $datamoney_x['collect_money'] = $accountMoney_borrower['money_collect'];

        $datamoney_x['experience_money'] = $accountMoney_borrower['money_experience'];

        $datamoney_x['freeze_money'] = 	$accountMoney_borrower['money_freeze'] - $datamoney_x['affect_money'];

        $datamoney_x['yubi'] = $accountMoney_borrower['yubi'];
        $datamoney_x['freeze_yubi'] = $accountMoney_borrower['yubi_freeze'];



        $mmoney_x['money_freeze'] = $datamoney_x['freeze_money'];

        $mmoney_x['money_collect'] = $datamoney_x['collect_money'];

        $mmoney_x['account_money'] = $datamoney_x['account_money'];

        $mmoney_x['money_experience'] = $datamoney_x['experience_money'];


        if($vos["total"]==1){
            $datamoney_x['info'] = "会员对{$borrow_name}还款";
        }else{
            $datamoney_x['info'] = "会员对{$borrow_name}第{$sort_order}期还款";
        }


        $datamoney_x['add_time'] = time();

        $datamoney_x['add_ip'] = get_client_ip();

        $datamoney_x['target_uid'] = 0;

        $datamoney_x['target_uname'] = "@网站管理员@";

        $moneynewid_x = m("member_moneylog")->add($datamoney_x);

        if ($moneynewid_x) {

            $bxid = m("member_money")->where("uid={$datamoney_x['uid']}")->save($mmoney_x);

        }


        //更新表
	    $upborrowsql = "update `{$pre}borrow_info` set ";

    	$upborrowsql .= " `repayment_interest`=`repayment_interest`+ {$vo['interest']}";

    	$upborrowsql .= ",`has_out`=`has_out`+ 1";

	    if ($investor_log['has_capital'] == 1) {

	    	$upborrowsql .= ",`repayment_money`=`repayment_money`+{$vo['capital']}";

	        $upborrowsql .= ",`borrow_status`=7";

	    }

	    if ($type == 1) {

	        $upborrowsql .= ",`has_pay`={$sort_order}";

	    }



	    $upborrowsql .= " WHERE `id`={$borrow_id}";

//	    echo $upborrowsql;die;

	    $upborrow_res = m()->execute($upborrowsql);

	    $idetail_status=1;

	    $updetail_res = m()->execute("update `{$pre}investor_detail` set `receive_capital`=`capital` ,`receive_chajia`=`chajia` ,`receive_interest`=(`interest`-`interest_fee`),`repayment_time`=" . time() . ", `status`={$idetail_status} WHERE `borrow_id`={$borrow_id} AND `sort_order`={$sort_order} and `interest`!=0");
        $gengxin=true;
	     foreach ($detailList as $v) {
            if($v["interest"]!=0){
                $getInterest = $v['interest'] - $v['interest_fee'];

                $upsql = "update `{$pre}borrow_investor` set ";
                if(floatval($v['capital']) != 0){
                    $upsql .= "`receive_capital`=`receive_capital`+{$v['capital']},";

                }
                if(floatval($getInterest) != 0){
                    $upsql .= "`receive_interest`=`receive_interest`+ {$getInterest},";
                }

                if ($investor_log['has_capital'] == 1) {
                    $upsql.="`chajia`=".$v['chajia'].',';
                    $upsql .= "`status`=5,";
                }
                $upsql .= "`paid_fee`=`paid_fee`+{$v['interest_fee']}";

                $upsql .= " WHERE `id`={$v['invest_id']}";

                if(floatval($v['capital']) != 0||floatval($getInterest) != 0){

                    $upinfo_res = m()->execute($upsql);
                    if(!$upinfo_res){
                        $gengxin=false;
                    }
                }

                if ($upinfo_res) {

                    $accountMoney = m("member_money")->field("money_freeze,money_collect,account_money,money_experience,yubi,yubi_freeze")->find($v['investor_uid']);

                    $datamoney['uid'] = $v['investor_uid'];

                    $datamoney['type'] = "9";

                    $datamoney['affect_money'] = $v['capital'] + $v['interest']+$v["chajia"];

                    $datamoney['account_money'] = $accountMoney['account_money'] + $datamoney['affect_money'];

                    $datamoney['collect_money'] = $accountMoney['money_collect'] - $v['capital'];

                    $datamoney['freeze_money'] = $accountMoney['money_freeze'];

                    $datamoney['experience_money'] = $accountMoney['money_experience'];

                    $datamoney['yubi'] = $accountMoney['yubi'];
                    $datamoney['freeze_yubi'] = $accountMoney['yubi_freeze'];



                    $mmoney['money_freeze'] = $datamoney['freeze_money'];

                    $mmoney['money_collect'] = $datamoney['collect_money'];

                    $mmoney['account_money'] = $datamoney['account_money'];

                    $mmoney['money_experience'] = $datamoney['experience_money'];

                    if($vos["total"]==1){
                        $datamoney['info'] = "会员对{$borrow_name}还款";
                    }else{
                        $datamoney['info'] = "会员对{$borrow_name}第{$sort_order}期还款";
                    }

                    $datamoney['add_time'] = time();

                    $datamoney['add_ip'] = get_client_ip();



                    $datamoney['target_uid'] = $binfo['borrow_uid'];

                    $datamoney['target_uname'] = $b_member['user_name'];



                    $moneynewid = m("member_moneylog")->add($datamoney);

                    if ($moneynewid) {

                        $xid = m("member_money")->where("uid={$datamoney['uid']}")->save($mmoney);

                        if(!$xid){
                            $gengxin=false;
                        }

                    }



                    //mtip("chk16", $v['investor_uid'], ["id"=>$borrow_id,"BORROW_NAME"=>$borrow_name]);

                }else{

                    $upsql = "update `{$pre}borrow_investor` set ";

                    if ($investor_log['has_capital'] == 1) {
                        $upsql .= "`status`=5,";
                    }
                    $upsql .= " WHERE `id`={$v['invest_id']}";
                        $upinfo_res = m()->execute($upsql);
                        if(!$upinfo_res){
                            $gengxin=false;
                        }
                }
            }
//   die;

	     }

         if ($investor_log['has_capital'] == 1) {
             $idata["status"]=5;
             M('borrow_investor')->where("borrow_id=".$binfo["id"])->save($idata);
             //foreach ($detailList as $v) {
         }
//$dd["updetail_res"]=$updetail_res;$dd["upborrow_res"]=$upborrow_res;
	     if ($updetail_res && $gengxin  && $upborrow_res) {

	            //file_put_contents('check1.txt', '11');

	     		$logdata['status']=1;

	     		$investor_log = m("investor_log")->where("borrow_id={$borrow_id} AND sort_order={$sort_order}")->save($logdata);

		        $borrowDetail->commit();

		        $detailListdx = $borrowDetail->field("investor_uid,sum(capital) as benjin,sum(interest) as lixi,sum(chajia) as chajia   ")->where("borrow_id={$borrow_id} AND sort_order={$sort_order}")->group("investor_uid")->select();


		        //select investor_uid,sum(capital) as benjin,sum(interest) as lixi  from lzh_investor_detail where  borrow_id=1455  AND sort_order=1 group by investor_uid
		       	//发送短信
		        foreach ($detailListdx as $v) {
		        	$affect_money = $v['benjin'] + $v['lixi']+$v['chajia'];
		        	mtip("chk16", $v['investor_uid'], ["xiangmu"=>$borrow_name,"qi"=>$sort_order,"shijian"=>date("Y-m-d H:i:s"),"qian"=>$affect_money]);
		        
		        }
		        // file_put_contents('check2.txt', var_export($investor_log, true));

		        //$rs = new BondBehavior();

		        $rs =  D("BondBehavior");

		        //file_put_contents('check3.txt', '33');

		        $rs->cancelBond($borrow_id);

		       // file_put_contents('check4.txt', '44');

		        $_last = true;

		        if ($investor_log['has_capital'] == 1) {

		            $_last = false;

		            $_is_last = lastrepayment($binfo);

		            if ($_is_last) {

		                $_last = true;

		            }

		        }

		        if ($_last === false) {

		           // return "因满标操作未完成，还款操作失败";

		        }

		        $done = true;

		       

		    } else {

		        $borrowDetail->rollback();

		        $done = false;

		    }
		   
		    if($done == true) ajaxmsg();

		    else ajaxmsg("还款失败，请重试或联系客服",0);

	}

	

	public function apprepaymentlist()

    {

 

				               	// <textarea name="info" id="info" rows="" cols=""></textarea>

				               	// <input type="hidden" name="borrow_id" id="borrow_id">

				               	// <input type="hidden" name="invest_orderid" id="invest_orderid">

				               	// <input type="hidden" name="capital" id="capital">

				               	// <input type="hidden" name="has_capital" id="has_capital">

				       

// 		sort_order		              

		$log['borrow_id'] = $borrow_id = $_GET['bid'];	    

        $log['sort_order'] = $invest_orderid= $_GET['sort_order']; 

        //查看项目详情

        $borrowinfo = M('borrow_info')->field(true)->find($borrow_id);

 

        

        $investor_detail = M('investor_detail')->where("borrow_id={$borrow_id} and sort_order={$invest_orderid}")->select();



        $this->assign("bj", array("gt"=>'大于',"eq"=>'等于',"lt"=>'小于'));

        $this->assign("list", $investor_detail); 

		$this->assign("xaction",ACTION_NAME);

        $this->assign("query", http_build_query($search));

		

        $this->display();

        //  var_dump($investor_detail);die;

         //编号投资人投资金额支持比例本金收益 最终收益（含本金）

        $investor_log = M('investor_log')->where("borrow_id={$borrow_id} and sort_order={$invest_orderid}")->find();

       

        // if (!isset($investor_detail)) {

        //     $this->error('请先生成收益明细');

        // }

        // if ($investor_log) {

        //     $this->error('收益申请重复');

        // }

//   var_dump(M('investor_detail')->getlastsql(),1);die;

        //$investor_log_id = m('investor_log')->add($log);

        // if ($investor_log_id) {

        //     memberMoneyLog($borrowinfo['borrow_uid'], 121, 0 - $capital, "对{$borrowinfo['borrow_name']}进行还款申请");

        //     $this->success('操作成功！', '/Member/');

        // } else {

        //     M('investor_detail')->where("borrow_id={$borrow_id} and sort_order={$invest_orderid}")->delete();

        //     $this->error('操作失败！');

        // }

    }

  

	

}