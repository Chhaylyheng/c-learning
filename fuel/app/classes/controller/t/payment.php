<?php
class Controller_T_Payment extends Controller_T_Base
{

	public function before()
	{
		parent::before();
		# サブタイトル生成
		$this->template->set_global('aClass',null);
	}

	public function action_index()
	{
		# パンくずリスト生成
		$this->template->set_global('breadcrumbs',array(array('name'=>'見積・購入履歴')));
		# ページタイトル生成
		$this->template->set_global('pagetitle','見積・購入履歴');

		# 見積取得
		$aBill = null;
		$aTemp = null;
		$result = Model_Payment::getPayDoc(array(array('ttID','=',$this->aTeacher['ttID'])),null,array('status'=>'asc','bDate'=>'desc','eDate'=>'desc'));
		if (count($result)) {
			$aTemp = $result->as_array();
			foreach ($aTemp as $aT)
			{
				$aBill[$aT['status']][] = $aT;
			}
		}

		$this->template->content = View::forge('t/payment/index');
		$this->template->content->set('aBill',$aBill);
		$this->template->javascript = array('cl.t.payment.js');
		return $this->template;
	}

	public function action_note()
	{
		$view = View::forge('template');
		$view->content = View::forge('t/payment/note');
		$view->javascript = array('cl.t.payment.js');
		$view->footer = View::forge('t/footer');
		return $view;
	}

	public function action_product()
	{
		$sTitle = 'ご購入商品の選択';
		# パンくずリスト生成
		$aBread = array(
				array('link'=>'/payment','name'=>'見積・購入履歴'),
				array('name'=>$sTitle),
		);
		$this->template->set_global('breadcrumbs',$aBread);
		# ページタイトル生成
		$this->template->set_global('pagetitle',$sTitle);

		$result = Model_Payment::getPlan();
		if (!count($result))
		{
			Session::set('SES_T_ERROR_MSG','プラン情報が確認できませんでした。');
			Response::redirect($sBack);
		}
		$aMPlan = $result->as_array('ptID');
		$result = Model_Contract::getContract(array(array('ttID','=',$this->aTeacher['ttID'])),null,array('coTermDate'=>'asc'));
		if (!count($result))
		{
			Session::set('SES_T_ERROR_MSG','契約情報が確認できませんでした。');
			Response::redirect($sBack);
		}
		$aContract = $result->as_array();

		if ($this->aTeacher['ptID'] < 2)
		{
			try
			{
				$aUpdate = array(
					'ttProgress' => 5,
				);
				$result = Model_Teacher::updateTeacher($this->aTeacher['ttID'], $aUpdate);
			}
			catch (Exception $e)
			{
				\Clfunc_Common::LogOut($e,__CLASS__);
				Session::set('SES_T_ERROR_MSG',$e->getMessage());
				Response::redirect($this->eRedirect);
			}
		}

		$this->template->content = View::forge('t/payment/product');
		$this->template->content->set('aMPlan',$aMPlan);
		$this->template->content->set('aContract',$aContract);
		$this->template->javascript = array('cl.t.payment.js');
		return $this->template;
	}

	public function action_contract($iPtID = null)
	{
		$sBack = '/t/payment/product';
		if (!isset($iPtID))
		{
			Session::set('SES_T_ERROR_MSG','正しく購入情報が指定されていません。');
			Response::redirect($sBack);
		}
		$result = Model_Payment::getPlan(array(array('ptID','=',$iPtID),array('ptSelect','=',1)));
		if (!count($result))
		{
			Session::set('SES_T_ERROR_MSG','正しく購入情報が指定されていません。');
			Response::redirect($sBack);
		}
		$aPlan = $result->current();

		$result = Model_Payment::getPlan();
		if (!count($result))
		{
			Session::set('SES_T_ERROR_MSG','プラン情報が確認できませんでした。');
			Response::redirect($sBack);
		}
		$aMPlan = $result->as_array('ptID');
		$result = Model_Contract::getContract(array(array('ttID','=',$this->aTeacher['ttID'])),null,array('coTermDate'=>'asc'));
		if (!count($result))
		{
			Session::set('SES_T_ERROR_MSG','契約情報が確認できませんでした。');
			Response::redirect($sBack);
		}
		$aContract = $result->as_array();

		$iPrice = $aPlan['ptPriceID'] + $aPlan['ptPriceCL'];
		$sExp = '（'.number_format($aPlan['ptPriceID']).'円 × 1ヶ月）＋ （'.number_format($aPlan['ptPriceCL']).'円 × 1ヶ月）';

		$sTitle = $aPlan['ptName'].'プランのご購入手続き';
		# パンくずリスト生成
		$aBread = array(
			array('link'=>'/payment','name'=>'見積・購入履歴'),
			array('link'=>'/payment/product','name'=>'ご購入商品の選択'),
			array('name'=>$sTitle),
		);
		$this->template->set_global('breadcrumbs',$aBread);
		# ページタイトル生成
		$this->template->set_global('pagetitle',$sTitle);

		$this->template->content = View::forge('t/payment/contract');
		$this->template->content->set('aPlan',$aPlan);
		$this->template->content->set('iPrice',$iPrice);
		$this->template->content->set('sExp',$sExp);
		$this->template->content->set('aMPlan',$aMPlan);
		$this->template->content->set('aContract',$aContract);
		$this->template->javascript = array('cl.t.payment.js');
		return $this->template;
	}


	public function action_change()
	{
		$sBack = '/t/payment/product';

		if ($this->aTeacher['ptID'] != 2)
		{
			Session::set('SES_T_ERROR_MSG','対象プランが異なるため、変更できません。');
			Response::redirect($sBack);
		}
		$result = Model_Payment::getPlan(array(array('ptID','=',$this->aTeacher['ptID']),array('ptSelect','=',1)));
		if (!count($result))
		{
			Session::set('SES_T_ERROR_MSG','契約中プランが見つかりません。');
			Response::redirect($sBack);
		}
		$aActPlan = $result->current();

		$result = Model_Payment::getPlan(array(array('ptID','=',3),array('ptSelect','=',1)));
		if (!count($result))
		{
			Session::set('SES_T_ERROR_MSG','変更対象プランが見つかりません。');
			Response::redirect($sBack);
		}
		$aCngPlan = $result->current();

		$iIDP = ($aCngPlan['ptPriceID'] - $aActPlan['ptPriceID']);
		$iRange = \Clfunc_Common::contractMonths($this->aTeacher['coTermDate']);

		$iCLP = $aCngPlan['ptPriceCL'];
		$iCLPd = ($iCLP - $aActPlan['ptPriceCL']);
		$iClass = $this->aTeacher['coClassNum'];

		$iSTP = $aCngPlan['ptPriceStu'];
		$iSTPd = ($iSTP - $aActPlan['ptPriceStu']);
		$iStu = ($this->aTeacher['coStuNum'] / 300) - 1;

		$iPrice =  ($iIDP * $iRange) +
		 ($iCLP * $iRange * $iClass) +
		 ($iSTP * $iRange * $iStu);

		$sExp = null;
		if ($iIDP > 0)
		{
			$sExp = '（'.number_format($iIDP).'円 × '.$iRange.'ヶ月）＋';
		}
		$sExp .= '（'.number_format($iCLP).'円 × '.$iClass.'講義 × '.$iRange.'ヶ月）';

		$result = Model_Payment::getPlan();
		if (!count($result))
		{
			Session::set('SES_T_ERROR_MSG','プラン情報が確認できませんでした。');
			Response::redirect($sBack);
		}
		$aMPlan = $result->as_array('ptID');
		$result = Model_Contract::getContract(array(array('ttID','=',$this->aTeacher['ttID'])),null,array('coTermDate'=>'asc'));
		if (!count($result))
		{
			Session::set('SES_T_ERROR_MSG','契約情報が確認できませんでした。');
			Response::redirect($sBack);
		}
		$aContract = $result->as_array();

		$sTitle = $aCngPlan['ptName'].'プランへの変更手続き';
		# パンくずリスト生成
		$aBread = array(
		array('link'=>'/payment','name'=>'見積・購入履歴'),
		array('link'=>'/payment/product','name'=>'ご購入商品の選択'),
		array('name'=>$sTitle),
		);
		$this->template->set_global('breadcrumbs',$aBread);
		# ページタイトル生成
		$this->template->set_global('pagetitle',$sTitle);

		$this->template->content = View::forge('t/payment/change');
		$this->template->content->set('aActPlan',$aActPlan);
		$this->template->content->set('aCngPlan',$aCngPlan);
		$this->template->content->set('iPrice',$iPrice);
		$this->template->content->set('iRange',$iRange);
		$this->template->content->set('iClass',$iClass);
		$this->template->content->set('iCLP',$iCLP);
		$this->template->content->set('iCLPd',$iCLPd);
		$this->template->content->set('iStu',$iStu);
		$this->template->content->set('iSTP',$iSTP);
		$this->template->content->set('iSTPd',$iSTPd);
		$this->template->content->set('sExp',$sExp);
		$this->template->content->set('aMPlan',$aMPlan);
		$this->template->content->set('aContract',$aContract);
		$this->template->javascript = array('cl.t.payment.js');
		return $this->template;
	}

	public function action_add()
	{
		$sBack = '/t/payment/product';

		if ($this->aTeacher['ptID'] != 3)
		{
			Session::set('SES_T_ERROR_MSG','対象プランが異なります。');
			Response::redirect($sBack);
		}
		if ($this->aTeacher['coClassNum'] == 20)
		{
			Session::set('SES_T_ERROR_MSG','これ以上講義を追加することはできません。');
			Response::redirect($sBack);
		}

		$result = Model_Payment::getPlan(array(array('ptID','=',$this->aTeacher['ptID']),array('ptSelect','=',1)));
		if (!count($result))
		{
			Session::set('SES_T_ERROR_MSG','契約中プランが見つかりません。');
			Response::redirect($sBack);
		}
		$aPlan = $result->current();

		$iRange = \Clfunc_Common::contractMonths($this->aTeacher['coTermDate']);
		$iPrice = $aPlan['ptPriceCL'] * $iRange * 1;

		$sExp = '（'.number_format($aPlan['ptPriceCL']).'円 × 1講義 × '.$iRange.'ヶ月）';

		$result = Model_Payment::getPlan();
		if (!count($result))
		{
			Session::set('SES_T_ERROR_MSG','プラン情報が確認できませんでした。');
			Response::redirect($sBack);
		}
		$aMPlan = $result->as_array('ptID');
		$result = Model_Contract::getContract(array(array('ttID','=',$this->aTeacher['ttID'])),null,array('coTermDate'=>'asc'));
		if (!count($result))
		{
			Session::set('SES_T_ERROR_MSG','契約情報が確認できませんでした。');
			Response::redirect($sBack);
		}
		$aContract = $result->as_array();

		$sTitle = '講義追加の手続き';
		# パンくずリスト生成
		$aBread = array(
		array('link'=>'/payment','name'=>'見積・購入履歴'),
		array('link'=>'/payment/product','name'=>'ご購入商品の選択'),
		array('name'=>$sTitle),
		);
		$this->template->set_global('breadcrumbs',$aBread);
		# ページタイトル生成
		$this->template->set_global('pagetitle',$sTitle);

		$this->template->content = View::forge('t/payment/add');
		$this->template->content->set('aPlan',$aPlan);
		$this->template->content->set('iRange',$iRange);
		$this->template->content->set('iPrice',$iPrice);
		$this->template->content->set('sExp',$sExp);
		$this->template->content->set('aMPlan',$aMPlan);
		$this->template->content->set('aContract',$aContract);
		$this->template->javascript = array('cl.t.payment.js');
		return $this->template;
	}

	public function action_stuadd()
	{
		$sBack = '/t/payment/product';

		if ($this->aTeacher['ptID'] != 3)
		{
			Session::set('SES_T_ERROR_MSG','対象プランが異なります。');
			Response::redirect($sBack);
		}
		if ($this->aTeacher['coStuNum'] >= 3000)
		{
			Session::set('SES_T_ERROR_MSG','これ以上履修人数を追加することはできません。');
			Response::redirect($sBack);
		}

		$result = Model_Payment::getPlan(array(array('ptID','=',$this->aTeacher['ptID']),array('ptSelect','=',1)));
		if (!count($result))
		{
			Session::set('SES_T_ERROR_MSG','契約中プランが見つかりません。');
			Response::redirect($sBack);
		}
		$aPlan = $result->current();

		$iRange = \Clfunc_Common::contractMonths($this->aTeacher['coTermDate']);
		$iActStu =
		$iPrice = $aPlan['ptPriceStu'] * $iRange * 1;

		$sExp = '（'.number_format($aPlan['ptPriceStu']).'円 × 1講義 × '.$iRange.'ヶ月）';

		$result = Model_Payment::getPlan();
		if (!count($result))
		{
			Session::set('SES_T_ERROR_MSG','プラン情報が確認できませんでした。');
			Response::redirect($sBack);
		}
		$aMPlan = $result->as_array('ptID');
		$result = Model_Contract::getContract(array(array('ttID','=',$this->aTeacher['ttID'])),null,array('coTermDate'=>'asc'));
		if (!count($result))
		{
			Session::set('SES_T_ERROR_MSG','契約情報が確認できませんでした。');
			Response::redirect($sBack);
		}
		$aContract = $result->as_array();

		$sTitle = '講義追加の手続き';
		# パンくずリスト生成
		$aBread = array(
		array('link'=>'/payment','name'=>'見積・購入履歴'),
		array('link'=>'/payment/product','name'=>'ご購入商品の選択'),
		array('name'=>$sTitle),
		);
		$this->template->set_global('breadcrumbs',$aBread);
		# ページタイトル生成
		$this->template->set_global('pagetitle',$sTitle);

		$this->template->content = View::forge('t/payment/add');
		$this->template->content->set('aPlan',$aPlan);
		$this->template->content->set('iRange',$iRange);
		$this->template->content->set('iPrice',$iPrice);
		$this->template->content->set('sExp',$sExp);
		$this->template->content->set('aMPlan',$aMPlan);
		$this->template->content->set('aContract',$aContract);
		$this->template->javascript = array('cl.t.payment.js');
		return $this->template;
	}

