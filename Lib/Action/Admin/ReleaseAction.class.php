<?php
// 全局设置
class ReleaseAction extends ACommonAction
{

    //
    public function _addFilter()
    {
        // $citylist = C("BORROW_CITY");
        $citylist=Array ( 283 => "济南",284 => "青岛",285=> "滨州",286 => "德州" ,287 => "东营" ,288 => "菏泽" ,289 => "济宁" ,290 => "莱芜" ,291 => "聊城" ,292 => "临沂" ,293 => "日照" ,294 => "泰安" ,295 => "威海" ,296 => "潍坊" ,297 =>"烟台" ,298 => "枣庄" ,299 => "淄博" );
    
        $this->assign('city', $citylist);

        $typelist = get_type_leve_list('0', 'Productcategory'); //分级栏目
        $pid      = array();
        foreach ($typelist as $key => $val) {
            $pid[$val["id"]] = $val["type_name"];
        }
        $this->assign('pid', $pid);

        $Bconfig = C('BORROW');
        $qc_info = $Bconfig['QC_INFO'];
        $this->assign('qc_info', $qc_info);
        //return $m;
    }
    //

    public function index()
    {

        $id = intval($_GET['id']);
        if ($_GET['type'] == "subsite") {
            $vo = M('article_area')->find($id);
        } else {
            $vo = M('article')->find($id);
        }

        $this->assign("vo", $vo);
        //left
        $typeid              = $vo['type_id'];
        $listparm['type_id'] = $typeid;
        $listparm['limit']   = 20;
        if ($_GET['type'] == "subsite") {
            $listparm['area_id'] = $this->siteInfo['id'];
            $leftlist            = getAreaTypeList($listparm);
        } else {
            $leftlist = getTypeList($listparm);
        }

        $this->assign("leftlist", $leftlist);
        $this->assign("cid", $typeid);

        if ($_GET['type'] == "subsite") {
            $vop = D('Aacategory')->field('type_name,parent_id')->find($typeid);
            if ($vop['parent_id'] != 0) {
                $this->assign('cname', D('Aacategory')->getFieldById($vop['parent_id'], 'type_name'));
            } else {
                $this->assign('cname', $vop['type_name']);
            }

        } else {
            $vop = D('Acategory')->field('type_name,parent_id')->find($typeid);
            if ($vop['parent_id'] != 0) {
                $this->assign('cname', D('Acategory')->getFieldById($vop['parent_id'], 'type_name'));
            } else {
                $this->assign('cname', $vop['type_name']);
            }

        }
        $this->display();
    }
    public function save()
    {
        
        $rate_lixt = explode("|",$this->glo['rate_lixi']);
        $borrow_duration = explode("|",$this->glo['borrow_duration']);
        $borrow_duration_day = explode("|",$this->glo['borrow_duration_day']);
        $fee_borrow_manage = explode("|",$this->glo['fee_borrow_manage']);
        //相关的判断参数
        $borrow['borrow_type'] = 1;
        $borrow['repayment_type'] = 5;
        $borrow['borrow_money'] = intval($_POST['borrow_money']);
        $_minfo = getMinfo($this->uid,true);
        $_capitalinfo = getMemberBorrowScan($this->uid);
        ///////////////////////////////////////////////////////
        //$borrowNum=M('borrow_info')->field("count(id) as num,sum(borrow_money) as money,sum(repayment_money) as repayment_money")->where("borrow_uid = {$this->uid} AND borrow_status=6 ")->group("borrow_type")->select();
        $borrowDe = array();
        foreach ($borrowNum as $k => $v) {
            $borrowDe[$v['borrow_type']] = $v['money'] - $v['repayment_money'];
        }
        ///////////////////////////////////////////////////
        //echo $borrow['borrow_type'];
        //exit;
        /*if($borrow['borrow_type']==2){//担保标               
            $borrow['reward_vouch_rate'] = floatval($_POST['vouch_rate']);
            $borrow['reward_vouch_money'] = getFloatValue($borrow['borrow_money']*$borrow['reward_vouch_rate']/100,2);
            $borrow['vouch_member'] = text($_POST['vouch_member']);
        }
        */
        /*if($borrow['borrow_type']==3){//秒标                
            $borrow['daishou'] = $_POST['daishou'];
            $borrow['daishou_money'] = $_POST["daishou_money"];
        }*/

        //$borrow['borrow_uid'] = intval($_POST['borrow_uid']);
        $borrow['borrow_duration'] = ($borrow['borrow_type']==3)?1:intval($_POST['borrow_duration']);//秒标固定为一月
        $borrow['borrow_interest_rate'] = floatval($_POST['borrow_interest_rate']);
        if(strtolower($_POST['is_day'])=='yes') $borrow['repayment_type'] = 1;
        elseif($borrow['borrow_type']==3) $borrow['repayment_type'] = 2;//秒标按月还
        else $borrow['repayment_type'] = intval($_POST['repayment_type']);
        
        if($_POST['show_tbzj']==1) $borrow['is_show_invest'] = 1;//共几期(分几次还)
        
        $borrow['total'] = ($borrow['repayment_type']==1)?1:$borrow['borrow_duration'];//共几期(分几次还)
        if($borrow['repayment_type']==5){
            $borrow['total']=1; 
        }
        //echo $borrow['total'];
        //exit;
        $borrow['borrow_status'] = 0;
        $borrow['borrow_lx'] = intval($_POST['borrow_lx']);
        $borrow['borrow_hy'] = intval($_POST['borrow_hy']);
        $borrow['borrow_use'] = intval($_POST['borrow_use']);
        $borrow['borrow_name'] = text($_POST['borrow_name']);
        $borrow['borrow_sccj'] = text($_POST['borrow_sccj']);
        $borrow['add_time'] = time();
        $borrow['collect_day'] = intval($_POST['borrow_time']);
        $borrow['add_ip'] = get_client_ip();
        $borrow['borrow_info'] = stripslashes(htmlspecialchars_decode($_POST['borrow_info']));
        $borrow['borrow_tk'] = stripslashes(htmlspecialchars_decode($_POST['borrow_tk']));
        $borrow['borrow_con'] = stripslashes(htmlspecialchars_decode($_POST['borrow_con']));
        $borrow['company_info'] = stripslashes(htmlspecialchars_decode($_POST['company_info']));
        $borrow['reward_type'] = intval($_POST['reward_type']);
        $borrow['reward_num'] = floatval($_POST["reward_type_{$borrow['reward_type']}_value"]);
        $borrow['borrow_min'] = intval($_POST['borrow_min']);
        $borrow['borrow_max'] = intval($_POST['borrow_max']);
        //借款人
        $borrow_username = empty($_POST['borrow_username']) ? '' : $_POST['borrow_username'];   
            $borrow_member = M('members')->where(array('user_name'=>$borrow_username))->find();
            if(!is_array($borrow_member)||count($borrow_member)<1) {$this->error("借款用户不存在 !");die;}
            
            $borrow['borrow_uid'] = $borrow_member['id'];
        //借款人
        //担保公司
    
        //投资产品
        $borrow['pid'] = intval($_POST['pid']);
        //投资产品
        $borrow['is_houtai'] = text($_POST['is_houtai']);//后台上传
        $borrow['province'] = empty($vminfo['province_now']) ? '0' : $vminfo['province_now'];
        $borrow['city'] = empty($vminfo['city_now']) ? '0' : $vminfo['city_now'];
        $borrow['area'] = empty($vminfo['area_now']) ? '0' : $vminfo['area_now'];
        if($_POST['is_pass']&&intval($_POST['is_pass'])==1) $borrow['password'] = $_POST['password'];
        
        
        if($borrow['repayment_type'] == 1){//按天还
            $fee_rate = (is_numeric($fee_borrow_manage[0]))?($fee_borrow_manage[0]/1000):0;
            $borrow['borrow_fee'] = getFloatValue($fee_rate*$borrow['borrow_money']*$borrow['borrow_duration'],2);
        }else{
            $fee_rate_1=(is_numeric($fee_borrow_manage[1]))?($fee_borrow_manage[1]/1000):0;
            $borrow['borrow_fee']=getFloatValue($borrow['borrow_money']*$fee_rate_1*$borrow['borrow_duration'],2);
        }
        if($borrow['repayment_type']==5){
            $fee_rate = (is_numeric($fee_borrow_manage[0]))?($fee_borrow_manage[0]/100):0.001;
            $borrow['borrow_fee'] = getFloatValue($fee_rate*$borrow['borrow_money']*$borrow['borrow_duration'],2);
        }
        
        if($borrow['borrow_type']==3){//秒还标
            if($borrow['reward_type']>0){
                if($borrow['reward_type']==1) $_reward_money = getFloatValue($borrow['borrow_money']*$borrow['reward_num']/100,2);
                elseif($borrow['reward_type']==2) $_reward_money = getFloatValue($borrow['reward_num'],2);
            }
            $_reward_money =floatval($_reward_money);
            $__reward_money=0;
            $borrow['borrow_fee']=0;
            if(($_minfo['account_money'])<($borrow['borrow_fee']+$_reward_money)) $this->error("众筹您最少需保证您的帐户余额大于等于".($borrow['borrow_fee']+$_reward_money)."元，以确保可以支付投标奖励费用");
        }
        
        $borrow['borrow_video'] = text($_POST['borrow_video']);
        $img="";
        $bigimg="";
        if(!empty($_FILES['borrow_img']['name'])){
            $this->fix = false;
            $this->saveRule = date("YmdHis",time()).rand(0,1000)."_{$this->uid}";
            $this->savePathNew = 'UF/Uploads/borrowimg/';
            $info = $this->CUpload();
            $img = $info[0]['savepath'].$info[0]['savename'];
            $bigimg = $info[1]['savepath'].$info[1]['savename'];
        }
        
        $borrow['borrow_img']=$img;
        $borrow['borrow_img_big']=$bigimg;
        //投标上传图片资料（暂隐）
        
        foreach($_POST['swfimglist'] as $key=>$v){
            if($key>10) break;
            $row[$key]['img'] = substr($v,1);
            $row[$key]['info'] = $_POST['picinfo'][$key];
        }
        $borrow['updata']=serialize($row);
        
        $borrow['auto_info'] = $this->glo['ttxf_auto_all'];
        
        $borrow['p_auto_info'] = $this->glo['ttxf_auto_p'];
        // echo "<pre>";print_r($borrow);exit;
        $newid = M("borrow_info")->add($borrow);
        
        // echo M("borrow_info")->getlastsql();die;
        // print_r($borrow);
        //if($newid)echo "Yes";else echo "No";
        //exit;
        if($newid){
              $this->success("众筹发布成功，网站会尽快初审", __APP__ . "/admin/borrow/waitverify");}
        else {$this->error("发布失败，请先检查是否完成了个人详细资料然后重试");}
        
    }

