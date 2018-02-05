<?php
class Controller_S_Coop extends Controller_S_Baseclass
{
	private $baseName = 'coop';
	private $aCCategory = null;
	private $aCoop = null;
	private $aCoopBase = array(
		'c_title'=>null,
		'c_text' =>null,
	);


	public function action_index()
	{
		$aCCategory = null;
		$result = Model_Coop::getCoopCategoryFromClass($this->aClass['ctID'],null,null,array('ccSort'=>'desc'));
		if (count($result))
		{
			$aCCategory = $result->as_array('ccID');
		}
		$aStuCoop = null;
		$result = Model_Coop::getCoopStudents(array(array('stID','=',$this->aStudent['stID'])));
		if (count($result))
		{
			$aStuCoop = $result->as_array('ccID');
		}


		$aCnt = null;
		$result = Model_Coop::getCoopAlreadyCountFromUser($this->aStudent['stID']);
		if (count($result))
		{
			$aCnt = $result->as_array('ccID');
		}

		if (!is_null($aCCategory))
		{
			foreach ($aCCategory as $sID => $aMC)
			{
				if (!isset($aStuCoop[$sID]))
				{
					unset($aCCategory[$sID]);
					continue;
				}
				$aCCategory[$sID]['already'] = 0;
				if (isset($aCnt[$sID]))
				{
					$aCCategory[$sID]['already'] = (int)$aCnt[$sID]['aCnt'];
				}
			}
		}
		if (count($aCCategory) == 1)
		{
			$sID = array_keys($aCCategory);
			Response::redirect('/s/'.$this->baseName.'/thread/'.$sID[0].$this->sesParam);
		}

		# タイトル
		$sTitle = __('協働板');
		$this->template->set_global('pagetitle',$sTitle);
		# パンくずリスト生成
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		$this->template->content = View::forge($this->vDir.DS.$this->baseName.'/index');
		$this->template->content->set('aCCategory',$aCCategory);
		$this->template->javascript = array('cl.s.coop.js');
		return $this->template;
	}

	public function action_thread($sID = null, $iNO = null)
	{
		$aChk = self::CoopCategoryChecker($sID);
		if (is_array($aChk))
		{
			Session::set('SES_S_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url'].$this->sesParam);
		}
		$this->template->set_global('aCCategory',$this->aCCategory);

		$aPWhere = array(
			array('ci.ccID','=',$sID),
		);
		$aCWhere = $aPWhere;

		if (!is_null($iNO))
		{
			$aChk = self::CoopChecker($sID,$iNO);
			if (is_array($aChk))
			{
				Session::set('SES_S_ERROR_MSG',$aChk['msg']);
				Response::redirect($aChk['url'].$this->sesParam);
			}
			$aPWhere[] = array('ci.cNO','=',$iNO);
			$aCWhere[] = array('ci.cRoot','=',$iNO);
		}
		else
		{
			$aPWhere[] = array('ci.cRoot','=',0);
			$aCWhere[] = array('ci.cRoot','!=',0);
		}

		$aParents = null;
		$result = \Model_Coop::getCoop($aPWhere,null,array('ci.cSort'=>'desc'));
		if (count($result))
		{
			$aParents = $result->as_array();
		}

		if ($this->iDevice == CL_DEV_MB)
		{
			$aCnt = null;
			$aCoops = null;
			$result = \Model_Coop::getCoop($aCWhere,null,array('ci.cRoot'=>'asc','ci.cBranch'=>'asc','ci.cSort'=>'asc','ci.cDate'=>'asc'));
			if (count($result))
			{
				foreach ($result as $aC)
				{
					if (isset($aCnt['r'.$aC['cRoot']]))
					{
						$aCnt['r'.$aC['cRoot']]++;
					}
					else
					{
						$aCnt['r'.$aC['cRoot']] = 1;
					}
					if ($aC['cBranch'] == 0)
					{
						$aCoops[$aC['cRoot']][$aC['cNO']] = $aC;
					}
					else
					{
						if (isset($aCnt['p'.$aC['cBranch']]))
						{
							$aCnt['p'.$aC['cBranch']]++;
						}
						else
						{
							$aCnt['p'.$aC['cBranch']] = 1;
						}
						$aCoops[$aC['cRoot']][$aC['cBranch']]['children'][$aC['cNO']] = $aC;
					}
				}
			}

			$aAlready = null;
			$result = Model_Coop::getCoopAlready(array(array('ccID','=',$sID),array('caID','=',$this->aStudent['stID'])));
			if (count($result))
			{
				$aAlready = $result->as_array('cNO');
			}
			$result = \Model_Coop::setCoopAlready($this->aStudent['stID'],$sID,$aParents);
			\Session::delete('CL_STU_UNREAD_'.$this->aStudent['stID']);
		}

