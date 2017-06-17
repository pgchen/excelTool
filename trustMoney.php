<?php
/// rong
require "db.php";
class trustMoney extends db
{	
	function __construct()
	{
		parent::__construct();
		$this->table        = 'zkb2b_trust_sign';
		$this->memberTable  = 'destoon_member';
		$this->test = true;
		$this->version = '1.0';
		$this->signType = '0';//MD5 1, RSA:1
		$this->_set();
	}

	private function _set()
	{
		if($this->test)
		{//测试
			$this->partner = '100000000015549';//伙伴ID
			$this->key = '9720g8e810da9gbb7750af1fad854232003f6d929769c9cg162c7716899eb8ge';//加密key
			$this->domain = 'http://111.203.228.5:28001/';
			$this->webUrl = 'http://www.zkweb.com/index.php/';
		}else
		{//运营
			$this->partner = '100000000039075';
			$this->key = '4b8f68511c46b9562aac883g02a86cc2ceefgc58bb55b84fbebd9ccbcab0602b';
			$this->domain = 'http://reagw.reapal.com/';
			$this->webUrl = 'http://www.zuanbank.com/';
		}
	}

	public function excel()
	{
		$idTmp = 0;
		$resList = array();
		while (1) {
			$sql = "select * from {$this->table} where itemid > {$idTmp} and status = 1 limit 1 ";
			$this->query($sql);
			$row = $this->getRow();
			if(empty($row)) break;

			$sql = "select * from {$this->memberTable} where username = '{$row['username']}' limit 1 ";
			$this->query($sql);
			$memberRow = $this->getRow();

			$idTmp = $row['itemid'];
			$reqData['contracts'] = $row['contracts'];
			$reqData['queryTime'] = date('Y-m-d H:i:s');
			$this->url = $this->domain.'reagw/agreement/agreeApi.htm';
			$this->service = 'reapal.trust.balanceQuery';
			$res = $this->_run($reqData,true);
			$tmp = stripos($res, 'resData');
			if($tmp !== false)
			{//正确
				$res = explode('=',$res);
				$resData = $res[count($res)-1];
				$resData = json_decode($resData,true);
			}

			echo $idTmp;
			$tmp = array();
			$tmp['userid'] = $memberRow['userid'];
			$tmp['username'] = $memberRow['username'];
			$tmp['truename'] = $memberRow['truename'];
			$tmp['money'] = $memberRow['money'];
			$tmp['trustMoney'] = $resData['totalAmount'];
			$resList['list'][] = $tmp;
			break;
		}
		return $resList;

	}

	private function _run($datas)
	{
		$postData = $this->reqParam($datas);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->url);
		
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
		
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));

		$res = curl_exec( $ch );
		curl_close( $ch );		
		return $res;
	}

	private function reqParam($reqData)
	{
		/*$reqParam  = "?version=$this->version&";
		$reqParam .= "service=$this->service&";
		$reqParam .= "partner=$this->partner&";
		$reqParam .= "signType=$this->signType&";*/
		$reqParam['version'] = $this->version;
		$reqParam['service'] = $this->service;
		$reqParam['partner'] = $this->partner;
		$reqParam['reqData'] = $this->djson_encode($reqData);
		$reqParam['sign'] = $this->getSign($reqParam['reqData'], $this->key);
		$reqParam['signType'] = $this->signType;
		return $reqParam;
	}

	//转换json编码中的中文
	function djson_encode($datas=array())
	{
		$string = json_encode($datas);
		if(! empty($string))
		{
			$string = preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $string);
		}
		return $string;
	}

	private function getSign($content, $key)
	{
		return md5($content.$key);
	}

	

}