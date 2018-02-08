<?php
class Controller_T_Ajax_Quest extends Controller_T_Ajax
{
	public function post_Sort()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>__('アンケート情報が正しく送信されていません。'));
		$par = Input::post();
		if ($par)
		{
			$result = Model_Quest::getQuestBaseFromID($par['qb']);
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('指定のアンケート情報が見つかりません。'));
				$this->response($res);
				return;
			}
			$aQuest = $result->current();
			$result = Model_Quest::getQuestBaseFromClass($par['ct'],null,null,array('qb.qbSort'=>'desc'));
			if (!count($result))
			{
				$res = array('err'=>-1,'res'=>'','msg'=>__('並び順を変更するアンケートの情報が見つかりません。'));
				$this->response($res);
				return;
			}
			$iMax = count($result);
			if (($aQuest['qbSort'] == $iMax && $par['m'] == 'up') || ($aQuest['qbSort'] == 1 && $par['m'] == 'down'))
			{
				$res = array('err'=>-1,'res'=>'','msg'=>__('一番上か一番下のアンケートのため、並び順を変更することはできません。'));
				$this->response($res);
				return;
			}
			$result = Model_Quest::sortQuest($aQuest,$par['m']);
			$res = array('err'=>0,'res'=>array('m'=>$par['m']));
		}
		$this->response($res);
		return;
	}

	public function post_QuerySort()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>__('アンケート情報が正しく送信されていません。'));
		$par = Input::post();
		if ($par)
		{
			$result = Model_Quest::getQuestQuery(array(array('qbID','=',$par['qb']),array('qqNO','=',$par['qn'])));
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('指定のアンケート設問が見つかりません。'));
				$this->response($res);
				return;
			}
			$aQuery = $result->current();

			$result = Model_Quest::getQuestQuery(array(array('qbID','=',$par['qb'])));
			if (!count($result))
			{
				$res = array('err'=>-1,'res'=>'','msg'=>__('並び順を変更するアンケートの設問が見つかりません。'));
				$this->response($res);
				return;
			}
			$iMax = count($result);
			if (($aQuery['qqSort'] == $iMax && $par['m'] == 'down') || ($aQuery['qqSort'] == 1 && $par['m'] == 'up'))
			{
				$res = array('err'=>-1,'res'=>'','msg'=>__('一番上か一番下の設問のため、並び順を変更することはできません。'));
				$this->response($res);
				return;
			}
			$result = Model_Quest::sortQuestQuery($aQuery,$par['m']);
			$res = array('err'=>0,'res'=>array('m'=>$par['m'],'qs'=>$aQuery['qqSort']));
		}
		$this->response($res);
		return;
	}

	public function post_BentPublic()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>__('アンケート情報が正しく送信されていません。'));
		$par = Input::post();
		if ($par)
		{
			$result = Model_Quest::getQuestBaseFromID($par['qb'],array(array('qb.ctID','=',$par['ct'])));
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('指定のアンケート情報が見つかりません。'));
				$this->response($res);
				return;
			}
			$aQuest = $result->current();

			if ($par['m'] == 'public')
			{
				$iBent = 1;
				$sText = __('公開中');
				$sClass = 'font-blue';
			}
			elseif ($par['m'] == 'private')
			{
				$iBent = 0;
				$sText = __('非公開');
				$sClass = 'font-default';
			}
			else
			{
				$res = array('err'=>-1,'res'=>'','msg'=>__('公開情報が正しく送信されていません。'));
				$this->response($res);
				return;
			}

			$aUpdate = array(
					'qbBentPublic'=>$iBent,
			);
			$result = Model_Quest::updateQuest($aUpdate,array(array('qbID','=',$aQuest['qbID'])));
			$res = array('err'=>0,'res'=>array('class'=>$sClass,'text'=>$sText),'msg'=>__('集計公開を変更しました。'));
		}
		$this->response($res);
		return;
	}

	public function post_Public()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>__('アンケート情報が正しく送信されていません。'));
		$par = Input::post();
		if ($par)
		{
			$result = Model_Quest::getQuestBaseFromID($par['qb'],array(array('qb.ctID','=',$par['ct'])));
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('指定のアンケート情報が見つかりません。'));
				$this->response($res);
				return;
			}
			$aQuest = $result->current();
			if ($aQuest['qbNum'] == 0)
			{
				$res = array('err'=>-1,'res'=>'','msg'=>__('設問がないため公開情報を変更することはできません。'));
				$this->response($res);
				return;
			}

			$bPut = 0;
			$result = Model_Quest::getQuestPut(array(array('qp.qbID','=',$aQuest['qbID']),array('qp.stID','=',$this->aTeacher['ttID'])));
			if (count($result))
			{
				$bPut = 1;
			}

			$aUpdate = array();
			$sDate = date('YmdHis');
			$sTimer = null;
			if ($par['m'] == 'public')
			{
				$iBent = 1;
				$sText = __('公開中');
				$sClass = 'font-blue';
				$aUpdate['qbAutoPublicDate'] = CL_DATETIME_DEFAULT;
				$aUpdate['qbPublicDate'] = $sDate;
				if ($aQuest['qbAutoCloseDate'] != CL_DATETIME_DEFAULT)
				{
					$sTimer = '～ '.Clfunc_Tz::tz('n/j H:i',$this->tz,$aQuest['qbAutoCloseDate']);
				}
				if ($bPut)
				{
					$sMode = 'Default';
					$sURL = '/t/quest/result/'.$aQuest['qbID'];
				}
				else
				{
					$sMode = 'Default';
					$sURL = '/t/quest/ans/'.$aQuest['qbID'];
				}
			}
			elseif ($par['m'] == 'close')
			{
				$iBent = 2;
				$sText = __('締切');
				$aUpdate['qbAutoCloseDate'] = CL_DATETIME_DEFAULT;
				$aUpdate['qbCloseDate'] = $sDate;
				$sClass = 'font-red';
				if ($bPut)
				{
					$sMode = 'Default';
					$sURL = '/t/quest/ansdetail/'.$aQuest['qbID'].DS.$this->aTeacher['ttID'];
				}
				else
				{
					$sMode = 'Disable';
					$sURL = '';
				}
			}
			elseif ($par['m'] == 'private')
			{
				$iBent = 0;
				$sText = __('非公開');
				$sClass = 'font-default';
				$aUpdate['qbPublicDate'] = CL_DATETIME_DEFAULT;
				$aUpdate['qbCloseDate'] = CL_DATETIME_DEFAULT;
				if ($aQuest['qbAutoPublicDate'] != CL_DATETIME_DEFAULT)
				{
					$sTimer = ClFunc_Tz::tz('n/j H:i',$this->tz,$aQuest['qbAutoPublicDate']).' ～';
				}
				if ($bPut)
				{
					$sMode = 'Default';
					$sURL = '/t/quest/ansdetail/'.$aQuest['qbID'].DS.$this->aTeacher['ttID'];
				}
				else
				{
					$sMode = 'Disable';
					$sURL = '';
				}
			}
			else
			{
				$res = array('err'=>-1,'res'=>'','msg'=>__('公開情報が正しく送信されていません。'));
				$this->response($res);
				return;
			}

			$aUpdate['qbPublic'] = $iBent;
			$result = Model_Quest::updateQuest($aUpdate,array(array('qbID','=',$aQuest['qbID'])));
			$res = array('err'=>0,'res'=>array('class'=>$sClass,'text'=>$sText,'timer'=>$sTimer, 'put'=>$bPut, 'url'=>$sURL, 'mode'=>$sMode),'msg'=>__('公開情報を変更しました。'));
		}
		$this->response($res);
		return;
	}

	public function post_PutReset()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>__('アンケート情報が正しく送信されていません。'));
		$par = Input::post();
		if ($par)
		{
			$result = Model_Quest::getQuestBaseFromID($par['qb'],array(array('qb.ctID','=',$par['ct'])));
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('指定のアンケート情報が見つかりません。'));
				$this->response($res);
				return;
			}
			$aQuest = $result->current();

			$result = Model_Quest::deleteQuestPut($aQuest['qbID']);
			$res = array('err'=>0,'res'=>'','msg'=>__('提出状況をリセットしました。'));
		}
		$this->response($res);
		return;
	}

	public function post_QueryLoad()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>__('アンケート情報が正しく送信されていません。'));
		$par = Input::post();
		if ($par)
		{
			$result = Model_Quest::getQuestQuery(array(array('qbID','=',$par['qb']),array('qqNO','=',$par['qn'])));
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('指定のアンケート設問が見つかりません。'));
				$this->response($res);
				return;
			}
			$aQuery = $result->current();

			$sTempPath = CL_UPPATH.DS.$aQuery['qbID'].DS.$aQuery['qqSort'].'_tmp';
			$sQueryPath = CL_UPPATH.DS.$aQuery['qbID'].DS.$aQuery['qqNO'];
			if (file_exists($sTempPath))
			{
				system('rm -rf '.$sTempPath);
			}
			if (file_exists($sQueryPath))
			{
				system('cp -Rfp '.$sQueryPath.' '.$sTempPath);
			}

			$res = array('err'=>0,'res'=>$aQuery,'msg'=>'', 'path'=>DS.CL_UPDIR.DS.$aQuery['qbID'].DS.$aQuery['qqSort'].'_tmp'.DS);
		}
		$this->response($res);
		return;
	}

	public function post_QueryImageDelete()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>__('アンケート情報が正しく送信されていません。'));
		$par = Input::post();
		if ($par)
		{
			$sTempPath = CL_UPPATH.DS.$par['qb'].DS.$par['qs'].'_tmp'.DS;
			list($fn,$ex) = explode('.', $par['fn']);

			$result = Model_Quest::getQuestQuery(array(array('qbID','=',$par['qb']),array('qqSort','=',$par['qs'])));
			if (count($result))
			{
				$aQuery = $result->current();

				$sImgPath = CL_UPPATH.DS.$aQuery['qbID'].DS.$aQuery['qqNO'].DS;

				if ($fn == 'base')
				{
					$aUpdate = array('qqImage'=>'');
				}
				else
				{
					$aUpdate = array('qqChoiceImg'.$fn=>'');
				}
				$result = Model_Quest::updateQuestQuery($aUpdate,array(array('qbID','=',$aQuery['qbID']),array('qqNO','=',$aQuery['qqNO'])));
				if (file_exists($sImgPath.$par['fn']))
				{
					File::delete($sImgPath.$par['fn']);
					File::delete($sImgPath.CL_Q_SMALL_PREFIX.$par['fn']);
				}
			}

			if (file_exists($sTempPath.$par['fn']))
			{
				File::delete($sTempPath.$par['fn']);
				File::delete($sTempPath.CL_Q_SMALL_PREFIX.$par['fn']);
			}
			$res = array('err'=>0,'res'=>array('del'=>$fn), 'msg'=>'');
		}
		$this->response($res);
		return;
	}

	public function post_TextPickUp()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>__('アンケート情報が正しく送信されていません。'));
		$par = Input::post();
		if ($par)
		{
			$result = Model_Quest::getQuestAns(array(array('qa.qbID','=',$par['qb']),array('qa.qqNO','=',$par['qn']),array('qa.stID','=',$par['st'])));
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('指定のアンケート情報が見つかりません。'));
				$this->response($res);
				return;
			}
			$aBent = $result->current();

			if ($aBent['qaPick'] == $par['m'])
			{
				$res = array('err'=>0,'res'=>0);
				$this->response($res);
				return;
			}
			else
			{
				switch ($par['m'])
				{
					case '1':
						$sStar = Asset::get_file('icon_pick_a.png','img');
						$sClass = 'bg-success';
						break;
					case '0':
						$sStar = Asset::get_file('icon_pick_b.png','img');
						$sClass = '';
						break;
					case '-1':
						$sStar = Asset::get_file('icon_pick_c.png','img');
						$sClass = 'text-muted';
						break;
				}
				try
				{
					$result = Model_Quest::updateQuestPick((int)$par['m'],$aBent['qbID'],$aBent['qqNO'],$aBent['stID']);
				}
				catch(Exception $e)
				{
					\Clfunc_Common::LogOut($e,__CLASS__);
					$res = array('err'=>-3,'res'=>'','msg'=>$e->getMessage());
					$this->response($res);
					return;
				}
			}
			$res = array('err'=>0,'res'=>array('class'=>$sClass,'img'=>$sStar));
		}
		$this->response($res);
		return;
	}

	public function post_Bent()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>__('アンケート情報が正しく送信されていません。'));
		$par = Input::post();
		if ($par)
		{
			$result = Model_Quest::getQuestBaseFromID($par['qb']);
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('指定のアンケート情報が見つかりません。'));
				$this->response($res);
				return;
			}
			$aQuest = $result->current();

			$result = Model_Quest::getQuestQuery(array(array('qbID','=',$par['qb'])),null,array('qqSort'=>'asc'));
			if (!count($result))
			{
				Session::set('SES_T_ERROR_MSG',__('指定のアンケートには設問がありません。'));
				Response::redirect('/t/quest');
			}
			$aRes = $result->as_array();
			foreach ($aRes as $aR)
			{
				$aQuery['qq'.$aR['qqNO']] = $aR;
			}

			try
			{
				$result = Model_Quest::setQuestBent($aQuest,0);
			}
			catch (Exception $e)
			{
				\Clfunc_Common::LogOut($e,__CLASS__);
				$res = array('err'=>-3,'res'=>'','msg'=>$e->getMessage());
				$this->response($res);
				return;
			}
			$aBent = null;
			$result = Model_Quest::getQuestBent(array(array('qb.qbID','=',$par['qb']),array('qb.qqNO','=',1),array('qb.qbMode','=',$par['mode'])));
			if (count($result))
			{
				$aRes = $result->as_array();
				foreach ($aRes as $aR)
				{
					$per = ($aR['qbAll'])? round(($aR['qbNum']/$aR['qbAll'])*100,1):0;
					$aBent[$aR['qbMode']][$aR['qbNO']] = array(
						'num' => (int)$aR['qbNum'],
						'per' => $per,
					);
				}
			}

			$aComment = null;
			if ($par['com'])
			{
				$selResult = Model_Quest::getQuestAns(array(array('qa.qbID','=',$par['qb']),array('qq.qqSort','=',1)),null,array('qa.qaDate'=>'ASC'));
				$txtResult = Model_Quest::getQuestBent(array(array('qb.qbID','=',$par['qb']),array('qb.qqNO','=',2),array('qb.qbMode','=',$par['mode'])),null,array('qa.qaDate'=>'DESC'));
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

						$sName = '['.__('匿名').']';
						if (preg_match('/^s.+/',$sStID))
						{
							$sName = $aT['qpstName'];
						}
						if (preg_match('/^t.+/',$sStID))
						{
							$sName = ($aT['qpstName'])? $aT['qpstName']:$aT['ttName'];
						}
						elseif ($aQuest['qbOpen'] == 2)
						{
							$sName = (($aT['qpstName'])? $aT['qpstName']:(($aT['gtName'])? $aT['gtName']:'[GUEST]'));
						}

						$aComment[$aT['qbMode']][$sStID] = array(
							'text'    => nl2br($aT['qbText']),
							'cName'   => $sChoice,
							'cNO'     => $iNO,
							'cPick'   => $aT['qaPick'],
							'cPosted' => $sName,
						);
					}
				}
			}
			$data = array('bent'=>$aBent,'comment'=>$aComment,'quest'=>array('qpNum'=>$aQuest['qpNum'],'scNum'=>$aQuest['scNum'],'qpGNum'=>$aQuest['qpGNum'],'qpTNum'=>$aQuest['qpTNum']));
			$res = array('err'=>0,'res'=>$data,'msg'=>'');
		}
		$this->response($res);
		return;
	}

	public function post_BentComment()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>__('アンケート情報が正しく送信されていません。'));
		$par = Input::post();
		if ($par)
		{
			$result = Model_Quest::getQuestBaseFromID($par['qb']);
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('指定のアンケートが見つかりません。'));
				$this->response($res);
				return;
			}
			$aQuest = $result->current();

			try
			{
				$result = Model_Quest::setQuestBent($aQuest,0);
			}
			catch (Exception $e)
			{
				\Clfunc_Common::LogOut($e,__CLASS__);
				$res = array('err'=>-3,'res'=>'','msg'=>$e->getMessage());
				$this->response($res);
				return;
			}

			$aComment = null;
			$txtResult = Model_Quest::getQuestBent(array(array('qb.qbID','=',$par['qb']),array('qb.qqNO','=',1),array('qb.qbMode','=',$par['mode'])),null,array('qa.qaDate'=>'ASC'));
			if (count($txtResult))
			{
				$aTxt = $txtResult->as_array();
				foreach ($aTxt as $aT)
				{
					$sStID = $aT['stID'];
					$aComment[$aT['qbMode']][$sStID] = array(
						'text'  => nl2br($aT['qbText']),
						'cPick' => $aT['qaPick'],
					);

					$sName = '['.__('匿名').']';
					if (preg_match('/^s.+/',$sStID))
					{
						$sName = $aT['qpstName'];
					}
					elseif (preg_match('/^t.+/',$sStID))
					{
						$sName = ($aT['qpstName'])? $aT['qpstName']:$aT['ttName'];
					}
					elseif ($aQuest['qbOpen'] == 2)
					{
						$sName = (($aT['qpstName'])? $aT['qpstName']:(($aT['gtName'])? $aT['gtName']:'[GUEST]'));
					}
					$aComment[$aT['qbMode']][$sStID]['cPosted'] = $sName;
				}
			}

			$data = array('comment'=>$aComment,'quest'=>array('qpNum'=>$aQuest['qpNum'],'scNum'=>$aQuest['scNum'],'qpGNum'=>$aQuest['qpGNum'],'qpTNum'=>$aQuest['qpTNum']));
			$res = array('err'=>0,'res'=>$data,'msg'=>'');
		}
		$this->response($res);
		return;
	}

	public function post_TeachComment()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>__('アンケート情報が正しく送信されていません。'));
		$par = Input::post();
		if ($par)
		{
			$result = Model_Quest::getQuestBaseFromID($par['qb']);
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('指定のアンケート情報が見つかりません。'));
				$this->response($res);
				return;
			}
			$aQuest = $result->current();

			try
			{
				if ($par['st'] == 'ALL')
				{
					$aUpdate = array(
						'qbComment'=>$par['com'],
					);
					$result = Model_Quest::updateQuest($aUpdate,array(array('qbID','=',$aQuest['qbID'])));
				}
				else
				{
					$result = Model_Quest::getQuestPut(array(array('qp.qbID','=',$aQuest['qbID']),array('qp.stID','=',$par['st'])));
					if (!count($result))
					{
						$res = array('err'=>-2,'res'=>'','msg'=>__('指定の学生が見つかりません。'));
						$this->response($res);
						return;
					}
					$aPut = $result->current();

					$aUpdate = array(
						'qpComment'=>$par['com'],
						'qpComDate'=>($par['com'] != '')? date('YmdHis'):CL_DATETIME_DEFAULT,
					);
					$result = Model_Quest::updateQuestPut($aUpdate,array(array('qbID','=',$aQuest['qbID']),array('stID','=',$aPut['stID'])));
				}
			}
			catch (Exception $e)
			{
				\Clfunc_Common::LogOut($e,__CLASS__);
				$res = array('err'=>-3,'res'=>'','msg'=>$e->getMessage());
				$this->response($res);
				return;
			}
			$res = array('err'=>0,'res'=>1,'msg'=>__('コメントの更新が完了しました。'));
		}
		$this->response($res);
		return;
	}

	public function post_QueryTextUpdate()
	{
		global $gaQuickTitle;

		$res = array('err'=>-3,'res'=>'','msg'=>__('アンケート情報が正しく送信されていません。'));
		$par = Input::post();
		if ($par)
		{
			$result = Model_Quest::getQuestBaseFromID($par['qb']);
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('指定のアンケート情報が見つかりません。'));
				$this->response($res);
				return;
			}
			$aQuest = $result->current();
			$sTitle = $aQuest['qbTitle'];

			$aQQUpdate = array('qqText' => $par['text']);
			$aQBUpdate = null;

			if ($sTitle == __($gaQuickTitle[$aQuest['qbQuickMode']]))
			{
				$aQBUpdate['qbTitle'] = mb_strimwidth($par['text'], 0, 20, '…').' - '.$sTitle;
				$sTitle = $aQBUpdate['qbTitle'];
			}

			try
			{
				$result = Model_Quest::updateQuestQuery($aQQUpdate,array(array('qbID','=',$aQuest['qbID']),array('qqNO','=',1)));
				if (!is_null($aQBUpdate))
				{
					$result = Model_Quest::updateQuest($aQBUpdate,array(array('qbID','=',$aQuest['qbID'])));
				}
			}
			catch (Exception $e)
			{
				\Clfunc_Common::LogOut($e,__CLASS__);
				$res = array('err'=>-3,'res'=>'','msg'=>$e->getMessage());
				$this->response($res);
				return;
			}
			$res = array('err'=>0,'res'=>array('text'=>$par['text'], 'title'=>'[Q]'.$sTitle),'msg'=>__('設問文の更新が完了しました。'));
		}
		$this->response($res);
		return;
	}

	public function post_BentMember()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>__('アンケート情報が正しく送信されていません。'));
		$par = Input::post();
		if ($par)
		{
			$result = Model_Quest::getQuestBaseFromID($par['qb']);
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('指定のアンケート情報が見つかりません。'));
				$this->response($res);
				return;
			}
			$aQuest = $result->current();

			$aPWhere = array(
				array('qp.qbID','=',$par['qb']),
			);
			$aWhere = array(
				array('qa.qbID','=',$par['qb']),
				array('qa.qqNO','=',$par['qq']),
			);
			if ($par['ch'] > 0)
			{
				$aWhere[] = array('qa.qaChoice'.$par['ch'],'=',1);
			}
			else
			{
				for ($i = 1; $i <= 50; $i++)
				{
					$aWhere[] = array('qa.qaChoice'.$i,'=',0);
				}
			}
			if ($par['mode'] == 'STUDENT')
			{
				$aPWhere[] = array('qp.stID','LIKE','s%');
				$aWhere[] = array('qa.stID','LIKE','s%');
			}
			elseif ($par['mode'] == 'GUEST')
			{
				$aPWhere[] = array('qp.stID','LIKE','g%');
				$aWhere[] = array('qa.stID','LIKE','g%');
			}
			elseif ($par['mode'] == 'TEACH')
			{
				$aPWhere[] = array('qp.stID','LIKE','t%');
				$aWhere[] = array('qa.stID','LIKE','t%');
			}

			$result = Model_Quest::getQuestPut($aPWhere,null,array('qp.qpDate'=>'desc'));
			if (!count($result))
			{
				$res = array('err'=>0,'res'=>'');
				$this->response($res);
				return;
			}
			$aPutMember = $result->as_array('stID');

			$result = Model_Quest::getQuestAns($aWhere,null,array('qa.qaDate'=>'desc'));
			if (!count($result))
			{
				$res = array('err'=>0,'res'=>'');
				$this->response($res);
				return;
			}

			$sMember = '';
			$sSep = '';
			$aMember = $result->as_array('stID');
			$iCnt = 0;
			foreach ($aMember as $sStID => $aA)
			{
				$aM = $aPutMember[$sStID];
				if (preg_match('/^s.+/', $sStID))
				{
					$sMember .= $sSep.$aM['qpstName'];
					$sSep = '、';
				}
				elseif (preg_match('/^t.+/', $sStID))
				{
					$sMember .= $sSep.'<span class="font-red">'.(($aM['qpstName'])? $aM['qpstName']:$aM['ttName']).'</span>';
					$sSep = '、';
				}
				else
				{
					if ($aQuest['qbOpen'] == 2)
					{
						$sMember .= $sSep.'<span class="font-green">'.(($aM['qpstName'])? $aM['qpstName']:(($aM['gtName'])? $aM['gtName']:'[GUEST]')).'</span>';
						$sSep = '、';
					}
					else
					{
						$iCnt++;
					}
				}
			}
			if ($iCnt > 0)
			{
				$sMember .= $sSep.'<span class="font-green">ゲスト'.$iCnt.'名</span>';
			}
			$res = array('err'=>0,'res'=>$sMember);
		}
		$this->response($res);
		return;
	}

	public function post_SwitchOpen()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>__('アンケート情報が正しく送信されていません。'));
		$par = Input::post();
		if ($par)
		{
			$result = Model_Quest::getQuestBaseFromID($par['qb'],array(array('qb.ctID','=',$par['ct'])));
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('指定のアンケート情報が見つかりません。'));
				$this->response($res);
				return;
			}
			$aQuest = $result->current();

			switch($par['m'])
			{
				case 'close':
					$iOpen = 0;
				break;
				case 'anonymous':
					$iOpen = 1;
				break;
				case 'signature':
					$iOpen = 2;
				break;
				default:
					$res = array('err'=>-1,'res'=>'','msg'=>__('ゲスト回答の指定情報が正しくありません。'));
					$this->response($res);
					return;
				break;
			}

			$aUpdate = array(
				'qbOpen'=>$iOpen,
			);
			$result = Model_Quest::updateQuest($aUpdate,array(array('qbID','=',$aQuest['qbID'])));
			$res = array('err'=>0,'res'=>'','msg'=>'');
		}
		$this->response($res);
		return;
	}

	public function post_ArchiveDownloadBtn()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>__('情報が正しく送信されていません。'));
		$par = Input::post();
		if ($par)
		{
			$result = Model_Class::getClassArchive($par['ct'],$par['type']);
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('指定の講義が見つかりません。'));
				$this->response($res);
				return;
			}
			$aCArchive = $result->current();

			$result = null;
			switch ($aCArchive['caProgress'])
			{
				case 0:
					$result['status'] = 0;
					$result['href'] = \Uri::create('getfile/s3file/:fid/:mode',array('fid'=>$aCArchive['fID'],'mode'=>'e'));
					$result['text'] = __('アーカイブファイルのダウンロード').' ('.ClFunc_Tz::tz('Y/m/d H:i',$this->tz,$aCArchive['fDate']).' '.\Clfunc_Common::FilesizeFormat($aCArchive['fSize'],1).')';
					break;
				case 1:
					$result['status'] = 1;
					break;
				case 2:
					$result['status'] = 2;
					$result['text'] = __('アーカイブファイルの作成失敗');
					break;
			}
			$res = array('err'=>0, 'res'=>$result, 'msg'=>'');
		}
		$this->response($res);
		return;
	}
public function get_Question()
 {
  $aQSAL = null;
  $this->format = 'json';   
  while (is_null($aQSAL))
  {
   $result = Model_Class::getClassArchive('c005096089','QuestStuAnsList');
   if (!count($result))
   {
    try
    {
     $aInsert = array(
      'ctID' => 'c005096089',
      'caType' => 'QuestStuAnsList',
      'caProgress' => 0,
      'caDate' => date('YmdHis'),
     );
     $result = Model_Class::insertClassArchive($aInsert);
    }
    catch (Exception $e)
    {
     \Clfunc_Common::LogOut($e,__CLASS__);
     Session::set('SES_T_ERROR_MSG',$e->getMessage());
     Response::redirect($this->eRedirect);
    }
   }
   else
   {
    $aQSAL = $result->current();
   }
  }

  $aQuest = null;
  $result = Model_Quest::getQuestBaseFromClass('c005096089',null,null,array('qb.qbSort'=>'desc'));
  if (count($result))
  {
   $aQuest = $result->as_array();
  }
  $res = array('data'=>$aQuest);
  $this->response($res);
  return;
 }
}
