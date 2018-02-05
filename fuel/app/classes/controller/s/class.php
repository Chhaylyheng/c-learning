<?php
class Controller_S_Class extends Controller_S_Base
{
	private $sCode = null;

	public function action_index($sCtID = null)
	{
		if (is_null($sCtID))
		{
			Session::set('SES_S_ERROR_MSG',__('講義が指定されていません。'));
			Response::redirect('/s/index'.$this->sesParam);
		}
		$result = Model_Class::getClassFromStudent($this->aStudent["stID"],1,$sCtID, null, null, false);
		if (!count($result)) {
			Session::set('SES_S_ERROR_MSG',__('指定された講義が見つかりません。'));
			Response::redirect('/s/index'.$this->sesParam);
		}
		$aClass = $result->current();
		\Session::set('CL_STU_ACTIVE_CLASS_'.$this->aStudent['stID'], array('DATE' => strtotime('+5 minutes'), 'CLASS' => $aClass));

		if (Cookie::get('CL_COOKIE_CHK',false))
		{
			Cookie::set('SES_S_CLASS_ID',$sCtID);
		}
		else
		{
			Session::set('SES_S_CLASS_ID',$sCtID);
		}

		# 講義強制取得判定
		$iStuProfile = Clfunc_Common::getStudentProfileRead($this->aStudent);
		$iGetFlag = $aClass['ctStudentGetFlag'] ^ $iStuProfile & $aClass['ctStudentGetFlag'];

		if ($iGetFlag > 0)
		{
			Response::redirect('/s/class/getprofile/'.$sCtID);
		}

		$result = \Model_Teacher::getTeacherPosition(array(array('ctID','=',$aClass['ctID']),array('tpMaster','=',1)));
		if (count($result))
		{
			$aTP = $result->current();
			$result = \Model_Contract::getContract(array(array('ttID','=',$aTP['ttID']),array('ptID','!=',99),array('coStartDate','<=',\DB::expr('NOW()')),array('coTermDate','>=',\DB::expr('NOW()'))));
			if (!count($result))
			{
				$this->bQuickTeacher = true;
			}
			if (!is_null($this->aGroup))
			{
				$this->bQuickTeacher = false;
			}
		}
		$this->template->set_global('bQuickTeacher',$this->bQuickTeacher);

		$aNews = null;
		$sNow = date('Y-m-d H:i:s');
		$aWhere = array(array('cnStart','<=',$sNow),array('cnEnd','>=',$sNow));
		$result = Model_Class::getNews($sCtID,$aWhere);
		if (count($result))
		{
			foreach ($result as $i => $aN)
			{
				$aNews[$i] = $aN;
				$aNews[$i]['cnChain'] = ($aN['cnURL'])? \Clfunc_Common::ExtUrlDetectForStudent($aN['cnURL'], $this->aStudent['stID']):null;
			}
		}
		$this->template->set_global('aClassNews',$aNews);

		# 未読情報取得
		$oUC = new ClFunc_UnreadCount();
		$oUC->setClass($aClass);
		$oUC->setStudent($this->aStudent);
		$this->template->set_global('iContact',$oUC->getContact());
		$this->template->set_global('iMaterial',$oUC->getMaterial());
		$this->template->set_global('iCoop',$oUC->getCoop());

		$aAttend = null;
		$result = Model_Attend::getAttendCalendarActive($sCtID);
		if (count($result))
		{
			$aAttend = $result->current();
			$aAttend['already'] = false;
			$aWhere = array(array('ctID','=',$aClass['ctID']),array('abDate','=',$aAttend['abDate']),array('acNO','=',$aAttend['acNO']),array('amAttendState','>',0));
			$result = Model_Attend::getAttendBookFromStudent($this->aStudent['stID'],$aWhere);
			if (count($result))
			{
				$res = $result->current();
				$aAttend['already'] = true;
				$aAttend['abData'] = array(
					'AttendTime' => date('H:i',strtotime($res['abAttendDate'])),
					'amName'     => $res['amName'],
					'amAbsence'  => $res['amAbsence'],
					'amTime'     => $res['amTime'],
				);
			}
		}

		$sPWH = '';
		$sSep = '';
		if ($aClass['dpNO'])
		{
			$sPWH .= $this->aPeriod[$aClass['dpNO']];
			$sSep = '/';
		}
		if ($aClass['ctWeekDay'])
		{
			$sPWH .= $sSep.$this->aWeekday[$aClass['ctWeekDay']];
			$sSep = '/';
		}
		if ($aClass['dhNO'])
		{
			$sPWH .= $sSep.$this->aHour[$aClass['dhNO']];
		}
		if ($sPWH)
		{
			$sPWH = '（'.$sPWH.'）';
		}

		# タイトル
		$sTitle = '<i class="fa fa-book fa-fw"></i>'.$aClass['ctName'].$sPWH;
		$this->template->set_global('pagetitle',__('講義トップ'));
		$this->template->set_global('classtitle',$sTitle,false);
		$this->template->set_global('subtitle',__('講義コード').'［'.\Clfunc_Common::getCode($aClass['ctCode']).'］');

		$sTitle = $aClass['ctName'];
		# パンくずリスト生成
		$this->template->set_global('breadcrumbs',array(array('name'=>$sTitle)));

		$this->template->content = View::forge($this->vDir.DS.'class/index');

		$aMCategory = null;
		if (CL_CAREERTASU_MODE)
		{
			$result = Model_Material::getMaterialCategoryFromClass($aClass['ctID'],null,null,array('mcSort'=>'desc'));
			if (count($result))
			{
				$aMCategory = $result->as_array('mcID');
			}

			$aCnt = null;
			$result = Model_Material::getMaterialAlreadyCountFromStudent($this->aStudent['stID']);
			if (count($result))
			{
				$aCnt = $result->as_array('mcID');
			}

			if (!is_null($aMCategory))
			{
				foreach ($aMCategory as $sID => $aMC)
				{
					if (count($aMCategory) == 1)
					{
						# Response::redirect('/s/material/list/'.$sID);
					}
					$aMCategory[$sID]['already'] = 0;
					if (isset($aCnt[$sID]))
					{
						$aMCategory[$sID]['already'] = (int)$aCnt[$sID]['aCnt'];
					}
				}
			}

			if ($aClass['ctCode'] != '1000')
			{
				# カスタムメニュー
				$aCustomMenu = array(
					array(
						'url'  => '/s/class/remove/'.$sCtID,
						'name' => __('履修の解除'),
						'show' => 0,
					),
				);
				$this->template->set_global('aCustomMenu',$aCustomMenu);
			}

			$aTeacher = null;
			$result = Model_Teacher::getTeacherFromID($aTP['ttID']);
			if (count($result))
			{
				$aTeacher = $result->current();
			}
			$this->template->content->set('aITSTeacher',$aTeacher);
		}

		$this->template->content->set('aClass',$aClass);
		$this->template->content->set('aAttend',$aAttend);
		$this->template->content->set('aMCategory',$aMCategory);
		$this->template->javascript = array('cl.s.class.js');
		return $this->template;
	}

