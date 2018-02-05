<?php
class Controller_Mainte extends Controller
{
	public function action_index()
	{
		if (!CL_MAINTE)
		{
			Response::redirect('index/404','location',404);
		}

		$view = View::forge('mainte');
		$view->set_safe('msg',CL_MAINTE_MSG);
		return Response::forge($view);
	}

	public function action_preview()
	{
		if (CL_MAINTE)
		{
			Response::redirect('mainte','location');
		}

		$view = View::forge('mainte');
		$view->set_safe('msg',CL_MAINTE_MSG);
		return Response::forge($view);
	}
}
