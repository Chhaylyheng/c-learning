<?php
class Controller_Org_Class extends Controller_Org_Base
{
	private $bn = 'org/class';

	private $aClasses = null;
	private $aClassBase = array(
		'c_code'=>null,
		'c_name'=>null,
		'c_year'=>null,
		'c_period'=>null,
		'c_weekday'=>null,
		'c_hour'=>null
	);


	public function action_index($sTID = null)
	{
		$sTitle = __('講義一覧');
		$aBreadCrumbs = array();
		$aMasters = null;

		$aWhere = array(
			array('gtID','=',$this->aGroup['gtID'])
		);
		if (!is_null($sTID))
		{
			$aWhere[] = array('ttID','=',$sTID);

			$result = Model_Group::getGroupTeachers(array(array('gtp.gtID','=',$this->aGroup['gtID']),array('gtp.ttID','=',$sTID)));
			if (!count($result))
			{
				Response::redirect('/'.$this->bn);
			}
			$aTeacher = $result->current();

			$sTitle = __('講義一覧').'('.(($aTeacher['ttName'])? $aTeacher['ttName']:$aTeacher['ttMail']).')';
			$aBreadCrumbs[] = array('name' => __('先生一覧'), 'link' => '/teacher');

			$result = Model_Group::getGroupTeachersClasses(array(array('gc.gtID','=',$this->aGroup['gtID']),array('tp.ttID','=',$sTID)),null,array('ct.ctCode'=>'asc'));
			if (count($result))
			{
				$this->aClasses = $result->as_array();
			}
			$result = Model_Group::getGroupTeachersClasses(array(array('gc.gtID','=',$this->aGroup['gtID']),array('tp.tpMaster','=',1)));
			if (count($result))
			{
				$aMasters = $result->as_array('ctID');
			}
		}
		else
		{
			$result = Model_Group::getGroupClasses($aWhere,null,array('ctCode'=>'asc'));
			if (count($result))
			{
				$this->aClasses = $result->as_array();
			}
			$result = Model_Group::getGroupTeachersClasses(array(array('gc.gtID','=',$this->aGroup['gtID']),array('tp.tpMaster','=',1)));
			if (count($result))
			{
				$aMasters = $result->as_array('ctID');
			}
		}

		# カスタムボタン
		$aCustomMenu = array(
			array(
				'url'  => DS.$this->bn.DS.'add',
				'name' => __('講義の新規作成'),
			),
			array(
				'url'  => DS.$this->bn.DS.'csv',
				'name' => __('CSVから講義の登録'),
			),
			array(
				'url'  => DS.$this->bn.DS.'csvteach',
				'name' => __('CSVから担当の登録'),
			),
			array(
				'url'  => '/org/output/classlist.csv',
				'name' => __('一覧のCSVダウンロード'),
				'icon' => 'fa-download',
				'option' => array(
					'target' => '_blank',
				)
			),
			array(
				'url'  => '/org/output/inchargelist.csv',
				'name' => __('担当情報のCSVダウンロード'),
				'icon' => 'fa-download',
				'option' => array(
					'target' => '_blank',
				)
			),
		);
		$this->template->set_global('aCustomMenu',$aCustomMenu);

		# チェックドロップダウン
		$aCheckDrop = array(
			'option' => 'class',
			'name' => __('チェックした講義に対する操作'),
			'list' => array(
				array(
				'url' => '#',
				'class' => 'CheckDelete',
				'name' => __('削除する'),
				),
			),
		);
		$this->template->set_global('aCheckDrop',$aCheckDrop);

		# パンくずリスト生成
		$aBreadCrumbs[] = array('name' => $sTitle);
		$this->template->set_global('breadcrumbs',$aBreadCrumbs);
		# ページタイトル生成
		$this->template->set_global('pagetitle',$sTitle);

		$this->template->content = View::forge($this->bn.DS.'index');
		$this->template->content->set('aClasses',$this->aClasses);
		$this->template->content->set('aMasters',$aMasters);
		$this->template->javascript = array('cl.org.class.js');
		return $this->template;
	}

