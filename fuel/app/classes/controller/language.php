<?php
class Controller_Language extends Controller_Base
{
	public function action_select($sLang = null)
	{
		$sBack = Input::referrer();

		switch ($sLang)
		{
			case 'ja':
				Cookie::set('CL_LANG','ja');
				Session::set('CL_LANG','ja');
				if (CL_CORPORATE_MODE)
				{
					Cookie::set('CL_LANG','cp');
					Session::set('CL_LANG','cp');
				}
				elseif (CL_CAREERTASU_MODE)
				{
					Cookie::set('CL_LANG','ct');
					Session::set('CL_LANG','ct');
				}
			break;
			default:
				Cookie::set('CL_LANG','en');
				Session::set('CL_LANG','en');
			break;
		}
		Response::redirect($sBack);
	}
}
