<?php
class Controller_T_Drill extends Controller_T_Baseclass
{
	private $baseName = 'drill';
	private $bn = '';

	private $aDrillCate = array(
		'dc_name'=>null,
	);

	private $aDrillGroup = array(
		'dg_name'=>null,
	);

	private $aDrillBase = array(
		'd_title'=>null,
		'd_pubnum'=>10,
		'd_rand'=>1,
		'd_select_style'=>1,
		'd_query_rand'=>1,
	);

	private $aDCategory = null;
	private $aDQGroup = null;
	private $aDrill = null;
	private $aQuery = null;

	private $aTestBase = array(
		't_name'=>null, 't_auto_public'=>0,
		't_auto_s_date'=>null, 't_auto_e_date'=>null, 't_auto_s_time'=>null, 't_auto_e_time'=>null,
		't_qualify_score'=>0, 't_limit_time'=>0, 't_explain'=>null, 'tbImage'=>null, 't_query_rand'=>0,
		't_select_style'=>1, 't_query_rand'=>0,
		't_score_public'=>3, 't_com_public'=>0,
	);

	public function before()
	{
		$this->bn = 't/'.$this->baseName;

		// アップロード初期設定
		$this->config = array(
			'max_size' => (CL_IMGSIZE * 1024 * 1024),
			'ext_whitelist' => array('jpg','jpeg','png','gif'),
			'type_whitelist' => array('image'),
		);

		parent::before();
		\Clfunc_Common::ContractDetect($this->aTeacher, __CLASS__);
	}

	public function action_index()
	{
		$aDrillCate = null;
		$result = Model_Drill::getDrillCategoryFromClass($this->aClass['ctID'],null,null,array('dcSort'=>'desc'));
		if (count($result))
		{
			$aDrillCate = $result->as_array();
		}
		else
		{
			if ($this->aClass['ctStatus'] > 0)
			{
				Response::redirect(DS.$this->bn.'/catecreate');
			}
		}

		# タイトル
		$sTitle = __('ドリルカテゴリ一覧');
		$this->template->set_global('pagetitle',$sTitle);

		# パンくずリスト生成
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		# カスタムメニュー
		$aCustomMenu = array(
			array(
				'url'  => DS.$this->bn.'/catecreate/',
				'name' => __('ドリルカテゴリの新規作成'),
				'show' => 1,
			),
		);

		$this->template->set_global('aCustomMenu',$aCustomMenu);

		$this->template->content = View::forge('t/'.$this->baseName.'/index');
		$this->template->content->set('aDrillCate',$aDrillCate);
		$this->template->javascript = array('cl.t.'.$this->baseName.'.js');
		return $this->template;
	}


	public function action_catecreate()
	{
		$view = 't/'.$this->baseName.'/cateedit';
		# タイトル
		$sTitle = __('ドリルカテゴリの新規作成');
		$this->template->set_global('pagetitle',$sTitle);

		# パンくずリスト生成
		$this->aBread[] = array('link'=>DS.$this->baseName,'name'=>__('ドリルカテゴリ一覧'));
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		if (!Input::post(null,false))
		{
			$data = $this->aDrillCate;
			$data['error'] = null;
			$this->template->content = View::forge($view,$data);
			$this->template->javascript = array('cl.t.'.$this->baseName.'.js');
			return $this->template;
		}

		$aInput = Input::post();

		$val = Validation::forge();
		$val->add_callable('Helper_CustomValidation');
		$val->add_field('dc_name', __('カテゴリ名'), 'required|max_length['.CL_TITLE_LENGTH.']');
		if (!$val->run())
		{
			$data = $aInput;
			$data['error'] = $val->error();
			$this->template->content = View::forge($view,$data);
			$this->template->javascript = array('cl.t.'.$this->baseName.'.js');
			return $this->template;
		}

		// 登録データ生成
		$aInsert = array(
			'ctID'   => $this->aClass['ctID'],
			'dcName' => $aInput['dc_name'],
			'dcDate' => date('YmdHis'),
		);

		try
		{
			$result = Model_Drill::insertDrillCategory($aInsert);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		Session::set('SES_T_NOTICE_MSG',__('ドリルカテゴリを作成しました。'));
		Response::redirect(DS.$this->bn);
	}

	public function action_cateedit($sID = null)
	{
		$view = 't/'.$this->baseName.'/cateedit';

		$aChk = self::DrillCategoryChecker($sID);
		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}

		# タイトル
		$sTitle = __('ドリルカテゴリ情報の編集');
		$this->template->set_global('pagetitle',$sTitle);

		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/'.$this->baseName,'name'=>__('ドリルカテゴリ一覧'));
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		if (!Input::post(null,false))
		{
			$data = $this->aDrillCate;
			$data['dc_name'] = $this->aDCategory['dcName'];
			$data['error'] = null;
			$this->template->content = View::forge($view,$data);
			$this->template->content->set('aDCategory',$this->aDCategory);
			$this->template->javascript = array('cl.t.'.$this->baseName.'.js');
			return $this->template;
		}

		$aInput = Input::post();

		$val = Validation::forge();
		$val->add_callable('Helper_CustomValidation');
		$val->add_field('dc_name', __('カテゴリ名'), 'required|max_length['.CL_TITLE_LENGTH.']');

		if (!$val->run())
		{
			$data = $aInput;
			$data['error'] = $val->error();
			$this->template->content = View::forge($view,$data);
			$this->template->content->set('aDCategory',$this->aDCategory);
			$this->template->javascript = array('cl.t.'.$this->baseName.'.js');
			return $this->template;
		}

		// 更新データ生成
		$aUpdate = array(
			'dcName' => $aInput['dc_name'],
		);

		try
		{
			$result = Model_Drill::updateDrillCategory($aUpdate,array(array('dcID','=',$sID)));
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		Session::set('SES_T_NOTICE_MSG',__('ドリルカテゴリ情報を更新しました。'));
		Response::redirect(DS.$this->bn);

	}


	public function action_catedelete($sID = null)
	{
		$sImgPath = CL_UPPATH.DS.$sID;
		$aChk = self::DrillCategoryChecker($sID);
		if (is_array($aChk))
		{
			Session::set('SES_S_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}
		$aDrill = array();
		$result = \Model_Drill::getDrill(array(array('dcID','=',$sID)));
		if (count($result))
		{
			$aDrill = $result->as_array();
		}

		try
		{
			$result = Model_Drill::deleteDrillCategory($sID,$this->aDCategory,$aDrill);
			if (file_exists($sImgPath))
			{
				system('rm -rf '.$sImgPath);
			}
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		Session::set('SES_T_NOTICE_MSG',__('ドリルカテゴリを削除しました。'));
		Response::redirect(DS.$this->bn);
	}

	public function action_groupedit($sID = null)
	{
		$view = 't/'.$this->baseName.'/groupedit';

		$aChk = self::DrillCategoryChecker($sID);
		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}

		# タイトル
		$sTitle = __('問題グループの編集').'（'.$this->aDCategory['dcName'].'）';
		$this->template->set_global('pagetitle',$sTitle);

		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/'.$this->baseName,'name'=>__('ドリルカテゴリ一覧'));
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		$result = Model_Drill::getDrillQueryGroup(array(array('dcID','=',$sID)),null,array('dgSort'=>'asc'));
		if (count($result))
		{
			$this->aDQGroup = $result->as_array();
		}

		$this->template->content = View::forge($view);
		$this->template->content->set('aDQGroup',$this->aDQGroup);
		$this->template->javascript = array('cl.t.'.$this->baseName.'.js');
		return $this->template;
	}

	public function action_list($sID = null)
	{
		Session::delete('SES_T_DRILL');

		$aChk = self::DrillCategoryChecker($sID);
		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}

		$aDrill = null;
		$result = Model_Drill::getDrill(array(array('dcID','=',$sID)),null,array('dbSort'=>'desc'));
		if (count($result))
		{
			$aDrill = $result->as_array();
		}

		$aPut = null;
		$result = Model_Drill::getDrillPut(array(array('dcID','=',$sID)));
		if (count($result))
		{
			foreach ($result as $aP)
			{
				if (!isset($aPut[$aP['dbNO']]))
				{
					$aPut[$aP['dbNO']] = array('num'=>0, 'sum'=>0);
				}
				$aPut[$aP['dbNO']]['num'] += 1;
				$aPut[$aP['dbNO']]['sum'] += $aP['dpAvg'];
			}
		}

		# タイトル
		$sTitle = $this->aDCategory['dcName'];
		$this->template->set_global('pagetitle',$sTitle);

		# パンくずリスト生成
		$this->aBread[] = array('name'=>__('ドリルカテゴリ一覧'),'link'=>'/'.$this->baseName);
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		# カスタムボタン
		$aCustomBtn = array(
			array(
				'url'  => DS.$this->bn.'/create/'.$sID,
				'name' => __('ドリルの新規登録'),
				'show' => 1,
			),
		);
		$this->template->set_global('aCustomBtn',$aCustomBtn);

		$this->template->content = View::forge('t/'.$this->baseName.'/list');
		$this->template->content->set('aDrill',$aDrill);
		$this->template->content->set('aPut',$aPut);
		$this->template->javascript = array('cl.t.'.$this->baseName.'.js');
		return $this->template;
	}

	public function action_create($sID = null)
	{
		$aChk = self::DrillCategoryChecker($sID);
		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}

		# タイトル
		$sTitle = __('ドリルの新規登録');
		$this->template->set_global('pagetitle',$sTitle);

		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/'.$this->baseName, 'name'=>__('ドリルカテゴリ一覧'));
		$this->aBread[] = array('link'=>'/'.$this->baseName.'/list/'.$sID,'name'=>$this->aDCategory['dcName']);
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		$this->template->set_global('aDCategory',$this->aDCategory);

		if (!Input::post(null,false))
		{
			$data = $this->aDrillBase;
			$data['error'] = null;
			if ($aInput = unserialize(Session::get('SES_T_DRILL',false)))
			{
				$data = array_merge($data,$aInput);
			}
			$this->template->content = View::forge('t/'.$this->baseName.'/edit',$data);
			$this->template->javascript = array('cl.t.'.$this->baseName.'.js');
			return $this->template;
		}

		$aInput = Input::post();

		$val = Validation::forge();
		$val->add_callable('Helper_CustomValidation');
		$val->add_field('d_title', __('タイトル'), 'required|max_length['.CL_TITLE_LENGTH.']');
		$val->add_field('d_pubnum', __('出題数'), 'required|numeric|numeric_min[1]|numeric_max[100]');

		if (!$val->run())
		{
			$data = $this->aDrillBase;
			$data['error'] = $val->error();
			$data = array_merge($data,$aInput);
			$this->template->content = View::forge('t/'.$this->baseName.'/edit',$data);
			$this->template->javascript = array('cl.t.'.$this->baseName.'.js');
			return $this->template;
		}

		Session::set('SES_T_DRILL',serialize($aInput));
		Response::redirect(DS.$this->bn.'/check/'.$sID);
	}

	public function action_edit($sID = null,$iNO = null)
	{
		$aChk = self::DrillCategoryChecker($sID);
		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}
		$aChk = self::DrillChecker($sID,$iNO);
		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}

