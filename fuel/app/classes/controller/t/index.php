<?php
class Controller_T_Index extends Controller_T_Base
{
	public function action_index()
	{
		$aReport = null;
		$result = Model_KReport::getKReportTarget(array(array('ttID','=',$this->aTeacher['ttID']),array('krPublic','!=',0)),null,array('krYear'=>'desc','krPeriod'=>'desc'));
		if (count($result)) {
			$aReport = $result->as_array();
		}

		if (!is_null($aReport))
		{
			foreach ($aReport as $i => $aR)
			{
				$aReport[$i]['new'] = false;
				$result = Model_KReport::getKReportPut(array(array('krYear','=',$aR['krYear']),array('krPeriod','=',$aR['krPeriod']),array('krDate','!=',null)));
				if (count($result))
				{
					$aTarget = $result->as_array('ttID');
					$result2 = Model_KReport::getKReportAlready(array(array('krYear','=',$aR['krYear']),array('krPeriod','=',$aR['krPeriod']),array('kaID','=',$this->aTeacher['ttID']),array('kaAlready','=',1)));
					if (count($result2))
					{
						$aAlready = $result2->as_array('ttID');
						foreach ($aTarget as $sID => $aT)
						{
							if (!isset($aAlready[$sID]))
							{
								$aReport[$i]['new'] = true;
								break;
							}
						}
					}
					else
					{
						$aReport[$i]['new'] = true;
					}
				}
			}
		}

#		echo Crypt::encode(serialize(array('hash'=>'b7e38085e1c59845af4ce8fc5ebbcd6565bd5a25','ip'=>Input::real_ip())));


		# タイトル
		if ($this->aAssistant)
		{
			$sTitle = (($this->aAssistant['atName'])? $this->aAssistant['atName']:$this->aAssistant['atMail']);
			if ($this->aAssistant['atLastLoginDate'] != CL_DATETIME_DEFAULT)
			{
				$sLogined = __('前回ログイン：').\ClFunc_Tz::tz('Y/m/d H:i',$this->tz,$this->aAssistant['atLastLoginDate']);
			}
			else
			{
				$sLogined = __('初ログイン');
			}
		}
		else
		{
			$sTitle = (($this->aTeacher['ttName'])? $this->aTeacher['ttName']:$this->aTeacher['ttMail']);
			if ($this->aTeacher['ttLastLoginDate'] != CL_DATETIME_DEFAULT)
			{
				$sLogined = __('前回ログイン：').\ClFunc_Tz::tz('Y/m/d H:i',$this->tz,$this->aTeacher['ttLastLoginDate']);
			}
			else
			{
				$sLogined = __('初ログイン');
			}
		}

		// 講義作成コード
		$aCreateKey = null;
		$sCreateKey = Session::get('SES_T_CLASS_CREATE_KEY',false);
		if ($sCreateKey)
		{
			$aCreateKey = unserialize($sCreateKey);
			Session::delete('SES_T_CLASS_CREATE_KEY');
		}

		$aAssists = null;
		$aAPos = null;

		if (is_null($this->aGroup) || !$this->aGroup['gtLDAP'] || $this->aGroup['gtLAssistant'])
		{
			$result = Model_Assistant::getAssistant(array(array('ttID','=',$this->aTeacher['ttID'])));
			if (count($result))
			{
				$aAssists = $result->as_array('atID');
				$pres = Model_Assistant::getAssistantPosition(array(array('ap.atID','IN',array_keys($aAssists))));
				if (count($pres))
				{
					$aAPos = $pres->as_array('ctID');
				}
			}
		}

		$aMasters = null;
		if (!is_null($this->aGroup))
		{
			$result = Model_Group::getGroupTeachersClasses(
				array(
					array('gc.gtID','=',$this->aGroup['gtID']),
					array('ct.ctStatus','=',1),
					array('tp.tpMaster','=',1),
				));
			if (count($result)) {
				$aMasters = $result->as_array('ctID');
			}

		}
		$this->template->set_global('aMasters',$aMasters);

		$this->template->content = View::forge('t/index');

		$this->template->content->set('aAssists',$aAssists);
		$this->template->content->set('aAPos',$aAPos);

		$this->template->content->set('sTitle',$sTitle);
		$this->template->content->set('sLogined',$sLogined);
		$this->template->content->set('aActClass',$this->aActClass);
		$this->template->content->set('aReport',$aReport);
		$this->template->content->set('aCreateKey',$aCreateKey);

		$this->template->javascript = array('cl.t.index.js');
		return $this->template;
	}

