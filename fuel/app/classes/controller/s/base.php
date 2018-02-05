<?php
class Controller_S_Base extends Controller_Base
{
	public $template = 's/template_logined';
	public $aStudent = null;
	public $aActClass = null;
	public $aClass = null;
	public $aGroup = null;
	public $bQuickTeacher = false;
	public $iDevice = CL_DEV_PC;
	public $aSes = null;
	public $vDir = 's';
	public $gSes = null;
	public $pSes = null;
	public $eRedirect = 'index/error/s';
	public $sesParam = '';
	public $sAwsSavePath = null;
	public $sTempFilePath = null;
	public $aUnread = null;
	public $iUnread = 0;

	public function before()
	{
		parent::before();

		if (Agent::is_smartphone())
		{
			// スマートフォン
			$this->iDevice = CL_DEV_SP;
		}
		elseif (Clfunc_Mobile::is_mobiledevice())
		{
			Config::set('base_url', 'http://'.CL_DOMAIN.'/');

			$this->iDevice = CL_DEV_MB;
			$this->vDir = 'sm';
			$this->template = View::forge($this->vDir.DS.'template.php');
			if (Cookie::get('CL_COOKIE_CHK',false) === false)
			{
				ini_set('session.use_cookies', 0);
				ini_set('session.use_only_cookies', 0);
				ini_set('session.use_trans_sid', 1);

				$sessionid = Crypt::encode(serialize(array(Session::key())));
				$this->sesParam = '?'.Config::get('session.file.cookie_name').'='.$sessionid;
			}
		}

		$this->sSiteTitle .= '（'.__('学生').'）';
		$this->template->set_global('dir', 's');
		$this->template->set_global('title', $this->sSiteTitle);
		$this->template->footer = View::forge($this->vDir.DS.'footer');

		if (Cookie::get('CL_COOKIE_CHK',false))
		{
			Cookie::set('CL_COOKIE_CHK','cookie_enable');
			$sHash = Cookie::get('CL_SL_HASH',false);
		}
		else
		{
			$sHash = Session::get('CL_SL_HASH',false);
		}
		if (!$sHash)
		{
			Response::redirect('s/login/index/hng');
		}
		$aLogin = unserialize(Crypt::decode($sHash));

		$result = Model_Student::getStudentFromHash($aLogin['hash']);
		if (!count($result))
		{
			Response::redirect('s/login/index/hns');
		}
		$this->aStudent = $result->current();

		if ($aListTemp = \Session::get('CL_STU_CLASS_LIST_'.$this->aStudent['stID'], false))
		{
			if ($aListTemp['DATE'] >= time())
			{
				$this->aActClass = $aListTemp['LIST'];
			}
		}

		if (is_null($this->aActClass))
		{
			// $result = Model_Class::getClassFromStudent($this->aStudent['stID'],1);
			$result = Model_Class::getClassFromStudent($this->aStudent['stID'],1,null,null,null,array('ct.ctWeekDay'=>'asc','ct.dhNO'=>'asc','ct.ctCode'=>'asc'));
			if (count($result)) {
				$this->aActClass = $result->as_array();
				\Session::set('CL_STU_CLASS_LIST_'.$this->aStudent['stID'], array('DATE' => strtotime('+1 minutes'), 'LIST' => $this->aActClass));
			}
		}

		$aCTeach = null;
		if (!is_null($this->aActClass))
		{
			if (CL_CAREERTASU_MODE)
			{
				foreach ($this->aActClass as $aC)
				{
					$result = Model_Teacher::getTeacherFromClass(array(array('tp.ctID','=',$aC['ctID'],'tp.tpMaster','=',1)));
					if (count($result))
					{
						$aCTeach[$aC['ctID']] = $result->current();
					}
				}
			}
		}
		$this->template->set_global('aCTeach',$aCTeach);

		if (Cookie::get('CL_COOKIE_CHK',false))
		{
			$sCtID = Cookie::get('SES_S_CLASS_ID',false);
		}
		else
		{
			$sCtID = Session::get('SES_S_CLASS_ID',false);
		}

		$sDirectID = \Input::get('ct',false);
		if ($sDirectID)
		{
			Cookie::set('SES_S_CLASS_ID',$sDirectID);
			Session::set('SES_S_CLASS_ID',$sDirectID);
			$sCtID = $sDirectID;
		}

		if ($sCtID)
		{
			if ($aClassTemp = \Session::get('CL_STU_ACTIVE_CLASS_'.$this->aStudent['stID'], false))
			{
				if ($aClassTemp['DATE'] >= time() && $aClassTemp['CLASS']['ctID'] == $sCtID)
				{
					$this->aClass = $aClassTemp['CLASS'];
				}
			}
			if (is_null($this->aClass))
			{
				$result = Model_Class::getClassFromStudent($this->aStudent["stID"],1,$sCtID,null,null,false);
				if (count($result)) {
					$this->aClass = $result->current();
					\Session::set('CL_STU_ACTIVE_CLASS_'.$this->aStudent['stID'], array('DATE' => strtotime('+30 minutes'), 'CLASS' => $this->aClass));
				}
			}
			$result = \Model_Teacher::getTeacherPosition(array(array('ctID','=',$this->aClass['ctID']),array('tpMaster','=',1)));
			if (count($result))
			{
				$aTP = $result->current();
				$result = \Model_Contract::getContract(array(array('ttID','=',$aTP['ttID']),array('ptID','!=',99),array('coStartDate','<=',\DB::expr('NOW()')),array('coTermDate','>=',\DB::expr('NOW()'))));
				if (!count($result))
				{
					$this->bQuickTeacher = true;
				}
			}

		}

		$result = Model_Group::getGroupStudents(array(array('gsp.stID','=',$this->aStudent['stID'])));
		if (count($result))
		{
			$this->bQuickTeacher = false;
			$aGS = $result->current();

			if ($aGroupTemp = \Session::get('CL_STU_GROUP_'.$this->aStudent['stID'], false))
			{
				if ($aGroupTemp['DATE'] >= time() && $aGroupTemp['GROUP']['gtID'] == $aGS['gtID'])
				{
					$this->aGroup = $aGroupTemp['GROUP'];
				}
			}

			if (is_null($this->aGroup))
			{
				$result = Model_Group::getGroup(array(array('gb.gtID','=',$aGS['gtID'])));
				if (count($result))
				{
					$this->aGroup = $result->current();
					\Session::set('CL_STU_GROUP_'.$this->aStudent['stID'], array('DATE' => strtotime('+5 minutes'), 'GROUP' => $this->aGroup));
				}
			}
		}

		if (!is_null($this->aActClass))
		{
			if ($aUnreadTemp = \Session::get('CL_STU_UNREAD_'.$this->aStudent['stID'], false))
			{
				if ($aUnreadTemp['DATE'] >= time() && $aUnreadTemp['UNREAD'])
				{
					$this->aUnread = $aUnreadTemp['UNREAD'];
				}
			}
			if (is_null($this->aUnread))
			{
				$oUC = new ClFunc_UnreadCount();
				$oUC->setStudent($this->aStudent);
				foreach ($this->aActClass as $aC)
				{
					$oUC->setClass($aC);
					$this->aUnread[$aC['ctID']] = $oUC->getClassCount();

				}
				\Session::set('CL_STU_UNREAD_'.$this->aStudent['stID'], array('DATE' => strtotime('+1 minutes'), 'UNREAD' => $this->aUnread));
			}
		}
		if (is_array($this->aUnread))
		{
			$this->iUnread = array_sum($this->aUnread);
		}

		$this->sAwsSavePath = 'student'.DS.$this->aStudent['stID'];
		$this->sTempFilePath = CL_UPPATH.DS.'temp';

		$this->aSes = Session::get(null,false);
		$this->template->set_global('ses',$this->aSes);
		$this->template->set_global('aStudent',$this->aStudent);
		$this->template->set_global('aClassList',$this->aActClass);
		$this->template->set_global('aUnread',$this->aUnread);
		$this->template->set_global('iUnread',$this->iUnread);
		$this->template->set_global('aCLNewsList',null);
		$this->template->set_global('aClass',$this->aClass);
		$this->template->set_global('bQuickTeacher',$this->bQuickTeacher);
		$this->template->set_global('aGroup',$this->aGroup);
		$this->template->set_global('iDevice',$this->iDevice);

		$this->template->set_global('tz',$this->aStudent['stTimeZone']);
	}
}