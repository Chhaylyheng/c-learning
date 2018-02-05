<?php
class Model_Teacher extends \Model
{
	public static function getTeacherFromPostLogin($sMail = null,$sPass = null)
	{
		if (is_null($sMail) || is_null($sPass))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{   
			echo "test++++++++";
			$result = DB::select_array()
				->from('Teacher_Table')
				->where('ttMail',$sMail)
				->where('ttPass',sha1($sPass))
				->execute();

			if (!count($result))
			{ 
				DB::start_transaction();
				$result2 = DB::update('Teacher_Table')
					->value('ttPassMiss', DB::expr('`ttPassMiss`+1'))
					->where('ttMail',$sMail)
					->execute();
				DB::commit_transaction();
			}
			return $result;
		}
		catch (Exception $e)
		{   
			echo "ttt****";
			// 未決のトランザクションクエリをロールバックする
			DB::rollback_transaction();
			throw $e;
		}
	}

	public static function setLoginUpdate($aUser = null,$bTZ = false)
	{
		if (is_null($aUser))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			$query = DB::update('Teacher_Table');
			$query->value('ttLoginNum', DB::expr('`ttLoginNum`+1'));
			$query->value('ttLastLoginDate', DB::expr('`ttLoginDate`'));
			$query->value('ttLoginDate', DB::expr('NOW()'));
			$query->value('ttPassMiss', 0);
			$query->value('ttUA', Input::user_agent());
			$query->value('ttHash', sha1($aUser['ttMail'].$aUser['ttPass']));
			if ($bTZ)
			{
				$query->value('ttTimeZone', $aUser['ttTimeZone']);
			}
			$query->where('ttID',$aUser['ttID']);
			$result = $query->execute();
			DB::commit_transaction();

			$result = self::getTeacherFromID($aUser['ttID']);
			return $result;
		}
		catch (Exception $e)
		{
			// 未決のトランザクションクエリをロールバックする
			DB::rollback_transaction();
			throw $e;
		}
	}




	public static function getTeacherFromSocialID($sUID = null,$sSocial = null,$sTtID = null)
	{
		if (is_null($sUID) || is_null($sSocial))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			$result = DB::select_array()
				->from('Teacher_Table')
				->where('tt'.ucfirst($sSocial).'ID',$sUID)
				->execute();
			return $result;
		}
		catch (Exception $e)
		{
			// 未決のトランザクションクエリをロールバックする
			DB::rollback_transaction();
			throw $e;
		}
	}
	public static function getTeacherFromLoginID($sID = null)
	{
		if (is_null($sID))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			$query = DB::update('Teacher_Table');
			$query->value('ttLoginNum', DB::expr('`ttLoginNum`+1'));
			$query->value('ttLastLoginDate', DB::expr('`ttLoginDate`'));
			$query->value('ttLoginDate', DB::expr('NOW()'));
			$query->value('ttPassMiss', 0);
			$query->value('ttUA', Input::user_agent());
			$query->where('ttLoginID',$sID);
			$query->where('ttStatus','>=',1);
			$result = $query->execute();
			DB::commit_transaction();

			$query = DB::select_array()
				->from('Teacher_Table')
				->where('ttLoginID',$sID)
				->where('ttStatus','>=',1)
			;
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
	public static function getTeacherFromMail($sMail = null, $sTtID = null)
	{
		$query = DB::select_array()->from('Teacher_Table')->where('ttMail',$sMail);
		if (!is_null($sTtID))
		{
			$query->where('ttID','!=',$sTtID);
		}
		$result = $query->execute();

		return $result;
	}
	public static function getTeacherFromHash($sHash = null)
	{
		$query = DB::select_array()->from('Teacher_View')->where('ttHash',$sHash);
		$result = $query->execute();

		return $result;
	}
	public static function getTeacherFromID($sID = null)
	{
		$result = DB::select_array()->from('Teacher_View')->where('ttID',$sID)->execute();
		return $result;
	}
	public static function getTeacherFromID_ex($sID = null)
	{
		$result = DB::select_array()->from('Teacher_Table')->where('ttID',$sID)->execute();
		return $result;
	}

	public static function getPreTeacher($aAndWhere = null,$aOrWhere = null,$aSort = null)
	{
		$query = DB::select_array()->from('PreTeacher_Table');
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

	public static function getTeacher($aAndWhere = null,$aOrWhere = null,$aSort = null,$aOrGroup = null)
	{
		$query = DB::select_array()->from('Teacher_View');
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

	public static function getTeacherFromClass($aAndWhere = null,$aOrWhere = null,$aSort = null)
	{
/*
		$query = DB::select_array()->from('TeacherClassList_View');
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

		$result = $query->execute();
		return $result;
	}

	public static function insertPreTeacher($aInsert = null)
	{
		if (is_null($aInsert))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			$aInsert['ttHash'] = self::getPreHash();
			$query = DB::delete('PreTeacher_Table')->where('ttMail',$aInsert['ttMail'])->execute();
			$query = DB::insert('PreTeacher_Table')->set($aInsert)->execute();
			DB::commit_transaction();
			// クエリの結果を返す
			return $aInsert['ttHash'];
		}
		catch (Exception $e)
		{
			// 未決のトランザクションクエリをロールバックする
			DB::rollback_transaction();
			throw $e;
		}
	}

	public static function insertTeacher($aInsert = null,$sImg = null)
	{
		if (is_null($aInsert))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			$result = Model_Payment::getPlan(array(array('ptID','=',1)));
			if (!count($result))
			{
				throw new Exception('契約プランマスタに情報がありません');
			}
			$aPlan = $result->current();
			$aPlanIns = array(
				'coNO' => 1,
				'ptID' => 1,
				'coClassNum' => 1,
				'coStuNum' => 50,
				'coCapacity' => $aPlan['ptCapacity'],
				'coPayment' => 0,
				'coStartDate' => date('Ymd'),
				'coTermDate' => date('Ymd',strtotime('+'.CL_FREE_DAYS.'days')),
			);

			DB::start_transaction();
			// 先生IDを生成
			$sTtID = self::getTeacherID();
			$aInsert['teacher']['ttID'] = $sTtID;
			$aInsert['account']['ttID'] = $sTtID;
			$aPlanIns['ttID'] = $sTtID;
			if (!is_null($sImg))
			{
				$aInsert['teacher']['ttImage'] = $sTtID.'.jpg';
				$sImagePath = CL_UPPATH.'/profile/t/';
				file_put_contents($sImagePath.$aInsert['teacher']['ttImage'], $sImg);
				chmod($sImagePath.$aInsert['teacher']['ttImage'],0666);
			}
			$query = DB::insert('Teacher_Table')->set($aInsert['teacher'])->execute();
			$query = DB::insert('AccountHistory_Table')->set($aInsert['account'])->execute();

			$query = DB::insert('Contract_Table')->set($aPlanIns)->execute();

			$query = DB::delete('PreTeacher_Table')->where('ttMail',$aInsert['teacher']['ttMail'])->execute();
			DB::commit_transaction();
			// クエリの結果を返す
			return $sTtID;
		}
		catch (Exception $e)
		{
			// 未決のトランザクションクエリをロールバックする
			DB::rollback_transaction();
			throw $e;
		}
	}


	public static function insertOrgTeacher($aInsert = null,$sImg = null)
	{
		if (is_null($aInsert))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			$aPlanIns = array(
				'coNO' => 1,
				'ptID' => 99,
				'coClassNum' => 0,
				'coStuNum' => 0,
				'coCapacity' => 0,
				'coPayment' => 0,
				'coStartDate' => date('Ymd'),
			);

			DB::start_transaction();
			// 先生IDを生成
			$sTtID = self::getTeacherID();
			$aInsert['teacher']['ttID'] = $sTtID;
			$aInsert['group']['ttID'] = $sTtID;
			$aPlanIns['ttID'] = $sTtID;
			if (!is_null($sImg))
			{
				$aInsert['teacher']['ttImage'] = $sTtID.'.jpg';
				$sImagePath = CL_UPPATH.'/profile/t/';
				file_put_contents($sImagePath.$aInsert['teacher']['ttImage'], $sImg);
				chmod($sImagePath.$aInsert['teacher']['ttImage'],0666);
			}
			$query = DB::insert('Teacher_Table')->set($aInsert['teacher'])->execute();
			$query = DB::insert('GroupTPos_Table')->set($aInsert['group'])->execute();
			$query = DB::insert('Contract_Table')->set($aPlanIns)->execute();

			$query = DB::delete('PreTeacher_Table')->where('ttMail',$aInsert['teacher']['ttMail'])->execute();
			DB::commit_transaction();
			// クエリの結果を返す
			return $sTtID;
		}
		catch (Exception $e)
		{
			// 未決のトランザクションクエリをロールバックする
			DB::rollback_transaction();
			throw $e;
		}
	}


	public static function insertTeacherFromCSV($aCSV = null,$aGroup = null)
	{
		if (is_null($aCSV) || is_null($aGroup))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			$aIDs = null;

			$aPlanIns = array(
				'coNO' => 1,
				'ptID' => 99,
				'coClassNum' => 0,
				'coStuNum' => 0,
				'coCapacity' => 0,
				'coPayment' => 0,
				'coStartDate' => date('Ymd'),
			);

			foreach ($aCSV as $aS)
			{
				// IDを生成
				$sTtID = self::getTeacherID($aIDs);
				$aIDs[] = $sTtID;

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
					'ttID'            => $sTtID,
					'ttMail'          => $aS[0],
					'ttPass'          => $sPass,
					'ttFirst'         => $sFirst,
					'ttName'          => $aS[2],
					'ttDept'          => $aS[3],
					'ttSubject'       => $aS[4],
					'ttLoginNum'      => 0,
					'ttLastLoginDate' => '00000000000000',
					'ttLoginDate'     => '00000000000000',
					'ttPassDate'      => '00000000',
					'ttPassMiss'      => 0,
					'ttHash'          => $sHash,
					'ttStatus'        => 1,
					'ttDate'          => date('YmdHis'),
				);
				if ($aGroup['gtLDAP'])
				{
					if (isset($aS[5]) && $aS[5] != '')
					{
						$aInsert['ttLoginID'] = $aS[5];
					}
				}

				$query = DB::insert('Teacher_Table')->set($aInsert)->execute();

				$aPlanIns['ttID'] = $sTtID;
				$query = DB::insert('Contract_Table')->set($aPlanIns)->execute();

				$aTPos = array(
					'gtID' => $aGroup['gtID'],
					'ttID' => $sTtID,
					'gpDate' => date('YmdHis'),
				);
				$query = DB::insert('GroupTPos_Table')->set($aTPos)->execute();
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

	public static function updateTeacherFromCSV($aUpdate = null,$aGroup = null)
	{
		if (is_null($aUpdate) || is_null($aGroup))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();

			foreach ($aUpdate as $sTtID => $aS)
			{
				$aInsert = array(
					'ttName'    => $aS[2],
					'ttDept'    => $aS[3],
					'ttSubject' => $aS[4],
				);
				if ($aGroup['gtLDAP'])
				{
					if (isset($aS[5]))
					{
						$aInsert['ttLoginID'] = ($aS[5])? $aS[5]:null;
					}
				}
				if (trim($aS[1]))
				{
					$aInsert['ttFirst'] = $aS[1];
					$aInsert['ttPass'] = sha1($aS[1]);
					$aInsert['ttHash'] = sha1($aS[0].$aInsert['ttPass']);
				}

				$query = DB::update('Teacher_Table')
					->set($aInsert)
					->where('ttID','=',$sTtID)
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

	public static function updateTeacher($sTtID = null,$aUpdate = null,$sImg = null)
	{
		if (is_null($sTtID) || is_null($aUpdate))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			if (!is_null($sImg))
			{
				$aUpdate['ttImage'] = $sTtID.'.jpg';
				$sImagePath = CL_UPPATH.'/profile/t/';
				file_put_contents($sImagePath.$aUpdate['ttImage'], $sImg);
				chmod($sImagePath.$aUpdate['ttImage'],0666);
			}
			$query = DB::update('Teacher_Table');
			$query->where('ttID',$sTtID);
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

	public static function deleteTeacher($aAct = null, $aClasses = null)
	{
		if (is_null($aAct))
		{
			throw new Exception('処理に必要な情報がありません');
		}

		$sTtID = $aAct['ttID'];

		try
		{
			$aFiles = null;
			$result = Model_File::getFile(array(array('fUser','=',$sTtID)));
			if (count($result))
			{
				$aFiles = $result->as_array('fID');
			}

			$aClassTable = array(
				'AttendBook_Table','AttendCalendar_Table','AttendState_Master',
				'ClassGeometry_Table',
				'ClassMailHistory_Table','ClassNews_Table',
				'CoopCategory_Table','MaterialCategory_Table',
				'QuestBase_Table',
				'TestBase_Table',
				'ReportBase_Table','ReportRate_Master',
				'StudentPosition_Table',
				'Class_Table'
			);

			DB::start_transaction();

			if (!is_null($aClasses))
			{
				foreach ($aClasses as $aC)
				{
					foreach ($aClassTable as $sT)
					{
						$result = DB::delete($sT)
							->where('ctID',$aC['ctID'])
							->execute()
						;
					}
				}
			}

			if ($aAct['ttImage'])
			{
				$sImagePath = CL_UPPATH.'/profile/t/'.$aAct['ttImage'];
				@unlink($sImagePath);
			}

			$result = DB::delete('File_Table')
				->where('fUser',$sTtID)
				->execute();

			$result = DB::delete('Contract_Table')
				->where('ttID',$sTtID)
				->execute();

			$result = DB::delete('TeacherPosition_Table')
				->where('ttID',$sTtID)
				->execute();

			$result = DB::delete('Teacher_Table')
				->where('ttID',$sTtID)
				->execute();

			$result = DB::insert('TeacherMissingID_Table')
				->set(array('ttID'=>$sTtID))
				->execute();

			DB::commit_transaction();

			if (!is_null($aFiles))
			{
				$sAwsSavePath = 'teacher'.DS.$sTtID;
				foreach ($aFiles as $fID => $aF)
				{
					\Clfunc_Aws::deleteFile($sAwsSavePath,$fID.'.'.$aF['fExt']);
					if ($aF['fFileType'] == 1)
					{
						\Clfunc_Aws::deleteFile($sAwsSavePath, CL_PREFIX_THUMBNAIL.$fID.'.'.$aF['fExt']);
					}
					if ($aF['fFileType'] == 2)
					{
						\Clfunc_Aws::deleteFile($sAwsSavePath,CL_PREFIX_ENCODE.$fID.CL_AWS_ENCEXT);
						\Clfunc_Aws::deleteFile($sAwsSavePath,CL_PREFIX_THUMBNAIL.$fID.'-00001.png');
					}
				}
			}

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

	public static function deleteGroupTeacher($aTtIDs = null, $aTtImgs = null)
	{
		if (is_null($aTtIDs))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			if (!is_null($aTtImgs))
			{
				foreach ($aTtImgs as $sT)
				{
					$sImagePath = CL_UPPATH.'/profile/t/'.$sT;
					@unlink($sImagePath);
				}
			}
			DB::start_transaction();

			$result = DB::delete('GroupTPos_Table')
				->where('ttID','IN',$aTtIDs)
				->execute();

			$result = DB::delete('Contract_Table')
				->where('ttID','IN',$aTtIDs)
				->execute();

			$result = DB::delete('TeacherPosition_Table')
				->where('ttID','IN',$aTtIDs)
				->execute();

			$result = DB::delete('Teacher_Table')
				->where('ttID','IN',$aTtIDs)
				->execute();

			foreach ($aTtIDs as $sT)
			{
				$result = DB::insert('TeacherMissingID_Table')
					->set(array('ttID'=>$sT))
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

	public static function setTeacherClassNum($aIDs = null)
	{
		if (is_null($aIDs))
		{
			throw new Exception('処理に必要な情報がありません');
		}

		try
		{
			foreach ($aIDs as $sTtID)
			{
				$result = DB::select_array()
					->from('TeacherUsed_Table')
					->where('ttID','=',$sTtID)
					->execute()
				;
				if (!count($result))
				{
					self::setTeacherUsed($sTtID);
					continue;
				}

				$aUpdate = array(
					'ttClassNum'=>0,
					'ttCloseNum'=>0,
				);
				$res = Model_Class::getClassFromTeacher($sTtID, 1);
				$aUpdate['ttClassNum'] = count($res);
				$res = Model_Class::getClassFromTeacher($sTtID, 0);
				$aUpdate['ttCloseNum'] = count($res);

				DB::start_transaction();

				$result = DB::update('TeacherUsed_Table')
					->set($aUpdate)
					->where('ttID','=',$sTtID)
					->execute()
				;

				DB::commit_transaction();
			}
			return;
		}
		catch (Exception $e)
		{
			// 未決のトランザクションクエリをロールバックする
			DB::rollback_transaction();
			throw $e;
		}


	}



	public static function setTeacherUsed($sID = null)
	{
		if (is_null($sID))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			$aInsert = array(
				'ttID' => $sID,
				'ttClassNum' => 0,
				'ttCloseNum' => 0,
				'ttStuNum' => 0,
				'ttDiskUsed' => 0,
			);

			$res = Model_Class::getClassFromTeacher($sID, 1);
			$aInsert['ttClassNum'] = count($res);
			$res = Model_Class::getClassFromTeacher($sID, 0);
			$aInsert['ttCloseNum'] = count($res);

			$iStuNum = 0;
			$iSize = 0;
			$result = Model_Class::getClassFromTeacher($sID);
			if (count($result))
			{
				foreach ($result as $aC)
				{
					$iStuNum = ($iStuNum < (int)$aC['scNum'])? (int)$aC['scNum']:$iStuNum;

					$res1 = Model_Coop::getCoopCategoryFromClass($aC['ctID']);
					if (count($res1))
					{
						foreach ($res1 as $aCC)
						{
							$iSize += (int)$aCC['ccTotalSize'];
						}
					}
					$res1 = Model_Material::getMaterialCategoryFromClass($aC['ctID']);
					if (count($res1))
					{
						foreach ($res1 as $aMC)
						{
							$iSize += (int)$aMC['mcTotalSize'];
						}
					}

					$res1 = Model_Report::getReportBase(array(array('rb.ctID','=',$aC['ctID'])));
					if (count($res1))
					{
						foreach ($res1 as $aR)
						{
							$iSize += (int)$aR['baseFSize'];
							$iSize += (int)$aR['resultFSize'];

							$res2 = Model_Report::getReportPut(array(array('rp.rbID','=',$aR['rbID'])));
							if (count($res2))
							{
								foreach ($res2 as $aP)
								{
									$iSize += (int)$aP['fSize1'];
									$iSize += (int)$aP['fSize2'];
									$iSize += (int)$aP['fSize3'];
									$iSize += (int)$aP['rSize1'];
									$iSize += (int)$aP['rSize2'];
									$iSize += (int)$aP['rSize3'];
								}
							}
						}
					}
				}
			}

			$aInsert['ttStuNum'] = $iStuNum;
			$aInsert['ttDiskUsed'] = $iSize;

			DB::start_transaction();

			DB::delete('TeacherUsed_Table')
				->where('ttID',$sID)
				->execute()
			;

			$result = DB::Insert('TeacherUsed_Table')
				->set($aInsert)
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

	public static function getTeacherPosition($aAndWhere = null,$aOrWhere = null,$aSort = null)
	{
		$query = DB::select_array()->from('TeacherPosition_Table');
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

	private static function getTeacherID($aIDs = null)
	{
		try
		{
			while (true)
			{
				$sTtID = 'tt'.Str::random('numeric',8);
				if (!is_null($aIDs))
				{
					if (array_search($sTtID, $aIDs) !== false)
					{
						continue;
					}
				}
				$result1 = DB::select()->from('Teacher_Table')->where('ttID',$sTtID)->execute()->as_array();
				$result2 = DB::select()->from('TeacherMissingID_Table')->where('ttID',$sTtID)->execute()->as_array();
				if (empty($result1) && empty($result2))
				{
					break;
				}
			}
			return $sTtID;
		}
		catch (Exception $e)
		{
			throw $e;
		}
	}

	private static function getPreHash()
	{
		try
		{
			while (true)
			{
				$sHash = Str::random('alnum', 24);
				$result = DB::select()->from('PreTeacher_Table')->where('ttHash',$sHash)->execute()->as_array();
				if (empty($result))
				{
					break;
				}
			}
			return $sHash;
		}
		catch (Exception $e)
		{
			throw $e;
		}
	}



}
