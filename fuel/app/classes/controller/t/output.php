<?php
class Controller_T_Output extends Controller_Restbase
{
	public $aClass = null;
	public $tz = null;

	public function before()
	{
		parent::before();

		if (Session::get('CL_AL_LOGIN', false))
		{
			$this->bT = false;
			$this->sDir = 'a';
			$sHash = Cookie::get('CL_AL_HASH',false);
			if (!$sHash)
			{
				Response::redirect($this->sDir.'/login/index/1');
			}
			$aLogin = unserialize(Crypt::decode($sHash));

			$result = Model_Assistant::getAssistantFromHash($aLogin['hash']);
			if (!count($result))
			{
				Response::redirect($this->sDir.'/login/index/1');
			}
			$this->aAssistant = $result->current();

			$result = Model_Assistant::getAssistantPosition(array(array('ap.atID','=',$this->aAssistant['atID']),array('ct.ctStatus','=',1)),null,array('ap.apSort'=>'desc'));
			if (count($result))
			{
				$this->aActClass = $result->as_array();
			}

			$result = Model_Assistant::getAssistantPosition(array(array('ap.atID','=',$this->aAssistant['atID'])));
			$this->iClassNum = count($result);

			$this->sAwsSavePath = 'assistant'.DS.$this->aAssistant['atID'];
			$this->sTempFilePath = CL_UPPATH.DS.'temp';

			$result = Model_Teacher::getTeacherFromID($this->aAssistant['ttID']);
			if (!count($result))
			{
				Response::redirect($this->sDir.'/login/index/1');
			}
			$this->aTeacher = $result->current();
			$this->aTeacher['ttTimeZone'] = $this->aAssistant['atTimeZone'];
		}
		else
		{
			$sHash = Cookie::get('CL_TL_HASH',false);
			if (!$sHash)
			{
				Response::redirect('t/login/index/1');
			}
			$aLogin = unserialize(Crypt::decode($sHash));

			$result = Model_Teacher::getTeacherFromHash($aLogin['hash']);
			if (!count($result))
			{
				Response::redirect('t/login/index/1');
			}
			$this->aTeacher = $result->current();
		}

		$sCtID = Cookie::get('CL_T_CLASS_ID',false);
		if (!$sCtID)
		{
			Session::set('SES_T_ERROR_MSG',__('ログイン情報が確認できませんでした。'));
			Response::redirect($this->eRedirect);
		}
		$result = Model_Class::getClassFromID($sCtID);
		if (!count($result)) {
		}
		$this->aClass = $result->current();

		$this->tz = $this->aTeacher['ttTimeZone'];
	}

	public function action_studentlist()
	{
		$res = array(
			array(
				__('ログインID'),
				__('パスワード'),
				__('氏名'),
				__('性別'),
				__('学籍番号'),
				__('学部'),
				__('学科'),
				__('学年'),
				__('クラス'),
				__('コース'),
				__('メールアドレス'),
			)
		);

		$result = Model_Student::getStudentFromClass($this->aClass['ctID'],null,null,array('st.stNO'=>'asc','st.stName'=>'acs','st.stLogin'=>'acs'));
		if (count($result))
		{
			$aRes = $result->as_array();
			foreach ($aRes as $aR)
			{
				$res[] = array(
					$aR['stLogin'],
					(($aR['stFirst'])? $aR['stFirst']:__('変更済み')),
					$aR['stName'],
					$this->aSex[$aR['stSex']],
					$aR['stNO'],
					$aR['stDept'],
					$aR['stSubject'],
					($aR['stYear'])? $aR['stYear']:'',
					$aR['stClass'],
					$aR['stCourse'],
					(($aR['stMail'])? __('登録済み'):__('未登録')),
				);
			}
		}
		mb_convert_variables('sjis-win','UTF-8',$res);

		$this->response($res);
		return;
	}


	public function action_attendtable()
	{
		$res = array(
			array(
				__('学籍番号'),
				__('クラス'),
				__('氏名'),
				__('出席数'),
			)
		);

		$aAttendMaster = null;
		$result = Model_Attend::getAttendMasterFromClass($this->aClass['ctID']);
		if (count($result))
		{
			$aAttendMaster = $result->as_array();
		}

		$aAttendList = null;
		$result = Model_Attend::getAttendCalendarFromClass($this->aClass['ctID'],array(array('ac.acAStart','=',CL_DATETIME_DEFAULT),array('ac.abDate','<=',date('Y-m-d'))),null,'desc');
		if (count($result))
		{
			$aAttendList = $result->as_array();
		}
		$aStudent = null;
		$aAttend = null;
		$result = Model_Student::getStudentFromClass($this->aClass['ctID'],null,null,array('st.stNO'=>'asc'));
		if (count($result))
		{
			$aRes = $result->as_array();
			foreach ($aRes as $aR)
			{
				$aStudent[$aR['stID']] = $aR;
			}
		}
		$result = Model_Attend::getAttendBookFromClass($this->aClass['ctID'],array(array('abDate','<=', date('Y-m-d'))),null,'desc');
		if (count($result))
		{
			$aRes = $result->as_array();
			foreach ($aRes as $aR)
			{
				if (isset($aStudent[$aR['stID']]))
				{
					$aStudent[$aR['stID']]["attend"][$aR["abDate"]][$aR["acNO"]] = $aR;
				}
			}
		}

		if (!is_null($aAttendList))
		{
			foreach ($aAttendList as $aA)
			{
				$sDate = ($aA['acStart'] != CL_DATETIME_DEFAULT)? ClFunc_Tz::tz('Y/n/j',$this->tz,$aA['acStart']):((($aA['acAStart'] != CL_DATETIME_DEFAULT)? ClFunc_Tz::tz('H:i',$this->tz,$aA['acAStart']):date('Y/n/j',strtotime($aA['abDate']))));
				$sTime = ($aA['acStart'] != CL_DATETIME_DEFAULT)? ClFunc_Tz::tz('H:i',$this->tz,$aA['acStart']):((($aA['acAStart'] != CL_DATETIME_DEFAULT)? ClFunc_Tz::tz('H:i',$this->tz,$aA['acAStart']):''));
				$res[0][] = $sDate.(($sTime)? ' '.$sTime.'～':'');
				$res[0][] = __('出席時刻');
			}
		}

		if (!is_null($aStudent))
		{
			$i = 1;
			foreach ($aStudent as $aS)
			{
				$res[$i] = array($aS['stNO'],$aS['stClass'],$aS['stName'],$aS['abNum']);
				if (!is_null($aAttendList))
				{
					foreach ($aAttendList as $aA)
					{
						if (!isset($aS['attend'][$aA['abDate']][$aA['acNO']]))
						{
							$aSA = array(
								'amAbsence' => 1,
								'amTime' => 0,
								'amName' => $aAttendMaster[0]['amName'],
								'abAttendDate' => CL_DATETIME_DEFAULT,
							);
						}
						else
						{
							$aSA = $aS['attend'][$aA['abDate']][$aA['acNO']];
						}
						$res[$i][] = $aSA['amName'];
						$res[$i][] = ($aSA['abAttendDate'] != CL_DATETIME_DEFAULT)? ClFunc_Tz::tz('H:i',$this->tz,$aSA['abAttendDate']):'';
					}
				}
				$i++;
			}
		}

		mb_convert_variables('sjis-win','UTF-8',$res);
		$this->response($res);
		return;
	}

