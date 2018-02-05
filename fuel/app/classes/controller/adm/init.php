<?php
class Controller_Adm_Init extends Controller_Adm_Base
{
	public function action_TeacherClassSort()
	{
		$result = Model_Class::getClassFromTeacher(null,1,null,null,array('ttID'=>'desc','tpSort'=>'asc','ctStatus'=>'desc','ctYear'=>'desc','dpNO'=>'desc','ctWeekday'=>'desc','dhNO'=>'desc'));
		if (count($result))
		{
			DB::start_transaction();

			$sTtID = null;
			$iSort = 1;
			foreach ($result as $aC)
			{
				if ($sTtID !== $aC['ttID'])
				{
					$iSort = 1;
				}

				$res = DB::update('TeacherPosition_Table')
					->where('ttID','=',$aC['ttID'])
					->where('ctID','=',$aC['ctID'])
					->set(array('tpSort'=>$iSort))
					->execute()
				;

				echo $aC['ttID'].' - '.$aC['ctID'].' - '.$iSort.'<br>';

				$iSort++;
				$sTtID = $aC['ttID'];
			}

			$result = Model_Class::getClassFromTeacher(null,0);
			if (count($result))
			{
				foreach ($result as $aC)
				{
					$res = DB::update('TeacherPosition_Table')
						->where('ttID','=',$aC['ttID'])
						->where('ctID','=',$aC['ctID'])
						->set(array('tpSort'=>0))
						->execute()
					;
				}
			}

			DB::commit_transaction();
		}


		exit();
	}

	public function action_CoopItemSort()
	{
		$result = Model_Coop::getCoop(array(array('ci.cRoot','=',0)),null,array('ci.ccID'=>'acs','ci.cDate'=>'asc'));

		if (count($result))
		{
			DB::start_transaction();

			$sCcID = null;
			$iSort = 1;
			foreach ($result as $aC)
			{
				if ($sCcID !== $aC['ccID'])
				{
					$iSort = 1;
				}

				$res = DB::update('CoopItem_Table')
					->where('cNO','=',$aC['cNO'])
					->set(array('cSort'=>$iSort))
					->execute()
				;

				echo $aC['ccID'].' - '.$aC['cNO'].' - '.$iSort.'<br>';

				$iSort++;
				$sCcID = $aC['ccID'];
			}

			DB::commit_transaction();
		}

		exit();
	}

	public function action_CoopChildSort()
	{
		$result = Model_Coop::getCoop(array(array('ci.cRoot','!=',0),array('ci.cBranch','=',0)),null,array('ci.ccID'=>'acs','ci.cRoot'=>'asc','ci.cDate'=>'asc'));

		if (count($result))
		{
			DB::start_transaction();

			$iRoot = null;
			$iSort = 1;
			foreach ($result as $aC)
			{
				if ($iRoot !== $aC['cRoot'])
				{
					$iSort = 1;
				}

				$res = DB::update('CoopItem_Table')
				->where('cNO','=',$aC['cNO'])
				->set(array('cSort'=>$iSort))
				->execute()
				;

				echo $aC['ccID'].' - '.$aC['cRoot'].' - '.$aC['cNO'].' - '.$iSort.'<br>';

				$iSort++;
				$iRoot = $aC['cRoot'];
			}

			DB::commit_transaction();
		}

		exit();
	}

