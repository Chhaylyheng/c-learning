<?php
/**
 * Fuel is a fast, lightweight, community driven PHP5 framework.
 *
 * @package    Fuel
 * @version    1.7
 * @author     Fuel Development Team
 * @license    MIT License
 * @copyright  2010 - 2013 Fuel Development Team
 * @link       http://fuelphp.com
 */

/**
 * The Welcome Controller.
 *
 * A basic controller example.  Has examples of how to set the
 * response body and status.
 *
 * @package  app
 * @extends  Controller
 */
class Controller_Index extends Controller_Base
{

	public function before()
	{
		parent::before();

		$this->template->set_global('title', $this->sSiteTitle);
		$this->template->footer = View::forge('t/footer');
	}


	/**
	 * The basic welcome message
	 *
	 * @access  public
	 * @return  Response
	 */
	public function action_index()
	{
		//Response::redirect('http://c-learning.jp/');

	    Response::redirect('t/login');
		//echo json_encode($get);
	}

	/**
	 * The 404 action for the application.
	 *
	 * @access  public
	 * @return  Response
	 */
	public function action_404()
	{
		$this->template->content = View::forge('404');
		return Response::forge($this->template, 404);
	}

	public function action_error($sM = 't')
	{
		$data['admmsg'] = Session::get('SES_ADM_ERROR_MSG');
		$data['tmsg'] = Session::get('SES_T_ERROR_MSG');
		$data['smsg'] = Session::get('SES_S_ERROR_MSG');
		$data['orgmsg'] = Session::get('SES_ORG_ERROR_MSG');
		$data['path'] = $sM;
		Session::delete('SES_ADM_ERROR_MSG');
		Session::delete('SES_T_ERROR_MSG');
		Session::delete('SES_S_ERROR_MSG');
		Session::delete('SES_ORG_ERROR_MSG');

		$this->template->set_global('dir', $sM);
		$this->template->content = View::forge('error',$data);
		$this->template->footer = View::forge('t/footer');
		return $data;
	}
}
