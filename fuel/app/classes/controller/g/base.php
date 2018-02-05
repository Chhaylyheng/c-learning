<?php
class Controller_G_Base extends Controller_Base
{
	public $template = 'g/template_logined';
	public $aGuest = null;
	public $aActClass = null;
	public $aClass = null;
	public $iDevice = CL_DEV_PC;
	public $aSes = null;
	public $vDir = 'g';
	public $gSes = null;
	public $pSes = null;
	public $eRedirect = 'index/error/g';
	public $sesParam = '';

	public function before()
	{
		parent::before();

		if (Agent::is_smartphone())
		{
			// スマートフォン
			$this->iDevice = CL_DEV_SP;
		}
		elseif (Clfunc_Mobile::is_mobiledevice())
		{
			Config::set('base_url', 'http://'.CL_DOMAIN.'/');

			$this->iDevice = CL_DEV_MB;
			$this->vDir = 'gm';
			$this->template = View::forge($this->vDir.DS.'template.php');
			if (Cookie::get('CL_COOKIE_CHK',false) === false)
			{
				ini_set('session.use_cookies', 0);
				ini_set('session.use_only_cookies', 0);
				ini_set('session.use_trans_sid', 1);

				$sessionid = Crypt::encode(serialize(array(Session::key())));
				$this->sesParam = '?'.Config::get('session.file.cookie_name').'='.$sessionid;
			}
		}

		$this->sSiteTitle .= '（'.__('ゲスト').'）';
		$this->template->set_global('dir', 'g');
		$this->template->set_global('title', $this->sSiteTitle);
		$this->template->footer = View::forge($this->vDir.DS.'footer');

		if (Cookie::get('CL_COOKIE_CHK',false))
		{
			Cookie::set('CL_COOKIE_CHK','cookie_enable');
			$sHash = Cookie::get('CL_GL_HASH',false);
			$this->sTZ = Cookie::get('CL_GL_TZ',date_default_timezone_get());
		}
		else
		{
			$sHash = Session::get('CL_GL_HASH',false);
			$this->sTZ = Session::get('CL_GL_TZ',date_default_timezone_get());
		}
		if (!$sHash)
		{
			Response::redirect('g/login/index/1');
		}
		$aLogin = unserialize(Crypt::decode($sHash));

		$result = Model_Guest::getGuestFromID($aLogin['id']);
		if (count($result)) {
			$this->aGuest = $result->current();
		}

		$result = Model_Class::getClassFromID($aLogin['ct'],1);
		if (count($result)) {
			$this->aClass = $result->current();
		}

		$this->aSes = Session::get(null,false);
		$this->template->set_global('ses',$this->aSes);
		$this->template->set_global('aGuest',$this->aGuest);
		$this->template->set_global('aClass',$this->aClass);
		$this->template->set_global('iDevice',$this->iDevice);
		$this->template->set_global('tz',$this->sTZ);
	}
}