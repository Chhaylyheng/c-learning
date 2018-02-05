<?php
class Controller_A_Profile extends Controller_T_Base
{
	public function before()
	{
		parent::before();
		# サブタイトル生成
		$this->template->set_global('aClass',null);
	}

	public function action_index()
	{
		$sTitle = __('アカウント設定');
		# パンくずリスト生成
		$this->template->set_global('breadcrumbs',array(array('name'=>$sTitle)));
		# ページタイトル生成
		$this->template->set_global('pagetitle',$sTitle);

		# 基本データ登録
		$data['a_name'] = $this->aAssistant['atName'];
		$data['a_timezone'] = $this->aAssistant['atTimeZone'];

		# タイムゾーンの取得
		$tz_list = ClFunc_Tz::tz_list();
		$tz_region = ClFunc_Tz::$regions;
		$this->template->set_global('tz_list',$tz_list);
		$this->template->set_global('tz_region',$tz_region);

		if (!Input::post(null,false))
		{
			$data['error'] = null;
			$this->template->content = View::forge('a/profile/index',$data);
			return $this->template;
		}

		$aInput = Input::post();

		switch ($aInput['mode'])
		{
			case 'profile':
				$val = Validation::forge();
				$val->add_callable('Helper_CustomValidation');

				$val->add('a_name', __('氏名'))
					->add_rule('required')
					->add_rule('max_length',50);
			break;
			case 'mail':
				$val = Validation::forge();
				$val->add_callable('Helper_CustomValidation');
				$val->add('a_mail', __('新しいメールアドレス'))
					->add_rule('valid_email')
					->add_rule('max_length',200)
					->add_rule('amail_chk',$this->aAssistant['atID']);
				$val->add('a_mail_chk', __('新しいメールアドレス（確認）'))
					->add_rule('match_field','a_mail');
			break;
			case 'pass':
				$val = Validation::forge();
				$val->add_callable('Helper_CustomValidation');
				$val->add('a_pass_now', __('現在のパスワード'))
					->add_rule('passwd_true',$this->aAssistant['atPass']);
				$val->add('a_pass_edit', __('新しいパスワード'))
					->add_rule('required')
					->add_rule('min_length',8)
					->add_rule('max_length',32)
					->add_rule('passwd_char')
					->add_rule('passwd_false',$this->aAssistant['atPass']);
				$val->add('a_pass_chk', __('新しいパスワード（確認）'))
					->add_rule('required')
					->add_rule('match_field','a_pass_edit');
			break;
		}
		if (!$val->run())
		{
			$data = array_merge($data,$aInput);
			$data['error'] = $val->error();
			$data['error']['profile_error'] = __('変更に失敗しました。入力内容をご確認ください。');
			$this->template->content = View::forge('a/profile/index',$data);
			return $this->template;
		}

		switch ($aInput['mode'])
		{
			case 'profile':
				$aUpdate = array(
					'atName'=>$aInput['a_name'],
					'atTimeZone' => $aInput['a_timezone'],
				);
				Session::set('SES_T_NOTICE_MSG',__('プロフィールの変更が完了しました。'));
			break;
			case 'mail':
				$sMain = '';
				$aUpdate = array();
				if (isset($aInput['a_mail']) && trim($aInput['a_mail']))
				{
					$aUpdate['atMail'] = trim($aInput['a_mail']);
					$aUpdate['atHash'] = sha1($aUpdate['atMail'].$this->aAssistant['atPass']);
				}
				Session::set('SES_T_NOTICE_MSG',__('メールアドレスの変更が完了しました。'));
			break;
			case 'pass':
				$aUpdate = array(
					'atPass' => sha1($aInput['a_pass_edit']),
					'atPassDate' => date('Ymd'),
					'atHash' => sha1($this->aAssistant['atMail'].sha1($aInput['a_pass_edit'])),
				);
				Session::set('SES_T_NOTICE_MSG',__('パスワードの変更が完了しました。'));
			break;
		}
		try
		{
			$result = Model_Assistant::updateAssistant($this->aAssistant['atID'],$aUpdate);
			if (isset($aUpdate['atHash']))
			{
				Cookie::delete('CL_AL_KEY');
				Cookie::set('CL_AL_HASH',Crypt::encode(serialize(array('hash'=>$aUpdate['atHash'],'ip'=>Input::real_ip()))));
			}
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::delete('SES_T_NOTICE_MSG');
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}
		Response::redirect('/a/profile');
	}
}