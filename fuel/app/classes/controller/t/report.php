<?php
class Controller_T_Report extends Controller_T_Baseclass
{
	private $bn = 't/report';
	private $aReportBase = array(
		'r_name'=>null,
		'r_auto_public'=>0, 'r_auto_s_date'=>null, 'r_auto_e_date'=>null, 'r_auto_s_time'=>null, 'r_auto_e_time'=>null,
		'r_text'=>null, 'r_file'=>null, 'r_result'=>null,
		'r_share'=>0, 'r_anonymous'=>0,
		'base_fileinfo' =>array(
			'file'=>'',
			'name'=>'',
			'size'=>0,
			'isimg'=>false,
		),
		'result_fileinfo' =>array(
			'file'=>'',
			'name'=>'',
			'size'=>0,
			'isimg'=>false,
		),
	);
	private $aSearchCol = array(
		'stLogin','stName','stNO','stDept','stSubject','stYear','stClass','stCourse'
	);
	private $aReport = null;
	private $aPut = null;
	private $aRateM = null;

	public function before()
	{
		parent::before();
		\Clfunc_Common::ContractDetect($this->aTeacher, __CLASS__);

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

		$result = Model_Report::setReportStatus($this->aClass['ctID']);
		$result = Model_Report::getReportBase(array(array('rb.ctID','=',$this->aClass['ctID'])),null,array('rb.rbSort'=>'desc'));
		if (count($result))
		{
			$aReport = $result->as_array();
		}
		elseif ($this->aClass['ctStatus'])
		{
			Response::redirect(DS.$this->bn.'/create');
		}

		# タイトル
		$sTitle = __('レポート');
		$this->template->set_global('pagetitle',$sTitle);

		# パンくずリスト生成
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		# カスタムメニュー
		$aCustomMenu = array(
			array(
				'url'  => DS.$this->bn.'/create/',
				'name' => __('レポートテーマの新規作成'),
				'show' => 1,
			),
			array(
				'url'  => DS.$this->bn.'/ratesetting/',
				'name' => __('レポート評価値の設定'),
				'show' => 1,
			),
			array(
				'url'  => '/t/output/reportputlist.csv',
				'name' => __('提出一覧CSVのダウンロード'),
				'show' => 0,
				'icon' => 'fa-download',
			),
		);
		$this->template->set_global('aCustomMenu',$aCustomMenu);
		$aCustomBtn = array(
			array(
				'url'  => DS.$this->bn.'/putlist/',
				'name' => __('提出一覧'),
				'show' => 0,
			),
		);
		$this->template->set_global('aCustomBtn',$aCustomBtn);

		$this->template->content = View::forge($this->bn.'/index');
		$this->template->content->set('aReport',$aReport);
		$this->template->javascript = array('cl.t.report.js');
		return $this->template;
	}

