<?php
// 本类由系统自动生成，仅供测试用途
class IndexAction extends WCommonAction {	
    function __construct(){
        parent::__construct();
    

        if($this->uid){
            $map["uid"]=$this->uid;
            $map["type"]=1;
            $map["status"]=1;
            $map["fangshi"]=1;
            $carnum=M("car")->where($map)->select();
            $carn=0;
            foreach ($carnum as $k => $v) {
                $carn+=$v["num"];
            }        
        }else{
            $carn=0;
        }
        $this->assign("carnum",$carn);

    }

    public function index(){
   	    $this->assign("dh",1);

		$Bconfig = C("BORROW");
		$per = C('DB_PREFIX');
		$curl = $_SERVER['REQUEST_URI'];
 	   if(is_mobile()==1){
    				 
	 
			echo "<script type='text/javascript'>";

     	    echo "window.location.href='/m/?version=2.7.7';";

			echo "</script>";die;
			// echo $_SERVER['PHP_SELF']; #/PHP/XX.php
		}
		//预热项目
		$pre = C('DB_PREFIX');
		  
        $searchMaps['borrow_status']= array("in",'1,2');
		// $searchMaps['start_time']= array("gt",time());     
		$searchMaps['pid'] = array('neq',4);
        $parm['limit'] = 3;
        $parm['map'] = $searchMaps;
        $parm['orderby']="b.id desc";
        
        $listProduct_yr = getBorrowList($parm);  
        foreach ($listProduct_yr['list'] as $key => $vo) {
            $listProduct_yr['list'][$key]['progress'] = getFloatValue($vo['has_borrow'] / $vo['borrow_money'] * 100, 4);
        }
        $this->assign("listProduct_yr",$listProduct_yr);


        $horder="art_time desc";
        $hlist=M("article")->field('id,title,art_keyword,art_img')->where("type_id=531")->order($horder)->select();
        $this->assign('hlist', $hlist);

        $xlist=M("article")->field('id,type_id,title,art_keyword,art_img')->where("type_id=526")->limit("0,3")->order($horder)->select();
        $this->assign('xlist', $xlist);

        $rlist=M("article")->field('id,type_id,title,art_keyword,art_img')->where("type_id=54")->limit("0,3")->order($horder)->select();
        $this->assign('rlist', $rlist);

        $mlist=M("article")->field('id,type_id,title,art_keyword,art_img')->where("type_id=22")->limit("0,3")->order($horder)->select();
        $this->assign('mlist', $mlist);


        $yzmap["borrow_status"]=array("in",array(1,2,4,6,7));

        $yzz=M("borrow_info")->field("id,borrow_uid,borrow_type,borrow_money,borrow_name")->where($yzmap)->order('id desc')->find();
        if($yzz["borrow_type"]=="1"){
            $yzz["type"]="联合养殖";
            $yzz["entype"]="Combined culture";
        }
        if($yzz["borrow_type"]=="2"){
            $yzz["type"]="联合销售";
            $yzz["entype"]="Joint sale";
        }

        $this->assign('yzz', $yzz);

        //成功项目
        unset($searchMaps['start_time']);
        $searchMaps['borrow_status']= array("in",'6');  
        $parm['limit'] = 3;
        $parm['map'] = $searchMaps;
         $parm['orderby']="b.start_time desc"; 
        $listProduct_suc = getBorrowList($parm);  
        $this->assign("listProduct_suc",$listProduct_suc);
		$glo = array('web_title'=>'我要投资');
    	$this->assign($glo);	

		
		//商城
		$_REQUEST["pid"]=$_REQUEST["pid"]==0?"0":$_REQUEST["pid"];
		$_REQUEST["jifen"]=$_REQUEST["jifen"]==0?"0":$_REQUEST["jifen"];
		$map=array();
		$map['art_set'] = 0;
		if($_REQUEST["type_id"]!=0){
			$map["type_id"]=$_REQUEST["type_id"];	
		} 

		//分页处理
		$field= '*';
		$list['list'] = M('market')->where($map)->limit(3)->order("id DESC")->select();
        $this->assign("bj", array("gt"=>'大于',"eq"=>'等于',"lt"=>'小于'));
        $this->assign("list", $list);


		
		
		//文章
		
        $parmarticle = array(
        'type_id'=> 21,
        'pagesize'=> 7
        );  
        $article['article']['name'] = $nowCategory['type_name'];
        $article['list'] = getArticleList($parmarticle);
        // var_dump($tpl_var['list']['list']); 
      

        $this->assign('article',$article['list']); 
		$this->assign('articlerightList',$article['rightList']);

        //新添加
        $tjgoods=M("market")->where("tuijian=1")->limit('0,3')->select();
        $this->assign('tjgoods',$tjgoods);

        //新添加
        $tjlist=M("market")->where("tuijian=1")->limit('2,10')->select();
        $this->assign('tjlist',$tjlist);


        $danpin=M("market")->where("leixing=1")->limit('0,8')->select();
        $this->assign('danpin',$danpin);

        $tuangou=M("market")->where("leixing=2")->limit('0,6')->select();
        $this->assign('tuangou',$tuangou);

       
        $guanggao=get_ad(42);
        $this->assign('guanggao',$guanggao);

        $tinfo=M("article_category")->field("id,type_name,type_url")->where("id=513")->order("parent_id asc")->find();
        $tinfo=explode(",", $tinfo["type_url"]);
        $this->assign('tinfo',$tinfo);


        //var_dump($guanggao);exit();
	    $this->display(); 
		

        /****************************募集期内标未满,自动流标 新增 2013-03-13****************************/
    }
    public function getimg(){
        $mlist=M("market")->select();
        foreach ($mlist as $k => $v) {
            echo "<img src='https://www.rzmwzc.com/".$v["art_img"]."'>";
        }
    }
    public function shop(){
        //新添加
        $tjgoods=M("market")->where("tuijian=1")->limit('0,4')->select();
        $this->assign('tjgoods',$tjgoods);


        $tlist=M("article_category")->field("id,type_name,type_url")->where("parent_id=513")->order("parent_id asc")->select();

        foreach ($tlist as $k => $v) {
            $tlist[$k]["zlist"]=M("article_category")->where("parent_id=".$v["id"])->select();
        }
        //pre($tlist);exit;

//        foreach ($tlist as $k => $v) {
//            $tlist[$k]["key"]=explode(",", $v["type_url"]);
//        }
        $this->assign('tlist',$tlist);

        $tinfo=M("article_category")->field("id,type_name,type_url")->where("id=513")->order("parent_id asc")->find();
        $tinfo=explode(",", $tinfo["type_url"]);
  // var_dump($tinfo);exit;
        $this->assign('tinfo',$tinfo);


        //分页处理
        import("ORG.Util.Page");

        $type_id=513;
        if($_REQUEST["type_id"]!=0&&$_REQUEST["type_id"]!=513){
            $type_id=$_REQUEST["type_id"];
            $aad=M("article_category")->where("parent_id=".$type_id)->select();
            if($aad){
                $this->assign('type_id',$type_id);
                foreach ($aad as $k=>$v) {
                    $type_id.=",".$v['id'];
                }
            }else{

                $aab=M("article_category")->where("id=".$type_id)->find();
                $this->assign('type_id',$aab["parent_id"]);
                $this->assign('type_ids',$type_id);
            }
            $map["type_id"]=array("in",$type_id);
        }
       // var_dump($map["type_id"]);exit;

        $map["art_set"]=0;
        //模糊查询商品名称
        $name =  text($_REQUEST["name"]);
        //分类
        $key =  text($_REQUEST["keys"]);
        if(!empty($name)&&!empty($key)){
            $map1["title"] = array('like','%'.$name.'%');
            $map2["title"] = array('like','%'.$key.'%');

            $map1['_logic'] = 'or';
            $map1['_complex'] = $map2;
            $map['_complex']=$map1;
        }else{
            if(!empty($name)){
               $map["title"] = array('like','%'.$name.'%');
            }
            if(!empty($key)){
               $map["title"] = array('like','%'.$key.'%');
            }
        }
        $this->assign('name',$name);
        $this->assign('keys',$key);

        // $count = M('market')->where($map)->count('id');
        // $this->assign("count", $count);
        // $p = new Page($count, 15);
       // $page = $p->show("#more");
        //$Lsql = "{$p->firstRow},{$p->listRows}";
        if($type == 'new'){
            $order =" art_time desc";
            $type='new';
        }else if($type == 'hot'){
            $order=" id desc";
            $type='hot';
        }else if($type == 'jifen'){
            $order=" art_jifen desc";
            $type='jifen';
        }else{
            $order=" art_time desc";
            $type='';
        }
        //分页处理
        $field= '*';
        $list = M('market')->field($field)->where($map)->order($order)->select();
        //var_dump(M("market")->getlastsql());exit;
        // $list['page']   =$page; 
        $this->assign("list", $list);
        //$this->assign("pagebar", $page);
        //exit;
        $this->display(); 
    }
    public function test(){
		if(empty($_GET['p'])){
			$_GET['p']=0;
		}else{
			$_GET['p']=$_GET['p']-1;
		}
       	  $invitation = M('members')->field("id,incode")  ->limit($_GET['p'],100) ->select();
		  foreach($invitation as $k=>$v){
			   $add["incode"]=getincode();
			   var_dump($add["incode"],M('members')->where("id=".$v["id"]) ->save($add));
		  }
    }
 
    public function islogin(){
        $session_id=$_REQUEST["session_id"];
        $uid =  S($session_id); 
        if(!empty($uid)){
            writelog($uid);
            $vo = M('members')->field("id,user_name")->find($uid);
            if(is_array($vo)){
                session(array('name'=>'session_id','expire'=>15*3600));
                foreach($vo as $key=>$v){
                    session("u_{$key}",$v);
                }
                $up['uid'] = $vo['id'];
                $up['add_time'] = time();
                $up['ip'] = get_client_ip();
                M('member_login')->add($up);
                // session($session_id, '');  
                ajaxmsg(); 
                exit();
            }else{
                ajaxmsg("",0);
            }
        }else{
             ajaxmsg("",0);
        }
    }
}
