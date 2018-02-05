<?php
class Controller_S_Alog extends Controller_S_Baseclass
{
	private $baseName = 'alog';
	private $aALTheme = null;
	private $aALGoal = null;
	private $aALog = null;
	private $aALGoalBase = array(
		'ag_text'=>null,
	);
	private $aALBase = array(
		'al_title'=>null,
		'al_date_s'=>null,
		'al_date_e'=>null,
		'al_time_s'=>null,
		'al_time_e'=>null,
		'al_opt1'=>null,
		'al_opt2'=>null,
		'al_text'=>null,
		'al_file' =>null,
		'fileinfo' =>array(
			'file'=>'',
			'name'=>'',
			'size'=>0,
			'isimg'=>false,
		),
	);
	private $aSearchCol = array(
		'al.alTitle','al.alOpt1','al.alOpt2','al.alText','ft.fName','al.alCom'
	);

	public function action_index()
	{
		$aALTheme = null;
		$aALTIDs = null;
		$aALogList = null;

		$result = Model_Alog::getAlogThemeFromClass($this->aClass['ctID'],array(array('altPublic','=',1)),null,array('altSort'=>'desc'));
		if (count($result))
		{
			$aALTheme = $result->as_array('altID');
			$aALTIDs = array_keys($aALTheme);
		}

		$aY = array(
			date('Y-m-d',strtotime('-1 month')),
			date('Y-m-d'),
		);

		$aActive = null;
		$sAlt = null;
		$aWhere = array(
			array('al.stID','=',$this->aStudent['stID']),
		);

		if (Input::post(null,false))
		{
			$aReq = Input::post();
			if ($aReq['sd'] <= $aReq['ed'])
			{
				$aY[0] = $aReq['sd'];
				$aY[1] = $aReq['ed'];
			} else {
				$aY[0] = $aReq['ed'];
				$aY[1] = $aReq['sd'];
			}
			$sAlt = $aReq['alt'];
			if ($sAlt)
			{
				$aWhere[] = array('al.altID','=',$sAlt);
				$aActive = $aALTheme[$sAlt];
				Session::set('SES_S_ALOG_ALTID',$sAlt);
			}
			else
			{
				if (!is_null($aALTIDs))
				{
					$aWhere[] = array('al.altID','IN',$aALTIDs);
				}
			}
		}
		else
		{
			if (!is_null($aALTIDs))
			{
				$sAlt = Session::get('SES_S_ALOG_ALTID',false);
				if ($sAlt && isset($aALTheme[$sAlt]))
				{
					$aWhere[] = array('al.altID','=',$sAlt);
					$aActive = $aALTheme[$sAlt];
					Session::set('SES_S_ALOG_ALTID',$sAlt);
				} else {
					$sAlt = $aALTIDs[0];
					$aWhere[] = array('al.altID','=',$sAlt);
					$aActive = $aALTheme[$sAlt];
					Session::set('SES_S_ALOG_ALTID',$sAlt);
				}
			}
		}
		$iSC = strtotime($aY[0]);
		$iEC = strtotime($aY[1]);
		$aWhere[] = array('al.alDate','between',array(date('Y-m-d 00:00:00',$iSC),date('Y-m-d 23:59:59',$iEC)));

		$result = Model_Alog::getAlog($aWhere,null,array('al.no'=>'desc'));
		if (count($result))
		{
			foreach ($result as $aC)
			{
				$aALogList[ClFunc_Tz::tz('Y-m-d',$this->tz,$aC['alDate'])][$aC['altID']][$aC['no']] = $aC;
			}
		}

		# タイトル
		$sTitle = __('活動履歴');
		$this->template->set_global('pagetitle',$sTitle);
		# パンくずリスト生成
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		# カスタムボタン
		$aCustomBtn = array(
			array(
				'url'  => '/s/alog/fulltext',
				'name' => __('記録内容一覧'),
				'show' => 1,
			),
		);
		$this->template->set_global('aCustomBtn',$aCustomBtn);

		$this->template->content = View::forge($this->vDir.DS.$this->baseName.'/index');
		$this->template->content->set('aY',$aY);
		$this->template->content->set('sAlt',$sAlt);
		$this->template->content->set('aActive',$aActive);
		$this->template->content->set('aALTheme',$aALTheme);
		$this->template->content->set('aALogList',$aALogList);
		$this->template->javascript = array('cl.s.'.$this->baseName.'.js');
		return $this->template;
	}

