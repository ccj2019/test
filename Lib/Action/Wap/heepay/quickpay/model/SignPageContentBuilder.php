<?php
/* *
 * 功能：支付宝手机网站支付接口(alipay.trade.wap.pay)接口业务参数封装
 * 版本：2.0
 * 修改日期：2016-11-01
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 */


class SignPageContentBuilder
{
    // 商户订单号.
    private $outTradeNo;

    // 商户订单时间
    private $outTradeTime;

    // 商户平台用户唯一标识
    private $merchUserId;

    // 设备信息
    private $deviceInfo;

    private $bizContentarr = array();

    private $bizContent = NULL;

    public function getBizContent()
    {
        if(!empty($this->bizContentarr)){
            $this->bizContent = json_encode($this->bizContentarr,JSON_UNESCAPED_UNICODE);
        }
        return $this->bizContent;
    }

    public function __construct()
    {
        $this->bizContentarr['productCode'] = "productCode";
    }

    public function SignPageContentBuilder()
    {
        $this->__construct();
    }

    public function setOutTradeNo($outTradeNo)
    {
        $this->outTradeNo = $outTradeNo;
        $this->bizContentarr['out_trade_no'] = $outTradeNo;
    }

    public function getOutTradeNo()
    {
        return $this->outTradeNo;
    }

    public function setOutTradeTime($outTradeTime)
    {
        $this->outTradeTime = $outTradeTime;
        $this->bizContentarr['out_trade_time'] = $outTradeTime;
    }

    public function getOutTradeTime()
    {
        return $this->outTradeTime;
    }

    public function setMerchUserId($merchUserId)
    {
        $this->merchUserId = $merchUserId;
        $this->bizContentarr['merch_user_id'] = $merchUserId;
    }

    public function getMerchUserId()
    {
        return $this->merchUserId;
    }

    public function setDeviceInfo($deviceInfo)
    {
        $this->deviceInfo = $deviceInfo;
        $this->bizContentarr['device_info'] = $deviceInfo;
    }

    public function getDeviceInfo()
    {
        return $this->deviceInfo;
    }
}

?>