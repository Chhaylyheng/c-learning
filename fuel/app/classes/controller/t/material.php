<?php
class Controller_T_Material extends Controller_T_Baseclass
{
	private $baseName = 'material';

	private $aMatCate = array(
		'mc_name'=>null,
		'mc_mail'=>0,
	);
	private $aMatBase = array(
		'm_title'=>null,
		'm_url'  =>null,
		'm_file' =>null,
		'fileinfo' =>array(
			'file'=>'',
			'name'=>'',
			'size'=>0,
			'isimg'=>false,
		),
		'm_text' =>null,
		'm_public' =>0,
		'clurl' => false,
	);

	private $aMCategory = null;
	private $aMaterial = null;
	private $aNums = null;

	public function before()
	{
		parent::before();
		\Clfunc_Common::ContractDetect($this->aTeacher, __CLASS__);
	}

	public function action_index()
	{
		$aMatCate = null;
		$result = Model_Material::getMaterialCategoryFromClass($this->aClass['ctID'],null,null,array('mcSort'=>'desc'));
		if (count($result))
		{
			$aMatCate = $result->as_array();
		}
		else
		{
			if ($this->aClass['ctStatus'] > 0)
			{
				Response::redirect('/t/'.$this->baseName.'/catecreate');
			}
		}

		# タイトル
		$sTitle = __('教材倉庫カテゴリ一覧');
		$this->template->set_global('pagetitle',$sTitle);

		# パンくずリスト生成
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		# カスタムメニュー
		$aCustomMenu = array(
			array(
				'url'  => '/t/'.$this->baseName.'/catecreate/',
				'name' => __('教材倉庫カテゴリの新規作成'),
				'show' => 1,
			),
		);

		$this->template->set_global('aCustomMenu',$aCustomMenu);

		$this->template->content = View::forge('t/'.$this->baseName.'/index');
		$this->template->content->set('aMatCate',$aMatCate);
		$this->template->javascript = array('cl.t.material.js');
		return $this->template;
	}


