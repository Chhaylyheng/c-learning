<?php
class Model_Contact extends \Model
{
	public static function getContact($sCtID = null,$sStID = null,$aAndWhere = null,$aOrWhere = null,$aSort = null)
	{
		$query = DB::select_array()->from(array('ContactHistory_Table','co'));

		if (!is_null($sCtID))
		{
			$query->where('co.ctID',$sCtID);
		}
		if (!is_null($sStID))
		{
			$query->where('co.stID',$sStID);
		}
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

	public static function insertContact($aInsert = null)
	{
		if (is_null($aInsert))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			$result = DB::insert('ContactHistory_Table')->set($aInsert)->execute();
			$iNO = $result[0];
			DB::commit_transaction();
			// クエリの結果を返す
			return $iNO;
		}
		catch (Exception $e)
		{
			// 未決のトランザクションクエリをロールバックする
			DB::rollback_transaction();
			throw $e;
		}
	}

	public static function updateContact($aUpdate = null,$aAndWhere = null,$aOrWhere = null)
	{
		if (is_null($aUpdate))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			$query = DB::update('ContactHistory_Table');
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

	public static function updateStatus($aUpdate = null, $sCtID = null, $sStID = null, $iNO = null)
	{
		if (is_null($aUpdate) || is_null($sCtID) || is_null($sStID) || is_null($iNO))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			$query = DB::update('ContactHistory_Table');

			$query->and_where('ctID','=',$sCtID);
			$query->and_where('stID','=',$sStID);
			$query->and_where_open();
			$query->or_where('no','=',$iNO);
			$query->or_where('parent','=',$iNO);
			$query->and_where_close();

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

	public static function deleteContact($iNO = null)
	{
		if (is_null($iNO))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();

			$result = DB::delete('ContactHistory_Table')
				->where('no','=',$iNO)
				->execute();

			$result = DB::delete('ContactHistory_Table')
				->where('parent','=',$iNO)
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


	public static function sortMaterial($aMaterial = null,$sSort = null)
	{
		if (is_null($aMaterial) || is_null($sSort))
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
			$query = DB::update('Material_Table');
			$query->and_where('mcID',$aMaterial['mcID']);
			$query->and_where('mSort',$aMaterial['mSort']+$iWhere);
			$query->set(array('mSort'=>DB::expr('mSort'.$iUp1)));
			$result = $query->execute();
			$query = DB::update('Material_Table');
			$query->and_where('mcID',$aMaterial['mcID']);
			$query->and_where('mNO',$aMaterial['mNO']);
			$query->set(array('mSort'=>DB::expr('mSort'.$iUp2)));
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

	public static function publicMaterial($aMaterial = null,$iPub = null)
	{
		if (is_null($aMaterial) || is_null($iPub))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			$query = DB::update('Material_Table');
			$query->and_where('mcID',$aMaterial['mcID']);
			$query->and_where('mNO',$aMaterial['mNO']);
			$query->set(array('mPublic'=>$iPub));
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

	public static function getMaterialAlreadyCountFromStudent($sStID = null, $aAndWhere = null, $aOrWhere = null)
	{
		if (is_null($sStID))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		$query = DB::select_array(array(
			'mcID',
			DB::expr('count(mNO) as aCnt')
		));
		$query->from('MaterialAlready_Table');
		$query->where('stID','=',$sStID);
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
		$query->group_by('mcID');
		$result = $query->execute();
		return $result;
	}

	public static function getMaterialAlready($aAndWhere = null, $aOrWhere = null)
	{
		$query = DB::select_array()->from('MaterialAlready_Table');
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
		$result = $query->execute();
		return $result;
	}

	public static function insertMaterialAlready($aInsert = null)
	{
		if (is_null($aInsert))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			$result = DB::insert('MaterialAlready_Table')
				->set($aInsert)
				->execute()
			;
			$result = DB::update('Material_Table')
				->and_where('mNO',$aInsert['mNO'])
				->set(array('mAlreadyNum'=>DB::expr('mAlreadyNum + 1')))
				->execute()
			;
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

	public static function copyMaterial($aCate = null, $aSelClass = null, $aQbIDs = null, $aTbIDs = null)
	{
		if (is_null($aCate) || is_null($aSelClass))
		{
			throw new Exception('処理に必要な情報がありません');
		}

		$sNow = date('YmdHis');

		# ベースデータ生成
		$aMBase = $aCate;
		$aMBase['mcSort'] = 0;
		$aMBase['mcDate'] = $sNow;

		unset($aMBase['mcNum']);
		unset($aMBase['mcPubNum']);
		unset($aMBase['mcTotalSize']);
		unset($aMBase['mcLastDate']);

		# 教材データ生成
		$result = self::getMaterial(array(array('mcID','=',$aCate['mcID'])),null,array('mSort'=>'desc'));
		$aMaterial = null;
		if (count($result))
		{
			foreach ($result as $aM)
			{
				$aMaterial[$aM['mNO']] = array(
					'mcID' => null,
					'mTitle' => $aM['mTitle'],
					'fID' => $aM['fID'],
					'mURL' => $aM['mURL'],
					'mText' => $aM['mText'],
					'mID' => $aM['mID'],
					'mPublic' => $aM['mPublic'],
					'mSort' => $aM['mSort'],
					'mAlreadyNum' => 0,
					'mDate' => $aM['mDate'],
				);
			}
		}

		try
		{
			DB::start_transaction();

			foreach ($aSelClass as $sCtID => $aC)
			{
				$sMcID = self::getMaterialCategoryID();

				$aMBase['mcID'] = $sMcID;
				$aMBase['ctID'] = $sCtID;
				$aMBase['mcSort'] = self::getMaterialCategorySort($sCtID);

				$result = DB::insert('MaterialCategory_Table')
					->set($aMBase)
					->execute()
				;

				if (!is_null($aMaterial))
				{
					foreach ($aMaterial as $aM)
					{
						$aM['mcID'] = $sMcID;

						if ($aM['mURL'])
						{
							$aM['mURL'] = \Clfunc_Common::ExtUrlDetectForCopy($aM['mURL'], $sCtID, $aQbIDs, $aTbIDs);
						}

						$result = DB::insert('Material_Table')
							->set($aM)
							->execute()
						;
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
}