	public function action_purchase($number)
	{
		if (!isset($number))
		{
			Session::set('SES_T_ERROR_MSG','正しく見積情報が指定されていません。');
			Response::redirect('/t/payment');
		}
		$result = Model_Payment::getPayDoc(array(array('ttID','=',$this->aTeacher['ttID']),array('eNO','=',$number),array('status','=',0)));
		if (!count($result))
		{
			Session::set('SES_T_ERROR_MSG','正しく見積情報が指定されていません。');
			Response::redirect('/t/payment');
		}
		$aE = $result->current();

		switch ($aE['eBilling'])
		{
			case 1:
				Response::redirect('/t/payment/purchase_card/'.$aE['eNO']);
			break;
			case 2:
				# 銀行振込処理
				$aUpdate = array('status'=>1,'bDate'=>date('YmdHis'));
				try
				{
					$aP = unserialize($aE['purchase']);
					switch ($aP['product'])
					{
						case 'contract':
							$result = Model_Payment::getPlan(array(array('ptID','=',$aP['pt']),array('ptSelect','=',1)));
							if (!count($result))
							{
								Log::warning('Not Found Plan - ['.$this->aTeacher['ttID'].'/'.$this->aTeacher['ttName'].'] - '.serialize($aP));
								throw new Exception('購入対象のプランが見つかりませんでした。');
							}
							$aPlan = $result->current();
							$result = Model_Contract::getContract(array(array('ttID','=',$this->aTeacher['ttID'])),null,array('coNO'=>'desc'));
							if (!count($result))
							{
								Log::warning('Not Found Contract - ['.$this->aTeacher['ttID'].'/'.$this->aTeacher['ttName'].'] - '.serialize($aP));
								throw new Exception('現在の契約内容が確認できませんでした。');
							}
							$aCon = $result->as_array();
							$aLast = $aCon[0];

							if ($aLast['coTermDate'] >= date('Y-m-d'))
							{
								$sStart = date('Y-m-d',strtotime('+1 day',strtotime($aLast['coTermDate'])));
							}
							else
							{
								$sStart = date('Y-m-d');
							}
							$sEnd = \Clfunc_Common::contractEnd($sStart,$aP['range']);
							$sLimit = date('Y-m-t', strtotime('+1 month'));
							$coNO = $aLast['coNO'] + 1;

							$iAmount = $aE['ePrice'];
							$iTax = floor($aE['ePrice'] * $aE['eTax']);

							$aIns = array(
								'ttID' => $this->aTeacher['ttID'],
								'coNO' => $coNO,
								'ptID' => $aP['pt'],
								'coStartDate' => $sStart,
								'coTermDate' => $sEnd,
								'coLimitDate' => $sLimit,
								'coClassNum' => $aP['class'],
								'coStuNum' => $aPlan['ptStuNum'] + ($aP['stu'] * 300),
								'coCapacity' => $aPlan['ptCapacity'],
								'coPayment' => ($iAmount + $iTax),
								'coMonths' => $aP['range'],
							);

							if ($this->aTeacher['ptID'] == 1)
							{
								$aIns['coStartDate'] = date('Y-m-d');

								$aUp = array(
									'coTermDate' => date('Y-m-d', strtotime('-1 day')),
								);
								$result = Model_Contract::updateContract($aUp,array(array('ttID','=',$this->aTeacher['ttID']),array('ptID','=',1)));
							}
							$result = Model_Contract::insertContract($aIns);

							$sMailOpt =
							(($coNO == 2)? '新規契約':'継続契約')."\n".
							'契約プラン：'.$aPlan['ptName']."\n".
							'契約期間：'.$aP['range'].'ヶ月（'.date('Y年n月j日',strtotime($sStart)).'～'.date('Y年n月j日',strtotime($sEnd)).'）'."\n".
							'講義数：'.$aP['class'].'講義';
						break;
						case 'change':
							$result = Model_Payment::getPlan(array(array('ptID','=',$this->aTeacher['ptID']),array('ptSelect','=',1)));
							if (!count($result))
							{
								Log::warning('Not Found Plan - ['.$this->aTeacher['ttID'].'/'.$this->aTeacher['ttName'].'] - '.serialize($aP));
								throw new Exception('現在の契約中プランの内容が確認できませんでした。');
							}
							$aActPlan = $result->current();
							if ($aActPlan['ptID'] != 2)
							{
								Log::warning('Plan Changed - ['.$this->aTeacher['ttID'].'/'.$this->aTeacher['ttName'].'] - '.serialize($aP));
								throw new Exception('見積もり作成時と現在の契約が一致しません。再度、見積もりを作成してください。');
							}

							$result = Model_Payment::getPlan(array(array('ptID','=',$aP['pt']),array('ptSelect','=',1)));
							if (!count($result))
							{
								Log::warning('Not Found New Plan - ['.$this->aTeacher['ttID'].'/'.$this->aTeacher['ttName'].'] - '.serialize($aP));
								throw new Exception('変更対象プランが見つかりません。');
							}
							$aCngPlan = $result->current();

							$sCode = '';
							$iDiscount = 0;
							if (isset($aP['coupon-code']) && mb_strlen($aP['coupon-code']) == 10)
							{
								$result = Model_Payment::getCoupon(array(array('cpCode','=',$aP['coupon-code']),array('cpTermDate','>',date('Y-m-d'))));
								if (!count($result))
								{
									$iDiscount = 0;
								}
								else
								{
									$aCoupon = $result->current();
									$iDiscount = $aCoupon['cpDiscount'];
									$sCode = $aP['coupon-code'];
								}
							}

							$sLimit = date('Y-m-t', strtotime('+1 month'));

							$iIDP = ($aCngPlan['ptPriceID'] - $aActPlan['ptPriceID']);
							$iRange = \Clfunc_Common::contractMonths($this->aTeacher['coTermDate']);

							$iCLP = ($aCngPlan['ptPriceCL'] * ((100 - $iDiscount)/100));
							$iCLPd = ($iCLP - $aActPlan['ptPriceCL']);
							$iActClass = $this->aTeacher['coClassNum'];
							$iCngClass = (int)$aP['class'];

							$iSTP = ($aCngPlan['ptPriceStu'] * ((100 - $iDiscount)/100));
							$iSTPd = ($iSTP - $aActPlan['ptPriceStu']);
							$iActStu = $this->aTeacher['coStuNum'];
							$iCngStu = (int)$aP['stu'];

							$iPrice = ($iIDP * $iRange) +
							 ($iCLPd * $iRange * $iActClass) + ($iCLP * $iRange * ($iCngClass - $iActClass)) +
							 ($iSTPd * $iRange * $iActStu) + ($iSTP * $iRange * ($iCngStu - $iActStu));
							$iTax = floor($iPrice * $aE['eTax']);

							$coNO = $this->aTeacher['coNO'];

							if ($aP['price'] != $iPrice)
							{
								Log::warning('Price Changed - ['.$this->aTeacher['ttID'].'/'.$this->aTeacher['ttName'].'] - '.serialize($aP));
								throw new Exception('見積もり時と現在で金額に変更があります。再度、現在の状態で見積もりを作成してください。');
							}

							$aUp = array(
								'ptID'        => $aCngPlan['ptID'],
								'coCapacity'  => $aCngPlan['ptCapacity'],
								'coClassNum'  => $iCngClass,
								'coStuNum'    => $aCngPlan['ptStuNum'] + ($iCngStu * 300),
								'coPayment'   => ($this->aTeacher['coPayment'] + $iPrice + $iTax),
								'coLimitDate' => $sLimit,
							);
							$result = Model_Contract::updateContract($aUp,array(array('ttID','=',$this->aTeacher['ttID']),array('coNO','=',$this->aTeacher['coNO'])));

							$sMailOpt =
							'プラン変更契約'."\n".
							'契約プラン：'.$aActPlan['ptName'].' → '.$aCngPlan['ptName']."\n".
							'契約期間：'.$iRange.'ヶ月（'.date('Y年n月j日').'～'.date('Y年n月j日',strtotime($this->aTeacher['coTermDate'])).'）'."\n".
							'講義数：'.$iActClass.' → '.$iCngClass.'講義';
						break;
						case 'add':
							$result = Model_Payment::getPlan(array(array('ptID','=',$this->aTeacher['ptID']),array('ptSelect','=',1)));
							if (!count($result))
							{
								Log::warning('Not Found Plan - ['.$this->aTeacher['ttID'].'/'.$this->aTeacher['ttName'].'] - '.serialize($aP));
								throw new Exception('現在の契約中プランの内容が確認できませんでした。');
							}
							$aPlan = $result->current();
							if ($aPlan['ptID'] != 3)
							{
								Log::warning('Plan Changed - ['.$this->aTeacher['ttID'].'/'.$this->aTeacher['ttName'].'] - '.serialize($aP));
								throw new Exception('見積もり作成時と現在の契約が一致しません。再度、見積もりを作成してください。');
							}

							$sCode = '';
							$iDiscount = 0;
							if (isset($aP['coupon-code']) && mb_strlen($aP['coupon-code']) == 10)
							{
								$result = Model_Payment::getCoupon(array(array('cpCode','=',$aP['coupon-code']),array('cpTermDate','>',date('Y-m-d'))));
								if (!count($result))
								{
									$iDiscount = 0;
								}
								else
								{
									$aCoupon = $result->current();
									$iDiscount = $aCoupon['cpDiscount'];
									$sCode = $aP['coupon-code'];
								}
							}

							$sLimit = date('Y-m-t', strtotime('+1 month'));

							$iCLP = ($aPlan['ptPriceCL'] * ((100 - $iDiscount)/100));

							$iRange = \Clfunc_Common::contractMonths($this->aTeacher['coTermDate']);
							$iClass = (int)$aP['class'];
							if (($this->aTeacher['coClassNum'] + $iClass) > 20)
							{
								Log::warning('Class Over - ['.$this->aTeacher['ttID'].'/'.$this->aTeacher['ttName'].'] - '.serialize($aP));
								throw new Exception('この講義追加により契約講義数が20講義を越えてしまいます。再度、見積もりを作成してください。');
							}

							$iPrice = $iCLP * $iRange * $iClass;
							$iTax = floor($iPrice * $aE['eTax']);

							$coNO = $this->aTeacher['coNO'];

							if ($aP['price'] != $iPrice)
							{
								Log::warning('Price Changed - ['.$this->aTeacher['ttID'].'/'.$this->aTeacher['ttName'].'] - '.serialize($aP));
								throw new Exception('見積もり時と現在で金額に変更があります。再度、現在の状態で見積もりを作成してください。');
							}

							$aUp = array(
								'coClassNum' => ($this->aTeacher['coClassNum'] + $iClass),
								'coPayment'  => ($this->aTeacher['coPayment'] + $iPrice + $iTax),
								'coLimitDate' => $sLimit,
							);
							$result = Model_Contract::updateContract($aUp,array(array('ttID','=',$this->aTeacher['ttID']),array('coNO','=',$this->aTeacher['coNO'])));

							$sMailOpt =
							'講義数の追加契約'."\n".
							'契約プラン：'.$aPlan['ptName']."\n".
							'契約期間：'.$iRange.'ヶ月（'.date('Y年n月j日').'～'.date('Y年n月j日',strtotime($this->aTeacher['coTermDate'])).'）'."\n".
							'追加講義数：'.$iClass.'講義（合計 '.$aUp['coClassNum'].'講義）';
						break;
					}
					$aP['coNO'] = $coNO;
					$aUpdate['purchase'] = serialize($aP);
					$result = Model_Payment::updatePayDoc($aUpdate,$aE,true);
				}
				catch (Exception $e)
				{
					\Clfunc_Common::LogOut($e,__CLASS__);
					Session::set('SES_T_ERROR_MSG',$e->getMessage());
					Response::redirect($this->eRedirect);
				}
				# 購入手続き完了メール
				$email = \Email::forge();
				$email->from(CL_MAIL_FROM, CL_MAIL_SENDER);
				$email->to($this->aTeacher['ttMail']);
				$email->bcc(array(CL_KEIYAKUMAIL,CL_MIYATAMAIL));
				$email->subject('[CL]購入手続き完了のお知らせ（銀行振込）');
				$body = View::forge('email/t_purchase_bank', array('aE'=>$aE,'aT'=>$this->aTeacher,'sMailOpt'=>$sMailOpt), false);
				$email->body($body);

				try
				{
					$email->send();
				}
				catch (\EmailValidationFailedException $e)
				{
					Log::warning('TeacherBankPurchaseStartMail - ' . $e->getMessage());
				}
				catch (\EmailSendingFailedException $e)
				{
					Log::warning('TeacherBankPurchaseStartMail - ' . $e->getMessage());
				}

				try
				{
					$aUpdate = array(
							'ttProgress' => 7,
					);
					$result = Model_Teacher::updateTeacher($this->aTeacher['ttID'], $aUpdate);
				}
				catch (Exception $e)
				{
					\Clfunc_Common::LogOut($e,__CLASS__);
				}

				Session::set('SES_T_NOTICE_MSG','銀行振込の購入手続きが完了しました。請求書を発行する場合は、下欄購入履歴よりご利用ください。');
				Session::set('SES_T_PURCHASE_BANK',true);
				Response::redirect('/t/payment');
			break;
			case 4:
				Response::redirect('/t/payment/purchase_paypal/'.$aE['eNO']);
			break;
		}
	}