    //swf上传图片
    public function swfUpload()
    {
        if ($_POST['picpath']) {
            $imgpath = substr($_POST['picpath'], 1);
            if (in_array($imgpath, $_SESSION['imgfiles'])) {
                unlink(C("WEB_ROOT") . $imgpath);
                $thumb = get_thumb_pic($imgpath);
                $res   = unlink(C("WEB_ROOT") . $thumb);
                if ($res) {
                    $this->success("删除成功", "", $_POST['oid']);
                } else {
                    $this->error("删除失败", "", $_POST['oid']);
                }

            } else {
                $this->error("图片不存在", "", $_POST['oid']);
            }
        } else {
            $this->savePathNew     = C('HOME_UPLOAD_DIR') . 'Product/';
            $this->thumbMaxWidth   = C('PRODUCT_UPLOAD_W');
            $this->thumbMaxHeight  = C('PRODUCT_UPLOAD_H');
            $this->saveRule        = date("YmdHis", time()) . rand(0, 1000);
            $info                  = $this->CUpload();
            $data['product_thumb'] = $info[0]['savepath'] . $info[0]['savename'];
            if (!isset($_SESSION['count_file'])) {
                $_SESSION['count_file'] = 1;
            } else {
                $_SESSION['count_file']++;
            }

            $_SESSION['imgfiles'][$_SESSION['count_file']] = $data['product_thumb'];
            echo "{$_SESSION['count_file']}:" . __ROOT__ . "/" . $data['product_thumb']; //返回给前台显示缩略图
        }
    }
}
