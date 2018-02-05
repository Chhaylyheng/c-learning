<?php
class Controller_Org_Student extends Controller_Org_Base
{
	private $bn = 'org/student';

	private $aStudents = null;
	private $aStudentBase = array(
		's_login'=>null,
		's_pass'=>null,
		's_name'=>null,
		's_sex'=>null,
		's_no'=>null,
		's_school'=>null,
		's_dept'=>null,
		's_subject'=>null,
		's_year'=>null,
		's_class'=>null,
		's_course'=>null,
	);
	private $aSearchCol = array(
		'st.stLogin','st.stName','st.stNO','st.stSchool','st.stDept','st.stSubject','st.stYear','st.stClass','st.stCourse'
	);


	public function action_index()
	{
		$sTitle = __('学生一覧');
		$aBreadCrumbs = array();

		$aWords = null;
		$sWords = null;
		$sW = Input::get('w',false);
		if ($sW)
		{
			$aW = \Clfunc_Common::getSearchWords($sW);
			$aWords = \Clfunc_Common::getSearchWhere($aW,$this->aSearchCol);
			$sWords = implode(' ', $aW);
		}

		$aWhere = array(
			array('gsp.gtID','=',$this->aGroup['gtID'])
		);
		$result = Model_Group::getGroupStudents($aWhere,null,array('st.stNO'=>'asc'),$aWords);
		if (count($result))
		{
			$this->aStudents = $result->as_array();
		}

		# パンくずリスト生成
		$aBreadCrumbs[] = array('name' => $sTitle);
		$this->template->set_global('breadcrumbs',$aBreadCrumbs);
		# ページタイトル生成
		$this->template->set_global('pagetitle',$sTitle);

		# カスタムボタン
		$aCustomMenu = array(
			array(
				'url'  => DS.$this->bn.DS.'add',
				'name' => __('学生の新規作成'),
			),
			array(
				'url'  => DS.$this->bn.DS.'csv',
				'name' => __('CSVから学生の登録'),
			),
			array(
				'url'  => DS.$this->bn.DS.'csvstady',
				'name' => __('CSVから履修の登録'),
			),
			array(
				'url'  => '/org/output/studentlist.csv',
				'name' => __('一覧のCSVダウンロード'),
				'icon' => 'fa-download',
				'option' => array(
					'target' => '_blank',
				)
			),
			array(
				'url'  => '/org/output/studylist.csv',
				'name' => __('履修情報のCSVダウンロード'),
				'icon' => 'fa-download',
				'option' => array(
					'target' => '_blank',
				)
			),
		);
		$this->template->set_global('aCustomMenu',$aCustomMenu);

		# チェックドロップダウン
		$aCheckDrop = array(
			'option' => 'student',
			'name' => __('チェックした学生に対する操作'),
			'list' => array(
				array(
					'url' => '#',
					'class' => 'CheckDelete',
					'name' => __('削除する'),
				),
			),
		);
		$this->template->set_global('aCheckDrop',$aCheckDrop);

		$aSearchForm = array(
			'url' => '/org/student',
		);
		$this->template->set_global('aSearchForm',$aSearchForm);
		$this->template->set_global('sWords',$sWords);

		$this->template->content = View::forge($this->bn.DS.'index');
		$this->template->content->set('aStudents',$this->aStudents);
		$this->template->javascript = array('cl.org.student.js');
		return $this->template;
	}

	public function action_add()
	{
		$this->template->javascript = array('cl.school_select.js');

		$sView = 'edit';

		# タイトル
		$sTitle = __('学生の新規登録');
		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/student','name'=>__('学生一覧'));
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);
		$this->template->set_global('pagetitle',$sTitle);

