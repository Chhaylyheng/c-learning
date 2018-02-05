<?php
class Controller_T_ALog extends Controller_T_Baseclass
{
	private $baseName = 'alog';

	private $aALogTheme = array(
		'at_name'=>null,
		'at_goal_label'=>null,
		'at_goal_desc'=>null,
		'at_title'=>0,
		'at_title_label'=>null,
		'at_title_desc'=>null,
		'at_range'=>0,
		'at_range_label'=>null,
		'at_range_desc'=>null,
		'at_opt1'=>0,
		'at_opt1_label'=>null,
		'at_opt1_desc'=>null,
		'at_opt2'=>0,
		'at_opt2_label'=>null,
		'at_opt2_desc'=>null,
		'at_text_label'=>null,
		'at_text_desc'=>null,
		'at_file'=>0,
		'at_file_label'=>null,
		'at_file_desc'=>null,
	);

	private $aALTheme = null;
	private $aALog = null;
	private $aALGoal = null;
	private $aSearchCol = array(
		'al.alTitle','al.alOpt1','al.alOpt2','al.alText','ft.fName','al.alCom'
	);

	public function before()
	{
		parent::before();
		\Clfunc_Common::ContractDetect($this->aTeacher, __CLASS__);
	}

	public function action_index()
	{
		$aALTheme = null;
		$result = Model_ALog::getALogThemeFromClass($this->aClass['ctID'],null,null,array('altSort'=>'desc'));
		if (count($result))
		{
			$aALTheme = $result->as_array();
		}
		else
		{
			if ($this->aClass['ctStatus'] > 0)
			{
				Response::redirect('/t/'.$this->baseName.'/create');
			}
		}

		# タイトル
		$sTitle = __('活動履歴テーマ一覧');
		$this->template->set_global('pagetitle',$sTitle);

		# パンくずリスト生成
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		# カスタムメニュー
		$aCustomMenu = array(
			array(
				'url'  => '/t/'.$this->baseName.'/create/',
				'name' => __('活動履歴テーマの新規作成'),
				'show' => 1,
			),
		);

		$this->template->set_global('aCustomMenu',$aCustomMenu);

		$this->template->content = View::forge('t/'.$this->baseName.'/index');
		$this->template->content->set('aALTheme',$aALTheme);
		$this->template->javascript = array('cl.t.'.$this->baseName.'.js');
		return $this->template;
	}


