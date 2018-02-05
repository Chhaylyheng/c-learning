<?php
class Controller_Org_Teacher extends Controller_Org_Base
{
	private $bn = 'org/teacher';

	private $aTeachers = null;
	private $aTeacherBase = array(
		't_mail'=>null,
		't_pass'=>null,
		't_name'=>null,
		't_dept'=>null,
		't_subject'=>null,
		't_uid'=>null,
		't_plan'=>0,
		's_date'=>null,
		'e_date'=>null,
	);
	private $aSearchCol = array(
		'tv.ttMail','tv.ttName','tv.ttDept','tv.ttSubject','tv.ttLoginID'
	);

	public function action_index()
	{
		$sTitle = __('先生一覧');
		# パンくずリスト生成
		$this->template->set_global('breadcrumbs',array(array('name'=>$sTitle)));
		# ページタイトル生成
		$this->template->set_global('pagetitle',$sTitle);

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
			array('gtID','=',$this->aGroup['gtID'])
		);

		$result = Model_Group::getGroupTeachers(array(array('gtp.gtID','=',$this->aGroup['gtID'])),null,array('tv.ttName'=>'asc'),$aWords);
		if (count($result))
		{
			$this->aTeachers = $result->as_array();
		}

		# カスタムボタン
		$aCustomMenu = array(
			array(
				'url'  => DS.$this->bn.DS.'add',
				'name' => __('先生の新規作成'),
			),
			array(
				'url'  => DS.$this->bn.DS.'csv',
				'name' => __('CSVから先生の登録'),
			),
			array(
				'url'  => '/org/output/teacherlist.csv',
				'name' => __('一覧のCSVダウンロード'),
				'icon' => 'fa-download',
				'option' => array(
					'target' => '_blank',
				)
			),
		);
		$this->template->set_global('aCustomMenu',$aCustomMenu);

		# チェックドロップダウン
		$aCheckDrop = array(
			'option' => 'teacher',
			'name' => __('チェックした先生に対する操作'),
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
				'url' => '/org/teacher',
		);
		$this->template->set_global('aSearchForm',$aSearchForm);
		$this->template->set_global('sWords',$sWords);

