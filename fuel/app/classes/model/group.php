<?php
class Model_Group extends \Model
{
	public static function getGroupAdminFromPostLogin($sID = null,$sPass = null)
	{
		if (is_null($sID) || is_null($sPass))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			$query = DB::update('GroupAdmin_Table');
			$query->value('gaLoginNum', DB::expr('`gaLoginNum`+1'));
			$query->value('gaLastLoginDate', DB::expr('`gaLoginDate`'));
			$query->value('gaLoginDate', DB::expr('NOW()'));
			$query->value('gaPassMiss', 0);
			$query->value('gaUA', Input::user_agent());
			$query->value('gaHash', sha1($sID.sha1($sPass)));
			$query->where('gaLogin',$sID);
			$query->where('gaPass',sha1($sPass));
			$result = $query->execute();
			DB::commit_transaction();

			$query = DB::select_array()
				->from('GroupAdmin_Table')
				->where('gaLogin',$sID)
				->where('gaPass',sha1($sPass))
			;
			$result = $query->execute();

			if (!count($result))
			{
				DB::start_transaction();
				$query = DB::update('GroupAdmin_Table');
				$query->value('gaPassMiss', DB::expr('`gaPassMiss`+1'));
				$query->where('gaLogin',$sID);
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
	public static function getGroupAdminFromHash($sHash = null)
	{
		$query = DB::select_array()->from('GroupAdmin_Table')->where('gaHash',$sHash);
		$result = $query->execute();

		return $result;
	}
	public static function getGroupAdminFromID($sID = null)
	{
		$result = DB::select_array()->from('GroupAdmin_Table')->where('gaID',$sID)->execute();
		return $result;
	}

	public static function getGroup($aAndWhere = null,$aOrWhere = null,$aSort = null)
	{
		$query = DB::select_array(
			array(
				'gb.*',
				DB::expr('(SELECT count(ttID) FROM GroupTPos_Table WHERE gtID=gb.gtID) AS gtTNum'),
				DB::expr('(SELECT count(ctID) FROM GroupCPos_Table WHERE gtID=gb.gtID) AS gtCNum'),
				DB::expr('(SELECT count(stID) FROM GroupSPos_Table WHERE gtID=gb.gtID) AS gtSNum'),
				DB::expr('(SELECT count(gaID) FROM GroupAdmin_Table WHERE gtID=gb.gtID) AS gtANum'),
			)
		)
			->from(array('GroupBase_Table','gb'))
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

	public static function getGroupAdmins($aAndWhere = null,$aOrWhere = null,$aSort = null)
	{
		$query = DB::select_array()->from('GroupAdmin_Table');
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

	public static function getGroupTeachers($aAndWhere = null,$aOrWhere = null,$aSort = null,$aOrGroup = null)
	{
		$query = DB::select_array(
			array(
				'tv.*',
				'gtp.gtID',
				'gtp.gpDate',
				DB::expr('(SELECT count(tp.ctID) from TeacherPosition_Table tp where gtp.gtID=tp.gtID and gtp.ttID=tp.ttID) AS ttGtClassNum'),
			)
		)
			->from(array('GroupTPos_Table','gtp'))
			->join(array('Teacher_View','tv'),'left')
			->on('gtp.ttID','=','tv.ttID')
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

	public static function getGroupTeachersClasses($aAndWhere = null,$aOrWhere = null,$aSort = null)
	{
		/*
			$query = DB::select_array()->from('TeacherGroupClassList_View');
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
						'tt.*','ct.*','tp.*','tu.*',
						'cm.cmName','cm.cmAddress','cm.cmPref','cm.cmCity',
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
		->join(array('GroupCPos_Table','gc'),'LEFT')
		->on('ct.ctID','=','gc.ctID')
		->join(array('TeacherUsed_Table','tu'),'LEFT')
		->on('tp.ttID','=','tu.ttID')
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

	public static function getGroupClasses($aAndWhere = null,$aOrWhere = null,$aSort = null)
	{
		$query = DB::select_array()->from('GroupCPos_View');
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

	public static function getGroupClasses2($aAndWhere = null,$aOrWhere = null,$aSort = null)
	{
		$aNSubWhere = array('gcp.ctID' => DB::expr('sp.ctID'), 'sp.spAuth' => 1);
		$subquery = DB::select(DB::expr('count(sp.stID)'))
		->from(array('StudentPosition_Table','sp'))
		->where($aNSubWhere)
		->compile()
		;
		$sNSub = '('.$subquery.') AS `scNum`';

		$query = DB::select_array(
			array(
				'ct.*',
				'gcp.*',
				DB::expr($sNSub),
			)
		)
			->from(array('GroupCPos_Table','gcp'))
			->join(array('Class_Table','ct'),'LEFT')
			->on('gcp.ctID','=','ct.ctID')
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


	public static function getGroupStudents($aAndWhere = null,$aOrWhere = null,$aSort = null,$aOrGroup = null, $oPaging = null)
	{
		/*
		$query = DB::select_array()->from('GroupSPos_View');
		*/

		$aSubWhere = array('gsp.stID' => DB::expr('sp.stID'), 'gsp.gtID' => DB::expr('gc.gtID'), 'sp.spAuth' => 1);
		$subquery = DB::select(DB::expr('count(sp.ctID)'))
			->from(array('StudentPosition_Table','sp'))
			->join(array('GroupCPos_Table','gc'),'LEFT')
			->on('sp.ctID','=','gc.ctID')
			->where($aSubWhere)
			->compile();
		$subquery = '('.$subquery.') AS `stGtClassNum`';

		$query = DB::select_array(array('st.*','gsp.*',DB::expr($subquery)))
			->from(array('GroupSPos_Table','gsp'))
			->join(array('Student_Table','st'),'LEFT')
			->on('gsp.stID','=','st.stID')
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

		if (!is_null($oPaging))
		{
			$query->limit($oPaging->per_page);
			$query->offset($oPaging->offset);
		}

		$result = $query->execute();
		return $result;
	}

	public static function getGroupStudentsClasses($aAndWhere = null,$aOrWhere = null,$aSort = null)
	{
		$query = DB::select_array()->from('StudentGroupClassList_View');
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

	public static function insertGroup($aInsert = null)
	{
		if (is_null($aInsert))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			$sGtID = self::getGroupID();
			$aInsert['gtID'] = $sGtID;
			$query = DB::insert('GroupBase_Table')->set($aInsert)->execute();
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

	public static function updateGroup($aUpdate = null,$aAndWhere = null,$aOrWhere = null)
	{
		if (is_null($aUpdate))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			$query = DB::update('GroupBase_Table');
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

	public static function insertGroupAdmin($aInsert = null)
	{
		if (is_null($aInsert))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			$sGaID = self::getGroupAdminID();
			$aInsert['gaID'] = $sGaID;
			$query = DB::insert('GroupAdmin_Table')->set($aInsert)->execute();
			DB::commit_transaction();
			// クエリの結果を返す
			return $sGaID;
		}
		catch (Exception $e)
		{
			// 未決のトランザクションクエリをロールバックする
			DB::rollback_transaction();
			throw $e;
		}
	}

	public static function updateGroupAdmin($aUpdate = null,$aAndWhere = null,$aOrWhere = null)
	{
		if (is_null($aUpdate))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			$query = DB::update('GroupAdmin_Table');
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

	public static function deleteGroupAdmin($sAID = null)
	{
		if (is_null($sAID))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			$query = DB::delete('GroupAdmin_Table')
				->where('gaID','=',$sAID);
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


	public static function addGroupTeachers($aTIDs = null, $sID = null)
	{
		if (is_null($aTIDs) || is_null($sID))
		{
			throw new Exception('処理に必要な情報がありません');
		}

		$sDate = date('YmdHis');

		try
		{
			DB::start_transaction();

			$aClasses = array();
			$aStudents = array();

			$query = \DB::insert('GroupTPos_Table')
				->columns(array(
					'gtID',
					'ttID',
					'gpDate',
				));

			$iCnt = 0;
			foreach ($aTIDs as $sTID)
			{
				$result = Model_Teacher::getTeacherFromID($sTID);
				if (!count($result))
				{
					continue;
				}
				$result = Model_Group::getGroupTeachers(array(array('gtp.ttID','=',$sTID)));
				if (count($result))
				{
					continue;
				}
				$iCnt++;
				$query->values(array($sID,$sTID,$sDate));

				$result = Model_Class::getClassFromTeacher($sTID,null,array(array('tp.tpMaster','=',1)));
				if (count($result))
				{
					$aTemp = $result->as_array('ctID','ctID');
					$aClasses = array_merge($aClasses,$aTemp);
				}

				# 契約をGroupに
				$cont = \DB::update('Contract_Table')
					->where('ttID','=',$sTID)
					->set(array(
						'ptID'=>99,
						'coTermDate'=>'0000-00-00',
						'coClassNum'=>0,
						'coStuNum'=>0,
						'coCapacity'=>0,
						'coPayment'=>0,
						'coMonths'=>0,
					))
					->execute();
			}
			if ($iCnt)
			{
				$result = $query->execute();
			}

			if (!count($aClasses))
			{
				DB::commit_transaction();
				return $iCnt;
			}

			$query = \DB::insert('GroupCPos_Table')
			->columns(array(
				'gtID',
				'ctID',
				'gpDate',
			));
			foreach ($aClasses as $sCID)
			{
				if (!$sCID)
				{
					continue;
				}

				$result = Model_Group::getGroupClasses(array(array('ctID','=',$sCID),array('gtID','=',$sID)));
				if (count($result))
				{
					continue;
				}
				$query->values(array($sID,$sCID,$sDate));

				$result = Model_Student::getStudentFromClass($sCID);
				if (count($result))
				{
					$aTemp = $result->as_array('stID','stID');
					$aStudents = array_merge($aStudents,$aTemp);
				}
			}
			$result = $query->execute();

			if (!count($aStudents))
			{
				DB::commit_transaction();
				return $iCnt;
			}

			$query = \DB::insert('GroupSPos_Table')
			->columns(array(
				'gtID',
				'stID',
				'gpDate',
			));
			$bStu = false;
			foreach ($aStudents as $sSID)
			{
				if (!$sSID)
				{
					continue;
				}

				$result = Model_Group::getGroupStudents(array(array('gsp.stID','=',$sSID),array('gsp.gtID','=',$sID)));
				if (count($result))
				{
					continue;
				}

				$bStu = true;
				$query->values(array($sID,$sSID,$sDate));
			}
			if ($bStu)
			{
				$result = $query->execute();
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

	public static function changeGroupClassMaster($sCID = null,$sTID = null)
	{
		if (is_null($sCID) || is_null($sTID))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();

			$result = DB::update('TeacherPosition_Table')
				->where('ctID','=',$sCID)
				->set(array('tpMaster'=>0))
				->execute();

			$result = DB::update('TeacherPosition_Table')
				->where('ctID','=',$sCID)
				->and_where('ttID','=',$sTID)
				->set(array('tpMaster'=>1))
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

	public static function annualClass($aClass = null, $iCopy = 0)
	{
		if (is_null($aClass))
		{
			throw new Exception('処理に必要な情報がありません');
		}

		$sDate = date('YmdHis');
		$aUpdate = array(
			'ctStatus' => 0,
		);
		$aKeys = array_keys($aClass);

		$aInsert = array();
		$aIDs = null;
		$aCodes = null;
		$aTIDs = null;

		try
		{
			if ($iCopy)
			{
				foreach ($aClass as $sCtID => $aC)
				{
					$sID = \Model_Class::getClassID($aIDs);
					$sCode = \Model_Class::getClassCode(CL_CLASSCODE, $aCodes);

					$aIDs[] = $sID;
					$aCodes[] = $sCode;

					$aInsert[$sID]['Class_Table']['columns'] = array(
						'ctID','ctCode','ctName','cmKCode','dmNO','ctYear','dpNO','ctWeekDay','dhNO','ctStatus','ctDate',
					);
					$aInsert[$sID]['Class_Table']['values'][] = array(
						$sID,$sCode,$aC['ctName'],$aC['cmKCode'],$aC['dmNO'],($aC['ctYear'] + 1),$aC['dpNO'],$aC['ctWeekDay'],$aC['dhNO'],1,$sDate,
					);

					$aInsert[$sID]['GroupCPos_Table']['columns'] = array(
						'gtID','ctID','gpDate',
					);
					$aInsert[$sID]['GroupCPos_Table']['values'][] = array(
						$aC['gtID'],$sID,$sDate,
					);

					$aInsert[$sID]['AttendState_Master']['columns'] = array(
						'ctID','amAttendState','amName','amShort','amAbsence','amDefault','amTime','amDate'
					);
					$aInsert[$sID]['AttendState_Master']['values'][] = array($sID,0,'欠席','欠',1,0,0,$sDate);
					$aInsert[$sID]['AttendState_Master']['values'][] = array($sID,1,'出席','出',0,1,0,$sDate);
					$aInsert[$sID]['AttendState_Master']['values'][] = array($sID,2,'遅刻','遅',0,0,10,$sDate);
					$aInsert[$sID]['AttendState_Master']['values'][] = array($sID,3,'早退','早',0,0,0,$sDate);
					$aInsert[$sID]['AttendState_Master']['values'][] = array($sID,4,'その他','他',0,0,0,$sDate);

					$aQbIDs = null;
					$aTbIDs = null;

					$qr = \Model_Quest::getQuestBaseFromClass($sCtID,null,null,array('qb.qbSort'=>'asc'));
					if (count($qr))
					{
						foreach ($qr as $aQ)
						{
							$r = \Model_Quest::copyQuest($aQ,array($sID=>array('ctID'=>$sID)),$aQbIDs);
						}
					}

					$tr = \Model_Test::getTestBaseFromClass($sCtID,null,null,array('tb.tbSort'=>'asc'));
					if (count($tr))
					{
						foreach ($tr as $aT)
						{
							$r = \Model_Test::copyTest($aT,array($sID=>array('ctID'=>$sID)),$aTbIDs);
						}
					}

					$mr = \Model_Material::getMaterialCategoryFromClass($sCtID,null,null,array('mcSort'=>'asc'));
					if (count($mr))
					{
						foreach ($mr as $aM)
						{
							$r = \Model_Material::copyMaterial($aM,array($sID=>array('ctID'=>$sID)), $aQbIDs, $aTbIDs);
						}
					}

					$cr = \Model_Coop::getCoopCategoryFromClass($sCtID,null,null,array('ccSort'=>'asc'));
					if (count($cr))
					{
						foreach ($cr as $aC)
						{
							$r = \Model_Coop::copyCoopCategory($aC,array($sID=>array('ctID'=>$sID)));
						}
					}

					$rr = \Model_Report::getReportBase(array(array('rb.ctID','=',$sCtID)),null,array('rb.rbSort'=>'asc'));
					if (count($rr))
					{
						foreach ($rr as $aR)
						{
							$r = \Model_Report::copyReportBase($aR,array($sID=>array('ctID'=>$sID)));
						}
					}

					if ($iCopy == 2)
					{
						$tp = \Model_Teacher::getTeacherFromClass(array(array('tp.ctID','=',$sCtID)));
						if (count($tp))
						{
							$aInsert[$sID]['TeacherPosition_Table']['columns'] = array(
								'ctID','ttID','tpMaster','tpSort','tpDate'
							);
							foreach ($tp as $aT)
							{
								$aInsert[$sID]['TeacherPosition_Table']['values'][] = array(
									$sID,$aT['ttID'],$aT['tpMaster'],$aT['tpSort'],$sDate
								);
								$aTIDs[] = $aT['ttID'];
							}
						}
					}
				}

			}

			DB::start_transaction();

			if ($iCopy)
			{
				foreach ($aInsert as $sID => $aIns)
				{
					foreach ($aIns as $sTable => $aIn)
					{
						$query = DB::insert($sTable)
							->columns($aIn['columns']);

						foreach ($aIn['values'] as $aV)
						{
							$query->values($aV);
						}

						$result = $query->execute();
					}
				}
			}

			$result = DB::update('Class_Table')
				->set($aUpdate)
				->where('ctID','IN',$aKeys)
				->execute()
			;

			if (!is_null($aTIDs))
			{
				\Model_Class::resetTeacherClassSort($aTIDs);
			}

			DB::commit_transaction();
			return $result;
		}
		catch (Exception $e)
		{
			// 未決のトランザクションクエリをロールバックする
			DB::rollback_transaction();
			throw $e;
		}
	}

	public static function annualStudentYearIncrement($aSIDs = null)
	{
		if (is_null($aSIDs))
		{
			throw new Exception('処理に必要な情報がありません');
		}

		try
		{
			DB::start_transaction();

			$result = DB::update('Student_Table')
				->where('stID','IN',$aSIDs)
				->set(array('stYear'=>DB::expr('stYear + 1')))
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

	private static function getGroupID()
	{
		try
		{
			while (true):
				$sGtID = 'g'.Str::random('numeric',9);
				$result = DB::select()->from('GroupBase_Table')->where('gtID',$sGtID)->execute()->as_array();
				if (empty($result)):
					break;
				endif;
			endwhile;
			return $sGtID;
		}
		catch (Exception $e)
		{
			throw $e;
		}
	}

	private static function getGroupAdminID()
	{
		try
		{
			while (true):
				$sGaID = 'a'.Str::random('numeric',9);
				$result = DB::select()->from('GroupAdmin_Table')->where('gaID',$sGaID)->execute()->as_array();
				if (empty($result)):
					break;
				endif;
			endwhile;
			return $sGaID;
		}
		catch (Exception $e)
		{
			throw $e;
		}
	}
}