	public function action_purchase_card($number = null)
	{
		$sBack = '/t/payment';
		$aBread = array(
			array('link'=>'/payment','name'=>'見積・購入履歴'),
			array('name'=>'クレジットカード決済'),
		);
		$this->template->set_global('breadcrumbs',$aBread);
		# ページタイトル生成
		$this->template->set_global('pagetitle','クレジットカード決済');

		if (!isset($number))
		{
			Session::set('SES_T_ERROR_MSG','正しく見積情報が指定されていません。');
			Response::redirect($sBack);
		}
		$result = Model_Payment::getPayDoc(array(array('ttID','=',$this->aTeacher['ttID']),array('eNO','=',$number),array('status','=',0)));
		if (!count($result))
		{
			Session::set('SES_T_ERROR_MSG','正しく見積情報が指定されていません。');
			Response::redirect($sBack);
		}
		$aE = $result->current();

		try
		{
			$aP = unserialize($aE['purchase']);
			switch ($aP['product'])
			{
				case 'change':
					$result = Model_Payment::getPlan(array(array('ptID','=',$this->aTeacher['ptID']),array('ptSelect','=',1)));
					if (!count($result))
					{
						Log::warning('Not Found Plan - ['.$this->aTeacher['ttID'].'/'.$this->aTeacher['ttName'].'] - '.serialize($aP));
						throw new Exception('現在の契約中プランの内容が確認できませんでした。');
					}
					$aActPlan = $result->current();
					if ($aActPlan['ptID'] != 2)
					{
						Log::warning('Plan Changed - ['.$this->aTeacher['ttID'].'/'.$this->aTeacher['ttName'].'] - '.serialize($aP));
						throw new Exception('見積もり作成時と現在の契約が一致しません。再度、見積もりを作成してください。');
					}

					$result = Model_Payment::getPlan(array(array('ptID','=',$aP['pt']),array('ptSelect','=',1)));
					if (!count($result))
					{
						Log::warning('Not Found Plan - ['.$this->aTeacher['ttID'].'/'.$this->aTeacher['ttName'].'] - '.serialize($aP));
						throw new Exception('変更対象プランが見つかりません。');
					}
					$aCngPlan = $result->current();

					$sCode = '';
					$iDiscount = 0;
					if (isset($aP['coupon-code']) && mb_strlen($aP['coupon-code']) == 10)
					{
						$result = Model_Payment::getCoupon(array(array('cpCode','=',$aP['coupon-code']),array('cpTermDate','>',date('Y-m-d'))));
						if (!count($result))
						{
							$iDiscount = 0;
						}
						else
						{
							$aCoupon = $result->current();
							$iDiscount = $aCoupon['cpDiscount'];
							$sCode = $aP['coupon-code'];
						}
					}

					$sLimit = date('Y-m-t', strtotime('+1 month'));

					$iIDP = ($aCngPlan['ptPriceID'] - $aActPlan['ptPriceID']);
					$iRange = \Clfunc_Common::contractMonths($this->aTeacher['coTermDate']);

					$iCLP = ($aCngPlan['ptPriceCL'] * ((100 - $iDiscount)/100));
					$iCLPd = ($iCLP - $aActPlan['ptPriceCL']);
					$iActClass = $this->aTeacher['coClassNum'];
					$iCngClass = (int)$aP['class'];

					$iSTP = ($aCngPlan['ptPriceStu'] * ((100 - $iDiscount)/100));
					$iSTPd = ($iSTP - $aActPlan['ptPriceStu']);
					$iActStu = $this->aTeacher['coStuNum'];
					$iCngStu = (int)$aP['stu'];

					$iPrice = ($iIDP * $iRange) +
					 ($iCLPd * $iRange * $iActClass) + ($iCLP * $iRange * ($iCngClass - $iActClass)) +
					 ($iSTPd * $iRange * $iActStu) + ($iSTP * $iRange * ($iCngStu - $iActStu));

					if ($aP['price'] != $iPrice)
					{
						Log::warning('Price Changed - ['.$this->aTeacher['ttID'].'/'.$this->aTeacher['ttName'].'] - '.serialize($aP));
						throw new Exception('見積もり時と現在で金額に変更があります。再度、現在の状態で見積もりを作成してください。');
					}
				break;
				case 'add':
					$result = Model_Payment::getPlan(array(array('ptID','=',$this->aTeacher['ptID']),array('ptSelect','=',1)));
					if (!count($result))
					{
						Log::warning('Not Found Plan - ['.$this->aTeacher['ttID'].'/'.$this->aTeacher['ttName'].'] - '.serialize($aP));
						throw new Exception('現在の契約中プランの内容が確認できませんでした。');
					}
					$aPlan = $result->current();
					if ($aPlan['ptID'] != 3)
					{
						Log::warning('Plan Changed - ['.$this->aTeacher['ttID'].'/'.$this->aTeacher['ttName'].'] - '.serialize($aP));
						throw new Exception('見積もり作成時と現在の契約が一致しません。再度、見積もりを作成してください。');
					}

					$sCode = '';
					$iDiscount = 0;
					if (isset($aP['coupon-code']) && mb_strlen($aP['coupon-code']) == 10)
					{
						$result = Model_Payment::getCoupon(array(array('cpCode','=',$aP['coupon-code']),array('cpTermDate','>',date('Y-m-d'))));
						if (!count($result))
						{
							$iDiscount = 0;
						}
						else
						{
							$aCoupon = $result->current();
							$iDiscount = $aCoupon['cpDiscount'];
							$sCode = $aP['coupon-code'];
						}
					}

					$sLimit = date('Y-m-t', strtotime('+1 month'));

					$iCLP = ($aPlan['ptPriceCL'] * ((100 - $iDiscount)/100));

					$iRange = \Clfunc_Common::contractMonths($this->aTeacher['coTermDate']);
					$iClass = (int)$aP['class'];
					if (($this->aTeacher['coClassNum'] + $iClass) > 20)
					{
						Log::warning('Class Over - ['.$this->aTeacher['ttID'].'/'.$this->aTeacher['ttName'].'] - '.serialize($aP));
						throw new Exception('この講義追加により契約講義数が20講義を越えてしまいます。再度、見積もりを作成してください。');
					}

					$iPrice = $iCLP * $iRange * $iClass;

					if ($aP['price'] != $iPrice)
					{
						Log::warning('Price Changed - ['.$this->aTeacher['ttID'].'/'.$this->aTeacher['ttName'].'] - '.serialize($aP));
						throw new Exception('見積もり時と現在で金額に変更があります。再度、現在の状態で見積もりを作成してください。');
					}
				break;
			}
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect($sBack);
		}

		# クレジットカード情報を取得
		$aCardInfo = null;
		$oPG = new Clfunc_GmoPGPayment();
		$result = $oPG->readCard($this->aTeacher['ttID']);
		if ($result == 0)
		{
			$aCardInfo = $oPG->getResult();
		}

		$this->template->content = View::forge('t/payment/card');
		$this->template->content->set('aE',$aE);
		$this->template->content->set('aCardInfo',$aCardInfo);
		$this->template->javascript = array('cl.t.payment.js');
		return $this->template;
	}

	public function action_purchase_paypal($number = null)
	{
		// ウェブペイメントプラス用ジャンプ
		Response::redirect('/t/payment/ppsuccess/'.$number);

		$sBack = '/t/payment';
		if (!isset($number))
		{
			Session::set('SES_T_ERROR_MSG','正しく見積情報が指定されていません。');
			Response::redirect($sBack);
		}
		$result = Model_Payment::getPayDoc(array(array('ttID','=',$this->aTeacher['ttID']),array('eNO','=',$number),array('status','=',0)));
		if (!count($result))
		{
			Session::set('SES_T_ERROR_MSG','正しく見積情報が指定されていません。');
			Response::redirect($sBack);
		}
		$aE = $result->current();

		try
		{
			$aP = unserialize($aE['purchase']);
			switch ($aP['product'])
			{
				case 'change':
					$result = Model_Payment::getPlan(array(array('ptID','=',$this->aTeacher['ptID']),array('ptSelect','=',1)));
					if (!count($result))
					{
						Log::warning('Not Found Plan - ['.$this->aTeacher['ttID'].'/'.$this->aTeacher['ttName'].'] - '.serialize($aP));
						throw new Exception('現在の契約中プランの内容が確認できませんでした。');
					}
					$aActPlan = $result->current();
					if ($aActPlan['ptID'] != 2)
					{
						Log::warning('Plan Changed - ['.$this->aTeacher['ttID'].'/'.$this->aTeacher['ttName'].'] - '.serialize($aP));
						throw new Exception('見積もり作成時と現在の契約が一致しません。再度、見積もりを作成してください。');
					}

					$result = Model_Payment::getPlan(array(array('ptID','=',$aP['pt']),array('ptSelect','=',1)));
					if (!count($result))
					{
						Log::warning('Not Found Plan - ['.$this->aTeacher['ttID'].'/'.$this->aTeacher['ttName'].'] - '.serialize($aP));
						throw new Exception('変更対象プランが見つかりません。');
					}
					$aCngPlan = $result->current();

					$sCode = '';
					$iDiscount = 0;
					if (isset($aP['coupon-code']) && mb_strlen($aP['coupon-code']) == 10)
					{
						$result = Model_Payment::getCoupon(array(array('cpCode','=',$aP['coupon-code']),array('cpTermDate','>',date('Y-m-d'))));
						if (!count($result))
						{
							$iDiscount = 0;
						}
						else
						{
							$aCoupon = $result->current();
							$iDiscount = $aCoupon['cpDiscount'];
							$sCode = $aP['coupon-code'];
						}
					}

					$sLimit = date('Y-m-t', strtotime('+1 month'));

					$iIDP = ($aCngPlan['ptPriceID'] - $aActPlan['ptPriceID']);
					$iRange = \Clfunc_Common::contractMonths($this->aTeacher['coTermDate']);

					$iCLP = ($aCngPlan['ptPriceCL'] * ((100 - $iDiscount)/100));
					$iCLPd = ($iCLP - $aActPlan['ptPriceCL']);
					$iActClass = $this->aTeacher['coClassNum'];
					$iCngClass = (int)$aP['class'];

					$iSTP = ($aCngPlan['ptPriceStu'] * ((100 - $iDiscount)/100));
					$iSTPd = ($iSTP - $aActPlan['ptPriceStu']);
					$iActStu = $this->aTeacher['coStuNum'];
					$iCngStu = (int)$aP['stu'];

					$iPrice = ($iIDP * $iRange) +
					 ($iCLPd * $iRange * $iActClass) + ($iCLP * $iRange * ($iCngClass - $iActClass)) +
					 ($iSTPd * $iRange * $iActStu) + ($iSTP * $iRange * ($iCngStu - $iActStu));

					if ($aP['price'] != $iPrice)
					{
						Log::warning('Price Changed - ['.$this->aTeacher['ttID'].'/'.$this->aTeacher['ttName'].'] - '.serialize($aP));
						throw new Exception('見積もり時と現在で金額に変更があります。再度、現在の状態で見積もりを作成してください。');
					}
				break;
				case 'add':
					$result = Model_Payment::getPlan(array(array('ptID','=',$this->aTeacher['ptID']),array('ptSelect','=',1)));
					if (!count($result))
					{
						Log::warning('Not Found Plan - ['.$this->aTeacher['ttID'].'/'.$this->aTeacher['ttName'].'] - '.serialize($aP));
						throw new Exception('現在の契約中プランの内容が確認できませんでした。');
					}
					$aPlan = $result->current();
					if ($aPlan['ptID'] != 3)
					{
						Log::warning('Plan Changed - ['.$this->aTeacher['ttID'].'/'.$this->aTeacher['ttName'].'] - '.serialize($aP));
						throw new Exception('見積もり作成時と現在の契約が一致しません。再度、見積もりを作成してください。');
					}

					$sCode = '';
					$iDiscount = 0;
					if (isset($aP['coupon-code']) && mb_strlen($aP['coupon-code']) == 10)
					{
						$result = Model_Payment::getCoupon(array(array('cpCode','=',$aP['coupon-code']),array('cpTermDate','>',date('Y-m-d'))));
						if (!count($result))
						{
							$iDiscount = 0;
						}
						else
						{
							$aCoupon = $result->current();
							$iDiscount = $aCoupon['cpDiscount'];
							$sCode = $aP['coupon-code'];
						}
					}

					$sLimit = date('Y-m-t', strtotime('+1 month'));

					$iCLP = ($aPlan['ptPriceCL'] * ((100 - $iDiscount)/100));

					$iRange = \Clfunc_Common::contractMonths($this->aTeacher['coTermDate']);
					$iClass = (int)$aP['class'];
					if (($this->aTeacher['coClassNum'] + $iClass) > 20)
					{
						Log::warning('Class Over - ['.$this->aTeacher['ttID'].'/'.$this->aTeacher['ttName'].'] - '.serialize($aP));
						throw new Exception('この講義追加により契約講義数が20講義を越えてしまいます。再度、見積もりを作成してください。');
					}

					$iPrice = $iCLP * $iRange * $iClass;

					if ($aP['price'] != $iPrice)
					{
						Log::warning('Price Changed - ['.$this->aTeacher['ttID'].'/'.$this->aTeacher['ttName'].'] - '.serialize($aP));
						throw new Exception('見積もり時と現在で金額に変更があります。再度、現在の状態で見積もりを作成してください。');
					}
				break;
			}
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect($sBack);
		}

		$iAmt = floor($aE['ePrice']*(1+$aE['eTax']));

		$ppp = new Clfunc_PayPalPayment();
		$ppp->setAmount($iAmt);
		$ppp->setReturnParam($number);

		try
		{
			# 認証チェック
			$res = $ppp->PaymentCheck($this->aTeacher);
		}
		catch (\Exception $e)
		{
			Session::set('SES_T_ERROR_MSG','PayPal Payment Faild. ['.$e->getCode().']'.$e->getMessage());
			Response::redirect('/t/payment');
		}
	}