	public function action_close()
	{
		$aClsClass = null;

		if ($this->aAssistant)
		{
			$result = Model_Assistant::getAssistantPosition(
				array(
					array('ap.atID','=',$this->aAssistant['atID']),
					array('ct.ctStatus','=',0)
				),null,array('ct.ctYear'=>'asc','ct.dpNO'=>'asc','ct.ctWeekDay'=>'asc','ct.dhNO'=>'asc'));
			if (count($result))
			{
				$aClsClass = $result->as_array();
			}
		}
		else
		{
			if ($this->aTeacher['gtID'])
			{
				$result = Model_Group::getGroupTeachersClasses(
					array(
						array('tp.ttID','=',$this->aTeacher['ttID']),
						array('ct.ctStatus','=',0)
					),null,array('ct.ctYear'=>'asc','ct.dpNO'=>'asc','ct.ctWeekDay'=>'asc','ct.dhNO'=>'asc'));
				if (count($result)) {
					$aClsClass = $result->as_array();
				}

				$aMasters = null;
				$result = Model_Group::getGroupTeachersClasses(
					array(
						array('gc.gtID','=',$this->aGroup['gtID']),
						array('ct.ctStatus','=',0),
						array('tp.tpMaster','=',1),
					));
				if (count($result)) {
					$aMasters = $result->as_array('ctID');
				}
				$this->template->set_global('aMasters',$aMasters);
			}
			else
			{
				$result = Model_Class::getClassFromTeacher($this->aTeacher['ttID'],0);
				if (count($result)) {
					$aClsClass = $result->as_array();
				}
			}
		}

		$sTitle = __('終了した講義');
		# パンくずリスト生成
		$this->template->set_global('breadcrumbs',array(array('name'=>$sTitle)));
		# ページタイトル生成
		$this->template->set_global('pagetitle',$sTitle);
		$this->template->set_global('aClass',null);

		$this->template->content = View::forge('t/close');
		$this->template->content->set('aClsClass',$aClsClass);
		return $this->template;
	}


	public function action_logout()
	{
		Cookie::delete('CL_TL_HASH');
		Response::redirect('t/login');
	}

	public function action_TeacherSwitcher($sTtID = null)
	{
		if (Input::real_ip() != '27.96.53.101' || is_null($sTtID))
		{
			Response::redirect('index/404','location',404);
		}
		$result = Model_Teacher::getTeacher(array(array('ttID','=',$sTtID)));
		if (!count($result))
		{
			Response::redirect('index/404','location',404);
		}
		$aAct = $result->current();

		Cookie::set("CL_TL_HASH",Crypt::encode(serialize(array('hash'=>sha1($aAct['ttMail'].$aAct['ttPass']),'ip'=>Input::real_ip()))));
		Response::redirect('t/index');
	}

	public function action_TeacherDelete()
	{
		if (Input::real_ip() != '27.96.53.101')
		{
			Response::redirect('index/404','location',404);
		}
		try
		{
			$result = Model_Teacher::deleteTeacher($this->aTeacher);
			Cookie::delete('CL_TL_HASH');
			Session::set('SES_T_ERROR_MSG','先生を削除しました。');
			Response::redirect('t/login');
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect('t/index');
		}
	}

	public function action_tutorial()
	{
		$sCtID = Cookie::get('CL_T_CLASS_ID',false);
		if ($sCtID)
		{
			$result = Model_Class::getClassFromTeacher($this->aTeacher['ttID'],null,array(array('tp.ctID','=',$sCtID)));
			if (count($result)) {
				$this->aClass = $result->current();
			}
		}
		if (is_null($this->aClass))
		{
			Session::set('SES_T_ERROR_MSG',__('講義情報が確認できませんでした。'));
			Response::redirect('/t/index');
		}
		$this->template->set_global('aClass',$this->aClass);

		try
		{
			$aUpdate = array(
				'ttStatus' => 2,
			);
			$result = Model_Teacher::updateTeacher($this->aTeacher['ttID'], $aUpdate);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}
		Response::redirect('/t/class/index/'.$sCtID);
	}

	public function action_manual()
	{
		$sTitle = __('先生機能マニュアル');
		# パンくずリスト生成
		$this->template->set_global('breadcrumbs',array(array('name'=>$sTitle)));
		# ページタイトル生成
		$this->template->set_global('pagetitle',$sTitle);
		$this->template->set_global('aClass',null);

		$this->template->content = View::forge('t/manual');
		return $this->template;
	}

}