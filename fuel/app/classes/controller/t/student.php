<?php
class Controller_T_Student extends Controller_T_Baseclass
{
	private $aStudentBase = array(
		's_login'=>null,
		's_pass'=>null,
		's_name'=>null,
		's_no'=>null,
		's_sex'=>null,
		's_dept'=>null,
		's_subject'=>null,
		's_year'=>null,
		's_class'=>null,
		's_course'=>null,
	);
	private $aSearchCol = array(
		'st.stLogin','st.stName','st.stNO','st.stDept','st.stSubject','st.stYear','st.stClass','st.stCourse'
	);

	public function action_index()
	{
		$aWords = null;
		$sWords = null;
		$sW = Input::get('w',false);
		if ($sW)
		{
			$aW = \Clfunc_Common::getSearchWords($sW);
			$aWords = \Clfunc_Common::getSearchWhere($aW,$this->aSearchCol);
			$sWords = implode(' ', $aW);
		}

		$aStudent = null;
		$aContact = null;
		$result = Model_Student::getStudentFromClass($this->aClass['ctID'],null,null,array('st.stNO'=>'asc','st.stName'=>'acs','st.stLogin'=>'acs'),$aWords);
		if (count($result))
		{
			$aRes = $result->as_array();
			foreach ($aRes as $aR)
			{
				$aStudent[$aR['stID']] = $aR;
				$aContact[$aR['stID']] = array('num'=>0, 'unread'=>0);
			}
		}
/*
		$result = Model_Contact::getContact($this->aClass['ctID'],null,null,null,array('co.stID'=>'asc'));
		if (count($result))
		{
			foreach ($result as $aC)
			{
				if (isset($aContact[$aC['stID']]))
				{
					$aContact[$aC['stID']]['num']++;

					if (!$aC['coTeach'] && !$aC['coRead'])
					{
						$aContact[$aC['stID']]['unread']++;
					}
				}
			}
		}
*/
		# タイトル
		$sTitle = __('学生管理');
		$this->template->set_global('pagetitle',$sTitle);

		# パンくずリスト生成
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		# カスタムメニュー
		$aCustomMenu = null;
		if (is_null($this->aGroup) || !($this->aGroup['gtTeacherAuthFlag'] & \Clfunc_Flag::T_AUTH_STUDENT))
		{
			$aCustomMenu[] = array(
				'url'  => '/t/student/add',
				'name' => __('学生の新規登録'),
				'show' => 1,
			);
			$aCustomMenu[] = array(
				'url'  => '/t/student/csv',
				'name' => __('CSVから学生の登録'),
				'show' => 1,
			);
		}
		if (is_null($this->aGroup) || !($this->aGroup['gtTeacherAuthFlag'] & \Clfunc_Flag::T_AUTH_STUDY))
		{
			if (!is_null($this->aGroup) || $this->iClassNum > 1)
			{
				$aCustomMenu[] = array(
					'url'  => '/t/student/listadd',
					'name' => __('既存学生の履修登録'),
					'show' => 1,
				);
			}
			if (!$this->aGroup['gtLDAP'])
			{
				$aCustomMenu[] = array(
					'url'  => Asset::get_file(CL_PDF_PREFIX.'_student_entry_manual.pdf', 'docs'),
					'name' => __('学生のログイン/登録URL配布資料印刷'),
					'show' => 1,
					'icon' => 'fa-print',
					'option' => array(
						'target' => '_blank',
					),
				);
			}
		}
		$aCustomMenu[] = array(
			'url'  => '/print/t/StuIdList/'.$this->aClass['ctID'],
			'name' => __('ID/パスワードの配付資料印刷'),
			'show' => 1,
			'icon' => 'fa-print',
			'option' => array(
				'target' => '_blank',
			),
		);
		$aCustomMenu[] = array(
			'url'  => '/t/output/studentlist.csv',
			'name' => __('一覧をCSVでダウンロード'),
			'show' => 0,
			'icon' => 'fa-download',
		);
		$this->template->set_global('aCustomMenu',$aCustomMenu);

		# カスタムボタン
		$aCustomBtn = null;

		if (is_null($this->aGroup) || !($this->aGroup['gtTeacherAuthFlag'] & \Clfunc_Flag::T_AUTH_STUDY))
		{
			if (!$this->aGroup['gtLDAP'])
			{
				$aCustomBtn[] = array(
					'url'  => Asset::get_file(CL_PDF_PREFIX.'_student_entry_manual.pdf', 'docs'),
					'name' => __('学生のログイン/登録URL配布資料印刷'),
					'show' => 1,
					'icon' => 'fa-print',
					'option' => array(
						'target' => '_blank',
					),
				);
			}
		}
		if ($this->aClass['ctGuestAuth'] > 0)
		{
			$aCustomBtn[] = array(
				'url'  => '/print/t/GuestLogin/'.$this->aClass['ctID'],
				'name' => __('ゲストログイン配布資料印刷'),
				'show' => 1,
				'icon' => 'fa-print',
				'option' => array(
						'target' => '_blank',
				),
			);
		}
		$this->template->set_global('aCustomBtn',$aCustomBtn);

		$aSearchForm = array(
			'url' => '/t/student',
		);
		$this->template->set_global('aSearchForm',$aSearchForm);
		$this->template->set_global('sWords',$sWords);

		$this->template->content = View::forge('t/student/index');
		$this->template->content->set('aStudent',$aStudent);
		$this->template->javascript = array('cl.t.student.js');
		return $this->template;
	}


