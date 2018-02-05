<?php
class ClFunc_GmoPGInfo
{
	const UNPROC  = 1;
	const CAPTURE = 2;
	const SALES   = 3;
	const VOID    = 4;
	const SRETURN = 5;

	const JOB_CHECK   = 1;
	const JOB_CAPTURE = 2;
	const JOB_SALES   = 3;
	const JOB_VOID    = 4;
	const JOB_RETURN  = 5;

	public $sOrderId  = null;
	public $iState    = 0;
	public $iJobCode  = 0;
	public $iPrcDate  = 0;
	public $sAcsId    = null;
	public $sAcsPs    = null;
	public $sSiteId   = null;
	public $sMemberId = null;
	public $sItemCode = null;
	public $sCardNo   = null;
	public $sExpire   = null;
	public $iMethod   = 0;
	public $iAmount   = 0;
	public $iTax      = 0;
	public $sTranId   = null;
	public $sApprove  = null;

	public $aVals_iState    = array('','未決済','即時売上','実売上','取消','返品');
	public $aVals_iJobCode  = array('','','','','');
	public $aRVals_iState   = array('UNPROCESSED'=>1,'CAPTURE'=>2,'SALES'=>3,'VOID'=>4,'RETURN'=>5);
	public $aRVals_iJobCode = array('CHECK'=>1,'CAPTURE'=>2,'SALES'=>3,'VOID'=>4,'RETURN'=>5);

	public function __construct($rsResult=null){
		parse_str($rsResult,$res);
		$this->oOrder->sOrderId  = $res['OrderID'];
		$this->oOrder->iState    = $this->oOrder->aRVals_iState[$res['Status']];
		$this->oOrder->iJobCode  = $this->oOrder->aRVals_iJobCode[$res['JobCd']];
		$this->oOrder->iPrcDate  = $res['ProcessDate'];
		$this->oOrder->sAcsId    = $res['AccessID'];
		$this->oOrder->sAcsPs    = $res['AccessPass'];
		$this->oOrder->sSiteId   = $res['SiteID'];
		$this->oOrder->sMemberId = $res['MemberID'];
		$this->oOrder->sItemCode = $res['ItemCode'];
		$this->oOrder->sCardNo   = $res['CardNo'];
		$this->oOrder->sExpire   = $res['Expire'];
		$this->oOrder->iMethod   = $res['Method'];
		$this->oOrder->iAmount   = $res['Amount'];
		$this->oOrder->iTax      = $res['Tax'];
		$this->oOrder->sTranId   = $res['TranID'];
		$this->oOrder->sApprove  = $res['Approve'];
	}

}
