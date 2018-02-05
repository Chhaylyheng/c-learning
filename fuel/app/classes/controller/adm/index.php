<?php
class Controller_Adm_Index extends Controller_Adm_Base
{
	public function action_index()
	{
		$this->template->content = View::forge('adm/index');
		return $this->template;
	}

	public function action_logout()
	{
		Cookie::delete('CL_ADM_HASH');
		Response::redirect('adm/AdminLogin');
	}
}