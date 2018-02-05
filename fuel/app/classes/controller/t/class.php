<?php
class Controller_T_Class extends Controller_T_Base
{
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
/*
		if (!$this->aTeacher['gtID'] && $this->aTeacher['coTermDate'] < date('Y-m-d'))
		{
			Session::set('SES_T_ERROR_MSG',__('現在、契約がありません。:siteを利用するにはプランを選択の上、購入・契約を行ってください。',array('site'=>CL_SITENAME)));
			Response::redirect('/t/payment/product');
		}
*/
	}

	public function action_index($sCtID = null, $sSub = null)
	{
		if (is_null($sCtID))
		{
			Session::set('SES_T_ERROR_MSG',__('講義が指定されていません。'));
			Response::redirect('/t/index');
		}
		$result = Model_Class::getClassFromTeacher($this->aTeacher['ttID'],null,array(array('tp.ctID','=',$sCtID)));
		if (count($result)) {
			$aClass = $result->current();
			$this->template->set_global('aClass',$aClass);
			Cookie::set('CL_T_CLASS_ID',$sCtID);
		}
		else
		{
			Session::set('SES_T_ERROR_MSG',__('指定された講義が見つかりません。'));
			Response::redirect('/t/index');
		}

		if (!is_null($sSub))
		{
			Response::redirect('/t/'.$sSub);
		}

		# ニュース取得
		$aNews = null;
		$sNow = date('Y-m-d H:i:s');
		$aWhere = array(array('cnStart','<=',$sNow),array('cnEnd','>=',$sNow));
		$result = Model_Class::getNews($sCtID,$aWhere);
		if (count($result))
		{
			foreach ($result as $i => $aN)
			{
				$aNews[$i] = $aN;
				$aNews[$i]['cnChain'] = ($aN['cnURL'])? \Clfunc_Common::ExtUrlDetect($aN['cnURL']):null;
			}
		}
		$this->template->set_global('aClassNews',$aNews);

		$sPWH = '';
		$sSep = '';
		if ($aClass['dpNO'])
		{
			$sPWH .= $this->aPeriod[$aClass['dpNO']];
			$sSep = '/';
		}
		if ($aClass['ctWeekDay'])
		{
			$sPWH .= $sSep.$this->aWeekday[$aClass['ctWeekDay']];
			$sSep = '/';
		}
		if ($aClass['dhNO'])
		{
			$sPWH .= $sSep.$this->aHour[$aClass['dhNO']];
		}
		if ($sPWH)
		{
			$sPWH = '（'.$sPWH.'）';
		}

		# 未読情報取得
		$oUC = new ClFunc_UnreadCount();
		$oUC->setClass($aClass);
		if (!is_null($this->aAssistant))
		{
			$oUC->setAssistant($this->aAssistant);
		}
		else
		{
			$oUC->setTeacher($this->aTeacher);
		}
		$this->template->set_global('iContact',$oUC->getContact());
		$this->template->set_global('iCoop',$oUC->getCoop());

		# タイトル
		$sTitle = '<i class="fa fa-book fa-fw"></i>'.$aClass['ctName'].$sPWH.' <i class="fa fa-user"></i>'.__(':num名',array('num'=>$aClass['scNum']));
		$this->template->set_global('pagetitle',__('講義トップ'));
		$this->template->set_global('classtitle',$sTitle,false);
		$sGuest = ($aClass['ctGuestAuth'])? '[G]':'';
		$this->template->set_global('subtitle',__('講義コード').' ['.\Clfunc_Common::getCode($aClass['ctCode']).'] '.$sGuest);

		# パンくずリスト生成
		$this->aBread = array();
		if ($aClass['ctStatus'] == 0)
		{
			$this->aBread[] = array('link'=>'/index/close','name'=>__('終了した講義'));
		}
		$this->aBread[] = array('name'=>$aClass['ctName']);
		$this->template->set_global('breadcrumbs',$this->aBread);

		# カスタムメニュー
		$aCustomMenu = null;

		if (!$this->aAssistant)
		{
			if (is_null($this->aGroup) || !($this->aGroup['gtTeacherAuthFlag'] & \Clfunc_Flag::T_AUTH_CLASS))
			{
				if ($aClass['tpMaster'])
				{
					$aCustomMenu = array(
						array(
							'url'  => '/t/class/edit/'.$aClass['ctID'],
							'name' => __('講義情報の編集'),
							'show' => 0,
						),
						array(
							'url'  => '/t/class/close/'.$aClass['ctID'],
							'name' => __('講義の終了'),
							'show' => 1,
						),
						array(
							'url'  => '/t/class/open/'.$aClass['ctID'],
							'name' => __('講義の実施'),
							'show' => -1,
						),
						array(
							'url'  => '/t/class/delete/'.$aClass['ctID'],
							'name' => __('講義の削除'),
							'show' => -1,
							'option' => array(
								'class' => 'deleteBtn',
								'data'  => 't-class',
							),
						),
					);
				}
			}
			$this->template->set_global('aCustomMenu',$aCustomMenu);
		}

		# カスタムボタン
		$aCustomBtn[] = array(
			'url'  => '/t/class/distribute/'.$aClass['ctID'],
			'name' => __('学生・ゲスト回答者への配布資料'),
			'show' => 1,
			'icon' => 'fa-download',
			'class' => array(
				'default'
			)
		);
		if ($this->aTeacher['gtID'] == '' && !CL_CAREERTASU_MODE)
		{
			$aCustomBtn[] = array(
					'url'  => '/t/index/tutorial',
					'name' => __('今すぐはじめるクイックアンケート'),
					'show' => 0,
					'class' => array(
							'default'
					)
			);
		}
		$this->template->set_global('aCustomBtn',$aCustomBtn);

		$this->template->content = View::forge('t/class/index');

		// 講義作成コード
		$aCreateKey = null;
		$sCreateKey = Session::get('SES_T_CLASS_CREATE_KEY',false);
		if ($sCreateKey)
		{
			$aCreateKey = unserialize($sCreateKey);
			Session::delete('SES_T_CLASS_CREATE_KEY');
		}
		$this->template->content->set('aCreateKey',$aCreateKey);

		return $this->template;
	}


	public function action_create()
{
		
		// if (!is_null($this->aGroup) && ($this->aGroup['gtTeacherAuthFlag'] & \Clfunc_Flag::T_AUTH_CLASS))
		// {
		// 	Response::redirect('/t/test');
		// }

// 		if ($this->aTeacher['gtID'] == '')
// //		if ($this->aTeacher['gtID'] == '' && $this->aTeacher['ttClassNum'] != 0)
// 		{
// 			if ($this->aTeacher['ttClassNum'] >= $this->aTeacher['coClassNum'])
// 			{
// 				switch ($this->aTeacher['ptID'])
// 				{
// 					case 1:
// 					case 2:
// 						Session::set('SES_T_ERROR_MSG',__('契約講義数が足りません。Standardプランの契約が必要です。'));
// 					break;
// 					case 3:
// 						Session::set('SES_T_ERROR_MSG',__('契約講義数が足りません。講義の追加購入が必要です。'));
// 					break;
// 				}
// 				Response::redirect('/t/test');
// 			}
// 		}

		$this->aClassFlag = \Clfunc_Flag::getClassFlag();
		$this->template->set_global('aClassFlag', $this->aClassFlag);
		$this->template->set_global('bEdit', false);

		# パンくずリスト生成
		$this->template->set_global('breadcrumbs',array(array('name'=>__('新しい講義を作成'))));
		# ページタイトル生成
		$this->template->set_global('pagetitle',__('新しい講義を作成'));
		$this->template->set_global('aClass',null);


		$aWeek = $this->aWeekday;
		$aWeek[0] = __('指定なし');
		ksort($aWeek);
		$iY = (date('n') <= 3)? date('Y',strtotime('-1 year')):date('Y');
		$aYear = Clfunc_Common::YearList($iY);

		if (!Input::post(null,false))
		{
			$aPeriod = $this->aPeriod;
			$aHour = $this->aHour;

			$data = $this->aClassBase;

			$data['c_year'] = $iY;
			$data['C_FUNC'] = \Clfunc_Common::dec2Bits(4095);
			$data['S_GET']  = \Clfunc_Common::dec2Bits(0);

			$data['error'] = null;
			$this->template->content = View::forge('t/class/edit',$data);
			$this->template->content->set('weekdaylist',$aWeek);
			$this->template->content->set('periodlist',$aPeriod);
			$this->template->content->set('hourlist',$aHour);
			$this->template->content->set('yearlist',$aYear);
			$this->template->javascript = array('cl.t.class.js');
			return $this->template;
		}

		$aInput = Input::post();
		$val = Validation::forge();
		$val->add_callable('Helper_CustomValidation');

		$val->add('c_name', __('講義名'))
			->add_rule('required')
			->add_rule('trim')
			->add_rule('max_length',30);

		if (!$val->run())
		{
			$aPeriod = $this->aPeriod;
			$aHour = $this->aHour;

			$data = $aInput;
			$data['error'] = $val->error();
			$this->template->content = View::forge('t/class/edit',$data);
			$this->template->content->set('weekdaylist',$aWeek);
			$this->template->content->set('periodlist',$aPeriod);
			$this->template->content->set('hourlist',$aHour);
			$this->template->content->set('yearlist',$aYear);
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


		// 登録データ生成
		$aInsert['class'] = array(
			'ctID'       => null,
			'ctCode'     => Model_Class::getClassCode(CL_CLASSCODE),
			'ctName'     => $aInput['c_name'],
			'ctYear'     => $aInput['c_year'],
			'dpNO'       => $aInput['c_period'],
			'ctWeekday'  => $aInput['c_weekday'],
			'dhNO'       => $aInput['c_hour'],
			'ctStatus'   => 1,
			'ctDate'     => date('YmdHis'),
			'ctFunctionFlag' => $iFunc,
			'ctStudentGetFlag' => $iSGet,
		);
		$aInsert['position'] = array(
			'ctID'     => null,
			'ttID'     => $this->aTeacher['ttID'],
			'tpMaster' => 1,
			'tpDate'   => date('YmdHis'),
		);

		try
		{
			$result = Model_Class::insertClass($aInsert);
			$sCtID = $result;
			$result = Model_Class::resetTeacherClassSort(array($this->aTeacher['ttID']));
			$result = Model_Teacher::setTeacherClassNum(array($this->aTeacher['ttID']));
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		Session::set('SES_T_CLASS_CREATE_KEY',serialize(array($sCtID,$aInsert['class']['ctCode'])));
		Response::redirect('/t/index');
	}


	public function action_edit($sCtID = null)
	{
		if (!is_null($this->aGroup) && ($this->aGroup['gtTeacherAuthFlag'] & \Clfunc_Flag::T_AUTH_CLASS))
		{
			Response::redirect('/t/index');
		}

		$this->aClassFlag = \Clfunc_Flag::getClassFlag();
		$this->template->set_global('aClassFlag', $this->aClassFlag);
		$this->template->set_global('bEdit', true);

		$aWeek = $this->aWeekday;
		$aWeek[0] = __('指定なし');
		ksort($aWeek);

		if (is_null($sCtID))
		{
			Session::set('SES_T_ERROR_MSG',__('講義の情報が確認できませんでした。'));
			Response::redirect('/t/index');
		}

		$aClass = null;
		$result = Model_Class::getClassFromID($sCtID);
		if (!count($result))
		{
			Session::set('SES_T_ERROR_MSG',__('指定された講義が見つかりません。'));
			Response::redirect('/t/index');
		}
		$aClass = $result->current();

		$iY = (date('n') <= 3)? date('Y',strtotime('-1 year')):date('Y');
		$iY = ((int)$aClass['ctYear'] > $iY)? $iY:(int)$aClass['ctYear'];
		$aYear = Clfunc_Common::YearList($iY);

		$sTitle = __('講義情報の編集');
		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/class/index/'.$aClass['ctID'],'name'=>$aClass['ctName']);
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);
		# ページタイトル生成
		$this->template->set_global('pagetitle',$sTitle);
		$this->template->set_global('aClass',$aClass);

		if (!Input::post(null,false))
		{
			$aPeriod = $this->aPeriod;
			$aHour = $this->aHour;

			$data = $this->aClassBase;
			$data['c_name'] = $aClass['ctName'];
			$data['c_year'] = $aClass['ctYear'];
			$data['c_period'] = $aClass['dpNO'];
			$data['c_weekday'] = $aClass['ctWeekDay'];
			$data['c_hour'] = $aClass['dhNO'];
			$data['C_FUNC'] = \Clfunc_Common::dec2Bits((int)$aClass['ctFunctionFlag']);
			$data['S_GET']  = \Clfunc_Common::dec2Bits((int)$aClass['ctStudentGetFlag']);

			$data['error'] = null;
			$this->template->content = View::forge('t/class/edit',$data);
			$this->template->content->set('weekdaylist',$aWeek);
			$this->template->content->set('periodlist',$aPeriod);
			$this->template->content->set('hourlist',$aHour);
			$this->template->content->set('yearlist',$aYear);
			$this->template->javascript = array('cl.t.class.js');
			return $this->template;
		}
		$aInput = Input::post();

		$val = Validation::forge();
		$val->add_callable('Helper_CustomValidation');

		$val->add('c_name', __('講義名'))
			->add_rule('required')
			->add_rule('trim')
			->add_rule('max_length',30);

		if (!$val->run())
		{
			$aPeriod = $this->aPeriod;
			$aHour = $this->aHour;

			$data = $aInput;
			$data['error'] = $val->error();
			$this->template->content = View::forge('t/class/edit',$data);
			$this->template->content->set('aClass',$aClass);
			$this->template->content->set('weekdaylist',$aWeek);
			$this->template->content->set('periodlist',$aPeriod);
			$this->template->content->set('hourlist',$aHour);
			$this->template->content->set('yearlist',$aYear);
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

		// 登録データ生成
		$aInsert = array(
			'ctName'     => $aInput['c_name'],
			'ctYear'     => $aInput['c_year'],
			'dpNO'       => $aInput['c_period'],
			'ctWeekday'  => $aInput['c_weekday'],
			'dhNO'       => $aInput['c_hour'],
			'ctFunctionFlag' => $iFunc,
			'ctStudentGetFlag' => $iSGet,
		);
		try
		{
			$result = Model_Class::updateClass($aInsert,$aClass);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		Session::set('SES_T_NOTICE_MSG',__('講義「:class」の情報を更新しました。',array('class'=>$aInsert['ctName'])));

		Response::redirect('/t/class/index/'.$aClass['ctID']);
	}

	public function action_close($sCtID = null)
	{
		if (!is_null($this->aGroup) && ($this->aGroup['gtTeacherAuthFlag'] & \Clfunc_Flag::T_AUTH_CLASS))
		{
			Response::redirect('/t/index');
		}

		if (is_null($sCtID))
		{
			Session::set('SES_T_ERROR_MSG',__('講義の情報が確認できませんでした。'));
			Response::redirect('/t/index');
		}

		$aClass = null;
		$result = Model_Class::getClassFromID($sCtID);
		if (!count($result))
		{
			Session::set('SES_T_ERROR_MSG',__('指定された講義が見つかりません。'));
			Response::redirect('/t/index');
		}
		$aClass = $result->current();


		$aInsert = array('ctStatus'=>0);
		try
		{
			$result = Model_Class::updateClass($aInsert,$aClass);
			$result = Model_Class::updateClassTeacher(array('tpSort'=>0), $aClass);

			$result = Model_Teacher::getTeacherFromClass(array(array('tp.ctID','=',$sCtID)));
			if (count($result))
			{
				$aRes = $result->as_array('ttID');
				$aTIDs = array_keys($aRes);

				Model_Class::resetTeacherClassSort($aTIDs);
			}

			$result = Model_Teacher::setTeacherClassNum(array($this->aTeacher['ttID']));
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		Session::set('SES_T_NOTICE_MSG',__('講義「:class」を終了しました。',array('class'=>$aClass['ctName'])));
		Response::redirect('/t/class/index/'.$aClass['ctID']);
	}


	public function action_open($sCtID = null)
	{
		if (!is_null($this->aGroup) && ($this->aGroup['gtTeacherAuthFlag'] & \Clfunc_Flag::T_AUTH_CLASS))
		{
			Response::redirect('/t/index');
		}

		if ($this->aTeacher['gtID'] == '' && $this->aTeacher['ttClassNum'] != 0)
		{
			if (!$this->aTeacher['coClassNum'])
			{
				\Clfunc_Common::ContractDetect($this->aTeacher);
			}
			else  if ($this->aTeacher['ttClassNum'] >= $this->aTeacher['coClassNum'])
			{
				switch ($this->aTeacher['ptID'])
				{
					case 1:
					case 2:
						Session::set('SES_T_ERROR_MSG',__('契約講義数が足りません。Standardプランの契約が必要です。'));
					break;
					case 3:
						Session::set('SES_T_ERROR_MSG',__('契約講義数が足りません。講義の追加購入が必要です。'));
					break;
				}
				Response::redirect('/t/payment/product');
			}
		}

		if (is_null($sCtID))
		{
			Session::set('SES_T_ERROR_MSG',__('講義の情報が確認できませんでした。'));
			Response::redirect('/t/index');
		}

		$aClass = null;
		$result = Model_Class::getClassFromID($sCtID);
		if (!count($result))
		{
			Session::set('SES_T_ERROR_MSG',__('指定された講義が見つかりません。'));
			Response::redirect('/t/index');
		}
		$aClass = $result->current();


		$aInsert = array('ctStatus'=>1);
		try
		{
			$result = Model_Class::updateClass($aInsert,$aClass);

			$result = Model_Teacher::getTeacherFromClass(array(array('tp.ctID','=',$sCtID)));
			if (count($result))
			{
				$aRes = $result->as_array('ttID');
				$aTIDs = array_keys($aRes);

				Model_Class::resetTeacherClassSort($aTIDs);
			}
			$result = Model_Teacher::setTeacherClassNum(array($this->aTeacher['ttID']));
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		Session::set('SES_T_NOTICE_MSG',__('講義「:class」を実施しました。',array('class'=>$aClass['ctName'])));
		Response::redirect('/t/class/index/'.$aClass['ctID']);
	}


	public function action_delete($sCtID = null)
	{
		if (!is_null($this->aGroup) && ($this->aGroup['gtTeacherAuthFlag'] & \Clfunc_Flag::T_AUTH_CLASS))
		{
			Response::redirect('/t/index');
		}

		if (is_null($sCtID))
		{
			Session::set('SES_T_ERROR_MSG',__('講義の情報が確認できませんでした。'));
			Response::redirect('/t/index');
		}

		$aClass = null;
		$result = Model_Class::getClassFromID($sCtID);
		if (!count($result))
		{
			Session::set('SES_T_ERROR_MSG',__('指定された講義が見つかりません。'));
			Response::redirect('/t/index');
		}
		$aClass = $result->current();

		try
		{
			$result = Model_Class::deleteClass(array($sCtID));
			$result = Model_Teacher::setTeacherClassNum(array($this->aTeacher['ttID']));
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		Session::set('SES_T_NOTICE_MSG',__('講義「:class」を削除しました。',array('class'=>$aClass['ctName'])));

		Response::redirect('/t/index');
	}

	public function action_teacher($sCtID = null)
	{
		if (is_null($sCtID))
		{
			Session::set('SES_T_ERROR_MSG',__('講義の情報が確認できませんでした。'));
			Response::redirect('/t/index');
		}

		$aWhere = array(
			array('gtID','=',$this->aGroup['gtID']),
			array('ctID','=',$sCtID),
		);

		$result = Model_Group::getGroupClasses($aWhere);
		if (!count($result))
		{
			Response::redirect('/t/index');
		}
		$aClass = $result->current();
		$this->template->set_global('aClass',$aClass);

		$aTeachers = null;
		$result = Model_Group::getGroupTeachersClasses(
			array(
				array('gc.gtID','=',$this->aGroup['gtID']),
				array('tp.ctID','=',$sCtID),
			),null,array('tt.ttName'=>'asc'));
		if (count($result))
		{
			$aTeachers = $result->as_array();
		}

		$sTitle = __(':classの共同先生一覧',array('class'=>$aClass['ctName']));
		$aBreadCrumbs = array();

		# パンくずリスト生成
		$aBreadCrumbs[] = array('name' => $sTitle);
		$this->template->set_global('breadcrumbs',$aBreadCrumbs);
		# ページタイトル生成
		$this->template->set_global('pagetitle',$sTitle);

		$this->template->content = View::forge('t/class/teacher');
		$this->template->content->set('aTeachers',$aTeachers);
		return $this->template;
	}

	public function action_distribute($sCtID = null)
	{
		if (is_null($sCtID))
		{
			Session::set('SES_T_ERROR_MSG',__('講義が指定されていません。'));
			Response::redirect('/t/index');
		}
		$result = Model_Class::getClassFromTeacher($this->aTeacher['ttID'],null,array(array('tp.ctID','=',$sCtID)));
		if (count($result)) {
			$aClass = $result->current();
			$this->template->set_global('aClass',$aClass);
			Cookie::set('CL_T_CLASS_ID',$sCtID);
		}
		else
		{
			Session::set('SES_T_ERROR_MSG',__('指定された講義が見つかりません。'));
			Response::redirect('/t/index');
		}

		Session::set('SES_T_CLASS_CREATE_KEY',serialize(array($sCtID,$aClass['ctCode'])));
		Response::redirect('/t/class/index/'.$sCtID);
	}




}