	public function action_ppsuccess($number = null)
	{
		$url = Input::referrer();
		$url = parse_url($url);
		// paypal判定
		/*
		if (strpos($url['host'], 'paypal.com') === false) {
			return;
		}
		*/

		$sBack = '/t/payment';
		if (!isset($number))
		{
			Session::set('SES_T_ERROR_MSG','正しく見積情報が指定されていません。');
			Response::redirect($sBack);
		}
		$result = Model_Payment::getPayDoc(array(array('ttID','=',$this->aTeacher['ttID']),array('eNO','=',$number),array('status','=',0)));
		if (!count($result))
		{
			Session::set('SES_T_ERROR_MSG','正しく見積情報が指定されていません。');
			Response::redirect($sBack);
		}
		$aE = $result->current();
		$iAmount = $aE['ePrice'];
		$iTax = floor($aE['ePrice'] * $aE['eTax']);

		$aIns = null;
		$aUp = null;

		try
		{
			$aP = unserialize($aE['purchase']);
			switch ($aP['product'])
			{
				case 'contract':
					$result = Model_Payment::getPlan(array(array('ptID','=',$aP['pt']),array('ptSelect','=',1)));
					if (!count($result))
					{
						Log::warning('Not Found Plan - ['.$this->aTeacher['ttID'].'/'.$this->aTeacher['ttName'].'] - '.serialize($aP));
						throw new Exception('購入対象のプランが見つかりませんでした。');
					}
					$aPlan = $result->current();
					$result = Model_Contract::getContract(array(array('ttID','=',$this->aTeacher['ttID'])),null,array('coNO'=>'desc'));
					if (!count($result))
					{
						Log::warning('Not Found Contract - ['.$this->aTeacher['ttID'].'/'.$this->aTeacher['ttName'].'] - '.serialize($aP));
						throw new Exception('現在の契約内容が確認できませんでした。');
					}
					$aCon = $result->as_array();
					$aLast = $aCon[0];

					if ($aLast['coTermDate'] >= date('Y-m-d'))
					{
						$sStart = date('Y-m-d',strtotime('+1 day',strtotime($aLast['coTermDate'])));
					}
					else
					{
						$sStart = date('Y-m-d');
					}
					$sEnd = \Clfunc_Common::contractEnd($sStart,$aP['range']);
					$coNO = $aLast['coNO'] + 1;

					$aIns = array(
						'ttID' => $this->aTeacher['ttID'],
						'coNO' => $coNO,
						'ptID' => $aP['pt'],
						'coStartDate' => $sStart,
						'coTermDate' => $sEnd,
						'coClassNum' => $aP['class'],
						'coStuNum' => $aPlan['ptStuNum'] + ($aP['stu'] * 300),
						'coCapacity' => $aPlan['ptCapacity'],
						'coPayment' => ($iAmount + $iTax),
						'coMonths' => $aP['range'],
					);

					$sMailOpt =
					(($coNO == 2)? '新規契約':'継続契約')."\n".
						'契約プラン：'.$aPlan['ptName']."\n".
						'契約期間：'.$aP['range'].'ヶ月（'.date('Y年n月j日',strtotime($sStart)).'～'.date('Y年n月j日',strtotime($sEnd)).'）'."\n".
						'講義数：'.$aP['class'].'講義';
				break;
				case 'change':
					$result = Model_Payment::getPlan(array(array('ptID','=',$this->aTeacher['ptID']),array('ptSelect','=',1)));
					if (!count($result))
					{
						Log::warning('Not Found Plan - ['.$this->aTeacher['ttID'].'/'.$this->aTeacher['ttName'].'] - '.serialize($aP));
						throw new Exception('現在の契約中プランの内容が確認できませんでした。');
					}
					$aActPlan = $result->current();
					if ($aActPlan['ptID'] != 2)
					{
						Log::warning('Plan Changed - ['.$this->aTeacher['ttID'].'/'.$this->aTeacher['ttName'].'] - '.serialize($aP));
						throw new Exception('見積もり作成時と現在の契約が一致しません。再度、見積もりを作成してください。');
					}

					$result = Model_Payment::getPlan(array(array('ptID','=',$aP['pt']),array('ptSelect','=',1)));
					if (!count($result))
					{
						Log::warning('Not Found New Plan - ['.$this->aTeacher['ttID'].'/'.$this->aTeacher['ttName'].'] - '.serialize($aP));
						throw new Exception('変更対象プランが見つかりません。');
					}
					$aCngPlan = $result->current();

					$sCode = '';
					$iDiscount = 0;
					if (isset($aP['coupon-code']) && mb_strlen($aP['coupon-code']) == 10)
					{
						$result = Model_Payment::getCoupon(array(array('cpCode','=',$aP['coupon-code']),array('cpTermDate','>',date('Y-m-d'))));
						if (!count($result))
						{
							$iDiscount = 0;
						}
						else
						{
							$aCoupon = $result->current();
							$iDiscount = $aCoupon['cpDiscount'];
							$sCode = $aP['coupon-code'];
						}
					}

					$iIDP = ($aCngPlan['ptPriceID'] - $aActPlan['ptPriceID']);
					$iRange = \Clfunc_Common::contractMonths($this->aTeacher['coTermDate']);

					$iCLP = ($aCngPlan['ptPriceCL'] * ((100 - $iDiscount)/100));
					$iCLPd = ($iCLP - $aActPlan['ptPriceCL']);
					$iActClass = $this->aTeacher['coClassNum'];
					$iCngClass = (int)$aP['class'];

					$iSTP = ($aCngPlan['ptPriceStu'] * ((100 - $iDiscount)/100));
					$iSTPd = ($iSTP - $aActPlan['ptPriceStu']);
					$iActStu = $this->aTeacher['coStuNum'];
					$iCngStu = (int)$aP['stu'];

					$iPrice = ($iIDP * $iRange) +
					($iCLPd * $iRange * $iActClass) + ($iCLP * $iRange * ($iCngClass - $iActClass)) +
					($iSTPd * $iRange * $iActStu) + ($iSTP * $iRange * ($iCngStu - $iActStu));
					$iTax = floor($iPrice * $aE['eTax']);

					$coNO = $this->aTeacher['coNO'];

					if ($aP['price'] != $iPrice)
					{
						Log::warning('Price Changed - ['.$this->aTeacher['ttID'].'/'.$this->aTeacher['ttName'].'] - '.serialize($aP));
						throw new Exception('見積もり時と現在で金額に変更があります。再度、現在の状態で見積もりを作成してください。');
					}

					$aUp = array(
						'ptID'       => $aCngPlan['ptID'],
						'coCapacity' => $aCngPlan['ptCapacity'],
						'coClassNum' => $iCngClass,
						'coStuNum'   => $aCngPlan['ptStuNum'] + ($iCngStu * 300),
						'coPayment'  => ($this->aTeacher['coPayment'] + $iPrice + $iTax),
					);

					$sMailOpt =
						'プラン変更契約'."\n".
						'契約プラン：'.$aActPlan['ptName'].' → '.$aCngPlan['ptName']."\n".
						'契約期間：'.$iRange.'ヶ月（'.date('Y年n月j日').'～'.date('Y年n月j日',strtotime($this->aTeacher['coTermDate'])).'）'."\n".
						'講義数：'.$iActClass.' → '.$iCngClass.'講義';
				break;
				case 'add':
					$result = Model_Payment::getPlan(array(array('ptID','=',$this->aTeacher['ptID']),array('ptSelect','=',1)));
					if (!count($result))
					{
						Log::warning('Not Found Plan - ['.$this->aTeacher['ttID'].'/'.$this->aTeacher['ttName'].'] - '.serialize($aP));
						throw new Exception('現在の契約中プランの内容が確認できませんでした。');
					}
					$aPlan = $result->current();
					if ($aPlan['ptID'] != 3)
					{
						Log::warning('Plan Changed - ['.$this->aTeacher['ttID'].'/'.$this->aTeacher['ttName'].'] - '.serialize($aP));
						throw new Exception('見積もり作成時と現在の契約が一致しません。再度、見積もりを作成してください。');
					}

					$sCode = '';
					$iDiscount = 0;
					if (isset($aP['coupon-code']) && mb_strlen($aP['coupon-code']) == 10)
					{
						$result = Model_Payment::getCoupon(array(array('cpCode','=',$aP['coupon-code']),array('cpTermDate','>',date('Y-m-d'))));
						if (!count($result))
						{
							$iDiscount = 0;
						}
						else
						{
							$aCoupon = $result->current();
							$iDiscount = $aCoupon['cpDiscount'];
							$sCode = $aP['coupon-code'];
						}
					}

					$iCLP = ($aPlan['ptPriceCL'] * ((100 - $iDiscount)/100));

					$iRange = \Clfunc_Common::contractMonths($this->aTeacher['coTermDate']);
					$iClass = (int)$aP['class'];

					if (($this->aTeacher['coClassNum'] + $iClass) > 20)
					{
						Log::warning('Class Over - ['.$this->aTeacher['ttID'].'/'.$this->aTeacher['ttName'].'] - '.serialize($aP));
						throw new Exception('この講義追加により契約講義数が20講義を越えてしまいます。再度、見積もりを作成してください。');
					}

					$iPrice = $iCLP * $iRange * $iClass;
					$iTax = floor($iPrice * $aE['eTax']);

					$coNO = $this->aTeacher['coNO'];

					if ($aP['price'] != $iPrice)
					{
						Log::warning('Price Changed - ['.$this->aTeacher['ttID'].'/'.$this->aTeacher['ttName'].'] - '.serialize($aP));
						throw new Exception('見積もり時と現在で金額に変更があります。再度、現在の状態で見積もりを作成してください。');
					}

					$aUp = array(
						'coClassNum' => ($this->aTeacher['coClassNum'] + $iClass),
						'coPayment' => ($this->aTeacher['coPayment'] + $iPrice + $iTax),
					);

					$sMailOpt =
						'講義数の追加契約'."\n".
						'契約プラン：'.$aPlan['ptName']."\n".
						'契約期間：'.$iRange.'ヶ月（'.date('Y年n月j日').'～'.date('Y年n月j日',strtotime($this->aTeacher['coTermDate'])).'）'."\n".
						'追加講義数：'.$iClass.'講義（合計 '.$aUp['coClassNum'].'講義）';
				break;
			}
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect($sBack);
		}

		try
		{
			$result = Model_Payment::updatePayDoc(array('cNum'=>(int)$aE['cNum'] + 1),$aE);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG','購入処理に失敗しました。時間をおいてから、再度実行してください。('.$e->getMessage().')');
			Response::redirect($sBack);
		}

		$iAmt = $iAmount + $iTax;
		$ppp = new Clfunc_PayPalPayment();
		$ppp->setToken(Input::get('token'));
		$ppp->setPayerID(Input::get('PayerID'));
		$ppp->setAmount($iAmt);

		try
		{
			# 支払い実行
			// $tranID = $ppp->PaymentStart();

			# リダイレクトURL取得
			$ppp->setTeacher($this->aTeacher);
			$ppp->setBNO($number);
			$ppp->setReturn(CL_URL.DS.'t/payment/ppcomplete');
			$url = $ppp->ButtonCreate();
			Session::set('SES_T_PPP_DATASET_'.$number, serialize(array('aIns'=>$aIns,'coNO'=>$coNO,'aUp'=>$aUp,'sMailOpt'=>$sMailOpt)));
			Response::redirect($url);
		}
		catch (\Exception $e)
		{
			Session::set('SES_T_ERROR_MSG','PayPal決済に失敗しました。['.$e->getCode().']'.$e->getMessage());
			Response::redirect('/t/payment');
		}

		try
		{
			$aP = unserialize($aE['purchase']);
			switch ($aP['product'])
			{
				case 'contract':
					if ($this->aTeacher['ptID'] == 1)
					{
						$aIns['coStartDate'] = date('Y-m-d');
						$aUp = array(
								'coTermDate' => date('Y-m-d', strtotime('-1 day')),
						);
						$result = Model_Contract::updateContract($aUp,array(array('ttID','=',$this->aTeacher['ttID']),array('ptID','=',1)));
					}
					$result = Model_Contract::insertContract($aIns);
				break;
				case 'change':
					$result = Model_Contract::updateContract($aUp,array(array('ttID','=',$this->aTeacher['ttID']),array('coNO','=',$this->aTeacher['coNO'])));
				break;
				case 'add':
					$result = Model_Contract::updateContract($aUp,array(array('ttID','=',$this->aTeacher['ttID']),array('coNO','=',$this->aTeacher['coNO'])));
				break;
			}
			$aP['coNO'] = $coNO;
			$aE['purchase'] = serialize($aP);
			$aE['tranID'] = $tranID;
			$result = Model_Payment::setPaymentCard($aE);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG','PayPal決済は完了しましたが、'.CL_SITENAME.'上でエラーが発生しました。申し訳ございませんが'.CL_SITENAME.'の利用を中止してサポートセンターまでご連絡ください。['.$e->getCode().']'.$e->getMessage());
			Response::redirect('/t/payment');
		}

		$result = Model_Payment::getPayDoc(array(array('ttID','=',$this->aTeacher['ttID']),array('eNO','=',$aE['eNO'])));
		if (count($result))
		{
			$aE = $result->current();
		}

		# 購入完了メール
		$email = \Email::forge();
		$email->from(CL_MAIL_FROM, CL_MAIL_SENDER);
		$email->to($this->aTeacher['ttMail']);
		$email->bcc(array(CL_KEIYAKUMAIL,CL_MIYATAMAIL));
		$email->subject('[CL]購入完了のお知らせ（PayPal決済）');
		$body = View::forge('email/t_purchase_paypal', array('aE'=>$aE, 'aT'=>$this->aTeacher, 'sMailOpt'=>$sMailOpt), false);
		$email->body($body);

		try
		{
			$email->send();
		}
		catch (\EmailValidationFailedException $e)
		{
			Log::warning('TeacherPayPalPurchaseStartMail - ' . $e->getMessage());
		}
		catch (\EmailSendingFailedException $e)
		{
			Log::warning('TeacherPayPalPurchaseStartMail - ' . $e->getMessage());
		}

		try
		{
			$aUpdate = array(
				'ttProgress' => 9,
			);
			$result = Model_Teacher::updateTeacher($this->aTeacher['ttID'], $aUpdate);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
		}

		Session::set('SES_T_NOTICE_MSG','PayPalによる購入が完了しました。請求書を発行する場合は、下欄購入履歴よりご利用ください。');
		Session::set('SES_T_PURCHASE_BANK',true);
		Response::redirect('/t/payment');
	}

