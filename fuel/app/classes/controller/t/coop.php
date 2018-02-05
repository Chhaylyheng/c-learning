<?php
class Controller_T_Coop extends Controller_T_Baseclass
{
	private $baseName = 'coop';

	private $aCoopCate = array(
		'cc_name'=>null,
		'cc_stuwrite'=>1,
		'cc_anonymous'=>2,
		'cc_sturange'=>2,
	);
	private $aCoopBase = array(
		'c_title'=>null,
		'c_file1' =>null,
		'c_file2' =>null,
		'c_file3' =>null,
		'fileinfo1' =>array(
			'file'=>'',
			'name'=>'',
			'size'=>0,
			'isimg'=>false,
		),
		'fileinfo2' =>array(
			'file'=>'',
			'name'=>'',
			'size'=>0,
			'isimg'=>false,
		),
		'fileinfo3' =>array(
			'file'=>'',
			'name'=>'',
			'size'=>0,
			'isimg'=>false,
		),
		'c_text' =>null,
	);
	private $aSearchCol = array(
		'ci.cTitle','ci.cText','ft1.fName','ft2.fName','ft3.fName'
	);

	private $aCCategory = null;
	private $aCoop = null;

	public function before()
	{
		parent::before();
		\Clfunc_Common::ContractDetect($this->aTeacher, __CLASS__);

		$aAssist = null;
		$result = Model_Assistant::getAssistantPosition(array(array('ap.ctID','=',$this->aClass['ctID'])));
		if (count($result))
		{
			$aAssist = $result->current();
		}
		$this->template->set_global('aAssist',$aAssist);
	}

	public function action_index()
	{
		$aCoopCate = null;
		$result = Model_Coop::getCoopCategoryFromClass($this->aClass['ctID'],null,null,array('ccSort'=>'desc'));
		if (count($result))
		{
			$aCoopCate = $result->as_array();
		}
		else
		{
			if ($this->aClass['ctStatus'] > 0)
			{
				Response::redirect('/t/'.$this->baseName.'/catecreate/start');
			}
		}

		# タイトル
		$sTitle = __('協働板一覧');
		$this->template->set_global('pagetitle',$sTitle);

		# パンくずリスト生成
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		# カスタムメニュー
		$aCustomMenu = array(
			array(
				'url'  => '/t/'.$this->baseName.'/catecreate/',
				'name' => __('協働板の新規作成'),
				'show' => 1,
			),
		);
		$this->template->set_global('aCustomMenu',$aCustomMenu);

		$aAlready = null;
		$result = Model_Coop::getCoopCategoryAlreadyNum(array(array('caID','=',$this->sCurrentID)));
		if (count($result))
		{
			$aAlready = $result->as_array('ccID');
		}

		$this->template->content = View::forge('t/'.$this->baseName.'/index');
		$this->template->content->set('aCoopCate',$aCoopCate);
		$this->template->content->set('aAlready',$aAlready);
		$this->template->javascript = array('cl.t.coop.js');
		return $this->template;
	}


