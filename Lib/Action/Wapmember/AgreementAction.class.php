<?php

// 本类由系统自动生成，仅供测试用途

class AgreementAction extends MCommonAction {

	var $mypdf=NULL;

	var $pdfPath=NULL;

	public function index(){

	   

		$Model = new Model(); // 实例化一个model对象 没有对应任何数据表

	 $is_hetong=$_GET['is_hetong'];

	 if(!isset($is_hetong)&empty($is_hetong)){

	 	$is_hetong=0;

	 }

	$p=intval($_GET['p']);

	if($p<=0){

		$p=1;

	}

	//分页处理

	$pagesize=10;

	  $z=$pagesize*($p-1);

	  $sql="SELECT Count(*)  FROM lzh_borrow_info A JOIN lzh_borrow_investor B  on A.id = B.borrow_id WHERE (A.borrow_status in (6,7)) and ";

	  if($is_hetong==1){

    	$sql.="(B.borrow_uid=".$this->uid.") or ";

    }else if($is_hetong==0){

		$sql.="(B.add_time>1548664779) and ";

}

	 $sql.=	"(B.investor_uid =".$this->uid.") ";

	 if($is_hetong!=3){

	  $sql.=" and (B.is_hetong=".$is_hetong.')';

	 }

	 

$count=$Model->query($sql);

// var_dump($count);

//die;	 

$sql="SELECT B.borrow_id,B.id as id,A.borrow_name,B.is_hetong  FROM lzh_borrow_info A JOIN lzh_borrow_investor B  on A.id = B.borrow_id WHERE (A.borrow_status in (6,7)) and " ;

if($is_hetong==1){

	$sql.="(B.borrow_uid=".$this->uid.") or ";

}else if($is_hetong==0){

	 $sql.="(B.add_time>1548664779) and ";

}

//echo 1;die;

$sql.=" (B.investor_uid =".$this->uid.")";

$sql.="  and (B.is_hetong <>-1)";

if($is_hetong!=3){

	$sql.=" and (B.is_hetong=".$is_hetong.')';

 }

 	$sql.=" ORDER BY  update_time DESC ,  B.id DESC ";

//and (B.is_hetong=".$is_hetong.") 

 	$sql.= "limit ".$z.",".$pagesize;

//echo $Model->getlastsql();die;

// echo $sql;die;

$list=$Model->query($sql);

 // echo $Model->getlastsql().'(1)';die;

		$zong=$count[0]['Count(*)'];

		$page=  ceil($zong/$pagesize);

		$srt= '<div class="navslis">   <a href="/Wapmember/Agreement/?p=1&is_hetong='.$is_hetong.'">首页</a> ';

		if($page<2){

			$pagez=$page;

		}else{

			$pagez=2;

		}



				$z=$p-1;

				//var_dump($z);

				$lt=$p+1;

				if($z>0){

					$srt.=' <a href="/Wapmember/Agreement/?p='.$z.'&is_hetong='.$is_hetong.'" class="nl">上一页</a>    &nbsp;';

					

				}

				$srt.='<span class="current">'.$p.'</span>  &nbsp;';

				$p++; 

					for($i=1;$i<=$pagez;$i++){

						$xiaz=$p++;

						if($xiaz<=$page){

					    	$srt.='<a href="/Wapmember/Agreement?p='.$xiaz.'&is_hetong='.$is_hetong.'">'.$xiaz.'</a>  &nbsp;';

						}else{

							break;

						}

					}

				

				if($lt<$page){

					$srt.='<a href="/Wapmember/Agreement/?p='.$lt.'&is_hetong='.$is_hetong.'" class="nl">下一页</a>    &nbsp;';

				}

		$srt.='<a href="/Wapmember/Agreement?p='.$page.'&is_hetong='.$is_hetong.'">尾页</a></div>';



	 	$this->assign('list',$list);

	 	$this->assign('srt',$srt); 

	 	$this->assign('is_hetong',$is_hetong); 		

		$this->assign('p',intval($_GET['p'])); 



		$this->display();

		die;

	}