	public function action_add()
	{
		# タイトル
		$sTitle = '講義の新規登録';
		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/class','name'=>'講義一覧');
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);
		$this->template->set_global('pagetitle',$sTitle);

		$aWeek = $this->aWeekday;
		$aWeek[0] = __('指定なし');
		ksort($aWeek);
		$iY = (date('n') <= 3)? date('Y',strtotime('-1 year')):date('Y');
		$aYear = Clfunc_Common::YearList($iY);

		$aPeriod = $this->aPeriod;
		$aHour = $this->aHour;

		$this->template->set_global('weekdaylist',$aWeek);
		$this->template->set_global('periodlist',$aPeriod);
		$this->template->set_global('hourlist',$aHour);
		$this->template->set_global('yearlist',$aYear);

		if (!Input::post(null,false))
		{
			$data = $this->aClassBase;
			$data['c_year'] = $iY;
			$data['error'] = null;
			$this->template->content = View::forge($this->bn.DS.'edit',$data);
			return $this->template;
		}

		$aInput = Input::post();
		$val = Validation::forge();
		$val->add_callable('Helper_CustomValidation');

		$val->add('c_name', __('講義名'))
			->add_rule('required')
			->add_rule('trim')
			->add_rule('max_length',30);

		$val->add('c_code', __('講義コード'))
			->add_rule('trim')
			->add_rule('max_length',20)
			->add_rule('valid_string', array('alpha','numeric','dashes','utf8'))
			->add_rule('class_code',array(''));

		if (!$val->run())
		{
			$aInput['error'] = $val->error();
			$this->template->content = View::forge($this->bn.DS.'edit',$aInput);
			return $this->template;
		}

		// 登録データ生成
		$aInsert['class'] = array(
			'ctCode'     => ($aInput['c_code'])? $aInput['c_code']:null,
			'ctID'       => null,
			'ctName'     => $aInput['c_name'],
			'ctYear'     => $aInput['c_year'],
			'dpNO'       => $aInput['c_period'],
			'ctWeekday'  => $aInput['c_weekday'],
			'dhNO'       => $aInput['c_hour'],
			'ctStatus'   => 1,
			'ctDate'     => date('YmdHis'),
		);
		$aInsert['group'] = array(
			'gtID' => $this->aGroup['gtID'],
			'gpDate' => date('YmdHis'),
		);

		try
		{
			$sCtID = Model_Class::insertOrgClass($aInsert);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_ORG_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		Session::set('SES_ORG_NOTICE_MSG','講義を作成しました。続けて担当の先生を設定してください。');
		Response::redirect('/org/teacher/classlist/'.$sCtID);
	}