	public function action_catecreate($sMode = null)
	{
		$view = 't/'.$this->baseName.'/cateedit';
		# タイトル
		$sTitle = __('協働板の新規作成');
		$this->template->set_global('pagetitle',$sTitle);

		# パンくずリスト生成
		$this->aBread[] = array('link'=>DS.$this->baseName,'name'=>__('協働板一覧'));
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		if (!Input::post(null,false))
		{
			$data = $this->aCoopCate;
			$data['error'] = null;
			$data['cc_name'] = ($sMode == 'start')? __('全員参加の協働板'):'';
			$this->template->content = View::forge($view,$data);
			$this->template->javascript = array('cl.t.coop.js');
			return $this->template;
		}

		$aInput = Input::post();

		$val = Validation::forge();
		$val->add_callable('Helper_CustomValidation');
		$val->add_field('cc_name', __('協働板名'), 'required|max_length['.CL_TITLE_LENGTH.']');
		if (!$val->run())
		{
			$data = $aInput;
			$data['error'] = $val->error();
			$this->template->content = View::forge($view,$data);
			$this->template->javascript = array('cl.t.coop.js');
			return $this->template;
		}

		// 登録データ生成
		$aInsert = array(
			'ctID'        => $this->aClass['ctID'],
			'ccName'      => $aInput['cc_name'],
			'ccStuWrite'  => $aInput['cc_stuwrite'],
			'ccAnonymous' => $aInput['cc_anonymous'],
			'ccStuRange'  => $aInput['cc_sturange'],
			'ccDate'      => date('YmdHis'),
		);

		try
		{
			$result = Model_Coop::insertCoopCategory($aInsert);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		if ($aInput['cc_sturange'] == 1)
		{
			Session::set('SES_T_NOTICE_MSG',__('協働板を作成しました。').__('参加する学生を選択してください。'));
			Response::redirect('/t/'.$this->baseName.'/stuadd/'.$result);
		}
		Session::set('SES_T_NOTICE_MSG',__('協働板を作成しました。'));
		Response::redirect('/t/'.$this->baseName);
	}

	public function action_cateedit($sID = null)
	{
		$view = 't/'.$this->baseName.'/cateedit';

		$aChk = self::CoopCategoryChecker($sID);
		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}

		# タイトル
		$sTitle = __('協働板情報の編集');
		$this->template->set_global('pagetitle',$sTitle);

		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/'.$this->baseName,'name'=>__('協働板一覧'));
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		if (!Input::post(null,false))
		{
			$data = $this->aCoopCate;
			$data['cc_name'] = $this->aCCategory['ccName'];
			$data['cc_stuwrite'] = $this->aCCategory['ccStuWrite'];
			$data['cc_anonymous'] = $this->aCCategory['ccAnonymous'];
			$data['cc_sturange'] = $this->aCCategory['ccStuRange'];
			$data['error'] = null;
			$this->template->content = View::forge($view,$data);
			$this->template->content->set('aCCategory',$this->aCCategory);
			$this->template->javascript = array('cl.t.coop.js');
			return $this->template;
		}

		$aInput = Input::post();

		$val = Validation::forge();
		$val->add_callable('Helper_CustomValidation');
		$val->add_field('cc_name', __('協働板名'), 'required|max_length['.CL_TITLE_LENGTH.']');

		if (!$val->run())
		{
			$data = $aInput;
			$data['error'] = $val->error();
			$this->template->content = View::forge($view,$data);
			$this->template->content->set('aCCategory',$this->aCCategory);
			$this->template->javascript = array('cl.t.coop.js');
			return $this->template;
		}

		// 更新データ生成
		$aUpdate = array(
			'ccName'      => $aInput['cc_name'],
			'ccStuWrite'  => $aInput['cc_stuwrite'],
			'ccAnonymous' => $aInput['cc_anonymous'],
			'ccStuRange'  => $aInput['cc_sturange'],
		);

		try
		{
			$result = Model_Coop::updateCoopCategory($aUpdate,array(array('ccID','=',$sID)));
			$result = Model_Coop::changeStudentRange($this->aCCategory,$aInput['cc_sturange']);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		if ($aInput['cc_sturange'] == 1 && $this->aCCategory['ccStuRange'] != 1)
		{
			Session::set('SES_T_NOTICE_MSG',__('協働板情報を更新しました。').__('参加する学生を選択してください。'));
			Response::redirect('/t/'.$this->baseName.'/stuadd/'.$sID);
		}
		Session::set('SES_T_NOTICE_MSG',__('協働板情報を更新しました。'));
		Response::redirect('/t/'.$this->baseName);

	}


	public function action_catedelete($sID = null)
	{
		$aChk = self::CoopCategoryChecker($sID);
		if (is_array($aChk))
		{
			Session::set('SES_S_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}
		$aCoop = array();
		$result = \Model_Coop::getCoop(array(array('ci.ccID','=',$sID)));
		if (count($result))
		{
			$aCoop = $result->as_array();
		}

		try
		{
			$result = Model_Coop::deleteCoopCategory($sID,$this->aCCategory,$aCoop);
			foreach ($aCoop as $aM)
			{
				for ($i = 1; $i <= 3; $i++)
				{
					if ($aM['fID'.$i])
					{
						\Clfunc_Aws::deleteFile($this->sAwsSavePath,$aM['fID'.$i].'.'.$aM['fExt'.$i]);
					}
				}
			}
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		Session::set('SES_T_NOTICE_MSG',__('協働板を削除しました。'));
		Response::redirect('/t/'.$this->baseName);
	}

	public function action_stuadd($sID = null)
	{
		$aChk = self::CoopCategoryChecker($sID);
		if (is_array($aChk))
		{
			Session::set('SES_S_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}

		$aWhere = array(
			array('ccID','=',$sID),
		);

		$aCStu = null;
		$result = Model_Coop::getCoopStudents($aWhere,null,array('stClass'=>'asc','stNO'=>'asc'));
		if (count($result))
		{
			$aCStu = $result->as_array('stID');
		}

		$aStu = null;
		$result = Model_Student::getStudentFromClass($this->aClass['ctID'],null,null,array('st.stClass'=>'asc','st.stNO'=>'asc'));
		if (count($result))
		{
			foreach ($result as $aS)
			{
				if (!isset($aCStu[$aS['stID']]))
				{
					$aStu[$aS['stID']] = $aS;
				}
			}
		}

		# タイトル
		$sTitle = __('対象学生選択').'｜'.$this->aCCategory['ccName'];
		$this->template->set_global('pagetitle',$sTitle);

		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/'.$this->baseName,'name'=>__('協働板一覧'));
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		$this->template->content = View::forge('t/'.$this->baseName.'/stuadd');
		$this->template->content->set('aCCategory',$this->aCCategory);
		$this->template->content->set('aCStu',$aCStu);
		$this->template->content->set('aStu',$aStu);
		$this->template->javascript = array('cl.t.coop.js');
		return $this->template;
	}

	public function action_list($sID = null)
	{
		Session::set('SES_T_COOP_BACK',Input::uri());

		Session::delete('SES_T_COOP');

		$aChk = self::CoopCategoryChecker($sID);
		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}
		$this->template->set_global('aCCategory',$this->aCCategory);


		$aParents = null;
		$result = \Model_Coop::getCoop(array(array('ci.ccID','=',$sID),array('ci.cRoot','=',0)),null,array('ci.cSort'=>'desc'));
		if (count($result))
		{
			$aParents = $result->as_array();
		}

		$aCnt = null;
		$aCoops = null;
		$result = \Model_Coop::getCoop(array(array('ci.ccID','=',$sID),array('ci.cRoot','!=',0)),null,array('ci.cRoot'=>'asc','ci.cBranch'=>'asc','ci.cSort'=>'asc','ci.cDate'=>'asc'));
		if (count($result))
		{
			foreach ($result as $aC)
			{
				if (isset($aCnt['r'.$aC['cRoot']]))
				{
					$aCnt['r'.$aC['cRoot']]++;
				}
				else
				{
					$aCnt['r'.$aC['cRoot']] = 1;
				}

				if ($aC['cBranch'] == 0)
				{
					$aCoops[$aC['cRoot']][$aC['cNO']] = $aC;
				}
				else
				{
					if (isset($aCnt['p'.$aC['cBranch']]))
					{
						$aCnt['p'.$aC['cBranch']]++;
					}
					else
					{
						$aCnt['p'.$aC['cBranch']] = 1;
					}
					$aCoops[$aC['cRoot']][$aC['cBranch']]['children'][$aC['cNO']] = $aC;
				}
			}
		}

		$aAlready = null;
		$result = Model_Coop::getCoopAlready(array(array('ccID','=',$sID),array('caID','=',$this->aTeacher['ttID'])));
		if (count($result))
		{
			$aAlready = $result->as_array('cNO');
		}

		# タイトル
		$sTitle = $this->aCCategory['ccName'];
		$this->template->set_global('pagetitle',$sTitle);

		# パンくずリスト生成
		$this->aBread[] = array('name'=>__('協働板一覧'),'link'=>'/'.$this->baseName);
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		# カスタムボタン
		$aCustomBtn = array(
			array(
				'url'  => '/t/'.$this->baseName.'/thread/'.$sID,
				'name' => __('本文表示'),
				'show' => 0,
			),
			array(
				'url'  => '/t/'.$this->baseName.'/list/'.$sID,
				'name' => __('詳細情報'),
				'show' => 0,
			),
			array(
				'url'  => '#',
				'name' => __('スレッドを立てる'),
				'show' => 1,
				'icon' => 'fa-plus',
				'class' => array('CoopThreadCreate'),
				'option' => array('obj'=>$sID),
			),
		);
		$this->template->set_global('aCustomBtn',$aCustomBtn);

		$this->template->content = View::forge('t/'.$this->baseName.'/list');
		$this->template->content->set('aParents',$aParents);
		$this->template->content->set('aCoops',$aCoops);
		$this->template->content->set('aCnt',$aCnt);
		$this->template->content->set('aAlready',$aAlready);
		$this->template->javascript = array('cl.t.coop.js','cl.coop.js');
		return $this->template;
	}

	public function action_thread($sID = null, $iNO = null)
	{
		Session::set('SES_T_COOP_BACK',Input::uri());
		Session::delete('SES_T_COOP');

		$aWords = null;
		$sWords = null;
		$sW = Input::get('w',false);
		if ($sW)
		{
			$aW = \Clfunc_Common::getSearchWords($sW);
			$aWords = \Clfunc_Common::getSearchWhere($aW,$this->aSearchCol);
			$sWords = implode(' ', $aW);
		}
		$aSearchForm = array(
			'url' => '/t/'.$this->baseName.'/thread/'.$sID,
		);
		$this->template->set_global('aSearchForm',$aSearchForm);
		$this->template->set_global('sWords',$sWords);

		$aChk = self::CoopCategoryChecker($sID);
		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}
		$this->template->set_global('aCCategory',$this->aCCategory);

		$aPWhere = array(
			array('ci.ccID','=',$sID),
		);
		$aCWhere = $aPWhere;

		if (!is_null($iNO))
		{
			$aChk = self::CoopChecker($sID,$iNO);
			if (is_array($aChk))
			{
				Session::set('SES_T_ERROR_MSG',$aChk['msg']);
				Response::redirect($aChk['url']);
			}
			$aPWhere[] = array('ci.cNO','=',$iNO);
			$aCWhere[] = array('ci.cRoot','=',$iNO);
		}
		else
		{
			$aPWhere[] = array('ci.cRoot','=',0);
			$aCWhere[] = array('ci.cRoot','!=',0);
		}

		$aParents = null;
		$result = \Model_Coop::getCoop($aPWhere,null,array('ci.cSort'=>'desc'));
		if (count($result))
		{
			$aParents = $result->as_array();
		}

		$aSearchParents = null;
		$iCnt = 0;
		if (!is_null($aWords))
		{
			$result = \Model_Coop::getCoop($aPWhere,null,null,null,$aWords);
			if (count($result))
			{
				$iCnt += count($result);
				foreach ($result as $r)
				{
					$aSearchParents[$r['cNO']] = true;
				}
			}
			$result = \Model_Coop::getCoop($aCWhere,null,null,null,$aWords);
			if (count($result))
			{
				$iCnt += count($result);
				foreach ($result as $r)
				{
					$aSearchParents[$r['cRoot']] = true;
				}
			}
		}

		# タイトル
		$sTitle = $this->aCCategory['ccName'];
		$this->template->set_global('pagetitle',$sTitle);

		# パンくずリスト生成
		$this->aBread[] = array('name'=>__('協働板一覧'),'link'=>'/'.$this->baseName);
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		# カスタムボタン
		$aCustomBtn = array(
			array(
				'url'  => '/t/'.$this->baseName.'/thread/'.$sID,
				'name' => __('本文表示'),
				'show' => 0,
			),
			array(
				'url'  => '/t/'.$this->baseName.'/list/'.$sID,
				'name' => __('詳細情報'),
				'show' => 0,
			),
			array(
				'url'  => '#',
				'name' => __('スレッドを立てる'),
				'show' => 1,
				'icon' => 'fa-plus',
				'class' => array('CoopThreadCreate'),
				'option' => array('obj'=>$sID),
			),
		);

		if ($this->aCCategory['ccStuWrite'])
		{
			$aCustomBtn[] = array(
				'url'  => '/t/'.$this->baseName.'/threadtile/'.$sID,
				'name' => __('画像のタイル表示'),
				'show' => 0,
				'icon' => 'fa-object-group',
				'option' => array('target'=>'_blank'),
			);
		}

		$this->template->set_global('aCustomBtn',$aCustomBtn);

		$this->template->content = View::forge('t/'.$this->baseName.'/thread-reader');
		$this->template->content->set('aParents',$aParents);

		$this->template->content->set('aSearchParents',$aSearchParents);
		$this->template->content->set('iCnt',$iCnt);

		$this->template->javascript = array('cl.t.coop.js','cl.coop.js','jquery.mark.min.js');
		return $this->template;
	}

	public function action_threadtile($sID = null)
	{
		$url = '/t/'.$this->baseName.'/thread/'.$sID;

		$aChk = self::CoopCategoryChecker($sID);
		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}

		if (!$this->aCCategory['ccStuWrite'] || !$this->aCCategory['ccStuNum'])
		{
			Session::set('SES_T_ERROR_MSG',__('書込可能な学生が存在しない協働板はタイル表示できません。'));
			Response::redirect($url);
		}

		$result = Model_Coop::getCoopStudents(array(array('ccID','=',$sID)),null,array('stNO'=>'asc','stName'=>'acs','stLogin'=>'acs'));
		if (!count($result))
		{
			Session::set('SES_T_ERROR_MSG',__('書込可能な学生が存在しない協働板はタイル表示できません。'));
			Response::redirect($url);
		}
		$aStudent = $result->as_array('stID');

		$view = View::forge('template');
		$view->content = View::forge('t/'.$this->baseName.'/threadtile');
		$view->content->set('aCCategory',$this->aCCategory);
		$view->content->set('aStudent',$aStudent);
		$view->javascript = array('cl.t.coop.js','cl.t.coop.tile.js');
		$view->footer = View::forge('t/footer');
		return $view;
	}

	public function action_tile($sID = null, $iNO = null)
	{
		$url = '/t/'.$this->baseName.'/thread/'.$sID;

		$aChk = self::CoopCategoryChecker($sID);
		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}

		$aChk = self::CoopChecker($sID,$iNO);
		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}

		if (!$this->aCCategory['ccStuWrite'] || !$this->aCCategory['ccStuNum'])
		{
			Session::set('SES_T_ERROR_MSG',__('書込可能な学生が存在しない協働板はタイル表示できません。'));
			Response::redirect($url);
		}
		if ($this->aCoop['cRoot'] > 0)
		{
			Session::set('SES_T_ERROR_MSG',__('タイル表示にはスレッドを指定してください。'));
			Response::redirect($url);
		}

		$result = Model_Coop::getCoopStudents(array(array('ccID','=',$sID)),null,array('stNO'=>'asc','stName'=>'acs','stLogin'=>'acs'));
		if (!count($result))
		{
			Session::set('SES_T_ERROR_MSG',__('書込可能な学生が存在しない協働板はタイル表示できません。'));
			Response::redirect($url);
		}
		$aStudent = $result->as_array('stID');

		$view = View::forge('template');
		$view->content = View::forge('t/'.$this->baseName.'/tile');
		$view->content->set('aCCategory',$this->aCCategory);
		$view->content->set('aParent',$this->aCoop);
		$view->content->set('aStudent',$aStudent);
		$view->javascript = array('cl.t.coop.js','cl.t.coop.tile.js');
		$view->footer = View::forge('t/footer');
		return $view;
	}

	public function action_res($sID = null)
	{
		$url = '/t/'.$this->baseName.'/thread/'.$sID;
		$url = Session::get('SES_T_COOP_BACK',$url);

		$aChk = self::CoopCategoryChecker($sID);
		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}

		$sMsg = __('必要な情報を取得することができませんでした。');
		if (!Input::post(null,false))
		{
			Session::set('SES_T_ERROR_MSG',$sMsg);
			Response::redirect($url);
		}

		$aInput = Input::post();
		$iNO = $aInput['c_no'];

		if ($iNO != 0)
		{
			$aChk = self::CoopChecker($sID,$iNO);
			if (is_array($aChk))
			{
				Session::set('SES_T_ERROR_MSG',$aChk['msg']);
				Response::redirect($aChk['url']);
			}
		}
		else if ($aInput['mode'] != 'pcreate')
		{
			Session::set('SES_T_ERROR_MSG',__('必要な情報を取得することができませんでした。'));
			Response::redirect($aChk['url']);
		}

		$sCID = $this->aTeacher['ttID'];
		$sCName = $this->aTeacher['ttName'];
		$sCMail = $this->aTeacher['ttMail'];
		$sCSMail = $this->aTeacher['ttSubMail'];

		if (!is_null($this->aAssistant))
		{
			$sCID = $this->aAssistant['atID'];
			$sCName = $this->aAssistant['atName'];
			$sCMail = $this->aAssistant['atMail'];
			$sCSMail = '';
		}

		$sResMsg = '登録';

		if ($aInput['mode'] == 'pcreate')
		{
			$sResName = 'スレッド';
			$afID = null;
			try
			{
				$afID = \Clfunc_Common::CoopFileSave($aInput,$sCID,$this->sTempFilePath,$this->sAwsSavePath);
				$aInsert = array(
					'ccID'     => $sID,
					'cTitle'   => $aInput['c_title'],
					'fID1'     => $afID[1]['id'],
					'fID2'     => $afID[2]['id'],
					'fID3'     => $afID[3]['id'],
					'cText'    => $aInput['c_text'],
					'cCharNum' => mb_strlen($aInput['c_text']),
					'cID'      => $sCID,
					'cName'    => $sCName,
					'cDate'    => date('YmdHis'),
					'cRoot'    => 0,
					'fSumSize' => ($afID[1]['size'] + $afID[2]['size'] + $afID[3]['size']),
				);
				$result = \Model_Coop::insertCoop($aInsert);
			}
			catch (Exception $e)
			{
				if (!is_null($afID))
				{
					foreach ($afID as $i => $aF)
					{
						if (!$aF['id'])
						{
							continue;
						}
						$sfID = $aF['id'];
						$sFile = $aF['file'];
						\Clfunc_Aws::deleteFile($this->sAwsSavePath,$sFile);
						if ($iFileType == 1)
						{
							\Clfunc_Aws::deleteFile($this->sAwsSavePath, CL_PREFIX_THUMBNAIL.$sFile);
						}
						if ($iFileType == 2)
						{
							\Clfunc_Aws::deleteFile($this->sAwsSavePath,CL_PREFIX_ENCODE.$sfID.CL_AWS_ENCEXT);
							\Clfunc_Aws::deleteFile($this->sAwsSavePath,CL_PREFIX_THUMBNAIL.$sfID.'-00001.png');
						}
						\Model_File::deleteFile($sfID);
					}
				}

				\Clfunc_Common::LogOut($e,__CLASS__);
				Session::set('SES_T_ERROR_MSG',__('登録に失敗しました。').$e->getMessage());
				Response::redirect($url);
			}

			if (!is_null($afID))
			{
				foreach ($afID as $i => $aF)
				{
					if (!$aF['id'])
					{
						continue;
					}
					@unlink($aF['sourcefile']);
					@unlink($aF['thumbfile']);
				}
			}
		}
		else
		{
			Session::set('SES_T_ERROR_MSG',$sMsg);
			Response::redirect($url);
		}

		$iTeacher = (isset($aInput['mail-teacher']) && $aInput['mail-teacher'] == 1)? 1:0;
		$iStudent = (isset($aInput['mail-student']) && $aInput['mail-student'] == 1)? 1:0;
		if ($iTeacher || $iStudent)
		{
			$sUn = ($this->aCCategory['ccAnonymous'] == 0)? __('匿名'):$sCName;
			$aOptions = array(
				'cID'      => $sCID,
				'cMail'    => $sCMail,
				'cName'    => $sCName,
				'cSubMail' => $sCSMail,
				'files'    => $afID,
				'cTitle'   => $aInput['c_title'],
				'cText'    => $aInput['c_text'],
				'sWriter'  => '',
				'cUnknown' => $sUn,
			);
			\ClFunc_Mailsend::MailSendToCoop($this->aClass['ctID'],$this->aCCategory['ccID'],'t',null,(int)$iTeacher,(int)$iStudent,$aOptions);
		}

		Session::set('SES_T_NOTICE_MSG',__($sResName.'を'.$sResMsg.'しました。'));
		Response::redirect($url);
	}