	public function action_create()
	{
		$view = 't/'.$this->baseName.'/edit';
		# タイトル
		$sTitle = __('活動履歴テーマの新規作成');
		$this->template->set_global('pagetitle',$sTitle);

		# パンくずリスト生成
		$this->aBread[] = array('link'=>DS.$this->baseName,'name'=>__('活動履歴テーマ一覧'));
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		if (!Input::post(null,false))
		{
			$data = $this->aALogTheme;

			$data['at_title'] = 1;
			$data['at_range'] = 1;

			$data['error'] = null;
			$this->template->content = View::forge($view,$data);
			$this->template->javascript = array('cl.t.'.$this->baseName.'.js');
			return $this->template;
		}

		$aInput = Input::post();
		$aInput = array_merge($this->aALogTheme, $aInput);

		$val = Validation::forge();
		$val->add_callable('Helper_CustomValidation');
		$val->add_field('at_name', __('テーマ名'), 'required|max_length['.CL_TITLE_LENGTH.']');
		$val->add_field('at_goal_label', __('目標').'['.__('ラベル').']', 'required|max_length[10]');
		$val->add_field('at_goal_desc', __('目標').'['.__('補足').']', 'max_length[100]');
		$val->add_field('at_text_label', __('内容').'['.__('ラベル').']', 'required|max_length[10]');
		$val->add_field('at_text_desc', __('内容').'['.__('補足').']', 'max_length[100]');

		if ($aInput['at_title'])
		{
			$val->add_field('at_title_label', __('タイトル').'['.__('ラベル').']', 'required|max_length[10]');
			$val->add_field('at_title_desc', __('タイトル').'['.__('補足').']', 'max_length[100]');
		}
		if ($aInput['at_range'])
		{
			$val->add_field('at_range_label', __('期間').'['.__('ラベル').']', 'required|max_length[10]');
			$val->add_field('at_range_desc', __('期間').'['.__('補足').']', 'max_length[100]');
		}
		if ($aInput['at_opt1'])
		{
			$val->add_field('at_opt1_label', __('オプション1').'['.__('ラベル').']', 'required|max_length[10]');
			$val->add_field('at_opt1_desc', __('オプション1').'['.__('補足').']', 'max_length[100]');
		}
		if ($aInput['at_opt2'])
		{
			$val->add_field('at_opt2_label', __('オプション2').'['.__('ラベル').']', 'required|max_length[10]');
			$val->add_field('at_opt2_desc', __('オプション2').'['.__('補足').']', 'max_length[100]');
		}
		if ($aInput['at_file'])
		{
			$val->add_field('at_file_label', __('添付ファイル').'['.__('ラベル').']', 'required|max_length[10]');
			$val->add_field('at_file_desc', __('添付ファイル').'['.__('補足').']', 'max_length[100]');
		}
		if (!$val->run())
		{
			$data = array_merge($this->aALogTheme, $aInput);
			$data['error'] = $val->error();
			$this->template->content = View::forge($view,$data);
			$this->template->javascript = array('cl.t.'.$this->baseName.'.js');
			return $this->template;
		}

		// 登録データ生成
		$aInsert = array(
			'ctID'    => $this->aClass['ctID'],
			'altName' => $aInput['at_name'],
			'altGoal'  => 1,
			'altGoalLabel' => $aInput['at_goal_label'],
			'altGoalDescription' => $aInput['at_goal_desc'],
			'altTitle'  => (int)$aInput['at_title'],
			'altTitleLabel' => $aInput['at_title_label'],
			'altTitleDescription' => $aInput['at_title_desc'],
			'altRange'  => (int)$aInput['at_range'],
			'altRangeLabel' => $aInput['at_range_label'],
			'altRangeDescription' => $aInput['at_range_desc'],
			'altOpt1'  => (int)$aInput['at_opt1'],
			'altOpt1Label' => $aInput['at_opt1_label'],
			'altOpt1Description' => $aInput['at_opt1_desc'],
			'altOpt2'  => (int)$aInput['at_opt2'],
			'altOpt2Label' => $aInput['at_opt2_label'],
			'altOpt2Description' => $aInput['at_opt2_desc'],
			'altText'  => 1,
			'altTextLabel' => $aInput['at_text_label'],
			'altTextDescription' => $aInput['at_text_desc'],
			'altFile'  => (int)$aInput['at_file'],
			'altFileLabel' => $aInput['at_file_label'],
			'altFileDescription' => $aInput['at_file_desc'],
			'altDate' => date('YmdHis'),
		);

		try
		{
			$result = Model_ALog::insertALogTheme($aInsert);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		Session::set('SES_T_NOTICE_MSG',__('活動履歴テーマを作成しました。'));
		Response::redirect('/t/'.$this->baseName);
	}

	public function action_edit($sID = null)
	{
		$view = 't/'.$this->baseName.'/edit';

		$aChk = self::ALogThemeChecker($sID);
		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}

		# タイトル
		$sTitle = __('活動履歴テーマ情報の編集');
		$this->template->set_global('pagetitle',$sTitle);

		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/'.$this->baseName,'name'=>__('活動履歴テーマ一覧'));
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		if (!Input::post(null,false))
		{
			$data = $this->aALogTheme;
			$data['at_name'] = $this->aALTheme['altName'];
			$data['at_goal_label'] = $this->aALTheme['altGoalLabel'];
			$data['at_goal_desc'] = $this->aALTheme['altGoalDescription'];
			$data['at_title'] = $this->aALTheme['altTitle'];
			$data['at_title_label'] = $this->aALTheme['altTitleLabel'];
			$data['at_title_desc'] = $this->aALTheme['altTitleDescription'];
			$data['at_range'] = $this->aALTheme['altRange'];
			$data['at_range_label'] = $this->aALTheme['altRangeLabel'];
			$data['at_range_desc'] = $this->aALTheme['altRangeDescription'];
			$data['at_opt1'] = $this->aALTheme['altOpt1'];
			$data['at_opt1_label'] = $this->aALTheme['altOpt1Label'];
			$data['at_opt1_desc'] = $this->aALTheme['altOpt1Description'];
			$data['at_opt2'] = $this->aALTheme['altOpt2'];
			$data['at_opt2_label'] = $this->aALTheme['altOpt2Label'];
			$data['at_opt2_desc'] = $this->aALTheme['altOpt2Description'];
			$data['at_text_label'] = $this->aALTheme['altTextLabel'];
			$data['at_text_desc'] = $this->aALTheme['altTextDescription'];
			$data['at_file'] = $this->aALTheme['altFile'];
			$data['at_file_label'] = $this->aALTheme['altFileLabel'];
			$data['at_file_desc'] = $this->aALTheme['altFileDescription'];

			$data['error'] = null;
			$this->template->content = View::forge($view,$data);
			$this->template->content->set('aALTheme',$this->aALTheme);
			$this->template->javascript = array('cl.t.'.$this->baseName.'.js');
			return $this->template;
		}

		$aInput = Input::post();
		$aInput = array_merge($this->aALogTheme, $aInput);

		$val = Validation::forge();
		$val->add_callable('Helper_CustomValidation');
		$val->add_field('at_name', __('テーマ名'), 'required|max_length['.CL_TITLE_LENGTH.']');
			$val->add_field('at_goal_label', __('目標').'['.__('ラベル').']', 'required|max_length[10]');
		$val->add_field('at_goal_desc', __('目標').'['.__('補足').']', 'max_length[100]');
		$val->add_field('at_text_label', __('内容').'['.__('ラベル').']', 'required|max_length[10]');
		$val->add_field('at_text_desc', __('内容').'['.__('補足').']', 'max_length[100]');

		if ($aInput['at_title'])
		{
			$val->add_field('at_title_label', __('タイトル').'['.__('ラベル').']', 'required|max_length[10]');
			$val->add_field('at_title_desc', __('タイトル').'['.__('補足').']', 'max_length[100]');
		}
		if ($aInput['at_range'])
		{
			$val->add_field('at_range_label', __('期間').'['.__('ラベル').']', 'required|max_length[10]');
			$val->add_field('at_range_desc', __('期間').'['.__('補足').']', 'max_length[100]');
		}
		if ($aInput['at_opt1'])
		{
			$val->add_field('at_opt1_label', __('オプション1').'['.__('ラベル').']', 'required|max_length[10]');
			$val->add_field('at_opt1_desc', __('オプション1').'['.__('補足').']', 'max_length[100]');
		}
		if ($aInput['at_opt2'])
		{
			$val->add_field('at_opt2_label', __('オプション2').'['.__('ラベル').']', 'required|max_length[10]');
			$val->add_field('at_opt2_desc', __('オプション2').'['.__('補足').']', 'max_length[100]');
		}
		if ($aInput['at_file'])
		{
			$val->add_field('at_file_label', __('添付ファイル').'['.__('ラベル').']', 'required|max_length[10]');
			$val->add_field('at_file_desc', __('添付ファイル').'['.__('補足').']', 'max_length[100]');
		}

		if (!$val->run())
		{
			$data = array_merge($this->aALogTheme, $aInput);
			$data['error'] = $val->error();
			$this->template->content = View::forge($view,$data);
			$this->template->content->set('aALTheme',$this->aALTheme);
			$this->template->javascript = array('cl.t.'.$this->baseName.'.js');
			return $this->template;
		}

		// 更新データ生成
		$aUpdate = array(
			'altName' => $aInput['at_name'],
			'altGoalLabel' => $aInput['at_goal_label'],
			'altGoalDescription' => $aInput['at_goal_desc'],
			'altTitle'  => (int)$aInput['at_title'],
			'altTitleLabel' => $aInput['at_title_label'],
			'altTitleDescription' => $aInput['at_title_desc'],
			'altRange'  => (int)$aInput['at_range'],
			'altRangeLabel' => $aInput['at_range_label'],
			'altRangeDescription' => $aInput['at_range_desc'],
			'altOpt1'  => (int)$aInput['at_opt1'],
			'altOpt1Label' => $aInput['at_opt1_label'],
			'altOpt1Description' => $aInput['at_opt1_desc'],
			'altOpt2'  => (int)$aInput['at_opt2'],
			'altOpt2Label' => $aInput['at_opt2_label'],
			'altOpt2Description' => $aInput['at_opt2_desc'],
			'altTextLabel' => $aInput['at_text_label'],
			'altTextDescription' => $aInput['at_text_desc'],
			'altFile'  => (int)$aInput['at_file'],
			'altFileLabel' => $aInput['at_file_label'],
			'altFileDescription' => $aInput['at_file_desc'],
			'altDate' => date('YmdHis'),
		);

		try
		{
			$result = Model_ALog::updateALogTheme($aUpdate,array(array('altID','=',$sID)));
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		Session::set('SES_T_NOTICE_MSG',__('活動履歴テーマ情報を更新しました。'));
		Response::redirect('/t/'.$this->baseName);

	}

	public function action_delete($sID = null)
	{
		$aChk = self::ALogThemeChecker($sID);
		if (is_array($aChk))
		{
			Session::set('SES_S_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}

		try
		{
			$result = Model_ALog::deleteALogTheme($sID,$this->aALTheme);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		Session::set('SES_T_NOTICE_MSG',__('活動履歴テーマを削除しました。'));
		Response::redirect('/t/'.$this->baseName);
	}

	public function action_preview($sID = null)
	{
		$aChk = self::ALogThemeChecker($sID);
		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}

		# タイトル
		$sTitle = $this->aALTheme['altName'].'｜'.__('プレビュー');
		$this->template->set_global('pagetitle',$sTitle);
		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/'.$this->baseName, 'name'=>__('活動履歴テーマ一覧'));
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		$this->template->content = View::forge('t/'.$this->baseName.'/preview');
		$this->template->content->set('aALTheme',$this->aALTheme);
		$this->template->javascript = array('jquery.timepicker.js','cl.t.'.$this->baseName.'.js');
		return $this->template;
	}

	public function action_list($sID = null)
	{
		$view = 't/'.$this->baseName.'/list';

		$aChk = self::ALogThemeChecker($sID);
		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}

		$aStudent = null;
		$result = Model_Student::getStudentFromClass($this->aClass['ctID'],null,null,array('st.stNO'=>'asc'));
		if (count($result))
		{
			$aRes = $result->as_array();
			foreach ($aRes as $aR)
			{
				$aStudent[$aR['stID']] = $aR;
				$aStudent[$aR['stID']]["alogNum"] = 0;
			}
		}

		$aY = array(
				date('Y-m-d',strtotime('-1 month')),
				date('Y-m-d'),
		);

		if (Input::get('sd',false))
		{
			$aReq = Input::get();
			if ($aReq['sd'] <= $aReq['ed'])
			{
				$aY[0] = $aReq['sd'];
				$aY[1] = $aReq['ed'];
			} else {
				$aY[0] = $aReq['ed'];
				$aY[1] = $aReq['sd'];
			}
		}
		$iSC = strtotime($aY[0]);
		$iEC = strtotime($aY[1]);
		$aWhere = array(
			array('al.altID','=',$sID),
			array('al.alDate','between',array(date('Y-m-d 00:00:00',$iSC),date('Y-m-d 23:59:59',$iEC))),
		);

		$aDays = null;
		$result = Model_Alog::getAlog($aWhere,null,array('al.no'=>'desc'));
		if (count($result))
		{
			foreach ($result as $aC)
			{
				if (isset($aStudent[$aC['stID']]))
				{
					$sDate = ClFunc_Tz::tz('Y-m-d',$this->tz,$aC['alDate']);
					$aStudent[$aC['stID']]["alog"][$sDate][] = $aC;
					$aDays[$sDate] = ClFunc_Tz::tz('n/j',$this->tz,$aC['alDate']).'('.$this->aWeekday[ClFunc_Tz::tz('N',$this->tz,$aC['alDate'])].')';
					$aStudent[$aC['stID']]["alogNum"] += 1;
				}
			}
		}

		# タイトル
		$sTitle = __('入力状況').'｜'.$this->aALTheme['altName'];
		$this->template->set_global('pagetitle',$sTitle);
		# パンくずリスト生成
		$this->aBread[] = array('name'=>$sTitle);
		$this->aBread[] = array('link'=>'/'.$this->baseName,'name'=>__('活動履歴テーマ一覧'));
		$this->template->set_global('breadcrumbs',$this->aBread);

		# カスタムボタン
		$aCustomBtn = array(
			array(
				'url'  => '/t/'.$this->baseName.'/fulltext/'.$sID,
				'name' => __('記録内容一覧'),
				'show' => 0,
			),
		);
		$this->template->set_global('aCustomBtn',$aCustomBtn);

		$this->template->content = View::forge($view);
		$this->template->content->set('aY',$aY);
		$this->template->content->set('aALTheme',$this->aALTheme);
		$this->template->content->set('aStudent',$aStudent);
		$this->template->content->set('aDays',$aDays);
		$this->template->javascript = array('cl.t.'.$this->baseName.'.js');
		return $this->template;
	}

	public function action_fulltext($sID = null)
	{
		$view = 't/'.$this->baseName.'/fulltext';

		$aALTheme = null;
		$aALTIDs = null;
		$aALogList = null;
		$aWords = null;
		$sWords = null;
		$sAlt = null;

		$aChk = self::ALogThemeChecker($sID);
		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}

		$aStudent = null;
		$result = Model_Student::getStudentFromClass($this->aClass['ctID'],null,null,array('st.stNO'=>'asc'));
		if (count($result))
		{
			$aStudent = $result->as_array('stID');
		}

		$aY = array(
			date('Y-m-d',strtotime('-1 month')),
			date('Y-m-d'),
		);

		$aWhere = array(array('al.altID','=',$sID));
		if (Input::get('sd',false))
		{
			$aReq = Input::get();
			if ($aReq['sd'] <= $aReq['ed'])
			{
				$aY[0] = $aReq['sd'];
				$aY[1] = $aReq['ed'];
			} else {
				$aY[0] = $aReq['ed'];
				$aY[1] = $aReq['sd'];
			}
			$iSC = strtotime($aY[0]);
			$iEC = strtotime($aY[1]);
			$aWhere[] = array('al.alDate','between',array(date('Y-m-d 00:00:00',$iSC),date('Y-m-d 23:59:59',$iEC)));

			$sW = $aReq['w'];
			if ($sW)
			{
				$aW = \Clfunc_Common::getSearchWords($sW);
				$aWords = \Clfunc_Common::getSearchWhere($aW,$this->aSearchCol);
				$sWords = implode(' ', $aW);
			}

			$result = Model_Alog::getAlog($aWhere,null,array('al.no'=>'desc'),$aWords);
			if (count($result))
			{
				$aALogList = $result->as_array();
			}
		}

		# タイトル
		$sTitle = __('記録内容一覧');
		$this->template->set_global('pagetitle',$sTitle);
		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/'.$this->baseName,'name'=>__('活動履歴テーマ一覧'));
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		$this->template->content = View::forge($view);
		$this->template->content->set('aY',$aY);
		$this->template->content->set('sWords',$sWords);
		$this->template->content->set('aALTheme',$this->aALTheme);
		$this->template->content->set('aStudent',$aStudent);
		$this->template->content->set('aALogList',$aALogList);
		$this->template->javascript = array('cl.t.'.$this->baseName.'.js');
		return $this->template;
	}

	public function action_detail($sID = null,$iNO = null)
	{
		$view = 't/'.DS.$this->baseName.'/detail';

		$aChk = self::ALogThemeChecker($sID);
		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}
		$aChk = self::ALogChecker($sID,$iNO);
		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}