	public function action_add()
	{
		if (!is_null($this->aGroup) && ($this->aGroup['gtTeacherAuthFlag'] & \Clfunc_Flag::T_AUTH_STUDENT))
		{
			Response::redirect('/t/student');
		}

		if (!$this->aClass['ctStatus'])
		{
			Session::set('SES_T_ERROR_MSG', __('終了した講義に学生を追加することはできません。'));
			Response::redirect('/t/student');
		}

		# タイトル
		$sTitle = __('学生の新規登録');
		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/student','name'=>__('学生管理'));
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);
		$this->template->set_global('pagetitle',$sTitle);

		if (!Input::post(null,false))
		{
			$data = $this->aStudentBase;

			$data['error'] = null;
			$this->template->content = View::forge('t/student/edit',$data);
			return $this->template;
		}

		$aInput = Input::post();
		$val = Validation::forge();
		$val->add_callable('Helper_CustomValidation');

		$val->add('s_login', __('ログインID'))
		->add_rule('required')
		->add_rule('trim')
		->add_rule('min_length', 4)
		->add_rule('max_length', 20)
		->add_rule('valid_string', array('alpha','numeric','dashes','dots','utf8'));

		if ($this->aGroup['gtLDAP'])
		{
			$aInput['s_pass'] = '';
		}
		else
		{
			if (trim($aInput['s_pass']))
			{
				$val->add('s_pass', __('パスワード'))
					->add_rule('required')
					->add_rule('trim')
					->add_rule('min_length', 8)
					->add_rule('max_length', 32)
					->add_rule('passwd_char');
			}
		}

		$val->add('s_name', __('氏名'))
			->add_rule('required')
			->add_rule('trim')
			->add_rule('max_length', 50);

		$val->add('s_no', __('学籍番号'))
			->add_rule('trim')
			->add_rule('max_length', 20)
			->add_rule('valid_string', array('alpha','numeric','dashes','dots','utf8'));

		$val->add('s_class', __('クラス'))
			->add_rule('trim')
			->add_rule('max_length', 50);

		$val->add('s_course', __('コース'))
			->add_rule('trim')
			->add_rule('max_length', 50);

		if (!CL_CAREERTASU_MODE)
		{
			$val->add('s_dept', __('学部'))
				->add_rule('trim')
				->add_rule('max_length', 50);

			$val->add('s_subject', __('学科'))
				->add_rule('trim')
				->add_rule('max_length', 50);

			$val->add('s_year', __('学年'))
				->add_rule('trim')
				->add_rule('valid_string', array('numeric','utf8'));
		}