	public function action_attendlist()
	{
		$res = array(
			array(
				__('日付'),
				__('開始時間'),
				__('終了時間'),
				__('回'),
				__('学籍番号'),
				__('クラス'),
				__('氏名'),
				__('出席内容'),
				__('出席時刻'),
				__('出席扱'),
			)
		);

		$aAttendMaster = null;
		$result = Model_Attend::getAttendMasterFromClass($this->aClass['ctID']);
		if (count($result))
		{
			$aAttendMaster = $result->as_array();
		}

		$aAttendList = null;
		$result = Model_Attend::getAttendCalendarFromClass($this->aClass['ctID'],array(array('ac.acAStart','=',CL_DATETIME_DEFAULT),array('ac.abDate','<=',date('Y-m-d'))),null,'desc');
		if (count($result))
		{
			$aAttendList = $result->as_array('acNO');
		}
		$aStudent = null;
		$aAttend = null;
		$result = Model_Student::getStudentFromClass($this->aClass['ctID'],null,null,array('st.stNO'=>'asc'));
		if (count($result))
		{
			$aStudent = $result->as_array('stID');
		}
		$result = Model_Attend::getAttendBookFromClass($this->aClass['ctID'],array(array('abDate','<=',date('Y-m-d'))),null,'asc');
		if (count($result))
		{
			$aRes = $result->as_array();
			$i = 1;
			$num = 0;
			$acNO = 0;
			foreach ($aRes as $aR)
			{
				if ($aR['acNO'] != $acNO)
				{
					$acNO = $aR['acNO'];
					$num++;
				}
				if (isset($aStudent[$aR['stID']]))
				{
					$aS = array(
						'no'   => $aStudent[$aR['stID']]['stNO'],
						'class' => $aStudent[$aR['stID']]['stClass'],
						'name' => $aStudent[$aR['stID']]['stName'],
					);
				}
				else
				{
					$aS = array(
						'no'   => $aR['abStNO'],
						'class' => '',
						'name' => $aR['abStName'],
					);
				}

				$aAD = array();
				if (isset($aAttendList[$aR['acNO']]))
				{
					$aAD = array(
						'date' => ($aAttendList[$aR['acNO']]['acStart'] != CL_DATETIME_DEFAULT)? ClFunc_Tz::tz('Y-m-d',$this->tz,$aAttendList[$aR['acNO']]['acStart']):$aR['abDate'],
						'start' => ($aAttendList[$aR['acNO']]['acStart'] != CL_DATETIME_DEFAULT)? ClFunc_Tz::tz('H:i',$this->tz,$aAttendList[$aR['acNO']]['acStart']):'',
						'end' => ($aAttendList[$aR['acNO']]['acEnd'] != CL_DATETIME_DEFAULT)? ClFunc_Tz::tz('H:i',$this->tz,$aAttendList[$aR['acNO']]['acEnd']):'',
					);
				}
				else
				{
					$aAD = array(
						'date' => $aR['abDate'],
						'start' => '',
						'end' => '',
					);
				}

				$res[$i] = array(
					$aAD['date'],
					$aAD['start'],
					$aAD['end'],
					$num,
					$aS['no'],
					$aS['class'],
					$aS['name'],
					$aR['amName'],
					($aR['abAttendDate'] != CL_DATETIME_DEFAULT)? ClFunc_Tz::tz('H:i',$this->tz,$aR['abAttendDate']):'',
					(int)!$aR['amAbsence'],
				);

				$i++;
			}
		}

		mb_convert_variables('sjis-win','UTF-8',$res);
		$this->response($res);
		return;
	}


