<?php
/**
 * 汇付宝接口公共参数封装
 *
 * @author auto create
 * @since 1.0, 2022-08-26
 */
class HeepayTradeRequest
{
	private $method;
	private $version;
	private $merchId;
	private $timestamp;
	private $bizContent;
	private $apiParas = array();


	public function setBizContent($bizContent)
    	{
    		$this->bizContent = $bizContent;
    		$this->apiParas["biz_content"] = $bizContent;
    	}

    	public function getBizContent()
    	{
    		return $this->bizContent;
    	}

	public function setApiMethodName($method)
	{
		$this->method = $method;
	}

	public function getApiMethodName()
	{
		return $this->method;
	}

	public function setApiVersion($version)
	{
		$this->version = $version;
	}

	public function getApiVersion()
	{
		return $this->version;
	}

	public function setMerchId($merchId)
	{
		$this->merchId=$merchId;
	}

	public function getMerchId()
	{
		return $this->merchId;
	}

	public function setTimestamp($timestamp)
	{
		$this->timestamp=$timestamp;
	}

	public function getTimestamp()
	{
		return $this->timestamp;
	}

    public function getApiParas()
    {
        return $this->apiParas;
    }
}