	public function action_DefaultTeacherPlan()
	{
		$result = Model_Teacher::getTeacher();

		if (count($result))
		{
			DB::start_transaction();

			foreach ($result as $aT)
			{
				if (!$aT['ptID'])
				{
					if ($aT['gtID'])
					{
						$aInsert = array(
							'ttID' => $aT['ttID'],
							'ptID' => 99,
							'coStartDate' => $aT['ttDate'],
						);
					}
					else
					{
						$aInsert = array(
							'ttID' => $aT['ttID'],
							'ptID' => 1,
							'coStartDate' => $aT['ttDate'],
							'coTermDate'  => '20161031',
							'coClassNum'  => $aT['ttClassNum'],
							'coStuNum'    => 300,
							'coCapacity'  => 1,
							'coPayment'   => 0,
							'coMonths'    => 1,
						);
					}

					$res = DB::insert('Contract_Table')
						->set($aInsert)
						->execute()
					;

					echo $aT['ttID'].' - '.$aT['ttName'].' - '.$aT['gtID'].'<br>';
				}
			}

			DB::commit_transaction();
		}

		exit();
	}


	public function action_GroupPrefixInsert()
	{
		$result = Model_Group::getGroup();

		if (count($result))
		{
			$aGroup = $result->as_array('gtID');

			$result = Model_Class::getClass(array(array('gtID','!=',null)));
			if (count($result))
			{
				DB::start_transaction();

				foreach ($result as $aC)
				{
					if (strpos($aC['ctCode'],'@') !== false)
					{
						continue;
					}

					$aUpdate = array(
						'ctCode' => $aGroup[$aC['gtID']]['gtPrefix'].'@'.$aC['ctCode']
					);

					$res = DB::update('Class_Table')
						->set($aUpdate)
						->where('ctID','=',$aC['ctID'])
						->execute();

					echo $aC['ctID'].' - '.$aC['ctName'].' - '.$aUpdate['ctCode'].'<br>';
				}

				DB::commit_transaction();
			}
		}

		exit();
	}

	public function action_TeacherUsed()
	{
		$result = Model_Teacher::getTeacher();
		if (count($result))
		{
			$aTeacher = $result->as_array('ttID');
			$aClassNum = null;
			$aCloseNum = null;
			$aStuNum = null;
			$aDiskUse = null;

			$result = DB::select_array(array('ttID',DB::expr('count(ctID) AS ttClassNum')))
				->from('TeacherClassList_View')
				->where('ctStatus',1)
				->group_by('ttID')
				->execute();
			if (count($result))
			{
				$aClassNum = $result->as_array('ttID');
			}
			$result = DB::select_array(array('ttID',DB::expr('count(ctID) AS ttCloseNum')))
				->from('TeacherClassList_View')
				->where('ctStatus',0)
				->group_by('ttID')
				->execute();
			if (count($result))
			{
				$aCloseNum = $result->as_array('ttID');
			}
			$result = DB::select_array(array('ttID',DB::expr('max(scNum) AS ttStuNum')))
				->from('TeacherClassList_View')
				->group_by('ttID')
				->execute();
			if (count($result))
			{
				$aStuNum = $result->as_array('ttID');
			}

			foreach ($aTeacher as $sTtID => $aT)
			{
				$iSize = 0;
				$result = Model_Class::getClassFromTeacher($sTtID);
				if (count($result))
				{
					foreach ($result as $aC)
					{
						$res1 = DB::select_array(array(DB::expr('SUM(ccTotalSize) AS CoopSize')))
							->from('CoopCategory_View')
							->where('ctID',$aC['ctID'])
							->execute();
						if (count($res1))
						{
							$aRes = $res1->current();
							$iSize += (int)$aRes['CoopSize'];
						}
						$res1 = DB::select_array(array(DB::expr('SUM(mcTotalSize) AS MatSize')))
							->from('MaterialCategory_View')
							->where('ctID',$aC['ctID'])
							->execute();
						if (count($res1))
						{
							$aRes = $res1->current();
							$iSize += (int)$aRes['MatSize'];
						}

						$res2 = Model_Report::getReportBase(array(array('rb.ctID','=',$aC['ctID'])));
						if (count($res2))
						{
							foreach ($res2 as $aR)
							{
								$iSize += (int)$aR['baseFSize'];
								$iSize += (int)$aR['resultFSize'];

								$res3 = Model_Report::getReportPut(array(array('rp.rbID','=',$aR['rbID'])));
								if (count($res3))
								{
									foreach ($res3 as $aP)
									{
										$iSize += (int)$aP['fSize1'];
										$iSize += (int)$aP['fSize2'];
										$iSize += (int)$aP['fSize3'];

										$iSize += (int)$aP['rSize1'];
										$iSize += (int)$aP['rSize2'];
										$iSize += (int)$aP['rSize3'];
									}
								}

							}
						}

					}
				}

				$aInsert = array(
						'ttiD' => $sTtID,
						'ttClassNum' => ((isset($aClassNum[$sTtID]))? (int)$aClassNum[$sTtID]['ttClassNum']:0),
						'ttCloseNum' => ((isset($aCloseNum[$sTtID]))? (int)$aCloseNum[$sTtID]['ttCloseNum']:0),
						'ttStuNum' => ((isset($aStuNum[$sTtID]))? (int)$aStuNum[$sTtID]['ttStuNum']:0),
						'ttDiskUsed' => $iSize,
				);

				DB::start_transaction();

				DB::delete('TeacherUsed_Table')
					->where('ttID',$sTtID)
					->execute()
				;

				DB::insert('TeacherUsed_Table')
					->set($aInsert)
					->execute()
				;

				DB::commit_transaction();
			}
			exit();
		}
	}

