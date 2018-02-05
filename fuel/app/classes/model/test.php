<?php
class Model_Test extends \Model
{
	public static function getTestBaseFromClass($sCtID = null,$aAndWhere = null,$aOrWhere = null,$aSort = null)
	{
/*
		$query = DB::select_array()->from('TestBase_View');
		$query->where('ctID',$sCtID);
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
*/
		$aPWhere = array(array('tb.tbID','=',DB::expr('tp.tbID')));
		$subquery = DB::select(DB::expr('count(tp.no)'))->from(array('TestPut_Table','tp'))
			->where($aPWhere)
			->compile();
		$sPSub = '('.$subquery.') AS `tpNum`';

		$aSWhere = array(array('tb.tbID','=',DB::expr('tp.tbID')));
		$subquery = DB::select(DB::expr('sum(tp.tpScore)'))->from(array('TestPut_Table','tp'))
			->where($aSWhere)
			->compile();
		$sSSub = '('.$subquery.') AS `tpScore`';

		$aQWhere = array(array('tb.tbID','=',DB::expr('tp.tbID')),array('tp.tpQualify','=',1));
		$subquery = DB::select(DB::expr('count(tp.tpQualify)'))->from(array('TestPut_Table','tp'))
			->where($aQWhere)
			->compile();
		$sQSub = '('.$subquery.') AS `tpQualify`';

		$aNWhere = array(array('tb.ctID','=',DB::expr('sp.ctID')),array('sp.spAuth','=',1));
		$subquery = DB::select(DB::expr('count(sp.stID)'))->from(array('StudentPosition_Table','sp'))
			->where($aNWhere)
			->compile();
		$sNSub = '('.$subquery.') AS `scNum`';

		$query = DB::select_array(array('tb.*',DB::expr($sNSub),DB::expr($sSSub),DB::expr($sQSub),DB::expr($sPSub)))
			->from(array('TestBase_Table','tb'))
			->where('tb.ctID','=',$sCtID)
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

	public static function getTestBaseFromID($sID = null,$aAndWhere = null)
	{
/*
		$query = DB::select_array()->from('TestBase_View');
		$query->where('tbID',$sID);
		if (!is_null($aAndWhere))
		{
			foreach ($aAndWhere as $aW)
			{
				$query->where($aW[0],$aW[1],$aW[2]);
			}
		}
		$result = $query->execute();
		return $result;
*/
		$aPWhere = array(array('tb.tbID','=',DB::expr('tp.tbID')));
		$subquery = DB::select(DB::expr('count(tp.no)'))->from(array('TestPut_Table','tp'))
			->where($aPWhere)
			->compile();
		$sPSub = '('.$subquery.') AS `tpNum`';

		$aSWhere = array(array('tb.tbID','=',DB::expr('tp.tbID')));
		$subquery = DB::select(DB::expr('sum(tp.tpScore)'))->from(array('TestPut_Table','tp'))
			->where($aSWhere)
			->compile();
		$sSSub = '('.$subquery.') AS `tpScore`';

		$aQWhere = array(array('tb.tbID','=',DB::expr('tp.tbID')),array('tp.tpQualify','=',1));
		$subquery = DB::select(DB::expr('count(tp.tpQualify)'))->from(array('TestPut_Table','tp'))
			->where($aQWhere)
			->compile();
		$sQSub = '('.$subquery.') AS `tpQualify`';

		$aNWhere = array(array('tb.ctID','=',DB::expr('sp.ctID')),array('sp.spAuth','=',1));
		$subquery = DB::select(DB::expr('count(sp.stID)'))->from(array('StudentPosition_Table','sp'))
			->where($aNWhere)
			->compile();
		$sNSub = '('.$subquery.') AS `scNum`';

		$query = DB::select_array(array('tb.*',DB::expr($sNSub),DB::expr($sSSub),DB::expr($sQSub),DB::expr($sPSub)))
			->from(array('TestBase_Table','tb'))
			->where('tb.tbID','=',$sID)
		;
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
	public static function insertTest($aInsert = null)
	{
		if (is_null($aInsert))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			$sQbID = self::getTestID();
			$aInsert['tbID'] = $sQbID;
			$aInsert['tbSort'] = self::getTestSort($aInsert['ctID']);
			$result = DB::insert('TestBase_Table')->set($aInsert)->execute();
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
	public static function updateTest($aUpdate = null,$aAndWhere = null,$aOrWhere = null)
	{
		if (is_null($aUpdate))
		{
			throw new Exception('処理に必要な情報がありません');
		}
			try
		{
			DB::start_transaction();
			$query = DB::update('TestBase_Table');
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
	public static function deleteTest($sID = null,$aActive = null)
	{
		if (is_null($sID) || is_null($aActive))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			$result = DB::delete('TestBent_Table')->where('tbID',$sID)->execute();
			$result = DB::delete('TestAns_Table')->where('tbID',$sID)->execute();
			$result = DB::delete('TestPut_Table')->where('tbID',$sID)->execute();
			$result = DB::delete('TestQuery_Table')->where('tbID',$sID)->execute();
			$result = DB::delete('TestBase_Table')->where('tbID',$sID)->execute();

			$query = DB::update('TestBase_Table');
			$query->and_where('ctID',$aActive['ctID']);
			$query->and_where('tbSort','>',$aActive['tbSort']);
			$query->set(array('tbSort'=>DB::expr('tbSort - 1')));
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

	public static function insertTestFromCSV($aBase = null,$aQuery = null,$sCtID = null)
	{
		if (is_null($aBase) || is_null($aQuery) || is_null($sCtID))
		{
			throw new Exception('処理に必要な情報がありません');
		}

		$sDate = date('YmdHis');

		try
		{
			DB::start_transaction();
			$sTbID = self::getTestID();
			$aBase['tbID'] = $sTbID;
			$aBase['tbSort'] = self::getTestSort($sCtID);
			$aBase['ctID'] = $sCtID;
			$aBase['tbDate'] = $sDate;

			$result = DB::insert('TestBase_Table')->set($aBase)->execute();
			foreach ($aQuery as $iQN => $aQ)
			{
				$aQ['tbID'] = $sTbID;
				$aQ['tqNO'] = $iQN;
				$aQ['tqSort'] = $iQN;
				$aQ['tqDate'] = $sDate;
				$result = DB::insert('TestQuery_Table')->set($aQ)->execute();
			}
			DB::commit_transaction();
			// クエリの結果を返す
			return $sTbID;
		}
		catch (Exception $e)
		{
			// 未決のトランザクションクエリをロールバックする
			DB::rollback_transaction();
			throw $e;
		}
	}

	public static function sortTest($aTest = null,$sSort = null)
	{
		if (is_null($aTest) || is_null($sSort))
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
			$query = DB::update('TestBase_Table');
			$query->and_where('ctID',$aTest['ctID']);
			$query->and_where('tbSort',$aTest['tbSort']+$iWhere);
			$query->set(array('tbSort'=>DB::expr('tbSort'.$iUp1)));
			$result = $query->execute();
			$query = DB::update('TestBase_Table');
			$query->and_where('tbID',$aTest['tbID']);
			$query->set(array('tbSort'=>DB::expr('tbSort'.$iUp2)));
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

	public static function insertTestQuery($aInsert = null)
	{
		if (is_null($aInsert))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			$iTqNO = self::getQueryNO($aInsert['tbID']);
			self::setQuerySort($aInsert['tbID'],$aInsert['tqSort']);
			$aInsert['tqNO'] = $iTqNO;
			$result = DB::insert('TestQuery_Table')->set($aInsert)->execute();
			$result = DB::update('TestBase_Table')
				->set(array('tbNum'=>DB::expr('tbNum+1')))
				->set(array('tbTotal'=>DB::expr('(SELECT sum(tqScore) FROM TestQuery_Table WHERE tbID="'.$aInsert['tbID'].'")')))
				->where('tbID',$aInsert['tbID'])
				->execute();
			DB::commit_transaction();
			// クエリの結果を返す
			return $iTqNO;
		}
		catch (Exception $e)
		{
			// 未決のトランザクションクエリをロールバックする
			DB::rollback_transaction();
			throw $e;
		}
	}

	public static function updateTestQuery($aUpdate = null,$aAndWhere = null,$aOrWhere = null,$sID = null)
	{
		if (is_null($aUpdate))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			$query = DB::update('TestQuery_Table');
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

			if (!is_null($sID))
			{
				$result = DB::update('TestBase_Table')
					->set(array('tbTotal'=>DB::expr('(SELECT sum(tqScore) FROM TestQuery_Table WHERE tbID="'.$sID.'")')))
					->where('tbID',$sID)
					->execute();
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

	public static function deleteTestQuery($sID = null, $iNO = null,$aActive = null)
	{
		if (is_null($sID) || is_null($iNO) || is_null($aActive))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			$result = DB::delete('TestQuery_Table')->where('tbID',$sID)->and_where('tqNO',$iNO)->execute();
			self::setQuerySort($sID,$aActive['tqSort'],true);
			$result = DB::update('TestBase_Table')
				->set(array('tbNum'=>DB::expr('tbNum-1')))
				->set(array('tbTotal'=>DB::expr('(SELECT sum(tqScore) FROM TestQuery_Table WHERE tbID="'.$sID.'")')))
				->where('tbID',$sID)
				->execute();
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

	public static function sortTestQuery($aQuery = null,$sSort = null)
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
			$query = DB::update('TestQuery_Table');
			$query->and_where('tbID',$aQuery['tbID']);
			$query->and_where('tqSort',$aQuery['tqSort']+$iWhere);
			$query->set(array('tqSort'=>DB::expr('tqSort'.$iUp1)));
			$result = $query->execute();
			$query = DB::update('TestQuery_Table');
			$query->and_where('tbID',$aQuery['tbID']);
			$query->and_where('tqNO',$aQuery['tqNO']);
			$query->set(array('tqSort'=>DB::expr('tqSort'.$iUp2)));
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

	public static function insertTestQueries($aInsert = null)
	{
		if (is_null($aInsert))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			$aInSet = null;
			DB::start_transaction();

			foreach ($aInsert as $iDqNO => $aI)
			{
				$iTqNO = self::getQueryNO($aI['tbID']);
				self::setQuerySort($aI['tbID'],$aI['tqSort']);
				$aI['tqNO'] = $iTqNO;
				$result = DB::insert('TestQuery_Table')->set($aI)->execute();
				$result = DB::update('TestBase_Table')
					->set(array('tbNum'=>DB::expr('tbNum+1')))
					->set(array('tbTotal'=>DB::expr('(SELECT sum(tqScore) FROM TestQuery_Table WHERE tbID="'.$aI['tbID'].'")')))
					->where('tbID',$aI['tbID'])
					->execute();

				$aInsSet[$iDqNO] = $iTqNO;
			}

			DB::commit_transaction();
			// クエリの結果を返す
			return $aInsSet;
		}
		catch (Exception $e)
		{
			// 未決のトランザクションクエリをロールバックする
			DB::rollback_transaction();
			throw $e;
		}
	}

	public static function deleteTestPut($sID = null)
	{
		if (is_null($sID))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			$result = DB::delete('TestBent_Table')->where('tbID',$sID)->execute();
			$result = DB::delete('TestAns_Table')->where('tbID',$sID)->execute();
			$result = DB::delete('TestPut_Table')->where('tbID',$sID)->execute();
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

	public static function setTestBent($aTest = null)
	{
		if (is_null($aTest))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		$result = DB::select_array()->from('TestQuery_Table')->and_where('tbID','=',$aTest['tbID'])->execute();
		if (!count($result))
		{
			throw new Exception('設問がありません。先に設問を作成してください。');
		}
		$aQqs = $result->as_array();
		$sDate = date("YmdHis");

		try
		{
			DB::start_transaction();
			$result = DB::delete('TestBent_Table')->and_where('tbID','=',$aTest['tbID'])->execute();
			$aColmuns = array('tbID','tqNO','tbNO','tbText','tbNum','tbAll','tbDate','tbTotal');
			$insertQuery = DB::insert('TestBent_Table',$aColmuns);
			$aInsert = null;

			foreach ($aQqs as $aQq)
			{
				if ($aQq['tqStyle'] == 2)
				{
					$query = DB::select_array(array('taText',DB::expr('COUNT(no) AS tbNum'),DB::expr('MIN(taDate) AS tbDate')));
					$query->from('TestAns_Table');
					$query->and_where('tbID','=',$aQq['tbID'])->and_where('tqNO','=',$aQq['tqNO']);
					$query->group_by('taText')->order_by('taDate','asc')->order_by('taText','asc');
					$result = $query->execute();
					if (count($result))
					{
						$aQas = $result->as_array();
						$iCnt = 1;
						$aInsert = null;

						foreach ($aQas as $aQa)
						{
							$iQbNum = (isset($aQa["tbNum"]))? (int)$aQa["tbNum"]:1;
							$aInsert = array(
								$aQq['tbID'],
								$aQq['tqNO'],
								$iCnt,
								$aQa['taText'],
								$iQbNum,
								$aTest['tpNum'],
								$sDate,
								$aTest['tpNum'],
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
					for($i = 1; $i <= $aQq["tqChoiceNum"]; $i++)
					{
						$aQa = null;
						$query = DB::select_array(array(DB::expr('COUNT(no) AS tbNum'),DB::expr('MIN(taDate) AS tbDate')));
						$query->from('TestAns_Table');
						$query->and_where('tbID','=',$aQq['tbID'])->and_where('tqNO','=',$aQq['tqNO'])->and_where('taChoice'.$i,'=',1);
						$query->order_by('taDate','asc');
						$result = $query->execute();
						$aQa['tbNum'] = 0;
						if (count($result))
						{
							$aQa = $result->current();
						}
						$iQbTotal += $aQa['tbNum'];
						$aInsert[$i] = array(
							$aQq['tbID'],
							$aQq['tqNO'],
							$i,
							'',
							$aQa['tbNum'],
							$aTest['tpNum'],
							$sDate,
						);
					}
					$aQa = null;
					$query = DB::select_array(array(DB::expr('COUNT(no) AS tbNum'),DB::expr('MIN(taDate) AS tbDate')));
					$query->from('TestAns_Table');
					$query->and_where('tbID','=',$aQq['tbID'])->and_where('tqNO','=',$aQq['tqNO']);
					for ($i = 1; $i <= 50; $i++)
					{
						$query->and_where('taChoice'.$i,'=',0);
					}
					$query->order_by('taDate','asc');
					$result = $query->execute();
					$aQa['tbNum'] = 0;
					if (count($result))
					{
						$aQa = $result->current();
					}
					$iQbTotal += $aQa['tbNum'];
					$aInsert[$i] = array(
						$aQq['tbID'],
						$aQq['tqNO'],
						0,
						'',
						$aQa['tbNum'],
						$aTest['tpNum'],
						$sDate,
					);
					foreach ($aInsert as $aI)
					{
						$aI['tbTotal'] = $iQbTotal;
						$insertQuery->values($aI);
					}
				}
			}
			if (!is_null($aInsert))
			{
				$result = $insertQuery->execute();
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
	public static function getTestPut($aAndWhere = null,$aOrWhere = null,$aSort = null)
	{
/*
		$query = DB::select_array()->from('TestPut_View');
*/
		$query = DB::select_array(
			array(
				'tb.ctID','tp.*',
			)
		)
			->from(array('TestPut_Table','tp'))
			->join(array('TestBase_Table','tb'),'LEFT')
			->on('tp.tbID','=','tb.tbID')
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
	public static function getTestQuery($aAndWhere = null,$aOrWhere = null,$aSort = null)
	{
		$query = DB::select_array()->from('TestQuery_Table');
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
	public static function getTestAns($aAndWhere = null,$aOrWhere = null,$aSort = null)
	{
/*
		$query = DB::select_array()->from('TestAns_View');
*/
		$query = DB::select_array(
				array(
						'ta.*','tq.*','tp.tpstName'
				)
		)
			->from(array('TestAns_Table','ta'))
			->join(array('TestQuery_Table','tq'),'LEFT')
			->on('ta.tbID','=','tq.tbID')
			->on('ta.tqNO','=','tq.tqNO')
			->join(array('TestPut_Table','tp'),'LEFT')
			->on('ta.tbID','=','tp.tbID')
			->on('ta.stID','=','tp.stID')
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
		$query->order_by('tq.tqSort','asc');
		$result = $query->execute();
		return $result;
	}
	public static function setTestPut($aTest = null,$aQuery = null,$aStudent = null,$aInput = null,$iTime = null,$bUpdate = false)
	{
		if (is_null($aTest) || is_null($aStudent) || is_null($aInput))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			$sDate = date('YmdHis');
			$iLetterNum = 0;
			$iScore = 0;
			DB::start_transaction();
			if ($bUpdate)
			{
				foreach ($aQuery as $aQ)
				{
					$iTqNO = $aQ['tqNO'];
					$aA = $aInput[$iTqNO];
					$query = DB::update('TestAns_Table');
					$query->and_where('tbID','=',$aTest['tbID']);
					$query->and_where('stID','=',$aStudent['stID']);
					$query->and_where('tqNO','=',$iTqNO);
					$aUpdate = null;
					switch ($aQ['tqStyle'])
					{
						case 0:
						case 1:
							$aUpdate['taText'] = '';
							$aChoice = explode('|', $aA['select']);
							sort($aChoice,SORT_NUMERIC);
							$aRight = explode('|', $aQ['tqRight1']);
							sort($aRight,SORT_NUMERIC);
							for ($i = 1; $i <= 50; $i++)
							{
								$iV = (array_search($i,$aChoice) !== false)? 1:0;
								$aUpdate['taChoice'.$i] = $iV;
							}
							if (implode('|',$aChoice) == implode('|',$aRight)) {
								$aUpdate['taRight'] = 1;
								$iScore += $aQ['tqScore'];
							}
							else
							{
								$aUpdate['taRight'] = 0;
							}
						break;
						case 2:
							$iLetterNum += mb_strlen($aA['text'],CL_ENC);
							$aUpdate['taText'] = $aA['text'];
							for ($i = 1; $i <= 50; $i++)
							{
								$aUpdate['taChoice'.$i] = 0;
							}
							if (
								$aA['text'] != '' &&
								(
									$aA['text'] == $aQ['tqRight1'] ||
									$aA['text'] == $aQ['tqRight2'] ||
									$aA['text'] == $aQ['tqRight3'] ||
									$aA['text'] == $aQ['tqRight4'] ||
									$aA['text'] == $aQ['tqRight5']
								)
							)
							{
								$aUpdate['taRight'] = 1;
								$iScore += $aQ['tqScore'];
							}
							else
							{
								$aUpdate['taRight'] = 0;
							}
						break;
					}
					$query->set($aUpdate);
					$result = $query->execute();
				}

				$iQualify = ($iScore >= $aTest['tbQualifyScore'])? 1:0;
				$aUpdate = array(
					'tpDate'=>$sDate,
					'tpLetterNum'=>$iLetterNum,
					'tpScore'=>$iScore,
					'tpQualify'=>$iQualify,
					'tpTime'=>$iTime,
					'tpstName'=>$aStudent['stName'],
					'tpstKana'=>$aStudent['stKana'],
					'tpstNO'=>$aStudent['stNO'],
					'tpstClass'=>$aStudent['stClass'],
					'cmKCode'=>$aStudent['cmKCode'],
					'dmNO'=>$aStudent['dmNO'],
				);
				$query = DB::update('TestPut_Table');
				$query->and_where('tbID','=',$aTest['tbID']);
				$query->and_where('stID','=',$aStudent['stID']);
				$query->set($aUpdate);
				$result = $query->execute();
			}
			else
			{
				$aColumn = array('tbID','tqNO','stID','taDate','taText');
				for ($i = 1; $i <= 50; $i++)
				{
					$aColumn[] = 'taChoice'.$i;
				}
				$aColumn[] = 'taRight';
				$query = DB::insert('TestAns_Table',$aColumn);
				foreach ($aQuery as $aQ)
				{
					$iTqNO = $aQ['tqNO'];
					$aA = $aInput[$iTqNO];
					$aInsert = array($aTest['tbID'],$iTqNO,$aStudent['stID'],$sDate);

					switch ($aQ['tqStyle'])
					{
						case 0:
						case 1:
							$aInsert[] = '';
							$aChoice = explode('|', $aA['select']);
							sort($aChoice,SORT_NUMERIC);
							$aRight = explode('|', $aQ['tqRight1']);
							sort($aRight,SORT_NUMERIC);
							for ($i = 1; $i <= 50; $i++)
							{
								$iV = (array_search($i,$aChoice) !== false)? 1:0;
								$aInsert[] = $iV;
							}
							if (implode('|',$aChoice) == implode('|',$aRight)) {
								$aInsert[] = 1;
								$iScore += $aQ['tqScore'];
							}
							else
							{
								$aInsert[] = 0;
							}
						break;
						case 2:
							$iLetterNum += mb_strlen($aA['text'],CL_ENC);
							$aInsert[] = $aA['text'];
							for ($i = 1; $i <= 50; $i++)
							{
								$aInsert[] = 0;
							}
							if (
								$aA['text'] != '' &&
								(
									$aA['text'] == $aQ['tqRight1'] ||
									$aA['text'] == $aQ['tqRight2'] ||
									$aA['text'] == $aQ['tqRight3'] ||
									$aA['text'] == $aQ['tqRight4'] ||
									$aA['text'] == $aQ['tqRight5']
								)
							)
							{
								$aInsert[] = 1;
								$iScore += $aQ['tqScore'];
							}
							else
							{
								$aInsert[] = 0;
							}
						break;
					}
					$query->values($aInsert);
				}
				$result = $query->execute();

				$iQualify = ($iScore >= $aTest['tbQualifyScore'])? 1:0;
				$aInsert = array(
					'tbID'=>$aTest['tbID'],
					'stID'=>$aStudent['stID'],
					'tpDate'=>$sDate,
					'tpLetterNum'=>$iLetterNum,
					'tpScore'=>$iScore,
					'tpQualify'=>$iQualify,
					'tpTime'=>$iTime,
					'tpstName'=>$aStudent['stName'],
					'tpstKana'=>$aStudent['stKana'],
					'tpstNO'=>$aStudent['stNO'],
					'tpstClass'=>$aStudent['stClass'],
					'cmKCode'=>$aStudent['cmKCode'],
					'dmNO'=>$aStudent['dmNO'],
				);
				$query = DB::insert('TestPut_Table');
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

	public static function updateTestPut($aUpdate = null,$aAndWhere = null,$aOrWhere = null)
	{
		if (is_null($aUpdate))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			$query = DB::update('TestPut_Table');
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

	public static function resetScore($sID = null)
	{
		if (is_null($sID))
		{
			throw new Exception('処理に必要な情報がありません');
		}

		$result = self::getTestBaseFromID($sID);
		if (!$result)
		{
			throw new Exception('小テストの情報がみつかりません');
		}
		$aTest = $result->current();

		$result = self::getTestQuery(array(array('tbID','=',$sID)),null,array('tqNO'=>'asc'));
		if (!$result)
		{
			throw new Exception('小テストの問題情報がみつかりません');
		}
		$aQuery = $result->as_array('tqNO');

		$result = self::getTestAns(array(array('ta.tbID','=',$sID)),null,array('ta.stID'=>'asc','ta.tqNO'=>'asc'));
		if (!$result)
		{
			throw new Exception('小テストの解答情報がみつかりません');
		}
		$aTemp = $result->as_array();

		$aAns = null;
		foreach ($aTemp as $aA)
		{
			$aAns[$aA['stID']][$aA['tqNO']] = $aA;
		}

		$aAUpdate = null;
		$aPUpdate = null;
		foreach($aAns as $sStID => $aAQ)
		{
			$aAUpdate[$sStID] = null;
			$aPUpdate[$sStID] = array('tpScore'=>0,'tpQualify'=>0);
			foreach ($aAQ as $iTqNO => $aA)
			{
				if ($aQuery[$iTqNO]['tqStyle'] == 2)
				{
					$bRight = false;
					if (
						$aA['taText'] != '' &&
						(
							$aA['taText'] == $aQuery[$iTqNO]['tqRight1'] ||
							$aA['taText'] == $aQuery[$iTqNO]['tqRight2'] ||
							$aA['taText'] == $aQuery[$iTqNO]['tqRight3'] ||
							$aA['taText'] == $aQuery[$iTqNO]['tqRight4'] ||
							$aA['taText'] == $aQuery[$iTqNO]['tqRight5']
						)
					)
					{
						$bRight = true;
					}
				}
				else
				{
					$aRight = explode('|', $aQuery[$iTqNO]['tqRight1']);
					$bRight = true;
					foreach ($aRight as $iR)
					{
						if (!$aA['taChoice'.$iR]) {
							$bRight = false;
							break;
						}
					}
				}

				# 正解判定
				if ($bRight)
				{
					$aAUpdate[$sStID][$iTqNO] = array('taRight'=>1);
					$aPUpdate[$sStID]['tpScore'] += $aQuery[$iTqNO]['tqScore'];
				}
				else
				{
					$aAUpdate[$sStID][$iTqNO] = array('taRight'=>0);
				}

			}

			if ($aPUpdate[$sStID]['tpScore'] >= $aTest['tbQualifyScore'])
			{
				$aPUpdate[$sStID]['tpQualify'] = 1;
			}
		}

		try
		{
			DB::start_transaction();

			foreach ($aPUpdate as $sStID => $aP)
			{
				$query = DB::update('TestPut_Table')
					->set($aP)
					->and_where('tbID','=',$sID)
					->and_where('stID','=',$sStID)
				;
				$result = $query->execute();

				foreach ($aAUpdate[$sStID] as $iTqNO => $aA)
				{
					$query = DB::update('TestAns_Table')
						->set($aA)
						->and_where('tbID','=',$sID)
						->and_where('stID','=',$sStID)
						->and_where('tqNO','=',$iTqNO)
					;
					$result = $query->execute();
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


	public static function getTestBent($aAndWhere = null,$aOrWhere = null,$aSort = null)
	{
		$query = DB::select_array()->from('TestBent_Table');
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


	public static function copyTest($aTest = null, $aSelClass = null, &$aTbIDs = null)
	{
		if (is_null($aTest) || is_null($aSelClass))
		{
			throw new Exception('処理に必要な情報がありません');
		}

		$sNow = date('YmdHis');

		# 元画像パス
		$sBasePath = CL_UPPATH.DS.$aTest['tbID'];

		# ベースデータ生成
		$aTBase = $aTest;
		$aTBase['tbSort'] = 0;
		$aTBase['tbPublic'] = 0;
		$aTBase['tbPublicDate'] = CL_DATETIME_DEFAULT;
		$aTBase['tbCloseDate'] = CL_DATETIME_DEFAULT;
		$aTBase['tbDate'] = $sNow;

		unset($aTBase['tpNum']);
		unset($aTBase['tpScore']);
		unset($aTBase['tpQualify']);
		unset($aTBase['scNum']);

		# クエリデータ生成
		$result = self::getTestQuery(array(array('tbID','=',$aTest['tbID'])));
		$aTQuery = null;
		if (count($result))
		{
			foreach ($result as $aTQ)
			{
				$aTQuery[$aTQ['tqNO']] = $aTQ;

				unset($aTQuery[$aTQ['tqNO']]['no']);
				$aTQuery[$aTQ['tqNO']]['tqDate'] = $sNow;
			}
		}

		try
		{
			DB::start_transaction();

			foreach ($aSelClass as $sCtID => $aC)
			{
				$sTbID = self::getTestID();
				$sCopyPath = CL_UPPATH.DS.$sTbID;

				$aTbIDs[$sCtID][$aTest['tbID']] = $sTbID;

				$aTBase['tbID'] = $sTbID;
				$aTBase['ctID'] = $sCtID;
				$aTBase['tbSort'] = self::getTestSort($sCtID);

				$result = DB::insert('TestBase_Table')
					->set($aTBase)
					->execute()
				;

				if (!is_null($aTQuery))
				{
					foreach ($aTQuery as $qNO => $aTQ)
					{
						$aTQ['tbID'] = $sTbID;
						$result = DB::insert('TestQuery_Table')
							->set($aTQ)
							->execute()
						;
					}
				}

				if (file_exists($sBasePath))
				{
					$res = system('cp -rp '.$sBasePath.' '.$sCopyPath);
					if ($res === false)
					{
						throw new Exception('小テストのコピーに失敗しました。再度実行してください。');
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


	private static function getTestID()
	{
		try
		{
			while (true):
				$sQbID = 'tb'.Str::random('numeric',8);
				$result = DB::select()->from('TestBase_Table')->where('tbID',$sQbID)->execute()->as_array();
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
			$result = DB::select(DB::expr('MAX(tqNO) AS qNO'))->from('TestQuery_Table')->where('tbID',$sID)->execute();
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
	private static function getTestSort($sCtID = null)
	{
		$iSort = 1;
		$result = DB::select(DB::expr('MAX(tbSort) AS tbMax'))->from('TestBase_Table')->where('ctID',$sCtID)->execute();
		if (count($result))
		{
			$aRes = $result->current();
			$iSort = $aRes['tbMax'] + 1;
		}
		return $iSort;
	}
	private static function setQuerySort($sID = null, $iSort = null, $bDelete = false)
	{
		try
		{
			$query = DB::update('TestQuery_Table')->where('tbID',$sID);
			if ($bDelete)
			{
				$query->set(array('tqSort'=>DB::expr('tqSort-1')))->and_where('tqSort','>',$iSort);
			}
			else
			{
				$query->set(array('tqSort'=>DB::expr('tqSort+1')))->and_where('tqSort','>=',$iSort);
			}
			$result = $query->execute();
		}
		catch (Exception $e)
		{
			throw $e;
		}
	}


}
