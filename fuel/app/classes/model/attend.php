<?php
class Model_Attend extends \Model
{
	public static function getAttendCalendarFromClass($sCtID = null,$aAndWhere = null,$aOrWhere = null,$sSort = 'asc')
	{
		$aSubWhere = array('ab.ctID' => DB::expr('ac.ctID'),'ab.abDate' => DB::expr('ac.abDate'), 'ab.acNO' => DB::expr('ac.acNO'), 'ab.amAbsence' => 0);
		$subquery = DB::select(DB::expr('count(ab.no)'))->from(array('AttendBook_View','ab'))
			->where($aSubWhere)
			->compile();
		$subquery = '('.$subquery.') AS `abNum`';

		$query = DB::select_array(array('ac.*',DB::expr('st_x(`ag`.`agLatLon`) AS `agLon`'),DB::expr('st_y(`ag`.`agLatLon`) AS `agLat`'),'ag.agLatLon',DB::expr($subquery)))
			->from(array('AttendCalendar_Table','ac'))
			->join(array('AttendCalendarGeometry_Table','ag'),'LEFT')
			->on('ac.no','=','ag.no')
			->where('ac.ctID',$sCtID)
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
		$query->order_by('ac.abDate',$sSort);
		$query->order_by('ac.acAStart',$sSort);
		$query->order_by('ac.acStart',$sSort);
		$query->order_by('ac.acNO',$sSort);
		$result = $query->execute();
		return $result;
	}
	public static function getAttendCalendarActive($sCtID = null)
	{
		$aSubWhere = array('ab.ctID' => DB::expr('ac.ctID'),'ab.abDate' => DB::expr('ac.abDate'), 'ab.acNO' => DB::expr('ac.acNO'), 'ab.amAbsence' => 0);
		$subquery = DB::select(DB::expr('count(ab.no)'))->from(array('AttendBook_View','ab'))
			->where($aSubWhere)
			->compile();
		$subquery = '('.$subquery.') AS `abNum`';

		$query = DB::select_array(array('ac.*',DB::expr('st_x(`ag`.`agLatLon`) AS `agLon`'),DB::expr('st_y(`ag`.`agLatLon`) AS `agLat`'),'ag.agLatLon',DB::expr($subquery)))
			->from(array('AttendCalendar_Table','ac'))
			->join(array('AttendCalendarGeometry_Table','ag'),'LEFT')
			->on('ac.no','=','ag.no')
			->where('ac.ctID',$sCtID)
			->where('ac.abDate',date('Y-m-d'))
			->where('ac.acAStart',CL_DATETIME_DEFAULT)
			->where('ac.acAEnd','!=',CL_DATETIME_DEFAULT)
		;
		$result = $query->execute();
		return $result;
	}
	public static function getAttendCalendarFromNO($iNO = null,$aAndWhere = null)
	{
		$query = DB::select_array()->from('AttendCalendar_View');
		$query->where('no',$iNO);
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

	public static function insertAttend($aInsert = null, $aLatLon = null)
	{
		if (is_null($aInsert))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		if ($aInsert['acGIS'] && is_null($aLatLon))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			// 最大NOを取得
			$iAcNO = self::getAcNO($aInsert['ctID']) + 1;
			$aInsert['acNO'] = $iAcNO;
			$result = DB::insert('AttendCalendar_Table')->set($aInsert)->execute();
			$iNO = $result[0];
			$aBookInsert['columns'] = array(
				'ctID',
				'abDate',
				'acNO',
				'stID',
				'abStName',
				'abStNO',
			);
			$result = Model_Student::getStudentFromClass($aInsert['ctID']);
			if (count($result))
			{
				$res = $result->as_array();
				$query = DB::insert('AttendBook_Table');
				$query->columns($aBookInsert['columns']);
				foreach ($res as $r)
				{
					$a = array(
						$aInsert['ctID'],
						$aInsert['abDate'],
						$aInsert['acNO'],
						$r['stID'],
						$r['stName'],
						$r['stNO'],
					);
					$query->values($a);
				}
				$result = $query->execute();
			}

			if (!is_null($aLatLon))
			{
				$result = DB::delete('AttendCalendarGeometry_Table')->where('no','=',$iNO)->execute();

				$query = DB::insert('AttendCalendarGeometry_Table');
				$query->columns(array('no','agLatLon'));
				$query->values(array($iNO,DB::expr('GeomFromText("POINT('.$aLatLon['lon'].' '.$aLatLon['lat'].')")')));
				$result = $query->execute();

				$result = DB::delete('ClassGeometry_Table')->where('ctID','=',$aInsert['ctID'])->execute();

				$query = DB::insert('ClassGeometry_Table');
				$query->columns(array('ctID','ctLatLon'));
				$query->values(array($aInsert['ctID'],DB::expr('GeomFromText("POINT('.$aLatLon['lon'].' '.$aLatLon['lat'].')")')));
				$result = $query->execute();
			}
			DB::commit_transaction();
			// クエリの結果を返す
			return $iAcNO;
		}
		catch (Exception $e)
		{
			// 未決のトランザクションクエリをロールバックする
			DB::rollback_transaction();
			throw $e;
		}
	}