	public function action_OrgUsed()
	{
		$result = Model_Group::getGroup();
		if (count($result))
		{
			$aDiskUse = null;

			echo '"団体名称","先生のファイルサイズ","先生のファイル数","学生のファイルサイズ","学生のファイル数"'."<br>";

			foreach ($result as $aG)
			{
				$sGtID = $aG['gtID'];
				$iTSize = 0;
				$iSSize = 0;
				$iTCnt = 0;
				$iSCnt = 0;

				$resT = Model_Group::getGroupTeachers(array(array('gtp.gtID','=',$sGtID)));
				if (count($resT))
				{
					$aTs = $resT->as_array('ttID');
					$aTIDs = array_keys($aTs);

					$res1 = DB::select_array(array(DB::expr('SUM(fSize) AS TSize'),DB::expr('count(fSize) AS TCnt')))
						->from('File_Table')
						->where('fUser','IN',$aTIDs)
						->execute();
					if (count($res1))
					{
						$aTSize = $res1->current();
						$iTSize += $aTSize['TSize'];
						$iTCnt += $aTSize['TCnt'];
					}
				}

				$resS = Model_Group::getGroupStudents(array(array('gsp.gtID','=',$sGtID)));
				if (count($resS))
				{
					$aSs = $resS->as_array('stID');
					$aSIDs = array_keys($aSs);

					$res1 = DB::select_array(array(DB::expr('SUM(fSize) AS SSize'),DB::expr('count(fSize) AS SCnt')))
					->from('File_Table')
					->where('fUser','IN',$aSIDs)
					->execute();
					if (count($res1))
					{
						$aSSize = $res1->current();
						$iSSize += $aSSize['SSize'];
						$iSCnt += $aSSize['SCnt'];
					}
				}

				echo '"'.$aG['gtName'].'",'.$iTSize.','.$iTCnt.','.$iSSize.','.$iSCnt."<br>";
			}
			exit();
		}
	}

	public function action_GroupPrefixDelete()
	{
		$result = Model_Group::getGroup();

		if (count($result))
		{
			$aGroup = $result->as_array('gtID');

			$result = Model_Class::getClass(array(array('gtID','!=',null)));
			if (count($result))
			{
				DB::start_transaction();

				foreach ($result as $aC)
				{
					if (strpos($aC['ctCode'],'@') === false)
					{
						continue;
					}

					$aUpdate = array(
						'ctCode' => str_replace($aGroup[$aC['gtID']]['gtPrefix'].'@', '', $aC['ctCode'])
					);

					$res = DB::update('Class_Table')
						->set($aUpdate)
						->where('ctID','=',$aC['ctID'])
						->execute();

					echo $aC['ctID'].' - '.$aC['ctName'].' - '.$aUpdate['ctCode'].'<br>';
				}

				DB::commit_transaction();
			}
		}
		exit();
	}