		$result = Model_Student::getStudentFromClass($this->aClass['ctID'],array(array('sp.stID','=',$this->aALog['stID'])));
		if (!count($result))
		{
			Session::set('SES_T_ERROR_MSG',__('指定された学生は本講義を履修していません。'));
			Response::redirect('/t/'.$this->baseName.'/list/'.$sID);
		}
		$aStudent = $result->current();

		$aChk = self::ALogGoalChecker($sID,$aStudent['stID'],false);
		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}

		# タイトル
		$sTitle = '['.$aStudent['stNO'].']'.$aStudent['stName'].'｜'.$this->aALTheme['altName'];
		$this->template->set_global('pagetitle',$sTitle);

		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/'.$this->baseName, 'name'=>__('活動履歴'));
		$this->aBread[] = array('link'=>'/'.$this->baseName.'/list/'.$sID, 'name'=>__('入力状況').'｜'.$this->aALTheme['altName']);
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		$this->template->set_global('aALTheme',$this->aALTheme);
		$this->template->set_global('aALGoal',$this->aALGoal);
		$this->template->set_global('aALog',$this->aALog);

		$this->template->content = View::forge($view);
		$this->template->javascript = array('cl.t.kreport.js','cl.t.'.$this->baseName.'.js');
		return $this->template;
	}

	private function ALogThemeChecker($sAltID = null)
	{
		if (is_null($this->aClass))
		{
			return array('msg'=>__('講義情報が確認できませんでした。'),'url'=>'/t/index');
		}
		if (is_null($sAltID))
		{
			return array('msg'=>__('テーマ情報が送信されていません。'),'url'=>'/t/'.$this->baseName);
		}
		$result = Model_ALog::getALogThemeFromID($sAltID);
		if (!count($result))
		{
			return array('msg'=>__('指定されたテーマが見つかりません。').$sAltID,'url'=>'/t/'.$this->baseName);
		}
		$this->aALTheme = $result->current();

		return true;
	}

	private function ALogGoalChecker($sAltID = null, $sStID = null, $bRequire = true)
	{
		if (is_null($sAltID) || is_null($sStID))
		{
			return array('msg'=>__('必要な情報が送信されていません。'),'url'=>'/t/'.$this->baseName);
		}
		$result = Model_ALog::getALogGoal(array(array('altID','=',$sAltID),array('stID','=',$sStID)));
		if (!count($result))
		{
			if ($bRequire)
			{
				return array('msg'=>__('指定された目標が見つかりません。'),'url'=>'/t/'.$this->baseName);
			}
			else
			{
				return true;
			}
		}
		$this->aALGoal = $result->current();

		return true;
	}

	private function ALogChecker($sAltID = null,$iNO =null)
	{
		if (is_null($sAltID) || is_null($iNO))
		{
			return array('msg'=>__('必要な情報が送信されていません。'),'url'=>'/t/'.$this->baseName);
		}
		$result = Model_ALog::getALog(array(array('al.altID','=',$sAltID),array('al.no','=',$iNO)));
		if (!count($result))
		{
			return array('msg'=>__('指定された記録が見つかりません。'),'url'=>'/t/'.$this->baseName);
		}
		$this->aALog = $result->current();

		return true;
	}

}