		if (!Input::post(null,false))
		{
			$data = $this->aStudentBase;

			$data['error'] = null;
			$this->template->content = View::forge($this->bn.DS.$sView,$data);
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
			->add_rule('valid_string', array('alpha','numeric','dashes','dots','utf8'))
			->add_rule('slogin_chk');

		$val->add('s_name', __('氏名'))
			->add_rule('required')
			->add_rule('trim')
			->add_rule('max_length', 50);

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


		$val->add('s_dept', __('学部'))
		->add_rule('trim')
		->add_rule('max_length', 50);

		if (!CL_CAREERTASU_MODE)
		{
			$val->add('s_no', __('学籍番号'))
				->add_rule('trim')
				->add_rule('max_length', 20)
				->add_rule('valid_string', array('alpha','numeric','dashes','dots','utf8'));

			$val->add('s_subject', __('学科'))
				->add_rule('trim')
				->add_rule('max_length', 50);

			$val->add('s_year', __('学年'))
				->add_rule('trim')
				->add_rule('valid_string', array('numeric','utf8'));

			$val->add('s_class', __('クラス'))
				->add_rule('trim')
				->add_rule('max_length', 50);

			$val->add('s_course', __('コース'))
				->add_rule('trim')
				->add_rule('max_length', 50);
		} else {
			$val->add('s_school', __('学校'))
				->add_rule('required')
				->add_rule('trim')
				->add_rule('max_length',50);
		}

		if (!$val->run())
		{
			$aInput['error'] = $val->error();

			$this->template->content = View::forge($this->bn.DS.$sView,$aInput);
			return $this->template;
		}


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
			'stMail'          => '',
			'stLogin'         => $aInput['s_login'],
			'stName'          => $aInput['s_name'],
			'stSex'           => $aInput['s_sex'],
			'stNO'            => (isset($aInput['s_no']))? $aInput['s_no']:'',
			'stDept'          => (isset($aInput['s_dept']))? $aInput['s_dept']:'',
			'stSubject'       => (isset($aInput['s_subject']))? $aInput['s_subject']:'',
			'stYear'          => (isset($aInput['s_year']))? $aInput['s_year']:'',
			'stClass'         => (isset($aInput['s_class']))? $aInput['s_class']:'',
			'stCourse'        => (isset($aInput['s_course']))? $aInput['s_course']:'',
			'stPass'          => $sPass,
			'stFirst'         => $sFirst,
			'stLoginNum'      => 0,
			'stLastLoginDate' => '00000000000000',
			'stLoginDate'     => '00000000000000',
			'stPassMiss'      => 0,
			'stHash'          => $sHash,
			'stStatus'        => 1,
			'stDate'          => date('YmdHis'),
		);

		$sCmKCode = '';
		if (isset($aInput['s_school']))
		{
			$result = Model_College::getCollegeFromName($aInput['s_school']);
			$row = $result->current();
			if (!empty($row))
			{
				$sCmKCode = $row['cmKCode'];
			}
			else
			{
				$sCmKCode = Model_College::setCollege($aInput['s_school']);
			}
			$aInsert['cmKCode'] = $sCmKCode;
			$aInsert['stSchool'] = $aInput['s_school'];
		}

		try
		{
			$sStID = Model_Student::insertStudent($aInsert,null,$this->aGroup['gtID']);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_ORG_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		Session::set('SES_ORG_NOTICE_MSG', __('学生「:name」を登録しました。',array('name'=>$aInput['s_name'])));
		Response::redirect(DS.$this->bn);
	}

	public function action_edit($sStID = null)
	{
		$this->template->javascript = array('cl.school_select.js');

		$sView = 'edit';

		if (is_null($sStID))
		{
			Session::set('SES_ORG_ERROR_MSG', __('学生が指定されていません。'));
			Response::redirect(DS.$this->bn);
		}
		$result = Model_Group::getGroupStudents(array(array('gsp.stID','=',$sStID),array('gsp.gtID','=',$this->aGroup['gtID'])));
		if (!count($result))
		{
			Session::set('SES_ORG_ERROR_MSG', __('対象の学生情報が見つかりません。'));
			Response::redirect(DS.$this->bn);
		}
		$aStudent = $result->current();
		$this->template->set_global('aStudent',$aStudent);

		$aInput = array(
			's_login'   => $aStudent['stLogin'],
			's_pass'    => '',
			's_name'    => $aStudent['stName'],
			's_no'      => $aStudent['stNO'],
			's_school'  => $aStudent['stSchool'],
			's_dept'    => $aStudent['stDept'],
			's_subject' => $aStudent['stSubject'],
			's_year'    => $aStudent['stYear'],
			's_class'   => $aStudent['stClass'],
			's_course'  => $aStudent['stCourse'],
			's_sex'     => $aStudent['stSex'],
			'error' => null,
		);

		# タイトル
		$sTitle = __('学生情報の編集');
		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/student','name'=>__('学生一覧'));
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);
		$this->template->set_global('pagetitle',$sTitle);