		if (!$val->run())
		{
			$aInput['error'] = $val->error();

			$this->template->content = View::forge('t/student/edit',$aInput);
			return $this->template;
		}
		$result = Model_Student::getStudentFromLogin($aInput['s_login']);
		if (count($result))
		{
			$aInput['error'] = array('s_login'=>__('指定のログインIDは利用できません。'));
			$this->template->content = View::forge('t/student/edit',$aInput);
			return $this->template;
		}

		// 登録データ生成
		$sFirst = null;
		if (trim($aInput['s_pass']))
		{
			$sFirst = $aInput['s_pass'];
		}
		else
		{
			$sFirst = strtolower(Str::random('distinct', 8));
		}
		$sPass = sha1($sFirst);
		$sHash = sha1($aInput['s_login'].$sPass);

		$aInsert = array(
			'stID'            => null,
			'stLogin'         => $aInput['s_login'],
			'stPass'          => $sPass,
			'stFirst'         => $sFirst,
			'stMail'          => '',
			'stName'          => $aInput['s_name'],
			'stNO'            => $aInput['s_no'],
			'stSex'           => $aInput['s_sex'],
			'stDept'          => ((isset($aInput['s_dept']))? $aInput['s_dept']:''),
			'stSubject'       => ((isset($aInput['s_subject']))? $aInput['s_subject']:''),
			'stYear'          => ((isset($aInput['s_year']))? $aInput['s_year']:''),
			'stClass'         => $aInput['s_class'],
			'stCourse'        => $aInput['s_course'],
			'stLoginNum'      => 0,
			'stLastLoginDate' => '00000000000000',
			'stLoginDate'     => '00000000000000',
			'stPassDate'      => '00000000',
			'stPassMiss'      => 0,
			'stHash'          => $sHash,
			'stStatus'        => 1,
			'stDate'          => date('YmdHis'),
		);

		try
		{
			$sStID = Model_Student::insertStudent($aInsert,$this->aClass['ctID'],(($this->aTeacher['gtID'])? $this->aTeacher['gtID']:null));
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		Session::set('SES_T_ERROR_MSG', __('学生「:stu」を登録しました。',array('stu'=>$aInput['s_name'])));
		Response::redirect('/t/student');
	}

	public function action_listadd()
	{
		if (!is_null($this->aGroup) && ($this->aGroup['gtTeacherAuthFlag'] & \Clfunc_Flag::T_AUTH_STUDY))
		{
			Response::redirect('/t/student');
		}

		$view = 't/student/listadd';

		if (!$this->aClass['ctStatus'])
		{
			Session::set('SES_T_ERROR_MSG', __('終了した講義に学生を追加することはできません。'));
			Response::redirect('/t/student');
		}
		if (is_null($this->aGroup) && $this->iClassNum < 2)
		{
			Session::set('SES_T_ERROR_MSG', __('管理する講義が二つ以上必要です。'));
			Response::redirect('/t/student');
		}

		$aCESIDs = null;
		$result = Model_Student::getStudentFromClass($this->aClass['ctID']);
		if (count($result))
		{
			$aCESIDs = $result->as_array('stLogin');
		}


		# タイトル
		$sTitle = __('既存学生の履修登録');
		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/student','name'=>__('学生管理'));
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);
		$this->template->set_global('pagetitle',$sTitle);

		if (!Input::post(null,false))
		{
			$data = array('cslist' => '');
			$data['error'] = null;
			$this->template->content = View::forge($view,$data);
			return $this->template;
		}

		$aInput = Input::post(null,false);
		$array = explode("\n", $aInput['cslist']); // とりあえず行に分割
		$array = array_map('trim', $array); // 各行にtrim()をかける
		$array = array_filter($array, 'strlen'); // 文字数が0の行を取り除く
		$array = array_unique($array);
		$aSLIDs = array_values($array); // これはキーを連番に振りなおしてるだけ

