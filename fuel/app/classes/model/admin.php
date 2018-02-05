<?php
class Model_Admin extends \Model
{
	public static function getAdminFromPostLogin($sID = null,$sPass = null)
	{
		if (is_null($sID) || is_null($sPass))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			$query = DB::update('Admin_Table');
			$query->value('adLoginNum', DB::expr('`adLoginNum`+1'));
			$query->value('adLastLoginDate', DB::expr('`adLoginDate`'));
			$query->value('adLoginDate', DB::expr('NOW()'));
			$query->value('adPassMiss', 0);
			$query->value('adUA', Input::user_agent());
			$query->value('adHash', sha1($sID.sha1($sPass)));
			$query->where('adLogin',$sID);
			$query->where('adPass',sha1($sPass));
			$query->where('adStatus',1);
			$result = $query->execute();
			DB::commit_transaction();

			$query = DB::select_array()
				->from('Admin_Table')
				->where('adLogin',$sID)
				->where('adPass',sha1($sPass))
				->where('adStatus',1);
			$result = $query->execute();

			if (!count($result))
			{
				DB::start_transaction();
				$query = DB::update('Admin_Table');
				$query->value('adPassMiss', DB::expr('`adPassMiss`+1'));
				$query->where('adLogin',$sID);
				$query->where('adStatus',1);
				$result2 = $query->execute();
				DB::commit_transaction();
			}

			return $result;
		}
		catch (Exception $e)
		{
			// 未決のトランザクションクエリをロールバックする
			DB::rollback_transaction();
			throw $e;
		}
	}
	public static function getAdminFromMail($sMail = null, $sAdID = null)
	{
		$query = DB::select_array()->from('Admin_Table')->where('adMail',$sMail);
		if (!is_null($sAdID))
		{
			$query->where('adID','!=',$sAdID);
		}
		$result = $query->execute();

		return $result;
	}
	public static function getAdminFromHash($sHash = null)
	{
		$query = DB::select_array()->from('Admin_Table')->where('adHash',$sHash);
		$result = $query->execute();

		return $result;
	}
	public static function getAdminFromID($sAdID = null)
	{
		$result = DB::select_array()->from('Admin_Table')->where('adID',$sAdID)->execute();
		return $result;
	}

	public static function getAdmin($aAndWhere = null,$aOrWhere = null,$aSort = null)
	{
		$query = DB::select_array()->from('Admin_Table');
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

	public static function updateAdmin($sAdID = null,$aUpdate = null)
	{
		if (is_null($sAdID) || is_null($aUpdate))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			$query = DB::update('Admin_Table');
			$query->where('adID',$sAdID);
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
}