		$this->template->content = View::forge($this->bn.DS.'index');
		$this->template->content->set('aTeachers',$this->aTeachers);
		$this->template->javascript = array('cl.org.teacher.js');
		return $this->template;
	}

	public function action_add()
	{
		$this->template->javascript = array('cl.org.teacher.js');
		# タイトル
		$sTitle = __('先生の新規登録');
		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/teacher','name'=>__('先生一覧'));
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);
		$this->template->set_global('pagetitle',$sTitle);

		if (!Input::post(null,false))
		{
			$data = $this->aTeacherBase;
			$data['s_date'] = date('Y/m/d');
			$data['e_date'] = date('Y/m/d',strtotime('+1month'));

			$data['error'] = null;
			$this->template->content = View::forge($this->bn.DS.'edit',$data);
			return $this->template;
		}

		$aInput = Input::post();
		$val = Validation::forge();
		$val->add_callable('Helper_CustomValidation');

		$val->add('t_mail', __('メールアドレス'))
			->add_rule('required')
			->add_rule('valid_email')
			->add_rule('max_length',200)
			->add_rule('tmail_chk');

		if ($this->aGroup['gtLDAP'])
		{
			$aInput['t_pass'] = '';
		}
		else
		{
			if (trim($aInput['t_pass']))
			{
				$val->add('t_pass', __('パスワード'))
				->add_rule('required')
				->add_rule('trim')
				->add_rule('min_length', 8)
				->add_rule('max_length', 32)
				->add_rule('passwd_char');
			}
		}

		$val->add('t_name', __('氏名'))
		->add_rule('required')
		->add_rule('trim')
		->add_rule('max_length', 50);

		if (CL_CAREERTASU_MODE)
		{
			$val->add('s_date', __('利用開始日'))
				->add_rule('required')
				->add_rule('date')
				->add_rule('min_date',array(date('Y/m/d')));
			$val->add('e_date', __('利用終了日'))
				->add_rule('required')
				->add_rule('date')
				->add_rule('min_date',array($aInput['s_date']));
		}
		else
		{
			$val->add('t_dept', __('学部名'))
				->add_rule('max_length',50);

			$val->add('t_subject',__('学科名'))
				->add_rule('max_length',50);
		}

		if ($this->aGroup['gtLDAP'])
		{
			$val->add('t_uid', 'uid')
				->add_rule('required')
				->add_rule('trim')
				->add_rule('max_length', 50)
				->add_rule('tuid_chk', $this->aGroup['gtID']);
		}

		if (!$val->run())
		{
			$aInput['error'] = $val->error();

			$this->template->content = View::forge($this->bn.DS.'edit',$aInput);
			return $this->template;
		}

		$sFirst = null;
		if (trim($aInput['t_pass']))
		{
			$sFirst = $aInput['t_pass'];
		}
		else
		{
			$sFirst = strtolower(Str::random('distinct', 8));
		}
		$sPass = sha1($sFirst);
		$sHash = sha1($aInput['t_mail'].$sPass);

		$aInsert['teacher'] = array(
			'ttID'            => null,
			'ttMail'          => $aInput['t_mail'],
			'ttName'          => $aInput['t_name'],
			'ttDept'          => (isset($aInput['t_dept']))? $aInput['t_dept']:'',
			'ttSubject'       => (isset($aInput['t_subject']))? $aInput['t_subject']:'',
			'ttPass'          => $sPass,
			'ttFirst'         => $sFirst,
			'ttLoginNum'      => 0,
			'ttLastLoginDate' => '00000000000000',
			'ttLoginDate'     => '00000000000000',
			'ttPassMiss'      => 0,
			'ttHash'          => $sHash,
			'ttStatus'        => 1,
			'ttDate'          => date('YmdHis'),
			'ttCTPlan'        => (isset($aInput['t_plan']))? $aInput['t_plan']:0,
			'ttCTStart'       => (isset($aInput['s_date']))? $aInput['s_date']:'0000-00-00',
			'ttCTEnd'         => (isset($aInput['e_date']))? $aInput['e_date']:'0000-00-00',
		);
		$aInsert['group'] = array(
			'gtID' => $this->aGroup['gtID'],
			'gpDate' => date('YmdHis'),
		);
		if ($this->aGroup['gtLDAP'])
		{
			$aInsert['teacher']['ttLoginID'] = $aInput['t_uid'];
		}

		try
		{
			$sTtID = Model_Teacher::insertOrgTeacher($aInsert);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_ORG_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		Session::set('SES_ORG_NOTICE_MSG', __('先生「:name」を登録しました。',array('name'=>$aInput['t_name'])));
		Response::redirect(DS.$this->bn);
	}


	public function action_edit($sTtID = null)
	{
		$this->template->javascript = array('cl.org.teacher.js');
		if (is_null($sTtID))
		{
			Session::set('SES_ORG_ERROR_MSG', __('先生が指定されていません。'));
			Response::redirect(DS.$this->bn);
		}
		$result = Model_Group::getGroupTeachers(array(array('gtp.ttID','=',$sTtID),array('gtp.gtID','=',$this->aGroup['gtID'])));
		if (!count($result))
		{
			Session::set('SES_ORG_ERROR_MSG', __('対象の先生情報が見つかりません。'));
			Response::redirect(DS.$this->bn);
		}
		$aTeacher = $result->current();
		$this->template->set_global('aTeacher',$aTeacher);

		$aInput = array(
			't_mail'    => $aTeacher['ttMail'],
			't_name'    => $aTeacher['ttName'],
			't_pass'    => '',
			't_dept'    => $aTeacher['ttDept'],
			't_subject' => $aTeacher['ttSubject'],
			't_uid'     => $aTeacher['ttLoginID'],
			't_plan'    => $aTeacher['ttCTPlan'],
			's_date'    => ($aTeacher['ttCTStart'] != '0000-00-00')? date('Y/m/d',strtotime($aTeacher['ttCTStart'])):date('Y/m/d'),
			'e_date'    => ($aTeacher['ttCTEnd'] != '0000-00-00')? date('Y/m/d',strtotime($aTeacher['ttCTEnd'])):date('Y/m/d'),
			'error' => null,
		);

		# タイトル
		$sTitle = __('先生情報の編集');
		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/teacher','name'=>__('先生一覧'));
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);
		$this->template->set_global('pagetitle',$sTitle);

		if (!Input::post(null,false))
		{
			$this->template->content = View::forge($this->bn.DS.'edit',$aInput);
			return $this->template;
		}

		$aInput = Input::post();
		$val = Validation::forge();
		$val->add_callable('Helper_CustomValidation');

		$val->add('t_mail', __('メールアドレス'))
		->add_rule('required')
		->add_rule('valid_email')
		->add_rule('max_length',200)
		->add_rule('tmail_chk',$aTeacher['ttID']);

		if ($this->aGroup['gtLDAP'])
		{
			$aInput['t_pass'] = '';
		}
		else
		{
			if (trim($aInput['t_pass']))
			{
				$val->add('t_pass', __('パスワード'))
				->add_rule('required')
				->add_rule('trim')
				->add_rule('min_length', 8)
				->add_rule('max_length', 32)
				->add_rule('passwd_char');
			}
		}

		$val->add('t_name', __('氏名'))
		->add_rule('required')
		->add_rule('trim')
		->add_rule('max_length', 50);

		if (CL_CAREERTASU_MODE)
		{
			$val->add('s_date', __('利用開始日'))
				->add_rule('required')
				->add_rule('date');
			$val->add('e_date', __('利用終了日'))
				->add_rule('required')
				->add_rule('date')
				->add_rule('min_date',array($aInput['s_date']));
		}
		else
		{
			$val->add('t_dept', __('学部名'))
				->add_rule('max_length',50);
			$val->add('t_subject', __('学科名'))
				->add_rule('max_length',50);
		}

		if ($this->aGroup['gtLDAP'])
		{
			$val->add('t_uid', 'uid')
			->add_rule('required')
			->add_rule('trim')
			->add_rule('max_length', 50)
			->add_rule('tuid_chk', $this->aGroup['gtID'] , $aTeacher['ttID']);
		}

		if (!$val->run())
		{
			$aInput['error'] = $val->error();

			$this->template->content = View::forge($this->bn.DS.'edit',$aInput);
			return $this->template;
		}

		$aInsert = array(
			'ttMail'    => $aInput['t_mail'],
			'ttName'    => $aInput['t_name'],
			'ttDept'    => (isset($aInput['t_dept']))? $aInput['t_dept']:'',
			'ttSubject' => (isset($aInput['t_subject']))? $aInput['t_subject']:'',
			'ttHash'    => sha1($aInput['t_mail'].$aTeacher['ttPass']),
			'ttCTPlan'  => (isset($aInput['t_plan']))? $aInput['t_plan']:0,
			'ttCTStart' => (isset($aInput['s_date']))? $aInput['s_date']:'0000-00-00',
			'ttCTEnd'   => (isset($aInput['e_date']))? $aInput['e_date']:'0000-00-00',
		);
		if (trim($aInput['t_pass']))
		{
			$sFirst = $aInput['t_pass'];
			$sPass = sha1($sFirst);
			$sHash = sha1($aInput['t_mail'].$sPass);

			$aInsert['ttFirst'] = $sFirst;
			$aInsert['ttPass'] = sha1($sFirst);
			$aInsert['ttPassMiss'] = 0;
			$aInsert['ttPassDate'] = '0000-00-00';
			$aInsert['ttHash'] = sha1($aInput['t_mail'].$sPass);
		}
		if ($this->aGroup['gtLDAP'])
		{
			$aInsert['ttLoginID'] = $aInput['t_uid'];
		}

		try
		{
			$sTtID = Model_Teacher::updateTeacher($aTeacher['ttID'], $aInsert);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_ORG_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		Session::set('SES_ORG_NOTICE_MSG', __('先生「:name」の情報を更新しました。',array('name'=>$aInput['t_name'])));
		Response::redirect(DS.$this->bn);
	}

	public function action_csv()
	{
		# タイトル
		$sTitle = __('CSVから先生の登録');
		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/teacher','name'=>__('先生一覧'));
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);
		$this->template->set_global('pagetitle',$sTitle);

		if (!Input::post(null,false))
		{
			$data = array('error'=>null);
			$this->template->content = View::forge($this->bn.DS.'csv',$data);
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
			$st_csv = Upload::get_errors('tt_csv');
			if ($st_csv['errors'])
			{
				switch ($st_csv['errors'][0]['error'])
				{
					case Upload::UPLOAD_ERR_INI_SIZE:
					case Upload::UPLOAD_ERR_FORM_SIZE:
					case Upload::UPLOAD_ERR_MAX_SIZE:
						$aMsg['tt_csv'] = __('指定できるファイルのサイズは:sizeMBまでです。',array('size'=>CL_FILESIZE));
						break;
					case Upload::UPLOAD_ERR_EXTENSION:
					case Upload::UPLOAD_ERR_EXT_BLACKLISTED:
					case Upload::UPLOAD_ERR_EXT_NOT_WHITELISTED:
					case Upload::UPLOAD_ERR_TYPE_BLACKLISTED:
					case Upload::UPLOAD_ERR_TYPE_NOT_WHITELISTED:
					case Upload::UPLOAD_ERR_MIME_BLACKLISTED:
					case Upload::UPLOAD_ERR_MIME_NOT_WHITELISTED:
						$aMsg['tt_csv'] = __('登録できるファイルの拡張子は txt,csv のみです。');
						break;
					default:
						$aMsg['tt_csv'] = __('ファイルアップロードに失敗しました。');
						break;
				}
			}
		}
		if (!is_null($aMsg))
		{
			$data['error'] = $aMsg;
			$this->template->content = View::forge($this->bn.DS.'csv',$data);
			return $this->template;
		}

		$oFile = Upload::get_files('tt_csv');
		$aCSV = ClFunc_Common::getCSV($oFile['file']);
		if (!count($aCSV))
		{
			$data['error'] = array('tt_csv'=>__('登録するデータがCSVから取得できませんでした。'));
			$this->template->content = View::forge($this->bn.DS.'csv',$data);
			return $this->template;
		}
		if ($aCSV[0][0] == 'メールアドレス' || $aCSV[0][0] == 'Mail Address')
		{
			array_shift($aCSV);
		}

		$aInsert = null;
		$aUpdate = null;

		$aMail = array();
		$aMsg = null;
		$iAllCnt = 0;
		if (!empty($aCSV))
		{
			foreach ($aCSV as $iKey => $aS)
			{
				$aTemp = null;
				$iAllCnt++;
				if (count($aS) == 1 && !$aS[0])
				{
					continue;
				}
				if (!$aS[0] || !filter_var($aS[0], FILTER_VALIDATE_EMAIL))
				{
					$aMsg[] = __(':no件目のメールアドレス（:value）は、メールアドレスとして正しく入力されていません。',array('no'=>($iKey + 1),'value'=>htmlspecialchars($aS[0])));
				}
				$result = Model_Teacher::getTeacherFromMail($aS[0]);
				if (count($result))
				{
					$result = Model_Group::getGroupTeachers(array(array('tv.ttMail','=',$aS[0]),array('gtp.gtID','=',$this->aGroup['gtID'])));
					if (count($result))
					{
						$aTemp = $result->current();
						$aUpdate[$aTemp['ttID']] = $aS;
					}
					else
					{
						$aMsg[] = __(':no件目のメールアドレス（:value）は、団体外で既に利用されているため、登録できません。',array('no'=>($iKey + 1),'value'=>htmlspecialchars($aS[0])));
					}
				}
				else
				{
					$aInsert[$aS[0]] = $aS;
				}
				if (!ClFunc_Common::stringValidation($aS[1],false,array(8,32),array('alpha','numeric','dashes','dots','slashes'),true))
				{
					$aMsg[] = __(':no件目のパスワードを指定する場合は、8文字以上32文字以下で半角大小英数字と一部記号【.（ドット）_（アンダースコア）/（スラッシュ）-（ハイフン）】の2種類以上を組み合わせて入力してください。',array('no'=>($iKey + 1)));
				}
				if (!ClFunc_Common::stringValidation($aS[2],true,array(0,50)))
				{
					$aMsg[] = __(':no件目の氏名（:value）は、50文字以下で入力してください。',array('no'=>($iKey + 1),'value'=>htmlspecialchars($aS[2])));;
				}
				if (!ClFunc_Common::stringValidation($aS[3],false,array(0,50)))
				{
					$aMsg[] = __(':no件目の学部（:value）は、50文字以下で入力してください。',array('no'=>($iKey + 1),'value'=>htmlspecialchars($aS[3])));;
				}
				if (!ClFunc_Common::stringValidation($aS[4],false,array(0,50)))
				{
					$aMsg[] = __(':no件目の学科（:value）は、50文字以下で入力してください。',array('no'=>($iKey + 1),'value'=>htmlspecialchars($aS[4])));;
				}
				if ($this->aGroup['gtLDAP'])
				{
					if (isset($aS[5]) && $aS[5] != '')
					{
						$where = array(
							array('gtp.gtID','=',$this->aGroup['gtID']),
							array('tv.ttLoginID','=',$aS[5]),
						);
						if (!is_null($aTemp))
						{
							$where[] = array('gtp.ttID','!=',$aTemp['ttID']);
						}
						$result = Model_Group::getGroupTeachers($where);
						if (count($result))
						{
							$aMsg[] = __(':no件目のuid（:value）は、既に利用されているため、登録できません。',array('no'=>($iKey + 1),'value'=>htmlspecialchars($aS[5])));
						}
					}
				}
			}
		}
		else
		{
			$data['error'] = array('tt_csv'=>__('登録するデータがありません。'));
			$this->template->content = View::forge($this->bn.DS.'csv',$data);
			return $this->template;
		}
		if (!is_null($aMsg))
		{
			$data['error'] = array('valid'=>$aMsg);
			$this->template->content = View::forge($this->bn.DS.'csv',$data);
			return $this->template;
		}

		try
		{
			if (!is_null($aInsert))
			{
				$result = Model_Teacher::insertTeacherFromCSV($aInsert,$this->aGroup);
			}
			if (!is_null($aUpdate))
			{
				$result = Model_Teacher::updateTeacherFromCSV($aUpdate,$this->aGroup);
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

	public function action_delete($sTtID = null)
	{
		if (is_null($sTtID))
		{
			Response::redirect('/'.$this->bn);
		}
		$result = Model_Group::getGroupTeachers(array(array('gtp.ttID','=',$sTtID),array('gtp.gtID','=',$this->aGroup['gtID'])));
		if (!count($result))
		{
			Session::set('SES_ORG_ERROR_MSG', __('対象の先生情報が見つかりません。'));
			Response::redirect(DS.$this->bn);
		}
		$aTeacher = $result->current();
		$aTtImgs = ($aTeacher['ttImage'])? array($aTeacher['ttImage']):null;

		try
		{
			$result = Model_Teacher::deleteGroupTeacher(array($aTeacher['ttID']),$aTtImgs);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_ORG_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		Session::set('SES_ORG_NOTICE_MSG',__('先生「:name」を削除しました。',array('name'=>$aTeacher['ttName'])));
		Response::redirect(DS.$this->bn);
	}


	public function post_modify()
	{
		$aInput = Input::post();

		if (!isset($aInput['TeachChk']) || !count($aInput['TeachChk']))
		{
			Session::set('SES_ORG_ERROR_MSG', __('先生がチェックされていません。'));
			Response::redirect(DS.$this->bn);
		}

		$result = Model_Group::getGroupTeachers(array(array('gtp.ttID','IN',$aInput['TeachChk']),array('gtp.gtID','=',$this->aGroup['gtID'])));
		if (!count($result))
		{
			Session::set('SES_ORG_ERROR_MSG', __('対象の先生情報が見つかりません。'));
			Response::redirect(DS.$this->bn);
		}
		$aTtIDs = null;
		$aTtImgs = null;
		$sFin = null;
		foreach ($result as $aT)
		{
			$aTtIDs[] = $aT['ttID'];
			if ($aT['ttImage'])
			{
				$aTtImgs[] = $aT['ttImage'];
			}
			$sFin .= "\n".$aT['ttName'].'（'.$aT['ttMail'].'）';
		}

		switch ($aInput['mode'])
		{
			case 'delete':
				try
				{
					$result = Model_Teacher::deleteGroupTeacher($aTtIDs,$aTtImgs);
				}
				catch (Exception $e)
				{
					\Clfunc_Common::LogOut($e,__CLASS__);
					Session::set('SES_ORG_ERROR_MSG',$e->getMessage());
					Response::redirect($this->eRedirect);
				}

				Session::set('SES_ORG_NOTICE_MSG',__('下記の先生を削除しました。').'（'.__(':num名',array('num'=>count($aTtIDs))).'）'.$sFin);
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

		$result = Model_Group::getGroupTeachersClasses(array(array('gc.gtID','=',$this->aGroup['gtID']),array('tp.ctID','=',$sCID),),null,array('tt.ttName'=>'asc'));
		if (count($result))
		{
			$this->aTeachers = $result->as_array();
		}

		$sTitle = __('先生一覧').' ['.$aClass['ctName'].']';
		$aBreadCrumbs = array();

		# パンくずリスト生成
		$aBreadCrumbs[] = array('link' => '/class', 'name' => __('講義一覧'));
		$aBreadCrumbs[] = array('name' => $sTitle);
		$this->template->set_global('breadcrumbs',$aBreadCrumbs);
		# ページタイトル生成
		$this->template->set_global('pagetitle',$sTitle);

		# カスタムボタン
		$aCustomBtn = array(
			array(
				'url'  => '/org/teacher/classmodify/'.$sCID,
				'name' => __('先生の追加/削除'),
			),
		);
		$this->template->set_global('aCustomBtn',$aCustomBtn);

		$this->template->content = View::forge($this->bn.DS.'classlist');
		$this->template->content->set('aTeachers',$this->aTeachers);
		$this->template->content->set('aClass',$aClass);
		$this->template->javascript = array('cl.org.teacher.js');
		return $this->template;
	}

	public function action_classmodify($sCID = null)
	{
		if (is_null($sCID))
		{
			Response::redirect('/'.$this->bn);
		}

		$aWhere = array(
			array('gtID','=',$this->aGroup['gtID']),
		);

		$result = Model_Group::getGroupTeachers(array(array('gtp.gtID','=',$this->aGroup['gtID'])),null,array('tv.ttMail'=>'asc'));
		if (count($result))
		{
			$this->aTeachers = $result->as_array('ttID');
		}

		$aWhere[] = array('ctID','=',$sCID);

		$result = Model_Group::getGroupClasses($aWhere);
		if (!count($result))
		{
			Response::redirect('/'.$this->bn);
		}
		$aClass = $result->current();

		$aCTeachers = null;
		$result = Model_Group::getGroupTeachersClasses(array(array('gc.gtID','=',$this->aGroup['gtID']),array('tp.ctID','=',$sCID)),null,array('tt.ttMail'=>'asc'));
		if (count($result))
		{
			$aCTeachers = $result->as_array('ttID');
		}

		$sTitle = __('先生の追加/削除').' ['.$aClass['ctName'].']';
		$aBreadCrumbs = array();

		# パンくずリスト生成
		$aBreadCrumbs[] = array('name' => __('講義一覧'), 'link' => '/class');
		$aBreadCrumbs[] = array('name' => __('先生一覧').' ['.$aClass['ctName'].']', 'link' => '/teacher/classlist/'.$sCID);
		$aBreadCrumbs[] = array('name' => $sTitle);
		$this->template->set_global('breadcrumbs',$aBreadCrumbs);
		# ページタイトル生成
		$this->template->set_global('pagetitle',$sTitle);

		$this->template->content = View::forge($this->bn.DS.'classmodify');
		$this->template->content->set('aTeachers',$this->aTeachers);
		$this->template->content->set('aCTeachers',$aCTeachers);
		$this->template->content->set('aClass',$aClass);
		$this->template->javascript = array('cl.org.teacher.js');
		return $this->template;
	}
}


