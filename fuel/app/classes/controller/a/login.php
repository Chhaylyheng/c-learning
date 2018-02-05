<?php
class Controller_A_Login extends Controller_A_Basenl
{
	public function action_index($noCookie = null)
	{
		if ($red = Session::get('CL_AL_LOGINMODEL',false))
		{
			$red .= (!is_null($noCookie))? DS.$noCookie:'';
			Session::delete('CL_AL_LOGINMODEL');
			Response::redirect($red);
		}

		Session::destroy();

		$data['algn_mail'] = null;
		$data['algn_pass'] = null;
		$data['algn_chk'] = false;
		$data['error'] = null;
		$sNC = null;
		if (!is_null($noCookie))
		{
			$sNC  = __('ログイン情報が確認できませんでした。').'<br>'.__('以下の可能性がありますので、ご確認ください。').'<br>';
			$sNC .= ' 1.'.__('ログインしたまま長時間操作していない場合').'<br> -> '.__('再度ログインすることで解決します。').'<br>';
			$sNC .= ' 2.'.__('COOKIEの情報が確認できない場合').'<br> -> '.__('お使いのブラウザにおいてCOOKIEの利用が有効かどうかご確認ください。').'<br>';
		}

		$sKey = Cookie::get("CL_AL_KEY",false);
		if ($sKey)
		{
			$aKey = unserialize(Crypt::decode($sKey));
			$data['algn_mail'] = (isset($aKey['mail']))? $aKey['mail']:'';
			$data['algn_pass'] = (isset($aKey['pass']))? $aKey['pass']:'';
			$data['algn_chk'] = true;
		}

		$this->template->content = View::forge('a/login',$data);
		if (!is_null($sNC))
		{
			$this->template->content->set('noCookie',$sNC,false);
		}
		return $this->template;
	}

	public function action_loginchk()
	{
		$aInput = Input::post();
		$aInput['algn_chk'] = (isset($aInput['algn_chk']))? true:false;

		$val = Validation::forge();
		$val->add_field('algn_mail', __('メールアドレス'), 'required|max_length[200]');
		$val->add_field('algn_pass', __('パスワード'), 'required|max_length[200]');
		if (!$val->run())
		{
			$aInput['error'] = $val->error();
			$this->template->content = View::forge('a/login',$aInput);
			return $this->template;
		}

		$result = Model_Assistant::getAssistantFromPostLogin($aInput['algn_mail'],$aInput['algn_pass']);
		if (!count($result))
		{
			$aInput['error'] = array('login'=>__('入力されたメールアドレスが存在しないか、パスワードが異なるためログインできません。'));
			$this->template->content = View::forge('a/login',$aInput);
			return $this->template;
		}
		$aResult = $result->current();

		// タイムゾーンの取得と確認
		$bTZ = false;
		try
		{
			ClFunc_Tz::tz_chk($aInput['ltzone']);
		}
		catch (Exception $e)
		{
			$aInput['ltzone'] = date_default_timezone_get();
		}
		if (!$aResult['atTimeZone'])
		{
			$aResult['atTimeZone'] = $aInput['ltzone'];
			$bTZ = true;
		}

		$result = Model_Assistant::setLoginUpdate($aResult,$bTZ);
		if (!count($result))
		{
			$aInput['error'] = array('login'=>__('入力されたメールアドレスが存在しないか、パスワードが異なるためログインできません。'));
			$this->template->content = View::forge('a/login',$aInput);
			return $this->template;
		}
		$aResult = $result->current();

		if ($aInput['algn_chk'])
		{
			Cookie::set('CL_AL_KEY',Crypt::encode(serialize(array('aseed'=>mt_rand(),'mail'=>$aInput['algn_mail'],'pass'=>$aInput['algn_pass']))),60*60*24*180);
		}
		else
		{
			Cookie::delete('CL_AL_KEY');
		}

		Cookie::set('CL_AL_HASH',Crypt::encode(serialize(array('aseed'=>mt_rand(),'hash'=>sha1($aResult['atMail'].$aResult['atPass']),'ip'=>Input::real_ip()))));
		Cookie::delete('CL_TL_HASH');

		if ($aResult['atFirst'])
		{
			Response::redirect('a/password/first');
		}

		Response::redirect('t/index');
	}

	public function action_logout()
	{
		Cookie::delete('CL_AL_HASH');
		Response::redirect('a/login');
	}

}
