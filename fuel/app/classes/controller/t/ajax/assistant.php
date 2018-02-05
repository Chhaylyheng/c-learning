<?php
class Controller_T_Ajax_Assistant extends Controller_T_Ajax
{
	public function post_regist()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>__('情報が正しく送信されていません。'));
		$par = Input::post();
		if ($par)
		{
			$result = Model_Class::getClassFromTeacher($this->aTeacher['ttID'],null,array(array('tp.ctID','=',$par['ct'])));
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('講義情報が確認できませんでした。'));
				$this->response($res);
				return;
			}
			$aClass = $result->current();

			$val = Validation::forge();
			$val->add_callable('Helper_CustomValidation');
			$val->add('a_name', __('氏名'))
				->add_rule('required')
				->add_rule('max_length',200)
			;
			$val->add('a_mail', __('メールアドレス'))
				->add_rule('required')
				->add_rule('valid_email')
				->add_rule('max_length',200)
				->add_rule('amail_chk')
			;
			if (!$val->run())
			{
				$e = $val->error();
				$msg = null;
				foreach ($e as $ei)
				{
					$msg .= htmlspecialchars($ei->get_message()).'<br>';
				}

				$res = array('err'=>-3,'res'=>'','msg'=>$msg);
				$this->response($res);
				return;
			}

			$sAtID = null;
			$result = Model_Assistant::getAssistantPosition(array(array('ap.ctID','=',$aClass['ctID'])));
			if (count($result))
			{
				$aTemp = $result->current();
				$sAtID = $aTemp['atID'];
			}

			$result = Model_Assistant::getAssistant(array(array('ttID','=',$this->aTeacher['ttID']),array('atMail','=',$par['a_mail'])));
			if (count($result))
			{
				$aAssist = $result->current();

				try
				{
					Model_Assistant::updateAssistant($aAssist['atID'],array('atName'=>$par['a_name']));

					if (!is_null($sAtID))
					{
						if ($sAtID != $aAssist['atID'])
						{
							Model_Assistant::removeAssistant(array(array('ctID','=',$aClass['ctID'])));
							Model_Assistant::setAssistant($aClass['ctID'],$aAssist['atID']);
							Model_Assistant::resetAssistantClassSort(array($sAtID,$aAssist['atID']));
						}
					}
					else
					{
						Model_Assistant::setAssistant($aClass['ctID'],$aAssist['atID']);
						Model_Assistant::resetAssistantClassSort(array($aAssist['atID']));
					}
				}
				catch (\Exception $e)
				{
					\Clfunc_Common::LogOut($e,__CLASS__);
					$res = array('err'=>-2,'res'=>'','msg'=>$e->getMessage());
					$this->response($res);
					return;
				}
			}
			else
			{
				try
				{
					$aInsert = array(
						'atMail' => $par['a_mail'],
						'atName' => $par['a_name'],
						'ttID' => $this->aTeacher['ttID'],
					);

					$sNewID = Model_Assistant::insertAssistant($aInsert);

					$result = Model_Assistant::getAssistant(array(array('ttID','=',$this->aTeacher['ttID']),array('atID','=',$sNewID)));
					if (count($result))
					{
						$aAssist = $result->current();
					}

					if (!is_null($sAtID))
					{
						Model_Assistant::removeAssistant(array(array('ctID','=',$aClass['ctID'])));
						Model_Assistant::setAssistant($aClass['ctID'],$sNewID);
						Model_Assistant::resetAssistantClassSort(array($sAtID,$sNewID));
					}
					else
					{
						Model_Assistant::setAssistant($aClass['ctID'],$sNewID);
						Model_Assistant::resetAssistantClassSort(array($sNewID));
					}
				}
				catch (\Exception $e)
				{
					\Clfunc_Common::LogOut($e,__CLASS__);
					$res = array('err'=>-2,'res'=>'','msg'=>$e->getMessage());
					$this->response($res);
					return;
				}
			}

			$aBcc = null;
			// 副担当登録メール送信
			$email = \Email::forge();
			$email->from(CL_MAIL_FROM, CL_MAIL_SENDER);
			$email->to($aAssist['atMail']);
			$aBcc[$this->aTeacher['ttMail']] = $this->aTeacher['ttName'];
			if ($this->aTeacher['ttSubMail'] != '')
			{
				$aBcc[$this->aTeacher['ttSubMail']] = $this->aTeacher['ttName'];
			}
			$email->bcc($aBcc);
			$email->subject('[CL]'.__('副担当登録メール'));

			$html_body = View::forge('email/a_reg_html');
			$html_body->set('aAssist',$aAssist);
			$html_body->set('aTeacher',$this->aTeacher);
			$html_body->set('aClass',$aClass);
			$email->html_body($html_body);

			$body = View::forge('email/a_reg_plain');
			$body->set('aAssist',$aAssist);
			$body->set('aTeacher',$this->aTeacher);
			$body->set('aClass',$aClass);
			$email->alt_body($body);

			try
			{
				$email->send();
			}
			catch (\EmailValidationFailedException $e)
			{
				Log::warning('AssisntantRegistMail - ' . $e->getMessage());
			}
			catch (\EmailSendingFailedException $e)
			{
				Log::warning('AssisntantRegistMail - ' . $e->getMessage());
			}

			$sMsg = __(':nameさんを「:class」の副担当に設定し、登録メールを送信しました。',array('name'=>$par['a_name'], 'class'=> $aClass['ctName']));
			$res = array('err'=>0,'res'=>array('atid'=>$aAssist['atID'],'name'=>$par['a_name'],'mail'=>$par['a_mail']),'msg'=>$sMsg);
		}
		$this->response($res);
		return;
	}

	public function post_set()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>__('情報が正しく送信されていません。'));
		$par = Input::post();
		if ($par)
		{
			$result = Model_Class::getClassFromTeacher($this->aTeacher['ttID'],null,array(array('tp.ctID','=',$par['ct'])));
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('講義情報が確認できませんでした。'));
				$this->response($res);
				return;
			}
			$aClass = $result->current();

			$result = Model_Assistant::getAssistant(array(array('ttID','=',$this->aTeacher['ttID']),array('atID','=',$par['at'])));
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('副担当情報が確認できませんでした。'));
				$this->response($res);
				return;
			}
			$aAssist = $result->current();

			$sCurrentID = null;
			$result = Model_Assistant::getAssistantPosition(array(array('ap.ctID','=',$aClass['ctID'])));
			if (count($result))
			{
				$aTemp = $result->current();
				$sCurrentID = $aTemp['atID'];
			}

			try
			{
				if (!is_null($sCurrentID))
				{
					if ($sCurrentID != $aAssist['atID'])
					{
						Model_Assistant::removeAssistant(array(array('ctID','=',$aClass['ctID'])));
						Model_Assistant::setAssistant($aClass['ctID'],$aAssist['atID']);
						Model_Assistant::resetAssistantClassSort(array($sCurrentID,$aAssist['atID']));
					}
				}
				else
				{
					Model_Assistant::setAssistant($aClass['ctID'],$aAssist['atID']);
					Model_Assistant::resetAssistantClassSort(array($aAssist['atID']));
				}
			}
			catch (\Exception $e)
			{
				\Clfunc_Common::LogOut($e,__CLASS__);
				$res = array('err'=>-2,'res'=>'','msg'=>$e->getMessage());
				$this->response($res);
				return;
			}

			$aBcc = null;
			// 副担当登録メール送信
			$email = \Email::forge();
			$email->from(CL_MAIL_FROM, CL_MAIL_SENDER);
			$aBcc[$this->aTeacher['ttMail']] = $this->aTeacher['ttName'];
			if ($this->aTeacher['ttSubMail'] != '')
			{
				$aBcc[$this->aTeacher['ttSubMail']] = $this->aTeacher['ttName'];
			}
			$email->bcc($aBcc);
			$email->to($aAssist['atMail']);
			$email->subject('[CL]'.__('副担当登録メール'));

			$html_body = View::forge('email/a_reg_html');
			$html_body->set('aAssist',$aAssist);
			$html_body->set('aTeacher',$this->aTeacher);
			$html_body->set('aClass',$aClass);
			$email->html_body($html_body);

			$body = View::forge('email/a_reg_plain');
			$body->set('aAssist',$aAssist);
			$body->set('aTeacher',$this->aTeacher);
			$body->set('aClass',$aClass);
			$email->alt_body($body);

			try
			{
				$email->send();
			}
			catch (\EmailValidationFailedException $e)
			{
				Log::warning('AssisntantRegistMail - ' . $e->getMessage());
			}
			catch (\EmailSendingFailedException $e)
			{
				Log::warning('AssisntantRegistMail - ' . $e->getMessage());
			}

			$sMsg = __(':nameさんを「:class」の副担当に設定し、登録メールを送信しました。',array('name'=>$aAssist['atName'], 'class'=>$aClass['ctName']));
			$res = array('err'=>0,'res'=>array('atid'=>$aAssist['atID'],'name'=>$aAssist['atName'],'mail'=>$aAssist['atMail']),'msg'=>$sMsg);
		}
		$this->response($res);
		return;
	}

	public function post_remove()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>__('情報が正しく送信されていません。'));
		$par = Input::post();
		if ($par)
		{
			$result = Model_Class::getClassFromTeacher($this->aTeacher['ttID'],null,array(array('tp.ctID','=',$par['ct'])));
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('講義情報が確認できませんでした。'));
				$this->response($res);
				return;
			}
			$aClass = $result->current();

			$sCurrentID = null;
			$result = Model_Assistant::getAssistantPosition(array(array('ap.ctID','=',$aClass['ctID'])));
			if (count($result))
			{
				$aTemp = $result->current();
				$sCurrentID = $aTemp['atID'];
			}

			try
			{
				Model_Assistant::removeAssistant(array(array('ctID','=',$aClass['ctID'])));
				if (!is_null($sCurrentID))
				{
					Model_Assistant::resetAssistantClassSort(array($sCurrentID));
				}
			}
			catch (\Exception $e)
			{
				\Clfunc_Common::LogOut($e,__CLASS__);
				$res = array('err'=>-2,'res'=>'','msg'=>$e->getMessage());
				$this->response($res);
				return;
			}

			$sMsg = __('「:class」から副担当を削除しました。',array('class'=>$aClass['ctName']));
			$res = array('err'=>0,'res'=>array('name'=>__('未設定')),'msg'=>$sMsg);
		}
		$this->response($res);
		return;
	}

}