	public function action_catecreate()
	{
		$view = 't/'.$this->baseName.'/cateedit';
		# タイトル
		$sTitle = __('教材倉庫カテゴリの新規作成');
		$this->template->set_global('pagetitle',$sTitle);

		# パンくずリスト生成
		$this->aBread[] = array('link'=>DS.$this->baseName,'name'=>__('教材倉庫カテゴリ一覧'));
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		if (!Input::post(null,false))
		{
			$data = $this->aMatCate;
			$data['error'] = null;
			$this->template->content = View::forge($view,$data);
			$this->template->javascript = array('cl.t.material.js');
			return $this->template;
		}

		$aInput = Input::post();

		$val = Validation::forge();
		$val->add_callable('Helper_CustomValidation');
		$val->add_field('mc_name', __('カテゴリ名'), 'required|max_length['.CL_TITLE_LENGTH.']');
		if (!$val->run())
		{
			$data = $aInput;
			$data['error'] = $val->error();
			$this->template->content = View::forge($view,$data);
			$this->template->javascript = array('cl.t.material.js');
			return $this->template;
		}

		// 登録データ生成
		$aInsert = array(
			'ctID'   => $this->aClass['ctID'],
			'mcName' => $aInput['mc_name'],
			'mcMail' => $aInput['mc_mail'],
			'mcDate' => date('YmdHis'),
		);

		try
		{
			$result = Model_Material::insertMaterialCategory($aInsert);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		Session::set('SES_T_NOTICE_MSG',__('教材倉庫カテゴリを作成しました。'));
		Response::redirect('/t/'.$this->baseName);
	}

	public function action_cateedit($sID = null)
	{
		$view = 't/'.$this->baseName.'/cateedit';

		$aChk = self::MatCategoryChecker($sID);
		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}

		# タイトル
		$sTitle = __('教材倉庫カテゴリ情報の編集');
		$this->template->set_global('pagetitle',$sTitle);

		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/'.$this->baseName,'name'=>__('教材倉庫カテゴリ一覧'));
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		if (!Input::post(null,false))
		{
			$data = $this->aMatCate;
			$data['mc_name'] = $this->aMCategory['mcName'];
			$data['mc_mail'] = $this->aMCategory['mcMail'];
			$data['error'] = null;
			$this->template->content = View::forge($view,$data);
			$this->template->content->set('aMCategory',$this->aMCategory);
			$this->template->javascript = array('cl.t.material.js');
			return $this->template;
		}

		$aInput = Input::post();

		$val = Validation::forge();
		$val->add_callable('Helper_CustomValidation');
		$val->add_field('mc_name', __('カテゴリ名'), 'required|max_length['.CL_TITLE_LENGTH.']');

		if (!$val->run())
		{
			$data = $aInput;
			$data['error'] = $val->error();
			$this->template->content = View::forge($view,$data);
			$this->template->content->set('aMCategory',$this->aMCategory);
			$this->template->javascript = array('cl.t.material.js');
			return $this->template;
		}

		// 更新データ生成
		$aUpdate = array(
			'mcName' => $aInput['mc_name'],
			'mcMail' => $aInput['mc_mail'],
		);

		try
		{
			$result = Model_Material::updateMaterialCategory($aUpdate,array(array('mcID','=',$sID)));
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		Session::set('SES_T_NOTICE_MSG',__('教材倉庫カテゴリ情報を更新しました。'));
		Response::redirect('/t/'.$this->baseName);

	}


	public function action_catedelete($sID = null)
	{
		$aChk = self::MatCategoryChecker($sID);
		if (is_array($aChk))
		{
			Session::set('SES_S_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}
		$aMaterial = array();
		$result = \Model_Material::getMaterial(array(array('mt.mcID','=',$sID),array('mt.fID','!=','')));
		if (count($result))
		{
			$aMaterial = $result->as_array();
		}

		try
		{
			$result = Model_Material::deleteMaterialCategory($sID,$this->aMCategory,$aMaterial);
			foreach ($aMaterial as $aM)
			{
				\Clfunc_Aws::deleteFile($this->sAwsSavePath,$aM['fID'].'.'.$aM['fExt']);
			}
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		Session::set('SES_T_NOTICE_MSG',__('教材倉庫カテゴリを削除しました。'));
		Response::redirect('/t/'.$this->baseName);
	}


	public function action_list($sID = null)
	{
		Session::delete('SES_T_MATERIAL');

		$aChk = self::MatCategoryChecker($sID);
		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}

		$aMaterial = null;
		$result = Model_Material::getMaterial(array(array('mt.mcID','=',$sID)),null,array('mt.mSort'=>'desc'));
		if (count($result))
		{
			$aMaterial = $result->as_array();
		}
		if (!is_null($aMaterial))
		{
			foreach ($aMaterial as $iNO => $aM)
			{
				$urls = explode("\n", $aM['mURL']);
				if (is_array($urls))
				{
					$aMaterial[$iNO]['mURL'] = $urls;
					foreach ($urls as $i => $v)
					{
						$aMaterial[$iNO]['clurl'][$i] = \Clfunc_Common::ExtUrlDetect($v);
					}
				}
			}
		}

		# タイトル
		$sTitle = $this->aMCategory['mcName'];
		$this->template->set_global('pagetitle',$sTitle);

		# パンくずリスト生成
		$this->aBread[] = array('name'=>__('教材倉庫カテゴリ一覧'),'link'=>'/'.$this->baseName);
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		# カスタムボタン
		$aCustomBtn = array(
			array(
				'url'  => '/t/'.$this->baseName.'/create/'.$sID,
				'name' => __('教材の新規登録'),
				'show' => 1,
				'icon' => 'fa-upload',
			),
		);
		$this->template->set_global('aCustomBtn',$aCustomBtn);

		$this->template->content = View::forge('t/'.$this->baseName.'/list');
		$this->template->content->set('aMaterial',$aMaterial);
		$this->template->javascript = array('cl.t.material.js');
		return $this->template;
	}

	public function action_create($sID = null)
	{
		$aChk = self::MatCategoryChecker($sID);
		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}

		self::getContentsNums();
		$this->template->set_global('aNums',$this->aNums);

		# タイトル
		$sTitle = __('教材の新規登録');
		$this->template->set_global('pagetitle',$sTitle);

		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/'.$this->baseName, 'name'=>__('教材倉庫カテゴリ一覧'));
		$this->aBread[] = array('link'=>'/'.$this->baseName.'/list/'.$sID,'name'=>$this->aMCategory['mcName']);
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		$this->template->set_global('aMCategory',$this->aMCategory);

		if (!Input::post(null,false))
		{
			$data = $this->aMatBase;
			$data['error'] = null;
			if ($aInput = unserialize(Session::get('SES_T_MATERIAL',false)))
			{
				$data = array_merge($data,$aInput);
			}
			$this->template->content = View::forge('t/'.$this->baseName.'/edit',$data);
			$this->template->javascript = array('cl.t.material.js');
			return $this->template;
		}

		$aInput = Input::post();

		if ($aInput['m_file'] != '')
		{
			$aInput['fileinfo'] = unserialize($aInput['m_file']);
		}

		$val = Validation::forge();
		$val->add_callable('Helper_CustomValidation');
		$val->add_field('m_title', __('タイトル'), 'required|max_length['.CL_TITLE_LENGTH.']');

		if (is_array($aInput['m_url']))
		{
			foreach ($aInput['m_url'] as $i => $v)
			{
				$val->add_field('m_url.'.$i,__('教材URL').($i + 1),'url_opt');
			}
		}

		if (!$val->run())
		{
			$data = $this->aMatBase;
			$data['error'] = $val->error();
			$data = array_merge($data,$aInput);
			$this->template->content = View::forge('t/'.$this->baseName.'/edit',$data);
			$this->template->javascript = array('cl.t.material.js');
			return $this->template;
		}

		if (is_array($aInput['m_url']))
		{
			foreach ($aInput['m_url'] as $i => $v)
			{
				$aN = \Clfunc_Common::ExtUrlDetect($v);
				$aInput['clurl'][$i] = $aN['title'];
			}
		}

		Session::set('SES_T_MATERIAL',serialize($aInput));
		Response::redirect('/t/'.$this->baseName.'/check/'.$sID);
	}

	public function action_edit($sID = null,$iNO = null)
	{
		self::getContentsNums();
		$this->template->set_global('aNums',$this->aNums);

		$aChk = self::MatCategoryChecker($sID);
		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}
		$aChk = self::MatChecker($sID,$iNO);
		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}

		# タイトル
		$sTitle = __('教材の編集');
		$this->template->set_global('pagetitle',$sTitle);

		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/'.$this->baseName, 'name'=>__('教材倉庫カテゴリ一覧'));
		$this->aBread[] = array('link'=>'/'.$this->baseName.'/list/'.$sID,'name'=>$this->aMCategory['mcName']);
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		$this->template->set_global('aMCategory',$this->aMCategory);
		$this->template->set_global('aMaterial',$this->aMaterial);

		if (!Input::post(null,false))
		{
			$data = $this->aMatBase;
			$data['error'] = null;
			if (!$aInput = unserialize(Session::get('SES_T_MATERIAL',false)))
			{
				$aMFile = false;
				if ($this->aMaterial['fID'])
				{
					try
					{
						/*
						$sFileName = $this->aMaterial['fID'].'.'.$this->aMaterial['fExt'];
						$sTempPath = $this->sTempFilePath.DS.'_material_'.$sFileName;
						$result = \Clfunc_Aws::getFile($this->aMaterial['fPath'],$sFileName,$sTempPath);
						$aMFile = array(
							'file'  => '_material_'.$sFileName,
							'name'  => $this->aMaterial['fName'],
							'size'  => $this->aMaterial['fSize'],
							'isimg' => ($this->aMaterial['fFileType'] == 1)? 1:0,
						);
						*/
						$aMFile = array(
							'file'  => $this->aMaterial['fID'],
							'name'  => $this->aMaterial['fName'],
							'size'  => $this->aMaterial['fSize'],
							'isimg' => ($this->aMaterial['fFileType'] == 1)? 1:0,
						);
					}
					catch (Exception $e)
					{
						$aMFile = false;
					}
				}

				$aInput = array(
					'm_title'=>$this->aMaterial['mTitle'],
					'm_text' =>$this->aMaterial['mText'],
				);

				$aInput['m_url'] = explode("\n", $this->aMaterial['mURL']);
				if (is_array($aInput['m_url']))
				{
					foreach ($aInput['m_url'] as $i => $v)
					{
						$aN = \Clfunc_Common::ExtUrlDetect($v);
						$aInput['clurl'][$i] = $aN['title'];
					}
				}

				if ($aMFile)
				{
					$aInput['m_file'] = serialize($aMFile);
					$aInput['fileinfo'] = $aMFile;
				}
			}
			$data = array_merge($data,$aInput);
			$this->template->content = View::forge('t/'.$this->baseName.'/edit',$data);
			$this->template->javascript = array('cl.t.material.js');
			return $this->template;
		}

		$aInput = Input::post();

		if ($aInput['m_file'] != '')
		{
			$aInput['fileinfo'] = unserialize($aInput['m_file']);
		}

		$val = Validation::forge();
		$val->add_callable('Helper_CustomValidation');
		$val->add_field('m_title', __('タイトル'), 'required|max_length['.CL_TITLE_LENGTH.']');

		if (is_array($aInput['m_url']))
		{
			foreach ($aInput['m_url'] as $i => $v)
			{
				$val->add_field('m_url.'.$i,__('教材URL').($i + 1),'url_opt');
			}
		}

		if (!$val->run())
		{
			$data = $this->aMatBase;
			$data['error'] = $val->error();
			$data = array_merge($data,$aInput);
			$this->template->content = View::forge('t/'.$this->baseName.'/edit',$data);
			$this->template->javascript = array('cl.t.material.js');
			return $this->template;
		}

		if (is_array($aInput['m_url']))
		{
			foreach ($aInput['m_url'] as $i => $v)
			{
				$aN = \Clfunc_Common::ExtUrlDetect($v);
				$aInput['clurl'][$i] = $aN['title'];
			}
		}

		Session::set('SES_T_MATERIAL',serialize($aInput));
		Response::redirect('/t/'.$this->baseName.'/check/'.$sID.DS.$iNO);
	}

