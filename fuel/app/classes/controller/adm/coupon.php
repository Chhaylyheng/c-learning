<?php
class Controller_Adm_Coupon extends Controller_Adm_Base
{
	private $bn = 'coupon';

	private $aCouponBase = array(
		'cpCode'=>null,
		'cpName'=>null,
		'cpDiscount'=>20,
		'infinityRange'=>0,
		'cpTermDate'=>null,
		'cpPaymentType'=>array(),
		'cpRange'=>1,
	);
	private $aCoupon = null;
	private $aPaymentType = null;

	public function before()
	{
		parent::before();

		$this->aPaymentType = \Clfunc_Flag::getPaymentFlag();
		$this->template->set_global('aPaymentType', $this->aPaymentType);
	}

	public function action_index()
	{
		$sTitle = 'クーポン一覧';
		# パンくずリスト生成
		$this->template->set_global('breadcrumbs',array(array('name'=>$sTitle)));
		# ページタイトル生成
		$this->template->set_global('pagetitle',$sTitle);

		# カスタムボタン
		$aCustomBtn = array(
		array(
			'url'  => '/adm/'.$this->bn.'/create',
			'name' => 'クーポンの新規登録',
		),
		);
		$this->template->set_global('aCustomBtn',$aCustomBtn);

		$aCoupons = null;
		$result = Model_Coupon::getCoupon(null,null,array('cpDate'=>'desc'));
		if (count($result))
		{
			$aCoupons = $result->as_array();
		}

		$this->template->content = View::forge('adm/'.$this->bn.'/index');
		$this->template->content->set('aCoupons',$aCoupons);
		$this->template->javascript = array('cl.adm.coupon.js');
		return $this->template;
	}

