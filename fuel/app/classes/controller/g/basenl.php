<?php
class Controller_G_Basenl extends Controller_Base
{
	public $vDir = 'g';
	public $sesParam = '';
	public $eRedirect = 'index/error/g';

	public function before()
	{
		parent::before();

		if (Clfunc_Mobile::is_mobiledevice())
		{
			Config::set('base_url', 'http://'.CL_DOMAIN.'/');

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
	}
}