    public function qianshu(){

        $uid=$this->uid;

        $Model = new Model(); // 实例化一个model对象 没有对应任何数据表

        $id=$_GET['id'];

        $borrow_investor=M('borrow_investor')->find($id);

        if($borrow_investor['borrow_uid'] != $uid and $borrow_investor['investor_uid'] != $uid) die;

        $sql="SELECT B.*,A.borrow_name,A.borrow_interest_rate,A.templateid,A.start_time,A.lead_time,A.yifang_company_name,A.yifang_name,A.borrow_money,A.yifang_xinyongdaima FROM lzh_borrow_info A JOIN lzh_borrow_investor B  on A.id = B.borrow_id WHERE  B.id=".$id."  limit  1";

        $vm =$Model->query($sql);

        $me = M('member_info')->where("uid=".$borrow_investor["investor_uid"]) ->find();

        $meyi = M('member_info')->where("uid=".$borrow_investor["borrow_uid"]) ->find();

        header("Content-Type: text/html;charset=utf-8");

        //获取token

        $globalz=M('global')->select();

        foreach($globalz as $k=>$v){

            $global[$v['code']]=$v['text'];

        }

        //获取token

        //$datass['appId']='2018050916380600068';

        //$datass['appKey']='JM34AbbcRI9VzQ';

        $datass['appId']=$global['appid'];

        $datass['appKey']=$global['appkey'];

        /* 获取长效令牌 */

        $token = yunhetong_login("https://api.yunhetong.com/api/auth/login",$datass['appId'],$datass['appKey']);

        $zz=round((int)$vm[0]['investor_capital']/(int)$vm[0]['borrow_money']*100,2);

        $lead_time=date("Y-m-d H:i:s",$vm[0]["start_time"]);

        if(!empty($borrow_investor["contractid"])&&$borrow_investor["contractid"]!='0'){

            $contractId=$borrow_investor["contractid"];

        }else{

            /***************************************************************************/
            $murl = "https://api.yunhetong.com/api/contract/templateContract";

            $mdata['contractTitle']=$vm[0]['borrow_name'];

            $mdata['templateId']=empty($vm[0]['templateid'])?"TEM1005147":$vm[0]['templateid'];//TEM1001739

            $mdata['contractNo']="t".time();

            ////乙方公司 : $(yifang_company_name)

            //乙方代表人 : $(yifang_name)

            //乙方信用代码 : $(yifang_xinyongdaima) -->

            $cont=[

                '${deal_name}'=>$vm[0]["borrow_name"],

                '${bianhao}'=>$vm[0]["id"],

                '${jiafang}'=>$me["real_name"],

                '${idno}'=>$me["idcard"],

                '${bingfang}'=>$global["bingfang"],

                '${yifang_company_name}'=>$meyi["company_name"],

                '${yifang_name}'=>$meyi["real_name"],

                '${yifang_xinyongdaima}'=>$meyi["company_idcard"],

                '${riqi}'=>date("Y-m-d H:i:s",time()),

                '${bingfang_xinyongdaima}'=>$global["bingfang_xinyongdaima"],

                '${total_money}'=>$vm[0]["borrow_money"],

                '${load_money}'=>$vm[0]["investor_capital"],

                '${baifenbi}'=>"$zz",

                '${end_time}'=>"$lead_time"

            ];

            $mdata['contractData']=$cont;

            $mres = url_request_json($mdata,$murl,$token);

            writeLog($mres);

            $mrs = json_decode($mres,true);

            if($mrs['code']=='200'){

                $contractId=numberchange($mrs['data']['contractId']);

            }

            /***************************************************************************/

            M('borrow_investor')->where("id=".$borrow_investor["id"]) ->save(['contractid'=>$contractId]);

        }

        $vmv = M('borrow_investor')->where("borrow_id=".$vm[0]["id"])->find();

        $sign = M('members')->where("id=".$vm[0]["borrow_uid"])->find();

        //添加甲方签署者

        $datacs1["idType"]=0;

        $datacs1["contractId"]=$contractId;

        $datacs1["signerId"]=$me['signerid'];//$signerId;

        $datacs1["signPositionType"]=1;//签署的定位方式：0=关键字定位，1=签名占位符定位，2=签署坐标

        $datacs1["positionContent"]= "jia_sign";//坐标位置为第 20 页（34，57）;//对应定位方式的内容，如果用签名占位符 定位可以传多个签名占位符，并以分号隔开,最多 20 个;如果用签署坐标定位， 则该参数包含三个信息：“页面,x 轴坐标,y 轴坐标”（如 20,30,49）

        $vaz=contract_signer("https://api.yunhetong.com/api/contract/signer",$datacs1,$token);

        //添加丙方签署者

        $datacs2["idType"]=0;

        $datacs2["contractId"]=$contractId;

        $datacs2["signerId"]=$global['signerid'];

        $datacs2["signPositionType"]=1;//签署的定位方式：0=关键字定位，1=签名占位符定位，2=签署坐标

        $datacs2["positionContent"]= "bing_sign";//坐标位置为第 20 页（34，57）;//对应定位方式的内容，如果用签名占位符 定位可以传多个签名占位符，并以分号隔开,最多 20 个;如果用签署坐标定位， 则该参数包含三个信息：“页面,x 轴坐标,y 轴坐标”（如 20,30,49）

        $datacs2["signValidateType"]=0;//签署验证方式：0=不校验，1=短信验证;

        $datacs2["signMode"]=1;//印章使用类型（针对页面签署）：0=指定印章， 1=每次绘制

        $signva=contract_signer("https://api.yunhetong.com/api/contract/signer",$datacs2,$token);


        //添加乙方签署者

        ////乙方公司 : $(yifang_company_name)

        //乙方代表人 : $(yifang_name)

        //乙方信用代码 : $(yifang_xinyongdaima) -->

        $datacs3["idType"]=0;

        $datacs3["contractId"]=$contractId;

        $datacs3["signerId"]=$sign['signerid'];

        $datacs3["signPositionType"]=1;//签署的定位方式：0=关键字定位，1=签名占位符定位，2=签署坐标

        $datacs3["positionContent"]= "yi_sign";//坐标位置为第 20 页（34，57）;//对应定位方式的内容，如果用签名占位符

        $datacs3["signValidateType"]=0;//签署验证方式：0=不校验，1=短信验证;

        $datacs3["signMode"]=1;//印章使用类型（针对页面签署）：0=指定印章， 1=每次绘制

        $yic=	contract_signer("https://api.yunhetong.com/api/contract/signer",$datacs3,$token);

        ///甲合同签署

        $datacsz2['idType']=0;//参数类型：0 合同 ID，1 合同自定义编号

        $datacsz2['idContent']=$contractId;//ID 内容

        $datacsz2['signerId']=$me['signerid']; //签署者 ID，可选参数，使用指定签署者的令牌调用 接口时可不传该参数

        $datacsz2['sealClass']=0;//印模 ID，可选参数，不传时使用用户最新印模//签章样式，0=常规样式，2=含摘要样式，3=含签 署时间样式，可选参数，不传时使用常规样式

        $contract_signapi2=contract_sign("https://api.yunhetong.com/api/contract/sign",$datacsz2,$token) ;

        // 丙合同签署

        $contract_sign1['idType']=0;//参数类型：0 合同 ID，1 合同自定义编号

        $contract_sign1['idContent']=$contractId;//ID 内容

        $contract_sign1['signerId']=$global['signerid']; //签署者 ID，可选参数，使用指定签署者的令牌调用 接口时可不传该参数

        $contract_sign1['sealClass']=0;//印模 ID，可选参数，不传时使用用户最新印模//签章样式，0=常规样式，2=含摘要样式，3=含签 署时间样式，可选参数，不传时使用常规样式

        $contract_signapi1=contract_sign("https://api.yunhetong.com/api/contract/sign",$contract_sign1,$token) ;

        //	乙合同签署

        $contract_sign2['idType']=0;//参数类型：0 合同 ID，1 合同自定义编号

        $contract_sign2['idContent']=$contractId;//ID 内容

        $contract_sign2['signerId']=$sign['signerid']; //签署者 ID，可选参数，使用指定签署者的令牌调用 接口时可不传该参数

        $contract_sign2['sealClass']=0;//印模 ID，可选参数，不传时使用用户最新印模//签章样式，0=常规样式，2=含摘要样式，3=含签 署时间样式，可选参数，不传时使用常规样式

        $contract_signapi3=contract_sign("https://api.yunhetong.com/api/contract/sign",$contract_sign2,$token) ;

        if($contract_signapi1==0 || $contract_signapi2==0 || $contract_signapi3==0){

            echo "<script type='text/javascript'>";

            echo "alert('签署失败,请返回');";

            echo "window.history.go(-1);";

            echo "</script>";die;

        }else{

            $va= M('borrow_investor')->where("id=". $id) ->save(['is_hetong'=>1,'update_time'=>time()]);

            echo "<script type='text/javascript'>";

            echo "alert('签署完毕,请返回');";

            echo "location.replace(document.referrer);";

            echo "</script>";die;

        }

    }