	public function action_questresult($sQbID = null)
	{
		try
		{
			if (is_null($sQbID))
			{
				throw new Exception(__('アンケートが指定されていません。'));
			}
			$aQuest = null;
			$aQuery = null;
			$aStudent = null;

			$result = Model_Quest::getQuestBaseFromID($sQbID);
			if (!count($result))
			{
				throw new Exception(__('指定されたアンケートが見つかりません。'));
			}
			$aQuest = $result->current();

			$result = Model_Quest::getQuestQuery(array(array('qbID','=',$sQbID)),null,array('qqSort'=>'asc'));
			if (!count($result))
			{
				throw new Exception(__('指定されたアンケート設問が見つかりません。'));
			}
			$aQuery = $result->as_array('qqSort');

			$result = Model_Student::getStudentFromClass($this->aClass['ctID'],null,null,array('st.stNO'=>'asc'));
			if (count($result))
			{
				$aRes = $result->as_array('stID');
				foreach ($aRes as $sStID => $aS)
				{
					$aStudent[$sStID]['stu'] = $aS;
				}
			}
			$aGuest = null;
			$aTeach = null;
			$result = Model_Quest::getQuestPut(array(array('qp.qbID','=',$sQbID)));
			if (count($result))
			{
				$aRes = $result->as_array('stID');
				foreach ($aRes as $sStID => $aP)
				{
					if (isset($aStudent[$sStID]))
					{
						$aStudent[$sStID]['put'] = $aP;
					}
					if (preg_match('/^g.+/',$sStID))
					{
						$aGuest[$sStID]['put'] = $aP;
					}
					if (preg_match('/^t.+/',$sStID))
					{
						$aTeach[$sStID]['put'] = $aP;
					}
				}
			}

			$result = Model_Quest::getQuestAns(array(array('qa.qbID','=',$sQbID)),null,array('qq.qqSort'=>'asc'));
			if (count($result))
			{
				$aRes = $result->as_array();
				foreach ($aRes as $aA)
				{
					if (isset($aStudent[$aA['stID']]))
					{
						$aStudent[$aA['stID']]['ans'][$aA['qqSort']] = $aA;
					}
					if (preg_match('/^g.+/',$aA['stID']))
					{
						$aGuest[$aA['stID']]['ans'][$aA['qqSort']] = $aA;
					}
					if (preg_match('/^t.+/',$aA['stID']))
					{
						$aTeach[$aA['stID']]['ans'][$aA['qqSort']] = $aA;
					}
				}
			}

			$res = array(
				array(
					__('学籍番号'),
					__('学年'),
					__('クラス'),
					__('氏名'),
					__('提出日時'),
				)
			);
			$aQQs = array();
			foreach ($aQuery as $aQQ)
			{
				$res[0][] = $aQQ['qqText'];
				if ($aQQ['qqStyle'] < 2)
				{
					$res[0][] = '';
				}
				$aQQs[$aQQ['qqNO']] = $aQQ;
			}
			$res[0][] = __('コメント');

			if (!is_null($aStudent))
			{
				if ($aQuest['qbAnonymous'])
				{
					$aStudent = \Clfunc_Common::array_shuffle($aStudent);
				}
				foreach ($aStudent as $sStID => $aS)
				{
					$aRow = array(
						(isset($aS['stu']))? $aS['stu']['stNO']:$aS['put']['qpstNO'],
						(isset($aS['stu']))? $aS['stu']['stYear']:'',
						(isset($aS['stu']))? $aS['stu']['stClass']:$aS['put']['qpstClass'],
						(isset($aS['stu']))? $aS['stu']['stName']:$aS['put']['qpstName'],
						(isset($aS['put']))? ClFunc_Tz::tz('Y/m/d H:i',$this->tz,$aS['put']['qpDate']):__('未提出'),
					);
					if ($aQuest['qbAnonymous'])
					{
						$aRow[0] = '';
						$aRow[1] = '';
						$aRow[2] = '';
						$aRow[3] = ($aRow[3] != __('未提出'))? __('提出'):$aRow[3];
					}

					if (isset($aS['ans']))
					{
						foreach ($aS['ans'] as $aA)
						{
							$aQQ = $aQQs[$aA['qqNO']];
							$sAns = null;
							$sText = null;
							switch ($aQQ['qqStyle'])
							{
								case 0:
								case 1:
									$sSep = '';
									for ($i = 1; $i <= $aQQ['qqChoiceNum']; $i++)
									{
										if ($aA['qaChoice'.$i])
										{
											$sAns .= $sSep.$i;
											$sText .= $sSep.$aQQ['qqChoice'.$i];
											$sSep = '|';
										}
									}
								break;
								case 2:
									$sAns = $aA['qaText'];
								break;
							}
							$aRow[] = $sAns;
							if (!is_null($sText))
							{
								$aRow[] = $sText;
							}
						}
					}
					$aRow[] = (isset($aS['put']))? $aS['put']['qpComment']:'';
					$res[] = $aRow;
				}
			}

			if (!is_null($aGuest))
			{
				foreach ($aGuest as $sStID => $aS)
				{
					$aRow = array(
						'[GUEST]',
						'',
						'',
						(($aQuest['qbOpen'] == 2)? (($aS['put']['qpstName'])? $aS['put']['qpstName']:(($aS['put']['gtName'])? $aS['put']['gtName']:__('─無記名─'))):__('─匿名─')),
						ClFunc_Tz::tz('Y/m/d H:i',$this->tz,$aS['put']['qpDate']),
					);

					if (isset($aS['ans']))
					{
						foreach ($aS['ans'] as $aA)
						{
							$aQQ = $aQQs[$aA['qqNO']];
							$sAns = null;
							$sText = null;
							switch ($aQQ['qqStyle'])
							{
								case 0:
								case 1:
									$sSep = '';
									for ($i = 1; $i <= $aQQ['qqChoiceNum']; $i++)
									{
										if ($aA['qaChoice'.$i])
										{
											$sAns .= $sSep.$i;
											$sText .= $sSep.$aQQ['qqChoice'.$i];
											$sSep = '|';
										}
									}
								break;
								case 2:
									$sAns = $aA['qaText'];
								break;
							}
							$aRow[] = $sAns;
							if (!is_null($sText))
							{
								$aRow[] = $sText;
							}
						}
					}
					$aRow[] = (isset($aS['put']))? $aS['put']['qpComment']:'';
					$res[] = $aRow;
				}
			}

			if (!is_null($aTeach))
			{
				foreach ($aTeach as $sStID => $aS)
				{
					$aRow = array(
						'[TEACHER]',
						'',
						(($aS['put']['qpstClass'])? $aS['put']['qpstClass']:$aS['put']['ttDept'].$aS['put']['ttSubject']),
						(($aS['put']['qpstName'])? $aS['put']['qpstName']:$aS['put']['ttName']),
						ClFunc_Tz::tz('Y/m/d H:i',$this->tz,$aS['put']['qpDate']),
					);

					if (isset($aS['ans']))
					{
						foreach ($aS['ans'] as $aA)
						{
							$aQQ = $aQQs[$aA['qqNO']];
							$sAns = null;
							$sText = null;
							switch ($aQQ['qqStyle'])
							{
								case 0:
								case 1:
									$sSep = '';
									for ($i = 1; $i <= $aQQ['qqChoiceNum']; $i++)
									{
										if ($aA['qaChoice'.$i])
										{
											$sAns .= $sSep.$i;
											$sText .= $sSep.$aQQ['qqChoice'.$i];
											$sSep = '|';
										}
									}
								break;
								case 2:
									$sAns = $aA['qaText'];
								break;
							}
							$aRow[] = $sAns;
							if (!is_null($sText))
							{
								$aRow[] = $sText;
							}
						}
					}
					$aRow[] = (isset($aS['put']))? $aS['put']['qpComment']:'';
					$res[] = $aRow;
				}
			}

			mb_convert_variables('sjis-win','UTF-8',$res);
			$this->response($res);
			return;
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect('/t/quest');
		}
	}


