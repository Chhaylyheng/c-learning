<?php
class ClFunc_Createcsv
{
	public static function QuestStuAnsList($aStu = null, $aQuest = null, $tz = null)
	{
		$tz = ($tz)? $tz:date_default_timezone_get();
		$sStID = $aStu['stID'];
		$iQNum = 0;
		$aQtIDs = null;

		foreach ($aQuest as $aQ)
		{
			$aQtIDs[] = $aQ['qbID'];
			if ($aQ['qbNum'] > $iQNum)
			{
				$iQNum = $aQ['qbNum'];
			}
		}

		$aPut = null;

		$aPWhere = array(array('qp.stID','=',$sStID));
		$aQWhere = null;
		$aAWhere = array(array('qa.stID','=',$sStID));
		if (!is_null($aQtIDs))
		{
			$aPWhere[] = array('qp.qbID','IN',$aQtIDs);
			$aQWhere[] = array('qbID','IN',$aQtIDs);
			$aAWhere[] = array('qa.qbID','IN',$aQtIDs);
		}
		$result = Model_Quest::getQuestPut($aPWhere);
		if (count($result))
		{
			foreach ($result as $aP)
			{
				$aPut[$aP['qbID']] = $aP;
			}
		}

		$aQuery = null;
		$result = Model_Quest::getQuestQuery($aQWhere);
		if (count($result))
		{
			foreach ($result as $aQ)
			{
				$aQuery[$aQ['qbID']][$aQ['qqSort']] = $aQ;
			}
		}

		$aAns = null;
		$result = Model_Quest::getQuestAns($aAWhere);
		if (count($result))
		{
			foreach ($result as $aA)
			{
				$aAns[$aA['qbID']][$aA['qqSort']] = $aA;
			}
		}

		$res = array(
			array(
				$aStu['stLogin'],
				$aStu['stNO'],
				$aStu['stName'],
				$aStu['stDept'],
				$aStu['stSubject'],
				($aStu['stYear'])? $aStu['stYear']:'',
				$aStu['stClass'],
				$aStu['stCourse'],
			),
			array(
				__('アンケート'),
				__('提出'),
			)
		);
		for ($i = 1; $i <= $iQNum; $i++)
		{
			$res[1][] = 'Q'.$i;
		}

		foreach ($aQuest as $aQ)
		{
			$sQbID = $aQ['qbID'];
			$sQuick = ($aQ['qbQuickMode'])? '[Q]':'';
			$aM = array($sQuick.$aQ['qbTitle'],__('未'));

			if (isset($aPut[$sQbID]))
			{
				if ($aQ['qbAnonymous'])
				{
					$aM[1] = __('済');
				}
				else
				{
					$aM[1] = \Clfunc_Tz::tz('Y/m/d H:i',$tz,$aPut[$sQbID]['qpDate']);
				}
			}
			if (isset($aAns[$sQbID]))
			{
				$iCol = 2;
				foreach ($aAns[$sQbID] as $iQS => $aA)
				{
					$aQQ = $aQuery[$sQbID][$iQS];
					$aM[$iCol] = '';
					if ($aQ['qbAnonymous'])
					{
						$aM[$iCol] = '─';
						$iCol++;
						continue;
					}
					if ($aQQ['qqStyle'] == 2)
					{
						$aM[$iCol] = $aA['qaText'];
					}
					else
					{
						for($i = 1; $i <= 50; $i++)
						{
							if ($aA['qaChoice'.$i])
							{
								$aM[$iCol] .= '['.$i.']'.$aQQ['qqChoice'.$i]."\r\n";
							}
						}
					}
					$iCol++;
				}
				while(count($aM) < ($iQNum + 2))
				{
					$aM[] = '';
				}
			}
			$res[] = $aM;
		}

		return $res;
	}

	public static function TestStuAnsList($aStu = null, $aTest = null, $tz = null)
	{
		$tz = ($tz)? $tz:date_default_timezone_get();
		$sStID = $aStu['stID'];
		$iQNum = 0;
		$aTbIDs = null;

		foreach ($aTest as $aQ)
		{
			$aTbIDs[] = $aQ['tbID'];
			if ($aQ['tbNum'] > $iQNum)
			{
				$iQNum = $aQ['tbNum'];
			}
		}

		$aPut = null;

		$aPWhere = array(array('tp.stID','=',$sStID));
		$aQWhere = null;
		$aAWhere = array(array('ta.stID','=',$sStID));
		if (!is_null($aTbIDs))
		{
			$aPWhere[] = array('tp.tbID','IN',$aTbIDs);
			$aQWhere[] = array('tbID','IN',$aTbIDs);
			$aAWhere[] = array('ta.tbID','IN',$aTbIDs);
		}
		$result = Model_Test::getTestPut($aPWhere);
		if (count($result))
		{
			foreach ($result as $aP)
			{
				$aPut[$aP['tbID']] = $aP;
			}
		}

		$aQuery = null;
		$result = Model_Test::getTestQuery($aQWhere);
		if (count($result))
		{
			foreach ($result as $aQ)
			{
				$aQuery[$aQ['tbID']][$aQ['tqSort']] = $aQ;
			}
		}

		$aAns = null;
		$result = Model_Test::getTestAns($aAWhere);
		if (count($result))
		{
			foreach ($result as $aA)
			{
				$aAns[$aA['tbID']][$aA['tqSort']] = $aA;
			}
		}

		$res = array(
			array(
				$aStu['stLogin'],
				$aStu['stNO'],
				$aStu['stName'],
				$aStu['stDept'],
				$aStu['stSubject'],
				($aStu['stYear'])? $aStu['stYear']:'',
				$aStu['stClass'],
				$aStu['stCourse'],
			),
			array(
				__('小テスト'),
				__('合格点'),
				__('提出'),
				__('得点'),
				__('合格'),
			)
		);
		for ($i = 1; $i <= $iQNum; $i++)
		{
			$res[1][] = 'Q'.$i;
			$res[1][] = __('正解');
		}

		foreach ($aTest as $aQ)
		{
			$sTbID = $aQ['tbID'];
			$aM = array(
				$aQ['tbTitle'],
				(int)$aQ['tbQualifyScore'],
				__('未'),
				0,
				'',
			);

			if (isset($aPut[$sTbID]))
			{
				$aM[2] = \Clfunc_Tz::tz('Y/m/d H:i',$tz,$aPut[$sTbID]['tpDate']);
				$aM[3] = (int)$aPut[$sTbID]['tpScore'];
				$aM[4] = ($aPut[$sTbID]['tpQualify'])? '○':'';
			}
			if (isset($aAns[$sTbID]))
			{
				$iCol = 5;
				foreach ($aAns[$sTbID] as $iQS => $aA)
				{
					$aQQ = $aQuery[$sTbID][$iQS];
					$aM[$iCol] = '';
					$aM[($iCol + 1)] = ($aA['taRight'])? '○':'';
					if ($aQQ['tqStyle'] == 2)
					{
						$aM[$iCol] = $aA['taText'];
					}
					else
					{
						for($i = 1; $i <= 50; $i++)
						{
							if ($aA['taChoice'.$i])
							{
								$aM[$iCol] .= '['.$i.']'.$aQQ['tqChoice'.$i]."\r\n";
							}
						}
					}
					$iCol+=2;
				}
				while(count($aM) < (($iQNum * 2) + 5))
				{
					$aM[] = '';
				}
			}
			$res[] = $aM;
		}
		return $res;
	}
}