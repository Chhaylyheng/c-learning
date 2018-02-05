<?php
class Controller_T_Ajax_Contact extends Controller_T_Ajax
{
	public function post_ContactRes()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>__('情報が正しく送信されていません。'));
		$par = Input::post();
		if ($par)
		{
			$result = Model_Class::getClassFromID($par['ct'],1);
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('講義情報が見つかりません。'));
				$this->response($res);
				return;
			}
			$aClass = $result->current();

			$iNO = (int)$par['cn'];
			if ($iNO == 0)
			{
				$this->response($res);
				return;
			}
			$result = Model_Contact::getContact($aClass['ctID'],null,array(array('co.no','=',$iNO)));
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('情報が見つかりません。'));
				$this->response($res);
				return;
			}
			$aContact = $result->current();

			$sDate = date('Y-m-d H:i:s');

			switch ($par['m'])
			{
				case 'input':
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
							'parent'    => $iNO,
							'coSubject' => $par['c_subject'],
							'coBody'    => $par['c_text'],
							'ctID'      => $aClass['ctID'],
							'stID'      => $aContact['stID'],
							'coID'      => $sCoID,
							'coName'    => $sCoName,
							'coDate'    => $sDate,
							'coTeach'   => 1,
						);
						$cNO = \Model_Contact::insertContact($aInsert);
					}
					catch (Exception $e)
					{
						$res = array('err'=>-2,'res'=>'','msg'=>__('送信に失敗しました。').$e->getMessage());
						$this->response($res);
						return;
					}
				break;
			}
		}

		$res['err'] = 0;
		$res['res'] = array(
			'no'        => $cNO,
			'parent'    => $iNO,
			'coName'    => $sCoName,
			'coDate'    => ClFunc_Tz::tz('Y/m/d H:i',$this->tz,$sDate),
			'coSubject' => $par['c_subject'],
			'coBody'    => nl2br(\Clfunc_Common::url2link($par['c_text'],480)),
		);

		$aOptions = array(
			'cID'      => $sCoID,
			'cMail'    => $sCoMail,
			'cName'    => $sCoName,
			'cSubMail' => $sCoSubMail,
			'cSubject' => $par['c_subject'],
			'cBody'    => $par['c_text'],
		);
		\ClFunc_Mailsend::MailSendToContact($aClass['ctID'],$aContact['stID'],'t',$aOptions);

		$res['msg'] = __('返信しました。');
		$this->response($res);
		return;
	}

	public function post_ContactStatus()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>__('情報が正しく送信されていません。'));
		$par = Input::post();
		if ($par)
		{
			$result = Model_Class::getClassFromID($par['ct'],1);
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('講義情報が見つかりません。'));
				$this->response($res);
				return;
			}
			$aClass = $result->current();

			$iNO = $par['cn'];
			$result = Model_Contact::getContact($aClass['ctID'],$this->aStudent['stID'],array(array('co.no','=',$iNO)));
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('情報が見つかりません。'));
				$this->response($res);
				return;
			}
			$aContact = $result->current();

			$iState = ($par['m'] == 'done')? 1:0;

			try
			{
				$aUpdate = array(
					'coStatus' => $iState,
				);
				$cNO = \Model_Contact::updateStatus($aUpdate, $aClass['ctID'], $this->aStudent['stID'], $iNO);
			}
			catch (Exception $e)
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('処理に失敗しました。時間をおいてから、再度実行してください。').$e->getMessage());
				$this->response($res);
				return;
			}
		}

		$res['err'] = 0;
		$res['res'] = array(
			'class' => ($iState)? 'font-blue':'font-red',
			'text' => ($iState)? __('済'):__('未'),
		);
		$this->response($res);
		return;
	}


	public function post_ContactDelete()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>__('情報が正しく送信されていません。'));
		$par = Input::post();
		if ($par)
		{
			$iNO = $par['cn'];
			$result = Model_Contact::getContact(null,null,array(array('co.no','=',$iNO)));
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('情報が見つかりません。'));
				$this->response($res);
				return;
			}
			$aContact = $result->current();

			try
			{
				$result = \Model_Contact::deleteContact($iNO);
			}
			catch (Exception $e)
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('処理に失敗しました。時間をおいてから、再度実行してください。').$e->getMessage());
				$this->response($res);
				return;
			}
		}

		$res = array('err'=>0,'res'=>array('childNum'=>$result));
		$this->response($res);
		return;
	}


	public function post_ContactRead()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>__('情報が正しく送信されていません。'));
		$par = Input::post();
		if ($par)
		{
			$iNO = $par['cn'];
			$result = Model_Contact::getContact(null,null,array(array('co.no','=',$iNO)));
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('情報が見つかりません。'));
				$this->response($res);
				return;
			}
			$aContact = $result->current();

			try
			{
				$aUpdate = array('coRead'=>1);
				$aWhere = array(array('coTeach','=',0),array('no','=',$iNO));
				$result = \Model_Contact::updateContact($aUpdate,$aWhere);
				$aWhere = array(array('coID','=',0),array('parent','=',$iNO));
				$result = \Model_Contact::updateContact($aUpdate,$aWhere);
			}
			catch (Exception $e)
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('処理に失敗しました。時間をおいてから、再度実行してください。').$e->getMessage());
				$this->response($res);
				return;
			}
		}

		\Session::delete('CL_TEACH_UNREAD_'.$this->sCurrentID);
		$res = array('err'=>0,'res'=>'');
		$this->response($res);
		return;
	}



}

