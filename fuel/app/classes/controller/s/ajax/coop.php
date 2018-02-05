<?php
class Controller_S_Ajax_Coop extends Controller_S_Ajax
{
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

			if (!$aCCategory['ccStuWrite'])
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('操作が許可されていません。'));
				$this->response($res);
				return;
			}

			$result = Model_Coop::getCoop(array(array('ci.ccID','=',$par['cc']),array('ci.cNO','=',$par['cn'])));
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('スレッド情報が見つかりません。'));
				$this->response($res);
				return;
			}
			$aCoop = $result->current();

			$result = Model_Coop::getCoop(null,array(array('ci.cRoot','=',$par['cn']),array('ci.cBranch','=',$par['cn']),array('ci.cParent','=',$par['cn'])));
			if (count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('配下にコメントがあるため、削除できません。'));
				$this->response($res);
				return;
			}

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
			$res = array('err'=>0,'res'=>array('childNum'=>0, 'parentNo'=>array($aCoop['cRoot'],$aCoop['cBranch'])));
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
			$result = Model_Class::getClassFromID($par['ct'],1);
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
			if (!$aCCategory['ccStuWrite'])
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('操作が許可されていません。'));
				$this->response($res);
				return;
			}

			$aBaseCoop = null;
			$iNO = $par['cn'];
			if ($iNO != 0)
			{
				$result = Model_Coop::getCoop(array(array('ci.ccID','=',$sID),array('ci.cNO','=',$iNO)));
				if (!count($result))
				{
					$res = array('err'=>-2,'res'=>'','msg'=>__('情報が見つかりません。'));
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

			$sResMsg = '登録';
			$sDate = date('Y-m-d H:i:s');

			switch ($par['m'])
			{
				case 'input':
					$sResName = 'コメント';
					$afID = null;
					try
					{
						$afID = \Clfunc_Common::CoopFileSave($par,$this->aStudent['stID'],$this->sTempFilePath,$this->sAwsSavePath);
						$aInsert = array(
							'ccID'     => $sID,
							'cTitle'   => '',
							'fID1'     => $afID[1]['id'],
							'fID2'     => $afID[2]['id'],
							'fID3'     => $afID[3]['id'],
							'cText'    => $par['c_text'],
							'cCharNum' => mb_strlen($par['c_text']),
							'cID'      => $this->aStudent['stID'],
							'cName'    => $this->aStudent['stName'],
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
					if ($aCoop['cID'] != $this->aStudent['stID'])
					{
						$res = array('err'=>-2,'res'=>'','msg'=>__('操作が許可されていません。'));
						$this->response($res);
						return;
					}
					$sResMsg = '更新';
					$sResName = 'コメント';
					$afID = null;
					try
					{
						$afID = \Clfunc_Common::CoopFileSave($par,$this->aStudent['stID'],$this->sTempFilePath,$this->sAwsSavePath);
						$aUpdate = array(
							'fID1'     => $afID[1]['id'],
							'fID2'     => $afID[2]['id'],
							'fID3'     => $afID[3]['id'],
							'cName'    => $this->aStudent['stName'],
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
		if (isset($par['mail_reply']) && $par['mail_reply'] == 1 && !is_null($aBaseCoop) && $aBaseCoop['cID'] != $this->aStudent['stID'])
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
			$sUn = ($aCCategory['ccAnonymous'] < 2)? __('匿名'):$this->aStudent['stName'];
			$aOptions = array(
				'cID'    => $this->aStudent['stID'],
				'cMail'  => $this->aStudent['stMail'],
				'cName'  => $this->aStudent['stName'],
				'cSubMail' => $this->aStudent['stSubMail'],
				'files'  => $afID,
				'cTitle' => ((isset($par['c_title']))? $par['c_title']:''),
				'cText'  => $par['c_text'],
				'sWriter'  => $sWriter,
				'cUnknown' => $sUn,
			);
			\ClFunc_Mailsend::MailSendToCoop($aClass['ctID'],$aCCategory['ccID'],'s',$aReply,(int)$iTeacher,(int)$iStudent,$aOptions);
		}

		$res['res']['cBranch'] = (int)$aActive['cBranch'];
		$res['res']['cName'] = (($aActive['ttName'])? $aActive['ttName']:(($aActive['stName'])? $aActive['stName']:$aActive['cName']));
		$res['msg'] = __($sResName.'を'.$sResMsg.'しました。');
		$this->response($res);
		return;
	}
}