	public function action_check($sID = null, $iNO = 0)
	{
		$aChk = self::MatCategoryChecker($sID);
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

			$aChk = self::MatChecker($sID,$iNO);
			if (is_array($aChk))
			{
				Session::set('SES_T_ERROR_MSG',$aChk['msg']);
				Response::redirect($aChk['url']);
			}
		}

		$aInput = $this->aMatBase;
		$aSes = unserialize(Session::get('SES_T_MATERIAL',false));
		if (!$aSes)
		{
			Session::set('SES_T_ERROR_MSG',__('登録内容が取得できませんでした。再度入力してください。'));
			Response::redirect('/t/'.$this->baseName.'/'.$sMode.'/'.$sID.(($iNO)? DS.$iNO:''));
		}
		$aInput = array_merge($aInput,$aSes);

		# タイトル
		$sTitle = __('教材の'.$sMTitle);
		$this->template->set_global('pagetitle',$sTitle);

		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/'.$this->baseName, 'name'=>__('教材倉庫カテゴリ一覧'));
		$this->aBread[] = array('link'=>'/'.$this->baseName.'/list/'.$sID,'name'=>$this->aMCategory['mcName']);
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		$this->template->set_global('aMCategory',$this->aMCategory);
		$this->template->set_global('iNO',$iNO);

