<?php
class Controller_T_Kreport extends Controller_T_Base
{
	public function before()
	{
		parent::before();
		$this->template->set_global('aClass',null);
	}

	public function action_index()
	{
		Response::redirect('index/404','location',404);
	}

	public function action_put($iYear = null,$iPeriod = null)
	{
		$aReport = null;
		$aTeachers = null;
		$aAlready = null;

		$aChk = self::ReportChecker($iYear,$iPeriod,$aReport);
		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}

		$sRName = 'ケータイ研レポート（'.$aReport['krYear'].'年度 '.(($aReport['krPeriod'] == 1)? '4～9月期':'10～3月期').'）回答一覧';
		# パンくずリスト生成
		$this->template->set_global('breadcrumbs',array(array('name'=>$sRName)));
		# ページタイトル生成
		$this->template->set_global('pagetitle',$sRName);

		$result = Model_KReport::getKReportTarget(array(array('krYear','=',$aReport['krYear']),array('krPeriod','=',$aReport['krPeriod'])),null,array('ttName'=>'asc'));
		if (count($result))
		{
			$aTs = $result->as_array('ttID');
		}

		$result = Model_KReport::getKReportPut(array(array('krYear','=',$aReport['krYear']),array('krPeriod','=',$aReport['krPeriod'])),null,array('krDate'=>'desc'));
		if (count($result))
		{
			foreach ($result->as_array() as $put)
			{
				if (isset($aTs[$put['ttID']]))
				{
					$aTeachers[$put['ttID']]['put'] = null;
					$aTeachers[$put['ttID']]['teach'] = $aTs[$put['ttID']];
					unset($aTs[$put['ttID']]);
					if ($put['krStatus'] == 1 || $put['ttID'] == $this->aTeacher['ttID'])
					{
						$aTeachers[$put['ttID']]['put'][] = $put;
					}
				}
				else if (isset($aTeachers[$put['ttID']]))
				{
					if ($put['krStatus'] == 1 || $put['ttID'] == $this->aTeacher['ttID'])
					{
						$aTeachers[$put['ttID']]['put'][] = $put;
					}
				}
			}
		}
		if (count($aTs))
		{
			foreach ($aTs as $sTtID => $aT)
			{
				$aTeachers[$sTtID]['put'] = null;
				$aTeachers[$sTtID]['teach'] = $aT;
			}
		}

		$aMine = $aTeachers[$this->aTeacher['ttID']];
		unset($aTeachers[$this->aTeacher['ttID']]);
		$aTeachers = array_merge(array($this->aTeacher['ttID']=>$aMine),$aTeachers);

		$result = Model_KReport::getKReportAlready(array(array('krYear','=',$aReport['krYear']),array('krPeriod','=',$aReport['krPeriod']),array('kaID','=',$this->aTeacher['ttID'])));
		if (count($result))
		{
			$aTemp = $result->as_array();
			foreach ($aTemp as $aT)
			{
				$aAlready[$aT['ttID']][$aT['krSub']] = $aT;
			}
		}

