<?php
class Controller_S_Contact extends Controller_S_Baseclass
{
	private $aContact = null;
	private $baseName = 'contact';
	private $aContactBase = array(
		'c_subject'=>null,
		'c_text' =>null,
	);


	public function action_index()
	{
		$aNew = null;
		$result = Model_Contact::getContact($this->aClass['ctID'],$this->aStudent['stID'],null,null,array('co.no'=>'desc'));
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
					if ($aC['coID'] != $this->aStudent['stID'] && !$aC['coRead'])
					{
						$aNew[$aC['parent']] = true;
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

		# カスタムボタン
		$aCustomBtn = array(
			array(
				'url'  => '/s/'.$this->baseName.'/rescreate',
				'name' => __('新規作成'),
				'show' => 1,
				'icon' => 'fa-envelope-o',
				'class' => array('ContactCreate'),
			),
		);
		$this->template->set_global('aCustomBtn',$aCustomBtn);

		$this->template->content = View::forge($this->vDir.DS.$this->baseName.DS.'index');
		$this->template->content->set('aContact',$this->aContact);
		$this->template->content->set('aNew',$aNew);
		$this->template->javascript = array('cl.s.contact.js','cl.contact.js');
		return $this->template;
	}

	public function action_thread($iNO = null)
	{
		$url = '/s/'.$this->baseName;

		$aChk = self::ContactChecker($iNO);
		if (is_array($aChk))
		{
			Session::set('SES_S_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url'].$this->sesParam);
		}

		$aChildren = null;
		$bNew = null;
		$result = Model_Contact::getContact($this->aClass['ctID'],$this->aStudent['stID'],array(array('co.parent','=',$iNO)),null,array('co.no'=>'asc'));
		if (count($result))
		{
			foreach ($result as $aC)
			{
				$aChildren[$aC['no']] = $aC;
				if ($aC['coID'] != $this->aStudent['stID'] && !$aC['coRead'])
				{
					$bNew = true;
				}
			}
		}

		try
		{
			$aUpdate = array('coRead'=>1);
			$aWhere = array(array('coID','!=',$this->aStudent['stID']),array('no','=',$iNO));
			$result = \Model_Contact::updateContact($aUpdate,$aWhere);
			$aWhere = array(array('coID','!=',$this->aStudent['stID']),array('parent','=',$iNO));
			$result = \Model_Contact::updateContact($aUpdate,$aWhere);
			\Session::delete('CL_STU_CONTACT_UNREAD_'.$this->aStudent['stID']);
		}
		catch (Exception $e)
		{
			Session::set('SES_S_ERROR_MSG',__('必要な情報を取得することができませんでした。').$e->getMessage());
			Response::redirect($url.$this->sesParam);
			return;
		}

		# タイトル
		$sTitle = ($this->aContact['coSubject'])? $this->aContact['coSubject']:'(No Subject)';
		$this->template->set_global('pagetitle',$sTitle);
		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/'.$this->baseName, 'name'=>__('連絡・相談'));
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		# カスタムボタン
		$aCustomBtn = array(
			array(
				'url'  => '/s/'.$this->baseName.'/rescreate',
				'name' => __('新規作成'),
				'show' => 1,
				'icon' => 'fa-envelope-o',
				'class' => array('ContactCreate'),
			),
		);
		$this->template->set_global('aCustomBtn',$aCustomBtn);

		$this->template->content = View::forge($this->vDir.DS.$this->baseName.DS.'thread');
		$this->template->content->set('aContact',$this->aContact);
		$this->template->content->set('aChildren',$aChildren);
		$this->template->content->set('bNew',$bNew);
		$this->template->javascript = array('cl.s.contact.js','cl.contact.js');
		return $this->template;
	}


