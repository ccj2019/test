<?php
// 本类由系统自动生成，仅供测试用途
class BondAction extends MCommonAction {

        public $Bond;

        public function __construct()
        {
            parent::__construct();
            D("BondBehavior");
            $this->Bond  = new BondBehavior($this->uid);
        }
        /**
        * 债权转让默认页
        * 
        */
        public function index()
        {
           $this->display();
        } 
        /**
        * 可流转的标
        * 
        */
        public function change()
        {
           $list = $this->Bond->canTransfer();
           $this->assign('list', $list);
           $data['html'] = $this->fetch();
           exit(json_encode($data));
        }
        public function sellhtml()
        {
            $invest_id = isset($_GET['id'])? intval($_GET['id']):0;
            !$invest_id && ajaxmsg(L('parameter_error'),0);
            $info = $this->Bond->countBond($invest_id);
            $this->assign('info', $info);
            $datag = get_global_setting();
            $this->assign('Bond_fee', $datag['Bond_fee']);
            $this->assign('invest_id', $invest_id);
            
            $borrow = M('borrow_investor i')
            ->join(C('DB_PREFIX')."borrow_info b ON i.borrow_id = b.id")
            ->field("borrow_name")
            ->where("i.id=".$invest_id)
            ->find();
            $this->assign("borrow_name", $borrow['borrow_name']);
			$this->display();
        }
        public function sell()
        {
            $money = floatval($_POST['money']);
            $paypass = $_POST['paypass'];
            $invest_id = intval($_POST['invest_id']);
            if($money && $paypass && $invest_id){
				
                $result = $this->Bond->sell($invest_id, $money, $paypass);
                if($result ==='TRUE')
                {
                    ajaxmsg('债权转让购买成功');   
                }else{
                    ajaxmsg($result,0);
                }
            }else{
                ajaxmsg('债权转让购买失败',0);
            }
            
            
        }
        /**
        * 进行中的债权
        * 
        */
        public function onBonds()
        {
            $list = $this->Bond->onBonds();
            $this->assign('list', $list);
            $data['html'] = $this->fetch();
            exit(json_encode($data));
        }
        /**
        *    成功的债权
        * 
        */
        public function successClaims()
        {
            $list = $this->Bond->successBond();
            $this->assign('list', $list);
            $data['html'] = $this->fetch();
            exit(json_encode($data));
        }
        /**
        * 已购买的债权
        * 
        */
        public function buybond()
        {
            $list = $this->Bond->buybond();
            $this->assign('list', $list);
            $data['html'] = $this->fetch();
            exit(json_encode($data)); 
        }
        /**
        * 回收中的债权
        * 
        */
        public function onbond()
        {
            $list = $this->Bond->onBond();
            $this->assign('list', $list);
            $data['html'] = $this->fetch();
            exit(json_encode($data));
        }
        
        /**
        * 撤销转让债权ajax
        * 
        */
        public function cancelhtml()
        {
            $invest_id = $_REQUEST['invest_id'];
            $this->assign('invest_id', $invest_id);
            $this->display();
        }
        /**
        *  撤销债权转让
        * 
        */
        public function cancel()
        {
            $invest_id = $_REQUEST['invest_id'];
            $paypsss = strval($_POST['paypass']);
            !$invest_id && ajaxmsg(L('parameter_error'), 0);
        
            if($this->Bond->cancel($invest_id, $paypsss)) {
                ajaxmsg(L('撤销成功'), 1);
            }else{  
                ajaxmsg(L('撤销失败'), 0);
            }
            
        }
        
        /**
        * 取消的债权软让
        * 
        */
        public function cancellist()
        {
            $list = $this->Bond->cancelList();
            $this->assign('list', $list);
            $data['html'] = $this->fetch();
            exit(json_encode($data));
        }
        
        public function  agreement()
        {
            $invest_id = $this->_get('invest_id','trim',0);
            $Bond = M("invest_bond d")
                    ->join(C('DB_PREFIX')."borrow_investor i ON d.invest_id=i.id")
                    ->join(C('DB_PREFIX')."borrow_info b ON i.borrow_id=b.id")
                    ->join(C('DB_PREFIX')."members m ON d.sell_uid=m.id")
                    ->field("d.serialid, d.buy_time,d.transfer_price, d.buy_uid, m.user_name, b.borrow_name, b.id, b.borrow_interest_rate, b.total,d.money, b.has_pay")
                    ->where("d.invest_id={$invest_id}")->find();
            $Bond_total = $this->Bond->getAlsoPeriods($invest_id);
            $this->assign('Bond_total', $Bond_total);
            $buy_user = M("members")->field("user_name")->where("id={$Bond['buy_uid']}")->find();
            $this->assign('buy_user', $buy_user['user_name']);
            $this->assign('Bond', $Bond);
            $this->display();
        }

}