	public function action_ppcancel()
	{
		$url = Input::referrer();
		$url = parse_url($url);
		// paypal判定
		if (strpos($url['host'], 'paypal.com') === false) {
			return;
		}

		Session::set('SES_T_ERROR_MSG','PayPalによる支払いがキャンセルされました。');
		Response::redirect('/t/payment');
	}

	public function post_ppnotify()
	{
		Log::write('PayPal',print_r(Input::all(),true));
		exit();
	}

	public function post_ppcomplete()
	{
		$url = Input::referrer();
		$url = parse_url($url);
		// paypal判定
		if (strpos($url['host'], 'paypal.com') === false) {
			return;
		}

		try
		{
			if (!$aRes = Input::post(null,false))
			{
				throw new Exception('PayPalによる支払い完了通知が届きませんでした。契約管理センター（'.CL_KEIYAKUMAIL.'）までご連絡ください。');
			}
			if ($aRes['payment_status'] != 'Completed')
			{
				throw new Exception('PayPalによる支払いが完了しませんでした。契約管理センター（'.CL_KEIYAKUMAIL.'）までご連絡ください。');
			}

			$number = $aRes['invoice'];
			$tranID = $aRes['txn_id'];
			$result = Model_Payment::getPayDoc(array(array('ttID','=',$this->aTeacher['ttID']),array('eNO','=',$number),array('status','=',0)));
			if (!count($result))
			{
				throw new Exception('PayPalによる支払いが完了しましたが、契約手続きが継続できません。契約管理センター（'.CL_KEIYAKUMAIL.'）までご連絡ください。');
			}
			$aE = $result->current();

			$aDataSet = Session::get('SES_T_PPP_DATASET_'.$number,false);
			if (!$aDataSet)
			{
				throw new Exception('PayPalによる支払いが完了しましたが、契約手続きが継続できません。契約管理センター（'.CL_KEIYAKUMAIL.'）までご連絡ください。');
			}
		}
		catch (Exception $e)
		{
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect('/t/payment');
		}

		$aDataSet = unserialize($aDataSet);
		$coNO = $aDataSet['coNO'];
		$aIns = $aDataSet['aIns'];
		$aUp  = $aDataSet['aUp'];
		$sMailOpt = $aDataSet['sMailOpt'];

		try
		{
			$aP = unserialize($aE['purchase']);
			switch ($aP['product'])
			{
				case 'contract':
					if ($this->aTeacher['ptID'] == 1)
					{
						$aIns['coStartDate'] = date('Y-m-d');
						$aUp = array(
							'coTermDate' => date('Y-m-d', strtotime('-1 day')),
						);
						$result = Model_Contract::updateContract($aUp,array(array('ttID','=',$this->aTeacher['ttID']),array('ptID','=',1)));
					}
					$result = Model_Contract::insertContract($aIns);
				break;
				case 'change':
					$result = Model_Contract::updateContract($aUp,array(array('ttID','=',$this->aTeacher['ttID']),array('coNO','=',$this->aTeacher['coNO'])));
				break;
				case 'add':
					$result = Model_Contract::updateContract($aUp,array(array('ttID','=',$this->aTeacher['ttID']),array('coNO','=',$this->aTeacher['coNO'])));
				break;
			}
			$aP['coNO'] = $coNO;
			$aE['purchase'] = serialize($aP);
			$aE['tranID'] = $tranID;
			$result = Model_Payment::setPaymentCard($aE);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG','PayPal決済は完了しましたが、'.CL_SITENAME.'上でエラーが発生しました。申し訳ございませんが'.CL_SITENAME.'の利用を中止してサポートセンターまでご連絡ください。['.$e->getCode().']'.$e->getMessage());
			Response::redirect('/t/payment');
		}

		$result = Model_Payment::getPayDoc(array(array('ttID','=',$this->aTeacher['ttID']),array('eNO','=',$aE['eNO'])));
		if (count($result))
		{
			$aE = $result->current();
		}

		# 購入完了メール
		$email = \Email::forge();
		$email->from(CL_MAIL_FROM, CL_MAIL_SENDER);
		$email->to($this->aTeacher['ttMail']);
		$email->bcc(array(CL_KEIYAKUMAIL,CL_MIYATAMAIL));
		$email->subject('[CL]購入完了のお知らせ（PayPal決済）');
		$body = View::forge('email/t_purchase_paypal', array('aE'=>$aE, 'aT'=>$this->aTeacher, 'sMailOpt'=>$sMailOpt), false);
		$email->body($body);

		try
		{
			$email->send();
		}
		catch (\EmailValidationFailedException $e)
		{
			Log::warning('TeacherPayPalPurchaseStartMail - ' . $e->getMessage());
		}
		catch (\EmailSendingFailedException $e)
		{
			Log::warning('TeacherPayPalPurchaseStartMail - ' . $e->getMessage());
		}

		try
		{
			$aUpdate = array(
				'ttProgress' => 9,
			);
			$result = Model_Teacher::updateTeacher($this->aTeacher['ttID'], $aUpdate);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
		}

		Session::set('SES_T_NOTICE_MSG','PayPalによる購入が完了しました。請求書を発行する場合は、下欄購入履歴よりご利用ください。');
		Session::set('SES_T_PURCHASE_BANK',true);
		Session::delete('SES_T_PPP_DATASET_'.$number);
		Response::redirect('/t/payment');
	}

	public function action_charge()
	{
		# パンくずリスト生成
		$this->template->set_global('breadcrumbs',array(
			array('link'=>'/payment','name'=>'見積・購入履歴'),
			array('name'=>'購入手続き')
		));
		# ページタイトル生成
		$this->template->set_global('pagetitle','購入手続き');

		$aPoint = null;
		$result = Model_Payment::getPointRate(array(array('prStatus','=',1)),null,array('prPriority'=>'desc'));
		if (count($result)) {
			$aPoint = $result->as_array();
		}

		$aActClass = null;
		$result = Model_Class::getClassFromTeacher($this->aTeacher['ttID'],1,null,null,array('tp.ctDate'=>'asc'));
		if (count($result)) {
			$aActClass = $result->as_array();
		}

		$data = array(
			'iActCnt'    => count($aActClass),
			'iClsCnt'    => (int)$this->aTeacher['ttCloseNum'],
			'iActPay'    => 0,
			'iLack'      => 0,
			'iRest'      => 0,
			'iStop'      => 0,
			'sRange'     => '0ヶ月',
			'sNextMonth' => date('Y年n月',strtotime('+1month')),
		);
		$data['iActPay'] = $data['iActCnt'] * (int)$this->aTeacher['ptPrice'];
		$data['iLack']   = $data['iActPay'] - (int)$this->aTeacher['ttPoint'];

		$iDPt = 0;

		if ($data['iLack'] > 0)
		{
			$data['iStop'] = ceil($data['iLack'] / (int)$this->aTeacher['ptPrice']);
			$iDPt = ($data['iLack'] < CL_PT_BLINE)? CL_PT_BLINE:$data['iLack'];
		}
		else
		{
			$data['iRest'] = abs($data['iLack']);
			$data['iLack'] = 0;

			if ($data['iActPay'])
			{
				$iRange = floor((int)$this->aTeacher['ttPoint'] / $data['iActPay']);
				if ($iRange > 12)
				{
					$data['sRange'] = floor($iRange/12).'年'.($iRange%12).'ヶ月';
				}
				else
				{
					$data['sRange'] = $iRange.'ヶ月';
				}
			}
		}

		$iDPt = 6000;
		# ポイント金額換算
		$aPPSet = ClFunc_Common::mathPoint(null,$iDPt);

		$data['error'] = null;
		$this->template->content = View::forge('t/payment/charge',$data);
		$this->template->content->set('aActClass',$aActClass);
		$this->template->content->set('aPoint',$aPoint);
		$this->template->content->set('aPPSet',$aPPSet);
		$this->template->javascript = array('cl.t.payment.js');
		return $this->template;
	}

