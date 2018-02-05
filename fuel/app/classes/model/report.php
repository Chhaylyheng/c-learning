<?php
class Model_Report extends \Model
{
	public static function getReportBase($aAndWhere = null,$aOrWhere = null,$aSort = null)
	{

		$query = DB::select_array(
				array(
					'rb.*',
					array('bf.fName','baseFName'),
					array('bf.fSize','baseFSize'),
					array('bf.fExt','baseFExt'),
					array('bf.fContentType','baseFContentType'),
					array('bf.fFileType','baseFFileType'),
					array('bf.fPath','baseFPath'),
					array('bf.fUserType','baseFUserType'),
					array('bf.fUser','baseFUser'),
					array('bf.fDate','baseFDate'),
					array('rf.fName','resultFName'),
					array('rf.fSize','resultFSize'),
					array('rf.fExt','resultFExt'),
					array('rf.fContentType','resultFContentType'),
					array('rf.fFileType','resultFFileType'),
					array('rf.fPath','resultFPath'),
					array('rf.fUserType','resultFUserType'),
					array('rf.fUser','resultFUser'),
					array('rf.fDate','resultFDate'),
					array('zf.fName','zipFName'),
					array('zf.fSize','zipFSize'),
					array('zf.fExt','zipFExt'),
					array('zf.fContentType','zipFContentType'),
					array('zf.fFileType','zipFFileType'),
					array('zf.fPath','zipFPath'),
					array('zf.fUserType','zipFUserType'),
					array('zf.fUser','zipFUser'),
					array('zf.fDate','zipFDate'),
				)
			)
			->from(array('ReportBase_Table','rb'))
			->join(array('File_Table','bf'),'LEFT')
			->on('rb.baseFID','=','bf.fID')
			->join(array('File_Table','rf'),'LEFT')
			->on('rb.resultFID','=','rf.fID')
			->join(array('File_Table','zf'),'LEFT')
			->on('rb.zipFID','=','zf.fID')
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

	public static function setReportStatus($sCtID)
	{
		try
		{
			$result = DB::update(array('ReportBase_Table','rb'))
				->value('rb.rbLastPutDate', DB::expr('(SELECT MAX(rpDate) FROM ReportPut_Table WHERE rbID=rb.rbID)'))
				->value('rb.rbPutNum', DB::expr('(SELECT count(no) FROM ReportPut_Table WHERE rbID=rb.rbID AND (rpDate != "0000-00-00" OR rpTeachPut = 1 ))'))
				->where('rb.ctID',$sCtID)
				->execute();
			;
			return;
		}
		catch (Exception $e)
		{
			// 未決のトランザクションクエリをロールバックする
			DB::rollback_transaction();
			throw $e;
		}
	}


	public static function insertReport($aInsert = null)
	{
		if (is_null($aInsert))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			$sRbID = self::getReportID();
			$aInsert['rbID'] = $sRbID;
			$aInsert['rbSort'] = self::getReportSort($aInsert['ctID']);
			$result = DB::insert('ReportBase_Table')->set($aInsert)->execute();
			DB::commit_transaction();
			// クエリの結果を返す
			return $sRbID;
		}
		catch (Exception $e)
		{
			// 未決のトランザクションクエリをロールバックする
			DB::rollback_transaction();
			throw $e;
		}
	}

