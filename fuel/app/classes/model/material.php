<?php
class Model_Material extends \Model
{
	public static function getMaterialCategoryFromClass($sCtID = null,$aAndWhere = null,$aOrWhere = null,$aSort = null)
	{
		$query = DB::select_array()->from('MaterialCategory_View');
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

	public static function getMaterialCategoryFromID($sID = null,$aAndWhere = null)
	{
		$query = DB::select_array()->from('MaterialCategory_View');
		$query->where('mcID',$sID);
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

	public static function insertMaterialCategory($aInsert = null)
	{
		if (is_null($aInsert))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			$sMcID = self::getMaterialCategoryID();
			$aInsert['mcID'] = $sMcID;
			$aInsert['mcSort'] = self::getMaterialCategorySort($aInsert['ctID']);
			$result = DB::insert('MaterialCategory_Table')->set($aInsert)->execute();
			DB::commit_transaction();
			// クエリの結果を返す
			return $sMcID;
		}
		catch (Exception $e)
		{
			// 未決のトランザクションクエリをロールバックする
			DB::rollback_transaction();
			throw $e;
		}
	}

	public static function updateMaterialCategory($aUpdate = null,$aAndWhere = null,$aOrWhere = null)
	{
		if (is_null($aUpdate))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			$query = DB::update('MaterialCategory_Table');
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

	public static function deleteMaterialCategory($sID = null,$aActive = null,$aMaterial = null)
	{
		if (is_null($sID) || is_null($aActive))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();

			foreach ($aMaterial as $aM)
			{
				$result = DB::delete('File_Table')->where('fID',$aM['fID'])->execute();
			}
			$result = DB::delete('Material_Table')->where('mcID',$sID)->execute();
			$result = DB::delete('MaterialAlready_Table')->where('mcID',$sID)->execute();
			$result = DB::delete('MaterialCategory_Table')->where('mcID',$sID)->execute();

			$query = DB::update('MaterialCategory_Table');
			$query->and_where('ctID',$aActive['ctID']);
			$query->and_where('mcSort','>',$aActive['mcSort']);
			$query->set(array('mcSort'=>DB::expr('mcSort - 1')));
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

	public static function sortMaterialCategory($aMCategory = null,$sSort = null)
	{
		if (is_null($aMCategory) || is_null($sSort))
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
			$query = DB::update('MaterialCategory_Table');
			$query->and_where('ctID',$aMCategory['ctID']);
			$query->and_where('mcSort',$aMCategory['mcSort']+$iWhere);
			$query->set(array('mcSort'=>DB::expr('mcSort'.$iUp1)));
			$result = $query->execute();
			$query = DB::update('MaterialCategory_Table');
			$query->and_where('mcID',$aMCategory['mcID']);
			$query->set(array('mcSort'=>DB::expr('mcSort'.$iUp2)));
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

	public static function getMaterial($aAndWhere = null,$aOrWhere = null,$aSort = null)
	{
		$query = DB::select_array(
			array(
				'mt.*',
				DB::expr('ft.fName AS fName'),
				DB::expr('ft.fSize AS fSize'),
				DB::expr('ft.fExt AS fExt'),
				DB::expr('ft.fContentType AS fContentType'),
				DB::expr('ft.fFileType AS fFileType'),
				DB::expr('ft.fPath AS fPath'),
				DB::expr('ft.fUserType AS fUserType'),
				DB::expr('ft.fUser AS fUser'),
				DB::expr('ft.fDate AS fDate'),
				'tt.ttName',
			)
		)
		->from(array('Material_Table','mt'))
		->join(array('Teacher_Table','tt'),'LEFT')
		->on('mt.mID','=','tt.ttID')
		->join(array('File_Table','ft'),'LEFT')
		->on('mt.fID','=','ft.fID')
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

	public static function insertMaterial($aInsert = null)
	{
		if (is_null($aInsert))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			$aInsert['mSort'] = self::getMaterialSort($aInsert['mcID']);
			$result = DB::insert('Material_Table')->set($aInsert)->execute();
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

	public static function updateMaterial($aUpdate = null,$aAndWhere = null,$aOrWhere = null)
	{
		if (is_null($aUpdate))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			$query = DB::update('Material_Table');
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

	public static function deleteMaterial($aActive = null)
	{
		if (is_null($aActive))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			$result = DB::delete('Material_Table')
				->where('mcID',$aActive['mcID'])
				->and_where('mNO',$aActive['mNO'])
				->execute();

			$result = DB::update('Material_Table')
				->where('mcID',$aActive['mcID'])
				->and_where('mSort','>',$aActive['mSort'])
				->set(array('mSort'=>DB::expr('mSort - 1')))
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
			'ma.mcID',
			DB::expr('count(ma.mNO) as aCnt')
		));
		$query->from(array('MaterialAlready_Table','ma'))
			->join(array('Material_Table','mt'))
			->on('ma.mNO','=','mt.mNO')
			->where('mt.mPublic','=',1)
			->where('ma.stID','=',$sStID);
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
		$query->group_by('ma.mcID');
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
		$result = self::getMaterial(array(array('mt.mcID','=',$aCate['mcID'])),null,array('mt.mSort'=>'desc'));
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


	private static function getMaterialCategoryID()
	{
		try
		{
			while (true)
			{
				$sMcID = 'm'.Str::random('numeric',9);
				$result = DB::select()->from('MaterialCategory_View')->where('mcID',$sMcID)->execute()->as_array();
				if (empty($result))
				{
					break;
				}
			}
			return $sMcID;
		}
		catch (Exception $e)
		{
			throw $e;
		}
	}
	private static function getMaterialCategorySort($sCtID = null)
	{
		$iSort = 1;
		$result = DB::select(DB::expr('MAX(mcSort) AS mcMax'))->from('MaterialCategory_View')->where('ctID',$sCtID)->execute();
		if (count($result))
		{
			$aRes = $result->current();
			$iSort = $aRes['mcMax'] + 1;
		}
		return $iSort;
	}
	private static function getMaterialSort($sMcID = null)
	{
		$iSort = 1;
		$result = DB::select(DB::expr('MAX(mSort) AS mMax'))->from('Material_View')->where('mcID',$sMcID)->execute();
		if (count($result))
		{
			$aRes = $result->current();
			$iSort = $aRes['mMax'] + 1;
		}
		return $iSort;
	}
}
