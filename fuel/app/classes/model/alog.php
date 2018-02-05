<?php
class Model_Alog extends \Model
{
	public static function getAlogThemeFromClass($sCtID = null,$aAndWhere = null,$aOrWhere = null,$aSort = null)
	{
		$query = DB::select_array()->from('ActivityLogTheme_Table');
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

	public static function getAlogThemeFromID($sID = null,$aAndWhere = null)
	{
		$query = DB::select_array()->from('ActivityLogTheme_Table');
		$query->where('altID',$sID);
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

	public static function insertAlogTheme($aInsert = null)
	{
		if (is_null($aInsert))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			$sAltID = self::getAlogThemeID();
			$aInsert['altID'] = $sAltID;
			$aInsert['altSort'] = self::getAlogThemeSort($aInsert['ctID']);
			$result = DB::insert('ActivityLogTheme_Table')->set($aInsert)->execute();
			DB::commit_transaction();
			// クエリの結果を返す
			return $sAltID;
		}
		catch (Exception $e)
		{
			// 未決のトランザクションクエリをロールバックする
			DB::rollback_transaction();
			throw $e;
		}
	}

	public static function updateAlogTheme($aUpdate = null,$aAndWhere = null,$aOrWhere = null)
	{
		if (is_null($aUpdate))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			$query = DB::update('ActivityLogTheme_Table');
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

	public static function deleteAlogTheme($sID = null,$aActive = null)
	{
		if (is_null($sID) || is_null($aActive))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();

			$result = DB::delete('ActivityLogTheme_Table')->where('altID',$sID)->execute();

			$query = DB::update('ActivityLogTheme_Table');
			$query->and_where('ctID',$aActive['ctID']);
			$query->and_where('altSort','>',$aActive['altSort']);
			$query->set(array('altSort'=>DB::expr('altSort - 1')));
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

	public static function sortAlogTheme($aALTheme = null,$sSort = null)
	{
		if (is_null($aALTheme) || is_null($sSort))
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
			$query = DB::update('ActivityLogTheme_Table');
			$query->and_where('ctID',$aALTheme['ctID']);
			$query->and_where('altSort',$aALTheme['altSort']+$iWhere);
			$query->set(array('altSort'=>DB::expr('altSort'.$iUp1)));
			$result = $query->execute();
			$query = DB::update('ActivityLogTheme_Table');
			$query->and_where('altID',$aALTheme['altID']);
			$query->set(array('altSort'=>DB::expr('altSort'.$iUp2)));
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

	public static function publicAlogTheme($aALTheme = null,$iPub = null)
	{
		if (is_null($aALTheme) || is_null($iPub))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			$result = DB::update('ActivityLogTheme_Table')
				->and_where('altID',$aALTheme['altID'])
				->set(array('altPublic'=>$iPub))
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

	public static function getAlogGoal($aAndWhere = null,$aOrWhere = null,$aSort = null)
	{
		$query = DB::select_array()->from('ActivityLogGoal_Table');
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

	public static function insertAlogGoal($aInsert = null)
	{
		if (is_null($aInsert))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			$result = DB::insert('ActivityLogGoal_Table')->set($aInsert)->execute();
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

	public static function updateAlogGoal($aUpdate = null,$aAndWhere = null,$aOrWhere = null)
	{
		if (is_null($aUpdate))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			$query = DB::update('ActivityLogGoal_Table');
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

	public static function getAlog($aAndWhere = null,$aOrWhere = null,$aSort = null, $aOrGroup = null)
	{
		$query = DB::select_array(
			array(
				'al.*',
				'ft.fName',
				'ft.fSize',
				'ft.fExt',
				'ft.fContentType',
				'ft.fFileType',
				'ft.fPath',
				'ft.fUserType',
				'ft.fUser',
				'ft.fDate',
			)
		)
			->from(array('ActivityLog_Table','al'))
			->join(array('File_Table','ft'),'LEFT')
			->on('al.fID','=','ft.fID')
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
		if (!is_null($aOrGroup))
		{
			$query->and_where_open();
			foreach ($aOrGroup as $aW)
			{
				$query->or_where($aW[0],$aW[1],$aW[2]);
			}
			$query->and_where_close();
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

	public static function insertAlog($aInsert = null)
	{
		if (is_null($aInsert))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			$result = DB::insert('ActivityLog_Table')
				->set($aInsert)
				->execute();

			$result = DB::update('ActivityLogTheme_Table')
				->set(array('alNum'=>DB::expr('alNum+1')))
				->where('altID',$aInsert['altID'])
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

	public static function updateAlog($aUpdate = null,$aAndWhere = null,$aOrWhere = null)
	{
		if (is_null($aUpdate))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			$query = DB::update('ActivityLog_Table');
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

	public static function deleteAlog($aActive = null)
	{
		if (is_null($aActive))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			$result = DB::delete('ActivityLog_Table')
				->where('altID',$aActive['altID'])
				->and_where('no',$aActive['no'])
				->execute();

			$result = DB::update('ActivityLogTheme_Table')
				->set(array('alNum'=>DB::expr('alNum-1')))
				->where('altID',$aActive['altID'])
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

	private static function getAlogThemeID()
	{
		try
		{
			while (true)
			{
				$sAltID = 'L'.Str::random('numeric',9);
				$result = DB::select()->from('ActivityLogTheme_Table')->where('altID',$sAltID)->execute()->as_array();
				if (empty($result))
				{
					break;
				}
			}
			return $sAltID;
		}
		catch (Exception $e)
		{
			throw $e;
		}
	}

	private static function getAlogThemeSort($sCtID = null)
	{
		$iSort = 1;
		$result = DB::select(DB::expr('MAX(altSort) AS nMax'))->from('ActivityLogTheme_Table')->where('ctID',$sCtID)->execute();
		if (count($result))
		{
			$aRes = $result->current();
			$iSort = $aRes['nMax'] + 1;
		}
		return $iSort;
	}

}
