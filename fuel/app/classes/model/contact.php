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

	public static function getContactUnread($sStID = null, $aCtIDs = null)
	{
		$query = DB::select_array(
				array(
					'co.ctID',
					DB::expr('count(co.no) AS Unread')
				)
			)
			->from(array('ContactHistory_Table','co'))
			->and_where('co.coRead','=',0)
			->group_by('co.ctID')
		;

		if (!is_null($sStID))
		{
			$query->and_where('co.stID','=',$sStID)
				->and_where('co.coID','!=',$sStID)
			;
		}
		else
		{
			$query->and_where('co.coTeach','=',0);
		}

		if (!is_null($aCtIDs))
		{
			$query->and_where('co.ctID','IN',$aCtIDs);
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

}
