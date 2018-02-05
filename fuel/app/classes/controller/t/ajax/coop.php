<?php
class Controller_T_Ajax_Coop extends Controller_T_Ajax
{
	public function post_CateSort()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>__('情報が正しく送信されていません。'));
		$par = Input::post();
		if ($par)
		{
			$result = Model_Coop::getCoopCategoryFromID($par['cc']);
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('協働板情報が見つかりません。'));
				$this->response($res);
				return;
			}
			$aCCategory = $result->current();
			$result = Model_Coop::getCoopCategoryFromClass($par['ct'],null,null,array('ccSort'=>'desc'));
			if (!count($result))
			{
				$res = array('err'=>-1,'res'=>'','msg'=>__('並び順を変更する協働板の情報が見つかりません。'));
				$this->response($res);
				return;
			}
			$iMax = count($result);
			if (($aCCategory['ccSort'] == $iMax && $par['m'] == 'up') || ($aCCategory['ccSort'] == 1 && $par['m'] == 'down'))
			{
				$res = array('err'=>-1,'res'=>'','msg'=>__('一番上か一番下の協働板のため、並び順を変更することはできません。'));
				$this->response($res);
				return;
			}
			$result = Model_Coop::sortCoopCategory($aCCategory,$par['m']);
			$res = array('err'=>0,'res'=>array('m'=>$par['m']));
		}
		$this->response($res);
		return;
	}

	public function post_RangeChange()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>__('情報が正しく送信されていません。'));
		$par = Input::post();
		if ($par)
		{
			$result = Model_Coop::getCoopCategoryFromID($par['cc']);
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('協働板情報が見つかりません。'));
				$this->response($res);
				return;
			}
			$aCCategory = $result->current();

			if ($par['m'] == 'all')
			{
				$iRange = 2;
				$sText = __('全員');
				$sClass = 'font-blue';
			}
			elseif ($par['m'] == 'select')
			{
				$iRange = 1;
				$sText = __('選択');
				$sClass = 'font-green';
			}
			elseif ($par['m'] == 'none')
			{
				$iRange = 0;
				$sText = __('なし');
				$sClass = 'font-default';
			}
			else
			{
				$res = array('err'=>-1,'res'=>'','msg'=>__('更新情報が正しく送信されていません。'));
				$this->response($res);
				return;
			}

			$result = Model_Coop::changeStudentRange($aCCategory,$iRange);

			$res = array('err'=>0,'res'=>array('class'=>$sClass,'text'=>$sText),'msg'=>__('対象学生範囲を変更しました。'));
		}
		$this->response($res);
		return;
	}

	public function post_CoopAdd()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>__('追加する学生情報が正しく送信されていません。'));
		$par = Input::post();
		if ($par)
		{
			$result = Model_Class::getClassFromTeacher($this->aTeacher['ttID'],null,array(array('tp.ctID','=',$par['ct'])));
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('講義情報が見つかりません。'));
				$this->response($res);
				return;
			}
			$aClass = $result->current();
			$result = Model_Coop::getCoopCategoryFromID($par['cc'],array(array('ctID','=',$par['ct'])));
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('協働板情報が見つかりません。'));
				$this->response($res);
				return;
			}
			$aCCategory = $result->current();

			$result = Model_Student::getStudentFromClass($par['ct'],array(array('sp.stID','IN',$par['st'])));
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('対象の学生情報が見つかりません。'));
				$this->response($res);
				return;
			}
			$aStu = $result->as_array('stID');

			$result = Model_Coop::getCoopStudents(array(array('stID','IN',$par['st']),array('ccID','=',$par['cc'])));
			if (count($result))
			{
				foreach ($result as $aSC)
				{
					if (isset($aStu[$aSC['stID']]))
					{
						unset($aStu[$aSC['stID']]);
					}
				}
			}

			try
			{
				$iCnt = 0;
				if (count($aStu))
				{
					$result = Model_Coop::entryCoopsStudents(array($par['cc']),$aStu);
					$iCnt = $result[1];
				}
			}
			catch (Exception $e)
			{
				\Clfunc_Common::LogOut($e,__CLASS__);
				$res = array('err'=>-2,'res'=>'','msg'=>__('処理に失敗しました。時間をおいてから、再度実行してください。').'('.$e->getMessage().')');
				$this->response($res);
				return;
			}
			$res = array('err'=>0,'res'=>$iCnt);
		}
		$this->response($res);
		return;
	}

	public function post_CoopRemove()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>__('削除する学生情報が正しく送信されていません。'));
		$par = Input::post();
		if ($par)
		{
			$result = Model_Class::getClassFromTeacher($this->aTeacher['ttID'],null,array(array('tp.ctID','=',$par['ct'])));
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('講義情報が見つかりません。'));
				$this->response($res);
				return;
			}
			$aClass = $result->current();
			$result = Model_Coop::getCoopCategoryFromID($par['cc'],array(array('ctID','=',$par['ct'])));
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('協働板情報が見つかりません。'));
				$this->response($res);
				return;
			}
			$aCCategory = $result->current();

			$result = Model_Coop::getCoopStudents(array(array('stID','IN',$par['st']),array('ccID','=',$par['cc'])));
			if (count($result))
			{
				$aStu = $result->as_array('stID');
			}

			try
			{
				$iCnt = 0;
				if (count($aStu))
				{
					$result = Model_Coop::removeCoopsStudents(array($par['cc']),$aStu);
					$iCnt = $result;
				}
			}
			catch (Exception $e)
			{
				\Clfunc_Common::LogOut($e,__CLASS__);
				$res = array('err'=>-2,'res'=>'','msg'=>__('処理に失敗しました。時間をおいてから、再度実行してください。').'('.$e->getMessage().')');
				$this->response($res);
				return;
			}
			$res = array('err'=>0,'res'=>$iCnt);
		}
		$this->response($res);
		return;
	}

	public function post_CoopDelete()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>__('情報が正しく送信されていません。'));
		$par = Input::post();
		if ($par)
		{
			$result = Model_Coop::getCoopCategoryFromID($par['cc']);
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('協働板情報が見つかりません。'));
				$this->response($res);
				return;
			}
			$aCCategory = $result->current();
			$result = Model_Coop::getCoop(array(array('ci.ccID','=',$par['cc']),array('ci.cNO','=',$par['cn'])));
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('スレッド情報が見つかりません。'));
				$this->response($res);
				return;
			}
			$aCoop = $result->current();

			try
			{
				$result = Model_Coop::deleteCoop($aCoop);
			}
			catch (Exception $e)
			{
				\Clfunc_Common::LogOut($e,__CLASS__);
				$res = array('err'=>-2,'res'=>'','msg'=>__('処理に失敗しました。時間をおいてから、再度実行してください。').'('.$e->getMessage().')');
				$this->response($res);
				return;
			}
			$res = array('err'=>0,'res'=>array('childNum'=>$result, 'parentNo'=>array($aCoop['cRoot'],$aCoop['cBranch'])));
		}
		$this->response($res);
		return;
	}


	public function post_CoopRes()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>__('情報が正しく送信されていません。'));
		$par = Input::post();
		if ($par)
		{
			$result = Model_Class::getClassFromTeacher($this->aTeacher['ttID'],null,array(array('tp.ctID','=',$par['ct'])));
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('講義情報が見つかりません。'));
				$this->response($res);
				return;
			}
			$aClass = $result->current();

			$sID = $par['cc'];
			$result = Model_Coop::getCoopCategoryFromID($sID);
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('協働板情報が見つかりません。'));
				$this->response($res);
				return;
			}
			$aCCategory = $result->current();

			$aBaseCoop = null;
			$iNO = $par['cn'];
			if ($iNO != 0)
			{
				$result = Model_Coop::getCoop(array(array('ci.ccID','=',$sID),array('ci.cNO','=',$iNO)));
				if (!count($result))
				{
					$res = array('err'=>-2,'res'=>'','msg'=>__('スレッド情報が見つかりません。'));
					$this->response($res);
					return;
				}
				$aCoop = $result->current();

				if ($par['m'] == 'input')
				{
					$aBaseCoop = $aCoop;
				}
				else
				{
					if ($aCoop['cParent'] > 0)
					{
						$result = Model_Coop::getCoop(array(array('ci.ccID','=',$sID),array('ci.cNO','=',$aCoop['cParent'])));
						if (!count($result))
						{
							$res = array('err'=>-2,'res'=>'','msg'=>__('スレッド情報が見つかりません。'));
							$this->response($res);
							return;
						}
						$aBaseCoop = $result->current();
					}
				}
			}


			$sCID = $this->aTeacher['ttID'];
			$sCName = $this->aTeacher['ttName'];
			$sCMail = $this->aTeacher['ttMail'];
			$sCSMail = $this->aTeacher['ttSubMail'];

			if (!is_null($this->aAssistant))
			{
				$sCID = $this->aAssistant['atID'];
				$sCName = $this->aAssistant['atName'];
				$sCMail = $this->aAssistant['atMail'];
				$sCSMail = '';
			}

			$sResMsg = '登録';
			$sDate = date('Y-m-d H:i:s');

			switch ($par['m'])
			{
				case 'input':
					$sResName = 'コメント';
					$afID = null;
					try
					{
						$afID = \Clfunc_Common::CoopFileSave($par,$sCID,$this->sTempFilePath,$this->sAwsSavePath);
						$aInsert = array(
							'ccID'     => $sID,
							'cTitle'   => '',
							'fID1'     => $afID[1]['id'],
							'fID2'     => $afID[2]['id'],
							'fID3'     => $afID[3]['id'],
							'cText'    => $par['c_text'],
							'cCharNum' => mb_strlen($par['c_text']),
							'cID'      => $sCID,
							'cName'    => $sCName,
							'cDate'    => $sDate,
							'cRoot'    => (($aCoop['cRoot'] > 0)? $aCoop['cRoot']:$iNO),
							'cBranch'  => (($aCoop['cBranch'] > 0)? $aCoop['cBranch']:(($aCoop['cRoot'] > 0)? $aCoop['cNO']:0)),
							'cParent'  => $iNO,
							'fSumSize' => ($afID[1]['size'] + $afID[2]['size'] + $afID[3]['size']),
						);
						$iNO = \Model_Coop::insertCoop($aInsert);
					}
					catch (Exception $e)
					{
						if (!is_null($afID))
						{
							foreach ($afID as $i => $aF)
							{
								if (!$aF['id'])
								{
									continue;
								}
								$sfID = $aF['id'];
								$sFile = $aF['file'];
								\Clfunc_Aws::deleteFile($this->sAwsSavePath,$sFile);
								if ($iFileType == 1)
								{
									\Clfunc_Aws::deleteFile($this->sAwsSavePath, CL_PREFIX_THUMBNAIL.$sFile);
								}
								if ($iFileType == 2)
								{
									\Clfunc_Aws::deleteFile($this->sAwsSavePath,CL_PREFIX_ENCODE.$sfID.CL_AWS_ENCEXT);
									\Clfunc_Aws::deleteFile($this->sAwsSavePath,CL_PREFIX_THUMBNAIL.$sfID.'-00001.png');
								}
								\Model_File::deleteFile($sfID);
							}
						}

						\Clfunc_Common::LogOut($e,__CLASS__);
						$res = array('err'=>-2,'res'=>'','msg'=>__('登録に失敗しました。').$e->getMessage());
						$this->response($res);
						return;
					}

					if (!is_null($afID))
					{
						foreach ($afID as $i => $aF)
						{
							if (!$aF['id'])
							{
								continue;
							}
							@unlink($aF['sourcefile']);
							@unlink($aF['thumbfile']);
						}
					}
				break;
				case 'pedit':
				case 'edit':
					$sResMsg = '更新';
					$sResName = 'コメント';
					$afID = null;
					try
					{
						$afID = \Clfunc_Common::CoopFileSave($par,$sCID,$this->sTempFilePath,$this->sAwsSavePath);
						$aUpdate = array(
							'fID1'     => $afID[1]['id'],
							'fID2'     => $afID[2]['id'],
							'fID3'     => $afID[3]['id'],
							'cName'    => $sCName,
							'cText'    => $par['c_text'],
							'cCharNum' => mb_strlen($par['c_text']),
							'cDate'    => $sDate,
							'fSumSize' => ($afID[1]['size'] + $afID[2]['size'] + $afID[3]['size']),
						);
						if (isset($par['c_title']) && $par['c_title'] != '')
						{
							$sResName = 'スレッド';
							$aUpdate['cTitle'] = $par['c_title'];
						}
						$aWhere = array(
							array('cNO','=',$iNO),
							array('ccID','=',$sID),
						);
						$result = \Model_Coop::updateCoop($aUpdate,$aWhere,null,$sID);
					}
					catch (Exception $e)
					{
						if (!is_null($afID))
						{
							foreach ($afID as $i => $aF)
							{
								if (!$aF['file'])
								{
									continue;
								}
								$sfID = $aF['id'];
								$sFile = $aF['file'];
								\Clfunc_Aws::deleteFile($this->sAwsSavePath,$sFile);
								if ($iFileType == 1)
								{
									\Clfunc_Aws::deleteFile($this->sAwsSavePath, CL_PREFIX_THUMBNAIL.$sFile);
								}
								if ($iFileType == 2)
								{
									\Clfunc_Aws::deleteFile($this->sAwsSavePath,CL_PREFIX_ENCODE.$sfID.CL_AWS_ENCEXT);
									\Clfunc_Aws::deleteFile($this->sAwsSavePath,CL_PREFIX_THUMBNAIL.$sfID.'-00001.png');
								}
								\Model_File::deleteFile($sfID);
							}
						}

						\Clfunc_Common::LogOut($e,__CLASS__);
						$res = array('err'=>-2,'res'=>'','msg'=>__('更新に失敗しました。').$e->getMessage());
						$this->response($res);
						return;
					}

					if (!is_null($afID))
					{
						foreach ($afID as $i => $aF)
						{
							if (!$aF['file'])
							{
								continue;
							}
							@unlink($aF['sourcefile']);
							@unlink($aF['thumbfile']);
						}
					}
				break;
			}
		}

		$res['err'] = 0;
		$res['res'] = array(
			'cNO'     => $iNO,
			'cName'   => '',
			'cBranch' => 0,
			'cDate'   => ClFunc_Tz::tz('Y/m/d H:i',$this->tz,$sDate),
			'cText'   => nl2br(\Clfunc_Common::url2link($par['c_text'],480)),
			'cFiles'  => array(),
		);

		$result = \Model_Coop::getCoop(array(array('ci.ccID','=',$sID),array('ci.cNO','=',$iNO)));
		if (!count($result))
		{
			$res = array('err'=>-2,'res'=>'','msg'=>__('スレッド情報が見つかりません。'));
			$this->response($res);
			return;
		}
		$aActive = $result->current();

		for ($i = 1; $i <= 3; $i++)
		{
			if ($aActive['fID'.$i])
			{
				$sLink = \Uri::create('getfile/s3file/:fid',array('fid'=>$aActive['fID'.$i]));
				$sThumb = \Uri::create('getfile/s3file/:fid/:mode',array('fid'=>$aActive['fID'.$i],'mode'=>'t'));
				$sSize = \Clfunc_Common::FilesizeFormat($aActive['fSize'.$i],1);
				switch ($aActive['fFileType'.$i])
				{
					case 2:	# 映像の場合
						$sTag = '<video class="width-100" controls="controls" preload="none" src="'.$sLink.'" poster="'.$sThumb.'"></video>';
					break;
					case 1:	# 画像の場合
						$sTag = '<img class="width-100" src="'.$sLink.'" alt="'.$aActive['fName'.$i].'('.$sSize.')">';
					break;
					default:	# その他
						$sTag = '<i class="fa fa-paperclip"></i> <a href="'.$sLink.'" target="_blank">'.$aActive['fName'.$i].'('.$sSize.')</a>';
					break;
				}

				$res['res']['cFiles'][$i] = array(
					'obj'  => $i.'_'.$aActive['fID'.$i],
					'tag'  => $sTag,
					'name' => $aActive['fName'.$i],
					'size' => $sSize,
					'path' => $sLink,
				);
			}
		}

		$sWriter = null;
		$aReply = null;
		if (isset($par['mail_reply']) && $par['mail_reply'] == 1 && !is_null($aBaseCoop) && $aBaseCoop['cID'] != $sCID)
		{
			$bTeach = preg_match('/^[t|a]/', $aBaseCoop['cID']);
			$cName = ($aBaseCoop['atName'])? $aBaseCoop['atName']:(($aBaseCoop['ttName'])? $aBaseCoop['ttName']:(($aBaseCoop['stName'])? $aBaseCoop['stName']:$aBaseCoop['cName']));

			if (preg_match('/^t/', $aBaseCoop['cID']))
			{
				$result = Model_Teacher::getTeacherFromID_ex($aBaseCoop['cID']);
				if (count($result))
				{
					$aU = $result->current();
					$aReply = array(
						'type'  => 't',
						'ttID'  => $aU['ttID'],
						'name'  => $cName,
						'mail'  => $aU['ttMail'],
						'sub'   => $aU['ttSubMail'],
						'app'   => $aU['ttApp'],
						'token' => $aU['ttDeviceToken'],
					);
				}
			}
			elseif (preg_match('/^a/', $aBaseCoop['cID']))
			{
				$result = Model_Assistant::getAssistantFromID($aBaseCoop['cID']);
				if (count($result))
				{
					$aU = $result->current();
					$aReply = array(
						'type'  => 'a',
						'atID'  => $aU['atID'],
						'name'  => $cName,
						'mail'  => $aU['atMail'],
						'sub'   => null,
						'app'   => 0,
						'token' => null,
					);
				}
			}
			else
			{
				$result = Model_Student::getStudentFromID($aBaseCoop['cID']);
				if (count($result))
				{
					$aU = $result->current();
					if ($aU['stMail'] || $aU['stSubMail'] || $aU['stApp'])
					{
						$aReply = array(
							'type'  => 's',
							'stID'  => $aU['stID'],
							'name'  => $cName,
							'mail'  => $aU['stMail'],
							'sub'   => $aU['stSubMail'],
							'app'   => $aU['stApp'],
							'token' => $aU['stDeviceToken'],
						);
					}
				}
			}
			switch ($aCCategory['ccAnonymous'])
			{
				case 0:
					$sWriter = __('匿名');
				break;
				case 1:
					if ($bTeach):
					$sWriter = $cName;
					else:
					$sWriter = __('匿名');
					endif;
				break;
				case 2:
					$sWriter = $cName;
				break;
			}
		}
		$iTeacher = (isset($par['mail_teacher']) && $par['mail_teacher'] == 1)? 1:0;
		$iStudent = (isset($par['mail_student']) && $par['mail_student'] == 1)? 1:0;
		if ($iTeacher || $iStudent || !is_null($aReply))
		{
			$sUn = ($aCCategory['ccAnonymous'] == 0)? __('匿名'):$sCName;
			$aOptions = array(
				'cID'      => $sCID,
				'cMail'    => $sCMail,
				'cName'    => $sCName,
				'cSubMail' => $sCSMail,
				'files'    => $afID,
				'cTitle'   => ((isset($par['c_title']))? $par['c_title']:''),
				'cText'    => $par['c_text'],
				'sWriter'  => $sWriter,
				'cUnknown' => $sUn,
			);
			\ClFunc_Mailsend::MailSendToCoop($aClass['ctID'],$aCCategory['ccID'],'t',$aReply,(int)$iTeacher,(int)$iStudent,$aOptions);
		}

		$res['res']['cBranch'] = (int)$aActive['cBranch'];
		$res['res']['cName'] = (($aActive['atName'])? $aActive['atName']:(($aActive['ttName'])? $aActive['ttName']:(($aActive['stName'])? $aActive['stName']:$aActive['cName'])));
		$res['msg'] = $sResName.'を'.$sResMsg.'しました。';
		$this->response($res);
		return;
	}

	public function post_Sort()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>__('情報が正しく送信されていません。'));
		$par = Input::post();
		if ($par)
		{
			$result = Model_Coop::getCoop(array(array('ci.cNO','=',$par['cn']),array('ci.cRoot','=',0)));
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('スレッド情報が見つかりません。'));
				$this->response($res);
				return;
			}
			$aCoop = $result->current();
			$result = Model_Coop::getCoop(array(array('ci.ccID','=',$par['cc']),array('ci.cRoot','=',0)),null,array('ci.cSort'=>'desc'));
			if (!count($result))
			{
				$res = array('err'=>-1,'res'=>'','msg'=>__('並び順を変更するスレッドの情報が見つかりません。'));
				$this->response($res);
				return;
			}
			$iMax = count($result);
			if (($aCoop['cSort'] == $iMax && $par['m'] == 'up') || ($aCoop['cSort'] == 1 && $par['m'] == 'down'))
			{
				$res = array('err'=>-1,'res'=>'','msg'=>__('一番上か一番下のスレッドのため、並び順を変更することはできません。'));
				$this->response($res);
				return;
			}
			$result = Model_Coop::sortCoop($aCoop,$par['m']);
			$res = array('err'=>0,'res'=>array('m'=>$par['m']));
		}
		$this->response($res);
		return;
	}

	public function post_ChildSort()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>__('情報が正しく送信されていません。'));
		$par = Input::post();
		if ($par)
		{
			$result = Model_Coop::getCoop(array(array('ci.cNO','=',$par['cn']),array('ci.cRoot','!=',0),array('ci.cBranch','=',0)));
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('コメント情報が見つかりません。'));
				$this->response($res);
				return;
			}
			$aCoop = $result->current();
			$result = Model_Coop::getCoop(array(array('ci.cRoot','=',$aCoop['cRoot']),array('ci.cBranch','=',0)),null,array('ci.cSort'=>'asc'));
			if (!count($result))
			{
				$res = array('err'=>-1,'res'=>'','msg'=>__('並び順を変更するコメントの情報が見つかりません。'));
				$this->response($res);
				return;
			}
			$iMax = count($result);
			if ($aCoop['cSort'] == 1)
			{
				$res = array('err'=>-1,'res'=>'','msg'=>__('一番上のコメントのため、並び順を変更することはできません。'));
				$this->response($res);
				return;
			}
			$result = Model_Coop::sortChildCoop($aCoop);
			$res = array('err'=>0,'res'=>1);
		}
		$this->response($res);
		return;
	}

	public function post_TileLoad()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>__('情報が正しく送信されていません。'));
		$par = Input::post();
		if ($par)
		{
			$result = Model_Coop::getCoopCategoryFromID($par['Base'][0]);
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('協働板情報が見つかりません。'));
				$this->response($res);
				return;
			}
			$aCCategory = $result->current();

			if (isset($par['Base'][1]))
			{
				$result = Model_Coop::getCoop(array(array('ci.cNO','=',$par['Base'][1]),array('ci.cRoot','=',0)));
				if (!count($result))
				{
					$res = array('err'=>-2,'res'=>'','msg'=>__('スレッド情報が見つかりません。'));
					$this->response($res);
					return;
				}
				$aParent = $result->current();
				$aWBase = array('ci.cRoot','=',$aParent['cNO']);
			}
			else
			{
				$aWBase = array('ci.cRoot','=',0);
			}

			$aRes = null;
			foreach ($par['Stu'] as $sStID => $aData)
			{
				$aWhere = array(
					array('ci.ccID','=',$aCCategory['ccID']),
					$aWBase,
					array('ci.cBranch','=',0),
					array('ci.cID','=',$sStID),
				);
				$result = Model_Coop::getCoop($aWhere,null,array('ci.cDate'=>'desc'));
				if (count($result))
				{
					$aC = $result->as_array();
					$aRep = $aC[0];
					if (!isset($aData['cNO']) || $aRep['cNO'] != $aData['cNO'])
					{
						# 新しい記事になった
						if ($aRep['fFileType1'] == 1)
						{
							# 新規画像セット
							$sPath = \Uri::create('getfile/s3file/:fid',array('fid'=>$aRep['fID1']));
							$aRes[$sStID] = array('n'=>1,'cNO'=>$aRep['cNO'],'fID'=>$aRep['fID1'],'cText'=>'','path'=>$sPath);
						}
						else
						{
							# 画像じゃないのでテキストをセット
							$aRes[$sStID] = array('n'=>1,'cNO'=>$aRep['cNO'],'fID'=>'','cText'=>nl2br($aRep['cText']),'path'=>'');
						}
					}
					else
					{
						if ($aRep['fID1'] != '')
						{
							if ($aRep['fID1'] == $aData['fID'])
							{
								# 変化なし
								$aRes[$sStID] = array('n'=>0);
							}
							else if ($aRep['fFileType1'] == 1)
							{
								# 新規画像セット
								$sPath = \Uri::create('getfile/s3file/:fid',array('fid'=>$aRep['fID1']));
								$aRes[$sStID] = array('n'=>1,'cNO'=>$aRep['cNO'],'fID'=>$aRep['fID1'],'cText'=>'','path'=>$sPath);
							}
							else
							{
								# 画像じゃないのでテキストをセット
								$aRes[$sStID] = array('n'=>1,'cNO'=>$aRep['cNO'],'fID'=>'','cText'=>nl2br($aRep['cText']),'path'=>'');
							}
						}
						else
						{
							if (nl2br($aRep['cText']) == $aData['cText'])
							{
								# 変化なし
								$aRes[$sStID] = array('n'=>0);
							}
							else
							{
								# テキストをセット
								$aRes[$sStID] = array('n'=>1,'cNO'=>$aRep['cNO'],'fID'=>'','cText'=>nl2br($aRep['cText']),'path'=>'');
							}
						}
					}
				}
				else
				{
					# 変化なし
					$aRes[$sStID] = array('n'=>0);
					if ($aData != 'null') {
						# 記事削除等
						$aRes[$sStID] = array('n'=>2);
					}
					continue;
				}
			}
			$res = array('err'=>0, 'res'=>$aRes, 'msg'=>'');
		}
		$this->response($res);
		return;
	}

	public function post_AlreadyMember()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>__('情報が正しく送信されていません。'));
		$par = Input::post();
		if ($par)
		{
			$sID = $par['cc'];
			$iNO = $par['no'];

			$aStudent = null;
			$result = Model_Coop::getCoopStudents(array(array('ccID','=',$sID)),null,array('stNO'=>'asc','stName'=>'acs','stLogin'=>'acs'));
			if ($iCoopCount = count($result))
			{
				$aRes = $result->as_array();
				foreach ($aRes as $aR)
				{
					$aStudent[$aR['stID']] = $aR;
				}
			}

			$sMember = '';
			$sSep = '';
			$iCnt = 0;
			$result = Model_Coop::getCoopAlready(array(array('cNO','=',$iNO)));
			if (count($result))
			{
				$aRes = $result->as_array();
				foreach ($aRes as $aR)
				{
					if (isset($aStudent[$aR['caID']]))
					{
						$sMember .= $sSep.$aStudent[$aR['caID']]['stName'];
						$sSep = '、';
					}
				}
			}
			$res = array('err'=>0,'res'=>$sMember);
		}
		$this->response($res);
		return;
	}

}