	public function action_quest($sQbID = null)
	{
		try
		{
			if (is_null($sQbID))
			{
				throw new Exception(__('アンケートが指定されていません。'));
			}
			$aQuest = null;
			$aQuery = null;

			$result = Model_Quest::getQuestBaseFromID($sQbID);
			if (!count($result))
			{
				throw new Exception(__('指定されたアンケートが見つかりません。'));
			}
			$aQuest = $result->current();
			$result = Model_Quest::getQuestQuery(array(array('qbID','=',$sQbID)),null,array('qqSort'=>'asc'));
			if (!count($result))
			{
				throw new Exception(__('指定されたアンケート設問が見つかりません。'));
			}
			$aQuery = $result->as_array('qqSort');

			$res = array(
				array('アンケートタイトル', $aQuest['qbTitle']),
				array('公開予定日時(年/月/日 時:分)', ($aQuest['qbAutoPublicDate'] != CL_DATETIME_DEFAULT)? ClFunc_Tz::tz('Y/m/d H:i',$this->tz,$aQuest['qbAutoPublicDate']):''),
				array('締切予定日時(年/月/日 時:分)', ($aQuest['qbAutoCloseDate'] != CL_DATETIME_DEFAULT)? ClFunc_Tz::tz('Y/m/d H:i',$this->tz,$aQuest['qbAutoCloseDate']):''),
				array('選択肢の表示方法', $aQuest['qbQueryStyle']),
				array('選択肢の並び順', $aQuest['qbQuerySort']),
				array('答えなおし', $aQuest['qbReAnswer']),
				array('個人の回答内容の公開範囲', $aQuest['qbAnsPublic']),
				array('個人宛の先生コメントの公開範囲', $aQuest['qbComPublic']),
				array('ゲスト回答', $aQuest['qbOpen']),
			);
			if ($this->aTeacher['gtID'])
			{
				$res[] = array('匿名回答',$aQuest['qbAnonymous']);
			}

			$aStyle = array('radio','select','text');
			foreach ($aQuery as $aQ)
			{
				$qq = array(
					array(''),
					array('回答形式', $aStyle[$aQ['qqStyle']]),
					array('必須回答', $aQ['qqRequired']),
					array('設問文', $aQ['qqText']),
				);

				if ($aQ['qqStyle'] < 2)
				{
					for ($i = 1; $i <= 50; $i++)
					{
						if ($aQ['qqChoice'.$i] != '')
						{
							$qq[] = array('選択肢'.$i , $aQ['qqChoice'.$i]);
						}
					}
				}

				$res = array_merge($res, $qq);
			}

			mb_convert_variables('sjis-win','UTF-8',$res);
			$this->response($res);
			return;
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect('/t/quest');
		}
	}

