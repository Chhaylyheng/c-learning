<?php
class Controller_T_App extends Controller_Rest
{
	public $aTeacher = null;
	public $aClass = null;
	public $aMCate = null;

	/**
	 * ログイン判定
	 * @param string $sHash ログインハッシュ
	 */
	private function loginChk($sHash = null)
	{
		$res = array('err'=>-3,'res'=>'','msg'=>'ログインハッシュが取得できません');

		if (is_null($sHash))
		{
			$this->response($res);
			return;
		}
		$aLogin = unserialize(Crypt::decode($sHash));

		$result = Model_Teacher::getTeacherFromHash($aLogin['hash']);
		if (!count($result))
		{
			$res['err'] = -2;
			$res['msg'] = 'ログイン情報がハッシュから特定できないため、処理を続行することはできません';
			$this->response($res);
			return;
		}
		$this->aTeacher = $result->current();
		return;
	}
	/**
	 * 講義判定
	 * @param string $sCtID 講義ID
	 */
	private function classChk($sCtID = null)
	{
		$res = array('err'=>-3,'res'=>'','msg'=>'講義IDが取得できません');

		if (is_null($sCtID))
		{
			$this->response($res);
			return;
		}

		$result = Model_Class::getClassFromTeacher($this->aTeacher["ttID"],null,array(array('tp.ctID','=',$sCtID)));
		if (!count($result)) {
			$res['err'] = -2;
			$res['msg'] = '講義情報が講義IDから特定できないため、処理を続行することはできません';
			$this->response($res);
			return;
		}
		$this->aClass = $result->current();
		return;
	}

	/**
	 * 教材倉庫カテゴリチェック
	 * @param string $sMcID
	 */
	private function mcChk($sMcID = null)
	{
		$res = array('err'=>-3,'res'=>'','msg'=>'必要な情報が取得できません');

		if (is_null($sMcID))
		{
			$this->response($res);
			return;
		}

		$result = Model_Material::getMaterialCategoryFromClass($this->aClass['ctID'],array(array('mcID','=',$sMcID)));
		if (!count($result))
		{
			$res = array('err'=>-2,'res'=>'','msg'=>'教材倉庫カテゴリ情報が見つかりません');
			$this->response($res);
			return;
		}
		$this->aMCate = $result->current();

		return;
	}


	/**
	 * ログイン処理
	 */
	public function post_login()
	{
		$res = array('err'=>-3,'res'=>'');
		$par = Input::post();

		$val = Validation::forge();
		$val->add_field('login', 'メールアドレス', 'required|max_length[200]');
		$val->add_field('pass', 'パスワード', 'required|max_length[200]');
		if (!$val->run())
		{
			$res['msg'] = '入力されたメールアドレスが存在しないか、パスワードが異なるためログインできません';
			$this->response($res);
			return;
		}

		$result = Model_Teacher::getTeacherFromPostLogin($par['login'],$par['pass']);
		if (!count($result))
		{
			$res['err'] = -2;
			$res['msg'] = '入力されたメールアドレスが存在しないか、パスワードが異なるためログインできません';
			$this->response($res);
			return;
		}
		$aResult = $result->current();
		$result = Model_Teacher::setLoginUpdate($aResult);
		if (!count($result))
		{
			$res['err'] = -2;
			$res['msg'] = '入力されたログインIDまたはメールアドレスが存在しないか、パスワードが異なるためログインできません';
			$this->response($res);
			return;
		}
		$aResult = $result->current();
		$result = Model_Group::getGroupTeachers(array(array('gtp.ttID','=',$aResult['ttID'])));
		if (count($result))
		{
			$aGT = $result->current();
			$result = Model_Group::getGroup(array(array('gb.gtID','=',$aGT['gtID'])));
			if (count($result))
			{
				$aGroup = $result->current();
				if ($aGroup['gtLDAP'])
				{
					$res['err'] = -2;
					$res['msg'] = '入力されたログインIDまたはメールアドレスが存在しないか、パスワードが異なるためログインできません';
					$this->response($res);
					return;
				}
			}
		}

		Cookie::set("CL_TL_HASH",Crypt::encode(serialize(array('hash'=>sha1($aResult['ttMail'].$aResult['ttPass']),'ip'=>Input::real_ip()))));

		if (isset($par['os']) && isset($par['token']))
		{
			try
			{
				$aUpdate = array(
					'ttApp' => (($par['os'] == 'iOS')? 1:(($par['os'] == 'Android')? 2:0)),
					'ttDeviceToken' => $par['token'],
					'ttDeviceID' => ((isset($par['did']))? $par['did']:''),
				);
				$result = Model_Teacher::updateTeacher($aResult['ttID'], $aUpdate);
			}
			catch (Exception $e)
			{
				$res['err'] = -1;
				$res['msg'] = $e->getMessage();
				$this->response($res);
				return;
			}
		}

		$sHash = Crypt::encode(serialize(array('hash'=>sha1($aResult['ttMail'].$aResult['ttPass']),'ip'=>Input::real_ip())));
		$res = array('err'=>0,'res'=>array('hash'=>$sHash,'Teacher'=>$aResult));
		$this->response($res);
		return;
	}