		# タイトル
		$sTitle = __('ドリル情報の編集');
		$this->template->set_global('pagetitle',$sTitle);

		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/'.$this->baseName, 'name'=>__('ドリルカテゴリ一覧'));
		$this->aBread[] = array('link'=>'/'.$this->baseName.'/list/'.$sID,'name'=>$this->aDCategory['dcName']);
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		$this->template->set_global('aDCategory',$this->aDCategory);
		$this->template->set_global('aDrill',$this->aDrill);

		if (!Input::post(null,false))
		{
			$data = $this->aDrillBase;
			$data['error'] = null;
			if (!$aInput = unserialize(Session::get('SES_T_DRILL',false)))
			{
				$aInput = array(
					'd_title'=>$this->aDrill['dbTitle'],
					'd_pubnum'=>$this->aDrill['dbPublicNum'],
					'd_rand'=>$this->aDrill['dbRand'],
					'd_select_style'=>$this->aDrill['dbQueryStyle'],
					'd_query_rand'=>$this->aDrill['dbQueryRand'],
				);
			}
			$data = array_merge($data,$aInput);
			$this->template->content = View::forge('t/'.$this->baseName.'/edit',$data);
			$this->template->javascript = array('cl.t.'.$this->baseName.'.js');
			return $this->template;
		}

		$aInput = Input::post();

		$val = Validation::forge();
		$val->add_callable('Helper_CustomValidation');
		$val->add_field('d_title', __('タイトル'), 'required|max_length['.CL_TITLE_LENGTH.']');
		$val->add_field('d_pubnum', __('出題数'), 'required|numeric|numeric_min[1]|numeric_max[100]');

		if (!$val->run())
		{
			$data = $this->aDrillBase;
			$data['error'] = $val->error();
			$data = array_merge($data,$aInput);
			$this->template->content = View::forge('t/'.$this->baseName.'/edit',$data);
			$this->template->javascript = array('cl.t.'.$this->baseName.'.js');
			return $this->template;
		}

