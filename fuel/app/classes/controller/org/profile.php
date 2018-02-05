<?php
class Controller_Org_Profile extends Controller_Org_Base
{
	public function action_index()
	{
		$sTitle = __('アカウント設定');
		# パンくずリスト生成
		$this->template->set_global('breadcrumbs',array(array('name'=>$sTitle)));
		# ページタイトル生成
		$this->template->set_global('pagetitle',$sTitle);

		# 基本データ登録
		$data['a_name'] = $this->aAdmin['gaName'];
		$data['a_timezone'] = $this->aAdmin['gaTimeZone'];

		# タイムゾーンの取得
		$tz_list = ClFunc_Tz::tz_list();
		$tz_region = ClFunc_Tz::$regions;
		$this->template->set_global('tz_list',$tz_list);
		$this->template->set_global('tz_region',$tz_region);

		if (!Input::post(null,false))
		{
			$data['error'] = null;
			$this->template->content = View::forge('org/profile_index',$data);
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
			case 'pass':
				$val = Validation::forge();
				$val->add_callable('Helper_CustomValidation');
				$val->add('a_pass_now', __('現在のパスワード'))
					->add_rule('passwd_true',$this->aAdmin['gaPass']);
				$val->add('a_pass_edit', __('新しいパスワード'))
					->add_rule('required')
					->add_rule('min_length',8)
					->add_rule('max_length',32)
					->add_rule('passwd_char');
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
			$this->template->content = View::forge('org/profile_index',$data);
			return $this->template;
		}

		switch ($aInput['mode'])
		{
			case 'profile':
				$aUpdate = array(
					'gaName' => $aInput['a_name'],
					'gaTimeZone' => $aInput['a_timezone'],
				);
				Session::set('SES_ORG_NOTICE_MSG', __('プロフィールの変更が完了しました。'));
			break;
			case 'pass':
				$aUpdate = array(
					'gaPass' => sha1($aInput['a_pass_edit']),
					'gaPassDate' => date('Ymd'),
					'gaHash' => sha1($this->aAdmin['gaLogin'].sha1($aInput['a_pass_edit'])),
				);
				Session::set('SES_ORG_NOTICE_MSG', __('パスワードの変更が完了しました。'));
			break;
		}
		try
		{
			$result = Model_Group::updateGroupAdmin($aUpdate,array(array('gaID','=',$this->aAdmin['gaID'])));
			if (isset($aUpdate['gaHash']))
			{
				Cookie::delete('CL_ORG_KEY');
				Cookie::set('CL_ORG_HASH',Crypt::encode(serialize(array('hash'=>$aUpdate['gaHash'],'ip'=>Input::real_ip()))));
			}
		}
		catch (Exception $e)
		{
			Session::delete('SES_ORG_NOTICE_MSG');
			Session::set('SES_ORG_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		Response::redirect('/org/profile');
	}
}
