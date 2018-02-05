<?php
class Controller_T_Profile extends Controller_T_Base
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
		$sTitle = __('アカウント設定');
		# パンくずリスト生成
		$this->template->set_global('breadcrumbs',array(array('name'=>$sTitle)));
		# ページタイトル生成
		$this->template->set_global('pagetitle',$sTitle);

		# 基本データ登録
		$data['t_name'] = $this->aTeacher['ttName'];
		$data['t_school'] = $this->aTeacher['cmName'];
		$data['t_dept'] = $this->aTeacher['ttDept'];
		$data['t_timezone'] = $this->aTeacher['ttTimeZone'];
		$data['t_subject'] = $this->aTeacher['ttSubject'];
		$data['t_submail'] = $this->aTeacher['ttSubMail'];

		# タイムゾーンの取得
		$tz_list = ClFunc_Tz::tz_list();
		$tz_region = ClFunc_Tz::$regions;
		$this->template->set_global('tz_list',$tz_list);
		$this->template->set_global('tz_region',$tz_region);

		if (!Input::post(null,false))
		{
			$data['error'] = null;
			$this->template->content = View::forge('t/profile/index',$data);
			$this->template->javascript = array('cl.school_select.js');
			return $this->template;
		}

		$aInput = Input::post();

		switch ($aInput['mode'])
		{
			case 'profile':
				$val = Validation::forge();
				$val->add_callable('Helper_CustomValidation');

				if (is_null($this->aGroup) || !($this->aGroup['gtTeacherProfFlag'] & \Clfunc_Flag::T_PROF_NAME))
				{
					$val->add('t_name', __('氏名'))
						->add_rule('required')
						->add_rule('max_length',50);
				}
				if (!CL_CAREERTASU_MODE)
				{
					if (is_null($this->aGroup))
					{
						$val->add('t_school', __('所属学校名'))
							->add_rule('required')
							->add_rule('max_length',50);
					}
					if (isset($aInput['t_dept']))
					{
						$val->add('t_dept', __('学部名'))
						->add_rule('trim')
						->add_rule('max_length',50);
					}
					if (isset($aInput['t_subject']))
					{
						$val->add('t_subject', __('学科名'))
						->add_rule('trim')
						->add_rule('max_length',50);
					}
				}
			break;
			case 'mail':
				$val = Validation::forge();
				$val->add_callable('Helper_CustomValidation');
				$val->add('t_mail', __('新しいメールアドレス'))
					->add_rule('valid_email')
					->add_rule('max_length',200)
					->add_rule('tmail_chk',$this->aTeacher['ttID']);
				$val->add('t_mail_chk', __('新しいメールアドレス（確認）'))
					->add_rule('match_field','t_mail');

				$val->add('t_submail', __('サブメールアドレス'))
					->add_rule('valid_email')
					->add_rule('max_length',200);
			break;
			case 'pass':
				$val = Validation::forge();
				$val->add_callable('Helper_CustomValidation');
				$val->add('t_pass_now', __('現在のパスワード'))
					->add_rule('passwd_true',$this->aTeacher['ttPass']);
				$val->add('t_pass_edit', __('新しいパスワード'))
					->add_rule('required')
					->add_rule('min_length',8)
					->add_rule('max_length',32)
					->add_rule('passwd_char')
					->add_rule('passwd_false',$this->aTeacher['ttPass']);
				$val->add('t_pass_chk', __('新しいパスワード（確認）'))
					->add_rule('required')
					->add_rule('match_field','t_pass_edit');
			break;
		}
		if (!$val->run())
		{
			$data = array_merge($data,$aInput);
			$data['error'] = $val->error();
			$data['error']['profile_error'] = __('変更に失敗しました。入力内容をご確認ください。');
			$this->template->content = View::forge('t/profile/index',$data);
			$this->template->javascript = array('cl.school_select.js');
			return $this->template;
		}

		switch ($aInput['mode'])
		{
			case 'profile':
				$aUpdate = array();

				if (is_null($this->aGroup) || !($this->aGroup['gtTeacherProfFlag'] & \Clfunc_Flag::T_PROF_NAME))
				{
					$aUpdate['ttName'] = $aInput['t_name'];
				}
				if (is_null($this->aGroup))
				{
					$sCmKCode = '';
					$result = Model_College::getCollegeFromName($aInput['t_school']);
					$row = $result->current();
					if (!empty($row))
					{
						$sCmKCode = $row['cmKCode'];
					}
					else
					{
						$sCmKCode = Model_College::setCollege($aInput['t_school']);
					}
					$aUpdate['cmKCode'] = $sCmKCode;
				}
				if (is_null($this->aGroup) || !($this->aGroup['gtTeacherProfFlag'] & \Clfunc_Flag::T_PROF_DEPT))
				{
					$aUpdate['ttDept'] = $aInput['t_dept'];
				}
				if (is_null($this->aGroup) || !($this->aGroup['gtTeacherProfFlag'] & \Clfunc_Flag::T_PROF_SUBJECT))
				{
					$aUpdate['ttSubject'] = $aInput['t_subject'];
				}

				$aUpdate['ttTimeZone'] = $aInput['t_timezone'];

				if (!count($aUpdate))
				{
					Response::redirect('/t/profile');
				}
				Session::set('SES_T_NOTICE_MSG',__('プロフィールの変更が完了しました。'));
			break;
			case 'mail':
				$sMain = '';
				$aUpdate = array(
						'ttSubMail' => trim($aInput['t_submail']),
				);
				if (isset($aInput['t_mail']) && trim($aInput['t_mail']))
				{
					$aUpdate['ttMail'] = trim($aInput['t_mail']);
					$aUpdate['ttHash'] = sha1($aUpdate['ttMail'].$this->aTeacher['ttPass']);
					$aUpdate['ttMailAuth'] = 0;
					$sMain = "\n".__('「メールアドレス認証メール」を送信しましたので、メール内のURLにアクセスして認証してください。');
				}

				Session::set('SES_T_NOTICE_MSG',__('メールアドレスの変更が完了しました。').$sMain);
			break;
			case 'pass':
				$aUpdate = array(
					'ttPass' => sha1($aInput['t_pass_edit']),
					'ttPassDate' => date('Ymd'),
					'ttHash' => sha1($this->aTeacher['ttMail'].sha1($aInput['t_pass_edit'])),
				);
				Session::set('SES_T_NOTICE_MSG',__('パスワードの変更が完了しました。'));
			break;
		}
		try
		{
			$result = Model_Teacher::updateTeacher($this->aTeacher['ttID'],$aUpdate);
			if (isset($aUpdate['ttHash']))
			{
				Cookie::delete('CL_TL_KEY');
				Cookie::set('CL_TL_HASH',Crypt::encode(serialize(array('hash'=>$aUpdate['ttHash'],'ip'=>Input::real_ip()))));
			}

			if (isset($aUpdate['ttMail']) && $aUpdate['ttMail'])
			{
				// 認証メール送信
				$email = \Email::forge();
				$email->from(CL_MAIL_FROM, CL_MAIL_SENDER);
				$email->to($aUpdate['ttMail']);
				$email->subject('[CL]'.__('メールアドレス認証メール'));

				$html_body = View::forge('email/t_mailauth_html');
				$html_body->set('aTeacher', $this->aTeacher, false);
				$html_body->set('sMail', $aUpdate['ttMail'], false);
				$html_body->set('sHash', $aUpdate['ttHash'], false);
				$email->html_body($html_body);

				$body = View::forge('email/t_mailauth_plain');
				$body->set('aTeacher', $this->aTeacher, false);
				$body->set('sMail', $aUpdate['ttMail'], false);
				$body->set('sHash', $aUpdate['ttHash'], false);
				$email->alt_body($body);

				try
				{
					$email->send();
				}
				catch (\EmailValidationFailedException $e)
				{
					Log::warning('TeacherMailAuth - ' . $e->getMessage());
				}
				catch (\EmailSendingFailedException $e)
				{
					Log::warning('TeacherMailAuth - ' . $e->getMessage());
				}
			}
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::delete('SES_T_NOTICE_MSG');
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		Response::redirect('/t/profile');
	}

	public function action_photo()
	{
		if (!is_null($this->aGroup) && ($this->aGroup['gtTeacherProfFlag'] & \Clfunc_Flag::T_PROF_PHOTO))
		{
			Response::redirect('/t/profile');
		}

		$sTitle = __('写真を変更する');
		# パンくずリスト生成
		$this->template->set_global('breadcrumbs',array(array('link'=>'/profile','name'=>__('アカウント設定')),array('name'=>$sTitle)));
		# ページタイトル生成
		$this->template->set_global('pagetitle',$sTitle);

		if (!Input::post(null,false))
		{
			$data['error'] = null;
			$this->template->content = View::forge('t/profile/photo',$data);
			return $this->template;
		}

		$config = array(
			'max_size' => CL_IMGSIZE*1024*1024,
			'path' => CL_UPPATH.DS.'temp',
			'file_chmod' => 0666,
			'ext_whitelist' => array('jpg', 'jpeg', 'gif', 'png'),
			'type_whitelist' => array('image'),
		);

		Upload::process($config);

		# 添付処理
		$aMsg = null;
		if (!Upload::is_valid())
		{
			$ttImage = Upload::get_errors('ttImage');
			if ($ttImage['errors'])
			{
				switch ($ttImage['errors'][0]['error'])
				{
					case Upload::UPLOAD_ERR_INI_SIZE:
					case Upload::UPLOAD_ERR_FORM_SIZE:
					case Upload::UPLOAD_ERR_MAX_SIZE:
						$aMsg['ttImage'] = __('登録できるファイルのサイズは:sizeMBまでです。',array('size'=>CL_IMGSIZE));
					break;
					case Upload::UPLOAD_ERR_EXTENSION:
					case Upload::UPLOAD_ERR_EXT_BLACKLISTED:
					case Upload::UPLOAD_ERR_EXT_NOT_WHITELISTED:
					case Upload::UPLOAD_ERR_TYPE_BLACKLISTED:
					case Upload::UPLOAD_ERR_TYPE_NOT_WHITELISTED:
					case Upload::UPLOAD_ERR_MIME_BLACKLISTED:
					case Upload::UPLOAD_ERR_MIME_NOT_WHITELISTED:
						$aMsg['ttImage'] = __('登録できるファイルは画像（JPEG,PNG,GIF）のみです。');
					break;
					default:
						$aMsg['ttImage'] = __('ファイルアップロードに失敗しました。');
					break;
				}
			}
		}
		if (!is_null($aMsg))
		{
			$data['error'] = $aMsg;
			$this->template->content = View::forge('t/profile/photo',$data);
			return $this->template;
		}

		$ttImage = Upload::get_files('ttImage');
		$sProfilePath = CL_UPPATH.DS.'profile'.DS.'t';
		$aInput['ttImage'] = $this->aTeacher['ttID'].'.'.$ttImage['extension'];
		$sTempImg = $this->aTeacher['ttID'].'.tmp.'.$ttImage['extension'];

		ClFunc_Common::chkDir($sProfilePath,true);
		File::rename($ttImage['file'], $sProfilePath.DS.$sTempImg);
		ClFunc_Common::OrientationFixedImage($sProfilePath.DS.$sTempImg);
		$image = Image::load($sProfilePath.DS.$sTempImg);
		$image->crop_resize(100,100)->save($sProfilePath.DS.$aInput['ttImage'],0666);
		File::delete($sProfilePath.DS.$sTempImg);

		try
		{
			$result = Model_Teacher::updateTeacher($this->aTeacher['ttID'],$aInput);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		Session::set('SES_T_NOTICE_MSG',__('写真を変更しました。'));
		Response::redirect('/t/profile/photo');
	}

	public function action_socialconnect()
	{
		$aInput = null;
		$aRes = Session::get('SES_AUTH',false);

		if ($aRes)
		{
			$aRes = unserialize($aRes);
			$aInput['tent_name'] = $aRes['info']['name'];
			$aInput['tent_uid']  = $aRes['uid'];
			$aInput['provider']  = $aRes['provider'];

			Log::write('OAUTH',print_r($aRes,true));

			$sImg = null;
			if (isset($aRes['info']["image"]) && !$this->aTeacher['ttImage']) {
				ini_set("allow_url_fopen",true);
				$sImg = file_get_contents($aRes['info']["image"], FILE_BINARY);
				ini_set("allow_url_fopen",false);
			}
		}
		else
		{
			Session::set('SES_T_ERROR_MSG',__('指定されているURLは無効です。'));
			Response::redirect('index/error/');
		}
		Session::delete('SES_AUTH');

		$aWhere = array(
			array('ttID','!=',$this->aTeacher['ttID']),
			array('tt'.ucfirst($aInput['provider']).'ID','=',$aInput['tent_uid']),
		);
		$result = Model_Teacher::getTeacher($aWhere);
		if (count($result))
		{
			Session::set('SES_T_ERROR_MSG',__('既に別のアカウントに:providerが連携されています。',array('provider'=>$aInput['provider'])));
			Response::redirect('/t/profile');
		}

		$aUpdate = array('tt'.ucfirst($aInput['provider']).'ID'=>$aInput['tent_uid']);
		if (!$this->aTeacher['ttName'])
		{
			$aUpdate['ttName'] = $aInput['tent_name'];
		}
		try
		{
			$result = Model_Teacher::updateTeacher($this->aTeacher['ttID'],$aUpdate,$sImg);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		Session::set('SES_T_NOTICE_MSG',__(':providerと連携しました。',array('provider'=>$aInput['provider'])));
		Response::redirect('/t/profile');
	}

	public function action_socialout($sService = null)
	{
		if (is_null($sService))
		{
			Session::set('SES_T_ERROR_MSG',__('連携削除対象のサービスが指定されていません。'));
			Response::redirect('/t/profile');
		}
		switch ($sService)
		{
			case 'facebook':
				$aUpdate = array('ttFacebookID'=>null);
			break;
			case 'google':
				$aUpdate = array('ttGoogleID'=>null);
			break;
			default:
				Session::set('SES_T_ERROR_MSG',__('連携削除対象のサービスが指定されていません。'));
				Response::redirect('/t/profile');
			break;
		}
		try
		{
			$result = Model_Teacher::updateTeacher($this->aTeacher['ttID'],$aUpdate);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		Session::set('SES_T_NOTICE_MSG',__(':providerとの連携を解除しました。',array('provider'=>$sService)));
		Response::redirect('/t/profile');
	}

	public function action_mailauth()
	{
		// 認証メール送信
		$email = \Email::forge();
		$email->from(CL_MAIL_FROM, CL_MAIL_SENDER);
		$email->to($this->aTeacher['ttMail']);
		$email->subject('[CL]'.__('メールアドレス認証メール'));

		$html_body = View::forge('email/t_mailauth_html');
		$html_body->set('aTeacher', $this->aTeacher, false);
		$html_body->set('sMail', $this->aTeacher['ttMail'], false);
		$html_body->set('sHash', $this->aTeacher['ttHash'], false);
		$email->html_body($html_body);

		$body = View::forge('email/t_mailauth_plain');
		$body->set('aTeacher', $this->aTeacher, false);
		$body->set('sMail', $this->aTeacher['ttMail'], false);
		$body->set('sHash', $this->aTeacher['ttHash'], false);
		$email->alt_body($body);

		try
		{
			$email->send();
		}
		catch (\EmailValidationFailedException $e)
		{
			Log::warning('TeacherMailAuth - ' . $e->getMessage());
		}
		catch (\EmailSendingFailedException $e)
		{
			Log::warning('TeacherMailAuth - ' . $e->getMessage());
		}

		Session::set('SES_T_NOTICE_MSG',__('「メールアドレス認証メール」を送信しましたので、メール内のURLにアクセスして認証してください。'));
		Response::redirect('/t/index');
	}
}