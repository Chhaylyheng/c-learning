<?php
class Model_Class extends \Model
{
	public static function getClassFromTeacher($sTtID=null,$iStatus=null,$aAndWhere=null,$aOrWhere=null,$aSort=null)
	{
/*
		$query = DB::select_array()->from('TeacherClassList_View');
		if (!is_null($sTtID))
		{
			$query->where('ttID','=',$sTtID);
		}
		if (!is_null($iStatus))
		{
			$query->where('ctStatus','=',$iStatus);
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
		else
		{
			$query->order_by('ctYear','asc');
			$query->order_by('dpNO','asc');
			$query->order_by('ctWeekDay','asc');
			$query->order_by('dhNO','asc');
		}
*/

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
				'tt.*','ct.*','tp.*',
				'cm.cmName','cm.cmAddress','cm.cmPref','cm.cmCity',
				DB::expr('st_x(`cg`.`ctLatLon`) AS `ctLon`'),
				DB::expr('st_y(`cg`.`ctLatLon`) AS `ctLat`'),
				DB::expr('ASTEXT(cg.ctLatLon) AS `ctLatLon`'),
				'gc.gtID',
				DB::expr($sNSub),
				DB::expr($sWSub),
				DB::expr($sGSub),
				DB::expr($sTSub),
			)
		)
			->from(array('TeacherPosition_Table','tp'))
			->join(array('Teacher_Table','tt'),'LEFT')
			->on('tp.ttID','=','tt.ttID')
			->join(array('Class_Table','ct'),'LEFT')
			->on('tp.ctID','=','ct.ctID')
			->join(array('College_Master','cm'),'LEFT')
			->on('ct.cmKCode','=','cm.cmKCode')
			->join(array('ClassGeometry_Table','cg'),'LEFT')
			->on('tp.ctID','=','cg.ctID')
			->join(array('GroupCPos_Table','gc'),'LEFT')
			->on('ct.ctID','=','gc.ctID')
		;
		if (!is_null($sTtID))
		{
			$query->where('tp.ttID','=',$sTtID);
		}
		if (!is_null($iStatus))
		{
			$query->where('ct.ctStatus','=',$iStatus);
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
			if ($aSort !== false)
			{
				foreach ($aSort as $sC => $sS)
				{
					$query->order_by($sC,$sS);
				}
			}
		}
		else
		{
			$query->order_by('ct.ctYear','asc');
			$query->order_by('ct.dpNO','asc');
			$query->order_by('ct.ctWeekDay','asc');
			$query->order_by('ct.dhNO','asc');
		}
		$result = $query->execute();

		return $result;
	}

	public static function getClassFromStudent($sStID = null,$iStatus = null,$sCtID = null,$aAndWhere = null,$aOrWhere = null,$aSort = null)
	{
/*
		$query = DB::select_array()->from('StudentClassList_View')->where('stID',$sStID);
		if (!is_null($iStatus))
		{
			$query->where('ctStatus','=',$iStatus);
		}
		if (!is_null($sCtID))
		{
			$query->where('ctID','=',$sCtID);
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
		else
		{
			$query->order_by('ctCode','asc');
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
		if (!is_null($sStID))
		{
			$query->where('sp.stID','=',$sStID);
		}
		if (!is_null($iStatus))
		{
			$query->where('ct.ctStatus','=',$iStatus);
		}
		if (!is_null($sCtID))
		{
			$query->where('sp.ctID','=',$sCtID);
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
			if ($aSort !== false)
			{
				foreach ($aSort as $sC => $sS)
				{
					$query->order_by($sC,$sS);
				}
			}
		}
		else
		{
			$query->order_by('ct.ctCode','asc');
		}
		$result = $query->execute();
		return $result;
	}

	public static function getStudentPosition($sStID = null,$iStatus = null)
	{
		$query = DB::select_array(
				array(
					'sp.*','ct.ctStatus'
				)
			)
			->from(array('StudentPosition_Table','sp'))
			->join(array('Class_Table','ct'),'LEFT')
			->on('sp.ctID','=','ct.ctID')
			->where(array('sp.spAuth'=>1))
		;
		if (!is_null($sStID))
		{
			$query->where('sp.stID','=',$sStID);
		}
		if (!is_null($iStatus))
		{
			$query->where('ct.ctStatus','=',$iStatus);
		}
		$result = $query->execute();
		return $result;
	}
	public static function getAssistantPosition($sAtID = null)
	{
		$query = DB::select_array(
				array(
					'ap.*'
				)
			)
			->from(array('AssistantPosition_Table','ap'))
		;
		if (!is_null($sAtID))
		{
			$query->where('ap.atID','=',$sAtID);
		}
		$result = $query->execute();
		return $result;
	}
	public static function getTeacherPosition($sTtID = null)
	{
		$query = DB::select_array(
				array(
						'tp.*'
				)
			)
			->from(array('TeacherPosition_Table','tp'))
		;
		if (!is_null($sTtID))
		{
			$query->where('tp.ttID','=',$sTtID);
		}
		$result = $query->execute();
		return $result;
	}

	public static function getClass($aAndWhere = null,$aOrWhere = null,$aSort = null)
	{
		$query = DB::select_array()->from('ClassList_View');
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

	public static function getClassFromID($sCtID = null,$iStatus = null,$aAndWhere = null)
	{
		$query = DB::select_array()->from('ClassList_View')->where('ctID',$sCtID);
		if (!is_null($iStatus))
		{
			$query->where('ctStatus','=',$iStatus);
		}
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

	public static function getClassFromCode($sCode = null,$iStatus = null,$aAndWhere = null)
	{
		$query = DB::select_array()->from('ClassList_View')->where('ctCode',$sCode);
		if (!is_null($iStatus))
		{
			$query->where('ctStatus','=',$iStatus);
		}
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

	public static function getClassFromGuestCode($sCode = null,$iStatus = null,$aAndWhere = null)
	{
		$query = DB::select_array()->from('ClassList_View')->where(DB::expr('SUBSTRING_INDEX(ctCode,"@",-1)'),'=',$sCode);
		if (!is_null($iStatus))
		{
			$query->where('ctStatus','=',$iStatus);
		}
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

	public static function insertClass($aInsert = null)
	{
		if (is_null($aInsert))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{   
			DB::start_transaction();
			// IDを生成
			$sCtID = self::getClassID();
			$aInsert['class']['ctID'] = $sCtID;
			$aInsert['position']['ctID'] = $sCtID;
			$query = DB::insert('Class_Table')->set($aInsert['class'])->execute();
			echo "test---++++4";
			$aInsert['position']['tpSort'] = self::getTClassSort($aInsert['position']['ttID']);
			$query = DB::insert('TeacherPosition_Table')->set($aInsert['position'])->execute();
			$sDate = date('YmdHis');
			$aAMColumn = array('ctID','amAttendState','amName','amShort','amAbsence','amDefault','amTime','amDate');
			$aAMValue = array(
				array($sCtID,0,'欠席','欠',1,0,0,$sDate),
				array($sCtID,1,'出席','出',0,1,0,$sDate),
				array($sCtID,2,'遅刻','遅',0,0,10,$sDate),
				array($sCtID,3,'早退','早',0,0,0,$sDate),
				array($sCtID,4,'その他','他',0,0,0,$sDate)
			);
			foreach ($aAMValue as $aAMV)
			{
				
				$query = DB::insert('AttendState_Master')->columns($aAMColumn)->values($aAMV)->execute();
			}

			$result = Model_Group::getGroupTeachers(array(array('gtp.ttID','=',$aInsert['position']['ttID'])));
			if (count($result))
			{   
				
				$aGT = $result->current();
				$query = DB::insert('GroupCPos_Table')->set(array('gtID'=>$aGT['gtID'],'ctID'=>$sCtID,'gpDate'=>$sDate))->execute();
			}

			DB::commit_transaction();
			// クエリの結果を返す
			return $sCtID;
		}
		catch (Exception $e)
		{
			// 未決のトランザクションクエリをロールバックする

			// DB::rollback_transaction();
			throw $e;
		}
	}

	public static function entryClass($sCtID =null,$mStID = null)
	{
		if (is_null($sCtID) || is_null($mStID))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		$sDate = date('YmdHis');
		try
		{
			DB::start_transaction();

			if (!is_array($mStID))
			{
				$iCnt = 1;
				$sSID = $mStID;
				$aInsert = array('ctID'=>$sCtID,'stID'=>$sSID,'spDate'=>$sDate,'spAuth'=>1);
				$result = DB::insert('StudentPosition_Table')->set($aInsert)->execute();

				$result = Model_Group::getGroupClasses(array(array('ctID','=',$sCtID)));
				if (count($result))
				{
					$aGC = $result->current();
					$result = Model_Group::getGroupStudents(array(array('gsp.gtID','=',$aGC['gtID']),array('gsp.stID','=',$sSID)));
					if (!count($result))
					{
						$query = DB::insert('GroupSPos_Table')->set(array('gtID'=>$aGC['gtID'],'stID'=>$sSID,'gpDate'=>$sDate))->execute();
					}
				}

				$result = Model_Coop::getCoopCategoryFromClass($sCtID,array(array('ccStuRange','=',2)));
				if (count($result))
				{
					$aCC = $result->as_array('ccID');
					$aCID = array_keys($aCC);
					Model_Coop::entryCoopsStudents($aCID,array($sSID => array('stID'=>$sSID)));
				}
			}
			else
			{
				$iCnt = count($mStID);
				foreach ($mStID as $sSID)
				{
					$aInsert = array('ctID'=>$sCtID,'stID'=>$sSID,'spDate'=>$sDate,'spAuth'=>1);
					$result = DB::insert('StudentPosition_Table')->set($aInsert)->execute();

					$result = Model_Group::getGroupClasses(array(array('ctID','=',$sCtID)));
					if (count($result))
					{
						$aGC = $result->current();
						$result = Model_Group::getGroupStudents(array(array('gsp.gtID','=',$aGC['gtID']),array('gsp.stID','=',$sSID)));
						if (!count($result))
						{
							$query = DB::insert('GroupSPos_Table')->set(array('gtID'=>$aGC['gtID'],'stID'=>$sSID,'gpDate'=>$sDate))->execute();
						}
					}

					$result = Model_Coop::getCoopCategoryFromClass($sCtID,array(array('ccStuRange','=',2)));
					if (count($result))
					{
						$aCC = $result->as_array('ccID');
						$aCID = array_keys($aCC);
						Model_Coop::entryCoopsStudents($aCID,array($sSID => array('stID'=>$sSID)));
					}
				}
			}

			DB::commit_transaction();
			// クエリの結果を返す
			return $iCnt;
		}
		catch (Exception $e)
		{
			// 未決のトランザクションクエリをロールバックする
			DB::rollback_transaction();
			throw $e;
		}
	}

	public static function removeClass($sCtID =null,$sStID = null)
	{
		if (is_null($sCtID) || is_null($sStID))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			$query = DB::delete('StudentPosition_Table');
			$query->where('ctID','=',$sCtID);
			$query->where('stID','=',$sStID);
			$result = $query->execute();

			$result = Model_Group::getGroupClasses2(array(array('gcp.ctID','=',$sCtID)));
			if (count($result))
			{
				$aGC = $result->current();
				$result = Model_Class::getClassFromStudent($sStID,null,null,array(array('gc.gtID','!=','')));
				if (count($result))
				{
					$aSC = $result->as_array('gtID');
					if (!isset($aSC[$aGC['gtID']]))
					{
						# $query = DB::delete('GroupSPos_Table')->where(array(array('gtID','=',$aGC['gtID']),array('stID','=',$sStID)))->execute();
					}
				} else {
					# $query = DB::delete('GroupSPos_Table')->where(array(array('gtID','=',$aGC['gtID']),array('stID','=',$sStID)))->execute();
				}
			}

			$result = Model_Coop::getCoopCategoryFromClass($sCtID,array(array('ccStuRange','!=',0)));
			if (count($result))
			{
				$aCC = $result->as_array('ccID');
				$aCID = array_keys($aCC);
				Model_Coop::removeCoopsStudents($aCID,array($sStID => array('stID'=>$sStID)));
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

	public static function updateClass($aInsert =null,$aClass = null)
	{
		if (is_null($aInsert) || is_null($aClass))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			$query = DB::update('Class_Table');
			$query->where('ctID','=',$aClass['ctID']);
			$query->set($aInsert);
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

	public static function deleteClass($aCtIDs =null)
	{
		if (is_null($aCtIDs))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();

			foreach ($aCtIDs as $sCtID)
			{
				$result = Model_Group::getGroupClasses(array(array('ctID','=',$sCtID)));
				if (count($result))
				{
					$aGC = $result->current();
					$result = Model_Student::getStudentFromClass($sCtID);
					if (count($result))
					{
						$aCS = $result->as_array('stID');
						foreach ($aCS as $sStID => $aS)
						{
							$result = Model_Class::getClassFromStudent($sStID,null,null,array(array('gc.gtID','!=','')));
							if (count($result))
							{
								$aSC = $result->as_array('gtID');
								if (!isset($aSC[$aGC['gtID']]))
								{
									# $query = DB::delete('GroupSPos_Table')->where(array(array('gtID','=',$aGC['gtID']),array('stID','=',$sStID)))->execute();
								}
							} else {
								# $query = DB::delete('GroupSPos_Table')->where(array(array('gtID','=',$aGC['gtID']),array('stID','=',$sStID)))->execute();
							}
						}
					}
					$query = DB::delete('GroupCPos_Table')->where(array(array('gtID','=',$aGC['gtID']),array('ctID','=',$sCtID)))->execute();
				}

				$aTables = array(
					'AttendBook_Table','AttendCalendar_Table','AttendState_Master',
					'ClassGeometry_Table',
					'MaterialCategory_Table',
					'QuestBase_Table',
					'TestBase_Table',
					'CoopCategory_Table',
					'TeacherPosition_Table',
					'StudentPosition_Table',
					'Class_Table',
				);
				foreach ($aTables as $sTable)
				{
					$result = DB::delete($sTable)
						->where('ctID','=',$sCtID)
						->execute()
					;
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

	public static function sortClass($aClass = null,$aCtIDs = null,$sSort = null)
	{
		if (is_null($aClass) || is_null($aCtIDs) || is_null($sSort))
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
			$result = DB::update('TeacherPosition_Table')
				->and_where('ttID',$aClass['ttID'])
				->and_where('ctID','IN',$aCtIDs)
				->and_where('tpSort',$aClass['tpSort']+$iWhere)
				->set(array('tpSort'=>DB::expr('tpSort'.$iUp1)))
				->execute()
			;
			$result = DB::update('TeacherPosition_Table')
				->and_where('ttID',$aClass['ttID'])
				->and_where('ctID',$aClass['ctID'])
				->set(array('tpSort'=>DB::expr('tpSort'.$iUp2)))
				->execute()
			;

			self::resetTeacherClassSort(array($aClass['ttID']));
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

	public static function entryClassTeacher($aClass = null, $aTeachers = null)
	{
		if (is_null($aClass) || is_null($aTeachers))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		$sDate = date('YmdHis');
		try
		{
			DB::start_transaction();

			$aKeys = array_keys($aTeachers);

			$query = \DB::insert('TeacherPosition_Table')
				->columns(array(
					'ctID',
					'ttID',
					'tpMaster',
					'tpSort',
					'tpDate',
				)
			);

			$iSort = 1;
			foreach ($aTeachers as $sTID => $aT)
			{
				if (isset($aClass['ctStatus']) && $aClass['ctStatus'])
				{
					$result = Model_Class::getClassFromTeacher($sTID,1);
					$iSort = count($result) + 1;
				}
				$query->values(array($aClass['ctID'],$sTID,0,$iSort,$sDate));
			}

			if (isset($aClass['ctStatus']) && $aClass['ctStatus'])
			{
				self::resetTeacherClassSort($aKeys);
			}
			$result = $query->execute();

			self::resetTeacherClassMaster(array($aClass['ctID']));

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

	public static function removeClassTeacher($aClass = null, $aTeachers = null)
	{
		if (is_null($aClass) || is_null($aTeachers))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();

			$aKeys = array_keys($aTeachers);

			$aWhere = array(array('ctID','=',$aClass['ctID']),array('ttID','IN',$aKeys),array('tpMaster','=',0));
			$result = DB::delete('TeacherPosition_Table')->where($aWhere)->execute();

			if (isset($aClass['ctStatus']) && $aClass['ctStatus'])
			{
				self::resetTeacherClassSort($aKeys);
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

	public static function updateClassTeacher($aUpdate = null, $aClass = null)
	{
		if (is_null($aUpdate) || is_null($aClass))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();

			$result = DB::update('TeacherPosition_Table')
				->where('ctID','=',$aClass['ctID'])
				->set($aUpdate)
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

	public static function resetTeacherClassSort($aTIDs)
	{
		$result = Model_Class::getClassFromTeacher(null,1,array(array('tp.ttID','IN',$aTIDs)),null,array('tp.ttID'=>'asc','tp.tpSort'=>'asc'));
		if (!count($result))
		{
			return;
		}

		try
		{
			$sTtID = null;
			$iSort = 1;
			foreach ($result as $aC)
			{
				if ($sTtID !== $aC['ttID'])
				{
					$iSort = 1;
				}

				$res = DB::update('TeacherPosition_Table')
					->where('ttID','=',$aC['ttID'])
					->where('ctID','=',$aC['ctID'])
					->set(array('tpSort'=>$iSort))
					->execute()
				;

				$iSort++;
				$sTtID = $aC['ttID'];
			}
			return;
		}
		catch (Exception $e)
		{
			throw $e;
		}
	}


	public static function resetTeacherClassMaster($aCIDs)
	{
		$result = Model_Teacher::getTeacherFromClass(array(array('tp.ctID','IN',$aCIDs)),null,array('tp.ctID'=>'asc','tp.ttID'=>'asc'));
		if (!count($result))
		{
			return;
		}

		try
		{
			$sCtID = null;
			$aMaster = array();
			foreach ($result as $aT)
			{
				if ($sCtID == $aT['ctID'])
				{
					continue;
				}

				$aMaster[$aT['ctID']] = $aT['ttID'];
				if ($aT['tpMaster'])
				{
					unset($aMaster[$aT['ctID']]);
					$sCtID = $aT['ctID'];
				}
			}

			if (!empty($aMaster))
			{
				foreach ($aMaster as $sCtID => $sTtID)
				{
					$res = DB::update('TeacherPosition_Table')
						->where('ttID','=',$sTtID)
						->where('ctID','=',$sCtID)
						->set(array('tpMaster'=>1))
						->execute()
					;
				}
			}
			return;
		}
		catch (Exception $e)
		{
			throw $e;
		}
	}

	public static function insertOrgClass($aInsert = null)
	{
		if (is_null($aInsert))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			// 先生IDを生成
			$sCtID = self::getClassID();
			if (!$aInsert['class']['ctCode'])
			{
				$sCode = self::getClassCode(CL_CLASSCODE);
				$aInsert['class']['ctCode'] = $sCode;
			}
			$aInsert['class']['ctID'] = $sCtID;
			$aInsert['group']['ctID'] = $sCtID;

			$query = DB::insert('Class_Table')->set($aInsert['class'])->execute();
			$query = DB::insert('GroupCPos_Table')->set($aInsert['group'])->execute();

			$sDate = date('YmdHis');
			$aAMColumn = array('ctID','amAttendState','amName','amShort','amAbsence','amDefault','amTime','amDate');
			$aAMValue = array(
				array($sCtID,0,'欠席','欠',1,0,0,$sDate),
				array($sCtID,1,'出席','出',0,1,0,$sDate),
				array($sCtID,2,'遅刻','遅',0,0,10,$sDate),
				array($sCtID,3,'早退','早',0,0,0,$sDate),
				array($sCtID,4,'その他','他',0,0,0,$sDate)
			);
			foreach ($aAMValue as $aAMV)
			{
				$query = DB::insert('AttendState_Master')->columns($aAMColumn)->values($aAMV)->execute();
			}

			DB::commit_transaction();
			// クエリの結果を返す
			return $sCtID;
		}
		catch (Exception $e)
		{
			// 未決のトランザクションクエリをロールバックする
			DB::rollback_transaction();
			throw $e;
		}
	}

	public static function insertOrgClassFromCSV($aCSV = null,$aGroup = null)
	{
		if (is_null($aCSV) || is_null($aGroup))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			$aIDs = null;
			$aCode = null;

			foreach ($aCSV as $aS)
			{
				// IDを生成
				$sCtID = self::getClassID($aIDs);
				$aIDs[] = $sCtID;
				if ($aS[0] == '')
				{
					$sCode = self::getClassCode(CL_CLASSCODE,$aCode);
				}
				else
				{
					$sCode = $aS[0];
				}
				$aCode[] = $sCode;

				// 登録データ生成
				$aInsert = array(
					'ctID'            => $sCtID,
					'ctCode'          => $sCode,
					'ctName'          => $aS[1],
					'ctYear'          => (int)$aS[2],
					'dpNO'            => (int)$aS[3],
					'ctWeekDay'       => (int)$aS[4],
					'dhNO'            => (int)$aS[5],
					'ctStatus'        => (int)$aS[6],
					'ctDate'          => date('YmdHis'),
				);

				$query = DB::insert('Class_Table')->set($aInsert)->execute();

				$aCPos = array(
					'gtID' => $aGroup['gtID'],
					'ctID' => $sCtID,
					'gpDate' => date('YmdHis'),
				);
				$query = DB::insert('GroupCPos_Table')->set($aCPos)->execute();

				$sDate = date('YmdHis');
				$aAMColumn = array('ctID','amAttendState','amName','amShort','amAbsence','amDefault','amTime','amDate');
				$aAMValue = array(
						array($sCtID,0,'欠席','欠',1,0,0,$sDate),
						array($sCtID,1,'出席','出',0,1,0,$sDate),
						array($sCtID,2,'遅刻','遅',0,0,10,$sDate),
						array($sCtID,3,'早退','早',0,0,0,$sDate),
						array($sCtID,4,'その他','他',0,0,0,$sDate)
				);
				foreach ($aAMValue as $aAMV)
				{
					$query = DB::insert('AttendState_Master')->columns($aAMColumn)->values($aAMV)->execute();
				}
			}

			DB::commit_transaction();
			// クエリの結果を返す
			return true;
		}
		catch (Exception $e)
		{
			// 未決のトランザクションクエリをロールバックする
			DB::rollback_transaction();
			throw $e;
		}
	}

	public static function updateOrgClassFromCSV($aUpdate = null,$aGroup = null)
	{
		if (is_null($aUpdate) || is_null($aGroup))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();

			foreach ($aUpdate as $sCtID => $aS)
			{
				$aInsert = array(
					'ctName'    => $aS[1],
					'ctYear'    => (int)$aS[2],
					'dpNO'      => (int)$aS[3],
					'ctWeekDay' => (int)$aS[4],
					'dhNO'      => (int)$aS[5],
					'ctStatus'  => (int)$aS[6],
				);

				$query = DB::update('Class_Table')
				->set($aInsert)
				->where('ctID','=',$sCtID)
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

	public static function insertOrgClassTeachersFromCSV($aCSV = null)
	{
		if (is_null($aCSV))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			$aIDs = array();
			$aCIDs = array();

			foreach ($aCSV as $aS)
			{
				$result = DB::select_array()
					->from('TeacherPosition_Table')
					->and_where('ctID','=',$aS[0])
					->and_where('ttID','=',$aS[1])
					->execute();

				if ($aS[2] == 1)
				{
					$res = DB::update('TeacherPosition_Table')
					->set(array('tpMaster' => 0))
					->where('ctID','=',$aS[0])
					->execute();
				}

				if (count($result))
				{
					$aUpdate = array(
						'tpMaster' => (int)$aS[2],
					);

					$result = DB::update('TeacherPosition_Table')
						->set($aUpdate)
						->and_where('ctID','=',$aS[0])
						->and_where('ttID','=',$aS[1])
						->execute();
				}
				else
				{
					$aInsert = array(
						'ctID' => $aS[0],
						'ttID' => $aS[1],
						'tpMaster' => (int)$aS[2],
						'tpDate' => date('YmdHis'),
					);

					$result = DB::insert('TeacherPosition_Table')
						->set($aInsert)
						->execute();
				}

				if (array_search($aS[0], $aCIDs) === false)
				{
					$aCIDs[] = $aS[0];
				}
				if (array_search($aS[1], $aIDs) === false)
				{
					$aIDs[] = $aS[1];
				}
			}

			self::resetTeacherClassSort($aIDs);
			self::resetTeacherClassMaster($aCIDs);
			\Model_Teacher::setTeacherClassNum($aIDs);

			DB::commit_transaction();
			// クエリの結果を返す
			return true;
		}
		catch (Exception $e)
		{
			// 未決のトランザクションクエリをロールバックする
			DB::rollback_transaction();
			throw $e;
		}
	}


	public static function insertOrgClassStadyFromCSV($aCSV = null)
	{
		if (is_null($aCSV))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			$aIDs = array();
			$aValues = null;
			$aCoopSet = null;

			$iGrp = 0;
			$iCnt = 0;
			foreach ($aCSV as $aS)
			{
				$result = DB::select_array()
					->from('StudentPosition_Table')
					->and_where('ctID','=',$aS[0])
					->and_where('stID','=',$aS[1])
					->execute();

				if (count($result))
				{
					continue;
				}
				if (array_search($aS[0].CL_SEP.$aS[1], $aIDs) !== false)
				{
					continue;
				}
				$aIDs[] = $aS[0].CL_SEP.$aS[1];

				$aValues[$iGrp][] = array($aS[0],$aS[1],1,date('YmdHis'));
				$aCoopSet[] = array('ctID'=>$aS[0],'stID'=>$aS[1]);
				$iCnt++;
				if ($iCnt > 100) {
					$iGrp++;
					$iCnt = 0;
				}
			}

			if (!is_null($aValues))
			{
				DB::start_transaction();
				foreach ($aValues as $i => $aVs)
				{
					$query = DB::insert('StudentPosition_Table')
						->columns(array('ctID','stID','spAuth','spDate'))
					;
					foreach ($aVs as $aV)
					{
						$query->values($aV);
					}
					$result = $query->execute();
				}

				foreach ($aCoopSet as $aV)
				{
					$sCtID = $aV['ctID'];
					$sStID = $aV['stID'];
					$result = Model_Coop::getCoopCategoryFromClass($sCtID,array(array('ccStuRange','=',2)));
					if (count($result))
					{
						$aCC = $result->as_array('ccID');
						$aCID = array_keys($aCC);
						Model_Coop::entryCoopsStudents($aCID,array($sStID => array('stID'=>$sStID)));
					}
				}
				DB::commit_transaction();
			}
			// クエリの結果を返す
			return true;
		}
		catch (Exception $e)
		{
			// 未決のトランザクションクエリをロールバックする
			DB::rollback_transaction();
			throw $e;
		}
	}



	public static function getMailHistory($sCtID = null,$aAndWhere = null,$aOrWhere = null,$aSort = null)
	{
		$query = DB::select_array()->from('ClassMailHistory_Table');
		if (!is_null($sCtID))
		{
			$query->where('ctID','=',$sCtID);
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

	public static function insertMailHistory($aInsert = null)
	{
		if (is_null($aInsert))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			// IDを生成
			$query = DB::insert('ClassMailHistory_Table')->set($aInsert)->execute();
			DB::commit_transaction();
			// クエリの結果を返す
			return $query[0];
		}
		catch (Exception $e)
		{
			// 未決のトランザクションクエリをロールバックする
			DB::rollback_transaction();
			throw $e;
		}
	}


	public static function getNews($sCtID = null,$aAndWhere = null,$aOrWhere = null,$aSort = null)
	{
		$query = DB::select_array()->from('ClassNews_Table');
		if (!is_null($sCtID))
		{
			$query->where('ctID','=',$sCtID);
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

	public static function insertNews($aInsert = null)
	{
		if (is_null($aInsert))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			// IDを生成
			$query = DB::insert('ClassNews_Table')->set($aInsert)->execute();
			DB::commit_transaction();
			// クエリの結果を返す
			return $query[0];
		}
		catch (Exception $e)
		{
			// 未決のトランザクションクエリをロールバックする
			DB::rollback_transaction();
			throw $e;
		}
	}

	public static function updateNews($aUpdate = null, $no = null)
	{
		if (is_null($aUpdate) || is_null($no))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();

			$result = DB::update('ClassNews_Table')
				->where('no','=',$no)
				->set($aUpdate)
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

	public static function deleteNews($no = null)
	{
		if (is_null($no))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();

			$result = DB::delete('ClassNews_Table')
				->where('no','=',$no)
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

	public static function getClassID($aIDs = null)
	{
		try
		{
			while (true):
				$sCtID = 'c'.Str::random('numeric',9);
				if (!is_null($aIDs))
				{
					if (array_search($sCtID, $aIDs) !== false)
					{
						continue;
					}
				}
				$result = DB::select()->from('Class_Table')->where('ctID',$sCtID)->execute()->as_array();
				if (empty($result)):
					break;
				endif;
			endwhile;
			return $sCtID;
		}
		catch (Exception $e)
		{
			throw $e;
		}
	}

	public static function getClassCode($iDigit = 8, $aCodes = null)
	{
		try
		{
			while (true)
			{
				$sCode = Str::random('numeric',$iDigit);
				if (!is_null($aCodes))
				{
					if (array_search($sCode, $aCodes) !== false)
					{
						continue;
					}
				}
				if (preg_match('/^0/', $sCode))
				{
					continue;
				}

				$result = DB::select()->from('Class_Table')->where('ctCode',$sCode)->execute()->as_array();
				if (empty($result))
				{
					break;
				}
			}
			return $sCode;
		}
		catch (Exception $e)
		{
			throw $e;
		}
	}


	public static function getClassArchive($sCtID = null,$sType = null)
	{
		$query = DB::select_array(
			array(
				'ca.*',
				'ft.*'
			)
		)
			->from(array('ClassArchive_Table','ca'))
			->join(array('File_Table','ft'),'LEFT')
			->on('ca.fID','=','ft.fID')
		;
		if (!is_null($sCtID))
		{
			$query->where('ca.ctID','=',$sCtID);
		}
		if (!is_null($sType))
		{
			$query->where('ca.caType','=',$sType);
		}
		$result = $query->execute();
		return $result;
	}

	public static function insertClassArchive($aInsert = null)
	{
		if (is_null($aInsert))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			// IDを生成
			$query = DB::insert('ClassArchive_Table')->set($aInsert)->execute();
			DB::commit_transaction();
			// クエリの結果を返す
			return $query[0];
		}
		catch (Exception $e)
		{
			// 未決のトランザクションクエリをロールバックする
			DB::rollback_transaction();
			throw $e;
		}
	}

	public static function updateClassArchive($aUpdate = null, $aAndWhere = null)
	{
		if (is_null($aUpdate) || is_null($aAndWhere))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();

			$query = DB::update('ClassArchive_Table')
				->set($aUpdate)
			;

			if (!is_null($aAndWhere))
			{
				foreach ($aAndWhere as $aW)
				{
					$query->where($aW[0],$aW[1],$aW[2]);
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


	private static function getTClassSort($sTtID = null)
	{
		$result = Model_Class::getClassFromTeacher(null,1,array(array('tp.ttID','=',$sTtID)),null,array('tp.tpSort'=>'desc'));
		if (!count($result))
		{
			return 1;
		}
		$aClass = $result->as_array();

		return ($aClass[0]['tpSort'] + 1);
	}


}
