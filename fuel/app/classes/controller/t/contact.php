<?php
class Controller_T_Contact extends Controller_T_Baseclass
{
	private $aContact = null;
	private $baseName = 'contact';
	private $aContactBase = array(
		'c_subject'=>null,
		'c_text' =>null,
	);
	private $aSearchCol = array(
		'st.stLogin','st.stName','st.stNO','st.stDept','st.stSubject','st.stYear','st.stClass','st.stCourse'
	);

	public function before()
	{
		parent::before();
		\Clfunc_Common::ContractDetect($this->aTeacher, __CLASS__);
	}

	public function action_index()
	{
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
		$aContact = null;
		$result = Model_Student::getStudentFromClass($this->aClass['ctID'],null,null,array('st.stNO'=>'asc','st.stName'=>'acs','st.stLogin'=>'acs'),$aWords);
		if (count($result))
		{
			$aRes = $result->as_array();
			foreach ($aRes as $aR)
			{
				$aStudent[$aR['stID']] = $aR;
				$aContact[$aR['stID']] = array('num'=>0, 'unread'=>0);
			}
		}

		$result = Model_Contact::getContact($this->aClass['ctID'],null,null,null,array('co.stID'=>'asc'));
		if (count($result))
		{
			foreach ($result as $aC)
			{
				if (isset($aContact[$aC['stID']]))
				{
					if ($aC['parent'] == 0)
					{
						$aContact[$aC['stID']]['num']++;
					}
					if (!$aC['coTeach'] && !$aC['coRead'])
					{
						$aContact[$aC['stID']]['unread']++;
					}
				}
			}
		}

		# タイトル
		$sTitle = __('連絡・相談');
		$this->template->set_global('pagetitle',$sTitle);

		# パンくずリスト生成
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		# カスタムメニュー
		$aCustomMenu = null;
		$aCustomMenu[] = array(
			'url'  => '/t/mail',
			'name' => __('一括連絡履歴'),
			'show' => 0,
		);
		$aCustomMenu[] = array(
			'url'  => '/t/'.$this->baseName.'/thread',
			'name' => __('相談履歴'),
			'show' => 0,
		);
		$this->template->set_global('aCustomMenu',$aCustomMenu);

		# カスタムボタン
		$aCustomBtn = null;
		$aCustomBtn[] = array(
			'url'  => '',
			'name' => __('チェックした学生に連絡'),
			'show' => 1,
			'icon' => 'fa-envelope',
			'option' => array(
				'id' => 'StudentMailSend',
			),
		);
		$this->template->set_global('aCustomBtn',$aCustomBtn);

		$aSearchForm = array(
			'url' => '/t/'.$this->baseName,
		);
		$this->template->set_global('aSearchForm',$aSearchForm);
		$this->template->set_global('sWords',$sWords);

		$this->template->content = View::forge('t/'.$this->baseName.'/index');
		$this->template->content->set('aStudent',$aStudent);
		$this->template->content->set('aContact',$aContact);
		$this->template->javascript = array('cl.t.student.js');
		return $this->template;
	}


	public function action_thread($sStID = null)
	{
		$aStu = null;
		if (!is_null($sStID))
		{
			$result = Model_Student::getStudentFromClass($this->aClass['ctID'],array(array('sp.stID','=',$sStID)));
			if (!count($result))
			{
				Session::set('SES_T_ERROR_MSG',__('指定された学生は本講義を履修していません。'));
				Response::redirect('/t/contact');
			}
			$aStu = $result->current();
			$this->template->set_global('aStu',$aStu);
		}

		$aNew = null;
		$result = Model_Contact::getContact($this->aClass['ctID'],$sStID,null,null,array('co.no'=>'desc'));
		if (count($result))
		{
			foreach ($result as $aC)
			{
				if ($aC['parent'] == 0)
				{
					$this->aContact[$aC['no']]['P'] = $aC;
				}
				else
				{
					$this->aContact[$aC['parent']]['C'][$aC['no']] = $aC;
					if (!$aC['coTeach'] && !$aC['coRead'])
					{
						$aNew[$aC['parent']] = true;
					}
				}
			}
		}

		# タイトル
		$sTitle = __('相談履歴').((!is_null($aStu))? '｜'.$aStu['stName']:'');
		$this->template->set_global('pagetitle',$sTitle);
		# パンくずリスト生成
		$this->aBread[] = array('link'=>DS.$this->baseName, 'name'=>__('連絡・相談'));
		if (!is_null($aStu))
		{
			$this->aBread[] = array('link'=>DS.$this->baseName.DS.'thread', 'name'=>__('相談履歴'));
		}
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		# カスタムボタン
		if (!is_null($aStu))
		{
			$aCustomBtn = array(
				array(
					'url'  => '/t/'.$this->baseName.'/rescreate',
					'name' => __('新規作成'),
					'show' => 1,
					'icon' => 'fa-envelope-o',
					'class' => array('ContactCreate'),
				),
			);
			$this->template->set_global('aCustomBtn',$aCustomBtn);
		}

		$this->template->content = View::forge('t/'.$this->baseName.'/thread');
		$this->template->content->set('aContact',$this->aContact);
		$this->template->content->set('aNew',$aNew);
		$this->template->javascript = array('cl.t.contact.js','cl.contact.js');
		return $this->template;
	}


