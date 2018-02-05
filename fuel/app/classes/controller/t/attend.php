<?php
class Controller_T_Attend extends Controller_T_Baseclass
{
	public function before()
	{
		parent::before();
		\Clfunc_Common::ContractDetect($this->aTeacher, __CLASS__);
	}

	public function action_index()
	{
		$aAttendMaster = null;
		$result = Model_Attend::getAttendMasterFromClass($this->aClass['ctID']);
		if (count($result))
		{
			$aAttendMaster = $result->as_array();
		}

		$aAttendList = null;
		$result = Model_Attend::getAttendCalendarFromClass($this->aClass['ctID'],array(array('ac.acAStart','=',CL_DATETIME_DEFAULT),array('ac.abDate','<=',date('Y-m-d'))),null,'desc');
		if (count($result))
		{
			$aAttendList = $result->as_array();
		}
		$aStudent = null;
		$aAttend = null;
		$result = Model_Student::getStudentFromClass($this->aClass['ctID'],null,null,array('st.stNO'=>'asc'));
		if (count($result))
		{
			$aRes = $result->as_array();
			foreach ($aRes as $aR)
			{
				$aStudent[$aR['stID']] = $aR;
			}
		}
		$result = Model_Attend::getAttendBookFromClass($this->aClass['ctID'],array(array('abDate','<=',date('Y-m-d'))),null,'desc');
		if (count($result))
		{
			$aRes = $result->as_array();
			foreach ($aRes as $aR)
			{
				if (isset($aStudent[$aR['stID']]))
				{
					$aStudent[$aR['stID']]["attend"][$aR["abDate"]][$aR["acNO"]] = $aR;
				}
			}
		}

		$aActive = null;
		$result = Model_Attend::getAttendCalendarActive($this->aClass['ctID']);
		if (count($result))
		{
			$aRes = $result->as_array();
			$aActive = $aRes[0];
		}

		if ($this->aClass['ctLatLon'])
		{
			$aLatLon = array(
				'lat'=>$this->aClass['ctLat'],
				'lon'=>$this->aClass['ctLon'],
			);
		}
		else
		{
			$sAdrs = ($this->aClass['cmAddress'])? $this->aClass['cmAddress']:$this->aClass['cmPref'].$this->aClass['cmCity'];
			$aLatLon = Clfunc_Common::getGeocoding($sAdrs);
		}

		# タイトル
		$sTitle = __('出席管理');
		$this->template->set_global('pagetitle',$sTitle);

		# パンくずリスト生成
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		# カスタムメニュー
		$aCustomMenu = array(
			array(
				'url'  => '/t/attend/csv/',
				'name' => __('CSVで一括予約'),
				'show' => 1,
			),
			array(
				'url'  => '/t/output/attendtable.csv',
				'name' => __('出席表のダウンロード'),
				'icon' => 'fa-download',
				'show' => 0,
			),
			array(
				'url'  => '/t/output/attendlist.csv',
				'name' => __('出席一覧のダウンロード'),
				'icon' => 'fa-download',
				'show' => 0,
			),
			array(
				'url'  => '/t/attend/editMaster/',
				'name' => __('出席項目の設定'),
				'show' => 1,
			),
		);
		$this->template->set_global('aCustomMenu',$aCustomMenu);

		$aCustomBtn = array(
			array(
				'url'  => '/t/attend/reserve/',
				'name' => __('出席予約の確認/追加'),
				'show' => 1,
			),
		);
		$this->template->set_global('aCustomBtn',$aCustomBtn);

		$this->template->content = View::forge('t/attend/index');
		$this->template->content->set('sTitle',$sTitle);
		$this->template->content->set('aActive',$aActive);
		$this->template->content->set('aLatLon',$aLatLon);
		$this->template->content->set('aAttendList',$aAttendList);
		$this->template->content->set('aAttendMaster',$aAttendMaster);
		$this->template->content->set('aStudent',$aStudent);
		$this->template->javascript = array('jquery.timepicker.js','cl.t.attend.js');
		return $this->template;
	}