		# タイトル
		$sTitle = $this->aCCategory['ccName'];
		$this->template->set_global('pagetitle',$sTitle);

		# パンくずリスト生成
		$this->aBread[] = array('name'=>__('協働板一覧'),'link'=>'/'.$this->baseName);
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		# カスタムボタン
		if ($this->aCCategory['ccStuWrite'])
		{
			$aCustomBtn = array(
				array(
					'url'  => '#',
					'name' => __('スレッドを立てる'),
					'show' => 1,
					'icon' => 'fa-plus',
					'class' => array('CoopThreadCreate'),
					'option' => array('obj'=>$sID),
				),
			);
			$this->template->set_global('aCustomBtn',$aCustomBtn);
		}

		$this->template->content = View::forge($this->vDir.DS.$this->baseName.'/thread');
		$this->template->content->set('aParents',$aParents);

		if ($this->iDevice == CL_DEV_MB)
		{
			$this->template->content->set('aCoops',$aCoops);
			$this->template->content->set('aCnt',$aCnt);
			$this->template->content->set('aAlready',$aAlready);
		}

		$this->template->javascript = array('cl.s.coop.js','cl.coop.js');
		return $this->template;
	}


	public function action_threadpiece($sID = null, $iNO = null)
	{
		$this->template = View::forge($this->vDir.DS.$this->baseName.'/thread-piece');

		$aChk = self::CoopCategoryChecker($sID);
		if (is_array($aChk))
		{
			$this->template = View::forge('piece-error');
			$this->template->set_global('sMsg',$aChk['msg']);
			return $this->template;
		}
		$this->template->set_global('aCCategory',$this->aCCategory);

		$aPWhere = array(
			array('ci.ccID','=',$sID),
		);
		$aCWhere = $aPWhere;

		$aChk = self::CoopChecker($sID,$iNO);
		if (is_array($aChk))
		{
			$this->template = View::forge('piece-error');
			$this->template->set_global('sMsg',$aChk['msg']);
			return $this->template;
		}
		$aPWhere[] = array('ci.cNO','=',$iNO);
		$aCWhere[] = array('ci.cRoot','=',$iNO);

		$aParents = null;
		$result = \Model_Coop::getCoop($aPWhere,null,array('ci.cSort'=>'desc'));
		if (count($result))
		{
			$aParents = $result->as_array();
		}

		$aCnt = null;
		$aCoops = null;
		$result = \Model_Coop::getCoop($aCWhere,null,array('ci.cRoot'=>'asc','ci.cBranch'=>'asc','ci.cSort'=>'asc','ci.cDate'=>'asc'));
		if (count($result))
		{
			foreach ($result as $aC)
			{
				if (isset($aCnt['r'.$aC['cRoot']]))
				{
					$aCnt['r'.$aC['cRoot']]++;
				}
				else
				{
					$aCnt['r'.$aC['cRoot']] = 1;
				}
				if ($aC['cBranch'] == 0)
				{
					$aCoops[$aC['cRoot']][$aC['cNO']] = $aC;
				}
				else
				{
					if (isset($aCnt['p'.$aC['cBranch']]))
					{
						$aCnt['p'.$aC['cBranch']]++;
					}
					else
					{
						$aCnt['p'.$aC['cBranch']] = 1;
					}
					$aCoops[$aC['cRoot']][$aC['cBranch']]['children'][$aC['cNO']] = $aC;
				}
			}
		}

		$aAlready = null;
		$result = Model_Coop::getCoopAlready(array(array('ccID','=',$sID),array('caID','=',$this->aStudent['stID'])));
		if (count($result))
		{
			$aAlready = $result->as_array('cNO');
		}
		$result = \Model_Coop::setCoopAlready($this->aStudent['stID'],$sID,$aParents);
		\Session::delete('CL_STU_UNREAD_'.$this->aStudent['stID']);

		$this->template->set_global('aParents',$aParents);
		$this->template->set_global('aCoops',$aCoops);
		$this->template->set_global('aCnt',$aCnt);
		$this->template->set_global('aAlready',$aAlready);
		return $this->template;
	}


	public function action_rescreate($sID = null, $iNO = 0)
	{
		$aChk = self::CoopCategoryChecker($sID);
		if (is_array($aChk))
		{
			Session::set('SES_S_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url'].$this->sesParam);
		}
		$bRes = false;
		$sTitle = __('スレッドを立てる');
		$sCheck = 'threadcreate';
		if ($iNO > 0)
		{
			$aChk = self::CoopChecker($sID,$iNO);
			if (is_array($aChk))
			{
				Session::set('SES_S_ERROR_MSG',$aChk['msg']);
				Response::redirect($aChk['url'].$this->sesParam);
			}
			$bRes = true;
			$sCheck = 'rescreate';
			$this->template->set_global('aCoop',$this->aCoop);
			$sTitle = __('コメントする');
		}

		# タイトル
		$this->template->set_global('pagetitle',$sTitle);

		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/'.$this->baseName, 'name'=>__('協働板一覧'));
		$this->aBread[] = array('link'=>'/'.$this->baseName.'/thread/'.$sID,'name'=>$this->aCCategory['ccName']);
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		$this->template->set_global('aCCategory',$this->aCCategory);
		$this->template->set_global('iNO',$iNO);
		$this->template->set_global('bRes',$bRes);
		$this->template->set_global('bEdit',false);

		if (!Input::post(null,false))
		{
			$data = $this->aCoopBase;
			$data['error'] = null;
			if ($aInput = unserialize(Session::get('SES_S_COOP',false)))
			{
				$data = array_merge($data,$aInput);
			}
			$this->template->content = View::forge($this->vDir.DS.$this->baseName.'/resedit',$data);
			$this->template->javascript = array('cl.s.coop.js');
			return $this->template;
		}

		$aInput = Input::post();

		$val = Validation::forge();
		$val->add_callable('Helper_CustomValidation');
		if (!$bRes)
		{
			$val->add_field('c_title', __('タイトル'), 'required|max_length['.CL_TITLE_LENGTH.']');
		}
		$val->add_field('c_text', __('本文'), 'required');
		if (!$val->run())
		{
			$data = $this->aCoopBase;
			$data['error'] = $val->error();
			$data = array_merge($data,$aInput);
			$this->template->content = View::forge($this->vDir.DS.$this->baseName.'/resedit',$data);
			$this->template->javascript = array('cl.s.coop.js','cl.coop.js');
			return $this->template;
		}

		Session::set('SES_S_COOP',serialize($aInput));
		Response::redirect('/s/'.$this->baseName.'/rescheck/'.$sID.DS.$sCheck.DS.$iNO.$this->sesParam);
	}

	public function action_resedit($sID = null,$iNO = 0)
	{
		$aChk = self::CoopCategoryChecker($sID);
		if (is_array($aChk))
		{
			Session::set('SES_S_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url'].$this->sesParam);
		}
		$aChk = self::CoopChecker($sID,$iNO);
		if (is_array($aChk))
		{
			Session::set('SES_S_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url'].$this->sesParam);
		}

		$bRes = false;
		$sTitle = __('スレッドの編集');
		$sCheck = 'threadedit';
		if ($this->aCoop['cRoot'] > 0)
		{
			$bRes = true;
			$sTitle = __('コメントの編集');
			$sCheck = 'resedit';
		}

		# タイトル
		$this->template->set_global('pagetitle',$sTitle);

		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/'.$this->baseName, 'name'=>__('協働板一覧'));
		$this->aBread[] = array('link'=>'/'.$this->baseName.'/thread/'.$sID,'name'=>$this->aCCategory['ccName']);
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		$this->template->set_global('aCCategory',$this->aCCategory);
		$this->template->set_global('aCoop',$this->aCoop);
		$this->template->set_global('iNO',$iNO);
		$this->template->set_global('bRes',$bRes);
		$this->template->set_global('bEdit',true);

		if (!Input::post(null,false))
		{
			$data = $this->aCoopBase;
			$data['error'] = null;
			if (!$aInput = unserialize(Session::get('SES_S_COOP',false)))
			{
				$aInput = array(
					'c_title'=>$this->aCoop['cTitle'],
					'c_text' =>$this->aCoop['cText'],
				);
			}
			$data = array_merge($data,$aInput);
			$this->template->content = View::forge($this->vDir.DS.$this->baseName.'/resedit',$data);
			$this->template->javascript = array('cl.s.coop.js');
			return $this->template;
		}

		$aInput = Input::post();

		$val = Validation::forge();
		$val->add_callable('Helper_CustomValidation');
		if (!$bRes)
		{
			$val->add_field('c_title', __('タイトル'), 'required|max_length['.CL_TITLE_LENGTH.']');
		}
		$val->add_field('c_text', __('本文'), 'required');
		if (!$val->run())
		{
			$data = $this->aMatBase;
			$data['error'] = $val->error();
			$data = array_merge($data,$aInput);
			$this->template->content = View::forge($this->vDir.DS.$this->baseName.'/resedit',$data);
			$this->template->javascript = array('cl.s.coop.js');
			return $this->template;
		}

		Session::set('SES_S_COOP',serialize($aInput));
		Response::redirect('/s/'.$this->baseName.'/rescheck/'.$sID.DS.$sCheck.DS.$iNO.$this->sesParam);
	}

	public function action_rescheck($sID = null, $sM = null, $iNO = 0)
	{
		$aChk = self::CoopCategoryChecker($sID);
		if (is_array($aChk))
		{
			Session::set('SES_S_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url'].$this->sesParam);
		}
		$bRes = false;
		$bEdit = false;
		$sMode = 'rescreate';
		$sTitle = __('スレッドを立てる');
		$sFinMsg = 'スレッドを登録';

		switch ($sM)
		{
			case 'rescreate':
				$bRes = true;
				$sTitle = __('コメントする');
				$sFinMsg = 'コメントを登録';
			break;
			case 'resedit':
				$bRes = true;
				$bEdit = true;
				$sMode = 'resedit';
				$sTitle = __('コメントの編集');
				$sFinMsg = 'コメントを更新';
			break;
			case 'threadedit':
				$bEdit = true;
				$sMode = 'resedit';
				$sTitle = __('スレッドの編集');
				$sFinMsg = 'スレッドを更新';
			break;
			default:
			break;
		}
		if ($iNO > 0)
		{
			$aChk = self::CoopChecker($sID,$iNO);
			if (is_array($aChk))
			{
				Session::set('SES_S_ERROR_MSG',$aChk['msg']);
				Response::redirect($aChk['url'].$this->sesParam);
			}
			$this->template->set_global('aCoop',$this->aCoop);
			if ($bRes && $bEdit)
			{
				$aMyCoop = $this->aCoop;
				$aChk = self::CoopChecker($sID,$this->aCoop['cParent']);
				$this->template->set_global('aBaseCoop',$this->aCoop);
			}
			else
			{
				$this->template->set_global('aBaseCoop',$this->aCoop);
			}
		}

		$aInput = $this->aCoopBase;
		$aSes = unserialize(Session::get('SES_S_COOP',false));
		if (!$aSes)
		{
			Session::set('SES_S_ERROR_MSG',__('登録内容が取得できませんでした。再度入力してください。'));
			Response::redirect('/s/'.$this->baseName.'/'.$sMode.'/'.$sID.(($iNO)? DS.$iNO:'').$this->sesParam);
		}
		$aInput = array_merge($aInput,$aSes);

		# タイトル
		$this->template->set_global('pagetitle',$sTitle);

		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/'.$this->baseName, 'name'=>__('協働板一覧'));
		$this->aBread[] = array('link'=>'/'.$this->baseName.'/thread/'.$sID,'name'=>$this->aCCategory['ccName']);
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		$this->template->set_global('aCCategory',$this->aCCategory);
		$this->template->set_global('iNO',$iNO);
		$this->template->set_global('bRes',$bRes);
		$this->template->set_global('bEdit',$bEdit);
		$this->template->set_global('sCheck',$sM);

		if (!Input::post(null,false))
		{
			$data = $this->aCoopBase;
			$data['error'] = null;
			$data = array_merge($data,$aInput);
			$this->template->content = View::forge($this->vDir.DS.$this->baseName.'/rescheck',$data);
			$this->template->javascript = array('cl.s.coop.js','cl.coop.js');
			return $this->template;
		}

		$aPost = Input::post(null,false);
		if (isset($aPost['back']))
		{
			Response::redirect('/s/'.$this->baseName.'/'.$sMode.'/'.$sID.(($iNO)? DS.$iNO:'').$this->sesParam);
		}

		try
		{
			if ($bEdit)
			{
				$aUpdate = array(
					'cTitle'   => (isset($aInput['c_title']))? $aInput['c_title']:'',
					'cText'    => $aInput['c_text'],
					'cCharNum' => mb_strlen($aInput['c_text']),
					'cName'    => $this->aStudent['stName'],
					'cDate'    => date('YmdHis'),
				);
				$aWhere = array(
					array('cNO','=',$iNO),
					array('ccID','=',$sID),
				);
				$result = \Model_Coop::updateCoop($aUpdate,$aWhere,null,$sID);
			}
			else
			{
				$aInsert = array(
					'ccID'     => $sID,
					'cTitle'   => (isset($aInput['c_title']))? $aInput['c_title']:'',
					'cText'    => $aInput['c_text'],
					'cCharNum' => mb_strlen($aInput['c_text']),
					'cID'      => $this->aStudent['stID'],
					'cName'    => $this->aStudent['stName'],
					'cRoot'    => (($this->aCoop['cRoot'] > 0)? $this->aCoop['cRoot']:$iNO),
					'cBranch'  => (($this->aCoop['cBranch'] > 0)? $this->aCoop['cBranch']:(($this->aCoop['cRoot'] > 0)? $this->aCoop['cNO']:0)),
					'cParent'  => $iNO,
					'cDate'    => date('YmdHis'),
				);
				$result = \Model_Coop::insertCoop($aInsert);
			}
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_S_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		$sWriter = null;
		$aReply = null;
		if (isset($aPost['mailr']) && $aPost['mailr'] == 1 && $this->aCoop['cID'] != $this->aStudent['stID'])
		{
			$bTeach = preg_match('/^[t|a]/', $this->aCoop['cID']);
			$cName = ($this->aCoop['atName'])? $this->aCoop['atName']:(($this->aCoop['ttName'])? $this->aCoop['ttName']:(($this->aCoop['stName'])? $this->aCoop['stName']:$this->aCoop['cName']));
			if (preg_match('/^t/', $this->aCoop['cID']))
			{
				$result = Model_Teacher::getTeacherFromID_ex($this->aCoop['cID']);
				if (count($result))
				{
					$aU = $result->current();
					$aReply = array(
						'type'  => 't',
						'ttID'  => $aU['ttID'],
						'name'  => $cName,
						'mail'  => $aU['ttMail'],
						'sub'   => $aU['ttSubMail'],
						'app'   => $aU['ttApp'],
						'token' => $aU['ttDeviceToken'],
					);
				}
			}
			elseif (preg_match('/^a/', $this->aCoop['cID']))
			{
				$result = Model_Assistant::getAssistantFromID($this->aCoop['cID']);
				if (count($result))
				{
					$aU = $result->current();
					$aReply = array(
						'type'  => 'a',
						'atID'  => $aU['atID'],
						'name'  => $cName,
						'mail'  => $aU['atMail'],
						'sub'   => null,
						'app'   => 0,
						'token' => null,
					);
				}
			}
			else
			{
				$result = Model_Student::getStudentFromID($this->aCoop['cID']);
				if (count($result))
				{
					$aU = $result->current();
					if ($aU['stMail'] || $aU['stSubMail'] || $aU['stApp'])
					{
						$aReply = array(
							'type'  => 's',
							'stID'  => $aU['stID'],
							'name'  => $cName,
							'mail'  => $aU['stMail'],
							'sub'   => $aU['stSubMail'],
							'app'   => $aU['stApp'],
							'token' => $aU['stDeviceToken'],
						);
					}
				}
			}
			switch ($this->aCCategory['ccAnonymous'])
			{
				case 0:
					$sWriter = __('匿名');
				break;
				case 1:
					if ($bTeach):
						$sWriter = $cName;
					else:
						$sWriter = __('匿名');
					endif;
				break;
				case 2:
					$sWriter = $cName;
				break;
			}
		}
		$iTeacher = (isset($aPost['mailt']) && $aPost['mailt'] == 1)? 1:0;
		$iStudent = (isset($aPost['mails']) && $aPost['mails'] == 1)? 1:0;
		if ($iTeacher || $iStudent || !is_null($aReply))
		{
			$sUn = ($this->aCCategory['ccAnonymous'] < 2)? __('匿名'):$this->aStudent['stName'];
			$aOptions = array(
				'cID'      => $this->aStudent['stID'],
				'cMail'    => $this->aStudent['stMail'],
				'cName'    => $this->aStudent['stName'],
				'cSubMail' => $this->aStudent['stSubMail'],
				'files'    => array(),
				'cTitle'   => ((isset($aInput['c_title']))? $aInput['c_title']:''),
				'cText'    => $aInput['c_text'],
				'sWriter'  => $sWriter,
				'cUnknown' => $sUn,
			);
			\ClFunc_Mailsend::MailSendToCoop($this->aClass['ctID'],$this->aCCategory['ccID'],'s',$aReply,(int)$iTeacher,(int)$iStudent,$aOptions);
		}

		Session::delete('SES_S_COOP');
		Session::set('SES_S_NOTICE_MSG',__($sFinMsg.'しました。'));

		$url = '/s/'.$this->baseName.'/thread/'.$sID.$this->sesParam;
		Response::redirect($url);
	}

	public function action_resdelete($sID = null,$iNO = null)
	{
		$aChk = self::CoopCategoryChecker($sID);
		if (is_array($aChk))
		{
			Session::set('SES_S_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url'].$this->sesParam);
		}
		$aChk = self::CoopChecker($sID,$iNO);
		if (is_array($aChk))
		{
			Session::set('SES_S_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url'].$this->sesParam);
		}

		# タイトル
		$sTitle = __('削除');
		$this->template->set_global('pagetitle',$sTitle);

		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/'.$this->baseName, 'name'=>__('協働板一覧'));
		$this->aBread[] = array('link'=>'/'.$this->baseName.'/thread/'.$sID,'name'=>$this->aCCategory['ccName']);
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		$this->template->set_global('aCCategory',$this->aCCategory);
		$this->template->set_global('aCoop',$this->aCoop);
		$this->template->set_global('iNO',$iNO);

		if (!Input::post(null,false))
		{
			$this->template->content = View::forge($this->vDir.DS.$this->baseName.'/resdelete');
			return $this->template;
		}
		$aPost = Input::post(null,false);
		if (isset($aPost['back']))
		{
			Response::redirect('/s/'.$this->baseName.'/thread/'.$sID.$this->sesParam);
		}

		try
		{
			$result = Model_Coop::deleteCoop($this->aCoop);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_S_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		Session::set('SES_S_NOTICE_MSG',__('削除しました。'));

		$url = '/s/'.$this->baseName.'/thread/'.$sID.$this->sesParam;
		Response::redirect($url);
	}



	public function action_res($sID = null)
	{
		$url = '/s/'.$this->baseName.'/thread/'.$sID;
		$aChk = self::CoopCategoryChecker($sID);
		if (is_array($aChk))
		{
			Session::set('SES_S_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}

		$sMsg = __('必要な情報を取得することができませんでした。');
		if (!Input::post(null,false))
		{
			Session::set('SES_S_ERROR_MSG',$sMsg);
			Response::redirect($url);
		}

		$aInput = Input::post();
		$iNO = $aInput['c_no'];

		if ($iNO != 0)
		{
			$aChk = self::CoopChecker($sID,$iNO);
			if (is_array($aChk))
			{
				Session::set('SES_S_ERROR_MSG',$aChk['msg']);
				Response::redirect($aChk['url']);
			}
		}
		else if ($aInput['mode'] != 'pcreate')
		{
			Session::set('SES_S_ERROR_MSG',$sMsg);
			Response::redirect($url);
		}

		$sResMsg = '登録';

		if ($aInput['mode'] == 'pcreate')
		{
			$sResName = 'スレッド';
			$afID = null;
			try
			{
				$afID = \Clfunc_Common::CoopFileSave($aInput,$this->aStudent['stID'],$this->sTempFilePath,$this->sAwsSavePath);
				$aInsert = array(
					'ccID'     => $sID,
					'cTitle'   => $aInput['c_title'],
					'fID1'     => $afID[1]['id'],
					'fID2'     => $afID[2]['id'],
					'fID3'     => $afID[3]['id'],
					'cText'    => $aInput['c_text'],
					'cCharNum' => mb_strlen($aInput['c_text']),
					'cID'      => $this->aStudent['stID'],
					'cName'    => $this->aStudent['stName'],
					'cDate'    => date('YmdHis'),
					'cRoot'    => 0,
					'fSumSize' => ($afID[1]['size'] + $afID[2]['size'] + $afID[3]['size']),
				);
				$result = \Model_Coop::insertCoop($aInsert);
			}
			catch (Exception $e)
			{
				if (!is_null($afID))
				{
					foreach ($afID as $i => $aF)
					{
						if (!$aF['id'])
						{
							continue;
						}
						$sfID = $aF['id'];
						$sFile = $aF['file'];
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
						\Model_File::deleteFile($sfID);
					}
				}
				Session::set('SES_S_ERROR_MSG',__('登録に失敗しました。').$e->getMessage());
				Response::redirect($url);
			}

			if (!is_null($afID))
			{
				foreach ($afID as $i => $aF)
				{
					if (!$aF['id'])
					{
						continue;
					}
					@unlink($aF['sourcefile']);
					@unlink($aF['thumbfile']);
				}
			}
		}
		else
		{
			Session::set('SES_S_ERROR_MSG',$sMsg);
			Response::redirect($url);
		}

		$iTeacher = (isset($aInput['mail-teacher']) && $aInput['mail-teacher'] == 1)? 1:0;
		$iStudent = (isset($aInput['mail-student']) && $aInput['mail-student'] == 1)? 1:0;
		if ($iTeacher || $iStudent)
		{
			$sUn = ($this->aCCategory['ccAnonymous'] < 2)? __('匿名'):$this->aStudent['stName'];
			$aOptions = array(
				'cID'    => $this->aStudent['stID'],
				'cMail'  => $this->aStudent['stMail'],
				'cName'  => $this->aStudent['stName'],
				'cSubMail' => $this->aStudent['stSubMail'],
				'files'  => $afID,
				'cTitle' => $aInput['c_title'],
				'cText'  => $aInput['c_text'],
				'sWriter'  => '',
				'cUnknown' => $sUn,
			);
			\ClFunc_Mailsend::MailSendToCoop($this->aClass['ctID'],$this->aCCategory['ccID'],'s',null,(int)$iTeacher,(int)$iStudent,$aOptions);
		}

		Session::set('SES_S_NOTICE_MSG',__($sResName.'を'.$sResMsg.'しました。'));
		Response::redirect($url);
	}

	private function CoopCategoryChecker($sCcID = null)
	{
		if (is_null($this->aClass))
		{
			return array('msg'=>__('講義情報が確認できませんでした。'),'url'=>'/s/index');
		}
		if (is_null($sCcID))
		{
			return array('msg'=>__('協働板情報が送信されていません。'),'url'=>'/s/'.$this->baseName);
		}
		$result = Model_Coop::getCoopCategoryFromID($sCcID);
		if (!count($result))
		{
			return array('msg'=>__('指定された協働板が見つかりません。'),'url'=>'/s/'.$this->baseName);
		}
		$this->aCCategory = $result->current();

		$result = Model_Coop::getCoopStudents(array(array('stID','=',$this->aStudent['stID']),array('ccID','=',$sCcID)));
		if (!count($result))
		{
			return array('msg'=>__('指定された協働板が見つかりません。'),'url'=>'/s/'.$this->baseName);
		}
		return true;
	}

	private function CoopChecker($sCcID = null, $iCtNO = null)
	{
		if (is_null($this->aClass))
		{
			return array('msg'=>__('講義情報が確認できませんでした。'),'url'=>'/s/index');
		}
		if (is_null($sCcID) || is_null($iCtNO))
		{
			return array('msg'=>__('必要な情報が送信されていません。'),'url'=>'/s/'.$this->baseName);
		}
		$result = Model_Coop::getCoop(array(array('ci.ccID','=',$sCcID),array('ci.cNO','=',$iCtNO)));
		if (!count($result))
		{
			return array('msg'=>__('指定された記事が見つかりません。'),'url'=>'/s/'.$this->baseName);
		}
		$this->aCoop = $result->current();

		return true;
	}
}