	public function action_res()
	{
		$url = '/t/'.$this->baseName;
		$sMsg = __('必要な情報を取得することができませんでした。');

		if (!Input::post(null,false))
		{
			Session::set('SES_T_ERROR_MSG',$sMsg);
			Response::redirect($url);
		}

		$aInput = Input::post();
		$sStID = $aInput['c_st'];

		if (is_null($sStID))
		{
			Session::set('SES_T_ERROR_MSG',$sMsg);
			Response::redirect($url);
		}
		$result = Model_Student::getStudentFromClass($this->aClass['ctID'],array(array('sp.stID','=',$sStID)));
		if (!count($result))
		{
			Session::set('SES_T_ERROR_MSG',__('指定された学生は本講義を履修していません。'));
			Response::redirect('/t/student');
		}
		$aStu = $result->current();
		$url .= '/thread/'.$sStID;

		$iNO = $aInput['c_no'];
		if ($iNO != 0)
		{
			$aChk = self::ContactChecker($iNO);
			if (is_array($aChk))
			{
				Session::set('SES_T_ERROR_MSG',$aChk['msg']);
				Response::redirect($aChk['url']);
			}
		}
		else if ($aInput['mode'] != 'pcreate')
		{
			Session::set('SES_T_ERROR_MSG',$sMsg);
			Response::redirect($url);
		}

		$val = Validation::forge();
		$val->add_callable('Helper_CustomValidation');
		$val->add_field('c_subject', __('件名'), 'max_length['.CL_TITLE_LENGTH.']');
		$val->add_field('c_text', __('本文'), 'required');
		if (!$val->run())
		{
			$sMsg = implode('<br>', $val->error());
			Session::set('SES_T_ERROR_MSG',$sMsg);
			Response::redirect($url);
		}

		if ($aInput['mode'] == 'pcreate')
		{
			try
			{
				$sCoID = $this->aTeacher['ttID'];
				$sCoName = $this->aTeacher['ttName'];
				$sCoMail = $this->aTeacher['ttMail'];
				$sCoSubMail = $this->aTeacher['ttSubMail'];
				if (!is_null($this->aAssistant))
				{
					$sCoID = $this->aAssistant['atID'];
					$sCoName = $this->aAssistant['atName'];
					$sCoMail = $this->aAssistant['atMail'];
					$sCoSubMail = '';
				}
				$aInsert = array(
					'parent'    => 0,
					'ctID'      => $this->aClass['ctID'],
					'stID'      => $sStID,
					'coID'      => $sCoID,
					'coSubject' => $aInput['c_subject'],
					'coBody'    => $aInput['c_text'],
					'coName'    => $sCoName,
					'coDate'    => date('YmdHis'),
					'coTeach'   => 1,
				);
				$result = \Model_Contact::insertContact($aInsert);
			}
			catch (Exception $e)
			{
				Session::set('SES_T_ERROR_MSG',__('送信に失敗しました。').$e->getMessage());
				Response::redirect($url);
			}
		}
		else
		{
			Session::set('SES_T_ERROR_MSG',$sMsg);
			Response::redirect($url);
		}

		$aOptions = array(
			'cID'      => $sCoID,
			'cMail'    => $sCoMail,
			'cName'    => $sCoName,
			'cSubMail' => $sCoSubMail,
			'cSubject' => $aInput['c_subject'],
			'cBody'    => $aInput['c_text'],
		);
		\ClFunc_Mailsend::MailSendToContact($this->aClass['ctID'],$sStID,'t',$aOptions);

		if (!$aStu['stMail'] && !$aStu['stSubMail'])
		{
			$sMsg = __(':nameさんはメールアドレス未登録のため、連絡をメール送信していません。',array('name'=>$aStu['stName']));
		}
		else
		{
			$sMsg = __('連絡を送信しました。');
		}

		Session::set('SES_T_NOTICE_MSG',$sMsg);
		Response::redirect($url);
	}


	private function ContactChecker($iNO = null)
	{
		if (is_null($this->aClass))
		{
			return array('msg'=>__('講義情報が確認できませんでした。'),'url'=>'/t/index');
		}
		if (is_null($iNO))
		{
			return array('msg'=>__('必要な情報が送信されていません。'),'url'=>'/t/'.$this->baseName);
		}
		$result = Model_Contact::getContact($this->aClass['ctID'],$this->aStudent['stID'],array(array('co.no','=',$iNO)));
		if (!count($result))
		{
			return array('msg'=>__('指定された連絡・相談が見つかりません。'),'url'=>'/t/'.$this->baseName);
		}
		$this->aContact = $result->current();

		return true;
	}


}
