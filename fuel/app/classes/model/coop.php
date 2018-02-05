<?php
class Model_Coop extends \Model
{
	public static function getCoopCategoryFromClass($sCtID = null,$aAndWhere = null,$aOrWhere = null,$aSort = null)
	{
		$query = DB::select_array()->from('CoopCategory_View');
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

	public static function getCoopCategoryFromID($sID = null,$aAndWhere = null)
	{
		$query = DB::select_array()->from('CoopCategory_View');
		$query->where('ccID',$sID);
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

	public static function insertCoopCategory($aInsert = null)
	{
		if (is_null($aInsert))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			$sCcID = self::getCoopCategoryID();
			$aInsert['ccID'] = $sCcID;
			$aInsert['ccSort'] = self::getCoopCategorySort($aInsert['ctID']);
			$result = DB::insert('CoopCategory_Table')->set($aInsert)->execute();

			if ($aInsert['ccStuRange'] == 2)
			{
				$result = Model_Student::getStudentFromClass($aInsert['ctID']);
				if (count($result))
				{
					$aStu = $result->as_array('stID');
					$result = self::entryCoopsStudents(array($sCcID),$aStu);
				}
			}

			DB::commit_transaction();
			// クエリの結果を返す
			return $sCcID;
		}
		catch (Exception $e)
		{
			// 未決のトランザクションクエリをロールバックする
			DB::rollback_transaction();
			throw $e;
		}
	}

	public static function updateCoopCategory($aUpdate = null,$aAndWhere = null,$aOrWhere = null)
	{
		if (is_null($aUpdate))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			$query = DB::update('CoopCategory_Table');
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

	public static function updateCoopCategorySize($iSize = 0,$aAndWhere = null,$aOrWhere = null)
	{
		if (!$iSize)
		{
			return true;
		}
		try
		{
			DB::start_transaction();
			$query = DB::update('CoopCategory_Table');
			$query->set(array('ccTotalSize' => DB::expr('ccTotalSize+'.$iSize)));
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

	public static function deleteCoopCategory($sID = null,$aActive = null,$aCoop = null)
	{
		if (is_null($sID) || is_null($aActive))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();

			foreach ($aCoop as $aM)
			{
				for($i = 1; $i <= 3; $i++)
				{
					if ($aM['fID'.$i] != '')
					{
						$result = DB::delete('File_Table')->where('fID',$aM['fID'.$i])->execute();
					}
				}
			}
			$result = DB::delete('CoopItem_Table')->where('ccID',$sID)->execute();
			$result = DB::delete('CoopAlready_Table')->where('ccID',$sID)->execute();
			$result = DB::delete('CoopStudent_Table')->where('ccID',$sID)->execute();
			$result = DB::delete('CoopCategory_Table')->where('ccID',$sID)->execute();

			$query = DB::update('CoopCategory_Table');
			$query->and_where('ctID',$aActive['ctID']);
			$query->and_where('ccSort','>',$aActive['ccSort']);
			$query->set(array('ccSort'=>DB::expr('ccSort - 1')));
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

	public static function sortCoopCategory($aCCategory = null,$sSort = null)
	{
		if (is_null($aCCategory) || is_null($sSort))
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
			$query = DB::update('CoopCategory_Table');
			$query->and_where('ctID',$aCCategory['ctID']);
			$query->and_where('ccSort',$aCCategory['ccSort']+$iWhere);
			$query->set(array('ccSort'=>DB::expr('ccSort'.$iUp1)));
			$result = $query->execute();
			$query = DB::update('CoopCategory_Table');
			$query->and_where('ccID',$aCCategory['ccID']);
			$query->set(array('ccSort'=>DB::expr('ccSort'.$iUp2)));
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

	public static function getCoopStudents($aAndWhere = null,$aOrWhere = null,$aSort = null)
	{
		$query = DB::select_array()->from('CoopStudent_View');
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

	public static function entryCoopsStudents($aCID = null, $aStu = null)
	{
		if (is_null($aCID) || is_null($aStu))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		$sDate = date('YmdHis');
		try
		{
			DB::start_transaction();

			$query = \DB::insert('CoopStudent_Table')
				->columns(array(
						'ccID',
						'stID',
						'csDate',
					)
				);

			foreach ($aCID as $sCID)
			{
				foreach ($aStu as $aS)
				{
					$sSID = $aS['stID'];
					$query->values(array($sCID,$sSID,$sDate));
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

	public static function removeCoopsStudents($aCID = null, $aStu = null)
	{
		if (is_null($aCID) || is_null($aStu))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();

			$aSKeys = array_keys($aStu);
			$aWhere = array(array('ccID','IN',$aCID),array('stID','IN',$aSKeys));
			$result = DB::delete('CoopStudent_Table')->where($aWhere)->execute();

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

	public static function changeStudentRange($aCCategory = null, $iRange = null)
	{
		if (is_null($aCCategory) || is_null($iRange))
		{
			throw new Exception('処理に必要な情報がありません');
		}

		$sCcID = $aCCategory['ccID'];
		$sCtID = $aCCategory['ctID'];

		try
		{
			DB::start_transaction();

			$result = DB::update('CoopCategory_Table')
				->and_where('ccID',$sCcID)
				->set(array('ccStuRange'=>$iRange))
				->execute();

			if ($iRange == 0)
			{
				$result = DB::delete('CoopStudent_Table')
					->and_where('ccID',$sCcID)
					->execute();
			}
			elseif ($iRange == 2)
			{
				$sDate = date('YmdHis');

				$result = DB::delete('CoopStudent_Table')
					->and_where('ccID',$sCcID)
					->execute();

				$result = Model_Student::getStudentFromClass($sCtID);
				if (count($result))
				{
					$aStu = $result->as_array('stID');
					$result = self::entryCoopsStudents(array($sCcID),$aStu);
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


	public static function getCoop($aAndWhere = null,$aOrWhere = null,$aSort = null,$aLimit = null,$aOrGroup = null)
	{
		$query = DB::select_array(
			array(
				'ci.*',
				DB::expr('ft1.fName AS fName1'), DB::expr('ft1.fSize AS fSize1'), DB::expr('ft1.fExt AS fExt1'), DB::expr('ft1.fContentType AS fContentType1'), DB::expr('ft1.fFileType AS fFileType1'), DB::expr('ft1.fPath AS fPath1'),
				DB::expr('ft2.fName AS fName2'), DB::expr('ft2.fSize AS fSize2'), DB::expr('ft2.fExt AS fExt2'), DB::expr('ft2.fContentType AS fContentType2'), DB::expr('ft2.fFileType AS fFileType2'), DB::expr('ft2.fPath AS fPath2'),
				DB::expr('ft3.fName AS fName3'), DB::expr('ft3.fSize AS fSize3'), DB::expr('ft3.fExt AS fExt3'), DB::expr('ft3.fContentType AS fContentType3'), DB::expr('ft3.fFileType AS fFileType3'), DB::expr('ft3.fPath AS fPath3'),
				'tt.ttName', 'st.stName', 'at.atName',
//				DB::expr('(ifnull(`ft1`.`fSize`,0) + ifnull(`ft2`.`fSize`,0) + ifnull(`ft3`.`fSize`,0)) AS fSumSize'),
			)
		)
			->from(array('CoopItem_Table','ci'))
			->join(array('Teacher_Table','tt'),'LEFT')
			->on('ci.cID','=','tt.ttID')
			->join(array('Student_Table','st'),'LEFT')
			->on('ci.cID','=','st.stID')
			->join(array('Assistant_Table','at'),'LEFT')
			->on('ci.cID','=','at.atID')
			->join(array('File_Table','ft1'),'LEFT')
			->on('ci.fID1','=','ft1.fID')
			->join(array('File_Table','ft2'),'LEFT')
			->on('ci.fID2','=','ft2.fID')
			->join(array('File_Table','ft3'),'LEFT')
			->on('ci.fID3','=','ft3.fID')
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
		if (!is_null($aOrGroup))
		{
			$query->and_where_open();
			foreach ($aOrGroup as $aW)
			{
				$query->or_where($aW[0],$aW[1],$aW[2]);
			}
			$query->and_where_close();
		}
		/*
		if (!is_null($aLimit))
		{
			$query->offset($aLimit[0]);
			$query->limit($aLimit[1]);
		}
		*/
		$result = $query->execute();
		return $result;
	}

	public static function insertCoop($aInsert = null)
	{
		if (is_null($aInsert))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			if ($aInsert['cRoot'] == 0)
			{
				$aInsert['cSort'] = self::getCoopSort($aInsert['ccID']);
			}
			else if ($aInsert['cBranch'] == 0)
			{
				$aInsert['cSort'] = self::getCoopChildSort($aInsert['ccID'],$aInsert['cRoot']);
			}
			$result = DB::insert('CoopItem_Table')->set($aInsert)->execute();
			$iNO = $result[0];
			$result = DB::update('CoopCategory_Table')
				->set(array(
					'ccLastDate'  => $aInsert['cDate'],
				))
				->where('ccID',$aInsert['ccID'])
				->execute();

			$result = DB::insert('CoopAlready_Table')
				->set(array(
					'cNO' => $iNO,
					'caID' => $aInsert['cID'],
					'ccID' => $aInsert['ccID'],
					'caDate' => date('YmdHis'),
				))
				->execute();

			if (preg_match('/^s/', $aInsert['cID']))
			{
				$result = DB::update('CoopItem_Table')
					->where('cNO','=',$iNO)
					->set(array('cAlreadyNum'=>DB::expr('cAlreadyNum + 1')))
					->execute();
			}

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

	public static function updateCoop($aUpdate = null,$aAndWhere = null,$aOrWhere = null,$sCcID = null)
	{
		if (is_null($aUpdate) || is_null($sCcID))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			$query = DB::update('CoopItem_Table');
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

			$result = DB::update('CoopCategory_Table')
			->set(array(
				'ccLastDate'  => date('YmdHis'),
			))
			->where('ccID',$sCcID)
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

	public static function deleteCoop($aCoop = null)
	{
		if (is_null($aCoop))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			$result = DB::delete('CoopItem_Table')
				->where('cNO',$aCoop['cNO'])
				->execute();

			$aWhere = array();
			if ($aCoop['cRoot'] > 0)
			{
				$aWhere[] = array('cRoot','=',$aCoop['cRoot']);
				$aWhere[] = array('cBranch','=',$aCoop['cNO']);
			}
			else
			{
				$aWhere[] = array('cRoot','=',$aCoop['cNO']);
			}
			if (count($aWhere))
			{
				$result = DB::delete('CoopItem_Table')
					->where($aWhere)
					->execute();
			}

			if ($aCoop['cSort'] > 0)
			{
				$query = DB::update('CoopItem_Table');
				$query->and_where('ccID',$aCoop['ccID']);
				$query->and_where('cRoot',0);
				$query->and_where('cSort','>',$aCoop['cSort']);
				$query->set(array('cSort'=>DB::expr('cSort - 1')));
				$res = $query->execute();
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

	public static function sortCoop($aCoop = null,$sSort = null)
	{
		if (is_null($aCoop) || is_null($sSort))
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
			$query = DB::update('CoopItem_Table');
			$query->and_where('ccID',$aCoop['ccID']);
			$query->and_where('cRoot',0);
			$query->and_where('cSort',$aCoop['cSort']+$iWhere);
			$query->set(array('cSort'=>DB::expr('cSort'.$iUp1)));
			$result = $query->execute();
			$query = DB::update('CoopItem_Table');
			$query->and_where('ccID',$aCoop['ccID']);
			$query->and_where('cNO',$aCoop['cNO']);
			$query->set(array('cSort'=>DB::expr('cSort'.$iUp2)));
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

	public static function sortChildCoop($aCoop = null)
	{
		if (is_null($aCoop))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			$query = DB::update('CoopItem_Table');
			$query->and_where('ccID',$aCoop['ccID']);
			$query->and_where('cRoot',$aCoop['cRoot']);
			$query->and_where('cBranch',0);
			$query->and_where('cSort','<',$aCoop['cSort']);
			$query->set(array('cSort'=>DB::expr('cSort + 1')));
			$result = $query->execute();
			$query = DB::update('CoopItem_Table');
			$query->and_where('ccID',$aCoop['ccID']);
			$query->and_where('cNO',$aCoop['cNO']);
			$query->set(array('cSort'=>1));
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

	public static function getCoopAlreadyCountFromUser($sID = null, $aAndWhere = null, $aOrWhere = null)
	{
		if (is_null($sID))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		$query = DB::select_array(array(
			'ccID',
			DB::expr('count(cNO) as aCnt')
		));
		$query->from('CoopAlready_Table');
		$query->where('caID','=',$sID);
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
		$query->group_by('ccID');
		$result = $query->execute();
		return $result;
	}

	public static function getCoopAlready($aAndWhere = null, $aOrWhere = null)
	{
		$query = DB::select_array()->from('CoopAlready_Table');
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

	public static function getCoopCategoryAlreadyNum($aAndWhere = null, $aOrWhere = null)
	{
		$query = DB::select('ccID',array(DB::expr('count(cNO)'),'cNum'))->from('CoopAlready_Table');
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
		$query->group_by('ccID');
		$result = $query->execute();
		return $result;
	}

	public static function insertCoopAlready($aInsert = null)
	{
		if (is_null($aInsert))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			$result = DB::insert('CoopAlready_Table')
				->set($aInsert)
				->execute()
			;
			$result = DB::update('CoopItem_Table')
				->and_where('cNO',$aInsert['cNO'])
				->set(array('cAlreadyNum'=>DB::expr('cAlreadyNum + 1')))
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

	public static function setCoopAlready($sID = null, $sCcID =null, $aParents = null)
	{
		if (is_null($sID) || is_null($sCcID))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		if (is_null($aParents))
		{
			return;
		}
		try
		{
			DB::start_transaction();

			$aAlready = null;
			$result = DB::select_array()
				->from('CoopAlready_Table')
				->where('caID','=',$sID)
				->where('ccID','=',$sCcID)
				->execute();
			if (count($result))
			{
				$aAlready = $result->as_array('cNO');
			}

			$sDate = date('YmdHis');
			$aValues = null;
			$aNums = null;
			foreach ($aParents as $aP)
			{
				if (!isset($aAlready[$aP['cNO']]))
				{
					$aNums[] = $aP['cNO'];
					$aValues[] = array($aP['cNO'],$sID,$sCcID,$sDate);
				}
				$result = DB::select_array()->from('CoopItem_Table')
					->where('cRoot','=',$aP['cNO'])
					->execute();
				if (count($result))
				{
					foreach ($result as $aC)
					{
						if (!isset($aAlready[$aC['cNO']]))
						{
							$aNums[] = $aC['cNO'];
							$aValues[] = array($aC['cNO'],$sID,$sCcID,$sDate);
						}
					}
				}
			}
			if (is_null($aValues))
			{
				return;
			}
			$result = DB::insert('CoopAlready_Table')
				->columns(array('cNO','caID','ccID','caDate'))
				->values($aValues)
				->execute()
			;

			if (preg_match('/^s/', $sID))
			{
				$result = DB::update('CoopItem_Table')
					->where('cNO','IN',$aNums)
					->set(array('cAlreadyNum'=>DB::expr('cAlreadyNum + 1')))
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

	public static function copyCoopCategory($aCate = null, $aSelClass = null)
	{
		if (is_null($aCate) || is_null($aSelClass))
		{
			throw new Exception('処理に必要な情報がありません');
		}

		$sNow = date('YmdHis');

		# ベースデータ生成
		$aCBase = $aCate;
		$aCBase['ccSort'] = 0;
		$aCBase['ccDate'] = $sNow;
		$aCBase['ccLastDate'] = CL_DATETIME_DEFAULT;

		unset($aCBase['ccStuNum']);
		unset($aCBase['ccItemNum']);
		unset($aCBase['ccCharNum']);
		unset($aCBase['ccTotalSize']);

		try
		{
			DB::start_transaction();

			foreach ($aSelClass as $sCtID => $aC)
			{
				$sCcID = self::getCoopCategoryID();

				$aCBase['ccID'] = $sCcID;
				$aCBase['ctID'] = $sCtID;
				$aCBase['ccSort'] = self::getCoopCategorySort($sCtID);

				$result = DB::insert('CoopCategory_Table')
					->set($aCBase)
					->execute()
				;

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

	private static function getCoopCategoryID()
	{
		try
		{
			while (true)
			{
				$sCcID = 'm'.Str::random('numeric',9);
				$result = DB::select()->from('CoopCategory_Table')->where('ccID',$sCcID)->execute()->as_array();
				if (empty($result))
				{
					break;
				}
			}
			return $sCcID;
		}
		catch (Exception $e)
		{
			throw $e;
		}
	}
	private static function getCoopCategorySort($sCtID = null)
	{
		$iSort = 1;
		$result = DB::select(DB::expr('MAX(ccSort) AS mcMax'))->from('CoopCategory_Table')->where('ctID',$sCtID)->execute();
		if (count($result))
		{
			$aRes = $result->current();
			$iSort = $aRes['mcMax'] + 1;
		}
		return $iSort;
	}
	private static function getCoopSort($sCcID = null)
	{
		$iSort = 1;
		$result = DB::select(DB::expr('MAX(cSort) AS mcMax'))->from('CoopItem_Table')->where('ccID',$sCcID)->and_where('cRoot',0)->execute();
		if (count($result))
		{
			$aRes = $result->current();
			$iSort = $aRes['mcMax'] + 1;
		}
		return $iSort;
	}
	private static function getCoopChildSort($sCcID = null, $iRoot = null)
	{
		$iSort = 1;
		$result = DB::select(DB::expr('MAX(cSort) AS mcMax'))->from('CoopItem_Table')->where('ccID',$sCcID)->and_where('cRoot',$iRoot)->and_where('cBranch',0)->execute();
		if (count($result))
		{
			$aRes = $result->current();
			$iSort = $aRes['mcMax'] + 1;
		}
		return $iSort;
	}
}
