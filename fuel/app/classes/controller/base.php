<?php
class Controller_Base extends Controller_Template
{
	public $aPeriod = array();
	public $aWeekday = array();
	public $aHour = array();
	public $aSex = array();
	public $tz = null;
	public $aCTPlan = array();
	public $sSiteTitle = CL_SITENAME;
	public $sLang = 'ja';

	public function before()
	{
		usleep(80000);

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
			if ($lang == 'ja' || $lang == '')
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

		$this->template->set_global('sLang',$this->sLang);

		$sVQ = (\Config::get('asset.url') != '/')? \Clfunc_Common::contentsSum('query'):'';
		$this->template->set_global('sVQ',$sVQ);

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

		$this->aCTPlan = array(
			'0'=>__('アンケート'),
			'1'=>__('スタンダード'),
		);

		$this->template->set_global('aPeriod',$this->aPeriod);
		$this->template->set_global('aWeekDay',$this->aWeekday);
		$this->template->set_global('aHour',$this->aHour);
		$this->template->set_global('aSex',$this->aSex);
		$this->template->set_global('aCTPlan',$this->aCTPlan);

		$this->aSes = Session::get(null,false);
		$this->template->set_global('ses',$this->aSes);

		$sLogo = (CL_CAREERTASU_MODE)? 'logo_its':'logo';
		$this->template->set_global('sLogo',$sLogo);

		if (CL_MAINTE)
		{
			Response::redirect('mainte');
		}
	}
}
