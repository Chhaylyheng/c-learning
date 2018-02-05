<?php
class Controller_T_Login extends Controller_T_Basenl
{
	private $aLoginBase = array(
		'tlgn_mail'=>null,
		'tlgn_pass'=>null,
		'tlgn_chk'=>false,
		// echo json_encode($aLoginBase);
	);

	public function before() {
		parent::before();

		$display = $this->template->set_global('pagetitle',__('先生ログイン'));
		// echo json_encode($display);
	}

	public function action_index($noCookie = null)
	{
		if ($red = Session::get('CL_TL_LOGINMODEL',false))
		{
			$red .= (!is_null($noCookie))? DS.$noCookie:'';
			Session::delete('CL_TL_LOGINMODEL');
			 Response::redirect($red);
			//echo json_encode($red);
		}

		Session::destroy();

		$data = $this->aLoginBase;
		$data['error'] = null;
		$sNC = null;
		if (!is_null($noCookie))
		{
			$sNC  = __('ログイン情報が確認できませんでした。').'<br>'.__('以下の可能性がありますので、ご確認ください。').'<br>';
			$sNC .= ' 1.'.__('ログインしたまま長時間操作していない場合').'<br> -> '.__('再度ログインすることで解決します。').'<br>';
			$sNC .= ' 2.'.__('COOKIEの情報が確認できない場合').'<br> -> '.__('お使いのブラウザにおいてCOOKIEの利用が有効かどうかご確認ください。').'<br>';
		}

		$sKey = Cookie::get("CL_TL_KEY",false);
		if ($sKey)
		{
			$aKey = unserialize(Crypt::decode($sKey));
			$data['tlgn_mail'] = (isset($aKey['mail']))? $aKey['mail']:'';
			$data['tlgn_pass'] = (isset($aKey['pass']))? $aKey['pass']:'';
			$data['tlgn_chk'] = true;
		}

		$this->template->content = View::forge('t/login',$data);
		if (!is_null($sNC))
		{
			$this->template->content->set('noCookie',$sNC,false);
		}
		return $this->template;
		//echo json_encode($this->template);
	}