	public function action_estimatecreate() {
		if (!Input::post(null,false))
		{
			Session::set('SES_T_ERROR_MSG','正しく見積情報が指定されていません。');
			Response::redirect($this->eRedirect);
		}
		$aInput = Input::post();

		if (isset($aInput['product']))
		{
			switch ($aInput['product'])
			{
				case 'contract':
					$result = Model_Payment::getPlan(array(array('ptID','=',$aInput['pt']),array('ptSelect','=',1)));
					if (!count($result))
					{
						Session::set('SES_T_ERROR_MSG','正しく購入情報が指定されていません。');
						Response::redirect('/t/payment/contract/'.$aInput['pt']);
					}
					$aPlan = $result->current();

					$sCode = '';
					$iDiscount = 0;
					if (isset($aInput['coupon-code']) && mb_strlen($aInput['coupon-code']) == 10)
					{
						$result = Model_Payment::getCoupon(array(array('cpCode','=',$aInput['coupon-code']),array('cpTermDate','>',date('Y-m-d'))));
						if (!count($result))
						{
							$iDiscount = 0;
						}
						else
						{
							$aCoupon = $result->current();
							if (($aCoupon['cpPaymentType'] & (int)$aInput['billing']) && $aCoupon['cpRange'] <= $aInput['range'])
							{
								$iDiscount = $aCoupon['cpDiscount'];
								$sCode = $aInput['coupon-code'];
							}
						}
					}

					if (!$aInput['range'])
					{
						Session::set('SES_T_ERROR_MSG','正しく購入情報が指定されていません。');
						Response::redirect('/t/payment/contract/'.$aInput['pt']);
					}

					$iClassPrice = $aPlan['ptPriceCL'] * ((100 - $iDiscount)/100);
					$iStuPrice = $aPlan['ptPriceStu'] * ((100 - $iDiscount)/100);
					$iPrice = ($aPlan['ptPriceID'] * (int)$aInput['range']) +
					 ($iClassPrice * (int)$aInput['class'] * (int)$aInput['range']) +
					 ($iStuPrice * (int)$aInput['stu'] * (int)$aInput['range']);

					$aPoint = array(
						'pt' => 0,
						'pr' => $iPrice,
						'purchase' => base64_encode(serialize(array(
							'product' => 'contract',
							'pt' => $aInput['pt'],
							'coupon-code' => $sCode,
							'range' => $aInput['range'],
							'class' => $aInput['class'],
							'stu'   => $aInput['stu'],
							'price' => $iPrice,
						)))
					);

					$aBack = array('link'=>'/payment/contract/'.$aInput['pt'], 'name'=>'プランのご購入手続き');

				break;
				case 'change':
					$result = Model_Payment::getPlan(array(array('ptID','=',$this->aTeacher['ptID']),array('ptSelect','=',1)));
					if (!count($result))
					{
						Session::set('SES_T_ERROR_MSG','契約中プランが見つかりません。');
						Response::redirect($sBack);
					}
					$aActPlan = $result->current();

					$result = Model_Payment::getPlan(array(array('ptID','=',$aInput['pt']),array('ptSelect','=',1)));
					if (!count($result))
					{
						Session::set('SES_T_ERROR_MSG','変更対象プランが見つかりません。');
						Response::redirect($sBack);
					}
					$aCngPlan = $result->current();

					$iIDP = ($aCngPlan['ptPriceID'] - $aActPlan['ptPriceID']);
					$iRange = \Clfunc_Common::contractMonths($this->aTeacher['coTermDate']);

					$sCode = '';
					$iDiscount = 0;
					if (isset($aInput['coupon-code']) && mb_strlen($aInput['coupon-code']) == 10)
					{
						$result = Model_Payment::getCoupon(array(array('cpCode','=',$aInput['coupon-code']),array('cpTermDate','>',date('Y-m-d'))));
						if (!count($result))
						{
							$iDiscount = 0;
						}
						else
						{
							$aCoupon = $result->current();
							if (($aCoupon['cpPaymentType'] & (int)$aInput['billing']) && $aCoupon['cpRange'] <= $iRange)
							{
								$iDiscount = $aCoupon['cpDiscount'];
								$sCode = $aInput['coupon-code'];
							}
						}
					}


					$iCLP = ($aCngPlan['ptPriceCL'] * ((100 - $iDiscount)/100));
					$iCLPd = ($iCLP - $aActPlan['ptPriceCL']);
					$iActClass = $this->aTeacher['coClassNum'];
					$iCngClass = (int)$aInput['class'];

					$iSTP = ($aCngPlan['ptPriceStu'] * ((100 - $iDiscount)/100));
					$iSTPd = ($iSTP - $aActPlan['ptPriceStu']);
					$iActStu = $this->aTeacher['coStuNum'];
					$iCngStu = (int)$aInput['stu'];

					$iPrice = ($iIDP * $iRange) +
					 ($iCLPd * $iRange * $iActClass) + ($iCLP * $iRange * ($iCngClass - $iActClass)) +
					 ($iSTPd * $iRange * $iActStu) + ($iSTP * $iRange * ($iCngStu - $iActStu));

					if ($iPrice <= 0)
					{
						try
						{
							$aUp = array(
								'ptID' => $aCngPlan['ptID'],
								'coStuNum' => $aCngPlan['ptStuNum'] + ($iCngStu * $aCngPlan['ptStuNum']),
								'coCapacity' => $aCngPlan['ptCapacity'],
								'coClassNum' => $aInput['class'],
							);
							$result = Model_Contract::updateContract($aUp,array(array('ttID','=',$this->aTeacher['ttID']),array('coNO','=',$this->aTeacher['coNO'])));
						}
						catch (Exception $e)
						{
							\Clfunc_Common::LogOut($e,__CLASS__);
							Session::set('SES_T_ERROR_MSG',$e->getMessage());
							Response::redirect($this->eRedirect);
						}

						Session::set('SES_T_NOTICE_MSG',$aCngPlan['ptName'].'プランへの変更が完了しました。');
						Response::redirect('/t/payment');
					}

					$aPoint = array(
						'pt' => 0,
						'pr' => $iPrice,
						'purchase' => base64_encode(serialize(array(
							'product' => 'change',
							'pt' => $aInput['pt'],
							'coNO' => $this->aTeacher['coNO'],
							'coupon-code' => $sCode,
							'class' => $aInput['class'],
							'stu'   => $aInput['stu'],
							'price' => $iPrice,
						)))
					);

					$aBack = array('link'=>'/payment/change/', 'name'=>'プラン変更の手続き');
				break;
				case 'add':
					$result = Model_Payment::getPlan(array(array('ptID','=',$this->aTeacher['ptID']),array('ptSelect','=',1)));
					if (!count($result))
					{
						Session::set('SES_T_ERROR_MSG','契約中プランが見つかりません。');
						Response::redirect($sBack);
					}
					$aPlan = $result->current();

					$iRange = \Clfunc_Common::contractMonths($this->aTeacher['coTermDate']);

					$sCode = '';
					$iDiscount = 0;
					if (isset($aInput['coupon-code']) && mb_strlen($aInput['coupon-code']) == 10)
					{
						$result = Model_Payment::getCoupon(array(array('cpCode','=',$aInput['coupon-code']),array('cpTermDate','>',date('Y-m-d'))));
						if (!count($result))
						{
							$iDiscount = 0;
						}
						else
						{
							$aCoupon = $result->current();
							if (($aCoupon['cpPaymentType'] & (int)$aInput['billing']) && $aCoupon['cpRange'] <= $iRange)
							{
								$iDiscount = $aCoupon['cpDiscount'];
								$sCode = $aInput['coupon-code'];
							}
						}
					}

					$iCLP = ($aPlan['ptPriceCL'] * ((100 - $iDiscount)/100));

					$iClass = (int)$aInput['class'];

					$iPrice = $iCLP * $iRange * $iClass;

					$aPoint = array(
						'pt' => 0,
						'pr' => $iPrice,
						'purchase' => base64_encode(serialize(array(
							'product' => 'add',
							'pt' => $aInput['pt'],
							'coNO' => $this->aTeacher['coNO'],
							'coupon-code' => $sCode,
							'class' => $aInput['class'],
							'stu'   => 0,
							'price' => $iPrice,
						)))
					);

					$aBack = array('link'=>'/payment/add/', 'name'=>'講義追加の手続き');
				break;
				default:
					Session::set('SES_T_ERROR_MSG','正しく見積情報が指定されていません。');
					Response::redirect($this->eRedirect);
				break;
			}
		}
		else
		{
			$aPurchase = unserialize(base64_decode($aInput['purchase']));
			$aPoint = array(
				'pt' => 0,
				'pr' => $aPurchase['price'],
				'purchase' => $aInput['purchase'],
			);

			switch ($aPurchase['product'])
			{
				case 'contract':
					$aBack = array('link'=>'/payment/contract/'.$aPurchase['pt'], 'name'=>'プランのご購入手続き');
				break;
				case 'change':
					$aBack = array('link'=>'/payment/change/', 'name'=>'プラン変更の手続き');
				break;
				case 'add':
					$aBack = array('link'=>'/payment/add/', 'name'=>'講義追加の手続き');
				break;
			}
		}

		$aBread = array(
			array('link'=>'/payment','name'=>'見積・購入履歴'),
			array('link'=>'/payment/product','name'=>'ご購入商品の選択'),
			$aBack,
			array('name'=>'見積作成'),
		);
		$this->template->set_global('breadcrumbs',$aBread);

		switch ($aInput['mode'])
		{
			case 'start':
				# ページタイトル生成
				$this->template->set_global('pagetitle','見積作成');

				$aInput['pubdate'] = date('Y年n月j日');
				$aInput['sendto']  = $this->aTeacher['cmName'].' '.$this->aTeacher['ttName'];

				# 内訳の初期値を設定
				$aInput['dname'][0]  = CL_SITENAME.' 利用料金';
				$aInput['dprice'][0] = $aPoint['pr'];
				$aInput['dnum'][0]   = 1;
				$aInput['dunit'][0]  = '式';

				$aInput['error'] = null;
				$this->template->content = View::forge('t/payment/estimate/create',$aInput);
				$this->template->content->set('aPoint',$aPoint);
				$this->template->javascript = array('cl.t.payment.js');
				return $this->template;
			break;
			case 'input':
				# ページタイトル生成
				$this->template->set_global('pagetitle','見積情報確認');
				$aInput['error'] = null;

				$val = Validation::forge();
				$val->add_callable('Helper_CustomValidation');
				$val->add('pubdate', '発行日')
					->add_rule('required')
					->add_rule('date_ja');
				$val->add('sendto', '宛名')
					->add_rule('required')
					->add_rule('max_length',25);
				if (!$val->run())
				{
					$aInput['error'] = $val->error();
					$aInput['error']['estimate_error'] = '入力内容に誤りがあります。各項目をご確認ください。';
				}
				$iK = 0;
				foreach ($aInput['dprice'] as $i => $p)
				{
					$iK += ($p * $aInput['dnum'][$i]);
				}
				if ($iK != $aPoint['pr'])
				{
					$aInput['error']['detail'] = '内訳金額の合計と小計が一致していません。';
					$aInput['error']['estimate_error'] = '入力内容に誤りがあります。各項目をご確認ください。';
				}

				if (!is_null($aInput['error']))
				{
					$this->template->content = View::forge('t/payment/estimate/create',$aInput);
					$this->template->content->set('aPoint',$aPoint);
					$this->template->javascript = array('cl.t.payment.js');
					return $this->template;
				}

				$aInput['number'] = self::EstimateNumber($aInput['pubdate']);
				$aInput['tax_rate'] = CL_TAX_RATE;
				$aInput['ses_hash'] = date('YmdHis.').mt_rand();
				Session::set('SES_T_ESTIMATE_TEMP',serialize(array($aInput['ses_hash']=>$aInput)));

				\ClFunc_Pdf::createEstimatePDF($this->aTeacher['ttID'],$aInput,$aPoint['pr'],true,true);

				$this->template->content = View::forge('t/payment/estimate/check',$aInput);
				$this->template->content->set('aPoint',$aPoint);
				$this->template->javascript = array('cl.t.payment.js');
				return $this->template;
			break;
			case 'check':
			case 'save':
			case 'back':
				$sTemp = Session::get('SES_T_ESTIMATE_TEMP',false);
				try
				{
					if (!isset($aInput['ses_hash']) || !$sTemp)
					{
						Log::warning('ESTIMATE-SES-ERR ['.$aInput['ses_hash'].'] '.$sTemp);
						throw new Exception('正しく見積情報が指定されていません。');
					}
					$aTemp = unserialize($sTemp);
					if (!isset($aTemp[$aInput['ses_hash']]))
					{
						Log::warning('ESTIMATE-SES-ERR ['.$aInput['ses_hash'].'] '.$sTemp);
						throw new Exception('正しく見積情報が指定されていません。');
					}
					$aCheck = $aTemp[$aInput['ses_hash']];
					Session::delete('SES_T_KREPORT_TEMP');
				}
				catch(Exception $e)
				{
					\Clfunc_Common::LogOut($e,__CLASS__);
					Session::set('SES_T_ERROR_MSG',$e->getMessage());
					Response::redirect($this->eRedirect);
				}
				if ($aInput['mode'] == 'back')
				{
					# ページタイトル生成
					$this->template->set_global('pagetitle','見積作成');

					$aCheck['error'] = null;
					$this->template->content = View::forge('t/payment/estimate/create',$aCheck);
					$this->template->content->set('aPoint',$aPoint);
					$this->template->javascript = array('cl.t.payment.js');
					return $this->template;
				}

				// 登録データ生成
				$aDetail = array(
					'dname' => $aCheck['dname'],
					'dprice' => $aCheck['dprice'],
					'dnum'   => $aCheck['dnum'],
					'dunit'  => $aCheck['dunit'],
				);

				$iDate = Clfunc_Common::strtotime_ja($aCheck['pubdate']);
				$aInsert = array(
					'ttID'       => $this->aTeacher['ttID'],
					'year'       => ((date('n',$iDate) <= 3)? (int)date('Y',$iDate)-1:(int)date('Y',$iDate)),
					'status'     => 0,
					'point'      => $aPoint['pt'],
					'purchase'   => base64_decode($aPoint['purchase']),
					'eNO'        => $aCheck['number'],
					'eDate'      => date('Y-m-d',$iDate),
					'eName'      => $aCheck['sendto'],
					'eTitle'     => CL_SITENAME.' お見積',
					'eBilling'   => $aCheck['billing'],
					'ePrice'     => $aPoint['pr'],
					'eTax'       => CL_TAX_RATE,
					'eDetail'    => serialize($aDetail),
					'updateDate' => date('YmdHis'),
					'createDate' => date('YmdHis'),
				);

				try
				{
					$result = Model_Payment::insertPayDoc($aInsert);
				}
				catch (Exception $e)
				{
					\Clfunc_Common::LogOut($e,__CLASS__);
					Session::set('SES_T_ERROR_MSG',$e->getMessage());
					Response::redirect($this->eRedirect);
				}

				if ($this->aTeacher['ptID'] < 2)
				{
					try
					{
						$aUpdate = array(
								'ttProgress' => 6,
						);
						$result = Model_Teacher::updateTeacher($this->aTeacher['ttID'], $aUpdate);
					}
					catch (Exception $e)
					{
						\Clfunc_Common::LogOut($e,__CLASS__);
						Session::set('SES_T_ERROR_MSG',$e->getMessage());
						Response::redirect($this->eRedirect);
					}
				}

				if ($aInput['mode'] == 'save')
				{
					Session::set('SES_T_NOTICE_MSG','見積を保存しました。（見積番号 '.$aCheck['number'].'）');
					Response::redirect('/t/payment');
				}
				else
				{
					Response::redirect('/t/payment/purchase/'.$aCheck['number']);
				}

			break;
		}
	}

