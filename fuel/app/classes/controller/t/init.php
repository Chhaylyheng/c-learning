<?php
class Controller_T_Init extends Controller_T_Basenl
{
	public $aTeacher = null;
	public $aGroup = null;
	private $aClassBase = array(
		'c_name'=>null,
		'c_year'=>null,
		'c_period'=>null,
		'c_weekday'=>null,
		'c_hour'=>null,
	);
	private $aClassFlag;

	public function before()
	{
		parent::before();
		$sTtID = Cookie::get('CL_INIT_TID', false);
		if (!$sTtID)
		{
			Response::redirect('t/login/index/1');
		}
		$result = Model_Teacher::getTeacherFromID($sTtID);
		if (!count($result))
		{
			Response::redirect('t/login/index/1');
		}
		$this->aTeacher = $result->current();
		$this->template->set_global('aTeacher',$this->aTeacher);

		if ($this->aTeacher['gtID'])
		{
			$result = Model_Group::getGroup(array(array('gb.gtID','=',$this->aTeacher['gtID'])));
			if (count($result)) {
				$this->aGroup = $result->current();
			}
		}

		if ($this->aTeacher['ttStatus'] <= 2)
		{
			Response::redirect('t/index');
		}
	}

	public function action_profile()
	{
		# タイムゾーンの取得
		$tz_list = ClFunc_Tz::tz_list();
		$tz_region = ClFunc_Tz::$regions;
		$this->template->set_global('tz_list',$tz_list);
		$this->template->set_global('tz_region',$tz_region);

		if (!Input::post(null,false))
		{
			$data = array(
				'tent_name' => $this->aTeacher['ttName'],
				'tent_tel' => $this->aTeacher['ttSTel'],
				'tent_school' => $this->aTeacher['cmName'],
				'tent_dept' => $this->aTeacher['ttDept'],
				'tent_subject' => $this->aTeacher['ttSubject'],
				'tent_telsupport' => $this->aTeacher['ttTelSupport'],
				'tent_timezone' => $this->aTeacher['ttTimeZone'],
				'error' => null,
			);

			$this->template->content = View::forge('t/init/profile',$data);
			$this->template->javascript = array('cl.school_select.js');
			return $this->template;
		}

		$aInput = Input::post();
		$val = Validation::forge();
		$val->add_callable('Helper_CustomValidation');

		$val->add('tent_pass', __('パスワード'))
			->add_rule('required')
			->add_rule('min_length', 8)
			->add_rule('max_length', 32)
			->add_rule('passwd_char');

		$val->add('tent_passchk', __('パスワード（確認）'))
			->add_rule('required')
			->add_rule('match_field', 'tent_pass');

		$val->add('tent_name', __('氏名'))
			->add_rule('required')
			->add_rule('trim')
			->add_rule('max_length',50);

		if (!CL_CAREERTASU_MODE)
		{
			if (isset($aInput['tent_tel']))
			{
				$val->add('tent_tel', __('電話番号'))
					->add_rule('required')
					->add_rule('trim')
					->add_rule('match_pattern','/^[\+]?[0-9]{9,14}$/',__('数値で10～15文字'));
			}
			if (isset($aInput['tent_school']))
			{
				$val->add('tent_school', __('所属学校名'))
					->add_rule('required')
					->add_rule('trim')
					->add_rule('max_length',50);
			}
			if (isset($aInput['tent_dept']))
			{
				$val->add('tent_dept', __('学部名'))
					->add_rule('trim')
					->add_rule('max_length',50);
			}
			if (isset($aInput['tent_subject']))
			{
				$val->add('tent_subject', __('学科名'))
					->add_rule('trim')
					->add_rule('max_length',50);
			}
			if (isset($aInput['tent_telsupport']))
			{
				$val->add('tent_telsupport', __('電話による初期サポート'))
					->add_rule('required');
			}
		}
		if (!$val->run())
		{
			$aInput['error'] = $val->error();
			$this->template->content = View::forge('t/init/profile',$aInput);
			$this->template->javascript = array('cl.school_select.js');
			return $this->template;
		}

		$sCmKCode = '';

		if (isset($aInput['tent_school']))
		{
			$result = Model_College::getCollegeFromName($aInput['tent_school']);
			$row = $result->current();
			if (!empty($row))
			{
				$sCmKCode = $row['cmKCode'];
			}
			else
			{
				$sCmKCode = Model_College::setCollege($aInput['tent_school']);
			}
		}

		// 登録データ生成
		$aUpdate = array(
			'ttName'  => $aInput['tent_name'],
			'ttPass'  => sha1($aInput['tent_pass']),
			'ttPassDate' => date('Ymd'),
			'ttPassMiss' => 0,
			'ttHash' => sha1($this->aTeacher['ttMail'].sha1($aInput['tent_pass'])),
			'cmKCode' => $sCmKCode,
			'ttDept' => (isset($aInput['tent_dept']))? $aInput['tent_dept']:'',
			'ttSubject' => (isset($aInput['tent_subject']))? $aInput['tent_subject']:'',
			'ttSTel'  => (isset($aInput['tent_tel']))? $aInput['tent_tel']:'',
			'ttTelSupport' => (isset($aInput['tent_telsupport']))? (int)$aInput['tent_telsupport']:0,
			'ttProgress' => 2,
			'ttTimeZone' => $aInput['tent_timezone'],
		);

		try
		{
			$result = Model_Teacher::updateTeacher($this->aTeacher['ttID'], $aUpdate);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

//		Response::redirect('t/init/student');
		Response::redirect('t/init/classset');
	}

	public function action_classset()
	{
		$aClass = null;
		if (Cookie::get('CL_INIT_CLASSID_'.$this->aTeacher['ttID'],false))
		{
			$sCtID = Cookie::get('CL_INIT_CLASSID_'.$this->aTeacher['ttID'],false);
			$result = Model_Class::getClassFromID($sCtID);
			if (!count($result))
			{
				Cookie::delete('CL_INIT_CLASSID_'.$this->aTeacher['ttID']);
				$sCtID = null;
			}
			else
			{
				$aClass = $result->current();
			}
		}
		else
		{
			$result = Model_Class::getClassFromTeacher($this->aTeacher['ttID']);
			if (!count($result))
			{
				Cookie::delete('CL_INIT_CLASSID_'.$this->aTeacher['ttID']);
				$sCtID = null;
			}
			else
			{
				$aClass = $result->current();
			}
		}

		# 選択系のマスタ設定
		$this->aClassFlag = \Clfunc_Flag::getClassFlag();

		$aWeek = $this->aWeekday;
		$aWeek[0] = __('指定なし');
		ksort($aWeek);

		$iY = (date('n') <= 3)? date('Y',strtotime('-1 year')):date('Y');
		if (!is_null($aClass))
		{
			$iY = ((int)$aClass['ctYear'] > $iY)? $iY:(int)$aClass['ctYear'];
		}
		$aYear = Clfunc_Common::YearList($iY);

		$this->template->set_global('aClassFlag', $this->aClassFlag);
		$this->template->set_global('yearlist',$aYear);
		$this->template->set_global('periodlist',$this->aPeriod);
		$this->template->set_global('weekdaylist',$aWeek);
		$this->template->set_global('hourlist',$this->aHour);

		if (!Input::post(null,false))
		{
			$data = $this->aClassBase;
			$data['c_name'] = __('サンプル講義');
			$data['C_FUNC'] = \Clfunc_Common::dec2Bits(4095);
			$data['S_GET']  = \Clfunc_Common::dec2Bits(0);

			if (!is_null($aClass))
			{
				$data['c_name'] = $aClass['ctName'];
				$data['c_year'] = $aClass['ctYear'];
				$data['c_period'] = $aClass['dpNO'];
				$data['c_weekday'] = $aClass['ctWeekDay'];
				$data['c_hour'] = $aClass['dhNO'];
				$data['C_FUNC'] = \Clfunc_Common::dec2Bits((int)$aClass['ctFunctionFlag']);
				$data['S_GET']  = \Clfunc_Common::dec2Bits((int)$aClass['ctStudentGetFlag']);
			}
			$data['error'] = null;

			$this->template->content = View::forge('t/init/classset',$data);
			$this->template->javascript = array('cl.t.class.js');
			return $this->template;
		}

		$aInput = Input::post();
		if (isset($aInput['back']))
		{
			Response::redirect('t/init/profile');
		}

		$val = Validation::forge();
		$val->add_callable('Helper_CustomValidation');

		$val->add('c_name', __('講義名'))
			->add_rule('required')
			->add_rule('trim')
			->add_rule('max_length',30);

		if (!$val->run())
		{
			$data = $aInput;
			$data['error'] = $val->error();
			$this->template->content = View::forge('t/init/classset',$data);
			$this->template->javascript = array('cl.t.class.js');
			return $this->template;
		}

		$iFunc = 0;
		if (isset($aInput['C_FUNC']))
		{
			foreach ($aInput['C_FUNC'] as $iV)
			{
				$iFunc = ($iFunc | (int)$iV);
			}
		}
		$iSGet = 0;
		if (isset($aInput['S_GET']))
		{
			foreach ($aInput['S_GET'] as $iV)
			{
				$iSGet = ($iSGet | (int)$iV);
			}
		}

		try
		{
			if (is_null($aClass))
			{
				// 登録データ生成
				$aInsert['class'] = array(
					'ctID'             => null,
					'ctCode'           => Model_Class::getClassCode(6,$this->aGroup['gtPrefix']),
					'ctName'           => $aInput['c_name'],
					'cmKCode'          => $this->aTeacher['cmKCode'],
					'ctYear'           => $aInput['c_year'],
					'dpNO'             => $aInput['c_period'],
					'ctWeekday'        => $aInput['c_weekday'],
					'dhNO'             => $aInput['c_hour'],
					'ctStatus'         => 1,
					'ctFunctionFlag'   => $iFunc,
					'ctStudentGetFlag' => $iSGet,
					'ctDate'           => date('YmdHis'),
				);
				$aInsert['position'] = array(
					'ctID'     => null,
					'ttID'     => $this->aTeacher['ttID'],
					'tpMaster' => 1,
					'tpDate'   => date('YmdHis'),
				);
				$sCtID = Model_Class::insertClass($aInsert);
				$result = Model_Class::resetTeacherClassSort(array($this->aTeacher['ttID']));
				Cookie::set('CL_INIT_CLASSID_'.$this->aTeacher['ttID'],$sCtID);
			}
			else
			{
				// 登録データ生成
				$aInsert = array(
					'ctName'           => $aInput['c_name'],
					'cmKCode'          => $this->aTeacher['cmKCode'],
					'ctYear'           => $aInput['c_year'],
					'dpNO'             => $aInput['c_period'],
					'ctWeekday'        => $aInput['c_weekday'],
					'dhNO'             => $aInput['c_hour'],
					'ctFunctionFlag'   => $iFunc,
					'ctStudentGetFlag' => $iSGet,
					'ctDate'           => date('YmdHis'),
				);
				$result = Model_Class::updateClass($aInsert,$aClass);
				Cookie::set('CL_INIT_CLASSID_'.$this->aTeacher['ttID'],$aClass['ctID']);
			}
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		Response::redirect('t/init/student');
	}

	public function action_student()
	{
		$aClass = null;
		if (!Cookie::get('CL_INIT_CLASSID_'.$this->aTeacher['ttID'],false))
		{
			Session::set('SES_T_ERROR_MSG',__('講義の情報が確認できませんでした。'));
			Response::redirect('/t/init/classset');
		}
		$sCtID = Cookie::get('CL_INIT_CLASSID_'.$this->aTeacher['ttID'],false);
		$result = Model_Class::getClassFromID($sCtID);
		if (!count($result))
		{
			Session::set('SES_T_ERROR_MSG',__('講義の情報が確認できませんでした。'));
			Response::redirect('/t/init/classset');
		}
		$aClass = $result->current();

		if (!Input::post(null,false))
		{
			$this->template->content = View::forge('t/init/student');
			$this->template->content->set('aClass',$aClass);
			return $this->template;
		}

		$aInput = Input::post();
		if (isset($aInput['back']))
		{
			Response::redirect('t/init/classset');
		}

		try
		{
			$aUpdate = array(
				'ttStatus' => 1,
				'ttProgress' => 4,
			);
			$result = Model_Teacher::updateTeacher($this->aTeacher['ttID'], $aUpdate);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		if (Cookie::get('CL_TE_SOCIAL_'.$this->aTeacher['ttID'], false))
		{
			$this->aTeacher['provider'] = Cookie::get('CL_TE_SOCIAL_'.$this->aTeacher['ttID'], false);

			// 登録完了メール送信
			$email = \Email::forge();
			$email->from(CL_MAIL_FROM, CL_MAIL_SENDER);
			$email->to($this->aTeacher['ttMail']);
			$email->bcc(array(CL_KEIYAKUMAIL,CL_MIYATAMAIL));
			$email->subject('[CL]先生アカウント登録手続き完了のお知らせ');

			$email->attach(DOCROOT.Asset::find_file(CL_PDF_PREFIX.'_student_entry_manual.pdf', 'docs'));

			$html_body = View::forge('email/t_social_fin_html');
			$html_body->set('aTeacher', $this->aTeacher);
			$html_body->set('aClass', $aClass);
			$email->html_body($html_body);

			$body = View::forge('email/t_social_fin_plain');
			$body->set('aTeacher', $this->aTeacher);
			$body->set('aClass', $aClass);
			$email->alt_body($body);

			try
			{
				$email->send();
			}
			catch (\EmailValidationFailedException $e)
			{
				Log::warning('TeacherSocialRegistFinishMail - ' . $e->getMessage());
			}
			catch (\EmailSendingFailedException $e)
			{
				Log::warning('TeacherSocialRegistFinishMail - ' . $e->getMessage());
			}
		}
		else
		{
			// 登録完了メール送信
			$email = \Email::forge();
			$email->from(CL_MAIL_FROM, CL_MAIL_SENDER);
			$email->to($this->aTeacher['ttMail']);
			$email->bcc(array(CL_KEIYAKUMAIL,CL_MIYATAMAIL));
			$email->subject('[CL]'.__('先生アカウント登録手続き完了のお知らせ'));

			$email->attach(DOCROOT.Asset::find_file(CL_PDF_PREFIX.'_student_entry_manual.pdf', 'docs'));

			$html_body = View::forge('email/t_fin_html');
			$html_body->set('aTeacher', $this->aTeacher);
			$html_body->set('aClass', $aClass);
			$email->html_body($html_body);

			$body = View::forge('email/t_fin_plain');
			$body->set('aTeacher', $this->aTeacher);
			$body->set('aClass', $aClass);
			$email->alt_body($body);

			try
			{
				$email->send();
			}
			catch (\EmailValidationFailedException $e)
			{
				Log::warning('TeacherRegistFinishMail - ' . $e->getMessage());
			}
			catch (\EmailSendingFailedException $e)
			{
				Log::warning('TeacherRegistFinishMail - ' . $e->getMessage());
			}
		}

		Cookie::set("CL_TL_HASH",Crypt::encode(serialize(array('hash'=>sha1($this->aTeacher['ttMail'].$this->aTeacher['ttPass']),'ip'=>Input::real_ip()))));
		Cookie::delete('CL_TE_SOCIAL_'.$this->aTeacher['ttID']);
		Cookie::delete('CL_INIT_CLASSID_'.$this->aTeacher['ttID']);
		Response::redirect('/t/index');
	}
}


