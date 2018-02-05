<?php
class Controller_Org_Base extends Controller_Base
{
	public $template = 'org/template_logined';
	public $aAdmin = null;
	public $aGroup = null;
	public $aSes = null;
	public $eRedirect = 'index/error/org';

	public function before()
	{
		parent::before();

		$this->sSiteTitle .= '（'.__('団体管理').'）';
		$this->template->set_global('dir', 'org');
		$this->template->set_global('title', $this->sSiteTitle);
		$this->template->footer = View::forge('org/footer');

		$sHash = Cookie::get('CL_ORG_HASH',false);
		if (!$sHash)
		{
			Response::redirect('org/login/index/1');
		}
		$aLogin = unserialize(Crypt::decode($sHash));

		$result = Model_Group::getGroupAdminFromHash($aLogin['hash']);
		if (!count($result))
		{
			Response::redirect('org/login/index/1');
		}
		$this->aAdmin = $result->current();

		$result = Model_Group::getGroup(array(array('gb.gtID','=',$this->aAdmin['gtID'])));
		if (!count($result))
		{
			Response::redirect('org/login/index/1');
		}
		$this->aGroup = $result->current();

		$this->aSes = Session::get(null,false);
		$this->template->set_global('ses',$this->aSes);
		$this->template->set_global('aAdmin',$this->aAdmin);
		$this->template->set_global('aGroup',$this->aGroup);

		$this->template->set_global('tz',$this->aAdmin['gaTimeZone']);
	}
}