	public function action_create()
	{
		if (!$this->aClass['ctStatus'])
		{
			Session::set('SES_T_ERROR_MSG',__('終了した講義にはレポートを新規作成することはできません。'));
			Response::redirect(DS.$this->bn);
		}

		# タイトル
		$sTitle = __('レポートテーマの新規作成');
		$this->template->set_global('pagetitle',$sTitle);

		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/report','name'=>__('レポート'));
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		if (!Input::post(null,false))
		{
			$data = $this->aReportBase;
			$data['r_auto_s_date'] = ClFunc_Tz::tz('Y/m/d',$this->tz);
			$data['r_auto_e_date'] = ClFunc_Tz::tz('Y/m/d',$this->tz);
			$data['r_auto_s_time'] = ClFunc_Tz::tz('H:i',$this->tz);
			$data['r_auto_e_time'] = ClFunc_Tz::tz('H:i',$this->tz);
			$data['error'] = null;
			$this->template->content = View::forge($this->bn.'/edit',$data);
			$this->template->javascript = array('jquery.timepicker.js','cl.t.report.js');
			return $this->template;
		}

		$aInput = Input::post();

		if ($aInput['r_file'] != '')
		{
			$aInput['base_fileinfo'] = unserialize($aInput['r_file']);
		}

		$val = Validation::forge();
		$val->add_callable('Helper_CustomValidation');
		$val->add_field('r_name', __('テーマタイトル'), 'required|max_length['.CL_TITLE_LENGTH.']');
		if ($aInput['r_auto_public'])
		{
			$val->add_field('r_auto_s_time', __('開始日時'), 'required|time')
				->add_rule('min_time',ClFunc_Tz::tz('Y/m/d H:i',$this->tz),$aInput['r_auto_s_date']);
			$val->add_field('r_auto_e_time', __('終了日時'), 'required|time')
				->add_rule('min_time',$aInput['r_auto_s_date'].' '.$aInput['r_auto_s_time'],$aInput['r_auto_e_date']);
		}
		$val->add_field('r_text', __('内容/備考'), 'required');

		if (!$val->run())
		{
			$data = $aInput;
			$data['error'] = $val->error();
			$this->template->content = View::forge($this->bn.'/edit',$data);
			$this->template->javascript = array('jquery.timepicker.js','cl.t.report.js');
			return $this->template;
		}

		// 登録データ生成
		$sfID = null;
		if ($aInput['r_file'] != '')
		{
			$sSourseFile = $this->sTempFilePath.DS.$aInput['base_fileinfo']['file'];
			$sThumbFile = $this->sTempFilePath.DS.CL_PREFIX_THUMBNAIL.$aInput['base_fileinfo']['file'];
			$sContentType = \Clfunc_Common::GetContentType($sSourseFile);
			$sExt = pathinfo($sSourseFile,PATHINFO_EXTENSION);
			$iFileType = \Clfunc_Common::GetFileType($sSourseFile);

			$sfID = \Model_File::getFileID();
			$sFile = $sfID.'.'.$sExt;

			# 登録情報作成
			$aInsert = array(
				'fID'          => $sfID,
				'fName'        => $aInput['base_fileinfo']['name'],
				'fSize'        => $aInput['base_fileinfo']['size'],
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
				Session::set('SES_T_ERROR_MSG','指定した添付ファイルが保存できませんでした。'.$e->getMessage());
				Response::redirect(DS.$this->bn.'/create');
			}
		}

		$aInsert = array(
			'ctID'         => $this->aClass['ctID'],
			'rbTitle'      => $aInput['r_name'],
			'rbText'       => $aInput['r_text'],
			'rbShare'      => $aInput['r_share'],
			'rbAnonymous'  => $aInput['r_anonymous'],
			'rbDate'       => date('YmdHis'),
			'rbPublic'     => 0,
			'baseFID'      => $sfID,
		);
		if ($aInput['r_auto_public'])
		{
			$aInsert['rbAutoPublicDate'] = ClFunc_Tz::tz('Y-m-d H:i:s',null,$aInput['r_auto_s_date'].' '.$aInput['r_auto_s_time'].':00',$this->tz);
			$aInsert['rbAutoCloseDate'] = ClFunc_Tz::tz('Y-m-d H:i:s',null,$aInput['r_auto_e_date'].' '.$aInput['r_auto_e_time'].':00',$this->tz);
		}

		try
		{
			$result = Model_Report::insertReport($aInsert);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		Session::set('SES_T_NOTICE_MSG',__('レポートテーマを作成しました。').'['.$aInput['r_name'].']');
		Response::redirect(DS.$this->bn);
	}

	public function action_edit($sID = null)
	{
		$aChk = self::ReportChecker($sID);
		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}

		# タイトル
		$sTitle = __('レポートテーマ情報の編集');
		$this->template->set_global('pagetitle',$sTitle);

		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/report','name'=>__('レポート'));
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		if (!Input::post(null,false))
		{
			$data = $this->aReportBase;
			$data['r_name']         = $this->aReport['rbTitle'];
			$data['r_text']         = $this->aReport['rbText'];
			$data['r_auto_public']  = ($this->aReport['rbAutoPublicDate'] != CL_DATETIME_DEFAULT)? 1:0;
			$data['r_auto_s_date']  = ($this->aReport['rbAutoPublicDate'] != CL_DATETIME_DEFAULT)? ClFunc_Tz::tz('Y/m/d',$this->tz,$this->aReport['rbAutoPublicDate']):ClFunc_Tz::tz('Y/m/d',$this->tz);
			$data['r_auto_s_time']  = ($this->aReport['rbAutoPublicDate'] != CL_DATETIME_DEFAULT)? ClFunc_Tz::tz('H:i',$this->tz,$this->aReport['rbAutoPublicDate']):ClFunc_Tz::tz('H:i',$this->tz);
			$data['r_auto_e_date']  = ($this->aReport['rbAutoCloseDate'] != CL_DATETIME_DEFAULT)? ClFunc_Tz::tz('Y/m/d',$this->tz,$this->aReport['rbAutoCloseDate']):ClFunc_Tz::tz('Y/m/d',$this->tz);
			$data['r_auto_e_time']  = ($this->aReport['rbAutoCloseDate'] != CL_DATETIME_DEFAULT)? ClFunc_Tz::tz('H:i',$this->tz,$this->aReport['rbAutoCloseDate']):ClFunc_Tz::tz('H:i',$this->tz);
			$data['r_share']        = $this->aReport['rbShare'];
			$data['r_anonymous']    = $this->aReport['rbAnonymous'];
			$data['r_file']         = $this->aReport['baseFID'];
			$data['r_result']       = $this->aReport['resultFID'];
			$data['error'] = null;

			$aBFile = false;
			if ($this->aReport['baseFID'])
			{
				try
				{
					/*
					$sFileName = $this->aReport['baseFID'].'.'.$this->aReport['baseFExt'];
					$sTempPath = $this->sTempFilePath.DS.'_report_'.$sFileName;
					$result = \Clfunc_Aws::getFile($this->aReport['baseFPath'],$sFileName,$sTempPath);
					$aBFile = array(
						'file'  => '_report_'.$sFileName,
						'name'  => $this->aReport['baseFName'],
						'size'  => $this->aReport['baseFSize'],
						'isimg' => ($this->aReport['baseFFileType'] == 1)? 1:0,
					);
					*/
					$aBFile = array(
						'file'  => $this->aReport['baseFID'],
						'name'  => $this->aReport['baseFName'],
						'size'  => $this->aReport['baseFSize'],
						'isimg' => ($this->aReport['baseFFileType'] == 1)? 1:0,
					);
				}
				catch (Exception $e)
				{
					$aBFile = false;
				}
			}
			if ($aBFile)
			{
				$data['r_file'] = serialize($aBFile);
				$data['base_fileinfo'] = $aBFile;
			}
			$aRFile = false;
			if ($this->aReport['resultFID'])
			{
				try
				{
					/*
					$sFileName = $this->aReport['resultFID'].'.'.$this->aReport['resultFExt'];
					$sTempPath = $this->sTempFilePath.DS.'_report_'.$sFileName;
					$result = \Clfunc_Aws::getFile($this->aReport['resultFPath'],$sFileName,$sTempPath);
					$aRFile = array(
						'file'  => '_report_'.$sFileName,
						'name'  => $this->aReport['resultFName'],
						'size'  => $this->aReport['resultFSize'],
						'isimg' => ($this->aReport['resultFFileType'] == 1)? 1:0,
					);
					*/
					$aRFile = array(
						'file'  => $this->aReport['resultFID'],
						'name'  => $this->aReport['resultFName'],
						'size'  => $this->aReport['resultFSize'],
						'isimg' => ($this->aReport['resultFFileType'] == 1)? 1:0,
					);
				}
				catch (Exception $e)
				{
					$aRFile = false;
				}
			}
			if ($aRFile)
			{
				$data['r_result'] = serialize($aRFile);
				$data['result_fileinfo'] = $aRFile;
			}

			$this->template->content = View::forge($this->bn.'/edit',$data);
			$this->template->content->set('aReport',$this->aReport);
			$this->template->javascript = array('jquery.timepicker.js','cl.t.report.js');
			return $this->template;
		}

		$aInput = Input::post();

		if ($aInput['r_file'] != '')
		{
			$aInput['base_fileinfo'] = unserialize($aInput['r_file']);
		}
		if ($aInput['r_result'] != '')
		{
			$aInput['result_fileinfo'] = unserialize($aInput['r_result']);
		}


		$val = Validation::forge();
		$val->add_callable('Helper_CustomValidation');
		$val->add_field('r_name', __('テーマタイトル'), 'required|max_length['.CL_TITLE_LENGTH.']');
		if ($aInput['r_auto_public'])
		{
			$val->add_field('r_auto_e_time', __('終了日時'), 'required|time')
				->add_rule('min_time',$aInput['r_auto_s_date'].' '.$aInput['r_auto_s_time'],$aInput['r_auto_e_date']);
		}
		$val->add_field('r_text', __('内容/備考'), 'required');

		if (!$val->run())
		{
			$data = $aInput;
			$data['error'] = $val->error();
			$this->template->content = View::forge($this->bn.'/edit',$data);
			$this->template->content->set('aReport',$this->aReport);
			$this->template->javascript = array('jquery.timepicker.js','cl.t.report.js');
			return $this->template;
		}

		// 登録データ生成
		$base_fID = null;
		if ($aInput['r_file'] != '')
		{
			if ($aInput['base_fileinfo']['file'] != $this->aReport['baseFID'])
			{
				$sSourseFile = $this->sTempFilePath.DS.$aInput['base_fileinfo']['file'];
				$sThumbFile = $this->sTempFilePath.DS.CL_PREFIX_THUMBNAIL.$aInput['base_fileinfo']['file'];
				$sContentType = \Clfunc_Common::GetContentType($sSourseFile);
				$sExt = pathinfo($sSourseFile,PATHINFO_EXTENSION);
				$iFileType = \Clfunc_Common::GetFileType($sSourseFile);

				$base_fID = \Model_File::getFileID();
				$sFile = $base_fID.'.'.$sExt;

				# 登録情報作成
				$aInsert = array(
					'fID'          => $base_fID,
					'fName'        => $aInput['base_fileinfo']['name'],
					'fSize'        => $aInput['base_fileinfo']['size'],
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
						$result = \Clfunc_Aws::encodeMovie($this->sAwsSavePath, $base_fID, $sExt);
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
						\Clfunc_Aws::deleteFile($this->sAwsSavePath,CL_PREFIX_ENCODE.$base_fID.CL_AWS_ENCEXT);
						\Clfunc_Aws::deleteFile($this->sAwsSavePath,CL_PREFIX_THUMBNAIL.$base_fID.'-00001.png');
					}
					\Clfunc_Common::LogOut($e,__CLASS__);
					Session::set('SES_T_ERROR_MSG','指定した添付ファイルが保存できませんでした。'.$e->getMessage());
					Response::redirect(DS.$this->bn.'/create');
				}
			}
			else
			{
				$base_fID = $this->aReport['baseFID'];
			}
		}
		// 登録データ生成
		$result_fID = null;
		if ($aInput['r_result'] != '')
		{
			if ($aInput['result_fileinfo']['file'] != $this->aReport['resultFID'])
			{
				$sRSourseFile = $this->sTempFilePath.DS.$aInput['result_fileinfo']['file'];
				$sRThumbFile = $this->sTempFilePath.DS.CL_PREFIX_THUMBNAIL.$aInput['result_fileinfo']['file'];
				$sRContentType = \Clfunc_Common::GetContentType($sRSourseFile);
				$sRExt = pathinfo($sRSourseFile,PATHINFO_EXTENSION);
				$iRFileType = \Clfunc_Common::GetFileType($sRSourseFile);

				$result_fID = \Model_File::getFileID();
				$sRFile = $result_fID.'.'.$sRExt;

				# 登録情報作成
				$aInsert = array(
					'fID'          => $result_fID,
					'fName'        => $aInput['result_fileinfo']['name'],
					'fSize'        => $aInput['result_fileinfo']['size'],
					'fExt'         => $sRExt,
					'fContentType' => $sRContentType,
					'fFileType'    => $iRFileType,
					'fPath'        => $this->sAwsSavePath,
					'fUserType'    => 0,
					'fUser'        => $this->aTeacher['ttID'],
					'fDate'        => date('YmdHis'),
				);

				try
				{
					$result = \Clfunc_Aws::putFile($this->sAwsSavePath, $sRFile, $sRSourseFile, $sRContentType);
					if ($iRFileType == 1 && file_exists($sRThumbFile))
					{
						$result = \Clfunc_Aws::putFile($this->sAwsSavePath, CL_PREFIX_THUMBNAIL.$sRFile, $sRThumbFile, $sRContentType);
					}
					if ($iRFileType == 2)
					{
						$result = \Clfunc_Aws::encodeMovie($this->sAwsSavePath, $result_fID, $sRExt);
					}
					$result = \Model_File::insertFile($aInsert);
				}
				catch (Exception $e)
				{
					\Clfunc_Aws::deleteFile($this->sAwsSavePath,$sRFile);
					if ($iRFileType == 1)
					{
						\Clfunc_Aws::deleteFile($this->sAwsSavePath, CL_PREFIX_THUMBNAIL.$sRFile);
					}
					if ($iRFileType == 2)
					{
						\Clfunc_Aws::deleteFile($this->sAwsSavePath,CL_PREFIX_ENCODE.$result_fID.CL_AWS_ENCEXT);
						\Clfunc_Aws::deleteFile($this->sAwsSavePath,CL_PREFIX_THUMBNAIL.$result_fID.'-00001.png');
					}
					\Clfunc_Common::LogOut($e,__CLASS__);
					Session::set('SES_T_ERROR_MSG','指定した添付ファイルが保存できませんでした。'.$e->getMessage());
					Response::redirect(DS.$this->bn.'/create');
				}
			}
			else
			{
				$result_fID = $this->aReport['resultFID'];
			}
		}

