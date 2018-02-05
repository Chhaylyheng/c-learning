<?php
class Controller_T_Test extends Controller_T_Baseclass
{
	private $bn = 't/test';
	private $config;

	private $aTestBase = array(
		't_name'=>null, 't_auto_public'=>0,
		't_auto_s_date'=>null, 't_auto_e_date'=>null, 't_auto_s_time'=>null, 't_auto_e_time'=>null,
		't_qualify_score'=>0, 't_limit_time'=>0, 't_explain'=>null, 'tbImage'=>null, 't_query_rand'=>0,
		't_select_style'=>1, 't_query_rand'=>0,
		't_score_public'=>3, 't_com_public'=>0,
	);
	private $aSearchCol = array(
		'stLogin','stName','stNO','stDept','stSubject','stYear','stClass','stCourse'
	);
	private $aDrillBase = array(
		'd_title'=>null,
		'd_pubnum'=>10,
		'd_rand'=>1,
		'd_select_style'=>1,
		'd_query_rand'=>1,
	);


	public function before()
	{
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
		$aTSAL = null;
		while (is_null($aTSAL))
		{
			$result = Model_Class::getClassArchive($this->aClass['ctID'],'TestStuAnsList');
			if (!count($result))
			{
				try
				{
					$aInsert = array(
						'ctID' => $this->aClass['ctID'],
						'caType' => 'TestStuAnsList',
						'caProgress' => 0,
						'caDate' => date('YmdHis'),
					);
					$result = Model_Class::insertClassArchive($aInsert);
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
				$aTSAL = $result->current();
			}
		}

		$aTest = null;
		$result = Model_Test::getTestBaseFromClass($this->aClass['ctID'],null,null,array('tb.tbSort'=>'desc'));
		if (count($result))
		{
			$aTest = $result->as_array();
		}

		# タイトル
		$sTitle = __('小テスト');
		$this->template->set_global('pagetitle',$sTitle);

		# パンくずリスト生成
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		# カスタムメニュー
		$aCustomMenu = array(
			array(
				'url'  => DS.$this->bn.'/create/'.mt_rand(),
				'name' => __('小テストの新規作成'),
				'show' => 1,
			),
			array(
				'url'  => DS.$this->bn.'/csv/',
				'name' => __('CSVから小テストの登録'),
				'show' => 1,
			),
			array(
				'url'  => '/t/output/testputlist.csv',
				'name' => __('提出一覧CSVのダウンロード'),
				'show' => 0,
				'icon' => 'fa-download',
			),
			array(
				'url'  => DS.$this->bn.'/archive',
				'name' => __('解答一覧ファイルアーカイブの作成'),
				'show' => 0,
				'icon' => 'fa-archive',
			),
		);
		$this->template->set_global('aCustomMenu',$aCustomMenu);

		$aCustomBtn = array(
			array(
				'url'  => DS.$this->bn.'/create/'.mt_rand(),
				'name' => __('小テストの新規作成'),
				'show' => 1,
			),
			array(
				'url'  => DS.$this->bn.'/putlist/',
				'name' => __('提出一覧'),
				'show' => 0,
			),
		);

		if ($aTSAL['caProgress'] == 2)
		{
			$aCustomBtn = array(
				'url' => '#',
				'name' => __('アーカイブファイルの作成失敗'),
				'show' => 0,
				'icon' => 'fa-exclamation-triangle',
				'option' => array(
					'id' => 'archive-download-btn',
					'obj' => $this->aClass['ctID'].'_QuestStuAnsList',
					'disabled' => 'disabled',
				),
			);
		}
		else if ($aTSAL['caProgress'] == 1)
		{
			$aCustomBtn[] = array(
				'url' => '#',
				'name' => __('アーカイブファイルを作成中…'),
				'show' => 0,
				'icon' => 'fa-spinner fa-spin',
				'option' => array(
					'id' => 'archive-download-btn',
					'obj' => $this->aClass['ctID'].'_TestStuAnsList',
					'disabled' => 'disabled',
				),
			);
		}
		else if ($aTSAL['fID'])
		{
			$aCustomBtn[] = array(
				'url' => \Uri::create('getfile/s3file/:fid/:mode',array('fid'=>$aTSAL['fID'],'mode'=>'e')),
				'name' => __('アーカイブファイルのダウンロード').' ('.ClFunc_Tz::tz('Y/m/d H:i',$this->tz,$aTSAL['fDate']).' '.\Clfunc_Common::FilesizeFormat($aTSAL['fSize'],1).')',
				'show' => 0,
				'icon' => 'fa-download',
				'option' => array(
					'id' => 'archive-download-btn',
					'obj' => $this->aClass['ctID'].'_TestStuAnsList',
				),
			);
		}
		$this->template->set_global('aCustomBtn',$aCustomBtn);

		$this->template->content = View::forge($this->bn.'/index');
		$this->template->content->set('aTest',$aTest);
		$this->template->content->set('aTSAL',$aTSAL);
		$this->template->javascript = array('cl.t.test.js');
		return $this->template;
	}

	public function action_create($sKey = null)
	{
		$sTempPath = CL_UPPATH.DS.'temp'.DS.'test'.DS.$sKey;
		# タイトル
		$sTitle = __('小テストの新規作成');
		$this->template->set_global('pagetitle',$sTitle);

		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/test','name'=>__('小テスト'));
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		# ファイル格納キー
		$this->template->set_global('fKey',$sKey);

		if (!Input::post(null,false))
		{
			$data = $this->aTestBase;
			$data['t_auto_s_date'] = ClFunc_Tz::tz('Y/m/d',$this->tz);
			$data['t_auto_e_date'] = ClFunc_Tz::tz('Y/m/d',$this->tz);
			$data['t_auto_s_time'] = ClFunc_Tz::tz('H:i',$this->tz);
			$data['t_auto_e_time'] = ClFunc_Tz::tz('H:i',$this->tz);
			$data['error'] = null;
			$this->template->content = View::forge('t/test/edit',$data);
			$this->template->javascript = array('jquery.timepicker.js','cl.t.test.js');
			return $this->template;
		}

		$aInput = Input::post();

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

		if (isset($aInput['error']))
		{
			$this->template->content = View::forge('t/test/edit',$aInput);
			$this->template->javascript = array('jquery.timepicker.js','cl.t.test.js');
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
			$result = Model_Test::insertTest($aInsert);
			$sSavePath = CL_UPPATH.DS.$result.DS.'base';
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		if ($aInput['tbImage'])
		{
			ClFunc_Common::chkDir($sSavePath,true);
			File::rename($sTempPath.DS.$aInput['tbImage'],$sSavePath.DS.$aInput['tbImage']);
			File::rename($sTempPath.DS.CL_Q_SMALL_PREFIX.$aInput['tbImage'],$sSavePath.DS.CL_Q_SMALL_PREFIX.$aInput['tbImage']);
		}

		if (isset($aInput['finish']))
		{
			Session::set('SES_T_NOTICE_MSG',__('小テストを作成しました。'));
			Response::redirect('/t/test');
		}
		else
		{
			Response::redirect('/t/test/querylist/'.$result);
		}
	}

	public function action_edit($sID = null)
	{
		$aTest = null;
		$aChk = self::TestChecker($sID,$aTest);
		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}

		$sTempPath = CL_UPPATH.DS.$aTest['tbID'].DS.'base_tmp';
		$sSavePath = CL_UPPATH.DS.$aTest['tbID'].DS.'base';

		# タイトル
		$sTitle = __('小テスト情報の編集');
		$this->template->set_global('pagetitle',$sTitle);

		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/test','name'=>__('小テスト'));
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		if (!Input::post(null,false))
		{
			if (file_exists($sTempPath))
			{
				system('rm -rf '.$sTempPath);
			}
			if (file_exists($sSavePath))
			{
				system('cp -Rfp '.$sSavePath.' '.$sTempPath);
			}

			$data = $this->aTestBase;
			$data['t_name']          = $aTest['tbTitle'];
			$data['t_auto_public']   = ($aTest['tbAutoPublicDate'] != CL_DATETIME_DEFAULT)? 1:0;
			$data['t_auto_s_date']   = ($aTest['tbAutoPublicDate'] != CL_DATETIME_DEFAULT)? ClFunc_Tz::tz('Y/m/d',$this->tz,$aTest['tbAutoPublicDate']):ClFunc_Tz::tz('Y/m/d',$this->tz);
			$data['t_auto_s_time']   = ($aTest['tbAutoPublicDate'] != CL_DATETIME_DEFAULT)? ClFunc_Tz::tz('H:i',$this->tz,$aTest['tbAutoPublicDate']):ClFunc_Tz::tz('H:i',$this->tz);
			$data['t_auto_e_date']   = ($aTest['tbAutoCloseDate'] != CL_DATETIME_DEFAULT)? ClFunc_Tz::tz('Y/m/d',$this->tz,$aTest['tbAutoCloseDate']):ClFunc_Tz::tz('Y/m/d',$this->tz);
			$data['t_auto_e_time']   = ($aTest['tbAutoCloseDate'] != CL_DATETIME_DEFAULT)? ClFunc_Tz::tz('H:i',$this->tz,$aTest['tbAutoCloseDate']):ClFunc_Tz::tz('H:i',$this->tz);
			$data['t_select_style']  = $aTest['tbQueryStyle'];
			$data['t_qualify_score'] = $aTest['tbQualifyScore'];
			$data['t_limit_time']    = $aTest['tbLimitTime'];
			$data['t_score_public']  = $aTest['tbScorePublic'];
			$data['t_explain']       = $aTest['tbExplain'];
			$data['tbImage']          = $aTest['tbExplainImage'];
			$data['t_query_rand']    = $aTest['tbQueryRand'];
			$data['error'] = null;
			$this->template->content = View::forge('t/test/edit',$data);
			$this->template->content->set('aTest',$aTest);
			$this->template->javascript = array('jquery.timepicker.js','cl.t.test.js');
			return $this->template;
		}

	}

	public function post_editchk($sID = null)
	{
		$aTest = null;
		$aChk = self::TestChecker($sID,$aTest);
		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}

		$sTempPath = CL_UPPATH.DS.$aTest['tbID'].DS.'base_tmp';
		$sSavePath = CL_UPPATH.DS.$aTest['tbID'].DS.'base';

		# タイトル
		$sTitle = __('小テスト情報の編集');
		$this->template->set_global('pagetitle',$sTitle);

		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/test','name'=>__('小テスト'));
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		$aInput = Input::post();

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
						$aInput['error']['tbImage'] = __('解説に登録できるファイルのサイズは:sizeMBまでです。',array('size'=>CL_IMGSIZE));
						break;
					case Upload::UPLOAD_ERR_EXTENSION:
					case Upload::UPLOAD_ERR_EXT_BLACKLISTED:
					case Upload::UPLOAD_ERR_EXT_NOT_WHITELISTED:
					case Upload::UPLOAD_ERR_TYPE_BLACKLISTED:
					case Upload::UPLOAD_ERR_TYPE_NOT_WHITELISTED:
					case Upload::UPLOAD_ERR_MIME_BLACKLISTED:
					case Upload::UPLOAD_ERR_MIME_NOT_WHITELISTED:
						$aInput['error']['tbImage'] = __('解説に登録できるファイルは画像（JPG,JPEG）のみです。');
						break;
					case Upload::UPLOAD_ERR_NO_FILE:
						# ファイルを指定していない
						break;
					default:
						$aInput['error']['tbImage'] = __('解説のファイルアップロードに失敗しました。');
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
			ClFunc_Common::OrientationFixedImage($sTempPath.DS.$sTempImg);
			$image = Image::load($sTempPath.DS.$sTempImg);
			$image->config('quality',CL_Q_IMG_QUALITY)->resize(CL_Q_IMG_SIZE,(CL_Q_IMG_SIZE / 4 * 3),true)->save($sTempPath.DS.$aInput['tbImage'],0666);
			$image->config('quality',CL_Q_SMALL_QUALITY)->resize(CL_Q_SMALL_SIZE,(CL_Q_SMALL_SIZE / 4 * 3),true)->save($sTempPath.DS.$sThumbImg,0666);
			File::delete($sTempPath.DS.$sTempImg);
		}

