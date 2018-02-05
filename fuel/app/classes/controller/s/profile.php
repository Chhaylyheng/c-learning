<?php
class Controller_S_Profile extends Controller_S_Base
{
	private function DeptList($sCmName = null)
	{
		$result = Model_College::getDeptListFromCollegeName($sCmName);
		if (count($result))
		{
			foreach ($result as $r)
			{
				$aDept[$r['dmName']] = $r['dmName'];
			}
		}
		else
		{
			$aDept = array(''=>__('学部指定なし'));
		}
		return $aDept;
	}

	public function before()
	{
		parent::before();
		# サブタイトル生成
		$this->template->set_global('aClass',null);
	}

	public function action_index()
	{
		$this->template->javascript = array('cl.school_select.js');

		$sTitle = __('アカウント設定');
		# パンくずリスト生成
		$this->template->set_global('breadcrumbs',array(array('name'=>$sTitle)));
		# ページタイトル生成
		$this->template->set_global('pagetitle',$sTitle);

		# 基本データ登録
		$data['s_name']     = $this->aStudent['stName'];
		$data['s_no']       = $this->aStudent['stNO'];
		$data['s_sex']      = $this->aStudent['stSex'];
		$data['s_school']   = $this->aStudent['stSchool'];
		$data['s_dept']     = $this->aStudent['stDept'];
		$data['s_subject']  = $this->aStudent['stSubject'];
		$data['s_year']     = $this->aStudent['stYear'];
		$data['s_class']    = $this->aStudent['stClass'];
		$data['s_course']   = $this->aStudent['stCourse'];
		$data['s_submail']  = $this->aStudent['stSubMail'];
		$data['s_timezone'] = $this->aStudent['stTimeZone'];

		# タイムゾーンの取得
		$tz_list = ClFunc_Tz::tz_list();
		$tz_region = ClFunc_Tz::$regions;
		$this->template->set_global('tz_list',$tz_list);
		$this->template->set_global('tz_region',$tz_region);

		if (!Input::post(null,false))
		{
			$data['error'] = null;
			$this->template->content = View::forge($this->vDir.DS.'profile/index',$data);
			return $this->template;
		}

		$aInput = Input::post();

		switch ($aInput['mode'])
		{
			case 'profile':
				$val = Validation::forge();
				$val->add_callable('Helper_CustomValidation');

				if (is_null($this->aGroup) || !($this->aGroup['gtStudentProfFlag'] & \Clfunc_Flag::S_PROF_NAME))
				{
					$val->add('s_name', __('氏名'))
						->add_rule('required')
						->add_rule('max_length',50);
				}
				if (isset($aInput['s_no']))
				{
					$val->add('s_no', __('学籍番号'))
						->add_rule('trim')
						->add_rule('max_length', 20)
						->add_rule('valid_string', array('alpha','numeric','dashes','dots','utf8'));
					if (is_null($this->aGroup) || ($this->aGroup['gtStudentGetFlag'] & \Clfunc_Flag::S_GET_NO))
					{
						$val->field('s_no')->add_rule('required');
					}
				}

				if (isset($aInput['s_school']))
				{
					$val->add('s_school', __('学校'))
					->add_rule('required')
					->add_rule('trim')
					->add_rule('max_length',50);
				}

				if (isset($aInput['s_dept']))
				{
					$val->add('s_dept', __('学部'))
						->add_rule('trim')
						->add_rule('max_length',50);
					if (is_null($this->aGroup) || ($this->aGroup['gtStudentGetFlag'] & \Clfunc_Flag::S_GET_DEPT))
					{
						$val->field('s_dept')->add_rule('required');
					}
				}
				if (isset($aInput['s_subject']))
				{
					$val->add('s_subject', __('学科'))
						->add_rule('trim')
						->add_rule('max_length',50);
					if (is_null($this->aGroup) || ($this->aGroup['gtStudentGetFlag'] & \Clfunc_Flag::S_GET_SUBJECT))
					{
						$val->field('s_subject')->add_rule('required');
					}
				}
				if (isset($aInput['s_year']))
				{
					$val->add('s_year', __('学年'))
						->add_rule('trim')
						->add_rule('numeric_min', 1)
						->add_rule('valid_string', array('numeric','utf8'));
					if (is_null($this->aGroup) || ($this->aGroup['gtStudentGetFlag'] & \Clfunc_Flag::S_GET_YEAR))
					{
						$val->field('s_year')->add_rule('required');
					}
				}
				if (isset($aInput['s_class']))
				{
					$val->add('s_class', __('クラス'))
						->add_rule('trim')
						->add_rule('max_length',50);
					if (is_null($this->aGroup) || ($this->aGroup['gtStudentGetFlag'] & \Clfunc_Flag::S_GET_CLASS))
					{
						$val->field('s_class')->add_rule('required');
					}
				}
				if (isset($aInput['s_course']))
				{
					$val->add('s_course', __('コース'))
						->add_rule('trim')
						->add_rule('max_length',50);
					if (is_null($this->aGroup) || ($this->aGroup['gtStudentGetFlag'] & \Clfunc_Flag::S_GET_COURSE))
					{
						$val->field('s_course')->add_rule('required');
					}
				}
			break;
			case 'mail':
				$val = Validation::forge();
				$val->add_callable('Helper_CustomValidation');
				$val->add('s_mail', __('新しいメールアドレス'))
					->add_rule('valid_email')
					->add_rule('max_length',200)
					->add_rule('smail_chk',$this->aStudent['stID']);
				$val->add('s_mail_chk', __('新しいメールアドレス（確認）'))
					->add_rule('match_field','s_mail');

				$val->add('s_submail', __('サブメールアドレス'))
					->add_rule('valid_email')
					->add_rule('max_length',200);
			break;
			case 'pass':
				$val = Validation::forge();
				$val->add_callable('Helper_CustomValidation');
				$val->add('s_pass_now', __('現在のパスワード'))
					->add_rule('required')
					->add_rule('passwd_true',$this->aStudent['stPass']);
				$val->add('s_pass_edit', __('新しいパスワード'))
					->add_rule('required')
					->add_rule('min_length',8)
					->add_rule('max_length',32)
					->add_rule('passwd_char')
					->add_rule('passwd_false',$this->aStudent['stPass']);
				$val->add('s_pass_chk', __('新しいパスワード（確認）'))
					->add_rule('required')
					->add_rule('match_field','s_pass_edit');
			break;
		}
		if (!$val->run())
		{
			$data = array_merge($data,$aInput);
			$data['error'] = $val->error();
			$data['error']['profile_error'] = __('変更に失敗しました。入力内容をご確認ください。');
			$this->template->content = View::forge($this->vDir.DS.'profile/index',$data);
			return $this->template;
		}

		switch ($aInput['mode'])
		{
			case 'profile':
				$aUpdate = array();

				if (is_null($this->aGroup) || !($this->aGroup['gtStudentProfFlag'] & \Clfunc_Flag::S_PROF_NAME))
				{
					$aUpdate['stName'] =  $aInput['s_name'];
				}
				if (isset($aInput['s_no']))
				{
					$aUpdate['stNO'] =  $aInput['s_no'];
				}
				if (isset($aInput['s_sex']))
				{
					$aUpdate['stSex'] =  (int)$aInput['s_sex'];
				}
				if (isset($aInput['s_dept']))
				{
					$aUpdate['stDept'] =  $aInput['s_dept'];
				}
				if (isset($aInput['s_subject']))
				{
					$aUpdate['stSubject'] =  $aInput['s_subject'];
				}
				if (isset($aInput['s_year']))
				{
					$aUpdate['stYear'] =  $aInput['s_year'];
				}
				if (isset($aInput['s_class']))
				{
					$aUpdate['stClass'] =  $aInput['s_class'];
				}
				if (isset($aInput['s_course']))
				{
					$aUpdate['stCourse'] =  $aInput['s_course'];
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

				$aUpdate['stTimeZone'] =  $aInput['s_timezone'];

				if (!count($aUpdate))
				{
					Response::redirect('s/profile'.$this->sesParam);
				}

				Session::set('SES_S_NOTICE_MSG',__('プロフィールの変更が完了しました。'));
			break;
			case 'mail':
				$sMain = '';
				$aUpdate = array(
					'stSubMail' => trim($aInput['s_submail']),
				);
				if (trim($aInput['s_mail']))
				{
					$aUpdate['stMail'] = trim($aInput['s_mail']);
					$aUpdate['stMailAuth'] = 0;
					$sMain = "\n".__('「メールアドレス認証メール」を送信しましたので、メール内のURLにアクセスして認証してください。');
				}
				Session::set('SES_S_NOTICE_MSG',__('メールアドレスの変更が完了しました。').$sMain);
			break;
			case 'pass':
				$aUpdate = array(
					'stFirst' => '',
					'stPass' => sha1($aInput['s_pass_edit']),
					'stPassDate' => date('Ymd'),
					'stHash' => sha1($this->aStudent['stLogin'].sha1($aInput['s_pass_edit'])),
				);
				Session::set('SES_S_NOTICE_MSG',__('パスワードの変更が完了しました。'));
			break;
		}
		try
		{
			$result = Model_Student::updateStudent($this->aStudent['stID'],$aUpdate);
			if (isset($aUpdate['stHash']))
			{
				if (Cookie::get('CL_COOKIE_CHK',false))
				{
					Cookie::delete('CL_SL_KEY');
					Cookie::set('CL_SL_HASH',Crypt::encode(serialize(array('hash'=>$aUpdate['stHash'],'ip'=>Input::real_ip()))));
				}
				else
				{
					Session::set('CL_SL_HASH',Crypt::encode(serialize(array('hash'=>$aUpdate['stHash'],'ip'=>Input::real_ip()))));
				}
			}
			if (isset($aUpdate['stMail']) && $aUpdate['stMail'])
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
			Session::delete('SES_S_NOTICE_MSG');
			Session::set('SES_S_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		Response::redirect('s/profile'.$this->sesParam);
	}

	public function action_remove()
	{
		$sTitle = __('会員の解約');

		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/profile','name'=>__('アカウント設定'));
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);
		# ページタイトル生成
		$this->template->set_global('pagetitle',$sTitle);

		$aInput = Input::post();

		if (isset($aInput['check']))
		{
			$this->template->content = View::forge($this->vDir.DS.'profile/removeCheck');
			return $this->template;
		}

		if (isset($aInput['cancel']))
		{
			Response::redirect('s/index'.$this->sesParam);
		}

		if (!isset($aInput['remove']))
		{
			Response::redirect('s/index'.$this->sesParam);
		}

		$aCtIDs = null;
		$result = Model_Class::getClassFromStudent($this->aStudent['stID']);
		if (count($result))
		{
			$aCtIDs = $result->as_array('ctID');
		}

		try
		{
			if (!is_null($aCtIDs))
			{
				foreach ($aCtIDs as $sCtID => $aC)
				{
					$result = Model_Class::removeClass($sCtID,$this->aStudent['stID']);
				}
			}
			$result = Model_Student::deleteGroupStudent(array($this->aStudent['stID']));
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_S_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		Session::set('SES_S_NOTICE_MSG',__('学生の解約が完了しました。'));
		Cookie::delete('CL_SL_HASH');
		Session::delete('CL_SL_HASH');
		Response::redirect('s/login');
	}



	public function action_mailauth()
	{
		// 認証メール送信
		$email = \Email::forge();
		$email->from(CL_MAIL_FROM, CL_MAIL_SENDER);
		$email->to($this->aStudent['stMail']);
		$email->subject('[CL]'.__('メールアドレス認証メール'));

		$html_body = View::forge('email/s_mailauth_html');
		$html_body->set('aStudent', $this->aStudent, false);
		$html_body->set('sMail', $this->aStudent['stMail'], false);
		$html_body->set('sHash', $this->aStudent['stHash'], false);
		$email->html_body($html_body);

		$body = View::forge('email/s_mailauth_plain');
		$body->set('aStudent', $this->aStudent, false);
		$body->set('sMail', $this->aStudent['stMail'], false);
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

		Session::set('SES_S_NOTICE_MSG',__('「メールアドレス認証メール」を送信しましたので、メール内のURLにアクセスして認証してください。'));
		Response::redirect('/s/index'.$this->sesParam);
	}

}