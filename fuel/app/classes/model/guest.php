<?php
class Model_Guest extends \Model
{
	public static function getGuestCheck($sID = null)
	{
		if (is_null($sID))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			// ログインIDで判定
			$query = DB::select_array()->from('Guest_Table');
			$query->where('gtID',$sID);
			$result = $query->execute();

			if (!count($result))
			{
				$aInsert = array(
					'gtLastAccess'=>date('YmdHis'),
					'gtDate'=>date('YmdHis'),
					'gtUA'=>Input::user_agent(),
				);
				$aUser['gtID'] = self::insertGuest($aInsert);
			}
			else
			{
				$aUser = $result->current();
				DB::start_transaction();
				$query = DB::update('Guest_Table');
				$query->value('gtLastAccess', DB::expr('NOW()'));
				$query->value('gtUA', Input::user_agent());
				$query->where('gtID',$aUser['gtID']);
				$result = $query->execute();
				DB::commit_transaction();
			}

			$query = DB::select_array()->from('Guest_Table')->where('gtID',$aUser['gtID']);
			$result = $query->execute();
			return $result;
		}
		catch (Exception $e)
		{
			// 未決のトランザクションクエリをロールバックする
			DB::rollback_transaction();
			throw $e;
		}
	}

	public static function getGuestFromID($sID = null)
	{
		$result = DB::select_array()->from('Guest_Table')->where('gtID',$sID)->execute();
		return $result;
	}

	public static function getGuest($aAndWhere = null,$aOrWhere = null,$aSort = null)
	{
		$query = DB::select_array()->from('Guest_Table');
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

	public static function insertGuest($aInsert = null)
	{
		if (is_null($aInsert))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			// IDを生成
			$sGtID = self::getGuestID();
			$aInsert['gtID'] = $sGtID;
			$query = DB::insert('Guest_Table')->set($aInsert)->execute();

			DB::commit_transaction();
			// クエリの結果を返す
			return $sGtID;
		}
		catch (Exception $e)
		{
			// 未決のトランザクションクエリをロールバックする
			DB::rollback_transaction();
			throw $e;
		}
	}

	public static function updateGuest($sGtID = null,$aUpdate = null)
	{
		if (is_null($sGtID) || is_null($aUpdate))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			// 先生IDを生成
			$query = DB::update('Guest_Table');
			$query->where('gtID',$sGtID);
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

	private static function getGuestID($aIDs = null)
	{
		try
		{
			while (true)
			{
				$sGtID = 'g'.Str::random('numeric',9);
				if (!is_null($aIDs))
				{
					if (array_search($sGtID, $aIDs) !== false)
					{
						continue;
					}
				}
				$result = DB::select()->from('Guest_Table')->where('gtID',$sGtID)->execute()->as_array();
				if (empty($result))
				{
					break;
				}
			}
			return $sGtID;
		}
		catch (Exception $e)
		{
			throw $e;
		}
	}
}