		if (isset($aInput['error']))
		{
			$this->template->content = View::forge('t/test/edit',$aInput);
			$this->template->content->set('aTest',$aTest);
			$this->template->javascript = array('jquery.timepicker.js','cl.t.test.js');
			return $this->template;
		}

		// 更新データ生成
		$aUpdate = array(
				'tbTitle'        => $aInput['t_name'],
				'tbQueryStyle'   => $aInput['t_select_style'],
				'tbQualifyScore' => $aInput['t_qualify_score'],
				'tbLimitTime'    => $aInput['t_limit_time'],
				'tbExplain'      => $aInput['t_explain'],
				'tbExplainImage' => $aInput['tbImage'],
				'tbScorePublic'  => $aInput['t_score_public'],
				'tbQueryRand'    => $aInput['t_query_rand'],
				'tbDate'         => date('YmdHis'),
		);

		if ($aInput['t_auto_public'])
		{
			$aUpdate['tbAutoPublicDate'] = ClFunc_Tz::tz('Y-m-d H:i:s',null,$aInput['t_auto_s_date'].' '.$aInput['t_auto_s_time'].':00',$this->tz);
			$aUpdate['tbAutoCloseDate'] = ClFunc_Tz::tz('Y-m-d H:i:s',null,$aInput['t_auto_e_date'].' '.$aInput['t_auto_e_time'].':00',$this->tz);
		}
		else
		{
			$aUpdate['tbAutoPublicDate'] = CL_DATETIME_DEFAULT;
			$aUpdate['tbAutoCloseDate'] = CL_DATETIME_DEFAULT;
		}

		try
		{
			$result = Model_Test::updateTest($aUpdate,array(array('tbID','=',$sID)));
			if ($aTest['tpNum'] && $aInput['t_qualify_score'] != $aTest['tbQualifyScore'])
			{
				$result = Model_Test::resetScore($sID);
			}
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		if ($aInput['tbImage'])
		{
			ClFunc_Common::chkDir($sSavePath,true);
			File::rename($sTempPath.DS.$aInput['tbImage'],$sSavePath.DS.$aInput['tbImage']);
			File::rename($sTempPath.DS.CL_Q_SMALL_PREFIX.$aInput['tbImage'],$sSavePath.DS.CL_Q_SMALL_PREFIX.$aInput['tbImage']);
		}

		if (isset($aInput['finish']))
		{
			Session::set('SES_T_NOTICE_MSG',__('小テスト情報を更新しました。'));
			Response::redirect('/t/test');
		}
		else
		{
			Response::redirect('/t/test/querylist/'.$sID);
		}

	}


	public function action_copy($sID = null)
	{
		$aTest = null;
		$aChk = self::TestChecker($sID,$aTest);
		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}
		$this->template->set_global('aTest',$aTest);

		# タイトル
		$sTitle = __('小テストのコピー');
		$sTitle .= '（'.$aTest['tbTitle'].'）';
		$this->template->set_global('pagetitle',$sTitle);

		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/test','name'=>__('小テスト'));
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		# カスタムボタン
		$aCustomBtn = array(
			array(
				'url'  => '',
				'name' => __('コピー実行'),
				'show' => 0,
				'icon' => 'fa-files-o',
				'option' => array(
					'id' => 'TestCopyExec',
				),
			),
		);
		$this->template->set_global('aCustomBtn',$aCustomBtn);


		if ($this->aTeacher['gtID'])
		{
			$result = Model_Group::getGroupClasses(array(array('gtID','=',$this->aTeacher['gtID'])));
		}
		else
		{
			$result = Model_Class::getClassFromTeacher($this->aTeacher['ttID'],null);
		}
		$aCtIDs = null;
		$aClasses = null;
		if (count($result))
		{
			foreach ($result as $aC)
			{
				$aCtIDs[$aC['ctID']] = $aC;

				if ($aC['ctID'] == $this->aClass['ctID'])
				{
					continue;
				}
				if ($aC['ttID'] == $this->aTeacher['ttID'])
				{
					$aClasses[0][$aC['ctID']] = $aC;
					continue;
				}
				$aClasses[1][$aC['ctID']] = $aC;
			}
		}
		$this->template->set_global('aClasses',$aClasses);

		if (!Input::post(null,false))
		{
			$data = array(
				'selclass'=>null,
				'error'=>null,
			);

			$this->template->content = View::forge('t/test/copy',$data);
			$this->template->javascript = array('cl.t.test.js');
			return $this->template;
		}

		$aInput = Input::post();
		$aSelClass = null;
		$sFin = null;
		$sMsg = null;
		if (!isset($aInput['selclass']) || !count($aInput['selclass']))
		{
			$sMsg = __('講義を選択してください。');
		}
		else
		{
			foreach ($aInput['selclass'] as $sC)
			{
				if (!isset($aCtIDs[$sC]))
				{
					$sMsg = __('コピー先の講義が見つかりません。');
					break;
				}
				$aSelClass[$sC] = $aCtIDs[$sC];
				$sFin .= "\n　".$aCtIDs[$sC]['ctName'].' ['.\Clfunc_Common::getCode($aCtIDs[$sC]['ctCode']).']'.(($this->aTeacher['gtID'])? '（'.$aCtIDs[$sC]['ttName'].'）':'');
			}
		}
		if (!is_null($sMsg))
		{
			$data = $aInput;
			$data['error']['selclass'] = $sMsg;
			$this->template->content = View::forge('t/test/copy',$data);
			$this->template->javascript = array('cl.t.test.js');
			return $this->template;
		}