		if (!count($aSLIDs))
		{
			$aInput['error'] = array('cslist'=>__('履修登録する学生ログインIDが指定されていません。'));
			$this->template->content = View::forge($view,$aInput);
			return $this->template;
		}

		if (!is_null($this->aGroup))
		{
			$sGtID = $this->aGroup['gtID'];

			$result = Model_Group::getGroupStudents(array(array('gsp.gtID','=',$sGtID)));
			if (!count($result))
			{
				Session::set('SES_T_ERROR_MSG',__(':groupに所属している学生がいません。',array('group'=>$this->aGroup['gtName'])));
				Response::redirect('/t/student');
			}
			$aSCs = $result->as_array('stLogin');
		}
		else
		{
			$result = Model_Class::getClassFromTeacher($this->aTeacher['ttID']);
			if (!count($result))
			{
				Session::set('SES_T_ERROR_MSG',__('管理講義を確認できませんでした。'));
				Response::redirect('/t/student');
			}
			$aCLs = $result->as_array('ctID');
			unset($aCLs[$this->aClass['ctID']]);

			if (!count($aCLs))
			{
				Session::set('SES_T_ERROR_MSG',__('現在管理している講義が一つしかありませんので、既存学生の履修登録はできません。'));
				Response::redirect('/t/student');
			}
			$aCIDs = array_keys($aCLs);

			$result = Model_Student::getStudentFromClass(null,array(array('sp.ctID','IN',$aCIDs)));
			if (!count($result))
			{
				Session::set('SES_T_ERROR_MSG',__('現在管理している講義に履修している学生のみ履修登録が可能です。'));
				Response::redirect('/t/student');
			}
			$aSCs = $result->as_array('stLogin');
		}

		$aSIDs = null;
		$aNLIDs = null;
		foreach ($aSLIDs as $i => $sS)
		{
			if (isset($aSCs[$sS]) && !isset($aCESIDs[$sS]))
			{
				$aSIDs[] = $aSCs[$sS]['stID'];
			}
			else
			{
				$aNLIDs[] = $sS;
				unset($aSLIDs[$i]);
			}
		}

		if (is_null($aSIDs))
		{
			$aInput['error'] = array('cslist'=>__('履修可能な学生ログインIDが指定されていません。'));
			$this->template->content = View::forge($view,$aInput);
			return $this->template;
		}


		try
		{
			$iSEnt = Model_Class::entryClass($this->aClass['ctID'],$aSIDs);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		$sMsg = __(':num名（:id）の履修登録が完了しました。',array('num'=>$iSEnt,'id'=>implode(",", $aSLIDs)));
		$sBack = '/t/student';
		if (!is_null($aNLIDs))
		{
			$sMsg .= __('残りの:idは、履修登録ができませんでした。指定の学生ログインIDを再度ご確認ください。',array('id'=>implode(",", $aNLIDs)));
			$sBack = '/t/student/listadd';
		}

		Session::set('SES_T_NOTICE_MSG',$sMsg);
		Response::redirect($sBack);
	}


	public function action_edit($sStID = null)
	{
		if (!is_null($this->aGroup) && ($this->aGroup['gtTeacherAuthFlag'] & \Clfunc_Flag::T_AUTH_STUDENT))
		{
			Response::redirect('/t/student');
		}

		if (is_null($sStID))
		{
			Session::set('SES_T_ERROR_MSG',__('学生の情報が確認できませんでした。'));
			Response::redirect('/t/student');
		}
		$aStu = null;
		$result = Model_Student::getStudentFromClass($this->aClass['ctID'],array(array('sp.stID','=',$sStID)));
		if (!count($result))
		{
			Session::set('SES_T_ERROR_MSG',__('指定された学生は本講義を履修していません。'));
			Response::redirect('/t/student');
		}
		$aStu = $result->current();

		# タイトル
		$sTitle = __('学生情報の変更');
		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/student','name'=>__('学生管理'));
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);
		$this->template->set_global('pagetitle',$sTitle);

