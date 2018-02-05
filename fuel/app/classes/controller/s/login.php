<?php
class Controller_S_Login extends Controller_S_Basenl
{
	public function action_index($noCookie = null)
	{
		if ($red = Session::get('CL_SL_LOGINMODEL',false))
		{
			$red .= (!is_null($noCookie))? DS.$noCookie:'';
			Session::delete('CL_SL_LOGINMODEL');
			Response::redirect($red.$this->sesParam);
		}

//		Session::destroy();

		$data['slgn_id'] = null;
		$data['slgn_pass'] = null;
		$data['slgn_chk'] = false;
		$data['error'] = null;
		$sNC = null;
		if (!is_null($noCookie))
		{
			$sNC  = __('ログイン情報が確認できませんでした。').'<br>'.__('以下の可能性がありますので、ご確認ください。').'<br>';
			$sNC .= ' 1.'.__('ログインしたまま長時間操作していない場合').'<br> -> '.__('再度ログインすることで解決します。').'<br>';
			$sNC .= ' 2.'.__('COOKIEの情報が確認できない場合').'<br> -> '.__('お使いのブラウザにおいてCOOKIEの利用が有効かどうかご確認ください。').'<br>';
		}

		$sKey = Cookie::get('CL_SL_KEY',false);
		if ($sKey)
		{
			$aKey = unserialize(Crypt::decode($sKey));
			$data['slgn_id'] = (isset($aKey['id']))? $aKey['id']:'';
			$data['slgn_pass'] = (isset($aKey['pass']))? $aKey['pass']:'';
			$data['slgn_chk'] = true;
		}

		$this->template->content = View::forge($this->vDir.DS.'login',$data);
		if (!is_null($sNC))
		{
			$this->template->content->set('noCookie',$sNC,false);
		}

		// チェッカークッキー発行
		Cookie::set('CL_COOKIE_CHK','cookies_enable');

		return $this->template;
	}

	public function action_loginchk()
	{
		$aInput = Input::post();
		$aInput['slgn_chk'] = (isset($aInput['slgn_chk']))? true:false;
		$aInput['forgot_mail'] = null;

		$val = Validation::forge();
		$val->add_field('slgn_id', __('ログインIDまたはメールアドレス'), 'required|max_length[200]');
		$val->add_field('slgn_pass', __('パスワード'), 'required|max_length[200]');
		if (!$val->run())
		{
			$aInput['error'] = $val->error();
			$this->template->content = View::forge($this->vDir.DS.'login',$aInput);
			return $this->template;
		}

		$result = Model_Student::getStudentMailLogin($aInput['slgn_id']);
		if (count($result))
		{
			$aResult = $result->current();
			$result = Model_Group::getGroupStudents(array(array('gsp.stID','=',$aResult['stID'])));
			if (count($result))
			{
				$aGS = $result->current();
				$result = Model_Group::getGroup(array(array('gb.gtID','=',$aGS['gtID'])));
				if (count($result))
				{
					$aGroup = $result->current();
					if ($aGroup['gtLDAP'])
					{
						\Session::set('SES_S_ERROR_MSG', __('こちらの専用ログイン画面よりログインしてください。'));
						Response::redirect(DS.$aGroup['gtPrefix'].DS.'s'.$this->sesParam);
					}
				}
			}
		}

		$result = Model_Student::getStudentFromPostLogin($aInput['slgn_id'],$aInput['slgn_pass']);
		if (!count($result))
		{
			$aInput['error'] = array('login'=>__('入力されたログインIDまたはメールアドレスが存在しないか、パスワードが異なるためログインできません。'));
			$this->template->content = View::forge($this->vDir.DS.'login',$aInput);
			return $this->template;
		}
		$aResult = $result->current();

		// タイムゾーンの取得と確認
		$bTZ = false;
		try
		{
			ClFunc_Tz::tz_chk($aInput['ltzone']);
		}
		catch (Exception $e)
		{
			$aInput['ltzone'] = date_default_timezone_get();
		}
		if (!$aResult['stTimeZone'])
		{
			$aResult['stTimeZone'] = $aInput['ltzone'];
			$bTZ = true;
		}

		$result = Model_Student::setLoginUpdate($aResult,$bTZ);
		if (!count($result))
		{
			$aInput['error'] = array('login'=>__('入力されたログインIDまたはメールアドレスが存在しないか、パスワードが異なるためログインできません。'));
			$this->template->content = View::forge($this->vDir.DS.'login',$aInput);
			return $this->template;
		}
		$aResult = $result->current();

		if ($aInput['slgn_chk'])
		{
			Cookie::set("CL_SL_KEY",Crypt::encode(serialize(array('sseed'=>mt_rand(),'id'=>$aInput['slgn_id'],'pass'=>$aInput['slgn_pass']))),60*60*24*180);
		}
		else
		{
			Cookie::delete("CL_SL_KEY");
		}

		if (Cookie::get('CL_COOKIE_CHK',false))
		{
			Cookie::set("CL_SL_HASH",Crypt::encode(serialize(array('sseed'=>mt_rand(),'hash'=>sha1($aResult['stLogin'].$aResult['stPass']),'ip'=>Input::real_ip()))));
		}
		else
		{
			Session::set('CL_SL_HASH',Crypt::encode(serialize(array('sseed'=>mt_rand(),'hash'=>sha1($aResult['stLogin'].$aResult['stPass']),'ip'=>Input::real_ip()))));
		}

		if ($aResult['stFirst'])
		{
			Response::redirect('s/password/first'.$this->sesParam);
		}

		Response::redirect('s/index'.$this->sesParam);
	}