		try
		{
			$result = Model_Test::copyTest($aTest,$aSelClass);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		Session::set('SES_T_NOTICE_MSG',__('小テストのコピーが完了しました。（:num件）',array('num'=>$result)).$sFin);
		Response::redirect('/t/test');
	}


	public function action_csv()
	{
		if (!$this->aClass['ctStatus'])
		{
			Session::set('SES_T_ERROR_MSG',__('終了した講義には小テストを新規作成することはできません。'));
			Response::redirect('/t/test');
		}

		# タイトル
		$sTitle = __('CSVから小テストの登録');
		$this->template->set_global('pagetitle',$sTitle);

		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/test','name'=>__('小テスト'));
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		if (!Input::post(null,false))
		{
			$data = array('error'=>null);
			$this->template->content = View::forge('t/test/csv',$data);
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
			$this->template->content = View::forge('t/test/csv',$data);
			return $this->template;
		}

		$oFile = Upload::get_files('tt_csv');
		$aCSV = ClFunc_Common::getCSV($oFile['file']);
		if (!count($aCSV))
		{
			$data['error'] = array('tt_csv'=>__('登録するデータがCSVから取得できませんでした。'));
			$this->template->content = View::forge('t/test/csv',$data);
			return $this->template;
		}

		$aMsg = null;
		$sDatePtn = "/^([0-9]{4}\/[0-9]{1,2}\/[0-9]{1,2})\s+([0-9]{1,2}:[0-9]{1,2}|[0-9]{1,2}:[0-9]{1,2}:[0-9]{1,2})$/";
		$iQN = 0;
		$iCN = 0;
		$aBase = array(
			'tbAutoPublicDate' => CL_DATETIME_DEFAULT,
			'tbAutoCloseDate' => CL_DATETIME_DEFAULT,
		);
		$aQuery = null;
		$iSd = 0;
		$iEd = 0;
		$iTotal = 0;
		if (!empty($aCSV))
		{
			foreach ($aCSV as $iKey => $aS)
			{
				switch ($aS[0])
				{
					case '小テストタイトル':
					case 'テストタイトル':
					case __('小テストタイトル'):
					case __('テストタイトル'):
						if ($aS[1] == '')
						{
							$aMsg[] = __('小テストタイトルが指定されていません。');
							$aBase['tbTitle'] = '';
						}
						else
						{
							$aBase['tbTitle'] = mb_substr(strip_tags($aS[1]), 0, CL_TITLE_LENGTH);
						}
					break;
					case '選択肢の表示方法':
					case '選択肢配置':
					case '選択肢配列':
					case __('選択肢の表示方法'):
					case __('選択肢配置'):
					case __('選択肢配列'):
						if ($aS[1] == '3' || $aS[1] == '2' || $aS[1] == '1')
						{
							$aBase['tbQueryStyle'] = (int)$aS[1];
						}
						else
						{
							$aBase['tbQueryStyle'] = 1;
						}
					break;
					case '選択肢の並び順':
					case __('選択肢の並び順'):
						if ($aS[1] == '1' || $aS[1] == '0')
						{
							$aBase['tbQueryRand'] = (int)$aS[1];
						}
						else
						{
							$aBase['tbQueryRand'] = 0;
						}
					break;
					case '合格点数':
					case __('合格点数'):
						if (is_numeric($aS[1]))
						{
							$aBase['tbQualifyScore'] = (int)$aS[1];
						}
						else
						{
							$aBase['tbQualifyScore'] = 0;
						}
					break;
					case '制限時間':
					case __('制限時間'):
						if (is_numeric($aS[1]))
						{
							$aBase['tbLimitTime'] = (int)$aS[1];
						}
						else
						{
							$aBase['tbLimitTime'] = 0;
						}
					break;
					case '公開予定日時(年/月/日 時:分)':
					case '開始予定日時(年/月/日 時:分)':
					case __('公開予定日時(年/月/日 時:分)'):
					case __('開始予定日時(年/月/日 時:分)'):
						if ($aS[1] != '')
						{
							if (!preg_match($sDatePtn,$aS[1],$aSDate))
							{
								$aMsg[] = __('公開予定日時はYYYY/MM/DD HH:mm(:SS)形式で記入してください。');
							}
							else
							{
								if (!\Clfunc_Common::dateValidation($aSDate[1],true))
								{
									$aMsg[] = __('公開予定日が無効な値です。');
								}
								$aSTime = explode(':',$aSDate[2]);
								$aSTime[1] = $aSTime[1] - ($aSTime[1] % 5);
								$iSd  = strtotime($aSDate[1].' '.$aSTime[0].':'.$aSTime[1].':00');

								if (!\Clfunc_Common::timeValidation($aSTime[0].':'.$aSTime[1],$aSDate[1],true,array('min'=>ClFunc_Tz::tz('Y-m-d H:i:00',$this->tz))))
								{
									$aMsg[] = __('公開予定日時が無効な値か、現在より過去に設定されています。');
								}
								else
								{
									$aBase['tbAutoPublicDate'] = ClFunc_Tz::tz('Y/m/d H:i:s',null,$aSDate[1].' '.$aSTime[0].':'.$aSTime[1].':00',$this->tz);

									if ($iEd > 0)
									{
										if ($iSd >= $iEd)
										{
											$aMsg[] = __('締切予定日時は公開予定日時より未来に設定してください。');
										}
									}
								}
							}
						}
					break;
					case '締切予定日時(年/月/日 時:分)':
					case '終了予定日時(年/月/日 時:分)':
					case __('締切予定日時(年/月/日 時:分)'):
					case __('終了予定日時(年/月/日 時:分)'):
						if ($aS[1] != '')
						{
							if (!preg_match($sDatePtn,$aS[1],$aEDate))
							{
								$aMsg[] = __('締切予定日時はYYYY/MM/DD HH:mm(:SS)形式で記入してください。');
							}
							else
							{
								if (!\Clfunc_Common::dateValidation($aEDate[1],true))
								{
									$aMsg[] = __('締切予定日が無効な値です。');
								}
								$aETime = explode(':',$aEDate[2]);
								$aETime[1] = $aETime[1] - ($aETime[1] % 5);
								$iEd  = strtotime($aEDate[1].' '.$aETime[0].':'.$aETime[1].':00');

								if (!\Clfunc_Common::timeValidation($aETime[0].':'.$aETime[1],$aEDate[1],true))
								{
									$aMsg[] = __('締切予定日時が無効な値です');
								}
								else
								{
									$aBase['tbAutoCloseDate'] = ClFunc_Tz::tz('Y/m/d H:i:s',null,$aEDate[1].' '.$aETime[0].':'.$aETime[1].':00',$this->tz);
									if ($iSd > 0)
									{
										if ($iSd >= $iEd)
										{
											$aMsg[] = __('締切予定日時は公開予定日時より未来に設定してください。');
										}
									}
								}
							}
						}
					break;
					case '成績公開':
					case '点数、解説の公開':
					case __('成績公開'):
					case __('点数、解説の公開'):
						if ($aS[1] == '3' || $aS[1] == '2' || $aS[1] == '1' || $aS[1] == '0')
						{
							$aBase['tbScorePublic'] = (int)$aS[1];
						}
						else
						{
							$aBase['tbScorePublic'] = 0;
						}
					break;
					case '小テストの全体的な解説':
					case '解説文':
					case __('小テストの全体的な解説'):
					case __('解説文'):
						if ($iQN > 0)
						{
							if ($aS[1] != '')
							{
								$aQuery[$iQN]['tqExplain'] = strip_tags($aS[1]);
							}
							else
							{
								$aQuery[$iQN]['tqExplain'] = '';
							}
						}
						else
						{
							if ($aS[1] != '')
							{
								$aBase['tbExplain'] = strip_tags($aS[1]);
							}
							else
							{
								$aBase['tbExplain'] = '';
							}
						}
					break;
					case '回答形式':
					case __('回答形式'):
						if ($iQN > 0)
						{
							$aQuery[$iQN]['tqChoiceNum'] = $iCN;
							if (($aQuery[$iQN]['tqStyle'] === 1 || $aQuery[$iQN]['tqStyle'] === 0) && $iCN < 2)
							{
								$aMsg[] = __(':no問目の選択肢を二つ以上指定してください。',array('no'=>$iQN));
							}
							if ($iRN == 0)
							{
								$aMsg[] = __(':no問目の正解を指定してください。',array('no'=>$iQN));
							}
							if ($aQuery[$iQN]['tqStyle'] === 0 && $aQuery[$iQN]['tqRight1'] > $iCN)
							{
								$aMsg[] = __(':no問目の正解で指定した番号が選択肢数を越えています。',array('no'=>$iQN));
							}
							if ($aQuery[$iQN]['tqStyle'] === 1)
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
						$aQuery[$iQN]['tqStyle'] = null;

						switch($aS[1])
						{
							case 'radio':
								$aQuery[$iQN]['tqStyle'] = 0;
							break;
							case 'select':
								$aQuery[$iQN]['tqStyle'] = 1;
							break;
							case 'text':
								$aQuery[$iQN]['tqStyle'] = 2;
							break;
							default:
								$aMsg[] = __(':no問目の形式が正しく指定されていません。',array('no'=>$iQN));
								continue;
							break;
						}
					break;
					case '配点':
					case __('配点'):
						if (is_numeric($aS[1]))
						{
							$iTotal += (int)$aS[1];
							$aQuery[$iQN]['tqScore'] = (int)$aS[1];
						}
						else
						{
							$aMsg[] = __(':no問目の配点が無効な値です。',array('no'=>$iQN));
						}
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
							$aQuery[$iQN]['tqText'] = strip_tags($aS[1]);
						}
					break;
					default:
						if (preg_match('/^(正解|'.__('正解').')/', $aS[0]))
						{
							if ($aQuery[$iQN]['tqStyle'] == 2)
							{
								$sRight = str_replace(array("\r\n","\n","\r"), '', strip_tags($aS[1]));
								if ($sRight != '')
								{
									$iRN++;
									if ($iRN <= 5)
									{
										$sRight = \ClFunc_Common::convertKana(preg_replace(CL_WHITE_TRIM_PTN, '$1', $sRight),'aqpu');
										$aQuery[$iQN]['tqRight'.$iRN] = $sRight;
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
								if ($aQuery[$iQN]['tqStyle'] == 0)
								{
									if (is_numeric($aS[1]) && $aS[1] >= 1 && $aS[1] <= 50)
									{
										$iRN = 1;
										$aQuery[$iQN]['tqRight1'] = (int)$aS[1];
									}
									else
									{
										$aMsg[] = __(':no問目の正解1が無効な値です。',array('no'=>$iQN));
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
										$aQuery[$iQN]['tqRight1'] = $aS[1];
									}
								}
							}
							break;
						}
						if (preg_match('/^(選択肢|'.__('選択肢').')/', $aS[0]))
						{
							if ($aQuery[$iQN]['tqStyle'] == 2)
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
								$aQuery[$iQN]['tqChoice'.$iCN] = strip_tags($aS[1]);
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
				$aQuery[$iQN]['tqChoiceNum'] = $iCN;
				if (($aQuery[$iQN]['tqStyle'] === 1 || $aQuery[$iQN]['tqStyle'] === 0) && $iCN < 2)
				{
					$aMsg[] = __(':no問目の選択肢を二つ以上指定してください。',array('no'=>$iQN));
				}
				if ($iRN == 0)
				{
					$aMsg[] = __(':no問目の正解を指定してください。',array('no'=>$iQN));
				}
				if ($aQuery[$iQN]['tqStyle'] === 0 && $aQuery[$iQN]['tqRight1'] > $iCN)
				{
					$aMsg[] = __(':no問目の正解で指定した番号が選択肢数を越えています。',array('no'=>$iQN));
				}
				if ($aQuery[$iQN]['tqStyle'] === 1)
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
			if (!isset($aBase['tbTitle']))
			{
				$aMsg[] = __('小テストタイトルが指定されていません。');
			}
			if ($aBase['tbQualifyScore'] > $iTotal)
			{
				$aMsg[] = __('合格点数が問題の合計点よりも高くなっています。');
			}
			if (($iSd > 0 && $iEd == 0) || ($iEd > 0 && $iSd == 0))
			{
				$aMsg[] = __('自動公開をする場合、公開予定日時と締切予定日時は両方指定してください。');
			}
		}
		else
		{
			$data['error'] = array('tt_csv'=>__('登録するデータがありません。'));
			$this->template->content = View::forge('t/test/csv',$data);
			return $this->template;
		}
		if (!is_null($aMsg))
		{
			$data['error'] = array('valid'=>$aMsg);
			$this->template->content = View::forge('t/test/csv',$data);
			return $this->template;
		}

		$aBase['tbNum'] = $iQN;
		$aBase['tbTotal'] = $iTotal;

		try
		{
			$result = Model_Test::insertTestFromCSV($aBase,$aQuery,$this->aClass['ctID']);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}


		Session::set('SES_T_NOTICE_MSG',__('CSVから小テストの登録が完了しました。'));
		Response::redirect('t/test');
	}


	public function action_querylist($sID = null,$iTqNO = null)
	{
		$aTest = null;
		$aQuery = null;
		$aQQ = null;
		$aImg = null;
		$aChoice = null;
		$aInput = null;
		$aMsg = null;

		$aChk = self::TestChecker($sID,$aTest);
		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}
		if ($aTest['tbPublic'] == 1)
		{
			Session::set('SES_T_ERROR_MSG',__('公開中の小テスト問題を変更することはできません。'));
			Response::redirect('/t/test');
		}
		$result = Model_Test::getTestQuery(array(array('tbID','=',$sID)),null,array('tqSort'=>'asc'));
		if (count($result))
		{
			$aQuery = $result->as_array('tqSort');
		}
		$iTqNO = $aQuery[1]['tqNO'];

		# タイトル
		$sTitle = $aTest['tbTitle'];
		$sTitle .= '｜'.__('満点').':'.__(':num点',array('num'=>$aTest['tbTotal']));
		$sTitle .= ($aTest['tbQualifyScore'])? ' '.__('合格点').':'.__(':num点',array('num'=>$aTest['tbQualifyScore'])):'';
		$this->template->set_global('pagetitle',$sTitle);

		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/test','name'=>__('小テスト'));
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		# カスタムボタン
		$aCustomBtn = array(
			array(
				'url'  => '/t/test',
				'name' => __('問題編集の終了'),
				'show' => 0,
			),
		);
		$this->template->set_global('aCustomBtn',$aCustomBtn);

		$bMod = ($aTest['tpNum'] > 0)? true:false;

		$this->template->content = View::forge('t/test/querylist');
		$this->template->content->set('aTest',$aTest);
		$this->template->content->set('aQuery',$aQuery);
		$this->template->content->set('aQQ',$aQQ);
		$this->template->content->set('aInput',$aInput);
		$this->template->content->set('aChoice',$aChoice);
		$this->template->content->set('aImg',$aImg);
		$this->template->content->set('aMsg',$aMsg);
		$this->template->content->set('bMod',$bMod);
		$this->template->content->set('iTqNO',$iTqNO);
		$this->template->javascript = array('cl.t.test.js');
		return $this->template;
	}

	public function action_queryedit($sID = null)
	{
		$aTest = null;
		$aQuery = null;

		$aChk = self::TestChecker($sID,$aTest);
		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}
		if ($aTest['tbPublic'] == 1)
		{
			Session::set('SES_T_ERROR_MSG',__('公開中の小テスト問題を変更することはできません。'));
			Response::redirect('/t/test');
		}

		# タイトル
		$sTitle = $aTest['tbTitle'];
		$sTitle .= '｜'.__('問題リスト').'｜'.__('満点').'：'.__(':num点',array('num'=>$aTest['tbTotal']));
		$sTitle .= ($aTest['tbQualifyScore'])? '｜'.__('合格点').'：'.__(':num点',array('num'=>$aTest['tbQualifyScore'])):'';
		$this->template->set_global('pagetitle',$sTitle);

		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/test','name'=>__('小テスト'));
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		$bMod = ($aTest['tpNum'] > 0)? true:false;

		$result = Model_Test::getTestQuery(array(array('tbID','=',$sID)),null,array('tqSort'=>'asc'));
		if (count($result))
		{
			$aQuery = $result->as_array();
		}
		if (!Input::post(null,false))
		{
			Response::redirect('/t/test/querylist');
		}

		$bMod = ($aTest['tpNum'] > 0)? true:false;

		$aMsg = null;
		$aQQ = null;
		$aImg = null;
		$aInput = Input::post();
		$aChoice = null;
		$aRight = null;
		$aRightText = null;
		$sTempPath = CL_UPPATH.DS.$aTest['tbID'].DS.$aInput['qSort'].'_tmp';

		if ($aInput['qNo'])
		{
			$result = Model_Test::getTestQuery(array(array('tbID','=',$sID),array('tqNO','=',$aInput['qNo'])));
			if (count($result))
			{
				$aQQ = $result->current();
			}
		}

		if (!is_numeric($aInput['qScore']))
		{
			$aMsg[] = __('配点は数値で入力してください。');
		}
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
						$aMsg[] = __(':nameに登録できるファイルは画像（JPG,JPEG）のみです。',array('name'=>__('問題')));
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
			$aInput['tqImage'] = 'base.'.$qImage['extension'];
			$sThumbImg = CL_Q_SMALL_PREFIX.$aInput['tqImage'];
			$sTempImg = 'base_tmp.'.$qImage['extension'];

			ClFunc_Common::chkDir($sTempPath,true);
			File::rename($qImage['file'], $sTempPath.DS.$sTempImg);
			ClFunc_Common::OrientationFixedImage($sTempPath.DS.$sTempImg);
			$image = Image::load($sTempPath.DS.$sTempImg);
			$image->config('quality',CL_Q_IMG_QUALITY)->resize(CL_Q_IMG_SIZE,(CL_Q_IMG_SIZE / 4 * 3),true)->save($sTempPath.DS.$aInput['tqImage'],0666);
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
						$aMsg[] = __(':nameに登録できるファイルは画像（JPG,JPEG）のみです。',array('name'=>__('解説')));
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
			$aInput['tqExplainImage'] = 'explain.'.$qeImage['extension'];
			$sThumbImg = CL_Q_SMALL_PREFIX.$aInput['tqExplainImage'];
			$sTempImg = 'explain_tmp.'.$qeImage['extension'];

			ClFunc_Common::chkDir($sTempPath,true);
			File::rename($qeImage['file'], $sTempPath.DS.$sTempImg);
			ClFunc_Common::OrientationFixedImage($sTempPath.DS.$sTempImg);
			$image = Image::load($sTempPath.DS.$sTempImg);
			$image->config('quality',CL_Q_IMG_QUALITY)->resize(CL_Q_IMG_SIZE,(CL_Q_IMG_SIZE / 4 * 3),true)->save($sTempPath.DS.$aInput['tqExplainImage'],0666);
			$image->config('quality',CL_Q_SMALL_QUALITY)->resize(CL_Q_SMALL_SIZE,(CL_Q_SMALL_SIZE / 4 * 3),true)->save($sTempPath.DS.$sThumbImg,0666);
			File::delete($sTempPath.DS.$sTempImg);
		}

		if ($bMod)
		{
			$aInput['qType'] = $aQQ['tqStyle'];
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
								$aMsg[] = __(':nameに登録できるファイルは画像（JPG,JPEG）のみです。',array('name'=>__('選択肢').$iCnt));
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
					else if ($aInput["tqChoiceImage".$i] != "")
					{
						$aImg[$iCnt] = $aInput["tqChoiceImage".$i];
					}
					$iCnt++;
				}
				else if ($aQQ['tqChoiceImg'.$i])
				{
					$aDelImg[$i] = $aQQ['tqChoiceImg'.$i];
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
			if ($bMod && count($aChoice) != $aQQ['tqChoiceNum'])
			{
				$aMsg[] = __('解答がある問題の選択肢は増減させることはできません。');
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
			$this->template->content = View::forge('t/test/querylist');
			$this->template->content->set('aTest',$aTest);
			$this->template->content->set('aQuery',$aQuery);
			$this->template->content->set('aQQ',$aQQ);
			$this->template->content->set('aInput',$aInput);
			$this->template->content->set('aChoice',$aChoice);
			$this->template->content->set('aRight',$aRight);
			$this->template->content->set('aRightText',$aRightText);
			$this->template->content->set('aImg',$aImg);
			$this->template->content->set('aMsg',$aMsg);
			$this->template->content->set('bMod',$bMod);
			$this->template->content->set('iTqNO',null);
			$this->template->javascript = array('cl.t.test.js');
			return $this->template;
		}

		if (!is_null($aQQ))
		{
			$aUpdate = array(
				'tqText' => $aInput['qText'],
				'tqScore' => $aInput['qScore'],
				'tqImage' => $aInput['tqImage'],
				'tqStyle' => $aInput['qType'],
				'tqChoiceNum' => ($aInput['qType'] == 2)? 0:count($aChoice),
				'tqExplain' => $aInput['qExplain'],
				'tqExplainImage' => $aInput['tqExplainImage'],
				'tqDate' => date('YmdHis'),
			);
			for ($i = 1; $i <= 50; $i++)
			{
				if (isset($aChoice[$i]))
				{
					$aUpdate['tqChoice'.$i] = $aChoice[$i];
					if (isset($aImg[$i]))
					{
						$aUpdate['tqChoiceImg'.$i] = $aImg[$i];
					}
					else
					{
						$aUpdate['tqChoiceImg'.$i] = '';
					}
				}
				else
				{
					$aUpdate['tqChoice'.$i] = '';
					$aUpdate['tqChoiceImg'.$i] = '';
				}
			}
			if ($aInput['qType'] != 2)
			{
				$aKeys = array_keys($aRight);
				$aUpdate['tqRight1'] = implode('|',$aKeys);
			}
			else
			{
				foreach ($aRightText as $i => $sRight)
				{
					$aUpdate['tqRight'.$i] = $sRight;
				}
			}

			$aWhere = array(
				array('tbID','=',$aQQ['tbID']),
				array('tqNO','=',$aQQ['tqNO']),
			);

			try
			{
				$result = Model_Test::updateTestQuery($aUpdate,$aWhere,null,$aQQ['tbID']);
				$sSavePath = CL_UPPATH.DS.$aTest['tbID'].DS.$aQQ['tqNO'];

				if ($bMod)
				{
					$result = Model_Test::resetScore($aTest['tbID']);
				}
			}
			catch (Exception $e)
			{
				\Clfunc_Common::LogOut($e,__CLASS__);
				Session::set('SES_T_ERROR_MSG',$e->getMessage());
				Response::redirect($this->eRedirect);
			}
			$sSesM = __('問題:noを更新しました。',array('no'=>$aQQ['tqSort']));
			$iTqNO = $aQQ['tqNO'];
		}
		else
		{
			$aInsert = array(
				'tbID' => $aTest['tbID'],
				'tqSort' => $aInput['qSort'],
				'tqText' => $aInput['qText'],
				'tqScore' => $aInput['qScore'],
				'tqImage' => $aInput['tqImage'],
				'tqStyle' => $aInput['qType'],
				'tqChoiceNum' => ($aInput['qType'] == 2)? 0:count($aChoice),
				'tqExplain' => $aInput['qExplain'],
				'tqExplainImage' => $aInput['tqExplainImage'],
				'tqDate' => date('YmdHis'),
			);
			if ($aInput['qType'] != 2)
			{
				foreach ($aChoice as $i => $sChoice)
				{
					$aInsert['tqChoice'.$i] = $sChoice;
					if (isset($aImg[$i]))
					{
						$aInsert['tqChoiceImg'.$i] = $aImg[$i];
					}
				}
				$aKeys = array_keys($aRight);
				$aInsert['tqRight1'] = implode('|',$aKeys);
			}
			else
			{
				foreach ($aRightText as $i => $sRight)
				{
					$aInsert['tqRight'.$i] = $sRight;
				}
			}

			try
			{
				$result = Model_Test::insertTestQuery($aInsert);
				$sSavePath = CL_UPPATH.DS.$aTest['tbID'].DS.$result;

				if ($bMod)
				{
					$result = Model_Test::resetScore($aTest['tbID']);
				}
			}
			catch (Exception $e)
			{
				\Clfunc_Common::LogOut($e,__CLASS__);
				Session::set('SES_T_ERROR_MSG',$e->getMessage());
				Response::redirect($this->eRedirect);
			}
			$sSesM = __('問題:noを追加しました。',array('no'=>$aInsert['tqSort']));
			$iTqNO = $result;
		}

		if (file_exists($sSavePath))
		{
			system('rm -rf '.$sSavePath);
		}
		if ($aInput['tqImage'])
		{
			ClFunc_Common::chkDir($sSavePath,true);
			File::rename($sTempPath.DS.$aInput['tqImage'],$sSavePath.DS.$aInput['tqImage']);
			File::rename($sTempPath.DS.CL_Q_SMALL_PREFIX.$aInput['tqImage'],$sSavePath.DS.CL_Q_SMALL_PREFIX.$aInput['tqImage']);
		}
		if ($aInput['tqExplainImage'])
		{
			ClFunc_Common::chkDir($sSavePath,true);
			File::rename($sTempPath.DS.$aInput['tqExplainImage'],$sSavePath.DS.$aInput['tqExplainImage']);
			File::rename($sTempPath.DS.CL_Q_SMALL_PREFIX.$aInput['tqExplainImage'],$sSavePath.DS.CL_Q_SMALL_PREFIX.$aInput['tqExplainImage']);
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
		Response::redirect('/t/test/querylist/'.$aTest['tbID']);
	}

	public function action_querydelete($sID = null,$iNO = null)
	{
		$sImgPath = CL_UPPATH.DS.$sID.DS.$iNO;
		$aTest = null;
		$aQuery = null;
		$aChk = self::TestChecker($sID,$aTest);
		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}
		$aChk = self::QueryChecker($sID,$iNO,$aQuery);
		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}
		try
		{
			$result = Model_Test::deleteTestQuery($sID,$iNO,$aQuery);
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
		Session::set('SES_T_NOTICE_MSG',__('問題:noを削除しました。',array('no'=>$aQuery['tqSort'])));
		Response::redirect('/t/test/querylist/'.$sID);
	}

	public function action_preview($sID = null)
	{
		$aTest = null;
		$aQuery = null;

		$aChk = self::TestChecker($sID,$aTest);
		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}
		$result = Model_Test::getTestQuery(array(array('tbID','=',$sID)),null,array('tqSort'=>'asc'));
		if (count($result))
		{
			$aQuery = $result->as_array();
		}

		$aTimer[$sID] = time();
		Session::set('SES_S_TEST_TIMER_'.$sID,serialize($aTimer));

		# タイトル
		$sTitle = $aTest['tbTitle'].'｜'.__('小テストプレビュー');
		$this->template->set_global('pagetitle',$sTitle);
		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/test','name'=>__('小テスト'));
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		$this->template->content = View::forge('t/test/preview');
		$this->template->content->set('aTest',$aTest);
		$this->template->content->set('aQuery',$aQuery);
		$this->template->javascript = array('cl.s.test.js','cl.t.test.js');
		return $this->template;
	}

	public function action_delete($sID = null)
	{
		$sImgPath = CL_UPPATH.DS.$sID;
		$aTest = null;
		$aChk = self::TestChecker($sID,$aTest);
		if (is_array($aChk))
		{
			Session::set('SES_S_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}

		try
		{
			$result = Model_Test::deleteTest($sID,$aTest);
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

		Session::set('SES_T_NOTICE_MSG',__('小テストを削除しました。'));
		Response::redirect('/t/test');
	}

	public function action_bent($sID = null)
	{
		$aTest = null;
		$aQuery = null;
		$aChk = self::TestChecker($sID,$aTest);
		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}
		$result = Model_Test::getTestQuery(array(array('tbID','=',$sID)),null,array('tqSort'=>'asc'));
		if (!count($result))
		{
			Session::set('SES_T_ERROR_MSG',__('指定の小テストには問題がありません。'));
			Response::redirect('/t/test');
		}
		$aRes = $result->as_array();
		foreach ($aRes as $aR)
		{
			$aQuery['tq'.$aR['tqNO']] = $aR;
		}

		try
		{
			$result = Model_Test::setTestBent($aTest);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		$aBent = null;
		$result = Model_Test::getTestBent(array(array('tbID','=',$sID)));
		if (count($result))
		{
			$aRes = $result->as_array();
			foreach ($aRes as $aR)
			{
				$aBent['tq'.$aR['tqNO']][$aR['tbNO']] = $aR;
			}
		}
		$aComment = null;

		# タイトル
		$sTitle = $aTest['tbTitle'];

		$view = View::forge('template');
		$view->content = View::forge('t/test/bent');
		$view->content->set('sTitle',$sTitle);
		$view->content->set('aTest',$aTest);
		$view->content->set_safe('aQuery',$aQuery);
		$view->content->set('aBent',$aBent);
		$view->content->set('aComment',$aComment);
		$view->javascript = array('cl.t.test.js','cl.t.kreport.js');
		$view->footer = View::forge('t/footer');
		return $view;
	}

	public function action_put($sID = null)
	{
		$aTest = null;
		$aChk = self::TestChecker($sID,$aTest);
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
			}
		}
		$result = Model_Test::getTestPut(array(array('tp.tbID','=',$sID)));
		if (count($result))
		{
			$aRes = $result->as_array('stID');
			foreach ($aRes as $sStID => $aP)
			{
				if (isset($aStudent[$sStID]))
				{
					$aStudent[$sStID]['put'] = $aP;
				}
			}
		}

		# タイトル
		$sTitle = $aTest['tbTitle'];
		$sTitle .= '｜'.__('提出状況');
		$this->template->set_global('pagetitle',$sTitle);

		# カスタムボタン
		$aCustomBtn = array(
			array(
				'url'  => '/t/test/anslist/'.$sID,
				'name' => __('解答一覧'),
				'show' => 0,
			),
		);
		$this->template->set_global('aCustomBtn',$aCustomBtn);

		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/test','name'=>__('小テスト'));
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		$this->template->content = View::forge('t/test/put');
		$this->template->content->set('aTest',$aTest);
		$this->template->content->set('aStudent',$aStudent);
		$this->template->javascript = array('cl.t.test.js');
		return $this->template;
	}

	public function action_ansdetail($sID = null, $sStID = null)
	{
		$aTest = null;
		$aQuery = null;
		$sName = null;
		$aAns = null;

		$aChk = self::TestChecker($sID,$aTest);
		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}
		$result = Model_Test::getTestQuery(array(array('tbID','=',$sID)),null,array('tqSort'=>'asc'));
		if (!count($result))
		{
			Session::set('SES_T_ERROR_MSG',__('指定の小テストには問題がありません。'));
			Response::redirect('/t/test');
		}
		$aQuery = $result->as_array();
		$result = Model_Student::getStudentFromClass($this->aClass['ctID'],array(array('sp.stID','=',$sStID)));
		if (count($result))
		{
			$aRes = $result->current();
			$sName = $aRes['stName'];
		}
		$result = Model_Test::getTestPut(array(array('tp.tbID','=',$sID),array('tp.stID','=',$sStID)));
		if (!count($result))
		{
			Session::set('SES_S_ERROR_MSG',__('指定の学生は小テスト未解答です。'));
			Response::redirect('/t/test/put/'.$sID);
		}
		$aPut = $result->current();
		if (is_null($sName))
		{
			$sName = $aPut['tpstName'];
		}

		$result = Model_Test::getTestAns(array(array('ta.tbID','=',$sID),array('ta.stID','=',$sStID)));
		if (!count($result))
		{
			Session::set('SES_S_ERROR_MSG',__('指定の学生は小テスト未解答です。'));
			Response::redirect('/t/test/put/'.$sID);
		}
		$aAns = $result->as_array();

		# タイトル
		$sTitle = $aTest['tbTitle'];
		$sTitle .= '｜'.__(':nameさんの解答内容',array('name'=>$sName));
		$this->template->set_global('pagetitle',$sTitle);

		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/test','name'=>__('小テスト'));
		$this->aBread[] = array('link'=>'/test/put/'.$sID,'name'=>__('提出状況'));
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		$this->template->content = View::forge('t/test/ansdetail');
		$this->template->content->set('aTest',$aTest);
		$this->template->content->set('aQuery',$aQuery);
		$this->template->content->set('aAns',$aAns);
		$this->template->content->set('aPut',$aPut);
		$this->template->javascript = array('cl.t.test.js','cl.t.kreport.js');
		return $this->template;
	}


	public function action_anslist($sID = null)
	{
		$aTest = null;
		$aQuery = null;
		$aStudent = null;

		$aChk = self::TestChecker($sID,$aTest);
		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}
		$result = Model_Test::getTestQuery(array(array('tbID','=',$sID)),null,array('tqSort'=>'asc'));
		if (!count($result))
		{
			Session::set('SES_T_ERROR_MSG',__('指定の小テストには問題がありません。'));
			Response::redirect('/t/test');
		}
		$aQuery = $result->as_array();
		$result = Model_Student::getStudentFromClass($this->aClass['ctID'],null,null,array('st.stNO'=>'asc'));
		if (count($result))
		{
			$aRes = $result->as_array('stID');
			foreach ($aRes as $sStID => $aS)
			{
				$aStudent[$sStID]['stu'] = $aS;
			}
		}
		$result = Model_Test::getTestPut(array(array('tp.tbID','=',$sID)));
		if (count($result))
		{
			$aRes = $result->as_array('stID');
			foreach ($aRes as $sStID => $aP)
			{
				$aStudent[$sStID]['put'] = $aP;
			}
		}
		$result = Model_Test::getTestAns(array(array('ta.tbID','=',$sID)));
		if (count($result))
		{
			$aRes = $result->as_array();
			foreach ($aRes as $aA)
			{
				$aStudent[$aA['stID']]['ans'][$aA['tqSort']] = $aA;
			}
		}

		# タイトル
		$sTitle = $aTest['tbTitle'];
		$sTitle .= '｜'.__('解答一覧');
		$this->template->set_global('pagetitle',$sTitle);

		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/test','name'=>__('小テスト'));
		$this->aBread[] = array('link'=>'/test/put/'.$sID,'name'=>__('提出状況'));
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		# カスタムボタン
		$aCustomBtn = array(
			array(
				'url'  => '/t/test/put/'.$sID,
				'name' => __('提出状況'),
				'show' => 0,
			),
		);
		$this->template->set_global('aCustomBtn',$aCustomBtn);

		$this->template->content = View::forge('t/test/anslist');
		$this->template->content->set('aTest',$aTest);
		$this->template->content->set('aQuery',$aQuery);
		$this->template->content->set('aStudent',$aStudent);
		$this->template->javascript = array('cl.t.test.js');
		return $this->template;
	}

	public function action_putlist()
	{
		$aTest = null;
		$aTbIDs = null;
		$result = Model_Test::getTestBaseFromClass($this->aClass['ctID'],null,null,array('tb.tbSort'=>'desc'));
		if (count($result))
		{
			foreach ($result as $aQ)
			{
				$aTest[] = $aQ;
				$aTbIDs[] = $aQ['tbID'];
			}
		}

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
		$aStIDs = null;
		$result = Model_Student::getStudentFromClass($this->aClass['ctID'],null,null,array('st.stNO'=>'asc'),$aWords);
		if (count($result))
		{
			$aRes = $result->as_array('stID');
			foreach ($aRes as $sStID => $aS)
			{
				$aStudent[$sStID]['stu'] = $aS;
				$aStIDs[] = $sStID;
			}
		}

		$aWhere = null;
		if (!is_null($aTbIDs))
		{
			$aWhere[] = array('tp.tbID','IN',$aTbIDs);
		}
		if (!is_null($aStIDs))
		{
			$aWhere[] = array('tp.stID','IN',$aStIDs);
		}

		$result = Model_Test::getTestPut($aWhere);
		if (count($result))
		{
			foreach ($result as $aP)
			{
				$sStID = $aP['stID'];
				$sTbID = $aP['tbID'];
				if (isset($aStudent[$sStID]))
				{
					$aStudent[$sStID]['put'][$sTbID] = $aP;
				}
			}
		}

		# タイトル
		$sTitle = __('提出一覧');
		$this->template->set_global('pagetitle',$sTitle);
		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/test','name'=>__('小テスト'));
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		# カスタムボタン
		$aCustomBtn = array(
			array(
				'url'  => '/t/output/testputlist.csv',
				'name' => __('提出一覧CSVのダウンロード'),
				'show' => 1,
				'icon' => 'fa-download',
			),
		);
		$this->template->set_global('aCustomBtn',$aCustomBtn);

		$aSearchForm = array(
			'url' => DS.$this->bn.'/putlist',
		);
		$this->template->set_global('aSearchForm',$aSearchForm);
		$this->template->set_global('sWords',$sWords);

		$this->template->content = View::forge($this->bn.DS.'putlist');
		$this->template->content->set('aTest',$aTest);
		$this->template->content->set('aStudent',$aStudent);
		$this->template->javascript = array('cl.t.test.js');
		return $this->template;
	}

	public function action_stuanslist($sStID = null)
	{
		$iQNum = 0;
		$aTest = null;
		$aTbIDs = null;
		$result = Model_Test::getTestBaseFromClass($this->aClass['ctID'],null,null,array('tb.tbSort'=>'desc'));
		if (count($result))
		{
			foreach ($result as $aQ)
			{
				$aTest[] = $aQ;
				$aTbIDs[] = $aQ['tbID'];
				if ($aQ['tbNum'] > $iQNum)
				{
					$iQNum = $aQ['tbNum'];
				}
			}
		}
		$aStudent = null;
		$result = Model_Student::getStudentFromClass($this->aClass['ctID'],array(array('sp.stID','=',$sStID)));
		if (count($result))
		{
			$aStudent = $result->current();
		}

		$aPut = null;

		$aPWhere = array(array('tp.stID','=',$sStID));
		$aQWhere = null;
		$aAWhere = array(array('ta.stID','=',$sStID));
		if (!is_null($aTbIDs))
		{
			$aPWhere[] = array('tp.tbID','IN',$aTbIDs);
			$aQWhere[] = array('tbID','IN',$aTbIDs);
			$aAWhere[] = array('ta.tbID','IN',$aTbIDs);
		}
		$result = Model_Test::getTestPut($aPWhere);
		if (count($result))
		{
			foreach ($result as $aP)
			{
				$aPut[$aP['tbID']] = $aP;
			}
		}

		$aQuery = null;
		$result = Model_Test::getTestQuery($aQWhere);
		if (count($result))
		{
			foreach ($result as $aQ)
			{
				$aQuery[$aQ['tbID']][$aQ['tqSort']] = $aQ;
			}
		}

		$aAns = null;
		$result = Model_Test::getTestAns($aAWhere);
		if (count($result))
		{
			foreach ($result as $aA)
			{
				$aAns[$aA['tbID']][$aA['tqSort']] = $aA;
			}
		}

		# タイトル
		$sTitle = __('解答内容一覧').' ['.$aStudent['stNO'].']'.$aStudent['stName'];
		$this->template->set_global('pagetitle',$sTitle);
		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/test','name'=>__('小テスト'));
		$this->aBread[] = array('link'=>'/test/putlist','name'=>__('提出一覧'));
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		$this->template->content = View::forge($this->bn.DS.'stuanslist');
		$this->template->content->set('aTest',$aTest);
		$this->template->content->set('aPut',$aPut);
		$this->template->content->set('aQuery',$aQuery);
		$this->template->content->set('aAns',$aAns);
		$this->template->content->set('iQNum',$iQNum);
		$this->template->content->set('aStudent',$aStudent);
		$this->template->javascript = array('cl.t.test.js');
		return $this->template;
	}

	public function action_archive()
	{
		if (!$this->aClass['scNum'])
		{
			Session::set('SES_T_ERROR_MSG',__('履修学生がいないため、アーカイブ作成はできません。'));
		}
		$result = Model_Test::getTestBaseFromClass($this->aClass['ctID'],null,null,array('tb.tbSort'=>'desc'));
		if (!count($result))
		{
			Session::set('SES_T_ERROR_MSG',__('小テストが1件もないため、アーカイブ作成はできません。'));
		}
		else
		{
			shell_exec('/usr/bin/php '.CL_OILPATH.' r exectestansarchive '.$this->aClass['ctID'].' '.$this->aTeacher['ttID'].' '.$this->sLang.' > /dev/null 2>&1 &');
			Session::set('SES_T_NOTICE_MSG',__('アーカイブ作成を開始しました。\nアーカイブ作成には時間がかかる場合があります。\n作成が完了すると、この画面上にダウンロードボタンが表示されます。'));
		}
		Response::redirect(DS.$this->bn);
	}

	public function action_dqselect($sID = null)
	{
		$aTest = null;
		$aQuery = null;

		$aChk = self::TestChecker($sID,$aTest);
		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}
		$result = Model_Test::getTestQuery(array(array('tbID','=',$sID)),null,array('tqSort'=>'asc'));
		if (count($result))
		{
			$aQuery = $result->as_array('tqSort');
		}
		else
		{
			Session::set('SES_T_ERROR_MSG',__('指定の小テストには問題がありません。'));
			Response::redirect('/t/test');
		}

		if ($aInput = Input::post(null,false))
		{
			if (!isset($aInput['QueryChk']))
			{
				Session::set('SES_T_ERROR_MSG',__('問題が選択されていません。'));
				Response::redirect(DS.$this->bn.DS.'dqselect'.DS.$sID);
			}

			Session::set('SES_T_TEST2DRILL_'.$sID, serialize($aInput['QueryChk']));

			Response::redirect(DS.$this->bn.DS.'dbselect'.DS.$sID);
		}

		$aInput['QueryChk'] = array();
		if ($sSel = Session::get('SES_T_TEST2DRILL_'.$sID,false))
		{
			$aInput['QueryChk'] = unserialize($sSel);
		}

		# タイトル
		$sTitle = __('問題をドリルにコピー').'｜'.$aTest['tbTitle'];
		$this->template->set_global('pagetitle',$sTitle);

		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/test','name'=>__('小テスト'));
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		$this->template->content = View::forge('t/test/dqselect');
		$this->template->content->set('aTest',$aTest);
		$this->template->content->set('aQuery',$aQuery);
		$this->template->content->set('aQChk',$aInput['QueryChk']);
		$this->template->javascript = array('cl.t.test.js');
		return $this->template;
	}

