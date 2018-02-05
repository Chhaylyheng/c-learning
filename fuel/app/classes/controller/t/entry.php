<?php

class Controller_T_Entry extends Controller_T_Basenl
{
	public function action_index()
	{
		if (!Input::post())
		{
			$data = array(
				'tent_mail' => null,
				'error' => null,
			);

			$this->template->content = View::forge('t/entry/preEntry',$data);
			return $this->template;
		}

		$aInput = Input::post();
		$val = Validation::forge();
		$val->add_callable('Helper_CustomValidation');

		$val->add('tent_mail', __('メールアドレス'))
		->add_rule('required')
		->add_rule('trim')
		->add_rule('valid_email')
		->add_rule('max_length', 200);

		if (!$val->run())
		{
			$aInput['error'] = $val->error();
			$this->template->content = View::forge('t/entry/preEntry',$aInput);
			return $this->template;
		}

		$bAlready = false;

		$result = Model_Teacher::getTeacherFromMail($aInput['tent_mail']);
		if (count($result))
		{
			$aTemp = $result->current();

			if ($aTemp['ttStatus'] < 3)
			{
				$bAlready = true;
				// 先生存在メール送信
				$email = \Email::forge();
				$email->from(CL_MAIL_FROM, CL_MAIL_SENDER);
				$email->to($aInput['tent_mail']);
				$email->subject('[CL]'.__('先生登録認証メール'));
				$html_body = View::forge('email/t_already_html', $aInput);
				$email->html_body($html_body);
				$body = View::forge('email/t_already_plain', $aInput);
				$email->alt_body($body);

				try
				{
					$email->send();
				}
				catch (\EmailValidationFailedException $e)
				{
					Log::warning('PreTeacherAlreadyMail - ' . $e->getMessage());
				}
				catch (\EmailSendingFailedException $e)
				{
					Log::warning('PreTeacherAlreadyMail - ' . $e->getMessage());
				}
			}
		}

		if (!$bAlready)
		{
			$aInsert = array(
				'ttMail' => trim($aInput['tent_mail']),
				'ttDate' => date('YmdHis'),
			);
			$sHash = Model_Teacher::insertPreTeacher($aInsert);

			$aInsert['ttHash'] = $sHash;

			// 先生登録認証メール送信
			$email = \Email::forge();
			$email->from(CL_MAIL_FROM, CL_MAIL_SENDER);
			$email->to($aInsert['ttMail']);
			$email->subject('[CL]'.__('先生登録認証メール'));
			$html_body = View::forge('email/t_pre_html', $aInsert);
			$email->html_body($html_body);
			$body = View::forge('email/t_pre_plain', $aInsert);
			$email->alt_body($body);

			try
			{
				$email->send();
			}
			catch (\EmailValidationFailedException $e)
			{
				Log::warning('PreTeacherEntryMail - ' . $e->getMessage());
			}
			catch (\EmailSendingFailedException $e)
			{
				Log::warning('PreTeacherEntryMail - ' . $e->getMessage());
			}
		}

		Session::set('SES_T_ENTRY_MAIL', $aInput['tent_mail']);
		Response::redirect('/t/entry/entrymail');
	}

	public function action_entrymail()
	{
		# タイトル
		$sTitle = __('先生アカウントの新規登録');
		$this->template->set_global('pagetitle',$sTitle);

		$sMail = Session::get('SES_T_ENTRY_MAIL',false);
		if (!$sMail)
		{
			Session::set('SES_T_ERROR_MSG',__('表示するための情報がありません。認証メールが届いているかご確認ください。'));
			Response::redirect($this->eRedirect);
		}

		$this->template->content = View::forge('t/entry/preFinish');
		$this->template->content->set('sMail',$sMail);
		return $this->template;
	}

	public function action_regist($sHash = null)
	{
		if (is_null($sHash))
		{
			Session::set('SES_T_ERROR_MSG',__('情報が正しく送信されていません。'));
			Response::redirect($this->eRedirect);
		}

		$result = Model_Teacher::getPreTeacher(array(array('ttHash','=',$sHash)));
		if (!count($result))
		{
			Session::set('SES_T_ERROR_MSG',__('認証情報が見つかりません。登録手続きを最初から行ってください。'));
			Response::redirect($this->eRedirect);
		}
		$aPre = $result->current();

		$dt1 = new DateTime('now');
		$dt2 = new DateTime($aPre['ttDate']);
		$diff = $dt1->diff($dt2);
		if ($diff->h >= CL_PRE_TIME)
		{
			Session::set('SES_T_ERROR_MSG',__('認証の有効期限が過ぎています。登録手続きを最初から行ってください。'));
			Response::redirect($this->eRedirect);
		}

		$sTtID = null;
		$aAlready = null;
		$result = Model_Teacher::getTeacherFromMail($aPre['ttMail']);
		if (count($result))
		{
			$aAlready = $result->current();
			if ($aAlready['ttStatus'] < 3)
			{
				Session::set('SES_T_ERROR_MSG',__('登録予定のメールアドレスが既に登録されているため、ログインすることができます。'));
				Response::redirect('/t');
			}
			$sTtID = $aAlready['ttID'];
		}

		if (is_null($aAlready))
		{
			// 登録データ生成
			$aInsert['teacher'] = array(
				'ttID'            => null,
				'ttMail'          => $aPre['ttMail'],
				'ttPass'          => '',
				'ttLoginNum'      => 0,
				'ttLastLoginDate' => '00000000000000',
				'ttLoginDate'     => '00000000000000',
				'ttPassDate'      => '00000000',
				'ttPassMiss'      => 0,
				'ttUA'            => Input::user_agent(),
				'ttHash'          => '',
				'ttStatus'        => 3,
				'ttProgress'      => 1,
				'ttDate'          => date('YmdHis'),
			);
			$aInsert['account'] = array(
				'ttID'    => null,
				'ahTitle' => __('先生アカウントの新規作成'),
				'ahIP'    => Input::real_ip(),
				'ahUA'    => Input::user_agent(),
				'ahDate'  => date('YmdHis'),
			);

			try
			{
				$sTtID = Model_Teacher::insertTeacher($aInsert);
			}
			catch (Exception $e)
			{
				\Clfunc_Common::LogOut($e,__CLASS__);
				Session::set('SES_T_ERROR_MSG',$e->getMessage());
				Response::redirect($this->eRedirect);
			}
		}

		Cookie::set('CL_INIT_TID', $sTtID);
		Session::set('SES_T_NOTICE_MSG', __('メール認証が完了しました。')."\n".('続いて先生アカウントを登録するにあたり、必要な情報を入力してください。'));
		Response::redirect('/t/init/profile');
	}