		if (!Input::post(null,false))
		{
			$this->template->content = View::forge($this->bn.DS.$sView,$aInput);
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
			->add_rule('valid_string', array('alpha','numeric','dashes','dots','utf8'))
			->add_rule('slogin_chk',$aStudent['stID']);

		$val->add('s_name', __('氏名'))
			->add_rule('required')
			->add_rule('trim')
			->add_rule('max_length', 50);

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

		$val->add('s_dept', __('学部'))
			->add_rule('trim')
			->add_rule('max_length', 50);

		if (!CL_CAREERTASU_MODE)
		{
			$val->add('s_no', __('学籍番号'))
				->add_rule('trim')
				->add_rule('max_length', 20)
				->add_rule('valid_string', array('alpha','numeric','dashes','dots','utf8'));

			$val->add('s_subject', __('学科'))
				->add_rule('trim')
				->add_rule('max_length', 50);

			$val->add('s_year', __('学年'))
				->add_rule('trim')
				->add_rule('valid_string', array('numeric','utf8'));

			$val->add('s_class', __('クラス'))
				->add_rule('trim')
				->add_rule('max_length', 50);

			$val->add('s_course', __('コース'))
				->add_rule('trim')
				->add_rule('max_length', 50);
		} else {
			$val->add('s_school', __('学校'))
				->add_rule('required')
				->add_rule('trim')
				->add_rule('max_length',50);
		}

		if (!$val->run())
		{
			$aInput['error'] = $val->error();

			$this->template->content = View::forge($this->bn.DS.$sView,$aInput);
			return $this->template;
		}

		// 登録データ生成
		$aInsert = array(
			'stLogin'   => $aInput['s_login'],
			'stName'    => $aInput['s_name'],
			'stNO'      => $aInput['s_no'],
			'stSex'     => $aInput['s_sex'],
			'stDept'    => $aInput['s_dept'],
			'stSubject' => $aInput['s_subject'],
			'stYear'    => $aInput['s_year'],
			'stClass'   => $aInput['s_class'],
			'stCourse'  => $aInput['s_course'],
		);

		if (trim($aInput['s_pass']))
		{
			$sFirst = $aInput['s_pass'];
			$sPass = sha1($sFirst);
			$sHash = sha1($aInput['s_login'].$sPass);

			$aInsert['stFirst'] = $sFirst;
			$aInsert['stPass'] = sha1($sFirst);
			$aInsert['stPassMiss'] = 0;
			$aInsert['stPassDate'] = '0000-00-00';
			$aInsert['stHash'] = sha1($aInput['s_login'].$sPass);
		}

		$sCmKCode = '';
		if (isset($aInput['s_school']))
		{
			$result = Model_College::getCollegeFromName($aInput['s_school']);
			$row = $result->current();
			if (!empty($row))
			{
				$sCmKCode = $row['cmKCode'];
			}
			else
			{
				$sCmKCode = Model_College::setCollege($aInput['s_school']);
			}
			$aInsert['cmKCode'] = $sCmKCode;
			$aInsert['stSchool'] = $aInput['s_school'];
		}

		try
		{
			$sStID = Model_Student::updateStudent($aStudent['stID'], $aInsert);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_ORG_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		Session::set('SES_ORG_NOTICE_MSG', __('学生「:name」の情報を更新しました。',array('name'=>$aInput['s_name'])));
		Response::redirect(DS.$this->bn);
	}

	public function action_csv()
	{
		$sView = 'csv';

		# タイトル
		$sTitle = __('CSVから学生の登録');
		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/student','name'=>__('学生一覧'));
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);
		$this->template->set_global('pagetitle',$sTitle);

		if (!Input::post(null,false))
		{
			$data = array('error'=>null);
			$this->template->content = View::forge($this->bn.DS.$sView,$data);
			return $this->template;
		}

		$config = array(
			'max_size' => CL_FILESIZE*1024*1024,
			'path' => CL_UPPATH.DS.'temp',
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
			$this->template->content = View::forge($this->bn.DS.$sView,$data);
			return $this->template;
		}

		$oFile = Upload::get_files('st_csv');
		$aCSV = ClFunc_Common::getCSV($oFile['file']);
		if (!count($aCSV))
		{
			$data['error'] = array('st_csv'=>__('登録するデータがCSVから取得できませんでした。'));
			$this->template->content = View::forge($this->bn.DS.$sView,$data);
			return $this->template;
		}
		if ($aCSV[0][0] == 'ログインID' || $aCSV[0][0] == 'Login ID')
		{
			array_shift($aCSV);
		}

		$aInsert = null;
		$aUpdate = null;

		$aLogin = array();
		$aMsg = null;
		$iAllCnt = 0;
		if (!empty($aCSV))
		{
			foreach ($aCSV as $iKey => $aS)
			{
				$aTemp = null;
				$iAllCnt++;
				$i = 0;

				if (count($aS) == 1 && !$aS[0])
				{
					continue;
				}

				if (!ClFunc_Common::stringValidation($aS[$i],true,array(4,20),array('alpha','numeric','dashes','dots'),false))
				{
					$aMsg[] = __(':no件目のログインID（:value）は、4文字以上20文字以下で半角大小英数字と一部記号【.（ドット）_（アンダースコア）-（ハイフン）】で入力してください。',array('no'=>($iKey + 1),'value'=>htmlspecialchars($aS[$i])));
				}
				$result = Model_Student::getStudentFromLogin($aS[$i]);
				if (count($result))
				{
					$result = Model_Group::getGroupStudents(array(array('st.stLogin','=',$aS[$i]),array('gsp.gtID','=',$this->aGroup['gtID'])));
					if (count($result))
					{
						$aTemp = $result->current();
						$aUpdate[$aTemp['stID']] = $aS;
					}
					else
					{
						$aMsg[] = __(':no件目のログインID（:value）は、団体外で既に利用されているため、登録できません。',array('no'=>($iKey + 1),'value'=>htmlspecialchars($aS[$i])));
					}
				}
				else
				{
					$aInsert[$aS[$i]] = $aS;
				}

				$i++;
				if (!ClFunc_Common::stringValidation($aS[$i],false,array(8,32),array('alpha','numeric','dashes','dots','slashes'),true))
				{
					$aMsg[] = __(':no件目のパスワードを指定する場合は、8文字以上32文字以下で半角大小英数字と一部記号【.（ドット）_（アンダースコア）/（スラッシュ）-（ハイフン）】の2種類以上を組み合わせて入力してください。',array('no'=>($iKey + 1)));
				}

				$i++;
				if (!ClFunc_Common::stringValidation($aS[$i],true,array(0,50)))
				{
					$aMsg[] = __(':no件目の氏名（:value）は、50文字以下で入力してください。',array('no'=>($iKey + 1),'value'=>htmlspecialchars($aS[$i])));
				}

				$i++;
				if ((int)$aS[$i] > 2 || (int)$aS[$i] < 0)
				{
					$aMsg[] = __(':no件目の性別（:value）は、数値（0:指定なし、1:男性、2:女性）で入力してください。',array('no'=>($iKey + 1),'value'=>htmlspecialchars($aS[$i])));
				}

				$i++;
				if (!ClFunc_Common::stringValidation($aS[$i],false,array(0,20),array('alpha','numeric','dashes','dots')))
				{
					$aMsg[] = __(':no件目の学籍番号（:value）を指定する場合は、20文字以下で半角大小英数字と一部記号【.（ドット）_（アンダースコア）-（ハイフン）】で入力してください。',array('no'=>($iKey + 1),'value'=>htmlspecialchars($aS[$i])));
				}

				$i++;
				if (!ClFunc_Common::stringValidation($aS[$i],false,array(0,50)))
				{
					$aMsg[] = __(':no件目の学部（:value）は、50文字以下で入力してください。',array('no'=>($iKey + 1),'value'=>htmlspecialchars($aS[$i])));
				}

				$i++;
				if (!ClFunc_Common::stringValidation($aS[$i],false,array(0,50)))
				{
					$aMsg[] = __(':no件目の学科（:value）は、50文字以下で入力してください。',array('no'=>($iKey + 1),'value'=>htmlspecialchars($aS[$i])));
				}

				$i++;
				if ($aS[$i] != '' && !is_numeric($aS[$i]))
				{
					$aMsg[] = __(':no件目の学年（:value）は、数値で入力してください。',array('no'=>($iKey + 1),'value'=>htmlspecialchars($aS[$i])));
				}

				$i++;
				if (!ClFunc_Common::stringValidation($aS[$i],false,array(0,50)))
				{
					$aMsg[] = __(':no件目のクラス（:value）は、50文字以下で入力してください。',array('no'=>($iKey + 1),'value'=>htmlspecialchars($aS[$i])));
				}

				$i++;
				if (!ClFunc_Common::stringValidation($aS[$i],false,array(0,50)))
				{
					$aMsg[] = __(':no件目のコース（:value）は、50文字以下で入力してください。',array('no'=>($iKey + 1),'value'=>htmlspecialchars($aS[$i])));
				}

			}
		}
		else
		{
			$data['error'] = array('st_csv'=>__('登録するデータがありません。'));
			$this->template->content = View::forge($this->bn.DS.$sView,$data);
			return $this->template;
		}
		if (!is_null($aMsg))
		{
			$data['error'] = array('valid'=>$aMsg);
			$this->template->content = View::forge($this->bn.DS.$sView,$data);
			return $this->template;
		}

		try
		{
			if (!is_null($aInsert))
			{
				$result = Model_Student::insertStudentFromCSV($aInsert,null,$this->aGroup['gtID']);
			}
			if (!is_null($aUpdate))
			{
				$result = Model_Student::updateStudentFromCSV($aUpdate,$this->aGroup);
			}
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_ORG_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}


		Session::set('SES_ORG_NOTICE_MSG',__('CSVからの一括登録が完了しました。').__('（新規：:num1、更新：:num2）',array('num1'=>count($aInsert),'num2'=>count($aUpdate))));
		Response::redirect(DS.$this->bn);
	}

