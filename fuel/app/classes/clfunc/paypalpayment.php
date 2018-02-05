<?php
class Clfunc_PayPalPayment
{
	private $sSECUrl_     = 'https://api-3t.paypal.com/nvp';
	private $sREQUrl_     = 'https://www.paypal.com/cgi-bin/webscr?cmd=_express-checkout&useraction=commit&token=';
	private $sVersion_    = 124;
	private $sApiToken_   = 'Aq1zm2rneWuYQqiw2ZRhn0HFi81yAhcp09UTxMy5Tne8ZYxJ5WaTcfdI';
	private $sUser_       = 'keiyaku_api1.netman.co.jp';
	private $sPass_       = 'R9TZPKXDVV6E4Q7P';
	private $sMerchantID_ = 'MH79PNYB4H492';

	private $sReturn_   = '';
	private $sCancel_   = '';
	private $sNotify_   = '';

	private $iAmount_   = 0;
	private $sToken_    = '';
	private $sPayerID_  = '';

	private $aTeacher_   = null;
	private $sBNO_       = null;

	public function __construct()
	{
		if(constant('CL_ENV') == 'DEVELOPMENT')
		{
			$this->sSECUrl_   = 'https://api-3t.sandbox.paypal.com/nvp';
			$this->sREQUrl_   = 'https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_express-checkout&useraction=commit&token=';
			$this->sApiToken_ = 'AmaRhnanYPKcNtVevHkWfXA-4NJwAnMGzrjVVA0GfCH6L0EanEJHtvGj';
			$this->sUser_     = 'sugi_api1.rsn.ne.jp';
			$this->sPass_     = 'LLTT4WA8XY6Z9S5J';
			$this->sMerchantID_ = 'QWBZWRJYYA87G';
		}

		$this->sReturn_ = CL_URL.DS.'t/payment/ppsuccess';
		$this->sCancel_ = CL_URL.DS.'t/payment/ppcancel';
		$this->sNotify_ = CL_URL.DS.'t/payment/ppnotify';
	}

	public function setAmount($iAmt = 0)
	{
		$this->iAmount_ = $iAmt;
	}
	public function setReturnParam($sParam = '')
	{
		$this->sReturn_ .= DS.$sParam;
	}
	public function setToken($sToken = '')
	{
		$this->sToken_ = $sToken;
	}
	public function setPayerID($sID = '')
	{
		$this->sPayerID_ = $sID;
	}
	public function setTeacher($aT = '')
	{
		$this->aTeacher_ = $aT;
	}
	public function setBNO($sB = '')
	{
		$this->sBNO_ = $sB;
	}
	public function setReturn($sURL = '')
	{
		$this->sReturn_ = $sURL;
	}

	public function PaymentCheck($aTeacher = null)
	{
		$POST_DATA = array(
			'USER' => $this->sUser_,
			'PWD' => $this->sPass_,
			'SIGNATURE' => $this->sApiToken_,
			'METHOD' => 'SetExpressCheckout',
			'VERSION' => $this->sVersion_,
			'PAYMENTREQUEST_0_AMT' => $this->iAmount_,
			'PAYMENTREQUEST_0_CURRENCYCODE' => 'JPY',
			'PAYMENTREQUEST_0_PAYMENTACTION' => 'Sale',
			'returnUrl' => $this->sReturn_,
			'cancelUrl' => $this->sCancel_,
			'PAYMENTREQUEST_0_SHIPTONAME' => $aTeacher['ttName'],
			'PAYMENTREQUEST_0_EMAIL' => $aTeacher['ttMail'],
			'PAYMENTREQUEST_0_SHIPTOPHONENUM' => $aTeacher['ttTel'],
			'PAYMENTREQUEST_0_SHIPTOZIP' => '',
			'PAYMENTREQUEST_0_SHIPTOSTATE' => '',
			'PAYMENTREQUEST_0_SHIPTOCITY' => '',
			'PAYMENTREQUEST_0_SHIPTOSTREET' => '',
			'PAYMENTREQUEST_0_SHIPTOSTREET2' => '',
			'PAYMENTREQUEST_0_SHIPTOCOUNTRYCODE' => '',
			'NOSHIPPING' => 1,
		);

		try
		{
			# PayPal決済開始API (SetExpressCeckout) 実行
			$curl = curl_init($this->sSECUrl_);
			curl_setopt($curl, CURLOPT_POST, TRUE);
			curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($POST_DATA));
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);

