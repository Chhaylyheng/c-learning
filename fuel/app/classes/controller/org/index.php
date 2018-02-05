<?php
class Controller_Org_Index extends Controller_Org_Base
{
	public function action_index()
	{
		$this->template->content = View::forge('org/index');
		return $this->template;
	}

	public function action_logout()
	{
		Cookie::delete('CL_ORG_HASH');
		Response::redirect('org/login');
	}
}