	public function action_fulltext()
	{
		$aALTheme = null;
		$aALTIDs = null;
		$aALogList = null;
		$aWords = null;
		$sWords = null;
		$sAlt = null;
		$bPost = false;

		$result = Model_Alog::getAlogThemeFromClass($this->aClass['ctID'],array(array('altPublic','=',1)),null,array('altSort'=>'desc'));
		if (count($result))
		{
			$aALTheme = $result->as_array('altID');
			$aALTIDs = array_keys($aALTheme);
		}

		$aY = array(
				date('Y-m-d',strtotime('-1 month')),
				date('Y-m-d'),
		);

		$aWhere = array(array('al.stID','=',$this->aStudent['stID']));
		if (Input::post(null,false))
		{
			$aReq = Input::post();
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

			$sW = Input::post('w',false);
			if ($sW)
			{
				$aW = \Clfunc_Common::getSearchWords($sW);
				$aWords = \Clfunc_Common::getSearchWhere($aW,$this->aSearchCol);
				$sWords = implode(' ', $aW);
			}

			$sAlt = Input::post('alt',false);
			if ($sAlt)
			{
				$aWhere[] = array('al.altID','=',$sAlt);
				Session::set('SES_S_ALOG_ALTID',$sAlt);
			}
			else
			{
				if (!is_null($aALTIDs))
				{
					$aWhere[] = array('al.altID','IN',$aALTIDs);
				}
			}

			$result = Model_Alog::getAlog($aWhere,null,array('al.no'=>'desc'),$aWords);
			if (count($result))
			{
				$aALogList = $result->as_array();
			}
			$bPost = true;
		}

		# タイトル
		$sTitle = __('記録内容一覧');
		$this->template->set_global('pagetitle',$sTitle);
		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/'.$this->baseName, 'name'=>__('活動履歴'));
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		$this->template->content = View::forge($this->vDir.DS.$this->baseName.'/fulltext');
		$this->template->content->set('aY',$aY);
		$this->template->content->set('sWords',$sWords);
		$this->template->content->set('sAlt',$sAlt);
		$this->template->content->set('aALTheme',$aALTheme);
		$this->template->content->set('aALogList',$aALogList);
		$this->template->content->set('bPost',$bPost);
		$this->template->javascript = array('cl.s.'.$this->baseName.'.js');
		return $this->template;
	}

