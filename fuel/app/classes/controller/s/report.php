<?php
class Controller_S_Report extends Controller_S_Baseclass
{
	private $baseName = 'report';
	private $aReport = null;
	private $aPut = null;
	private $aRateM = null;

	private $aCommentBase = array(
		'c_text' =>null,
	);

	public function before()
	{
		parent::before();

		$result = Model_Report::getRateMasterFromClass($this->aClass['ctID']);
		if (count($result))
		{
			foreach ($result as $r)
			{
				$this->aRateM[$r['rrScore']] = $r;
			}
		}
		$this->template->set_global('aRateMaster',$this->aRateM);
	}

	public function action_index()
	{
		$aReport = null;
		$result = Model_Report::getReportBase(array(array('rb.rbPublic','>',0),array('rb.ctID','=',$this->aClass['ctID'])),null,array('rb.rbSort'=>'desc'));
		if (count($result))
		{
			$aTemp = $result->as_array();
			foreach ($aTemp as $aQ)
			{
				$aReport[$aQ['rbID']] = $aQ;
			}
		}
		if (!is_null($aReport))
		{
			$aPut = null;
			$result = Model_Report::getReportPut(array(array('rb.ctID','=',$this->aClass['ctID']),array('rp.stID','=',$this->aStudent['stID'])));
			if (count($result))
			{
				$aPut = $result->as_array();
				foreach ($aPut as $aP)
				{
					if (array_key_exists($aP['rbID'],$aReport))
					{
						$aReport[$aP['rbID']]['RPut'] = $aP;
					}
				}
			}
		}

		# タイトル
		$sTitle = __('レポート');
		$this->template->set_global('pagetitle',$sTitle);
		# パンくずリスト生成
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		$this->template->content = View::forge($this->vDir.DS.'report/index');
		$this->template->content->set('aReport',$aReport);
		$this->template->javascript = array('cl.s.report.js');
		return $this->template;
	}

	public function action_put($sRbID)
	{
		$sBackURL = '/s/report'.$this->sesParam;

		$aChk = self::ReportPutChecker($sRbID,$this->aStudent['stID']);
		if ($this->aReport['rbPublic'] > 1 || $this->aPut['rpScore'] > 0)
		{
			Session::set('SES_S_ERROR_MSG',__('現在レポートの提出はできません。'));
			Response::redirect($sBackURL);
		}

		# タイトル
		$sTitle = '['.$this->aReport['rbTitle'].'] '.((!is_null($this->aPut))? __('再提出'):__('提出'));
		$this->template->set_global('pagetitle',$sTitle);
		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/report','name'=>__('レポート'));
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		if (!Input::post(null,false))
		{
			$aInput = null;
			$aTemp = Session::get('SES_S_REPORT_PUT_'.$sRbID,false);
			$aTemp = ($aTemp)? unserialize($aTemp):null;
			if (isset($aTemp['rbID']))
			{
				$aInput = $aTemp;
			}
			else if (!is_null($this->aPut))
			{
				$aInput = $this->aPut;

				for ($i = 1; $i <= 3; $i++)
				{
					$aFile = false;
					if ($this->aPut['fID'.$i])
					{
						try
						{
							/*
							$sFileName = $this->aPut['fID'.$i].'.'.$this->aPut['fExt'.$i];
							$sTempPath = $this->sTempFilePath.DS.'_report_'.$sFileName;
							$result = \Clfunc_Aws::getFile($this->aPut['fPath'.$i],$sFileName,$sTempPath);
							$aFile = array(
								'file'  => '_report_'.$sFileName,
								'name'  => $this->aPut['fName'.$i],
								'size'  => $this->aPut['fSize'.$i],
								'isimg' => ($this->aPut['fFileType'.$i] == 1)? 1:0,
							);
							*/
							$aFile = array(
								'file'  => $this->aPut['fID'.$i],
								'name'  => $this->aPut['fName'.$i],
								'size'  => $this->aPut['fSize'.$i],
								'isimg' => ($this->aPut['fFileType'.$i] == 1)? 1:0,
							);
						}
						catch (Exception $e)
						{
							$aFile = false;
						}
					}
					if ($aFile)
					{
						$aInput['f'.$i] = serialize($aFile);
						$aInput['fileinfo'.$i] = $aFile;
					}
				}
			}

			$this->template->content = View::forge($this->vDir.DS.'report/put');
			$this->template->content->set('aReport',$this->aReport);
			$this->template->content->set('aPut',$this->aPut);
			$this->template->content->set('aInput',$aInput);
			$this->template->javascript = array('cl.s.report.js');
			return $this->template;
		}

		$aInput = Input::post(null,false);

		$bFile = false;
		for ($i = 1; $i <= 3; $i++)
		{
			if (isset($aInput['f'.$i]) && $aInput['f'.$i] != '')
			{
				$bFile = true;
				$aInput['fileinfo'.$i] = unserialize($aInput['f'.$i]);
			}
		}

		if (!$bFile && !$aInput['rpText'])
		{
			$data['error'] = __('提出テキストか提出ファイルのどちらかは必ず入力してください。');
			$this->template->content = View::forge($this->vDir.DS.'report/put',$data);
			$this->template->content->set('aReport',$this->aReport);
			$this->template->content->set('aPut',$this->aPut);
			$this->template->content->set('aInput',$aInput);
			$this->template->javascript = array('cl.s.report.js');
			return $this->template;
		}

		$aInput['rbID'] = $sRbID;
		Session::set('SES_S_REPORT_PUT_'.$sRbID,serialize($aInput));
		Response::redirect('/s/report/check/'.$sRbID.$this->sesParam);
	}

