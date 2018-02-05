<?php
class Model_Drill extends \Model
{
	public static function getDrillCategoryFromClass($sCtID = null,$aAndWhere = null,$aOrWhere = null,$aSort = null)
	{
		$query = DB::select_array()->from('DrillCategory_Table');
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
	}

	public static function getDrillCategoryFromID($sID = null,$aAndWhere = null)
	{
		$query = DB::select_array()->from('DrillCategory_Table');
		$query->where('dcID',$sID);
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

	public static function insertDrillCategory($aInsert = null)
	{
		if (is_null($aInsert))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			$sDcID = self::getDrillCategoryID();
			$aInsert['dcID'] = $sDcID;
			$aInsert['dcSort'] = self::getDrillCategorySort($aInsert['ctID']);
			$result = DB::insert('DrillCategory_Table')->set($aInsert)->execute();
			$result = DB::insert('DrillQueryGroup_Table')->set(
				array(
					'dcID' => $sDcID,
					'dgNO' => 0,
					'dgName' => 'None',
				)
			)->execute();
			DB::commit_transaction();
			// クエリの結果を返す
			return $sDcID;
		}
		catch (Exception $e)
		{
			// 未決のトランザクションクエリをロールバックする
			DB::rollback_transaction();
			throw $e;
		}
	}

