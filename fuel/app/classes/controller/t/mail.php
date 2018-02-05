<?php
class Controller_T_Mail extends Controller_T_Baseclass
{
	private $aMailBase = array(
		'm_name'=>null,
		'm_subject'=>null,
		'm_body'=>null,
	);
	private $sUrl = '/t/contact';

	public function before()
	{
		parent::before();
		$this->template->javascript = array('cl.t.student.js');
	}

	public function action_index()
	{
		$aHist = null;
		$result = Model_Class::getMailHistory($this->aClass['ctID'],null,null,array('no'=>'desc'));
		if (count($result))
		{
			$aHist = $result->as_array();
		}

		# タイトル
		$sTitle = __('一括連絡履歴');
		$this->template->set_global('pagetitle',$sTitle);

		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/contact','name'=>__('連絡・相談'));
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);
		$this->template->set_global('pagetitle',$sTitle);

		$this->template->content = View::forge('t/mail/index');
		$this->template->content->set('aHist',$aHist);
		return $this->template;
	}


	public function action_send()
	{
		if (!$this->aClass['ctStatus'])
		{
			Session::set('SES_T_ERROR_MSG', __('終了した講義から学生に連絡することはできません。'));
			Response::redirect($this->sUrl);
		}

		if (!Input::post(null,false))
		{
			Response::redirect($this->sUrl);
		}

		$aInput = Input::post();

		switch ($aInput['func'])
		{
			case 'report':
				$this->sBack = '/t/report/put/'.$aInput['rb'];
				$result = Model_Report::getReportBase(array(array('rb.rbID','=',$aInput['rb'])));
				if (!count($result))
				{
					Session::set('SES_T_ERROR_MSG', __('指定されたレポートが見つかりません。'));
					Response::redirect($this->sUrl);
				}
				$aReport = $result->current();
				$this->template->set_global('aReport',$aReport);

				$sMailTemplate = __('レポート').'['.$aReport['rbTitle'].']'."\n".CL_URL.'/s/report/';
				$this->template->set_global('sMailTemplate',$sMailTemplate);

				$this->aBread[] = array('link'=>'/report','name'=>__('レポート'));
				$this->aBread[] = array('link'=>'/report/put/'.$aInput['rb'],'name'=>$aReport['rbTitle'].'｜'.__('提出状況'));

				$sReturn = $this->sBack;
			break;
			default:
				$sMailTemplate = '';
				$this->template->set_global('sMailTemplate',$sMailTemplate);

				$this->aBread[] = array('link'=>'/contact','name'=>__('連絡・相談'));
				$this->aBread[] = array('link'=>'/mail','name'=>__('一括連絡履歴'));
				$sReturn = '/t/mail';
			break;
		}

		# タイトル
		$sTitle = __('学生への連絡');
		# パンくずリスト生成
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);
		$this->template->set_global('pagetitle',$sTitle);

		if (!Input::post(null,false))
		{
			Response::redirect($this->sUrl);
		}

		$aInput = Input::post();

		$aMail = null;
		if (Session::get('SES_T_MAILSEND_LIST', false))
		{
			$aMail = unserialize(Session::get('SES_T_MAILSEND_LIST'));
		}
		$aStudent = null;
		if (Session::get('SES_T_CONTACT_LIST',false))
		{
			$aStudent = unserialize(Session::get('SES_T_CONTACT_LIST'));
		}
		$this->template->set_global('aStudent',$aStudent);

		switch ($aInput['mode'])
		{
			case 'select':
				try
				{
					if (isset($aInput['StuChk']))
					{
						$aStudent = null;
						$aMail = null;
						Session::delete('SES_T_MAIL_SEND_LIST');
						$aStu = $aInput['StuChk'];
						if (!count($aStu))
						{
							throw new Exception(__('連絡先の学生が選択されていません。'));
						}
						$result = Model_Student::getStudentFromClass($this->aClass['ctID'],array(array('sp.stID','IN',$aStu)),null,array('st.stNO'=>'asc','st.stName'=>'acs','st.stLogin'=>'acs'));
						if (!count($result))
						{
							throw new Exception(__('連絡先の学生が見つかりません。'));
						}
						$aRes = $result->as_array();
						foreach ($aRes as $aR)
						{
							$aStudent[$aR['stID']]['name'] = $aR['stName'];
							if ($aR['stMail'] || $aR['stSubMail'])
							{
								$aMail[$aR['stID']]['name'] = $aR['stName'];
								if ($aR['stMail'])
								{
									$aMail[$aR['stID']]['main'] = $aR['stMail'];
								}
								if ($aR['stSubMail'])
								{
									$aMail[$aR['stID']]['sub'] = $aR['stSubMail'];
								}
							}
							if ($aR['stApp'] && $aR['stDeviceToken'])
							{
								$aMail[$aR['stID']]['stApp'] = $aR['stApp'];
								$aMail[$aR['stID']]['stDeviceToken'] = $aR['stDeviceToken'];
							}
						}
						if (is_null($aStudent))
						{
							throw new Exception(__('連絡先の学生が見つかりません。'));
						}
						Session::set('SES_T_CONTACT_LIST', serialize($aStudent));
						Session::set('SES_T_MAILSEND_LIST', serialize($aMail));
						$this->template->set_global('aStudent',$aStudent);
					}
					else if (is_null($aStudent))
					{
						throw new Exception(__('連絡先の学生が指定されていません。'));
					}

					$data = $this->aMailBase;
					$data['m_name'] = $this->aTeacher['ttName'];
					if (!is_null($this->aAssistant))
					{
						$data['m_name'] = $this->aAssistant['atName'];
					}
					$data['error'] = null;
					$this->template->content = View::forge('t/mail/edit',$data);
					return $this->template;
				}
				catch (Exception $e)
				{
					Session::set('SES_T_ERROR_MSG', $e->getMessage());
					Response::redirect($this->sUrl);
				}
			break;
			case 'input':
				$val = Validation::forge();
				$val->add_callable('Helper_CustomValidation');

				$val->add('m_name', __('送信者名'))
					->add_rule('required')
					->add_rule('trim')
					->add_rule('max_length', 50);

				$val->add('m_subject', __('件名'))
					->add_rule('required')
					->add_rule('trim')
					->add_rule('max_length', 50);

				$val->add('m_body', __('本文'))
					->add_rule('required')
					->add_rule('trim');

				if (!$val->run())
				{
					$aInput['error'] = $val->error();
					$this->template->content = View::forge('t/mail/edit',$aInput);
					return $this->template;
				}

				Session::set('SES_T_MAIL_SET',serialize($aInput));
				$this->template->content = View::forge('t/mail/check',$aInput);
				return $this->template;
			break;
			case 'check':
				if (!Session::get('SES_T_MAIL_SET',false))
				{
					Session::set('SES_T_ERROR_MSG', __('情報が見つかりませんでした。再度、実施してください。'));
					Response::redirect($this->sUrl);
				}
				$aSesInput = unserialize(Session::get('SES_T_MAIL_SET'));

				if (isset($aInput['back']))
				{
					$aSesInput['error'] = null;
					$this->template->content = View::forge('t/mail/edit',$aSesInput);
					return $this->template;
				}

				$aInsert = array(
					'ttID'         => $this->aTeacher['ttID'],
					'ctID'         => $this->aClass['ctID'],
					'ttName'       => $aSesInput['m_name'],
					'cmNum'        => count($aStudent),
					'cmSubject'    => $aSesInput['m_subject'],
					'cmBody'       => $aSesInput['m_body'],
					'cmSendMember' => base64_encode(serialize($aStudent)),
					'cmDate'       => date('YmdHis'),
				);

				$sCoID = $this->aTeacher['ttID'];
				if (!is_null($this->aAssistant))
				{
					$sCoID = $this->aAssistant['atID'];
				}
				$aContact = array(
					'parent'    => 0,
					'ctID'      => $this->aClass['ctID'],
					'coID'      => $sCoID,
					'coSubject' => $aSesInput['m_subject'],
					'coBody'    => $aSesInput['m_body'],
					'coName'    => $aSesInput['m_name'],
					'coDate'    => date('YmdHis'),
					'coTeach'   => 1,
				);

				try
				{
					if (!is_null($aMail))
					{
						\Clfunc_Mailsend::FreeMailSendToStudents($this->aTeacher,$aMail,$aSesInput['m_name'],$aSesInput['m_subject'],$aSesInput['m_body'],$this->aAssistant, $this->aClass, true);
					}
					$no = Model_Class::insertMailHistory($aInsert);

					foreach ($aStudent as $sStID => $aS)
					{
						$aContactInsert = $aContact;
						$aContactInsert['stID'] = $sStID;
						$res = Model_Contact::insertContact($aContactInsert);
					}
				}
				catch (Exception $e)
				{
					\Clfunc_Common::LogOut($e,__CLASS__);
					Session::set('SES_T_ERROR_MSG',$e->getMessage());
					Response::redirect($this->eRedirect);
				}

				$sMsg = __(':num名の学生へ連絡しました。',array('num'=>count($aStudent)));
				if (!is_null($aMail))
				{
					$sMsg .= __('また、連絡内容を:num名の学生にメール送信しました。',array('num'=>count($aMail)));
				}


				Session::delete('SES_T_MAIL_SEND_LIST');
				Session::delete('SES_T_MAIL_SET');
				Session::set('SES_T_NOTICE_MSG', $sMsg);

				Response::redirect($sReturn);
			break;
		}
	}




}