	public function xiazai1(){

			$uid=$this->uid;

					$Model = new Model(); // 实例化一个model对象 没有对应任何数据表

		$id=$_GET['id'];

 $sql='SELECT B.borrow_id,B.contractid,A.borrow_name,B.investor_uid,B.borrow_uid  FROM lzh_borrow_info A ';

 $sql.='JOIN lzh_borrow_investor B  on A.id = B.borrow_id';

  $sql.=" WHERE   B.id=". $id." limit 1"; 

 

$vm =$Model->query($sql); 

//var_dump($vm,$sql)		;die;

$me = M('member_info')->where("uid=$uid") ->find();			

               //  ['contractid'=>$contractId]

header("Content-Type: text/html;charset=utf-8");

  //获取token

		$datass['appId']='2018050916380600068'; 

		$datass['appKey']='JM34AbbcRI9VzQ';

		 

    /* 获取长效令牌 */

		$token =	yunhetong_login("https://api.yunhetong.com/api/auth/login",$datass['appId'],$datass['appKey']); 

		

$datadc['idType']=0;//参数类型：0 合同 ID，1 合同自定义编号

$datadc['idContent']=$vm[0]['contractid'];//ID 内容

//	var_dump($datadc['idContent'])	;

   download_contract("https://api.yunhetong.com/api/download/contract",$datadc,$token);







 

 



		

	}

		public function xiazai(){

			$uid=$this->uid;

					$Model = new Model(); // 实例化一个model对象 没有对应任何数据表

		$id=$_GET['id'];

 $sql='SELECT B.borrow_id,B.contractid,A.borrow_name,B.investor_uid,B.borrow_uid  FROM lzh_borrow_info A ';

 $sql.='JOIN lzh_borrow_investor B  on A.id = B.borrow_id';

  $sql.=" WHERE   B.id=". $id." limit 1"; 

 

$vm =$Model->query($sql); 

//var_dump($vm,$sql)		;die;

$me = M('member_info')->where("uid=$uid") ->find();			

               //  ['contractid'=>$contractId]

header("Content-Type: text/html;charset=utf-8");

  //获取token

	  		$globalz=M('global')->select();

			 

			foreach($globalz as $k=>$v){

				$global[$v['code']]=$v['text'];

			}

		//	var_dump($global);die;

		 

  //获取token

//		$datass['appId']='2018050916380600068'; 

//		$datass['appKey']='JM34AbbcRI9VzQ';

$datass['appId']=$global['appid']; 

 		$datass['appKey']=$global['appkey'];

		 

    /* 获取长效令牌 */

		$token =	yunhetong_login("https://api.yunhetong.com/api/auth/login",$datass['appId'],$datass['appKey']); 

		

$datadc['idType']=0;//参数类型：0 合同 ID，1 合同自定义编号

$datadc['idContent']=$vm[0]['contractid'];//ID 内容

	var_dump($datadc['idContent'])	;

   download_contract("https://api.yunhetong.com/api/download/contract",$datadc,$token);







 

 



		

	}

	

	

