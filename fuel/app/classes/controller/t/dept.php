<?php
class Controller_T_Dept extends Controller_T_Base
{
	private $month = array();
	private $day = array();

	private function LastDay($month = null,$day = null)
	{
		$iSLD = (int)date("t",strtotime(date("Y").'-'.$month.'-01'));
		return ((int)$day > $iSLD)? $iSLD:$day;
	}


	public function before()
	{
		parent::before();

		for ($i = 1; $i <= 12; $i++)
		{
			$this->month[$i] = $i.'月';
		}
		for ($i = 1; $i <= 31; $i++)
		{
			$this->day[$i] = $i.'日';
		}
	}

	public function action_index()
	{
		Response::redirect('index/404','location',404);
	}
	public function action_periodedit()
	{
		$period = null;
		$result = Model_College::getPeriod(array('cmKCode'=>$this->aTeacher['cmKCode'],'dmNO'=>$this->aTeacher['dmNO']));
		if (count($result))
		{
			$aResult = $result->as_array();
			foreach ($aResult as $aR)
			{
				$period[$aR['dpNO']] = $aR;
			}
		}

		if (!Input::post(null,false))
		{
			$data['period'] = $period;
			$data['error'] = null;
			$this->template->content = View::forge('t/dept_periodedit',$data);
			$this->template->content->set('month',$this->month);
			$this->template->content->set('day',$this->day);
			$this->template->funcNav = View::forge('t/dept_func');
			return $this->template;
		}

		$val = Validation::forge();
		for ($i = 1; $i < 10; $i++)
		{
			$period[$i]['dpName'] = Input::post('period_name'.$i);
			$period[$i]['dpStartDate'] = sprintf('%02d',Input::post('period_s_m'.$i)).'-'.sprintf('%02d',$this->LastDay(Input::post('period_s_m'.$i),Input::post('period_s_d'.$i)));
			$period[$i]['dpEndDate'] = sprintf('%02d',Input::post('period_e_m'.$i)).'-'.sprintf('%02d',$this->LastDay(Input::post('period_e_m'.$i),Input::post('period_e_d'.$i)));
			if ($period[$i]['dpName'] == '')
			{
				unset($period[$i]);
			}
			$val->add_field('period_name'.$i, 'No.'.$i, 'max_length[20]');
		}
		if (!$val->run())
		{
			$data['period'] = $period;
			$data['error'] = $val->error();
			$this->template->content = View::forge('t/dept_periodedit',$data);
			$this->template->content->set('month',$this->month);
			$this->template->content->set('day',$this->day);
			$this->template->funcNav = View::forge('t/dept_func');
			return $this->template;
		}

		$aInsert = null;
		$aColumns = array('cmKCode','dmNO','dpNO','dpName','dpStartDate','dpEndDate','dpDate');
		$sDate = date('YmdHis');
		for ($i = 1; $i < 10; $i++)
		{
			if (isset($period[$i]))
			{
				$aInsert[] = array(
					$this->aTeacher['cmKCode'],
					$this->aTeacher['dmNO'],
					$i,
					$period[$i]['dpName'],
					$period[$i]['dpStartDate'],
					$period[$i]['dpEndDate'],
					$sDate,
				);
			}
		}
		try
		{
			$result = Model_College::insertPeriod($this->aTeacher['cmKCode'],$this->aTeacher['dmNO'],$aColumns,$aInsert);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}
		Session::set('SES_T_NOTICE_MSG','学部の期間情報を変更しました。');
		Response::redirect('/t/index');
	}
}