	public function action_estimateedit($number)
	{
		$aBread = array(
			array('link'=>'/payment','name'=>'見積・購入履歴'),
			array('name'=>'見積情報変更'),
		);
		$this->template->set_global('breadcrumbs',$aBread);

		if (!isset($number))
		{
			Session::set('SES_T_ERROR_MSG','正しく見積情報が指定されていません。');
			Response::redirect('/t/payment');
		}
		$result = Model_Payment::getPayDoc(array(array('ttID','=',$this->aTeacher['ttID']),array('eNO','=',$number),array('status','=',0)));
		if (!count($result))
		{
			Session::set('SES_T_ERROR_MSG','正しく見積情報が指定されていません。');
			Response::redirect('/t/payment');
		}
		$aE = $result->current();

		if (!Input::post(null,false))
		{
			# ページタイトル生成
			$this->template->set_global('pagetitle','見積情報変更');

			$aInput = unserialize($aE['eDetail']);
			$aInput['sendto'] = $aE['eName'];
			$aInput['error'] = null;

			$this->template->content = View::forge('t/payment/estimate/edit',$aInput);
			$this->template->content->set('aE',$aE);
			$this->template->javascript = array('cl.t.payment.js');
			return $this->template;
		}
		$aInput = Input::post();

		switch ($aInput['mode'])
		{
			case 'input':
				# ページタイトル生成
				$this->template->set_global('pagetitle','見積情報確認');
				$aInput['error'] = null;

				$val = Validation::forge();
				$val->add_callable('Helper_CustomValidation');
				$val->add('sendto', '宛名')
					->add_rule('required')
					->add_rule('max_length',25);
				if (!$val->run())
				{
					$aInput['error'] = $val->error();
					$aInput['error']['estimate_error'] = '入力内容に誤りがあります。各項目をご確認ください。';
				}
				$iK = 0;
				foreach ($aInput['dprice'] as $i => $p)
				{
					$iK += ($p * $aInput['dnum'][$i]);
				}
				if ($iK != $aE['ePrice'])
				{
					$aInput['error']['detail'] = '内訳金額の合計と小計が一致していません。';
					$aInput['error']['estimate_error'] = '入力内容に誤りがあります。各項目をご確認ください。';
				}

				if (!is_null($aInput['error']))
				{
					$this->template->content = View::forge('t/payment/estimate/edit',$aInput);
					$this->template->content->set('aE',$aE);
					$this->template->javascript = array('cl.t.payment.js');
					return $this->template;
				}

				$aInput['number']   = $aE['eNO'];
				$aInput['pubdate']  = date('Y年n月j日',strtotime($aE['eDate']));
				$aInput['tax_rate'] = $aE['eTax'];
				$aInput['billing']  = $aE['eBilling'];
				$aInput['ses_hash'] = date('YmdHis.').mt_rand();
				Session::set('SES_T_ESTIMATE_TEMP',serialize(array($aInput['ses_hash']=>$aInput)));

				\ClFunc_Pdf::createEstimatePDF($this->aTeacher['ttID'],$aInput,$aE['ePrice'],false,true);

				$this->template->content = View::forge('t/payment/estimate/check',$aInput);
				$this->template->content->set('aE',$aE);
				$this->template->javascript = array('cl.t.payment.js');
				return $this->template;
			break;
			case 'check':
			case 'save':
			case 'back':
				$sTemp = Session::get('SES_T_ESTIMATE_TEMP',false);
				try
				{
					if (!isset($aInput['ses_hash']) || !$sTemp)
					{
						throw new Exception('正しく見積情報が指定されていません。');
					}
					$aTemp = unserialize($sTemp);
					if (!isset($aTemp[$aInput['ses_hash']]))
					{
						throw new Exception('正しく見積情報が指定されていません。');
					}
					$aCheck = $aTemp[$aInput['ses_hash']];
					Session::delete('SES_T_KREPORT_TEMP');
				}
				catch(Exception $e)
				{
					\Clfunc_Common::LogOut($e,__CLASS__);
					Session::set('SES_T_ERROR_MSG',$e->getMessage());
					Response::redirect($this->eRedirect);
				}
				if ($aInput['mode'] == 'back')
				{
					# ページタイトル生成
					$this->template->set_global('pagetitle','見積情報変更');

					$aCheck['error'] = null;
					$this->template->content = View::forge('t/payment/estimate/edit',$aCheck);
					$this->template->content->set('aE',$aE);
					$this->template->javascript = array('cl.t.payment.js');
					return $this->template;
				}

				// 登録データ生成
				$aDetail = array(
					'dname'  => $aCheck['dname'],
					'dprice' => $aCheck['dprice'],
					'dnum'   => $aCheck['dnum'],
					'dunit'  => $aCheck['dunit'],
				);

				$aUpdate = array(
					'status'     => 0,
					'eName'      => $aCheck['sendto'],
					'eDetail'    => serialize($aDetail),
					'updateDate' => date('YmdHis'),
				);

				try
				{
					$result = Model_Payment::updatePayDoc($aUpdate,$aE);
				}
				catch (Exception $e)
				{
					\Clfunc_Common::LogOut($e,__CLASS__);
					Session::set('SES_T_ERROR_MSG',$e->getMessage());
					Response::redirect($this->eRedirect);
				}

				$sFilePath = CL_FILEPATH.DS.$this->aTeacher['ttID'].DS.'payment_pdf'.DS;
				if (file_exists($sFilePath.$aE['eNO'].'-T.pdf'))
				{
					rename($sFilePath.$aE['eNO'].'-T.pdf',$sFilePath.$aE['eNO'].'.pdf');
				}

				if ($aInput['mode'] == 'save')
				{
					Session::set('SES_T_NOTICE_MSG','見積を保存しました。（見積番号 '.$aE['eNO'].'）');
					Response::redirect('/t/payment');
				}
				else
				{
					Response::redirect('/t/payment/purchase/'.$aE['eNO']);
				}

				break;
		}
	}

	public function action_estimatedelete($number)
	{
		if (!isset($number))
		{
			Session::set('SES_T_ERROR_MSG','正しく見積情報が指定されていません。');
			Response::redirect('/t/payment');
		}
		$result = Model_Payment::getPayDoc(array(array('ttID','=',$this->aTeacher['ttID']),array('eNO','=',$number),array('status','=',0)));
		if (!count($result))
		{
			Session::set('SES_T_ERROR_MSG','正しく見積情報が指定されていません。');
			Response::redirect('/t/payment');
		}
		$aE = $result->current();

		try
		{
			$result = Model_Payment::deletePayDoc($aE['eNO']);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}
		$sFilePath = CL_FILEPATH.DS.$this->aTeacher['ttID'].DS.'payment_pdf'.DS.$aE['eNO'].'.pdf';
		if (file_exists($sFilePath))
		{
			unlink($sFilePath);
		}
		Session::set('SES_T_NOTICE_MSG','見積を削除しました。（見積番号 '.$aE['eNO'].'）');
		Response::redirect('/t/payment');
	}


