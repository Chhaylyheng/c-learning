<?php
class Controller_T_Quest extends Controller_T_Baseclass
{
	private $bn = 't/quest';
	private $aQuestBase = array(
		'q_name'=>null,
		'q_auto_public'=>0, 'q_auto_s_date'=>null, 'q_auto_e_date'=>null, 'q_auto_s_time'=>null, 'q_auto_e_time'=>null,
		'q_select_style'=>1, 'q_select_sort'=>0,
		'q_open'=>0, 'q_retry'=>0, 'q_ans_public'=>0, 'q_com_public'=>0,
		'q_anonymous'=>0,
	);
	private $aSearchCol = array(
		'stLogin','stName','stNO','stDept','stSubject','stYear','stClass','stCourse'
	);

	public function action_index()
	{
		$aQSAL = null;
		while (is_null($aQSAL))
		{
			$result = Model_Class::getClassArchive($this->aClass['ctID'],'QuestStuAnsList');
			if (!count($result))
			{
				try
				{
					$aInsert = array(
						'ctID' => $this->aClass['ctID'],
						'caType' => 'QuestStuAnsList',
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
				$aQSAL = $result->current();
			}
		}

		$aQuest = null;
		$result = Model_Quest::getQuestBaseFromClass($this->aClass['ctID'],null,null,array('qb.qbSort'=>'desc'));
		if (count($result))
		{
			$aQuest = $result->as_array();
		}

		$aPut = null;
		$result = Model_Quest::getQuestPut(array(array('qb.ctID','=',$this->aClass['ctID']),array('qp.stID','=',$this->aTeacher['ttID'])));
		if (count($result))
		{
			$aPut = $result->as_array('qbID');
		}

		# タイトル
		$sTitle = __('アンケート');
		$this->template->set_global('pagetitle',$sTitle);

		# パンくずリスト生成
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		# カスタムメニュー
		$aCustomMenu = array(
			array(
				'url'  => DS.$this->bn.'/create/',
				'name' => __('アンケートの新規作成'),
				'show' => 1,
			),
			array(
				'url'  => DS.$this->bn.'/csv/',
				'name' => __('CSVからアンケートの登録'),
				'show' => 1,
			),
			array(
				'url'  => '/t/output/questputlist.csv',
				'name' => __('提出一覧CSVのダウンロード'),
				'show' => 0,
				'icon' => 'fa-download',
			),
			array(
				'url'  => DS.$this->bn.'/archive',
				'name' => __('回答一覧ファイルアーカイブの作成'),
				'show' => 0,
				'icon' => 'fa-archive',
			),
		);
		# カスタムボタン
		$aCustomBtn = array(
			array(
				'url'  => DS.$this->bn.'/create/',
				'name' => __('アンケートの新規作成'),
				'show' => 1,
			),
			array(
				'url'  => DS.$this->bn.'/putlist/',
				'name' => __('提出一覧'),
				'show' => 0,
			),
		);

		if ($aQSAL['caProgress'] == 2)
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
		else if ($aQSAL['caProgress'] == 1)
		{
			$aCustomBtn[] = array(
					'url' => '#',
					'name' => __('アーカイブファイルを作成中…'),
					'show' => 0,
					'icon' => 'fa-spinner fa-spin',
					'option' => array(
							'id' => 'archive-download-btn',
							'obj' => $this->aClass['ctID'].'_QuestStuAnsList',
							'disabled' => 'disabled',
					),
			);
		}
		else if ($aQSAL['fID'])
		{
			$aCustomBtn[] = array(
				'url' => \Uri::create('getfile/s3file/:fid/:mode',array('fid'=>$aQSAL['fID'],'mode'=>'e')),
				'name' => __('アーカイブファイルのダウンロード').' ('.ClFunc_Tz::tz('Y/m/d H:i',$this->tz,$aQSAL['fDate']).' '.\Clfunc_Common::FilesizeFormat($aQSAL['fSize'],1).')',
				'show' => 0,
				'icon' => 'fa-download',
				'option' => array(
					'id' => 'archive-download-btn',
					'obj' => $this->aClass['ctID'].'_QuestStuAnsList',
				),
			);
		}

		if (
			(!is_null($this->aAssistant) && !$this->aTeacher['gtID'] && $this->aTeacher['coTermDate'] < date('Y-m-d')) ||
			(CL_CAREERTASU_MODE && $this->aTeacher['ttCTPlan'] == 0)
		)
		{
			$aCustomMenu = null;
			$aCustomBtn = null;
		}
		else
		{
			$this->template->set_global('aCustomMenu',$aCustomMenu);
			$this->template->set_global('aCustomBtn',$aCustomBtn);
		}

		$this->template->content = View::forge('t/quest/index');
		$this->template->content->set('aQuest',$aQuest);
		$this->template->content->set('aPut',$aPut);
		$this->template->content->set('aQSAL',$aQSAL);
		$this->template->javascript = array('cl.t.quest.js');
		return $this->template;
	}

	public function action_create()
	{
		\Clfunc_Common::ContractDetect($this->aTeacher, __CLASS__);

		if (!$this->aClass['ctStatus'])
		{
			Session::set('SES_T_ERROR_MSG',__('終了した講義にはアンケートを新規作成することはできません。'));
			Response::redirect('/t/quest');
		}

		# タイトル
		$sTitle = __('アンケートの新規作成');
		$this->template->set_global('pagetitle',$sTitle);

		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/quest','name'=>__('アンケート'));
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		if (!Input::post(null,false))
		{
			$data = $this->aQuestBase;
			$data['q_auto_s_date'] = ClFunc_Tz::tz('Y/m/d',$this->tz);
			$data['q_auto_e_date'] = ClFunc_Tz::tz('Y/m/d',$this->tz);
			$data['q_auto_s_time'] = ClFunc_Tz::tz('H:i',$this->tz);
			$data['q_auto_e_time'] = ClFunc_Tz::tz('H:i',$this->tz);
			$data['error'] = null;
			$this->template->content = View::forge('t/quest/create',$data);
			$this->template->javascript = array('jquery.timepicker.js','cl.t.quest.js');
			return $this->template;
		}

		$aInput = Input::post();

		$val = Validation::forge();
		$val->add_callable('Helper_CustomValidation');
		$val->add_field('q_name', __('アンケートタイトル'), 'required|max_length['.CL_TITLE_LENGTH.']');
		if ($aInput['q_auto_public'])
		{
			$val->add_field('q_auto_s_time', __('開始日時'), 'required|time')
				->add_rule('min_time',ClFunc_Tz::tz('Y/m/d H:i',$this->tz),$aInput['q_auto_s_date']);
			$val->add_field('q_auto_e_time', __('終了日時'), 'required|time')
				->add_rule('min_time',$aInput['q_auto_s_date'].' '.$aInput['q_auto_s_time'],$aInput['q_auto_e_date']);
		}
		if (!$val->run())
		{
			$data = $aInput;
			$data['error'] = $val->error();
			$this->template->content = View::forge('t/quest/create',$data);
			$this->template->javascript = array('jquery.timepicker.js','cl.t.quest.js');
			return $this->template;
		}

		$aInput['q_anonymous'] = (isset($aInput['q_anonymous']))? $aInput['q_anonymous']:0;

		// 登録データ生成
		$aInsert = array(
			'ctID'         => $this->aClass['ctID'],
			'qbQueryStyle' => $aInput['q_select_style'],
			'qbQuerySort'  => $aInput['q_select_sort'],
			'qbNum'        => 0,
			'qbTitle'      => $aInput['q_name'],
			'qbQuickMode'  => 0,
			'qbDate'       => date('YmdHis'),
			'qbOpen'       => $aInput['q_open'],
			'qbAnonymous'  => (int)$aInput['q_anonymous'],
			'qbPublic'     => 0,
		);
		if ($aInput['q_auto_public'])
		{
			$aInsert['qbAutoPublicDate'] = ClFunc_Tz::tz('Y-m-d H:i:s',null,$aInput['q_auto_s_date'].' '.$aInput['q_auto_s_time'].':00',$this->tz);
			$aInsert['qbAutoCloseDate'] = ClFunc_Tz::tz('Y-m-d H:i:s',null,$aInput['q_auto_e_date'].' '.$aInput['q_auto_e_time'].':00',$this->tz);
		}
		$aInsert['qbReAnswer']  = $aInput['q_retry'];
		$aInsert['qbAnsPublic'] = $aInput['q_ans_public'];
		$aInsert['qbComPublic'] = $aInput['q_com_public'];

		try
		{
			$result = Model_Quest::insertQuest($aInsert);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		if (isset($aInput['finish']))
		{
			Session::set('SES_T_NOTICE_MSG',__('アンケートを作成しました。'));
			Response::redirect('/t/quest');
		}
		else
		{
			Response::redirect('/t/quest/querylist/'.$result);
		}
	}

	public function action_edit($sID = null)
	{
		$aQuest = null;
		$aChk = self::QuestChecker($sID,$aQuest);
		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}

		# タイトル
		$sTitle = __('アンケート情報の編集');
		$sTitle .= ($aQuest['qbQuickMode'])? __('（クイックアンケート）'):'';
		$this->template->set_global('pagetitle',$sTitle);

		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/quest','name'=>__('アンケート'));
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		if (!Input::post(null,false))
		{
			$data = $this->aQuestBase;
			$data['q_name']         = $aQuest['qbTitle'];
			$data['q_auto_public']  = ($aQuest['qbAutoPublicDate'] != CL_DATETIME_DEFAULT)? 1:0;
			$data['q_auto_s_date']  = ($aQuest['qbAutoPublicDate'] != CL_DATETIME_DEFAULT)? ClFunc_Tz::tz('Y/m/d',$this->tz,$aQuest['qbAutoPublicDate']):ClFunc_Tz::tz('Y/m/d',$this->tz);
			$data['q_auto_s_time']  = ($aQuest['qbAutoPublicDate'] != CL_DATETIME_DEFAULT)? ClFunc_Tz::tz('H:i',$this->tz,$aQuest['qbAutoPublicDate']):ClFunc_Tz::tz('H:i',$this->tz);
			$data['q_auto_e_date']  = ($aQuest['qbAutoCloseDate'] != CL_DATETIME_DEFAULT)? ClFunc_Tz::tz('Y/m/d',$this->tz,$aQuest['qbAutoCloseDate']):ClFunc_Tz::tz('Y/m/d',$this->tz);
			$data['q_auto_e_time']  = ($aQuest['qbAutoCloseDate'] != CL_DATETIME_DEFAULT)? ClFunc_Tz::tz('H:i',$this->tz,$aQuest['qbAutoCloseDate']):ClFunc_Tz::tz('H:i',$this->tz);
			$data['q_select_style'] = $aQuest['qbQueryStyle'];
			$data['q_select_sort']  = $aQuest['qbQuerySort'];
			$data['q_open']         = $aQuest['qbOpen'];
			$data['q_anonymous']    = $aQuest['qbAnonymous'];
			$data['q_retry']        = $aQuest['qbReAnswer'];
			$data['q_ans_public']   = $aQuest['qbAnsPublic'];
			$data['q_com_public']   = $aQuest['qbComPublic'];
			$data['error'] = null;
			$this->template->content = View::forge('t/quest/edit',$data);
			$this->template->content->set('aQuest',$aQuest);
			$this->template->javascript = array('jquery.timepicker.js','cl.t.quest.js');
			return $this->template;
		}

		$aInput = Input::post();

		$val = Validation::forge();
		$val->add_callable('Helper_CustomValidation');
		$val->add_field('q_name', __('アンケートタイトル'), 'required|max_length['.CL_TITLE_LENGTH.']');

		if (!$aQuest['qbQuickMode'])
		{
			if ($aInput['q_auto_public'])
			{
				$val->add_field('q_auto_s_time', __('開始日時'), 'required|time')
					->add_rule('min_time',ClFunc_Tz::tz('Y/m/d H:i',$this->tz),$aInput['q_auto_s_date']);
				$val->add_field('q_auto_e_time', __('終了日時'), 'required|time')
					->add_rule('min_time',$aInput['q_auto_s_date'].' '.$aInput['q_auto_s_time'],$aInput['q_auto_e_date']);
			}
		}
		if (!$val->run())
		{
			$data = $aInput;
			$data['error'] = $val->error();
			$this->template->content = View::forge('t/quest/edit',$data);
			$this->template->javascript = array('jquery.timepicker.js','cl.t.quest.js');
			$this->template->content->set('aQuest',$aQuest);
			return $this->template;
		}

		// 更新データ生成
		$aUpdate = array(
			'qbTitle'      => $aInput['q_name'],
			'qbOpen'       => $aInput['q_open'],
		);
		if (!$aQuest['qbQuickMode'])
		{
			$aUpdate['qbQueryStyle'] = $aInput['q_select_style'];
			$aUpdate['qbQuerySort'] = $aInput['q_select_sort'];
			$aUpdate['qbDate'] = date('YmdHis');

			if ($aInput['q_auto_public'])
			{
				$aUpdate['qbAutoPublicDate'] = ClFunc_Tz::tz('Y-m-d H:i:s',null,$aInput['q_auto_s_date'].' '.$aInput['q_auto_s_time'].':00',$this->tz);
				$aUpdate['qbAutoCloseDate'] = ClFunc_Tz::tz('Y-m-d H:i:s',null,$aInput['q_auto_e_date'].' '.$aInput['q_auto_e_time'].':00',$this->tz);
			}
			else
			{
				$aUpdate['qbAutoPublicDate'] = CL_DATETIME_DEFAULT;
				$aUpdate['qbAutoCloseDate'] = CL_DATETIME_DEFAULT;
			}
			$aUpdate['qbReAnswer']  = $aInput['q_retry'];
			$aUpdate['qbAnsPublic'] = $aInput['q_ans_public'];
			$aUpdate['qbComPublic'] = $aInput['q_com_public'];
			$aUpdate['qbAnonymous']  = (isset($aInput['q_anonymous']))? (int)$aInput['q_anonymous']:0;
		}

		try
		{
			$result = Model_Quest::updateQuest($aUpdate,array(array('qbID','=',$sID)));
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		if (isset($aInput['finish']))
		{
			Session::set('SES_T_NOTICE_MSG',__('アンケート情報を更新しました。'));
			Response::redirect('/t/quest');
		}
		else
		{
			Response::redirect('/t/quest/querylist/'.$sID);
		}
	}

	public function action_copy($sID = null)
	{
		\Clfunc_Common::ContractDetect($this->aTeacher, __CLASS__);

		$aQuest = null;
		$aChk = self::QuestChecker($sID,$aQuest);
		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}
		$this->template->set_global('aQuest',$aQuest);

		# タイトル
		$sTitle = __('アンケートのコピー');
		$sTitle .= '（'.$aQuest['qbTitle'].'）';
		$this->template->set_global('pagetitle',$sTitle);

		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/quest','name'=>__('アンケート'));
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
					'id' => 'QuestCopyExec',
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

			$this->template->content = View::forge('t/quest/copy',$data);
			$this->template->javascript = array('cl.t.quest.js');
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
			$this->template->content = View::forge('t/quest/copy',$data);
			$this->template->javascript = array('cl.t.quest.js');
			return $this->template;
		}

		try
		{
			$result = Model_Quest::copyQuest($aQuest,$aSelClass);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		Session::set('SES_T_NOTICE_MSG',__('アンケートのコピーが完了しました。（:num件）',array('num'=>$result)).$sFin);
		Response::redirect('/t/quest');
	}

	public function action_csv()
	{
		\Clfunc_Common::ContractDetect($this->aTeacher, __CLASS__);

		if (!$this->aClass['ctStatus'])
		{
			Session::set('SES_T_ERROR_MSG',__('終了した講義にはアンケートを新規作成することはできません。'));
			Response::redirect('/t/quest');
		}

		# タイトル
		$sTitle = __('CSVからアンケートの登録');
		$this->template->set_global('pagetitle',$sTitle);

		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/quest','name'=>__('アンケート'));
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		if (!Input::post(null,false))
		{
			$data = array('error'=>null);
			$this->template->content = View::forge('t/quest/csv',$data);
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
			$st_csv = Upload::get_errors('qt_csv');
			if ($st_csv['errors'])
			{
				switch ($st_csv['errors'][0]['error'])
				{
					case Upload::UPLOAD_ERR_INI_SIZE:
					case Upload::UPLOAD_ERR_FORM_SIZE:
					case Upload::UPLOAD_ERR_MAX_SIZE:
						$aMsg['qt_csv'] = __('登録できるファイルのサイズは:sizeMBまでです。',array('size'=>CL_FILESIZE));
						break;
					case Upload::UPLOAD_ERR_EXTENSION:
					case Upload::UPLOAD_ERR_EXT_BLACKLISTED:
					case Upload::UPLOAD_ERR_EXT_NOT_WHITELISTED:
					case Upload::UPLOAD_ERR_TYPE_BLACKLISTED:
					case Upload::UPLOAD_ERR_TYPE_NOT_WHITELISTED:
					case Upload::UPLOAD_ERR_MIME_BLACKLISTED:
					case Upload::UPLOAD_ERR_MIME_NOT_WHITELISTED:
						$aMsg['qt_csv'] = __('登録できるファイルの拡張子は txt,csv のみです。');
						break;
					default:
						$aMsg['qt_csv'] = __('ファイルアップロードに失敗しました。');
						break;
				}
			}
		}
		if (!is_null($aMsg))
		{
			$data['error'] = $aMsg;
			$this->template->content = View::forge('t/quest/csv',$data);
			return $this->template;
		}

		$oFile = Upload::get_files('qt_csv');
		$aCSV = ClFunc_Common::getCSV($oFile['file']);
		if (!count($aCSV))
		{
			$data['error'] = array('qt_csv'=>__('登録するデータがCSVから取得できませんでした。'));
			$this->template->content = View::forge('t/quest/csv',$data);
			return $this->template;
		}

		$aMsg = null;
		$sDatePtn = "/^([0-9]{4}\/[0-9]{1,2}\/[0-9]{1,2})\s+([0-9]{1,2}:[0-9]{1,2}|[0-9]{1,2}:[0-9]{1,2}:[0-9]{1,2})$/";
		$iQN = 0;
		$iCN = 0;
		$aBase = array(
			'qbAutoPublicDate' => CL_DATETIME_DEFAULT,
			'qbAutoCloseDate' => CL_DATETIME_DEFAULT,
		);
		$aQuery = null;
		$iSd = 0;
		$iEd = 0;
		if (!empty($aCSV))
		{
			foreach ($aCSV as $iKey => $aS)
			{
				switch ($aS[0])
				{
					case 'アンケートタイトル':
					case __('アンケートタイトル'):
						if ($aS[1] == '')
						{
							$aMsg[] = __('アンケートタイトルが指定されていません。');
							$aBase['qbTitle'] = '';
						}
						else
						{
							$aBase['qbTitle'] = mb_substr(strip_tags($aS[1]), 0, CL_TITLE_LENGTH);
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
							$aBase['qbQueryStyle'] = (int)$aS[1];
						}
						else
						{
							$aBase['qbQueryStyle'] = 1;
						}
					break;
					case '選択肢の並び順':
					case __('選択肢の並び順'):
						if ($aS[1] == '1' || $aS[1] == '0')
						{
							$aBase['qbQuerySort'] = (int)$aS[1];
						}
						else
						{
							$aBase['qbQuerySort'] = 0;
						}
					break;
					case '答えなおし':
					case __('答えなおし'):
						if ($aS[1] == '1' || $aS[1] == '0')
						{
							$aBase['qbReAnswer'] = (int)$aS[1];
						}
						else
						{
							$aBase['qbReAnswer'] = 0;
						}
					break;
					case '公開予定日時(年/月/日 時:分)':
					case __('公開予定日時(年/月/日 時:分)'):
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
									$aBase['qbAutoPublicDate'] = ClFunc_Tz::tz('Y/m/d H:i:s',null,$aSDate[1].' '.$aSTime[0].':'.$aSTime[1].':00',$this->tz);

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
					case __('締切予定日時(年/月/日 時:分)'):
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
									$aMsg[] = __('締切予定日時が無効な値です。');
								}
								else
								{
									$aBase['qbAutoCloseDate'] = ClFunc_Tz::tz('Y/m/d H:i:s',null,$aEDate[1].' '.$aETime[0].':'.$aETime[1].':00',$this->tz);
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
					case '個人の回答内容の公開範囲':
					case __('個人の回答内容の公開範囲'):
						if ($aS[1] == '2' || $aS[1] == '1' || $aS[1] == '0')
						{
							$aBase['qbAnsPublic'] = (int)$aS[1];
						}
						else
						{
							$aBase['qbAnsPublic'] = 0;
						}
					break;
					case '個人宛の先生コメントの公開範囲':
					case __('個人宛の先生コメントの公開範囲'):
						if ($aS[1] == '2' || $aS[1] == '1' || $aS[1] == '0')
						{
							$aBase['qbComPublic'] = (int)$aS[1];
						}
						else
						{
							$aBase['qbComPublic'] = 0;
						}
					break;
					case 'ゲスト回答':
					case __('ゲスト回答'):
						if ($aS[1] == '2' || $aS[1] == '1' || $aS[1] == '0')
						{
							$aBase['qbOpen'] = (int)$aS[1];
						}
						else
						{
							$aBase['qbOpen'] = 0;
						}
					break;
					case '匿名回答':
					case __('匿名回答'):
						if ($aS[1] == '1' || $aS[1] == '0')
						{
							$aBase['qbAnonymous'] = (int)$aS[1];
						}
						else
						{
							$aBase['qbAnonymous'] = 0;
						}
					break;
					case '回答形式':
					case __('回答形式'):
						if ($iQN > 0)
						{
							$aQuery[$iQN]['qqChoiceNum'] = $iCN;
							if (($aQuery[$iQN]['qqStyle'] === 1 || $aQuery[$iQN]['qqStyle'] === 0) && $iCN < 2)
							{
								$aMsg[] = __(':no問目の選択肢を二つ以上指定してください。',array('no'=>$iQN));
							}
						}
						$iQN++;
						$iCN = 0;
						$aQuery[$iQN]['qqStyle'] = null;

						switch($aS[1])
						{
							case 'radio':
								$aQuery[$iQN]['qqStyle'] = 0;
							break;
							case 'select':
								$aQuery[$iQN]['qqStyle'] = 1;
							break;
							case 'text':
								$aQuery[$iQN]['qqStyle'] = 2;
							break;
							default:
								$aMsg[] = __(':no問目の形式が正しく指定されていません。',array('no'=>$iQN));
								continue;
							break;
						}
					break;
					case '必須回答':
					case __('必須回答'):
						if ($aQuery[$iQN]['qqStyle'] < 2)
						{
							$aQuery[$iQN]['qqRequired'] = 1;
						}
						else
						{
							$aQuery[$iQN]['qqRequired'] = 0;
						}
						if ($aS[1] !== '')
						{
							$aQuery[$iQN]['qqRequired'] = ((int)$aS[1] == 0)? 0:1;
						}
					break;
					case '設問文':
					case __('設問文'):
						if ($aS[1] == '')
						{
							$aMsg[] = __(':no問目の設問文が指定されていません。',array('no'=>$iQN));
							continue;
						}
						else
						{
							$aQuery[$iQN]['qqText'] = strip_tags($aS[1]);
						}
					break;
					default:
						if (preg_match('/^(選択肢|'.__('選択肢').')\d{1,2}/', $aS[0]))
						{
							if ($aQuery[$iQN]['qqStyle'] == 2)
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
								$aQuery[$iQN]['qqChoice'.$iCN] = strip_tags($aS[1]);
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
				$aQuery[$iQN]['qqChoiceNum'] = $iCN;
				if (($aQuery[$iQN]['qqStyle'] === 1 || $aQuery[$iQN]['qqStyle'] === 0) && $iCN < 2)
				{
					$aMsg[] = __(':no問目の選択肢を二つ以上指定してください。',array('no'=>$iQN));
				}
			}
			else
			{
				$aMsg[] = __('設問がありません。');
			}
			if (!isset($aBase['qbTitle']))
			{
				$aMsg[] = __('アンケートタイトルが指定されていません。');
			}
			if (($iSd > 0 && $iEd == 0) || ($iEd > 0 && $iSd == 0))
			{
				$aMsg[] = __('自動公開をする場合、公開予定日時と締切予定日時は両方指定してください。');
			}

		}
		else
		{
			$data['error'] = array('qt_csv'=>__('登録するデータがありません。'));
			$this->template->content = View::forge('t/quest/csv',$data);
			return $this->template;
		}
		if (!is_null($aMsg))
		{
			$data['error'] = array('valid'=>$aMsg);
			$this->template->content = View::forge('t/quest/csv',$data);
			return $this->template;
		}

		$aBase['qbNum'] = $iQN;

		try
		{
			$result = Model_Quest::insertQuestFromCSV($aBase,$aQuery,$this->aClass['ctID']);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}


		Session::set('SES_T_NOTICE_MSG',__('CSVからアンケートの登録が完了しました。'));
		Response::redirect('t/quest');
	}


	public function action_querylist($sID = null,$iQqNO = null)
	{
		$aQuest = null;
		$aQuery = null;
		$aQQ = null;
		$aImg = null;
		$aChoice = null;
		$aInput = null;
		$aMsg = null;

		$aChk = self::QuestChecker($sID,$aQuest);
		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}
		if ($aQuest['qbPublic'] == 1)
		{
			Session::set('SES_T_ERROR_MSG',__('公開中のアンケート設問を変更することはできません。'));
			Response::redirect('/t/quest');
		}
		$result = Model_Quest::getQuestQuery(array(array('qbID','=',$sID)),null,array('qqSort'=>'asc'));
		if (count($result))
		{
			$aQuery = $result->as_array('qqSort');
		}

		# タイトル
		$sTitle = (($aQuest['qbQuickMode'])? '[Q]':'').$aQuest['qbTitle'];
		$this->template->set_global('pagetitle',$sTitle);

		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/quest','name'=>__('アンケート'));
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		# カスタムボタン
		$aCustomBtn = array(
			array(
				'url'  => '/t/quest',
				'name' => __('設問編集の終了'),
				'show' => 0,
			),
		);
		$this->template->set_global('aCustomBtn',$aCustomBtn);

		$bMod = ($aQuest['qpNum'] > 0 || $aQuest['qbQuickMode'])? true:false;

		$this->template->content = View::forge('t/quest/querylist');
		$this->template->content->set('aQuest',$aQuest);
		$this->template->content->set('aQuery',$aQuery);
		$this->template->content->set('aQQ',$aQQ);
		$this->template->content->set('aInput',$aInput);
		$this->template->content->set('aChoice',$aChoice);
		$this->template->content->set('aImg',$aImg);
		$this->template->content->set('aMsg',$aMsg);
		$this->template->content->set('bMod',$bMod);
		$this->template->content->set('iQqNO',$iQqNO);
		$this->template->javascript = array('cl.t.quest.js');
		return $this->template;
	}

	public function action_queryedit($sID = null)
	{

		// アップロード初期設定
		$config = array(
			'path' => CL_UPPATH.DS.$sID,
			'max_size' => CL_IMGSIZE*1024*1024,
			'ext_whitelist' => array('jpg','jpeg','png','gif'),
			'type_whitelist' => array('image'),
		);

		$aQuest = null;
		$aQuery = null;
		$aChk = self::QuestChecker($sID,$aQuest);
		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}
		if ($aQuest['qbPublic'] == 1)
		{
			Session::set('SES_T_ERROR_MSG',__('公開中のアンケート設問を変更することはできません。'));
			Response::redirect('/t/quest');
		}

		# タイトル
		$sTitle = (($aQuest['qbQuickMode'])? '[Q]':'').$aQuest['qbTitle'];
		$sTitle .= '｜'.__('設問リスト');
		$this->template->set_global('pagetitle',$sTitle);

		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/quest','name'=>__('アンケート'));
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		$bMod = ($aQuest['qpNum'] > 0 || $aQuest['qbQuickMode'])? true:false;

		$result = Model_Quest::getQuestQuery(array(array('qbID','=',$sID)),null,array('qqSort'=>'asc'));
		if (count($result))
		{
			$aQuery = $result->as_array();
		}
		if (!Input::post(null,false))
		{
			Response::redirect('/t/quest/querylist');
		}

		$bMod = ($aQuest['qpNum'] > 0 || $aQuest['qbQuickMode'])? true:false;

		$aMsg = null;
		$aQQ = null;
		$aImg = null;
		$aInput = Input::post();
		$aChoice = null;
		$sTempPath = CL_UPPATH.DS.$aQuest['qbID'].DS.$aInput['qSort'].'_tmp';

		if ($aInput['qNo'])
		{
			$result = Model_Quest::getQuestQuery(array(array('qbID','=',$sID),array('qqNO','=',$aInput['qNo'])));
			if (count($result))
			{
				$aQQ = $result->current();
			}
		}

		$sText = preg_replace(CL_WHITE_TRIM_PTN, '$1', $aInput['qText']);
		if ($sText == '')
		{
			$aMsg[] = __('設問文が入力されていません。');
		}

		Upload::process($config);

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
						$aMsg[] = __('設問に登録できるファイルのサイズは:sizeMBまでです。',array('size'=>CL_IMGSIZE));
					break;
					case Upload::UPLOAD_ERR_EXTENSION:
					case Upload::UPLOAD_ERR_EXT_BLACKLISTED:
					case Upload::UPLOAD_ERR_EXT_NOT_WHITELISTED:
					case Upload::UPLOAD_ERR_TYPE_BLACKLISTED:
					case Upload::UPLOAD_ERR_TYPE_NOT_WHITELISTED:
					case Upload::UPLOAD_ERR_MIME_BLACKLISTED:
					case Upload::UPLOAD_ERR_MIME_NOT_WHITELISTED:
						$aMsg[] = __('設問に登録できるファイルは画像（JPG,JPEG）のみです。');
					break;
					case Upload::UPLOAD_ERR_NO_FILE:
						# ファイルを指定していない
					break;
					default:
						$aMsg[] = __('設問のファイルアップロードに失敗しました。');
					break;
				}
			}
		}
		$qImage = Upload::get_files('qImage');
		if ($qImage)
		{
			$aInput['qqImage'] = 'base.'.$qImage['extension'];
			$sThumbImg = CL_Q_SMALL_PREFIX.$aInput['qqImage'];
			$sTempImg = 'base_tmp.'.$qImage['extension'];

			ClFunc_Common::chkDir($sTempPath,true);
			File::rename($qImage['file'], $sTempPath.DS.$sTempImg);
			ClFunc_Common::OrientationFixedImage($sTempPath.DS.$sTempImg);
			$image = Image::load($sTempPath.DS.$sTempImg);
			$image->config('quality',CL_Q_IMG_QUALITY)->resize(CL_Q_IMG_SIZE,(CL_Q_IMG_SIZE / 4 * 3),true)->save($sTempPath.DS.$aInput['qqImage'],0666);
			$image->config('quality',CL_Q_SMALL_QUALITY)->resize(CL_Q_SMALL_SIZE,(CL_Q_SMALL_SIZE / 4 * 3),true)->save($sTempPath.DS.$sThumbImg,0666);
			File::delete($sTempPath.DS.$sTempImg);
		}

