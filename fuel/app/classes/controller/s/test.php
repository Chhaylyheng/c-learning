<?php
class Controller_S_Test extends Controller_S_Baseclass
{
	public function action_index()
	{
		$aTest = null;
		$result = Model_Test::getTestBaseFromClass($this->aClass['ctID'],array(array('tb.tbPublic','>',0)),null,array('tb.tbSort'=>'desc'));
		if (count($result))
		{
			$aTemp = $result->as_array();
			foreach ($aTemp as $aQ)
			{
				$aTest[$aQ['tbID']] = $aQ;
			}
		}
		if (!is_null($aTest))
		{
			$aPut = null;
			$result = Model_Test::getTestPut(array(array('tb.ctID','=',$this->aClass['ctID']),array('tp.stID','=',$this->aStudent['stID'])));
			if (count($result))
			{
				$aPut = $result->as_array();
				foreach ($aPut as $aP)
				{
					if (array_key_exists($aP['tbID'],$aTest))
					{
						$aTest[$aP['tbID']]['TPut'] = $aP;
					}
				}
			}
		}

		# タイトル
		$sTitle = __('小テスト');
		$this->template->set_global('pagetitle',$sTitle);
		# パンくずリスト生成
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		$this->template->content = View::forge($this->vDir.DS.'test/index');
		$this->template->content->set('aTest',$aTest);
		$this->template->javascript = array('cl.s.test.js');
		return $this->template;
	}

