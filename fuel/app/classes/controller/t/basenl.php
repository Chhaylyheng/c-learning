<?php
class Controller_T_Basenl extends Controller_Base
{
	public $eRedirect = 'index/error/t';

	public function before()
	{
		parent::before();

		$this->sSiteTitle .= '（'.__('先生').'）';
		$this->template->set_global('dir', 't');
		$this->template->set_global('title', $this->sSiteTitle);
		$this->template->footer = View::forge('t/footer');

		Session::delete('CL_AL_LOGIN');
	}
}