		if ($bMod)
		{
			$aInput['qType'] = $aQQ['qqStyle'];
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
			for ($i = 1; $i <= 50; $i++)
			{
				$sChoice = $aInput['qChoice'.$i];
				$sName = 'qChoice'.$i.'Image';
				$sChoice = preg_replace(CL_WHITE_TRIM_PTN, '$1', $sChoice);
				if ($sChoice != '')
				{
					$aChoice[$iCnt] = $sChoice;
					$bNone = false;

					$qChoiceImg = Upload::get_errors($sName);
					if ($qChoiceImg)
					{
						switch ($qChoiceImg['errors'][0]['error'])
						{
							case Upload::UPLOAD_ERR_INI_SIZE:
							case Upload::UPLOAD_ERR_FORM_SIZE:
							case Upload::UPLOAD_ERR_MAX_SIZE:
								$aMsg[] = __('選択肢:noに登録できるファイルのサイズは:sizeMBまでです。',array('no'=>$iCnt,'size'=>CL_IMGSIZE));
								break;
							case Upload::UPLOAD_ERR_EXTENSION:
							case Upload::UPLOAD_ERR_EXT_BLACKLISTED:
							case Upload::UPLOAD_ERR_EXT_NOT_WHITELISTED:
							case Upload::UPLOAD_ERR_TYPE_BLACKLISTED:
							case Upload::UPLOAD_ERR_TYPE_NOT_WHITELISTED:
							case Upload::UPLOAD_ERR_MIME_BLACKLISTED:
							case Upload::UPLOAD_ERR_MIME_NOT_WHITELISTED:
								$aMsg[] = __('選択肢:noに登録できるファイルは画像（JPG,JPEG）のみです。',array('no'=>$iCnt));
							break;
							case Upload::UPLOAD_ERR_NO_FILE:
								# ファイルを指定していない
							break;
							default:
								$aMsg[] = __('選択肢:noのファイルアップロードに失敗しました。',array('no'=>$iCnt));
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
					else if ($aInput["qqChoiceImage".$i] != "")
					{
						$aImg[$iCnt] = $aInput["qqChoiceImage".$i];
					}
					$iCnt++;
				}
				else if ($aQQ['qqChoiceImg'.$i])
				{
					$aDelImg[$i] = $aQQ['qqChoiceImg'.$i];
				}
			}
			if ($bNone)
			{
				$aMsg[] = __('選択肢を一つ以上指定してください。');
			}
			if ($bMod && count($aChoice) != $aQQ['qqChoiceNum'])
			{
				$aMsg[] = __('回答があるアンケート、またはクイックアンケートの選択肢は増減させることはできません。');
			}
		}

		if (!is_null($aMsg))
		{
			$this->template->content = View::forge('t/quest/querylist');
			$this->template->content->set('aQuest',$aQuest);
			$this->template->content->set('aQuery',$aQuery);
			$this->template->content->set('aQQ',$aQQ);
			$this->template->content->set('aInput',$aInput);
			$this->template->content->set('aChoice',$aChoice);
			$this->template->content->set('aImg',$aImg);
			$this->template->content->set('aMsg',$aMsg);
			$this->template->content->set('bMod',$bMod);
			$this->template->content->set('iQqNO',null);
			$this->template->javascript = array('cl.t.quest.js');
			return $this->template;
		}

		if (!is_null($aQQ))
		{
			$aUpdate = array(
				'qqText' => $aInput['qText'],
				'qqImage' => $aInput['qqImage'],
				'qqStyle' => $aInput['qType'],
				'qqRequired' => isset($aInput['qRequired']),
				'qqChoiceNum' => ($aInput['qType'] == 2)? 0:count($aChoice),
				'qqDate' => date('YmdHis'),
			);
			for ($i = 1; $i <= 50; $i++)
			{
				if (isset($aChoice[$i]))
				{
					$aUpdate['qqChoice'.$i] = $aChoice[$i];
					if (isset($aImg[$i]))
					{
						$aUpdate['qqChoiceImg'.$i] = $aImg[$i];
					}
					else
					{
						$aUpdate['qqChoiceImg'.$i] = '';
					}
				}
				else
				{
					$aUpdate['qqChoice'.$i] = '';
					$aUpdate['qqChoiceImg'.$i] = '';
				}
			}
			$aWhere = array(
				array('qbID','=',$aQQ['qbID']),
				array('qqNO','=',$aQQ['qqNO']),
			);

			try
			{
				$result = Model_Quest::updateQuestQuery($aUpdate,$aWhere);
				$sSavePath = CL_UPPATH.DS.$aQuest['qbID'].DS.$aQQ['qqNO'];
			}
			catch (Exception $e)
			{
				\Clfunc_Common::LogOut($e,__CLASS__);
				Session::set('SES_T_ERROR_MSG',$e->getMessage());
				Response::redirect($this->eRedirect);
			}
			$sSesM = __('設問:noを更新しました。',array('no'=>$aQQ['qqSort']));
			$iQqNO = $aQQ['qqNO'];
		}
		else
		{
			$aInsert = array(
				'qbID' => $aQuest['qbID'],
				'qqSort' => $aInput['qSort'],
				'qqText' => $aInput['qText'],
				'qqImage' => $aInput['qqImage'],
				'qqStyle' => $aInput['qType'],
				'qqRequired' => isset($aInput['qRequired']),
				'qqChoiceNum' => ($aInput['qType'] == 2)? 0:count($aChoice),
				'qqDate' => date('YmdHis'),
			);
			if ($aInput['qType'] != 2)
			{
				foreach ($aChoice as $i => $sChoice)
				{
					$aInsert['qqChoice'.$i] = $sChoice;
					if (isset($aImg[$i]))
					{
						$aInsert['qqChoiceImg'.$i] = $aImg[$i];
					}
				}
			}

			try
			{
				$result = Model_Quest::insertQuestQuery($aInsert);
				$sSavePath = CL_UPPATH.DS.$aQuest['qbID'].DS.$result;
			}
			catch (Exception $e)
			{
				\Clfunc_Common::LogOut($e,__CLASS__);
				Session::set('SES_T_ERROR_MSG',$e->getMessage());
				Response::redirect($this->eRedirect);
			}
			$sSesM = __('設問:noを追加しました。',array('no'=>$aInsert['qqSort']));
			$iQqNO = null;
		}

		if (file_exists($sSavePath))
		{
			system('rm -rf '.$sSavePath);
		}
		if ($aInput['qqImage'])
		{
			ClFunc_Common::chkDir($sSavePath,true);
			File::rename($sTempPath.DS.$aInput['qqImage'],$sSavePath.DS.$aInput['qqImage']);
			File::rename($sTempPath.DS.CL_Q_SMALL_PREFIX.$aInput['qqImage'],$sSavePath.DS.CL_Q_SMALL_PREFIX.$aInput['qqImage']);
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
		Response::redirect('/t/quest/querylist/'.$aQuest['qbID'].((is_null($iQqNO))? '':DS.$iQqNO));
	}

	public function action_querydelete($sID = null,$iNO = null)
	{
		$sImgPath = CL_UPPATH.DS.$sID.DS.$iNO;
		$aQuest = null;
		$aQuery = null;
		$aChk = self::QuestChecker($sID,$aQuest);
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
			$result = Model_Quest::deleteQuestQuery($sID,$iNO,$aQuery);
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
		Session::set('SES_T_NOTICE_MSG',__('設問:noを削除しました。',array('no'=>$aQuery['qqSort'])));
		Response::redirect('/t/quest/querylist/'.$sID);
	}

	public function action_preview($sID = null)
	{
		$aQuest = null;
		$aQuery = null;

		$aChk = self::QuestChecker($sID,$aQuest);
		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}
		$result = Model_Quest::getQuestQuery(array(array('qbID','=',$sID)),null,array('qqSort'=>'asc'));
		if (count($result))
		{
			$aQuery = $result->as_array();
		}

		# タイトル
		$sQuick = ($aQuest['qbQuickMode'])? '[Q]':'';
		$sTitle = $sQuick.$aQuest['qbTitle'].'｜'.__('アンケートプレビュー');
		$this->template->set_global('pagetitle',$sTitle);
		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/quest','name'=>__('アンケート'));
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		$this->template->content = View::forge('t/quest/preview');
		$this->template->content->set('aQuest',$aQuest);
		$this->template->content->set('aQuery',$aQuery);
		$this->template->javascript = array('cl.s.quest.js');
		return $this->template;
	}

	public function action_quick($iMode = null)
	{
		global $gaQuickTitle;

		if (is_null($iMode))
		{
			Session::set('SES_T_ERROR_MSG',__('作成するアンケートの情報が確認できませんでした。'));
			Response::redirect('/t/quest');
		}
		$sDate = date('YmdHis');

		$aQBase = array(
			'ctID' => $this->aClass['ctID'],
			'qbQueryStyle' => 2,
			'qbNum' => 1,
			'qbDate' => $sDate,
			'qbTitle' => '',
			'qbQuickMode' => 0,
		);
		$aQQuery = array(
			0 => array(
				'qqNO' => 1,
				'qqSort' => 1,
				'qqChoiceNum' => 2,
				'qqRequired' => 1,
				'qqDate' => $sDate,
				'qqText' => __('質問に対しての回答を選択してください。'),
				'qqChoice1' => '1',
				'qqChoice2' => '2',
			),
		);
		$aQCom = array(
			'qqNO' => 2,
			'qqSort' => 2,
			'qqStyle' => 2,
			'qqChoiceNum' => 0,
			'qqDate' => $sDate,
			'qqText' => __('選択した理由を記入してください。'),
		);

		$sTitle = __($gaQuickTitle[$iMode]);

		switch ((int)$iMode)
		{
			case 21: # 二択コメント付き
				$aQBase['qbTitle'] = $sTitle;
				$aQQuery[1] = $aQCom;
				$aQBase['qbNum'] = 2;
			case 20: # 二択
				if ($aQBase['qbTitle'] == '') { $aQBase['qbTitle'] = $sTitle; }
			break;
			case 23: # はい/いいえコメント付き
				$aQBase['qbTitle'] = $sTitle;
				$aQQuery[1] = $aQCom;
				$aQBase['qbNum'] = 2;
			case 22: # はい/いいえ
				if ($aQBase['qbTitle'] == '') { $aQBase['qbTitle'] = $sTitle; }
				$aQQuery[0]['qqChoice1'] = __('はい');
				$aQQuery[0]['qqChoice2'] = __('いいえ');
			break;
			case 25: # 賛成/反対コメント付き
				$aQBase['qbTitle'] = $sTitle;
				$aQQuery[1] = $aQCom;
				$aQBase['qbNum'] = 2;
			case 24: # 賛成/反対
				if ($aQBase['qbTitle'] == '') { $aQBase['qbTitle'] = $sTitle; }
				$aQQuery[0]['qqChoice1'] = __('賛成');
				$aQQuery[0]['qqChoice2'] = __('反対');
			break;
			case 31: # 三択コメント付き
				$aQBase['qbTitle'] = $sTitle;
				$aQQuery[1] = $aQCom;
				$aQBase['qbNum'] = 2;
			case 30: # 三択
				if ($aQBase['qbTitle'] == '') { $aQBase['qbTitle'] = $sTitle; }
				$aQQuery[0]['qqChoice3'] = '3';
			break;
			case 41: # 四択コメント付き
				$aQBase['qbTitle'] = $sTitle;
				$aQQuery[1] = $aQCom;
				$aQBase['qbNum'] = 2;
			case 40: # 四択
				if ($aQBase['qbTitle'] == '') { $aQBase['qbTitle'] = $sTitle; }
				$aQQuery[0]['qqChoice3'] = '3';
				$aQQuery[0]['qqChoice4'] = '4';
			break;
			case 51: # 五択コメント付き
				$aQBase['qbTitle'] = $sTitle;
				$aQQuery[1] = $aQCom;
				$aQBase['qbNum'] = 2;
			case 50: # 五択
				if ($aQBase['qbTitle'] == '') { $aQBase['qbTitle'] = $sTitle; }
				$aQQuery[0]['qqChoice3'] = '3';
				$aQQuery[0]['qqChoice4'] = '4';
				$aQQuery[0]['qqChoice5'] = '5';
			break;
			case 100:
				$aQBase = array(
					'ctID' => $this->aClass['ctID'],
					'qbQueryStyle' => 1,
					'qbNum' => 6,
					'qbDate' => $sDate,
					'qbTitle' => __('満足度アンケート'),
					'qbQuickMode' => 0,
					'qbPublicDate' => $sDate,
				);
				$aQQuery = array(
					0 => array(
						'qqNO' => 1,
						'qqSort' => 1,
						'qqStyle' => 0,
						'qqChoiceNum' => 5,
						'qqDate' => $sDate,
						'qqText' => __('今回参加してみて、どのくらい満足していますか。'),
						'qqChoice1' => __('非常に満足'),
						'qqChoice2' => __('満足'),
						'qqChoice3' => __('ふつう'),
						'qqChoice4' => __('不満'),
						'qqChoice5' => __('非常に不満'),
					),
					1 => array(
						'qqNO' => 2,
						'qqSort' => 2,
						'qqStyle' => 2,
						'qqDate' => $sDate,
						'qqText' => __('上記、満足度を選んだ理由をお教えください。'),
					),
					2 => array(
						'qqNO' => 3,
						'qqSort' => 3,
						'qqStyle' => 0,
						'qqChoiceNum' => 5,
						'qqDate' => $sDate,
						'qqText' => __('内容についてどのくらい理解できましたか？'),
						'qqChoice1' => __('よく理解できた'),
						'qqChoice2' => __('理解できた'),
						'qqChoice3' => __('普通'),
						'qqChoice4' => __('理解できなかった'),
						'qqChoice5' => __('全く理解できなかった'),
					),
					3 => array(
						'qqNO' => 4,
						'qqSort' => 4,
						'qqStyle' => 1,
						'qqChoiceNum' => 5,
						'qqDate' => $sDate,
						'qqText' => __('詳しく知りたい内容はありますか？'),
						'qqChoice1' => __('業界のこと'),
						'qqChoice2' => __('企業のこと'),
						'qqChoice3' => __('仕事内容'),
						'qqChoice4' => __('会社の雰囲気'),
						'qqChoice5' => __('採用のこと'),
					),
					4 => array(
						'qqNO' => 5,
						'qqSort' => 5,
						'qqStyle' => 2,
						'qqDate' => $sDate,
						'qqText' => __('上記以外で聞きたいことがあれば、具体的に書いてください。'),
					),
					5 => array(
						'qqNO' => 6,
						'qqSort' => 6,
						'qqStyle' => 2,
						'qqDate' => $sDate,
						'qqText' => __('ご意見・ご要望がございましたら、ご自由にお書きください。'),
					),
				);

			break;
			case 1: # コメントのみ
				$aQBase['qbTitle'] = $sTitle;
				$aQQuery = array(
					0 => array(
						'qqNO' => 1,
						'qqSort' => 1,
						'qqStyle' => 2,
						'qqDate' => $sDate,
						'qqText' => __('質問に対しての回答を入力してください。'),
					),
				);
			break;
			default:
				Session::set('SES_T_ERROR_MSG',__('作成するアンケートの情報が確認できませんでした。'));
				Response::redirect('/t/quest');
			break;
		}
		if ($iMode < 100)
		{
			$aQQuery[0]['qqChoiceNum'] = floor($iMode / 10);
			$aQBase['qbQuickMode'] = $iMode;
			$aQBase['qbPublicDate'] = $sDate;
		}

		try
		{
			$result = Model_Quest::insertQuickQuest($aQBase,$aQQuery);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}
		Response::redirect('/t/quest/bent/'.$result);
	}

	public function action_delete($sID = null)
	{
		$sImgPath = CL_UPPATH.DS.$sID;
		$aQuest = null;
		$aChk = self::QuestChecker($sID,$aQuest);
		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}

		try
		{
			$result = Model_Quest::deleteQuest($sID,$aQuest);
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

		Session::set('SES_T_NOTICE_MSG',__('アンケートを削除しました。'));
		Response::redirect('/t/quest');
	}

	public function action_bent($sID = null, $iTextBent = 0)
	{
		$aQuest = null;
		$aQuery = null;
		$aChk = self::QuestChecker($sID,$aQuest);
		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}
		$result = Model_Quest::getQuestQuery(array(array('qbID','=',$sID)),null,array('qqSort'=>'asc'));
		if (!count($result))
		{
			Session::set('SES_T_ERROR_MSG',__('指定のアンケートには設問がありません。'));
			Response::redirect('/t/quest');
		}
		$aRes = $result->as_array();
		foreach ($aRes as $aR)
		{
			$aQuery['qq'.$aR['qqNO']] = $aR;
		}

		try
		{
			$result = Model_Quest::setQuestBent($aQuest,$iTextBent);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		$aBent = null;
		$result = Model_Quest::getQuestBent(array(array('qb.qbID','=',$sID)));
		if (count($result))
		{
			$aRes = $result->as_array();
			foreach ($aRes as $aR)
			{
				$aBent[$aR['qbMode']]['qq'.$aR['qqNO']][$aR['qbNO']] = $aR;

				if ($aR['stID'])
				{
					$sName = '['.__('匿名').']';
					if (preg_match('/^s.+/',$aR['stID']))
					{
						$sName = $aR['qpstName'];
					}
					elseif (preg_match('/^t.+/',$aR['stID']))
					{
						$sName = ($aR['qpstName'])? $aR['qpstName']:$aR['ttName'];
					}
					elseif ($aQuest['qbOpen'] == 2 && preg_match('/^g.+/',$aR['stID']))
					{
						$sName = (($aR['qpstName'])? $aR['qpstName']:(($aR['gtName'])? $aR['gtName']:'[GUEST]'));
					}
					$aBent[$aR['qbMode']]['qq'.$aR['qqNO']][$aR['qbNO']]['cPosted'] = $sName;
				}
			}
		}
		$aComment = null;
		if ($aQuest['qbQuickMode'] && isset($aQuery['qq2']))
		{
			$selResult = Model_Quest::getQuestAns(array(array('qa.qbID','=',$sID),array('qq.qqSort','=',1)),null,array('qa.qaDate'=>'DESC'));
			$txtResult = Model_Quest::getQuestBent(array(array('qb.qbID','=',$sID),array('qb.qqNO','=',2)),null,array('qa.qaDate'=>'DESC'));
			if (count($selResult) && count($txtResult))
			{
				$aSel = $selResult->as_array('stID');
				$aTxt = $txtResult->as_array();
				foreach ($aTxt as $aT)
				{
					$sStID = $aT['stID'];
					$aS = $aSel[$sStID];
					$sChoice = null;
					$iNO = null;
					for ($i = 1; $i <= $aQuery['qq1']['qqChoiceNum']; $i++)
					{
						if ($aS['qaChoice'.$i] == 1)
						{
							$sChoice = $aQuery['qq1']['qqChoice'.$i];
							$iNO = $i;
							break;
						}
					}

					$sName = '['.__('匿名').']';
					if (preg_match('/^s.+/',$sStID))
					{
						$sName = $aT['qpstName'];
					}
					elseif (preg_match('/^t.+/',$sStID))
					{
						$sName = ($aT['qpstName'])? $aT['qpstName']:$aT['ttName'];
					}
					elseif ($aQuest['qbOpen'] == 2 && preg_match('/^g.+/',$sStID))
					{
						$sName = (($aT['qpstName'])? $aT['qpstName']:(($aT['gtName'])? $aT['gtName']:'[GUEST]'));
					}

					$aComment[$aT['qbMode']][$sStID] = array(
						'text'    => $aT['qbText'],
						'cName'   => $sChoice,
						'cNO'     => $iNO,
						'cPick'   => $aT['qaPick'],
						'cPosted' => $sName,
					);
				}
			}
		}

		# タイトル
		$sTitle = (($aQuest['qbQuickMode'])? '[Q]':'').$aQuest['qbTitle'];

		$view = View::forge('template');
		$view->content = View::forge('t/quest/bent');
		$view->content->set('sTitle',$sTitle);
		$view->content->set('aQuest',$aQuest);
		$view->content->set('aQuery',$aQuery);
		$view->content->set('aBent',$aBent);
		$view->content->set('aComment',$aComment);
		$view->javascript = array('Chart.js','cl.t.quest.js','cl.t.kreport.js');
		$view->footer = View::forge('t/footer');
		return $view;
	}

	public function action_put($sID = null)
	{
		$aQuest = null;
		$aChk = self::QuestChecker($sID,$aQuest);
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
		$aGuest = null;
		$aTeach = null;
		$result = Model_Quest::getQuestPut(array(array('qp.qbID','=',$sID)));
		if (count($result))
		{
			$aRes = $result->as_array('stID');
			foreach ($aRes as $sStID => $aP)
			{
				if (isset($aStudent[$sStID]))
				{
					$aStudent[$sStID]['put'] = $aP;
				}
				if (preg_match('/^g.+/',$sStID))
				{
					$aGuest[$sStID]['put'] = $aP;
				}
				if (preg_match('/^t.+/',$sStID))
				{
					$aTeach[$sStID]['put'] = $aP;
				}
			}
		}

		# タイトル
		$sTitle = (($aQuest['qbQuickMode'])? '[Q]':'').$aQuest['qbTitle'];
		$sTitle .= '｜'.__('提出状況');
		$this->template->set_global('pagetitle',$sTitle);

		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/quest','name'=>__('アンケート'));
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		# カスタムボタン
		if (!$aQuest['qbAnonymous'])
		{
			$aCustomBtn = array(
				array(
					'url'  => '/t/quest/anslist/'.$sID,
					'name' => __('回答一覧'),
					'show' => 0,
				),
			);
			$this->template->set_global('aCustomBtn',$aCustomBtn);
		}

		$aSearchForm = array(
			'url' => '/t/quest/put/'.$sID,
		);
		$this->template->set_global('aSearchForm',$aSearchForm);
		$this->template->set_global('sWords',$sWords);

		$this->template->content = View::forge('t/quest/put');
		$this->template->content->set('aQuest',$aQuest);
		$this->template->content->set('aStudent',$aStudent);
		$this->template->content->set('aGuest',$aGuest);
		$this->template->content->set('aTeach',$aTeach);
		$this->template->javascript = array('cl.t.quest.js');
		return $this->template;
	}

	public function action_ansdetail($sID = null, $sStID = null)
	{
		$aQuest = null;
		$aQuery = null;
		$sName = null;
		$aAns = null;

		$aChk = self::QuestChecker($sID,$aQuest);
		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}

		if ($aQuest['qbAnonymous'])
		{
			Session::set('SES_T_ERROR_MSG',__('指定のアンケート情報が見つかりませんでした。'));
			Response::redirect('/t/quest');
		}

		$result = Model_Quest::getQuestQuery(array(array('qbID','=',$sID)),null,array('qqSort'=>'asc'));
		if (!count($result))
		{
			Session::set('SES_T_ERROR_MSG',__('指定のアンケートには設問がありません。'));
			Response::redirect('/t/quest');
		}
		$aQuery = $result->as_array();
		$result = Model_Student::getStudentFromClass($this->aClass['ctID'],array(array('sp.stID','=',$sStID)));
		if (count($result))
		{
			$aRes = $result->current();
			$sName = $aRes['stName'];
		}
		$result = Model_Quest::getQuestPut(array(array('qp.qbID','=',$sID),array('qp.stID','=',$sStID)));
		if (!count($result))
		{
			Session::set('SES_T_ERROR_MSG',__('指定の学生はアンケート未回答です。'));
			Response::redirect('/t/quest/put/'.$sID);
		}
		$aPut = $result->current();
		if (is_null($sName))
		{
			$sName = $aPut['qpstName'];
		}
		if (preg_match('/^g.+/', $sStID))
		{
			$sName = ($aQuest['qbOpen'] == 2)? (($aPut['qpstName'])? $aPut['qpstName']:(($aPut['gtName'])? $aPut['gtName']:__('─無記名─'))):__('─匿名─');
		}
		if (preg_match('/^t.+/', $sStID))
		{
			$sName = ($aPut['qpstName'])? $aPut['qpstName']:$aPut['ttName'];
		}
		$result = Model_Quest::getQuestAns(array(array('qa.qbID','=',$sID),array('qa.stID','=',$sStID)));
		if (!count($result))
		{
			Session::set('SES_T_ERROR_MSG',__('指定の学生はアンケート未回答です。'));
			Response::redirect('/t/quest/put/'.$sID);
		}
		$aAns = $result->as_array('qqNO');

		# タイトル
		$sTitle = (($aQuest['qbQuickMode'])? '[Q]':'').$aQuest['qbTitle'];
		$sTitle .= '｜'.__(':nameの回答内容',array('name'=>$sName));
		$this->template->set_global('pagetitle',$sTitle);

		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/quest','name'=>__('アンケート'));
		$this->aBread[] = array('link'=>'/quest/put/'.$sID,'name'=>__('提出状況'));
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		$this->template->content = View::forge('t/quest/ansdetail');
		$this->template->content->set('aQuest',$aQuest);
		$this->template->content->set('aQuery',$aQuery);
		$this->template->content->set('aAns',$aAns);
		$this->template->content->set('aPut',$aPut);
		$this->template->javascript = array('cl.t.quest.js','cl.t.kreport.js');
		return $this->template;
	}