	public function action_csvstady()
	{
		$sView = 'csvstady';

		# タイトル
		$sTitle = __('CSVから履修の登録');
		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/student','name'=>__('学生一覧'));
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);
		$this->template->set_global('pagetitle',$sTitle);

		if (!Input::post(null,false))
		{
			$data = array('error'=>null);
			$this->template->content = View::forge($this->bn.DS.$sView,$data);
			return $this->template;
		}

		$config = array(
			'max_size' => CL_FILESIZE*1024*1024,
			'path' => CL_UPPATH.DS.'temp',
			'file_chmod' => 0666,
			'ext_whitelist' => array('txt', 'csv'),
			'type_whitelist' => array('text'),
		);

		Upload::process($config);

		# 添付処理
		$aMsg = null;
		if (!Upload::is_valid())
		{
			$st_csv = Upload::get_errors('se_csv');
			if ($st_csv['errors'])
			{
				switch ($st_csv['errors'][0]['error'])
				{
					case Upload::UPLOAD_ERR_INI_SIZE:
					case Upload::UPLOAD_ERR_FORM_SIZE:
					case Upload::UPLOAD_ERR_MAX_SIZE:
						$aMsg['se_csv'] = __('指定できるファイルのサイズは:sizeMBまでです。',array('size'=>CL_FILESIZE));
						break;
					case Upload::UPLOAD_ERR_EXTENSION:
					case Upload::UPLOAD_ERR_EXT_BLACKLISTED:
					case Upload::UPLOAD_ERR_EXT_NOT_WHITELISTED:
					case Upload::UPLOAD_ERR_TYPE_BLACKLISTED:
					case Upload::UPLOAD_ERR_TYPE_NOT_WHITELISTED:
					case Upload::UPLOAD_ERR_MIME_BLACKLISTED:
					case Upload::UPLOAD_ERR_MIME_NOT_WHITELISTED:
						$aMsg['se_csv'] = __('登録できるファイルの拡張子は txt,csv のみです。');
						break;
					default:
						$aMsg['se_csv'] = __('ファイルアップロードに失敗しました。');
						break;
				}
			}
		}
		if (!is_null($aMsg))
		{
			$data['error'] = $aMsg;
			$this->template->content = View::forge($this->bn.DS.$sView,$data);
			return $this->template;
		}

