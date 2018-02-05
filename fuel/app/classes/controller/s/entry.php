<?php
class Controller_S_Entry extends Controller_S_Basenl
{
	public function action_index()
	{
		# タイムゾーンの取得
		$tz_list = ClFunc_Tz::tz_list();
		$tz_region = ClFunc_Tz::$regions;
		$this->template->set_global('tz_list',$tz_list);
		$this->template->set_global('tz_region',$tz_region);

		$data['error'] = null;
		$this->template->content = View::forge($this->vDir.DS.'entry/index',$data);
	}

	public function action_entryform()
	{
		# タイムゾーンの取得
		$tz_list = ClFunc_Tz::tz_list();
		$tz_region = ClFunc_Tz::$regions;
		$this->template->set_global('tz_list',$tz_list);
		$this->template->set_global('tz_region',$tz_region);

		$aInput = Input::post();
		$val = Validation::forge();
		$val->add_callable('Helper_CustomValidation');

		$val->add('sent_name', __('氏名'))
			->add_rule('required')
			->add_rule('trim')
			->add_rule('max_length', 50);

		$val->add('sent_mail', __('メールアドレス'))
			->add_rule('required')
			->add_rule('trim')
			->add_rule('valid_email')
			->add_rule('max_length', 200)
			->add_rule('smail_chk');

		$val->add('sent_pass', __('パスワード'))
			->add_rule('required')
			->add_rule('trim')
			->add_rule('min_length', 8)
			->add_rule('max_length', 32)
			->add_rule('passwd_char');

		$val->add('sent_passchk', __('パスワード（確認）'))
			->add_rule('required')
			->add_rule('trim')
			->add_rule('match_field', 'sent_pass');

		if (!$val->run())
		{
			$aInput['error'] = $val->error();
			$this->template->content = View::forge($this->vDir.DS.'entry/index',$aInput);
			return $this->template;
		}

		if (CL_CAREERTASU_MODE)
		{
			Session::set('SES_S_ENTRYINPUT', base64_encode(serialize($aInput)));
			Response::redirect('s/entry/agreement'.$this->sesParam);
		}
		self::studentEntry($aInput);
		return;
	}

	public function action_agreement()
	{
		$sBackURL = '/s/entry'.$this->sesParam;
		$sTemp = Session::get('SES_S_ENTRYINPUT',false);
		if (!$sTemp)
		{
			Session::set('SES_S_ERROR_MSG',__('情報が正しく送信されていません。'));
			Response::redirect($sBackURL);
		}

		$post = Input::post(null,false);
		if ($post)
		{
			if (isset($post['agree']))
			{
				$aInput = unserialize(base64_decode($sTemp));
				self::studentEntry($aInput);
				return;
			}
			Session::set('SES_S_ERROR_MSG',__('規約に同意されない場合は、登録できません。'));
			Response::redirect($sBackURL);
		}

		$sPath = Asset::find_file('Comitasu_Member_agreement.txt', 'docs');
		$sTerm = file_get_contents($sPath);

		$this->template->content = View::forge($this->vDir.DS.'entry/agreement');
		$this->template->content->set('sTerm',$sTerm);

		return $this->template;
	}

	private function studentEntry($aInput = null)
	{
		// ログインID自動生成
		while(true)
		{
			$sLogin = strtolower(Str::random('distinct', 8));
			$aResult = Model_Student::getStudentFromLogin($sLogin);
			if (count($aResult))
			{
				continue;
			}
			break;
		}

		// 登録データ生成
		$aInsert = array(
			'stID'            => null,
			'stMail'          => trim($aInput['sent_mail']),
			'stPass'          => sha1(trim($aInput['sent_pass'])),
			'stName'          => $aInput['sent_name'],
			'stLogin'         => $sLogin,
			'stLoginNum'      => 0,
			'stLastLoginDate' => '00000000000000',
			'stLoginDate'     => '00000000000000',
			'stPassDate'      => date('Ymd'),
			'stPassMiss'      => 0,
			'stUA'            => Input::user_agent(),
			'stHash'          => sha1($sLogin.sha1(trim($aInput['sent_pass']))),
			'stStatus'        => 1,
			'stMailAuth'      => 0,
			'stTimeZone'      => (isset($aInput['sent_timezone']))? $aInput['sent_timezone']:date_default_timezone_get(),
			'stDate'          => date('YmdHis'),
		);

		try
		{
			$sStID = Model_Student::insertStudent($aInsert);
			if (CL_CAREERTASU_MODE)
			{
				$aClass = null;
				$result = Model_Class::getClassFromCode('1000',1);
				if (!count($result))
				{
					Session::set('SES_S_ERROR_MSG',__('指定の講義コードに該当する講義はありません。'));
					Response::redirect('s/entry'.$this->sesParam);
				}
				$aClass = $result->current();

				$result = Model_Class::entryClass($aClass['ctID'],$sStID);
			}
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_S_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		// 登録完了メール送信
		if ($aInsert['stMail']) {
			$email = \Email::forge();
			$email->from(CL_MAIL_FROM, CL_MAIL_SENDER);
			$email->to($aInsert['stMail']);
			$email->subject('[CL]'.__('学生アカウント登録手続き完了のお知らせ'));

			$html_body = View::forge('email/s_fin_html', $aInsert);
			$email->html_body($html_body);

			$body = View::forge('email/s_fin_plain', $aInsert);
			$email->alt_body($body);

			try
			{
				$email->send();
			}
			catch (\EmailValidationFailedException $e)
			{
				Log::warning('StudentRegistFinishMail - ' . $e->getMessage());
			}
			catch (\EmailSendingFailedException $e)
			{
				Log::warning('StudentRegistFinishMail - ' . $e->getMessage());
			}
		}

		if (Cookie::get('CL_COOKIE_CHK',false))
		{
			Cookie::set("CL_SL_HASH",Crypt::encode(serialize(array('hash'=>sha1($aInsert['stLogin'].$aInsert['stPass']),'ip'=>Input::real_ip()))));
		}
		else
		{
			Session::set('CL_SL_HASH',Crypt::encode(serialize(array('hash'=>sha1($aInsert['stLogin'].$aInsert['stPass']),'ip'=>Input::real_ip()))));
		}

		$this->template->content = View::forge($this->vDir.DS.'entry/mailSend',$aInput);
		return $this->template;
	}


	public function action_mailauth($sHash = null)
	{
		if (is_null($sHash))
		{
			Session::set('SES_S_ERROR_MSG',__('情報が正しく送信されていません。'));
			Response::redirect($this->eRedirect);
		}

		$result = Model_Student::getStudent(array(array('stHash','=',$sHash)));
		if (!count($result))
		{
			Session::set('SES_S_ERROR_MSG',__('認証情報が見つかりません。再度、認証メールの送信を行ってください。'));
			Response::redirect($this->eRedirect);
		}
		$aStudent = $result->current();

		// 登録データ生成
		$aUpdate = array(
			'stMailAuth' => 1,
		);

		try
		{
			$sStID = Model_Student::updateStudent($aStudent['stID'],$aUpdate);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_S_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		$this->template->content = View::forge($this->vDir.DS.'entry/mailauthFinish',$aStudent);
		return $this->template;
	}

}