	public function action_anslist($sID = null)
	{
		$aQuest   = null;
		$aQuery   = null;
		$aStudent = null;
		$aGuest   = null;
		$aTeach   = null;

		$aChk = self::QuestChecker($sID,$aQuest);
		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}
		$result = Model_Quest::getQuestQuery(array(array('qbID','=',$sID)),null,array('qqSort'=>'asc'));
		if (!count($result))
		{
			Session::set('SES_T_ERROR_MSG',__('指定のアンケートには設問がありません。'));
			Response::redirect('/t/quest');
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
		$result = Model_Quest::getQuestPut(array(array('qp.qbID','=',$sID)));
		if (count($result))
		{
			$aRes = $result->as_array('stID');
			foreach ($aRes as $sStID => $aP)
			{
				if (isset($aStudent[$sStID]))
				{
					$aStudent[$sStID]['put'] = $aP;
				}
				if (preg_match('/^g.++/', $sStID))
				{
					$aGuest[$sStID]['put'] = $aP;
				}
				if (preg_match('/^t.++/', $sStID))
				{
					$aTeach[$sStID]['put'] = $aP;
				}
			}
		}
		$result = Model_Quest::getQuestAns(array(array('qa.qbID','=',$sID)));
		if (count($result))
		{
			$aRes = $result->as_array();
			foreach ($aRes as $aA)
			{
				if (isset($aStudent[$aA['stID']]))
				{
					$aStudent[$aA['stID']]['ans'][$aA['qqSort']] = $aA;
				}
				if (isset($aGuest[$aA['stID']]))
				{
					$aGuest[$aA['stID']]['ans'][$aA['qqSort']] = $aA;
				}
				if (isset($aTeach[$aA['stID']]))
				{
					$aTeach[$aA['stID']]['ans'][$aA['qqSort']] = $aA;
				}
			}
		}

		# タイトル
		$sTitle = (($aQuest['qbQuickMode'])? '[Q]':'').$aQuest['qbTitle'];
		$sTitle .= '｜'.__('回答一覧');
		$this->template->set_global('pagetitle',$sTitle);

		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/quest','name'=>__('アンケート'));
		$this->aBread[] = array('link'=>'/quest/put/'.$sID,'name'=>__('提出状況'));
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		# カスタムボタン
		$aCustomBtn = array(
			array(
				'url'  => '/t/quest/put/'.$sID,
				'name' => __('提出状況'),
				'show' => 0,
			),
		);
		$this->template->set_global('aCustomBtn',$aCustomBtn);

		$this->template->content = View::forge('t/quest/anslist');
		$this->template->content->set('aQuest',$aQuest);
		$this->template->content->set('aQuery',$aQuery);
		$this->template->content->set('aStudent',$aStudent);
		$this->template->content->set('aGuest',$aGuest);
		$this->template->content->set('aTeach',$aTeach);
		$this->template->javascript = array('cl.t.quest.js');
		return $this->template;
	}