	public function action_loginchk()
	{
		$aInput = Input::post();
		$aInput['tlgn_chk'] = (isset($aInput['tlgn_chk']))? true:false;

		$val = Validation::forge();
		$val->add_field('tlgn_mail', __('メールアドレス'), 'required|max_length[200]');
		$val->add_field('tlgn_pass', __('パスワード'), 'required|max_length[200]');
		if (!$val->run())
		{
			$aInput['error'] = $val->error();
			$this->template->content = View::forge('t/login',$aInput);
			return $this->template;
		}

		$result = Model_Teacher::getTeacherFromMail($aInput['tlgn_mail']);
		if (count($result))
		{
			$aResult = $result->current();
			$result = Model_Group::getGroupTeachers(array(array('gtp.ttID','=',$aResult['ttID'])));
			if (count($result))
			{
				$aGT = $result->current();
				$result = Model_Group::getGroup(array(array('gb.gtID','=',$aGT['gtID'])));
				if (count($result))
				{
					$aGroup = $result->current();
					if ($aGroup['gtLDAP'])
					{
						\Session::set('SES_T_ERROR_MSG', __('こちらの専用ログイン画面よりログインしてください。'));
						Response::redirect(DS.$aGroup['gtPrefix'].DS.'t');
					}
				}
			}
		}

		$result = Model_Teacher::getTeacherFromPostLogin($aInput['tlgn_mail'],$aInput['tlgn_pass']);
		if (!count($result))
		{
			$aInput['error'] = array('login'=>__('入力されたメールアドレスが存在しないか、パスワードが異なるためログインできません。'));
			$this->template->content = View::forge('t/login',$aInput);
			return $this->template;
		}
		$aResult = $result->current();

		if ($aResult['ttStatus'] == 0)
		{
			$this->template->content = View::forge('t/login',$aInput);
			$this->template->content->set_safe('error',array('login'=>__('このアカウントは虚偽の申請と判断し、停止させていただきました。<br>正しい内容を:sysmailまでご連絡下さい。',array('sysmail'=>'<a href="mailto:'.CL_KEIYAKUMAIL.'">'.CL_KEIYAKUMAIL.'</a>'))));
			return $this->template;
		}
		if ($aResult['ttStatus'] == 2)
		{
			try
			{
				$aUpdate = array(
					'ttStatus' => 1,
				);
				$result = Model_Teacher::updateTeacher($aResult['ttID'], $aUpdate);
			}
			catch (Exception $e)
			{
				\Clfunc_Common::LogOut($e,__CLASS__);
				Session::set('SES_T_ERROR_MSG',$e->getMessage());
				Response::redirect($this->eRedirect);
			}
		}

		if (CL_CAREERTASU_MODE)
		{
			if (strtotime($aResult['ttCTStart']) > strtotime(date('Y/m/d')))
			{
				$aInput['error'] = array('login'=>__('契約開始日以降にログイン可能です。'));
				$this->template->content = View::forge('t/login',$aInput);
				return $this->template;
			}
			if (strtotime($aResult['ttCTEnd']) < strtotime(date('Y/m/d')))
			{
				$aInput['error'] = array('login'=>__('すでに契約が終了しています。'));
				$this->template->content = View::forge('t/login',$aInput);
				return $this->template;
			}
		}

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
		if (!$aResult['ttTimeZone'])
		{
			$aResult['ttTimeZone'] = $aInput['ltzone'];
			$bTZ = true;
		}

		$result = Model_Teacher::setLoginUpdate($aResult,$bTZ);
		if (!count($result))
		{
			$aInput['error'] = array('login'=>__('入力されたメールアドレスが存在しないか、パスワードが異なるためログインできません。'));
			$this->template->content = View::forge('t/login',$aInput);
			return $this->template;
		}
		$aResult = $result->current();

		if ($aInput['tlgn_chk'])
		{
			Cookie::set("CL_TL_KEY",Crypt::encode(serialize(array('tseed'=>mt_rand(),'mail'=>$aInput['tlgn_mail'],'pass'=>$aInput['tlgn_pass']))),60*60*24*180);
		}
		else
		{
			Cookie::delete("CL_TL_KEY");
		}

		Cookie::set("CL_TL_HASH",Crypt::encode(serialize(array('tseed'=>mt_rand(),'hash'=>sha1($aResult['ttMail'].$aResult['ttPass']),'ip'=>Input::real_ip()))));
		Cookie::delete('CL_AL_HASH');

		if ($aResult['ttFirst'])
		{
			Response::redirect('t/password/first');
		}
		Response::redirect('t/index');
	}