		// 更新データ生成
		$aUpdate = array(
			'rbTitle'      => $aInput['r_name'],
			'rbText'       => $aInput['r_text'],
			'rbShare'      => $aInput['r_share'],
			'rbAnonymous'  => $aInput['r_anonymous'],
			'baseFID'      => $base_fID,
			'resultFID'    => $result_fID,
		);
		if ($aInput['r_auto_public'])
		{
			$aUpdate['rbAutoPublicDate'] = ClFunc_Tz::tz('Y-m-d H:i:s',null,$aInput['r_auto_s_date'].' '.$aInput['r_auto_s_time'].':00',$this->tz);
			$aUpdate['rbAutoCloseDate'] = ClFunc_Tz::tz('Y-m-d H:i:s',null,$aInput['r_auto_e_date'].' '.$aInput['r_auto_e_time'].':00',$this->tz);
		}
		else
		{
			$aUpdate['rbAutoPublicDate'] = CL_DATETIME_DEFAULT;
			$aUpdate['rbAutoCloseDate'] = CL_DATETIME_DEFAULT;
		}

		try
		{
			$result = Model_Report::updateReport($aUpdate,array(array('rbID','=',$sID)));
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		if ($base_fID)
		{
			@unlink($sSourseFile);
			@unlink($sThumbFile);
		}
		if ($result_fID)
		{
			@unlink($sRSourseFile);
			@unlink($sRThumbFile);
		}

		if ($this->aReport['baseFID'] && $this->aReport['baseFID'] != $base_fID)
		{
			\Clfunc_Aws::deleteFile($this->aReport['baseFPath'],$this->aReport['baseFID'].'.'.$this->aReport['baseFExt']);
			if ($this->aReport['baseFFileType'] == 1)
			{
				\Clfunc_Aws::deleteFile($this->sAwsSavePath, CL_PREFIX_THUMBNAIL.$this->aReport['baseFID'].'.'.$this->aReport['baseFExt']);
			}
			if ($this->aReport['baseFFileType'] == 2)
			{
				\Clfunc_Aws::deleteFile($this->sAwsSavePath,CL_PREFIX_ENCODE.$this->aReport['baseFID'].CL_AWS_ENCEXT);
				\Clfunc_Aws::deleteFile($this->sAwsSavePath,CL_PREFIX_THUMBNAIL.$this->aReport['baseFID'].'-00001.png');
			}
			\Model_File::deleteFile($this->aReport['baseFID']);
		}
		if ($this->aReport['resultFID'] && $this->aReport['resultFID'] != $result_fID)
		{
			\Clfunc_Aws::deleteFile($this->aReport['resultFPath'],$this->aReport['resultFID'].'.'.$this->aReport['resultFExt']);
			if ($this->aReport['resultFFileType'] == 1)
			{
				\Clfunc_Aws::deleteFile($this->sAwsSavePath, CL_PREFIX_THUMBNAIL.$this->aReport['resultFID'].'.'.$this->aReport['resultFExt']);
			}
			if ($this->aReport['resultFFileType'] == 2)
			{
				\Clfunc_Aws::deleteFile($this->sAwsSavePath,CL_PREFIX_ENCODE.$this->aReport['resultFID'].CL_AWS_ENCEXT);
				\Clfunc_Aws::deleteFile($this->sAwsSavePath,CL_PREFIX_THUMBNAIL.$this->aReport['resultFID'].'-00001.png');
			}
			\Model_File::deleteFile($this->aReport['resultFID']);
		}

		Session::set('SES_T_NOTICE_MSG',__('レポートテーマ情報を更新しました。').'['.$aInput['r_name'].']');
		Response::redirect(DS.$this->bn);
	}