		if (!Input::post(null,false))
		{
			$data = $this->aMatBase;
			$data['error'] = null;
			$data = array_merge($data,$aInput);
			$this->template->content = View::forge('t/'.$this->baseName.'/check',$data);
			$this->template->content->set('aMaterial',$this->aMaterial);
			$this->template->javascript = array('cl.t.material.js');
			return $this->template;
		}

		$aPost = Input::post(null,false);
		if (isset($aPost['back']))
		{
			Response::redirect('/t/'.$this->baseName.'/'.$sMode.'/'.$sID.(($iNO)? DS.$iNO:''));
		}

		$sfID = null;
		$ifSize = 0;
		if ($aInput['fileinfo']['file'] != '')
		{
			if (isset($this->aMaterial['fID']) && $this->aMaterial['fID'] == $aInput['fileinfo']['file'])
			{
				$sfID = $this->aMaterial['fID'];
				$ifSize = $this->aMaterial['fSize'];
			}
			else
			{
				$sSourseFile = $this->sTempFilePath.DS.$aInput['fileinfo']['file'];
				$sThumbFile = $this->sTempFilePath.DS.CL_PREFIX_THUMBNAIL.$aInput['fileinfo']['file'];
				$sContentType = \Clfunc_Common::GetContentType($sSourseFile);
				$sExt = pathinfo($sSourseFile,PATHINFO_EXTENSION);
				$iFileType = \Clfunc_Common::GetFileType($sSourseFile);

				$sfID = \Model_File::getFileID();
				$ifSize = $aInput['fileinfo']['size'];
				$sFile = $sfID.'.'.$sExt;

				# 登録情報作成
				$aInsert = array(
					'fID'          => $sfID,
					'fName'        => $aInput['fileinfo']['name'],
					'fSize'        => $ifSize,
					'fExt'         => $sExt,
					'fContentType' => $sContentType,
					'fFileType'    => $iFileType,
					'fPath'        => $this->sAwsSavePath,
					'fUserType'    => 0,
					'fUser'        => $this->aTeacher['ttID'],
					'fDate'        => date('YmdHis'),
				);

				try
				{
					$result = \Clfunc_Aws::putFile($this->sAwsSavePath, $sFile, $sSourseFile, $sContentType);
					if ($iFileType == 1 && file_exists($sThumbFile))
					{
						$result = \Clfunc_Aws::putFile($this->sAwsSavePath, CL_PREFIX_THUMBNAIL.$sFile, $sThumbFile, $sContentType);
					}
					if ($iFileType == 2)
					{
						$result = \Clfunc_Aws::encodeMovie($this->sAwsSavePath, $sfID, $sExt);
					}
					$result = \Model_File::insertFile($aInsert);
				}
				catch (Exception $e)
				{
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
					\Clfunc_Common::LogOut($e,__CLASS__);
					Session::set('SES_T_ERROR_MSG',__('指定した教材ファイルが保存できませんでした。').$e->getMessage());
					Response::redirect('/t/'.$this->baseName.'/'.$sMode.'/'.$sID.(($iNO)? DS.$iNO:''));
				}
			}
		}

