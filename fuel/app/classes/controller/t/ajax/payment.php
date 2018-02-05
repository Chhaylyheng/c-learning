<?php
class Controller_T_Ajax_Payment extends Controller_T_Ajax
{
	public function post_CouponCheck()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>'情報が正しく送信されていません。');
		$par = Input::post();
		if ($par)
		{
			if ($par['pt'] != 3)
			{
				$res = array('err'=>-2,'res'=>'','msg'=>'プラン情報が確認できませんでした。');
				$this->response($res);
				return;
			}
			$result = Model_Payment::getPlan(array(array('ptID','=',$par['pt']),array('ptSelect','=',1)));
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>'プラン情報が確認できませんでした。');
				$this->response($res);
				return;
			}
			$aPlan = $result->current();

			$result = Model_Payment::getCoupon(array(array('cpCode','=',$par['code']),array('cpTermDate','>',date('Y-m-d'))));
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>'入力されたクーポンコードは無効です。');
				$this->response($res);
				return;
			}
			$aCoupon = $result->current();

			if (($aCoupon['cpPaymentType'] & (int)$par['bi']) && $aCoupon['cpRange'] <= $par['rg'])
			{
				$iDiscount = $aCoupon['cpDiscount'];
			}
			else
			{
				$iDiscount = 0;
			}

			$aType = \Clfunc_Flag::getPaymentFlag();
			$sText = '※このクーポンコードは、';
			$sText .= ($aCoupon['cpPaymentType'] & \Clfunc_Flag::P_TYPE_PAYPAL)? '「'.$aType[\Clfunc_Flag::P_TYPE_PAYPAL].'」':'';
			$sText .= ($aCoupon['cpPaymentType'] & \Clfunc_Flag::P_TYPE_CARD)? '「'.$aType[\Clfunc_Flag::P_TYPE_CARD].'」':'';
			$sText .= ($aCoupon['cpPaymentType'] & \Clfunc_Flag::P_TYPE_BANK)? '「'.$aType[\Clfunc_Flag::P_TYPE_BANK].'」':'';
			if ($aCoupon['cpRange'] > 1)
			{
				$sText .= 'の支払い方法で、'.$aCoupon['cpRange'].'ヶ月以上の契約時に適用されます。';
			}
			else
			{
				$sText .= 'の支払い方法の場合に適用されます。';
			}

			$res = array('err'=>0,'res'=>array('discount'=>$iDiscount,'text'=>$sText));
		}
		$this->response($res);
		return;
	}

	public function post_ContractMath()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>'情報が正しく指定されていません。');

		$par = Input::post();
		if ($par)
		{
			$result = Model_Payment::getPlan(array(array('ptID','=',$par['pt']),array('ptSelect','=',1)));
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>'プラン情報が確認できませんでした。');
				$this->response($res);
				return;
			}
			$aPlan = $result->current();

			$iClassPrice = $aPlan['ptPriceCL'] * ((100 - (int)$par['cc'])/100);
			$iStuPrice = $aPlan['ptPriceStu'] * ((100 - (int)$par['cc'])/100);
			$iPrice = ($aPlan['ptPriceID'] * (int)$par['rg']) + ($iClassPrice * (int)$par['cn'] * (int)$par['rg']) + ($iStuPrice * (int)$par['sn'] * (int)$par['rg']);

			$sCalc = '（'.number_format($aPlan['ptPriceID']).'円 × '.(int)$par['rg'].'ヶ月）';
			if ((int)$par['cn'])
			{
				$sCalc .= '＋ （'.number_format($iClassPrice).'円 × '.(int)$par['cn'].'講義 × '.(int)$par['rg'].'ヶ月）';
			}
			if ((int)$par['sn'])
			{
				$sCalc .= '＋ （'.number_format($iStuPrice).'円 × '.(int)$par['sn'].'[+'.number_format((int)$par['sn']*300).'名分] × '.(int)$par['rg'].'ヶ月）';
			}

			$result = Model_Contract::getContract(array(array('ttID','=',$this->aTeacher['ttID'])),null,array('coNO'=>'desc'));
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>'現在の契約内容が確認できませんでした。');
				$this->response($res);
				return;
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
			$sEnd = \Clfunc_Common::contractEnd($sStart,$par['rg']);
			if ($this->aTeacher['ptID'] == 1)
			{
				$sStart = date('Y-m-d');
			}

			$res = array('err'=>0,'res'=>array('ClassPrice'=>$iClassPrice,'StuPrice'=>$iStuPrice, 'Price'=>$iPrice, 'Calc'=>$sCalc, 'start'=>date('Y年n月j日',strtotime($sStart)), 'term'=>date('Y年n月j日',strtotime($sEnd))));
		}
		$this->response($res);
		return;
	}

	public function post_ChangeMath()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>'情報が正しく指定されていません。');

		$par = Input::post();
		if ($par)
		{
			$result = Model_Payment::getPlan(array(array('ptID','=',$this->aTeacher['ptID']),array('ptSelect','=',1)));
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>'現在のプラン情報が確認できませんでした。');
				$this->response($res);
				return;
			}
			$aActPlan = $result->current();

			$result = Model_Payment::getPlan(array(array('ptID','=',$par['pt']),array('ptSelect','=',1)));
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>'対象のプラン情報が確認できませんでした。');
				$this->response($res);
				return;
			}
			$aCngPlan = $result->current();

			$iIDP = ($aCngPlan['ptPriceID'] - $aActPlan['ptPriceID']);
			$iCLP = ($aCngPlan['ptPriceCL'] * ((100 - (int)$par['cc'])/100));
			$iCLPd = ($iCLP - $aActPlan['ptPriceCL']);

			$iSTP = ($aCngPlan['ptPriceStu'] * ((100 - (int)$par['cc'])/100));
			$iSTPd = ($iSTP - $aActPlan['ptPriceStu']);

			$iRange = \Clfunc_Common::contractMonths($this->aTeacher['coTermDate']);

			$iActClass = $this->aTeacher['coClassNum'];
			$iCngClass = (int)$par['cn'];

			$iActStu = ($this->aTeacher['coStuNum'] / 300) - 1;
			$iCngStu = (int)$par['sn'];

			$iPrice = ($iIDP * $iRange) +
			 ($iCLPd * $iRange * $iActClass) + ($iCLP * $iRange * ($iCngClass - $iActClass)) +
			 ($iSTPd * $iRange * $iActStu) + ($iSTP * $iRange * ($iCngStu - $iActStu));
			$iPrice = ($iPrice < 0)? 0:$iPrice;

			$aCalc = null;
			if ($iIDP > 0)
			{
				$aCalc[] = '（'.number_format($iIDP).'円 × '.$iRange.'ヶ月）';
			}
			if ($iActClass > 0 && ($iCngClass - $iActClass) >= 0)
			{
				$aCalc[] = '（'.number_format($iCLPd).'円 × '.$iActClass.'講義 × '.$iRange.'ヶ月）';
			}
			if (($iCngClass - $iActClass) > 0)
			{
				$aCalc[] = '（'.number_format($iCLP).'円 × '.($iCngClass - $iActClass).'講義 × '.$iRange.'ヶ月）';
			}
			if ($iActStu > 0 && ($iCngStu - $iActStu) >= 0)
			{
				$aCalc[] = '（'.number_format($iSTPd).'円 × '.$iActStu.'（'.($iActStu * 300).'名分） × '.$iRange.'ヶ月）';
			}
			if (($iCngStu - $iActStu) > 0)
			{
				$aCalc[] = '（'.number_format($iSTP).'円 × '.($iCngStu - $iActStu).'[+'.number_format(($iCngStu - $iActStu) * 300).'名分] × '.$iRange.'ヶ月）';
			}
			$sCalc = implode('＋', $aCalc);

			$res = array('err'=>0,'res'=>array('ClassPriceDifferent'=>$iCLPd, 'ClassPrice'=>$iCLP, 'StuPriceDifferent'=>$iSTPd, 'StuPrice'=>$iSTP, 'Price'=>$iPrice, 'Calc'=>$sCalc));
		}
		$this->response($res);
		return;
	}

	public function post_AddMath()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>'情報が正しく指定されていません。');

		$par = Input::post();
		if ($par)
		{
			$result = Model_Payment::getPlan(array(array('ptID','=',$this->aTeacher['ptID']),array('ptSelect','=',1)));
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>'現在のプラン情報が確認できませんでした。');
				$this->response($res);
				return;
			}
			$aPlan = $result->current();

			$iCLP = ($aPlan['ptPriceCL'] * ((100 - (int)$par['cc'])/100));

			$iRange = \Clfunc_Common::contractMonths($this->aTeacher['coTermDate']);
			$iClass = (int)$par['cn'];

			$iPrice = $iCLP * $iRange * $iClass;

			$sCalc = '（'.number_format($iCLP).'円 × '.$iClass.'講義 × '.$iRange.'ヶ月）';

			$res = array('err'=>0,'res'=>array('ClassPrice'=>$iCLP, 'Price'=>$iPrice, 'Calc'=>$sCalc));
		}
		$this->response($res);
		return;
	}

	public function post_PointMath()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>'購入情報が正しく指定されていません。');
		$par = Input::post();
		if ($par)
		{
			if (!isset($par['pt']) && !isset($par['pr']))
			{
				$this->response($res);
				return;
			}

			if (isset($par['pt']))
			{
				$aPPSet = ClFunc_Common::mathPoint((int)$par['pt']);
			}
			else
			{
				$aPPSet = ClFunc_Common::mathPoint(null,(int)$par['pr']);
			}
			$res = array('err'=>0,'res'=>$aPPSet,'msg'=>'');
			$this->response($res);
			return;
		}
	}

	public function post_CardRegist()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>'カード情報が正しく指定されていません。');
		$par = Input::post();
		if ($par)
		{
			$result = Model_Teacher::getTeacher(array(array('ttID','=',$par['tt']),array('ttStatus','=',1)));
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>'カード登録対象の先生情報が見つかりません。');
				$this->response($res);
				return;
			}
			$aTeacher = $result->current();
			$sCardNo = $par['cN'];
			if (!is_numeric($sCardNo) || strlen($sCardNo) != 16)
			{
				$res = array('err'=>-2,'res'=>'','msg'=>'カード番号が数値16桁でないため登録できません。');
				$this->response($res);
				return;
			}
			$sCardExpire = $par['cY'].$par['cM'];
			if (!is_numeric($sCardExpire) || strlen($sCardExpire) != 4)
			{
				$res = array('err'=>-2,'res'=>'','msg'=>'カード有効期限が有効でないため登録できません。');
				$this->response($res);
				return;
			}
			$sSeqCode = $par['cC'];
			if (!is_numeric($sSeqCode) || (strlen($sSeqCode) != 3 && strlen($sSeqCode) != 4))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>'セキュリティコードが有効でないため登録できません。');
				$this->response($res);
				return;
			}

			$oPG = new Clfunc_GmoPGPayment();
			$result = $oPG->readMember($aTeacher['ttID']);
			if ($result != 0)
			{
				if ($result == CL_PG_ERR_NOMEMBER)
				{
					$result = $oPG->addMember($aTeacher['ttID'],$aTeacher['ttName']);
					if ($result != 0)
					{
						$res = array('err'=>-2,'res'=>'','msg'=>'カード情報の登録に失敗しました。再度実行してください。(AM-'.$result.')');
						$this->response($res);
						return;
					}
				}
				else
				{
					$res = array('err'=>-2,'res'=>'','msg'=>'カード情報の登録に失敗しました。再度実行してください。(RM-'.$result.')');
					$this->response($res);
					return;
				}
			}

			$sOrderId = $aTeacher['ttID'].'-'.str_replace('.','-',microtime(true));

			$result = $oPG->checktran($sOrderId,$sCardNo,$sCardExpire,$sSeqCode);
			if ($result != 0)
			{
				switch ($result)
				{
					case CL_PG_ERR_C_LACK:
						$sMsg = 'カード残高が不足しています。別のカードをご利用ください。';
						break;
					case CL_PG_ERR_C_LIMIT:
						$sMsg = 'カードの限度額を超えています。別のカードをご利用ください。';
						break;
					case CL_PG_ERR_C_NUMBER:
						$sMsg = 'ご入力のカード番号の有効性が確認できませんでした。カード番号の入力内容に誤りが無いかご確認ください。';
						break;
					case CL_PG_ERR_C_TIME:
						$sMsg = 'カードの有効期限が誤っています。有効期限の入力内容に誤りが無いかご確認ください。';
						break;
					case CL_PG_ERR_C_SEQCODE:
						$sMsg = 'ご入力のセキュリティコードに誤りがあります。セキュリティコードの入力内容に誤りが無いかご確認ください。';
						break;
					default:
						$sMsg = 'カード情報が有効ではありません。入力内容をご確認の上再度実行いただくか、別のカードをご利用ください。';
						break;
				}
				$sE = $sMsg.'(CT-'.$result.'/'.$oPG->sErrInfo_.')';
				$res = array('err'=>-2,'res'=>'','msg'=>$sE);
				Log::warning('CardRegist - ['.$aTeacher['ttID'].'/'.$aTeacher['ttName'].'] - '.$sE);
				$this->response($res);
				return;
			}

			$result = $oPG->readCard($aTeacher['ttID']);
			if ($result != 0)
			{
				if ($result == CL_PG_ERR_NOCARD)
				{
					$result = $oPG->addCard($aTeacher['ttID'],$sCardNo,$sCardExpire);
					if ($result != 0)
					{
						$res = array('err'=>-2,'res'=>'','msg'=>'カード情報の登録に失敗しました。再度実行してください。(AC-'.$result.')');
						$this->response($res);
						return;
					}
				}
				else
				{
					$res = array('err'=>-2,'res'=>'','msg'=>'カード情報の登録に失敗しました。再度実行してください。(RC-'.$result.')');
					$this->response($res);
					return;
				}
			}
			else
			{
				$result = $oPG->upCard($aTeacher['ttID'],$sCardNo,$sCardExpire);
				if ($result != 0)
				{
					$res = array('err'=>-2,'res'=>'','msg'=>'カード情報の登録に失敗しました。再度実行してください。(UC-'.$result.')');
					$this->response($res);
					return;
				}
			}
			$result = $oPG->readCard($aTeacher['ttID']);
			if ($result != 0)
			{
				$res = array('err'=>-2,'res'=>'','msg'=>'カード情報の登録に失敗しました。再度実行してください。(RC2-'.$result.')');
				$this->response($res);
				return;
			}
			$aCard = $oPG->getResult();

			try
			{
				$result = Model_Teacher::updateTeacher($aTeacher['ttID'],array('ccSeqCode'=>$sSeqCode));
			}
			catch (Exception $e)
			{
				\Clfunc_Common::LogOut($e,__CLASS__);
				$res = array('err'=>-2,'res'=>'','msg'=>'カード情報の登録に失敗しました。再度実行してください。('.$e->getMessage().')');
				$this->response($res);
				return;
			}

			$aRes = array(
					'CardNo'   => wordwrap($aCard['CardNo'],4,'-',true),
					'Expire'   => substr($aCard['Expire'],2).'/\''.substr($aCard['Expire'],0,2),
			);
			$res = array('err'=>0,'res'=>$aRes,'msg'=>'');
		}
		$this->response($res);
		return;
	}


	public function post_CardPurchase()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>'購入情報が正しく指定されていません。');
		$par = Input::post();
		if ($par)
		{
			$result = Model_Teacher::getTeacher(array(array('ttID','=',$par['tt']),array('ttStatus','=',1)));
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>'購入対象の先生情報が見つかりません。');
				$this->response($res);
				return;
			}
			$aTeacher = $result->current();
			if ($aTeacher['ttPass'] != sha1($par['pw']))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>'ログインパスワードが異なるため、購入手続きを中止しました。');
				$this->response($res);
				return;
			}
			$result = Model_Payment::getPayDoc(array(array('ttID','=',$aTeacher['ttID']),array('eNO','=',$par['eN']),array('status','=',0)));
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>'購入情報が見つかりません。');
				$this->response($res);
				return;
			}
			$aE = $result->current();

			$oPG = new Clfunc_GmoPGPayment();
			# カード情報取得
			$result = $oPG->readCard($aTeacher['ttID']);
			if ($result != 0)
			{
				$res = array('err'=>-2,'res'=>'','msg'=>'カードが登録されていません。カード登録を先に行ってください。(RC-'.$result.')');
				$this->response($res);
				return;
			}

			$sOrderId = $aE['eNO'].'-'.sprintf('%02d',$aE['cNum']);
			try
			{
				$result = Model_Payment::updatePayDoc(array('cNum'=>(int)$aE['cNum'] + 1),$aE);
			}
			catch (Exception $e)
			{
				\Clfunc_Common::LogOut($e,__CLASS__);
				$res = array('err'=>-2,'res'=>'','msg'=>'購入処理に失敗しました。時間をおいてから、再度実行してください。('.$e->getMessage().')');
				$this->response($res);
				return;
			}

			$iAmount = $aE['ePrice'];
			$iTax = floor($aE['ePrice'] * $aE['eTax']);
			$result = $oPG->tran($sOrderId,$aTeacher['ttID'],$aTeacher['ccSeqCode'],$iAmount,$iTax);
			if ($result != 0)
			{
				switch ($result)
				{
					case CL_PG_ERR_NOMEMBER:
					case CL_PG_ERR_NOCARD:
						$sMsg = 'カードが登録されていません。カード登録を先に行ってください。';
						break;
					case CL_PG_ERR_C_LACK:
						$sMsg = 'カード残高が不足しています。別のカードを登録して購入処理を行ってください。';
						break;
					case CL_PG_ERR_C_LIMIT:
						$sMsg = 'カードの限度額を超えています。別のカードを登録して購入処理を行ってください。';
						break;
					case CL_PG_ERR_C_NUMBER:
						$sMsg = 'カードの番号が無効です。別のカードを登録して購入処理を行ってください。';
						break;
					case CL_PG_ERR_C_TIME:
						$sMsg = 'カードの有効期限が誤っています。別のカードを登録して購入処理を行ってください。';
						break;
					case CL_PG_ERR_C_SEQCODE:
						$sMsg = 'セキュリティコードに誤りがあります。別のカードを登録して購入処理を行ってください。';
						break;
					default:
						$sMsg = '購入処理に失敗しました。時間をおいてから、再度実行してください。';
						break;
				}
				$sE = $sMsg.'(PT-'.$result.'/'.$oPG->sErrInfo_.')';
				$res = array('err'=>-2,'res'=>'','msg'=>$sE);
				Log::warning('CardRegist - ['.$aTeacher['ttID'].'/'.$aTeacher['ttName'].'] - '.$sE);
				$this->response($res);
				return;
			}

			try
			{
				$aP = unserialize($aE['purchase']);
				switch ($aP['product'])
				{
					case 'contract':
						$result = Model_Payment::getPlan(array(array('ptID','=',$aP['pt']),array('ptSelect','=',1)));
						if (!count($result))
						{
							Log::warning('Not Found Plan - ['.$aTeacher['ttID'].'/'.$aTeacher['ttName'].'] - '.serialize($aP));
							throw new Exception('購入対象のプランが見つかりませんでした。');
						}
						$aPlan = $result->current();
						$result = Model_Contract::getContract(array(array('ttID','=',$aTeacher['ttID'])),null,array('coNO'=>'desc'));
						if (!count($result))
						{
							Log::warning('Not Found Contract - ['.$aTeacher['ttID'].'/'.$aTeacher['ttName'].'] - '.serialize($aP));
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
							'ttID' => $aTeacher['ttID'],
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

						if ($aTeacher['ptID'] == 1)
						{
							$aIns['coStartDate'] = date('Y-m-d');

							$aUp = array(
								'coTermDate' => date('Y-m-d', strtotime('-1 day')),
							);
							$result = Model_Contract::updateContract($aUp,array(array('ttID','=',$aTeacher['ttID']),array('ptID','=',1)));
						}
						$result = Model_Contract::insertContract($aIns);

						$sMailOpt =
						(($coNO == 2)? '新規契約':'継続契約')."\n".
						'契約プラン：'.$aPlan['ptName']."\n".
						'契約期間：'.$aP['range'].'ヶ月（'.date('Y年n月j日',strtotime($sStart)).'～'.date('Y年n月j日',strtotime($sEnd)).'）'."\n".
						'講義数：'.$aP['class'].'講義';
					break;
					case 'change':
						$result = Model_Payment::getPlan(array(array('ptID','=',$aTeacher['ptID']),array('ptSelect','=',1)));
						if (!count($result))
						{
							Log::warning('Not Found Plan - ['.$aTeacher['ttID'].'/'.$aTeacher['ttName'].'] - '.serialize($aP));
							throw new Exception('現在の契約中プランの内容が確認できませんでした。');
						}
						$aActPlan = $result->current();
						if ($aActPlan['ptID'] != 2)
						{
							Log::warning('Plan Changed - ['.$aTeacher['ttID'].'/'.$aTeacher['ttName'].'] - '.serialize($aP));
							throw new Exception('見積もり作成時と現在の契約が一致しません。再度、見積もりを作成してください。');
						}

						$result = Model_Payment::getPlan(array(array('ptID','=',$aP['pt']),array('ptSelect','=',1)));
						if (!count($result))
						{
							Log::warning('Not Found New Plan - ['.$aTeacher['ttID'].'/'.$aTeacher['ttName'].'] - '.serialize($aP));
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
						$iRange = \Clfunc_Common::contractMonths($aTeacher['coTermDate']);

						$iCLP = ($aCngPlan['ptPriceCL'] * ((100 - $iDiscount)/100));
						$iCLPd = ($iCLP - $aActPlan['ptPriceCL']);
						$iActClass = $aTeacher['coClassNum'];
						$iCngClass = (int)$aP['class'];

						$iSTP = ($aCngPlan['ptPriceStu'] * ((100 - $iDiscount)/100));
						$iSTPd = ($iSTP - $aActPlan['ptPriceStu']);
						$iActStu = $aTeacher['coStuNum'];
						$iCngStu = (int)$aP['stu'];

						$iPrice = ($iIDP * $iRange) +
						 ($iCLPd * $iRange * $iActClass) + ($iCLP * $iRange * ($iCngClass - $iActClass)) +
						 ($iSTPd * $iRange * $iActStu) + ($iSTP * $iRange * ($iCngStu - $iActStu));
						$iTax = floor($iPrice * $aE['eTax']);

						$coNO = $aTeacher['coNO'];

						if ($aP['price'] != $iPrice)
						{
							Log::warning('Price Changed - ['.$aTeacher['ttID'].'/'.$aTeacher['ttName'].'] - '.serialize($aP));
							throw new Exception('見積もり時と現在で金額に変更があります。再度、現在の状態で見積もりを作成してください。');
						}

						$aUp = array(
							'ptID'       => $aCngPlan['ptID'],
							'coCapacity' => $aCngPlan['ptCapacity'],
							'coClassNum' => $iCngClass,
							'coStuNum'   => $aCngPlan['ptStuNum'] + ($iCngStu * 300),
							'coPayment'  => ($aTeacher['coPayment'] + $iPrice + $iTax),
						);
						$result = Model_Contract::updateContract($aUp,array(array('ttID','=',$aTeacher['ttID']),array('coNO','=',$aTeacher['coNO'])));

						$sMailOpt =
						'プラン変更契約'."\n".
						'契約プラン：'.$aActPlan['ptName'].' → '.$aCngPlan['ptName']."\n".
						'契約期間：'.$iRange.'ヶ月（'.date('Y年n月j日').'～'.date('Y年n月j日',strtotime($this->aTeacher['coTermDate'])).'）'."\n".
						'講義数：'.$iActClass.' → '.$iCngClass.'講義';
					break;
					case 'add':
						$result = Model_Payment::getPlan(array(array('ptID','=',$aTeacher['ptID']),array('ptSelect','=',1)));
						if (!count($result))
						{
							Log::warning('Not Found Plan - ['.$aTeacher['ttID'].'/'.$aTeacher['ttName'].'] - '.serialize($aP));
							throw new Exception('現在の契約中プランの内容が確認できませんでした。');
						}
						$aPlan = $result->current();
						if ($aPlan['ptID'] != 3)
						{
							Log::warning('Plan Changed - ['.$aTeacher['ttID'].'/'.$aTeacher['ttName'].'] - '.serialize($aP));
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

						$iRange = \Clfunc_Common::contractMonths($aTeacher['coTermDate']);
						$iClass = (int)$aP['class'];

						if (($aTeacher['coClassNum'] + $iClass) > 20)
						{
							Log::warning('Class Over - ['.$aTeacher['ttID'].'/'.$aTeacher['ttName'].'] - '.serialize($aP));
							throw new Exception('この講義追加により契約講義数が20講義を越えてしまいます。再度、見積もりを作成してください。');
						}

						$iPrice = $iCLP * $iRange * $iClass;
						$iTax = floor($iPrice * $aE['eTax']);

						$coNO = $aTeacher['coNO'];

						if ($aP['price'] != $iPrice)
						{
							Log::warning('Price Changed - ['.$aTeacher['ttID'].'/'.$aTeacher['ttName'].'] - '.serialize($aP));
							throw new Exception('見積もり時と現在で金額に変更があります。再度、現在の状態で見積もりを作成してください。');
						}

						$aUp = array(
							'coClassNum' => ($aTeacher['coClassNum'] + $iClass),
							'coPayment' => ($aTeacher['coPayment'] + $iPrice + $iTax),
						);
						$result = Model_Contract::updateContract($aUp,array(array('ttID','=',$aTeacher['ttID']),array('coNO','=',$aTeacher['coNO'])));

						$sMailOpt =
						'講義数の追加契約'."\n".
						'契約プラン：'.$aPlan['ptName']."\n".
						'契約期間：'.$iRange.'ヶ月（'.date('Y年n月j日').'～'.date('Y年n月j日',strtotime($this->aTeacher['coTermDate'])).'）'."\n".
						'追加講義数：'.$iClass.'講義（合計 '.$aUp['coClassNum'].'講義）';
					break;
				}
				$aP['coNO'] = $coNO;
				$aE['purchase'] = serialize($aP);
				$result = Model_Payment::setPaymentCard($aE);
			}
			catch (Exception $e)
			{
				\Clfunc_Common::LogOut($e,__CLASS__);
				$res = array('err'=>-2,'res'=>'','msg'=>'カード決済は完了しましたが、'.CL_SITENAME.'上でエラーが発生しました。申し訳ございませんが'.CL_SITENAME.'の利用を中止してサポートセンターまでご連絡ください。('.$e->getMessage().')');
				$this->response($res);
				return;
			}
			$result = Model_Payment::getPayDoc(array(array('ttID','=',$aTeacher['ttID']),array('eNO','=',$aE['eNO'])));
			if (count($result))
			{
				$aE = $result->current();
			}

			# 購入完了メール
			$email = \Email::forge();
			$email->from(CL_MAIL_FROM, CL_MAIL_SENDER);
			$email->to($aTeacher['ttMail']);
			$email->bcc(array(CL_KEIYAKUMAIL,CL_MIYATAMAIL));
			$email->subject('[CL]購入完了のお知らせ（カード決済）');
			$body = View::forge('email/t_purchase_card', array('aE'=>$aE, 'aT'=>$aTeacher, 'sMailOpt'=>$sMailOpt), false);
			$email->body($body);

			try
			{
				$email->send();
			}
			catch (\EmailValidationFailedException $e)
			{
				Log::warning('TeacherCardPurchaseStartMail - ' . $e->getMessage());
			}
			catch (\EmailSendingFailedException $e)
			{
				Log::warning('TeacherCardPurchaseStartMail - ' . $e->getMessage());
			}

			try
			{
				$aUpdate = array(
					'ttProgress' => 8,
				);
				$result = Model_Teacher::updateTeacher($this->aTeacher['ttID'], $aUpdate);
			}
			catch (Exception $e)
			{
				\Clfunc_Common::LogOut($e,__CLASS__);
			}

			$res = array('err'=>0,'res'=>array('fin'=>'ご購入手続きが完了しました。'),'msg'=>'');
		}
		$this->response($res);
		return;
	}

}