	public function action_check($sRbID)
	{
		$aReport = null;
		$sBackURL = '/s/report/put/'.$sRbID.$this->sesParam;
		$aChk = self::ReportPutChecker($sRbID,$this->aStudent['stID']);
		if ($this->aReport['rbPublic'] > 1 || $this->aPut['rpScore'] > 0)
		{
			Session::set('SES_S_ERROR_MSG',__('現在レポートの提出はできません。'));
			Response::redirect($sBackURL);
		}

		$aTemp = Session::get('SES_S_REPORT_PUT_'.$sRbID,false);
		if (!$aTemp)
		{
			Session::set('SES_S_ERROR_MSG',__('レポートの提出情報が見つかりませんでした。'));
			Response::redirect($sBackURL);
		}
		$aTemp = ($aTemp)? unserialize($aTemp):null;
		if (!isset($aTemp['rbID']))
		{
			Session::set('SES_S_ERROR_MSG',__('レポートの提出情報が見つかりませんでした。'));
			Response::redirect($sBackURL);
		}
		$aInput = $aTemp;

		if (!Input::post(null,false))
		{
			# タイトル
			$sTitle = '['.$this->aReport['rbTitle'].'] '.((!is_null($this->aPut))? __('再提出'):__('提出'));
			$this->template->set_global('pagetitle',$sTitle);
			# パンくずリスト生成
			$this->aBread[] = array('link'=>'/report','name'=>__('レポート'));
			$this->aBread[] = array('name'=>$sTitle);
			$this->template->set_global('breadcrumbs',$this->aBread);

			$this->template->content = View::forge($this->vDir.DS.'report/check');
			$this->template->content->set('aReport',$this->aReport);
			$this->template->content->set('aPut',$this->aPut);
			$this->template->content->set('aInput',$aInput);
			$this->template->javascript = array('cl.s.report.js');
			return $this->template;
		}

		$aSubmit = Input::post(null,false);

		if (isset($aSubmit['back']))
		{
			Response::redirect($sBackURL);
		}

		// 登録データ生成
		for ($i = 1; $i <= 3; $i++)
		{
			$fID = null;
			if (isset($aInput['f'.$i]) && $aInput['f'.$i] != '')
			{
				if ($aInput['fileinfo'.$i]['file'] != $this->aPut['fID'.$i])
				{
					$sSourseFile = $this->sTempFilePath.DS.$aInput['fileinfo'.$i]['file'];
					$sThumbFile = $this->sTempFilePath.DS.CL_PREFIX_THUMBNAIL.$aInput['fileinfo'.$i]['file'];
					$sContentType = \Clfunc_Common::GetContentType($sSourseFile);
					$sExt = pathinfo($sSourseFile,PATHINFO_EXTENSION);
					$iFileType = \Clfunc_Common::GetFileType($sSourseFile);

					$fID = \Model_File::getFileID();
					$sFile = $fID.'.'.$sExt;

					# 登録情報作成
					$aInsert = array(
						'fID'          => $fID,
						'fName'        => $aInput['fileinfo'.$i]['name'],
						'fSize'        => $aInput['fileinfo'.$i]['size'],
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
	// 網ログ
	\Log::warning('Report File Regist '.$i.' - AWS PUT - '.$sSourseFile.' - '.$this->aStudent['stID'].' - '.\Clfunc_Common::FilesizeFormat($aInput['fileinfo'.$i]['size'],1).' - '.$sContentType);

						$result = \Clfunc_Aws::putFile($this->sAwsSavePath, $sFile, $sSourseFile, $sContentType);
						if ($iFileType == 1 && file_exists($sThumbFile))
						{
							$result = \Clfunc_Aws::putFile($this->sAwsSavePath, CL_PREFIX_THUMBNAIL.$sFile, $sThumbFile, $sContentType);
						}
						if ($iFileType == 2)
						{
							$result = \Clfunc_Aws::encodeMovie($this->sAwsSavePath, $fID, $sExt);
						}

	// 網ログ
	\Log::warning('Report File Regist '.$i.' - DB INSERT - '.$sSourseFile.' - '.$fID);

						$result = \Model_File::insertFile($aInsert);
					}
					catch (Exception $e)
					{

	// 網ログ
	\Log::warning('Report File Regist '.$i.' - ERROR - '.$sSourseFile.' - File exists:'.file_exists($sSourseFile));

						\Clfunc_Aws::deleteFile($this->sAwsSavePath,$sFile);
						if ($iFileType == 1)
						{
							\Clfunc_Aws::deleteFile($this->sAwsSavePath, CL_PREFIX_THUMBNAIL.$sFile);
						}
						if ($iFileType == 2)
						{
							\Clfunc_Aws::deleteFile($this->sAwsSavePath,CL_PREFIX_ENCODE.$fID.CL_AWS_ENCEXT);
							\Clfunc_Aws::deleteFile($this->sAwsSavePath,CL_PREFIX_THUMBNAIL.$fID.'-00001.png');
						}
						\Clfunc_Common::LogOut($e,__CLASS__);
						Session::set('SES_S_ERROR_MSG',__('指定した提出ファイルが保存できませんでした。').'('.$i.')'.$e->getMessage());
						Response::redirect($sBackURL);
					}
				}
				else
				{
					$fID = $this->aPut['fID'.$i];
				}
			}
			$aInput['fID'.$i] = $fID;
		}

		if (is_null($this->aPut))
		{
			$aInsert = array(
				'rbID' => $sRbID,
				'stID' => $this->aStudent['stID'],
				'fID1' => $aInput['fID1'],
				'fID2' => $aInput['fID2'],
				'fID3' => $aInput['fID3'],
				'rpText' => $aInput['rpText'],
				'rpDate' => date('YmdHis'),
				'rpstName' => $this->aStudent['stName'],
				'rpstNO' => $this->aStudent['stNO'],
				'rpstClass' => $this->aStudent['stClass'],
			);

			try
			{
				$result = Model_Report::insertPut($aInsert);
			}
			catch (Exception $e)
			{
				\Clfunc_Common::LogOut($e,__CLASS__);
				Session::set('SES_S_ERROR_MSG',$e->getMessage());
				Response::redirect($this->eRedirect);
			}
		}
		else
		{
			$aUpdate = array(
				'fID1' => $aInput['fID1'],
				'fID2' => $aInput['fID2'],
				'fID3' => $aInput['fID3'],
				'rpText' => $aInput['rpText'],
				'rpDate' => date('YmdHis'),
				'rpstName' => $this->aStudent['stName'],
				'rpstNO' => $this->aStudent['stNO'],
				'rpstClass' => $this->aStudent['stClass'],
			);

			try
			{
				$result = Model_Report::updatePut($aUpdate,$this->aPut);
			}
			catch (Exception $e)
			{
				\Clfunc_Common::LogOut($e,__CLASS__);
				Session::set('SES_S_ERROR_MSG',$e->getMessage());
				Response::redirect($this->eRedirect);
			}

			for($i = 1; $i <= 3; $i++)
			{
				if ($this->aPut['fID'.$i] && $this->aPut['fID'.$i] != $aInput['fID'.$i])
				{

// 網ログ
\Log::warning('Report File Delete '.$i.' - AWS DELETE - '.$this->aPut['fPath'.$i].' - '.$this->aPut['fID'.$i].'.'.$this->aPut['fExt'.$i]);

					\Clfunc_Aws::deleteFile($this->aPut['fPath'.$i],$this->aPut['fID'.$i].'.'.$this->aPut['fExt'.$i]);
					if ($this->aPut['fFileType'.$i] == 1)
					{
						\Clfunc_Aws::deleteFile($this->sAwsSavePath, CL_PREFIX_THUMBNAIL.$this->aPut['fID'.$i].'.'.$this->aPut['fExt'.$i]);
					}
					if ($this->aPut['fFileType'.$i] == 2)
					{
						\Clfunc_Aws::deleteFile($this->sAwsSavePath,CL_PREFIX_ENCODE.$this->aPut['fID'.$i].CL_AWS_ENCEXT);
						\Clfunc_Aws::deleteFile($this->sAwsSavePath,CL_PREFIX_THUMBNAIL.$this->aPut['fID'.$i].'-00001.png');
					}

// 網ログ
\Log::warning('Report File Delete '.$i.' - DB DELETE - '.$this->aPut['fPath'.$i].' - '.$this->aPut['fID'.$i].'.'.$this->aPut['fExt'.$i]);

					\Model_File::deleteFile($this->aPut['fID'.$i]);
				}
			}
		}

		for($i = 1; $i <= 3; $i++)
		{
			if (isset($aInput['fileinfo'.$i]))
			{
				$sSourseFile = $this->sTempFilePath.DS.$aInput['fileinfo'.$i]['file'];
				$sThumbFile = $this->sTempFilePath.DS.CL_PREFIX_THUMBNAIL.$aInput['fileinfo'.$i]['file'];

// 網ログ
\Log::warning('Report File Temp Delete '.$i.' - unlink - '.$sSourseFile);

				@unlink($sSourseFile);
				@unlink($sThumbFile);
			}
		}

		Session::delete('SES_S_REPORT_PUT_'.$sRbID);
		Session::set('SES_S_NOTICE_MSG',__('レポートを提出しました。').'['.$this->aReport['rbTitle'].']');
		Response::redirect('/s/report'.$this->sesParam);
	}