		$oFile = Upload::get_files('se_csv');
		$aCSV = ClFunc_Common::getCSV($oFile['file']);
		if (!count($aCSV))
		{
			$data['error'] = array('se_csv'=>__('登録するデータがCSVから取得できませんでした。'));
			$this->template->content = View::forge($this->bn.DS.$sView,$data);
			return $this->template;
		}
		if ($aCSV[0][0] == __('講義コード') || $aCSV[0][0] == '講義コード' || $aCSV[0][0] == 'Code')
		{
			array_shift($aCSV);
		}

		$aInsert = null;
		$aMsg = null;
		$iAllCnt = 0;
		if (!empty($aCSV))
		{
			foreach ($aCSV as $iKey => $aS)
			{
				$iAllCnt++;

				if (count($aS) == 1 && !$aS[0])
				{
					continue;
				}

				$aInsert[$iKey] = $aS;

				# 講義コード
				$i = 0;
				if (!ClFunc_Common::stringValidation($aS[$i],true,array(1,20),array('alpha','numeric','dashes')))
				{
					$aMsg[] = __(':no件目の講義コード（:value）は、1文字以上20文字以下で半角大小英数字と一部記号【_（アンダースコア）-（ハイフン）】で入力してください。',array('no'=>($iKey + 1),'value'=>htmlspecialchars($aS[$i])));
				}
				$result = Model_Group::getGroupClasses2(array(array('ct.ctCode','=',$aS[$i]),array('gcp.gtID','=',$this->aGroup['gtID'])));
				if (!count($result))
				{
					$aMsg[] = __(':no件目の講義コード（:value）が存在しません。',array('no'=>($iKey + 1),'value'=>htmlspecialchars($aS[$i])));
				}
				else
				{
					$aClass = $result->current();
					$aInsert[$iKey][$i] = $aClass['ctID'];
				}

				# ログインID
				$i++;
				if (!ClFunc_Common::stringValidation($aS[$i],true,array(4,20),array('alpha','numeric','dashes','dots')))
				{
					$aMsg[] = __(':no件目のログインID（:value）は、4文字以上20文字以下で半角大小英数字と一部記号【.（ドット）_（アンダースコア）-（ハイフン）】で入力してください。',array('no'=>($iKey + 1),'value'=>htmlspecialchars($aS[$i])));
				}
				$result = Model_Student::getStudentFromLogin($aS[$i]);
				if (!count($result))
				{
					$aMsg[] = __(':no件目のログインID（:value）が存在しません。',array('no'=>($iKey + 1),'value'=>htmlspecialchars($aS[$i])));
				}
				else
				{
					$aStudent = $result->current();
					$aInsert[$iKey][$i] = $aStudent['stID'];
				}

			}
		}
		else
		{
			$data['error'] = array('se_csv'=>__('登録するデータがありません。'));
			$this->template->content = View::forge($this->bn.DS.$sView,$data);
			return $this->template;
		}
		if (!is_null($aMsg))
		{
			$data['error'] = array('valid'=>$aMsg);
			$this->template->content = View::forge($this->bn.DS.$sView,$data);
			return $this->template;
		}