	public function post_oauthlogin()
	{
		$res = array('err'=>-3,'res'=>'');
		$par = Input::post();

		$result = Model_Teacher::getTeacherFromSocialID($par['uid'],$par['sns']);
		if (!count($result))
		{
			$res['err'] = -2;
			$res['msg'] = '入力されたIDが存在しません。';
			$this->response($res);
			return;
		}
		$aResult = $result->current();
		$result = Model_Teacher::setLoginUpdate($aResult);
		if (!count($result))
		{
			$res['err'] = -2;
			$res['msg'] = '入力されたIDが存在しません。';
			$this->response($res);
			return;
		}
		$aResult = $result->current();
		$result = Model_Group::getGroupTeachers(array(array('gtp.ttID','=',$aResult['ttID'])));
		if (count($result))
		{
			$aGT = $result->current();
			$result = Model_Group::getGroup(array(array('gb.gtID','=',$aGT['gtID'])));
			if (count($result))
			{
				$aGroup = $result->current();
				if ($aGroup['gtLDAP'])
				{
					$res['err'] = -2;
					$res['msg'] = '入力されたログインIDまたはメールアドレスが存在しないか、パスワードが異なるためログインできません';
					$this->response($res);
					return;
				}
			}
		}

		Cookie::set("CL_TL_HASH",Crypt::encode(serialize(array('hash'=>sha1($aResult['ttMail'].$aResult['ttPass']),'ip'=>Input::real_ip()))));

		if (isset($par['os']) && isset($par['token']))
		{
			try
			{
				$aUpdate = array(
					'ttApp' => (($par['os'] == 'iOS')? 1:(($par['os'] == 'Android')? 2:0)),
					'ttDeviceToken' => $par['token'],
					'ttDeviceID' => ((isset($par['did']))? $par['did']:''),
				);
				$result = Model_Teacher::updateTeacher($aResult['ttID'], $aUpdate);
			}
			catch (Exception $e)
			{
				$res['err'] = -1;
				$res['msg'] = $e->getMessage();
				$this->response($res);
				return;
			}
		}

		$sHash = Crypt::encode(serialize(array('hash'=>sha1($aResult['ttMail'].$aResult['ttPass']),'ip'=>Input::real_ip())));
		$res = array('err'=>0,'res'=>array('hash'=>$sHash,'Teacher'=>$aResult));
		$this->response($res);
		return;
	}