		try
		{
			if ($iNO)
			{
				$aUpdate = array(
					'mTitle' => $aInput['m_title'],
					'fID'    => $sfID,
					'mURL'   => implode("\n", $aInput['m_url']),
					'mText'  => $aInput['m_text'],
					'mID'    => $this->aTeacher['ttID'],
					'mDate'  => date('YmdHis'),
					'fSize'  => $ifSize,
				);
				$aWhere = array(
					array('mNO','=',$iNO),
					array('mcID','=',$sID),
				);
				$result = \Model_Material::updateMaterial($aUpdate,$aWhere);
			}
			else
			{
				$aInsert = array(
					'mcID'    => $sID,
					'mTitle'  => $aInput['m_title'],
					'fID'     => $sfID,
					'mPublic' => $aInput['m_public'],
					'mURL'    => implode("\n", $aInput['m_url']),
					'mText'   => $aInput['m_text'],
					'mID'     => $this->aTeacher['ttID'],
					'mDate'   => date('YmdHis'),
					'fSize'  => $ifSize,
				);
				$result = \Model_Material::insertMaterial($aInsert);

				if ($aInput['m_public'] == 1 && $this->aMCategory['mcMail'] > 0)
				{
					$aOptions = array(
						'mcName' => $this->aMCategory['mcName'],
						'mTitle' => $aInput['m_title'],
					);
					\ClFunc_Mailsend::MailSendToClassStudents($this->aTeacher['ttID'],$this->aMCategory['ctID'],'MatPublic',$aOptions);
				}
			}
		}
		catch (Exception $e)
		{
			if ($sfID)
			{
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
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		if ($sfID)
		{
			@unlink($sSourseFile);
			@unlink($sThumbFile);
		}
		if ($iNO && $this->aMaterial['fID'] && $this->aMaterial['fID'] != $sfID)
		{
			\Clfunc_Aws::deleteFile($this->aMaterial['fPath'],$this->aMaterial['fID'].'.'.$this->aMaterial['fExt']);
			if ($this->aMaterial['fFileType'] == 1)
			{
				\Clfunc_Aws::deleteFile($this->sAwsSavePath, CL_PREFIX_THUMBNAIL.$this->aMaterial['fID'].'.'.$this->aMaterial['fExt']);
			}
			if ($this->aMaterial['fFileType'] == 2)
			{
				\Clfunc_Aws::deleteFile($this->sAwsSavePath,CL_PREFIX_ENCODE.$this->aMaterial['fID'].CL_AWS_ENCEXT);
				\Clfunc_Aws::deleteFile($this->sAwsSavePath,CL_PREFIX_THUMBNAIL.$this->aMaterial['fID'].'-00001.png');
			}
			\Model_File::deleteFile($this->aMaterial['fID']);
		}

		Session::delete('SES_T_MATERIAL');
		Session::set('SES_T_NOTICE_MSG',__('教材を'.(($iNO)? '更新':'登録').'しました。'));
		Response::redirect('/t/'.$this->baseName.'/list/'.$sID);
	}

	public function action_delete($sID = null, $iNO = null)
	{
		$aChk = self::MatCategoryChecker($sID);
		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}
		$aChk = self::MatChecker($sID,$iNO);
		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}