	public function action_res()
	{
		$url = '/s/'.$this->baseName;
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
			$aChk = self::ContactChecker($iNO);
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

		$val = Validation::forge();
		$val->add_callable('Helper_CustomValidation');
		$val->add_field('c_subject', __('件名'), 'max_length['.CL_TITLE_LENGTH.']');
		$val->add_field('c_text', __('本文'), 'required');
		if (!$val->run())
		{
			$sMsg = implode('<br>', $val->error());
			Session::set('SES_S_ERROR_MSG',$sMsg);
			Response::redirect($url);
		}

		$sResMsg = '送信';

		if ($aInput['mode'] == 'pcreate')
		{
			$sResName = '連絡・相談';
			try
			{
				$aInsert = array(
					'parent'    => 0,
					'ctID'      => $this->aClass['ctID'],
					'stID'      => $this->aStudent['stID'],
					'coID'      => $this->aStudent['stID'],
					'coSubject' => $aInput['c_subject'],
					'coBody'    => $aInput['c_text'],
					'coName'    => $this->aStudent['stName'],
					'coDate'    => date('YmdHis'),
				);
				$result = \Model_Contact::insertContact($aInsert);
			}
			catch (Exception $e)
			{
				Session::set('SES_S_ERROR_MSG',__('送信に失敗しました。').$e->getMessage());
				Response::redirect($url);
			}
		}
		else
		{
			Session::set('SES_S_ERROR_MSG',$sMsg);
			Response::redirect($url);
		}

		$aOptions = array(
			'cID'      => $this->aStudent['stID'],
			'cMail'    => $this->aStudent['stMail'],
			'cName'    => $this->aStudent['stName'],
			'cSubMail' => $this->aStudent['stSubMail'],
			'cSubject' => $aInput['c_subject'],
			'cBody'    => $aInput['c_text'],
		);
		\ClFunc_Mailsend::MailSendToContact($this->aClass['ctID'],$this->aStudent['stID'],'s',$aOptions);

		Session::set('SES_S_NOTICE_MSG',__($sResName.'を'.$sResMsg.'しました。'));
		Response::redirect($url);
	}



	public function action_rescreate($iNO = 0)
	{
		$sCheck = 'rescreate';
		$sTitle = __('新規作成');
		if ($iNO > 0)
		{
			$aChk = self::ContactChecker($iNO);
			if (is_array($aChk))
			{
				Session::set('SES_S_ERROR_MSG',$aChk['msg']);
				Response::redirect($aChk['url'].$this->sesParam);
			}
			$sCheck = 'rescreate';
			$this->template->set_global('aContact',$this->aContact);
			$sTitle = __('返信する');
		}

		# タイトル
		$this->template->set_global('pagetitle',$sTitle);

		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/'.$this->baseName, 'name'=>__('連絡・相談'));
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		$this->template->set_global('iNO',$iNO);
		$this->template->set_global('bEdit',false);

		if (!Input::post(null,false))
		{
			$data = $this->aContactBase;
			if ($iNO > 0)
			{
				$data['c_subject'] = 'Re:'.$this->aContact['coSubject'];
			}
			$data['error'] = null;
			if ($aInput = unserialize(Session::get('SES_S_CONTACT',false)))
			{
				$data = array_merge($data,$aInput);
			}
			$this->template->content = View::forge($this->vDir.DS.$this->baseName.'/resedit',$data);
			$this->template->javascript = array('cl.s.contact.js','cl.contact.js');
			return $this->template;
		}

		$aInput = Input::post();

		$val = Validation::forge();
		$val->add_callable('Helper_CustomValidation');
		$val->add_field('c_subject', __('件名'), 'max_length['.CL_TITLE_LENGTH.']');
		$val->add_field('c_text', __('本文'), 'required');
		if (!$val->run())
		{
			$data = $this->aContactBase;
			$data['error'] = $val->error();
			$data = array_merge($data,$aInput);
			$this->template->content = View::forge($this->vDir.DS.$this->baseName.'/resedit',$data);
			$this->template->javascript = array('cl.s.contact.js','cl.contact.js');
			return $this->template;
		}

		Session::set('SES_S_CONTACT',serialize($aInput));
		Response::redirect('/s/'.$this->baseName.'/rescheck/'.$sCheck.DS.$iNO.$this->sesParam);
	}