	public function action_auth($mode = 'ad', $sys = null, $noCookie = null)
	{
		$sUrl = $sys.DS.'s';
		$this->template->set_global('dir', $sUrl);
		$this->template->content = View::forge($this->vDir.DS.'login_ad');

		$sNC = null;
		if (!is_null($noCookie))
		{
			$sNC  = __('ログイン情報が確認できませんでした。').'<br>'.__('以下の可能性がありますので、ご確認ください。').'<br>';
			$sNC .= ' 1.'.__('ログインしたまま長時間操作していない場合').'<br> -> '.__('再度ログインすることで解決します。').'<br>';
			$sNC .= ' 2.'.__('COOKIEの情報が確認できない場合').'<br> -> '.__('お使いのブラウザにおいてCOOKIEの利用が有効かどうかご確認ください。').'<br>';
			$this->template->content->set('noCookie',$sNC,false);
		}

		if (is_null($sys))
		{
			Response::redirect('index/404','location',404);
		}
		$result = Model_Group::getGroup(array(array('gb.gtPrefix','=',$sys),array('gb.gtLDAP','=',1)));
		if (!count($result))
		{
			Response::redirect('index/404','location',404);
		}
		$aGroup = $result->current();
		$this->template->content->set('aGroup',$aGroup);

		if (!Input::post(null,false))
		{
			// チェッカークッキー発行
			Cookie::set('CL_COOKIE_CHK','cookies_enable');

			$aInput = array('slgn_id'=>null,'slgn_pass'=>null,'error'=>null);
			$this->template->content->set($aInput);
			return $this->template;
		}

		$aInput = Input::post(null,false);
		$val = Validation::forge();
		$val->add_field('slgn_id', __('ログインID'), 'required');
		$val->add_field('slgn_pass', __('パスワード'), 'required');
		if (!$val->run())
		{
			$aInput['error'] = $val->error();
			$this->template->content->set($aInput);
			return $this->template;
		}

		try
		{
			\Clfunc_Common::LDAPAuthCommand($aGroup, $aInput['slgn_id'], $aInput['slgn_pass']);
		}
		catch (\Exception $e)
		{
			$aErr = explode('|', $e->getMessage());
			$aInput['error']['login'] = $aErr[0];
			$this->template->content->set($aInput);
			return $this->template;
		}

		$result = Model_Group::getGroupStudents(array(array('gsp.gtID','=',$aGroup['gtID']),array('st.stLogin','=',$aInput['slgn_id'])));
		if (!count($result))
		{
			$aInput['error']['slgn_id'] = __(':siteに登録されていないログインIDです。先生にご確認ください。',array('site'=>CL_SITENAME));
			$this->template->content->set($aInput);
			return $this->template;
		}

		$result = Model_Student::getStudentFromLoginID($aInput['slgn_id']);
		if (!count($result))
		{
			$aInput['error'] = array('login'=>__('ログインに失敗しました。再度実行してみてください。'));
			$this->template->content->set($aInput);
			return $this->template;
		}
		$aResult = $result->current();

		// タイムゾーンの取得と確認
		$bTZ = false;
		try
		{
			ClFunc_Tz::tz_chk($aInput['ltzone']);
		}
		catch (Exception $e)
		{
			$aInput['ltzone'] = date_default_timezone_get();
		}
		if (!$aResult['stTimeZone'])
		{
			$aResult['stTimeZone'] = $aInput['ltzone'];
			$bTZ = true;
		}

		$result = Model_Student::setLoginUpdate($aResult,$bTZ);
		if (!count($result))
		{
			$aInput['error'] = array('login'=>__('ログインに失敗しました。再度実行してみてください。'));
			$this->template->content->set($aInput);
			return $this->template;
		}
		$aResult = $result->current();

		Session::set('CL_SL_LOGINMODEL', $sUrl);
		if (Cookie::get('CL_COOKIE_CHK',false))
		{
			Cookie::set("CL_SL_HASH",Crypt::encode(serialize(array('sseed'=>mt_rand(),'hash'=>sha1($aResult['stLogin'].$aResult['stPass']),'ip'=>Input::real_ip()))));
		}
		else
		{
			Session::set('CL_SL_HASH',Crypt::encode(serialize(array('sseed'=>mt_rand(),'hash'=>sha1($aResult['stLogin'].$aResult['stPass']),'ip'=>Input::real_ip()))));
		}
		Response::redirect('s/index'.$this->sesParam);
	}


