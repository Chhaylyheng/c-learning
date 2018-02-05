<?php
class Controller_S_Quest extends Controller_S_Baseclass
{
	public function action_index()
	{
		$aQuest = null;
		$result = Model_Quest::getQuestBaseFromClass($this->aClass['ctID'],array(array('qb.qbPublic','>',0)),null,array('qb.qbSort'=>'desc'));
		if (count($result))
		{
			$aTemp = $result->as_array();
			foreach ($aTemp as $aQ)
			{
				$aQuest[$aQ['qbID']] = $aQ;
			}
		}
		if (!is_null($aQuest))
		{
			$aPut = null;
			$result = Model_Quest::getQuestPut(array(array('qb.ctID','=',$this->aClass['ctID']),array('qp.stID','=',$this->aStudent['stID'])));
			if (count($result))
			{
				$aPut = $result->as_array();
				foreach ($aPut as $aP)
				{
					if (array_key_exists($aP['qbID'],$aQuest))
					{
						$aQuest[$aP['qbID']]['QPut'] = $aP;
					}
				}
			}
		}

		# タイトル
		$sTitle = __('アンケート');
		$this->template->set_global('pagetitle',$sTitle);
		# パンくずリスト生成
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		$this->template->content = View::forge($this->vDir.DS.'quest/index');
		$this->template->content->set('aQuest',$aQuest);
		$this->template->javascript = array('cl.s.quest.js');
		return $this->template;
	}