	public function action_create()
	{
		$view = 'adm/'.$this->bn.'/edit';

		$result = Model_Payment::getPlan(array(array('ptID','=',3)));
		if (!count($result))
		{
			Session::set('SES_ADM_ERROR_MSG','プランが設定されていません。');
			Response::redirect('/adm'.DS.$this->bn);
		}
		$aPlan = $result->as_array('ptID');
		$this->template->set_global('aPlan',$aPlan);

		# タイトル
		$sTitle = 'クーポンの新規作成';
		$this->template->set_global('pagetitle',$sTitle);

		# パンくずリスト生成
		$this->aBread[] = array('link'=>DS.$this->bn,'name'=>'クーポン一覧');
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		if (!Input::post(null,false))
		{
			$data = $this->aCouponBase;
			$data['cpTermDate'] = date('Y/m/d',strtotime('+1month'));
			$data['cpPaymentType'] = \Clfunc_Common::dec2Bits(7);
			$data['error'] = null;
			$this->template->content = View::forge($view,$data);
			$this->template->javascript = array('cl.adm.coupon.js');
			return $this->template;
		}

		$aInput = Input::post();

		$val = Validation::forge();
		$val->add_callable('Helper_CustomValidation');
		$val->add('cpCode', 'クーポンコード')
			->add_rule('required')
			->add_rule('trim')
			->add_rule('exact_length', 10)
			->add_rule('valid_string', array('alpha','uppercase','numeric','utf8'));
		;
		$val->add('cpName', 'クーポン名称')
			->add_rule('required')
			->add_rule('trim')
			->add_rule('max_length', 30)
		;
		$val->add('cpDiscount', '割引率')
			->add_rule('required')
			->add_rule('trim')
			->add_rule('valid_string','numeric')
			->add_rule('numeric_min', 1)
			->add_rule('numeric_max', 100)
		;
		$val->add('cpPaymentType', '支払い条件')
			->add_rule('required')
		;
		if (!$val->run())
		{
			$data = $this->aCouponBase;
			$data = array_merge($data, $aInput);
			$data['error'] = $val->error();
			$this->template->content = View::forge($view,$data);
			$this->template->javascript = array('cl.adm.coupon.js');
			return $this->template;
		}

		$result = Model_Coupon::getCoupon(array(array('cpCode','=',$aInput['cpCode'])));
		if (count($result))
		{
			$aInput['error'] = array('cpCode'=>'指定のクーポンコードは既に利用されています。');
			$this->template->content = View::forge($view,$aInput);
			$this->template->javascript = array('cl.adm.coupon.js');
			return $this->template;
		}

		// 登録データ生成
		$aInput['cpDate'] = date('YmdHis');
		if (isset($aInput['infinityRange']) && $aInput['infinityRange'] == 1)
		{
			$aInput['cpTermDate'] = '9999-12-31';
		}
		unset($aInput['infinityRange']);
		if (isset($aInput['sub_state']))
		{
			unset($aInput['sub_state']);
		}
		$iPType = 0;
		if (isset($aInput['cpPaymentType']))
		{
			foreach ($aInput['cpPaymentType'] as $iV)
			{
				$iPType = ($iPType | (int)$iV);
			}
		}
		$aInput['cpPaymentType'] = $iPType;

		try
		{
			$result = Model_Coupon::insertCoupon($aInput);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_ADM_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		Session::set('SES_ADM_NOTICE_MSG','クーポンを作成しました。【'.$aInput['cpName'].'】');
		Response::redirect('/adm/'.$this->bn);
	}

	public function action_edit($no = null)
	{
		$view = 'adm/'.$this->bn.'/edit';

		$aChk = self::CouponChecker($no);
		if (is_array($aChk))
		{
			Session::set('SES_ADM_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}
		$this->template->set_global('aCoupon',$this->aCoupon);

		$result = Model_Payment::getPlan(array(array('ptID','=',3)));
		if (!count($result))
		{
			Session::set('SES_ADM_ERROR_MSG','プランが設定されていません。');
			Response::redirect('/adm'.DS.$this->bn);
		}
		$aPlan = $result->as_array('ptID');
		$this->template->set_global('aPlan',$aPlan);

		# タイトル
		$sTitle = 'クーポンの編集';
		$this->template->set_global('pagetitle',$sTitle);

		# パンくずリスト生成
		$this->aBread[] = array('link'=>DS.$this->bn,'name'=>'クーポン一覧');
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		if (!Input::post(null,false))
		{
			$data = $this->aCouponBase;
			$data = array_merge($data, $this->aCoupon);
			if ($this->aCoupon['cpTermDate'] == '9999-12-31')
			{
				$data['cpTermDate'] = date('Y/m/d',strtotime('+1month'));
				$data['infinityRange'] = 1;
			}
			$data['cpPaymentType'] = \Clfunc_Common::dec2Bits((int)$this->aCoupon['cpPaymentType']);
			$data['error'] = null;
			$this->template->content = View::forge($view,$data);
			$this->template->javascript = array('cl.adm.coupon.js');
			return $this->template;
		}

		$aInput = Input::post();

		$val = Validation::forge();
		$val->add_callable('Helper_CustomValidation');
		$val->add('cpCode', 'クーポンコード')
			->add_rule('required')
			->add_rule('trim')
			->add_rule('exact_length', 10)
			->add_rule('valid_string', array('alpha','uppercase','numeric','utf8'));
		;
		$val->add('cpName', 'クーポン名称')
			->add_rule('required')
			->add_rule('trim')
			->add_rule('max_length', 30)
		;
		$val->add('cpDiscount', '割引率')
			->add_rule('required')
			->add_rule('trim')
			->add_rule('valid_string','numeric')
			->add_rule('numeric_min', 1)
			->add_rule('numeric_max', 100)
		;
		$val->add('cpPaymentType', '支払い条件')
			->add_rule('required')
		;
		if (!$val->run())
		{
			$data = $this->aCouponBase;
			$data = array_merge($data, $aInput);
			$data['error'] = $val->error();
			$this->template->content = View::forge($view,$data);
			$this->template->javascript = array('cl.adm.coupon.js');
			return $this->template;
		}

		$result = Model_Coupon::getCoupon(array(array('cpCode','=',$aInput['cpCode']),array('no','!=',$this->aCoupon['no'])));
		if (count($result))
		{
			$aInput['error'] = array('cpCode'=>'指定のクーポンコードは既に利用されています。');
			$this->template->content = View::forge($view,$aInput);
			$this->template->javascript = array('cl.adm.coupon.js');
			return $this->template;
		}

		// 登録データ生成
		$aInput['cpDate'] = date('YmdHis');
		if (isset($aInput['infinityRange']) && $aInput['infinityRange'] == 1)
		{
			$aInput['cpTermDate'] = '9999-12-31';
		}
		unset($aInput['infinityRange']);
		if (isset($aInput['sub_state']))
		{
			unset($aInput['sub_state']);
		}
		$iPType = 0;
		if (isset($aInput['cpPaymentType']))
		{
			foreach ($aInput['cpPaymentType'] as $iV)
			{
				$iPType = ($iPType | (int)$iV);
			}
		}
		$aInput['cpPaymentType'] = $iPType;

		try
		{
			$result = Model_Coupon::updateCoupon($aInput,array(array('no','=',$this->aCoupon['no'])));
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_ADM_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		Session::set('SES_ADM_NOTICE_MSG','クーポンを更新しました。【'.$aInput['cpName'].'】');
		Response::redirect('/adm/'.$this->bn);
	}

	public function action_delete($no = null)
	{
		$aChk = self::CouponChecker($no);
		if (is_array($aChk))
		{
			Session::set('SES_ADM_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}

		try
		{
			$result = Model_Coupon::deleteCoupon($no);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_ADM_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		Session::set('SES_ADM_NOTICE_MSG','クーポンを削除しました。【'.$this->aCoupon['cpName'].'】');
		Response::redirect('/adm/'.$this->bn);
	}

	private function CouponChecker($no = null)
	{
		if (is_null($no))
		{
			return array('msg'=>'クーポンが送信されていません。','url'=>'/adm/'.$this->bn);
		}
		$result = Model_Coupon::getCoupon(array(array('no','=',$no)));
		if (!count($result))
		{
			return array('msg'=>'指定されたクーポンが見つかりません。','url'=>'/adm/'.$this->bn);
		}
		$this->aCoupon = $result->current();

		return true;
	}


}