<?php
class Controller_Org_Password extends Controller_Base
{
	public function before()
	{
		parent::before();

		$this->sSiteTitle .= '（団体管理）';
		$this->template->set_global('dir', 'org');
		$this->template->set_global('title', $this->sSiteTitle);
		$this->template->footer = View::forge('org/footer');
	}

	public function action_first()
	{
		$sHash = Cookie::get('CL_ORG_HASH',false);
		if (!$sHash)
		{
			Response::redirect('org/login/index/1');
		}
		$aLogin = unserialize(Crypt::decode($sHash));
/*
		if (Input::real_ip() != $aLogin['ip'])
		{
			Session::set('SES_ORG_ERROR_MSG','ログインした時と異なるネットワーク、または端末からアクセスされた可能性があります。');
			Response::redirect($this->eRedirect);
		}
*/
		$result = Model_Group::getGroupAdminFromHash($aLogin['hash']);
		if (!count($result))
		{
			Response::redirect('org/login/index/1');
		}
		$aAdmin = $result->current();

		if (!$aAdmin['gaFirst'])
		{
			Response::redirect('org/index');
		}

		if (!Input::post(null,false))
		{
			$data = array(
				'pre_pass'    => null,
				'pre_passchk' => null,
				'error'       => null,
			);
			$this->template->content = View::forge('org/password/first_input',$data);
			return $this->template;
		}

		$aInput = Input::post();

		$val = Validation::forge();
		$val->add_callable('Helper_CustomValidation');
		$val->add_field('pre_pass', '新しいパスワード', 'required|min_length[8]|max_length[32]|passwd_char');
		$val->add_field('pre_passchk', 'パスワード（確認）', 'required|match_field[pre_pass]');
		if (!$val->run())
		{
			$aInput['error'] = $val->error();
			$this->template->content = View::forge('org/password/first_input',$aInput);
			return $this->template;
		}

		$aUpdate = array(
			'gaFirst'    => '',
			'gaPass'     => sha1($aInput['pre_pass']),
			'gaPassDate' => date('Ymd'),
			'gaPassMiss' => 0,
			'gaHash'     => sha1($aAdmin['gaLogin'].sha1($aInput['pre_pass'])),
		);

		try
		{
			$result = Model_Group::updateGroupAdmin($aUpdate,array(array('gaID','=',$aAdmin['gaID'])));
		}
		catch (Exception $e)
		{
			Session::set('SES_ORG_ERROR_MSG',$e->getMessage());
			Response::redirect('index/error/org');
		}

		Cookie::set("CL_ORG_HASH",Crypt::encode(serialize(array('hash'=>sha1($aAdmin['gaLogin'].$aUpdate['gaPass']),'ip'=>Input::real_ip()))));

		Session::set('SES_ORG_NOTICE_MSG','パスワードを変更しました。');
		Response::redirect('org/index');
	}
}