	public function action_ans($sQbID)
	{
		$aQuest = null;
		$aQuery = null;
		$aChk = self::QuestAnsChecker('ans',$sQbID,$aQuest,$aQuery,$this->aTeacher['ttID']);

		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}

		$aQQs = array();
		foreach ($aQuery as $aQ)
		{
			$aQQs[$aQ['qqNO']] = $aQ;
		}

		if (Input::post(null,false))
		{
			$aPost = Input::post();
			$aMsg = null;
			$aInput = null;
			foreach ($aQuery as $aQ)
			{
				$iQqNO = $aQ['qqNO'];
				$bReq = (int)$aQ['qqRequired'];
				$aInput[$iQqNO]['select'] = '';
				$aInput[$iQqNO]['text'] = '';
				switch($aQ['qqStyle'])
				{
					case 0:
						if (!isset($aPost['radioSel_'.$iQqNO]))
						{
							if ($bReq)
							{
								$aMsg[$iQqNO] = __('選択は必須です。');
							}
							else
							{
								$aInput[$iQqNO]['select'] = null;
							}
						}
						else
						{
							$aInput[$iQqNO]['select'] = $aPost['radioSel_'.$iQqNO];
						}
					break;
					case 1:
						if (!isset($aPost['checkSel_'.$iQqNO]))
						{
							if ($bReq)
							{
								$aMsg[$iQqNO] = __('選択は必須です。');
							}
							else
							{
								$aInput[$iQqNO]['select'] = null;
							}
						}
						else
						{
							$sChecks = implode("|",$aPost['checkSel_'.$iQqNO]);
							$aInput[$iQqNO]['select'] = $sChecks;
						}
					break;
					case 2:
						if (!$aPost['textAns_'.$iQqNO])
						{
							if ($bReq)
							{
								$aMsg[$iQqNO] = __('入力は必須です。');
							}
							else
							{
								$aInput[$iQqNO]['text'] = null;
							}
						}
						else
						{
							$sTemp = preg_replace('/^[\s　]*(.*?)[\s　]*$/u', '$1', $aPost['textAns_'.$iQqNO]);
							$sTemp = mb_convert_kana($sTemp,"as",CL_ENC);
							$sTemp = str_replace(array("\r\n","\r"), "\n", $sTemp);
							$aInput[$iQqNO]['text'] = trim($sTemp);
						}
					break;
				}
			}
			Session::set('SES_T_QUEST_ANS_'.$sQbID,serialize(array($sQbID=>$aInput)));
			if (!is_null($aMsg))
			{
				Session::set('SES_T_QUEST_MSG_'.$sQbID,serialize($aMsg));
				Response::redirect(DS.$this->bn.DS.'ans'.DS.$sQbID);
			}
			Response::redirect(DS.$this->bn.DS.'check'.DS.$sQbID);
		}

