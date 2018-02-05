<?php
class Clfunc_GmoPGPayment
{

	private $oOrderInf_ = null;

	private $sUrl_      = 'https://p01.mul-pay.jp/payment/';
	private $sSiteId_   = 'mst2000000671';
	private $sSitePass_ = 'nmf4m5cb';
	private $sShopId_   = '9100029101129';
	private $sShopPass_ = 'xybyy252';

	private $sResult_ = null;
	public $sErrInfo_ = null;

	public function __construct()
	{
		if(constant('CL_ENV') == 'DEVELOPMENT')
		{
			$this->sUrl_      = 'https://pt01.mul-pay.jp/payment/';
			$this->sSiteId_   = 'tsite00013884';
			$this->sSitePass_ = 'thubuhcb';
			$this->sShopId_   = 'tshop00014650';
			$this->sShopPass_ = 'ev3bxhar';
		}
	}

	public function getResult()
	{
		parse_str($this->sResult_,$res);
		return $res;
	}

	public function getErrCode()
	{
		return $this->sErrInfo_;
	}

	public function getOrderInf()
	{
		return $this->oOrderInf_;
	}

/*---------------------------------------------------------------------------*
 * Public Methods
 *---------------------------------------------------------------------------*/
	public function readMember($rsId)
	{
		$url = $this->sUrl_.'SearchMember.idPass';
		$data = array('SiteID'=>$this->sSiteId_,'SitePass'=>$this->sSitePass_);
		$data['MemberID'] = $rsId;
		$rc = $this->_request($url,$data);
		if($rc != 0) return $rc;
		return 0;
	}

	public function addMember($rsId,$rsName)
	{
		$url = $this->sUrl_.'SaveMember.idPass';
		$data = array('SiteID'=>$this->sSiteId_,'SitePass'=>$this->sSitePass_);
		$data['MemberID'] = $rsId;
		$data['MemberName'] = $rsName;
		$rc = $this->_request($url,$data);
		return $rc;
	}

	public function rmMember($rsId)
	{
		$url = $this->sUrl_.'DeleteMember.idPass';
		$data = array('SiteID'=>$this->sSiteId_,'SitePass'=>$this->sSitePass_);
		$data['MemberID'] = $rsId;
		$rc = $this->_request($url,$data);
		return $rc;
	}

	public function upMember($rsId,$rsName)
	{
		$url = $this->sUrl_.'UpdateMember.idPass';
		$data = array('SiteID'=>$this->sSiteId_,'SitePass'=>$this->sSitePass_);
		$data['MemberID'] = $rsId;
		$data['MemberName'] = $rsName;
		$rc = $this->_request($url,$data);
		return $rc;
	}

	public function readCard($rsId)
	{
		$url = $this->sUrl_.'SearchCard.idPass';
		$data = array('SiteID'=>$this->sSiteId_,'SitePass'=>$this->sSitePass_);
		$data['MemberID'] = $rsId;
		$data['SeqMode'] = 0;
		$data['CardSeq'] = 0;
		$rc = $this->_request($url,$data);
		return $rc;
	}

	public function addCard($rsId,$rsCardNo,$rsExpire)
	{
		$url = $this->sUrl_.'SaveCard.idPass';
		$data = array('SiteID'=>$this->sSiteId_,'SitePass'=>$this->sSitePass_);
		$data['MemberID'] = $rsId;
		$data['CardNo'] = $rsCardNo;
		$data['Expire'] = $rsExpire;
		$rc = $this->_request($url,$data);
		return $rc;
	}

	public function rmCard($rsId,$rsCardSeq)
	{
		$url = $this->sUrl_.'DeleteCard.idPass';
		$data = array('SiteID'=>$this->sSiteId_,'SitePass'=>$this->sSitePass_);
		$data['MemberID'] = $rsId;
		$data['CardSeq'] = $rsCardSeq;
		$rc = $this->_request($url,$data);
		return $rc;
	}

	public function upCard($rsId,$rsCardNo,$rsExpire)
	{
		$url = $this->sUrl_.'SaveCard.idPass';
		$data = array('SiteID'=>$this->sSiteId_,'SitePass'=>$this->sSitePass_);
		$data['MemberID'] = $rsId;
		$data['CardSeq'] = 0;
		$data['CardNo'] = $rsCardNo;
		$data['Expire'] = $rsExpire;
		$rc = $this->_request($url,$data);
		return $rc;
	}

	public function readOrder($rsId)
	{
		$url = $this->sUrl_.'SearchTrade.idPass';
		$data = array('ShopID'=>$this->sShopId_,'ShopPass'=>$this->sShopPass_);
		$data['OrderID'] = $rsId;
		$rc = $this->_request($url,$data);
		if($rc != 0) return $rc;
		$this->oOrderInf_ = new Clfunc_GmoPGInfo($this->sResult_);
		return 0;
	}

	public function begin($rsId,$rbCheck,$riAmount=null,$riTax=null)
	{
		$url = $this->sUrl_.'EntryTran.idPass';
		$data = array('ShopID'=>$this->sShopId_,'ShopPass'=>$this->sShopPass_);
		$data['OrderID'] = $rsId;
		if ($rbCheck)
		{
			$data['JobCd'] = 'CHECK';
		}
		else
		{
			$data['JobCd'] = 'CAPTURE';
			$data['Amount'] = $riAmount;
			$data['Tax'] = $riTax;
		}
		$rc = $this->_request($url,$data);
		return $rc;
	}