	public function action_billpublish($number)
	{
		$this->template->content = View::forge('t/payment/bill/edit');
		$this->template->javascript = array('cl.t.payment.js');

		$aBread = array(
			array('link'=>'/payment','name'=>'見積・購入履歴'),
			array('name'=>'請求書発行'),
		);
		$this->template->set_global('breadcrumbs',$aBread);
		# ページタイトル生成
		$this->template->set_global('pagetitle','請求書発行');

		if (!isset($number))
		{
			Session::set('SES_T_ERROR_MSG','正しく請求情報が指定されていません。');
			Response::redirect('/t/payment');
		}
		$result = Model_Payment::getPayDoc(array(array('ttID','=',$this->aTeacher['ttID']),array('eNO','=',$number),array('status','>',0)));
		if (!count($result))
		{
			Session::set('SES_T_ERROR_MSG','正しく請求情報が指定されていません。');
			Response::redirect('/t/payment');
		}
		$aE = $result->current();
		$this->template->content->set('aE',$aE);
		$this->template->content->set_safe('aP',unserialize($aE['purchase']));

		$aP = unserialize($aE['purchase']);
		if ($aP['product'] == 'contract')
		{
			$result = Model_Contract::getContract(array(array('ttID','=',$aE['ttID']),array('coNO','=',$aP['coNO'])));
			if (!count($result))
			{
				Session::set('SES_T_ERROR_MSG','契約情報が確認できませんでした。契約管理センター（keiyaku@netman.co.jp）にご確認ください。');
				Response::redirect($this->eRedirect);
			}
			$aCon = $result->current();
		}
		else
		{
			/*
			$result = Model_Contract::getContract(array(array('ttID','=',$aE['ttID'])),null,array('coNO'=>'desc'));
			if (!count($result))
			{
				Session::set('SES_T_ERROR_MSG','契約情報が確認できませんでした。契約管理センター（keiyaku@netman.co.jp）にご確認ください。');
				Response::redirect($this->eRedirect);
			}
			$aTemp = $result->as_array();
			$aCon = $aTemp[0];
			*/
			$aCon['coStartDate'] = $aE['bDate'];
		}
		$this->template->content->set('aCon',$aCon);

		if ($aE['bNum'] <= 0)
		{
			Session::set('SES_T_ERROR_MSG','限度回数を超えているため、請求書の発行はできません。');
			Response::redirect('/t/payment');
		}

		if (!Input::post(null,false))
		{

			$aInput = array(
				'sendto' => ($aE['bName'])? $aE['bName']:$aE['eName'],
				'pubdate' => date('Y年n月j日', strtotime((($aE['bPubDate'] != '0000-00-00')? $aE['bPubDate']:$aCon['coStartDate']))),
				'error'  => null,
			);

			$this->template->content->set($aInput);
			return $this->template;
		}
		$aInput = Input::post();

		$val = Validation::forge();
		$val->add_callable('Helper_CustomValidation');
		$val->add('pubdate', '請求日')
			->add_rule('required')
			->add_rule('date_ja');
		$val->add('sendto', '宛名')
		->add_rule('required')
		->add_rule('max_length',25);
		if (!$val->run())
		{
			$aInput['error'] = $val->error();
			$this->template->content->set($aInput);
			return $this->template;
		}

		$iPub = \Clfunc_Common::strtotime_ja($aInput['pubdate']);

		if ($iPub < strtotime(date('Y/m/d',strtotime($aE['bDate']))) || $iPub > strtotime(date('Y/m/t',strtotime($aCon['coStartDate']))))
		{
			$aInput['error']['pubdate'] = '請求日は、購入日から契約開始月の末日までの間で指定してください。';
			$this->template->content->set($aInput);
			return $this->template;
		}

		$aUpdate = array(
			'bName'    => $aInput['sendto'],
			'bNum'     => (int)$aE['bNum'] - 1,
			'bPubDate' => date('Ymd', $iPub),
		);
		try
		{
			$result = Model_Payment::updatePayDoc($aUpdate,$aE);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		$aE['bPubDate'] = date('Y-m-d', $iPub);
		\ClFunc_Pdf::createBillPDF($this->aTeacher['ttID'],$aInput['sendto'],$aE,true);

		Response::redirect('/t/payment/billshow/'.$aE['eNO']);
	}


	public function action_billshow($number = null)
	{
		$aBread = array(
				array('link'=>'/payment','name'=>'見積・購入履歴'),
				array('name'=>'請求書発行'),
		);
		$this->template->set_global('breadcrumbs',$aBread);
		# ページタイトル生成
		$this->template->set_global('pagetitle','請求書発行');

		if (!isset($number))
		{
			Session::set('SES_T_ERROR_MSG','正しく請求情報が指定されていません。');
			Response::redirect('/t/payment');
		}
		$result = Model_Payment::getPayDoc(array(array('ttID','=',$this->aTeacher['ttID']),array('eNO','=',$number),array('status','>',0)));
		if (!count($result))
		{
			Session::set('SES_T_ERROR_MSG','正しく請求情報が指定されていません。');
			Response::redirect('/t/payment');
		}
		$aE = $result->current();

		if ($aE['bNum'] < 0)
		{
			Session::set('SES_T_ERROR_MSG','限度回数を超えているため、請求書の発行はできません。');
			Response::redirect('/t/payment');
		}

		$this->template->content = View::forge('t/payment/bill/show');
		$this->template->content->set('aE',$aE);
		$this->template->javascript = array('cl.t.payment.js');
		return $this->template;
	}


	public function action_receiptpublish($number)
	{
		$aBread = array(
				array('link'=>'/payment','name'=>'見積・購入履歴'),
				array('name'=>'領収書発行'),
		);
		$this->template->set_global('breadcrumbs',$aBread);
		# ページタイトル生成
		$this->template->set_global('pagetitle','領収書発行');

		if (!isset($number))
		{
			Session::set('SES_T_ERROR_MSG','正しく請求情報が指定されていません。');
			Response::redirect('/t/payment');
		}
		$result = Model_Payment::getPayDoc(array(array('ttID','=',$this->aTeacher['ttID']),array('eNO','=',$number),array('status','>',0)));
		if (!count($result))
		{
			Session::set('SES_T_ERROR_MSG','正しく請求情報が指定されていません。');
			Response::redirect('/t/payment');
		}
		$aE = $result->current();

		if ($aE['rNum'] <= 0)
		{
			Session::set('SES_T_ERROR_MSG','限度回数を超えているため、領収書の発行はできません。');
			Response::redirect('/t/payment');
		}

		if (!Input::post(null,false))
		{
			$aInput = array(
					'sendto' => ($aE['rName'])? $aE['rName']:($aE['bName'])? $aE['bName']:$aE['eName'],
					'note'   => ($aE['rNote'])? $aE['rNote']:CL_SITENAME.' ご利用料として',
					'error'  => null,
			);

			$this->template->content = View::forge('t/payment/receipt/edit',$aInput);
			$this->template->content->set('aE',$aE);
			$this->template->javascript = array('cl.t.payment.js');
			return $this->template;
		}
		$aInput = Input::post();

		$val = Validation::forge();
		$val->add_callable('Helper_CustomValidation');
		$val->add('sendto', '宛名')
			->add_rule('required')
			->add_rule('max_length',25);
		$val->add('note', '但書')
			->add_rule('required')
			->add_rule('max_length',25);
		if (!$val->run())
		{
			$aInput['error'] = $val->error();
			$this->template->content = View::forge('t/payment/receipt/edit',$aInput);
			$this->template->content->set('aE',$aE);
			$this->template->javascript = array('cl.t.payment.js');
			return $this->template;
		}

		$aUpdate = array(
			'rName' => $aInput['sendto'],
			'rNote' => $aInput['note'],
			'rNum'  => (int)$aE['rNum'] - 1,
		);
		try
		{
			$result = Model_Payment::updatePayDoc($aUpdate,$aE);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		\ClFunc_Pdf::createReceiptPDF($this->aTeacher['ttID'],$aInput,$aE,true);

		Response::redirect('/t/payment/receiptshow/'.$aE['eNO']);
	}


	public function action_receiptshow($number = null)
	{
		$aBread = array(
				array('link'=>'/payment','name'=>'見積・購入履歴'),
				array('name'=>'領収書発行'),
		);
		$this->template->set_global('breadcrumbs',$aBread);
		# ページタイトル生成
		$this->template->set_global('pagetitle','領収書発行');

		if (!isset($number))
		{
			Session::set('SES_T_ERROR_MSG','正しく請求情報が指定されていません。');
			Response::redirect('/t/payment');
		}
		$result = Model_Payment::getPayDoc(array(array('ttID','=',$this->aTeacher['ttID']),array('eNO','=',$number),array('status','>',0)));
		if (!count($result))
		{
			Session::set('SES_T_ERROR_MSG','正しく請求情報が指定されていません。');
			Response::redirect('/t/payment');
		}
		$aE = $result->current();

		if ($aE['rNum'] < 0)
		{
			Session::set('SES_T_ERROR_MSG','限度回数を超えているため、領収書の発行はできません。');
			Response::redirect('/t/payment');
		}

		$this->template->content = View::forge('t/payment/receipt/show');
		$this->template->content->set('aE',$aE);
		$this->template->javascript = array('cl.t.payment.js');
		return $this->template;
	}

	/**
	 * ライセンス証明書（納品書）設定
	 *
	 * @param string $number
	 */
	public function action_licensepublish($number = null)
	{
		$sName = 'ライセンス証明書（納品書）';
		$aBread = array(
				array('link'=>'/payment','name'=>'見積・購入履歴'),
				array('name'=>$sName.'発行'),
		);
		$this->template->set_global('breadcrumbs',$aBread);
		# ページタイトル生成
		$this->template->set_global('pagetitle',$sName.'発行');

		if (!isset($number))
		{
			Session::set('SES_T_ERROR_MSG','正しく請求情報が指定されていません。');
			Response::redirect('/t/payment');
		}
		$result = Model_Payment::getPayDoc(array(array('ttID','=',$this->aTeacher['ttID']),array('eNO','=',$number),array('status','>',0)));
		if (!count($result))
		{
			Session::set('SES_T_ERROR_MSG','正しく請求情報が指定されていません。');
			Response::redirect('/t/payment');
		}
		$aE = $result->current();

		if (!$aE['purchase'])
		{
			Session::set('SES_T_ERROR_MSG','2016年10月以前の契約について、'.$sName.'の発行はできません。');
			Response::redirect('/t/payment');
		}

		if ($aE['lNum'] <= 0)
		{
			Session::set('SES_T_ERROR_MSG','限度回数を超えているため、'.$sName.'の発行はできません。');
			Response::redirect('/t/payment');
		}

		if (!Input::post(null,false))
		{
			$aInput = array(
				'name'    => ($aE['lName'])? $aE['lName']:(($this->aTeacher['ttName'])? $this->aTeacher['ttName']:$this->aTeacher['ttMail']),
				'org'     => ($aE['lOrg'])?  $aE['lOrg']:$this->aTeacher['cmName'],
				'pubdate' => date('Y年n月j日'),
				'error' => null,
			);

			$this->template->content = View::forge('t/payment/license/edit',$aInput);
			$this->template->content->set('aE',$aE);
			$this->template->javascript = array('cl.t.payment.js');
			return $this->template;
		}
		$aInput = Input::post();

		$val = Validation::forge();
		$val->add_callable('Helper_CustomValidation');
		$val->add('pubdate', '発行日')
			->add_rule('required')
			->add_rule('date_ja');
		$val->add('name', '契約者名')
			->add_rule('required')
			->add_rule('max_length',15);
		$val->add('org', '団体名')
			->add_rule('required')
			->add_rule('max_length',15);
		if (!$val->run())
		{
			$aInput['error'] = $val->error();
			$this->template->content = View::forge('t/payment/license/edit',$aInput);
			$this->template->content->set('aE',$aE);
			$this->template->javascript = array('cl.t.payment.js');
			return $this->template;
		}

		$aUpdate = array(
			'lName' => $aInput['name'],
			'lOrg'  => $aInput['org'],
			'lDate' => date('Ymd', \Clfunc_Common::strtotime_ja($aInput['pubdate'])),
			'lNum'  => (int)$aE['lNum'] - 1,
		);
		try
		{
			$result = Model_Payment::updatePayDoc($aUpdate,$aE);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		$aP = unserialize($aE['purchase']);
		$result = Model_Payment::getPlan(array(array('ptID','=',$aP['pt']),array('ptSelect','=',1)));
		if (!count($result))
		{
			Session::set('SES_T_ERROR_MSG','購入プラン情報が取得できませんでした。契約管理センター（keiyaku@netman.co.jp）にご確認ください。');
			Response::redirect($this->eRedirect);
		}
		$aPlan = $result->current();

		if (isset($aP['coNO']))
		{
			$result = Model_Contract::getContract(array(array('ttID','=',$aE['ttID']),array('coNO','=',$aP['coNO'])));
			if (!count($result))
			{
				Session::set('SES_T_ERROR_MSG','契約情報が確認できませんでした。契約管理センター（keiyaku@netman.co.jp）にご確認ください。');
				Response::redirect($this->eRedirect);
			}
			$aCon = $result->current();
		}
		else
		{
			$result = Model_Contract::getContract(array(array('ttID','=',$aE['ttID'])),null,array('coNO'=>'desc'));
			if (!count($result))
			{
				Session::set('SES_T_ERROR_MSG','契約情報が確認できませんでした。契約管理センター（keiyaku@netman.co.jp）にご確認ください。');
				Response::redirect($this->eRedirect);
			}
			$aTemp = $result->as_array();
			$aCon = $aTemp[0];
		}

		\ClFunc_Pdf::createLicensePDF($this->aTeacher['ttID'],$aInput,$aE,$aCon,$aPlan,true);

		Response::redirect('/t/payment/licenseshow/'.$aE['eNO']);
	}

	/**
	 * ライセンス証明書（納品書）表示
	 *
	 * @param string $number
	 */
	public function action_licenseshow($number = null)
	{
		$sName = 'ライセンス証明書（納品書）';
		$aBread = array(
				array('link'=>'/payment','name'=>'見積・購入履歴'),
				array('name'=>$sName.'発行'),
		);
		$this->template->set_global('breadcrumbs',$aBread);
		# ページタイトル生成
		$this->template->set_global('pagetitle',$sName.'発行');

		if (!isset($number))
		{
			Session::set('SES_T_ERROR_MSG','正しく請求情報が指定されていません。');
			Response::redirect('/t/payment');
		}
		$result = Model_Payment::getPayDoc(array(array('ttID','=',$this->aTeacher['ttID']),array('eNO','=',$number),array('status','>',0)));
		if (!count($result))
		{
			Session::set('SES_T_ERROR_MSG','正しく請求情報が指定されていません。');
			Response::redirect('/t/payment');
		}
		$aE = $result->current();

		if ($aE['lNum'] < 0)
		{
			Session::set('SES_T_ERROR_MSG','限度回数を超えているため、'.$sName.'の発行はできません。');
			Response::redirect('/t/payment');
		}

		$this->template->content = View::forge('t/payment/license/show');
		$this->template->content->set('aE',$aE);
		$this->template->javascript = array('cl.t.payment.js');
		return $this->template;
	}

	public function action_pdfview($mode = null,$number = null,$o = 's')
	{
		if (is_null($mode) || is_null($number))
		{
			Session::set('SES_T_ERROR_MSG','PDF出力情報が指定されていません。');
			Response::redirect($this->eRedirect);
		}

		$aE = null;

		switch ($mode)
		{
			case 'b':
				$result = Model_Payment::getPayDoc(array(array('ttID','=',$this->aTeacher['ttID']),array('eNO','=',$number),array('status','>',0),array('bNum','>=',0)));
				if (!count($result))
				{
					Session::set('SES_T_ERROR_MSG','発行回数上限に達しているため、請求書を発行できません。');
					Response::redirect($this->eRedirect);
				}
				$aE = $result->current();
				$sFileName = $aE['bNO'].'.pdf';
			break;
			case 'r':
				$result = Model_Payment::getPayDoc(array(array('ttID','=',$this->aTeacher['ttID']),array('eNO','=',$number),array('status','=',2),array('rNum','>=',0)));
				if (!count($result))
				{
					Session::set('SES_T_ERROR_MSG','発行回数上限に達しているため、領収書を発行できません。');
					Response::redirect($this->eRedirect);
				}
				$aE = $result->current();
				$sFileName = $aE['bNO'].'-R.pdf';
			break;
			case 'l':
				$result = Model_Payment::getPayDoc(array(array('ttID','=',$this->aTeacher['ttID']),array('eNO','=',$number),array('status','>',0),array('lNum','>=',0)));
				if (!count($result))
				{
					Session::set('SES_T_ERROR_MSG','発行回数上限に達しているため、ライセンス証明書（納品書）を発行できません。');
					Response::redirect($this->eRedirect);
				}
				$aE = $result->current();
				$sFileName = $aE['bNO'].'-L.pdf';
			break;
			case 'e':
			default:
				$sFileName = $number.'.pdf';
			break;
		}

		# ファイルパス生成
		$sFilePath = CL_FILEPATH.DS.$this->aTeacher['ttID'].DS.'payment_pdf'.DS.$sFileName;
		if (!file_exists($sFilePath))
		{
			Session::set('SES_T_ERROR_MSG','指定のPDFファイルが見つかりません。');
			Response::redirect($this->eRedirect);
		}

		$sOutput = ($o == 's')? 'inline':'attachment';
		header('Content-Type: application/pdf');
		header('Content-Disposition: '.$sOutput.'; filename="'.$sFileName.'"');

		readfile($sFilePath);
		exit();
	}

	public function action_pptest()
	{
		$ppp = new Clfunc_PayPalPayment();
		$ppp->setAmount(10800);

		try
		{
			# 認証チェック
			$res = $ppp->PaymentCheck($this->aTeacher);
		}
		catch (\Exception $e)
		{
			Session::set('SES_T_ERROR_MSG','PayPal Payment Faild. ['.$e->getCode().']'.$e->getMessage());
			Response::redirect('/t/payment');
		}
	}

	/**
	 * 見積もり番号生成
	 *
	 * @param unknown $pubdate
	 * @return string
	 */
	private function EstimateNumber($pubdate)
	{
		$iDate = Clfunc_Common::strtotime_ja($pubdate);

		$iYear = (date('n',$iDate) <= 3)? date('Y',$iDate) - 1:date('Y',$iDate);

		$result = Model_Payment::getPayDoc(array(array('year','=',$iYear)),null,array('no'=>'desc'));
		$iAC = 1;
		if (count($result))
		{
			$last = $result->current();
			$aNo = explode('-', $last['eNO']);
			$iAC = (int)$aNo[2] + 1;
		}

		$result = Model_Payment::getPayDoc(array(array('ttID','=',$this->aTeacher['ttID']),array('year','=',$iYear)),null,array('no'=>'desc'));
		$iTC = 1;
		if (count($result))
		{
			$last = $result->current();
			$aNo = explode('-', $last['eNO']);
			$iTC = (int)$aNo[3] + 1;
		}

		$sNumber = $iYear;
		$sNumber .= '-'.substr($this->aTeacher['ttID'],2);
		$sNumber .= '-'.sprintf('%06d',$iAC);
		$sNumber .= '-'.sprintf('%03d',$iTC);

		return $sNumber;
	}

}
