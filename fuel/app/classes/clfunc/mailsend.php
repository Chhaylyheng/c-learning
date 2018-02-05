<?php
class ClFunc_Mailsend
{
	private static $bccPack = 10;

	public static function MailSendToClassStudents($sTtID = null, $sCtID = null, $sMode = null, $aOptions = null)
	{
		try
		{
			if (is_null($sCtID) || is_null($sMode))
			{
				throw new Exception('処理に必要な情報がありません');
			}

			$aTeacher = null;
			if (!is_null($sTtID))
			{
				$result = \Model_Teacher::getTeacherFromID($sTtID);
				if (!count($result)) {
					throw new Exception('先生が見つかりません');
				}
				$aTeacher = $result->current();
			}

			$result = \Model_Class::getClassFromID($sCtID);
			if (!count($result)) {
				throw new Exception('指定講義が見つかりません');
			}
			$aClass = $result->current();

			$iCnt = 0;
			$i = 0;
			$aBcc = null;
			$aDTs = array('Android'=>null, 'Apple'=>null);

			$result = \Model_Student::getStudentFromClass($sCtID, null, null, null, array(array('st.stMail','!=',''),array('st.stSubMail','!=','')));
			if ($iCnt = count($result)) {
				foreach ($result as $aS)
				{
					if ($aS['stMail'] != '')
					{
						$aBcc[$i][$aS['stMail']] = $aS['stName'];
					}
					if ($aS['stSubMail'] != '')
					{
						$aBcc[$i][$aS['stSubMail']] = $aS['stName'];
					}
					if (count($aBcc[$i]) >= self::$bccPack)
					{
						$i++;
					}

					if ($aS['stApp'] == 1 && $aS['stDeviceToken'] != '')
					{
						$oUC = new ClFunc_UnreadCount();
						$oUC->setStudent($aS);
						$aDTs['Apple'][] = array(
							'id' => $aS['stDeviceToken'],
							'badge' => $oUC->getUserCount(),
						);
					}
					else if ($aS['stApp'] == 2 && $aS['stDeviceToken'] != '')
					{
						$aDTs['Android'][] = $aS['stDeviceToken'];
					}

				}
			}

			$email = \Email::forge();
			$email->from(CL_MAIL_FROM, CL_MAIL_SENDER);
			$email->to(CL_MAIL_FROM);
			$email->reply_to(CL_MAIL_FROM);

			switch ($sMode)
			{
				case 'MatPublic':
					$sSubject = '[CL]教材を公開しました';
					$email->subject($sSubject);
					$aOptions['ttName'] = $aTeacher['ttName'];
					$aOptions['ctName'] = $aClass['ctName'].'（'.(\Clfunc_Common::getCode($aClass['ctCode'])).'）';
					$body = View::forge('email/t_material', $aOptions, false);

					$sMsg = __('新しい教材（:mat）が公開されました。',array('mat'=>$aOptions['mTitle']));
				break;
				case 'NewsSend':
					$sSubject = '[CL]講義からのお知らせ';
					$email->subject($sSubject);
					$aOptions['ctName'] = $aClass['ctName'].'（'.(\Clfunc_Common::getCode($aClass['ctCode'])).'）';
					$body = View::forge('email/t_news', $aOptions, false);

					$sMsg = $aOptions['body'];
				break;
				default:
					throw new Exception('メールの送信内容が不明です');
				break;
			}

			$email->body($body);

			try
			{
				if (!is_null($aBcc))
				{
					foreach ($aBcc as $aB)
					{
						$email->clear_bcc();
						$email->bcc($aB);
						$email->send();
					}
				}
				$email->clear_bcc();
				$email->clear_to();

				$email->to($aTeacher['ttMail'],$aTeacher['ttName']);
				if ($aTeacher['ttSubMail'])
				{
					$email->cc($aTeacher['ttSubMail'],$aTeacher['ttName']);
				}
				$email->body('[このメールは確認用です]'."\n".'このメールは、学生'.$iCnt.'名に送信しました。'."\n\n".$body);
				$email->send();
			}
			catch (\EmailValidationFailedException $e)
			{
				Log::warning('MailSendToClassStudents - '.$sMode.' - ' . $e->getMessage());
				throw new Exception('メールの送信に失敗しました');
			}
			catch (\EmailSendingFailedException $e)
			{
				Log::warning('MailSendToClassStudents - '.$sMode.' - ' . $e->getMessage());
				throw new Exception('メールの送信に失敗しました');
			}

			try
			{
				$res_and = null;
				$res_app = null;
				$res_app2 = null;

				$aCustom = array('name'=>'url', 'value'=>CL_PROTOCOL.'://'.CL_DOMAIN.'/s/class/index/'.$aClass['ctID']);
				if (!is_null($aDTs['Apple']))
				{
					$docroot = (isset($aOptions['docroot']))? $aOptions['docroot']:DOCROOT;
					$res_app = \ClFunc_Apppush::ApplePush($docroot.'assets/docs/', $aDTs['Apple'], '['.$aClass['ctName'].'] '.$sMsg, $aCustom, 'L');
					$res_app2 = \ClFunc_Apppush::ApplePush($docroot.'assets/docs/', $aDTs['Apple'], '['.$aClass['ctName'].'] '.$sMsg, $aCustom);
				}
				if (!is_null($aDTs['Android']))
				{
					$res_and = \ClFunc_Apppush::AndroidPush($aDTs['Android'], '['.$aClass['ctName'].'] '.$sMsg, $aCustom);
				}
			}
			catch (\Exception $e)
			{
				$dump = \Clfunc_Common::vdumpStr($e);
				\Log::error('Push Failed - '.$dump);
				throw new Exception('プッシュ通知の送信に失敗しました');
			}

		}
		catch (\Exception $e)
		{
			Log::warning('MailSendToClassStudents - '.$sMode.' - ' . $e->getMessage());
		}
		return true;
	}

