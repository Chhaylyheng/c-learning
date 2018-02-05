<?php
class Controller_A_Basenl extends Controller_Base
{
	public $eRedirect = 'index/error/a';

	public function before()
	{
		parent::before();

		$this->sSiteTitle .= '（'.__('副担当').'）';
		$this->template->set_global('dir', 'a');
		$this->template->set_global('title', $this->sSiteTitle);
		$this->template->footer = View::forge('t/footer');

		\Session::set('CL_AL_LOGIN', true);
	}
}