	public function _MyInit(){

		$this->pdfPath = C("APP_ROOT").'Lib/Tcpdf/';

		require $this->pdfPath.'config/lang/eng.php';

		require $this->pdfPath.'tcpdf.php';

		// create new PDF document

		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		

		// set document information

		$pdf->SetCreator(PDF_CREATOR);

		$pdf->SetAuthor(str_replace("http://","",C('WEB_URL')));

		$pdf->SetTitle('借款合同');

		$pdf->SetSubject('借款合同');

		$pdf->SetKeywords('借款, 合同');

		

		// set default header data

		//页头

		// $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "", str_replace("http://","",C('WEB_URL')));

		$pdf->SetHeaderData('', '', "", str_replace("http://","",C('WEB_URL')));

		

		// set header and footer fonts



		$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));

		$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

		

		// set default monospaced font

		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		

		//set margins



		/*$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);

		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);

		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);*/

		

		$pdf->SetMargins(11, 20, 11);

		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);

		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);



		//set auto page breaks

		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		

		//set image scale factor

		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

		

		//set some language-dependent strings

		$pdf->setLanguageArray($l);

		

		// ---------------------------------------------------------

		

		// set font

		//$pdf->SetFont('droidsansfallback', '', 10);		

		$pdf->SetFont('cid0cs', '', 10);		

		

		// add a page

		$pdf->AddPage();





		$this->mypdf = $pdf;

	}

	

    public function downfile(){

		$per = C('DB_PREFIX');

		$borrow_config = require C("APP_ROOT")."Conf/borrow.php";

		$invest_id=intval($_GET['id']);

		//$borrow_id=intval($_GET['id']);

		$iinfo = M('borrow_investor')->field('borrow_id,investor_capital,investor_interest,deadline,investor_uid')->where("(investor_uid={$this->uid} OR borrow_uid={$this->uid}) AND id={$invest_id}")->find();

		$borrow_id = $iinfo['borrow_id'];



		$this->redirect("borrow",array('id'=>$borrow_id),0);



		$binfo = M('borrow_info')->field('repayment_type,borrow_duration,borrow_uid,borrow_type,full_time,add_time,borrow_interest_rate')->find($iinfo['borrow_id']);

		$mBorrow = M("members m")->join("{$per}member_info mi ON mi.uid=m.id")->field('mi.real_name')->where("m.id={$binfo['borrow_uid']}")->find();

		$mInvest = M("members m")->join("{$per}member_info mi ON mi.uid=m.id")->field('mi.real_name')->where("m.id={$iinfo['investor_uid']}")->find();

		if(!is_array($iinfo)||!is_array($binfo)||!is_array($mBorrow)||!is_array($mInvest)) exit;

		

		$type = $borrow_config['REPAYMENT_TYPE'];

		//echo $binfo['repayment_type'];

		$binfo['repayment_name'] = $type[$binfo['repayment_type']];

		$iinfo['repay'] = getFloatValue(($iinfo['investor_capital']+$iinfo['investor_interest'])/$binfo['borrow_duration'],2);

		//print_r($type);

		$this->assign('iinfo',$iinfo);

		$this->assign('binfo',$binfo);

		$this->assign('mBorrow',$mBorrow);

		$this->assign('mInvest',$mInvest);

		$detail_list = M('investor_detail')->field(true)->where("invest_id={$invest_id}")->select();

		$this->assign("detail_list",$detail_list);

		// 后加

		/*合同参数*/

		$hetong['bianhao'] = 'JH'.date('Ymd',time()).$invest_id;

		$hetong['xiangmubianhao'] = 'JH'.$iinfo['borrow_id'];

		$hetong['Bconfig'] =require C("APP_ROOT")."Conf/borrow_config.php";

		/*担保*/

		$this->assign($hetong);





		

		$html = $this->fetch('index');

		

		$this->mypdf->writeHTML($html, true, false, true, false, '');

		//$this->mypdf->MultiCell(0, 5, "ssssssssssssssssssssssssssssssss", 0, 'J', 0, 2, '', '', true, 0, false);		

		

		//路径,x坐标,y坐标,图片宽度,图片高度（''表示自适应）,网址,

		//$mask = $this->mypdf->Image($this->pdfPath.'images/alpha.png', 10, 10, 10, '', '', '', '', false, 100, '', true);

		//$this->mypdf->Image($this->pdfPath.'images/image_with_alpha.png', 70, 120, 60, 60, '', '', '', false, 10, '', true, $mask);//出图的,放在后面图就在上层，放在前面图就在下层



		// ---------------------------------------------------------

		

		//Close and output PDF document

		$this->mypdf->Output('jiedaihetong.pdf', 'I');

		

    }

    public function _cny_map_unit($list,$units) {

        $ul=count($units);

        $xs=array();

        foreach (array_reverse($list) as $x) {

            $l=count($xs);

            if ($x!="0" || !($l%4)) $n=($x=='0'?'':$x).($units[($l-1)%$ul]);

            else $n=is_numeric($xs[0][0])?$x:'';

            array_unshift($xs,$n);

        }

        return $xs;

    }

    public function cny($ns) {

        static $cnums=array("零","壹","贰","叁","肆","伍","陆","柒","捌","玖"),

            $cnyunits=array("圆","角","分"),

            $grees=array("拾","佰","仟","万","拾","佰","仟","亿");

        list($ns1,$ns2)=explode(".",$ns,2);

        $ns2=array_filter(array($ns2[1],$ns2[0]));

        $ret=array_merge($ns2,array(implode("", self::_cny_map_unit(str_split($ns1),$grees)),""));

        $ret=implode("",array_reverse(self::_cny_map_unit($ret,$cnyunits)));

        return str_replace(array_keys($cnums),$cnums,$ret);

    }

    public function borrow(){

		$per = C('DB_PREFIX');

		$borrow_config = require C("APP_ROOT")."Conf/borrow.php";

		//$invest_id=intval($_GET['id']);





		$borrow_id=intval($_GET['id']);

		//$iinfo = M('borrow_investor')->field('borrow_id,investor_capital,investor_interest,deadline,investor_uid')->where("(investor_uid={$this->uid} OR borrow_uid={$this->uid}) AND id={$invest_id}")->find();

		$iinfo = M('borrow_investor i')->join("{$per}members m ON m.id=i.investor_uid")->join("{$per}member_info mi ON mi.uid=i.investor_uid")->field('i.*,mi.real_name,mi.idcard,m.user_name')->where("i.borrow_id={$borrow_id} and uid=".$this->uid)->order("i.id DESC")->find();



		$binfo = M('borrow_info')->find($borrow_id);

               // echo M('borrow_info')->getlastsql();exit;

		$mBorrow = M("members m")->join("{$per}member_info mi ON mi.uid=m.id")->field('m.*,mi.real_name')->where("m.id={$binfo['borrow_uid']}")->find();

		//$mInvest = M("members m")->join("{$per}member_info mi ON mi.uid=m.id")->field('mi.real_name')->where("m.id={$iinfo['investor_uid']}")->find();



		if(!is_array($iinfo)||!is_array($binfo)||!is_array($mBorrow)) exit;

		

		$type = $borrow_config['REPAYMENT_TYPE'];

		//echo $binfo['repayment_type'];

		$binfo['repayment_name'] = $type[$binfo['repayment_type']];

		//$iinfo['repay'] = getFloatValue(($iinfo['investor_capital']+$iinfo['investor_interest'])/$binfo['borrow_duration'],2);

		//print_r($type);

                $member = M("members m")->join('lzh_member_info mi on m.id = mi.uid')->field('m.user_name,mi.idcard')->where("m.id={$this->uid}")->find();

                

		$this->assign('member',$member);

                $paying_list = getMemberThisBorrow($binfo['borrow_uid'],200,$borrow_id); //当前标的情况

		$this->assign("paying_list",$paying_list);



		$this->assign('iinfo',$iinfo);

                $this->assign('money',  $this->cny($iinfo['investor_capital']));

		$this->assign('binfo',$binfo);

		$this->assign('mBorrow',$mBorrow);

		//$this->assign('mInvest',$mInvest);



		$detail_list = M('investor_detail')->field(true)->where("invest_id={$invest_id}")->select();

		$this->assign("detail_list",$detail_list);







		/*合同参数*/

		$hetong['bianhao'] = 'HT_BORROW_'.date('Ymd',time()).$borrow_id;

		$hetong['xiangmubianhao'] = '#'.$borrow_id;

		$hetong['Bconfig'] =require C("APP_ROOT")."Conf/borrow.php";

		/*担保*/

		$this->assign($hetong);

		

		//dump($binfo);

		$html = $this->fetch('borrow');

		//echo $html;die;

		/*

		require APP_PATH."Common/word.php";



		$word=new word();

		$word->start();

		echo $html;

		$file_name = "HT_".$hetong['bianhao']."_".$hetong['xiangmubianhao']."_".time().".doc";

		$wordname=APP_PATH."hetong/".$file_name;

		$word->save($wordname);//保存word并且结束



		header('Content-type: application/msword');//输出的类型  

		header('Content-Disposition: attachment; filename="'.$file_name.'"'); //下载显示的名字,注意格式  

		readfile($wordname); 

		die;*/



		//$this->display('agreement');die;

		//$this->display('index');die;

		//$html = $this->fetch('index');

		

		$this->mypdf->writeHTML($html, true, false, true, false, '');

		$pdf = $this->mypdf;



		//$this->mypdf->MultiCell(0, 5, "担保人", 0, 'J', 0, 2, '', '', true, 0, false);		

	

		

		//路径,x坐标,y坐标,图片宽度,图片高度（''表示自适应）,网址,

		//$mask = $this->mypdf->Image($this->pdfPath.'../../../public/jinhang/images/z/z.jpg', 10, 10, 10, '', '', '', '', false, 100, '', true);

		//$this->mypdf->Image($this->pdfPath.'../../../public/jinhang/images/z/z.jpg', 70, 120, 60, 60, '', '', '', false, 10, '', true, $mask);//出图的,放在后面图就在上层，放在前面图就在下层





		//$pdf->Write(0, 'XObject Templates', '', 0, 'C', 1, 0, false, false, 0);



		/*

		 * An XObject Template is a PDF block that is a self-contained

		 * description of any sequence of graphics objects (including path

		 * objects, text objects, and sampled images).

		 * An XObject Template may be painted multiple times, either on

		 * several pages or at several locations on the same page and produces

		 * the same results each time, subject only to the graphics state at

		 * the time it is invoked.

		 */





		// start a new XObject Template and set transparency group option

		$template_id = $pdf->startTemplate(60, 60, true);

		// create Template content

		// ...................................................................

		//Start Graphic Transformation

		$pdf->StartTransform();

		// set clipping mask

		//$pdf->StarPolygon(30, 30, 29, 10, 3, 0, 1, 'CNZ');



		// draw jpeg image to be clipped

		$pdf->Image($this->pdfPath.'../../../public/tnt/hetong.png', 0, 0, 60, 60, '', '', '', true, 72, '', false, false, 0, false, false, false);



		//Stop Graphic Transformation

		$pdf->StopTransform();







		$pdf->SetXY($pdf->GetX(), $pdf->GetY());

		//$pdf->SetFont('times', '', 40);

		$pdf->SetTextColor(255, 0, 0);

		// print a text

		//$pdf->Cell(60, 60, '担保人：', 0, 0, 'C', false, '', 0, false, 'T', 'M');

		// ...................................................................

		// end the current Template

		$pdf->endTemplate();

		// print the selected Template various times using various transparencies



		/*$pdf->SetAlpha(0.4);

		$pdf->printTemplate($template_id, 15, 50, 20, 20, '', '', false);



		$pdf->SetAlpha(0.6);

		$pdf->printTemplate($template_id, 27, 62, 40, 40, '', '', false);



		$pdf->SetAlpha(0.8);

		$pdf->printTemplate($template_id, 55, 85, 60, 60, '', '', false);



		$pdf->SetAlpha(1);

		$pdf->printTemplate($template_id, 95, 125, 80, 80, '', '', false);*/



		$pdf->SetAlpha(0.9);

		$pdf->printTemplate($template_id, 27, 90, 60, 40, '', '', false);



		// $html = '<style type="text/css">

		// 	.footer_div{position: relative;}

		// 	.footer{line-height: 5px;}

		// 	.footer_img{margin-top: -100px;}

		// </style>

		// <div class="footer_div">

		// <table class="footer" >

		// 	<tr><td>平台方：'.$this->glo['pintai_name'].'</td></tr>

		// 	<tr><td>地址：'.$this->glo['pintai_addr'].'</td></tr>	

		// </table>

		// </div>';

				$pdf->SetAlpha(1);



			//==	$this->mypdf->writeHTML($html, true, false, true, false, '');

		// ---------------------------------------------------------

		

		//Close and output PDF document

		$this->mypdf->Output($hetong['bianhao'].'.pdf', 'I');

    }









    //创建个人用户

    public function create(){

    	$per = C('DB_PREFIX');

    	$tokenurl = "https://api.yunhetong.com/api/auth/login";

		$tokendata['appId']='2018050315414700052';

		$tokendata['appKey']='JFLcCaZ1yZrk';

		$tokendata['signerId']='';

		$res = url_request_token($tokendata,$tokenurl);

		if($res['code'] == '200'){

    		$murl = "https://api.yunhetong.com/api/user/person";

			$minfo = M("members m")->join("{$per}member_info mi ON mi.uid=m.id")->field('m.*,mi.real_name,mi.idcard')->where("m.id={$this->uid}")->find();

			$mdata['userName']=$minfo['real_name'];

			$mdata['identityRegion']=0;

			$mdata['certifyNum']=$minfo['idcard'];

			$mdata['phoneRegion']=0;

			$mdata['phoneNo']=$minfo['user_phone'];

			$mdata['caType']='B2';

			$mres = url_request_json($mdata,$murl,$res['token']);

			writeLog($mres);

			$mrs = json_decode($mres,true);

			if($mrs['code']=='200'){

				$m['signerId']=$mrs['data']['signerId'];

				$newid=M('members')->where("id={$this->uid}")->save($m);

				if($newid){

					$this->redirect("personmoulage",0);

					// $this->success('创建成功',__APP__."/member");

				}

			}else{

				$this->success($mrs['msg'],__APP__."/member");

			}

		}else{

			$this->success($res['msg'],__APP__."/member");

		}



    }

    //创建企业用户

    public function company(){

    	$per = C('DB_PREFIX');

    	$tokenurl = "https://api.yunhetong.com/api/auth/login";

		$tokendata['appId']='2018050315414700052';

		$tokendata['appKey']='JFLcCaZ1yZrk';

		$tokendata['signerId']='';

		$res = url_request_token($tokendata,$tokenurl);

		if($res['code'] == '200'){

    		$murl = "https://api.yunhetong.com/api/user/company";

			// $minfo = M("members m")->join("{$per}member_info mi ON mi.uid=m.id")->field('m.*,mi.real_name,mi.idcard')->where("m.id={$this->uid}")->find();

			// $mdata['userName']=$minfo['real_name'];

			// $mdata['certifyType']=1;

			// $mdata['certifyNum']=$minfo['idcard'];

			$mdata['userName']='自贡市雷利农业科技有限公司';

			$mdata['certifyType']=1;

			$mdata['certifyNum']='915103007866707853';

			

			$mdata['phoneNo']='15222222222';

			$mdata['caType']='B2';

			$mres = url_request_json($mdata,$murl,$res['token']);

			writeLog($mres);

			$mrs = json_decode($mres,true);

			// var_dump($mrs);

			if($mrs['code']=='200'){

				$m['signerId']=$mrs['data']['signerId'];

				// $newid=M('members')->where("id={$this->uid}")->save($m);

				$newid=M('members')->where("id=1506")->save($m);

				if($newid){

					$this->success('创建成功',__APP__."/member");

				}

			}else{

				$this->success($mrs['msg'],__APP__."/member");

			}

		}else{

			$this->success($res['msg'],__APP__."/member");

		}

    }

    //创建个人印模

    public function personmoulage(){

    	$per = C('DB_PREFIX');

    	$minfo = M("members m")->join("{$per}member_info mi ON mi.uid=m.id")->field('m.*,mi.real_name,mi.idcard')->where("m.id={$this->uid}")->find();

    	$tokenurl = "https://api.yunhetong.com/api/auth/login";

		$tokendata['appId']='2018050315414700052';

		$tokendata['appKey']='JFLcCaZ1yZrk';

		$tokendata['signerId']=$minfo['signerId'];

		$res = url_request_token($tokendata,$tokenurl);

		if($res['code'] == '200'){

			$murl = "https://api.yunhetong.com/api/user/personMoulage";

			

			$mdata['signerId']=$minfo['signerId'];

			$mdata['borderType']='B1';

			$mdata['fontFamily']='F1';

			$mres = url_request_json($mdata,$murl,$res['token']);

			writeLog($mres);

			$mrs = json_decode($mres,true);

			if($mrs['code']=='200'){

				$m['moulageId']=$mrs['data']['moulageId'];

				$newid=M('members')->where("id={$this->uid}")->save($m);

				if($newid){

					$this->success('创建成功',__APP__."/member");

				}

			}else{

				$this->success($mrs['msg'],__APP__."/member");

			}

		}else{

			$this->success($res['msg'],__APP__."/member");

		}

    }

    //创建企业印模 深圳七彩誉互联网金融服务有限公司  ["moulageId"]=> int(562) ["signerId"]=> int(454)

    public function companyMoulage(){

    	$per = C('DB_PREFIX');

    	$minfo = M("members m")->join("{$per}member_info mi ON mi.uid=m.id")->field('m.*,mi.real_name,mi.idcard')->where("m.id=1506")->find();

    	$tokenurl = "https://api.yunhetong.com/api/auth/login";

		$tokendata['appId']='2018050315414700052';

		$tokendata['appKey']='JFLcCaZ1yZrk';

		$tokendata['signerId']=$minfo['signerId'];

		// $tokendata['signerId']='454';

		$res = url_request_token($tokendata,$tokenurl);

		if($res['code'] == '200'){

			$murl = "https://api.yunhetong.com/api/user/companyMoulage";

			

			$mdata['signerId']=$minfo['signerId'];

			// $mdata['signerId']='454';

			$mdata['styleType']='1';

			$mdata['textContent']='';

			$mdata['keyContent']='5103010000417';

			$mres = url_request_json($mdata,$murl,$res['token']);

			writeLog($mres);

			$mrs = json_decode($mres,true);

			// var_dump($mrs);

			if($mrs['code']=='200'){

				$m['moulageId']=$mrs['data']['moulageId'];

				$newid=M('members')->where("id=1506")->save($m);

				if($newid){

					$this->success('创建成功',__APP__."/member");

				}

			}else{

				$this->success($mrs['msg'],__APP__."/member");

			}

		}else{

			$this->success($res['msg'],__APP__."/member");

		}

    }

    //生成合同

    public function templatecontract(){

    	$per = C('DB_PREFIX');

    	$minfo = M("members m")->join("{$per}member_info mi ON mi.uid=m.id")->field('m.*,mi.real_name,mi.idcard')->where("m.id={$this->uid}")->find();

    	$tokenurl = "https://api.yunhetong.com/api/auth/login";

		$tokendata['appId']='2018050315414700052';

		$tokendata['appKey']='JFLcCaZ1yZrk';

		$tokendata['signerId']=$minfo['signerId'];

		$res = url_request_token($tokendata,$tokenurl);



		if($res['code'] == '200'){

			$murl = "https://api.yunhetong.com/api/contract/templateContract";

			$invest_id=intval($_GET['id']);

			$iinfo = M('borrow_investor')->field('borrow_id,investor_capital,investor_interest,deadline,investor_uid,add_time')->where("investor_uid={$this->uid} AND id={$invest_id}")->find();

			// var_dump(M('borrow_investor')->getlastsql());die;

			$binfo = M('borrow_info')->find($iinfo['borrow_id']);

			$mBorrow = M("members m")->join("{$per}member_info mi ON mi.uid=m.id")->field('m.*,mi.real_name')->where("m.id={$binfo['borrow_uid']}")->find();

			$mInvest = M("members m")->join("{$per}member_info mi ON mi.uid=m.id")->field('mi.real_name')->where("m.id={$iinfo['investor_uid']}")->find();

			if(!is_array($iinfo)||!is_array($binfo)||!is_array($mBorrow)||!is_array($mInvest)) exit;



			$mdata['contractTitle']=$binfo['borrow_name'];

			$mdata['templateId']='TEM1002104';

			$mdata['contractNo']='TNTZCHPT'.$invest_id;



			$cont=array(

				'${investor}'					=>				$mBorrow["user_name"],

				// '${busername}'					=>				$minfo["user_name"],

				'${borrowor}'					=>				$minfo["real_name"],

				'${bidcard}'					=>				$minfo["idcard"],

				'${borrow_sccj}'				=>				$binfo["borrow_sccj"],

				'${borrowname}'					=>				$binfo["borrow_name"],

				'${borrow_duration}'			=>				$binfo["borrow_duration"],

				'${borrow_interest_rate}'		=>				$binfo["borrow_interest_rate"],

				'${investor_capital}'			=>				$iinfo["investor_capital"],

				'${money}'						=>				$this->cny($iinfo["investor_capital"]),

				'${year}'						=>				date("Y",$iinfo["add_time"]),

				'${month}'						=>				date("m",$iinfo["add_time"]),

				'${day}'						=>				date("d",$iinfo["add_time"])

			);

			

			$mdata['contractData']=$cont;



			$mres = url_request_json($mdata,$murl,$res['token']);

			writeLog($mres);

			$mrs = json_decode($mres,true);



			if($mrs['code']=='200'){

				$m['contractId']=numberchange($mrs['data']['contractId']);

				$newid=M('borrow_investor')->where("id={$invest_id}")->save($m);

				if($newid){

					$this->success('生成合同成功',__APP__."/member");

				}

			}else{

				$this->success($mrs['msg'],__APP__."/member");

			}

		}else{

			$this->success($res['msg'],__APP__."/member");

		}

    }

    //添加签署者

    public function addsigner(){

    	$per = C('DB_PREFIX');

    	$minfo = M("members m")->join("{$per}member_info mi ON mi.uid=m.id")->field('m.*,mi.real_name,mi.idcard')->where("m.id={$this->uid}")->find();

    	$tokenurl = "https://api.yunhetong.com/api/auth/login";

		$tokendata['appId']='2018050315414700052';

		$tokendata['appKey']='JFLcCaZ1yZrk';

		$tokendata['signerId']=$minfo['signerId'];

		$res = url_request_token($tokendata,$tokenurl);

		if($res['code'] == '200'){

			$murl = "https://api.yunhetong.com/api/contract/signer";

			$invest_id=intval($_GET['id']);

			$iinfo = M('borrow_investor')->field('borrow_id,investor_capital,investor_interest,deadline,investor_uid,add_time,contractId')->where("investor_uid={$this->uid} AND id={$invest_id}")->find();

			$binfo = M('borrow_info')->find($iinfo['borrow_id']);

			$mBorrow = M("members m")->join("{$per}member_info mi ON mi.uid=m.id")->field('m.*,mi.real_name')->where("m.id={$binfo['borrow_uid']}")->find();

			if(!is_array($iinfo)) exit;



			$mdata['idType']=0;

			$mdata['idContent']=$iinfo['contractId'];

			$cont=array(

				array(

					'signerId'					=>				$mBorrow["signerId"],

					'signPositionType'			=>				0,

					'positionContent'			=>				'甲方合同盖章',

					'signValidateType'			=>				0,

				),

				array(

					'signerId'					=>				$minfo["signerId"],

					'signPositionType'			=>				0,

					'positionContent'			=>				'乙方合同盖章',

					'signValidateType'			=>				0,

				),

				array(

					'signerId'					=>				'454',

					'signPositionType'			=>				0,

					'positionContent'			=>				'丙方合同盖章',

					'signValidateType'			=>				0,

				),



			);

			writeLog($cont);

			$mdata['signers']=$cont;

			

			$mres = url_request_json($mdata,$murl,$res['token']);

			writeLog($mres);

			$mrs = json_decode($mres,true);

			if($mrs['code']=='200'){

				$m['step']=4;

				$newid=M('borrow_investor')->where("id={$invest_id}")->save($m);

				$this->redirect("qcsign",array('id'=>$invest_id),0);

				// $this->success('合同签署成功',__APP__."/member");

			}else{

				$this->success($mrs['msg'],__APP__."/member");

			}

		}else{

			$this->success($res['msg'],__APP__."/member");

		}

    }

    //合同签署

    public function sign(){

    	$per = C('DB_PREFIX');

    	$minfo = M("members m")->join("{$per}member_info mi ON mi.uid=m.id")->field('m.*,mi.real_name,mi.idcard')->where("m.id={$this->uid}")->find();

    	$tokenurl = "https://api.yunhetong.com/api/auth/login";

		$tokendata['appId']='2018050315414700052';

		$tokendata['appKey']='JFLcCaZ1yZrk';

		$tokendata['signerId']=$minfo['signerId'];

		$res = url_request_token($tokendata,$tokenurl);

		if($res['code'] == '200'){

			$murl = "https://api.yunhetong.com/api/contract/sign";

			$invest_id=intval($_GET['id']);

			$iinfo = M('borrow_investor')->field('borrow_id,investor_capital,investor_interest,deadline,investor_uid,add_time,contractId')->where("investor_uid={$this->uid} AND id={$invest_id}")->find();

			$binfo = M('borrow_info')->find($iinfo['borrow_id']);

			$mBorrow = M("members m")->join("{$per}member_info mi ON mi.uid=m.id")->field('m.*,mi.real_name')->where("m.id={$binfo['borrow_uid']}")->find();

			if(!is_array($iinfo)) exit;



			$mdata['idType']=0;

			$mdata['idContent']=$iinfo['contractId'];

			

			$mres = url_request_json($mdata,$murl,$res['token']);

			writeLog($mres);

			$mrs = json_decode($mres,true);

			if($mrs['code']=='200'){

				$m['is_sign']=1;

				$m['step']=3;

				$newid=M('borrow_investor')->where("id={$invest_id}")->save($m);

				$this->success('合同签署成功',__APP__."/member");

			}else{

				$this->success($mrs['msg'],__APP__."/member");

			}

		}else{

			$this->success($res['msg'],__APP__."/member");

		}

    }

    //深圳七彩誉互联网金融服务有限公司合同签署

    public function qcsign(){

    	$per = C('DB_PREFIX');



    	$tokenurl = "https://api.yunhetong.com/api/auth/login";

		$tokendata['appId']='2018050315414700052';

		$tokendata['appKey']='JFLcCaZ1yZrk';

		$tokendata['signerId']='454';

		$res = url_request_token($tokendata,$tokenurl);

		if($res['code'] == '200'){

			$murl = "https://api.yunhetong.com/api/contract/sign";

			$invest_id=intval($_GET['id']);

			$iinfo = M('borrow_investor')->field('contractId')->where("id={$invest_id}")->find();

			if(!is_array($iinfo)) exit;



			$mdata['idType']=0;

			$mdata['idContent']=$iinfo['contractId'];

			

			$mres = url_request_json($mdata,$murl,$res['token']);

			writeLog($mres);

			$mrs = json_decode($mres,true);

			

			if($mrs['code']=='200'){

				$m['step']=1;

				$newid=M('borrow_investor')->where("id={$invest_id}")->save($m);

				$this->redirect("jfsign",array('id'=>$invest_id),0);

				// $this->success('添加签署者成功');

				

			}else{

				$this->success($mrs['msg'],__APP__."/member");

			}

		}else{

			$this->success($res['msg'],__APP__."/member");

		}

    }

    //甲方合同签署

    public function jfsign(){

    	$per = C('DB_PREFIX');

    	$invest_id=intval($_GET['id']);

    	$iinfo = M('borrow_investor')->field('borrow_id,investor_capital,investor_interest,deadline,investor_uid,add_time,contractId')->where("id={$invest_id}")->find();

		$binfo = M('borrow_info')->find($iinfo['borrow_id']);

		$mBorrow = M("members m")->join("{$per}member_info mi ON mi.uid=m.id")->field('m.*,mi.real_name')->where("m.id={$binfo['borrow_uid']}")->find();



    	$tokenurl = "https://api.yunhetong.com/api/auth/login";

		$tokendata['appId']='2018050315414700052';

		$tokendata['appKey']='JFLcCaZ1yZrk';

		$tokendata['signerId']=$mBorrow['signerId'];

		$res = url_request_token($tokendata,$tokenurl);

		if($res['code'] == '200'){

			$murl = "https://api.yunhetong.com/api/contract/sign";

			

			if(!is_array($iinfo)) exit;



			$mdata['idType']=0;

			$mdata['idContent']=$iinfo['contractId'];

			

			$mres = url_request_json($mdata,$murl,$res['token']);

			writeLog($mres);

			$mrs = json_decode($mres,true);

		

			if($mrs['code']=='200'){

				$m['step']=2;

				$newid=M('borrow_investor')->where("id={$invest_id}")->save($m);

				$this->redirect("sign",array('id'=>$invest_id),0);

				// $this->success('合同签署成功');

			}else{

				$this->success($mrs['msg'],__APP__."/member");

			}

		}else{

			$this->success($res['msg'],__APP__."/member");

		}

    }



    //合同存证

    public function cz(){

    	$per = C('DB_PREFIX');

    	$minfo = M("members m")->join("{$per}member_info mi ON mi.uid=m.id")->field('m.*,mi.real_name,mi.idcard')->where("m.id={$this->uid}")->find();

    	$tokenurl = "https://api.yunhetong.com/api/auth/login";

		$tokendata['appId']='2018050315414700052';
		$tokendata['appKey']='JFLcCaZ1yZrk';

		$tokendata['signerId']=$minfo['signerId'];

		$res = url_request_token($tokendata,$tokenurl);

		if($res['code'] == '200'){

			$murl = "https://api.yunhetong.com/api/contract/cz";

			$invest_id=intval($_GET['id']);

			$iinfo = M('borrow_investor')->field('borrow_id,investor_capital,investor_interest,deadline,investor_uid,add_time,contractId')->where("investor_uid={$this->uid} AND id={$invest_id}")->find();



			if(!is_array($iinfo)) exit;



			$mdata['idType']=0;

			$mdata['idContent']=$iinfo['contractId'];

			

			$mres = url_request_json($mdata,$murl,$res['token']);

			writeLog($mres);

			$mrs = json_decode($mres,true);

			var_dump($mrs);

			// if($mrs['code']=='200'){

			// 	$this->success('合同签署成功',__APP__."/member");

			// }else{

			// 	$this->success($mrs['msg'],__APP__."/member");

			// }

		}else{

			$this->success($res['msg'],__APP__."/member");

		}

    }



    //查看合同状态

    public function cstatus(){

    	$per = C('DB_PREFIX');

    	$minfo = M("members m")->join("{$per}member_info mi ON mi.uid=m.id")->field('m.*,mi.real_name,mi.idcard')->where("m.id={$this->uid}")->find();

    	$tokenurl = "https://api.yunhetong.com/api/auth/login";

		$tokendata['appId']='2018050315414700052';

		$tokendata['appKey']='JFLcCaZ1yZrk';

		$tokendata['signerId']=$minfo['signerId'];

		$res = url_request_token($tokendata,$tokenurl);

		if($res['code'] == '200'){

			

			$invest_id=intval($_GET['id']);

			$iinfo = M('borrow_investor')->field('borrow_id,investor_capital,investor_interest,deadline,investor_uid,add_time,contractId')->where("investor_uid={$this->uid} AND id={$invest_id}")->find();



			if(!is_array($iinfo)) exit;





			$murl = "https://api.yunhetong.com/api/contract/status/0/".$iinfo['contractId'];

			$mres = curl_get_https($murl,$res['token']);

			echo "<pre />";

			$mrs = json_decode($mres,true);

			var_dump($mrs);

		}else{

			$this->success($res['msg'],__APP__."/member");

		}

    }

    public function findagree(){

    	$per = C('DB_PREFIX');

    	$minfo = M("members m")->join("{$per}member_info mi ON mi.uid=m.id")->field('m.*,mi.real_name,mi.idcard')->where("m.id={$this->uid}")->find();

    	$tokenurl = "https://api.yunhetong.com/api/auth/login";

		$tokendata['appId']='2018050315414700052';

		$tokendata['appKey']='JFLcCaZ1yZrk';

		$tokendata['signerId']=$minfo['signerId'];

		$res = url_request_token($tokendata,$tokenurl);

		if($res['code'] == '200'){

			

			$invest_id=intval($_GET['id']);

			$iinfo = M('borrow_investor')->field('borrow_id,investor_capital,investor_interest,deadline,investor_uid,add_time,contractId')->where("investor_uid={$this->uid} AND id={$invest_id}")->find();



			if(!is_array($iinfo)) exit;





			$murl = "https://api.yunhetong.com/api/contract/signReport/0/".$iinfo['contractId'];

			$mres = curl_get_https($murl,$res['token']);

			var_dump($mres);

		}else{

			$this->success($res['msg'],__APP__."/member");

		}

    }

    public function download(){

    	$per = C('DB_PREFIX');

    	$minfo = M("members m")->join("{$per}member_info mi ON mi.uid=m.id")->field('m.*,mi.real_name,mi.idcard')->where("m.id={$this->uid}")->find();

    	$tokenurl = "https://api.yunhetong.com/api/auth/login";

		$tokendata['appId']='2018050315414700052';

		$tokendata['appKey']='JFLcCaZ1yZrk';

		$tokendata['signerId']=$minfo['signerId'];

		$res = url_request_token($tokendata,$tokenurl);

		if($res['code'] == '200'){

			

			$invest_id=intval($_GET['id']);

			$iinfo = M('borrow_investor')->field('borrow_id,investor_capital,investor_interest,deadline,investor_uid,add_time,contractId')->where("investor_uid={$this->uid} AND id={$invest_id}")->find();



			if(!is_array($iinfo)) exit;



			$murl = "https://api.yunhetong.com/api/contract/download/0/".$iinfo['contractId'];

			$mres = curl_get_https($murl,$res['token']);

			$mrs = json_decode($mres,true);

			if($mrs['code']=='200'){

				$murls = "https://api.yunhetong.com/api/auth/download/".$mrs['data'];

				// $ress = curl_get_https($murls,$res['token']);

				// var_dump($ress);

				header("Location:".$murls);

			}else{

				$this->success($mrs['msg'],__APP__."/member");

			}

		}else{

			$this->success($res['msg'],__APP__."/member");

		}

    }





























    

}