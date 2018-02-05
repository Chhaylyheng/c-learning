<?php
class Model_Quest extends \Model
{
	public static function getQuestBaseFromClass($sCtID = null,$aAndWhere = null,$aOrWhere = null,$aSort = null)
	{
		$aSWhere = array(array('qb.qbID','=',DB::expr('qp.qbID')),array('qp.stID','like','s%'));
		$subquery = DB::select(DB::expr('count(qp.no)'))->from(array('QuestPut_Table','qp'))
			->where($aSWhere)
			->compile();
		$sSSub = '('.$subquery.') AS `qpNum`';

		$aGWhere = array(array('qb.qbID','=',DB::expr('qp.qbID')),array('qp.stID','like','g%'));
		$subquery = DB::select(DB::expr('count(qp.no)'))->from(array('QuestPut_Table','qp'))
			->where($aGWhere)
			->compile();
		$sGSub = '('.$subquery.') AS `qpGNum`';

		$aTWhere = array(array('qb.qbID','=',DB::expr('qp.qbID')),array('qp.stID','like','t%'));
		$subquery = DB::select(DB::expr('count(qp.no)'))->from(array('QuestPut_Table','qp'))
			->where($aTWhere)
			->compile();
		$sTSub = '('.$subquery.') AS `qpTNum`';

		$aNWhere = array(array('qb.ctID','=',DB::expr('sp.ctID')),array('sp.spAuth','=',1));
		$subquery = DB::select(DB::expr('count(sp.stID)'))->from(array('StudentPosition_Table','sp'))
			->where($aNWhere)
			->compile();
		$sNSub = '('.$subquery.') AS `scNum`';

		$query = DB::select_array(array('qb.*',DB::expr($sNSub),DB::expr($sSSub),DB::expr($sGSub),DB::expr($sTSub)))
			->from(array('QuestBase_Table','qb'))
			->where('qb.ctID','=',$sCtID)
		;
		if (!is_null($aAndWhere))
		{
			foreach ($aAndWhere as $aW)
			{
				$query->where($aW[0],$aW[1],$aW[2]);
			}
		}
		if (!is_null($aOrWhere))
		{
			foreach ($aOrWhere as $aW)
			{
				$query->or_where($aW[0],$aW[1],$aW[2]);
			}
		}
		if (!is_null($aSort))
		{
			foreach ($aSort as $sC => $sS)
			{
				$query->order_by($sC,$sS);
			}
		}
		$result = $query->execute();
		return $result;
	}
	public static function getQuestBaseFromID($sID = null,$aAndWhere = null)
	{
		$aSWhere = array(array('qb.qbID','=',DB::expr('qp.qbID')),array('qp.stID','like','s%'));
		$subquery = DB::select(DB::expr('count(qp.no)'))->from(array('QuestPut_Table','qp'))
			->where($aSWhere)
			->compile();
		$sSSub = '('.$subquery.') AS `qpNum`';

		$aGWhere = array(array('qb.qbID','=',DB::expr('qp.qbID')),array('qp.stID','like','g%'));
		$subquery = DB::select(DB::expr('count(qp.no)'))->from(array('QuestPut_Table','qp'))
			->where($aGWhere)
			->compile();
		$sGSub = '('.$subquery.') AS `qpGNum`';

		$aTWhere = array(array('qb.qbID','=',DB::expr('qp.qbID')),array('qp.stID','like','t%'));
		$subquery = DB::select(DB::expr('count(qp.no)'))->from(array('QuestPut_Table','qp'))
			->where($aTWhere)
			->compile();
		$sTSub = '('.$subquery.') AS `qpTNum`';

		$aNWhere = array(array('qb.ctID','=',DB::expr('sp.ctID')),array('sp.spAuth','=',1));
		$subquery = DB::select(DB::expr('count(sp.stID)'))->from(array('StudentPosition_Table','sp'))
		->where($aNWhere)
		->compile();
		$sNSub = '('.$subquery.') AS `scNum`';

		$query = DB::select_array(array('qb.*',DB::expr($sNSub),DB::expr($sSSub),DB::expr($sGSub),DB::expr($sTSub)))
			->from(array('QuestBase_Table','qb'))
			->where('qb.qbID','=',$sID);

		if (!is_null($aAndWhere))
		{
			foreach ($aAndWhere as $aW)
			{
				$query->where($aW[0],$aW[1],$aW[2]);
			}
		}
		$result = $query->execute();
		return $result;
	}
	public static function insertQuickQuest($aQBase = null,$aQQuery = null)
	{
		if (is_null($aQBase) || is_null($aQQuery))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			$sQbID = self::getQuestID();
			$aQBase['qbID'] = $sQbID;
			$aQBase['qbSort'] = self::getQuestSort($aQBase['ctID']);
			$aQBase['qbPublic'] = 1;
			$aQBase['qbBentPublic'] = 1;

			$result = DB::insert('QuestBase_Table')->set($aQBase)->execute();
			foreach ($aQQuery as $aQ)
			{
				$aQ['qbID'] = $sQbID;
				$result = DB::insert('QuestQuery_Table')->set($aQ)->execute();
			}
			DB::commit_transaction();
			// クエリの結果を返す
			return $sQbID;
		}
		catch (Exception $e)
		{
			// 未決のトランザクションクエリをロールバックする
			DB::rollback_transaction();
			throw $e;
		}
	}
	public static function insertQuest($aInsert = null)
	{
		if (is_null($aInsert))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			$sQbID = self::getQuestID();
			$aInsert['qbID'] = $sQbID;
			$aInsert['qbSort'] = self::getQuestSort($aInsert['ctID']);
			$result = DB::insert('QuestBase_Table')->set($aInsert)->execute();
			DB::commit_transaction();
			// クエリの結果を返す
			return $sQbID;
		}
		catch (Exception $e)
		{
			// 未決のトランザクションクエリをロールバックする
			DB::rollback_transaction();
			throw $e;
		}
	}
	public static function updateQuest($aUpdate = null,$aAndWhere = null,$aOrWhere = null)
	{
		if (is_null($aUpdate))
		{
			throw new Exception('処理に必要な情報がありません');
		}
			try
		{
			DB::start_transaction();
			$query = DB::update('QuestBase_Table');
			if (!is_null($aAndWhere))
			{
				foreach ($aAndWhere as $aW)
				{
					$query->and_where($aW[0],$aW[1],$aW[2]);
				}
			}
			if (!is_null($aOrWhere))
			{
				foreach ($aOrWhere as $aW)
				{
					$query->or_where($aW[0],$aW[1],$aW[2]);
				}
			}
			$query->set($aUpdate);
			$result = $query->execute();
			DB::commit_transaction();
			// クエリの結果を返す
			return $result;
		}
		catch (Exception $e)
		{
			// 未決のトランザクションクエリをロールバックする
			DB::rollback_transaction();
			throw $e;
		}
	}
	public static function deleteQuest($sID = null,$aActive = null)
	{
		if (is_null($sID) || is_null($aActive))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			$result = DB::delete('QuestBent_Table')->where('qbID',$sID)->execute();
			$result = DB::delete('QuestAns_Table')->where('qbID',$sID)->execute();
			$result = DB::delete('QuestPut_Table')->where('qbID',$sID)->execute();
			$result = DB::delete('QuestQuery_Table')->where('qbID',$sID)->execute();
			$result = DB::delete('QuestBase_Table')->where('qbID',$sID)->execute();

			$query = DB::update('QuestBase_Table');
			$query->and_where('ctID',$aActive['ctID']);
			$query->and_where('qbSort','>',$aActive['qbSort']);
			$query->set(array('qbSort'=>DB::expr('qbSort - 1')));
			$result = $query->execute();

			DB::commit_transaction();
			// クエリの結果を返す
			return $result;
		}
		catch (Exception $e)
		{
			// 未決のトランザクションクエリをロールバックする
			DB::rollback_transaction();
			throw $e;
		}
	}
	public static function insertQuestFromCSV($aQBase = null,$aQQuery = null,$sCtID = null)
	{
		if (is_null($aQBase) || is_null($aQQuery) || is_null($sCtID))
		{
			throw new Exception('処理に必要な情報がありません');
		}

		$sDate = date('YmdHis');

		try
		{
			DB::start_transaction();
			$sQbID = self::getQuestID();
			$aQBase['qbID'] = $sQbID;
			$aQBase['qbSort'] = self::getQuestSort($sCtID);
			$aQBase['ctID'] = $sCtID;
			$aQBase['qbDate'] = $sDate;

			$result = DB::insert('QuestBase_Table')->set($aQBase)->execute();
			foreach ($aQQuery as $iQN => $aQ)
			{
				$aQ['qbID'] = $sQbID;
				$aQ['qqNO'] = $iQN;
				$aQ['qqSort'] = $iQN;
				$aQ['qqDate'] = $sDate;
				if (!isset($aQ['qqRequired']))
				{
					if ($aQ['qqStyle'] < 2)
					{
						$aQ['qqRequired'] = 1;
					}
					else
					{
						$aQ['qqRequired'] = 0;
					}
				}
				$result = DB::insert('QuestQuery_Table')->set($aQ)->execute();
			}
			DB::commit_transaction();
			// クエリの結果を返す
			return $sQbID;
		}
		catch (Exception $e)
		{
			// 未決のトランザクションクエリをロールバックする
			DB::rollback_transaction();
			throw $e;
		}
	}

	public static function sortQuest($aQuest = null,$sSort = null)
	{
		if (is_null($aQuest) || is_null($sSort))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			if ($sSort == "down")
			{
				$iWhere = -1;
				$iUp1 = "+1";
				$iUp2 = "-1";
			} else {
				$iWhere = 1;
				$iUp1 = "-1";
				$iUp2 = "+1";
			}
			DB::start_transaction();
			$query = DB::update('QuestBase_Table');
			$query->and_where('ctID',$aQuest['ctID']);
			$query->and_where('qbSort',$aQuest['qbSort']+$iWhere);
			$query->set(array('qbSort'=>DB::expr('qbSort'.$iUp1)));
			$result = $query->execute();
			$query = DB::update('QuestBase_Table');
			$query->and_where('qbID',$aQuest['qbID']);
			$query->set(array('qbSort'=>DB::expr('qbSort'.$iUp2)));
			$result = $query->execute();
			DB::commit_transaction();
			// クエリの結果を返す
			return $result;
		}
		catch (Exception $e)
		{
			// 未決のトランザクションクエリをロールバックする
			DB::rollback_transaction();
			throw $e;
		}
	}

	public static function insertQuestQuery($aInsert = null)
	{
		if (is_null($aInsert))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			$iQqNO = self::getQueryNO($aInsert['qbID']);
			self::setQuerySort($aInsert['qbID'],$aInsert['qqSort']);
			$aInsert['qqNO'] = $iQqNO;
			$result = DB::insert('QuestQuery_Table')->set($aInsert)->execute();
			$result = DB::update('QuestBase_Table')->set(array('qbNum'=>DB::expr('qbNum+1')))->where('qbID',$aInsert['qbID'])->execute();
			DB::commit_transaction();
			// クエリの結果を返す
			return $iQqNO;
		}
		catch (Exception $e)
		{
			// 未決のトランザクションクエリをロールバックする
			DB::rollback_transaction();
			throw $e;
		}
	}

	public static function updateQuestQuery($aUpdate = null,$aAndWhere = null,$aOrWhere = null)
	{
		if (is_null($aUpdate))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			$query = DB::update('QuestQuery_Table');
			if (!is_null($aAndWhere))
			{
				foreach ($aAndWhere as $aW)
				{
					$query->and_where($aW[0],$aW[1],$aW[2]);
				}
			}
			if (!is_null($aOrWhere))
			{
				foreach ($aOrWhere as $aW)
				{
					$query->or_where($aW[0],$aW[1],$aW[2]);
				}
			}
			$query->set($aUpdate);
			$result = $query->execute();
			DB::commit_transaction();
			// クエリの結果を返す
			return $result;
		}
		catch (Exception $e)
		{
			// 未決のトランザクションクエリをロールバックする
			DB::rollback_transaction();
			throw $e;
		}
	}

	public static function deleteQuestQuery($sID = null, $iNO = null,$aActive = null)
	{
		if (is_null($sID) || is_null($iNO) || is_null($aActive))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			$result = DB::delete('QuestQuery_Table')->where('qbID',$sID)->and_where('qqNO',$iNO)->execute();
			self::setQuerySort($sID,$aActive['qqSort'],true);
			$result = DB::update('QuestBase_Table')->set(array('qbNum'=>DB::expr('qbNum-1')))->where('qbID',$sID)->execute();
			DB::commit_transaction();
			// クエリの結果を返す
			return $result;
		}
		catch (Exception $e)
		{
			// 未決のトランザクションクエリをロールバックする
			DB::rollback_transaction();
			throw $e;
		}
	}

	public static function sortQuestQuery($aQuery = null,$sSort = null)
	{
		if (is_null($aQuery) || is_null($sSort))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			if ($sSort == "down")
			{
				$iWhere = +1;
				$iUp1 = "-1";
				$iUp2 = "+1";
			} else {
				$iWhere = -1;
				$iUp1 = "+1";
				$iUp2 = "-1";
			}
			DB::start_transaction();
			$query = DB::update('QuestQuery_Table');
			$query->and_where('qbID',$aQuery['qbID']);
			$query->and_where('qqSort',$aQuery['qqSort']+$iWhere);
			$query->set(array('qqSort'=>DB::expr('qqSort'.$iUp1)));
			$result = $query->execute();
			$query = DB::update('QuestQuery_Table');
			$query->and_where('qbID',$aQuery['qbID']);
			$query->and_where('qqNO',$aQuery['qqNO']);
			$query->set(array('qqSort'=>DB::expr('qqSort'.$iUp2)));
			$result = $query->execute();
			DB::commit_transaction();
			// クエリの結果を返す
			return $result;
		}
		catch (Exception $e)
		{
			// 未決のトランザクションクエリをロールバックする
			DB::rollback_transaction();
			throw $e;
		}
	}

	public static function deleteQuestPut($sID = null)
	{
		if (is_null($sID))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			$result = DB::delete('QuestBent_Table')->where('qbID',$sID)->execute();
			$result = DB::delete('QuestAns_Table')->where('qbID',$sID)->execute();
			$result = DB::delete('QuestPut_Table')->where('qbID',$sID)->execute();
			DB::commit_transaction();
			// クエリの結果を返す
			return $result;
		}
		catch (Exception $e)
		{
			// 未決のトランザクションクエリをロールバックする
			DB::rollback_transaction();
			throw $e;
		}
	}

	public static function setQuestBent($aQuest = null,$iTextBent = 0)
	{
		if (is_null($aQuest))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		$result = DB::select_array()->from('QuestQuery_Table')->and_where('qbID','=',$aQuest['qbID'])->execute();
		if (!count($result))
		{
			throw new Exception('設問がありません。先に設問を作成してください。');
		}
		$aQqs = $result->as_array();
		$sDate = date("YmdHis");

		try
		{
			DB::start_transaction();
			if ((int)$aQuest["qbBentText"] != $iTextBent)
			{
				$result = DB::update('QuestBase_Table')->and_where('qbID','=',$aQuest['qbID'])->set(array('qbBentText'=>$iTextBent))->execute();
				$aQuest["qbBentText"] = $iTextBent;
			}
			$result = DB::delete('QuestBent_Table')->and_where('qbID','=',$aQuest['qbID'])->execute();

			// 基本
			$aColmuns = array('qbID','qbMode','qqNO','qbNO','qbText','stID','qbNum','qbAll','qbDate','qbTotal');

			$aMode = array(
				'ALL'=>array('where'=>'','num'=>$aQuest['qpNum']+$aQuest['qpGNum']+$aQuest['qpTNum']),
				'STUDENT'=>array('where'=>array(array('stID','LIKE','s%')),'num'=>$aQuest['qpNum']),
				'GUEST'=>array('where'=>array(array('stID','LIKE','g%')),'num'=>$aQuest['qpGNum']),
				'TEACH'=>array('where'=>array(array('stID','LIKE','t%')),'num'=>$aQuest['qpTNum']),
			);

			foreach ($aMode as $sMode => $aM)
			{
				$insertQuery = DB::insert('QuestBent_Table',$aColmuns);
				// ALL
				$aInsert = null;

				foreach ($aQqs as $aQq)
				{
					if ($aQq['qqStyle'] == 2)
					{
						if ($aQuest["qbBentText"])
						{
							$query = DB::select_array(array('qaText',DB::expr('COUNT(no) AS qbNum'),DB::expr('MIN(qaDate) AS qbDate')));
							$query->from('QuestAns_Table');
							$query->and_where('qbID','=',$aQq['qbID'])->and_where('qqNO','=',$aQq['qqNO']);
							if (is_array($aM['where']))
							{
								$query->and_where($aM['where']);
							}
							$query->group_by('qaText')->order_by('qaDate','asc')->order_by('qaText','asc');
						}
						else
						{
							$query = DB::select_array(array('qaText','qaDate','stID'));
							$query->from('QuestAns_Table');
							$query->and_where('qbID','=',$aQq['qbID'])->and_where('qqNO','=',$aQq['qqNO']);
							if (is_array($aM['where']))
							{
								$query->and_where($aM['where']);
							}
							$query->order_by('qaDate','asc')->order_by('qaText','asc');
						}
						$result = $query->execute();
						if (count($result))
						{
							$aQas = $result->as_array();
							$iCnt = 1;
							$aInsert = null;

							foreach ($aQas as $aQa)
							{
								$iQbNum = (isset($aQa["qbNum"]))? (int)$aQa["qbNum"]:1;
								$sStID = (isset($aQa["stID"]))? $aQa["stID"]:'';
								$aInsert = array(
									$aQq['qbID'],
									$sMode,
									$aQq['qqNO'],
									$iCnt,
									$aQa['qaText'],
									$sStID,
									$iQbNum,
									$aM['num'],
									$sDate,
									$iQbNum,
								);
								$insertQuery->values($aInsert);
								$iCnt++;
							}
						}
					}
					else
					{
						$iQbTotal = 0;
						$aInsert = null;
						for($i = 1; $i <= $aQq["qqChoiceNum"]; $i++)
						{
							$aQa = null;
							$query = DB::select_array(array(DB::expr('COUNT(no) AS qbNum'),DB::expr('MIN(qaDate) AS qbDate')));
							$query->from('QuestAns_Table');
							$query->and_where('qbID','=',$aQq['qbID'])->and_where('qqNO','=',$aQq['qqNO'])->and_where('qaChoice'.$i,'=',1);
							if (is_array($aM['where']))
							{
								$query->and_where($aM['where']);
							}
							$query->order_by('qaDate','asc');
							$result = $query->execute();
							$aQa['qbNum'] = 0;
							if (count($result))
							{
								$aQa = $result->current();
							}
							$iQbTotal += $aQa['qbNum'];
							$aInsert[$i] = array(
								$aQq['qbID'],
								$sMode,
								$aQq['qqNO'],
								$i,
								'',
								'',
								$aQa['qbNum'],
								$aM['num'],
								$sDate,
							);
						}
						$aQa = null;
						$query = DB::select_array(array(DB::expr('COUNT(no) AS qbNum'),DB::expr('MIN(qaDate) AS qbDate')));
						$query->from('QuestAns_Table');
						$query->and_where('qbID','=',$aQq['qbID'])->and_where('qqNO','=',$aQq['qqNO']);
						if (is_array($aM['where']))
						{
							$query->and_where($aM['where']);
						}
						for ($i = 1; $i <= 50; $i++)
						{
							$query->and_where('qaChoice'.$i,'=',0);
						}
						$query->order_by('qaDate','asc');
						$result = $query->execute();
						$aQa['qbNum'] = 0;
						if (count($result))
						{
							$aQa = $result->current();
						}
						$iQbTotal += $aQa['qbNum'];
						$aInsert[$i] = array(
							$aQq['qbID'],
							$sMode,
							$aQq['qqNO'],
							0,
							'',
							'',
							$aQa['qbNum'],
							$aM['num'],
							$sDate,
						);
						foreach ($aInsert as $aI)
						{
							$aI['qbTotal'] = $iQbTotal;
							$insertQuery->values($aI);
						}
					}
				}
				if (!is_null($aInsert))
				{
					$result = $insertQuery->execute();
				}
			}

			DB::commit_transaction();
			// クエリの結果を返す
			return $result;
		}
		catch (Exception $e)
		{
			// 未決のトランザクションクエリをロールバックする
			DB::rollback_transaction();
			throw $e;
		}
	}
	public static function getQuestPut($aAndWhere = null,$aOrWhere = null,$aSort = null)
	{
		$query = DB::select_array(array('qb.ctID','qp.*','gt.gtName','tt.ttName','tt.ttDept','tt.ttSubject'))
			->from(array('QuestPut_Table','qp'))
			->join(array('QuestBase_Table','qb'),'LEFT')
			->on('qp.qbID','=','qb.qbID')
			->join(array('Guest_Table','gt'),'LEFT')
			->on('qp.stID','=','gt.gtID')
			->join(array('Teacher_Table','tt'),'LEFT')
			->on('qp.stID','=','tt.ttID')
		;
		if (!is_null($aAndWhere))
		{
			foreach ($aAndWhere as $aW)
			{
				$query->where($aW[0],$aW[1],$aW[2]);
			}
		}
		if (!is_null($aOrWhere))
		{
			foreach ($aOrWhere as $aW)
			{
				$query->or_where($aW[0],$aW[1],$aW[2]);
			}
		}
		if (!is_null($aSort))
		{
			foreach ($aSort as $sC => $sS)
			{
				$query->order_by($sC,$sS);
			}
		}
		$result = $query->execute();
		return $result;
	}
	public static function getQuestQuery($aAndWhere = null,$aOrWhere = null,$aSort = null)
	{
		$query = DB::select_array()->from('QuestQuery_Table');
		if (!is_null($aAndWhere))
		{
			foreach ($aAndWhere as $aW)
			{
				$query->where($aW[0],$aW[1],$aW[2]);
			}
		}
		if (!is_null($aOrWhere))
		{
			foreach ($aOrWhere as $aW)
			{
				$query->or_where($aW[0],$aW[1],$aW[2]);
			}
		}
		if (!is_null($aSort))
		{
			foreach ($aSort as $sC => $sS)
			{
				$query->order_by($sC,$sS);
			}
		}
		$result = $query->execute();
		return $result;
	}
	public static function getQuestAns($aAndWhere = null,$aOrWhere = null,$aSort = null)
	{

//		$query = DB::select_array()->from('QuestAns_View');
		$query = DB::select_array(array('qa.*','qq.qqSort'))
			->from(array('QuestAns_Table','qa'))
			->join(array('QuestQuery_Table','qq'),'LEFT')
			->on('qa.qqNO','=','qq.qqNO')
			->on('qa.qbID','=','qq.qbID')
		;



		if (!is_null($aAndWhere))
		{
			foreach ($aAndWhere as $aW)
			{
				$query->where($aW[0],$aW[1],$aW[2]);
			}
		}
		if (!is_null($aOrWhere))
		{
			foreach ($aOrWhere as $aW)
			{
				$query->or_where($aW[0],$aW[1],$aW[2]);
			}
		}
		if (!is_null($aSort))
		{
			foreach ($aSort as $sC => $sS)
			{
				$query->order_by($sC,$sS);
			}
		}
		$result = $query->execute();
		return $result;
	}

	public static function setQuestPut($aQuest = null,$aQuery = null,$aStudent = null,$aInput = null,$bUpdate = false)
	{
		if (is_null($aQuest) || is_null($aStudent) || is_null($aInput))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			$sDate = date('YmdHis');
			$iLetterNum = 0;
			DB::start_transaction();
			if ($bUpdate)
			{
				foreach ($aQuery as $aQ)
				{
					$iQqNO = $aQ['qqNO'];
					$aA = $aInput[$iQqNO];
					$query = DB::update('QuestAns_Table');
					$query->and_where('qbID','=',$aQuest['qbID']);
					$query->and_where('stID','=',$aStudent['stID']);
					$query->and_where('qqNO','=',$iQqNO);
					$aUpdate = null;
					switch ($aQ['qqStyle'])
					{
						case 0:
						case 1:
							$aUpdate['qaText'] = '';
							$aChoice = explode('|', $aA['select']);
							for ($i = 1; $i <= 50; $i++)
							{
								$iV = (array_search($i,$aChoice) !== false)? 1:0;
								$aUpdate['qaChoice'.$i] = $iV;
							}
						break;
						case 2:
							$iLetterNum += mb_strlen($aA['text'],CL_ENC);
							$aUpdate['qaText'] = $aA['text'];
							for ($i = 1; $i <= 50; $i++)
							{
								$aUpdate['qaChoice'.$i] = 0;
							}
						break;
					}
					$query->set($aUpdate);
					$result = $query->execute();
				}
				$aUpdate = array(
					'qpDate'=>$sDate,
					'qpLetterNum'=>$iLetterNum,
					'qpstName'=>$aStudent['stName'],
					'qpstKana'=>$aStudent['stKana'],
					'qpstNO'=>$aStudent['stNO'],
					'qpstClass'=>$aStudent['stClass'],
					'cmKCode'=>$aStudent['cmKCode'],
					'dmNO'=>$aStudent['dmNO'],
				);
				$query = DB::update('QuestPut_Table');
				$query->and_where('qbID','=',$aQuest['qbID']);
				$query->and_where('stID','=',$aStudent['stID']);
				$query->set($aUpdate);
				$result = $query->execute();
			}
			else
			{
				$aColumn = array('qbID','qqNO','stID','qaDate','qaText');
				for ($i = 1; $i <= 50; $i++)
				{
					$aColumn[] = 'qaChoice'.$i;
				}
				$query = DB::insert('QuestAns_Table',$aColumn);
				foreach ($aQuery as $aQ)
				{
					$iQqNO = $aQ['qqNO'];
					$aA = $aInput[$iQqNO];
					$aInsert = array($aQuest['qbID'],$iQqNO,$aStudent['stID'],$sDate);

					switch ($aQ['qqStyle'])
					{
						case 0:
						case 1:
							$aInsert[] = '';
							$aChoice = explode('|', $aA['select']);
							for ($i = 1; $i <= 50; $i++)
							{
								$iV = (array_search($i,$aChoice) !== false)? 1:0;
								$aInsert[] = $iV;
							}
						break;
						case 2:
							$iLetterNum += mb_strlen($aA['text'],CL_ENC);
							$aInsert[] = $aA['text'];
							for ($i = 1; $i <= 50; $i++)
							{
								$aInsert[] = 0;
							}
						break;
					}
					$query->values($aInsert);
				}
				$result = $query->execute();

				$aInsert = array(
					'qbID'=>$aQuest['qbID'],
					'stID'=>$aStudent['stID'],
					'qpDate'=>$sDate,
					'qpLetterNum'=>$iLetterNum,
					'qpstName'=>$aStudent['stName'],
					'qpstKana'=>$aStudent['stKana'],
					'qpstNO'=>$aStudent['stNO'],
					'qpstClass'=>$aStudent['stClass'],
					'cmKCode'=>$aStudent['cmKCode'],
					'dmNO'=>$aStudent['dmNO'],
				);
				$query = DB::insert('QuestPut_Table');
				$query->set($aInsert);
				$result = $query->execute();
			}
			DB::commit_transaction();
			// クエリの結果を返す
			return $result;
		}
		catch (Exception $e)
		{
			// 未決のトランザクションクエリをロールバックする
			DB::rollback_transaction();
			throw $e;
		}
	}

	public static function updateQuestPut($aUpdate = null,$aAndWhere = null,$aOrWhere = null)
	{
		if (is_null($aUpdate))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			$query = DB::update('QuestPut_Table');
			if (!is_null($aAndWhere))
			{
				foreach ($aAndWhere as $aW)
				{
					$query->and_where($aW[0],$aW[1],$aW[2]);
				}
			}
			if (!is_null($aOrWhere))
			{
				foreach ($aOrWhere as $aW)
				{
					$query->or_where($aW[0],$aW[1],$aW[2]);
				}
			}
			$query->set($aUpdate);
			$result = $query->execute();
			DB::commit_transaction();
			// クエリの結果を返す
			return $result;
		}
		catch (Exception $e)
		{
			// 未決のトランザクションクエリをロールバックする
			DB::rollback_transaction();
			throw $e;
		}
	}

	public static function updateQuestPick($iPick = null,$sQbID = null,$iQqNO = null,$sStID = null)
	{
		if (is_null($iPick) || is_null($sQbID) || is_null($iQqNO) || is_null($sStID))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			$query = DB::update('QuestAns_Table')->set(array('qaPick'=>$iPick));
			$query->and_where('qbID','=',$sQbID);
			$query->and_where('qqNO','=',$iQqNO);
			$query->and_where('stID','=',$sStID);
			$result = $query->execute();

			$query = DB::update('QuestPut_Table');
			$query->set(
				array(
					'qpPickUp'=>DB::expr('(select count(*) from QuestAns_Table as qa where qa.qbID="'.$sQbID.'" AND qa.stID="'.$sStID.'" AND qa.qaPick=1)'),
					'qpPickDown'=>DB::expr('(select count(*) from QuestAns_Table as qa where qa.qbID="'.$sQbID.'" AND qa.stID="'.$sStID.'" AND qa.qaPick=-1)'),
				)
			);
			$query->and_where('qbID','=',$sQbID);
			$query->and_where('stID','=',$sStID);
			$result = $query->execute();

			DB::commit_transaction();
			// クエリの結果を返す
			return $result;
		}
		catch (Exception $e)
		{
			// 未決のトランザクションクエリをロールバックする
			DB::rollback_transaction();
			throw $e;
		}
	}


	public static function getQuestBent($aAndWhere = null,$aOrWhere = null,$aSort = null)
	{
		// $query = DB::select_array()->from('QuestBent_View');

		$query = DB::select_array(array('qb.*','qa.qaPick','qa.qaDate','qp.qpstName','gt.gtName','tt.ttName'))
			->from(array('QuestBent_Table','qb'))
			->join(array('QuestAns_Table','qa'),'LEFT')
			->on('qb.qbID','=','qa.qbID')
			->on('qb.qqNO','=','qa.qqNO')
			->on('qb.stID','=','qa.stID')
			->join(array('QuestPut_Table','qp'),'LEFT')
			->on('qb.qbID','=','qp.qbID')
			->on('qb.stID','=','qp.stID')
			->join(array('Guest_Table','gt'),'LEFT')
			->on('qb.stID','=','gt.gtID')
			->join(array('Teacher_Table','tt'),'LEFT')
			->on('qb.stID','=','tt.ttID')
		;
		if (!is_null($aAndWhere))
		{
			foreach ($aAndWhere as $aW)
			{
				$query->where($aW[0],$aW[1],$aW[2]);
			}
		}
		if (!is_null($aOrWhere))
		{
			foreach ($aOrWhere as $aW)
			{
				$query->or_where($aW[0],$aW[1],$aW[2]);
			}
		}
		if (!is_null($aSort))
		{
			foreach ($aSort as $sC => $sS)
			{
				$query->order_by($sC,$sS);
			}
		}
		$result = $query->execute();
		return $result;
	}


	public static function setGuestQuestPut($aQuest = null,$aQuery = null,$aGuest = null,$aInput = null,$bUpdate = false)
	{
		if (is_null($aQuest) || is_null($aGuest) || is_null($aInput))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			$sDate = date('YmdHis');
			$iLetterNum = 0;
			DB::start_transaction();
			if ($bUpdate)
			{
				foreach ($aQuery as $aQ)
				{
					$iQqNO = $aQ['qqNO'];
					$aA = $aInput[$iQqNO];
					$query = DB::update('QuestAns_Table');
					$query->and_where('qbID','=',$aQuest['qbID']);
					$query->and_where('stID','=',$aGuest['gtID']);
					$query->and_where('qqNO','=',$iQqNO);
					$aUpdate = null;
					switch ($aQ['qqStyle'])
					{
						case 0:
						case 1:
							$aUpdate['qaText'] = '';
							$aChoice = explode('|', $aA['select']);
							for ($i = 1; $i <= 50; $i++)
							{
								$iV = (array_search($i,$aChoice) !== false)? 1:0;
								$aUpdate['qaChoice'.$i] = $iV;
							}
						break;
						case 2:
							$iLetterNum += mb_strlen($aA['text'],CL_ENC);
							$aUpdate['qaText'] = $aA['text'];
							for ($i = 1; $i <= 50; $i++)
							{
								$aUpdate['qaChoice'.$i] = 0;
							}
						break;
					}
					$query->set($aUpdate);
					$result = $query->execute();

					$aUpdate = array(
						'qpDate'      => $sDate,
						'qpLetterNum' => $iLetterNum,
						'qpstName'    => $aGuest['gtName'],
					);
					$query = DB::update('QuestPut_Table');
					$query->and_where('qbID','=',$aQuest['qbID']);
					$query->and_where('stID','=',$aGuest['gtID']);
					$query->set($aUpdate);
					$result = $query->execute();
				}
			}
			else
			{
				$aColumn = array('qbID','qqNO','stID','qaDate','qaText');
				for ($i = 1; $i <= 50; $i++)
				{
					$aColumn[] = 'qaChoice'.$i;
				}
				$query = DB::insert('QuestAns_Table',$aColumn);
				foreach ($aQuery as $aQ)
				{
					$iQqNO = $aQ['qqNO'];
					$aA = $aInput[$iQqNO];
					$aInsert = array($aQuest['qbID'],$iQqNO,$aGuest['gtID'],$sDate);

					switch ($aQ['qqStyle'])
					{
						case 0:
						case 1:
							$aInsert[] = '';
							$aChoice = explode('|', $aA['select']);
							for ($i = 1; $i <= 50; $i++)
							{
								$iV = (array_search($i,$aChoice) !== false)? 1:0;
								$aInsert[] = $iV;
							}
						break;
						case 2:
							$iLetterNum += mb_strlen($aA['text'],CL_ENC);
							$aInsert[] = $aA['text'];
							for ($i = 1; $i <= 50; $i++)
							{
								$aInsert[] = 0;
							}
						break;
					}
					$query->values($aInsert);
				}
				$result = $query->execute();

				$aInsert = array(
					'qbID'        => $aQuest['qbID'],
					'stID'        => $aGuest['gtID'],
					'qpDate'      => $sDate,
					'qpLetterNum' => $iLetterNum,
					'qpstName'    => $aGuest['gtName'],
				);
				$query = DB::insert('QuestPut_Table');
				$query->set($aInsert);
				$result = $query->execute();
			}
			DB::commit_transaction();
			// クエリの結果を返す
			return $result;
		}
		catch (Exception $e)
		{
			// 未決のトランザクションクエリをロールバックする
			DB::rollback_transaction();
			throw $e;
		}
	}

	public static function setTeacherQuestPut($aQuest = null,$aQuery = null,$aTeacher = null,$aInput = null,$bUpdate = false)
	{
		if (is_null($aQuest) || is_null($aTeacher) || is_null($aInput))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			$sDate = date('YmdHis');
			$iLetterNum = 0;
			DB::start_transaction();
			if ($bUpdate)
			{
				foreach ($aQuery as $aQ)
				{
					$iQqNO = $aQ['qqNO'];
					$aA = $aInput[$iQqNO];
					$query = DB::update('QuestAns_Table');
					$query->and_where('qbID','=',$aQuest['qbID']);
					$query->and_where('stID','=',$aTeacher['ttID']);
					$query->and_where('qqNO','=',$iQqNO);
					$aUpdate = null;
					switch ($aQ['qqStyle'])
					{
						case 0:
						case 1:
							$aUpdate['qaText'] = '';
							$aChoice = explode('|', $aA['select']);
							for ($i = 1; $i <= 50; $i++)
							{
								$iV = (array_search($i,$aChoice) !== false)? 1:0;
								$aUpdate['qaChoice'.$i] = $iV;
							}
						break;
						case 2:
							$iLetterNum += mb_strlen($aA['text'],CL_ENC);
							$aUpdate['qaText'] = $aA['text'];
							for ($i = 1; $i <= 50; $i++)
							{
								$aUpdate['qaChoice'.$i] = 0;
							}
						break;
					}
					$query->set($aUpdate);
					$result = $query->execute();

					$aUpdate = array(
						'qpDate'=>$sDate,
						'qpLetterNum'=>$iLetterNum,
						'qpstName'=>$aTeacher['ttName'],
						'qpstKana'=>$aTeacher['ttKana'],
						'qpstNO'=>'',
						'qpstClass'=>$aTeacher['ttDept'].$aTeacher['ttSubject'],
						'cmKCode'=>$aTeacher['cmKCode'],
						'dmNO'=>$aTeacher['dmNO'],
					);
					$query = DB::update('QuestPut_Table');
					$query->and_where('qbID','=',$aQuest['qbID']);
					$query->and_where('stID','=',$aTeacher['ttID']);
					$query->set($aUpdate);
					$result = $query->execute();
				}
			}
			else
			{
				$aColumn = array('qbID','qqNO','stID','qaDate','qaText');
				for ($i = 1; $i <= 50; $i++)
				{
					$aColumn[] = 'qaChoice'.$i;
				}
				$query = DB::insert('QuestAns_Table',$aColumn);
				foreach ($aQuery as $aQ)
				{
					$iQqNO = $aQ['qqNO'];
					$aA = $aInput[$iQqNO];
					$aInsert = array($aQuest['qbID'],$iQqNO,$aTeacher['ttID'],$sDate);

					switch ($aQ['qqStyle'])
					{
						case 0:
						case 1:
							$aInsert[] = '';
							$aChoice = explode('|', $aA['select']);
							for ($i = 1; $i <= 50; $i++)
							{
								$iV = (array_search($i,$aChoice) !== false)? 1:0;
								$aInsert[] = $iV;
							}
						break;
						case 2:
							$iLetterNum += mb_strlen($aA['text'],CL_ENC);
							$aInsert[] = $aA['text'];
							for ($i = 1; $i <= 50; $i++)
							{
								$aInsert[] = 0;
							}
						break;
					}
					$query->values($aInsert);
				}
				$result = $query->execute();

				$aInsert = array(
					'qbID'=>$aQuest['qbID'],
					'stID'=>$aTeacher['ttID'],
					'qpDate'=>$sDate,
					'qpLetterNum'=>$iLetterNum,
					'qpstName'=>$aTeacher['ttName'],
					'qpstKana'=>$aTeacher['ttKana'],
					'qpstNO'=>'',
					'qpstClass'=>$aTeacher['ttDept'].$aTeacher['ttSubject'],
					'cmKCode'=>$aTeacher['cmKCode'],
					'dmNO'=>$aTeacher['dmNO'],
				);
				$query = DB::insert('QuestPut_Table');
				$query->set($aInsert);
				$result = $query->execute();
			}
			DB::commit_transaction();
			// クエリの結果を返す
			return $result;
		}
		catch (Exception $e)
		{
			// 未決のトランザクションクエリをロールバックする
			DB::rollback_transaction();
			throw $e;
		}
	}


	public static function copyQuest($aQuest = null, $aSelClass = null, &$aQbIDs = null)
	{
		if (is_null($aQuest) || is_null($aSelClass))
		{
			throw new Exception('処理に必要な情報がありません');
		}

		$sNow = date('YmdHis');

		# 元画像パス
		$sBasePath = CL_UPPATH.DS.$aQuest['qbID'];

		# ベースデータ生成
		$aQBase = $aQuest;
		$aQBase['qbSort'] = 0;
		$aQBase['qbPublic'] = 0;
		$aQBase['qbPublicDate'] = CL_DATETIME_DEFAULT;
		$aQBase['qbCloseDate'] = CL_DATETIME_DEFAULT;
		$aQBase['qbComment'] = '';
		$aQBase['qbDate'] = $sNow;

		unset($aQBase['qpNum']);
		unset($aQBase['qpGNum']);
		unset($aQBase['qpTNum']);
		unset($aQBase['scNum']);

		# クエリデータ生成
		$result = self::getQuestQuery(array(array('qbID','=',$aQuest['qbID'])));
		$aQQuery = null;
		if (count($result))
		{
			foreach ($result as $aQQ)
			{
				$aQQuery[$aQQ['qqNO']] = $aQQ;

				unset($aQQuery[$aQQ['qqNO']]['no']);
				$aQQuery[$aQQ['qqNO']]['qqDate'] = $sNow;
			}
		}

		try
		{
			DB::start_transaction();

			foreach ($aSelClass as $sCtID => $aC)
			{
				$sQbID = self::getQuestID();
				$sCopyPath = CL_UPPATH.DS.$sQbID;

				$aQbIDs[$sCtID][$aQuest['qbID']] = $sQbID;

				$aQBase['qbID'] = $sQbID;
				$aQBase['ctID'] = $sCtID;
				$aQBase['qbSort'] = self::getQuestSort($sCtID);

				$result = DB::insert('QuestBase_Table')
					->set($aQBase)
					->execute()
				;

				if (!is_null($aQQuery))
				{
					foreach ($aQQuery as $qNO => $aQQ)
					{
						$aQQ['qbID'] = $sQbID;
						$result = DB::insert('QuestQuery_Table')
							->set($aQQ)
							->execute()
						;
					}
				}

				if (file_exists($sBasePath))
				{
					$res = system('cp -rp '.$sBasePath.' '.$sCopyPath);
					if ($res === false)
					{
						throw new Exception('アンケートのコピーに失敗しました。再度実行してください。');
					}
				}
			}

			DB::commit_transaction();

			return count($aSelClass);
		}
		catch (Exception $e)
		{
			// 未決のトランザクションクエリをロールバックする
			DB::rollback_transaction();
			throw $e;
		}
	}





	private static function getQuestID()
	{
		try
		{
			while (true):
				$sQbID = 'qb'.Str::random('numeric',8);
				$result = DB::select()->from('QuestBase_Table')->where('qbID',$sQbID)->execute()->as_array();
				if (empty($result)):
					break;
				endif;
			endwhile;
			return $sQbID;
		}
		catch (Exception $e)
		{
			throw $e;
		}
	}
	private static function getQueryNO($sID = null)
	{
		try
		{
			$result = DB::select(DB::expr('MAX(qqNO) AS qNO'))->from('QuestQuery_Table')->where('qbID',$sID)->execute();
			if (count($result))
			{
				$aRes = $result->current();
				return ($aRes['qNO'] + 1);
			}
			return 1;
		}
		catch (Exception $e)
		{
			throw $e;
		}
	}
	private static function getQuestSort($sCtID = null)
	{
		$iSort = 1;
		$result = DB::select(DB::expr('MAX(qbSort) AS qbMax'))->from('QuestBase_Table')->where('ctID',$sCtID)->execute();
		if (count($result))
		{
			$aRes = $result->current();
			$iSort = $aRes['qbMax'] + 1;
		}
		return $iSort;
	}
	private static function setQuerySort($sID = null, $iSort = null, $bDelete = false)
	{
		try
		{
			$query = DB::update('QuestQuery_Table')->where('qbID',$sID);
			if ($bDelete)
			{
				$query->set(array('qqSort'=>DB::expr('qqSort-1')))->and_where('qqSort','>',$iSort);
			}
			else
			{
				$query->set(array('qqSort'=>DB::expr('qqSort+1')))->and_where('qqSort','>=',$iSort);
			}
			$result = $query->execute();
		}
		catch (Exception $e)
		{
			throw $e;
		}
	}


}