		Session::set('SES_T_DRILL',serialize($aInput));
		Response::redirect(DS.$this->bn.'/check/'.$sID.DS.$iNO);
	}

	public function action_check($sID = null, $iNO = 0)
	{
		$aChk = self::DrillCategoryChecker($sID);
		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}
		$sMode = 'create';
		$sMTitle = '新規登録';

		if ($iNO)
		{
			$sMode = 'edit';
			$sMTitle = '編集';

			$aChk = self::DrillChecker($sID,$iNO);
			if (is_array($aChk))
			{
				Session::set('SES_T_ERROR_MSG',$aChk['msg']);
				Response::redirect($aChk['url']);
			}
		}

		$aInput = $this->aDrillBase;
		$aSes = unserialize(Session::get('SES_T_DRILL',false));
		if (!$aSes)
		{
			Session::set('SES_T_ERROR_MSG',__('登録内容が取得できませんでした。再度入力してください。'));
			Response::redirect(DS.$this->bn.'/'.$sMode.'/'.$sID.(($iNO)? DS.$iNO:''));
		}
		$aInput = array_merge($aInput,$aSes);

		$aPost = Input::post(null,false);
		if (isset($aPost['back']))
		{
			Response::redirect(DS.$this->bn.'/'.$sMode.'/'.$sID.(($iNO)? DS.$iNO:''));
		}

		try
		{
			if ($iNO)
			{
				$aUpdate = array(
					'dbTitle' => $aInput['d_title'],
					'dbPublicNum' => (int)$aInput['d_pubnum'],
					'dbRand'  => (int)$aInput['d_rand'],
					'dbQueryStyle' => (int)$aInput['d_select_style'],
					'dbQueryRand' => (int)$aInput['d_query_rand'],
					'dbDate'  => date('YmdHis'),
				);
				if ((int)$aInput['d_pubnum'] > (int)$this->aDrill['dbQueryNum'])
				{
					$aUpdate['dbPublic'] = 0;
				}
				$aWhere = array(
					array('dbNO','=',$iNO),
					array('dcID','=',$sID),
				);
				$result = \Model_Drill::updateDrill($aUpdate,$aWhere);
			}
			else
			{
				$aInsert = array(
					'dcID' => $sID,
					'dbTitle' => $aInput['d_title'],
					'dbPublicNum' => (int)$aInput['d_pubnum'],
					'dbRand'  => (int)$aInput['d_rand'],
					'dbQueryStyle' => (int)$aInput['d_select_style'],
					'dbQueryRand' => (int)$aInput['d_query_rand'],
					'dbDate'  => date('YmdHis'),
				);
				$iNO = \Model_Drill::insertDrill($aInsert);
			}
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		Session::delete('SES_T_DRILL');

		if (isset($aInput['finish']))
		{
			Session::set('SES_T_NOTICE_MSG',__('ドリルを'.(($iNO)? '更新':'登録').'しました。'));
			Response::redirect(DS.$this->bn.'/list/'.$sID);
		}
		else
		{
			Response::redirect(DS.$this->bn.'/querylist/'.$sID.DS.$iNO);
		}
	}

	public function action_delete($sID = null, $iNO = null)
	{
		$aChk = self::DrillCategoryChecker($sID);
		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}
		$aChk = self::DrillChecker($sID,$iNO);
		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}

		try
		{
			$result = Model_Drill::deleteDrill($this->aDrill);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		Session::set('SES_T_NOTICE_MSG',__('ドリルを削除しました。'));
		Response::redirect(DS.$this->bn.'/list/'.$sID);
	}


	public function action_querylist($sID = null,$iDbNO = null,$iDqNO = null)
	{
		$aQuery = null;
		$aQQ = null;
		$aImg = null;
		$aChoice = null;
		$aInput = null;
		$aMsg = null;

		$aChk = self::DrillCategoryChecker($sID);
		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}
		$aChk = self::DrillChecker($sID,$iDbNO);
		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}
		$aChk = self::DrillQueryGroup($sID);
		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}
		$this->template->set_global('aDQGroup',$this->aDQGroup);


		$result = Model_Drill::getDrillQuery(array(array('dcID','=',$sID),array('dbNO','=',$iDbNO)),null,array('dqSort'=>'asc'));
		if (count($result))
		{
			$aQuery = $result->as_array('dqSort');
		}
		$iDqNO = $aQuery[1]['dqNO'];

		# タイトル
		$sTitle = $this->aDrill['dbTitle'];
		$this->template->set_global('pagetitle',$sTitle);

		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/'.$this->baseName, 'name'=>__('ドリルカテゴリ一覧'));
		$this->aBread[] = array('link'=>'/'.$this->baseName.'/list/'.$sID,'name'=>$this->aDCategory['dcName']);
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		# カスタムボタン
		$aCustomBtn = array(
			array(
				'url'  => DS.$this->bn.'/csv/'.$sID.DS.$iDbNO,
				'name' => __('CSVから問題追加'),
				'show' => 1,
			),
			array(
				'url'  => DS.$this->bn.'/list/'.$sID,
				'name' => __('問題編集の終了'),
				'show' => 0,
			),
		);
		$this->template->set_global('aCustomBtn',$aCustomBtn);

		$this->template->content = View::forge('t/'.$this->baseName.'/querylist');
		$this->template->content->set('aDrill',$this->aDrill);
		$this->template->content->set('aQuery',$aQuery);
		$this->template->content->set('aQQ',$aQQ);
		$this->template->content->set('aInput',$aInput);
		$this->template->content->set('aChoice',$aChoice);
		$this->template->content->set('aImg',$aImg);
		$this->template->content->set('aMsg',$aMsg);
		$this->template->content->set('iDqNO',$iDqNO);
		$this->template->javascript = array('cl.t.'.$this->baseName.'.js');
		return $this->template;
	}

	public function action_queryedit($sID = null, $iDbNO = null)
	{
		$aQuery = null;

		$aChk = self::DrillCategoryChecker($sID);
		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}
		$aChk = self::DrillChecker($sID,$iDbNO);
		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}
		$aChk = self::DrillQueryGroup($sID);
		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}
		$this->template->set_global('aDQGroup',$this->aDQGroup);

		# タイトル
		$sTitle = $this->aDrill['dbTitle'];
		$this->template->set_global('pagetitle',$sTitle);

		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/'.$this->baseName, 'name'=>__('ドリルカテゴリ一覧'));
		$this->aBread[] = array('link'=>'/'.$this->baseName.'/list/'.$sID,'name'=>$this->aDCategory['dcName']);
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		# カスタムボタン
		$aCustomBtn = array(
			array(
				'url'  => DS.$this->bn.'/csv/'.$sID.DS.$iDbNO,
				'name' => __('CSVから問題追加'),
				'show' => 1,
			),
			array(
				'url'  => DS.$this->bn.'/list/'.$sID,
				'name' => __('問題編集の終了'),
				'show' => 0,
			),
		);
		$this->template->set_global('aCustomBtn',$aCustomBtn);

		$result = Model_Drill::getDrillQuery(array(array('dcID','=',$sID)),null,array('dqSort'=>'asc'));
		if (count($result))
		{
			$aQuery = $result->as_array();
		}
		if (!Input::post(null,false))
		{
			Response::redirect(DS.$this->bn.'/querylist/'.$sID.DS.$iDbNO);
		}

		$aMsg = null;
		$aQQ = null;
		$aImg = null;
		$aInput = Input::post();
		$aChoice = null;
		$aRight = null;
		$aRightText = null;
		$sTempPath = CL_UPPATH.DS.$this->aDrill['dcID'].DS.$this->aDrill['dbNO'].DS.$aInput['qSort'].'_tmp';

		if ($aInput['qNo'])
		{
			$result = Model_Drill::getDrillQuery(array(array('dcID','=',$sID),array('dbNO','=',$iDbNO),array('dqNO','=',$aInput['qNo'])));
			if (count($result))
			{
				$aQQ = $result->current();
			}
		}

		$sGroup = preg_replace(CL_WHITE_TRIM_PTN, '$1', $aInput['qGroup']);
		$sText = preg_replace(CL_WHITE_TRIM_PTN, '$1', $aInput['qText']);
		if ($sText == '')
		{
			$aMsg[] = __('問題文が入力されていません。');
		}

		Upload::process($this->config);

		# 添付処理
		$qImage = Upload::get_errors('qImage');
		if ($qImage)
		{
			if ($qImage['error'])
			{
				switch ($qImage['errors'][0]['error'])
				{
					case Upload::UPLOAD_ERR_INI_SIZE:
					case Upload::UPLOAD_ERR_FORM_SIZE:
					case Upload::UPLOAD_ERR_MAX_SIZE:
						$aMsg[] = __(':nameに登録できるファイルのサイズは:sizeMBまでです。',array('name'=>__('問題'),'num'=>CL_IMGSIZE));
						break;
					case Upload::UPLOAD_ERR_EXTENSION:
					case Upload::UPLOAD_ERR_EXT_BLACKLISTED:
					case Upload::UPLOAD_ERR_EXT_NOT_WHITELISTED:
					case Upload::UPLOAD_ERR_TYPE_BLACKLISTED:
					case Upload::UPLOAD_ERR_TYPE_NOT_WHITELISTED:
					case Upload::UPLOAD_ERR_MIME_BLACKLISTED:
					case Upload::UPLOAD_ERR_MIME_NOT_WHITELISTED:
						$aMsg[] = __(':nameに登録できるファイルは画像（JPG,JPEG,GIF,PNG）のみです。',array('name'=>__('問題')));
						break;
					case Upload::UPLOAD_ERR_NO_FILE:
						# ファイルを指定していない
						break;
					default:
						$aMsg[] = __(':nameのファイルアップロードに失敗しました。',array('name'=>__('問題')));
						break;
				}
			}
		}
		$qImage = Upload::get_files('qImage');
		if ($qImage)
		{
			$aInput['dqImage'] = 'base.'.$qImage['extension'];
			$sThumbImg = CL_Q_SMALL_PREFIX.$aInput['dqImage'];
			$sTempImg = 'base_tmp.'.$qImage['extension'];

			ClFunc_Common::chkDir($sTempPath,true);
			File::rename($qImage['file'], $sTempPath.DS.$sTempImg);
			ClFunc_Common::OrientationFixedImage($sTempPath.DS.$sTempImg);
			$image = Image::load($sTempPath.DS.$sTempImg);
			$image->config('quality',CL_Q_IMG_QUALITY)->resize(CL_Q_IMG_SIZE,(CL_Q_IMG_SIZE / 4 * 3),true)->save($sTempPath.DS.$aInput['dqImage'],0666);
			$image->config('quality',CL_Q_SMALL_QUALITY)->resize(CL_Q_SMALL_SIZE,(CL_Q_SMALL_SIZE / 4 * 3),true)->save($sTempPath.DS.$sThumbImg,0666);
			File::delete($sTempPath.DS.$sTempImg);
		}

		# 添付処理
		$qeImage = Upload::get_errors('qExplainImage');
		if ($qeImage)
		{
			if ($qeImage['error'])
			{
				switch ($qeImage['errors'][0]['error'])
				{
					case Upload::UPLOAD_ERR_INI_SIZE:
					case Upload::UPLOAD_ERR_FORM_SIZE:
					case Upload::UPLOAD_ERR_MAX_SIZE:
						$aMsg[] = __(':nameに登録できるファイルのサイズは:sizeMBまでです。',array('name'=>__('解説'),'num'=>CL_IMGSIZE));
						break;
					case Upload::UPLOAD_ERR_EXTENSION:
					case Upload::UPLOAD_ERR_EXT_BLACKLISTED:
					case Upload::UPLOAD_ERR_EXT_NOT_WHITELISTED:
					case Upload::UPLOAD_ERR_TYPE_BLACKLISTED:
					case Upload::UPLOAD_ERR_TYPE_NOT_WHITELISTED:
					case Upload::UPLOAD_ERR_MIME_BLACKLISTED:
					case Upload::UPLOAD_ERR_MIME_NOT_WHITELISTED:
						$aMsg[] = __(':nameに登録できるファイルは画像（JPG,JPEG,GIF,PNG）のみです。',array('name'=>__('解説')));
						break;
					case Upload::UPLOAD_ERR_NO_FILE:
						# ファイルを指定していない
						break;
					default:
						$aMsg[] = __(':nameのファイルアップロードに失敗しました。',array('name'=>__('解説')));
						break;
				}
			}
		}
		$qeImage = Upload::get_files('qExplainImage');
		if ($qeImage)
		{
			$aInput['dqExplainImage'] = 'explain.'.$qeImage['extension'];
			$sThumbImg = CL_Q_SMALL_PREFIX.$aInput['dqExplainImage'];
			$sTempImg = 'explain_tmp.'.$qeImage['extension'];

			ClFunc_Common::chkDir($sTempPath,true);
			File::rename($qeImage['file'], $sTempPath.DS.$sTempImg);
			ClFunc_Common::OrientationFixedImage($sTempPath.DS.$sTempImg);
			$image = Image::load($sTempPath.DS.$sTempImg);
			$image->config('quality',CL_Q_IMG_QUALITY)->resize(CL_Q_IMG_SIZE,(CL_Q_IMG_SIZE / 4 * 3),true)->save($sTempPath.DS.$aInput['dqExplainImage'],0666);
			$image->config('quality',CL_Q_SMALL_QUALITY)->resize(CL_Q_SMALL_SIZE,(CL_Q_SMALL_SIZE / 4 * 3),true)->save($sTempPath.DS.$sThumbImg,0666);
			File::delete($sTempPath.DS.$sTempImg);
		}

		if ($aInput['qType'] != 2)
		{
			$aFile = null;
			$aTemp = null;
			$aImg = null;
			$aDelImg = null;
			$bNone = true;
			$iCnt = 1;
			$aChoice = null;
			$aRight = null;
			for ($i = 1; $i <= 50; $i++)
			{
				$iRight = isset($aInput['qRight'.$i]);
				$sChoice = $aInput['qChoice'.$i];
				$sName = 'qChoice'.$i.'Image';
				$sChoice = preg_replace(CL_WHITE_TRIM_PTN, '$1', $sChoice);
				if ($sChoice != '')
				{
					$aChoice[$iCnt] = $sChoice;
					if ($iRight)
					{
						$aRight[$iCnt] = 1;
					}
					$bNone = false;

					$qChoiceImg = Upload::get_errors($sName);
					if ($qChoiceImg)
					{
						switch ($qChoiceImg['errors'][0]['error'])
						{
							case Upload::UPLOAD_ERR_INI_SIZE:
							case Upload::UPLOAD_ERR_FORM_SIZE:
							case Upload::UPLOAD_ERR_MAX_SIZE:
								$aMsg[] = __(':nameに登録できるファイルのサイズは:sizeMBまでです。',array('name'=>__('選択肢').$iCnt,'num'=>CL_IMGSIZE));
							break;
							case Upload::UPLOAD_ERR_EXTENSION:
							case Upload::UPLOAD_ERR_EXT_BLACKLISTED:
							case Upload::UPLOAD_ERR_EXT_NOT_WHITELISTED:
							case Upload::UPLOAD_ERR_TYPE_BLACKLISTED:
							case Upload::UPLOAD_ERR_TYPE_NOT_WHITELISTED:
							case Upload::UPLOAD_ERR_MIME_BLACKLISTED:
							case Upload::UPLOAD_ERR_MIME_NOT_WHITELISTED:
								$aMsg[] = __(':nameに登録できるファイルは画像（JPG,JPEG,GIF,PNG）のみです。',array('name'=>__('選択肢').$iCnt));
							break;
							case Upload::UPLOAD_ERR_NO_FILE:
								# ファイルを指定していない
							break;
							default:
								$aMsg[] = __(':nameのファイルアップロードに失敗しました。',array('name'=>__('選択肢').$iCnt));
							break;
						}
					}
					$qChoiceImg = Upload::get_files($sName);
					if ($qChoiceImg)
					{
						$aFile[$iCnt] = $qChoiceImg['name'];
						$aTemp[$iCnt] = '';

						$aImg[$iCnt] = $iCnt.'.'.$qChoiceImg['extension'];
						$sThumbImg = CL_Q_SMALL_PREFIX.$aImg[$iCnt];
						$sTempImg = $iCnt.'_tmp.'.$qChoiceImg['extension'];

						ClFunc_Common::chkDir($sTempPath,true);
						File::rename($qChoiceImg['file'], $sTempPath.DS.$sTempImg);
						ClFunc_Common::OrientationFixedImage($sTempPath.DS.$sTempImg);
						$image = Image::load($sTempPath.DS.$sTempImg);
						$image->config('quality',CL_Q_IMG_QUALITY)->resize(CL_Q_IMG_SIZE,(CL_Q_IMG_SIZE / 4 * 3),true)->save($sTempPath.DS.$aImg[$iCnt],0666);
						$image->config('quality',CL_Q_SMALL_QUALITY)->resize(CL_Q_SMALL_SIZE,(CL_Q_SMALL_SIZE / 4 * 3),true)->save($sTempPath.DS.$sThumbImg,0666);
						File::delete($sTempPath.DS.$sTempImg);
					}
					else if ($aInput["dqChoiceImage".$i] != "")
					{
						$aImg[$iCnt] = $aInput["dqChoiceImage".$i];
					}
					$iCnt++;
				}
				else if ($aQQ['dqChoiceImg'.$i])
				{
					$aDelImg[$i] = $aQQ['dqChoiceImg'.$i];
				}
			}
			if ($bNone)
			{
				$aMsg[] = __('選択肢を一つ以上指定してください。');
			}
			if (is_null($aRight))
			{
				$aMsg[] = __('正解を一つ以上選択してください。');
			}
			else if ($aInput['qType'] == 0 && count($aRight) > 1)
			{
				$aMsg[] = __('択一式では、正解を一つだけ選択してください。');
			}
		}
		else
		{
			$aRightText = array(1=>'',2=>'',3=>'',4=>'',5=>'');
			$bRight = false;
			$iCnt = 1;
			for ($i = 1; $i <= 5; $i++)
			{
				$sRight = $aInput['qRightText'.$i];
				$sRight = ClFunc_Common::convertKana(preg_replace(CL_WHITE_TRIM_PTN, '$1', $sRight),'aqpu');
				if ($sRight != '')
				{
					$aRightText[$iCnt] = $sRight;
					$iCnt++;
					$bRight = true;
				}
			}
			if (!$bRight)
			{
				$aMsg[] = __('正解文字列を一つ以上記入してください。');
			}
		}

		if (!is_null($aMsg))
		{
			$this->template->content = View::forge('t/'.$this->baseName.'/querylist');
			$this->template->content->set('aDrill',$this->aDrill);
			$this->template->content->set('aQuery',$aQuery);
			$this->template->content->set('aQQ',$aQQ);
			$this->template->content->set('aInput',$aInput);
			$this->template->content->set('aChoice',$aChoice);
			$this->template->content->set('aRight',$aRight);
			$this->template->content->set('aRightText',$aRightText);
			$this->template->content->set('aImg',$aImg);
			$this->template->content->set('aMsg',$aMsg);
			$this->template->content->set('iDqNO',null);
			$this->template->javascript = array('cl.t.'.$this->baseName.'.js');
			return $this->template;
		}

		$iDgNO = 0;
		if ($sGroup != '')
		{
			$result = Model_Drill::getDrillQueryGroup(array(array('dcID','=',$sID),array('dgName','=',$sGroup)));
			$row = $result->current();
			if (!empty($row))
			{
				$iDgNO = $row['dgNO'];
			}
			else
			{
				$iDgNO = Model_Drill::insertDrillQueryGroup(array('dcID'=>$sID,'dgName'=>$sGroup));
			}
		}


		if (!is_null($aQQ))
		{
			$aUpdate = array(
				'dgNO' => $iDgNO,
				'dqText' => $aInput['qText'],
				'dqImage' => $aInput['dqImage'],
				'dqStyle' => $aInput['qType'],
				'dqChoiceNum' => ($aInput['qType'] == 2)? 0:count($aChoice),
				'dqExplain' => $aInput['qExplain'],
				'dqExplainImage' => $aInput['dqExplainImage'],
				'dqDate' => date('YmdHis'),
			);
			for ($i = 1; $i <= 50; $i++)
			{
				if (isset($aChoice[$i]))
				{
					$aUpdate['dqChoice'.$i] = $aChoice[$i];
					if (isset($aImg[$i]))
					{
						$aUpdate['dqChoiceImg'.$i] = $aImg[$i];
					}
					else
					{
						$aUpdate['dqChoiceImg'.$i] = '';
					}
				}
				else
				{
					$aUpdate['dqChoice'.$i] = '';
					$aUpdate['dqChoiceImg'.$i] = '';
				}
			}
			if ($aInput['qType'] != 2)
			{
				$aKeys = array_keys($aRight);
				$aUpdate['dqRight1'] = implode('|',$aKeys);
			}
			else
			{
				foreach ($aRightText as $i => $sRight)
				{
					$aUpdate['dqRight'.$i] = $sRight;
				}
			}
			$aWhere = array(
				array('dcID','=',$aQQ['dcID']),
				array('dbNO','=',$aQQ['dbNO']),
				array('dqNO','=',$aQQ['dqNO']),
			);

			try
			{
				$result = Model_Drill::updateDrillQuery($aUpdate,$aWhere);
				$result = Model_Drill::setDrillQueryGroupQNum($sID);
				$sSavePath = CL_UPPATH.DS.$sID.DS.$iDbNO.DS.$aQQ['dqNO'];
			}
			catch (Exception $e)
			{
				\Clfunc_Common::LogOut($e,__CLASS__);
				Session::set('SES_T_ERROR_MSG',$e->getMessage());
				Response::redirect($this->eRedirect);
			}
			$sSesM = __('問題:noを更新しました。',array('no'=>$aQQ['dqSort']));
			$iDqNO = $aQQ['dqNO'];
		}
		else
		{
			$aInsert = array(
				'dcID' => $this->aDrill['dcID'],
				'dbNO' => $this->aDrill['dbNO'],
				'dgNO' => $iDgNO,
				'dqSort' => $aInput['qSort'],
				'dqText' => $aInput['qText'],
				'dqImage' => $aInput['dqImage'],
				'dqStyle' => $aInput['qType'],
				'dqChoiceNum' => ($aInput['qType'] == 2)? 0:count($aChoice),
				'dqExplain' => $aInput['qExplain'],
				'dqExplainImage' => $aInput['dqExplainImage'],
				'dqDate' => date('YmdHis'),
			);
			if ($aInput['qType'] != 2)
			{
				foreach ($aChoice as $i => $sChoice)
				{
					$aInsert['dqChoice'.$i] = $sChoice;
					if (isset($aImg[$i]))
					{
						$aInsert['dqChoiceImg'.$i] = $aImg[$i];
					}
				}
				$aKeys = array_keys($aRight);
				$aInsert['dqRight1'] = implode('|',$aKeys);
			}
			else
			{
				foreach ($aRightText as $i => $sRight)
				{
					$aInsert['dqRight'.$i] = $sRight;
				}
			}

			try
			{
				$result = Model_Drill::insertDrillQuery($aInsert);
				$sSavePath = CL_UPPATH.DS.$sID.DS.$iDbNO.DS.$result;
			}
			catch (Exception $e)
			{
				\Clfunc_Common::LogOut($e,__CLASS__);
				Session::set('SES_T_ERROR_MSG',$e->getMessage());
				Response::redirect($this->eRedirect);
			}
			$sSesM = __('問題:noを追加しました。',array('no'=>$aInsert['dqSort']));
			$iDqNO = $result;
		}

		if (file_exists($sSavePath))
		{
			system('rm -rf '.$sSavePath);
		}
		if ($aInput['dqImage'])
		{
			ClFunc_Common::chkDir($sSavePath,true);
			File::rename($sTempPath.DS.$aInput['dqImage'],$sSavePath.DS.$aInput['dqImage']);
			File::rename($sTempPath.DS.CL_Q_SMALL_PREFIX.$aInput['dqImage'],$sSavePath.DS.CL_Q_SMALL_PREFIX.$aInput['dqImage']);
		}
		if ($aInput['dqExplainImage'])
		{
			ClFunc_Common::chkDir($sSavePath,true);
			File::rename($sTempPath.DS.$aInput['dqExplainImage'],$sSavePath.DS.$aInput['dqExplainImage']);
			File::rename($sTempPath.DS.CL_Q_SMALL_PREFIX.$aInput['dqExplainImage'],$sSavePath.DS.CL_Q_SMALL_PREFIX.$aInput['dqExplainImage']);
		}
		if ($aInput['qType'] != 2)
		{
			if (!is_null($aImg))
			{
				ClFunc_Common::chkDir($sSavePath,true);
				foreach ($aImg as $v)
				{
					File::rename($sTempPath.DS.$v,$sSavePath.DS.$v);
					File::rename($sTempPath.DS.CL_Q_SMALL_PREFIX.$v,$sSavePath.DS.CL_Q_SMALL_PREFIX.$v);
				}
			}
		}
		if (file_exists($sTempPath))
		{
			system('rm -rf '.$sTempPath);
		}

		Session::set('SES_T_NOTICE_MSG',$sSesM);
		Response::redirect(DS.$this->bn.'/querylist/'.$sID.DS.$iDbNO);
	}

	public function action_querydelete($sID = null,$iDbNO = null,$iDqNO = null)
	{
		$sImgPath = CL_UPPATH.DS.$sID.DS.$iDbNO.DS.$iDqNO;
		$aQuery = null;
		$aChk = self::DrillCategoryChecker($sID);
		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}
		$aChk = self::DrillChecker($sID,$iDbNO);
		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}
		$aChk = self::QueryChecker($sID,$iDbNO,$iDqNO);
		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}

		try
		{
			$result = Model_Drill::deleteDrillQuery($this->aQuery);
			if (file_exists($sImgPath))
			{
				system('rm -rf '.$sImgPath);
			}

			if ($this->aDrill['dbQueryNum'] <= $this->aDrill['dbPublicNum'])
			{
				$result = Model_Drill::updateDrill(array('dbPublic'=>0),array(array('dcID','=',$sID),array('dbNO','=',$iDbNO)));
			}
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}
		Session::set('SES_T_NOTICE_MSG',__('問題:noを削除しました。',array('no'=>$aQuery['dqSort'])));
		Response::redirect(DS.$this->bn.'/querylist/'.$sID.DS.$iDbNO);
	}

	public function action_csv($sID = null, $iDbNO = null)
	{
		$this->template->content = View::forge('t/'.$this->baseName.'/csv');

		if (!$this->aClass['ctStatus'])
		{
			Session::set('SES_T_ERROR_MSG',__('終了した講義にはドリル問題を追加することはできません。'));
			Response::redirect('/t/drill');
		}

		$aChk = self::DrillCategoryChecker($sID);
		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}
		$aChk = self::DrillChecker($sID,$iDbNO);
		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}
		$this->template->content->set('aDrill',$this->aDrill);

		# タイトル
		$sTitle = $this->aDrill['dbTitle'].'｜'.__('CSVから問題追加');
		$this->template->set_global('pagetitle',$sTitle);

		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/'.$this->baseName, 'name'=>__('ドリルカテゴリ一覧'));
		$this->aBread[] = array('link'=>'/'.$this->baseName.'/list/'.$sID,'name'=>$this->aDCategory['dcName']);
		$this->aBread[] = array('link'=>'/'.$this->baseName.'/querylist/'.$sID.DS.$iDbNO,'name'=>$this->aDrill['dbTitle']);
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		if (!Input::post(null,false))
		{
			$data = array('error'=>null);
			$this->template->content->set($data);
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
			$st_csv = Upload::get_errors('tt_csv');
			if ($st_csv['errors'])
			{
				switch ($st_csv['errors'][0]['error'])
				{
					case Upload::UPLOAD_ERR_INI_SIZE:
					case Upload::UPLOAD_ERR_FORM_SIZE:
					case Upload::UPLOAD_ERR_MAX_SIZE:
						$aMsg['tt_csv'] = __('登録できるファイルのサイズは:sizeMBまでです。',array('size'=>CL_FILESIZE));
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
			$this->template->content->set($data);
			return $this->template;
		}

		$oFile = Upload::get_files('tt_csv');
		$aCSV = ClFunc_Common::getCSV($oFile['file']);
		if (!count($aCSV))
		{
			$data['error'] = array('tt_csv'=>__('登録するデータがCSVから取得できませんでした。'));
			$this->template->content->set($data);
			return $this->template;
		}

		$aMsg = null;
		$iQN = 0;
		$iCN = 0;
		$aQuery = null;
		$aGroup = null;
		if (!empty($aCSV))
		{
			foreach ($aCSV as $iKey => $aS)
			{
				switch ($aS[0])
				{
					case '回答形式':
					case __('回答形式'):
						if ($iQN > 0)
						{
							$aQuery[$iQN]['dqChoiceNum'] = $iCN;
							if (($aQuery[$iQN]['dqStyle'] === 1 || $aQuery[$iQN]['dqStyle'] === 0) && $iCN < 2)
							{
								$aMsg[] = __(':no問目の選択肢を二つ以上指定してください。',array('no'=>$iQN));
							}
							if ($iRN == 0)
							{
								$aMsg[] = __(':no問目の正解を指定してください。',array('no'=>$iQN));
							}
							if ($aQuery[$iQN]['dqStyle'] === 0 && $aQuery[$iQN]['dqRight1'] > $iCN)
							{
								$aMsg[] = __(':no問目の正解で指定した番号が選択肢数を越えています。',array('no'=>$iQN));
							}
							if ($aQuery[$iQN]['dqStyle'] === 1)
							{
								if (is_array($aRights))
								{
									foreach ($aRights as $iR)
									{
										if ($iR > $iCN)
										{
											$aMsg[] = __(':no問目の正解で指定した番号が選択肢数を越えています。',array('no'=>$iQN));
											break;
										}
									}
								}
							}
						}
						$iQN++;
						$iCN = 0;
						$iRN = 0;
						$aQuery[$iQN]['dqStyle'] = null;

						switch($aS[1])
						{
							case 'radio':
								$aQuery[$iQN]['dqStyle'] = 0;
							break;
							case 'select':
								$aQuery[$iQN]['dqStyle'] = 1;
							break;
							case 'text':
								$aQuery[$iQN]['dqStyle'] = 2;
							break;
							default:
								$aMsg[] = __(':no問目の形式が正しく指定されていません。',array('no'=>$iQN));
								continue;
							break;
						}
					break;
					case '問題グループ':
					case __('問題グループ'):
						$aGroup[$iQN] = preg_replace(CL_WHITE_TRIM_PTN, '$1', strip_tags($aS[1]));
					break;
					case '問題文':
					case '設問文':
					case __('問題文'):
					case __('設問文'):
						if ($aS[1] == '')
						{
							$aMsg[] = __(':no問目の問題文が指定されていません。',array('no'=>$iQN));
							continue;
						}
						else
						{
							$aQuery[$iQN]['dqText'] = strip_tags($aS[1]);
						}
					break;
					default:
						if (preg_match('/^(正解|'.__('正解').')\d/', $aS[0]))
						{
							if ($aQuery[$iQN]['dqStyle'] == 2)
							{
								$sRight = str_replace(array("\r\n","\n","\r"), '', strip_tags($aS[1]));
								if ($sRight != '')
								{
									$iRN++;
									if ($iRN <= 5)
									{
										$sRight = \ClFunc_Common::convertKana(preg_replace(CL_WHITE_TRIM_PTN, '$1', $sRight),'aqpu');
										$aQuery[$iQN]['dqRight'.$iRN] = $sRight;
									}
									else
									{
										$iRN = 5;
									}
								}
							}
							else
							{
								if ($aS[0] != '正解1' && $aS[0] != __('正解').'1')
								{
									break;
								}
								if ($aQuery[$iQN]['dqStyle'] == 0)
								{
									if (is_numeric($aS[1]) && $aS[1] >= 1 && $aS[1] <= 50)
									{
										$iRN = 1;
										$aQuery[$iQN]['dqRight1'] = (int)$aS[1];
									}
									else
									{
										$aMsg[] = __(':no問目の正解1が無効な値です。',array('no'=>$iQN)).'['.$aS[1].']';
									}
								}
								else
								{
									$bMiss = false;
									$aRights = explode("|",$aS[1]);
									if (is_array($aRights))
									{
										foreach ($aRights as $iR)
										{
											if (!is_numeric($iR) || $iR < 1 || $iR > 50)
											{
												$bMiss = true;
											}
										}
									}
									else
									{
										$bMiss = true;
									}
									if ($bMiss)
									{
										$aMsg[] = __(':no問目の正解1が無効な値です。',array('no'=>$iQN)).__('"1|2|3"の形式で指定してください。').print_r($aRights,true);
									}
									else
									{
										$iRN = 1;
										$aQuery[$iQN]['dqRight1'] = $aS[1];
									}
								}
							}
							break;
						}
						if (preg_match('/^(選択肢|'.__('選択肢').')\d{1,2}/', $aS[0]))
						{
							if ($aQuery[$iQN]['dqStyle'] == 2)
							{
								break;
							}
							if ($aS[1] == '')
							{
								break;
							}
							$iCN++;
							if ($iCN <= 50)
							{
								$aQuery[$iQN]['dqChoice'.$iCN] = strip_tags($aS[1]);
							}
							else
							{
								$iCN = 50;
							}
						}
						break;
				}
			}
			if ($iQN > 0)
			{
				$aQuery[$iQN]['dqChoiceNum'] = $iCN;
				if (($aQuery[$iQN]['dqStyle'] === 1 || $aQuery[$iQN]['dqStyle'] === 0) && $iCN < 2)
				{
					$aMsg[] = __(':no問目の選択肢を二つ以上指定してください。',array('no'=>$iQN));
				}
				if ($iRN == 0)
				{
					$aMsg[] = __(':no問目の正解を指定してください。',array('no'=>$iQN));
				}
				if ($aQuery[$iQN]['dqStyle'] === 0 && $aQuery[$iQN]['dqRight1'] > $iCN)
				{
					$aMsg[] = __(':no問目の正解で指定した番号が選択肢数を越えています。',array('no'=>$iQN));
				}
				if ($aQuery[$iQN]['dqStyle'] === 1)
				{
					if (is_array($aRights))
					{
						foreach ($aRights as $iR)
						{
							if ($iR > $iCN)
							{
								$aMsg[] = __(':no問目の正解で指定した番号が選択肢数を越えています。',array('no'=>$iQN));
								break;
							}
						}
					}
				}
			}
			else
			{
				$aMsg[] = __('問題がありません。');
			}
		}
		else
		{
			$data['error'] = array('tt_csv'=>__('登録するデータがありません。'));
			$this->template->content->set($data);
			return $this->template;
		}
		if (!is_null($aMsg))
		{
			$data['error'] = array('valid'=>$aMsg);
			$this->template->content->set($data);
			return $this->template;
		}

		foreach ($aQuery as $iQN => $aQ)
		{
			$iDgNO = 0;
			if (isset($aGroup[$iQN]) && $aGroup[$iQN] != '')
			{
				$result = Model_Drill::getDrillQueryGroup(array(array('dcID','=',$sID),array('dgName','=',$aGroup[$iQN])));
				$row = $result->current();
				if (!empty($row))
				{
					$iDgNO = $row['dgNO'];
				}
				else
				{
					$iDgNO = Model_Drill::insertDrillQueryGroup(array('dcID'=>$sID,'dgName'=>$aGroup[$iQN]));
				}
			}
			$aQuery[$iQN]['dgNO'] = $iDgNO;
		}

		try
		{
			$result = Model_Drill::insertDrillQueryFromCSV($this->aDrill,$aQuery);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}


		Session::set('SES_T_NOTICE_MSG',__('CSVからドリル問題の追加が完了しました。'));
		Response::redirect('t/'.$this->baseName.'/querylist/'.$sID.DS.$iDbNO);
	}

	public function action_preview($sID = null, $iDbNO = null)
	{
		$iDqNO = (int)Input::get('qn');
		$iQqNO = (int)Input::get('qq') - 1;

		$aChk = self::DrillCategoryChecker($sID);
		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}
		$aChk = self::DrillChecker($sID,$iDbNO);
		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}
		if ($iDqNO != 0)
		{
			if (Session::get('DPRV_'.$sID.'_'.$iDbNO,false)) {
				Session::delete('DPRV_'.$sID.'_'.$iDbNO);
			}
			$aChk = self::QueryChecker($sID,$iDbNO,$iDqNO);
			if (is_array($aChk))
			{
				Session::set('SES_T_ERROR_MSG',$aChk['msg']);
				Response::redirect($aChk['url']);
			}
		}
		else
		{
			if ($iQqNO == -1) {
				if (Session::get('DPRV_'.$sID.'_'.$iDbNO,false)) {
					Session::delete('DPRV_'.$sID.'_'.$iDbNO);
				}
				if ($this->aDrill['dbRand'])
				{
					# 設問番号をランダム生成
					$aAll = null;
					for ($i = 1; $i <= $this->aDrill["dbQueryNum"]; $i++) {
						$aAll[$i] = $i;
					}
					if (is_null($aAll))
					{
						Session::set('SES_T_ERROR_MSG',__('問題がないため、プレビューできません。先に問題を作成してください。'));
						Response::redirect(DS.$this->bn.'/list/'.$sID);
					}
					$iP = (count($aAll) >= $this->aDrill["dbPublicNum"])? $this->aDrill["dbPublicNum"]:count($aAll);
					$aRand = array_rand($aAll, $iP);
					if (!is_array($aRand)) {
						$aRand = array($aRand);
					}
					shuffle($aRand);
				}
				else
				{
					for ($i = 1; $i <= $this->aDrill["dbPublicNum"]; $i++) {
						$aRand[] = $i;
					}
				}
				# セッションに格納
				Session::set('DPRV_'.$sID.'_'.$iDbNO, $aRand);
				# 問題へジャンプ
				Response::redirect(DS.$this->bn.'/preview/'.$sID.DS.$iDbNO.DS.'?qq=1');
			}
			else
			{
				if (!Session::get('DPRV_'.$sID.'_'.$iDbNO,false)) {
					# 問題列作り直し
					Response::redirect(DS.$this->bn.'/preview/'.$sID.DS.$iDbNO);
				}
				$aQPub = Session::get('DPRV_'.$sID.'_'.$iDbNO);
				$iDqNO = $aQPub[$iQqNO];

				$aChk = self::QueryChecker($sID,$iDbNO,$iDqNO);
				if (is_array($aChk))
				{
					Session::set('SES_T_ERROR_MSG',$aChk['msg']);
					Response::redirect($aChk['url']);
				}
			}
		}

		# タイトル
		$sTitle = $this->aDrill['dbTitle'].'｜'.__('ドリルプレビュー');
		$this->template->set_global('pagetitle',$sTitle);
		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/'.$this->baseName, 'name'=>__('ドリルカテゴリ一覧'));
		$this->aBread[] = array('link'=>'/'.$this->baseName.'/list/'.$sID,'name'=>$this->aDCategory['dcName']);
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		$this->template->content = View::forge('t/'.$this->baseName.'/preview');
		$this->template->content->set('aDrill',$this->aDrill);
		$this->template->content->set('aQuery',$this->aQuery);
		$this->template->content->set('iQqNO',$iQqNO);
		$this->template->javascript = array('cl.s.'.$this->baseName.'.js','cl.t.'.$this->baseName.'.js');
		return $this->template;
	}


	public function action_put($sID = null, $iDbNO = null)
	{
		$aChk = self::DrillCategoryChecker($sID);
		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}
		$aChk = self::DrillChecker($sID,$iDbNO);
		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}

		$aStudent = null;
		$result = Model_Student::getStudentFromClass($this->aClass['ctID'],null,null,array('st.stNO'=>'asc'));
		if (count($result))
		{
			$aRes = $result->as_array('stID');
			foreach ($aRes as $sStID => $aS)
			{
				$aStudent[$sStID]['stu'] = $aS;
				$aStudent[$sStID]['num'] = 0;
				$aStudent[$sStID]['sum'] = 0;
			}
		}
		$result = Model_Drill::getDrillPut(array(array('dcID','=',$sID),array('dbNO','=',$iDbNO)),null,array('stID'=>'asc','dpDate'=>'desc'));
		if (count($result))
		{
			foreach ($result as $aP)
			{
				$sStID = $aP['stID'];
				if (isset($aStudent[$sStID]))
				{
					$aStudent[$sStID]['num'] += 1;
					$aStudent[$sStID]['sum'] += $aP['dpAvg'];
					$aStudent[$sStID]['put'][] = array('dpDate'=>$aP['dpDate'], 'dpAvg'=>$aP['dpAvg']);
				}
			}
		}

		# タイトル
		$sTitle = $this->aDrill['dbTitle'];
		$sTitle .= '｜'.__('実施状況');
		$this->template->set_global('pagetitle',$sTitle);

		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/'.$this->baseName, 'name'=>__('ドリルカテゴリ一覧'));
		$this->aBread[] = array('link'=>'/'.$this->baseName.'/list/'.$sID,'name'=>$this->aDCategory['dcName']);
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		$this->template->content = View::forge('t/'.$this->baseName.'/put');
		$this->template->content->set('aDrill',$this->aDrill);
		$this->template->content->set('aStudent',$aStudent);
		$this->template->javascript = array('cl.t.'.$this->baseName.'.js');
		return $this->template;
	}

	public function action_queryanalysis($sID = null, $iDbNO = null)
	{
		$aChk = self::DrillCategoryChecker($sID);
		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}
		$aChk = self::DrillChecker($sID,$iDbNO);
		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}
		$aChk = self::DrillQueryGroup($sID);
		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}
		$this->template->set_global('aDQGroup',$this->aDQGroup);

		$aQuery = null;
		$result = Model_Drill::getDrillQuery(array(array('dcID','=',$sID),array('dbNO','=',$iDbNO)),null,array('dqSort'=>'asc'));
		if (!count($result))
		{
			Session::set('SES_T_ERROR_MSG',__('指定のドリルには問題がありません。'));
			Response::redirect(DS.$this->bn.DS.$sID);
		}
		foreach ($result as $aR)
		{
			$aQuery['dq'.$aR['dqNO']] = $aR;
		}

		try
		{
			$result = Model_Drill::setDrillQueryAnalysis($this->aDrill);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		$aBent = null;
		$result = Model_Drill::getDrillQueryAnalysis(array(array('dcID','=',$sID),array('dbNO','=',$iDbNO)));
		if (count($result))
		{
			foreach ($result as $aR)
			{
				$aBent['dq'.$aR['dqNO']] = $aR;
			}
		}

		# タイトル
		$sTitle = $this->aDrill['dbTitle'];
		$sTitle .= '｜'.__('問題別正答率');
		$this->template->set_global('pagetitle',$sTitle);

		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/'.$this->baseName, 'name'=>__('ドリルカテゴリ一覧'));
		$this->aBread[] = array('link'=>'/'.$this->baseName.'/list/'.$sID,'name'=>$this->aDCategory['dcName']);
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		$this->template->content = View::forge('t/'.$this->baseName.'/queryanalysis');
		$this->template->content->set('aDrill',$this->aDrill);
		$this->template->content->set('aQuery',$aQuery);
		$this->template->content->set('aBent',$aBent);
		$this->template->javascript = array('cl.t.'.$this->baseName.'.js');
		return $this->template;
	}

	public function action_analysis($sID = null)
	{
		$aChk = self::DrillCategoryChecker($sID);
		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}
		$aChk = self::DrillQueryGroup($sID);
		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}
		$this->template->set_global('aDCategory',$this->aDCategory);
		$this->template->set_global('aDQGroup',$this->aDQGroup);

		# タイトル
		$sTitle = $this->aDCategory['dcName'].' '.__('問題分析');
		if ($this->aDCategory['dcAnalysisDate'] != CL_DATETIME_DEFAULT)
		{
			$sTitle .= '｜'.date('Y/m/d H:i',strtotime($this->aDCategory['dcAnalysisDate']));
		}
		else
		{
			$sTitle .= '｜'.__('集計未実施');
		}
		$this->template->set_global('pagetitle',$sTitle);

		# カスタムボタン
		$aCustomBtn = null;
		switch ($this->aDCategory['dcAnalysisProgress'])
		{
			case 1:
				$aCustomBtn = array(
					array(
						'url' => '#',
						'name' => __('集計実行中…'),
						'show' => 0,
						'icon' => 'fa-spinner fa-spin',
						'option' => array(
							'id' => 'drill-analysis-btn',
							'obj' => $this->aDCategory['dcID'],
							'disabled' => 'disabled',
						),
					),
				);
			break;
			case 2:
			case 0:
			default:
				$aCustomBtn = array(
					array(
						'url' => DS.$this->bn.'/aggregation/'.$sID,
						'name' => __('集計実行'),
						'show' => 0,
						'option' => array(
							'id' => 'drill-analysis-btn',
							'obj' => $this->aDCategory['dcID'],
						),
					),
				);
			break;
		}
		$this->template->set_global('aCustomBtn',$aCustomBtn);

		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/'.$this->baseName, 'name'=>__('ドリルカテゴリ一覧'));
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		$this->template->content = View::forge('t/'.$this->baseName.'/analysis');
		$this->template->javascript = array('cl.t.'.$this->baseName.'.js');
		return $this->template;
	}

	public function action_aggregation($sID = null)
	{
		$aChk = self::DrillCategoryChecker($sID);
		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}
		$aChk = self::DrillQueryGroup($sID);
		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}

		$sBack = DS.$this->bn.'/analysis/'.$sID;

		$result = Model_Drill::getDrillQuery(array(array('dcID','=',$sID)));
		if (!count($result))
		{
			Session::set('SES_T_ERROR_MSG',__('問題がないため、集計できません。先に問題を作成してください。'));
			Response::redirect($sBack);
		}
		$result = Model_Drill::getDrillAns(array(array('dcID','=',$sID)));
		if (!count($result))
		{
			Session::set('SES_T_ERROR_MSG',__('回答がないため、集計できません。'));
			Response::redirect($sBack);
		}

		shell_exec('/usr/bin/php '.CL_OILPATH.' r execdrillaggregation '.$sID.' '.$this->aTeacher['ttID'].' > /dev/null 2>&1 &');
		Session::set('SES_T_NOTICE_MSG',__('集計を開始しました。\n集計には時間がかかる場合があります。\n集計が完了すると、画面が再読み込みされます。'));

		Response::redirect($sBack);
	}

	public function action_tqselect($sID = null, $iNO = null)
	{
		$sBack = DS.$this->bn.DS.'list'.DS.$sID;

		$aQuery = null;

		$aChk = self::DrillCategoryChecker($sID);
		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}
		$aChk = self::DrillChecker($sID,$iNO);
		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}
		$result = Model_Drill::getDrillQuery(array(array('dcID','=',$sID),array('dbNO','=',$iNO)),null,array('dqSort'=>'asc'));
		if (count($result))
		{
			$aQuery = $result->as_array('dqSort');
		}
		else
		{
			Session::set('SES_T_ERROR_MSG',__('指定のドリルには問題がありません。'));
			Response::redirect($sBack);
		}

		if ($aInput = Input::post(null,false))
		{
			if (!isset($aInput['QueryChk']))
			{
				Session::set('SES_T_ERROR_MSG',__('問題が選択されていません。'));
				Response::redirect(DS.$this->bn.DS.'tqselect'.DS.$sID.DS.$iNO);
			}

			Session::set('SES_T_DRILL2TEST_'.$sID.'_'.$iNO, serialize($aInput['QueryChk']));

			Response::redirect(DS.$this->bn.DS.'tbselect'.DS.$sID.DS.$iNO);
		}

		$aInput['QueryChk'] = array();
		if ($sSel = Session::get('SES_T_DRILL2TEST_'.$sID.'_'.$iNO,false))
		{
			$aInput['QueryChk'] = unserialize($sSel);
		}

		# タイトル
		$sTitle = __('問題を小テストにコピー').'｜'.$this->aDrill['dbTitle'];
		$this->template->set_global('pagetitle',$sTitle);

		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/'.$this->baseName, 'name'=>__('ドリルカテゴリ一覧'));
		$this->aBread[] = array('link'=>'/'.$this->baseName.'/list/'.$sID,'name'=>$this->aDCategory['dcName']);
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		$this->template->content = View::forge($this->bn.'/tqselect');
		$this->template->content->set('aDrill',$this->aDrill);
		$this->template->content->set('aQuery',$aQuery);
		$this->template->content->set('aQChk',$aInput['QueryChk']);
		$this->template->javascript = array('cl.t.drill.js');
		return $this->template;
	}

	public function action_tbselect($sID = null, $iNO = null)
	{
		$sTempPath = CL_UPPATH.DS.'temp'.DS.'test'.DS.$sID;
		$sBack = DS.$this->bn.DS.'list'.DS.$sID;

		$aTests = null;
		$aTest = null;
		$aQuery = null;
		$aRQuery = null;
		$aInput = $this->aTestBase;
		$aInput['t_select'] = null;

		$aChk = self::DrillCategoryChecker($sID);
		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}
		$aChk = self::DrillChecker($sID,$iNO);
		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}
		$result = Model_Drill::getDrillQuery(array(array('dcID','=',$sID),array('dbNO','=',$iNO)),null,array('dqSort'=>'asc'));
		if (count($result))
		{
			$aQuery = $result->as_array('dqSort');
			$aRQuery = $result->as_array('dqNO');
		}
		else
		{
			Session::set('SES_T_ERROR_MSG',__('指定のドリルには問題がありません。'));
			Response::redirect($sBack);
		}

		if (!$sSel = Session::get('SES_T_DRILL2TEST_'.$sID.'_'.$iNO,false))
		{
			Session::set('SES_T_ERROR_MSG',__('問題が選択されていません。'));
			Response::redirect(DS.$this->bn.DS.'tqselect'.DS.$sID.DS.$iNO);
		}

		$result = Model_Test::getTestBaseFromClass($this->aClass['ctID'],array(array('tb.tbPublic','=',0)),null,array('tb.tbSort'=>'desc'));
		if (count($result))
		{
			foreach ($result as $r)
			{
				if ($r['tpNum'] == 0)
				{
					$aTests[] = $r;
				}
			}
		}

		# タイトル
		$sTitle = __('問題を小テストにコピー').'｜'.$this->aDrill['dbTitle'];
		$this->template->set_global('pagetitle',$sTitle);

		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/'.$this->baseName, 'name'=>__('ドリルカテゴリ一覧'));
		$this->aBread[] = array('link'=>'/'.$this->baseName.'/list/'.$sID,'name'=>$this->aDCategory['dcName']);
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		$aSel = unserialize($sSel);
		$iTotal = count($aSel) * 10;

		if (!Input::post(null,false))
		{
			$this->template->content = View::forge($this->bn.'/tbselect');
			$this->template->content->set('aDrill',$this->aDrill);
			$this->template->content->set('aQuery',$aQuery);
			$this->template->content->set('aTests',$aTests);
			$this->template->content->set('aInput',$aInput);
			$this->template->content->set('aSel',$aSel);
			$this->template->content->set('iTotal',$iTotal);
			$this->template->javascript = array('jquery.timepicker.js','cl.t.drill.js');
			return $this->template;
		}

		$iTotal = 0;
		$aInput = Input::post(null,false);

		if (isset($aInput['back']))
		{
			Response::redirect(DS.$this->bn.DS.'tqselect'.DS.$sID.DS.$iNO);
		}

		if ($aInput['t_select'] == '0')
		{
			Session::set('SES_T_ERROR_MSG',__('小テストを選択してください。'));
			Response::redirect(DS.$this->bn.DS.'tbselect'.DS.$sID.DS.$iNO);
		}

		$aTScore = null;

		if ($aInput['t_select'] == 'new')
		{
			$val = Validation::forge();
			$val->add_callable('Helper_CustomValidation');
			$val->add('t_name', __('小テストタイトル'))
				->add_rule('required')
				->add_rule('max_length',CL_TITLE_LENGTH);
			$val->add('t_qualify_score',__('合格点数'))
				->add_rule('required')
				->add_rule('valid_string', array('numeric','utf8'))
				->add_rule('numeric_min',0)
				->add_rule('numeric_max',99999);
			$val->add('t_limit_time',__('制限時間'))
				->add_rule('required')
				->add_rule('valid_string', array('numeric','utf8'))
				->add_rule('numeric_min',0)
				->add_rule('numeric_max',999);

			if ($aInput['t_auto_public'])
			{
				$val->add_field('t_auto_s_time', __('開始日時'), 'required|time')
					->add_rule('min_time',ClFunc_Tz::tz('Y/m/d H:i',$this->tz),$aInput['t_auto_s_date']);
				$val->add_field('t_auto_e_time', __('終了日時'), 'required|time')
					->add_rule('min_time',$aInput['t_auto_s_date'].' '.$aInput['t_auto_s_time'],$aInput['t_auto_e_date']);
			}
			if (!$val->run())
			{
				$aInput['error'] = $val->error();
			}

			Upload::process($this->config);

			# 添付処理
			$bImage = Upload::get_errors('bImage');
			if ($bImage)
			{
				if ($bImage['error'])
				{
					switch ($bImage['errors'][0]['error'])
					{
						case Upload::UPLOAD_ERR_INI_SIZE:
						case Upload::UPLOAD_ERR_FORM_SIZE:
						case Upload::UPLOAD_ERR_MAX_SIZE:
							$aInput['error']['tbImage'] = __(':nameに登録できるファイルのサイズは:sizeMBまでです。',array('name'=>__('解説'),'size'=>CL_IMGSIZE));
							break;
						case Upload::UPLOAD_ERR_EXTENSION:
						case Upload::UPLOAD_ERR_EXT_BLACKLISTED:
						case Upload::UPLOAD_ERR_EXT_NOT_WHITELISTED:
						case Upload::UPLOAD_ERR_TYPE_BLACKLISTED:
						case Upload::UPLOAD_ERR_TYPE_NOT_WHITELISTED:
						case Upload::UPLOAD_ERR_MIME_BLACKLISTED:
						case Upload::UPLOAD_ERR_MIME_NOT_WHITELISTED:
							$aInput['error']['tbImage'] = __(':nameに登録できるファイルは画像（JPG,JPEG）のみです。',array('name'=>__('解説')));
							break;
						case Upload::UPLOAD_ERR_NO_FILE:
							# ファイルを指定していない
							break;
						default:
							$aInput['error']['tbImage'] = __(':nameのファイルアップロードに失敗しました。',array('name'=>__('解説')));
							break;
					}
				}
			}
			$bImage = Upload::get_files('bImage');
			if ($bImage)
			{
				$aInput['tbImage'] = 'base-explain.'.$bImage['extension'];
				$sThumbImg = CL_Q_SMALL_PREFIX.$aInput['tbImage'];
				$sTempImg = 'base-explain_tmp.'.$bImage['extension'];

				ClFunc_Common::chkDir($sTempPath,true);
				File::rename($bImage['file'], $sTempPath.DS.$sTempImg);
				chmod($sTempPath.DS.$sTempImg,0666);
				ClFunc_Common::OrientationFixedImage($sTempPath.DS.$sTempImg);
				$image=Image::load($sTempPath.DS.$sTempImg);
				$image->config('quality',CL_Q_IMG_QUALITY)->resize(CL_Q_IMG_SIZE,(CL_Q_IMG_SIZE / 4 * 3),true)->save($sTempPath.DS.$aInput['tbImage']);
				$image->config('quality',CL_Q_SMALL_QUALITY)->resize(CL_Q_SMALL_SIZE,(CL_Q_SMALL_SIZE / 4 * 3),true)->save($sTempPath.DS.$sThumbImg);
				chmod($sTempPath.DS.$aInput['tbImage'],0666);
				chmod($sTempPath.DS.$sThumbImg,0666);
				File::delete($sTempPath.DS.$sTempImg);
			}

			foreach ($aSel as $iDqNO)
			{
				if (!$aInput['ts_'.$iDqNO])
				{
					$aInput['error']['scores'] = __('全ての問題に配点を指定してください。');
					continue;
				}
				$iTotal += $aInput['ts_'.$iDqNO];
				$aTScore[$iDqNO] = $aInput['ts_'.$iDqNO];
			}

			if (isset($aInput['error']))
			{
				$aInput = array_merge($this->aTestBase,$aInput);
				$this->template->content = View::forge($this->bn.'/tbselect');
				$this->template->content->set('aDrill',$this->aDrill);
				$this->template->content->set('aQuery',$aQuery);
				$this->template->content->set('aTests',$aTests);
				$this->template->content->set('aInput',$aInput);
				$this->template->content->set('aSel',$aSel);
				$this->template->content->set('iTotal',$iTotal);
				$this->template->javascript = array('jquery.timepicker.js','cl.t.drill.js');
				return $this->template;
			}

			// 登録データ生成
			$aInsert = array(
				'ctID'           => $this->aClass['ctID'],
				'tbQueryStyle'   => $aInput['t_select_style'],
				'tbNum'          => 0,
				'tbTitle'        => $aInput['t_name'],
				'tbDate'         => date('YmdHis'),
				'tbPublic'       => 0,
				'tbQualifyScore' => $aInput['t_qualify_score'],
				'tbLimitTime'    => $aInput['t_limit_time'],
				'tbExplain'      => $aInput['t_explain'],
				'tbExplainImage' => $aInput['tbImage'],
				'tbScorePublic'  => $aInput['t_score_public'],
				'tbQueryRand'    => $aInput['t_query_rand'],
			);
			if ($aInput['t_auto_public'])
			{
				$aInsert['tbAutoPublicDate'] = ClFunc_Tz::tz('Y-m-d H:i:s',null,$aInput['t_auto_s_date'].' '.$aInput['t_auto_s_time'].':00',$this->tz);
				$aInsert['tbAutoCloseDate'] = ClFunc_Tz::tz('Y-m-d H:i:s',null,$aInput['t_auto_e_date'].' '.$aInput['t_auto_e_time'].':00',$this->tz);
			}

			try
			{
				$sTbID = Model_Test::insertTest($aInsert);
				$sSavePath = CL_UPPATH.DS.$sTbID.DS.'base';
				$aTest = array('tbTitle'=>$aInput['t_name']);
				if ($aInput['tbImage'])
				{
					ClFunc_Common::chkDir($sSavePath,true);
					File::rename($sTempPath.DS.$aInput['tbImage'],$sSavePath.DS.$aInput['tbImage']);
					File::rename($sTempPath.DS.CL_Q_SMALL_PREFIX.$aInput['tbImage'],$sSavePath.DS.CL_Q_SMALL_PREFIX.$aInput['tbImage']);
				}
			}
			catch (Exception $e)
			{
				\Clfunc_Common::LogOut($e,__CLASS__);
				Session::set('SES_T_ERROR_MSG',$e->getMessage());
				Response::redirect($this->eRedirect);
			}
		}
		else
		{
			$sTbID = $aInput['t_select'];
			$result = Model_Test::getTestBaseFromClass($this->aClass['ctID'],array(array('tbID','=',$sTbID)));
			if (!count($result))
			{
				Session::set('SES_T_ERROR_MSG',__('指定された小テストが見つかりません。'));
				Response::redirect(DS.$this->bn.DS.'tbselect'.DS.$sID.DS.$iNO);
			}
			$aTest = $result->current();

			foreach ($aSel as $iDqNO)
			{
				if (!$aInput['ts_'.$iDqNO])
				{
					$aInput['error']['scores'] = __('全ての問題に配点を指定してください。');
					continue;
				}
				$iTotal += $aInput['ts_'.$iDqNO];
				$aTScore[$iDqNO] = $aInput['ts_'.$iDqNO];
			}

			if (isset($aInput['error']))
			{
				$aInput = array_merge($this->aTestBase,$aInput);
				$this->template->content = View::forge($this->bn.'/tbselect');
				$this->template->content->set('aDrill',$this->aDrill);
				$this->template->content->set('aQuery',$aQuery);
				$this->template->content->set('aTests',$aTests);
				$this->template->content->set('aInput',$aInput);
				$this->template->content->set('aSel',$aSel);
				$this->template->content->set('iTotal',$iTotal);
				$this->template->javascript = array('jquery.timepicker.js','cl.t.drill.js');
				return $this->template;
			}
		}

		$iSort = 1;
		$result = Model_Test::getTestQuery(array(array('tbID','=',$sTbID)),null,array('tqSort'=>'desc'));
		if (count($result))
		{
			$res = $result->as_array();
			$iSort = (int)$res[0]['tqSort'] + 1;
		}

		$aInsert = null;
		foreach ($aSel as $iDqNO)
		{
			$aQ = $aRQuery[$iDqNO];

			$aInsert[$iDqNO] = array(
				'tbID' => $sTbID,
				'tqSort' => $iSort,
				'tqScore' => $aTScore[$iDqNO],
				'tqText' => $aQ['dqText'],
				'tqImage' => $aQ['dqImage'],
				'tqStyle' => $aQ['dqStyle'],
				'tqChoiceNum' => (int)$aQ['dqChoiceNum'],
				'tqExplain' => $aQ['dqExplain'],
				'tqExplainImage' => $aQ['dqExplainImage'],
				'tqDate' => date('YmdHis'),
			);
			for ($i = 1; $i <= 5; $i++)
			{
				$aInsert[$iDqNO]['tqRight'.$i] = $aQ['dqRight'.$i];
			}
			for ($i = 1; $i <= 50; $i++)
			{
				$aInsert[$iDqNO]['tqChoice'.$i] = $aQ['dqChoice'.$i];
				$aInsert[$iDqNO]['tqChoiceImg'.$i] = $aQ['dqChoiceImg'.$i];
			}
			$iSort++;
		}

		try
		{
			$result = Model_Test::insertTestQueries($aInsert);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		foreach ($result as $iDqNO => $iTqNO)
		{
			$sSourcePath = CL_UPPATH.DS.$sID.DS.$iNO.DS.$iDqNO;
			$sSavePath = CL_UPPATH.DS.$sTbID.DS.$iTqNO;

			if (file_exists($sSourcePath))
			{
				ClFunc_Common::chkDir($sSavePath,true);
				system('cp -rfp '.$sSourcePath.'/* '.$sSavePath.'/');
			}
		}

		Session::delete('SES_T_DRILL2TEST_'.$sID.'_'.$iNO);
		Session::set('SES_T_NOTICE_MSG',__('ドリル「:drill」の問題を小テスト「:test」にコピーしました。（:num件）',array('test'=>$aTest['tbTitle'],'drill'=>$this->aDrill['dbTitle'],'num'=>count($aSel))));
		Response::redirect(DS.$this->bn);

	}


	private function DrillCategoryChecker($sDcID = null)
	{
		if (is_null($this->aClass))
		{
			return array('msg'=>__('講義情報が確認できませんでした。'),'url'=>'/t/index');
		}
		if (is_null($sDcID))
		{
			return array('msg'=>__('カテゴリ情報が送信されていません。'),'url'=>DS.$this->bn);
		}
		$result = Model_Drill::getDrillCategoryFromID($sDcID);
		if (!count($result))
		{
			return array('msg'=>__('指定されたカテゴリが見つかりません。').$sDcID,'url'=>DS.$this->bn);
		}
		$this->aDCategory = $result->current();

		return true;
	}

	private function DrillChecker($sDcID = null, $iDbNO = null)
	{
		if (is_null($this->aClass))
		{
			return array('msg'=>__('講義情報が確認できませんでした。'),'url'=>'/t/index');
		}
		if (is_null($sDcID) || is_null($iDbNO))
		{
			return array('msg'=>__('必要な情報が送信されていません。'),'url'=>DS.$this->bn);
		}
		$result = Model_Drill::getDrill(array(array('dcID','=',$sDcID),array('dbNO','=',$iDbNO)));
		if (!count($result))
		{
			return array('msg'=>__('指定されたドリルが見つかりません。'),'url'=>DS.$this->bn);
		}
		$this->aDrill = $result->current();

		return true;
	}

	private function QueryChecker($sDcID = null, $iDbNO = null, $iDqNO = null)
	{
		if (is_null($this->aClass))
		{
			return array('msg'=>__('講義情報が確認できませんでした。'),'url'=>'/t/index');
		}
		if (is_null($sDcID) || is_null($iDbNO) || is_null($iDqNO))
		{
			return array('msg'=>__('必要な情報が送信されていません。'),'url'=>DS.$this->bn);
		}
		$result = Model_Drill::getDrillQuery(array(array('dcID','=',$sDcID),array('dbNO','=',$iDbNO),array('dqNO','=',$iDqNO)));
		if (!count($result))
		{
			return array('msg'=>__('指定されたドリル問題が見つかりません。'),'url'=>DS.$this->bn);
		}
		$this->aQuery = $result->current();

		return true;
	}

	private function DrillQueryGroup($sDcID = null)
	{
		if (is_null($this->aClass))
		{
			return array('msg'=>__('講義情報が確認できませんでした。'),'url'=>'/t/index');
		}
		if (is_null($sDcID))
		{
			return array('msg'=>__('必要な情報が送信されていません。'),'url'=>DS.$this->bn);
		}
		$result = Model_Drill::getDrillQueryGroup(array(array('dcID','=',$sDcID)),null,array('dgSort'=>'asc'));
		if (!count($result))
		{
			return array('msg'=>__('指定された問題グループが見つかりません。'),'url'=>DS.$this->bn);
		}
		$this->aDQGroup = $result->as_array('dgNO');

		return true;
	}

}