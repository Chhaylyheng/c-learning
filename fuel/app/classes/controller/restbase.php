<?php
class Controller_Restbase extends Controller_Rest
{
	public $sLang = 'ja';
	public $aPeriod = array();
	public $aWeekday = array();
	public $aHour = array();
	public $aSex = array();
	public $tz = null;

	public function before()
	{
		if (Cookie::get('CL_LANG'))
		{
			Config::set('language', Cookie::get('CL_LANG'));
			$this->sLang = Cookie::get('CL_LANG');
		}
		else if (Session::get('CL_LANG'))
		{
			Config::set('language', Session::get('CL_LANG'));
			$this->sLang = Session::get('CL_LANG');
		}
		else
		{
			$lang = Agent::languages();
			if (preg_match('/CL_AIR/i', Input::user_agent()))
			{
				$lang = array('ja');
			}
			$lang = explode('-', $lang[0]);
			$lang = $lang[0];
			if ($lang == 'ja')
			{
				Config::set('language', 'ja');
				$this->sLang = 'ja';
				Cookie::set('CL_LANG','ja');
				Session::set('CL_LANG','ja');
			}
			else
			{
				Config::set('language', 'en');
				$this->sLang = 'en';
				Cookie::set('CL_LANG','en');
				Session::set('CL_LANG','en');
			}
		}

		if ($this->sLang == 'ja' && CL_CORPORATE_MODE)
		{
			Config::set('language', 'cp');
			$this->sLang = 'cp';
			Cookie::set('CL_LANG','cp');
			Session::set('CL_LANG','cp');
		}
		elseif ($this->sLang == 'ja' && CL_CAREERTASU_MODE)
		{
			Config::set('language', 'ct');
			$this->sLang = 'ct';
			Cookie::set('CL_LANG','ct');
			Session::set('CL_LANG','ct');
		}

		Lang::load('i18n');

		parent::before();

		$this->aPeriod = array(
			'0'=>__('指定なし'),
			'1'=>__('前期'),
			'2'=>__('後期'),
			'3'=>__('通期'),
		);
		$this->aWeekday = array(
			0=>__('指定なし'),
			1=>__('月曜'),
			2=>__('火曜'),
			3=>__('水曜'),
			4=>__('木曜'),
			5=>__('金曜'),
			6=>__('土曜'),
			7=>__('日曜')
		);
		$this->aHour = array(
			'0'=>__('指定なし'),
			'1'=>__(':num限',array('num'=>1)),
			'2'=>__(':num限',array('num'=>2)),
			'3'=>__(':num限',array('num'=>3)),
			'4'=>__(':num限',array('num'=>4)),
			'5'=>__(':num限',array('num'=>5)),
			'6'=>__(':num限',array('num'=>6)),
			'7'=>__(':num限',array('num'=>7)),
		);

		$this->aSex = array(
			'0'=>__('指定なし'),
			'1'=>__('男性'),
			'2'=>__('女性'),
		);

		if (CL_MAINTE)
		{
			Response::redirect('mainte');
		}
	}

	public function after($response)
	{
		$response = parent::after($response);

		usleep(80000);

		return $response;
	}

	public function action_languageDetect()
	{
		$res = array('lang'=>'en');
		if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE']))
		{
			$res['lang'] = strtolower(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'],0,2));
		}
		$this->response($res);
		return;
	}


}