	public function action_auth($mode = 'ad', $sys = null, $noCookie = null)
	{
		$sUrl = $sys.DS.'t';
		$this->template->set_global('dir', $sUrl);
		$this->template->content = View::forge('t/login_ad');

		$sNC = null;
		if (!is_null($noCookie))
		{
			$sNC  = __('ログイン情報が確認できませんでした。').'<br>'.__('以下の可能性がありますので、ご確認ください。').'<br>';
			$sNC .= ' 1.'.__('ログインしたまま長時間操作していない場合').'<br> -> '.__('再度ログインすることで解決します。').'<br>';
			$sNC .= ' 2.'.__('COOKIEの情報が確認できない場合').'<br> -> '.__('お使いのブラウザにおいてCOOKIEの利用が有効かどうかご確認ください。').'<br>';
			$this->template->content->set('noCookie',$sNC,false);
		}

		if (is_null($sys))
		{
			Response::redirect('index/404','location',404);
		}
		$result = Model_Group::getGroup(array(array('gb.gtPrefix','=',$sys),array('gb.gtLDAP','=',1)));
		if (!count($result))
		{
			Response::redirect('index/404','location',404);
		}
		$aGroup = $result->current();
		$this->template->content->set('aGroup',$aGroup);


		if (!Input::post(null,false))
		{
			$aInput = array('tlgn_id'=>null,'tlgn_pass'=>null,'error'=>null);
			$this->template->content->set($aInput);
			return $this->template;
		}

		$aInput = Input::post(null,false);
		$val = Validation::forge();
		$val->add_field('tlgn_id', __('ログインID'), 'required');
		$val->add_field('tlgn_pass', __('パスワード'), 'required');
		if (!$val->run())
		{
			$aInput['error'] = $val->error();
			$this->template->content->set($aInput);
			return $this->template;
		}

		try
		{
			\Clfunc_Common::LDAPAuthCommand($aGroup, $aInput['tlgn_id'], $aInput['tlgn_pass']);
		}
		catch (\Exception $e)
		{
			$aErr = explode('|', $e->getMessage());
			$aInput['error']['login'] = $aErr[0];
			$this->template->content->set($aInput);
			return $this->template;
		}

		$result = Model_Group::getGroupTeachers(array(array('gtp.gtID','=',$aGroup['gtID']),array('tv.ttLoginID','=',$aInput['tlgn_id'])));
		if (!count($result))
		{
			$aInput['error']['tlgn_id'] = __(':siteに登録されていないログインIDです。団体管理者にご確認ください。',array('site'=>CL_SITENAME));
			$this->template->content->set($aInput);
			return $this->template;
		}

		$result = Model_Teacher::getTeacherFromLoginID($aInput['tlgn_id']);
		if (!count($result))
		{
			$aInput['error'] = array('login'=>__('ログインに失敗しました。再度実行してみてください。'));
			$this->template->content->set($aInput);
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
		if (!$aResult['ttTimeZone'])
		{
			$aResult['ttTimeZone'] = $aInput['ltzone'];
			$bTZ = true;
		}

		$result = Model_Teacher::setLoginUpdate($aResult,$bTZ);
		if (!count($result))
		{
			$aInput['error'] = array('login'=>__('ログインに失敗しました。再度実行してみてください。'));
			$this->template->content->set($aInput);
			return $this->template;
		}
		$aResult = $result->current();

		Session::set('CL_TL_LOGINMODEL', $sUrl);
		Cookie::set('CL_TL_HASH',Crypt::encode(serialize(array('tseed'=>mt_rand(),'hash'=>sha1($aResult['ttMail'].$aResult['ttPass']),'ip'=>Input::real_ip()))));
		Cookie::delete('CL_AL_HASH');

		Response::redirect('t/index');
	}


	public function action_oauthloginchk($provider)
	{
		$sTZ = Session::get('SES_AUTH_LOGIN_TZ',false);
		Session::delete('SES_AUTH_LOGIN_TZ');
		$sTZ = ($sTZ)? $sTZ:date_default_timezone_get();
		$data = $this->aLoginBase;

		$sUID = Session::get('SES_AUTH_UID',false);
		if (!$sUID)
		{
			Session::set('SES_T_ERROR_MSG',__('ログインできませんでした。'));
			Response::redirect($this->eRedirect);
		}

		$result = Model_Teacher::getTeacherFromSocialID($sUID,$provider);
		if (!count($result))
		{
			$data['error'] = array('login'=>__(':providerアカウントでログインできませんでした。', array('provider'=>$provider)));
			$this->template->content = View::forge('t/login',$data);
			return $this->template;
		}
		$aResult = $result->current();

		if ($aResult['ttStatus'] == 0)
		{
			$this->template->content = View::forge('t/login',$data);
			$this->template->content->set_safe('error',array('login'=>__('このアカウントは虚偽の申請と判断し、停止させていただきました。<br>正しい内容を:sysmailまでご連絡下さい。',array('sysmail'=>'<a href="mailto:'.CL_KEIYAKUMAIL.'">'.CL_KEIYAKUMAIL.'</a>'))));
			return $this->template;
		}
		if ($aResult['ttStatus'] == 2)
		{
			try
			{
				$aUpdate = array(
					'ttStatus' => 1,
				);
				$result = Model_Teacher::updateTeacher($aResult['ttID'], $aUpdate);
			}
			catch (Exception $e)
			{
				\Clfunc_Common::LogOut($e,__CLASS__);
				Session::set('SES_T_ERROR_MSG',$e->getMessage());
				Response::redirect($this->eRedirect);
			}
		}

		if (CL_CAREERTASU_MODE)
		{
			if (strtotime($aResult['ttCTStart']) > strtotime(date('Y/m/d')))
			{
				$data['error'] = array('login'=>__('契約開始日以降にログイン可能です。'));
				$this->template->content = View::forge('t/login',$data);
				return $this->template;
			}
			if (strtotime($aResult['ttCTEnd']) < strtotime(date('Y/m/d')))
			{
				$data['error'] = array('login'=>__('すでに契約が終了しています。'));
				$this->template->content = View::forge('t/login',$data);
				return $this->template;
			}
		}

		// タイムゾーンの取得と確認
		$bTZ = false;
		try
		{
			ClFunc_Tz::tz_chk($sTZ);
		}
		catch (Exception $e)
		{
			$sTZ = date_default_timezone_get();
		}
		if (!$aResult['ttTimeZone'])
		{
			$aResult['ttTimeZone'] = $sTZ;
			$bTZ = true;
		}

		$result = Model_Teacher::setLoginUpdate($aResult,$bTZ);
		if (!count($result))
		{
			$data['error'] = array('login'=>__('入力されたメールアドレスが存在しないか、パスワードが異なるためログインできません。'));
			$this->template->content = View::forge('t/login',$data);
			return $this->template;
		}
		$aResult = $result->current();

		$result = Model_Group::getGroupTeachers(array(array('gtp.ttID','=',$aResult['ttID'])));
		if (count($result))
		{
			$aGT = $result->current();
			$result = Model_Group::getGroup(array(array('gb.gtID','=',$aGT['gtID'])));
			if (count($result))
			{
				$aGroup = $result->current();
				if ($aGroup['gtLDAP'])
				{
					\Session::set('SES_T_ERROR_MSG', __('こちらの専用ログイン画面よりログインしてください。'));
					Response::redirect(DS.$aGroup['gtPrefix'].DS.'t');
				}
			}
		}

		Cookie::set("CL_TL_HASH",Crypt::encode(serialize(array('tseed'=>mt_rand(),'hash'=>sha1($aResult['ttMail'].$aResult['ttPass']),'ip'=>Input::real_ip()))));
		Cookie::delete('CL_AL_HASH');

		Response::redirect('t/index');
	}
	public function action_loginAPI(){

		$input = array();

		$input['tlgn_mail'] = $_POST['tlgn_mail'];
		$input['tlgn_pass'] = $_POST['tlgn_pass'];
		$result = Model_Teacher::getTeacherFromPostLogin($input['tlgn_mail'],$input['tlgn_pass']);
		$aResult = $result->current();
		echo json_encode($aResult);


	}

	public function action_createclassAPI() {

		$input = array();
		$input['ttID'] = $_POST['ttID'];
		$input['cname'] = $_POST['cname'];

		$aInsert['class'] = array(
			'ctID'       => null,
			'ctCode'     => Model_Class::getClassCode(CL_CLASSCODE),
			'ctName'     => $input['cname'],
			'ctYear'     => "2019",
		);

		$aInsert['position'] = array(
			'ctID'     => null,
			'ttID'     => $input['ttID'],
			'tpMsater' => 1,
			'tpDate'   => date('YmdHis'),
		);
		try
		{
			$result = Model_Class::insertClass($aInsert);
			$sCtID = $result;
		}
		catch (Exception $e)
		{
			echo json_encode($e);
		}
		$aResult = $sCtID;
		echo json_encode($aResult);
        
	}
}
