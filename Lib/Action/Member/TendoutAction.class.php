<?php

// 本类由系统自动生成，仅供测试用途
class TendoutAction extends MCommonAction
{
    public function index()
    {
        
        $strUrl = U("tendout/index").'?1=1';
        $map['investor_uid'] = $this->uid;
        $map['status'] = array('in', '1,4,5,6');
        $borrow_status = '';
        
        if (isset($_GET['key']) && !empty($_GET['key']) ) {
            $map['borrow_name'] =["like","%". $_GET['key']."%"];
            $strUrl .= '&type='.$_GET['type'];
            $this->assign('borrow_name',$_GET["key"]);
        }
        if (isset($_GET['type']) && !empty($_GET['type']) && 0 != $_GET['type']) {
            $map['pid'] = $_GET['type'];
            $strUrl .= '&type='.$_GET['type'];
        }
        $this->assign("type",$_GET["type"]);
        if(isset($_GET['borrow_status'])){
             $map['borrow_status']= $_GET['borrow_status'];
             $borrow_status = $_GET['borrow_status'];
        }

        // if (isset($_GET['borrow_status']) && !empty($_GET['borrow_status']) && 1 == $_GET['borrow_status']) {
        //     $map['borrow_status'] = array('in', '0,2,6');
        // } elseif (isset($_GET['borrow_status']) && !empty($_GET['borrow_status']) && $_GET['borrow_status'] = 2) {
        //     $map['borrow_status'] = array('in', '7');
        // } elseif (isset($_GET['borrow_status']) && !empty($_GET['borrow_status']) && $_GET['borrow_status'] = 0) {
        // }
		$count = M('borrow_investor i') ->where($map)->join("lzh_borrow_info b ON b.id=i.borrow_id")->count('i.id');
        $list = getTenderList2($map, 10);

        $this->assign('count', $count);
        $this->assign('borrow_status', $borrow_status);
        $this->assign('strUrl', $strUrl);
        $this->assign('list', $list['list']);
        $this->assign('pagebar', $list['page']);
        $this->assign('total', $list['total_money']);
        $this->assign('num', $list['total_num']);
        $mdata = M('members')->find($this->uid);
        $this->assign('mdata', $mdata);
 //var_dump( $list );die;
        $this->display();
    }

    public function zhongzhi()
    {
        

        ///$map['status'] = array('in', '1,4,5,6');
   

        import('ORG.Util.Page');
        $map = null;
        if (isset($_GET['key']) && !empty($_GET['key']) ) {
            $map['x.pinlei'] =["like","%". $_GET['key']."%"];
            $this->assign('borrow_name',$_GET["key"]);
        }

        $map['uid'] = $this->uid;
        if($_GET["status"]==''){
            $map["o.status"]=array("in",array(1,2,3,4));
        }else{
            $map["o.status"]=$_GET["status"];
        }
        $this->assign('status',$_GET["status"]);


        $count = m('order_zz o')->where($map)->join("lzh_zhongzhi_xx x ON x.id=o.gid")->where($map)->count();
        $num = 10;
        $page = new Page($count,$num);
        foreach($getval as $key=>$val) {
            $page->parameter .= "$key=".urlencode($val).'&';
        }
        $order='o.id desc';
        $show = $page->show();
        $limit = $page->firstRow.','.$page->listRows;

        
        $list = m('order_zz o')->field('o.ordernums,o.status,o.add_time,o.jine,o.pay_way,o.id as oid,x.*,z.name,z.images')->where($map)->join("lzh_zhongzhi_xx x ON x.id=o.gid")->join("lzh_zhongzhi z ON z.id=x.zid")->order($order)->limit($limit)->select();
        
        foreach ($list as $k => $v) {  
            $list[$k]["images"]=explode(",",$v["images"])[0];
        }  
        $this->assign('page',$show);
        $this->assign('list',$list);
        $this->assign('count',$count);
        
        $this->assign('strUrl',$strUrl);
        $this->display();
    }

    //已经分红

    public function borrowbreak()
    {
        $map['borrow_uid'] = $this->uid;
        $map['borrow_status'] = 6;

        if ($_GET['start_time'] && $_GET['end_time']) {
            $_GET['start_time'] = strtotime($_GET['start_time'].' 00:00:00');
            $_GET['end_time'] = strtotime($_GET['end_time'].' 23:59:59');

            if ($_GET['start_time'] < $_GET['end_time']) {
                $map['add_time'] = array('between', "{$_GET['start_time']},{$_GET['end_time']}");
                $search['start_time'] = $_GET['start_time'];
                $search['end_time'] = $_GET['end_time'];
            }
        }
        $map['borrow_status'] = array('gt', 5);
        $list = getBorrowList($map, 10);
        $this->assign('search', $search);
        $this->assign('list', $list['list']);
        $this->assign('pagebar', $list['page']);
        $this->display();
        die;
        $this->display();
        die;

        //exit(json_encode($data));
    }