	public function action_entry()
	{
		if (!is_null($this->aGroup) && ($this->aGroup['gtStudentAuthFlag'] & \Clfunc_Flag::S_AUTH_STADY))
		{
			Response::redirect('s/index'.$this->sesParam);
		}

		# タイトル
		$sTitle = __('履修登録');
		# パンくずリスト生成
		$aBread = array(array('name'=>$sTitle));
		$this->template->set_global('breadcrumbs',$aBread);
		$this->template->set_global('pagetitle',$sTitle);
		$this->template->set_global('aClass',null);

		$this->sCode = Input::post('c_code',false);
		$data['c_code'] = $this->sCode;
		if (!$this->sCode)
		{
			Session::set('SES_S_ERROR_MSG',__('履修する講義の講義コードを入力してください。'));
			Response::redirect('s/index'.$this->sesParam);
		}

		$aClass = null;
		$result = Model_Class::getClassFromCode($this->sCode,1);
		if (!count($result))
		{
			Session::set('SES_S_ERROR_MSG',__('指定の講義コードに該当する講義はありません。'));
			Response::redirect('s/index'.$this->sesParam);
		}
		$aClass = $result->current();

		if ($aClass['gtID'])
		{
			$result = Model_Group::getGroup(array(array('gb.gtID','=',$aClass['gtID'])));
			if (!count($result))
			{
				Session::set('SES_S_ERROR_MSG',__('指定の講義コードに該当する講義はありません。'));
				Response::redirect('s/index'.$this->sesParam);
			}
			$aGC = $result->current();

			if ($aGC['gtLDAP'] && ($this->aGroup['gtID'] != $aGC['gtID']))
			{
				Session::set('SES_S_ERROR_MSG',__('指定の講義コードに該当する講義はありません。'));
				Response::redirect('s/index'.$this->sesParam);
			}

			if (isset($this->aGroup['gtID']) && $this->aGroup['gtID'] != $aClass['gtID'])
			{
				Session::set('SES_S_ERROR_MSG',__('指定の講義コードに該当する講義はありません。'));
				Response::redirect('s/index'.$this->sesParam);
			}
		}


		$result = Model_Class::getClassFromStudent($this->aStudent['stID'],null,$aClass['ctID']);
		if (count($result))
		{
			Session::set('SES_S_NOTICE_MSG',__('指定の講義には既に履修済みです。'));
			Response::redirect('s/index'.$this->sesParam);
		}

		if (!Input::post('c_check',false))
		{
			$data['class'] = $aClass;
			$data['error'] = null;
			$this->template->content = View::forge($this->vDir.DS.'class/entryCheck',$data);
			return $this->template;
		}

		try
		{
			$result = Model_Class::entryClass($aClass['ctID'],$this->aStudent['stID']);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_S_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		Session::delete('CL_STU_CLASS_LIST_'.$this->aStudent['stID']);
		Session::set('SES_S_NOTICE_MSG',__(':classを履修しました。',array('class'=>$aClass['ctName'])));
		Response::redirect('s/index'.$this->sesParam);
	}


	public function action_remove($sCtID = null)
	{
		if (!CL_CAREERTASU_MODE)
		{
			Response::redirect('s/index'.$this->sesParam);
		}

		if (is_null($sCtID))
		{
			Session::set('SES_S_ERROR_MSG',__('講義が指定されていません。'));
			Response::redirect('/s/index'.$this->sesParam);
		}
		$result = Model_Class::getClassFromStudent($this->aStudent['stID'],null,$sCtID,array(array('ct.ctCode','!=','1000')));
		if (!count($result))
		{
			Session::set('SES_S_ERROR_MSG',__('指定された講義が見つかりません。'));
			Response::redirect('/s/index'.$this->sesParam);
		}
		$aTemp = $result->as_array();
		$aClass = $aTemp[0];

		# タイトル
		$sTitle = __('履修の解除');
		# パンくずリスト生成
		$aBread = array(array('name'=>$sTitle));
		$this->template->set_global('breadcrumbs',$aBread);
		$this->template->set_global('pagetitle',$sTitle);
		$this->template->set_global('aClass',null);

		$post = Input::post(null,false);
		if ($post)
		{
			if (isset($post['cancel']))
			{
				Response::redirect('s/class/index/'.$sCtID.$this->sesParam);
			}

			try
			{
				$result = Model_Class::removeClass($sCtID,$this->aStudent['stID']);
			}
			catch (Exception $e)
			{
				\Clfunc_Common::LogOut($e,__CLASS__);
				Session::set('SES_S_ERROR_MSG',$e->getMessage());
				Response::redirect($this->eRedirect);
			}

			Session::delete('CL_STU_CLASS_LIST_'.$this->aStudent['stID']);
			Session::set('SES_S_NOTICE_MSG',__(':classを履修から解除しました。',array('class'=>$aClass['ctName'])));
			Response::redirect('/s/index'.$this->sesParam);
		}

		$data['class'] = $aClass;
		$this->template->content = View::forge($this->vDir.DS.'class/removeCheck',$data);
		return $this->template;
	}




	public function action_delete($sCtID = null)
	{
		if (is_null($sCtID))
		{
			Session::set('SES_S_ERROR_MSG',__('講義が指定されていません。'));
			Response::redirect('/s/index'.$this->sesParam);
		}
		$result = Model_Class::getClassFromStudent($this->aStudent['stID'],null,$sCtID);
		if (!count($result))
		{
			Session::set('SES_S_ERROR_MSG',__('指定された講義が見つかりません。'));
			Response::redirect('/s/index'.$this->sesParam);
		}
		$aTemp = $result->as_array();
		$aClass = $aTemp[0];

		try
		{
			$result = Model_Class::removeClass($sCtID,$this->aStudent['stID']);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_S_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}
		Session::set('SES_S_NOTICE_MSG',__(':classを履修から削除しました。',array('class'=>$aClass['ctName'])));
		Response::redirect('/s/index'.$this->sesParam);
	}


	public function action_getprofile($sCtID = null)
	{
		$this->template->javascript = array('cl.school_select.js');

		if (is_null($sCtID))
		{
			Session::set('SES_S_ERROR_MSG',__('講義が指定されていません。'));
			Response::redirect('/s/index'.$this->sesParam);
		}
		$result = Model_Class::getClassFromStudent($this->aStudent["stID"],1,$sCtID, null, null, false);
		if (!count($result)) {
			Session::set('SES_S_ERROR_MSG',__('指定された講義が見つかりません。'));
			Response::redirect('/s/index'.$this->sesParam);
		}
		$aClass = $result->current();

		# 講義強制取得判定
		$iStuProfile = Clfunc_Common::getStudentProfileRead($this->aStudent);
		$iGetFlag = $aClass['ctStudentGetFlag'] ^ $iStuProfile & $aClass['ctStudentGetFlag'];

		if (!$iGetFlag)
		{
			Response::redirect('/s/class/index/'.$sCtID);
		}

		$aFlag  = \Clfunc_Common::dec2Bits((int)$aClass['ctStudentGetFlag']);
		$aGet = \Clfunc_Flag::getStuGetFlag();

		$this->template->set_global('aClass', $aClass);
		$this->template->set_global('sCtID', $sCtID);
		$this->template->set_global('aFlag', $aFlag);
		$this->template->set_global('aGet', $aGet);

		# 基本データ登録
		$data['s_no']      = $this->aStudent['stNO'];
		$data['s_sex']     = $this->aStudent['stSex'];
		$data['s_school']  = $this->aStudent['stSchool'];
		$data['s_dept']    = $this->aStudent['stDept'];
		$data['s_subject'] = $this->aStudent['stSubject'];
		$data['s_year']    = $this->aStudent['stYear'];
		$data['s_class']   = $this->aStudent['stClass'];
		$data['s_course']  = $this->aStudent['stCourse'];
		$data['s_mail']    = $this->aStudent['stMail'];

		if (!Input::post(null,false))
		{
			$data['error'] = null;
			$this->template->content = View::forge($this->vDir.DS.'firstprofile',$data);
			return $this->template;
		}

		$aInput = Input::post();

		$val = Validation::forge();
		$val->add_callable('Helper_CustomValidation');

		$aUpdate = array();

		if (isset($aInput['s_no']))
		{
			$val->add('s_no', __('学籍番号'))
			->add_rule('trim')
			->add_rule('required')
			->add_rule('max_length', 20)
			->add_rule('valid_string', array('alpha','numeric','dashes','dots','utf8'));

			$aUpdate['stNO'] =  $aInput['s_no'];
		}

		if (isset($aInput['s_school']))
		{
			$val->add('s_school', __('学校名'))
			->add_rule('required')
			->add_rule('trim')
			->add_rule('max_length',50);
		}

		if (isset($aInput['s_sex']))
		{
			$aUpdate['stSex'] =  (int)$aInput['s_sex'];
		}
		if (isset($aInput['s_dept']))
		{
			$val->add('s_dept', __('学部'))
			->add_rule('trim')
			->add_rule('required')
			->add_rule('max_length',50);

			$aUpdate['stDept'] =  $aInput['s_dept'];
		}
		if (isset($aInput['s_subject']))
		{
			$val->add('s_subject', __('学科'))
			->add_rule('trim')
			->add_rule('required')
			->add_rule('max_length',50);

			$aUpdate['stSubject'] =  $aInput['s_subject'];
		}
		if (isset($aInput['s_year']))
		{
			$val->add('s_year', __('学年'))
			->add_rule('trim')
			->add_rule('required')
			->add_rule('numeric_min', 1)
			->add_rule('valid_string', array('numeric','utf8'));

			$aUpdate['stYear'] =  $aInput['s_year'];
		}
		if (isset($aInput['s_class']))
		{
			$val->add('s_class', __('クラス'))
			->add_rule('trim')
			->add_rule('required')
			->add_rule('max_length',50);

			$aUpdate['stClass'] =  $aInput['s_class'];
		}
		if (isset($aInput['s_course']))
		{
			$val->add('s_course', __('コース'))
			->add_rule('trim')
			->add_rule('required')
			->add_rule('max_length',50);

			$aUpdate['stCourse'] =  $aInput['s_course'];
		}
		$sMain = '';
		if (isset($aInput['s_mail']))
		{
			$val->add('s_mail', __('メールアドレス'))
			->add_rule('required')
			->add_rule('valid_email')
			->add_rule('max_length',200)
			->add_rule('smail_chk',$this->aStudent['stID']);

			if ($this->aStudent['stMail'] != $aInput['s_mail'])
			{
				$aUpdate['stMail'] =  trim($aInput['s_mail']);
				$aUpdate['stMailAuth'] = 0;
				$sMain = "\n".__('「メールアドレス認証メール」を送信しましたので、メール内のURLにアクセスして認証してください。');
			}
		}
		if (!$val->run())
		{
			$data = array_merge($data,$aInput);
			$data['error'] = $val->error();
			$data['error']['profile_error'] = __('変更に失敗しました。入力内容をご確認ください。');
			$this->template->content = View::forge($this->vDir.DS.'firstprofile',$data);
			return $this->template;
		}

		$sCmKCode = '';

		if (isset($aInput['s_school']))
		{
			$result = Model_College::getCollegeFromName($aInput['s_school']);
			$row = $result->current();
			if (!empty($row))
			{
				$sCmKCode = $row['cmKCode'];
			}
			else
			{
				$sCmKCode = Model_College::setCollege($aInput['s_school']);
			}
			$aUpdate['cmKCode'] = $sCmKCode;
			$aUpdate['stSchool'] = $aInput['s_school'];
		}

		try
		{
			$result = Model_Student::updateStudent($this->aStudent['stID'],$aUpdate);

			if (isset($aUpdate['stMail']))
			{
				// 認証メール送信
				$email = \Email::forge();
				$email->from(CL_MAIL_FROM, CL_MAIL_SENDER);
				$email->to($aInput['s_mail']);
				$email->subject('[CL]'.__('メールアドレス認証メール'));

				$html_body = View::forge('email/s_mailauth_html');
				$html_body->set('aStudent', $this->aStudent, false);
				$html_body->set('sMail', $aUpdate['stMail'], false);
				$html_body->set('sHash', $this->aStudent['stHash'], false);
				$email->html_body($html_body);

				$body = View::forge('email/s_mailauth_plain');
				$body->set('aStudent', $this->aStudent, false);
				$body->set('sMail', $aUpdate['stMail'], false);
				$body->set('sHash', $this->aStudent['stHash'], false);
				$email->alt_body($body);

				try
				{
					$email->send();
				}
				catch (\EmailValidationFailedException $e)
				{
					Log::warning('StudentMailAuth - ' . $e->getMessage());
				}
				catch (\EmailSendingFailedException $e)
				{
					Log::warning('StudentMailAuth - ' . $e->getMessage());
				}
			}
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_S_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		Session::set('SES_S_NOTICE_MSG',__('登録が完了しました。').$sMain);
		Response::redirect('s/class/index/'.$sCtID);
	}
}