			$output = curl_exec($curl);
			parse_str($output,$res);

			if (isset($res['ACK']) && $res['ACK'] == 'Failure')
			{
				\Log::error('PayPalPayment::PaymentCheck - '.$res['L_ERRORCODE0'].' - '.$res['L_LONGMESSAGE0']);
				throw new \Exception($res['L_LONGMESSAGE0'],$res['L_ERRORCODE0']);
			}

			$sToken = $res['TOKEN'];

			# PayPal決済処理開始
			Response::redirect($this->sREQUrl_.$sToken);
		}
		catch(\Exception $e)
		{
			throw $e;
		}

		return;
	}

	public function PaymentStart()
	{
		$POST_DATA = array(
			'USER' => $this->sUser_,
			'PWD' => $this->sPass_,
			'SIGNATURE' => $this->sApiToken_,
			'METHOD' => 'DoExpressCheckoutPayment',
			'VERSION' => $this->sVersion_,
			'PAYMENTREQUEST_0_AMT' => $this->iAmount_,
			'PAYMENTREQUEST_0_CURRENCYCODE' => 'JPY',
			'PAYMENTREQUEST_0_PAYMENTACTION' => 'Sale',
			'TOKEN' => $this->sToken_,
			'PAYERID' => $this->sPayerID_,
		);

		try
		{
			# PayPal決済実行API (DoExpressCheckoutPayment) 実行
			$curl = curl_init($this->sSECUrl_);
			curl_setopt($curl, CURLOPT_POST, TRUE);
			curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($POST_DATA));
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);

			$output = curl_exec($curl);
			parse_str($output,$res);

			if ($res['PAYMENTINFO_0_ERRORCODE'] != 0)
			{
				\Log::error('PayPalPayment::PaymentStart - '.$res['PAYMENTINFO_0_ERRORCODE'].' - '.$res['PAYMENTINFO_0_LONGMESSAGE']);
				throw new \Exception($res['PAYMENTINFO_0_LONGMESSAGE'],$res['PAYMENTINFO_0_ERRORCODE']);
			}

			return $res['PAYMENTINFO_0_TRANSACTIONID'];
		}
		catch(\Exception $e)
		{
			throw $e;
		}

		return;
	}

	public function ButtonCreate()
	{
		$var = array(
			'business' => $this->sMerchantID_,
			'paymentaction' => 'sale',
			'currency_code'=>'JPY',
			'subtotal' => $this->iAmount_,
			'invoice' => $this->sBNO_,
			'return' => $this->sReturn_,
			'cancel_return' => $this->sCancel_,
			'buyer_email' => $this->aTeacher_['ttMail'],
			'logoText'  => CL_SITENAME,
			'logoImage' => CL_URL.\Asset::get_file('logo.png', 'img'),
			'logoImagePosition' => 'center',
			'showHostedThankyouPage' => 'false',
//			'notify_url' => $this->sNotify_,
		);


		$POST_DATA = array(
			'USER' => $this->sUser_,
			'PWD' => $this->sPass_,
			'SIGNATURE' => $this->sApiToken_,
			'METHOD' => 'BMCreateButton',
			'VERSION' => $this->sVersion_,
			'BUTTONCODE' => 'TOKEN',
			'BUTTONTYPE' => 'PAYMENT',
			'BUTTONIMAGEURL' => 'https://www.paypal.com/en_US/i/btn/btn_billing.gif',
		);
		$i = 1;
		foreach ($var as $n => $v)
		{
			$POST_DATA['L_BUTTONVAR'.$i] = $n.'='.$v;
			$i++;
		}

		try
		{
			# PayPalボタン生成API (BMCreateButton) 実行
			$curl = curl_init($this->sSECUrl_);
			curl_setopt($curl, CURLOPT_POST, TRUE);
			curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($POST_DATA));
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);

			$output = curl_exec($curl);
			parse_str($output,$res);

			if (isset($res['ACK']) && $res['ACK'] == 'Failure')
			{
				\Log::error('PayPalPayment::PaymentCheck - '.$res['L_ERRORCODE0'].' - '.$res['L_LONGMESSAGE0']);
				throw new \Exception($res['L_LONGMESSAGE0'],$res['L_ERRORCODE0']);
			}

			return $res['EMAILLINK'];
		}
		catch(\Exception $e)
		{
			throw $e;
		}

		return;
	}




}