		if (!Input::post(null,false))
		{
			$data = array(
				's_name'    => $aStu['stName'],
				's_no'      => $aStu['stNO'],
				's_sex'     => $aStu['stSex'],
				's_dept'    => $aStu['stDept'],
				's_subject' => $aStu['stSubject'],
				's_year'    => $aStu['stYear'],
				's_class'   => $aStu['stClass'],
				's_course'  => $aStu['stCourse'],
			);

			$data['error'] = null;
			$this->template->content = View::forge('t/student/edit',$data);
			$this->template->content->set('aStu',$aStu);
			return $this->template;
		}

		$aInput = Input::post();
		$val = Validation::forge();
		$val->add_callable('Helper_CustomValidation');

		$val->add('s_name', __('氏名'))
			->add_rule('required')
			->add_rule('trim')
			->add_rule('max_length', 50);

		$val->add('s_no', __('学籍番号'))
			->add_rule('trim')
			->add_rule('max_length', 20)
			->add_rule('valid_string', array('alpha','numeric','dashes','dots','utf8'));

		$val->add('s_class', __('クラス'))
			->add_rule('trim')
			->add_rule('max_length', 50);

		$val->add('s_course', __('コース'))
			->add_rule('trim')
			->add_rule('max_length', 50);

		if (!CL_CAREERTASU_MODE)
		{
			$val->add('s_dept', __('学部'))
				->add_rule('trim')
				->add_rule('max_length', 50);

			$val->add('s_subject', __('学科'))
				->add_rule('trim')
				->add_rule('max_length', 50);

			$val->add('s_year', __('学年'))
				->add_rule('trim')
				->add_rule('valid_string', array('numeric','utf8'));
		}

		if (!$val->run())
		{
			$aInput['error'] = $val->error();

			$this->template->content = View::forge('t/student/edit',$aInput);
			$this->template->content->set('aStu',$aStu);
			return $this->template;
		}

		// 登録データ生成
		$aInsert = array(
			'stName'          => $aInput['s_name'],
			'stNO'            => $aInput['s_no'],
			'stSex'           => $aInput['s_sex'],
			'stDept'          => ((isset($aInput['s_dept']))? $aInput['s_dept']:''),
			'stSubject'       => ((isset($aInput['s_subject']))? $aInput['s_subject']:''),
			'stYear'          => ((isset($aInput['s_year']))? $aInput['s_year']:''),
			'stClass'         => $aInput['s_class'],
			'stCourse'        => $aInput['s_course'],
		);

		try
		{
			$sStID = Model_Student::updateStudent($aStu['stID'],$aInsert);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		Session::set('SES_T_ERROR_MSG',__('学生「:stu」の情報を更新しました。',array('stu'=>$aInput['s_name'])));
		Response::redirect('/t/student');
	}


	public function action_remove($sStID = null)
	{
		if (!is_null($this->aGroup) && ($this->aGroup['gtTeacherAuthFlag'] & \Clfunc_Flag::T_AUTH_STUDY))
		{
			Response::redirect('/t/student');
		}

		if (is_null($sStID))
		{
			Session::set('SES_T_ERROR_MSG',__('学生の情報が確認できませんでした。'));
			Response::redirect('/t/student');
		}

		$aStu = null;
		$result = Model_Student::getStudentFromClass($this->aClass['ctID'],array(array('sp.stID','=',$sStID)));
		if (!count($result))
		{
			Session::set('SES_T_ERROR_MSG',__('指定された学生は本講義を履修していません。'));
			Response::redirect('/t/student');
		}
		$aStu = $result->current();

		try
		{
			$result = Model_Class::removeClass($this->aClass['ctID'],$sStID);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}
		Session::set('SES_T_NOTICE_MSG',__('学生「:stu」を履修から削除しました。',array('stu'=>$aStu['stName'])));
		Response::redirect('/t/student');
	}