	public static function updateDrillCategory($aUpdate = null,$aAndWhere = null,$aOrWhere = null)
	{
		if (is_null($aUpdate))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			$query = DB::update('DrillCategory_Table');
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

	public static function deleteDrillCategory($sID = null,$aActive = null,$aDrill = null)
	{
		if (is_null($sID) || is_null($aActive))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();

			$result = DB::delete('DrillCategory_Table')->where('dcID',$sID)->execute();

			$query = DB::update('DrillCategory_Table');
			$query->and_where('ctID',$aActive['ctID']);
			$query->and_where('dcSort','>',$aActive['dcSort']);
			$query->set(array('dcSort'=>DB::expr('dcSort - 1')));
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

	public static function sortDrillCategory($aDCategory = null,$sSort = null)
	{
		if (is_null($aDCategory) || is_null($sSort))
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
			$query = DB::update('DrillCategory_Table');
			$query->and_where('ctID',$aDCategory['ctID']);
			$query->and_where('dcSort',$aDCategory['dcSort']+$iWhere);
			$query->set(array('dcSort'=>DB::expr('dcSort'.$iUp1)));
			$result = $query->execute();
			$query = DB::update('DrillCategory_Table');
			$query->and_where('dcID',$aDCategory['dcID']);
			$query->set(array('dcSort'=>DB::expr('dcSort'.$iUp2)));
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

	public static function setDrillCategoryNums($sDcID = null)
	{
		if (is_null($sDcID))
		{
			return;
		}

		$result = self::getDrill(array(array('dcID','=',$sDcID)));
		$iDcNum = count($result);

		$result = self::getDrill(array(array('dcID','=',$sDcID),array('dbPublic','=',1)));
		$iPbNum = count($result);

		try
		{
			DB::start_transaction();
			$query = DB::update('DrillCategory_Table');
			$query->and_where('dcID',$sDcID);
			$query->set(array('dcPubNum'=>$iPbNum, 'dcNum'=>$iDcNum));
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


	public static function getDrillQueryGroup($aAndWhere = null,$aOrWhere = null,$aSort = null)
	{
		$query = DB::select_array()->from('DrillQueryGroup_Table');
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

	public static function insertDrillQueryGroup($aInsert = null)
	{
		if (is_null($aInsert))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();

			if (!isset($aInsert['dgNO']))
			{
				$aInsert['dgNO'] = self::getDrillQueryGroupNO($aInsert['dcID']);
			}
			$aInsert['dgSort'] = self::getDrillQueryGroupSort($aInsert['dcID']);
			$result = DB::insert('DrillQueryGroup_Table')
				->set($aInsert)
				->execute();

			DB::commit_transaction();
			// クエリの結果を返す
			return $aInsert['dgNO'];
		}
		catch (Exception $e)
		{
			// 未決のトランザクションクエリをロールバックする
			DB::rollback_transaction();

			if ($e->getCode() == 1022)
			{
				$aInsert['dgNO']++;
				return self::insertDrillQueryGroup($aInsert);
			}

			throw $e;
		}
	}

	public static function updateDrillQueryGroup($aUpdate = null,$aAndWhere = null,$aOrWhere = null)
	{
		if (is_null($aUpdate))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			$query = DB::update('DrillQueryGroup_Table');
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

	public static function updateDrillQueryGroupAnalysis($aUpdate = null,$sID = null)
	{
		if (is_null($aUpdate) || is_null($sID))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();

			foreach ($aUpdate as $iDgNO => $aA)
			{
				$aA['dgRate'] = ($aA['dgANum'] > 0)? round((($aA['dgRNum']/$aA['dgANum']) * 100), 1):0;

				$result = DB::update('DrillQueryGroup_Table')
					->set($aA)
					->where('dcID',$sID)
					->where('dgNO',$iDgNO)
					->execute()
				;
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

	public static function deleteDrillQueryGroup($aActive = null)
	{
		if (is_null($aActive))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();

			$result = DB::delete('DrillQueryGroup_Table')
				->where('dcID',$aActive['dcID'])
				->where('dgNO',$aActive['dgNO'])
				->execute();

			$query = DB::update('DrillQueryGroup_Table');
			$query->and_where('dcID',$aActive['dcID']);
			$query->and_where('dgSort','>',$aActive['dgSort']);
			$query->set(array('dgSort'=>DB::expr('dgSort - 1')));
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

	public static function sortDrillQueryGroup($aDQGroup = null,$sSort = null)
	{
		if (is_null($aDQGroup) || is_null($sSort))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			if ($sSort == "up")
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
			$query = DB::update('DrillQueryGroup_Table');
			$query->and_where('dcID',$aDQGroup['dcID']);
			$query->and_where('dgSort',$aDQGroup['dgSort'] + $iWhere);
			$query->set(array('dgSort'=>DB::expr('dgSort'.$iUp1)));
			$result = $query->execute();
			$query = DB::update('DrillQueryGroup_Table');
			$query->and_where('dcID',$aDQGroup['dcID']);
			$query->and_where('dgNO',$aDQGroup['dgNO']);
			$query->set(array('dgSort'=>DB::expr('dgSort'.$iUp2)));
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

	public static function getDrill($aAndWhere = null,$aOrWhere = null,$aSort = null)
	{
		$query = DB::select_array()->from('DrillBase_Table');
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

	public static function insertDrill($aInsert = null)
	{
		if (is_null($aInsert))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			$iDbNO = self::getDrillNO($aInsert['dcID']);
			$aInsert['dbNO'] = $iDbNO;
			$aInsert['dbSort'] = self::getDrillSort($aInsert['dcID']);
			$result = DB::insert('DrillBase_Table')->set($aInsert)->execute();
			DB::commit_transaction();

			// カテゴリ計算
			self::setDrillCategoryNums($aInsert['dcID']);

			// クエリの結果を返す
			return $iDbNO;
		}
		catch (Exception $e)
		{
			// 未決のトランザクションクエリをロールバックする
			DB::rollback_transaction();
			throw $e;
		}
	}

	public static function updateDrill($aUpdate = null,$aAndWhere = null,$aOrWhere = null)
	{
		if (is_null($aUpdate))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			$query = DB::update('DrillBase_Table');
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

	public static function deleteDrill($aActive = null)
	{
		if (is_null($aActive))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			$result = DB::delete('DrillBase_Table')
				->where('dcID',$aActive['dcID'])
				->and_where('dbNO',$aActive['dbNO'])
				->execute();

			$result = DB::update('DrillBase_Table')
				->where('dcID',$aActive['dcID'])
				->and_where('dbSort','>',$aActive['dbSort'])
				->set(array('dbSort'=>DB::expr('dbSort - 1')))
				->execute();

			DB::commit_transaction();

			// カテゴリ計算
			self::setDrillCategoryNums($aActive['dcID']);

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

	public static function sortDrill($aDrill = null,$sSort = null)
	{
		if (is_null($aDrill) || is_null($sSort))
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
			$query = DB::update('DrillBase_Table');
			$query->and_where('dcID',$aDrill['dcID']);
			$query->and_where('dbSort',$aDrill['dbSort']+$iWhere);
			$query->set(array('dbSort'=>DB::expr('dbSort'.$iUp1)));
			$result = $query->execute();
			$query = DB::update('DrillBase_Table');
			$query->and_where('dcID',$aDrill['dcID']);
			$query->and_where('dbNO',$aDrill['dbNO']);
			$query->set(array('dbSort'=>DB::expr('dbSort'.$iUp2)));
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

	public static function publicDrill($aDrill = null,$iPub = null)
	{
		if (is_null($aDrill) || is_null($iPub))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			$result = DB::update('DrillBase_Table')
				->and_where('dcID',$aDrill['dcID'])
				->and_where('dbNO',$aDrill['dbNO'])
				->set(array('dbPublic'=>$iPub))
				->execute();
			DB::commit_transaction();

			// カテゴリ計算
			self::setDrillCategoryNums($aDrill['dcID']);

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

	public static function getDrillQuery($aAndWhere = null,$aOrWhere = null,$aSort = null)
	{
		$query = DB::select_array()->from('DrillQuery_Table');
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

	public static function insertDrillQuery($aInsert = null)
	{
		if (is_null($aInsert))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			$iDqNO = self::getQueryNO($aInsert['dcID'],$aInsert['dbNO']);
			self::setQuerySort($aInsert['dcID'],$aInsert['dbNO'],$aInsert['dqSort']);
			$aInsert['dqNO'] = $iDqNO;
			$result = DB::insert('DrillQuery_Table')->set($aInsert)->execute();
			$result = DB::update('DrillBase_Table')
				->set(array('dbQueryNum'=>DB::expr('dbQueryNum+1')))
				->where('dcID',$aInsert['dcID'])
				->where('dbNO',$aInsert['dbNO'])
				->execute();
			$result = DB::update('DrillQueryGroup_Table')
				->set(array('dgQNum'=>DB::expr('dgQNum + 1')))
				->where('dcID',$aInsert['dcID'])
				->where('dgNO',$aInsert['dgNO'])
				->execute();
			DB::commit_transaction();
			// クエリの結果を返す
			return $iDqNO;
		}
		catch (Exception $e)
		{
			// 未決のトランザクションクエリをロールバックする
			DB::rollback_transaction();
			throw $e;
		}
	}

	public static function insertDrillQueryFromCSV($aDrill = null,$aQuery = null)
	{
		if (is_null($aDrill) || is_null($aQuery))
		{
			throw new Exception('処理に必要な情報がありません');
		}

		$sDate = date('YmdHis');

		try
		{
			$aGQ = null;
			DB::start_transaction();
			foreach ($aQuery as $iQN => $aQ)
			{
				$aQ['dcID'] = $aDrill['dcID'];
				$aQ['dbNO'] = $aDrill['dbNO'];
				$aQ['dqNO'] = self::getQueryNO($aDrill['dcID'],$aDrill['dbNO']);
				$aQ['dqSort'] = self::getQuerySort($aDrill['dcID'],$aDrill['dbNO']);
				$aQ['dqDate'] = $sDate;
				$result = DB::insert('DrillQuery_Table')->set($aQ)->execute();
				if (isset($aGQ[$aQ['dgNO']]))
				{
					$aGQ[$aQ['dgNO']]++;
				}
				else
				{
					$aGQ[$aQ['dgNO']] = 1;
				}
			}

			$result = DB::update('DrillBase_Table')
				->set(array('dbQueryNum'=>DB::expr('dbQueryNum + '.count($aQuery))))
				->where('dcID',$aDrill['dcID'])
				->where('dbNO',$aDrill['dbNO'])
				->execute();

			foreach ($aGQ as $iDgNO => $iN)
			{
				$result = DB::update('DrillQueryGroup_Table')
					->set(array('dgQNum'=>DB::expr('dgQNum + '.$iN)))
					->where('dcID',$aDrill['dcID'])
					->where('dgNO',$iDgNO)
					->execute();
			}

			DB::commit_transaction();
			// クエリの結果を返す
			return true;
		}
		catch (Exception $e)
		{
			// 未決のトランザクションクエリをロールバックする
			DB::rollback_transaction();
			throw $e;
		}
	}

	public static function updateDrillQuery($aUpdate = null,$aAndWhere = null,$aOrWhere = null)
	{
		if (is_null($aUpdate))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			$query = DB::update('DrillQuery_Table');
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

	public static function deleteDrillQuery($aActive = null)
	{
		if (is_null($aActive))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			$result = DB::delete('DrillQuery_Table')
				->where('dcID',$aActive['dcID'])
				->where('dbNO',$aActive['dbNO'])
				->where('dqNO',$aActive['dqNO'])
				->execute();
			self::setQuerySort($aActive['dcID'],$aActive['dbNO'],$aActive['dqSort'],true);
			$result = DB::update('DrillBase_Table')
				->set(array('dbQueryNum'=>DB::expr('dbQueryNum-1')))
				->where('dcID',$aActive['dcID'])
				->where('dbNO',$aActive['dbNO'])
				->execute();
			$result = DB::update('DrillQueryGroup_Table')
				->set(array('dgQNum'=>DB::expr('dgQNum - 1')))
				->where('dcID',$aActive['dcID'])
				->where('dgNO',$aActive['dgNO'])
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

	public static function sortDrillQuery($aQuery = null,$sSort = null)
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
			$query = DB::update('DrillQuery_Table');
			$query->and_where('dcID',$aQuery['dcID']);
			$query->and_where('dbNO',$aQuery['dbNO']);
			$query->and_where('dqSort',$aQuery['dqSort']+$iWhere);
			$query->set(array('dqSort'=>DB::expr('dqSort'.$iUp1)));
			$result = $query->execute();
			$query = DB::update('DrillQuery_Table');
			$query->and_where('dcID',$aQuery['dcID']);
			$query->and_where('dbNO',$aQuery['dbNO']);
			$query->and_where('dqNO',$aQuery['dqNO']);
			$query->set(array('dqSort'=>DB::expr('dqSort'.$iUp2)));
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

	public static function insertDrillQueries($aInsert = null)
	{
		if (is_null($aInsert))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			$aInSet = null;
			DB::start_transaction();

			foreach ($aInsert as $iTqNO => $aI)
			{
				$iDqNO = self::getQueryNO($aI['dcID'],$aI['dbNO']);
				self::setQuerySort($aI['dcID'],$aI['dbNO'],$aI['dqSort']);
				$aI['dqNO'] = $iDqNO;
				$result = DB::insert('DrillQuery_Table')->set($aI)->execute();
				$result = DB::update('DrillBase_Table')
					->set(array('dbQueryNum'=>DB::expr('dbQueryNum + 1')))
					->where('dcID',$aI['dcID'])
					->where('dbNO',$aI['dbNO'])
					->execute();
				$result = DB::update('DrillQueryGroup_Table')
					->set(array('dgQNum'=>DB::expr('dgQNum + 1')))
					->where('dcID',$aI['dcID'])
					->where('dgNO',$aI['dgNO'])
					->execute();

				$aInsSet[$iTqNO] = $iDqNO;
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


	public static function getDrillPut($aAndWhere = null,$aOrWhere = null,$aSort = null)
	{
		$query = DB::select_array()->from('DrillPut_Table');
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

	public static function setDrillPut($aDrill = null,$aPutIns = null,$aAnsIns = null,$aStudent = null)
	{
		if (is_null($aDrill) || is_null($aPutIns) || is_null($aAnsIns) || is_null($aStudent))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			$sDate = date('YmdHis');

			DB::start_transaction();

			$aColumns = array('dcID','dbNO','dqNO','stID','daRight','daDate');
			$query = DB::insert('DrillAns_Table')->columns($aColumns);
			foreach ($aAnsIns as $aA)
			{
				$query->values(
					array(
						$aDrill['dcID'],
						$aDrill['dbNO'],
						$aA['dqNO'],
						$aStudent['stID'],
						$aA['daRight'],
						$sDate
					)
				);
			}
			$result = $query->execute();

			$aPutIns['dcID'] = $aDrill['dcID'];
			$aPutIns['dbNO'] = $aDrill['dbNO'];
			$aPutIns['stID'] = $aStudent['stID'];
			$aPutIns['dpDate'] = $sDate;

			$result = DB::insert('DrillPut_Table')
				->set($aPutIns)
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

	public static function deleteDrillPut($aActive = null)
	{
		if (is_null($aActive))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			$result = DB::delete('DrillAns_Table')->where('dcID',$aActive['dcID'])->where('dbNO',$aActive['dbNO'])->execute();
			$result = DB::delete('DrillPut_Table')->where('dcID',$aActive['dcID'])->where('dbNO',$aActive['dbNO'])->execute();
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

	public static function setDrillQueryAnalysis($aDrill = null)
	{
		if (is_null($aDrill))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		$sID = $aDrill['dcID'];
		$iNO = $aDrill['dbNO'];

		$result = DB::select_array()
			->from('DrillQuery_Table')
			->and_where('dcID','=',$sID)
			->and_where('dbNO','=',$iNO)
			->execute();
		if (!count($result))
		{
			return;
		}
		$aQqs = $result->as_array();
		$sDate = date("YmdHis");

		$aBent = null;
		# 回答数
		$result = DB::select('dqNO',DB::expr('count(no) as dqaANum'))
			->from('DrillAns_Table')
			->and_where('dcID','=',$sID)
			->and_where('dbNO','=',$iNO)
			->group_by('dqNO')
			->order_by('dqNO','asc')
			->execute();
		if (count($result))
		{
			foreach ($result as $aB)
			{
				$aBent[$aB['dqNO']] = array(
					'dqaANum' => (int)$aB['dqaANum'],
					'dqaRNum' => 0,
					'dqaRate' => 0,
				);
			}
		}

		# 正解数
		$result = DB::select('dqNO',DB::expr('count(no) as dqaRNum'))
		->from('DrillAns_Table')
		->and_where('dcID','=',$sID)
		->and_where('dbNO','=',$iNO)
		->and_where('daRight','=',1)
		->group_by('dqNO')
		->order_by('dqNO','asc')
		->execute();
		if (count($result))
		{
			foreach ($result as $aB)
			{
				if (isset($aBent[$aB['dqNO']]))
				{
					$aBent[$aB['dqNO']]['dqaRNum'] = $aB['dqaRNum'];
					$aBent[$aB['dqNO']]['dqaRate'] = round((($aBent[$aB['dqNO']]['dqaRNum']/$aBent[$aB['dqNO']]['dqaANum']) * 100), 1);
				}
			}
		}

		$aColmuns = array('dcID','dbNO','dqNO','dqaANum','dqaRNum','dqaRate');
		$insertQuery = DB::insert('DrillQueryAnalysis_Table',$aColmuns);
		$aInsert = null;

		foreach ($aQqs as $aQ)
		{
			$iDqNO = $aQ['dqNO'];

			$aInsert = array($sID,$iNO,$iDqNO,0,0,0);

			if (isset($aBent[$iDqNO]))
			{
				$aInsert[3] = $aBent[$iDqNO]['dqaANum'];
				$aInsert[4] = $aBent[$iDqNO]['dqaRNum'];
				$aInsert[5] = $aBent[$iDqNO]['dqaRate'];
			}

			$insertQuery->values($aInsert);
		}

		try
		{
			DB::start_transaction();
			$result = DB::delete('DrillQueryAnalysis_Table')
				->and_where('dcID','=',$sID)
				->and_where('dbNO','=',$iNO)
				->execute();

			$result = $insertQuery->execute();

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

	public static function getDrillQueryAnalysis($aAndWhere = null,$aOrWhere = null,$aSort = null)
	{
		$query = DB::select_array()->from('DrillQueryAnalysis_Table');
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

	public static function getDrillAns($aAndWhere = null,$aOrWhere = null,$aSort = null)
	{
		$query = DB::select_array()->from('DrillAns_Table');
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


	public static function setDrillQueryGroupQNum($sID)
	{
		$result = self::getDrillQuery(array(array('dcID','=',$sID)));
		if (!count($result))
		{
			$query = DB::update('DrillQueryGroup_Table')
				->where('dcID',$sID)
				->set(array('dgQNum'=>0));
			try
			{
				DB::start_transaction();
				$result = $query->execute();
				DB::commit_transaction();

				return $result;
			}
			catch (Exception $e)
			{
				DB::rollback_transaction();
				throw $e;
			}
		}
		else
		{
			$aUp = null;
			foreach ($result as $q)
			{
				if (isset($aUp[$q['dgNO']]))
				{
					$aUp[$q['dgNO']]++;
				}
				else
				{
					$aUp[$q['dgNO']] = 1;
				}
			}
			if (is_null($aUp))
			{
				return;
			}

			try
			{
				DB::start_transaction();
				$result = DB::update('DrillQueryGroup_Table')
					->where('dcID',$sID)
					->set(array('dgQNum'=>0))
					->execute();

				foreach ($aUp as $iDgNO => $iQNum)
				{
					$result = DB::update('DrillQueryGroup_Table')
						->where('dcID',$sID)
						->where('dgNO',$iDgNO)
						->set(array('dgQNum' => $iQNum))
						->execute();
				}

				DB::commit_transaction();

				return $result;
			}
			catch (Exception $e)
			{
				DB::rollback_transaction();
				throw $e;
			}
		}




	}


	private static function getDrillCategoryID()
	{
		try
		{
			while (true)
			{
				$sDcID = 'd'.Str::random('numeric',9);
				$result = DB::select()->from('DrillCategory_Table')->where('dcID',$sDcID)->execute()->as_array();
				if (empty($result))
				{
					break;
				}
			}
			return $sDcID;
		}
		catch (Exception $e)
		{
			throw $e;
		}
	}

	private static function getDrillCategorySort($sCtID = null)
	{
		$iSort = 1;
		$result = DB::select(DB::expr('MAX(dcSort) AS nMax'))->from('DrillCategory_Table')->where('ctID',$sCtID)->execute();
		if (count($result))
		{
			$aRes = $result->current();
			$iSort = $aRes['nMax'] + 1;
		}
		return $iSort;
	}


	private static function getDrillNO($sID = null)
	{
		try
		{
			$result = DB::select(DB::expr('MAX(dbNO) AS nMax'))->from('DrillBase_Table')->where('dcID',$sID)->execute();
			if (count($result))
			{
				$aRes = $result->current();
				return ($aRes['nMax'] + 1);
			}
			return 1;
		}
		catch (Exception $e)
		{
			throw $e;
		}
	}

	private static function getDrillSort($sID = null)
	{
		$iSort = 1;
		$result = DB::select(DB::expr('MAX(dbSort) AS nMax'))->from('DrillBase_Table')->where('dcID',$sID)->execute();
		if (count($result))
		{
			$aRes = $result->current();
			$iSort = $aRes['nMax'] + 1;
		}
		return $iSort;
	}

	private static function getDrillQueryGroupNO($sID = null)
	{
		$iNO = 1;
		$result = DB::select(DB::expr('MAX(dgNO) AS nMax'))->from('DrillQueryGroup_Table')->where('dcID',$sID)->execute();
		if (count($result))
		{
			$aRes = $result->current();
			$iNO = $aRes['nMax'] + 1;
		}
		return $iNO;
	}

	private static function getDrillQueryGroupSort($sID = null)
	{
		$iSort = 1;
		$result = DB::select(DB::expr('MAX(dgSort) AS nMax'))->from('DrillQueryGroup_Table')->where('dcID',$sID)->execute();
		if (count($result))
		{
			$aRes = $result->current();
			$iSort = $aRes['nMax'] + 1;
		}
		return $iSort;
	}

	private static function getQueryNO($sID = null,$iNO = null)
	{
		try
		{
			$result = DB::select(DB::expr('MAX(dqNO) AS nMax'))->from('DrillQuery_Table')->where('dcID',$sID)->where('dbNO',$iNO)->execute();
			if (count($result))
			{
				$aRes = $result->current();
				return ($aRes['nMax'] + 1);
			}
			return 1;
		}
		catch (Exception $e)
		{
			throw $e;
		}
	}

	private static function getQuerySort($sID = null,$iNO = null)
	{
		try
		{
			$result = DB::select(DB::expr('MAX(dqSort) AS nMax'))->from('DrillQuery_Table')->where('dcID',$sID)->where('dbNO',$iNO)->execute();
			if (count($result))
			{
				$aRes = $result->current();
				return ($aRes['nMax'] + 1);
			}
			return 1;
		}
		catch (Exception $e)
		{
			throw $e;
		}
	}

	private static function setQuerySort($sID = null, $iNO = null, $iSort = null, $bDelete = false)
	{
		try
		{
			$query = DB::update('DrillQuery_Table')->where('dcID',$sID)->where('dbNO',$iNO);
			if ($bDelete)
			{
				$query->set(array('dqSort'=>DB::expr('dqSort-1')))->and_where('dqSort','>',$iSort);
			}
			else
			{
				$query->set(array('dqSort'=>DB::expr('dqSort+1')))->and_where('dqSort','>=',$iSort);
			}
			$result = $query->execute();
		}
		catch (Exception $e)
		{
			throw $e;
		}
	}

}
