<?php
class Controller_Adm_Profile extends Controller_Adm_Base
{
	public function action_index()
	{
		# パンくずリスト生成
		$this->template->set_global('breadcrumbs',array(array('name'=>'アカウント設定')));
		# ページタイトル生成
		$this->template->set_global('pagetitle','アカウント設定');

		# 基本データ登録
		$data['a_name'] = $this->aAdmin['adName'];
		$data['a_timezone'] = $this->aAdmin['adTimeZone'];

		# タイムゾーンの取得
		$tz_list = ClFunc_Tz::tz_list();
		$tz_region = ClFunc_Tz::$regions;
		$this->template->set_global('tz_list',$tz_list);
		$this->template->set_global('tz_region',$tz_region);

		if (!Input::post(null,false))
		{
			$data['error'] = null;
			$this->template->content = View::forge('adm/profile_index',$data);
			return $this->template;
		}

		$aInput = Input::post();

		switch ($aInput['mode'])
		{
			case 'profile':
				$val = Validation::forge();
				$val->add_callable('Helper_CustomValidation');
				$val->add('a_name', '氏名')
					->add_rule('required')
					->add_rule('max_length',50);
			break;
			case 'mail':
				$val = Validation::forge();
				$val->add_callable('Helper_CustomValidation');
				$val->add('a_mail', '新しいメールアドレス')
					->add_rule('required')
					->add_rule('valid_email')
					->add_rule('max_length',200)
					->add_rule('tmail_chk',$this->aAdmin['adID']);
				$val->add('a_mail_chk', '新しいメールアドレス（確認）')
				->add_rule('required')
				->add_rule('match_field','a_mail');
			break;
			case 'pass':
				$val = Validation::forge();
				$val->add_callable('Helper_CustomValidation');
				$val->add('a_pass_now', '現在のパスワード')
					->add_rule('passwd_true',$this->aAdmin['adPass']);
				$val->add('a_pass_edit', '新しいパスワード')
					->add_rule('required')
					->add_rule('min_length',8)
					->add_rule('max_length',32)
					->add_rule('passwd_char');
				$val->add('a_pass_chk', '新しいパスワード（確認）')
					->add_rule('required')
					->add_rule('match_field','a_pass_edit');
			break;
		}
		if (!$val->run())
		{
			$data = array_merge($data,$aInput);
			$data['error'] = $val->error();
			$data['error']['profile_error'] = '変更に失敗しました。入力内容をご確認ください。';
			$this->template->content = View::forge('adm/profile_index',$data);
			return $this->template;
		}

		switch ($aInput['mode'])
		{
			case 'profile':
				$aUpdate = array(
					'adName' => $aInput['a_name'],
					'adTimeZone' => $aInput['a_timezone'],
				);
				Session::set('SES_ADM_NOTICE_MSG','プロフィールの変更が完了しました。');
			break;
			case 'mail':
				$aUpdate = array(
					'adMail' => $aInput['t_mail'],
				);
				Session::set('SES_ADM_NOTICE_MSG','メールアドレスの変更が完了しました。');
			break;
			case 'pass':
				$aUpdate = array(
					'adPass' => sha1($aInput['a_pass_edit']),
					'adPassDate' => date('Ymd'),
					'adHash' => sha1($this->aAdmin['adLogin'].sha1($aInput['a_pass_edit'])),
				);
				Session::set('SES_ADM_NOTICE_MSG','パスワードの変更が完了しました。');
			break;
		}
		try
		{
			$result = Model_Admin::updateAdmin($this->aAdmin['adID'],$aUpdate);
			if (isset($aUpdate['adHash']))
			{
				Cookie::delete('CL_ADM_KEY');
				Cookie::set('CL_ADM_HASH',Crypt::encode(serialize(array('hash'=>$aUpdate['adHash'],'ip'=>Input::real_ip()))));
			}
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::delete('SES_ADM_NOTICE_MSG');
			Session::set('SES_ADM_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		Response::redirect('/adm/profile');
	}
}