	public function post_start()
	{
		if (!Input::post(null,false))
		{
			Session::set('SES_T_ERROR_MSG',__('出席情報が送信されていません。'));
			Response::redirect('/t/attend');
		}
		$aInput = Input::post();
		$val = Validation::forge();
		$val->add_callable('Helper_CustomValidation');
		$val->add_field('e_time', __('終了時刻'), 'required|time')
			->add_rule('min_time',date('H:i'))
			->add_rule('now_attend',array($this->aClass['ctID']));
		$val->add_field('keycode', __('確認キー'), 'exact_length[4]')
			->add_rule('valid_string',array('alpha','numeric'));
		if (!$val->run())
		{
			Session::set('SES_T_ERROR_MSG',implode("\n",$val->error()));
			Response::redirect('/t/attend');
		}
		// 登録データ生成
		$aInsert = array(
			'ctID'       => $this->aClass['ctID'],
			'abDate'     => date('Y-m-d'),
			'acKey'      => $aInput['keycode'],
			'acGIS'      => (int)Input::post('geochk',0),
			'acAStart'   => CL_DATETIME_DEFAULT,
			'acAEnd'     => date('Y-m-d '.$aInput['e_time'].':00'),
			'acStart'    => date('Y-m-d H:i:s'),
			'acEnd'      => CL_DATETIME_DEFAULT,
			'acDate'     => date('Y-m-d H-i-s'),
		);
		$aLatLon = null;
		if ($aInsert['acGIS'])
		{
			$aLatLon = array(
				'lat' => $aInput['lat'],
				'lon' => $aInput['lon'],
			);
		}

		try
		{
			$result = Model_Attend::insertAttend($aInsert,$aLatLon);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		Session::set('SES_T_NOTICE_MSG',__('出席受付を開始しました。'));

		Response::redirect('/t/attend');
	}

	public function action_reserve()
	{
		$aAttendList = null;
		$result = Model_Attend::getAttendCalendarFromClass($this->aClass['ctID'],array(array('ac.acAStart','!=',CL_DATETIME_DEFAULT)));
		if (count($result))
		{
			$aAttendList = $result->as_array();
		}

		# タイトル
		$sTitle = __('出席予約の確認/追加');

		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/attend','name'=>__('出席管理'));
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);
		$this->template->set_global('pagetitle',$sTitle);

		# カスタムメニュー
		$aCustomMenu = array(
			array(
				'url'  => '/t/attend/csv/',
				'name' => __('CSVで一括予約'),
				'show' => 1,
			),
			array(
				'url'  => '/t/output/attendtable.csv',
				'name' => __('出席表のダウンロード'),
				'icon' => 'fa-download',
				'show' => 0,
			),
			array(
				'url'  => '/t/output/attendlist.csv',
				'name' => __('出席一覧のダウンロード'),
				'icon' => 'fa-download',
				'show' => 0,
			),
			array(
				'url'  => '/t/attend/editMaster/',
				'name' => __('出席項目の設定'),
				'show' => 1,
			),
		);
		$this->template->set_global('aCustomMenu',$aCustomMenu);

		if (!Input::post(null,false))
		{
			$data = array(
				'date'    => ClFunc_Tz::tz('Y/m/d',$this->tz),
				's_time'  => ClFunc_Tz::tz('H:i',$this->tz,date('Y-m-d '.Clfunc_Common::endTime(5).':00')),
				'e_time'  => ClFunc_Tz::tz('H:i',$this->tz,date('Y-m-d '.Clfunc_Common::endTime(95).':00')),
				'keycode' => '',
				'geochk'  => 0,
				'error'   => null,
			);
			if ($this->aClass['ctLatLon'])
			{
				$data['latlon'] = array(
					'lat'=>$this->aClass['ctLat'],
					'lon'=>$this->aClass['ctLon'],
				);
			}
			else
			{
				$sAdrs = ($this->aClass['cmAddress'])? $this->aClass['cmAddress']:$this->aClass['cmPref'].$this->aClass['cmCity'];
				$data['latlon'] = Clfunc_Common::getGeocoding($sAdrs);
			}

			$this->template->content = View::forge('t/attend/reserve',$data);
			$this->template->content->set('no',0);
			$this->template->content->set('aAttendList',$aAttendList);
			$this->template->javascript = array('jquery.timepicker.js','cl.t.attend.js');
			return $this->template;
		}

		$aInput = Input::post();
		$sStart = $aInput['date'].' '.$aInput['s_time'].':00';
		$val = Validation::forge();
		$val->add_callable('Helper_CustomValidation');
		$val->add_field('date', __('予約日付'), 'required|date')
			->add_rule('min_date',array(ClFunc_Tz::tz('Y/m/d',$this->tz)));
		$val->add_field('s_time', __('開始時刻'), 'required|time')
			->add_rule('min_time',ClFunc_Tz::tz('Y/m/d H:i:00',$this->tz),$aInput['date']);
		$val->add_field('e_time', __('終了時刻'), 'required|time')
			->add_rule('min_time',$sStart,$aInput['date'])
			->add_rule('exists_attend',array($this->aClass['ctID'],ClFunc_Tz::tz('Y-m-d',null,$sStart,$this->tz),ClFunc_Tz::tz('H:i',null,$sStart,$this->tz)));
		$val->add_field('keycode', __('確認キー'), 'exact_length[4]')
			->add_rule('valid_string',array('alpha','numeric'));
		if (!$val->run())
		{
			$data = $aInput;
			$data['latlon'] = array(
				'lat' => $aInput['lat'],
				'lon' => $aInput['lon'],
			);
			$data['error'] = $val->error();
			$data['geochk'] = (isset($data['geochk']))? $data['geochk']:0;

			$this->template->content = View::forge('t/attend/reserve',$data);
			$this->template->content->set('no',0);
			$this->template->content->set('aAttendList',$aAttendList);
			$this->template->javascript = array('jquery.timepicker.js','cl.t.attend.js');
			return $this->template;
		}

		$sDate = ClFunc_Tz::tz('Y-m-d',null,$aInput['date'].' '.$aInput['s_time'].':00',$this->tz);
		// 登録データ生成
		$aInsert = array(
			'ctID'       => $this->aClass['ctID'],
			'abDate'     => $sDate,
			'acKey'      => $aInput['keycode'],
			'acGIS'      => (int)Input::post('geochk',0),
			'acAStart'   => ClFunc_Tz::tz('Y-m-d H:i:00',null,$aInput['date'].' '.$aInput['s_time'].':00',$this->tz),
			'acAEnd'     => ClFunc_Tz::tz('Y-m-d H:i:00',null,$aInput['date'].' '.$aInput['e_time'].':00',$this->tz),
			'acStart'    => CL_DATETIME_DEFAULT,
			'acEnd'      => CL_DATETIME_DEFAULT,
			'acDate'     => date('Y-m-d H-i-s'),
		);
		$aLatLon = null;
		if ($aInsert['acGIS'])
		{
			$aLatLon = array(
				'lat' => $aInput['lat'],
				'lon' => $aInput['lon'],
			);
		}

		try
		{
			$result = Model_Attend::insertAttend($aInsert,$aLatLon);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		Session::set('SES_T_NOTICE_MSG',__('出席予約を登録しました。'));

		Response::redirect('/t/attend/reserve');
	}

	public function action_edit($iNO = null)
	{
		if (is_null($iNO))
		{
			Session::set('SES_T_ERROR_MSG',__('出席情報が送信されていません。'));
			Response::redirect('/t/attend');
		}
		$result = Model_Attend::getAttendCalendarFromClass($this->aClass['ctID'],array(array('ac.no','=',$iNO),array('ac.acAStart','!=',CL_DATETIME_DEFAULT)));
		if (!count($result))
		{
			Session::set('SES_T_ERROR_MSG',__('指定された出席情報が見つかりません。'));
			Response::redirect('/t/attend');
		}
		$aActive = $result->current();

		$aAttendList = null;
		$result = Model_Attend::getAttendCalendarFromClass($this->aClass['ctID'],array(array('ac.acAStart','!=',CL_DATETIME_DEFAULT)));
		if (count($result))
		{
			$aAttendList = $result->as_array();
		}
		$this->template->set_global('aAttendList',$aAttendList);

		# タイトル
		$sTitle = __('出席予約の編集');

		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/attend','name'=>__('出席管理'));
		$this->aBread[] = array('link'=>'/attend/reserve','name'=>__('出席予約の確認/追加'));
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);
		$this->template->set_global('pagetitle',$sTitle);

		if (!Input::post(null,false))
		{
			$data = array(
				'date'    => ClFunc_Tz::tz('Y/m/d',$this->tz,$aActive['acAStart']),
				's_time'  => ClFunc_Tz::tz('H:i',$this->tz,$aActive['acAStart']),
				'e_time'  => ClFunc_Tz::tz('H:i',$this->tz,$aActive['acAEnd']),
				'keycode' => $aActive['acKey'],
				'geochk'  => $aActive['acGIS'],
				'error'   => null,
			);
			if ($aActive['agLatLon'])
			{
				$data['latlon'] = array(
						'lat'=>$aActive['agLat'],
						'lon'=>$aActive['agLon'],
				);
			}
			else if ($this->aClass['ctLatLon'])
			{
				$data['latlon'] = array(
					'lat'=>$this->aClass['ctLat'],
					'lon'=>$this->aClass['ctLon'],
				);
			}
			else
			{
				$sAdrs = ($this->aClass['cmAddress'])? $this->aClass['cmAddress']:$this->aClass['cmPref'].$this->aClass['cmCity'];
				$data['latlon'] = Clfunc_Common::getGeocoding($sAdrs);
			}

			$this->template->content = View::forge('t/attend/reserve',$data);
			$this->template->content->set('no',$iNO);
			$this->template->javascript = array('jquery.timepicker.js','cl.t.attend.js');
			return $this->template;
		}

		$aInput = Input::post();
		$aInput['date'] = ClFunc_Tz::tz('Y/m/d',$this->tz,$aActive['acAStart']);
		$sStart = $aInput['date'].' '.$aInput['s_time'].':00';
		$val = Validation::forge();
		$val->add_callable('Helper_CustomValidation');
		$val->add_field('s_time', __('開始時刻'), 'required|time')
			->add_rule('min_time',ClFunc_Tz::tz('Y/m/d H:i',$this->tz),$aInput['date']);
		$val->add_field('e_time', __('終了時刻'), 'required|time')
			->add_rule('min_time',$sStart,$aInput['date'])
			->add_rule('exists_attend',array($this->aClass['ctID'],ClFunc_Tz::tz('Y-m-d',null,$sStart,$this->tz),ClFunc_Tz::tz('H:i',null,$sStart,$this->tz),$iNO));
		$val->add_field('keycode', __('確認キー'), 'exact_length[4]')
			->add_rule('valid_string',array('alpha','numeric'));
		if (!$val->run())
		{
			$data = $aInput;
			$data['latlon'] = array(
				'lat' => $aInput['lat'],
				'lon' => $aInput['lon'],
			);
			$data['error'] = $val->error();
			$data['geochk'] = (isset($data['geochk']))? $data['geochk']:0;

			$this->template->content = View::forge('t/attend/reserve',$data);
			$this->template->content->set('no',$iNO);
			$this->template->javascript = array('jquery.timepicker.js','cl.t.attend.js');
			return $this->template;
		}

		$sDate = ClFunc_Tz::tz('Y-m-d',null,$aInput['date'].' '.$aInput['s_time'].':00',$this->tz);
		// 登録データ生成
		$aUpdate = array(
			'abDate'     => $sDate,
			'acKey'      => $aInput['keycode'],
			'acGIS'      => (int)Input::post('geochk',0),
			'acAStart'   => ClFunc_Tz::tz('Y-m-d H:i:00',null,$aInput['date'].' '.$aInput['s_time'].':00',$this->tz),
			'acAEnd'     => ClFunc_Tz::tz('Y-m-d H:i:00',null,$aInput['date'].' '.$aInput['e_time'].':00',$this->tz),
			'acStart'    => CL_DATETIME_DEFAULT,
			'acEnd'      => CL_DATETIME_DEFAULT,
			'acDate'     => date('YmdHis'),
		);
		$aLatLon = null;
		if ($aUpdate['acGIS'])
		{
			$aLatLon = array(
				'lat' => $aInput['lat'],
				'lon' => $aInput['lon'],
			);
		}

		try
		{
			$result = Model_Attend::updateAttend($aUpdate,$aActive,$aLatLon);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		Session::set('SES_T_NOTICE_MSG',__('出席予約を更新しました。'));

		Response::redirect('/t/attend/reserve');
	}

	public function action_stop($iNO = null)
	{
		if (is_null($iNO))
		{
			Session::set('SES_T_ERROR_MSG',__('出席情報が送信されていません。'));
			Response::redirect('/t/attend');
		}
		$result = Model_Attend::getAttendCalendarFromClass($this->aClass['ctID'],array(array('ac.no','=',$iNO),array('ac.acAStart','=',CL_DATETIME_DEFAULT),array('acAEnd','!=',CL_DATETIME_DEFAULT)));
		if (!count($result))
		{
			Session::set('SES_T_ERROR_MSG',__('指定された受付中の出席情報が見つかりません。'));
			Response::redirect('/t/attend');
		}
		$aActive = $result->current();

		$aUpdate = array(
			'acAEnd' => CL_DATETIME_DEFAULT,
			'acEnd'  => date('YmdHis'),
		);

		try
		{
			$result = Model_Attend::updateAttend($aUpdate,$aActive);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		Session::set('SES_T_NOTICE_MSG',__('受付中の出席を停止しました。'));
		Response::redirect('/t/attend');
	}

	public function action_delete($iNO = null)
	{
		if (is_null($iNO))
		{
			Session::set('SES_T_ERROR_MSG',__('出席情報が送信されていません。'));
			Response::redirect(Input::referrer());
		}
		$result = Model_Attend::getAttendCalendarFromClass($this->aClass['ctID'],array(array('ac.no','=',$iNO)));
		if (!count($result))
		{
			Session::set('SES_T_ERROR_MSG',__('指定された出席情報が見つかりません。'));
			Response::redirect(Input::referrer());
		}
		$aRes = $result->as_array();

		try
		{
			$result = Model_Attend::deleteAttend($iNO,$aRes[0]);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		Session::set('SES_T_NOTICE_MSG',__('出席情報を削除しました。'));
		Response::redirect(Input::referrer());
	}

	public function post_add()
	{
		if (!Input::post(null,false))
		{
			Session::set('SES_T_ERROR_MSG',__('出席情報が送信されていません。'));
			Response::redirect('/t/attend');
		}
		$aInput = Input::post();
		$val = Validation::forge();
		$val->add_callable('Helper_CustomValidation');
		$val->add_field('date', __('日付'), 'required|date')->add_rule('max_date',array(date('Y/m/d')));
		if (!$val->run())
		{
			Session::set('SES_T_ERROR_MSG',implode("\n",$val->error()));
			Response::redirect('/t/attend');
		}
		$sDate = date('Y-m-d',strtotime($aInput['date']));
		// 登録データ生成
		$aInsert = array(
			'ctID'       => $this->aClass['ctID'],
			'abDate'     => $sDate,
			'acDate'     => date('YmdHis'),
			'acGIS'      => 0,
		);

		try
		{
			$result = Model_Attend::insertAttend($aInsert);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		Session::set('SES_T_NOTICE_MSG',__('出席列を追加しました。'));
		Response::redirect('/t/attend');
	}

	public function action_csv()
	{
		if (!$this->aClass['ctStatus'])
		{
			Session::set('SES_T_ERROR_MSG',__('終了した講義に出席予約を追加することはできません。'));
			Response::redirect('/t/attend');
		}

		# タイトル
		$sTitle = __('CSVで一括予約');
		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/attend','name'=>__('出席管理'));
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);
		$this->template->set_global('pagetitle',$sTitle);

		if (!Input::post(null,false))
		{
			$data = array('error'=>null);
			$this->template->content = View::forge('t/attend/csv',$data);
			return $this->template;
		}

		$config = array(
			'max_size' => CL_FILESIZE*1024*1024,
			'path' => CL_UPPATH.DS.'t'.DS.'profile',
			'file_chmod' => 0666,
			'ext_whitelist' => array('txt', 'csv'),
			'type_whitelist' => array('text'),
		);

		Upload::process($config);

		# 添付処理
		$aMsg = null;
		if (!Upload::is_valid())
		{
			$st_csv = Upload::get_errors('at_csv');
			if ($st_csv['errors'])
			{
				switch ($st_csv['errors'][0]['error'])
				{
					case Upload::UPLOAD_ERR_INI_SIZE:
					case Upload::UPLOAD_ERR_FORM_SIZE:
					case Upload::UPLOAD_ERR_MAX_SIZE:
						$aMsg['at_csv'] = __('指定できるファイルのサイズは:sizeMBまでです。',array('size'=>CL_FILESIZE));;
						break;
					case Upload::UPLOAD_ERR_EXTENSION:
					case Upload::UPLOAD_ERR_EXT_BLACKLISTED:
					case Upload::UPLOAD_ERR_EXT_NOT_WHITELISTED:
					case Upload::UPLOAD_ERR_TYPE_BLACKLISTED:
					case Upload::UPLOAD_ERR_TYPE_NOT_WHITELISTED:
					case Upload::UPLOAD_ERR_MIME_BLACKLISTED:
					case Upload::UPLOAD_ERR_MIME_NOT_WHITELISTED:
						$aMsg['at_csv'] = __('登録できるファイルの拡張子は txt,csv のみです。');
						break;
					default:
						$aMsg['at_csv'] = __('ファイルアップロードに失敗しました。');
						break;
				}
			}
		}
		if (!is_null($aMsg))
		{
			$data['error'] = $aMsg;
			$this->template->content = View::forge('t/attend/csv',$data);
			return $this->template;
		}

		$oFile = Upload::get_files('at_csv');
		$aCSV = ClFunc_Common::getCSV($oFile['file']);
		if (!count($aCSV))
		{
			$data['error'] = array('at_csv'=>__('登録するデータがCSVから取得できませんでした。'));
			$this->template->content = View::forge('t/attend/csv',$data);
			return $this->template;
		}
		if ($aCSV[0][0] == '予約日' || $aCSV[0][0] == 'YOYAKUBI')
		{
			array_shift($aCSV);
		}

		$aIns = null;
		$aRange = null;
		$aMsg = null;
		$iAllCnt = 0;
		if (!empty($aCSV))
		{
			foreach ($aCSV as $iKey => $aS)
			{
				$iAllCnt++;
				if (!ClFunc_Common::dateValidation($aS[0],true,array('min'=>ClFunc_Tz::tz('Y/m/d',$this->tz))))
				{
					$aMsg[] = __(':no件目の予約日（:value）は、今日以降を指定してください。',array('no'=>($iKey + 1),'value'=>htmlspecialchars($aS[0])));
				}
				if (!ClFunc_Common::timeValidation($aS[1],$aS[0],true,array('min'=>ClFunc_Tz::tz('Y/m/d H:i',$this->tz))))
				{
					$aMsg[] = __(':no件目の予約開始時刻（:value）は、今日の現時刻以降を指定してください。',array('no'=>($iKey + 1),'value'=>htmlspecialchars($aS[1])));
				}
				if (!ClFunc_Common::timeValidation($aS[1],$aS[0],true,array('min'=>date('Y/m/d 6:00',strtotime($aS[0])),'max'=>date('Y/m/d 23:00',strtotime($aS[0])))))
				{
					$aMsg[] = __(':no件目の予約開始時刻（:value）は、6:00～22:55の間で指定してください。',array('no'=>($iKey + 1),'value'=>htmlspecialchars($aS[2])));
				}
				if (!ClFunc_Common::timeValidation($aS[2],$aS[0],true,array('min'=>date('Y/m/d H:i',strtotime($aS[0].' '.$aS[1])))))
				{
					$aMsg[] = __(':no件目の予約終了時刻（:value）は、予約開始時刻以降を指定してください。',array('no'=>($iKey + 1),'value'=>htmlspecialchars($aS[2])));
				}
				if (!ClFunc_Common::timeValidation($aS[2],$aS[0],true,array('min'=>date('Y/m/d 6:00',strtotime($aS[0])),'max'=>date('Y/m/d 23:00',strtotime($aS[0])))))
				{
					$aMsg[] = __(':no件目の予約終了時刻（:value）は、6:00～22:55の間で指定してください。',array('no'=>($iKey + 1),'value'=>htmlspecialchars($aS[2])));
				}
				if (!ClFunc_Common::existsAttend($this->aClass['ctID'],$aS[0],$aS[1],$aS[2],$this->tz))
				{
					$aMsg[] = __(':no件目の予約日時（:value）は、既存の出席予約と重複しています。',array('no'=>($iKey + 1),'value'=>htmlspecialchars($aS[0].' '.$aS[1].'～'.$aS[2])));
				}
				if (!ClFunc_Common::AttendTimeRange($aS[0],$aS[1],$aS[2],$aRange))
				{
					$aMsg[] = __(':no件目の予約日時（:value）は、入力CSV内で既に予約設定されています。',array('no'=>($iKey + 1),'value'=>htmlspecialchars($aS[0].' '.$aS[1].'～'.$aS[2])));
				}
				$aRange[] = array('start'=>strtotime($aS[0].' '.$aS[1]),'end'=>strtotime($aS[0].' '.$aS[2]));
				if (!ClFunc_Common::stringValidation($aS[3],false,array(4,4),array('alpha','numeric')))
				{
					$aMsg[] = __(':no件目の確認キーを指定する場合は、半角大小英数字4文字で入力してください。',array('no'=>($iKey + 1)));
				}

				$aIns[] = array(
					'abDate'   => ClFunc_Tz::tz('Y-m-d',null,$aS[0].' '.$aS[1],$this->tz),
					'acKey'    => $aS[3],
					'acGIS'    => (int)$aS[4],
					'acAStart' => ClFunc_Tz::tz('Y-m-d H:i:00',null,$aS[0].' '.ClFunc_Common::i5MinFloor($aS[1]),$this->tz),
					'acAEnd'   => ClFunc_Tz::tz('Y-m-d H:i:00',null,$aS[0].' '.ClFunc_Common::i5MinFloor($aS[2]),$this->tz),
				);
			}
		}
		else
		{
			$data['error'] = array('st_csv'=>__('登録するデータがありません。'));
			$this->template->content = View::forge('t/attend/csv',$data);
			return $this->template;
		}
		if (!is_null($aMsg))
		{
			$data['error'] = array('valid'=>$aMsg);
			$this->template->content = View::forge('t/attend/csv',$data);
			return $this->template;
		}

		if (!$this->aClass['ctLat'])
		{
			$sAdrs = ($this->aClass['cmAddress'])? $this->aClass['cmAddress']:$this->aClass['cmPref'].$this->aClass['cmCity'];
			$aLatLon = Clfunc_Common::getGeocoding($sAdrs);
			$this->aClass['ctLat'] = $aLatLon['lat'];
			$this->aClass['ctLon'] = $aLatLon['lon'];
		}

		try
		{
			$result = Model_Attend::insertAttendFromCSV($aIns,$this->aClass);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		Session::set('SES_T_NOTICE_MSG','CSVからの予約一括登録が完了しました。');
		Response::redirect('t/attend/reserve');
	}

	public function action_detail($iNo = null)
	{
		if (is_null($iNo))
		{
			Session::set('SES_T_ERROR_MSG',__('出席情報が送信されていません。'));
			Response::redirect('/t/attend');
		}

		$aAttendMaster = null;
		$result = Model_Attend::getAttendMasterFromClass($this->aClass['ctID']);
		if (count($result))
		{
			$aAttendMaster = $result->as_array();
		}

		$aAttend = null;
		$result = Model_Attend::getAttendCalendarFromNO($iNo,array(array('ctID','=',$this->aClass['ctID'])));
		if (!count($result))
		{
			Session::set('SES_T_ERROR_MSG',__('出席情報が見つかりませんでした。'));
			Response::redirect('/t/attend');
		}
		$aAttend = $result->current();

		# タイトル
		$sTitle = __('出席状況').'（'.ClFunc_Tz::tz(__('Y年n月j日'),$this->tz,$aAttend['abDate']).' '.ClFunc_Tz::tz('H:i～',$this->tz,$aAttend['acStart']).'）';

		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/attend','name'=>__('出席管理'));
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);
		$this->template->set_global('pagetitle',$sTitle);

		$aStudent = null;
		$result = Model_Student::getStudentFromClass($this->aClass['ctID'],null,null,array('st.stNO'=>'asc'));
		if (count($result))
		{
			$aRes = $result->as_array();
			foreach ($aRes as $aR)
			{
				$aStudent[$aR['stID']] = $aR;
			}
		}
		$result = Model_Attend::getAttendBookFromClass($this->aClass['ctID'],array(array('abDate','=',$aAttend['abDate']),array('acNO','=',$aAttend['acNO'])));
		if (count($result))
		{
			$aRes = $result->as_array();
			foreach ($aRes as $aR)
			{
				if (isset($aStudent[$aR['stID']]))
				{
					$aStudent[$aR['stID']]["attend"][$aR["abDate"]][$aR["acNO"]] = $aR;
				}
			}
		}

		$this->template->content = View::forge('t/attend/detail');
		$this->template->content->set('aAttend',$aAttend);
		$this->template->content->set('aAttendMaster',$aAttendMaster);
		$this->template->content->set('aStudent',$aStudent);
		$this->template->javascript = array('jquery.timepicker.js','cl.t.attend.js');
		return $this->template;
	}

	public function action_addNote()
	{
		$sDate = date('Y-m-d');
		// 登録データ生成
		$aInsert = array(
			'ctID'       => $this->aClass['ctID'],
			'abDate'     => $sDate,
			'acDate'     => date('YmdHis'),
		);

		try
		{
			$iAcNO = Model_Attend::insertAttend($aInsert);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		$result = Model_Attend::getAttendCalendarFromClass($this->aClass['ctID'],array(array('ac.abDate','=',date('Y-m-d')),array('ac.acNO','=',$iAcNO)));
		$aRes = $result->current();

		Response::redirect('/t/attend/detail/'.$aRes['no']);
	}

	public function action_editMaster()
	{
		# タイトル
		$sTitle = __('出席項目の設定');

		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/attend','name'=>__('出席管理'));
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);
		$this->template->set_global('pagetitle',$sTitle);

		$aInput = null;
		$aMsg = null;
		if (Input::post(null,false))
		{
			for ($i = 0; $i < 20; $i++)
			{
				if (!Input::post('name'.$i))
				{
					if ($i == 0 || $i == 1)
					{
						$aMsg[$i] = __('この項目を省略することはできません。');
					}
					continue;
				}
				$aInput[$i]['amName']    = Input::post('name'.$i);
				$aInput[$i]['amShort']   = Input::post('short'.$i);
				$aInput[$i]['amAbsence'] = ($i == 0)? 1:(int)Input::post('absence'.$i);
				$aInput[$i]['amDefault'] = 0;
				if ($aInput[$i]['amShort'] == '')
				{
					$aMsg[$i] = __('名称設定されている項目の短縮名を省略することはできません。');
				}
				$aInput[$i]['amTime'] = (int)Input::post('time'.$i);
			}
			if (!(int)Input::post('default'))
			{
				$aMsg['default'] = __('デフォルト項目を選択してください。');
			}
			else if (!isset($aInput[(int)Input::post('default')]))
			{
				$aMsg['default'] = __('名称設定されていない項目をデフォルトに指定することはできません。');
			}
			else
			{
				$aInput[(int)Input::post('default')]['amDefault'] = 1;
				$aInput[(int)Input::post('default')]['amTime'] = 0;
			}

			if (is_null($aMsg))
			{
				try
				{
					$result = Model_Attend::updateAttendMaster($this->aClass['ctID'],$aInput);
				}
				catch (Exception $e)
				{
					\Clfunc_Common::LogOut($e,__CLASS__);
					Session::set('SES_T_ERROR_MSG',$e->getMessage());
					Response::redirect($this->eRedirect);
				}
				Session::set('SES_T_NOTICE_MSG',__('出席項目を設定しました。'));
				Response::redirect('/t/attend/editMaster');
			}
		}

		$aAttendMaster = null;
		$result = Model_Attend::getAttendMasterFromClass($this->aClass['ctID']);
		if (count($result))
		{
			foreach($result as $aR)
			{
				$aAttendMaster[$aR['amAttendState']] = $aR;
			}
		}
		if (is_null($aInput))
		{
			$aInput = $aAttendMaster;
		}

		$this->template->content = View::forge('t/attend/editMaster');
		$this->template->content->set('aInput',$aInput);
		$this->template->content->set('aMsg',$aMsg);
		return $this->template;
	}
}