	public function action_questputlist()
	{
		try
		{
			$aQuest = null;
			$aQtIDs = null;
			$result = Model_Quest::getQuestBaseFromClass($this->aClass['ctID'],null,null,array('qb.qbSort'=>'desc'));
			if (count($result))
			{
				foreach ($result as $aQ)
				{
					$aQuest[] = $aQ;
					$aQtIDs[] = $aQ['qbID'];
				}
			}

			$aStudent = null;
			$aStIDs = null;
			$result = Model_Student::getStudentFromClass($this->aClass['ctID'],null,null,array('st.stNO'=>'asc'));
			if (count($result))
			{
				$aRes = $result->as_array('stID');
				foreach ($aRes as $sStID => $aS)
				{
					$aStudent[$sStID]['stu'] = $aS;
					$aStIDs[] = $sStID;
				}
			}

			$aWhere = null;
			if (!is_null($aQtIDs))
			{
				$aWhere[] = array('qp.qbID','IN',$aQtIDs);
			}
			if (!is_null($aStIDs))
			{
				$aWhere[] = array('qp.stID','IN',$aStIDs);
			}

			$result = Model_Quest::getQuestPut($aWhere);
			if (count($result))
			{
				foreach ($result as $aP)
				{
					$sStID = $aP['stID'];
					$sQbID = $aP['qbID'];
					if (isset($aStudent[$sStID]))
					{
						$aStudent[$sStID]['put'][$sQbID] = $aP;
					}
				}
			}

			$res = array(
				array(
					'',
					'',
					'',
					'',
					'',
				),
				array(
					__('学籍番号'),
					__('氏名'),
					__('学年'),
					__('クラス'),
					__('提出'),
				)
			);
			if (!is_null($aQuest))
			{
				foreach ($aQuest as $aQ)
				{
					$sQuick = ($aQ['qbQuickMode'])? '[Q]':'';
					$res[0][] = $sQuick.$aQ['qbTitle'];
					$res[1][] = (int)$aQ['qpNum'];
				}
			}

			if (!is_null($aStudent))
			{
				foreach ($aStudent as $sStID => $aS)
				{
					$aM = array(0=>'',1=>'',2=>'',3=>'',4=>0);

					if (isset($aS['stu']))
					{
						$aM[0] = $aS['stu']['stNO'];
						$aM[1] = $aS['stu']['stName'];
						$aM[2] = $aS['stu']['stYear'];
						$aM[3] = $aS['stu']['stClass'];
					}
					$aP = null;
					if (isset($aS['put']))
					{
						$aM[4] = count($aS['put']);
						$aP = $aS['put'];
					}
					if (!is_null($aQuest))
					{
						foreach ($aQuest as $aQ)
						{
							if (isset($aP[$aQ['qbID']]))
							{
								if ($aQ['qbAnonymous'])
								{
									$aM[] = __('済');
								}
								else
								{
									$aM[] = ClFunc_Tz::tz('Y/m/d H:i',$this->tz,$aP[$aQ['qbID']]['qpDate']);
								}
							}
							else
							{
								$aM[] = '';
							}
						}
					}
					$res[] = $aM;
				}
			}

			mb_convert_variables('sjis-win','UTF-8',$res);
			$this->response($res);
			return;
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect('/t/quest');
		}
	}

	public function action_testresult($sTbID = null)
	{
		try
		{
			if (is_null($sTbID))
			{
				throw new Exception(__('小テストが指定されていません。'));
			}
			$aTest = null;
			$aQuery = null;
			$aStudent = null;

			$result = Model_Test::getTestBaseFromID($sTbID);
			if (!count($result))
			{
				throw new Exception(__('指定の小テスト情報が見つかりません。'));
			}
			$aTest = $result->current();
			$result = Model_Test::getTestQuery(array(array('tbID','=',$sTbID)),null,array('tqSort'=>'asc'));
			if (!count($result))
			{
				throw new Exception(__('指定の小テスト問題が見つかりません。'));
			}
			$aQuery = $result->as_array('tqSort');

			$result = Model_Student::getStudentFromClass($this->aClass['ctID'],null,null,array('st.stNO'=>'asc'));
			if (count($result))
			{
				$aRes = $result->as_array('stID');
				foreach ($aRes as $sStID => $aS)
				{
					$aStudent[$sStID]['stu'] = $aS;
				}
			}
			$result = Model_Test::getTestPut(array(array('tp.tbID','=',$sTbID)));
			if (count($result))
			{
				$aRes = $result->as_array('stID');
				foreach ($aRes as $sStID => $aP)
				{
					$aStudent[$sStID]['put'] = $aP;
				}
			}

			$result = Model_Test::getTestAns(array(array('ta.tbID','=',$sTbID)),null,array('tq.tqSort'=>'asc'));
			if (count($result))
			{
				$aRes = $result->as_array();
				foreach ($aRes as $aA)
				{
					$aStudent[$aA['stID']]['ans'][$aA['tqSort']] = $aA;
				}
			}

			$res = array(
				array(
					__('学籍番号'),
					__('学年'),
					__('クラス'),
					__('氏名'),
					__('提出日時'),
					__('総得点'),
					__('解答時間'),
					__('合格'),
				)
			);
			foreach ($aQuery as $aQQ)
			{
				$res[0][] = $aQQ['tqText'];
				if ($aQQ['tqStyle'] < 2)
				{
					$res[0][] = '';
				}
				$res[0][] = __('問題得点');
			}

			foreach ($aStudent as $sStID => $aS)
			{
				$aRow = array(
					(isset($aS['stu']))? $aS['stu']['stNO']:$aS['put']['tpstNO'],
					(isset($aS['stu']))? $aS['stu']['stYear']:'',
					(isset($aS['stu']))? $aS['stu']['stClass']:$aS['put']['tpstClass'],
					(isset($aS['stu']))? $aS['stu']['stName']:$aS['put']['tpstName'],
					(isset($aS['put']))? ClFunc_Tz::tz('Y/m/d H:i',$this->tz,$aS['put']['tpDate']):__('未提出'),
					(isset($aS['put']))? $aS['put']['tpScore']:'',
					(isset($aS['put']))? Clfunc_Common::Sec2Min($aS['put']['tpTime']):'',
					(isset($aS['put']))? (($aS['put']['tpQualify'])? __('合格'):__('不合格')):'',
				);

				if (isset($aS['ans']))
				{
					foreach ($aS['ans'] as $aA)
					{
						$sAns = null;
						$sText = null;
						switch ($aA['tqStyle'])
						{
							case 0:
							case 1:
								$sSep = '';
								for ($i = 1; $i <= $aA['tqChoiceNum']; $i++)
								{
									if ($aA['taChoice'.$i])
									{
										$sAns .= $sSep.$i;
										$sText .= $sSep.$aA['tqChoice'.$i];
										$sSep = '|';
									}
								}
							break;
							case 2:
								$sAns = $aA['taText'];
							break;
						}
						$aRow[] = $sAns;
						if (!is_null($sText))
						{
							$aRow[] = $sText;
						}
						$aRow[] = ($aA['taRight'])? $aQuery[$aA['tqSort']]['tqScore']:'0';
					}
				}
				$res[] = $aRow;
			}
			mb_convert_variables('sjis-win','UTF-8',$res);
			$this->response($res);
			return;
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect('/t/test');
		}
	}