	public function action_ans($sTbID, $sMat = null)
	{
		if (!is_null($sMat))
		{
			Session::set('CL_MAT_TEST_'.$sTbID, $_SERVER['HTTP_REFERER']);
		}

		$aTest = null;
		$aQuery = null;
		$aChk = self::TestAnsChecker('ans',$sTbID,$aTest,$aQuery,$this->aStudent['stID']);

		if (is_array($aChk))
		{
			Session::set('SES_S_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}

		$aTimer = Session::get('SES_S_TEST_TIMER_'.$sTbID,false);
		$aTimer = ($aTimer)? unserialize($aTimer):null;
		if (!isset($aTimer[$sTbID]))
		{
			$aTimer[$sTbID] = time();
			Session::set('SES_S_TEST_TIMER_'.$sTbID,serialize($aTimer));
		}

		if (Input::post(null,false))
		{
			$aPost = Input::post();
			$aMsg = null;
			$aInput = null;
			foreach ($aQuery as $aQ)
			{
				$iTqNO = $aQ['tqNO'];
				$aInput[$iTqNO]['select'] = '';
				$aInput[$iTqNO]['text'] = '';
				switch($aQ['tqStyle'])
				{
					case 0:
						if (isset($aPost['radioSel_'.$iTqNO]))
						{
							$aInput[$iTqNO]['select'] = $aPost['radioSel_'.$iTqNO];
						}
					break;
					case 1:
						if (isset($aPost['checkSel_'.$iTqNO]))
						{
							$sChecks = implode("|",$aPost['checkSel_'.$iTqNO]);
							$aInput[$iTqNO]['select'] = $sChecks;
						}
					break;
					case 2:
						if (isset($aPost['textAns_'.$iTqNO]))
						{
							$sTemp = ClFunc_Common::convertKana(preg_replace(CL_WHITE_TRIM_PTN, '$1', $aPost['textAns_'.$iTqNO]),'aqpu');
							$sTemp = str_replace(array("\r\n","\r"), "\n", $sTemp);
							$aInput[$iTqNO]['text'] = trim($sTemp);
						}
					break;
				}
			}
			Session::set('SES_S_TEST_ANS_'.$sTbID,serialize(array($sTbID=>$aInput)));
			if (!is_null($aMsg))
			{
				Session::set('SES_S_TEST_MSG',serialize($aMsg));
				Response::redirect('/s/test/ans/'.$sTbID.$this->sesParam);
			}
			Response::redirect('/s/test/check/'.$sTbID.$this->sesParam);
		}

		$aInput = null;
		$aTemp = Session::get('SES_S_TEST_ANS_'.$sTbID,false);
		$aTemp = ($aTemp)? unserialize($aTemp):null;
		if (isset($aTemp[$sTbID]))
		{
			$aInput = $aTemp[$sTbID];
		}
		else
		{
			$result = Model_Test::getTestAns(array(array('ta.tbID','=',$sTbID),array('ta.stID','=',$this->aStudent['stID'])));
			if (count($result))
			{
				$aAns = $result->as_array();
				foreach ($aAns as $aA)
				{
					if ($aA['tqStyle'] == 2)
					{
						$aInput[$aA['tqNO']] = array('text'=>$aA['taText']);
					}
					else
					{
						$aSel = array();
						for ($i = 1; $i <= $aA['tqChoiceNum']; $i++)
						{
							if ($aA['taChoice'.$i])
							{
								$aSel[] = $i;
							}
							$sSel = implode('|',$aSel);
							$aInput[$aA['tqNO']] = array('select'=>$sSel);
						}
					}
				}
			}
		}

		# タイトル
		$sTitle = $aTest['tbTitle'];
		$this->template->set_global('pagetitle',$sTitle);
		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/test','name'=>__('小テスト'));
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		$aMsg = Session::get('SES_S_TEST_MSG',false);
		$aMsg = ($aMsg)? unserialize($aMsg):null;
		Session::delete('SES_S_TEST_MSG');

		$this->template->content = View::forge($this->vDir.DS.'/test/ans');
		$this->template->content->set('aTest',$aTest);
		$this->template->content->set('aQuery',$aQuery);
		$this->template->content->set('aInput',$aInput);
		$this->template->content->set('aMsg',$aMsg);
		$this->template->javascript = array('cl.s.test.js');
		return $this->template;
	}

	public function action_check($sTbID)
	{
		$aTest = null;
		$aQuery = null;
		$sBackURL = '/s/test/ans/'.$sTbID.$this->sesParam;
		$aChk = self::TestAnsChecker('ans',$sTbID,$aTest,$aQuery,$this->aStudent['stID']);

		if (is_array($aChk))
		{
			Session::set('SES_S_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}

		$aTemp = Session::get('SES_S_TEST_ANS_'.$sTbID,false);
		if (!$aTemp)
		{
			Session::set('SES_S_ERROR_MSG',__('小テストの解答情報が見つかりませんでした。'));
			Response::redirect($sBackURL);
		}
		$aTemp = ($aTemp)? unserialize($aTemp):null;
		if (!isset($aTemp[$sTbID]))
		{
			Session::set('SES_S_ERROR_MSG',__('指定の小テスト解答情報が見つかりませんでした。'));
			Response::redirect($sBackURL);
		}
		$aInput = $aTemp[$sTbID];

		# タイトル
		$sTitle = $aTest['tbTitle'];
		$this->template->set_global('pagetitle',$sTitle);
		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/test','name'=>__('小テスト'));
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		$this->template->content = View::forge($this->vDir.DS.'test/check');
		$this->template->content->set('aTest',$aTest);
		$this->template->content->set('aQuery',$aQuery);
		$this->template->content->set('aInput',$aInput);
		$this->template->javascript = array('cl.s.test.js');
		return $this->template;
	}

	public function post_submit($sTbID)
	{
		$aTest = null;
		$aQuery = null;
		$sBackURL = '/s/test/ans/'.$sTbID.$this->sesParam;
		$aChk = self::TestAnsChecker('ans',$sTbID,$aTest,$aQuery,$this->aStudent['stID']);
		if (is_array($aChk))
		{
			Session::set('SES_S_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}
		$aSubmit = Input::post(null,false);
		if (!$aSubmit)
		{
			Session::set('SES_S_ERROR_MSG',__('正しく提出がされませんでした。'));
			Response::redirect($sBackURL);
		}
		if (isset($aSubmit['back']))
		{
			Response::redirect($sBackURL);
		}
		$aTemp = Session::get('SES_S_TEST_ANS_'.$sTbID,false);
		if (!$aTemp)
		{
			Session::set('SES_S_ERROR_MSG',__('小テストの解答情報が見つかりませんでした。'));
			Response::redirect($sBackURL);
		}
		$aTemp = ($aTemp)? unserialize($aTemp):null;
		if (!isset($aTemp[$sTbID]))
		{
			Session::set('SES_S_ERROR_MSG',__('指定の小テスト解答情報が見つかりませんでした。'));
			Response::redirect($sBackURL);
		}
		$aInput = $aTemp[$sTbID];

		$bUpdate = false;
		$result = Model_Test::getTestPut(array(array('tp.tbID','=',$sTbID),array('tp.stID','=',$this->aStudent['stID'])));
		if (count($result))
		{
			$bUpdate = true;
		}

		$aTimer = Session::get('SES_S_TEST_TIMER_'.$sTbID,false);
		$aTimer = ($aTimer)? unserialize($aTimer):null;
		if (isset($aTimer[$sTbID]))
		{
			$iTime = time() - $aTimer[$sTbID];
			unset($aTimer[$sTbID]);
		}
		else
		{
			$iTime = 0;
		}

		try
		{
			Model_Test::setTestPut($aTest,$aQuery,$this->aStudent,$aInput,$iTime,$bUpdate);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_S_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		Session::delete('SES_S_TEST_ANS_'.$sTbID);
		Session::set('SES_S_TEST_TIMER_'.$sTbID,serialize($aTimer));

		Session::set('SES_S_NOTICE_MSG',__(':nameに解答を提出しました。',array('name'=>$aTest['tbTitle'])));
		if ($sBack = Session::get('CL_MAT_TEST_'.$sTbID, false))
		{
			Response::redirect($sBack.$this->sesParam);
		}
		Response::redirect('/s/test'.$this->sesParam);
	}

	public function action_result($sID = null)
	{
		$aTest = null;
		$aQuery = null;
		$aChk = self::TestAnsChecker('result',$sID,$aTest,$aQuery,$this->aStudent['stID']);
		if (is_array($aChk))
		{
			Session::set('SES_S_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}
		$result = Model_Test::getTestPut(array(array('tp.tbID','=',$sID),array('tp.stID','=',$this->aStudent['stID'])));
		if (!count($result))
		{
			Session::set('SES_S_ERROR_MSG',__('未解答小テストの結果を閲覧することはできません。'));
			Response::redirect('/s/test'.$this->sesParam);
		}
		$aPut = $result->current();
		$result = Model_Test::getTestAns(array(array('ta.tbID','=',$sID),array('ta.stID','=',$this->aStudent['stID'])));
		if (!count($result))
		{
			Session::set('SES_S_ERROR_MSG',__('未解答小テストの結果を閲覧することはできません。'));
			Response::redirect('/s/test'.$this->sesParam);
		}
		$aAns = $result->as_array();

		# タイトル
		$sTitle = $aTest['tbTitle'];
		$this->template->set_global('pagetitle',$sTitle);
		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/test','name'=>__('小テスト'));
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		$this->template->content = View::forge($this->vDir.DS.'test/result');
		$this->template->content->set('aTest',$aTest);
		$this->template->content->set('aQuery',$aQuery);
		$this->template->content->set('aPut',$aPut);
		$this->template->content->set('aAns',$aAns);
		$this->template->content->set('aStu',$this->aStudent);
		$this->template->javascript = array('cl.s.test.js');
		return $this->template;
	}

	private function TestChecker($sTbID = null,&$aTest = null)
	{
		if (is_null($sTbID))
		{
			return array('msg'=>__('小テスト情報が送信されていません。'),'url'=>'/s/test'.$this->sesParam);
		}
		$result = Model_Test::getTestBaseFromClass($this->aClass['ctID'],array(array('tb.tbID','=',$sTbID),array('tb.tbPublic','!=',0)));
		if (!count($result))
		{
			return array('msg'=>__('指定された小テストが見つかりません。'),'url'=>'/s/test'.$this->sesParam);
		}
		$aTest = $result->current();

		return true;
	}

	private function TestAnsChecker($sMode,$sTbID,&$aTest,&$aQuery,$sStID = null)
	{
		if (is_null($sTbID))
		{
			return array('msg'=>__('小テストが指定されていません。'),'url'=>'/s/test'.$this->sesParam);
		}

		switch ($sMode)
		{
			case 'ans':
				$result = Model_Test::getTestBaseFromClass($this->aClass['ctID'],array(array('tb.tbID','=',$sTbID),array('tb.tbPublic','=',1)));
				if (!count($result))
				{
					return array('msg'=>__('解答可能な小テスト情報が見つかりませんでした。'),'url'=>'/s/test'.$this->sesParam);
				}
				$aTest = $result->current();

				if (!is_null($sStID))
				{
					$result = Model_Test::getTestPut(array(array('tp.tbID','=',$sTbID),array('tp.stID','=',$sStID)));
					if (count($result))
					{
						return array('msg'=>__('指定の小テストは既に解答済みです。'),'url'=>'/s/test'.$this->sesParam);
					}
				}
			break;
			case 'result':
				$result = Model_Test::getTestBaseFromClass($this->aClass['ctID'],array(array('tb.tbID','=',$sTbID),array('tb.tbPublic','!=',0)));
				if (!count($result))
				{
					return array('msg'=>__('指定の小テスト情報が見つかりませんでした。'),'url'=>'/s/test'.$this->sesParam);
				}
				$aTest = $result->current();

				if (is_null($sStID))
				{
					return array('msg'=>__('指定の学生が確認できませんでした。'),'url'=>'/s/test'.$this->sesParam);
				}
				$result = Model_Test::getTestPut(array(array('tp.tbID','=',$sTbID),array('tp.stID','=',$sStID)));
				if (!count($result))
				{
					return array('msg'=>__('未解答小テストの結果を閲覧することはできません。'),'url'=>'/s/test'.$this->sesParam);
				}
			break;
		}
		$result = Model_Test::getTestQuery(array(array('tbID','=',$sTbID)),null,array('tqSort'=>'asc'));
		if (!count($result))
		{
			return array('msg'=>__('指定の小テストには問題がありません。'),'url'=>'/s/test'.$this->sesParam);
		}
		$aQuery = $result->as_array();

		return true;
	}
}