		try
		{
			$result = Model_Class::insertOrgClassStadyFromCSV($aInsert);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_ORG_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}


		Session::set('SES_ORG_NOTICE_MSG',__('CSVからの一括登録が完了しました。'));
		Response::redirect(DS.$this->bn);
	}

	public function action_delete($sStID = null)
	{
		if (is_null($sStID))
		{
			Response::redirect('/'.$this->bn);
		}
		$result = Model_Group::getGroupStudents(array(array('gsp.stID','=',$sStID),array('gsp.gtID','=',$this->aGroup['gtID'])));
		if (!count($result))
		{
			Session::set('SES_ORG_ERROR_MSG', __('対象の学生情報が見つかりません。'));
			Response::redirect(DS.$this->bn);
		}
		$aStudent = $result->current();

		try
		{
			$result = Model_Student::deleteGroupStudent(array($sStID));
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_ORG_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		Session::set('SES_ORG_NOTICE_MSG', __('学生「:name」を削除しました。',array('name'=>$aStudent['stName'])));
		Response::redirect(DS.$this->bn);
	}

	public function post_modify()
	{
		$aInput = Input::post();

		if (!isset($aInput['StuChk']) || !count($aInput['StuChk']))
		{
			Session::set('SES_ORG_ERROR_MSG', __('学生がチェックされていません。'));
			Response::redirect(DS.$this->bn);
		}

		$result = Model_Group::getGroupStudents(array(array('gsp.stID','IN',$aInput['StuChk']),array('gsp.gtID','=',$this->aGroup['gtID'])));
		if (!count($result))
		{
			Session::set('SES_ORG_ERROR_MSG', __('対象の学生情報が見つかりません。'));
			Response::redirect(DS.$this->bn);
		}
		$aStIDs = null;
		$sFin = null;
		foreach ($result as $aS)
		{
			$aStIDs[] = $aS['stID'];
			$sFin .= "\n".$aS['stName'].(($aS['stNO'])? ' ['.$aS['stNO'].']':'');
		}

		switch ($aInput['mode'])
		{
			case 'delete':
				try
				{
					$result = Model_Student::deleteGroupStudent($aStIDs);
				}
				catch (Exception $e)
				{
					\Clfunc_Common::LogOut($e,__CLASS__);
					Session::set('SES_ORG_ERROR_MSG',$e->getMessage());
					Response::redirect($this->eRedirect);
				}

				Session::set('SES_ORG_NOTICE_MSG',__('下記の学生を削除しました。').'（'.__(':num名',array('num'=>count($aStIDs))).'）'.$sFin);
				Response::redirect(DS.$this->bn);
			break;
			default:
				Session::set('SES_ORG_ERROR_MSG', __('操作の指定が誤っています。'));
				Response::redirect(DS.$this->bn);
			break;
		}

	}







	public function action_classlist($sCID = null)
	{
		if (is_null($sCID))
		{
			Response::redirect('/'.$this->bn);
		}

		$aWhere = array(
				array('gtID','=',$this->aGroup['gtID']),
				array('ctID','=',$sCID),
		);

		$result = Model_Group::getGroupClasses($aWhere);
		if (!count($result))
		{
			Response::redirect('/'.$this->bn);
		}
		$aClass = $result->current();

		$result = Model_Group::getGroupStudentsClasses($aWhere,null,array('stNO'=>'asc'));
		if (count($result))
		{
			$this->aStudents = $result->as_array();
		}

		$sTitle = __('学生一覧').' ['.$aClass['ctName'].']';
		$aBreadCrumbs = array();

		# パンくずリスト生成
		$aBreadCrumbs[] = array('name' => __('講義一覧'), 'link' => '/class');
		$aBreadCrumbs[] = array('name' => $sTitle);
		$this->template->set_global('breadcrumbs',$aBreadCrumbs);
		# ページタイトル生成
		$this->template->set_global('pagetitle',$sTitle);

		# カスタムボタン
		$aCustomBtn = array(
			array(
				'url'  => DS.$this->bn.DS.'listadd'.DS.$aClass['ctID'],
				'name' => __('既存学生の履修登録'),
			),
		);
		$this->template->set_global('aCustomBtn',$aCustomBtn);

		$this->template->content = View::forge($this->bn.DS.'classlist');
		$this->template->content->set('aStudents',$this->aStudents);
		$this->template->content->set('aClass',$aClass);
		$this->template->javascript = array('cl.org.student.js');
		return $this->template;
	}


	public function action_listadd($sCID = null)
	{
		$sView = 'listadd';

		if (is_null($sCID))
		{
			Response::redirect(DS.$this->bn);
		}

		$aWhere = array(
				array('gtID','=',$this->aGroup['gtID']),
				array('ctID','=',$sCID),
		);

		$result = Model_Group::getGroupClasses($aWhere);
		if (!count($result))
		{
			Response::redirect(DS.$this->bn);
		}
		$aClass = $result->current();
		$this->template->set_global('aClass',$aClass);

		# タイトル
		$sTitle = __('既存学生の履修登録');
		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/class','name'=>__('講義一覧'));
		$this->aBread[] = array('link'=>'/student/classlist/'.$aClass['ctID'],'name'=>__('学生一覧').' ['.$aClass['ctName'].']');
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);
		$this->template->set_global('pagetitle',$sTitle);

		if (!Input::post(null,false))
		{
			$data = array('cslist'=>'');
			$data['error'] = null;
			$this->template->content = View::forge($this->bn.DS.$sView,$data);
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
			$this->template->content = View::forge($this->bn.DS.$sView,$aInput);
			return $this->template;
		}

		$sGtID = $this->aGroup['gtID'];

		$result = Model_Group::getGroupStudents(array(array('gsp.gtID','=',$sGtID)));
		if (!count($result))
		{
			Session::set('SES_ORG_ERROR_MSG',__(':groupに所属している学生がいません。',array('group'=>$this->aGroup['gtName'])));
			Response::redirect(DS.$this->bn.DS.'classlist'.DS.$aClass['ctID']);
		}
		$aSCs = $result->as_array('stLogin');

		$aCESIDs = null;
		$result = Model_Student::getStudentFromClass($aClass['ctID']);
		if (count($result))
		{
			$aCESIDs = $result->as_array('stLogin');
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
			$this->template->content = View::forge($this->bn.DS.$sView,$aInput);
			return $this->template;
		}

		try
		{
			$iSEnt = Model_Class::entryClass($aClass['ctID'],$aSIDs);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_ORG_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		$sMsg = __(':num名（:id）の履修登録が完了しました。',array('num'=>$iSEnt,'id'=>implode(",", $aSLIDs)));
		$sBack = DS.$this->bn.DS.'classlist'.DS.$aClass['ctID'];
		if (!is_null($aNLIDs))
		{
			$sMsg .= __('残りの:idは、履修登録ができませんでした。指定の学生ログインIDを再度ご確認ください。',array('id'=>implode(",", $aNLIDs)));
			$sBack = DS.$this->bn.DS.'listadd'.DS.$aClass['ctID'];
		}

		Session::set('SES_ORG_NOTICE_MSG',$sMsg);
		Response::redirect($sBack);
	}

	public function action_remove($sCtID = null,$sStID = null)
	{
		if (is_null($sCtID) || is_null($sStID))
		{
			Response::redirect('/'.$this->bn);
		}
		$result = Model_Group::getGroupStudentsClasses(array(array('ctID','=',$sCtID),array('stID','=',$sStID),array('gtID','=',$this->aGroup['gtID'])));
		if (!count($result))
		{
			Session::set('SES_ORG_ERROR_MSG', __('対象の履修情報が見つかりません。'));
			Response::redirect(DS.$this->bn);
		}
		$aStudent = $result->current();

		try
		{
			$result = Model_Class::removeClass($sCtID,$sStID);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_ORG_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		Session::set('SES_ORG_NOTICE_MSG',__('学生「:name」を履修から削除しました。',array('name'=>$aStudent['stName'])));
		Response::redirect(DS.$this->bn.DS.'classlist'.DS.$sCtID);
	}

}