	public function action_test($sTbID = null)
	{
		try
		{
			if (is_null($sTbID))
			{
				throw new Exception(__('小テストが指定されていません。'));
			}
			$aTest = null;
			$aQuery = null;

			$result = Model_Test::getTestBaseFromID($sTbID);
			if (!count($result))
			{
				throw new Exception(__('指定の小テスト情報が見つかりません。'));
			}
			$aTest = $result->current();
			$result = Model_Test::getTestQuery(array(array('tbID','=',$sTbID)),null,array('tqSort'=>'asc'));
			if (!count($result))
			{
				throw new Exception(__('指定の小テスト問題が見つかりません。'));
			}
			$aQuery = $result->as_array('tqSort');

			$res = array(
				array('小テストタイトル', $aTest['tbTitle']),
				array('合格点数', $aTest['tbQualifyScore']),
				array('制限時間', $aTest['tbLimitTime']),
				array('公開予定日時(年/月/日 時:分)', ($aTest['tbAutoPublicDate'] != CL_DATETIME_DEFAULT)? ClFunc_Tz::tz('Y/m/d H:i',$this->tz,$aTest['tbAutoPublicDate']):''),
				array('締切予定日時(年/月/日 時:分)', ($aTest['tbAutoCloseDate'] != CL_DATETIME_DEFAULT)? ClFunc_Tz::tz('Y/m/d H:i',$this->tz,$aTest['tbAutoCloseDate']):''),
				array('選択肢の表示方法', $aTest['tbQueryStyle']),
				array('選択肢の並び順', $aTest['tbQueryRand']),
				array('点数、解説の公開', $aTest['tbScorePublic']),
				array('小テストの全体的な解説', $aTest['tbExplain']),
			);

			$aStyle = array('radio','select','text');
			foreach ($aQuery as $aQ)
			{
				$tq = array(
					array(''),
					array('回答形式', $aStyle[$aQ['tqStyle']]),
					array('配点', $aQ['tqScore']),
					array('問題文', $aQ['tqText']),
					array('解説文', $aQ['tqExplain']),
				);

				if ($aQ['tqStyle'] < 2)
				{
					$tq[] = array('正解1', $aQ['tqRight1']);
					for ($i = 1; $i <= 50; $i++)
					{
						if ($aQ['tqChoice'.$i] != '')
						{
							$tq[] = array('選択肢'.$i , $aQ['tqChoice'.$i]);
						}
					}
				}
				else
				{
					for($i = 1; $i <= 5; $i++)
					{
						if ($aQ['tqRight'.$i] != '')
						{
							$tq[] = array('正解'.$i , $aQ['tqRight'.$i]);
						}
					}
				}
				$res = array_merge($res, $tq);
			}

			mb_convert_variables('sjis-win','UTF-8',$res);
			$this->response($res);
			return;
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect('/t/test');
		}
	}

	public function action_testputlist()
	{
		try
		{
			$aTest = null;
			$aTbIDs = null;
			$result = Model_Test::getTestBaseFromClass($this->aClass['ctID'],null,null,array('tb.tbSort'=>'desc'));
			if (count($result))
			{
				foreach ($result as $aQ)
				{
					$aTest[] = $aQ;
					$aTbIDs[] = $aQ['tbID'];
				}
			}

			$aStudent = null;
			$aStIDs = null;
			$result = Model_Student::getStudentFromClass($this->aClass['ctID'],null,null,array('st.stNO'=>'asc'));
			if (count($result))
			{
				$aRes = $result->as_array('stID');
				foreach ($aRes as $sStID => $aS)
				{
					$aStudent[$sStID]['stu'] = $aS;
					$aStIDs[] = $sStID;
				}
			}

			$aWhere = null;
			if (!is_null($aTbIDs))
			{
				$aWhere[] = array('tp.tbID','IN',$aTbIDs);
			}
			if (!is_null($aStIDs))
			{
				$aWhere[] = array('tp.stID','IN',$aStIDs);
			}

			$result = Model_Test::getTestPut($aWhere);
			if (count($result))
			{
				foreach ($result as $aP)
				{
					$sStID = $aP['stID'];
					$sTbID = $aP['tbID'];
					if (isset($aStudent[$sStID]))
					{
						$aStudent[$sStID]['put'][$sTbID] = $aP;
					}
				}
			}

			$res = array(
				array(
					'',
					'',
					'',
					'',
					'',
				),
				array(
					__('学籍番号'),
					__('氏名'),
					__('学年'),
					__('クラス'),
					__('提出'),
				)
			);
			if (!is_null($aTest))
			{
				foreach ($aTest as $aQ)
				{
					$res[0][] = $aQ['tbTitle'];
					$res[0][] = __('得点');
					$res[0][] = __('合格');
					$res[1][] = (int)$aQ['tpNum'];
					$res[1][] = ($aQ['tpNum'])? round(($aQ['tpScore']/$aQ['tpNum']),1):'0';
					$res[1][] = (int)$aQ['tpQualify'];
				}
			}

			if (!is_null($aStudent))
			{
				foreach ($aStudent as $sStID => $aS)
				{
					$aM = array(0=>'',1=>'',2=>'',3=>'',4=>0);

					if (isset($aS['stu']))
					{
						$aM[0] = $aS['stu']['stNO'];
						$aM[1] = $aS['stu']['stName'];
						$aM[2] = $aS['stu']['stYear'];
						$aM[3] = $aS['stu']['stClass'];
					}
					$aP = null;
					if (isset($aS['put']))
					{
						$aM[4] = count($aS['put']);
						$aP = $aS['put'];
					}
					if (!is_null($aTest))
					{
						foreach ($aTest as $aQ)
						{
							if (isset($aP[$aQ['tbID']]))
							{
								$aM[] = ClFunc_Tz::tz('Y/m/d H:i',$this->tz,$aP[$aQ['tbID']]['tpDate']);
								$aM[] = (int)$aP[$aQ['tbID']]['tpScore'];
								$aM[] = ($aP[$aQ['tbID']]['tpQualify'])? '○':'';
							}
							else
							{
								$aM[] = '';
								$aM[] = '';
								$aM[] = '';
							}
						}
					}
					$res[] = $aM;
				}
			}

			mb_convert_variables('sjis-win','UTF-8',$res);
			$this->response($res);
			return;
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect('/t/test');
		}
	}


