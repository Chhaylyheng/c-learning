<?php
class Controller_S_Index extends Controller_S_Base
{
	public function action_index()
	{
		# タイトル
		$sTitle = $this->aStudent['stName'];
		if ($this->aStudent['stLastLoginDate'] != CL_DATETIME_DEFAULT)
		{
			$sLogined = __('前回ログイン：').date('Y/m/d H:i',strtotime($this->aStudent['stLastLoginDate']));
		}
		else
		{
			$sLogined = __('初ログイン');
		}

		if (!is_null($this->aGroup) && ($this->aGroup['gtStudentGetFlag'] > 0))
		{
			$aFlag  = \Clfunc_Common::dec2Bits((int)$this->aGroup['gtStudentGetFlag']);
			$aGet = \Clfunc_Flag::getStuGetFlag();

			foreach ($aFlag as $i)
			{
				if (!$this->aStudent[$aGet[$i]])
				{
					Response::redirect('s/login/getprofile'.$this->sesParam);
				}
			}
		}

		$aNews = null;
		$aCTeach = null;
		if (!is_null($this->aActClass))
		{
			foreach ($this->aActClass as $aC)
			{
				$sNow = date('Y-m-d H:i:s');
				$aWhere = array(array('cnStart','<=',$sNow),array('cnEnd','>=',$sNow));
				$result = Model_Class::getNews($aC['ctID'],$aWhere);
				if (count($result))
				{
					foreach ($result as $i => $aN)
					{
						$aNews[$aC['ctID']][$i] = $aN;
						$aNews[$aC['ctID']][$i]['cnChain'] = ($aN['cnURL'])? \Clfunc_Common::ExtUrlDetectForStudent($aN['cnURL'], $this->aStudent['stID']):null;
					}
				}

				if (CL_CAREERTASU_MODE)
				{
					$result = Model_Teacher::getTeacherFromClass(array(array('tp.ctID','=',$aC['ctID'],'tp.tpMaster','=',1)));
					if (count($result))
					{
						$aCTeach[$aC['ctID']] = $result->current();
					}
				}
			}
		}
		$this->template->set_global('aNewsList',$aNews);
		$this->template->set_global('aCTeach',$aCTeach);

		$this->template->content = View::forge($this->vDir.DS.'index');
		$this->template->content->set('sTitle',$sTitle);
		$this->template->content->set('sLogined',$sLogined);
		$this->template->content->set('aActClass',$this->aActClass);

		return $this->template;
	}

	public function action_logout()
	{
		Cookie::delete('CL_SL_HASH');
		Session::delete('CL_SL_HASH');
		Response::redirect('s/login');
	}

}