	public function action_AttendMasterRegist()
	{
		$result = Model_Class::getClass(array(array('ctID','NOT IN',DB::expr('(SELECT DISTINCT ctID FROM AttendState_Master)'))));
		if (!count($result))
		{
			exit();
		}

		foreach ($result as $aC)
		{
			$sCtID = $aC['ctID'];
			print $aC['ctID'].' / '.$aC['ctCode'].' / '.$aC['ctName'].' - Start ('.date('Y-m-d H:i:s.').Clfunc_Common::msec().')'."\n";

			DB::start_transaction();

			$sDate = date('YmdHis');
			$aAMColumn = array('ctID','amAttendState','amName','amShort','amAbsence','amDefault','amTime','amDate');
			$aAMValue = array(
				array($sCtID,0,'欠席','欠',1,0,0,$sDate),
				array($sCtID,1,'出席','出',0,1,0,$sDate),
				array($sCtID,2,'遅刻','遅',0,0,10,$sDate),
				array($sCtID,3,'早退','早',0,0,0,$sDate),
				array($sCtID,4,'その他','他',0,0,0,$sDate)
			);
			foreach ($aAMValue as $aAMV)
			{
				$query = DB::insert('AttendState_Master')->columns($aAMColumn)->values($aAMV)->execute();
			}

			DB::commit_transaction();

			print $aC['ctID'].' / '.$aC['ctCode'].' / '.$aC['ctName'].' - End ('.date('Y-m-d H:i:s.').Clfunc_Common::msec().')'."\n";
		}
		exit();
	}


	public function action_MaterialFileSize()
	{
		$result = Model_Material::getMaterial();

		if (!count($result))
		{
			exit();
		}

		DB::start_transaction();

		foreach ($result as $aM)
		{
			print $aM['mcID'].' / '.$aM['mNO'].' / '.$aM['fID'].' / '.$aM['ftSize'].' - Start ('.date('Y-m-d H:i:s.').Clfunc_Common::msec().')'."\n";

			$aUpdate = array('fSize'=>$aM['ftSize']);

			$query = DB::update('Material_Table')
				->and_where('mNO','=',$aM['mNO'])
				->set($aUpdate)
				->execute()
			;

			print $aM['mcID'].' / '.$aM['mNO'].' / '.$aM['fID'].' / '.$aM['ftSize'].' - End ('.date('Y-m-d H:i:s.').Clfunc_Common::msec().')'."\n";
		}

		DB::commit_transaction();
		exit();
	}

	public function action_CoopFileSize()
	{
		$result = Model_Coop::getCoop();

		if (!count($result))
		{
			exit();
		}

		DB::start_transaction();

		foreach ($result as $aC)
		{
			print $aC['ccID'].' / '.$aC['cNO'].' / '.$aC['fSize1'].' / '.$aC['fSize2'].' / '.$aC['fSize3'].' - Start ('.date('Y-m-d H:i:s.').Clfunc_Common::msec().')'."\n";

			$aUpdate = array('fSumSize'=>($aC['fSize1'] + $aC['fSize2'] + $aC['fSize3']));

			$query = DB::update('CoopItem_Table')
				->and_where('cNO','=',$aC['cNO'])
				->set($aUpdate)
				->execute()
				;

			print $aC['ccID'].' / '.$aC['cNO'].' / '.$aC['fSize1'].' / '.$aC['fSize2'].' / '.$aC['fSize3'].' - End ('.date('Y-m-d H:i:s.').Clfunc_Common::msec().')'."\n";
		}

		DB::commit_transaction();
		exit();
	}

}
