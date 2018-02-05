<?php
class Controller_T_Password extends Controller_T_Basenl
{
	public $psHash = null;
	public $paParam = null;
	public $aTeacher = null;

	public function action_index()
	{
		if (!Input::post(null,false))
		{
			$aInput = array('reset_mail'=>'');
			$this->template->content = View::forge('t/password/index',$aInput);
			return $this->template;
		}

		$aInput = Input::post();
		$val = Validation::forge();
		$val->add_field('reset_mail', __('メールアドレス'), 'required|max_length[200]');
		if (!$val->run())
		{
			$aInput['error'] = $val->error();
			$this->template->content = View::forge('t/password/index',$aInput);
			return $this->template;
		}

		$result = Model_Teacher::getTeacherFromMail($aInput['reset_mail']);
		if (!count($result))
		{
			$aInput['error'] = array('reset_mail'=>__('登録されていないメールアドレスです。'));
			$this->template->content = View::forge('t/password/index',$aInput);
			return $this->template;
		}
		$aResult = $result->current();

		// 再設定URL生成
		$aMD['reset_hash'] = Crypt::encode($aResult['ttID'].CL_SEP.strtotime('+24Hours'));

		// 登録完了メール送信
		$email = \Email::forge();
		$email->from(CL_MAIL_FROM, CL_MAIL_SENDER);
		$email->to($aResult['ttMail']);
		$email->subject('[CL]'.__('先生パスワードの再設定'));

		$html_body = View::forge('email/t_reset_html', $aMD);
		$email->html_body($html_body);

		$body = View::forge('email/t_reset_plain', $aMD);
		$email->alt_body($body);

		try
		{
			$email->send();
		}
		catch (\EmailValidationFailedException $e)
		{
			Log::warning('TeacherPasswordReminderMail - ' . $e->getMessage());
		}
		catch (\EmailSendingFailedException $e)
		{
			Log::warning('TeacherPasswordReminderMail - ' . $e->getMessage());
		}

		$this->template->content = View::forge('t/password/mailSend',$aInput);
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
			$this->template->content = View::forge('t/password/input',$data);
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
			$this->template->content = View::forge('t/password/input',$aInput);
			return $this->template;
		}

		$aUpdate = array(
			'ttPass'     => sha1($aInput['pre_pass']),
			'ttPassDate' => date('Ymd'),
			'ttPassMiss' => 0,
			'ttFirst'    => '',
			'ttHash'     => sha1($this->aTeacher['ttMail'].sha1($aInput['pre_pass'])),
		);

		try
		{
			$result = Model_Teacher::updateTeacher($this->paParam[0],$aUpdate);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		$this->template->content = View::forge('t/password/end',$aInput);
		return $this->template;

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
			print_r($this->paParam);
			exit();
		}
		if ($this->paParam[1] <= time())
		{
			Session::set('SES_T_ERROR_MSG',__('パスワード再設定URLの有効期限が切れています。改めて、変更手続きをお願いいたします。'));
			Response::redirect($this->eRedirect);
		}
		$result = Model_Teacher::getTeacherFromID($this->paParam[0]);
		if (!count($result))
		{
			Session::set('SES_T_ERROR_MSG',__('パスワードを再設定する対象の先生情報が見つかりませんでした。改めて、変更手続きをお願いいたします。'));
			Response::redirect($this->eRedirect);
		}
		$this->aTeacher = $result->current();
		$this->psHash = $hash;
	}


	public function action_first()
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
		$aTeacher = $result->current();

		if (!$aTeacher['ttFirst'])
		{
			Response::redirect('t/index');
		}

		if (!Input::post(null,false))
		{
			$data = array(
				'pre_pass'    => null,
				'pre_passchk' => null,
				'error'       => null,
			);
			$this->template->content = View::forge('t/password/first_input',$data);
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
			$this->template->content = View::forge('t/password/first_input',$aInput);
			return $this->template;
		}

		$aUpdate = array(
			'ttFirst' => '',
			'ttPass'     => sha1($aInput['pre_pass']),
			'ttPassDate' => date('Ymd'),
			'ttPassMiss' => 0,
			'ttHash'     => sha1($aTeacher['ttMail'].sha1($aInput['pre_pass'])),
		);

		try
		{
			$result = Model_Teacher::updateTeacher($aTeacher['ttID'],$aUpdate);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_S_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		Cookie::set("CL_TL_HASH",Crypt::encode(serialize(array('hash'=>sha1($aTeacher['ttMail'].$aUpdate['ttPass']),'ip'=>Input::real_ip()))));

		Session::set('SES_T_NOTICE_MSG',__('パスワードを変更しました。'));
		Response::redirect('t/index');
	}


}