	public function action_drill($sDcID = null, $iDbNO = null, $sFileName = null)
	{
		try
		{
			if (is_null($sDcID) || is_null($iDbNO) || is_null($sFileName))
			{
				throw new Exception(__('ドリルが指定されていません。'));
			}
			$aDrill = null;
			$aQuery = null;
			$aDGroup = null;

			$result = Model_Drill::getDrill(array(array('dcID','=',$sDcID),array('dbNO','=',$iDbNO)));
			if (!count($result))
			{
				throw new Exception(__('指定されたドリル情報が見つかりません。'));
			}
			$aDrill = $result->current();
			$result = Model_Drill::getDrillQuery(array(array('dcID','=',$sDcID),array('dbNO','=',$iDbNO)),null,array('dqSort'=>'asc'));
			if (!count($result))
			{
				throw new Exception(__('指定されたドリル問題が見つかりません。'));
			}
			$aQuery = $result->as_array('dqSort');

			$result = Model_Drill::getDrillQueryGroup(array(array('dcID','=',$sDcID)));
			if (!count($result))
			{
				throw new Exception(__('指定されたドリル問題が見つかりません。'));
			}
			$aDGroup = $result->as_array('dgNO');

			$res = array();

			$aStyle = array('radio','select','text');
			foreach ($aQuery as $aQ)
			{
				$dq = array(
					array(''),
					array('回答形式', $aStyle[$aQ['dqStyle']]),
					array('問題グループ', $aDGroup[$aQ['dgNO']]['dgName']),
					array('問題文', $aQ['dqText']),
					array('解説文', $aQ['dqExplain']),
				);

				if ($aQ['dqStyle'] < 2)
				{
					$dq[] = array('正解1', $aQ['dqRight1']);
					for ($i = 1; $i <= 50; $i++)
					{
						if ($aQ['dqChoice'.$i] != '')
						{
							$dq[] = array('選択肢'.$i , $aQ['dqChoice'.$i]);
						}
					}
				}
				else
				{
					for($i = 1; $i <= 5; $i++)
					{
						if ($aQ['dqRight'.$i] != '')
						{
							$dq[] = array('正解'.$i , $aQ['dqRight'.$i]);
						}
					}
				}
				$res = array_merge($res, $dq);
			}

			mb_convert_variables('sjis-win','UTF-8',$res);
			$this->response($res);
			return;
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			if (!is_null($sDcID))
			{
				Response::redirect('/t/drill/list/'.$sDcID);
			}
			else
			{
				Response::redirect('/t/drill');
			}
		}
	}

