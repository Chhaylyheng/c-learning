<?php
class Controller_Adm_AdminLogin extends Controller_Base
{
	public function before()
	{
		parent::before();

		$this->sSiteTitle .= '（契約管理センター）';
		$this->template->set_global('dir', 'adm');
		$this->template->set_global('title', $this->sSiteTitle);
		$this->template->footer = View::forge('adm/footer');
	}

	public function action_index($noCookie = null)
	{
		Session::destroy();

		$data['algn_id'] = null;
		$data['algn_pass'] = null;
		$data['algn_chk'] = false;
		$data['error'] = null;
		$sNC = null;
		if (!is_null($noCookie))
		{
			$sNC  = 'ログイン情報が確認できませんでした。<br>以下の可能性がありますので、ご確認ください。<br>';
			$sNC .= '　1. ログインしたまま長時間操作していない場合<br>　　→ 再度ログインすることで解決します。<br>';
			$sNC .= '　2. COOKIEの情報が確認できない場合<br>　　→ お使いのブラウザにおいてCOOKIEの利用が有効かどうかご確認ください。<br>';
		}
		$sKey = Cookie::get("CL_ADM_KEY",false);
		if ($sKey)
		{
			$aKey = unserialize(Crypt::decode($sKey));
			$data['algn_id'] = $aKey['id'];
			$data['algn_pass'] = $aKey['pass'];
			$data['algn_chk'] = true;
		}

		$this->template->content = View::forge('adm/login',$data);
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
		$val->add_field('algn_id', 'ログインID', 'required|max_length[200]');
		$val->add_field('algn_pass', 'パスワード', 'required|max_length[200]');
		if (!$val->run())
		{
			$aInput['error'] = $val->error();
			$this->template->content = View::forge('adm/login',$aInput);
			return $this->template;
		}

		$result = Model_Admin::getAdminFromPostLogin($aInput['algn_id'],$aInput['algn_pass']);
		if (!count($result))
		{
			$aInput['error'] = array('login'=>'ログインIDまたはパスワードが間違っているため、ログインできません。');
			$this->template->content = View::forge('adm/login',$aInput);
			return $this->template;
		}
		$aResult = $result->current();

		// タイムゾーンの取得と確認
		try
		{
			ClFunc_Tz::tz_chk($aInput['ltzone']);
		}
		catch (Exception $e)
		{
			$aInput['ltzone'] = date_default_timezone_get();
		}
		if (!$aResult['adTimeZone'])
		{
			$aResult['adTimeZone'] = $aInput['ltzone'];
			$result = Model_Admin::updateAdmin($aResult['adID'],array('adTimeZone'=>$aInput['ltzone']));
		}

		if ($aInput['algn_chk'])
		{
			Cookie::set("CL_ADM_KEY",Crypt::encode(serialize(array('admseed'=>mt_rand(),'id'=>$aInput['algn_id'],'pass'=>$aInput['algn_pass']))),60*60*24*180);
		}
		else
		{
			Cookie::delete("CL_ADM_KEY");
		}
		Cookie::set("CL_ADM_HASH",Crypt::encode(serialize(array('admseed'=>mt_rand(),'hash'=>sha1($aResult['adLogin'].$aResult['adPass']),'ip'=>Input::real_ip()))));
		Response::redirect('adm/index');
	}
}