	public function action_csv()
	{
		if (!is_null($this->aGroup) && ($this->aGroup['gtTeacherAuthFlag'] & \Clfunc_Flag::T_AUTH_STUDENT))
		{
			Response::redirect('/t/student');
		}

		if (!$this->aClass['ctStatus'])
		{
			Session::set('SES_T_ERROR_MSG',__('終了した講義に学生を追加することはできません。'));
			Response::redirect('/t/student');
		}

		# タイトル
		$sTitle = __('CSVから学生の登録');
		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/student','name'=>__('学生管理'));
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);
		$this->template->set_global('pagetitle',$sTitle);

		if (!Input::post(null,false))
		{
			$data = array('error'=>null);
			$this->template->content = View::forge('t/student/csv',$data);
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
			$st_csv = Upload::get_errors('st_csv');
			if ($st_csv['errors'])
			{
				switch ($st_csv['errors'][0]['error'])
				{
					case Upload::UPLOAD_ERR_INI_SIZE:
					case Upload::UPLOAD_ERR_FORM_SIZE:
					case Upload::UPLOAD_ERR_MAX_SIZE:
						$aMsg['st_csv'] = __('指定できるファイルのサイズは:sizeMBまでです。',array('size'=>CL_FILESIZE));
					break;
					case Upload::UPLOAD_ERR_EXTENSION:
					case Upload::UPLOAD_ERR_EXT_BLACKLISTED:
					case Upload::UPLOAD_ERR_EXT_NOT_WHITELISTED:
					case Upload::UPLOAD_ERR_TYPE_BLACKLISTED:
					case Upload::UPLOAD_ERR_TYPE_NOT_WHITELISTED:
					case Upload::UPLOAD_ERR_MIME_BLACKLISTED:
					case Upload::UPLOAD_ERR_MIME_NOT_WHITELISTED:
						$aMsg['st_csv'] = __('登録できるファイルの拡張子は txt,csv のみです。');
					break;
					default:
						$aMsg['st_csv'] = __('ファイルアップロードに失敗しました。');
					break;
				}
			}
		}
		if (!is_null($aMsg))
		{
			$data['error'] = $aMsg;
			$this->template->content = View::forge('t/student/csv',$data);
			return $this->template;
		}

		$oFile = Upload::get_files('st_csv');
		$aCSV = ClFunc_Common::getCSV($oFile['file']);
		if (!count($aCSV))
		{
			$data['error'] = array('st_csv'=>'登録するデータがCSVから取得できませんでした。');
			$this->template->content = View::forge('t/student/csv',$data);
			return $this->template;
		}
		if ($aCSV[0][0] == 'ログインID' || $aCSV[0][0] == 'Login-ID')
		{
			array_shift($aCSV);
		}