    public function done()
    {
        $map['borrow_uid'] = $this->uid;
        $map['borrow_status'] = 7;

        if ($_GET['start_time8'] && $_GET['end_time8']) {
            $_GET['start_time8'] = strtotime($_GET['start_time8'].' 00:00:00');
            $_GET['end_time8'] = strtotime($_GET['end_time8'].' 23:59:59');

            if ($_GET['start_time8'] < $_GET['end_time8']) {
                $map['add_time'] = array('between', "{$_GET['start_time8']},{$_GET['end_time8']}");
                $search['start_time8'] = $_GET['start_time8'];
                $search['end_time8'] = $_GET['end_time8'];
            }
        }
        if (isset($_GET['type']) && !empty($_GET['type']) && 0 != $_GET['type']) {
            $map['Borrow.pid'] = $_GET['type'];
            //$Wsql.='AND (b.pid = '.$_GET["type"].')';
        }
        $list = getBorrowList($map, 10);

        $this->assign('search', $search);
        $this->assign('list', $list['list']);
        $this->assign('pagebar', $list['page']);
        //var_dump($list['list']);die;

        $this->display();
    }

    

    public function summary()
    {
        $uid = $this->uid;
        $pre = C('DB_PREFIX');

        $this->assign('dc', M('investor_detail')->where("investor_uid = {$this->uid}")->sum('substitute_money'));
        $this->assign('mx', getMemberBorrowScan($this->uid));
        $this->display();
    }

    public function tending()
    {
        //$map['i.investor_uid'] = $this->uid;
        // $map['i.status'] = 1;
        $map['investor_uid'] = $this->uid;
        $map['status'] = 1;
        //$map['start_time'] = array('gt',time());
        $list = getTenderList($map, 15);

        $this->assign('list', $list['list']);
        $this->assign('pagebar', $list['page']);
        $this->assign('total', $list['total_money']);
        $this->assign('num', $list['total_num']);
        $this->display();
    }

    public function myyuyuelist()
    {
        $map['investor_uid'] = $this->uid;
        $map['status'] = 1;
        $list = getTenderList($map, 15);
        $this->assign('list', $list['list']);
        $this->assign('pagebar', $list['page']);
        $this->assign('total', $list['total_money']);
        $this->assign('num', $list['total_num']);
        $data['html'] = $this->fetch();
        //$this->display("Public:_footer");
        exit(json_encode($data));
    }

    public function guanzhulist()
    {
        $map['investor_uid'] = $this->uid;
        $list = M('pro_guanzhu i')->field('b.*')->join('lzh_borrow_info b ON b.id=i.bid')->where('i.uid='.$this->uid)->select();
        $this->assign('list', $list);
        $this->assign('pagebar', $list['page']);
        $this->assign('total', $list['total_money']);
        $this->assign('num', $list['total_num']);
        $data['html'] = $this->fetch();
        exit(json_encode($data));
    }

    public function pinglunlist()
    {
        $list = M('comment i')->field('i.*,b.borrow_name')->join('lzh_borrow_info b ON b.id=i.tid')->where('i.uid='.$this->uid)->select();
        $this->assign('list', $list);
        $this->assign('pagebar', $list['page']);
        $this->assign('total', $list['total_money']);
        $this->assign('num', $list['total_num']);
        $data['html'] = $this->fetch();
        exit(json_encode($data));
    }

    public function yuetanlist()
    {
        $list = M('comment_yuetan i')->field('i.*,b.borrow_name')->join('lzh_borrow_info b ON b.id=i.tid')->where('i.uid='.$this->uid)->select();
        $this->assign('list', $list);
        $this->assign('pagebar', $list['page']);
        $this->assign('total', $list['total_money']);
        $this->assign('num', $list['total_num']);
        $data['html'] = $this->fetch();
        exit(json_encode($data));
    }

    public function tendbacking()
    {
        //$map['i.investor_uid'] = $this->uid;
        // $map['i.status'] = 4;
        $map['investor_uid'] = $this->uid;
        $map['status'] = 4;
        if ($_GET['start_time2'] && $_GET['end_time2']) {
            $_GET['start_time2'] = strtotime($_GET['start_time2'].' 00:00:00');
            $_GET['end_time2'] = strtotime($_GET['end_time2'].' 23:59:59');

            if ($_GET['start_time2'] < $_GET['end_time2']) {
                $map['add_time'] = array('between', "{$_GET['start_time2']},{$_GET['end_time2']}");
                $search['start_time2'] = $_GET['start_time2'];
                $search['end_time2'] = $_GET['end_time2'];
            }
        }
        $list = getTenderList($map, 15);
        $this->assign('list', $list['list']);
        $this->assign('pagebar', $list['page']);
        $this->assign('total', $list['total_money']);
        $this->assign('alltotal', $list['total_money'] + $list['lixi']);
        $this->assign('num', $list['total_num']);
        $mdata = M('members')->find($this->uid);
        $this->assign('mdata', $mdata);
        $this->display();
    }

