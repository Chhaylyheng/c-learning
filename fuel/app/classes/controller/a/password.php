<?php
class Controller_A_Password extends Controller_A_Basenl
{
	public $psHash = null;
	public $paParam = null;
	public $aAssist = null;

	public function action_index()
	{
		if (!Input::post(null,false))
		{
			$aInput = array('reset_mail'=>'');
			$this->template->content = View::forge('a/password/index',$aInput);
			return $this->template;
		}

		$aInput = Input::post();
		$val = Validation::forge();
		$val->add_field('reset_mail', __('メールアドレス'), 'required|max_length[200]');
		if (!$val->run())
		{
			$aInput['error'] = $val->error();
			$this->template->content = View::forge('a/password/index',$aInput);
			return $this->template;
		}

		$result = Model_Assistant::getAssistantFromMail($aInput['reset_mail']);
		if (!count($result))
		{
			$aInput['error'] = array('reset_mail'=>__('登録されていないメールアドレスです。'));
			$this->template->content = View::forge('a/password/index',$aInput);
			return $this->template;
		}
		$aResult = $result->current();

		// 再設定URL生成
		$aMD['reset_hash'] = Crypt::encode($aResult['atID'].CL_SEP.strtotime('+24Hours'));

		// 登録完了メール送信
		$email = \Email::forge();
		$email->from(CL_MAIL_FROM, CL_MAIL_SENDER);
		$email->to($aResult['atMail']);
		$email->subject('[CL]'.__('副担当パスワードの再設定'));

		$html_body = View::forge('email/a_reset_html', $aMD);
		$email->html_body($html_body);

		$body = View::forge('email/a_reset_plain', $aMD);
		$email->alt_body($body);

		try
		{
			$email->send();
		}
		catch (\EmailValidationFailedException $e)
		{
			Log::warning('AssistantPasswordReminderMail - ' . $e->getMessage());
		}
		catch (\EmailSendingFailedException $e)
		{
			Log::warning('AssistantPasswordReminderMail - ' . $e->getMessage());
		}

		$this->template->content = View::forge('a/password/mailSend',$aInput);
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
			$this->template->content = View::forge('a/password/input',$data);
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
			$this->template->content = View::forge('a/password/input',$aInput);
			return $this->template;
		}

		$aUpdate = array(
			'atPass'     => sha1($aInput['pre_pass']),
			'atPassDate' => date('Ymd'),
			'atPassMiss' => 0,
			'atFirst'    => '',
			'atHash'     => sha1($this->aAssistant['atMail'].sha1($aInput['pre_pass'])),
		);

		try
		{
			$result = Model_Assistant::updateAssistant($this->paParam[0],$aUpdate);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		$this->template->content = View::forge('a/password/end',$aInput);
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
		$result = Model_Assistant::getAssistantFromID($this->paParam[0]);
		if (!count($result))
		{
			Session::set('SES_T_ERROR_MSG',__('パスワードを再設定する対象の副担当情報が見つかりませんでした。改めて、変更手続きをお願いいたします。'));
			Response::redirect($this->eRedirect);
		}
		$this->aAssistant = $result->current();
		$this->psHash = $hash;
	}


	public function action_first()
	{
		$sHash = Cookie::get('CL_AL_HASH',false);
		if (!$sHash)
		{
			Response::redirect('a/login/index/1');
		}
		$aLogin = unserialize(Crypt::decode($sHash));

		$result = Model_Assistant::getAssistantFromHash($aLogin['hash']);
		if (!count($result))
		{
			Response::redirect('a/login/index/1');
		}
		$aAssistant = $result->current();

		if (!$aAssistant['atFirst'])
		{
			Response::redirect('a/index');
		}

		if (!Input::post(null,false))
		{
			$data = array(
				'pre_pass'    => null,
				'pre_passchk' => null,
				'error'       => null,
			);
			$this->template->content = View::forge('a/password/first_input',$data);
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
			$this->template->content = View::forge('a/password/first_input',$aInput);
			return $this->template;
		}

		$aUpdate = array(
			'atFirst' => '',
			'atPass'     => sha1($aInput['pre_pass']),
			'atPassDate' => date('Ymd'),
			'atPassMiss' => 0,
			'atHash'     => sha1($aAssistant['atMail'].sha1($aInput['pre_pass'])),
		);

		try
		{
			$result = Model_Assistant::updateAssistant($aAssistant['atID'],$aUpdate);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		Cookie::set("CL_AL_HASH",Crypt::encode(serialize(array('hash'=>sha1($aAssistant['atMail'].$aUpdate['atPass']),'ip'=>Input::real_ip()))));

		Session::set('SES_T_NOTICE_MSG',__('パスワードを変更しました。'));
		Response::redirect('t/index');
	}


}