	public static function updateAttend($aUpdate = null,$aActive = null,$aLatLon = null)
	{
		if (is_null($aUpdate) || is_null($aActive))
		{
			throw new Exception('処理に必要な情報がありません');
		}
			try
		{
			DB::start_transaction();
			$query = DB::update('AttendCalendar_Table');
			$query->and_where('no','=',$aActive['no']);
			$query->set($aUpdate);
			$result = $query->execute();

			if (!is_null($aLatLon))
			{
				$result = DB::delete('AttendCalendarGeometry_Table')->where('no','=',$aActive['no'])->execute();

				$query = DB::insert('AttendCalendarGeometry_Table');
				$query->columns(array('no','agLatLon'));
				$query->values(array($aActive['no'],DB::expr('GeomFromText("POINT('.$aLatLon['lon'].' '.$aLatLon['lat'].')")')));
				$result = $query->execute();

				$result = DB::delete('ClassGeometry_Table')->where('ctID','=',$aActive['ctID'])->execute();

				$query = DB::insert('ClassGeometry_Table');
				$query->columns(array('ctID','ctLatLon'));
				$query->values(array($aActive['ctID'],DB::expr('GeomFromText("POINT('.$aLatLon['lon'].' '.$aLatLon['lat'].')")')));
				$result = $query->execute();
			}

			if (isset($aUpdate['abDate']) && $aUpdate['abDate'] != $aActive['abDate'])
			{
				$result = DB::update('AttendBook_Table')
					->where('ctID','=',$aActive['ctID'])
					->where('acNO','=',$aActive['acNO'])
					->set(array(
						'abDate' => $aUpdate['abDate'],
					))
					->execute();
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


	public static function updateAttendBatch($aUpdate = null,$aAndWhere = null,$aOrWhere = null)
	{
		if (is_null($aUpdate))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			$query = DB::update('AttendCalendar_Table');
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


	public static function deleteAttend($iNO = null,$aActive = null)
	{
		if (is_null($iNO) || is_null($aActive))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();

			$query = DB::delete('AttendBook_Table');
			$query->where('ctID',$aActive['ctID']);
			$query->where('abDate',$aActive['abDate']);
			$query->where('acNO',$aActive['acNO']);
			$result = $query->execute();

			$query = DB::delete('AttendCalendar_Table');
			$query->where('no',$iNO);
			$result = $query->execute();

			$query = DB::delete('AttendCalendarGeometry_Table');
			$query->where('no',$iNO);
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


	public static function insertAttendFromCSV($aIns = null, $aClass = null)
	{
		if (is_null($aIns) || is_null($aClass))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		$sCtID = $aClass['ctID'];
		$aLatLon = array('lat'=>$aClass['ctLat'],'lon'=>$aClass['ctLon']);
		$aStu = null;
		$result = Model_Student::getStudentFromClass($sCtID);
		if (count($result))
		{
			$aStu = $result->as_array();
		}

		try
		{
			DB::start_transaction();

			foreach ($aIns as $aI)
			{
				// 最大NOを取得
				$iAcNO = self::getAcNO($sCtID) + 1;
				$aI['ctID'] = $sCtID;
				$aI['acNO'] = $iAcNO;

				$result = DB::insert('AttendCalendar_Table')->set($aI)->execute();
				$iNO = $result[0];

				$aColumns = array(
					'ctID',
					'abDate',
					'acNO',
					'stID',
					'abStName',
					'abStNO',
				);
				if (!is_null($aStu))
				{
					$query = DB::insert('AttendBook_Table');
					$query->columns($aColumns);
					foreach ($aStu as $aS)
					{
						$aB = array(
							$aI['ctID'],
							$aI['abDate'],
							$aI['acNO'],
							$aS['stID'],
							$aS['stName'],
							$aS['stNO'],
						);
						$query->values($aB);
					}
					$result = $query->execute();
				}

				if ($aI['acGIS'])
				{
					$result = DB::delete('AttendCalendarGeometry_Table')->where('no','=',$iNO)->execute();

					$query = DB::insert('AttendCalendarGeometry_Table');
					$query->columns(array('no','agLatLon'));
					$query->values(array($iNO,DB::expr('GeomFromText("POINT('.$aLatLon['lon'].' '.$aLatLon['lat'].')")')));
					$result = $query->execute();

					$result = DB::delete('ClassGeometry_Table')->where('ctID','=',$sCtID)->execute();

					$query = DB::insert('ClassGeometry_Table');
					$query->columns(array('ctID','ctLatLon'));
					$query->values(array($sCtID,DB::expr('GeomFromText("POINT('.$aLatLon['lon'].' '.$aLatLon['lat'].')")')));
					$result = $query->execute();
				}
			}
			DB::commit_transaction();
			// クエリの結果を返す
			return $iAcNO;
		}
		catch (Exception $e)
		{
			// 未決のトランザクションクエリをロールバックする
			DB::rollback_transaction();
			throw $e;
		}
	}


	public static function getAttendBookFromClass($sCtID = null,$aAndWhere = null,$aOrWhere = null,$sSort = 'asc')
	{
		$query = DB::select_array()->from('AttendBook_View');
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
		$query->order_by('abDate',$sSort);
		$query->order_by('acNO',$sSort);
		$result = $query->execute();
		return $result;
	}

	public static function getAttendBookFromStudent($sStID = null,$aWhere = null,$aSort = null)
	{
		$query = DB::select_array()->from('AttendBook_View');
		$query->where('stID',$sStID);
		if (!is_null($aWhere))
		{
			foreach ($aWhere as $aW)
			{
				$query->where($aW[0],$aW[1],$aW[2]);
			}
		}
		if (!is_null($aSort))
		{
			foreach ($aSort as $aS)
			{
				$query->order_by($aS[0],$aS[1]);
			}
		}
		$result = $query->execute();
		return $result;
	}

	public static function getAttendMasterFromClass($sCtID = null,$aWhere = null,$aSort = null)
	{
		$query = DB::select_array()->from('AttendState_Master');
		$query->where('ctID',$sCtID);
		if (!is_null($aWhere))
		{
			foreach ($aWhere as $aW)
			{
				$query->where($aW[0],$aW[1],$aW[2]);
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

	public static function getAttendMasterFromClasses($aClass = null,$aWhere = null,$aSort = null)
	{
		if (is_null($aClass))
		{
			return null;
		}

		$aAMaster = null;
		foreach ($aClass as $aC)
		{
			$query = DB::select_array()->from('AttendState_Master');
			$query->where('ctID',$aC['ctID']);
			if (!is_null($aWhere))
			{
				foreach ($aWhere as $aW)
				{
					$query->where($aW[0],$aW[1],$aW[2]);
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
			if (count($result))
			{


				foreach($result as $v)
				{
					$aAMaster[$aC['ctID']][$v['amAttendState']] = $v;
				}
			}
		}

		return $aAMaster;
	}

	public static function insertAttendBook($aInsert = null,$aGeo = null)
	{
		if (is_null($aInsert))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			if (!is_null($aGeo))
			{
				$sGeo = 'GeomFromText("POINT('.$aGeo['lon'].' '.$aGeo['lat'].')")';
				$sLen = 'Glength(GeomFromText("LineString('.$aGeo['lon'].' '.$aGeo['lat'].','.$aGeo['agLon'].' '.$aGeo['agLat'].')"))*112120';

				$query = DB::insert('AttendBookGeometry_Table');
				$query->columns(array('agLatLon','agLength'));
				$query->values(array(
					DB::expr($sGeo),
					DB::expr($sLen),
				));
				list($last,$count) = $query->execute();
				$aInsert['agNO'] = $last;
			}
			$result = DB::insert('AttendBook_Table')->set($aInsert)->execute();
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

	public static function updateAttendBook($aWhere = null,$aUpdate = null,$aGeo = null)
	{
		if (is_null($aWhere) || is_null($aUpdate))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			if (!is_null($aGeo))
			{
				$sGeo = 'GeomFromText("POINT('.$aGeo['lon'].' '.$aGeo['lat'].')")';
				$sLen = 'Glength(GeomFromText("LineString('.$aGeo['lon'].' '.$aGeo['lat'].','.$aGeo['agLon'].' '.$aGeo['agLat'].')"))*112120';

				if (isset($aGeo['agNO']))
				{
					$query = DB::update('AttendBookGeometry_Table');
					$query->value('agLatLon',DB::expr($sGeo));
					$query->value('agLength',DB::expr($sLen));
					$query->and_where('no','=',$aGeo['agNO']);
					$result = $query->execute();
					$aUpdate['agNO'] = $aGeo['agNO'];
				} else {
					$query = DB::insert('AttendBookGeometry_Table');
					$query->columns(array('agLatLon','agLength'));
					$query->values(array(DB::expr($sGeo),DB::expr($sLen)));
					list($last,$count) = $query->execute();
					$aUpdate['agNO'] = $last;
				}
			}
			$query = DB::update('AttendBook_Table');
			foreach ($aWhere as $aW)
			{
				$query->where($aW[0],$aW[1],$aW[2]);
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

	public static function updateAttendMaster($sCtID = null,$aInput = null)
	{
		if (is_null($sCtID) || is_null($aInput))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();

			$result = DB::delete('AttendState_Master')->and_where('ctID','=',$sCtID)->execute();

			foreach($aInput as $i => $aR)
			{
				$aIns = $aR;
				$aIns['ctID'] = $sCtID;
				$aIns['amAttendState'] = $i;
				$aIns['amDate'] = date('YmdHis');
				$result = DB::insert('AttendState_Master')->set($aIns)->execute();
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

	private static function getAcNO($sCtID = null)
	{
		try
		{
			$query = DB::select(DB::expr('MAX(`acNO`) AS maxno'))->from('AttendCalendar_Table');
			$query->where('ctID',$sCtID);
			$result = $query->execute()->as_array();
			return (int)$result[0]['maxno'];
		}
		catch (Exception $e)
		{
			throw $e;
		}
	}
}