    public function tenddone()
    {
        //$map['i.investor_uid'] = $this->uid;
        // $map['i.status'] = array("in","5,6");
        $map['investor_uid'] = $this->uid;
        $map['status'] = array('in', '5,6');
        $list = getTenderList($map, 15);
        $this->assign('list', $list['list']);
        $this->assign('pagebar', $list['page']);
        $this->assign('total', $list['total_money']);
        $this->assign('num', $list['total_num']);
        $this->assign('uid', $this->uid);
        $mdata = M('members')->find($this->uid);
        $this->assign('mdata', $mdata);
        $this->display();
    }

    public function tendbreak()
    {
        $map['d.status'] = array('neq', 0);
        $map['d.repayment_time'] = array('eq', '0');
        $map['d.deadline'] = array('lt', time());
        $map['d.investor_uid'] = $this->uid;

        $list = getMBreakInvestList($map, 15);
        $this->assign('list', $list['list']);
        $this->assign('pagebar', $list['page']);
        $this->assign('total', $list['total_money']);
        $this->assign('num', $list['total_num']);
        //$this->display("Public:_footer");

        $data['html'] = $this->fetch();
        exit(json_encode($data));
    }

    public function tendoutdetail()
    {
        $pre = C('DB_PREFIX');
        $status_arr = array('还未还', '已还完', '已提前还款', '迟还', '网站代还本金', '逾期还款', '', '等待还款');
        $investor_id = (int) ($_GET['id']);
       
        $vo = M('borrow_investor i')->field('bs.money_bonus,i.yubi,b.borrow_name,b.pid,b.borrow_min,i.receive_interest,i.investor_capital,i.receive_interest,i.add_time,b.borrow_status')->join("{$pre}member_bonus bs ON bs.id=i.bonus_id")->join("{$pre}borrow_info b ON b.id=i.borrow_id")->where("i.investor_uid={$this->uid} AND i.id={$investor_id}")->find();
        $vo["yue"]=$vo["investor_capital"]-$vo["yubi"]-$vo["money_bonus"]; 
        if (!is_array($vo)) {
            $this->error('数据有误');
        }
        //var_dump($vo['receive_interest']);
		if($vo['borrow_status']==7){
		   // $lid=M("investor_detail")->where("invest_id={$investor_id}")->find();
		   // $vo['receive_interest']=  empty($lid['receive_interest'])?"0.00":$lid['receive_interest'];
		   // var_dump(M("investor_detail")->getlastsql());die;
           $vo['receive_interest']=$vo["receive_interest"];
		}else{
		   $vo['receive_interest']=  "0.00";
		  
		}
        //var_dump($vo['receive_interest']);
		if(empty($vo["borrow_min"])&&$vo["borrow_min"]==0){
			$vo["borrow_min"]=100;
		}
	    //var_dump($vo['receive_interest']);die;
        $map['invest_id'] = $investor_id;
        $reinterest = M('investor_detail')->where($map)->sum('receive_interest');
        $this->assign('status_arr', $status_arr);
        $this->assign('reinterest', $reinterest);
        $this->assign('name', $vo['borrow_name']);
        $this->assign('vo', $vo);
        
        $this->display();
    }

    public function zdetail()
    {
        $id=$_REQUEST["id"];
        $map["id"]=$id;
        $order=M('order_zz')->field('yijian,id,gid,jifen,uid,num,cell_phone,action,real_name,address,jine,add_time,ordernums,status')->where($map)->find();

        $goods=M("zhongzhi_xx x")->field('x.*,z.images,z.name')->where("x.id=".$order["gid"])->join("lzh_zhongzhi z ON z.id=x.zid")->find();
      
        $goods["imglist"]=explode(",",$goods["images"]);

        $goods["title"]=$goods["pinlei"]."&nbsp;配送".$goods["cishu"]."次/".$goods["guige"]."/".$goods["zhouqi"]."个月";

        if($goods["yijian"]==""){
            $goods["yijian"]="快递名称:快递单号";   
        }

        //var_dump($order);
        $this->assign("goods", $goods); 

        $this->assign("order", $order); 
        
        $this->display();
    }
}