	public function checktran($rsOrderId,$rsCardNo,$rsCardExpire,$rsSeqCode)
	{
		// 取引登録
		$rc = $this->begin($rsOrderId,true);
		if($rc != 0) return $rc;
		$res = $this->getResult();
		$acsId = $res['AccessID'];
		$acsPs = $res['AccessPass'];
		// 決済実行
		$url = $this->sUrl_.'ExecTran.idPass';
		$data = array('SiteID'=>$this->sSiteId_,'SitePass'=>$this->sSitePass_);
		$data['AccessID']     = $acsId;
		$data['AccessPass']   = $acsPs;
		$data['OrderID']      = $rsOrderId;
		$data['CardNo']       = $rsCardNo;
		$data['Expire']       = $rsCardExpire;
		$data['SecurityCode'] = $rsSeqCode;
		$rc = $this->_request($url,$data);
		return $rc;
	}

	public function tran($rsOrderId,$rsMemberId,$rsSeqCode,$riAmount,$riTax)
	{
		// 取引登録
		$rc = $this->begin($rsOrderId,false,$riAmount,$riTax);
		if($rc != 0) return $rc;
		$res = $this->getResult();
		$acsId = $res['AccessID'];
		$acsPs = $res['AccessPass'];
		// 決済実行
		$url = $this->sUrl_.'ExecTran.idPass';
		$data = array('SiteID'=>$this->sSiteId_,'SitePass'=>$this->sSitePass_,'MemberID'=>$rsMemberId);
		$data['AccessID'] = $acsId;
		$data['AccessPass'] = $acsPs;
		$data['OrderID'] = $rsOrderId;
		$data['Method'] = 1;
		$data['CardSeq'] = 0;
		$data['SecurityCode'] = $rsSeqCode;
		$rc = $this->_request($url,$data);
		return $rc;
	}

/*---------------------------------------------------------------------------*
 * Private Methods
 *---------------------------------------------------------------------------*/
	private function _request($rsUrl,$aData)
	{
		$sData = http_build_query($aData, "", "&");
		$headers = array(
			'User-Agent: PHP/'.phpversion(),
			'Accept-Charset: utf-8;q=0.7,*;q=0.7',
			"Content-Type: application/x-www-form-urlencoded",
			"Content-Length: ".strlen($sData),
		);
		$options = array('http'=>
			array('method'=>'POST','content'=>$sData,'header'=>implode("\r\n",$headers))
		);
		$this->sResult_ = file_get_contents($rsUrl,false,stream_context_create($options));
		$trc = $rsUrl.' '.var_export($aData,true).' '.$this->sResult_;
		Log::info($trc);
		$rc = $this->_getErrorCode($this->sResult_);
		return $rc;
	}

	private function _getErrorCode($rsRes)
	{
		$pos = strpos($rsRes,'ErrCode');
		if($pos === false) return 0;
		$str = substr($rsRes,$pos);
		$infStr = substr($str,strpos($str,'ErrInfo=')+8);
		$infs = explode('|',$infStr);
		$inf = $infs[0];
		$this->sErrInfo_ = $inf;
		if($inf == 'E01390010')
		{ // 指定されたサイトIDと会員IDの会員が既に存在しています。
			return CL_PG_ERR_EXIST;
		}
		else if($inf == 'E01040010')
		{ // 既にオーダーIDが存在しています。
			return CL_PG_ERR_EXIST;
		}
		else if($inf == 'E01390002')
		{ // 指定されたサイトIDと会員IDの会員が存在しません。
			return CL_PG_ERR_NOMEMBER;
		}
		else if($inf == 'E01220001')
		{ // 会員IDが指定されていません。
			return CL_PG_ERR_UNSETMEMBER;
		}
		else if($inf == 'E01240002')
		{ // 指定されたカードが存在しません。
			return CL_PG_ERR_NOCARD;
		}
		else if($inf == 'E01110002')
		{ // 指定されたIDとパスワードの取引が存在しません。
			return CL_PG_ERR_NOENTRY;
		}

		switch($inf)
		{
			case '42G020000': // カード残高が不足しているために、決済を完了する事が出来ませんでした。
			case '42G040000':
			return CL_PG_ERR_C_LACK;
			case '42G030000': // カード限度額を超えているために、決決済を完了する事が出来ませんでした。
			case '42G050000':
			case '42G550000':
			return CL_PG_ERR_C_LIMIT;
			case '42G650000': // カード番号に誤りがあるために、決済を完了する事が出来ませんでした。
			return CL_PG_ERR_C_NUMBER;
			case '42G830000': // 有効期限に誤りがあるために、決済を完了する事が出来ませんでした。
			return CL_PG_ERR_C_TIME;
			case '42G440000': // セキュリティコードに誤りがあるために、決済を完了する事が出来ませんでした。
			case '42G450000': // セキュリティコードが空のために、決済を完了する事が出来ませんでした。
			return CL_PG_ERR_C_SEQCODE;
			case '42G120000': // このカードでは取引をする事が出来ません。
			case '42G220000':
			case '42G300000':
			case '42G540000':
			case '42G560000':
			case '42G600000':
			case '42G610000':
			case '42G670000':
			case '42G950000':
			case '42G960000':
			case '42G970000':
			case '42G980000':
			case '42G990000':
			return CL_PG_ERR_C_FAILD;
			case '42G670000': // 商品コードに誤りがあるために、決済を完了する事が出来ませんでした。
			return CL_PG_ERR_S_PCODE;
			case '42G680000': // 金額に誤りがあるために、決済を完了する事が出来ませんでした。
			return CL_PG_ERR_S_PRICE;
			case '42G690000': // 税送料に誤りがあるために、決済を完了する事が出来ませんでした。
			return CL_PG_ERR_S_TAX;
			default:
			return CL_PG_ERR_S_ETC;
		}
	}
}
?>
