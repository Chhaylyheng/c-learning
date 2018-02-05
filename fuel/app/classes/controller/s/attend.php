<?php
class Controller_S_Attend extends Controller_S_Base
{
	private function classchk()
	{
		if (is_null($this->aClass))
		{
			Session::set('SES_S_ERROR_MSG',__('講義情報が確認できませんでした。'));
			Response::redirect('/s/index'.$this->sesParam);
		}
		$aC = explode('_', get_class($this));
		$sC = strtolower($aC[2]);
		if ($this->bQuickTeacher && $sC != 'quest')
		{
			Response::redirect('/s/index'.$this->sesParam);
		}

		# 基本のパンくずリスト
		$this->aBread[] = array('link'=>'/class/index/'.$this->aClass['ctID'],'name'=>$this->aClass['ctName']);

		return;
	}


	public function action_index()
	{
		Response::redirect('index/404','location',404);
	}

	public function post_request()
	{
		self::classchk();

		$sRedirect = '/s/class/index/'.$this->aClass['ctID'].$this->sesParam;
		$aAttend = null;
		$result = Model_Attend::getAttendCalendarActive($this->aClass['ctID']);
		if (!count($result))
		{
			Session::set('SES_S_ERROR_MSG',__('出席は受け付けていません。'));
			Response::redirect($sRedirect);
		}
		$aAttend = $result->current();

		if (!Input::post(null,false))
		{
			Session::set('SES_S_ERROR_MSG',__('出席情報が送信されていません。'));
			Response::redirect($sRedirect);
		}
		$aInput = Input::post();

		if ($aInput['no'] != $aAttend['no'])
		{
			Session::set('SES_S_ERROR_MSG',__('対象の出席情報が見つかりませんでした。再度出席処理をしてください。'));
			Response::redirect($sRedirect);
		}

		if ($aAttend['acKey'] != '')
		{
			if ($aInput['keycode'] != $aAttend['acKey'])
			{
				Session::set('SES_S_ERROR_MSG',__('確認キーが異なるため、出席できません。'));
				Response::redirect($sRedirect);
			}
		}

		$aWhere = array(array('ctID','=',$this->aClass['ctID']),array('abDate','=',$aAttend['abDate']),array('acNO','=',$aAttend['acNO']));
		$bAlready = false;
		$result = Model_Attend::getAttendBookFromStudent($this->aStudent['stID'],$aWhere);
		if (count($result))
		{
			$aActive = $result->current();
			$bAlready = true;
		}

		# 出席マスター取得
		$result = Model_Attend::getAttendMasterFromClass($this->aClass['ctID'],null,array('amDefault'=>'DESC','amTime'=>'DESC','amAttendState'=>'ASC'));
		if (!count($result))
		{
			Session::set('SES_S_ERROR_MSG',__('出席マスタが登録されていません。'));
			Response::redirect($sRedirect);
		}
		$aRes = $result->as_array();

		# 経過時間でステータス変更
		$iDefault = null;
		$iState = null;
		foreach ($aRes as $aA)
		{
			if ($aA['amDefault'] == 1)
			{
				$iDefault = $aA['amAttendState'];
			}
			if ((int)$aA['amTime'] > 0)
			{
				$iChkTime = time() - strtotime($aAttend['acStart']);
				if ($iChkTime >= ((int)$aA['amTime']*60))
				{
					$iState = $aA['amAttendState'];
					break;
				}
			}
		}
		if (is_null($iState))
		{
			$iState = $iDefault;
		}

		$aGeo = null;
		if ($aInput['geoLat'] != "" && $aInput['geoLon'] != "")
		{
			$aGeo = array(
				'lat'=>$aInput['geoLat'],
				'lon'=>$aInput['geoLon'],
				'agLat'=>$aAttend['agLat'],
				'agLon'=>$aAttend['agLon'],
			);
			if ($bAlready && $aActive['agNO'] != 0)
			{
				$aGeo['agNO'] = $aActive['agNO'];
			}
		}

		try
		{
			if (!$bAlready)
			{
				# 出席情報生成
				$aInput = array(
					'ctID'          => $this->aClass['ctID'],
					'abDate'        => $aAttend['abDate'],
					'acNO'          => $aAttend['acNO'],
					'stID'          => $this->aStudent['stID'],
					'amAttendState' => $iState,
					'agNO'          => 0,
					'abAttendMemo'  => '',
					'abAttendDate'  => date('YmdHis'),
					'abStName'      => $this->aStudent['stName'],
					'abStNO'        => $this->aStudent['stNO'],
				);
				Model_Attend::insertAttendBook($aInput,$aGeo);
			}
			else
			{
				$aWhere[] = array('stID','=',$this->aStudent['stID']);
				$aInput = array(
					'amAttendState' => $iState,
					'agNO'          => 0,
					'abAttendDate'  => date('YmdHis'),
					'abStName'      => $this->aStudent['stName'],
					'abStNO'        => $this->aStudent['stNO'],
				);
				Model_Attend::updateAttendBook($aWhere,$aInput,$aGeo);
			}
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_S_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		Session::set('SES_S_NOTICE_MSG',__('出席しました。'));
		Response::redirect($sRedirect);
	}

	public function action_history($iALL = 0)
	{
		if (!$iALL)
		{
			self::classchk();
		}

		$sRedirect = '/s/index';
		# 履修講義取得
		if (is_null($this->aActClass))
		{
			Session::set('SES_S_ERROR_MSG',__('講義に履修していないため、出席履歴を確認することはできません。'));
			Response::redirect($sRedirect);
		}

		# 出席マスター取得
		$aAMaster = Model_Attend::getAttendMasterFromClasses($this->aActClass);
		if (is_null($aAMaster))
		{
			Session::set('SES_S_ERROR_MSG',__('出席履歴に必要な情報がありません。'));
			Response::redirect($sRedirect);
		}

		$aSelectClass = array('ALL'=>__('全ての講義'));
		$aClassList = null;
		foreach ($this->aActClass as $aC)
		{
			$aClassList[$aC['ctID']] = array(
				'ctID' => $aC['ctID'],
				'ctName' => $aC['ctName'],
				'ctCode' => \Clfunc_Common::getCode($aC['ctCode']),
				'ctSchedule' => (($aC['dpNO'])? $this->aPeriod[$aC['dpNO']].'/':'').(($aC['ctWeekDay'] > 0)? $this->aWeekday[$aC['ctWeekDay']].'/':'').(($aC['dhNO'])? $this->aHour[$aC['dhNO']]:''),
			);
			$aSelectClass[$aC['ctID']] = '['.\Clfunc_Common::getCode($aC['ctCode']).'] '.$aC['ctName'];
		}

		# 出席カレンダー取得
		$aYears = null;
		$aAttendList = null;
		$aAttendCal = null;
		foreach ($this->aActClass as $aC)
		{
			if (!$iALL && $aC['ctID'] != $this->aClass['ctID'])
			{
				continue;
			}
			$result = Model_Attend::getAttendCalendarFromClass($aC['ctID'],array(array('ac.acAStart','=',CL_DATETIME_DEFAULT),array('ac.abDate','<=',date('Y-m-d'))),null,'desc');
			if (count($result))
			{
				$aCal = $result->as_array();
				$aAttendList[$aC['ctID']] = $aCal;
				foreach ($aCal as $aC)
				{
					$iMonth = strtotime($aC['abDate']);
					$aYears[date('Y-m',$iMonth)] = date(__('Y年m月'),$iMonth);
					$aAttendCal[$aC['ctID']][$aC['abDate']][$aC['acNO']] = ($aC['acStart'])? ClFunc_Tz::tz('Y-m-d',$this->tz,$aC['acStart']):(($aC['acAStart'])? ClFunc_Tz::tz('Y-m-d',$this->tz,$aC['acAStart']):$aC['abDate']);
				}
			}
		}

		$aY[0] = date('Y-m');
		$aY[1] = date('Y-m');

		if (!is_null($aYears))
		{
			# 全ての年を取得
			$aKeys = array_keys($aYears);

			$i = 0;
			do
			{
				$iDay = strtotime('-'.$i.'years');
				if ((int)date('m',$iDay) > 3)
				{
					$sSY = date('Y',$iDay).'-04';
					$sEY = date('Y',strtotime('+1year',$iDay)).'-03';
				} else {
					$sSY = date('Y',strtotime('-1year',$iDay)).'-04';
					$sEY = date('Y',$iDay).'-03';
				}
				$i++;
			} while($aKeys[0] < $sSY);

			foreach ($aKeys as $sK)
			{
				if ($sK <= $sEY)
				{
					$aY[1] = $sK;
					break;
				}
			}
			krsort($aKeys);
			foreach ($aKeys as $sK)
			{
				if ($sK >= $sSY)
				{
					$aY[0] = $sK;
					break;
				}
			}
		}
		else
		{
			$aYears[date('Y-m')] = date(__('Y年m月'));
		}
		krsort($aYears);

		$sCtID = ($iALL)? 'ALL':$this->aClass['ctID'];
		if (Input::post(null,false))
		{
			$aReq = Input::post();
			if ($aReq['sy'] <= $aReq['ey'])
			{
				$aY[0] = $aReq['sy'];
				$aY[1] = $aReq['ey'];
			} else {
				$aY[0] = $aReq['ey'];
				$aY[1] = $aReq['sy'];
			}
			if ($iALL)
			{
				$sCtID = $aReq['ct'];
			}
		}
		$iSC = strtotime($aY[0].'-01');
		$iEC = strtotime($aY[1].'-01');
		$aWhere[] = array('abDate','between',array(date('Y-m-01',$iSC),date('Y-m-t',$iEC)));
		if ($sCtID != 'ALL')
		{
			$aWhere[] = array('ctID','=',$sCtID);
		}

		# 出席情報の取得
		$aBooks = null;
		$result = Model_Attend::getAttendBookFromStudent($this->aStudent['stID'],$aWhere);
		if (count($result))
		{
			$aRes = $result->as_array();
			foreach ($aRes as $aA)
			{
				$d = (isset($aAttendCal[$aA['ctID']][$aA['abDate']][$aA['acNO']]))? $aAttendCal[$aA['ctID']][$aA['abDate']][$aA['acNO']]:$aA['abDate'];
				$aBooks[$d][$aA['ctID']][] = $aA;
			}
			krsort($aBooks);
		}

		# タイトル
		$sTitle = __('出席履歴');
		$this->template->set_global('pagetitle',$sTitle);
		# パンくずリスト生成
		if ($iALL)
		{
			$this->template->set_global('aClass',null);
			$this->aBread = array();
		}
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		$this->template->content = View::forge($this->vDir.DS.'attend/history');
		$this->template->content->set('aY',$aY);
		$this->template->content->set('sCtID',$sCtID);
		$this->template->content->set('aSelectClass',$aSelectClass);
		$this->template->content->set('aAMaster',$aAMaster);
		$this->template->content->set('aClassList',$aClassList);
		$this->template->content->set('aYears',$aYears);
		$this->template->content->set('aAttendList',$aAttendList);
		$this->template->content->set('aBooks',$aBooks);
		$this->template->set_global('iALL',$iALL);
		return $this->template;
	}
}