	public function action_already($sID = null, $iNO = null)
	{
		$aChk = self::CoopCategoryChecker($sID);
		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}
		$aChk = self::CoopChecker($sID,$iNO);
		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}

		$aStudent = null;
		$result = Model_Coop::getCoopStudents(array(array('ccID','=',$sID)),null,array('stNO'=>'asc','stName'=>'acs','stLogin'=>'acs'));
		if ($iCoopCount = count($result))
		{
			$aRes = $result->as_array();
			foreach ($aRes as $aR)
			{
				$aStudent[$aR['stID']] = $aR;
			}
		}

		$iCnt = 0;
		$result = Model_Coop::getCoopAlready(array(array('cNO','=',$iNO)));
		if (count($result))
		{
			$aRes = $result->as_array();
			foreach ($aRes as $aR)
			{
				if (isset($aStudent[$aR['caID']]))
				{
					$aStudent[$aR['caID']]['already'] = $aR['caDate'];
					$iCnt++;
				}
			}
		}

		# タイトル
		$sTitle = $this->aCoop['cTitle'].' '.__('既読一覧').'（'.$iCnt.'/'.$iCoopCount.'）';
		$this->template->set_global('pagetitle',$sTitle);

		# パンくずリスト生成
		$this->aBread[] = array('name'=>__('協働板一覧'),'link'=>'/'.$this->baseName);
		$this->aBread[] = array('name'=>$this->aCCategory['ccName'],'link'=>'/'.$this->baseName.'/thread/'.$sID);
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		$this->template->content = View::forge('t/'.$this->baseName.'/already');
		$this->template->content->set('aStudent',$aStudent);
		$this->template->javascript = array('cl.t.coop.js');
		return $this->template;
	}


	public function action_threadpiece($sID = null, $iNO = null)
	{
		$this->template = View::forge('t/'.$this->baseName.'/thread-piece');

		$aChk = self::CoopCategoryChecker($sID);
		if (is_array($aChk))
		{
			$this->template = View::forge('piece-error');
			$this->template->set('sMsg',$aChk['msg']);
			return $this->template;
		}
		$this->template->set_global('aCCategory',$this->aCCategory);

		$aPWhere = array(
				array('ci.ccID','=',$sID),
		);
		$aCWhere = $aPWhere;

		$aChk = self::CoopChecker($sID,$iNO);
		if (is_array($aChk))
		{
			$this->template = View::forge('piece-error');
			$this->template->set('sMsg',$aChk['msg']);
			return $this->template;
		}
		$aPWhere[] = array('ci.cNO','=',$iNO);
		$aCWhere[] = array('ci.cRoot','=',$iNO);

		$aParents = null;
		$result = \Model_Coop::getCoop($aPWhere,null,array('ci.cSort'=>'desc'));
		if (count($result))
		{
			$aParents = $result->as_array();
		}

		$aCnt = null;
		$aCoops = null;
		$result = \Model_Coop::getCoop($aCWhere,null,array('ci.cRoot'=>'asc','ci.cBranch'=>'asc','ci.cSort'=>'asc','ci.cDate'=>'asc'));
		if (count($result))
		{
			foreach ($result as $aC)
			{
				if (isset($aCnt['r'.$aC['cRoot']]))
				{
					$aCnt['r'.$aC['cRoot']]++;
				}
				else
				{
					$aCnt['r'.$aC['cRoot']] = 1;
				}
				if ($aC['cBranch'] == 0)
				{
					$aCoops[$aC['cRoot']][$aC['cNO']] = $aC;
				}
				else
				{
					if (isset($aCnt['p'.$aC['cBranch']]))
					{
						$aCnt['p'.$aC['cBranch']]++;
					}
					else
					{
						$aCnt['p'.$aC['cBranch']] = 1;
					}
					$aCoops[$aC['cRoot']][$aC['cBranch']]['children'][$aC['cNO']] = $aC;
				}
			}
		}

		$aAlready = null;
		$result = Model_Coop::getCoopAlready(array(array('ccID','=',$sID),array('caID','=',$this->sCurrentID)));
		if (count($result))
		{
			$aAlready = $result->as_array('cNO');
		}
		$result = \Model_Coop::setCoopAlready($this->sCurrentID,$sID,$aParents);
		\Session::delete('CL_TEACH_UNREAD_'.$this->sCurrentID);

		$this->template->set_global('aParents',$aParents);
		$this->template->set_global('aCoops',$aCoops);
		$this->template->set_global('aCnt',$aCnt);
		$this->template->set_global('aAlready',$aAlready);
		return $this->template;
	}


	public function action_threadsearch($sID = null, $iNO = null)
	{
		$this->template = View::forge('t/'.$this->baseName.'/thread-search');

		$aChk = self::CoopCategoryChecker($sID);
		if (is_array($aChk))
		{
			$this->template = View::forge('piece-error');
			$this->template->set('sMsg',$aChk['msg']);
			return $this->template;
		}
		$this->template->set_global('aCCategory',$this->aCCategory);

		$aPWhere = array(
			array('ci.ccID','=',$sID),
		);
		$aCWhere = $aPWhere;

		$aChk = self::CoopChecker($sID,$iNO);
		if (is_array($aChk))
		{
			$this->template = View::forge('piece-error');
			$this->template->set('sMsg',$aChk['msg']);
			return $this->template;
		}
		$aPWhere[] = array('ci.cNO','=',$iNO);
		$aCWhere[] = array('ci.cRoot','=',$iNO);

		$aWords = null;
		$sW = Input::get('w',false);
		if ($sW)
		{
			$aW = \Clfunc_Common::getSearchWords($sW);
			$aWords = \Clfunc_Common::getSearchWhere($aW,$this->aSearchCol);
		}
		else
		{
			Response::redirect('/t/'.$this->baseName.'/threadpiece/'.$sID.DS.$iNO);
		}

		$aParents = null;
		$result = \Model_Coop::getCoop($aPWhere,null,array('ci.cSort'=>'desc'),null,$aWords);
		if (count($result))
		{
			$aParents = $result->as_array();
		}

		$aCoops = null;
		$result = \Model_Coop::getCoop($aCWhere,null,array('ci.cRoot'=>'asc','ci.cBranch'=>'asc','ci.cSort'=>'asc','ci.cDate'=>'asc'),null,$aWords);
		if (count($result))
		{
			$aCoops = $result->as_array();
		}

		if (is_null($aParents) && is_null($aCoops))
		{
			exit();
		}

		$this->template->set_global('aParents',$aParents);
		$this->template->set_global('aCoops',$aCoops);
		$this->template->set_global('sWords',$sW);
		$this->template->set_global('iNO',$iNO);
		return $this->template;
	}

	private function CoopCategoryChecker($sCcID = null)
	{
		if (is_null($this->aClass))
		{
			return array('msg'=>__('講義情報が確認できませんでした。'),'url'=>'/t/index');
		}
		if (is_null($sCcID))
		{
			return array('msg'=>__('協働板情報が送信されていません。'),'url'=>'/t/'.$this->baseName);
		}
		$result = Model_Coop::getCoopCategoryFromID($sCcID);
		if (!count($result))
		{
			return array('msg'=>__('指定された協働板が見つかりません。'),'url'=>'/t/'.$this->baseName);
		}
		$this->aCCategory = $result->current();

		return true;
	}

	private function CoopChecker($sCcID = null, $iCtNO = null)
	{
		if (is_null($this->aClass))
		{
			return array('msg'=>__('講義情報が確認できませんでした。'),'url'=>'/t/index');
		}
		if (is_null($sCcID) || is_null($iCtNO))
		{
			return array('msg'=>__('必要な情報が送信されていません。'),'url'=>'/t/'.$this->baseName);
		}
		$result = Model_Coop::getCoop(array(array('ci.ccID','=',$sCcID),array('ci.cNO','=',$iCtNO)));
		if (!count($result))
		{
			return array('msg'=>__('指定された記事が見つかりません。'),'url'=>'/t/'.$this->baseName);
		}
		$this->aCoop = $result->current();

		return true;
	}
}