		$aLogin = array();
		$aMsg = null;
		$iAllCnt = 0;
		if (!empty($aCSV))
		{
			foreach ($aCSV as $iKey => $aS)
			{
				$iAllCnt++;
				if (!ClFunc_Common::stringValidation($aS[0],true,array(4,20),array('alpha','numeric','dashes','dots','singlequotes','doublequotes','slashes','quotes')))
				{
					$aMsg[] = __(':no件目のログインID（:value）は、4文字以上20文字以下で半角大小英数字と一部記号【.（ドット）_（アンダースコア）-（ハイフン）】で入力してください。',array('no'=>($iKey + 1),'value'=>htmlspecialchars($aS[0])));
				}
				$result = Model_Student::getStudentFromLogin($aS[0]);
				if (count($result))
				{
					$aMsg[] = __(':no件目のログインID（:value）は、既に利用されています。別のログインIDを指定してください。',array('no'=>($iKey + 1),'value'=>htmlspecialchars($aS[0])));
				}
				if (array_search($aS[0], $aLogin) !== false)
				{
					$aMsg[] = __(':no件目のログインID（:value）は、入力CSV内で既に指定されています。別のログインIDを指定してください。',array('no'=>($iKey + 1),'value'=>htmlspecialchars($aS[0])));
				}
				$aLogin[] = $aS[0];
				if (!ClFunc_Common::stringValidation($aS[1],false,array(8,32),array('alpha','numeric','dashes','dots','slashes'),true))
				{
					$aMsg[] = __(':no件目のパスワードを指定する場合は、8文字以上32文字以下で半角大小英数字と一部記号【.（ドット）_（アンダースコア）/（スラッシュ）-（ハイフン）】の2種類以上を組み合わせて入力してください。',array('no'=>($iKey + 1)));
				}
				if (!ClFunc_Common::stringValidation($aS[2],true,array(0,50)))
				{
					$aMsg[] = __(':no件目の氏名（:value）は、50文字以下で入力してください。',array('no'=>($iKey + 1),'value'=>htmlspecialchars($aS[2])));;
				}
				if ((int)$aS[3] > 2 || (int)$aS[3] < 0)
				{
					$aMsg[] = __(':no件目の性別（:value）は、数値（0:指定なし、1:男性、2:女性）で入力してください。',array('no'=>($iKey + 1),'value'=>htmlspecialchars($aS[3])));;
				}
				if (!ClFunc_Common::stringValidation($aS[4],false,array(0,20),array('alpha','numeric','dashes','dots')))
				{
					$aMsg[] = __(':no件目の学籍番号（:value）を指定する場合は、20文字以下で半角大小英数字と一部記号【.（ドット）_（アンダースコア）-（ハイフン）】で入力してください。',array('no'=>($iKey + 1),'value'=>htmlspecialchars($aS[4])));;
				}
				if (!ClFunc_Common::stringValidation($aS[5],false,array(0,50)))
				{
					$aMsg[] = __(':no件目の学部（:value）は、50文字以下で入力してください。',array('no'=>($iKey + 1),'value'=>htmlspecialchars($aS[5])));;
				}
				if (!ClFunc_Common::stringValidation($aS[6],false,array(0,50)))
				{
					$aMsg[] = __(':no件目の学科（:value）は、50文字以下で入力してください。',array('no'=>($iKey + 1),'value'=>htmlspecialchars($aS[6])));;
				}
				if ($aS[7] != '' && !is_numeric($aS[7]))
				{
					$aMsg[] = __(':no件目の学年（:value）は、数値で入力してください。',array('no'=>($iKey + 1),'value'=>htmlspecialchars($aS[7])));;
				}
				if (!ClFunc_Common::stringValidation($aS[8],false,array(0,50)))
				{
					$aMsg[] = __(':no件目のクラス（:value）は、50文字以下で入力してください。',array('no'=>($iKey + 1),'value'=>htmlspecialchars($aS[8])));;
				}
				if (!ClFunc_Common::stringValidation($aS[9],false,array(0,50)))
				{
					$aMsg[] = __(':no件目のコース（:value）は、50文字以下で入力してください。',array('no'=>($iKey + 1),'value'=>htmlspecialchars($aS[9])));;
				}
			}
		}
		else
		{
			$data['error'] = array('st_csv'=>__('登録するデータがありません。'));
			$this->template->content = View::forge('t/student/csv',$data);
			return $this->template;
		}
		if (!is_null($aMsg))
		{
			$data['error'] = array('valid'=>$aMsg);
			$this->template->content = View::forge('t/student/csv',$data);
			return $this->template;
		}

		try
		{
			$result = Model_Student::insertStudentFromCSV($aCSV,$this->aClass['ctID'],(($this->aTeacher['gtID'])? $this->aTeacher['gtID']:null));
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}


		Session::set('SES_T_NOTICE_MSG',__('CSVからの一括登録が完了しました。'));
		Response::redirect('t/student');
	}

	public function action_addtext()
	{
		$this->template->content = View::forge('t/student/addtext');
		return $this->template;
	}






}