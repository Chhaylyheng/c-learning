<?php
class Controller_T_Ajax_Material extends Controller_T_Ajax
{
	public function post_CateSort()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>__('情報が正しく送信されていません。'));
		$par = Input::post();
		if ($par)
		{
			$result = Model_Material::getMaterialCategoryFromID($par['mc']);
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('指定されたカテゴリが見つかりません。'));
				$this->response($res);
				return;
			}
			$aMCategory = $result->current();
			$result = Model_Material::getMaterialCategoryFromClass($par['ct'],null,null,array('mcSort'=>'desc'));
			if (!count($result))
			{
				$res = array('err'=>-1,'res'=>'','msg'=>__('並び順を変更するカテゴリの情報が見つかりません。'));
				$this->response($res);
				return;
			}
			$iMax = count($result);
			if (($aMCategory['mcSort'] == $iMax && $par['m'] == 'up') || ($aMCategory['mcSort'] == 1 && $par['m'] == 'down'))
			{
				$res = array('err'=>-1,'res'=>'','msg'=>__('一番上か一番下のカテゴリのため、並び順を変更することはできません。'));
				$this->response($res);
				return;
			}
			$result = Model_Material::sortMaterialCategory($aMCategory,$par['m']);
			$res = array('err'=>0,'res'=>array('m'=>$par['m']));
		}
		$this->response($res);
		return;
	}

	public function post_Sort()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>__('情報が正しく送信されていません。'));
		$par = Input::post();
		if ($par)
		{
			$result = \Model_Material::getMaterial(array(array('mt.mcID','=',$par['mc']),array('mt.mNO','=',$par['mn'])));
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('指定された教材が見つかりません。'));
				$this->response($res);
				return;
			}
			$aMaterial = $result->current();
			$result = Model_Material::getMaterial(array(array('mt.mcID','=',$par['mc'])),null,array('mt.mSort'=>'desc'));
			if (!count($result))
			{
				$res = array('err'=>-1,'res'=>'','msg'=>__('並び順を変更する教材が見つかりません。'));
				$this->response($res);
				return;
			}
			$iMax = count($result);
			if (($aMaterial['mSort'] == $iMax && $par['m'] == 'up') || ($aMaterial['mSort'] == 1 && $par['m'] == 'down'))
			{
				$res = array('err'=>-1,'res'=>'','msg'=>__('一番上か一番下の教材のため、並び順を変更することはできません。'));
				$this->response($res);
				return;
			}
			$result = Model_Material::sortMaterial($aMaterial,$par['m']);
			$res = array('err'=>0,'res'=>array('m'=>$par['m']));
		}
		$this->response($res);
		return;
	}

	public function post_Public()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>__('情報が正しく送信されていません。'));
		$par = Input::post();
		if ($par)
		{
			$result = Model_Material::getMaterialCategoryFromID($par['mc']);
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('指定されたカテゴリが見つかりません。'));
				$this->response($res);
				return;
			}
			$aMCategory = $result->current();

			$result = Model_Material::getMaterial(array(array('mt.mcID','=',$par['mc']),array('mt.mNO','=',$par['mn'])));
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('指定された教材が見つかりません。'));
				$this->response($res);
				return;
			}
			$aMaterial = $result->current();

			if ($par['m'] == 'public')
			{
				$iPub = 1;
				$sText = __('公開中');
				$sClass = 'font-blue';
			}
			elseif ($par['m'] == 'private')
			{
				$iPub = 0;
				$sText = __('非公開');
				$sClass = 'font-default';
			}
			else
			{
				$res = array('err'=>-1,'res'=>'','msg'=>__('公開情報が正しく送信されていません。'));
				$this->response($res);
				return;
			}

			$result = Model_Material::publicMaterial($aMaterial,$iPub);

			if ($aMCategory['mcMail'] && $iPub)
			{
				try
				{
					$aOptions = array(
						'mcName' => $aMCategory['mcName'],
						'mTitle' => $aMaterial['mTitle'],
					);
					\ClFunc_Mailsend::MailSendToClassStudents($this->aTeacher['ttID'],$aMCategory['ctID'],'MatPublic',$aOptions);
				}
				catch (Exception $e)
				{
					\Clfunc_Common::LogOut($e,__CLASS__);
				}
			}

			$res = array('err'=>0,'res'=>array('class'=>$sClass,'text'=>$sText),'msg'=>__('教材公開ステータスを変更しました。'));
		}
		$this->response($res);
		return;
	}

	public function post_ListData()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>__('情報が正しく送信されていません。'));
		$par = Input::post();
		if ($par)
		{
			$result = Model_Class::getClassFromTeacher($this->aTeacher['ttID'],1,array(array('tp.ctID','=',$par['c'])));
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('講義情報が見つかりません。'));
				$this->response($res);
				return;
			}
			$aClass = $result->current();

			$aList = null;
			$sP = null;
			switch ($par['m'])
			{
				case 'quest':
					$result = Model_Quest::getQuestBaseFromClass($aClass['ctID'],null,null,array('qb.qbSort'=>'desc'));
					$sP = 'qb';
				break;
				case 'test':
					$result = Model_Test::getTestBaseFromClass($aClass['ctID'],null,null,array('tb.tbSort'=>'desc'));
					$sP = 'tb';
				break;
				case 'material':
					$result = Model_Material::getMaterialCategoryFromClass($aClass['ctID'],array(array('mcNum','>',0)),null,array('mcSort'=>'desc'));
					if (!count($result))
					{
						$res = array('err'=>-2,'res'=>'','msg'=>__('取得する情報が見つかりません。'));
						$this->response($res);
						return;
					}
					foreach ($result as $r)
					{
						$aList[] = array(
							'type' => __('教材倉庫'),
							'title' => $r['mcName'],
							'cat' => 1,
							'tree' => $r['mcID'],
							'url' => urlencode('//'.CL_DOMAIN.'/s/'.$par['m'].'/list/'.$r['mcID']),
						);
						$result2 = Model_Material::getMaterial(array(array('mt.mcID','=',$r['mcID'])),null,array('mt.mSort'=>'desc'));
						if (count($result2))
						{
							foreach ($result2 as $s)
							{
								$aList[] = array(
									'type' => __('教材倉庫'),
									'url' => urlencode('//'.CL_DOMAIN.'/s/'.$par['m'].'/list/'.$s['mcID'].'/#m'.$s['mNO']),
									'title' => $s['mTitle'],
									'stateColor' => (!$s['mPublic'])? 'font-default':(($s['mPublic'] == 1)? 'font-blue':'font-red'),
									'status' => (!$s['mPublic'])? __('非公開'):(($s['mPublic'] == 1)? __('公開中'):__('締切')),
									'date' => ClFunc_Tz::tz('Y/m/d H:i',$this->tz,$s['mDate']),
									'cat' => 0,
									'tree' => $s['mcID'],
								);
							}
						}
					}
				break;
				case 'coop':
					$result = Model_Coop::getCoopCategoryFromClass($aClass['ctID'],array(array('ccItemNum','>',0)),null,array('ccSort'=>'desc'));
					if (!count($result))
					{
						$res = array('err'=>-2,'res'=>'','msg'=>__('取得する情報が見つかりません。'));
						$this->response($res);
						return;
					}
					foreach ($result as $r)
					{
						$aList[] = array(
							'type' => __('協働板'),
							'title' => $r['ccName'],
							'cat' => 1,
							'tree' => $r['ccID'],
							'url' => urlencode('//'.CL_DOMAIN.'/s/'.$par['m'].'/thread/'.$r['ccID']),
						);
						$result2 = Model_Coop::getCoop(array(array('ci.ccID','=',$r['ccID']),array('ci.cRoot','=',0)),null,array('ci.cSort'=>'desc'));
						if (count($result2))
						{
							foreach ($result2 as $s)
							{
								$bTeach = preg_match('/^t/', $s['cID']);
								$cName = ($bTeach)? (($s['ttName'])? $s['ttName']:$s['cName']):(($s['stName'])? $s['stName']:$s['cName']);
								$cColor = ($bTeach)? 'font-red':'font-green';

								if ($s['cID'] == $this->aTeacher['ttID'])
								{
									$sStatus = 'font-red';
									$sWriter = $this->aTeacher['ttName'];
								}
								else
								{
									switch ($r['ccAnonymous'])
									{
										case 0:
											$sStatus = 'font-default';
											$sWriter = __('匿名');
										break;
										case 1:
											if ($bTeach)
											{
												$sStatus = $cColor;
												$sWriter = $cName;
											}
											else
											{
												$sStatus = 'font-default';
												$sWriter = __('匿名');
											}
										break;
										case 2:
											$sStatus = $cColor;
											$sWriter = $cName;
										break;
									}
								}

								$aList[] = array(
									'type' => __('協働板'),
									'url' => urlencode('//'.CL_DOMAIN.'/s/'.$par['m'].'/thread/'.$s['ccID'].'/'.$s['cNO']),
									'title' => $s['cTitle'],
									'stateColor' => $sStatus,
									'status' => $sWriter,
									'date' => ClFunc_Tz::tz('Y/m/d H:i',$this->tz,$s['cDate']),
									'cat' => 0,
									'tree' => $s['ccID'],
								);
							}
						}
					}
				break;
				case 'report':
					$result = Model_Report::getReportBase(array(array('rb.ctID','=',$aClass['ctID'])),null,array('rb.rbSort'=>'desc'));
					if (!count($result))
					{
						$res = array('err'=>-2,'res'=>'','msg'=>__('取得する情報が見つかりません。'));
						$this->response($res);
						return;
					}
					foreach ($result as $r)
					{
						$aList[] = array(
							'type' => __('レポート'),
							'url' => urlencode('//'.CL_DOMAIN.'/s/report/put/'.$r['rbID']),
							'title' => $r['rbTitle'],
							'stateColor' => (!$r['rbPublic'])? 'font-default':(($r['rbPublic'] == 1)? 'font-blue':'font-red'),
							'status' => (!$r['rbPublic'])? __('非公開'):(($r['rbPublic'] == 1)? __('公開中'):__('締切')),
							'date' => ClFunc_Tz::tz('Y/m/d H:i',$this->tz,$r['rbDate']),
							'cat' => 0,
							'tree' => $r['rbID'],
						);
					}
				break;
				default:
					$this->response($res);
				break;
			}

			if ($par['m'] == 'quest' || $par['m'] == 'test')
			{
				if (!count($result))
				{
					$res = array('err'=>-2,'res'=>'','msg'=>__('取得する情報が見つかりません。'));
					$this->response($res);
					return;
				}
				foreach ($result as $aQ)
				{
					$aQ['qbQuickMode'] = (isset($aQ['qbQuickMode']))? $aQ['qbQuickMode']:0;

					$aList[] = array(
						'type' => ($par['m'] == 'quest')? __('アンケート'):__('小テスト'),
						'url' => urlencode('//'.CL_DOMAIN.'/s/'.$par['m'].'/ans/'.$aQ[$sP.'ID']),
						'title' => (($aQ['qbQuickMode'])? '[Q]':'').$aQ[$sP.'Title'],
						'stateColor' => (!$aQ[$sP.'Public'])? 'font-default':(($aQ[$sP.'Public'] == 1)? 'font-blue':'font-red'),
						'status' => (!$aQ[$sP.'Public'])? __('非公開'):(($aQ[$sP.'Public'] == 1)? __('公開中'):__('締切')),
						'date' => ClFunc_Tz::tz('Y/m/d H:i',$this->tz,$aQ[$sP.'Date']),
						'cat' => 0,
						'tree' => $aQ[$sP.'ID'],
					);
				}
			}
		}

		$res = array('err'=>0,'res'=>$aList,'msg'=>'');
		$this->response($res);
		return;
	}

}
