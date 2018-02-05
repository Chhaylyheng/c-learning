<?php
class Model_Student extends \Model
{
	public static function getStudentFromPostLogin($sLogin = null,$sPass = null)
	{
		if (is_null($sLogin) || is_null($sPass))
		{
			throw new Exception('処理に必要な情報がありません');
		}

		try
		{
			// ログインIDで判定
			$query = DB::select_array()->from('Student_Table');
			$query->where('stPass',sha1($sPass));
			$query->and_where('stStatus',1);
			$query->and_where(function($query) use ($sLogin)
			{
				$query->where('stLogin',$sLogin);
				$query->or_where('stMail',$sLogin);
			});
			$result = $query->execute();

			if (!count($result))
			{
				DB::start_transaction();
				$query = DB::update('Student_Table');
				$query->value('stPassMiss', DB::expr('`stPassMiss`+1'));
				$query->where('stStatus',1);
				$query->and_where(function($query) use ($sLogin)
				{
					$query->where('stLogin',$sLogin);
					$query->or_where('stMail',$sLogin);
				});
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

	public static function setLoginUpdate($aUser = null, $bTZ = false)
	{
		if (is_null($aUser))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			$query = DB::update('Student_Table');
			$query->value('stLoginNum', DB::expr('`stLoginNum`+1'));
			$query->value('stLastLoginDate', DB::expr('`stLoginDate`'));
			$query->value('stLoginDate', DB::expr('NOW()'));
			$query->value('stUA', Input::user_agent());
			$query->value('stHash', sha1($aUser['stLogin'].$aUser['stPass']));
			if ($bTZ)
			{
				$query->value('stTimeZone', $aUser['stTimeZone']);
			}
			$query->where('stID',$aUser['stID']);
			$result = $query->execute();
			DB::commit_transaction();

			$result = self::getStudentFromID($aUser['stID']);
			return $result;
		}
		catch (Exception $e)
		{
			// 未決のトランザクションクエリをロールバックする
			DB::rollback_transaction();
			throw $e;
		}
	}

	public static function getStudentFromLoginID($sLogin = null)
	{
		if (is_null($sLogin))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			// ログインIDで判定
			$query = DB::select_array()->from('Student_Table');
			$query->where('stLogin',$sLogin);
			$query->and_where('stStatus',1);
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

	public static function getStudentMailLogin($sLogin = null)
	{
		if (is_null($sLogin))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			// ログインIDで判定
			$query = DB::select_array()->from('Student_Table');
			$query->and_where('stStatus',1);
			$query->and_where(function($query) use ($sLogin)
			{
				$query->where('stLogin',$sLogin);
				$query->or_where('stMail',$sLogin);
			});
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

	public static function getStudentFromMail($sMail = null, $sStID = null)
	{
		$query = DB::select_array()->from('Student_Table')->where('stMail',$sMail);
		if (!is_null($sStID))
		{
			$query->where('stID','!=',$sStID);
		}
		$result = $query->execute();

		return $result;
	}
	public static function getStudentFromLogin($sLogin = null, $sStID = null)
	{
		$query = DB::select_array()->from('Student_Table')->where('stLogin',$sLogin);
		if (!is_null($sStID))
		{
			$query->where('stID','!=',$sStID);
		}
		$result = $query->execute();

		return $result;
	}
	public static function getStudentFromHash($sHash = null)
	{
		$query = DB::select_array()->from('Student_Table')
			->where('stHash',$sHash)
		;
		$result = $query->execute();

		return $result;
	}
	public static function getStudentFromID($sID = null)
	{
		$result = DB::select_array()->from('Student_Table')->where('stID',$sID)->execute();
		return $result;
	}
	public static function getStudentFromClass($sID = null,$aAndWhere = null,$aOrWhere = null, $aSort = null, $aOrGroup = null)
	{
/*
		$query = DB::select_array()->from('StudentClassList_View');
		if (!is_null($sID))
		{
			$query->where('ctID',$sID);
		}
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
				$query->and_where($aW[0],$aW[1],$aW[2]);
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
			foreach ($aSort as $sK => $sS)
			{
				$query->order_by($sK,$sS);
			}
		}
*/

		$aASubWhere = array('ab.ctID' => DB::expr('sp.ctID'), 'ab.stID' => DB::expr('sp.stID'), 'am.amAbsence'=>0);
		$subquery = DB::select(DB::expr('count(ab.no)'))
			->from(array('AttendBook_Table','ab'))
			->join(array('AttendState_Master','am'))
			->on('ab.ctID','=','am.ctID')
			->on('ab.amAttendState','=','am.amAttendState')
			->where($aASubWhere)
			->compile()
		;
		$sASub = '('.$subquery.') AS `abNum`';

		$aNSubWhere = array('ct.ctID' => DB::expr('sp.ctID'), 'sp.spAuth' => 1);
		$subquery = DB::select(DB::expr('count(sp.stID)'))
			->from(array('StudentPosition_Table','sp'))
			->where($aNSubWhere)
			->compile()
		;
		$sNSub = '('.$subquery.') AS `scNum`';

		$aWSubWhere = array('ct.ctID' => DB::expr('sp.ctID'), 'sp.spAuth' => 0);
		$subquery = DB::select(DB::expr('count(sp.stID)'))
			->from(array('StudentPosition_Table','sp'))
			->where($aWSubWhere)
			->compile()
		;
		$sWSub = '('.$subquery.') AS `scWaitNum`';

		$aGSubWhere = array(array('ct.ctID','=',DB::expr('qt.ctID')),array('qt.qbOpen','>',0));
		$subquery = DB::select(DB::expr('count(qt.qbID)'))
			->from(array('QuestBase_Table','qt'))
			->where($aGSubWhere)
			->compile()
		;
		$sGSub = '('.$subquery.') AS `ctGuestAuth`';

		$aTSubWhere = array('ct.ctID' => DB::expr('tp.ctID'));
		$subquery = DB::select(DB::expr('count(tp.ttID) - 1'))
			->from(array('TeacherPosition_Table','tp'))
			->where($aTSubWhere)
			->compile()
		;
		$sTSub = '('.$subquery.') AS `tpNum`';

		$query = DB::select_array(
			array(
				'st.*','ct.*',
				'cm.cmName','cm.cmAddress','cm.cmPref','cm.cmCity',
				DB::expr('st_x(`cg`.`ctLatLon`) AS `ctLon`'),
				DB::expr('st_y(`cg`.`ctLatLon`) AS `ctLat`'),
				DB::expr('ASTEXT(cg.ctLatLon) AS `ctLatLon`'),
				'gc.gtID',
				DB::expr($sASub),
				DB::expr($sNSub),
				DB::expr($sWSub),
				DB::expr($sGSub),
				DB::expr($sTSub),
			)
		)
			->from(array('StudentPosition_Table','sp'))
			->join(array('Student_Table','st'),'LEFT')
			->on('sp.stID','=','st.stID')
			->join(array('Class_Table','ct'),'LEFT')
			->on('sp.ctID','=','ct.ctID')
			->join(array('College_Master','cm'),'LEFT')
			->on('ct.cmKCode','=','cm.cmKCode')
			->join(array('ClassGeometry_Table','cg'),'LEFT')
			->on('sp.ctID','=','cg.ctID')
			->join(array('GroupCPos_Table','gc'),'LEFT')
			->on('ct.ctID','=','gc.ctID')
			->where(array('sp.spAuth'=>1))
		;
		if (!is_null($sID))
		{
			$query->where('sp.ctID','=',$sID);
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
			if ($aSort !== false)
			{
				foreach ($aSort as $sC => $sS)
				{
					$query->order_by($sC,$sS);
				}
			}
		}
		$result = $query->execute();
		return $result;
	}

	public static function getStudent($aAndWhere = null,$aOrWhere = null,$aSort = null)
	{
		$query = DB::select_array()->from('Student_View');
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

	public static function getStudentPosition($aAndWhere = null,$aOrWhere = null,$aSort = null)
	{
		$query = DB::select_array()->from('StudentPosition_Table');
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

	public static function getPreStudentFromHash($sHash = null)
	{
		$query = DB::select_array()->from('PreStudent_Table')->where('stHash',$sHash);
		$result = $query->execute();

		return $result;
	}
	public static function insertPreStudent($aInsert = null)
	{
		if (is_null($aInsert))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			$query = DB::delete('PreStudent_Table')->where('stMail',$aInsert['stMail'])->execute();
			$query = DB::insert('PreStudent_Table')->set($aInsert)->execute();
			DB::commit_transaction();
			// クエリの結果を返す
			return $query;
		}
		catch (Exception $e)
		{
			// 未決のトランザクションクエリをロールバックする
			DB::rollback_transaction();
			throw $e;
		}
	}

	public static function insertStudent($aInsert = null,$sCtID = null,$sGtID = null)
	{
		if (is_null($aInsert))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			// IDを生成
			$sStID = self::getStudentID();
			$aInsert['stID'] = $sStID;
			$query = DB::insert('Student_Table')->set($aInsert)->execute();
			$query = DB::delete('PreStudent_Table')->where('stMail',$aInsert['stMail'])->execute();

			if (!is_null($sCtID))
			{
				$result = Model_Class::entryClass($sCtID,$sStID);
			}
			else
			{
				if (!is_null($sGtID))
				{
					$aGIns = array(
						'gtID' => $sGtID,
						'stID' => $sStID,
						'gpDate' => date('YmdHis'),
					);
					$query = DB::insert('GroupSPos_Table')->set($aGIns)->execute();
				}
			}

			DB::commit_transaction();
			// クエリの結果を返す
			return $sStID;
		}
		catch (Exception $e)
		{
			// 未決のトランザクションクエリをロールバックする
			DB::rollback_transaction();
			throw $e;
		}
	}

	public static function insertStudentFromCSV($aCSV = null,$sCtID = null,$sGtID = null)
	{
		if (is_null($aCSV))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			$aIDs = null;

			foreach ($aCSV as $aS)
			{
				// IDを生成
				$sStID = self::getStudentID($aIDs);
				$aIDs[] = $sStID;

				// 登録データ生成
				$sFirst = null;
				if (trim($aS[1]))
				{
					$sFirst = $aS[1];
				}
				else
				{
					$sFirst = strtolower(Str::random('distinct', 8));
				}
				$sPass = sha1($sFirst);
				$sHash = sha1($aS[0].$sPass);

				$aInsert = array(
					'stID'            => $sStID,
					'stLogin'         => $aS[0],
					'stPass'          => $sPass,
					'stFirst'         => $sFirst,
					'stName'          => $aS[2],
					'stSex'           => (int)$aS[3],
					'stNO'            => $aS[4],
					'stDept'          => $aS[5],
					'stSubject'       => $aS[6],
					'stYear'          => $aS[7],
					'stClass'         => $aS[8],
					'stCourse'        => $aS[9],
					'stLoginNum'      => 0,
					'stLastLoginDate' => '00000000000000',
					'stLoginDate'     => '00000000000000',
					'stPassDate'      => '00000000',
					'stPassMiss'      => 0,
					'stHash'          => $sHash,
					'stStatus'        => 1,
					'stDate'          => date('YmdHis'),
				);

				$query = DB::insert('Student_Table')->set($aInsert)->execute();

				// 講義履修
				if (!is_null($sCtID))
				{
					$result = Model_Class::entryClass($sCtID,$sStID);
				}
				else
				{
					if (!is_null($sGtID))
					{
						$aSPos = array(
							'gtID' => $sGtID,
							'stID' => $sStID,
							'gpDate' => date('YmdHis'),
						);

						$result = DB::insert('GroupSPos_Table')
							->set($aSPos)
							->execute();
					}
				}
			}

			DB::commit_transaction();
			// クエリの結果を返す
			return $sStID;
		}
		catch (Exception $e)
		{
			// 未決のトランザクションクエリをロールバックする
			DB::rollback_transaction();
			throw $e;
		}
	}

	public static function updateStudentFromCSV($aUpdate = null,$aGroup = null)
	{
		if (is_null($aUpdate) || is_null($aGroup))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();

			foreach ($aUpdate as $sStID => $aS)
			{
				$aInsert = array(
					'stName'    => $aS[2],
					'stSex'     => (int)$aS[3],
					'stNO'      => $aS[4],
					'stDept'    => $aS[5],
					'stSubject' => $aS[6],
					'stYear'    => $aS[7],
					'stClass'   => $aS[8],
					'stCourse'  => $aS[9],
				);
				if (trim($aS[1]))
				{
					$aInsert['stFirst'] = $aS[1];
					$aInsert['stPass'] = sha1($aS[1]);
					$aInsert['stHash'] = sha1($aS[0].$aInsert['stPass']);
				}

				$query = DB::update('Student_Table')
					->set($aInsert)
					->where('stID','=',$sStID)
					->execute()
				;
			}

			DB::commit_transaction();
			return true;
		}
		catch (Exception $e)
		{
			// 未決のトランザクションクエリをロールバックする
			DB::rollback_transaction();
			throw $e;
		}
	}


	public static function updateStudent($sStID = null,$aUpdate = null)
	{
		if (is_null($sStID) || is_null($aUpdate))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			// 先生IDを生成
			$query = DB::update('Student_Table');
			$query->where('stID',$sStID);
			$query->set($aUpdate);
			$result = $query->execute();

			if (isset($aUpdate['stDeviceToken']))
			{
				$query = DB::update('Student_Table')
					->where('stID','!=',$sStID)
					->and_where('stAPP','=',$aUpdate['stApp'])
					->set(array('stApp'=>0, 'stDeviceToken'=>'', 'stDeviceID'=>''));

				if ($aUpdate['stDeviceID'] != '')
				{
					$query->and_where('stDeviceID','=',$aUpdate['stDeviceID']);
				}
				else
				{
					$query->and_where('stDeviceToken','=',$aUpdate['stDeviceToken']);
				}
				$result = $query->execute();
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

	public static function deleteGroupStudent($aStIDs = null)
	{
		if (is_null($aStIDs))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();

			$result = DB::delete('GroupSPos_Table')
				->where('stID','IN',$aStIDs)
				->execute();

			$result = DB::delete('StudentPosition_Table')
				->where('stID','IN',$aStIDs)
				->execute();

			$result = DB::delete('CoopStudent_Table')
				->where('stID','IN',$aStIDs)
				->execute();

			$result = DB::delete('Student_Table')
				->where('stID','IN',$aStIDs)
				->execute();

			$result = DB::insert('StudentMissingID_Table')
				->columns(array('stID'));
			foreach ($aStIDs as $sS)
			{
				$result = $result->values(array($sS));
			}
			$result->execute();

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
	private static function getStudentID($aIDs = null)
	{
		try
		{
			while (true)
			{
				$sStID = 's'.Str::random('numeric',9);
				if (!is_null($aIDs))
				{
					if (array_search($sStID, $aIDs) !== false)
					{
						continue;
					}
				}
				$result1 = DB::select()->from('Student_Table')->where('stID',$sStID)->execute()->as_array();
				$result2 = DB::select()->from('StudentMissingID_Table')->where('stID',$sStID)->execute()->as_array();
				if (empty($result1) && empty($result2))
				{
					break;
				}
			}
			return $sStID;
		}
		catch (Exception $e)
		{
			throw $e;
		}
	}
}