	public function action_delete($sID = null)
	{
		$sImgPath = CL_UPPATH.DS.$sID;
		$aReport = null;
		$aPut = null;
		$aChk = self::ReportChecker($sID);
		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}
		$result = Model_Report::getReportPut(array(array('rp.rbID','=',$sID)));
		if (count($result))
		{
			$aPut = $result->as_array();
		}


		try
		{
			$result = Model_Report::deleteReport($sID,$this->aReport,$aPut);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		Session::set('SES_T_NOTICE_MSG',__('レポートテーマを削除しました。'));
		Response::redirect(DS.$this->bn);
	}

	public function action_put($sID = null)
	{
		$aChk = self::ReportChecker($sID);
		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
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
		$result = Model_Student::getStudentFromClass($this->aClass['ctID'],null,null,array('st.stNO'=>'asc'),$aWords);
		if (count($result))
		{
			$aRes = $result->as_array('stID');
			foreach ($aRes as $sStID => $aS)
			{
				$aStudent[$sStID]['stu'] = $aS;
			}
		}
		$result = Model_Report::getReportPut(array(array('rp.rbID','=',$sID)));
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
		$sTitle = $this->aReport['rbTitle'];
		$sTitle .= '｜'.__('提出状況');
		$this->template->set_global('pagetitle',$sTitle);

		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/report','name'=>__('レポート'));
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		$aSearchForm = array(
			'url' => DS.$this->bn.'/put/'.$sID,
		);
		$this->template->set_global('aSearchForm',$aSearchForm);
		$this->template->set_global('sWords',$sWords);

		# カスタムメニュー
		$aCustomMenu = array(
			array(
				'url'  => '#',
				'name' => __('チェックした学生に連絡'),
				'show' => 1,
				'icon' => 'fa-envelope',
				'option' => array(
					'class' => 'checkStudentMail',
				),
			),
			array(
				'url'  => DS.$this->bn.'/archive/'.$sID,
				'name' => __('提出ファイルアーカイブの作成'),
				'show' => 0,
				'icon' => 'fa-archive',
			)
		);
		$this->template->set_global('aCustomMenu',$aCustomMenu);

		# カスタムボタン
		$aCustomBtn = null;
		if ($this->aReport['zipFID'])
		{
			$aCustomBtn = array(
				array(
					'url' => \Uri::create('getfile/s3file/:fid/:mode',array('fid'=>$this->aReport['zipFID'],'mode'=>'e')),
					'name' => __('アーカイブファイルのダウンロード').' ('.ClFunc_Tz::tz('Y/m/d H:i',$this->tz,$this->aReport['zipFDate']).' '.\Clfunc_Common::FilesizeFormat($this->aReport['zipFSize'],1).')',
					'show' => 0,
					'icon' => 'fa-download',
					'option' => array(
						'id' => 'archive-download-btn',
						'obj' => $this->aReport['rbID'],
					),
				),
			);
		}
		if ($this->aReport['zipProgress'] == 1)
		{
			$aCustomBtn = array(
				array(
					'url' => '#',
					'name' => __('アーカイブファイルを作成中…'),
					'show' => 0,
					'icon' => 'fa-spinner fa-spin',
					'option' => array(
						'id' => 'archive-download-btn',
						'obj' => $this->aReport['rbID'],
						'disabled' => 'disabled',
					),
				),
			);
		}
		if ($this->aReport['zipProgress'] == 2)
		{
			$aCustomBtn = array(
				array(
					'url' => '#',
					'name' => __('アーカイブファイルの作成失敗'),
					'show' => 0,
					'icon' => 'fa-exclamation-triangle',
					'option' => array(
						'id' => 'archive-download-btn',
						'obj' => $this->aReport['rbID'],
						'disabled' => 'disabled',
					),
				),
			);
		}



		$this->template->set_global('aCustomBtn',$aCustomBtn);

		$this->template->content = View::forge($this->bn.'/put');
		$this->template->content->set('aReport',$this->aReport);
		$this->template->content->set('aStudent',$aStudent);
		$this->template->javascript = array('cl.t.report.js');
		return $this->template;
	}