		$aInput = null;
		$aTemp = Session::get('SES_T_QUEST_ANS_'.$sQbID,false);
		$aTemp = ($aTemp)? unserialize($aTemp):null;
		if (isset($aTemp[$sQbID]))
		{
			$aInput = $aTemp[$sQbID];
		}
		else
		{
			$result = Model_Quest::getQuestAns(array(array('qa.qbID','=',$sQbID),array('qa.stID','=',$this->aTeacher['ttID'])));
			if (count($result))
			{
				$aAns = $result->as_array();
				foreach ($aAns as $aA)
				{
					$aQQ = $aQQs[$aA['qqNO']];
					if ($aQQ['qqStyle'] == 2)
					{
						$aInput[$aA['qqNO']] = array('text'=>$aA['qaText']);
					}
					else
					{
						$aSel = array();
						for ($i = 1; $i <= $aQQ['qqChoiceNum']; $i++)
						{
							if ($aA['qaChoice'.$i])
							{
								$aSel[] = $i;
							}
							$sSel = implode('|',$aSel);
							$aInput[$aA['qqNO']] = array('select'=>$sSel);
						}
					}
				}
			}
		}

		# タイトル
		$sQuick = ($aQuest['qbQuickMode'])? '[Q]':'';
		$sTitle = $sQuick.$aQuest['qbTitle'];
		$this->template->set_global('pagetitle',$sTitle);
		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/quest','name'=>__('アンケート'));
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		$aMsg = Session::get('SES_T_QUEST_MSG_'.$sQbID,false);
		$aMsg = ($aMsg)? unserialize($aMsg):null;
		Session::delete('SES_T_QUEST_MSG_'.$sQbID);