	public function action_edit($sCtID = null)
	{
		if (is_null($sCtID))
		{
			Session::set('SES_ORG_ERROR_MSG', '講義が指定されていません。');
			Response::redirect(DS.$this->bn);
		}
		$result = Model_Group::getGroupClasses(array(array('ctID','=',$sCtID),array('gtID','=',$this->aGroup['gtID'])));
		if (!count($result))
		{
			Session::set('SES_ORG_ERROR_MSG', '対象の講義情報が見つかりません。');
			Response::redirect(DS.$this->bn);
		}
		$aClass = $result->current();
		$this->template->set_global('aClass',$aClass);

		$iY = (date('n') <= 3)? date('Y',strtotime('-1 year')):date('Y');
		$iY = ((int)$aClass['ctYear'] > $iY)? $iY:(int)$aClass['ctYear'];
		$aYear = Clfunc_Common::YearList($iY);

		$aWeek = $this->aWeekday;
		$aWeek[0] = __('指定なし');
		ksort($aWeek);

		$aPeriod = $this->aPeriod;
		$aHour = $this->aHour;

		$this->template->set_global('weekdaylist',$aWeek);
		$this->template->set_global('periodlist',$aPeriod);
		$this->template->set_global('hourlist',$aHour);
		$this->template->set_global('yearlist',$aYear);

		$aInput = array(
			'c_code'    => $aClass['ctCode'],
			'c_name'    => $aClass['ctName'],
			'c_year'    => $aClass['ctYear'],
			'c_period'  => $aClass['dpNO'],
			'c_weekday' => $aClass['ctWeekDay'],
			'c_hour'    => $aClass['dhNO'],
			'error'     => null,
		);

		# タイトル
		$sTitle = '講義情報の編集';
		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/class','name'=>'講義一覧');
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

		$val->add('c_name', __('講義名'))
			->add_rule('required')
			->add_rule('trim')
			->add_rule('max_length',30);

		$val->add('c_code', __('講義コード'))
			->add_rule('required')
			->add_rule('trim')
			->add_rule('max_length',20)
			->add_rule('valid_string', array('alpha','numeric','dashes','utf8'))
			->add_rule('class_code',array($aClass['ctCode']));

		if (!$val->run())
		{
			$aInput['error'] = $val->error();
			$this->template->content = View::forge($this->bn.DS.'edit',$aInput);
			return $this->template;
		}

		// 登録データ生成
		$aInsert = array(
			'ctCode'     => $aInput['c_code'],
			'ctName'     => $aInput['c_name'],
			'ctYear'     => $aInput['c_year'],
			'dpNO'       => $aInput['c_period'],
			'ctWeekday'  => $aInput['c_weekday'],
			'dhNO'       => $aInput['c_hour'],
		);
		try
		{
			$result = Model_Class::updateClass($aInsert,$aClass);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_ORG_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		Session::set('SES_ORG_NOTICE_MSG','講義「'.$aInsert['ctName'].'」の情報を更新しました。');

		Response::redirect(DS.$this->bn);
	}

	public function action_csv()
	{
		# タイトル
		$sTitle = __('CSVから講義の登録');
		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/class','name'=>__('講義一覧'));
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
			$st_csv = Upload::get_errors('ct_csv');
			if ($st_csv['errors'])
			{
				switch ($st_csv['errors'][0]['error'])
				{
					case Upload::UPLOAD_ERR_INI_SIZE:
					case Upload::UPLOAD_ERR_FORM_SIZE:
					case Upload::UPLOAD_ERR_MAX_SIZE:
						$aMsg['ct_csv'] = __('指定できるファイルのサイズは:sizeMBまでです。',array('size'=>CL_FILESIZE));
						break;
					case Upload::UPLOAD_ERR_EXTENSION:
					case Upload::UPLOAD_ERR_EXT_BLACKLISTED:
					case Upload::UPLOAD_ERR_EXT_NOT_WHITELISTED:
					case Upload::UPLOAD_ERR_TYPE_BLACKLISTED:
					case Upload::UPLOAD_ERR_TYPE_NOT_WHITELISTED:
					case Upload::UPLOAD_ERR_MIME_BLACKLISTED:
					case Upload::UPLOAD_ERR_MIME_NOT_WHITELISTED:
						$aMsg['ct_csv'] = __('登録できるファイルの拡張子は txt,csv のみです。');
						break;
					default:
						$aMsg['ct_csv'] = __('ファイルアップロードに失敗しました。');
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

		$oFile = Upload::get_files('ct_csv');
		$aCSV = ClFunc_Common::getCSV($oFile['file']);
		if (!count($aCSV))
		{
			$data['error'] = array('ct_csv'=>__('登録するデータがCSVから取得できませんでした。'));
			$this->template->content = View::forge($this->bn.DS.'csv',$data);
			return $this->template;
		}
		if ($aCSV[0][0] == '講義コード' || $aCSV[0][0] == 'Code')
		{
			array_shift($aCSV);
		}

		$aInsert = null;
		$aUpdate = null;

		$aCodes = array();
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

				# 講義コード
				$i = 0;
				if (!ClFunc_Common::stringValidation($aS[$i],false,array(0,20),array('alpha','numeric','dashes')))
				{
					$aMsg[] = __(':no件目の講義コード（:value）は、1文字以上20文字以下で半角大小英数字と一部記号【_（アンダースコア）-（ハイフン）】で入力してください。',array('no'=>($iKey + 1),'value'=>htmlspecialchars($aS[$i])));
				}

				# 講義名
				$i++;
				if (!ClFunc_Common::stringValidation($aS[$i],true,array(0,50)))
				{
					$aMsg[] = __(':no件目の講義名（:value）は、50文字以下で入力してください。',array('no'=>($iKey + 1),'value'=>htmlspecialchars($aS[$i])));
				}

				# 年度
				$i++;
				if (!ClFunc_Common::stringValidation($aS[$i],false,array(4,4),array('numeric')))
				{
					$aMsg[] = __(':no件目の年度（:value）は、4桁の数字で入力してください。',array('no'=>($iKey + 1),'value'=>htmlspecialchars($aS[$i])));
				}

				# 期
				$i++;
				if (isset($aS[$i]))
				{
					$iSet = array_search($aS[$i], $this->aPeriod);
					if ($iSet !== false)
					{
						$aS[$i] = $iSet;
					}
					else
					{
						$aS[$i] = 0;
					}
				}
				else
				{
					$aS[$i] = 0;
				}

				# 曜日
				$i++;
				if (isset($aS[$i]))
				{
					$iSet = array_search($aS[$i], $this->aWeekday);
					if ($iSet !== false)
					{
						$aS[$i] = $iSet;
					}
					else
					{
						$aS[$i] = 0;
					}
				}
				else
				{
					$aS[$i] = 0;
				}

				# 時限
				$i++;
				if (isset($aS[$i]))
				{
					if ((int)$aS[$i] < 0 || (int)$aS[$i] > 7)
					{
						$aS[$i] = 0;
					}
				}
				else
				{
					$aS[$i] = 0;
				}

				# 実施状況
				$i++;
				if (isset($aS[$i]))
				{
					if ($aS[$i] == 1)
					{
						$aS[$i] = 1;
					}
					else
					{
						$aS[$i] = 0;
					}
				}
				else
				{
					$aS[$i] = 0;
				}

				if ($aS[0] != '')
				{
					$result = Model_Group::getGroupClasses(array(array('ctCode','=',$aS[0]),array('gtID','=',$this->aGroup['gtID'])));
					if (count($result))
					{
						$aTemp = $result->current();
						$aUpdate[$aTemp['ctID']] = $aS;
					}
					else
					{
						$result = Model_Class::getClassFromCode($aS[0]);
						if (count($result))
						{
							$aMsg[] = __(':no件目の講義コード（:value）は、利用できません。別の講義コードに変更してください。',array('no'=>($iKey + 1),'value'=>htmlspecialchars($aS[0])));
						}
						else
						{
							if (!array_search($aS[0], $aCodes))
							{
								$aInsert[] = $aS;
								$aCodes[] = $aS[0];
							}
							else
							{
								$aMsg[] = __(':no件目の講義コード（:value）は、入力CSV内で既に指定されています。別の講義コードを指定してください。',array('no'=>($iKey + 1),'value'=>htmlspecialchars($aS[0])));
							}
						}
					}
				}
				else
				{
					$aInsert[] = $aS;
				}
			}
		}
		else
		{
			$data['error'] = array('ct_csv'=>__('登録するデータがありません。'));
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
				$result = Model_Class::insertOrgClassFromCSV($aInsert,$this->aGroup);
			}
			if (!is_null($aUpdate))
			{
				$result = Model_Class::updateOrgClassFromCSV($aUpdate,$this->aGroup);
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

	public function action_csvteach()
	{
		$sView = 'csvteach';

		# タイトル
		$sTitle = 'CSVから担当の登録';
		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/class','name'=>'講義一覧');
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
			$st_csv = Upload::get_errors('te_csv');
			if ($st_csv['errors'])
			{
				switch ($st_csv['errors'][0]['error'])
				{
					case Upload::UPLOAD_ERR_INI_SIZE:
					case Upload::UPLOAD_ERR_FORM_SIZE:
					case Upload::UPLOAD_ERR_MAX_SIZE:
						$aMsg['te_csv'] = __('指定できるファイルのサイズは:sizeMBまでです。',array('size'=>CL_FILESIZE));
						break;
					case Upload::UPLOAD_ERR_EXTENSION:
					case Upload::UPLOAD_ERR_EXT_BLACKLISTED:
					case Upload::UPLOAD_ERR_EXT_NOT_WHITELISTED:
					case Upload::UPLOAD_ERR_TYPE_BLACKLISTED:
					case Upload::UPLOAD_ERR_TYPE_NOT_WHITELISTED:
					case Upload::UPLOAD_ERR_MIME_BLACKLISTED:
					case Upload::UPLOAD_ERR_MIME_NOT_WHITELISTED:
						$aMsg['te_csv'] = __('登録できるファイルの拡張子は txt,csv のみです。');
						break;
					default:
						$aMsg['te_csv'] = __('ファイルアップロードに失敗しました。');
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

		$oFile = Upload::get_files('te_csv');
		$aCSV = ClFunc_Common::getCSV($oFile['file']);
		if (!count($aCSV))
		{
			$data['error'] = array('te_csv'=>__('登録するデータがCSVから取得できませんでした。'));
			$this->template->content = View::forge($this->bn.DS.$sView,$data);
			return $this->template;
		}
		if ($aCSV[0][0] == '講義コード' || $aCSV[0][0] == 'Code')
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
				$result = Model_Group::getGroupClasses(array(array('ctCode','=',$aS[$i]),array('gtID','=',$this->aGroup['gtID'])));
				if (!count($result))
				{
					$aMsg[] = __(':no件目の講義コード（:value）が存在しません。',array('no'=>($iKey + 1),'value'=>htmlspecialchars($aS[$i])));
				}
				else
				{
					$aClass = $result->current();
					$aInsert[$iKey][$i] = $aClass['ctID'];
				}

				# メールアドレス
				$i++;
				if (!$aS[$i] || !filter_var($aS[$i], FILTER_VALIDATE_EMAIL))
				{
					$aMsg[] = __(':no件目のメールアドレス（:value）は、メールアドレスとして正しく入力されていません。',array('no'=>($iKey + 1),'value'=>htmlspecialchars($aS[$i])));
				}
				$result = Model_Group::getGroupTeachers(array(array('tv.ttMail','=',$aS[$i]),array('gtp.gtID','=',$this->aGroup['gtID'])));
				if (!count($result))
				{
					$aMsg[] = __(':no件目のメールアドレス（:value）が存在しません。',array('no'=>($iKey + 1),'value'=>htmlspecialchars($aS[$i])));
				}
				else
				{
					$aTeach = $result->current();
					$aInsert[$iKey][$i] = $aTeach['ttID'];
				}

				# 主副
				$i++;
				if (isset($aS[$i]))
				{
					if ($aS[$i] == 1)
					{
						$aInsert[$iKey][$i] = 1;
					}
					else
					{
						$aInsert[$iKey][$i] = 0;
					}
				}
				else
				{
					$aInsert[$iKey][$i] = 0;
				}

			}
		}
		else
		{
			$data['error'] = array('te_csv'=>__('登録するデータがありません。'));
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
			$result = Model_Class::insertOrgClassTeachersFromCSV($aInsert);
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


	public function action_delete($sCtID = null)
	{
		if (is_null($sCtID))
		{
			Response::redirect('/'.$this->bn);
		}
		$result = Model_Group::getGroupClasses(array(array('ctID','=',$sCtID),array('gtID','=',$this->aGroup['gtID'])));
		if (!count($result))
		{
			Session::set('SES_ORG_ERROR_MSG', '対象の講義情報が見つかりません。');
			Response::redirect(DS.$this->bn);
		}
		$aClass = $result->current();

		try
		{
			$result = Model_Class::deleteClass(array($aClass['ctID']));
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_ORG_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		Session::set('SES_ORG_NOTICE_MSG','講義「'.$aClass['ctName'].'」を削除しました。');
		Response::redirect(DS.$this->bn);
	}

	public function post_modify()
	{
		$aInput = Input::post();

		if (!isset($aInput['ClassChk']) || !count($aInput['ClassChk']))
		{
			Session::set('SES_ORG_ERROR_MSG', __('講義がチェックされていません。'));
			Response::redirect(DS.$this->bn);
		}

		$result = Model_Group::getGroupClasses(array(array('ctID','IN',$aInput['ClassChk']),array('gtID','=',$this->aGroup['gtID'])));
		if (!count($result))
		{
			Session::set('SES_ORG_ERROR_MSG', __('対象の講義情報が見つかりません。'));
			Response::redirect(DS.$this->bn);
		}
		$aCtIDs = null;
		$sFin = null;
		foreach ($result as $aC)
		{
			$aCtIDs[] = $aC['ctID'];
			$sFin .= "\n".$aC['ctName'].' ['.$aC['ctCode'].']';
		}

		switch ($aInput['mode'])
		{
			case 'delete':
				try
				{
					$result = Model_Class::deleteClass($aCtIDs);
				}
				catch (Exception $e)
				{
					\Clfunc_Common::LogOut($e,__CLASS__);
					Session::set('SES_ORG_ERROR_MSG',$e->getMessage());
					Response::redirect($this->eRedirect);
				}

				Session::set('SES_ORG_NOTICE_MSG',__('下記の講義を削除しました。').'（'.count($aCtIDs).__('講義').'）'.$sFin);
				Response::redirect(DS.$this->bn);
				break;
			default:
				Session::set('SES_ORG_ERROR_MSG', __('操作の指定が誤っています。'));
				Response::redirect(DS.$this->bn);
				break;
		}

	}


	public function action_studentlist($sSID = null)
	{
		if (is_null($sSID))
		{
			Response::redirect('/'.$this->bn);
		}

		$aWhere = array(
			array('gsp.gtID','=',$this->aGroup['gtID']),
			array('gsp.stID','=',$sSID),
		);

		$result = Model_Group::getGroupStudents($aWhere);
		if (!count($result))
		{
			Response::redirect('/'.$this->bn);
		}
		$aStudent = $result->current();

		$aWhere = array(
			array('gtID','=',$this->aGroup['gtID']),
			array('stID','=',$sSID),
		);

		$result = Model_Group::getGroupStudentsClasses($aWhere,null,array('ctCode'=>'asc'));
		if (count($result))
		{
			$this->aClasses = $result->as_array();
		}

		$sTitle = $aStudent['stName'].'の履修講義一覧';
		$aBreadCrumbs = array();

		# パンくずリスト生成
		$aBreadCrumbs[] = array('name' => '学生一覧', 'link' => '/student');
		$aBreadCrumbs[] = array('name' => $sTitle);
		$this->template->set_global('breadcrumbs',$aBreadCrumbs);
		# ページタイトル生成
		$this->template->set_global('pagetitle',$sTitle);

		$this->template->content = View::forge($this->bn.DS.'studentlist');
		$this->template->content->set('aClasses',$this->aClasses);
		$this->template->javascript = array('cl.org.class.js');
		return $this->template;
	}

}


