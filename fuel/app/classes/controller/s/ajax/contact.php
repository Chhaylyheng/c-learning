<?php
class Controller_S_Ajax_Contact extends Controller_S_Ajax
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

			$iNO = $par['cn'];
			if ($iNO != 0)
			{
				$result = Model_Contact::getContact($aClass['ctID'],$this->aStudent['stID'],array(array('co.no','=',$iNO)));
				if (!count($result))
				{
					$res = array('err'=>-2,'res'=>'','msg'=>__('情報が見つかりません。'));
					$this->response($res);
					return;
				}
				$aContact = $result->current();
			}

			$sDate = date('Y-m-d H:i:s');

			switch ($par['m'])
			{
				case 'input':
					try
					{
						$aInsert = array(
							'parent'    => $iNO,
							'coSubject' => $par['c_subject'],
							'coBody'    => $par['c_text'],
							'ctID'      => $aClass['ctID'],
							'stID'      => $this->aStudent['stID'],
							'coID'      => $this->aStudent['stID'],
							'coName'    => $this->aStudent['stName'],
							'coDate'    => $sDate,
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
			'coName'    => $this->aStudent['stName'],
			'coDate'    => ClFunc_Tz::tz('Y/m/d H:i',$this->tz,$sDate),
			'coSubject' => $par['c_subject'],
			'coBody'    => nl2br(\Clfunc_Common::url2link($par['c_text'],480)),
		);

		$aOptions = array(
			'cID'      => $this->aStudent['stID'],
			'cMail'    => $this->aStudent['stMail'],
			'cName'    => $this->aStudent['stName'],
			'cSubMail' => $this->aStudent['stSubMail'],
			'cSubject' => $par['c_subject'],
			'cBody'    => $par['c_text'],
		);
		\ClFunc_Mailsend::MailSendToContact($aClass['ctID'],$this->aStudent['stID'],'s',$aOptions);

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
			$result = Model_Contact::getContact(null,$this->aStudent['stID'],array(array('co.no','=',$iNO)));
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
			$result = Model_Contact::getContact(null,$this->aStudent['stID'],array(array('co.no','=',$iNO)));
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
				$aWhere = array(array('coID','!=',$this->aStudent['stID']),array('no','=',$iNO));
				$result = \Model_Contact::updateContact($aUpdate,$aWhere);
				$aWhere = array(array('coID','!=',$this->aStudent['stID']),array('parent','=',$iNO));
				$result = \Model_Contact::updateContact($aUpdate,$aWhere);
			}
			catch (Exception $e)
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('処理に失敗しました。時間をおいてから、再度実行してください。').$e->getMessage());
				$this->response($res);
				return;
			}
		}

		\Session::delete('CL_STU_UNREAD_'.$this->aStudent['stID']);
		$res = array('err'=>0,'res'=>'');
		$this->response($res);
		return;
	}



}

