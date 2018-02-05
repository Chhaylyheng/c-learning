<?php
class Controller_G_Login extends Controller_G_Basenl
{
	public function action_index($noCookie = null)
	{
		Session::destroy();

		$data['cl_code'] = null;
		$data['error'] = null;
		$sNC = null;
		if (!is_null($noCookie))
		{
			$sNC  = __('ゲスト情報が確認できませんでした。').'<br>'.__('再度、講義コードを入力してください。').'<br>';
		}

		$this->template->content = View::forge($this->vDir.DS.'login',$data);
		if (!is_null($sNC))
		{
			$this->template->content->set('noCookie',$sNC,false);
		}

		// チェッカークッキー発行
		Cookie::set('CL_COOKIE_CHK','cookies_enable',60*60*24);

		return $this->template;
	}

	public function action_loginchk()
	{
		$aInput = Input::post();

		if (Cookie::get('CL_COOKIE_CHK',false))
		{
			Cookie::set('CL_COOKIE_CHK','cookie_enable');
			$sHash = Cookie::get('CL_GL_HASH',false);
		}
		else
		{
			$sHash = Session::get('CL_GL_HASH',false);
		}
		$aGuest = null;
		$aLogin['id'] = 'new-guest';
		if ($sHash)
		{
			$aLogin = unserialize(Crypt::decode($sHash));
		}
		$result = Model_Guest::getGuestCheck($aLogin['id']);
		if (count($result)) {
			$aGuest = $result->current();
		}

		$sCode = isset($aInput['cl_code'])? trim($aInput['cl_code']):'';
		$result = Model_Class::getClassFromGuestCode($sCode,1);
		if (!count($result))
		{
			$aInput['error'] = array('cl_code'=> __('指定された講義コードが誤っているか、ゲスト訪問が許可されていません。'));
			$this->template->content = View::forge($this->vDir.DS.'login',$aInput);
			return $this->template;
		}
		$aClass = $result->current();

		// タイムゾーンの取得と確認
		try
		{
			ClFunc_Tz::tz_chk($aInput['ltzone']);
		}
		catch (Exception $e)
		{
			$aInput['ltzone'] = date_default_timezone_get();
		}

		if (Cookie::get('CL_COOKIE_CHK',false))
		{
			Cookie::set("CL_GL_HASH",Crypt::encode(serialize(array('gseed'=>mt_rand(),'id'=>$aGuest['gtID'],'ct'=>$aClass['ctID'],'ip'=>Input::real_ip()))),60*60*24);
			Cookie::set("CL_GL_TZ",$aInput['ltzone'],60*60*24);
		}
		else
		{
			Session::set('CL_GL_HASH',Crypt::encode(serialize(array('gseed'=>mt_rand(),'id'=>$aGuest['gtID'],'ct'=>$aClass['ctID'],'ip'=>Input::real_ip()))));
			Session::set("CL_GL_TZ",$aInput['ltzone']);
		}


		Response::redirect('g/index'.$this->sesParam);
	}
}
