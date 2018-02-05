<?php
class Controller_Org_Login extends Controller_Base
{
	public function before()
	{
		parent::before();

		$this->sSiteTitle .= '（'.__('団体管理').'）';
		$this->template->set_global('dir', 'org');
		$this->template->set_global('title', $this->sSiteTitle);
		$this->template->footer = View::forge('org/footer');
	}

	public function action_index($noCookie = null)
	{
		Session::destroy();

		$data['olgn_id'] = null;
		$data['olgn_pass'] = null;
		$data['olgn_chk'] = false;
		$data['error'] = null;
		$sNC = null;
		if (!is_null($noCookie))
		{
			$sNC  = __('ログイン情報が確認できませんでした。').'<br>'.__('以下の可能性がありますので、ご確認ください。').'<br>';
			$sNC .= ' 1.'.__('ログインしたまま長時間操作していない場合').'<br> -> '.__('再度ログインすることで解決します。').'<br>';
			$sNC .= ' 2.'.__('COOKIEの情報が確認できない場合').'<br> -> '.__('お使いのブラウザにおいてCOOKIEの利用が有効かどうかご確認ください。').'<br>';
		}
		$sKey = Cookie::get("CL_ORG_KEY",false);
		if ($sKey)
		{
			$aKey = unserialize(Crypt::decode($sKey));
			$data['olgn_id'] = (isset($aKey['id']))? $aKey['id']:'';
			$data['olgn_pass'] = (isset($aKey['pass']))? $aKey['pass']:'';
			$data['olgn_chk'] = true;
		}

		$this->template->content = View::forge('org/login',$data);
		if (!is_null($sNC))
		{
			$this->template->content->set('noCookie',$sNC,false);
		}
		return $this->template;
	}

	public function action_loginchk()
	{
		$aInput = Input::post();
		$aInput['olgn_chk'] = (isset($aInput['olgn_chk']))? true:false;

		$val = Validation::forge();
		$val->add_field('olgn_id', __('ログインID'), 'required|max_length[200]');
		$val->add_field('olgn_pass', __('パスワード'), 'required|max_length[200]');
		if (!$val->run())
		{
			$aInput['error'] = $val->error();
			$this->template->content = View::forge('org/login',$aInput);
			return $this->template;
		}

		$result = Model_Group::getGroupAdminFromPostLogin($aInput['olgn_id'],$aInput['olgn_pass']);
		if (!count($result))
		{
			$aInput['error'] = array('login'=> __('ログインIDまたはパスワードが間違っているため、ログインできません。'));
			$this->template->content = View::forge('org/login',$aInput);
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
		if (!$aResult['gaTimeZone'])
		{
			$aResult['gaTimeZone'] = $aInput['ltzone'];
			$result = Model_Group::updateGroupAdmin(array('gaTimeZone'=>$aInput['ltzone']),array(array('gaID','=',$aResult['gaID'])));
		}

		if ($aInput['olgn_chk'])
		{
			Cookie::set("CL_ORG_KEY",Crypt::encode(serialize(array('orgseed'=>mt_rand(),'id'=>$aInput['olgn_id'],'pass'=>$aInput['olgn_pass']))),60*60*24*180);
		}
		else
		{
			Cookie::delete("CL_ORG_KEY");
		}
		Cookie::set("CL_ORG_HASH",Crypt::encode(serialize(array('orgseed'=>mt_rand(),'hash'=>sha1($aResult['gaLogin'].$aResult['gaPass']),'ip'=>Input::real_ip()))));

		if ($aResult['gaFirst'])
		{
			Response::redirect('org/password/first');
		}
		Response::redirect('org/index');
	}
}