	public function action_detail($sRbID = null, $sStID = null, $sTab = 'd')
	{
		$sName = null;
		$aAns = null;
		$aStudent = null;

		$result = Model_Student::getStudentFromClass($this->aClass['ctID'],array(array('sp.stID','=',$sStID)));
		if (count($result))
		{
			$aStudent = $result->current();
			$sName = $aStudent['stName'];
		}

		$aChk = self::ReportPutChecker($sRbID,$sStID);
		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}
		if (is_null($this->aPut))
		{
			$aInsert = array(
				'rbID' => $sRbID,
				'stID' => $sStID,
				'rpDate' => CL_DATETIME_DEFAULT,
				'rpstName'=>$aStudent['stName'],
				'rpstNO'=>$aStudent['stNO'],
				'rpstClass'=>$aStudent['stClass'],
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

		$aCount = null;
		$result = \Model_Report::getReportCount(array(array('rc.rbID','=',$sRbID),array('rc.stID','=',$sStID)));
		if (count($result))
		{
			$aCount = $result->current();
		}

		# タイトル
		$sTitle = $this->aReport['rbTitle'];
		$sTitle .= '｜'.$sName;
		$this->template->set_global('pagetitle',$sTitle);

		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/report','name'=>__('レポート'));
		$this->aBread[] = array('link'=>'/report/put/'.$sRbID,'name'=>__('提出状況'));
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		# 出力設定
		$this->template->content = View::forge($this->bn.'/detail');
		$this->template->content->set('aReport',$this->aReport);
		$this->template->content->set('aStudent',$aStudent);
		$this->template->content->set('aPut',$this->aPut);
		$this->template->content->set('aCnt',$aCnt);
		$this->template->content->set('aParents',$aParents);
		$this->template->content->set('aComments',$aComments);
		$this->template->content->set('aCount',$aCount);
		$this->template->content->set('sTab',$sTab);
		$this->template->javascript = array('cl.t.report.js','cl.report.js');

		if (!Input::post(null,false))
		{
			$aInput = null;
			$aTemp = Session::get('SES_T_REPORT_RATE_'.$sRbID.$sStID,false);
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
					if ($this->aPut['rID'.$i])
					{
						try
						{
							$sFileName = $this->aPut['rID'.$i].'.'.$this->aPut['rExt'.$i];
							$sTempPath = $this->sTempFilePath.DS.'_report_'.$sFileName;
							$result = \Clfunc_Aws::getFile($this->aPut['rPath'.$i],$sFileName,$sTempPath);
							$aFile = array(
								'file'  => '_report_'.$sFileName,
								'name'  => $this->aPut['rName'.$i],
								'size'  => $this->aPut['rSize'.$i],
								'isimg' => ($this->aPut['rFileType'.$i] == 1)? 1:0,
							);
						}
						catch (Exception $e)
						{
							$aFile = false;
						}
					}
					if ($aFile)
					{
						$aInput['r'.$i] = serialize($aFile);
						$aInput['rinfo'.$i] = $aFile;
					}
				}
			}

			$this->template->content->set('aInput',$aInput);
			return $this->template;
		}

