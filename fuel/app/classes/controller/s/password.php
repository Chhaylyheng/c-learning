<?php
class Controller_S_Password extends Controller_S_Basenl
{
	public $psHash = null;
	public $paParam = null;
	public $aStudent = null;

	public function action_index()
	{
		if (!Input::post(null,false))
		{
			$aInput = array('reset_mail'=>'');
			$this->template->content = View::forge($this->vDir.DS.'password/reset_index',$aInput);
			return $this->template;
		}

		$aInput = Input::post();
		$val = Validation::forge();
		$val->add_field('reset_mail', __('メールアドレス'), 'required|max_length[200]');
		if (!$val->run())
		{
			$aInput['error'] = $val->error();
			$this->template->content = View::forge($this->vDir.DS.'password/reset_index',$aInput);
			return $this->template;
		}

		$result = Model_Student::getStudentFromMail($aInput['reset_mail']);
		if (!count($result))
		{
			$aInput['error'] = array('reset_mail'=>__('登録されていないメールアドレスです。'));
			$this->template->content = View::forge($this->vDir.DS.'password/reset_index',$aInput);
			return $this->template;
		}
		$aResult = $result->current();

		// 再設定URL生成
		$aMD['reset_hash'] = Crypt::encode($aResult['stID'].CL_SEP.strtotime('+24Hours'));

		// 登録完了メール送信
		$email = \Email::forge();
		$email->from(CL_MAIL_FROM, CL_MAIL_SENDER);
		$email->to($aResult['stMail']);
		$email->subject('[CL]'.__('学生パスワードの再設定'));

		$html_body = View::forge('email/s_reset_html', $aMD);
		$email->html_body($html_body);

		$body = View::forge('email/s_reset_plain', $aMD);
		$email->alt_body($body);

		try
		{
			$email->send();
		}
		catch (\EmailValidationFailedException $e)
		{
			Log::warning('StudentPasswordReminderMail - ' . $e->getMessage());
		}
		catch (\EmailSendingFailedException $e)
		{
			Log::warning('StudentPasswordReminderMail - ' . $e->getMessage());
		}

		$this->template->content = View::forge($this->vDir.DS.'password/reset_mailSend',$aInput);
		return $this->template;
	}


	public function action_reset($hash = null)
	{
		$this->hashDetect($hash);

		if (!Input::post(null,false))
		{
			$data = array(
				'pre_pass'    => null,
				'pre_passchk' => null,
				'error'       => null,
				'hash'        => $this->psHash,
			);
			$this->template->content = View::forge($this->vDir.DS.'password/reset_input',$data);
			return $this->template;
		}

		$aInput = Input::post();
		$aInput['hash'] = $this->psHash;

		$val = Validation::forge();
		$val->add_callable('Helper_CustomValidation');
		$val->add_field('pre_pass', __('新しいパスワード'), 'required|min_length[8]|max_length[32]|passwd_char');
		$val->add_field('pre_passchk', __('パスワード（確認）'), 'required|match_field[pre_pass]');
		if (!$val->run())
		{
			$aInput['error'] = $val->error();
			$this->template->content = View::forge($this->vDir.DS.'password/reset_input',$aInput);
			return $this->template;
		}

		$aUpdate = array(
			'stFirst'    => '',
			'stPass'     => sha1($aInput['pre_pass']),
			'stPassDate' => date('Ymd'),
			'stPassMiss' => 0,
			'stHash'     => sha1($this->aStudent['stLogin'].sha1($aInput['pre_pass'])),
		);

		try
		{
			$result = Model_Student::updateStudent($this->paParam[0],$aUpdate);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_S_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		$this->template->content = View::forge($this->vDir.DS.'password/reset_end',$aInput);
		return $this->template;

	}

	public function action_first()
	{
		if (Cookie::get('CL_COOKIE_CHK',false))
		{
			$sHash = Cookie::get('CL_SL_HASH',false);
		}
		else
		{
			$sHash = Session::get('CL_SL_HASH',false);
		}
		if (!$sHash)
		{
			Response::redirect('s/login/index/1');
		}
		$aLogin = unserialize(Crypt::decode($sHash));

		$result = Model_Student::getStudentFromHash($aLogin['hash']);
		if (!count($result))
		{
			Response::redirect('s/login/index/1');
		}
		$aStudent = $result->current();

		if (!$aStudent['stFirst'])
		{
			Response::redirect('s/index'.$this->sesParam);
		}

		if (!Input::post(null,false))
		{
			$data = array(
				'pre_pass'    => null,
				'pre_passchk' => null,
				'error'       => null,
			);
			$this->template->content = View::forge($this->vDir.DS.'password/first_input',$data);
			return $this->template;
		}

		$aInput = Input::post();

		$val = Validation::forge();
		$val->add_callable('Helper_CustomValidation');
		$val->add_field('pre_pass', __('新しいパスワード'), 'required|min_length[8]|max_length[32]|passwd_char');
		$val->add_field('pre_passchk', __('パスワード（確認）'), 'required|match_field[pre_pass]');
		if (!$val->run())
		{
			$aInput['error'] = $val->error();
			$this->template->content = View::forge($this->vDir.DS.'password/first_input',$aInput);
			return $this->template;
		}

		$aUpdate = array(
			'stFirst' => '',
			'stPass'     => sha1($aInput['pre_pass']),
			'stPassDate' => date('Ymd'),
			'stPassMiss' => 0,
			'stHash'     => sha1($aStudent['stLogin'].sha1($aInput['pre_pass'])),
		);

		try
		{
			$result = Model_Student::updateStudent($aStudent['stID'],$aUpdate);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_S_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		if (Cookie::get('CL_COOKIE_CHK',false))
		{
			Cookie::set("CL_SL_HASH",Crypt::encode(serialize(array('hash'=>sha1($aStudent['stLogin'].$aUpdate['stPass']),'ip'=>Input::real_ip()))));
		}
		else
		{
			Session::set('CL_SL_HASH',Crypt::encode(serialize(array('hash'=>sha1($aStudent['stLogin'].$aUpdate['stPass']),'ip'=>Input::real_ip()))));
		}

		Session::set('SES_S_NOTICE_MSG',__('パスワードを変更しました。'));
		Response::redirect('s/index'.$this->sesParam);
	}


	private function hashDetect($hash = null)
	{
		if (is_null($hash) || Crypt::decode($hash) === false)
		{
			Response::redirect('index/404','location',404);
		}
		$this->paParam = explode(CL_SEP, Crypt::decode($hash));
		if (!isset($this->paParam[1]))
		{
			Response::redirect('index/404','location',404);
		}
		if ($this->paParam[1] <= time())
		{
			Session::set('SES_S_ERROR_MSG',__('パスワード再設定URLの有効期限が切れています。改めて、変更手続きをお願いいたします。'));
			Response::redirect($this->eRedirect);
		}
		$result = Model_Student::getStudentFromID($this->paParam[0]);
		if (!count($result))
		{
			Session::set('SES_S_ERROR_MSG',__('パスワードを再設定する対象の学生情報が見つかりませんでした。改めて、変更手続きをお願いいたします。'));
			Response::redirect($this->eRedirect);
		}
		$this->aStudent = $result->current();
		$this->psHash = $hash;
	}
}