	public static function MailSendToCoop($sCtID = null, $sCcID = null, $mode = 's', $aReply = null, $iTeacher =0, $iStudent = 0, $aOptions = null)
	{
		try
		{
			if (is_null($sCtID) || is_null($sCcID))
			{
				throw new Exception('処理に必要な情報がありません');
			}

			$result = \Model_Class::getClassFromID($sCtID);
			if (!count($result)) {
				throw new Exception('指定講義が見つかりません');
			}
			$aClass = $result->current();

			$result = \Model_Coop::getCoopCategoryFromID($sCcID);
			if (!count($result)) {
				throw new Exception('指定協働板が見つかりません');
			}
			$aCCategory = $result->current();

			$iCnt = 0;
			$i = 0;
			$aTBcc = null;
			$aSended = null;
			$aDTs = array('Android'=>null, 'Apple'=>null);
			$aDSs = array('Android'=>null, 'Apple'=>null);

			if (!is_null($aReply))
			{
				$aSended[] = $aOptions['sWriter'].'さん';
			}

			if ($iTeacher)
			{
				$result = \Model_Assistant::getAssistantFromClass(array(array('ap.ctID','=',$sCtID),array('ap.atID','!=',$aOptions['cID'])));
				if (count($result)) {
					foreach ($result as $aT)
					{
						$aTBcc[$i][$aT['atMail']] = $aT['atName'];
						if (count($aTBcc[$i]) >= self::$bccPack)
						{
							$i++;
						}
					}
				}

				$result = \Model_Teacher::getTeacherFromClass(array(array('tp.ctID','=',$sCtID),array('tp.ttID','!=',$aOptions['cID'])));
				if ($iCnt = count($result)) {
					foreach ($result as $aT)
					{
						$aTBcc[$i][$aT['ttMail']] = $aT['ttName'];
						if ($aT['ttSubMail'] != '')
						{
							$aTBcc[$i][$aT['ttSubMail']] = $aT['ttName'];
						}
						if (count($aTBcc[$i]) >= self::$bccPack)
						{
							$i++;
						}

						if ($aT['ttApp'] == 1 && $aT['ttDeviceToken'] != '')
						{
							$oUC = new ClFunc_UnreadCount();
							$oUC->setTeacher($aT);
							$aDTs['Apple'][] = array(
								'id' => $aT['ttDeviceToken'],
								'badge' => $oUC->getUserCount(),
							);
						}
						else if ($aT['ttApp'] == 2 && $aT['ttDeviceToken'] != '')
						{
							$aDTs['Android'][] = $aT['ttDeviceToken'];
						}

					}
				}
				$aSended[] = '先生'.$iCnt.'名';
			}

			$iCnt = 0;
			$i = 0;
			$aSBcc = null;

			if ($iStudent)
			{
				$result = \Model_Coop::getCoopStudents(array(array('ccID','=',$sCcID),array('stID','!=',$aOptions['cID'])));
				if (count($result)) {
					foreach ($result as $aS)
					{
						if ($aS['stMail'] != '' || $aS['stMail'] != '')
						{
							$iCnt++;
						}
						if ($aS['stMail'] != '')
						{
							$aSBcc[$i][$aS['stMail']] = $aS['stName'];
						}
						if ($aS['stSubMail'] != '')
						{
							$aSBcc[$i][$aS['stSubMail']] = $aS['stName'];
						}
						if (count($aSBcc[$i]) >= self::$bccPack)
						{
							$i++;
						}

						if ($aS['stApp'] == 1 && $aS['stDeviceToken'] != '')
						{
							$oUC = new ClFunc_UnreadCount();
							$oUC->setStudent($aS);
							$aDSs['Apple'][] = array(
								'id' => $aS['stDeviceToken'],
								'badge' => $oUC->getUserCount(),
							);
						}
						else if ($aS['stApp'] == 2 && $aS['stDeviceToken'] != '')
						{
							$aDSs['Android'][] = $aS['stDeviceToken'];
						}

					}
				}
				$aSended[] = '学生'.$iCnt.'名';
			}

			$email = \Email::forge();
			$email->from(CL_MAIL_FROM, CL_MAIL_SENDER);
			$email->to(CL_MAIL_FROM);
			$email->reply_to(CL_MAIL_FROM);

			$email->subject('[CL]協働板に投稿されました');
			$aOptions['ctName'] = $aClass['ctName'].'（'.(\Clfunc_Common::getCode($aClass['ctCode'])).'）';
			$aOptions['ccName'] = $aCCategory['ccName'];

			try
			{
				if (!is_null($aReply))
				{
					$email->clear_to();
					if ($aReply['mail'])
					{
						$email->to($aReply['mail']);
						if ($aReply['sub'])
						{
							$email->cc(array($aReply['sub']));
						}
					} else {
						if ($aReply['sub'])
						{
							$email->to(array($aReply['sub']));
						}
					}
					if ($aReply['type'] == 't')
					{
						$aOptions['mode'] = 't';
						if ($aReply['app'] == 1 && $aReply['token'] != '')
						{
							$oUC = new ClFunc_UnreadCount();
							$oUC->setTeacher($aReply);
							$aDTs['Apple'][] = array(
								'id' => $aReply['token'],
								'badge' => $oUC->getUserCount(),
							);
						}
						else if ($aReply['app'] == 2 && $aReply['token'] != '')
						{
							$aDSs['Android'][] = $aReply['token'];
						}
					}
					elseif ($aReply['type'] == 's')
					{
						$aOptions['mode'] = 's';
						if ($aReply['app'] == 1 && $aReply['token'] != '')
						{
							$oUC = new ClFunc_UnreadCount();
							$oUC->setStudent($aReply);
							$aDSs['Apple'][] = array(
								'id' => $aReply['token'],
								'badge' => $oUC->getUserCount(),
							);
						}
						else if ($aReply['app'] == 2 && $aReply['token'] != '')
						{
							$aDSs['Android'][] = $aReply['token'];
						}
					}
					else
					{
						$aOptions['mode'] = 't';
					}
					$email->subject('[CL]協働板に返信がありました');
					if ($email->get_to())
					{
						$aOptions['rName'] = $aReply['name'];
						$body = View::forge('email/coop', $aOptions, false);
						$email->body($body);
						$email->send();
					}
					$aOptions['rName'] = '';
				}

				$email->clear_to();
				$email->to(CL_MAIL_FROM);
				$email->clear_cc();
				$email->clear_bcc();
				$email->subject('[CL]協働板に投稿されました');

				if (!is_null($aTBcc))
				{
					$aOptions['mode'] = 't';
					$body = View::forge('email/coop', $aOptions, false);
					$email->body($body);
					foreach ($aTBcc as $aB)
					{
						$email->clear_bcc();
						$email->bcc($aB);
						$email->send();
					}
				}
				if (!is_null($aSBcc))
				{
					$aOptions['mode'] = 's';
					$body = View::forge('email/coop', $aOptions, false);
					$email->body($body);
					foreach ($aSBcc as $aB)
					{
						$email->clear_bcc();
						$email->bcc($aB);
						$email->send();
					}
				}

				$email->clear_bcc();
				$email->clear_to();

				if ($aOptions['cMail'])
				{
					$aOptions['cUnknown'] = '';
					$aOptions['mode'] = $mode;
					$body = View::forge('email/coop', $aOptions, false);
					$email->body($body);
					$email->to($aOptions['cMail'],$aOptions['cName']);
					if ($aOptions['cSubMail'])
					{
						$email->cc($aOptions['cSubMail'],$aOptions['cName']);
					}
					$email->body('[このメールは確認用です]'."\n".'このメールは、'.implode('・',$aSended).'に送信しました。'."\n\n".$body);
					$email->send();
				}
			}
			catch (\EmailValidationFailedException $e)
			{
				Log::warning('MailSendToCoopStudents - '.$sCcID.' - ' . $e->getMessage());
				throw new Exception('メールの送信に失敗しました');
			}
			catch (\EmailSendingFailedException $e)
			{
				Log::warning('MailSendToCoopStudents - '.$sCcID.' - ' . $e->getMessage());
				throw new Exception('メールの送信に失敗しました');
			}

			try
			{
				$res_and = null;
				$res_app = null;
				$res_app2 = null;

				$aCustom = array('name'=>'url', 'value'=>CL_PROTOCOL.'://'.CL_DOMAIN.'/t/class/index/'.$aClass['ctID']);
				$sMsg = '['.$aOptions['cName'].']'.__('協働板（:cc）に書き込みしました。',array('cc'=>$aCCategory['ccName']));

				if (!is_null($aDTs['Apple']))
				{
					$res_app = \ClFunc_Apppush::ApplePush(DOCROOT.'assets/docs/', $aDTs['Apple'], '['.$aClass['ctName'].'] '.$sMsg, $aCustom, 'T');
					$res_app2 = \ClFunc_Apppush::ApplePush(DOCROOT.'assets/docs/', $aDTs['Apple'], '['.$aClass['ctName'].'] '.$sMsg, $aCustom, 'TT');
				}
				if (!is_null($aDTs['Android']))
				{
					$res_and = \ClFunc_Apppush::AndroidPush($aDTs['Android'], '['.$aClass['ctName'].'] '.$sMsg, $aCustom, 'T');
				}

				$aCustom = array('name'=>'url', 'value'=>CL_PROTOCOL.'://'.CL_DOMAIN.'/s/class/index/'.$aClass['ctID']);
				if (!is_null($aDSs['Apple']))
				{
					$res_app = \ClFunc_Apppush::ApplePush(DOCROOT.'assets/docs/', $aDSs['Apple'], '['.$aClass['ctName'].'] '.$sMsg, $aCustom, 'L');
					$res_app2 = \ClFunc_Apppush::ApplePush(DOCROOT.'assets/docs/', $aDSs['Apple'], '['.$aClass['ctName'].'] '.$sMsg, $aCustom);
				}
				if (!is_null($aDSs['Android']))
				{
					$res_and = \ClFunc_Apppush::AndroidPush($aDSs['Android'], '['.$aClass['ctName'].'] '.$sMsg, $aCustom);
				}
			}
			catch (\Exception $e)
			{
				$dump = \Clfunc_Common::vdumpStr($e);
				\Log::error('Push Failed - '.$dump);
				throw new Exception('プッシュ通知の送信に失敗しました');
			}

		}
		catch (\Exception $e)
		{
			Log::warning('MailSendToCoopStudents - '.$sCcID.' - ' . $e->getMessage());
		}
		return true;
	}