		$this->template->content = View::forge('t/kreport/put');
		$this->template->content->set('aReport',$aReport);
		$this->template->content->set('aTeachers',$aTeachers);
		$this->template->content->set('aAlready',$aAlready);
		$this->template->javascript = array('cl.t.kreport.js');
		return $this->template;
	}

	public function action_all($iYear = null,$iPeriod = null)
	{
		$aReport = null;
		$aQuery = null;
		$aTeachers = null;
		$aAns = null;

		$aChk = self::QueryChecker($iYear,$iPeriod,$aReport,$aQuery);
		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}

		$sRName = 'ケータイ研レポート（'.$aReport['krYear'].'年度 '.(($aReport['krPeriod'] == 1)? '4～9月期':'10～3月期').'）回答一覧';
		# パンくずリスト生成
		$this->template->set_global('breadcrumbs',array(array('name'=>$sRName,'link'=>'/kreport/put/'.$aReport['krYear'].'/'.$aReport['krPeriod']),array('name'=>'回答全表示')));
		# ページタイトル生成
		$this->template->set_global('pagetitle','回答全表示');

		$result = Model_KReport::getKReportTarget(array(array('krYear','=',$aReport['krYear']),array('krPeriod','=',$aReport['krPeriod'])),null,array('ttName'=>'asc'));
		if (count($result))
		{
			$aTs = $result->as_array('ttID');
		}

		$result = Model_KReport::getKReportAns(array(array('krYear','=',$aReport['krYear']),array('krPeriod','=',$aReport['krPeriod'])));
		if (count($result))
		{
			$aTemp = $result->as_array();
			foreach ($aTemp as $aA)
			{
				$aAnswer[$aA['ttID']][$aA['krSub']][$aA['krNO']] = $aA;
			}
		}
		$aAlready = null;
		if (!is_null($aTs))
		{
			$result = Model_KReport::getKReportPut(array(array('krYear','=',$aReport['krYear']),array('krPeriod','=',$aReport['krPeriod']),array('krStatus','=',1)),null,array('krDate'=>'desc'));
			if (count($result))
			{
				foreach ($result->as_array() as $put)
				{
					if (isset($aTs[$put['ttID']]))
					{
						$aTeachers[$put['ttID']]['put'] = null;
						$aTeachers[$put['ttID']]['teach'] = $aTs[$put['ttID']];
						unset($aTs[$put['ttID']]);
						if ($put['krStatus'] == 1 || $put['ttID'] == $this->aTeacher['ttID'])
						{
							$aTeachers[$put['ttID']]['put'][] = $put;
						}
					}
					else if (isset($aTeachers[$put['ttID']]))
					{
						if ($put['krStatus'] == 1 || $put['ttID'] == $this->aTeacher['ttID'])
						{
							$aTeachers[$put['ttID']]['put'][] = $put;
						}
					}
				}
			}
			if (count($aTs))
			{
				foreach ($aTs as $sTtID => $aT)
				{
					$aTeachers[$sTtID]['put'] = null;
					$aTeachers[$sTtID]['teach'] = $aT;
				}
			}
			$aMine = $aTeachers[$this->aTeacher['ttID']];
			unset($aTeachers[$this->aTeacher['ttID']]);
			$aTeachers = array_merge(array($this->aTeacher['ttID']=>$aMine),$aTeachers);
		}

		if (!is_null($aTeachers))
		{
			$result = Model_KReport::getKReportAlready(array(array('krYear','=',$aReport['krYear']),array('krPeriod','=',$aReport['krPeriod']),array('kaID','=',$this->aTeacher['ttID'])));
			if (count($result))
			{
				foreach ($result->as_array() as $aAl)
				{
					$aAlready[$aAl['ttID'].'-'.$aAl['krSub']] = $aAl['kaAlready'];
				}
			}
			foreach ($aTeachers as $sTtID => $aT)
			{
				if (!is_null($aT['put']))
				{
					foreach ($aT['put'] as $aP)
					{
						$bUpdate = false;
						if (isset($aAlready[$sTtID.'-'.$aP['krSub']]))
						{
							$bUpdate = true;
							if ($aAlready[$sTtID.'-'.$aP['krSub']])
							{
								continue;
							}
						}
						$result = Model_KReport::setKReportAlready('already',$this->aTeacher,$aP,$bUpdate);
					}
				}
			}
		}

		$this->template->content = View::forge('t/kreport/all');
		$this->template->content->set('aReport',$aReport);
		$this->template->content->set('aQuery',$aQuery);
		$this->template->content->set('aTeachers',$aTeachers);
		$this->template->content->set('aAnswer',$aAnswer);
		return $this->template;
	}

	public function action_ansdetail($iYear = null,$iPeriod = null, $sTtID = null, $iSub = null)
	{
		$back = '/t/kreport/put/'.$iYear.'/'.$iPeriod;

		$aReport = null;
		$aQuery = null;
		$aTeacher = null;
		$aAns = null;

		$aChk = self::QueryChecker($iYear,$iPeriod,$aReport,$aQuery);
		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}

		$result = Model_Teacher::getTeacherFromID($sTtID);
		if (!count($result))
		{
			Session::set('SES_T_ERROR_MSG','指定の先生が見つかりませんでした。');
			Response::redirect($back);
		}
		$aPTeacher = $result->current();
		$result = Model_KReport::getKReportAns(array(array('krYear','=',$aReport['krYear']),array('krPeriod','=',$aReport['krPeriod']),array('ttID','=',$sTtID),array('krSub','=',$iSub)));
		if (!count($result))
		{
			Session::set('SES_T_ERROR_MSG','指定の先生は未回答です。');
			Response::redirect($back);
		}
		$aTemp = $result->as_array();
		foreach ($aTemp as $aA)
		{
			$aAns[$aA['krNO']] = $aA;
		}

		$result = Model_KReport::getKReportPut(array(array('krYear','=',$aReport['krYear']),array('krPeriod','=',$aReport['krPeriod']),array('ttID','=',$sTtID),array('krSub','=',$iSub)));
		if (!count($result))
		{
			Session::set('SES_T_ERROR_MSG','指定の先生は未回答です。');
			Response::redirect($back);
		}
		$aPut = $result->current();

		$aAlready = null;
		$bAlready = false;
		$bUpdate = false;
		$result = Model_KReport::getKReportAlready(array(array('krYear','=',$aReport['krYear']),array('krPeriod','=',$aReport['krPeriod']),array('ttID','=',$sTtID),array('krSub','=',$iSub),array('kaID','=',$this->aTeacher['ttID'])));
		if (count($result))
		{
			$bUpdate = true;
			$aAlready = $result->current();
			if ($aAlready['kaAlready'] == 1)
			{
				$bAlready = true;
			}
		}
		if (!$bAlready)
		{
			$result = Model_KReport::setKReportAlready('already',$this->aTeacher,$aPut,$bUpdate);
		}

		$sTName = (($aPTeacher['ttName'])? $aPTeacher['ttName']:$aPTeacher['ttMail']).'さんのレポート'.$iSub;
		$sName = 'ケータイ研レポート（'.$aReport['krYear'].'年度 '.(($aReport['krPeriod'] == 1)? '4～9月期':'10～3月期').'）回答一覧';
		# パンくずリスト生成
		$this->template->set_global('breadcrumbs',array(array('name'=>$sName,'link'=>'/kreport/put/'.$aReport['krYear'].'/'.$aReport['krPeriod']),array('name'=>$sTName)));
		# ページタイトル生成
		$this->template->set_global('pagetitle',$sTName.'（'.ClFunc_Tz::tz('Y/m/d H:i',$this->tz,$aAns[1]['krDate']).'提出）');

		$aSes = Session::get(null,false);
		$this->template->content = View::forge('t/kreport/ansdetail');
		$this->template->content->set('aReport',$aReport);
		$this->template->content->set('aQuery',$aQuery);
		$this->template->content->set('aPTeacher',$aPTeacher);
		$this->template->content->set('aPut',$aPut);
		$this->template->content->set('aAns',$aAns);
		$this->template->content->set('aAlready',$aAlready);
		$this->template->javascript = array('cl.t.kreport.js');
		return $this->template;
	}

	public function action_ans($iYear,$iPeriod,$iSub = null)
	{
		$aReport = null;
		$aQuery = null;
		$aChk = self::KReportAnsChecker($iYear,$iPeriod,$this->aTeacher['ttID'],$aReport,$aQuery);

		if (is_null($iSub))
		{
			$result = Model_KReport::getKReportPut(array(array('krYear','=',$aReport['krYear']),array('krPeriod','=',$aReport['krPeriod']),array('ttID','=',$this->aTeacher['ttID'])),null,array('krSub'=>'desc'));
			if (count($result)) {
				$aPut = $result->current();
				$iSub = $aPut['krSub'] + 1;
			} else {
				$iSub = 1;
			}
			\Session::delete('SES_T_KREPORT_ANS_'.$iSub);
		}
		$sKrID = $iYear.'-'.$iPeriod.'-'.$iSub;

		$sRName = 'ケータイ研レポート提出（'.$iYear.'年度 '.(($iPeriod == 1)? '4～9月期':'10～3月期').'）';
		$sName = 'ケータイ研レポート（'.$aReport['krYear'].'年度 '.(($aReport['krPeriod'] == 1)? '4～9月期':'10～3月期').'）回答一覧';
		# パンくずリスト生成
		$this->template->set_global('breadcrumbs',array(array('name'=>$sName,'link'=>'/kreport/put/'.$aReport['krYear'].'/'.$aReport['krPeriod']),array('name'=>$sRName)));
		# ページタイトル生成
		$this->template->set_global('pagetitle',$sRName);

		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}

		if (Input::post(null,false))
		{
			$aPost = Input::post();
			$aMsg = null;
			$aUploads = null;
			if (isset($aPost['files']))
			{
				foreach ($aPost['files'] as $sFile)
				{
					if ($sFile) {
						$aUploads[] = unserialize($sFile);
					}
				}
			}
			$aInput = null;
			foreach ($aQuery as $aQ)
			{
				$iKrNO = $aQ['krNO'];
				$aInput[$iKrNO]['select'] = '';
				$aInput[$iKrNO]['text'] = '';
				switch($aQ['krStyle'])
				{
					case 0:
						if (!isset($aPost['radioSel_'.$iKrNO]))
						{
						}
						else
						{
							$aInput[$iKrNO]['select'] = $aPost['radioSel_'.$iKrNO];
						}
					break;
					case 1:
						if (!isset($aPost['checkSel_'.$iKrNO]))
						{
						}
						else
						{
							$sChecks = implode("|",$aPost['checkSel_'.$iKrNO]);
							$aInput[$iKrNO]['select'] = $sChecks;
						}
					break;
					case 2:
						if (isset($aPost['textAns_'.$iKrNO]))
						{
							$sTemp = preg_replace('/^[\s　]*(.*?)[\s　]*$/u', '$1', $aPost['textAns_'.$iKrNO]);
							$sTemp = mb_convert_kana($sTemp,"as",CL_ENC);
							$sTemp = str_replace(array("\r\n","\r"), "\n", $sTemp);
							$aInput[$iKrNO]['text'] = trim($sTemp);
						}
					break;
				}
			}
			Session::set('SES_T_KREPORT_ANS_'.$iSub,serialize(array($sKrID=>array('input'=>$aInput,'files'=>$aUploads))));
			if (!is_null($aMsg))
			{
				Session::set('SES_T_KREPORT_MSG',serialize($aMsg));
				Response::redirect('/t/kreport/ans/'.$iYear.DS.$iPeriod.DS.$iSub);
			}

			if ($aPost['state'] == 'check')
			{
				Response::redirect('/t/kreport/check/'.$iYear.DS.$iPeriod.DS.$iSub);
			}

			// 一時保存機能
			$bUpdate = false;
			$result = Model_KReport::getKReportPut(array(array('krYear','=',$iYear),array('krPeriod','=',$iPeriod),array('ttID','=',$this->aTeacher['ttID']),array('krSub','=',$iSub)));
			if (count($result))
			{
				$bUpdate = true;
			}
			try
			{
				Model_KReport::setKReportPut($aReport,$aQuery,$this->aTeacher,$iSub,$aInput,$aUploads,0,$bUpdate);
			}
			catch (Exception $e)
			{
				\Clfunc_Common::LogOut($e,__CLASS__);
				Session::set('SES_T_ERROR_MSG',$e->getMessage());
				Response::redirect($this->eRedirect);
			}

			Session::delete('SES_T_KREPORT_ANS_'.$iSub);

			Session::set('SES_T_NOTICE_MSG',$sRName.'の回答を一時保存しました。');
			Response::redirect('/t/kreport/put/'.$aReport['krYear'].'/'.$aReport['krPeriod']);
		}

		$aInput = null;
		$aUploads = null;
		$aTemp = Session::get('SES_T_KREPORT_ANS_'.$iSub,false);
		$aTemp = ($aTemp)? unserialize($aTemp):null;
		if (isset($aTemp[$sKrID]))
		{
			$aInput = $aTemp[$sKrID]['input'];
			$aUploads = $aTemp[$sKrID]['files'];
		}
		else
		{
			$result = Model_KReport::getKReportAns(array(array('krYear','=',$iYear),array('krPeriod','=',$iPeriod),array('ttID','=',$this->aTeacher['ttID']),array('krSub','=',$iSub)));
			if (count($result))
			{
				$aAns = $result->as_array();
				foreach ($aAns as $aA)
				{
					if ($aA['krStyle'] == 2)
					{
						$aInput[$aA['krNO']] = array('text'=>$aA['krText']);
					}
					else
					{
						$aSel = array();
						for ($i = 1; $i <= $aA['krChoiceNum']; $i++)
						{
							if ($aA['krChoice'.$i])
							{
								$aSel[] = $i;
							}
							$sSel = implode('|',$aSel);
							$aInput[$aA['krNO']] = array('select'=>$sSel);
						}
					}
				}
			}
			$result = Model_KReport::getKReportPut(array(array('krYear','=',$iYear),array('krPeriod','=',$iPeriod),array('ttID','=',$this->aTeacher['ttID']),array('krSub','=',$iSub)));
			if (count($result))
			{
				$aPut = $result->current();


				for ($i = 1; $i < 5; $i++)
				{
					echo $aPut['krFile'.$i.'Name'];

					if ($aPut['krFile'.$i.'Name'])
					{
						$aUploads[] = array(
							'name' => $aPut['krFile'.$i.'Name'],
							'file' => $aPut['krFile'.$i.'File'],
							'size' => $aPut['krFile'.$i.'Size'],
						);
					}
				}
			}
		}

		$aMsg = Session::get('SES_T_KREPORT_MSG',false);
		$aMsg = ($aMsg)? unserialize($aMsg):null;
		Session::delete('SES_T_KREPORT_MSG');

		$this->template->content = View::forge('t/kreport/ans');
		$this->template->content->set('aReport',$aReport);
		$this->template->content->set('aQuery',$aQuery);
		$this->template->content->set('aInput',$aInput);
		$this->template->content->set('aUploads',$aUploads);
		$this->template->content->set('iSub',$iSub);
		$this->template->content->set('aMsg',$aMsg);
		$this->template->javascript = array('cl.t.kreport.js');
		return $this->template;
	}

	public function action_check($iYear,$iPeriod,$iSub)
	{
		$aReport = null;
		$aQuery = null;
		$aChk = self::KReportAnsChecker($iYear,$iPeriod,$this->aTeacher['ttID'],$aReport,$aQuery);
		$sKrID = $iYear.'-'.$iPeriod.'-'.$iSub;
		$sBack = '/t/kreport/ans/'.$iYear.DS.$iPeriod.DS.$iSub;

		$sRName = 'ケータイ研レポート提出（'.$iYear.'年度 '.(($iPeriod == 1)? '4～9月期':'10～3月期').'）';
		# パンくずリスト生成
		$this->template->set_global('breadcrumbs',array(array('name'=>$sRName)));
		# ページタイトル生成
		$this->template->set_global('pagetitle',$sRName);

		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}

		$aTemp = Session::get('SES_T_KREPORT_ANS_'.$iSub,false);
		if (!$aTemp)
		{
			Session::set('SES_T_ERROR_MSG','アンケートの回答情報が見つかりませんでした。');
			Response::redirect($sBack);
		}
		$aTemp = ($aTemp)? unserialize($aTemp):null;
		if (!isset($aTemp[$sKrID]))
		{
			Session::set('SES_T_ERROR_MSG','指定のアンケート回答情報が見つかりませんでした。');
			Response::redirect($sBack);
		}
		$aInput = $aTemp[$sKrID]['input'];
		$aUploads = $aTemp[$sKrID]['files'];

		$this->template->content = View::forge('t/kreport/check');
		$this->template->content->set('aReport',$aReport);
		$this->template->content->set('aQuery',$aQuery);
		$this->template->content->set('aInput',$aInput);
		$this->template->content->set('aUploads',$aUploads);
		$this->template->content->set('iSub',$iSub);
		$this->template->javascript = array('cl.t.kreport.js');
		return $this->template;
	}

	public function post_submit($iYear,$iPeriod,$iSub)
	{
		$aReport = null;
		$aQuery = null;
		$aChk = self::KReportAnsChecker($iYear,$iPeriod,$this->aTeacher['ttID'],$aReport,$aQuery);
		$sKrID = $iYear.'-'.$iPeriod.'-'.$iSub;
		$sBack = '/t/kreport/ans/'.$iYear.DS.$iPeriod.DS.$iSub;

		$sRName = 'ケータイ研レポート提出（'.$iYear.'年度 '.(($iPeriod == 1)? '4～9月期':'10～3月期').'）';
		# パンくずリスト生成
		$this->template->set_global('breadcrumbs',array(array('name'=>$sRName)));
		# ページタイトル生成
		$this->template->set_global('pagetitle',$sRName);

		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}
		$aSubmit = Input::post(null,false);
		if (!$aSubmit)
		{
			Session::set('SES_T_ERROR_MSG','正しく提出がされませんでした。');
			Response::redirect($sBack);
		}
		if ($aSubmit['state'] == 'back')
		{
			Response::redirect($sBack);
		}
		$aTemp = Session::get('SES_T_KREPORT_ANS_'.$iSub,false);
		if (!$aTemp)
		{
			Session::set('SES_T_ERROR_MSG','レポート回答情報が見つかりませんでした。');
			Response::redirect($sBack);
		}
		$aTemp = ($aTemp)? unserialize($aTemp):null;
		if (!isset($aTemp[$sKrID]))
		{
			Session::set('SES_T_ERROR_MSG','指定のレポート回答情報が見つかりませんでした。');
			Response::redirect($sBack);
		}
		$aInput = $aTemp[$sKrID]['input'];
		$aUploads = $aTemp[$sKrID]['files'];

		$bUpdate = false;
		$bNew = true;
		$result = Model_KReport::getKReportPut(array(array('krYear','=',$iYear),array('krPeriod','=',$iPeriod),array('ttID','=',$this->aTeacher['ttID']),array('krSub','=',$iSub)));
		if (count($result))
		{
			$aPut = $result->current();
			$bUpdate = true;
			if ($aPut['krStatus'])
			{
				$bNew = false;
			}
		}

		try
		{
			Model_KReport::setKReportPut($aReport,$aQuery,$this->aTeacher,$iSub,$aInput,$aUploads,1,$bUpdate);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		if ($bNew)
		{
			$result = Model_KReport::getKReportTarget(array(array('krYear','=',$aReport['krYear']),array('krPeriod','=',$aReport['krPeriod'])),null,array('ttName'=>'asc'));
			if (count($result))
			{
				$aTs = $result->as_array('ttID');

				foreach ($aTs as $sTtID => $aT)
				{
					$sMine = null;
					if ($this->aTeacher['ttID'] == $sTtID)
					{
						$sMine = '[このメールは本人確認用です]';
					}

					# レポート投稿メール
					$email = \Email::forge();
					$email->from(CL_MAIL_FROM, CL_MAIL_SENDER);
					$email->to($aT['ttMail']);
					$email->subject('[CL]ケータイ研レポート：レポート投稿のお知らせ');
					$body = View::forge('email/t_kreport_put', array('aP'=>$this->aTeacher,'aT'=>$aT,'sMine'=>$sMine));
					$email->body($body);

					try
					{
						\Log::warning('TeacherKReportPutMail - ' . $this->aTeacher['ttMail'] . ' -> ' . $aT['ttMail']);
						$email->send();
					}
					catch (\EmailValidationFailedException $e)
					{
						\Log::warning('TeacherKReportPutMail - ' . $e->getMessage());
					}
					catch (\EmailSendingFailedException $e)
					{
						\Log::warning('TeacherKReportPutMail - ' . $e->getMessage());
					}

				}
			}
		}

		Session::delete('SES_T_KREPORT_ANS_'.$iSub);

		Session::set('SES_T_NOTICE_MSG',$sRName.'の回答を提出しました。回答期間中は書き換え可能です。');
		Response::redirect('/t/kreport/put/'.$aReport['krYear'].'/'.$aReport['krPeriod']);
	}

	private function ReportChecker($iYear,$iPeriod,&$aReport)
	{
		$back = '/t/index';
		if (is_null($iYear) || is_null($iPeriod))
		{
			return array('msg'=>'レポート受付情報が送信されていません。','url'=>$back);
		}
		$result = Model_KReport::getKReportBase(array(array('krYear','=',$iYear),array('krPeriod','=',$iPeriod)));
		if (!count($result))
		{
			return array('msg'=>'指定されたレポート受付情報が見つかりません。','url'=>$back);
		}
		$aReport = $result->current();
		return true;
	}

	private function QueryChecker($iYear,$iPeriod,&$aReport,&$aQuery)
	{
		$back = '/t/index';
		if (is_null($iYear) || is_null($iPeriod))
		{
			return array('msg'=>'レポート受付情報が送信されていません。','url'=>$back);
		}
		$result = Model_KReport::getKReportBase(array(array('krYear','=',$iYear),array('krPeriod','=',$iPeriod)));
		if (!count($result))
		{
			return array('msg'=>'指定されたレポート受付情報が見つかりません。','url'=>$back);
		}
		$aReport = $result->current();

		$result = Model_KReport::getKReportQuery(array(array('krYear','=',$aReport['krYear']),array('krPeriod','=',$aReport['krPeriod'])));
		if (!count($result))
		{
			return array('msg'=>'指定されたレポートの設問が見つかりません。','url'=>$back);
		}
		$aQuery = $result->as_array();

		return true;
	}

	private function KReportAnsChecker($iYear,$iPeriod,$sTtID,&$aReport,&$aQuery)
	{
		$back = '/t/index';

		if (is_null($iYear) || is_null($iPeriod))
		{
			return array('msg'=>'回答するレポートが指定されていません。','url'=>$back);
		}

		$result = Model_KReport::getKReportTarget(array(array('ttID','=',$sTtID),array('krYear','=',$iYear),array('krPeriod','=',$iPeriod),array('krPublic','=',1)));
		if (!count($result))
		{
			return array('msg'=>'回答可能なレポート情報が見つかりませんでした。','url'=>$back);
		}
		$aReport = $result->current();

		$result = Model_KReport::getKReportQuery(array(array('krYear','=',$iYear),array('krPeriod','=',$iPeriod)));
		if (!count($result))
		{
			return array('msg'=>'指定のレポートには設問がありません。','url'=>$back);
		}
		$aQuery = $result->as_array();

		return true;
	}
}