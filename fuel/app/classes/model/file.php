<?php
class Model_File extends \Model
{
	public static function getFile($aAndWhere = null,$aOrWhere = null,$aSort = null)
	{
		$query = DB::select_array()->from('File_Table');
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

	public static function getFileFromID($sfID = null,$aAndWhere = null,$aOrWhere = null,$aSort = null)
	{
		$query = DB::select_array()->from('File_Table');
		$query->where('fID',$sfID);
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

	public static function insertFile($aInsert = null)
	{
		if (is_null($aInsert))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			$result = DB::insert('File_Table')->set($aInsert)->execute();
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

	public static function updateFile($aUpdate = null,$aAndWhere = null,$aOrWhere = null)
	{
		if (is_null($aUpdate))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			$query = DB::update('File_Table');
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

	public static function deleteFile($sfID = null)
	{
		if (is_null($sfID))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			$result = DB::delete('File_Table')->where('fID',$sfID)->execute();
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

	public static function getFileID()
	{
		try
		{
			while (true):
				$sfID = 'f'.Str::random('numeric',10);
				$result = DB::select()->from('File_Table')->where('fID',$sfID)->execute()->as_array();
				if (empty($result)):
					break;
				endif;
			endwhile;
			return $sfID;
		}
		catch (Exception $e)
		{
			throw $e;
		}
	}
}