	public static function FreeMailSendToStudents($aTeacher = null, $aStudents = null, $sName = null, $sSubject = null, $sBody = null, $aAssistant = null, $aClass = null, $bPush = false)
	{
		try
		{
			if (is_null($aTeacher) ||is_null($aStudents) || is_null($sName) || is_null($sSubject) || is_null($sBody))
			{
				throw new Exception('処理に必要な情報がありません');
			}

			$email = \Email::forge();
			$email->from(CL_MAIL_FROM, $sName);
			$email->to(CL_MAIL_FROM);
			$email->reply_to(CL_MAIL_FROM);

			$email->subject($sSubject);
			$body = View::forge('email/t_free');
			$body->set('mode','s');
			$body->set('body',$sBody,false);
			$email->body($body);

			$iCnt = count($aStudents);
			$i = 0;
			$aBcc = null;
			$aDTs = array('Android'=>null, 'Apple'=>null);

			foreach ($aStudents as $sStID => $aS)
			{
				if (isset($aS['main']))
				{
					$aBcc[$i][$aS['main']] = $aS['name'];
				}
				if (isset($aS['sub']))
				{
					$aBcc[$i][$aS['sub']] = $aS['name'];
				}
				if (count($aBcc[$i]) >= self::$bccPack)
				{
					$i++;
				}

				if ($bPush && isset($aS['stApp']))
				{
					if ($aS['stApp'] == 1 && $aS['stDeviceToken'] != '')
					{
						$aS['stID'] = $sStID;
						$oUC = new ClFunc_UnreadCount();
						$oUC->setStudent($aS);
						$aDTs['Apple'][] = array(
							'id' => $aS['stDeviceToken'],
							'badge' => $oUC->getUserCount(),
						);
					}
					else if ($aS['stApp'] == 2 && $aS['stDeviceToken'] != '')
					{
						$aDTs['Android'][] = $aS['stDeviceToken'];
					}
				}
			}

			try
			{
				if (!is_null($aBcc))
				{
					foreach ($aBcc as $aB)
					{
						$email->clear_bcc();
						$email->bcc($aB);
						$email->send();
					}
				}
				$email->clear_bcc();
				$email->clear_cc();
				$email->clear_to();

				$email->to($aTeacher['ttMail'],$aTeacher['ttName']);
				if ($aTeacher['ttSubMail'])
				{
					$email->cc($aTeacher['ttSubMail'],$aTeacher['ttName']);
				}
				if (!is_null($aAssistant))
				{
					$email->cc($aAssistant['atMail'],$aAssistant['atName']);
				}
				$email->body('[このメールは確認用です]'."\n".'このメールは、学生'.$iCnt.'名に送信しました。'."\n\n".$body);
				$email->send();
			}
			catch (\EmailValidationFailedException $e)
			{
				Log::warning('FreeMailSendToStudents - ' . $e->getMessage());
				throw new Exception('メールの送信に失敗しました');
			}
			catch (\EmailSendingFailedException $e)
			{
				Log::warning('FreeMailSendToStudents - ' . $e->getMessage());
				throw new Exception('メールの送信に失敗しました');
			}

			if ($bPush)
			{
				try
				{
					$res_and = null;
					$res_app = null;
					$res_app2 = null;

					$aCustom = array('name'=>'url', 'value'=>CL_PROTOCOL.'://'.CL_DOMAIN.'/s/class/index/'.$aClass['ctID']);
					$sMsg = '['.$sSubject.']'.$sBody;

					if (!is_null($aDTs['Apple']))
					{
						$res_app = \ClFunc_Apppush::ApplePush(DOCROOT.'assets/docs/', $aDTs['Apple'], '['.$aClass['ctName'].'] '.$sMsg, $aCustom, 'L');
						$res_app2 = \ClFunc_Apppush::ApplePush(DOCROOT.'assets/docs/', $aDTs['Apple'], '['.$aClass['ctName'].'] '.$sMsg, $aCustom);
					}
					if (!is_null($aDTs['Android']))
					{
						$res_and = \ClFunc_Apppush::AndroidPush($aDTs['Android'], '['.$aClass['ctName'].'] '.$sMsg, $aCustom);
					}
				}
				catch (\Exception $e)
				{
					$dump = \Clfunc_Common::vdumpStr($e);
					\Log::error('Push Failed - '.$dump);
					throw new Exception('プッシュ通知の送信に失敗しました');
				}
			}
		}
		catch (\Exception $e)
		{
			Log::warning('FreeMailSendToStudents - ' . $e->getMessage());
			throw new Exception('メールの送信に失敗しました');
		}
		return true;
	}