	public function action_list($sRbID = null)
	{
		$sBack = '/s/report'.$this->sesParam;

		$aChk = self::ReportChecker($sRbID);
		if (is_array($aChk))
		{
			Session::set('SES_S_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}

		if ($this->aReport['rbShare'] == 0)
		{
			Session::set('SES_S_ERROR_MSG',__('他の人の提出内容は公開されていません。'));
			Response::redirect($sBack);
		}

		$aStu = null;
		$result = Model_Student::getStudentFromClass($this->aClass['ctID'],null,null,array('st.stNO'=>'ASC'));
		if (count($result))
		{
			foreach ($result as $aS)
			{
				$aStu[$aS['stID']]['stu'] = $aS;
			}
		}

		$result = Model_Report::getReportPut(array(array('rp.rbID','=',$sRbID)));
		if (count($result))
		{
			foreach ($result as $aP)
			{
				if (isset($aStu[$aP['stID']]))
				{
					$aStu[$aP['stID']]['put'] = $aP;
				}
			}
		}

		# タイトル
		$sTitle = $this->aReport['rbTitle'].'｜'.__('共有板');
		$this->template->set_global('pagetitle',$sTitle);
		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/report','name'=>__('レポート'));
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		$this->template->content = View::forge($this->vDir.DS.'report/list');
		$this->template->content->set('aReport',$this->aReport);
		$this->template->content->set('aStu',$aStu);
		$this->template->javascript = array('cl.s.report.js');
		return $this->template;
	}


	public function action_shareboard($sRbID = null, $sStID = null, $sM = null)
	{
		$sBack = '/s/report/list/'.$sRbID.$this->sesParam;

		$aChk = self::ReportPutChecker($sRbID,$sStID);
		if (is_array($aChk))
		{
			Session::set('SES_S_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}
		if (is_null($this->aPut))
		{
			$aInsert = array(
				'rbID' => $sRbID,
				'stID' => $sStID,
				'rpDate' => CL_DATETIME_DEFAULT,
			);
			try
			{
				$result = Model_Report::insertPut($aInsert);
			}
			catch (Exception $e)
			{
				\Clfunc_Common::LogOut($e,__CLASS__);
				Session::set('SES_T_ERROR_MSG',$e->getMessage());
				Response::redirect($this->eRedirect);
			}
			$aChk = self::ReportPutChecker($sRbID,$sStID);
		}

		if ($this->aReport['rbShare'] == 0)
		{
			Session::set('SES_S_ERROR_MSG',__('他の人の提出内容は公開されていません。'));
			Response::redirect('/s/report'.$this->sesParam);
		}

		$result = Model_Student::getStudentFromClass($this->aClass['ctID'],array(array('sp.stID','=',$sStID)));
		if (count($result))
		{
			$aStu = $result->current();
			$sName = $aStu['stName'];
		}
		if (is_null($sName))
		{
			$sName = $this->aPut['rpstName'];
		}

		$aCnt = null;
		$aParents = null;
		$result = \Model_Report::getReportComment(array(array('rc.rbID','=',$sRbID),array('rc.stID','=',$sStID),array('rc.rcBranch','=',0)),null,array('no'=>'desc'));
		if (count($result))
		{
			foreach ($result as $aC)
			{
				$aParents['p'.$aC['no']] = $aC;
			}
		}

		$aComments = null;
		$result = \Model_Report::getReportComment(array(array('rc.rbID','=',$sRbID),array('rc.stID','=',$sStID),array('rc.rcBranch','>',0)),null,array('no'=>'asc'));
		if (count($result))
		{
			foreach ($result as $aC)
			{
				if (isset($aCnt['p'.$aC['rcBranch']]))
				{
					$aCnt['p'.$aC['rcBranch']]++;
				}
				else
				{
					$aCnt['p'.$aC['rcBranch']] = 1;
				}
				$aComments['p'.$aC['rcBranch']]['children'][$aC['no']] = $aC;
			}
		}

		$aRate = null;
		$result = \Model_Report::getReportRate(array(array('rr.rbID','=',$sRbID),array('rr.stID','=',$sStID),array('rr.rrID','=',$this->aStudent['stID'])));
		if (count($result))
		{
			$aRate = $result->current();
		}

		$aCount = null;
		$result = \Model_Report::getReportCount(array(array('rc.rbID','=',$sRbID),array('rc.stID','=',$sStID)));
		if (count($result))
		{
			$aCount = $result->current();
		}

		# タイトル
		$sTitle = $this->aReport['rbTitle'].'｜'.$aStu['stName'];
		$this->template->set_global('pagetitle',$sTitle);
		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/report','name'=>__('レポート'));
		$this->aBread[] = array('link'=>'/report/list/'.$sRbID,'name'=>__('共有板'));
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		$this->template->content = View::forge($this->vDir.DS.'report/shareboard');
		$this->template->content->set('aReport',$this->aReport);
		$this->template->content->set('aStu',$aStu);
		$this->template->content->set('aPut',$this->aPut);
		$this->template->content->set('aCnt',$aCnt);
		$this->template->content->set('aParents',$aParents);
		$this->template->content->set('aComments',$aComments);
		$this->template->content->set('aCount',$aCount);
		$this->template->content->set('aRate',$aRate);
		$this->template->content->set('sM',$sM);
		$this->template->javascript = array('cl.s.report.js','cl.report.js');
		return $this->template;
	}

	public function action_rate($sRbID = null, $sStID = null)
	{
		$sBack = Input::referrer();

		$aChk = self::ReportPutChecker($sRbID,$sStID);
		if (is_array($aChk))
		{
			Session::set('SES_S_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}
		if (is_null($this->aPut))
		{
			Session::set('SES_S_ERROR_MSG',__('レポートの提出情報が見つかりませんでした。'));
			Response::redirect($sBack);
		}

		if (!Input::post(null,false))
		{
			Session::set('SES_S_ERROR_MSG',__('情報が正しく送信されていません。'));
			Response::redirect($sBack);
		}
		$aInput = Input::post(null,false);

		$iScore = $aInput['rate'];
		if ($iScore < 1 || $iScore > 5)
		{
			Session::set('SES_S_ERROR_MSG',__('評価を選択してください。'));
			Response::redirect($sBack);
		}

		$result = Model_Report::getReportRate(array(array('rr.rbID','=',$sRbID),array('rr.stID','=',$sStID),array('rr.rrID','=',$this->aStudent['stID'])));
		if (count($result))
		{
			$aRate = $result->current();
			try
			{
				$aUpdate = array(
					'rrScore' => $iScore,
					'rrDate' => date('YmdHis'),
				);
				$result = \Model_Report::updateReportRate($aUpdate,$aRate);
			}
			catch (Exception $e)
			{
				\Clfunc_Common::LogOut($e,__CLASS__);
				Session::set('SES_S_ERROR_MSG',$e->getMessage());
				Response::redirect($this->eRedirect);
				return;
			}
		}
		else
		{
			try
			{
				$aInsert = array(
					'rbID' => $sRbID,
					'stID' => $sStID,
					'rrID' => $this->aStudent['stID'],
					'rrScore' => $iScore,
					'rrDate' => date('YmdHis'),
				);
				$result = \Model_Report::insertReportRate($aInsert);
			}
			catch (Exception $e)
			{
				\Clfunc_Common::LogOut($e,__CLASS__);
				Session::set('SES_S_ERROR_MSG',$e->getMessage());
				Response::redirect($this->eRedirect);
				return;
			}
		}
		Session::set('SES_S_NOTICE_MSG',__('評価しました。'));
		Response::redirect($sBack);
	}

	public function action_rescreate($sRbID = null, $sStID = null, $iNO = 0)
	{
		$sBack = Input::referrer();

		$aChk = self::ReportPutChecker($sRbID,$sStID);
		if (is_array($aChk))
		{
			Session::set('SES_S_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url'].$this->sesParam);
		}

		$result = Model_Student::getStudentFromClass($this->aClass['ctID'],array(array('sp.stID','=',$sStID)));
		if (count($result))
		{
			$aStu = $result->current();
			$sName = $aStu['stName'];
		}
		if (is_null($sName))
		{
			$sName = $this->aPut['rpstName'];
		}

		$sCheck = 'rescreate';
		$sTitle = __('コメントする');
		if ($iNO > 0)
		{
			$result = Model_Report::getReportComment(array(array('rc.no','=',$iNO),array('rc.rbID','=',$sRbID),array('rc.stID','=',$sStID)));
			if (!count($result))
			{
				Session::set('SES_S_ERROR_MSG',__('記事情報が見つかりません。'));
				Response::redirect($sBack);
			}
			$aParent = $result->current();
			$this->template->set_global('aParent',$aParent);
		}

		# タイトル
		$this->template->set_global('pagetitle',$sTitle);

		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/report','name'=>__('レポート'));
		$this->aBread[] = array('link'=>'/report/list/'.$sRbID,'name'=>__('共有板'));
		$this->aBread[] = array('link'=>'/report/sharebord/'.$sRbID.DS.$sStID.'/s','name'=>$this->aReport['rbTitle'].'｜'.$aStu['stName']);
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		$this->template->set_global('aReport',$this->aReport);
		$this->template->set_global('aPut',$this->aPut);
		$this->template->set_global('aStu',$aStu);
		$this->template->set_global('iNO',$iNO);
		$this->template->set_global('bEdit',false);

		if (!Input::post(null,false))
		{
			$data = $this->aCommentBase;
			$data['error'] = null;
			if ($aInput = unserialize(Session::get('SES_S_REPORT_COMMENT',false)))
			{
				$data = array_merge($data,$aInput);
			}
			$this->template->content = View::forge($this->vDir.DS.$this->baseName.'/resedit',$data);
			$this->template->javascript = array('cl.s.report.js','cl.report.js');
			return $this->template;
		}

		$aInput = Input::post();

		$val = Validation::forge();
		$val->add_callable('Helper_CustomValidation');
		$val->add_field('c_text', __('コメント'), 'required');
		if (!$val->run())
		{
			$data =$this->aCommentBase;
			$data['error'] = $val->error();
			$data = array_merge($data,$aInput);
			$this->template->content = View::forge($this->vDir.DS.$this->baseName.'/resedit',$data);
			$this->template->javascript = array('cl.s.report.js','cl.report.js');
			return $this->template;
		}

		try
		{
			$aInsert = array(
				'rbID'      => $sRbID,
				'stID'      => $sStID,
				'rcComment' => $aInput['c_text'],
				'rcID'      => $this->aStudent['stID'],
				'rcDate'    => date('YmdHis'),
				'rcTeach'   => 0,
				'rcBranch'  => $iNO,
			);
			$iNO = \Model_Report::insertReportComment($aInsert);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_S_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
			return;
		}

		Session::set('SES_S_NOTICE_MSG',__('コメントを登録しました。'));
		Response::redirect('/s/'.$this->baseName.'/shareboard/'.$sRbID.DS.$sStID.'/s'.Clfunc_Mobile::SesID());
	}

	public function action_resedit($sRbID = null, $sStID = null, $iNO = 0)
	{
		$sBack = Input::referrer();

		$aChk = self::ReportPutChecker($sRbID,$sStID);
		if (is_array($aChk))
		{
			Session::set('SES_S_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url'].$this->sesParam);
		}

		$result = Model_Student::getStudentFromClass($this->aClass['ctID'],array(array('sp.stID','=',$sStID)));
		if (count($result))
		{
			$aStu = $result->current();
			$sName = $aStu['stName'];
		}
		if (is_null($sName))
		{
			$sName = $this->aPut['rpstName'];
		}

		$sCheck = 'resedit';
		$sTitle = __('コメントの編集');
		if ($iNO > 0)
		{
			$result = Model_Report::getReportComment(array(array('rc.no','=',$iNO),array('rc.rbID','=',$sRbID),array('rc.stID','=',$sStID),array('rc.rcID','=',$this->aStudent['stID'])));
			if (!count($result))
			{
				Session::set('SES_S_ERROR_MSG',__('記事情報が見つかりません。'));
				Response::redirect($sBack);
			}
			$aParent = $result->current();

			$this->template->set_global('aParent',$aParent);
		} else {
			Session::set('SES_S_ERROR_MSG',__('記事情報が見つかりません。'));
			Response::redirect($sBack);
		}

		# タイトル
		$this->template->set_global('pagetitle',$sTitle);

		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/report','name'=>__('レポート'));
		$this->aBread[] = array('link'=>'/report/list/'.$sRbID,'name'=>__('共有板'));
		$this->aBread[] = array('link'=>'/report/sharebord/'.$sRbID.DS.$sStID.'/s','name'=>$this->aReport['rbTitle'].'｜'.$aStu['stName']);
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		$this->template->set_global('aReport',$this->aReport);
		$this->template->set_global('aPut',$this->aPut);
		$this->template->set_global('aStu',$aStu);
		$this->template->set_global('iNO',$iNO);
		$this->template->set_global('bEdit',true);

		if (!Input::post(null,false))
		{
			$data = $this->aCommentBase;
			$data['error'] = null;
			if (!$aInput = unserialize(Session::get('SES_S_REPORT_COMMENT',false)))
			{
				$aInput = array(
					'c_text' =>$aParent['rcComment'],
				);
			}
			$data = array_merge($data,$aInput);
			$this->template->content = View::forge($this->vDir.DS.$this->baseName.'/resedit',$data);
			$this->template->javascript = array('cl.s.coop.js','cl.report.js');
			return $this->template;
		}

		$aInput = Input::post();

		$val = Validation::forge();
		$val->add_callable('Helper_CustomValidation');
		$val->add_field('c_text', __('コメント'), 'required');
		if (!$val->run())
		{
			$data = $this->aCommentBase;
			$data['error'] = $val->error();
			$data = array_merge($data,$aInput);
			$this->template->content = View::forge($this->vDir.DS.$this->baseName.'/resedit',$data);
			$this->template->javascript = array('cl.s.coop.js','cl.report.js');
			return $this->template;
		}

		try
		{
			$aUpdate = array(
				'rcComment' => $aInput['c_text'],
				'rcDate'    => date('YmdHis'),
			);
			$aWhere = array(array('no','=',$iNO));
			$result = \Model_Report::updateReportComment($aUpdate,$aWhere);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_S_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
			return;
		}

		Session::set('SES_S_NOTICE_MSG',__('コメントを更新しました。'));
		Response::redirect('/s/'.$this->baseName.'/shareboard/'.$sRbID.DS.$sStID.'/s'.Clfunc_Mobile::SesID());
	}


	public function action_resdelete($sRbID = null, $sStID = null, $iNO = null)
	{
		$sBack = Input::referrer();
		$sShareboard = '/s/'.$this->baseName.'/shareboard/'.$sRbID.DS.$sStID.'/s'.$this->sesParam;

		$aChk = self::ReportPutChecker($sRbID,$sStID);
		if (is_array($aChk))
		{
			Session::set('SES_S_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url'].$this->sesParam);
		}

		$result = Model_Student::getStudentFromClass($this->aClass['ctID'],array(array('sp.stID','=',$sStID)));
		if (count($result))
		{
			$aStu = $result->current();
			$sName = $aStu['stName'];
		}
		if (is_null($sName))
		{
			$sName = $this->aPut['rpstName'];
		}

		if ($iNO > 0)
		{
			$result = Model_Report::getReportComment(array(array('rc.no','=',$iNO),array('rc.rbID','=',$sRbID),array('rc.stID','=',$sStID),array('rc.rcID','=',$this->aStudent['stID'])));
			if (!count($result))
			{
				Session::set('SES_S_ERROR_MSG',__('記事情報が見つかりません。'));
				Response::redirect($sBack);
			}
			$aCom = $result->current();

			$this->template->set_global('aCom',$aCom);
		} else {
			Session::set('SES_S_ERROR_MSG',__('記事情報が見つかりません。'));
			Response::redirect($sBack);
		}

		# タイトル
		$sTitle = __('コメントの削除');
		$this->template->set_global('pagetitle',$sTitle);

		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/report','name'=>__('レポート'));
		$this->aBread[] = array('link'=>'/report/list/'.$sRbID,'name'=>__('共有板'));
		$this->aBread[] = array('link'=>'/report/sharebord/'.$sRbID.DS.$sStID.'/s','name'=>$this->aReport['rbTitle'].'｜'.$aStu['stName']);
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		$this->template->set_global('aReport',$this->aReport);
		$this->template->set_global('aPut',$this->aPut);
		$this->template->set_global('aStu',$aStu);
		$this->template->set_global('iNO',$iNO);

		if (!Input::post(null,false))
		{
			$this->template->content = View::forge($this->vDir.DS.$this->baseName.'/resdelete');
			return $this->template;
		}
		$aPost = Input::post(null,false);
		if (isset($aPost['back']))
		{
			Response::redirect($sShareboard);
		}

		try
		{
			$result = Model_Report::deleteReportComment($aCom);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_S_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		Session::set('SES_S_NOTICE_MSG',__('コメントを削除しました。'));
		Response::redirect($sShareboard);
	}


	private function ReportChecker($sRbID = null)
	{
		if (is_null($sRbID))
		{
			return array('msg'=>__('レポート情報が送信されていません。'),'url'=>'/s/report'.$this->sesParam);
		}
		$result = Model_Report::getReportBase(array(array('rb.rbID','=',$sRbID),array('rb.ctID','=',$this->aClass['ctID']),array('rb.rbPublic','!=',0)));
		if (!count($result))
		{
			return array('msg'=>__('指定されたレポートが見つかりません。'),'url'=>'/s/report'.$this->sesParam);
		}
		$this->aReport = $result->current();
		return true;
	}

	private function ReportPutChecker($sRbID,$sStID = null)
	{
		$aChk = self::ReportChecker($sRbID);
		if (is_array($aChk))
		{
			Session::set('SES_S_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url'].$this->sesParam);
		}

		$result = Model_Report::getReportPut(array(array('rp.rbID','=',$sRbID),array('rp.stID','=',$sStID)));
		if (count($result))
		{
			$this->aPut = $result->current();
		}

		return true;
	}
}