	/**
	 * ログアウト処理
	 */
	public function post_logout()
	{
		$res = array('err'=>-3,'res'=>'');
		$par = Input::post();

		self::loginChk($par['hash']);

		try
		{
			$aUpdate = array(
				'ttApp' => 0,
				'ttDeviceToken' => '',
				'ttDeviceID' => '',
			);
			$result = Model_Teacher::updateTeacher($this->aTeacher['ttID'], $aUpdate);
		}
		catch (Exception $e)
		{
			$res['err'] = -1;
			$res['msg'] = $e->getMessage();
			$this->response($res);
			return;
		}

		$res = array('err'=>0,'res'=>1);
		$this->response($res);
		return;
	}

	/**
	 * デバイストークン登録
	 */
	public function post_tokenadd()
	{
		$res = array('err'=>-3,'res'=>'');
		$par = Input::post();

		self::loginChk($par['hash']);

		if (isset($par['os']) && isset($par['token']))
		{
			try
			{
				$aUpdate = array(
					'ttApp' => (($par['os'] == 'iOS')? 1:(($par['os'] == 'Android')? 2:0)),
					'ttDeviceToken' => $par['token'],
					'ttDeviceID' => ((isset($par['did']))? $par['did']:''),
				);
				$result = Model_Teacher::updateTeacher($this->aTeacher['ttID'], $aUpdate);
			}
			catch (Exception $e)
			{
				$res['err'] = -1;
				$res['msg'] = $e->getMessage();
				$this->response($res);
				return;
			}
		}

		$res = array('err'=>0,'res'=>1);
		$this->response($res);
		return;
	}

	/**
	 * パスワードリセット
	 */
	public function post_reset()
	{
		$res = array('err'=>-3,'res'=>'');
		$par = Input::post();

		$val = Validation::forge();
		$val->add_callable('Helper_CustomValidation');

		$val->add('mail', 'メールアドレス')
		->add_rule('required')
		->add_rule('trim')
		->add_rule('valid_email')
		->add_rule('max_length', 200);

		if (!$val->run())
		{
			foreach ($val->error() as $field => $error) {
				$res['msg'] = $error->get_message();
			}
			$this->response($res);
			return;
		}

		$result = Model_Teacher::getTeacherFromMail($par['mail']);
		if (!count($result))
		{
			$res['err'] = -2;
			$res['msg'] = '指定されたメールアドレスは'.CL_SITENAME.'に登録されていません';
			$this->response($res);
			return;
		}
		$aResult = $result->current();

		// 再設定URL生成
		$aMD['reset_hash'] = Crypt::encode($aResult['ttID'].CL_SEP.strtotime('+24Hours'));

		// 登録完了メール送信
		$email = \Email::forge();
		$email->from(CL_MAIL_FROM, CL_MAIL_SENDER);
		$email->to($aResult['ttMail']);
		$email->subject('[CL]先生パスワードの再設定');
		$body = View::forge('email/t_reset', $aMD);
		$email->body($body);

		try
		{
			$email->send();
		}
		catch (\EmailValidationFailedException $e)
		{
			Log::warning('TeacherPasswordReminderMail - ' . $e->getMessage());
		}
		catch (\EmailSendingFailedException $e)
		{
			Log::warning('TeacherPasswordReminderMail - ' . $e->getMessage());
		}

		$res = array('err'=>0,'res'=>1);
		$this->response($res);
		return;
	}


	/**
	 * ハッシュ取得処理
	 */
	public function post_gethash()
	{
		$res = array('err'=>-3,'res'=>'');
		$par = Input::post();

		$result = Model_Teacher::getTeacherFromID($par['tid']);
		if (!count($result))
		{
			$res['err'] = -2;
			$res['msg'] = '入力されたIDは存在しません。';
			$this->response($res);
			return;
		}
		$aResult = $result->current();

		$sHash = Crypt::encode(serialize(array('hash'=>sha1($aResult['ttMail'].$aResult['ttPass']),'ip'=>Input::real_ip())));
		$res = array('err'=>0,'res'=>array('hash'=>$sHash));
		$this->response($res);
		return;
	}