	public static function MailSendToContact($sCtID = null, $sStID = null, $mode = 's', $aOptions = null)
	{
		try
		{
			if (is_null($sCtID))
			{
				throw new Exception('処理に必要な情報がありません');
			}

			$result = \Model_Class::getClassFromID($sCtID);
			if (!count($result)) {
				throw new Exception('指定講義が見つかりません');
			}
			$aClass = $result->current();

			$i = 0;
			$aTBcc = null;
			$aSended = null;
			$aDTs = array('Android'=>null, 'Apple'=>null);

			if ($mode == 's')
			{
				$iCnt = 0;
				$result = \Model_Assistant::getAssistantFromClass(array(array('ap.ctID','=',$sCtID),array('ap.atID','!=',$aOptions['cID'])));
				if (count($result)) {
					foreach ($result as $aT)
					{
						$aTBcc[$i][$aT['atMail']] = $aT['atName'];
						if (count($aTBcc[$i]) >= self::$bccPack)
						{
							$i++;
						}
						$iCnt++;
					}
				}

				$result = \Model_Teacher::getTeacherFromClass(array(array('tp.ctID','=',$sCtID),array('tp.ttID','!=',$aOptions['cID'])));
				if (count($result)) {
					foreach ($result as $aT)
					{
						$aTBcc[$i][$aT['ttMail']] = $aT['ttName'];
						if ($aT['ttSubMail'] != '')
						{
							$aTBcc[$i][$aT['ttSubMail']] = $aT['ttName'];
						}
						if (count($aTBcc[$i]) >= self::$bccPack)
						{
							$i++;
						}

						if ($aT['ttApp'] == 1 && $aT['ttDeviceToken'] != '')
						{
							$oUC = new ClFunc_UnreadCount();
							$oUC->setTeacher($aT);
							$aDTs['Apple'][] = array(
								'id' => $aT['ttDeviceToken'],
								'badge' => $oUC->getUserCount(),
							);
						}
						else if ($aT['ttApp'] == 2 && $aT['ttDeviceToken'] != '')
						{
							$aDTs['Android'][] = $aT['ttDeviceToken'];
						}

						$iCnt++;
					}
				}
				$aSended[] = '先生'.$iCnt.'名';
			}

			$i = 0;
			$aSBcc = null;

			if ($mode == 't')
			{
				$iCnt = 0;
				$result = \Model_Student::getStudentFromClass($sCtID,array(array('sp.stID','=',$sStID)));
				if (count($result)) {
					$aS = $result->current();

					if ($aS['stMail'] != '')
					{
						$aSBcc[$i][$aS['stMail']] = $aS['stName'];
					}
					if ($aS['stSubMail'] != '')
					{
						$aSBcc[$i][$aS['stSubMail']] = $aS['stName'];
					}
					if (isset($aSBcc[$i]))
					{
						$iCnt++;
						if (count($aSBcc[$i]) >= self::$bccPack)
						{
							$i++;
						}
					}

					if ($aS['stApp'] == 1 && $aS['stDeviceToken'] != '')
					{
						$oUC = new ClFunc_UnreadCount();
						$oUC->setStudent($aS);
						$aDTs['Apple'][] = array(
							'id' => $aS['stDeviceToken'],
							'badge' => $oUC->getUserCount(),
						);
					}
					else if ($aS['stApp'] == 2 && $aS['stDeviceToken'] != '')
					{
						$aDTs['Android'][] = $aS['stDeviceToken'];
					}

					$aSended[] = $aS['stName'].'さん';
				}
			}

			$email = \Email::forge();
			$email->from(CL_MAIL_FROM, CL_MAIL_SENDER);
			$email->to(CL_MAIL_FROM);
			$email->reply_to(CL_MAIL_FROM);

			$email->subject('[CL]'.$aOptions['cSubject']);
			$aOptions['ctName'] = $aClass['ctName'].'（'.(\Clfunc_Common::getCode($aClass['ctCode'])).'）';

			try
			{
				if (!is_null($aTBcc))
				{
					$aOptions['mode'] = 't';
					$body = View::forge('email/contact', $aOptions, false);
					$email->body($body);
					foreach ($aTBcc as $aB)
					{
						$email->clear_bcc();
						$email->bcc($aB);
						$email->send();
					}
				}
				if (!is_null($aSBcc))
				{
					$aOptions['mode'] = 's';
					$body = View::forge('email/contact', $aOptions, false);
					$email->body($body);
					foreach ($aSBcc as $aB)
					{
						$email->clear_bcc();
						$email->bcc($aB);
						$email->send();
					}
				}

				$email->clear_bcc();
				$email->clear_to();

				if ($aOptions['cMail'])
				{
					$aOptions['mode'] = $mode;
					$body = View::forge('email/contact', $aOptions, false);
					$email->body($body);
					$email->to($aOptions['cMail'],$aOptions['cName']);
					if ($aOptions['cSubMail'])
					{
						$email->cc($aOptions['cSubMail'],$aOptions['cName']);
					}
					if ($iCnt > 0)
					{
						$sOptMsg = "\n".'このメールは、'.implode('・',$aSended).'に送信しました。';
					}
					else
					{
						$sOptMsg = "\n".$aSended[0].'は、メールアドレス未登録のため送信していません。';
					}

					$email->body('[このメールは確認用です]'.$sOptMsg."\n\n".$body);
					$email->send();
				}
			}
			catch (\EmailValidationFailedException $e)
			{
				Log::warning('MailSendToContactStudents - '.$sStID.' - ' . $e->getMessage());
				throw new Exception('メールの送信に失敗しました');
			}
			catch (\EmailSendingFailedException $e)
			{
				Log::warning('MailSendToContactStudents - '.$sStID.' - ' . $e->getMessage());
				throw new Exception('メールの送信に失敗しました');
			}

			try
			{
				$res_and = null;
				$res_app = null;
				$res_app2 = null;

				$sMsg = '['.$aOptions['cName'].']'.$aOptions['cBody'];

				if ($mode == 's')
				{
					$aCustom = array('name'=>'url', 'value'=>CL_PROTOCOL.'://'.CL_DOMAIN.'/t/class/index/'.$aClass['ctID']);
					if (!is_null($aDTs['Apple']))
					{
						$res_app = \ClFunc_Apppush::ApplePush(DOCROOT.'assets/docs/', $aDTs['Apple'], '['.$aClass['ctName'].'] '.$sMsg, $aCustom, 'T');
						$res_app2 = \ClFunc_Apppush::ApplePush(DOCROOT.'assets/docs/', $aDTs['Apple'], '['.$aClass['ctName'].'] '.$sMsg, $aCustom, 'TT');
					}
					if (!is_null($aDTs['Android']))
					{
						$res_and = \ClFunc_Apppush::AndroidPush($aDTs['Android'], '['.$aClass['ctName'].'] '.$sMsg, $aCustom, 'T');
					}
				}
				else if ($mode == 't')
				{
					$aCustom = array('name'=>'url', 'value'=>CL_PROTOCOL.'://'.CL_DOMAIN.'/s/class/index/'.$aClass['ctID']);
					if (!is_null($aDTs['Apple']))
					{
						$res_app = \ClFunc_Apppush::ApplePush(DOCROOT.'assets/docs/', $aDTs['Apple'], '['.$aClass['ctName'].'] '.$sMsg, $aCustom, 'L');
						$res_app2 = \ClFunc_Apppush::ApplePush(DOCROOT.'assets/docs/', $aDTs['Apple'], '['.$aClass['ctName'].'] '.$sMsg, $aCustom);
					}
					if (!is_null($aDTs['Android']))
					{
						$res_and = \ClFunc_Apppush::AndroidPush($aDTs['Android'], '['.$aClass['ctName'].'] '.$sMsg, $aCustom);
					}
				}
			}
			catch (\Exception $e)
			{
				$dump = \Clfunc_Common::vdumpStr($e);
				\Log::error('Push Failed - '.$dump);
				throw new Exception('プッシュ通知の送信に失敗しました');
			}

		}
		catch (\Exception $e)
		{
			Log::warning('MailSendToContactStudents - '.$sStID.' - ' . $e->getMessage());
		}
		return true;
	}



}