	public static function updateReport($aUpdate = null,$aAndWhere = null,$aOrWhere = null)
	{
		if (is_null($aUpdate))
		{
			throw new Exception('処理に必要な情報がありません');
		}
			try
		{
			DB::start_transaction();
			$query = DB::update('ReportBase_Table');
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

	public static function updateReportPutNum($sRbID = null, $iNum = null)
	{
		if (is_null($sRbID) || is_null($iNum))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();

			$result = DB::update('ReportBase_Table')
			->where('rbID',$sRbID)
			->set(array('rbPutNum'=>DB::expr('rbPutNum + '.$iNum)))
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

	public static function deleteReport($sID = null,$aActive = null, $aPut = null)
	{
		if (is_null($sID) || is_null($aActive))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();

			if (!is_null($aPut))
			{
				foreach ($aPut as $aM)
				{
					for($i = 1; $i <= 3; $i++)
					{
						if ($aM['fID'.$i] != '')
						{
							$result = DB::delete('File_Table')->where('fID',$aM['fID'.$i])->execute();
						}
						if ($aM['rID'.$i] != '')
						{
							$result = DB::delete('File_Table')->where('fID',$aM['rID'.$i])->execute();
						}
					}
				}
			}

			if ($aActive['baseFID'] != '')
			{
				// $result = DB::delete('File_Table')->where('fID',$aActive['baseFID'])->execute();
			}
			if ($aActive['resultFID'] != '')
			{
				// $result = DB::delete('File_Table')->where('fID',$aActive['resultFID'])->execute();
			}

			$result = DB::delete('ReportComment_Table')->where('rbID',$sID)->execute();
			$result = DB::delete('ReportRate_Table')->where('rbID',$sID)->execute();
			$result = DB::delete('ReportPut_Table')->where('rbID',$sID)->execute();
			$result = DB::delete('ReportBase_Table')->where('rbID',$sID)->execute();

			$query = DB::update('ReportBase_Table');
			$query->and_where('ctID',$aActive['ctID']);
			$query->and_where('rbSort','>',$aActive['rbSort']);
			$query->set(array('rbSort'=>DB::expr('rbSort - 1')));
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

	public static function sortReport($aReport = null,$sSort = null)
	{
		if (is_null($aReport) || is_null($sSort))
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
			$query = DB::update('ReportBase_Table');
			$query->and_where('ctID',$aReport['ctID']);
			$query->and_where('rbSort',$aReport['rbSort']+$iWhere);
			$query->set(array('rbSort'=>DB::expr('rbSort'.$iUp1)));
			$result = $query->execute();
			$query = DB::update('ReportBase_Table');
			$query->and_where('rbID',$aReport['rbID']);
			$query->set(array('rbSort'=>DB::expr('rbSort'.$iUp2)));
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

	public static function getReportPut($aAndWhere = null,$aOrWhere = null,$aSort = null)
	{
		$query = DB::select_array(
			array(
				'rb.ctID',
				'rp.*',
				array('f1.fName','fName1'),
				array('f1.fSize','fSize1'),
				array('f1.fExt','fExt1'),
				array('f1.fContentType','fContentType1'),
				array('f1.fFileType','fFileType1'),
				array('f1.fPath','fPath1'),
				array('f1.fUserType','fUserType1'),
				array('f1.fUser','fUser1'),
				array('f1.fDate','fDate1'),

				array('f2.fName','fName2'),
				array('f2.fSize','fSize2'),
				array('f2.fExt','fExt2'),
				array('f2.fContentType','fContentType2'),
				array('f2.fFileType','fFileType2'),
				array('f2.fPath','fPath2'),
				array('f2.fUserType','fUserType2'),
				array('f2.fUser','fUser2'),
				array('f2.fDate','fDate2'),
				array('f3.fName','fName3'),

				array('f3.fSize','fSize3'),
				array('f3.fExt','fExt3'),
				array('f3.fContentType','fContentType3'),
				array('f3.fFileType','fFileType3'),
				array('f3.fPath','fPath3'),
				array('f3.fUserType','fUserType3'),
				array('f3.fUser','fUser3'),
				array('f3.fDate','fDate3'),

				array('r1.fName','rName1'),
				array('r1.fSize','rSize1'),
				array('r1.fExt','rExt1'),
				array('r1.fContentType','rContentType1'),
				array('r1.fFileType','rFileType1'),
				array('r1.fPath','rPath1'),
				array('r1.fUserType','rUserType1'),
				array('r1.fUser','rUser1'),
				array('r1.fDate','rDate1'),

				array('r2.fName','rName2'),
				array('r2.fSize','rSize2'),
				array('r2.fExt','rExt2'),
				array('r2.fContentType','rContentType2'),
				array('r2.fFileType','rFileType2'),
				array('r2.fPath','rPath2'),
				array('r2.fUserType','rUserType2'),
				array('r2.fUser','rUser2'),
				array('r2.fDate','rDate2'),

				array('r3.fName','rName3'),
				array('r3.fSize','rSize3'),
				array('r3.fExt','rExt3'),
				array('r3.fContentType','rContentType3'),
				array('r3.fFileType','rFileType3'),
				array('r3.fPath','rPath3'),
				array('r3.fUserType','rUserType3'),
				array('r3.fUser','rUser3'),
				array('r3.fDate','rDate3'),
			))
			->from(array('ReportPut_Table','rp'))
			->join(array('ReportBase_Table','rb'),'LEFT')
			->on('rp.rbID','=','rb.rbID')
			->join(array('File_Table','f1'),'LEFT')
			->on('rp.fID1','=','f1.fID')
			->join(array('File_Table','f2'),'LEFT')
			->on('rp.fID2','=','f2.fID')
			->join(array('File_Table','f3'),'LEFT')
			->on('rp.fID3','=','f3.fID')
			->join(array('File_Table','r1'),'LEFT')
			->on('rp.rID1','=','r1.fID')
			->join(array('File_Table','r2'),'LEFT')
			->on('rp.rID2','=','r2.fID')
			->join(array('File_Table','r3'),'LEFT')
			->on('rp.rID3','=','r3.fID')
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

	public static function insertPut($aInsert = null)
	{
		if (is_null($aInsert))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			$aSet = null;
			if ($aInsert['rpDate'] != CL_DATETIME_DEFAULT)
			{
				$aSet = array(
						'rbLastPutDate' => $aInsert['rpDate'],
						'rbPutNum' => \DB::expr('rbPutNum + 1'),
				);
			}
			elseif (isset($aInsert['rpTeachPut']) && $aInsert['rpTeachPut'] > 0)
			{
				$aSet = array(
						'rbPutNum' => \DB::expr('rbPutNum + 1'),
				);
			}

			DB::start_transaction();
			$result = DB::insert('ReportPut_Table')->set($aInsert)->execute();
			$iNO = $result[0];

			if (!is_null($aSet))
			{
				$result = DB::update('ReportBase_Table')
					->set($aSet)
					->where('rbID',$aInsert['rbID'])
					->execute();
			}
			DB::commit_transaction();
			return $iNO;
		}
		catch (Exception $e)
		{
			// 未決のトランザクションクエリをロールバックする
			DB::rollback_transaction();
			throw $e;
		}
	}

	public static function updatePut($aUpdate = null,$aPut = null)
	{
		if (is_null($aUpdate) || is_null($aPut))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			$result = DB::update('ReportPut_Table')
				->where('no',$aPut['no'])
				->set($aUpdate)
				->execute();

			if (isset($aUpdate['rpDate']))
			{
				$result = DB::update('ReportBase_Table')
					->set(array(
						'rbLastPutDate' => $aUpdate['rpDate'],
					))
					->where('rbID',$aPut['rbID'])
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

	public static function deletePut($aPut = null)
	{
		if (is_null($aPut))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();

			for($i = 1; $i <= 3; $i++)
			{
				if ($aPut['fID'.$i] != '')
				{
					$result = DB::delete('File_Table')->where('fID',$aPut['fID'.$i])->execute();
				}
				if ($aPut['rID'.$i] != '')
				{
					$result = DB::delete('File_Table')->where('fID',$aPut['rID'.$i])->execute();
				}
			}

			$result = DB::delete('ReportComment_Table')->where('rbID',$aPut['rbID'])->where('stID',$aPut['stID'])->execute();
			$result = DB::delete('ReportRate_Table')->where('rbID',$aPut['rbID'])->where('stID',$aPut['stID'])->execute();
			$result = DB::delete('ReportPut_Table')->where('rbID',$aPut['rbID'])->where('stID',$aPut['stID'])->execute();


			$subquery = DB::select(\DB::expr('max(rp.rpDate)'))->from(array('ReportPut_Table','rp'))
				->where('rp.rbID',$aPut['rbID'])
				->compile();

			$result = DB::update('ReportBase_Table')
				->set(array(
					'rbLastPutDate' => \DB::expr('('.$subquery.')'),
					'rbPutNum' => \DB::expr('rbPutNum - 1'),
				))
				->where('rbID',$aPut['rbID'])
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

	public static function getRateMasterFromClass($sCtID = null)
	{
		if (is_null($sCtID))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			$result = DB::select()
				->from('ReportRate_Master')
				->where('ctID',$sCtID)
				->order_by('rrScore','asc')
				->execute()
			;

			if (count($result))
			{
				return $result;
			}

			DB::start_transaction();

			$insert = DB::insert('ReportRate_Master')
				->columns(array('ctID', 'rrScore', 'rrName', 'rrDate'))
				->values(array($sCtID, 1, 'A', DB::expr('NOW()')))
				->values(array($sCtID, 2, 'B', DB::expr('NOW()')))
				->values(array($sCtID, 3, 'C', DB::expr('NOW()')))
				->values(array($sCtID, 4, 'D', DB::expr('NOW()')))
				->values(array($sCtID, 5, 'F', DB::expr('NOW()')))
				->execute()
			;

			DB::commit_transaction();

			$result = DB::select()
				->from('ReportRate_Master')
				->where('ctID',$sCtID)
				->order_by('rrScore','asc')
				->execute()
			;
			return $result;
		}
		catch (Exception $e)
		{
			// 未決のトランザクションクエリをロールバックする
			DB::rollback_transaction();
			throw $e;
		}
	}

	public static function updateRateMaster($sCtID = null, $aUpdate = null)
	{
		if (is_null($sCtID) || is_null($aUpdate))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();

			$result = DB::delete('ReportRate_Master')
				->and_where('ctID','=',$sCtID)
				->execute()
			;

			$query = DB::insert('ReportRate_Master')
				->columns(array('ctID', 'rrScore', 'rrName', 'rrDate'))
			;

			foreach($aUpdate as $i => $aR)
			{
				$aIns = array();
				$aIns[] = $sCtID;
				$aIns[] = $i;
				$aIns[] = $aR;
				$aIns[] = date('YmdHis');

				$query->values($aIns);
			}

			$result = $query->execute();

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

	public static function getReportComment($aAndWhere = null,$aOrWhere = null,$aSort = null)
	{
		$query = DB::select_array(
			array(
				'rc.*',
				array('st.stName','stName'),
				array('tt.ttName','ttName'),
				array('at.atName','atName'),
			)
		)
		->from(array('ReportComment_Table','rc'))
		->join(array('Student_Table','st'),'LEFT')
		->on('rc.rcID','=','st.stID')
		->join(array('Teacher_Table','tt'),'LEFT')
		->on('rc.rcID','=','tt.ttID')
		->join(array('Assistant_Table','at'),'LEFT')
		->on('rc.rcID','=','at.atID')
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

	public static function insertReportComment($aInsert = null)
	{
		if (is_null($aInsert))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			$result = DB::insert('ReportComment_Table')->set($aInsert)->execute();
			$iNO = $result[0];
			$result = DB::update('ReportPut_Table')
			->set(array(
				'rpComNum' => DB::expr('rpComNum + 1'),
			))
			->where('rbID',$aInsert['rbID'])
			->where('stID',$aInsert['stID'])
			->execute();
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

	public static function updateReportComment($aUpdate = null,$aAndWhere = null,$aOrWhere = null)
	{
		if (is_null($aUpdate))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			$query = DB::update('ReportComment_Table');
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
	public static function deleteReportComment($aCom = null)
	{
		if (is_null($aCom))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			$result = DB::delete('ReportComment_Table')
			->where('no',$aCom['no'])
			->execute();

			$result = DB::delete('ReportComment_Table')
			->where('rcBranch',$aCom['no'])
			->execute();

			$res = DB::update('ReportPut_Table')
			->and_where('rbID',$aCom['rbID'])
			->and_where('stID',$aCom['stID'])
			->set(array('rpComNum'=>DB::expr('rpComNum - '.($result + 1))))
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

	public static function getReportRate($aAndWhere = null,$aOrWhere = null,$aSort = null)
	{
		$query = DB::select_array()
		->from(array('ReportRate_Table','rr'))
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

	public static function insertReportRate($aInsert = null)
	{
		if (is_null($aInsert))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();

			$result = DB::insert('ReportRate_Table')
				->set($aInsert)
				->execute()
			;

			self::setReportCount($aInsert['rbID'],$aInsert['stID']);

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

	public static function updateReportRate($aUpdate = null,$aRate = null)
	{
		if (is_null($aUpdate) || is_null($aRate))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			$result = DB::update('ReportRate_Table')
			->where('rbID',$aRate['rbID'])
			->where('stID',$aRate['stID'])
			->where('rrID',$aRate['rrID'])
			->set($aUpdate)
			->execute();

			self::setReportCount($aRate['rbID'],$aRate['stID']);

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

	public static function setReportCount($sRbID = null,$sStID = null)
	{
		if (is_null($sRbID) || is_null($sStID))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			$aInsert = array(
					'rbID' => $sRbID,
					'stID' => $sStID,
					'rcNum' => 0,
					'rcTotal' => 0,
					'rcAvg' => 0,
					'rc1' => 0,
					'rc2' => 0,
					'rc3' => 0,
					'rc4' => 0,
					'rc5' => 0,
					'rcDate' => date('YmdHis'),
			);
			$result = self::getReportRate(array(array('rr.rbID','=',$sRbID),array('rr.stID','=',$sStID)));
			if (count($result))
			{
				foreach ($result as $r)
				{
					$iS = (int)$r['rrScore'];

					$aInsert['rcNum']++;
					$aInsert['rcTotal'] += $iS;
					$aInsert['rc'.$iS]++;
				}
				$aInsert['rcAvg'] = ($aInsert['rcNum'] > 0)? round($aInsert['rcTotal'] / $aInsert['rcNum'], 1):0;
			}

			DB::start_transaction();

			$result = DB::delete('ReportCount_Table')
				->where('rbID',$sRbID)
				->where('stID',$sStID)
				->execute()
			;

			$result = DB::insert('ReportCount_Table')
				->set($aInsert)
				->execute()
			;

			$result = DB::update('ReportPut_Table')
				->set(array('rpAvgScore'=>$aInsert['rcAvg']))
				->where('rbID',$sRbID)
				->where('stID',$sStID)
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

	public static function getReportCount($aAndWhere = null,$aOrWhere = null,$aSort = null)
	{
		$query = DB::select_array()
		->from(array('ReportCount_Table','rc'))
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

	public static function copyReportBase($aReport = null, $aSelClass = null)
	{
		if (is_null($aReport) || is_null($aSelClass))
		{
			throw new Exception('処理に必要な情報がありません');
		}

		$sNow = date('YmdHis');

		# ベースデータ生成
		$aBase = array(
			'rbID' => null,
			'ctID' => null,
			'rbSort' => 0,
			'rbTitle' => $aReport['rbTitle'],
			'rbText' => $aReport['rbText'],
			'baseFID' => $aReport['baseFID'],
			'resultFID' => $aReport['resultFID'],
			'rbPublic' => 0,
			'rbRatePublic' => $aReport['rbRatePublic'],
			'rbShare' => $aReport['rbShare'],
			'rbAnonymous' => $aReport['rbAnonymous'],
			'rbDate' => $sNow,
		);

		try
		{
			DB::start_transaction();

			foreach ($aSelClass as $sCtID => $aC)
			{
				$sRbID = self::getReportID();

				$aBase['rbID'] = $sRbID;
				$aBase['ctID'] = $sCtID;
				$aBase['rbSort'] = self::getReportSort($sCtID);

				$result = DB::insert('ReportBase_Table')
				->set($aBase)
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

	private static function getReportID()
	{
		try
		{
			while (true):
				$sRbID = 'rb'.Str::random('numeric',8);
				$result = DB::select()->from('ReportBase_Table')->where('rbID',$sRbID)->execute()->as_array();
				if (empty($result)):
					break;
				endif;
			endwhile;
			return $sRbID;
		}
		catch (Exception $e)
		{
			throw $e;
		}
	}

	private static function getReportSort($sCtID = null)
	{
		$iSort = 1;
		$result = DB::select(DB::expr('MAX(rbSort) AS rbMax'))->from('ReportBase_Table')->where('ctID',$sCtID)->execute();
		if (count($result))
		{
			$aRes = $result->current();
			$iSort = $aRes['rbMax'] + 1;
		}
		return $iSort;
	}

}
