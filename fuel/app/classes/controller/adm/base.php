<?php
class Controller_Adm_Base extends Controller_Base
{
	public $template = 'adm/template_logined';
	public $aAdmin = null;
	public $aSes = null;
	public $eRedirect = 'index/error/adm';

	public function before()
	{
		parent::before();

		$this->sSiteTitle .= '（契約管理センター）';
		$this->template->set_global('dir', 'adm');
		$this->template->set_global('title', $this->sSiteTitle);
		$this->template->footer = View::forge('adm/footer');

		$sHash = Cookie::get('CL_ADM_HASH',false);
		if (!$sHash)
		{
			Response::redirect('adm/AdminLogin/index/1');
		}
		$aLogin = unserialize(Crypt::decode($sHash));
/*
		if (Input::real_ip() != $aLogin['ip'])
		{
			Session::set('SES_ADM_ERROR_MSG','ログインした時と異なるネットワーク、または端末からアクセスされた可能性があります。');
			Response::redirect($this->eRedirect);
		}
*/
		$result = Model_Admin::getAdminFromHash($aLogin['hash']);
		if (!count($result))
		{
			Response::redirect('adm/AdminLogin/index/1');
		}
		$this->aAdmin = $result->current();

		$this->aSes = Session::get(null,false);
		$this->template->set_global('ses',$this->aSes);
		$this->template->set_global('aAdmin',$this->aAdmin);

		$this->template->set_global('tz',$this->aAdmin['adTimeZone']);
	}
}