	public function post_oauthregist()
	{
		$res = array('err'=>-3,'res'=>'');
		$par = Input::post();

		if (isset($par['name']) && isset($par['mail']) && isset($par['uid']) && isset($par['sns']))
		{
			$aInput['tent_name'] = $par['name'];
			$aInput['tent_mail'] = $par['mail'];
			$aInput['tent_uid']  = $par['uid'];
			$aInput['provider']  = $par['sns'];

			$sImg = null;
			if (isset($par["image"]) && $par["image"]) {
				ini_set("allow_url_fopen",true);
				$sImg = file_get_contents($par["image"], FILE_BINARY);
				ini_set("allow_url_fopen",false);
			}
		}
		else
		{
			$res['err'] = -2;
			$res['msg'] = '必要な情報が指定されていません。';
			$this->response($res);
			return;
		}

		$aWhere = array(
			array('tt'.ucfirst($aInput['provider']).'ID','=',$aInput['tent_uid']),
		);
		$result = Model_Teacher::getTeacher($aWhere);
		if (count($result))
		{
			$res['err'] = -2;
			$res['msg'] = '既に同一のIDで先生登録されています。';
			$this->response($res);
			return;
		}

		try
		{
			$result = Model_Teacher::getTeacherFromMail($aInput['tent_mail']);
			if (count($result))
			{
				$aTeacher = $result->current();
				$aUpdate = array('tt'.ucfirst($aInput['provider']).'ID'=>$aInput['tent_uid']);
				$res = Model_Teacher::updateTeacher($aTeacher['ttID'],$aUpdate);
				$aInsert['teacher'] = $aTeacher;
				$sTtID = $aInsert['teacher']['ttID'];
			} else {
				$sPass = strtolower(Str::random('distinct', 8));
				// 登録データ生成
				$aInsert['teacher'] = array(
					'ttID'            => null,
					'ttMail'          => $aInput['tent_mail'],
					'ttPass'          => sha1($sPass),
					'ttName'          => $aInput['tent_name'],
					'tt'.ucfirst($aInput['provider']).'ID' => $aInput['tent_uid'],
					'ttLoginNum'      => 0,
					'ttLastLoginDate' => '00000000000000',
					'ttLoginDate'     => '00000000000000',
					'ttPassDate'      => '00000000',
					'ttPassMiss'      => 0,
					'ttUA'            => Input::user_agent(),
					'ttHash'          => sha1($aInput['tent_mail'].sha1($sPass)),
					'ttStatus'        => 1,
					'ttDate'          => date('YmdHis'),
				);
				$aInsert['account'] = array(
					'ttID'    => null,
					'ahTitle' => '先生アカウントの新規作成',
					'ahIP'    => Input::real_ip(),
					'ahUA'    => Input::user_agent(),
					'ahDate'  => date('YmdHis'),
				);
				$sTtID = Model_Teacher::insertTeacher($aInsert,$sImg);
			}
		}
		catch (Exception $e)
		{
			$res['err'] = -3;
			$res['msg'] = 'データベース登録に失敗しました。';
			$this->response($res);
			return;
		}

		// 登録完了メール送信
		$email = \Email::forge();
		$email->from(CL_MAIL_FROM, CL_MAIL_SENDER);
		$email->to($aInput['tent_mail']);
		$email->bcc(array(CL_KEIYAKUMAIL,CL_MIYATAMAIL));
		$email->subject('[CL]先生アカウント登録手続き完了のお知らせ');
		$body = View::forge('email/t_social_fin', $aInput);
		$email->body($body);

		try
		{
			$email->send();
		}
		catch (\EmailValidationFailedException $e)
		{
			Log::warning('TeacherSocialRegistFinishMail - ' . $e->getMessage());
		}
		catch (\EmailSendingFailedException $e)
		{
			Log::warning('TeacherSocialRegistFinishMail - ' . $e->getMessage());
		}

		Cookie::set("CL_TL_HASH",Crypt::encode(serialize(array('hash'=>sha1($aInsert['teacher']['ttMail'].$aInsert['teacher']['ttMail']),'ip'=>Input::real_ip()))));

		$sHash = Crypt::encode(serialize(array('hash'=>sha1($aInsert['teacher']['ttMail'].$aInsert['teacher']['ttPass']),'ip'=>Input::real_ip())));
		$res = array('err'=>0,'res'=>array('tid'=>$sTtID,'hash'=>$sHash));
		$this->response($res);
		return;
	}