		$this->template->content = View::forge($this->bn.DS.'ans');
		$this->template->content->set('aQuest',$aQuest);
		$this->template->content->set('aQuery',$aQuery);
		$this->template->content->set('aInput',$aInput);
		$this->template->content->set('aMsg',$aMsg);
		$this->template->javascript = array('cl.s.quest.js');
		return $this->template;
	}

	public function action_check($sQbID)
	{
		$aQuest = null;
		$aQuery = null;
		$sBackURL = DS.$this->bn.DS.'ans'.DS.$sQbID;
		$aChk = self::QuestAnsChecker('ans',$sQbID,$aQuest,$aQuery,$this->aTeacher['ttID']);

		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}

		$aTemp = Session::get('SES_T_QUEST_ANS_'.$sQbID,false);
		if (!$aTemp)
		{
			Session::set('SES_T_ERROR_MSG',__('アンケートの回答情報が見つかりませんでした。'));
			Response::redirect($sBackURL);
		}
		$aTemp = ($aTemp)? unserialize($aTemp):null;
		if (!isset($aTemp[$sQbID]))
		{
			Session::set('SES_T_ERROR_MSG',__('指定のアンケート回答情報が見つかりませんでした。'));
			Response::redirect($sBackURL);
		}
		$aInput = $aTemp[$sQbID];

		# タイトル
		$sQuick = ($aQuest['qbQuickMode'])? '[Q]':'';
		$sTitle = $sQuick.$aQuest['qbTitle'];
		$this->template->set_global('pagetitle',$sTitle);
		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/quest','name'=>__('アンケート'));
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		$this->template->content = View::forge($this->bn.DS.'check');
		$this->template->content->set('aQuest',$aQuest);
		$this->template->content->set('aQuery',$aQuery);
		$this->template->content->set('aInput',$aInput);
		$this->template->javascript = array('cl.s.quest.js');
		return $this->template;
	}

	public function post_submit($sQbID)
	{
		$aQuest = null;
		$aQuery = null;
		$sBackURL = DS.$this->bn.DS.'ans'.DS.$sQbID;
		$aChk = self::QuestAnsChecker('ans',$sQbID,$aQuest,$aQuery,$this->aTeacher['ttID']);
		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}
		$aSubmit = Input::post(null,false);
		if (!$aSubmit)
		{
			Session::set('SES_T_ERROR_MSG',__('正しく提出がされませんでした。'));
			Response::redirect($sBackURL);
		}
		if (isset($aSubmit['back']))
		{
			Response::redirect($sBackURL);
		}
		$aTemp = Session::get('SES_T_QUEST_ANS_'.$sQbID,false);
		if (!$aTemp)
		{
			Session::set('SES_T_ERROR_MSG',__('アンケートの回答情報が見つかりませんでした。'));
			Response::redirect($sBackURL);
		}
		$aTemp = ($aTemp)? unserialize($aTemp):null;
		if (!isset($aTemp[$sQbID]))
		{
			Session::set('SES_T_ERROR_MSG',__('指定のアンケート回答情報が見つかりませんでした。'));
			Response::redirect($sBackURL);
		}
		$aInput = $aTemp[$sQbID];

		$bUpdate = false;
		$result = Model_Quest::getQuestPut(array(array('qp.qbID','=',$sQbID),array('qp.stID','=',$this->aTeacher['ttID'])));
		if (count($result))
		{
			$bUpdate = true;
		}

		try
		{
			Model_Quest::setTeacherQuestPut($aQuest,$aQuery,$this->aTeacher,$aInput,$bUpdate);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		Session::delete('SES_T_QUEST_ANS_'.$sQbID);

		Session::set('SES_T_NOTICE_MSG',__(':titleに回答を提出しました。',array('title'=>$aQuest['qbTitle'])));
		Response::redirect(DS.$this->bn);
	}

	public function action_result($sID = null)
	{
		$aQuest = null;
		$aQuery = null;
		$aChk = self::QuestAnsChecker('result',$sID,$aQuest,$aQuery,$this->aTeacher['ttID']);
		if (is_array($aChk))
		{
			Session::set('SES_T_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}
		$result = Model_Quest::getQuestPut(array(array('qp.qbID','=',$sID),array('qp.stID','=',$this->aTeacher['ttID'])));
		if (!count($result))
		{
			Session::set('SES_T_ERROR_MSG',__('未回答アンケートの結果を閲覧することはできません。'));
			Response::redirect(DS.$this->bn);
		}
		$aPut = $result->current();
		$result = Model_Quest::getQuestAns(array(array('qa.qbID','=',$sID),array('qa.stID','=',$this->aTeacher['ttID'])));
		if (!count($result))
		{
			Session::set('SES_T_ERROR_MSG',__('未回答アンケートの結果を閲覧することはできません。'));
			Response::redirect(DS.$this->bn);
		}
		$aAns = $result->as_array('qqNO');

		# タイトル
		$sQuick = ($aQuest['qbQuickMode'])? '[Q]':'';
		$sTitle = $sQuick.$aQuest['qbTitle'];
		$this->template->set_global('pagetitle',$sTitle);
		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/quest','name'=>__('アンケート'));
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		$this->template->content = View::forge($this->bn.DS.'result');
		$this->template->content->set('aQuest',$aQuest);
		$this->template->content->set('aQuery',$aQuery);
		$this->template->content->set('aPut',$aPut);
		$this->template->content->set('aAns',$aAns);
		$this->template->content->set('bOther',false);
		$this->template->javascript = array('cl.s.quest.js');
		return $this->template;
	}

	public function action_putlist()
	{
		$aQuest = null;
		$aQtIDs = null;
		$result = Model_Quest::getQuestBaseFromClass($this->aClass['ctID'],null,null,array('qb.qbSort'=>'desc'));
		if (count($result))
		{
			foreach ($result as $aQ)
			{
				$aQuest[] = $aQ;
				$aQtIDs[] = $aQ['qbID'];
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
		if (!is_null($aQtIDs))
		{
			$aWhere[] = array('qp.qbID','IN',$aQtIDs);
		}
		if (!is_null($aStIDs))
		{
			$aWhere[] = array('qp.stID','IN',$aStIDs);
		}

		$result = Model_Quest::getQuestPut($aWhere);
		if (count($result))
		{
			foreach ($result as $aP)
			{
				$sStID = $aP['stID'];
				$sQbID = $aP['qbID'];
				if (isset($aStudent[$sStID]))
				{
					$aStudent[$sStID]['put'][$sQbID] = $aP;
				}
			}
		}

		# タイトル
		$sTitle = __('提出一覧');
		$this->template->set_global('pagetitle',$sTitle);
		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/quest','name'=>__('アンケート'));
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		# カスタムボタン
		$aCustomBtn = array(
			array(
				'url'  => '/t/output/questputlist.csv',
				'name' => __('提出一覧CSVのダウンロード'),
				'show' => 1,
				'icon' => 'fa-download',
			),
		);
		$this->template->set_global('aCustomBtn',$aCustomBtn);

		$aSearchForm = array(
			'url' => '/t/quest/putlist',
		);
		$this->template->set_global('aSearchForm',$aSearchForm);
		$this->template->set_global('sWords',$sWords);

		$this->template->content = View::forge($this->bn.DS.'putlist');
		$this->template->content->set('aQuest',$aQuest);
		$this->template->content->set('aStudent',$aStudent);
		$this->template->javascript = array('cl.t.quest.js');
		return $this->template;
	}

	public function action_stuanslist($sStID = null)
	{
		$iQNum = 0;
		$aQuest = null;
		$aQtIDs = null;
		$result = Model_Quest::getQuestBaseFromClass($this->aClass['ctID'],null,null,array('qb.qbSort'=>'desc'));
		if (count($result))
		{
			foreach ($result as $aQ)
			{
				$aQuest[] = $aQ;
				$aQtIDs[] = $aQ['qbID'];
				if ($aQ['qbNum'] > $iQNum)
				{
					$iQNum = $aQ['qbNum'];
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

		$aPWhere = array(array('qp.stID','=',$sStID));
		$aQWhere = null;
		$aAWhere = array(array('qa.stID','=',$sStID));
		if (!is_null($aQtIDs))
		{
			$aPWhere[] = array('qp.qbID','IN',$aQtIDs);
			$aQWhere[] = array('qbID','IN',$aQtIDs);
			$aAWhere[] = array('qa.qbID','IN',$aQtIDs);
		}
		$result = Model_Quest::getQuestPut($aPWhere);
		if (count($result))
		{
			foreach ($result as $aP)
			{
				$aPut[$aP['qbID']] = $aP;
			}
		}

		$aQuery = null;
		$result = Model_Quest::getQuestQuery($aQWhere);
		if (count($result))
		{
			foreach ($result as $aQ)
			{
				$aQuery[$aQ['qbID']][$aQ['qqSort']] = $aQ;
			}
		}

		$aAns = null;
		$result = Model_Quest::getQuestAns($aAWhere);
		if (count($result))
		{
			foreach ($result as $aA)
			{
				$aAns[$aA['qbID']][$aA['qqSort']] = $aA;
			}
		}

		# タイトル
		$sTitle = __('回答内容一覧').' ['.$aStudent['stNO'].']'.$aStudent['stName'];
		$this->template->set_global('pagetitle',$sTitle);
		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/quest','name'=>__('アンケート'));
		$this->aBread[] = array('link'=>'/quest/putlist','name'=>__('提出一覧'));
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		$this->template->content = View::forge($this->bn.DS.'stuanslist');
		$this->template->content->set('aQuest',$aQuest);
		$this->template->content->set('aPut',$aPut);
		$this->template->content->set('aQuery',$aQuery);
		$this->template->content->set('aAns',$aAns);
		$this->template->content->set('iQNum',$iQNum);
		$this->template->content->set('aStudent',$aStudent);
		$this->template->javascript = array('cl.t.quest.js');
		return $this->template;
	}

	public function action_archive()
	{
		if (!$this->aClass['scNum'])
		{
			Session::set('SES_T_ERROR_MSG',__('履修学生がいないため、アーカイブ作成はできません。'));
		}
		$result = Model_Quest::getQuestBaseFromClass($this->aClass['ctID'],null,null,array('qb.qbSort'=>'desc'));
		if (!count($result))
		{
			Session::set('SES_T_ERROR_MSG',__('アンケートが1件もないため、アーカイブ作成はできません。'));
		}
		else
		{
			shell_exec('/usr/bin/php '.CL_OILPATH.' r execquestansarchive '.$this->aClass['ctID'].' '.$this->aTeacher['ttID'].' '.$this->sLang.' > /dev/null 2>&1 &');
			Session::set('SES_T_NOTICE_MSG',__('アーカイブ作成を開始しました。\nアーカイブ作成には時間がかかる場合があります。\n作成が完了すると、この画面上にダウンロードボタンが表示されます。'));
		}
		Response::redirect(DS.$this->bn);
	}

	private function QuestChecker($sQbID = null,&$aQuest = null)
	{
		if (is_null($sQbID))
		{
			return array('msg'=>__('アンケート情報が送信されていません。'),'url'=>DS.$this->bn);
		}
		$result = Model_Quest::getQuestBaseFromID($sQbID);
		if (!count($result))
		{
			return array('msg'=>__('指定されたアンケートが見つかりません。'),'url'=>DS.$this->bn);
		}
		$aQuest = $result->current();

		return true;
	}

	private function QueryChecker($sQbID = null, $iQqNO = null, &$aQuery = null)
	{
		if (is_null($sQbID) || is_null($iQqNO))
		{
			return array('msg'=>__('必要な情報が送信されていません。'),'url'=>DS.$this->bn);
		}
		$result = Model_Quest::getQuestQuery(array(array('qbID','=',$sQbID),array('qqNO','=',$iQqNO)));
		if (!count($result))
		{
			return array('msg'=>__('指定されたアンケート設問が見つかりません。'),'url'=>DS.$this->bn);
		}
		$aQuery = $result->current();

		return true;
	}

	private function QuestAnsChecker($sMode,$sQbID,&$aQuest,&$aQuery,$sTtID = null)
	{
		if (is_null($sQbID))
		{
			return array('msg'=>__('アンケート情報が送信されていません。'),'url'=>DS.$this->bn);
		}

		switch ($sMode)
		{
			case 'ans':
				$result = Model_Quest::getQuestBaseFromClass($this->aClass['ctID'],array(array('qb.qbID','=',$sQbID),array('qb.qbPublic','=',1)));
				if (!count($result))
				{
					return array('msg'=>__('回答可能なアンケート情報が見つかりませんでした。'),'url'=>DS.$this->bn);
				}
				$aQuest = $result->current();

				if (!is_null($sTtID) && !$aQuest['qbReAnswer'])
				{
					$result = Model_Quest::getQuestPut(array(array('qp.qbID','=',$sQbID),array('qp.stID','=',$sTtID)));
					if (count($result))
					{
						return array('msg'=>__('指定のアンケートは既に回答済みです。'),'url'=>DS.$this->bn);
					}
				}
			break;
			case 'result':
				$result = Model_Quest::getQuestBaseFromClass($this->aClass['ctID'],array(array('qb.qbID','=',$sQbID),array('qb.qbPublic','!=',0)));
				if (!count($result))
				{
					return array('msg'=>__('指定のアンケート情報が見つかりませんでした。'),'url'=>DS.$this->bn);
				}
				$aQuest = $result->current();

				if (is_null($sTtID))
				{
					return array('msg'=>__('指定の先生が確認できませんでした。'),'url'=>DS.$this->bn);
				}
				$result = Model_Quest::getQuestPut(array(array('qp.qbID','=',$sQbID),array('qp.stID','=',$sTtID)));
				if (!count($result))
				{
					return array('msg'=>__('未回答アンケートの結果を閲覧することはできません。'),'url'=>DS.$this->bn);
				}
			break;
		}
		$result = Model_Quest::getQuestQuery(array(array('qbID','=',$sQbID)),null,array('qqSort'=>'asc'));
		if (!count($result))
		{
			return array('msg'=>__('指定のアンケートには設問がありません。'),'url'=>DS.$this->bn);
		}
		$aQuery = $result->as_array();

		return true;
	}











}