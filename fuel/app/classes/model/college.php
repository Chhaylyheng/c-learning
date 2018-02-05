<?php
class Model_College extends \Model
{
	public static function getCollegeLikeName($sName = null)
	{
		$result = DB::select('cmName')
			->from('College_Master')
			->where('cmName','LIKE','%'.$sName.'%')
			->order_by('no','asc')
			->execute();
		return $result;
	}
	public static function getCollegeFromName($sName = null)
	{
		$result = DB::select()->from('College_Master')->where('cmName',$sName)->execute();
		return $result;
	}

	public static function setCollege($sName = null)
	{
		if (is_null($sName))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			$sCmKCode = self::getCmKCode();
			$query = DB::Insert('College_Master');
			$query->set(
				array(
					'cmKCode' => $sCmKCode,
					'cmName'  => $sName,
					'cmDate'  => date('YmdHis')
				)
			);
			$result = $query->execute();
			DB::commit_transaction();
			// クエリの結果を返す
			return $sCmKCode;
		}
		catch (Exception $e)
		{
			// 未決のトランザクションクエリをロールバックする
			DB::rollback_transaction();
			throw $e;
		}
	}


	public static function getDeptList()
	{
		$query = DB::select()->from('Dept_Master');
		$result = $query->execute();
		return $result;
	}
	public static function getDeptListFromKCode($sKCode = null, $bBaseFlag = false)
	{
		$query = DB::select()->from('Dept_Master')->where('cmKCode',$sKCode);
		if (!$bBaseFlag) {
			$query->where('dmNO','!=',0);
		}
		$result = $query->execute();
		return $result;
	}
	public static function getDeptValidation($sCollege = null, $sDept = null)
	{
		$query = DB::query(
			'SELECT * FROM Dept_View WHERE cmName='.DB::escape($sCollege)
		);
		$result = $query->execute();
		if (!count($result) && (is_null($sDept) || $sDept == ''))
		{
			return true;
		}
		else
		{
			foreach ($result as $r)
			{
				if ($r['dmName'] == $sDept)
				{
					return true;
				}
			}
		}
		return false;
	}
	public static function getDeptListFromCollegeName($sName = null, $bBaseFlag = false)
	{
		$sql = 'SELECT * FROM Dept_View WHERE cmName='.DB::escape($sName);
		if (!$bBaseFlag)
		{
			$sql .= ' AND dmNO!=0';
		}
		$query = DB::query($sql);
		$result = $query->execute();
		return $result;
	}
	public static function getDeptFromName($sCollege = null, $sDept = null)
	{
		$sql = 'SELECT * FROM Dept_View WHERE cmName='.DB::escape($sCollege);
		if (is_null($sDept) || $sDept == '')
		{
			$sql .= ' AND dmNO=0';
		} else {
			$sql .= ' AND dmName='.DB::escape($sDept);
		}
		$result = DB::query($sql)->execute();
		return $result;
	}
	public static function getPeriod($aInput = null)
	{
		$query = DB::select()->from('DeptPeriod_View');
		if (!is_null($aInput))
		{
			foreach ($aInput as $sKey => $sValue)
			{
				$query->where($sKey,$sValue);
			}
		}
		$query->order_by('dpNO','asc');

		$result = $query->execute();
		return $result;
	}
	public static function insertPeriod($sKCode = null,$iDmNO = null,$aColumns = null,$aInsert = null)
	{
		if (is_null($sKCode) || is_null($iDmNO) || is_null($aColumns))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			$query = DB::delete('DeptPeriod_Table');
			$query->where('cmKCode',$sKCode);
			$query->where('dmNO',$iDmNO);
			$result = $query->execute();

			if (!is_null($aInsert))
			{
				$query = DB::Insert('DeptPeriod_Table');
				$query->columns($aColumns);
				foreach ($aInsert as $aI)
				{
					$query->values($aI);
				}
			}
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
	public static function getHour($aInput = null)
	{
		$query = DB::select()->from('DeptHour_View');
		if (!is_null($aInput))
		{
			foreach ($aInput as $sKey => $sValue)
			{
				$query->where($sKey,$sValue);
			}
		}
		$query->order_by('dhNO','asc');

		$result = $query->execute();
		return $result;
	}
	public static function insertHour($sKCode = null,$iDmNO = null,$aColumns = null,$aInsert = null)
	{
		if (is_null($sKCode) || is_null($iDmNO) || is_null($aColumns))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			$query = DB::delete('DeptHour_Table');
			$query->where('cmKCode',$sKCode);
			$query->where('dmNO',$iDmNO);
			$result = $query->execute();

			if (!is_null($aInsert))
			{
				$query = DB::Insert('DeptHour_Table');
				$query->columns($aColumns);
				foreach ($aInsert as $aI)
				{
					$query->values($aI);
				}
			}
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

	private static function getCmKCode()
	{
		try
		{
			while (true)
			{
				$sCmKCode = 'C'.Str::random('numeric',8);
				$result1 = DB::select()->from('College_Master')->where('cmKCode',$sCmKCode)->execute()->as_array();
				if (empty($result1))
				{
					break;
				}
			}
			return $sCmKCode;
		}
		catch (Exception $e)
		{
			throw $e;
		}
	}

}
