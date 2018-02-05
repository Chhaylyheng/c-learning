<?php
class Controller_T_Ajax_Test extends Controller_T_Ajax
{
	public function post_Sort()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>__('小テスト情報が正しく送信されていません。'));
		$par = Input::post();
		if ($par)
		{
			$result = Model_Test::getTestBaseFromID($par['tb']);
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('指定の小テスト情報が見つかりません。'));
				$this->response($res);
				return;
			}
			$aTest = $result->current();
			$result = Model_Test::getTestBaseFromClass($par['ct'],null,null,array('tb.tbSort'=>'desc'));
			if (!count($result))
			{
				$res = array('err'=>-1,'res'=>'','msg'=>__('並び順を変更する小テストの情報が見つかりません。'));
				$this->response($res);
				return;
			}
			$iMax = count($result);
			if (($aTest['tbSort'] == $iMax && $par['m'] == 'up') || ($aTest['tbSort'] == 1 && $par['m'] == 'down'))
			{
				$res = array('err'=>-1,'res'=>'','msg'=>__('一番上か一番下の小テストのため、並び順を変更することはできません。'));
				$this->response($res);
				return;
			}
			$result = Model_Test::sortTest($aTest,$par['m']);
			$res = array('err'=>0,'res'=>array('m'=>$par['m']));
		}
		$this->response($res);
		return;
	}

	public function post_QuerySort()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>__('小テスト情報が正しく送信されていません。'));
		$par = Input::post();
		if ($par)
		{
			$result = Model_Test::getTestQuery(array(array('tbID','=',$par['tb']),array('tqNO','=',$par['qn'])));
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('指定の小テスト問題が見つかりません。'));
				$this->response($res);
				return;
			}
			$aQuery = $result->current();

			$result = Model_Test::getTestQuery(array(array('tbID','=',$par['tb'])));
			if (!count($result))
			{
				$res = array('err'=>-1,'res'=>'','msg'=>__('並び順を変更する小テストの問題が見つかりません。'));
				$this->response($res);
				return;
			}
			$iMax = count($result);
			if (($aQuery['tqSort'] == $iMax && $par['m'] == 'down') || ($aQuery['tqSort'] == 1 && $par['m'] == 'up'))
			{
				$res = array('err'=>-1,'res'=>'','msg'=>__('一番上か一番下の問題のため、並び順を変更することはできません。'));
				$this->response($res);
				return;
			}
			$result = Model_Test::sortTestQuery($aQuery,$par['m']);
			$res = array('err'=>0,'res'=>array('m'=>$par['m'],'qs'=>$aQuery['tqSort']));
		}
		$this->response($res);
		return;
	}

	public function post_ScorePublic()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>__('小テスト情報が正しく送信されていません。'));
		$par = Input::post();
		if ($par)
		{
			$result = Model_Test::getTestBaseFromID($par['tb'],array(array('tb.ctID','=',$par['ct'])));
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('指定の小テスト情報が見つかりません。'));
				$this->response($res);
				return;
			}
			$aTest = $result->current();

			switch ($par['m'])
			{
				case 'public3':
					$iScore = 3;
					$sText = __('点数・解説');
					$sClass = 'font-blue';
				break;
				case 'public2':
					$iScore = 2;
					$sText = __('解説');
					$sClass = 'font-green';
				break;
				case 'public1':
					$iScore = 1;
					$sText = __('点数');
					$sClass = 'font-green';
				break;
				case 'private':
					$iScore = 0;
					$sText = __('非公開');
					$sClass = 'font-default';
				break;
				default:
					$res = array('err'=>-1,'res'=>'','msg'=>__('公開情報が正しく送信されていません。'));
					$this->response($res);
					return;
				break;
			}

			$aUpdate = array(
				'tbScorePublic'=>$iScore,
			);
			$result = Model_Test::updateTest($aUpdate,array(array('tbID','=',$aTest['tbID'])));
			$res = array('err'=>0,'res'=>array('class'=>$sClass,'text'=>$sText),'msg'=>__('点数と解説の公開を変更しました。'));
		}
		$this->response($res);
		return;
	}


	public function post_Public()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>__('小テスト情報が正しく送信されていません。'));
		$par = Input::post();
		if ($par)
		{
			$result = Model_Test::getTestBaseFromID($par['tb'],array(array('tb.ctID','=',$par['ct'])));
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('指定の小テスト情報が見つかりません。'));
				$this->response($res);
				return;
			}
			$aTest = $result->current();
			if ($aTest['tbNum'] == 0)
			{
				$res = array('err'=>-1,'res'=>'','msg'=>__('問題がないため公開情報を変更することはできません。'));
				$this->response($res);
				return;
			}
			$aUpdate = array();
			$sDate = date('YmdHis');
			$sTimer = null;
			if ($par['m'] == 'public')
			{
				$iBent = 1;
				$sText = __('公開中');
				$sClass = 'font-blue';
				$aUpdate['tbAutoPublicDate'] = CL_DATETIME_DEFAULT;
				$aUpdate['tbPublicDate'] = $sDate;
				if ($aTest['tbAutoCloseDate'] != CL_DATETIME_DEFAULT)
				{
					$sTimer = '～ '.ClFunc_Tz::tz('n/j H:i',$this->tz,$aTest['tbAutoCloseDate']);
				}
			}
			elseif ($par['m'] == 'close')
			{
				$iBent = 2;
				$sText = __('締切');
				$aUpdate['tbAutoCloseDate'] = CL_DATETIME_DEFAULT;
				$aUpdate['tbCloseDate'] = $sDate;
				$sClass = 'font-red';
			}
			elseif ($par['m'] == 'private')
			{
				$iBent = 0;
				$sText = __('非公開');
				$sClass = 'font-default';
				$aUpdate['tbPublicDate'] = CL_DATETIME_DEFAULT;
				$aUpdate['tbCloseDate'] = CL_DATETIME_DEFAULT;
				if ($aTest['tbAutoPublicDate'] != CL_DATETIME_DEFAULT)
				{
					$sTimer = ClFunc_Tz::tz('n/j H:i',$this->tz,$aTest['tbAutoPublicDate']).' ～';
				}
			}
			else
			{
				$res = array('err'=>-1,'res'=>'','msg'=>__('公開情報が正しく送信されていません。'));
				$this->response($res);
				return;
			}

			$aUpdate['tbPublic'] = $iBent;
			$result = Model_Test::updateTest($aUpdate,array(array('tbID','=',$aTest['tbID'])));
			$res = array('err'=>0,'res'=>array('class'=>$sClass,'text'=>$sText,'timer'=>$sTimer),'msg'=>__('公開情報を変更しました。'));
		}
		$this->response($res);
		return;
	}

	public function post_PutReset()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>__('小テスト情報が正しく送信されていません。'));
		$par = Input::post();
		if ($par)
		{
			$result = Model_Test::getTestBaseFromID($par['tb'],array(array('tb.ctID','=',$par['ct'])));
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('指定の小テスト情報が見つかりません。'));
				$this->response($res);
				return;
			}
			$aTest = $result->current();

			$result = Model_Test::deleteTestPut($aTest['tbID']);
			$res = array('err'=>0,'res'=>'','msg'=>__('提出状況をリセットしました。'));
		}
		$this->response($res);
		return;
	}

	public function post_BaseImageDelete()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>__('小テスト情報が正しく送信されていません。'));
		$par = Input::post();
		if ($par)
		{
			list($fn,$ex) = explode('.', $par['fn']);
			$result = Model_Test::getTestBaseFromID($par['tb']);
			if (count($result))
			{
				$aTest = $result->current();

				$sImgPath = CL_UPPATH.DS.$aTest['tbID'].DS.'base'.DS;
				$aUpdate = array('tbExplainImage'=>'');
				$result = Model_Test::updateTest($aUpdate,array(array('tbID','=',$aTest['tbID'])));
				if (file_exists($sImgPath.$par['fn']))
				{
					File::delete($sImgPath.$par['fn']);
					File::delete($sImgPath.CL_Q_SMALL_PREFIX.$par['fn']);
				}
			}
			else
			{
				$sTempPath = CL_UPPATH.DS.'temp'.DS.'test'.DS.$par['tb'].DS;
				if (file_exists($sTempPath.$par['fn']))
				{
					File::delete($sTempPath.$par['fn']);
					File::delete($sTempPath.CL_Q_SMALL_PREFIX.$par['fn']);
				}
			}
			$res = array('err'=>0,'res'=>array('del'=>$fn), 'msg'=>'');
		}
		$this->response($res);
		return;
	}

	public function post_QueryLoad()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>__('小テスト情報が正しく送信されていません。'));
		$par = Input::post();
		if ($par)
		{
			$result = Model_Test::getTestQuery(array(array('tbID','=',$par['tb']),array('tqNO','=',$par['qn'])));
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('指定の小テスト設問が見つかりません。'));
				$this->response($res);
				return;
			}
			$aQuery = $result->current();

			$sTempPath = CL_UPPATH.DS.$aQuery['tbID'].DS.$aQuery['tqSort'].'_tmp';
			$sQueryPath = CL_UPPATH.DS.$aQuery['tbID'].DS.$aQuery['tqNO'];
			if (file_exists($sTempPath))
			{
				system('rm -rf '.$sTempPath);
			}
			if (file_exists($sQueryPath))
			{
				system('cp -Rfp '.$sQueryPath.' '.$sTempPath);
			}

			$res = array('err'=>0,'res'=>$aQuery,'msg'=>'', 'path'=>DS.CL_UPDIR.DS.$aQuery['tbID'].DS.$aQuery['tqSort'].'_tmp'.DS);
		}
		$this->response($res);
		return;
	}

	public function post_QueryImageDelete()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>__('小テスト情報が正しく送信されていません。'));
		$par = Input::post();
		if ($par)
		{
			$sTempPath = CL_UPPATH.DS.$par['tb'].DS.$par['qs'].'_tmp'.DS;
			list($fn,$ex) = explode('.', $par['fn']);

			$result = Model_Test::getTestQuery(array(array('tbID','=',$par['tb']),array('tqSort','=',$par['qs'])));
			if (count($result))
			{
				$aQuery = $result->current();

				$sImgPath = CL_UPPATH.DS.$aQuery['tbID'].DS.$aQuery['tqNO'].DS;

				if ($fn == 'base')
				{
					$aUpdate = array('tqImage'=>'');
				}
				else if ($fn == 'explain')
				{
					$aUpdate = array('tqExplainImage'=>'');
				}
				else
				{
					$aUpdate = array('tqChoiceImg'.$fn=>'');
				}
				$result = Model_Test::updateTestQuery($aUpdate,array(array('tbID','=',$aQuery['tbID']),array('tqNO','=',$aQuery['tqNO'])));
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

	public function post_Bent()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>__('小テスト情報が正しく送信されていません。'));
		$par = Input::post();
		if ($par)
		{
			$result = Model_Test::getTestBaseFromID($par['tb']);
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('指定の小テストが見つかりません。'));
				$this->response($res);
				return;
			}
			$aTest = $result->current();

			$result = Model_Test::getTestQuery(array(array('tbID','=',$par['tb'])),null,array('tqSort'=>'asc'));
			if (!count($result))
			{
				Session::set('SES_T_ERROR_MSG',__('指定の小テストには問題がありません。'));
				Response::redirect('/t/quest');
			}
			$aRes = $result->as_array();
			foreach ($aRes as $aR)
			{
				$aQuery['tq'.$aR['tqNO']] = $aR;
			}

			try
			{
				$result = Model_Test::setTestBent($aTest,0);
			}
			catch (Exception $e)
			{
				\Clfunc_Common::LogOut($e,__CLASS__);
				$res = array('err'=>-3,'res'=>'','msg'=>$e->getMessage());
				$this->response($res);
				return;
			}
			$aBent = null;
			$result = Model_Test::getTestBent(array(array('tbID','=',$par['tb']),array('tqNO','=',1)));
			if (count($result))
			{
				$aRes = $result->as_array();
				foreach ($aRes as $aR)
				{
					$per = ($aR['tbAll'])? round(($aR['tbNum']/$aR['tbAll'])*100,1):0;
					$aBent[$aR['tbNO']] = array(
						'num' => (int)$aR['tbNum'],
						'per' => $per,
					);
				}
			}

			$aComment = null;
			if ($par['com'])
			{
				$selResult = Model_Test::getTestAns(array(array('ta.tbID','=',$par['tb']),array('tq.tqSort','=',1)),null,array('ta.taDate'=>'ASC'));
				$txtResult = Model_Test::getTestAns(array(array('ta.tbID','=',$par['tb']),array('tq.tqSort','=',2)),null,array('ta.taDate'=>'ASC'));
				if (count($selResult) && count($txtResult))
				{
					$aSel = $selResult->as_array('stID');
					$aTxt = $txtResult->as_array('stID');
					foreach ($aTxt as $sStID => $aT)
					{
						$aS = $aSel[$sStID];
						$sChoice = null;
						$iNO = null;
						for ($i = 1; $i <= $aQuery['tq1']['tqChoiceNum']; $i++)
						{
							if ($aS['taChoice'.$i] == 1)
							{
								$sChoice = $aQuery['tq1']['tqChoice'.$i];
								$iNO = $i;
								break;
							}
						}
						$aComment[$sStID] = array(
							'text'  => nl2br($aT['taText']),
							'cName' => $sChoice,
							'cNO'   => $iNO,
							'cPick' => $aT['taPick'],
						);
					}
				}
			}
			$data = array('bent'=>$aBent,'comment'=>$aComment,'quest'=>array('qpNum'=>$aTest['qpNum'],'scNum'=>$aTest['scNum']));
			$res = array('err'=>0,'res'=>$data,'msg'=>'');
		}
		$this->response($res);
		return;
	}

	public function post_BentComment()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>__('小テスト情報が正しく送信されていません。'));
		$par = Input::post();
		if ($par)
		{
			$result = Model_Test::getTestBaseFromID($par['tb']);
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('指定の小テストが見つかりません。'));
				$this->response($res);
				return;
			}
			$aTest = $result->current();

			$aComment = null;
			$txtResult = Model_Test::getTestAns(array(array('ta.tbID','=',$par['tb']),array('tq.tqSort','=',1)),null,array('ta.taDate'=>'ASC'));
			if (count($txtResult))
			{
				$aTxt = $txtResult->as_array('stID');
				foreach ($aTxt as $sStID => $aT)
				{
					$aComment[$sStID] = array(
						'text'  => nl2br($aT['taText']),
					);
				}
			}
			$data = array('comment'=>$aComment,'quest'=>array('qpNum'=>$aTest['qpNum'],'scNum'=>$aTest['scNum']));
			$res = array('err'=>0,'res'=>$data,'msg'=>'');
		}
		$this->response($res);
		return;
	}

	public function post_TeachComment()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>__('小テスト情報が正しく送信されていません。'));
		$par = Input::post();
		if ($par)
		{
			$result = Model_Test::getTestBaseFromID($par['tb']);
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('指定の小テストが見つかりません。'));
				$this->response($res);
				return;
			}
			$aTest = $result->current();

			if ($par['st'] == 'ALL')
			{
				$aUpdate = array(
					'tbComment'=>$par['com'],
				);
				$result = Model_Test::updateTest($aUpdate,array(array('tbID','=',$aTest['tbID'])));
			}
			else
			{
				$result = Model_Test::getTestPut(array(array('tp.tbID','=',$aTest['tbID']),array('tp.stID','=',$par['st'])));
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
				$result = Model_Test::updateTestPut($aUpdate,array(array('tbID','=',$aTest['tbID']),array('stID','=',$aPut['stID'])));
			}
			$res = array('err'=>0,'res'=>1,'msg'=>__('コメントの更新が完了しました。'));
		}
		$this->response($res);
		return;
	}


	public function post_LimitTime()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>__('小テスト情報が正しく送信されていません。'));
		$par = Input::post();

		if ($par)
		{
			$result = Model_Test::getTestBaseFromID($par['tb']);
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('指定の小テストが見つかりません。'));
				$this->response($res);
				return;
			}
			$aTest = $result->current();

			$aTimer = Session::get('SES_S_TEST_TIMER_'.$par['tb'],false);
			$aTimer = ($aTimer)? unserialize($aTimer):null;
			if (!isset($aTimer[$par['tb']]))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('小テストの制限時間が取得できません。'));
				$this->response($res);
				return;
			}

			$res = array('err'=>0, 'res'=>array('start'=>($aTimer[$par['tb']]*1000), 'limit'=>($aTest['tbLimitTime']*60*1000), 'server'=>(time()*1000)));
		}
		$this->response($res);
		return;
	}

	public function post_BentMember()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>__('小テスト情報が正しく送信されていません。'));
		$par = Input::post();
		if ($par)
		{
			$result = Model_Test::getTestBaseFromID($par['tb']);
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('指定の小テスト情報が見つかりません。'));
				$this->response($res);
				return;
			}
			$aQuest = $result->current();

			if ($par['ch'] != 'text')
			{
				$aWhere = array(
					array('ta.tbID','=',$par['tb']),
					array('ta.tqNO','=',$par['tq']),
				);
				if ($par['ch'] > 0)
				{
					$aWhere[] = array('ta.taChoice'.$par['ch'],'=',1);
				}
				else
				{
					for ($i = 1; $i <= 50; $i++)
					{
						$aWhere[] = array('ta.taChoice'.$i,'=',0);
					}
				}
			}
			else
			{
				$aWhere = array(
					array('ta.tbID','=',$par['tb']),
					array('ta.tqNO','=',$par['tq']),
					array('ta.taText','=',$par['txt']),
				);
			}

			$result = Model_Test::getTestAns($aWhere,null,array('ta.taDate'=>'desc'));
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
				$sMember .= $sSep.$aA['tpstName'];
				$sSep = '、';
			}
			$res = array('err'=>0,'res'=>$sMember);
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

	public function post_drill()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>__('情報が正しく送信されていません。'));
		$par = Input::post();
		if ($par)
		{
			$result = Model_Drill::getDrillCategoryFromID($par['dc']);
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('指定されたカテゴリが見つかりません。'));
				$this->response($res);
				return;
			}

			$aDrill = array();
			$result = Model_Drill::getDrill(array(array('dcID','=',$par['dc'])),null,array('dbSort'=>'desc'));
			if (count($result))
			{
				$aDrill = $result->as_array();
			}
			$res = array('err'=>0,'res'=>$aDrill,'msg'=>'');
		}
		$this->response($res);
		return;
	}

}