	public function action_dbselect($sID = null)
	{
		$aTest = null;
		$aQuery = null;
		$aInput = $this->aDrillBase;
		$aInput['d_category'] = null;
		$aInput['d_select'] = null;

		$aChk = self::TestChecker($sID,$aTest);
		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}
		$result = Model_Test::getTestQuery(array(array('tbID','=',$sID)),null,array('tqSort'=>'asc'));
		if (count($result))
		{
			$aQuery = $result->as_array('tqNO');
		}
		else
		{
			Session::set('SES_T_ERROR_MSG',__('指定の小テストには問題がありません。'));
			Response::redirect(DS.$this->bn);
		}

		if (!$sSel = Session::get('SES_T_TEST2DRILL_'.$sID,false))
		{
			Session::set('SES_T_ERROR_MSG',__('問題が選択されていません。'));
			Response::redirect(DS.$this->bn.DS.'dqselect'.DS.$sID);
		}

		$aDrillCate = null;
		$result = Model_Drill::getDrillCategoryFromClass($this->aClass['ctID'],null,null,array('dcSort'=>'desc'));
		if (count($result))
		{
			$aDrillCate = $result->as_array();
		}
		else
		{
			// 登録データ生成
			$aInsert = array(
				'ctID'   => $this->aClass['ctID'],
				'dcName' => __('ドリル'),
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
			$result = Model_Drill::getDrillCategoryFromClass($this->aClass['ctID'],null,null,array('dcSort'=>'desc'));
			$aDrillCate = $result->as_array();
		}

		$aDrill = null;
		$result = Model_Drill::getDrill(array(array('dcID','=',$aDrillCate[0]['dcID'])),null,array('dbSort'=>'desc'));
		if (count($result))
		{
			$aDrill = $result->as_array();
		}

		# タイトル
		$sTitle = __('問題をドリルにコピー').'｜'.$aTest['tbTitle'];
		$this->template->set_global('pagetitle',$sTitle);

		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/test','name'=>__('小テスト'));
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		if (Input::post(null,false))
		{
			$aInput = Input::post(null,false);
			$sDcID = $aInput['d_category'];

			if (isset($aInput['back']))
			{
				Response::redirect(DS.$this->bn.DS.'dqselect'.DS.$sID);
			}

			$aDrill = null;
			$result = Model_Drill::getDrill(array(array('dcID','=',$sDcID)),null,array('dbSort'=>'desc'));
			if (count($result))
			{
				$aDrill = $result->as_array();
			}

			if ($aInput['d_select'] == '0')
			{
				Session::set('SES_T_ERROR_MSG',__('ドリルを選択してください。'));
				Response::redirect(DS.$this->bn.DS.'dbselect'.DS.$sID);
			}

			if ($aInput['d_select'] == 'new')
			{
				$val = Validation::forge();
				$val->add_callable('Helper_CustomValidation');
				$val->add_field('d_title', __('タイトル'), 'required|max_length['.CL_TITLE_LENGTH.']');
				$val->add_field('d_pubnum', __('出題数'), 'required|numeric|numeric_min[1]|numeric_max[100]');

				if (!$val->run())
				{
					$aInput['error'] = $val->error();
					$aInput = array_merge($this->aDrillBase,$aInput);
					$this->template->content = View::forge('t/test/dbselect');
					$this->template->content->set('aTest',$aTest);
					$this->template->content->set('aQuery',$aQuery);
					$this->template->content->set('aDrillCate',$aDrillCate);
					$this->template->content->set('aDrill',$aDrill);
					$this->template->content->set('aInput',$aInput);
					$this->template->javascript = array('cl.t.test.js');
					return $this->template;
				}

				try
				{
					$aInsert = array(
						'dcID' => $sDcID,
						'dbTitle' => $aInput['d_title'],
						'dbPublicNum' => (int)$aInput['d_pubnum'],
						'dbRand'  => (int)$aInput['d_rand'],
						'dbQueryStyle' => (int)$aInput['d_select_style'],
						'dbQueryRand' => (int)$aInput['d_query_rand'],
						'dbDate'  => date('YmdHis'),
					);
					$iDbNO = \Model_Drill::insertDrill($aInsert);
					$aDrill = array('dbTitle'=>$aInput['d_title']);
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
				$iDbNO = $aInput['d_select'];
				$result = Model_Drill::getDrill(array(array('dcID','=',$sDcID),array('dbNO','=',$iDbNO)));
				if (!count($result))
				{
					Session::set('SES_T_ERROR_MSG',__('指定されたドリルが見つかりません。'));
					Response::redirect(DS.$this->bn.DS.'dbselect'.DS.$sID);
				}
				$aDrill = $result->current();
			}

			$iSort = 1;
			$result = Model_Drill::getDrillQuery(array(array('dcID','=',$sDcID),array('dbNO','=',$iDbNO)),null,array('dqSort'=>'desc'));
			if (count($result))
			{
				$res = $result->as_array();
				$iSort = (int)$res[0]['dqSort'] + 1;
			}

			$aSel = unserialize($sSel);

			$aInsert = null;
			foreach ($aSel as $iTqNO)
			{
				$aQ = $aQuery[$iTqNO];

				$aInsert[$iTqNO] = array(
					'dcID' => $sDcID,
					'dbNO' => $iDbNO,
					'dgNO' => 0,
					'dqSort' => $iSort,
					'dqText' => $aQ['tqText'],
					'dqImage' => $aQ['tqImage'],
					'dqStyle' => $aQ['tqStyle'],
					'dqChoiceNum' => (int)$aQ['tqChoiceNum'],
					'dqExplain' => $aQ['tqExplain'],
					'dqExplainImage' => $aQ['tqExplainImage'],
					'dqDate' => date('YmdHis'),
				);
				for ($i = 1; $i <= 5; $i++)
				{
					$aInsert[$iTqNO]['dqRight'.$i] = $aQ['tqRight'.$i];
				}
				for ($i = 1; $i <= 50; $i++)
				{
					$aInsert[$iTqNO]['dqChoice'.$i] = $aQ['tqChoice'.$i];
					$aInsert[$iTqNO]['dqChoiceImg'.$i] = $aQ['tqChoiceImg'.$i];
				}
				$iSort++;
			}

			try
			{
				$result = Model_Drill::insertDrillQueries($aInsert);
			}
			catch (Exception $e)
			{
				\Clfunc_Common::LogOut($e,__CLASS__);
				Session::set('SES_T_ERROR_MSG',$e->getMessage());
				Response::redirect($this->eRedirect);
			}

			foreach ($result as $iTqNO => $iDqNO)
			{
				$sSourcePath = CL_UPPATH.DS.$sID.DS.$iTqNO;
				$sSavePath = CL_UPPATH.DS.$sDcID.DS.$iDbNO.DS.$iDqNO;

				if (file_exists($sSourcePath))
				{
					ClFunc_Common::chkDir($sSavePath,true);
					system('cp -rfp '.$sSourcePath.'/* '.$sSavePath.'/');
				}
			}

			Session::delete('SES_T_TEST2DRILL_'.$sID);
			Session::set('SES_T_NOTICE_MSG',__('小テスト「:test」の問題をドリル「:drill」にコピーしました。（:num件）',array('test'=>$aTest['tbTitle'],'drill'=>$aDrill['dbTitle'],'num'=>count($aSel))));
			Response::redirect(DS.$this->bn);
		}

		$this->template->content = View::forge('t/test/dbselect');
		$this->template->content->set('aTest',$aTest);
		$this->template->content->set('aQuery',$aQuery);
		$this->template->content->set('aDrillCate',$aDrillCate);
		$this->template->content->set('aDrill',$aDrill);
		$this->template->content->set('aInput',$aInput);
		$this->template->javascript = array('cl.t.test.js');
		return $this->template;
	}

	private function TestChecker($sTbID = null,&$aTest = null)
	{
		if (is_null($this->aClass))
		{
			return array('msg'=>__('講義情報が確認できませんでした。'),'url'=>'/t/index');
		}
		if (is_null($sTbID))
		{
			return array('msg'=>__('小テスト情報が送信されていません。'),'url'=>'/t/test');
		}
		$result = Model_Test::getTestBaseFromID($sTbID);
		if (!count($result))
		{
			return array('msg'=>__('指定された小テストが見つかりません。'),'url'=>'/t/test');
		}
		$aTest = $result->current();

		return true;
	}

	private function QueryChecker($sTbID = null, $iTqNO = null, &$aQuery = null)
	{
		if (is_null($this->aClass))
		{
			return array('msg'=>__('講義情報が確認できませんでした。'),'url'=>'/t/index');
		}
		if (is_null($sTbID) || is_null($iTqNO))
		{
			return array('msg'=>__('必要な情報が送信されていません。'),'url'=>'/t/test');
		}
		$result = Model_Test::getTestQuery(array(array('tbID','=',$sTbID),array('tqNO','=',$iTqNO)));
		if (!count($result))
		{
			return array('msg'=>__('指定された小テスト問題が見つかりません。'),'url'=>'/t/test');
		}
		$aQuery = $result->current();

		return true;
	}













}