	public function action_rescheck($sM = null, $iNO = 0)
	{
		$sMode = 'rescreate';
		$sTitle = __('新規作成');
		$sFinMsg = '連絡・相談を送信';

		switch ($sM)
		{
			case 'rescreate':
				$bRes = true;
				$sTitle = __('返信する');
				$sFinMsg = '返信';
			break;
			default:
			break;
		}
		if ($iNO > 0)
		{
			$aChk = self::ContactChecker($iNO);
			if (is_array($aChk))
			{
				Session::set('SES_S_ERROR_MSG',$aChk['msg']);
				Response::redirect($aChk['url'].$this->sesParam);
			}
			$this->template->set_global('aContact',$this->aContact);
		}

		$aInput = $this->aContactBase;
		$aSes = unserialize(Session::get('SES_S_CONTACT',false));
		if (!$aSes)
		{
			Session::set('SES_S_ERROR_MSG',__('登録内容が取得できませんでした。再度入力してください。'));
			Response::redirect('/s/'.$this->baseName.'/'.$sMode.(($iNO)? DS.$iNO:'').$this->sesParam);
		}
		$aInput = array_merge($aInput,$aSes);

		# タイトル
		$this->template->set_global('pagetitle',$sTitle);

		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/'.$this->baseName, 'name'=>__('連絡・相談'));
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		$this->template->set_global('iNO',$iNO);
		$this->template->set_global('sCheck',$sM);

		if (!Input::post(null,false))
		{
			$data = $this->aContactBase;
			$data['error'] = null;
			$data = array_merge($data,$aInput);
			$this->template->content = View::forge($this->vDir.DS.$this->baseName.'/rescheck',$data);
			$this->template->javascript = array('cl.s.contact.js','cl.contact.js');
			return $this->template;
		}

		$aPost = Input::post(null,false);
		if (isset($aPost['back']))
		{
			Response::redirect('/s/'.$this->baseName.'/'.$sMode.(($iNO)? DS.$iNO:'').$this->sesParam);
		}

		try
		{
			$aInsert = array(
				'parent'    => $iNO,
				'ctID'      => $this->aClass['ctID'],
				'stID'      => $this->aStudent['stID'],
				'coID'      => $this->aStudent['stID'],
				'coSubject' => $aInput['c_subject'],
				'coBody'    => $aInput['c_text'],
				'coName'    => $this->aStudent['stName'],
				'coDate'    => date('YmdHis'),
			);
			$result = \Model_Contact::insertContact($aInsert);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_S_ERROR_MSG',__('処理に失敗しました。時間をおいてから、再度実行してください。').$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		$aOptions = array(
			'cID'      => $this->aStudent['stID'],
			'cMail'    => $this->aStudent['stMail'],
			'cName'    => $this->aStudent['stName'],
			'cSubMail' => $this->aStudent['stSubMail'],
			'cSubject' => $aInput['c_subject'],
			'cBody'    => $aInput['c_text'],
		);
		\ClFunc_Mailsend::MailSendToContact($this->aClass['ctID'],$this->aStudent['stID'],'s',$aOptions);

		Session::delete('SES_S_CONTACT');
		Session::set('SES_S_NOTICE_MSG',__($sFinMsg.'しました。'));

		$url = '/s/'.$this->baseName.'/thread/'.(($iNO)? $iNO:$result).$this->sesParam;
		Response::redirect($url);
	}

	public function action_resdelete($iNO = null)
	{
		$aChk = self::ContactChecker($iNO);
		if (is_array($aChk))
		{
			Session::set('SES_S_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url'].$this->sesParam);
		}
		if ($this->aContact['parent'])
		{
			$iParent = $this->aContact['parent'];
			$aContact = $this->aContact;
			$aChk = self::ContactChecker($iParent);
			if (is_array($aChk))
			{
				Session::set('SES_S_ERROR_MSG',$aChk['msg']);
				Response::redirect($aChk['url'].$this->sesParam);
			}
			$sParent = $this->aContact['coSubject'];
			$this->aContact = $aContact;
		}
		else
		{
			$iParent = $iNO;
			$sParent = $this->aContact['coSubject'];
		}

		# タイトル
		$sTitle = __('削除');
		$this->template->set_global('pagetitle',$sTitle);

		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/'.$this->baseName, 'name'=>__('連絡・相談'));
		$this->aBread[] = array('link'=>'/'.$this->baseName.'/thread/'.$iParent,'name'=>(($sParent)? $sParent:'(No subject)'));
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		$this->template->set_global('aContact',$this->aContact);
		$this->template->set_global('iNO',$iNO);

		if (!Input::post(null,false))
		{
			$this->template->content = View::forge($this->vDir.DS.$this->baseName.'/resdelete');
			return $this->template;
		}
		$aPost = Input::post(null,false);
		if (isset($aPost['back']))
		{
			Response::redirect('/s/'.$this->baseName.'/thread/'.$iParent.$this->sesParam);
		}

		try
		{
			$result = Model_Contact::deleteContact($iNO);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_S_ERROR_MSG',__('処理に失敗しました。時間をおいてから、再度実行してください。').$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		Session::set('SES_S_NOTICE_MSG',__('削除しました。'));

		if ($iParent == $iNO)
		{
			$url = '/s/'.$this->baseName.$this->sesParam;
		}
		else
		{
			$url = '/s/'.$this->baseName.'/thread/'.$iParent.$this->sesParam;
		}
		Response::redirect($url);
	}

	private function ContactChecker($iNO = null)
	{
		if (is_null($this->aClass))
		{
			return array('msg'=>__('講義情報が確認できませんでした。'),'url'=>'/s/index');
		}
		if (is_null($iNO))
		{
			return array('msg'=>__('必要な情報が送信されていません。'),'url'=>'/s/'.$this->baseName);
		}
		$result = Model_Contact::getContact($this->aClass['ctID'],$this->aStudent['stID'],array(array('co.no','=',$iNO)));
		if (!count($result))
		{
			return array('msg'=>__('指定された連絡・相談が見つかりません。'),'url'=>'/s/'.$this->baseName);
		}
		$this->aContact = $result->current();

		return true;
	}


}
