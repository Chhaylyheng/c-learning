<?php
class Controller_T_Base extends Controller_Base
{
	public $template = 't/template_logined';
	public $aGroup = null;
	public $aTeacher = null;
	public $aAssistant = null;
	public $sCurrentID = null;
	public $aActClass = null;
	public $aClass = null;
	public $iClassNum = 0;
	public $aBread = array();
	public $eRedirect = 'index/error/t';
	public $sAwsSavePath = null;
	public $sTempFilePath = null;
	public $sDir = 't';
	public $bT = true;
	public $aUnread = null;
	public $iUnread = 0;

	public function before()
	{
		parent::before();

		$this->sSiteTitle .= '（'.__('先生').'）';
		$this->template->set_global('dir', 't');
		$this->template->set_global('title', $this->sSiteTitle);
		$this->template->footer = View::forge('t/footer');

		if (Session::get('CL_AL_LOGIN', false))
		{
			$this->bT = false;
			$this->sDir = 'a';
			$sHash = Cookie::get('CL_AL_HASH',false);
			if (!$sHash)
			{
				Response::redirect($this->sDir.'/login/index/4');
			}
			$aLogin = unserialize(Crypt::decode($sHash));

			$result = Model_Assistant::getAssistantFromHash($aLogin['hash']);
			if (!count($result))
			{
				Response::redirect($this->sDir.'/login/index/5');
			}
			$this->aAssistant = $result->current();

			$result = Model_Assistant::getAssistantPosition(array(array('ap.atID','=',$this->aAssistant['atID']),array('ct.ctStatus','=',1)),null,array('ap.apSort'=>'desc'));
			if (count($result))
			{
				$this->aActClass = $result->as_array();
			}

			$result = Model_Assistant::getAssistantPosition(array(array('ap.atID','=',$this->aAssistant['atID'])));
			$this->iClassNum = count($result);

			$this->sAwsSavePath = 'assistant'.DS.$this->aAssistant['atID'];
			$this->sTempFilePath = CL_UPPATH.DS.'temp';

			$result = Model_Teacher::getTeacherFromID($this->aAssistant['ttID']);
			if (!count($result))
			{
				Response::redirect($this->sDir.'/login/index/6');
			}
			$this->aTeacher = $result->current();
			$this->aTeacher['ttTimeZone'] = $this->aAssistant['atTimeZone'];
			$this->sCurrentID = $this->aAssistant['atID'];
		}
		else
		{
			$sHash = Cookie::get('CL_TL_HASH',false);
			if (!$sHash)
			{
				Response::redirect($this->sDir.'/login/index/1');
			}
			$aLogin = unserialize(Crypt::decode($sHash));

			$result = Model_Teacher::getTeacherFromHash($aLogin['hash']);
			if (!count($result))
			{
				Response::redirect($this->sDir.'/login/index/2');
			}
			$this->aTeacher = $result->current();

			$result = Model_Class::getClassFromTeacher($this->aTeacher['ttID']);
			$this->iClassNum = count($result);

			Cookie::set("CL_TL_HASH", $sHash);
			$this->sAwsSavePath = 'teacher'.DS.$this->aTeacher['ttID'];
			$this->sTempFilePath = CL_UPPATH.DS.'temp';
			$this->sCurrentID = $this->aTeacher['ttID'];
		}

		if (strtotime($this->aTeacher['ttSumTime']) <= strtotime('-30min'))
		{
			$result = Model_Teacher::setTeacherUsed($this->aTeacher['ttID']);
			$result = Model_Teacher::getTeacherFromID($this->aTeacher['ttID']);
			if (!count($result))
			{
				Response::redirect($this->sDir.'/login/index/3');
			}
			$this->aTeacher = $result->current();
		}

		if ($this->aTeacher['ttStatus'] == 3 && $this->bT)
		{
			Cookie::set('CL_INIT_TID', $this->aTeacher['ttID']);
			Response::redirect('t/init/profile');
		}

		if ($this->aTeacher['gtID'])
		{
			$result = Model_Group::getGroup(array(array('gb.gtID','=',$this->aTeacher['gtID'])));
			if (count($result))
			{
				$this->aGroup = $result->current();
			}
			if ($this->bT)
			{
				$result = Model_Group::getGroupTeachersClasses(
					array(
						array('tp.ttID','=',$this->aTeacher['ttID']),
						array('ct.ctStatus','=',1)
					),null,array('tp.tpSort'=>'desc'));
				if (count($result))
				{
					$this->aActClass = $result->as_array();
				}
			}
		}
		else
		{
			if ($this->bT)
			{
				$result = Model_Class::getClassFromTeacher($this->aTeacher['ttID'],1,null,null,array('tp.tpSort'=>'desc'));
				if (count($result))
				{
					$this->aActClass = $result->as_array();
				}
			}
		}

		if (!is_null($this->aActClass))
		{
			if ($aUnreadTemp = \Session::get('CL_TEACH_UNREAD_'.$this->sCurrentID, false))
			{
				if ($aUnreadTemp['DATE'] >= time() && $aUnreadTemp['UNREAD'])
				{
					$this->aUnread = $aUnreadTemp['UNREAD'];
				}
			}
			if (is_null($this->aUnread))
			{
				$oUC = new ClFunc_UnreadCount();
				if (!is_null($this->aAssistant))
				{
					$oUC->setAssistant($this->aAssistant);
				}
				else
				{
					$oUC->setTeacher($this->aTeacher);
				}
				foreach ($this->aActClass as $aC)
				{
					$oUC->setClass($aC);
					$this->aUnread[$aC['ctID']] = $oUC->getClassCount();

				}
				\Session::set('CL_TEACH_UNREAD_'.$this->sCurrentID, array('DATE' => strtotime('+1 minutes'), 'UNREAD' => $this->aUnread));
			}

			$iCNum = count($this->aActClass);
			if ($iCNum > 0 && !$this->aTeacher['gtID'])
			{
				if ($this->aTeacher['ptID'] == 0 || $iCNum > $this->aTeacher['coClassNum'])
				{
					$iC = ($this->aTeacher['ptID'] == 0)? 1:$this->aTeacher['coClassNum'];
					$aClasses = array_slice($this->aActClass, $iC);

					foreach ($aClasses as $aC)
					{
						$aInsert = array('ctStatus' => 0);
						try
						{
							$result = Model_Class::updateClass($aInsert,$aC);
							$result = Model_Class::updateClassTeacher(array('tpSort'=>0), $aC);

							$result = Model_Teacher::getTeacherFromClass(array(array('tp.ctID','=',$aC['ctID'])));
							if (count($result))
							{
								$aRes = $result->as_array('ttID');
								$aTIDs = array_keys($aRes);
								Model_Class::resetTeacherClassSort($aTIDs);
							}
							$result = Model_Teacher::setTeacherClassNum(array($this->aTeacher['ttID']));
						}
						catch (Exception $e)
						{
							\Clfunc_Common::LogOut($e,__CLASS__);
							Session::set('SES_T_ERROR_MSG',$e->getMessage());
							Response::redirect($this->eRedirect);
						}
					}
					$this->aActClass = array_slice($this->aActClass, 0, $iC);
				}
			}
		}

		if (is_array($this->aUnread))
		{
			$this->iUnread = array_sum($this->aUnread);
		}

		$this->aSes = Session::get(null,false);
		$this->template->set_global('ses',$this->aSes);
		$this->template->set_global('aGroup',$this->aGroup);
		$this->template->set_global('aTeacher',$this->aTeacher);
		$this->template->set_global('aAssistant',$this->aAssistant);
		$this->template->set_global('bT',$this->bT);
		$this->template->set_global('aClassList',$this->aActClass);
		$this->template->set_global('aCLNewsList',null);
		$this->template->set_global('aUnread',$this->aUnread);
		$this->template->set_global('iUnread',$this->iUnread);
		$this->template->set_global('iClassNum',$this->iClassNum);
		$this->template->set_global('sTempPath',$this->sTempFilePath);
		$this->template->set_global('sCurrentID',$this->sCurrentID);

		$this->tz = $this->aTeacher['ttTimeZone'];
		$this->template->set_global('tz',$this->tz);
	}
}