	public function action_getprofile()
	{
		$this->template->javascript = array('cl.school_select.js');

		if (Cookie::get('CL_COOKIE_CHK',false))
		{
			$sHash = Cookie::get('CL_SL_HASH',false);
		}
		else
		{
			$sHash = Session::get('CL_SL_HASH',false);
		}
		if (!$sHash)
		{
			Response::redirect('s/login/index/1');
		}
		$aLogin = unserialize(Crypt::decode($sHash));

		$result = Model_Student::getStudentFromHash($aLogin['hash']);
		if (!count($result))
		{
			Response::redirect('s/login/index/1');
		}
		$aStudent = $result->current();

		$aGroup = null;
		$result = Model_Group::getGroupStudents(array(array('gsp.stID','=',$aStudent['stID'])));
		if (count($result))
		{
			$aGS = $result->current();
			$result = Model_Group::getGroup(array(array('gb.gtID','=',$aGS['gtID'])));
			if (count($result))
			{
				$aGroup = $result->current();
			}
		}

		if (is_null($aGroup) || ($aGroup['gtStudentGetFlag'] == 0))
		{
			Response::redirect('s/index'.$this->sesParam);
		}

		$aFlag  = \Clfunc_Common::dec2Bits((int)$aGroup['gtStudentGetFlag']);
		$aGet = \Clfunc_Flag::getStuGetFlag();

		$this->template->set_global('aFlag', $aFlag);
		$this->template->set_global('aGet', $aGet);
		$this->template->set_global('aGroup', $aGroup);
		$this->template->set_global('aStudent', $aStudent);

		# 基本データ登録
		$data['s_name']    = $aStudent['stName'];
		$data['s_no']      = $aStudent['stNO'];
		$data['s_sex']     = $aStudent['stSex'];
		$data['s_school']  = $aStudent['stSchool'];
		$data['s_dept']    = $aStudent['stDept'];
		$data['s_subject'] = $aStudent['stSubject'];
		$data['s_year']    = $aStudent['stYear'];
		$data['s_class']   = $aStudent['stClass'];
		$data['s_course']  = $aStudent['stCourse'];
		$data['s_mail']    = $aStudent['stMail'];

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

		if (isset($aInput['s_name']))
		{
			$val->add('s_name', __('氏名'))
				->add_rule('trim')
				->add_rule('required')
				->add_rule('max_length',50);

			$aUpdate['stName'] =  $aInput['s_name'];
		}
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
				->add_rule('smail_chk',$aStudent['stID']);

			$aUpdate['stMail'] =  trim($aInput['s_mail']);
			$aUpdate['stMailAuth'] = 0;
			$sMain = "\n".__('「メールアドレス認証メール」を送信しましたので、メール内のURLにアクセスして認証してください。');
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
			$result = Model_Student::updateStudent($aStudent['stID'],$aUpdate);

			if (isset($aUpdate['stMail']))
			{
				// 認証メール送信
				$email = \Email::forge();
				$email->from(CL_MAIL_FROM, CL_MAIL_SENDER);
				$email->to($aInput['s_mail']);
				$email->subject('[CL]'.__('メールアドレス認証メール'));

				$html_body = View::forge('email/s_mailauth_html');
				$html_body->set('aStudent', $aStudent, false);
				$html_body->set('sMail', $aUpdate['stMail'], false);
				$html_body->set('sHash', $aStudent['stHash'], false);
				$email->html_body($html_body);

				$body = View::forge('email/s_mailauth_plain');
				$body->set('aStudent', $aStudent, false);
				$body->set('sMail', $aUpdate['stMail'], false);
				$body->set('sHash', $aStudent['stHash'], false);
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
		Response::redirect('s/index'.$this->sesParam);
	}
}