	public function action_ans($sQbID,$sMat = null)
	{
		if (!is_null($sMat))
		{
			Session::set('CL_MAT_QUEST_'.$sQbID, $_SERVER['HTTP_REFERER']);
		}

		$aQuest = null;
		$aQuery = null;
		$aChk = self::QuestAnsChecker('ans',$sQbID,$aQuest,$aQuery,$this->aStudent['stID']);

		if (is_array($aChk))
		{
			Session::set('SES_S_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}

		$aQQs = array();
		foreach ($aQuery as $aQ)
		{
			$aQQs[$aQ['qqNO']] = $aQ;
		}

		if (Input::post(null,false))
		{
			$aPost = Input::post();
			$aMsg = null;
			$aInput = null;
			foreach ($aQuery as $aQ)
			{
				$iQqNO = $aQ['qqNO'];
				$bReq = (int)$aQ['qqRequired'];
				$aInput[$iQqNO]['select'] = '';
				$aInput[$iQqNO]['text'] = '';
				switch($aQ['qqStyle'])
				{
					case 0:
						if (!isset($aPost['radioSel_'.$iQqNO]))
						{
							if ($bReq)
							{
								$aMsg[$iQqNO] = __('選択は必須です。');
							}
							else
							{
								$aInput[$iQqNO]['select'] = null;
							}
						}
						else
						{
							$aInput[$iQqNO]['select'] = $aPost['radioSel_'.$iQqNO];
						}
					break;
					case 1:
						if (!isset($aPost['checkSel_'.$iQqNO]))
						{
							if ($bReq)
							{
								$aMsg[$iQqNO] = __('選択は必須です。');
							}
							else
							{
								$aInput[$iQqNO]['select'] = null;
							}
						}
						else
						{
							$sChecks = implode("|",$aPost['checkSel_'.$iQqNO]);
							$aInput[$iQqNO]['select'] = $sChecks;
						}
					break;
					case 2:
						if (!$aPost['textAns_'.$iQqNO])
						{
							if ($bReq)
							{
								$aMsg[$iQqNO] = __('入力は必須です。');
							}
							else
							{
								$aInput[$iQqNO]['text'] = null;
							}
						}
						else
						{
							$sTemp = preg_replace('/^[\s　]*(.*?)[\s　]*$/u', '$1', $aPost['textAns_'.$iQqNO]);
							$sTemp = mb_convert_kana($sTemp,"as",CL_ENC);
							$sTemp = str_replace(array("\r\n","\r"), "\n", $sTemp);
							$aInput[$iQqNO]['text'] = trim($sTemp);
						}
					break;
				}
			}
			Session::set('SES_S_QUEST_ANS_'.$sQbID,serialize(array($sQbID=>$aInput)));
			if (!is_null($aMsg))
			{
				Session::set('SES_S_QUEST_MSG_'.$sQbID,serialize($aMsg));
				Response::redirect('/s/quest/ans/'.$sQbID.$this->sesParam);
			}
			Response::redirect('/s/quest/check/'.$sQbID.$this->sesParam);
		}

		$aInput = null;
		$aTemp = Session::get('SES_S_QUEST_ANS_'.$sQbID,false);
		$aTemp = ($aTemp)? unserialize($aTemp):null;
		if (isset($aTemp[$sQbID]))
		{
			$aInput = $aTemp[$sQbID];
		}
		else
		{
			$result = Model_Quest::getQuestAns(array(array('qa.qbID','=',$sQbID),array('qa.stID','=',$this->aStudent['stID'])));
			if (count($result))
			{
				$aAns = $result->as_array();
				foreach ($aAns as $aA)
				{
					$aQQ = $aQQs[$aA['qqNO']];
					if ($aQQ['qqStyle'] == 2)
					{
						$aInput[$aA['qqNO']] = array('text'=>$aA['qaText']);
					}
					else
					{
						$aSel = array();
						for ($i = 1; $i <= $aQQ['qqChoiceNum']; $i++)
						{
							if ($aA['qaChoice'.$i])
							{
								$aSel[] = $i;
							}
							$sSel = implode('|',$aSel);
							$aInput[$aA['qqNO']] = array('select'=>$sSel);
						}
					}
				}
			}
		}

		# タイトル
		$sQuick = ($aQuest['qbQuickMode'])? '[Q]':'';
		$sTitle = $sQuick.$aQuest['qbTitle'];
		$this->template->set_global('pagetitle',$sTitle);
		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/quest','name'=>__('アンケート'));
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		$aMsg = Session::get('SES_S_QUEST_MSG_'.$sQbID,false);
		$aMsg = ($aMsg)? unserialize($aMsg):null;
		Session::delete('SES_S_QUEST_MSG_'.$sQbID);

		$this->template->content = View::forge($this->vDir.DS.'quest/ans');
		$this->template->content->set('aQuest',$aQuest);
		$this->template->content->set('aQuery',$aQuery);
		$this->template->content->set('aInput',$aInput);
		$this->template->content->set('aMsg',$aMsg);
		$this->template->javascript = array('cl.s.quest.js');
		return $this->template;
	}

	public function action_check($sQbID)
	{
		$aQuest = null;
		$aQuery = null;
		$sBackURL = '/s/quest/ans/'.$sQbID.$this->sesParam;
		$aChk = self::QuestAnsChecker('ans',$sQbID,$aQuest,$aQuery,$this->aStudent['stID']);

		if (is_array($aChk))
		{
			Session::set('SES_S_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}

		$aTemp = Session::get('SES_S_QUEST_ANS_'.$sQbID,false);
		if (!$aTemp)
		{
			Session::set('SES_S_ERROR_MSG',__('アンケートの回答情報が見つかりませんでした。'));
			Response::redirect($sBackURL);
		}
		$aTemp = ($aTemp)? unserialize($aTemp):null;
		if (!isset($aTemp[$sQbID]))
		{
			Session::set('SES_S_ERROR_MSG',__('指定のアンケート回答情報が見つかりませんでした。'));
			Response::redirect($sBackURL);
		}
		$aInput = $aTemp[$sQbID];

		# タイトル
		$sQuick = ($aQuest['qbQuickMode'])? '[Q]':'';
		$sTitle = $sQuick.$aQuest['qbTitle'];
		$this->template->set_global('pagetitle',$sTitle);
		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/quest','name'=>__('アンケート'));
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		$this->template->content = View::forge($this->vDir.DS.'quest/check');
		$this->template->content->set('aQuest',$aQuest);
		$this->template->content->set('aQuery',$aQuery);
		$this->template->content->set('aInput',$aInput);
		$this->template->javascript = array('cl.s.quest.js');
		return $this->template;
	}

	public function post_submit($sQbID)
	{
		$aQuest = null;
		$aQuery = null;
		$sBackURL = '/s/quest/ans/'.$sQbID.$this->sesParam;
		$aChk = self::QuestAnsChecker('ans',$sQbID,$aQuest,$aQuery,$this->aStudent['stID']);
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
		$aTemp = Session::get('SES_S_QUEST_ANS_'.$sQbID,false);
		if (!$aTemp)
		{
			Session::set('SES_S_ERROR_MSG',__('アンケートの回答情報が見つかりませんでした。'));
			Response::redirect($sBackURL);
		}
		$aTemp = ($aTemp)? unserialize($aTemp):null;
		if (!isset($aTemp[$sQbID]))
		{
			Session::set('SES_S_ERROR_MSG',__('指定のアンケート回答情報が見つかりませんでした。'));
			Response::redirect($sBackURL);
		}
		$aInput = $aTemp[$sQbID];

		$bUpdate = false;
		$result = Model_Quest::getQuestPut(array(array('qp.qbID','=',$sQbID),array('qp.stID','=',$this->aStudent['stID'])));
		if (count($result))
		{
			$bUpdate = true;
		}

		try
		{
			Model_Quest::setQuestPut($aQuest,$aQuery,$this->aStudent,$aInput,$bUpdate);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_S_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		Session::delete('SES_S_QUEST_ANS_'.$sQbID);

		Session::set('SES_S_NOTICE_MSG',__(':titleに回答を提出しました。',array('title'=>$aQuest['qbTitle'])));
		if ($sBack = Session::get('CL_MAT_QUEST_'.$sQbID, false))
		{
			Response::redirect($sBack.$this->sesParam);
		}
		Response::redirect('/s/quest'.$this->sesParam);
	}

	public function action_result($sID = null, $sMat = null)
	{
		if (!is_null($sMat))
		{
			Session::set('CL_MAT_QUEST_'.$sID, $_SERVER['HTTP_REFERER']);
		}

		$aQuest = null;
		$aQuery = null;
		$aChk = self::QuestAnsChecker('result',$sID,$aQuest,$aQuery,$this->aStudent['stID']);
		if (is_array($aChk))
		{
			Session::set('SES_S_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}
		$result = Model_Quest::getQuestPut(array(array('qp.qbID','=',$sID),array('qp.stID','=',$this->aStudent['stID'])));
		if (!count($result))
		{
			Session::set('SES_S_ERROR_MSG',__('未回答アンケートの結果を閲覧することはできません。'));
			Response::redirect('/s/quest'.$this->sesParam);
		}
		$aPut = $result->current();
		$result = Model_Quest::getQuestAns(array(array('qa.qbID','=',$sID),array('qa.stID','=',$this->aStudent['stID'])));
		if (!count($result))
		{
			Session::set('SES_S_ERROR_MSG',__('未回答アンケートの結果を閲覧することはできません。'));
			Response::redirect('/s/quest'.$this->sesParam);
		}
		$aAns = $result->as_array('qqNO');

		# タイトル
		$sQuick = ($aQuest['qbQuickMode'])? '[Q]':'';
		$sTitle = $sQuick.$aQuest['qbTitle'];
		$this->template->set_global('pagetitle',$sTitle);
		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/quest','name'=>__('アンケート'));
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		$this->template->content = View::forge($this->vDir.DS.'quest/result');
		$this->template->content->set('aQuest',$aQuest);
		$this->template->content->set('aQuery',$aQuery);
		$this->template->content->set('aPut',$aPut);
		$this->template->content->set('aAns',$aAns);
		$this->template->content->set('aStu',$this->aStudent);
		$this->template->content->set('bOther',false);
		$this->template->javascript = array('cl.s.quest.js');
		return $this->template;
	}

	public function action_bent($sID = null, $iTextBent = 0)
	{
		$aQuest = null;
		$aQuery = null;
		$aChk = self::QuestChecker($sID,$aQuest);
		if (is_array($aChk))
		{
			Session::set('SES_S_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}
		if (!$aQuest['qbBentPublic'])
		{
			Session::set('SES_S_ERROR_MSG',__('指定のアンケートの集計は公開されていません。'));
			Response::redirect('/s/quest'.$this->sesParam);
		}

		$result = Model_Quest::getQuestQuery(array(array('qbID','=',$sID)),null,array('qqSort'=>'asc'));
		if (!count($result))
		{
			Session::set('SES_S_ERROR_MSG',__('指定のアンケートには設問がありません。'));
			Response::redirect('/s/quest'.$this->sesParam);
		}
		$aRes = $result->as_array();
		foreach ($aRes as $aR)
		{
			$aQuery['qq'.$aR['qqNO']] = $aR;
		}

		$iPAll = $aQuest['qpNum'] + $aQuest['qpGNum'] + $aQuest['qpTNum'];
		$aBent = null;
		$result = Model_Quest::getQuestBent(array(array('qb.qbID','=',$sID),array('qb.qbMode','=','ALL'),array('qb.qbDate','>=',date('YmdHis',strtotime('-2min'))),array('qbAll','=',$iPAll)));

		if (!count($result))
		{
			try
			{
				$result = Model_Quest::setQuestBent($aQuest,$iTextBent);
			}
			catch (Exception $e)
			{
				\Clfunc_Common::LogOut($e,__CLASS__);
				Session::set('SES_S_ERROR_MSG',$e->getMessage());
				Response::redirect($this->eRedirect);
			}
		}
		$result = Model_Quest::getQuestBent(array(array('qb.qbID','=',$sID)));

		if (count($result))
		{
			$aRes = $result->as_array();
			foreach ($aRes as $aR)
			{
				$aBent[$aR['qbMode']]['qq'.$aR['qqNO']][$aR['qbNO']] = $aR;
			}
		}
		$aComment = null;
		if ($aQuest['qbQuickMode'] && isset($aQuery['qq2']))
		{
			$selResult = Model_Quest::getQuestAns(array(array('qa.qbID','=',$sID),array('qq.qqSort','=',1)),null,array('qa.qaDate'=>'DESC'));
			$txtResult = Model_Quest::getQuestBent(array(array('qb.qbID','=',$sID),array('qb.qqNO','=',2)),null,array('qa.qaDate'=>'DESC'));
			if (count($selResult) && count($txtResult))
			{
				$aSel = $selResult->as_array('stID');
				$aTxt = $txtResult->as_array();
				foreach ($aTxt as $aT)
				{
					$sStID = $aT['stID'];
					$aS = $aSel[$sStID];
					$sChoice = null;
					$iNO = null;
					for ($i = 1; $i <= $aQuery['qq1']['qqChoiceNum']; $i++)
					{
						if ($aS['qaChoice'.$i] == 1)
						{
							$sChoice = $aQuery['qq1']['qqChoice'.$i];
							$iNO = $i;
							break;
						}
					}

					$aComment[$aT['qbMode']][$sStID] = array(
						'text'    => $aT['qbText'],
						'cName'   => $sChoice,
						'cNO'     => $iNO,
						'cPick'   => $aT['qaPick'],
					);
				}
			}
		}

		# タイトル
		$sQuick = ($aQuest['qbQuickMode'])? '[Q]':'';
		$sTitle = $sQuick.$aQuest['qbTitle'].'｜'.__('集計結果');
		$this->template->set_global('pagetitle',$sTitle);
		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/quest','name'=>__('アンケート'));
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		$this->template->content = View::forge($this->vDir.DS.'quest/bent');
		$this->template->content->set('aQuest',$aQuest);
		$this->template->content->set_safe('aQuery',$aQuery);
		$this->template->content->set('aBent',$aBent);
		$this->template->content->set('aComment',$aComment);
		$this->template->javascript = array('Chart.js','cl.s.quest.js');
		return $this->template;
	}

	public function action_put($sID = null)
	{
		$aQuest = null;
		$aChk = self::QuestChecker($sID,$aQuest);
		if (is_array($aChk))
		{
			Session::set('SES_S_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}
		if (!$aQuest['qbAnsPublic'])
		{
			Session::set('SES_S_ERROR_MSG',__('他の人の回答内容は公開されていません。'));
			Response::redirect('/s/quest'.$this->sesParam);
		}

		$aStu = null;
		$result = Model_Student::getStudentFromClass($this->aClass['ctID'],null,null,array('st.stNO'=>'asc'));
		if (count($result))
		{
			$aRes = $result->as_array();
			foreach ($aRes as $aR)
			{
				if ($aR['stID'] != $this->aStudent['stID'])
				{
					$aStu[$aR['stID']]['stu'] = $aR;
				}
			}
		}
		$result = Model_Quest::getQuestPut(array(array('qp.qbID','=',$sID)));
		if (count($result))
		{
			$aRes = $result->as_array();
			foreach ($aRes as $aR)
			{
				if ($aR['stID'] == $this->aStudent['stID'])
				{
					continue;
				}
				if (isset($aStu[$aR['stID']]))
				{
					$aStu[$aR['stID']]['put'] = $aR;
				}
			}
		}

		if ($aQuest['qbAnsPublic'] == 1 && !is_null($aStu))
		{
			shuffle($aStu);
		}

		# タイトル
		$sQuick = ($aQuest['qbQuickMode'])? '[Q]':'';
		$sTitle = $sQuick.$aQuest['qbTitle'].'｜'.__('他の学生の回答');
		$this->template->set_global('pagetitle',$sTitle);
		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/quest','name'=>__('アンケート'));
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		$this->template->content = View::forge($this->vDir.DS.'quest/put');
		$this->template->content->set('aQuest',$aQuest);
		$this->template->content->set('aStu',$aStu);
		$this->template->javascript = array('cl.s.quest.js');
		return $this->template;
	}

	public function action_ansdetail($sID = null, $sSt = null)
	{
		$aQuest = null;
		$aQuery = null;
		$aStu = null;
		$aAns = null;
		$aPut = null;

		$aChk = self::QuestChecker($sID,$aQuest);
		if (is_array($aChk))
		{
			Session::set('SES_S_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}
		if (!$aQuest['qbAnsPublic'])
		{
			Session::set('SES_S_ERROR_MSG',__('他の人の回答内容は公開されていません。'));
			Response::redirect('/s/quest'.$this->sesParam);
		}

		$result = Model_Quest::getQuestQuery(array(array('qbID','=',$sID)),null,array('qqSort'=>'asc'));
		if (!count($result))
		{
			Session::set('SES_S_ERROR_MSG',__('指定のアンケートには設問がありません。'));
			Response::redirect('/s/quest'.$this->sesParam);
		}
		$aQuery = $result->as_array();

		$sStID = Crypt::decode($sSt);
		$result = Model_Student::getStudentFromClass($this->aClass['ctID'],array(array('sp.stID','=',$sStID)));
		if (!count($result))
		{
			Session::set('SES_S_ERROR_MSG',__('指定の学生が確認できませんでした。'));
			Response::redirect('/s/quest/put/'.$sID.$this->sesParam);
		}
		$aStu = $result->current();

		$result = Model_Quest::getQuestPut(array(array('qp.qbID','=',$sID),array('qp.stID','=',$sStID)));
		if (!count($result))
		{
			Session::set('SES_S_ERROR_MSG',__('指定の学生はアンケート未回答です。'));
			Response::redirect('/s/quest'.$this->sesParam);
		}
		$aPut = $result->current();

		$result = Model_Quest::getQuestAns(array(array('qa.qbID','=',$sID),array('qa.stID','=',$sStID)));
		if (!count($result))
		{
			Session::set('SES_S_ERROR_MSG',__('指定の学生はアンケート未回答です。'));
			Response::redirect('/s/quest/put/'.$sID.$this->sesParam);
		}
		$aAns = $result->as_array('qqNO');

		$bOther = false;
		# タイトル
		$sQuick = ($aQuest['qbQuickMode'])? '[Q]':'';
		$sTitle = $sQuick.$aQuest['qbTitle'];
		if ($aStu['stID'] != $this->aStudent['stID'])
		{
			$sName = __('他の学生（匿名）の回答').'｜';
			$bOther = true;
			if ($aQuest['qbAnsPublic'] == 2)
			{
				$sName = $aStu['stName'].'｜';
			}
			$sTitle = $sName.$sTitle;
		}


		$this->template->set_global('pagetitle',$sTitle);
		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/quest','name'=>__('アンケート'));
		$this->aBread[] = array('link'=>'/quest/put/'.$sID,'name'=>__('他の学生の回答'));
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		$this->template->content = View::forge($this->vDir.DS.'quest/result');
		$this->template->content->set('aQuest',$aQuest);
		$this->template->content->set('aQuery',$aQuery);
		$this->template->content->set('aStu',$aStu);
		$this->template->content->set('aAns',$aAns);
		$this->template->content->set('aPut',$aPut);
		$this->template->content->set('bOther',$bOther);
		$this->template->javascript = array('cl.s.quest.js');
		return $this->template;
	}

	private function QuestChecker($sQbID = null,&$aQuest = null)
	{
		if (is_null($sQbID))
		{
			return array('msg'=>__('アンケート情報が送信されていません。'),'url'=>'/s/quest'.$this->sesParam);
		}
		$result = Model_Quest::getQuestBaseFromClass($this->aClass['ctID'],array(array('qb.qbID','=',$sQbID),array('qb.qbPublic','!=',0)));
		if (!count($result))
		{
			return array('msg'=>__('指定されたアンケートが見つかりません。'),'url'=>'/s/quest'.$this->sesParam);
		}
		$aQuest = $result->current();

		return true;
	}

	private function QuestAnsChecker($sMode,$sQbID,&$aQuest,&$aQuery,$sStID = null)
	{
		if (is_null($sQbID))
		{
			return array('msg'=>__('アンケート情報が送信されていません。'),'url'=>'/s/quest'.$this->sesParam);
		}

		switch ($sMode)
		{
			case 'ans':
				$result = Model_Quest::getQuestBaseFromClass($this->aClass['ctID'],array(array('qb.qbID','=',$sQbID),array('qb.qbPublic','=',1)));
				if (!count($result))
				{
					return array('msg'=>__('回答可能なアンケート情報が見つかりませんでした。'),'url'=>'/s/quest'.$this->sesParam);
				}
				$aQuest = $result->current();

				if (!is_null($sStID) && !$aQuest['qbReAnswer'])
				{
					$result = Model_Quest::getQuestPut(array(array('qp.qbID','=',$sQbID),array('qp.stID','=',$sStID)));
					if (count($result))
					{
						return array('msg'=>__('指定のアンケートは既に回答済みです。'),'url'=>'/s/quest'.$this->sesParam);
					}
				}
			break;
			case 'result':
				$result = Model_Quest::getQuestBaseFromClass($this->aClass['ctID'],array(array('qb.qbID','=',$sQbID),array('qb.qbPublic','!=',0)));
				if (!count($result))
				{
					return array('msg'=>__('指定のアンケート情報が見つかりませんでした。'),'url'=>'/s/quest'.$this->sesParam);
				}
				$aQuest = $result->current();

				if (is_null($sStID))
				{
					return array('msg'=>__('指定の学生が確認できませんでした。'),'url'=>'/s/quest'.$this->sesParam);
				}
				$result = Model_Quest::getQuestPut(array(array('qp.qbID','=',$sQbID),array('qp.stID','=',$sStID)));
				if (!count($result))
				{
					return array('msg'=>__('未回答アンケートの結果を閲覧することはできません。'),'url'=>'/s/quest'.$this->sesParam);
				}
			break;
		}
		$result = Model_Quest::getQuestQuery(array(array('qbID','=',$sQbID)),null,array('qqSort'=>'asc'));
		if (!count($result))
		{
			return array('msg'=>__('指定のアンケートには設問がありません。'),'url'=>'/s/quest'.$this->sesParam);
		}
		$aQuery = $result->as_array();

		return true;
	}
}