<?php


class ResponseHandler  {
	
	/** ��Կ */
	var $key;
	
	/** Ӧ��Ĳ��� */
	var $parameters;
	
	/** debug��Ϣ */
	var $debugInfo;
	
	function __construct() {
		$this->ResponseHandler();
	}
	
	function ResponseHandler() {
		$this->key = "";
		$this->parameters = array();
		$this->debugInfo = "";
		
		/* GET */
		$receive_get = $this->filterParameter($_GET);
		foreach($receive_get as $k => $v) {
			$this->setParameter($k, $v);
		}
		/* POST */
		$receive_post = $this->filterParameter($_POST);
		foreach($receive_post as $k => $v) {
			$this->setParameter($k, $v);
		}
	}

    /**
     * �����ַ�����
     * @param $parameter
     */
	private function filterParameter($parameter)
	{
		$para = array();
		foreach ($parameter as $key => $value)
		{
			if ('' == $value || 'm' == $key  || 'a' == $key  || 'g' == $key   || 'payid' == $key || '_URL_' == $key) continue;
			else $para[$key] = $value;
		}
		return $para;
	}
		
	/**
	*��ȡ��Կ
	*/
	function getKey() {
		return $this->key;
	}
	
	/**
	*������Կ
	*/	
	function setKey($key) {
		$this->key = $key;
	}
	
	/**
	*��ȡ����ֵ
	*/	
	function getParameter($parameter) {
		return $this->parameters[$parameter];
	}
	
	/**
	*���ò���ֵ
	*/	
	function setParameter($parameter, $parameterValue) {
		$this->parameters[$parameter] = $parameterValue;
	}
	
	/**
	*��ȡ��������Ĳ���
	*@return array
	*/
	function getAllParameters() {
		return $this->parameters;
	}	
	
	/**
	*�Ƿ�Ƹ�ͨǩ��,������:����������a-z����,������ֵ�Ĳ������μ�ǩ����
	*true:��
	*false:��
	*/	
	function isTenpaySign() {
		$signPars = "";
		ksort($this->parameters);
		foreach($this->parameters as $k => $v) {
			if("sign" != $k && "" != $v) {
				$signPars .= $k . "=" . $v . "&";
			}
		}
		$signPars .= "key=" . $this->getKey();
		
		$sign = strtolower(md5($signPars));
		
		$tenpaySign = strtolower($this->getParameter("sign"));
				
		//debug��Ϣ
		$this->_setDebugInfo($signPars . " => sign:" . $sign .
				" tenpaySign:" . $this->getParameter("sign"));
		
		return $sign == $tenpaySign;
		
	}
	
	/**
	*��ȡdebug��Ϣ
	*/	
	function getDebugInfo() {
		return $this->debugInfo;
	}
	
	/**
	*��ʾ���������
	*@param $show_url ��ʾ���������url��ַ,����url��ַ����ʽ(http://www.xxx.com/xxx.php)��
	*/	
	function doShow($show_url) {
		$strHtml = "<html><head>\r\n" .
			"<meta name=\"TENCENT_ONLINE_PAYMENT\" content=\"China TENCENT\">" .
			"<script language=\"javascript\">\r\n" .
				"window.location.href='" . $show_url . "';\r\n" .
			"</script>\r\n" .
			"</head><body></body></html>";
			
		echo $strHtml;
		
		exit;
	}
	
	/**
	 * �Ƿ�Ƹ�ͨǩ��
	 * @param signParameterArray ǩ���Ĳ�������
	 * @return boolean
	 */	
	function _isTenpaySign($signParameterArray) {
	
		$signPars = "";
		foreach($signParameterArray as $k) {
			$v = $this->getParameter($k);
			if("sign" != $k && "" != $v) {
				$signPars .= $k . "=" . $v . "&";
			}			
		}
		$signPars .= "key=" . $this->getKey();
		
		$sign = strtolower(md5($signPars));
		
		$tenpaySign = strtolower($this->getParameter("sign"));
				
		//debug��Ϣ
		$this->_setDebugInfo($signPars . " => sign:" . $sign .
				" tenpaySign:" . $this->getParameter("sign"));
		
		return $sign == $tenpaySign;		
		
	
	}
	
	/**
	*����debug��Ϣ
	*/	
	function _setDebugInfo($debugInfo) {
		$this->debugInfo = $debugInfo;
	}
	
}


?>