	public function action_create($sAltID = null)
	{
		$view = $this->vDir.DS.$this->baseName.'/edit';

		$aChk = self::ALogThemeChecker($sAltID);
		if (is_array($aChk))
		{
			Session::set('SES_S_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}
		$aChk = self::ALogGoalChecker($sAltID,false);
		if (is_array($aChk))
		{
			Session::set('SES_S_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}

		# タイトル
		$sTitle = __('記録の新規追加').'｜'.$this->aALTheme['altName'];
		$this->template->set_global('pagetitle',$sTitle);

		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/'.$this->baseName, 'name'=>__('活動履歴'));
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		$this->template->set_global('aALTheme',$this->aALTheme);
		$this->template->set_global('aALGoal',$this->aALGoal);
		$this->template->set_global('aALog',null);

		if (!Input::post(null,false))
		{
			$data = $this->aALBase;
			$data['error'] = null;
			if ($aInput = unserialize(Session::get('SES_S_ALOG_'.$sAltID,false)))
			{
				$data = array_merge($data,$aInput);
			}
			else
			{
				$data['al_date_s'] = ClFunc_Tz::tz('Y/m/d',$this->tz);
				$data['al_date_e'] = ClFunc_Tz::tz('Y/m/d',$this->tz);
				$data['al_time_s'] = ClFunc_Tz::tz('H:i',$this->tz);
				$data['al_time_e'] = ClFunc_Tz::tz('H:i',$this->tz);
			}
			$this->template->content = View::forge($view,$data);
			$this->template->javascript = array('jquery.timepicker.js','cl.s.'.$this->baseName.'.js');
			return $this->template;
		}

		$aInput = Input::post();

		if ($this->aALTheme['altFile'])
		{
			if (isset($aInput['al_file']) && $aInput['al_file'] != '')
			{
				$aInput['fileinfo'] = unserialize($aInput['al_file']);
			}
		}

		$val = Validation::forge();
		$val->add_callable('Helper_CustomValidation');
		$val->add_field('al_text', $this->aALTheme['altTextLabel'], 'required');

		if ($this->aALTheme['altRange'])
		{
			$val->add_field('al_time_s', $this->aALTheme['altRangeLabel'].' '.__('開始日時'), 'required|time')
				->add_rule('max_time',$aInput['al_date_e'].' '.$aInput['al_time_e'],$aInput['al_date_s']);
			$val->add_field('al_time_e', $this->aALTheme['altRangeLabel'].' '.__('終了日時'), 'required|time')
				->add_rule('max_time',ClFunc_Tz::tz('Y/m/d',$this->tz).' 23:59:00',$aInput['al_date_e']);
		}

		if (!$val->run())
		{
			$data = $this->aALBase;
			$data['error'] = $val->error();
			$data = array_merge($data,$aInput);
			$this->template->content = View::forge($view,$data);
			$this->template->javascript = array('jquery.timepicker.js','cl.s.'.$this->baseName.'.js');
			return $this->template;
		}

		Session::set('SES_S_ALOG_'.$sAltID,serialize($aInput));
		Response::redirect('/s/'.$this->baseName.'/check/'.$sAltID.$this->sesParam);
	}

	public function action_edit($sAltID = null,$iNO = null)
	{
		$view = $this->vDir.DS.$this->baseName.'/edit';

		$aChk = self::ALogThemeChecker($sAltID);
		if (is_array($aChk))
		{
			Session::set('SES_S_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}
		$aChk = self::ALogGoalChecker($sAltID,false);
		if (is_array($aChk))
		{
			Session::set('SES_S_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}
		$aChk = self::ALogChecker($sAltID,$iNO);
		if (is_array($aChk))
		{
			Session::set('SES_S_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}

		# タイトル
		$sTitle = __('記録の編集').'｜'.$this->aALTheme['altName'];
		$this->template->set_global('pagetitle',$sTitle);

		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/'.$this->baseName, 'name'=>__('活動履歴'));
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		$this->template->set_global('aALTheme',$this->aALTheme);
		$this->template->set_global('aALGoal',$this->aALGoal);
		$this->template->set_global('aALog',$this->aALog);

		if (!Input::post(null,false))
		{
			$data = $this->aALBase;
			$data['error'] = null;
			if (!$aInput = unserialize(Session::get('SES_S_ALOG_'.$sAltID,false)))
			{
				$aMFile = false;
				if ($this->aALog['fID'])
				{
					try
					{
						$sFileName = $this->aALog['fID'].'.'.$this->aALog['fExt'];
						$sTempPath = $this->sTempFilePath.DS.'_alog_'.$sFileName;
						$result = \Clfunc_Aws::getFile($this->aALog['fPath'],$sFileName,$sTempPath);
						$aMFile = array(
							'file'  => '_alog_'.$sFileName,
							'name'  => $this->aALog['fName'],
							'size'  => $this->aALog['fSize'],
							'isimg' => ($this->aALog['fFileType'] == 1)? 1:0,
						);
					}
					catch (Exception $e)
					{
						$aMFile = false;
					}
				}

				$aInput = array(
					'al_title'  => $this->aALog['alTitle'],
					'al_date_s' => ClFunc_Tz::tz('Y/m/d',$this->tz,$this->aALog['alStart']),
					'al_time_s' => ClFunc_Tz::tz('H:i',$this->tz,$this->aALog['alStart']),
					'al_date_e' => ClFunc_Tz::tz('Y/m/d',$this->tz,$this->aALog['alEnd']),
					'al_time_e' => ClFunc_Tz::tz('H:i',$this->tz,$this->aALog['alEnd']),
					'al_text'   => $this->aALog['alText'],
					'al_opt1'   => $this->aALog['alOpt1'],
					'al_opt2'   => $this->aALog['alOpt2'],
				);
				if ($aMFile)
				{
					$aInput['al_file'] = serialize($aMFile);
					$aInput['fileinfo'] = $aMFile;
				}
			}
			$data = array_merge($data,$aInput);
			$this->template->content = View::forge($view,$data);
			$this->template->javascript = array('jquery.timepicker.js','cl.s.'.$this->baseName.'.js');
			return $this->template;
		}

		$aInput = Input::post();

		if ($this->aALTheme['altFile'])
		{
			if (isset($aInput['al_file']) && $aInput['al_file'] != '')
			{
				$aInput['fileinfo'] = unserialize($aInput['al_file']);
			}
		}

		$val = Validation::forge();
		$val->add_callable('Helper_CustomValidation');
		$val->add_field('al_text', $this->aALTheme['altTextLabel'], 'required');

		if ($this->aALTheme['altRange'])
		{
			$val->add_field('al_time_s', $this->aALTheme['altRangeLabel'].' '.__('開始日時'), 'required|time')
				->add_rule('max_time',$aInput['al_date_e'].' '.$aInput['al_time_e'],$aInput['al_date_s']);
			$val->add_field('al_time_e', $this->aALTheme['altRangeLabel'].' '.__('終了日時'), 'required|time')
				->add_rule('max_time',ClFunc_Tz::tz('Y/m/d',$this->tz).' 23:59:59',$aInput['al_date_e']);
		}

		if (!$val->run())
		{
			$data = $this->aALBase;
			$data['error'] = $val->error();
			$data = array_merge($data,$aInput);
			$this->template->content = View::forge($view,$data);
			$this->template->javascript = array('jquery.timepicker.js','cl.s.'.$this->baseName.'.js');
			return $this->template;
		}

		Session::set('SES_S_ALOG_'.$sAltID,serialize($aInput));
		Response::redirect('/s/'.$this->baseName.'/check/'.$sAltID.DS.$iNO.$this->sesParam);
	}

	public function action_check($sAltID = null, $iNO = 0)
	{
		$aChk = self::ALogThemeChecker($sAltID);
		if (is_array($aChk))
		{
			Session::set('SES_S_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}
		$aChk = self::ALogGoalChecker($sAltID,false);
		if (is_array($aChk))
		{
			Session::set('SES_S_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}
		$sMode = 'create';
		$sMTitle = '新規追加';

		if ($iNO)
		{
			$sMode = 'edit';
			$sMTitle = '編集';

			$aChk = self::ALogChecker($sAltID,$iNO);
			if (is_array($aChk))
			{
				Session::set('SES_S_ERROR_MSG',$aChk['msg']);
				Response::redirect($aChk['url']);
			}
		}

		$sBack = '/s/'.$this->baseName.DS.$sMode.DS.$sAltID.(($iNO)? DS.$iNO:'').$this->sesParam;

		$aInput = $this->aALBase;
		$aSes = unserialize(Session::get('SES_S_ALOG_'.$sAltID,false));
		if (!$aSes)
		{
			Session::set('SES_S_ERROR_MSG',__('登録内容が取得できませんでした。再度入力してください。'));
			Response::redirect($sBack);
		}
		$aInput = array_merge($aInput,$aSes);

		# タイトル
		$sTitle = __('記録の'.$sMTitle).'｜'.$this->aALTheme['altName'];
		$this->template->set_global('pagetitle',$sTitle);

		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/'.$this->baseName, 'name'=>__('活動履歴'));
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		$this->template->set_global('aALTheme',$this->aALTheme);
		$this->template->set_global('aALGoal',$this->aALGoal);
		$this->template->set_global('aALog',$this->aALog);
		$this->template->set_global('iNO',$iNO);

		if (!Input::post(null,false))
		{
			$data = $this->aALBase;
			$data['error'] = null;
			$data = array_merge($data,$aInput);
			$this->template->content = View::forge($this->vDir.DS.$this->baseName.'/check',$data);
			$this->template->javascript = array('cl.t.'.$this->baseName.'.js');
			return $this->template;
		}

		$aPost = Input::post(null,false);
		if (isset($aPost['back']))
		{
			Response::redirect($sBack);
		}

		$sfID = null;
		$ifSize = 0;
		if ($aInput['fileinfo']['file'] != '')
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
				'fUserType'    => 1,
				'fUser'        => $this->aStudent['stID'],
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
				Session::set('SES_S_ERROR_MSG',__('指定したファイルが保存できませんでした。').$e->getMessage());
				Response::redirect($sBack);
			}
		}

		try
		{
			if ($iNO)
			{
				$aUpdate = array(
					'alTitle' => $aInput['al_title'],
					'alStart' => null,
					'alEnd'   => null,
					'alOpt1'  => $aInput['al_opt1'],
					'alOpt2'  => $aInput['al_opt2'],
					'alText'  => $aInput['al_text'],
					'fID'     => $sfID,
				);
				if ($this->aALTheme['altRange'])
				{
					$aUpdate['alStart'] = ClFunc_Tz::tz('Y-m-d H:i:s',null,$aInput['al_date_s'].' '.$aInput['al_time_s'].':00',$this->tz);
					$aUpdate['alEnd']   = ClFunc_Tz::tz('Y-m-d H:i:s',null,$aInput['al_date_e'].' '.$aInput['al_time_e'].':00',$this->tz);
				}

				$aWhere = array(
					array('no','=',$iNO),
					array('altID','=',$sAltID),
				);
				$result = \Model_Alog::updateAlog($aUpdate,$aWhere);
			}
			else
			{
				$aInsert = array(
					'altID'   => $sAltID,
					'stID'    => $this->aStudent['stID'],
					'alTitle' => $aInput['al_title'],
					'alStart' => null,
					'alEnd'   => null,
					'alOpt1'  => $aInput['al_opt1'],
					'alOpt2'  => $aInput['al_opt2'],
					'alText'  => $aInput['al_text'],
					'fID'     => $sfID,
					'alDate'  => date('YmdHis'),
				);
				if ($this->aALTheme['altRange'])
				{
					$aInsert['alStart'] = ClFunc_Tz::tz('Y-m-d H:i:s',null,$aInput['al_date_s'].' '.$aInput['al_time_s'].':00',$this->tz);
					$aInsert['alEnd']   = ClFunc_Tz::tz('Y-m-d H:i:s',null,$aInput['al_date_e'].' '.$aInput['al_time_e'].':00',$this->tz);
				}
				$result = \Model_Alog::insertAlog($aInsert);
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
			Session::set('SES_S_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		if ($sfID)
		{
			@unlink($sSourseFile);
			@unlink($sThumbFile);
		}
		if ($iNO && $this->aALog['fID'])
		{
			\Clfunc_Aws::deleteFile($this->aALog['fPath'],$this->aALog['fID'].'.'.$this->aALog['fExt']);
			if ($this->aALog['fFileType'] == 1)
			{
				\Clfunc_Aws::deleteFile($this->sAwsSavePath, CL_PREFIX_THUMBNAIL.$this->aALog['fID'].'.'.$this->aALog['fExt']);
			}
			if ($this->aALog['fFileType'] == 2)
			{
				\Clfunc_Aws::deleteFile($this->sAwsSavePath,CL_PREFIX_ENCODE.$this->aALog['fID'].CL_AWS_ENCEXT);
				\Clfunc_Aws::deleteFile($this->sAwsSavePath,CL_PREFIX_THUMBNAIL.$this->aALog['fID'].'-00001.png');
			}
			\Model_File::deleteFile($this->aALog['fID']);
		}

		Session::delete('SES_S_ALOG_'.$sAltID);
		Session::set('SES_S_NOTICE_MSG',__('記録を'.(($iNO)? '更新':'登録').'しました。'));
		Response::redirect('/s/'.$this->baseName);
	}

	public function action_delete($sID = null, $iNO = null)
	{
		$aChk = self::ALogThemeChecker($sID);
		if (is_array($aChk))
		{
			Session::set('SES_S_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}
		$aChk = self::AlogChecker($sID,$iNO);
		if (is_array($aChk))
		{
			Session::set('SES_S_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}

		try
		{
			$result = Model_Alog::deleteAlog($this->aALog);
			if ($this->aALog['fID'])
			{
				\Clfunc_Aws::deleteFile($this->sAwsSavePath,$this->aALog['fID'].'.'.$this->aALog['fExt']);
				if ($this->aALog['fFileType'] == 1)
				{
					\Clfunc_Aws::deleteFile($this->sAwsSavePath, CL_PREFIX_THUMBNAIL.$this->aALog['fID'].'.'.$this->aALog['fExt']);
				}
				if ($this->aALog['fFileType'] == 2)
				{
					\Clfunc_Aws::deleteFile($this->sAwsSavePath,CL_PREFIX_ENCODE.$this->aALog['fID'].CL_AWS_ENCEXT);
					\Clfunc_Aws::deleteFile($this->sAwsSavePath,CL_PREFIX_THUMBNAIL.$this->aALog['fID'].'-00001.png');
				}
				\Model_File::deleteFile($this->aALog['fID']);
			}
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_S_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		Session::set('SES_S_NOTICE_MSG',__('記録を削除しました。'));
		Response::redirect('/s/'.$this->baseName);
	}

	public function action_detail($sAltID = null,$iNO = null)
	{
		$view = $this->vDir.DS.$this->baseName.'/detail';

		$aChk = self::ALogThemeChecker($sAltID);
		if (is_array($aChk))
		{
			Session::set('SES_S_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}
		$aChk = self::ALogGoalChecker($sAltID,false);
		if (is_array($aChk))
		{
			Session::set('SES_S_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}
		$aChk = self::ALogChecker($sAltID,$iNO);
		if (is_array($aChk))
		{
			Session::set('SES_S_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}

		# タイトル
		$sTitle = __('記録詳細').'｜'.$this->aALTheme['altName'];
		$this->template->set_global('pagetitle',$sTitle);

		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/'.$this->baseName, 'name'=>__('活動履歴'));
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		$this->template->set_global('aALTheme',$this->aALTheme);
		$this->template->set_global('aALGoal',$this->aALGoal);
		$this->template->set_global('aALog',$this->aALog);

		$this->template->content = View::forge($view);
		$this->template->javascript = array('cl.s.'.$this->baseName.'.js');
		return $this->template;
	}

	public function action_goal($sAltID = null)
	{
		$view = $this->vDir.DS.$this->baseName.'/goal';

		$aChk = self::ALogThemeChecker($sAltID);
		if (is_array($aChk))
		{
			Session::set('SES_S_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}
		$aChk = self::ALogGoalChecker($sAltID, false);
		if (is_array($aChk))
		{
			Session::set('SES_S_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}

		# タイトル
		$sTitle = __(':goalの設定',array('goal'=>$this->aALTheme['altGoalLabel'])).'｜'.$this->aALTheme['altName'];
		$this->template->set_global('pagetitle',$sTitle);

		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/'.$this->baseName, 'name'=>__('活動履歴'));
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		$this->template->set_global('aALTheme',$this->aALTheme);
		$this->template->set_global('aALGoal',$this->aALGoal);

		if (!Input::post(null,false))
		{
			$data = $this->aALGoalBase;
			$data['error'] = null;
			$aInput = array();
			if (!is_null($this->aALGoal))
			{
				$aInput = array(
					'ag_text' => $this->aALGoal['algText'],
				);
			}
			$data = array_merge($data,$aInput);
			$this->template->content = View::forge($view,$data);
			$this->template->javascript = array('cl.s.'.$this->baseName.'.js');
			return $this->template;
		}

		$aInput = Input::post();

		$val = Validation::forge();
		$val->add_callable('Helper_CustomValidation');
		$val->add_field('ag_text', $this->aALTheme['altGoalLabel'], 'required');
		if (!$val->run())
		{
			$data = $this->aALGoalBase;
			$data['error'] = $val->error();
			$data = array_merge($data,$aInput);
			$this->template->content = View::forge($view,$data);
			$this->template->javascript = array('cl.s.'.$this->baseName.'.js');
			return $this->template;
		}

		try
		{
			if (!is_null($this->aALGoal))
			{
				$aUpdate = array(
					'algText' => $aInput['ag_text'],
					'algDate' => date('YmdHis'),
				);
				$aWhere = array(
					array('altID','=',$sAltID),
					array('stID','=',$this->aStudent['stID']),
				);
				$result = Model_Alog::updateAlogGoal($aUpdate,$aWhere);
			}
			else
			{
				$aInsert = array(
					'altID'   => $this->aALTheme['altID'],
					'stID'    => $this->aStudent['stID'],
					'algText' => $aInput['ag_text'],
					'algDate' => date('YmdHis'),
				);
				$result = Model_Alog::insertAlogGoal($aInsert);
			}
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_S_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		Session::set('SES_S_NOTICE_MSG',__('目標を更新しました。'));
		Response::redirect('/s/'.$this->baseName);
	}

	private function ALogThemeChecker($sAltID = null)
	{
		if (is_null($this->aClass))
		{
			return array('msg'=>__('講義情報が確認できませんでした。'),'url'=>'/s/index'.$this->sesParam);
		}
		if (is_null($sAltID))
		{
			return array('msg'=>__('テーマ情報が送信されていません。'),'url'=>'/s/'.$this->baseName.$this->sesParam);
		}
		$result = Model_ALog::getALogThemeFromID($sAltID);
		if (!count($result))
		{
			return array('msg'=>__('指定されたテーマが見つかりません。').$sAltID,'url'=>'/s/'.$this->baseName.$this->sesParam);
		}
		$this->aALTheme = $result->current();
		Session::set('SES_S_ALOG_ALTID', $sAltID);
		return true;
	}

	private function ALogGoalChecker($sAltID = null, $bRequire = true)
	{
		if (is_null($sAltID))
		{
			return array('msg'=>__('テーマ情報が送信されていません。'),'url'=>'/s/'.$this->baseName.$this->sesParam);
		}
		$result = Model_ALog::getALogGoal(array(array('altID','=',$sAltID),array('stID','=',$this->aStudent['stID'])));
		if (!count($result))
		{
			if ($bRequire)
			{
				return array('msg'=>__('指定された目標が見つかりません。').$sAltID,'url'=>'/s/'.$this->baseName.$this->sesParam);
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
			return array('msg'=>__('必要な情報が送信されていません。'),'url'=>'/s/'.$this->baseName.$this->sesParam);
		}
		$result = Model_ALog::getALog(array(array('al.altID','=',$sAltID),array('al.no','=',$iNO),array('al.stID','=',$this->aStudent['stID'])));
		if (!count($result))
		{
			return array('msg'=>__('指定された記録が見つかりません。').$sAltID,'url'=>'/s/'.$this->baseName.$this->sesParam);
		}
		$this->aALog = $result->current();

		return true;
	}

}