		try
		{
			$result = Model_Material::deleteMaterial($this->aMaterial);
/*
			if ($this->aMaterial['fID'])
			{
				\Clfunc_Aws::deleteFile($this->sAwsSavePath,$this->aMaterial['fID'].'.'.$this->aMaterial['fExt']);
				if ($this->aMaterial['fFileType'] == 1)
				{
					\Clfunc_Aws::deleteFile($this->sAwsSavePath, CL_PREFIX_THUMBNAIL.$this->aMaterial['fID'].'.'.$this->aMaterial['fExt']);
				}
				if ($this->aMaterial['fFileType'] == 2)
				{
					\Clfunc_Aws::deleteFile($this->sAwsSavePath,CL_PREFIX_ENCODE.$this->aMaterial['fID'].CL_AWS_ENCEXT);
					\Clfunc_Aws::deleteFile($this->sAwsSavePath,CL_PREFIX_THUMBNAIL.$this->aMaterial['fID'].'-00001.png');
				}
				\Model_File::deleteFile($this->aMaterial['fID']);
			}
*/
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		Session::set('SES_T_NOTICE_MSG',__('教材を削除しました。'));
		Response::redirect('/t/'.$this->baseName.'/list/'.$sID);
	}

	public function action_already($sID = null, $iNO = null)
	{
		$aChk = self::MatCategoryChecker($sID);
		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}
		$aChk = self::MatChecker($sID,$iNO);
		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}

		$aStudent = null;
		$result = Model_Student::getStudentFromClass($this->aClass['ctID'],null,null,array('st.stNO'=>'asc','st.stName'=>'acs','st.stLogin'=>'acs'));
		if (count($result))
		{
			$aRes = $result->as_array();
			foreach ($aRes as $aR)
			{
				$aStudent[$aR['stID']] = $aR;
			}
		}

		$iCnt = 0;
		$result = Model_Material::getMaterialAlready(array(array('mNO','=',$iNO)));
		if (count($result))
		{
			$aRes = $result->as_array();
			foreach ($aRes as $aR)
			{
				if (isset($aStudent[$aR['stID']]))
				{
					$aStudent[$aR['stID']]['already'] = $aR['maDate'];
					$iCnt++;
				}
			}
		}

		# タイトル
		$sTitle = $this->aMaterial['mTitle'].' '.__('既読一覧').'（'.$iCnt.'/'.$this->aClass['scNum'].'）';
		$this->template->set_global('pagetitle',$sTitle);

		# パンくずリスト生成
		$this->aBread[] = array('name'=>__('教材倉庫カテゴリ一覧'),'link'=>'/'.$this->baseName);
		$this->aBread[] = array('name'=>$this->aMCategory['mcName'],'link'=>'/'.$this->baseName.'/list/'.$sID);
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		$this->template->content = View::forge('t/'.$this->baseName.'/already');
		$this->template->content->set('aStudent',$aStudent);
		$this->template->javascript = array('cl.t.material.js');
		return $this->template;

	}


	private function MatCategoryChecker($sMcID = null)
	{
		if (is_null($this->aClass))
		{
			return array('msg'=>__('講義情報が確認できませんでした。'),'url'=>'/t/index');
		}
		if (is_null($sMcID))
		{
			return array('msg'=>__('カテゴリ情報が送信されていません。'),'url'=>'/t/'.$this->baseName);
		}
		$result = Model_Material::getMaterialCategoryFromID($sMcID);
		if (!count($result))
		{
			return array('msg'=>__('指定されたカテゴリが見つかりません。').$sMcID,'url'=>'/t/'.$this->baseName);
		}
		$this->aMCategory = $result->current();

		return true;
	}

	private function MatChecker($sMcID = null, $iMtNO = null)
	{
		if (is_null($this->aClass))
		{
			return array('msg'=>__('講義情報が確認できませんでした。'),'url'=>'/t/index');
		}
		if (is_null($sMcID) || is_null($iMtNO))
		{
			return array('msg'=>__('必要な情報が送信されていません。'),'url'=>'/t/'.$this->baseName);
		}
		$result = Model_Material::getMaterial(array(array('mt.mcID','=',$sMcID),array('mt.mNO','=',$iMtNO)));
		if (!count($result))
		{
			return array('msg'=>__('指定された教材が見つかりません。'),'url'=>'/t/'.$this->baseName);
		}
		$this->aMaterial = $result->current();

		return true;
	}

	private function getContentsNums()
	{
		$this->aNums = array('Quest'=>0,'Test'=>0,'Coop'=>0,'Report'=>0);

		$result = Model_Quest::getQuestBaseFromClass($this->aClass['ctID']);
		$this->aNums['Quest'] = count($result);
		$result = Model_Test::getTestBaseFromClass($this->aClass['ctID']);
		$this->aNums['Test'] = count($result);
		$result = Model_Coop::getCoopCategoryFromClass($this->aClass['ctID'],array(array('ccItemNum','>',0)));
		$this->aNums['Coop'] = count($result);
		$result = Model_Report::getReportBase(array(array('rb.ctID','=',$this->aClass['ctID'])));
		$this->aNums['Report'] = count($result);

		return;
	}


}