	public function action_socialregist()
	{
		$aInput = null;
		$aRes = Session::get('SES_AUTH',false);

		if ($aRes)
		{
			$aRes = unserialize($aRes);
			$aInput['tent_name'] = $aRes['info']['name'];
			$aInput['tent_mail'] = $aRes['info']['email'];
			$aInput['tent_uid']  = $aRes['uid'];
			$aInput['provider']  = $aRes['provider'];

			$sImg = null;
			if (isset($aRes['info']["image"])) {
				ini_set("allow_url_fopen",true);
				$sImg = file_get_contents($aRes['info']["image"], FILE_BINARY);
				ini_set("allow_url_fopen",false);
			}
		}
		else
		{
			Session::set('SES_T_ERROR_MSG','指定されているURLは無効です。');
			Response::redirect('index/error/');
		}
		Session::delete('SES_AUTH');

		$aWhere = array(
			array('tt'.ucfirst($aInput['provider']).'ID','=',$aInput['tent_uid']),
		);
		$result = Model_Teacher::getTeacher($aWhere);

		if (count($result))
		{
			$data = array('provider'=>$aInput['provider']);
			$this->template->content = View::forge('t/entry/already',$aInput);
			return $this->template;
		}

		try
		{
			$result = Model_Teacher::getTeacherFromMail($aInput['tent_mail']);
			if (count($result))
			{
				$aTeacher = $result->current();
				$aUpdate = array('tt'.ucfirst($aInput['provider']).'ID'=>$aInput['tent_uid']);
				$res = Model_Teacher::updateTeacher($aTeacher['ttID'],$aUpdate);
				$aInsert['teacher'] = $aTeacher;
				$sTtID = $aTeacher['ttID'];
			} else {
				$sPass = strtolower(Str::random('distinct', 8));
				// 登録データ生成
				$aInsert['teacher'] = array(
					'ttID'            => null,
					'ttMail'          => $aInput['tent_mail'],
					'ttPass'          => sha1($sPass),
					'ttName'          => $aInput['tent_name'],
					'tt'.ucfirst($aInput['provider']).'ID' => $aInput['tent_uid'],
					'ttLoginNum'      => 0,
					'ttLastLoginDate' => '00000000000000',
					'ttLoginDate'     => '00000000000000',
					'ttPassDate'      => '00000000',
					'ttPassMiss'      => 0,
					'ttUA'            => Input::user_agent(),
					'ttHash'          => sha1($aInput['tent_mail'].sha1($sPass)),
					'ttStatus'        => 3,
					'ttProgress'      => 1,
					'ttDate'          => date('YmdHis'),
				);
				$aInsert['account'] = array(
					'ttID'    => null,
					'ahTitle' => '先生アカウントの新規作成',
					'ahIP'    => Input::real_ip(),
					'ahUA'    => Input::user_agent(),
					'ahDate'  => date('YmdHis'),
				);
				$sTtID = Model_Teacher::insertTeacher($aInsert,$sImg);
			}
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect('index/error/');
		}

		Cookie::set('CL_TE_SOCIAL_'.$sTtID, $aRes['provider']);
		Cookie::set('CL_INIT_TID', $sTtID);

		Session::set('SES_T_NOTICE_MSG', __(':providerの認証が完了しました。',array('provider'=>$aRes['provider']))."\n".('続いて先生アカウントを登録するにあたり、必要な情報を入力してください。'));
		Response::redirect('/t/init/profile');
	}

	public function action_mailauth($sHash = null)
	{
		if (is_null($sHash))
		{
			Session::set('SES_T_ERROR_MSG',__('情報が正しく送信されていません。'));
			Response::redirect($this->eRedirect);
		}

		$result = Model_Teacher::getTeacher(array(array('ttHash','=',$sHash)));
		if (!count($result))
		{
			Session::set('SES_T_ERROR_MSG',__('認証情報が見つかりません。再度、認証メールの送信を行ってください。'));
			Response::redirect($this->eRedirect);
		}
		$aTeacher = $result->current();

		// 登録データ生成
		$aUpdate = array(
			'ttMailAuth' => 1,
		);

		try
		{
			$sTtID = Model_Teacher::updateTeacher($aTeacher['ttID'],$aUpdate);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		$this->template->content = View::forge('t/entry/mailauthFinish',$aTeacher);
		return $this->template;
	}

}


