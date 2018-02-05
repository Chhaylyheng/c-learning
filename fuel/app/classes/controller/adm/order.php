<?php
class Controller_Adm_Order extends Controller_Adm_Base
{
	public function action_index()
	{
		# パンくずリスト生成
		$this->template->set_global('breadcrumbs',array(array('name'=>'銀行振込確認')));
		# ページタイトル生成
		$this->template->set_global('pagetitle','銀行振込確認');

		$aBill = null;
		$aAnd = array(
			array('eBilling','=',2),
			array('status','=',1),
		);
		$result = Model_Payment::getPayDoc($aAnd,null,array('bDate'=>'asc'));
		if (count($result))
		{
			$aBill = $result->as_array();
		}

		$this->template->content = View::forge('adm/order_index');
		$this->template->content->set('aBill',$aBill);
		$this->template->javascript = array('cl.adm.order.js');
		return $this->template;
	}

	public function action_paymentcheck()
	{
		if (!Input::post(null,false))
		{
			Session::set('SES_ADM_ERROR_MSG','正しく情報が指定されていません。');
			Response::redirect($this->eRedirect);
		}
		$aInput = Input::post();

		$aB = $aInput['chkB'];
		if (!count($aB))
		{
			Session::set('SES_ADM_ERROR_MSG','注文が選択されていません。');
			Response::redirect('/adm/order');
		}
		$sIN = '("'.implode('","', $aB).'")';

		$result = Model_Payment::getPayDoc(array(array('bNO','IN',DB::expr($sIN))));
		if (!count($result))
		{
			Session::set('SES_ADM_ERROR_MSG','正しい注文が選択されていません。');
			Response::redirect('/adm/order');
		}
		$aBill = $result->as_array();

		try
		{
			$result = Model_Payment::setPaymentBank($aInput['paydate'],$aBill);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_ADM_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		foreach ($aBill as $aB)
		{
			$aP = unserialize($aB['purchase']);
			$result = Model_Payment::getPlan(array(array('ptID','=',$aP['pt']),array('ptSelect','=',1)));
			if (!count($result))
			{
				Session::set('SES_ADM_ERROR_MSG','対象のプランが見つかりませんでした。 - '.serialize($aB));
				Response::redirect('/adm/order');
			}
			$aPlan = $result->current();

			if (isset($aP['coNO']))
			{
				$result = Model_Contract::getContract(array(array('ttID','=',$aB['ttID']),array('coNO','=',$aP['coNO'])));
				if (!count($result))
				{
					Session::set('SES_ADM_ERROR_MSG','契約情報が確認できませんでした。 - '.serialize($aB));
					Response::redirect('/adm/order');
				}
				$aCon = $result->current();
			}
			else
			{
				$result = Model_Contract::getContract(array(array('ttID','=',$aB['ttID'])),null,array('coNO'=>'desc'));
				if (!count($result))
				{
					Session::set('SES_ADM_ERROR_MSG','契約情報が確認できませんでした。 - '.serialize($aB));
					Response::redirect('/adm/order');
				}
				$aTemp = $result->as_array();
				$aCon = $aTemp[0];
			}

			switch ($aP['product'])
			{
				case 'contract':
					$sMailOpt =
					(($aCon['coNO'] == 2)? '新規契約':'継続契約')."\n".
					'契約プラン：'.$aPlan['ptName']."\n".
					'契約期間：'.$aP['range'].'ヶ月（'.date('Y年n月j日',strtotime($aCon['coStartDate'])).'～'.date('Y年n月j日',strtotime($aCon['coTermDate'])).'）'."\n".
					'講義数：'.$aP['class'].'講義';
				break;
				case 'change':
					$iRange = \Clfunc_Common::contractMonths($aCon['coTermDate'], date('Y-m-d', strtotime($aB['bDate'])));
					$sMailOpt =
					'プラン変更契約'."\n".
					'契約プラン：'.$aPlan['ptName']."\n".
					'契約期間：'.$iRange.'ヶ月（'.date('Y年n月j日', strtotime($aB['bDate'])).'～'.date('Y年n月j日',strtotime($aCon['coTermDate'])).'）'."\n".
					'講義数：'.$aCon['coClassNum'].'講義';
				break;
				case 'add':
					$iRange = \Clfunc_Common::contractMonths($aCon['coTermDate'], date('Y-m-d', strtotime($aB['bDate'])));
					$iClass = (int)$aP['class'];

					$sMailOpt =
					'講義数の追加契約'."\n".
					'契約プラン：'.$aPlan['ptName']."\n".
					'契約期間：'.$iRange.'ヶ月（'.date('Y年n月j日').'～'.date('Y年n月j日',strtotime($aCon['coTermDate'])).'）'."\n".
					'追加講義数：'.$iClass.'講義（合計 '.$aCon['coClassNum'].'講義）';
				break;
			}

			$aB['bPayDate'] = $aInput['paydate'];
			# 購入完了メール
			$email = \Email::forge();
			$email->from(CL_MAIL_FROM, CL_MAIL_SENDER);
			$email->to($aB['ttMail']);
			$email->subject('[CL]購入完了のお知らせ（銀行振込）');
			$body = View::forge('email/t_purchase_bank2', array('aE'=>$aB, 'sMailOpt'=>$sMailOpt), false);
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
		}


		Session::set('SES_ADM_NOTICE_MSG','対象の注文を入金済みにしました。');
		Response::redirect('/adm/order');
	}


	public function action_paymentremove($sBN = null)
	{
		if (is_null($sBN))
		{
			Session::set('SES_ADM_ERROR_MSG','正しく情報が指定されていません。');
			Response::redirect($this->eRedirect);
		}
		$result = Model_Payment::getPayDoc(array(array('bNO','=',$sBN)));
		if (!count($result))
		{
			Session::set('SES_ADM_ERROR_MSG','指定された注文情報が見つかりません。');
			Response::redirect('/adm/order');
		}
		$aBill = $result->current();

		try
		{
			$result = Model_Payment::deletePayDoc($aBill['eNO']);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_ADM_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		Session::set('SES_ADM_NOTICE_MSG','対象の注文を削除しました。');
		Response::redirect('/adm/order');
	}

	public function action_pdfview($sTtID = null, $mode = null,$number = null,$o = 's')
	{
		if (is_null($sTtID) || is_null($mode) || is_null($number))
		{
			Session::set('SES_T_ERROR_MSG','PDF出力情報が指定されていません。');
			Response::redirect($this->eRedirect);
		}

		$aE = null;
		$result = Model_Payment::getPayDoc(array(array('ttID','=',$sTtID),array('eNO','=',$number)));
		if (!count($result))
		{
			Session::set('SES_T_ERROR_MSG','支払い情報が見つかりません');
			Response::redirect($this->eRedirect);
		}
		$aE = $result->current();

		switch ($mode)
		{
			case 'b':
				$sFileName = $aE['bNO'].'.pdf';
			break;
			case 'r':
				$sFileName = $aE['bNO'].'-R.pdf';
			break;
			case 'l':
				$sFileName = $aE['bNO'].'-L.pdf';
			break;
			case 'e':
			default:
				$sFileName = $number.'.pdf';
			break;
		}

		# ファイルパス生成
		$sFilePath = CL_FILEPATH.DS.$sTtID.DS.'payment_pdf'.DS.$sFileName;
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



}