		$aInput = Input::post(null,false);

		for ($i = 1; $i <= 3; $i++)
		{
			if ($aInput['r'.$i] != '')
			{
				$aInput['rinfo'.$i] = unserialize($aInput['r'.$i]);
			}
		}

		$aInput['rbID'] = $sRbID;
		Session::set('SES_T_REPORT_RATE_'.$sRbID.$sStID, serialize($aInput));

		// 登録データ生成
		for ($i = 1; $i <= 3; $i++)
		{
			$fID = null;
			if ($aInput['r'.$i] != '')
			{
				$sSourseFile = $this->sTempFilePath.DS.$aInput['rinfo'.$i]['file'];
				$sThumbFile = $this->sTempFilePath.DS.CL_PREFIX_THUMBNAIL.$aInput['rinfo'.$i]['file'];
				$sContentType = \Clfunc_Common::GetContentType($sSourseFile);
				$sExt = pathinfo($sSourseFile,PATHINFO_EXTENSION);
				$iFileType = \Clfunc_Common::GetFileType($sSourseFile);

				$fID = \Model_File::getFileID();
				$sFile = $fID.'.'.$sExt;

				# 登録情報作成
				$aInsert = array(
					'fID'          => $fID,
					'fName'        => $aInput['rinfo'.$i]['name'],
					'fSize'        => $aInput['rinfo'.$i]['size'],
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
						$result = \Clfunc_Aws::encodeMovie($this->sAwsSavePath, $fID, $sExt);
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
						\Clfunc_Aws::deleteFile($this->sAwsSavePath,CL_PREFIX_ENCODE.$fID.CL_AWS_ENCEXT);
						\Clfunc_Aws::deleteFile($this->sAwsSavePath,CL_PREFIX_THUMBNAIL.$fID.'-00001.png');
					}
					\Clfunc_Common::LogOut($e,__CLASS__);
					Session::set('SES_T_ERROR_MSG',__('指定した提出ファイルが保存できませんでした。').'('.$i.')'.$e->getMessage());
					Response::redirect($sBackURL);
				}
			}
			$aInput['rID'.$i] = $fID;
		}

		$aUpdate = array(
			'rpScore' => $aInput['rpScore'],
			'rID1' => $aInput['rID1'],
			'rID2' => $aInput['rID2'],
			'rID3' => $aInput['rID3'],
			'rpComment' => $aInput['rpComment'],
			'rpRateDate' => date('YmdHis'),
		);

		try
		{
			$result = Model_Report::updatePut($aUpdate,$this->aPut);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		for($i = 1; $i <= 3; $i++)
		{
			if ($this->aPut['rID'.$i])
			{
				\Clfunc_Aws::deleteFile($this->aPut['rPath'.$i],$this->aPut['rID'.$i].'.'.$this->aPut['rExt'.$i]);
				if ($this->aPut['rFileType'.$i] == 1)
				{
					\Clfunc_Aws::deleteFile($this->sAwsSavePath, CL_PREFIX_THUMBNAIL.$this->aPut['rID'.$i].'.'.$this->aPut['rExt'.$i]);
				}
				if ($this->aPut['rFileType'.$i] == 2)
				{
					\Clfunc_Aws::deleteFile($this->sAwsSavePath,CL_PREFIX_ENCODE.$this->aPut['rID'.$i].CL_AWS_ENCEXT);
					\Clfunc_Aws::deleteFile($this->sAwsSavePath,CL_PREFIX_THUMBNAIL.$this->aPut['rID'.$i].'-00001.png');
				}
				\Model_File::deleteFile($this->aPut['rID'.$i]);
			}
		}

		for($i = 1; $i <= 3; $i++)
		{
			if (isset($aInput['rinfo'.$i]))
			{
				$sSourseFile = $this->sTempFilePath.DS.$aInput['rinfo'.$i]['file'];
				$sThumbFile = $this->sTempFilePath.DS.CL_PREFIX_THUMBNAIL.$aInput['rinfo'.$i]['file'];
				@unlink($sSourseFile);
				@unlink($sThumbFile);
			}
		}

		Session::delete('SES_T_REPORT_RATE_'.$sRbID.$sStID);
		Session::set('SES_T_NOTICE_MSG',__('評価しました。').'['.$sName.']');
		Response::redirect(DS.$this->bn.'/put/'.$sRbID);
	}

	public function action_submit($sRbID = null, $sStID = null, $mode = null)
	{
		$sBack = DS.$this->bn.'/put/'.$sRbID;

		$aChk = self::ReportPutChecker($sRbID,$sStID);
		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}
		$result = Model_Student::getStudentFromClass($this->aClass['ctID'],array(array('sp.stID','=',$sStID)));
		if (!count($result))
		{
			Session::set('SES_T_ERROR_MSG',__('学生の情報が確認できませんでした。'));
			Response::redirect($sBack);
		}
		$aStudent = $result->current();

		if ($mode == 'd')
		{
			if (is_null($this->aPut))
			{
				Session::set('SES_T_ERROR_MSG',__('レポートの提出情報が見つかりませんでした。'));
				Response::redirect($sBack);
			}

			$aUpdate = array(
				'rpTeachPut' => 0,
			);
			try
			{
				$result = Model_Report::updatePut($aUpdate,$this->aPut);
				$result = Model_Report::updateReportPutNum($sRbID, -1);
			}
			catch (Exception $e)
			{
				\Clfunc_Common::LogOut($e,__CLASS__);
				Session::set('SES_T_ERROR_MSG',$e->getMessage());
				Response::redirect($this->eRedirect);
			}

			Session::set('SES_T_NOTICE_MSG',__('レポートの提出を取り消しました。').'['.$aStudent['stName'].']');
			Response::redirect($sBack);
		}

		if (is_null($this->aPut))
		{
			$aInsert = array(
				'rbID' => $sRbID,
				'stID' => $sStID,
				'rpDate' => CL_DATETIME_DEFAULT,
				'rpTeachPut' => 1,
				'rpstName'=>$aStudent['stName'],
				'rpstNO'=>$aStudent['stNO'],
				'rpstClass'=>$aStudent['stClass'],
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
		}
		else
		{
			$aUpdate = array(
				'rpDate' => CL_DATETIME_DEFAULT,
				'rpTeachPut' => 1,
				'rpstName'=>$aStudent['stName'],
				'rpstNO'=>$aStudent['stNO'],
				'rpstClass'=>$aStudent['stClass'],
			);
			try
			{
				$result = Model_Report::updatePut($aUpdate,$this->aPut);
				$result = Model_Report::updateReportPutNum($sRbID, 1);
			}
			catch (Exception $e)
			{
				\Clfunc_Common::LogOut($e,__CLASS__);
				Session::set('SES_T_ERROR_MSG',$e->getMessage());
				Response::redirect($this->eRedirect);
			}
		}
		Session::set('SES_T_NOTICE_MSG',__('レポートを提出済みにしました。').'['.$aStudent['stName'].']');
		Response::redirect($sBack);
	}


	public function action_ratesetting()
	{
		# タイトル
		$sTitle = __('レポート評価値の設定');

		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/report','name'=>__('レポート'));
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);
		$this->template->set_global('pagetitle',$sTitle);

		$aInput = null;
		$aMsg = null;
		if (Input::post(null,false))
		{
			$bNone = true;
			for ($i = 1; $i <= 10; $i++)
			{
				$sN = Input::post('name'.$i, false);
				if ($sN)
				{
					$aInput[$i]['rrName'] = Input::post('name'.$i);
					$bNone = false;
					if (!\Clfunc_Common::stringValidation($sN,array(0,5)))
					{
						$aMsg[$i] = __(':num文字以内で入力してください。',array('num'=>5));
					}
				}
			}

			if (!$bNone && is_null($aMsg))
			{
				try
				{
					$result = Model_Report::updateRateMaster($this->aClass['ctID'],$aInput);
				}
				catch (Exception $e)
				{
					\Clfunc_Common::LogOut($e,__CLASS__);
					Session::set('SES_T_ERROR_MSG',$e->getMessage());
					Response::redirect($this->eRedirect);
				}
				Session::set('SES_T_NOTICE_MSG',__('レポート評価値を設定しました。'));
				Response::redirect(DS.$this->bn);
			}
			else
			{
				$aMsg['default'] = __('評価値を一つ以上設定してください。');
			}
		}

		if (is_null($aInput))
		{
			$aInput = $this->aRateM;
		}

		$this->template->content = View::forge($this->bn.'/ratesetting');
		$this->template->content->set('aInput',$aInput);
		$this->template->content->set('aMsg',$aMsg);
		return $this->template;
	}

	public function action_archive($sID = null)
	{
		$aChk = self::ReportChecker($sID);
		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}

		$result = Model_Report::getReportPut(array(array('rp.rbID','=',$sID)));
		$bFile = false;
		if (count($result))
		{
			$aRes = $result->as_array('stID');
			foreach ($aRes as $sStID => $aP)
			{
				for($i = 1; $i <= 3; $i++)
				{
					if ($aP['fID'.$i])
					{
						$bFile = true;
						break;
					}
				}
			}
		}

		if (!$bFile)
		{
			Session::set('SES_T_ERROR_MSG',__('提出ファイルが1件もないため、アーカイブ作成はできません。'));
		}
		else
		{
			shell_exec('/usr/bin/php '.CL_OILPATH.' r execreportarchive '.$sID.' '.$this->aTeacher['ttID'].' > /dev/null 2>&1 &');
			Session::set('SES_T_NOTICE_MSG',__('提出ファイルのアーカイブ作成を開始しました。\nアーカイブ作成には時間がかかる場合があります。\n作成が完了すると、この画面上にダウンロードボタンが表示されます。'));
		}
		Response::redirect(DS.$this->bn.'/put/'.$sID);
	}

	public function action_putlist()
	{
		$aReport = null;
		$aRbIDs = null;
		$result = Model_Report::getReportBase(array(array('rb.ctID','=',$this->aClass['ctID'])),null,array('rb.rbSort'=>'desc'));
		if (count($result))
		{
			foreach ($result as $aQ)
			{
				$aReport[] = $aQ;
				$aRbIDs[] = $aQ['rbID'];
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
		if (!is_null($aRbIDs))
		{
			$aWhere[] = array('rp.rbID','IN',$aRbIDs);
		}
		if (!is_null($aStIDs))
		{
			$aWhere[] = array('rp.stID','IN',$aStIDs);
		}

		$result = Model_Report::getReportPut($aWhere);
		if (count($result))
		{
			foreach ($result as $aP)
			{
				$sStID = $aP['stID'];
				$sRbID = $aP['rbID'];
				if (isset($aStudent[$sStID]))
				{
					$aStudent[$sStID]['put'][$sRbID] = $aP;
				}
			}
		}

		# タイトル
		$sTitle = __('提出一覧');
		$this->template->set_global('pagetitle',$sTitle);
		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/report','name'=>__('レポート'));
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		# カスタムボタン
		$aCustomBtn = array(
		array(
		'url'  => '/t/output/reportputlist.csv',
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
		$this->template->content->set('aReport',$aReport);
		$this->template->content->set('aStudent',$aStudent);
		$this->template->javascript = array('cl.t.report.js');
		return $this->template;
	}

	private function ReportChecker($sRbID = null)
	{
		if (is_null($sRbID))
		{
			return array('msg'=>__('レポート情報が送信されていません。'),'url'=>DS.$this->bn);
		}
		$result = Model_Report::getReportBase(array(array('rb.rbID','=',$sRbID)));
		if (!count($result))
		{
			return array('msg'=>__('指定されたレポートが見つかりません。'),'url'=>DS.$this->bn);
		}
		$this->aReport = $result->current();

		return true;
	}

	private function ReportPutChecker($sRbID,$sStID = null)
	{
		self::ReportChecker($sRbID);

		$result = Model_Report::getReportPut(array(array('rp.rbID','=',$sRbID),array('rp.stID','=',$sStID)));
		if (count($result))
		{
			$this->aPut = $result->current();
		}

		return true;
	}

}