	public function post_oauthchange()
	{
		$res = array('err'=>-3,'res'=>'');
		$par = Input::post();

		if (isset($par['name']) && isset($par['uid']) && isset($par['sns']) && isset($par['hash']))
		{
			self::loginChk($par['hash']);

			$aInput['tent_name'] = $par['name'];
			$aInput['tent_uid']  = $par['uid'];
			$aInput['provider']  = $par['sns'];

			$sImg = null;
			if (isset($par["image"]) && !$this->aTeacher['ttImage']) {
				ini_set("allow_url_fopen",true);
				$sImg = file_get_contents($par["image"], FILE_BINARY);
				ini_set("allow_url_fopen",false);
			}
		}
		else
		{
			$res['err'] = -2;
			$res['msg'] = '必要な情報が指定されていません。';
			$this->response($res);
			return;
		}

		$aWhere = array(
			array('ttID','!=',$this->aTeacher['ttID']),
			array('tt'.ucfirst($aInput['provider']).'ID','=',$aInput['tent_uid']),
		);
		$result = Model_Teacher::getTeacher($aWhere);
		if (count($result))
		{
			$res['err'] = -2;
			$res['msg'] = '既に別のアカウントに'.$aInput['provider'].'が連携されています。';
			$this->response($res);
			return;
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

		$res = array('err'=>0,'res'=>'');
		$this->response($res);
		return;
	}

	/**
	 * 教材倉庫一覧
	 */
	public function post_materiallist()
	{
		$res = array('err'=>-3,'res'=>'');
		$par = Input::post();

		self::loginChk($par['hash']);
		self::classChk($par['ctid']);
		self::mcChk($par['mcid']);

		$aMaterial = null;
		$result = Model_Material::getMaterial(array(array('mt.mcID','=',$this->aMCate['mcID'])),null,array('mt.mSort'=>'desc'));
		if (count($result))
		{
			$aMaterial = $result->as_array();
		}

		if (!is_null($aMaterial))
		{
			foreach ($aMaterial as $i => $aM)
			{
				$aMaterial[$i]['fileURL'] = '';
				$aMaterial[$i]['externalURL'] = '';
				$aMaterial[$i]['thumbURL'] = '';
				if ($aM['fID'] != '')
				{
					$aMaterial[$i]['fileURL'] = \Uri::create('getfile/s3file/:fid/:mode',array('fid'=>$aM['fID'],'mode'=>'e'));
					if ($aM['fFileType'] == 2)
					{
						$aMaterial[$i]['thumbURL'] = \Uri::create('getfile/s3file/:fid/:mode',array('fid'=>$aM['fID'],'mode'=>'t'));
					}
				}

				if ($aM['mURL'] != '')
				{
					$urls = explode("\n", $aM['mURL']);
					if (is_array($urls))
					{
						foreach ($urls as $iENO => $v)
						{
							if (!$v) continue;
							$aMaterial[$i]['externalURL'][] = \Uri::create('getfile/externallink/:mno/:eno',array('mno'=>$aM['mNO'],'eno'=>$iENO));
						}
					}
				}
			}
		}

		if (!is_null($aMaterial))
		{
			foreach ($aMaterial as $i => $aM)
			{
				foreach ($aM as $sKey => $sV)
				{
					if (is_null($sV))
					{
						$aMaterial[$i][$sKey] = '';
					}
				}
			}
		}

		$res = array('err'=>0,'res'=>is_null($aMaterial)? '':$aMaterial);
		$this->response($res);
		return;
	}

}