	public function action_reportputlist()
	{
		try
		{
			$aRateM = null;
			$result = Model_Report::getRateMasterFromClass($this->aClass['ctID']);
			if (count($result))
			{
				foreach ($result as $r)
				{
					$aRateM[$r['rrScore']] = $r;
				}
			}

			$aReport = null;
			$aRbIDs = null;
			$result = Model_Report::getReportBase(array(array('rb.ctID','=',$this->aClass['ctID'])),null,array('rb.rbSort'=>'desc'));
			if (count($result))
			{
				foreach ($result as $aQ)
				{
					$aReport[] = $aQ;
					$aRbIDs[] = $aQ['rbID'];
				}
			}

			$aStudent = null;
			$aStIDs = null;
			$result = Model_Student::getStudentFromClass($this->aClass['ctID'],null,null,array('st.stNO'=>'asc'));
			if (count($result))
			{
				$aRes = $result->as_array('stID');
				foreach ($aRes as $sStID => $aS)
				{
					$aStudent[$sStID]['stu'] = $aS;
					$aStIDs[] = $sStID;
				}
			}

			$aWhere = null;
			if (!is_null($aRbIDs))
			{
				$aWhere[] = array('rp.rbID','IN',$aRbIDs);
			}
			if (!is_null($aStIDs))
			{
				$aWhere[] = array('rp.stID','IN',$aStIDs);
			}

			$result = Model_Report::getReportPut($aWhere);
			if (count($result))
			{
				foreach ($result as $aP)
				{
					$sStID = $aP['stID'];
					$sRbID = $aP['rbID'];
					if (isset($aStudent[$sStID]))
					{
						$aStudent[$sStID]['put'][$sRbID] = $aP;
					}
				}
			}

			$res = array(
				array(
					'',
					'',
					'',
					'',
					'',
				),
				array(
					__('学籍番号'),
					__('氏名'),
					__('学年'),
					__('クラス'),
					__('提出'),
				)
			);
			if (!is_null($aReport))
			{
				foreach ($aReport as $aQ)
				{
					$res[0][] = $aQ['rbTitle'];
					$res[0][] = __('評価');
					$res[1][] = (int)$aQ['rbPutNum'];
					$res[1][] = '';
				}
			}

			if (!is_null($aStudent))
			{
				foreach ($aStudent as $sStID => $aS)
				{
					$aM = array(0=>'',1=>'',2=>'',3=>'',4=>0);

					if (isset($aS['stu']))
					{
						$aM[0] = $aS['stu']['stNO'];
						$aM[1] = $aS['stu']['stName'];
						$aM[2] = $aS['stu']['stYear'];
						$aM[3] = $aS['stu']['stClass'];
					}
					$aP = null;
					if (isset($aS['put']))
					{
						$aM[4] = count($aS['put']);
						$aP = $aS['put'];
					}
					if (!is_null($aReport))
					{
						foreach ($aReport as $aQ)
						{
							if (isset($aP[$aQ['rbID']]))
							{
								$aM[] = ($aP[$aQ['rbID']]['rpDate'] != CL_DATETIME_DEFAULT)? ClFunc_Tz::tz('Y/m/d H:i',$this->tz,$aP[$aQ['rbID']]['rpDate']):__('先生による提出');
								$aM[] = ($aP[$aQ['rbID']]['rpScore'])? $aRateM[$aP[$aQ['rbID']]['rpScore']]['rrName']:'─';
							}
							else
							{
								$aM[] = '';
								$aM[] = '';
							}
						}
					}
					$res[] = $aM;
				}
			}

			mb_convert_variables('sjis-win','UTF-8',$res);
			$this->response($res);
			return;
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect('/t/report');
		}
	}

	public function action_alog($sID = null, $sFileName = null)
	{
		try
		{
			if (is_null($sID) || is_null($sFileName))
			{
				throw new Exception(__('必要な情報が送信されていません。'));
			}
			$aALTheme = null;
			$aALog = null;
			$aALGoal = null;
			$aStudent = null;

			$result = Model_Alog::getAlogThemeFromID($sID);
			if (!count($result))
			{
				throw new Exception(__('指定されたテーマが見つかりません。'));
			}
			$aALTheme = $result->current();

			$result = Model_Alog::getAlogGoal(array(array('altID','=',$sID)));
			if (!count($result))
			{
				throw new Exception(__('指定されたテーマが見つかりません。'));
			}
			$aALGoal = $result->as_array('stID');

			$result = Model_Student::getStudentFromClass($this->aClass['ctID'],null,null,array('st.stNO'=>'asc'));
			if (count($result))
			{
				$aStudent = $result->as_array('stID');
			}

			$result = Model_Alog::getAlog(array(array('al.altID','=',$sID)),null,array('al.no'=>'desc'));
			if (!count($result))
			{
				throw new Exception(__('指定された記録が見つかりません。'));
			}
			$aALog = $result->as_array();

			$res = array(
				array('活動履歴テーマ', $aALTheme['altName']),
				array(
					__('記録日時'),
					__('曜日'),
					__('学籍番号'),
					__('学年'),
					__('クラス'),
					__('氏名'),
					$aALTheme['altGoalLabel'],
					($aALTheme['altTitle'])? $aALTheme['altTitleLabel']:'',
					($aALTheme['altRange'])? $aALTheme['altRangeLabel'].' '.__('開始日時'):'',
					($aALTheme['altRange'])? $aALTheme['altRangeLabel'].' '.__('終了日時'):'',
					$aALTheme['altTextLabel'],
					($aALTheme['altFile'])? $aALTheme['altFileLabel']:'',
					($aALTheme['altOpt1'])? $aALTheme['altOpt1Label']:'',
					($aALTheme['altOpt2'])? $aALTheme['altOpt2Label']:'',
					__('先生コメント'),
				),
			);

			foreach ($aALog as $aL)
			{
				$aRow = array(
					ClFunc_Tz::tz('Y/m/d H:i',$this->tz,$aL['alDate']),
					$this->aWeekday[date('N',strtotime($aL['alDate']))],
					$aStudent[$aL['stID']]['stNO'],
					$aStudent[$aL['stID']]['stYear'],
					$aStudent[$aL['stID']]['stClass'],
					$aStudent[$aL['stID']]['stName'],
					$aALGoal[$aL['stID']]['algText'],
					($aALTheme['altTitle'])? $aL['alTitle']:'',
					($aALTheme['altRange'])? $aL['alStart']:'',
					($aALTheme['altRange'])? $aL['alEnd']:'',
					$aL['alText'],
					($aALTheme['altFile'])? $aL['fName'].'('.$aL['fSize'].')':'',
					($aALTheme['altOpt1'])? $aL['alOpt1']:'',
					($aALTheme['altOpt2'])? $aL['alOpt2']:'',
					$aL['alCom'],
				);
				$res[] = $aRow;
			}
			mb_convert_variables('sjis-win','UTF-8',$res);
			$this->response($res);
			return;
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect('/t/alog');
		}
	}


}
